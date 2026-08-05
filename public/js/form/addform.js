var AddForm = function(config) {

    var t = this;
    t.config       = config;
    t.wrapper      = $("#add-form-wrapper");
    t.frm          = $("#dynamic_form_builder");
    t.isSubmitting = false;   

    t.frmEl = {};
    t.frmEl.form_name    = $("#form_name");
    t.frmEl.descriptions = $("#descriptions");
    t.frmEl.fields       = $("#fields");

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
    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            form_name:    { required: true },
            descriptions: { required: true }
        },
        messages: {
            form_name:    config.translations.form_name_required,
            descriptions: config.translations.description_required
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

    t.handleSubmit = function() {
        if (t.isSubmitting) {
            console.warn("Already submitting, ignoring duplicate call.");
            return;
        }

        if (t.frmValidator.form() === false) {
            return false;
        }

        var fields = t.frmEl.fields.val();
        if (!fields || fields === "[]" || fields.trim() === "") {
            sweetAlert("center", "error", {
                msg: config.translations.atleast_one_field
            });
            return false;
        }

        t.isSubmitting = true;   

        var payload = {
            _token:       t.config.token,
            form_name:    t.frmEl.form_name.val(),
            descriptions: t.frmEl.descriptions.val(),
            fields:       t.frmEl.fields.val()
        };

        $.ajax({
            url: t.config.url.addForm,
            type: "POST",
            data: payload
        })
        .done(function(data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    sweetAlert("center", "success", data);
                    setTimeout(function() {
                        window.location = t.config.url.form_list;
                    }, 800);
                } else {
                    sweetAlert("center", "error", data);
                    t.isSubmitting = false;   
                }
            }
        })
        .fail(function() {
            sweetAlert("center", "error", {
                msg: config.translations.something_wrong
            });
            t.isSubmitting = false;  
        });
    };

    t.wrapper.on("click", ".btn-go-list", function(e) {
        e.preventDefault();
        window.location = t.config.url.form_list;
    });

    t.wrapper.on("click", ".save-template", function(e) {
        e.preventDefault();
        t.handleSubmit();
    });

    $(document).on('click', '.sticky-controls .ui-sortable li', function () {
        setTimeout(function () {
            $('.fld-label').each(function () {
                if ($(this).data('summernote')) {
                    return;
                }
                $(this).summernote({
                    inheritPlaceholder: true,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol']]
                    ],
                    minHeight: 200,
                    callbacks: {
                        onChange: function(contents) {
                            $(this).html(contents);
                        },
                    }
                });
            });
        }, 300);
    });
    
    $('#depends_on_field').select2({
        width: '100%',
        dropdownParent: $('#modal-form-dependson')
    });

    $('#depends_on_select_container').select2({
        width: '100%',
        dropdownParent: $('#modal-form-dependson')
    });

    $('#depends_on_radio-group_container').select2({
        width: '100%',
        dropdownParent: $('#modal-form-dependson')
    });

    $('#depends_on_checkbox-group_container').select2({
        width: '100%',
        dropdownParent: $('#modal-form-dependson')
    });
};