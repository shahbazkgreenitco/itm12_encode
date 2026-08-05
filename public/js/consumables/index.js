var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.content = $("main.main-content");
    t.table = t.content.find("#mytable");

    t.showHideButton = t.content.find('.show-hide-columns');

    t.httpCall = true;
    t.httpPostPath = "";
    t.btn = {};
    t.filters = {
        wrapper: t.content.find("#consumableFilterModal"),
        location: null,
        supplier: null,
        status: null,
        date: null,
        daterange: null,
        reportrange: t.content.find('#reportrange'),
        badge: t.content.find(".filter-count-badge"),
    };
    t.filters.location = t.filters.wrapper.find("#filter_by_location");
    t.filters.categories = t.filters.wrapper.find("#filter_by_category");
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
    t.filters.date_range = t.filters.wrapper.find("#daterange"),
        t.filters.assetDepartment = t.filters.wrapper.find("#filter_by_department"),
        t.filters.purchase_reference = t.filters.wrapper.find("#filter_by_purchase_reference");
    t.btn.filter = t.filters.wrapper.find('#filter');
    t.btn.clear = t.filters.wrapper.find('#clear');

    var ConsumableForm = {};
    var consumableModal = $('#consumablemodal');
    var consumabletabs = consumableModal.find('#consumableTabs');
    ConsumableForm.frm = $("#ConsumableForm");
    ConsumableForm.frmEl = {};
    ConsumableForm.frmEl.id = ConsumableForm.frm.find("#id");
    ConsumableForm.frmEl.supplier = ConsumableForm.frm.find("#supplier_id");
    ConsumableForm.frmEl.company_id = ConsumableForm.frm.find("#company_id");
    ConsumableForm.frmEl.category_id = ConsumableForm.frm.find("#category_id");
    ConsumableForm.frmEl.manufacturer_id = ConsumableForm.frm.find("#manufacturer_id");
    ConsumableForm.frmEl.location_id = ConsumableForm.frm.find("#location_id");
    ConsumableForm.frmEl.internal_place = ConsumableForm.frm.find("#internal_place");
    ConsumableForm.frmEl.invoice_id = ConsumableForm.frm.find("#invoice_id");
    ConsumableForm.frmEl.currency = ConsumableForm.frm.find("#currency");
    ConsumableForm.frmEl.name = ConsumableForm.frm.find("#name");
    ConsumableForm.frmEl.order_number = ConsumableForm.frm.find("#order_number");
    ConsumableForm.frmEl.purchase_date = ConsumableForm.frm.find("#purchase_date");
    ConsumableForm.frmEl.purchase_cost = ConsumableForm.frm.find("#purchase_cost");
    ConsumableForm.frmEl.qty = ConsumableForm.frm.find("#qty");
    ConsumableForm.frmEl.notes = ConsumableForm.frm.find("#notes");
    ConsumableForm.frmEl.customFieldsPrvEl = ConsumableForm.frm.find(".custom-fields-follow");
    ConsumableForm.frmEl.department_id = ConsumableForm.frm.find("#department_id");
    ConsumableForm.frmEl.imgviewcover = ConsumableForm.frm.find(".imgviewcover");
    ConsumableForm.frmEl.imgview = ConsumableForm.frm.find("#imgview");
    ConsumableForm.frmEl.clone_img = ConsumableForm.frm.find("#clone_img");
    ConsumableForm.frmEl.image = ConsumableForm.frm.find("#image");
    ConsumableForm.frmEl.unit = ConsumableForm.frm.find("#unit");
    ConsumableForm.frmEl.consumable_thresholds = ConsumableForm.frm.find("#consumable_thresholds");
    ConsumableForm.frmEl.thresholds_alerts = ConsumableForm.frm.find("#thresholds_alerts");
    ConsumableForm.frmEl.threshold_alert_users = ConsumableForm.frm.find("#threshold_alert_users");
    ConsumableForm.frmEl.reorder_limits = ConsumableForm.frm.find("#reorder_limits");
    ConsumableForm.frmEl.unique_tag = ConsumableForm.frm.find("#unique_tag");
    ConsumableForm.frmEl.images_list = ConsumableForm.frm.find('#consumables-image-list');

    // on image input, showing frm validator error
    $('input[type="file"]').on('change', function () {
        ConsumableForm.frmValidator.element(this);
    });

    var count = 0;
    var current_purchase_date = "";
    var current_supplier_name = current_supplier_id = "";
    var order_no = purchase_cost = currency_default = "";
    t.internal_places = [];
    t.action = '';
    ConsumableForm.frmValidator = ConsumableForm.frm.validate({
        onsubmit: false,
        ignore: ':hidden:not(.select2-hidden-accessible)',
        onkeyup: function (element, event) {
            this.element(element);
        },
        errorClass: 'error amg-form-invalid',
        rules: {
            company_id: {
                required: true,
            },
            unique_tag: {
                maxlength: 100
            },
            name: {
                maxlength: 100,
                required: true,
                str_name: false,
                clean_text_only: true
            },
            category_id: {
                required: true,
            },
            manufacturer_id: {
                required: false
            },
            department_id: {
                required: false,
            },
            location_id: {
                required: true
            },
            supplier_id: {
                required: false
            },
            order_number: {
                maxlength: 100,
                required: false,
                clean_text_only: true
            },
            invoice_id: {
                required: false
            },
            purchase_date: {
                remarks: true
            },
            internal_place: {
                required: false,
            },
            supplier: {
                required: false,
            },
            currency: {
                required: false,
            },
            purchase_cost: {
                min: 0,
                number: true
            },
            qty: {
                min: 1,
                required: true,
                digits: true,
            },
            consumable_thresholds: {
                digits: true,
                min: 0,
                max: 10000,
            },
            reorder_limits: {
                digits: true,
                min: 0,
                max: 10000,
            },
            notes: {
                remarks: true,
                clean_text_only: true
            },
            image: {
                customImageType: true,
                filesize: 2000000,
                maxfiles: 1
            }
        },
        messages: {
            image: {
                customImageType: "Only png, jpg, jpeg and bmp files are allowed.",
                filesize: "File size must be less than 2MB."
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').addClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.addClass("amg-form-invalid");
            }
            var isSelect2 = $element.hasClass("select2-hidden-accessible");
            if (isSelect2) {
                $element.next(".select2-container").find(".select2-selection").addClass("amg-form-select-error");
            }
        },
        unhighlight: function (element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').removeClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.removeClass("amg-form-invalid");
            }
            var isSelect2 = $element.hasClass("select2-hidden-accessible");
            if (isSelect2) {
                $element.next(".select2-container").find(".select2-selection").removeClass("amg-form-select-error");
            }
        }
    });

    $.validator.addMethod("customImageType", function (value, element) {
        if (element.files.length === 0) {
            return true;
        }
        var allowedMimeTypes = [
            "image/png",
            "image/jpeg",
            "image/bmp"
        ];
        return allowedMimeTypes.includes(element.files[0].type);
    }, "Only png, jpg, jpeg and bmp files are allowed.");

    var originalResetForm = ConsumableForm.frmValidator.resetForm;
    ConsumableForm.frmValidator.resetForm = function () {
        originalResetForm.call(this);
        $('#ConsumableForm').find(':input').each((_, element) => {
            this.settings.unhighlight.call(
                this,
                element,
                this.settings.errorClass,
                this.settings.validClass
            );
        });
    };

    $(document).on('select2:select select2:unselect', 'select', function () {
        var $form = $(this).closest('form');
        if ($form.is('#ConsumableForm')) {
            ConsumableForm.frmValidator.element(this);
        }
        if ($form.is('#ChkoutConsumableForm')) {
            ChkoutConsumableMdl.frmValidator.element(this);
        }
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    // function checkThresholdValidation() {
    //     var thresholdValue = ConsumableForm.frmEl.consumable_thresholds.val();
    //     var thresholdAlertValue = ConsumableForm.frmEl.threshold_alert_users.val();

    //     if((thresholdValue > 0 && thresholdValue != null) && thresholdAlertValue === null){
    //         $("#threshold_alert_users").rules("add", {
    //             required: true, 
    //             messages: { required: "This field is required when thresholds are set." } 
    //         });
    //     } else{
    //         $("#threshold_alert_users").rules("remove", "required");
    //     }
    //     ConsumableForm.frmValidator.element("#threshold_alert_users");
    // }

    // $("#consumable_thresholds").on("input", function () {
    //     checkThresholdValidation();
    // });

    // $("#submitBtn").on("click", function (e) {
    //     checkThresholdValidation(); 

    //     if (!ConsumableForm.frm.valid()) {
    //         e.preventDefault();
    //     }
    // });

    var ChkoutConsumableMdl = $("#checkoutconsumablemodal");
    ChkoutConsumableMdl.frm = ChkoutConsumableMdl.find("#ConsumableCheckoutForm");
    ChkoutConsumableMdl.frmEl = {};
    ChkoutConsumableMdl.frmEl.assigned_for = ChkoutConsumableMdl.find("#assigned_for");
    ChkoutConsumableMdl.frmEl.assigned_to = ChkoutConsumableMdl.find("#assigned_to");
    ChkoutConsumableMdl.frmEl.device_id = ChkoutConsumableMdl.find("#device_id");
    ChkoutConsumableMdl.frmEl.assigned_place = ChkoutConsumableMdl.find("#assigned_place");
    ChkoutConsumableMdl.frmEl.note = ChkoutConsumableMdl.find("#note");
    ChkoutConsumableMdl.frmEl.assigned_to.select2({ width: '100%', allowClear: true });
    ChkoutConsumableMdl.frmValidator = ChkoutConsumableMdl.frm.validate({
        onsubmit: false,
        onkeyup: function (element, event) {
            this.element(element);
        },
        ignore: ':hidden:not(.select2-hidden-accessible)',
        errorClass: 'error',
        rules: {
            assigned_to: {
                required: true,
                clean_text_only: true
            },
            note: {
                required: function () {
                    return config.client === "knightfrank";
                },
                clean_text_only: true
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').addClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.addClass("amg-form-invalid");
            }
            var isSelect2 = $element.hasClass("select2-hidden-accessible");
            if (isSelect2) {
                $element.next(".select2-container").find(".select2-selection").addClass("amg-form-select-error");
            }
        },
        unhighlight: function (element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').removeClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.removeClass("amg-form-invalid");
            }
            var isSelect2 = $element.hasClass("select2-hidden-accessible");
            if (isSelect2) {
                $element.next(".select2-container").find(".select2-selection").removeClass("amg-form-select-error");
            }
        }
    });

    $("#checkoutconsumablemodal").on('show.bs.modal', function (e) {
        $('#ConsumableCheckoutForm')[0].reset();
        ChkoutConsumableMdl.frmValidator.resetForm();
        ChkoutConsumableMdl.frmEl.assigned_to.val(null).trigger("change");
        ChkoutConsumableMdl.frmEl.note.val(null);
        var company_id = $(e.relatedTarget).attr('data-company_id');
        var entityId = $(e.relatedTarget).attr('data-id');
        var select2Opts = { width: "100%" };
        ChkoutConsumableMdl.frmEl.assigned_to.select2($.extend({}, select2Opts, {
            dropdownParent: $('#checkoutconsumablemodal'),
            ajax: {
                url: t.config.getUserByAjax,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
                        success({ results: [] });
                        return;
                    }
                    let request = $.ajax(params);
                    request.then(success);
                    request.fail(failure);
                    return request;
                },
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        company_id: function () {
                            return company_id;
                        }
                    };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: t.config.translations.Select_the_User,
            templateSelection: function (data, container) {
                $(container).attr('title', data.text);
                return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
            },
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                var email = s.email == null ? "" : s.email;
                var name = t.safeDisplayValue(s.text, "-");
                var imageUrl = t.safeDisplayValue(s.img_path, "");
                var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");

                return $(
                    '<div class="d-flex align-items-center gap-2">' +
                    avatarHtml +
                    '<span>' + t.tblHelpers.escapeHtml(name) + '</span>' +
                    '</div>'
                );
            },
        }));

        if (ChkoutConsumableMdl.frmEl.assigned_place.hasClass("select2-hidden-accessible")) {
            ChkoutConsumableMdl.frmEl.assigned_place.select2("destroy");
        }

        ChkoutConsumableMdl.frmEl.assigned_place.empty();
        ChkoutConsumableMdl.frmEl.assigned_place.select2($.extend({}, select2Opts, {
            dropdownParent: ChkoutConsumableMdl.frmEl.assigned_place.parent(),
            ajax: {
                url: t.config.ajaxGetInternalPlace,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
                        success({ results: [] });
                        return;
                    }
                    let request = $.ajax(params);
                    request.then(success);
                    request.fail(failure);
                    return request;
                },
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        company_id: function () {
                            return company_id;
                        }
                    };
                },
                delay: 300,
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }

            },
            allowClear: true,
            placeholder: t.config.translations.Select_Place,
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                if (!s.id) {
                    return s.text;
                }
                var $container = $("<div>");
                $container.append(`<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M20 10C20 14.4183 12 22 12 22C12 22 4 14.4183 4 10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10Z" stroke="#131927" stroke-width="1.5" />
                    <path d="M12 11C12.5523 11 13 10.5523 13 10C13 9.44772 12.5523 9 12 9C11.4477 9 11 9.44772 11 10C11 10.5523 11.4477 11 12 11Z" fill="#131927" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg> ${s.text}`);
                return $container;
            },
            templateSelection: function (data, container) {
                if (container) {
                    $(container).attr('title', data.text);
                }
                return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
            },
            allowClear: true,
            placeholder: "Select The Place"
        }));

        ChkoutConsumableMdl.frmEl.assigned_for.select2($.extend({}, select2Opts, { dropdownParent: ConsumableForm.frmEl.assigned_for }));
        ChkoutConsumableMdl.frmEl.device_id.select2($.extend({}, select2Opts, {
            dropdownParent: ChkoutConsumableMdl.frmEl.device_id.parent(),
            ajax: {
                url: t.config.getDeviceForCheckoutDropDown,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
                        success({ results: [] });
                        return;
                    }
                    let request = $.ajax(params);
                    request.then(success);
                    request.fail(failure);
                    return request;
                },
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        company_id: function () {
                            return company_id;
                        }
                    };
                },
                delay: 300
            },
            allowClear: true,
            //minimumInputLength: 1,
            placeholder: config.translations.Select_the_Device,
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                a = `<div class="so-t">
                        <svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' viewBox='0 0 24 24'>
                            <path d='M0 0h24v24H0z' fill='none' />
                            <path fill='currentColor' d='M5.5 7A1.5 1.5 0 0 1 4 5.5A1.5 1.5 0 0 1 5.5 4A1.5 1.5 0 0 1 7 5.5A1.5 1.5 0 0 1 5.5 7m15.91 4.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.11 0-2 .89-2 2v7c0 .55.22 1.05.59 1.41l8.99 9c.37.36.87.59 1.42.59s1.05-.23 1.41-.59l7-7c.37-.36.59-.86.59-1.41c0-.56-.23-1.06-.59-1.42' />
                        </svg>
                        ${s.asset_tag}
                    </div>`;

                if (s.asset_name != null) {
                    a += `<div class="so-t">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                            <path d="M0 0h24v24H0z" fill="none" />
                                            <path fill="currentColor" d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2zM4 6h16v10H4z" />
                                        </svg>
                                        ${s.asset_name}
                                  </div>`;
                }

                a += `
                            <div class="so-m">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M12.588 6.086q.22-.222.22-.549t-.222-.547t-.549-.22t-.548.22t-.22.55t.222.547t.549.22t.548-.22M6 22V2h12.077v4.83H19v3.686h-.923V22zm1-1h10.077V3H7zm0 0V3z" />
                                </svg>
                                ${s.name} ${s.modelno}
                            </div>
                            <div class="so-t">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path fill="currentColor" d="M1 21v-5h2v3h3v2zm17 0v-2h3v-3h2v5zM4 18V6h2v12zm3 0V6h1v12zm3 0V6h2v12zm3 0V6h3v12zm4 0V6h1v12zm2 0V6h1v12zM1 8V3h5v2H3v3zm20 0V5h-3V3h5v5z" />
                                </svg>
                                ${s.serial}
                            </div>`;
                return $("<div>" + a + "</div>");
            },
            templateSelection: function (data, container) {
                if (container) {
                    $(container).attr('title', data.text);
                }
                return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
            }
        }));

        ChkoutConsumableMdl.frmEl.assigned_for.select2($.extend({}, select2Opts, {
            dropdownParent: $('#checkoutconsumablemodal'),
            data: t.config.assignedForOptions,
            width: '100%',
            allowClear: true
        }));

        ChkoutConsumableMdl.frmEl.assigned_for.val("1").trigger('change');

        $('label.msg').remove();
        $('#checkoutmodalHead').html('Checkout');
        $('#checkoutsubmitBtn').html('Checkout');
        $('#chechout-consumable-name').html($(e.relatedTarget).attr('data-name'));
        $('#ConsumableCheckoutForm').attr('action', t.config.url.checkout + '/' + entityId);
    });

    ChkoutConsumableMdl.frm.submit(function (e) {
        e.preventDefault();
        if (ChkoutConsumableMdl.frmValidator.form() == false) {
            return false;
        }

        var formData = new FormData(this);
        var submitUrl = $(this).attr("action");

        var submitBtn = $("#checkoutsubmitBtn");
        submitBtn.prop("disabled", true);

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                submitBtn.prop('disabled', true).text('Please wait...');
            },
            success: function (data) {
                $('label.msg').remove();
                if (data.status == 'error') {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                } else if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data.msg + '.</p></div>'});
                    $('#checkoutconsumablemodal').modal('hide');
                    t.dTbl.ajax.reload();
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false).text('Save');
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    ConsumableForm.frm.submit(function (e) {
        e.preventDefault();
        // if(requestOngoing) return false;

        if (ConsumableForm.frmValidator.form() == false) {
            return false;
        }
        //  = true;
        var formData = new FormData(this);
        var submitUrl = $(this).attr("action");
        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            beforeSend: function () {
                $('#submitBtn').prop('disabled', true).text('Please wait...');
            },
            complete: function () {
                $('#submitBtn').prop('disabled', false).text('Save');
            },
            success: function (data) {
                $('label.msg').remove();

                if (data.status == 'error') {
                    if (typeof data.section != 'undefined') {
                        sweetAlert('center', 'error', data);
                        // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                    } else {
                        $.each(data.errors, function (index, value) {
                            var parent = $("[name='" + index + "']").parent();
                            if (parent.hasClass("input-group"))
                                parent.after('<label class="error msg" for="">' + value + '</label>')
                            else
                                $("[name='" + index + "']").after('<label class="error msg" for="">' + value + '</label>')
                        });
                    }

                }
                else if (typeof data[0] != 'undefined') {
                    if (data[0].status == 'success') {
                        sweetAlert('center', 'success', data[0]);
                        // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data[0].msg + '.</p></div>'});
                        $('#consumablemodal').modal('hide');

                        //for clone
                        if (typeof data[0].id != "undefined")
                            window.location.href = config.url.infourl + '/' + data[0].id
                        //for clone
                        if (typeof dtblRef != "undefined") { dtblRef.dTbl.ajax.reload(); }
                        else {
                            $.get(config.url.infocontent, function (data) {
                                $('#info-tab').html(data);
                            })
                        }
                    }
                }
                else if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data.msg + '.</p></div>'});
                    $('#checkoutconsumablemodal').modal('hide');
                    if (typeof dtblRef != "undefined")
                        dtblRef.dTbl.ajax.reload();
                    if ('{{Route::getCurrentRoute()->getActionMethod()}}' == 'consumableInfo') {
                        $.get(config.url.infocontent, function (data) {
                            $('#info-tab').html(data);
                        })
                        dTbl.ajax.reload()
                        dTblHis.ajax.reload()
                    }
                }
                // requestOngoing = false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    function populateWithEntity(id, action) {
        //  current_Purchase_date = "";
        //  order_no = "";
        ConsumableForm.frmValidator.resetForm();
        $.getJSON(config.url.edit + '/' + id, function (data) {
            ConsumableForm.frmEl.id.val(id);
            ConsumableForm.frmEl.company_id.val(data.data.company_id);
            ConsumableForm.frmEl.name.val(data.data.name);
            // ConsumableForm.frmEl.category_id.val(data.data.category_id);
            ConsumableForm.frmEl.manufacturer_id.val(data.data.manufacturer_id);
            ConsumableForm.frmEl.invoice_id.empty();
            if (typeof data.dropdown == "object" && typeof data.dropdown.invoice == "object" && data.dropdown.invoice != null) {
                ConsumableForm.frmEl.invoice_id.append(new Option(data.dropdown.invoice.text, data.dropdown.invoice.id, true, true)).trigger("change");
            }
            ConsumableForm.frmEl.invoice_id.trigger("change");
            order_no = data.data.order_number;
            ConsumableForm.frmEl.order_number.val(order_no);
            current_Purchase_date = data.data.purchase_date;
            ConsumableForm.frmEl.purchase_date.datepicker("update", current_Purchase_date);
            if (data.data.currency != "" && data.data.currency != null) {
                ConsumableForm.frmEl.currency.val(data.data.currency);
            }
            if (data.data.requestable == 1) {
                ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", true);
            } else {
                ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", false);
            }
            ConsumableForm.frmEl.purchase_cost.val(data.data.purchase_cost);
            ConsumableForm.frmEl.notes.val($.trim(data.data.notes));
            ConsumableForm.frmEl.reorder_limits.val(data.data.reorder_limits);
            ConsumableForm.frmEl.unique_tag.val(data.data.unique_tag);
            ConsumableForm.frmEl.qty.val(data.data.qty);
            ConsumableForm.frmEl.consumable_thresholds.val(data.data.consumable_thresholds);
            if (data.data.thresholds_alerts == 1) {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", true);
            } else {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", false);
            }
            var option = new Option(data.dropdown.company.text, data.dropdown.company.id, true, true);
            ConsumableForm.frmEl.company_id.append(option).trigger("change");
            // ConsumableForm.frmEl.category_id.trigger("change");
            // ConsumableForm.frmEl.manufacturer_id.trigger("change");
            ConsumableForm.frmEl.currency.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.department == "object" && data.dropdown.department != null) {
                ConsumableForm.frmEl.department_id.append(new Option(data.dropdown.department.text, data.dropdown.department.id, true, true)).trigger("change");
            }
            if (typeof data.dropdown == "object" && typeof data.dropdown.supplier == "object" && data.dropdown.supplier != null) {
                ConsumableForm.frmEl.supplier.append(new Option(data.dropdown.supplier.text, data.dropdown.supplier.id, true, true));
            }
            if (typeof data.dropdown == "object" && typeof data.dropdown.manufacturer == "object" && data.dropdown.manufacturer != null) {
                ConsumableForm.frmEl.manufacturer_id.append(new Option(data.dropdown.manufacturer.text, data.dropdown.manufacturer.id, true, true)).trigger("change");
            }
            ConsumableForm.frmEl.supplier.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.location == "object" && data.dropdown.location != null) {
                ConsumableForm.frmEl.location_id.append(new Option(data.dropdown.location.text, data.dropdown.location.id, true, true)).trigger("change");
            }
            ConsumableForm.frmEl.threshold_alert_users.trigger("change");
            ConsumableForm.frmEl.threshold_alert_users.empty();
            if (typeof data.dropdown == "object" && Array.isArray(data.dropdown.thresholdUserEmails) && data.dropdown.thresholdUserEmails.length > 0) {
                data.dropdown.thresholdUserEmails.forEach(user => {
                    if (user.text) { // Avoid adding null values
                        ConsumableForm.frmEl.threshold_alert_users.append(
                            new Option(user.text, user.id, true, true)
                        );
                    }
                });
                ConsumableForm.frmEl.threshold_alert_users.trigger("change");
            }

            if (typeof data.dropdown == "object" && typeof data.dropdown.internal_place == "object" && data.dropdown.internal_place != null) {
                t.internal_places.push(data.dropdown.internal_place.id);
            }
            ConsumableForm.frmEl.internal_place.trigger('change');
            if (typeof data.dropdown == "object" && typeof data.dropdown.unit == "object" && data.dropdown.unit != null) {
                ConsumableForm.frmEl.unit.append(new Option(data.dropdown.unit.text, data.dropdown.unit.id, true, true));
            }
            ConsumableForm.frmEl.unit.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.consumable == "object" && data.dropdown.consumable != null) {
                ConsumableForm.frmEl.category_id.attr('data-noLoadField', true);
                ConsumableForm.frmEl.category_id.append(new Option(data.dropdown.consumable.text, data.dropdown.consumable.id, true, true));
                ConsumableForm.frmEl.category_id.attr('data-noLoadField', false);
                ConsumableForm.frmEl.category_id.trigger("change");
            }
            clearCustomFields();
            if (typeof data.custom_fields == "object" && typeof data.custom_fields.html != "") {
                fillCustomFields(data.custom_fields);
            }
            $('#preview-image').show();
            if (data.data.image) {
                let $li = $("<li>")
                    .addClass("list-group-item d-flex align-items-center justify-content-between")
                    .attr("data-file", data.data.image);

                let $img = $("<img>")
                    .attr("src", baseURL + "/storage/uploads/consumable/" + data.data.image)
                    .addClass("img-thumbnail")
                    .css({ width: "80px", height: "80px", objectFit: "cover", marginRight: "10px" });

                let $link = $("<a>")
                    .attr("href", baseURL + "/storage/uploads/consumable/" + data.data.image)
                    .attr("target", "_blank")
                    .text(data.data.image);

                let $left = $("<div>").append($img).append("<br>").append($link);

                let $del = $("<button>")
                    .addClass("btn btn-sm btn-danger del-link")
                    .attr("data-action", 'clone')
                    .html("Delete");

                $li.append($left).append($del);
                ConsumableForm.frmEl.images_list.html($li);
                ConsumableForm.frmEl.clone_img.val(data.data.image);
            } else {
                ConsumableForm.frmEl.images_list.html("<li class='list-group-item'>No files uploaded</li>");
            }
        });
        return true;
    }

    clearCustomFields = function () {
        ConsumableForm.frm.find(".custom-field-row").remove();
    }

    fillCustomFields = function (data) {
        if (typeof data.html == "undefined") {
            return;
        }
        clearCustomFields();
        ConsumableForm.frmEl.customFieldsPrvEl.after(data.html);
        if (data.required_fields.length > 0) {
            $.each(data.required_fields, function (i, d) {
                ConsumableForm.frm.find("#" + d).rules("add", { required: true });
            });
        }
        $.each(data.all_fields, function (i, d) {
            ConsumableForm.frm.find("#" + d).rules("add", { remarks: false });
        });
        $(".cf-select2").each(function () {
            var $field = $(this);
            var fieldId = $field.attr("id");

            if (fieldId) {
                $field.select2($.extend({}, select2Opts, { dropdownParent: $field.parent() }));
                if ($field.hasClass("custFieldUser")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getUser',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_User,
                        templateSelection: function (data, container) {
                            let user = (data.text || '').trim();
                            $(container).attr('title', user);
                            return user.length > 50 ? user.substring(0, 50) + '...' : user;
                        },
                        templateResult: function (data) {
                            if (typeof data.loading != "undefined" && data.loading) {
                                return $("<div>" + data.text + "</div>");
                            }
                            var name = t.safeDisplayValue(data.text, "-");
                            var imageUrl = t.safeDisplayValue(data.img_path, "");
                            var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");
                            return $(
                                '<div class="d-flex align-items-center gap-2">' +
                                    avatarHtml +
                                    '<span>' + t.tblHelpers.escapeHtml(name) + '</span>' +
                                '</div>'
                            );
                        }
                    }));
                }

                if ($field.hasClass("custFieldLocation")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getLocation',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_location,
                        templateSelection: function (data, container) {
                            let location = (data.text || '').trim();
                            $(container).attr('title', location);
                            return location.length > 50 ? location.substring(0, 50) + '...' : location;
                        }
                    }));
                }

                if ($field.hasClass("custFieldDevice")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getDevice',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_device,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        },
                        templateResult: function (data, container) {
                            if (typeof data.loading != "undefined" && data.loading) {
                                return $("<div>" + data.text + "</div>");
                            }

                            a = `<div class="so-t">
                                <svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' viewBox='0 0 24 24'>
                                    <path d='M0 0h24v24H0z' fill='none' />
                                    <path fill='currentColor' d='M5.5 7A1.5 1.5 0 0 1 4 5.5A1.5 1.5 0 0 1 5.5 4A1.5 1.5 0 0 1 7 5.5A1.5 1.5 0 0 1 5.5 7m15.91 4.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.11 0-2 .89-2 2v7c0 .55.22 1.05.59 1.41l8.99 9c.37.36.87.59 1.42.59s1.05-.23 1.41-.59l7-7c.37-.36.59-.86.59-1.41c0-.56-.23-1.06-.59-1.42' />
                                </svg>
                                ${data.asset_tag}
                            </div>`;

                            if (data.asset_name != null) {
                                a += `<div class="so-t">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                                <path d="M0 0h24v24H0z" fill="none" />
                                                <path fill="currentColor" d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2zM4 6h16v10H4z" />
                                            </svg>
                                            ${data.asset_name}
                                        </div>`;
                            }

                            a += `
                                <div class="so-m">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor" d="M12.588 6.086q.22-.222.22-.549t-.222-.547t-.549-.22t-.548.22t-.22.55t.222.547t.549.22t.548-.22M6 22V2h12.077v4.83H19v3.686h-.923V22zm1-1h10.077V3H7zm0 0V3z" />
                                    </svg>
                                    ${data.name} ${data.modelno}
                                </div>
                                <div class="so-t">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor" d="M1 21v-5h2v3h3v2zm17 0v-2h3v-3h2v5zM4 18V6h2v12zm3 0V6h1v12zm3 0V6h2v12zm3 0V6h3v12zm4 0V6h1v12zm2 0V6h1v12zM1 8V3h5v2H3v3zm20 0V5h-3V3h5v5z" />
                                    </svg>
                                    ${data.serial}
                                </div>`;
                                return $("<div>" + a + "</div>");
                        }
                    }));
                }

                if ($field.hasClass("custFieldModels")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getModel',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_model,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldSupplier")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getSupplier',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_Supplier,
                        templateSelection: function (data, container) {
                            let supplier = (data.text || '').trim();
                            $(container).attr('title', supplier);
                            return supplier.length > 50 ? supplier.substring(0, 50) + '...' : supplier;
                        }
                    }));
                }

                if ($field.hasClass("custFieldProjects")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getProject',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_project,
                        templateSelection: function (data, container) {
                            let project = (data.text || '').trim();
                            $(container).attr('title', project);
                            return project.length > 50 ? project.substring(0, 50) + '...' : project;
                        }
                    }));
                }

                if ($field.hasClass("custFieldInternalPlace")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.ajaxGetInternalPlace,
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_internal_place,
                        templateSelection: function (data, container) {
                            let internalPlace = (data.text || '').trim();
                            $(container).attr('title', internalPlace);
                            return internalPlace.length > 50 ? internalPlace.substring(0, 50) + '...' : internalPlace;
                        }
                    }));
                }

                if ($field.hasClass("custFieldContract")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            // url: t.config.getLeaseByAjax,
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getContract',
                            dataType: "json",
                            data: function (p) {
                                return {
                                    search: p.term,
                                    page: p.page || 1,
                                    company_id: function () {
                                        return ConsumableForm.frmEl.company_id.val();
                                    }
                                };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_contract,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldComponent")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getComponent',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_component,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldLicense")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getLicense',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_license,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldTasks")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getTask',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_task,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldChangeManagement")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getRecord',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_change_management,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldManufacturers")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getManufacture',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_manufacturer,
                        templateSelection: function (data, container) {
                            let manufacturer = (data.text || '').trim();
                            $(container).attr('title', manufacturer);
                            return manufacturer.length > 50 ? manufacturer.substring(0, 50) + '...' : manufacturer;
                        }
                    }));
                }

                if ($field.hasClass("custFieldTickets")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getTicket',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_ticket,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldRequest")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getTicketProcureRequest',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_ticket_procure_request,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldPurchase")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery + '/' + 'getPurchase',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_Purchase_Invoice,
                        templateSelection: function (data, container) {
                            let purchase = (data.text || '').trim();
                            $(container).attr('title', purchase);
                            return purchase.length > 50 ? purchase.substring(0, 50) + '...' : purchase;
                        }
                    }));
                }

                if ($field.hasClass("custFieldDept")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.url.getAssetDepartments,
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_department,
                        templateSelection: function (data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }
            } else {
                console.warn("Select2 field without ID found. Skipping initialization.");
            }
        });
    }

    getCustomFields = function (e) {
        e.preventDefault();
        if (ConsumableForm.frmEl.category_id.attr('data-noLoadField') == "true") {
            return;
        }

        clearCustomFields();
        if (typeof config.custom_fields == "object" && typeof config.custom_fields.html != "") {
            fillCustomFields(config.custom_fields);
        }
        var category_id = parseInt(ConsumableForm.frmEl.category_id.val());
        if (category_id < 1 || isNaN(category_id)) {
            return;
        }

        // not calling this extra api, since data is already coming in the show api
        // var type_id = ConsumableForm.frmEl.id.val();

        // var http = $.get(config.url.getCustomFieldsByCategory + "/" + category_id + "/" + 'consumable' + "/" + type_id);
        // http.done(function (data) {
        //     if (typeof data == "object") {
        //         if (data.status == "success") {
        //             fillCustomFields(data);
        //         }
        //     }
        // });
        // http.fail(function () {
        //     alert(config.translations.something_went_wrong);
        // });
        // http.always(function () {
        //     httpCall = true;
        // });
    };

    ConsumableForm.frmEl.category_id.on("change", $.proxy(getCustomFields));

    $("#consumablemodal").on('show.bs.modal', function (e) {
        $('label.msg').remove();
        current_Purchase_date = "";
        order_no = "";
        var action = $(e.relatedTarget).attr('action');
        ConsumableForm.frmValidator.resetForm();
        if (action == 'add' || action == 'clone') {
            ConsumableForm.frmEl.company_id.prop("disabled", false);
            $('#modalHead').html('Add');
            $('#submitBtn').html(config.translations.add);
            $('#ConsumableForm').attr('action', config.url.add);
            $('#ConsumableForm')[0].reset();
            $('#ConsumableForm').find("#currency").val(window.config.default_currency_format);
            $('#ConsumableForm').find("input[name='unique_tag']").attr('readonly', false);
            ConsumableForm.frmEl.qty.attr("readonly", false);
            $('#ConsumableForm').find("input[name='unique_tag']").prop('disabled', false);
            // ConsumableForm.frmEl.qty.prop('disabled', false);
            clearCustomFields();
            if (typeof config.custom_fields == "object" && typeof config.custom_fields.html != "") {
                fillCustomFields(config.custom_fields);
            }
            if (action == 'add') {
                ConsumableForm.frmEl.imgviewcover.addClass("hide");
            }
            ConsumableForm.frmEl.id.val(null);
            ConsumableForm.frmEl.purchase_cost.val(null);
            ConsumableForm.frmEl.unit.empty().trigger("change");
            ConsumableForm.frmEl.company_id.empty().trigger("change");
            ConsumableForm.frmEl.category_id.empty().trigger("change");
            ConsumableForm.frmEl.manufacturer_id.empty().trigger("change");
            ConsumableForm.frmEl.location_id.empty().trigger("change");
            ConsumableForm.frmEl.currency.trigger("change");
            ConsumableForm.frmEl.invoice_id.empty().trigger("change");
            ConsumableForm.frmEl.department_id.empty().trigger("change");
            ConsumableForm.frmEl.supplier.empty().trigger('change');
            ConsumableForm.frmEl.threshold_alert_users.empty().trigger("change");
            $('#preview-image').hide();
            if (action == 'clone') {
                populateWithEntity($(e.relatedTarget).attr('data-id'), action);
            }

        }
        else if (action == 'edit') {
            // t.Categoryfill();
            count = 0;
            var entityId = $(e.relatedTarget).attr('data-id');
            $('#modalHead').html(config.translations.edit);
            $('#ConsumableForm').attr('action', config.url.update + '/' + entityId);
            $('#submitBtn').html(config.translations.edit);
            $('#ConsumableForm').find("#currency").val(window.config.default_currency_format);
            $('#ConsumableForm').find("input[name='unique_tag']").attr('readonly', true);
            $('#ConsumableForm').find("input[name='unique_tag']").prop('disabled', true);

            //populate values
            $.ajax({
                url: config.url.edit + '/' + entityId,
                data: {},
                async: false,
                dataType: "json",
                success: function (data) {
                    if (typeof data.status != "undefined") {
                        e.preventDefault();
                        if (data.status == 'error') {
                            sweetAlert('center', 'error', data);
                            // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                        }
                        return false;
                    }
                    ConsumableForm.frmEl.id.val(entityId);
                    ConsumableForm.frmEl.company_id.val(data.data.company_id);
                    if (data.data.totalAssignedQty != 0) {
                        ConsumableForm.frmEl.company_id.prop("disabled", true);
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'company_id_hidden',
                            name: 'company_id',
                            value: data.data.company_id
                        }).appendTo(ConsumableForm.frm);
                    } else {
                        ConsumableForm.frmEl.company_id.prop("disabled", false);
                    }
                    ConsumableForm.frmEl.name.val(data.data.name);
                    ConsumableForm.frmEl.image.val('');
                    $('#preview-image').show();
                    if (data.data.image) {
                        let $li = $("<li>")
                            .addClass("list-group-item d-flex align-items-center justify-content-between")
                            .attr("data-file", data.data.image);

                        let $img = $("<img>")
                            .attr("src", baseURL + "/storage/uploads/consumable/" + data.data.image)
                            .addClass("img-thumbnail")
                            .css({ width: "80px", height: "80px", objectFit: "cover", marginRight: "10px" });

                        let $link = $("<a>")
                            .attr("href", baseURL + "/storage/uploads/consumable/" + data.data.image)
                            .attr("target", "_blank")
                            .text(data.data.image);

                        let $left = $("<div>").append($img).append("<br>").append($link);

                        let $del = $("<button>")
                            .addClass("btn btn-sm btn-danger del-link")
                            .attr("data-id", entityId)
                            .attr('data-action', 'edit')
                            .html("Delete");

                        $li.append($left).append($del);
                        ConsumableForm.frmEl.images_list.html($li);
                    } else {
                        ConsumableForm.frmEl.images_list.html("<li class='list-group-item'>No files uploaded</li>");
                    }
                    // ConsumableForm.frmEl.category_id.val(data.data.category_id);
                    ConsumableForm.frmEl.manufacturer_id.val(data.data.manufacturer_id);
                    // ConsumableForm.frmEl.location_id.val(data.data.location_id);
                    order_no = data.data.order_number;
                    purchase_cost = data.data.purchase_cost;
                    currency_default = data.data.currency;
                    ConsumableForm.frmEl.order_number.val(order_no);
                    current_Purchase_date = data.data.purchase_date;
                    ConsumableForm.frmEl.purchase_date.datepicker("update", current_Purchase_date);
                    if (data.data.currency != "" && data.data.currency != null) {
                        ConsumableForm.frmEl.currency.val(data.data.currency);
                    }
                    ConsumableForm.frmEl.purchase_cost.val(data.data.purchase_cost);
                    ConsumableForm.frmEl.notes.val($.trim(data.data.notes));
                    ConsumableForm.frmEl.qty.attr("readonly", true).val(data.data.qty);
                    // ConsumableForm.frmEl.qty.prop("disabled", true).val(data.data.qty);
                    ConsumableForm.frmEl.reorder_limits.val(data.data.reorder_limits);
                    ConsumableForm.frmEl.unique_tag.val(data.data.unique_tag);
                    ConsumableForm.frmEl.consumable_thresholds.val(data.data.consumable_thresholds);
                    if (data.data.thresholds_alerts == 1) {
                        ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", true);
                    } else {
                        ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", false);
                    }
                    ConsumableForm.frmEl.company_id.trigger("change");
                    ConsumableForm.frmEl.manufacturer_id.trigger("change");
                    ConsumableForm.frmEl.currency.trigger("change");
                    if (typeof data.dropdown == "object" && typeof data.dropdown.company == "object" && data.dropdown.company != null) {
                        ConsumableForm.frmEl.company_id.append(new Option(data.dropdown.company.text, data.dropdown.company.id, true, true)).trigger("change");
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.department == "object" && data.dropdown.department != null) {
                        ConsumableForm.frmEl.department_id.append(new Option(data.dropdown.department.text, data.dropdown.department.id, true, true)).trigger("change");
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.location == "object" && data.dropdown.location != null) {
                        ConsumableForm.frmEl.location_id.append(new Option(data.dropdown.location.text, data.dropdown.location.id, true, true)).trigger("change");
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.manufacturer == "object" && data.dropdown.manufacturer != null) {
                        ConsumableForm.frmEl.manufacturer_id.append(new Option(data.dropdown.manufacturer.text, data.dropdown.manufacturer.id, true, true)).trigger("change");
                    }
                    ConsumableForm.frmEl.threshold_alert_users.trigger("change");
                    ConsumableForm.frmEl.threshold_alert_users.empty();
                    if (typeof data.dropdown == "object" && Array.isArray(data.dropdown.thresholdUserEmails) && data.dropdown.thresholdUserEmails.length > 0) {
                        data.dropdown.thresholdUserEmails.forEach(user => {
                            if (user.text) {
                                ConsumableForm.frmEl.threshold_alert_users.append(
                                    new Option(user.text, user.id, true, true)
                                );
                            }
                        });
                        ConsumableForm.frmEl.threshold_alert_users.trigger("change");
                    }

                    if (typeof data.dropdown == "object" && typeof data.dropdown.internal_place == "object" && data.dropdown.internal_place != null) {
                        t.internal_places.push(data.dropdown.internal_place.id);
                    }
                    ConsumableForm.frmEl.internal_place.trigger('change');
                    if (data.dropdown.supplier != null) {
                        current_supplier_name = data.dropdown.supplier.text;
                        current_supplier_id = data.dropdown.supplier.id;
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.supplier == "object" && data.dropdown.supplier != null) {
                        ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true));
                    }
                    ConsumableForm.frmEl.supplier.trigger("change");
                    if (data.data.requestable == 1) {
                        ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", true);
                    } else {
                        ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", false);
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.unit == "object" && data.dropdown.unit != null) {
                        ConsumableForm.frmEl.unit.append(new Option(data.dropdown.unit.text, data.dropdown.unit.id, true, true));
                    }
                    ConsumableForm.frmEl.unit.trigger("change");

                    ConsumableForm.frmEl.invoice_id.empty();
                    if (typeof data.dropdown == "object" && typeof data.dropdown.invoice == "object" && data.dropdown.invoice != null) {
                        ConsumableForm.frmEl.invoice_id.append(new Option(data.dropdown.invoice.text, data.dropdown.invoice.id, true, true)).trigger("change");
                    }
                    ConsumableForm.frmEl.invoice_id.trigger("change");
                    if (typeof data.dropdown == "object" && typeof data.dropdown.consumable == "object" && data.dropdown.consumable != null) {
                        ConsumableForm.frmEl.category_id.attr('data-noLoadField', true);
                        ConsumableForm.frmEl.category_id.append(new Option(data.dropdown.consumable.text, data.dropdown.consumable.id, true, true));
                        ConsumableForm.frmEl.category_id.attr('data-noLoadField', false);
                        ConsumableForm.frmEl.category_id.trigger("change");
                    }
                    clearCustomFields();
                    if (typeof data.custom_fields == "object" && typeof data.custom_fields.html != "") {
                        fillCustomFields(data.custom_fields);
                    }
                    if (data.data.image != null) {
                        if ((action == "edit" || action == "clone")) {
                            ConsumableForm.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + data.data.image);
                            ConsumableForm.frmEl.imgviewcover.removeClass("hide");
                            if (action == "clone") {
                                ConsumableForm.frmEl.clone_img.val(data.data.image);
                            }
                        }
                    } else {
                        ConsumableForm.frmEl.imgview.attr("src", "");
                        ConsumableForm.frmEl.imgviewcover.addClass("hide");
                    }
                }
            });
        }
    });

    t.Categoryfill = function () {
        if (typeof t.config.categories != "undefined") {
            ConsumableForm.frmEl.category_id.empty();
            $.each(t.config.categories, function (i, v) {
                ConsumableForm.frmEl.category_id.append(new Option(v.text, v.id));
            });
            ConsumableForm.frmEl.category_id.closest(".row").show();
            ConsumableForm.frmEl.category_id.trigger("change");
        }
    };
    t.Unitfill = function () {
        if (typeof t.config.units != "undefined") {
            ConsumableForm.frmEl.unit.empty();
            $.each(t.config.units, function (i, v) {
                ConsumableForm.frmEl.unit.append(new Option(v.text, v.id));
            });
            ConsumableForm.frmEl.unit.closest(".row").show();
            ConsumableForm.frmEl.unit.trigger("change");
        }
    };

    // $('.datepicker').datepicker({autoclose: true});
    var select2Opts = { width: "100%" };
    ConsumableForm.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    ConsumableForm.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.company_id.parent(),
        ajax: {
            url: t.config.url.getCompanyUsers,
            dataType: "json",
            delay: 300,
            data: function (p) { return { search: p.term, page: p.page || 1 }; }
        },
        placeholder: config.translations.select_the_company,
    }));
    // ConsumableForm.frmEl.category_id.select2($.extend({}, select2Opts, { dropdownParent: ConsumableForm.frmEl.category_id.parent(),}));
    ConsumableForm.frmEl.manufacturer_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.manufacturer_id.parent(),
        allowClear: true,
        ajax: {
            url: t.config.url.getManufacturerByQuery,
            dataType: "json",
            delay: 300,
            data: function (p) { return { search: p.term, page: p.page || 1 }; }
        },
        placeholder: config.translations.select_the_manufacturer,
    }));
    ConsumableForm.frmEl.currency.select2($.extend({}, select2Opts, { dropdownParent: ConsumableForm.frmEl.currency.parent(), }));
    // ConsumableForm.frmEl.company_id.select2(select2Opts);
    // ConsumableForm.frmEl.category_id.select2(select2Opts);
    // ConsumableForm.frmEl.manufacturer_id.select2(select2Opts);
    // ConsumableForm.frmEl.currency.select2(select2Opts);
    ConsumableForm.frmEl.company_id.on("change", function () {
        var companyId = $(this).val();

        if (companyId > 0) {
            ConsumableForm.frmEl.location_id.empty().trigger("change");
            ConsumableForm.frmEl.department_id.empty().trigger("change");
            ConsumableForm.frmEl.invoice_id.empty().trigger("change");
            ConsumableForm.frmEl.internal_place.empty().trigger("change");
            t.internal_places = [];
        }
    });
    ConsumableForm.frmEl.supplier.select2($.extend({}, { width: "100%" }, {
        dropdownParent: ConsumableForm.frmEl.supplier.parent(),
        ajax: {
            url: getSupplierByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_the_Supplier
    }));

    ConsumableForm.frmEl.threshold_alert_users.select2($.extend({}, select2Opts, {
        dropdownParent: consumableModal,
        ajax: {
            url: config.url.getActivatedUsers,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: "Select The User",
        templateResult: function (s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            var name = t.safeDisplayValue(s.text, "-");
            var imageUrl = t.safeDisplayValue(s.img_path, "");
            var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");
            return $(
                '<div class="d-flex align-items-center gap-2">' +
                avatarHtml +
                '<span>' + t.tblHelpers.escapeHtml(name) + '</span>' +
                '</div>'
            );
        }
    }));

    ConsumableForm.frmEl.unit.select2($.extend({}, { width: "100%" }, {
        dropdownParent: ConsumableForm.frmEl.unit.parent(),
        ajax: {
            url: config.url.getUnitsByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_the_unit,
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    }));
    ConsumableForm.frmEl.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.department_id.parent(),
        ajax: {
            url: t.config.url.getAssetDepartments,
            dataType: "json",
            transport: function (params, success, failure) {
                // console.log('value', ConsumableForm.frmEl.company_id.val());
                if (!ConsumableForm.frmEl.company_id.val()) {
                    success({ results: [] });
                    return;
                }
                let request = $.ajax(params);
                request.then(success);
                request.fail(failure);
                return request;
            },
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_department,
        language: {
            noResults: function () {
                if (!ConsumableForm.frmEl.company_id.val()) {
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    }));
    ConsumableForm.frmEl.location_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.location_id.parent(),
        ajax: {
            url: t.config.getLocationByAjax,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!ConsumableForm.frmEl.company_id.val()) {
                    success({ results: [] });
                    return;
                }
                let request = $.ajax(params);
                request.then(success);
                request.fail(failure);
                return request;
            },
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_location,
        language: {
            noResults: function () {
                if (!ConsumableForm.frmEl.company_id.val()) {
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    })).on("change", function () {
        t.getInternalPlace();
    });

    // Internal Place Dropdown Initialization (Single Initialization)
    ConsumableForm.frmEl.internal_place.select2($.extend({}, select2Opts, {
        placeholder: "Select the Internal Place",
        allowClear: true,
        dropdownParent: ConsumableForm.frmEl.internal_place.parent()
    }));
    // Function to Fetch Internal Places
    t.getInternalPlace = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        ConsumableForm.frmEl.internal_place.empty().append(new Option(config.translations.select_internal_place, "", true, true));
        ConsumableForm.frmEl.internal_place.trigger("change");
        var location_id = ConsumableForm.frmEl.location_id.val();

        if (location_id > 0) {
            $.get(t.config.getInternalPlaceByAjax + '/' + location_id).done(function (data) {
                if (typeof data == "object" && data.results.length) {
                    $.each(data.results, function (i, v) {
                        if ($.inArray(v.id, t.internal_places) !== -1) {
                            ConsumableForm.frmEl.internal_place.append(new Option(v.text, v.id, true, true));
                        } else {
                            ConsumableForm.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
                        }
                    });

                }
            }).always(function () {
                ConsumableForm.frmEl.internal_place.trigger("change");
                t.internal_places = [];
            });
        }
    };

    ConsumableForm.frmEl.category_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.category_id.parent(),
        ajax: {
            url: t.config.getCategoryByQuery,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    type: 'consumable',
                    all: 'all'
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_category,
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    }));
    ConsumableForm.frmEl.invoice_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.invoice_id.parent(),
        ajax: {
            url: getInvoiceByAjax,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!ConsumableForm.frmEl.company_id.val()) {
                    success({ results: [] });
                    return;
                }
                let request = $.ajax(params);
                request.then(success);
                request.fail(failure);
                return request;
            },
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_the_Purchase_Invoice,
        language: {
            noResults: function () {
                if (!ConsumableForm.frmEl.company_id.val()) {
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function (s, container) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr({ 'data-currency': s.currency, 'data-purchase-cost': s.purchase_cost, 'data-invoice_date': s.invoice_date, 'data-name': s.supplier_name, 'data-id': s.supplier_id, 'data-order-number': s.order_number, 'data-po-number': s.po_number });
            return s.text;
        }
    }))
    ConsumableForm.frmEl.invoice_id.on("change", function (e) {
        if (count >= 2 || ConsumableForm.frmEl.invoice_id.val() == null) {
            if (ConsumableForm.frmEl.invoice_id.val() == null) {
                count = count + 2;
                if (current_Purchase_date != null) {
                    purchasedate = current_Purchase_date.split('-').join('/')
                    ConsumableForm.frmEl.purchase_date.val(purchasedate);
                } else {
                    ConsumableForm.frmEl.purchase_date.val(null);
                }
                if (current_supplier_name != "" && current_supplier_id != "") {
                    ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
                } else {
                    ConsumableForm.frmEl.supplier.empty('').trigger('change');
                }
                if (order_no != null) {
                    ConsumableForm.frmEl.order_number.val(order_no);
                } else {
                    ConsumableForm.frmEl.order_number.val(null);
                }
                if (purchase_cost != null && purchase_cost != null) {
                    ConsumableForm.frmEl.purchase_cost.val(purchase_cost);
                } else {
                    ConsumableForm.frmEl.purchase_cost.val('');
                }
            } else {
                t.getPurchaseDate();
            }
        }
        count++;
    });

    t.getPurchaseDate = function (e) {
        //this part is added to handle re-selection from Select2 dropdown
        var selectedOption = ConsumableForm.frmEl.invoice_id.find(':selected');
        if (!selectedOption.attr('data-invoice_date')) {
            // Use stored default values for options without data attributes
            if (current_Purchase_date != null) {
                purchasedate = current_Purchase_date.split('-').join('/');
                ConsumableForm.frmEl.purchase_date.val(purchasedate);
            } else {
                ConsumableForm.frmEl.purchase_date.val('');
            }
            ConsumableForm.frmEl.supplier.empty();
            if (current_supplier_name && current_supplier_id) {
                ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
            }
            if (order_no != null) {
                ConsumableForm.frm.find("input[name='order_number']").val(order_no);
            } else {
                ConsumableForm.frm.find("input[name='order_number']").val('');
            }
            //for select2 Purchase Reference dropdown error fix
            if (purchase_cost != null) {
                ConsumableForm.frm.find("input[name='purchase_cost']").val(purchase_cost);
            } else {
                ConsumableForm.frm.find("input[name='purchase_cost']").val('');
            }
            if (currency_default != null) {
                ConsumableForm.frmEl.currency.val(currency_default).trigger('change');
            } else {
                ConsumableForm.frmEl.currency.val('').trigger('change');
            }
            return;
        }
        //end this part is added to handle re-selection from Select2 dropdown

        var date = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-invoice_date');
        ConsumableForm.frmEl.purchase_date.val(date);
        var supplier_name = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-name');
        var supplier_id = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-id');
        ConsumableForm.frmEl.supplier.append(new Option(supplier_name, supplier_id, true, true)).trigger("change");
        if (supplier_name == undefined && supplier_id == undefined && t.action == "edit") {
            ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
        }
        var order_number = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-po-number');
        ConsumableForm.frmEl.order_number.val(order_number);
        var purchase_cost_value = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-purchase-cost');
        ConsumableForm.frmEl.purchase_cost.val(purchase_cost_value || '0.00');
        var purchase_currency = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-currency');
        ConsumableForm.frmEl.currency.val(purchase_currency).trigger("change");
    }

    $(document).on('click', '.dtActDel', function (e) {
        e.preventDefault();
        if ($(this).attr("data-indent") != "consumable") {
            return false;
        }
        url = window.config.url.delete + "/" + $(this).attr('data-id');

        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(t.config.translations.are_you_want_delete, 'warning', url, dtblRef.dTbl, data);
    });

    let showDeletedConsumables = false;
    // t.showDeletedConsumables = false;

    t.btn = {};

    const iconsList = {
        icons: {
            ellipsis: function () {
                return '<svg width="5" height="14" viewBox="0 0 5 20" fill="none" ><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>';
            },
            edit: function () {
                return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
            },
            delete: function () {
                return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
            },
            clone: function () {
                return '<svg viewBox="0 0 16 16" fill="none" ><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"/></svg>';
            },
            view: function () {
                return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
            },
            restore: function () {
                return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g clip-path="url(#clip0_2515_18046)"><path d="M6.6784 20.5673C2.53239 18.0212 0.759002 12.7584 2.71775 8.1439C4.8757 3.0601 10.7463 0.68822 15.8301 2.84617C20.9139 5.00412 23.2858 10.8747 21.1278 15.9585C20.2846 17.945 18.8746 19.5174 17.1661 20.5673" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 16V20.4C17 20.7314 17.2686 21 17.6 21H22" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22.01L12.01 21.9989" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_2515_18046"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>';
            },
            checkout: function () {
                return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z" fill="currentColor"></path>
                        </svg>`;
            },
        }
    }

    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };

    t.tblHelpers = {
        renderActionsCell: function (record, type) {
            // let showDeletedConsumables = t.showDeletedConsumables;
            var t = this;
            var actionState = t.getRowActionState(record);
            var menuActions = t.buildRowActions(record);
            var quickActions = [];
            var dropdownId;

            if (type !== "display") return "";
            // console.log(showDeletedConsumables);

            if (showDeletedConsumables) {
                quickActions.push(t.quickActionButtonHtml(
                    (config.translations || {}).Restore_Consumable || "Restore Consumable",
                    "dtActRestore",
                    record.id,
                    "restore",
                    "",
                    'data-indent="consumable"'
                ));
                menuActions = [];
            } else {
                if (actionState.ConsumableEdit) {
                    quickActions.push(t.quickActionButtonHtmlEdit(
                        (config.translations || {}).Edit_Consumable || "Edit Consumable",
                        "dtActEdit",
                        record.id,
                        "edit",
                    ));
                }
                if (actionState.ConsumableDelete) {
                    quickActions.push(t.quickActionButtonHtml(
                        (config.translations || {}).Delete_Consumable || "Delete Consumable",
                        "dtActDel",
                        record.id,
                        "delete",
                        "is-delete",
                        'data-indent="consumable"'
                    ));
                }
            }

            if (!quickActions.length && !menuActions.length) {
                return '<span class="user-list-empty">-</span>';
            }

            dropdownId = "user-table-action-dropdown-" + record.id;

            return [
                '<div class="user-list-actions">',
                quickActions.join(""),
                menuActions.length ? [
                    '<div>',
                    '<button type="button" class="action-link user-list-menu-toggle" id="', dropdownId, '" aria-expanded="false" aria-haspopup="true" aria-label="More actions">',
                    this.getIcon("ellipsis"),
                    '</button>',
                    '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="', dropdownId, '">',
                    menuActions.join(""),
                    '</ul>',
                    '</div>'
                ].join("") : "",
                '</div>'
            ].join("");
        },
        quickActionButtonHtml: function (label, cls, id, iconKey, extraClass, extraAttributes = null) {
            return [
                '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '" data-bs-toggle="tooltip"', extraAttributes, '>',
                this.getIcon(iconKey),
                '</button>'
            ].join("");
        },
        quickActionButtonHtmlEdit: function (label, cls, id, iconKey, extraClass, extraAttributes = null) {
            return [
                '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '" data-bs-toggle="modal" action="edit" data-bs-target="#consumablemodal"', extraAttributes, '>',
                this.getIcon(iconKey),
                '</button>'
            ].join("");
        },
        getRowActionState: function (record) {
            return {
                ConsumableRestore: this.hasPermission("ConsumableRestore"),
                ConsumableEdit: this.hasPermission("ConsumableEdit"),
                ConsumableAdd: this.hasPermission("ConsumableAdd"),
                ConsumableDelete: this.hasPermission("ConsumableDelete"),
                ConsumableView: this.hasPermission("ConsumableView"),
                ConsumableCheckout: this.hasPermission("ConsumableCheckout"),
            };
        },
        escapeHtml: function (text) {
            return String(text === null || text === undefined ? "" : text).replace(
                /[&<>"'`=\/]/g,
                function (s) {
                    return entityMap[s];
                }
            );
        },
        buildRowActions: function (record) {
            var t = this;
            var id = record.id;
            let company_id = record.company_id;
            let name = record.name;
            var actionState = this.getRowActionState(record);
            var actions = [];
            var tr = config.translations || {};

            if (showDeletedConsumables) {
                actions.push(t.actionItemHtml(tr.Restore_Consumable || "Restore Consumable", "dtActRestore", id, "restore"));
                return actions;
            }

            // if (actionState.ConsumableEdit) actions.push(t.actionItemHtml(tr.Edit_Consumable || "Edit Consumable", "dtActEdit", id, "edit"));
            // if (actionState.ConsumableDelete) actions.push(t.actionItemHtml(tr.Delete_Consumable || "Delete Consumable", "dtActDel", id, "delete"));
            // if (actions.length && (actionState.ConsumableAdd || actionState.ConsumableView || actionState.ConsumableCheckout)) {
            //     actions.push(t.actionDividerHtml());
            // }
            if (actionState.ConsumableAdd) actions.push(t.actionItemHtmlModal(tr.Clone_Consumable || "Clone Consumable", "dtActClone", id, "clone", "consumablemodal"));
            if (actionState.ConsumableView) {
                actions.push([
                    '<li>',
                    '<a class="dropdown-item dtActView" ', 'href="', baseURL, '/consumable-info/', id, '" ', 'target="_blank" ', 'data-id="', id, '">',
                    t.getIcon("view"),
                    '<span class="b3-text">',
                    tr.Consumable_Detail || "View Consumable",
                    '</span>', '</a>', '</li>'].join(""));
            }
            let extraAttributesForCheckout = `data-name="${name}" data-company_id="${company_id}"`;
            if (actionState.ConsumableCheckout) actions.push(t.actionItemHtmlModal(tr.Checkout_Consumable || "Checkout Consumable", "dtActClone", id, "checkout", "checkoutconsumablemodal", extraAttributesForCheckout));

            return actions;
        },
        hasPermission: function (name) {
            return Array.isArray(config.permissions) && config.permissions.indexOf(name) !== -1;
        },
        getIcon: function (key) {
            return (iconsList.icons && iconsList.icons[key]) ? iconsList.icons[key]() : "";
        },
        actionItemHtml: function (label, cls, id, iconKey) {
            return [
                '<li>',
                '<a class="dropdown-item ', cls, '" href="#" data-id="', id, '">',
                this.getIcon(iconKey),
                '<span class="b3-text">', label, '</span>',
                '</a>',
                '</li>'
            ].join("");
        },
        actionItemHtmlModal: function (label, cls, id, iconKey, modalName, attributes = null) {
            return [
                '<li>',
                '<a class="dropdown-item ', cls, '" href="#" data-id="', id, '" action="clone" data-bs-target="#', modalName, '" data-bs-toggle="modal" data-original-title="', config.translations.Clone_Consumable, '" ', attributes, '>',
                this.getIcon(iconKey),
                '<span class="b3-text">', label, '</span>',
                '</a>',
                '</li>'
            ].join("");
        },
        actionDividerHtml: function () {
            return '<li><hr class="dropdown-divider"></li>';
        },
        consumable: function () {
            return function (d) {
                var a = [];
                a.push('<a href="' + baseURL + '/consumable-info/' + d.id + '" target="_blank" style="cursor: pointer; color: ' + (showDeletedConsumables === true ? '#ef0707' : '#309ef2ff') + '"><div><span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' + config.translations.Batch_No + '">' + d.con_batch_id + '</span>' + (d.unique_tag ? ' | <span data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' + config.translations.unique_tag + '">' + d.unique_tag + '</span>' : '') + '</div></a>');
                a.push('<a href="' + baseURL + '/consumable-info/' + d.id + '" target="_blank" style="cursor: pointer;" ><div class="deviceModelText" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' + config.translations.Consumable_Name + '">' + d.name + '</div></a>');
                a.push('<div class="textCategory" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' + config.translations.Category + '">' + d.cat_name + '</div>');
                a.push('<div class="deviceCompanyText" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Department Name">' + d.department + '</div>');
                a.push('<div class="textCategory" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="' + config.translations.Company_Name + '">' + d.cmp_name + '</div>');
                if (d.consumable_img != null) {
                    a.push(`<div><img class="img-sm" src="${d.consumable_img}"/></div>`);
                }
                return a.join(" ");
            }
        },
        purchasecost: function () {
            return function (d) {
                if (typeof currencies[d.currency] != "undefined")
                    return currencies[d.currency].symbol + ' ' + d.purchase_cost_format;
                else
                    return d.purchase_cost_format;
            }
        },
        purchaseInfo: function () {
            return function (d) {
                var a = [];
                if (d.purchase_date_on) {
                    a.push('<div class="deviceModelText text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Purchase Date">' + d.purchase_date_on + '</div>');
                }
                if (d.currency) {
                    a.push('<div class="textCategory text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Purchase Cost">' + (currencies[d.currency]?.symbol || '') + ' ' + d.purchase_cost_format + '</div>');
                } else {
                    a.push('<div class="textCategory text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Purchase Cost"> Rs ' + d.purchase_cost_format + '</div>');
                }
                if (d.order_number) {
                    a.push('<div class="deviceModelText text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Order Number">' + d.order_number + '</div>');
                }
                return a.join(" ");
            };
        },
        locationInfo: function () {
            return function (d) {
                var a = [];
                if (d.loc_name) {
                    a.push('<div class="deviceModelText text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Location">' + d.loc_name + '</div>');
                }
                if (d.internal_place) {
                    a.push('<div class="deviceModelText text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="internal Place">' + d.internal_place + '</div>');
                }
                if (d.loc_branch_code) {
                    a.push('<div class="deviceModelText text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Location Code">' + d.loc_branch_code + '</div>');
                }
                return a.join(" ");
            };
        }
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        lengthChange: false,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        },
        {
            targets: [3, 6, 7],
            class: 'text-left'
        }, {
            targets: 1,
            render: t.tblHelpers.consumable(),
            className: 'text-start'
        }, {
            targets: 2,
            render: t.tblHelpers.locationInfo()
        }, {
            targets: 7,
            render: t.tblHelpers.purchaseInfo()
        },
        {
            targets: 4,
            render: function (d) {
                var a = [];
                if ((d.consumable_thresholds > 0 && d.available_qty <= d.consumable_thresholds) ||
                    (d.catthreshold > 0 && d.available_qty <= d.catthreshold)) {
                    a.push("<span style='color: red;'>" + d.available_qty + "</span>");
                } else {
                    a.push("<span>" + d.available_qty + "</span>");
                }
                return a.join("");
            }
        }

        ],
        order: [
            [8, 'desc']
        ],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        ajax: {
            url: t.config.url.consumables,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.showDeletedConsumables = showDeletedConsumables;
                d.filters = t.config.other_filters;
                d.department = t.config.department_filter;
                d.location = t.config.location_filter;
                d.cat_id = t.config.cat_id_filter;
                d.type = t.config.type;
                d.q = t.config.purchaseFilter;
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            { data: 'a' },
            { data: 'a' },
            {
                data: 'a.qty',
            },
            // { data: 'a.available_qty' },
            // { data: 'a.purchase_date_on' },
            { data: 'a' },
            {
                data: 'a.scrap_qty',
            },
            {
                data: 'a.consumable_thresholds',
            },
            { data: 'a' },
            {
                data: 'a.last_updated_at',
            },
            // { data: 'a' },
            {
                data: "a.id",
                className: "amg-col-actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return t.tblHelpers.renderActionsCell(row.a || {}, type);
                }
            }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#mytable_filter").remove();
            $("#mytable_length").find("select").select2();

            $("#tableSearch").on("keyup", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(v).draw();
                }
            });

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();

                api.columns().every(function () {
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;

                    controls.append(`
                        <div class="dropdown-item text-nowrap">
                            <label class="d-inline-flex align-items-center gap-2 mb-0">
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

            t.btn.search = t.content.find(".amg-list-searchbar__icon");
            t.btn.export = t.content.find(".btn-export-consumables");
            t.btn.export_pdf = t.content.find(".btn-export-consumables-pdf");
            t.btn.reload = t.content.find(".btn-reload-list");
            t.btn.bulk_checkout = t.content.find(".btn-bulk-checkout");
            t.btn.bulk_checkin = t.content.find(".btn-bulk-checkin");
            t.btn.import = t.content.find(".btn-import-consumables");
            t.btn.deletedConsumable = t.content.find(".btn-deleted-consumable");
            t.btn.openFilter = t.content.find(".btn-open-filter");
            t.show_entries = t.content.find('#showSelect');
        },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        }
    });

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#tableSearch").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.switchCheckTarget = function () {
        if (ChkoutConsumableMdl.frmEl.assigned_for.val() == "1") {
            ChkoutConsumableMdl.frmEl.device_id.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").show();
            ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.assigned_to.rules("add", { required: true });
            ChkoutConsumableMdl.frmEl.assigned_place.rules("remove", "required");
            ChkoutConsumableMdl.frmEl.device_id.rules("remove", "required");
        } else if (ChkoutConsumableMdl.frmEl.assigned_for.val() == "2") {
            ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").show();
            ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.device_id.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.assigned_place.rules("add", { required: true });
            ChkoutConsumableMdl.frmEl.assigned_to.rules("remove", "required");
            ChkoutConsumableMdl.frmEl.device_id.rules("remove", "required");
        } else {
            ChkoutConsumableMdl.frmEl.device_id.closest(".cover").show();
            ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").hide();
            ChkoutConsumableMdl.frmEl.device_id.rules("add", { required: true });
            ChkoutConsumableMdl.frmEl.assigned_to.rules("remove", "required");
            ChkoutConsumableMdl.frmEl.assigned_place.rules("remove", "required");
        }
    };
    ChkoutConsumableMdl.frmEl.assigned_for.on("change", $.proxy(t.switchCheckTarget));

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    }

    t.exportPDF = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url_pdf + "?q=" + t.config.export_filters;
    }

    t.bulkCheckout = function (e) {
        e.preventDefault();
        window.location = t.config.url.bulk_checkout;
    };

    t.bulkCheckin = function (e) {
        e.preventDefault();
        window.location = t.config.url.bulk_checkin;
    };

    t.importConsumables = function (e) {
        e.preventDefault();
        window.location = t.config.url.import;
    };

    $(document).on('click', '.dtActRestore', function (e) {
        e.preventDefault();
        if ($(this).attr("data-indent") != "consumable") {
            return false;
        }
        url = window.config.url.restore + "/" + $(this).attr('data-id');
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(t.config.translations.are_you_restore, 'warning', url, dtblRef.dTbl, data);
    });

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.filters.fun = {
        reload_location: function () {
            t.filters.location.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.location.parent(),
                ajax: {
                    url: t.config.getLocationByAjax,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                // minimumInputLength: 1,
                placeholder: config.translations.select_the_location,
                templateSelection: function (data, container) {
                    $(container).attr('title', data.text);
                    return data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
                },
            }));
        },
        reload_categories: function () {
            // t.filters.categories.empty().append(new Option(parent.config.translations.No_Filter, null, false, false));
            $.each(t.config.categories, function (i, k) {
                t.filters.categories.append(new Option(k.text, k.id, false, false));
            });
            t.filters.categories.trigger("change");
        },
        purchase_reference: function () {
            t.filters.purchase_reference.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.purchase_reference.parent(),
                ajax: {
                    url: getInvoiceByAjax,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_Purchase_Invoice,
                templateSelection: function (s, container) {
                    if (typeof s.loading != "undefined" && s.loading) {
                        return $("<div>" + s.text + "</div>");
                    }
                    $(s.element).attr({ 'data-invoice_date': s.invoice_date, 'data-name': s.supplier_name, 'data-id': s.supplier_id, 'data-order-number': s.order_number, 'data-po-number': s.po_number, 'data-purchase-cost': s.bill_amount, 'data-currency': s.currency });
                    return s.text;
                }
            }));
        }
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#tableSearch").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert(config.translations.please_enter_valid_search);
                return false;
            }
            t.config.search = v;
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.cache_filter_values = function () {
        var v = $("#tableSearch").validate_str_param();
        t.config.search = v;
        t.config.other_filters = {};
        t.config.dashboard_filters = {};
        t.config.dashboard_filters.department = t.config.department_filter;
        t.config.dashboard_filters.location = t.config.location_filter;
        t.config.dashboard_filters.cat_id = t.config.cat_id_filter;
        t.config.dashboard_filters.type = t.config.type;
        if (t.filters.categories.val() && t.filters.categories.val() != 'null')
            t.config.other_filters.categories = t.filters.categories.val();
        if (t.filters.purchase_reference.val() && t.filters.purchase_reference.val() != 'null')
            t.config.other_filters.purchase_reference = t.filters.purchase_reference.val();
        if (t.filters.location.val() && t.filters.location.val() != 'null')
            t.config.other_filters.location = t.filters.location.val();
        if (t.filters.based_on.val() && t.filters.based_on.val() != 'null')
            t.config.other_filters.based_on = t.filters.based_on.val();
        if (t.filters.date_range.val() && t.filters.date_range.val() != 'null')
            t.config.other_filters.date_range = t.filters.date_range.val();
        if (t.filters.assetDepartment.val() && t.filters.assetDepartment.val() != 'null')
            t.config.other_filters.asset_department = t.filters.assetDepartment.val();
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters, 'dashboard_filters': t.config.dashboard_filters, 'showDeletedConsumables': showDeletedConsumables, "over_all_purchase_filter": t.config.purchaseFilter };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.based_on.val(), false);
        this.updateFilterBadge();
        var modalInstance = bootstrap.Modal.getInstance(document.getElementById("consumableFilterModal"));
        if (modalInstance) { modalInstance.hide(); }
    };

    t.clear = function () {
        t.config.other_filters = {};
        t.filters.location.val(0).trigger("change");
        t.filters.categories.val(0).trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.filters.assetDepartment.empty("").trigger("change");
        t.filters.purchase_reference.val("null").trigger("change");
        t.cache_filter_values();
        // resetDateRangeFilter();
        resetFilterCount();
        // $('#advance-filters').collapse('hide');
        t.reload();
    }

    function resetFilterCount() {
        t.filters.badge.addClass("d-none").text("0");
    }

    t.filters.assetDepartment.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.assetDepartment.parent(),
        ajax: {
            url: t.config.url.getAssetDepartments,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        // minimumInputLength: 1,
        placeholder: config.translations.filter_by_department,
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    }));

    t.toggleDeletedConsumable = function () {
        showDeletedConsumables = showDeletedConsumables == false ? true : false;
        // var lblTxt = t.showDeletedConsumables == false ? '<i class="ps-icon fa fa-recycle"></i>' : '<i class="ps-icon fa fa-bolt deleted"></i>';
        $('.non-deleted-icon').toggleClass("d-none");
        $('.deleted-icon').toggleClass('d-none');
        var title = showDeletedConsumables == false ? config.translations.show_deleted_consumables : config.translations.show_non_deleted_consumables;
        // t.btn.deletedConsumable.html(lblTxt);
        t.btn.deletedConsumable.attr("data-original-title", title);
        t.reload();
    };

    t.filters.location.select2($.extend({}, select2Opts, { placeholder: "Filter By Location" }));
    t.filters.categories.select2($.extend({}, select2Opts, {
        dropdownParent: $('#consumableFilterModal'),
        placeholder: "Filter By Category",
        ajax: {
            url: t.config.getCategoryByQuery,
            dataType: "json",
            delay: 300,
            data: function (p) { return { search: p.term, page: p.page || 1 }; }
        },
    }));
    t.filters.based_on.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_Based_on, dropdownParent: t.filters.wrapper }));
    t.filters.fun.reload_location();
    t.filters.fun.reload_categories();
    t.filters.fun.purchase_reference();
    // t.filters.wrapper.on('click', '#filter_by_from_date');
    // t.filters.wrapper.on('click', '#filter_by_to_date');
    t.filters.wrapper.on("click", "#apply_filter", t.search);
    t.filters.wrapper.on("click", "#clear", t.clear);

    // need to uncomment later
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.export.on("click", $.proxy(t.export));
    t.btn.export_pdf.on("click", $.proxy(t.exportPDF));
    t.btn.search.on("click", $.proxy(t.tableSearch));
    t.btn.bulk_checkout.on("click", $.proxy(t.bulkCheckout))
    t.btn.bulk_checkin.on("click", $.proxy(t.bulkCheckin))
    t.btn.import.on("click", $.proxy(t.importConsumables));
    t.btn.deletedConsumable.on("click", $.proxy(t.toggleDeletedConsumable));
    // ChkoutConsumableMdl.frmEl.assigned_for.val("1").trigger('change');

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.showHideButton.on("click", (e) => t.showHide(e));

    t.showHide = function (e) {
        e.preventDefault();
        $('#columnVisibilityControls').toggle();
    };

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
            $('#columnVisibilityControls').hide();
        }
    });

    t.btn.openFilter.off('click').on('click', function (e) {
        e.preventDefault();
        t.openFilterModal();
    });

    t.openFilterModal = function () {
        if (this.filters.wrapper.length) {
            this.filters.wrapper.modal("show");
            t.initDateRangePicker();
        }
    };

    t.initDateRangePicker = function () {
        if (!t.filters.reportrange.length || typeof moment === "undefined" || !$.fn.daterangepicker) { return; }
        if (t.filters.reportrange.data('daterangepicker')) {
            t.syncDateRangePicker();
            return;
        }
        if (t.filters.rangePicker) { t.syncDateRangePicker(); return; }
        var start = moment().startOf("day");
        var end = moment().endOf("day");


        t.filters.reportrange.daterangepicker({
            parentEl: t.filters.wrapper,
            startDate: start,
            endDate: end,
            timePicker: true,
            timePicker24Hour: true,
            timePickerSeconds: true,
            timePickerIncrement: 1,
            autoApply: false,
            autoUpdateInput: false,
            opens: "right",
            drops: "down",
            locale: {
                format: "DD-MM-YYYY HH:mm:ss",
                cancelLabel: "Clear"
            },
            ranges: {
                "Today": [moment().startOf("day"), moment().endOf("day")],
                "Yesterday": [moment().subtract(1, "days").startOf("day"), moment().subtract(1, "days").endOf("day")],
                "Last 7 Days": [moment().subtract(6, "days").startOf("day"), moment().endOf("day")],
                "Last 30 Days": [moment().subtract(29, "days").startOf("day"), moment().endOf("day")],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, cb);
        cb(start, end);

        t.syncDateRangePicker = function () {
            if (!t.filters.rangePicker) return;
            var val = t.filters.daterange.val();
            if (!val) {
                t.clearDateRange();
                return;
            }
            var parts = val.split(" - ");
            if (parts.length !== 2) return;
            var start = moment(parts[0], "YYYY-MM-DD HH:mm:ss");
            var end = moment(parts[1], "YYYY-MM-DD HH:mm:ss");
            t.setDateRange(start, end);
        };

        t.setDateRange = function (start, end) {
            if (!start || !end) { t.clearDateRange(); return; }
            if (t.filters.reportrangeText.length) {
                t.filters.reportrangeText.text(
                    start.format("DD-MM-YYYY HH:mm:ss") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
                );
            }
            t.filters.daterange.val(
                start.format("YYYY-MM-DD HH:mm:ss") + " - " + end.format("YYYY-MM-DD HH:mm:ss")
            );
        };
    };

    t.updateFilterBadge = function () {
        var filterCount = 0;
        var filters = t.config.other_filters || {};
        if (filters.location && filters.location !== "null") {
            filterCount += 1;
        }

        if (filters.date_range && filters.based_on) {
            filterCount += 1;
        }

        if (Array.isArray(filters.categories) && filters.categories.length) {
            filterCount += 1;
        }

        if (Array.isArray(filters.purchase_reference) && filters.purchase_reference.length) {
            filterCount += 1;
        }

        if (Array.isArray(filters.asset_department) && filters.asset_department.length) {
            filterCount += 1;
        }

        if (!t.filters.badge.length) {
            return;
        }

        if (filterCount > 0) {
            t.filters.badge.text(filterCount).removeClass("d-none");
        } else {
            t.filters.badge.addClass("d-none").text("0");
        }
    };

    t.getAvatarHtml = function (name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";
        var safeName = t.tblHelpers.escapeHtml(this.safeDisplayValue(name, "User"));

        if (this.isFilledValue(imageUrl)) {
            return '<img src="' + t.tblHelpers.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + t.tblHelpers.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + t.tblHelpers.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + t.tblHelpers.escapeHtml(this.getUserInitials(name)) + '</span>';
    }

    t.safeDisplayValue = function (value, fallback) {
        return this.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.getUserInitials = function (name) {
        var value = String(this.safeDisplayValue(name, "")).trim();
        if (!value) return "NA";
        return value.charAt(0).toUpperCase();
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    $(document).on('click', '.del-link', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var deleteUrl = config.url.image_delete + '/' + id;
        var $currentLi = $(this).closest('li');

        Swal.fire({
            title: config.translations.are_you_delete_attachment,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5bd810",
            cancelButtonColor: "#cc3333",
            confirmButtonText: "Yes, Delete",
            cancelButtonText: "Cancel",
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                if ($(this).attr('data-action') == "clone") {
                    $currentLi.fadeOut(300, function () {
                        $(this).remove();
                        if ($('#consumables-image-list').children().length === 0) {
                            $('#consumables-image-list').append("<li class='list-group-item'>No files uploaded</li>");
                        }
                        $('#delete_img').val("1");
                    });
                    return;
                }
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: { _token: config.token },
                    success: function (response) {
                        if (response.status === 'success') {
                            $currentLi.fadeOut(300, function () {
                                $(this).remove();
                                if (ConsumableForm.frmEl.images_lists.children().length === 0) {
                                    ConsumableForm.frmEl.images_list.append("<li class='list-group-item'>No files uploaded</li>");
                                }
                            });
                            t.mdl.modal('hide');
                            // sweetAlert('center', 'success', response);
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: response.msg || "Deleted successfully",
                                confirmButtonText: "OK",
                            });
                        } else {
                            // sweetAlert('center', 'error', response);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.msg || "Delete failed",
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function () {
                        var data = { msg: config.translations.something_went_wrong };
                        // sweetAlert('center', 'error', data);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.msg || "Delete failed",
                            confirmButtonText: "OK",
                        });
                    }
                });
            }
        });
    });

    t.updateFilterBadge();
    t.dTbl.ajax.reload();

    // the entire js written below is for clicking the all options section in actions.

    // this function is used so that anywhere other than the options button is clicked then the options should become hidden in this case
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#detached-action-menu').length && !$(e.target).closest('.user-list-menu-toggle').length) {
            $('#detached-action-menu').remove();
            $('.user-list-menu-toggle').data('menu-open', false);
        }
    });

    // this function is used for what happens when we click on the all options button
    $(document).on('click', '.user-list-menu-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $btn = $(this);

        if ($btn.data('menu-open')) {
            $('#detached-action-menu').remove();
            $btn.data('menu-open', false);
            return;
        }

        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);

        var $menu = $btn.siblings('.dropdown-menu').clone(true);
        $menu.attr('id', 'detached-action-menu').addClass('show');

        $('body').append($menu);

        var btnRect = $btn[0].getBoundingClientRect();
        var menuWidth = 200;

        $menu.css({
            position: 'fixed',
            top: btnRect.bottom + 'px',
            left: (btnRect.left - menuWidth + btnRect.width) + 'px',
            zIndex: 99999,
            display: 'block'
        });

        setTimeout(function () {
            var menuHeight = $menu.outerHeight();
            var windowHeight = $(window).height();
            if (btnRect.bottom + menuHeight > windowHeight) {
                $menu.css('top', (btnRect.top - menuHeight) + 'px');
            }

            var menuLeft = parseFloat($menu.css('left'));
            if (menuLeft < 0) {
                $menu.css('left', btnRect.left + 'px');
            }
        }, 0);

        $btn.data('menu-open', true);
    });

    // this function is used for what happens when we click on the elements inside the all options like edit, delete, clone, checkout etc.
    $(document).on('click', '#detached-action-menu .dropdown-item', function (e) {
        if (!($(this).hasClass('dtActView'))) {
            e.preventDefault();
            e.stopPropagation();

            var $item = $(this);
            var id = $item.data('id');
            var bsTarget = $item.attr('data-bs-target');
            var action = $item.attr('action');

            $('#detached-action-menu').remove();
            $('.user-list-menu-toggle').data('menu-open', false);

            if (bsTarget) {
                var $modal = $(bsTarget);
                if ($modal.length) {
                    $modal.data('id', id);
                    $modal.data('action', action);
                    var modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
                    modalInstance.show();
                }
                return;
            }

            var actionClass = null;
            ['dtActEdit', 'dtActDel', 'dtActClone', 'dtActRestore'].forEach(function (cls) {
                if ($item.hasClass(cls)) actionClass = cls;
            });

            if (actionClass && id) {
                $('#mytable').find('.' + actionClass + '[data-id="' + id + '"]').first().trigger('click');
            }
        }
    });

    // this function is used such that when we scroll, the extra options should become hidden if it is visible in this case.
    $(document).on('scroll', function () {
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);
    });

    // using this for adjusting whenever any api is called for datatable
    $(document).on('draw.dt', function (e, settings) {
        new $.fn.dataTable.Api(settings).columns.adjust();
    });

    consumabletabs.find('a[data-bs-toggle="tab"]').on('hide.bs.tab', function (e) {
        const $modal = $(this).closest('.modal');
        $modal.find('select.select2-hidden-accessible').each(function () {
            $(this).select2('close');
        });
        $modal.find('.modal-body').off('scroll.select2');
    });
};
