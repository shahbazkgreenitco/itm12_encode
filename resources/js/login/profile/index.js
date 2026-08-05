var ModelAdd = function (config) {

    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#changeRow');

    t.frm = t.mdl.find('#addForm');

    t.frmEl = {};
    t.frmEl.current_password = t.frm.find('#current_password');
    t.frmEl.new_password = t.frm.find('#new_password');
    t.frmEl.confirm_password = t.frm.find('#confirm_password');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;

    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;

    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            current_password: {
                required: true,
                maxlength:30,
                password: true
            },
            new_password: {
                required: true,
                minlength:6,
                maxlength:30,
                password: true
            },
            confirm_password: {
                required: true,
                minlength:6,
                maxlength:30,
                password: true
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo( element.parent("div").parent('div'));
        }
    });
    $(document).on('click', '.toggle-password', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#current_password");
        input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
    });
    $(document).on('click', '.toggle-password1', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#new_password");
        input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
    });
    $(document).on('click', '.toggle-password2', function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#confirm_password");
        input.attr('type') === 'password' ? input.attr('type','text') : input.attr('type','password')
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));

};

var LoginWithMicrosoft = function(config) {
    var t = this;
    t.config = config;
    var t = this;
    t.config = config;

    t.content = $('.cls-content');
    t.frm = t.content.find('#loginFrm');
    

    t.frmEl = {};
    t.frmEl.username = t.frm.find('#username');
    t.frmEl.password = t.frm.find('#password');

    t.btn = {};
    t.btn.continue = t.frm.find('.btnContinue');
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.btnMsLogin = t.frm.find('#btnMsLogin');
    t.btn.otpSubmit = t.frm.find('#btnSubmitOtp');
    t.loader = t.frm.find('#loader_img');
    t.btn.clear = t.frm.find('#btnClear');

    // t.btn.submit.prop('disabled', true);
    t.httpCall = false;

    t.handlesubmit = function (e) {
        const isPwdVisible = $('.passwordDiv').css('display') != 'none';
        if(!isPwdVisible) {
            return false;
        }
        // if (config.impersonate === true) {
        //     const isotpVisible = $('.otpDiv').css('display') != 'none';
        //     if(!isotpVisible) {
        //         return false;
        //     }
        // }
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
    };

    t.frmValidator = t.frm.validate({
        // onsubmit: false,
        rules: {
            username: {
                required: true,
                username: true
            },
            password: {
                required: true,
                password: true
            },
            email:{
                required: false,
            }
        }
    });

    t.btn.submit.on('click', function() {
        t.loader.hide();
        t.frmValidator.settings.rules.password.required = true;
        t.frmValidator.settings.rules.email = {
            required: false,
            email: false
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
    });

    t.btn.otpSubmit.on('click', function() {
        t.frmValidator.settings.rules.password.required = false;
        t.frmValidator.settings.rules.email = {
            required: true,
            email: true
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
        var isEmailValid = t.frmValidator.element('#email');
        if (isEmailValid) {
            t.loader.show();
        } else {
            t.loader.hide();
        }
    });

    t.validateUserName = function(e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method : 'GET',
            url : t.config.url.validateUsername+"/"+t.frmEl.username.val(),
            success:function(data){
                if(data.status == "success") {
                    t.btn.continue.hide();
                    t.btn.submit.show();
                    t.frm.find(".validateUserMsg").hide();
                    t.frm.find(".passwordDiv").show();
                    if(data.impersonate === true) {
                        t.btn.otpSubmit.show();
                        t.frm.find(".emailDiv").show();
                    }
                    // t.btn.submit.prop('disabled', false);
                } else {
                    t.frm.find(".validateUserMsg").html(data.msg);
                    t.frm.find(".validateUserMsg").show();
                }
            },
            fail:function(data){
                t.frm.find(".validateUserMsg").show();
            }
        })
    }
    t.validateMicrosoftUserName = function(e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method : 'GET',
            url : t.config.url.validateUsername+"/"+t.frmEl.username.val(),
            success:function(data){
                if(data.status == "success") {
                    window.location = "https://apps.greenitco.com/shyammetalics/login-with-office?username="+t.frmEl.username.val();
                    // t.btn.submit.prop('disabled', false);
                } else {
                    t.frm.find(".validateUserMsg").html(data.msg);
                    t.frm.find(".validateUserMsg").show();
                }
            },
            fail:function(data){
                t.frm.find(".validateUserMsg").show();
            }
        })
    }
    t.frm.on('submit',  $.proxy(t.validateUserName));
    t.btn.continue.on("click",t.validateUserName);
    t.btn.btnMsLogin.on("click",t.validateMicrosoftUserName);
}

var LoginAdd = function (config) {
    var t = this;
    t.config = config;

    t.content = $('.cls-content');
    t.frm = t.content.find('#loginFrm');
    // console.log(t.frm);

    t.frmEl = {};
    t.frmEl.username = t.frm.find('#username');
    t.frmEl.password = t.frm.find('#password');
    t.frmEl.email = t.frm.find('#email');
    
    t.btn = {};
    t.btn.continue = t.frm.find('.btnContinue');
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.otpSubmit = t.frm.find('#btnSubmitOtp');
    t.btn.clear = t.frm.find('#btnClear');
    t.loader = t.frm.find('#loader_img');
    t.httpCall = false;

    t.handlesubmit = function (e) {
        const isPwdVisible = $('.passwordDiv').css('display') != 'none';
        if(!isPwdVisible) {
            return false;
        }

        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            username: {
                required: true,
                username: true
            },
            password: {
                required: true,
                password: true
            },
            otp: {
                required: false,
            },
            email:{
                required: false,
            }
        }
    });
    t.btn.otpSubmit.on('click', function() {
        t.frmValidator.settings.rules.password.required = false;
        t.frmValidator.settings.rules.email = {
            required: true,
            email: true
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
        var isEmailValid = t.frmValidator.element('#email');
        if (isEmailValid) {
            t.loader.show();
        } else {
            t.loader.hide();
        }
    });
    t.btn.submit.on('click', function() {
        t.loader.hide();
        t.frmValidator.settings.rules.password.required = true;
        t.frmValidator.settings.rules.email = {
            required: false,
            email: false
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
    });
    if(config.client == "ltts" || config.client == "tbsl") {
        $('#username').on('input', function() {
            var inputValue = $(this).val().trim();
            if(typeof config.impersonate_un !== 'undefined') {
                if(inputValue === config.impersonate_un) {
                    t.btn.otpSubmit.show();
                    t.frm.find(".emailDiv").show();
                } else {
                    t.btn.otpSubmit.hide();
                    t.frm.find(".emailDiv").hide();
                }
            }
        });
    }

    t.validateUserName = function(e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method : 'GET',
            url : t.config.url.validateUsername+"/"+t.frmEl.username.val(),
            success:function(data){
                if(data.status == "success") {
                    t.btn.continue.hide();
                    t.btn.submit.show();
                    t.frm.find(".validateUserMsg").hide();
                    t.frm.find(".passwordDiv").show();
                    t.frmEl.username.prop("readonly", true);
                    if (data.impersonate === true) {
                        t.btn.otpSubmit.show();
                        t.frm.find(".emailDiv").show();
                    }
                    // t.btn.submit.prop('disabled', false);
                } else {
                    t.frm.find(".validateUserMsg").html(data.msg);
                    t.frm.find(".validateUserMsg").show();
                }
            },
            fail:function(data){
                t.frm.find(".validateUserMsg").show();
            }
        })
    }
    t.btn.continue.show();
    t.frm.on('submit',  $.proxy(t.handlesubmit));
    t.btn.continue.on("click",t.validateUserName);
};

var ProfileAdd = function (config) {
    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#profileadd');

    t.frm = t.mdl.find('#profileForm');

    t.frmEl = {};
    t.frmEl.first_name = t.frm.find('#first_name');
    t.frmEl.last_name = t.frm.find('#last_name');
    t.frmEl.location_id = t.frm.find('#location_id');
    t.frmEl.website = t.frm.find('#website');
    t.frmEl.gravatar = t.frm.find('#gravatar');
    t.frmEl.request_approval_delegated_user = t.frm.find('#request_approval_delegated_user');
    t.frmEl.ticket_handler_delegated_user = t.frm.find('#ticket_handler_delegated_user');
    t.frmEl.change_approval_delegated_user = t.frm.find('#change_approval_delegated_user');
    t.frmEl.procurement_approval_delegated_user = t.frm.find('#procurement_approval_delegated_user');
    var select2Opts = { width: "100%" };
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;

    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;

    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            first_name: {
                required: true,
                alpha_str: true
            },
            last_name: {
                required: true,
                alpha_str: true
            },
            location_id: {
                required: true,
                str_name: true
            },
            website: {
                url: true
            },
            gravatar: {
                email: true
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo( element.parent("div").parent('div'));
        }
    });

    t.btn.submit.on('click',  $.proxy(t.handlesubmit));
    t.userDropDowns = t.content.find(".delegation");
    t.documentPhase = new DocumentPhase(config);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#documents-tab') {
            $(".user-mdl-box").removeClass("user-mdl-box");
            $(".tab-footer").hide();
            t.documentPhase.load();
        }
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#user_detail') {
            $("#profileadd").addClass("user-mdl-box");
            $(".tab-footer").show();
        }
    });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#delegation_detail') {
            $("#profileadd").addClass("user-mdl-box");
            $(".tab-footer").show();
        }
    });
    t.documentUploadPhase = new DocumentUploadPhase($.extend({}, config, { tbl: t.documentPhase }));

    t.userDropDowns.select2($.extend({}, select2Opts, {
        ajax: {
            url: config.url.getUsersParameterBase,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    exclude_logged_user:1,
                    user_status: 1,
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: "Select user for delegation",
        templateResult: function(data) {
            if (!data) return $("<div>No data available</div>");
            var imgPaddingLeft = "25px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    }));

    t.loadDelegation = function(){
        if(typeof t.config.reqDelegation != 'undefined'){
            t.frmEl.request_approval_delegated_user.append(new Option(t.config.reqDelegation.text, t.config.reqDelegation.id, true, true));
        }
        if(typeof t.config.ticketDelegation != 'undefined'){
            t.frmEl.ticket_handler_delegated_user.append(new Option(t.config.ticketDelegation.text, t.config.ticketDelegation.id, true, true));
        }
        if(typeof t.config.changeDelegation != 'undefined'){
            t.frmEl.change_approval_delegated_user.append(new Option(t.config.changeDelegation.text, t.config.changeDelegation.id, true, true));
        }
        if(typeof t.config.procureDelegation != 'undefined'){
            t .frmEl.procurement_approval_delegated_user.append(new Option(t.config.procureDelegation.text, t.config.procureDelegation.id, true, true));
        }
    }
    t.loadDelegation();

    t.config.userDropdownFormat = function (s, imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + s.text + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + s.email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };
};

var Mail = function (config) {
    var t = this;
    t.config = config;

    t.content = $('.cls-content');
    t.frm = t.content.find('#mail');

    t.frmEl = {};
    t.frmEl.email = t.frm.find('#email');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;

    t.handlesubmit = function (e) {

        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;

    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            email: {
                required: true,
                email: true
            }
        }
    });

    t.frm.on('submit',  $.proxy(t.handlesubmit));
};

var DocumentPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find("#documents-tab");
    t.table = t.tab.find("#tblDocument");
    t.searchbox = t.tab.find(".searchbox");
    t.mdl = $(".content");
    t.mdl.remarks = t.mdl.find("#remarksModal");
    t.mdl.remarks.body = t.mdl.remarks.find(".modal-body");

    t.tblHelpers = {
        actions: function() {
            return function(d) {
                var a = [];
                if (d.uploader_id == t.config.user_id.id) {
                    a.push("<button class='btn dtActbtn deleteButton' data-toggle='tooltip' data-placement='right' data-original-title='"+config.translations.Delete_Document+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                }
                a.push("<button class='btn dtActbtn tri-view' data-toggle='tooltip' data-placement='right' data-original-title='View' data-id=\"" + d.id + "\" data-type='user'><i class=\"fa fa-eye\"></i></button>");
                a.push("<a href=\"" + t.config.url.document_download + "/" + d.file_name + "\" class='btn dtActbtn' data-placement='right' download data-toggle='tooltip' data-original-title='"+config.translations.Download_Document+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                return '<div class="popup-toolbox checkselect" style="display: inline-block;">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            };
        }
    };

    t.deleteDocument = function(e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.document_delete;
        var data = {
            'msg':config.translations.something_went_wrong,
        };
        var send_data = {
            "asset_id": t.config.user_id.id,
            "asset_type": "user",
            "id": docId,
            "_token": t.config.token
        }
        sweetAlertPost(config.translations.delete_document,'warning',  t.httpPostPath, t.dTbl.ajax, data, send_data);
    };

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
            autoWidth: false,
            aoColumnDefs: [{
                targets: 0,
                bSortable: false,
                render: t.tblHelpers.actions()
            }],
            order: [
                [2, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.documents,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.user_id.id;
                    d.asset_type = "user";
                    d.search = { value: t.config.search || "" };
                    return d;
                }
            },
            columns: [
                {data: 'a'},
                {data: 'a.org_name'},
                {data: 'a.created_at_format'},
                {data: 'a.note',
                    render: function(data, type, row) {
                        if (!data) {
                            return '';
                        }
                        data = String(data);
    
                        if (data.length > 50) {
                            var truncated = truncateHtml(data, 50);
                            return truncated+ '<a class="read-more" data-full-text="' + escapeHtml(data) + '">...Read More</a>';
                        } else {
                            return  data ;
                        }
                    }
                }
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();
                $("#tblDocument_filter input").off(".DT");
                var searchBox = '<div class="input-group table-search-btns">'+
                '<input type="text" class="form-control plain-search" id="searchbox" placeholder="'+t.config.translations.press_enter_with_Search+'" />'+
                '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="left" data-original-title="' + t.config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>';
                searchBox += '<span class="input-group-addon  btn-reload-list" id="btn_reload" data-toggle="tooltip" data-placement="left" data-original-title="' + t.config.translations.Reload + '"><i class="ps-icon fa fa-refresh"></i></span>';
                searchBox += '<span id="btn_upload_document" class=" input-group-addon btn-upload-document" data-toggle="tooltip" data-placement="left" data-original-title="' + config.translations.Upload_Document + '"><i class="ps-icon fa fa-upload"></i></span>';
                searchBox += '</div>';
                $("#tblDocument_wrapper").removeClass("form-inline");
                $(searchBox).insertBefore("#tblDocument_filter");
                $("#tblDocument_filter").remove();
                t.table.parent().addClass('table-responsive');
                $("#tblDocument_length").find("select").select2();
    
                $("form").on("submit", function(e) {
                    if ($(document.activeElement).is("#searchbox")) {
                        e.preventDefault();
                    }
                });
    
                $("#searchbox").on("keyup", function(e) {
                    if (e.keyCode === 13) {
                        var v = $(this).val().trim();
                        if (!v || v.length === 0) {
                            t.config.search = "";
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        t.config.search = v;
                        t.reload();
                    }
                });
                $(".content").on("click", ".read-more", function(e) {
                    e.preventDefault();
                    var fullText = $(this).data("full-text");
                    $(t.mdl.remarks.body).html(fullText);
                    $(t.mdl.remarks).modal("show");
                });
            }
        });
    };

    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    function escapeHtml(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
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

    // $("#searchForm").on("submit", function (e) {
    //     t.search(e); // Trigger the custom search logic
    // });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $('#tblDocument_wrapper .plain-search').val().trim();
            // console.log(v);
            if (!v || v.length === 0) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.reload();
        }
    };


    t.attachmentView = function(e) {
        e.preventDefault();
        window.open(t.config.url.attachment_view + "/" + $(this).attr("data-id") + "?type=" + $(this).attr("data-type"), '_blank');
    };
    t.tab.on("click", ".tri-view", $.proxy(t.attachmentView))
    // $(".deleteButton").on("click", t.deleteDocument);
    $(document).on('click', '.deleteButton', t.deleteDocument);
    t.tab.on("click", "#btn_reload", $.proxy(t.reload));
    t.tab.on('click', '.btn-searchbox',$.proxy(t.search));
};

var DocumentUploadPhase = function(config) {
    var t = this;
    t.config = config;
    t.httpCall = true;
    t.httpPostPath = "";
    t.mdl = $("section.content").find("#document-mdl");
    t.mdl.title = t.mdl.find('.modal-title');
    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');
    t.mdl.frm = t.mdl.find("#document-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.token = t.mdl.frm.find("input[name='_token']");
    t.mdl.frmEl.note = t.mdl.frm.find("#note");
    t.mdl.frmEl.asset_id = t.mdl.frm.find("input[name='asset_id']");
    t.mdl.frmEl.asset_type = t.mdl.frm.find("input[name='asset_type']");

    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        // t.frmUploadValidator.resetForm();
    };

    t.frmUploadValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            note: {
                remarks: true,
            },
            document: {
                required: true,
                extension: "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
                filesize: 2000000
            }
        },
        messages: {
            document: {
                required: "Please upload a document.",
                extension: "Invalid file extension",
                filesize: "File size must be less than 2MB."
            }
        }
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    t.addDocument = function(e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.document_upload;
        t.mdl.title.html(config.translations.New_Document_Upload);
        t.mdl.btnSubmit.text("Save");
        t.mdl.frmEl.token.val(t.config.token);                                                                                                                                                                                                                                  
        t.mdl.frmEl.asset_id.val(t.config.user_id.id);
        t.mdl.frmEl.asset_type.val("user");
        t.mdl.modal("show");
    };

    t.handleSubmit = function(e) {
        e.preventDefault();

        if (t.frmUploadValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                t.mdl.btnSubmit.prop('disabled', true); 
            },
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    if (typeof t.config.tbl !== "undefined") {
                        t.config.tbl.dTbl.ajax.reload();
                    }
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            alert(config.translations.something_went_wrong);
        });
        http.always(function() {
            t.httpCall = true;
            t.mdl.btnSubmit.prop('disabled', false);
        });
    };

    $("section.content").on("click", "#btn_upload_document", $.proxy(t.addDocument));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};
