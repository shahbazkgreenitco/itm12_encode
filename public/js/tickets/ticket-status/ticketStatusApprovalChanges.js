var TicketStatusApprovalChanges = function (config) {
    var t = this;

    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.other_filters = t.config.other_filters || {};
    t.config.search = t.config.search || "";

    t.page = $("#main-status-approval-change-wrapper");
    t.table = t.page.find("#statusApprovalChanges");
    t.searchInput = t.page.find(".searchbox");
    t.pageLength = t.page.find(".user-list-page-length");
    t.filterBadge = t.page.find(".filter-count-badge");
    t.columnVisibilityButton = t.page.find("#columnVisibilityButton");
    t.columnVisibilityControls = t.page.find("#columnVisibilityControls");
    t.filterMdl = t.page.find("#advanceFilterModal");
    t.mdl = t.page.find("#approvalModal");
    t.filters = {
        data: {
            problem_categories: {}
        },
        department: t.filterMdl.find("#department"),
        problemCategory: t.filterMdl.find("#category"),
        subCategory: t.filterMdl.find("#subcategory"),
        requestedStatus: t.filterMdl.find("#requested_status"),
        basedOn: t.filterMdl.find("#filter_by_date"),
        daterange: t.filterMdl.find("#daterange"),
        reportrange: t.filterMdl.find("#reportrange"),
        reportrangeText: t.filterMdl.find("#reportrange span").first(),
        rangePicker: null
    };
    t.mdl.frm = t.mdl.find("#approval-mdl-frm");
    t.mdl.frmEl = {
        startTime: t.mdl.frm.find("#start_time"),
        endTime: t.mdl.frm.find("#end_time"),
        status: t.mdl.frm.find("#status"),
        statusApprovalId: t.mdl.frm.find("#status_approval_id"),
        btnSubmit: t.mdl.frm.find("#btnSubmit"),
        btnClear: t.mdl.frm.find("#btnClear")
    };

    t.tr = function (key, fallback) {
        return t.config.translations[key] || fallback;
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.hasFilterValue = function (value) {
        return value !== null && value !== undefined && String(value).trim() !== "" && String(value).trim() !== "null";
    };

    t.getValidatedSearchValue = function (showAlert) {
        var value;

        if (typeof t.searchInput.validate_str_param === "function") {
            value = t.searchInput.validate_str_param();
            if (value === false) {
                if (showAlert) {
                    alert(t.tr("please_enter_valid_search", "Please enter a valid search."));
                }
                return false;
            }
            return value;
        }

        return $.trim(t.searchInput.val() || "");
    };

    t.showModal = function (modal) {
        var modalEl = modal.get(0);

        if (window.bootstrap && modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }

        modal.modal("show");
    };

    t.hideModal = function (modal) {
        var modalEl = modal.get(0);
        var instance;

        if (window.bootstrap && modalEl) {
            instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) {
                instance.hide();
            }
            return;
        }

        modal.modal("hide");
    };

    t.actionButtonHtml = function (label, classes, id, iconClass) {
        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            t.escapeHtml(classes || ""),
            '" data-id="',
            t.escapeHtml(id),
            '" title="',
            t.escapeHtml(label),
            '" aria-label="',
            t.escapeHtml(label),
            '"><i class="',
            t.escapeHtml(iconClass),
            '"></i></button>'
        ].join("");
    };

    t.renderActionButtons = function (row) {
        var actions = [];
        console.log(row);
        if (row.approver_id == row.auth_id && row.approval_status == "Pending") {
            actions.push(t.actionButtonHtml(t.tr("approval", "Approval"), "dtActApproval", row.id, "bi bi-shield-check"));
        }

        actions.push(t.actionButtonHtml(t.tr("info", "Info"), "dtActInfo", row.id, "bi bi-info-circle"));

        return '<div class="user-list-actions role-list-actions justify-content-start">' + actions.join("") + "</div>";
    };

    t.renderApprovalStatus = function (data, type) {
        if (type !== "display") {
            return data;
        }

        if (data === "Pending") {
            return '<span class="badge bg-warning">' + t.escapeHtml(t.tr("pending", "Pending")) + "</span>";
        }

        if (data === "Approved") {
            return '<span class="badge bg-success">' + t.escapeHtml(t.tr("approved", "Approved")) + "</span>";
        }

        if (data === "Rejected") {
            return '<span class="badge bg-danger">' + t.escapeHtml(t.tr("rejected", "Rejected")) + "</span>";
        }

        return '<span class="badge bg-secondary">' + t.escapeHtml(t.tr("unknown", "Unknown")) + "</span>";
    };

    t.renderTicket = function (data, type) {
        if (type !== "display") {
            return data;
        }

        return data ? "#" + t.escapeHtml(data) : "";
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        dom:'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        order: [[7, "desc"]],
        ajax: {
            url: t.config.url.ajax_status_approval_list + "/" + t.config.page_filter,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search.value = t.config.search || "";
                d.filters = t.config.other_filters || {};
                d.main_filter = t.config.main_filter;
                d.company_id = $("#config-company").val();
            }
        },
        language: {
            emptyTable: t.tr("no_records_found", "No matching records found"),
            zeroRecords: t.tr("no_records_found", "No matching records found")
        },
        drawCallback: function () {
            t.updateFilterBadge();
        },
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        columns: [
            { data: "company_name", defaultContent: "-" },
            { data: "department", defaultContent: "-" },
            { data: "problem_category", defaultContent: "-" },
            { data: "sub_category", defaultContent: "-" },
            { data: "ticket_id", defaultContent: "", render: t.renderTicket },
            { data: "requested_status", defaultContent: "-" },
            { data: "approval_status", defaultContent: "-", render: t.renderApprovalStatus },
            { data: "start_time_format", defaultContent: "-" },
            { data: "end_time_format", defaultContent: "-" },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return t.renderActionButtons(row);
                }
            }
        ]
    });

    t.collectFilter = function (filters, key, element) {
        var value = element.val();

        if (!t.hasFilterValue(value)) {
            return 0;
        }

        filters[key] = value;
        return 1;
    };

    t.cacheFilterValues = function () {
        var value = t.getValidatedSearchValue(false);
        var filters = {};

        if (value === false) {
            return false;
        }

        t.config.search = value;
        t.collectFilter(filters, "department", t.filters.department);
        t.collectFilter(filters, "prob_category", t.filters.problemCategory);
        t.collectFilter(filters, "sub_category", t.filters.subCategory);
        t.collectFilter(filters, "requested_status", t.filters.requestedStatus);

        if (t.hasFilterValue(t.filters.basedOn.val()) && t.hasFilterValue(t.filters.daterange.val())) {
            filters.based_on = t.filters.basedOn.val();
            filters.daterange = t.filters.daterange.val();
        }

        t.config.other_filters = filters;
        t.config.export_filters = btoa(JSON.stringify({
            search: t.config.search,
            other_filters: t.config.other_filters
        }));
        t.updateFilterBadge();
        return true;
    };

    t.updateFilterBadge = function () {
        var count = 0;

        $.each(t.config.other_filters || {}, function (key, value) {
            if (key !== "daterange" && t.hasFilterValue(value)) {
                count += 1;
            }
        });

        if (count > 0) {
            t.filterBadge.removeClass("d-none").text(count);
        } else {
            t.filterBadge.addClass("d-none").text("0");
        }
    };

    t.reload = function (e) {
        if (e) {
            e.preventDefault();
        }

        if (t.cacheFilterValues() === false) {
            return false;
        }

        t.dTbl.ajax.reload(null, false);
    };

    t.search = function (e) {
        var value;

        if (e) {
            e.preventDefault();
        }

        value = t.getValidatedSearchValue(true);
        if (value === false) {
            return false;
        }

        t.config.search = value;
        t.cacheFilterValues();
        t.dTbl.ajax.reload();
    };

    t.changePageLength = function () {
        var length = parseInt(t.pageLength.val(), 10);

        if (length > 0) {
            t.dTbl.page.len(length).draw(false);
        }
    };

    t.openFilterModal = function (e) {
        if (e) {
            e.preventDefault();
        }

        t.showModal(t.filterMdl);
        t.initDateRangePicker();
    };

    t.applyFilters = function (e) {
        if (e) {
            e.preventDefault();
        }

        if (t.cacheFilterValues() === false) {
            return false;
        }

        t.dTbl.ajax.reload();
        t.hideModal(t.filterMdl);
    };

    t.clearDateRange = function () {
        if (t.filters.rangePicker && typeof moment !== "undefined") {
            t.filters.rangePicker.setStartDate(moment().startOf("day"));
            t.filters.rangePicker.setEndDate(moment().endOf("day"));
        }

        t.filters.daterange.val("");
        t.filters.reportrangeText.text(t.tr("select_date_range", "Select date range"));
    };

    t.clearFilters = function (e) {
        if (e) {
            e.preventDefault();
        }

        t.config.other_filters = {};
        t.filters.department.val(null).trigger("change");
        t.filters.problemCategory.empty().val(null).trigger("change");
        t.filters.subCategory.empty().val(null).trigger("change");
        t.filters.requestedStatus.val(null).trigger("change");
        t.filters.basedOn.val("").trigger("change");
        t.clearDateRange();
        t.updateFilterBadge();
        t.dTbl.ajax.reload();
        t.hideModal(t.filterMdl);
    };

    t.setDateRange = function (start, end) {
        t.filters.reportrangeText.text(start.format("DD-MM-YYYY HH:mm:ss") + " - " + end.format("DD-MM-YYYY HH:mm:ss"));
        t.filters.daterange.val(start.format("YYYY-MM-DD HH:mm:ss") + " - " + end.format("YYYY-MM-DD HH:mm:ss"));
    };

    t.initDateRangePicker = function () {
        if (!t.filters.reportrange.length || typeof moment === "undefined" || !$.fn.daterangepicker || t.filters.rangePicker) {
            return;
        }

        t.filters.reportrange.daterangepicker({
            parentEl: t.filterMdl,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            autoUpdateInput: false,
            locale: {
                format: "DD-MM-YYYY HH:mm:ss",
                cancelLabel: t.tr("clear", "Clear")
            }
        }, function (start, end) {
            t.setDateRange(start, end);
        });

        t.filters.rangePicker = t.filters.reportrange.data("daterangepicker");
        t.filters.reportrange.on("cancel.daterangepicker", t.clearDateRange);
    };

    t.reloadProblemCategories = function () {
        var department = t.filters.department.val();

        t.filters.problemCategory.empty().trigger("change");
        t.filters.subCategory.empty().trigger("change");
        t.filters.data.problem_categories = {};

        if (!t.hasFilterValue(department)) {
            return;
        }

        $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
            if (typeof data === "object" && data.data && data.data.length > 0) {
                $.each(data.data, function (i, item) {
                    t.filters.problemCategory.append(new Option(item.name, item.id, false, false));
                    if ($.isArray(item.sub)) {
                        t.filters.data.problem_categories["sc" + item.id] = item.sub;
                    }
                });
                t.filters.problemCategory.trigger("change");
            }
        });
    };

    t.reloadSubCategories = function () {
        var category = t.filters.problemCategory.val();
        var items = t.filters.data.problem_categories["sc" + category] || [];

        t.filters.subCategory.empty();
        $.each(items, function (i, item) {
            t.filters.subCategory.append(new Option(item.name, item.id, false, false));
        });
        t.filters.subCategory.trigger("change");
    };

    t.updateColumnVisibilityControls = function () {
        var controls = t.columnVisibilityControls.empty();

        t.dTbl.columns().every(function () {
            var columnIndex = this.index();
            var columnTitle = $(this.header()).text().trim();
            var columnId = "column-toggle-" + columnIndex;

            controls.append(
                '<div class="dropdown-item" style="padding:6px">' +
                    '<label for="' + columnId + '">' +
                        '<input type="checkbox" id="' + columnId + '" data-column="' + columnIndex + '" ' + (this.visible() ? "checked" : "") + '> ' + t.escapeHtml(columnTitle) +
                    '</label>' +
                '</div>'
            );
        });
    };

    t.toggleColumnVisibilityMenu = function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.columnVisibilityControls.toggleClass("show");
    };

    t.closeColumnVisibilityMenu = function () {
        t.columnVisibilityControls.removeClass("show");
    };

    t.toDateTimeLocal = function (value) {
        if (!value) {
            return "";
        }

        return String(value).replace(" ", "T").slice(0, 16);
    };

    t.nowDateTimeLocal = function () {
        var now = new Date();
        var pad = function (n) { return String(n).padStart(2, "0"); };
        return now.getFullYear() + "-" +
            pad(now.getMonth() + 1) + "-" +
            pad(now.getDate()) + "T" +
            pad(now.getHours()) + ":" +
            pad(now.getMinutes());
    };

    t.openApproval = function (e) {
        var id;
        var minNow;

        if (e) {
            e.preventDefault();
        }

        id = $(this).attr("data-id");
        t.mdl.frm[0].reset();
        t.mdl.frmEl.statusApprovalId.val(id);

        minNow = t.nowDateTimeLocal();
        t.mdl.frmEl.startTime.attr("min", minNow);
        t.mdl.frmEl.endTime.attr("min", minNow);

        $.get(t.config.url.statusApprovalData + "/" + id, function (res) {
            if (res.status === "success") {
                t.mdl.frmEl.startTime.val(t.toDateTimeLocal(res.data.start_time));
                t.mdl.frmEl.endTime.val(t.toDateTimeLocal(res.data.end_time));
            } else {
                if (typeof sweetAlert === "function") {
                    sweetAlert("center", "error", { msg: res.msg || t.tr("something_went_wrong", "Something went wrong. Please try again.") });
                } else {
                    alert(res.msg || t.tr("something_went_wrong", "Something went wrong. Please try again."));
                }
            }
        });

        t.showModal(t.mdl);
    };

    t.openInfo = function (e) {
        var id;

        if (e) {
            e.preventDefault();
        }

        id = $(this).attr("data-id");
        window.open(t.config.url.infoStatusApproval + "/" + id + "/"+ t.config.page_filter, "_blank");
    };

    t.submitStatusApproval = function (e) {
        var formData;

        if (e) {
            e.preventDefault();
        }

        let startTime = t.mdl.frmEl.startTime.val();
        let endTime = t.mdl.frmEl.endTime.val();

        // Required validation
        if (!startTime) {
            e.preventDefault();
            sweetAlert("center", "error", {
                msg: t.tr("select_start_time")
            });
            return false;
        }

        if (!endTime) {
            e.preventDefault();
            sweetAlert("center", "error", {
                msg: t.tr("select_end_time")
            });
            return false;
        }

        let start = new Date(startTime);
        let end = new Date(endTime);

        if (start.getTime() === end.getTime()) {
            e.preventDefault();
            sweetAlert("center", "error", {
                msg: t.tr("start_end_same")
            });
            return false;
        }

        // 30 minutes gap validation
        if ((end - start) < (30 * 60 * 1000)) {
            e.preventDefault();
            sweetAlert("center", "error", {
                msg: t.tr("end_time_gap")
            });
            return false;
        }

        if (!t.mdl.frmEl.status.val()) {
            if (typeof sweetAlert === "function") {
                sweetAlert("center", "error", { msg: t.tr("select_status", "Select Status") });
            } else {
                alert(t.tr("select_status", "Select Status"));
            }
            return false;
        }

        formData = new FormData(t.mdl.frm[0]);

        $.ajax({
            url: t.config.url.statusChangeApproval,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        }).done(function (data) {
            if (typeof data === "object" && data.status === "success") {
                if (typeof sweetAlert === "function") {
                    sweetAlert("center", "success", data);
                }
                t.dTbl.ajax.reload(null, false);
                t.hideModal(t.mdl);
                return;
            }

            if (typeof sweetAlert === "function") {
                sweetAlert("center", "error", data);
            } else {
                alert((data && data.msg) || t.tr("something_went_wrong", "Something went wrong. Please try again."));
            }
        }).fail(function () {
            var data = { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") };

            if (typeof sweetAlert === "function") {
                sweetAlert("center", "error", data);
            } else {
                alert(data.msg);
            }
        });
    };

    var select2Opts = {
        width: "100%",
        allowClear: true
    };

    t.filters.department.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_department", "Filter By Department"),
        ajax: {
            url: t.config.url.departments_by_company,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term || "",
                    company_id: $("#config-company").val(),
                    page: params.page || 1
                };
            }
        }
    }));

    t.filters.problemCategory.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_problem_category", "Filter By Problem Category")
    }));

    t.filters.subCategory.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_sub_category", "Filter By Sub Category")
    }));

    t.filters.requestedStatus.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("requested_status", "Requested Status"),
        ajax: {
            url: t.config.url.getStatusByAjax,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term || "",
                    company_id: $("#config-company").val(),
                    page: params.page || 1
                };
            }
        }
    }));

    t.filters.basedOn.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("based_on", "Based On"),
        minimumResultsForSearch: Infinity
    }));

    t.mdl.frmEl.status.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        placeholder: t.tr("select_status", "Select Status"),
        minimumResultsForSearch: Infinity
    }));

    t.updateColumnVisibilityControls();
    t.updateFilterBadge();

    t.columnVisibilityControls.off("change.statusApprovalChangesTable").on("change.statusApprovalChangesTable", 'input[type="checkbox"]', function (e) {
        e.stopPropagation();
        t.dTbl.column(+$(this).data("column")).visible(this.checked);
    });

    t.dTbl.on("column-reorder.dt.statusApprovalChangesTable column-visibility.dt.statusApprovalChangesTable", function () {
        setTimeout(t.updateColumnVisibilityControls, 10);
    });

    t.filters.department.on("change", t.reloadProblemCategories);
    t.filters.problemCategory.on("change", t.reloadSubCategories);
    t.page.on("click", ".btn-open-filter", t.openFilterModal);
    t.page.on("click", "#advanceFilterModal .btn-filter", t.applyFilters);
    t.page.on("click", "#advanceFilterModal #btnClrFilter, #advanceFilterModal .btn-clear-filter", t.clearFilters);
    t.page.on("click", ".btn-reload-list", t.reload);
    t.page.on("click", ".amg-list-searchbar__icon", t.search);
    t.page.on("change", ".user-list-page-length", t.changePageLength);
    t.page.on("keyup", ".searchbox", function (e) {
        if (e.which === 13) {
            t.search(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.search(e);
        }
    });
    t.table.on("click", ".dtActApproval", t.openApproval);
    t.table.on("click", ".dtActInfo", t.openInfo);
    t.mdl.frmEl.btnSubmit.on("click", t.submitStatusApproval);

    t.mdl.frmEl.startTime.on("change", function () {
        var startVal = $(this).val();
        if (startVal) {
            t.mdl.frmEl.endTime.attr("min", startVal);
            if (t.mdl.frmEl.endTime.val() && t.mdl.frmEl.endTime.val() < startVal) {
                t.mdl.frmEl.endTime.val(startVal);
            }
        }
    });

    t.columnVisibilityButton.on("click", t.toggleColumnVisibilityMenu);
    t.columnVisibilityControls.on("click", function (e) {
        e.stopPropagation();
    });
    $(document).on("click.statusApprovalChangesColumnVisibility", function (e) {
        if (!$(e.target).closest(".column-visibility-dropdown").length) {
            t.closeColumnVisibilityMenu();
        }
    });
};
