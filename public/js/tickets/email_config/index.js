var EmailConfig = function (config) {
    
    let defaultCompany = localStorage.getItem('Default_Company');
    let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;
    var t = this;
    t.config = config;
    t.content = $('.main-content');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#emailtoTicketConfigMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#emailToTicketConfigMdlForm');
    t.frmEl = {};
    t.mdl.emailcover = t.frm.find(".emailcover");
    t.mdl.passcover = t.frm.find(".passcover");
    t.frmEl.auto_create_from_email = t.frm.find('#auto_create_from_email');
    t.frmEl.validate_cert = t.frm.find('#validate_cert');
    t.frmEl.ebts_username = t.frm.find('#ebts_username');
    t.frmEl.ebts_password = t.frm.find('#ebts_password');
    t.frmEl.ebts_host = t.frm.find('#ebts_host');
    t.frmEl.ebts_port = t.frm.find('#ebts_port');
    t.frmEl.ebts_encryption = t.frm.find('#ebts_encryption');
    t.frmEl.ticketing_blocked_accounts = t.frm.find('#ticketing_blocked_accounts');
    t.frmEl.outGoingMailServerId = t.frm.find('#outgoing_mail_id');
    t.mdl.domainallowcover = t.frm.find(".domainallowcover");
    t.mdl.domainallcover = t.frm.find(".domainallcover");
    t.mdl.subCategoryIdCvr = t.frm.find("#sub_category_id_cvr");
    t.mdl.notify = t.frm.find(".notifyCover");
    t.mdl.ccnotify = t.frm.find(".ccnotifyCover");
    t.frmEl.default_department_id = t.frm.find("#default_department_id");
    t.frmEl.company_id = t.frm.find("#email_company_id");

    t.frmEl.isReqDeptChgBfrResolve = t.frm.find("#isReqDeptChgBfrResolve");
    t.frmEl.except_notify_status = t.frm.find("#except_notify_status");
    t.frmEl.except_notify_tos = t.frm.find("#except_notify_tos");
    t.frmEl.except_notify_ccs = t.frm.find("#except_notify_ccs");
    t.frmEl.default_prob_cat_id = t.frm.find("#default_prob_cat_id_email");
    t.frmEl.default_sub_cat_id = t.frm.find("#default_sub_cat_id_email");
    t.frmEl.ticketing_restricted = t.frm.find('#ticketing_restricted');
    t.frmEl.ticketing_allowed_domains = t.frm.find('#ticketing_allowed_domains');
    t.frmEl.ticketing_blocked_domains = t.frm.find('#ticketing_blocked_domains');
    t.frmEl.restricted_words = t.frm.find('#restricted_words');
    t.frmEl.isReqDeptChgBfrAssigned = t.frm.find("#isReqDeptChgBfrAssigned");
    t.frmEl.isReqDeptChgBfrTransfer = t.frm.find("#isReqDeptChgBfrTransfer");
    t.mdl.lblemail = t.frm.find("label[for=email]");
    t.mdl.lblemailpass = t.frm.find("label[for=ebts_password]");
    t.mdl.lblemailhos = t.frm.find("label[for=email_hos]");
    t.mdl.lblemailpor = t.frm.find("label[for=email_por]");
    t.load_spin = t.content.find('#ticket_type_mdl_loader');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');
    t.pageLength = t.content.find(".user-list-page-length");

    t.mdl.view = {};
    t.mdl.view = t.content.find('#emailConfigviewMdl');
    t.mdltitle_view = t.mdl.view.find('.modal-title');
    t.loader = t.mdl.view.find('#loader_img');
    t.frm_view = t.mdl.view.find('#emailConfigMdlviewForm');

    t.frmEl.view = {};
    t.mdl.view.emailcover = t.frm_view.find(".emailcover");
    t.frmEl.view.auto_create_from_email = t.frm_view.find('#auto_create_from_email');
    t.frmEl.view.validate_cert = t.frm_view.find('#validate_cert');
    t.frmEl.view.ebts_username = t.frm_view.find('#ebts_username');
    t.frmEl.view.ebts_password = t.frm_view.find('#ebts_password');
    t.frmEl.view.ebts_host = t.frm_view.find('#ebts_host');
    t.frmEl.view.ebts_port = t.frm_view.find('#ebts_port');
    t.frmEl.view.ebts_encryption = t.frm_view.find('#ebts_encryption');
    t.mdl.view.domainallowcover = t.frm_view.find(".domainallowcover");
    t.mdl.view.domainallcover = t.frm_view.find(".domainallcover");
    t.frmEl.view.ticketing_restricted = t.frm_view.find('#ticketing_restricted');
    t.frmEl.view.ticketing_allowed_domains = t.frm_view.find('#ticketing_allowed_domains');
    t.frmEl.view.ticketing_blocked_domains = t.frm_view.find('#ticketing_blocked_domains');
    t.frmEl.view.restricted_words = t.frm_view.find('#restricted_words');
    t.frmEl.view.blocked_email_accounts = t.frm_view.find('#blocked_email_accounts');
    t.mdl.view.lblemail = t.frm_view.find("label[for=email]");
    t.mdl.view.lblemailpass = t.frm_view.find("label[for=email_pass]");
    t.mdl.view.lblemailhos = t.frm_view.find("label[for=email_hos]");
    t.mdl.view.lblemailpor = t.frm_view.find("label[for=email_por]");
    t.resetFrm = {};
    t.btn.view = {};
    t.btn.view.submit = t.frm.find('#btnSubmit');
    t.btn.view.clear = t.frm.find('#btnClear');
    t.actionStyleScope = t.content.find(".list-view-panel").first();

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
        var noteEditor = element.next(".note-editor");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }

        if (noteEditor.length) {
            noteEditor.toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.getFormSelectDropdownParent = function (element) {
        var parent;

        if (!element || !element.length) {
            return t.mdl;
        }

        parent = element.closest(".input-group");
        return parent.length ? parent : t.mdl;
    };

    t.data = {
        problem_categories: [],
        sub_categories: []
    };

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }
    var select2Opts = { width: "100%" };
    t.frmEl.default_prob_cat_id.select2({select2Opts});
    t.frmEl.company_id.select2({select2Opts});

    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");
    // t.mdl.subCategoryIdCvr.hide();
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
            '" title="',
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

        if (record) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("action_edit_ticket_type", "Edit Ticket Type"),
                    "open-edit-modal",
                    record.id,
                    t.icons.edit
                )
            );
        }

        if (record) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("action_delete_ticket_type", "Delete Ticket Type"),
                    "open-delete is-delete",
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

    t.tblHelpers = {
        actions: function () {
            return function (data, type, row) {
                return t.renderActionButtons(row.a || data);
            };
        },
    };

    /* make table as dataTable */
    t.dTl = t.table.DataTable({
        autoWidth: false,
        bFilter: false,
        aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [4]
            },
            {
                targets: 4,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
            },
            {
                targets: 1,
                bSortable: false,
                render: function (data, type, row) {
                    return row.a.auto_create_from_text == "Enabled"
                        ? `<span class="badge rounded-pill b5-text py-1" style="background:#E5FFE7;color:#006D1D">Enabled</span>`
                        : `<span class="badge rounded-pill b5-text py-1"  style="background:#F2E5FF;color:#5C00E5">Disabled</span>`;
                },
            },

        ],
        order: [
            [0, 'asc']
        ],
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
        deferLoading: true,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        ajax: {
            url: t.config.url.email_config,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.company_id = companyId;
                d.search = t.searchbox.val();
            }
        },
        
        columns: [
            { data: 'a.ebts_username' },
            { data: 'a.auto_create_from_text' },
            { data: 'a.ebts_host' },
            { data: 'a.ebts_port' },
            { data: 'a' },
            //{ data: 'a.ebts_encryption_text' }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).find('td').addClass('b5-text');
        }, 
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            var searchBox = '<div class="input-group table-search-btns"><input type="text" class="form-control input-sm" placeholder="'+config.translations.serach_option+'" style="width: 210px;" aria-controls="mytable" /></div>';
            $("#mytable_filter").empty().html(searchBox);
            $("#mytable_filter input").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });


    t.reload = function () {
        t.dTl.ajax.reload();
    };

     // refill the problem category
    t.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.problem_categories = [];
        t.frmEl.default_prob_cat_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.frmEl.default_department_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        t.frmEl.default_prob_cat_id.append(new Option(v.name, v.id));
                    });
                }
            }).always(function () {
                t.frmEl.default_prob_cat_id.trigger("change");
            });
        }
        else {
            t.frmEl.default_prob_cat_id.trigger("change");
        }
    };

    // refill the sub category
    t.refillSubCategory = function (e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.sub_categories = [];
        t.frmEl.default_sub_cat_id.empty().append(new Option(config.translations.Select_Sub_Category, ""));
        var type_val = parseInt($.trim(t.frmEl.default_prob_cat_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.problem_categories, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.sub_categories = v.sub;
                            $.each(v.sub, function (j, k) {
                                t.frmEl.default_sub_cat_id.append(new Option(k.name, k.id));
                            });
                            return false;
                        }
                    }
                });
                
            }
            catch (e) {
                console.log(e);
            }
        }
        
        //t.frmEl.default_sub_cat_id.val(24);
        if (typeof val != "undefined") {
            t.frmEl.default_sub_cat_id.val(val);
        }
            
        t.updateSubCategoryVisibility();
        t.frmEl.default_sub_cat_id.trigger("change");
    };

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibility = function () {
        if (t.data.sub_categories.length > 0) {
            t.frmEl.default_sub_cat_id.rules("add", {
                required: true,
                str_name: true
            });
            t.mdl.subCategoryIdCvr.show();
        }
        else {
            t.frmEl.default_sub_cat_id.rules("remove");
            t.mdl.subCategoryIdCvr.hide();
        }
    };

    t.mdl.domainRestriction = function() {
        var acfe = t.frmEl.auto_create_from_email.val();
        if( acfe == 1 && t.frmEl.ticketing_restricted.val() ==  1) {
            t.mdl.domainallowcover.removeClass("hide");
            t.mdl.domainallcover.addClass("hide");
            t.frmEl.ticketing_allowed_domains.rules("add", {required:true});
        }
        else if( acfe == 1 && t.frmEl.ticketing_restricted.val() == 2) {
            t.mdl.domainallowcover.addClass("hide");
            t.mdl.domainallcover.removeClass("hide");
        }
        else {
            t.mdl.domainallowcover.addClass("hide");
            t.mdl.domainallcover.addClass("hide");
        }
    };

    t.loadAccViewForm = function (obj) {
        t.resetFrm();
        t.frmEl.toggleListen();
        t.mdl.passcover.addClass("hide");
        t.frmEl.ebts_host.val(obj.val.ebts_host).prop('disabled', true);
        t.frmEl.ebts_port.val(obj.val.ebts_port).prop('disabled', true);
        t.frmEl.ebts_username.val(obj.val.ebts_username).prop('disabled', true);
        t.frmEl.except_notify_ccs.val(obj.val.except_notify_ccs).prop('disabled', true);
        t.frmEl.except_notify_tos.val(obj.val.except_notify_tos).prop('disabled', true);
        t.frmEl.ticketing_allowed_domains.val(obj.val.ticketing_allowed_domains).prop('disabled', true);
        t.frmEl.ticketing_blocked_accounts.val(obj.val.ticketing_blocked_accounts).prop('disabled', true);
        t.frmEl.restricted_words.val(obj.val.restricted_words).prop('disabled', true);
        t.frmEl.outGoingMailServerId.val(obj.val.outgoing_mail_id).prop('disabled', true);
        if (obj.department != null) {
            t.frmEl.default_department_id.append(new Option(obj.department.text, obj.department.id, true, true)).trigger("change").prop('disabled', true);
        }

        /* set problem category */
        t.frmEl.default_prob_cat_id.empty();
        $.each(obj.problem_category, function(i, d) {
            var op = (d.id == obj.val.default_prob_cat_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
            t.frmEl.default_prob_cat_id.append(op).prop('disabled', true);
        });
        t.frmEl.default_prob_cat_id.trigger("change");
        t.data.problem_categories = obj.problem_category;
        t.frmEl.outGoingMailServerId.select2('trigger', 'select', {
            data: {id: obj.val.outgoing_mail_id, selected: true}
        });

        /* set sub category */
        t.refillSubCategory(undefined, obj.val.default_sub_cat_id);
        t.frmEl.default_sub_cat_id.prop('disabled', true);
        
        if(obj.val.auto_create_from_email >= 0) {
            t.frmEl.auto_create_from_email.val(obj.val.auto_create_from_email).trigger("change").prop('disabled', true);
        }
        if(obj.val.validate_cert >= 0) {
            t.frmEl.validate_cert.val(obj.val.validate_cert).trigger("change").prop('disabled', true);
        }
        if(obj.val.ticketing_restricted >= 0) {
            t.frmEl.ticketing_restricted.val(obj.val.ticketing_restricted).trigger("change").prop('disabled', true);
        }
        if(obj.val.except_notify_status >= 0) {
            t.frmEl.except_notify_status.val(obj.val.except_notify_status).trigger("change").prop('disabled', true);
        }
        if(obj.val.isReqDeptChgBfrResolve >= 0) {
            t.frmEl.isReqDeptChgBfrResolve.val(obj.val.isReqDeptChgBfrResolve).trigger("change").prop('disabled', true);
        }
        if(obj.val.isReqDeptChgBfrTransfer >= 0) {
            t.frmEl.isReqDeptChgBfrTransfer.val(obj.val.isReqDeptChgBfrTransfer).trigger("change").prop('disabled', true);
        }
        if(obj.val.isReqDeptChgBfrAssigned >= 0) {
            t.frmEl.isReqDeptChgBfrAssigned.val(obj.val.isReqDeptChgBfrAssigned).trigger("change").prop('disabled', true);
        }
        if(obj.val.ebts_encryption == "ssl") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', true);
        }else if(obj.val.ebts_encryption == "tls") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', true);
        }else if(obj.val.ebts_encryption == "false") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', true);
        }
        t.frmEl.toggleListen(true);
        
    };

    t.frmEl.toggleListen = function(s) {
        t.frmEl.default_department_id.off("change", $.proxy(t.refillProblemCategory));

        if (typeof s != "undefined" && s == true) {
            t.frmEl.default_department_id.on("change", $.proxy(t.refillProblemCategory));
        }
    };

    t.loadAccForm = function (obj) {
        t.resetFrm();
        t.frmEl.toggleListen();
        t.frmEl.ebts_host.val(obj.val.ebts_host).prop('disabled', false);
        t.frmEl.ebts_port.val(obj.val.ebts_port).prop('disabled', false);
        t.frmEl.ebts_username.val(obj.val.ebts_username).prop('disabled', false);
        t.frmEl.except_notify_ccs.val(obj.val.except_notify_ccs).prop('disabled', false);
        t.frmEl.except_notify_tos.val(obj.val.except_notify_tos).prop('disabled', false);
        t.frmEl.ticketing_allowed_domains.val(obj.val.ticketing_allowed_domains).prop('disabled', false);
        t.frmEl.ticketing_blocked_accounts.val(obj.val.ticketing_blocked_accounts).prop('disabled', false);
        t.frmEl.restricted_words.val(obj.val.restricted_words).prop('disabled', false);
        t.frmEl.outGoingMailServerId.val(obj.val.outgoing_mail_id).prop('disabled', false);

        t.frmEl.default_department_id.empty().append(new Option(obj.department.text, obj.department.id, true, true)).trigger("change").prop('disabled', false);
        
        /* set problem category */
        t.frmEl.default_prob_cat_id.empty();
        $.each(obj.problem_category, function(i, d) {
            var op = (d.id == obj.val.default_prob_cat_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
            t.frmEl.default_prob_cat_id.append(op).prop('disabled', false);
        });
        t.frmEl.default_prob_cat_id.trigger("change");
        t.data.problem_categories = obj.problem_category;
        t.frmEl.outGoingMailServerId.select2('trigger', 'select', {
            data: {id: obj.val.outgoing_mail_id, selected: true}
        });

        /* set sub category */
        t.refillSubCategory(undefined, obj.val.default_sub_cat_id);
        t.frmEl.default_sub_cat_id.prop('disabled', false);
        
        if(obj.val.auto_create_from_email >= 0) {
            t.frmEl.auto_create_from_email.val(obj.val.auto_create_from_email).trigger("change").prop('disabled', false);
        }
        if(obj.val.validate_cert >= 0) {
            t.frmEl.validate_cert.val(obj.val.validate_cert).trigger("change").prop('disabled', false);
        }
        if(obj.val.ticketing_restricted >= 0) {
            t.frmEl.ticketing_restricted.val(obj.val.ticketing_restricted).trigger("change").prop('disabled', false);
        }
        if(obj.val.ebts_encryption == "ssl") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', false);
        }else if(obj.val.ebts_encryption == "tls") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', false);
        }else if(obj.val.ebts_encryption == "false") {
            t.frmEl.ebts_encryption.val(obj.val.ebts_encryption).trigger("change").prop('disabled', false);
        }
        if(obj.val.except_notify_status >= 0) {
            t.frmEl.except_notify_status.val(obj.val.except_notify_status).trigger("change").prop('disabled', false);
        }
        if(obj.val.isReqDeptChgBfrResolve >= 0) {
            t.frmEl.isReqDeptChgBfrResolve.val(obj.val.isReqDeptChgBfrResolve).trigger("change").prop('disabled', false);
        }
        if(obj.val.isReqDeptChgBfrTransfer >= 0) {
            t.frmEl.isReqDeptChgBfrTransfer.val(obj.val.isReqDeptChgBfrTransfer).trigger("change").prop('disabled', false);
        }
        if(obj.val.isReqDeptChgBfrAssigned >= 0) {
            t.frmEl.isReqDeptChgBfrAssigned.val(obj.val.isReqDeptChgBfrAssigned).trigger("change").prop('disabled', false);
        }
        if (obj.val.tkt_ac_account_ids != null) {
            t.frmEl.tkt_ac_account_ids.val(obj.val.tkt_ac_account_ids.split(",")).trigger("change").prop('disabled', false);
        }
        t.frmEl.toggleListen(true);
        
    };
 
    t.mdl.new_notify = function(){
        
        var notify = t.frmEl.except_notify_status.val();
       
        if(notify ==  0){
            t.mdl.notify.addClass("d-none");
            t.mdl.ccnotify.addClass("d-none");
        }else if(notify ==  1){
            t.mdl.notify.removeClass("d-none");
            t.frmEl.except_notify_tos.rules("add", {required:true});
            t.mdl.ccnotify.removeClass("d-none");
            t.frmEl.except_notify_ccs.rules("add", {required:true});
        }

    }
    
    t.resetFrm = function () {
        t.frm.trigger("reset");
        t.frmEl.auto_create_from_email.val(1).trigger("change").prop('disabled', false);
        t.frmEl.ebts_encryption.val("ssl").trigger("change").prop('disabled', false);
        t.frmEl.default_department_id.val("").empty().trigger("change").prop('disabled', false);
        t.frmEl.default_prob_cat_id.val("").empty().trigger("change").prop('disabled', false);
        t.frmEl.ebts_username.val('').prop('disabled', false);
        t.frmEl.except_notify_status.val('').prop('disabled', false);
        t.frmEl.except_notify_tos.val('').prop('disabled', false);
        t.frmEl.ebts_password.val('').prop('disabled', false);
        t.frmEl.ebts_host.val('').prop('disabled', false);
        t.frmEl.ebts_port.val('').prop('disabled', false);
        t.frmEl.ticketing_allowed_domains.val('').prop('disabled', false);
        t.frmEl.ticketing_blocked_domains.val('').prop('disabled', false);
        t.frmEl.ticketing_blocked_accounts.val('').prop('disabled', false);
        t.frmEl.restricted_words.val('').prop('disabled', false);
        t.frmEl.validate_cert.val(1).trigger("change").prop('disabled', false);
        t.frmEl.isReqDeptChgBfrResolve.val(1).trigger("change").prop('disabled', false);
        t.frmEl.isReqDeptChgBfrAssigned.val(1).trigger("change").prop('disabled', false);
        t.frmEl.isReqDeptChgBfrTransfer.val(1).trigger("change").prop('disabled', false);
        t.frmEl.except_notify_ccs.val('').prop('disabled', false);
        t.frmEl.ticketing_restricted.val(1).trigger("change").prop('disabled', false);
        // t.frmValidator.resetForm();
        //t.mdl.emailRestriction();
        t.frm.find('.amg-form-invalid').removeClass('amg-form-invalid');
        t.frm.find('.amg-form-select-error').removeClass('amg-form-select-error');
        t.frm.find('.amg-form-error-wrap').empty();
    };

    t.addModal = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.add_acc;
        t.mdl.lblemailpass.addClass("mandatory");
        t.load_spin.addClass('hide');
        t.frmEl.ebts_password.rules("add", { required: true, acceptable_spcl_chr: true });
        t.mdltitle.text(t.config.translations.Add_Email_Service_Status);
        t.frmEl.toggleListen(true);
        t.btn.submit.removeClass("hide");
        t.mdl.modal("show");
        t.resetFrm();
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorElement: 'label',
        errorClass: 'error',

        
        rules: {
            auto_create_from_email: {
                required: true
            },
            ebts_host: {
                required: true,
                acceptable_spcl_chr:true
            },
            ebts_port: {
                required: true,
                acceptable_spcl_chr:true
            },
            ebts_username: {
                required: true,
                email:true
            },
            default_prob_cat_id: {
                required: true
            },
            default_department_id: {
                required: true
            },
            except_notify_tos:{
                acceptable_spcl_chr:true
            },
            except_notify_ccs:{
                acceptable_spcl_chr:true
            },
            ebts_password:{
                required: true,
                acceptable_spcl_chr:true
            },
            ticketing_blocked_domains:{
                clean_text_only:true,
            },
            ticketing_allowed_domains:{
                clean_text_only:true,
            },
            ticketing_blocked_accounts:{
                clean_text_only:true,
            },
            restricted_words:{
                clean_text_only: true,
            },
        },
        errorPlacement: function (error, element) {
            var errorWrap = getErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else {
                error.insertAfter(element.closest(".input-group"));
            }
            updateValidationState(element, true);
        },
        highlight: function (element) {
            updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            updateValidationState($(element), false);
        },
    });

    t.handlesubmit = function (e) {
        e.preventDefault();
        if( t.frmValidator.form() == false ) {
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append('company_id',companyId);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    //t.frmEl.ebts_password.val('');
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg' : t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            // t.httpCall = true;
            $('#img').hide();
        });
    };

    t.editAcc = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit_acc + "/" + id;
        var http = $.get(t.config.url.get_acc + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    //t.mdl.lblemailpass.removeClass("mandatory");
                    //t.frmEl.ebts_password.rules("add", { required: false, acceptable_spcl_chr: true });
                    t.mdl.modal("show");
                    t.btn.submit.removeClass("hide");
                    t.mdltitle.text("Edit");
                    t.btn.submit.text("Save Changes");
                    t.loadAccForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg' : t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.viewAccModal = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit_acc + "/" + id;
        var http = $.get(t.config.url.get_acc + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdltitle.text("View");
                    t.btn.submit.addClass("hide");
                    t.mdl.passcover.addClass("hide");
                    t.mdl.modal("show");
                    t.loadAccViewForm(data.data);
                }
            }
        });
    };

    t.deleteAcc = function (e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete_acc + "/" + Id;
        sweetAlertConfirm({
            message: 'Are you sure to delete this Auto Created Account?',
            url: t.httpPostPath,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            onSuccess: function (data) {
                if (data.status === "success") {
                    sweetAlert('center', 'success', data);
                    t.dTl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            complete: function () {
                t.httpCall = true;
            }
        });
    };

    t.goBack = function() {
        window.location = t.config.url.goBack;
    }
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
        t.dTl.ajax.reload();
    };
  
    t.frmEl.auto_create_from_email.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.auto_create_from_email.parent()})).trigger("change");
    t.frmEl.validate_cert.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.validate_cert.parent()}));
    t.frmEl.ebts_encryption.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.ebts_encryption.parent()}));
    t.frmEl.default_prob_cat_id.select2(
        $.extend({}, select2Opts, {
            dropdownParent: $('#emailtoTicketConfigMdl')
        })
    ).on("change", $.proxy(t.refillSubCategory, t));
    // t.frmEl.default_prob_cat_id.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.default_prob_cat_id.parent()})).on("change", $.proxy(t.refillSubCategory));
    t.frmEl.default_sub_cat_id.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.default_sub_cat_id.parent()}));
    t.frmEl.isReqDeptChgBfrResolve.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.isReqDeptChgBfrResolve.parent()}));
    t.frmEl.isReqDeptChgBfrAssigned.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.isReqDeptChgBfrAssigned.parent()}));
    t.frmEl.isReqDeptChgBfrTransfer.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.isReqDeptChgBfrTransfer.parent()}));
    t.frmEl.outGoingMailServerId.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.outGoingMailServerId.parent()}));
    t.frmEl.except_notify_status.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.except_notify_status.parent()})).on("change", $.proxy(t.mdl.new_notify)).trigger("change");
    t.frmEl.ticketing_restricted.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.ticketing_restricted.parent()})).on("change", $.proxy(t.mdl.domainRestriction)).trigger("change");

    t.frmEl.default_department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.default_department_id.parent(),
        placeholder: config.translations.select_department,
        allowClear: true,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '',
                    company_id: t.frmEl.company_id.val()
                };
            },
            processResults: function (response) {
                return {
                    results: Array.isArray(response.results) ? response.results : [],
                    pagination: {
                        more: !!response.pagination?.more
                    }
                };
            }
        }
    })).on("change", $.proxy(t.refillProblemCategory));

    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
        placeholder: "Select Company", 
        allowClear: true,
        ajax: {
            url: t.config.url.company_defulte, 
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || ''  
                };
            },
            processResults: function (response) {
                return {
                    results: Array.isArray(response.results) ? response.results : [],
                    pagination: {
                        more: !!response.pagination?.more
                    }
                };
            }
        }
    })).on("change", function () {
        t.frmEl.default_department_id.val(null).trigger("change");
    });

    // $.get(t.config.url.company_defulte, { id: companyId }, function (response) {
    //     let company = response.results[0];
    //     if (company) {
    //         let option = new Option(company.text, company.id, true, true);
    //         t.frmEl.company_id.append(option).trigger("change");
    //     }
    // });



    t.content.on("keyup", ".searchbox", function (e) {
        if (e.which === 13) {
            t.searchData(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.searchData(e);
        }
    });
    t.changePageLength = function (e) {
        var length = parseInt(t.pageLength.val(), 10);

        if (e) {
            e.preventDefault();
        }

        if (length > 0) {
            t.dTl.page.len(length).draw(false);
        }
    };
    t.content.on('click', '.open-view', $.proxy(t.viewAccModal));
    t.content.on('click', '.add-email-to-ticket-account', $.proxy(t.addModal));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editAcc));
    t.content.on('click', '.open-delete', $.proxy(t.deleteAcc));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.go-back-config', $.proxy(t.goBack));
    t.content.on("change", ".user-list-page-length", t.changePageLength);

    t.dTl.ajax.reload();
};

var EmailAccountList = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.acc_mdl = {};
    t.acc_mdl = t.content.find('#accemailConfigMdl');
    t.mdltitle = t.acc_mdl.find('.modal-title');
    t.loader = t.acc_mdl.find('#loader_img');
    t.frm = t.acc_mdl.find('#accemailConfigMdlForm');

    t.frmEl = {};
    t.frmEl.email = t.frm.find('#email');
    t.frmEl.start_date = t.frm.find('#start_date');
    t.frmEl.end_date = t.frm.find('#end_date');
    t.frmEl.tkt_ac_account_ids = t.frm.find('#tkt_ac_account_ids');
    t.frmEl.status = t.frm.find('#status');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');
    t.acc_mdl.view = {};
    t.acc_mdl.view = t.content.find('#accemailConfigviewMdl');
    t.mdltitle_view = t.acc_mdl.view.find('.modal-title');
    t.loader = t.acc_mdl.view.find('#loader_img');
    t.frm_view = t.acc_mdl.view.find('#accemailConfigMdlviewForm');

    t.frmEl.view = {};
    t.frmEl.view.email = t.frm_view.find('#email');
    t.frmEl.view.start_date = t.frm_view.find('#start_date');
    t.frmEl.view.end_date = t.frm_view.find('#end_date');
    t.frmEl.view.tkt_ac_account_ids = t.frm_view.find('#tkt_ac_account_ids');
    t.frmEl.view.status = t.frm_view.find('#status');
    t.resetFrm = {};
    t.btn.view = {};
    t.btn.view.submit = t.frm_view.find('#btnSubmit');
    t.btn.view.clear = t.frm_view.find('#btnClear');
    

    t.searchbtn = t.content.find(".btn-searchbox");


    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        }, {
            targets: 0,
            render: function (d) {
                var x = d;
                var a = [];
                if(d.status == 1){
                    a.push("<button class='btn dtActbtn  open-del-sch-modal' data-toggle='tooltip' data-original-title='Delete Scheduled Block' data-id=\"" + d.id + "\"><i class=\"fa fa-trash\"></i></button>");
                    a.push("<button class='btn dtActbtn open-edit-sch-modal' data-toggle='tooltip' data-original-title='Edit Scheduled Block' data-id=\"" + d.id + "\"><i class=\"fa fa-pencil\"></i></button>");
                }

                if(d.status == 2){
                    a.push("<button class='btn dtActbtn dtActDel open-force-complete' data-placement='right' data-toggle='tooltip' data-original-title='Force To Complete' data-id=\"" + d.id + "\" ><i class=\"fa fa-warning\"></i></button>");
                }
                    
                a.push("<button class='btn dtActbtn open-acc-view' data-toggle='tooltip' data-original-title='View Scheduled Block' data-id=\"" + d.id + "\"><i class=\"fa fa-eye\"></i></button>");
                    
                return '<div class="popup-toolbox checkselect" style="display: inline-block;">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            }
        }
        ],
        order: [
            [1, 'asc']
        ],
        //deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.email_config_list,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
            }
        },
        columns: [
            { data: 'a' },
            { data: 'a.email' },
            { data: 'a.start_date_format' },
            { data: 'a.end_date_format' },
            { data: 'a.tkt_ac_account_name' },
            { data: 'a.status_text' }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            var searchBox = '<div class="input-group table-search-btns"><input type="text" class="form-control input-sm" placeholder="'+config.translations.serach_option+'" style="width: 210px;" aria-controls="mytable" /></div>';
            $("#mytable_filter").empty().html(searchBox);
            $("#mytable_filter input").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });


    t.reload = function () {
        t.dTbl.ajax.reload();
    };


    t.addAccModal = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.add_block;
        t.mdltitle.text("Add Scheduled Blocker");
        t.btn.submit.removeClass("hide");
        t.acc_mdl.modal("show");
        t.resetFrm();
    };

    t.loadForm = function (obj) {
        t.resetFrm();
        t.frmEl.email.val(obj.email);
        t.frmEl.start_date.val(obj.start_date);
        t.frmEl.end_date.val(obj.end_date);
        if(obj.status >= 0) {
            t.frmEl.status.val(obj.status).trigger("change");
        }
        if (obj.tkt_ac_account_ids != null) {
            t.frmEl.tkt_ac_account_ids.val(obj.tkt_ac_account_ids.split(",")).trigger("change");
        }
        
    };
    t.frmEl.email.prop('disabled', false);
    t.frmEl.start_date.prop('disabled', false);
    t.frmEl.end_date.prop('disabled', false);
    t.frmEl.status.prop('disabled', false);
    t.frmEl.tkt_ac_account_ids.prop('disabled', false);

    t.loadViewForm = function (obj) {
        t.resetFrm();
        t.frmEl.email.val(obj.email).prop('disabled', true);
        t.frmEl.start_date.val(obj.start_date).prop('disabled', true);
        t.frmEl.end_date.val(obj.end_date).prop('disabled', true);
        if(obj.status >= 0) {
            t.frmEl.status.val(obj.status).trigger("change").prop('disabled', true);
        }
        if (obj.tkt_ac_account_ids != null) {
            t.frmEl.tkt_ac_account_ids.val(obj.tkt_ac_account_ids.split(",")).trigger("change").prop('disabled', true);
        }
        
    };

    
    t.handlesubmit = function (e) {
        e.preventDefault();
        if( t.acc_frmValidator.form() == false ) {
            return false;
        }
        var frmData = new FormData;
        frmData.append('_token', t.config.token);
        frmData.append('email', t.frmEl.email.val());
        frmData.append('start_date', t.frmEl.start_date.val());
        frmData.append('end_date', t.frmEl.end_date.val());
        frmData.append('tkt_ac_account_ids', t.frmEl.tkt_ac_account_ids.val().join(","));
        frmData.append('status', t.frmEl.status.val());
        frmData.append('company_id',companyId);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.acc_mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            vex.dialog.alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            // t.httpCall = true;
        });
    };

    t.acc_frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            email: {
                required: true,
                email:true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true
            },
            tkt_ac_account_ids: {
                required: true
            },
            status: {
                required: true
            }
        }
    });

    t.resetFrm = function () {
        t.frmEl.email.val('');
        t.frmEl.email.prop('disabled', false);
        t.frmEl.start_date.val('');
        t.frmEl.start_date.prop('disabled', false);
        t.frmEl.end_date.val('');
        t.frmEl.end_date.prop('disabled', false);
        t.frmEl.tkt_ac_account_ids.val('');
        t.frmEl.tkt_ac_account_ids.prop('disabled', false);
        t.frmEl.status.val('');
        t.frmEl.status.prop('disabled', false);
        t.acc_mdl.tkt_ac_account_ids();
        t.acc_mdl.status();
        t.acc_frmValidator.resetForm();
    };

    t.viewModal = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + id;
        var http = $.get(t.config.url.get + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdltitle.text("View Scheduled Blocker");
                    t.btn.submit.addClass("hide");
                    t.acc_mdl.modal("show");
                    t.loadViewForm(data.data);
                }
            }
        });
    };

    t.editSchModal = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + id;
        var http = $.get(t.config.url.get + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.acc_mdl.modal("show");
                    t.mdltitle.text("Edit");
                    t.btn.submit.removeClass("hide");
                    t.btn.submit.text("Save Changes");
                    t.loadForm(data.data);
                }
                else {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteSchModal = function (e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + Id;
        vex.dialog.confirm({
            message: 'Are you sure to delete this Scheduled Block?',
            callback: function (value) {
                if (value == true) {
                    var http = $.get(t.httpPostPath);
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
                                t.dTbl.ajax.reload();
                            }
                            else {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function () {
                        t.httpCall = true;
                    });
                }
            }
        });
    };
    t.openForceComplete = function (e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.force_complete + "/" + Id;
        vex.dialog.confirm({
            message: 'Are you Force to Complete?',
            callback: function (value) {
                if (value == true) {
                    var http = $.get(t.httpPostPath);
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
                                t.dTbl.ajax.reload();
                            }
                            else {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function () {
                        t.httpCall = true;
                    });
                }
            }
        });
    };

    t.goBack = function() {
        window.location = t.config.url.goBack;
    }

    var select2Opts = { width: "100%" };
    t.frmEl.view.tkt_ac_account_ids.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.view.tkt_ac_account_ids.parent()}));
    t.frmEl.view.status.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.view.status.parent()}));
    t.frmEl.tkt_ac_account_ids.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.tkt_ac_account_ids.parent()}));
    t.frmEl.status.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.status.parent()}));
    t.frmEl.view.status.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.view.status.parent()}));

    t.acc_mdl.tkt_ac_account_ids = function () {
        t.frmEl.tkt_ac_account_ids.empty();
        $.each(t.config.auto_creation_acc , function (i, v) {
            t.frmEl.tkt_ac_account_ids.append(new Option(v.text, v.id));
        });
        t.frmEl.tkt_ac_account_ids.trigger("change");
    };

    t.acc_mdl.status = function () {
        t.frmEl.status.empty().append(new Option(config.translations.Select_Status, "", true, true));
        $.each(t.config.acc_status, function (i, v) {
            t.frmEl.status.append(new Option(v.name, v.id));
        });
        t.frmEl.status.trigger("change");
    };
    t.frmEl.start_date.on("dp.change", function(e) {
        if(typeof e !== undefined || typeof e.date !== undefined) {
            t.frmEl.end_date.data('DateTimePicker').minDate(e.date);
        }
    });

    t.frmEl.start_date.datetimepicker({ format: "DD/MM/YYYY hh:mm A", showClear: true, showClose: true, sideBySide: true });
    t.frmEl.end_date.datetimepicker({ format: "DD/MM/YYYY hh:mm A", showClear: true, showClose: true, sideBySide: true });
    t.content.on('click', '.open-acc-view', $.proxy(t.viewModal));
    t.content.on('click', '.open-force-complete', $.proxy(t.openForceComplete));
    t.content.on('click', '.open-del-sch-modal', $.proxy(t.deleteSchModal));
    t.content.on('click', '.open-edit-sch-modal', $.proxy(t.editSchModal));
    t.content.on('click', '.open-acc-add-modal', $.proxy(t.addAccModal));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.go-back-config', $.proxy(t.goBack));
    t.dTbl.ajax.reload();
};

var TicketReplayEmail = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#reply_email_config');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#reply_email_form');

    t.frmEl = {};
    t.frmEl.mail_service_enabled = t.frm.find('#mail_service_enabled');
    t.frmEl.mail_driver = t.frm.find('#mail_driver');
    t.frmEl.mail_host = t.frm.find('#mail_host');
    t.frmEl.mail_port = t.frm.find('#mail_port');
    t.frmEl.mail_username = t.frm.find('#mail_username');
    t.frmEl.mail_password = t.frm.find('#mail_password');
    t.frmEl.mail_encryption = t.frm.find('#mail_encryption');
    t.frmEl.mail_from_address = t.frm.find('#mail_from_address');
    t.frmEl.mail_from_name = t.frm.find('#mail_from_name');
    t.frmEl.mailgun_domain = t.frm.find('#mailgun_domain');
    t.frmEl.mailgun_secret = t.frm.find('#mailgun_secret');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');

    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        },
        {
            targets: 0,
            render: function (d) {
                var x = d;
                var a = [];
                a.push("<button class='btn dtActbtn open-reply-email-edit-modal' data-toggle='tooltip' data-original-title='Edit Account' data-id=\"" + d.id + "\" ><i class=\"fa fa-pencil\"></i></button>");
                a.push("<button class='btn dtActbtn open-reply-email-delete' data-toggle='tooltip' data-original-title='Delete Account' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                return '<div class="popup-toolbox checkselect" style="display: inline-block;">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            }
        }
        ],
        order: [
            [10, 'desc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: config.url.list,
            type: "get",
            data: function (d) {
                d._token = config.token;
            }
        },
        columns: [
            { data: 'a' },
            { data: 'a.mail_service_enabled_text' },
            { data: 'a.mail_driver' },
            { data: 'a.mail_host'},
            { data: 'a.mail_port'},
            { data: 'a.mail_username'},
            { data: 'a.mail_encryption_text'},
            { data: 'a.mail_from_address'},
            { data: 'a.mail_from_name'},
            { data: 'a.mailgun_domain'},
            { data: 'a.updated_at_format'}
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#mytable_filter input").off(".DT");
            $("#mytable_filter").empty().html(
                '<div class="input-group margin">' +
                '<div class="input-group-btn">' +
                '</div>' +
                '<input type="text" class="form-control" placeholder="'+config.translations.serach_option+'"  style="margin-right:16px;"  aria-controls="mytable"  >' +
                '</div>');

            $("#mytable_filter input").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    t.createReplyEmail = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.add;
        t.mdltitle.text("Add Email Account");
        t.btn.submit.text("Save");
        t.mdl.modal("show");
        t.resetFrm();
    };

    t.loadForm = function(obj) {
        t.resetFrm();
        if(obj.mail_service_enabled >= 0) {
            t.frmEl.mail_service_enabled.val(obj.mail_service_enabled).trigger("change");
        }
        if(obj.mail_encryption == "ssl") {
            t.frmEl.mail_encryption.val(obj.mail_encryption).trigger("change");
        }else if(obj.mail_encryption == "tls") {
            t.frmEl.mail_encryption.val(obj.mail_encryption).trigger("change");
        }else if(obj.mail_encryption == "false") {
            t.frmEl.mail_encryption.val(obj.mail_encryption).trigger("change");
        }
        t.frmEl.mail_driver.val(obj.mail_driver);
        t.frmEl.mail_host.val(obj.mail_host);
        t.frmEl.mail_port.val(obj.mail_port);
        t.frmEl.mail_username.val(obj.mail_username);
        t.frmEl.mail_from_address.val(obj.mail_from_address);
        t.frmEl.mail_from_name.val(obj.mail_from_name);
        t.frmEl.mailgun_domain.val(obj.mailgun_domain);
        t.frmEl.mailgun_secret.val(obj.mailgun_secret);
        t.mdl.modal("show");
    };

    t.resetFrm = function () {
        t.frmEl.mail_driver.val('');
        t.frmEl.mail_host.val('');
        t.frmEl.mail_port.val('');
        t.frmEl.mail_username.val('');
        t.frmEl.mail_password.val('');
        t.frmEl.mail_from_address.val('');
        t.frmEl.mail_service_enabled.trigger("change");
        t.frmEl.mail_encryption.val("ssl").trigger("change");
        t.frmEl.mail_from_name.val('');
        t.frmEl.mailgun_domain.val('');
        t.frmEl.mailgun_secret.val('');
        t.frmValidator.resetForm();
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if( t.frmValidator.form() == false ) {
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            vex.dialog.alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            // t.httpCall = true;
        });
    };

    t.editReplyEmail = function (e) {
        e.preventDefault();
        var acc_id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.update + "/" + acc_id;
        var http = $.get(t.config.url.get + "/" + acc_id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text("Edit Email Account");
                    t.btn.submit.text("Update");
                    t.loadForm(data.data);
                }
                else {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteReplyEmail = function (e) {
        e.preventDefault();
        var email_id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + email_id;
        vex.dialog.confirm({
            message: 'Are you sure to delete this Account?',
            callback: function (value) {
                if (value == true) {
                    var http = $.get(t.httpPostPath);
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
                                t.dTbl.ajax.reload();
                            }
                            else {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function () {
                        // t.httpCall = true;
                    });
                }
            }
        });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            mail_driver: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_host: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_port: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_username: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_password: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_from_address: {
                required: true,
                acceptable_spcl_chr: true
            },
            mail_from_name: {
                required: true,
                acceptable_spcl_chr: true
            },
            mailgun_domain: {
                required: true,
                acceptable_spcl_chr: true
            },
            mailgun_secret: {
                required: true,
                acceptable_spcl_chr: true
            }
        }
    });

    var select2Opts = {width:"100%"};
    t.frmEl.mail_service_enabled.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.mail_service_enabled.parent()}));
    t.frmEl.mail_encryption.select2($.extend({},select2Opts,{dropdownParent: t.frmEl.mail_encryption.parent()}));
    t.content.on('click', '.open-add-modal', $.proxy(t.createReplyEmail));
    t.content.on('click', '.open-reply-email-edit-modal', $.proxy(t.editReplyEmail));
    t.content.on('click', '.open-reply-email-delete', $.proxy(t.deleteReplyEmail));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));

    t.dTbl.ajax.reload();

};

var AliasAccount = function(config) {
    let defaultCompany = localStorage.getItem('Default_Company');
    let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;
    var t = this;
    t.config = config;
    t.content = $('.main-content');
    t.panel = t.content.find("#alias_account_panel");
    t.table = t.panel.find('#alias_accounts');
    t.mdl = t.content.find('#alias_account_mdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');

    t.frm = t.mdl.find('#alias_account_form');
    t.mdl.subCategoryIdCvr = t.frm.find("#sub_category_id_cvr");
    t.httpCall = true;

    t.el = {};
    t.el.alias_email = t.frm.find('#alias_email');
    t.el.account_status = t.frm.find('#account_status');
    t.el.default_department_id = t.frm.find("#default_department_ids");
    t.el.default_prob_cat_id = t.frm.find("#default_prob_cat_id");
    t.el.default_sub_cat_id = t.frm.find("#default_sub_cat_id");
    t.el.company_id = t.frm.find("#company_id");
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');
    t.pageLength = t.content.find(".user-list-page-length-alias"); 
    t.searchbox = t.content.find(".searchbox-alias");
    t.data = {
        problem_categories: [],
        sub_categories: []
    };
    t.changePageLengthAlias = function (e) {
        var length = parseInt(t.pageLength.val(), 10);

        if (e) {
            e.preventDefault();
        }

        if (length > 0) {
            t.dTl.page.len(length).draw(false);
        }
    }
    t.actionStyleScope = t.content.find(".list-view-panels").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

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
            '" title="',
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

        if (record) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("action_edit_alias_account", "Edit Alias Account"),
                    "open-edit-modal-alias-account",
                    record.id,
                    t.icons.edit
                )
            );
        }

        if (record) {
            buttons.push(
                t.actionButtonHtml(
                    t.tr("action_delete_alias_account", "Delete Alias Account"),
                    "open-delete-modal-alias-account is-delete",
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

    t.tblHelpers = {
        actions: function () {
            return function (data, type, row) {
                return t.renderActionButtons(row.a || data);
            };
        },
    };
    /* make table as dataTable */
    t.dTl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [5]
            }, {
                
                targets: 5,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
                
            },
            {
                targets: 1,
                bSortable: false,
                render: function (data, type, row) {
                    return row.a.account_status_text == "Enabled"
                        ? `<span class="badge rounded-pill b5-text py-1" style="background:#E5FFE7;color:#006D1D">Enabled</span>`
                        : `<span class="badge rounded-pill b5-text py-1"  style="background:#F2E5FF;color:#5C00E5">Disabled</span>`;
                },
            },
        ],
        order: [
            [0, 'asc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
        ajax: {
            url: t.config.url.alias_account_list,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.company_id = companyId;
                d.search = t.searchbox.val();
            }
        },
        columns: [
            { data: 'a.alias_email' },
            { data: 'a.account_status_text' },
            { data: 'a.department' },
            { data: 'a.pro_cat' },
            { data: 'a.sub_cat' },
            { data: 'a' },
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).find('td').addClass('b5-text');
        },
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            var searchBox = '<div class="input-group table-search-btns"><input type="text" class="form-control input-sm" placeholder="'+config.translations.serach_option+'" style="width: 210px;" aria-controls="mytable" /></div>';
            $("#alias_accounts_filter").empty().html(searchBox);
            $("#alias_accounts_filter input").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    t.reload = function () {
        t.dTl.ajax.reload();
    };

     // refill the problem category
    t.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.problem_categories = [];
        t.el.default_prob_cat_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.el.default_department_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        t.el.default_prob_cat_id.append(new Option(v.name, v.id));
                    });
                }
            }).always(function () {
                t.el.default_prob_cat_id.trigger("change");
            });
        }
        else {
            t.el.default_prob_cat_id.trigger("change");
        }
    };

    // refill the sub category
    t.refillSubCategory = function (e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.sub_categories = [];
        t.el.default_sub_cat_id.empty().append(new Option(config.translations.Select_Sub_Category, ""));
        var type_val = parseInt($.trim(t.el.default_prob_cat_id.val()));
        let hasSubCategory = false;
        if (type_val > 0 && !isNaN(type_val)) {
            $.each(t.data.problem_categories, function (i, v) {
                if (v.id == type_val) {
                    if (Array.isArray(v.sub) && v.sub.length > 0) {
                        hasSubCategory = true;
                        t.data.sub_categories = v.sub;
                        $.each(v.sub, function (j, k) {
                            t.el.default_sub_cat_id.append(new Option(k.name, k.id));
                        });
                    }
                    return false;
                }
            });
        }
        if (hasSubCategory) {
            $("#default_sub_cat_id").closest(".col-md-12").show();
        } else {
            $("#default_sub_cat_id").val("");
            $("#default_sub_cat_id").closest(".col-md-12").hide();
        }
        if (typeof val != "undefined") {
            t.el.default_sub_cat_id.val(val);
        }
        t.el.default_sub_cat_id.trigger("change");
    };

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibility = function () {
        if (t.data.sub_categories.length > 0) {
            t.el.default_sub_cat_id.rules("add", {
                required: true,
                str_name: true
            });
            t.mdl.subCategoryIdCvr.show();
        }
        else {
            t.el.default_sub_cat_id.rules("remove");
            t.mdl.subCategoryIdCvr.hide();
        }
    };
    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            alias_email: {
                required: true,
                clean_text_only:true,
            },
            default_department_id: {
                required: true
            },
            default_prob_cat_id: {
                required: true
            },
            default_sub_cat_id: {
                required: true
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = getErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else {
                error.insertAfter(element.closest(".input-group"));
            }
            updateValidationState(element, true);
        },
        highlight: function (element) {
            updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            updateValidationState($(element), false);
        },

    });

    t.toggleListen = function(s) {
        t.el.default_department_id.off("change", $.proxy(t.refillProblemCategory));

        if (typeof s != "undefined" && s == true) {
            t.el.default_department_id.on("change", $.proxy(t.refillProblemCategory));
        }
    };

    t.loadAccForm = function (obj) {
        t.resetFrm();
        t.toggleListen();
        t.el.alias_email.val(obj.val.alias_email);
        
        t.el.default_department_id.empty().append(new Option(obj.department.text, obj.department.id, true, true)).trigger("change").prop('disabled', false);
        
        /* set problem category */
        t.el.default_prob_cat_id.empty();
        $.each(obj.problem_category, function(i, d) {
            var op = (d.id == obj.val.default_prob_cat_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
            t.el.default_prob_cat_id.append(op).prop('disabled', false);
        });
        t.el.default_prob_cat_id.trigger("change");
        t.data.problem_categories = obj.problem_category;

        /* set sub category */
        t.refillSubCategory(undefined, obj.val.default_sub_cat_id);
        t.el.default_sub_cat_id.prop('disabled', false);
        
        t.el.account_status.val(obj.val.account_status).trigger("change");

        t.toggleListen(true);        
    };

    t.mdl.new_notify = function(){
        
        var notify = t.frmEl.except_notify_status.val();
    
        if(notify ==  0){
            t.mdl.notify.addClass("hide");
            t.mdl.ccnotify.addClass("hide");
        }else if(notify ==  1){
            t.mdl.notify.removeClass("hide");
            t.frmEl.except_notify_tos.rules("add", {required:true});
            t.mdl.ccnotify.removeClass("hide");
            t.frmEl.except_notify_ccs.rules("add", {required:true});
        }

    }
    
    t.resetFrm = function () {
        t.frm.trigger("reset");
        t.el.alias_email.val("");
        t.el.default_department_id.val("").empty().trigger("change");
        t.el.default_prob_cat_id.val("").empty().trigger("change");
        t.el.default_sub_cat_id.val("").empty().trigger("change");
        t.el.account_status.val(1).trigger("change").prop('disabled', false);
        // t.frmValidator.resetForm();
        t.frm.find('.amg-form-invalid').removeClass('amg-form-invalid');
        t.frm.find('.amg-form-select-error').removeClass('amg-form-select-error');
        t.frm.find('.amg-form-error-wrap').empty();
    };

    t.addModal = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.add_alias_acc;
        t.mdltitle.text(t.config.translations.New_Alias_Account);
        t.toggleListen(true);
        t.btn.submit.removeClass("hide");
        t.mdl.modal("show");
        t.resetFrm();
    };


    t.handlesubmit = function (e) {
        e.preventDefault();

        if(t.httpCall != true) {
            return false;
        }

        if( t.frmValidator.form() == false ) {
            return false;
        }

        t.httpCall = false;
        var frmData = new FormData(t.frm[0]);
        frmData.append('company_id',companyId);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg' : t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);

        });
        http.always(function () {
            t.httpCall = true;
            $('#img').hide();
        });
    };

    t.editAcc = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.update_alias_acc + "/" + id;
        var http = $.get(t.config.url.load_alias_acc + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.btn.submit.removeClass("hide");
                    t.mdltitle.text("Edit Alias Account");
                    t.btn.submit.text("Save Changes");
                    t.loadAccForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg' : t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.viewAccModal = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit_acc + "/" + id;
        var http = $.get(t.config.url.get_acc + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdltitle.text("View");
                    t.btn.submit.addClass("hide");
                    t.mdl.passcover.addClass("hide");
                    t.mdl.modal("show");
                    t.loadAccViewForm(data.data);
                }
            }
        });
    };

    // t.deleteAcc = function (e) {
    //     e.preventDefault();
    //     var Id = $(this).attr("data-id");
    //     t.httpPostPath = t.config.url.delete_alias_acc + "/" + Id;
    //     sweetAlertConfirm({
    //         message:'Are you sure to delete this alias email account?',
    //         url: t.httpPostPath,
    //         data: {},
    //         onSuccess: function (data) {
    //             if (data.status === "success") {
    //                 sweetAlert('center', 'success', data);
    //                 t.dTl.ajax.reload();
    //             } else {
    //                 sweetAlert('center', 'error', data);
    //             }
    //         },
    //         complete: function () {
    //             t.httpCall = true;
    //         }
    //     });
    // };
    t.deleteAcc = function (e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete_alias_acc + "/" + Id;
        sweetAlertConfirm({
            message: 'Are you sure to delete this alias email account?',
            url: t.httpPostPath,
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            onSuccess: function (data) {

                if (data.status === "success") {
                    sweetAlert('center', 'success', data);
                    t.dTl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }

            },

            complete: function () {
                t.httpCall = true;
            }
        });
    };
    t.content.on("keyup", ".searchbox-alias", function (e) {
        if (e.which === 13) {
            t.searchData(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.searchData(e);
        }
    });
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
        t.dTl.ajax.reload();
    };
    var select2Opts = {dropdownParent: $('#alias_account_mdl'), width: "100%" };
    t.el.account_status.select2(select2Opts);
    t.el.default_prob_cat_id.select2(select2Opts).on("change", $.proxy(t.refillSubCategory));
    t.el.default_sub_cat_id.select2(select2Opts);
    t.el.default_department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.el.default_department_id.parent(),
        placeholder: config.translations.select_department,
        allowClear: true,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '',
                    company_id: t.el.company_id.val()
                };
            },
            processResults: function (response) {
                return {
                    results: Array.isArray(response.results)
                        ? response.results
                        : [],
                    pagination: {
                        more: !!response.pagination?.more
                    }
                };
            }
        }
    })).on("change", $.proxy(t.refillProblemCategory));

    t.el.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.el.company_id.parent(),
        placeholder: "Select Company", 
        allowClear: true,
        ajax: {
            url: t.config.url.company_defulte, 
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || ''  
                };
            },
            processResults: function (response) {
                return {
                    results: Array.isArray(response.results) ? response.results : [],
                    pagination: {
                        more: !!response.pagination?.more
                    }
                };
            }
        }
    })).on("change", function () {
        t.el.default_department_id.val(null).trigger("change");
    });

    $.get(t.config.url.company_defulte, { id: companyId }, function (response) {
        let company = response.results[0];
        if (company) {
            let option = new Option(company.text, company.id, true, true);
            t.el.company_id.append(option).trigger("change");
        }
    });

    t.panel.on('click', '.open-alias-add-modal', $.proxy(t.addModal));
    t.panel.on('click', '.open-edit-modal-alias-account', $.proxy(t.editAcc));
    t.content.on('click', '.open-delete-modal-alias-account', $.proxy(t.deleteAcc));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.btn-reload-list-alias', $.proxy(t.reload));
    t.content.on("change", ".user-list-page-length-alias", $.proxy(t.changePageLengthAlias));

    t.dTl.ajax.reload();
};

