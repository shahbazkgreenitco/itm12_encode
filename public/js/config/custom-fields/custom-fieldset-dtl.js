
var CustomFieldsetDetail = function (config) {
    var t = this;
    t.config = config;
    t.container = $("section.content");  // adjust if your wrapper is different
    t.table = t.container.find("#mytable");
    t.modalElement = document.getElementById('fieldAddModal');
    t.modal = null;

    // Modal elements
    t.mdl = {
        modal: null,
        form: $('#AddFieldForm'),
        fieldSelect: $('#field_id'),
        referenceSelect: $('#reference_id'),
        orderInput: $('#order'),
        requiredCheck: $('#requiredCheck'),
        submitBtn: $('#btnSubmitFieldSet')
    };

    // ========== Initialize DataTable (client‑side processing) ==========
    t.dt = t.table.DataTable({
        processing: true,
        serverSide: false,        
        ajax: {
            url: t.config.url.list,
            type: "GET"
        },
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        rowReorder: {
            selector: '.drag-handle',
            update: false            
        },
        columns: [
            { data: 'name' },
            { data: 'format' },
            { data: 'element' },
            { data: 'required' },
            {
                data: 'id',
                orderable: false,
                render: function (data, type, row) {
                    return `
                        <div class="d-flex gap-2">
                            <span class="user-list-action-btn drag-handle" data-id="${data}" style="cursor: grab;">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="5" cy="2" r="1.5" fill="currentColor"/><circle cx="11" cy="2" r="1.5" fill="currentColor"/><circle cx="5" cy="8" r="1.5" fill="currentColor"/><circle cx="11" cy="8" r="1.5" fill="currentColor"/><circle cx="5" cy="14" r="1.5" fill="currentColor"/><circle cx="11" cy="14" r="1.5" fill="currentColor"/></svg>
                            </span>
                            <button class="user-list-action-btn delete-field" data-id="${data}">
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor">
                                    <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/>
                                </svg>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [],               
        pageLength: parseInt($('#user-list-page-length').val()),
        lengthChange: false,
        searching: true
    });

    // ========== Row Reorder Event ==========
    t.table.on('row-reorder.dt', function (e, diff) {
        if (!diff.length) return;

        let updatedData = [];

        diff.forEach(function (row) {
            let rowData = t.dt.row(row.node).data();

            updatedData.push({
                id: rowData.id,
                position: row.newPosition + 1, 
                fieldset_id: t.config.fieldset_id
            });
        });

        $.ajax({
            url: t.config.url.reorder,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(updatedData),
            headers: {
                'X-CSRF-TOKEN': t.config.token
            },
            success: function (response) {
                if (response.status === 'success') {
                    sweetAlert('center', 'success', { msg: 'Order updated' });
                    t.reload();
                } else {
                    console.error('Reorder failed', response);
                    t.reload();
                }
            },
            error: function () {
                console.error('Reorder request failed');
                t.reload();
            }
        });
    });

    // ========== Helper: Reload DataTable ==========
    t.reload = function () {
        t.dt.ajax.reload(); 
    };

    // ========== Search (trigger on Enter key) ==========
    t.initSearch = function () {
        $('#fieldset-details-list-search').off('keyup').on('keyup', function (e) {
            if (e.key === 'Enter') {
                let value = $(this).val().trim();
                t.dt.search(value).draw();
            }
        });
    };

    t.initPageLength = function () {
        const $lengthSelect = $('#user-list-page-length');        
        $lengthSelect.val(t.dt.page.len());
        $(document).on('change', '#user-list-page-length', function () {
            let length = parseInt($(this).val(), 10);
            if (!isNaN(length)) {
                t.dt.page.len(length).draw(false);
            }
        });
        t.dt.on('draw.dt', function () {
            $lengthSelect.val(t.dt.page.len());
        });
        const originalReload = t.reload;
        t.reload = function () {
            $lengthSelect.val(t.dt.page.len());
        };
    };
    // ========== Reload Button ==========
    t.initReloadButton = function () {
        $('.btn-reload-list').off('click').on('click', function () {
            t.reload();
        });
    };

    // ========== Delete Field with SweetAlert confirmation ==========
    
    t.initDelete = function () {
        t.container.on('click', '.delete-field', function () {
            let fieldId = $(this).data('id');
            let fieldsetId = t.config.fieldset_id;  

            sweetAlertConfirmation({
                message: t.config.translations.are_you_delete || 'Are you sure you want to delete this?',
                onConfirm: function() {
                    $.ajax({
                        url: t.config.url.delete,     
                        type: "GET",                  
                        data: {
                            fldSet: fieldsetId,
                            field: fieldId
                        },
                        success: function(response) {
                            if (response.status === "success") {
                                sweetAlert('center', 'success',{ msg: 'Deleted Successful' });
                                t.reload();
                            } else {
                                sweetAlert('center', 'error', response || 'Deletion failed');
                            }
                        },
                        error: function() {
                            alert(t.config.translations.something_went_wrong || 'Something went wrong');
                        }
                    });
                }
            });
        });
    };

    // ========== Modal Handling (Bootstrap 5) ==========
    t.initModal = function () {
        if (t.modalElement) {
            t.modal = new bootstrap.Modal(t.modalElement);
        }

        $('.open-add-modal').off('click').on('click', function () {
            t.resetAddForm();
            t.modal.show();
        });
    };

    t.resetAddForm = function () {
        t.mdl.form[0].reset();
        t.mdl.referenceSelect.html('<option value="0">Select Reference</option>').trigger('change');
        t.mdl.fieldSelect.val(null).trigger('change');
        t.mdl.orderInput.val('');
        t.mdl.requiredCheck.prop('checked', false);
        if (t.frmValidator) {
            t.frmValidator.resetForm();
        }
    };

    // ========== Select2 with AJAX for Field ==========
    t.initFieldSelect2 = function () {
        t.mdl.fieldSelect.select2({
            width: '100%',
            dropdownParent: $('#fieldAddModal'),
            placeholder: 'Select Field',
            ajax: {
                url: t.config.url.getFields,
                type: 'POST',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        set_id: t.config.fieldset_id,
                        _token: t.config.token
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                }
            }
        });
    };

    // ========== Reference Dropdown (depends on field_id) ==========
    t.initReferenceLoader = function () {
        t.mdl.fieldSelect.on('change', function () {
            let fieldId = $(this).val();
            if (!fieldId) return;

            $.ajax({
                url: t.config.url.fetchReference,
                type: "POST",
                data: {
                    id: fieldId,
                    _token: t.config.token
                },
                success: function (res) {
                    let $ref = t.mdl.referenceSelect;
                    $ref.empty().append('<option value="0">Select Reference</option>');
                    if (res && res.length) {
                        $.each(res, function (i, item) {
                            $ref.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                    }
                    $ref.trigger('change');
                }
            });
        });
    };

    // ========== Reference Select2 ==========
    t.initReferenceSelect2 = function () {
        t.mdl.referenceSelect.select2({
            width: '100%',
            dropdownParent: $('#fieldAddModal'),
            placeholder: 'Select Reference'
        });
    };

    // ========== jQuery Validation ==========
    t.initValidation = function () {
        t.frmValidator = t.mdl.form.validate({
            onsubmit: false,
            ignore: [],
            rules: {
                field_id: { required: true },
                order: {
                    required: true,
                    digits: true,
                    min: 1
                },
                reference_id: {
                    required: {
                        depends: function () {
                            return false;
                        }
                    }
                }
            },
            messages: {
                field_id: "Please select a field",
                order: {
                    required: "Please enter the display order",
                    digits: "Please enter a valid number",
                    min: "Order must be at least 1"
                },
                reference_id: "Please select a reference"
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
    };

    // ========== Submit Add Field Form with Validation ==========
    t.initFormSubmit = function () {
        t.mdl.form.on('submit', function (e) {
            e.preventDefault();
            if (!t.frmValidator.form()) {
                return false;
            }
            let btn = t.mdl.submitBtn;
            btn.prop('disabled', true).text('Saving...');

            let formData = {
                field_id: t.mdl.fieldSelect.val(),
                order: t.mdl.orderInput.val(),
                required: t.mdl.requiredCheck.is(':checked') ? 1 : 0,
                reference_id: t.mdl.referenceSelect.val(),
                _token: t.config.token
            };

            $.ajax({
                url: t.config.url.add,
                type: "POST",
                data: formData,
                success: function (res) {
                    if (res.status === 'success') {
                        sweetAlert('center', 'success', { msg: res.msg });
                        t.modal.hide();
                        t.reload();
                    } else {
                        sweetAlert('center', 'error', { msg: res.msg || 'Something went wrong. Please check given details are correct' });
                    }
                },
                error: function (xhr) {
                    let msg = "Something went wrong";
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        msg = xhr.responseJSON.msg;
                    }
                    sweetAlert('center', 'error', { msg: msg });
                },
                complete: function () {
                    btn.prop('disabled', false).text('Save');
                }
            });
        });
    };

    // ========== Cleanup modal on close ==========
    t.initModalCleanup = function () {
        $(t.modalElement).on('hidden.bs.modal', function () {
            t.resetAddForm();
        });
    };

    // ========== Public Initialization ==========
    t.init = function () {
        t.initSearch();
        t.initPageLength();
        t.initReloadButton();
        t.initDelete();
        t.initModal();
        t.initFieldSelect2();
        t.initReferenceSelect2();
        t.initReferenceLoader();
        t.initValidation();
        t.initFormSubmit();
        t.initModalCleanup();
    };

    t.init();
};