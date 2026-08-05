var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#main-schedule-wrapper");
    t.table = t.content.find("#mytable");
    t.pageLength = t.content.find(".userModulePageLenth");
    t.searchInput = t.content.find(".plain-search");
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    t.searchTimer = null;

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.httpCall = true;
    t.httpPostPath = "";
    t.action_type = "";
    t.data = {
        problem_categories: [],
        sub_categories: []
    };

    t.offListen = false;
    t.btn = {};
    t.btn.search = t.content.find(".btn-search-mail-list");
    t.btn.add = t.content.find(".btn-add-mail");
    t.btn.export = t.content.find(".btn-download");
    t.btn.reload = t.content.find(".btn-reload-list");
    t.mdl = t.content.find("#scheduleModal");

    //t.page = $("#page_boxed");
    //t.mdl = t.page.find("#scheduleModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#schedule-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.forAction = t.mdl.frm.find("#for_action");
    t.mdl.frmEl.companyId = t.mdl.frm.find("#company_id");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.problemCategoryId = t.mdl.frm.find("#problem_category_id");
    t.mdl.frmEl.subCategoryId = t.mdl.frm.find("#sub_category_id");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.priorityId = t.mdl.frm.find("#priority_id");
    t.mdl.frmEl.deviceId = t.mdl.frm.find("#device_id");
    t.mdl.frmEl.ac_email_id = t.mdl.frm.find("#ac_email_id");
    t.mdl.frmEl.tat = t.mdl.frm.find("#tat");
    t.mdl.frmEl.seatNo = t.mdl.frm.find("#seat_no");
    t.mdl.frmEl.creatorId = t.mdl.frm.find("#creator_id");
    t.mdl.frmEl.subject = t.mdl.frm.find("#subject");
    t.mdl.frmEl.content = t.mdl.frm.find("#content");
    t.mdl.btn = {};
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");

    t.mdl.modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    /* creator info */
    t.creatorinfo = {};
    t.creatorinfo.wrapper = t.mdl.find("#creator-info");
    t.creatorinfo.fullname = t.creatorinfo.wrapper.find('#fullname');
    t.creatorinfo.username = t.creatorinfo.wrapper.find('#username');
    t.creatorinfo.emp_code = t.creatorinfo.wrapper.find('#emp_code');
    t.creatorinfo.job_title = t.creatorinfo.wrapper.find('#job_title');
    t.creatorinfo.location_name = t.creatorinfo.wrapper.find('#location_name');
    t.creatorinfo.mobile = t.creatorinfo.wrapper.find('#mobile');
    t.creatorinfo.email = t.creatorinfo.wrapper.find('#email');
    t.creatorinfo.company = t.creatorinfo.wrapper.find('#company');
    t.creatorinfo.profile = t.creatorinfo.wrapper.find('#profile');
    t.creatorinfo.reload = function(e) {
        t.mdl.frmEl.deviceId.val("").trigger("change");
        if (typeof e !== "undefined") e.preventDefault();
        var u = t.mdl.frmEl.creatorId.val();
        if (!u) {
            return;
        }
        $.get(t.config.url.user_basic_info + "/" + u + "/true", function(d) {
            if (typeof d !== "undefined" && d.status === "success") {
                t.creatorinfo.fullname.html(d.data.fullname);
                t.creatorinfo.username.html(d.data.username);
                t.creatorinfo.emp_code.html(d.data.employee_code);
                t.creatorinfo.job_title.html(d.data.jobtitle);
                t.creatorinfo.location_name.html(d.data.location);
                t.creatorinfo.mobile.html(d.data.phone);
                t.creatorinfo.email.html(d.data.email);
                if(t.config.client == 'ril' || t.config.client == 'rolepermission'){
                    t.mdl.frmEl.seatNo.val(d.data.seat_no);
                }
                t.creatorinfo.company.html(d.data.company);
                t.creatorinfo.profile.attr("src", d.data.profile);
            }
        });
    };

    /* attachment */
    t.attachment = t.mdl.find("#attachments");
    t.attachment_dropper_cover = t.mdl.find("#attachment-dropper-cover");
    t.attachment_dropper = t.mdl.find("#attachment-dropper");
    if (window.AMGDragDrop) {
        window.AMGDragDrop.initAll(t.mdl[0]);
    }
    t.uploader = t.attachment_dropper_cover.length ? t.attachment_dropper_cover[0].AMGDragDropUploader : null;
    t.attachment.removeAttach = function(e) {
        e.preventDefault();
        var attachId = $(this).closest(".attach");
        var p = $(this).siblings("p");
        $.post(t.config.url.schedular_attachment_remove, { "_token": t.config.token, "id": attachId.attr("data-id"), "name": p.text() }, function(d) {});
        attachId.fadeOut("slow").remove();
    };

    t.prepareModalFieldLayout = function(form) {
        if (!form || !form.length) {
            return;
        }

        form.find(".input-group").each(function() {
            var group = $(this);
            var field = group.closest(".amg-form-field");

            if (!field.length) {
                return;
            }

            if (field.find(".input-group").length > 1 && group.parent(".flex-grow-1").length) {
                if (!group.siblings(".amg-form-error-wrap").length) {
                    group.after('<div class="amg-form-error-wrap ms-0 w-100"></div>');
                }
                return;
            }

            field.addClass("amg-form-field-row");
            if (!field.children(".amg-form-error-wrap").length) {
                field.append('<div class="amg-form-error-wrap"></div>');
            }
        });

        form.find("textarea.summernote").each(function() {
            $(this).closest(".amg-form-field").addClass("amg-form-field-row");
        });
    };

    t.getModalErrorWrap = function(element) {
        var groupedWrap = element.closest(".flex-grow-1").children(".amg-form-error-wrap").first();
        var row;
        var wrap;

        if (groupedWrap.length) {
            return groupedWrap;
        }

        row = element.closest(".amg-form-field-row");
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

    t.updateValidationState = function(element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");
        var noteEditor = element.next(".note-editor");

        if (!noteEditor.length) {
            noteEditor = element.closest(".amg-form-field").find(".note-editor").first();
        }

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }

        if (noteEditor.length) {
            noteEditor.toggleClass("amg-form-invalid", !!hasError);
        }
    };

    t.showContentError = function(message) {
        var errorWrap = t.getModalErrorWrap(t.mdl.frmEl.content);

        if (errorWrap.length) {
            errorWrap.empty().append(
                $("<label>", {
                    id: "content-error",
                    "class": "amg-form-error error",
                    "for": "content"
                }).text(message)
            );
        }
        t.updateValidationState(t.mdl.frmEl.content, true);
    };

    t.clearContentError = function() {
        var errorWrap = t.getModalErrorWrap(t.mdl.frmEl.content);

        if (errorWrap.length) {
            errorWrap.empty();
        }
        t.updateValidationState(t.mdl.frmEl.content, false);
    };

    t.prepareModalFieldLayout(t.mdl.frm);

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible):not(#content)",
        errorElement: "label",
        errorClass: "amg-form-error error",
        rules: {
            company_id: {
                required: true,
                str_name: true
            },
            department_id: {
                required: true,
                str_name: true
            },
            creator_id:{
                required:true,
                str_name:true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            subject: {
                maxlength: 255,
                required: true,
                acceptable_spcl_chr: true
            },
            priority_id: {
                required: true
            },
            tat: {
                required: true
            },
            content: {
                summernote: true
            },
            monthly_date: {
                required: true,
                number: true,
                max:31
            },
            yearly_day: {
                required: true,
                number: true,
                max:31
            },
            seat_no: {
               clean_text_only: true,
            },
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);
            if (element.attr("name") === "monthly_date") {
                error.appendTo(".monthly-date-error");
                return;
            }

            if (errorWrap.length) {
                error.appendTo(errorWrap.empty());
            } else if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function(element) {
            $(element).addClass("is-invalid");
            t.updateValidationState($(element), true);
        },
        unhighlight: function(element) {
            $(element).removeClass("is-invalid");
            t.updateValidationState($(element), false);
        },
        success: function(label, element) {
            $(element).removeClass("is-invalid");
            t.updateValidationState($(element), false);
            $(label).remove();
        }
    });

    t.fillAcEmailId = function() {
        if (typeof t.config.ac_email_accounts != "undefined" && Array.isArray(t.config.ac_email_accounts) == true && t.config.ac_email_accounts.length > 0) {
            t.mdl.frmEl.ac_email_id.empty().append(new Option(config.translations.select_reply_to_account, ""));
            $.each(t.config.ac_email_accounts, function(i, v) {
                t.mdl.frmEl.ac_email_id.append(new Option(v.text, v.id));
            });
            t.mdl.frmEl.ac_email_id.closest(".row").show();
            t.mdl.frmEl.ac_email_id.trigger("change");
        } else {
            t.mdl.frmEl.ac_email_id.empty().closest(".row").hide();
        }
    };

    // refill the problem category
    t.refillProblemCategory = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.problem_categories = [];
        t.mdl.frmEl.problemCategoryId.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.mdl.frmEl.departmentId.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function(data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.problem_categories = data.data;
                    $.each(data.data, function(i, v) {
                        t.mdl.frmEl.problemCategoryId.append(new Option(v.name, v.id));
                    });
                }
            }).always(function() {
                t.mdl.frmEl.problemCategoryId.trigger("change");
            });
        } else {
            t.mdl.frmEl.problemCategoryId.trigger("change");
        }
    };

    // refill the sub category
    t.refillSubCategory = function(e) {
        t.mdl.frmEl.subCategoryId.empty();
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.sub_categories = [];
        //t.mdl.frmEl.subCategoryId.empty().append(new Option("Select Sub Category", ""));
        var type_val = parseInt($.trim(t.mdl.frmEl.problemCategoryId.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
                                t.mdl.frmEl.subCategoryId.append(new Option(k.name, k.id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }
        t.updateSubCategoryVisibility();
        t.mdl.frmEl.subCategoryId.trigger("change");
    };

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibility = function() {
        if (t.data.sub_categories.length > 0) {
            t.mdl.frmEl.subCategoryId.rules("add", {
                required: true,
                str_name: true
            });
            t.mdl.frmEl.subCategoryIdCvr.show();
        } else {
            t.mdl.frmEl.subCategoryId.rules("remove");
            t.mdl.frmEl.subCategoryIdCvr.hide();
        }
    };

    // choose the priority and tat by problem type setup
    t.fillSla = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.mdl.frmEl.problemCategoryId.val();
        var sub_category_id = t.mdl.frmEl.subCategoryId.val();
        var priority = t.mdl.frmEl.priorityId.val();
        var tat = t.mdl.frmEl.tat.val()
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.sub_categories) == true && t.data.sub_categories.length) {
            $.each(t.data.sub_categories, function(i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat) && t.action_type == "add") {
                        t.offListen = true;
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                        t.mdl.frmEl.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        if(priority && priority > 0 && t.editInititalFlag){
                            t.mdl.frmEl.priorityId.val(priority).trigger("change");
                            t.mdl.frmEl.tat.val(tat);
                            t.editInititalFlag = false
                        }else{
                            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                            t.mdl.frmEl.tat.val(k.tat);
                        }
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.problem_categories != "undefined" && t.data.problem_categories.length) {
            $.each(t.data.problem_categories, function(i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat) && t.action_type == "add") {
                        t.offListen = true;
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                        t.mdl.frmEl.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        if(priority && priority > 0 && t.editInititalFlag){
                            t.mdl.frmEl.priorityId.val(priority).trigger("change");
                            t.mdl.frmEl.tat.val(tat);
                        }else{
                            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                            t.mdl.frmEl.tat.val(k.tat);
                        }
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.mdl.frmEl.priorityId.val("").trigger("change");
            t.mdl.frmEl.tat.val(0);
            t.offListen = false;
        }
    };

    // refill the priority dropdown 
    t.refillPriority = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.mdl.frmEl.priorityId.empty().append(new Option(config.translations.select_priority, ""));
        //t.mdl.frmEl.tat.val("");
        $.each(t.config.priorities, function(i, k) {
            t.mdl.frmEl.priorityId.append(new Option(k.name, k.id));
        });
        t.mdl.frmEl.priorityId.trigger("change");
    };

    // to fill the tat by default priority service time
    t.refillTat = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.mdl.frmEl.priorityId.val();
        var val = 0;
        $.each(t.config.priorities, function(i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.mdl.frmEl.tat.val(val);
    }

    t.fillDepartment = function() {
        if (typeof t.config.departments != "undefined" && Array.isArray(t.config.departments) == true && t.config.departments.length > 0) {
            t.mdl.frmEl.departmentId.empty();
            $.each(t.config.departments, function(i, v) {
                // console.log(i,v);
                t.mdl.frmEl.departmentId.append(new Option(v.name, v.id));
            });
            t.mdl.frmEl.departmentId.closest(".row").show();
            t.mdl.frmEl.departmentId.val(null).trigger("change");
        }
    };

    t.icons = {
        edit: function() {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function() {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        }
    };

    t.escapeHtml = function(value) {
        if (value === null || value === undefined) return "";
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.isFilledValue = function(value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.safeDisplayValue = function(value, fallback) {
        return t.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.getUserInitials = function(name) {
        var words = String(t.safeDisplayValue(name, "")).split(/\s+/).filter(Boolean);
        if (!words.length) return "NA";
        if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    };

    t.getAvatarHtml = function(name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";
        var safeName = t.escapeHtml(t.safeDisplayValue(name, "User"));

        if (t.isFilledValue(imageUrl)) {
            return '<img src="' + t.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + t.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + t.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + t.escapeHtml(t.getUserInitials(name)) + '</span>';
    };

    t.getIcon = function(key) {
        return t.icons[key] ? t.icons[key]() : "";
    };

    t.quickActionButtonHtml = function(label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            t.escapeHtml(extraClass || ""),
            " ",
            cls,
            " ",
            '" data-bs-toggle="tooltip" data-bs-placement="top"',
            '" data-id="',
            t.escapeHtml(id),
            '" title="',
            t.escapeHtml(label),
            '" aria-label="',
            t.escapeHtml(label),
            '">',
            t.getIcon(iconKey),
            "</button>"
        ].join("");
    };

    t.renderActionsCell = function(record, type) {
        var actions = [];

        if (type !== "display") {
            return record && record.id ? record.id : "";
        }

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        if(jQuery.inArray("SchedularEdit", t.config.permissions) !== -1) {
            actions.push(t.quickActionButtonHtml(config.translations.Edit || "Edit", "dtActEdit", record.id, "edit"));
        }

        if(jQuery.inArray("SchedularDelete", t.config.permissions) !== -1) {
            actions.push(t.quickActionButtonHtml(config.translations.Delete || "Delete", "dtActDel", record.id, "delete", "is-delete"));
        }

        if (!actions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions role-list-actions justify-content-start">',
            actions.join(""),
            "</div>"
        ].join("");
    };
    t.scheduleTableLayoutSync = function() {
        window.setTimeout(function() {
            if (!t.dTbl) {
                return;
            }

            try {
                t.dTbl.columns.adjust();

                if (typeof t.dTbl.fixedColumns === "function") {
                    var fixedColumnsApi = t.dTbl.fixedColumns();

                    if (fixedColumnsApi && typeof fixedColumnsApi.relayout === "function") {
                        fixedColumnsApi.relayout();
                    } else if (fixedColumnsApi && typeof fixedColumnsApi.update === "function") {
                        fixedColumnsApi.update();
                    }
                }
            } catch (err) {
                console.warn("Schedular table relayout skipped:", err);
            }
        }, 0);
    };
    t.syncTableMetaVisibility = function() {
        var wrapper;
        var info;
        var paginate;
        var records = 0;

        if (!t.dTbl) {
            return;
        }

        wrapper = t.table.closest(".dataTables_wrapper");
        info = wrapper.find(".dataTables_info");
        paginate = wrapper.find(".dataTables_paginate");
        records = t.dTbl.page.info().recordsDisplay || 0;

        info.toggle(records > 0);
        paginate.toggle(records > 0);
    };
    
    t.dTbl = t.table.DataTable({
        language: {
            info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
            infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
            emptyTable: t.config.datatable_translations.empty_result,
            zeroRecords: t.config.datatable_translations.empty_result,
            infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
            paginate: {
                previous: t.config.datatable_translations.prev,
                next: t.config.datatable_translations.next
            }
        },
        autoWidth: false,
        order: [
            [10, 'desc']
        ],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        scrollX: true,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        lengthChange:false,
        searching:false,
        
        ajax: {
            url: t.config.url.shedular_list,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $.trim(t.searchInput.val() || "");
            },
            error: function (reason) {
                location.reload();
            }
        },
       fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        dom: "<'dt-top'<'left'f><'right'l>>tr<'dt-bottom'<'left'i><'right'p>>",
        columns: [
            { data: 'a.company_name' },
            { data: 'a.dept_name' },
            { data: 'a.username' },
            { data: 'a.cat_name' },
            { data: 'a.sub_cat_name' },
            { data: 'a.priority_name' },
            { data: 'a.tat' },
            { data: 'a.device_name' },
            { data: 'a.action_data' },
            { data: 'a.subject' },
            {
            data: 'a.updated_at_display',
            defaultContent: "",
            className:"amg-table-col-144"
            },
            {
            data: 'a',
            orderable: false,
            searchable: false,
            width: "110px",
            className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
            render: function(data, type, row) {
                return t.renderActionsCell(row.a || {}, type);
            }
            },
        ],
        fnInitComplete: function(oSettings, json) {
            if (t.pageLength.length) {
                t.pageLength.val(String(parseInt(t.pageLength.val(), 10) || 10));
            }
            if(jQuery.inArray("SchedularEdit", t.config.permissions) == -1 && jQuery.inArray("SchedularDelete", t.config.permissions) == -1) {
                t.dTbl.column(11).visible(false);
            }
            t.syncTableMetaVisibility();
            t.scheduleTableLayoutSync();
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
            t.syncTableMetaVisibility();
            t.scheduleTableLayoutSync();
        }
    });
    t.handleSearchInput = function(e) {
        if (e.keyCode == 13) {
            window.clearTimeout(t.searchTimer);
            t.searchTimer = window.setTimeout(function() {
                t.dTbl.ajax.reload();
            }, 300);
        }
    };
    t.searchNow = function(e) {
        if (e) e.preventDefault();
        window.clearTimeout(t.searchTimer);
        t.dTbl.ajax.reload();
    };
    t.tableSearch = function(e) {
        return t.searchNow(e);
    };
    t.export = function(e) {
        e.preventDefault();
        var v = $.trim(t.searchInput.val());
        window.location = t.config.url.download_url + btoa(JSON.stringify(v)) ;
    }
    t.showDummyModal = function(e) {
        if (e) e.preventDefault();

        if (!t.mdl.length) {
            return;
        }

        if (t.mdl.title.length) {
            t.mdl.title.text("Add Schedular");
        }

        t.syncScheduleFrequencyPane();

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(t.mdl[0]).show();
        } else {
            t.mdl.modal("show");
        }
    };

    t.setScheduleFrequencyPane = function(targetSelector) {
        var panes = t.mdl.find("#daily, #weekly, #monthly, #yearly");
        var activePane;

        panes.removeClass("active show");

        activePane = targetSelector ? t.mdl.find(targetSelector).first() : $();
        if (!activePane.length) {
            activePane = t.mdl.find("#daily");
        }

        activePane.addClass("active show");
    };

    t.syncScheduleFrequencyPane = function() {
        var checkedOption = t.mdl.find("input[name='inlineRadioOptions']:checked").first();
        var targetSelector = checkedOption.data("target");

        t.setScheduleFrequencyPane(targetSelector);
    };

    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        t.mdl.frmEl.id.val("");
        //t.mdl.frmEl.forAction.val("");
        t.mdl.frmEl.companyId.val("").empty().trigger("change");
        t.mdl.frmEl.departmentId.val("").empty().trigger("change");
        t.mdl.frmEl.problemCategoryId.val("").empty().trigger("change");
        t.mdl.frmEl.subCategoryId.val("").empty().trigger("change");
        t.mdl.frmEl.priorityId.val("").trigger("change");
        t.mdl.frmEl.tat.val("").empty().trigger("change");
        t.mdl.frmEl.creatorId.val("").empty().trigger("change");
        t.mdl.frmEl.subject.val("");
        t.mdl.frmEl.seatNo.val("");
        t.mdl.frmEl.content.val('').summernote('code', '');
        t.mdl.frmEl.deviceId.empty().trigger("change");
        t.mdl.frmEl.ac_email_id.empty().trigger("change");
        t.updateSubCategoryVisibility();
        t.frmValidator.resetForm();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find("label.error").remove();
        t.mdl.frm.find(".is-invalid").removeClass("is-invalid");
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        if (t.uploader) {
            t.uploader.clear();
        } else {
            t.attachment.empty();
        }
        t.clearContentError();
        t.syncScheduleFrequencyPane();
    };

    t.loadForm = function(data, forAction) {
        t.resetFrm();
        t.refillPriority();
        t.fillAcEmailId();
        if (data[0].company_id != "" || data[0].company_id != null) {
            t.mdl.frmEl.companyId.val(data[0].company_id).empty().append(new Option(data[0].company_name, data[0].company_id, true, true)).trigger("change");
        }
        if (data[0].department_id != "" || data[0].department_id != null) {
            t.mdl.frmEl.departmentId.val(data[0].department_id).empty().append(new Option(data[0].dept_name, data[0].department_id, true, true)).trigger("change");
        }
        if (data[0].problem_category_id != "" || data[0].problem_category_id != null) {
            t.mdl.frmEl.problemCategoryId.val(data[0].problem_category_id).empty().append(new Option(data[0].cat_name, data[0].problem_category_id, true, true)).trigger("change");
        }
        if (data[0].sub_category_id != "" || data[0].sub_category_id != null) {
            t.mdl.frmEl.subCategoryId.val(data[0].sub_category_id).trigger("change");
        }
        if (data[0].priority_id != "" || data[0].priority_id != null) {
            t.mdl.frmEl.priorityId.val(data[0].priority_id).trigger("change");
        }
        if (data[0].ac_email_id != "" || data[0].ac_email_id != null) {
            t.mdl.frmEl.ac_email_id.val(data[0].ac_email_id).trigger("change");
        }
        if(data[0].device_id != null) {
            t.mdl.frmEl.deviceId.val(data[0].device_id).append(new Option(data[0].device_name, data[0].device_id, true, true)).trigger("change");
        } else {
            t.mdl.frmEl.deviceId.empty().append(new Option(config.translations.select_device, ""));
        }
        if (data[0].creator_id != "" || data[0].creator_id != null) {
            t.mdl.frmEl.creatorId.empty().append(new Option(data[0].displayName ? data[0].displayName : data[0].first_name + " " + data[0].last_name + " (" + data[0].username + ")",data[0].creator_id,true,true ));   
        }
        if (data[0].seat_no != "" || data[0].seat_no != null) {
            t.mdl.frmEl.seatNo.val(data[0].seat_no).trigger("change");
        }
        if (data[0].attachment_data != "" && data[0].attachment_data != null) {
            var fileObj = jQuery.parseJSON(data[0].attachment_data);
            $.each(fileObj, function(index, value) {
                if (t.uploader) {
                    t.uploader.addExisting(value);
                } else {
                    t.attachment.append('<div id="attach' + index + '" class="attach pad-top"><div class="bord-btm clearfix">' + '<p class="name pull-left">' + value.original_file_name + '</p><span style="cursor:pointer" class="remove-attach pull-right"><i class="fa fa-remove"></i> Remove</span></div>');
                    t.attachment.find("#attach" + index + " .name").text(decodeURIComponent(value.original_file_name));
                    t.attachment.find("#attach" + index).attr("data-id", value.id);
                }
            });

        }
        t.mdl.frm.find("input[name='subject']").val(data[0].subject);
        t.mdl.frm.find("input[name='tat']").val(data[0].tat);
        t.mdl.frm.find("textarea[name='content']").summernote('code', data[0].content);
        t.mdl.frmEl.id.val(data[0].id);
        t.data.id = data[0].id;
        var crondata = JSON.parse(data[0].action_expression);
        if (data[0].action_data == "daily_everyday") {
            $('#optDaily').prop("checked", true);
            $('#daily_all').prop("checked", true);
            $("#daily_hour").val(crondata.daily_hour);
            $("#daily_minute").val(crondata.daily_minute);
        }
        if (data[0].action_data == "daily_weekday") {
            $('#optDaily').prop("checked", true);
            $('#daily_weekday').prop("checked", true);
            $("#daily_hour").val(crondata.daily_hour);
            $("#daily_minute").val(crondata.daily_minute);
        }
        if (data[0].action_data == "weekly") {
            $('#optWeekly').prop("checked", true);
            $("#weekly_hour").val(crondata.weekly_hour);
            $("#weekly_minute").val(crondata.weekly_minute);
            var days = crondata.days.split(",");
            $.each(days, function(index, value) {
                $("input[value='" + value + "']").prop('checked', true);
            });
        }
        if (data[0].action_data == "monthly_once") {
            $('#monthly_once').prop("checked", true);
            $('#optMonthly').prop("checked", true);
            $("#monthly_hour").val(crondata.monthly_hour);
            $("#monthly_minute").val(crondata.monthly_minute);
            t.mdl.frm.find("input[name='monthly_date']").val(crondata.monthly_date);
            $("#monthly_numbers").val(crondata.monthly_numbers);
        }
        if (data[0].action_data == "monthly_condition") {
            $('#monthly_condition').prop("checked", true);
            $('#optMonthly').prop("checked", true);
            $("#monthly_hour").val(crondata.monthly_hour);
            $("#monthly_minute").val(crondata.monthly_minute);
            $("#monthly_week").val(crondata.monthly_week);
            $("#monthly_month").val(crondata.monthly_month);
            $("#monthly_day").val(crondata.monthly_day);
        }
        if (data[0].action_data == "yearly_once") {
            $('#yearly_once').prop("checked", true);
            $('#optYearly').prop("checked", true);
            $("#yearly_hour").val(crondata.yearly_hour);
            $("#yearly_minute").val(crondata.yearly_minute);
            t.mdl.frm.find("input[name='yearly_day']").val(crondata.yearly_day);
            $("#yearly_months").val(crondata.yearly_months);
        }
        if (data[0].action_data == "yearly_condition") {
            $('#yearly_condition').prop("checked", true);
            $('#optYearly').prop("checked", true);
            $("#yearly_hour").val(crondata.yearly_hour);
            $("#yearly_minute").val(crondata.yearly_minute);
            $("#yearly_months_week").val(crondata.yearly_months_week);
            $("#yearly_months_week_day").val(crondata.yearly_months_week_day);
            $("#yearly_months2").val(crondata.yearly_months2);
        }
        t.syncScheduleFrequencyPane();
        t.mdl.modal("show");
    };
    t.handleSubmit = function(e) {
        e.preventDefault();
        let s = '';
        if(t.action_type == 'add'){
            s = t.mdl.frmEl.content.val();
        }else{
            s = t.mdl.frmEl.content.val().replace(/<br\s*\/?>/gi, '').trim();
        }

        if (t.frmValidator.form() == false) {
            let s = t.mdl.frmEl.content.val();
            if (s == '') {
                t.showContentError('This field is required.');
            }
            return false;
        }

        if (s == '') {
            t.showContentError('This field is required.');
            return false;
        }else{
            t.clearContentError();
        }

        if (t.mdl.frmEl.priorityId.prop("disabled")) {
            let val = t.mdl.frmEl.priorityId.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.mdl.frm);
        }
        if (t.mdl.frmEl.tat.prop("disabled")) {
            let tatVal = t.mdl.frmEl.tat.val();
            console.log(tatVal);
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.mdl.frm);
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
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.dTbl.ajax.reload();
                    t.mdl.frmEl.content.val('').summernote('code', '');
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.addSchedular = function(e) {
        //alert("hi");
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.create_schedular;
        t.action_type = "add";
        t.mdl.title.html(config.translations.New_Schedular);
        t.mdl.btnSubmit.text(config.translations.create);
        if(t.config.client == 'ril' || t.config.client == 'rolepermission'){
            t.mdl.frmEl.seatNo.val(t.config.user.seat_no);
        }
        var default_company_id   = $("#config-company").val();
        var default_company_text = $("#config-company option:selected").text();
        if (default_company_id && default_company_id != 0) {
            let option = new Option(default_company_text,default_company_id, true, true);
            t.mdl.frmEl.companyId.empty().append(option).trigger("change");
        }else{
            t.mdl.frmEl.companyId.val("").trigger("change")
        }
        var http = $.get(t.config.url.save);
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.data.id = data.id;
                    t.mdl.frmEl.id.val(data.id);
                    t.mdl.frmEl.forAction.val("1");
                    t.mdl.frmEl.creatorId.empty().append(new Option(t.config.user.displayName ? t.config.user.displayName : t.config.user.first_name + " " + t.config.user.last_name + " (" + t.config.user.username + ")",t.config.user.id, true,true)).trigger("change");
                    t.fillAcEmailId();
                    t.refillPriority();
                    t.fillDepartment();
                    t.attachment.empty();
                    t.mdl.modal("show");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
    };

    t.editSchedular = function(e) {
        e.preventDefault();
        var shedId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.create_schedular;
        t.action_type = "edit";
        var http = $.get(t.config.url.edit + "/" + shedId);
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.frmEl.forAction.val("edit");
                    t.mdl.title.html(config.translations.Edit_Schedular);
                    t.mdl.btnSubmit.text(config.translations.save_changes);
                    t.mdl.modal("show");
                    t.editInititalFlag = true;
                    t.loadForm(data.edit_schedular, "edit");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': t.config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.deleteSchedular = function(e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + Id;
        sweetAlertConfirmation({
            message: config.translations.delete_record,
            onConfirm: function() {
                var http = $.get(t.httpPostPath);
                http.done(function(data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center','success',data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert('center','error',data);
                            t.refreshInfoTab();
                        }
                    }
                });
                http.fail(function() {
                    var data = {
                        'msg': t.config.translations.something_went_wrong,
                    }
                    sweetAlert('center','error',data);
                });
                http.always(function() {
                    t.httpCall = true;
                });
            }
        });
    };

    t.reload = function (e) {
        if (e) e.preventDefault();
        t.dTbl.ajax.reload(null, false);
    };

    var select2Opts = { width: "100%" };
    t.mdl.frmEl.creatorId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.creatorId.parent(),
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.mdl.frmEl.companyId.val()
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.enter_first_few_letter,
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function(data) {
            if (!data || !data.text) return data && data.text ? data.text : "";

            var name = t.safeDisplayValue(data.text, "-");
            var imageUrl = t.safeDisplayValue(data.img_path, "");
            var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar");

            return $(
                '<div class="d-flex align-items-center gap-2">' +
                    avatarHtml +
                    '<span>' + t.escapeHtml(name) + '</span>' +
                '</div>'
            );
        },
        escapeMarkup: function(markup) {
            return markup;
        }
    })).on("change", $.proxy(t.creatorinfo.reload));
    t.userDropdownFormat = function (s) {
        if (!s) return $("<div>No data</div>");
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + t.escapeHtml(s.text || "") + "</div>");
        }

        var name = t.safeDisplayValue(s.text, "-");
        var email = t.safeDisplayValue(s.email, "");
        var empNo = t.safeDisplayValue(s.employee_num, "");
        var imageUrl = t.safeDisplayValue(s.img_path, "");
        var tName = name.length > 35 ? name.substring(0, 35) + "..." : name;
        var tEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
        var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar");

        return $([
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">', t.escapeHtml(tName), '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' + t.escapeHtml(tEmail) + '</span>' : "",
                    empNo ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' + t.escapeHtml(empNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join(""));
    };

    t.mdl.frmEl.companyId.select2({
        dropdownParent: t.mdl.frmEl.companyId.parent(),
        ajax: {
            url: function (params) {
                return t.config.url.getCompanyByUserAccess;
            },
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page,
                }
            },
            processResults: function (data) {
                return {
                    results: data.results,
                }
            },
            cache: true
        },
        width: "100%",
        allowClear: true,
        placeholder: t.config.translations.company_placeholder
    }).on("change",function(){
        t.mdl.frmEl.departmentId.val("").empty().trigger("change");
        t.mdl.frmEl.creatorId.val("").empty().trigger("change");
    });

    t.mdl.frmEl.departmentId.select2({
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        ajax: {
            url: function (params) {
                return t.config.url.departments_with_company;
            },
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page,
                    company_id: t.mdl.frmEl.companyId.val(),
                }
            },
            processResults: function (data) {
                return {
                    results: data.results,
                }
            },
            cache: true
        },
        width: "100%",
        allowClear: true,
        placeholder: config.translations.connect
    }).on("change", $.proxy(t.refillProblemCategory));

    t.mdl.frmEl.problemCategoryId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.problemCategoryId.parent()})).on("change", $.proxy(t.fillSla));
    t.mdl.frmEl.problemCategoryId.on("change", $.proxy(t.refillSubCategory));
    t.mdl.frmEl.subCategoryId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.subCategoryId.parent()})).on("change", $.proxy(t.fillSla));
    t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.priorityId.parent()})).on("change", $.proxy(t.refillTat));
    t.mdl.frmEl.ac_email_id.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.frmEl.ac_email_id.parent()}));

    t.mdl.frmEl.deviceId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.deviceId.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.mdl.frmEl.creatorId.val(),
                    problemManagementApiCall: config.client == "ltts" ? true : false,
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_device,
        templateResult: function(s) {
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
        }
    }));

    t.mdl.frm.find("select").on("change", function() {
        var element = $(this);
        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    // t.attachment_dropper_cover.filedrop({
    //     fallback_id: 'attachment',
    //     fallback_dropzoneClick: true,
    //     url: t.config.url.schedular_attachment_add,
    //     paramname: "attachment",
    //     data: {
    //         "_token": t.config.token,
    //         "id": function() { return t.data.id; }
    //     },
    //     maxfiles: 5,
    //     maxfilesize: 6,
    //     error: function(err, file) {
    //         switch(err) {
    //             case 'TooManyFiles':
    //                 vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + config.translations.upload_file + '.</p></div>' });
    //                 break;
    //             default:
    //                 break;
    //         }
    //     },
    //     allowedfileextensions: ['.jpg', '.jpeg', '.png', '.gif', '.xls', '.xlsx', '.doc', '.docx', '.ppt', '.pdf', '.txt', '.msg', '.zip', '.psd', '.csv'],
    //     uploadFinished: function(i, file, response, time) {
    //         if (response.status === "success") {
    //             t.attachment.find("#attach" + i + " .name").text(decodeURIComponent(response.data.filename));
    //             t.attachment.find("#attach" + i).attr("data-id", response.data.id);
    //             t.attachment.find("#attach" + i + " .progress").fadeOut("slow");
    //         } else {
    //             t.attachment.find("#attach" + i + " .name").text(file.name + " upload failed");
    //             t.attachment.find("#attach" + i + " .upload_length").addClass("progress-bar-danger");
    //         }
    //     },
    //     progressUpdated: function(i, file, progress) {
    //         t.attachment.find("#attach" + i + " .upload_length").css("width", progress + "%");
    //     },
    //     beforeSend: function(file, i, done) {
    //         var matched = $('.count_img');
    //         if(matched.length <= 4) {
    //             if (t.attachment.find("#attach" + i).length) {
    //                 t.attachment.find("#attach" + i).attr("id", "attach" + (Math.random().toString()).substring(2, 15));
    //             }
    //             t.attachment.append('<div id="attach' + i + '" class="attach pad-top count_img"><div class="bord-btm clearfix">' + '<p class="name pull-left">' + file.name + '</p><span style="cursor:pointer" class="remove-attach pull-right"><i class="fa fa-remove"></i> Remove</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div>');
    //             done();
    //         } else {
    //             vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + config.translations.upload_file + '.</p></div>' });
    //         }
    //     },
    //     dragOver: function() {
    //         t.attachment_dropper.show();
    //     },
    //     drop: function() {
    //         t.attachment_dropper.show();
    //     }
    // });
    t.mdl.on("click", "#manual_file_trigger", function(e) {
        e.preventDefault();
    });
    t.mdl.on("click", ".remove-attach", $.proxy(t.attachment.removeAttach));
    t.mdl.frmEl.content.summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.note,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: styleTags,
        minHeight: 120,
        focus: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            },
            onChange: function() {
                if (t.frmValidator) {
                    t.frmValidator.element(t.mdl.frmEl.content[0]);
                }
            }
        }
    });
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.add.on("click", $.proxy(t.addSchedular));
    // t.btn.search.on("click", $.proxy(t.searchNow));
    t.btn.export.on("click", $.proxy(t.export));
    t.pageLength.on("change", function(e) {
        if (e) e.preventDefault();
        if (t.dTbl) {
            t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw(false);
        }
    });
    t.searchInput.on("input", $.proxy(t.handleSearchInput));
    t.searchInput.on("keydown", function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
            t.searchNow();
        }
    });
    t.table.on("click", ".dtActEdit", $.proxy(t.editSchedular));
    t.table.on("click", ".dtActDel", $.proxy(t.deleteSchedular));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
    t.mdl.on("change", "input[name='inlineRadioOptions']", function() {
        t.syncScheduleFrequencyPane();
    });
    $(window).off("resize.schedularTable").on("resize.schedularTable", function() {
        t.scheduleTableLayoutSync();
    });
    t.dTbl.ajax.reload();
};
