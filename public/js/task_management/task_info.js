var Info = function (config) {
    var t = this;
    t.config = config;
    t.content = $('.sr-detail-wrapper');

    t.httpCall = true;
    t.taskModal = new AddTaskModal(config);

    t.content.on("click", "[data-task-action='edit']", function () {
        var taskId = $(this).data("id");   
        t.taskModal.openForEdit(taskId);
    });

    // ----- COMMENT FORM -----
    t.frmComment = t.content.find("#frm_comment");
    t.frmComment.el = {};
    t.frmComment.el.id = t.frmComment.find("#id");
    t.frmComment.el.tmp_id = t.frmComment.find("#tmp_id");
    t.frmComment.el.comment = t.frmComment.find("#comment");
    t.frmComment.attachment_dropper_cover = t.frmComment.find('#attachment-dropper-cover');
    t.frmComment.attachment_dropper = t.frmComment.attachment_dropper_cover.find('#attachment-dropper');
    t.frmComment.el.is_note = t.frmComment.find('#task_internal_note');
    t.frmComment.el.btnSubmit = t.frmComment.find("#btnSubmit");
    t.attachment = t.frmComment.find("#attachments");

    t.frmComment.el.comment.summernote({
        inheritPlaceholder: true,
        placeholder: "Enter your comment",
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']]
        ],
        minHeight: 200,
        focus: false
    });

    t.frmCommentSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        let s = t.frmComment.el.comment.val();
        if (s == '') {
            $('#frm_comment').find('#shows_error').html('This field is required.');
            $('#frm_comment').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            $('#frm_comment').find('#shows_error').html('');
        }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;

        var formData = new FormData(t.frmComment.get(0));
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
                    t.refreshTimeLine();
                    t.frmCommentTokenize();
                    sweetAlert('center', 'success', data);
                    t.frmComment.el.comment.val("").summernote('code', '');
                    $('#frm_comment').find('#shows_error').html("");
                    t.frmComment.el.is_note.prop("checked", false);
                    t.attachment.empty();
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

    t.frmCommentTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.frmComment.el.tmp_id.val(v);
        t.frmUpdateTaskStatus.el.temp_id.val(v);
    };

    // ----- TIMELINE -----
    t.timeline = t.content.find("#task_timeline");
    t.toggle_tiny_view = t.content.find('#toggle_tiny_view');

    t.refreshTimeLine = function () {
        var url = t.config.url.get_timeline;
        if (window.location.pathname.indexOf('task-management/archivedTaskInfo') !== -1) {
            url = t.config.url.get_archived_timeline;
        }

        var formData = new FormData();
        formData.append('id', t.config.param);

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
                        // Add title and wrapper
                        t.timeline.append(`
                            <div id="timelineTitle" style="font-weight:600; margin-bottom:10px;">
                                Task Comments (${data.data.length})
                            </div>
                            <div id="commentWrapper" class="comment-wrapper" style="border:1px solid #eee; border-radius:10px; padding:20px; background:#fff;">
                        `);

                        var class_name = '';
                        $.each(data.data, function (i, v) {
                            if (v.updated_by == v.assigned_to && v.assigned_to != null) {
                                class_name = "color-code-bar color-code-blue-text";
                            } else if (v.updated_by == v.creator_id && v.creator_id != null) {
                                class_name = "color-code-bar color-code-rose-text";
                            } else if (v.updated_by != v.creator_id && v.creator_id != null) {
                                class_name = "color-code-bar color-code-yellow-text";
                            }

                            var previewText = (v.remarks?.length > 100) ? v.remarks.slice(0, 100): (v.remarks || '');
                            var attachmentsHtml = '';

                            if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                var at = [];
                                $.each(v.attachments, function (ai, v2) {
                                    var ext = v2.ext.toLowerCase();
                                    var fileName = decodeURIComponent(v2.name);
                                    var eye_link = '';
                                    if (t.isImageExtension(ext)) {
                                        eye_link = `<span class="tri-view" data-view_mode="1" data-view="${t.config.url.view_attachment}/${v2.id}" data-name="${decodeURIComponent(v2.name)}"><i class="bi bi-eye"></i></span>`;
                                    }
                                    var ai_bg = v2.thumb == 1 ? "ai-bg" : "";
                                    var bg = v2.thumb == 1 ? `background-image: url(${t.config.url.view_attachment}/${v2.id}/1)` : "";

                                    at.push(`
                                        <div class="attach-item ${ai_bg}" style="${bg}; display:inline-block; margin-right:8px; margin-bottom:6px;">
                                            <div class="attach-item-cntnt nobg" style="display:flex; align-items:center; gap:6px; padding:4px 8px; background:#f3f4f6; border-radius:4px;">
                                                ${t.getAttachmentIconHtml(ext)}
                                                <span class="attach-name" style="color:#001f5b; background-color:transparent; font-size:12px; max-width:120px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">${fileName}</span>
                                                <div class="icons" style="display:flex; gap:4px;">
                                                    <span class="attachment-header">${eye_link}</span>
                                                    <span class="tri-download attachment-header" data-url="${t.config.url.download_attachment}/${v2.id}" style="cursor:pointer;"><i class="bi bi-download" style="font-size:14px;"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                });
                                attachmentsHtml = `
                                    <div class="attachments" style="margin-top:10px;">
                                        <div class="text-bold attachment-header" style="margin-bottom:.7rem;">Attachments:</div>
                                        ${at.join('')}
                                    </div>
                                `;
                            }

                            var commentHtml = `
                                <div class="single-comment" style="display:flex; position:relative; padding:20px 4px; margin-top:5px; ${(v.is_note != null && v.is_note == 1) ? 'background-color:#fffcd3; box-shadow:0 1px 1px rgba(0,0,0,.05);' : ''}">
                                    <div class="left-column" style="width:30px; display:flex; flex-direction:column; align-items:center; position:relative;">
                                        <img src="${v.profile_img || ''}" alt="User Icon" style="width:32px; height:32px; border-radius:50%;">
                                        <div class="vertical-line" style="flex:1; width:1px; background:#ddd; margin:5px 0;"></div>
                                    </div>
                                    <div class="right-content" style="flex:1; padding-left:15px;">
                                        <div style="margin-bottom:5px; font-size:14px;">
                                            ${v.is_note == 1 ? 'Note Added By' : 'Commented By'}
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
                                    <div class="comment-timestamp" style="position:absolute; margin:5px; bottom:-5px; left:43px; width:100%; display:flex; justify-content:flex-start; font-size:12px; font-weight:600;">
                                        ${v.updated_at_format}
                                    </div>
                                </div>
                            `;

                            $('#commentWrapper').append(commentHtml);
                        });

                        // Close wrapper
                        t.timeline.append('</div>');

                        // Add scroll styling
                        t.timeline.css({
                            'overflow-y': 'scroll',
                            'max-height': '350px',
                            'scrollbar-width': 'thin',
                            'scrollbar-color': '#D7D7D7 transparent'
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

    t.getAttachmentIconHtml = function (ext) {
        var iconMap = {
            'pdf': 'bi-file-pdf',
            'doc': 'bi-file-word',
            'docx': 'bi-file-word',
            'xls': 'bi-file-excel',
            'xlsx': 'bi-file-excel',
            'ppt': 'bi-file-ppt',
            'pptx': 'bi-file-ppt',
            'zip': 'bi-file-zip',
            'rar': 'bi-file-zip',
            'txt': 'bi-file-text',
            'csv': 'bi-file-spreadsheet',
            'png': 'bi-file-image',
            'jpg': 'bi-file-image',
            'jpeg': 'bi-file-image',
            'gif': 'bi-file-image',
            'svg': 'bi-file-image',
            'mp3': 'bi-file-music',
            'wav': 'bi-file-music',
            'mp4': 'bi-file-play',
            'avi': 'bi-file-play',
            'mkv': 'bi-file-play',
            'js': 'bi-file-code',
            'html': 'bi-file-code',
            'css': 'bi-file-code',
            'php': 'bi-file-code',
            'py': 'bi-file-code',
            'json': 'bi-file-code',
            'xml': 'bi-file-code',
            'exe': 'bi-file-exe',
            'msi': 'bi-file-exe'
        };
        var icon = iconMap[ext] || 'bi-file-earmark';
        return '<i class="bi ' + icon + '" style="font-size:20px; color:#7F7F7F;"></i>';
    };

    t.isImageExtension = function (ext) {
        var imageExts = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'bmp', 'ico'];
        return imageExts.indexOf(ext) !== -1;
    };

    // Toggle comment expansion (chevron click)
    t.timeline.on('click', '.toggle-comment', function (e) {
        e.preventDefault();
        var $parent = $(this).closest('.single-comment');
        var $preview = $parent.find('.comment-preview');
        var $full = $parent.find('.comment-full');
        var $icon = $(this).find('i');

        if ($full.is(':visible')) {
            $preview.show();
            $full.hide();
            $icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            $preview.hide();
            $full.show();
            $icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });

    // Also support the single-tiny-viewer button if present
    t.timeline.on('click', '.single-tiny-viewer', function (e) {
        e.preventDefault();
        var $entry = $(this).closest('.single-comment');
        if ($entry.length) {
            var $preview = $entry.find('.comment-preview');
            var $full = $entry.find('.comment-full');
            var $icon = $(this).find('i');

            if ($full.is(':visible')) {
                $preview.show();
                $full.hide();
                $icon.removeClass('bi-arrows-angle-contract').addClass('bi-arrows-angle-expand');
            } else {
                $preview.hide();
                $full.show();
                $icon.removeClass('bi-arrows-angle-expand').addClass('bi-arrows-angle-contract');
            }
        }
    });

    // ----- UPDATE TASK FORM -----
    t.taskUpdateMdl = t.content.find("#taskUpdateStatusCard");
    t.frmUpdateTaskStatus = t.taskUpdateMdl.find("#frm_update_status");
    t.frmUpdateTaskStatus.el = {};
    t.frmUpdateTaskStatus.el.task_id = t.frmUpdateTaskStatus.find("#id");
    t.frmUpdateTaskStatus.el.temp_id = t.frmUpdateTaskStatus.find("#tmp_id");
    t.frmUpdateTaskStatus.el.task_status_id = t.frmUpdateTaskStatus.find('#task_status_id');
    t.frmUpdateTaskStatus.el.task_comment = t.frmUpdateTaskStatus.find("#task_comment");
    t.frmUpdateTaskStatus.el.btnSubmit = t.taskUpdateMdl.find("#btnSubmit");

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

    t.frmUpdateTaskStatus.el.task_status_id.select2({
        width: '100%',
        dropdownParent: t.frmUpdateTaskStatus.el.task_status_id.parent(),
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
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent('div'));
        }
    });

    $.validator.addMethod("summernotedescription", function (value, element) {
        let content = $(element).summernote('code').replace(/(<([^>]+)>)/gi, "").trim();
        return content.length > 0;
    }, "This field is required.");

    t.frmUpdateTaskStatus.el.btnSubmit.on("click", function (e) {
        e.preventDefault();

        if (!t.frmTaskStatusValidator.form()) {
            return false;
        }

        var formData = new FormData();
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
            data: formData,
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status === "success") {
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
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });

        http.always(function () {
            t.frmUpdateTaskStatus.el.btnSubmit.prop("disabled", false);
        });
    });

    // ----- DATA TABLES -----
    const defaultDataTableOptions = (baseClass) => ({
        pagingType: "simple",
        autoWidth: false,
        pageLength: 5,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
        lengthChange: false,
        searching: true,
        scrollX: true,
        initComplete: function () {
            let table = this.api();
            $(`#${baseClass}_wrapper`).removeClass("form-inline");
            $(`#${baseClass}_filter`).remove();
            $(`#${baseClass}_length`).find("select").select2();
            $(`#${baseClass}_filter input`).off(".DT");
            $(`.plain-search`).on("keyup", function (e) {
                if (e.keyCode === 13) {
                    let val = $(this).val().trim();
                    if (val === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    table.search(val).draw();
                }
            });

            $(`#${baseClass}_wrapper .plain-search-icon`).on("click", function () {
                let input = $(this).closest(".searchbox_cover").find(".plain-search");
                let val = input.validate_str_param();
                if (val === false) {
                    alert("Please enter a valid value for search");
                    return false;
                }
                table.search(val).draw();
            });
        }
    });

    t.table = t.content.find('#relevantRecord');
    t.ticketTable = t.content.find('#relevantTickets');
    t.table.DataTable(defaultDataTableOptions("relevantRecord"));
    t.ticketTable.DataTable(defaultDataTableOptions("relevantTickets"));

    // ----- SELF ASSIGN -----
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
                            window.location.reload();
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

    // ----- DELETE TASK -----
    t.deleteTask = function (taskId) {
        sweetAlertConfirmation({
            message: 'Are you sure you want to delete this task?',
            onConfirm: function () {
                $.ajax({
                    url: t.config.url.delete + '/' + taskId,
                    type: 'GET',
                    success: function (data) {
                        if (data.status === "success") {
                            sweetAlert('center', 'success', data);
                            window.location.reload();
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    },
                    error: function () {
                        var data = {
                            'msg': config.translations.something_went_wrong
                        };
                        sweetAlert('center', 'error', data);
                    }
                });
            }
        });
    };

    $(document).on('click', '#delete-task', function () {
        let taskId = $(this).data('id');
        t.deleteTask(taskId);
    });

    // ----- ATTACHMENT VIEW / DOWNLOAD -----
    t.attachmentView = function (e) {
        e.preventDefault();
        var $el = $(this);
        var url = $el.data('view');
        var name = $el.data('name');
        
        if (url && name) {
            // Make sure we're passing a proper jQuery object
            var $link = $('<a href="' + url + '">' + name + '</a>');
            
            if (typeof $.swipebox === 'function') {
                try {
                    $.swipebox($link, {
                        useSVG: true,
                        hideBarsDelay: 3000,
                        loopAtEnd: false,
                        hideBarsDelay: 3000,
                        initialIndexOnArray: 0,
                    });
                } catch (error) {
                    console.error('Swipebox error:', error);
                    window.open(url, '_blank');
                }
            } else {
                window.open(url, '_blank');
            }
        }
    };
    t.attachmentDownload = function (e) {
        e.preventDefault();
        var $el = $(this);
        var url = $el.data('url');
        window.location.href = url;
    };
    $(document).on('click', "#btn-history", function(e) {
        e.preventDefault();
        var taskId = $(this).data('id');
        var historyUrl = t.config.mainfilter === 'archived'
            ? t.config.url.archived_history
            : t.config.url.history;
        var token = t.config.token || $('meta[name="csrf-token"]').attr('content');
        TaskHistory.open(taskId, historyUrl, token);
    });

    // ----- INITIALIZATION -----
    t.refreshTimeLine();
    t.frmCommentTokenize();
    t.frmComment.el.btnSubmit.on("click", $.proxy(t.frmCommentSubmit));

    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));

    t.timeline.on("click", ".single-tiny-viewer", function (e) {
        e.preventDefault();
        var $entry = $(this).closest('.timeline-entry');
        if ($entry.hasClass('tiny-view-on')) {
            $entry.removeClass('tiny-view-on');
            $(this).html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
        } else {
            $entry.addClass('tiny-view-on');
            $(this).html('<i class="bi bi-arrows-angle-expand faa-fast animated"></i>').attr('title', 'Expand');
        }
    });

    // Trigger file input for comment attachment
    const triggerFileInput = function (e) {
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('attachment').click();
    };

    const btn = document.getElementById('manual_file_trigger');
    if (btn) {
        btn.addEventListener('click', triggerFileInput);
    }

    const dropper = document.getElementById('attachment-dropper');
    if (dropper) {
        dropper.addEventListener('click', triggerFileInput);
    }

    // User info popover functionality
    var activePopover = null;

    document.querySelectorAll('.user-info-trigger').forEach(function (trigger) {
        trigger.addEventListener('mouseenter', function () {
            var popoverId = this.getAttribute('data-popover');

            document.querySelectorAll('.user-info-popover').forEach(function (p) {
                if (p.id !== popoverId) {
                    p.classList.remove('active');
                }
            });

            var popover = document.getElementById(popoverId);
            if (popover) {
                popover.classList.add('active');
                activePopover = popover;
            }
        });
    });

    document.querySelectorAll('.user-info-popover').forEach(function (popover) {
        popover.addEventListener('mouseenter', function () {
            this.classList.add('active');
            activePopover = this;
        });

        popover.addEventListener('mouseleave', function () {
            this.classList.remove('active');
            activePopover = null;
        });
    });

    document.querySelectorAll('.user-info-trigger').forEach(function (trigger) {
        trigger.addEventListener('mouseleave', function (e) {
            var popoverId = this.getAttribute('data-popover');
            var popover = document.getElementById(popoverId);

            setTimeout(function () {
                if (activePopover !== popover) {
                    popover.classList.remove('active');
                }
            }, 100);
        });
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-close-popover')) {
            var popoverId = e.target.getAttribute('data-close');
            var popover = document.getElementById(popoverId);
            if (popover) {
                popover.classList.remove('active');
                activePopover = null;
            }
        }
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.user-info-popover') && !e.target.classList.contains('user-info-trigger')) {
            document.querySelectorAll('.user-info-popover').forEach(function (p) {
                p.classList.remove('active');
            });
            activePopover = null;
        }
    });
};

var AddTaskModal = function (config) {
  var t = this;
  t.config = config;
  t.content = $(".sr-detail-wrapper");

  t.mdl = t.content.find("#taskModal");
  t.frm = t.mdl.find("#task");
  t.frmEl = {};

  t.frmEl.company = t.mdl.find("#company_id");
  t.frmEl.taskId = t.mdl.find("#task_id");
  t.frmEl.name = t.mdl.find("#name");
  t.frmEl.status_id = t.mdl.find("#status_id");
  t.frmEl.priority_id = t.mdl.find("#priority_id");
  t.frmEl.type_id = t.mdl.find("#type_id");
  t.frmEl.ticket_id = t.mdl.find("#ticket_id");
  t.frmEl.department_id = t.mdl.find("#task_department_id");
  t.frmEl.problem_category_id = t.mdl.find("#task_problem_category_id");
  t.frmEl.sub_category_id = t.mdl.find("#task_sub_category_id");
  t.frmEl.subCategoryIdCvr = t.mdl.find("#task_sub_category_id_cvr");
  t.frmEl.project_id = t.mdl.find("#project_id");
  t.frmEl.change_id = t.mdl.find("#change_id");
  t.frmEl.start_date = t.mdl.find("#start_date");
  t.frmEl.due_date = t.mdl.find("#due_date");
  t.frmEl.end_date = t.mdl.find("#end_date");
  t.frmEl.description = t.mdl.find("#task_description");
  t.frmEl.assigned_to = t.mdl.find("#task_assigned_to");
  t.frmEl.is_visible_user = t.mdl.find("#is_visible_user");
  t.frmEl.cost = t.mdl.find("#cost");

  t.btn = {};
  t.btn.submit = t.mdl.find("#taskSubmit");

  t.httpCall = true;
  t.frmValidator = null;
  t.summernoteReady = false;
  t.flatpickrReady = false;
  t.problem_categories = [];
  t.ticket_data = null;
  t.ticket_add_mode = false;
  t.ticket_dept_id = null;
  t.ticket_dept_name = null;
  t.ticket_pro_id = null;
  t.ticket_pro_name = null;
  t.ticket_sub_id = null;
  t.ticket_sub_name = null;

  t.editMode   = false;   
  t.editTaskId = null;   
  t.editFlag   = false;

  // ✅ NEW: Added for preventing multiple API calls
  t._reloadTimer = null;
  t._lastLoadedDept = null;
  t._isLoadingTaskData = false;

  t.parentOf = function (el) {
    return el.parent();
  };
  t.rowOf = function (el) {
    return el.closest(".amg-form-field-row");
  };
  t.showRow = function (el) {
    t.rowOf(el).removeClass("d-none");
  };
  t.hideRow = function (el) {
    t.rowOf(el).addClass("d-none");
  };

  t.userDropdownFormat = function (s) {
    if (s && typeof s.loading !== "undefined" && s.loading) {
      return $("<div>" + s.text + "</div>");
    }
    var a = "<div class='row'><div class='col-sm-10'>";
    var truncText =
      s.text && s.text.length > 30
        ? s.text.substring(0, 30) + "..."
        : s.text || "";
    a +=
      "<div class='so-t'><i class='bi bi-person' style='padding-right:3px'></i>" +
      truncText;
    a +=
      s.status == 1
        ? "<span class='active-user'></span>"
        : "<span class='inactive-user'></span>";
    a += "</div>";
    if (s.email) {
      var truncEmail =
        s.email.length > 30 ? s.email.substring(0, 30) + "..." : s.email;
      a +=
        "<div class='so-t'><i class='bi bi-envelope' style='padding-right:3px'></i>" +
        truncEmail +
        "</div>";
    }
    if (s.employee_num) {
      a +=
        "<div class='so-t'><i class='bi bi-credit-card-2-front' style='padding-right:3px'></i>" +
        s.employee_num +
        "</div>";
    }
    a +=
      "</div><div class='col-sm-2'><img class='img-u' src='" +
      (s.img_path || "") +
      "'/></div></div>";
    return $("<div>" + a + "</div>");
  };

  t.initPriority = function () {
    t.frmEl.priority_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.priority_id),
      minimumResultsForSearch: -1,
    });
  };
  t.initStatus = function () {
    t.frmEl.status_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.status_id),
      minimumResultsForSearch: -1,
    });
  };

  t.initCompany = function () {
    t.frmEl.company.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.company),
      ajax: {
        url: t.config.url.getCompanies,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder: "Select Company",
      allowClear: true,
    });
  };

  t.initTypeSelect = function () {
    t.frmEl.type_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.type_id),
      placeholder: "Select Related To",
      allowClear: true,
    });
    t.frmEl.type_id.on("change", function () {
      t.typeIdChange();
    });
  };

  t.initAssignUserSelect = function (context) {
    if (t.frmEl.assigned_to.hasClass("select2-hidden-accessible")) {
      t.frmEl.assigned_to.val(null).trigger("change");
      t.frmEl.assigned_to.select2("destroy");
    }
    t.frmEl.assigned_to.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.assigned_to),
      ajax: {
        url: t.config.url.getUser,
        dataType: "json",
        delay: 250,
        data: function (p) {
          var d = { search: p.term || "", page: p.page || 1 };
          if (context === "task") {
            d.context = "task";
          }
          return d;
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.pagination && data.pagination.more },
          };
        },
      },
      templateResult: function (s) {
        if (!s || !s.id) return $("<div>No data</div>");
        return t.userDropdownFormat(s);
      },
      placeholder:
        t.config.translations.Select_task_assign || "Select Assigned To",
      allowClear: true,
    });
  };

  t.initProjectSelect = function () {
    t.frmEl.project_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.project_id),
      ajax: {
        url: t.config.url.getProjectsByQuery,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder:
        t.config.translations.select_task_project || "Select Project",
      allowClear: true,
    });
  };

  t.initChangeSelect = function () {
    t.frmEl.change_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.change_id),
      ajax: {
        url: t.config.url.getChangesByQuery,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder:
        t.config.translations.Select_task_record || "Select Change Record",
      allowClear: true,
    });
  };

  t.initTicketSelect = function () {
    t.frmEl.ticket_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.ticket_id),
      ajax: {
        url: t.config.url.getTickets,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { 
            search: p.term || "", 
            page: p.page || 1,
            company_id:t.frmEl.company.val(),
          };
        },
        processResults: function (data, params) {
          t.ticket_data = data.data;
          params.page = params.page || 1;
          return {
            results: (data.data || []).map(function (tk) {
              return { id: tk.id, text: tk.text };
            }),
            pagination: { more: data.next_page_url !== null },
          };
        },
      },
      placeholder: "Select Ticket",
      allowClear: true,
    });
  };

  // ✅ CHANGED: initDepartmentSelect with skip flag
  t.initDepartmentSelect = function () {
    t.frmEl.department_id
      .select2({
        width: "100%",
        dropdownParent: t.parentOf(t.frmEl.department_id),
        ajax: {
          url: t.config.url.departments_with_company,
          dataType: "json",
          delay: 200,
          data: function (p) {
            return { search: p.term || "", page: p.page || 1 };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results || [],
              pagination: { more: data.has_more === true },
            };
          },
        },
        placeholder: "Select Department",
        allowClear: true,
      })
      .on("change", function () {
        if (!t._isLoadingTaskData) {
          t.reloadProblemCategory();
        }
      });
  };

  // ✅ CHANGED: reloadProblemCategory with debounce and edit mode support
  t.reloadProblemCategory = function () {
    var dept = t.frmEl.department_id.val();
    var self = this;
    
    // Prevent duplicate calls for same department
    if (dept === self._lastLoadedDept && dept !== null) {
      return;
    }
    
    // Clear any pending timer
    clearTimeout(self._reloadTimer);
    
    self._reloadTimer = setTimeout(function() {
      self._lastLoadedDept = dept;
      
      self.frmEl.subCategoryIdCvr.addClass("d-none");
      self.frmEl.problem_category_id.empty().append(new Option("", "", false, false));
      self.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
      
      if (!dept || dept === "null") return;
      
      $.get(
        self.config.url.problem_categories_by_company + "/" + dept,
        function (data) {
          if (typeof data === "object" && data.data && data.data.length > 0) {
            self.problem_categories = data.data;
            $.each(data.data, function (i, k) {
              if (k.status != 0) {
                var label = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                var opt = new Option(label, k.id, false, false);
                $(opt).attr("title", k.name);
                self.frmEl.problem_category_id.append(opt);
              }
            });
            
            // ✅ Refresh select2
            self.frmEl.problem_category_id.trigger('change.select2');
            
            // Edit mode me problem category set karein
            if (self.editFlag && self._editData && self._editData.problem_category_id) {
              if (!self.frmEl.problem_category_id.find("option[value='" + self._editData.problem_category_id + "']").length) {
                var pcOpt = new Option(
                  self._editData.problem_category_name || self._editData.problem_category_id,
                  self._editData.problem_category_id,
                  true,
                  true
                );
                self.frmEl.problem_category_id.append(pcOpt);
              }
              self.frmEl.problem_category_id.val(self._editData.problem_category_id).trigger("change");
              self.frmEl.problem_category_id.trigger('change.select2');
            }
            
            if (self.ticket_pro_id && self.ticket_add_mode) {
              self.frmEl.problem_category_id.val(self.ticket_pro_id).trigger("change");
              self.frmEl.problem_category_id.trigger('change.select2');
            }
          }
        }
      );
    }, 200);
  };

  t.initProblemCategorySelect = function () {
    t.frmEl.problem_category_id
      .select2({
        width: "100%",
        dropdownParent: t.parentOf(t.frmEl.problem_category_id),
        placeholder: "Select Problem Category",
      })
      .on("change", function () {
        t.reloadSubCategory();
      });
  };

  // ✅ CHANGED: reloadSubCategory with sub array support
  t.reloadSubCategory = function () {
    var selectedPc = t.frmEl.problem_category_id.val();
    t.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
    
    if (!selectedPc) {
      t.frmEl.subCategoryIdCvr.addClass("d-none");
      return;
    }
    
    var item = t.problem_categories.find(function (pc) {
      return pc.id == selectedPc;
    });
    
    var hasSub = item && Array.isArray(item.sub) && item.sub.length > 0;
    
    if (hasSub) {
      t.frmEl.subCategoryIdCvr.removeClass("d-none");
      
      // ✅ Populate subcategories from the 'sub' array
      $.each(item.sub, function (i, subItem) {
        if (subItem.status != 0) {
          var label = subItem.name.length > 40 ? subItem.name.substring(0, 40) + "..." : subItem.name;
          var opt = new Option(label, subItem.id, false, false);
          $(opt).attr("title", subItem.name);
          t.frmEl.sub_category_id.append(opt);
        }
      });
      
      // ✅ Refresh select2
      t.frmEl.sub_category_id.trigger('change.select2');

      // ✅ Set subcategory value for edit mode
      if (t.editFlag && t._editData && t._editData.sub_category_id) {
        var subId = t._editData.sub_category_id;
        var subName = t._editData.sub_category_name || subId;
        
        // Check if option exists
        if (!t.frmEl.sub_category_id.find("option[value='" + subId + "']").length) {
          var opt = new Option(subName, subId, true, true);
          t.frmEl.sub_category_id.append(opt);
        }
        
        t.frmEl.sub_category_id.val(subId).trigger("change");
        t.frmEl.sub_category_id.trigger('change.select2');
      } 
      else if (t.ticket_sub_id && t.ticket_add_mode) {
        t.ticket_add_mode = false;
        if (!t.frmEl.sub_category_id.find("option[value='" + t.ticket_sub_id + "']").length) {
          var opt = new Option(t.ticket_sub_name, t.ticket_sub_id, true, true);
          t.frmEl.sub_category_id.append(opt);
        }
        t.frmEl.sub_category_id.val(t.ticket_sub_id).trigger("change");
        t.frmEl.sub_category_id.trigger('change.select2');
      } 
      else {
        t.frmEl.sub_category_id.trigger("change");
      }
    } else {
      t.frmEl.subCategoryIdCvr.addClass("d-none");
      t.frmEl.sub_category_id.empty();
    }
  };

  t.initSubCategorySelect = function () {
    t.frmEl.sub_category_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.sub_category_id),
      ajax: {
        url: t.config.url.sub_category,
        dataType: "json",
        delay: 200,
        data: function (p) {
          return {
            search: p.term || "",
            page: p.page || 1,
            id: [t.frmEl.problem_category_id.val()],
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data.results || [], function (item) {
              if (item.status === 0) return null;
              return { id: item.id, text: item.text };
            }),
          };
        },
      },
      placeholder:
        t.config.translations.select_sub_category || "Select Sub Category",
    });
  };

  t.initSummernote = function () {
    if (t.summernoteReady) return;
    t.frmEl.description.summernote({
      inheritPlaceholder: true,
      placeholder:t.config.translations.Enter_description || "Enter description",
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
            }
        }
    });

    t.summernoteReady = true;
  };

  t.initDatepickers = function (allowPastDates) {
      if (t.flatpickrReady) return;
      
      // If allowPastDates is true, don't set minDate
      var minDate = allowPastDates ? null : new Date();
      
      t.frmEl.start_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.frmEl.due_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.frmEl.end_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.flatpickrReady = true;
  };

  t.setDateField = function (el, val) {
    if (!val) return;
    var fp = el[0] && el[0]._flatpickr;
    if (fp) {
        var dateObj = fp.parseDate(val, "d/m/Y H:i");
        if (dateObj) {
            fp.setDate(dateObj, true);
        } else {
            fp.setDate(val, true, "d/m/Y H:i");
        }
    } else {
      el.val(val);
    }
  };

  t.typeIdChange = function () {
    var v = t.frmEl.type_id.val();

    t.hideRow(t.frmEl.change_id);
    t.hideRow(t.frmEl.project_id);
    t.hideRow(t.frmEl.ticket_id);
    t.hideRow(t.frmEl.is_visible_user);
    t.hideRow(t.frmEl.department_id);
    t.hideRow(t.frmEl.problem_category_id);
    t.frmEl.subCategoryIdCvr.addClass("d-none");

    t.frmEl.change_id.val(null).trigger("change");
    t.frmEl.project_id.val(null).trigger("change");
    t.frmEl.ticket_id.val(null).trigger("change");
    t.frmEl.department_id.val(null).trigger("change");
    t.frmEl.problem_category_id.empty();
    t.frmEl.sub_category_id.val(null).trigger("change");

    if (v == 1) {
      t.initAssignUserSelect(null);
      t.showRow(t.frmEl.change_id);
    } else if (v == 2) {
      t.initAssignUserSelect(null);
      t.showRow(t.frmEl.project_id);
    } else if (v == 4) {
      t.initAssignUserSelect("task");
      t.showRow(t.frmEl.ticket_id);
      t.showRow(t.frmEl.is_visible_user);
      t.showRow(t.frmEl.department_id);
      t.showRow(t.frmEl.problem_category_id);
    } else {
      t.initAssignUserSelect(null);
    }
  };

  t.parseDate = function (val) {
      if (!val) return NaN;

      val = $.trim(val);

      var parts = val.split(' ');
      var dateParts = parts[0].split('/');

      if (dateParts.length !== 3) return NaN;

      var day = parseInt(dateParts[0], 10);
      var month = parseInt(dateParts[1], 10) - 1;
      var year = parseInt(dateParts[2], 10);

      var hour = 0;
      var minute = 0;

      if (parts.length > 1) {
          var timeParts = parts[1].split(':');

          hour = parseInt(timeParts[0], 10) || 0;
          minute = parseInt(timeParts[1], 10) || 0;
      }

      return new Date(year, month, day, hour, minute, 0);
  };

  $.validator.addMethod(
    "dueAfterStart",
    function (value) {
      var due = t.parseDate(value);
      var start = t.parseDate(t.frmEl.start_date.val());
      return !isNaN(due) && !isNaN(start) ? due >= start : true;
    },
    "Planned Complete Date must be on or after Work Start Date.",
  );

  $.validator.addMethod(
    "dueBeforeEnd",
    function (value) {
      var due = t.parseDate(value);
      var end = t.parseDate(t.frmEl.end_date.val());
      return !isNaN(due) && !isNaN(end) ? due >= end : true;
    },
    "Planned Complete Date must be on or after Actual Complete Date.",
  );

  $.validator.addMethod(
    "startBeforeEnd",
    function (value) {
      var start = t.parseDate(t.frmEl.start_date.val());
      var end = t.parseDate(value);
      return !isNaN(start) && !isNaN(end) ? start <= end : true;
    },
    "Actual Complete Date must be on or after Work Start Date.",
  );

  t.frmValidator = t.frm.validate({
    debug: false,
    rules: {
      company_id:{
        required: true
      },
      name: {
        required: true,
        maxlength:255,
        clean_text_only: true,
        noSpecialStart: true
      },
      status_id: {
        required: true,
        digits: true,
        str_name:true
      },
      priority_id: {
        required: true,
        digits: true,
        str_name:true
      },
      type_id: {
        required: true,
        digits: true,
        str_name:true
      },
      project_id: {
        str_name:true
      },
      change_id:{
        str_name:true
      },
      cost: {
        number: true,
      },
      description: {
        required: true,
        summernotes: true,
        maxSummernoteChars: 2000
      },
      due_date: {
        dueAfterStart: true,
        dueBeforeEnd: true
      },
      end_date: {
        dueBeforeEnd: true,
        startBeforeEnd: true
      },
    },
    errorPlacement: function (error, element) {
      if (element.attr('id') === 'task_description') {
        error.insertAfter(element.parent().find('.note-editor'));
      }
      else if (element.closest('.input-group').length) {
        error.insertAfter(element.closest('.input-group'));
      }
      else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
      $(element).removeClass('is-invalid');
    },
    success: function (label) {
      label.remove();
    }
  });

  // ✅ CHANGED: resetForm with cleanup
  t.resetForm = function () {
    t._lastLoadedDept = null;
    clearTimeout(t._reloadTimer);
    t._isLoadingTaskData = false;
    t.flatpickrReady = false;
    t.frm[0].reset();
    t.editMode   = false;
    t.editTaskId = null;
    t.editFlag   = false;
    t._editData  = null;

    t.mdl.find(".modal-title").text(
      t.config.translations.add_task || "Add Task"
    );

    t.btn.submit.text(t.config.translations.create_btn || "Create");

    [
      t.frmEl.company, t.frmEl.type_id, t.frmEl.assigned_to,
      t.frmEl.project_id, t.frmEl.change_id, t.frmEl.ticket_id,
      t.frmEl.department_id, t.frmEl.problem_category_id, t.frmEl.sub_category_id,
    ].forEach(function (el) {
      if (el.hasClass("select2-hidden-accessible")) el.val(null).trigger("change");
    });

    t.frmEl.status_id.val(t.frmEl.status_id.find("option:first").val()).trigger("change");
    t.frmEl.priority_id.val(t.frmEl.priority_id.find("option:first").val()).trigger("change");

    if (t.summernoteReady) t.frmEl.description.summernote("code", "");

    [t.frmEl.start_date, t.frmEl.due_date, t.frmEl.end_date].forEach(function (el) {
      var fp = el[0] && el[0]._flatpickr;
      if (fp) fp.clear();
    });

    t.ticket_data = null; t.ticket_add_mode = false;
    t.ticket_dept_id = null; t.ticket_dept_name = null;
    t.ticket_pro_id = null; t.ticket_pro_name = null;
    t.ticket_sub_id = null; t.ticket_sub_name = null;
    t.problem_categories = [];

    t.typeIdChange();
    t.frm.find("label.error").remove();
    t.frm.find(".is-invalid, .has-error-group").removeClass("is-invalid has-error-group");
  };

  // ✅ CHANGED: loadTaskData with proper department/problem/subcategory handling
  t.loadTaskData = function (taskId) {
    t._isLoadingTaskData = true;
    t.btn.submit.prop("disabled", true);

    $.ajax({
      url: t.config.url.edit + "/" + taskId,
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (!data || data.status !== "success") {
          sweetAlert("center", "error", { msg: "Could not load task data." });
          t.btn.submit.prop("disabled", false);
          t._isLoadingTaskData = false;
          return;
        }

        var task = data.task;          
        t._editData = task;             
        t.editFlag  = true;          

        t.frmEl.name.val(task.name || "");
        t.frmEl.cost.val(task.cost || "");
        t.frmEl.status_id.val(task.status_id).trigger("change");
        t.frmEl.priority_id.val(task.priority_id).trigger("change");

        t.frmEl.is_visible_user.prop("checked", task.is_visible_user == 1);

        if (task.company_id && task.company_name) {
          var companyOpt = new Option(task.company_name, task.company_id, true, true);
          t.frmEl.company.append(companyOpt).trigger("change");
        }

        t.frmEl.type_id.val(task.type_id).trigger("change");

        if (task.assigned_to && task.assigned_to_name) {
          var assignOpt = new Option(task.assigned_to_name, task.assigned_to, true, true);
          t.frmEl.assigned_to.append(assignOpt).trigger("change");
        }

        if (task.type_id == 1 && task.change_id && task.change_name) {
          var chgOpt = new Option(task.change_name, task.change_id, true, true);
          t.frmEl.change_id.append(chgOpt).trigger("change");
        } else if (task.type_id == 2 && task.project_id && task.project_name) {
          var projOpt = new Option(task.project_name, task.project_id, true, true);
          t.frmEl.project_id.append(projOpt).trigger("change");
        } else if (task.type_id == 4 && task.ticket_id && task.ticket_text) {
          var tickOpt = new Option(task.ticket_text, task.ticket_id, true, true);
          t.frmEl.ticket_id.append(tickOpt).trigger("change");
        }

        // ✅ FIXED: Department, Problem Category, Subcategory
        if (task.type_id == 4) {
          // STEP 1: Set Department
          if (task.department_id && task.department_name) {
            var deptOpt = new Option(task.department_name, task.department_id, true, true);
            t.frmEl.department_id.append(deptOpt);
            t.frmEl.department_id.val(task.department_id);
            
            // ✅ Refresh select2
            t.frmEl.department_id.trigger('change.select2');
            
            // STEP 2: Load categories and set values
            t._loadCategoriesAndSetValues(task);
          } else {
            // No department, try to set problem category directly
            t._setProblemAndSubCategoryDirect(task);
          }
        }

        t.setDateField(t.frmEl.start_date, task.start_date_formatted || "");
        t.setDateField(t.frmEl.due_date,   task.due_date_formatted   || "");
        t.setDateField(t.frmEl.end_date,   task.end_date_formatted   || "");
        var minDateForAll = null;
        
        if (task.start_date_formatted) {
            var startDateObj = t.parseDate(task.start_date_formatted);
            if (!isNaN(startDateObj)) {
                minDateForAll = startDateObj;
            }
        }
        
        if (!minDateForAll) {
            minDateForAll = new Date();
        }

        [t.frmEl.start_date, t.frmEl.due_date, t.frmEl.end_date].forEach(function(el) {
            var fp = el[0] && el[0]._flatpickr;
            if (fp) {
                fp.set('minDate', minDateForAll);
                console.log("minDate set for:", el.attr('id'), "to:", minDateForAll);
            }
        });

        if (t.summernoteReady) {
          t.frmEl.description.summernote("code", task.description || "");
        }

        t._isLoadingTaskData = false;
        t.btn.submit.prop("disabled", false);
      },
      error: function () {
        sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
        t.btn.submit.prop("disabled", false);
        t._isLoadingTaskData = false;
      },
    });
  };

  t.frmEl.company.on('change',function(){
    t.frmEl.ticket_id.val(null).trigger("change")
    t.frmEl.department_id.val(null).trigger("change")
    t.frmEl.problem_category_id.val(null).trigger("change")
    t.frmEl.sub_category_id.val(null).trigger("change")
    t.frmEl.assigned_to.val(null).trigger("change");
  });

  // ✅ NEW: Helper function to load categories and set values
  t._loadCategoriesAndSetValues = function (task) {
    var self = this;
    var dept = task.department_id;
    
    // Clear existing
    self.frmEl.subCategoryIdCvr.addClass("d-none");
    self.frmEl.problem_category_id.empty().append(new Option("", "", false, false));
    self.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
    
    if (!dept || dept === "null") {
      self._setProblemAndSubCategoryDirect(task);
      return;
    }
    
    // Load categories from server
    $.get(
      self.config.url.problem_categories_by_company + "/" + dept,
      function (data) {
        if (typeof data === "object" && data.data && data.data.length > 0) {
          // ✅ Store full response with sub categories
          self.problem_categories = data.data;
          
          // Populate problem categories
          $.each(data.data, function (i, k) {
            if (k.status != 0) {
              var label = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
              var opt = new Option(label, k.id, false, false);
              $(opt).attr("title", k.name);
              self.frmEl.problem_category_id.append(opt);
            }
          });
          
          // ✅ Refresh select2
          self.frmEl.problem_category_id.trigger('change.select2');
          
          // ✅ Set problem category from task data
          if (task.problem_category_id) {
            // Check if option exists, if not create it
            if (!self.frmEl.problem_category_id.find("option[value='" + task.problem_category_id + "']").length) {
              var pcOpt = new Option(
                task.problem_category_name || task.problem_category_id, 
                task.problem_category_id, 
                true, 
                true
              );
              self.frmEl.problem_category_id.append(pcOpt);
            }
            
            self.frmEl.problem_category_id.val(task.problem_category_id).trigger("change");
            self.frmEl.problem_category_id.trigger('change.select2');
            
            // ✅ This will trigger reloadSubCategory which now handles sub from the response
            // Set subcategory after problem category change
            self._setSubCategoryAfterLoad(task);
          }
        } else {
          self._setProblemAndSubCategoryDirect(task);
        }
      }
    ).fail(function() {
      self._setProblemAndSubCategoryDirect(task);
    });
  };

  // ✅ NEW: Helper function to set problem and subcategory directly
  t._setProblemAndSubCategoryDirect = function (task) {
    var self = this;
    
    // Set problem category directly
    if (task.problem_category_id) {
      var pcOpt = new Option(task.problem_category_name || task.problem_category_id, task.problem_category_id, true, true);
      self.frmEl.problem_category_id.append(pcOpt);
      self.frmEl.problem_category_id.val(task.problem_category_id).trigger("change");
      self.frmEl.problem_category_id.trigger('change.select2');
      
      // Set subcategory
      self._setSubCategoryAfterLoad(task);
    }
  };

  // ✅ NEW: Helper function to set subcategory
  t._setSubCategoryAfterLoad = function (task) {
    var self = this;
    
    if (!task.sub_category_id) {
      console.log("No subcategory to set");
      return;
    }
    
    var attempts = 0;
    var maxAttempts = 15; // 15 * 200ms = 3 seconds max
    
    var checkAndSet = function() {
      attempts++;
      var subSelect = self.frmEl.sub_category_id;
      
      // ✅ Check if subcategory option exists
      var optionExists = subSelect.find("option[value='" + task.sub_category_id + "']").length > 0;
      
      if (optionExists) {
        // ✅ Option exists, set it
        subSelect.val(task.sub_category_id).trigger("change");
        subSelect.trigger('change.select2');
        self.frmEl.subCategoryIdCvr.removeClass("d-none");
        self.editFlag = false;
        
        console.log("✅ Subcategory set successfully:", task.sub_category_id, task.sub_category_name);
      } else if (attempts < maxAttempts) {
        // Wait and retry
        console.log("⏳ Waiting for subcategory option, attempt:", attempts);
        setTimeout(checkAndSet, 200);
      } else {
        // ✅ Max attempts reached, create option and set
        console.log("⚠️ Max attempts reached, creating subcategory option");
        
        // Find the subcategory name from problem_categories data
        var subName = task.sub_category_name || task.sub_category_id;
        
        // Try to find sub name from the stored data
        if (self.problem_categories && self.problem_categories.length > 0) {
          var pc = self.problem_categories.find(function(p) {
            return p.id == task.problem_category_id;
          });
          if (pc && pc.sub) {
            var sub = pc.sub.find(function(s) {
              return s.id == task.sub_category_id;
            });
            if (sub) {
              subName = sub.name;
            }
          }
        }
        
        // Create and set the option
        var subOpt = new Option(subName, task.sub_category_id, true, true);
        subSelect.append(subOpt);
        subSelect.val(task.sub_category_id).trigger("change");
        subSelect.trigger('change.select2');
        self.frmEl.subCategoryIdCvr.removeClass("d-none");
        self.editFlag = false;
        
        console.log("✅ Subcategory created and set:", task.sub_category_id, subName);
      }
    };
    
    // Start checking after a small delay
    setTimeout(checkAndSet, 300);
  };

  t.openForEdit = function (taskId) {
    t.resetForm();          
    t.editMode   = true;    
    t.editTaskId = taskId;

    t.mdl.find(".modal-title").text(
      t.config.translations.edit_task || "Edit Task"
    );
    t.btn.submit.text(t.config.translations.update_btn || "Update");
    t.frmEl.taskId.val(taskId);

    t.loadTaskData(taskId);
    t.mdl.modal("show");        
  };

  t.handleSubmit = function (e) {
    e.preventDefault();
    if (!t.frmValidator || !t.frmValidator.form()) return false;
    if (!t.httpCall) return false;

    t.btn.submit.prop("disabled", true);
    t.httpCall = false;

    var url = t.editMode
      ? t.config.url.updateTask + "/" + t.editTaskId
      : t.config.url.addTask;

    var http = $.ajax({
      url: url,
      type: "POST",
      processData: false,
      contentType: false,
      data: new FormData(t.frm[0]),
    });

    http.done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          sweetAlert("center", "success", data);
          t.mdl.modal("hide");
          t.mdl.trigger("task:saved");
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });

    http.fail(function () {
      sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
    });

    http.always(function () {
      t.httpCall = true;
      t.btn.submit.prop("disabled", false);
      setTimeout(function () {
        window.location.reload();
      }, 600);
    });
  };

  t.mdl.on("show.bs.modal", function () {
    t.initStatus();
    t.initPriority();
    t.initCompany();
    t.initTypeSelect();
    t.initAssignUserSelect(null);
    t.initProjectSelect();
    t.initChangeSelect();
    t.initTicketSelect();
    t.initDepartmentSelect();
    t.initProblemCategorySelect();
    t.initSubCategorySelect();
    t.initSummernote();
    t.initDatepickers(t.editMode);
    if (Array.isArray(t.config.company_defulte) && t.config.company_defulte.length === 1 && config.company_defulte[0].id) {
      let option = new Option(t.config.company_defulte[0].text, t.config.company_defulte[0].id, true, true);
      t.frmEl.company.append(option).trigger('change');
    }
  });

  t.btn.submit.off("click").on("click", function (e) {
    t.handleSubmit(e);
  });

};