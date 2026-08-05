var TicketMail = function(config) {
    var t = this;
    t.config = config;
    t.page = $("#main-outgoing-mail-wrapper");
    t.table = t.page.find("#mytable");
    t.searchInput = t.page.find(".plain-search");
    t.pageLength = t.page.find(".userModulePageLenth");
    t.actionStyleScope = t.page.find(".list-view-panel").first();
    t.searchTimer = null;

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.btn = {};
    t.btn.add = t.page.find(".btn-add-mail");
    t.btn.search = t.page.find(".btn-search-mail-list");
    t.btn.reload = t.page.find(".btn-reload-list");

    t.mdl = t.page.find("#mailModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#mail-mdl-frm");
    t.mdl.frmEl = {
        id: t.mdl.frm.find("#id"),
        forAction: t.mdl.frm.find("#for_action"),
        mail_driver: t.mdl.frm.find("#mail_driver"),
        mail_enabled: t.mdl.frm.find("#mail_enabled"),
        mail_host: t.mdl.frm.find("#mail_host"),
        mail_port: t.mdl.frm.find("#mail_port"),
        mail_username: t.mdl.frm.find("#mail_username"),
        mail_password: t.mdl.frm.find("#mail_password"),
        mail_encryption: t.mdl.frm.find("#mail_encryption"),
        mail_from_address: t.mdl.frm.find("#mail_from_address"),
        mail_from_name: t.mdl.frm.find("#mail_from_name"),
        company_id: t.mdl.frm.find("#company_id")
    };
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdlUpdate = t.page.find("#mailupdateModal");
    t.frmUpdate = t.mdlUpdate.find("#mail_update_form");
    t.frmUpdate.el = {
        id: t.frmUpdate.find("#id"),
        forAction: t.frmUpdate.find("#forAction"),
        token: t.frmUpdate.find("#token"),
        mail_driver: t.frmUpdate.find("#edit_mail_driver"),
        mail_enabled: t.frmUpdate.find("#edit_mail_enabled"),
        mail_host: t.frmUpdate.find("#edit_mail_host"),
        mail_port: t.frmUpdate.find("#edit_mail_port"),
        mail_username: t.frmUpdate.find("#edit_mail_username"),
        mail_password: t.frmUpdate.find("#edit_mail_password"),
        mail_encryption: t.frmUpdate.find("#edit_mail_encryption"),
        mail_from_address: t.frmUpdate.find("#edit_mail_from_address"),
        mail_from_name: t.frmUpdate.find("#edit_mail_from_name"),
        company_id: t.frmUpdate.find("#edit_company_id"),
        btnUpdate: t.frmUpdate.find("#btnUpdate")
    };
    t.mdlUpdate.title = t.mdlUpdate.find(".modal-title");

    t.icons = {
        edit: function() {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function() {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        active: function(){
            return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
        },
        inactive: function(){
            return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>'
        }

    };

    t.escapeHtml = function(value) {
        if (value === null || value === undefined) return "";
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getIcon = function(key) {
        return t.icons[key] ? t.icons[key]() : "";
    };

    t.quickActionButtonHtml = function(label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            t.escapeHtml(extraClass || ""),
            " ",
            cls,
            '" data-id="',
            t.escapeHtml(id),
            '" title="',
            t.escapeHtml(label),
            '" aria-label="',
            t.escapeHtml(label),
            '">',
            t.getIcon(iconKey),
            "</button>"
        ].join("");
    };

    t.renderStatusCell = function(value, type) {
        if (type !== "display") {
            return value;
        }
        var isEnabled = (value || "").toString().toLowerCase() === "enabled";
        var label = isEnabled
            ? (t.config.translations.status_enabled || "Enabled")
            : (t.config.translations.status_disabled || "Disabled");
        var cls = isEnabled ? "is-enabled" : "is-disabled";
        var icon = isEnabled
            ? t.icons.active()
            : t.icons.inactive();
        return [
            '<span class="mail-status-badge d-flex ', cls, '">',
                '<span class="mail-status-badge__icon">', icon, '</span>',
                '<span class="ms-1">', t.escapeHtml(label), '</span>',
            '</span>'
        ].join("");
    };

    t.renderActionsCell = function(record, type) {
        if (type !== "display") {
            return record && record.id ? record.id : "";
        }

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions role-list-actions justify-content-start">',
            t.quickActionButtonHtml(t.config.translations.edit_mail || "Edit", "dtActEdit", record.id, "edit"),
            t.quickActionButtonHtml(t.config.translations.delete || "Delete", "dtActDel", record.id, "delete", "is-delete"),
            "</div>"
        ].join("");
    };

    t.showModal = function($modal) {
        if (!$modal || !$modal.length) return;

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance($modal[0]).show();
            return;
        }

        $modal.modal("show");
    };

    t.hideModal = function($modal) {
        if (!$modal || !$modal.length) return;

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance($modal[0]).hide();
            return;
        }

        $modal.modal("hide");
    };

    t.getSelect2DropdownParent = function($el) {
        if (!$el || !$el.length) {
            return $(document.body);
        }

        if ($el.parent().length) {
            return $el.parent();
        }

        if ($el.closest(".modal").length) {
            return $el.closest(".modal");
        }

        return $(document.body);
    };

    t.prepareModalFieldLayout = function(form) {
        if (!form || !form.length) {
            return;
        }        form.find(".input-group").each(function() {
            $(this).closest(".amg-form-field").addClass("amg-form-field-row");
        });
    };

    t.ensureSelectOption = function($select, value, label) {
        if (!$select || !$select.length || value === null || value === undefined || value === "") {
            return;
        }

        if (!$select.find("option[value='" + String(value).replace(/'/g, "\\'") + "']").length) {
            $select.append(new Option(label || value, value, false, false));
        }
    };

    t.getModalErrorWrap = function(element) {
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

    t.updateValidationState = function(element, hasError) {
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

    t.scheduleTableLayoutSync = function() {
        window.setTimeout(function() {
            if (!t.dTbl) return;

            try {
                t.dTbl.columns.adjust();

                if (typeof t.dTbl.fixedColumns === "function") {
                    var fixedColumnsApi = t.dTbl.fixedColumns();

                    if (fixedColumnsApi && typeof fixedColumnsApi.relayout === "function") {
                        fixedColumnsApi.relayout();
                    } else if (fixedColumnsApi && typeof fixedColumnsApi.update === "function") {
                        fixedColumnsApi.update();
                    }
                }
            } catch (err) {
                console.warn("Outgoing mail table relayout skipped:", err);
            }
        }, 0);
    };

    t.syncTableMetaVisibility = function() {
        var wrapper;
        var info;
        var paginate;
        var records = 0;

        if (!t.dTbl) {
            return;
        }

        wrapper = t.table.closest(".dataTables_wrapper");
        info = wrapper.find(".dataTables_info");
        paginate = wrapper.find(".dataTables_paginate");
        records = t.dTbl.page.info().recordsDisplay || 0;

        info.toggle(records > 0);
        paginate.toggle(records > 0);
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        order: [[8, "desc"]],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        scrollX: true,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        lengthChange: false,
        searching: false,
        ajax: {
            url: t.config.url.mail_list,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $.trim(t.searchInput.val() || "");
            }
        },
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        dom: "<'dt-top'<'left'f><'right'l>>tr<'dt-bottom'<'left'i><'right'p>>",
        columns: [
            { data: "a.mail_driver", defaultContent: "" },
            { data: "a.company_name", defaultContent: "" },
            {
                data: "a.mail_enabled",
                defaultContent: "",
                render: function(data, type) {
                    return t.renderStatusCell(data, type);
                }
            },
            { data: "a.mail_host", defaultContent: "" },
            { data: "a.mail_port", defaultContent: "" },
            { data: "a.mail_username", defaultContent: "" },
            { data: "a.mail_from_address", defaultContent: "" },
            { data: "a.mail_from_name", defaultContent: "" },
            { data: "a.updated_at_display", 
                defaultContent: "" ,
                className:"amg-table-col-144"
            },
            {
                data: "a",
                orderable: false,
                searchable: false,
                width: "110px",
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function(data, type, row) {
                    return t.renderActionsCell(row.a || {}, type);
                }
            }
        ],
        fnInitComplete: function() {
            if (t.pageLength.length) {
                t.pageLength.val(String(parseInt(t.pageLength.val(), 10) || 10));
            }

            t.syncTableMetaVisibility();
            t.scheduleTableLayoutSync();
        },
        drawCallback: function() {
            t.syncTableMetaVisibility();
            t.scheduleTableLayoutSync();
        }
    });

    t.resetCreateForm = function() {
        t.mdl.frm[0].reset();
        t.mdl.frmEl.id.val("");
        t.mdl.frmEl.forAction.val("");
        t.mdl.frmEl.mail_driver.val("");
        t.mdl.frmEl.mail_enabled.val("1").trigger("change");
        t.mdl.frmEl.mail_encryption.val("ssl").trigger("change");
        t.mdl.frmEl.company_id.val(null).trigger("change");
        t.frmValidator.resetForm();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".is-invalid").removeClass("is-invalid");
    };

    t.resetUpdateForm = function() {
        t.frmUpdate[0].reset();
        t.frmUpdate.el.id.val("");
        t.frmUpdate.el.forAction.val("");
        t.frmUpdate.el.mail_driver.val("");
        t.frmUpdate.el.mail_enabled.val("1").trigger("change");
        t.frmUpdate.el.mail_encryption.val("ssl").trigger("change");
        t.frmUpdate.el.company_id.val(null).trigger("change");
        t.frmUpdateStatusValidator.resetForm();
        t.frmUpdate.find(".amg-form-error-wrap").empty();
        t.frmUpdate.find("label.error").remove();
        t.frmUpdate.find(".is-invalid").removeClass("is-invalid");
    };

    t.prefillDefaultCompany = function(selectElement) {
        if (config.company && config.company.company_id && config.company.company_name) {
            var option = new Option(config.company.company_name, config.company.company_id, true, true);
            selectElement.empty().append(option).trigger("change");
        } else {
            selectElement.val(null).trigger("change");
        }
    };

    t.showAddModal = function(e) {
        if (e) e.preventDefault();

        t.resetCreateForm();
        t.mdl.title.text(t.config.translations.create_mail || "Create Outgoing Email");
        t.prefillDefaultCompany(t.mdl.frmEl.company_id);
        t.showModal(t.mdl);
    };

    t.loadUpdateForm = function(data) {
        t.resetUpdateForm();
        t.frmUpdate.el.mail_driver.val(data.mail_driver || "");
        t.frmUpdate.el.mail_enabled.val(String(data.mail_enabled || "0")).trigger("change");
        t.frmUpdate.el.mail_host.val(data.mail_host || "");
        t.frmUpdate.el.mail_port.val(data.mail_port || "");
        t.frmUpdate.el.mail_username.val(data.mail_username || "");
        t.frmUpdate.el.mail_password.val(data.mail_password || "");
        t.frmUpdate.el.mail_encryption.val(data.mail_encryption || "ssl").trigger("change");
        t.frmUpdate.el.mail_from_address.val(data.mail_from_address || "");
        t.frmUpdate.el.mail_from_name.val(data.mail_from_name || "");
        t.frmUpdate.el.id.val(data.id || "");
        t.frmUpdate.el.forAction.val("edit");

        if (data.company_id && data.company_name) {
            t.frmUpdate.el.company_id
                .empty()
                .append(new Option(data.company_name, data.company_id, true, true))
                .trigger("change");
        } else {
            t.frmUpdate.el.company_id.val(null).trigger("change");
        }
    };

    t.reload = function(e) {
        if (e) e.preventDefault();
        t.dTbl.ajax.reload(null, false);
    };

    t.handleSearchInput = function() {
        window.clearTimeout(t.searchTimer);
        t.searchTimer = window.setTimeout(function() {
            t.dTbl.ajax.reload();
        }, 300);
    };

    t.searchNow = function(e) {
        if (e) e.preventDefault();
        window.clearTimeout(t.searchTimer);
        t.dTbl.ajax.reload();
    };

    t.createMail = function(e) {
        var formData;

        e.preventDefault();
        if (t.frmValidator.form() === false) {
            return false;
        }

        formData = new FormData(t.mdl.frm[0]);
        $.ajax({
            url: t.config.url.create_mail,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        }).done(function(data) {
            if (typeof data === "object" && data.status === "success") {
                t.hideModal(t.mdl);
                sweetAlert("center", "success", data);
                t.dTbl.ajax.reload();
            } else {
                sweetAlert("center", "error", data || { msg: t.config.translations.something_went_wrong });
            }
        }).fail(function() {
            sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
        });
    };

    t.editMail = function(e) {
        var id = $(e.currentTarget).data("id");

        e.preventDefault();
        $.get(t.config.url.edit + "/" + id)
            .done(function(data) {
                if (typeof data === "object" && data.status === "success") {
                    t.mdlUpdate.title.text(t.config.translations.edit_outmail || "Edit Outgoing Email");
                    t.frmUpdate.el.btnUpdate.text(t.config.translations.update || "Update");
                    t.loadUpdateForm(data.data || {});
                    t.showModal(t.mdlUpdate);
                } else {
                    sweetAlert("center", "error", data || { msg: t.config.translations.something_went_wrong });
                }
            })
            .fail(function() {
                sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
            });
    };

    t.updateMail = function(e) {
        var formData;

        e.preventDefault();
        if (t.frmUpdateStatusValidator.form() === false) {
            return false;
        }

        formData = new FormData(t.frmUpdate[0]);
        $.ajax({
            url: t.config.url.update,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        }).done(function(data) {
            if (typeof data === "object" && data.status === "success") {
                t.hideModal(t.mdlUpdate);
                sweetAlert("center", "success", data);
                t.dTbl.ajax.reload();
            } else {
                sweetAlert("center", "error", data || { msg: t.config.translations.something_went_wrong });
            }
        }).fail(function() {
            sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
        });
    };

    t.deleteMail = function(e) {
        var id = $(e.currentTarget).data("id");

        e.preventDefault();
        sweetAlertConfirmation({
            message: t.config.translations.delete_record,
            onConfirm: function() {
                $.get(t.config.url.delete + "/" + id)
                    .done(function(data) {
                        if (typeof data === "object" && data.status === "success") {
                            sweetAlert("center", "success", data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert("center", "error", data || { msg: t.config.translations.something_went_wrong });
                        }
                    })
                    .fail(function() {
                        sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
                    });
            }
        });
    };

    t.buildValidator = function(form) {
        var commonErrorPlacement = function(error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }

            t.updateValidationState(element, true);
        };

        return form.validate({
            onsubmit: false,
            ignore: ":hidden:not(.select2-hidden-accessible)",
            errorElement: "label",
            errorClass: "amg-form-error error",
            rules: {
                company_id: {
                    required: true
                },
                mail_driver: {
                    required: true,
                    clean_text_only: true
                },
                mail_enabled: {
                    required: true
                },
                mail_host: {
                    required: true,
                    clean_text_only: true
                },
                mail_port: {
                    required: true,
                    clean_text_only: true
                },
                mail_username: {
                    required: true,
                    email: true
                },
                mail_password: {
                    required: true,
                    clean_text_only: true
                },
                mail_encryption: {
                    required: true
                },
                mail_from_address: {
                    required: true,
                    email: true
                },
                mail_from_name: {
                    required: true,
                    clean_text_only: true
                }
            },
            messages: {
                company_id: {
                    required: t.config.validation.company_required
                },
                mail_driver: {
                    required: t.config.validation.mail_driver_required
                },
                mail_enabled: {
                    required: t.config.validation.mail_enabled_required
                },
                mail_host: {
                    required: t.config.validation.mail_host_required
                },
                mail_port: {
                    required: t.config.validation.mail_port_required
                },
                mail_username: {
                    required: t.config.validation.mail_username_required,
                    email: t.config.validation.mail_username_email
                },
                mail_password: {
                    required: t.config.validation.mail_password_required
                },
                mail_encryption: {
                    required: t.config.validation.mail_encryption_required
                },
                mail_from_address: {
                    required: t.config.validation.mail_from_address_required,
                    email: t.config.validation.mail_from_address_email
                },
                mail_from_name: {
                    required: t.config.validation.mail_from_name_required
                }
            },
            errorPlacement: commonErrorPlacement,
            highlight: function(element) {
                $(element).addClass("is-invalid");
                t.updateValidationState($(element), true);
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid");
                t.updateValidationState($(element), false);
            },
            success: function(label, element) {
                $(element).removeClass("is-invalid");
                t.updateValidationState($(element), false);
                $(label).remove();
            }
        });
    };

    t.initCompanySelect2 = function($el) {
        var dropdownParent;

        if (!$el || !$el.length) return;
        if ($el.hasClass("select2-hidden-accessible")) {
            $el.select2("destroy");
        }

        dropdownParent = t.getSelect2DropdownParent($el);
        $el.select2({
            placeholder: t.config.translations.select_company || "Select Company",
            allowClear: true,
            width: "100%",
            dropdownParent: dropdownParent,
            minimumInputLength: 0,
            ajax: {
                url: t.config.url.getCompany,
                type: "GET",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || "",
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            }
        });
    };

    t.initSelect2 = function($el) {
        var dropdownParent;

        if (!$el || !$el.length) return;
        if ($el.hasClass("select2-hidden-accessible")) {
            $el.select2("destroy");
        }

        dropdownParent = t.getSelect2DropdownParent($el);
        $el.select2({
            width: "100%",
            dropdownParent: dropdownParent,
            minimumResultsForSearch: Infinity
        });
    };

    t.prepareModalFieldLayout(t.mdl.frm);
    t.prepareModalFieldLayout(t.frmUpdate);

    t.frmValidator = t.buildValidator(t.mdl.frm);
    t.frmUpdateStatusValidator = t.buildValidator(t.frmUpdate);

    t.initCompanySelect2(t.mdl.frmEl.company_id);
    t.initCompanySelect2(t.frmUpdate.el.company_id);
    t.initSelect2(t.mdl.frmEl.mail_enabled);
    t.initSelect2(t.mdl.frmEl.mail_encryption);
    t.initSelect2(t.frmUpdate.el.mail_enabled);
    t.initSelect2(t.frmUpdate.el.mail_encryption);

    t.mdl.frm.find("select").on("change", function() {
        var element = $(this);
        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    t.frmUpdate.find("select").on("change", function() {
        var element = $(this);
        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    t.btn.add.on("click", $.proxy(t.showAddModal, t));
    t.btn.search.on("click", $.proxy(t.searchNow, t));
    t.btn.reload.on("click", $.proxy(t.reload, t));

    t.page.on("click", ".dtActEdit", $.proxy(t.editMail, t));
    t.page.on("click", ".dtActDel", $.proxy(t.deleteMail, t));
    t.page.on("click", ".js-act-create", $.proxy(t.createMail, t));
    t.page.on("click", ".js-act-update", $.proxy(t.updateMail, t));

    t.searchInput.on("input", $.proxy(t.handleSearchInput, t));
    t.searchInput.on("keydown", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            t.searchNow();
        }
    });

    t.pageLength.on("change", function() {
        if (t.dTbl) {
            t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw(false);
        }
    });

    t.mdl.frm.on("submit", $.proxy(t.createMail, t));
    t.frmUpdate.on("submit", $.proxy(t.updateMail, t));
    t.mdl.on("hidden.bs.modal", $.proxy(t.resetCreateForm, t));
    t.mdlUpdate.on("hidden.bs.modal", $.proxy(t.resetUpdateForm, t));
    $(window).off("resize.outgoingMailTable").on("resize.outgoingMailTable", function() {
        t.scheduleTableLayoutSync();
    });

    t.dTbl.ajax.reload();
};
