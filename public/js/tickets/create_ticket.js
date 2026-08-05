var CreateTicket = function (config) {
    var t = this;
    t.config = config;

    t.data = {
        problem_categories: [],
        sub_categories: []
    };
    t.enableUsbRequest = {};
    t.httpCall = true;
    var loadCustomForm = false;
    t.btnCreateTicket = $('#btnCreateTicket');
    t.mdl = $("#createTicket");
    t.mdl.frm = t.mdl.find("#ticket-mdl-frm");
    t.mdl.title = t.mdl.find("#createTicketTitle");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.tmp_id = t.mdl.frm.find("#tmp_id");
    t.mdl.frmEl.forAction = t.mdl.frm.find("#for_action");
    t.mdl.frmEl.company_id = t.mdl.frm.find("#company_id");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.creatorId = t.mdl.frm.find("#creator_id");
    t.mdl.frmEl.problemCategoryId = t.mdl.frm.find("#problem_category_id");
    t.mdl.frmEl.subCategoryId = t.mdl.frm.find("#sub_category_id");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.createTicketForOthers = t.mdl.frm.find("#create_ticket_for_others");
    t.mdl.frmEl.location_id = t.mdl.frm.find("#location_id");
    t.mdl.frmEl.internal_location_id = t.mdl.frm.find("#internal_location_id");
    t.mdl.frmEl.priorityId = t.mdl.frm.find("#priority_id");
    t.mdl.frmEl.tat = t.mdl.frm.find("#tat");
    t.mdl.frmEl.deviceId = t.mdl.frm.find("#device_id");
    t.mdl.frmEl.ac_email_id = t.mdl.frm.find("#ac_email_id");
    t.mdl.frmEl.created_via = t.mdl.frm.find("#created_via");
    t.mdl.frmEl.subject = t.mdl.frm.find("#subject");
    t.mdl.frmEl.seatNo = t.mdl.frm.find("#seat_no");
    t.mdl.frmEl.tags = t.mdl.frm.find("#tags");
    t.mdl.frmEl.description = t.mdl.frm.find("#descriptions");
    t.mdl.frmEl.description_info = t.mdl.frm.find("#description-info");
    t.mdl.frmEl.content = t.mdl.frm.find("#content");
    t.mdl.frmEl.follow_cc = t.mdl.frm.find("#follow_cc");
    t.mdl.frmEl.cc_emails = t.mdl.frm.find("#cc_emails");
    t.mdl.frmEl.getcc_email = t.mdl.frm.find('.cc_email');
    t.mdl.frmEl.form =t.mdl.frm.find("#form");
    t.mdl.frmEl.form_info =t.mdl.frm.find("#form-info");
    t.mdl.frmEl.request_form_view_url = t.mdl.frm.find("#request-form-view-url");
    t.mdl.frmEl.form_view_info =t.mdl.frm.find("#form-view-info");
    t.mdl.btn = {};
    t.mdl.btn.submit = t.mdl.find("#btnSubmit");
    t.mdl.btn.clear = t.mdl.find("#btnClear");
    t.attachment = t.mdl.find("#attachments");

    t.mdlServiceRequest = $('#mdl-servicerequest');
    t.frmServiceRequest = t.mdlServiceRequest.find('#requested_form');
    t.frmServiceRequest.el = {};
    t.frmServiceRequest.el.form_title = t.frmServiceRequest.find(".form_title");
    t.frmServiceRequest.el.form_id = t.frmServiceRequest.find("#form_id");
    t.frmServiceRequest.el.request_id = t.frmServiceRequest.find("#request_id");
    t.frmServiceRequest.el.tmp_id = t.frmServiceRequest.find("#tmp_id");
    t.frmServiceRequest.el.field_values = t.frmServiceRequest.find('#field_values');
    t.frmServiceRequest.el.field_form_required = t.frmServiceRequest.find('#field_form_required');
    t.frmServiceRequest.btnSubmit = t.mdlServiceRequest.find("#btnSubmit");
    t.frmServiceRequest.el.for_action = t.frmServiceRequest.find("#for_action");
    t.frmServiceRequest.el.dept_id = t.frmServiceRequest.find("#dept_id");
    t.frmServiceRequest.el.prob_id = t.frmServiceRequest.find("#prob_id");
    t.frmServiceRequest.el.user_id = t.frmServiceRequest.find("#user_id");
   
    t.kd_mdl = $("#mdl-kd-suggestion");
    t.kd_mdl.title = t.kd_mdl.find(".modal-title");
    
    t.kd_mdl.frmEl = {};
    t.kd_mdl.frmEl.kdSuggestionWrapper = t.kd_mdl.find('.kdSuggestionWrapper');
    t.kd_mdl.frmEl.relatedArticles = t.kd_mdl.find('.relatedArticles');

    $(document).on('click', '#btn-hide-all', function(e) {
        e.preventDefault();
        Swal.fire({
            title: t.config.translations.confirm_ticket,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText:t.config.translations.create,
            cancelButtonText: t.config.translations.close,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
    
                $('#mdl-kd-suggestion').modal('hide');
            } else {
                $('#mdl-kd-suggestion').modal('hide');
                $('#createTicket').modal('hide');
            }
        });
    });
   
    t.mdl.frmEl.content.summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.notes,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: styleTags,
        minHeight: 120,
        focus: true,
        callbacks: {
            onInit: function () {
                $(this).next('.note-editor').css('width', '100%').addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            },
            onChange: function () {
                if (t.frmValidator) {
                    t.frmValidator.element(t.mdl.frmEl.content[0]);
                }
            }
        }
    });

    var select2Opts = {
        width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    };

    t.mdl.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.company_id.parent(),
        placeholder: t.config.translations.select_company,
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {

                return {
                    search: p.term,
                    page: p.page || 1,
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text?.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    }))

    t.mdl.frmEl.location_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.location_id.parent(),
        placeholder: t.config.translations.select_location,
        ajax: {
            url: t.config.url.getCompanyWiseLocation,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.mdl.frmEl.company_id.val()
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text?.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    })).on("change", function (e) {
        t.getInternalPlace();
    });

    t.getInternalPlace = function (e, callback) {
        if (e && e.preventDefault) {
            e.preventDefault();
        }

        var location_ids = t.mdl.frmEl.location_id.val();
        if (!location_ids) {
            location_ids = [];
        } else if (!Array.isArray(location_ids)) {
            location_ids = [location_ids];
        }

        if (!location_ids.length) {
            t.mdl.frmEl.internal_location_id.empty().trigger("change");
            return;
        }

        $.get(t.config.url.getInternalPlaceByAjax + '/' + location_ids.join(','),
            {company_id: t.mdl.frmEl.company_id.val()}
        ).done(function (data) {
            t.mdl.frmEl.internal_location_id.empty();

            if (data?.results?.length) {
                $.each(data.results, function (i, v) {
                    var option = new Option(v.text, v.id.toString(), false, false);
                    t.mdl.frmEl.internal_location_id.append(option);
                });
            }

            t.mdl.frmEl.internal_location_id.trigger('change');

            if (typeof callback === "function") {
                callback();
            }
        });
    };

    t.mdl.frmEl.company_id.on("change", function () {
        let selectedCompanyId = t.mdl.frmEl.company_id.val();
        let user = t.config.user;
        let company_id = Number(t.mdl.frmEl.company_id.val());
        t.mdl.frmEl.priorityId.val("").trigger("change");
        t.mdl.frmEl.tat.val(0);
        if (t.config.user.limits && t.config.user.action_controls?.[company_id]?.create_for_others === 1) {
            t.mdl.frmEl.createTicketForOthers.removeClass('d-none');
            t.mdl.frmEl.creatorId.rules('add', { required: true });
        } else {
            var displayName = user.displayName || (user.first_name + " " + user.last_name + " (" + user.username + ")");
            let option = new Option(displayName, user.id, true, true);
            t.mdl.frmEl.creatorId.empty().append(option).trigger("change");
            t.mdl.frmEl.createTicketForOthers.addClass('d-none');
            t.mdl.frmEl.creatorId.rules('remove', 'required');
        }
        if (t.config.user.action_controls?.[company_id]?.ctrl_tat == 1) {
            t.mdl.frmEl.tat.prop('disabled', false);
        } else {
            t.mdl.frmEl.tat.prop('disabled', true);
        }
        if (t.config.user.action_controls?.[company_id]?.ctrl_priority == 1) {
            t.mdl.frmEl.priorityId.prop('disabled', false);
        } else {
            t.mdl.frmEl.priorityId.prop('disabled', true);
        }
        $('#other_location').addClass('d-none');
        if (user && user.company_id == selectedCompanyId) {
            var displayName = user.displayName || (user.first_name + " " + user.last_name + " (" + user.username + ")");
            let option = new Option(displayName, user.id, true, true);
            t.mdl.frmEl.creatorId.empty().append(option).trigger("change");
        } else {
            t.mdl.frmEl.creatorId.val(null).empty();
        }
        t.mdl.frmEl.departmentId.empty();
        t.mdl.frmEl.problemCategoryId.empty();
        t.mdl.frmEl.subCategoryId.empty();
        t.mdl.frmEl.creatorId.empty().trigger("change");
        t.mdl.frmEl.cc_emails.empty().trigger("change");
        t.mdl.frmEl.location_id.empty().trigger("change");
        t.mdl.frmEl.internal_location_id.empty().trigger("change");
    });

    t.mdl.frmEl.departmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        allowClear: true,
        delay: 200,
        placeholder: t.config.translations.select_department,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (params) {
                let companyId = t.mdl.frmEl.company_id.val();
                if (!companyId) {
                    return false;
                }

                return {
                    search: params.term || '',
                    page: params.page || 1,
                    company_id: companyId
                };
            }
        },

        templateSelection: function (s, container) {
            if (typeof s.loading !== "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr('tkt_auto_creation_id', s.tkt_auto_creation_id);
            return s.text;
        }
    })).on("change", function(e) {
        t.mdl.frmEl.request_form_view_url.html('');
        t.refillProblemCategory();  
    });

    t.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.problem_categories = [];
        t.mdl.frmEl.problemCategoryId.empty().append(new Option(t.config.translations.select_problem_category, ""));
        var type_val = parseInt($.trim(t.mdl.frmEl.departmentId.val()));
        var creatorID = t.mdl.frmEl.creatorId.val() ? t.mdl.frmEl.creatorId.val() : null;
        if (type_val > 0 && !isNaN(type_val)) {
            if (config.client === "ltts") {
                var selectedDept = t.mdl.frmEl.departmentId.find(':selected').text();
                if (selectedDept.indexOf('(') != -1) {
                    departmentName = selectedDept.substr(0, selectedDept.indexOf('('));
                } else {
                    departmentName = selectedDept;
                }
                if (departmentName.trim() == "Admin - India") {
                    t.mdl.frmEl.problemCategoryId.empty().append(new Option(t.config.translations.select_location, ""));
                }
            }
            $.get(t.config.url.problem_categories_by_company + "/" + type_val + "?creatorID=" + creatorID).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        t.mdl.frmEl.problemCategoryId.append($('<option>').val(v.id).text(v.name).attr('description', v.remarks).attr('form', v.form_id).attr('is_form_required', v.is_form_required));
                    });
                }
            }).always(function () {
                t.mdl.frmEl.problemCategoryId.trigger("change");
            });
        } else {
            t.mdl.frmEl.problemCategoryId.trigger("change");
        }
        if (t.data.problem_categories.length == 0) {
            t.mdl.frmEl.subCategoryId.empty();
            t.mdl.frmEl.subCategoryIdCvr.hide();
        }
    };

    /* creator info */
    t.creatorinfo = {};
    t.creatorinfo.wrapper = t.mdl.find(".creator-info-card");
    t.creatorinfo.fullname = t.creatorinfo.wrapper.find('#fullname');
    t.creatorinfo.username = t.creatorinfo.wrapper.find('#username');
    t.creatorinfo.emp_code = t.creatorinfo.wrapper.find('#emp_code');
    t.creatorinfo.job_title = t.creatorinfo.wrapper.find('#job_title');
    t.creatorinfo.location_name = t.creatorinfo.wrapper.find('#location_name');
    t.creatorinfo.base_location_name = t.creatorinfo.wrapper.find('#base_location_name');
    t.creatorinfo.mobile = t.creatorinfo.wrapper.find('#mobile');
    t.creatorinfo.email = t.creatorinfo.wrapper.find('#email');
    t.creatorinfo.company = t.creatorinfo.wrapper.find('#company');
    t.creatorinfo.profile = t.creatorinfo.wrapper.find('.profile-img');
    t.creatorinfo.excompany = t.creatorinfo.wrapper.find('#excompany');

    t.creatorinfo.reload = function (e) {
        t.mdl.frmEl.deviceId.val("").empty().trigger("change");
        if (typeof e !== "undefined") e.preventDefault();
        var u = t.mdl.frmEl.creatorId.val();
    t.creatorinfo.wrapper.addClass("hide");
        if (!u) {
            return;
        }
        $.get(t.config.url.user_basic_info + "/" + u + "/true", function (d) {
            if (typeof d !== "undefined" && d.status === "success") {
                var fullname = d.data.fullname && d.data.fullname.length > 30 ? d.data.fullname.substr(0, 30) + "..." : d.data.fullname;
                var username = d.data.username && d.data.username.length > 30 ? d.data.username.substr(0, 30) + "..." : d.data.username || ' ';
                t.creatorinfo.fullname.html("<span class='text-muted text-sm' data-toggle='tooltip' data-placement='bottom' data-original-title='" + d.data.fullname + "'>" + fullname + "</span>");
                t.creatorinfo.username.html("<span class='text-muted text-sm' data-toggle='tooltip' data-placement='bottom' data-original-title='" + d.data.username + "'>" + username + "</span>");

            t.creatorinfo.emp_code.text(d.data.employee_num || "-");
            t.creatorinfo.job_title.text(d.data.jobtitle || "-");
                if (d.data.company_id == t.mdl.frmEl.company_id.val()) {
                    t.mdl.frmEl.seatNo.val(d.data.seat_no);
                    $('#other_location').addClass('d-none');
                } else {
                    t.mdl.frmEl.seatNo.val('');
                    $('#other_location').removeClass('d-none');
                }
            t.creatorinfo.location_name.text(d.data.location || "-");
            t.creatorinfo.base_location_name.text(d.data.baseLocation || "-");
            t.creatorinfo.mobile.text(d.data.phone || "-");
            t.creatorinfo.email.text(d.data.email || "-");
            t.creatorinfo.excompany.text(d.data.ex_user_company || "-");
            t.creatorinfo.company.text(d.data.company || "-");
            t.creatorinfo.profile.attr("src", d.data.profile);
            t.creatorinfo.wrapper.removeClass("hide");
            }
        });
    };

    t.mdl.frmEl.creatorId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.creatorId.parent(),
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.mdl.frmEl.company_id.val(),
                    company_access_via: 1,
                };
            },
            delay: 300
        },
        transport: function (params, success, failure) {
            let companyId = t.mdl.frmEl.company_id.val();
            if (!companyId) {
                sweetAlert('center', 'warning', { msg: 'Please select a company first.' });
                return false;
            }
            let request = $.ajax(params);
            request.then(success);
            request.fail(failure);
            return request;
        },
        allowClear: true,
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "32px";
            return t.userDropdownFormat(data, imgPaddingLeft);
        },
    })).on("change", function (e) {
        t.creatorinfo.reload();
        // if(t.config.client == 'ril'){
        //     t.mdl.frmEl.seatNo.empty().trigger("change");
        // }else{
        //     t.mdl.frmEl.seatNo.val('');
        // }
        // t.mdl.frmEl.location_id.empty().trigger("change");
        // t.mdl.frmEl.internal_location_id.empty().trigger("change");
    });

    t.refillSubCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.sub_categories = [];
        var type_val = parseInt($.trim(t.mdl.frmEl.problemCategoryId.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.problem_categories, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.sub_categories = v.sub;
                            t.mdl.frmEl.subCategoryId.empty();
                            $.each(v.sub, function (j, k) {
                                t.mdl.frmEl.subCategoryId.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id).attr('is_form_required', k.is_form_required));
                                if(k.form_id !== 0) {
                                    var Id = k.form_id;
                                    var request_id = btoa(t.mdl.frmEl.id.val());
                                    var newUrl = config.url.requested_form +"/" + Id + "?q=" + request_id;
                                    t.mdl.frmEl.form.append($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                }
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }
        let company_id = t.mdl.frmEl.company_id.val();
        if(t.config.tkt_config.some(item => item.company_id == company_id && item.kd_auto_suggestion == 1)) {
            t.getRelatedDocs();
        }
        t.updateSubCategoryVisibility();
        t.mdl.frmEl.subCategoryId.trigger("change");
        if (t.data.sub_categories.length === 0) {
            t.loadCustomFields();
        }
    };

    t.fillSla = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.mdl.frmEl.problemCategoryId.val();
        var sub_category_id = t.mdl.frmEl.subCategoryId.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.sub_categories) == true && t.data.sub_categories.length) {
            $.each(t.data.sub_categories, function (i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                        t.mdl.frmEl.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.problem_categories != "undefined" && t.data.problem_categories.length && t.data.sub_categories.length == 0) {
            $.each(t.data.problem_categories, function (i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
                        t.mdl.frmEl.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
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
        t.updatesubject();
        t.updateform();
    };

    t.updatesubject = function () {
        t.mdl.frmEl.subject.val("");
        var pc = t.mdl.frmEl.problemCategoryId.find(':selected').text() == 'Select Problem Category' || t.mdl.frmEl.problemCategoryId.find(':selected').text() == 'Select Location' ? '' : t.mdl.frmEl.problemCategoryId.find(':selected').text();
        var sc = '';
        if (typeof t.mdl.frmEl.subCategoryId.val() !== 'undefined' && t.mdl.frmEl.subCategoryId.val() !== null) {
            var sc = t.mdl.frmEl.subCategoryId.find(':selected').text() == 'Select Sub Category' ? '' : " - " + t.mdl.frmEl.subCategoryId.find(':selected').text();
        }
        console.log(sc);
        subject = pc + "" + sc;
        t.mdl.frmEl.subject.val(subject);

        if (t.mdl.frmEl.problemCategoryId.find(':selected').attr('description') != null) {
            if (t.mdl.frmEl.problemCategoryId.find(':selected').attr('description') == null || t.mdl.frmEl.subCategoryId.find(':selected').attr('description') == null) {
                var pc = t.mdl.frmEl.problemCategoryId.find(':selected').attr('description');
                t.mdl.frmEl.description.html(pc);
            } else {
                var scc = t.mdl.frmEl.subCategoryId.find(':selected').attr('description');
                description = scc;
                t.mdl.frmEl.description.html(description);
            }
            t.mdl.frmEl.description_info.show();
        } else {
            t.mdl.frmEl.description_info.hide();
            if (t.mdl.frmEl.subCategoryId.find(':selected').attr('description') != null) {
                var scc = t.mdl.frmEl.subCategoryId.find(':selected').attr('description');
                description = scc;
                t.mdl.frmEl.description.html(description);
                t.mdl.frmEl.description_info.show();
            }
        }
        t.mdl.frmEl.request_form_view_url.html("");
    }

    t.refillTat = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.mdl.frmEl.priorityId.val();
        var val = "";
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.mdl.frmEl.tat.val(val);
    }

    t.updateSubCategoryVisibility = function () {
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

    t.resetFrm = function () {
        t.mdl.frm.trigger("reset");
        t.mdl.frmEl.id.val("");
        // t.mdl.frmEl.forAction.val("");
        t.mdl.frmEl.company_id.val("").trigger("change");
        t.mdl.frmEl.departmentId.val("").empty();
        t.mdl.frmEl.problemCategoryId.val("").empty();
        t.mdl.frmEl.subCategoryId.val("").empty();
        t.mdl.frmEl.subject.val("");
        if (t.config.client == 'ril') {
            t.mdl.frmEl.seatNo.val("").empty().trigger("change");
        } else {
            t.mdl.frmEl.seatNo.val("");
        }
        t.mdl.frmEl.content.val('').summernote('code', '');
        t.mdl.frmEl.deviceId.empty().trigger("change");
        t.mdl.frmEl.cc_emails.val("").trigger("change").attr('disabled', false);
        $('.departmentCustomFieldWrapper').html("");
        $('.categoryCustomFieldWrapper').html("");
        t.mdl.frmEl.description_info.hide();
        // t.mdl.find('#shows_error').html('');
        t.updateSubCategoryVisibility();
        t.frmValidator.resetForm();
    };

    t.refillPriority = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.mdl.frmEl.priorityId.empty().append(new Option(t.config.translations.select_priority, ""));
        t.mdl.frmEl.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.mdl.frmEl.priorityId.append(new Option(k.name, k.id));
        });
        t.mdl.frmEl.priorityId.trigger("change");
    };

    t.mdl.frmEl.ac_email_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.ac_email_id.parent(),
        placeholder: t.config.translations.select_reply_to_account,
        allowClear: true,
        ajax: {
            url: t.config.url.getEnabledAccountsForOptions,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term || '',
                    page: params.page || 1,
                    company_id: t.mdl.frmEl.company_id.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results || [],
                    pagination: {
                        more: data.pagination?.more || false
                    }
                };
            },
            cache: true
        }
    }));

    t.fillCreatedVia = function () {
        if (typeof t.config.created_via != "undefined" && typeof t.config.created_via == "object" && Object.keys(t.config.created_via).length > 0) {
            t.mdl.frmEl.created_via.empty();
            $.each(t.config.created_via, function (i, v) {
                t.mdl.frmEl.created_via.append(new Option(v, i));
            });
            t.mdl.frmEl.created_via.closest(".row").show();
            t.mdl.frmEl.created_via.trigger("change");
        } else {
            t.mdl.frmEl.created_via.empty().closest(".row").hide();
        }
    };

    t.mdl.frmEl.deviceId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.deviceId.parent(),
        placeholder:t.config.translations.select_device,
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.mdl.frmEl.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && config.client == "ltts") ? true : false,
                };
            },
            delay: 300
        },
        allowClear: true,
        templateResult: function (s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            a = "<div class='so-t'><i class='bi bi-tag'></i> " + s.asset_tag + "</div>";
            if (s.asset_name != null) {
                a += "<div class='so-t'><i class='bi bi-laptop'></i> " + s.asset_name + "</div>";
            }
            a += "<div class='so-m'><i class='bi bi-tablet'></i>" + s.name + " " + s.modelno + "</div>";
            a += "<div class='so-t'><i class='bi bi-qr-code'></i>" + s.serial + "</div>";
            return $("<div>" + a + "</div>");
        }
    }));

    t.mdl.frmEl.tags.select2({
        dropdownParent: t.mdl.frmEl.tags.parent(),
        width: "100%",
        placeholder: t.config.translations.select_tags,
        tags: true,
        allowClear: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getTagDetails;
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
                    page: params.page,
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });

    t.mdl.frmEl.cc_emails.select2({
        width: "100%",
        placeholder: t.config.translations.add_cc,
        allowClear: true,
        dropdownParent: t.mdl.frmEl.cc_emails.parent(),
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getUserCCByAjax;
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
                    page: params.page,
                    user_id: t.mdl.frmEl.creatorId.val(),
                    company_id: t.mdl.frmEl.company_id.val(),
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
    }).on('select2:unselecting', function (e) {
        var selected = $(e.params.args.data.element);
        if (selected.data('locked')) {
            e.preventDefault();
        }
    });

    t.frmValidator = t.mdl.frm.validate({
        ignore:[],
        onsubmit: false,
        rules: {
            department_id: {
                required: true,
                str_name: true
            },
            creator_id: {
                required: true,
                str_name: true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            subject: {
                required: true,
                acceptable_spcl_chr: false,
                clean_text_only: true,
            },
            content: {
                required: true,
                summernote:true,
            },
            tat: {
                required: true,
                remarks: true,
                digits: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            cc_emails: {
                multipleemailaddress: true
            },
            seat_no: {
                clean_text_only: true,
            },
            'tags[]': {
                clean_text_only: true,
            },
            company_id: {
                required: true,
            },
            location_id: {
                required: function () {
                    return !$('#other_location').hasClass('d-none');
                }
            }
        },
        messages: {
            cc_emails: {
                multipleemailaddress: t.config.translations.please_enter_a_valid_email_address
            }
        },
        errorPlacement: function (error, element) {
            var container = element.closest('.input-group');
            if (container.length) {
                error.insertAfter(container);
            } else {
                error.insertAfter(element.parent());
            }
        },
        invalidHandler: function (event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    t.handleSubmit = function (e) {
        e.preventDefault();
        let s = t.mdl.frmEl.content.val();
        count = s.replaceAll("&nbsp;", "").trim();
        if (t.frmValidator.form() == false) {
            return false;
        }
        if (t.httpCall != true) {
            return false;
        }
        let cc_email_check = t.mdl.frmEl.cc_emails.val() ? t.mdl.frmEl.cc_emails.val().toString() : "";
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
        t.httpCall = false;
        var formData = new FormData(t.mdl.frm[0]);
        formData.append('cc_emails', cc_email_check);
        var subCategoryId = t.mdl.frmEl.subCategoryId.val();
        (subCategoryId && subCategoryId !== '') ?'' : formData.append('sub_category_id', '');
        console.log(formData, 'formData', t.httpPostUrl);
        var http = $.ajax({
            url: t.httpPostUrl,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.frmEl.content.val('').summernote('code', '');
                    sweetAlert('center', 'success', data);
                    $('.btn-reload-list').trigger('click');
                    t.mdl.modal("hide");
                } else {
                    if (typeof data.requestForm !== 'undefined') {
                        t.updateform();
                    }
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
    $(document).on('click', '.custom_field_info', function() {
        var id = $(this).attr("data-id");
        var url = t.config.url.getCustomFieldNote + '/' + id;
        $("#helpNoteContent").html('');
    
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $("#helpNoteContent").html(response.help_note);
                $("#helpNoteDialog").modal("show"); 
            },
            error: function(xhr, status, error) {
                console.log("Error:", error);
                $("#helpNoteContent").html('<p class="text-danger">Failed to load data.</p>');
            }
        });
    });

    t.btnCreateTicket.on('click', function (e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostUrl = '';
        t.mdl.title.html(t.config.translations.create_ticket);
        t.mdl.btn.submit.text(t.config.translations.create);
        if (Array.isArray(t.config.company_defulte) && t.config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.mdl.frmEl.company_id.append(option).trigger('change');
        }
        t.mdl.frmEl.forAction.val("1");
        var displayName = t.config.user.displayName || (t.config.user.first_name + " " + t.config.user.last_name + " (" + t.config.user.username + ")");
        t.mdl.frmEl.creatorId.append(new Option(displayName, t.config.user.id, true, true)).trigger("change");
        t.attachment.empty();
        let company_id = t.mdl.frmEl.company_id.val();
        t.mdl.frmEl.follow_cc.prop(
            'checked',
            t.config.tkt_config.some(item =>
                item.company_id == company_id &&
                item.checked_cc_checkbox == 1
            )
        );
        t.frmTokenize();
        t.refillPriority();
        t.fillCreatedVia();
        if (t.config.user.limits) {
            t.mdl.frmEl.priorityId.closest('.amg-form-field-row').removeClass('d-none');
            t.mdl.frmEl.tat.closest('.amg-form-field-row').removeClass('d-none');
            t.mdl.frmEl.ac_email_id.closest('.amg-form-field-row').removeClass('d-none');
            t.mdl.frmEl.created_via.closest('.amg-form-field-row').removeClass('d-none').css('display', '');
            t.mdl.frmEl.tags.closest('.amg-form-field-row').removeClass('d-none');
            t.httpPostUrl =  t.config.url.create_ticket;
        } else {
            t.mdl.frmEl.priorityId.closest('.amg-form-field-row').addClass('d-none');
            t.mdl.frmEl.tat.closest('.amg-form-field-row').addClass('d-none');
            t.mdl.frmEl.ac_email_id.closest('.amg-form-field-row').addClass('d-none');
            t.mdl.frmEl.created_via.closest('.amg-form-field-row').addClass('d-none');
            t.mdl.frmEl.tags.closest('.amg-form-field-row').addClass('d-none');
            t.httpPostUrl =  t.config.url.create_ticket_by_user;
        }
        t.mdl.modal("show");
    });
    t.loadCustomFields = function () {
        var getDepartmentCustomFields =
            t.config.url.getDepartmentCustomFields + "/" + t.mdl.frmEl.departmentId.val();

        var customField = new CustomField(config);

        customField.displayCustomField(
            getDepartmentCustomFields,
            '',
            t.mdl.frmEl.problemCategoryId,
            t.mdl.frmEl.deviceId,
            t.mdl.frmEl.problemCategoryId.val(),
            t.mdl.frmEl.subCategoryId.val()
        );
    };
    t.updateform = function() {
        if(t.mdl.frmEl.request_form_view_url.html() != "") {
            return false;
        }
        var foundObject = null;
        if(t.mdl.frmEl.problemCategoryId.val() != null && t.mdl.frmEl.problemCategoryId.val() != '') {
            t.frmServiceRequest.el.dept_id = t.frmServiceRequest.find("#dept_id");
            t.frmServiceRequest.el.prob_id = t.frmServiceRequest.find("#prob_id");
            t.frmServiceRequest.el.user_id = t.frmServiceRequest.find("#user_id");
            $.each(t.data.problem_categories, function(index, obj) {
                if (obj.id == t.mdl.frmEl.problemCategoryId.val()) {
                    foundObject = obj;
                    return false; 
                }
            });
            if(foundObject != null) {
                var found = false
                if(foundObject.sub != undefined && foundObject.sub.length != 0){
                    $.each(foundObject.sub, function(key, value) {
                        if ( value.form_id != undefined && value.form_id != 0  && value.form_id != null ) {
                            found = true;
                            return false; // exit the loop
                        } else if(value.form_id == undefined && t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') !=0) {
                            found = true;
                            return false;
                        }
                    });

                    if(found) {
                        if(t.mdl.frmEl.subCategoryId.find(':selected').attr('form') != undefined && t.mdl.frmEl.subCategoryId.find(':selected').attr('form') != 0) {
                            var form_id = t.mdl.frmEl.subCategoryId.find(':selected').attr('form');
                            var is_form_required = t.mdl.frmEl.subCategoryId.find(':selected').attr('is_form_required');
                            if(form_id !== 0) {
                                var request_id = btoa(t.mdl.frmEl.id.val());
                                var newUrl = config.url.requested_form +"/" + form_id + "?q=" + request_id;
                                t.mdl.frmEl.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'success', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.request_id.val(t.mdl.frmEl.id.val());
                                        t.frmServiceRequest.el.for_action.val(1);
                                        if(is_form_required == 1) {
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } 
                                        if(is_form_required == 2) {
                                            t.frmServiceRequest.el.form_title.html("Custom Form");
                                            t.loadFormModal(form_id);
                                        }
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                                // t.mdl.frmEl.form_info.show();
                            }
                        }
                        if (t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != undefined && t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != 0 && t.mdl.frmEl.subCategoryId.find(':selected').attr('form') == undefined && t.mdl.frmEl.subCategoryId.val() != "") {
                            if (t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') == null || t.mdl.frmEl.subCategoryId.find(':selected').attr('form') == null) {
                                var form_id = t.mdl.frmEl.problemCategoryId.find(':selected').attr('form');
                                var is_form_required = t.mdl.frmEl.problemCategoryId.find(':selected').attr('is_form_required');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.mdl.frmEl.id.val());
                                    var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.request_id.val(t.mdl.frmEl.id.val());
                                            if(is_form_required == 1) {
                                                t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                                t.loadRequestedForm(result.data.fields);
                                                t.mdlServiceRequest.modal("show");
                                            } 
                                            if(is_form_required == 2) {
                                                t.frmServiceRequest.el.form_title.html("Custom Form");
                                                t.loadFormModal(form_id);
                                            }
                                        } else {
                                            var data = {
                                                'msg': 'unable to load Form.'
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.mdl.frmEl.subCategoryId.find(':selected').attr('form');
                                var request_id = btoa(t.mdl.frmEl.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if(t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != undefined && t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != 0) {
                            if(t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') == null || t.mdl.frmEl.subCategoryId.find(':selected').attr('form') == null){
                                var form_id = t.mdl.frmEl.problemCategoryId.find(':selected').attr('form');
                                var is_form_required = t.mdl.frmEl.problemCategoryId.find(':selected').attr('is_form_required');
                                // console.log("form_id:" + form_id);
                                if(form_id !== 0) {
                                    var request_id = btoa(t.mdl.frmEl.id.val());
                                    var newUrl = config.url.requested_form +"/" + form_id + "?q=" + request_id;
                                    // console.log(newUrl);
                                    t.mdl.frmEl.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.request_id.val(t.mdl.frmEl.id.val());
                                            t.frmServiceRequest.el.for_action.val(1);
                                            if(is_form_required == 1) {
                                                t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                                t.loadRequestedForm(result.data.fields);
                                                t.mdlServiceRequest.modal("show");
                                            } 
                                            if(is_form_required == 2) {
                                                t.frmServiceRequest.el.form_title.html("Custom Form");
                                                t.loadFormModal(form_id);
                                            }
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.mdl.frmEl.subCategoryId.find(':selected').attr('form');
                                var request_id = btoa(t.mdl.frmEl.id.val());
                                var newUrl = config.url.requested_form +"/" + form_id + "?q=" + request_id;
                                t.mdl.frmEl.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                            }
                        }
                    }
                } else {
                    if(t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != undefined && t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') != 0) {
                        if(t.mdl.frmEl.problemCategoryId.find(':selected').attr('form') == null || t.mdl.frmEl.subCategoryId.find(':selected').attr('form') == null){
                            var form_id = t.mdl.frmEl.problemCategoryId.find(':selected').attr('form');
                            var is_form_required = t.mdl.frmEl.problemCategoryId.find(':selected').attr('is_form_required');
                            // console.log("form_id:" + form_id);
                            if(form_id !== 0) {
                                var request_id = btoa(t.mdl.frmEl.id.val());
                                var newUrl = config.url.requested_form +"/" + form_id + "?q=" + request_id;
                                // console.log(newUrl);
                                t.mdl.frmEl.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.request_id.val(t.mdl.frmEl.id.val());
                                        t.frmServiceRequest.el.for_action.val('');
                                        if(is_form_required == 1) {
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } 
                                        if(is_form_required == 2) {
                                            t.frmServiceRequest.el.form_title.html("Custom Form");
                                            t.loadFormModal(form_id);
                                        }
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        } else {
                            var form_id = t.mdl.frmEl.subCategoryId.find(':selected').attr('form');
                            var request_id = btoa(t.mdl.frmEl.id.val());
                            var newUrl = config.url.requested_form +"/" + form_id + "?q=" + request_id;
                            t.mdl.frmEl.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                        }
                    }
                }
                if (found) {
                    $("#ticket-mdl").find("#request_submit_id").val(0);
                } else {
                    $("#ticket-mdl").find("#request_submit_id").val("");
                }
            }
        }
    }
    t.loadFormModal = function(id) {
        if(loadCustomForm == false) {
        $("#mdl-servicerequest").find(".modal-dialog").addClass("custom-modal-xl");
        $("#mdl-servicerequest").find("#build-wrap").empty();
            $.get(t.config.url.ticket_getcustomview + "/" + id, function(data) {
            $("#mdl-servicerequest").find("#build-wrap").html(data.content);
            t.frmServiceRequest.el.field_form_required.val(2);            
                $("#eye_icon").removeClass("disabled");
                if($(t.mdl.frmEl.subCategoryIdCvr).is(":visible")) {
                    let inputGroup = $("#ticket-mdl-frm").find("#sub_category_id").closest('.input-group');
                    if (inputGroup.find('#eye_icon').length === 0) {
                        inputGroup.append(`
                            <span id="eye_icon" class="input-group-addon eye-icon" data-form-id="${id}">
                                <i class="bi bi-eye"></i>
                            </span>
                        `);
                    }
                } else {
                    let inputGroup = $("#ticket-mdl-frm").find("#problem_category_id").closest('.input-group');
                    if (inputGroup.find('#eye_icon').length === 0) {
                        inputGroup.append(`
                            <span id="eye_icon" class="input-group-addon eye-icon" data-form-id="${id}">
                                <i class="bi bi-eye"></i>
                            </span>
                        `);
                    }
                }
                if(data.form_name =='customform1' || data.form_name =='ltsctinventoryform') {
                    $("#requested_form .modal-body").css("background", "linear-gradient(to right, #0b174e, #109fca)");
                    $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                } else {
                    if(t.config.client == 'ltts' || t.config.sub_client == 'live'){
                        $("#mdl-servicerequest").find(".modal-dialog").removeClass("custom-modal-xl");
                        $("#mdl-servicerequest").find(".modal-dialog").addClass("custom-modal-sm");
                    } else {
                        $("#mdl-servicerequest").find(".modal-dialog").removeClass("custom-modal-xl");
                        $("#mdl-servicerequest").find(".modal-dialog").addClass("custom-modal-xl");
                    }
                    $("#requested_form .modal-body").css("background", "#fff");
                    $("#mdl-servicerequest").find("#footer_button").addClass("hide");
                    if(data.form_name == "lttscustomform"){
                        let lockedCheckboxes = [];
                        $.get(t.config.url.fetch_active_ticket + "/" + t.mdl.frmEl.creatorId.val()+"?department="+t.mdl.frmEl.departmentId.val()+"&category="+t.mdl.frmEl.problemCategoryId.val()+"&sub_category="+t.mdl.frmEl.subCategoryId.val(), function(data) {
                             $("#mdl-servicerequest").find('#requested_form').find(".form_title").html(data.form_name);
                             $("#mdl-servicerequest").find('#github_access_url').val(data.github_url);

                             // disabled the rest access except gitHub access for external public start
                             var disabled = data.form_name == "Github-External-Public" ? true : false;
                             var classHide = data.form_name == "Github-External-Public" ? 'hide' : '';
                             $("#mdl-servicerequest").find("#requested_form").find('#not_active_ticket_options').addClass(classHide);
                             $("#mdl-servicerequest").find("#requested_form").find('#active_ticket_options').addClass(classHide);
                             $("#mdl-servicerequest").find("#requested_form").find('#github_gitbash').prop('disabled', disabled);
                             $("#mdl-servicerequest").find("#requested_form").find('#github_dlp').prop('disabled', disabled);
                             $("#mdl-servicerequest").find("#requested_form").find('#github_git_bash_dlp').prop('disabled', disabled);
                             $("#mdl-servicerequest").find("#requested_form").find('#code-push-pull').prop('disabled', disabled);
                             $("#mdl-servicerequest").find("#requested_form").find('#code-paste-upload').prop('disabled', disabled);
                             // disabled the rest access except gitHub access for external public end
                             var $externalCategory = $("#mdl-servicerequest #external_public_category");
                             $externalCategory.toggleClass("hide", data.form_name !== "Github-External-Public");

                             if(data.active_ticket == "yes"){
                                $("#mdl-servicerequest").find(".no_active_ticket").addClass("hide")
                                $("#mdl-servicerequest").find(".active_ticket").removeClass("hide")
                                $("#mdl-servicerequest").find("#cr_custom_form").val('active_ticket');
                                $("#mdl-servicerequest").find(".active_ticket_id").html("Already ticket id <a class='have_active_access' href='" + t.config.url.requestInfo + '/' + data.request_id + "' target='_blank'>#" + data.request_id + "</a>( "+data.req_status+") is active for GitHub<br><input type='hidden' name='active_ticket' value='"+data.request_id+"'> ");
                                $('#mdl-servicerequest').find('#nextData').prop('disabled', true);
                                var eliminateSpace = '';
                                (data.field_values).forEach(elements => {
                                    // var access = (elements).split(" + ");
                                    // access.forEach(element => {
                                        eliminateSpace = elements.replace(" ", "_");
                                        let idName = eliminateSpace.replace(/\s+/g, '').toLowerCase();
                                        lockedCheckboxes.push(idName);
                                        $("#mdl-servicerequest").find(".active_ticket").find("#"+eliminateSpace.toLowerCase()).prop('checked', true)
                                        $("#mdl-servicerequest").find(".active_ticket").on('click',"#"+eliminateSpace.toLowerCase(), function(e) {
                                            if (!$(this).prop('checked')) {
                                                $(this).prop('checked', true);
                                            }
                                        });
                                    // });
                                    });
                            }else{
                                $("#mdl-servicerequest").find('#requested_form').find(".form_title").html(data.form_name);
                                $("#mdl-servicerequest").find(".active_ticket").addClass("hide")
                                $("#mdl-servicerequest").find(".no_active_ticket").removeClass("hide");
                                $("#mdl-servicerequest").find("#cr_custom_form").val('no_active_ticket');
                            }
                        });

                        $("#mdl-servicerequest .active_ticket input[type='checkbox']").on('change', function () {
                            let checkedCount = $("#mdl-servicerequest .active_ticket input[type='checkbox']:checked").length;
                            if (checkedCount >= lockedCheckboxes.length + 1) {
                                $('#mdl-servicerequest').find('#nextData').prop('disabled', false);
                            } else {
                                $('#mdl-servicerequest').find('#nextData').prop('disabled', true);
                            }
                        });
                    }
                }
                
            t.mdlServiceRequest.modal("show");
        });
            loadCustomForm = true;
        }
    }
    t.loadRequestedForm = function(fields) {
        var test = fields;
        t.frmServiceRequest.el.field_form_required.val(1);
        var fbTemplate = document.getElementById('build-wrap'),
            $fbEditor = $(document.getElementById('fb-editor')),
            $formContainer = $(document.getElementById('field_values')),
            $editContainers = $(document.getElementById('fb-rendered-form')),

            fbOptions = {
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            },
            options = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                },
                allowStageSort: false,
                showActionButtons: false,
                stickyControls: false,
                disabledFieldButtons: {
                    autocomplete: ['remove','edit','copy'],
                    text: ['remove','edit','copy'],
                    select:  ['remove','edit','copy'],
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
                },
            },
            options2 = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            };
        $('#requested_form #build-wrap').empty();
        // formBuilder = $fbEditor.formBuilder(fbOptions);
        $(fbTemplate).formRender(options);
        setupDependsOn(document.getElementById('requested_form'));
        // $(fbTemplate).formBuilder(options);
        // formBuilders = $fbEditor.formBuilder(options2);

        document.getElementById("saveData").addEventListener("click", () => {
            var outputHtml = $(fbTemplate).formRender("userData");
            $formContainer.val(JSON.stringify(outputHtml));
            // console.log(JSON.stringify(outputHtml));
            // result = formBuilders.actions.save();
        });

        setTimeout( function() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            // $('.datepicker').attr('min', today);
            // $('input[type=date]').attr('min', today);
            console.log("datepicker reload");
        }, 3000);
    }
    t.mdl.frmEl.getcc_email.on('change', function() {
        t.mdl.frmEl.cc_emails.val('').trigger("change");
    
        var departmentId = t.mdl.frmEl.departmentId.val();
        var problemCategoryId = t.mdl.frmEl.problemCategoryId.val();  
        var subCategoryId = t.mdl.frmEl.subCategoryId.val();         
    
        if(!isNaN(departmentId) && departmentId != '' && departmentId != 'null' && departmentId !== null && 
           !isNaN(problemCategoryId) && problemCategoryId != '' && 
           config.client != "ltts") {
            var url = config.url.fetchccEmailID + "/" + departmentId + "/" + problemCategoryId;
            if (subCategoryId && !isNaN(subCategoryId)) {
                url += "/" + subCategoryId;
            }
            $.get(url).done(function(data) {
                if(typeof data == "object" && data.data.length > 0) {
                    t.mdl.frmEl.follow_cc.prop('checked', true);
                    t.mdl.frmEl.cc_emails.empty();
                    $.each(data.data, function(i, v) {
                        let newOption = new Option(v, v, true, true);
                        $(newOption).attr("data-locked", "true");
                        t.mdl.frmEl.cc_emails.append(newOption);
                    });
                    t.mdl.frmEl.cc_emails.val((data.cc).split(",")).trigger("change");
                }
            });
        }
    });
    
    t.mdl.frmEl.problemCategoryId.on("change", $.proxy(t.refillSubCategory));
    t.mdl.frmEl.problemCategoryId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.problemCategoryId.parent(),placeholder:t.config.translations.select_problem_category} )).on("change", $.proxy(t.fillSla));
    t.mdl.frmEl.subCategoryId.select2($.extend({}, select2Opts, {placeholder:t.config.translations.select_sub_category,dropdownParent: t.mdl.frmEl.subCategoryId.parent()})).on("change", function () {
        t.fillSla();
        t.loadCustomFields();
    });
    t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.priorityId.parent()} )).on("change", $.proxy(t.refillTat));
    t.mdl.frmEl.created_via.select2($.extend({},select2Opts,{dropdownParent: t.mdl.frmEl.created_via.parent()}));
    t.mdl.frmEl.internal_location_id.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.internal_location_id.parent(),placeholder: 'Select Locations'}));
    t.mdl.btn.submit.on("click", $.proxy(t.handleSubmit));
    var scrollableDiv = document.getElementById('relatedArticles');
    t.pageNo = 1;
    if(scrollableDiv) {
        scrollableDiv.addEventListener('scroll', function() {
            if (Math.round(scrollableDiv.scrollHeight - scrollableDiv.scrollTop) <= scrollableDiv.clientHeight) {
                t.pageNo++;
                let company_id = t.mdl.frmEl.company_id.val();
                if(t.config.tkt_config.some(item => item.company_id == company_id && item.kd_auto_suggestion == 1)) {
                    t.getRelatedDocs();
                }
            }
        });
    }
    $("#btn-hide-all").click(function (e) {
        t.kd_mdl.modal("hide");
        setTimeout(function() {
            t.mdlServiceRequest.modal("hide");
            // t.mdl.modal("hide");
        }, 500);
    });

    t.getRelatedDocs = function() {
        var departmentId = t.mdl.frmEl.departmentId.val();
        var problemCategoryId = t.mdl.frmEl.problemCategoryId.val();
        var subCategoryId = t.mdl.frmEl.subCategoryId.val();
        const baseUrl = config.url.articleImagePath;
        const baseViewUrl = config.url.view_article;
        if(problemCategoryId == null || problemCategoryId == '') {
            return false;
        }
        var formData = new FormData();
        if(departmentId != null && departmentId != '') {
            formData.append('department_id', departmentId);
        }
        if(problemCategoryId != null && problemCategoryId != '') {
            formData.append('parent_category_id', problemCategoryId);
        }
        if(subCategoryId != null && subCategoryId != '') {
            formData.append('sub_category_id', subCategoryId);
        }
        formData.append('page', t.pageNo);
        formData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.config.url.getRelatedDocuments,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (data.data && data.data.length > 0) {
                t.kd_mdl.frmEl.kdSuggestionWrapper.show();
                var html = "";
                t.kd_mdl.frmEl.relatedArticles.empty();
                t.kd_mdl.modal("show");

                html += `<div class="kd_details_cards"><div class="row card-container">`;
                $.each(data.data, function(i, v) {
                    html += `
                    <div class="col-md-3">
                        <a href="${baseViewUrl}/${v.id}" target="_blank">
                            <div class="card" style="background-image: url('${baseUrl}/${v.card_img ?? '17.png'}');">
                                ${ v.card_img == null ? `<div class="big-kd">KD</div>` : '' }
                                <div class="card-content position-relative pb-2 ps-1">
                                    <div class="title" data-toggle="tooltip" data-original-title="${v.title ?? ''}"><strong>${v.title && v.title.length > 30 ? v.title.substr(0, 30) + "..." : v.title}</strong></div>
                                    <div class="tags">
                                        ${v.dep_name ? `<button class="btn btn-outline-gray rounded-pill tag" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8), 0 0 10px rgba(0, 0, 0, 0.5);">${v.dep_name}</button>` : ''}
                                    </div>
                                </div>
                                <div class="curve_one"></div>
                                <div class="arro_back">
                                       <svg class="hex-icon-color" width="39" height="39" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"></path>
                                        <path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"></path>
                                        </svg>
                                </div>
                            </div>
                        </a>
                    </div>`;
                })
                html += `</div></div>`;
                t.kd_mdl.frmEl.relatedArticles.append(html);
            } else {
                t.kd_mdl.frmEl.kdSuggestionWrapper.hide();
            }
        });
    }

    t.userDropdownFormat = function (s, imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        function truncateText(text, length) {
            return text && text.length > length ? text.substring(0, length) + "..." : text;
        }

        var name = truncateText(s.text, 50);
        var email = truncateText(s.email ? s.email : "", 50);

        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='bi bi-person' style='padding-right: 3px;'></i>" + name + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class='bi bi-envelope' style='padding-right: 3px;'></i>" + email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class='bi bi-credit-card' style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    t.frmTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.mdl.frmEl.tmp_id.val(v);
        t.frmServiceRequest.el.tmp_id.val(v);
    };
    if (config.dashboardTicketCreateRedirect == true) {
        $("#btnCreateTicket").trigger("click");
    }
}