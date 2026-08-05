var CustomLTSCTForm = function(config) {
    var t = this;
    t.config = config;
    var count = 0;
    var cls= '';
    var manager_name = '';
    $('#mdl-servicerequest').on('shown.bs.modal', function() {
        $(this).find('#build-wrap').find('#issueDate').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true
        });

        var select2Opts = { width: "100%" };
        const buildWrap = $('#build-wrap').find("#custom_request");   
        $(this).find('#build-wrap').find("#location").select2($.extend({}, select2Opts, {
            dropdownParent:  $(this).find('#build-wrap').find("#location").parent(),
            ajax: {
                url: t.config.url.getLocationByAjax,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        consumable: 1,
                        department_id: $('#ticket-mdl-frm').find('#department_id').val(),
                        page: p.page || 1
                    };
                },
                delay: 300
            },
            allowClear:true,
            placeholder: "Select Location",
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                if(data.id != null) {
                    return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                }
            },
        }));

        $(this).find('#build-wrap').find("#location").on("change", function () {
            const selectedValue = $(this).text();
            $('input[name="location"]').val(selectedValue);
            $('input[name="location_id"]').val($(this).val());
        });

        $(this).find('#build-wrap').find("#emp_no").select2($.extend({}, select2Opts, {
            dropdownParent: $(this).find('#build-wrap').find("#emp_no").parent(),
            ajax: {
                url: t.config.url.getUserByQueryForCustomForm,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 300,
                processResults: function(data) {
                    return {
                        results: data.results.map(function(user) {
                            return {
                                id: user.id,
                                text: user.employee_num
                                    ? user.employee_num + " - " + user.first_name + " " + user.last_name
                                    : user.first_name + " " + user.last_name + " (" + user.username + ")"
                            };
                        })
                    };
                }
            },
            allowClear: true,
            placeholder: config.translations.enter_first_few_letter,
            templateResult: function(data) {
                if (!data.id) return $("<div>No data</div>");
                return $("<div>" + data.text + "</div>"); // Display text properly
            },
            templateSelection: function(data) {
                return data.text || config.translations.enter_first_few_letter; // Show selected text properly
            }
        }));


        $(document).on('select2:select', '#build-wrap #emp_no', function (e) {
            const selectedId = $(this).val(); // Get selected employee ID
            if (!selectedId) {
                sweetAlert('center', 'error', "Id not Found");
                return;
            }

            $.get(t.config.url.getUser + `?empid=` + selectedId).done(function (data) {
                if (typeof data === 'object') {
                    if (data.status === 'success') {
                        t.loadFormUser(data.user); // Load user details in form
                    } else {
                        t.loadFormUser(null);
                        sweetAlert('center', 'error', "User not found!");
                    }
                }
            })
            .fail(function () {
                sweetAlert('center', 'error', "Something went wrong!");
            })
            .always(function () {
                t.httpCall = true;
            });
        });
        
        $(document).on('click', '.next', function () {
            // Check if "Self" radio button is selected
            if ($('input[name="employee_type"][value="Self"]').is(':checked')) {
                $('#emp_no').prop('disabled', true);
                $('#emp_name').prop('readonly', true).val('');
                $('#email_id').prop('readonly', true).val('');
                $('#location').prop('disabled', true).empty();
                $('#department').prop('disabled', true).val('');
                $('#cost_center').prop('readonly', false).val('');
                $('#contact').prop('readonly', true).val('');
                const authId = t.config.auth.id;
                if (authId) {
                    var http = $.get(t.config.url.getUser + "?empid=" + authId);
        
                    http.done(function (data) {
                        if (typeof data === "object") {
                            if (data.status === "success") {
                                t.loadFormUser(data.user, $('#build-wrap'));
                            } else {
                                t.loadFormUser(null, $('#build-wrap'));
                            }
                        }
                    });
        
                    http.fail(function () {
                        var data = {
                            'msg': "Something went wrong",
                        };
                        sweetAlert('center', 'error', data.msg);
                    });
        
                    http.always(function () {
                        t.httpCall = true;
                    });
                } else {
                    // sweetAlert('center', 'warning', 'No Auth ID found.');
                }
            } else {
                // sweetAlert('center', 'warning', 'Please select option to proceed.');
            }
            // if ($('input[name="employee_type"][value="Non LTSCT Employee"]').is(':checked')) {
            //     $('#cost_center').prop('readonly', false).val('');
            // }
        });
                
        t.loadFormUser = function(user) {
            buildWrap.find('#manager_data').addClass('hide');
            if (user) {
                buildWrap.find('#emp_name').prop('readonly', true).val(user.data.full_name || '');
                if (typeof user.dropdown === "object" && typeof user.dropdown.location === "object") {
                    buildWrap.find('#location').select2('trigger', 'select', {
                        data: {
                            text: user.dropdown.location.text,
                            id: user.dropdown.location.id,
                            selected: true
                        }
                    });
                }
                // console.log("creator: " + $creator.val());
                // console.log("creator Np: " + $cform_creator_id.val());
                console.log(t.config.isTechnician);
                const $creator = $('#ticket-mdl-frm #creator_id');
                if($('input[name="employee_type"][value="LTSCT Employee"]').is(':checked')) {
                    if (t.config.isTechnician == 1 && $creator.val() != null) {
                        if ($("#mdl-servicerequest #requested_form #emp_no").val() != 0) {
                            const newOption = new Option(user.data.full_name, user.data.id, true, true);
                            $creator.append(newOption);
                            $creator.val(user.data.id).trigger('change');
                        } else {
                            $creator.val(user.data.id);
                            $creator.trigger('change');
                        }
                    } else {
                        const $creatByNp = $('#ticket-by-np-mdl #ticket-mdl-frm');

                        if (!$creatByNp.find("#creator_id").val()) {
                            console.log("not found: " + $creatByNp.find("#creator_id").val());
                            $('<input>').attr({
                                'type': 'hidden',
                                'id': 'creator_id',
                                'name': 'creator_id',
                                'value': user.data.id
                            }).appendTo($creatByNp);
                        } else {
                            if (!$creatByNp.find("#creator_id").val()) {
                            }
                            console.log("found: " + $creatByNp.find("#creator_id").val());
                            $creatByNp.find("#creator_id").val(user.data.id);
                        }
                        // $cform_creator_id.val(user.data.id);
                    }
                } else {
                    const $creatByNp = $('#ticket-by-np-mdl #ticket-mdl-frm');
                    if(t.config.isTechnician != 1 && $creatByNp.find("#creator_id").val()) {
                        $creatByNp.find("#creator_id").remove();
                    }
                    $creator.val(t.config.auth.id).trigger('change');
                }
                /*if (typeof user.dropdown === "object" && typeof user.dropdown.location === "object" && $("#mdl-servicerequest").find("#custom_form_id").val() == '') {
                    buildWrap.find('#b_location').select2('trigger', 'select', {
                        data: {
                            text: user.dropdown.location.text,
                            id: user.dropdown.location.id,
                            selected: true
                        }
                    });
                }*/
                if (typeof user.dropdown === "object" && typeof user.dropdown.place === "object" && $("#mdl-servicerequest").find("#custom_form_id").val() == '') {
                    buildWrap.find('#internal_location').select2('trigger', 'select', {
                        data: {
                            text: user.dropdown.place.text,
                            id: user.dropdown.place.id,
                            selected: true
                        }
                    });
                }
                if (typeof user.dropdown === "object" && typeof user.dropdown.sevice_ticket_department === "object") {
                    // buildWrap.find('#department').empty();
                    buildWrap.find('#department_name').val('');
                    buildWrap.find('#department_id').val('');
                    if(user.dropdown.sevice_ticket_department != null) {
                        $.each(user.dropdown.sevice_ticket_department, function (i, v) {
                            var option = new Option(v.text, v.id, true, true);
                            buildWrap.find('#department').append(option);
                            buildWrap.find('#department_name').val(v.text);
                            buildWrap.find('#department_id').val(v.id);
                        });
                    } else {
                        var option = new Option("Select Department", null, true, true);
                        buildWrap.find('#department').append(option);
                    }
                }
                const selectedValue = $('input[name="employee_type"]:checked').val();
                if (selectedValue === 'Self') {
                    manager_name = '';
                    buildWrap.find('#emp_no').empty();
                    let displayName = t.config.user.employee_num ? t.config.user.employee_num : (t.config.user.first_name + " " + t.config.user.last_name + " (" + t.config.user.username + ")");
                    let userOption = new Option(displayName, t.config.user.id, true, true);
                    buildWrap.find("#emp_no").append(userOption).trigger('change');
                    buildWrap.find("#emp_number").val(displayName);
                } else {
                    manager_name = '';
                    let displayName = user.data.employee_num ? user.data.employee_num : (user.data.first_name + " " + user.data.last_name + " (" + user.data.username + ")");
                    let userOption = new Option(displayName, user.data.id, true, true);
                    buildWrap.find("#emp_no").append(userOption).trigger('change');
                    buildWrap.find("#emp_number").val(displayName);
                }
                buildWrap.find('#location').prop('disabled', true);
                buildWrap.find('#department').prop('disabled', true);
                buildWrap.find('#cost_center').prop('readonly', true).val(user.data.baseCostCode || '');
                buildWrap.find('#contact').prop('readonly', true).val(user.data.phone || '');
                buildWrap.find('#email_id').prop('readonly', true).val(user.data.email || '');
                if(typeof user.manager === "object" && user.manager != null ){
                    buildWrap.find('#manager_data').removeClass('hide');
                    buildWrap.find('#manager_name').prop('readonly', true).val(user.manager.first_name +' '+ user.manager.last_name || '');
                    buildWrap.find('#manager_email').prop('readonly', true).val(user.manager.email || '');
                    buildWrap.find('#manager_ps_no').prop('readonly', true).val(user.manager.employee_num || '');
                } else {
                    buildWrap.find('#manager_data').addClass('hide');
                    manager_name = '';
                    buildWrap.find('#manager_name').prop('readonly', true).val('');
                    buildWrap.find('#manager_email').prop('readonly', true).val('');
                    buildWrap.find('#manager_ps_no').prop('readonly', true).val('');
                }
            } else {
                buildWrap.find('#emp_no').prop('disabled', false).val('');
                buildWrap.find('#emp_name').prop('readonly', true).val('');
                buildWrap.find('#location').prop('disabled', true).empty();
                buildWrap.find('#department').prop('disabled', true).empty();
                buildWrap.find('#cost_center').prop('readonly', false).val('');
                buildWrap.find('#contact').prop('readonly', true).val('');
                buildWrap.find('#email_id').prop('readonly', true).val('');
            }
        };
        
        $(this).find('#build-wrap').find("#b_location").select2($.extend({}, select2Opts, {
            dropdownParent: $(this).find('#build-wrap').find("#b_location").parent(),
            ajax: {
                url: t.config.url.getLocationByAjax,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        consumable: 1,
                        department_id: $('#ticket-mdl-frm').find('#department_id').val(),
                        page: p.page || 1
                    };
                },
                delay: 300
            },
            allowClear:true,
            // minimumInputLength: 1,
            placeholder: "Select Location",
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                if(data.id != null) {
                    return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                }
            },
        }));

        $('#consumableType').select2({
            dropdownParent: $(this).find('#build-wrap').find("#consumableType").parent(),
            width: '100%',
            placeholder: 'Select Consumable Type',
            allowClear: true,
            ajax: {
                url: t.config.url.getcustom_option + '/12',
                dataType: 'json',
                delay: 250,
                processResults: function (response) {
                    if (response.status === 'success') {
                        return {
                            results: response.options.map(option => ({
                                id: option,
                                text: option
                            }))
                        };
                    }
                }
            }
        });


        $(this).find('#build-wrap').find("#b_location").on('change', function () {
            const selectedLocation = $(this).val();
            $('#build-wrap').find("#internal_location").val('').trigger('change');
            if (selectedLocation) {
                 $('#build-wrap').find("#internal_location").select2($.extend({}, select2Opts, {
                    dropdownParent: $('#build-wrap').find("#internal_location").parent(),
                    ajax: {
                        url: t.config.url.getInternalPlaceByAjax + '/' + selectedLocation,
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
                    placeholder: "Select Internal Location",
                    templateSelection: function (data, container) {
                        $(container).attr('title', data.text);
                        if(data.id != null) {
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    },
                }));
            }
        });

        $("#internal_location").select2({
            placeholder: "Select Internal Location",
            allowClear: true,
            width: "100%",
        });
        $(this).find('#build-wrap').find("#internal_location").on("change", function () {
            const selectedValue = $(this).find(":selected").text();
            $('input[name="internal_location"]').val(selectedValue);
        });
        $(this).find('#build-wrap').find("#b_location").on("change", function () {
            const selectedValue = $(this).find(":selected").text();
            $('input[name="delivery_location"]').val(selectedValue);
        });

        $(this).find('#build-wrap').find("#consumableType").on("change", function () {
            const selectedValue = $(this).find(":selected").text();
            $('input[name="consumable_type"]').val(selectedValue);
        });

        $(this).find('#build-wrap').find(".category_id").select2($.extend({}, select2Opts, {
            dropdownParent: $(this).find('#build-wrap').find(".category_id").parent(),
            ajax: {
                url: function() {
                    var type_val = $("#ticket-mdl").find("#ticket-mdl-frm").find("#department_id").val()
                    ? $("#ticket-mdl").find("#ticket-mdl-frm").find("#department_id").val()
                    : $("#ticket-by-np-mdl").find("#ticket-mdl-frm").find("#department_id").val();
                    const location_id = $('#build-wrap').find("#b_location").val();
                    const consumable_type = $('#build-wrap').find("#consumableType").val();
                    if (type_val > 0 && !isNaN(type_val)) {
                        return t.config.url.getCategoryByAjax + "?department_id=" + type_val + "&location_id=" + location_id + "&category_type=" +consumable_type;
                    }
                },
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        type: 'consumable'
                    };
                },
                delay: 300
            },
            allowClear:true,
            // minimumInputLength: 1,
            placeholder: "Select Category",
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                if(data.id != null) {
                    return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                }
            },
            
        }));

        userDropdownFormat = function (s) {
            if (!s.id) {
                return $("<div>" + s.text + "</div>"); // Handling placeholders
            }
        
            var item = s.text;
            var imageUrl = s.image ? s.image : "default-user.png"; // Fallback image
        
            var a = '';
            a += "<div class='row align-items-center'>";
            a += "<div class='col-sm-1'>";
            a += "<img class='img-fluid rounded-circle' style='width:30px; height:30px;' src='" + imageUrl + "'/>";
            a += "</div>";
            a += "<div class='col-sm-10'>";
            a += "<span style='padding-left: 15px;'>" + item + "</span>";
            a += "</div>";
            a += "</div>";
        
            return $("<div>" + a + "</div>");
        };

        $(this).find('#build-wrap').find(".item").select2($.extend({}, select2Opts, {
            dropdownParent: $(this).find('#build-wrap').find(".item").parent(),
            ajax: {
                url: function() {
                    let prevSelectItem = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id"); 
                    var type_val = parseInt($.trim(buildWrap.find("#" + prevSelectItem).val()));
                    const location_id = $('#build-wrap').find("#b_location").val();
                    const consumable_type = $('#build-wrap').find("#consumableType").val();
                    if (type_val > 0 && !isNaN(type_val)) {
                        return t.config.url.getDataForCustomFormBaseOnType + "/?cat_id=" + type_val + "&location_id=" + location_id + "&category_type=" + consumable_type;
                    }
                },
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                    };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: "Select Item",
            templateResult: function(data) {
                if (!data) return $("<div>No data</div>");
                return userDropdownFormat(data); 
            }
        }));

        $(this).find('#build-wrap').find("#custom_user").select2($.extend({}, select2Opts, {
            dropdownParent:  $(this).find('#build-wrap').find("#custom_user").parent(),
            ajax: {
                url: t.config.url.getUserByAjax,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: "Select User",
        }));

        $(this).find('#build-wrap').find('.category_department').select2($.extend({}, select2Opts, {
            dropdownParent:  $(this).find('#build-wrap').find(".category_department").parent(),
            ajax: {
                url: t.config.url.departments_with_company,
                dataType: "json",
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 200
            },
            allowClear:true,
            // minimumInputLength: 1,
            placeholder: "Select Department",
        }));

        $(this).find('#build-wrap').find('.category_id').find(".category_id").select2({
            width: "100%",
            placeholder: "Select Category",
            allowClear: true,
            dropdownParent: $(this).find('.category_id').parent(),
        });

        $(this).find('.category_id').on('select2:select', function (e) {
            var selectedData = e.params.data;
            var selectedText = selectedData.text;
            let currentvSelect = $(this).closest(".input-group").parent().find(".selected-category-name").attr("id"); 
            $(this).closest('#build-wrap').find('#'+ currentvSelect).val(selectedText);
        });

        $(this).find('.item').on('select2:select', function (e) {
            var selectedData = e.params.data;
            var selectedText = selectedData.text;
            var selectedImg = selectedData.image;
            let currentvSelect = $(this).closest(".input-group").parent().find(".selected-item-name").attr("id");
            $(this).closest('#build-wrap').find('#' + currentvSelect).val(selectedText);
            let currentvSelectImg = $(this).closest(".input-group").parent().find(".selected-item-img").attr("id");
            $(this).closest('#build-wrap').find('#' + currentvSelectImg).val(selectedImg);
        });

        $(this).find('.quantity').on('blur',function(){
            let prevSelectCat = $(this).closest(".input-group").parent().prev().prev().find(".category_id").attr("id");
            let prevSelectItem = $(this).closest(".input-group").parent().prev().find(".item").attr("id");
            t.checkAvailableQnt(parseInt($.trim(buildWrap.find("#" + prevSelectItem).val())), parseInt($.trim(buildWrap.find("#" + prevSelectCat).val())), prevSelectItem.split("_")[1], $(this).val(),'quantity');
            const value = parseInt(this.value);
            this.value = isNaN(value) ? '' : value;
        });

        $(this).find('.item').on('change',function(){
            let prevSelectCat = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id");
            let prevSelectItem = $(this).closest(".input-group").parent().find(".item").attr("id");
            t.checkAvailableQnt(parseInt($.trim(buildWrap.find("#" + prevSelectItem).val())), parseInt($.trim(buildWrap.find("#" + prevSelectCat).val())), prevSelectItem.split("_")[1], $(this).val(),'item');
        });

        window.loadFormData = function (data) {
            $("input[name='employee_type'][value='" + data.field_values.employee_type + "']").prop("checked", true);
            if (data.field_values.employee_type === 'Non LTSCT Employee') {
                $('.psno-container').hide();
                buildWrap.find('#emp_name').prop('readonly', false).val(data.field_values.emp_name || '');
                buildWrap.find('#contact').prop('readonly', false).val(data.field_values.contact || '');
                buildWrap.find('#email_id').prop('readonly', false).val(data.field_values.email_id || '');
                buildWrap.find('#cost_center').prop('readonly', false).val(data.field_values.cost_center || '');
            }else{
                buildWrap.find('#emp_name').prop('readonly', true).val(data.field_values.emp_name || '');
                buildWrap.find('#contact').prop('readonly', true).val(data.field_values.contact || '');
                buildWrap.find('#email_id').prop('readonly', true).val(data.field_values.email_id || '');
                buildWrap.find('#cost_center').prop('readonly', true).val(data.field_values.cost_center || '');
            }

            if (typeof data.field_values === "object" && typeof data.field_values.emp_no !== "undefined" && data.field_values.emp_no != "null") {
                var newOption = new Option(data.field_values.emp_no,data.field_values.user_id, true, true);
                buildWrap.find('#emp_no').append(newOption).trigger('change');
            }

            buildWrap.find('#department').prop('disabled', true).empty();
            if (typeof data.field_values === "object" && typeof data.field_values.department !== "undefined" && data.field_values.department !== "null") {
                var option = new Option(data.field_values.department, data.field_values.department_id, true, true);
                buildWrap.find('#department').append(option);
                buildWrap.find('#department_name').val(data.field_values.department || '');
                buildWrap.find('#department_id').val(data.field_values.department_id || '');
            } else {
                var option = new Option("Select Department", null, true, true);
                buildWrap.find('#department').append(option);
            }

            buildWrap.find('#emp_number').prop('readonly', true).val(data.field_values.emp_no || '');
            buildWrap.find('#wbs_note').prop('readonly', false).val(data.field_values.wbs_note || '');
            buildWrap.find('#remark_item').prop('readonly', false).val(data.field_values.remark_item || '');
            buildWrap.find('#location').prop('disabled', true).empty();
            if (typeof data.field_values === "object" && typeof data.field_values.location_id !== "undefined" && data.field_values.location_id !== "null") {
                buildWrap.find('#location').select2('trigger', 'select', {
                    data: {
                        text: data.field_values.location,
                        id: data.field_values.location_id,
                        selected: true
                    }
                });
            }
            if (typeof data.field_values === "object" && typeof data.field_values.b_location !== "undefined" && data.field_values.b_location !== "null") {
                buildWrap.find('#b_location').select2('trigger', 'select', {
                    data: {
                        text: data.field_values.delivery_location,
                        id: data.field_values.b_location,
                        selected: true
                    }
                });
            }

            if (typeof data.field_values === "object" && data.field_values.consumableType && data.field_values.consumableType !== "null") {
                var select = buildWrap.find('#consumableType');
                var value = data.field_values.consumableType;
                var option = new Option(value, value, true, true);
                select.append(option).trigger('change');
                select.trigger({
                    type: 'select2:select',
                    params: {
                        data: { id: value, text: value }
                    }
                });
            }

            if (typeof data.field_values === "object" && typeof data.field_values.internal_location_id !== "undefined" && data.field_values.internal_location_id !== "null") {
                buildWrap.find('#internal_location').select2('trigger', 'select', {
                    data: {
                        text: data.field_values.internal_location,
                        id: data.field_values.internal_location_id,
                        selected: true
                    }
                });
            }

            if(typeof data.field_values.manager_name == "string") {
                manager_name = data.field_values.manager_name;
                buildWrap.find('#manager_data').removeClass('hide');
                buildWrap.find('#manager_name').prop('readonly', true).val(data.field_values.manager_name || '');
                buildWrap.find('#manager_email').prop('readonly', true).val(data.field_values.manager_email || '');
                buildWrap.find('#manager_ps_no').prop('readonly', true).val(data.field_values.manager_ps_no || '');
            } else {
                manager_name = '';
                buildWrap.find('#manager_data').addClass('hide');
                buildWrap.find('#manager_name').prop('readonly', true).val('');
                buildWrap.find('#manager_email').prop('readonly', true).val('');
                buildWrap.find('#manager_ps_no').prop('readonly', true).val('');
            }

            $("#repeater-container").empty();
            for (let i = 0; i < data.field_values.category.length; i++) {
                let newRow = `
                <tr class="entry">
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                            <select class="form-control category_id select2" id="category_id_${i}" name="category_id[]" style="width: 100%;" required>
                                <option value="${data.field_values.category_id[i]}" selected>${data.field_values.category[i]}</option>
                            </select>
                            <input type="hidden" class="selected-category-name" id="category_${i}" name="category[]" value="${data.field_values.category[i]}" required>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                            <select class="form-control item select2" id="item_${i}" name="item_id[]" style="width: 100%;" required>
                                <option value="${data.field_values.item_id[i] || ''}" selected>${data.field_values.item[i] || ''}</option>
                            </select>
                            <input type="hidden" class="selected-item-img" id="item_img_${i}" name="item_img[]" value="${data.field_values.item_img[i] || ''}">
                            <input type="hidden" class="selected-item-name" id="item_name_${i}" name="item[]" value="${data.field_values.item[i] || ''}" required>
                        </div>
                        <span id="available_quantity_${i}" class="available-quantity hide text-center" style="color:white;"></span>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon fa fa-shield"></i></span>
                            <input type="number" class="form-control quantity" name="quantity[]" min="1" id="quantity_${i}" placeholder="Qty" value="${data.field_values.quantity[i] || ''}" style="width: 100%;">
                        </div>
                        <div class="one_line_dis hide" id="one_line_dis_${i}">
                            <i class="fa fa-exclamation-triangle warning-icon"></i> 
                            <span class="warning-text" style="color:#ffc107;">You entered more than available item</span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-check-circle-o"></i></span>
                            <input type="text" class="form-control remarks" name="remarks[]" id="remarks_${i}" placeholder="Remarks" value="${data.field_values.remarks[i] || ''}">
                            <input type="hidden" class="selected-type-name" id="item_type_${i}" name="item_type[]" value="${data.field_values.item_type[i] || ''}">
                        </div>
                    </td>
                    <td class="btn-addnew">
                        ${i == 0 ? `
                            <button type="button" class="btn btn-theme-red addNewRow" style="min-width: 0px;">
                                <i class="glyphicon glyphicon-plus"></i>
                            </button>
                        ` : `
                            <button type="button" class="btn btn-theme-red remove-row" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Remove">
                                <i class="glyphicon glyphicon-minus"></i> 
                            </button>
                        `}
                    </td>
                </tr>`;

                // Append the new row to the table body
                $("#repeater-container").append(newRow);
                let appendedRow = $("#repeater-container").find(".entry:last");
                initializeSelect2ForRow(appendedRow);
            }

            $(".item").each(function () {
                let prevSelectCat = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id");
                let prevSelectItem = $(this).closest(".input-group").parent().find(".item").attr("id");
                let selectedValue = $(this).val();
                if (selectedValue) {
                    t.checkAvailableQnt(
                        parseInt($.trim(buildWrap.find("#" + prevSelectItem).val())),
                        parseInt($.trim(buildWrap.find("#" + prevSelectCat).val())),
                        prevSelectItem.split("_")[1],
                        selectedValue,
                        'item'
                    );
                }
            });

            $(".quantity").each(function () {
                let prevSelectCat = $(this).closest(".input-group").parent().prev().prev().find(".category_id").attr("id");
                let prevSelectItem = $(this).closest(".input-group").parent().prev().find(".item").attr("id");
                let selectedValue = $(this).val();
                if (selectedValue) {
                    t.checkAvailableQnt(
                        parseInt($.trim(buildWrap.find("#" + prevSelectItem).val())),
                        parseInt($.trim(buildWrap.find("#" + prevSelectCat).val())),
                        prevSelectItem.split("_")[1],
                        selectedValue,
                        'quantity'
                    );
                }
            });
        };
        $(this).find('#build-wrap').find('.category_id').on('change', function(){
            var cat_id = $(this).val();
            var id = $(this).attr('id');
            var item_count = id.split("_")[2];
            t.resetData(cat_id, item_count);
        });
    });

    t.resetData = function(cat, item_count){
        if(item_count != ''){
            $('#item_'+ item_count).empty();
            $('#quantity_'+ item_count).val('');
            $('#available_quantity_'+ item_count).addClass('hide');
            $('#remarks_'+ item_count).val('');
        }else{
            for(var i = 0; i < count ; i++){
                $('#category_id_'+ i).empty();
                $('#item_'+ i).empty();
                $('#quantity_'+ i).val('');
                $('#available_quantity_'+ i).addClass('hide');
                $('#remarks'+ i).val('');
                $('#remarks_'+ i).val('');
            }
        }
    }

    t.checkAvailableQnt = function(item, categoryVal, nexSelectId, val, itemType) {
        if (item > 0) {
            $.ajax({
                url: t.config.url.fetchAvailable,
                type: "POST",
                data: { item: item, category: categoryVal },
                success: function(data) {
                    let errorDiv = $('#one_line_dis_' + nexSelectId);
                    let remarksInput = $('#remarks' + nexSelectId);
                    let quantityDisplay = $('#available_quantity_' + nexSelectId);

                    if(data.data !== undefined) {
                        quantityDisplay.text("Available Quantity: " + data.data).removeClass('hide');
                    } else {
                        quantityDisplay.text("Available Quantity: 0").removeClass('hide');
                    }
                    $('#item_type_'+ nexSelectId).val(data.type);
                    console.log(itemType);
                     $('#quantity_' + nexSelectId).attr('data-valid', 'false');
                    if (data.data < val && itemType == 'quantity') {
                        errorDiv.removeClass('hide');
                        Swal.fire({
                            title: "More Item!",
                            text: "You can not entered more than available item.",
                            icon: "warning",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $('#quantity_'+ nexSelectId).val('');
                            errorDiv.addClass('hide');
                        });
                        // remarksInput.prop('required', true);
                    } else {
                        $('#quantity_' + nexSelectId).attr('data-valid', 'true'); // mark valid
                        errorDiv.addClass('hide');
                        remarksInput.prop('required', false);
                        remarksInput.removeClass('error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
    };

    function createNewRow(isFirst = false) {
        let cls = $("#repeater-container tr").length;
        count++
        return `
        <tr class="entry">
            <td>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                    <select class="form-control category_id" id="category_id_${cls}" name="category_id[]" style="width: 100%;" required>
                        <option value="">Select Category</option>
                    </select>
                    <input type="hidden" class="selected-category-name" id="category_${cls}" name="category[]">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                    <select class="form-control item" id="item_${cls}" name="item_id[]" style="width: 100%;" required>
                        <option value="">Select Item</option>
                    </select>
                    <input type="hidden" class="selected-item-img" id="item_img_${cls}" name="item_img[]">
                    <input type="hidden" class="selected-item-name" id="item_name_${cls}" name="item[]">
                </div>
                <span id="available_quantity_${cls}" class="available-quantity hide text-center" style="color:white;"></span>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon fa fa-shield"></i></span>
                    <input type="number" class="form-control quantity" name="quantity[]" min="1" id="quantity_${cls}" placeholder="Qty" style="width: 100%;" required>
                </div>
               <div class="one_line_dis hide" id="one_line_dis_${cls}">
                    <i class="fa fa-exclamation-triangle warning-icon"></i> 
                    <span class="warning-text" style="color: #ffc107;">You can't enter more than available item</span>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-check-circle-o"></i></span>
                    <input type="text" class="form-control remarks" name="remarks[]" id="remarks${cls}" placeholder="Remarks">
                    <input type="hidden" class="selected-type-name" id="item_type_${cls}" name="item_type[]">
                </div>
            </td>
            <td class="btn-addnew">
                ${isFirst ? `
                    <button type="button" class="btn btn-theme-red addNewRow" style="min-width: 0px;">
                        <i class="glyphicon glyphicon-plus"></i> Add
                    </button>
                ` : `
                    <button type="button" class="btn btn-theme-red remove-row" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Remove">
                        <i class="glyphicon glyphicon-minus"></i> 
                    </button>
                `}
            </td>
        </tr>`;
    }

    $(document).on('change','#b_location', function(){
        t.resetData('','');
    });

    $(document).on('change','#consumableType', function(){
        t.resetData('','');
    });

    function initializeSelect2ForRow(row) {
        row.find(".category_id").select2({
            dropdownParent: row.find(".category_id").parent(),
            ajax: {
                url: function() {
                    var type_val = $("#ticket-mdl").find("#ticket-mdl-frm").find("#department_id").val()
                    ? $("#ticket-mdl").find("#ticket-mdl-frm").find("#department_id").val()
                    : $("#ticket-by-np-mdl").find("#ticket-mdl-frm").find("#department_id").val();

                    const location_id = $('#build-wrap').find("#b_location").val();
                    const consumable_type = $('#build-wrap').find("#consumableType").val();
                    if (type_val > 0 && !isNaN(type_val)) {
                        return t.config.url.getCategoryByAjax + "?department_id=" + type_val + "&location_id=" + location_id + "&category_type=" +consumable_type;
                    }
                },
                // url: t.config.url.getCategoryByAjax,
                dataType: "json",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        type: 'consumable'
                    };
                },
                delay: 300,
            },
            allowClear: true,
            placeholder: "Select Category",
            templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50 ? data.text.substring(0, 50) + "..." : data.text;
            },
            id: function (data) {
                return data.id;
            },
        }).on("change", function () {
            let currentvSelect = $(this).closest(".input-group").parent().find(".selected-category-name").attr("id"); 
            const categoryId = row.find(".category_id").val();
            const categoryText = row.find(".category_id option:selected").text();
            row.find('#'+currentvSelect).val(categoryText);
            row.data("category_id", categoryId);
            row.data("category_name", categoryText);
        });

        row.find(".item").select2({
            dropdownParent: row.find(".item").parent(),
            ajax: {
                url: function () {
                    let prevSelectItem = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id");
                    const categoryVal = row.find("#" + prevSelectItem).val();
                    const location_id = $('#build-wrap').find("#b_location").val();
                    const consumable_type = $('#build-wrap').find("#consumableType").val();
                    if (categoryVal) {
                        return `${t.config.url.getDataForCustomFormBaseOnType}/?cat_id=${categoryVal}&location_id=${location_id}&category_type=${consumable_type}`;
                    }
                    return null;
                },
                dataType: "json",
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                    };
                },
                processResults: function (data) {
                    let selectedItems = $(".item").map(function () { return $(this).val(); }).get(); // Get selected values
                    let filteredResults = data.results.filter(item => !selectedItems.includes(item.id)); // Remove selected items

                    return {
                        results: filteredResults.map(item => ({
                            id: item.id,
                            text: item.text,
                            image: item.image
                        }))
                    };
                },
                delay: 300,
            },
            allowClear: true,
            placeholder: "Select Item",
            templateResult: function (data) {
                if (!data.id) return data.text;
                let img = data.image ? `<img src="${data.image}" style="width:25px;height:25px;margin-right:5px;">` : "";
                return $(`<span>${img}${data.text}</span>`);
            }
        }).on("change", function () {
            let selectedOption = $(this).select2("data")[0];
            if (selectedOption) {
                let itemText = selectedOption.text;
                let itemImage = selectedOption.image || "";
                let currentvSelect = $(this).closest(".input-group").parent().find(".selected-item-name").attr("id");
                let currentvSelectImage = $(this).closest(".input-group").parent().find(".selected-item-img").attr("id");

                row.find("#" + currentvSelect).val(itemText);
                row.find("#" + currentvSelectImage).val(itemImage);

                row.data("item_name", itemText);
                row.data("item_image", itemImage);
            }

            let selectedItems = $(".item").map(function () { return $(this).val(); }).get(); // Get all selected items
            const selectedValue = $(this).val();
            if (selectedValue != "" && selectedItems.filter(item => item === selectedValue).length > 1) {
                Swal.fire({
                    title: "Duplicate Item!",
                    text: "This item is already selected. Please choose a different item.",
                    icon: "warning",
                    confirmButtonText: "OK"
                }).then(() => {
                    $(this).val("").trigger("change");
                    let currentvSelect = $(this).attr("id");
                    $('#available_quantity_' + currentvSelect.split("_")[1]).addClass('hide');
                    $('#one_line_dis_' + currentvSelect.split("_")[1]).addClass('hide');
                });
            }
        });

        row.find('.quantity').on('blur',function(){
            let prevSelectCat = $(this).closest(".input-group").parent().prev().prev().find(".category_id").attr("id"); 
            let prevSelectItem = $(this).closest(".input-group").parent().prev().find(".item").attr("id"); 
            t.checkAvailableQnt(parseInt($.trim(row.find("#" + prevSelectItem).val())), parseInt($.trim(row.find("#" + prevSelectCat).val())), prevSelectItem.split("_")[1], $(this).val(),'quantity');
            const value = parseInt(this.value);
            this.value = isNaN(value) ? '' : value;
        });

        row.find('.item').on('change',function(){
            let prevSelectCat = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id");
            let prevSelectItem = $(this).closest(".input-group").parent().find(".item").attr("id");
            t.checkAvailableQnt(parseInt($.trim(row.find("#" + prevSelectItem).val())), parseInt($.trim(row.find("#" + prevSelectCat).val())), prevSelectItem.split("_")[1], $(this).val(),'item');
        });
        row.find('.category_id').on('change', function(){
            var cat_id = $(this).val();
            var id = $(this).closest(".input-group").parent().find(".category_id").attr("id");
            var item_count = id.split("_")[2];
            t.resetData(cat_id, item_count);
        });
    }

    function updateAddButton() {
        if($("#mdl-servicerequest").find("#custom_form_id").val() == '') {
            $(".btn-addnew").each(function (index) {
                if (index === $("#repeater-container .entry").length - 1) {
                    $(this).html('<button type="button" class="btn btn-theme-red addNewRow" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Remove"><i class="glyphicon glyphicon-plus"></i></button>');
                } else {
                    $(this).html('<button type="button" class="btn btn-theme-red remove-row" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Remove"><i class="glyphicon glyphicon-minus"></i></button>');
                }
            });
        } else {
            $(".btn-addnew").each(function (index) {
                // console.log(index);
                var $td = $(this).closest('.btn-addnew');
                if (index === 0 && $td.find('.addNewRow').length > 0) {
                    $(this).html(`
                        <button type="button" class="btn btn-theme-red addNewRow" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Add More">
                            <i class="glyphicon glyphicon-plus"></i>
                        </button>
                    `);
                } else {
                    $(this).html(`
                        <button type="button" class="btn btn-theme-red remove-row" style="min-width: 0px;" data-toggle="tooltip" data-placement="right" title="Remove">
                            <i class="glyphicon glyphicon-minus"></i>
                        </button>
                    `);
                }
            });
        }
    }

    $(document).on("click", ".addNewRow", function () {
        const newRow = $(createNewRow(false));
        $("#repeater-container").append(newRow);
        initializeSelect2ForRow(newRow);
        updateAddButton();
    });
    
    $(document).on("click", ".remove-row", function () {
        $(this).closest(".entry").remove();
        if ($("#repeater-container .entry").length === 0) {
            const initialRow = $(createNewRow(true));
            $("#repeater-container").append(initialRow);
            initializeSelect2ForRow(initialRow);
        }
        updateAddButton();
    });

    // Initialize with one row
    if ($("#repeater-container .entry").length === 0) {
        const initialRow = $(createNewRow(true));
        $("#repeater-container").append(initialRow);
        initializeSelect2ForRow(initialRow);
    }
    updateAddButton();

    var currentStep = 0;
    var form = $(document).find("#requested_form");

    function steps() { return $(document).find(".step"); }
    function progressBarItems() { return $(document).find(".progress-bar1 .step-marker"); }

    function showStep(step) {
        if(manager_name == '') {
            $(document).find('#manager_data').addClass('hide');
        }
        steps().removeClass("active").eq(step).addClass("active");
        progressBarItems().removeClass("active").eq(step).addClass("active");
    }

    function validateStep(step) {
        var valid = true;
        var inputs = steps().eq(step).find("input");

        inputs.each(function() {
            var input = $(this);
            var value = input.val().trim();
            var fieldName = input.attr("name");
            var employeeType = $(document).find('input[name="employee_type"]:checked').val();
            var errorMsg = "";

            if(fieldName === "emp_no" && employeeType == "LTSCT Employee") {
                if (value === "" || value.trim() === "")
                {
                    errorMsg = "EMP/PS Number is required.";
                }
            } else if(fieldName === "b_location" && employeeType == "LTSCT Employee") {
                if (value === "" || value.trim() === "") {
                    errorMsg = "Location is required.";
                }
            } else if((fieldName === "email_id" || fieldName === "contact") && employeeType == "Non LTSCT Employee") {
                var email = $('#email_id').val().trim();
                var contact = $('#contact').val().trim();
                if (email === "" && contact === "") {
                    errorMsg = "Either Email or Contact is required.";
                } else if(fieldName === "email_id" && email !== "" && !/^\S+@\S+\.\S+$/.test(email)) {
                    errorMsg = "Enter a valid email address.";
                }
            } else if(fieldName === "emp_name" && employeeType == "Non LTSCT Employee") {
                if (value === "") { errorMsg = "Name is required."; }
            }

            input.parent().next(".error-message").remove();
            if (errorMsg) {
                input.parent().after(`<div class="error-message" >${errorMsg}</div>`);
                valid = false;
            }
        });

        return valid;
    }

    $(document).on('click', '.next', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    $(document).on('click', '.prev', function() {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    window.getCurrentStep = function() {
        return currentStep;
    };

    window.resetStep = function() {
        currentStep = 0;
        showStep(currentStep);
    };

    $(document).on('change', 'input[name="employee_type"]', function () {
        $(".error-message").remove();
        const selectedValue = $(this).val();

        if (selectedValue === 'Non LTSCT Employee') {
            $('.psno-container').hide();
            $(document).find('#emp_no, #emp_name, #contact, #email_id, #cost_center, #wbs_note').prop('readonly', false).val('');
            $(document).find('#emp_number').prop('readonly', true).val('');
            $('input[name="department"]').val('');
            $('input[name="department_id"]').val('');
            $('input[name="location"]').val('');
            $('input[name="location_id"]').val('');
        } else {
            $('.psno-container').show();
            if(selectedValue === 'Self') {
                $(document).find('#emp_name, #email_id, #contact').prop('readonly', true).val('');
                $(document).find('#emp_no, #location, #department').prop('disabled', true).empty();
                $(document).find('#wbs_note').prop('readonly', false).val('');
                $(document).find('#cost_center').prop('readonly', true).val('');
                $(document).find('#emp_number').prop('readonly', true).val('');
                $('input[name="department"]').val('');
                $('input[name="department_id"]').val('');
                $('input[name="location"]').val('');
                $('input[name="location_id"]').val('');
                var option = new Option("Select Department", null, true, true);
                $(document).find('#build-wrap').find('#department').append(option);
            } else if (selectedValue === 'LTSCT Employee') {
                $(document).find('#emp_no').prop('disabled', false).empty();
                $(document).find('#emp_name, #email_id, #contact').prop('readonly', true).val('');
                $(document).find('#location, #department').prop('disabled', true).empty();
                $(document).find('#wbs_note').prop('readonly', false).val('');
                $(document).find('#cost_center').prop('readonly', true).val('');
                $(document).find('#emp_number').prop('readonly', true).val('');
                $('input[name="department"]').val('');
                $('input[name="department_id"]').val('');
                $('input[name="location"]').val('');
                $('input[name="location_id"]').val('');
                var option = new Option("Select Department", null, true, true);
                $(document).find('#build-wrap').find('#department').append(option);
            } else {
                $(document).find('#emp_name, #contact, #email_id').prop('readonly', false).val('');
                $(document).find('#emp_no, #location, #department').prop('disabled', false);
                $(document).find('#cost_center, #wbs_note').prop('readonly', false).val('');
                $(document).find('#wbs_note').prop('readonly', false).val('');
                $(document).find('#emp_number').prop('readonly', true).val('');
                $('input[name="department"]').val('');
                $('input[name="department_id"]').val('');
                $('input[name="location"]').val('');
                $('input[name="location_id"]').val('');
            }
        }
    });
};