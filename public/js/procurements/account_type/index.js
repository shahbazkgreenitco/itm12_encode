var AccountType = function (config) {
    var t = this;
    t.config = config;
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#accountTypeMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#AccountTypeForm');
   
    t.frmEl = {};
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.company_id = t.frm.find('#company_id');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.searchbox = t.content.find(".searchbox");
    t.showModal = function () {
        t.modalInstance = new bootstrap.Modal(t.mdl[0]);
        t.modalInstance.show();
    };
    t.hideModal = function () {
        if (t.modalInstance) t.modalInstance.hide();
    };
    t.tblHelpers = {
        actions: function() {
            return function(d) {            
                let buttons = '';
                if (jQuery.inArray("ProcureAccountTypeEdit", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActEdit"
                            data-id="${d.id}" title="Edit">
                            <svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>
                        </button>
                    `;
                }                    
                if (jQuery.inArray("ProcureAccountTypeDelete", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn dtActDel me-1" data-toggle="tooltip" data-placement="top" title="Delete" data-id="${d.id}" >
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                            </button>
                    `;
                }
                return buttons;
            };
        },
        alignRight: function() {
            return function(d) {
                if (d == "" || d == null) return null;
                return '<div class="text-right">' + d + '</div>';
            }
        }
    };    
    t.frmValidator = t.frm.validate({
        onsubmit: false,
        ignore: [],                     
        rules: {
            company_id: {
                required: true,
                str_name: true
            },
            name: {
                required: true,
                str_name: true,
                maxlength: 50,
            },           
        },
        messages: {
            company_id: "Please select a company",
            name: "Please enter a valid account type name (2-50 characters)"
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
        invalidHandler: function (event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });    

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        dom: 'drtip',
        language: {
            paginate: {
                previous: t.config.translations.Previous,
                next: t.config.translations.Next
            },
            emptyTable: t.config.translations.No_matching_records_found,
            zeroRecords: t.config.translations.No_matching_records_found
        },
        searching: false,
        aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [3]
            }, {
                targets: 3,
                render: t.tblHelpers.actions()
            }
        ],
        order: [[2, 'desc']],
        deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.accountTypes,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.search.value = t.searchbox.val().trim();
            }
        },
        columns: [
            { data: 'a.company_name' },
            { data: 'a.name' },
            { data: 'a.last_updated_at' },
            { data: 'a' },
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();

            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");

            var $pageLengthSelect = $(".list-page-length");
            $pageLengthSelect.on("change", function() {
                var newLength = parseInt($(this).val(), 10);
                t.dTbl.page.len(newLength).draw();
            });

            $(".list-page-length").find("select");
            $(".searchbox").on("keyup", function(e) {
                if (e.keyCode === 13) {
                    let v = $(this).val().trim();
                    if (v === "") {
                        t.reload();
                    } else {
                        t.tableSearch(e);
                    }
                }
            });
            t.btn.add = t.content.find(".open-add-modal");          
            t.btn.search = t.content.find(".btn-searchbox");
            t.btn.reload = t.content.find(".btn-reload-list");
        },
        drawCallback: function () {
            let api = this.api();
            let info = api.page.info();

            let text = t.config.translations.Showing + " " +
                (info.recordsDisplay > 0 ? info.start + 1 : 0) + " " +
                t.config.translations.to + " " +
                info.end + " " +
                t.config.translations.of + " " +
                info.recordsDisplay + " " +
                t.config.translations.entries;

            if (info.recordsDisplay < info.recordsTotal) {
                text += " (" +
                    t.config.translations.filtered_from + " " +
                    info.recordsTotal + " " +
                    t.config.translations.total_entries +
                    ")";
            }

            $(this).closest('.dataTables_wrapper')
                .find('.dataTables_info')
                .empty()
                .text(text);
        }
    });
    var select2Opts = { width: "100%" };
    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function(params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items || data.results || [],
                    pagination: {
                        more: (params.page * 10) < (data.total_count || 0)
                    }
                };
            }
        },
        placeholder: "Select Company",
        allowClear: true,
        minimumInputLength: 0
    }));
    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function(e) {
        e.preventDefault();
        let v = t.searchbox.val().trim();
        if (v === "") {
            t.reload();
        } else {
            t.dTbl.search(v).draw(); 
        }
    };
    t.resetFrm = function () {
        t.frmEl.name.val('');
        if (t.frmEl.company_id && t.frmEl.company_id.select2) {
            t.frmEl.company_id.val(null).trigger('change');
            t.frmEl.company_id.empty();   
        }
        if (t.frmValidator && typeof t.frmValidator.resetForm === 'function') {
            t.frmValidator.resetForm();
        }
    };
    t.createAccountType = function (e) {
        e.preventDefault();
        t.resetFrm();                   
        t.showModal();
        t.mdltitle.text(t.config.translations.add_account_type);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.frmEl.company_id.append(option).trigger('change');
        }
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (!t.frmValidator || t.frmValidator.form() == false) {
            return false;
        }
        var frmData = new FormData();
        frmData.append('_token', t.config.token);
        frmData.append('company_id', t.frmEl.company_id.val());
        frmData.append('name', t.frmEl.name.val());
        
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function() {
                t.btn.submit.prop('disabled', true);
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.hideModal();
                    sweetAlert('center', 'success', data);
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.loader.hide();
            t.btn.submit.prop('disabled', false);
        });
    };

    t.loadForm = function (obj) {
    t.resetFrm();

    t.frmEl.name.val(obj.name);
        if (obj.company && obj.company.id) {
            t.frmEl.company_id.empty().append(new Option(obj.company.text, obj.company.id, true, true)).trigger("change");
        }
    };

    t.editAccountType = function (e) {
        e.preventDefault();
        var accountTypeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + accountTypeId;
        var http = $.get(t.config.url.get + "/" + accountTypeId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.showModal(); 
                    t.mdltitle.text(t.config.translations.edit_account_type);
                    t.btn.submit.text(t.config.translations.save_changes);
                    t.loadForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteAccountType = function (e) {
        e.preventDefault();
        var accountTypeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + accountTypeId;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete,'warning', t.httpPostPath, t.dTbl, data);
    };
    t.content.on('click', '.open-add-modal', $.proxy(t.createAccountType));       
    t.content.on('click', '.dtActEdit', $.proxy(t.editAccountType));
    t.content.on('click', '.dtActDel', $.proxy(t.deleteAccountType));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.content.on("click", '.btn-reload-list', $.proxy(t.reload));
    t.dTbl.ajax.reload();
};