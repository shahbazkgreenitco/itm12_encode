var AddEdit = function (config, myapp, request_from) {
    var t = this;
    t.config = config;
    t.myapp = myapp;
    t.data = {};
    t.request_from = request_from;
    t.content = $("section.content");
    t.tokenEl = $('html head meta[name="csrf-token"]');

    t.mdl = t.content.find('#licensemodal');
    t.frm = t.mdl.find('#license-mdl-frm');
    var select2Opts = { width: "100%" };
    t.frmEl = {};
    t.frmEl.id = t.frm.find("#id");
    t.frmEl.department = t.frm.find("#department_id");
    t.frmEl.company_id = t.frm.find("#company_id");
    t.frmEl.manufacturer_id = t.frm.find("#manufacturer_id");
    t.frmEl.location = t.frm.find("#location_id");
    t.frmEl.internal_place = t.frm.find("#internal_place");
    t.frmEl.category_id = t.frm.find("#category_id");
    t.frmEl.supplier_id = t.frm.find("#supplier_id");
    t.frmEl.invoice_id = t.frm.find("#invoice_id");
    t.frmEl.purchase_date = t.frm.find("#purchase_date");
    t.frmEl.order_number = t.frm.find("#order_number");
    t.frmEl.purchase_order = t.frm.find("#purchase_order");
    t.frmEl.purchase_cost = t.frm.find("#purchase_cost");
    t.frmEl.currency = t.frm.find("#bill_currency");
    t.frmEl.depreciation_id = t.frm.find("#depreciation_id");
    t.frmEl.software_name = t.frm.find("#software_name");
    t.frmEl.software = t.frm.find("#product_id");
    t.frmEl.uniqueSerial_id = t.frm.find("#uniqueSerial_id");
    t.frmEl.maintained = t.frm.find("#maintained_id");
    t.frmEl.notes = t.frm.find("#notes_id");
    t.frmEl.support = t.frm.find("#support_id");
    t.frmEl.seats = t.frm.find("#seats_id");
    t.frmEl.license_email = t.frm.find("#license_email");
    t.frmEl.reassignable = t.frm.find("#reassignable");
    t.frmEl.agreement_no = t.frm.find("#agreement_no");
    t.frmEl.termination_date = t.frm.find("#termination_date");
    t.frmEl.expiration_date = t.frm.find("#expire_date");
    t.frmEl.added_via = t.frm.find('input[name="added_via"]');
    t.frmEl.product_id = t.frm.find('#product_id');
    t.frmEl.version = t.frm.find('#version');
    t.frmEl.all_version_check = t.frm.find('#all_version_check');
    t.frmEl.internal_place = t.frm.find('#internal_place');
    t.frmEl.is_tracked_div = t.frm.find('#is_tracked_div');
    t.frmEl.license_to_name = t.frm.find('#license_to_name');
    t.frmEl.imgviewcover = t.frm.find('#license_image_view');
    t.frmEl.is_tracked = t.frm.find('#is_tracked');
    t.frmEl.imgview = t.frm.find('#imgview');
    t.frmEl.unique__tag = t.frm.find('#unique_tag');
    t.forAction="";

    t.httpPostPath = '';
    t.httpCall = true;
    t.frmEl.submit = t.mdl.find('#btnSubmit');
    var current_purchase_date = "";
    var current_supplier_id = "";
    var order_no = "";
    t.internal_places = [];

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            company_id: {
                str_name: true,
                required: true,
            },
            department_id: {
            required: true,
            },
            category_id: {
                str_name: true
            },
            name: {
                required: true,
                clean_text_only: true,
                remarks: true
            },
            location_id: {
                required: true
            },
            manufacturer_id: {
                str_name: true
            },
            serial: {
                required: true,
                clean_text_only: true
            },
            license_name: {
                remarks: true,
                clean_text_only: true
            },
            license_email: {
                email: true
            },
            seats: {
                required: true,
                number: true,
                min: 1,
                max: 50000
            },
            supplier_id: {
                number: true
            },
            order_number: {
                remarks: true,
                clean_text_only: true
            },
            purchase_date: {
                remarks: true
            },
            currency: {
                str_name: true
            },
            purchase_cost: {
                number: true,
                min: 0
            },
            purchase_order: {
                remarks: true,
                clean_text_only: true
            },
            agreement_no: {
                remarks: true,
                clean_text_only: true
            },
            expire_date: {
                remarks: true
            },
            depreciation_id: {
                str_name: true,
                clean_text_only: true
            },
            termination_date: {
                remarks: true
            },
            notes: {
                remarks: false,
                clean_text_only: true
            },
            support: {
                remarks: true,
                clean_text_only: true
            },
            unique_tag: {
                clean_text_only: true
            }
        },
        errorPlacement: function (error, element) {
            element
                .closest(".amg-form-field")
                .find(".amg-form-error-wrap")
                .html(error);
        },

        highlight: function (element) {
            let $element = $(element);
            let $field = $element.closest(".amg-form-field");
            let $inputGroup = $field.find(".input-group");

            $field.addClass("error");
            $inputGroup.addClass("amg-form-invalid");

            // select2 error ui
            if ($element.next(".select2").length) {
                $element
                    .next(".select2")
                    .find(".select2-selection")
                    .addClass("amg-form-select-error");
            }
        },

        unhighlight: function (element) {
            let $element = $(element);
            let $field = $element.closest(".amg-form-field");
            let $inputGroup = $field.find(".input-group");

            // remove normal error
            $field.removeClass("error");
            $inputGroup.removeClass("amg-form-invalid");

            // remove select2 error
            if ($element.next(".select2").length) {
                $element
                    .next(".select2")
                    .find(".select2-selection")
                    .removeClass("amg-form-select-error");
            }

            // clear message
            $field.find(".amg-form-error-wrap").empty();
        },

    });
    t.frmEl.expiration_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });

    t.el = {};
    t.el.mdl_title = t.mdl.find('.modal-title');
    t.reset = function () {
        t.frm[0].reset();
        t.frmEl.invoice_id.empty();
        t.frmEl.version.empty();
        t.frmEl.software.empty().append(new Option("", true));
        t.frmEl.company_id.empty().trigger('change');
        t.frmEl.supplier_id.empty().trigger('change');
        t.frmEl.department.empty().trigger('change');
        t.frmEl.category_id.empty().trigger('change');
        t.frmEl.currency.empty().trigger('change');
        t.frmEl.location.empty().trigger('change');
        t.frmValidator.resetForm();
        t.optionsLocation();
        t.optionsProduct();
        t.loadImageViewer("");
        t.frmEl.termination_date.datepicker('setStartDate', null);
        t.frmEl.expiration_date.datepicker('setStartDate', null);
        // Reset network fields
        t.toggleNetworkFields(false);
        t.frmEl.added_via.filter('[value="1"]').prop('checked', true);
        t.frmEl.product_id.val(null).trigger('change');
        t.frmEl.version.val(null).trigger('change');
        t.frmEl.all_version_check.prop('checked', false);
        t.frmEl.is_tracked_div.addClass('hide');
    };
    t.el.submit = t.mdl.find('#submitBtn');
    t.action = '';
    t.frmEl.internal_place.trigger("change");

    // ========== TOGGLE NETWORK FIELDS ==========
    t.toggleNetworkFields = function (show) {
        // Find parent columns for the three fields
        var productField = t.frmEl.product_id.closest('.col-md-6');
        var versionField = t.frmEl.version.closest('.col-md-6');
        var allVersionField = t.frmEl.all_version_check.closest('.col-md-6');

        if (show) {
            // Show fields
            productField.show();
            versionField.show();
            allVersionField.show();
            t.frmEl.is_tracked_div.removeClass('hide');

            // Enable fields
            t.frmEl.product_id.prop('disabled', false);
            t.frmEl.version.prop('disabled', false);
            t.frmEl.all_version_check.prop('disabled', false);

            // Initialize Select2 for network fields if needed
            t.frmEl.product_id.trigger('change');
            t.frmEl.version.trigger('change');
        } else {
            // Hide fields
            productField.hide();
            versionField.hide();
            allVersionField.hide();
            t.frmEl.is_tracked_div.addClass('hide');

            // Disable and clear fields
            t.frmEl.product_id.prop('disabled', true).val(null).trigger('change');
            t.frmEl.version.prop('disabled', true).val(null).trigger('change');
            t.frmEl.all_version_check.prop('disabled', true).prop('checked', false);

            // Show version section if hidden by "check all versions"
            t.frm.find('.versionSection').show();
        }
    };

    // ========== INIT RADIO TOGGLE ==========
    t.initRadioToggle = function () {
        // Initial state - hide network fields
        t.toggleNetworkFields(false);
        t.frmEl.added_via.filter('[value="1"]').prop('checked', true);

        // Radio change event
        t.frmEl.added_via.on('change', function () {
            var isNetwork = $(this).val() == '2';
            t.toggleNetworkFields(isNetwork);
        });
    };

    // ========== INIT VERSION TOGGLE ==========
    t.initVersionToggle = function () {
        t.frmEl.all_version_check.on('change', function () {
            var versionField = t.frmEl.version.closest('.col-md-6');
            if ($(this).is(':checked')) {
                versionField.hide();
                t.frmEl.version.prop('disabled', true).val(null).trigger('change');
            } else {
                versionField.show();
                t.frmEl.version.prop('disabled', false);
            }
        });
    };

    // Call initialization functions
    t.initRadioToggle();
    t.initVersionToggle();

    t.addLicenseModel = function (e) {

        if (typeof e != 'undefined') {
            e.preventDefault();
        }

        t.reset();
        t.el.mdl_title.text(t.config.translations.add_licenses);

        t.httpPostPath = t.config.url.add;
        t.action = 'add';
        t.frmEl.seats.attr('readonly', false);
        t.frmEl.unique__tag.attr('readonly',false);
        t.frmEl.id.val(null);
        t.mdl.modal('show');
    };

    t.editLicenseModel = function (id) {
        t.reset();
        t.el.mdl_title.text(t.config.translations.edit_licenses);
        t.frmEl.seats.attr('readonly', true);
        t.loadForm(id, "edit");
        t.action = 'edit';
        t.httpPostPath = t.config.url.update + "/" + id;
    };

    t.cloneLicenseModel = function (id) {
        t.reset();
        t.el.mdl_title.text(t.config.translations.add_licenses);
        t.loadForm(id, "clone");
        t.action = 'clone';
        t.frmEl.seats.attr('readonly', false);
        t.httpPostPath = t.config.url.add;
    };

    t.loadImageViewer = function (img, action) {
        if (
            img != "" &&
            img != null &&
            img != "null" &&
            (action == "edit")
        ) {
            t.frmEl.imgviewcover.removeClass("hide");
            t.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + img);
        } else {
            t.frmEl.imgviewcover.attr("src", "");
            t.frmEl.imgviewcover.addClass("hide");
        }
    };

    t.loadForm = function (id, action) {
        var http = $.get(t.config.url.edit + "/" + id + "/" + action);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {

                    t.frmEl.id.val(id);
                    t.fillForm(data, action);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };


    t.fillForm = function (record, action) {
        t.frmEl.company_id.val(record.data.company_id).trigger("change");
        t.frmEl.department.val(record.data.department_id).trigger("change");
        t.frmEl.category_id.val(record.data.category_id).trigger("change");
        t.frmEl.software_name.val(record.data.name);
        t.frmEl.uniqueSerial_id.val(record.data.serial);
        if (action == 'clone') {
            $("#unique_tag_row").show();
            $('#LicenseForm input[name="unique_tag"]').prop('readonly', false);
            $('#licensemodal input[name="unique_tag"]').val("").prop('readonly', false);
        }

        if (action == 'edit') {
            $("#unique_tag_row").show();
            $('#license-mdl-frm input[name="unique_tag"]').val(record.data.unique_tag).prop('readonly', true);
        }
        t.frmEl.license_to_name.val(record.data.license_name);
        t.frmEl.license_email.val(record.data.license_email);
        t.frmEl.seats.val(record.data.seats);
        t.frmEl.reassignable.attr('checked', record.data.reassignable == 1);
        t.frmEl.is_tracked.attr('checked', record.data.is_tracked == 1);

        t.frmEl.manufacturer_id.val(record.data.manufacturer_id).trigger("change");
        if (typeof record.dropdown == "object" && typeof record.dropdown.invoice == "object" && record.dropdown.invoice != null) {
            $.each(record.dropdown.invoice, function (i, v) {
                t.frmEl.invoice_id.append(new Option(v.text, v.id, true, true));
            });
        }
        t.frmEl.invoice_id.trigger('change');
        current_purchase_date = record.data.purchase_date;
        t.frmEl.purchase_date.datepicker("update", current_purchase_date);
        order_no = record.data.order_number;
        t.frmEl.order_number.val(order_no);
        if (record.data.supplier_id != null) {
            current_supplier_id = record.data.supplier_id;
        }
        t.frmEl.supplier_id.val(current_supplier_id).trigger('change');

        t.frmEl.currency.val(record.data.currency).trigger('change');
        t.frmEl.purchase_cost.val(record.data.purchase_cost);
        t.frmEl.purchase_order.val(record.data.purchase_order);
        t.frmEl.agreement_no.val(record.data.agreement_no);
        t.frmEl.expiration_date.datepicker('update', record.data.expiration_date);
        t.frmEl.depreciation_id.val(record.data.depreciation_id).trigger('change');
        t.frmEl.maintained.attr('checked', record.data.maintained == 1);
        t.frmEl.termination_date.datepicker('update', record.data.termination_date);
        t.frmEl.notes.val(record.data.notes);
        t.frmEl.support.val(record.data.support);

        // ========== HANDLE NETWORK/MANUAL TOGGLE ==========
        if (record.data.added_via == 2) {
            t.frmEl.added_via.filter('[value="2"]').prop('checked', true);
            t.toggleNetworkFields(true);

            // Populate product and version if data exists
            if (record.data.product_ids) {
                t.frmEl.product_id.val(record.data.product_ids).trigger('change');
            }
            if (record.data.versions) {
                t.frmEl.version.val(record.data.versions).trigger('change');
            }
            if (record.data.check_all_versions == 1) {
                t.frmEl.all_version_check.prop('checked', true);
                t.frmEl.version.closest('.col-md-6').hide();
                t.frmEl.version.prop('disabled', true);
            }
        } else {
            t.frmEl.added_via.filter('[value="1"]').prop('checked', true);
            t.toggleNetworkFields(false);
        }
        // =================================================

        if (typeof record.dropdown == "object" && typeof record.dropdown.category == "object") {
            t.frmEl.category_id.attr('data-noLoadField', true);
            t.frmEl.category_id.append(new Option(record.dropdown.category.text, record.dropdown.category.id, true, true));
            t.frmEl.category_id.attr('data-noLoadField', false);
            t.frmEl.category_id.trigger("change");
        }


        if (typeof record.data.dropdown == "object" && typeof record.data.dropdown == "object" && record.data.dropdown != null) {
            t.frmEl.software.append(new Option(record.data.dropdown.text, record.data.dropdown.text, true, true)).trigger("change");
        }

        if (record.data.Versions.length > 0) {
            t.frmEl.Version.html("");
            $.each(record.data.Versions, function (i, v) {
                var newOption = new Option(v.Version, v.id, true, true);
                t.frmEl.Version.append(newOption);
            });
        }

        if (record.data.product_id.length > 0) {
            t.frmEl.software.html("");
            $.each(record.data.product_id, function (i, v) {
                var newOption = new Option(v.Caption, v.id, true, true);
                t.frmEl.software.append(newOption);
            });

            if (record.data.Versions.length > 0) {
                $.each(record.data.Versions, function (i, v) {
                    t.selected_version.push(v.Version);
                });
            }
            t.frmEl.software.trigger("change");
        }

        if (typeof record.dropdown == "object" && typeof record.dropdown.location == "object" && record.dropdown.location != null) {
            t.frmEl.location.append(new Option(record.dropdown.location.text, record.dropdown.location.id, true, true)).trigger("change");
        }

        if (typeof record.dropdown == "object" && typeof record.dropdown.internal_place == "object" && record.dropdown.internal_place != null) {
            t.internal_places.push(record.dropdown.internal_place.id);
        }
        t.frmEl.internal_place.trigger("change");

        if (typeof record.dropdown == "object" && typeof record.dropdown.department == "object" && record.dropdown.department != null) {
            t.frmEl.department.append(new Option(record.dropdown.department.text, record.dropdown.department.id, true, true)).trigger("change");
        } else {
            t.frmEl.department.val(null).trigger("change");
        }
        t.loadImageViewer(record.data.image, action);

        t.mdl.modal('show');
    };

    t.frmEl.company_id.on("change", function () {
        t.frmEl.location.val(null).trigger("change");
        t.frmEl.department.val(null).trigger("change");
        t.frmEl.internal_place.val(null).trigger("change");
        t.frmEl.invoice_id.val(null).trigger("change");
    });


    t.frmEl.company_id.select2($.extend({},
        select2Opts, {

        dropdownParent: t.frmEl.company_id.parent(),
        ajax: {
            url: t.config.url.getCompanyOptions,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_company,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));

    t.frmEl.manufacturer_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.manufacturer_id.parent(),
        ajax: {
            url: t.config.url.getManufacturerByAjax,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_the_manufacturer,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));


    t.frmEl.category_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.category_id.parent(),
        ajax: {
            url: t.config.url.getCategoryByAjax,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_Category,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));
   
    t.frmEl.supplier_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.supplier_id.parent(),
        ajax: {
            url: t.config.url.getSupplierByAjax,

            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_the_Supplier,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));

    t.frmEl.currency.select2(
        $.extend({}, 
        select2Opts, {
        dropdownParent: t.frmEl.currency.parent(),
        ajax: {
            url: t.config.url.getCurrencyOptions,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_Currency_Format,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));


    t.frmEl.depreciation_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.depreciation_id.parent(),
        ajax: {
            url: t.config.url.getDepreciationOptions,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.Select_Depreciation,
        language: {
            noResults: function () {
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text;
        }
    }));

    t.frmEl.invoice_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.invoice_id.parent(),
        ajax: {
            url: t.config.url.getInvoiceByAjax,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!t.frmEl.company_id.val()) {
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
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        // minimumInputLength: 1,
        placeholder: config.translations.Select_the_Purchase_Invoice,
        language: {
            noResults: function () {
                if (!t.frmEl.company_id.val()) {
                    return "Please select the company than fetch data";
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
    })).on("change", function (e) {
        if (t.frmEl.invoice_id.val() == null) {
            if (current_purchase_date != null) {
                purchasedate = current_purchase_date.split('-').join('/')
                t.frmEl.purchase_date.val(purchasedate);
            } else {
                t.frmEl.purchase_date.val(null);
            }
            if (current_supplier_id != null) {
                t.frmEl.supplier_id.val(current_supplier_id).trigger('change');

            } else {
                t.frmEl.supplier_id.empty('').trigger('change');
            }
            if (order_no != null && order_no != "") {
                t.frmEl.order_number.val(order_no);
            } else {
                t.frmEl.order_number.val(null);
            }
        } else {
            t.getPurchaseDate();
        }
    });

    t.getPurchaseDate = function (e) {
        //this part is added to handle re-selection from Select2 dropdown
        var selectedOption = t.frmEl.invoice_id.find(':selected');
        if (!selectedOption.attr('data-invoice_date')) {
            // Use stored default values for options without data attributes
            if (current_purchase_date != null) {
                purchasedate = current_purchase_date.split('-').join('/');
                t.frmEl.purchase_date.val(purchasedate);
            } else {
                t.frmEl.purchase_date.val('');
            }
            //t.frmEl.supplier_id.empty();
            if (current_supplier_id) {
                t.frmEl.supplier_id.val(current_supplier_id).trigger('change');
            }
            if (order_no != null) {
                t.frmEl.order_number.val(order_no);
            } else {
                t.frmEl.order_number.val('');
            }
            return;
        }
        //end this part is added to handle re-selection from Select2 dropdown
        var date = t.frmEl.invoice_id.find(':selected').attr('data-invoice_date');
        t.frmEl.purchase_date.val(date);
        var supplier_id = t.frmEl.invoice_id.find(':selected').attr('data-id');
        t.frmEl.supplier_id.val(supplier_id).trigger('change');
        if (supplier_id == undefined && t.action == "edit") {
            t.frmEl.supplier_id.val(current_supplier_id).trigger('change');
        }
        var order_number = t.frmEl.invoice_id.find(':selected').attr('data-order-number');
        t.frmEl.order_number.val(order_number);
        var purchase_order = t.frmEl.invoice_id.find(':selected').attr('data-po-number');
        t.frmEl.purchase_order.val(purchase_order);
        var purchase_cost = t.frmEl.invoice_id.find(':selected').attr('data-purchase-cost');
        t.frmEl.purchase_cost.val(purchase_cost || '0.00');
        var purchase_currency = t.frmEl.invoice_id.find(':selected').attr('data-currency');
        t.frmEl.currency.val(purchase_currency).trigger("change");
    }

    t.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy", }).on('changeDate', function (e) {
        var purchaseDate = e.date;
        t.frmEl.termination_date.datepicker('setStartDate', purchaseDate);
        t.frmEl.expiration_date.datepicker('setStartDate', purchaseDate);
        var terminationDate = convertToTimestamp(t.frmEl.termination_date.val());
        var purchaseDate = convertToTimestamp(t.frmEl.purchase_date.val());
        var expirationDate = convertToTimestamp(t.frmEl.expiration_date.val());

        if (terminationDate && terminationDate < purchaseDate) {
            t.frmEl.termination_date.val('');
        }
        if (expirationDate && expirationDate < purchaseDate) {
            t.frmEl.termination_date.val('');
        }
    });

    t.frmEl.department.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.department.parent(),
        ajax: {
            url: t.config.url.getAssetDepartments,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!t.frmEl.company_id.val()) {
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
                    company_id: t.frmEl.company_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        // minimumInputLength: 1,
        placeholder: 'Select Department',
        language: {
            noResults: function () {
                if (!t.frmEl.company_id.val()) {
                    return "Please select the company than fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    }));

    t.optionsLocation = function () {
        t.frmEl.location.select2($.extend({}, select2Opts, {
            dropdownParent: t.frmEl.location.parent(),
            ajax: {
                url: t.config.getLocationByAjax,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!t.frmEl.company_id.val()) {
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
                            return t.frmEl.company_id.val();
                        }
                    };
                },
                delay: 300
            },
            allowClear: true,
            // minimumInputLength: 1,
            placeholder: config.translations.Select_the_Location,
            language: {
                noResults: function () {
                    if (!t.frmEl.company_id.val()) {
                        return "Please select the company than fetch data";
                    }
                    return "No Data Found";
                }
            },
            templateSelection: function (data, container) {
                $(container).attr('title', data.text);
                return data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
            },
        })).on("change", function (e) {
            t.getInternalPlace();
        });

        t.frmEl.internal_place.select2($.extend({}, select2Opts, {
            placeholder: config.translations.select_internal_place,
            allowClear: true,
            dropdownParent: t.frmEl.internal_place.parent(),
            language: {
                noResults: function () {
                    if (!t.frmEl.location.val()) {
                        return "Select location then fetch internal place";
                    }
                    return "No Data Found";
                }
            }
        }));


        t.optionsProduct = function () {
            t.frmEl.software.empty().append(new Option("", true));
            $.each(t.config.product, function (i, v) {
                t.frmEl.software.append(new Option(v.text, v.id));
            });
            t.frmEl.software.val("").on("change", function (e) {
                t.refillVersion();
            });
        };

        t.getInternalPlace = function (e) {
            if (typeof e !== "undefined") {
                e.preventDefault();
            }
            t.frmEl.internal_place.empty().append(new Option(config.translations.select_internal_place, "", true, true));
            t.frmEl.internal_place.trigger("change");
            var location_id = t.frmEl.location.val();
            if (location_id > 0) {
                $.get(t.config.getInternalPlaceByAjax + '/' + location_id).done(function (data) {
                    if (typeof data == "object" && data.results != 'undefined' && data.results.length) {
                        $.each(data.results, function (i, v) {
                            if ($.inArray(v.id, t.internal_places) !== -1) {
                                t.frmEl.internal_place.append(new Option(v.text, v.id, true, true));
                            } else {
                                t.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
                            }
                        });
                    }
                }).always(function () {
                    t.frmEl.internal_place.trigger("change");
                    t.internal_places = [];
                });
            }
        }
    };



    // Initialize Select2 for product_id and version (network fields)
    t.frmEl.product_id.select2($.extend({}, select2Opts, {
        placeholder: 'Select Software',
        allowClear: true,
        dropdownParent: t.frmEl.product_id.parent()
    }));

    t.frmEl.version.select2($.extend({}, select2Opts, {
        placeholder: 'Select Version',
        allowClear: true,
        dropdownParent: t.frmEl.version.parent()
    }));
    t.frmEl.software.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.software.parent(),
        ajax: {
            url: t.config.getSoftwareByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300,
        },
        allowClear: true,
        placeholder: "Select License"
    })).on("change", function (e) {
        t.refillVersion();
        t.updatename();
    });

    t.updatename = function() {
    var selectedOption = t.frmEl.software.find(':selected');
    var softwareName = '';
    
    // Check if a valid option is selected
    if (selectedOption.length > 0 && selectedOption.val() && selectedOption.val() !== '') {
        softwareName = selectedOption.text().trim();
        // Remove placeholder text
        if (softwareName === 'Select License' || softwareName === 'Select Software') {
            softwareName = '';
        }
        t.frmEl.software_name.val(softwareName);
    }
    
    // Update the software_name field
    
    };


    t.refillVersion = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.version = [];
        t.frmEl.version.empty();
        var type_val = t.frmEl.software.val();

        if (type_val != "") {
            $.get(t.config.getVersionBySoftwares + "/?product_id=" + type_val).done(function (data) {
                if (typeof data == "object") {
                    t.data.version = data.results;
                    $.each(data.results, function (i, v) {
                        if ($.inArray(v.text, t.selected_version) !== -1) {
                            t.frmEl.version.append(new Option(v.text, v.text, true, true));
                        } else {
                            t.frmEl.version.append(new Option(v.text, v.text, false, false));
                        }
                    });
                }
            }).always(function () {
                t.frmEl.version.trigger("change");
                t.selected_version = [];
            });
        }
    }

    t.selectAll = function (e) {
        $("#Version > option").prop("selected", true);
        t.frmEl.Version.trigger("change");
    }
    
    // ========== HANDLE SUBMIT ==========
    t.handleSubmit = function(e) {

        e.preventDefault();
        if (t.frmValidator.form() == false) {
            t.frmEl.submit.prop("disabled", false);
            return false;
        }
        t.frmEl.submit.prop("disabled", true);
        if (t.httpCall != true) {
            t.frmEl.submit.prop("disabled", false);
            return false;
        }
        t.httpCall = false;
            
        var formData = new FormData(t.frm[0]);
        if (!formData.has('category_id')) {
            formData.append('category_id', '');
        }
        formData.append('_token', t.tokenEl.attr('content'));
        for (const [key, value] of formData.entries()) {
                console.log(key, value);
            }
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    if (t.request_from == "detail") {
                        t.myapp.infoPhase.reload();
                        t.myapp.seatPhase.reload();
                        return;
                    }
                    t.myapp.reload();
                } else {
                    sweetAlert('center', 'error', data);
                    t.frmEl.submit.prop("disabled", false);
                    t.httpCall = true;
                }
            }
        });

        http.fail(function (xhr, status, error) {
            console.error('AJAX Error:', error);
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
            t.frmEl.submit.prop("disabled", false);
            t.httpCall = true;
        });
        
        http.always(function() {
            t.frmEl.submit.prop("disabled", false);
            t.httpCall = true;
        });
    };
    if ($(this).is(":checked")) {
        t.frm.find(".versionSection").hide();
        t.frmEl.Version.val(null).trigger("change");
    } else {
        t.frm.find(".versionSection").show();
    }
    t.frmEl.submit.on('click', $.proxy(t.handleSubmit, t));
}


