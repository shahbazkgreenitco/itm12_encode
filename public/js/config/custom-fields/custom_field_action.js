var CustomAction = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.table = t.content.find("#customAction");
    t.mdl = t.content.find("#addAction");
    t.modalElement = t.mdl[0];

    // Modal elements
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmitAction");
    t.mdl.frm = t.mdl.find("#addActionForm");
    t.mdl.frmEl = {
        action: t.mdl.frm.find("#action"),
        condition: t.mdl.frm.find("#condition"),
        no_of_day: t.mdl.frm.find("#no_of_day"),
        to_user: t.mdl.frm.find("#to_user"),
        content: t.mdl.frm.find("#contents"),
        template: t.mdl.frm.find("#template"),
        subject: t.mdl.frm.find("#subject"),
        action_id: t.mdl.frm.find("#action_id")
    };

    // Initially hide conditional sections
    $('.no-of-days').hide();
    $(".email_trigger_for_content").hide();

    

    // ========== Initialize Select2 ==========
    t.mdl.frmEl.action.select2({
        width: "100%",
        placeholder: 'Select Action',
        allowClear: true,
        dropdownParent: $('#addAction')
    });
    t.mdl.frmEl.condition.select2({
        width: "100%",
        placeholder: 'Select Condition',
        allowClear: true,
        dropdownParent: $('#addAction')
    });
    t.mdl.frmEl.template.select2({
        width: "100%",
        placeholder: 'Select Defined Template',
        allowClear: true,
        dropdownParent: $('#addAction')
    });
    t.mdl.frmEl.to_user.select2({
        width: "100%",
        placeholder: 'Select User',
        allowClear: true,
        dropdownParent: $('#addAction')
    });

    // Populate To User dropdown with users from config
    function populateToUserOptions(selectedId = null) {
        t.mdl.frmEl.to_user.empty().append('<option value=""></option>');
        if (config.users && config.users.length) {
            $.each(config.users, function(i, user) {
                var option = new Option(user.text, user.id, false, (selectedId == user.id));
                t.mdl.frmEl.to_user.append(option);
            });
        }
        t.mdl.frmEl.to_user.trigger('change');
    }

    // ========== Initialize Summernote ==========
    t.mdl.frmEl.content.summernote({
        height: 200,
        placeholder: 'Write email content here...',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

     t.tblHelpers = {        
        action: function () {
            return function (d) {
                var a = [];
                if (typeof t.config.actions == "object")
                {
                    $.each(t.config.actions, function (i, v) {
                        if (v.id == d) {
                            a.push(v.name);
                        }
                    });
                }
                return a.join("");
            };
        },
        condition: function () {
            return function (d) {
                var a = [];
                if (typeof t.config.conditions == "object")
                {
                    $.each(t.config.conditions, function (i, v) {
                        if (v.id == d) {
                            a.push(v.name);
                        }
                    });
                }
                return a.join("");
            };
        },
    };

    // ========== DataTable ==========
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        dom: 'lrtip',
        colResize: { resizeTable: true },
        responsive: true,
        stateSave: true,
        stateSaveParams: function (settings, data) {
            data.search.search = '';
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: config.url.getCustomActions + "/" + t.config.id,
            type: "POST",
            data: function (d) {
                d._token = config.token;
                // Transform search: controller expects a string, not an object
                if (d.search && typeof d.search === 'object') {
                    d.search = d.search.value || '';
                } else if (!d.search) {
                    d.search = '';
                }
                
                if (d.order && d.order.length > 0) {
                    let colIndex = d.order[0].column;
                    let newIndex = null;
                    if (colIndex === 0) newIndex = 1;
                    else if (colIndex === 1) newIndex = 2;
                    else if (colIndex === 2) newIndex = 3;
                    if (newIndex !== null) {
                        d.order[0].column = newIndex;
                    } else {
                        delete d.order;
                    }
                }
            }
        },
        fixedColumns: {
            rightColumns: 1
        },
        columns: [
            { data: "action" },
            { data: "condition" },
            { data: "no_of_day" },
            { data: "id", name: "id", orderable: false, searchable: false }
        ],
        columnDefs: [
            {
                targets: 3,
                orderable: false,
                searchable: false,
                className: "users-col-actions dtfc-fixed-right",
                width: "100px",
                render: function (data, type, row) {
                    let buttons = '';
                    
                        buttons += `<button class="user-list-action-btn me-1 dtActEdit" data-id="${data}" title="Edit">
                                        <svg viewBox="0 0 16 16" fill="none" width="16" height="16">
                                            <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                                        </svg>
                                    </button>`;
                   
                        buttons += `<button class="user-list-action-btn is-delete me-1 dtActDel" data-id="${data}" title="Delete">
                                        <svg viewBox="0 0 15 17" fill="none" width="16" height="16">
                                            <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                                        </svg>
                                    </button>`;
                    
                    return buttons;
                }
            },
             {
                targets: 0,
                render: t.tblHelpers.action(),
            },
            {
                targets: 1,
                render: t.tblHelpers.condition(),
            },
        ],
        order: [[0, 'desc']], 
        fnInitComplete: function () {
            var api = this.api();
            $('#customAction_length').hide();
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });

            $('#custom-list-search').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    let value = $(this).val().trim();
                    api.search(value).draw();
                }
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
            updateColumnVisibilityControls();
            $('#columnVisibilityControls').on('change', 'input[type="checkbox"]', function () {
                api.column(+$(this).data('column')).visible(this.checked);
            });
            api.on('column-reorder', () => setTimeout(updateColumnVisibilityControls, 10));
        }
    });

    t.reload = function() {
        let searchVal = $('#custom-list-search').val().trim();

        if (searchVal) {
            t.dTbl.search(searchVal).draw();
        } else {
            t.dTbl.search('').draw();
        }
    };

    // ========== Form Validation (conditional) ==========
    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: [],
        rules: {
            action: { required: true },
            condition: { required: true },
            no_of_day: {
                required: {
                    depends: function() {
                        var condVal = t.mdl.frmEl.condition.val();
                        return condVal && condVal != '1';
                    }
                }
            },
            to_user: {
                required: {
                    depends: function() {
                        var actionVal = t.mdl.frmEl.action.val();
                        var templateVal = t.mdl.frmEl.template.val();
                        return actionVal == '2' && !templateVal;
                    }
                }
            },
            subject: {
                required: {
                    depends: function() {
                        var actionVal = t.mdl.frmEl.action.val();
                        var templateVal = t.mdl.frmEl.template.val();
                        return actionVal == '2' && !templateVal;
                    }
                },
                clean_text_only: true
            },
            content: {
                required: {
                    depends: function() {
                        var actionVal = t.mdl.frmEl.action.val();
                        var templateVal = t.mdl.frmEl.template.val();
                        return actionVal == '2' && !templateVal;
                    }
                }
            }
        },
        messages: {
            action: "Please select an action",
            condition: "Please select a condition",
            no_of_day: "Please enter number of days",
            to_user: "Please select a user",
            subject: "Please enter a subject",
            content: "Please enter email content"
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

    // ========== Helper to show/hide sections ==========
    function toggleSections() {
        var actionVal = t.mdl.frmEl.action.val();
        var conditionVal = t.mdl.frmEl.condition.val();
        var templateVal = t.mdl.frmEl.template.val();

        if (conditionVal && conditionVal != 1) {
            $('.no-of-days').show();
        } else {
            $('.no-of-days').hide();
        }

        if (actionVal == 2) {
            $(".email_trigger_for_content").show();
            if (templateVal) {
                $('#email_trigger_part').hide();
            } else {
                $('#email_trigger_part').show();
            }
        } else {
            $(".email_trigger_for_content").hide();
        }
    }

    // ========== Modal Control (Bootstrap 5) ==========
    function showModal() {
        if (!t.modalElement) return;
        let modal = bootstrap.Modal.getOrCreateInstance(t.modalElement);
        modal.show();
    }
    function hideModal() {
        if (!t.modalElement) return;
        let modal = bootstrap.Modal.getInstance(t.modalElement);
        if (modal) modal.hide();
    }

    // ========== Reset form and validation ==========
    function resetFormAndValidation() {
        t.mdl.frm[0].reset();
        t.frmValidator.resetForm();
        t.mdl.frmEl.content.summernote('code', '');
        t.mdl.frmEl.action.val("").trigger('change');
        t.mdl.frmEl.condition.val("").trigger('change');
        t.mdl.frmEl.template.val("").trigger('change');
        t.mdl.frmEl.to_user.val("").trigger('change');
        t.mdl.frmEl.no_of_day.val("");
        t.mdl.frmEl.subject.val("");
        t.mdl.frmEl.action_id.val("");
        $('.no-of-days').hide();
        $(".email_trigger_for_content").hide();
        $('#email_trigger_part').show();      
    }

    // ========== Modal close event ==========
    t.mdl.on('hidden.bs.modal', function() {
        resetFormAndValidation();
    });

    // ========== Add Action ==========
    t.addAction = function(e) {
        e.preventDefault();
        resetFormAndValidation();
        t.mdl.title.html(config.translations.add_custom_action);
        t.mdl.btnSubmit.text(config.translations.save);
        showModal();
    };

    // ========== Save / Update ==========
    t.saveForm = function(e) {
        e.preventDefault();
        if (!t.frmValidator.form()) return false;

        var formData = t.mdl.frm.serialize();
        var url = t.mdl.frmEl.action_id.val() ? t.config.url.update + "/" + t.mdl.frmEl.action_id.val() : t.config.url.add;

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            success: function(data) {
                if (data.status == "success") {
                    hideModal();
                    sweetAlert('center', 'success', data);
                    t.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            error: function() { alert(config.translations.something_went_wrong); }
        });
    };

    // ========== Edit ==========
    t.loadEditForm = function(e) {
        e.preventDefault();
        var Id = $(e.currentTarget).attr("data-id");
        $.get(t.config.url.getCustomAction + "/" + Id, function(data) {
            if (data.status == "success") {
                var item = data.data;
                resetFormAndValidation();
                t.mdl.frmEl.action.val(item.action).trigger('change');
                t.mdl.frmEl.condition.val(item.condition).trigger('change');
                t.mdl.frmEl.no_of_day.val(item.no_of_day);
                t.mdl.frmEl.action_id.val(item.id);
                t.mdl.title.html(config.translations.edit_custom_action);
                t.mdl.btnSubmit.text(config.translations.update);

                if (item.mail_trigger_content) {
                    var contentData = JSON.parse(item.mail_trigger_content);
                    if (contentData.definedTemplate) {
                        t.mdl.frmEl.template.val(contentData.definedTemplate).trigger('change');
                    } else {
                        t.mdl.frmEl.template.val("").trigger('change');
                        populateToUserOptions(contentData.to ? contentData.to.split(',')[2] : null);
                        t.mdl.frmEl.subject.val(contentData.subject || '');
                        t.mdl.frmEl.content.summernote('code', contentData.content || '');
                    }
                }
                toggleSections();
                showModal();
            } else {
                sweetAlert('center', 'error', data);
            }
        }).fail(function() { alert(config.translations.something_went_wrong); });
    };

    // ========== Delete ==========
    t.deleteCustomAction = function(e) {
        e.preventDefault();
        var Id = $(e.currentTarget).attr("data-id");
        sweetAlertConfirmation({
            message: config.translations.are_you_delete,
            onConfirm: function() {
                $.get(t.config.url.delete + "/" + Id, function(data) {
                    if (data.status == "success") {
                        sweetAlert('center', 'success', data);
                        t.reload();
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                }).fail(function() { alert(config.translations.something_went_wrong); });
            }
        });
    };

    // ========== Event Binding ==========
    t.content.on("click", ".add_custom_action", t.addAction);
    t.content.on("click", "#btnSubmitAction", t.saveForm);
    t.content.on("click", ".dtActEdit", t.loadEditForm);
    t.content.on("click", ".dtActDel", t.deleteCustomAction);
    t.content.on('click', '.btn-reload-list',t.reload);
    t.content.on("change", "#action, #condition, #template", toggleSections);
};