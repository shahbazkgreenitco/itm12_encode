
var SettingsAdd = function (config) {
    // console.log("setting add");
    let defaultCompany = localStorage.getItem("Default_Company");
    let companyId = config.company_defulte
    ? config.company_defulte
    : null;
    var t = this;
    t.config = config;

    t.content = $('section.content');
    
    t.mdl = t.content.find('#settingsmdl');
    
    t.frm = t.mdl.find('#SettingForm');
 
    t.frmEl = {};
    t.frmEl.site_name = t.frm.find('#site_name');
    t.frmEl.full_multiple_companies_support = t.frm.find('#full_multiple_companies_support');
    t.frmEl.clear_logo = t.frm.find('#clear_logo');
    t.frmEl.brand = t.frm.find('#brand');
    t.frmEl.default_currency = t.frm.find('#default_currency');
    t.frmEl.alert_email = t.frm.find('#alert_email');
    t.frmEl.alerts_enabled = t.frm.find('#alerts_enabled');
    t.frmEl.accessory_block_checkin = t.frm.find('#accessory_block_checkin');
    t.frmEl.auto_increment_assets = t.frm.find('#auto_increment_assets');
    t.frmEl.auto_increment_prefix = t.frm.find('#auto_increment_prefix');
    t.frmEl.qr_code = t.frm.find('#qr_code');
    t.frmEl.barcode_type = t.frm.find('#barcode_type');
    t.frmEl.qr_text = t.frm.find('#qr_text');
    t.frmEl.default_eula_text = t.frm.find('#default_eula_text');
    t.frmEl.ldap_enabled = t.frm.find('#ldap_enabled');
    t.frmEl.ldap_server = t.frm.find('#ldap_server');
    t.frmEl.ldap_server_cert_ignore = t.frm.find('#ldap_server_cert_ignore');
    t.frmEl.ldap_uname = t.frm.find('#ldap_uname');
    t.frmEl.ldap_pword = t.frm.find('#ldap_pword');
    t.frmEl.ldap_basedn = t.frm.find('#ldap_basedn');
    t.frmEl.ldap_filter = t.frm.find('#ldap_filter');
    t.frmEl.ldap_username_field = t.frm.find('#ldap_username_field');
    t.frmEl.ldap_lname_field = t.frm.find('#ldap_lname_field');
    t.frmEl.ldap_fname_field = t.frm.find('#ldap_fname_field');
    t.frmEl.ldap_auth_filter_query = t.frm.find('#ldap_auth_filter_query');
    t.frmEl.ldap_version = t.frm.find('#ldap_version');
    t.frmEl.ldap_active_flag = t.frm.find('#ldap_active_flag');
    t.frmEl.ldap_emp_num = t.frm.find('#ldap_emp_num');
    t.frmEl.ldap_email = t.frm.find('#ldap_email');
    t.frmEl.history_id = t.frm.find('#history_id');
    t.frmEl.custom_fieldset_id = t.frm.find('#custom_fieldset_id');
    t.frmEl.licence_custom_fieldset_id = t.frm.find('#licence_custom_fieldset_id');
    t.frmEl.accessories_custom_fieldset_id = t.frm.find('#accessories_custom_fieldset_id');
    t.frmEl.component_custom_fieldset_id = t.frm.find('#component_custom_fieldset_id');
    t.frmEl.consumable_custom_fieldset_id = t.frm.find('#consumable_custom_fieldset_id');
    t.frmEl.sez_custom_fieldset_id = t.frm.find('#sez_custom_fieldset_id');
    t.frmEl.barcode_type = t.frm.find('#barcode_type');
    t.frmEl.blockDeviceIntmateUser = t.frm.find('#block_device_movement_intimation_users');
    t.frmEl.enable_2fa_authentication = t.frm.find('#enable_2fa_authentication');
    t.frmEl.enable_2fa_authentication_with = t.frm.find('#enable_2fa_authentication_with');
    t.frmEl.alerts_enabled = t.frm.find('#alerts_enabled');
    t.frmEl.alert_email = t.frm.find('#alert_email');
    t.frmEl.alert_types = t.frm.find('#alerts_type');
    t.frmEl.alert_value = t.frm.find('#alert_value');
    t.frmEl.asset_tag_type = t.frm.find('#asset_tag_type');
    t.frmEl.asset_tag_separator = t.frm.find('#asset_tag_separator');
    t.frmEl.alert_users_email = t.frm.find('#alert_users_email');
    t.frmEl.device_report_email = t.frm.find('#device_report_email');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit2');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;
    t.httpPostPath = t.config.url.add
    t.resetFrm = {};

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
        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }     
    };   

    function initializeSummernote(element) {
        element.summernote({
            toolbar: [
                ['color', ['color']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ],
            minHeight: 200,
            focus: true
        });
    }

    // Initialize Summernote for each textarea
    initializeSummernote( t.frmEl.default_eula_text);

    $('#btnSubmit').click(function () {
        $('#img').show();
    });

    $('.plus_min--col').on("click",function () {
        $('.abv').removeClass('active');
        if ($(this).hasClass('collapsed')) {
            $(this).find('.abv').addClass('active');
        }
    });
    $('.plus_min1--col').on("click",function () {
        $('.blw').removeClass('active');
        if ($(this).hasClass('collapsed')) {
            $(this).find('.blw').addClass('active');
        }
    });

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            $('#img').hide();
            return false;
        }
        var frmData = new FormData(t.frm[0]);
         frmData.append("company_id", companyId);
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
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    window.location.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            // vex.dialog.alert("Something went wrong. Please check given details are correct");
            var data = {
             'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            $('#img').hide();
        });
    };

    $.validator.addMethod("customPrefix", function(value, element) {
        return this.optional(element) || /^[A-Za-z0-9/_-]+$/.test(value);
    }, "Only letters, numbers, /, _, and - are allowed.");

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            site_name: {
                required: true,
                str_name: false
            },
            alert_email: {
                required: '#alerts_enabled:checked',
                email: true
            },
            auto_increment_prefix:{
                required: true,
                customPrefix: true
            },
            qr_text:{
                required: true, 
                str_name: true
            },
            // tnc_content: {
            //     required: '#tnc_accept:checked',
            // },
            // ldap_server: {
            //     required: true, 
            //     remarks: true
            // },
            // ldap_uname: {
            //     required: true, 
            //     str_name: true
            // },
            // ldap_basedn:{
            //     required: true,
            //     str_name: true 
            // },
            // ldap_filter:{
            //     required: true,
            //     str_name: true
            // },
            // ldap_username_field:{
            //     required: true, 
            //     str_name: true
            // },
            // ldap_lname_field: {
            //     required: true, 
            //     str_name: true
            // },
            // ldap_fname_field: {
            //     required: true,
            //     str_name: true  
            // },
            // ldap_auth_filter_query: {
            //     required: true, 
            //     str_name: true 
            // },
            // ldap_version: {
            //     required: true,
            //     str_name: true 
            // },
            // ldap_active_flag: {
            //     required: true,
            //     str_name: true  
            // },
            // ldap_emp_num: {
            //     required: true,
            //     str_name: true   
            // },
            // ldap_email: {
            //     required: true, 
            //     email: true 
            // },
            default_eula_text: {
                remarks: false
            },
            default_currency:{
                required:true,
                str_name:true
            },
            ni_not_detected: {
                required: true,
            },
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
    });

    t.frmEl.enable_2fa_authentication_with.select2({
        width: '200%',
        allowclear : true,
    });

    t.frmEl.alert_users_email.select2({
        width: "100%",
        placeholder: config.translations.enter_email_comma,
        maximumSelectionLength: 10,
        ajax: {
            url: function (params) {
                return t.config.url.getUserNIByQuery;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results.map(function(item) {
                        return {
                            id: item.text,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more,
                    }
                };
            },
            cache: true
        },
        
    });

    t.frmEl.device_report_email.select2({
        width: "100%",
        placeholder: config.translations.enter_email_comma,
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getUserNIByQuery;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results.map(function(item) {
                        return {
                            id: item.text,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more,
                    }
                };
            },
            cache: true
        },
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        insertTag: function (data, tag) {
            data.push(tag);
        }
    });

    var select2Opts = { width: "100%" };
    t.frmEl.blockDeviceIntmateUser.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Enter first few letters of the User"
    }));

    t.frmEl.custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule + "/"+ 1,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));

    t.frmEl.licence_custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.licence_custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule +"/"+ 7,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));
    t.frmEl.accessories_custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.accessories_custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule +"/"+ 6,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));

    t.frmEl.component_custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.component_custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule +"/"+ 4,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));

    t.frmEl.consumable_custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.consumable_custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule +"/"+ 5,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));


    t.frmEl.sez_custom_fieldset_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.sez_custom_fieldset_id.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule +"/"+ 3,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_custom_fieldset
    }));

    if(t.config.blockDeviceIntimationUsers != null) {
        $.each(t.config.blockDeviceIntimationUsers, function(i, v) {
            t.frmEl.blockDeviceIntmateUser.append(new Option(v.first_name+" "+v.last_name, v.id, true, true)).trigger("change");
        })
    }
    
    $('#alerts_type').on("change", function() {
        if($(this).val() == '1') {
            $('.alert_value').removeClass('hide');
            $('.alert_users_email').addClass('hide');
        } else if($(this).val() == '2') {
            $('.alert_users_email').removeClass('hide');
            $('.alert_value').addClass('hide');
        } else {
            $('.alert_users_email').addClass('hide');
            $('.alert_value').addClass('hide');
        }
    });

    $('#asset_tag_type').on("change", function() {
        if($(this).val() == '3') {
            $('.CustomTag').removeClass('hide');
        }else{
            $('.CustomTag').addClass('hide');
        }
    });
    if (t.config.assetTagData && t.config.assetTagData.asset_tag_type == 3) {
        $('.CustomTag').removeClass('hide');
    } else {
        $('.CustomTag').addClass('hide');
    }

    $(document).ready(function() {
        $('#alerts_enabled').on("change", function() {
            if ($(this).is(':checked')) {
                $('.alert_email').removeClass('hide');
                $('.alerts_type').removeClass('hide');
                if(t.config.data.alerts_type == '1'){
                    $('.alert_value').removeClass('hide');
                }else if(t.config.data.alerts_type == '2'){
                    $('.alert_users_email').removeClass('hide');
                }else{
                    $('.alert_users_email').addClass('hide');
                    $('.alert_value').addClass('hide');
                }
            } else {
                $('.alert_email').addClass('hide');
                $('.alerts_type').addClass('hide');
                $('.alert_users_email').addClass('hide');
                $('.alert_value').addClass('hide');

            }
        }).trigger('change');
    });

    var select2Opts = { width: "100%" };
    if(typeof t.config.editAlertRoleReminder != "undefined" && typeof t.config.editAlertRoleReminder == "object" && Object.keys(t.config.editAlertRoleReminder).length > 0) {
        var existingIds = {};
        $.each(t.config.editAlertRoleReminder, function(i, role) {
            existingIds[role.id] = true;
        });
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            $.each(t.config.roles, function(i, v) {
                if (!existingIds[v.id]) {
                    t.frmEl.alert_value.append(new Option(v.name, v.id, false, false));
                } else {
                    t.frmEl.alert_value.append(new Option(v.name, v.id, true, true));
                }
            });
        }
    } else {
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            // t.frmEl.ni_not_detected_value.empty().append(new Option(config.translations.select_role, "null"));
            $.each(t.config.roles, function(i, v) {
                t.frmEl.alert_value.append(new Option(v.name, v.id, true, false)).trigger("change");
            })
        }
    }

    t.frmEl.asset_tag_type.select2({width: '100%', allowclear : true});
    t.frmEl.alert_value.select2({width: '100%', placeholder: config.translations.select_role, allowclear : true});
    t.frmEl.asset_tag_separator.select2({width: '100%', placeholder: config.translations.select_role, allowclear : true});
    t.frmEl.history_id.select2({width: '100%', allowclear : true});
    t.frmEl.default_currency.select2({width: '100%', placeholder: "Select Currency", allowclear : true});
    t.frmEl.accessory_block_checkin.select2({width: '100%', allowclear : true});
    t.frmEl.brand.select2({width: '100%', allowclear : true});
    t.frmEl.barcode_type.select2({width: '100%', allowclear : true});
    t.frmEl.alert_types.select2({width: '100%', allowclear : true});
    t.frmEl.enable_2fa_authentication_with.select2({width: '100%', allowclear : true});
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};

var SettingsEdit = function (config) {
    let companyId = config.company_defulte ? config.company_defulte : null;
    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#settingsmdl2');

    t.frm = t.mdl.find('#SettingForm2');

    t.frmEl = {};
    t.frmEl.alerts_enabled = t.frm.find('#New_ticket_created');
    t.frmEl.Ticket_reopened = t.frm.find('#Ticket_reopened');
    t.frmEl.Ticket_commented = t.frm.find('#Ticket_commented');
    t.frmEl.sla_breached = t.frm.find('#sla_breached');
    t.frmEl.New_user_added = t.frm.find('#New_user_added');
    t.frmEl.user_deleted = t.frm.find('#user_deleted');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit3');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;
    t.httpPostPath = t.config.url.edit
    t.resetFrm = {};

    $('#btnSubmit3').click(function () {
        // $('#img2').show();
    });

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            $('#img').hide();
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append("company_id", companyId);
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
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    window.location.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            // vex.dialog.alert("Something went wrong. Please check given details are correct");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            $('#img').hide();
        });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {

        }
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};

var DeviceSettings = function (config) {
    let defaultCompany = localStorage.getItem("Default_Company");
    let companyId = config.company_defulte ? config.company_defulte : null;
    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#settingsmdl3');

    t.frm = t.mdl.find('#SettingForm3');

    t.frmEl = {};
    t.frmEl.device_checkin = t.frm.find('#device_checkin');
    t.frmEl.device_checkout = t.frm.find('#device_checkout');
    t.frmEl.ni_not_detected = t.frm.find('#ni_not_detected_devices');
    t.frmEl.ni_not_detected_type = t.frm.find('#ni_not_detected_type');
    t.frmEl.ni_not_detected_value = t.frm.find('#ni_not_detected_value');
    t.frmEl.ni_not_detected_email = t.frm.find('#ni_not_detected_email');
    t.frmEl.live_monitor_auto_refresh = t.frm.find('#live_monitor_auto_refresh');
    t.frmEl.live_monitor_auto_run_in_minute = t.frm.find('#live_monitor_auto_run_in_minute');
    t.frmEl.send_reminder = t.frm.find('#send_reminder');
    t.frmEl.number_of_devices = t.frm.find('#number_of_devices');
    t.frmEl.send_reminder_users_type = t.frm.find('#send_reminder_users_type');
    t.frmEl.send_reminder_users_value = t.frm.find('#send_reminder_users_value');
    t.frmEl.send_reminder_users_email = t.frm.find('#send_reminder_users_email');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit3');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;
    t.httpPostPath = t.config.url.ni_not_detected_notification
    t.resetFrm = {};

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
        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }     
    };   

    $('#btnSubmit3').click(function () {
        $('#img3s').show();
    });

    if(t.config.ni_not_detected_set != null) {
        if( t.config.ni_not_detected_set.high_priority_ni_not_detected == 1 ) {
            $('.ni_not_detected_day').removeClass('hide');
            $('.ni_not_detected_type').removeClass('hide');
            if(t.frmEl.ni_not_detected_type.val() == '1') {
                $('.ni_not_detected_value').removeClass('hide');
                $('.ni_not_detected_email').addClass('hide');
            } else if(t.frmEl.ni_not_detected_type.val() == '2') {
                $('.ni_not_detected_email').removeClass('hide');
                $('.ni_not_detected_value').addClass('hide');
            } else {
                $('.ni_not_detected_email').addClass('hide');
                $('.ni_not_detected_value').addClass('hide');
            }
            $('#ni_not_detected_devices').on("change", function() {
                if ($(this).is(':checked')) {
                    $('.ni_not_detected_day').removeClass('hide');
                    $('.ni_not_detected_type').removeClass('hide');
                    if(t.frmEl.ni_not_detected_type.val() == '1') {
                        $('.ni_not_detected_value').removeClass('hide');
                        $('.ni_not_detected_email').addClass('hide');
                    } else if(t.frmEl.ni_not_detected_type.val() == '2') {
                        $('.ni_not_detected_email').removeClass('hide');
                        $('.ni_not_detected_value').addClass('hide');
                    } else {
                        $('.ni_not_detected_email').addClass('hide');
                        $('.ni_not_detected_value').addClass('hide');
                    }
                } else {
                    $('.ni_not_detected_day').addClass('hide');
                    $('.ni_not_detected_type').addClass('hide');
                    $('.ni_not_detected_email').addClass('hide');
                    $('.ni_not_detected_value').addClass('hide');
                }
            });
        } else {
            $('#ni_not_detected_devices').on("change", function() {
                if ($(this).is(':checked')) {
                    $('.ni_not_detected_day').removeClass('hide');
                    $('.ni_not_detected_type').removeClass('hide');
                    if(t.frmEl.ni_not_detected_type.val() == '1') {
                        $('.ni_not_detected_value').removeClass('hide');
                        $('.ni_not_detected_email').addClass('hide');
                    } else if(t.frmEl.ni_not_detected_type.val() == '2') {
                        $('.ni_not_detected_email').removeClass('hide');
                        $('.ni_not_detected_value').addClass('hide');
                    } else {
                        $('.ni_not_detected_email').addClass('hide');
                        $('.ni_not_detected_value').addClass('hide');
                    }
                } else {
                    $('.ni_not_detected_day').addClass('hide');
                    $('.ni_not_detected_type').addClass('hide');
                    $('.ni_not_detected_email').addClass('hide');
                    $('.ni_not_detected_value').addClass('hide');
                }
            });
        }
    } else {
        $('#ni_not_detected_devices').on("change", function() {
            if ($(this).is(':checked')) {
                $('.ni_not_detected_type').removeClass('hide');
                $('.ni_not_detected_day').removeClass('hide');
                if(t.frmEl.ni_not_detected_type.val() == '1') {
                    $('.ni_not_detected_value').removeClass('hide');
                    $('.ni_not_detected_email').addClass('hide');
                } else if(t.frmEl.ni_not_detected_type.val() == '2') {
                    $('.ni_not_detected_email').removeClass('hide');
                    $('.ni_not_detected_value').addClass('hide');
                } else {
                    $('.ni_not_detected_email').addClass('hide');
                    $('.ni_not_detected_value').addClass('hide');
                }
            } else {
                $('.ni_not_detected_day').addClass('hide');
                $('.ni_not_detected_type').addClass('hide');
                $('.ni_not_detected_email').addClass('hide');
                $('.ni_not_detected_value').addClass('hide');
            }
        });
    }

    $('#ni_not_detected_type').on("change", function() {
        if(t.frmEl.ni_not_detected_type.val() == '1') {
            $('.ni_not_detected_value').removeClass('d-none');
            $('.ni_not_detected_email').addClass('d-none');
        } else if(t.frmEl.ni_not_detected_type.val() == '2') {
            $('.ni_not_detected_email').removeClass('d-none');
            $('.ni_not_detected_value').addClass('d-none');
        } else {
            $('.ni_not_detected_email').addClass('d-none');
            $('.ni_not_detected_value').addClass('d-none');
        }
    });

    if(t.config.ni_not_detected_set != null) {
        if( t.config.ni_not_detected_set.send_reminder == 1 ) {
            $('.number_of_devices').removeClass('d-none');
            $('.send_reminder_users_type').removeClass('d-none');
            if(t.frmEl.send_reminder_users_type.val() == '1') {
                $('.send_reminder_users_value').removeClass('d-none');
                $('.send_reminder_users_email').addClass('d-none');
            } else if(t.frmEl.send_reminder_users_type.val() == '2') {
                $('.send_reminder_users_email').removeClass('d-none');
                $('.send_reminder_users_value').addClass('d-none');
            } else {
                $('.send_reminder_users_email').addClass('d-none');
                $('.send_reminder_users_value').addClass('d-none');
            }
            $('#send_reminder').on("change", function() {
                if ($(this).is(':checked')) {
                    $('.number_of_devices').removeClass('d-none');
                    $('.send_reminder_users_value').removeClass('d-none');
                    $('.send_reminder_users_type').removeClass('d-none');
                    if(t.frmEl.send_reminder_users_type.val() == '1') {
                        $('.send_reminder_users_value').removeClass('d-none');
                        $('.send_reminder_users_email').addClass('d-none');
                    } else if(t.frmEl.send_reminder_users_type.val() == '2') {
                        $('.send_reminder_users_email').removeClass('d-none');
                        $('.send_reminder_users_value').addClass('d-none');
                    } else {
                        $('.send_reminder_users_email').addClass('d-none');
                        $('.send_reminder_users_value').addClass('d-none');
                    }
                } else {
                    $('.number_of_devices').addClass('d-none');
                    $('.send_reminder_users_type').addClass('d-none');
                    $('.send_reminder_users_email').addClass('d-none');
                    $('.send_reminder_users_value').addClass('d-none');
                }
            });
        } else {
            $('#send_reminder').on("change", function() {
                if ($(this).is(':checked')) {
                    $('.number_of_devices').removeClass('d-none');
                    $('.send_reminder_users_type').removeClass('d-none');
                    $('.send_reminder_users_value').removeClass('d-none');
                    if(t.frmEl.send_reminder_users_type.val() == '1') {
                        $('.send_reminder_users_value').removeClass('d-none');
                        $('.send_reminder_users_email').addClass('d-none');
                    } else if(t.frmEl.send_reminder_users_type.val() == '2') {
                        $('.send_reminder_users_email').removeClass('d-none');
                        $('.send_reminder_users_value').addClass('d-none');
                    } else {
                        $('.send_reminder_users_email').addClass('d-none');
                        $('.send_reminder_users_value').addClass('d-none');
                    }
                } else {
                    $('.number_of_devices').addClass('d-none');
                    $('.send_reminder_users_type').addClass('d-none');
                    $('.send_reminder_users_email').addClass('d-none');
                    $('.send_reminder_users_value').addClass('d-none');
                }
            });
        }
    } else {
        $('#send_reminder').on("change", function() {
            if ($(this).is(':checked')) {
                $('.send_reminder_users_type').removeClass('d-none');
                $('.number_of_devices').removeClass('d-none');
                $('.send_reminder_users_type').removeClass('d-none');
                if(t.frmEl.send_reminder_users_type.val() == '1') {
                    $('.send_reminder_users_value').removeClass('d-none');
                    $('.send_reminder_users_email').addClass('d-none');
                } else if(t.frmEl.send_reminder_users_type.val() == '2') {
                    $('.send_reminder_users_email').removeClass('d-none');
                    $('.send_reminder_users_value').addClass('d-none');
                } else {
                    $('.send_reminder_users_email').addClass('d-none');
                    $('.send_reminder_users_value').addClass('d-none');
                }
            } else {
                $('.number_of_devices').addClass('d-none');
                $('.send_reminder_users_type').addClass('d-none');
                $('.send_reminder_users_email').addClass('d-none');
                $('.send_reminder_users_value').addClass('d-none');
            }
        });
    }
    $('#send_reminder_users_type').on("change", function() {
        if(t.frmEl.send_reminder_users_type.val() == '1') {
            $('.send_reminder_users_value').removeClass('d-none');
            $('.send_reminder_users_email').addClass('d-none');
        } else if(t.frmEl.send_reminder_users_type.val() == '2') {
            $('.send_reminder_users_email').removeClass('d-none');
            $('.send_reminder_users_value').addClass('d-none');
        } else {
            $('.send_reminder_users_email').addClass('d-none');
            $('.send_reminder_users_value').addClass('d-none');
        }
    });

    

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            $('#img').hide();
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append("company_id", companyId);
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
                    sweetAlert('center', 'success', data);
                    window.location.reload();
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
            $('#img').hide();
        });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            ni_not_detected: {
                required: function(element) {
                    return $('#ni_not_detected_devices').is(':checked') ? true:false;
                }
            },
            ni_not_detected_type: {
                required: function(element) {
                    return $('#ni_not_detected_devices').is(':checked') ? true:false;
                }
            },
            ni_not_detected_value: {
                required: function(element) {
                    return $('#ni_not_detected_type').val() == '1' ? true:false;
                }
            },
            ni_not_detected_email: {
                required: function(element) {
                    return $('#ni_not_detected_type').val() == '2' ? true:false;
                },
            },
            number_of_devices: {
                required: function(element) {
                    return $('#send_reminder').is(':checked') ? true:false;
                }
            },
            send_reminder_users_type: {
                required: function(element) {
                    return $('#send_reminder').is(':checked') ? true:false;
                }
            },
            send_reminder_users_value: {
                required: function(element) {
                    return $('#send_reminder_users_type').val() == '1' ? true:false;
                }
            },
            send_reminder_users_email: {
                required: function(element) {
                    return $('#send_reminder_users_type').val() == '2' ? true:false;
                },
            },
            live_monitor_auto_run_in_minute: {
                min: 1,
                required: true,
                digits: true,
            },
            
            messages: {
                ni_not_detected_type: "Please select a type.",
                ni_not_detected_value: "Please enter a value.",
                ni_not_detected_email: "Please enter a valid email.",
                send_reminder_users_type: "Please select a type.",
                send_reminder_users_value: "Please enter a value.",
                send_reminder_users_email: "Please enter a valid email.",
                live_monitor_auto_run_in_minute: "Please enter a value",
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

        },
    });

    var select2Opts = { width: "100%" };

    if(typeof t.config.edit_role != "undefined" && typeof t.config.edit_role == "object" && Object.keys(t.config.edit_role).length > 0) {
        // t.frmEl.ni_not_detected_value.empty().append(new Option(config.translations.select_role, "null"));
        var existingIds = {};
        $.each(t.config.edit_role, function(i, role) {
            existingIds[role.id] = true;
        });
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            $.each(t.config.roles, function(i, v) {
                if (!existingIds[v.id]) {
                    t.frmEl.ni_not_detected_value.append(new Option(v.name, v.id, false, false));
                } else {
                    t.frmEl.ni_not_detected_value.append(new Option(v.name, v.id, true, true));
                }
            });
        }
    } else {
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            // t.frmEl.ni_not_detected_value.empty().append(new Option(config.translations.select_role, "null"));
            $.each(t.config.roles, function(i, v) {
                t.frmEl.ni_not_detected_value.append(new Option(v.name, v.id, true, false)).trigger("change");
            })
        }
    }

    var select2Opts = { width: "100%" };
    if(typeof t.config.editRoleReminder != "undefined" && typeof t.config.editRoleReminder == "object" && Object.keys(t.config.editRoleReminder).length > 0) {
        // t.frmEl.ni_not_detected_value.empty().append(new Option(config.translations.select_role, "null"));
        var existingIds = {};
        $.each(t.config.editRoleReminder, function(i, role) {
            existingIds[role.id] = true;
        });
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            $.each(t.config.roles, function(i, v) {
                if (!existingIds[v.id]) {
                    t.frmEl.send_reminder_users_value.append(new Option(v.name, v.id, false, false));
                } else {
                    t.frmEl.send_reminder_users_value.append(new Option(v.name, v.id, true, true));
                }
            });
        }
    } else {
        if(typeof t.config.roles != "undefined" && typeof t.config.roles == "object" && Object.keys(t.config.roles).length > 0) {
            // t.frmEl.ni_not_detected_value.empty().append(new Option(config.translations.select_role, "null"));
            $.each(t.config.roles, function(i, v) {
                t.frmEl.send_reminder_users_value.append(new Option(v.name, v.id, true, false)).trigger("change");
            })
        }
    }

    t.frmEl.ni_not_detected_email.select2({
        width: "100%",
        placeholder: config.translations.enter_email_comma,
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getUserNIByQuery;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results.map(function(item) {
                        return {
                            id: item.text,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more,
                    }
                };
            },
            cache: true
        },
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        insertTag: function (data, tag) {
            data.push(tag);
        }
    });

    t.frmEl.send_reminder_users_email.select2({
        width: "100%",
        placeholder: config.translations.enter_email_comma,
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getUserNIByQuery;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results.map(function(item) {
                        return {
                            id: item.text,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more,
                    }
                };
            },
            cache: true
        },
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        insertTag: function (data, tag) {
            data.push(tag);
        }
    });
    t.frmEl.ni_not_detected_value.select2({width: '100%', placeholder: config.translations.select_role, allowclear : true});
    t.frmEl.send_reminder_users_value.select2({width: '100%', placeholder: config.translations.select_role, allowclear : true});
    t.frmEl.ni_not_detected_type.select2({width: '100%', allowclear : true});
    t.frmEl.live_monitor_auto_refresh.select2({width: '100%', allowclear : true});
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};

var EmailSettings = function (config) {
    let defaultCompany = localStorage.getItem("Default_Company");
    let companyId = config.company_defulte ? config.company_defulte : null;
    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#settingsmdl4'); 

    t.frm = t.mdl.find('#SettingForm4');

    t.frmEl = {};
    t.frmEl.mail_service_enabled = t.frm.find('#mail_service_enabled');
    t.frmEl.mail_driver = t.frm.find('#mail_driver');
    t.frmEl.mail_host = t.frm.find('#mail_host');
    t.frmEl.mail_port = t.frm.find('#mail_port');
    t.frmEl.mail_username = t.frm.find('#mail_username');
    t.frmEl.mail_password = t.frm.find('#mail_password');
    t.frmEl.mail_encryption = t.frm.find('#mail_encryption');
    t.frmEl.mail_from_address = t.frm.find('#mail_from_address');
    t.frmEl.mail_from_name = t.frm.find('#mail_from_name');

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
        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }     
    };  
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit4');

    t.httpCall = false;
    t.httpPostPath = t.config.url.smtp_config; // URL for saving

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append("company_id", companyId);
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
                    sweetAlert('center', 'success', data);
                    window.location.reload();
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
            // hide loader if any
        });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            mail_driver: {
                required: true
            },
            mail_host: {
                required: true
            },
            mail_port: {
                required: true,
                digits: true
            },
            mail_from_address: {
                required: true,
                email: true
            },
            mail_from_name: {
                required: true
            }
        },
        messages: {
            mail_driver: "Please select a mail driver.",
            mail_host: "Please enter the mail host.",
            mail_port: "Please enter a valid port number.",
            mail_from_address: "Please enter a valid email address.",
            mail_from_name: "Please enter the from name."
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
    });

    // Initialize select2 for selects
    t.frmEl.mail_encryption.select2({width: '100%', allowclear: true});

    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};