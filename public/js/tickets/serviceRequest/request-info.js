var TicketRequestInfo = function (config) {
    var t = this;
    t.config = config;
    t.page = $("#page_boxed");
    t.content = $('.sr-detail-wrapper');
    t.timeline = t.content.find("#ticket_timeline");
    t.main_attachments = t.content.find('.main_attachments');

    t.frmAproval = t.content.find("#decide_on_request");
    t.frmAproval.approve_status = t.frmAproval.find('#approve_status');
    t.frmAproval.comments = t.frmAproval.find('#comments');
    t.frmAproval.button = t.frmAproval.find('#approve-button');

    t.reqCommentForm = t.content.find('#frmReqComment');
    t.reqCommentForm.el = {};
    t.reqCommentForm.el.id = t.reqCommentForm.find("#id");
    t.reqCommentForm.el.tmp_id = t.reqCommentForm.find("#tmp_id");
    t.reqCommentForm.el.comment = t.reqCommentForm.find("#comment");
    t.reqCommentForm.el.is_note = t.reqCommentForm.find("#is_note");
    t.reqCommentForm.el.comment_cc = t.reqCommentForm.find('.comment_cc');
    t.reqCommentForm.el.follow_cc = t.reqCommentForm.find("#follow_cc");
    t.reqCommentForm.el.cc_emails = t.reqCommentForm.find('#cc_emails');
    t.reqCommentForm.el.btnSubmit = t.reqCommentForm.find('#commentButton');

    t.approval_requests_panel = t.content.find("#srd-panel-approvals");

    t.frm = t.content.find("#tkt_reuqest_update_status_form");
    t.frmEl = {};
    t.frmEl.comment = t.frm.find("#comment");
    t.frmEl.status_id = t.frm.find('#status_id');
    t.frmEl.btnSubmit = t.frm.find("#updateStatus");
    t.frmEl.id = t.frm.find("#id");
    t.frmEl.tmp_id = t.frm.find("#tmp_id");
    t.frmEl.is_note = t.frm.find("#is_note");
    t.frmEl.comment_cc = t.frm.find('.comment_cc');
    t.frmEl.follow_cc = t.frm.find("#follow_cc");
    t.frmEl.cc_emails = t.frm.find('#cc_emails');

    t.frm.httpCall = true;

    const summernoteOptions = {
        inheritPlaceholder: true,
        placeholder: 'Enter Your Message',
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true,
    };

    t.setMainAttachments = function () {
        var at = [];
        $.each(t.config.attachments, function (i, v) {
            var sext = v.ext.toLowerCase();
            var isImage = ["png", "jpeg", "jpg", "gif", "webp"].includes(sext);
            var fileIcon = 'bi bi-file-earmark text-secondary';
            if (sext == "pdf") {
                fileIcon = 'bi bi-file-earmark-pdf text-danger';
            } else if (["xls", "xlsx", "csv"].includes(sext)) {
                fileIcon = 'bi bi-file-earmark-excel text-success';
            } else if (["doc", "docx"].includes(sext)) {
                fileIcon = 'bi bi-file-earmark-word text-primary';
            } else if (isImage) {
                fileIcon = 'bi bi-image text-primary';
            }
            var actions = '';
            if (isImage) {
                actions +=
                    '<span class="tri-view text-secondary small cursor-pointer" ' +
                    'data-view_mode="1" ' +
                    'data-view="' + t.config.url.attachment_view + '/' + v.id + '" ' +
                    'data-name="' + decodeURIComponent(v.name) + '">' +
                    '<i class="bi bi-eye"></i>' +
                    '</span>';
            }

            actions +=
                '<span class="tri-download text-secondary small cursor-pointer ms-2" ' +
                'data-url="' + t.config.url.attachment_download + "/" + v.id + '">' +
                '<i class="bi bi-download"></i>' +
                '</span>';

            at.push(
                '<div class="d-flex align-items-center bg-light border rounded-4 px-2 py-2 mb-2 col-md-4">' +
                '<div class="bg-white border rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width:30px;height:30px;">' +
                '<i class="' + fileIcon + '"></i>' +
                '</div>' +
                '<div class="flex-grow-1 text-truncate fw-medium small text-dark pe-2">' +
                decodeURIComponentSafe(unescape(v.name)) +
                '</div>' +
                '<div class="d-flex align-items-center flex-shrink-0">' +
                actions +
                '</div>' +
                '</div>'
            );
        });
        if (at.length > 0) {
            t.main_attachments.html(
                '<div>' +
                '<div class="fw-semibold mb-2">' +
                'Attachment:' +
                '</div><div class="row gap-2 px-3">' +
                at.join("") +
                '</div></div>'
            );
        }
    };

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

    t.attachmentView = function (e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        if (type == 1) {
            $.swipebox([{ href: $(this).attr('data-view'), title: $(this).attr('data-name') }]);
        }
    };

    t.attachmentDownload = function (e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }

    t.frmValidator = t.frmAproval.validate({
        onsubmit: false,
        rules: {
            approve_status: {
                required: true
            },
            approved_day: {
                required: function () {
                    return t.frmAproval.approve_status.val() != 2;
                },
                number: true,
                digits: true,
                min: 1,
                max: t.config.data.no_of_approval_day != null ? t.config.data.no_of_approval_day : 90,
            }
        }
    });

    t.submit = function (e) {
        e.preventDefault();
        let s = t.frmAproval.comments.val();
        count = s.replaceAll("&nbsp;", "").trim();
        var checkLTTSAdmin = 1;
        if (t.config.client == "ltts" && t.config.sub_client == "admin" && t.frmAproval.approve_status.val() == 1) {
            checkLTTSAdmin = 0;
        }
        if (t.frmValidator.form() == false) {
            if (count.length <= 0 && checkLTTSAdmin == 1 && t.config.client !== "etherealmachines") {
                t.frmAproval.find('#shows_error_approve').html('This field is required.');
            }
            return false;
        }
        if (count.length <= 0 && checkLTTSAdmin == 1 && t.config.client !== "etherealmachines") {
            t.frmAproval.find('#shows_error_approve').html('This field is required');
            return false;
        } else {
            t.frmAproval.find('#shows_error_approve').html('');
        }
        t.frmAproval.button.prop('disabled', true);
        t.frmAproval.button.html('Sending...');

        var updation_url = t.config.url.approve;

        var frmData = new FormData(t.frmAproval[0]);
        frmData.append('_token', t.config.token);
        var req_id = t.config.id != '' ? '/' + t.config.id : ''
        t.frm.httpCall = false;
        var http = $.ajax({
            url: updation_url,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmAproval.comments.val('').summernote('code', '');
                    sweetAlert('center', 'success', data);
                    window.location = t.config.url.info + req_id;
                } else if (data.status == 'danger') {
                    t.frmAproval.comments.val('').summernote('code', '');
                    sweetAlert('center', 'error', data);
                    window.location = t.config.url.info + req_id;
                } else {
                    sweetAlert('center', 'error', data);
                    t.frmAproval.button.prop('disabled', false);
                    t.frmAproval.button.html('Send');
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': "Something went wrong..!",
            }
            sweetAlert('center', 'error', data);
            t.frmAproval.button.prop('disabled', false);
            t.frmAproval.button.html('Send');
        });
        http.always(function (data) {
            t.frm.httpCall = true;
        });
    };
    t.frmCommentValidator = t.reqCommentForm.validate({
        onsubmit: false,
        rules: {
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

    t.frmCommentSubmit = function (e) {
        e.preventDefault();
        let s = t.reqCommentForm.el.comment.val();
        if (t.frmCommentValidator.form() == false) {
            return false;
        }
        if (s == '') {
            t.reqCommentForm.find('#shows_error').html('This field is required.');
            return false;
        }else {
            t.reqCommentForm.find('#shows_error').html('');
        }
        var formData = new FormData(t.reqCommentForm.get(0));
        formData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.config.url.update,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.data = data.data;
                    t.refreshTimeLine();
                    t.frmCommentTokenize();
                    sweetAlert('center', 'success', data);
                    t.reqCommentForm.el.comment.val("").summernote('code', '');
                    t.reqCommentForm.find('#shows_error').html("");
                    var uploader = document.querySelector('#attachment-dropper-cover-comment').AMGDragDropUploader;
                    if (uploader) {
                        uploader.clear();
                    }
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': "Something went Wrong",
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.revokeApprovalRequestDecision = function (e) {
        e.preventDefault();

        var frmData = new FormData(t.frmAproval[0]);
        frmData.append('_token', t.config.token);
        frmData.append('approval_request_id', $(this).attr('data-id'));
        sweetAlertConfirmation({
            message: "Revoke this approval",
            onConfirm: function () {
                var url = t.config.url.revokeDecision.replace(":pr_id", t.config.id);
                t.frm.httpCall = false;
                var http = $.ajax({
                    url: url,
                    type: "POST",
                    processData: false,
                    contentType: false,
                    data: frmData
                });
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            window.location = t.config.url.info + '/' + t.config.id;
                        }
                        else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function () {
                    var data = {
                        'msg': "Something went Wrong",
                    }
                    sweetAlert('center', 'error', data);
                });
                http.always(function (data) {
                    t.frm.httpCall = true;
                });
            }
        });
    };

    t.refreshTimeLine = function () {
        var formData = new FormData();
        var url = t.config.url.getRefreshedTimeLine;

        formData.append('_token', t.config.token);
        formData.append('id', t.config.data.id);

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
                    t.timeline.empty();
                    if (data.data.length > 0) {
                        $.each(data.data, function (i, v) {
                            var initials = '';
                            if (v.commenter) {
                                initials = v.commenter
                                    .split(' ')
                                    .map(word => word.charAt(0))
                                    .join('')
                                    .substring(0, 2)
                                    .toUpperCase();
                            }
                            var avatarHtml = '';
                            if (v.profile_image) {
                                avatarHtml = `
                                    <img src="${v.profile_image}"
                                        class="rounded-circle srd-conv-avatar-img"
                                        alt="${v.commenter}">
                                `;
                            } else {
                                avatarHtml = `
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold flex-shrink-0 srd-conv-avatar">
                                        ${initials}
                                    </div>
                                `;
                            }
                            var attachmentsHtml = '';
                            if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                var at = [];
                                $.each(v.attachments, function (i, a) {
                                    var sext = a.ext.toLowerCase();
                                    var isImage = ["png", "jpeg", "jpg", "gif", "webp"].includes(sext);
                                    var fileIcon = 'bi bi-file-earmark text-secondary';
                                    if (sext == "pdf") {
                                        fileIcon = 'bi bi-file-earmark-pdf text-danger';
                                    } else if (["xls", "xlsx", "csv"].includes(sext)) {
                                        fileIcon = 'bi bi-file-earmark-excel text-success';
                                    } else if (["doc", "docx"].includes(sext)) {
                                        fileIcon = 'bi bi-file-earmark-word text-primary';
                                    } else if (isImage) {
                                        fileIcon = 'bi bi-image text-primary';
                                    }
                                    var actions = '';
                                    if (isImage) {
                                        actions +=
                                            '<span class="tri-view text-secondary small cursor-pointer" ' +
                                            'data-view_mode="1" ' +
                                            'data-view="' + t.config.url.attachment_view + '/' + a.id + '" ' +
                                            'data-name="' + decodeURIComponent(a.name) + '">' +
                                            '<i class="bi bi-eye"></i>' +
                                            '</span>';
                                    }
                                    actions +=
                                        '<span class="tri-download text-secondary small cursor-pointer ms-2" ' +
                                        'data-url="' + t.config.url.attachment_download + "/" + a.id + '">' +
                                        '<i class="bi bi-download"></i>' +
                                        '</span>';
                                    at.push(
                                        '<div class="d-flex align-items-center bg-light border rounded-4 px-2 py-2 mb-2 col-md-4">' +
                                        '<div class="bg-white border rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width:30px;height:30px;">' +
                                        '<i class="' + fileIcon + '"></i>' +
                                        '</div>' +
                                        '<div class="flex-grow-1 text-truncate fw-medium small text-dark pe-2">' +
                                        decodeURIComponentSafe(unescape(a.name)) +
                                        '</div>' +
                                        '<div class="d-flex align-items-center flex-shrink-0">' +
                                        actions +
                                        '</div>' +
                                        '</div>'
                                    );
                                });

                                attachmentsHtml =
                                    '<div class="mt-2">' +
                                    '<div class="fw-semibold small mb-2">' +
                                    'Attachment:' +
                                   '</div><div class="row mx-0 gap-2 px-1">' +
                                    at.join("") +
                                    '</div></div>';
                            }

                            let remarksContent = '';
                            if (v.is_html) {
                                remarksContent = `
                                    <iframe
                                        src="${t.config.url.mail_body}/${v.ticket_id}?tfid=${v.tfid}"
                                        style="width:100%; min-height:350px; border:1px solid #ddd; border-radius:8px;">
                                    </iframe>
                                `;
                            } else {
                                remarksContent = `
                                    <div class="medium text-muted">
                                        ${v.remarks}
                                    </div>
                                `;
                            }

                            var html = `
                                <div class="d-flex gap-2 mb-1 srd-comment-item">
                                    <!-- Avatar -->
                                    <div
                                        class="rounded-circle d-flex align-items-center justify-content-center text-white fw-semibold flex-shrink-0 srd-conv-avatar">
                                        ${avatarHtml}

                                    </div>
                                    <!-- Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="fw-semibold small text-dark">
                                                ${v.commenter}
                                            </span>
                                            <span class="text-muted small border-start ps-2">
                                                ${v.updated_at_format}
                                            </span>
                                            <span class="text-muted small border-start ps-2">
                                                ${v.cc_emails ? ` <span class="fw-medium">CC:</span> ${v.cc_emails}` : ''}
                                            </span>
                                        </div>
                                        ${remarksContent}
                                        ${attachmentsHtml}
                                    </div>
                                </div>
                            `;
                            t.timeline.append(html);
                        });
                        t.timeline.removeClass("hide");
                    } else {
                        t.timeline.addClass("hide");
                    }
                } else if (data.msg != "") {
                    sweetAlert('center', 'error', data);
                }
            }
        });
    };
    t.frmUpdateStatusValidator = t.frm.validate({
        onsubmit: false,
        rules: {
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

    t.updateStatus = function (e) {
        e.preventDefault();
        let s = t.frmEl.comment.val();
        if (t.frmUpdateStatusValidator.form() == false) {
            return false;
        }
    
        if (s == '') {
            $('#tkt_reuqest_update_status_form').find('#shows_error').html('This field is required.');
            return false;
        } else {
            $('#tkt_reuqest_update_status_form').find('#shows_error').html('');
        }
        var updation_url = t.config.url.update;

        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);

        t.frm.httpCall = false;
        var http = $.ajax({
            url: updation_url,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    window.location = t.config.url.info + '/' + t.config.id;
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': "Something went Wrong",
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.frm.httpCall = true;
        });
    };

    t.frmCommentTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.reqCommentForm.el.tmp_id.val(v);
        t.frmEl.tmp_id.val(v);
    };

    // $(document).on('click', '.srd-expand-btn', function () {
    //     console.log("calling 1");
    //     var isCurrentlyExpanded =
    //         $('.srd-comment-item').hasClass('srd-comment-expanded') ||
    //         !$('.approval-comment').hasClass('approval-comment-hidden') ||
    //         !$('.approval-comment-box').hasClass('approval-comment-hidden');

    //     var expandNow = !isCurrentlyExpanded;

    //     $('.srd-comment-item').toggleClass('srd-comment-expanded', expandNow);
    //     $('.approval-comment').toggleClass('approval-comment-hidden', !expandNow);
    //     $('.approval-comment-box').toggleClass('approval-comment-hidden', !expandNow);

    //     const svg = $(this).find('svg');

    //     if (expandNow) {
    //         svg.html(`
    //             <polyline points="4 14 10 14 10 20"></polyline>
    //             <polyline points="20 10 14 10 14 4"></polyline>
    //             <line x1="14" y1="10" x2="21" y2="3"></line>
    //             <line x1="3" y1="21" x2="10" y2="14"></line>
    //         `);
    //     } else {
    //         svg.html(`
    //             <polyline points="15 3 21 3 21 9"></polyline>
    //             <polyline points="9 21 3 21 3 15"></polyline>
    //             <line x1="21" y1="3" x2="14" y2="10"></line>
    //             <line x1="3" y1="21" x2="10" y2="14"></line>
    //         `);
    //     }
    // });

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href");
        if (target == "#srd-panel-conversations") {
            t.refreshTimeLine();
        }
    });

    t.frmCommentTokenize();
    t.setMainAttachments();
    t.refreshTimeLine();
    t.main_attachments.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.main_attachments.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    t.frmAproval.button.on("click", $.proxy(t.submit));
    t.reqCommentForm.el.btnSubmit.on("click", $.proxy(t.frmCommentSubmit));
    t.approval_requests_panel.on("click", ".revoke-approval-request-decision", $.proxy(t.revokeApprovalRequestDecision));
    t.frmEl.btnSubmit.on("click", $.proxy(t.updateStatus));

    var select2Opts = { width: "100%" };
    t.frmEl.status_id.select2($.extend({}, select2Opts));
    t.frmAproval.approve_status.select2($.extend({}, select2Opts));
    t.frmEl.comment.summernote(summernoteOptions);
    t.reqCommentForm.el.comment.summernote(summernoteOptions);
    t.frmAproval.comments.summernote(summernoteOptions);

    if (t.config.tkt_config.checked_cc_checkbox == 1) {
        t.frmEl.follow_cc.prop('checked', true);
        t.reqCommentForm.el.follow_cc.prop('checked', true);
    }

    $(document).on('click', '.srd-expand-btn', function () {
        $('.srd-comment-item').toggleClass('srd-comment-expanded');
        $('.approval-comment').toggleClass('approval-comment-hidden');
        $('.approval-comment-box').toggleClass('approval-comment-hidden');
        const svg = $(this).find('svg');
        const isAnyExpanded =
            $('.srd-comment-item').hasClass('srd-comment-expanded') ||
            !$('.approval-comment').hasClass('approval-comment-hidden') ||
            !$('.approval-comment-box').hasClass('approval-comment-hidden');
        if (isAnyExpanded) {
        svg.html(`
            <polyline points="4 14 10 14 10 20"></polyline>
            <polyline points="20 10 14 10 14 4"></polyline>

            <line x1="14" y1="10" x2="21" y2="3"></line>
            <line x1="3" y1="21" x2="10" y2="14"></line>
        `);
        } else {
            svg.html(`
            <polyline points="15 3 21 3 21 9"></polyline>
            <polyline points="9 21 3 21 3 15"></polyline>

            <line x1="21" y1="3" x2="14" y2="10"></line>
            <line x1="3" y1="21" x2="10" y2="14"></line>
        `);
        }
    });

    t.content.on("click", ".btn-print-section", function (e) {
        e.preventDefault();
        var originalContents = document.body.innerHTML;
        var printContents = $('<div class="print-wrapper"></div>');
        // Basic Info
        printContents.append($('.srd-info').clone());
        // Approvals
        var approvalsPanel = $('#srd-panel-approvals').clone();
        approvalsPanel.find('form').remove(); 
        approvalsPanel
            .removeClass('fade')
            .addClass('show active')
            .css({
                display: 'block',
                opacity: '1',
                visibility: 'visible',
                height: 'auto',
                maxHeight: 'none',
                overflow: 'visible'
            });

        printContents.append('<h3 style="margin:20px 0 10px;">Approvals</h3>');
        printContents.append(approvalsPanel);

        // Conversations
        var conversationsPanel = $('#srd-panel-conversations').find('#ticket_timeline').clone();

        conversationsPanel
            .removeClass('fade')
            .addClass('show active')
            .css({
                display: 'block',
                opacity: '1',
                visibility: 'visible',
                height: 'auto',
                maxHeight: 'none',
                overflow: 'visible'
            });

        printContents.append('<h3 style="margin:20px 0 10px;">Conversations</h3>');
        printContents.append(conversationsPanel);

        // Remove unwanted controls
        printContents.find('.tab-bar').remove();
        printContents.find('.srd-expand-btn').remove();
        printContents.find('.btn').remove();


        printContents.find('a').each(function () {
            $(this).replaceWith($(this).text());
        });

        printContents.find('*').css({
            maxHeight: 'none'
        });

        printContents.find('.tab-pane, .timeline, .conversation-list, .approval-list').css({
            overflow: 'visible',
            height: 'auto',
            maxHeight: 'none'
        });

        printContents.find('.col-sm-5').css({
            width: '40%',
            display: 'inline-block',
            verticalAlign: 'top'
        });

        printContents.find('.col-sm-4').css({
            width: '30%',
            display: 'inline-block',
            verticalAlign: 'top'
        });

        printContents.find('.col-sm-3').css({
            width: '25%',
            display: 'inline-block',
            verticalAlign: 'top'
        });

        printContents.find('.col-md-6').css({
            width: '48%',
            display: 'inline-block',
            verticalAlign: 'top'
        });

        printContents.find('.row').css({
            width: '100%',
            display: 'block'
        });

        var pageStyle = document.createElement('style');
        pageStyle.innerHTML = `
            @page {
                margin: 10mm;
                size: auto;
            }

            body {
                margin: 0;
                padding: 15px;
                font-size: 12px;
            }

            h3 {
                margin-top: 20px;
                margin-bottom: 10px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
            }

            .tab-pane {
                display: block !important;
                opacity: 1 !important;
                visibility: visible !important;
            }
        `;

        document.head.appendChild(pageStyle);

        document.body.innerHTML = printContents.html();

        window.print();

        setTimeout(function () {
            document.body.innerHTML = originalContents;
            location.reload();
        }, 500);
    });

    $(document).on('click', '.btn-ticket-history', function (e) {
        e.preventDefault();
        $.ajax({
            url: t.config.url.getTicketRequestHistory + "/" + t.config.id,
            type: "GET",
            dataType: "json",
            success: function (data) {
                let ticket_history = '';
                if (data.status == "success") {
                    $('#ticketHistoryModal').find('#modalTitle').text(t.config.translations.service_request_history);
                    var sub_cat = data.data.sc == null ? "" : data.data.sc;
                    var asset_tag = data.data.asset_tag == null ? "" : data.data.asset_tag;
                    $('#ticket_details_subject').text(data.data.subject);
                    $('#ticket_details_subject').attr('data-original-title', data.data.subject);
                    $('#ticket_details_id').text('#' + data.data.ticket_no);
                    $('#ticket_details_company').text(data.data.company);
                    if (asset_tag != '' && asset_tag != null && asset_tag != undefined) {
                        $('.related-device-row').show();
                        $('#ticket_details_device').text(asset_tag);
                    } else {
                        $('.related-device-row').hide();
                    }
                    $('#ticket_details_category').text(data.data.pc);
                    if (sub_cat != '' && sub_cat != null && sub_cat != undefined) {
                        $('.subcategory-row').show();
                        $('#ticket_details_subcategory').text(sub_cat);
                    } else {
                        $('.subcategory-row').hide();
                    }
                    if (data.history.length > 0) {
                        $.each(data.history, function (key, value) {
                            let commenterName = value.user_name ?? 'System';
                            let updatedTime = value.updated_at_format ?? '';
                            let actionHtml = value.change_info ?? '';
                            let timeAgoText = value.time_ago ?? '';
                            let badgeClass = 'green';
                            let avatarHtml = '';
                            if (value.user_image != null && value.user_image != '') {
                                avatarHtml =
                                    '<img src="' + value.user_image + '" class="w-100 h-100 object-fit-cover">';
                            } else {
                                avatarHtml =
                                    '<span class="fw-bold text-uppercase">' +
                                    commenterName.charAt(0) +
                                    '</span>';
                            }
                            ticket_history += '<div class="tkt-hst-timeline-item d-flex gap-3 align-items-start">' +
                                '<div class="d-flex flex-column align-items-center flex-shrink-0 align-self-stretch">' +
                                '<div class="rounded-circle d-flex flex-column align-items-center justify-content-center text-center text-white flex-shrink-0 tkt-hst-badge ' + badgeClass + '">' + timeAgoText + '</div>' +
                                '<div class="tkt-hst-connector-line"></div>' +
                                '</div>' +
                                '<div class="flex-fill mt-1 tkt-hst-card mb-1">' +
                                '<div class="d-flex align-items-center gap-1 mb-1 meta-time">' +
                                '<svg width="12" height="12" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                '<path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="#7F7F7F"/>' +
                                '</svg>' +
                                '<span class="b7-text" style="color:#7F7F7F">' + value.updated_at_format + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center gap-2 mb-1">' +
                                '<div class="rounded-circle overflow-hidden flex-shrink-0 tkt-hst-avatar d-flex align-items-center justify-content-center">' +
                                avatarHtml +
                                '</div>' +
                                '<span class="b6-text fw-bold">' + commenterName + '</span>' +
                                '<span class="b6-text fw-bold">•</span>' +
                                '<span class="b6-text fw-light" style="color:#7F7F7F">Changes done by ' + commenterName + '</span>' +
                                '</div>' +
                                '<div class="b6-text fw-normal">' + actionHtml + '</div>' +
                                '</div>' +
                                '</div>';
                        });

                    } else {
                        ticket_history =
                            '<div class="text-center p-4">' +
                            '<p>No history available</p>' +
                            '</div>';
                    }
                    $('#ticket_history_container').html(ticket_history);
                    $('#ticketHistoryModal').modal('show');
                } else {
                    $('#ticket_history_container').html(`
                    <div class="text-center p-4">
                        <p>${data.msg}</p>
                    </div>
                `);
                }
            },
            error: function () {
                $('#ticket_history_container').html(`
                <div class="text-center p-4 text-danger">
                    Something went wrong
                </div>
            `);

            }
        });

    });

}