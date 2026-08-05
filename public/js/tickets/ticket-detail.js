var CcMaster = function (config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');
    t.page = $('#page_boxed');
    t.pane = t.page.find('#cc_master');
    t.set1 = t.pane.find('.set1');
    t.set2 = t.pane.find('.set2');
    t.cc_master_value = t.pane.find('#cc_master_value');
    t.cc_emails = t.pane.find('#cc_emails');
    t.httpCall = true;

    t.setData = function (data) {
        t.config.data = data;
        var value = typeof t.config.data != "undefined" && t.config.data.cc_emails != null && t.config.data.cc_emails != 'null' ? t.config.data.cc_emails : "";
        if (value != "") {
            t.cc_emails.val(value);
            
            t.cc_master_value.text(value);
        } else {
            t.cc_master_value.text('-- No CC E-Mails found');
            t.cc_emails.val(value);
        }
    };
    t.visibilityDecision = function () {
        var wai = parseInt(t.config.wai),
            status = parseInt(t.config.data.status_id);
        if (wai != 23) {
            t.pane.addClass('hide');
        } else {
            t.pane.removeClass('hide');
        }
    }

    t.setShow = function (set_mode, emails) {
        var setval = typeof set_mode != "undefined" ? set_mode : 'set1';
        var setemails = typeof emails != "undefined" ? emails : t.config.data.cc_emails;

        if (setval == 'set2') {
            t.set1.addClass('hide');
            t.set2.removeClass('hide');
            t.cc_emails.val(setemails);
        } else {
            t.set1.removeClass('hide');
            t.set2.addClass('hide');
            var get_cc = (setemails == "" || setemails == null) ? "-- No CC E-Mails found" : setemails;
            t.cc_master_value.text(get_cc);
        }
    };

    t.handleSubmit = function (e) {
        e.preventDefault();
        try {
            var value = $.trim(t.cc_emails.val());
            if (t.httpCall != true) {
                return false;
            }

            t.httpCall = false;
            var formData = new FormData();
            formData.append('emails', value);
            formData.append('_token', t.token.attr('content'));

            var http = $.ajax({
                url: t.config.url.update_master_cc + '/' + t.config.data.id,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function (data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.setShow('set1');
                        sweetAlert('center', 'success', data);
                        setTimeout(function () {
                            window.location.reload();
                        }, 600);
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                } else {
                    sweetAlert('center', 'error', {
                        msg: "Unable to update CC"
                    });
                }
            });
            http.fail(function () {
                sweetAlert('center', 'error', {
                    msg: "Something went wrong. Please check the given details are correct."
                });
            });

            http.always(function () {
                t.httpCall = true;
            });
            http.always(function () {
                t.httpCall = true;
            });
        } catch (e) {
            console.log(e);
        }
    };

    t.pane.on('click', '.btn_act_update', function () {
        t.setShow('set2');
    });

    t.pane.on('click', '.btn_act_cancel', function () {
        t.setShow('set1');
    });

    t.pane.on('click', '.btn_act_save', $.proxy(t.handleSubmit));

    var tktEmail = new TktEmail();
    t.cc_emails.on("blur", $.proxy(tktEmail.formatEmailsAdapter));

    t.setShow();
};

var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.data = config.data;
    t.config.star_data = false;
    t.token = $('head meta[name="csrf-token"]');
    t.original_data = config.data;

    t.page = $("#page_boxed");
    t.content = $(".content");
    t.mdl_popup_loader = t.page.find("#mdl_popup_loader");
    t.tkt_content = t.page.find('#tkt-content');
    t.main_attachments = t.tkt_content.find('.main_attachments');

    t.toggle_tiny_view = t.page.find('#toggle_tiny_view');
    t.toggle_ticket_detail = t.page.find('#toggleTicketDetail');
    t.toggle_conversation_view = t.page.find('#toggle_conversation_view');
    t.toggle_tiny_view_read = t.page.find('#toggle_tiny_view_read');
    t.actionBar = t.page.find('.tkd-action-bar');
    t.actionMore = t.actionBar.find('.js-action-more');

    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.getUserInitials = function (name) {
        var words = String(this.safeDisplayValue(name, "")).split(/\s+/).filter(Boolean);
        if (!words.length) return "NA";
        if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.safeDisplayValue = function (value, fallback) {
        return this.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getAvatarHtml = function (name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";
        var safeName = this.escapeHtml(this.safeDisplayValue(name, "User"));

        if (this.isFilledValue(imageUrl)) {
            return '<img src="' + this.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + this.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + this.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + this.escapeHtml(this.getUserInitials(name)) + '</span>';
    };

    t.getUserDropdownName = function (s) {
        return this.safeDisplayValue(s.text || s.full_name || s.fullname || s.name, "-");
    };

    t.getUserDropdownImage = function (s) {
        return this.safeDisplayValue(s.img_path || s.profile_img || s.gravatar || s.avatar, "");
    };

    t.userDropdownSelectionFormat = function (s) {
        if (!s || !s.id) return s && s.text ? s.text : "";

        var name = this.getUserDropdownName(s);
        var avatarHtml = this.getAvatarHtml(
            name,
            this.getUserDropdownImage(s),
            "user-list-avatar"
        );

        return $(
            '<div class="d-flex align-items-center gap-2 min-w-0">' +
                avatarHtml +
                '<span class="text-truncate">' + this.escapeHtml(name) + '</span>' +
            '</div>'
        );
    };

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + this.escapeHtml(s.text) + "</div>");
        }
        if (!s) return $("<div>No data</div>");

        var name = this.getUserDropdownName(s);
        var email = this.safeDisplayValue(s.email, "");
        var empNo = this.safeDisplayValue(s.employee_num || s.employee_no || s.emp_no, "");
        var tName = name.length > 35 ? name.substring(0, 35) + "..." : name;
        var tEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
        var avatarHtml = this.getAvatarHtml(
            name,
            this.getUserDropdownImage(s),
            "user-list-avatar"
        );

        var html = [
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + this.escapeHtml(tName) + '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' + this.escapeHtml(tEmail) + '</span>' : "",
                    empNo ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' + this.escapeHtml(empNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join("");

        return $(html);
    };

    t.timeline = t.page.find("#ticket_timeline");
    t.conversation = t.timeline.closest(".tkd-conversation");

    t.expireInfo = t.page.find("#expireInfo");
    t.workaroundInfo = t.page.find("#workaroundInfo");
    t.responseInfo = t.page.find("#responseInfo");
    t.frmUpdateStatus = t.page.find("#frm_update_status");
    t.frmUpdateStatus.el = {};
    t.frmUpdateStatus.el.id = t.frmUpdateStatus.find("#id");
    t.frmUpdateStatus.el.statusId = t.frmUpdateStatus.find("#status_id");
    t.frmUpdateStatus.el.priorityId = t.frmUpdateStatus.find("#status_priority_id");
    t.frmUpdateStatus.el.tat = t.frmUpdateStatus.find("#tat");
    t.frmUpdateStatus.el.comment = t.frmUpdateStatus.find("#status_comment, textarea[name='comment']").first();
    t.frmUpdateStatus.el.comment_cc = t.frmUpdateStatus.find('.comment_cc');
    t.frmUpdateStatus.el.follow_cc = t.frmUpdateStatus.find("#follow_cc");
    t.frmUpdateStatus.el.cc_emails = t.frmUpdateStatus.find('#update_cc_emails');
    t.frmUpdateStatus.el.is_workaround = t.frmUpdateStatus.find('#is_workaround');
    t.frmUpdateStatus.el.tmp_id = t.frmUpdateStatus.find("#tmp_id");
    t.frmUpdateStatus.attachment_dropper_cover = t.frmUpdateStatus.find('#update-dropper-cover');
    t.frmUpdateStatus.attachment_dropper = t.frmUpdateStatus.attachment_dropper_cover.find('#update-dropper');
    t.attachment_update = t.frmUpdateStatus.find("#attachment_updates");
    t.frmUpdateStatus.btnSubmit = t.frmUpdateStatus.closest(".panel, #updateTicket").find("#btnSubmit");
    t.frmUpdateStatus.el.revoke_access_div = t.frmUpdateStatus.find("#revoke_access_div");
    t.frmUpdateStatus.el.need_time_duration = t.frmUpdateStatus.find("#need_time_duration");
    t.frmUpdateStatus.el.start_time = t.frmUpdateStatus.find("#start_time");
    t.frmUpdateStatus.el.end_time = t.frmUpdateStatus.find("#end_time");
    t.frmUpdateStatus.commentWrapper = t.frmUpdateStatus.find('.final-comment-wrapper');
    // console.log(t.config.translations.enter_your_message);
    
    const summernoteOptions = {
        placeholder: t.config.translations.enter_your_message || t.summernoteOptions.placeholders,
        disableResizeEditor: true,
        tabsize: 2,
        height: 200,
        toolbar: [
            ['color', ['color']],
            ['font', ['bold', 'underline', 'italic', 'clear']],
            ['para', ['ol', 'ul']]
        ],
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $('.note-style .dropdown-toggle').html('Tt <span style="font-size:.65rem">&#9662;</span>');
            }
        }
    };
    t.summernoteOptions = summernoteOptions;
    t.getSummernoteOptions = function (overrides) {
        var options = $.extend(true, {}, t.summernoteOptions);
        if (typeof overrides == "object") {
            $.each(overrides, function (key, value) {
                if ($.isPlainObject(value) && $.isPlainObject(options[key])) {
                    options[key] = $.extend(true, {}, options[key], value);
                } else {
                    options[key] = value;
                }
            });
        }

        return options;
    };
    t.runSummernoteBaseInit = function () {
        if (t.summernoteOptions.callbacks && typeof t.summernoteOptions.callbacks.onInit == "function") {
            t.summernoteOptions.callbacks.onInit();
        }
    };
    t.initUpdateStatusSummernote = function () {
        var comment = t.frmUpdateStatus.el.comment;
        if (!comment.length) {
            return;
        }

        if (typeof $.fn.summernote === "undefined") {
            t.frmUpdateStatus.commentWrapper.addClass('sn-ready');
            return;
        }

        if (comment.next('.note-editor').length) {
            t.frmUpdateStatus.commentWrapper.addClass('sn-ready');
            return;
        }

        comment.summernote(t.getSummernoteOptions({
            placeholder: t.config.translations.enter_your_message || t.summernoteOptions.placeholder,
            dialogsInBody: true,
            focus: false,
            callbacks: {
                onInit: function () {
                    t.runSummernoteBaseInit();
                    t.frmUpdateStatus.commentWrapper.addClass('sn-ready');
                },
                onChange: function (contents) {
                    comment.val(contents);
                    t.frmUpdateStatus.find('#shows_error').html('');
                }
            }
        }));
    };

    t.frmComment = t.page.find("#frm_comment");
    t.frmComment.el = {};
    t.frmComment.el.id = t.frmComment.find("#id");
    t.frmComment.el.tmp_id = t.frmComment.find("#tmp_id");
    t.frmComment.el.comment = t.frmComment.find("#reply_comment, textarea[name='comment']").first();
    t.frmComment.el.is_note = t.frmComment.find("#is_note");
    t.frmComment.el.comment_cc = t.frmComment.find('.comment_cc');
    t.frmComment.el.follow_cc = t.frmComment.find("#follow_cc");
    t.frmComment.el.cc_emails = t.frmComment.find('#cc_emails');
    t.frmComment.attachment_dropper_cover = t.frmComment.find('#attachment-dropper-cover');
    t.frmComment.attachment_dropper = t.frmComment.attachment_dropper_cover.find('#attachment-dropper');
    t.frmComment.commentWrapper = t.frmComment.find('#reply-summernote-wrapper');
    t.attachment = t.frmComment.find("#attachments");
   t.frmComment.el.comment.summernote(summernoteOptions);
    

    t.mdlAssignTo = t.page.find("#mdl-assign-to");
    t.mdlAssignTo.title = t.mdlAssignTo.find(".modal-title");
    t.frmAssignTo = t.mdlAssignTo.find("#frm-assign-to");
    t.frmAssignTo.el = {};
    t.frmAssignTo.el.id = t.frmAssignTo.find("#id");
    t.frmAssignTo.el.token = t.frmAssignTo.find("#token");
    t.frmAssignTo.el.assigned_to = t.frmAssignTo.find("#user_assigned_to");
    t.mdlAssignTo.btnSubmit = t.mdlAssignTo.find("#btnSubmit");

    /* problem-mgt */
    t.mdladdProblem = t.page.find("#addproblemModal");
    t.mdladdProblem.title = t.mdladdProblem.find(".modal-title");
    t.frmaddProblem = t.mdladdProblem.find("#addproblem-mdl-frm");
    t.frmaddProblem.frmEl = {};
    t.frmaddProblem.frmEl.checkbox_value = t.mdladdProblem.find("#add-problem-mgt");
    t.frmaddProblem.frmEl.id = t.frmaddProblem.find("#ticket_id");
    t.frmaddProblem.frmEl.token = t.frmaddProblem.find("#token");
    t.frmaddProblem.frmEl.problem_mgt = t.frmaddProblem.find("#problem_mgt");
    t.mdladdProblem.btnSubmit = t.mdladdProblem.find("#btnSubmit");

    /* add incident mdl */
    t.mdladdIncident = t.page.find("#addincidentModal");
    t.mdladdIncident.title = t.mdladdIncident.find(".modal-title");
    t.frmaddIncident = t.mdladdIncident.find("#addincident-mdl-frm");
    t.frmaddIncident.frmEl = {};
    t.frmaddIncident.frmEl.checkbox_value = t.mdladdIncident.find("#add-incident");
    t.frmaddIncident.frmEl.id = t.frmaddIncident.find("#ticket_id");
    t.frmaddIncident.frmEl.token = t.frmaddIncident.find("#token");
    t.frmaddIncident.frmEl.incident = t.frmaddIncident.find("#incident");
    t.mdladdIncident.btnSubmit = t.mdladdIncident.find("#btnSubmit");

    /* change creator mdl */
    t.mdlChangeCreator = t.page.find("#mdl-change-creator");
    t.mdlChangeCreator.title = t.mdlChangeCreator.find(".modal-title");
    t.frmChangeCreator = t.mdlChangeCreator.find("#frm-change-creator");
    t.frmChangeCreator.el = {};
    t.frmChangeCreator.el.id = t.frmChangeCreator.find("#id");
    t.frmChangeCreator.el.token = t.frmChangeCreator.find("#token");
    t.frmChangeCreator.el.creator_id = t.frmChangeCreator.find("#creator_id");
    t.frmChangeCreator.btnSubmit = t.frmChangeCreator.find("#btnSubmit");
    t.js_act_change_creator = t.page.find('.js-act-change-creator');

    t.mdlTransfer = t.page.find("#ticketTransferModal");
    t.frmTransfer = t.mdlTransfer.find("#ticket-transfer-mdl-frm");
    t.frmTransfer.extraData = {};
    t.frmTransfer.el = {};
    t.mdlTransfer.title = t.frmTransfer.find('.modal-title');
    t.frmTransfer.el.id = t.frmTransfer.find("#id");
    t.frmTransfer.el.token = t.frmTransfer.find("#token");
    t.frmTransfer.el.department_id = t.frmTransfer.find("#transfer_department_id");
    t.frmTransfer.el.problem_category_id = t.frmTransfer.find("#transfer_problem_category_id");
    t.frmTransfer.el.sub_category_id_cvr = t.frmTransfer.find("#transfer_sub_category_id_cvr");
    t.frmTransfer.el.sub_category_id = t.frmTransfer.find("#transfer_sub_category_id");
    t.frmTransfer.el.priority_id = t.frmTransfer.find("#transfer_priority_id");
    t.frmTransfer.el.tat = t.frmTransfer.find("#tat");
    t.frmTransfer.el.token = t.frmTransfer.find("#token");
    t.frmTransfer.el.self_assign = t.frmTransfer.find("#self_assign");
    t.frmTransfer.el.assign_to_cvr = t.frmTransfer.find("#assigned_to_cvr");
    t.frmTransfer.el.assign_to = t.frmTransfer.find("#transfer_assigned_to");
    t.frmTransfer.el.device_id = t.frmTransfer.find("#transfer_device_id");
    t.frmTransfer.el.tags = t.frmTransfer.find("#transfer_tags");
    t.frmTransfer.el.delete_previous_tasks = t.frmTransfer.find("#remove_tasks");
    t.frmTransfer.el.btnSubmit = t.frmTransfer.find('#btnSubmit');

    t.frmTransfer.el.creatorId = t.frmTransfer.find("#creator_id");
    t.frmTransfer.el.remarks = t.frmTransfer.find("#remarks");
    t.frmTransfer.el.is_note = t.frmTransfer.find("#is_note");

    t.mdl = t.page.find("#mdl-feedback-update");
    t.mdl.frm = t.mdl.find("#frm-feedback-update");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.token = t.mdl.frm.find("#token");
    t.mdl.frmEl.remarks = t.mdl.frm.find("#remarks");
    t.mdl.frmEl.divQuestion = t.mdl.frm.find("#question");
    t.mdl.frmEl.divResult = t.mdl.frm.find("#result");
    t.mdl.frmEl.btnSubmit = t.mdl.frm.find(".js-act-update");

    t.mdlFeedback = t.page.find("#mdl-feedback");
    t.frmFeedback = t.mdlFeedback.find("#frm-feedback");
    t.frmFeedback.el = {};
    t.frmFeedback.el.id = t.frmFeedback.find("#id");
    t.frmFeedback.el.token = t.frmFeedback.find("#token");
    t.frmFeedback.el.remarks = t.frmFeedback.find("#remarks");
    t.frmFeedback.el.divQuestion = t.mdlFeedback.find("#question");
    t.frmFeedback.el.divResult = t.mdlFeedback.find("#result");
    t.frmFeedback.btnSubmit = t.mdlFeedback.find("#btnSubmit");
    t.frmFeedback.mdl_popup_loader = t.mdlFeedback.find("#mdl_popup_loader");
    t.lastFb = t.page.find("#last-feed-back");

    t.feedBackRating = t.page.find("#feedBackRating");
    t.feedBackRating.lfbImg = t.feedBackRating.find("#lfb-img");
    t.feedBackRating.lfbScore = t.feedBackRating.find("#lfb-score");
    t.feedBackRating.ofbImg = t.feedBackRating.find("#ofb-img");
    t.feedBackRating.ofbScore = t.feedBackRating.find("#ofb-score");
    t.feedBackRating.ifFBF = t.feedBackRating.find("#ifFBF");
    t.feedBackRating.elseFBF = t.feedBackRating.find("#elseFBF");

    /* mdl reopen elements binding with js */
    t.mdlReopen = t.page.find('#mdl-reopen');
    t.frmReopen = t.mdlReopen.find('#frm_reopen');
    t.frmReopen.el = {};
    t.frmReopen.el.comment = t.frmReopen.find('#comment');
    t.frmReopen.el.comment.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true
    });
    var summernoteConfig = {
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true
    };
    t.mdl.frmEl.remarks.summernote(summernoteConfig);
    t.frmFeedback.el.remarks.summernote(summernoteConfig);
    t.mdlReopen.btnSubmit = t.mdlReopen.find("#btnSubmit");

    /* mdl update ticket tags */
    t.mdlManageTags = t.page.find("#mdl-add-tag");
    t.mdlManageTags.assigned_tags = t.mdlManageTags.find("#assigned_tags");
    t.mdlManageTags.getTicketTags = t.page.find(".getTicketTags");
    t.mdlManageTags.saveTags = t.mdlManageTags.find("#btnSubmit");

    t.mdl_ticketsentiment = t.page.find("#ticketSentiment");
    t.mdlTicketsentiment_btnClose = t.mdl_ticketsentiment.find(".btn-close");
    t.mdl_ticketsentiment.sentimentText = t.mdl_ticketsentiment.find("#sentimentText");
    t.mdl_ticketsentiment.sentimentIcon = t.mdl_ticketsentiment.find("#sentimentIcon");
    t.mdl_ticketsentiment.explanationText = t.mdl_ticketsentiment.find("#explanationText");
    t.mdl_ticketsentiment.sentimentScore = t.mdl_ticketsentiment.find("#sentimentScore");
    t.mdl_ticketsentiment.sentimentIndicator = t.mdl_ticketsentiment.find("#sentimentIndicator");
    t.mdl_ticketsentiment.takenAction = t.mdl_ticketsentiment.find("#takenAction");

    t.mdl_openCanvas = t.page.find("#openCanvas");
    t.mdl_closeCanvas = t.page.find("#closeCanvas");
    t.mdl_topOffcanvas = t.page.find("#topOffcanvas");
    t.statusApprovalChangesTable = t.mdl_topOffcanvas.find('#statusApprovalChanges');
    t.statusApprovalSearchbox = t.mdl_topOffcanvas.find('.approval-searchbox');
    t.statusApprovalPageLength = t.mdl_topOffcanvas.find('.approval-page-length');
    t.mdl_openCanvas.click(function(e){
        e.preventDefault();
        t.loadCheckStatusApprovalList.ajax.reload();
        t.mdl_topOffcanvas.modal("show");
    });
    t.mdl_closeCanvas.click(function(){t.mdl_topOffcanvas.modal("hide");});

    t.loadCheckStatusApprovalList = t.statusApprovalChangesTable.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
           },
        ],
        order: [
            [1, 'desc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        searching: true,
        lengthChange: false,
        dom: "rtip",
        pageLength: parseInt(t.statusApprovalPageLength.val(), 10) || 10,
        ajax: {
            url: t.config.url.getTicketStatusApprovalList,
            type: "GET",
            data: function (d) {
                d._token = t.config.token;
                d.ticket_id = t.config.data.id;
            },
        },
        columns: [
            { data: 'company_name' },
            { data: 'department' },
            { data: 'problem_category' },
            { data: 'sub_category' },
            { data: 'requested_status' },
            { data: 'approval_status' },
            { data: 'start_time_format'},
            { data: 'end_time_format'}
        ],
        fnInitComplete: function (oSettings, json) {
            $("#statusApprovalChanges_wrapper").removeClass("form-inline");
            t.statusApprovalChangesTable.closest("div").addClass("table-responsive");
        }
    });

    t.searchStatusApprovalData = function (e) {
        var v = $.trim(t.statusApprovalSearchbox.val() || "");

        if (e) {
            e.preventDefault();
        }

        if (typeof $.fn.validate_str_param === "function") {
            v = t.statusApprovalSearchbox.validate_str_param();
        }

        if (v === false) {
            alert("Please enter a valid value for search");
            return false;
        }

        t.statusApprovalSearchbox.val(v);
        t.loadCheckStatusApprovalList.search(v).draw();
    };

    t.reloadStatusApprovalList = function (e) {
        if (e) {
            e.preventDefault();
        }

        t.statusApprovalSearchbox.val("");
        t.loadCheckStatusApprovalList.search("").draw();
    };

    t.changeStatusApprovalPageLength = function (e) {
        var length = parseInt(t.statusApprovalPageLength.val(), 10);

        if (e) {
            e.preventDefault();
        }

        if (length > 0) {
            t.loadCheckStatusApprovalList.page.len(length).draw(false);
        }
    };

    t.mdl_topOffcanvas.on('shown.bs.modal', function () {
        t.loadCheckStatusApprovalList.columns.adjust();
    });
    t.mdl_topOffcanvas.on('click', '.btn-searchbox-approval, .amg-list-searchbar__icon', $.proxy(t.searchStatusApprovalData));
    t.mdl_topOffcanvas.on('keyup', '.approval-searchbox', function (e) {
        if (e.which === 13) {
            t.searchStatusApprovalData(e);
        }

        if (!this.value.length && e.which !== 13) {
            t.searchStatusApprovalData(e);
        }
    });
    t.mdl_topOffcanvas.on('click', '.btn-reload-list-approval', $.proxy(t.reloadStatusApprovalList));
    t.mdl_topOffcanvas.on('change', '.approval-page-length', $.proxy(t.changeStatusApprovalPageLength));

    t.Ticketsentiment = function(e){
        e.preventDefault();
        var ticket_id = $(this).attr('data-ticket-id');
        var icon = '😠';
        var iconText = 'No Sentiment Detected';
        $.ajax({
            type: 'get',
            url: t.config.url.getSentiment+'/'+ticket_id,
            success: function (res) {
                if(res.status == 'danger'){
                    sweetAlert('center', 'error', res);
                }
                var data = res.data[0];
                if(res.status == 'success'){
                    if(data.ticket_sentiment == 1){
                        icon = '😊';
                        iconText = 'Positive Sentiment Detected';
                        sentiment_Text = 'Positive';
                    } else if (data.ticket_sentiment == 2){
                        icon = '😐';
                        iconText = 'Neutral Sentiment Detected';
                        sentiment_Text = 'Neutral';
                    } else if (data.ticket_sentiment == 3) {
                        icon = '😠';
                        iconText = 'Negative Sentiment Detected';
                        sentiment_Text = 'Negative';
                    } else {
                        icon = '😶';
                        iconText = 'No Sentiment Detected';
                        sentiment_Text = 'None';
                    }
                    var scrore = 'Score: ' + data.sentiment_score + '('+sentiment_Text+')';
                    const score = Math.max(-1, Math.min(1, data.sentiment_score));
                    const position = ((score + 1) / 2) * 100;
                    t.mdl_ticketsentiment.sentimentIndicator.css('left', position + '%');
                    t.mdl_ticketsentiment.sentimentText.text(iconText);
                    t.mdl_ticketsentiment.sentimentIcon.text(icon);
                    t.mdl_ticketsentiment.explanationText.text(data.sentiment_reson);
                    t.mdl_ticketsentiment.sentimentScore.text(scrore);
                    t.mdl_ticketsentiment.modal("show");
                }
            }
        });
    }

    t.mdlTicketsentiment_btnClose.on('click',function(){
        t.mdl_ticketsentiment.modal("hide");
    });

    t.mdlManageTags.assigned_tags.select2({
        dropdownParent:$("#mdl-add-tag"),
        placeholder: "Select Tags",
        width: "100%",
        tags: true,
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

    // Common summernote editor
   function initSummernote(element, placeholder = 'Enter Your Message', height = 140) {
        $(element).summernote({
            placeholder: placeholder,
            height: height,
            disableResizeEditor: true,
            disableDragAndDrop: true,
            tabsize: 2,
            toolbar: [
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['para', ['ul', 'ol']]
            ]
        });
    }

    t.frmTransfer.el.remarks.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 100,
        focus: true
    });

    t.mdlTask = t.page.find('#taskModal');
    t.frmTask = t.mdlTask.find('#task');
    t.frmTask.ticketId = t.frmTask.find("#ticket_id");
    t.frmTask.task_id = t.frmTask.find("#task_id");
    t.frmTask.name = t.frmTask.find('#name');
    t.frmTask.company = t.frmTask.find('#company');
    t.frmTask.due_date = t.frmTask.find('#due_date');
    t.frmTask.status_id = t.frmTask.find('#task_edit_status_id');
    t.frmTask.priority_id = t.frmTask.find('#priority_id');
    t.frmTask.cost = t.frmTask.find('#cost');
    t.frmTask.description = t.frmTask.find('#task_description');
    t.frmTask.departmentId = t.frmTask.find("#task_department_id");
    t.frmTask.problemCategoryId = t.frmTask.find("#task_problem_category_id");
    t.frmTask.subCategoryId = t.frmTask.find("#task_sub_category_id");
    t.frmTask.subCategoryIdCvr = t.frmTask.find("#task_sub_category_id_cvr");
    t.frmTask.assigned_to = t.frmTask.find('#task_assigned_to');
    t.frmTask.is_visible_user = t.frmTask.find('#is_visible_user');
    t.frmTask.ticketData = {};
    t.frmTask.btnSubmit = t.mdlTask.find("#taskSubmit");
    var assignToName = t.config.data.assignToName;
    var creator_id = t.config.data.creator_id;
    t.descriptonMdl = t.page.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    t.taskUpdateMdl = t.page.find("#task_update_details");
    t.frmUpdateTaskStatus = t.taskUpdateMdl.find("#frm_update_task_status");
    t.frmUpdateTaskStatus.el = {};
    t.frmUpdateTaskStatus.el.task_id = t.frmUpdateTaskStatus.find("#id");
    t.frmUpdateTaskStatus.el.statusWrapper = t.frmUpdateTaskStatus.find(".status-wrapper");
    t.frmUpdateTaskStatus.el.temp_id =  t.frmUpdateTaskStatus.find("#tmp_id");
    t.frmUpdateTaskStatus.el.task_status_id = t.frmUpdateTaskStatus.find('#task_status_id');
    t.frmUpdateTaskStatus.el.task_comment = t.frmUpdateTaskStatus.find("#task_comment")
    t.frmUpdateTaskStatus.el.btnSubmit = t.taskUpdateMdl.find("#btnSubmit");

    // start of task dropper
    t.frmUpdateTaskStatus.el.attachment_dropper_cover = t.frmUpdateTaskStatus.find('#update-dropper-cover');
    t.frmUpdateTaskStatus.el.attachment_dropper = t.frmUpdateTaskStatus.el.attachment_dropper_cover.find('#update-dropper');
    t.frmUpdateTaskStatus.el.attachment_updates = t.frmUpdateTaskStatus.find("#task_attachment_updates");
    t.frmUpdateTaskStatus.el.commentWrapper = t.frmUpdateTaskStatus.find('.final-comment-wrapper');

    t.optionsCompany = function() {
       t.frmTask.company.empty().append(new Option("Select Company", ""));
        $.each(t.config.companies, function(i,v) {
            if (companyId && companyId == v.id) {
               t.frmTask.company.append(new Option(v.text, v.id,true,true)).prop('disabled',true).trigger("change");
            }else{
               t.frmTask.company.append(new Option(v.text, v.id));
            }
        });
    };

    t.frmUpdateTaskStatus.el.task_comment.summernote({
        inheritPlaceholder: true,
        placeholder: "Enter comment",
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true,
        callbacks: {
            onChange: function (contents, $editable) {
                t.frmUpdateTaskStatus.el.task_comment.val(contents).valid();
            }
        }
    });

    t.resetTaskForm = function () {
        t.frmTask[0].reset();
        t.frmTask.ticketId.val("");
        t.frmTask.task_id.val("");
        t.frmTask.name.val("");
        t.frmTask.due_date.val("");
        if (t.dueDatePicker) {
            t.dueDatePicker.clear();
        }
        t.frmTask.company.val("")
        .val(null)
        .prop('disabled', false)
        .trigger("change");
        t.frmTask.description.summernote("code", "");
        t.frmTask.assigned_to.val(null).trigger("change");
        t.frmTask.is_visible_user.prop('checked', false);
        t.frmTaskValidator.resetForm();
    };

    t.frmTask.company.select2({
        width: "100%",
        placeholder: "Select Company",
        dropdownParent: $("#taskModal")
    });

    $.validator.addMethod("summernotedescription", function (value, element) {
        let content = $(element).summernote('code').replace(/(<([^>]+)>)/gi, "").trim();
        return content.length > 0;
    }, "This field is required.");


    initSummernote(
        t.frmTask.description,
        'Enter Task Description',
        140
    );

    function formatTaskDateTime(date) {
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0');
        let yyyy = date.getFullYear();

        let hours = String(date.getHours()).padStart(2, '0');
        let minutes = String(date.getMinutes()).padStart(2, '0');

        return `${dd}/${mm}/${yyyy} ${hours}:${minutes}`;
    }
    t.submitTask = function (e) {
        e.preventDefault();

        if (t.frmTask.btnSubmit.prop("disabled")) {
            return false;
        }
        t.frmTask.description.val(t.frmTask.description.summernote("code"));
        let formData = new FormData(t.frmTask[0]);
        let isEditMode = t.frmTask.task_id && t.frmTask.task_id.val() !== "";
        formData.append('type_id', 4);
        formData.append('status_id', t.frmTask.status_id.val() || 1);
        formData.append('company_id', t.frmTask.company.val() || 1);
        formData.append('priority_id', t.frmTask.priority_id.val() || 1);
        formData.append('cost', t.frmTask.cost.val() || (0).toFixed(2));
        formData.append('ticketmodule', 'true');
        
        if (!isEditMode) {
            let currentDate = new Date();
            formData.append('start_date', formatTaskDateTime(currentDate));
        }
        t.frmTask.btnSubmit.prop("disabled", true);
        let ajaxUrl = isEditMode ? t.config.url.ajaxEditTask + "/" + t.frmTask.task_id.val() : t.config.url.addTask;
        let http = $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdlTask.modal("hide");
                    t.relatedTask();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });

        http.fail(function (xhr) {
            var data = {
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });

        http.always(function () {
            t.frmTask.btnSubmit.prop("disabled", false);
        });
        return false;
    };

    t.frmTask.btnSubmit.on("click", $.proxy(t.submitTask));
    t.frmUpdateTaskStatus.el.btnSubmit.on("click", function (e) {
        e.preventDefault();

        if (!t.frmTaskStatusValidator.form()) {
            return false;
        }

        var formData = new FormData();
        formData.append('_token', t.config.token);
        formData.append('id', t.frmUpdateTaskStatus.el.task_id.val() || "");
        formData.append('status_id', t.frmUpdateTaskStatus.el.task_status_id.val() || 1);
        formData.append('task_comment', t.frmUpdateTaskStatus.el.task_comment.val() || "");
        formData.append('temp_id', t.frmUpdateTaskStatus.el.temp_id.val() || "");
        formData.append('type_id', 4);
        formData.append('ticketmodule', true);

            let http = $.ajax({
            url: t.config.url.ajaxStatusEditTask + "/" + t.frmUpdateTaskStatus.el.task_id.val(),
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status === "success") {
                    sweetAlert('center', 'success', data);
                    t.taskUpdateMdl.modal("hide");
                    t.frmUpdateTaskStatus.el.attachment_updates.empty();
                    t.refreshTaskTimeLine();
                    t.relatedTask();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });

        http.fail(function (xhr) {
            var data = {
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });

        http.always(function () {
            t.frmUpdateTaskStatus.el.btnSubmit.prop("disabled", false);
        });
    });
    t.frmTaskValidator = t.frmTask.validate({
        ignore: [],
        debug: false,
        rules: {
            name: {
                required: true,
                maxlength: 255,
                clean_text_only: true,
                noSpecialStart: true
            },
            description: {
                required: true,
                summernotedescription: true,
                maxSummernoteChars: 2000
            },
            cost: {
                number: {
                    depends: function () {
                        return t.frmTask.task_id && t.frmTask.task_id.val() !== "";
                    }
                }
            },due_date: {
                required: true,
            }
        }, 
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'task_description') {
                error.insertAfter(element.parent().find('.note-editor'));
            }
            else if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            }
            else {
                error.insertAfter(element);
            }
        }
    });

    t.frmTaskStatusValidator = t.frmUpdateTaskStatus.validate({
        ignore: [],
        debug: false,
        rules: {
            task_update_comment: {
                required: true,
                summernotedescription: true,
                maxSummernoteChars: 2000
            }
        }, errorPlacement: function (error, element) {
            error.appendTo(element.parent('div'));
        }

    });

    var problem_categories = [];
    t.fun = {
        reload_problem_category: function (element) {
            var department = t.frmTask.departmentId.val();
            t.frmTask.subCategoryIdCvr.addClass('hide');
            t.frmTask.problemCategoryId.empty().append(new Option("", "", false, false));
            t.frmTask.subCategoryId.empty().append(new Option("", "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            if (k.status != 0) {
                                let truncatedName = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                                let option = new Option(truncatedName, k.id, false, false);
                                $(option).attr("title", k.name);
                                t.frmTask.problemCategoryId.append(option);
                            }
                        });
                        if (t.config.parent_Category && t.config.parent_Category_id && t.config.add) {
                            t.frmTask.problemCategoryId.val(t.config.parent_Category_id).trigger('change');
                        }else if(t.config.edit_pc_id && t.config.edit_pc_name && t.config.edit){
                            t.frmTask.problemCategoryId.val(t.config.edit_pc_id).trigger('change');
                        }
                    }
                });
            }
        },
        reload_sub_category: function (element){
            let selectedPcIds = t.frmTask.problemCategoryId.val() || [];

            t.frmTask.subCategoryId.empty().append(new Option("", "", false, false));
            if (!selectedPcIds.length) {
                t.frmTask.subCategoryIdCvr.addClass('hide');
                return;
            }
            let item = problem_categories.find(pc => pc.id == selectedPcIds);
            let hasSubCategory = item && Array.isArray(item.sub) && item.sub.length > 0;
            if (hasSubCategory) {
                t.frmTask.subCategoryIdCvr.removeClass('hide');
                if(t.config.sub_Category != null && t.config.sub_Category_id != null && t.config.add){
                    t.config.add = false;
                    if (!t.frmTask.subCategoryId.find("option[value='" + t.config.sub_Category_id + "']").length) {
                        const option = new Option(t.config.sub_Category, t.config.sub_Category_id, true, true);
                        t.frmTask.subCategoryId.append(option);
                        t.frmTask.subCategoryId.val(t.config.sub_Category_id).trigger('change');
                    }
                }
                else if(t.config.edit_spc_name && t.config.edit_spc_id && t.config.edit){
                    if (!t.frmTask.subCategoryId.find("option[value='" + t.config.edit_spc_id + "']").length) {
                        t.config.edit = false;
                        const option = new Option(t.config.edit_spc_name, t.config.edit_spc_id, true, true);
                        t.frmTask.subCategoryId.append(option);
                    }
                    t.frmTask.subCategoryId.val(t.config.edit_spc_id).trigger('change');
                }
                else{
                    t.frmTask.subCategoryId.trigger('change');
                }
            } else {
                t.frmTask.subCategoryIdCvr.addClass('hide');
                t.frmTask.subCategoryId.empty().append('Select Sub Category', "", false, false);
            }
        }
    }
    t.frmTask.assigned_to.select2($.extend({}, {
        dropdownParent: t.mdlTask,
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    context: 'task',
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            }
        },
        allowClear: true,
        placeholder: "Select Assign to",
        templateResult: function (data) {
            if (!data || !data.id) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.userDropdownSelectionFormat(data);
        },
    }));

    document.addEventListener('click', function (e) {
        if (e.target.closest('.task-card-header')) {
            const taskCard = e.target.closest('.task-card');
            if (taskCard) {
                taskCard.classList.toggle('collapsed');
                const trigger = taskCard.querySelector('.task-header-left');
                if (trigger) {
                    trigger.setAttribute('aria-expanded', String(!taskCard.classList.contains('collapsed')));
                }
            }
        }
    });
    t.dueDatePicker = t.frmTask.due_date.flatpickr({
        enableTime: true,
        time_24hr: true,
        dateFormat: "d/m/Y H:i",
        minuteIncrement: 15,
        allowInput: false,
    });
    t.config.add = false;
    t.config.edit = false;
    $(document).on("click", '.btn-add-task', function () {
        t.config.add = true;
        t.config.edit = false;
        t.resetTaskForm();
        t.mdlTask.find('.modal-title').text(t.config.translations.add_task);
        t.frmTask.ticketId.val(t.config.data.id);
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        t.dueDatePicker.set('minDate', tomorrow);
        t.dueDatePicker.setDate(tomorrow, true);
        if (t.config.data.assigned_to) {
            var assignedUser = {
                id: t.config.data.assigned_to,
                text: assignToName,
            };
            if (assignedUser.id && assignedUser.text) {
                var option = new Option(assignedUser.text, assignedUser.id, true, true);
                t.frmTask.assigned_to.append(option).trigger("change");
            }
        }
        t.frmTask.status_id.closest('.amg-form-field-row').addClass('d-none');
        t.frmTask.priority_id.closest('.amg-form-field-row').addClass('d-none');
        t.frmTask.btnSubmit.text('Save');
        if(t.config.department){
            let option = new Option(t.config.department, t.config.department_id, true, true);
            t.frmTask.departmentId.append(option).trigger('change');
        }
        t.optionsCompany();
        t.mdlTask.modal("show");
    });
    var assign_to;
    t.taskBootstrapIconClass = function (iconClass) {
        const iconMap = {
            'fa-thumbs-up': 'bi-hand-thumbs-up-fill',
            'fa-thumbs-down': 'bi-hand-thumbs-down-fill',
            'fa-warning': 'bi-exclamation-triangle-fill',
            'fa-exclamation-triangle': 'bi-exclamation-triangle-fill',
            'fa-minus-circle': 'bi-dash-circle-fill',
            'fa-circle': 'bi-circle-fill'
        };

        return iconMap[iconClass] || 'bi-circle-fill';
    };
    t.taskBootstrapIconClass = function (iconClass) {
        const iconMap = {
            'fa-thumbs-up': 'bi-hand-thumbs-up-fill',
            'fa-thumbs-down' : 'bi-hand-thumbs-down-fill',
            'fa-warning': 'bi-exclamation-triangle-fill',
            'fa-exclamation-triangle' : 'bi-exclamation-triangle-fill',
            'fa-minus-circle': 'bi-dash-circle-fill',
            'fa-circle': 'bi-circle-fill'
        };
        return iconMap[iconClass] || 'bi-circle-fill';
    };

    t.relatedTask = function (e) {
        $.ajax({
            url : t.config.url.getRelatedTask,
            type : "GET",
            data : {
                ticket_id : t.config.data.id,
                search : t.config.search,
                filters : t.config.export_filters
            },
            success: function (response) {
                var taskCard = t.page.find(".task-card.current").first();
                if (!taskCard.length) { return; }

                if (response.data && response.data.length > 0) {
                    var userDepartments = t.config.user_privilege_departments;

                    let visibleTasks = response.data.filter(function (task) {
                        if (t.config.user.id === t.config.data.creator_id) {
                            return task.is_visible_user == 1;
                        }
                        return true;
                    });

                    if (visibleTasks.length > 0) {
                        let totalTask     = visibleTasks.length;
                        let completedTask = visibleTasks.filter(function (task) {
                            return [4, 7, 9].includes(parseInt(task.status_id, 10));
                        }).length;

                        let tabNavItems = ''; 
                        let tabPanels = '';

                        visibleTasks.forEach(function (task, index) {
                            let activeClass= index === 0 ? 'active'      : '';
                            let showClass = index === 0 ? 'show active' : '';
                            let iconClass = t.taskBootstrapIconClass(task.progress.iconClass || 'fa-circle');
                            let borderColor = task.progress.borderColor || '#000';
                            let per = task.progress.per || '0';

                            var has_access = false;
                            if (
                                t.config.user.id == t.config.data.creator_id ||
                                t.config.user.id == t.config.data.assigned_to ||
                                task.department_id == null
                            ) {
                                has_access = true;
                            } else if (
                                t.config.user.role === 'SuperAdmin' ||
                                t.config.user.role === 'Admin' ||
                                t.config.access_privilege == '1'
                            ) {
                                has_access = userDepartments.includes(task.department_id);
                            }

                            tabNavItems += `
                                <li class="nav-item task-tab ${!has_access ? 'no-access-tab' : ''}">
                                    <a class="nav-link task-tab-link ${activeClass}"
                                        id="tab-trigger-${task.task_no}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#task-pane-${task.task_no}"
                                        role="tab"
                                        aria-controls="task-pane-${task.task_no}"
                                        aria-selected="${index === 0 ? 'true' : 'false'}"
                                        data-task-no="${task.task_no}"
                                        data-task-name="${task.task_name}"
                                        data-assign-to="${task.assigned_to_id}"
                                        data-status="${task.status}"
                                        data-color="${borderColor}"
                                        data-creator="${task.creator_id}"
                                        data-status-id="${task.status_id}"
                                        data-change_by_module="${task.change_by_module}"
                                        data-encrypt-task-id="${task.task_no}"
                                        data-icon-color="${borderColor}">
                                        #${task.task_no}
                                        <i class="bi ${iconClass}"
                                            style="margin-left:5px; color:${borderColor};"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="${task.progress.status || 'No Status'}"></i>
                                    </a>
                                </li>`;

                            if (has_access) {
                                let createdByModuleHtml = '';
                                if (t.config.parent_Category === 'GitHub Access' &&['Internal-LTTS', 'External-Public', 'External-Customer'].includes(t.config.sub_Category) &&['ltts'].includes(t.config.client) &&['dev', 'live'].includes(t.config.sub_client)
                                ) {
                                    createdByModuleHtml = `<div>Created By Module: <span class="fw-bold">System</span></div>`;
                                } else {
                                    let moduleLabel = task.change_by_module === 3 ? 'Problem Category': task.change_by_module === 2 ? 'Ticket' : task.change_by_module === 1 ? 'Task' : '';
                                    createdByModuleHtml = `<div>Created By Module: <span class="fw-bold">${moduleLabel}</span></div>`;
                                }
                                let costHtml = t.config.client !== 'ltts' ? `
                                    <div class="timeline-block">
                                        <p class="icon">
                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g opacity="0.4">
                                                <circle cx="10.2144" cy="10.3281" r="9.5" fill="white" stroke="#E3200B"/>
                                                <path d="M14.0654 4.87109L13.4195 6.51739H11.7334C11.9631 6.7962 12.0751 7.10365 12.1325 7.4645H14.0654L13.4195 9.11068H12.1327C11.9895 9.84064 11.6151 10.3181 11.0758 10.7398C10.4516 11.1978 9.73822 11.4381 9.02853 11.5046L13.0561 15.785H10.4365L6.75309 11.6242V9.97748C7.5935 9.96075 8.23574 10.0517 8.97846 9.81855C9.4104 9.66767 9.77855 9.52676 9.90659 9.11079H6.36328L7.00917 7.46482H9.89929C9.74326 7.02998 9.39979 6.75148 9.00923 6.64768C8.47755 6.52243 7.93794 6.51814 7.43286 6.5176H6.36328L7.00917 4.87142L14.0654 4.87109Z" fill="#E3200B"/>
                                                </g>
                                            </svg>
                                        </p>
                                        <p class="label">Cost</p>
                                        <p class="date" style="padding-left:43px;">${task.cost || '0.00'}</p>
                                    </div>` : '';

                                tabPanels += `
                                    <div class="tab-pane fade  ${showClass}" id="task-pane-${task.task_no}" role="tabpanel" aria-labelledby="tab-trigger-${task.task_no}">
                                        <!-- action buttons placeholder (filled by activateTaskTab) -->
                                        <div class="tab-button" id="taskbutton-${task.task_no}"></div>
                                        <div class="task-pane-inner d-flex order-2">
                                            <!-- ── LEFT (top-left box) ── -->
                                            <div class="box-container flex-grow-1 left-side-task-card">
                                                <div class="box top-left">
                                                    <div class="task-details">
                                                        <div class="col-md-5">
                                                            <div class="task-info">
                                                                <p class="task-label">Task Name</p>
                                                                <p class="task-name"
                                                                    data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="${task.task_name}">
                                                                    ${task.task_name && task.task_name.length > 25
                                                                        ? task.task_name.substring(0, 25) + '…'
                                                                        : task.task_name || 'No Task Name'}
                                                                </p>
                                                            </div>
                                                            ${createdByModuleHtml}
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="task-meta">
                                                                <div class="task-progress">
                                                                    <span data-bs-toggle="tooltip"
                                                                          data-bs-placement="top"
                                                                          title="${per}%">
                                                                        <svg width="40" height="40" viewBox="0 0 36 36" class="circular-progress">
                                                                            <circle class="circle-bg" cx="18" cy="18" r="16" stroke-width="4"></circle>
                                                                            <circle class="circle" cx="18" cy="18" r="16" stroke-width="3.8"
                                                                                stroke="${per > 0 ? borderColor : 'none'}"
                                                                                stroke-dasharray="${per > 0 ? per + ', 100' : '0, 100'}"
                                                                                stroke-dashoffset="${per > 0 ? 100 - per : 100}">
                                                                            </circle>
                                                                            <foreignObject x="10" y="10" width="16" height="16">
                                                                                <i class="bi ${iconClass}"
                                                                                    style="color:${borderColor}; font-size:16px; display:block; text-align:center;"></i>
                                                                            </foreignObject>
                                                                        </svg>
                                                                    </span>
                                                                    <span class="progress-text">${task.progress.status}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="task-details">
                                                        <div class="col-md-5">
                                                            <div class="task-info">
                                                                <p class="task-label">Status</p>
                                                                <span class="task-status" style="background:#DBFFEC; color:#0B8431">${task.status || 'No Status'}</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="task-assignee">
                                                                <div class="assignee-info">
                                                                    ${task.assign_to_avatar
                                                                        ? `<img src="${task.assign_to_avatar}" alt="${task.assigned_to}" class="assignee-avatar">`
                                                                        : ''}
                                                                    <div class="assignee-details">
                                                                        <p class="task-label">Assign to</p>
                                                                        <span class="assignee-name"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-placement="top"
                                                                            title="${task.assigned_to && task.assigned_to.trim() !== '' ? task.assigned_to : 'NA'}">
                                                                            ${task.assigned_to && task.assigned_to.trim() !== ''
                                                                                ? (task.assigned_to.length > 20 ? task.assigned_to.substring(0, 20) + '…' : task.assigned_to)
                                                                                : 'NA'}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="box bottom-left">
                                                    <div class="task-details">
                                                        <div class="col-md-5">
                                                            <div class="timeline-vertical">
                                                                <div class="timeline-item">
                                                                    <div class="timeline-icon">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="timeline-content">
                                                                        <p class="timeline-lebal">Start Date</p>
                                                                        <p class="timeline-date">${task.start_date}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="timeline-item">
                                                                    <div class="timeline-icon">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="timeline-content">
                                                                        <p class="timeline-lebal">End Date</p>
                                                                        <p class="timeline-date">${task.due_date}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <div class="timeline-vertical">
                                                                <div class="timeline-item">
                                                                    <div class="timeline-icon">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="timeline-content">
                                                                        <p class="timeline-lebal">Created At</p>
                                                                        <p class="timeline-date">${task.created_at}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="timeline-item">
                                                                    <div class="timeline-icon">
                                                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="timeline-content">
                                                                        <p class="timeline-lebal">Updated at</p>
                                                                        <p class="timeline-date">${task.updated_at}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div><!-- /box-container -->

                                            <!-- ── RIGHT (top-right box) ── -->
                                            <div class="box top-right right-task-box">
                                                <div style="padding:22px;">
                                                    <p class="task-label" style="margin-bottom:5px;">Priority</p>
                                                    <span class="task-priority">${task.priority || 'NA'}</span>
                                                </div>
                                                <div style="padding:9px 0px 3px 10px">
                                                    <div class="assignee-info">
                                                        ${task.creator_avatar
                                                            ? `<img src="${task.creator_avatar}" alt="${task.creator_avatar}" class="assignee-avatar">`
                                                            : ''}
                                                        <div class="assignee-details">
                                                            <p class="task-label">Creator</p>
                                                            <span class="assignee-name"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="${task.creator && task.creator.trim() !== '' ? task.creator : 'NA'}">
                                                                ${task.creator && task.creator.trim() !== ''
                                                                    ? (task.creator.length > 20 ? task.creator.substring(0, 20) + '…' : task.creator)
                                                                    : 'NA'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="box bottom-right">
                                                    <div class="timeline-block">
                                                        <div class="icon">
                                                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g opacity="0.4">
                                                                <circle cx="10.2144" cy="10.3281" r="9.5" fill="white" stroke="#E3200B"/>
                                                                <path d="M10.2144 4.32812C9.42642 4.32812 8.64621 4.48332 7.91825 4.78485C7.1903 5.08638 6.52887 5.52833 5.97171 6.08548C4.8465 7.2107 4.21436 8.73683 4.21436 10.3281C4.21436 11.9194 4.8465 13.4455 5.97171 14.5708C6.52887 15.1279 7.1903 15.5699 7.91825 15.8714C8.64621 16.1729 9.42642 16.3281 10.2144 16.3281C11.8057 16.3281 13.3318 15.696 14.457 14.5708C15.5822 13.4455 16.2144 11.9194 16.2144 10.3281C16.2144 9.54019 16.0592 8.75998 15.7576 8.03202C15.4561 7.30407 15.0141 6.64264 14.457 6.08548C13.8998 5.52833 13.2384 5.08638 12.5105 4.78485C11.7825 4.48332 11.0023 4.32813 10.2144 4.32812ZM12.7344 12.8481L9.61436 10.9281V7.32812H10.5144V10.4481L13.2144 12.0681L12.7344 12.8481Z" fill="#E3200B"/>
                                                                </g>
                                                            </svg>
                                                        </div>
                                                        <div class="label">Actual End Date</div>
                                                        <div class="date">
                                                            ${task.end_date && task.end_date != ' ' ? task.end_date : 'Not Available'}
                                                        </div>
                                                    </div>
                                                    ${costHtml}
                                                </div>
                                            </div><!-- /top-right box -->
                                        </div><!-- /task-pane-inner -->
                                        <!-- timeline + comment section placeholder -->
                                        <div class="tab-comment order-3" id="taskComment-${task.task_no}">
                                            <div id="task_timeline_${task.task_no}" class="timeline no-bg hide" style="margin-top:10px"></div>
                                        </div>

                                    </div>`;

                            } else {
                                tabPanels += `
                                    <div class="tab-pane fade ${showClass}"
                                        id="task-pane-${task.task_no}"
                                        role="tabpanel"
                                        aria-labelledby="tab-trigger-${task.task_no}">
                                        <div class="box">
                                            <h4>You do not have access to this task</h4>
                                        </div>
                                    </div>`;
                            }
                        });

                        let addTaskBtn = '';
                        if (
                            t.config.data.assigned_to == t.config.user.id &&
                            t.config.data.status_id !== 5 &&
                            t.config.data.status_id !== 6 &&
                            t.config.data.status_id !== 10 &&
                            jQuery.inArray('TaskAdd', t.config.permissions) !== -1
                        ) {
                            addTaskBtn = `
                                <div id="expandTabs">
                                    <span class="btn-add-task">
                                        <i class="bi bi-plus-square-dotted"></i> Add Task
                                    </span>
                                </div>`;
                        }

                        let sectionHtml = `
                            <div class="task-tabs-container">
                                <ul class="nav nav-tabs task-tabs" id="taskTabs" role="tablist">
                                    ${tabNavItems}
                                </ul>
                                ${addTaskBtn}
                            </div>
                            <div class="tab-content" id="taskTabContent">
                                ${tabPanels}
                            </div>`;

                        let completedPercentage = totalTask > 0
                            ? Math.round((completedTask / totalTask) * 100)
                            : 0;
                        taskCard.find('.task-percentage').html(completedPercentage + '%');
                        taskCard.find('> .progress-bar .fill').css('width', completedPercentage + '%');
                        taskCard.find('.total-task').html(totalTask);

                        taskCard.find('.task-section').html(sectionHtml);

                        taskCard.find('[data-bs-toggle="tooltip"]').each(function () {
                            new bootstrap.Tooltip(this, {
                                boundary  : document.body,
                                offset    : [0, 10],
                                container : 'body',
                                placement : 'auto'
                            });
                        });
                        t.triggerFirstTab(taskCard);
                    } else {
                        t._renderEmptyTask(taskCard);
                    }
                } else {
                    t._renderEmptyTask(taskCard);
                }
            },
            error: function (error) {
                console.log('Error fetching tasks:', error);
            }
        });
    };

    t._renderEmptyTask = function (taskCard) {
        let addTaskBtn = '';
        if (
            t.config.data.assigned_to == t.config.user.id &&
            t.config.data.status_id !== 5 &&
            t.config.data.status_id !== 6 &&
            t.config.data.status_id !== 10 &&
            jQuery.inArray('TaskAdd', t.config.permissions) !== -1
        ) {
            addTaskBtn = `
                <div id="expandTabs">
                    <span class="btn-add-task">
                        <i class="bi bi-plus-square-dotted"></i> Add Task
                    </span>
                </div>`;
        }
        taskCard.find('.task-section').html(
            addTaskBtn +
            `<div class="task-empty-wrapper">
                <img src="${t.config.url.image}" alt="No tasks" class="task-empty-img">
                <p class="task-empty-text">Related task not found</p>
            </div>`
        );
        taskCard.find('.task-percentage').html('0%');
        taskCard.find('> .progress-bar .fill').css('width', '0%');
        taskCard.find('.total-task').html(0);
    };

    $(document).on('shown.bs.tab', '.task-tab-link', function (e) {
        var $tab = $(e.target);
        t.activateTaskTab($tab);
        t.frmTaskComment && t.frmTaskComment.el && t.frmTaskComment.el.comment &&
        t.frmTaskComment.el.comment.val('').summernote('code', '');
        var $form = $tab.closest('.task-card').find('#frm_task_comment');
        $form.find('#shows_error').html('');
        t.taskAttachment && t.taskAttachment.empty();
    });

    t.activateTaskTab = function ($tab) {
        var taskCard = $tab.closest('.task-card');
        var has_no_access = $tab.parent().hasClass('no-access-tab');
        t.taskNo = $tab.data('task-no');
        var assign_to  = $tab.data('assign-to');
        t.taskStatus = $tab.data('status');
        t.statusId = $tab.data('status-id');
        t.taskCreator = $tab.data('creator');
        t.change_by_module = $tab.data('change_by_module');
        t.encrypt_task_id = $tab.data('encrypt-task-id');
        taskCard.find('.task-tab-link').each(function () {
            var $link  = $(this);
            var iconColor  = $link.data('icon-color') || '#000';
            $link.closest('.task-tab').css({ 'background-color': '', 'color': '' });
            $link.find('i').css('color', iconColor);
        });
        var color = $tab.data('color') || '#d9534f';
        $tab.closest('.task-tab').css({ 'background-color': color, 'color': '#fff' });
        $tab.find('i').css('color', '#fff');

        t.taskTimeline = taskCard.find('#task_timeline_' + t.taskNo);

        taskCard.find('.comment-section').remove();

        let taskbutton = `<div class="task-actions mt-2 d-flex gap-2 text-end p-2">`;

        if (t.config.data.status_id !== 5 && t.config.data.status_id !== 6 && t.config.data.status_id !== 10) {
            if (jQuery.inArray('TaskEdit', t.config.permissions) !== -1 &&
                (assign_to == t.config.user.id || t.config.data.assigned_to == t.config.user.id)) {
                taskbutton += `<button class="btn dtActbtn edit-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Edit Task"><i class="bi bi-pencil-square"></i></button>`;
            }
            if ((t.config.data.assigned_to == t.config.user.id ||
                 assign_to == t.config.user.id ||
                 (t.taskCreator == t.config.user.id && t.change_by_module != 3)) &&
                jQuery.inArray('TaskDelete', t.config.permissions) !== -1) {
                taskbutton += `<button class="btn dtActbtn delete-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Delete Task"><i class="bi bi-trash"></i></button>`;
            }
        }

        if (jQuery.inArray('TaskHistory', t.config.permissions) !== -1) {
            taskbutton += `<button type="button" class="btn dtActbtn history-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Task History"><i class="bi bi-clock-history"></i></button>`;
        }

        if (t.config.data.status_id !== 5 && t.config.data.status_id !== 6 && t.config.data.status_id !== 10 &&
            (assign_to == t.config.user.id ||
             (t.config.client !== 'ltts' && t.config.data.assigned_to == t.config.user.id))) {
            taskbutton += `<button class="btn dtActbtn update-task-status" data-id="${t.taskNo}" data-status="${t.statusId}" data-bs-toggle="tooltip" title="Update Status"><i class="bi bi-folder-check"></i></button>`;
        }

        if (t.config.data.status_id !== 5 && t.config.data.status_id !== 6 && t.config.data.status_id !== 10 &&
            (assign_to == t.config.user.id || t.config.data.assigned_to == t.config.user.id || t.config.data.creator_id == t.config.user.id) &&
            (t.taskStatus == 'Not Applicable' || t.taskStatus == 'Completed')) {
            taskbutton += `<button class="btn dtActbtn incomplete-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Reopen Task"><i class="bi bi-folder-symlink"></i></button>`;
        }

        taskbutton += `<button class="btn dtActbtn"><a href="${t.config.url.task_info}/${t.encrypt_task_id}" target="_blank" class="btn dtActbtn info-task" data-bs-toggle="tooltip" title="Task Info"><i class="bi bi-info-circle"></i></a></button>`;

        if (t.taskStatus !== 'Completed' &&
            ![5, 6, 10].includes(t.config.data.status_id) &&
            t.taskStatus !== 'Not Applicable' &&
            assign_to !== t.config.user.id &&
            t.config.authuser_isTechnician == 1) {
            taskbutton += `<button class="btn dtActbtn self-assign-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Self Assign Task"><i class="bi bi-person-plus"></i></button>`;
        }
        taskbutton += `</div>`;

        var $pane = taskCard.find('#task-pane-' + t.taskNo);
        if (!has_no_access) {
            $pane.find('#taskbutton-' + t.taskNo).html(taskbutton);
        } else {
            $pane.find('#taskbutton-' + t.taskNo).empty();
        }

        $pane.find('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this, {
                boundary  : document.body,
                offset    : [0, 10],
                container : 'body',
                placement : 'auto'
            });
        });

        let taskComment = '';
        let task_internal_div = '';

        if (t.config.user.role !== 'User' ||
            (t.config.user.role === 'User' && assign_to === t.config.user.id)) {
            task_internal_div = `
                <label for="task_internal_note" class="comment_cc">
                    <input type="checkbox" id="task_internal_note" name="task_internal_note" value="1">
                    Make it note for internal purpose
                </label>`;
        }

        if (t.taskStatus !== 'Completed' &&
            ![5, 6, 10].includes(t.config.data.status_id) &&
            t.taskStatus !== 'Not Applicable') {
            taskComment = `
                <div class="comment-section" style="margin-top:5%">
                    <form name="frm_task_comment" id="frm_task_comment" action="#" class="form-horizontal">
                        <input type="hidden" id="id" name="id" value="${t.taskNo}"/>
                        <input type="hidden" id="tmp_id" name="tmp_id" value="" />
                        <div class="card bg-white rounded">
                            <div class="form-group pad-hor">
                                <textarea id="task_comment" name="comment" class="form-control" rows="2"
                                    placeholder="${t.config.translations.comment_summer}"></textarea>
                                <div id="shows_error"></div>
                            </div>
                        </div>

                        <div class="task_attachment_wrapper" style="margin-top:1%">
                            <div id="task-attachment-dropper-cover"
                                class="amg-uploader tkd-comment-uploader"
                                data-amg-uploader
                                data-multiple="true"
                                data-auto-upload="true"
                                data-max-files="5"
                                data-max-size="10"
                                data-accept=".jpg,.jpeg,.png,.gif,.xls,.xlsx,.doc,.docx,.ppt,.pdf,.txt,.msg,.zip,.psd,.csv,.eml,.mp4"
                                data-upload-url="${t.config.url.add_task_attachment}"
                                data-remove-url="${t.config.url.remove_task_attachment}"
                                data-token="${t.config.token}"
                                data-record-input="#frm_task_comment #tmp_id"
                                data-extra-inputs="#frm_task_comment #id"
                                data-record-id="${t.taskNo}"
                                data-upload-field="attachment">

                                <label class="tkd-upload-label mt-3 mb-1">
                                    ${t.config.translations.Attachment || 'Attachment'}
                                </label>
                                <input type="file"  class="amg-uploader__input" multiple hidden>
                                <div class="amg-uploader__dropzone">
                                    <div class="amg-uploader__message" style="margin:0 auto;>
                                        <span class="amg-uploader__icon-wrap">
                                            <i class="bi bi-cloud-upload"></i>
                                        </span>
                                        <span>
                                            ${t.config.translations.upload_note}
                                            ${t.config.translations.or || 'or'}
                                            <span class="amg-uploader__hint">
                                                ${t.config.translations.drop_files_here || 'Drop files here'}
                                            </span>
                                        </span>
                                    </div>
                                    <button type="button" class="amg-btn amg-btn-outline amg-btn-sm amg-uploader__trigger">
                                        ${t.config.translations.Add_Attachment}
                                    </button>
                                </div>

                                <div class="amg-uploader__preview tkd-upload-preview"></div>
                                <div class="amg-uploader__error"></div>
                            </div>
                        </div>

                        <div class="mar-top clearfix d-flex align-items-center" style="color:#4a4a4a;">
                            ${task_internal_div}
                            <button class="amg-btn amg-btn-primary amg-btn-sm ms-auto"
                                type="button" id="btnSubmit"
                                style="border-radius:5px; font-size:13px; font-weight:bold;">
                                <i class="bi bi-send"></i> ${t.config.translations.comment}
                            </button>
                        </div>
                    </form>
                    <input type="file" id="task_attachment" name="task_attachment" style="visibility:hidden" />
                </div>`;
        }
        t.taskTimeline.after(taskComment);
        AMGDragDrop.initAll(document);
        t.frmTaskComment = $pane.find('#frm_task_comment');
        t.frmTaskComment.el = {};
        t.frmTaskComment.el.id  = t.frmTaskComment.find('#id');
        t.frmTaskComment.el.tmp_id = t.frmTaskComment.find('#tmp_id');
        t.frmTaskComment.el.comment  = t.frmTaskComment.find('#task_comment');
        t.frmTaskComment.attachment_dropper_cover = t.frmTaskComment.find('#task-attachment-dropper-cover');
        t.frmTaskComment.attachment_dropper  = t.frmTaskComment.attachment_dropper_cover.find('#task-attachment-dropper');
        t.frmTaskComment.is_note = t.frmTaskComment.find('#task_internal_note');
        t.frmTaskComment.el.btnSubmit = t.frmTaskComment.find('#btnSubmit');
        t.taskAttachment = t.frmTaskComment.find('#task_attachments');
        if (t.frmTaskComment.el.comment.length) {
            t.frmTaskComment.el.comment.summernote({
                inheritPlaceholder : true,
                placeholder : t.config.translations.comment_summer,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font',  ['strikethrough']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['misc',  [t.config.aiAsssistEnabled != '' ? 'aiAssist' : '']]
                ],
                buttons: {
                    aiAssist: function (context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents  : '<img src="' + t.config.url.matiImage + '" alt="icon" style="width:20px;height:20px;margin-right:5px;"> <span class="ai-assist-text">AI Assist</span>',
                            className : 'ai-assist-btn',
                            tooltip   : 'Search AI Assistance',
                            click: function () {
                                new bootstrap.Modal(document.getElementById('aiModal')).show();
                                let $context = $(document).find('#task' + t.taskNo);
                                let title    = $context.find('#task-name').text().trim();
                                $('#aiModal')
                                    .attr('data-type',   'task')
                                    .attr('data-title',  title)
                                    .attr('data-target', '#task_comment');
                            }
                        });
                        return button.render();
                    }
                },
                minHeight : 200,
                focus : false
            });
        }
        t.frmTaskCommentTokenize = function () {
            var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
            t.frmTaskComment.el.tmp_id.val(v);
            t.frmUpdateTaskStatus && t.frmUpdateTaskStatus.el && t.frmUpdateTaskStatus.el.temp_id &&
                t.frmUpdateTaskStatus.el.temp_id.val(v);
        };

        t.frmTaskCommentSubmit = function (e) {
            if (typeof e !== 'undefined') { e.preventDefault(); }
            let s = t.frmTaskComment.el.comment.val();
            if (s == '') {
                t.frmTaskComment.find('#shows_error')
                    .html('This field is required.')
                    .css({ color: '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                return false;
            } else {
                t.frmTaskComment.find('#shows_error').html('');
            }
            if (t.httpCall != true) { return false; }
            t.httpCall = false;
            var formData = new FormData(t.frmTaskComment.get(0));
            formData.append('ticketmodule', 'true');
            var http = $.ajax({
                url : t.config.url.add_task_comment,
                type : 'POST',
                processData : false,
                contentType : false,
                headers : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data : formData
            });
            http.done(function (data) {
                if (typeof data == 'object') {
                    if (data.status == 'success') {
                        t.refreshTaskTimeLine();
                        t.frmTaskCommentTokenize();
                        sweetAlert('center', 'success', data);
                        t.frmTaskComment.el.comment.val('').summernote('code', '');
                        t.frmTaskComment.find('#shows_error').html('');
                        t.frmTaskComment.is_note.prop('checked', false);
                        t.taskAttachment.empty();
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.fail(function () {
                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
            });
            http.always(function () { t.httpCall = true; });
        };

        let totalFileSize = 0;
        let uploadFile    = [];

        // t.frmTaskComment.attachment_dropper_cover.filedrop({
        //     fallback_id : 'task_attachment',
        //     fallback_dropzoneClick: true,
        //     url: t.config.url.add_task_attachment,
        //     paramname : 'attachment',
        //     data: {
        //         '_token' : t.config.token,
        //         'id' : function () { return t.taskNo; },
        //         'tmp_id' : function () { return t.frmTaskComment.el.tmp_id.val(); }
        //     },
        //     maxfiles    : 5,
        //     maxfilesize : 10,
        //     error: function (err) {
        //         switch (err) {
        //             case 'TooManyFiles':
        //                 sweetAlert('center', 'error', { msg: t.config.translations.upload_file });
        //                 break;
        //             case 'FileTooLarge':
        //                 sweetAlert('center', 'error', { msg: 'File size exceeds 10 MB. Please upload a smaller file.' });
        //                 break;
        //         }
        //     },
        //     uploadFinished: function (i, file, response) {
        //         if (response.status === 'success') {
        //             t.taskAttachment.find('#attach' + i + ' .name').text(decodeURIComponent(response.data.original_file_name));
        //             t.taskAttachment.find('#attach' + i).attr('data-id', response.data.id);
        //             t.taskAttachment.find('#attach' + i + ' .progress').fadeOut('slow');
        //             totalFileSize -= file.size;
        //         } else {
        //             t.taskAttachment.find('#attach' + i + ' .name').text(file.name + ' upload failed');
        //             t.taskAttachment.find('#attach' + i + ' .upload_length').addClass('progress-bar-danger');
        //         }
        //     },
        //     progressUpdated: function (i, file, progress) {
        //         t.taskAttachment.find('#attach' + i + ' .upload_length').css('width', progress + '%');
        //     },
        //     beforeSend: function (file, i, done) {
        //         if (uploadFile.includes(file.name)) {
        //             sweetAlert('center', 'error', { msg: 'This file has been already uploaded.' });
        //             return;
        //         }
        //         const allowedExtensions = ['.jpg','.jpeg','.png','.gif','.xls','.xlsx','.doc','.docx','.ppt','.pdf','.txt','.msg','.zip','.psd','.csv','.eml'];
        //         const fileExtension     = '.' + file.name.split('.').pop().toLowerCase();
        //         if ($.inArray(fileExtension, allowedExtensions) === -1) {
        //             sweetAlert('center', 'error', { msg: 'This file type is not allowed.' });
        //             return;
        //         }
        //         if ($('.count_img').length >= 5) {
        //             sweetAlert('center', 'error', { msg: t.config.translations.upload_file });
        //             return;
        //         }
        //         if (file.size > 10 * 1024 * 1024) {
        //             sweetAlert('center', 'error', { msg: 'File size exceeds 10 MB.' });
        //             return;
        //         }
        //         if (totalFileSize + file.size > 10 * 1024 * 1024) {
        //             sweetAlert('center', 'error', { msg: 'Total file size exceeds 10 MB.' });
        //             return;
        //         }
        //         totalFileSize += file.size;
        //         uploadFile.push(file.name);
        //         let fileSizeKB       = Math.ceil(file.size / 1024);
        //         let fileNameWithSize = file.name + ' [' + fileSizeKB + ' KB]';
        //         if (t.taskAttachment.find('#attach' + i).length) {
        //             t.taskAttachment.find('#attach' + i).attr('id', 'attach' + (Math.random().toString()).substring(2, 15));
        //         }
        //         t.taskAttachment.append(
        //             `<div id="attach${i}" class="attach pad-top count_img" data-size="${file.size}">
        //                 <div class="bord-btm clearfix">
        //                     <p class="float-start">${fileNameWithSize}</p>
        //                     <span style="cursor:pointer" class="remove-attach float-end" data-size="${file.size}">
        //                         <i class="bi bi-x-lg"></i> Remove
        //                     </span>
        //                 </div>
        //                 <div class="progress">
        //                     <div style="width:1%;" class="progress-bar upload_length"></div>
        //                 </div>
        //             </div>`
        //         );
        //         done();
        //     },
        //     dragOver : function () { t.frmTaskComment.attachment_dropper.show(); },
        //     drop     : function () {
        //         t.frmTaskComment.attachment_dropper.show();
        //         $('.note-editor.panel-default').removeClass('dragover');
        //     }
        // });

        // t.taskAttachment.removeAttach = function (e) {
        //     e.preventDefault();
        //     var p        = $(this).closest('.attach');
        //     let fileSize = parseFloat(p.attr('data-size'));
        //     if (fileSize && totalFileSize - fileSize >= 0) { totalFileSize -= fileSize; }
        //     let fileName = p.find('.bord-btm p').text().split(' [')[0];
        //     const index  = uploadFile.indexOf(fileName);
        //     if (index > -1) { uploadFile.splice(index, 1); }
        //     $.post(t.config.url.remove_task_attachment, { '_token': t.config.token, 'id': p.attr('data-id') });
        //     p.fadeOut('slow').remove();
        // };

        t.taskAttachmentView = function (e) {
            e.preventDefault();
            const $clicked = $(e.currentTarget);
            if ($clicked.attr('data-view_mode') == '1') {
                const images = [];
                $('.tri-view[data-view_mode="1"]').each(function () {
                    images.push({ href: $(this).attr('data-view'), title: $(this).attr('data-name') });
                });
                $.swipebox(images, {
                    initialIndexOnArray    : $('.tri-view[data-view_mode="1"]').index($clicked),
                    hideCloseButtonOnMobile: false,
                    removeBarsOnMobile     : false
                });
            }
        };

        t.taskAttachmentDownload = function (e) {
            e.preventDefault();
            window.location = $(this).attr('data-url');
        };

        t.timelineData         = [];
        t.timelineDisplayIndex = 0;

        t.refreshTaskTimeLine = function () {
            var formData = new FormData();
            formData.append('id', t.taskNo);
            formData.append('ticketmodule', 'true');

            var http = $.ajax({
                url         : t.config.url.get_task_timeline,
                type        : 'POST',
                headers     : { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                processData : false,
                contentType : false,
                data        : formData
            });
            http.done(function (data) {
                if (typeof data == 'object' && data.status == 'success') {
                    t.taskTimeline.empty();
                    t.timelineData         = data.data;
                    t.timelineDisplayIndex = 0;
                    t.loadMoreTimelineEntries(2);

                    if (t.timelineData.length > 2) {
                        if (!$('#loadMoreTimeline').length) {
                            $('#commentWrapper').append(`
                                <div id="loadMoreTimeline">
                                    <button class="btn">View more</button>
                                </div>`);
                            $('#loadMoreTimeline button').on('click', function () {
                                t.loadMoreTimelineEntries(2);
                                t.taskTimeline.css({
                                    'overflow-y' : 'scroll',
                                    'max-height': '350px',
                                    'scrollbar-width': 'thin',
                                    'scrollbar-color': '#D7D7D7 transparent'
                                });
                            });
                        }
                    } else {
                        $('#loadMoreTimeline').remove();
                    }

                    t.taskTimeline.off('click', '.tri-view').on('click', '.tri-view', $.proxy(t.taskAttachmentView));
                    t.taskTimeline.removeClass('hide');
                } else if (data.msg != '') {
                    sweetAlert('center', 'error', data);
                }
            });
        };

        t.loadMoreTimelineEntries = function (count) {
            var start = t.timelineDisplayIndex;
            var end   = Math.min(start + count, t.timelineData.length);

            if (
                t.config.user.id == t.taskCreator        ||
                t.config.user.id == assign_to             ||
                t.config.user.id == t.config.data.assigned_to ||
                t.config.user.role == 'SuperAdmin'
            ) {
                if (!$('#commentWrapper').length && t.timelineData.length > 0) {
                    t.taskTimeline.append(`
                        <div id="timelineTitle" style="font-weight:600; margin-bottom:10px;">
                            Task Comments (${t.timelineData.length})
                        </div>
                        <div id="commentWrapper" class="comment-wrapper"
                            style="border:1px solid #eee; border-radius:10px; padding:20px; background:#fff;"></div>`);
                }

                for (var i = start; i < end; i++) {
                    var v  = t.timelineData[i];
                    var class_name = '';
                    if      (v.updated_by == v.assigned_to && v.assigned_to != null) { class_name = 'color-code-bar color-code-blue-text'; }
                    else if (v.updated_by == v.creator_id  && v.creator_id  != null) { class_name = 'color-code-bar color-code-rose-text'; }
                    else if (v.updated_by != v.creator_id  && v.creator_id  != null) { class_name = 'color-code-bar color-code-yellow-text'; }

                    var previewText = (v.remarks?.length > 100) ? v.remarks.slice(0, 100): (v.remarks || '');                    
                    var attachmentsHtml = '';

                    if (typeof v.attachments !== 'undefined' && v.attachments.length > 0) {
                        var at = [];
                        $.each(v.attachments, function (ai, v2) {
                            // var sext     = v2.ext.toLowerCase();
                            var ext = v2.ext.toLowerCase();
                            var fileName = decodeURIComponent(v2.name);
                            var eye_link = '';
                            // if (['png','jpeg','jpg'].includes(sext)) {
                            if (t.isImageExtension(ext)) { 
                                eye_link = `<span class="tri-view"
                                    data-bs-toggle="tooltip" data-bs-original-title="View"
                                    data-view_mode="1"
                                    data-view="${t.config.url.view_task_attachment}/${v2.id}"
                                    data-name="${decodeURIComponent(v2.name)}">
                                    <i class="bi bi-eye"></i></span>`;
                            }
                            var ai_bg = v2.thumb == 1 ? 'ai-bg' : '';
                            var bg = v2.thumb == 1 ? `background-image:url(${t.config.url.view_task_attachment}/${v2.id}/1)` : '';
                            at.push(`
                                <div class="attach-item ${ai_bg}" style="${bg}">
                                    <div class="attach-item-cntnt nobg">
                                        ${t.getAttachmentIconHtml(ext)}
                                        <span class="attach-name" style="color:#001f5b; background-color:transparent;">${fileName}</span>
                                        <div class="icons">
                                            <span class="attachment-header">${eye_link}</span>
                                            <span class="tri-download attachment-header"
                                                data-bs-toggle="tooltip" data-bs-original-title="Download"
                                                data-url="${t.config.url.download_task_attachment}/${v2.id}">
                                                <i class="bi bi-download"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>`);
                        });
                        attachmentsHtml = `<div class="attachments" style="margin-top:10px;">
                            <div class="text-bold attachment-header" style="margin-bottom:.7rem;">${t.config.translations.attachment}:</div>
                            ${at.join('')}
                        </div>`;
                    }

                    $('#commentWrapper').append(`
                        <div class="single-comment" style="display:flex; position:relative; padding:20px 4px; margin-top:5px;
                            ${v.is_note != null && v.is_note == 1 ? 'background-color:#fff3f7; box-shadow:0 1px 1px rgba(0,0,0,.05);' : ''}">
                            <div class="left-column" style="width:30px; display:flex; flex-direction:column; align-items:center; position:relative;">
                                <img src="${v.profile_img}" alt="User Icon" style="width:32px; height:32px; border-radius:50%;">
                                <div class="vertical-line" style="flex:1; width:1px; background:#ddd; margin:5px 0;"></div>
                            </div>
                            <div class="right-content" style="flex:1; padding-left:15px;">
                                <div style="margin-bottom:5px; font-size:14px;">
                                    ${v.is_note != null && v.is_note == 1 ? 'Note Added By' : 'Commented By'}
                                    <strong class="${class_name}">${v.commenter || 'Anonymous'}</strong>
                                </div>
                                <div class="comment-preview" style="font-size:14px;margin-bottom:5px;">${previewText}</div>
                                <div class="comment-full" style="display:none; font-size:14px; margin-bottom:5px;">
                                    ${v.remarks}
                                    ${attachmentsHtml}
                                </div>
                            </div>
                            <div class="toggle-comment" style="cursor:pointer; margin-left:10px; padding-top:5px;">
                                <i class="bi bi-chevron-down" style="padding:10px; border-radius:20px;"></i>
                            </div>
                            <div class="comment-timestamp" style="position:absolute; margin:5px; bottom:-5px; left:43px; width:100%;
                                display:flex; justify-content:flex-start; font-size:12px;  font-weight:600;">
                                ${v.updated_at_format}
                            </div>
                        </div>`);
                }

                if ($('#loadMoreTimeline').length) { $('#loadMoreTimeline').appendTo('#commentWrapper'); }
                t.timelineDisplayIndex = end;
                if (t.timelineDisplayIndex >= t.timelineData.length) { $('#loadMoreTimeline').remove(); }
            }
        };

        t.taskTimeline.on('click', '.tri-view',  $.proxy(t.taskAttachmentView));
        t.taskTimeline.on('click', '.tri-download',  $.proxy(t.taskAttachmentDownload));
        t.frmTaskComment.el.btnSubmit.on('click', $.proxy(t.frmTaskCommentSubmit));
        t.frmTaskCommentTokenize();
        t.refreshTaskTimeLine();
    };

    $(document).off('click', '.toggle-comment').on('click', '.toggle-comment', function () {
        const $comment = $(this).closest('.single-comment');
        const $preview = $comment.find('.comment-preview');
        const $full = $comment.find('.comment-full');

        if ($full.is(':visible')) {
            $full.hide();
            $preview.show();
            $(this).find('i').removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            $full.show();
            $preview.hide();
            $(this).find('i').removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });

    t.triggerFirstTab = function (taskCard) {
        var scope = taskCard && taskCard.length ? taskCard : t.page;
        const $firstTab = scope.find('.task-tab-link').first();
        if ($firstTab.length) {
            t.activateTaskTab($firstTab);
        }
    }

    t.relatedTask();
    t.refillTaskStatus = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmUpdateTaskStatus.el.task_status_id.empty();
        $.each(t.config.task_statuses, function (i, k) {
            t.frmUpdateTaskStatus.el.task_status_id.append(new Option(k.name, k.id));
        });
        t.frmUpdateTaskStatus.el.task_status_id.trigger("change");
    }

    t.frmTask.status_id.select2({
        width: '100%',
        dropdownParent: t.frmTask.status_id.parent(),
    });

    t.frmTask.priority_id.select2({
        width: '100%',
        dropdownParent: t.frmTask.priority_id.parent(),
    });
    t.frmUpdateTaskStatus.el.task_status_id.select2({
        width: '100%',
        dropdownParent: t.frmUpdateTaskStatus.el.task_status_id.parent(),
    })
     t.frmUpdateTaskStatus.el.attachment_updates.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attachs");
        let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalUpdateTaskStatusFileSize - fileSize >= 0) {
            totalUpdateTaskStatusFileSize -= fileSize;
        }
        let fileName = p.find('.bord-btm p').text().split(' [')[0];
        const index = uploadedUpdateTaskStatusFiles.indexOf(fileName);
        if (index > -1) {
            uploadedUpdateTaskStatusFiles.splice(index, 1);
        }
        $.post(t.config.url.remove_task_attachment, { "_token": t.config.token, "id": p.attr("data-id") }, function (d) { });
        p.fadeOut("slow").remove();
    };

    t.frmTask.departmentId.select2($.extend({width: "100%",placeholder:"Select Department"}, {
        dropdownParent: t.frmTask.departmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.data.company_id
                };
            },
        },
        allowClear: true,
        delay: 200,
        placeholder: config.translations.connect ?? "Enter Department",
        templateSelection: function(s,container) {
            if(typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr('tkt_auto_creation_id', s.tkt_auto_creation_id);
            return s.text;
        }
    })).on("change", function(e) {
        t.fun.reload_problem_category();
    });

    t.frmTask.problemCategoryId.select2({ width: "100%",dropdownParent: t.frmTask.problemCategoryId.parent(),placeholder:"Select Problem Category" }).on("change",function(e){
        t.fun.reload_sub_category();
    });
    t.frmTask.subCategoryId.select2($.extend({width: "100%",placeholder:"Select Sub Category"}, {
        dropdownParent: t.frmTask.subCategoryId.parent(),
        ajax: {
            url: t.config.url.sub_category,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: [t.frmTask.problemCategoryId.val()]
                };
            },
            delay: 200,
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        if (item.status === 0) {
                            return null;
                        }
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }
        },
        placeholder: config.translations.select_sub_category ?? "Select Sub Category",
    }))

    $(document).on('click','.update-task-status',function(){
        t.task_id = $(this).data('id');
        t.status_id = $(this).data('status');
        t.frmUpdateTaskStatus.el.statusWrapper.show();
        t.refillTaskStatus();
        t.frmTaskCommentTokenize();
        t.frmUpdateTaskStatus.el.task_id.val(t.task_id);
        t.frmUpdateTaskStatus.el.task_status_id.val(t.status_id).trigger("change");
        t.frmUpdateTaskStatus.el.task_comment.summernote('code', '');
        t.frmUpdateTaskStatus.el.attachment_updates.empty();
        t.taskUpdateMdl.modal('show');
    })

    $(document).on('click','.incomplete-task',function(){
        t.task_id = $(this).data('id');
        t.frmTaskCommentTokenize();
        t.frmUpdateTaskStatus.el.statusWrapper.hide();
        t.frmUpdateTaskStatus.el.task_id.val(t.task_id);
        t.frmUpdateTaskStatus.el.task_status_id.val(1).trigger("change");
        t.frmUpdateTaskStatus.el.task_comment.summernote('code', '');
        t.frmUpdateTaskStatus.el.attachment_updates.empty();
        t.taskUpdateMdl.modal('show');
    })

    $(document).on('click', '.self-assign-task', function () {
        let taskId = $(this).data('id');
        sweetAlertConfirmation({
            message: config.translations.are_you_pick_task,
            onConfirm: function () {
                $.ajax({
                    url: t.config.url.self_assign_task,
                    type: 'POST',
                    data: {
                        task_id: taskId,
                        _token: t.config.token,
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            sweetAlert('center', 'success', res);
                            t.relatedTask();
                        } else {
                            sweetAlert('center', 'error', res);
                        }
                    },
                    error: function () {
                        sweetAlert('center', 'error', {
                            msg: config.translations.something_went_wrong
                        });
                    }
                });

            }
        });
    });


    $(document).on("click", ".edit-task", function () {
        t.config.add = false;
        t.config.edit = true;
        t.config.edit_pc_name = '';
        t.config.edit_pc_id = '';
        t.config.edit_spc_name = '';
        t.config.edit_spc_id = '';
        t.resetTaskForm();
        t.optionsCompany()
        var taskId = $(this).data("id");
        t.mdlTask.find('.modal-title').text(t.config.translations.edit_task);
        t.frmTask.task_id.val(taskId);
        t.frmTask.ticketId.val(t.config.data.id);
        t.frmTask.status_id.closest('.amg-form-field-row').removeClass('d-none');
        t.frmTask.priority_id.closest('.amg-form-field-row').removeClass('d-none');
        if(t.config.client != 'ltts') {
            t.frmTask.cost.closest('.amg-form-field-row').removeClass('d-none');
        }

        $.ajax({
            url: t.config.url.editTask + '/' + taskId,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    t.frmTask.name.val(response.data.task_name);
                    t.frmTask.status_id.val(response.data.status_id).trigger("change");
                    t.frmTask.priority_id.val(response.data.priority_id).trigger("change");
                   function parseDate(dateString) {
                                        let parts = dateString.split(' ');
                                        let date = parts[0].split('/');
                                        let time = parts[1].split(':');

                                        return new Date(
                                            parseInt(date[2]),      // Year
                                            parseInt(date[1]) - 1,  // Month (0-based)
                                            parseInt(date[0]),      // Day
                                            parseInt(time[0]),      // Hour
                                            parseInt(time[1])       // Minute
                                        );
                    }

                    if (response.data.start_date) {
                        let startDate = parseDate(response.data.start_date);
                        t.dueDatePicker.set('minDate', startDate);
                        t.dueDatePicker.setDate(response.data.due_date, true);
                    } else {
                        t.dueDatePicker.set('minDate', new Date());
                        t.dueDatePicker.setDate(response.data.due_date, true);
                    }
                    if(config.client != 'ltts') {
                        t.frmTask.cost.val(response.data.cost);
                    }
                    t.frmTask.description.summernote("code", response.data.description);
                    if (response.data.assigned_to) {
                        var assignedUser = {
                            id: response.data.assigned_to,
                            text: response.data.assignToName
                        };

                        var newOption = new Option(assignedUser.text, assignedUser.id, true, true);
                        t.frmTask.assigned_to.append(newOption).trigger("change");
                    }
                    if (response.data.is_visible_user == 1) {
                        t.frmTask.is_visible_user.prop('checked', true);
                    } else {
                        t.frmTask.is_visible_user.prop('checked', false);
                    }
                    if(response.data.department_name && response.data.department_id){
                        var newOption = new Option(response.data.department_name,response.data.department_id,true,true);
                        t.frmTask.departmentId.append(newOption).trigger('change');
                    }
                    if(response.data.problem_category_name && response.data.problem_category_id){
                        t.config.edit_pc_name = response.data.problem_category_name;
                        t.config.edit_pc_id = response.data.problem_category_id;
                    }
                    if(response.data.sub_category_id && response.data.sub_category_id){
                        t.config.edit_spc_name = response.data.sub_category_name;
                        t.config.edit_spc_id = response.data.sub_category_id
                    }

                    t.frmTask.btnSubmit.text('Update');
                    // hideError();
                    t.mdlTask.modal("show");
                } else {
                    sweetAlert('center', 'error', response);
                }
            },
            error: function () {
                sweetAlert('center', 'error', { 'msg': config.translations.something_went_wrong });
            }
        });
    });

    $(document).on('click', '.delete-task', function () {
        let taskId = $(this).data('id');
        let deleteUrl = config.url.deleteTask;
        let table = $("#relevantTasks");
        t.deleteTask(taskId, deleteUrl, table);
    });

    t.deleteTask = function (taskId, deleteUrl, table) {
        sweetAlertConfirmation({
            message: 'Are you sure you want to delete this task?',
            onConfirm: function () {
                $('#taskbutton-' + taskId).addClass('disabled').find('button').prop('disabled', true);
                $.ajax({
                    url: deleteUrl + '/' + taskId,
                    type: 'GET',
                    success: function (data) {
                        if (data.status === "success") {
                            sweetAlert('center', 'success', data);
                            t.relatedTask();
                        } else {
                            $('#taskbutton-' + taskId).addClass('disabled').find('button').prop('disabled', false);
                            sweetAlert('center', 'error', data);
                        }
                    },
                    error: function () {
                        $('#taskbutton-' + taskId).addClass('disabled').find('button').prop('disabled', false);
                        var data = {
                            'msg': config.translations.something_went_wrong
                        };
                        sweetAlert('center', 'error', data);
                    }
                });
            },
        });
    }

    $(document).on('click', ".history-task", function(e) {
      e.preventDefault();
      var taskId = $(this).data('id');
      var historyUrl = t.config.url.Taskhistory
      var token = t.config.token || $('meta[name="csrf-token"]').attr('content');
      TaskHistory.open(taskId, historyUrl, token);
    });


    t.truncateHtml = function(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    t.escapeHtml= function(text) {
        return String(text === null || typeof text === 'undefined' ? '' : text).replace(/[&<>"'`=\/]/g, function (s) {
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

    t.page.on("click", ".read-more", function(e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    });
    t.frmUpdateTaskStatus.el.attachment_updates.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attachs");
        let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalUpdateStatusFileSize - fileSize >= 0) {
            totalUpdateStatusFileSize -= fileSize;
        }
        let fileName = p.find('.bord-btm p').text().split(' [')[0];
        const index = uploadedUpdateTaskStatusFiles.indexOf(fileName);
        if (index > -1) {
            uploadedUpdateTaskStatusFiles.splice(index, 1);
        }
        $.post(t.config.url.attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") }, function (d) { });
        p.fadeOut("slow").remove();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#historyTable_wrapper .plain-search").validate_str_param();
        console.log(v)
        if (v === false) {
            alert(t.config.translations.please_enter_valid_search);
            return false;
        }
        t.dt1.search(v).draw();
    };

    $(document).on("click", ".btn-mdl-reload", function (e) {
        e.preventDefault();
        t.dt1.ajax.reload();
    });

    $(document).on("click",'.task-searchbox', $.proxy(t.tableSearch));
    t.ticketTypeSelect2 = t.page.find('.ticketType');
    t.ticket_type_fields_div = t.page.find('.ticket-type-fields-div');
    t.ticket_type_fieldset_forms = t.page.find('.ticket_type_fieldset_forms');
    t.ticketTypeDetailsForm = t.page.find('.ticket_type_fieldset_forms');

    t.mdlServiceRequest = t.page.find('#mdl-servicerequest');
    t.frmServiceRequest = t.mdlServiceRequest.find('#requested_form');
    t.frmServiceRequest.el = {};
    t.frmServiceRequest.el.form_title = t.frmServiceRequest.find(".form_title");
    t.frmServiceRequest.el.form_id = t.frmServiceRequest.find("#form_id");
    t.frmServiceRequest.el.request_id = t.frmServiceRequest.find("#request_id");
    t.frmServiceRequest.el.field_values = t.frmServiceRequest.find('#field_values');
    t.frmServiceRequest.el.for_action = t.frmServiceRequest.find("#for_action");
    t.frmServiceRequest.btnSubmit = t.mdlServiceRequest.find("#btnSubmit");

    // status serviceRequestForm Fields
    t.mdlStatusServiceRequest = t.page.find('#mdl-status-servicerequest');
    t.frmStatusServiceRequest = t.mdlStatusServiceRequest.find('#status_requested_form');
    t.frmStatusServiceRequest.el = {};
    t.frmStatusServiceRequest.el.form_id = t.frmStatusServiceRequest.find("#form_id");
    t.frmStatusServiceRequest.el.status_id = t.frmStatusServiceRequest.find("#status_id");
    t.frmStatusServiceRequest.el.ticket_id = t.frmStatusServiceRequest.find("#ticket_id");
    t.mdlStatusServiceRequest.btnSubmit = t.mdlStatusServiceRequest.find("#Status_saveData");
    t.mdlStatusServiceRequest.title = t.mdlStatusServiceRequest.find(".modal-title");
    t.frmStatusServiceRequest.el.field_values = t.frmStatusServiceRequest.find('#field_values');
    t.frmStatusServiceRequest.btnSubmit = t.mdlStatusServiceRequest.find("#btnSubmit");

    t.mdlEdit = t.page.find("#editTicketModal");
    t.mdlEdit.title = t.mdlEdit.find('.modal-title');
    t.frmEdit = t.mdlEdit.find("#edit-ticket-mdl-frm");
    t.frmEdit.el = {};
    t.frmEdit.el.id = t.frmEdit.find("#id");
    t.frmEdit.el.token = t.frmEdit.find("#token");
    t.frmEdit.el.department_id = t.frmEdit.find("#edit_department_id");
    t.frmEdit.el.service_type_id = t.frmEdit.find("#service_type_id");
    t.frmEdit.el.problem_type_id = t.frmEdit.find("#problem_type_id");
    t.frmEdit.el.problem_category_id = t.frmEdit.find("#edit_problem_category_id");
    t.frmEdit.el.sub_category_id_cvr = t.frmEdit.find("#sub_category_id_cvr");
    t.frmEdit.el.tags = t.frmEdit.find("#tags");
    t.frmEdit.el.device_id = t.frmEdit.find("#edit_device_id");
    t.frmEdit.el.self_assign = t.frmEdit.find("#edit_device_id");
    t.frmEdit.el.assign_to = t.frmEdit.find("#edit_assigned_to"); 
    t.frmEdit.el.sub_category_id = t.frmEdit.find("#edit_sub_category_id");
    t.frmEdit.el.priority_id = t.frmEdit.find("#edit_priority_id");
    t.frmEdit.el.tat = t.frmEdit.find("#tat");
    t.frmEdit.el.delete_previous_tasks = t.frmEdit.find("#remove_tasks");
    t.frmEdit.el.token = t.frmEdit.find("#token");
    t.frmEdit.el.btnSubmit = t.frmEdit.find("#btnSubmit");

    t.mdlBookCalendar = t.page.find("#mdl_book_calendar");
    t.mdlBookCalendar.book_calendar_for_ticket = t.mdlBookCalendar.find('.book_calendar_for_ticket');
    t.BookCalendarTable = t.mdlBookCalendar.find("#book_calendar_table");
    t.mdlBookCalendar.searchbox = t.mdlBookCalendar.find(".searchbox");
    t.mdlBookCalendar.pageLength = t.mdlBookCalendar.find(".user-list-page-length");

    t.mdlAddBlockCalendar = t.page.find("#mdl_add_block_calendar");
    t.mdlAddBlockCalendar.title = t.mdlAddBlockCalendar.find('.modal-title');
    t.frmAddCalender = t.mdlAddBlockCalendar.find("#add-block-calendar-mdl-frm");
 
    t.frmAddCalender.el = {};
    t.frmAddCalender.el.start_date_time = t.frmAddCalender.find("#start_date_time");
    t.frmAddCalender.el.end_date_time = t.frmAddCalender.find("#end_date_time");
    t.frmAddCalender.el.subject = t.frmAddCalender.find("#subject");
    t.frmAddCalender.el.description = t.frmAddCalender.find("#description");
    t.frmAddCalender.el.cc_users = t.frmAddCalender.find("#cc_users");
    t.frmAddCalender.el.btnSubmit = t.frmAddCalender.find("#btnSubmit");
    t.frmAddCalender.el.btnClear = t.frmAddCalender.find("#btnClear");
    t.config.event_url = '';

    t.mdlAddEventCommentStatus = t.page.find("#add_event_comment");
    t.frmAddEventComment = t.mdlAddEventCommentStatus.find("#add-event-comment-mdl-frm");
    t.frmAddEventComment.el = {};
    t.frmAddEventComment.el.event_status = t.frmAddEventComment.find("#event_status");
    t.frmAddEventComment.el.comment = t.frmAddEventComment.find("#comment");
    t.frmAddEventComment.el.event_id = t.frmAddEventComment.find("#event_id");
    t.frmAddEventComment.el.btnSubmit = t.frmAddEventComment.find("#btnSubmit");
    t.frmAddEventComment.el.btnClear = t.frmAddEventComment.find("#btnClear");
    t.config.event_comment_url = '';

    let lastValidEndDateTime = null;
    t.frmAddCalender.el.start_date_time.datetimepicker({
        format: 'd F Y h:i A',
        step: 15,
        minDate: 0,
        validateOnBlur: false,
        onShow: function () {
            this.setOptions({ minDate: 0 });
        },
        onChangeDateTime: function (currentDateTime) {
            if (currentDateTime) {
                // Save selected start
                t.frmAddCalender._startDateTime = currentDateTime;

                // Add 30 minutes for end
                const endDate = new Date(currentDateTime.getTime() + 30 * 60 * 1000);

                // Format
                const day = String(endDate.getDate()).padStart(2, '0');
                const month = endDate.toLocaleString('default', { month: 'long' });
                const year = endDate.getFullYear();
                let hours = endDate.getHours();
                const minutes = String(endDate.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                const formatted = `${day} ${month} ${year} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;

                // Set end time
                t.frmAddCalender.el.end_date_time.val(formatted);
                t.frmAddCalender.el.end_date_time.datetimepicker('setDate', endDate);

                // Update last valid end time
                lastValidEndDateTime = endDate;
            }
        }
    });

    t.frmAddCalender.el.end_date_time.datetimepicker({
        format: 'd F Y h:i A',
        step: 15,
        validateOnBlur: false,
        onShow: function () {
            const startDate = t.frmAddCalender._startDateTime;

            if (startDate) {
                const minTime = `${String(startDate.getHours()).padStart(2, '0')}:${String(startDate.getMinutes()).padStart(2, '0')}`;
                this.setOptions({
                    minDate: startDate,
                    minTime: minTime
                });
            }
        },
    });

    if (t.config.tkt_config.checked_cc_checkbox == 1) {
        t.frmUpdateStatus.el.follow_cc.prop('checked', true);
        t.frmComment.el.follow_cc.prop('checked', true);
    }

    t.frmAddCalender.el.cc_users.select2({
        width: "100%",
        placeholder: "Add CC",
        dropdownParent: t.mdlAddBlockCalendar,
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
                    user_id: config.data.creator_id,
                }
            },
            processResults: function (data) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    t.btn_ticket_type_save = t.page.find('.btn_ticket_type_save');
    t.frmTicketTypeValidator = t.ticketTypeDetailsForm.validate({
        onsubmit: false,
        errorPlacement: function (error, element) {
            // Radio / Checkbox
            if (element.is(':checkbox, :radio')) {
                var group = element.closest('.custom-option-group');
                if (group.length) {
                    group.find('.cf-error-wrapper').remove();
                    var wrapper = $('<div class="cf-error-wrapper"></div>');

                    if (element.is(':checkbox')) {
                        wrapper.insertAfter(group.find('.custom-option-list'));
                    } else {
                        wrapper.appendTo(group);
                    }
                    wrapper.append(error);
                    return;
                }
            }
            // Select2
            if (element.hasClass('select2-hidden-accessible')) {
                error.insertAfter(element.next('.select2'));
                return;
            }

            // Input Group
            var inputGroup = element.closest('.input-group, .ticket-input-group');
            if (inputGroup.length) {
                error.insertAfter(inputGroup);
                return;
            }

            // Dynamic field
            var valueContainer = element.closest('.tkd-detail-value');
            if (valueContainer.length) {
                error.appendTo(valueContainer);
                return;
            }

            error.insertAfter(element);
        },

        highlight: function (element) {

            // Checkbox/Radio par error class mat lagao
            if ($(element).is(':checkbox,:radio')) {
                $(element)
                    .removeClass('error')
                    .closest('.custom-option-group')
                    .addClass('has-error');
                return;
            }

            $(element).addClass('error');
        },

        unhighlight: function (element) {
            if ($(element).is(':checkbox,:radio')) {
                $(element)
                    .closest('.custom-option-group')
                    .removeClass('has-error')
                    .find(':checkbox,:radio')
                    .removeClass('error');
                return;
            }
            $(element).removeClass('error');
        },

        success: function (label, element) {
            if ($(element).is(':checkbox,:radio')) {
                $(element)
                    .closest('.custom-option-group')
                    .find(':checkbox,:radio')
                    .removeClass('error');
            }
            label.remove();
        },
        invalidHandler: function (event, validator) {

            if (validator.numberOfInvalids()) {

                validator.errorList[0].element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

            }

        }

    });


    t.fieldsetFormUpdated = false;
    t.ticketTypeBootstrapping = true;

    t.ticket_type_fieldset_forms.on('change', 'input, select, textarea', function () {
        if (t.ticketTypeBootstrapping) {
            return;
        }
        t.fieldsetFormUpdated = true;
        t.btn_ticket_type_save.removeClass('hide').show();
    });

    t.ticketTypeSelect2.select2({
        width: '100%',
        minimumResultsForSearch: 0
    })

    t.btn_ticket_type_save.on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true).text('Updating..');
        if (t.frmTicketTypeValidator.form() == false) {
            btn.prop('disabled', false).text('Save Changes');
            return false;
        }
        var formData = new FormData(t.ticket_type_fieldset_forms[0]);
        formData.set('ticket_type', t.ticketTypeSelect2.val());
        $.ajax({
            url: t.config.url.updateTicketTypeDetails,
            data: formData,
            type: "POST",
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status == 'success') {
                    t.fieldsetFormUpdated = false;
                    sweetAlert('center', 'success', res);
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    sweetAlert('center', 'error', res);
                }
                btn.prop('disabled', false).text('Save Changes');
            },
            fail: function (res) {
                btn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    t.openMdlTktBookCalendar = function () {
        t.loadBoockCalendar.ajax.reload();
        t.mdlBookCalendar.book_calendar_for_ticket.html("#" + t.config.data.id);
        t.mdlBookCalendar.modal("show")
    }
    t.openMdlTktAddBlockCalendar = function () {
        t.resetFormAddBlockCalendar();
        t.config.event_url = t.config.url.addBlockCalendar;
        t.frmAddCalender.el.btnSubmit.html('Save');
        t.subjectOfTicketForBlockCalendar = '#' + t.config.data.id + ' ' + t.config.tkt_block_calendar.subject;
        t.frmAddCalender.el.subject.val(t.subjectOfTicketForBlockCalendar);
        t.mdlAddBlockCalendar.modal("show")
    }
    function formatDateTime(date) {
        const day = String(date.getDate()).padStart(2, '0');
        const month = date.toLocaleString('default', { month: 'long' });
        const year = date.getFullYear();
        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12 || 12;
        return `${day} ${month} ${year} ${String(hours).padStart(2, '0')}:${minutes} ${ampm}`;
    }
    t.page.on("click", ".edit_event", $.proxy(function () {
        t.resetFormAddBlockCalendar();
        var event_id = $(this).data('id');
        t.config.event_url = t.config.url.updateBlockCalendar + '/' + event_id;
        $.ajax({
            type: 'get',
            url: t.config.url.editBlockCalendar + '/' + event_id,
            success: function (response) {
                t.frmAddCalender.el.subject.val(response.data.subject);
                const startDate = new Date(response.data.start_date_time);
                if (!isNaN(startDate)) {
                    const formattedStart = formatDateTime(startDate);
                    t.frmAddCalender.el.start_date_time.val(formattedStart);
                    t.frmAddCalender.el.start_date_time.datetimepicker('setDate', startDate);
                    t.frmAddCalender._startDateTime = startDate;
                }

                const endDate = new Date(response.data.end_date_time);
                if (!isNaN(endDate)) {
                    const formattedEnd = formatDateTime(endDate);
                    t.frmAddCalender.el.end_date_time.val(formattedEnd);
                    t.frmAddCalender.el.end_date_time.datetimepicker('setDate', endDate);
                }
                t.frmAddCalender.el.description.summernote('code', response.data.description);
                t.frmAddCalender.el.btnSubmit.html('Update');
                const cc_users = response.data.cc_users;
                const cc_users_array = cc_users.split(',');
                t.frmAddCalender.el.cc_users.val(null);
                cc_users_array.forEach(function(email) {
                    email = $.trim(email);
                    var option = t.frmAddCalender.el.cc_users.find("option[value='" + email + "']");
                    if (option.length === 0) {
                        option = new Option(email, email, true, true);
                        t.frmAddCalender.el.cc_users.append(option);
                    } else {
                        option.prop("selected", true);
                    }
                });
                t.frmAddCalender.el.cc_users.trigger("change");

                t.mdlAddBlockCalendar.modal("show")
            }
        });

    }));

    t.frmAddEventComment.el.event_status.select2({
        width: '100%',
        placeholder: 'Select',
        allowClear: true,
        dropdownParent: t.mdlAddEventCommentStatus
    });
    t.page.on("click", ".add_comment_status", $.proxy(function (e) {
        e.preventDefault();
        var event_id = $(this).data('id');
        t.frmAddEventComment.el.btnSubmit.removeAttr('disabled', true);
        t.frmAddEventComment.el.comment.summernote('code', "");
        t.frmAddEventComment.el.event_id.val('');
        t.frmAddEventComment.find('#shows_error').html('');
        t.frmAddEventComment.el.event_status.val(null).trigger('change');
        $.ajax({
            type: 'get',
            url: t.config.url.event_feedback + '/' + event_id,
            success: function (response) {
                t.frmAddEventComment.el.comment.summernote('code', response.data.comment);
                t.frmAddEventComment.el.event_id.val(response.data.id);
                t.frmAddEventComment.el.event_status.val(response.data.status).trigger('change');
                t.mdlAddEventCommentStatus.modal("show")
            }
        });
    }));

    t.frmAddEventComment.el.btnSubmit.on("click", $.proxy(function (e) {
        e.preventDefault();
        var comment = t.frmAddEventComment.el.comment.val();
        if (comment == '' || comment == '<br>') {
            t.frmAddEventComment.find('#shows_error').html('This field is required.');
            t.frmAddEventComment.find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            t.frmAddEventComment.find('#shows_error').html('');
        }
        if (t.frmeventFeedcaback.form() == false) {
            return false;
        }
        t.frmAddEventComment.el.btnSubmit.prop('disabled', true);
        $.ajax({
            type: 'post',
            url: t.config.url.update_event_feedback,
            data: {
                _token: $('input[name="_token"]').val(),
                comment: comment,
                event_id: t.frmAddEventComment.el.event_id.val(),
                event_status: t.frmAddEventComment.el.event_status.val(),
            },
            success: function (response) {
                if (response.status == 'success') {
                    sweetAlert('center', 'success', response);
                    t.loadBoockCalendar.ajax.reload();
                    t.mdlAddEventCommentStatus.modal("hide");
                } else {
                    sweetAlert('center', 'error', response);
                    t.frmAddEventComment.el.btnSubmit.removeAttr('disabled', true);
                }
            },
            fail: function (res) {
                t.frmAddEventComment.el.btnSubmit.removeAttr('disabled', true);
            }
        });
    }));

    t.frmAddCalenderSubmit = function (e) {
        e.preventDefault();
        // t.frmAddCalender.el.start_date_time.data("DateTimePicker").hide();
        // t.frmAddCalender.el.end_date_time.data("DateTimePicker").hide();
        let s = t.frmAddCalender.el.description.val();
        if (s == '') {
            t.frmAddCalender.find('#shows_error').html('This field is required.');
            t.frmAddCalender.find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            t.frmAddCalender.find('#shows_error').html('');
        }

        if (t.frmValidatorBlockCalendar.form() == false) {
            return false;
        }
        var frmData = new FormData(t.frmAddCalender[0]);
        frmData.append('creator_id', t.config.tkt_block_calendar.creator_id);
        frmData.append('creator_email', t.config.tkt_block_calendar.creator_email);
        frmData.append('ticket_id', t.config.data.id);
        frmData.append('technician_id', t.config.tkt_block_calendar.assigned_to);
        frmData.append('technician_email', t.config.tkt_block_calendar.assigned_to_email);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.config.event_url,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function () {
                t.frmAddCalender.el.btnSubmit.prop('disabled', true);
            },
            success: function (data) {
                console.log(data);
                if (typeof data == "object") {
                    if (data.status === "warning" && data.overlap_detected) {
                        sweetAlertConfirmation({
                            message: data.msg,
                            onConfirm: function () {
                                frmData.append('force_book', true);
                                $.ajax({
                                    url: t.config.event_url,
                                    type: 'POST',
                                    data: frmData,
                                    processData: false,
                                    contentType: false,
                                    success: function (finalResponse) {
                                        if (finalResponse.status == "success") {
                                            sweetAlert('center', 'success', finalResponse);
                                            t.mdlAddBlockCalendar.modal("hide");
                                            t.loadBoockCalendar.ajax.reload();
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        console.error('AJAX error: ', status, error);
                                    }
                                });
                            }
                        });
                    } else if (data.status == "success") {
                        sweetAlert('center', 'success', data);
                        t.mdlAddBlockCalendar.modal("hide");
                        t.loadBoockCalendar.ajax.reload();
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                }
            },
            complete: function (xhr, status) {
                t.frmAddCalender.el.btnSubmit.prop('disabled', false);
                t.httpCall = true;
                console.log('Request completed with status: ' + status);
            },
            error: function (xhr, status, error) {
                console.error('AJAX error: ', status, error);
            }
        });
    }

    t.resetFormAddBlockCalendar = function () {
        t.frmAddCalender.el.start_date_time.val('');
        t.frmAddCalender.el.end_date_time.val('');
        t.frmAddCalender.el.subject.val('');
        t.frmAddCalender.el.description.summernote('code', "");
        t.frmAddCalender.el.cc_users.val(null).find('option').remove().end().trigger('change');
        t.frmAddCalender.find('#shows_error').html('');
    }

    t.frmValidatorBlockCalendar = t.frmAddCalender.validate({
        rules: {
            subject: {
                required: true
            },
            start_date_time: {
                required: true
            },
            end_date_time: {
                required: true
            },
            "tags[]":{
                clean_text_only: true,
            },
        },
        errorPlacement: function(error, element) {
        element.closest('.amg-form-field')
               .find('.amg-form-error-wrap')
               .html(error);
    }
    });
    t.frmeventFeedcaback = t.frmAddEventComment.validate({
        rules: {
            event_status: {
                required: true
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent("div").parent("div"));
        }
    });

    t.bookCalendarActionIcons = {
        edit: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
               </svg>`,
        comment: `<svg viewBox="0 0 16 16" fill="none">
                <path d="M8 0.5C3.85938 0.5 0.5 3.29688 0.5 6.75C0.5 8.32812 1.21875 9.76562 2.39062 10.8594C2.20312 11.625 1.85938 12.3281 1.34375 12.9375C1.21875 13.0781 1.1875 13.2812 1.26562 13.4531C1.34375 13.625 1.51562 13.7344 1.70312 13.7344C2.98438 13.7344 4.20312 13.3125 5.20312 12.6094C6.07812 12.8594 7.01562 13 8 13C12.1406 13 15.5 10.2031 15.5 6.75C15.5 3.29688 12.1406 0.5 8 0.5ZM8 12C7.0625 12 6.17188 11.8594 5.35938 11.6094C5.21875 11.5625 5.0625 11.5938 4.9375 11.6719C4.26562 12.1562 3.51562 12.4844 2.71875 12.6406C3.09375 12.0469 3.34375 11.3906 3.46875 10.7031C3.5 10.5312 3.4375 10.3594 3.3125 10.25C2.20312 9.29688 1.5 8.07812 1.5 6.75C1.5 3.85938 4.40625 1.5 8 1.5C11.5938 1.5 14.5 3.85938 14.5 6.75C14.5 9.64062 11.5938 12 8 12Z" fill="currentColor"/>
               </svg>`
    };

    t.bookCalendarActionButtonHtml = function (label, classes, id, iconMarkup) {
        var safeLabel = t.escapeHtml(label);
        var safeClasses = t.escapeHtml(classes || "");
        var safeId = t.escapeHtml(id);

        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            safeClasses,
            '" data-id="',
            safeId,
            '" title="',
            safeLabel,
            '" aria-label="',
            safeLabel,
            '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    t.tblcalendarHelpers = {
        actions: function () {
            return function (d) {
                var buttons = [];

                if (!d || !d.id || !t.config.user || !t.config.data || t.config.user.id != t.config.data.assigned_to) {
                    return '<span class="user-list-empty">-</span>';
                }

                buttons.push(
                    t.bookCalendarActionButtonHtml(
                        config.translations.edit_event || "Edit Event",
                        "edit_event",
                        d.id,
                        t.bookCalendarActionIcons.edit
                    )
                );
                buttons.push(
                    t.bookCalendarActionButtonHtml(
                        "Give Review",
                        "add_comment_status",
                        d.id,
                        t.bookCalendarActionIcons.comment
                    )
                );

                return [
                    '<div class="user-list-actions role-list-actions justify-content-start">',
                    buttons.join(""),
                    '</div>'
                ].join("");
            };
        },
        comment: function () {
            return function (d) {
                return `
                    <div class="meeting_comment">
                        ${d.comment ?? ''}
                    </div>
                `;
            };
        },
    };

    t.loadBoockCalendar = t.BookCalendarTable.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [6, 7]
        },
        {
            targets: 6,
            render: t.tblcalendarHelpers.comment()
        },
        {
            targets: 7,
            className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
            render: t.tblcalendarHelpers.actions()
        },
        ],
        order: [
            [2, 'desc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        lengthChange:false,
        searching:false,
        pageLength: parseInt(t.mdlBookCalendar.pageLength.val(), 10) || 10,
        ajax: {
            url: t.config.url.blockCalendarsList,
            type: "GET",
            data: function (d) {
                d._token = t.config.token;
                d.ticket_id = t.config.data.id;
                d.technician_id = t.config.tkt_block_calendar.assigned_to;
                d.search = d.search || {};
                d.search.value = $.trim(t.mdlBookCalendar.searchbox.val() || "");
            },
        },
        columns: [
            { data: 'a.technician_name' },
            { data: 'a.creator_name' },
            { data: 'a.start_date_time_formatted' },
            { data: 'a.end_date_time_formatted' },
            { data: 'a.cc_users_names' },
            { data: 'a.status' },
            { data: 'a' },
            { data: 'a' },
        ],
        fnInitComplete: function (oSettings, json) {
            $("#book_calendar_table_wrapper").removeClass("form-inline");
            t.BookCalendarTable.closest("div").addClass("table-responsive");
            $("#book_calendar_table_filter").remove();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    t.searchBookCalendarData = function (e) {
        if (e) {
            e.preventDefault();
        }

        t.loadBoockCalendar.ajax.reload();
    };

    t.reloadBookCalendar = function (e) {
        if (e) {
            e.preventDefault();
        }

        t.mdlBookCalendar.searchbox.val("");
        t.loadBoockCalendar.ajax.reload(null, false);
    };

    t.changeBookCalendarPageLength = function (e) {
        var length = parseInt(t.mdlBookCalendar.pageLength.val(), 10);

        if (e) {
            e.preventDefault();
        }

        if (length > 0) {
            t.loadBoockCalendar.page.len(length).draw(false);
        }
    };

    t.mdlBookCalendar.on("click", ".amg-list-searchbar__icon", t.searchBookCalendarData);
    t.mdlBookCalendar.on("keyup", ".searchbox", function (e) {
        if (e.which === 13 || !this.value.length) {
            t.searchBookCalendarData(e);
        }
    });
    t.mdlBookCalendar.on("click", ".btn-reload-list", t.reloadBookCalendar);
    t.mdlBookCalendar.on("change", ".user-list-page-length", t.changeBookCalendarPageLength);

    t.setTicketTypeForm = function () {
        var preDefinedOptionsArray = {
            1: t.config.url.getLocationByAjax,
            2: t.config.url.getUserByAjax,
            3:config.url.getByQueryDevice,
            4:config.url.getQueryPlace,
            5:config.url.getQueryManufacture,
            6:config.url.getQueryModel,
            7:config.url.getQueryComponent,
            8:config.url.getQueryTicket,
            9:config.url.getQueryTicketProcureRequest,
            10:config.url.getQueryRecord,
            11:config.url.getQueryTask,
            12:config.url.getQueryLicense,
            13:config.url.getQueryProject,
            14:config.url.getQueryPurchase,
            15:config.url.getQuerySupplier,
            16:config.url.getQueryContract,
            17:config.url.getQueryEsms,
            18:config.url.getQueryDepartment,
        }

        var ticketType = t.ticketTypeSelect2.val();
        t.ticket_type_fields_div.removeClass('hide').show();
        t.ticket_type_fields_div.find('.fieldsets').html("");
        var tickeTypeDetailsData = t.config.ticket_type_details;
        $.ajax({
            method: 'GET',
            url: t.config.url.getTicketTypeFieldsets + "/" + ticketType + "/ticketType",
            success: function (data) {
                if (data.status == 'success' && data.data.length > 0) {
                     t.ticket_type_fields_div.find('.fieldsets').addClass("custome-field-border")
                    var dataFieldsets = data.data;
                    t.ticket_type_fields_div.find('.fieldsets').html("");
                    $.each(dataFieldsets, function (i, v) {
                        if (v.field_collections != undefined && v.field_collections.length > 0) {
                            var value = '';
                            var formats = '';
                            $.each(v.field_collections, function (i, v) {
                                var requiredMark = v.required == 1 ? '<sup style="color: red;font-weight: bold;font-size: 11px;">*</sup>' : '';
                                var labelHtml = '<span class="tkd-detail-label">' +
                                                    '<i class="bi bi-threads"></i> ' + v.name + ' ' + requiredMark +
                                                '</span>';
                                var inputHtml = '';
                                value = tickeTypeDetailsData[v.id] != undefined ? tickeTypeDetailsData[v.id] : '';

                                // Build input based on element type
                                if (v.element == 'dropdown') {
                                    inputHtml = '<select class="ticketTypeFields form-control" id="field' + v.id + '" name="field[' + v.id + ']">';
                                    if (v.options != null && v.option_type == 2) {
                                        $.each(v.options, function (i, d) {
                                            var selected = d == value ? 'selected' : '';
                                            inputHtml += "<option " + selected + " value='" + d + "'>" + d + "</option>";
                                        });
                                    }
                                    if (v.option_type == 1) {
                                        inputHtml += "<option value='" + value + "'>" + value + "</option>";
                                    }
                                    inputHtml += '</select>';
                                }else if (v.element == 'radio') {

                                    var labels = [];

                                    try {
                                        labels = typeof v.custom_label == 'string'
                                            ? JSON.parse(v.custom_label)
                                            : v.custom_label;
                                    } catch (e) {}

                                    inputHtml = '<div class="custom-option-group d-flex flex-wrap gap-3">';

                                    $.each(labels, function (k, opt) {

                                        var checked = opt == value ? 'checked' : '';

                                        inputHtml += `
                                            <div class="form-check">
                                                <input type="radio"
                                                    class="form-check-input ticketTypeFields"
                                                    name="field[${v.id}]"
                                                    id="field_${v.id}_${k}"
                                                    value="${opt}"
                                                    ${checked}>

                                                <label class="form-check-label"
                                                    for="field_${v.id}_${k}">
                                                    ${opt}
                                                </label>
                                            </div>
                                        `;
                                    });

                                    inputHtml += '</div>';

                                }else if (v.element == 'checkbox') {

                                    var labels = [];
                                    var selectedValues = [];

                                    try {
                                        labels = typeof v.custom_label == 'string'
                                            ? JSON.parse(v.custom_label)
                                            : v.custom_label;
                                    } catch (e) {}

                                    if (value) {
                                        selectedValues = Array.isArray(value)
                                            ? value
                                            : value.toString().split(',');
                                    }

                                    inputHtml = '<div class="custom-option-group d-flex flex-wrap gap-3">'+                             
                                     '<div class="custom-option-list d-flex flex-wrap gap-3 align-items-center">';

                                    $.each(labels, function (k, opt) {

                                        var checked = selectedValues.includes(opt)
                                            ? 'checked'
                                            : '';

                                        inputHtml += `
                                            <div class="form-check">
                                                <input type="checkbox"
                                                    class="form-check-input ticketTypeFields"
                                                    name="field[${v.id}][]"
                                                    id="field_${v.id}_${k}"
                                                    value="${opt}"
                                                    ${checked}>

                                                <label class="form-check-label"
                                                    for="field_${v.id}_${k}">
                                                    ${opt}
                                                </label>
                                            </div>
                                        `;
                                    });

                                    inputHtml += '</div></div>';

                                }else {
                                    // text, number, date, datetime, time, etc.
                                    if (v.element != 'date') {
                                        formats = v.format;
                                    }
                                    var elementType = v.element;
                                    if (elementType == "datetime") elementType = "datetime-local";
                                    if (elementType == "time") elementType = "time";
                                    inputHtml = '<input class="ticketTypeFields form-control" id="field' + v.id + '" name="field[' + v.id + ']" ' +
                                                'pattern="' + formats + '" value="' + value + '" type="' + elementType + '" />';
                                    formats = '';
                                }

                                // Build the row using the target design classes
                                var fullRow = '<div class="tkd-detail-row">' +
                                                labelHtml +
                                                '<span class="tkd-detail-value">' + inputHtml + '</span>' +
                                            '</div>';

                                t.ticket_type_fields_div.find('.fieldsets').append(fullRow);

                                // ---------- Everything below stays exactly the same (Select2, validation, etc.) ----------
                                if (v.element == 'dropdown') {
                                    if (v.option_type == 2) {
                                        $("#field" + v.id).select2({
                                            width: "100%",
                                            allowClear: true,
                                            placeholder: "Select"
                                        });
                                    } else {
                                        if (v.preDefinedOptions == 17) {
                                            $("#field" + v.id).select2({
                                                width: "100%",
                                                allowClear: true,
                                                dropdownParent: $("#field" + v.id).parent(),
                                                ajax: {
                                                    url: "http://164.52.201.124:8083/post_view_call_text",
                                                    method: "POST",
                                                    timeout: 0,
                                                    headers: {
                                                        "Content-Type": "application/json"
                                                    },
                                                    data: function () {
                                                        return "call esms_n.s_aa_process_at_sw_list()";
                                                    },
                                                    processResults: function (data) {
                                                        if (data.status === "pass" && Array.isArray(data.message[0])) {
                                                            return {
                                                                results: data.message[0].map(function (item) {
                                                                    return {
                                                                        text: item.sw,
                                                                        id: item.sw
                                                                    };
                                                                })
                                                            };
                                                        }
                                                        return {
                                                            results: []
                                                        };
                                                    },
                                                    delay: 300
                                                },
                                                placeholder: "Select"
                                            });

                                        }else if (v.preDefinedOptions != null && v.preDefinedOptions != 0) {
                                            let selector = $("#field" + v.id);
                                            selector.select2({
                                                allowClear: true,
                                                width: '100%',
                                                dropdownParent: selector.parent(),
                                                ajax: {
                                                    url: preDefinedOptionsArray[v.preDefinedOptions],
                                                    dataType: "json",
                                                    data: function (p) {
                                                        return {
                                                            search: p.term,
                                                            page: p.page || 1,
                                                            company_id: t.config.data.company_id
                                                        };
                                                    },
                                                    processResults: function (data) {
                                                        const responseData = data.data || data.results || [];
                                                        if (v.preDefinedOptions == 1) {
                                                            return {
                                                                results: responseData.map(function(item) {
                                                                    const record = item.a || item;
                                                                    return {
                                                                        // text: item['a'].text,
                                                                        // id: item['a'].text
                                                                        text: record.text,
                                                                        id: record.text
                                                                    }
                                                                })
                                                            };
                                                        } else if (v.preDefinedOptions == 2) {
                                                            return {
                                                                results: responseData.map(function(item) {
                                                                    const record = item.a || item;
                                                                    return {
                                                                        text: record.text,
                                                                        id: record.text,
                                                                        email: record.email,
                                                                        status: record.status,
                                                                        employee_num: record.employee_num,
                                                                        img_path: record.img_path,
                                                                        profile_img: record.profile_img,
                                                                        gravatar: record.gravatar
                                                                        // text: item.text,
                                                                        // id: item.text,
                                                                        // email: item.email,
                                                                        // status: item.status,
                                                                        // employee_num: item.employee_num,
                                                                        // img_path: item.img_path,
                                                                        // profile_img: item.profile_img,
                                                                        // gravatar: item.gravatar
                                                                    }
                                                                })
                                                            };
                                                        }else{
                                                            return {
                                                                results: responseData.map(function(item) {
                                                                    const record = item.a || item;
                                                                    return {
                                                                        // text: item['a'].text,
                                                                        // id: item['a'].text
                                                                        text: record.text,
                                                                        id: record.text
                                                                    };
                                                                })
                                                            };
                                                        }
                                                    },
                                                    delay: 300
                                                },
                                                placeholder: 'Select'
                                            });
                                        } else {

                                            $("#field" + v.id).select2({
                                                width: '100%',
                                                dropdownParent: $("#customField" + v.id).parent()
                                            });
                                        }
                                    }
                                }
                                if (v.required == 1) {
                                    if (v.element == 'radio') {
                                        $('[name="field[' + v.id + ']"]')
                                            .first()
                                            .rules("add", {
                                                required: true,
                                                messages: {
                                                    required: "Please select an option"
                                                }
                                            });
                                    } else if (v.element == 'checkbox') {
                                        $('[name="field[' + v.id + '][]"]')
                                            .first()
                                            .rules("add", {
                                                required: true,
                                                minlength: 1,
                                                messages: {
                                                    required: "Please select at least one option",
                                                    minlength: "Please select at least one option"
                                                }
                                            });
                                    } else {
                                        $("#field" + v.id).rules("add", {
                                            required: true
                                        });
                                    }
                                }
                                // --------------------------------------------------------------------------------------
                            })
                            t.ticket_type_fields_div.find(".ticketTypeFields").prop('disabled', t.config.canManageTicketType == true ? false : true);
                        }
                    });
                    t.ticket_type_fields_div.find('.ticket-bio-data').addClass("custom-boxes");
                } else {
                    t.ticket_type_fields_div.find('.ticket-bio-data').removeClass("custom-boxes");
                }
            },
            fail: function (data) {
                console.log(data)
            }
        })
        t.setstatusAsPerTicketType();
    }

    t.setCustomFieldForm = function () {
        var preDefinedOptionsArray = {
            1: t.config.url.getLocationByAjax,
            2: t.config.url.getUserByAjax,
            3:config.url.getByQueryDevice,
            4:config.url.getQueryPlace,
            5:config.url.getQueryManufacture,
            6:config.url.getQueryModel,
            7:config.url.getQueryComponent,
            8:config.url.getQueryTicket,
            9:config.url.getQueryTicketProcureRequest,
            10:config.url.getQueryRecord,
            11:config.url.getQueryTask,
            12:config.url.getQueryLicense,
            13:config.url.getQueryProject,
            14:config.url.getQueryPurchase,
            15:config.url.getQuerySupplier,
            16:config.url.getQueryContract,
            17:config.url.getQueryEsms,
            18:config.url.getQueryDepartment,
        }
        t.ticket_type_fields_div.find('.customFieldset').removeClass('hide').show();
        t.ticket_type_fields_div.find('.customFieldset').html("");
        var tickeTypeDetailsData = t.config.custom_fieldData != null ? JSON.parse(t.config.custom_fieldData) : [];
        t.frmUpdateStatus.find('#custom_field_data_section').append('<input type="hidden" name="customFieldCheck" id="customFieldCheck">');
        t.frmUpdateStatus.find('#custom_field_data_section').append('<div id="customFieldValues"></div>');
        $.ajax({
            url: config.url.getCustomField + '/' + t.config.data.id,
            type: 'GET',
            success: function (res) {
                if (res.fields && res.fields.length > 0) {
                    t.ticket_type_fields_div.find('.customFieldset').addClass('custome-field-border')  
                }
                $('.customFieldset').empty();
                $.each(res.fields, function (i, v) {
                    let value = v.value || '';
                    let html = '';
                    html += `
                        <div class="row align-items-center mb-1 custom-field-row">

                            <div class="col-md-3 text-md-center">
                                <label class="form-label mb-0">
                                    ${v.label}
                                    ${v.required == 1
                                        ? '<span class="text-danger">*</span>'
                                        : ''}
                                </label>
                            </div>
                            <div class="col-md-9">
                    `;
                    // TEXT
                    if (v.element === 'text') {
                        html += `<div class="input-group">`;
                        html += `
                            <input type="text"
                                class="form-control customField"
                                name="custom_fields[${v.id}]"
                                value="${value}">
                        `;
                        if (v.help_note) {
                            html += `
                                <span class="input-group-text custom-field-help"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }
                        html += `</div>`;
                    }
                    // DATE
                    else if (v.element === 'date') {
                        html += `<div class="input-group">`;
                        html += `
                            <input type="date"
                                class="form-control customField"
                                name="custom_fields[${v.id}]"
                                value="${value}">
                        `;
                        if (v.help_note) {
                            html += `
                                <span class="input-group-text custom-field-help"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }
                        html += `</div>`;
                    }
                    // TIME
                    else if (v.element === 'time') {
                        html += `<div class="input-group">`;
                        html += `
                            <input type="time"
                                class="form-control customField"
                                name="custom_fields[${v.id}]"
                                value="${value}">
                        `;
                        if (v.help_note) {
                            html += `
                                <span class="input-group-text custom-field-help"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }
                        html += `</div>`;
                    }
                    // DATETIME
                    else if (v.element === 'datetime') {
                        html += `<div class="input-group">`;
                        html += `
                            <input type="datetime-local"
                                class="form-control customField"
                                name="custom_fields[${v.id}]"
                                value="${value}">
                        `;
                        if (v.help_note) {
                            html += `
                                <span class="input-group-text custom-field-help"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }
                        html += `</div>`;
                    }
                    // DROPDOWN
                    else if (v.element === 'dropdown') {
                        html += `<div class="d-flex align-items-center gap-2">`;
                        html += `
                            <select class="form-control customField"
                                    id="customField${v.id}"
                                    name="custom_fields[${v.id}]">

                                <option value="">Select</option>
                        `;
                        let options = [];
                        if (v.options) {
                            try {
                                options = typeof v.options === 'string'
                                    ? JSON.parse(v.options)
                                    : v.options;
                            } catch (e) {
                                options = [];
                            }
                        }
                        $.each(options, function (k, opt) {
                            let selected = opt == value ? 'selected' : '';
                            html += `
                                <option value="${opt}" ${selected}>
                                    ${opt}
                                </option>
                            `;
                        });
                        html += `</select>`;
                        if (v.help_note) {
                            html += `
                                <span class="input-group-text custom-field-help"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }
                        html += `</div>`;
                    }

                    // RADIO
                    else if (v.element === 'radio') {
                        let labels = [];
                        try {
                            labels = JSON.parse(v.custom_label || '[]');
                        } catch (e) {}
                        html += `<div class="custom-option-group d-flex flex-wrap gap-3">`;
                        $.each(labels, function (k, opt) {
                            let checked = opt == value ? 'checked' : '';

                            html += `
                                <div class="form-check">
                                    <input type="radio"
                                        class="form-check-input customField"
                                        name="custom_fields[${v.id}]"
                                        value="${opt}"
                                        ${checked}>

                                    <label class="form-check-label">
                                        ${opt}
                                    </label>
                                </div>
                            `;
                        });

                        if (v.help_note) {
                            html += `
                                <span class="custom-field-help ms-2"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }

                        html += `</div>`;
                    }

                    // CHECKBOX
                    else if (v.element === 'checkbox') {

                        let labels = [];
                        let selectedValues = [];

                        try {
                            labels = JSON.parse(v.custom_label || '[]');
                        } catch (e) {}

                        if (value) {

                            if (Array.isArray(value)) {
                                selectedValues = value;
                            } else {
                                selectedValues = value.split(',');
                            }
                        }

                        // html += `<div class="custom-option-group d-flex flex-wrap gap-3">`;
                        html += `
                            <div class="custom-option-group">
                                <div class="custom-option-list d-flex flex-wrap gap-3 align-items-center">
                            `;

                        $.each(labels, function (k, opt) {

                            let checked =
                                selectedValues.includes(opt)
                                    ? 'checked'
                                    : '';

                            html += `
                                <div class="form-check">
                                    <input type="checkbox"
                                        class="form-check-input customField"
                                        name="custom_fields[${v.id}][]"
                                        value="${opt}"
                                        ${checked}>

                                    <label class="form-check-label">
                                        ${opt}
                                    </label>
                                </div>
                            `;
                        });

                        if (v.help_note) {
                            html += `
                                <span class="custom-field-help ms-2"
                                    data-note="${encodeURIComponent(v.help_note)}"
                                    style="cursor:pointer;">
                                    <i class="bi bi-info-circle-fill text-primary"></i>
                                </span>
                            `;
                        }

                        // html += `</div>`;
                        html += `
                                </div>

                                <div class="cf-error-wrapper"></div>

                            </div>
                            `;
                    }

                    // DEFAULT
                    else {

                        html += `
                            <input type="text"
                                class="form-control customField"
                                name="custom_fields[${v.id}]"
                                value="${value}">
                        `;
                    }

                    html += `
                            </div>
                        </div>
                    `;

                    $('.customFieldset').append(html);

                    // Validation
                    if (v.required == 1) {

                        if (v.element === 'radio') {

                            $('[name="custom_fields[' + v.id + ']"]').first().rules('add', {
                                required: true,
                                messages: {
                                    required: 'Please select an option'
                                }
                            });

                        } else if (v.element === 'checkbox') {

                            $('[name="custom_fields[' + v.id + '][]"]').first().rules('add', {
                                required: true,
                                minlength: 1,
                                messages: {
                                    required: 'Please select at least one option',
                                    minlength: 'Please select at least one option'
                                }
                            });

                        } else {

                            $('[name="custom_fields[' + v.id + ']"]').rules('add', {
                                required: true
                            });

                        }
                    }

                    if (v.element == 'dropdown') {

                        if (v.option_type == 2) {

                           $("#customField" + v.id).select2({
                                width: "100%",
                                allowClear: true,
                                placeholder: "Select"
                            }).val(value).trigger('change');
                        } else {

                            if (v.preDefinedOptions == 17) {

                                $("#customField" + v.id).select2({
                                    width: "100%",
                                    allowClear: true,
                                    dropdownParent: $("#customField" + v.id).parent(),

                                    ajax: {
                                        url: "http://164.52.201.124:8083/post_view_call_text",
                                        method: "POST",
                                        timeout: 0,

                                        headers: {
                                            "Content-Type": "application/json"
                                        },

                                        data: function () {
                                            return "call esms_n.s_aa_process_at_sw_list()";
                                        },

                                        processResults: function (data) {

                                            if (data.status === "pass" && Array.isArray(data.message[0])) {

                                                return {
                                                    results: data.message[0].map(function (item) {
                                                        return {
                                                            text: item.sw,
                                                            id: item.sw
                                                        };
                                                    })
                                                };
                                            }

                                            return {
                                                results: []
                                            };
                                        },

                                        delay: 300
                                    },

                                    placeholder: "Select"
                                });

                            }else if (v.preDefinedOptions != null && v.preDefinedOptions != 0) {
                                let selector = $("#customField" + v.id);
                                selector.select2({
                                    allowClear: true,
                                    width: '100%',
                                    dropdownParent: selector.parent(),
                                    ajax: {
                                        url: preDefinedOptionsArray[v.preDefinedOptions],
                                        dataType: "json",

                                        data: function (p) {
                                            return {
                                                search: p.term,
                                                page: p.page || 1,
                                                company_id: t.config.data.company_id
                                            };
                                        },

                                        processResults: function (data) {
                                            const responseData = data.data || data.results || [];
                                            if (v.preDefinedOptions == 1) {
                                            return {
                                                results: responseData.map(function(item) {
                                                    const record = item.a || item;
                                                    return {
                                                        // text: item['a'].text,
                                                        // id: item['a'].text
                                                           text: record.text,
                                                           id: record.text
                                                        }
                                                    }),
                                                    pagination: {
                                                        more: data.pagination.more
                                                    }
                                                };
                                                
                                            } else if (v.preDefinedOptions == 2) {
                                                return {
                                                    results: $.map(data.results, function (item) {
                                                        const record = item.a || item;
                                                        return {
                                                            // text: item.text,
                                                            // id: item.text,
                                                            // email: item.email,
                                                            // status: item.status,
                                                            // employee_num: item.employee_num,
                                                            // img_path: item.img_path,
                                                            // profile_img: item.profile_img,
                                                            // gravatar: item.gravatar
                                                            text: record.text,
                                                            id: record.text,
                                                            email: record.email,
                                                            status: record.status,
                                                            employee_num: record.employee_num,
                                                            img_path: record.img_path,
                                                            profile_img: record.profile_img,
                                                            gravatar: record.gravatar
                                                        }
                                                    }),
                                                    pagination: {
                                                        more: data.pagination.more
                                                    }
                                                };
                                            }else{
                                                return {
                                                    results: responseData.map(function(item) {
                                                        const record = item.a || item;
                                                        return {
                                                            // text: item['a'].text,
                                                            // id: item['a'].text,
                                                            text:record.text,
                                                            id:record.text
                                                    };
                                                }),
                                                pagination: {
                                                    more: data.pagination.more
                                                }
                                            };
                                            }
                                        },
                                        delay: 300
                                    },

                                    placeholder: 'Select ' + v.label
                                });
                                if (value) {
                                    var option = new Option(value, value, true, true);
                                    selector.append(option).trigger('change');
                                }
                            } else {

                                $("#customField" + v.id).select2({
                                    width: '100%',
                                    dropdownParent: $("#customField" + v.id).parent()
                                });
                            }
                        }
                    }

                    $("#customField" + v.id).rules("add", {
                        required: v.required == 1
                    });
                });
            }
        });


        // Help Note Click
        $(document).on('click', '.custom-field-help', function () {
            let note = decodeURIComponent($(this).data('note'));

            $('#helpNoteContent').html(note);

            $('#helpNoteDialog').modal('show');
        });
    }
    const limit = (string, length, end = "...") => {
        return string.length < length ? string : string.substring(0, length) + end
    }
    setDateValue = function (val, endID) {
        $.ajax({
            url: t.config.url.getDayForEndDate,
            type: "POST",
            data: { ticketID: $('#ticketID').val(), date: val },
            success: function (res) {
                if (res != 0 && endID != '') {
                    $("#customField" + endID).val(res);
                    $("#customField" + endID).prop('readonly', true);
                    $("#customValue" + endID).val(res);
                } else {
                    $("#customField" + endID).prop('readonly', false);
                    $('#customField' + endID).datetimepicker({ format: 'd-m-Y H:i' });
                }
            }
        });
    };
    setEndValue = function (id) {
        $('#customValue' + id).val($('#customField' + id).val());
    }

    $(document).on('click', '.custom_field_info', function () {
        var id = $(this).attr("data-id");
        var url = t.config.url.getCustomFieldNote + '/' + id;
        $("#helpNoteContent").html('');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $("#helpNoteContent").html(response.help_note);
                $("#helpNoteDialog").modal("show");
            },
            error: function (xhr, status, error) {
                console.log("Error:", error);
                $("#helpNoteContent").html('<p class="text-danger">Failed to load data.</p>');
            }
        });
    });

    t.setstatusAsPerTicketType = function () {
        var status = parseInt(t.data.status_id);
        if (status === 5 || status === 6 || status == t.config.statusRejected) {
            t.frmComment.closest('.panel').addClass("hide");
            t.frmUpdateStatus.el.statusId.empty();
            $.each(t.config.statuses1, function (i, v) {
                t.frmUpdateStatus.el.statusId.append($('<option>').val(v.id).text(v.name).attr('form', v.status_form));
            });
            t.frmUpdateStatus.el.statusId.val(t.data.status_id).trigger("change");
        } else {
            t.frmUpdateStatus.el.statusId.empty();
            $.ajax({
                method: 'GET',
                url: t.config.url.getStatusByTicketType + "/" + t.ticketTypeSelect2.val(),
                data : {
                    company_id : t.config.data.company_id
                },
                success: function (res) {
                    if (res.status == 'success') {
                        t.frmUpdateStatus.el.statusId.empty();
                        $.each(res.data, function (i, v) {
                            t.frmUpdateStatus.el.statusId.append($('<option>').val(v.id).text(v.name).attr('form', v.status_form));
                        });
                        t.frmUpdateStatus.el.statusId.val(t.data.status_id).trigger("change");
                    }
                }
            });
        }
    }

    t.setTicketTypeFields = function () {
        var status = t.frmUpdateStatus.el.statusId.val();
        t.ticket_bug_details_div.find(".optionsDiv").addClass('hide').hide();
        if (t.ticketTypeSelect2.val() == 1) {
            if (t.config.ticketTypeFields.bug[status] != undefined) {
                for (i = 0; i < t.config.ticketTypeFields.bug[status].length; i++) {
                    t.ticket_bug_details_div.find("." + t.config.ticketTypeFields.bug[status][i]).removeClass('hide').show();
                }
            }
        }
        if (t.ticketTypeSelect2.val() == 2) {
            if (t.config.ticketTypeFields.bug[status] != undefined) {
                for (i = 0; i < t.config.ticketTypeFields.cr[status].length; i++) {
                    t.ticket_cr_details_div.find("." + t.config.ticketTypeFields.cr[status][i]).removeClass('hide').show();
                }
            }
        }
    }

    t.ticketTypeSelect2.on('change', $.proxy(t.setTicketTypeForm));
    t.ticketTypeSelect2.val(t.data.ticket_type).trigger('change').prop('disabled', t.config.canManageTicketType == true ? false : true);
    setTimeout(function () {
        t.ticketTypeBootstrapping = false;
    }, 0);

    t.getTicketTagDetails = function () {
         t.mdlManageTags.modal('show');
        $.get(t.config.url.get_data_for_transfer + "/" + t.data.id, function (result) {
            t.mdlManageTags.assigned_tags.html("");
            $.each(result.data.tags.tags, function (key, value) {
                var newOption = new Option(value.tags, value.tags, true, true);
                t.mdlManageTags.assigned_tags.append(newOption);
            });
        })
    }

    t.checkTechnicalAvabilityForEdit = function () {
        var attendarId = t.frmEdit.el.assign_to.val();
        t.frmEdit.find(".availabilityError, .availabilitySuccess").html("");
        if (attendarId == '') {
            return false;
        }
        $.ajax({
            url: t.config.url.getTechCurrentStatusById + '/' + attendarId,
            method: 'GET',
            success: function (result) {
                if (result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmEdit.find(".availabilityError").html("The technician is unavailable but still you can assign ticket. Technician will revert only once he/she will available.")
                } else {
                    t.frmEdit.find(".availabilitySuccess").html("The technician is available.")
                }
            }
        });
    }

    t.saveTicketDetailTags = function (e) {
        e.preventDefault();
        var myformData = new FormData();
        myformData.append('ticketId', t.mdlManageTags.find('#id').val());
        myformData.append('tags', t.mdlManageTags.assigned_tags.val());
        myformData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        $.ajax({
            method: 'post',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            url: t.config.url.updateTicketTags,
            success: function (res) {
                if (res.status == 'success') {
                    sweetAlert('center', 'success', res);
                    t.mdlManageTags.modal('hide');
                    window.location.reload();
                } else {
                    sweetAlert('center', 'error', res);
                }
            }
        })
    }

    t.mdlManageTags.getTicketTags.on('click', t.getTicketTagDetails);
    t.mdlManageTags.saveTags.on('click', t.saveTicketDetailTags);

    t.httpCall = true;
    t.mdlTktHistory = t.page.find("#mdl-ticket-history");
    t.result = t.page.find("#result");

    t.attachment.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attach");
        let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalCommentFileSize - fileSize >= 0) {
            totalCommentFileSize -= fileSize;
        }
        let fileName = p.find('.bord-btm p').text().split(' [')[0];
        const index = uploadedCommentFiles.indexOf(fileName);
        if (index > -1) {
            uploadedCommentFiles.splice(index, 1);
        }
        $.post(t.config.url.attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") }, function (d) { });
        p.fadeOut("slow").remove();
    };

    t.attachment_update.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attachs");
        let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalUpdateStatusFileSize - fileSize >= 0) {
            totalUpdateStatusFileSize -= fileSize;
        }
        let fileName = p.find('.bord-btm p').text().split(' [')[0];
        const index = uploadedUpdateStatusFiles.indexOf(fileName);
        if (index > -1) {
            uploadedUpdateStatusFiles.splice(index, 1);
        }
        $.post(t.config.url.attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") }, function (d) { });
        p.fadeOut("slow").remove();
    };

    $.validator.addMethod(
        "multipleemailaddress",
        function (value, element) {
            if (this.optional(element))
                return true;
            var emails = value.toString().split(/[;,]+/);
            $('#frm_comment').find('#shows_error_cc').html('');
            $('#frm_update_status').find('#shows_error_cc').html('');
            const emailReg = new RegExp(/^(\s?[^\s,]+@[^\s,]+\.[^\s,]+\s?,)*(\s?[^\s,]+@[^\s,]+\.[^\s,]+)$/);
            valid = true;
            for (var i in emails) {
                value = emails[i];
                valid = valid && emailReg.test(value) && $.validator.methods.email.call(this, $.trim(value), element);
            }
            return valid;
        },
        $.validator.messages.multipleemailaddress
    );

    t.frmUpdateStatusValidator = t.frmUpdateStatus.validate({
        onsubmit: false,
        rules: {
            status_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
                max: 1000
            },
            cc_emails: {
                multipleemailaddress: true
            },
        },
        messages: {
            cc_emails: {
                multipleemailaddress: "Please enter a valid E-mail address"
            }
        }
    });

   t.frmCommentValidator = t.frmComment.validate({
    onsubmit: false,
    rules: {
        comment: {
            required: true
        },
        cc_emails: {
            multipleemailaddress: true
        }
    },
    messages: {
        cc_emails: {
            multipleemailaddress: "{{ trans('validation.multiple_email') }}"
        }
    },
    errorPlacement: function (error, element) {
        var name = element.attr('name');
        if (name === 'comment') {
            // Insert error directly after the textarea
            error.insertAfter(element);
        } else if (name === 'cc_emails') {
            // Insert into the existing container for CC errors
            $('#shows_error_cc').html(error);
        } else {
            // Default fallback
            error.insertAfter(element);
        }
    },
    errorClass: 'text-danger small',
    validClass: 'text-success small'
});

    t.frmAssignToValidator = t.frmAssignTo.validate({
        onsubmit: false,
        ignore: [], 
        rules: {
            assigned_to: {
                required: true,
                str_name: true
            }
        },
        errorPlacement: function (error, element) {
        element
            .closest('.amg-form-field')
            .find('.amg-form-error-wrap')
            .append(error);
    }
    });

    t.frmProblemImpactTicektValidator = t.frmaddProblem.validate({
        onsubmit: false,
        rules: {
            problem_mgt: {
                required: true,
                str_name: true
            }
        },
        errorPlacement: function(error, element) {
            element.closest('.amg-form-field')
                .find('.amg-form-error-wrap')
                .html(error);
        }
    });

    t.frmIncidentTicektValidator = t.frmaddIncident.validate({
        onsubmit: false,
        rules: {
            incident: {
                required: true,
                str_name: true
            }
        },
        errorPlacement: function(error, element) {
            element.closest('.amg-form-field')
                .find('.amg-form-error-wrap')
                .html(error);
        }
    });

    t.frmChangeCreatorValidator = t.frmChangeCreator.validate({
        onsubmit: false,
        rules: {
            creator_id: {
                required: true,
                str_name: true
            }
        },
        errorPlacement: function(error, element) {
            element.closest('.amg-form-field')
                .find('.amg-form-error-wrap')
                .html(error);
        }
    });
    var feedbackRequired = t.config.tkt_config.feedback_required || 0;
    var maxFeedbackRating = t.config.tkt_config.feedback_max_rating || 0;

    $.validator.addMethod("summernote1", function (value, element) {
        var selectedRating = parseInt($('input[name="feedback"]:checked').val() || 0);
        var maxFeedbackRating = t.config.tkt_config.feedback_max_rating || 0;
        if (selectedRating > maxFeedbackRating) {
            return true; 
        }
        return !$('#remarks').summernote('isEmpty');
    }, "Remarks is required.");

    $.validator.addMethod("summernoteMaxLength", function (value, element, max) {
        var html = $(element).summernote('code');
        html = $('<textarea/>').html(html).text();
        return html.length <= max;
    }, $.validator.format("Maximum {0} characters allowed."));

    $('input[name="feedback"]').on('change', function () {
        var selectedRating = parseInt($(this).val());
        var maxFeedbackRating = t.config.tkt_config.feedback_max_rating || 0;
        $('#remarks_required').toggleClass(
            'd-none',
            selectedRating > maxFeedbackRating
        );
    });

    t.frmFeedbackValidator = t.frmFeedback.validate({
        ignore: [],
        onsubmit: false,
        rules: {
            remarks: {
                summernote1: true,
                summernoteMaxLength: 2000
            }
        },
        messages: {
            remarks: {
                summernote1: "Remarks is required.",
                summernoteMaxLength: "Maximum 2000 characters allowed."
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr('id') === 'remarks') {
                error.insertAfter(element.next('.note-editor'));
            } else {
                element.closest('.amg-form-field')
                    .find('.amg-form-error-wrap')
                    .html(error);
            }
        }
    });

    t.frmUpdateFeedbackValidator = t.mdl.frm.validate({
        ignore: [],
        onsubmit: false,
        rules: {
            remarks: {
                summernote1: true,
                summernoteMaxLength: 2000
            }
        },
        messages: {
            remarks: {
                summernote1: "Remarks is required.",
                summernoteMaxLength: "Maximum 2000 characters allowed."
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr('id') === 'remarks') {
                error.insertAfter(element.next('.note-editor'));
            } else {
                element.closest('.amg-form-field')
                    .find('.amg-form-error-wrap')
                    .html(error);
            }
        },
    });

    t.frmReopenValidator = t.frmReopen.validate({
        onsubmit: false,
        rules: {
            comment: {
                required: true,
                maxlength: 2000,
                acceptable_spcl_chr: true
            }
        }
    });

    t.frmTransferValidator = t.frmTransfer.validate({
        onsubmit: false,
        rules: {
            department_id: {
                required: true,
                str_name: true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmForEditValidator = t.frmEdit.validate({
        onsubmit: false,
        rules: {
            department_id: {
                required: true,
                str_name: true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
            }
        }, 
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.initUpdateForm = function (withData) {
        t.frmUpdateStatus.el.id.val(t.data.id);

        t.frmUpdateStatus.el.priorityId.empty();
        $.each(t.config.priorities, function (i, v) {
            var option = new Option(v.name, v.id);
            var priorityName = (v.name || '').toLowerCase();
            var priorityColor = '#FF0A0E';
            if (priorityName == 'critical') {
                priorityColor = '#9f1239';
            } else if (priorityName == 'medium') {
                priorityColor = '#f59e0b';
            } else if (priorityName == 'low') {
                priorityColor = '#22c55e';
            }
            $(option).attr('data-color', priorityColor);
            t.frmUpdateStatus.el.priorityId.append(option);
        });
        if (typeof withData != "undefined") {
            t.frmUpdateStatus.el.priorityId.val(t.data.priority_id);
            t.frmUpdateStatus.el.tat.val(t.data.tat);
        }
        t.frmUpdateStatus.el.priorityId.trigger("change");
        // t.frmUpdateStatus.el.cc_emails.val(t.data.cc_emails);
        if (t.data.cc_emails) {
            let emails_array = t.data.cc_emails.split(',').map(e => e.trim());
            const $select = t.frmUpdateStatus.el.cc_emails;
            emails_array.forEach(email => {
                if ($select.find('option[value="' + email + '"]').length === 0) {
                    $select.append(new Option(email, email, false, false));
                }
            });
            $select.val(emails_array).trigger('change');
        }
        // t.frmUpdateStatus.el.follow_cc.prop('checked', false);
        if (t.frmUpdateStatus.el.priorityId.prop("disabled")) {
            let val = t.frmUpdateStatus.el.priorityId.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmUpdateStatus);
        }
        if (t.frmUpdateStatus.el.tat.prop("disabled")) {
            let tatVal =  t.frmUpdateStatus.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmUpdateStatus);
        }
    };
    t.frmUpdateStatus.el.priorityId.on("select2:select", function (e) {
        var data = e.params.data;
        var selectedId = parseInt(data.id, 10);

        var tatObj = t.config.priorities.find(function(item) {
            return item.id === selectedId;
        });

        if (tatObj) {
            t.frmUpdateStatus.el.tat.val(tatObj.service_time);
        }
    });

    t.updateCCField = function () {
        var cc_emails = typeof t.data != "undefined" && typeof t.data.cc_emails != "undefined" ? t.data.cc_emails : "";
        t.frmUpdateStatus.el.cc_emails.val(cc_emails);
        t.frmComment.el.cc_emails.val(cc_emails);
    };

    t.setupSpamBtn = function () {
        let btn = t.page.find(".js-act-spam");
        if (t.data.spam == 1) {
            btn.attr('title',t.config.translations.remove_from_spam_list)
            .data('title', t.config.translations.remove_from_spam_list)
            .find('span')
            .text(t.config.translations.mark_not_spam);
            btn.find('svg').html(`
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0z"/>
                <path d="M12.03 5.97a.75.75 0 0 0-1.06-1.06L7 8.88 5.03 6.91a.75.75 0 1 0-1.06 1.06l2.5 2.5a.75.75 0 0 0 1.06 0z"/>
            `);

        } else {
        btn.attr('title', t.config.translations.add_to_spam_list)
            .data('title', t.config.translations.add_to_spam_list)
            .find('span')
            .text(t.config.translations.mark_spam);
            btn.find('svg').html(`
                 <path
                    d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.547-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.8 11.8 0 0 1-2.517 2.453 7 7 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7 7 0 0 1-1.048-.625 11.8 11.8 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 63 63 0 0 1 5.072.56" />
                <path
                    d="M6.146 5.146a.5.5 0 0 1 .708 0L8 6.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 7l1.147 1.146a.5.5 0 0 1-.708.708L8 7.707 6.854 8.854a.5.5 0 1 1-.708-.708L7.293 7 6.146 5.854a.5.5 0 0 1 0-.708" />
            `);
        }
    };

    t.isSpamTicket = function () {
        return t.data.spam == 1;
    };

    function initCcEmailsSelect2($el) {
        $el.select2({
            width: "100%",
            placeholder: "Add CC",
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
                        q: params.term || "",
                        page: params.page || 1,
                        company_id: t.config.data.company_id,

                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: (params.page * 20) < data.total
                        }
                    }
                },
                cache: true
            }
        }).on('select2:unselecting', function(e) {
            var selected = $(e.params.args.data.element);
            if (selected.data('locked')) {
                e.preventDefault();
            }
        });
    }
    initCcEmailsSelect2(t.frmUpdateStatus.el.cc_emails);
    initCcEmailsSelect2(t.frmComment.el.cc_emails);

    t.shouldShowUpdateTicketPanel = function () {
        var wai = parseInt(t.config.wai);
        var status = parseInt(t.data.status_id);
        var rejected = parseInt(t.config.statusRejected);

        return wai === 23 && !t.isSpamTicket() && status !== 6 && status !== rejected;
    };

    t.syncUpdateTicketPanelVisibility = function () {
        var panel = t.frmUpdateStatus.closest(".panel");

        if (t.shouldShowUpdateTicketPanel()) {
            panel.removeClass("hide").show();
        } else {
            panel.addClass("hide").hide();
        }
    };

    t.setActionVisible = function (selector, shouldShow) {
        var actions = t.page.find(selector);

        actions.each(function () {
            var action = $(this);
            var timer = action.data('tkdActionTimer');

            if (timer) {
                clearTimeout(timer);
            }

            if (shouldShow) {
                if (!action.hasClass('hidden')) {
                    action
                        .removeClass('tkd-action-animating-out')
                        .addClass('tkd-action-animating-in');

                    action.data('tkdActionTimer', setTimeout(function () {
                        action.removeClass('tkd-action-animating-in').removeData('tkdActionTimer');
                    }, 190));
                    return;
                }

                action.data('tkdActionTimer', setTimeout(function () {
                    action
                        .removeClass('hidden tkd-action-animating-out')
                        .addClass('tkd-action-animating-in');
                    t.updateActionOverflow();

                    action.data('tkdActionTimer', setTimeout(function () {
                        action.removeClass('tkd-action-animating-in').removeData('tkdActionTimer');
                    }, 190));
                }, 20));
                return;
            }

            if (action.hasClass('hidden')) {
                action.removeClass('tkd-action-animating-in tkd-action-animating-out');
                return;
            }

            action.removeClass('tkd-action-animating-in').addClass('tkd-action-animating-out');
            action.data('tkdActionTimer', setTimeout(function () {
                action
                    .addClass('hidden')
                    .removeClass('tkd-action-animating-out')
                    .removeData('tkdActionTimer');
                t.updateActionOverflow();
            }, 190));
        });
    };

    t.showAction = function (selector) {
        t.setActionVisible(selector, true);
    };

    t.hideAction = function (selector) {
        t.setActionVisible(selector, false);
    };

    t.setActionOverflowHidden = function (actions, shouldHide) {
        actions.each(function () {
            var action = $(this);
            var timer = action.data('tkdOverflowTimer');

            if (timer) {
                clearTimeout(timer);
            }

            if (shouldHide) {
                if (action.hasClass('tkd-action-overflow-hidden') || action.hasClass('hidden')) {
                    action.removeClass('tkd-action-animating-in tkd-action-animating-out');
                    return;
                }

                action.removeClass('tkd-action-animating-in').addClass('tkd-action-animating-out');
                action.data('tkdOverflowTimer', setTimeout(function () {
                    action
                        .addClass('tkd-action-overflow-hidden')
                        .removeClass('tkd-action-animating-out')
                        .removeData('tkdOverflowTimer');
                }, 190));
                return;
            }

            if (!action.hasClass('tkd-action-overflow-hidden')) {
                action.removeClass('tkd-action-animating-out');
                return;
            }

            action.data('tkdOverflowTimer', setTimeout(function () {
                action
                    .removeClass('tkd-action-overflow-hidden tkd-action-animating-out')
                    .addClass('tkd-action-animating-in');

                action.data('tkdOverflowTimer', setTimeout(function () {
                    action.removeClass('tkd-action-animating-in').removeData('tkdOverflowTimer');
                }, 190));
            }, 20));
        });
    };

    t.updateActionOverflow = function () {
        if (!t.actionBar.length || !t.actionMore.length) return;
        const isExpanded = t.actionBar.hasClass('is-expanded');
        const allBtns = t.actionBar.find('.tkd-action-btn').not('.js-action-more');
        const visibleBtns = allBtns.filter(function () {
            const $btn = $(this);
            return !$btn.hasClass('hidden') && !$btn.hasClass('tkd-action-animating-out');
        });

        if (visibleBtns.length > 4) {
            t.actionMore.removeClass('d-none').attr('aria-expanded', isExpanded);
            const hiddenGroup = visibleBtns.slice(4);
            const firstFour = visibleBtns.slice(0, 4);
            if (!isExpanded) {
                t.setHidden(hiddenGroup, true);
                t.setHidden(firstFour.filter('.tkd-action-overflow-hidden'), false);
            } else {
                t.setHidden(visibleBtns.filter('.tkd-action-overflow-hidden'), false);
            }
        } else {
            t.actionBar.removeClass('is-expanded');
            t.actionMore.addClass('d-none').attr('aria-expanded', 'false');
            t.setHidden(visibleBtns.filter('.tkd-action-overflow-hidden'), false);
        }
    };

    t.setHidden = function ($elements, hide) {
        $elements.each(function () {
            $(this).toggleClass('d-none', hide)
                .toggleClass('tkd-action-overflow-hidden', hide);
        });
    };

    t.toggleActionOverflow = function (e) {
        e.preventDefault();
        bootstrap.Tooltip.getInstance(this)?.hide();
        t.actionBar.toggleClass('is-expanded');
        t.updateActionOverflow();
    };
    const debounce = (fn, delay = 50) => {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    };
    t._debouncedUpdate = debounce(t.updateActionOverflow.bind(t), 50);

    $(function () {
        t._debouncedUpdate();
        $(document).on('ajaxComplete', t._debouncedUpdate).on('permissionUpdated overflowUpdate', t._debouncedUpdate);

        if (t.actionBar.length) {
            const observer = new MutationObserver((mutations) => {
                const shouldUpdate = mutations.some(m =>
                    m.type === 'childList' ||
                    (m.type === 'attributes' &&
                    m.attributeName === 'class' &&
                    m.target.classList.contains('tkd-action-btn'))
                );
                if (shouldUpdate) t._debouncedUpdate();
            });

            observer.observe(t.actionBar[0], {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class']
            });

            t._overflowObserver = observer;
        }
    });

    t.configFun = function () {
        var wai = parseInt(t.config.wai),
            status = parseInt(t.data.status_id);

        t.hideAction(".js-act-self-assign");
        t.hideAction(".js-act-change-creator");

        t.frmComment.closest('.panel').addClass("hide");
        if ((status !== 5 && status !== 6 && t.config.access_privilege == "1" && !t.isSpamTicket()) || (status !== 5 && status !== 6 && wai === 24 && !t.isSpamTicket())) {
            t.frmComment.closest('.panel').removeClass("hide");
            // t.frmComment.el.follow_cc.prop('checked', false);
            if(t.data.cc_emails){
                let emails_array = t.data.cc_emails.split(',').map(e => e.trim());
                const $select = t.frmComment.el.cc_emails;
                emails_array.forEach(email => {
                    if ($select.find('option[value="' + email + '"]').length === 0) {
                        $select.append(new Option(email, email, false, false));
                    }
                });
                $select.val(emails_array).trigger('change');
            }
        }

        t.setupSpamBtn();
        if ($('#ticketID').val()) {
            t.checkReopenEligibility($('#ticketID').val());
        }
        if (wai) {
            t.feedBackRating.removeClass("hide");
            t.feedBackRating.ifFBF.addClass("hide");
            t.feedBackRating.elseFBF.addClass("hide");
            if (typeof t.config.feedback == "object" && t.config.feedback != "") {
                t.feedBackRating.ifFBF.removeClass("hide");
                t.feedBackRating.lfbImg.attr("src", t.feedBackRating.attr('data-img') + "/" + t.config.feedback.lfb + ".gif");
                t.feedBackRating.lfbScore.text(t.config.feedback.lfb);
                t.feedBackRating.ofbImg.attr("src", t.feedBackRating.attr('data-img') + "/" + Math.round(t.config.feedback.ofb) + ".gif");
                t.feedBackRating.ofbScore.text(t.config.feedback.ofb);
            } else {
                t.feedBackRating.elseFBF.removeClass("hide");
            }
        }

        if (wai === 23) {
            if (status === 5 || status === 6 || status == t.config.statusRejected) {
                // t.frmComment.closest('.panel').addClass("hide");
                t.frmUpdateStatus.el.statusId.empty();
                if (status === 5 || status == t.config.statusRejected) {
                    $.each(t.config.statuses1, function (i, v) {
                        t.frmUpdateStatus.el.statusId.append($('<option>').val(v.id).text(v.name).attr('form', v.status_form));
                    });
                }
            } else {
                t.frmUpdateStatus.el.statusId.empty();
                $.each(t.config.statuses, function (i, v) {
                    t.frmUpdateStatus.el.statusId.append($('<option>').val(v.id).text(v.name).attr('form', v.status_form));
                });
                // t.frmComment.closest('.panel').removeClass("hide");
            }

            if (status === 6 || status == t.config.statusRejected) {
                t.frmComment.closest('.panel').addClass('hide');
            }

            if (status === 6 || status == t.config.statusRejected) {
                t.frmUpdateStatus.closest(".panel").hide();
            }

            t.frmUpdateStatus.el.statusId.val(t.data.status_id).trigger("change")
            if (!t.isSpamTicket() && status !== 6) {
                t.frmUpdateStatus.closest(".panel").removeClass("hide");
            }
        } else if (wai === 24 || wai === 25) {
            t.frmUpdateStatus.closest(".panel").addClass("hide");
            // t.frmComment.el.is_note.prop("checked", false).closest("label").addClass("hide");
            var fb = parseInt(t.data.feedback);
            if (status === 5 && t.config.user.id == t.data.creator_id && (t.data.merge_primary == null || t.data.merge_primary < 1)) {
                // t.frmComment.closest('.panel').addClass("hide");
                if (isNaN(fb) || fb === 0) {
                    setTimeout(function () {
                        t.loadFeedbackMdl();
                    }, 1000);
                }
            }
            if (status == t.config.statusRejected) {
                t.frmComment.closest('.panel').addClass("hide");
            }
            if (wai != 25) {
                t.expireInfo.addClass("hide");
            }
        } else {
            t.frmUpdateStatus.closest(".panel").addClass("hide");
            // t.frmComment.closest('.panel').addClass("hide");
        }

        if (wai == 25 || wai == 26) {
            t.showAction(".js-act-self-assign");
        }

        if (wai === 24) {
            t.hideAction(".js-act-delete");
            t.hideAction(".js-act-assign-to");
            t.hideAction(".js-act-transfer");
            t.showAction(".js-act-staring");
            t.hideAction(".js-act-self-assign");
            if (status === 5 || (status === 6 && (t.data.merge_primary == null || t.data.merge_primary < 1))) {
                t.showAction(".js-act-reopen");
            }
            t.hideAction(".js-act-spam");
        } else {
            t.showAction(".js-act-delete");
            t.showAction(".js-act-assign-to");
            t.showAction(".js-act-transfer");
            t.showAction(".js-act-staring");
            t.hideAction(".js-act-reopen");
            if (status != 5 && status != 6 && wai != 27 && wai != 28 && wai != 29) {
                t.showAction(".js-act-spam");
            }
        }

        if (wai == 27 || wai == 29) {
            t.hideAction('.js-act-staring');
        }

        /* show action controls based on user privilege */
        t.hideAction(".js-act-transfer");
        t.hideAction(".js-act-delete");
        t.hideAction(".js-act-assign-to");
        t.hideAction(".js-act-self-assign");
        t.hideAction(".js-act-change-creator");

        if (typeof t.config.action_controls != "undefined" && t.config.access_privilege == "1") {
            if (typeof t.config.action_controls.ctrl_transfer != "undefined" && t.config.action_controls.ctrl_transfer === 1) {
                t.showAction(".js-act-transfer");
            }
            if (typeof t.config.action_controls.ctrl_assign != "undefined" && t.config.action_controls.ctrl_assign === 1) {
                t.showAction(".js-act-assign-to");
            }
            if (typeof t.config.action_controls.ctrl_delete != "undefined" && t.config.action_controls.ctrl_delete === 1) {
                t.showAction(".js-act-delete");
            }
            if (typeof t.config.action_controls.ctrl_self_assign != "undefined" && t.config.action_controls.ctrl_self_assign === 1 && t.config.user.id != t.config.data.assigned_to) {
                t.showAction(".js-act-self-assign");
            }
            if (typeof t.config.action_controls.ctrl_change_creator != "undefined" && t.config.action_controls.ctrl_change_creator === 1) {
                t.showAction(".js-act-self-assign");
                t.showAction(".js-act-change-creator");
            }
        }

        t.staringUi();
        t.syncUpdateTicketPanelVisibility();
        t.updateActionOverflow();
    };

    t.loadFeedbackMdl = function () {
        t.frmFeedback.el.id.val(t.data.id);
        t.frmFeedback.el.token.val(t.config.token);
        t.frmFeedback.el.remarks.summernote('code', '');
        t.frmFeedback.el.divQuestion.removeClass("hide");
        t.frmFeedback.el.divResult.addClass("hide");
        t.frmFeedback.btnSubmit.removeClass("hide");
        t.mdlFeedback.modal("show");
    };

    t.frmFeedbackSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (parseInt(t.config.wai) !== 24 && parseInt(t.config.wai) !== 25) {
            var data = {
                'msg': 'This ticket is not assigned to you. Please contact your manager.',
            };
            sweetAlert('center', 'error', data);
            return;
        }

        if (t.frmFeedbackValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.frmFeedback.btnSubmit.attr("disabled", true);
        t.frmFeedback.mdl_popup_loader.addClass('active');

        t.httpCall = false;
        var formData = new FormData(t.frmFeedback.get(0));
        var http = $.ajax({
            url: t.config.url.add_feedback,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmFeedback.btnSubmit.removeAttr("disabled");
                    t.frmFeedback.mdl_popup_loader.removeClass('active');
                    t.data = data.data;
                    t.refreshExpireInfo();
                    t.refreshworkAroundInfo();
                    t.refreshResponseInfo();
                    t.configFun();
                    t.mdlFeedback.modal("hide");
                    sweetAlert('center', 'success', data);
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.frmFeedback.btnSubmit.removeAttr("disabled");
                    t.frmFeedback.mdl_popup_loader.removeClass('active');
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.frmFeedback.btnSubmit.removeAttr("disabled");
            t.frmFeedback.mdl_popup_loader.removeClass('active');
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.frmFeedback.btnSubmit.removeAttr("disabled");
            t.frmFeedback.mdl_popup_loader.removeClass('active');
            t.httpCall = true;
        });
    };

    t.attachmentView = function (e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        var src = $(this).attr('data-view');
        var name = $(this).attr('data-name') || '';
        if (type == 1) {
            $.swipebox([
                {
                    href: src,
                    title: name
                }
            ]);
        }

         else if (type == 2) {
            $("body").append(`
                <div class="video-modal">
                    <video controls autoplay style="width:80%;max-height:80vh">
                        <source src="${src}" type="video/mp4">
                    </video>
                    <div class="video-close">X</div>
                </div>
            `);
        }
    };

    $(document).on("click", ".video-close", function(){
        $(".video-modal").remove();
    });

    t.attachmentDownload = function (e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }

    t.refreshTimeLine = function () {
        var url = t.config.url.get_timeline;
        if (t.config.archive == true) {
            url = t.config.url.get_timeline_archived;
        }
        var class_name = '';
        var currentPage = 1;
        var perPage = 5;
        var isTimelineLoading = false;
        var hasMoreTimeline = true;
        var $loadComment = t.conversation.find('.load-comment');

        function loadRecords(page) {
            if (isTimelineLoading || (page > 1 && !hasMoreTimeline)) {
                return;
            }

            isTimelineLoading = true;
            if (page > 1) {
                $loadComment.removeClass('hide');
            }

            var formData = new FormData();
            formData.append('_token', t.config.token);
            formData.append('id', t.data.id);
            formData.append('page', page);
            formData.append('per_page', perPage);
            var http = $.ajax({
                url: url,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
                  });
                http.done(function (data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        let lastTimelineId = data.latest_timeline_id;
                        var exi_el_state = {};
                        t.timeline.find('.timeline-entry').each(function (exi_ind, exi_el) {
                            exi_el_state['e' + exi_el.id] = $(exi_el).hasClass('tiny-view-on');
                        });

                        let cardIndex = t.timeline.find('.timeline-entry').length;
                        if (page === 1) {
                            t.timeline.empty();
                            hasMoreTimeline = true;
                        }
                        $('.ticket_comment').removeClass('hide');
                        if (data.data.length > 0) {
                            t.conversation.removeClass('hide');
                            var tiny_viewer_state = t.toggle_conversation_view.hasClass('tiny-view-on');
                            $.each(data.data, function (i, v) {
                                var bgClass = '';
                                switch (cardIndex % 2) {
                                    case 0:
                                        bgClass = 'timeline-card-pink';
                                        break;
                                    case 1:
                                        bgClass = 'timeline-card-blue';
                                        break;
                                }
                                if (v.updated_by == v.assigned_to && v.assigned_to != null) {
                                    class_name = "color-code-bar color-code-blue-text";
                                } else if (v.updated_by == v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-rose-text";
                                } else if (v.updated_by != v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-yellow-text";
                                }
                                let feedback_comments = v.action_type == 3 ? '(This is a feedback)' : '';

                                var t2 = v.auto_response == 1 ? "Auto Response By" : (v.is_note == 1 ? "Note Added By" : "Commented By");
                                var profileImage = (v.auto_response) ? '<img src="'+ t.config.botIcon +'" class="rounded-circle" alt="Bot" width="35" height="35">' : '<img src="' + v.profile_img + '" alt="Profile" class="rounded-circle" width="35" height="35">';
                                var c = v.is_note == 1 ? "tl-note-background" : "";
                                var s = v.is_service_request == 1 ? "service-note" : "";
                                if (v.action_type == 6) {
                                    c = "tl-merge";
                                    if (t.config.data.is_merge_primary == 1) {
                                        try {
                                            var merged_items = [];
                                            $.each(t.config.data.merged_ids.split(","), function (j, x) {
                                                merged_items.push('<a target="_blank" href="' + t.config.url.current_page + '/' + x + '" class="btn-link text-main text-bold">#' + x + '</a>');
                                            });

                                            t2 = 'Merged with ' + merged_items.join(", ") + ' by ';
                                        } catch (e) {
                                            console.log("e :", e);
                                        }
                                    } else {
                                        t2 = 'Merged with <a target="_blank" href="' + t.config.url.current_page + '/' + v.merge_primary + '" class="btn-link text-main text-bold">#' + v.merge_primary + '</a> by ';
                                    }
                                }
                                // var cc_emails = v.cc_emails != "" && v.cc_emails != null ? " | CC: " + v.cc_emails : "";
                                var cc_emails = "";
                                    if (v.cc_emails && v.cc_emails.trim() !== "") {
                                        var emails = v.cc_emails.split(',');
                                        var visibleEmails = emails.slice(0, 1).join(', ');
                                        var hiddenEmails = emails.slice(1).join('<br>');
                                        if (emails.length > 1) {
                                            cc_emails =
                                                ` | CC: ${visibleEmails}
                                                <span class="cc-more-mails text-primary"
                                                    data-bs-toggle="popover"
                                                    data-bs-trigger="click"
                                                    data-bs-placement="bottom"
                                                    data-bs-html="true"
                                                    data-bs-content="${hiddenEmails}">
                                                    +${emails.length - 1} more
                                                </span>`;
                                        } else {
                                            cc_emails = ` | CC: ${visibleEmails}`;
                                        }
                                    }
                                var form = "";
                                if (typeof v.ticket_status_form_id != "undefined" && v.ticket_status_form_id != null) {
                                    form = '<a href="' + config.url.view_status_form + '/' + v.ticket_status_form_id + '" class="btn-ex-com" target="_blank" data-toggle="tooltip" data-title="View Form" data-placement="right"><i class="bi bi-ui-checks"></i></a>';
                                }
                                var tec_location = "";
                                if (typeof v.latitude !== "undefined" && v.latitude !== null && typeof v.longitude !== "undefined" && v.longitude !== null) {
                                    tec_location = `<span class="btn-ex-com tech-location" target="_blank" data-toggle="tooltip" data-html="true" data-title="<i>Address:</i> ${v.tech_address ?? ''}" data-placement="bottom">&nbsp;<i class="fa fa-map-marker fa-lg"></i></span>`;
                                }
                                var toggleIcon = '';
                                if (v.is_workaround != null && v.is_workaround != '') {
                                    toggleIcon = `<span class="badge text-grey"> Is this workaround valid${data.tkt_data.ticket_creator == t.config.user.id ? `?<i class="bi bi-x-lg invalid_workaround" data-tfid="${v.tfid}" data-toggle="tooltip" data-title="Click 'x' if creator not satisfied with the workaround sla" data-placement="bottom"></i>` : ''}</span>`;
                                } else if (v.is_workaround == 0) {
                                    toggleIcon = `<span class="badge  text-grey">Is Workaround</span>&nbsp;<span class="badge text-grey">Is Invalid</span>`;
                                }
                                var commenterName = v.auto_response ? 'MATI-AI' : v.commenter;
                                var commenterInitials = commenterName ? $.map(commenterName.split(' '), function(namePart) {
                                    return namePart ? namePart.charAt(0) : '';
                                }).join('').substring(0, 2).toUpperCase() : 'NA';
                                var profileHtml = v.profile_img
                                    ? '<img src="' + v.profile_img + '" class="rounded-circle flex-shrink-0" width="24" height="24" alt="' + commenterName + '">'
                                    : '<span class="rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0 text-white av-24 av-teal">' + commenterInitials + '</span>';

                                var t3 = '<div class="d-flex align-items-center gap-2 flex-wrap p-3 pb-1">' +
                                            profileHtml +
                                    '<span class="b4-text pe-2 border-end border-2 ' + class_name + '">' + commenterName + '</span>' +
                                    '<span class="b4-text fw-normal">' + v.updated_at_format + '</span>' +
                                            form + feedback_comments + toggleIcon + cc_emails;

                                var t5 = "";
                                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                    var at = [];
                                    var totalAttachments = v.attachments.length;
                                    $.each(v.attachments, function (i, attachment) {
                                        var fileName = t.getAttachmentDisplayName(attachment);
                                        var ext = t.getFileExtension(fileName, attachment.ext);
                                        if (attachment.is_motion_photo == 1) {
                                            ext = "mp4";
                                        }

                                        var viewUrl = t.config.url.attachment_view + "/" + attachment.id;
                                        var downloadUrl = t.config.url.attachment_download + "/" + attachment.id;
                                        // var eye_link = t.isImageExtension(ext) ? t.getImageViewActionHtml(viewUrl, fileName) : "";

                                        var eye_link = "";
                                            if (t.isImageExtension(ext)) {
                                                eye_link =
                                                    '<span class="tri-view text-secondary small cursor-pointer" ' +
                                                    'data-view_mode="1" ' +
                                                    'data-view="' + viewUrl + '" ' +
                                                    'data-name="' + decodeURIComponent(fileName) + '">' +
                                                    '<i class="bi bi-eye"></i>' +
                                                    '</span>';
                                            }
                                        var fileSize = t.formatAttachmentSize(attachment.size);

                                        at.push(
                                            '<div class="attachment-item">' +
                                            '<div class="attachment-content d-flex gap-2">' +
                                            t.getAttachmentIconHtml(ext) +
                                            '<div class="file-details">' +
                                            '<span class="file-name" title="' + escapeAttr(fileName) + '">' + escapeHtml(fileName) + '</span>'+
                                            '<div class="file-info-actions d-flex gap-2">' +
                                            '<div class="attachment-actions">' +
                                            eye_link +
                                            t.getDownloadActionHtml(downloadUrl) +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>'
                                        );

                                        // at.push('<div class="attach-item"><div>' + eye_link + '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '"><i class="fa fa-download"></i> Download</span></div><span class="attach-name">' + v.name + '</span></div>');
                                    });
                                   
                                    if (at.length > 0) {
                                        t5 =
                                            '<div class="attachment-header" style="margin-top:.8rem; font-size:.75rem;">' +
                                            '<i class="bi bi-paperclip"></i> ' + t.config.translations.attachment + ' (' + totalAttachments + ')</div>' +
                                            '<div class="attachment-container">' +
                                            at.join("") +
                                            "</div>";
                                    }
                                }
                                var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                                var signle_tiny_viewer = tiny_viewer_state == true ? 'fa-expand faa-fast animated' : 'fa-compress';

                                if (typeof exi_el_state['e' + v.tfid] != 'undefined') {
                                    if (exi_el_state['e' + v.tfid] == true) {
                                        tiny_view_class = 'tiny-view-on';
                                        signle_tiny_viewer = 'fa-expand faa-fast animated';
                                    }
                                    else {
                                        tiny_view_class = '';
                                        signle_tiny_viewer = 'fa-compress';
                                    }
                                }
                                var collapsedClass = tiny_view_class == 'tiny-view-on' ? ' is-collapsed' : '';
                                var timelineToggleIcon = tiny_view_class == 'tiny-view-on' ? 'bi-arrows-angle-expand' : 'bi-arrows-angle-contract';
                                var timelineToggleExpanded = tiny_view_class == 'tiny-view-on' ? 'false' : 'true';
                                var t3View = t3 +
                                    '<button type="button" class="tkd-icon-plain single-tiny-viewer ms-auto" aria-label="Collapse conversation" aria-expanded="' + timelineToggleExpanded + '"></button>' +
                                    '</div>';
                                var serviceClass = v.is_service_request == 1 ? s : c;
                                var t4 = '<div class="w-100 ' + serviceClass + '">' +
                                    t3View +
                                    '<div class="tkd-comment-body ms-4 ps-2">' +
                                    '<div class="b4-text tkd-message-body opacity-70 tml-content-container">' + v.remarks + '</div>' +
                                    t5 +
                                    '</div>' +
                                    '</div>';
                                t.timeline.append('<div id="' + v.tfid + '" class="tkd-conversation-card card timeline-entry tiny-view ' + bgClass + tiny_view_class + collapsedClass + ' ' + serviceClass + '">' + t4 + '</div>');
                                cardIndex++;
                            });

                            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (el) {
                                new bootstrap.Popover(el);
                            });

                            $(document).on('click', function (e) {
                                if (!$(e.target).closest('.cc-more-mails, .popover').length) {
                                    $('[data-bs-toggle="popover"]').each(function () {
                                        bootstrap.Popover.getInstance(this)?.hide();
                                    });
                                }
                            });

                            if (data.data.length < perPage) {
                                hasMoreTimeline = false;
                            } else {
                                hasMoreTimeline = true;
                            }
                            $loadComment.addClass('hide');

                            t.timeline.removeClass("hide");
                        } else {
                            if (page === 1) {
                                t.timeline.addClass("hide");
                                t.conversation.addClass('hide');
                            }
                            hasMoreTimeline = false;
                            $loadComment.addClass('hide');
                            if ([5, 6, 10].includes(data.tkt_data.status_id)) {
                                $('.ticket_comment').addClass('hide');
                            }
                        }
                    } else if (data.msg != "") {
                        if (page > 1) {
                            currentPage = Math.max(1, currentPage - 1);
                        }
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.fail(function () {
                if (page > 1) {
                    currentPage = Math.max(1, currentPage - 1);
                }
            });
            http.always(function () {
                isTimelineLoading = false;
                $loadComment.addClass('hide');
            });
        }
        loadRecords(currentPage);

        t.timeline.off('scroll.ticketTimeline').on('scroll.ticketTimeline', function () {
            var el = this;
            var isNearBottom = el.scrollTop + t.timeline.innerHeight() >= el.scrollHeight - 24;
            if (!isNearBottom || isTimelineLoading || !hasMoreTimeline) {
                return;
            }

            currentPage++;
            loadRecords(currentPage);
            t.timeline.css({ 'overflow-y': 'auto', 'scrollbar-width': 'thin' });
        });

        $(document).on('click', '.invalid_workaround', function () {
            let tfid = $(this).data('tfid');
            var valid_workaround = true;
            t.isValidWorkaround(tfid, valid_workaround);
        });
    };

    $(document).on('click', '.cc-more-mails', function () {
        $('.cc-more-mails').not(this).each(function () {
            let pop = bootstrap.Popover.getInstance(this);
            if (pop) {
                pop.hide();
            }
        });
    });

    t.isValidWorkaround = function (id, valid_workaround) {
        var workaround_url = t.config.url.is_valid_workaround;
        var msg = {
            'msg': 'Something went wrong!',
        };
        Swal.fire({
            title: 'Are you sure?',
            text: 'This workaround is invalid?',
            icon: 'warning',
            showCancelButton: true,
            showCloseButton: true,
            confirmButtonColor: '#5bd810',
            cancelButtonColor: '#cc3333',
            confirmButtonText: 'Yes',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: workaround_url,
                    type: 'POST',
                    data: {
                        "_token": t.config.token,
                        "is_valid_workaround": valid_workaround,
                        "id": id,
                    },
                    success: function (response) {
                        var msg = {
                            'msg': 'Workaround marked as invalid!',
                        };
                        sweetAlert('center', 'success', msg);
                        t.refreshTimeLine();
                    },
                    error: function (error) {
                        sweetAlert('center', 'error', msg);
                    }
                });
            }
        });
    };

    t.hasTicketSummaryValue = function (value) {
        if (value === undefined || value === null) {
            return false;
        }

        var normalized = $.trim(String(value));
        if (normalized === '') {
            return false;
        }

        return ['n/a', 'not available', 'null', 'undefined'].indexOf(normalized.toLowerCase()) === -1;
    };

    t.showTicketSummaryBadge = function ($badge) {
        return $badge.removeClass('hide').css('display', '');
    };

    t.hideTicketSummaryBadge = function ($badge, valueSelector) {
        if (valueSelector) {
            $badge.find(valueSelector).text('');
        } else {
            $badge.text('');
        }

        return $badge.addClass('hide').css('display', 'none');
    };

    t.refreshTicketStripInfo = function () {
        var statusName = t.data.status && t.data.status.name ? t.data.status.name : '';
        var priorityName = '';
        var priorityColor = '#FF0A0E';
        var $statusInfo = t.page.find('#statusInfo');
        var $priorityInfo = t.page.find('#priorityInfo');
        var $tatInfo = t.page.find('#tatInfo');

        if (!t.hasTicketSummaryValue(statusName) && t.config.statuses) {
            $.each(t.config.statuses, function (i, k) {
                if (parseInt(k.id) == parseInt(t.data.status_id)) {
                    statusName = k.name || '';
                    return false;
                }
            });
        }

        if (t.config.priorities) {
            $.each(t.config.priorities, function (i, k) {
                if (parseInt(k.id) == parseInt(t.data.priority_id)) {
                    priorityName = k.name || '';
                    return false;
                }
            });
        }

        var priorityKey = String(priorityName).toLowerCase();
        if (priorityKey == 'critical') {
            priorityColor = '#9f1239';
        } else if (priorityKey == 'medium') {
            priorityColor = '#f59e0b';
        } else if (priorityKey == 'low') {
            priorityColor = '#22c55e';
        }

        if (t.hasTicketSummaryValue(statusName)) {
            t.showTicketSummaryBadge($statusInfo).text(statusName);
        } else {
            t.hideTicketSummaryBadge($statusInfo);
        }

        if (t.hasTicketSummaryValue(priorityName)) {
            t.showTicketSummaryBadge($priorityInfo);
            $priorityInfo.find('[data-ticket-summary-value="priority"]').text(priorityName);
            $priorityInfo.find('path').attr('fill', priorityColor).attr('stroke', priorityColor);
        } else {
            t.hideTicketSummaryBadge($priorityInfo, '[data-ticket-summary-value="priority"]');
        }

        if (t.hasTicketSummaryValue(t.data.tat)) {
            t.showTicketSummaryBadge($tatInfo).text('TAT: ' + t.data.tat + ' Hrs');
        } else {
            t.hideTicketSummaryBadge($tatInfo);
        }
    };

    t.refreshExpireInfo = function () {
        // t.refreshTicketStripInfo();
        // if (t.data.status_id == 5 || t.data.status_id == 6 || t.data.tat_halt == 1 || parseInt(t.config.wai) == 24 || t.isSpamTicket()) {
        if (parseInt(t.config.wai) == 24 || t.isSpamTicket()) {
            t.hideTicketSummaryBadge(t.expireInfo, '[data-ticket-summary-value="tat-sla"]');
            if (t.expireInterval) {
                clearInterval(t.expireInterval);
                t.expireInterval = null;
            }
            return;
        }
        if (!t.hasTicketSummaryValue(t.data.expire_info)) {
            t.hideTicketSummaryBadge(t.expireInfo, '[data-ticket-summary-value="tat-sla"]');
            if (t.expireInterval) {
                clearInterval(t.expireInterval);
                t.expireInterval = null;
            }
            return;
        }
        if (String(t.data.expire_info).toLowerCase() == "overdue") {
            t.showTicketSummaryBadge(t.expireInfo).find('[data-ticket-summary-value="tat-sla"]').text('SLA: Overdue');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.showTicketSummaryBadge(t.expireInfo).find('[data-ticket-summary-value="tat-sla"]').html('TAT Expire: <span data-expire="' + t.data.expire_info + '">' + t.data.expire_info + '</span>');
                if (t.expireInterval) {
                    clearInterval(t.expireInterval);
                }
                if (!t.config.halt_statuses.includes(t.data.status_id)) {
                    t.expireInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    t.expireInterval = setInterval(function () {
                        t.expireInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    }, 30000);
                }
            }
            else {
                t.showTicketSummaryBadge(t.expireInfo).find('[data-ticket-summary-value="tat-sla"]').html('SLA: <span data-countdown=" ' + t.data.expire_info + ' "></span>');
                t.expireInfo.find('[data-countdown]').countdown(t.data.expire_info, function (event) {
                    if (event.type == "finish") {
                        t.data.expire_info = "Overdue";
                        t.refreshExpireInfo();
                    } else {
                        $(this).text(event.strftime('%D days %H:%M:%S'));
                    }
                });
            }
        }
    };

    t.refreshworkAroundInfo = function () {
        if (parseInt(t.config.wai) == 24 || t.isSpamTicket()) {
            t.hideTicketSummaryBadge(t.workaroundInfo, '[data-ticket-summary-value="workaround-sla"]');
            if (t.workaroundInterval) {
                clearInterval(t.workaroundInterval);
                t.workaroundInterval = null;
            }
            return;
        }
        if (!t.hasTicketSummaryValue(t.data.work_around_info)) {
            t.hideTicketSummaryBadge(t.workaroundInfo, '[data-ticket-summary-value="workaround-sla"]');
            if (t.workaroundInterval) {
                clearInterval(t.workaroundInterval);
                t.workaroundInterval = null;
            }
            return false;
        }
        if (String(t.data.work_around_info).toLowerCase() == "overdue") {
            t.showTicketSummaryBadge(t.workaroundInfo).find('[data-ticket-summary-value="workaround-sla"]').text('Workaround SLA: Overdue');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.showTicketSummaryBadge(t.workaroundInfo).find('[data-ticket-summary-value="workaround-sla"]').html('Workaround SLA: <span data-expire="' + t.data.work_around_info + '">' + t.data.work_around_info + '</span>');
                if (t.workaroundInterval) {
                    clearInterval(t.workaroundInterval);
                }
                if (!t.config.halt_statuses.includes(t.data.status_id)) {
                    t.workaroundInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    t.workaroundInterval = setInterval(function () {
                        t.workaroundInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    }, 30000);
                }
            } else {
                t.showTicketSummaryBadge(t.workaroundInfo).find('[data-ticket-summary-value="workaround-sla"]').html('Workaround SLA: <span data-countdown=" ' + t.data.work_around_info + ' "></span>');
                t.workaroundInfo.find('[data-countdown]').countdown(t.data.work_around_info, function (event) {
                    if (event.type == "finish") {
                        t.data.work_around_info = "Overdue";
                        t.refreshworkAroundInfo();
                    } else {
                        $(this).text(event.strftime('%D days %H:%M:%S'));
                    }
                });
            }
        }
    };

    t.refreshResponseInfo = function () {
        if (parseInt(t.config.wai) == 24 || t.isSpamTicket()) {
            t.hideTicketSummaryBadge(t.responseInfo, '[data-ticket-summary-value="response-sla"]');
            if (t.responseInterval) {
                clearInterval(t.responseInterval);
                t.responseInterval = null;
            }
            return;
        }
        if (!t.hasTicketSummaryValue(t.data.response_info)) {
            t.hideTicketSummaryBadge(t.responseInfo, '[data-ticket-summary-value="response-sla"]');
            if (t.responseInterval) {
                clearInterval(t.responseInterval);
                t.responseInterval = null;
            }
            return false;
        }
        if (String(t.data.response_info).toLowerCase() == "overdue") {
            t.showTicketSummaryBadge(t.responseInfo).find('[data-ticket-summary-value="response-sla"]').text('Response SLA: Overdue');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.showTicketSummaryBadge(t.responseInfo).find('[data-ticket-summary-value="response-sla"]').html('Response SLA: <span data-expire="' + t.data.response_info + '">' + t.data.response_info + '</span>');
                if (t.responseInterval) {
                    clearInterval(t.responseInterval);
                }
                if (!t.config.halt_statuses.includes(t.data.status_id)) {
                    t.responseInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    t.responseInterval = setInterval(function () {
                        t.responseInfo.find('[data-expire]').work_hour_count_down(t.config.tkt_config);
                    }, 30000);
                }
            } else {
                t.showTicketSummaryBadge(t.responseInfo).find('[data-ticket-summary-value="response-sla"]').html('Response SLA: <span data-countdown=" ' + t.data.response_info + ' "></span>');
                t.responseInfo.find('[data-countdown]').countdown(t.data.response_info, function (event) {
                    if (event.type == "finish") {
                        t.data.response_info = "Overdue";
                        t.refreshResponseInfo();
                    } else {
                        $(this).text(event.strftime('%D days %H:%M:%S'));
                    }
                });
            }
        }
    };

    t.frmUpdateStatusReset = function(){
        t.initUpdateStatusSummernote();
        t.frmUpdateStatus.el.need_time_duration.addClass('hide');
        t.frmUpdateStatus.el.comment.val('').summernote('code', '');
        t.frmUpdateStatus.el.end_time.val("");
        t.frmUpdateStatus.el.start_time.val("");
        t.frmUpdateStatus.el.end_time.rules("remove","required");
        t.frmUpdateStatus.el.start_time.rules("remove","required");
    }

    t.frmUpdateStatusSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        var formAttr = t.frmUpdateStatus.el.statusId.find(':selected').attr('form');
        if (typeof formAttr !== 'undefined' && formAttr != 0 && $("#ticket_status_form_id").val() != 'not_found' && $("#ticket_status_form_id").val() == '') {
            t.loadRequestForm_onStatus();
            t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
        } else {
            var status = parseInt(t.frmUpdateStatus.el.statusId.val());
            if (status == 5 && t.fieldsetFormUpdated) {
                var data = {
                    'msg': 'Please save chages for the form fields.',
                };
                sweetAlert('center', 'error', data);
                return false;
            }

            if (t.config.wai != 23) {
                var data = {
                    'msg': 'This ticket is not assigned to you. Please contact your manager.',
                };
                sweetAlert('center', 'error', data);
                return false;
            }

            if (t.frmUpdateStatusValidator.form() == false) {
                return false;
            }

            var commentCode = t.frmUpdateStatus.el.comment.summernote('code');
            t.frmUpdateStatus.el.comment.val(commentCode);
            if (t.frmUpdateStatus.el.comment.summernote('isEmpty')) {
                $('#frm_update_status').find('#shows_error').html('This field is required.');
                $('#frm_update_status').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                return false;
            } else {
                $('#frm_update_status').find('#shows_error').html('');
            }
            // let cc_checkbox = $('#frm_update_status').find('#follow_cc').is(':checked');
            let cc_email_check =  t.frmUpdateStatus.el.cc_emails.val() ? t.frmUpdateStatus.el.cc_emails.val().toString() : "";
            // if (cc_checkbox == true && cc_email_check == ''){
            //     $('#frm_update_status').find('#shows_error_cc').html('This field is required.');
            //     $('#frm_update_status').find('#shows_error_cc').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
            //     return false;
            // } else {
            //     $('#frm_update_status').find('#shows_error_cc').html('');
            // }

            if (t.httpCall != true) {
                return false;
            }
            t.frmUpdateStatus.btnSubmit.attr("disabled", true);
            t.httpCall = false;

            /* need to clean comment input based on setup */
            if (((t.data.status_id != status && (status == 2 || status == 5)) || (typeof t.config.tkt_config != null && t.config.tkt_config.mail_all_status_changes == 1)) === false) {
                t.frmUpdateStatus.el.comment.val('').summernote('code', '');
            }
            if (status == 5) {
                var customField = $('#customFieldCheck').val();
                // console.log("customField: " + customField);
                if (customField != "" && customField != undefined) {
                    var splCustom = customField.split(",");
                    var required = true;
                    $.each(splCustom, function (i, v) {
                        if ($('#' + v).val() == '') {
                            required = false;
                        }
                    });
                    if (required == false) {
                        var data = {
                            'msg': t.config.translations.checkRequired,
                        };
                        sweetAlert('center', 'error', data);
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        t.httpCall = true;
                        return false;
                    }
                }
            }

            var formData = new FormData(t.frmUpdateStatus.get(0));
            formData.append('cc_emails', cc_email_check);
            var http = $.ajax({
                url: t.config.url.update_status,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function (data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        $("#set_edit_status_form").html("");
                        t.data = data.data;
                        if ((t.data.status_id == t.config.status_spam) || (t.data.status_id == 5)) {
                            setTimeout(function () {
                                window.location.reload();
                            }, 600);
                        }
                        t.refreshExpireInfo();
                        t.refreshworkAroundInfo();
                        t.refreshResponseInfo();
                        t.refreshTimeLine();
                        t.configFun();
                        t.frmCommentTokenize();
                        t.cc_master.setData(data.data);
                        t.updateCCField();
                        $("#repeater-container-ticket").find(".quantity").prop('disabled', t.data.status_id === 5 || t.data.status_id === 6);
                        $("#repeater-container-ticket").find(`.remove-row`).toggleClass("hide", config.data.status_id === 5 || config.data.status_id === 6);
                        $("#item-table").find(`#submit-request-items`).toggleClass("hide", config.data.status_id === 5 || config.data.status_id === 6);

                        $('#frm_update_status').find('#shows_error').html("");
                        $("#ticket_status_form_id").val("");
                        $("#edit_status_form").html("");
                        t.frmUpdateStatus.el.comment.val('').summernote('code', '');
                        t.clearTicketDetailUploader(t.updateStatusUploader, t.attachment_update);
                        t.relatedTask();
                        sweetAlert('center', 'success', data);
                    }else if(data.status == "info"){
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        t.frmUpdateStatusReset();
                        sweetAlert('center', 'info', data);
                        setTimeout(function () {
                            window.location.reload();
                        }, 800);
                    } else {
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        sweetAlert('center', 'error', data);
                        // setTimeout(function () {
                        //     window.location.reload();
                        // }, 800);
                    }
                }
            });
            http.fail(function () {
                t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                var data = {
                    'msg': config.translations.something_went_wrong,
                };
                sweetAlert('center', 'error', data);
            });
            http.always(function () {
                t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                t.httpCall = true;
            });
        }
    };

    // t.userDropdownFormat = function (s) {
    //     if (s && typeof s.loading !== "undefined" && s.loading) {
    //         return $("<div>" + s.text + "</div>");
    //     }

    //     var email = s.email == null ? "" : s.email;
    //     var a = '';
    //     a += "<div class='row'>";
    //     a += "<div class='col-sm-10'>";
    //     var truncatedText = s.text.length > 30 ? s.text.substring(0, 30) + "..." : s.text;
    //     a += "<div class='so-t' title='" + s.text + "'><i class='fa fa-user' style='padding-right: 3px;'></i>" + truncatedText + " ";
    //     a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
    //     a += "</div>";
    //     if (s.email != null && s.email != "") {
    //         var truncatedEmail = s.email.length > 30 ? s.email.substring(0, 30) + "..." : s.email;
    //         a += "<div class='so-t' title='" + s.email + "'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + truncatedEmail + "</div>";
    //     }
    //     if (s.employee_num != null && s.employee_num != "") {
    //         a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
    //     }
    //     a += "</div>";
    //     a += "<div class='col-sm-2'>";
    //     a += "<div><img class='img-u' src='" + s.img_path + "'/></div>";
    //     a += "</div>";
    //     a += "</div>";
    //     return $("<div>" + a + "</div>");
    // };

    t.getUserInitials = function (name) {
        var words = String(this.safeDisplayValue(name, "")).split(/\s+/).filter(Boolean);
        if (!words.length) return "NA";
        if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.safeDisplayValue = function (value, fallback) {
        return this.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getAvatarHtml = function (name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";
        var safeName = this.escapeHtml(this.safeDisplayValue(name, "User"));

        if (this.isFilledValue(imageUrl)) {
            return '<img src="' + this.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + this.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + this.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + this.escapeHtml(this.getUserInitials(name)) + '</span>';
    };

    t.getUserDropdownName = function (s) {
        return this.safeDisplayValue(s.text || s.full_name || s.fullname || s.name, "-");
    };

    t.getUserDropdownImage = function (s) {
        return this.safeDisplayValue(s.img_path || s.profile_img || s.gravatar || s.avatar, "");
    };

    t.userDropdownSelectionFormat = function (s) {
        if (!s || !s.id) return s && s.text ? s.text : "";

        var name = this.getUserDropdownName(s);
        var avatarHtml = this.getAvatarHtml(
            name,
            this.getUserDropdownImage(s),
            "user-list-avatar"
        );

        return $(
            '<div class="d-flex align-items-center gap-2 min-w-0">' +
                avatarHtml +
                '<span class="text-truncate">' + this.escapeHtml(name) + '</span>' +
            '</div>'
        );
    };

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + this.escapeHtml(s.text) + "</div>");
        }
        if (!s) return $("<div>No data</div>");
        var name  = this.getUserDropdownName(s);
        var email = this.safeDisplayValue(s.email, "");
        var empNo = this.safeDisplayValue(s.employee_num || s.employee_no || s.emp_no, "");
        var tName  = name.length  > 35 ? name.substring(0, 35)  + "..." : name;
        var tEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
        var imageUrl = this.getUserDropdownImage(s);
        var avatarHtml = this.getAvatarHtml(
            name,
            imageUrl,
            "user-list-avatar"
        );

        var html = [
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + this.escapeHtml(tName) + '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' + this.escapeHtml(tEmail) + '</span>' : "",
                    empNo ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' + this.escapeHtml(empNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join("");
        return $(html);
    };

    t.frmCommentTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.frmComment.el.tmp_id.val(v);
        t.frmUpdateStatus.el.tmp_id.val(v);
    };

    t.setupTicketDetailUploader = function (cover) {
        if (!window.AMGDragDropUploader || !cover || !cover.length) {
            return null;
        }

        var root = cover[0];
        var uploader = root.AMGDragDropUploader;
        var extraData = function () {
            return {
                ticket_id: t.data.id
            };
        };

        if (uploader) {
            uploader.options.data = extraData;
            return uploader;
        }

        return new window.AMGDragDropUploader(root, {
            data: extraData
        });
    };

    t.clearTicketDetailUploader = function (uploader, fallbackPreview) {
        if (uploader && typeof uploader.clear == "function") {
            uploader.clear();
            return;
        }

        if (fallbackPreview && fallbackPreview.length) {
            fallbackPreview.empty();
        }
    };

    t.frmCommentSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        let btn = $('#frm_comment button[type="submit"]');
        let commentCode = t.frmComment.el.comment.val();
        let isSummernoteReady = t.frmComment.el.comment.next('.note-editor').length > 0;
        if (isSummernoteReady) {
            commentCode = t.frmComment.el.comment.summernote('code');
            t.frmComment.el.comment.val(commentCode);
        }
        if (t.frmCommentValidator.form() == false) {
            return false;
        }

        let s = $('<div>').html(commentCode || '').text().replace(/\u00a0/g, ' ').trim();
        if (s == '') {
            $('#frm_comment').find('#shows_error').html('This field is required.');
            $('#frm_comment').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            $('#frm_comment').find('#shows_error').html('');
        }
        // let cc_checkbox = $('#frm_comment').find('#follow_cc').is(':checked');
        let cc_email_check = t.frmComment.el.cc_emails.val() ? t.frmComment.el.cc_emails.val().toString() : "" ;
        // if (cc_checkbox == true && cc_email_check == ''){
        //     $('#frm_comment').find('#shows_error_cc').html('This field is required.');
        //     $('#frm_comment').find('#shows_error_cc').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
        //     return false;
        // } else {
        //     $('#frm_comment').find('#shows_error_cc').html('');
        // }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        btn.prop('disabled', true);
        $("#comment_loader").show();
        var formData = new FormData(t.frmComment.get(0));
        formData.append('cc_emails', cc_email_check);
        var http = $.ajax({
            url: t.config.url.add_comment,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.data = data.data;
                    console.log(t.data);
                    if(t.data.updated_status == 5){
                        window.location.reload();
                    }
                    t.refreshTimeLine();
                    t.refreshExpireInfo();
                    t.refreshworkAroundInfo();
                    t.refreshResponseInfo();
                    t.frmCommentTokenize();
                    t.cc_master.setData(data.data);
                    t.updateCCField();
                    btn.prop('disabled', false);
                    $("#comment_loader").hide();
                    sweetAlert('center', 'success', data);
                    t.frmComment.el.comment.val("").summernote('code', '');
                    $('#frm_comment').find('#shows_error').html("");
                    t.frmComment.el.is_note.prop("checked", false);
                    t.clearTicketDetailUploader(t.commentUploader, t.attachment);
                    totalCommentFileSize = 0;
                    uploadedCommentFiles = [];
                } else {
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
        http.always(function (data) {
            t.httpCall = true;
            btn.prop('disabled', false);
            $("#comment_loader").hide();
        });
    };

    t.mdlDeleteTicket = $("#mdlDeleteTicket");
    t.frmDeleteTicket = t.mdlDeleteTicket.find("#edit-ticket-mdl-frm");
    t.frmDeleteTicket.el = {};
    t.frmDeleteTicket.el.id = t.frmDeleteTicket.find("#id");
    t.frmDeleteTicket.el.ticket_id = t.frmDeleteTicket.find("#ticket_id");
    t.frmDeleteTicket.el.reason = t.frmDeleteTicket.find("#delete-reason");
    t.frmDeleteTicket.el.error = t.frmDeleteTicket.find("#shows_error_reason");
    t.frmDeleteTicket.el.btnSubmit = t.frmDeleteTicket.find("#btndelsubmit");
    if (typeof $.fn.summernote !== "undefined" && t.frmDeleteTicket.el.reason.length) {

         t.frmDeleteTicket.el.reason.summernote({
            inheritPlaceholder: true,
            placeholder: "write content here",
            container: 'body',
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
                        $('.note-style .dropdown-toggle').html(textResizeIcon);
            },
            },
        });
    }
    t.resetDeleteFrm = function () {
        if (t.frmDeleteTicket.length) {
            t.frmDeleteTicket[0].reset();
        }
        t.frmDeleteTicket.el.id.val('');
        t.frmDeleteTicket.el.ticket_id.val('');
        t.frmDeleteTicket.el.error.html('');
        if (
            typeof $.fn.summernote !== "undefined" &&
            t.frmDeleteTicket.el.reason.next('.note-editor').length
        ) {
            t.frmDeleteTicket.el.reason.summernote('code', '');
        } else {
            t.frmDeleteTicket.el.reason.val('');
    }
        
    };

    t.getDeleteReason = function () {
        if (
            typeof $.fn.summernote !== "undefined" &&
            t.frmDeleteTicket.el.reason.next('.note-editor').length
        ) {
            return t.frmDeleteTicket.el.reason.summernote('code');
        }
        return t.frmDeleteTicket.el.reason.val();
    };
    t.submitDeleteTicket = function (e) {
        e.preventDefault();
        let reason = t.getDeleteReason();
        let text = $('<div>').html(reason).text().trim();
        if (text.length === 0) {
            t.frmDeleteTicket.el.error
                .html('This field is required')
                .css({
                    color: '#c53030',
                    fontSize: '13px',
                    fontFamily: 'Arial, sans-serif'
                });
            return false;
        }
        t.frmDeleteTicket.el.error.html('');
        if (t.httpCall !== true) {
            return false;
        }
        t.httpCall = false;
        t.frmDeleteTicket.el.btnSubmit.prop("disabled", true);
        var http = $.ajax({
            url: t.config.url.delete,
            type: "POST",
            data: {
                _token: t.config.token,
                id:  config.data.id,
                note: reason
            }
        });
        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    sweetAlert('center', 'success', data);
                    t.mdlDeleteTicket.modal('hide');
                    setTimeout(function () {
                        window.location = t.config.url.alltickets;
                    }, 800);

                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });

        http.fail(function () {
            sweetAlert('center', 'error', {
                msg: config.translations.something_went_wrong
            });
        });

        http.always(function () {
            t.httpCall = true;
            t.frmDeleteTicket.el.btnSubmit.prop("disabled", false);

        });
    };
    t.frmDeleteTicket.el.btnSubmit.on(
        "click",
        $.proxy(t.submitDeleteTicket)
    );
    t.deleteTicket = function (e, ticketId) {
        if (e) {
            e.preventDefault();
        }
        t.resetDeleteFrm();
        t.frmDeleteTicket.el.id.val(ticketId);
        t.frmDeleteTicket.el.ticket_id.val(ticketId);
        t.mdlDeleteTicket.modal("show");
    };
    t.canReopenTicket = false;
    t.checkReopenEligibility = function (ticketId) {
        $.ajax({
            url: config.url.checkReopen,
            method: 'POST',
            dataType: 'json',
            data: {
                ticket_ids: [ticketId],
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === "success" && response.results) {
                    var ticketData = response.results[ticketId];
                    t.canReopenTicket = ticketData && ticketData.canReopen ? true : false;
                    t.configFunAfterReopenCheck();                }
            },
            error: function (xhr, status, error) {
                console.error('Error checking reopen eligibility:', error);
            }
        });

    };
    t.configFunAfterReopenCheck = function () {
        t.syncUpdateTicketPanelVisibility();
        t.updateActionOverflow();
    };

    t.resetDeleteFrm = function () {
        if (typeof $.fn.summernote !== "undefined" && t.frmDeleteTicket.el.reason.next('.note-editor').length) {
            t.frmDeleteTicket.el.reason.summernote('code', '');
        } else {
            t.frmDeleteTicket.el.reason.val('');
        }
        t.frmDeleteTicket.el.error.html("");
        t.frmDeleteTicket.el.btnSubmit.prop("disabled", false);
    };

    t.addProblemMgt = function (e) {
        t.mdladdProblem.title.html(config.translations.ticket_problem_mgt);
        t.frmaddProblem.frmEl.id.val(t.data.id);
        t.frmaddProblem.frmEl.token.val(t.config.token);
        var selected = $('#add-problem-mgt').is(":checked");
        if (selected == true) {
            t.mdladdProblem.modal("show");
            t.frmaddProblem.frmEl.problem_mgt.select2($.extend({}, select2Opts, {
                dropdownParent: t.frmaddProblem.frmEl.problem_mgt.parent(),
                ajax: {
                    url: t.config.url.problem_mgt,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.data.company_id
                        };
                    },
                    delay: 200
                },
                allowClear: true,
                placeholder: config.translations.ticket_problem_mgt,
            }));
            t.mdladdProblem.on('hidden.bs.modal', function () {
                $('#add-problem-mgt').prop('checked', false);
            });
        }
        else {
            if (t.httpCall != true) {
                return false;
            }
            sweetAlertConfirmation({
                message: config.translations.are_you_delete_problem_mgt,
                onConfirm: function () {
                    t.httpCall = false;
                    var http = $.ajax({
                        url: t.config.url.problem_mgt_delete,
                        type: "get",
                        data: {
                            "_token": t.config.token,
                            "ticket_id": t.data.id
                        }
                    });
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                sweetAlert('center', 'success', data);
                                // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                                setTimeout(function () {
                                    window.location.reload();
                                }, 800);
                            } else {
                                sweetAlert('center', 'error', data);
                                // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function () {
                        t.httpCall = true;
                    });
                },
                onCancel: function () {
                    $('#add-problem-mgt').prop('checked', true);
                }
            });
        }
    };

    t.addIncident = function (e) {
        t.frmIncidentTicektValidator.resetForm();
        t.frmaddIncident.frmEl.incident.empty().trigger('change');
        t.mdladdIncident.title.html(t.config.translations.select_incident);
        t.frmaddIncident.frmEl.id.val(t.data.id);
        t.frmaddIncident.frmEl.token.val(t.config.token);
        t.frmaddIncident.find(".availabilityError").html("");
        var selected = $('#add-incident').is(":checked");
        if (selected == true) {
            t.mdladdIncident.modal("show");
            t.frmaddIncident.frmEl.incident.select2($.extend({}, select2Opts, {
                dropdownParent: t.frmaddIncident.frmEl.incident.parent(),
                ajax: {
                    url: t.config.url.incident,
                    dataType: "json",
                    success: function (result) {
                        if ((Array.isArray(result.results) && result.results.length === 0)) {
                            t.frmaddIncident.find(".availabilityError").html("This departement, problem category,subcategoy base incident not available.")
                        }
                    },
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            ticket_id: t.frmaddIncident.frmEl.id.val(),
                            company_id: t.config.data.company_id
                        };
                    },
                    delay: 200
                },
                allowClear: true,
                placeholder: { subject: "Select Incident Subject" },
                templateResult: function (data) {
                    if (!data.id) {
                        return data.subject;
                    }
                    return $("<span> #" + data.id + " - " + data.subject + "</span>");
                },
                templateSelection: function (data) {
                    if (!data.id) {
                        return data.subject;
                    }
                    return "#" + data.id + " - " + data.subject;
                }
            }));
            t.mdladdIncident.on('hidden.bs.modal', function () {
                $('#add-incident').prop('checked', false);
            });
        } else {
            if (t.httpCall != true) {
                return false;
            }
            sweetAlertConfirmation({
                message: config.translations.are_you_delete_incident,
                onConfirm: function () {
                    t.httpCall = false;
                    var http = $.ajax({
                        url: t.config.url.incident_delete,
                        type: "get",
                        data: {
                            "_token": t.config.token,
                            "ticket_id": t.data.id
                        }
                    });
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                sweetAlert('center', 'success', data);
                                setTimeout(function () {
                                    window.location.reload();
                                }, 800);
                            } else {
                                sweetAlert('center', 'error', data);
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function () {
                        t.httpCall = true;
                    });
                },
                onCancel: function () {
                    $('#add-incident').prop('checked', true);
                }
            });
        }
    };

    t.submitIncident = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmIncidentTicektValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.mdladdIncident.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmaddIncident.get(0));
        var http = $.ajax({
            url: t.config.url.submit_incident,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdladdIncident.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdladdIncident.modal("hide");
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.mdladdIncident.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.mdladdIncident.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.mdladdIncident.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    }

    t.openMdlAssignTo = function (e) {
        e.preventDefault();
        t.mdlAssignTo.title.html(config.translations.Assign_Ticket_To);
        t.frmAssignTo.el.id.val(t.data.id);
        t.frmAssignTo.el.token.val(t.config.token);
        t.mdlAssignTo.modal("show");
        t.frmAssignTo.el.assigned_to.empty().append(new Option(config.translations.Select_User, "")).trigger("changed");
        // $.get(t.config.url.get_users_to_assign + "/" + t.data.id, function(data) {
        //     if (typeof data != "undefined" && typeof data.status != "undefined" && data.status == "success" && typeof data.data != "undefined" && data.data.length > 0) {
        //         $.each(data.data, function(i, v) {
        //             t.frmAssignTo.el.assigned_to.append(new Option(v.name, v.id));
        //         });
        //         t.frmAssignTo.el.assigned_to.trigger("change");
        //         t.mdlAssignTo.modal("show");
        //     } else {
        //         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>No User available to assign</p></div>' });
        //     }
        // });
        t.frmAssignTo.el.assigned_to.on('change', t.checkTechnicalAvabilityForAssignTo);
    };

    t.checkTechnicalAvabilityForAssignTo = function () {
        var attendarId = t.frmAssignTo.el.assigned_to.val();
        t.frmAssignTo.find(".availabilityError, .availabilitySuccess").html("");
        if (attendarId == '') {
            return false;
        }
        $.ajax({
            url: t.config.url.getTechCurrentStatusById + '/' + attendarId,
            method: 'GET',
            success: function (result) {
                // console.log(result);
                if (result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmAssignTo.find(".availabilityError").html("The technician is unavailable but still you can assign ticket. Technician will revert only once he/she will available.")
                } else {
                    t.frmAssignTo.find(".availabilitySuccess").html("The technician is available.")
                }
            }
        })
    }

    t.checkTechnicalAvabilityForTransfer = function () {
        var attendarId = t.frmTransfer.el.assign_to.val();
        t.frmTransfer.find(".availabilityError, .availabilitySuccess").html("");
        if (attendarId == '') {
            return false;
        }
        $.ajax({
            url: t.config.url.getTechCurrentStatusById + '/' + attendarId,
            method: 'GET',
            success: function (result) {
                if (result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmTransfer.find(".availabilityError").html("The technician is unavailable but still you can assign ticket. Technician will revert only once he/she will available.")
                } else {
                    t.frmTransfer.find(".availabilitySuccess").html("The technician is available.")
                }
            }
        })
    }
    t.buildTimelineItem = function(toneClass, ribbonText, ribbonIcon, actionHtml, avatarHtml, commenterName, updatedAt, updatedBy) {
        var userLink = (updatedBy && updatedBy != 0) ? `${t.config.url.user_info}/${updatedBy}`: null;
        return `
            <div class="t-item">

                <div class="t-actor">
                    ${avatarHtml}
                    ${
                        userLink
                        ? `<a href="${userLink}" target="_blank">${escapeHtml(commenterName)}</a>`
                        : `<a>${escapeHtml(commenterName)}</a>`
                    }
                </div>

                <div class="t-rail-col">
                    <span class="rail-cap start"></span>
                    <span class="rail-cap end"></span>
                </div>

                <div class="t-card ${toneClass}">
                    <div class="t-body">

                        <div class="t-time b5-text">
                            <i class="bi bi-clock"></i>
                            ${escapeHtml(updatedAt)}
                        </div>

                        <div class="t-text">
                            <p class="b4-text fw-normal">${actionHtml}</p>
                        </div>

                    </div>

                    <div class="t-ribbon b7-text fw-normal">
                        <i class="${ribbonIcon}"></i>
                        ${ribbonText}
                    </div>

                </div>

            </div>
        `;
    }
    t.openMdlTktHistory = function(e) {
        var url = t.config.url.get_tickets;
        var history_url = t.config.url.ticket_history;
        if (t.config.archive == true) {            
            url = t.config.url.get_archived_tickets;
            history_url = t.config.url.ticket_history_archived;
        }
        e.preventDefault();

        // Pagination variables
        var currentPage = 1;
        var perPage = 10;
        var isHistoryLoading = false;
        var hasMoreHistory = true;

        var container = $('#ticket_history_container');
        var loader = $('.ticket-history-loader');

        // Clear container and reset
        container.empty();
        loader.removeClass('d-none');
        container.scrollTop(0);

        // First AJAX call for ticket details
        var httpDetails = $.ajax({
            url: url,
            type: "POST",
            data: {
                id: t.data.id,
                _token: t.config.token
            },
            success: function(data) {
                var sub_cat = data.data.sub_cat == null ? "" : data.data.sub_cat;
                var asset_tag = data.data.asset_tag == null ? "" : data.data.asset_tag;
                
                $('#ticket_details_subject').text(data.data.subject);
                $('#ticket_details_subject').attr('data-original-title', data.data.subject);
                $('#ticket_details_id').text('#' + data.data.id);
                $('#ticket_details_company').text(data.data.comp_name);
                
                if(asset_tag != '' && asset_tag != null && asset_tag != undefined){
                    $('.related-device-row').show();
                    $('#ticket_details_device').text(asset_tag);
                } else {
                    $('.related-device-row').hide();
                }
                
                $('#ticket_details_category').text(data.data.prob_cat);
                
                if (sub_cat != '' && sub_cat != null && sub_cat != undefined) {
                    $('.subcategory-row').show();
                    $('#ticket_details_subcategory').text(sub_cat);
                } else {
                    $('.subcategory-row').hide();
                }
            },
            error: function(xhr, status, error) {
                if(xhr.status === 419) {
                    sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
                }
            }
        });
        // Function to load history page
        function loadHistoryPage(page) {
            if (isHistoryLoading) {
                return;
            }
            
            isHistoryLoading = true;
            loader.removeClass('d-none');
            
            var http2 = $.ajax({
                url: history_url,
                type: "POST",
                data: {
                    "_token": t.config.token,
                    "id": t.data.id,
                    "page": page,
                    "per_page": perPage
                }
            });
            
            http2.done(function(response) {
                if (typeof response == "object") {
                    if (response.status == "success") {
                        var ticket_history = '';
                        
                        if (response.data.length > 0) {
                            // Update pagination info
                            if (response.pagination) {
                                currentPage = response.pagination.current_page || page;
                                hasMoreHistory = response.pagination.more || false;
                                perPage = response.pagination.per_page || perPage;
                            } else {
                                // Fallback if pagination not returned
                                hasMoreHistory = response.data.length >= perPage;
                                currentPage = page;
                            }
                            
                            $.each(response.data, function(index, value) {
                                var actionHtml = '';
                                // Set commenter name (handle System user)
                                var commenterName = value.commenter == "System" ? "System" : value.commenter;
                                // Get avatar HTML
                                var avatarHtml = t.getAvatar(commenterName, value.commenter_avatar);
                                switch(parseInt(value.action_type)) {
        
                                case 1: // Assigned Ticket
                                    var toneClass = "tone-assigned";
                                    var ribbonText = "Assigned";
                                    var ribbonIcon = "bi bi-person-check-fill";
                                    if (value.assingned_user_fullname != null && value.assingned_user_fullname != '') {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold"><a href="'+ t.config.url.user_info + '/' + value.assigned_to +'" target="_blank">' + value.assingned_user_fullname +'</a></span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.assigned_to +'" target="_blank">' + value.assingned_user_fullname +'</a></span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_assigned_to +'">' + value.old_assigned_user_fullname +'</a></span>';
                                        }
                                    } else if (value.assigned_to == 0 || value.assigned_to == null) {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_assigned_to +'">' + value.old_assigned_user_fullname +'</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Assigned Ticket Updated';
                                    }
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;  
                                case 2: // Status Change
                                    if (value.old_status_name != null && value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold">' + value.old_status_name +'</span> &gt; <span class="fw-bold">' + value.name + '</span>';
                                    } else if (value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold">' + value.name + '</span>';
                                    } else {
                                        actionHtml = 'Status Updated';
                                    }
                                    var status = (value.name || '').toLowerCase().trim();
                                    switch (status) {
                                        case 'open':
                                            toneClass = 'tone-open';
                                            ribbonText = 'Open';
                                            ribbonIcon = 'bi bi-arrow-repeat';
                                            break;

                                        case 'in progress':
                                        case 'inprogress':
                                            toneClass = 'tone-open';
                                            ribbonText = 'In Progress';
                                            ribbonIcon = 'bi bi-arrow-repeat';
                                            break;

                                        case 'waiting for user':
                                            toneClass = 'tone-waiting';
                                            ribbonText = 'Waiting for User';
                                            ribbonIcon = 'bi bi-person-fill';
                                            break;

                                        case 'hold':
                                        case 'on hold':
                                            toneClass = 'tone-hold';
                                            ribbonText = 'On Hold';
                                            ribbonIcon = 'bi bi-pause-circle';
                                            break;

                                        case 'resolved':
                                            toneClass = 'tone-resolved';
                                            ribbonText = 'Resolved';
                                            ribbonIcon = 'bi bi-check2-square';
                                            break;

                                        case 'closed':
                                            toneClass = 'tone-closed';
                                            ribbonText = 'Closed';
                                            ribbonIcon = 'bi bi-lock-fill';
                                            break;

                                        case 'reopened':
                                        case 're-opened':
                                            toneClass = 'tone-reopen';
                                            ribbonText = 'Reopen';
                                            ribbonIcon = 'bi bi-check-lg';
                                            break;

                                        default:
                                            toneClass = 'tone-open';
                                            ribbonText = value.name || 'Status';
                                            ribbonIcon = 'bi bi-chat-left-text';
                                            break;
                                        }
                                        ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                        break;
                                case 3: // Feedback
                                    if (value.old_feedback != null && value.feedback != null) {
                                        actionHtml = 'Feedback Changed From <span class="fw-bold">' + value.old_feedback +'</span> To <span class="fw-bold">' + value.feedback + '</span>';
                                    } else if (value.feedback != null) {
                                        actionHtml = 'Feedback : <span class="fw-bold">' + value.feedback + '</span>';
                                    } else {
                                        actionHtml = 'Feedback Updated';
                                    }
                                    toneClass = 'tone-feedback';
                                    ribbonText = 'Feedback';
                                    ribbonIcon = 'bi bi-chat-left-text';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 4: // Added to Spam List
                                    actionHtml = 'Ticket Marked As <span class="fw-bold">Spam</span>';
                                    toneClass = 'tone-spam';
                                    ribbonText = 'Spam';
                                    ribbonIcon = 'bi bi-slash-circle-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 5: // Removed from Spam List
                                    actionHtml = 'Ticket Removed From <span class="fw-bold">Spam List</span>';
                                    toneClass = 'tone-success';
                                    ribbonText = 'Spam Removed';
                                    ribbonIcon = 'bi bi-shield-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 6: // Ticket Merge
                                    if (value.merge_primary != null) {
                                        actionHtml = 'Ticket Merged With <span class="fw-bold">' + value.merge_primary + '</span> ticket';
                                    } else if (value.merged_ids != null) {
                                        actionHtml = 'Primary Ticket Merged With <span class="fw-bold">' + value.merged_ids + '</span> tickets';
                                    } else {
                                        actionHtml = 'Ticket Merge Operation Performed';
                                    }

                                    toneClass = 'tone-merge';
                                    ribbonText = 'Merged';
                                    ribbonIcon = 'bi bi-diagram-3-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 7: // Add Comment
                                    if (value.is_note == 1) {
                                        actionHtml = 'Marked this as <span class="fw-bold">Note</span> and commented on ticket';
                                        toneClass = 'tone-note';
                                        ribbonText = 'Note';
                                        ribbonIcon = 'bi bi-stickies';
                                    } else if (value.auto_response == 1) {
                                        actionHtml = 'Auto responded by <span class="fw-bold">MATI-AI</span>';
                                        toneClass = 'tone-ai';
                                        ribbonText = 'AI Reply';
                                        ribbonIcon = 'bi bi-robot';
                                    } else {
                                        actionHtml = 'Commented on ticket';
                                        toneClass = 'tone-comment';
                                        ribbonText = 'Comment';
                                        ribbonIcon = 'bi bi-chat-left-text';
                                    }
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 8: // Changes Priority
                                    if (value.old_priority_name != null && value.priority_name != null) {
                                        actionHtml = 'Priority Changed From <span class="fw-bold">' + value.old_priority_name +'</span> To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else if (value.priority_name != null) {
                                        actionHtml = 'Priority Changed To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else {
                                        actionHtml = 'Priority Updated';
                                    }
                                    toneClass = 'tone-priority';
                                    ribbonText = 'Priority';
                                    ribbonIcon = 'bi bi-flag';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 9: // Changes TAT
                                    if (value.tat_changed != null && value.tat != null) {
                                        actionHtml = 'Changed TAT From <span class="fw-bold">' + value.tat_changed +' hours</span> To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else if (value.tat != null) {
                                        actionHtml = 'Changed TAT To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else {
                                        actionHtml = 'TAT Updated';
                                    }
                                    toneClass = 'tone-tat';
                                    ribbonText = 'TAT';
                                    ribbonIcon = 'bi bi-clock';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 10: // Ticket Transfer Department
                                    if (value.old_dept_name != null && value.dept_name != null) {
                                        actionHtml = 'Department Changed From <span class="fw-bold">' + value.old_dept_name +'</span> To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else if (value.dept_name != null) {
                                        actionHtml = 'Department Changed To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else {
                                        actionHtml = 'Department Transferred';
                                    }
                                    toneClass = 'tone-department';
                                    ribbonText = 'Department';
                                    ribbonIcon = 'bi bi-building';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 11: // Ticket Transfer Category
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed From <span class="fw-bold">' + value.old_pbm_cat_name +'</span> To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else if (value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Category Transferred';
                                    }
                                    toneClass = 'tone-category';
                                    ribbonText = 'Category';
                                    ribbonIcon = 'bi bi-folder2-open';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 12: // Ticket Transfer Sub Category
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed From <span class="fw-bold">' + value.old_sub_cat_name +'</span> To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else if (value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Sub Category Transferred';
                                    }
                                    toneClass = 'tone-subcategory';
                                    ribbonText = 'Sub Category';
                                    ribbonIcon = 'bi bi-diagram-2-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 14: // New Ticket History
                                    if (value.old_change_creator_user_fullname != null && value.updated_by != value.old_change_creator_id) {
                                        actionHtml = 'New ticket created for <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.old_change_creator_id +'" target="_blank">' + value.old_change_creator_user_fullname +'</a></span>';
                                    } else {
                                        actionHtml = 'New ticket created';
                                    }
                                    toneClass = 'tone-newticket';
                                    ribbonText = 'New Ticket';
                                    ribbonIcon = 'bi bi-ticket-perforated-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 15: // Ticket Type Update
                                    actionHtml = value.ticket_type_custom_fields || 'Ticket Type Updated';
                                    toneClass = 'tone-tickettype';
                                    ribbonText = 'Ticket Type';
                                    ribbonIcon = 'bi bi-list-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 16: // Ticket Creator Change
                                    if (value.creator_user_fullname != null) {
                                        if (value.old_change_creator_user_fullname != null) {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.change_creator_id +'" target="_blank">' +value.creator_user_fullname +'</a></span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_change_creator_id +'">' +value.old_change_creator_user_fullname +'</a></span>';
                                        } else {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.change_creator_id +'" target="_blank">' +value.creator_user_fullname +'</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Ticket Creator Changed';
                                    }
                                    toneClass = 'tone-creator';
                                    ribbonText = 'Creator';
                                    ribbonIcon = 'bi bi-person-fill-gear';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 17: // Custom Field Update
                                    actionHtml = value.custom_fields || 'Custom Field Updated';
                                    toneClass = 'tone-custom';
                                    ribbonText = 'Custom Field';
                                    ribbonIcon = 'bi bi-sliders';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 18: // Calendar Event
                                    actionHtml = `<span class="meetingDetails" data-id="${value.event_id}">Event calendar booked <i class="bi bi-calendar"></i></span>`;
                                    toneClass = 'tone-calendar';
                                    ribbonText = 'Calendar';
                                    ribbonIcon = 'bi bi-calendar2-day';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 19: // Device Change
                                    if (value.device_tag != null) {
                                        if (value.old_device_tag != null) {
                                            actionHtml = `Device Changed To <span class="fw-bold"><a href="${t.config.url.device_info}/${value.device_id}" target="_blank">${value.device_tag}</a></span>From<span class="fw-bold"><a href="${t.config.url.device_info}/${value.old_device_id}">${value.old_device_tag}</a></span>`;
                                        } else {
                                            actionHtml = `Device Changed To <span class="fw-bold"><a href="${t.config.url.device_info}/${value.device_id}" target="_blank">${value.device_tag}</a></span>`;
                                        }
                                    } else {
                                        actionHtml = 'Device Updated';
                                    }
                                    toneClass = 'tone-device';
                                    ribbonText = 'Device';
                                    ribbonIcon = 'bi bi-laptop';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 20: // Custom Form History
                                    if (value.new_custom_field_values != null) {
                                        if (value.old_custom_field_values == null) {
                                            actionHtml ='Form Data Changed<br>' + generateChangeHistoryTable(value.new_custom_field_values);
                                        } else {
                                            actionHtml ='Form Data Changed<br>' +generateChangeHistoryTable(value.new_custom_field_values) +'<br><span class="fw-bold">From</span><br>' +generateChangeHistoryTable(value.old_custom_field_values);
                                        }
                                    } else {
                                        actionHtml = 'Form Data Updated';
                                    }
                                    toneClass = 'tone-custom';
                                    ribbonText = 'Form';
                                    ribbonIcon = 'bi bi-file-earmark-text';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 21: // Custom Remove History
                                    if (value.new_custom_field_values != null) {
                                        if (value.old_custom_field_values == null) {
                                            actionHtml ='Item Removed<br>' + generateChangeHistoryTable(value.new_custom_field_values);
                                        } else {
                                            actionHtml ='Item Removed<br>' + generateChangeHistoryTable(value.new_custom_field_values) +'<br><span class="fw-bold">From</span><br>' + generateChangeHistoryTable(value.old_custom_field_values);
                                        }
                                    } else {
                                        actionHtml = 'Item Removed';
                                    }
                                    toneClass = 'tone-danger';
                                    ribbonText = 'Removed';
                                    ribbonIcon = 'bi bi-trash';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 22: // Task Operation
                                    if (value.remarks != null) {
                                        const parts = value.remarks.split(' ');
                                        const taskId = parts.shift().replace('#', '');
                                        const message = parts.join(' ');
                                        actionHtml = `<span class="fw-bold">#${taskId}</span> ${message}`;
                                    } else {
                                        actionHtml = 'Task Updated';
                                    }
                                    toneClass = 'tone-task';
                                    ribbonText = 'Task';
                                    ribbonIcon = 'bi bi-list-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 23: // Edit Category
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml ='Category Changed From <span class="fw-bold">' +value.old_pbm_cat_name +'</span> To <span class="fw-bold">' +value.pbm_cat_name +'</span>';
                                    } else {
                                        actionHtml ='Category Changed To <span class="fw-bold">' +value.pbm_cat_name +'</span>';
                                    }
                                    toneClass = 'tone-category';
                                    ribbonText = 'Category';
                                    ribbonIcon = 'bi bi-folder2-open';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 24: // Edit Sub Category
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml ='Sub Category Changed From <span class="fw-bold">' +value.old_sub_cat_name +'</span> To <span class="fw-bold">' +value.sub_cat_name +'</span>';
                                    } else {
                                        actionHtml ='Sub Category Changed To <span class="fw-bold">' +value.sub_cat_name +'</span>';
                                    }

                                    toneClass = 'tone-category';
                                    ribbonText = 'Sub Category';
                                    ribbonIcon = 'bi bi-diagram-3';
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                default:
                                        break;
                                }
                            });
                        } else {
                            if (page === 1) {
                                ticket_history = '<div class="text-center p-4"><p>' + (config.translations.Ticket_History_Not_Available || 'No history available') + '</p></div>';
                            } else {
                                hasMoreHistory = false;
                                ticket_history = '';
                            }
                        }
                        
                        if (page === 1) {
                            container.html(ticket_history);
                        } else {
                            container.append(ticket_history);
                        }
                        
                        loader.addClass('hide');
                        $('#ticketHistoryModal').modal("show");
                    }
                }
            });
            
            http2.fail(function(xhr, status, error) {
                if(xhr.status === 419) {
                    sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
                } else {
                    sweetAlert('center', 'error', {msg: config.translations.something_went_wrong || 'Something went wrong'});
                }
                loader.addClass('hide');
            });
            
            http2.always(function() {
                isHistoryLoading = false;
                t.httpCall = true;
            });
        }

        // Load first page
      loadHistoryPage(1);

        // Scroll event for infinite scroll / load more
        container.off('scroll.ticketHistory').on('scroll.ticketHistory', function () {
            if (isHistoryLoading || !hasMoreHistory) {
                return;
            }

            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) {
                loadHistoryPage(currentPage + 1);
            }
        });
    };
    // Helper function to get avatar
    t.getAvatar = function(commenter, avatar) {
        // Check if it's System user
        if (commenter === "System" || commenter === "system") {
            return '<img src="' + t.config.url.base_url + '/assets/mati.png" alt="System">';
        }
        
        // For other users
        if (avatar && avatar !== '' && avatar !== null) {
            // If avatar exists in database
            return '<img src="' + t.config.url.base_url + '/storage/avatar/' + avatar + '" alt="' + escapeHtml(commenter) + '">';
        } else {
            // Otherwise show initials
            var name = commenter || 'User';
            var initials = name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 1);
            return '<span class="tkt-hst-avatar-initials" style="display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: 600; text-transform: uppercase;">' + initials + '</span>';
        }
    }
 



    // Simple escape to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }


    t.openMdlChangeCreator = function (e) {
        e.preventDefault();
        // console.log(t.data);
        t.mdlChangeCreator.title.html("Change Ticket Creator");
        t.frmChangeCreator.el.id.val(t.data.id);
        t.frmChangeCreator.el.token.val(t.config.token);
        t.frmChangeCreator.el.creator_id.empty().append(new Option(config.translations.Select_User, "")).trigger("changed");
        t.mdlChangeCreator.modal("show");
    };

    t.selfAssign = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.httpCall != true) {
            return false;
        }

        sweetAlertConfirmation({
            message: config.translations.are_you_pick,
            onConfirm: function () {
                t.httpCall = false;
                $.get(t.config.url.get_self_assign_mode + '/' + t.config.data.id).done(function (data) {
                    try {
                        if (data.status == "success") {
                            var self_assign_mode = parseInt(data.assign_mode);
                            if (self_assign_mode == 2) {
                                t.httpCall = true;
                                var event = new Event('click');
                                t.loadTransfer(event, 1);
                                return;
                            }
                            else if (self_assign_mode == 1) {
                                var formData = new FormData();
                                formData.append("id", config.data.id);
                                formData.append("_token", t.config.token || $('meta[name="csrf-token"]').attr('content'));
                                var http = $.ajax({
                                    url: t.config.url.self_assign,
                                    type: "POST",
                                    processData: false,
                                    contentType: false,
                                    data: formData
                                });
                                http.done(function (data) {
                                    if (typeof data == "object") {
                                        if (data.status == "success") {
                                            t.page.find(".js-act-self-assign").addClass("hidden");
                                            sweetAlert('center', 'success', data);
                                            setTimeout(function () {
                                                window.location.reload();
                                            }, 600);
                                        } else {
                                            sweetAlert('center', 'error', data);
                                        }
                                    }
                                });
                                http.fail(function () {
                                    var data = {
                                        'msg': config.translations.something_went_wrong,
                                    };
                                    sweetAlert('center', 'error', data);
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 600);
                                });
                                http.always(function () {
                                    t.httpCall = true;
                                });
                            } else {
                                t.httpCall = true;
                                sweetAlert('center', 'error', data);
                                return;
                            }
                        }
                        else {
                            sweetAlert('center', 'error', data);
                        }
                    } catch (e) {
                        alert('Unable to initiate self-assign process');
                        return;
                    }
                }).fail(function (data) {
                    t.httpCall = true;
                    var data = {
                        'msg': config.translations.something_went_wrong,
                    };
                    sweetAlert('center', 'error', data);
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                });
            }
        });

    };

    /* handle the submission of reopen ticket form */
    t.frmReopenSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmReopenValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.mdlReopen.btnSubmit.attr("disabled", true);
        var formData = new FormData(t.frmReopen.get(0));
        formData.append('_token', t.token.attr('content'));
        formData.append('id', t.config.data.id);

        var http = $.ajax({
            url: t.config.url.reopen,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdlReopen.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlReopen.modal("hide");
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.mdlReopen.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.mdlReopen.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.mdlReopen.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    /* submit change creator */
    t.frmChangeCreatorSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmChangeCreatorValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.frmChangeCreator.get(0));
        var http = $.ajax({
            url: t.config.url.change_creator,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdlChangeCreator.modal("hide");
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
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
            t.httpCall = true;
        });
    };

    t.addImpactTicket = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmProblemImpactTicektValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.mdladdProblem.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmaddProblem.get(0));
        var http = $.ajax({
            url: t.config.url.submit_problem_mgt,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdladdProblem.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdladdProblem.modal("hide");
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.mdladdProblem.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.mdladdProblem.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.mdladdProblem.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    t.assignTo = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmAssignToValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.mdlAssignTo.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmAssignTo.get(0));
        var http = $.ajax({
            url: t.config.url.assign_to,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdlAssignTo.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlAssignTo.modal("hide");
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.mdlAssignTo.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.mdlAssignTo.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.mdlAssignTo.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    /* listen the status change by user */
    t.statusIdChanged = function (e) {
        // var status = parseInt($(this).val());
        // try {
        //     if ((t.data.status_id != status && (status == 2 || status == 5)) || (typeof t.config.tkt_config != null && t.config.tkt_config.mail_all_status_changes == 1)) {
        //         t.frmUpdateStatus.el.comment.rules("add", { required: true });
        //         t.frmUpdateStatus.commentWrapper.removeClass("hide");
        //     } else {
        //         t.frmUpdateStatus.el.comment.rules("remove");
        //         t.frmUpdateStatus.commentWrapper.addClass("hide");
        //     }
        // } catch (err) {
        //     console.log(err);
        // }
        if (t.data.status && t.data.status.id) {
            if (t.data.status_id != 5 && t.data.status_id != t.config.statusRejected && t.data.status.id != 2) {
                $("#frm_update_status #status_id option[value='2']").remove();
            }
        }
    };

    // check problem category for ticket status form
    t.getStatusFormIdViaTicketProblemCategory = function () {
        var ticket_id = config.data.id;
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: "GET",
                url: t.config.url.getFormByTicketProblemCategory,
                data: {
                    'ticket_id': ticket_id,
                    'status_id': t.frmUpdateStatus.el.statusId.val(),
                },
                success: function (response) {
                    resolve(response.form_id);
                    $("#ticket_status_form_id").val('');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                    $("#ticket_status_form_id").val('');
                }
            });
        });
    }

    t.loadRequestForm_onStatus = function () {
        if (t.frmUpdateStatus.el.statusId.find(':selected').attr('form') != undefined && t.frmUpdateStatus.el.statusId.find(':selected').attr('form') != 0) {
            var status_form = t.frmUpdateStatus.el.statusId.find('option:selected').attr('form');
            var status_id = t.frmUpdateStatus.el.statusId.find('option:selected').attr('value');
            if (status_form != 0) {
                t.getStatusFormIdViaTicketProblemCategory().then(function (form_id) {
                    if (form_id > 0 && form_id != null) {
                        $.ajax({
                            url: t.config.url.service_request_form + "/" + form_id,
                            method: 'GET',
                            success: function (result) {
                                if (typeof result === "object") {
                                    if (result.status !== "success") {
                                        sweetAlert('center', 'error', result);
                                        return;
                                    }
                                    t.frmStatusServiceRequest.el.form_id.val(form_id);
                                    t.mdlStatusServiceRequest.btnSubmit.text("Save");
                                    var form_name = result.data.form_name ? result.data.form_name : "Service Request Form";
                                    t.mdlStatusServiceRequest.title.html(form_name);
                                    t.frmStatusServiceRequest.el.status_id.val(status_id);
                                    t.frmStatusServiceRequest.el.ticket_id.val(config.data.id);
                                    t.loadStatusRequestedForm(result.data.fields);
                                    t.frmStatusServiceRequest.el.field_values.val(result.data.fields);
                                    t.mdlStatusServiceRequest.modal("show");
                                } else {
                                    var data = {
                                        'msg': 'Unable to load form.',
                                    };
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                var data = {
                                    'msg': 'Error occurred: ' + textStatus + ' - ' + errorThrown,
                                };
                                sweetAlert('center', 'error', data);
                            }
                        });
                    } else {
                        $("#ticket_status_form_id").val('not_found');
                    }
                }).catch(function (error) {
                    console.error('Error fetching form ID:', error);
                });
            }
        } else {
            $("#ticket_status_form_id").val('');
        }
    }

    t.loadCheckStatusApproval = function(){
        let status_id = $(this).val();
        let ticket_id = t.config.data.id;
        $.ajax({
            type: "get",
            url: t.config.url.getStatusApproval,
            data: {
                'status_id':status_id,
                'ticket_id':ticket_id,
            },
            beforeSend: function () {
                t.frmUpdateStatus.btnSubmit.attr("disabled", true);
                t.frmUpdateStatusReset();
            },
            success: function (res) {
                if(res.approval_required && res.need_time_duration){
                    t.frmUpdateStatus.el.need_time_duration.removeClass('hide');
                    t.frmUpdateStatus.el.end_time.rules("add",{ required:true });
                    t.frmUpdateStatus.el.start_time.rules("add",{ required:true });
                }
            },
            complete: function () {
                t.frmUpdateStatus.btnSubmit.attr("disabled", false);
            }
        });
    }

    t.loadStatusRequestedForm = function (fields) {
        var test = fields;
        var fbTemplate = document.getElementById('status-build-wrap'),
            // $fbEditor = $(document.getElementById('fb-editor')),
            $formContainer = $(document.getElementById('status_field_values')),
            // $editContainers = $(document.getElementById('fb-rendered-form')),

            fbOptions = {
                onSave: function () {
                    $formContainer.val(formBuilder.formData);
                }
            },
            options = {
                formData: test,
                onSave: function () {
                    $formContainer.val(formBuilder.formData);
                },
                allowStageSort: false,
                showActionButtons: false,
                stickyControls: false,
                disabledFieldButtons: {
                    autocomplete: ['remove', 'edit', 'copy'],
                    text: ['remove', 'edit', 'copy'],
                    select: ['remove', 'edit', 'copy'],
                    textarea: ['remove', 'edit', 'copy'],
                    paragraph: ['remove', 'edit', 'copy'],
                    number: ['remove', 'edit', 'copy'],
                    button: ['remove', 'edit', 'copy'],
                    date: ['remove', 'edit', 'copy'],
                    file: ['remove', 'edit', 'copy'],
                    header: ['remove', 'edit', 'copy'],
                    hidden: ['remove', 'edit', 'copy'],
                    'radio-group': ['remove', 'edit', 'copy'],
                    'checkbox-group': ['remove', 'edit', 'copy'],
                },
            },
            options2 = {
                formData: test,
                onSave: function () {
                    $formContainer.val(formBuilder.formData);
                }
            };
        $('#status_requested_form #status-build-wrap').empty();
        $(fbTemplate).formRender(options);
        $('#status-build-wrap .form-group').css('width', '100%');
        
        setupDependsOn(document.getElementById('status_requested_form'));
        

        document.getElementById("Status_saveData").addEventListener("click", () => {
            var outputHtml = $(fbTemplate).formRender("userData");
            $formContainer.val(JSON.stringify(outputHtml));
            // console.log(outputHtml);
        });

        setTimeout(function () {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            $('input[type=date]').attr('min', today);
        }, 3000);
    }
    /* transfer related functions */
    // refill the problem category
    t.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmTransfer.extraData.problem_categories = [];
        t.frmTransfer.el.problem_category_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.frmTransfer.el.department_id.val()));
        if(type_val != t.config.transfer_department_id){
            t.frmTransfer.el.delete_previous_tasks.closest('.row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.row').addClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            if (config.client === "ltts") {
                var selectedDept = t.frmTransfer.el.department_id.find(':selected').text();
                departmentName = selectedDept.substr(0, selectedDept.indexOf('('));
                if (departmentName.trim() == "Admin - India") {
                    t.frmTransfer.el.problem_category_id.empty().append(new Option("Select Location", ""));
                }
            }
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    t.frmTransfer.extraData.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        // t.frmTransfer.el.problem_category_id.append(new Option(v.name, v.id));
                        t.frmTransfer.el.problem_category_id.append($('<option>').val(v.id).text(v.name).attr('description', v.remarks).attr('form', v.form_id));
                    });
                }
            }).always(function () {
                t.frmTransfer.el.problem_category_id.trigger("change");
            });
        } else {
            t.frmTransfer.el.problem_category_id.trigger("change");
        }

        t.getAssignableUsers(type_val, t.frmTransfer.el.self_assign.val());
    };

    /* get the assignable users */
    t.getAssignableUsers = function (department_id, assign_mode) {
        if (department_id > 0 && !isNaN(department_id)) {
            $.get(t.config.url.get_users_to_assign_by_dep + '/' + department_id).done(function (data) {
                t.fillAssignedTo(data, assign_mode, "");
                t.fillAssignedToForEdit(data, assign_mode, t.original_data.assigned_to);
            });
        }
        else {
            t.fillAssignedTo([], assign_mode, "");
            t.fillAssignedToForEdit([], assign_mode, t.original_data.assigned_to);
        }
    };

    /* set assigned to handlers on transfer ticket */
    t.fillAssignedTo = function (handlers, assign_mode, current_handler) {
        var temp;
        t.frmTransfer.el.assign_to.empty().append(new Option(config.translations.Select_User, ""));
        if (assign_mode != 1) {
            $.each(handlers, function (i, v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmTransfer.el.assign_to.append(temp);
            });
        }
        t.frmTransfer.el.assign_to.trigger("change");
    };

    // choose the priority and tat by problem type setup
    t.fillSla = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmTransfer.el.problem_category_id.val();
        var sub_category_id = t.frmTransfer.el.sub_category_id.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.frmTransfer.extraData.sub_categories) == true && t.frmTransfer.extraData.sub_categories.length) {
            $.each(t.frmTransfer.extraData.sub_categories, function (i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmTransfer.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.frmTransfer.extraData.problem_categories != "undefined" && t.frmTransfer.extraData.problem_categories.length) {
            $.each(t.frmTransfer.extraData.problem_categories, function (i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmTransfer.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.frmTransfer.el.priority_id.val("").trigger("change");
            t.frmTransfer.el.tat.val(0);
            t.offListen = false;
        }
        // t.updateTransferform();
    };

    t.updateTransferform = function () {
        var foundObject = null;
        if (t.frmTransfer.el.problem_category_id.val() != null && t.frmTransfer.el.problem_category_id.val() != '') {
            $.each(t.frmTransfer.extraData.problem_categories, function (index, obj) {
                if (obj.id == t.frmTransfer.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if (foundObject != null) {
                var found = false
                if (foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function (key, value) {
                        if (value.form_id != undefined && value.form_id != 0 && value.form_id != null) {
                            found = true;
                            return false; // exit the loop
                        } else if (value.form_id == undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                            found = true;
                            return false;
                        }
                    });

                    if (found) {
                        if (t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // console.log(newUrl);
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                                // t.frmTransfer.el.form_info.show();
                            }
                        }
                        if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmTransfer.el.sub_category_id.val() != "") {
                            if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'unable to load Form.'
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                // console.log("form_id:" + form_id);
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    // console.log(newUrl);
                                    // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                    $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                            }
                        }
                    }
                } else {
                    if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                            // console.log("form_id:" + form_id);
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // console.log(newUrl);
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'unable to load Form.'
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        } else {
                            var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                            var request_id = btoa(t.frmTransfer.el.id.val());
                            var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                        }
                    }
                }
                if (found) {
                    $("#ticketTransferModal").find("#request_submit_id").val(0);
                } else {
                    $("#ticketTransferModal").find("#request_submit_id").val("");
                }
            }
        }
    }

    t.loadRequestedForm = function (fields) {
        var test = fields;
        var fbTemplate = document.getElementById('build-wrap'),
            $fbEditor = $(document.getElementById('fb-editor')),
            $formContainer = $(document.getElementById('field_values')),
            $editContainers = $(document.getElementById('fb-rendered-form')),

            fbOptions = {
                onSave: function () {
                    $formContainer.val(formBuilder.formData);
                }
            },
            options = {
                formData: test,
                onSave: function () {
                    $formContainer.val(formBuilder.formData);
                },
                allowStageSort: false,
                showActionButtons: false,
                stickyControls: false,
                disabledFieldButtons: {
                    autocomplete: ['remove', 'edit', 'copy'],
                    text: ['remove', 'edit', 'copy'],
                    select: ['remove', 'edit', 'copy'],
                    textarea: ['remove', 'edit', 'copy'],
                    paragraph: ['remove', 'edit', 'copy'],
                    number: ['remove', 'edit', 'copy'],
                    button: ['remove', 'edit', 'copy'],
                    date: ['remove', 'edit', 'copy'],
                    file: ['remove', 'edit', 'copy'],
                    header: ['remove', 'edit', 'copy'],
                    hidden: ['remove', 'edit', 'copy'],
                    'radio-group': ['remove', 'edit', 'copy'],
                    'checkbox-group': ['remove', 'edit', 'copy'],
                },
            },
            options2 = {
                formData: test,
                onSave: function () {
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

        setTimeout(function () {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            // $('.datepicker').attr('min', today);
            $('input[type=date]').attr('min', today);
            console.log("datepicker reload");
        }, 3000);
    }

    // refill the priority dropdown
    t.refillPriority = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmTransfer.el.priority_id.empty().append(new Option(config.translations.select_priority, ""));
        t.frmTransfer.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmTransfer.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmTransfer.el.priority_id.trigger("change");
    };

    // to fill the tat by default priority service time
    t.refillTat = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.frmTransfer.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmTransfer.el.tat.val(val);
    }

    t.refillSubCategory = function (e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmTransfer.extraData.sub_categories = [];
        t.frmTransfer.el.sub_category_id.empty().append(new Option(config.translations.Select_Sub_Category, ""));
        var type_val = parseInt($.trim(t.frmTransfer.el.problem_category_id.val()));
        if(type_val != t.config.transfer_pc){
            t.frmTransfer.el.delete_previous_tasks.closest('.row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.row').addClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.frmTransfer.extraData.problem_categories, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.frmTransfer.extraData.sub_categories = v.sub;
                            $.each(v.sub, function (j, k) {
                                // t.frmTransfer.el.sub_category_id.append(new Option(k.name, k.id));
                                t.frmTransfer.el.sub_category_id.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }

        // console.log("Pre Sub Cat Value : ", val);
        if (typeof val != "undefined") {
            t.frmTransfer.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibility();
        t.frmTransfer.el.sub_category_id.trigger("change");
    }

    /* Edit ticket related functions */
    // refill the problem category
    t.refillProblemCategoryForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEdit.extraData = []
        t.frmEdit.extraData.problem_categories = [];
        t.frmEdit.extraData.problem_category = [];
        t.frmEdit.el.problem_category_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.frmEdit.el.department_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            if (config.client === "ltts") {
                var selectedDept = t.frmEdit.el.department_id.find(':selected').text();
                departmentName = selectedDept.substr(0, selectedDept.indexOf('('));
                if (departmentName.trim() == "Admin - India") {
                    t.frmEdit.el.problem_category_id.empty().append(new Option("Select Location", ""));
                }
            }
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    // console.log(data)
                    t.frmEdit.extraData.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        if (v.id == t.frmEdit.el.problem_category_id.val()) return;
                        // t.frmTransfer.el.problem_category_id.append(new Option(v.name, v.id));
                        t.frmEdit.el.problem_category_id.append($('<option>').val(v.id).text(v.name).attr('description', v.remarks).attr('form', v.form_id).attr('is_form_required', v.is_form_required));
                    });
                }
            }).always(function () {
                t.frmEdit.el.problem_category_id.trigger("change");
            });
        } else {
            t.frmEdit.el.problem_category_id.trigger("change");
        }

        t.getAssignableUsers(type_val, t.frmEdit.el.self_assign.val());
    };

    /* set assigned to handlers on transfer ticket */
    t.fillAssignedToForEdit = function (handlers, assign_mode, current_handler) {
        var temp;
        t.frmEdit.el.assign_to.empty().append(new Option(config.translations.Select_User, ""));
        if (assign_mode != 1) {
            $.each(handlers, function (i, v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmEdit.el.assign_to.append(temp);
            });
        }
        t.frmEdit.el.assign_to.trigger("change");
    };
    // choose the priority and tat by problem type setup
    t.fillSlaForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmEdit.el.problem_category_id.val();
        var sub_category_id = t.frmEdit.el.sub_category_id.val();
        var found = false;
        /* if sub category there */
        if (Array.isArray(t.frmEdit.extraData.sub_categories) == true && t.frmEdit.extraData.sub_categories.length) {
            $.each(t.frmEdit.extraData.sub_categories, function (i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEdit.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEdit.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEdit.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.frmEdit.extraData.problem_categories != "undefined" && t.frmEdit.extraData.problem_categories.length) {
            $.each(t.frmEdit.extraData.problem_categories, function (i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEdit.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEdit.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEdit.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.frmEdit.el.priority_id.val("").trigger("change");
            t.frmEdit.el.tat.val(0);
            t.offListen = false;
        }
        t.updateSubCategoryVisibilityForEdit();
        // t.updateEditTicketform();
    };

    // refill the priority dropdown
    t.refillPriorityForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEdit.el.priority_id.empty().append(new Option(config.translations.select_priority, ""));
        t.frmEdit.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmEdit.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmEdit.el.priority_id.trigger("change");
    };

    // to fill the tat by default priority service time
    t.refillTatForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.frmEdit.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmEdit.el.tat.val(val);
    }

    t.refillSubCategoryForEdit = function (e, val) {
      
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEdit.extraData.sub_categories = [];
        t.frmEdit.el.sub_category_id.empty().append(new Option(config.translations.Select_Sub_Category, ""));
        var type_val = parseInt($.trim(t.frmEdit.el.problem_category_id.val()));
        if(t.config.pc != type_val){
            t.frmEdit.el.delete_previous_tasks.closest('.row').removeClass('d-none');
        }else{
            t.frmEdit.el.delete_previous_tasks.closest('.row').addClass('d-none');
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.frmEdit.extraData.problem_category, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.frmEdit.extraData.sub_categories = v.sub;
                            $.each(v.sub, function (j, k) {
                                // console.log(k);
                                if (k.id == t.original_data.sub_category_id) {
                                    t.config.s_pc = t.original_data.sub_category_id;
                                    t.frmEdit.el.sub_category_id.append($('<option selected>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                                } else {
                                    t.frmEdit.el.sub_category_id.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
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

        // console.log("Pre Sub Cat Value : ", val);
        if (typeof val != "undefined") {
            t.frmEdit.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibilityForEdit();
        t.frmEdit.el.sub_category_id.trigger("change");
    }

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibilityForEdit = function () {
        var currentProblemCat = parseInt(t.frmEdit.el.problem_category_id.val() || 0);
        var currentSubCat     = parseInt(t.frmEdit.el.sub_category_id.val() || 0);

        var originalProblemCat = parseInt(t.config.pc || 0);                  // original category
        var originalSubCat     = parseInt(t.original_data.sub_category_id || 0); // original subcategory

        var problemChanged = originalProblemCat && (currentProblemCat !== originalProblemCat);
        var subChanged     = originalSubCat && currentSubCat !== 0 && currentSubCat !== originalSubCat;

        if (problemChanged || subChanged) {
            t.frmEdit.el.delete_previous_tasks.closest('.row').removeClass('d-none');
        } else {
            t.frmEdit.el.delete_previous_tasks.closest('.row').addClass('d-none');
        }
        if (t.frmEdit.extraData.sub_categories.length > 0) {
            t.frmEdit.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmEdit.el.sub_category_id_cvr.show();
        } else {
            t.frmEdit.el.sub_category_id.rules("remove");
            t.frmEdit.el.sub_category_id_cvr.hide();
        }
    };

    t.updateEditTicketform = function () {
        var foundObject = null;
        if (t.frmEdit.el.problem_category_id.val() != null && t.frmEdit.el.problem_category_id.val() != '') {
            $.each(t.frmEdit.extraData.problem_categories, function (index, obj) {
                if (obj.id == t.frmEdit.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if (foundObject != null) {
                var found = false
                if (foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function (key, value) {
                        if (value.form_id != undefined && value.form_id != 0 && value.form_id != null) {
                            found = true;
                            return false; // exit the loop
                        } else if (value.form_id == undefined && t.frmEdit.el.problem_category_id.find(':selected').attr('form') != 0) {
                            found = true;
                            return false;
                        }
                    });
                    if (found) {
                        if (t.frmEdit.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmEdit.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmEdit.el.sub_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEdit.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // console.log(newUrl);
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEdit.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                                // t.frmTransfer.el.form_info.show();
                            }
                        }
                        if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEdit.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmEdit.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmEdit.el.sub_category_id.val() != "") {
                            if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') == null || t.frmEdit.el.sub_category_id.find(':selected').attr('form') == null) {

                                var form_id = t.frmEdit.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEdit.el.id.val());
                                    var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEdit.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmEdit.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEdit.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEdit.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') == null || t.frmEdit.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmEdit.el.problem_category_id.find(':selected').attr('form');
                                // console.log("form_id:" + form_id);
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEdit.el.id.val());
                                    var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    // console.log(newUrl);
                                    // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                                    $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEdit.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmEdit.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEdit.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                            }
                        }
                    }
                } else {
                    if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEdit.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if (t.frmEdit.el.problem_category_id.find(':selected').attr('form') == null || t.frmEdit.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmEdit.el.problem_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEdit.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function (result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEdit.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'unable to load Form.'
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        } else {
                            var form_id = t.frmEdit.el.sub_category_id.find(':selected').attr('form');
                            var request_id = btoa(t.frmEdit.el.id.val());
                            var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                        }
                    }
                }
                if (found) {
                    $("#editTicketModal").find("#request_submit_id").val(0);
                } else {
                    $("#editTicketModal").find("#request_submit_id").val("");
                }
            }

        }
    }

    t.frmTransfer.el.sub_category_id.on('change',function(){
        var currentProblemCat = parseInt(t.frmTransfer.el.problem_category_id.val() || 0);
        var currentSubCat     = parseInt(t.frmTransfer.el.sub_category_id.val() || 0);

        var originalProblemCat = parseInt(t.config.transfer_pc || 0);
        var originalSubCat     = parseInt(t.config.transfer_s_pc || 0);
        var problemChanged = originalProblemCat && (currentProblemCat !== originalProblemCat);
        var subChanged     = originalSubCat && currentSubCat !== 0 && currentSubCat !== originalSubCat;

        if (problemChanged || subChanged) {
            t.frmTransfer.el.delete_previous_tasks.closest('.row').removeClass('d-none');
        } else {
            t.frmTransfer.el.delete_previous_tasks.closest('.row').addClass('d-none');
        }
    })

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibility = function () {
        if (t.frmTransfer.extraData.sub_categories.length > 0) {
            t.frmTransfer.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmTransfer.el.sub_category_id_cvr.show();
        } else {
            t.frmTransfer.el.sub_category_id.rules("remove");
            t.frmTransfer.el.sub_category_id_cvr.hide();
        }
    };

    t.frmTransfer.toggleListen = function (s) {
        t.frmTransfer.el.department_id.off("change", $.proxy(t.refillProblemCategory));
        t.frmTransfer.el.problem_category_id.off("change", $.proxy(t.fillSla));
        t.frmTransfer.el.sub_category_id.off("change", $.proxy(t.fillSla));
        t.frmTransfer.el.priority_id.off("change", $.proxy(t.refillTat));

        if (typeof s != "undefined" && s == true) {
            t.frmTransfer.el.department_id.on("change", $.proxy(t.refillProblemCategory));
            t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.fillSla));
            t.frmTransfer.el.sub_category_id.on("change", $.proxy(t.fillSla));
            t.frmTransfer.el.priority_id.on("change", $.proxy(t.refillTat));
        }
    };

    t.loadEditTcket = function (e, assign_mode) {
        e.preventDefault();
        if (typeof assign_mode != "undefined") {
            t.frmEdit.el.self_assign.val(1);
        }
        t.frmEdit.el.self_assign.val("");
        t.mdlEdit.title.text("Edit Ticket");
        t.frmEdit.el.btnSubmit.text('Update');
        $.get(t.config.url.get_data_for_transfer + "/" + t.data.id + "?from=edit", function (result) {
            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                t.frmEdit.el.id.val(t.data.id);
                t.frmEdit.el.token.val(t.config.token); 
                t.frmEdit.find("#edit_department_id").val(result.data.opts.departments.id);
                t.frmEdit.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                t.frmEdit.el.service_type_id.empty();
                if (result.data.opts.devices.id != null) {
                    t.frmEdit.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmEdit.el.device_id.empty().append(new Option(config.translations.select_device, ""));
                }
                $.each(result.data.opts.service_types, function (i, d) {
                    var op = (d.id == result.data.ticket.service_type_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmEdit.el.service_type_id.append(op);
                });
                t.config.pc = result.data.ticket.problem_category_id;
                t.frmEdit.el.service_type_id.trigger("change");
                if (result.data.edit_ticket_action_controls?.ctrl_tat == 1) {
                    t.frmEdit.el.tat.prop('disabled', false);
                } else {
                    t.frmEdit.el.tat.prop('disabled', true);
                }
                if (result.data.edit_ticket_action_controls?.ctrl_priority == 1) {
                    t.frmEdit.el.priority_id.prop('disabled', false);
                } else {
                    t.frmEdit.el.priority_id.prop('disabled', true);
                }

                t.frmEdit.el.problem_category_id.empty();
                if (typeof result.data.opts.problem_categories != "undefined" && result.data.opts.problem_categories.length > 0) {
                    t.frmEdit.extraData = {}
                    t.frmEdit.extraData.problem_categories = result.data.opts.problem_categories;
                    t.frmEdit.extraData.problem_category = result.data.opts.problem_categories;
                    t.frmEdit.extraData.sub_categories = result.data.opts.problem_categories;
                   
                    $.each(result.data.opts.problem_categories, function(i, d) {
                        let option = $('<option>', {
                            value: d.id,
                            text: d.name,
                            selected: d.id == result.data.ticket.problem_category_id
                        }).attr('form', d.form_id).attr('is_form_required', d.is_form_required);
                        t.frmEdit.el.problem_category_id.append(option);
                    });
                }
                t.frmEdit.el.problem_category_id.trigger("change");
                
                t.refillSubCategoryForEdit(undefined, result.data.ticket.sub_category_id);
                t.frmEdit.el.priority_id.empty();
                $.each(t.config.priorities, function (i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmEdit.el.priority_id.append(op);
                });

                t.frmEdit.el.tat.val(result.data.ticket.tat);
                t.mdlEdit.modal("show");

                /* set handlers to assign to */
                t.fillAssignedToForEdit(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

            }
            else {
                var data = {
                    'msg': 'Unable to load transfer form',
                };
                sweetAlert('center', 'error', data);
            }
        });
    }

    t.frmEditSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        if (t.frmForEditValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        if (t.frmEdit.el.priority_id.prop("disabled")) {
            let val = t.frmEdit.el.priority_id.val();
            console.log(val);
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmEdit);
        }
        if (t.frmEdit.el.tat.prop("disabled")) {
            let tatVal =t.frmEdit.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmEdit);
        }
        t.frmEdit.el.btnSubmit.attr("disabled", true);
        t.mdl_popup_loader.addClass('active');
        t.httpCall = false;
        var formData = new FormData(t.frmEdit.get(0));
        formData.append('department_id',$('#edit_department_id').val());
        // console.log(formData);
        var http = $.ajax({
            url: t.config.url.editTicket,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmEdit.el.btnSubmit.removeAttr("disabled");
                    t.mdl_popup_loader.removeClass('active');
                    sweetAlert('center', 'success', data);
                    t.mdlEdit.modal('hide');
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.frmEdit.el.btnSubmit.removeAttr("disabled");
                    t.mdl_popup_loader.removeClass('active');
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateEditTicketform();
                    }
                }
            }
        });
        http.fail(function () {
            t.frmEdit.el.btnSubmit.removeAttr("disabled");
            t.mdl_popup_loader.removeClass('active');
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.frmEdit.el.btnSubmit.removeAttr("disabled");
            t.mdl_popup_loader.removeClass('active');
            t.httpCall = true;
        });
    };

    t.loadTransfer = function (e, assign_mode) {
        e.preventDefault();
        $("#ticketTransferModal").find("#remarks_wrapper").removeClass("hide");
        t.frmTransfer.el.remarks.removeClass("hide");
        t.frmTransfer.el.is_note.removeClass("hide");
        t.frmTransfer.el.self_assign.val("");
        t.frmTransfer.el.assign_to_cvr.removeClass("hide");
        t.mdlTransfer.title.text(config.translations.Transfer_Ticket);
        t.frmTransfer.el.btnSubmit.text('Transfer');
        t.frmTransferValidator.resetForm();
        if (typeof assign_mode != "undefined") {
            $("#ticketTransferModal").find("#remarks_wrapper").addClass("hide");
            t.frmTransfer.el.remarks.addClass("hide");
            t.frmTransfer.el.is_note.addClass("hide");
            t.frmTransfer.el.self_assign.val(1);
            t.frmTransfer.el.assign_to_cvr.addClass("hide");
            t.mdlTransfer.title.text('Ticket Self Assign');
            t.frmTransfer.el.btnSubmit.text('Assign');
        }

        $.get(t.config.url.get_data_for_transfer + "/" + t.data.id, function (result) {
            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                t.frmTransfer.toggleListen();
                t.frmTransfer.el.id.val(t.data.id);
                t.frmTransfer.el.token.val(t.config.token);
                t.frmTransfer.el.creatorId.val(result.data.ticket.creator_id);
                t.frmTransfer.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                t.config.transfer_department_id = result.data.opts.departments.id;
                if (result.data.opts.devices.id != null) {
                    t.frmTransfer.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmTransfer.el.device_id.empty().append(new Option(config.translations.Select_User, ""));
                }

                /* set problem category */
                t.frmTransfer.el.problem_category_id.empty();
                $.each(result.data.opts.problem_categories, function (i, d) {
                    if(d.id == result.data.ticket.problem_category_id){
                        var op =  new Option(d.name, d.id, true, true);
                        t.config.transfer_pc = d.id;
                    }else{
                        var op = new Option(d.name, d.id)
                    }
                    if (typeof d.form_id != 'undefined' && d.form_id != null) {
                        $(op).attr('form', d.form_id);
                    }
                    t.frmTransfer.el.problem_category_id.append(op);
                });
                t.frmTransfer.el.problem_category_id.trigger("change");
                t.frmTransfer.extraData.problem_categories = result.data.opts.problem_categories;

                /* set sub category */
                t.config.transfer_s_pc = result.data.ticket.sub_category_id;
                t.refillSubCategory(undefined, result.data.ticket.sub_category_id);

                t.frmTransfer.el.priority_id.empty();
                $.each(t.config.priorities, function (i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmTransfer.el.priority_id.append(op);
                });

                if (result.data.edit_ticket_action_controls?.ctrl_tat == 1) {
                    t.frmTransfer.el.tat.prop('disabled', false);
                } else {
                    t.frmTransfer.el.tat.prop('disabled', true);
                }
                if (result.data.edit_ticket_action_controls?.ctrl_priority == 1) {
                    t.frmTransfer.el.priority_id.prop('disabled', false);
                } else {
                    t.frmTransfer.el.priority_id.prop('disabled', true);
                }

                t.frmTransfer.el.tat.val(result.data.ticket.tat);
                t.frmTransfer.el.tags.empty();
                $.each(result.data.tags.tags, function (key, value) {
                    t.frmTransfer.el.tags.select2('trigger', 'select', {
                        data: { text: value.tags, id: value.id, selected: true }
                    });
                });

                /* set handlers to assign to */
                t.fillAssignedTo(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

                t.frmTransfer.toggleListen(true);
                t.mdlTransfer.modal("show");
            } else {
                var data = {
                    'msg': 'Unable to load transfer form.',
                };
                sweetAlert('center', 'error', data);
            }
        });
    };

    t.frmTransferSubmit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        //        if(t.config.wai != 23) {
        //            vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><p>This ticket is not assigned to you. Please contact your manager.</p></div>'});
        //            return;
        //        }
        if(t.frmTransfer.el.self_assign.val() !=1){
            let s = t.frmTransfer.find("#remarks").val();
            count = s.replaceAll("&nbsp;", "").trim();
            if (t.frmTransferValidator.form() == false) {
                if (count.length <= 0) {
                    $('#ticketTransferModal').find('#shows_error').html('This field is required.');
                    $('#ticketTransferModal').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                }
                return false;
            }
            if (count.length <= 0) {
                $('#ticketTransferModal').find('#shows_error').html('This field is required');
                $('#ticketTransferModal').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                return false;
            } else {
                $('#ticketTransferModal').find('#shows_error').html('');
            }
        } else {
            if (t.frmTransferValidator.form() == false) {
                return false;
            }

            if (t.httpCall != true) {
                return false;
            }
        }
        if (t.frmTransfer.el.priority_id.prop("disabled")) {
            let val = t.frmTransfer.el.priority_id.val();
            console.log(val);
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmTransfer);
        }
        if (t.frmTransfer.el.tat.prop("disabled")) {
            let tatVal = t.frmTransfer.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmTransfer);
        }
        t.frmTransfer.el.btnSubmit.attr("disabled", true);
        t.mdl_popup_loader.addClass('active');
        t.httpCall = false;
        var formData = new FormData(t.frmTransfer.get(0));
        var http = $.ajax({
            url: t.config.url.transfer,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    t.mdl_popup_loader.removeClass('active');
                    sweetAlert('center', 'success', data);
                    t.mdlTransfer.modal('hide');
                    setTimeout(function() {
                        window.location.reload();
                    }, 600);
                } else {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    t.mdl_popup_loader.removeClass('active');
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateTransferform();
                    }
                }
            }
        });
        http.fail(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            t.mdl_popup_loader.removeClass('active');
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            t.mdl_popup_loader.removeClass('active');
            t.httpCall = true;
        });
    };
    /* transfer related functions end */

    t.staringUi = function (val) {
        var s = (typeof val != "undefined") ? val : t.data.self_star;
        var e = t.page.find(".js-act-staring");
        var addStarText = (config.translations && config.translations.Add_Star) ? config.translations.Add_Star : 'Add Star';
        var starredText = (config.translations && config.translations.Starred) ? config.translations.Starred : 'Starred';

        t.config.star_data = s == true;

        if (e.hasClass('tkd-action-btn')) {
            var starIcon = s == true
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z"/></svg>';

            e.html(starIcon + '<span>' + (s == true ? starredText : addStarText) + '</span>');
            return;
        }

        if (s == true) {
            e.html('<i class="fa fa-star"></i> Starred');
        } else {
            e.html('Add Star');
        }
    };

    /* to show the reopen mdl after click the reopen btn */
    t.openReopenModal = function (e) {
        e.preventDefault();
        t.frmReopen.el.comment.summernote("code", "");
        t.mdlReopen.modal("show");
    };

    /* to show the feedback mdl  */
    t.openFeedbackModal = function (e) {
        e.preventDefault();
        var shedId = t.data.id;
        t.httpPostPath = t.config.url.edit_feedback;
        var http = $.get(t.httpPostPath + "/" + shedId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.loadForm(data.data);
                } else {
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
            t.httpCall = true;
        });
    };

    t.loadForm = function (data, forAction) {
        t.mdl.find("textarea[name='remarks']").summernote('code', data['remarks']);
        t.mdl.find("input[name='feedback'][value='" + data.feedback + "']").prop("checked", true);
        t.mdl.frmEl.id.val(data['ticket_id']);
        t.frmFeedback.el.divQuestion.removeClass("hide");
        t.frmFeedback.el.divResult.addClass("hide");
        t.mdl.frmEl.btnSubmit.removeClass("hide");
    };

    t.updateFeedback = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        if (t.frmUpdateFeedbackValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }
        t.mdl.frmEl.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        t.httpPostPath = t.config.url.update_feedback;
        var formData = new FormData($('#frm-feedback-update')[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

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
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    t.mdl.frmEl.btnSubmit.attr("disabled", false);
                    t.frmUpdateFeedbackValidator.resetForm();
                    t.httpCall = true;
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.httpCall = true;
            t.mdl.frmEl.btnSubmit.attr("disabled", false);
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
    };

    t.staring = function (e) {
        e.preventDefault();
        if (t.httpCall != true) {
            return false;
        }
        var message ='';
        if(t.config.star_data){
            message = config.translations.unstar_ticket;
        }else{
            message = config.translations.are_you_star;
        }
        sweetAlertConfirm({
            message: message,
            url: t.config.url.staring,
            data: {
                "_token": t.config.token,
                "id": t.data.id
            },
            onSuccess: function (data) {
                t.config.star_data =  data.star;
                t.staringUi(data.star);
            },
            errorMsg: config.translations.something_went_wrong,
            beforeSend: function () {
                t.httpCall = false;
            },
            complete: function () {
                t.httpCall = true;
            }
        });
    };

    t.spamming = function (e) {
        e.preventDefault();
        if (t.httpCall != true) {
            return false;
        }
        var question = '';
        if(t.config.creator_isTechnician && t.data.spam == null){
            question = 'The ticket was created by a technician. If you mark it as spam, this technician will be blocked in the system.';
        }else{
            question = t.data.spam == 1 ? 'Are you sure to remove ticket from Spam list' : config.translations.are_you_spam;
        }
        sweetAlertConfirm({
            message: question,
            url: t.config.url.spam,
            data: {
                "_token": t.config.token,
                "id": t.data.id,
                'is_spam': t.data.spam
            },
            onSuccess: function (data) {
                setTimeout(function () {
                    window.location.reload(true);
                }, 600);
            },
            errorMsg: config.translations.something_went_wrong,
            beforeSend: function () {
                t.httpCall = false;
            },
            complete: function () {
                t.httpCall = true;
            }
        });
    };

    t.goBack = function (e) {
        e.preventDefault();
        if (t.config.url.back_to != "") {
            window.location = t.config.url.back_to;
        } else if ('referrer' in document) {
            window.location = document.referrer;
        } else {
            window.history.back();
        }
    }
    t.toggleSection = function (e, section, button) {
        e.preventDefault();
        var target = t.page.find(section);
        var isCollapsed = target.toggleClass('is-collapsed').hasClass('is-collapsed');
        button
            .attr('title', isCollapsed ? 'Expand' : 'Collapse')
            .attr('aria-expanded', String(!isCollapsed));

        button.find('i')
            .toggleClass('bi-arrows-angle-expand', isCollapsed)
            .toggleClass('bi-arrows-angle-contract', !isCollapsed);
    };

    t.toggleTicketSummary = function (e) {
        t.toggleSection(e, '.tkd-ticket-summary', t.toggle_tiny_view);
    };

    // t.toggleTicketDetail = function (e) {
    //     t.toggleSection(e, '#ticketDetailContent', t.toggle_ticket_detail);
    // };
    t.toggleTicketDetail = function(e){
        e.preventDefault();

        var content = t.page.find('#ticketDetailContent');

        var isCollapsed = content.toggleClass('is-collapsed').hasClass('is-collapsed');

        t.toggle_ticket_detail
            .attr('title', isCollapsed ? 'Expand' : 'Collapse')
            .attr('aria-expanded', !isCollapsed);

        t.toggle_ticket_detail.find('i')
            .toggleClass('bi-arrows-angle-expand', isCollapsed)
            .toggleClass('bi-arrows-angle-contract', !isCollapsed);
    };

    t.setSingleTinyViewer = function (el, target_val) {
        var card = $(el).closest('.card');
        var content = card.find('.tml-content');
        var btn = $(el).find('.single-tiny-viewer');
        var useNewTimelineIcon = btn.hasClass('tkd-icon-plain');
        if (target_val == true) {
            card.css('height', '');
            card.css('height', 'auto');
            card.removeClass('is-collapsed');
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer')
                // .html(useNewTimelineIcon ? '<i class="bi bi-arrows-angle-contract"></i>' : '<i class="fa fa-compress"></i>')
                .attr('title', 'Compress')
                .attr('aria-expanded', 'true');
        } else {
            card.css('height', '');
            card.addClass('is-collapsed');
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer')
                // .html(useNewTimelineIcon ? '<i class="bi bi-arrows-angle-expand"></i>' : '<i class="fa fa-expand faa-fast animated"></i>')
                .attr('title', 'Expand')
                .attr('aria-expanded', 'false');
        }
    }

    t.setSingleTinyViewerRead = function (el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="fa fa-arrow-up"></i>').attr('title', 'Compress');
        } else {
            $(el).addClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="fa fa-arrow-down"></i>').attr('title', 'Expand');
        }
    }

    t.toggleSingleTinyViewer = function (e) {
        e.preventDefault();
        var el = $(e.currentTarget).closest('.card').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };

    t.toggleTinyViewAll = function (e) {
        e.preventDefault();
        if (t.toggle_conversation_view.hasClass('tiny-view-on')) {
            // Expanded state
            t.toggle_conversation_view
                .removeClass('tiny-view-on')
                .html('<i class="bi bi-arrows-angle-expand"></i>')
                .attr('title', 'Collapse all conversations')
                .attr('aria-expanded', 'true');
            t.timeline.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, true);
            });

        } else {
            t.toggle_conversation_view
                .addClass('tiny-view-on')
                .html('<i class="bi bi-arrows-angle-contract"></i>')
                .attr('title', 'Expand all conversations')
                .attr('aria-expanded', 'false');
            t.timeline.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, false);
            });
        }
    };

    t.toggleTinyViewAllRead = function (e) {
        e.preventDefault();
        if (t.toggle_tiny_view_read.hasClass('tiny-view-on-read') == true) {
            t.toggle_tiny_view_read.removeClass('tiny-view-on-read').html('<i class="fa fa-arrow-up"></i>').attr('title', 'Compress');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, true);
            });
        }
        else {
            t.toggle_tiny_view_read.addClass('tiny-view-on-read').html('<i class="fa fa-arrow-down"></i>').attr('title', 'Expand');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, false);
            });
        }
    };

    function escapeHtml(value) {
        return $('<div>').text(value == null ? '' : String(value)).html();
    }

    function escapeAttr(value) {
        return escapeHtml(value).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    t.getFileExtension = function (fileName, fallbackExt) {
        var ext = fallbackExt || '';
        if (fileName && fileName.indexOf('.') !== -1) {
            ext = fileName.split('.').pop();
        }

        return (ext || '').toString().toLowerCase();
    };

    t.isImageExtension = function (ext) {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].indexOf((ext || '').toLowerCase()) !== -1;
    };

    t.getAttachmentIconMeta = function (ext) {
        ext = (ext || '').toLowerCase();
        if (t.isImageExtension(ext)) {
            return { icon: 'bi-file-earmark-image', typeClass: 'image-icon' };
        }
        if (['xls', 'xlsx'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-excel', typeClass: 'excel-icon' };
        }
        if (['doc', 'docx'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-word', typeClass: 'word-icon' };
        }
        if (ext == 'pdf') {
            return { icon: 'bi-file-earmark-pdf', typeClass: 'pdf-icon' };
        }
        if (['zip', 'rar', '7z'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-zip', typeClass: 'zip-icon' };
        }
        if (ext == 'csv') {
            return { icon: 'bi-filetype-csv', typeClass: 'csv-icon' };
        }
        if (['ppt', 'pptx'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-ppt', typeClass: 'ppt-icon' };
        }
        if (['txt', 'log'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-text', typeClass: 'text-icon' };
        }
        if (['mp4', 'mov', 'avi', 'mkv', 'webm'].indexOf(ext) !== -1) {
            return { icon: 'bi-file-earmark-play', typeClass: 'video-icon' };
        }
        if (['msg', 'eml'].indexOf(ext) !== -1) {
            return { icon: 'bi-envelope-paper', typeClass: 'mail-icon' };
        }
        if (ext == 'psd') {
            return { icon: 'bi-file-earmark-richtext', typeClass: 'psd-icon' };
        }

        return { icon: 'bi-file-earmark', typeClass: 'default-icon' };
    };

    t.getAttachmentIconHtml = function (ext) {
        var meta = t.getAttachmentIconMeta(ext);
        return '<i class="bi ' + meta.icon + ' file-icon ' + meta.typeClass + '"></i>';
    };

    t.formatAttachmentSize = function (bytes) {
        var size = parseFloat(bytes);
        if (isNaN(size)) {
            return "Unknown size";
        }
        if (size < 1024 * 1024) {
            return Math.max(1, Math.ceil(size / 1024)) + " KB";
        }

        return (size / (1024 * 1024)).toFixed(2) + " MB";
    };

    t.getAttachmentDisplayName = function (attachment) {
        var name = attachment && attachment.name ? attachment.name : '';
        return decodeURIComponentSafe(unescape(name));
    };

    t.getImageViewActionHtml = function (viewUrl, fileName) {
        return '<button type="button" class="tri-view" data-view_mode="1" data-view="' + escapeAttr(viewUrl) + '" data-name="' + escapeAttr(fileName) + '" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></button>';
    };


    t.getDownloadActionHtml = function (downloadUrl) {
        return '<button type="button" class="tri-download" data-url="' + escapeAttr(downloadUrl) + '" data-toggle="tooltip" data-title="Download"><i class="bi bi-download"></i></button>';
    };

    t.uploadAttachmentPreviewHtml = function (prefix, index, file) {
        var fileName = file && file.name ? file.name : '';
        var ext = t.getFileExtension(fileName);
        var safeName = escapeHtml(fileName);
        var safeAttrName = escapeAttr(fileName);
        var fileSize = t.formatAttachmentSize(file ? file.size : null);

        return '<div id="' + prefix + index + '" class="' + (prefix == 'attach' ? 'attach' : 'attachs') + ' pad-top count_img" data-size="' + (file ? file.size : 0) + '">' +
            '<div class="bord-btm clearfix">' +
            '<p class="pull-left" title="' + safeAttrName + '">' + t.getAttachmentIconHtml(ext) + '<span class="name">' + safeName + '</span> <span class="upload-size">[' + fileSize + ']</span></p>' +
            '<span style="cursor:pointer" class="remove-attach pull-right" data-size="' + (file ? file.size : 0) + '"><i class="bi bi-x-lg"></i> Remove</span>' +
            '</div>' +
            '<div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div>' +
            '</div>';
    };

    t.setMainAttachments = function () {
        var at = [];
        $.each(t.config.main_attachments, function (i, v) {
            var fileName = t.getAttachmentDisplayName(v);
            var ext = t.getFileExtension(fileName, v.ext);
            if (v.is_motion_photo == 1) {
                ext = "mp4";
            }

            var safeName = escapeHtml(fileName);
            var safeAttrName = escapeAttr(fileName);
            var viewUrl = t.config.url.attachment_view + '/' + v.id;
            var downloadUrl = t.config.url.attachment_download + '/' + v.id;
            var ai_bg = v.thumb == 1 ? "ai-bg" : "";
            var bg = v.thumb == 1 ? "background-image: url(" + viewUrl + "/1);" : "";
            var viewAction = t.isImageExtension(ext) ? t.getImageViewActionHtml(viewUrl, fileName) : "";

            at.push('<div class="attach-item ' + ai_bg + '" >' +
                '<div class="attach-item-cntnt">' +
                t.getAttachmentIconHtml(ext) +
                '<span class="attach-name" title="' + safeAttrName + '">' + safeName + '</span>' +
                '<div class="icons">' + viewAction + t.getDownloadActionHtml(downloadUrl) + '</div>' +
                '</div>' +
                '</div>');
        });

        $(document).on('click', '.tri-view', function (e) {
            e.preventDefault();
            var imageUrl = $(this).data('view');
            var fileName = $(this).data('name');
            $.swipebox([
                {
                    href: imageUrl,
                    title: fileName
                }
            ]);
        });

        if (at.length > 0) {
            t.main_attachments.html('<div><div class="text-bold mar-rgt">' + t.config.translations.attachment +':</div>' + at.join("") + '</div>');
        }
        return;

        var at = [];
        $.each(t.config.main_attachments, function (i, v) {
            var fileName = decodeURIComponentSafe(unescape(v.name));
            var safeName = escapeHtml(fileName);
            var safeAttrName = escapeAttr(fileName);
            var viewUrl = t.config.url.attachment_view + '/' + v.id;
            var downloadUrl = t.config.url.attachment_download + '/' + v.id;
            var ai_bg = v.thumb == 1 ? "ai-bg" : "";
            var bg = v.thumb == 1 ? "background-image: url(" + viewUrl + "/1);" : "";

            at.push('<div class="attach-item ' + ai_bg + '" style="' + bg + '" ><div class="attach-item-cntnt"><span class="attach-name" title="' + safeAttrName + '">' + safeName + '</span><div class="icons"><button type="button" class="tkd-attachment-action tri-download" data-url="' + downloadUrl + '"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="15" height="19" viewBox="0 0 21 26" fill="currentColor"><rect width="21" height="26" fill="url(#pattern0_5871_86478)"/><defs><pattern id="pattern0_5871_86478" patternContentUnits="objectBoundingBox" width="1" height="1"><use xlink:href="#image0_5871_86478" transform="matrix(0.000838818 0 0 0.000677507 -0.00329075 0)"/></pattern><image id="image0_5871_86478" width="1200" height="1476" preserveAspectRatio="none" xlink:href="data:image/png;base64,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jFh+RbUAAADIkJwSAAAAAJBlAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpAiwAAAAAMk2ABQAAAECmCbAAAAAAyDQBFgAAAACZJsACAAAAINMEWAAAAABkmgALAAAAgEwTYAEAAACQaQIsAAAAADJNgAUAAABApgmwAAAAAMg0ARYAAAAAmSbAAgAAACDTBFgAAAAAZJoACwAAAIBME2ABAAAAkGkCLAAAAAAyTYAFAAAAQKYJsAAAAADINAEWAAAAAJkmwAIAAAAg0wRYAAAAAGSaAAsAAACATBNgAQAAAJBpBSUAyJbp06dHtVptiLl269YtisWipteISqUSLS0tdTWnHj16RD6f11w+V0tLS1QqFYVopA9JhUJ0795dIQCy9NysBADZcsghh8SIESMacu4fBVoLLbRQFIvF6NGjRzQ3N0fPnj1j4YUXjl69en387169esUiiywS/fv3j379+kWfPn0snk42dOjQOPjgg+t2fp8Ms3r06BGFQiEWWmih6NWrV/Tp0yd69eoVvXv3joUXXjh69+4dvXr1in79+sWSSy5p/dW5Y489Nl588UWFaCDbb799nHXWWQoBkCECLAAyo7W1NVpbW2PatGlzfdumpqZYZJFFYvHFF48ll1wyllpqqVh66aU//vciiyyiwHyuT55dNrdrsEePHrHEEkvEkksuGUsuuWQMGDAgBgwYEMsuu2wsu+yyzvICAJhPAiwA6kJ7e3uMGzcuxo0bN8szJXr37h0rr7xyrLTSSrHiiit+/GeXMNIRWlpaYsSIEbM8e7K5uTlWWGGFWHXVVWOllVaKVVZZJVZeeeXo0aOHwgEAzCEBFgANYcqUKfHcc8/Fc8899/HPmpubY7XVVosvfelLsfbaa8c666wTffv2VSw6VFtbWwwdOjSGDh368c9yuVwsvfTSse6668agQYNigw02iEUXXVSxAABmQ4AFQMNqa2uLF198MV588cX4wx/+EBERSy21VGy++eaxxRZbxDrrrBNNTU0KRYerVqsxevToGD16dNx9990fr70NN9wwNtpooxg0aFD07t1boQAA/kuABQCf8O6778att94at956a/Ts2TO23HLL2G677WKjjTayjxGdvvbefffduPPOOyOXy8Xaa68d22yzTWy11VbRr18/BQIAGpoACwBmY/r06XHvvffGvffeG3379o1tt9029txzzxg4cKDi0Kmq1WoMGTIkhgwZEr/61a9i3XXX/TjMcpkrANCIckoAAF9s0qRJcdttt8Xee+8dJ598crz22muKwgJRrVbj+eefjwsuuCB23HHHOProo+ORRx6JarWqOABAw3AGFgDMhWq1Gg8//HA8/PDDMWjQoDjmmGNilVVWURgW2Pp75pln4plnnokBAwbEHnvsEd/85jdj4YUXVhwAoK45AwsA5tFzzz0X3/3ud+OCCy6IKVOmKAgL1NixY+Oyyy6LnXfeOQYPHhwjRoxQFACgbgmwAGA+VKvVuP3222OPPfaIu+66S0FY4FpbW+POO++M/fbbL0455ZQYPXq0ogAAdUeABQAdYOrUqXHeeefFiSee6GwsukSapvGPf/wj9tlnnxg8eHBMmDBBUQCAuiHAAoAO9Mgjj8T+++8fzz//vGLQJSqVStx5552xxx57xOWXXx4ffvihogAANU+ABQAd7L333osf/OAH8cc//lEx6DKtra1x4403xh577BEPPvigggAANU2ABQCdoFqtxsUXXxyDBw+OSqWiIHSZSZMmxamnnhrHHXdcjBs3TkEAgJokwAKATnTnnXfGSSedFK2trYpBl3riiSdiv/32i1tuuSWq1aqCAAA1RYAFAJ3s0UcfjWOOOSZmzJihGHSp6dOnx0UXXRRHHHGEs7EAgJoiwAKABWDIkCFx7LHHCrHIzHr8zne+E08++aRiAAA1QYAFAAvIkCFD4sQTT4y2tjbFoMtNmTIljjvuuLj88stdUggAZJ4ACwAWoGeeeSZOP/10gQGZkKZp3HjjjXHsscfG5MmTFQQAyCwBFgAsYP/85z/jiiuuUAgy4+mnn47vfOc7MXz4cMUAADJJgAUAXeDGG2+Mu+++WyHIjPHjx8ehhx4aQ4YMUQwAIHMEWADQRS688MJ45ZVXFILM+PDDD+Poo4+Ohx56SDEAgEwRYAFAF2lra4uTTjrJ3kNkSnt7e5x22mnx17/+VTEAgMwQYAFAF5owYUKce+65CkGmVCqVOPfcc+Omm25SDAAgEwRYANDFHn300bjzzjsVgkxJ0zQuu+yyuPnmmxUDAOhyAiwAyICLL744Ro8erRBkzqWXXupyQgCgywmwACADZsyYET/72c+iWq0qBpmSpmkMHjw4HnvsMcUAALpMQQkA+CI9e/aMPn36dMh9VavV+PDDDz/+77a2tmhvb1fkiHjllVfizjvvjN13310xyJRKpRKnnHJKXHzxxbH++usrCACwwAmwAPhCu+yySxx77LEL5EPy1KlTY9q0aZ/699SpU+P999+PMWPGxLvvvhvvvPNO3X5z3xVXXBFbbbVVhwWGjaB///5RLBbn+fafDFUrlUq0tLQo6iy0tbXF8ccfH1dffXWssMIKCtLFevToEX379lWITqK2ANkjwAIgM/L5fPTt23eOPjhMnz49Ro8eHS+99FK8+OKL8eKLL8bEiRNrvgbTpk2Lyy67LE477TQLYg5ddNFFsdJKK3XY/VUqlY/D00mTJsV7770XEyZMiPHjx8dbb70Vo0aNinHjxkWapg1X6+nTp8dPfvKTuO6666Jnz54WXxf62te+FqeffrpCANAwBFgA1KSePXvG6quvHquvvnrsvffeERExevToeOSRR+K+++6LYcOG1ezc7rnnnthll11inXXW0egukM/no0+fPtGnT58YOHDgLH+npaUlhg8fHq+88kq8+uqr8fLLL8e4ceMaoj6jR4+OM844Iy644ILI5WynCgAsGAIsAOrGwIED44ADDogDDjggnnrqqbj66qvj5Zdfrrl5pGkaV1xxRVx55ZWamlE9evSItdZaK9Zaa62PfzZ69Oh46qmn4sknn4znnnuurvd2e+yxx+Laa6+Ngw8+2GIAABYIARYAdWmTTTaJjTfeOO6666647LLLPrVxfC0YMmRIPPfcczFo0CDNrBEDBw6MgQMHxl577RXTp0+PRx55JB544IF45plnolKp1N18r7766lhttdVis80203wAoNM57xuAupUkSey6665x4403xqqrrlpz47/qqqs0sUb17Nkzdtxxx7j44ovjzjvvjIMPPjgWXXTRuppjtVqN008/PcaPH6/hAECnE2ABUPeWXHLJuOqqq+JrX/taTY37o7OwqG39+/ePQw89NO666644/fTTZ7uvVi368MMP49xzz23IDe0BgAVLgAVAQ2hubo7zzjsvdtppp5oa9zXXXKN5daJQKMROO+0Ut956a5xzzjmxzDLL1MW8nn766bjrrrs0GADoVAIsABrnRS+Xi1NPPTW22Wabmhnzv//97xgxYoTm1dk63HbbbeOWW26Jk08+Ofr06VPzc7r44ovjnXfe0VwAoPPeQykBAA31wpfLxRlnnBEbbLBBzYz57rvv1rg6VCgUYtddd40//vGPsdVWW9X0XGbMmBGDBw92KSEA0Hnv45UAgEbT1NQU5513XgwYMKAmxnvPPfdEW1ubxtWpRRZZJAYPHhyDBw+u6bOxnnvuubj99ts1FADoFAIsABpSnz59YvDgwdHU1JT5sU6dOjUeeughTatzW221Vdx0002x7rrr1uwcfvvb38bkyZM1EwDocAIsABrW6quvHocffnhNjPWvf/2rhjWAxRZbLK644orYf//9a3L806ZNi6uvvlojAYAOJ8ACoKHtu+++sf7662d+nEOGDIlJkyZpWAPI5/Nx9NFHx+mnnx7FYrHmxn/77bf74gEAoMMJsABo7BfCXC5OPvnkzF9KWK1W47HHHtOwBrLTTjvFxRdfHD169KipcVer1bj00ks1EADo2PftSgBAoxs4cGDss88+mR/nP//5T81qMIMGDYorrrgievXqVVPjfvLJJ+PJJ5/UQACgwwiwACAiDjzwwFhsscUyPcbnnnsuWlpaNKvBrL766nHJJZfEQgstVFPjvvTSS6NarWogANAhBFgAEBE9evSI/fbbL9NjbGtri6efflqzGtAaa6wRv/zlL6O5ublmxjxixIh4+OGHNQ8A6BACLAD4r9122y369OmT6TG++OKLGtWg1l133Tj33HMjn8/XzJivu+66SNNU8wCA+SbAAoD/6t69e+y6666ZHuOQIUM0qoFtscUWcfTRR9fMeN9888144oknNA4AmG8CLAD4hJ133jmSJMns+N544w37YDW4fffdN3baaaeaGe8111yjaQDAfBNgAcAnLLPMMrHOOutkdnyVSiVeffVVjWpwJ554Yqy88so1MdZXXnklnn/+eU0DAOaLAAsAPmObbbbJ9Pjsg0W3bt3irLPOqplN3a+77jpNAwDmiwALAD5js802y/T4hg8frknECiusED/4wQ9qYqxPP/10jBgxQtMAgHkmwAKAz1hyySVj+eWXz+z4BAF8ZK+99ooNNtigJsZ65513ahgAMM8EWAAwCxtuuGFmx/bOO+9EuVzWJCJJkjjppJOiqakp82O95557orW1VdMAgHkiwAKAWVhzzTUzO7ZyuRzvvPOOJhEREQMHDoz9998/8+P88MMP48EHH9QwAGCeCLAAYBbWWGONTI/PZYR80ve+971YYoklMj/O22+/XbMAgHkiwAKAWVhmmWWiZ8+emR3fu+++q0l8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9toxcOBAjQIA5ooACwCgTnzwwQfx97//PdNj3GGHHTQKAJhrAiwAgDpx0003RWtra2bHVywWXT4IAMwTARYAQB2YPHly5jdv32yzzaJ3796aBQDMNQEWAEAduOGGG6KlpSXTY3T5IAAwrwRYAAA1buzYsfHnP/8502Ps27dvbLbZZpoFAMwTARYAQI274ooroq2tLdNj3H333aOpqUmzAIB5IsACAKhhr776ajz44IOZHmOhUIjddttNswCAeSbAAgCoUdVqNc4///xI0zTT49x6661jscUW0zAAYJ4JsAAAatRtt90Wr7/+eubHueeee2oWADBfBFgAADVo7Nix8bvf/S7z41x99dVjrbXW0jAAYL4IsAAAaky1Wo2zzjorpk+fnvmx7rvvvhoGAMw3ARYAQI256aab4vnnn8/8OAcOHBjbbLONhgEA802ABQBQQ4YMGRJXXnllTYz1+9//fuTzeU0DAOabAAsAoEZMnDgxTj311CiXy5kf6/LLL+/sKwCgwwiwAABqQGtra5xwwgkxYcKEmhjvwQcfHLmct5oAQMfwrgIAIOPK5XKcfPLJ8dprr9XEeFdYYYXYeuutNQ4A6DACLACADPvoGwefeOKJmhnz4Ycf7uwrAKBDeWcBAJBR1Wo1zj333Lj//vtrZswbbrhhbLnllpoHAHSoghIAAGRPqVSK008/PR5++OGaGXOxWIwTTjhB8wCADifAAgDImMmTJ8fJJ58czz//fE2Ne5999olll11WAwGADifAAgDIkOHDh8fxxx8fY8aMqalx9+vXLw488EANBAA6hT2wAAAy4p577olDDjmk5sKriIhjjjkmevbsqYkAQKdwBhYAQBebOnVq/PznP4+HHnqoJse/6aabxnbbbaeRAECnEWABAHShBx54IC655JKYOHFiTY6/V69eccopp0SSJJoJAHQaARYAQBcYMWJEXHjhhfHvf/+7pudx4oknxmKLLaahAECnEmABACxAY8eOjeuuuy7uvvvuqFarNT2X7bbbLrbddltNBQA6nQALAGAB+M9//hO33HJLPPjgg1Eul2t+PosttliceOKJGgsALBACLACATjJjxox49NFH4/bbb48XX3yxbuaVy+XijDPOiIUXXliTAYAFQoAFANCB2tra4vnnn4/7778/HnnkkWhpaam7OR5xxBGx4YYbajYAsMAIsAAA5kOapjFy5Mh44YUX4sknn4xnn302Wltb63a+22yzTRxwwAEaDwAsUAIsAIA5VKlU4p133olhw4bFiBEjYujQofHSSy/FtGnTGmL+K620Upx66qmRJInFAAAsUAIsAKCmvffee9G9e/d5vv0nw6dp06ZFa2trTJ069eN/xo8f//E/48aNi1Kp1JB17t27d/ziF7+Yr1oDAMwrARYAUNOOO+44RehkxWIxzjvvvFhyySUVAwDoEjklAABgtm8Wc7k4/fTTY9CgQYoBAHTdexIlAABgdo455pjYbrvtFAIA6FICLAAAZunwww+PffbZRyEAgC4nwAIA4H/sueeeceCBByoEAJAJAiwAAD5lt912ix//+McKAQBkhm8hBADgY3vvvXccd9xxkSSJYgAAmSHAAgAgIiIOOOCAOOqooxQCAMgcARYAQINLkiSOPfZYG7YDAJklwAIAaGDFYjFOO+202H777RUDAMgsARYAQIPq06dPDB48ONZff33FAAAyTYAFANCAVllllbjgggtiwIABigEAZF5OCQAAGsu2224bV111lfAKAKgZzsACAGgQuVwujjzyyNh///0jSRIFAQBqhgALAKABLL744nHmmWfa7woAqEkCLACAOrftttvGT37yk1h44YUVAwCoSQIsAIA61a9fv/jxj38cW2+9tWIAADVNgAUAUGdyuVzstttu8YMf/CB69uypIABAzRNgAQDUkUGDBsWxxx4bK6+8smIAAHVDgAUAUAdWW221OPTQQ2OzzTZTDACg7giwAABq2KqrrhqHHHJIbLHFFpEkiYIAAHVJgAUAUGNyuVxssMEGsffee8fmm28uuAIA6p4ACwCgRvTr1y923HHH+OY3vxnLLLOMggAADUOABQCQYc3NzfHlL385dtxxx9hss80in88rCgDQcARYAAAZ071799h4441jq622ii222CJ69OihKABAQxNgAQBkwMCBA+PLX/5yfPnLX4711lsvmpqaFAUA4L8EWAAAC1g+n48VVlgh1l133Vh33XVjvfXWi379+ikMAMBsCLAAADpR3759Y9lll42BAwfGSiutFGussUasssoq0dzcrDgAAHNIgAUAMB+ampqiT58+0a9fv1hqqaVi4MCBseyyy8YyyywTyy67bCy00EKKBAAwnwRYABmz/PLLZ27vm/79+2sM0b1791httdXqcm49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"/></defs></svg></i></button></div></div></div>');
        });

        if (at.length > 0) {
            t.main_attachments.html('<div><div class="text-bold mar-rgt">Attachments:</div>' + at.join("") + '</div>');
        }
    }

    function decodeURIComponentSafe(uri, mod) {
        var out = new String(),
            arr,
            i = 0,
            l,
            x;
        typeof mod === "undefined" ? mod = 0 : 0;
        arr = uri.split(/(%(?:d0|d1)%.{2})/);
        for (l = arr.length; i < l; i++) {
            try {
                x = decodeURIComponent(arr[i]);
            } catch (e) {
                x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
            }
            out += x;
        }
        return out;
    }
    t.checkRevokeAccess = function () {
        if (config.client == "ltts" || config.client == "rolepermission" || config.client == "grdemo") {
            if ($(this).val() == 5) {
                t.frmUpdateStatus.el.revoke_access_div.removeClass('hide');
            } else {
                t.frmUpdateStatus.el.revoke_access_div.addClass('hide');
                $('#revoke_access').prop('checked', false);
            }
        }
    }
    var select2Opts = { width: "100%" };
    t.toggleSingleTinyViewerHistory = function(e) {
        e.preventDefault();
        var el = $(this).closest('.tiny-view').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewerHistory( el, target_val );
    };
    t.setSingleTinyViewerHistory = function(el, target_val) {
        if( target_val == true ) {
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-compress"></i>').attr('title', 'Compress');
        }
        else {
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-expand"></i>').attr('title', 'Expand');
        }
    };
    t.mdlTktHistory.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewerHistory));
    function ticketPriorityTemplate(option) {
        if (!option.id) return option.text;

        var color = $(option.element).data('color') || '#6b7280';
        var icon = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M2.5 12L12 3L21.5 12H15.5V21H8.5V12H2.5Z" fill="' + color + '" stroke="' + color + '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>' +
            '</svg>';

        return $('<span class="d-flex align-items-center gap-2"><span class="s2-icon d-flex align-items-center justify-content-center">' + icon + '</span><span>' + option.text + '</span></span>');
    }

    t.initUpdateStatusSelects = function () {
        if (typeof $.fn.select2 === "undefined") {
            return;
        }

        var updateStatusSelect2Opts = $.extend({}, select2Opts, {
            dropdownParent: t.frmUpdateStatus,
            placeholder: "Select Status"
        });
        var updatePrioritySelect2Opts = $.extend({}, updateStatusSelect2Opts, {
            placeholder: "Select Priority",
            templateResult: ticketPriorityTemplate,
            templateSelection: ticketPriorityTemplate
        });

        if (t.frmUpdateStatus.el.statusId.hasClass('select2-hidden-accessible')) {
            t.frmUpdateStatus.el.statusId.select2('destroy');
        }

        if (t.frmUpdateStatus.el.priorityId.hasClass('select2-hidden-accessible')) {
            t.frmUpdateStatus.el.priorityId.select2('destroy');
        }

        t.frmUpdateStatus.el.statusId
            .select2(updateStatusSelect2Opts)
            .next('.select2-container')
            .css('display', 'block')
            .end()
            .off('.updateStatusSelect')
            .on("change.updateStatusSelect", $.proxy(t.statusIdChanged))
            .on('select2:select.updateStatusSelect', $.proxy(t.loadRequestForm_onStatus))
            .on('select2:select.updateStatusSelect', $.proxy(t.loadCheckStatusApproval))
            .on("change.updateStatusSelect", $.proxy(t.checkRevokeAccess));

        t.frmUpdateStatus.el.priorityId
            .select2(updatePrioritySelect2Opts)
            .next('.select2-container')
            .css('display', 'block');
    };
    t.initUpdateStatusSelects();
    t.frmAssignTo.el.assigned_to.select2($.extend({}, {
        dropdownParent: $("#mdl-assign-to"),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_avability + "/" + t.data.id,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
        },
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.userDropdownSelectionFormat(data);
        }
    }));

    /* related to change creator */
    t.frmChangeCreator.el.creator_id.select2($.extend({}, { width: "100%" }, {
        dropdownParent: $("#mdl-change-creator"),
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.data.company_id,
                    assigned_to: t.config.data.assigned_to,
                    creator_id: t.config.data.creator_id,
                };
            },
            delay: 300
        },
        allowClear: true,
        // minimumInputLength: 1,
        placeholder: "Enter first few letters of the User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.userDropdownSelectionFormat(data);
        }
    }));

    /* related to transfer ticket */
    t.frmTransfer.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmTransfer.el.department_id.parent(),
        ajax: {
            url: t.config.url.departments_with_company + "/" + t.data.id,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.data.company_id
                };
            },
            delay: 200
        },
        allowClear: true,
        // minimumInputLength: 1,
        placeholder: config.translations.Enter_starting
    })).on("change", $.proxy(t.refillServiceTypes));

    t.frmTransfer.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdlTransfer.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.frmTransfer.el.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && config.client == "ltts") ? true : false,
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_device,
        templateResult: function (s) {
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

    t.frmTransfer.el.tags.select2({
        width: "100%",
        placeholder: "Add Tags",
        tags: true,
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
    var select2Opts = {
        width: "100%",
        placeholder: config.translations.please_select,
        allowClear: true,
    };
    
    t.frmTransfer.el.problem_category_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmTransfer.el.problem_category_id.parent() })).on("change", $.proxy(t.fillSla));
    t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.refillSubCategory));

    t.frmTransfer.el.problem_category_id.on('select2:select', function () {
        t.updateTransferform();
    });
    t.frmTransfer.el.sub_category_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmTransfer.el.sub_category_id.parent() })).on("change", $.proxy(t.fillSla));
    t.frmTransfer.el.sub_category_id.on('select2:select', function () {
        if ($("#ticketTransferModal").find("#request_submit_id").val() == 0) {
            t.updateTransferform();
        }
    });
    t.frmTransfer.el.priority_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmTransfer.el.priority_id.parent() })).on("change", $.proxy(t.refillTat));
    t.frmTransfer.el.assign_to.select2($.extend({}, {
        dropdownParent: t.frmTransfer.el.assign_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id: t.frmTransfer.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id: t.frmTransfer.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.userDropdownSelectionFormat(data);
        }
    })).on('change', t.checkTechnicalAvabilityForTransfer);
    /* end */

    /* related to edit ticket */
    t.frmEdit.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEdit.el.department_id.parent(),
        ajax: {
            url: t.config.url.departments_with_company + "/" + t.data.id,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.data.company_id
                };
            },
            delay: 200
        },
        allowClear: true,
        disabled: 'readonly',
        // minimumInputLength: 1,
        placeholder: config.translations.Enter_starting
    })).on("change", $.proxy(t.refillServiceTypes));

    t.frmEdit.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEdit.el.device_id.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.original_data.creator_id,
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && config.client == "ltts") ? true : false,
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_device,
        templateResult: function (s) {
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

    t.frmEdit.el.tags.select2({
        width: "100%",
        placeholder: "Add Tags",
        tags: true,
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
    var select2Opts = { width: "100%" };
    t.frmEdit.el.problem_category_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmEdit.el.problem_category_id.parent() })).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEdit.el.problem_category_id.on('select2:select', function () {
        t.updateEditTicketform();
    });
    t.frmEdit.el.problem_category_id.on("change", $.proxy(t.refillSubCategoryForEdit));
    t.frmEdit.el.sub_category_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmEdit.el.sub_category_id.parent() })).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEdit.el.sub_category_id.on('select2:select', function () {
        if ($("#editTicketModal").find("#request_submit_id").val() == 0) {
            t.updateEditTicketform();
        }
    });
    t.frmEdit.el.priority_id.select2($.extend({}, select2Opts, { dropdownParent: t.frmEdit.el.priority_id.parent() })).on("change", $.proxy(t.refillTatForEdit));
    t.frmEdit.el.assign_to.select2($.extend({}, {
        dropdownParent: t.frmEdit.el.assign_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id: t.frmEdit.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id: t.frmEdit.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.userDropdownSelectionFormat(data);
        }
    })).on('change', t.checkTechnicalAvabilityForEdit);
    /* end */

    t.initUpdateForm(true);
    t.frmUpdateStatus.on("submit", $.proxy(t.frmUpdateStatusSubmit));
    t.frmAddCalender.on("submit", $.proxy(t.frmAddCalenderSubmit));
    t.frmUpdateStatus.btnSubmit.on("click", $.proxy(t.frmUpdateStatusSubmit));
    t.frmComment.on("submit", $.proxy(t.frmCommentSubmit));
    t.frmAssignTo.on("submit", $.proxy(t.assignTo));
    t.mdladdProblem.on("submit", $.proxy(t.addImpactTicket));
    t.mdladdIncident.btnSubmit.on("click", $.proxy(t.submitIncident));
    t.frmTransfer.on("submit", $.proxy(t.frmTransferSubmit));
    t.frmTransfer.el.btnSubmit.on("click", $.proxy(t.frmTransferSubmit));
    t.frmFeedback.on("submit", $.proxy(t.frmFeedbackSubmit));
    t.frmReopen.on("submit", $.proxy(t.frmReopenSubmit));
    t.frmChangeCreator.on("submit", $.proxy(t.frmChangeCreatorSubmit));
    t.frmEdit.on("submit", $.proxy(t.frmEditSubmit));
      t.frmEdit.el.btnSubmit.on("click", $.proxy(t.frmEditSubmit));
    // t.frmEdit.el.department_id.on("change", $.proxy(t.refillProblemCategoryForEdit));
    t.refreshTimeLine();
    t.refreshExpireInfo();
    t.refreshworkAroundInfo();
    t.refreshResponseInfo();
    t.configFun();
    t.setCustomFieldForm();
    t.page.on("click", ".js-act-delete", $.proxy(t.deleteTicket));
    t.page.on("click", ".js-act-assign-to", $.proxy(t.openMdlAssignTo));
    t.page.on("click", ".js-act-tkt-history", $.proxy(t.openMdlTktHistory));
    t.page.on("click", ".js-act-tkt-book-calendar", $.proxy(t.openMdlTktBookCalendar));
    t.page.on("click", "#add-block-calendar", $.proxy(t.openMdlTktAddBlockCalendar));
    t.page.on("click", ".js-act-self-assign", $.proxy(t.selfAssign));
    t.page.on("click", ".add-problem-mgt", $.proxy(t.addProblemMgt));
    t.page.on("click", ".add-incident", $.proxy(t.addIncident));
    t.page.on("click", ".js-act-transfer", $.proxy(t.loadTransfer));
    t.page.on("click", ".js-act-edit-feedback", $.proxy(t.openFeedbackModal));
    t.page.on("click", ".js-act-update", $.proxy(t.updateFeedback));
    t.page.on("click", ".js-act-staring", $.proxy(t.staring));
    t.page.on("click", ".sentiment-button", $.proxy(t.Ticketsentiment));
    t.page.on("click", ".js-act-spam", $.proxy(t.spamming));
    t.page.on("click", ".js-act-reopen", $.proxy(t.openReopenModal));
    t.page.on("click", ".js-act-go-back", $.proxy(t.goBack));
    t.page.on("click", ".js-act-change-creator", $.proxy(t.openMdlChangeCreator));
    t.toggle_tiny_view.on("click", $.proxy(t.toggleTicketSummary));
    t.toggle_ticket_detail.on('click', t.toggleTicketDetail);
    t.toggle_conversation_view.on("click", $.proxy(t.toggleTinyViewAll));
    t.toggle_tiny_view_read.on("click", $.proxy(t.toggleTinyViewAllRead));
    t.actionMore.on("click", $.proxy(t.toggleActionOverflow));
    t.page.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    t.page.on("click", ".js-act-edit-ticket", $.proxy(t.loadEditTcket));

    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    t.main_attachments.on("click", ".tri-download", $.proxy(t.attachmentDownload));

    /* format email address */
    var tktEmail = new TktEmail();
    t.frmComment.el.cc_emails.on("blur", $.proxy(tktEmail.formatEmailsAdapter));
    t.frmUpdateStatus.el.cc_emails.on("blur", $.proxy(tktEmail.formatEmailsAdapter));
    t.initUpdateStatusSummernote();
    t.frmAddCalender.el.description.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true
    });
    t.frmAddEventComment.el.comment.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true
    });

    t.frmCommentTokenize();
    t.commentUploader = t.setupTicketDetailUploader(t.frmComment.attachment_dropper_cover);
    t.updateStatusUploader = t.setupTicketDetailUploader(t.frmUpdateStatus.attachment_dropper_cover);
    if (!window.AMGDragDropUploader) {
    var totalCommentFileSize = 0;
    var uploadedCommentFiles = [];
    t.frmComment.attachment_dropper_cover.filedrop({
        fallback_id: "attachment_input",
        fallback_dropzoneClick: false,
        url: t.config.url.attachment_add,
        paramname: "attachment",
        data: {
            "_token": t.config.token,
            "ticket_id": function () { return t.data.id; },
            "tmp_id": function () { return t.frmComment.el.tmp_id.val() }
        },
        maxfiles: 5,
        maxfilesize: 10,
        error: function (err, file) {
            switch (err) {
                case 'TooManyFiles':
                    var data = {
                        'msg': config.translations.upload_file,
                    };
                    sweetAlert('center', 'error', data);
                    break;
                case 'FileTooLarge':
                    var data = {
                        'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.',
                    };
                    sweetAlert('center', 'error', data);
                    break;
                default:
                    break;
            }
        },
        uploadFinished: function (i, file, response, time) {
            if (response.status === "success") {
                t.attachment.find("#attach" + i + " .name").text(decodeURIComponent(response.data.original_file_name));
                t.attachment.find("#attach" + i).attr("data-id", response.data.id);
                t.attachment.find("#attach" + i + " .progress").fadeOut("slow");
            } else {
                t.attachment.find("#attach" + i + " .name").text(file.name + " upload failed");
                t.attachment.find("#attach" + i + " .upload_length").addClass("progress-bar-danger");
                sweetAlert('center', 'error', response);
            }
        },
        progressUpdated: function (i, file, progress) {
            t.attachment.find("#attach" + i + " .upload_length").css("width", progress + "%");
            // $(".note-editor").removeClass("dragover");
        },
        beforeSend: function (file, i, done) {
            var matched = $('.count_img');

            if (uploadedCommentFiles.includes(file.name)) {
                sweetAlert('center', 'error', { 'msg': 'This file has been already uploaded.' });
                return;
            }
            uploadedCommentFiles.push(file.name);
            const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.xls', '.xlsx', '.doc', '.docx', '.ppt', '.pdf', '.txt', '.msg', '.zip', '.psd', '.csv', '.eml','.mp4'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if ($.inArray(fileExtension, allowedExtensions) === -1) {
                sweetAlert('center', 'error', { 'msg': 'This file type is not allowed. Please upload a valid file.' });
                return;
            }
            if (matched.length < 5) {
                if (file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return;
                }
                if (totalCommentFileSize + file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'Total File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return;
                }

                totalCommentFileSize += file.size;
                let fileSizeKB = Math.ceil(file.size / 1024);
                let fileNameWithSize = file.name + ' [' + fileSizeKB + ' KB]';

                // $(".note-editor").removeClass("dragover");
                if (t.attachment.find("#attach" + i).length) {
                    t.attachment.find("#attach" + i).attr("id", "attach" + (Math.random().toString()).substring(2, 15));
                }
                t.attachment.append('<div id="attach' + i + '" class="attach pad-top count_img" data-size="' + file.size + '"><div class="bord-btm clearfix"><p class="pull-left">' + fileNameWithSize + '</p><span style="cursor:pointer" class="remove-attach pull-right" data-size="' + file.size + '"><i class="fa fa-remove"></i> Remove</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div></div>');
                done();
            } else {
                var data = {
                    'msg': config.translations.upload_file,
                };
                sweetAlert('center', 'error', data);
            }
        },
        dragOver: function () {
            t.frmComment.attachment_dropper.show();
        },
        drop: function () {
            t.frmComment.attachment_dropper.show();
            $(".note-editor.panel-default").removeClass('dragover');
        }
    });

    var totalUpdateStatusFileSize = 0;
    var uploadedUpdateStatusFiles = [];
    t.frmUpdateStatus.attachment_dropper_cover.filedrop({
        fallback_id: "update_attachment_input",
        fallback_dropzoneClick: false,
        url: t.config.url.attachment_add,
        paramname: "update_attachments",
        data: {
            "_token": t.config.token,
            "ticket_id": function () { return t.data.id; },
            "tmp_id": function () { return t.frmUpdateStatus.el.tmp_id.val() }
        },
        maxfiles: 5,
        maxfilesize: 10,
        error: function (err, file) {
            switch (err) {
                case 'TooManyFiles':
                    var data = {
                        'msg': config.translations.upload_file,
                    };
                    sweetAlert('center', 'error', data);
                    break;
                case 'FileTooLarge':
                    var data = {
                        'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.',
                    };
                    sweetAlert('center', 'error', data);
                    break;
                default:
                    break;
            }
        },
        uploadFinished: function (i, file, response, time) {
            if (response.status === "success") {
                t.attachment_update.find("#attachs" + i + " .name").text(decodeURIComponent(response.data.original_file_name));
                t.attachment_update.find("#attachs" + i).attr("data-id", response.data.id);
                t.attachment_update.find("#attachs" + i + " .progress").fadeOut("slow");
            } else {
                t.attachment_update.find("#attachs" + i + " .name").text(file.name + " upload failed");
                t.attachment_update.find("#attachs" + i + " .upload_length").addClass("progress-bar-danger");
            }
        },
        progressUpdated: function (i, file, progress) {
            t.attachment_update.find("#attachs" + i + " .upload_length").css("width", progress + "%");
        },
        beforeSend: function (file, i, done) {
            var matched = $('.count_img');
            if (uploadedUpdateStatusFiles.includes(file.name)) {
                sweetAlert('center', 'error', { 'msg': 'This file has been already uploaded.' });
                return;
            }
            uploadedUpdateStatusFiles.push(file.name);
            const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.xls', '.xlsx', '.doc', '.docx', '.ppt', '.pdf', '.txt', '.msg', '.zip', '.psd', '.csv', '.eml'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if ($.inArray(fileExtension, allowedExtensions) === -1) {
                sweetAlert('center', 'error', { 'msg': 'This file type is not allowed. Please upload a valid file.' });
                return;
            }
            if (matched.length < 5) {
                if (file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return;
                }
                if (totalUpdateStatusFileSize + file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'Total File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return;
                }

                totalUpdateStatusFileSize += file.size;
                let fileSizeKB = Math.ceil(file.size / 1024);
                let fileNameWithSize = file.name + ' [' + fileSizeKB + ' KB]';

                if (t.attachment_update.find("#attachs" + i).length) {
                    t.attachment_update.find("#attachs" + i).attr("id", "attachs" + (Math.random().toString()).substring(2, 15));
                }
                t.attachment_update.append('<div id="attachs' + i + '" class="attachs pad-top count_img" data-size="' + file.size + '"><div class="bord-btm clearfix"><p class="pull-left">' + fileNameWithSize + '</p><span style="cursor:pointer" class="remove-attach pull-right" data-size="' + file.size + '"><i class="fa fa-remove"></i> Remove</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div></div>');
                done();
            } else {
                var data = {
                    'msg': config.translations.upload_file,
                };
                sweetAlert('center', 'error', data);
            }
        },
        dragOver: function () {
            t.frmUpdateStatus.attachment_dropper.show();
        },
        drop: function () {
            t.frmUpdateStatus.attachment_dropper.show();
            $(".note-editor.panel-default").removeClass('dragover');
        }
    });

    }

    t.frmComment.el.comment.summernote(t.getSummernoteOptions({
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['misc', [t.config.aiAsssistEnabled != "" ? 'aiAssist' : '']]
        ],
        buttons: {
            aiAssist: function (context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: '<img src="' + t.config.url.matiImage + '" alt="icon" style="width:20px; height:20px; margin-right:5px;"> <span class="ai-assist-text">AI Assist</span>',
                    className: 'ai-assist-btn',
                    tooltip: 'Search AI Assistance',
                    click: function () {
                        $('#aiModal').modal('show');
                        $('#aiModal').attr("data-type", "ticket").attr("data-title", "#tkt-title").attr("data-desc", "#tkt-content").attr("data-target", "#comment");
                    }
                });
                return button.render();
            }
        },
        callbacks: {
            onInit: function () {
                t.runSummernoteBaseInit();
                var replyWrapper = $('#reply-summernote-wrapper');
                if (replyWrapper.length > 0) {
                    replyWrapper.find('.note-editor.note-frame').append($('#reply-custom-toolbar'));
                    replyWrapper.addClass('sn-ready');
                }
            }
        },
        minHeight: 200,
        focus: false
    }));

    $('#aiCloseBtn').on('click', function () {
        $('#aiModal').modal('hide');
    });

    $('#aiSearchBtn').on('click', function () {
        let $btn = $(this);
        $btn.val("Generating Response").attr('disabled', true);

        let query = $('#aiQuery').val();
        if (!query) {
            alert("Please enter a query.");
            $btn.val("Generate Response").attr('disabled', false);
            return;
        }

        let targetSelector =  $('#aiModal').attr("data-target");
        if (!targetSelector || !$(targetSelector).length) {
            alert("Target editor not found.");
            $btn.val("Generate Response").attr('disabled', false);
            return;
        }

        $.ajax({
            type: "POST",
            url: t.config.url.geminiUrl,
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept-Language": "en_US" // Set a valid locale
            },
            data: JSON.stringify({ query: query }),
            success: function (response) {
                if (response && response.candidates && response.candidates.length > 0) {
                    let aiText = response.candidates[0].content.parts[0].text;
                    let converter = new showdown.Converter();
                    let formattedText = converter.makeHtml(aiText);
                    let existingContent = $(targetSelector).summernote('code');
                    $(targetSelector).summernote('code', existingContent + `<p>${formattedText}</p>`);
                } else {
                    alert("No AI response received.");
                }

                $('#aiQuery').val("");
                $('#aiModal').modal('hide');
            },
            error: function (data) {
                console.log("Error:", data.responseText);
                alert("AI search failed. Try again.");
            },
            complete: function () {
                $btn.val("Generate Response").attr('disabled', false);
            }
        });
    });

    $("#customResponseBtn").click(function () {
        $("#customResponseWrap").show();
    });
    $('#aiModal').on('show.bs.modal', function () {
        $("#customResponseWrap").hide();
    });

    $(document).on("click", ".play-pause-btn", function () {
        const audioUrl = $(this).data("url");
        const playIcon = $(this).find(".play-icon");
        const pauseIcon = $(this).find(".pause-icon");
        $("audio").each(function () {
            this.pause();
            this.currentTime = 0;
        });
        $(".play-pause-btn .play-icon").show();
        $(".play-pause-btn .pause-icon").hide();
        if (!this.audio) {
            this.audio = new Audio(audioUrl);
            this.audio.addEventListener("ended", () => {
                playIcon.show();
                pauseIcon.hide();
            });
        }
        if (this.audio.paused) {
            this.audio.play();
            playIcon.hide();
            pauseIcon.show();
        } else {
            this.audio.pause();
            playIcon.show();
            pauseIcon.hide();
        }
    });

    $("#autoResponseBtn").click(function () {
        let $btn = $("#autoResponseBtn");
        $btn.val("Generating...").attr("disabled", true);

        let $modal = $('#aiModal');
        let type = $modal.attr("data-type");
        let titleSelector = $modal.attr("data-title");
        let descSelector = $modal.attr("data-desc");
        let targetSelector = $modal.attr("data-target");

        let query = '';
        if (type === "task") {
            let taskTitle = sanitizeHtml(titleSelector.trim());
            query = `Provide a precise and actionable response to the following:\nSubject: ${taskTitle}\nEnsure the response is direct, specific, and avoids general suggestions.`;

        } else if (type === "ticket") {
            let ticketTitle = sanitizeHtml($(titleSelector).html().trim());
            let ticketDesc = sanitizeHtml($(descSelector).html().trim());
            query = `Provide a precise and actionable response to the following:\nSubject: ${ticketTitle}\nDescription: ${ticketDesc}\nEnsure the response is direct, specific, and avoids general suggestions.`;
        }

        if (query.trim() === "") {
            alert("Please enter a query first.");
            $btn.val("Auto Response").attr("disabled", false); // Re-enable the button if no query
            return;
        }
        $.ajax({
            type: "POST",
            url: t.config.url.geminiUrl,
            contentType: "application/json",
            dataType: "json",
            headers: {
                "Accept-Language": "en_US"
            },
            data: JSON.stringify({ query: query }),
            success: function (response) {
                if (response && response.candidates && response.candidates.length > 0) {
                    let aiText = response.candidates[0].content.parts[0].text;
                    let converter = new showdown.Converter();
                    let formattedText = converter.makeHtml(aiText);
                    let existingContent = $(targetSelector).summernote('code');
                    $(targetSelector).summernote('code', existingContent + `<p>${formattedText}</p>`);
                } else {
                    alert("No AI response received.");
                }

                $('#aiQuery').val("");
                $('#aiModal').modal('hide');
            },
            error: function (data) {
                console.log("Error:", data.responseText);
                alert("AI search failed. Try again.");
            },
            complete: function () {
                $btn.val("Auto Response").attr("disabled", false);
            }
        });
    });

    function sanitizeHtml(html) {
        return html.replace(/<\/?[^>]+(>|$)/g, "").trim(); // Removes all HTML tags
    }

    function getTruncateLength() {
        if (window.matchMedia('(max-width: 320px)').matches) {
            return 10;
        } else if (window.matchMedia('(max-width: 480px)').matches) {
            return 15;
        } else if (window.matchMedia('(max-width: 768px)').matches) {
            return 20;
        } else if (window.matchMedia('(max-width: 1024px)').matches) {
            return 25;
        } else {
            return 25;
        }
    }

    var truncateLength = getTruncateLength();

    $('.truncate-text').each(function () {
        var $element = $(this);
        var fullText = $element.text().trim();
        var iconHtml = '';

        if (fullText.includes('E-mail')) {
            iconHtml = '<i class="fa fa-envelope" aria-hidden="true"></i>';
        } else if (fullText.includes('Chat')) {
            iconHtml = '<i class="fa fa-commenting" aria-hidden="true"></i>';
        } else if (fullText.includes('Mobile')) {
            iconHtml = '<i class="fa fa-mobile" aria-hidden="true"></i>';
        } else if (fullText.includes('Call')) {
            iconHtml = '<i class="fa fa-phone-square" aria-hidden="true"></i>';
        } else if (fullText.includes('Portal')) {
            iconHtml = '<i class="fa fa-desktop" aria-hidden="true"></i>';
        } else if (fullText.includes('BOT')) {
            iconHtml = '<i class="fa fa-cogs" aria-hidden="true"></i>';
        }

        fullText = fullText.replace(/\s+/g, ' ').trim();

        if (fullText.length > truncateLength) {
            var truncatedText = fullText.substring(0, truncateLength) + '...';
            $element.html(iconHtml + ' ' + truncatedText);
            $element.attr('title', fullText);
        } else {
            $element.html(iconHtml + ' ' + fullText);
            $element.attr('title', fullText);
        }
    })

    $('[data-toggle="tooltip"]').tooltip();
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
    function syncTooltipState() {
        document.querySelectorAll('.tooltip.show').forEach(tooltipEl => {
            const triggerEl = document.querySelector(`[aria-describedby="${tooltipEl.id}"]`);
            if (!triggerEl || !triggerEl.matches(':hover')) {
                const tt = triggerEl ? bootstrap.Tooltip.getInstance(triggerEl) : null;
                if (tt) tt.hide();
                else tooltipEl.remove();
            }
        });
    }
    document.addEventListener('mousemove', syncTooltipState);
    document.addEventListener('swal2:didClose', syncTooltipState);
    const swalObserver = new MutationObserver(() => {
        if (!document.querySelector('.swal2-container')) {
            syncTooltipState();
        }
    });
    swalObserver.observe(document.body, { childList: true });

    if (!window.AMGDragDropUploader) {
        t.frmComment.on("click", "#manual_file_trigger, .js-manual-file-trigger", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $("#attachment_input").trigger("click");
        });
        t.frmComment.on("click", ".remove-attach", $.proxy(t.attachment.removeAttach));
        t.frmUpdateStatus.on("click", "#update_file_trigger", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $("#update_attachment_input").trigger("click");
        });
        t.frmUpdateStatus.on("click", ".remove-attach", $.proxy(t.attachment_update.removeAttach));
    }
    t.frmUpdateTaskStatus.on("click", "#task_file_triggers", function (e) {
        e.preventDefault();
    });
    t.frmUpdateTaskStatus.on("click", ".remove-attach", $.proxy(t.frmUpdateTaskStatus.el.attachment_updates.removeAttach));

    t.setMainAttachments();

    setInterval(function () {
        if (![5, 6].includes(t.data.status_id)) {
            t.checkNewTimeline();
        }
    }, 30000);

    let lastTimelineId = null;
    let isChecking = false;


    t.checkNewTimeline = function () {
        if (isChecking) return;
        isChecking = true;
        var url = t.config.url.get_timeline;
        $.ajax({
            url: url,
            type: "POST",
            data: { _token: t.config.token, id: t.data.id, per_page: 1, page: 1 },
            success: function (res) {
                if (res.status === "success") {
                    if (lastTimelineId === null) {
                        lastTimelineId = res.latest_timeline_id;
                    } else if (res.latest_timeline_id !== lastTimelineId) {
                        lastTimelineId = res.latest_timeline_id;
                        t.refreshTimeLine();
                    }
                }
            },
            complete: function () {
                isChecking = false;
            }
        });
    };

    t.cc_master = new CcMaster(config);
    t.cc_master.visibilityDecision();
    const validClients = ["ltts", "rolepermission", "grdemo", "ltsct"];
    const validSubClients = ["admin"];
    if (config.form_type == 2 && validClients.includes(config.client) && validSubClients.includes(config.sub_client)) {
        $.ajax({
            url: t.config.url.requested_custom_form + "/" + config.form_id,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status == "success") {
                    var data = response.data;
                    $("#item-table").empty();
                    let newRowtable =
                        `<div class="form-group">
                        <h4>Request Item</h4>
                        <form name="custom_requested_form" id="custom_requested_form" method="post" action="#" class="form-horizontal">
                            <div class="col-md-12">
                                <div class="row">`;
                                    if(t.config.client != "ltsct") {
                                        newRowtable += `<div class="form-group col-md-6">
                                            <label class="control-label" for="Cost Center">Cost Center</label>
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-sort-numeric-asc"></i></span>
                                                    <input type="text" id="cost_center" name="cost_center" class="form-control cost_center" placeholder="Enter Cost Center" value="${data.field_values.cost_center || ''}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label class="control-label mandatory" for="WBS">WBS</label>
                                            <div class="col-md-12">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-building-o"></i></span>
                                                    <input type="text" id="wbs_center" name="wbs_center" class="form-control wbs_center" placeholder="Enter WBS" required value="${data.field_values.wbs_center || ''}">
                                                </div>
                                                <span id="wbs_error" class="text-danger d-block mt-1"></span>
                                            </div>
                                        </div>`;
                                    }
                            newRowtable +=`</div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="row">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="30%">Category <span class="text-danger">*</span></th>
                                                <th width="30%">Item <span class="text-danger">*</span></th>
                                                <th width="25%">Quantity<span class="text-danger">*</span></th>
                                                <th width="15%">Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="repeater-container-ticket">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right">
                                    <button type="button" id="submit-request-items" class="btn btn-primary">Submit / Issue Item</button>
                                </div>
                            </div>
                        </form>
                    </div>`;
                    $("#item-table").append(newRowtable);
                    $("#repeater-container-ticket").empty();
                    for (let i = 0; i < data.field_values.category.length; i++) {
                        let newRow = `
                        <tr class="entry">
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-list-alt"></i></span>
                                    <select class="form-control category_id select2" id="category_id_${i}" name="category_id[]" style="width: 100%;" disabled required data-toggle='tooltip' data-placement='bottom' data-original-title="${data.field_values.category[i] || 'No category selected'}">
                                        <option value="${data.field_values.category_id[i]}" selected>${data.field_values.category[i]}</option>
                                    </select>
                                    <input type="hidden" class="selected-category-name" id="category_${i}" name="category[]" value="${data.field_values.category[i]}" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                                    <select class="form-control item select2" id="item_${i}" style="width: 100%;" disabled required data-toggle='tooltip' data-placement='bottom' data-original-title="${data.field_values.item[i] || 'No category selected'}">
                                        <option value="${data.field_values.item_id[i] || ''}" selected>${data.field_values.item[i] || ''}</option>
                                    </select>
                                     <input type="hidden" name="item_id[]" value="${data.field_values.item_id[i] || ''}">
                                    <input type="hidden" class="selected-item-img" id="item_img_${i}" name="item_img[]" value="${data.field_values.item_img[i] || ''}">
                                    <input type="hidden" class="selected-item-name" id="item_name_${i}" name="item[]" value="${data.field_values.item[i] || ''}" required>
                                </div>
                                <span id="available_quantity_${i}" class="available-quantity hide text-center" style="color:#c63932;"></span>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon fa fa-shield"></i></span>
                                    <input type="number" class="form-control quantity" name="quantity[]" min="1" id="quantity_${i}" placeholder="Qty" value="${data.field_values.quantity[i] || 0}" style="width: 100%;" required>
                                </div>
                                <div class="one_line_dis hide" id="one_line_dis_${i}">
                                    <i class="fa fa-exclamation-triangle warning-icon"></i>
                                    <span class="warning-text" style="color:#ffc107;">You entered more than available item</span>
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-check-circle-o"></i></span>
                                    <input type="text" class="form-control remarks" name="remarks[]" id="remarks_${i}" placeholder="Remarks" value="${data.field_values.remarks[i] || ''}" disabled data-toggle='tooltip' data-placement='bottom' data-original-title="${data.field_values.remarks[i] || 'No remark'}">
                                    <input type="hidden" class="selected-type-name" id="item_type_${i}" name="item_type[]" value="${data.field_values.item_type[i] || ''}">
                                </div>
                            </td>
                            <td class="btn-addnew">
                                <button type="button" class="btn btn-primary remove-row" style="min-width: 0px;">
                                    <i class="glyphicon glyphicon-minus"></i>
                                </button>
                            </td>
                        </tr>`;
                        $("#repeater-container-ticket").append(newRow);
                        $("#repeater-container-ticket").find(`#quantity_${i}`).prop('disabled', config.data.status_id === 5 || config.data.status_id === 6 || t.config.data.assigned_to != t.config.user.id);
                        $("#repeater-container-ticket").find(`.remove-row`).toggleClass("hide", config.data.status_id === 5 || config.data.status_id === 6 || t.config.data.assigned_to != t.config.user.id);
                        $("#item-table").find(`#submit-request-items`).toggleClass("hide", config.data.status_id === 5 || config.data.status_id === 6 || t.config.data.assigned_to != t.config.user.id);
                    }
                    $(".item").each(function () {
                        let prevSelectCat = $(this).closest(".input-group").parent().prev().find(".category_id").attr("id");
                        let prevSelectItem = $(this).closest(".input-group").parent().find(".item").attr("id");
                        let selectedValue = $(this).val();
                        if (selectedValue) {
                            t.checkAvailableQnt(
                                parseInt($.trim($("#repeater-container-ticket").find("#" + prevSelectItem).val())),
                                parseInt($.trim($("#repeater-container-ticket").find("#" + prevSelectCat).val())),
                                prevSelectItem.split("_")[1],
                                selectedValue
                            );
                        }
                    });
                }
            },
            error: function () {
                var data = {
                    'msg': 'Something went wrong. Please refresh page and try again'
                }
                sweetAlert('center', 'error', data);
            }
        });
    }

    $(document).on('blur', '.quantity', function () {
        $("#submit-request-items").prop('disabled', true);
        let $row = $(this).closest("tr");
        let prevSelectCat = $row.find(".category_id").attr("id");
        let prevSelectItem = $row.find(".item").attr("id");

        if (prevSelectCat && prevSelectItem) {
            let categoryVal = parseInt($.trim($("#" + prevSelectCat).val())) || 0;
            let itemVal = parseInt($.trim($("#" + prevSelectItem).val())) || 0;
            let quantityVal = parseInt($(this).val()) || 0;
            t.checkAvailableQnt(itemVal, categoryVal, prevSelectItem.split("_")[1], quantityVal, 'quantity');
        }
    });

    $(document).on("click", "#submit-request-items", function () {
        let isValid = true;
        if(t.config.client != "ltsct") {
            let wbs_center = $("#item-table").find(".wbs_center").val();
            let inputGroup = $("#item-table").find(".wbs_center").closest(".input-group");
            let errorLocationMessage = inputGroup.next(".error-message");
            if (!wbs_center || wbs_center === "null" || wbs_center.trim() === "") {
                isValid = false;
                $("#item-table").find(".wbs_center").addClass("is-invalid");
                if (errorLocationMessage.length === 0) {
                    inputGroup.after('<div class="error-message text-danger">This field is required</div>');
                }
            } else {
                $("#item-table").find(".wbs_center").removeClass("is-invalid");
                inputGroup.next(".error-message").remove();
            }
        }
        $("#repeater-container-ticket .entry").each(function () {
            $(this).find("select, input").each(function () {
                let value = $(this).val();
                let parentDiv = $(this).closest("td");
                let errorMessage = parentDiv.find(".error-message");
                if ($(this).hasClass("quantity")) {
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
            });
        });

        if (!isValid) {
            return false;
        }

        var formData = new FormData($('#custom_requested_form')[0]);
        formData.append("form_id", config.form_id);
        formData.append("ticket_id", config.data.id);
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
        $.ajax({
            url: t.config.url.requested_custom_formqty,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.status == "success") {
                    if(t.config.client == "ltts" && t.config.sub_client == "admin") {
                        t.frmUpdateStatus.el.comment.val('').summernote('code', 'Requested Item issued successfully');
                        t.frmUpdateStatus.el.statusId.val(5).trigger('change');
                        $('#custom_field_data_section').append('<div id="submit_via_item_update"><input type="hidden" name="submit_via_item_update" value="1"></div>');
                        t.frmUpdateStatus.btnSubmit.click();
                    } else {
                        sweetAlert('center', 'success', response);
                    }
                } else {
                    sweetAlert('center', 'error', response);
                }
            },
            error: function () {
                var data = {
                    'msg': 'Something went wrong. Please refresh page and try again'
                }
                sweetAlert('center', 'error', data);
            }
        });
    });

    t.checkAvailableQnt = function (item, categoryVal, nexSelectId, val, itemType) {
        if (item > 0) {
            // if (itemType === 'quantity' && (val === 0 || val === '' || val === null)) {
            //     Swal.fire({
            //         title: "Invalid Quantity",
            //         text: "Quantity must be greater than zero.",
            //         icon: "warning",
            //         confirmButtonText: "OK"
            //     }).then(() => {
            //         $('#quantity_' + nexSelectId).val('');
            //         $("#submit-request-items").prop('disabled', false);
            //     });
            //     return; // Stop the function
            // }
            $.ajax({
                url: t.config.url.fetchAvailable,
                type: "POST",
                data: { item: item, category: categoryVal },
                success: function (data) {
                    let errorDiv = $('#one_line_dis_' + nexSelectId);
                    let remarksInput = $('#remarks' + nexSelectId);
                    let quantityDisplay = $('#available_quantity_' + nexSelectId);

                    if (data.data !== undefined) {
                        quantityDisplay.text("Available Quantity: " + data.data).removeClass('hide');
                    } else {
                        quantityDisplay.text("Available Quantity: 0").removeClass('hide');
                    }
                    $('#item_type_' + nexSelectId).val(data.type);
                    if (data.data < val && itemType == 'quantity') {
                        errorDiv.removeClass('hide');
                        Swal.fire({
                            title: "More Item!",
                            text: "You can not entered more than available item.",
                            icon: "warning",
                            confirmButtonText: "OK"
                        }).then(() => {
                            $('#quantity_' + nexSelectId).val('');
                            errorDiv.addClass('hide');
                            $("#submit-request-items").prop('disabled', false);
                        });
                    } else {
                        errorDiv.addClass('hide');
                        remarksInput.prop('required', false);
                        remarksInput.removeClass('error');
                        $("#submit-request-items").prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
    };

    $(document).on("click", ".remove-row", function () {
        let row = $(this).closest("tr");
        let itemId = row.find(".item").val(); // Selected row ka item ID
        let cateId = row.find(".category_id").val(); // Selected row ka Category ID
        let form_id = config.form_id;
        let ticket_id = config.data.id;

        if ($("#repeater-container-ticket tr").length > 1) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you really want to remove this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, remove it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: config.url.remove_table_row,
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr("content"),
                            item_id: itemId, // Item ID backend me bhejna
                            form_id: form_id,
                            category_id: cateId,
                            ticket_id: ticket_id,
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                row.remove(); // Remove row from UI
                                Swal.fire("Deleted!", "The item has been removed.", "success");
                            } else {
                                Swal.fire("Error!", "Failed to delete the item.", "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Oops!", "Something went wrong!", "error");
                        },
                    });
                }
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'At least one row is required!',
                text: 'You cannot remove the last remaining row.',
            });
        }
    });

    function generateChangeHistoryTable(changeInfo) {
        try {
            let changeData = JSON.parse(changeInfo);
            if (changeData.hasOwnProperty("category") && Array.isArray(changeData.category)) {
                let html = `
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                changeData.category.forEach((category, index) => {
                    let item = changeData.item?.[index] || "N/A";
                    let quantity = changeData.quantity?.[index] || "N/A";
                    let remarks = changeData.remarks?.[index] || "N/A";
                    // let image = changeData.item_img?.[index]
                    //     ? `<a href="#" data-toggle="modal" data-target="#imageModal" data-image="${changeData.item_img[index]}">
                    //             <img src="${changeData.item_img[index]}" alt="Item Image" width="50" height="50">
                    //        </a>`
                    //     : "N/A";

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${category}</td>
                            <td>${item}</td>
                            <td>${quantity}</td>
                            <td>${remarks}</td>
                        </tr>
                    `;
                });

                html += "</tbody></table>";

                return html;
            } else {
                return "<p>No valid data found</p>";
            }
        } catch (error) {
            return "<p>Invalid JSON format</p>";
        }
    }

    /* Sentimate Modal Open */

   const MODAL = '#modalBackdrop';

    function showModal() {
        $(MODAL).fadeIn(200);
    }

    function closeModal() {
        $(MODAL).fadeOut(200);
    }

    // Open modal
    $(document).on('click', '.sentiment-icon, .open-modal', function (e) {
        e.preventDefault();
        showModal();
    });

    // Close modal
    $(document).on('click', '.close-sentimate, .btn-close, .close-btn', function (e) {
        e.preventDefault();
        closeModal();
    });

    // Click outside to close
    $(document).on('click', MODAL, function (e) {
        if (e.target.id === 'modalBackdrop') closeModal();
    });

    // ESC to close
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    $(document).on('click', '#backBtnTicket', function () {
        var backUrl = localStorage.getItem('ticketBackUrl');
        if (backUrl) {
            localStorage.removeItem('ticketBackUrl');
            window.location.href = backUrl;
        }
    });

};
