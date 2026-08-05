var RequestedForm = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#body_wrapper");
    t.frm = t.content.find('#requested_form');
    t.frmEl = {};
    t.btn = {};
    t.btn.submit = t.frm.find("#saveData");
    t.btn.botSubmit = t.frm.find("#saveDataForBot");
    t.mdlServiceRequest = t.content.find('#mdl-servicerequest');
    t.mdlServiceRequest.title = t.mdlServiceRequest.find(".modal-title");
    t.mdlServiceRequest.btnSubmit = t.mdlServiceRequest.find("#saveData");
    t.mdlServiceRequest.forAction = t.mdlServiceRequest.find("#for_action");
    t.mdl = t.content.find("#createTicket");
    t.mdl.frm = t.mdl.find("#ticket-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.request_form_view_url = t.content.find("#request-form-view-url");
    // t.mdl.frmEl.request_form_view_url_non_priv = t.page.find("#request-form-view-url-non-priv");
    t.mdl.frmEl.form_view_info = t.content.find("#form-view-info");
    t.mdl.frmEl.form_data = t.content.find("#form_data");
    t.mdl.frmEl.form_view_data = t.content.find("#form-view-data");
    // t.mdl.frmEl.form_view_info_non_priv = t.content.find("#form-view-info-non-priv");
    t.httpPostPath = t.config.url.update_form;
    let selected = [];
    let preselected = [];

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        invalidHandler:function(form, validator) {
            var errors = validator.numberOfInvalids();
            if(errors) {
                validator.errorList[0].element.focus();
            }
        },
        errorPlacement: function (error, element) {
            element.closest(".form-group").next(".error-message").remove();
            element.closest(".form-group").after(error);
        }
    });

    t.handleSubmit = function(e) {
        e.preventDefault();
        var frmElements = $("#requested_form").find('[required]');
        var frmElementsfile = $("#requested_form").find('input[type^="file"]');
        var temp_file = 0;
        let isValid = true;
        let crValid = true;
        if($("#mdl-servicerequest").find("#field_form_required").val() == 2) {
            let b_location = $("#requested_form").find(".b_location").val();
            let inputGroup = $("#requested_form").find(".b_location").closest(".input-group");
            if(t.config.client == "ltsct") {
                var custom_type = $("#requested_form").find(".custom_type").val();
                var inputGroupCustom_type = $("#requested_form").find(".custom_type").closest(".input-group");
            }
            let errorLocationMessage = inputGroup.next(".error-message");
            if (!b_location || b_location === "null" || b_location.trim() === "") {
                isValid = false;
                $("#requested_form").find(".b_location").addClass("is-invalid");
                if (errorLocationMessage.length === 0) {
                    inputGroup.after('<div class="error-message text-danger">This field is required</div>');
                }
            } else {
                $("#requested_form").find(".b_location").removeClass("is-invalid");
                inputGroup.next(".error-message").remove();
            }
            if(t.config.client == "ltsct") {
                if (!custom_type || custom_type === "null" || custom_type.trim() === "") {
                    isValid = false;
                    $("#requested_form").find(".custom_type").addClass("is-invalid");
                    if (errorLocationMessage.length === 0) {
                        inputGroupCustom_type.after('<div class="error-message text-danger">This field is required</div>');
                    }
                } else {
                    $("#requested_form").find(".custom_type").removeClass("is-invalid");
                    inputGroupCustom_type.next(".error-message").remove();
                }
            }

            $("#repeater-container .entry").each(function () {
                $(this).find("select, input").each(function () {
                    let value = $(this).val();
                    let parentDiv = $(this).closest("td");
                    let errorMessage = parentDiv.find(".error-message");

                    if ($(this).hasClass("category_id")) {
                        if (!value || value === null || value === "null" || value === "") {
                            isValid = false;
                            $(this).addClass("is-invalid");

                            if (errorMessage.length === 0) {
                                parentDiv.append('<div class="error-message text-danger">This field is required</div>');
                            }
                        } else {
                            $(this).removeClass("is-invalid");
                            errorMessage.remove();
                        }
                    }

                    if ($(this).hasClass("item")) {
                        if (!value || value === null || value === "null" || value === "") {
                            isValid = false;
                            $(this).addClass("is-invalid");

                            if (errorMessage.length === 0) {
                                parentDiv.append('<div class="error-message text-danger">This field is required</div>');
                            }
                        } else {
                            $(this).removeClass("is-invalid");
                            errorMessage.remove();
                        }
                    }

                    if($(this).hasClass("quantity")) {
                        if (!value || value === null || value === "null" || value === "") {
                            isValid = false;
                            $(this).addClass("is-invalid");

                            if (errorMessage.length === 0) {
                                parentDiv.append('<div class="error-message text-danger">This field is required</div>');
                            }
                        } else {
                            $(this).removeClass("is-invalid");
                            errorMessage.remove();
                        }
                    }
                    $('input[type="number"][id^="quantity_"]').each(function() {
                        if ($(this).attr('data-valid') === 'false') {
                            isValid = false;
                        }
                    });
                });
                if (!isValid) {
                    return false;
                }
            });            
        }
        if($("#mdl-servicerequest").find("#field_form_required").val() != 2) {
            $.each(frmElements, function(i, v) {
                if($(v).is('[type="text"]') && t.config.client == "ltsct") {
                    $(v).rules("add", {
                        required: true,
                        minlength: t.config.client == "ltsct" ? 2 : 5,
                    });
                } else {
                    $(v).rules("add", {
                        required: true,
                    });
                }
                /*if($('input[type^="file"]')) {
                    if(typeof(this.files) != "undefined" && this.files != null) {
                        var i = 0;
                        while (i <= this.files.length - 1) {
                            // console.log(this.files[i].size);
                            temp_file = temp_file + this.files[i].size;
                            i++;
                        }
                    }
                }*/
            });
            $.each(frmElementsfile, function(j, v) {
                if($('input[type^="file"]')) {
                    // console.log(typeof(v.files));
                    if(typeof(this.files) != "undefined" && this.files != null) {
                        var i = 0;
                        while (i <= this.files.length - 1) {
                            // console.log(this.files[i].size);
                            temp_file = temp_file + this.files[i].size;
                            i++;
                        }
                    }
                }
            });
            if (temp_file > 10485760) {
                var data = {
                    'msg' : '<div style="text-align: center"><h3>Failure</h3><p> Please upload a file smaller than 10 MB</p></div>'
                }
                sweetAlert('center', 'error', data);
                return false;
            }
            if (t.frmValidator.form() == false) {
                return false;
            }
        }

        if($("#mdl-servicerequest").find("#field_form_required").val() == 2 && t.config.client == "ltts" && $("#mdl-servicerequest").find("#cr_custom_form").val() != '') {
            if($("#requested_form").valid() == false) {
                return false;
            }
        }
        if (!isValid && jQuery.inArray(t.config.client, ["ltts", "ltsct"]) !== -1 && jQuery.inArray(t.config.sub_client, ["admin", "ltsct"]) !== -1) {
            return false;
        }
        t.mdlServiceRequest.btnSubmit.attr("disabled", true);
        var formData = new FormData($('#requested_form')[0]);
        formData.set('request_id', btoa(formData.get('request_id')));
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdlServiceRequest.btnSubmit.removeAttr("disabled");

                    /*setTimeout(function() {
                        window.location = t.config.requested_form+"/view/"+data.requested_form_id;
                    }, 1000);*/
                    $("#mdl-edit").find("#request_submit_id").val(data.requested_form_id);
                    $("#mdl-transfer").find("#request_submit_id").val(data.requested_form_id);
                    t.mdlServiceRequest.modal("hide");
                    if($("#mdl-servicerequest").find("#field_form_required").val() != 2) {
                        sweetAlert('center', 'success', data);
                        var newUrl = t.config.requested_form+"/view/"+data.requested_form_id;
                        t.mdl.frmEl.request_form_view_url.html('<button class="btn dtActbtn dtActEdit" data-toggle="tooltip" data-original-title="Edit Request Form" data-id='+ data.requested_form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                        // t.mdl.frmEl.request_form_view_url_non_priv.html('<button class="btn dtActbtn dtActEdit" data-toggle="tooltip" data-original-title="Edit Request Form" data-id='+ data.requested_form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                    }
                    if($("#mdl-servicerequest").find("#field_form_required").val() == 2) {
                        t.mdl.frmEl.request_form_view_url.html('<button class="btn dtActbtn dtActEditCustom" data-toggle="tooltip" data-original-title="Edit Request Form" data-formid='+ data.form_id +' data-id='+ data.requested_form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                        // t.mdl.frmEl.request_form_view_url_non_priv.html('<button class="btn dtActbtn dtActEditCustom" data-toggle="tooltip" data-original-title="Edit Request Form" data-formid='+ data.form_id +' data-id='+ data.requested_form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                        if(jQuery.inArray(t.config.client, ["ltts", "ltsct"]) !== -1 && jQuery.inArray(t.config.sub_client, ["admin", "ltsct"]) !== -1) {
                            $("#ticket-mdl").find("#content").summernote('code', "Requested Items added Successfully");
                            $("#ticket-by-np-mdl").find("#content").summernote('code', "Requested Items added Successfully");
                            $('.ticket-mdl-box #btnSubmit').click();
                        }
                        $('#eye_icon').remove();

                        if(t.config.client == "ltts" && $("#mdl-servicerequest").find("#field_form_required").val() == 2 && data.exception_access_cr == "yes") {
                             $("#ticket-mdl-frm").append('<input type="hidden" name="cr_custom_form" value="'+data.exception_access_data+'">');
                             $("#ticket-by-np-mdl").find('#ticket-mdl-frm').append('<input type="hidden" name="cr_custom_form" value="'+data.exception_access_data+'">');
                        }
                    }
                    t.mdl.frmEl.form_view_info.removeClass("hide").show();
                    // t.mdl.frmEl.form_view_info_non_priv.removeClass("hide").show();
                    t.mdl.frmEl.request_form_view_url.show();
                    // t.mdl.frmEl.request_form_view_url_non_priv.show();

                    /*$.get(t.config.url.service_request_view_form+ "/" + data.requested_form_id, function(result) {
                        if (typeof result == "object") {
                            if (result.status != "success") {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + result.msg + '.</p></div>' });
                                return;
                            }
                            console.log(result.data.field_values);
                        } else {
                            vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>Unable to load form.</p></div>' });
                        }
                    });*/
                } else {
                    t.mdlServiceRequest.btnSubmit.removeAttr("disabled");
                    t.mdlServiceRequest.modal("hide");
                    sweetAlert('center', 'error', data);
                    if(data.existing != undefined && data.existing == true) {
                        var newUrl = t.config.requested_form+"/view/"+data.form_id;
                        t.mdl.frmEl.request_form_view_url.html('<button class="btn dtActbtn dtActEdit" data-toggle="tooltip" data-original-title="Edit Request Form" data-id='+ data.form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                        // t.mdl.frmEl.request_form_view_url_non_priv.html('<button class="btn dtActbtn dtActEdit" data-toggle="tooltip" data-original-title="Edit Request Form" data-id='+ data.form_id +' "\" ><img src="'+t.config.editFormImage+'" style="width: 21px;height: 20px;"></button>');
                        t.mdl.frmEl.form_view_info.removeClass("hide").show();
                        // t.mdl.frmEl.form_view_info_non_priv.removeClass("hide").show();
                        t.mdl.frmEl.request_form_view_url.show();
                        // t.mdl.frmEl.request_form_view_url_non_priv.show();
                    }
                }
            }
        });
        http.fail(function() {
            t.mdlServiceRequest.btnSubmit.removeAttr("disabled");
            var data = {
                'msg' : 'Something went wrong. Please refresh page and try again'
            }
            sweetAlert('center', 'error', data);
        });
    };

    t.handleBotSubmit = function(e) {
        e.preventDefault();
        var frmElements = $("#requested_form").find('[required]');
        var frmElementsfile = $("#requested_form").find('input[type^="file"]');
        var temp_file = 0;
        $.each(frmElements,function(i,v){
            if( $(v).is('[type="text"]') ) {
                $(v).rules("add", {
                    required: true,
                    minlength: 5,
                });
            } else {
                $(v).rules("add",{
                    required: true,
                });
            }
            /*if($('input[type^="file"]')) {
                if(typeof(this.files) != "undefined" && this.files != null) {
                    var i = 0;
                    while (i <= this.files.length - 1) {
                        // console.log(this.files[i].size);
                        temp_file = temp_file + this.files[i].size;
                        i++;
                    }
                }
            }*/
        });
        $.each(frmElementsfile, function(j,v) {
            if($('input[type^="file"]')) {
                // console.log(typeof(v.files));
                if(typeof(this.files) != "undefined"  && this.files !=null) {
                    var i = 0;
                    while (i <= this.files.length - 1) {
                        // console.log(this.files[i].size);
                        temp_file = temp_file + this.files[i].size;
                        i++;
                    }
                }
            }
        });
        if(temp_file > 10485760) {
            var data = {
                'msg' : '<div style="text-align: center"><h3>Failure</h3><p> Please upload a file smaller than 10 MB</p></div>'
            }
            sweetAlert('center', 'error', data);
            return false;
        }
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.mdlServiceRequest.btnSubmit.attr("disabled", true);
        var formData = new FormData($('#requested_form')[0]);
        var http = $.ajax({
            url: t.config.url.update_form_for_bot,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    localStorage.setItem('botFieldValues',data.data)
                    window.close();
                }
            }
        });
        http.fail(function() {
            t.mdlServiceRequest.btnSubmit.removeAttr("disabled");
            alert("Something went wrong. Please refresh page and try again");
        });
    };

    t.editForm = function(e) {
        e.preventDefault();
        var fId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.update;
        var http = $.get(t.config.url.edit + "/" + fId);
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdlServiceRequest.title.html("Edit Service Request Form");
                    t.mdlServiceRequest.forAction.val("edit");
                    t.mdlServiceRequest.btnSubmit.text("Update");
                    $('#requested_form #build-wrap').empty();
                    const fbRender = document.getElementById("build-wrap");
                    const formData = data.data.field_values;
                    var formD = ($.parseJSON(formData));
                    $(fbRender).formRender({ formData });
                    setupDependsOn(document.getElementById('requested_form'));
                    t.mdlServiceRequest.modal("show");
                    setTimeout(function() {
                        if(formD.length > 0) {
                            $.each(formD,function(i,v) {
                                var files = '';
                                if(v.type == 'file') {
                                    if(v.multiple == true) {
                                        $.each(v.value, function (j, w) {
                                            if (files == '') {
                                                files = w
                                            } else {
                                                files = files + " | " + w + " | ";
                                            }
                                        });
                                    } else {
                                        if (files == '') {
                                            files = v.value;
                                        } else {
                                            files = v.value;
                                        }
                                    }
                                    // console.log(files);
                                    $('#'+v.name).parent().append("<span style='float:left;margin:8px 0px;'>"+files+"</span>");
                                }
                            });
                            var countrySelect2 = ['select-1706852583269-0'];
                            $.each(countrySelect2, function(i,v){
                                if($("#"+v).length > 0) {
                                    $("#"+v).select2({
                                        width : '100%',
                                        placeholder: "Countries",
                                        ajax: {
                                            url: function (params) {
                                                return t.config.url.countries;
                                            },
                                            dataType: "json",
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            type: "GET",
                                            data: function (params) {
                                                return {
                                                    q: params.term,
                                                    page: params.page,
                                                    customField : true,
                                                }
                                            },
                                            processResults: function (data, params) {
                                                params.page = params.page || 1;
                                                return {
                                                    results: data.results,
                                                    pagination: {
                                                        more: (params.page * 30) < data.total
                                                    }
                                                }
                                            },
                                            cache: true
                                        }
                                    });
                                }
                            });
                        }
                    },2500);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg' : 'Something went wrong. Please refresh page and try again',
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    var renderForm = $('<form action="#"></form>' +
    '<button class="btn btn-default edit-form">Edit</button>');
    $(renderForm).find('div').attr('id', 'fb-rendered-form');
    $('#fb-rendered-form').append(renderForm);

    $(".go-list").on("click", function(e) {
        e.preventDefault();
        window.location = t.config.url.form_list;
    });

    t.editCustomForm = function(e) {
        e.preventDefault();
        var formid = $(this).attr("data-formid"); // Form ID lena
        var fId = $(this).attr("data-id");

        $("#mdl-servicerequest").find(".modal-dialog").addClass("custom-modal-xl");
        $("#mdl-servicerequest").find("#build-wrap").empty();

        $.get(t.config.url.ticket_getcustomview + "/" + formid, function(data) {
            $("#mdl-servicerequest").find("#build-wrap").html(data.content);
            $("#mdl-servicerequest").find("#custom_form_id").val(fId);
            $("#mdl-servicerequest").find("#field_form_required").val(2);
            if(data.form_name =='customform1') {
                $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                $("#requested_form .modal-body").css("background", "linear-gradient(to right, #0b174e, #109fca)");
            } else if(data.form_name == 'ltsctinventoryform') {
                $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                $("#requested_form .modal-body").css("background", "linear-gradient(to right, #0b174e, #109fca)");
            } else{
                $("#mdl-servicerequest").find(".modal-dialog").removeClass("custom-modal-xl");
                $("#mdl-servicerequest").find(".modal-dialog").addClass("custom-modal-sm");
                $("#requested_form .modal-body").css("background", "#fff");
                $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                if(data.form_name == "lttscustomform") {
                    $("#mdl-servicerequest").find("#mdl_popup_loader").addClass('active');
                    let user_id = $("#ticket-mdl-frm").find("#creator_id").val();
                    let depart_id = $("#ticket-mdl-frm").find("#department_id").val();
                    let problem_id = $("#ticket-mdl-frm").find("#problem_category_id").val();
                    let sub_cat_id = $("#ticket-mdl-frm").find("#sub_category_id").val();
                    $.get(t.config.url.fetch_active_ticket + "/" + user_id+"?department="+depart_id+"&category="+problem_id+"&sub_category="+sub_cat_id, function(data) {
                        if(data.active_ticket == "yes") {
                            $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                            $("#mdl-servicerequest").find(".no_active_ticket").addClass("hide");
                            $("#mdl-servicerequest").find(".active_ticket").removeClass("hide");
                            $("#mdl-servicerequest").find("#cr_custom_form").val('active_ticket');
                            $("#mdl-servicerequest").find(".active_ticket_id").html("Already ticket id <a class='have_active_access' href='" + t.config.url.requestInfo + '/' + data.request_id + "' target='_blank'>#" + data.request_id + "</a>("+data.req_status+") is active for GitHub<br><input type='hidden' name='active_ticket' value='"+data.request_id+"'> ");
                            var eliminateSpace = '';
                            (data.field_values).forEach(element => {
                                eliminateSpace = element.replace("-", "_");
                                $("#mdl-servicerequest").find(".active_ticket").find("#"+eliminateSpace.toLowerCase()).prop('checked', true)
                                let idName = eliminateSpace.replace(/\s+/g, '').toLowerCase();
                                $("#mdl-servicerequest .active_ticket #" + idName).addClass("checkbox_checked");
                                $("#mdl-servicerequest").find(".active_ticket").on('click',"#"+eliminateSpace.toLowerCase(), function(e) {
                                    if (!$(this).prop('checked')) {
                                        $(this).prop('checked', true);
                                    }
                                });
                            });
                        } else {
                            $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                            $("#mdl-servicerequest").find(".active_ticket").addClass("hide")
                            $("#mdl-servicerequest").find(".no_active_ticket").removeClass("hide")
                            $("#mdl-servicerequest").find("#cr_custom_form").val('no_active_ticket');
                        }
                    });
                    $("#mdl-servicerequest").find("#mdl_popup_loader").removeClass('active');
                }
                // $("#mdl-servicerequest").find("#cr_popup_loader").removeClass('active');
            }
        }).done(function() {
            var customFormData = {};
            $.ajax({
                url: t.config.url.requested_custom_form + "/" + fId,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        t.mdlServiceRequest.modal("show");
                        if($("#mdl-servicerequest").find("#field_form_required").val() == 2 && t.config.client == "ltts" &&  t.config.sub_client == "admin"){
                            setTimeout(function () {
                                loadFormData(response.data);
                            }, 1000);
                        }
                        if(typeof response.data.field_values.active_ticket !== "undefined" &&  response.data.field_values.active_ticket !== null ){
                            customFormData = response.data.field_values.exception_access ;
                        } else {
                            customFormData = response.data.field_values.exception_access.join('_').replace(/ /g, '_');
                        }
                    }
                    if($("#mdl-servicerequest").find("#field_form_required").val() == 2 && t.config.client == "ltts" &&  t.config.sub_client != "admin"){
                        if(Array.isArray(customFormData)){
                            var eliminateSpace = '';
                            (customFormData).forEach(element => {
                                eliminateSpace = element.replace(" ", "_");
                                $("#mdl-servicerequest").find(".active_ticket").find("#"+eliminateSpace.toLowerCase()).prop('checked', true)
                                let idName = eliminateSpace.replace(/\s+/g, '').toLowerCase();
                                $("#mdl-servicerequest .active_ticket #" + idName).addClass("checkbox_checked");
                                $("#mdl-servicerequest").find(".active_ticket").on('click',"#"+eliminateSpace.toLowerCase(), function(e) {
                                    if (!$(this).prop('checked')) {
                                        $(this).prop('checked', true);
                                    }
                                });
                            });
                        } else {
                            var eliminateSpace = customFormData;
                            $("#mdl-servicerequest").find(".no_active_ticket").find("#"+eliminateSpace.toLowerCase()).prop('checked', true)
                            $("#mdl-servicerequest").find(".no_active_ticket").on('click',"#"+eliminateSpace.toLowerCase(), function(e) {
                                if (!$(this).prop('checked')) {
                                    $(this).prop('checked', true);
                                }
                            });
                        }
                    }
                },
                error: function() {
                    alert("Something went wrong while fetching edit data!");
                }
            });
        });
    };

    t.arraysHaveSameElements = function(arr1, arr2) {
        return arr1.length === arr2.length && arr1.sort().every((val, i) => val === arr2.sort()[i]);
    }

    t.fetchNextData = function(e) {
        selected = [];
        var pass_params = false;
        document.querySelectorAll('input[name="exception_access[]"]:checked').forEach((cb) => {
            selected.push(cb.value);
        });
        if (selected.length > 0) {
            items = selected.join("+").split();
        }
        if (selected.length <= 0) {
            document.querySelectorAll('input[name="exception_access"]:checked').forEach((cb) => {
                selected.push(cb.value);
            });
        }
        if(selected.length == 0){
            $("#mdl-servicerequest").find("#exception_error").html("Please select any one access");
            return false;
        }else{
            $("#mdl-servicerequest").find("#exception_error").html("");
        }

        if($("#mdl-servicerequest").find("#dynamic_form_for_cr").hasClass("hide")){
            $("#mdl-servicerequest").find("#dynamic_form_for_cr").removeClass("hide");
            pass_params = true;
        }
        if($("#mdl-servicerequest").find("#footer_button").hasClass("hide")){
            $("#mdl-servicerequest").find("#footer_button").removeClass("hide");
        }
        if(!$("#mdl-servicerequest").find("#saveData").hasClass('backBtn')){
            $("#mdl-servicerequest").find("#backData").remove();
            console.log('sssss');
            $("#mdl-servicerequest").find("#saveData").before('<button type="button" id="backData" class="btn btn-theme-black backBtn">Back</button>');
        }
        // let actualData = selected;
        // let element = '';

        $("#mdl-servicerequest").find(".active_ticket").hide();
        $("#mdl-servicerequest").find(".no_active_ticket").hide();
        $("#mdl-servicerequest").find("#custom_form_footer").hide();
        if(t.arraysHaveSameElements(preselected, selected) == false){
            $("#mdl-servicerequest").find("#mdl_popup_loader").addClass('active');
            $.ajax({
                    url: t.config.url.fetch_git_form + "/" + selected,
                    type: "POST",
                    dataType: "json",
                    success: function(response) {

                        const formJson = JSON.parse(response[0].fields);
                        const container = document.getElementById("dynamic_form_for_cr");
                        container.innerHTML = "";
                        const form = document.createElement("div");

                        $("#mdl-servicerequest").find("#footer_button").removeClass("hide");
                        $("#mdl-servicerequest").find("#mdl_popup_loader").removeClass('active');

                        var test = JSON.parse(response[0].fields);

                        let sub_cat_name = $("#ticket-mdl-frm").find("#sub_category_id option:selected").text();
                        let sub_cat_id = $("#ticket-mdl-frm").find("#sub_category_id").val();
                        let sub_category_name = '';
                        if (sub_cat_id === null || sub_cat_id === "" || sub_cat_id === "null" || typeof sub_cat_id === "undefined") {
                            sub_cat_name = '';
                            sub_cat_id = $("#ticket-by-np-mdl").find("#sub_category_id").val();
                            if(sub_cat_id != null){
                                sub_cat_name = $("#ticket-by-np-mdl").find("#sub_category_id option:selected").text(); 
                            }
                        }
                        if (sub_cat_id === null || sub_cat_id === "" || sub_cat_id === "null" || typeof sub_cat_id === "undefined") {
                            let problem_category_name = $("#ticket-mdl-frm").find("#problem_category_id option:selected").text();
                            if(problem_category_name == ""){
                                problem_category_name = $("#ticket-by-np-mdl").find("#problem_category_id option:selected").text();
                            }
                            sub_cat_name = problem_category_name;
                        }
                        sub_category_name = sub_cat_name;

                        test.forEach(field => {
                            if (field.label && field.label.includes("GitHub Org URL")) {
                                if(sub_category_name != "" && sub_category_name == "Internal-LTTS") {
                                    field.label = "Provide existing LTTS GitHub Org URL";
                                }
                                if(sub_category_name != "" && sub_category_name == "External-Public") {
                                    field.label = "Provide the Public GitHub URL";
                                }
                                if(sub_category_name != "" && sub_category_name == "External-Customer") {
                                    field.label = "Provide the GitHub URL provided by customer";
                                }
                            }
                        });

                        var fbTemplate = document.getElementById('dynamic_form_for_cr'),
                        $formContainer = $("#form_data");

                        var options = {
                            formData: test,
                            allowStageSort: false,
                            showActionButtons: false,
                            stickyControls: false,
                            disabledFieldButtons: {
                                autocomplete: ['remove','edit','copy'],
                                text: ['remove','edit','copy'],
                                select: ['remove','edit','copy'],
                                textarea: ['remove','edit','copy'],
                                paragraph: ['remove','edit','copy'],
                                number: ['remove','edit','copy'],
                                button: ['remove','edit','copy'],
                                date: ['remove','edit','copy'],
                                file: ['remove','edit','copy'],
                                header: ['remove','edit','copy'],
                                hidden: ['remove','edit','copy'],
                                'radio-group': ['remove','edit','copy'],
                                'checkbox-group': ['remove','edit','copy'],
                            }
                        };

                        $('#requested_form #dynamic_form_for_cr').empty();
                        $(fbTemplate).formRender(options);
                        $formContainer.val(JSON.stringify(test));
                        if ($('.field-attachment_for_external_customer').length > 0) {
                            $('.field-attachment_for_external_customer').addClass('hide');
                        }
                        setTimeout(() => {
                        $("label").each(function () {
                            if ($(this).text().includes("GitHub Org URL") || $(this).text().includes("GitHub URL")) {
                                let input = $(this).next("input, textarea");
                                if (input.length) {
                                    input.attr("placeholder", "https://github.com");
                                }
                            }
                            if($('#mdl-servicerequest').find('#github_access_url').val() != '' ) {
                                $('#mdl-servicerequest').find('#github_url').val($('#mdl-servicerequest').find('#github_access_url').val());
                                $('#mdl-servicerequest').find('#github_url').prop('readonly', true);
                            } else {
                                $('#mdl-servicerequest').find('#github_url').prop('readonly', false);
                            }
                            if(sub_category_name != "" && sub_category_name == "External-Customer" && $('.field-attachment_for_external_customer').length > 0) {
                                $('.field-attachment_for_external_customer').removeClass('hide');
                            }
                        });
                    }, 200);
                        setupDependsOn(document.getElementById('requested_form'));
                        document.getElementById("saveData").addEventListener("click", () => {

                            if ($("#requested_form").valid()) {
                                var outputHtml = $(fbTemplate).formRender("userData");
                                $formContainer.val(JSON.stringify(outputHtml));
                            } else {
                                let rules = {};
                                let messages = {};

                                $("#requested_form :input[name]").each(function () {
                                    let fieldName = $(this).attr("name");
                                    rules[fieldName] = {};
                                    messages[fieldName] = {};

                                    if ($(this).prop("required")) {
                                        rules[fieldName].required = true;
                                        messages[fieldName].required = ($(this).attr("placeholder") || fieldName) + " is required";
                                    }

                                    if ($(this).attr("type") === "email") {
                                        rules[fieldName].email = true;
                                        messages[fieldName].email = "Please enter a valid email address";
                                    }

                                    if ($(this).attr("pattern")) {
                                        rules[fieldName].pattern = new RegExp($(this).attr("pattern"));
                                        messages[fieldName].pattern = "Invalid format for " + fieldName;
                                    }

                                    if ($(this).attr("min")) {
                                        rules[fieldName].min = parseInt($(this).attr("min"));
                                        messages[fieldName].min = "Value must be at least " + $(this).attr("min");
                                    }

                                    if ($(this).attr("max")) {
                                        rules[fieldName].max = parseInt($(this).attr("max"));
                                        messages[fieldName].max = "Value must be less than or equal to " + $(this).attr("max");
                                    }
                                });
                                $("#requested_form").validate({
                                    rules: rules,
                                    messages: messages,
                                    errorClass: "text-danger",
                                    errorElement: "span"
                                });
                                return false;
                            }
                        });
                    }
                });
                preselected = selected = [];
        }
    }

    t.backData = function() {
        var tabs= $("#mdl-servicerequest").find("#cr_custom_form").val();
        // console.log(tabs);
        $("#mdl-servicerequest").find("."+ tabs).show();
        $("#mdl-servicerequest").find("#footer_button").addClass("hide");
        $("#mdl-servicerequest").find("#custom_form_footer").show();
        $("#mdl-servicerequest").find("#backData").remove();
        $("#mdl-servicerequest").find("#dynamic_form_for_cr").addClass("hide");
        preselected = [];
    }

    t.content.on("click", "#saveData", $.proxy(t.handleSubmit));
    t.content.on("click", "#saveDataForBot", $.proxy(t.handleBotSubmit));
    t.content.on("click", ".dtActEdit", $.proxy(t.editForm));
    t.content.on("click", ".dtActEditCustom", $.proxy(t.editCustomForm));
    t.content.on("click", "#nextData", $.proxy(t.fetchNextData));
    t.content.on("click", "#backData", $.proxy(t.backData));
};
