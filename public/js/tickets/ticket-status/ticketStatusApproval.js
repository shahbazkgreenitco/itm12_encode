var TicketStatusApproval = function (config) {
    var t = this;

    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.other_filters = t.config.other_filters || {};
    t.config.search = t.config.search || "";

    t.page = $("#main-status-approval-wrapper");
    t.table = t.page.find("#statusApproval");
    t.searchInput = t.page.find(".searchbox");
    t.pageLength = t.page.find(".user-list-page-length");
    t.filterBadge = t.page.find(".filter-count-badge");
    t.columnVisibilityButton = t.page.find("#columnVisibilityButton");
    t.columnVisibilityControls = t.page.find("#columnVisibilityControls");
    t.actionStyleScope = t.page.find(".list-view-panel").first();
    t.filterMdl = t.page.find("#advanceFilterModal");
    t.filters = {
        department: t.filterMdl.find("#filter_by_department"),
        problemCategory: t.filterMdl.find("#filter_by_problem_category"),
        subCategory: t.filterMdl.find("#filter_by_sub_category"),
        status: t.filterMdl.find("#filter_by_status"),
        basedOn: t.filterMdl.find("#filter_by_date"),
        daterange: t.filterMdl.find("#daterange"),
        reportrange: t.filterMdl.find("#reportrange"),
        reportrangeText: t.filterMdl.find("#reportrange span").first(),
        rangePicker: null,
        data: {
            problem_categories: {}
        }
    };
    t.httpCall = true;
    t.httpPostPath = "";
    t.currentApprovalType = "user";
    t.httpPostPathUserGroup = t.config.url.getUserByQuery;
    t.translationUserGroup = t.config.translations.select_users || "Select Users";
    t.data = {
        problem_categories: [],
        sub_categories: []
    };

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.mdl = t.page.find("#statusApprovalModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#status-approval-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.company_id = t.mdl.frm.find("#company_id");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.problemCategoryId = t.mdl.frm.find("#category_id");
    t.mdl.frmEl.subCategoryId = t.mdl.frm.find("#subcategory_id");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.status_id = t.mdl.frm.find("#status_id");
    t.mdl.frmEl.fallback_status_id = t.mdl.frm.find("#fallback_status_id");
    t.mdl.frmEl.need_time_duration = t.mdl.frm.find("#need_time_duration");
    t.mdl.frmEl.approval_type = t.mdl.frm.find('input[name="approval_type"]');
    t.mdl.frmEl.approvalTarget = t.mdl.frm.find("#approvalTarget");
    t.mdl.frmEl.approvalLabel = t.mdl.frm.find("#approvalLabel");
    t.mdl.frmEl.approvalIcon = t.mdl.frm.find("#approvalIcon");
    t.mdl.btnSubmit = t.mdl.frm.find("#btnSubmit");

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

    t.userDropdownFormat = function (s) {
        var html;

        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + t.escapeHtml(s.text) + "</div>");
        }

        html = "<div class='row'>";
        html += "<div class='col-sm-10'>";
        html += "<div class='so-t'><span style='padding-right:3px;'>👤</span>" + t.escapeHtml(s.text) + " ";
        html += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        html += "</div>";
        if (s.email) {
            html += "<div class='so-t'><i class='fa fa-envelope-o' style='padding-right:3px;'></i>" + t.escapeHtml(s.email) + "</div>";
        }
        if (s.employee_num) {
            html += "<div class='so-t'><i class='fa fa-credit-card' style='padding-right:3px;'></i>" + t.escapeHtml(s.employee_num) + "</div>";
        }
        html += "</div>";
        html += "<div class='col-sm-2'><div><img class='img-u' src='" + t.escapeHtml(s.img_path || "") + "'/></div></div>";
        html += "</div>";

        return $("<div>" + html + "</div>");
    };

    t.userTemplates = {
        templateResult: function (data) {
            if (!data) {
                return $("<div>No data</div>");
            }
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data, container) {
            $(container).attr("title", data.text);
            return data.text && data.text.length > 50 ? data.text.substring(0, 50) + "..." : data.text;
        }
    };

    t.icons = {
        edit: '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>',
        trash: '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>'
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            t.escapeHtml(classes || ""),
            '" data-id="',
            t.escapeHtml(id),
            '" title="',
            t.escapeHtml(label),
            '" aria-label="',
            t.escapeHtml(label),
            '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    t.renderActionButtons = function (record) {
        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions role-list-actions justify-content-start">',
            t.actionButtonHtml(t.tr("edit_status", "Edit"), "dtActEdit", record.id, t.icons.edit),
            t.actionButtonHtml(t.tr("delete", "Delete"), "dtActDel is-delete", record.id, t.icons.trash),
            '</div>'
        ].join("");
    };

    t.getResponseMessage = function (data, fallback) {
        return data && data.msg ? data.msg : (fallback || t.tr("something_went_wrong", "Something went wrong."));
    };

    t.alertSuccess = function (data) {
        if (typeof sweetAlert === "function") {
            sweetAlert("center", "success", data);
            return;
        }

        Swal.fire({
            icon: "success",
            title: t.tr("success_title", "Success"),
            text: t.getResponseMessage(data),
            confirmButtonText: t.tr("ok_button", "OK")
        });
    };

    t.alertError = function (data) {
        if (typeof sweetAlert === "function") {
            sweetAlert("center", "error", data);
            return;
        }

        Swal.fire({
            icon: "error",
            title: t.tr("error_title", "Error"),
            text: t.getResponseMessage(data),
            confirmButtonText: t.tr("ok_button", "OK")
        });
    };

    t.getValidatedSearchValue = function (showAlert) {
        var value;

        if (typeof t.searchInput.validate_str_param === "function") {
            value = t.searchInput.validate_str_param();
            if (value === false) {
                if (showAlert) {
                    t.alertError({ msg: t.tr("please_enter_valid_search", "Please enter a valid search.") });
                }
                return false;
            }
            return value;
        }

        return $.trim(t.searchInput.val() || "");
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        order: [[7, "desc"]],
        ajax: {
            url: t.config.url.status_approval_list,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search.value = t.config.search || "";
                d.filters = t.config.other_filters || {};
            }
        },
        language: {
            emptyTable: t.tr("no_records_found", "No matching records found"),
            zeroRecords: t.tr("no_records_found", "No matching records found")
        },
        drawCallback: function () {
            t.updateFilterBadge();
        },
        columnDefs: [
            {
                targets: 8,
                orderable: false,
                searchable: false,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return t.renderActionButtons(row.a || data);
                }
            }
        ],
        columns: [
            { data: "a.company_name", defaultContent: "-" },
            { data: "a.department", defaultContent: "-" },
            { data: "a.problem_category", defaultContent: "-" },
            { data: "a.sub_category", defaultContent: "-" },
            { data: "a.name", defaultContent: "-" },
            { data: "a.fallback_status_name", defaultContent: "-" },
            { data: "a.created_at_format", defaultContent: "-" },
            { data: "a.updated_at_format", defaultContent: "-" },
            { data: "a", defaultContent: null }
        ]
    });

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

    t.updateColumnVisibilityControls();

    t.columnVisibilityControls.off("change.statusApprovalTable").on("change.statusApprovalTable", 'input[type="checkbox"]', function (e) {
        e.stopPropagation();
        t.dTbl.column(+$(this).data("column")).visible(this.checked);
    });

    t.dTbl.on("column-reorder.dt.statusApprovalTable column-visibility.dt.statusApprovalTable", function () {
        setTimeout(t.updateColumnVisibilityControls, 10);
    });

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

    t.changePageLength = function (e) {
        var length = parseInt(t.pageLength.val(), 10);

        if (e) {
            e.preventDefault();
        }

        if (length > 0) {
            t.dTbl.page.len(length).draw(false);
        }
    };

    t.hasFilterValue = function (value) {
        return value !== null && value !== undefined && String(value).trim() !== "" && String(value).trim() !== "null";
    };

    t.collectFilter = function (filters, key, element) {
        var value = element.val();

        if (!t.hasFilterValue(value)) {
            return 0;
        }

        filters[key] = value;
        return 1;
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
        t.collectFilter(filters, "status", t.filters.status);

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

    t.openFilterModal = function (e) {
        var modalEl;

        if (e) {
            e.preventDefault();
        }

        modalEl = t.filterMdl.get(0);
        if (window.bootstrap && modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            t.filterMdl.modal("show");
        }

        t.initDateRangePicker();
    };

    t.closeFilterModal = function () {
        var modalEl = t.filterMdl.get(0);
        var instance;

        if (window.bootstrap && modalEl) {
            instance = bootstrap.Modal.getInstance(modalEl);
            if (instance) {
                instance.hide();
            }
        } else {
            t.filterMdl.modal("hide");
        }
    };

    t.applyFilters = function (e) {
        if (e) {
            e.preventDefault();
        }

        if (t.cacheFilterValues() === false) {
            return false;
        }

        t.dTbl.ajax.reload();
        t.closeFilterModal();
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
        t.filters.status.val(null).trigger("change");
        t.filters.basedOn.val("").trigger("change");
        t.clearDateRange();
        t.updateFilterBadge();
        t.dTbl.ajax.reload();
        t.closeFilterModal();
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

    t.reloadFilterProblemCategories = function () {
        var departmentId = t.filters.department.val();

        t.filters.problemCategory.empty().val(null).trigger("change");
        t.filters.subCategory.empty().val(null).trigger("change");
        t.filters.data.problem_categories = {};

        if (!t.hasFilterValue(departmentId)) {
            return;
        }

        $.get(t.config.url.problem_categories_by_company + "/" + departmentId, function (data) {
            if (typeof data === "object" && data.data && data.data.length) {
                $.each(data.data, function (_, item) {
                    t.filters.problemCategory.append(new Option(item.name, item.id, false, false));
                    if ($.isArray(item.sub)) {
                        t.filters.data.problem_categories["sc" + item.id] = item.sub;
                    }
                });
            }
        });
    };

    t.reloadFilterSubCategories = function () {
        var categoryId = t.filters.problemCategory.val();
        var subs = t.filters.data.problem_categories["sc" + categoryId] || [];

        t.filters.subCategory.empty().val(null);
        $.each(subs, function (_, item) {
            t.filters.subCategory.append(new Option(item.name, item.id, false, false));
        });
        t.filters.subCategory.trigger("change");
    };

    t.resetSelect = function (element) {
        element.empty().val(null).trigger("change");
        t.clearFieldError(element);
    };

    t.clearFieldError = function (element) {
        var group = element.closest(".input-group");

        element.removeClass("error");
        element.next(".select2-container").find(".select2-selection").removeClass("amg-form-select-error");
        group.removeClass("amg-form-invalid");

        var errorWrap = t.getModalErrorWrap(element);
        if (errorWrap.length) {
            errorWrap.empty();
        }

        if (t.frmValidator) {
            delete t.frmValidator.invalid[element.attr("name")];
            t.frmValidator.showErrors();
        }
    };

    t.resetDependentFields = function () {
        t.resetSelect(t.mdl.frmEl.departmentId);
        t.resetSelect(t.mdl.frmEl.problemCategoryId);
        t.resetSelect(t.mdl.frmEl.subCategoryId);
        t.resetSelect(t.mdl.frmEl.status_id);
        t.resetSelect(t.mdl.frmEl.fallback_status_id);
        t.updateSubCategoryVisibility();

        if (t.frmValidator) {
            t.frmValidator.resetForm();
        }
    };

    t.updateSubCategoryVisibility = function () {
        if (t.data.sub_categories.length > 0) {
            t.mdl.frmEl.subCategoryIdCvr.removeClass("d-none").show();
        } else {
            t.mdl.frmEl.subCategoryIdCvr.addClass("d-none").hide();
        }
    };

    t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) {
            return $();
        }

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };

    t.updateValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {
            company_id: { required: true },
            department_id: { required: true },
            category_id: { required: true },
            status_id: { required: true },
            fallback_status_id: { required: true },
            approval_type: { required: true },
            "approval_target[]": { required: true }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.resetForm = function () {
        if (t.mdl.frm.length && t.mdl.frm[0]) {
            t.mdl.frm[0].reset();
        }

        t.frmValidator.resetForm();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frm.find(".error").removeClass("error");

        t.resetSelect(t.mdl.frmEl.company_id);
        t.resetDependentFields();
        t.mdl.frmEl.need_time_duration.prop("checked", false);
        t.currentApprovalType = "user";
        t.httpPostPathUserGroup = t.config.url.getUserByQuery;
        t.translationUserGroup = t.tr("select_users", "Select Users");
        t.mdl.frmEl.approval_type.filter('[value="user"]').prop("checked", true).trigger("change");
        t.mdl.btnSubmit.text(t.tr("create", "Create")).prop("disabled", false);
    };

    t.refillProblemCategory = function () {
        var departmentId = parseInt($.trim(t.mdl.frmEl.departmentId.val()), 10);

        t.data.problem_categories = [];
        t.data.sub_categories = [];
        t.mdl.frmEl.problemCategoryId.empty().trigger("change");
        t.mdl.frmEl.subCategoryId.empty().trigger("change");
        t.updateSubCategoryVisibility();

        if (!departmentId) {
            return;
        }

        $.get(t.config.url.problem_categories_by_company + "/" + departmentId).done(function (data) {
            if (typeof data === "object" && data.data && data.data.length) {
                t.data.problem_categories = data.data;
                $.each(data.data, function (_, item) {
                    t.mdl.frmEl.problemCategoryId.append(new Option(item.name, item.id, false, false));
                });
                t.mdl.frmEl.problemCategoryId.trigger("change");
            }
        });
    };

    t.refillSubCategory = function () {
        var categoryId = parseInt($.trim(t.mdl.frmEl.problemCategoryId.val()), 10);

        t.data.sub_categories = [];
        t.mdl.frmEl.subCategoryId.empty();

        if (categoryId) {
            $.each(t.data.problem_categories, function (_, item) {
                if (parseInt(item.id, 10) === categoryId && $.isArray(item.sub)) {
                    t.data.sub_categories = item.sub;
                    $.each(item.sub, function (_, sub) {
                        t.mdl.frmEl.subCategoryId.append(new Option(sub.name, sub.id, false, false));
                    });
                    return false;
                }
            });
        }

        t.updateSubCategoryVisibility();
        t.mdl.frmEl.subCategoryId.trigger("change");
    };

    t.loadApprovalTargetSelect = function () {
        var isUser = t.currentApprovalType === "user";
        var opts;

        if (t.mdl.frmEl.approvalTarget.hasClass("select2-hidden-accessible")) {
            t.mdl.frmEl.approvalTarget.select2("destroy");
        }

        opts = {
            dropdownParent: t.mdl.frmEl.approvalTarget.parent(),
            width: "100%",
            closeOnSelect: true,
            placeholder: t.translationUserGroup,
            ajax: {
                url: function () {
                    return t.httpPostPathUserGroup;
                },
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term || "",
                        page: params.page || 1,
                        company_id: t.mdl.frmEl.company_id.val()
                    };
                },
                processResults: function (data, params) {
                    var items;

                    params.page = params.page || 1;
                    items = data.results || data.data || [];

                    items = $.map(items, function (item) {
                        if (isUser) {
                            return {
                                id: item.id,
                                text: item.text || item.name,
                                email: item.email,
                                employee_num: item.employee_num,
                                status: item.status,
                                img_path: item.img_path
                            };
                        }
                        return {
                            id: item.id,
                            text: item.text || item.name
                        };
                    });

                    return {
                        results: items,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                }
            }
        };

        if (isUser) {
            opts.templateResult = t.userTemplates.templateResult;
            opts.templateSelection = t.userTemplates.templateSelection;
        }

        t.mdl.frmEl.approvalTarget.select2(opts);
    };

    t.addStatusApproval = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        t.resetForm();
        t.httpPostPath = t.config.url.ajaxCreateStatusApproval;
        t.mdl.title.text(t.tr("add_status_approval", "Add Status Approval"));
        t.mdl.btnSubmit.text(t.tr("create", "Create"));
        t.mdl.modal("show");
    };

    t.loadForm = function (res) {
        var data = res.data.data;
        var dd = res.data.dropdown || {};
        var type = data.approval_from == 2 ? "group" : "user";
        var members = [];

        t.resetForm();

        if (dd.base_company && dd.base_company.length) {
            t.mdl.frmEl.company_id.append(new Option(dd.base_company[0].text, dd.base_company[0].id, true, true)).trigger("change");
        }
        if (dd.sac_department && dd.sac_department.length) {
            t.mdl.frmEl.departmentId.append(new Option(dd.sac_department[0].text, dd.sac_department[0].id, true, true)).trigger("change");
        }
        if (dd.sac_category && dd.sac_category.length) {
            t.mdl.frmEl.problemCategoryId.append(new Option(dd.sac_category[0].text, dd.sac_category[0].id, true, true)).trigger("change");
        }
        if (dd.sac_subCategory && dd.sac_subCategory.length) {
            t.data.sub_categories = dd.sac_subCategory;
            t.updateSubCategoryVisibility();
            t.mdl.frmEl.subCategoryId.append(new Option(dd.sac_subCategory[0].text, dd.sac_subCategory[0].id, true, true)).trigger("change");
        }
        if (dd.sac_statusId && dd.sac_statusId.length) {
            t.mdl.frmEl.status_id.append(new Option(dd.sac_statusId[0].text, dd.sac_statusId[0].id, true, true)).trigger("change");
        }
        if (dd.sac_fallbackStatusId && dd.sac_fallbackStatusId.length) {
            t.mdl.frmEl.fallback_status_id.append(new Option(dd.sac_fallbackStatusId[0].text, dd.sac_fallbackStatusId[0].id, true, true)).trigger("change");
        }

        t.currentApprovalType = type;
        t.mdl.frmEl.approval_type.filter('[value="' + type + '"]').prop("checked", true).trigger("change");

        members = type === "group" ? (dd.approval_groups || []) : (dd.approval_users || []);
        $.each(members, function (_, member) {
            t.mdl.frmEl.approvalTarget.append(new Option(member.text, member.id, true, true));
        });
        t.mdl.frmEl.approvalTarget.trigger("change");
        t.mdl.frmEl.need_time_duration.prop("checked", parseInt(data.need_time_duration, 10) === 1);
    };

    t.editStatusApproval = function (e) {
        var id;

        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.ajaxUpdateStatusApproval + "/" + id;

        $.get(t.config.url.ajaxEditStatusApproval + "/" + id)
            .done(function (res) {
                if (res.status === "success") {
                    t.mdl.title.text(t.tr("edit_status_approval", "Edit Status Approval"));
                    t.mdl.btnSubmit.text(t.tr("update", "Update"));
                    t.loadForm(res);
                    t.mdl.modal("show");
                } else {
                    t.alertError({ msg: t.getResponseMessage(res, t.tr("unable_to_load_record", "Unable to load record.")) });
                }
            })
            .fail(function (xhr) {
                t.alertError(xhr.responseJSON || { msg: t.tr("something_went_wrong", "Something went wrong.") });
            });
    };

    t.submitStatusApproval = function (e) {
        var formData;

        if (e) {
            e.preventDefault();
        }

        if (t.frmValidator.form() === false || t.httpCall === false) {
            return false;
        }

        t.httpCall = false;
        t.mdl.btnSubmit.prop("disabled", true);
        formData = new FormData(t.mdl.frm[0]);
        formData.append("_token", t.config.token);

        $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        }).done(function (data) {
            if (data.status === "success") {
                t.mdl.modal("hide");
                t.alertSuccess(data);
                t.reload();
            } else {
                t.alertError(data);
            }
        }).fail(function (xhr) {
            t.alertError(xhr.responseJSON || { msg: t.tr("something_went_wrong", "Something went wrong.") });
        }).always(function () {
            t.httpCall = true;
            t.mdl.btnSubmit.prop("disabled", false);
        });
    };

    t.deleteStatusApproval = function (e) {
        var id;
        var onConfirm;

        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        id = $(this).attr("data-id");
        onConfirm = function () {
            $.get(t.config.url.ajaxDeleteStatusApproval + "/" + id)
                .done(function (data) {
                    if (data.status === "success") {
                        t.alertSuccess(data);
                        t.reload();
                    } else {
                        t.alertError(data);
                    }
                })
                .fail(function (xhr) {
                    t.alertError(xhr.responseJSON || { msg: t.tr("something_went_wrong", "Something went wrong.") });
                });
        };

        if (typeof sweetAlertConfirmation === "function") {
            sweetAlertConfirmation({
                message: t.tr("delete_record", "You want to delete this record?"),
                confirmButtonText: t.tr("confirm_delete_button", "Yes, delete it"),
                cancelButtonText: t.tr("cancel", "Cancel"),
                onConfirm: onConfirm
            });
            return;
        }

        Swal.fire({
            title: t.tr("confirm_delete_title", "Are you sure?"),
            text: t.tr("delete_record", "You want to delete this record?"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: t.tr("confirm_delete_button", "Yes, delete it")
        }).then(function (result) {
            if (result.isConfirmed) {
                onConfirm();
            }
        });
    };

    t.download = function (e) {
        var query;

        if (e) {
            e.preventDefault();
        }

        query = btoa(JSON.stringify({
            search: t.config.search || "",
            other_filters: t.config.other_filters || {}
        }));
        window.location = t.config.url.statusApprovalExport + "?q=" + query;
    };

    var select2Opts = { width: "100%" };

    t.mdl.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.company_id.parent(),
        placeholder: t.tr("select_company", "Select Company"),
        allowClear: true,
        ajax: {
            url: t.config.url.getCompany,
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.mdl.frmEl.departmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        placeholder: t.tr("select_department", "Select Department"),
        allowClear: true,
        ajax: {
            url: t.config.url.departments_by_company,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    company_id: t.mdl.frmEl.company_id.val()
                };
            },
            transport: function (params, success, failure) {
                if (!t.mdl.frmEl.company_id.val()) {
                    t.alertError({ msg: t.tr("select_company_first", "Please select a company first.") });
                    return false;
                }

                var request = $.ajax(params);
                request.then(success);
                request.fail(failure);
                return request;
            },
            processResults: function (data, params) {
                var items;

                params.page = params.page || 1;
                items = data.results || data.data || [];
                items = $.map(items, function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name
                    };
                });

                return {
                    results: items,
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.mdl.frmEl.problemCategoryId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.problemCategoryId.parent(),
        placeholder: t.tr("select_problem_category", "Select Problem Category"),
        allowClear: true
    }));

    t.mdl.frmEl.subCategoryId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.subCategoryId.parent(),
        placeholder: t.tr("select_subcategory", "Select Subcategory"),
        allowClear: true
    }));

    t.mdl.frmEl.status_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.status_id.parent(),
        placeholder: t.tr("select_status", "Select Status"),
        allowClear: true,
        ajax: {
            url: t.config.url.getStatusByAjax,
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    company_id: t.mdl.frmEl.company_id.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results || data.data || [],
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.mdl.frmEl.fallback_status_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.fallback_status_id.parent(),
        placeholder: t.tr("select_fallback_status", "Select Fallback Status"),
        allowClear: true,
        ajax: {
            url: t.config.url.getStatusByAjax,
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    company_id: t.mdl.frmEl.company_id.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results || data.data || [],
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.filters.department.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_department", "Filter By Department"),
        allowClear: true,
        ajax: {
            url: t.config.url.departments_by_company,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    company_id: $("#config-company").val()
                };
            },
            processResults: function (data, params) {
                var items;

                params.page = params.page || 1;
                items = data.results || data.data || [];
                items = $.map(items, function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name
                    };
                });

                return {
                    results: items,
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.filters.problemCategory.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_problem_category", "Filter By Problem Category"),
        allowClear: true
    }));

    t.filters.subCategory.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_subcategory", "Filter By Subcategory"),
        allowClear: true
    }));

    t.filters.status.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_status", "Filter By Status"),
        allowClear: true,
        ajax: {
            url: t.config.url.getStatusByAjax,
            type: "GET",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || "",
                    page: params.page || 1,
                    company_id: $("#config-company").val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results || data.data || [],
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        }
    }));

    t.filters.basedOn.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        placeholder: t.tr("filter_by_date", "Filter By Date"),
        allowClear: true,
        minimumResultsForSearch: Infinity
    }));

    t.loadApprovalTargetSelect();
    t.updateSubCategoryVisibility();
    t.updateFilterBadge();

    t.mdl.frmEl.company_id.on("change", t.resetDependentFields);
    t.mdl.frmEl.departmentId.on("change", t.refillProblemCategory);
    t.mdl.frmEl.problemCategoryId.on("change", t.refillSubCategory);
    t.filters.department.on("change", t.reloadFilterProblemCategories);
    t.filters.problemCategory.on("change", t.reloadFilterSubCategories);
    t.mdl.frmEl.approval_type.on("change", function () {
        var type = $(this).val();

        t.currentApprovalType = type;
        if (type === "group") {
            t.mdl.frmEl.approvalLabel.text(t.tr("group", "Group"));
            t.mdl.frmEl.approvalIcon.find("i").removeClass().addClass("bi bi-people");
            t.httpPostPathUserGroup = t.config.url.getCcUserGroups;
            t.translationUserGroup = t.tr("select_groups", "Select Groups");
        } else {
            t.mdl.frmEl.approvalLabel.text(t.tr("user", "User"));
            t.mdl.frmEl.approvalIcon.find("i").removeClass().addClass("bi bi-person");
            t.httpPostPathUserGroup = t.config.url.getUserByQuery;
            t.translationUserGroup = t.tr("select_users", "Select Users");
        }

        t.mdl.frmEl.approvalTarget.empty().val(null);
        t.loadApprovalTargetSelect();
    });

    t.page.on("click", ".btn-add-status, .btn-add-status-approval", t.addStatusApproval);
    t.page.on("click", ".btn-open-filter", t.openFilterModal);
    t.columnVisibilityButton.on("click", t.toggleColumnVisibilityMenu);
    t.columnVisibilityControls.on("click", function (e) {
        e.stopPropagation();
    });
    $(document).on("click.statusApprovalColumnVisibility", function (e) {
        if (!$(e.target).closest(".column-visibility-dropdown").length) {
            t.closeColumnVisibilityMenu();
        }
    });
    t.page.on("click", "#advanceFilterModal .btn-filter", t.applyFilters);
    t.page.on("click", "#advanceFilterModal #btnClrFilter, #advanceFilterModal .btn-clear-filter", t.clearFilters);
    t.page.on("click", "#btnSubmit", t.submitStatusApproval);
    t.page.on("click", ".btn-reload-list", t.reload);
    t.page.on("click", ".btn-download", t.download);
    t.page.on("click", ".btn-searchbox, .amg-list-searchbar__icon", t.search);
    t.page.on("change", ".user-list-page-length", t.changePageLength);
    t.page.on("keyup", ".searchbox", function (e) {
        if (e.which === 13) {
            t.search(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.search(e);
        }
    });
    t.table.on("click", ".dtActEdit", t.editStatusApproval);
    t.table.on("click", ".dtActDel", t.deleteStatusApproval);
};
