var CustomField = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');

    // ----- DataTables -----
    t.dTblFieldset = null;
    t.dTblFields = null;

    // ----- Modals (Bootstrap 5 instances) -----
    t.fieldSetModalEl = document.getElementById('custom-fieldset-mdl');
    t.customFieldModalEl = document.getElementById('custom-field-mdl');
    t.fieldSetModal = null;
    t.customFieldModal = null;

    // ----- Forms -----
    t.frmFieldSet = $('#CustomFieldSetForm');
    t.frmCustomField = $('#CustomFieldForm');

    // ----- Form Validators -----
    t.frmValidatorFieldSet = null;
    t.frmValidatorCustomField = null;

    // ----- UI Elements -----
    t.btn = {
        addFieldSet: t.content.find('.btn-add-fieldset'),
        addField: t.content.find('.btn-add-fields'),
        submitFieldSet: t.frmFieldSet.find('#btnSubmitFieldSet'),
        submitCustomField: t.frmCustomField.find('#btnSubmit'),
        reloadFieldset: null,
        reloadFields: null
    };
    // ========== Modal helpers ==========
    function showModal(modalElement) {
        if (!modalElement) return;
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (!modal) modal = new bootstrap.Modal(modalElement);
        modal.show();
        return modal;
    }
    function hideModal(modalElement) {
        if (!modalElement) return;
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
    }

    // ========== Update Format dropdown (and toggle Format column visibility) ==========
    function updateFormatOptions(elementType, $targetForm) {
        var $formatSelect = $targetForm.find('#format');
        if (!$formatSelect.length) return;

        var currentVal = $formatSelect.val();
        $formatSelect.empty();
        $formatSelect.append('<option value="">Select the Format</option>');

        var options = [];
        var showFormat = true;

        if (!elementType || elementType === '') {
            options = ['ANY', 'ALPHA', 'NUMERIC', 'DATE', 'TIME', 'DATETIME', 'MAC', 'IP', 'custom'];
        } else {
            switch (elementType) {
                case 'text':
                    options = ['ANY', 'ALPHA', 'NUMERIC', 'MAC', 'IP', 'custom'];
                    break;
                case 'date':
                    options = ['DATE', 'custom'];
                    break;
                case 'time':
                    options = ['TIME', 'custom'];
                    break;
                case 'datetime':
                    options = ['DATETIME', 'custom'];
                    break;
                default:
                    options = [];
                    showFormat = false;
                    break;
            }
        }

        $.each(options, function (i, opt) {
            var selected = (currentVal === opt) ? 'selected' : '';
            $formatSelect.append('<option value="' + opt + '" ' + selected + '>' + opt + '</option>');
        });

        if ($formatSelect.hasClass('select2-hidden-accessible')) {
            $formatSelect.select2('destroy').select2({ width: '100%', dropdownParent: $targetForm.closest('.modal') });
        }
        $formatSelect.trigger('change');

        // Hide/show only the Format column (first column of .formatdiv row)
        var $formatCol = $targetForm.find('.formatdiv .col-md-6:first');
        if (showFormat && options.length > 0) {
            $formatCol.show();
        } else {
            $formatCol.hide();
        }
        // Field Type column (second column) always remains visible
    }
    t.updateFormatOptions = updateFormatOptions;

    // ========== Update Option Type dropdown based on Form Element ==========
    function updateOptionTypeOptions(elementType, $targetForm) {
        var $optionTypeSelect = $targetForm.find('#optionType');
        if (!$optionTypeSelect.length) return;
        $optionTypeSelect.empty();

        if (elementType === 'dropdown') {
            $optionTypeSelect.append('<option value="">Select</option>');
            $optionTypeSelect.append('<option value="1">Predefined</option>');
            $optionTypeSelect.append('<option value="2">Custom</option>');
        } else if (elementType === 'radio' || elementType === 'checkbox') {
            $optionTypeSelect.append('<option value="">Select</option>');
            $optionTypeSelect.append('<option value="2">Custom</option>');
        } else {
            $optionTypeSelect.empty();
        }

        if ($optionTypeSelect.hasClass('select2-hidden-accessible')) {
            $optionTypeSelect.select2('destroy').select2({ width: '100%', dropdownParent: $targetForm.closest('.modal') });
        }
        $optionTypeSelect.trigger('change');
    }
    t.updateOptionTypeOptions = updateOptionTypeOptions;

    // ----- Table action helpers -----
    t.tblHelpers = {
        fieldsetActions: function () {
            return function (d) {
                var buttons = [];
                if (jQuery.inArray("CustomFieldDelete", t.config.permissions) !== -1) {
                    buttons.push(`
                        <button class="user-list-action-btn dtActDelFieldSet" title="Delete" data-id="${d.id}" ${d.deletable ? 'disabled' : ''}>
                           <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                        </button>
                    `);
                }
                return `<div class="d-flex gap-1">${buttons.join('')}</div>`;
            };
        },
        fieldsActions: function () {
            return function (d) {
                var buttons = [];
                if (!d.deleted_at && jQuery.inArray("CustomFieldDelete", t.config.permissions) !== -1 && d.deletable) {
                    buttons.push(`
                        <button class="user-list-action-btn open-edit-modal me-1" data-toggle="tooltip" data-placement="top" title="${t.config.translations.edit_field}" data-id="${d.id}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"/></svg>
                        </button>
                    `);
                }
                if (!d.deleted_at && jQuery.inArray("CustomFieldDelete", t.config.permissions) !== -1) {
                    buttons.push(`
                        <button class="user-list-action-btn dtActDelField me-1" data-toggle="tooltip" data-placement="top" title="${t.config.translations.delete_field_tooltip}" data-id="${d.id}" ${d.deletable ? '' : 'disabled'}>
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                        </button>
                    `);
                }
                buttons.push(`
                    <button class="user-list-action-btn customActionList" data-toggle="tooltip" data-placement="top" title="${t.config.translations.custom_action}" data-id="${d.id}">
                        <i class="bi bi-list-task"></i>
                    </button>
                `);
                return `<div class="d-flex justify-content-center">${buttons.join('')}</div>`;
            };
        }
    };

    // ----- Initialize DataTables with search/reload (matching AllocationType pattern) -----
    function initDataTables() {
        // Field Sets table
        t.dTblFieldset = $('#fieldsets').DataTable({
            colReorder: true,
            processing: true,
            // dom: 'drtip',
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            order: [[0, 'asc']],
            ajax: {
                url: t.config.url.fieldsetlist,
                type: "get",
                data: function (d) {
                    d._token = t.config.token;
                }
            },
            columns: [
                {
                    data: 'a.name', render: function (data, type, row) {
                        return `<a href="${t.config.url.fieldsetdtl}/${row.a.id}">${data}</a>`;
                    }
                },
                { data: 'a.qty' },
                { data: 'a.usedTicketType', render: function (data) { return data ? `<span>${data}</span>` : '-'; } },
                {
                    data: 'a.usedModels', render: function (data) {
                        let html = [];
                        if (data) $.each(data, function (i, v) { html.push(`<a target="_blank" href="/model/edit/${v.id}">${v.name}</a>`); });
                        return html.length ? html.join(", ") : '-';
                    }
                },
                {
                    data: 'a.usedModels', render: function (data) {
                        let html = [];
                        if (data) $.each(data, function (i, v) { if (v.category) html.push(`<span>${v.category.name}</span>`); });
                        return html.length ? html.join(", ") : '-';
                    }
                },
                { data: 'a.fieldSetType', render: function (data) { return data ? `<span>${data}</span>` : '-'; } },
                { data: 'a', orderable: false, searchable: false, width: "10%", render: t.tblHelpers.fieldsetActions() }
            ],
            fnInitComplete: function () {
                var api = this.api();
                $('#fieldset-searchbox').on('keyup', function (e) {
                    if (e.keyCode === 13) {
                        let v = $(this).val().trim();
                        if (v === '') t.reloadFieldset();
                        else api.search(v).draw();
                    }
                });
                $('.fieldset-btn-search').on('click', function (e) {
                    e.preventDefault();
                    let v = $('#fieldset-searchbox').val().trim();
                    if (v === '') t.reloadFieldset();
                    else api.search(v).draw();
                });
                $('.fieldset-btn-reload').on('click', function (e) {
                    e.preventDefault();
                    t.reloadFieldset();
                });
                $('#fieldset-page-length').on('change', function () {
                    api.page.len(parseInt($(this).val())).draw();
                });
            }
        });

        // Fields table
        t.dTblFields = $('#fieldsTable').DataTable({
            colReorder: true,
            // dom: 'drtip',
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            processing: true,
            aoColumnDefs: [
                { bSortable: false, aTargets: [5] },
                { targets: 5, render: t.tblHelpers.fieldsActions() },
                {
                    targets: 3, render: function (d) {
                        var a = [];
                        $.each(d.fieldset, function (index, value) {
                            a.push(`<span><a href="${t.config.url.fieldsetdtl}/${value.id}">${value.name}</a></span>`);
                        });
                        return a.join(" , ");
                    }
                }
            ],
            order: [[0, 'desc']],
            ajax: {
                url: t.config.url.list,
                type: "get",
                data: function (d) {
                    d._token = t.config.token;
                }
            },
            columns: [
                { data: 'a.name' },
                { data: 'a', render: function (data) { return data.format === false ? 'CUSTOM FORMAT' : data.format; } },
                { data: 'a.element' },
                { data: 'a' },
                { data: 'a.fieldType' },
                { data: 'a', width: "5%" }
            ],
            fnInitComplete: function () {
                var api = this.api();
                $('#fields-searchbox').on('keyup', function (e) {
                    if (e.keyCode === 13) {
                        let v = $(this).val().trim();
                        if (v === '') t.reloadFields();
                        else api.search(v).draw();
                    }
                });
                $('#fields-btn-search').on('click', function (e) {
                    e.preventDefault();
                    let v = $('#fields-searchbox').val().trim();
                    if (v === '') t.reloadFields();
                    else api.search(v).draw();
                });
                $('.fields-btn-reload').on('click', function (e) {
                    e.preventDefault();
                    t.reloadFields();
                });
                $('#fields-page-length').on('change', function () {
                    api.page.len(parseInt($(this).val())).draw();
                });
            }
        });
    }

    // ----- Reload methods -----
    t.reloadFieldset = function () {
        if (t.dTblFieldset) {
            $('#fieldset-searchbox').val('');
            t.dTblFieldset.search('').ajax.reload(null, false);
        }
    };
    t.reloadFields = function () {
        if (t.dTblFields) {
            $('#fields-searchbox').val('');
            t.dTblFields.search('').ajax.reload(null, false);
        }
    };

    // ----- Summernote initialization -----
    function initSummernote() {
        $('#help_note, #edit_help_note').summernote({
            placeholder: t.config.translations.comment_summer,
            toolbar: [['color', ['color']], ['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol']]],
            minHeight: 200,
            width:'100%',
            focus: true
        });
    }

    // ----- Form validators (with fixed field IDs) -----
    function initValidators() {
        // Field Set Validator
        t.frmValidatorFieldSet = t.frmFieldSet.validate({
            onsubmit: false,
            ignore: [],
            rules: {
                name: { required: true, clean_text_only: true, maxlength: 255 },
                custom_field_set_types: { required: true }
            },
            messages: {
                name: "Field set name is required",
                custom_field_set_types: "Please select a field set type"
            },
            errorPlacement: function (error, element) {
                var errorWrap = getErrorWrap(element);
                if (errorWrap.length) error.appendTo(errorWrap);
                else error.insertAfter(element.closest(".input-group"));
                updateValidationState(element, true);
            },
            highlight: function (element) { updateValidationState($(element), true); },
            unhighlight: function (element) { updateValidationState($(element), false); },
            invalidHandler: function (event, validator) {
                if (validator.numberOfInvalids()) {
                    validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            }
        });

        // Custom Field Validator (Add/Edit modal)
        t.frmValidatorCustomField = t.frmCustomField.validate({
            onsubmit: false,
            ignore: [],
            rules: {
                name: { required: true, clean_text_only: true, maxlength: 255 },
                element: { required: true },
                format: {
                    required: function () {
                        var type = $('#fieldType').val();
                        return ['text', 'time', 'date', 'datetime'].includes(type);
                    }
                },
                custom_field_types: { required: true },
                optionType: {
                    required: function () {
                        var type = $('#fieldType').val();
                        return (type === 'dropdown' || type === 'radio' || type === 'checkbox');
                    }
                }
            },
            messages: {
                name: "Field name is required",
                element: "Please select a form element",
                format: "Format is required for this field type",
                custom_field_types: "Please select the fields type",
                optionType: "Option type is required for dropdown/radio/checkbox"
            },
            errorPlacement: function (error, element) {
                var errorWrap = getErrorWrap(element);
                if (errorWrap.length) error.appendTo(errorWrap);
                else error.insertAfter(element.closest(".input-group"));
                updateValidationState(element, true);
            },
            highlight: function (element) { updateValidationState($(element), true); },
            unhighlight: function (element) { updateValidationState($(element), false); },
            invalidHandler: function (event, validator) {
                if (validator.numberOfInvalids()) {
                    validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
                }
            }
        });
    }

    // ----- Reset forms -----
    t.resetFieldSetForm = function () {
        t.frmFieldSet[0].reset();
        t.frmFieldSet.find('label.error').remove();
        $('#custom_field_set_types').val(null).trigger('change');
        $('.custom-fieldset-extra').addClass('d-none');
        $('#fieldset_for_ticket_type').val('null').trigger('change');
        $('#fieldset_for_ticket_type_status').val('null').trigger('change');
        if (t.frmValidatorFieldSet && typeof t.frmValidatorFieldSet.resetForm === 'function') {
            t.frmValidatorFieldSet.resetForm();
        }
    };

    t.resetCustomFieldForm = function (isEditMode = false) {
        t.frmCustomField[0].reset();
        t.frmCustomField.find('label.error').remove();
        $('#fieldType').val(null).trigger('change');
        $('#custom_field_types').val(null).trigger('change');
        $('.dropDownOptionDiv, .radioCheckOptionDiv').addClass('hide');
        $('.optionElementDiv').remove();
        $('.custom_format--div').addClass('d-none');
        $('#custom_format').val('');
        // Only clear hidden field when not in edit mode
        if (!isEditMode) $('#_field_id').val('');
        $('.formatdiv .col-md-6:first').show();
        t.updateFormatOptions('', t.frmCustomField);
        t.updateOptionTypeOptions('', t.frmCustomField);
        if (t.frmValidatorCustomField) t.frmValidatorCustomField.resetForm();
        if (!isEditMode) {
            $('#custom-field-mdl .modal-title').text(t.config.translations.add_custom_field || 'Add Custom Field');
            t.btn.submitCustomField.text('Save');
        }
    };

    // ----- CRUD operations -----
    t.createOrUpdateFieldSet = function (e) {
        e.preventDefault();
        if (!t.frmFieldSet.valid()) return;
        if (t.requestOngoing) return;
        t.requestOngoing = true;
        t.btn.submitFieldSet.prop('disabled', true).text('Please wait...');
        $.ajax({
            url: t.config.url.addFieldset,
            type: 'POST',
            data: t.frmFieldSet.serialize(),
            success: function (data) {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', text: data.msg, timer: 2000, showConfirmButton: false });
                    hideModal(t.fieldSetModalEl);
                    t.dTblFieldset.ajax.reload();
                    t.resetFieldSetForm();
                } else {
                    if (data.errors) {
                        $.each(data.errors, function (index, value) {
                            $("[name='" + index + "']").parent('div').after('<label class="error">' + value + '</label>');
                        });
                    } else {
                        Swal.fire({ icon: 'error', text: data.msg || t.config.translations.something_went_wrong });
                    }
                }
            },
            error: function () { Swal.fire({ icon: 'error', text: t.config.translations.something_went_wrong }); },
            complete: function () { t.btn.submitFieldSet.prop('disabled', false).text('Save'); t.requestOngoing = false; }
        });
    };

    
    t.createOrUpdateCustomField = function (e) {
        e.preventDefault();
        if (!t.frmCustomField.valid()) return;
        if (t.requestOngoing) return;
        t.requestOngoing = true;
        var btn = t.btn.submitCustomField;
        var originalText = btn.text();
        btn.prop('disabled', true).text('Please wait...');

        var fieldId = $('#_field_id').val();
        var isEdit = (fieldId && fieldId !== '');
        var url = isEdit ? t.config.url.edit + '/' + fieldId : t.config.url.addField;

        var formData = new FormData();
        formData.append('_token', t.config.token);

        if (isEdit) {
            formData.append('_method', 'PUT');
            formData.append('fieldName', $('#name').val());
            formData.append('fieldType', $('#fieldType').val());
            formData.append('custom_field_types', $('#custom_field_types').val());
            formData.append('help_note', $('#help_note').val());
            formData.append('format', $('#format').val());
            formData.append('preDefinedOptions', $('#preDefinedOptions').val());
            var optionTypeVal = $('#optionType').val();
            if (optionTypeVal === '1' || optionTypeVal === '2') {
                formData.append('option_type', parseInt(optionTypeVal, 10));

                if (optionTypeVal === '1') {
                    formData.append('preDefinedOptions', $('#preDefinedOptions').val());
                } else if (optionTypeVal === '2') {
                    var customOptions = [];
                    $('input[name="customOptions[]"]').each(function () {
                        var val = $(this).val();
                        if (val) customOptions.push(val);
                    });
                    if (customOptions.length > 0) {
                        $.each(customOptions, function (i, opt) {
                            formData.append('customOptions[]', opt);
                        });
                    }
                }
            } else {
                formData.append('option_type', '');
            }

            if ($('#custom_format').val()) {
                formData.append('custom_format', $('#custom_format').val());
            }
        } else {
            formData.append('name', $('#name').val());
            formData.append('element', $('#fieldType').val());
            formData.append('custom_field_types', $('#custom_field_types').val());
            formData.append('help_note', $('#help_note').val());
            formData.append('format', $('#format').val());

            var optionType = $('#optionType').val();
            if (optionType) {
                formData.append('optionType', optionType);  // camelCase
                if (optionType === '1') {
                    formData.append('preDefinedOptions', $('#preDefinedOptions').val());
                } else if (optionType === '2') {
                    var customOptions = [];
                    $('input[name="customOptions[]"]').each(function () {
                        var val = $(this).val();
                        if (val) customOptions.push(val);
                    });
                    if (customOptions.length > 0) {
                        $.each(customOptions, function (i, opt) {
                            formData.append('customOptions[]', opt);
                        });
                    }
                }
            }

            if ($('#custom_format').val()) {
                formData.append('custom_format', $('#custom_format').val());
            }
        }

        $.ajax({
            url: url,
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', text: data.msg, timer: 2000, showConfirmButton: false });
                    hideModal(t.customFieldModalEl);
                    t.dTblFields.ajax.reload();
                    t.resetCustomFieldForm();
                } else {
                    if (data.errors) {
                        $.each(data.errors, function (index, value) {
                            $("[name='" + index + "']").parent('div').after('<label class="error">' + value + '</label>');
                        });
                    } else {
                        Swal.fire({ icon: 'error', text: data.msg || t.config.translations.something_went_wrong });
                    }
                }
            },
            error: function (xhr) {
                console.error('AJAX Error:', xhr);
                Swal.fire({ icon: 'error', text: t.config.translations.something_went_wrong });
            },
            complete: function () {
                btn.prop('disabled', false).text(originalText);
                t.requestOngoing = false;
            }
        });
    };
    t.deleteFieldSet = function (e) {
        var id = $(e.currentTarget).attr('data-id');
        Swal.fire({
            title: t.config.translations.delete_field_set,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: t.config.url.delfildsetUrl + '/' + id,
                    type: 'GET',
                    data: { _token: t.config.token },
                    success: function (data) {
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', text: data.msg, timer: 2000, showConfirmButton: false });
                            t.dTblFieldset.ajax.reload();
                        } else {
                            Swal.fire({ icon: 'error', text: data.msg || t.config.translations.something_went_wrong });
                        }
                    },
                    error: function () { Swal.fire({ icon: 'error', text: t.config.translations.something_went_wrong }); }
                });
            }
        });
    };

    t.deleteField = function (e) {
        var id = $(e.currentTarget).attr('data-id');
        Swal.fire({
            title: t.config.translations.delete_field,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: t.config.url.delfildUrl + '/' + id,
                    type: 'GET',
                    data: { _token: t.config.token },
                    success: function (data) {
                        if (data.status === 'success') {
                            Swal.fire({ icon: 'success', text: data.msg, timer: 2000, showConfirmButton: false });
                            t.dTblFields.ajax.reload();
                        } else {
                            Swal.fire({ icon: 'error', text: data.msg || t.config.translations.something_went_wrong });
                        }
                    },
                    error: function () { Swal.fire({ icon: 'error', text: t.config.translations.something_went_wrong }); }
                });
            }
        });
    };

    t.editField = function (e) {
        e.preventDefault();
        var id = $(e.currentTarget).attr('data-id');
        $.get(t.config.url.get + '/' + id)
            .done(function (data) {
                if (data.status === 'success') {
                    t.resetCustomFieldForm(true); // true = edit mode (preserves hidden field)
                    var obj = data.data;
                    $('#name').val(obj.name);
                    $('#fieldType').val(obj.element).trigger('change');
                    t.updateOptionTypeOptions(obj.element, t.frmCustomField);
                    if (obj.option_type > 0) {
                        $('#optionType').val(obj.option_type).trigger('change');
                        if (obj.option_type == 1) $('#custom-field-mdl .predefinedOptioDiv').removeClass('d-none');
                        else $('#custom-field-mdl .customOptionDiv').removeClass('d-none');
                    } else if (obj.option_type == 'null' || obj.option_type == null) {
                        $('#optionType').val(null).trigger('change.select2');
                    }
                    if (obj.preDefinedOptions > 0) $('#preDefinedOptions').val(obj.preDefinedOptions).trigger('change');
                    if (obj.format === false) $('#format').val('custom').trigger('change');
                    else if (obj.format) $('#format').val(obj.format).trigger('change');
                    if (obj.custom_format) $('#custom_format').val(obj.custom_format);
                    $('#custom-field-mdl .customOptionsHolders').empty();
                    if (obj.custom_options) {
                        var opts = typeof obj.custom_options === 'string' ? JSON.parse(obj.custom_options) : obj.custom_options;
                        if (Array.isArray(opts)) opts.forEach(opt => t.addOptionDiv(opt));
                    }
                    if (obj.custom_label) {
                        var labels = typeof obj.custom_label === 'string' ? JSON.parse(obj.custom_label) : obj.custom_label;
                        if (Array.isArray(labels)) labels.forEach(label => t.addOptionDiv(label));
                    }
                    if (obj.custom_field_types > 0) $('#custom_field_types').val(obj.custom_field_types).trigger('change');
                    $('#help_note').val(obj.help_note).summernote('code', obj.help_note);
                    // Set hidden field ID for update
                    $('#_field_id').val(obj.id);
                    $('#custom-field-mdl .modal-title').text('Edit Custom Field');
                    t.btn.submitCustomField.text('Update');
                    showModal(t.customFieldModalEl);
                } else {
                    Swal.fire({ icon: 'error', text: data.msg || t.config.translations.something_went_wrong });
                }
            })
            .fail(function () { Swal.fire({ icon: 'error', text: t.config.translations.something_went_wrong }); });
    };

    t.addOptionDiv = function (val) {
        val = typeof val === 'string' ? val : '';
        var html = `<div class="row optionElementDiv mt-2">
                        <div class="col-md-10"><input name="customOptions[]" type="text" class="form-control" value="${escapeHtml(val)}"/></div>
                        <div class="col-md-2"><button type="button" class="btn btn-danger cancelCustomOption">-</button></div>
                    </div>`;
        $('#custom-field-mdl .customOptionsHolders').append(html);
    };

    function escapeHtml(str) {
        return str.replace(/[&<>]/g, function (m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    t.customActionList = function (e) {
        var id = $(e.currentTarget).attr('data-id');
        window.open(t.config.url.customAction + '/' + id);
    };

    // ----- Event binding -----
    function bindEvents() {
        t.content.on('click', '.customActionList', $.proxy(t.customActionList, t));
        t.content.on('click', '.btn-add-fields', function (e) {
            e.preventDefault();
            t.resetCustomFieldForm(false);
            showModal(t.customFieldModalEl);
        });
        t.content.on('click', '.open-edit-modal', $.proxy(t.editField));
        t.content.on('click', '.dtActDelFieldSet', $.proxy(t.deleteFieldSet, t));
        t.content.on('click', '.dtActDelField', $.proxy(t.deleteField, t));
        $(document).on('click', '.btn-add-fieldset', $.proxy(t.showAddFieldSetModal, t));
        $(document).on('click', '#btnSubmitFieldSet', $.proxy(t.createOrUpdateFieldSet, t));
        $(document).on('click', '#btnSubmit', $.proxy(t.createOrUpdateCustomField));

        $(document).on('click', '.addOptionDiv', function () {
            let html = `<div class="row optionElementDiv mt-2"><div class="col-md-10"><input name="customOptions[]" type="text" class="form-control" /></div><div class="col-md-2"><button type="button" class="btn btn-danger cancelCustomOption">-</button></div></div>`;
            $('#custom-field-mdl .customOptionsHolders').append(html);
        });
        $(document).on('click', '.cancelCustomOption', function () {
            $(this).closest('.optionElementDiv').remove();
        });

        $('#fieldType').on('change', function () {
            var val = $(this).val();
            var $form = t.frmCustomField;
            $form.find(".optionElementDiv").remove();
            $form.find("#optionType").val("").trigger('change');
            $form.find("#preDefinedOptions").val("").trigger('change');
            $('.custom_format--div').addClass('d-none');
            $('#custom_format').val('');
            if (val === 'dropdown' || val === 'radio' || val === 'checkbox') {
                $form.find('.optionTypeDiv').removeClass('d-none');
            } else {
                $form.find('.optionTypeDiv').addClass('d-none');
            }
            t.updateFormatOptions(val, $form);
            t.updateOptionTypeOptions(val, $form);
        });

        $('#fieldType, #custom_field_types, #format, #optionType').on('change', function () {
            let element = $(this);

            element.valid(); // trigger validation

            element.closest('.form-group, .col-md-6, .col-12')
                .find('label.error')
                .remove();

            element.removeClass('error');
        });

        $('#custom-field-mdl').on('change', '#optionType', function () {
            let modal = $('#custom-field-mdl');
            if ($(this).val() === '1') {
                modal.find('.customOptionDiv').addClass('d-none');
                modal.find('.predefinedOptioDiv').removeClass('d-none');
            } else if ($(this).val() === '2') {
                modal.find('.predefinedOptioDiv').addClass('d-none');
                modal.find('.customOptionDiv').removeClass('d-none');
            } else {
                modal.find('.customOptionDiv, .predefinedOptioDiv').addClass('d-none');
            }
        });

        $('#format').on('change', function () {
            if ($(this).val() === 'custom') {
                $('.custom_format--div').removeClass('d-none');
            } else {
                $('.custom_format--div').addClass('d-none');
                $('#custom_format').val('');
            }
        });

        $('#custom_field_set_types').on('change', function () {
            if ($(this).val() === '2') $('.custom-fieldset-extra').removeClass('d-none');
            else {
                $('.custom-fieldset-extra').addClass('d-none');
                $('#fieldset_for_ticket_type, #fieldset_for_ticket_type_status').val('null').trigger('change');
            }
        });
    }

    // ----- Show modals -----
    t.showAddFieldSetModal = function (e) {
        e.preventDefault();
        t.resetFieldSetForm();
        showModal(t.fieldSetModalEl);
    };

    // ----- Select2 initialization -----
    function initSelect2() {
        $('#fieldset_for_ticket_type, #fieldset_for_ticket_type_status').select2({ width: '100%', dropdownParent: $('#custom-fieldset-mdl') });
        $('#custom_field_types, #format, #fieldType, #optionType, #preDefinedOptions').select2({ width: '100%', dropdownParent: $('#custom-field-mdl') });
        $('#custom_field_set_types').select2({ width: '100%', dropdownParent: $('#custom-fieldset-mdl') });
    }

    // ----- Bootstrap modal cleanup -----
    function initModalEvents() {
        $('#custom-field-mdl').on('show.bs.modal', function () { /* optional */ });
        $('#custom-fieldset-mdl').on('show.bs.modal', function () { t.resetFieldSetForm(); });
    }

    // ----- Initialization -----
    t.requestOngoing = false;
    t.httpPostPath = '';
    initDataTables();
    initSummernote();
    initValidators();
    initSelect2();
    bindEvents();
    initModalEvents();

    t.fieldSetModal = t.fieldSetModalEl ? new bootstrap.Modal(t.fieldSetModalEl) : null;
    t.customFieldModal = t.customFieldModalEl ? new bootstrap.Modal(t.customFieldModalEl) : null;
};