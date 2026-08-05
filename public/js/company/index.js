$(document).ready(function () { 
    var deletedRecords = false; 
    var table = $('#mytable');
    var dTbl = table.DataTable({
        dom: 'lrtip',
        deferLoading: 0,
        autoWidth: false,
        colReorder: true,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [4]
        }, {
            targets: 4,
            render: function (data, type, row) {
                let str = '';
                if (deletedRecords) {
                    {
                    str += `
                   <button type="button"
                    class="btn user-list-action-btn me-1 go-restore"
                    data-id="${row.a.id}"
                    data-bs-toggle="tooltip"
                    title="Restore">
                   <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M6.678 20.567C2.532 18.021.759 12.758 2.718 8.144 4.876 3.06 10.746.688 15.83 2.846c5.084 2.158 7.456 8.029 5.298 13.112-.843 1.987-2.253 3.56-3.961 4.609" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M17 16v4.4c0 .331.269.6.6.6H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                  </button>
                  `;
                    }

                } else {
                    if (row.a.windows_agent_request == 0) {
                        str += `
                            <button type="button"
                                class="btn user-list-action-btn dtActReq me-1"
                                data-id="${row.a.id}" data-bs-toggle="tooltip" title="${config.translations.mark_request}">
                                
                                <i class="bi bi-hand-index-thumb"></i>
                            </button>
                        `;
                    }
                    if (config.client == 'rolepermission' && jQuery.inArray("CompanyEdit", config.permissions) !== -1) {
                        str += `
                            <button type="button"
                                class="btn user-list-action-btn me-1 open-edit-modal"
                                data-id="${row.a.id}" data-bs-toggle="tooltip" title="${config.translations.edit}">
                                
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                   <svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>
                                </svg>
                            </button>
                        `;
                    }
                    if (config.client == 'rolepermission' && jQuery.inArray("CompanyDelete", config.permissions) !== -1) {
                        str += `
                            <button type="button" class="btn user-list-action-btn me-1 go-del" data-bs-toggle="tooltip" data-id="${row.a.id}"  title="${config.translations.delete}">                                
                                <svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>
                            </button>
                        `;
                    }
                }
                if (str === '') return '';
                return `
                        <div class="d-flex align-items-center justify-content-center">
                            ${str}
                        </div>
                    `;
            }

        }, {
            targets: 3,
            render: function (c) {
                if (c.windows_agent_request == 2) {
                    return "Agent Generated";
                }
                else if (c.windows_agent_request == 1) {
                    return "Requested";
                }
                else {
                    return "Not Requested";
                }
            }
        },
        {
            targets: 2,
            render: function (c) {
                if (c != null) {
                    return '<a  href=" ' + c + '"> Click here to download </a>';
                }
                return '';
            }
        }
        ],
        order: [
            [1, 'asc']
        ],
        processing: true,
        serverSide: true,
        responsive: true,
        stateSave: true,
        language: {
            paginate: {
                previous: config.translations.previous,
                next: config.translations.next
            },
            info: config.translations.showing_entries,
            infoEmpty: config.translations.no_entries,
            infoFiltered: config.translations.filtered_from,
            zeroRecords: config.translations.no_matching_records,
            emptyTable: config.translations.no_data,
            search: config.translations.search,
            lengthMenu: config.translations.length_menu
        },
        colResize: {
            resizeTable: true
        },
        stateSaveParams: function (settings, data) {
            data.search.search = '';
        },
        ajax: {
            url: window.config.url.companies,
            type: "post",
            data: function (d) {
                d._token = window.config.token;
                d.deletedRecords = deletedRecords ? "true" : "false";
            }
        },
        columns: [
            { data: 'a.name' },
            { data: 'a.company_tag' },
            { data: 'a.windows_agent_path' },
            { data: 'a' },
            { data: 'a', width: "148px" }
        ],
        drawCallback: function (settings) {
            var $wrapper = $(this).closest('.dataTables_wrapper');

            $wrapper.find('.dataTables_info').css({
                'float': 'left',
                'margin-top': '15px'
            });

            $(this).find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) {
                    existing.dispose();
                }
                new bootstrap.Tooltip(this, {
                    trigger: "hover"
                });
            });
        },
        fnInitComplete: function () {
            var api = this.api();
            $('#mytable_length').hide();
            $('#companies-list-search').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    let value = $(this).val().trim();
                    api.search(value).draw();
                }
            });
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();
                api.columns().every(function () {
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;
                    controls.append(`
                        <div class="dropdown-item" style="padding:6px">
                            <label for="${columnId}">
                                <input type="checkbox" id="${columnId}" data-column="${columnIndex}" ${this.visible() ? 'checked' : ''}> ${columnTitle}
                            </label>
                        </div>
                    `);
                });
            }
        }

    });

    $(document).on('click', '.go-del', function (e) {
        e.preventDefault();
        var companyid = $(this).attr('data-id');
        httpPostPath = config.url.delete + "/" + companyid;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete,'warning', httpPostPath, dTbl, data);
    });

    var CompanyAdd = function (config) {
        var t = this;
        t.config = config;
        t.content = $('section.content');
        t.mdl = t.content.find('#companyMdl');
        t.mdltitle = t.mdl.find('.modal-title');
        t.frm = t.mdl.find('#CompanyForm');
        t.modalInstance = new bootstrap.Modal(t.mdl[0]);
        
        t.frmEl = {};
        t.frmEl.name = t.frm.find('#name');
        t.frmEl.company_tag = t.frm.find('#company_tag');
        t.frmEl.logo = t.frm.find('#logo');
        t.frmEl.img = t.frm.find('#img');

        t.btn = {};
        t.btn.submit = t.frm.find('#btnSubmit');
        t.btn.clear = t.frm.find('#btnClear');
        t.httpCall = true;
        t.httpPostPath = "";
        t.resetFrm = {};
        t.deletedRecords = false;       



        // ========== VALIDATOR INITIALIZATION ==========
        $.validator.addMethod("str_name", function (value, element) {
            if ($.trim(value) === "") {
                return true;
            }
            return /^[A-Za-z0-9]+(?:[ -][A-Za-z0-9]+)*$/.test(value);
        }, t.config.translations.invalid_company_name);
        t.frmValidator = t.frm.validate({
            onsubmit: false,
            ignore: [],
            rules: {
                name: {
                    required: true,
                    maxlength: 30,
                    str_name: true   // custom validator – ensure it's defined globally
                },
                company_tag: {                    
                    maxlength: 20,
                    str_name: true
                },
                logo: { validLogo: true }
                
            },
            messages: {
                name: {
                    required: "Company name is required",
                    maxlength: "Name cannot exceed 30 characters"
                },
                company_tag: {                    
                    maxlength: "Tag cannot exceed 20 characters"
                },
                logo: { validLogo: "Please upload a JPG, JPEG or PNG file smaller than 10 MB" }
            },

            errorPlacement: function (error, element) {
                var errorWrap = getErrorWrap(element);
                if (errorWrap.length) {
                    error.appendTo(errorWrap);
                } else {
                    error.insertAfter(element.closest(".input-group, .amg-form-field-row"));
                }
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

        // ========== TRIGGER VALIDATION ON USER INPUT ==========
        t.frmEl.name.on("change keyup", function () {
            $(this).valid();
        });
        t.frmEl.company_tag.on("change keyup", function () {
            $(this).valid();
        });
        t.frmEl.logo.on("change", function () {
            $(this).valid();
        });

        // ========== EXISTING METHODS (unchanged except minor fixes) ==========
        t.addCompany = function (e) {
            e.preventDefault();
            t.frmEl.name.val('');
            t.frmEl.company_tag.val('');
            t.frmEl.logo.val('');
            t.frm.find('#display_img').hide();
            t.modalInstance = new bootstrap.Modal(t.mdl[0]);   
            t.modalInstance.show();                            
            t.frmEl.logo.off('change').on('change', function () {
                $(this).valid(); 
                let file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        t.frm.find('#display_img').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            });
            t.mdltitle.text(t.config.translations.add_company);
            t.btn.submit.text(t.config.translations.save_company);
            t.httpPostPath = t.config.url.add;
            t.resetFrm();
        };

        t.editCompany = function (e) {
            e.preventDefault();
            let accId = $(this).data("id");
            t.httpPostPath = t.config.url.edit + "/" + accId;
            $.get(t.config.url.get + "/" + accId)
                .done(function (data) {
                    if (data.status === "success") {
                        t.resetFrm();
                        t.mdltitle.text(t.config.translations.edit_company);
                        t.btn.submit.text(t.config.translations.save_changes);
                        t.frmEl.name.val(data.data.name);
                        t.frmEl.company_tag.val(data.data.company_tag);
                        if (data.data.logo) {
                            t.frm.find('#display_img').attr('src', t.config.base_url + data.data.logo).show();
                        }
                        t.modalInstance = new bootstrap.Modal(t.mdl[0]);
                        t.modalInstance.show();
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                })
                .fail(function () {
                    sweetAlert('center', 'error', config.translations.something_went_wrong);
                });
        };

        t.makeRequest = function(e){
            e.preventDefault();
            var reqId = $(this).attr("data-id");
            t.httpPostPath = t.config.url.request + "/" + reqId;
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlerts(config.translations.make_a_request, 'warning', t.httpPostPath, dTbl, data);
        };

        t.handlesubmit = function (e) {
            e.preventDefault();
            if (t.frmValidator.form() == false) {
                $('#img').hide();
                return false;
            }
            var frmData = new FormData();
            frmData.append('_token', t.config.token);
            frmData.append('name', t.frmEl.name.val());
            frmData.append('company_tag', t.frmEl.company_tag.val());
            if (t.frmEl.logo[0].files.length > 0) {
                frmData.append('logo', t.frmEl.logo[0].files[0]);
            }
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
                        if (t.modalInstance) {
                            t.modalInstance.hide();
                        }
                        sweetAlert('center', 'success', data);
                        $('#hand').hide();
                        dTbl.ajax.reload();
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.fail(function () {
                sweetAlert('center', 'error', config.translations.name_already_taken);
            });
            http.always(function () {
                $('#img').hide();
            });
        };

        t.restore = function (e) {
            e.preventDefault();
            var companyId = $(e.currentTarget).data("id");
            var httpPostPath = t.config.url.restore + "/" + companyId;
            var data = {
                msg: t.config.translations.something_went_wrong,
            };
            sweetAlerts(t.config.translations.are_you_restore, "question", httpPostPath, dTbl, data);
        };

        t.loadForm = function (objCompany) {
            t.resetFrm();
            t.frm.find("input[name='name']").val(objCompany.name);
            t.frm.find("input[name='company_tag']").val(objCompany.company_tag);
            if (objCompany.logo != '') {
                t.frm.find('#display_img').attr('src', t.config.base_url + objCompany.logo).show();
            }
            var modal = new bootstrap.Modal(t.mdl[0]);
            modal.show();
            t.modalInstance = modal;
        };

        t.resetFrm = function () {
            t.frmEl.name.val('');
            t.frmEl.company_tag.val('');
            t.frmEl.logo.val('');
            t.frm.find('#display_img').hide().attr('src', '');
            t.frmValidator.resetForm();
            // Clear any visible error messages
            t.frm.find('.amg-form-error-wrap').empty();
            t.frm.find('.input-group').removeClass('amg-form-invalid');
        };

        t.showDeleted = function (e) {
            e.preventDefault();
            deletedRecords = !deletedRecords;
            $(".non-deleted-icon").toggleClass("d-none");
            $(".deleted-icon").toggleClass("d-none");
            var title = deletedRecords ? t.config.translations.show_present_companies : t.config.translations.show_deleted_companies;
            $(e.currentTarget)
                .attr("data-bs-original-title", title)
                .attr("title", title);
            var tooltip = bootstrap.Tooltip.getInstance(e.currentTarget);
            if (tooltip) {
                tooltip.dispose();
            }
            new bootstrap.Tooltip(e.currentTarget);
            dTbl.ajax.reload();
        };

        t.reload = function () {
            $('[data-bs-toggle="tooltip"]').each(function () {
                var tooltip = bootstrap.Tooltip.getInstance(this);
                if (tooltip) {
                    tooltip.hide();
                    tooltip.dispose();
                }
            });
            dTbl.ajax.reload();
        };

        // ========== EVENT BINDINGS ==========
        t.content.on('click', '.open-add-modal', $.proxy(t.addCompany));
        t.content.on("click", ".btn-show-companies", $.proxy(t.showDeleted));
        t.content.on("click", ".go-restore", $.proxy(t.restore));
        t.content.on("click", '.dtActReq', $.proxy(t.makeRequest));
        t.content.on('click', '.open-edit-modal', $.proxy(t.editCompany));
        t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
        t.btn.submit.on('click', $.proxy(t.handlesubmit));

        dTbl.ajax.reload();
    };
    new CompanyAdd(config);
});