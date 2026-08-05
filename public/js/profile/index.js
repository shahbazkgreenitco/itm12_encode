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
                maxlength: 30,
                password: true
            },
            new_password: {
                required: true,
                minlength: 6,
                maxlength: 30,
                password: true
            },
            confirm_password: {
                required: true,
                minlength: 6,
                maxlength: 30,
                password: true
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent("div").parent('div'));
        }
    });
    $(document).on('click', '.toggle-password', function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#current_password");
        input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
    });
    $(document).on('click', '.toggle-password1', function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#new_password");
        input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
    });
    $(document).on('click', '.toggle-password2', function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#confirm_password");
        input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));

};

var LoginWithMicrosoft = function (config) {
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
        if (!isPwdVisible) {
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
            email: {
                required: false,
            }
        }
    });

    t.btn.submit.on('click', function () {
        t.loader.hide();
        t.frmValidator.settings.rules.password.required = true;
        t.frmValidator.settings.rules.email = {
            required: false,
            email: false
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
    });

    t.btn.otpSubmit.on('click', function () {
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

    t.validateUserName = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method: 'GET',
            url: t.config.url.validateUsername + "/" + t.frmEl.username.val(),
            success: function (data) {
                if (data.status == "success") {
                    t.btn.continue.hide();
                    t.btn.submit.show();
                    t.frm.find(".validateUserMsg").hide();
                    t.frmEl.username.removeClass("is-invalid");
                    t.frm.find(".passwordDiv").show();
                    t.btn.back.show();
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
            fail: function (data) {
                t.frm.find(".validateUserMsg").show();
            }
        })
    }
    t.validateMicrosoftUserName = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method: 'GET',
            url: t.config.url.validateUsername + "/" + t.frmEl.username.val(),
            success: function (data) {
                if (data.status == "success") {
                    window.location = "https://apps.greenitco.com/shyammetalics/login-with-office?username=" + t.frmEl.username.val();
                    // t.btn.submit.prop('disabled', false);
                } else {
                    t.frm.find(".validateUserMsg").html(data.msg);
                    t.frm.find(".validateUserMsg").show();
                }
            },
            fail: function (data) {
                t.frm.find(".validateUserMsg").show();
            }
        })
    }
    t.frm.on('submit', $.proxy(t.validateUserName));
    t.btn.continue.on("click", t.validateUserName);
    t.btn.btnMsLogin.on("click", t.validateMicrosoftUserName);
}

var LoginAdd = function (config) {
    var t = this;
    t.config = config;

    t.content = $('.cls-content');
    t.frm = t.content.find('#loginFrm');

    t.frmEl = {};
    t.frmEl.username = t.frm.find('#username');
    t.frmEl.password = t.frm.find('#password');
    t.frmEl.email = t.frm.find('#email');

    t.btn = {};
    t.btn.continue = t.frm.find('.btnContinue');
    t.btn.back = t.frm.find('.btnback');
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.otpSubmit = t.frm.find('#btnSubmitOtp');
    t.btn.clear = t.frm.find('#btnClear');
    t.loader = t.frm.find('#loader_img');
    t.httpCall = false;
    function trimInput(el) {
        el.val(el.val().trim());
    }
    t.frmEl.username.on('paste', function () {
        let el = $(this);
        setTimeout(() => trimInput(el), 0);
    });
    t.frmEl.password.on('paste', function () {
        let el = $(this);
        setTimeout(() => trimInput(el), 0);
    });
    t.frmEl.username.on('input', function () {
        $(this).val($(this).val().replace(/^\s+/, ''));
        $(this).removeClass('is-invalid');
        t.frm.find(".validateUserMsg").hide();
    });
    t.frmEl.password.on('input', function () {
        $(this).val($(this).val().replace(/^\s+/, ''));
    });
    t.frmEl.username.on('blur', function () {
        trimInput($(this));
    });
    t.frmEl.password.on('blur', function () {
        trimInput($(this));
    });
    t.handlesubmit = function (e) {
        const isPwdVisible = $('.passwordDiv').css('display') != 'none';
        if (!isPwdVisible) {
            return false;
        }

        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        t.btn.submit.prop('disabled', true);
        return true;
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: "error",
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },
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
            email: {
                required: false,
            }
        }
    });
    t.btn.otpSubmit.on('click', function () {
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
    t.btn.submit.on('click', function () {
        t.loader.hide();
        t.frmValidator.settings.rules.password.required = true;
        t.frmValidator.settings.rules.email = {
            required: false,
            email: false
        };
        t.frmValidator.element('#password');
        t.frmValidator.element('#email');
        
    });
    if (config.client == "ltts" || config.client == "tbsl") {
        $('#username').on('input', function () {
            var inputValue = $(this).val().trim();
            if (typeof config.impersonate_un !== 'undefined') {
                if (inputValue === config.impersonate_un) {
                    t.btn.otpSubmit.show();
                    t.frm.find(".emailDiv").show();
                } else {
                    t.btn.otpSubmit.hide();
                    t.frm.find(".emailDiv").hide();
                }
            }
        });
    }

    t.validateUserName = function (e) {
        trimInput(t.frmEl.username);
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        $.ajax({
            method: 'GET',
            url: t.config.url.validateUsername + "/" + t.frmEl.username.val(),
            success: function (data) {
                if (data.status == "success") {
                    t.btn.continue.hide();
                    t.btn.submit.show();
                    t.frm.find(".validateUserMsg").hide();
                    t.frm.find(".passwordDiv").show();
                    t.frmEl.username.prop("readonly", true);
                    toggleBackButton()
                    if (data.impersonate === true) {
                        t.btn.otpSubmit.show();
                        t.frm.find(".emailDiv").show();
                    }
                    // t.btn.submit.prop('disabled', false);
                } else {
                    t.frm.find(".validateUserMsg").html(data.msg);
                    t.frm.find('.validateUserMsg').css('display', 'block')
                    t.frm.find(".validateUserMsg").show();
                    t.frmEl.username.addClass("is-invalid");
                }
            },
            fail: function (data) {
                t.frm.find(".validateUserMsg").show();
                t.frmEl.username.addClass("is-invalid");
            }
        })
    }

    if (t.config.type_status == "danger") {
        t.frm.find(".passwordDiv").show();
    } else {
        t.frm.find(".passwordDiv").hide();
    }

    function toggleBackButton() {
        if ($('.passwordDiv').is(':visible')) {
            t.btn.back.removeClass('d-none');
            t.btn.continue.hide();
            t.btn.submit.show();
        } else {
            t.btn.back.addClass('d-none');
            t.btn.submit.hide();
            t.btn.continue.show();
            t.frmEl.username.prop("readonly", false);
        }
    }

    toggleBackButton();
    t.btn.back.on('click', function () {
        $('.passwordDiv').hide();
        toggleBackButton();
    });

    t.frm.on('submit', $.proxy(t.handlesubmit));
    t.btn.continue.off('click').on("click", t.validateUserName);
};

var ProfileAdd = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main.main-content');
    t.mdl = t.content.find('#edit-profile');
    t.frm = t.mdl.find('#profileForm');
    t.delegateFrm = t.content.find('#delegationForm');
    t.avatarInput = $('#profileImageInput');
    t.uploadBtn = $('#uploadProfileBtn');
    t.removeImageBtn = $('#remove_image');
    t.removeBtn = $('#removeProfileBtn');
    t.frmEl = {};
    t.delegatefrmEl = {};
    t.frmEl.first_name = t.frm.find('#first_name');
    t.frmEl.last_name = t.frm.find('#last_name');
    t.frmEl.location_id = t.frm.find('#location_id');
    t.frmEl.website = t.frm.find('#website');
    t.frmEl.gravatar = t.frm.find('#gravatar');
    t.delegatefrmEl.request_approval_delegated_user = t.delegateFrm.find('#request_approval_delegated_user');
    t.delegatefrmEl.ticket_handler_delegated_user = t.delegateFrm.find('#ticket_handler_delegated_user');
    t.delegatefrmEl.change_approval_delegated_user = t.delegateFrm.find('#change_approval_delegated_user');
    t.delegatefrmEl.procurement_approval_delegated_user = t.delegateFrm.find('#procurement_approval_delegated_user');
    var select2Opts = { width: "100%" };
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');
    t.btn.delegateSubmit = t.delegateFrm.find('#btnSubmitDelegation');
    t.httpCall = false;
    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;

    };

    t.handleDelegateSubmit = function (e) {
        e.preventDefault();
        $('#delegationForm .profile-injected').remove();

        // doing like this since in the controller, profile's data should also be present with delegation's data.
        $('#profileForm').serializeArray().forEach(function (field) {
            $('#delegationForm').append(
                $('<input>').attr({
                    type: 'hidden',
                    name: field.name,
                    value: field.value,
                    class: 'profile-injected'
                })
            );
        });
        $('#delegationForm')[0].submit();
    }

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        onkeyup: function (element, event) {
            this.element(element);
        },
        rules: {
            first_name: {
                required: true,
                alpha_str: true,
                maxlength: 100
            },
            last_name: {
                required: true,
                alpha_str: true,
                maxlength: 100
            },
            location_id: {
                required: true,
                str_name: true
            },
            website: {
                url: true,
                maxlength: 250
            },
            gravatar: {
                email: true
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent("div").parent('div'));
        }
    });

        t.uploadBtn.on('click', function (e) {
        e.preventDefault();
        t.avatarInput.trigger('click');
    });

    t.avatarInput.on('change', function () {

        var file = this.files[0];
        if (!file) return;

        var allowedExtensions = ['png', 'jpg', 'jpeg', 'bmp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();

        if ($.inArray(fileExtension, allowedExtensions) === -1) {
            Swal.fire({
                icon: 'error',
                title: config.translations.invalid_file,
                text: config.translations.allowed_avatar_formats
            });
            $(this).val('');
            return;
        }

        var maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: config.translations.file_too_large,
                text: config.translations.avatar_max_size
            });
            $(this).val('');
            return;
        }

        var formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', t.config.token);

        $.ajax({
            url: t.config.url.profileImage,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                t.uploadBtn.prop('disabled', true);
            },
            success: function (res) {
                if (res.status === 'success' && res.image_url) {
                    // Directly use URL returned from controller
                    var newImageUrl = res.image_url + '?t=' + new Date().getTime();
                    $('#profilePreviewImage').attr('src', newImageUrl);
                    // console.log("Image Updated To:", newImageUrl);
                    $('#remove_image').removeClass('d-none');
                    t.avatarInput.val('');
                }
                t.uploadBtn.prop('disabled', false);
            }
            ,
            error: function (res) {
                // console.log(res.responseJSON.message);
                // alert('Upload failed');
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: res.responseJSON.message || config.translations.upload_failed_default,
                    confirmButtonText: "OK",
                });
                t.uploadBtn.prop('disabled', false);
            }
        });

    });

    t.removeImageBtn.on("click", function(){
        let deleteUrl = config.url.deleteImage;

        Swal.fire({
            title: config.translations.are_you_delete || "Are you sure you want to delete the profile image?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: config.translations.modal_yes_button,
            cancelButtonText: config.translations.modal_cancel_button,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: "POST",
                    data: {
                        _token: config.token
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: config.translations.deleting,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    },
                    success: function (res) {
                        Swal.close();
                        if (res.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: config.translations.deleted,
                                text: res.message || config.translations.deleted_successfully,
                                confirmButtonText: "OK",
                            });
                            
                            $('#profilePreviewImage').attr('src', res.img);
                            $('#remove_image').addClass('d-none');
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: res.message || config.translations.deleted_fail,
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: config.translations.error,
                            text: config.translations.something_went_wrong,
                            confirmButtonText: "OK",
                        });
                    },
                });
            }
        });
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.btn.delegateSubmit.on('click', $.proxy(t.handleDelegateSubmit));
    t.userDropDowns = t.content.find(".delegation");
    t.documentPhase = new DocumentPhase(config);
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#documents') {
            $(".user-mdl-box").removeClass("user-mdl-box");
            $(".tab-footer").hide();
            t.documentPhase.load();
        }

        if ($(e.target).attr('href') === '#edit-profile') {
            $("#profileadd").addClass("user-mdl-box");
            $(".tab-footer").show();
        }

        if ($(e.target).attr('href') === '#delegation') {
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
                    exclude_logged_user: 1,
                    user_status: 1,
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_user_for_delegation,
        templateResult: function (data) {
            if (!data) return $(`<div>${config.translations.no_data_available}</div>`);
                var email = data.email == null ? "" : data.email;
                var name = t.safeDisplayValue(data.text, "-");
                var imageUrl = t.safeDisplayValue(data.img_path, "");
                var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");

                return $(
                    '<div class="d-flex align-items-center gap-2">' +
                        avatarHtml +
                        '<span>' + t.escapeHtml(name) + '</span>' +
                    '</div>'
                );
        },
    }));

    t.escapeHtml = function (text) {
        return String(text === null || text === undefined ? "" : text).replace(
            /[&<>"'`=\/]/g,
            function (s) {
                return entityMap[s];
            }
        );
    };

    t.safeDisplayValue = function (value, fallback) {
        return this.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.getAvatarHtml = function(name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";
        var safeName = t.escapeHtml(this.safeDisplayValue(name, "User"));

        if (this.isFilledValue(imageUrl)) {
            return '<img src="' + t.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + t.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + t.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + t.escapeHtml(this.getUserInitials(name)) + '</span>';
    }

    t.getUserInitials = function(name){
        let words = String(name || '').split(/\s+/).filter(Boolean);

        if(!words.length){
            return 'NA';
        }

        if(words.length === 1){
            return words[0].substring(0, 2).toUpperCase();
        }

        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
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

    t.loadDelegation = function () {
        if (typeof t.config.reqDelegation != 'undefined') {
            t.delegatefrmEl.request_approval_delegated_user.append(new Option(t.config.reqDelegation.text, t.config.reqDelegation.id, true, true));
        }
        if (typeof t.config.ticketDelegation != 'undefined') {
            t.delegatefrmEl.ticket_handler_delegated_user.append(new Option(t.config.ticketDelegation.text, t.config.ticketDelegation.id, true, true));
        }
        if (typeof t.config.changeDelegation != 'undefined') {
            t.delegatefrmEl.change_approval_delegated_user.append(new Option(t.config.changeDelegation.text, t.config.changeDelegation.id, true, true));
        }
        if (typeof t.config.procureDelegation != 'undefined') {
            t.delegatefrmEl.procurement_approval_delegated_user.append(new Option(t.config.procureDelegation.text, t.config.procureDelegation.id, true, true));
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
        if (t.httpCall) {
            e.preventDefault();
            return false;
        }
        t.httpCall = true;
        t.btn.submit.prop('disabled', true);
        $('#continueSpinner').show();
        $('#continueTxt').text('Sending...');
        t.frm.submit(); 
        return true;
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: "error",
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
        },       
        rules: {
            email: {
                required: true,
                // email: true
            }
        }
    });

    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};

var DocumentPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("main#mainContent").find("#documents");
    t.table = t.tab.find("#tblDocument");
    t.searchbox = t.tab.find(".searchbox");
    t.mdl = $(".content");
    t.mdl.remarks = t.mdl.find("#remarksModal");
    t.mdl.remarks.body = t.mdl.remarks.find(".modal-body");
    t.show_entries = $('#showSelect');

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                var a = [];
                if (d.uploader_id == t.config.user_id.id) {
                    a.push("<button class='btn dtActbtn deleteButton' data-toggle='tooltip' data-placement='right' data-original-title='" + config.translations.Delete_Document + "' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                }
                a.push("<button class='btn dtActbtn tri-view' data-toggle='tooltip' data-placement='right' data-original-title='View' data-id=\"" + d.id + "\" data-type='user'><i class=\"fa fa-eye\"></i></button>");
                a.push("<a href=\"" + t.config.url.document_download + "/" + d.file_name + "\" class='btn dtActbtn' data-placement='right' download data-toggle='tooltip' data-original-title='" + config.translations.Download_Document + "' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                return '<div class="popup-toolbox checkselect" style="display: inline-block;">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            };
        }
    };

    t.deleteDocument = function (e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.document_delete;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        var send_data = {
            "asset_id": t.config.user_id.id,
            "asset_type": "user",
            "id": docId,
            "_token": t.config.token
        }
        sweetAlertPost(config.translations.delete_document, 'warning', t.httpPostPath, t.dTbl.ajax, data, send_data);
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            lengthChange: false,
            responsive: false,
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: {
                rightColumns: 1
            },
            dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
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
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `<span class="b1-text">${id ?? ''}</span>`;
                    },
                },
                { data: 'a.org_name' },
                { data: 'a.created_at_format' },
                {
                    data: 'a.note',
                    render: function (data, type, row) {
                        if (!data) {
                            return '';
                        }
                        data = String(data);

                        if (data.length > 50) {
                            var truncated = truncateHtml(data, 50);
                            return truncated + '<a class="read-more" style="cursor: pointer; color: #00a1ff;" data-full-note="' + escapeHtml(data) + '">...Read More</a>';
                        } else {
                            return data;
                        }
                    }
                },
                {
                    data : "a",
                    sortable: false,
                    searchable: false,
                    render : function (d) {
                        let deleteButton = "";
                         if (d.uploader_id == t.config.user_id.id) {
                            deleteButton = `<button type="button" class="user-list-action-btn deleteButton" data-id="${d.id}" title="${config.translations.Delete_Document}" aria-label="${config.translations.Delete_Document}">
                            <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
                            </button>`;
                         }

                        let downloadButton = `<button type="button" class="user-list-action-btn" title="${config.translations.Download_Document}" aria-label="${config.translations.Download_Document}">
                            <a href="${config.url.document_download}/${d.file_name}" class="btn dtActbtn" data-bs-placement="right" data-bs-toggle="tooltip" data-id="${d.id}" data-original-title="${config.translations.Download_Document}" download="${d.org_name}">
                                <svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>
                            </a>
                        </button>`;

                        let viewButton = "";
                        var imageExtensions = ['png', 'gif', 'jpg', 'jpeg'];
                        if (imageExtensions.includes(d.file_extension.toLowerCase())) {
                            viewButton = `<button type="button" class="user-list-action-btn tri-view" data-id="${d.id}" data-type="user" title="${config.translations.view_document}" aria-label="${config.translations.view_document}">
                            <svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>
                            </button>`;
                        }

                        return `<div class="user-list-actions justify-content-start">${deleteButton} ${viewButton} ${downloadButton}</div>`
                    }
                },
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();
                t.table.parent().addClass('table-responsive');
                // $("#tblDocument_length").find("select").select2();

                $("form").on("submit", function (e) {
                    if ($(document.activeElement).is("#searchbox")) {
                        e.preventDefault();
                    }
                });

                $("#searchbox").on("keyup", function (e) {
                    if (e.keyCode === 13 || this.value.length == 0) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            t.config.search = "";
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        t.config.search = v;
                        t.reload();
                    }
                });
                $(".content").on("click", ".read-more", function (e) {
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

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    $('#tblDocument').on('click', '.read-more', function(e) {
        e.preventDefault();
        var fullNote = $(this).data('full-note'); 
        $('#fullNote').html(fullNote);           
        $('#noteModal').modal("show"); 
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $('#searchbox').validate_str_param();
            // console.log(v);
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.reload();
        }
    };

    t.buttonSearch = function(e) {
        e.preventDefault();
        var v = $("#searchbox").validate_str_param();
        if(v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.config.search = v;
        t.reload();
    };


    t.attachmentView = function (e) {
        e.preventDefault();
        window.open(t.config.url.attachment_view + "/" + $(this).attr("data-id") + "?type=" + $(this).attr("data-type"), '_blank');
    };
    t.tab.on("click", ".tri-view", $.proxy(t.attachmentView))
    // $(".deleteButton").on("click", t.deleteDocument);
    $(document).on('click', '.deleteButton', t.deleteDocument);
    t.tab.on("click", "#btn_reload", $.proxy(t.reload));
    t.tab.on('click', '.amg-list-searchbar__icon-documents', $.proxy(t.buttonSearch));
};

var DocumentUploadPhase = function (config) {
    var t = this;
    t.config = config;
    t.httpCall = true;
    t.httpPostPath = "";
    t.mdl = $("main#mainContent").find("#document-mdl");
    t.mdl.title = t.mdl.find('.modal-title');
    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');
    t.mdl.frm = t.mdl.find("#document-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.token = t.mdl.frm.find("input[name='_token']");
    t.mdl.frmEl.note = t.mdl.frm.find("#note");
    t.mdl.frmEl.asset_id = t.mdl.frm.find("input[name='asset_id']");
    t.mdl.frmEl.asset_type = t.mdl.frm.find("input[name='asset_type']");

    t.resetFrm = function () {
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

    t.addDocument = function (e) {
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

    t.handleSubmit = function (e) {
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
            beforeSend: function () {
                t.mdl.btnSubmit.prop('disabled', true);
            },
        });
        http.done(function (data) {
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
        http.fail(function () {
            alert(config.translations.something_went_wrong);
        });
        http.always(function () {
            t.httpCall = true;
            t.mdl.btnSubmit.prop('disabled', false);
        });
    };

    $("main#mainContent").on("click", "#btn_upload_document", $.proxy(t.addDocument));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};
