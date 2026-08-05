var TicketStatus = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#main-ticket-status-wrapper");
    t.table = t.content.find("#mytable");
    t.searchbox = t.content.find(".searchbox");
    t.pageLength = t.content.find(".tkt-status-page-length");
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    t.page = t.content;
    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    // ─── FILTERS ────────────────────────────────────────────────────────────────
    t.filters = {
        wrapper: $('#ticketStatusFilterModal'),
        trigger: t.content.find(".btn-open-filter"),
        reportrange: $('#reportrange'),
        daterange: $('#daterange'),
        reportrangeText: $('#reportrange-text'),
        status: $('#ticketStatusFilterModal').find('#filter_by_status'),
        based_on: $('#ticketStatusFilterModal').find('#filter_by_date'),
        btnFilter: $('#ticketStatusFilterModal').find('.btn-filter'),
        btnfilterclr: $('#ticketStatusFilterModal').find('.btn-clear-filter'),
        badge: t.content.find(".filter-count-badge"),
        rangePicker: null
    };

    t.filters.fun = {
        reload_status: function () {
            if (!$.isArray(t.config.statuses) || t.filters.status.find("option").length > 1) {
                return;
            }
            $.each(t.config.statuses, function (i, k) {
                t.filters.status.append(new Option(k.name, k.id, false, false));
            });
            t.filters.status.trigger("change");
        }
    };

    t.offListen = false;

    // ─── CREATE MODAL REFS ──────────────────────────────────────────────────────
    t.btn = {};
    t.mdl = t.content.find("#statusModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#status-mdl-frm");
    t.mdl.frmEl  = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.forAction = t.mdl.frm.find("#for_action");
    t.mdl.frmEl.statusname = t.mdl.frm.find("#name");
    t.mdl.frmEl.status = t.mdl.frm.find("#is_enabled");
    t.mdl.frmEl.tat_halt = t.mdl.frm.find("#tat_halt");
    t.mdl.frmEl.color_code = t.mdl.frm.find("#color_code");
    t.mdl.frmEl.ticket_type = t.mdl.frm.find("#ticket_type");
    t.mdl.frmEl.attachment = t.mdl.frm.find("#attachment");
    t.mdl.frmEl.status_form = t.mdl.frm.find("#status_form");
    t.mdl.frmEl.form_repeater = t.mdl.frm.find(".form-repeater");
    t.mdl.frmEl.pc_id = t.mdl.frm.find(".pc_id");
    t.mdl.frmEl.sc_id = t.mdl.frm.find(".sc_id");
    t.mdl.frmEl.form_id = t.mdl.frm.find(".form_id");
    t.mdl.frmEl.company_id = t.mdl.frm.find("#company_id");
    t.mdl.btn = {};
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");

    // ─── UPDATE MODAL REFS ──────────────────────────────────────────────────────
    t.mdlUpdate = t.content.find("#statusupdateModal");
    t.frmUpdate = t.mdlUpdate.find("#status_update_form");
    t.frmUpdate.extraData = {};
    t.frmUpdate.el = {};
    t.mdlUpdate.title = t.mdlUpdate.find(".modal-title");
    t.frmUpdate.el.id = t.frmUpdate.find("#id");
    t.frmUpdate.el.iseditable = t.frmUpdate.find("#iseditable");
    t.frmUpdate.el.forAction = t.frmUpdate.find("#forAction");
    t.frmUpdate.el.token = t.frmUpdate.find("#token");
    t.frmUpdate.el.statusname = t.frmUpdate.find("#name");
    t.frmUpdate.el.status= t.frmUpdate.find("#is_enabled");
    t.frmUpdate.el.tat_halt = t.frmUpdate.find("#tat_halt");
    t.frmUpdate.el.color_code = t.frmUpdate.find("#color_code");
    t.frmUpdate.el.ticket_type = t.frmUpdate.find("#ticket_type");
    t.frmUpdate.el.attachment = t.frmUpdate.find("#attachment");
    t.frmUpdate.el.status_form = t.frmUpdate.find("#status_form");
    t.frmUpdate.el.form_repeater = t.frmUpdate.find(".form-repeater");
    t.frmUpdate.el.pc_id = t.frmUpdate.find(".pc_id");
    t.frmUpdate.el.sc_id = t.frmUpdate.find(".sc_id");
    t.frmUpdate.el.form_id = t.frmUpdate.find(".form_id");
    t.frmUpdate.el.btnUpdate = t.frmUpdate.find("#btnUpdate");
    t.frmUpdate.el.company_id = t.frmUpdate.find("#company_id");

    t.isEditMode = false;

    // ─── TRANSLATION / TOAST HELPERS ────────────────────────────────────────────
    t.tr = function (key, fallback) {
        if (t.config.translations && t.config.translations[key]) {
            return t.config.translations[key];
        }
        return fallback;
    };

    t.resolveMessage = function (message) {
        if (message && typeof message === "object" && message.msg) {
            return message.msg;
        }
        if (typeof message === "string" && $.trim(message).length) {
            return message;
        }
        return t.tr("something_went_wrong", "Something went wrong.");
    };

    t.showToast = function (icon, message) {
        var msg = t.resolveMessage(message);
        var dfd = $.Deferred();

        if (window.Swal && typeof Swal.fire === "function") {
            Swal.fire({
                icon: icon,
                title: icon === "success" ? t.tr("success_title", "Success") : t.tr("error_title", "Error"),
                text: msg,
                showConfirmButton: true,
                confirmButtonText: t.tr("ok_button", "OK"),
                allowOutsideClick: false
            }).then(function (result) {
                dfd.resolve(result);
            });
            return dfd.promise();
        }

        if (typeof sweetAlert === "function") {
            sweetAlert("center", icon === "success" ? "success" : "error", { msg: msg });
            dfd.resolve();
            return dfd.promise();
        }

        alert(msg);
        dfd.resolve();
        return dfd.promise();
    };

    t.showConfirm = function (options) {
        var dfd = $.Deferred();
        var text = t.resolveMessage((options || {}).text || (options || {}).message);

        if (window.Swal && typeof Swal.fire === "function") {
            Swal.fire({
                icon: (options || {}).icon || "warning",
                title: (options || {}).title || t.tr("confirm_title", "Confirm"),
                text: text,
                showCancelButton: true,
                confirmButtonText: (options || {}).confirmButtonText || t.tr("confirm_button", "Yes"),
                cancelButtonText: (options || {}).cancelButtonText || t.tr("cancel_button", "Cancel"),
                allowOutsideClick: false
            }).then(function (result) {
                dfd.resolve(!!result.isConfirmed);
            });
        } else {
            dfd.resolve(confirm(text));
        }

        return dfd.promise();
    };

    // ─── MODAL INIT ─────────────────────────────────────────────────────────────
    t.mdl.modal({ backdrop: "static", keyboard: false, show: false });
    if (t.mdlUpdate.length) {
        t.mdlUpdate.modal({ backdrop: "static", keyboard: false, show: false });
    }
    t.mdlUpdate.on('shown.bs.modal', function () {
        let hex = t.frmUpdate.el.color_code.val();
        t.frmUpdate.find('#color_hex').val(hex);
    });

    // ─── ESCAPE HELPER ──────────────────────────────────────────────────────────
    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.normalizeSelect2Items = function (data) {
        if ($.isArray(data)) {
            return data;
        }

        if (data && $.isArray(data.results)) {
            return data.results;
        }

        return [];
    };

    t.ensureSelectValue = function ($select, value, text) {
        var normalizedValue;
        var label;
        var $existingOption;

        if (!$select || !$select.length || value == null || value === "") {
            return;
        }

        normalizedValue = String(value);
        label = $.trim(text || "") || normalizedValue;
        $existingOption = $select.find("option").filter(function () {
            return String($(this).val()) === normalizedValue;
        }).first();

        if ($existingOption.length) {
            $existingOption.text(label).prop("selected", true);
        } else {
            $select.append(new Option(label, normalizedValue, true, true));
        }

        $select.val(normalizedValue).trigger("change");
    };

    t.prefillEditRepeaterRow = function ($row, mapping) {
        var rowData = mapping || {};
        var pcId = rowData.pc_id == null ? "" : String(rowData.pc_id);
        var scId = rowData.sc_id == null ? "" : String(rowData.sc_id);
        var formId = rowData.form_id == null ? "" : String(rowData.form_id);
        var $pcSelect = $row.find(".pc_id");
        var $scSelect = $row.find(".sc_id");
        var $formSelect = $row.find(".form_id");
        var $scField = $scSelect.closest('[class*="col-md-"]');

        if (!$pcSelect.length || !$scSelect.length || !$formSelect.length) {
            return;
        }

        if (pcId) {
            t.ensureSelectValue($pcSelect, pcId, rowData.pc_text);
        } else {
            $pcSelect.val(null).trigger("change");
        }

        if (formId) {
            t.ensureSelectValue($formSelect, formId, rowData.form_text);
        } else {
            $formSelect.val(null).trigger("change");
        }

        $scSelect.empty().append('<option value=""></option>');

        if (!pcId) {
            $scField.hide();
            return;
        }

        $.ajax({
            url: t.config.url.getSubcategory,
            type: "GET",
            dataType: "json",
            data: { pc_id: pcId }
        }).done(function (subData) {
            var subcategories = t.normalizeSelect2Items(subData);

            $.each(subcategories, function (_, item) {
                $scSelect.append(new Option(item.text || item.name || item.id, item.id));
            });

            if (scId) {
                t.ensureSelectValue($scSelect, scId, rowData.sc_text);
                $scField.show();
            } else {
                $scSelect.val(null).trigger("change");
                $scField.toggle(subcategories.length > 0);
            }
        }).fail(function () {
            if (scId) {
                t.ensureSelectValue($scSelect, scId, rowData.sc_text);
                $scField.show();
            } else {
                $scField.hide();
            }
        });
    };

    // ─── ICONS ──────────────────────────────────────────────────────────────────
    t.icons = {
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        active_icon: function () {
            return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
        },
        inactive_icon: function () {
            return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
        }
    };

    // ─── ACTION BUTTON HTML ─────────────────────────────────────────────────────
    t.actionButtonHtml = function (label, classes, id, iconMarkup, dataEditable) {
        var safeLabel = t.escapeHtml(label);
        var safeId = t.escapeHtml(id);
        var safeClasses = t.escapeHtml(classes || "");
        var safeEditable = dataEditable == null ? "" : t.escapeHtml(dataEditable);

        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ', safeClasses,
            '" data-id="', safeId, '"',
            safeEditable.length ? ' data-editable="' + safeEditable + '"' : '',
            ' title="', safeLabel, '" aria-label="', safeLabel, '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    // ─── REPEATER RESET ─────────────────────────────────────────────────────────
    t.resetRepeaterRows = function (wrapper) {
        var $w = $(wrapper);
        if (!$w.length) { return; }
        $w.find("[data-repeater-list]").empty();
        $w.find("[data-repeater-create]").first().trigger("click");
    };

    // ─── DATE-RANGE HELPERS ─────────────────────────────────────────────────────
    t.composeDateRange = function () {
        return $.trim(t.filters.daterange.val() || "");
    };

    t.clearDateRange = function () {
        if (t.filters.rangePicker && typeof moment !== "undefined") {
            t.filters.rangePicker.setStartDate(moment().startOf("day"));
            t.filters.rangePicker.setEndDate(moment().endOf("day"));
        }
        if (t.filters.reportrangeText.length) {
            t.filters.reportrangeText.text(t.tr("select_date_range", "Select date range"));
        }
        t.filters.daterange.val("");
    };

    t.setDateRange = function (start, end) {
        if (!start || !end) { t.clearDateRange(); return; }
        if (t.filters.reportrangeText.length) {
            t.filters.reportrangeText.text(
                start.format("DD-MM-YYYY HH:mm:ss") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
            );
        }
        t.filters.daterange.val(
            start.format("YYYY-MM-DD HH:mm:ss") + " - " + end.format("YYYY-MM-DD HH:mm:ss")
        );
    };

    t.syncDateRangePicker = function () {
        if (!t.filters.rangePicker) return;
        var val = t.filters.daterange.val();
        if (!val) {
            t.clearDateRange();
            return;
        }
        var parts = val.split(" - ");
        if (parts.length !== 2) return;
        var start = moment(parts[0], "YYYY-MM-DD HH:mm:ss");
        var end   = moment(parts[1], "YYYY-MM-DD HH:mm:ss");
        t.setDateRange(start, end);
    };

    // ─── DATE RANGE PICKER INIT ─────────────────────────────────────────────────
    t.initDateRangePicker = function () {
        if (!t.filters.reportrange.length || typeof moment === "undefined" || !$.fn.daterangepicker) { return; }
        if (t.filters.rangePicker) { t.syncDateRangePicker(); return; }
        var start = moment().startOf("day");
        var end   = moment().endOf("day");
        function cb(start, end) {
                $('#reportrange-text').html(
                    start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format('DD-MM-YYYY HH:mm:ss')
                );
                $('#daterange').val(
                    start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss')
                );
        }

        t.filters.reportrange.daterangepicker({
            parentEl: t.filters.wrapper, 
            startDate: start,
            endDate: end,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            autoApply: false,
            autoUpdateInput: false,
            opens: "right",
            drops: "down",
            locale: {
                format: "DD-MM-YYYY HH:mm:ss",
                cancelLabel: "Clear"
            },
            ranges: {
                "Today": [moment().startOf("day"), moment().endOf("day")],
                "Yesterday": [moment().subtract(1,"days").startOf("day"), moment().subtract(1,"days").endOf("day")],
                "Last 7 Days": [moment().subtract(6,"days").startOf("day"), moment().endOf("day")],
                "Last 30 Days": [moment().subtract(29,"days").startOf("day"), moment().endOf("day")],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1,"month").startOf("month"), moment().subtract(1,"month").endOf("month")]
            }
        },cb);
        cb(start, end);

    };

    // ─── FILTER BADGE ───────────────────────────────────────────────────────────
    t.updateFilterBadge = function () {
        var filterCount = 0;
        if (t.config.other_filters && t.config.other_filters.status)  { filterCount += 1; }
        if (t.config.other_filters && t.config.other_filters.based_on && t.config.other_filters.daterange) { filterCount += 1; }

        if (!t.filters.badge.length) { return; }
        if (filterCount > 0) {
            t.filters.badge.text(filterCount).removeClass("d-none");
        } else {
            t.filters.badge.addClass("d-none").text("0");
        }
    };

    // ─── STATUS RENDER ──────────────────────────────────────────────────────────
    t.getStatusIcon = function (status) {
        var normalized = String(status || "").toLowerCase();
        return (normalized === "enabled" || normalized === "active" || normalized === "1")
            ? t.icons.active_icon()
            : t.icons.inactive_icon();
    };

    t.renderStatus = function (status, type) {
        var normalized = String(status == null ? "" : status).toLowerCase();
        var isActive   = normalized === "enabled" || normalized === "active" || normalized === "1";
        var label      = String(status == null ? "" : status);

        if (type && type !== "display") { return label; }
        if (!label.length) { label = isActive ? "Enabled" : "Disabled"; }

        return [
            '<span class="user-list-status d-flex justify-content-center align-items-center ', isActive ? "is-active" : "is-inactive", '">',
            t.getStatusIcon(label),
            '<span class="user-list-status-label ms-2">', t.escapeHtml(label), '</span>',
            '</span>'
        ].join("");
    };

    // ─── ACTION BUTTONS RENDER ──────────────────────────────────────────────────
    t.renderActionButtons = function (record, type) {
        var buttons = [];

        if (type && type !== "display") {
            return record && record.id ? record.id : "";
        }
        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        buttons.push(t.actionButtonHtml(
            t.tr("edit_status", "Edit Status"),
            "dtActEdit",
            record.id,
            t.icons.edit(),
            record.is_editable
        ));

        if (String(record.is_editable) === "1") {
            buttons.push(t.actionButtonHtml(
                t.tr("delete_title", "Delete"),
                "dtActDel is-delete",
                record.id,
                t.icons.delete()
            ));
        }

        return ['<div class="user-list-actions role-list-actions justify-content-start">', buttons.join(""), '</div>'].join("");
    };

    // ─── TABLE HELPERS ──────────────────────────────────────────────────────────
    t.tblHelpers = {
        actions: function () {
            return function (data, type, row) {
                return t.renderActionButtons(row.a || data, type);
            };
        },
        status: function () {
            return function (data, type, row) {
                return t.renderStatus(row.a ? row.a.is_enabled : data, type);
            };
        },
        alignRight: function () {
            return function (d) {
                if (d === "" || d == null) { return null; }
                return '<div class="text-right">' + d + '</div>';
            };
        }
    };

    // ─── DATATABLE ──────────────────────────────────────────────────────────────
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        stateSave: true,
        dom: "lrtip",
        lengthChange: false,
        searching: true,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        stateSaveParams: function (settings, data) {
            data.search.search = "";
        },
        aoColumnDefs: [
            {
                targets: 3,
                render: t.tblHelpers.status()
            },
            {
                targets: 9,
                bSortable: false,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
            }
        ],
        order: [[0, "desc"]],
        fixedColumns: { leftColumns: 1, rightColumns: 1 },
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.status_list,
            type: "post",
            data: function (d) {
                d._token  = t.config.token;
                d.filters  = t.config.other_filters;
                d.main_filter  = t.config.main_filter;
            }
        },
        columns: [
            { 
                data: "a.id",
                orderable: true
            },
            { data: "a.name" },
            { data: "a.company_name" },
            { data: "a.is_enabled" },
            { data: "a.tat_halt" },
            {
                data: "a.attachment",
                bSortable: false,
                render: function (data) {
                    if (data != null && data !== "") {
                        let url = t.config.url.attachmentUrl + "/" + data;
                        return '<a href="' + url + '" target="_blank">' +
                                    '<img src="' + url + '" style="width:80px;">' +
                                '</a>';
                    }
                    return "-";
                }
            },
            {
                data: "a.color_code",
                render: function (data) {
                    return '<span style="background-color:' + data + ';height:30px !important;width:30px;display:block;border-radius:18px;"></span>';
                }
            },
            { data: "a.created_at" },
            { data: "a.updated_at" },
            { data: "a" }
        ],
    });

    // ─── CACHE FILTER VALUES ────────────────────────────────────────────────────
    t.cache_filter_values = function () {
        var v         = $.trim(t.searchbox.val() || "");
        var dateRange = t.composeDateRange();

        t.config.search       = v;
        t.config.other_filters = {};

        if (t.filters.status.val() && t.filters.status.val() !== "null") {
            t.config.other_filters.status = t.filters.status.val();
        }
        if (t.filters.based_on.val() && t.filters.based_on.val() !== "null" && dateRange.length) {
            t.config.other_filters.based_on = t.filters.based_on.val();
            t.config.other_filters.daterange = dateRange;
        }

        t.config.export_filters = btoa(JSON.stringify({
            search: t.config.search,
            other_filters: t.config.other_filters
        }));
        t.updateFilterBadge();
    };

    // ─── FILTER MODAL ───────────────────────────────────────────────────────────
    t.openFilterModal = function (e) {
        e.preventDefault();
        var modalEl = document.getElementById("ticketStatusFilterModal");
        var modal   = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        modalEl.addEventListener("shown.bs.modal", function () {
            t.initDateRangePicker();
            t.syncDateRangePicker();
        }, { once: true });
    };

    t.applyFilters = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        var modalInstance = bootstrap.Modal.getInstance(document.getElementById("ticketStatusFilterModal"));
        if (modalInstance) { modalInstance.hide(); }
    };

    t.btnClrFilter = function () {
        t.config.other_filters = {};
        t.filters.status.val("null").trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.clearDateRange();
        t.updateFilterBadge();
        t.dTbl.ajax.reload();
        var modalInstance = bootstrap.Modal.getInstance(document.getElementById("ticketStatusFilterModal"));
        if (modalInstance) { modalInstance.hide(); }
    };

    // ─── SEARCH / RELOAD ────────────────────────────────────────────────────────
    t.tableSearch = function (e) {
        var v;
        e.preventDefault();
        v = t.searchbox.validate_str_param ? t.searchbox.validate_str_param() : $.trim(t.searchbox.val());
        if (v === false) {
            t.showToast("error", t.tr("search_invalid", "Please enter a valid value for search."));
            return false;
        }
        t.searchbox.val(v);
        t.dTbl.search(v).draw();
    };

    t.reload = function (e) {
        var v;
        if (e) { e.preventDefault(); }
        v = t.searchbox.validate_str_param ? t.searchbox.validate_str_param() : $.trim(t.searchbox.val());
        if (v !== false) {
            t.searchbox.val(v);
            t.dTbl.search(v).draw();
            return false;
        }
        t.dTbl.ajax.reload();
    };

    t.changePageLength = function (e) {
        var length = parseInt(t.pageLength.val(), 10);
        if (e) { e.preventDefault(); }
        if (length > 0) { t.dTbl.page.len(length).draw(false); }
    };

    // ─── FORM REPEATER VISIBILITY ───────────────────────────────────────────────
    function updateCreateFormRepeaterVisibility() {
        var val = t.mdl.frmEl.status_form.val();
        if (val === "1") {
            t.mdl.frmEl.form_repeater.find("[data-repeater-list]").empty();
            t.mdl.frmEl.form_repeater.find("[data-repeater-create]").first().trigger("click");
            t.mdl.frmEl.form_repeater.show();
        } else {
            t.mdl.frmEl.form_repeater.hide();
        }
    }

    function updateEditFormRepeaterVisibility() {
        var val = t.frmUpdate.el.status_form.val();
        if (val === "1") {
            t.frmUpdate.el.form_repeater.show();
        } else {
            t.frmUpdate.el.form_repeater.hide();
        }
    }

    t.mdl.frmEl.status_form.on("change", updateCreateFormRepeaterVisibility);
    t.frmUpdate.el.status_form.on("change", updateEditFormRepeaterVisibility);

    // ─── RESET FORM ─────────────────────────────────────────────────────────────
    t.resetFrm = function () {
        t.mdl.frm.trigger("reset");
        if (t.frmUpdate.length) { t.frmUpdate.trigger("reset"); }
        if (t.frmValidator) { t.frmValidator.resetForm(); }
        if (t.frmUpdateStatusValidator) { t.frmUpdateStatusValidator.resetForm(); }

        t.mdl.frmEl.id.val("");
        t.mdl.frmEl.forAction.val("");
        t.mdl.frmEl.statusname.val("").attr("disabled", false);
        t.mdl.frmEl.status.val("").attr("disabled", false).trigger("change");
        t.mdl.frmEl.tat_halt.val("").attr("disabled", false).trigger("change");
        t.mdl.frmEl.color_code.val("");
        $('#color_hex').val("");
        t.mdl.frmEl.ticket_type.val(null).attr("disabled", false).trigger("change");
        t.mdl.frmEl.attachment.val("").attr("disabled", false);
        t.mdl.frmEl.status_form.val("").trigger("change");
        t.mdl.frmEl.company_id.empty().val("").trigger("change");
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frm.find(".error").removeClass("error");

        t.frmUpdate.el.id.val("");
        t.frmUpdate.el.forAction.val("");
        t.frmUpdate.el.statusname.val("");
        t.frmUpdate.el.status.val("").trigger("change");
        t.frmUpdate.el.tat_halt.val("").trigger("change");
        t.frmUpdate.el.color_code.val("");
        t.frmUpdate.el.ticket_type.val(null).trigger("change");
        t.frmUpdate.el.attachment.val("");
        t.frmUpdate.el.status_form.val("").trigger("change");
        t.frmUpdate.el.company_id.empty().val("").trigger("change");
        t.frmUpdate.find("label.error").remove();
        t.frmUpdate.find(".amg-form-error-wrap").empty();
        t.frmUpdate.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.frmUpdate.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.frmUpdate.find(".error").removeClass("error");
        t.frmUpdate.find("#deleteFileCheckbox").prop("checked", false);
        t.frmUpdate.find("#imgview").attr("src", "");
        t.frmUpdate.find("#uploadedFileSection").addClass("d-none").hide();

        t.mdl.frmEl.form_repeater.find("[data-repeater-list]").empty();
        t.resetRepeaterRows(t.frmUpdate.el.form_repeater);
        t.mdl.frmEl.form_repeater.hide();
        t.frmUpdate.el.form_repeater.hide();
    };

    // ─── ADD STATUS (open create modal) ─────────────────────────────────────────
    t.addStatus = function (e) {
        e.preventDefault();
        t.resetFrm();
        if (t.frmValidator) { t.frmValidator.resetForm(); }

        t.mdl.frmEl.company_id.empty();
        if (t.config.company &&
            t.config.company.company_id !== null &&
            t.config.company.company_name !== null) {
            var opt = new Option(t.config.company.company_name, t.config.company.company_id, true, true);
            t.mdl.frmEl.company_id.append(opt).trigger("change");
        } else {
            t.mdl.frmEl.company_id.val(null).trigger("change");
        }

        t.mdl.modal("show");
        t.mdl.frmEl.form_repeater.hide();
    };

    // ─── CREATE STATUS ──────────────────────────────────────────────────────────
    t.createStatus = function (e) {
        e.preventDefault();

        var statusFormValue = t.mdl.frmEl.status_form.val();
        if (statusFormValue === "1") {
            t.mdl.frmEl.form_repeater.find(".pc_id").each(function () {
                $(this).rules("add", { required: true });
            });
            t.mdl.frmEl.form_repeater.find(".form_id").each(function () {
                $(this).rules("add", { required: true });
            });
        }

        if (!t.frmValidator || t.frmValidator.form() === false || t.httpCall === false) {
            return false;
        }

        t.httpCall = false;
        t.httpPostPath = t.config.url.create_status;
        var formData = new FormData(t.mdl.frm[0]);

        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.mdl.modal("hide");
                    t.resetFrm();
                    t.showToast("success", data).then(function () {
                        t.dTbl.ajax.reload(null, false);
                    });
                } else {
                    t.showToast("error", data);
                }
            }
        });
        http.fail(function (xhr) {
            var data = (xhr && xhr.responseJSON) ? xhr.responseJSON : { msg: t.tr("something_went_wrong", "Something went wrong.") };
            t.showToast("error", data);
        });
        http.always(function () { t.httpCall = true; });
    };

    // ─── LOAD FORM (edit modal) ──────────────────────────────────────────────────
    t.loadForm = function (data) {
        t.resetFrm();
        if (t.frmUpdateStatusValidator) { t.frmUpdateStatusValidator.resetForm(); }

        t.frmUpdate.el.statusname.val(data.name);
        t.frmUpdate.el.status.val(data.is_enabled).trigger("change");
        t.frmUpdate.el.tat_halt.val(data.tat_halt).trigger("change");
        t.frmUpdate.el.color_code.val(data.color_code).trigger('input');
        t.frmUpdate.el.status_form.val(data.status_form).trigger("change");
        t.frmUpdate.el.id.val(data.id);

        t.frmUpdate.el.company_id.empty();
        var compOpt = new Option(data.company_name, data.company_id, true, true);
        t.frmUpdate.el.company_id.append(compOpt).trigger("change");

        if (data.attachment !== null && data.attachment !== "") {
            t.frmUpdate.find("#imgview").attr("src", t.config.imgviewpath + "/" + data.attachment);
            t.frmUpdate.find("#uploadedFileSection").removeClass("d-none").show();
        } else {
            t.frmUpdate.find("#imgview").attr("src", "");
            t.frmUpdate.find("#uploadedFileSection").addClass("d-none").hide();
        }

        if (data.availableTicketTypes != null && data.availableTicketTypes.length > 0) {
            t.frmUpdate.el.ticket_type.empty();
            $.each(data.availableTicketTypes, function (i, v) {
                var isSelected = false;
                if (data.ticketTypes != null) {
                    $.each(data.ticketTypes, function (j, selectedType) {
                        if (selectedType.id == v.id) { isSelected = true; return false; }
                    });
                }
                if (!t.frmUpdate.el.ticket_type.find('option[value="' + v.id + '"]').length) {
                    t.frmUpdate.el.ticket_type.append(new Option(v.name, v.id, isSelected, isSelected));
                }
            });
        }
        t.frmUpdate.el.ticket_type.trigger("change");

        updateEditFormRepeaterVisibility();

        var forms = $.map(data.forms || [], function (value) {
            return {
                pc_id: value.problem_category_id,
                sc_id: value.sub_category_id,
                form_id: value.form_id,
                pc_text: value.problem_category_text || "",
                sc_text: value.sub_category_text || "",
                form_text: value.form_text || ""
            };
        });

        if (forms.length) {
            t.frmUpdate.el.form_repeater.setList(forms);
        } else {
            t.resetRepeaterRows(t.frmUpdate.el.form_repeater);
        }

        setTimeout(function () {
            t.frmUpdate.el.form_repeater.find("[data-repeater-item]").each(function (index) {
                t.prefillEditRepeaterRow($(this), forms[index] || {});
            });
        }, 0);

        t.data.id = data.id;
    };

    // ─── EDIT STATUS ─────────────────────────────────────────────────────────────
    t.editStatus = function (e) {
        e.preventDefault();
        t.isEditMode = true;

        var $trigger = $(e.currentTarget);
        var staId = $trigger.attr("data-id");
        var dataEditable = $trigger.attr("data-editable");

        if (dataEditable == 0) {
            t.frmUpdate.el.iseditable.hide();
        } else {
            t.frmUpdate.el.iseditable.show();
        }

        var http = $.get(t.config.url.edit + "/" + staId);
        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.mdlUpdate.title.html(t.tr("edit_status", "Edit Ticket Status"));
                    t.frmUpdate.el.btnUpdate.text("Update");
                    t.frmUpdate.el.forAction.val("edit");
                    t.mdlUpdate.modal("show");
                    t.loadForm(data.data);
                } else {
                    t.showToast("error", data);
                }
            }
        });
        http.fail(function (xhr) {
            var data = (xhr && xhr.responseJSON) ? xhr.responseJSON : { msg: t.tr("something_went_wrong", "Something went wrong.") };
            t.showToast("error", data);
        });
        http.always(function () { t.httpCall = true; });
    };

    // ─── UPDATE STATUS ───────────────────────────────────────────────────────────
    t.updateStatus = function (e) {
        e.preventDefault();

        var statusFormValue = t.frmUpdate.el.status_form.val();
        if (statusFormValue === "1") {
            t.frmUpdate.el.form_repeater.find(".pc_id").each(function () {
                $(this).rules("add", { required: true });
            });
            t.frmUpdate.el.form_repeater.find(".form_id").each(function () {
                $(this).rules("add", { required: true });
            });
        }

        if (!t.frmUpdateStatusValidator || t.frmUpdateStatusValidator.form() === false || t.httpCall === false) {
            return false;
        }

        t.httpCall = false;
        t.httpPostPath = t.config.url.update;
        var formData = new FormData(t.frmUpdate[0]);
        formData.append("delete_attachment", t.frmUpdate.find("#deleteFileCheckbox").is(":checked") ? 1 : 0);

        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.mdlUpdate.modal("hide");
                    t.showToast("success", data).then(function () {
                        t.dTbl.ajax.reload(null, false);
                    });
                } else {
                    t.showToast("error", data);
                }
            }
        });
        http.fail(function (xhr) {
            var data = (xhr && xhr.responseJSON) ? xhr.responseJSON : { msg: t.tr("something_went_wrong", "Something went wrong.") };
            t.showToast("error", data);
        });
        http.always(function () { t.httpCall = true; });
    };

    // ─── DELETE STATUS ───────────────────────────────────────────────────────────
    t.deleteStatus = function (e) {
        e.preventDefault();
        var $trigger = $(e.currentTarget);
        var Id = $trigger.attr("data-id");
        t.showConfirm({
            title: t.tr("delete_title", "Delete"),
            text:  t.tr("delete_record", "Are you sure you want to delete this ticket status?")
        }).done(function (isConfirmed) {
            if (!isConfirmed) { return; }

            var http = $.get(t.config.url.delete + "/" + Id);
            http.done(function (data) {
                if (typeof data === "object") {
                    if (data.status === "success") {
                        t.showToast("success", data);
                        t.dTbl.ajax.reload();
                    } else {
                        t.showToast("error", data);
                    }
                }
            });
            http.fail(function (xhr) {
                var data = (xhr && xhr.responseJSON) ? xhr.responseJSON : { msg: t.tr("something_went_wrong", "Something went wrong.") };
                t.showToast("error", data);
            });
            http.always(function () { t.httpCall = true; });
        });
    };

    // ─── VALIDATORS ─────────────────────────────────────────────────────────────
    var commonRules = {
        name:        { required: true, pattern: /^[A-Za-z0-9\s]+$/ },
        is_enabled:  { required: true },
        status_form: { required: true },
        company_id:  { required: true }
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

    var commonErrorPlacement = function (error, element) {
        var errorWrap = t.getModalErrorWrap(element);

        if (errorWrap.length) {
            error.appendTo(errorWrap);
        } else if (element.closest(".input-group").length) {
            error.insertAfter(element.closest(".input-group"));
        } else {
            error.appendTo(element.closest("div"));
        }

        t.updateValidationState(element, true);
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: commonRules,
        errorPlacement: commonErrorPlacement,
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.frmUpdateStatusValidator = t.frmUpdate.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: commonRules,
        errorPlacement: commonErrorPlacement,
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.mdl.frm.find("select").on("change", function () {
        var element = $(this);

        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });
    t.frmUpdate.find("select").on("change", function () {
        var element = $(this);

        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    // ─── SELECT2 INIT HELPERS ───────────────────────────────────────────────────
    var select2Opts = { width: "100%" };

    t.getSelect2DropdownParent = function ($el) {
        if (!$el || !$el.length) { return $(document.body); }

        var $field = $el.closest(".amg-form-field");
        if ($field.length) {
            $field.css("position", "relative");
            return $field;
        }

        var $group = $el.closest(".input-group");
        if ($group.length) {
            $group.css("position", "relative");
            return $group;
        }

        var $modal = $el.closest(".modal");
        return $modal.length ? $modal : $(document.body);
    };

    t.initCompanySelect2 = function ($el) {
        if (!$el || !$el.length) { return; }
        if ($el.hasClass("select2-hidden-accessible")) { $el.select2("destroy"); }

        var $modal = $el.closest(".modal");
        $el.select2({
            placeholder: "Select Company",
            allowClear: true,
            width: "100%",
            dropdownParent: $modal.length ? $modal : $(document.body),
            ajax: {
                url: t.config.url.getCompany,
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { search: params.term || "", page: params.page || 1 };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: { more: data.pagination.more }
                    };
                },
                cache: true
            }
        });
    };

    t.initTicketTypeSelect2 = function (el, companyEl) {
        if (!el || !el.length) { return; }
        if (el.hasClass("select2-hidden-accessible")) { el.select2("destroy"); }

        var $modal = el.closest(".modal");
        el.select2({
            width: "100%",
            placeholder: "Select Ticket Type",
            allowClear: true,
            multiple: true,
            dropdownParent: $modal.length ? $modal : $(document.body),
            ajax: {
                url: t.config.url.getTicketType,
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return { search: params.term || "", company_id: companyEl.val() };
                },
                processResults: function (res) {
                    var data = [];
                    if (res.status === "success") {
                        data = $.map(res.data, function (obj) {
                            return { id: obj.id, text: obj.name };
                        });
                    }
                    return { results: data };
                },
                cache: true
            }
        });
    };

    t.initCreateModalSelect2 = function () {
        if (t.mdl.frmEl.status.hasClass("select2-hidden-accessible")) { t.mdl.frmEl.status.select2("destroy"); }
        if (t.mdl.frmEl.tat_halt.hasClass("select2-hidden-accessible")) { t.mdl.frmEl.tat_halt.select2("destroy"); }
        if (t.mdl.frmEl.status_form.hasClass("select2-hidden-accessible")) { t.mdl.frmEl.status_form.select2("destroy"); }

        t.mdl.frmEl.status.select2($.extend({}, select2Opts, {
            placeholder: "Select Status",
            allowClear: true,
            dropdownParent: t.mdl.frmEl.status.parent()
        }));
        t.mdl.frmEl.tat_halt.select2($.extend({}, select2Opts, {
            placeholder: "Select TAT",
            allowClear: true,
            dropdownParent: t.mdl.frmEl.tat_halt.parent()
        }));
        t.mdl.frmEl.status_form.select2($.extend({}, select2Opts, {
            placeholder: "Show status form",
            allowClear: true,
            dropdownParent: $("#statusModal")
        }));

        t.initCompanySelect2(t.mdl.frmEl.company_id);
        t.initTicketTypeSelect2(t.mdl.frmEl.ticket_type, t.mdl.frmEl.company_id);
    };

    // ─── REPEATER BUILDER ───────────────────────────────────────────────────────
    function buildRepeaterConfig(formRepeaterEl, isUpdate) {
        return {
            defaultValues: { matchType: "all" },
            show: function () {
                var $row = $(this);
                var $scField = $row.find(".sc_id").closest(".col-md-3");
                var $pcSelect = $row.find(".pc_id");
                var $scSelect = $row.find(".sc_id");
                var $formSelect = $row.find(".form_id");
                $row.slideDown();

                $row.find(".select2-container").remove();
                $row.find(".pc_id, .sc_id, .form_id").each(function () {
                    $(this)
                        .removeClass("select2-hidden-accessible")
                        .removeAttr("data-select2-id")
                        .removeAttr("aria-hidden")
                        .removeAttr("tabindex");
                });
                $row.find("option").removeAttr("data-select2-id");

                var pc_id = $pcSelect.select2({
                    width: "100%",
                    placeholder: "Problem Category",
                    allowClear: true,
                    dropdownParent: t.getSelect2DropdownParent($pcSelect)
                });

                var sc_id = $scSelect.select2({
                    width: "100%",
                    placeholder: "Sub Category",
                    allowClear: true,
                    dropdownParent: t.getSelect2DropdownParent($scSelect),
                    ajax: {
                        url: t.config.url.getSubcategory,
                        dataType: "json",
                        delay: 250,
                        type: "GET",
                        headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                        data: function (params) {
                            return { q: params.term, page: params.page, pc_id: pc_id.val() };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            if (data && data.length > 0) {
                                $scField.show();
                            } else {
                                $scField.hide();
                            }
                            return {
                                results: data,
                                pagination: { more: (params.page * 30) < data.total }
                            };
                        },
                        cache: true
                    }
                });
                $scField.hide();

                $formSelect.select2({
                    width: "100%",
                    placeholder: "Form",
                    allowClear: true,
                    dropdownParent: t.getSelect2DropdownParent($formSelect),
                    ajax: {
                        url: t.config.url.getForm,
                        dataType: "json",
                        data: function (params) {
                            return { search: params.term, page: params.page || 1 };
                        }
                    }
                });

                var preventChangeTrigger = false;

                pc_id.on("change", function () {
                    var selectedPcId = $(this).val();
                    preventChangeTrigger = true;
                    sc_id.val(null).trigger("change");
                    preventChangeTrigger = false;

                    if (!selectedPcId) {
                        $scField.hide();
                        return;
                    }

                    $.ajax({
                        url: t.config.url.getSubcategory,
                        type: "GET",
                        dataType: "json",
                        data: { pc_id: selectedPcId },
                        success: function (data) {
                            var subCount = (data && data.length) ? data.length : 0;
                            var selectedPairs = [];
                            formRepeaterEl.find(".pc_id").each(function (index) {
                                var curPcId = $(this).val();
                                var curScId = formRepeaterEl.find(".sc_id").eq(index).val();
                                if (curPcId) {
                                    selectedPairs.push({ pc_id: curPcId, sc_id: curScId });
                                }
                            });
                            var samePcCount = selectedPairs.filter(function (p) {
                                return p.pc_id === selectedPcId;
                            }).length;
                            if (subCount <= 1 && samePcCount > 1) {
                                t.showToast("error", "This Problem Category is already selected. Please choose a different one.");
                                pc_id.val(null).trigger("change");
                                return;
                            }
                            if (data && data.length > 0) {
                                $scField.show();
                            } else {
                                $scField.hide();
                            }
                        }
                    });

                    var selectedPairs = [];
                    formRepeaterEl.find(".pc_id").each(function (index) {
                        var curPcId = $(this).val();
                        var curScId = formRepeaterEl.find(".sc_id").eq(index).val();
                        if (curPcId) { selectedPairs.push({ pc_id: curPcId, sc_id: curScId }); }
                    });

                    // var duplicatePcCount = selectedPairs.filter(function (p) { return p.pc_id === selectedPcId; }).length;
                    // if (duplicatePcCount > 1) {
                    //     var hasScId = selectedPairs.some(function (p) { return p.pc_id === selectedPcId && p.sc_id; });
                    //     if (!hasScId) {
                    //         t.showToast("error", t.tr("duplicate_problem_category", "This Problem Category is already selected. Please choose a different one."));
                    //         pc_id.val(null).trigger("change");
                    //         return;
                    //     }
                    // }

                    sc_id.on("change", function () {
                        if (preventChangeTrigger) return;
                        var selectedPcId = pc_id.val();
                        var selectedScId = $(this).val();
                        var selectedPairs = [];
                        formRepeaterEl.find(".pc_id").each(function (index) {
                            var curPcId = $(this).val();
                            var curScId = formRepeaterEl.find(".sc_id").eq(index).val();
                            if (curPcId) {
                                selectedPairs.push({ pc_id: curPcId, sc_id: curScId });
                            }
                        });
                        var isDuplicate = selectedPairs.filter(function (p) {
                            return p.pc_id === selectedPcId && p.sc_id === selectedScId;
                        }).length > 1;
                        if (isDuplicate) {
                            t.showToast("error", "This Subcategory is already selected.");
                            preventChangeTrigger = true;
                            sc_id.val(null).trigger("change");
                            preventChangeTrigger = false;
                        }
                    });
                });
            },
            hide: function (deleteElement) {
                $(this).slideUp(function () { deleteElement(); });
            }
        };
    }

    // Init repeaters
    t.mdl.frmEl.form_repeater.repeater(buildRepeaterConfig(t.mdl.frmEl.form_repeater, false));
    t.frmUpdate.el.form_repeater.repeater($.extend(
        buildRepeaterConfig(t.frmUpdate.el.form_repeater, true),
        { isFirstItemUndeletable: true }
    ));

    // ─── STATIC SELECT2 INITS ───────────────────────────────────────────────────
    t.filters.status.select2($.extend({}, select2Opts, {
        placeholder: "Filter by status",
        dropdownParent: t.filters.wrapper
    }));
    t.filters.based_on.select2($.extend({}, select2Opts, {
        placeholder: "Filter by date",
        dropdownParent: t.filters.wrapper
    }));

    // Create modal
    t.initCreateModalSelect2();
    t.mdl.frmEl.pc_id.select2($.extend({}, select2Opts, {
        placeholder: "Problem Category",
        allowClear: true,
        dropdownParent: t.getSelect2DropdownParent(t.mdl.frmEl.pc_id)
    }));
    t.mdl.frmEl.sc_id.select2($.extend({}, select2Opts, {
        placeholder: "Sub Category",
        allowClear: true,
        dropdownParent: t.getSelect2DropdownParent(t.mdl.frmEl.sc_id),
        ajax: {
            url: t.config.url.getSubcategory,
            dataType: "json",
            data: function (params) {
                return { q: params.term, page: params.page || 1, pc_id: t.mdl.frmEl.pc_id.val() };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                if (data && data.length > 0) {
                    t.mdl.frmEl.sc_id.parent().show();
                } else {
                    t.mdl.frmEl.sc_id.parent().hide();
                }
                return { results: data, pagination: { more: (params.page * 30) < data.total } };
            }
        }
    }));
    t.mdl.frmEl.sc_id.parent().hide();

    t.mdl.frmEl.form_id.select2($.extend({}, select2Opts, {
        placeholder: "Form",
        allowClear: true,
        dropdownParent: t.getSelect2DropdownParent(t.mdl.frmEl.form_id),
        ajax: {
            url: t.config.url.getForm,
            dataType: "json",
            data: function (params) {
                return { search: params.term, page: params.page || 1 };
            }
        }
    }));

    t.mdl.frmEl.pc_id.on("change", function () {
        var selectedPcId = t.mdl.frmEl.pc_id.val();
        if (selectedPcId) {
            $.ajax({
                url: t.config.url.getSubcategory,
                type: "GET",
                dataType: "json",
                data: { pc_id: selectedPcId },
                success: function (data) {
                    if (data && data.length > 0) {
                        t.mdl.frmEl.sc_id.parent().show();
                    } else {
                        t.mdl.frmEl.sc_id.parent().hide();
                    }
                }
            });
        } else {
            t.mdl.frmEl.sc_id.parent().hide();
        }
    });

    // Update modal
    t.frmUpdate.el.status.select2($.extend({}, select2Opts, {
        placeholder: "Select Status",
        allowClear: true,
        dropdownParent: t.frmUpdate.el.status.parent()
    }));
    t.frmUpdate.el.tat_halt.select2($.extend({}, select2Opts, {
        placeholder: "Select TAT",
        allowClear: true,
        dropdownParent: t.frmUpdate.el.tat_halt.parent()
    }));
    t.frmUpdate.el.status_form.select2($.extend({}, select2Opts, {
        placeholder: "Show status form",
        allowClear: true,
        dropdownParent: t.frmUpdate.el.status_form.parent()
    }));

    t.initCompanySelect2(t.frmUpdate.el.company_id);
    t.initTicketTypeSelect2(t.frmUpdate.el.ticket_type, t.frmUpdate.el.company_id);

    t.mdl.frmEl.company_id.on("change", function () {
        t.mdl.frmEl.ticket_type.val(null).trigger("change");
    });
    t.frmUpdate.el.company_id.on("change", function () {
        if (t.isEditMode) { t.isEditMode = false; return; }
        t.frmUpdate.el.ticket_type.val(null).trigger("change");
    });

    // ─── FILTER STATUS OPTIONS ──────────────────────────────────────────────────
    t.filters.fun.reload_status();

    // ─── EVENT BINDINGS ─────────────────────────────────────────────────────────
    t.filters.btnFilter.on("click",    $.proxy(t.applyFilters, t));
    t.filters.btnfilterclr.on("click", t.btnClrFilter);

    t.content.on("click", ".btn-open-filter",                  $.proxy(t.openFilterModal, t));
    t.content.on("click", ".add-ticket-type, .btn-add-status", $.proxy(t.addStatus, t));
    t.content.on("click", ".btn-searchbox",                    $.proxy(t.tableSearch, t));
    t.content.on("click", ".btn-reload-list",                  $.proxy(t.reload, t));
    t.content.on("keyup", ".searchbox", function (e) {
        if (e.keyCode === 13 || !this.value.length) { t.tableSearch(e); }
    });
    t.content.on("change", ".tkt-status-page-length", $.proxy(t.changePageLength, t));
    t.mdl.on("shown.bs.modal", function () {
        let hex = t.mdl.frmEl.color_code.val();
        $('#color_hex').val(hex);
        t.initCreateModalSelect2();
    });

    t.table.on("click", ".dtActEdit", $.proxy(t.editStatus, t));
    t.table.on("click", ".dtActDel",  $.proxy(t.deleteStatus, t));

    t.content.on("click", ".js-act-create", $.proxy(t.createStatus, t));
    t.content.on("click", ".js-act-update", $.proxy(t.updateStatus, t));
    t.content.on('input', '#color_code', function () {
        let hex = $(this).val();
        let container = $(this).closest('.modal');
        container.find('#color_hex').val(hex);
    });
    // ─── INITIAL LOAD ───────────────────────────────────────────────────────────
    t.updateFilterBadge();
    t.dTbl.ajax.reload();
};
