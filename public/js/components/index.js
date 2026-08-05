var paginationListCheckedData = [];
var MyApp = function (config) {
    var t = this;
    console.log('dcdsc', config.url.components);
    t.config = config;
    t.content = $("main.main-content");
    t.table = t.content.find("#mytable");
    t.print_label_starter = t.content.find("#print_label_starter");

    t.httpCall = true;
    t.httpPostPath = "";
    t.btn = {};

    t.showDeletedComponents = false;

    t.filters = {
        wrapper: t.content.find("#componentFilterModal"),
        companies: null,
        location: null,
        internal_place: null,
        categories: null,
        condition: null,
        assigned_device: null,
        assigned_user: null,
        origin_info: null,
        based_on: null,
        date_range: null,
        reportrange: t.content.find('#reportrange'),
        badge: t.content.find(".filter-count-badge"),
        assetDepartment: null,
        purchase_reference: null
    };

    // Initialize filter elements
    t.filters.companies = t.filters.wrapper.find("#filter_by_company");
    t.filters.location = t.filters.wrapper.find("#filter_by_location");
    t.filters.internal_place = t.filters.wrapper.find("#filter_by_internal_place");
    t.filters.categories = t.filters.wrapper.find("#filter_by_category");
    t.filters.condition = t.filters.wrapper.find("#filter_by_con");
    t.filters.assigned_device = t.filters.wrapper.find("#filter_by_device");
    t.filters.assigned_user = t.filters.wrapper.find("#filter_by_user");
    t.filters.origin_info = t.filters.wrapper.find("#filter_by_origin_info");
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
    t.filters.date_range = t.filters.wrapper.find("#daterange");
    t.filters.assetDepartment = t.filters.wrapper.find("#filter_by_department");
    t.filters.purchase_reference = t.filters.wrapper.find("#filter_by_purchase_reference");

    t.btn.filter = t.filters.wrapper.find('#filter');
    t.btn.clear = t.filters.wrapper.find('#clear');

    // Merge items for print label
    t.merge_items = t.merge_items || { others: [] };
    t.config.print_component_array = t.config.print_component_array || [];
    t.config.data_print = t.config.data_print || [];

    // Component Modal
    var ComponentForm = {};
    ComponentForm.frm = $("#component-mdl-frm");
    ComponentForm.frmEl = {};
    ComponentForm.frmEl.id = ComponentForm.frm.find("#id");
    ComponentForm.frmEl.company = ComponentForm.frm.find("#company_id");
    ComponentForm.frmEl.category = ComponentForm.frm.find("#category_id");
    ComponentForm.frmEl.supplier = ComponentForm.frm.find("#supplier_id");
    ComponentForm.frmEl.location = ComponentForm.frm.find("#location_id");
    ComponentForm.frmEl.internal_place = ComponentForm.frm.find("#internal_place");
    ComponentForm.frmEl.status = ComponentForm.frm.find("#status");
    ComponentForm.frmEl.checkout_add = ComponentForm.frm.find(".checkout_add");
    ComponentForm.frmEl.notes = ComponentForm.frm.find("#notes");
    ComponentForm.frmEl.origin_from = ComponentForm.frm.find("#origin_from");
    ComponentForm.frmEl.purchase_date = ComponentForm.frm.find("#purchase_date");
    ComponentForm.frmEl.assigned_for = ComponentForm.frm.find("#assigned_for");
    ComponentForm.frmEl.checked_out_to_user = ComponentForm.frm.find("#checked_out_to_user");
    ComponentForm.frmEl.checked_out_to = ComponentForm.frm.find("#checked_out_to");
    ComponentForm.frmEl.checked_out_at = ComponentForm.frm.find("#checked_out_at");
    ComponentForm.frmEl.unique_tag = ComponentForm.frm.find("#unique_tag");
    ComponentForm.frmEl.purchase_currency = ComponentForm.frm.find("#purchase_currency");
    ComponentForm.frmEl.invoice_id = ComponentForm.frm.find("#invoice_id");
    ComponentForm.frmEl.parent_device = ComponentForm.frm.find("#parent_device");
    ComponentForm.frmEl.department_id = ComponentForm.frm.find("#department_id");
    ComponentForm.frmEl.forAction = ComponentForm.frm.find("#forAction");
    ComponentForm.frmEl.serial = ComponentForm.frm.find("#serial");
    ComponentForm.frmEl.name = ComponentForm.frm.find("#name");
    ComponentForm.frmEl.order_number = ComponentForm.frm.find("#order_number");
    ComponentForm.frmEl.purchase_cost = ComponentForm.frm.find("#purchase_cost");
    ComponentForm.frmEl.images_list = ComponentForm.frm.find('#component-image-list');
    ComponentForm.frmEl.customFieldsPrvEl = ComponentForm.frm.find(".custom-fields-follow");
    ComponentForm.frmEl.imgviewcover = ComponentForm.frm.find(".imgviewcover");
    ComponentForm.frmEl.imgview = ComponentForm.frm.find("#imgview");
    ComponentForm.frmEl.clone_img = ComponentForm.frm.find("#clone_img");
    ComponentForm.frmEl.image = ComponentForm.frm.find("#image");

    var count = 0;
    var current_purchase_date = "";
    var current_supplier_name = current_supplier_id = "";
    var order_no = purchase_cost = currency_default = "";
    t.internal_places = [];
    t.forAction = '';

    // Checkout Modal
    var ChkoutComponentMdl = $("#component-checkout-mdl");
    ChkoutComponentMdl.frm = ChkoutComponentMdl.find("#component-checkout-mdl-frm");
    ChkoutComponentMdl.frmEl = {};
    ChkoutComponentMdl.frmEl.id = ChkoutComponentMdl.frm.find("#id");
    ChkoutComponentMdl.frmEl.assigned_for = ChkoutComponentMdl.frm.find("#assigned_for");
    ChkoutComponentMdl.frmEl.assigned_to = ChkoutComponentMdl.frm.find("#assigned_to");
    ChkoutComponentMdl.frmEl.device_id = ChkoutComponentMdl.frm.find("#device_id");
    ChkoutComponentMdl.frmEl.checkout_at = ChkoutComponentMdl.frm.find("#checked_out_at");
    ChkoutComponentMdl.frmEl.expected_checkin = ChkoutComponentMdl.frm.find("#expected_checkin_at");
    ChkoutComponentMdl.frmEl.note = ChkoutComponentMdl.frm.find("#note");
    ChkoutComponentMdl.company_id = null;

    // Checkin Modal
    var ChkinComponentMdl = $("#component-checkin-mdl");
    ChkinComponentMdl.frm = ChkinComponentMdl.find("#component-checkin-mdl-frm");
    ChkinComponentMdl.frmEl = {};
    ChkinComponentMdl.frmEl.id = ChkinComponentMdl.frm.find("#id");
    ChkinComponentMdl.frmEl.checkoutinfo = ChkinComponentMdl.frm.find("#checkoutinfo");
    ChkinComponentMdl.frmEl.assigned_for = ChkinComponentMdl.frm.find("#assigned_for");
    ChkinComponentMdl.frmEl.checked_in_at = ChkinComponentMdl.frm.find("#checked_in_at");
    ChkinComponentMdl.frmEl.note = ChkinComponentMdl.frm.find("#note");

    // Print Modal
    var PrintMdl = $("#print-modal");
    PrintMdl.frm = PrintMdl.find("#print-modal-form");
    PrintMdl.frmEl = {};
    PrintMdl.frmEl.print_opt = PrintMdl.frm.find("#print_opt");
    PrintMdl.frmEl.print_usr_plc = PrintMdl.frm.find("#print_usr_plc");
    PrintMdl.frmEl.print_usr_info = PrintMdl.frm.find("#print_usr_info");
    PrintMdl.frmEl.print_usr_info_cvr = PrintMdl.frmEl.print_usr_info.closest('.cvr');

    // Component Form Validation
    ComponentForm.frmValidator = ComponentForm.frm.validate({
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
                maxlength: 100,
                clean_text_only: true
            },
            name: {
                maxlength: 100,
                required: true,
                clean_text_only: true
            },
            category_id: {
                required: true,
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
            purchase_currency: {
                required: false,
            },
            purchase_cost: {
                min: 0,
                number: true
            },
            serial: {
                required: true,
                clean_text_only: true
            },
            status: {
                required: true,
            },
            origin_from: {
                required: false,
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

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    // var originalResetForm = ComponentForm.frmValidator.resetForm;
    // ComponentForm.frmValidator.resetForm = function () {
    //     originalResetForm.call(this);
    //     $('#component-mdl-frm').find(':input').each((_, element) => {
    //         this.settings.unhighlight.call(
    //             this,
    //             element,
    //             this.settings.errorClass,
    //             this.settings.validClass
    //         );
    //     });
    // };

    $(document).on('select2:select select2:unselect', 'select', function () {
        var $form = $(this).closest('form');
        if ($form.is('#component-mdl-frm')) {
            ComponentForm.frmValidator.element(this);
        }
        if ($form.is('#component-checkout-mdl-frm')) {
            ChkoutComponentMdl.frmValidator.element(this);
        }
    });

    // Checkout Form Validation
    ChkoutComponentMdl.frmValidator = ChkoutComponentMdl.frm.validate({
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
            checked_out_at: {
                required: true
            },
            note: {
                clean_text_only: true,
                remarks: true
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

    // Checkin Form Validation
    ChkinComponentMdl.frmValidator = ChkinComponentMdl.frm.validate({
        onsubmit: false,
        onkeyup: function (element, event) {
            this.element(element);
        },
        ignore: ':hidden:not(.select2-hidden-accessible)',
        errorClass: 'error',
        rules: {
            checked_in_at: {
                required: true
            },
            note: {
                clean_text_only: true,
                remarks: true
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

    // ==================== MODAL EVENT HANDLERS ====================

    // Component Modal - Show
    $("#component-mdl").on('show.bs.modal', function (e) {
        $('label.msg').remove();
        current_purchase_date = "";
        order_no = "";
        var action = $(e.relatedTarget).attr('action');
        ComponentForm.frmValidator.resetForm();

        if (action == 'add' || action == 'clone') {
            ComponentForm.frmEl.company.prop("disabled", false);
            $('#component-mdl .modal-title').html(config.translations.add_new_component || 'Add Component');
            $('#btnSubmit').html(config.translations.save || 'Save');
            $('#component-mdl-frm').attr('action', t.config.url.add);
            $('#component-mdl-frm')[0].reset();
            $('#component-mdl-frm').find("#purchase_currency").val(window.config.default_currency_format);
            $('#component-mdl-frm').find("input[name='unique_tag']").attr('readonly', false);
            ComponentForm.frmEl.serial.attr("readonly", false);
            t.clearCustomFields();

            if (typeof t.config.custom_fields == "object" && typeof t.config.custom_fields.html != "") {
                t.fillCustomFields(t.config.custom_fields);
            }
            if (action == 'add') {
                ComponentForm.frmEl.imgviewcover.addClass("hide");
            }
            ComponentForm.frmEl.id.val(null);
            ComponentForm.frmEl.purchase_cost.val(null);
            ComponentForm.frmEl.company.trigger("change");
            ComponentForm.frmEl.category.empty().trigger("change");
            ComponentForm.frmEl.location.empty().trigger("change");
            ComponentForm.frmEl.purchase_currency.trigger("change");
            ComponentForm.frmEl.invoice_id.empty().trigger("change");
            ComponentForm.frmEl.department_id.empty().trigger("change");
            ComponentForm.frmEl.supplier.empty().trigger('change');
            ComponentForm.frmEl.status.empty().trigger("change");
            ComponentForm.frmEl.origin_from.empty().trigger("change");
            ComponentForm.frmEl.internal_place.empty().trigger("change");
            ComponentForm.frmEl.parent_device.empty().trigger("change");
            ComponentForm.frmEl.checked_out_to_user.empty().trigger("change");
            ComponentForm.frmEl.checked_out_to.empty().trigger("change");
            ComponentForm.frmEl.forAction.val("");
            $('#component-image-list').html("<li class='list-group-item'>No files uploaded</li>");

            if (action == 'clone') {
                t.populateWithEntity($(e.relatedTarget).attr('data-id'), action);
            }
        }
        else if (action == 'edit') {
            count = 0;
            var entityId = $(e.relatedTarget).attr('data-id');
            $('#component-mdl .modal-title').html(config.translations.edit_component || 'Edit Component');
            $('#component-mdl-frm').attr('action', t.config.url.edit + '/' + entityId);
            $('#btnSubmit').html(config.translations.edit || 'Update');
            $('#component-mdl-frm').find("#purchase_currency").val(window.config.default_currency_format);
            $('#component-mdl-frm').find("input[name='unique_tag']").attr('readonly', true);

            $.ajax({
                url: t.config.url.get + '/' + entityId + '/edit',
                data: {},
                async: false,
                dataType: "json",
                success: function (data) {
                    if (typeof data.status != "undefined") {
                        e.preventDefault();
                        if (data.status == 'error') {
                            sweetAlert('center', 'error', data);
                        }
                        return false;
                    }
                    t.loadForm(data, "edit");
                }
            });
        }
    });

    t.populateWithEntity = function (id, action) {
        ComponentForm.frmValidator.resetForm();
        $.getJSON(t.config.url.get + '/' + id + '/clone', function (data) {
            if (typeof data == "object" && data.status == "success") {
                t.loadForm(data, "clone");
            }
        });
    };

    t.loadForm = function (dev, forAction) {
        // Reset form first
        t.resetFrm();

        ComponentForm.frmEl.id.val(dev.data.id);
        ComponentForm.frmEl.company.val(dev.data.company_id).trigger("change");
        ComponentForm.frmEl.name.val(dev.data.name);
        ComponentForm.frmEl.serial.val(dev.data.serial);
        ComponentForm.frmEl.unique_tag.val(dev.data.unique_tag);
        ComponentForm.frmEl.order_number.val(dev.data.order_number);
        ComponentForm.frmEl.purchase_cost.val(dev.data.purchase_cost);
        ComponentForm.frmEl.notes.val($.trim(dev.data.notes));

        if (dev.data.purchase_currency != "" && dev.data.purchase_currency != null) {
            ComponentForm.frmEl.purchase_currency.val(dev.data.purchase_currency).trigger("change");
        }

        current_purchase_date = dev.data.purchase_date;
        if (current_purchase_date) {
            var purchasedate = current_purchase_date.split('-').join('/');
            ComponentForm.frmEl.purchase_date.val(purchasedate);
        }

        if (dev.data.status >= 0) {
            ComponentForm.frmEl.status.val(dev.data.status).trigger("change");
        }

        if (dev.data.origin_from > 0) {
            ComponentForm.frmEl.origin_from.val(dev.data.origin_from).trigger("change");
        }

        if (dev.data.requestable_component == 1) {
            ComponentForm.frm.find("input[name='requestable_component']").prop("checked", true);
        } else {
            ComponentForm.frm.find("input[name='requestable_component']").prop("checked", false);
        }

        // Set dropdown values
        if (typeof dev.dropdown == "object") {
            if (typeof dev.dropdown.company == "object" && dev.dropdown.company != null) {
                ComponentForm.frmEl.company.append(new Option(dev.dropdown.company.text, dev.dropdown.company.id, true, true)).trigger("change");
            }
            if (typeof dev.dropdown.category == "object" && dev.dropdown.category != null) {
                ComponentForm.frmEl.category.attr('data-noLoadField', true);
                ComponentForm.frmEl.category.append(new Option(dev.dropdown.category.text, dev.dropdown.category.id, true, true));
                ComponentForm.frmEl.category.attr('data-noLoadField', false);
                ComponentForm.frmEl.category.trigger("change");
            }
            if (typeof dev.dropdown.location == "object" && dev.dropdown.location != null) {
                ComponentForm.frmEl.location.append(new Option(dev.dropdown.location.text, dev.dropdown.location.id, true, true)).trigger("change");
            }
            if (typeof dev.dropdown.internal_place == "object" && dev.dropdown.internal_place != null) {
                t.internal_places.push(dev.dropdown.internal_place.id);
            }
            if (typeof dev.dropdown.supplier == "object" && dev.dropdown.supplier != null) {
                current_supplier_name = dev.dropdown.supplier.text;
                current_supplier_id = dev.dropdown.supplier.id;
                ComponentForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
            }
            if (typeof dev.dropdown.invoice == "object" && dev.dropdown.invoice != null) {
                ComponentForm.frmEl.invoice_id.append(new Option(dev.dropdown.invoice.text, dev.dropdown.invoice.id, true, true)).trigger("change");
            }
            if (typeof dev.dropdown.department == "object" && dev.dropdown.department != null) {
                ComponentForm.frmEl.department_id.append(new Option(dev.dropdown.department.text, dev.dropdown.department.id, true, true)).trigger("change");
            }
            if (typeof dev.dropdown.parent_device == "object" && dev.dropdown.parent_device != null) {
                ComponentForm.frmEl.parent_device.append(new Option(dev.dropdown.parent_device.text, dev.dropdown.parent_device.id, true, true)).trigger("change");
            }
        }

        ComponentForm.frmEl.internal_place.trigger('change');
        ComponentForm.frmEl.forAction.val(forAction);

        // Load image
        if (dev.data.image != null) {
            ComponentForm.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + dev.data.image);
            ComponentForm.frmEl.imgviewcover.removeClass("hide");
            if (forAction == "clone") {
                ComponentForm.frmEl.clone_img.val(dev.data.image);
            }
        } else {
            ComponentForm.frmEl.imgview.attr("src", "");
            ComponentForm.frmEl.imgviewcover.addClass("hide");
        }

        // Custom fields
        t.clearCustomFields();
        if (typeof dev.custom_fields == "object" && typeof dev.custom_fields.html != "") {
            t.fillCustomFields(dev.custom_fields);
        }

        // Show image list
        if (dev.data.image) {
            let $li = $("<li>")
                .addClass("list-group-item d-flex align-items-center justify-content-between")
                .attr("data-file", dev.data.image);

            let $img = $("<img>")
                .attr("src", t.config.imgviewpath + "/" + dev.data.image)
                .addClass("img-thumbnail")
                .css({ width: "80px", height: "80px", objectFit: "cover", marginRight: "10px" });

            let $link = $("<a>")
                .attr("href", t.config.imgviewpath + "/" + dev.data.image)
                .attr("target", "_blank")
                .text(dev.data.image);

            let $left = $("<div>").append($img).append("<br>").append($link);

            let $del = $("<button>")
                .addClass("btn btn-sm btn-danger del-link")
                .attr("data-id", dev.data.id)
                .attr('data-action', forAction)
                .html("Delete");

            $li.append($left).append($del);
            ComponentForm.frmEl.images_list.html($li);
        } else {
            ComponentForm.frmEl.images_list.html("<li class='list-group-item'>No files uploaded</li>");
        }

        ComponentForm.frmEl.company.prop("disabled", false);
        if (dev.data.status == 4) {
            ComponentForm.frmEl.company.prop("disabled", true);
        }

        $("#component-mdl").modal("show");
    };

    t.resetFrm = function () {
        ComponentForm.frm.get(0).reset();
        ComponentForm.frm.trigger("reset");
        ComponentForm.frmValidator.resetForm();
        ComponentForm.frmEl.location.empty().trigger("change");
        ComponentForm.frmEl.internal_place.empty().trigger("change");
        ComponentForm.frmEl.parent_device.empty().trigger("change");
        ComponentForm.frmEl.category.empty().trigger("change");
        ComponentForm.frmEl.supplier.empty().trigger("change");
        ComponentForm.frmEl.invoice_id.empty().trigger("change");
        ComponentForm.frmEl.department_id.empty().trigger("change");
        ComponentForm.frmEl.company.prop("disabled", false);
        ComponentForm.frmEl.status.prop("disabled", false);
        ComponentForm.frmEl.origin_from.prop("disabled", false);
        ComponentForm.frmEl.checked_out_to_user.empty().trigger("change");
        ComponentForm.frmEl.checked_out_to.empty().trigger("change");
        ComponentForm.frmEl.forAction.val("");
        ComponentForm.frm.find("#id").val("");
        t.clearCustomFields();
        ComponentForm.frmEl.imgview.attr("src", "");
        ComponentForm.frmEl.imgviewcover.addClass("hide");
    };

    t.clearCustomFields = function () {
        ComponentForm.frm.find(".custom-field-row").remove();
    };

    t.fillCustomFields = function (data) {
        if (typeof data.html == "undefined") {
            return;
        }
        t.clearCustomFields();
        ComponentForm.frmEl.customFieldsPrvEl.after(data.html);
        if (data.required_fields.length > 0) {
            $.each(data.required_fields, function (i, d) {
                ComponentForm.frm.find("#" + d).rules("add", { required: true });
            });
        }
        $.each(data.all_fields, function (i, d) {
            ComponentForm.frm.find("#" + d).rules("add", { remarks: false });
        });

        var select2Opts = { width: "100%" };
        $(".cf-select2").each(function () {
            var $field = $(this);
            var fieldId = $field.attr("id");

            if (fieldId) {
                // Initialize all custom field select2s with appropriate configs
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
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        },
                        templateResult: function (data) {
                            if (typeof data.loading != "undefined" && data.loading) {
                                return $("<div>" + data.text + "</div>");
                            }
                            var a = '';
                            a += "<div class='row'>";
                            a += "<div class='col-sm-10'>";
                            if (data.displayName != null && data.displayName != "") {
                                a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + data.displayName;
                            } else {
                                a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + data.first_name + " " + data.last_name;
                            }
                            var active = 'inactive-user';
                            if (data.user_status == "Active") {
                                active = 'active-user';
                            }
                            a += "<span class='" + active + "'></span>";
                            a += "</div>";
                            if (data.email != null && data.email != "") {
                                a += "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" + data.email + "</div>";
                            }
                            if (data.employee_num != null && data.employee_num != "") {
                                a += "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" + data.employee_num + "</div>";
                            }
                            a += "</div>";
                            a += "<div class='col-sm-2'>"
                            a += "<div><img class='img-u' src= '" + data.img_path + "'/></div>";
                            a += "</div>";
                            a += "</div>";
                            return $("<div>" + a + "</div>");
                        }
                    }));
                }
                // Add other custom field types similarly...
            }
        });
    };

    t.getCustomFields = function (e) {
        e.preventDefault();
        if (ComponentForm.frmEl.category.attr('data-noLoadField') == "true") {
            return;
        }

        t.clearCustomFields();
        if (typeof t.config.custom_fields == "object" && typeof t.config.custom_fields.html != "") {
            t.fillCustomFields(t.config.custom_fields);
        }
        var category_id = parseInt(ComponentForm.frmEl.category.val());
        if (category_id < 1 || isNaN(category_id)) {
            return;
        }

        var type_id = ComponentForm.frmEl.id.val();

        var http = $.get(t.config.getCustomFieldsByCategory + "/" + category_id + "/" + 'component' + "/" + type_id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.fillCustomFields(data);
                }
            }
        });
        http.fail(function () {
            alert(config.translations.something_went_wrong);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    // ==================== CHECKOUT MODAL ====================

    $("#checkoutconsumablemodal").on('show.bs.modal', function (e) {
        $('#component-checkout-mdl-frm')[0].reset();
        ChkoutComponentMdl.frmValidator.resetForm();
        ChkoutComponentMdl.frmEl.assigned_to.val(null).trigger("change");
        ChkoutComponentMdl.frmEl.device_id.val(null).trigger("change");
        ChkoutComponentMdl.frmEl.checkout_at.val(null);
        ChkoutComponentMdl.frmEl.expected_checkin.val(null);
        ChkoutComponentMdl.frmEl.note.val(null);

        var company_id = $(e.relatedTarget).attr('data-company_id');
        var entityId = $(e.relatedTarget).attr('data-id');
        ChkoutComponentMdl.company_id = company_id;
        var select2Opts = { width: "100%" };

        ChkoutComponentMdl.frmEl.assigned_to.select2($.extend({}, select2Opts, {
            dropdownParent: $('#component-checkout-mdl'),
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
            placeholder: config.translations.Select_the_User,
            templateSelection: function (data, container) {
                $(container).attr('title', data.text);
                return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
            },
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                var a = '';
                a += "<div class='row'>";
                a += "<div class='col-sm-10'>";
                if (s.displayName != null && s.displayName != "") {
                    a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.displayName;
                } else {
                    a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.first_name + " " + s.last_name;
                }
                var active = s.user_status == "Active" ? 'active-user' : 'inactive-user';
                a += "<span class='" + active + "'></span>";
                a += "</div>";
                if (s.email != null && s.email != "") {
                    a += "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" + s.email + "</div>";
                }
                if (s.employee_num != null && s.employee_num != "") {
                    a += "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" + s.employee_num + "</div>";
                }
                a += "</div>";
                a += "<div class='col-sm-2'>"
                a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
                a += "</div>";
                a += "</div>";
                return $("<div>" + a + "</div>");
            }
        }));

        ChkoutComponentMdl.frmEl.device_id.select2($.extend({}, select2Opts, {
            dropdownParent: $('#component-checkout-mdl'),
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
            placeholder: config.translations.Select_the_Device,
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                a = "<div class='so-t'><i class=\"fa fa-tag\"></i> " + s.asset_tag + "</div>";
                if (s.asset_name != null) {
                    a += "<div class='so-t'><i class=\"fa fa-laptop\"></i> " + s.asset_name + "</div>";
                }
                a += "<div class='so-m'><i class=\"fa fa-tablet\"></i> " + s.name + " " + s.modelno + "</div>";
                a += "<div class='so-t'><i class=\"fa fa-barcode\"></i> " + s.serial + "</div>";
                return $("<div>" + a + "</div>");
            },
            templateSelection: function (data, container) {
                if (container) {
                    $(container).attr('title', data.text);
                }
                return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
            }
        }));

        ChkoutComponentMdl.frmEl.assigned_for.select2($.extend({}, select2Opts, {
            dropdownParent: $('#component-checkout-mdl'),
            data: [
                { id: "1", text: "User" },
                { id: "2", text: "Device" }
            ],
            width: '100%',
            allowClear: true
        }));

        ChkoutComponentMdl.frmEl.assigned_for.val("1").trigger('change');

        $('#component-checkout-mdl .modal-title').html('Checkout Component');
        $('#checkoutsubmitBtn').html('Checkout');
        $('#component-checkout-mdl-frm').attr('action', t.config.url.checkout + '/' + entityId);

        // Set component tag and name
        var componentTag = $(e.relatedTarget).attr('data-unique_tag') || '';
        var componentName = $(e.relatedTarget).attr('data-name') || '';
        $('#lblComponentTag').html(componentTag);
        $('#lblComponentName').html(componentName);
    });

    ChkoutComponentMdl.frm.submit(function (e) {
        e.preventDefault();
        if (ChkoutComponentMdl.frmValidator.form() == false) {
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
            success: function (data) {
                $('label.msg').remove();
                if (data.status == 'error') {
                    sweetAlert('center', 'error', data);
                } else if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    $('#component-checkout-mdl').modal('hide');
                    t.dTbl.ajax.reload();
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    ChkoutComponentMdl.frmEl.assigned_for.on("change", function () {
        t.switchCheckTarget();
    });

    t.switchCheckTarget = function () {
        if (ChkoutComponentMdl.frmEl.assigned_for.val() == "2") {
            ChkoutComponentMdl.frmEl.device_id.closest(".cover").show();
            ChkoutComponentMdl.frmEl.assigned_to.closest(".cover").hide();
            ChkoutComponentMdl.frmEl.device_id.rules("add", { required: true });
            ChkoutComponentMdl.frmEl.assigned_to.rules("remove", "required");
        } else {
            ChkoutComponentMdl.frmEl.device_id.closest(".cover").hide();
            ChkoutComponentMdl.frmEl.assigned_to.closest(".cover").show();
            ChkoutComponentMdl.frmEl.device_id.rules("remove", "required");
            ChkoutComponentMdl.frmEl.assigned_to.rules("add", { required: true });
        }
    };

    // ==================== CHECKIN MODAL ====================

    $("#component-checkin-mdl").on('show.bs.modal', function (e) {
        $('#component-checkin-mdl-frm')[0].reset();
        ChkinComponentMdl.frmValidator.resetForm();
        ChkinComponentMdl.frmEl.checked_in_at.val(null);
        ChkinComponentMdl.frmEl.note.val(null);

        var entityId = $(e.relatedTarget).attr('data-id');
        var componentTag = $(e.relatedTarget).attr('data-unique_tag') || '';
        var componentName = $(e.relatedTarget).attr('data-name') || '';

        ChkinComponentMdl.frmEl.id.val(entityId);
        $('#lblComponentTag').html(componentTag);
        $('#lblComponentName').html(componentName);

        // Get checkout details
        $.get(t.config.url.checkin + '/' + entityId)
            .done(function (response) {
                if (response.status === 'success' && response.component) {
                    var component = response.component;
                    var checkoutDetails = component.checkout_details;
                    if (checkoutDetails) {
                        ChkinComponentMdl.frmEl.assigned_for.text(checkoutDetails.type || '');
                        ChkinComponentMdl.frmEl.checkoutinfo.text(checkoutDetails.name || '');
                    }
                }
            })
            .fail(function () {
                console.log('Error fetching component checkout details');
            });

        $('#component-checkin-mdl-frm').attr('action', t.config.url.checkin + '/' + entityId);
        $('#component-checkin-mdl .modal-title').html('Checkin Component');
        $('#checkinsubmitBtn').html('Checkin');
        $('#component-checkin-mdl').modal("show");
    });

    ChkinComponentMdl.frm.submit(function (e) {
        e.preventDefault();
        if (ChkinComponentMdl.frmValidator.form() == false) {
            return false;
        }

        var formData = new FormData(this);
        var submitUrl = $(this).attr("action");

        var submitBtn = $("#checkinsubmitBtn");
        submitBtn.prop("disabled", true);

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            success: function (data) {
                $('label.msg').remove();
                if (data.status == 'error') {
                    sweetAlert('center', 'error', data);
                } else if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    $('#component-checkin-mdl').modal('hide');
                    t.dTbl.ajax.reload();
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    // ==================== PRINT LABEL ====================

    t.printLabelModal = function (e) {
        e.preventDefault();
        var selected = [];

        // Get IDs from checked checkboxes
        t.table.find(".row_selector.chkbx-tk-ok").each(function (i, el) {
            selected.push($(el).attr("data-id"));
        });

        // If no checkboxes checked, check merge items
        if (selected.length === 0) {
            if (t.merge_items.others && t.merge_items.others.length > 0) {
                selected = t.merge_items.others.map(function (item) {
                    return item.id.toString();
                });
            }

            if (selected.length === 0) {
                var data = {
                    'msg': config.translations.Please_select_atleast_one_component || 'Please select at least one component',
                };
                sweetAlert('center', 'error', data);
                return;
            }
        }

        $('#print-modal .modal-title').text(config.translations.select_print_Option || 'Select Print Option');
        $('#printsubmitBtn').text("Print");
        $('#print-modal').modal("show");
    };

    t.printHandleSubmitModal = function (e) {
        e.preventDefault();
        var selected = [];

        t.table.find(".row_selector").each(function (i, el) {
            if ($(el).hasClass('chkbx-tk-ok')) {
                selected.push($(el).attr("data-id"));
            }
        });

        if (selected.length == 0) {
            if (t.config.print_component_array.length > 0) {
                $(t.config.print_component_array).each(function (i, v) {
                    selected.push(v.id);
                });
            }
        }

        var option_value = $('select[name=print_usr_plc] option:selected').val();
        var user_info = '';
        if (option_value == "1") {
            user_info = PrintMdl.frmEl.print_usr_info.val();
        }

        var printOpt = $('select[name=print_opt] option:selected').val();
        var url = '';

        if (printOpt == '1' && option_value) {
            url = t.config.url.print_component_barcode + "/" + option_value + "/" + btoa(selected.join(",")) + "/" + user_info;
        } else if (printOpt == '2' && option_value) {
            url = t.config.url.print_component_onecol + "/" + option_value + "/" + btoa(selected.join(",")) + "/" + user_info;
        } else if (printOpt == '3' && option_value) {
            url = t.config.url.print_component_twocol + "/" + option_value + "/" + btoa(selected.join(",")) + "/" + user_info;
        } else if (printOpt == '4' && option_value) {
            url = t.config.url.print_verticalcol + "/" + option_value + "/" + btoa(selected.join(",")) + "/" + user_info;
        }

        if (url) {
            t.print_label_starter.attr("href", url).get(0).click();
        }
    };

    t.addToMerge = function (component, collection = false) {
        try {
            if (!t.merge_items) {
                t.merge_items = { others: [] };
            }
            if (!Array.isArray(t.merge_items.others)) {
                t.merge_items.others = [];
            }
            if (!Array.isArray(t.config.print_component_array)) {
                t.config.print_component_array = [];
            }

            if (!component || !component.id) {
                return false;
            }

            let alreadyAdded = t.merge_items.others.some(item => item.id === component.id);

            if (alreadyAdded) {
                if (!collection) {
                    new PNotify({
                        text: (config.translations.component || 'Component') + " " + (component.name || '') + " " +
                            (config.translations.is_already_added_in_merge_list || 'is already added in merge list'),
                        type: "error"
                    });
                }
                return false;
            }

            t.merge_items.others.push(component);
            t.config.print_component_array.push(component);

            if (!collection) {
                new PNotify({
                    text: (config.translations.component || 'Component') + " " + (component.name || '') + " " +
                        (config.translations.has_been_added || 'has been added'),
                    type: "success"
                });
            }

            return true;

        } catch (e) {
            console.error(e);
            return false;
        }
    };

    t.removeFromMerge = function (id) {
        try {
            if (t.merge_items && Array.isArray(t.merge_items.others)) {
                for (let i = t.merge_items.others.length - 1; i >= 0; i--) {
                    if (t.merge_items.others[i] && t.merge_items.others[i].id == id) {
                        t.merge_items.others.splice(i, 1);
                        break;
                    }
                }
            }
            if (t.config.print_component_array && Array.isArray(t.config.print_component_array)) {
                for (let i = t.config.print_component_array.length - 1; i >= 0; i--) {
                    if (t.config.print_component_array[i] && t.config.print_component_array[i].id == id) {
                        t.config.print_component_array.splice(i, 1);
                        break;
                    }
                }
            }
        } catch (e) {
            console.error("Error removing from merge:", e);
        }
    };

    t.removeFromAllArrays = function (id) {
        var index = paginationListCheckedData.indexOf(id);
        if (index !== -1) {
            paginationListCheckedData.splice(index, 1);
        }
        t.removeFromMerge(id);
    };

    t.isInMergeItems = function (id) {
        if (!t.merge_items.others || !Array.isArray(t.merge_items.others)) return false;
        for (let i = 0; i < t.merge_items.others.length; i++) {
            if (t.merge_items.others[i].id == id) {
                return true;
            }
        }
        return false;
    };

    t.isInAnyArray = function (id) {
        return paginationListCheckedData.indexOf(id) !== -1 || t.isInMergeItems(id);
    };

    t.printLabel = function (e) {
        try {
            e.preventDefault();
            var componentId = $(this).attr("data-id");
            var $rowSelector = $(this).closest('tr').find('.row_selector');
            var found = false;

            $rowSelector.toggleClass("chkbx-tk-ok checkboxRemoved");
            var isChecked = $rowSelector.hasClass('chkbx-tk-ok');

            if (isChecked) {
                $.each(t.config.data_print, function (i, v) {
                    if (componentId == v.id) {
                        t.addToMerge(v);
                        found = true;
                        if (paginationListCheckedData.indexOf(componentId) === -1) {
                            paginationListCheckedData.push(componentId);
                        }
                        return false;
                    }
                });
            } else {
                t.removeFromAllArrays(componentId);
            }

            t.updateCheckAllState();

            if (isChecked && !found) {
                new PNotify({
                    text: config.translations.unable_to_add_device_label_print || 'Unable to add component for label print',
                    type: 'error'
                });
            }
        } catch (e) {
            console.log(e);
            new PNotify({
                text: config.translations.unable_to_add_device_label_print || 'Unable to add component for label print',
                type: 'error'
            });
        }
    };

    t.updateCheckAllState = function () {
        var allChecked = true;
        var hasChecked = false;

        t.table.find(".row_selector").each(function (i, el) {
            if (!$(el).hasClass("chkbx-tk-ok")) {
                allChecked = false;
            } else {
                hasChecked = true;
            }
        });

        if (allChecked && hasChecked) {
            t.checkAll.addClass("chkbx-tk-ok");
        } else {
            t.checkAll.removeClass("chkbx-tk-ok");
        }
    };

    t.checkAllAutoSelect = function (e) {
        e.preventDefault();
        $(this).toggleClass("chkbx-tk-ok");

        var id = $(this).attr("data-id");
        var isChecked = $(this).hasClass("chkbx-tk-ok");

        if (isChecked) {
            $(this).addClass('checkboxRemoved');
            $.each(t.config.data_print, function (i, v) {
                if (v.id == id) {
                    t.addToMerge(v);
                    return false;
                }
            });
            if (paginationListCheckedData.indexOf(id) === -1) {
                paginationListCheckedData.push(id);
            }
        } else {
            $(this).removeClass('checkboxRemoved');
            t.removeFromAllArrays(id);
        }

        var allChecked = true;
        t.table.find(".row_selector").each(function (i, el) {
            if (!$(el).hasClass("chkbx-tk-ok")) {
                allChecked = false;
                return false;
            }
        });

        if (allChecked) {
            t.checkAll.addClass("chkbx-tk-ok");
        } else {
            t.checkAll.removeClass("chkbx-tk-ok");
        }
    };

    t.toggleRowSelectors = function (e) {
        if (typeof e != "undefined") e.preventDefault();
        t.checkAll.toggleClass("chkbx-tk-ok");
        var state = t.checkAll.hasClass("chkbx-tk-ok");
        var showNotification = false;
        var componentNames = [];

        t.table.find(".row_selector").each(function (i, el) {
            var $el = $(el);
            var id = $el.attr("data-id");

            if (!state) {
                $el.removeClass("chkbx-tk-ok checkboxRemoved");
                t.removeFromAllArrays(id);
            } else {
                $el.addClass("chkbx-tk-ok");
                if (!t.isInAnyArray(id)) {
                    $.each(t.config.data_print, function (i, v) {
                        if (v.id == id) {
                            t.addToMerge(v, true);
                            componentNames.push(v.name);
                            return false;
                        }
                    });
                    showNotification = true;
                }
            }
        });

        if (showNotification && componentNames.length > 0) {
            new PNotify({
                text: (config.translations.component || 'Component') + "s " + (config.translations.has_been_added || 'have been added'),
                type: "success"
            });
        }
    };

    // ==================== COMPONENT FORM SUBMIT ====================

    ComponentForm.frm.submit(function (e) {
        e.preventDefault();

        if (ComponentForm.frmValidator.form() == false) {
            return false;
        }

        var formData = new FormData(this);
        var submitUrl = $(this).attr("action");

        var submitBtn = $("#btnSubmit");
        submitBtn.prop("disabled", true);

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            success: function (data) {
                $('label.msg').remove();

                if (data.status == 'error') {
                    if (typeof data.section != 'undefined') {
                        sweetAlert('center', 'error', data);
                    } else {
                        $.each(data.errors, function (index, value) {
                            var parent = $("[name='" + index + "']").parent();
                            if (parent.hasClass("input-group"))
                                parent.after('<label class="error msg" for="">' + value + '</label>')
                            else
                                $("[name='" + index + "']").after('<label class="error msg" for="">' + value + '</label>')
                        });
                    }
                } else if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    $('#component-mdl').modal('hide');
                    if (typeof dtblRef != "undefined")
                        dtblRef.dTbl.ajax.reload();
                }
            },
            complete: function () {
                submitBtn.prop("disabled", false);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    // ==================== ACTIONS (Edit, Delete, Clone, Restore) ====================

    $(document).on('click', '.dtActEdit', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#component-mdl').data('id', id);
        $('#component-mdl').data('action', 'edit');
        var modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('component-mdl'));
        modalInstance.show();
    });

    $(document).on('click', '.dtActClone', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $('#component-mdl').data('id', id);
        $('#component-mdl').data('action', 'clone');
        var modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('component-mdl'));
        modalInstance.show();
    });

    $(document).on('click', '.dtActDel', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = t.config.url.delete;

        var data = {
            'msg': config.translations.something_went_wrong || 'Something went wrong',
        };
        var send_data = {
            'token': t.config.token,
            'id': id,
        };
        sweetAlertPost(config.translations.are_you_want_delete || 'Are you sure you want to delete?', 'warning', url, t, data, send_data);
    });

    $(document).on('click', '.dtActRestore', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = t.config.url.restore + '/' + id;

        var data = {
            'msg': config.translations.something_went_wrong || 'Something went wrong',
        };
        sweetAlerts(config.translations.are_you_want_restore || 'Are you sure you want to restore?', 'warning', url, t, data, 'reload');
    });

    $(document).on('click', '.dtActCheckOut', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var company_id = $(this).attr('data-company_id');
        var unique_tag = $(this).attr('data-unique_tag') || '';
        var name = $(this).attr('data-name') || '';

        $('#component-checkout-mdl').data('id', id);
        $('#component-checkout-mdl').data('company_id', company_id);
        $('#component-checkout-mdl').data('unique_tag', unique_tag);
        $('#component-checkout-mdl').data('name', name);

        var modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('component-checkout-mdl'));
        modalInstance.show();
    });

    $(document).on('click', '.dtActCheckIn', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var unique_tag = $(this).attr('data-unique_tag') || '';
        var name = $(this).attr('data-name') || '';

        $('#component-checkin-mdl').data('id', id);
        $('#component-checkin-mdl').data('unique_tag', unique_tag);
        $('#component-checkin-mdl').data('name', name);

        var modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('component-checkin-mdl'));
        modalInstance.show();
    });

    // ==================== ICONS AND RENDER HELPERS ====================

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
            print_label: function () {
                return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 6.6V8.4C9 8.73137 8.73137 9 8.4 9H6.6C6.26863 9 6 8.73137 6 8.4V6.6C6 6.26863 6.26863 6 6.6 6H8.4C8.73137 6 9 6.26863 9 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 12H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 12V15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 18H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12.0111L12.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 12.0111L18.01 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 15.0111L12.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 15.0111L18.01 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 18.0111L18.01 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 9.01111L12.01 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 6.01111L12.01 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 15.6V17.4C9 17.7314 8.73137 18 8.4 18H6.6C6.26863 18 6 17.7314 6 17.4V15.6C6 15.2686 6.26863 15 6.6 15H8.4C8.73137 15 9 15.2686 9 15.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 6.6V8.4C18 8.73137 17.7314 9 17.4 9H15.6C15.2686 9 15 8.73137 15 8.4V6.6C15 6.26863 15.2686 6 15.6 6H17.4C17.7314 6 18 6.26863 18 6.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 3H21V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M18 21H21V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 3H3V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 21H3V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>`;
            }
        }
    };

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
            var t = this;
            var actionState = t.getRowActionState(record);
            var menuActions = t.buildRowActions(record);
            var quickActions = [];
            var dropdownId;

            if (type !== "display") return "";

            if (t.parent && t.parent.showDeletedComponents) {
                quickActions.push(t.quickActionButtonHtml(
                    (config.translations || {}).restore_component || "Restore Component",
                    "dtActRestore",
                    record.id,
                    "restore",
                    "",
                    'data-indent="component"'
                ));
                menuActions = [];
            } else {
                if (actionState.ComponentEdit) {
                    quickActions.push(t.quickActionButtonHtmlEdit(
                        (config.translations || {}).edit_component || "Edit Component",
                        "dtActEdit",
                        record.id,
                        "edit",
                    ));
                }
                if (actionState.ComponentDelete) {
                    quickActions.push(t.quickActionButtonHtml(
                        (config.translations || {}).delete_component || "Delete Component",
                        "dtActDel",
                        record.id,
                        "delete",
                        "is-delete",
                        'data-indent="component"'
                    ));
                }
            }

            if (!quickActions.length && !menuActions.length) {
                return '<span class="user-list-empty">-</span>';
            }

            dropdownId = "component-table-action-dropdown-" + record.id;

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
                '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '" data-bs-toggle="modal" action="edit" data-bs-target="#component-mdl"', extraAttributes, '>',
                this.getIcon(iconKey),
                '</button>'
            ].join("");
        },
        getRowActionState: function (record) {
            return {
                ComponentRestore: this.hasPermission("ComponentRestore"),
                ComponentEdit: this.hasPermission("ComponentEdit"),
                ComponentAdd: this.hasPermission("ComponentAdd"),
                ComponentDelete: this.hasPermission("ComponentDelete"),
                ComponentView: this.hasPermission("ComponentView"),
                ComponentCheckout: this.hasPermission("ComponentCheckout"),
                ComponentCheckin: this.hasPermission("ComponentCheckin"),
                ComponentPrintLabel: this.hasPermission("ComponentPrintLabel"),
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
            let unique_tag = record.unique_tag || '';
            var actionState = this.getRowActionState(record);
            var actions = [];
            var tr = config.translations || {};

            if (t.parent && t.parent.showDeletedComponents) {
                actions.push(t.actionItemHtml(tr.restore_component || "Restore Component", "dtActRestore", id, "restore"));
                return actions;
            }

            if (actionState.ComponentAdd) actions.push(t.actionItemHtmlModal(tr.clone_component || "Clone Component", "dtActClone", id, "clone", "component-mdl"));
            if (actionState.ComponentView) {
                actions.push([
                    '<li>',
                    '<a class="dropdown-item dtActView" ', 'href="', baseURL, '/component/info/', id, '" ', 'target="_blank" ', 'data-id="', id, '">',
                    t.getIcon("view"),
                    '<span class="b3-text">',
                    tr.view_component || "View Component",
                    '</span>', '</a>', '</li>'
                ].join(""));
            }

            let extraAttributesForCheckout = `data-name="${name}" data-company_id="${company_id}" data-unique_tag="${unique_tag}"`;
            if (actionState.ComponentCheckout) actions.push(t.actionItemHtmlModal(tr.checkout_component || "Checkout Component", "dtActCheckOut", id, "checkout", "component-checkout-mdl", extraAttributesForCheckout));

            let extraAttributesForCheckin = `data-name="${name}" data-unique_tag="${unique_tag}"`;
            if (actionState.ComponentCheckin) actions.push(t.actionItemHtmlModal(tr.checkin_component || "Checkin Component", "dtActCheckIn", id, "checkout", "component-checkin-mdl", extraAttributesForCheckin));

            if (actionState.ComponentPrintLabel) actions.push(t.actionItemHtmlModal(tr.print_label || "Print Label", "dtActPrintLabel", id, "print_label", "", `data-id="${id}"`));

            return actions;
        },
        hasPermission: function (name) {
            return Array.isArray(t.config.permissions) && t.config.permissions.indexOf(name) !== -1;
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
                '<a class="dropdown-item ', cls, '" href="#" data-id="', id, '" data-bs-target="#', modalName, '" data-bs-toggle="modal" ', attributes, '>',
                this.getIcon(iconKey),
                '<span class="b3-text">', label, '</span>',
                '</a>',
                '</li>'
            ].join("");
        },
        actionDividerHtml: function () {
            return '<li><hr class="dropdown-divider"></li>';
        },
        componentInfo: function () {
            return function (d) {
                var a = [];
                a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.unique_tag || 'Tag') + '">' + (d.unique_tag || '') + '</span></div>');
                a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.component_name || 'Name') + '">' + (d.name || '') + '</span></div>');
                a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Component_Serial || 'Serial') + '">' + (d.serial || '') + '</span></div>');
                return a.join(" ");
            };
        },
        checkoutInfo: function () {
            return function (d) {
                var a = [];
                if (d.assigned_to) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Assigned To">' + d.assigned_to + '</div>');
                }
                if (d.assigned_device) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Assigned Device">' + d.assigned_device + '</div>');
                }
                if (d.checked_out_at) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Checked Out Date">' + d.checked_out_at + '</div>');
                }
                return a.join(" ");
            };
        },
        locationInfo: function () {
            return function (d) {
                var a = [];
                if (d.company_name) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.company_name || 'Company') + '">' + d.company_name + '</div>');
                }
                if (d.category_name) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.category || 'Category') + '">' + d.category_name + '</div>');
                }
                if (d.location_name) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Location_Name || 'Location') + '">' + d.location_name + '</div>');
                }
                if (d.department_name) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Department_Name || 'Department') + '">' + d.department_name + '</div>');
                }
                return a.join(" ");
            };
        },
        originInfo: function () {
            return function (d) {
                var a = [];
                if (d.origin_from == 1 && d.invoice_no) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice">' + d.invoice_no + '</div>');
                }
                if (d.origin_from == 2 && d.parent_device_tag) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Parent Device">' + d.parent_device_tag + '</div>');
                }
                if (d.origin_from_text) {
                    a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Origin">' + d.origin_from_text + '</div>');
                }
                return a.join(" ");
            };
        },
        purchaseCost: function () {
            return function (d) {
                if (d.purchase_cost) {
                    if (typeof currencies[d.purchase_currency] != "undefined")
                        return currencies[d.purchase_currency].symbol + ' ' + d.purchase_cost;
                    else
                        return d.purchase_cost;
                }
                return '';
            };
        },
        status: function () {
            return function (d) {
                var statusClass = d.status_class || '';
                var statusName = d.status_name || '';
                return '<span class="badge ' + statusClass + '">' + statusName + '</span>';
            };
        }
    };

    // ==================== DATATABLE INITIALIZATION ====================

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
        }],
        order: [
            [7, 'desc']
        ],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        ajax: {
            url: config.url.components,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.showDeletedComponents = t.showDeletedComponents;
                d.filters = t.config.other_filters;
                d.department = t.config.department_filter;
                d.location = t.config.location_filter;
                d.category_id = t.config.category_id_filter;
                d.q = t.config.purchaseFilter;
                d.order = t.config.sort_dir || { id: 10, dir: 2 };
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    // console.log('data',data);
                    if (type === "display") {
                        // Access ID from the nested 'a' object
                        var id = row.a ? row.a.id : (row.id || '');
                        return '<input class="form-check-input row_selector" type="checkbox" data-id="' + id + '">';
                    }
                    return '';
                },
                className: 'text-start align-middle'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        // 'data' here is the 'a' object
                        var d = data || {};
                        var a = [];
                        a.push('<div class="text-start"><span style="color: #309ef2ff;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.unique_tag || 'Tag') + '">' + (d.unique_tag || '-') + '</span></div>');
                        return a.join(" ");
                    }
                    return '';
                },
                className: 'text-start align-middle'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = data || {};
                        var a = [];
                        if (d.name) {
                            a.push('<div class="text-start"><span style="color: #efab0c;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.component_name || 'Name') + '">' + (d.name || '-') + '</span></div>');
                        }
                        if (d.serial) {
                            a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Component_Serial || 'Serial') + '">' + (d.serial || '-') + '</span></div>');
                        }
                        if (d.cmp_name) {
                            a.push('<div class="text-start" style="color: #5cb85c;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.company_name || 'Company') + '">' + d.cmp_name + '</div>');
                        }
                        if (d.cat_name) {
                            a.push('<div class="text-start" style="color: #f9133c;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.category || 'Category') + '">' + d.cat_name + '</div>');
                        }
                        if (d.loc_name) {
                            a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Location_Name || 'Location') + '">' + d.loc_name + '</div>');
                        }
                        if (d.department) {
                            a.push('<div class="text-start" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' + (config.translations.Department_Name || 'Department') + '">' + d.department + '</div>');
                        }
                        return a.join(" ");
                    }
                    return '';
                },
                className: 'text-start'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = data || {};
                        var statusName = d.status_name || '-';
                        return '<span class="badge bg-dark text-white px-2 py-1">' + statusName + '</span>';
                    }
                    return '';
                },
                className: 'text-center align-middle'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = data || {};
                        var a = [];
                        if (d.checked_out_for == 1) {
                            a.push('<div class="text-start"><i class="bi bi-person-fill"></i> <span style="color: #309ef2ff;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="User Name">' + (d.checked_out_name || '-') + '</span></div>');
                        } else if (d.checked_out_for == 2) {
                            a.push('<div class="text-start"><i class="bi bi-laptop"></i> <span style="color: #309ef2ff;" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Device Tag">' + (d.chkout_dev_tag || '-') + '</span></div>');
                        }
                        if (d.checked_out_at) {
                            a.push('<div class="text-start"><i class="bi bi-calendar"></i> <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Checked Out Date">' + d.checked_out_at + '</span></div>');
                        }
                        if (d.expected_checkin_at) {
                            a.push('<div class="text-start"><i class="bi bi-calendar"></i> <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Expected Checkin Date">' + d.expected_checkin_at + '</span></div>');
                        }
                        return a.join(" ") || '-';
                    }
                    return '';
                },
                className: 'text-start align-middle'
            },
            {
                data: 'a.purchase_cost',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = row.a || {};
                        var purchaseCost = data || d.purchase_cost || '';
                        if (purchaseCost) {
                            return '<strong>' + (d.purchase_currency || '') + '</strong> ' + purchaseCost;
                        }
                        return '-';
                    }
                    return '';
                },
                className: 'text-center align-middle'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = data || {};
                        var a = [];

                        if (d.origin_from == 1) {
                            // For Origin From 1: Show origin_from_name, invoice_no, purchase_date
                            if (d.origin_from_name) {
                                a.push('<div class="text-start"><strong>From:</strong> <span class="badge bg-light text-black px-2 py-1">' + d.origin_from_name + '</span></div>');
                            }
                            if (d.invoice_no) {
                                a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Invoice no">' + d.invoice_no + '</span></div>');
                            }
                            if (d.purchase_date) {
                                a.push('<div class="text-start"><span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Purchase Date">' + d.purchase_date + '</span></div>');
                            }
                        }
                        else if (d.origin_from == 2) {
                            // For Origin From 2: Show origin_from_name, par_dev_tag
                            if (d.origin_from_name) {
                                a.push('<div class="text-start"><strong>From:</strong> <span class="badge bg-light text-black px-2 py-1">' + d.origin_from_name + '</span></div>');
                            }
                            if (d.par_dev_tag) {
                                a.push('<div class="text-start"><strong>Parent Device Tag:</strong> ' + d.par_dev_tag + '</div>');
                            }
                        }

                        return a.join(" ") || '-';
                    }
                    return '';
                },
                className: 'text-start align-middle'
            },
            {
                data: 'a',
                render: function (data, type, row) {
                    if (type === "display") {
                        var d = data || {};
                        var value = d.last_updated_at || d.updated_at || '-';
                        var formatted = value !== '-' ? value.split(' ').slice(0, 3).join(' ') + '<br>' + value.split(' ').slice(3).join(' ') : '-';
                        var a = [];
                        a.push('<div class="d-flex align-items-center justify-content-center h-100 w-100 text-center"><span>' + formatted + '</span></div>');
                        return a.join(" ");
                    }
                    return '';
                },
                className: 'text-center align-middle'
            },
            {
                data: "a.id",
                className: "amg-col-actions align-middle",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (type === "display") {
                        var record = row.a || row;
                        // Pass the record (which is the 'a' object) to renderActionsCell
                        return t.tblHelpers.renderActionsCell(record, type);
                    }
                    return '';
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
                        alert(config.translations.please_enter_valid_search || 'Please enter a valid search term');
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

            // Set up buttons
            t.btn.search = t.content.find(".amg-list-searchbar__icon");
            t.btn.export_excel = t.content.find(".btn-users-export");
            t.btn.export_pdf = t.content.find(".btn-users-export-pdf");
            t.btn.reload = t.content.find(".btn-reload-list");
            t.btn.bulk_update = t.content.find(".btn-users-update");
            t.btn.bulk_checkout = t.content.find(".btn-bulk-checkout");
            t.btn.bulk_checkin = t.content.find(".btn-bulk-checkin");
            t.btn.import = t.content.find(".btn-users-import");
            t.btn.deletedComponents = t.content.find(".btn-show-users");
            t.btn.openFilter = t.content.find(".btn-open-filter");
            t.btn.print_label = t.content.find(".btn-print-label");
            t.btn.addComponent = t.content.find(".btn-add-user");
            t.show_entries = t.content.find('#showSelect');

            // Check for deleted icon toggle
            if (t.showDeletedComponents) {
                $('.btn-show-users .non-deleted-icon').removeClass('d-none');
                $('.btn-show-users .deleted-icon').addClass('d-none');
            } else {
                $('.btn-show-users .non-deleted-icon').addClass('d-none');
                $('.btn-show-users .deleted-icon').removeClass('d-none');
            }
        },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        }
    });

    // ==================== BUTTON EVENT HANDLERS ====================

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#tableSearch").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search || 'Please enter a valid search term');
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_components + "?q=" + t.config.export_filters;
    };

    t.exportPDF = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_components_pdf + "?q=" + t.config.export_filters;
    };

    t.bulkUpdate = function (e) {
        e.preventDefault();
        window.location = t.config.url.bulk_update;
    };

    t.bulkCheckout = function (e) {
        e.preventDefault();
        window.location = t.config.url.bulk_checkout;
    };

    t.bulkCheckin = function (e) {
        e.preventDefault();
        window.location = t.config.url.bulk_checkin;
    };

    t.importComponents = function (e) {
        e.preventDefault();
        window.location = t.config.url.import_components;
    };

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.toggleDeletedComponents = function () {
        t.showDeletedComponents = t.showDeletedComponents == false ? true : false;

        // Toggle icons
        $('.btn-show-users .non-deleted-icon').toggleClass('d-none');
        $('.btn-show-users .deleted-icon').toggleClass('d-none');

        var title = t.showDeletedComponents == false ?
            (config.translations.show_deleted || 'Show Deleted Components') :
            (config.translations.show_non_delete || 'Show Non-Deleted Components');
        $('.btn-show-users').attr('data-original-title', title);

        // Reload table to fetch deleted components
        t.reload();
    };

    t.addComponent = function (e) {
        e.preventDefault();
        $('#component-mdl').data('action', 'add');
        var modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('component-mdl'));
        modalInstance.show();
    };

    // ==================== FILTER FUNCTIONS ====================

    t.cache_filter_values = function () {
        var v = $("#tableSearch").validate_str_param();
        t.config.search = v;
        t.config.other_filters = {};
        t.config.dashboard_filters = {};
        t.config.dashboard_filters.department = t.config.department_filter;
        t.config.dashboard_filters.location = t.config.location_filter;
        t.config.dashboard_filters.category_id = t.config.category_id_filter;

        if (t.filters.companies.val() && t.filters.companies.val() != 'null')
            t.config.other_filters.companies = t.filters.companies.val();
        if (t.filters.location.val() && t.filters.location.val() != 'null')
            t.config.other_filters.location = t.filters.location.val();
        if (t.filters.internal_place.val() && t.filters.internal_place.val() != 'null')
            t.config.other_filters.internal_place = t.filters.internal_place.val();
        if (t.filters.categories.val() && t.filters.categories.val() != 'null')
            t.config.other_filters.categories = t.filters.categories.val();
        if (t.filters.condition.val() && t.filters.condition.val() != 'null')
            t.config.other_filters.condition = t.filters.condition.val();
        if (t.filters.assigned_device.val() && t.filters.assigned_device.val() != 'null')
            t.config.other_filters.assigned_device = t.filters.assigned_device.val();
        if (t.filters.assigned_user.val() && t.filters.assigned_user.val() != 'null')
            t.config.other_filters.assigned_user = t.filters.assigned_user.val();
        if (t.filters.origin_info.val() && t.filters.origin_info.val() != 'null')
            t.config.other_filters.origin_info = t.filters.origin_info.val();
        if (t.filters.based_on.val() && t.filters.based_on.val() != 'null')
            t.config.other_filters.based_on = t.filters.based_on.val();
        if (t.filters.date_range.val() && t.filters.date_range.val() != 'null')
            t.config.other_filters.date_range = t.filters.date_range.val();
        if (t.filters.assetDepartment.val() && t.filters.assetDepartment.val() != 'null')
            t.config.other_filters.asset_department = t.filters.assetDepartment.val();
        if (t.filters.purchase_reference.val() && t.filters.purchase_reference.val() != 'null')
            t.config.other_filters.purchase_reference = t.filters.purchase_reference.val();

        var jobj = {
            "search": t.config.search,
            "other_filters": t.config.other_filters,
            'dashboard_filters': t.config.dashboard_filters,
            'showDeletedComponents': t.showDeletedComponents,
            'print_label_id': t.config.component_id_array,
            "over_all_purchase_filter": t.config.purchaseFilter
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.based_on.val(), false);
        t.updateFilterBadge();
    };

    t.updateFilterBadge = function () {
        var filterCount = 0;
        var filters = t.config.other_filters || {};

        if (filters.location && filters.location !== "null") filterCount += 1;
        if (filters.internal_place && filters.internal_place !== "null") filterCount += 1;
        if (filters.companies && filters.companies !== "null") filterCount += 1;
        if (filters.assigned_device && filters.assigned_device !== "null") filterCount += 1;
        if (filters.assigned_user && filters.assigned_user !== "null") filterCount += 1;
        if (filters.origin_info && filters.origin_info !== "null") filterCount += 1;
        if (filters.condition && filters.condition !== "null") filterCount += 1;
        if (filters.based_on && filters.based_on !== "null") filterCount += 1;
        if (filters.date_range) filterCount += 1;
        if (filters.asset_department && filters.asset_department !== "null") filterCount += 1;
        if (filters.purchase_reference && filters.purchase_reference !== "null") filterCount += 1;
        if (Array.isArray(filters.categories) && filters.categories.length) filterCount += 1;

        if (!t.filters.badge.length) return;

        if (filterCount > 0) {
            t.filters.badge.text(filterCount).removeClass("d-none");
        } else {
            t.filters.badge.addClass("d-none").text("0");
        }
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $("#tableSearch").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert(config.translations.please_enter_valid_search || 'Please enter a valid search term');
                return false;
            }
            t.config.search = v;
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.clear = function () {
        t.config.other_filters = {};
        t.filters.companies.val(0).trigger("change");
        t.filters.location.val(null).trigger("change");
        t.filters.internal_place.val(null).trigger("change");
        t.filters.categories.val(0).trigger("change");
        t.filters.condition.val(0).trigger("change");
        t.filters.assigned_device.val(0).trigger("change");
        t.filters.assigned_user.val(0).trigger("change");
        t.filters.origin_info.val(0).trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.filters.assetDepartment.val(null).trigger("change");
        t.filters.purchase_reference.val("null").trigger("change");
        t.cache_filter_values();
        resetDateRangeFilter();
        t.updateFilterBadge();
        t.reload();
    };

    t.openFilterModal = function () {
        if (t.filters.wrapper.length) {
            t.filters.wrapper.modal("show");
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

        function cb(start, end) {
            if (!start || !end) {
                if (t.filters.reportrangeText && t.filters.reportrangeText.length) {
                    t.filters.reportrangeText.text('');
                }
                t.filters.daterange.val('');
                return;
            }
            if (t.filters.reportrangeText && t.filters.reportrangeText.length) {
                t.filters.reportrangeText.text(
                    start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format('DD-MM-YYYY HH:mm:ss')
                );
            }
            t.filters.daterange.val(
                start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss')
            );
        }

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
            if (t.filters.reportrangeText && t.filters.reportrangeText.length) {
                t.filters.reportrangeText.text(
                    start.format("DD-MM-YYYY HH:mm:ss") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
                );
            }
            t.filters.daterange.val(
                start.format("YYYY-MM-DD HH:mm:ss") + " - " + end.format("YYYY-MM-DD HH:mm:ss")
            );
        };
    };

    // ==================== SELECT2 INITIALIZATION ====================

    var select2Opts = { width: "100%" };

    // Location filter
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
        placeholder: config.translations.Select_Location || "Filter By Location",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
        },
    }));

    // Categories filter
    t.filters.categories.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.categories.parent(),
        ajax: {
            url: t.config.getCategoryByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    type: 'component',
                    all: 'all'
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_Category || "Filter By Category",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    }));

    // Companies filter
    t.filters.companies.select2($.extend({}, select2Opts, {
        placeholder: config.translations.No_Filter || "All Companies",
        allowClear: true
    }));

    // Condition filter
    t.filters.condition.select2($.extend({}, select2Opts, {
        placeholder: config.translations.No_Filter || "Filter By Condition",
        dropdownParent: t.filters.condition.parent(),
        allowClear: true
    }));

    // Origin info filter
    t.filters.origin_info.select2($.extend({}, select2Opts, {
        placeholder: config.translations.No_Filter || "Filter By Origin",
        dropdownParent: t.filters.origin_info.parent(),
        allowClear: true
    }));

    // Based on filter
    t.filters.based_on.select2($.extend({}, select2Opts, {
        placeholder: config.translations.No_Filter || "Filter Based On",
        dropdownParent: t.filters.wrapper,
        allowClear: true
    }));

    // Assigned device filter
    t.filters.assigned_device.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.assigned_device.parent(),
        ajax: {
            url: t.config.url.device,
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
        placeholder: config.translations.Select_the_Device || "Filter By Device",
        templateResult: function (s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            var a = "<div class='so-t'><i class=\"fa fa-tag\"></i> " + s.asset_tag + "</div>";
            if (s.asset_name != null) {
                a += "<div class='so-t'><i class=\"fa fa-laptop\"></i> " + s.asset_name + "</div>";
            }
            a += "<div class='so-m'><i class=\"fa fa-tablet\"></i> " + s.name + " " + s.modelno + "</div>";
            a += "<div class='so-t'><i class=\"fa fa-barcode\"></i> " + s.serial + "</div>";
            return $("<div>" + a + "</div>");
        },
        templateSelection: function (data, container) {
            if (container) {
                $(container).attr('title', data.text);
            }
            return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
        }
    }));

    // Assigned user filter
    t.filters.assigned_user.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.assigned_user.parent(),
        ajax: {
            url: t.config.getUserByAjax,
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
        placeholder: config.translations.Select_the_User || "Filter By User",
        templateResult: function (s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            var a = '';
            a += "<div class='row'>";
            a += "<div class='col-sm-10'>";
            if (s.displayName != null && s.displayName != "") {
                a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.displayName;
            } else {
                a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.first_name + " " + s.last_name;
            }
            a += "<span class='active-user'></span>";
            a += "</div>";
            if (s.email != null && s.email != "") {
                a += "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" + s.email + "</div>";
            }
            if (s.employee_num != null && s.employee_num != "") {
                a += "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" + s.employee_num + "</div>";
            }
            a += "</div>";
            a += "<div class='col-sm-2'>"
            a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
            a += "</div>";
            a += "</div>";
            return $("<div>" + a + "</div>");
        },
        templateSelection: function (data, container) {
            if (container) {
                $(container).attr('title', data.text);
            }
            return data.text.length > 30 ? data.text.substring(0, 30) + '...' : data.text;
        }
    }));

    // Department filter
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
        placeholder: config.translations.select_the_department || "Filter By Department",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    }));

    // Internal place filter
    t.filters.internal_place.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.internal_place.parent(),
        ajax: {
            url: t.config.ajaxGetInternalPlace,
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
        placeholder: config.translations.select_internal_place || "Filter By Internal Place",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    }));

    // Purchase reference filter
    t.filters.purchase_reference.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.purchase_reference.parent(),
        ajax: {
            url: t.config.getInvoiceByAjax,
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
        placeholder: config.translations.Select_the_Purchase_Invoice || "Filter By Purchase Reference",
        templateSelection: function (s, container) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr({ 'data-invoice_date': s.invoice_date, 'data-name': s.supplier_name, 'data-id': s.supplier_id, 'data-order-number': s.order_number, 'data-po-number': s.po_number, 'data-purchase-cost': s.bill_amount, 'data-currency': s.currency });
            return s.text;
        }
    }));

    // ==================== EVENT BINDINGS ====================

    // Button events
    t.btn.search.on("click", $.proxy(t.tableSearch));
    t.btn.export_excel.on("click", $.proxy(t.export));
    t.btn.export_pdf.on("click", $.proxy(t.exportPDF));
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.bulk_update.on("click", $.proxy(t.bulkUpdate));
    t.btn.bulk_checkout.on("click", $.proxy(t.bulkCheckout));
    t.btn.bulk_checkin.on("click", $.proxy(t.bulkCheckin));
    t.btn.import.on("click", $.proxy(t.importComponents));
    t.btn.deletedComponents.on("click", $.proxy(t.toggleDeletedComponents));
    t.btn.addComponent.on("click", $.proxy(t.addComponent));
    t.btn.print_label.on("click", $.proxy(t.printLabelModal));
    t.btn.openFilter.off('click').on('click', function (e) {
        e.preventDefault();
        t.openFilterModal();
    });

    // Filter events
    t.filters.wrapper.on("click", "#apply_filter", t.search);
    t.filters.wrapper.on("click", "#clear", t.clear);

    // Show entries
    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    // Column visibility toggle
    t.content.find(".show-hide-columns").on("click", function (e) {
        e.preventDefault();
        $('#columnVisibilityControls').toggle();
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
            $('#columnVisibilityControls').hide();
        }
    });

    // Check all checkbox
    t.checkAll = t.table.find("#select-all");
    t.checkAll.on("click", $.proxy(t.toggleRowSelectors));
    t.table.on("click", ".row_selector", $.proxy(t.checkAllAutoSelect));

    // Print label click
    t.table.on("click", ".dtActPrintLabel", $.proxy(t.printLabel));

    // Print modal submit
    PrintMdl.frmEl.print_opt.select2($.extend({}, select2Opts, { placeholder: "Select Option" }));
    PrintMdl.frmEl.print_usr_plc.select2($.extend({}, select2Opts, { placeholder: "Select Option" })).on("change", function () {
        if (PrintMdl.frmEl.print_usr_plc.val() == "1") {
            PrintMdl.frmEl.print_usr_info_cvr.removeClass("hide");
        } else {
            PrintMdl.frmEl.print_usr_info_cvr.addClass("hide");
        }
    });
    PrintMdl.frmEl.print_usr_info.select2($.extend({}, select2Opts, { minimumInputLength: 0 }));
    PrintMdl.frm.find("#printsubmitBtn").on("click", $.proxy(t.printHandleSubmitModal));

    // ==================== CUSTOM FIELD EVENTS ====================

    ComponentForm.frmEl.category.on("change", $.proxy(t.getCustomFields));

    // ==================== LOCATION / INTERNAL PLACE ====================

    ComponentForm.frmEl.location.on("change", function () {
        t.getInternalPlace();
    });

    t.getInternalPlace = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        ComponentForm.frmEl.internal_place.empty().append(new Option(config.translations.select_internal_place || "Select Internal Place", "", true, true));
        ComponentForm.frmEl.internal_place.trigger("change");

        var location_id = ComponentForm.frmEl.location.val();
        if (location_id > 0) {
            $.get(t.config.getInternalPlaceByAjax + '/' + location_id).done(function (data) {
                if (typeof data == "object" && data.results.length) {
                    $.each(data.results, function (i, v) {
                        if ($.inArray(v.id, t.internal_places) !== -1) {
                            ComponentForm.frmEl.internal_place.append(new Option(v.text, v.id, true, true));
                        } else {
                            ComponentForm.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
                        }
                    });
                }
            }).always(function () {
                ComponentForm.frmEl.internal_place.trigger("change");
                t.internal_places = [];
            });
        }
    };

    // ==================== PURCHASE DATE FROM INVOICE ====================

    var count = 0;
    ComponentForm.frmEl.invoice_id.on("change", function (e) {
        if (count >= 2 || ComponentForm.frmEl.invoice_id.val() == null) {
            if (ComponentForm.frmEl.invoice_id.val() == null) {
                count = count + 2;
                if (current_purchase_date != null) {
                    var purchasedate = current_purchase_date.split('-').join('/');
                    ComponentForm.frmEl.purchase_date.val(purchasedate);
                } else {
                    ComponentForm.frmEl.purchase_date.val(null);
                }
                if (current_supplier_name != "" && current_supplier_id != "") {
                    ComponentForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
                } else {
                    ComponentForm.frmEl.supplier.empty('').trigger('change');
                }
                if (order_no != null) {
                    ComponentForm.frmEl.order_number.val(order_no);
                } else {
                    ComponentForm.frmEl.order_number.val(null);
                }
                if (purchase_cost != null && purchase_cost != null) {
                    ComponentForm.frmEl.purchase_cost.val(purchase_cost);
                } else {
                    ComponentForm.frmEl.purchase_cost.val('');
                }
            } else {
                t.getPurchaseDate();
            }
        }
        count++;
    });

    t.getPurchaseDate = function (e) {
        var selectedOption = ComponentForm.frmEl.invoice_id.find(':selected');
        if (!selectedOption.attr('data-invoice_date')) {
            if (current_purchase_date != null) {
                var purchasedate = current_purchase_date.split('-').join('/');
                ComponentForm.frmEl.purchase_date.val(purchasedate);
            } else {
                ComponentForm.frmEl.purchase_date.val('');
            }
            ComponentForm.frmEl.supplier.empty();
            if (current_supplier_name && current_supplier_id) {
                ComponentForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
            }
            if (order_no != null) {
                ComponentForm.frm.find("input[name='order_number']").val(order_no);
            } else {
                ComponentForm.frm.find("input[name='order_number']").val('');
            }
            if (purchase_cost != null) {
                ComponentForm.frm.find("input[name='purchase_cost']").val(purchase_cost);
            } else {
                ComponentForm.frm.find("input[name='purchase_cost']").val('');
            }
            if (currency_default != null) {
                ComponentForm.frmEl.purchase_currency.val(currency_default).trigger('change');
            } else {
                ComponentForm.frmEl.purchase_currency.val('').trigger('change');
            }
            return;
        }

        var date = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-invoice_date');
        ComponentForm.frmEl.purchase_date.val(date);
        var supplier_name = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-name');
        var supplier_id = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-id');
        ComponentForm.frmEl.supplier.append(new Option(supplier_name, supplier_id, true, true)).trigger("change");
        if (supplier_name == undefined && supplier_id == undefined && t.forAction == "edit") {
            ComponentForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
        }
        var order_number = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-po-number');
        ComponentForm.frmEl.order_number.val(order_number);
        var purchase_cost_value = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-purchase-cost');
        ComponentForm.frmEl.purchase_cost.val(purchase_cost_value || '0.00');
        var purchase_currency = ComponentForm.frmEl.invoice_id.find(':selected').attr('data-currency');
        ComponentForm.frmEl.purchase_currency.val(purchase_currency).trigger("change");
    };

    // ==================== DATEPICKER INITIALIZATION ====================

    // Component form datepicker
    ComponentForm.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });

    // Checkout modal datepickers
    // ChkoutComponentMdl.frmEl.checkout_at.datetimepicker({ 
    //     format: "DD/MM/YYYY hh:mm A", 
    //     showClear: true, 
    //     showClose: true, 
    //     sideBySide: true 
    // });
    // ChkoutComponentMdl.frmEl.expected_checkin.datetimepicker({ 
    //     format: "DD/MM/YYYY hh:mm A", 
    //     showClear: true, 
    //     showClose: true, 
    //     sideBySide: true 
    // });

    // Checkin modal datepicker
    // ChkinComponentMdl.frmEl.checked_in_at.datetimepicker({ 
    //     format: "DD/MM/YYYY hh:mm A", 
    //     showClear: true, 
    //     showClose: true, 
    //     sideBySide: true 
    // });

    // ==================== IMAGE DELETE ====================

    $(document).on('click', '.del-link', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var deleteUrl = t.config.url.image_delete + '/' + id;
        var $currentLi = $(this).closest('li');

        Swal.fire({
            title: config.translations.are_you_delete_attachment || 'Are you sure you want to delete this attachment?',
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
                        if (ComponentForm.frmEl.images_list.children().length === 0) {
                            ComponentForm.frmEl.images_list.append("<li class='list-group-item'>No files uploaded</li>");
                        }
                    });
                    return;
                }
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: { _token: t.config.token },
                    success: function (response) {
                        if (response.status === 'success') {
                            $currentLi.fadeOut(300, function () {
                                $(this).remove();
                                if (ComponentForm.frmEl.images_list.children().length === 0) {
                                    ComponentForm.frmEl.images_list.append("<li class='list-group-item'>No files uploaded</li>");
                                }
                            });
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: response.msg || "Deleted successfully",
                                confirmButtonText: "OK",
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.msg || "Delete failed",
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: config.translations.something_went_wrong || "Something went wrong",
                            confirmButtonText: "OK",
                        });
                    }
                });
            }
        });
    });

    // ==================== DETACHED ACTION MENU ====================

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#detached-action-menu').length && !$(e.target).closest('.user-list-menu-toggle').length) {
            $('#detached-action-menu').remove();
            $('.user-list-menu-toggle').data('menu-open', false);
        }
    });

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
            ['dtActEdit', 'dtActDel', 'dtActClone', 'dtActRestore', 'dtActCheckOut', 'dtActCheckIn', 'dtActPrintLabel'].forEach(function (cls) {
                if ($item.hasClass(cls)) actionClass = cls;
            });

            if (actionClass && id) {
                t.table.find('.' + actionClass + '[data-id="' + id + '"]').first().trigger('click');
            }
        }
    });

    $(document).on('scroll', function () {
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);
    });

    // ==================== FINAL INITIALIZATION ====================

    // Update filter badge
    t.updateFilterBadge();

    // Initial load
    t.dTbl.ajax.reload();

    // Store reference for use in other functions
    window.componentApp = t;
};