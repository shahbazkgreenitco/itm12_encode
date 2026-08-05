var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.mdl = $('#courier_info_modal');

    t.frm = t.mdl.find('#courier_form');

    t.frmEl = {};
    t.frmEl.base_charge = t.frm.find('#base_charge');
    t.frmEl.rate_per_gram = t.frm.find('#rate_per_gram');
    t.frmEl.volumetric_divisor = t.frm.find("#volumetric_divisor");
    t.frmEl.enable_volumetric_charge = t.frm.find("#enable_volumetric_charge");
    t.frmEl.fragile_liquid_charge = t.frm.find("#fragile_liquid_charge");
    t.frmEl.insurance_rate = t.frm.find("#insurance_rate");
    t.frmEl.insurance_handling_charges = t.frm.find("#insurance_handling_charges");
    t.frmEl.tax_name = t.frm.find("#tax_id");
    t.frmEl.taxNameDef = t.frm.find('.taxNameDef');
    t.frmEl.tax_element = t.frm.find('#tax_element');
    t.btn = {};
    t.btn.submit = t.frm.find('#courierBtnSubmit');

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var formData = new FormData(t.frm[0]);
        formData.append('supplier_id', config.supplier_id);

        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.resetFrm();
                    window.location.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong_check_details_are_correct,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.create = function (e) {
        e.preventDefault();
        t.mdl.modal("show");
        t.httpPostPath = t.config.url.addCourierDetails + '/' + t.config.supplier_id;
        t.resetFrm();

        const fetchUrl = t.config.url.getCourierDetails + '/' + t.config.supplier_id;
        $.get(fetchUrl).done(function (data) {
            if (data && typeof data === 'object' && data.data && data.data.base_charge) {

                t.frmEl.base_charge.val(data.data.base_charge.base_charge || '');
                t.frmEl.rate_per_gram.val(data.data.base_charge.rate_per_gram || '');
                t.frmEl.volumetric_divisor.val(data.data.base_charge.volumetric_divisor || '');
                t.frmEl.fragile_liquid_charge.val(data.data.base_charge.fragile_liquid_charge || '');
                t.frmEl.insurance_rate.val(data.data.base_charge.insurance_rate || '');
                t.frmEl.insurance_handling_charges.val(data.data.base_charge.insurance_handling_charges || '');
                
                const val_checkbox = data.data.base_charge.enable_volumetric_charge;
                if (val_checkbox === 1 || val_checkbox === '1') {
                    t.frmEl.enable_volumetric_charge.prop('checked', true).trigger('change');
                } else {
                    t.frmEl.enable_volumetric_charge.prop('checked', false).trigger('change');
                }

                // Handle tax dropdown
                if (data.data.dropdown?.tax) {
                    t.frmEl.tax_name.empty();
                    const option = new Option(data.data.dropdown.tax.text, data.data.dropdown.tax.id, true, true);
                    t.frmEl.tax_name.append(option).trigger('change', { triggeredChange: true });
                }

                // Store tax elements for refill
                if (data.data.dropdown?.taxElements) {
                    t.mdl.loadedData = {
                        taxElements: data.data.dropdown.taxElements || {} 
                    };
                    // Trigger refill after a short delay to ensure select2 is updated
                    setTimeout(function() {
                        t.refillCustomElements();
                    }, 100);
                }
                
            } else {
                console.log('No existing courier data found - this will be a new entry');
                // Reset form for new entry (already done above)
            }
        }).fail(function (xhr, status, error) {
            console.log('Failed to fetch courier details:', error);
            // Continue with form for new entry even if fetch fails
        });
    };

    t.resetFrm = function () {
        t.frmEl.base_charge.val('');
        t.frmEl.rate_per_gram.val('');
        t.frmEl.volumetric_divisor.val('');
        t.frmEl.fragile_liquid_charge.val('');
        t.frmEl.insurance_rate.val('');
        t.frmEl.insurance_handling_charges.val('');
        t.frmEl.tax_name.empty().trigger("change", { triggeredChange: true });
         t.frmEl.taxNameDef.val('');
        t.frmEl.tax_element.val('');
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        ignore: [],
        rules: {
            insurance_handling_charges: {
                required: function(element) {
                    return $("#insurance_rate").val() !== ""
                }
            },
           
        },errorPlacement: function(error, element) {
            error.appendTo( element.parent().parent("div") );
        }
    });

    var select2Opts = { width: "100%" };
    t.frmEl.tax_name.append(new Option(config.translations.select_tax_name, '', true, false));
    t.frmEl.tax_name.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        ajax: {
            url: t.config.url.custom_tax,
            dataType: "json"
        },
        allowClear: true,
        placeholder: config.translations.tax_name,
    })).on("change", function (e, params) {
        if (params && params.triggeredChange) {
            return;
        }
      t.refillCustomElements(); 
    });

    t.refillCustomElements = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEl.tax_element.empty().append(new Option(config.translations.chooseTaxname, ""));
        var type_val = t.frmEl.tax_name.val();
        if (type_val > 0 && !isNaN(type_val)) {
            $(".optionElementDiv").remove(); 
            $.get(t.config.url.custom_element + "/" + type_val).done(function (data) {
                if (typeof data === "object" && data.data.length) {
                    $.each(data.data, function (i, v) {
                        var previousValue = t.mdl.loadedData?.taxElements?.[v.tax_element] || '';
                        var html = `
                            <div class="optionElementDiv pt-4">
                                <div class="col-md-12">
                                    <label for="insurance_rate" class="form-label b1-text fw-bold mb-2">${v.tax_element}</label>
                                        <div class="input-group">
                                        <span class="input-group-text">
                                            <span>
                                                <svg width="20" height="20" viewBox="0 0 20 20"
                                                    fill="none">
                                                    <path
                                                        d="M16.25 4.375V6.875C16.25 7.04076 16.1842 7.19973 16.0669 7.31694C15.9497 7.43415 15.7908 7.5 15.625 7.5C15.4592 7.5 15.3003 7.43415 15.1831 7.31694C15.0658 7.19973 15 7.04076 15 6.875V5H10.625V15H12.5C12.6658 15 12.8247 15.0658 12.9419 15.1831C13.0592 15.3003 13.125 15.4592 13.125 15.625C13.125 15.7908 13.0592 15.9497 12.9419 16.0669C12.8247 16.1842 12.6658 16.25 12.5 16.25H7.5C7.33424 16.25 7.17527 16.1842 7.05806 16.0669C6.94085 15.9497 6.875 15.7908 6.875 15.625C6.875 15.4592 6.94085 15.3003 7.05806 15.1831C7.17527 15.0658 7.33424 15 7.5 15H9.375V5H5V6.875C5 7.04076 4.93415 7.19973 4.81694 7.31694C4.69973 7.43415 4.54076 7.5 4.375 7.5C4.20924 7.5 4.05027 7.43415 3.93306 7.31694C3.81585 7.19973 3.75 7.04076 3.75 6.875V4.375C3.75 4.20924 3.81585 4.05027 3.93306 3.93306C4.05027 3.81585 4.20924 3.75 4.375 3.75H15.625C15.7908 3.75 15.9497 3.81585 16.0669 3.93306C16.1842 4.05027 16.25 4.20924 16.25 4.375Z"
                                                        fill="black" />
                                                </svg>
                                            </span>
                                        </span>
                                        <input type="number" name="tax_percentage[${v.id}]" min="0" max="100" id="tax_percentage" class="form-control" placeholder="${config.translations.percentage}" value="${previousValue}" />
                                    </div>
                                </div>
                            </div>
                        `;
                        // var html = `
                        //     <div class="row optionElementDiv">
                        //         <div class="form-group col-md-12">
                        //             <label for="title" class="control-label col-md-4">${v.tax_element}</label>
                        //             <div class="col-md-7">
                        //                 <div class="input-group">
                        //                     <span class="input-group-addon"><i class="fa fa-calendar-plus-o"></i></span>
                        //                     <input type="number" id="tax_percentage" 
                        //                         name="tax_percentage[${v.id}]" 
                        //                         min="0" max="100" 
                        //                         class="form-control" 
                        //                         placeholder="Percentage" 
                        //                         value="${previousValue}" />
                        //                 </div>
                        //             </div>
                        //         </div>
                        //     </div>`;
                        $(".customOptionsHolders").append(html);
                    });
                }
            }).always(function () {
                 t.frmEl.tax_element.trigger("change");
            });
        } else {
             $(".optionElementDiv").remove(); 
             t.frmEl.tax_element.trigger("change");
        }
    };

    $(document).ready(function() {
        function toggleVolumetricDivisor() {
            if ($('#enable_volumetric_charge').is(':checked')) {
                $('#volumetric_divisor_group').removeClass('d-none');
            } else {
                $('#volumetric_divisor_group').addClass('d-none');
                $('#volumetric_divisor').val(''); 
            }
        }
        toggleVolumetricDivisor();
        $('#enable_volumetric_charge').on('change', toggleVolumetricDivisor);
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    $(document).on("click", "#courier_details_modal_btn", t.create);
};