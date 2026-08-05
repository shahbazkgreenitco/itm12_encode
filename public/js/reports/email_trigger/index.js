var EmailTriggers = function(config){
    var t = this;
    t.config = config;
    t.content = $('#mainContent');
    
    t.table = t.content.find('#mytable');
    t.calendar = t.content.find('#dd');
    t.mdl = t.content.find('#addReportEmailTriggerMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#addReportEmailTrigger');

    t.mdl.addGroupUser = t.content.find('#addReportEmailTriggerMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.frm.addUser = t.mdl.addGroupUser.find('#addReportEmailTrigger');

    t.frmEl = {};
    t.frmEl.reportTriggerName = t.frm.find('#trigger_name');
    t.frmEl.company_id= t.frm.find('#company_id');
    t.frmEl.reportTriggerDesc = t.frm.find('#trigger_description');
    t.frmEl.reportTriggerCCTo = t.frm.find('#trigger_cc_to');
    t.frmEl.reportDepartments = t.frm.find('#report_departments');
    t.frmEl.reportTriggerReportElements = t.frm.addUser.find('#trigger_report_elements');
    t.frmEl.enabled = t.frm.addUser.find('#enabled');
    
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.searchbox = t.content.find("#tableSearch");
    t.frmEl.reportTriggerCCTo.select2({
        width: "100%",
        placeholder: "Select User",
        dropdownParent: t.frmEl.reportTriggerCCTo.parent(),
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.frmEl.company_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
    });

    t.frmEl.reportTriggerReportElements.select2({
        width: "100%",
        placeholder: "Select",
        dropdownParent: t.frmEl.reportTriggerReportElements.parent(),
    });

    t.frmEl.reportDepartments.select2({
        width: "100%",
        placeholder: "Select Department",
        dropdownParent: t.frmEl.reportDepartments.parent(),
        ajax: {
            url: t.config.url.getDepartmentsWithTicketEnabled,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.frmEl.company_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
    });

    t.frmEl.enabled.select2({
        width: "100%",
        placeholder: "Select",
        dropdownParent:t.frmEl.enabled.parent(),
    });
    
    t.frmEl.company_id.select2({
        placeholder: "Select Company",
        allowClear: true,
        width: '100%',
        dropdownParent:t.frmEl.company_id.parent(),
        ajax: {
            url: t.config.url.getCompany,
            type: "GET",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '',
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

    t.buildActions = function(d) {
        var actions = [];
        if($.inArray("EmailReportTriggerEdit", t.config.permissions) !== -1 ) {
            actions.push(`
                <button
                data-id="${d}"
                class="amg-action-btn open-edit-modal"
                title="${"Edit Group"}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"
                        fill="currentColor"/>
                    </svg>
                </button>
            `);
        }
        if($.inArray("EmailReportTriggerDelete", t.config.permissions) !== -1 ) {
            actions.push(`
                <button class="amg-action-btn open-delete dtActDel"
                    data-id="${d}" title="Delete">
                    <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                    </svg>
                </button>
            `);
        }
        return `<div class="amg-datatable-actions d-flex gap-2">${actions.join("")}</div>`;
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        stateSave: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
        lengthChange: false,
        searching: false,
        scrollX: true,
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1,
        },
        aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [3,4,5,6]
            },
            {
                targets: 2,
                render: function (d) {
                    return d == 1 ? 'Yes' : 'No';
                }
            },
            {
                targets: 3,
                render: function (d) {
                    var text = '';
                    $.each(d,function(i,data) {
                        text += '<div class="recipient"><span class="badge bg-primary">'+data.username+'</span></div>';
                    });
                    return text;
                }
            },
            {
                targets: 4,
                render: function (d) {
                    var text = '';
                    $.each(d,function(i,data) {
                        text += '<div class="recipient"><span class="badge bg-primary">'+data+'</span></div> ';
                    });      
                    return text;
                }
            },
            {
                targets: 5,
                render: function (d) {
                    var text = '';
                    $.each(d,function(i,data){
                        text += '<div class="recipient"><span class="badge bg-primary">'+data+'</span></div> ';
                    });
                    return text;
                }
            },
             {
                targets: 6,
                render: function(d) {
                    return t.buildActions(d || {});
                }
            }
        ],
        order: [ [1, 'asc'] ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.getEmailReportTriggers,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
            }
        },
        columns: [
            { data: 'trigger_name' },
            { data: 'company_name' },
            { data: 'enabled' },
            { data: 'receipient' },
            { data: 'reportElementName' },
            { data: 'departmentElementName' },
            { data: 'id' }
        ],

        drawCallback: function(settings) {
            var api = this.api();
            var wrapper = $(api.table().node()).closest('.dataTables_wrapper');
            var info = wrapper.find('.dataTables_info');
            var summary = $('#page-btm-summary');
            if (info.length && summary.length) {
                summary.empty().append(info);
            }
            var paging = wrapper.find('.dataTables_paginate');
            var pagebtns = $('#pagebtns');
            if (paging.length && pagebtns.length) {
                pagebtns.empty().append(paging);
            }
        }
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.createEmailTrigger = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.frmValidator.resetForm();
        t.httpPostPath =  t.config.url.add;
        t.frm.addUser.trigger("reset");
        t.mdltitle.text("Add Email Report Trigger");
        t.btn.submit.text(t.config.translations.save);
        if (config.company &&
            config.company.company_id !== null &&
            config.company.company_name !== null
        ) {
            let option = new Option(config.company.company_name,config.company.company_id,true,true);
            t.frmEl.company_id.append(option).trigger('change');
        } else {
            t.frmEl.company_id.val(null).trigger('change');
        }
        t.mdl.modal("show");
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            trigger_name: { required: true, str_name_format: true, maxlength: 100 },
            trigger_description: { required: true },
            'trigger_cc_to[]': { required: true, minlength: 2 },
            'trigger_report_elements[]': { required: true },
            enabled: { required: true },
            company_id: { required: true }
        },
        errorPlacement: function(error, element) {

            if (element.closest('.input-group.ticket-input-group').length) {
                error.insertAfter(element.closest('.input-group.ticket-input-group'));
            } else {
                error.insertAfter(element);
            }

        }
    });

    t.handlesubmit = function (e) {
        e.preventDefault();
        if( t.frmValidator.form() == false ) {
            return false;
        }
    
        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);
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
                    toastr.success(data.msg);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        http.fail(function () {
            vex.dialog.alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.loader.hide();
        });
    };

    t.handleUsersubmit = function (e) {
        e.preventDefault();
        var frmData = new FormData(t.frm.addUser[0]);
        frmData.append('_token', t.config.token);
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
                    toastr.success(data.msg);
                    t.mdl.addGroupUser.modal("hide");
                    t.userdTbl.ajax.reload();
                } else {
                    toastr.error(data.msg);
                }
            }
        });
        http.fail(function () {
            vex.dialog.alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.loader.hide();
        });
    };

    t.loadForm = function (obj) {
        t.resetFrm();
        t.frmValidator.resetForm();
        $('#emailTriggerId').val(obj.data.id);
        t.frmEl.reportTriggerName.val(obj.data.trigger_name);
        t.frmEl.reportTriggerDesc.val(obj.data.trigger_description);
        if(typeof obj.dropdown.cc != "undefined") {
            t.frmEl.reportTriggerCCTo.empty();
            $.each(obj.dropdown.cc,function(i,d){
                t.frmEl.reportTriggerCCTo.append(new Option(d.text, d.id, true, true));
            })  
        }
        var option = new Option(obj.data.company_name, obj.data.company_id, true, true);
        t.frmEl.company_id.append(option);

        if(typeof obj.dropdown.elements != "undefined") {
            var elementData = obj.data.trigger_report_elements ? obj.data.trigger_report_elements.split(","): [];
            t.frmEl.reportTriggerReportElements.val(elementData).trigger("change");
        }
        if(typeof obj.dropdown.enabled != "undefined") {
            t.frmEl.enabled.val('');
            $.each(obj.dropdown.enabled,function(i,d){
                t.frmEl.enabled.val(d.id).change();
            })  
        }
        if(typeof obj.dropdown.report_departments != "undefined") {
            t.frmEl.reportDepartments.empty().trigger('change');
            $.each(obj.dropdown.report_departments,function(i,d){
                t.frmEl.reportDepartments.append(new Option(d.text, d.id, true, true));
            })
        }
    };

    t.resetFrm = function () {
        $('#emailTriggerId').val('');
        t.frmEl.reportTriggerName.val('');
        t.frmEl.reportTriggerDesc.val('');
        t.frmEl.reportTriggerCCTo.val('').trigger("change");
        t.frmEl.reportTriggerReportElements.val('').trigger("change");
        t.frmEl.enabled.val("1").trigger("change");
        t.frmEl.reportDepartments.val('').trigger("change");
        t.frmEl.company_id.val('').trigger("change");
    };

    t.editEmailTrigger = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.editEmailTrigger + "/" + id;
        var http = $.get(t.config.url.getEmailTrigger + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text("Edit Email Report Trigger");
                    t.btn.submit.text("Save Changes");
                    t.loadForm(data);
                } else {
                    toastr.error(data.msg);
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

    t.deleteEmailTrigger = function(e) {
        e.preventDefault();
        var recId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + recId;
        sweetAlertConfirmation({
            message: 'Are you sure to delete this email triggers?',
            onConfirm: function() {
                var http = $.get(t.httpPostPath);
                http.done(function(data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            toastr.success(data.msg);
                            t.dTbl.ajax.reload();
                        } else if(data.status == 'deleted') {
                            toastr.error(data.msg);
                            t.dTbl.ajax.reload();
                        } else {
                            toastr.error(data.msg);
                        }
                    }
                });
                http.fail(function() {
                    toastr.error(config.translations.something_went_wrong);
                });
                http.always(function() {
                    t.httpCall = true;
                });
            }
        });
    };

    t.refreshList = function() {
        t.dTbl.ajax.reload();
    }

    t.content.on('click', '.open-add-modal', $.proxy(t.createEmailTrigger));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editEmailTrigger));
    t.content.on('click', '.open-delete', $.proxy(t.deleteEmailTrigger));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));

    t.content.find('#tableSearch').on('keyup', function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            var v = $(this).val().trim();
            if (typeof $(this).validate_str_param === 'function') {
                v = $(this).validate_str_param();
                if (v === false) {
                    alert("Please enter a valid value for search");
                    return false;
                }
            }
            t.dTbl.search(v).draw();
        }
    });

    t.content.on('click', '.btn-reload-list', $.proxy(t.refreshList));
    t.content.find('#showSelect').on('change', function() {
        t.dTbl.page.len($(this).val()).draw();
    });

    t.dTbl.ajax.reload();

    t.frmEl.company_id.on('change', function () {
        t.frmEl.reportTriggerCCTo.val(null).trigger('change');
        t.frmEl.reportDepartments.val(null).trigger('change');
        t.frmEl.reportTriggerCCTo.empty();
        t.frmEl.reportDepartments.empty();
    });
}