var StatusRequestedForm = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#body_wrapper");
    t.frm = t.content.find('#status_requested_form');

    t.frmEl = {};

    t.btn = {};
    t.btn.submit = t.frm.find("#Status_saveData");
    t.mdlStatusServiceRequest = t.content.find('#mdl-status-servicerequest');
    t.mdlStatusServiceRequest.title = t.mdlStatusServiceRequest.find(".modal-title");
    t.mdlStatusServiceRequest.btnSubmit = t.mdlStatusServiceRequest.find("#Status_saveData");
    t.httpPostPath = t.config.url.store_form;

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        invalidHandler:function(form, validator) {
            var errors = validator.numberOfInvalids();
            if(errors) {
                validator.errorList[0].element.focus();
            }
        }
    });

    t.handleSubmit = function(e) {
        e.preventDefault();
        var frmElements = $("#status_requested_form").find('[required]');
        var frmElementsfile = $("#status_requested_form").find('input[type^="file"]');
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
        });
        $.each(frmElementsfile, function(j,v) {
            if($('input[type^="file"]')) {
                if(typeof(this.files) != "undefined"  && this.files !=null) {
                    var i = 0;
                    while (i <= this.files.length - 1) {
                        temp_file = temp_file + this.files[i].size;
                        i++;
                    }
                }
            }
        });
        if(temp_file > 19097000) {
            vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>'+ config.translations.file_size +'</p></div>' });
            return false;
        }
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.mdlStatusServiceRequest.btnSubmit.attr("disabled", true);
        var formData = new FormData($('#status_requested_form')[0]);
        console.log(formData);
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
                    t.mdlStatusServiceRequest.btnSubmit.removeAttr("disabled");
                    $("#ticket_status_form_id").val(data.ticket_status_form_id);
                    $("#set_edit_status_form").html(`
                        <div class="d-flex align-items-center"> 
                            <div class="col-md-9" id="edit_status_form" data-form_id="${data.ticket_status_form_id}">
                                <i class="bi bi-pencil-square" aria-hidden="true" style="font-size:20px; cursor:pointer; margin-right: 5px;"  data-toggle='tooltip' data-title='Edit Status Form'></i>
                                <span>Status Requested Form</span> 
                            </div>
                        </div>
                    `);
                    t.httpPostPath = t.config.url.store_form;
                    sweetAlert('center', 'success', data);
                    t.mdlStatusServiceRequest.modal("hide");
                } else {
                    t.mdlStatusServiceRequest.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            t.mdlStatusServiceRequest.btnSubmit.removeAttr("disabled");
            var data = {
                'msg' : 'Something went wrong. Please refresh page and try again'
            }
            sweetAlert('center', 'error', data);
        });
    };

    t.editStatusForm = function(e) {
        e.preventDefault();
        var fId = $(this).data("form_id");
        t.httpPostPath = t.config.url.update+ "/" + fId;
        var http = $.get(t.config.url.edit + "/" + fId);
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    var formName = data.data.form_name;
                    t.mdlStatusServiceRequest.title.html("Edit Service Request Form - " + formName);
                    t.mdlStatusServiceRequest.btnSubmit.text("Update");
                    $('#status_requested_form #status-build-wrap').empty();
                    const fbRender = document.getElementById("status-build-wrap");
                    const formData = data.data.field_values;
                    var formD = ($.parseJSON(formData));
                    $(fbRender).formRender({ formData });
                    $('#status_requested_form #status-build-wrap .form-group').css('width', '100%');
                    setupDependsOn(document.getElementById('requested_form'));
                    t.mdlStatusServiceRequest.modal("show");
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
                                    $('#'+v.name).parent().append("<span style='float:left;margin:8px 0px;'>"+files+"</span></br>");
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

    $(".go-list").on("click", function(e) {
        e.preventDefault();
        window.location = t.config.url.form_list;
    });

    t.content.on("click", "#Status_saveData", $.proxy(t.handleSubmit));
    t.content.on("click", "#edit_status_form", $.proxy(t.editStatusForm));
};
