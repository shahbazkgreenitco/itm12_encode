var userGroupMembers = function (config) {
    let defaultCompany = JSON.parse(localStorage.getItem("Default_Company"));
    let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : defaultCompany.id;
    var t = this;
    t.config = config;
    t.content = $("#cc-email-group-member-list-wrapper");

    t.mdl = t.content.find('#cc-email-member-modal');
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find('#cc-email-member-mdl-frm');
    t.mdl.frmEl = {};
    t.mdl.frmEl.addMember  = t.mdl.frm.find('.addMember');
    t.mdl.frmEl.editMember = t.mdl.frm.find('.editMember');
    t.mdl.frmEl.user       = t.mdl.frm.find("#user");
    t.mdl.frmEl.users      = t.mdl.frm.find('#users');
    t.mdl.btnSubmit        = t.mdl.find("#btnSubmit");
    t.mdl.btnClear         = t.mdl.find("#btnClear");

    t.httpCall     = false;
    t.httpPostPath = "";

    t.bsModal = new bootstrap.Modal(document.getElementById("cc-email-member-modal"), {
        backdrop: 'static',
        keyboard: false
    });

    t.buildActions = function(d) {
        var actions = [];
        actions.push(`
            <button class="amg-action-btn btn-edit-member dtActEdit"
                data-id="${d.id}" data-bs-toggle="tooltip"  title="${config.translations.edit_member}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                </svg>
            </button>
        `);
        actions.push(`
            <button class="amg-action-btn danger btn-delete-member dtActDel"
                data-id="${d.id}" data-bs-toggle="tooltip" title="${config.translations.delete_member}">
                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                </svg>
            </button>
        `);
        return `<div class="amg-datatable-actions d-flex justify-content-center gap-2">${actions.join("")}</div>`;
    };

    t.table = t.content.find("#cc-email-member-table");

    t.dTbl = t.table.DataTable({
        language: {
            info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
            infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
            emptyTable: t.config.datatable_translations.empty_result,
            zeroRecords: t.config.datatable_translations.empty_result,
            infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
            paginate: {
                previous: t.config.datatable_translations.prev,
                next: t.config.datatable_translations.next
            }
        },
        autoWidth: false,
        dom: "rtip",
        scrollX: true,
        aoColumnDefs: [
            {
                targets: 4,
                orderable: false,
                searchable: false,
                width: "120px",
                render: function(data, type, row) {
                    return t.buildActions(row.a || {});
                }
            }
        ],
        order: [[2, 'desc']],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.getMemberList + "/" + t.config.group.id,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
            }
        },
        columns: [
            { data: 'a.name' },
            { data: 'a.email' },
            { data: 'a.created_date' },
            { data: 'a.updated_date' },
            { data: 'a' }
        ],
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    var searchTimer = null;
    t.content.find(".user-list-search").on("keyup", function() {
        var v = $(this).val();
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            t.dTbl.search(v).draw();
        }, 400);
    });

    t.content.find(".cc-email-group-member-list-page-length").on("change", function() {
        t.dTbl.page.len($(this).val()).draw();
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    var select2Opts = { width: "100%" };

    t.userDropdownFormat = function(s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><span style='padding-right:3px;'>👤</span>" + s.text + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class='fa fa-envelope-o' style='padding-right:3px;'></i>" + s.email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class='fa fa-credit-card' style='padding-right:3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    var userAjaxOpts = {
        url: t.config.url.getUserByAjax,
        dataType: "json",
        delay: 300,
        data: function(p) {
            return {
                search: p.term,
                page: p.page || 1,
                company_id:companyId
            };
        }
    };

    var userTemplates = {
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50
                ? data.text.substring(0, 50) + '...'
                : data.text;
        }
    };

    t.mdl.frmEl.users.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        placeholder: config.translations.user_placeholder,
        ajax: userAjaxOpts,
        templateResult: userTemplates.templateResult,
        templateSelection: userTemplates.templateSelection,
    }));

    t.mdl.frmEl.user.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        placeholder: config.translations.user_placeholder,
        ajax: userAjaxOpts,
        templateResult: userTemplates.templateResult,
        templateSelection: userTemplates.templateSelection,
    }));

    t.mdl.frmEl.editMember.hide();

    t.mode = "add";

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        onfocusout: false,
        onkeyup: false,
        rules: {

        },
        errorPlacement: function(error, element) {
            error.appendTo(element.closest(".amg-form-field"));
        }
    });

    t.setValidationMode = function(mode) {
        t.mode = mode;
        if (mode === "add") {
           
            t.mdl.frmEl.users.rules("add", { required: true });
            t.mdl.frmEl.user.rules("remove");
        } else {
            t.mdl.frmEl.user.rules("add", { required: true });
            t.mdl.frmEl.users.rules("remove");
        }
    };

    t.resetFrm = function() {
        t.frmValidator.resetForm();
        t.mdl.frm.trigger("reset");
        t.mdl.frmEl.user.val(null).empty().trigger("change");
        t.mdl.frmEl.users.val(null).empty().trigger("change");
    };

    t.addMember = function() {
        t.resetFrm();
        t.setValidationMode("add"); 
        t.mdl.frm.attr('action', t.config.url.add);
        t.mdl.title.html('Add Member');
        t.mdl.btnSubmit.text("Add");
        t.mdl.frmEl.addMember.show();
        t.mdl.frmEl.editMember.hide();
        t.bsModal.show();
    };

    t.editMember = function(e) {
        e.preventDefault();
        var id = $(e.currentTarget).attr("data-id");

        $.get(t.config.url.edit + "/" + id)
            .done(function(data) {
                if (typeof data == "object" && data.status == "success") {
                    t.resetFrm();
                    t.setValidationMode("edit"); 
                    t.mdl.frm.attr('action', t.config.url.update + "/" + id);
                    t.mdl.title.html('Edit Member');
                    t.mdl.btnSubmit.text("Update");
                    t.mdl.frmEl.addMember.hide();
                    t.mdl.frmEl.editMember.show();

                    var u = data.data.users;
                    var displayName = u.displayName
                        ? u.displayName
                        : u.first_name + " " + u.last_name + " (" + u.username + ")";
                    t.mdl.frmEl.user
                        .empty()
                        .append(new Option(displayName, u.id, true, true))
                        .trigger("change");

                    t.bsModal.show();
                } else {
                    sweetAlert('center', 'error', data);
                }
            })
            .fail(function() {
                sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
            });
    };

    t.deleteMember = function(e) {
        e.preventDefault();
        var id = $(e.currentTarget).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + id;
        sweetAlerts(
            config.translations.delete_record,
            'warning',
            t.httpPostPath,
            t.dTbl,
            { msg: config.translations.something_went_wrong }
        );
    };

    t.handleSubmit = function(e) {
        e.preventDefault();

        if (t.frmValidator.form() == false) return false;

        t.mdl.btnSubmit.prop("disabled", true);
        t.httpPostPath = t.mdl.frm.attr('action');

        var formData = new FormData(t.mdl.frm[0]);

        $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        })
        .done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.bsModal.hide();
                    sweetAlert('center', 'success', data);
                    setTimeout(function() { t.dTbl.ajax.reload(); }, 800);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        })
        .fail(function() {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
        })
        .always(function() {
            t.mdl.btnSubmit.prop("disabled", false);
        });
    };

    t.content.on('click', '.btn-add-member',  $.proxy(t.addMember, t));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload, t));
    t.content.on('click', '.dtActEdit',       $.proxy(t.editMember, t));
    t.content.on('click', '.dtActDel',        $.proxy(t.deleteMember, t));
    t.mdl.on('click',     '#btnSubmit',       $.proxy(t.handleSubmit, t));
};