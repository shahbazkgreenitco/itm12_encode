var TicketType = function (config) {
    var t = this;
    t.config = config || {};
    t.content = $("#main-ticket-type-wrapper");
    t.table = t.content.find("#ticketTypes");
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");
    t.pageLength = t.content.find(".user-list-page-length");
    t.actionStyleScope = t.content.find(".list-view-panel").first();

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }
    
    t.mdl = $("#ticket-type");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");
    t.mdl.frm = t.mdl.find("#type-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.status = t.mdl.frm.find("#ticket_type_status");
    t.mdl.frmEl.type_Id = t.mdl.frm.find("#type_id");
    t.mdl.frmEl.company_id = t.mdl.frm.find("#company_id");
    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};
    t.editdata = false;

    t.tr = function (key, fallback) {
        if (t.config.translations && t.config.translations[key]) {
            return t.config.translations[key];
        }

        return fallback;
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.icons = {
        edit: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
               </svg>`,
        trash: `<svg viewBox="0 0 15 17" fill="none" >
                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                </svg>`,
        active: `<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`,
        inactive: `<svg width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        var safeLabel = t.escapeHtml(label);
        var safeId = t.escapeHtml(id);
        var safeClasses = t.escapeHtml(classes || "");

        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            safeClasses,
            '" data-id="',
            safeId,
            '" data-bs-toggle="tooltip"',
            ' data-bs-original-title="',
            safeLabel,
            '" aria-label="',
            safeLabel,
            '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    t.renderActionButtons = function (record) {
        var buttons = [];

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        if ($.inArray("TicketTypeEdit", t.config.permissions) !== -1) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("edit_ticket_type", "Edit Ticket Type"),
                    "dtActEdit",
                    record.id,
                    t.icons.edit
                )
            );
        }

        if ($.inArray("TicketTypeDelete", t.config.permissions) !== -1) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("action_delete_ticket_type", "Delete Ticket Type"),
                    "dtActDel is-delete",
                    record.id,
                    t.icons.trash
                )
            );
        }

        if (!buttons.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions role-list-actions justify-content-start">',
            buttons.join(""),
            '</div>'
        ].join("");
    };

   t.renderStatus = function (status) {
        var isActive = parseInt(status, 10) === 1;
        var label = isActive ? t.tr("active", "Active") : t.tr("inactive", "Inactive");

        return [
            '<span class="user-list-status ',
            isActive ? 'is-active' : 'is-inactive',
            '">',
            '<span class="user-list-status-icon">',
            isActive ? t.icons.active : t.icons.inactive,
            '</span>',
            '<span class="user-list-status-label ms-2">',
            t.escapeHtml(label),
            '</span>',
            '</span>'
        ].join("");
    };
    t.getResponseMessage = function (data, fallback) {
        if (data && data.msg) {
            return data.msg;
        }

        return fallback || t.tr("something_went_wrong", "Something went wrong.");
    };

    t.showSuccessAlert = function (message, options) {
        return Swal.fire($.extend({
            icon: "success",
            title: t.tr("success_title", "Success"),
            text: message,
            showConfirmButton: true,
            confirmButtonText: t.tr("ok_button", "OK"),
            allowOutsideClick: false
        }, options || {}));
    };

    t.showErrorAlert = function (message, title, options) {
        return Swal.fire($.extend({
            icon: "error",
            title: title || t.tr("error_title", "Error"),
            text: message || t.tr("something_went_wrong", "Something went wrong."),
            showConfirmButton: true,
            confirmButtonText: t.tr("ok_button", "OK"),
            allowOutsideClick: false
        }, options || {}));
    };

    t.showServerErrorAlert = function (message) {
        t.showErrorAlert(
            message || t.tr("something_went_wrong", "Something went wrong."),
            t.tr("server_error_title", "Server Error")
        );
    };

    t.tblHelpers = {
        actions: function () {
            return function (data, type, row) {
                return t.renderActionButtons(row.a || data);
            };
        },
        status: function () {
            return function (data, type, row) {
                var status = row.a ? row.a.status : data;
                return t.renderStatus(status);
            };
        }
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        bFilter: false,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [3, 4]
            },
            {
                targets: 3,
                render: t.tblHelpers.status()
            },
            {
                targets: 4,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
            }
        ],
        order: [[0, "asc"]],
        drawCallback: function () {
            t.table.find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        ajax: {
            url: config.url.getTicketTypes,
            type: "post",
            data: function (d) {
                d._token = config.token;
                d.search = $.trim(t.searchbox.val() || "");
            }
        },
        columns: [
            { data: "a.name", defaultContent: "" },
            { data: "a.department_name", defaultContent: "-" },
            { data: "a.company_name", defaultContent: "-" },
            { data: "a.status", defaultContent: 0 },
            { data: "a", defaultContent: null }
        ]
    });

    t.reload = function () {
        t.dTbl.ajax.reload(null, false);
    };

    t.getSelectedCompanyId = function () {
        var companyId = t.mdl.frmEl.company_id.val();

        if ($.isArray(companyId)) {
            companyId = companyId.length ? companyId[0] : null;
        }

        return companyId || null;
    };

    t.resetDepartmentSelection = function () {
        t.mdl.frmEl.department_id.empty().val(null).trigger("change");
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
            ticket_type: {
                required: true,
                clean_text_only: true
            },
            status: {
                required: true
            },
            company_id: {
                required: true
            }
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

    t.mdl.frm.find("select").on("change", function () {
        var element = $(this);

        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    t.resetModalForm = function () {
        if (t.mdl.frm.length && t.mdl.frm[0]) {
            t.mdl.frm[0].reset();
        }

        t.frmValidator.resetForm();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frm.find(".error").removeClass("error");
        t.mdl.frmEl.name.val("");
        t.resetDepartmentSelection();
        t.mdl.frmEl.status.val("").trigger("change");
        t.mdl.frmEl.type_Id.val("");
        t.mdl.frmEl.company_id.empty().val(null).trigger("change");
        t.editdata = false;
    };

    t.saveData = function (e) {
        var formData;
        var http;

        if (e) {
            e.preventDefault();
        }

        if (t.frmValidator.form() === false || t.httpCall === false) {
            return false;
        }

        t.httpCall = false;
        formData = t.mdl.frm.serialize();
        http = $.ajax({
            url: t.config.url.add,
            type: "POST",
            data: formData
        });

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.mdl.modal("hide");
                    t.showSuccessAlert(t.getResponseMessage(data)).then(function () {
                        t.reload();
                    });
                } else {
                    t.showErrorAlert(t.getResponseMessage(data));
                }
            }
        });

        http.fail(function (xhr) {
            var response = xhr.responseJSON || {};

            if (xhr.status === 422) {
                t.showErrorAlert(t.getResponseMessage(response));
                return;
            }

            t.showServerErrorAlert(t.getResponseMessage(response));
        });

        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteTicketType = function (e) {
        var typeId = $(this).attr("data-id");

        if (e) {
            e.preventDefault();
        }

        t.httpPostPath = t.config.url.delete + "/" + typeId;
        Swal.fire({
            title: t.tr("confirm_delete_title", "Are you sure you want to delete?"),
            // text: t.tr("are_you_delete", "Are you sure to delete this ticket type ?"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: t.tr("confirm_delete_button", "Yes, delete it")
        }).then(function (result) {
            if (result.isConfirmed) {
                var http = $.get(t.httpPostPath);

                http.done(function (data) {
                    if (typeof data === "object") {
                        if (data.status === "success") {
                            t.showSuccessAlert(t.getResponseMessage(data), {
                                title: t.tr("deleted_title", "Deleted!")
                            }).then(function () {
                                t.reload();
                            });
                        } else {
                            t.showErrorAlert(t.getResponseMessage(data));
                        }
                    }
                });

                http.fail(function (xhr) {
                    var response = xhr.responseJSON || {};
                    t.showServerErrorAlert(
                        t.getResponseMessage(
                            response,
                            t.tr("something_went_wrong", "Something went wrong please try again")
                        )
                    );
                });

                http.always(function () {
                    t.httpCall = true;
                });
            }
        });
    };

    t.editTicketType = function (e) {
        var typeId;
        var http;

        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        t.resetModalForm();
        t.editdata = true;
        typeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + typeId;
        http = $.get(t.config.url.getTicketTypeInfo + "/" + typeId);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    var companyOption;

                    t.mdl.title.html(t.tr("edit_ticket_type", "Edit Ticket Type"));
                    t.mdl.btnSubmit.text(t.tr("edit", "Save Changes"));
                    t.mdl.frmEl.name.val(data.data.name || "");

                    if (data.data.departments && data.data.departments.length) {
                        $.each(data.data.departments, function (_, dept) {
                            var option = new Option(dept.text, dept.id, true, true);
                            t.mdl.frmEl.department_id.append(option);
                        });
                    }

                    t.mdl.frmEl.department_id.trigger("change");
                    t.mdl.frmEl.status.val(String(data.data.status)).trigger("change");

                    if (data.data.company_id) {
                        companyOption = new Option(data.data.company_name, data.data.company_id, true, true);
                        t.mdl.frmEl.company_id.append(companyOption).trigger("change", [true]);
                    } else {
                        t.mdl.frmEl.company_id.val(null).trigger("change", [true]);
                    }

                    t.mdl.frmEl.type_Id.val(typeId);
                    t.mdl.modal("show");
                } else {
                    t.showErrorAlert(t.getResponseMessage(data));
                }
            }
        });

        http.fail(function (xhr) {
            var response = xhr.responseJSON || {};
            t.showServerErrorAlert(
                t.getResponseMessage(
                    response,
                    t.tr("something_went_wrong", "Something went wrong please try again")
                )
            );
        });

        http.always(function () {
            t.httpCall = true;
        });
    };

    t.DownloadExel = function (e) {
        if (e) {
            e.preventDefault();
        }

        window.location = t.config.url.typeExport + "/" + $.trim(t.searchbox.val() || "");
    };

    t.searchData = function (e) {
        var v = $.trim(t.searchbox.val() || "");

        if (e) {
            e.preventDefault();
        }

        if (typeof $.fn.validate_str_param === "function") {
            v = t.searchbox.validate_str_param();
        }

        if (v === false) {
            t.showErrorAlert(
                t.tr("please_enter_valid_search", "Please enter a valid search.")
            );
            return false;
        }

        t.searchbox.val(v);
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

    t.addTicketType = function (e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        t.resetModalForm();
        t.mdl.title.html(t.tr("add_ticket_type", "Add Ticket Type"));
        t.mdl.btnSubmit.text(t.tr("create", "Create"));

        if (
            config.company &&
            config.company.company_id !== null &&
            config.company.company_name !== null
        ) {
            var option = new Option(config.company.company_name, config.company.company_id, true, true);
            t.mdl.frmEl.company_id.append(option).trigger("change", [true]);
        } else {
            t.mdl.frmEl.company_id.val(null).trigger("change", [true]);
        }

        t.mdl.modal("show");
    };

    var select2Opts = { width: "100%" };

    t.mdl.frmEl.department_id.select2($.extend({}, select2Opts, {
        // dropdownParent: t.mdl.frmEl.department_id.parent(),
        dropdownParent:$("#ticket-type"),
        ajax: {
            url: t.config.url.departments,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.getSelectedCompanyId(),
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.tr("select_department", "Select Department")
    }));

    t.mdl.frmEl.status.select2($.extend({}, select2Opts, {
        placeholder: t.tr("select_status", "Select Status"),
        dropdownParent:$("#ticket-type")

    }));

    t.mdl.frmEl.company_id.select2({
        placeholder: t.tr("select_company", "Select Company"),
        allowClear: true,
        width: "100%",
        dropdownParent:$("#ticket-type"),
        // dropdownParent: t.mdl.frmEl.company_id.parent(),
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
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    t.mdl.frmEl.company_id.on("change", function (e, isInit) {
        if (t.editdata === true && isInit === true) {
            return;
        }

        t.resetDepartmentSelection();
    });

    t.table.on("click", ".dtActEdit", t.editTicketType);
    t.table.on("click", ".dtActDel", t.deleteTicketType);
    t.content.on("click", "#btnSubmit", t.saveData);
    t.content.on("click", ".btn-download", t.DownloadExel);
    t.content.on("click", ".btn-searchbox, .amg-list-searchbar__icon", t.searchData);
    t.content.on("keyup", ".searchbox", function (e) {
        if (e.which === 13) {
            t.searchData(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.searchData(e);
        }
    });
    t.content.on("click", ".add-ticket-type, .js-act-add-ticket-type", t.addTicketType);
    t.content.on("click", ".btn-reload-list", t.reload);
    t.content.on("change", ".user-list-page-length", t.changePageLength);
};
