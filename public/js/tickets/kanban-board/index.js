var boardList = function (config) {
    var t = this;
    t.showDeletedUsers = false;
    t.config = config;
    t.content = $("section.content");
    t.table = t.content.find("#mytable");
    t.searchbox = t.table.find(".searchbox");
    t.searchbtn = t.table.find(".btn-searchbox");
    t.home = t.content.find("#home");
    t.pagebtns = t.content.find("#pagebtns3");
    t.pageBtmSummary = t.content.find("#page-btm-summary3");
    t.filterbtn = t.content.find(".btn-open-filter");
    t.mdl = $("section.content").find("#addKanbanFormMdl");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");
    t.mdl.frm = t.mdl.find("#addKanbanForm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.problem_category_id = t.mdl.frm.find("#problem_category_id");
    t.mdl.frmEl.sub_category_id = t.mdl.frm.find("#sub_category_id");
    t.mdl.frmEl.board_items_type = t.mdl.frm.find("#board_items_type");
    t.mdl.frmEl.item = t.mdl.frm.find("#item");
    t.mdl.frmEl.auto_comment_on_card_change = t.mdl.frm.find("#auto_comment_on_card_change");
    t.mdl.frmEl.access_to_all_department_technician = t.mdl.frm.find("#access_to_all_department_technician");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.description = t.mdl.frm.find("#description");

    // assigned to
    t.mdlAssignTo = t.content.find("#mdl-assign-to");
    t.mdlAssignTo.title = t.mdlAssignTo.find(".modal-title");
    t.frmAssignTo = t.mdlAssignTo.find("#frm-assign-to");
    t.frmAssignTo.frm = {};
    t.frmAssignTo.frm.id = t.frmAssignTo.find("#id");
    t.frmAssignTo.frm.token = t.frmAssignTo.find("#token");
    t.frmAssignTo.frm.assigned_to = t.frmAssignTo.find("#user_assigned_to");
    t.mdlAssignTo.btnSubmit = t.mdlAssignTo.find("#btnSubmit");

    // view card
    t.mdlViewCard = t.content.find("#viewKanbanMdl");
    t.mdlViewCard.div = t.mdlViewCard.find('#viewCard');
    t.searchData = t.content.find('.searchbox');
    t.timeline = t.content.find("#ticket_timeline");
    t.toggle_tiny_view = t.content.find('#toggle_tiny_view');
    t.toggle_tiny_view_read = t.content.find('#toggle_tiny_view_read');


    t.httpPostPath = "";
    t.data = {};

    // sr form
    t.mdlServiceRequest = t.content.find('#mdl-servicerequest');
    t.frmServiceRequest = t.mdlServiceRequest.find('#requested_form');
    t.frmServiceRequest.el = {};
    t.frmServiceRequest.el.form_title = t.frmServiceRequest.find(".form_title");
    t.frmServiceRequest.el.form_id = t.frmServiceRequest.find("#form_id");
    t.frmServiceRequest.el.for_action = t.frmServiceRequest.find("#for_action");
    t.frmServiceRequest.el.request_id = t.frmServiceRequest.find("#request_id");
    t.frmServiceRequest.el.tmp_id = t.frmServiceRequest.find("#tmp_id");
    t.frmServiceRequest.el.field_values = t.frmServiceRequest.find('#field_values');
    t.frmServiceRequest.btnSubmit = t.mdlServiceRequest.find("#btnSubmit");

    //custom Card
    t.mdlCustomCard = t.content.find("#mdl-customCard");
    t.frmCustomCard = t.mdlCustomCard.find("#frm-customCard");
    t.mdlCustomCard.modaltitle = t.frmCustomCard.find('.modal-title');
    t.mdlCustomCard.ticketOption = t.frmCustomCard.find('#ticketOption');
    t.mdlCustomCard.customTaskOption = t.frmCustomCard.find('#customTaskOption');
    t.mdlCustomCard.ticketIdGroup = t.frmCustomCard.find('#ticketIdGroup');
    t.mdlCustomCard.customTaskFields = t.frmCustomCard.find('#customTaskFields');
    t.mdlCustomCard.title = t.frmCustomCard.find('#Title');
    t.mdlCustomCard.description = t.frmCustomCard.find('#description');
    t.mdlCustomCard.referenceTicket = t.frmCustomCard.find('#referenceTicket');
    t.mdlCustomCard.assign_to = t.frmCustomCard.find('#assign_to');
    t.mdlCustomCard.ticketId = t.frmCustomCard.find('#ticketId');
    t.mdlCustomCard.tmp_id = t.frmCustomCard.find("#tmp_id");
    t.mdlCustomCard.id = t.frmCustomCard.find("#id");
    t.mdlCustomCard.status = t.frmCustomCard.find('#status');
    t.mdlCustomCard.priority = t.frmCustomCard.find('#priority');
    t.mdlCustomCard.expected_date = t.frmCustomCard.find('#expectedDate');
    t.mdlCustomCard.radioGroup = t.frmCustomCard.find("#radioGroup");
    t.mdlCustomCard.btnSave = t.frmCustomCard.find('#btnSave');
    t.mdlCustomCard.card_attachment = t.mdlCustomCard.find("#card_attachments");
    t.mdlCustomCard.card_dropper_cover = t.mdlCustomCard.find("#card-dropper-cover");
    t.mdlCustomCard.card_dropper = t.mdlCustomCard.find("#card-dropper");
    t.mdlCustomCard.department = t.frmCustomCard.find("#taskDepartment");
    t.mdlCustomCard.problem_category = t.frmCustomCard.find("#taskProblemCategory");
    t.mdlCustomCard.sub_category = t.frmCustomCard.find("#taskSubCategory");

    t.mdlFilterModal = t.content.find("#mdl-filterModal");
    t.frmFilterModal = t.mdlFilterModal.find("#frm-filterModal");
    t.frmFilterModal.department = t.frmFilterModal.find("#department");
    t.frmFilterModal.problem_category = t.frmFilterModal.find("#problemCategory");
    t.frmFilterModal.sub_category = t.frmFilterModal.find("#subCategory");
    t.frmFilterModal.btnFilter = t.mdlFilterModal.find("#btnFilter");
    t.frmFilterModal.btnClear = t.mdlFilterModal.find("#btnClear");

    t.filters = {
        wrapper: t.content.find("#advanceFilterModal")
    };
    // board_id
    t.boardId = t.content.find('#board_id');

    t.httpCall = true;
    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter"),
    t.filters.priorityID = t.filters.wrapper.find('#filter_by_priority_id');
    t.filters.assigned_to = t.filters.wrapper.find('#filter_by_assigned_to');
    t.filters.creator_id = t.filters.wrapper.find('#filter_by_creator_id');
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.prob_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.filter_by_date = t.filters.wrapper.find("#filter_by_date");
    t.filters.reportrange = t.filters.wrapper.find("#daterange");
    t.filters.filter_by_status = t.filters.wrapper.find("#filter_by_status_id");
    t.filters.filter_by_custom_field = t.filters.wrapper.find("#filter_by_custom_field");
    t.filters.filter_by_custom_field_value = t.filters.wrapper.find("#filter_by_custom_field_value");
    
    t.cardHistoryMdl = t.content.find("#mdl-history")
    t.cardHistoryMdl.title = t.cardHistoryMdl.find(".modal-title");
    t.cardHistoryMdl.board_details = t.cardHistoryMdl.find("#board_details");
    t.cardHistoryMdl.result = t.cardHistoryMdl.find("#result");
    let companyId = t.config.data && t.config.data.company_id ? t.config.data.company_id : (t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null);

    var problem_categories = [];
    var sub_categories = [];

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                var a = [];
                a.push("<a style='color:white' href='javascript:void(0)' class='btn dtActbtn edit-kanban' data-toggle='tooltip' data-original-title='" + config.translations.edit + "' data-id='" + d.id + "'><i class=\"fa fa-pencil\"></i></a>");
                a.push("<a style='color:white' href='" + config.url.member_kanban + "/" + d.id + "' class='btn dtActbtn' data-toggle='tooltip' data-original-title='" + config.translations.members + "' data-id='" + d.id + "'><i class=\"fa fa-user\"></i></a>");
                a.push("<a style='color:white' href='javascript:void(0)' class='btn dtActbtn delete-kanban' data-toggle='tooltip' data-original-title='" + config.translations.delete + "' data-id='" + d.id + "'><i class=\"fa fa-trash\"></i></a>");
                return '<div class="popup-toolbox">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            }
        },
    };

    t.load = function (e) {
        const icon = $('.btn-reload-list').find('i');
        icon.addClass("reload-rotate");

        t.loadCardList = new loadCardList(config);

        t.loadCardList.load()
            .then(() => {
                icon.removeClass("reload-rotate");
            })
            .catch(() => {
                icon.removeClass("reload-rotate");
            });
    };

    $(document).on('click', '.custom_field_info', function () {
        var id = $(this).attr("data-id");
        var url = t.config.url.getCustomFieldNote + '/' + id;
        $("#helpNoteContent").html('');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $("#helpNoteContent").html(response.help_note);
                $("#helpNoteDialog").css('z-index', 9999);
                $("#helpNoteDialog").modal("show");
            },
            error: function (xhr, status, error) {
                $("#helpNoteContent").html('<p class="text-danger">Failed to load data.</p>');
            }
        });
    });

    t.assignedTO = function (e, statusData) {
        var ticketID = $(this).data('id');
        e.preventDefault();
        t.statusDataForAssign = statusData || {};
        t.statusGroupedForAssign = statusData && statusData.groupBy ? true : false;
        t.mdlAssignTo.title.html(config.translations.Assign_Ticket_To);
        t.frmAssignTo.frm.id.val(ticketID);
        t.frmAssignTo.frm.token.val(t.config.token);
        t.mdlAssignTo.modal("show");
        t.frmAssignTo.frm.assigned_to.empty();
        globalTicketID = ticketID;
        t.initializeDropdown(globalTicketID);
    };

    t.buildTimelineItem = function (toneClass, ribbonText, ribbonIcon, actionHtml, avatarHtml, commenterName, updatedAt, updatedBy) {
        var userLink = (updatedBy && updatedBy != 0) ? (t.config.url.user_info + '/' + updatedBy) : null;
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

                        <div class="t-text ">
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
    };

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

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
            return '<span class="tkt-hst-avatar-initials" style="display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #a78bfa, #7c3aed); color: white; font-size: 10px; font-weight: 600; text-transform: uppercase;">' + initials + '</span>';
        }
    }


    t.openMdlTktHistory = function (e, details = 0) {
        e.preventDefault();
        if (details == 1) {
            t.data.id = $('.viewKanbanCardHistory').attr('data-id');
        } else {
            t.data.id = $(e.currentTarget).data("id");
        }
        var url = t.config.url.get_tickets;
        var history_url = t.config.url.ticket_history;
        if (t.config.archive == true) {
            url = t.config.url.get_archived_tickets;
            history_url = t.config.url.ticket_history_archived;
        }
        var ticketId = t.data.id;
        var http = $.ajax({
            url: url,
            type: "POST",
            data: {
                id: ticketId,
                _token: t.config.token
            },
            success: function(data) {
                var sub_cat = data.data.sub_cat == null ? "" : data.data.sub_cat;
                var asset_tag = data.data.asset_tag == null ? "" : data.data.asset_tag;
                
                // Update ticket details in the info card
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
                
                if(sub_cat != '' && sub_cat != null && sub_cat != undefined){
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
        
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        
        // Second AJAX call for ticket history
        var http2 = $.ajax({
            url: history_url,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": ticketId
            }
        });
        
        http2.done(function(response) {
            if (typeof response == "object") {
                if (response.status == "success") {
                    $('#ticket_history_container').html('');
                    $('#loadKanbanCardHistory').html('');
                    var ticket_history = '';        
                    if (response.data.length > 0) {
                        $.each(response.data, function(index, value) {
                            var actionHtml = '';
                            var toneClass = 'tone-normal';
                            var ribbonText = 'Update';
                            var ribbonIcon = 'bi bi-chat-left-text';

                            switch(parseInt(value.action_type)) {

                                case 1: // Assigned Ticket
                                    toneClass = 'tone-assigned';
                                    ribbonText = 'Assigned';
                                    ribbonIcon = 'bi bi-person-check-fill';
                                    if (value.assingned_user_fullname != null && value.assingned_user_fullname != '') {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.assigned_to + '" target="_blank">' + value.assingned_user_fullname + '</a></span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.assigned_to + '" target="_blank">' + value.assingned_user_fullname + '</a></span> From <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.old_assigned_to + '">' + value.old_assigned_user_fullname + '</a></span>';
                                        }
                                    } else if (value.assigned_to == 0 || value.assigned_to == null) {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span> From <span class="fw-bold" ><a href="' + t.config.url.user_info + '/' + value.old_assigned_to + '">' + value.old_assigned_user_fullname + '</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Assigned Ticket Updated';
                                    }
                                    break;

                                case 2: // Status Change
                                    if (value.old_status_name != null && value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold" >' + value.old_status_name + '</span> &gt; <span class="fw-bold" >' + value.name + '</span>';
                                    } else if (value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold" >' + value.name + '</span>';
                                    } else {
                                        actionHtml = 'Status Updated';
                                    }
                                    var status = (value.name || '').toLowerCase().trim();
                                    switch (status) {
                                        case 'open':
                                            toneClass = 'tone-open'; ribbonText = 'Open'; ribbonIcon = 'bi bi-arrow-repeat';
                                            break;
                                        case 'in progress':
                                        case 'inprogress':
                                            toneClass = 'tone-open'; ribbonText = 'In Progress'; ribbonIcon = 'bi bi-arrow-repeat';
                                            break;
                                        case 'waiting for user':
                                            toneClass = 'tone-waiting'; ribbonText = 'Waiting for User'; ribbonIcon = 'bi bi-person-fill';
                                            break;
                                        case 'hold':
                                        case 'on hold':
                                            toneClass = 'tone-hold'; ribbonText = 'On Hold'; ribbonIcon = 'bi bi-pause-circle';
                                            break;
                                        case 'resolved':
                                            toneClass = 'tone-resolved'; ribbonText = 'Resolved'; ribbonIcon = 'bi bi-check2-square';
                                            break;
                                        case 'closed':
                                            toneClass = 'tone-closed'; ribbonText = 'Closed'; ribbonIcon = 'bi bi-lock-fill';
                                            break;
                                        case 'reopened':
                                        case 're-opened':
                                            toneClass = 'tone-reopen'; ribbonText = 'Reopen'; ribbonIcon = 'bi bi-check-lg';
                                            break;
                                        default:
                                            toneClass = 'tone-open'; ribbonText = value.name || 'Status'; ribbonIcon = 'bi bi-chat-left-text';
                                            break;
                                    }
                                    break;

                                case 3: // feedback
                                    toneClass = 'tone-feedback';
                                    ribbonText = 'Feedback';
                                    ribbonIcon = 'bi bi-chat-left-text';
                                    if (value.old_feedback != null && value.feedback != null) {
                                        actionHtml = 'Feedback Changed From <span class="fw-bold">' + value.old_feedback + '</span > To <span class="fw-bold">' + value.feedback + '</span>';
                                    } else if (value.feedback != null) {
                                        actionHtml = 'Feedback : <span class="fw-bold">' + value.feedback + '</span>';
                                    } else {
                                        actionHtml = 'Feedback Updated';
                                    }
                                    break;

                                case 4: // Added to Spam list
                                    toneClass = 'tone-spam'; ribbonText = 'Spam'; ribbonIcon = 'bi bi-slash-circle-fill';
                                    actionHtml = 'Ticket Marked As <span class="fw-bold">Spam</span>';
                                    break;

                                case 5: // Removed from Spam list
                                    toneClass = 'tone-success'; ribbonText = 'Spam Removed'; ribbonIcon = 'bi bi-shield-check';
                                    actionHtml = 'Ticket Removed From <span class="fw-bold">Spam List</span>';
                                    break;

                                case 6: // Ticket Merge
                                    toneClass = 'tone-merge'; ribbonText = 'Merged'; ribbonIcon = 'bi bi-diagram-3-fill';
                                    if (value.merge_primary != null) {
                                        actionHtml = 'Ticket Merged With <span class="fw-bold">' + value.merge_primary + '</span> ticket';
                                    } else if (value.merged_ids != null) {
                                        actionHtml = 'Primary Ticket Merged With <span class="fw-bold">' + value.merged_ids + '</span> tickets';
                                    } else {
                                        actionHtml = 'Ticket Merge Operation Performed';
                                    }
                                    break;

                                case 7: // Add Comment
                                    if (value.is_note == 1) {
                                        actionHtml = 'Marked this as <span class="fw-bold">Note</span> and commented on ticket';
                                        toneClass = 'tone-note'; ribbonText = 'Note'; ribbonIcon = 'bi bi-stickies';
                                    } else if (value.auto_response == 1) {
                                        actionHtml = 'Auto responded by <span class="fw-bold">MATI-AI</span>';
                                        toneClass = 'tone-ai'; ribbonText = 'AI Reply'; ribbonIcon = 'bi bi-robot';
                                    } else {
                                        actionHtml = 'Commented on ticket';
                                        toneClass = 'tone-comment'; ribbonText = 'Comment'; ribbonIcon = 'bi bi-chat-left-text';
                                    }
                                    break;

                                case 8: // Changes Priority
                                    toneClass = 'tone-priority'; ribbonText = 'Priority'; ribbonIcon = 'bi bi-flag';
                                    if (value.old_priority_name != null && value.priority_name != null) {
                                        actionHtml = 'Priority Changed From <span class="fw-bold">' + value.old_priority_name + '</span> To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else if (value.priority_name != null) {
                                        actionHtml = 'Priority Changed To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else {
                                        actionHtml = 'Priority Updated';
                                    }
                                    break;

                                case 9: // Changes TAT
                                    toneClass = 'tone-tat'; ribbonText = 'TAT'; ribbonIcon = 'bi bi-clock';
                                    if (value.tat_changed != null && value.tat != null) {
                                        actionHtml = 'Changed TAT From <span class="fw-bold">' + value.tat_changed + ' hours</span> To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else if (value.tat != null) {
                                        actionHtml = 'Changed TAT To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else {
                                        actionHtml = 'TAT Updated';
                                    }
                                    break;

                                case 10: // Ticket Transfer Department
                                    toneClass = 'tone-department'; ribbonText = 'Department'; ribbonIcon = 'bi bi-building';
                                    if (value.old_dept_name != null && value.dept_name != null) {
                                        actionHtml = 'Department Changed From <span class="fw-bold">' + value.old_dept_name + '</span> To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else if (value.dept_name != null) {
                                        actionHtml = 'Department Changed To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else {
                                        actionHtml = 'Department Transferred';
                                    }
                                    break;

                                case 11: // Ticket Transfer Category
                                    toneClass = 'tone-category'; ribbonText = 'Category'; ribbonIcon = 'bi bi-folder2-open';
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed From <span class="fw-bold">' + value.old_pbm_cat_name + '</span> To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else if (value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Category Transferred';
                                    }
                                    break;

                                case 12: // Ticket Transfer Sub Category
                                    toneClass = 'tone-subcategory'; ribbonText = 'Sub Category'; ribbonIcon = 'bi bi-diagram-2-fill';
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed From <span class="fw-bold">' + value.old_sub_cat_name + '</span> To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else if (value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Sub Category Transferred';
                                    }
                                    break;

                                case 14: // New Ticket History
                                    toneClass = 'tone-newticket'; ribbonText = 'New Ticket'; ribbonIcon = 'bi bi-ticket-perforated-fill';
                                    if (value.old_change_creator_user_fullname != null && value.updated_by != value.old_change_creator_id) {
                                        actionHtml = 'New ticket created for <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.old_change_creator_id + '" target="_blank">' + value.old_change_creator_user_fullname + '</a></span>';
                                    } else {
                                        actionHtml = 'New ticket created';
                                    }
                                    break;

                                case 15: // Ticket Type update
                                    toneClass = 'tone-tickettype'; ribbonText = 'Ticket Type'; ribbonIcon = 'bi bi-list-check';
                                    actionHtml = value.ticket_type_custom_fields || 'Ticket Type Updated';
                                    break;

                                case 16: // Ticket creator change
                                    toneClass = 'tone-creator'; ribbonText = 'Creator'; ribbonIcon = 'bi bi-person-fill-gear';
                                    if (value.creator_user_fullname != null) {
                                        if (value.old_change_creator_user_fullname != null) {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.change_creator_id + '" target="_blank">' + value.creator_user_fullname + '</a></span> From <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.old_change_creator_id + '">' + value.old_change_creator_user_fullname + '</a></span>';
                                        } else {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.change_creator_id + '" target="_blank">' + value.creator_user_fullname + '</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Ticket creator changed';
                                    }
                                    break;

                                case 17: // custom field update
                                case 20: // custom history
                                case 21: // custom remove history
                                    toneClass = 'tone-custom'; ribbonText = 'Custom Field'; ribbonIcon = 'bi bi-sliders';
                                    if (value.custom_fields) {
                                        actionHtml = value.custom_fields;
                                    } else if (value.new_custom_field_values) {
                                        actionHtml = 'Custom form data updated';
                                    } else {
                                        actionHtml = 'Custom field updated';
                                    }
                                    break;

                                case 18: // add block calendar
                                    toneClass = 'tone-calendar'; ribbonText = 'Calendar'; ribbonIcon = 'bi bi-calendar2-day';
                                    actionHtml = 'Event calendar booked <i class="bi bi-calendar"></i>';
                                    break;

                                case 19: // device history
                                    toneClass = 'tone-device'; ribbonText = 'Device'; ribbonIcon = 'bi bi-laptop';
                                    if (value.device_tag != null) {
                                        if (value.old_device_tag != null) {
                                            actionHtml = 'Device Changed To <span class="fw-bold"><a href="' + t.config.url.device_info + '/' + value.device_id + '" target="_blank">' + value.device_tag + '</a></span> From <span class="fw-bold"><a href="' + t.config.url.device_info + '/' + value.old_device_id + '">' + value.old_device_tag + '</a></span>';
                                        } else {
                                            actionHtml = 'Device Changed To <span class="fw-bold"><a href="' + t.config.url.device_info + '/' + value.device_id + '" target="_blank">' + value.device_tag + '</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Device information updated';
                                    }
                                    break;

                                case 22: // task operations
                                    toneClass = 'tone-task'; ribbonText = 'Task'; ribbonIcon = 'bi bi-list-check';
                                    if (value.remarks != null) {
                                        var parts = value.remarks.split(' ');
                                        var taskId = parts[0].replace('#', '');
                                        parts.shift();
                                        var message = parts.join(' ');
                                        actionHtml = '<span class="fw-bold">#' + taskId + '</span> ' + message;
                                    } else {
                                        actionHtml = 'Task operation performed';
                                    }
                                    break;

                                case 23: // Ticket Edit Category
                                    toneClass = 'tone-category'; ribbonText = 'Category'; ribbonIcon = 'bi bi-folder2-open';
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml = 'Ticket Edit Category From <span class="fw-bold">' + value.old_pbm_cat_name + '</span> To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else if (value.pbm_cat_name != null) {
                                        actionHtml = 'Ticket Edit Category To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Category Edited';
                                    }
                                    break;

                                case 24: // Ticket Edit Sub Category
                                    toneClass = 'tone-category'; ribbonText = 'Sub Category'; ribbonIcon = 'bi bi-diagram-3';
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml = 'Ticket Edit Sub Category From <span class="fw-bold">' + value.old_sub_cat_name + '</span> To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else if (value.sub_cat_name != null) {
                                        actionHtml = 'Ticket Edit Sub Category To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Sub Category Edited';
                                    }
                                    break;

                                default:
                                    actionHtml = 'Update performed on ticket';
                                    break;
                            }

                            var commenterName = value.commenter == "System" ? "System" : value.commenter;

                            var avatarHtml = t.getAvatar(commenterName, value.commenter_avatar);

                            ticket_history += t.buildTimelineItem(toneClass, ribbonText, ribbonIcon, actionHtml, avatarHtml, commenterName, value.updated_at_format, value.updated_by);
                        });
                    } else {
                        ticket_history = '<div class="text-center p-4"><p>' + (config.translations.Ticket_History_Not_Available || 'No history available') + '</p></div>';
                    }
                    if (details == 1) {
                        $('#loadKanbanCardHistory').html(ticket_history);
                    }else{
                        $('#ticket_history_container').append(ticket_history);
                        $('#ticketHistoryModal').modal("show");
                    }
                }
            }
        });
        
        http2.fail(function(xhr, status, error) {
            if(xhr.status === 419) {
                sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
            } else {
                sweetAlert('center', 'error', {msg: config.translations.something_went_wrong || 'Something went wrong'});
            }
        });
        
        http2.always(function() {
            t.httpCall = true;
        });
    };

    $(document).on('click', '.viewKanbanCardHistory', function (e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        let type = $(this).attr('data-task-type');
        let details = 1;
        if (id && type !== undefined && type !== "undefined" && type !== "") {
            t.loadCardHistory(id, details);
        } else {
            t.openMdlTktHistory(e, details);
        }
    });

    t.getHistoryIcon = function (type) {
        const icons = {
            "Created": `
                <svg width="18px" height="18px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.9991 11C21.99 7.8857 21.8915 6.23467 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H11.5"
                    stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M17.5 14V20" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M14 17H21" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M10 16H6" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M2 10L22 10" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                </svg>`,
            "Clone": `<svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2029_16)">
                        <path d="M9.00429 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.0725 8.14014" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17251 10.6118H3.89835" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M11.3559 12.9867C10.607 12.9867 9.99998 12.3675 9.99998 11.6037V8.83761C9.99998 8.07379 10.607 7.45459 11.3559 7.45459H13.5253C14.2742 7.45459 14.8812 8.07379 14.8812 8.83761" stroke="#1C274C" stroke-linecap="round"/>
                        <path d="M16.5083 10.4646C16.5083 9.70074 15.9013 9.08154 15.1524 9.08154H12.983C12.2341 9.08154 11.6271 9.70074 11.6271 10.4646V13.2306C11.6271 13.9944 12.2341 14.6136 12.983 14.6136H15.1524C15.9013 14.6136 16.5083 13.9944 16.5083 13.2306V10.4646Z" stroke="#1C274C"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2029_16">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>`,
            "Imported": `
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <!-- Downward arrow -->
                        <path d="M19 14V20M19 20L21 18M19 20L17 18" 
                                stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>

                        <!-- Main card/container -->
                        <path d="M22 12C22 8.22876 22 6.34315 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H14" 
                                stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                        <!-- Inside lines -->
                        <path d="M10 16H6" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M13 16H12.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                        <!-- Divider -->
                        <path d="M2 10H22" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>`,

            "Archived": `<svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2030_32)">
                        <path d="M8 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571V6.62893" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17249 10.6118H3.89833" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M9.49455 11.0341C9.49455 9.34645 9.49455 8.50264 10.0188 7.97837C10.5431 7.4541 11.3869 7.4541 13.0745 7.4541C14.7621 7.4541 15.6059 7.4541 16.1302 7.97837C16.6545 8.50264 16.6545 9.34645 16.6545 11.0341" stroke="#1C274C"/>
                        <path d="M9.49457 11.7502C9.49457 10.7477 9.49457 10.2465 9.68966 9.86357C9.86127 9.52677 10.1351 9.25294 10.4719 9.08133C10.8548 8.88623 11.356 8.88623 12.3585 8.88623H13.7905C14.793 8.88623 15.2942 8.88623 15.6771 9.08133C16.0139 9.25294 16.2878 9.52677 16.4594 9.86357C16.6545 10.2465 16.6545 10.7477 16.6545 11.7502C16.6545 12.7527 16.6545 13.2539 16.4594 13.6368C16.2878 13.9736 16.0139 14.2475 15.6771 14.419C15.2942 14.6142 14.793 14.6142 13.7905 14.6142H12.3585C11.356 14.6142 10.8548 14.6142 10.4719 14.419C10.1351 14.2475 9.86127 13.9736 9.68966 13.6368C9.49457 13.2539 9.49457 12.7527 9.49457 11.7502Z" stroke="#1C274C"/>
                        <path d="M13.0745 10.6763V12.8242M13.0745 12.8242L13.9695 11.9293M13.0745 12.8242L12.1795 11.9293" stroke="#1C274C" stroke-width="0.550056" stroke-linecap="round" stroke-linejoin="round"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2030_32">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    `,
            "Comment Added": `<svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.9363 6.86578C17.9363 3.57907 17.7957 2.69574 16.7916 1.67469C15.7875 0.653641 14.1712 0.653641 10.9389 0.653641H7.51044C4.27807 0.653641 2.6619 0.653641 1.65773 1.67469C0.653564 2.69574 0.653564 4.33909 0.653564 7.62581C0.653564 10.9125 0.653564 12.5559 1.65773 13.5769C2.6619 14.598 2.98858 14.5779 6.22095 14.5779H7.51853" stroke="#1C274C" stroke-width="1.30728" stroke-linecap="round"/>
                        <path d="M7.51047 11.1119H4.08203" stroke="#1C274C" stroke-width="1.30728" stroke-linecap="round"/>
                        <path d="M0.653564 5.88278H17.7957" stroke="#1C274C" stroke-width="1.30728" stroke-linecap="round"/>
                        <path d="M16.6446 7.76083C17.4063 7.76083 18.0238 8.34622 18.0238 9.06833V13.4266C18.0238 14.1488 17.4063 14.7341 16.6446 14.7341H11.2812L9.74879 15.8237C9.36996 16.093 8.82935 15.8368 8.82935 15.3879V9.06833C8.82935 8.34622 9.44682 7.76083 10.2085 7.76083H16.6446ZM16.6446 8.63249H10.2085C9.95461 8.63249 9.74879 8.82762 9.74879 9.06833V14.7341L10.7295 14.0368C10.8887 13.9237 11.0822 13.8625 11.2812 13.8625H16.6446C16.8985 13.8625 17.1043 13.6673 17.1043 13.4266V9.06833C17.1043 8.82762 16.8985 8.63249 16.6446 8.63249ZM12.9668 11.6833C13.2207 11.6833 13.4266 11.8784 13.4266 12.1191C13.4266 12.3427 13.2491 12.5269 13.0204 12.552L12.9668 12.555H11.5877C11.3338 12.555 11.1279 12.3598 11.1279 12.1191C11.1279 11.8956 11.3054 11.7114 11.5341 11.6862L11.5877 11.6833H12.9668ZM15.2654 9.93999C15.5193 9.93999 15.7252 10.1351 15.7252 10.3758C15.7252 10.6165 15.5193 10.8116 15.2654 10.8116H11.5877C11.3338 10.8116 11.1279 10.6165 11.1279 10.3758C11.1279 10.1351 11.3338 9.93999 11.5877 9.93999H15.2654Z" fill="#1C274C"/>
                        </svg> `,
            "Expected Date": `<svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.39332 15.0596H7.74802C4.41344 15.0596 2.74616 15.0596 1.71023 14.0062C0.674316 12.953 0.674316 11.2576 0.674316 7.86695C0.674316 4.47631 0.674316 2.78099 1.71023 1.72765C2.74616 0.674311 4.41344 0.674311 7.74802 0.674311H11.2849C14.6194 0.674311 16.2868 0.674311 17.3226 1.72765C18.3586 2.78099 18.5618 3.87378 18.5618 7.26443V8.29704" stroke="#1C274C" stroke-width="1.34862" stroke-linecap="round"/>
                        <path d="M7.74828 11.4633H4.21143" stroke="#1C274C" stroke-width="1.34862" stroke-linecap="round"/>
                        <path d="M0.674316 6.0688H18.3586" stroke="#1C274C" stroke-width="1.34862" stroke-linecap="round"/>
                        <path d="M16.8335 9.39612V8.60345C16.8335 8.49834 16.7917 8.39753 16.7174 8.3232C16.6431 8.24888 16.5423 8.20712 16.4372 8.20712C16.3321 8.20712 16.2312 8.24888 16.1569 8.3232C16.0826 8.39753 16.0408 8.49834 16.0408 8.60345V9.39612H12.8702V8.60345C12.8702 8.49834 12.8284 8.39753 12.7541 8.3232C12.6798 8.24888 12.579 8.20712 12.4738 8.20712C12.3687 8.20712 12.2679 8.24888 12.1936 8.3232C12.1193 8.39753 12.0775 8.49834 12.0775 8.60345V9.39612H10.4922V15.9543H18.4188V9.39612H16.8335ZM17.433 14.9448L11.5182 14.9685V10.5324H17.433V14.9448ZM16.4372 11.3778H14.0592V13.7558H16.4372V11.3778ZM15.7634 13.082H14.7329V12.0515H15.7634V13.082Z" fill="#1C274C"/>
                    </svg>`,
            "Title": `<svg width="18px" height="18px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.9991 11C21.99 7.8857 21.8915 6.23467 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H11.5"
                            stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                        <path d="M15 14H20" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M17.5 14V20" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                        <path d="M10 16H6" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>

                        <path d="M2 10L22 10" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>`,
            "Description": `<svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.765 7.34399C18.765 3.91621 18.5596 2.81145 17.5123 1.74657C16.4651 0.681694 14.7795 0.681694 11.4084 0.681694H7.83282C4.46172 0.681694 2.77618 0.681694 1.7289 1.74657C0.681641 2.81145 0.681641 4.52533 0.681641 7.95311C0.681641 11.3809 0.681641 13.0948 1.7289 14.1596C2.77618 15.2245 4.46172 15.2245 7.83282 15.2245H9.41307" stroke="#1C274C" stroke-width="1.36339" stroke-linecap="round"/>
                        <path d="M7.83267 11.5888H4.25708" stroke="#1C274C" stroke-width="1.36339" stroke-linecap="round"/>
                        <path d="M0.681641 6.13526H18.5596" stroke="#1C274C" stroke-width="1.36339" stroke-linecap="round"/>
                        <path d="M10.8662 8.66125C10.6262 8.66125 10.4316 8.86553 10.4316 9.11753V9.34566C10.4316 9.59766 10.6262 9.80194 10.8662 9.80194H18.6881C18.9281 9.80194 19.1226 9.59766 19.1226 9.34566V9.11753C19.1226 8.86553 18.9281 8.66125 18.6881 8.66125H10.8662Z" fill="#1C274C"/>
                        <path d="M10.8662 12.7677C10.6262 12.7677 10.4316 12.972 10.4316 13.224V13.4522C10.4316 13.7042 10.6262 13.9084 10.8662 13.9084H18.6881C18.9281 13.9084 19.1226 13.7042 19.1226 13.4522V13.224C19.1226 12.972 18.9281 12.7677 18.6881 12.7677H10.8662Z" fill="#1C274C"/>
                        <path d="M13.0388 11.1708C13.0388 10.9188 13.2334 10.7145 13.4734 10.7145H18.688C18.928 10.7145 19.1225 10.9188 19.1225 11.1708V11.3989C19.1225 11.6509 18.928 11.8552 18.688 11.8552H13.4734C13.2334 11.8552 13.0388 11.6509 13.0388 11.3989V11.1708Z" fill="#1C274C"/>
                        <path d="M13.4734 14.821C13.2334 14.821 13.0388 15.0253 13.0388 15.2773V15.5054C13.0388 15.7574 13.2334 15.9617 13.4734 15.9617H18.688C18.928 15.9617 19.1225 15.7574 19.1225 15.5054V15.2773C19.1225 15.0253 18.928 14.821 18.688 14.821H13.4734Z" fill="#1C274C"/>
                    </svg>
                `,
            "Priority": `<svg width="18px" height="18px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.9991 11C21.99 7.8857 21.8915 6.23467 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H11.5" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M15.5 14V20M15.5 20L17.5 18M15.5 20L13.5 18M20 20V14M20 14L22 16M20 14L18 16" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 16H6" stroke="#1C274C" stroke-width="1.2" stroke-linecap="round"/>
                        <path d="M2 10L22 10" stroke="#1C274C" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>`,
            "Status": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2028_2)">
                        <path d="M7.69591 13.9222L5.94103 13.9222C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14412 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.0725 8.14014" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17251 10.6118H3.89835" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7869 9.48873C16.0401 9.38536 16.3292 9.5068 16.4325 9.75996C17.2594 11.7853 16.2879 14.0975 14.2626 14.9244C13.0936 15.4017 11.8287 15.2793 10.8106 14.7025C10.5727 14.5677 10.4891 14.2655 10.6239 14.0276C10.7587 13.7897 11.0608 13.7061 11.2987 13.8409C12.0643 14.2747 13.0119 14.3654 13.8883 14.0076C15.4072 13.3874 16.1359 11.6533 15.5157 10.1343C15.4123 9.88111 15.5338 9.59209 15.7869 9.48873ZM9.89136 12.0108C9.92403 12.1346 9.96512 12.2579 10.015 12.3801C10.0649 12.5023 10.1218 12.6191 10.1852 12.7304C10.3204 12.9681 10.2374 13.2704 9.9997 13.4056C9.76203 13.5409 9.45973 13.4578 9.32449 13.2202C9.24003 13.0717 9.16431 12.9164 9.0982 12.7544C9.03209 12.5925 8.97741 12.4285 8.93386 12.2634C8.86411 11.999 9.02191 11.7281 9.28632 11.6583C9.55073 11.5886 9.82161 11.7464 9.89136 12.0108ZM10.0219 9.0812C10.2582 9.21896 10.338 9.52212 10.2002 9.75834C10.0693 9.98274 9.96817 10.2235 9.89948 10.4741C9.82718 10.7378 9.55478 10.893 9.29106 10.8207C9.02734 10.7484 8.87216 10.476 8.94447 10.2122C9.03591 9.87871 9.17056 9.55822 9.34479 9.25947C9.48255 9.02326 9.78572 8.94344 10.0219 9.0812ZM13.8103 7.43629C14.1439 7.52773 14.4643 7.66239 14.7631 7.83661C14.9993 7.97437 15.0791 8.27754 14.9414 8.51375C14.8036 8.74997 14.5005 8.82979 14.2642 8.69203C14.0398 8.56117 13.799 8.45999 13.5485 8.3913C13.2848 8.319 13.1296 8.0466 13.2019 7.78288C13.2742 7.51916 13.5466 7.36398 13.8103 7.43629ZM12.3642 7.77814C12.434 8.04255 12.2762 8.31343 12.0117 8.38318C11.8879 8.41585 11.7646 8.45694 11.6424 8.50681C11.5203 8.55669 11.4034 8.61366 11.2921 8.67699C11.0545 8.81223 10.7522 8.72919 10.6169 8.49152C10.4817 8.25385 10.5647 7.95155 10.8024 7.81631C10.9508 7.73185 11.1062 7.65613 11.2681 7.59002C11.4301 7.52391 11.594 7.46924 11.7592 7.42568C12.0236 7.35593 12.2945 7.51373 12.3642 7.77814Z" fill="#1C274C"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2028_2">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>`,

            "Ticket Reference": `<svg width="20" height="17" viewBox="0 0 20 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18.8291 7.36902C18.8291 3.92956 18.623 2.82103 17.5721 1.75252C16.5213 0.684021 14.83 0.684021 11.4474 0.684021H7.85963C4.47704 0.684021 2.78576 0.684021 1.73491 1.75252C0.684082 2.82103 0.684082 4.54076 0.684082 7.98022C0.684082 11.4196 0.684082 13.1395 1.73491 14.2079C2.78576 15.2764 4.47704 15.2764 7.85963 15.2764H8.18408" stroke="#1C274C" stroke-width="1.36804" stroke-linecap="round"/>
                        <path d="M7.85975 11.6283H4.27197" stroke="#1C274C" stroke-width="1.36804" stroke-linecap="round"/>
                        <path d="M0.684082 6.15618H18.623" stroke="#1C274C" stroke-width="1.36804" stroke-linecap="round"/>
                        <path d="M13.5648 10.504V10.049M13.5648 12.5515V12.0965M13.5648 14.599V14.144M11.3808 8.68402H17.5688C18.0784 8.68402 18.3333 8.68402 18.5279 8.78321C18.6992 8.87045 18.8384 9.00966 18.9256 9.18089C19.0248 9.37555 19.0248 9.63038 19.0248 10.14V10.7315C18.1453 10.7315 17.4323 11.4445 17.4323 12.324C17.4323 13.2035 18.1453 13.9165 19.0248 13.9165V14.508C19.0248 15.0177 19.0248 15.2725 18.9256 15.4672C18.8384 15.6384 18.6992 15.7776 18.5279 15.8648C18.3333 15.964 18.0784 15.964 17.5688 15.964H11.3808C10.8712 15.964 10.6163 15.964 10.4217 15.8648C10.2504 15.7776 10.1112 15.6384 10.024 15.4672C9.9248 15.2725 9.9248 15.0177 9.9248 14.508V13.9165C10.8043 13.9165 11.5173 13.2035 11.5173 12.324C11.5173 11.4445 10.8043 10.7315 9.9248 10.7315V10.14C9.9248 9.63038 9.9248 9.37555 10.024 9.18089C10.1112 9.00966 10.2504 8.87045 10.4217 8.78321C10.6163 8.68402 10.8712 8.68402 11.3808 8.68402Z" stroke="#1C274C" stroke-width="0.91" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`,

            "Assigned To": `<svg width="20" height="17" viewBox="0 0 20 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.1905 8.34122C19.1905 4.81527 19.0911 2.89197 18.0139 1.79659C16.9367 0.70122 15.2028 0.70122 11.7352 0.70122H8.05716C4.58951 0.70122 2.85569 0.70122 1.77843 1.79659C0.701172 2.89197 0.701172 4.65494 0.701172 8.18089C0.701172 11.7068 0.701172 13.4699 1.77843 14.5652C2.85569 15.6606 4.58951 15.6606 8.05716 15.6606H9.09019" stroke="#1C274C" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M8.05714 11.9207H4.37915" stroke="#1C274C" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M0.701172 6.31099H19.0911" stroke="#1C274C" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M19.3593 15.9812C19.3593 15.1476 18.5603 14.4386 17.445 14.1757M16.4878 15.9812C16.4878 14.924 15.2021 14.0668 13.6162 14.0668C12.0303 14.0668 10.7446 14.924 10.7446 15.9812M16.4878 12.6311C17.545 12.6311 18.4022 11.7739 18.4022 10.7167C18.4022 9.65938 17.545 8.80229 16.4878 8.80229M13.6162 12.6311C12.5589 12.6311 11.7018 11.7739 11.7018 10.7167C11.7018 9.65938 12.5589 8.80229 13.6162 8.80229C14.6735 8.80229 15.5306 9.65938 15.5306 10.7167C15.5306 11.7739 14.6735 12.6311 13.6162 12.6311Z" stroke="#1C274C" stroke-width="0.957191" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`,
            "Sprint Move": `<svg width="21" height="17" viewBox="0 0 21 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.5005 8.35625C19.5005 4.75472 19.5005 2.95396 18.4001 1.8351C17.2998 0.716251 15.5287 0.716251 11.9868 0.716251H8.22998C4.688 0.716251 2.91701 0.716251 1.81666 1.8351C0.716309 2.95396 0.716309 4.75472 0.716309 8.35625C0.716309 11.9577 0.716309 13.7586 1.81666 14.8774C2.91701 15.9963 4.688 15.9963 8.22998 15.9963H10.7179" stroke="#1C274C" stroke-width="1.4325" stroke-linecap="round"/>
                        <path d="M8.23022 12.1762H4.47339" stroke="#1C274C" stroke-width="1.4325" stroke-linecap="round"/>
                        <path d="M0.716309 6.44626H19.5005" stroke="#1C274C" stroke-width="1.4325" stroke-linecap="round"/>
                        <path d="M16.4619 9.59266L19.4138 12.5445L16.4619 15.4964" stroke="#1C274C" stroke-width="1.18074"/>
                        <path d="M13.8642 9.59274L10.9124 12.5446L13.8642 15.4965" stroke="#1C274C" stroke-width="1.18074"/>
                    </svg>
                    `,
            "Attachment": `<svg width="20" height="17" viewBox="0 0 20 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.6673 15.5698H8.01064C4.56308 15.5698 2.83931 15.5698 1.76828 14.4808C0.697266 13.3918 0.697266 11.639 0.697266 8.1335C0.697266 4.62797 0.697266 2.87522 1.76828 1.78618C2.83931 0.697159 4.56308 0.697159 8.01064 0.697159H11.6673C15.1149 0.697159 16.8387 0.697159 17.9097 1.78618C18.9807 2.87522 18.9807 4.62797 18.9807 8.1335V9.58826" stroke="#1C274C" stroke-width="1.39431" stroke-linecap="round"/>
                        <path d="M8.01069 11.8517H4.354" stroke="#1C274C" stroke-width="1.39431" stroke-linecap="round"/>
                        <path d="M0.697266 6.27443H18.9807" stroke="#1C274C" stroke-width="1.39431" stroke-linecap="round"/>
                        <path d="M19.87 13.173C19.8891 13.5334 19.8009 13.8915 19.6165 14.2018C19.4321 14.5121 19.1597 14.7607 18.8339 14.9161C18.5082 15.0716 18.1436 15.1269 17.7863 15.075C17.4291 15.0231 17.0953 14.8664 16.8272 14.6247L14.1146 12.1823C13.9947 12.0743 13.9226 11.9232 13.9142 11.762C13.9057 11.6009 13.9616 11.443 14.0696 11.3231C14.1775 11.2032 14.3287 11.1311 14.4898 11.1227C14.651 11.1143 14.8088 11.1702 14.9287 11.2781L17.6413 13.7205C17.7006 13.7744 17.77 13.8161 17.8454 13.8431C17.9209 13.8702 18.0009 13.882 18.0809 13.878C18.161 13.874 18.2394 13.8543 18.3118 13.8198C18.3842 13.7854 18.4491 13.7371 18.5027 13.6775C18.5563 13.618 18.5976 13.5484 18.6243 13.4728C18.651 13.3972 18.6624 13.3171 18.658 13.2371C18.6536 13.1571 18.6335 13.0787 18.5987 13.0065C18.564 12.9343 18.5153 12.8697 18.4555 12.8164L14.8387 9.55977C14.6606 9.39934 14.4527 9.27558 14.2267 9.19554C14.0008 9.1155 13.7613 9.08076 13.5219 9.0933C13.2826 9.10585 13.048 9.16543 12.8317 9.26865C12.6154 9.37186 12.4215 9.51669 12.2612 9.69486C11.9478 10.0593 11.7869 10.5306 11.8121 11.0107C11.8372 11.4907 12.0465 11.9426 12.3963 12.2724L15.1088 14.7148C15.1682 14.7682 15.2165 14.8329 15.2509 14.905C15.2853 14.9771 15.3051 15.0553 15.3093 15.135C15.3135 15.2148 15.3019 15.2947 15.2752 15.37C15.2486 15.4453 15.2073 15.5146 15.1539 15.574C15.1004 15.6333 15.0358 15.6816 14.9637 15.716C14.8916 15.7504 14.8134 15.7702 14.7336 15.7744C14.6538 15.7786 14.574 15.767 14.4987 15.7403C14.4234 15.7137 14.3541 15.6724 14.2947 15.619L11.5821 13.1766C10.3731 12.0868 10.2683 10.0861 11.357 8.88072C11.6242 8.58378 11.9473 8.3424 12.3079 8.17037C12.6684 7.99834 13.0593 7.89904 13.4583 7.87813C13.8572 7.85722 14.2563 7.91512 14.6329 8.04852C15.0094 8.18192 15.356 8.3882 15.6528 8.65559L19.2696 11.9121C19.4477 12.0725 19.5924 12.2664 19.6954 12.4827C19.7985 12.6991 19.8578 12.9336 19.87 13.173Z" fill="#1C274C"/>
                    </svg>`,
            "Department": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2032_22)">
                        <path d="M8 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571V6.78556" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17248 10.6118H3.89832" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M16.9764 7.79395H9.56935C9.29475 7.79395 9.07249 7.98121 9.07249 8.21132V14.433C9.07249 14.6631 9.29475 14.8503 9.56935 14.8503H16.9761C17.2504 14.8503 17.473 14.6631 17.473 14.433V8.21132C17.4733 7.98121 17.2504 7.79395 16.9764 7.79395ZM16.4796 14.0164H10.0662V8.62869H16.4793L16.4796 14.0164ZM15.6684 12.8275L10.8585 12.808L13.2815 9.73557L15.6684 12.8275Z" fill="#1C274C"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2032_22">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
            `,
            "Category": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.00429 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.1008 7.34862" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17251 10.6118H3.89835" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#1C274C" stroke-width="1.24845" stroke-linecap="round"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.7271 7.4541C10.4945 7.4541 10.3059 7.64267 10.3059 7.87528C10.3059 8.10789 10.4945 8.29645 10.7271 8.29645H15.7813C16.0139 8.29645 16.2024 8.10789 16.2024 7.87528C16.2024 7.64267 16.0139 7.4541 15.7813 7.4541H10.7271ZM9.96077 8.71763C9.44025 8.71763 9.04431 9.18503 9.12988 9.69847L9.83184 13.9102C9.89953 14.3164 10.251 14.6141 10.6627 14.6141H15.8456C16.2574 14.6141 16.6088 14.3164 16.6765 13.9102L17.3785 9.69847C17.4641 9.18503 17.0681 8.71763 16.5476 8.71763H9.96077ZM9.96077 9.55998H16.5476L15.8456 13.7717H10.6627L9.96077 9.55998Z" fill="#1C274C"/>
                    </svg>`,

        };

        return icons[type] || `
            <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <circle cx="10" cy="10" r="8" stroke="black" fill="currentColor"/>
            </svg>`;
    };

    t.formatDate = function (dateStr) {
        const date = new Date(dateStr);
        const day = String(date.getDate()).padStart(2, '0');
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const month = monthNames[date.getMonth()];
        const year = date.getFullYear();

        let hours = date.getHours();
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? "pm" : "am";

        hours = hours % 12 || 12;

        return `${day} ${month} ${year}, ${hours}:${minutes}${ampm}`;
    };

    t.getArrowSvg = function () {
        return `
            <svg viewBox="0 0 15 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M14.8172 6.69254L9.19219 12.3175C9.07491 12.4348 8.91585 12.5007 8.75 12.5007C8.58415 12.5007 8.42509 12.4348 8.30781 12.3175C8.19054 12.2003 8.12465 12.0412 8.12465 11.8753C8.12465 11.7095 8.19054 11.5504 8.30781 11.4332L12.8664 6.87535H0.625C0.45924 6.87535 0.300269 6.8095 0.183058 6.69229C0.0658481 6.57508 0 6.41611 0 6.25035C0 6.08459 0.0658481 5.92562 0.183058 5.80841C0.300269 5.6912 0.45924 5.62535 0.625 5.62535H12.8664L8.30781 1.06753C8.19054 0.95026 8.12465 0.7912 8.12465 0.625347C8.12465 0.459495 8.19054 0.300435 8.30781 0.18316C8.42509 0.0658846 8.58415 0 8.75 0C8.91585 0 9.07491 0.0658846 9.19219 0.18316L14.8172 5.80816C14.8753 5.86621 14.9214 5.93514 14.9529 6.01101C14.9843 6.08688 15.0005 6.16821 15.0005 6.25035C15.0005 6.33248 14.9843 6.41381 14.9529 6.48969C14.9214 6.56556 14.8753 6.63449 14.8172 6.69254Z" fill="currentColor" />
            </svg>`;
    };

    t.historyCardHeader = function (value, type) {
        return `
            <div id="card-history-content" class="history-content" style="margin-block:40px;padding-inline:30px;display:flex; flex-direction:column; gap:20px">
                <div class="history-timeline-item">
                    <div class="history-icon-container">
                        <div class="history-timeline-icon">
                            ${t.getHistoryIcon(type)}
                        </div>
                        <div class="history-timeline-line"></div>
                    </div>
                    <div class="history-detail-info-container">
                        <div class="history-detail-info-time-row">
                            <div class="history-detail-info-time-text">
                                <svg viewBox="0 0 17 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="currentColor" />
                                </svg>
                                <span class="history-detail-info-time-row-title">${t.formatDate(value.updated_at_formatted)}</span>
                            </div>
                            <a>
                                <svg class="history-detail-info-accrodian-icon" viewBox="0 0 26 26" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 0C10.4288 0 7.91543 0.762437 5.77759 2.19089C3.63975 3.61935 1.97351 5.64968 0.989572 8.02511C0.0056327 10.4006 -0.251811 13.0144 0.249797 15.5362C0.751405 18.0579 1.98953 20.3743 3.80762 22.1924C5.6257 24.0105 7.94208 25.2486 10.4638 25.7502C12.9856 26.2518 15.5994 25.9944 17.9749 25.0104C20.3503 24.0265 22.3807 22.3603 23.8091 20.2224C25.2376 18.0846 26 15.5712 26 13C25.9964 9.5533 24.6256 6.24882 22.1884 3.81163C19.7512 1.37445 16.4467 0.00363977 13 0ZM13 24C10.8244 24 8.69767 23.3549 6.88873 22.1462C5.07979 20.9375 3.66989 19.2195 2.83733 17.2095C2.00477 15.1995 1.78693 12.9878 2.21137 10.854C2.6358 8.72021 3.68345 6.7602 5.22183 5.22183C6.76021 3.68345 8.72022 2.6358 10.854 2.21136C12.9878 1.78692 15.1995 2.00476 17.2095 2.83732C19.2195 3.66989 20.9375 5.07979 22.1462 6.88873C23.3549 8.69767 24 10.8244 24 13C23.9967 15.9164 22.8367 18.7123 20.7745 20.7745C18.7123 22.8367 15.9164 23.9967 13 24ZM18.7075 10.2925C18.8005 10.3854 18.8742 10.4957 18.9246 10.6171C18.9749 10.7385 19.0008 10.8686 19.0008 11C19.0008 11.1314 18.9749 11.2615 18.9246 11.3829C18.8742 11.5043 18.8005 11.6146 18.7075 11.7075L13.7075 16.7075C13.6146 16.8005 13.5043 16.8742 13.3829 16.9246C13.2615 16.9749 13.1314 17.0008 13 17.0008C12.8686 17.0008 12.7385 16.9749 12.6171 16.9246C12.4957 16.8742 12.3854 16.8005 12.2925 16.7075L7.2925 11.7075C7.10486 11.5199 6.99945 11.2654 6.99945 11C6.99945 10.7346 7.10486 10.4801 7.2925 10.2925C7.48015 10.1049 7.73464 9.99944 8 9.99944C8.26537 9.99944 8.51987 10.1049 8.70751 10.2925L13 14.5862L17.2925 10.2925C17.3854 10.1995 17.4957 10.1258 17.6171 10.0754C17.7385 10.0251 17.8686 9.99921 18 9.99921C18.1314 9.99921 18.2615 10.0251 18.3829 10.0754C18.5043 10.1258 18.6146 10.1995 18.7075 10.2925Z" fill="currentColor" />
                                </svg>
                            </a>
                        </div>
                  
                                <div class="history-detail-info-modified-status">
                                    <div class="history-detail-info-modified-by">
                                        <div class="modfied-by-image">
                                            <img src="${value.updatedByUserImg}" alt="modified-by-image">
                                        </div>
                                        <span class="modfied-by-name">${value.updated_by}</span>
                                    </div>
                                    <span class="history-detail-info-modified-status-middot">ᐧ</span>`;
    };

    t.historyCardFooter = function () {
        return `

                            <div class="accordion-content">
                                <div class="accordion-content-inner">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
    };

    t.sanitizeText = function(input) {
        if (!input) return "";

        const temp = $("<div>").html(input);

        temp.find("img").each(function () {
            const file = $(this).data("filename");
            $(this).replaceWith(file ? ` 🖼️(${file}) ` : " 🖼️ ");
        });

        return temp.text().replace(/\s+/g, " ").trim();
    }

    t.generateHistoryContent = function(action, label, oldValue, newValue, action_id) {
        oldValue = t.sanitizeText(oldValue);
        newValue = t.sanitizeText(newValue);
        if(![9].includes(action_id)){
            oldValue = oldValue.length > 20 ? oldValue.slice(0, 20) + '...' : oldValue;
            newValue = newValue.length > 20 ? newValue.slice(0, 20) + '...' : newValue;
        }

        if (action === 'added') {
            return `
            <a class="">${label}</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">${newValue}</span>
            </div>`;
        }
        else if (action === 'removed') {
            return `
            <a class="">${label}</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">${oldValue}</span>
            </div>`;
        }
        else if (action === 'changed') {
            return `
            <a class="">${label}</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">${oldValue}</span>
                ${t.getArrowSvg()}
                <span class="changed-to">${newValue}</span>
            </div>`;
        }
        else if (action === 'created') {
            return `
            <a class="">Card Created</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Kanban Card Created Successfully</span>
            </div>`;
        }
        else if (action === 'comment') {
            return `
            <a class="">Comment Added</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Comment added on Kanban Card Successfully</span>
            </div>`;
        }
        else if (action === 'cloned') {
            return `
            <a class="">Card Cloned</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Kanban Card Cloned Successfully</span>
            </div>`;
        }
        else if (action === 'tkt-ref added') {
            return `
            <a class="">Ticket Reference Added</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Ticket Reference ${newValue} Added Successfully</span>
            </div>`;
        }
        else if (action === 'tkt-ref removed') {
            return `
            <a class="">Ticket Reference Added</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Ticket Reference ${newValue} Removed Successfully</span>
            </div>`;
        }
        else if (action === 'tkt-ref changed') {
            return `
            <a class="">Ticket Reference Added</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">${oldValue}</span>
                ${t.getArrowSvg()}
                <span class="changed-to">${newValue}</span>
            </div>`;
        }
        else if (action === 'imported') {
            return `
            <a class="">Card Imported</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Kanban Card Imported Successfully</span>
            </div>`;
        }
        else if (action === 'attachment') {
            return `
            <a class="">Attachment Added</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Kanban Attachment(s) Added Successfully</span>
            </div>`;
        }

        else if (action === 'attachment_removed') {
            return `
            <a class="">Attachment Removed</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Kanban Attachment(s) Removed Successfully</span>
            </div>`;
        }
        else if (action === 'archived_manual') {
            return `
            <a class="">Card Archived</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Card Archived Manually</span>
            </div>`;
        }
        else if (action === 'archived_auto') {
            return `
            <a class="">Card Archived</a>
            </div>
            <div class="history-detail-info-changed">
                <span class="changed-from">Card Archived Automatically</span>
            </div>`;
        }

        return '';
    };

    t.generateHistoryCard = function (value, type, action, label, oldValue, newValue) {
        const header = t.historyCardHeader(value, type);
        const content = t.generateHistoryContent(action, label, oldValue, newValue, value.action_id);
        const footer = t.historyCardFooter();

        return header + content + footer;
    };

    t.fullHistoryData = [];
    t.currentDisplayCount = 10;
    t.historyIncrement = 10;

    t.loadCardHistory = function (card_id, details) {
        const history_url = t.config.url.getCardHistory + '/' + card_id;

        t.cardHistoryMdl.result.html('');
        $('#card-history-content').html('');
        t.fullHistoryData = [];
        t.currentDisplayCount = 10;

        const loaderHTML = `
        <div id="history-loader" style="text-align:center;padding:40px;">
            <p style="margin-top:15px;font-weight:bold;">Loading history...</p>
        </div>`;

        if (details === 1) {
            $('#loadKanbanCardHistory').html(loaderHTML);
        } else {
            t.cardHistoryMdl.result.html(loaderHTML);
        }

        $.ajax({
            url: history_url,
            type: "POST",
            success: function (data) {
                $('#history-loader').remove();

                const historyData = data.data;

                if (historyData && historyData.length > 0) {
                    t.fullHistoryData = historyData;
                    t.renderHistoryWithPagination(details);
                } else {
                    const noDataMsg = '<div style="text-align:center;font-weight:bold;"><p>Card History Not Available.</p></div>';
                    if (details === 1) {
                        $('#loadKanbanCardHistory').html(noDataMsg);
                    } else {
                        t.cardHistoryMdl.result.html(noDataMsg);
                    }
                }

                if (details !== 1) {
                    t.cardHistoryMdl.title.text("Card History");
                    t.cardHistoryMdl.modal('show');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error loading card history:', error);
                const errorMsg = '<div style="text-align:center;font-weight:bold;color:red;"><p>Error loading card history.</p></div>';

                if (details === 1) {
                    $('#loadKanbanCardHistory').html(errorMsg);
                } else {
                    t.cardHistoryMdl.result.html(errorMsg);
                }
            }
        });
    };

    t.renderHistoryWithPagination = function (details) {
        let card_history = '';
        const displayCount = Math.min(t.currentDisplayCount, t.fullHistoryData.length);

        for (let i = 0; i < displayCount; i++) {
            card_history += t.processHistoryAction(t.fullHistoryData[i]);
        }

        if (t.fullHistoryData.length > t.currentDisplayCount) {
            const remaining = t.fullHistoryData.length - t.currentDisplayCount;
            card_history += `
                <div id="load-more-container" style="text-align:center;margin:20px 0;">
                    <button id="load-more-history">
                        <svg width="25px" height="25px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
                                <g id="Dribbble-Light-Preview" transform="translate(-140.000000, -6599.000000)">
                                    <g id="icons" transform="translate(56.000000, 160.000000)">
                                        <path d="M103.707257,6450.11258 C104.097581,6449.72125 104.097581,6449.0877 103.707257,6448.69736 C103.316933,6448.30603 102.685027,6448.30603 102.295701,6448.69736 L95.8578462,6455.14292 C95.5433906,6455.4582 94.9983342,6455.235 94.9983342,6454.78962 L94.9983342,6439.98385 C94.9983342,6439.43137 94.5590946,6439 94.0080486,6439 L94.0040555,6439 C93.4530095,6439 93.0017907,6439.43137 93.0017907,6439.98385 L93.0017907,6454.78962 C93.0017907,6455.235 92.4717084,6455.4582 92.1572528,6455.14292 L85.7004312,6448.66634 C85.3111053,6448.275 84.6811958,6448.275 84.2908715,6448.66634 L84.2918698,6448.66634 C83.9025438,6449.05667 83.9025438,6449.69022 84.2928681,6450.08156 L92.6034803,6458.41374 L92.6034803,6458.41374 C93.3831305,6459.19542 94.6479408,6459.19542 95.427591,6458.41374 C95.6072799,6458.23259 103.887944,6449.93143 103.707257,6450.11258" id="arrow_down-[#360]">
                                        </path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </button>
                </div>`;
        }

        if (details === 1) {
            $('#loadKanbanCardHistory').html(card_history);
        } else {
            t.cardHistoryMdl.result.html(card_history);
        }

        $('#load-more-history').off('click').on('click', function () {
            t.loadMoreHistory(details);
        });
    };

    t.loadMoreHistory = function (details) {
        t.currentDisplayCount += t.historyIncrement;
        t.renderHistoryWithPagination(details);
    };

    t.renderHistoryWithPagination = function (details) {
        let card_history = '';
        const displayCount = Math.min(t.currentDisplayCount, t.fullHistoryData.length);

        for (let i = 0; i < displayCount; i++) {
            card_history += t.processHistoryAction(t.fullHistoryData[i]);
        }

        if (t.fullHistoryData.length > t.currentDisplayCount) {
            const remaining = t.fullHistoryData.length - t.currentDisplayCount;
            card_history += `
                <div id="load-more-container" style="text-align:center;margin:20px 0;">
                    <button id="load-more-history">
                        <svg width="25px" height="25px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <g id="Page-1" stroke="none" stroke-width="1" fill="currentColor" fill-rule="evenodd">
                                <g id="Dribbble-Light-Preview" transform="translate(-140.000000, -6599.000000)">
                                    <g id="icons" transform="translate(56.000000, 160.000000)">
                                        <path d="M103.707257,6450.11258 C104.097581,6449.72125 104.097581,6449.0877 103.707257,6448.69736 C103.316933,6448.30603 102.685027,6448.30603 102.295701,6448.69736 L95.8578462,6455.14292 C95.5433906,6455.4582 94.9983342,6455.235 94.9983342,6454.78962 L94.9983342,6439.98385 C94.9983342,6439.43137 94.5590946,6439 94.0080486,6439 L94.0040555,6439 C93.4530095,6439 93.0017907,6439.43137 93.0017907,6439.98385 L93.0017907,6454.78962 C93.0017907,6455.235 92.4717084,6455.4582 92.1572528,6455.14292 L85.7004312,6448.66634 C85.3111053,6448.275 84.6811958,6448.275 84.2908715,6448.66634 L84.2918698,6448.66634 C83.9025438,6449.05667 83.9025438,6449.69022 84.2928681,6450.08156 L92.6034803,6458.41374 L92.6034803,6458.41374 C93.3831305,6459.19542 94.6479408,6459.19542 95.427591,6458.41374 C95.6072799,6458.23259 103.887944,6449.93143 103.707257,6450.11258" id="arrow_down-[#360]">
                                        </path>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </button>
                </div>`;

            // <svg width="50px" height="50px" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="m5.46967 9.46967c.29289-.29289.76777-.29289 1.06066 0l2.60589 2.60593c.60277.6027 1.02338 1.0225 1.37728 1.3229.3471.2947.5804.4302.7912.4987.4519.1469.9387.1469 1.3906 0 .2108-.0685.4441-.204.7912-.4987.3539-.3004.7745-.7202 1.3773-1.3229l2.6059-2.60593c.2929-.29289.7677-.29289 1.0606 0s.2929.76773 0 1.06063l-2.6286 2.6286c-.5747.5748-1.0383 1.0384-1.4444 1.3831-.4181.355-.8243.6278-1.2985.7818-.7531.2447-1.5645.2447-2.3176 0-.4742-.154-.8804-.4268-1.29852-.7818-.40608-.3447-.86966-.8083-1.44442-1.3831l-2.62859-2.6286c-.29289-.2929-.29289-.76774 0-1.06063z" fill="#A9A9A9" fill-rule="evenodd"/></svg>
        }

        if (details === 1) {
            $('#loadKanbanCardHistory').html(card_history);
        } else {
            t.cardHistoryMdl.result.html(card_history);
        }

        $('#load-more-history').off('click').on('click', function () {
            t.loadMoreHistory(details);
        });
    };

    t.loadMoreHistory = function (details) {
        t.currentDisplayCount += t.historyIncrement;
        t.renderHistoryWithPagination(details);
    };

    t.processHistoryAction = function (value) {
        switch (value.action_id) {
            case 1: // Card Created
                return t.generateHistoryCard(value, 'Created', 'created', 'Card', null, null);

            case 2: // Title Changed
                return t.processTitleChange(value);

            case 3: // Comment Added
                return t.generateHistoryCard(value, 'Comment Added', 'comment', 'Comment', null, null);

            case 4: // Description Changed
                return t.processDescriptionChange(value);

            case 5: // Status Changed
                return t.processStatusChange(value);

            case 6: // Priority Changed
                return t.processPriorityChange(value);

            case 7: // Expected Date Changed
                return t.processExpectedDateChange(value);

            case 8: // Reference Ticket Changed
                return t.processReferenceTicketChange(value);

            case 9: // Assigned Users Changed
                return t.processAssignedUsersChange(value);

            case 10: // Sprint Moved
                return t.processSprintMove(value);
            case 11: // Card Cloned
                return t.generateHistoryCard(value, 'Clone', 'cloned', 'Card Cloned', null, null);

            case 12: // Card Imported
                return t.generateHistoryCard(value, 'Imported', 'imported', 'Card Imported', null, null);

            case 13: // Attachment Added
                return t.generateHistoryCard(value, 'Attachment', 'attachment', 'Attachment Added', null, null);

            case 14: // Attachment Removed
                return t.generateHistoryCard(value, 'Attachment', 'attachment_removed', 'Attachment Removed', null, null);

            case 15: // Card Archived Manually
                return t.generateHistoryCard(value, 'Archived', 'archived_manual', 'Card Archived', null, null);

            case 16: // Card Archived Automatically
                return t.generateHistoryCard(value, 'Archived', 'archived_auto', 'Card Archived', null, null);
            case 17: // Department Changed
                return t.processDepartmentChange(value);

            case 18: // Problem Category Changed
                return t.processProblemCategoryChange(value);

            case 19: // Sub Category Changed
                return t.processSubCategoryChange(value);
            default:
                return '';
        }
    };

    t.processTitleChange = function (value) {
        const hasOld = value.old_title != null && value.old_title !== "";
        const hasNew = value.new_title != null && value.new_title !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Title', 'added', 'Card Title added', null, value.new_title);
        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(value, 'Title', 'removed', 'Card Title removed', value.old_title, null);
        }
        else if (hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Title', 'changed', 'Card Title changed', value.old_title, value.new_title);
        }

        return '';
    };

    t.processDepartmentChange = function (value) {
        const hasOld = value.old_dept_name != null && value.old_dept_name !== "";
        const hasNew = value.new_dept_name != null && value.new_dept_name !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Department', 'added', 'Department added', null, value.new_dept_name);
        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(value, 'Department', 'removed', 'Department removed', value.old_dept_name, null);
        }
        else if (hasOld && hasNew && value.old_dept_name !== value.new_dept_name) {
            return t.generateHistoryCard(value, 'Department', 'changed', 'Department changed', value.old_dept_name, value.new_dept_name);
        }

        return '';
    };

    t.processProblemCategoryChange = function (value) {
        const hasOld = value.old_pc_name != null && value.old_pc_name !== "";
        const hasNew = value.new_pc_name != null && value.new_pc_name !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Category', 'added', 'Problem Category added', null, value.new_pc_name);
        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(value, 'Category', 'removed', 'Problem Category removed', value.old_pc_name, null);
        }
        else if (hasOld && hasNew && value.old_pc_name !== value.new_pc_name) {
            return t.generateHistoryCard(value, 'Category', 'changed', 'Problem Category changed', value.old_pc_name, value.new_pc_name);
        }

        return '';
    };

    t.processSubCategoryChange = function (value) {
        const hasOld = value.old_spc_name != null && value.old_spc_name !== "";
        const hasNew = value.new_spc_name != null && value.new_spc_name !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Category', 'added', 'Sub Category added', null, value.new_spc_name);
        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(value, 'Category', 'removed', 'Sub Category removed', value.old_spc_name, null);
        }
        else if (hasOld && hasNew && value.old_spc_name !== value.new_spc_name) {
            return t.generateHistoryCard(value, 'Category', 'changed', 'Sub Category changed', value.old_spc_name, value.new_spc_name);
        }

        return '';
    };

    t.processExpectedDateChange = function (value) {

        const formatDate = (dateStr) => {
            if (!dateStr) return null;
            const d = new Date(dateStr);
            return d.toLocaleString("en-GB", {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            });
        };

        const oldDate = formatDate(value.old_expected_date);
        const newDate = formatDate(value.new_expected_date);

        const hasOld = !!value.old_expected_date;
        const hasNew = !!value.new_expected_date;

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(
                value,
                'Expected Date',
                'added',
                'Expected Date added',
                null,
                newDate
            );
        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(
                value,
                'Expected Date',
                'removed',
                'Expected Date removed',
                oldDate,
                null
            );
        }
        else if (hasOld && hasNew) {
            return t.generateHistoryCard(
                value,
                'Expected Date',
                'changed',
                'Expected Date changed',
                oldDate,
                newDate
            );
        }

        return '';
    };


    t.processDescriptionChange = function (value) {
        const hasOld = value.old_description != null && value.old_description !== "";
        const hasNew = value.new_description != null && value.new_description !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            console.log("added");
            return t.generateHistoryCard(value, 'Description', 'added', 'Description added', null, value.new_description);
        }
        else if (hasOld && !hasNew) {
            console.log("removed");
            return t.generateHistoryCard(value, 'Description', 'removed', 'Description removed', value.old_description, null);
        }
        else if (hasOld && hasNew) {
            console.log("changed");
            return t.generateHistoryCard(value, 'Description', 'changed', 'Description changed', value.old_description, value.new_description);
        }

        return '';
    };

    t.processStatusChange = function (value) {
        const hasOld = value.old_status != null && value.old_status !== "";
        const hasNew = value.new_status != null && value.new_status !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Status', 'added', 'Status added', null, value.new_status);
        }
        // else if (hasOld && !hasNew) {
        //     return t.generateHistoryCard(value, 'Status', 'removed', 'Status', value.old_status, null);
        // } 
        else if (hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Status', 'changed', 'Status changed', value.old_status, value.new_status);
        }

        return '';
    };

    t.processPriorityChange = function (value) {
        const hasOld = value.old_priority != null && value.old_priority !== "";
        const hasNew = value.new_priority != null && value.new_priority !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Priority', 'added', 'Priority added', null, value.new_priority);
        }
        // else if (hasOld && !hasNew) {
        //     return t.generateHistoryCard(value, 'Priority', 'removed', 'Priority Removed', value.old_priority, null);
        // } 
        else if (hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Priority', 'changed', 'Priority changed', value.old_priority, value.new_priority);
        }

        return '';
    };

    t.processReferenceTicketChange = function (value) {
        const hasOld = value.old_ticket_reference != null && value.old_ticket_reference !== "";
        const hasNew = value.new_ticket_reference != null && value.new_ticket_reference !== "";

        if (!hasOld && !hasNew) {
            return '';
        }

        if (!hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Ticket Reference', 'tkt-ref added', 'Reference Ticket added', null, '#' + value.new_ticket_reference);

        }
        else if (hasOld && !hasNew) {
            return t.generateHistoryCard(value, 'Ticket Reference', 'tkt-ref removed', 'Ticket Reference removed', '#' + value.old_ticket_reference, null);
        }
        else if (hasOld && hasNew) {
            return t.generateHistoryCard(value, 'Ticket Reference', 'tkt-ref changed', 'Ticket Reference changed', '#' + value.old_ticket_reference, '#' + value.new_ticket_reference);
        }

        return '';
    };

    t.processAssignedUsersChange = function (value) {
        const oldNames = value.old_assigned_to_names;
        const newNames = value.new_assigned_to_names;

        if (!oldNames && !newNames) {
            return '';
        }

        if (!oldNames && newNames) {
            return t.generateHistoryCard(value, 'Assigned To', 'added', 'User assigned to card', null, newNames);
        }

        if (oldNames && !newNames) {
            return t.generateHistoryCard(value, 'Assigned To', 'removed', 'User unassigned from card', oldNames, null);
        }

        const oldUsers = oldNames.split(',').map(u => u.trim());
        const newUsers = newNames.split(',').map(u => u.trim());
        const addedUsers = newUsers.filter(u => !oldUsers.includes(u));
        const removedUsers = oldUsers.filter(u => !newUsers.includes(u));

        let result = '';

        if (removedUsers.length > 0) {
            result += t.generateHistoryCard(value, 'Assigned To', 'removed', 'User unassigned from card', removedUsers.join(', '), null);
        }

        if (addedUsers.length > 0) {
            result += t.generateHistoryCard(value, 'Assigned To', 'added', 'User assigned to card', null, addedUsers.join(', '));
        }

        return result;
    };

    t.processSprintMove = function (value) {
        if (value.old_item_name === value.new_item_name) {
            return '';
        }

        return t.generateHistoryCard(value, 'Sprint Move', 'changed', 'Card Moved From sprint', value.old_item_name, value.new_item_name);
    };

    $(document).on('click', '.card-history', function () {
        let card_id = $(this).data('id');
        let details = 0;
        t.loadCardHistory(card_id, details);
    });

    $(document).on('click', '.history-detail-info-accrodian-icon', function () {

        const $icon = $(this);

        const $container = $icon.closest('.history-detail-info-container');
        const $accordionContent = $container.find('.accordion-content');

        $icon.toggleClass('expanded');
        $accordionContent.toggleClass('expanded');
    });

    t.card_history = function (value, type) {
        return '<div class="timeline-entry tiny-view tiny-view-on">' +
            '<div class="timeline-stat">' +
            '<div class="timeline-icon"></div>' +
            '<div class="timeline-time">' + value.updated_at_formatted + '</div>' +
            '</div>' +
            '<div class="timeline-label">' +
            ' <button class="btn btn-white btn-ex-com single-tiny-viewer"><i class="bi bi-arrows-angle-contract"></i></button>' +
            '<p class="mar-no pad-btm ">' + type + ' By ' +
            (value.user_id == 0 || value.user_id == null
                ? '<span class="text-bold text-primary">System</span>'
                : '<a href="' + t.config.url.user_info + '/' + value.user_id + '" target="_blank" class="btn-link text-main text-bold">' + value.updated_by + '</a>'
            ) +
            '</p>';
    }
    t.toggleSingleTinyViewer = function (e) {
        e.preventDefault();
        var el = $(this).closest('.tiny-view').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };
    t.setSingleTinyViewer = function (el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
        }
        else {
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-expand"></i>').attr('title', 'Expand');
        }
    };

    t.cardHistoryMdl.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    t.initializeDropdown = function (ticketID) {
        t.frmAssignTo.frm.assigned_to.select2({
            dropdownParent: t.mdlAssignTo,
            width: "100%",
            ajax: {
                url: t.config.url.get_users_to_assign_by_avability + "/" + ticketID,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
            },
            placeholder: "Select User",
            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                var a;
                if (s.status == true) {
                    a = "<div><span class='active-user'></span> " + s.text + "</div>";
                } else {
                    a = "<div><span class='inactive-user'></span> " + s.text + "</div>";
                }
                return $(a);
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (e.target.closest('.task-card-header')) {
            const taskCard = e.target.closest('.task-card');
            if (taskCard) {
                const wasCollapsed = taskCard.classList.contains('collapsed');
                taskCard.classList.toggle('collapsed');
                if (wasCollapsed) {
                    setTimeout(function () {
                        var $timeline = $(taskCard).find('.tab-pane.active [id^="task_timeline_"]');
                        if ($timeline.length) {
                            $('html, body').stop(true).animate({
                                scrollTop: $timeline.offset().top - 90
                            }, 300);
                        }
                    }, 100);
                }
            }
        }
    });

    $(document).on("click", "#task_manual_file_trigger", function (e) {
        e.preventDefault();
        document.getElementById('task_attachment').click();
    });
    $(document).on("click", "#task-attachment-dropper", function (e) {
        e.preventDefault();
        document.getElementById('task_attachment').click();
    });

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
        formData.append('kanbanBoard', 'true');
        formData.append('board_id', t.boardId.val());
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
                    t.loadCardList = new loadCardList(config);
                    t.loadCardList.loadBoardData(t.statusDataForAssign, t.statusGroupedForAssign);
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

    t.frmAssignToValidator = t.frmAssignTo.validate({
        onsubmit: false,
        rules: {
            assigned_to: {
                required: true,
                str_name: true
            }
        }
    });
    t.checkTechnicalAvabilityForAssignTo = function () {
        var attendarId = t.frmAssignTo.frm.assigned_to.val();
        t.frmAssignTo.find(".availabilityError, .availabilitySuccess").html("");
        if (attendarId == '') {
            return false;
        }
        $.ajax({
            url: t.config.url.getTechCurrentStatusById + '/' + attendarId,
            method: 'GET',
            success: function (result) {
                if (result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmAssignTo.find(".availabilityError").html("The technician is unavailable but still you can assign ticket. Technician will revert only once he/she will available.")
                } else {
                    t.frmAssignTo.find(".availabilitySuccess").html("The technician is available.")
                }
            }
        })
    }
    $(document).on('click', '.expand-collapse-icon', function (e) {
        e.preventDefault();
        $(this).toggleClass('collapsed');
        var target = $(this).data('target');
        var isExpanded = $(this).hasClass('collapsed') ? 'false' : 'true';
        $(this).attr('aria-expanded', isExpanded);

        if ($(this).hasClass('collapsed')) {
            $(target).stop(true, true).slideDown(300);
            $(target).siblings('hr').stop(true, true).slideDown(300);
            $(this).find('svg path').attr('d',
                'M0.704104 8.20406L8.2041 0.704065C8.30862 0.599185 8.43281 0.51597 8.56956 0.459189C8.7063 0.402408 8.85291 0.373178 9.00098 0.373178C9.14904 0.373178 9.29565 0.402408 9.4324 0.459189C9.56915 0.51597 9.69334 0.599185 9.79785 0.704066L17.2979 8.20407C17.5092 8.41541 17.6279 8.70206 17.6279 9.00094C17.6279 9.29983 17.5092 9.58647 17.2979 9.79782C17.0865 10.0092 16.7999 10.1279 16.501 10.1279C16.2021 10.1279 15.9154 10.0092 15.7041 9.79782L9.00004 3.09375L2.29598 9.79875C2.08463 10.0101 1.79799 10.1288 1.4991 10.1288C1.20022 10.1288 0.913574 10.0101 0.702229 9.79875C0.490885 9.58741 0.37215 9.30076 0.372151 9.00188C0.372151 8.70299 0.490885 8.41635 0.702229 8.205L0.704104 8.20406Z'
            );
        } else {
            $(target).stop(true, true).slideUp(300);
            $(target).siblings('hr').stop(true, true).slideUp(300);
            $(this).find('svg path').attr('d',
                'M17.296 2.79594L9.79596 10.2959C9.69144 10.4008 9.56725 10.484 9.4305 10.5408C9.29376 10.5976 9.14715 10.6268 8.99908 10.6268C8.85102 10.6268 8.70441 10.5976 8.56766 10.5408C8.43092 10.484 8.30672 10.4008 8.20221 10.2959L0.702208 2.79594C0.490864 2.58459 0.372131 2.29795 0.372131 1.99906C0.372131 1.70018 0.490864 1.41353 0.702208 1.20219C0.913552 0.990843 1.2002 0.872112 1.49908 0.872112C1.79797 0.872112 2.08461 0.990843 2.29596 1.20219L9.00002 7.90625L15.7041 1.20125C15.9154 0.989906 16.2021 0.871174 16.501 0.871174C16.7998 0.871174 17.0865 0.989906 17.2978 1.20125C17.5092 1.4126 17.6279 1.69924 17.6279 1.99813C17.6279 2.29701 17.5092 2.58366 17.2978 2.795L17.296 2.79594Z'
            );
        }
        return false;
    });
    // view 
    t.viewCard = function (e) {
        if ($(e.target).closest('a').length) return;
        if ($(e.target).closest('.task-box').length) return;
        if ($(e.target).closest('.expand-collapse-icon').length) {
            return;
        }
        e.preventDefault();
        $('#api_loader').removeClass('d-none');

        t.mdlViewCard.find('.black-slide-links li').removeClass('active');
        t.mdlViewCard.find('.black-slide-links li:first').addClass('active');
        t.mdlViewCard.find('.tab-pane').removeClass('in active');
        t.mdlViewCard.find('#info').addClass('in active');

        const taskType = $(this).data('task-type') || null;
        let set_id = $(this).data('id');
        $('.viewKanbanCardHistory').attr('data-id', set_id).attr('data-task-type', taskType);
        $.ajax({
            url: t.config.url.viewTicket,
            method: 'POST',
            data: { id: $(this).data('id'), task_type: taskType },
            success: function (result) {
                $('#api_loader').addClass('d-none');
                if (typeof result == "object" && result.status === "success") {
                    t.mdlViewCard.div.html(result.data);
                    t.mdlViewCard.find('#comment').summernote({
                        inheritPlaceholder: true,
                        placeholder: t.config.translations.comment_summer,
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
                    t.mdlViewCard.modal('show');
                } else {
                    sweetAlert('center', 'error', result);
                }
            }
        })
    }
    function truncateWithTooltip(text, maxLength) {
        if (text.length > maxLength) {
            return {
                display: text.substring(0, maxLength) + "...",
                full: text
            };
        }
        return {
            display: text,
            full: text
        };
    }

    function updateBoardTitle(boardName) {
        var truncated = truncateWithTooltip(boardName, 20);
        $(".text-2x.darkpanel-title")
            .text("My Board - " + truncated.display)
            .attr("title", "My Board - " + truncated.full);
    }

    t.boardId.select2({ width: '100%', dropdownParent: t.boardId.parent() });
    var savedBoardId = localStorage.getItem('lastBoardId');
    if (savedBoardId && $("#board_id option[value='" + savedBoardId + "']").length) {
        var savedName = $("#board_id option[value='" + savedBoardId + "']").text().trim();
        $("#board_id").val(savedBoardId).trigger("change");
        t.config.board_owner = $("#board_id option:selected").data("created_by");
        updateBoardTitle(savedName);
    } else {
        var $opt = $("#board_id option:first");
        $("#board_id").val($opt.val()).trigger("change");
        t.config.board_owner = $("#board_id option:selected").data("created_by");
        updateBoardTitle($opt.text().trim());
    }

    let el = $("#select2-board_id-container");
    let txt = el.text().trim();

    if (txt.length > 20) {
        el.text(txt.substring(0, 20) + "...");
    }

    $("#board_id").on("change", function () {
        var $opt = $(this).find("option:selected");
        t.config.board_owner = $(this).find(':selected').data('created_by');
        updateBoardTitle($opt.text().trim());
        localStorage.setItem('lastBoardId', $(this).val());
        localStorage.setItem('kanban-columns-per-row', '');
        $('.group_by #groupby-unact').removeClass('d-none');
        $('.group_by #groupby-act').addClass('d-none');
        $('.btn-getarchived #archive-unact').removeClass('d-none');
        $('.btn-getarchived #archive-act').addClass('d-none');
        t.boardType = $('#board_id option:selected').data('type');
        t.fun.reload_custom_field();
        t.fun.reload_custom_field_value();
    });

    t.filters.priorityID.select2({ width: '100%', placeholder: "Select Priority", dropdownParent:t.filters.priorityID.parent() });
    t.filters.filter_by_status.select2({ width: '100%', placeholder: "Select Status", dropdownParent: t.filters.filter_by_status.parent() });
    t.filters.department.select2({ width: '100%', placeholder: "Select Department",dropdownParent: t.filters.department.parent() });
    t.filters.filter_by_date.select2({ width: '100%', dropdownParent: t.filters.filter_by_date.parent() });
    t.filters.prob_category.select2({ width: '100%', placeholder: "Select Problem Category", dropdownParent: t.filters.prob_category.parent() });
    t.filters.sub_category.select2({ width: '100%', placeholder: "Select Sub Category",  dropdownParent: t.filters.sub_category.parent() });

    t.filters.assigned_to.select2($.extend({}, {
        dropdownParent: t.filters.assigned_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        // allowClear:true,
        // minimumInputLength: 1,
        placeholder: "Select user",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "-5px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    }));
    t.filters.creator_id.select2($.extend({}, {
        dropdownParent: t.filters.creator_id.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        // allowClear:true,
        // minimumInputLength: 1,
        placeholder: "Select Creator",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "-5px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    }));
    t.cache_filter_values = function () {
        t.config.other_filters = {};
        if (t.advFilters.name.val() != '')
            t.config.other_filters.name = t.advFilters.name.val();
        if (t.advFilters.description.val() != '')
            t.config.other_filters.description = t.advFilters.description.val();
        if (t.advFilters.hierarchyApproval.val() != '')
            t.config.other_filters.hierarchyApproval = t.advFilters.hierarchyApproval.val();
        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters,
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, '', false);
    };
    t.search = (e) => {
        t.load();
    };

    t.clearSearchText = (e) => {
        $(".plain-search").val('');
        $("#clear-search").addClass("hide");
        t.load();
    };

    $(".plain-search").on("keyup", function (e) {
        if (this.value.length >= 1) {
            $("#clear-search").removeClass("hide");
        } else {
            $("#clear-search").addClass("hide");
        }

        if (e.keyCode == 13 || this.value.length == 0) {
            var v = $.trim($(this).val());
            if (v === false) {
                alert(t.config.translations.search_data);
                return false;
            }
            t.load();
        }
    });


    // edit ticket code start from here 
    t.mdlEditTicket = t.content.find("#editTicketModal");
    t.frmEditTicket = t.mdlEditTicket.find("#edit-ticket-mdl-frm");
    t.frmEditTicket.extraData = {};
    t.frmEditTicket.el = {};
    t.mdlEditTicket.title = t.frmEditTicket.find('.modal-title');
    t.frmEditTicket.el.id = t.frmEditTicket.find("#id");
    t.frmEditTicket.el.department_id = t.frmEditTicket.find("#edit_department_id");
    t.frmEditTicket.el.problem_category_id = t.frmEditTicket.find("#edit_problem_category_id");
    t.frmEditTicket.el.sub_category_id_cvr = t.frmEditTicket.find("#sub_category_id_cvr");
    t.frmEditTicket.el.sub_category_id = t.frmEditTicket.find("#edit_sub_category_id");
    t.frmEditTicket.el.priority_id = t.frmEditTicket.find("#edit_priority_id");
    t.frmEditTicket.el.tat = t.frmEditTicket.find("#tat");
    t.frmEditTicket.el.token = t.frmEditTicket.find("#token");
    t.frmEditTicket.el.self_assign = t.frmEditTicket.find("#edit_self_assign");
    t.frmEditTicket.el.assign_to_cvr = t.frmEditTicket.find("#assigned_to_cvr");
    t.frmEditTicket.el.assign_to = t.frmEditTicket.find("#edit_assigned_to");
    t.frmEditTicket.el.device_id = t.frmEditTicket.find("#edit_device_id");
    t.frmEditTicket.el.creatorId = t.frmEditTicket.find("#edit_creator_id");
    t.frmEditTicket.el.delete_previous_tasks = t.frmEditTicket.find("#remove_tasks");
    t.frmEditTicket.el.btnSubmit = t.frmEditTicket.find('#btnSubmit');

    t.fillAssignedToForEdit = function(handlers, assign_mode, current_handler) {
        var temp;
        t.frmEditTicket.el.assign_to.empty().append(new Option(config.translations.Select_User, ""));
        if(assign_mode != 1) {
            $.each(handlers, function(i,v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmEditTicket.el.assign_to.append(temp);
            });
        }
        t.frmEditTicket.el.assign_to.trigger("change");
    };

    t.loadEditTicket = function (e, assign_mode, statusData) {
        e.preventDefault();
        t.statusDataForEdit = statusData || {};
        t.statusGroupedForEdit = statusData && statusData.groupBy ? true : false;

        if (typeof assign_mode != "undefined") {
            // t.frmEdit.el.self_assign.val(1);
        }
        t.frmEditTicket.el.self_assign.val("");
        t.frmEditTicket.el.assign_to_cvr.removeClass("hide");
        t.mdlEditTicket.title.text("Edit Ticket");
        t.frmEditTicket.el.btnSubmit.text('Update');
        var id = $(this).data('id');
        $.get(t.config.url.get_data_for_transfer + "/" + id + "?from=edit", function(result) {
            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                let editActionControls = result.data?.edit_ticket_action_controls;
                if (editActionControls?.ctrl_tat === 1) {
                    t.frmEditTicket.el.tat.prop('disabled',false);
                } else {
                    t.frmEditTicket.el.tat.prop('disabled',true);
                }
                if (editActionControls?.ctrl_priority === 1) {
                    t.frmEditTicket.el.priority_id.prop('disabled',false);
                } else {
                    t.frmEditTicket.el.priority_id.prop('disabled',true);
                }
                t.frmEditTicket.el.id.val(id);
                t.frmEditTicket.el.token.val(t.config.token);
                t.frmEditTicket.el.creatorId.val(result.data.ticket.creator_id);
                t.frmEditTicket.find("#department_id").val(result.data.opts.departments.id);
                t.frmEditTicket.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                if(result.data.opts.devices.id != null) {
                    t.frmEditTicket.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmEditTicket.el.device_id.empty().append(new Option(config.translations.select_device, ""));
                }
                /* set problem category */
                t.frmEditTicket.el.problem_category_id.empty();
                t.data.trnsfer_problem_categories = result.data.opts.problem_categories;
                t.data.transfer_sub_categories = result.data.opts.problem_categories;
                $.each(result.data.opts.problem_categories, function(i, d) {
                    if(d.id == result.data.ticket.problem_category_id){
                        var op = new Option(d.name, d.id, true, true);
                        t.config.pc = d.id;
                    }else{
                        var op = new Option(d.name,d.id);
                    }
                    var $op = $(op);
                    $op.attr('form', d.form_id);
                    t.frmEditTicket.el.problem_category_id.append(op);
                });
                t.frmEditTicket.el.problem_category_id.trigger("change");

                /* set sub category */
                t.config.s_pc = result.data.ticket.sub_category_id;
                t.refillSubCategoryForEdit(undefined, result.data.ticket.sub_category_id);

                t.frmEditTicket.el.priority_id.empty();
                $.each(t.config.priorities, function(i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmEditTicket.el.priority_id.append(op);
                });

                t.frmEditTicket.el.tat.val(result.data.ticket.tat);
                /* set handlers to assign to */
                t.fillAssignedToForEdit(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

                t.updateSubCategoryVisibilityForEdit();

                t.mdlEditTicket.modal("show");
            } else {
                var data = {
                    'msg': 'Unable to load transfer form.',
                };
                sweetAlert('center', 'error', data);
            }
        });
    };

    t.fillSlaForEdit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmEditTicket.el.problem_category_id.val();
        var sub_category_id = t.frmEditTicket.el.sub_category_id.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.transfer_sub_categories) == true && t.data.transfer_sub_categories.length) {
            $.each(t.data.transfer_sub_categories, function(i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEditTicket.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.trnsfer_problem_categories != "undefined" && t.data.trnsfer_problem_categories.length) {
            $.each(t.data.trnsfer_problem_categories, function(i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEditTicket.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.frmEditTicket.el.priority_id.val("").trigger("change");
            // t.frmTransfer.el.tat.val(0);
            t.offListen = false;
        }
        t.updateSubCategoryVisibilityForEdit();
    };

    t.refillPriorityForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEditTicket.el.priority_id.empty().append(new Option(config.translations.select_priority, ""));
        t.frmEditTicket.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmEditTicket.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmEditTicket.el.priority_id.trigger("change");
    };

    // to fill the tat by default priority service time
    t.refillTatForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.frmEditTicket.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmEditTicket.el.tat.val(val);
    }

    // refill the sub category for Ticket Transfer
    t.refillSubCategoryForEdit = function(e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.transfer_sub_categories = [];
        t.frmEditTicket.el.sub_category_id.empty().append(new Option(t.config.translations.select_sub_category, ""));
        var type_val = parseInt($.trim(t.frmEditTicket.el.problem_category_id.val()));
        if(t.config.pc != type_val){
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none');
        }else{
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none');
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.trnsfer_problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.transfer_sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
                                t.frmEditTicket.el.sub_category_id.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }

        if (typeof val != "undefined") {
            t.frmEditTicket.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibilityForEdit();
    };

    t.updateSubCategoryVisibilityForEdit = function() {
        if ( t.data.transfer_sub_categories.length > 0) {
            t.frmEditTicket.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmEditTicket.el.sub_category_id_cvr.show();
        } else {
            t.frmEditTicket.el.sub_category_id.rules("remove");
            t.frmEditTicket.el.sub_category_id_cvr.hide();
        }
    };

    t.frmEditTicket.el.sub_category_id.on('change',function(){
        let val = $(this).val();
        if((val != t.config.s_pc) && t.config.s_pc != 'undefined'){
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none');
        }else{
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none');
        }
    })

    t.frmForEditValidator = t.frmEditTicket.validate({
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
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmEditTicketSubmit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        if (t.frmForEditValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }

        if (t.frmEditTicket.el.priority_id.prop("disabled")) {
            let val =  t.frmEditTicket.el.priority_id.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmEditTicket);
        }
        if (t.frmEditTicket.el.tat.prop("disabled")) {
            let tatVal =  t.frmEditTicket.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmEditTicket);
        }
        t.frmEditTicket.el.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmEditTicket.get(0));
        var http = $.ajax({
            url: config.url.editTicket,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlEditTicket.modal("hide");
                    setTimeout(function() {
                        t.loadCardList = new loadCardList(config);
                        t.loadCardList.loadBoardData(t.statusDataForEdit, t.statusGroupedForEdit);
                    }, 2000);
                } else {
                    t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateEditTicketform();
                    }
                }
            }
        });
        http.fail(function() {
            t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    t.checkTechnicalAvabilityForTicketEdit = function() {
        var attendarId = t.frmEditTicket.el.assign_to.val();
        t.frmEditTicket.find(".availabilityError, .availabilitySuccess").html("");
        if(attendarId == '') {
            return false;
        }
        $.ajax({
            url:t.config.url.getTechCurrentStatusById+'/'+attendarId,
            method:'GET',
            success:function(result) {
                if(result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmEditTicket.find(".availabilityError").html(t.config.translations.tecnician_is_available_or_not)
                } else {
                    t.frmEditTicket.find(".availabilitySuccess").html(t.config.translations.tecnician_is_available)
                }
            }
        });
    }

    var select2Opts = { width: "100%" };

    /* related to ticket edit */
    t.frmEditTicket.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEditTicket.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: companyId,
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.enter_starting
    }));
    
    t.frmEditTicket.el.department_id.on('select2:opening', function(e) {
        e.preventDefault();
        return false;
    });

    // Add readonly styling
    t.frmEditTicket.el.department_id.next('.select2-container').find('.select2-selection')
    .css({
        'pointer-events': 'none',
        'background-color': 'rgb(203 204 205 / 28%)',
        'cursor': 'not-allowed'
    });
    
    t.frmEditTicket.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdlEditTicket,
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.frmEditTicket.el.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && config.client == "ltts") ? true : false,
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

    t.frmEditTicket.el.problem_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmEditTicket.el.problem_category_id.parent()} )).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEditTicket.el.problem_category_id.on("change", $.proxy(t.refillSubCategoryForEdit));
    t.frmEditTicket.el.problem_category_id.on('select2:select',function(){
        t.updateEditTicketform();
    });
    t.frmEditTicket.el.sub_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmEditTicket.el.sub_category_id.parent()} )).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEditTicket.el.sub_category_id.on('select2:select',function(){
        if($("#edit-ticket-mdl-frm").find("#request_submit_id").val() == 0) {
            t.updateEditTicketform();
        }
    });
    t.frmEditTicket.el.priority_id.select2($.extend({}, select2Opts, {dropdownParent: t.mdlEditTicket} )).on("change", $.proxy(t.refillTatForEdit));
    t.frmEditTicket.el.assign_to.select2($.extend({},{
        dropdownParent: t.mdlEditTicket,
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id : t.frmEditTicket.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id : t.frmEditTicket.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }
    })).on('change', t.checkTechnicalAvabilityForTicketEdit);

    t.updateEditTicketform = function() {
        var foundObject = null;
        if (t.frmEditTicket.el.problem_category_id.val() != null && t.frmEditTicket.el.problem_category_id.val() != '') {
            $.each(t.data.trnsfer_problem_categories, function(index, obj) {
                if (obj.id == t.frmEditTicket.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if (foundObject != null) {
                var found = false
                if (foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function(key, value) {
                        if (value.form_id != undefined && value.form_id != 0 && value.form_id != null) {
                            found = true;
                            return false; // exit the loop
                        } else if(value.form_id == undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') !=0) {
                            found = true;
                            return false;
                        }
                    });
                    if (found) {
                        if (t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
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
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmEditTicket.el.sub_category_id.val() != "") {
                            if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEditTicket.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': t.config.translations.unable_to_load_form
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEditTicket.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
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
                                var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                            }
                        }
                    }
                } else {
                    if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
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
                            var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                            var request_id = btoa(t.frmEditTicket.el.id.val());
                            var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                        }
                    }
                }
                if (found) {
                    t.mdlEditTicket.find("#request_submit_id").val(0);
                } else {
                    t.mdlEditTicket.find("#request_submit_id").val("");
                }
            }
        }
    }

    t.frmEditTicket.el.btnSubmit.on("click", $.proxy(t.frmEditTicketSubmit));
    t.content.on('click', '.js-act-edit-ticket', function (e) {
        const statusData = $(this).data('status');
        t.loadEditTicket.call(this, e, undefined, statusData);
    });
    // Edit ticket code end here


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
        }, 3000);
    }
    // start ticket transfer code from here
    t.mdlTransfer = t.content.find("#ticketTransferModal");
    t.frmTransfer = t.mdlTransfer.find("#ticket-transfer-mdl-frm");
    t.frmTransfer.extraData = {};
    t.frmTransfer.el = {};
    t.mdlTransfer.title = t.frmTransfer.find('#customStatusModalTitle');
    t.frmTransfer.el.id = t.frmTransfer.find("#id");
    t.frmTransfer.el.token = t.frmTransfer.find("#token");
    t.frmTransfer.el.department_id = t.frmTransfer.find("#transfer_department_id");
    t.frmTransfer.el.problem_category_id = t.frmTransfer.find("#transfer_problem_category_id");
    t.frmTransfer.el.sub_category_id_cvr = t.frmTransfer.find("#transfer_sub_category_id_cvr");
    t.frmTransfer.el.sub_category_id = t.frmTransfer.find("#transfer_sub_category_id");
    t.frmTransfer.el.priority_id = t.frmTransfer.find("#transfer_priority_id");
    t.frmTransfer.el.tat = t.frmTransfer.find("#tat");
    t.frmTransfer.el.self_assign = t.frmTransfer.find(".self_assign");
    t.frmTransfer.el.assign_to_cvr = t.frmTransfer.find("#transfer_assigned_to_cvr");
    t.frmTransfer.el.assign_to = t.frmTransfer.find("#transfer_assigned_to");
    t.frmTransfer.el.device_id = t.frmTransfer.find("#transfer_device_id");
    t.frmTransfer.el.tags = t.frmTransfer.find("#transfer_tags");
    t.frmTransfer.el.delete_previous_tasks = t.frmTransfer.find("#remove_tasks");
    t.frmTransfer.el.is_note = t.frmTransfer.find("#is_note");
    t.frmTransfer.el.creatorId = t.frmTransfer.find("#creator_id");
    t.frmTransfer.el.btnSubmit = t.frmTransfer.find('#btnSubmit');

    t.getAssignableUsers = function(department_id, assign_mode) {
        if( department_id > 0 && !isNaN(department_id) ) {
            $.get(t.config.url.get_users_to_assign_by_dep + '/' + department_id).done(function(data) {
                t.fillAssignedTo(data, assign_mode, "");
            });
        }
        else {
            t.fillAssignedTo([], assign_mode, "");
        }
    };

    // refill the problem category for Ticket Transfer
    t.refillProblemCategoryTicketTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.trnsfer_problem_categories = [];
        t.frmTransfer.el.problem_category_id.empty().append(new Option("Select category",""));
        var type_val = parseInt($.trim(t.frmTransfer.el.department_id.val()));
        if(type_val === t.config.transfer_department_id){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            if(t.config.client === "ltts") {
                var selectedDept = t.frmTransfer.el.department_id.find(':selected').text();
                departmentName = selectedDept.substr(0, selectedDept.indexOf('(') );
                if(departmentName.trim() == "Admin - India") {
                    t.frmTransfer.el.problem_category_id.empty().append(new Option("Select Location", ""));
                }
            }
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function(data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.trnsfer_problem_categories = data.data;
                    $.each(data.data, function(i, v) {
                        t.frmTransfer.el.problem_category_id.append($('<option>').val(v.id).text(v.name).attr('description', v.remarks).attr('form', v.form_id));
                    });
                }
            }).always(function() {
                t.frmTransfer.el.problem_category_id.trigger("change");
            });
        } else {
            t.frmTransfer.el.problem_category_id.trigger("change");
        }
        t.getAssignableUsers(type_val, t.frmTransfer.el.self_assign.val());
    };

    t.frmTransfer.el.problem_category_id.on('change',function() {
        t.frmTransfer.el.sub_category_id.val('').trigger('change');
    });

    // refill the sub category for Ticket Transfer
    t.refillSubCategoryTicketTransfer = function(e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.transfer_sub_categories = [];

        t.frmTransfer.el.sub_category_id.empty().append(new Option("select SubCategory", ""));
        var type_val = parseInt($.trim(t.frmTransfer.el.problem_category_id.val()));
        if(type_val != t.config.transfer_pc){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.trnsfer_problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.transfer_sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
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

        if (typeof val != "undefined") {
            t.frmTransfer.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibilityTicketTransfer();
        // t.frmTransfer.el.sub_category_id.trigger("change");
    };

    t.frmTransfer.el.sub_category_id.on('change',function(){
        let sub_category_id = $(this).val();
        if(sub_category_id != 'undefined' && (sub_category_id != t.config.transfer_s_pc)){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }
    })

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibilityTicketTransfer = function() {
        if ( t.data.transfer_sub_categories.length > 0) {
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

    t.fillSlaTicketTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmTransfer.el.problem_category_id.val();
        var sub_category_id = t.frmTransfer.el.sub_category_id.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.transfer_sub_categories) == true && t.data.transfer_sub_categories.length) {
            $.each(t.data.transfer_sub_categories, function(i, k) {
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
        } else if (typeof t.data.trnsfer_problem_categories != "undefined" && t.data.trnsfer_problem_categories.length) {
            $.each(t.data.trnsfer_problem_categories, function(i, k) {
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

    t.refillPriorityForTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmTransfer.el.priority_id.empty().append(new Option(t.config.translations.select_priority, ""));
        t.frmTransfer.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmTransfer.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmTransfer.el.priority_id.trigger("change");
    };

    t.refillTatForTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val =  t.frmTransfer.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function(i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmTransfer.el.tat.val(val);
    }

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
            },
            "tags[]":{
                clean_text_only: true,
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmTransferSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        let s = t.frmTransfer.find("#remarks").val();
        count = s.replaceAll("&nbsp;", "").trim();
        if (t.frmTransferValidator.form() == false) {
            if(count.length <= 0) {
                $('#ticketTransferModal').find('#shows_error').html('This field is required.');
                $('#ticketTransferModal').find('#shows_error').css({'color':'#c53030','font-size':'13px','margin-left': '-13px'});
            }
            return false;
        }
        if(count.length <= 0) {
            $('#ticketTransferModal').find('#shows_error').html('This field is required');
            $('#ticketTransferModal').find('#shows_error').css({'color':'#c53030','font-size':'13px','margin-left': '-13px'});
            return false;
        } else {
            $('#ticketTransferModal').find('#shows_error').html('');
        }

        if (t.httpCall != true) {
            return false;
        }
        if (t.frmTransfer.el.priority_id.prop("disabled")) {
            let val = t.frmTransfer.el.priority_id.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmTransfer);
        }
        if (t.frmTransfer.el.tat.prop("disabled")) {
            let tatVal = t.frmTransfer.el.tat.val();
            // console.log(tatVal);
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmTransfer);
        }
        t.frmTransfer.el.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmTransfer.get(0));
        formData.append('kanbanBoard', 'true');
        formData.append('board_id', t.boardId.val());
        // formData.append('remarks', t.frmTransfer.el.remarks.val() +" commented by kanban");
        var http = $.ajax({
            url: t.config.url.transfer,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlTransfer.modal('hide');
                    setTimeout(function() {
                        t.loadCardList = new loadCardList(config);
                        t.loadCardList.loadBoardData(t.statusDataForTransfer, t.statusGroupedForTransfer);
                    }, 2000);
                } else {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateTransferform();
                    }
                }
            }
        });
        http.fail(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': t.config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    t.frmTransfer.toggleListen = function(s) {
        t.frmTransfer.el.department_id.off("change", $.proxy(t.refillProblemCategoryTicketTransfer));
        t.frmTransfer.el.problem_category_id.off("change", $.proxy(t.fillSlaTicketTransfer));
        t.frmTransfer.el.sub_category_id.off("change", $.proxy(t.fillSlaTicketTransfer));
        t.frmTransfer.el.priority_id.off("change", $.proxy(t.refillTatForTransfer));

        if (typeof s != "undefined" && s == true) {
            t.frmTransfer.el.department_id.on("change", $.proxy(t.refillProblemCategoryTicketTransfer));
            // t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.fillSlaTicketTransfer));
            t.frmTransfer.el.sub_category_id.on("change", $.proxy(t.fillSlaTicketTransfer));
            t.frmTransfer.el.priority_id.on("change", $.proxy(t.refillTatForTransfer));
        }
    };

        /* set assigned to handlers on transfer ticket */
    t.fillAssignedTo = function(handlers, assign_mode, current_handler) {
        var temp;
        t.frmTransfer.el.assign_to.empty().append(new Option(t.config.translations.Select_User, ""));
        if(assign_mode != 1) {
            $.each(handlers, function(i,v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmTransfer.el.assign_to.append(temp);
            });
        }
        t.frmTransfer.el.assign_to.trigger("change");
    };

    t.loadTransfer = function (e, assign_mode, statusData) {
        e.preventDefault();
        t.statusDataForTransfer = statusData || {};
        t.statusGroupedForTransfer = statusData && statusData.groupBy ? true : false;
        var ticketID = $(this).data('id');
        t.frmTransfer.find("#remarks").summernote('code', '');
        t.frmTransfer.find("#shows_error").html(""); 
        t.frmTransfer.el.self_assign.val("");
        t.frmTransfer.el.assign_to_cvr.removeClass("hide");
        t.mdlTransfer.title.text(t.config.translations.Transfer_Ticket);
        t.frmTransfer.el.btnSubmit.text('Transfer');
        t.frmTransferValidator.resetForm();
        if (typeof assign_mode != "undefined") {
            t.frmTransfer.el.self_assign.val(1);
            t.frmTransfer.el.assign_to_cvr.addClass("hide");
            t.mdlTransfer.title.text('Ticket Self Assign');
            t.frmTransfer.el.btnSubmit.text('Assign');
        }
        $.get(t.config.url.get_data_for_transfer + "/" + ticketID, function(result) {

            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                let editActionControls = result.data?.edit_ticket_action_controls;
                if (editActionControls?.ctrl_tat === 1) {
                    t.frmTransfer.el.tat.prop('disabled',false);
                } else {
                    t.frmTransfer.el.tat.prop('disabled',true);
                }
                if (editActionControls?.ctrl_priority === 1) {
                    t.frmTransfer.el.priority_id.prop('disabled',false);
                } else {
                    t.frmTransfer.el.priority_id.prop('disabled',true);
                }
                t.frmTransfer.toggleListen();
                t.frmTransfer.el.id.val(ticketID);
                t.frmTransfer.el.token.val(t.config.token);
                t.frmTransfer.el.creatorId.val(result.data.ticket.creator_id);
                t.frmTransfer.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                if (result.data.opts.devices.id != null) {
                    t.frmTransfer.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmTransfer.el.device_id.empty().append(new Option(config.translations.Select_User, ""));
                }

                /* set problem category */
                t.frmTransfer.el.problem_category_id.empty();
                $.each(result.data.opts.problem_categories, function (i, d) {
                    if (d.id == result.data.ticket.problem_category_id) {
                        t.config.transfer_pc = d.id;
                        var op = new Option(d.name, d.id, true, true);
                    } else {
                        var op = new Option(d.name, d.id);
                    }
                    if (typeof d.form_id != 'undefined' && d.form_id != null) {
                        $(op).attr('form', d.form_id);
                    }
                    t.frmTransfer.el.problem_category_id.append(op);
                });
                t.frmTransfer.el.problem_category_id.trigger("change");
                t.data.trnsfer_problem_categories = result.data.opts.problem_categories;

                /* set sub category */
                t.config.transfer_s_pc = result.data.ticket.sub_category_id;
                t.refillSubCategoryTicketTransfer(undefined, result.data.ticket.sub_category_id);

                t.frmTransfer.el.priority_id.empty();
                $.each(t.config.priorities, function(i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmTransfer.el.priority_id.append(op);
                });

                t.frmTransfer.el.tat.val(result.data.ticket.tat);
                t.frmTransfer.el.tags.empty();
                $.each( result.data.tags.tags, function( key, value ) {
                    t.frmTransfer.el.tags.select2('trigger', 'select', {
                        data: {text: value.tags, id: value.id, selected: true}
                    });
                });

                /* set handlers to assign to */
                t.fillAssignedTo(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

                t.frmTransfer.toggleListen(true);
                t.mdlTransfer.modal("show");
            } else {
                var data = {
                    'msg': 'Unable to load transfer form.'
                };
                sweetAlert('center', 'error', data);
            }
        });
    };

    t.checkTechnicalAvabilityForTransfer = function() {
        var attendarId = t.frmTransfer.el.assign_to.val();
        t.frmTransfer.find(".availabilityError, .availabilitySuccess").html("");
        if(attendarId == '') {
            return false;
        }
        $.ajax({
            url:t.config.url.getTechCurrentStatusById+'/'+attendarId,
            method:'GET',
            success:function(result) {
                if(result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmTransfer.find(".availabilityError").html(t.config.translations.tecnician_is_available_or_not)
                } else {
                    t.frmTransfer.find(".availabilitySuccess").html(t.config.translations.tecnician_is_available)
                }
            }
        });
    }
    
    t.updateTransferform = function() {
        var foundObject = null;
        if(t.frmTransfer.el.problem_category_id.val() != null && t.frmTransfer.el.problem_category_id.val() != ''){
            $.each(t.data.trnsfer_problem_categories, function(index, obj) {
                if (obj.id == t.frmTransfer.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if(foundObject != null) {
                var found = false;
                if(foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function(key, value) {
                        if ( value.form_id != undefined && value.form_id != 0  && value.form_id != null ) {
                            found = true;
                            return false; // exit the loop
                        } else if(value.form_id == undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') !=0) {
                            found = true;
                            return false;
                        }
                    });

                    if(found) {
                        if(t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                            if(form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
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
                        }
                        if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmTransfer.el.sub_category_id.val() != "") {
                            if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
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
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                if(form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
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
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    }
                } else {
                    if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                            if(form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
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
                            var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
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

    t.frmTransfer.find("#remarks").summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.enter_your_message,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4'],
        minHeight: 120,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(`
                    Tt
                    <span style="font-size:.65rem">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.6">
                                <path d="M3 4.5L6 7.5L9 4.5"
                                    stroke="#7F7F7F"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </g>
                        </svg>
                    </span>
                `);
            }
        }
    });

    /* related to transfer ticket */
    t.frmTransfer.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmTransfer.el.department_id.parent(),
        placeholder: t.config.translations.select_department,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: companyId,
                };
            },
            processResults: function(response) {
                return {
                    results: response.results,
                    pagination: {
                        more: response.pagination.more
                    }
                };
            },
            delay: 200
        },
        allowClear:true,
    }));

    t.frmTransfer.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmTransfer.el.device_id.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.frmTransfer.el.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && t.config.client == "ltts") ? true : false,
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.select_device,
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
    t.frmTransfer.el.tags.select2({
        dropdownParent:t.frmTransfer.el.tags.parent(),
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
            delay: 200,
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
        placeholder: t.config.translations.enter_starting,
        allowClear: true,
    };
    t.frmTransfer.el.problem_category_id.select2($.extend({}, select2Opts, {placeholder: t.config.translations.select_problem_category, dropdownParent: t.frmTransfer.el.problem_category_id.parent()})).on("change", $.proxy(t.fillSlaTicketTransfer));
    t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.refillSubCategoryTicketTransfer));
    t.frmTransfer.el.problem_category_id.on('select2:select', function() {
        t.updateTransferform();
    });
    t.frmTransfer.el.sub_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmTransfer.el.sub_category_id.parent()})).on("change", $.proxy(t.fillSlaTicketTransfer));
    t.frmTransfer.el.sub_category_id.on('select2:select', function() {
        if($("#ticketTransferModal").find("#request_submit_id").val() == 0) {
            t.updateTransferform();
        }
    });
    t.frmTransfer.el.priority_id.select2($.extend({}, select2Opts, {placeholder: t.config.translations.select_priority ,dropdownParent: t.frmTransfer.el.priority_id.parent()})).on("change", $.proxy(t.refillTatForTransfer));
    t.frmTransfer.el.assign_to.select2($.extend({},{
        dropdownParent: t.frmTransfer.el.assign_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id : t.frmTransfer.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id : t.frmTransfer.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    })).on('change', t.checkTechnicalAvabilityForTransfer);

    t.content.on('click', '.js-act-transfer', function (e) {
        const statusData = $(this).data('status');
        t.loadTransfer.call(this, e, undefined, statusData);
    });    
    t.frmTransfer.el.btnSubmit.on("click", $.proxy(t.frmTransferSubmit));
    // end ticket transfer code here

    t.mdlViewCard.find('#comment').on('click', function (e) {
        e.preventDefault();
    });
    t.downloadTicketReport = function (e) {
        e.preventDefault();
        t.config.other_filters = {};
        sweetAlertConfirmation({
            message: "By default 6 month data will be exported only. If any date filter is applied then max of 6 month data is exported only.",
            onConfirm: function () {
                var input = t.content.find('#searchPlain');
                var filter_more = '';
                let search_value = encodeURIComponent(input.val());
                let url = config.url.kanbanCardExport + "?q=" + encodeURIComponent(t.config.export_filters) + "&main_filter=" + "&search=" + search_value + "&board_id=" + t.boardId.val() + "&groupBy=" + t.config.groupBy + "&archived=" + t.config.archived + "&groupby_date=" + t.config.groupby_date + "&column=" + t.config.selectedStatuses;
                window.location = url;
            }
        });
    }

    t.customAdd = function (e) {
        t.resetFrm();
        t.mdlCustomCard.radioGroup.show();
        t.createdDate = new Date();
        const $ticketSelect = $('#ticketId');
        $ticketSelect.parent().find(".no-results-msg").remove();
        const status = $(this).data('status');
        if (status && typeof status === 'object' && 'item_name' in status && 'id' in status) {
            const itemName = status.item_name;
            const itemId = status.id;
            t.custome_status_data = status;

            t.mdlCustomCard.modaltitle.text(itemName);
            $('#manualItemSelectWrapper').hide();
            $('#manualItemSelect').prop('required', false);
            t.mdlCustomCard.modal("show").data('boardItemId', itemId);
        } else {
            t.mdlCustomCard.modaltitle.text("New Item");
            const expectedDate = document.getElementById("expectedDate");
            setMinDateTime(expectedDate);

            const $dropdown = $('#manualItemSelect');
            $dropdown.empty().append(new Option('Select Item', '', true, true));

            $('#manualItemSelectWrapper').show();
            $('#manualItemSelect').prop('required', true);
            t.mdlCustomCard.modal("show").data('boardItemId', null);

            const boardId = t.boardId.val() || null;
            if (boardId) {
                $.ajax({
                    url: t.config.url.getItemsByBoard,
                    method: 'GET',
                    data: { board_id: boardId },
                    success: function (response) {
                        if (Array.isArray(response)) {
                            const items = response.map(item => new Option(item.name, item.id, false, false));
                            $dropdown.append(items);
                        } else {
                            console.warn("Unexpected response format:", response);
                        }
                    },
                    error: function (xhr) {
                        console.error("Failed to fetch items:", xhr.responseText);
                    }
                });
            } else {
                console.warn("Missing board_id.");
            }
        }

        $('#filter_counting').css('display', 'none');
        t.frmCommentTokenizeCustomCard();
        t.httpPostPath = t.config.url.storeBoardItemTicket;
        $('input[name="taskType"]').on('change', function () {
            $('#manualItemSelect-error').hide();
            $("#ticketId-error").hide();
            if (t.mdlCustomCard.ticketOption.is(':checked')) {
                t.mdlCustomCard.ticketIdGroup.show();
                t.mdlCustomCard.customTaskFields.hide();
            } else {
                t.mdlCustomCard.ticketIdGroup.hide();
                t.mdlCustomCard.customTaskFields.show();
            }
        });
        $('input[name="taskType"]:checked').trigger('change');
        t.mdlCustomCard.status.empty();
        t.mdlCustomCard.priority.empty();
        $.each(t.config.statuses, function (i, status) {
            if (![2, 6].includes(status.id)) {
                t.mdlCustomCard.status.append(new Option(status.name, status.id, false, false))
            }
        });
        t.mdlCustomCard.expected_date.val(new Date(Date.now() + 86400000).toLocaleString('sv-SE').slice(0, 16));
        $.each(t.config.priorities, function (i, k) {
            k.id == 4 ?  t.mdlCustomCard.priority.append(new Option(k.name, k.id,true,true)) :  t.mdlCustomCard.priority.append(new Option(k.name, k.id));
        });
    };

    function setMinDateTime(input, existingValue = null, createdDate = null) {
        let minDateTime;

        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;


        let errorSpan = input.parentNode.querySelector(".date-error");
        if (!errorSpan) {
            errorSpan = document.createElement("span");
            errorSpan.className = "date-error";
            errorSpan.style.color = "#c53030";
            errorSpan.style.fontSize = "13px";
            input.parentNode.appendChild(errorSpan);
        }

        errorSpan.textContent = "";

        if (existingValue) {
            const existing = new Date(existingValue.replace(" ", "T"));
            if (createdDate) {
                let created;
                if (createdDate instanceof Date) {
                    created = createdDate;
                } else {
                    created = new Date(createdDate.replace(" ", "T"));
                }

                const selected = new Date(input.value);
                // console.log(existing,created,existing < created)
                if (existing < created) {
                    errorSpan.textContent = "Expected date cannot be earlier than created date!";
                    return false;
                } else if (selected < created) {
                    errorSpan.textContent = "Expected date cannot be earlier than created date!";
                    return false;
                } else {
                    errorSpan.textContent = "";
                    input.value = existingValue.replace(" ", "T");
                    input.onchange = function () {
                        const selected = new Date(this.value);
                        if (selected < created) {
                            errorSpan.textContent = "Expected date cannot be earlier than created date!";
                            return false;
                        } else {
                            errorSpan.textContent = "";
                            return true;
                        }
                    };
                    return true;
                }
            }

            input.value = existingValue.replace(" ", "T");
        } else {
            return true;
        }

        input.min = minDateTime;
    }


    $('#manualItemSelect').select2({
        placeholder: 'Select Sprint',
        width: '100%',
        allowClear: true,
        dropdownParent: $('#manualItemSelectWrapper')
    });
    $('#manualItemSelect').on('change', function () {
        const selectedId = $(this).val();
        $.ajax({
            url: t.config.url.getBoardItemStatus + "/" + selectedId,
            method: 'GET',
            success: function (response) {
                if (response.status === "success") {
                    t.custome_status_data = response.data ? response.data : null;
                }
            },
            error: function (xhr) {
                console.error("Failed to fetch items:", xhr.responseText);
            }
        });
        t.mdlCustomCard.data('boardItemId', selectedId ? parseInt(selectedId) : null);
    });

    t.initSelect2 = function (selector) {
        selector.select2({
            dropdownParent: selector.parent(),
            width: "100%",
            placeholder: "Select Ticket",
            allowClear: true,
            ajax: {
                url: t.config.url.getTickets,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        id: t.boardId.val(),
                        filters: t.config.filters,
                        department: selector.attr('id') == 'referenceTicket' ? t.mdlCustomCard.department.val() : null,
                        problem_category: selector.attr('id') == 'referenceTicket' ? t.mdlCustomCard.problem_category.val() : null,
                        sub_category: selector.attr('id') == 'referenceTicket' ? t.mdlCustomCard.sub_category.val() : null
                    };
                },
                processResults: function (data) {
                    if ((!data.tickets || data.tickets.length === 0) && t.config.isNormalUser) {
                        let msgDiv = $("<div>").text("Member with user role can only add tickets which are created by the same member.").css({ color: "red", marginTop: "5px" });
                        selector.parent().find(".no-results-msg").remove();
                        msgDiv.addClass("no-results-msg");
                        selector.parent().append(msgDiv);
                    }
                    return {
                        results: data.tickets.map(function (ticket) {
                            return {
                                id: ticket.ticket_id,
                                text: ticket.ticket_text
                            };
                        })
                    };
                },
                cache: true
            }
        });
    };
    t.initSelect2(t.mdlCustomCard.ticketId);
    t.initSelect2(t.mdlCustomCard.referenceTicket);

    t.mdlCustomCard.description.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
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
    t.openFilter = function (e) {
        var customCardZIndex = parseInt(window.getComputedStyle(document.getElementById('mdl-customCard')).zIndex, 10) || 1050;

        t.mdlFilterModal.addClass("force-show").css({
            "z-index": customCardZIndex + 10
        });
        t.mdlFilterModal.modal("show");

        t.frmFilterModal.department.select2({
            width: "100%",
            placeholder: "Select Department",
            allowClear: true,
            dropdownParent: t.frmFilterModal.department.parent(),
            ajax: {
                url: t.config.url.getTickets,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        id: t.boardId.val(),
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.departments.map(function (department) {
                            return {
                                id: department.id,
                                text: department.name
                            };
                        })
                    };
                },
            }
        });
    };

    t.config.kanban_problem_category_ids = [];
    t.config.kanban_sub_category_ids = [];
    var problem_category_ids = [];
    t.reload_filter_problem_category = function (target, pc = null) {
        let category_data = JSON.parse(localStorage.getItem('kanban_categories')) || {};
        var department = target.department.val();
        const kanban_pcs = category_data[department] ? Object.keys(category_data[department]).map(Number) : [];
        target.problem_category.empty();
        target.sub_category.empty();
        if (department && department !== "null") {
            $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                if (data && data.data.length > 0) {
                    problem_category_ids = data.data;
                    target.problem_category.append(new Option("Select Problem Category", "", true, false));

                    $.each(data.data, function (index, category) {
                        if (Array.isArray(kanban_pcs) && kanban_pcs.length > 0 && !kanban_pcs.includes(category.id)) {
                            return;
                        }
                        if (category.id == pc) {
                            target.problem_category.append(new Option(category.name, category.id, true, true)).trigger('change');
                        } else {
                            target.problem_category.append(new Option(category.name, category.id, false, false));
                        }
                    });
                    target.problem_category.closest(".row").removeClass("d-none");
                }
            });
        } else {
            problem_category_ids = [];
            target.problem_category.trigger("change");
        }
    };

    t.reload_filter_sub_category = function (target,spc = null) {
        let category_data = JSON.parse(localStorage.getItem('kanban_categories')) || {};
        target.sub_category.empty();
        var selectedDepartmentId = parseInt($.trim(target.department.val()));
        var selectedCategoryId = parseInt($.trim(target.problem_category.val()));
        const kanban_spcs = category_data[selectedDepartmentId] && category_data[selectedDepartmentId][selectedCategoryId] ? category_data[selectedDepartmentId][selectedCategoryId] : [];
        if (isNaN(selectedCategoryId)) {
            target.sub_category.trigger("change");
            return;
        }
        let category = problem_category_ids.find(pc => pc.id === selectedCategoryId);
        if (!category) {
            target.sub_category.trigger("change");
            return;
        }
        target.sub_category.append(new Option("Select Sub Category", "", true, false));

        if (Array.isArray(category.sub) && category.sub.length > 0) {
            category.sub.forEach(sub => {
                if (Array.isArray(kanban_spcs) && kanban_spcs.length > 0 && !kanban_spcs.includes(sub.id)) {
                    return;
                }
                if (sub.id == spc) {
                    target.sub_category.append(new Option(sub.name, sub.id, true, true));
                } else {
                    target.sub_category.append(new Option(sub.name, sub.id, false, false));
                }
            });
            target.sub_category.closest(".row").removeClass("d-none");
        } else {
            target.sub_category.empty();
            target.sub_category.closest(".row").removeClass("d-none");
        }

        target.sub_category.trigger("change");
    }

    t.filter_values = function () {
        t.config.other_filters = {};
        if (t.frmFilterModal.department.val() != '' && t.frmFilterModal.department.val() != null) {
            t.config.other_filters.department = t.frmFilterModal.department.val();
        }
        if (t.frmFilterModal.problem_category.val() != '' && t.frmFilterModal.problem_category.val() != null) {
            t.config.other_filters.problem_category = t.frmFilterModal.problem_category.val();
        }
        if (t.frmFilterModal.sub_category.val() != null && t.frmFilterModal.sub_category.val() != '') {
            t.config.other_filters.sub_category = t.frmFilterModal.sub_category.val();
        }
        var jobj = {
            other_filters: t.config.other_filters,
        };
        t.config.filters = btoa(JSON.stringify(jobj));
        filterCounting(t.config.other_filters, false);
        return t.config.filters;
    };

    t.mdlCustomCard.department.select2({
        width: "100%",
        placeholder: "Select Department",
        allowClear: true,
        dropdownParent: t.mdlCustomCard.department.parent(),
        ajax: {
            url: t.config.url.getTickets,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    id: t.boardId.val(),
                };
            },
            processResults: function (data) {
                return {
                    results: data.departments.map(function (department) {
                        return {
                            id: department.id,
                            text: department.name
                        };
                    })
                };
            },
        }
    });

    t.frmFilterModal.department.on("change", function () { t.reload_filter_problem_category(t.frmFilterModal) });
    t.frmFilterModal.problem_category.select2({ width: "100%", placeholder: "Select Problem Category", allowClear: true, dropdownParent: t.frmFilterModal.problem_category.parent() });
    t.frmFilterModal.sub_category.select2({ width: "100%", placeholder: "Select Sub Category", allowClear: true, dropdownParent: t.frmFilterModal.sub_category.parent() });
    t.frmFilterModal.problem_category.on("change", function () { t.reload_filter_sub_category(t.frmFilterModal) });

    t.mdlCustomCard.department.on('change', function () { t.reload_filter_problem_category(t.mdlCustomCard, t.config.problemCategoryId) });
    t.mdlCustomCard.problem_category.select2({ width: "100%", placeholder: "Select Problem Category", allowClear: true, dropdownParent: t.mdlCustomCard.problem_category.parent() });
    t.mdlCustomCard.sub_category.select2({ width: "100%", placeholder: "Select Sub Category", allowClear: true, dropdownParent: t.mdlCustomCard.sub_category.parent() });
    t.mdlCustomCard.problem_category.on("change", function () { t.reload_filter_sub_category(t.mdlCustomCard, t.config.subCategoryId) });

    t.frmFilterModal.btnClear.on("click", function () {
        t.frmFilterModal.department.val(null).trigger("change");
        t.frmFilterModal.problem_category.val(null).trigger("change");
        t.frmFilterModal.sub_category.val(null).trigger("change");
        t.filter_values();
        t.mdlFilterModal.removeClass("force-show");
    });

    t.frmFilterModal.btnFilter.on("click", function () {
        t.filter_values();
        t.mdlFilterModal.removeClass("force-show");
    });
    t.frmFilterModal.on('click', '.close', function () {
        t.mdlFilterModal.removeClass("force-show");
    });
    function filterCounting(params, dateRange, multiple) {
        let filterCount = 0;
        $.each(params, function (i, v) {
            if (v != "null") {
                filterCount++;
            }
        });
        if (multiple) filterCount--;
        if (dateRange == "null" || dateRange > 0) filterCount--;
        if (filterCount > 0) {
            $('#filter_counting').css('display', 'flex');
            $('#filter_counting').text(filterCount);
        } else {
            $('#filter_counting').css('display', 'none');
        }
    }

    t.frmCommentTokenizeCustomCard = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.mdlCustomCard.tmp_id.val(v);
        t.frmServiceRequest.el.tmp_id.val(v);
    };
    t.mdlCustomCard.card_attachment.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attach");
      
        $.post(t.config.url.card_attachemnt_remove, { "_token": t.config.token, "id": p.attr("data-id"), "name": p.attr("name") }, function (d) { });
        p.fadeOut("slow").remove();
    };
    $('#mdl-customCard').on('hidden.bs.modal', function () {
        t.mdlCustomCard.card_attachment.empty();
    });

    t.mdlCustomCard.assign_to.select2($.extend({}, {
        width: "100%",
        dropdownParent: t.mdlCustomCard.assign_to.parent(),
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "12px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    }));

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

    t.btnSave = function (e) {
        e.preventDefault();
        const $btn = $(e.currentTarget);
        $btn.prop('disabled', true);
        let description = t.mdlCustomCard.description.summernote('code');
        t.mdlCustomCard.description.val(description);

        const isCreate = t.httpPostPath === t.config.url.storeBoardItemTicket;
        let taskType = $('input[name="taskType"]:checked').val();
        if (isCreate) {
            t.frmCustomCardValidator.settings.rules['ticketId[]'].required = true;
        } else {
            taskType = 'customTask';
            t.frmCustomCardValidator.settings.rules['ticketId[]'].required = false;
            t.frmCustomCardValidator.settings.rules['Title'].required = true;
        }
        const dateInput = document.getElementById("expectedDate");
        const isValidDate = setMinDateTime(dateInput, dateInput.value, t.createdDate);
        if (!isValidDate || t.frmCustomCardValidator.form() == false) {
            $btn.prop('disabled', false);
            return false;
        }

        let frmData = new FormData(t.frmCustomCard[0]);
        t.frmCustomCard.find('.fields').each(function () {
            let name = $(this).attr('name');
            let value = $(this).val();
            if (name) {
                frmData.append(name, value);
            }
        });
        let boardId = t.boardId.val();
        let boardItemId = t.mdlCustomCard.data('boardItemId');
        frmData.append('board_id', boardId);
        frmData.append('status_data', JSON.stringify(t.custome_status_data));
        frmData.append('board_item_id', boardItemId);
        if (taskType) {
            frmData.append('taskType', taskType);
            if (taskType === 'customTask') {
                let Title = t.mdlCustomCard.title.val();
                let description = t.mdlCustomCard.description.val();
                frmData.append('Title', Title);
                frmData.append('description', description);
            }
        }
        frmData.append('_token', t.config.token);
        $.ajax({
            url: t.httpPostPath,
            type: 'POST',
            processData: false,
            contentType: false,
            data: frmData,
            success: function (responses) {
                if (responses.status === 'success') {
                    sweetAlert('center', 'success', responses);
                    t.mdlCustomCard.modal('hide');
                    if (responses.statusData && responses.statusData.id) {
                        let targetId = responses.statusData.id;

                        $('.mainDiv').each(function () {
                            let statusData = $(this).data('statusdata');
                            if (statusData && statusData.id == targetId) {
                                $(this).removeClass('empty');
                            }
                        });
                    }
                    t.loadCardList = new loadCardList(config);
                    if (responses.grouped && responses.statusData.new_sprint_name && responses.statusData.new_sprint_name.name) {
                        let old_column_name = responses.statusData.Sprint_name;
                        let new_column_name = responses.statusData.new_sprint_name.name;
                        t.config.oldColumn = $('[data-status="' + old_column_name + '"]');
                        t.config.newColumn = $('[data-status="' + new_column_name + '"]');
                        t.loadCardList.updateBoardData(t.config.oldColumn, t.config.newColumn);
                    } else if (responses.grouped && responses.statusData.assigned_user) {
                        t.load();
                    } else {
                        t.loadCardList.loadBoardData(responses.statusData, responses.grouped);
                    }
                } else {
                    sweetAlert('center', 'error', responses);
                }
                $btn.prop('disabled', false);
            },
            error: function (xhr) {
                sweetAlert('center', 'error', { msg: 'An error occurred while saving data.' });
                $btn.prop('disabled', false);
            },
        });
    };

    t.config.problemCategoryId = null;
    t.config.subCategoryId = null;

    t.loadEditCard = function (id, status) {
        t.resetFrm();
        t.custome_status_data = status;
        t.mdlCustomCard.radioGroup.hide();
        t.mdlCustomCard.ticketIdGroup.hide();
        t.mdlCustomCard.customTaskFields.show();
        t.frmCommentTokenizeCustomCard();
        t.httpPostPath = t.config.url.updateCard + "/" + id;
        $.ajax({
            url: t.config.url.getcard + "/" + id,
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    let custom_fields_obj = response.data.custom_fields_values;
                    t.mdlCustomCard.modaltitle.text("Edit Custom Task");
                    t.mdlCustomCard.title.val(response.data.title);
                    t.mdlCustomCard.description.summernote('code', response.data.description);
                    t.mdlCustomCard.referenceTicket.empty();
                    if (response.data.expected_date && response.data.expected_date != null) {
                        setMinDateTime(t.mdlCustomCard.expected_date[0], response.data.expected_date, response.data.created_at);
                    } else {
                        setMinDateTime(t.mdlCustomCard.expected_date[0]);
                    }
                    const ticket = response.data.reference_ticket_id;
                    if (ticket) {
                        const option = new Option(ticket.ticket_text, ticket.ticket_id, true, true);
                        t.mdlCustomCard.referenceTicket.append(option);
                    };
                    t.mdlCustomCard.referenceTicket.trigger('change');
                    t.mdlCustomCard.assign_to.empty();
                    t.mdlCustomCard.status.empty();
                    t.mdlCustomCard.priority.empty();
                    t.mdlCustomCard.status.append(new Option('Select Status', '', true, true));
                    t.mdlCustomCard.priority.append(new Option('Select Priority', '', true, true));
                    const assign_to = response.data.assign_to;
                    if (assign_to && Array.isArray(assign_to)) {
                        assign_to.forEach(user => {
                            const option = new Option(user.assign_to_text, user.assign_to, true, true);
                            t.mdlCustomCard.assign_to.append(option);
                        });
                    }
                    t.mdlCustomCard.assign_to.trigger('change');
                    $.each(t.config.statuses, function (i, status) {
                        const isSelected = response.data.status === status.id;
                        if (response.data.status == 5) {
                            if (status.id == 2 || status.id == 5) {
                                t.mdlCustomCard.status.append(new Option(status.name, status.id, isSelected, isSelected));
                            }
                        }
                        else if (response.data.status == 2) {
                            if (status.id !== 6) {
                                t.mdlCustomCard.status.append(new Option(status.name, status.id, isSelected, isSelected));
                            }
                        }
                        else {
                            if (status.id !== 6 && status.id !== 2) {
                                t.mdlCustomCard.status.append(new Option(status.name, status.id, isSelected, isSelected));
                            }
                        }
                    });
                    $.each(t.config.priorities, function (i, priority) {
                        const isSelected = response.data.priority === priority.id;
                        t.mdlCustomCard.priority.append(new Option(priority.name, priority.id, isSelected, isSelected));
                    });
                    t.createdDate = response.data.created_at;
                    t.mdlCustomCard.expected_date.val(response.data.expected_date);
                    if (response.data.department) {
                        t.config.problemCategoryId = response.data.problem_category_id ?? null;
                        t.config.subCategoryId = response.data.sub_category_id ?? null;
                        t.mdlCustomCard.department.empty().append(new Option(response.data.department.name, response.data.department.id, true, true)).trigger("change");
                    }
                    if (response.data.attachment_data != "" || response.data.attachment_data != null) {
                        $.each(response.data.attachment_data, function (index, value) {
                            var attachHtml = `
                                <div id="attach${index}" class="attach pad-top" data-id="${value.id}">
                                    <div class="bord-btm clearfix">
                                        <p class="name pull-left">${decodeURIComponent(value.original_file_name)}</p>
                                        <a href="${t.config.url.card_attachment_download}/${value.id}" class="download-attach pull-right" target="_blank">Download</a>
                                        <span style="cursor:pointer;padding-right: 10px;" class="remove-attach pull-right">
                                            <i class="fa fa-remove"></i> Remove
                                        </span>
                                    </div>
                                </div>
                            `;
                            t.mdlCustomCard.card_attachment.append(attachHtml);
                        });
                    }
                    let custom_fields = t.mdlCustomCard.customTaskFields.find('[name^="fields["]');

                    custom_fields.each(function () {
                        let $field = $(this);
                        let fieldName = $field.attr("name");

                        if (fieldName) {
                            let custom_field_name = fieldName.match(/\[(.*?)\]/)[1];

                            if (custom_fields_obj.hasOwnProperty(custom_field_name)) {
                                let val = custom_fields_obj[custom_field_name].value;

                                if ($field.is("select")) {
                                    if (val) {
                                        let optionExists = $field.find(`option[value="${val}"]`).length > 0;
                                        if (!optionExists) {
                                            let newOption = new Option(val, val, true, true);
                                            $field.append(newOption);
                                        }
                                        $field.val(val).trigger("change");
                                    } else {
                                        $field.val(null).trigger("change");
                                    }
                                }

                                else if ($field.attr("type") === "radio") {
                                    if (val) {
                                        $(`input[name="${fieldName}"][value="${val}"]`, t.mdlCustomCard.customTaskFields)
                                            .prop("checked", true);
                                    } else {
                                        $(`input[name="${fieldName}"]`, t.mdlCustomCard.customTaskFields).prop("checked", false);
                                    }
                                }
                                else if ($field.attr("type") === "checkbox") {
                                    if (val) {
                                        let values = Array.isArray(val) ? val : val.split(",");
                                        values.forEach(v => {
                                            $(`input[name="${fieldName}"][value="${v.trim()}"]`, t.mdlCustomCard.customTaskFields)
                                                .prop("checked", true);
                                        });
                                    } else {
                                        $(`input[name="${fieldName}"]`, t.mdlCustomCard.customTaskFields).prop("checked", false);
                                    }
                                }
                                else if ($field.attr("type") === "datetime-local") {
                                    if (val) {
                                        let formatted = val.replace(" ", "T");
                                        $field.val(formatted);
                                    } else {
                                        $field.val("");
                                    }
                                }
                                else {
                                    $field.val(val || "");
                                }
                            } else {
                                if ($field.is("select")) {
                                    $field.val(null).trigger("change");
                                } else if ($field.is(":checkbox") || $field.is(":radio")) {
                                    $field.prop("checked", false);
                                } else {
                                    $field.val("");
                                }
                            }
                        }
                    });
                    t.mdlCustomCard.modal("show");
                } else {
                    var data_resmsg = { 'msg': response.msg };
                    sweetAlert('center', 'error', data_resmsg);
                    t.mdlCustomCard.modal('hide');
                }
            },
            error: function (xhr) {
                console.error("Server error:", xhr);
                alert('An error occurred while loading the data.');
                t.mdlCustomCard.modal('hide');
            }
        });
    }

    t.frmCustomCardValidator = t.frmCustomCard.validate({
        ignore: ":hidden",
        onsubmit: false,
        rules: {
            'ticketId[]': {
                required: function () {
                    return $('input[name="taskType"]:checked').val() === 'ticket';
                },
            },
            Title: {
                required: function () {
                    return $('input[name="taskType"]:checked').val() === 'customTask';
                },
                maxlength: 500,
                clean_text_only: true,
            }
        },
        messages: {
            Title: {
                maxlength: "Title cannot exceed 500 characters."
            }
        }, errorPlacement: function (error, element) {
            if (element.is(':radio')) {
                error.insertAfter(element.closest('.input-group').parent());
            } else if (element.attr('id') == 'Title') {
                error.insertAfter(element)
            } else if (element.hasClass("select2-hidden-accessible")) {
                if (element.attr('id') == 'ticketId') {
                    error.insertAfter(element.next('.select2'));
                } else if (element.attr('id') == 'manualItemSelect') {
                    error.appendTo(element.closest('#manualItemSelectWrapper').find('.col-md-9'));
                } else {
                    error.insertAfter(element.next('.select2').parent());
                }
            } else {
                error.insertAfter(element.closest('.input-group'));
            }
        }
    });

    t.resetFrm = function () {
        t.config.filters = {};
        t.custome_status_data = {};
        t.config.customeAddGroupby = {};
        t.frmCustomCard[0].reset();
        t.mdlCustomCard.find('#shows_error').text('');
        t.mdlCustomCard.ticketId.val(null).trigger('change');
        t.mdlCustomCard.referenceTicket.val(null).trigger('change');
        t.mdlCustomCard.assign_to.val(null).trigger('change');
        t.mdlCustomCard.status.val(null).trigger('change');
        t.mdlCustomCard.priority.val(null).trigger('change');
        t.mdlCustomCard.ticketOption.prop("checked", true);
        t.mdlCustomCard.customTaskOption.prop("checked", false);
        t.mdlCustomCard.description.val('').summernote('code', '');
        t.mdlCustomCard.card_attachment.empty();
        t.frmCustomCardValidator.resetForm();
        $('#manualItemSelectWrapper').hide();
        $('#manualItemSelect').prop('required', false);
        t.mdlCustomCard.btnSave.prop('disabled', false);
        t.mdlCustomCard.department.empty();
        t.mdlCustomCard.problem_category.empty();
        t.mdlCustomCard.sub_category.empty();
    };

    t.content.on('click', '.delete-card', function () {
        var itemId = $(this).data('id');
        const statusData = $(this).data('status');
        const grouped = statusData && statusData.groupBy ? true : false;
        sweetAlertConfirm({
            message: 'Are you sure you want to delete this task?',
            url: t.config.url.card_delete + "/" + itemId,
            type: "GET",
            data: {
                _token: t.config.token
            },
            onSuccess: function (response) {
                if (response.status === 'success') {
                    sweetAlert('center', 'success', response);
                    t.loadCardList = new loadCardList(config);
                    t.loadCardList.loadBoardData(statusData, grouped);
                } else {
                    sweetAlert('center', 'error', response);
                }
            },
            beforeSend: function () {
                t.httpCall = false;
            },
            complete: function () {
                t.httpCall = true;
            },
        });
    });

    t.visibleContent = function (e) {
        var $icons = $('.expand-collapse-icon');
        var isCollapsed = !$icons.first().hasClass('collapsed');
        var $expandSvg = $('#expandSvg');
        var $compressSvg = $('#compressSvg');
        $icons.each(function () {
            var $icon = $(this);
            var target = $icon.data('target');

            if (isCollapsed) {
                $icon.addClass('collapsed').attr('aria-expanded', 'false');
                $icon.find('svg path').attr(
                    'd',
                    'M0.704104 8.20406L8.2041 0.704065C8.30862 0.599185 8.43281 0.51597 8.56956 0.459189C8.7063 0.402408 8.85291 0.373178 9.00098 0.373178C9.14904 0.373178 9.29565 0.402408 9.4324 0.459189C9.56915 0.51597 9.69334 0.599185 9.79785 0.704066L17.2979 8.20407C17.5092 8.41541 17.6279 8.70206 17.6279 9.00094C17.6279 9.29983 17.5092 9.58647 17.2979 9.79782C17.0865 10.0092 16.7999 10.1279 16.501 10.1279C16.2021 10.1279 15.9154 10.0092 15.7041 9.79782L9.00004 3.09375L2.29598 9.79875C2.08463 10.0101 1.79799 10.1288 1.4991 10.1288C1.20022 10.1288 0.913574 10.0101 0.702229 9.79875C0.490885 9.58741 0.37215 9.30076 0.372151 9.00188C0.372151 8.70299 0.490885 8.41635 0.702229 8.205L0.704104 8.20406Z'
                );
                $(target).stop(true, true).slideDown(300);
                $(target).siblings('hr').stop(true, true).slideDown(300);
                $expandSvg.addClass('d-none');
                $compressSvg.removeClass('d-none');
            } else {
                $icon.removeClass('collapsed').attr('aria-expanded', 'true');
                $icon.find('svg path').attr(
                    'd',
                    'M17.296 2.79594L9.79596 10.2959C9.69144 10.4008 9.56725 10.484 9.4305 10.5408C9.29376 10.5976 9.14715 10.6268 8.99908 10.6268C8.85102 10.6268 8.70441 10.5976 8.56766 10.5408C8.43092 10.484 8.30672 10.4008 8.20221 10.2959L0.702208 2.79594C0.490864 2.58459 0.372131 2.29795 0.372131 1.99906C0.372131 1.70018 0.490864 1.41353 0.702208 1.20219C0.913552 0.990843 1.2002 0.872112 1.49908 0.872112C1.79797 0.872112 2.08461 0.990843 2.29596 1.20219L9.00002 7.90625L15.7041 1.20125C15.9154 0.989906 16.2021 0.871174 16.501 0.871174C16.7998 0.871174 17.0865 0.989906 17.2978 1.20125C17.5092 1.4126 17.6279 1.69924 17.6279 1.99813C17.6279 2.29701 17.5092 2.58366 17.2978 2.795L17.296 2.79594Z'
                );
                $(target).stop(true, true).slideUp(300);
                $(target).siblings('hr').stop(true, true).slideUp(300);
                $expandSvg.removeClass('d-none');
                $compressSvg.addClass('d-none');
            }
        });
    }

    t.addcard = function (e) {
        e.preventDefault();

        var $cardDiv = $(e.currentTarget);
        var $wrapper = $cardDiv.find('.add-card-wrapper');
        var statusData = $cardDiv.data('status');
        var grouped = $cardDiv.data('grouped') != undefined ? $cardDiv.data('grouped') : false;
        if ($cardDiv.find('.add-card-container').length) {
            return;
        }

        $wrapper.hide();
        $cardDiv.addClass('disable-hover');

        var inputBox = $(`
            <div class="add-card-container" style="display: flex; flex-direction: column; align-items: start; margin-top: 5px; gap: 5px; width: 100%;">
                <textarea class="form-control add-card-input" placeholder="Enter title"
                    style="border-radius: 8px; padding: 10px !important; min-width: 200px; 
                           width: 100%; max-width: 340px; word-wrap: break-word; overflow-wrap: break-word; 
                           white-space: normal; resize: none; font-size: 14px; 
                           overflow: hidden; height: 30px; min-height: 80px; max-height: 300px;"></textarea>                
                <span class="char-count" style="font-size: 12px; color: #666;">0/500</span>
                <span class="error-msg" style="color: red; font-size: 12px; display: none;">Maximum 500 characters allowed.</span>
                <span class="error-msg-invalid-text" style="color: red; font-size: 12px; display: none;">Invalid characters or HTML detected.</span>
    
                <div style="display: flex; align-items: center; gap: 10px; width: 98%; padding: 5px 10px; justify-content: space-between;">
                    <button class="btn btn-primary btn-sm save-card-btn" 
                        style="border-radius: 6px; font-size: initial; padding: 6px 6px; width: 35%;">Add Card</button>
                    <span class="close-card-input" 
                        style="cursor: pointer; color: #000000; font-size: 30px; padding: 0 10px; margin-left: auto;">&times;</span>
                </div>
            </div>
        `);
        $cardDiv.append(inputBox);
        var $inputField = inputBox.find('.add-card-input');
        var $charCount = inputBox.find('.char-count');
        var $errorMsg = inputBox.find('.error-msg');
        var $invalidTextErrorMsg = inputBox.find('.error-msg-invalid-text');
        var isSaving = false;

        $inputField.on('input', function () {
            const clearTextRegex = /^[a-zA-Z0-9\s\r\n\-/_%&{}\[\]:;",@'’.)(:?^]{0,2000}$/;
            var textLength = $(this).val().length;
            $charCount.text(`${textLength}/500`);
            $errorMsg.hide();
            $invalidTextErrorMsg.hide();

            if (textLength > 500) {
                $errorMsg.show();
                return;
            }

            if (!clearTextRegex.test($(this).val()) || /<[^>]*>/g.test($(this).val())) {
                $invalidTextErrorMsg.show();
                return;
            }
        });

        function saveCard(grouped) {
            var cardName = $inputField.val().trim();

            if (!cardName) {

                inputBox.remove();
                $wrapper.show();
                $cardDiv.removeClass('disable-hover');
                $(document).off('click.addCardEvent');
                return;
            }

            if (cardName.length > 500) {
                $errorMsg.show();
                return;
            } else {
                $errorMsg.hide();
            }

            const clearTextRegex = /^[a-zA-Z0-9\s\r\n\-/_%&{}\[\]:;",@'’.)(:?^]{0,2000}$/;
            if (!clearTextRegex.test(cardName) || /<[^>]*>/g.test(cardName)) {
                $invalidTextErrorMsg.show();
                return;
            } else {
                $invalidTextErrorMsg.hide();
            }

            if (isSaving) return;
            isSaving = true;

            $.ajax({
                url: t.config.url.card_add,
                type: 'POST',
                data: {
                    _token: t.config.token,
                    data: statusData,
                    card_name: cardName,
                    grouped: grouped
                },
                success: function (response) {
                    if (response.status === 'success') {
                        t.loadCardList = new loadCardList(config);
                        t.loadCardList.loadBoardData(statusData, grouped);
                        inputBox.remove();
                        $wrapper.show();
                        $cardDiv.removeClass('disable-hover');
                        $cardDiv.siblings('.mainDiv').removeClass('empty');
                    } else if (response.status === 'failure' || response.status === 'fail') {
                        var data_resmsg = {
                            'msg': response.msg,
                        };
                        sweetAlert('center', 'error', data_resmsg);
                    } else {
                        var data_resmsg = {
                            'msg': response.msg,
                        };
                        sweetAlert('center', 'error', data_resmsg);
                    }
                },
                error: function (xhr) {
                    console.error('Error storing card:', xhr.responseText);
                },
                complete: function () {
                    isSaving = false;
                    $(document).off('click.addCardEvent');
                }
            });
        }
        inputBox.find('.save-card-btn').on('click', function () {
            const btn = $(this);
            btn.prop('disabled', false);
            saveCard(grouped);
        });

        $(document).off('click.addCardEvent').on('click.addCardEvent', function (event) {
            if (!$(event.target).closest('.add-card-container, .add-card-wrapper').length) {
                saveCard(grouped);
            }
        });


        inputBox.on('click', function (event) {
            event.stopPropagation();
        });


        inputBox.find('.close-card-input').on('click', function () {
            inputBox.remove();
            $wrapper.show();
            $cardDiv.removeClass('disable-hover');
            $(document).off('click.addCardEvent');
        });
    };


    t.import = function (e) {
        e.preventDefault();
        var boardId = t.boardId.val();
        var importUrl = t.config.url.import + "?board_id=" + boardId;
        window.open(importUrl, '_blank');
    };

    t.content.on('click', '.card-div', $.proxy(t.addcard));
    t.content.on("click", ".btn-import", $.proxy(t.import));
    t.mdlCustomCard.priority.trigger("change");
    t.mdlCustomCard.status.trigger("change");
    t.mdlCustomCard.status.select2({ width: '100%', dropdownParent: t.mdlCustomCard.status.parent(), placeholder: 'select status' });
    t.mdlCustomCard.priority.select2({ width: '100%', dropdownParent: t.mdlCustomCard.priority.parent(), placeholder: 'select priority' });
    t.mdlCustomCard.on("click", ".remove-attach", $.proxy(t.mdlCustomCard.card_attachment.removeAttach));
    t.content.on('click', '#assignedToButton', function (e) {
        const statusData = $(this).data('status');
        t.assignedTO.call(this, e, statusData);
    });
    t.content.on("click", "#ticketHistoryButton", $.proxy(t.openMdlTktHistory));

    t.mdl.frmEl.board_items_type.on("change", $.proxy(t.itemSetting));
    t.content.on('click', '#reloadCard', $.proxy(t.load));
    t.mdlAssignTo.btnSubmit.on('click', $.proxy(t.assignTo));
    t.content.on('click', '.view-card', $.proxy(t.viewCard));
    t.content.on('click', '#ticket_card_id', $.proxy(t.viewCard));
    t.content.on('click', '#editcard', function () {
        let cardId = $(this).data('id');
        let status = $(this).data('status');
        t.loadEditCard(cardId, status);
    });
    t.content.on('change', '#board_id', function () {
        t.config.groupBy = {};
        t.config.groupby_date = {};
        t.config.archived = {};
        $('.btn-getarchived .fa-archive').css({ 'color': '' });
        t.config.selectedStatuses = [];
        $('.group-by-item .like-radio').removeClass('active_radio');
        //  $('.group_by svg').removeClass('board-icon-active');
        $('.group_by #groupby-act').addClass('d-none');
        $('.group_by #groupby-unact').removeClass('d-none');
        let selectedBoardName = $(this).find('option:selected').text();
        $('.darkpanel-title').text(`My Board - ${selectedBoardName}`);
        let el = $("#select2-board_id-container");
        let txt = el.text().trim();

        if (txt.length > 20) {
            el.text(txt.substring(0, 20) + "...");
        }
        t.load();
    });
    t.boardType = $('#board_id option:selected').data('type');
    var select2Opts = {
        width: "100%",
        placeholder: config.translations.please_select,
        allowClear: true,
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    };
    t.fun = {
        reload_filter_problem_category: function () {
            var department = t.filters.department.val();
            t.filters.prob_category.empty().append(new Option(t.config.translations.select_prob_category, "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            t.filters.prob_category.append(new Option(k.name, k.id, false, false));
                        });
                        t.filters.prob_category.trigger("change");
                    }
                });

            }
            t.filters.prob_category.trigger("change");
        },
        reload_filter_sub_category: function () {
            t.filters.sub_category.empty().append(new Option(config.translations.select_sub_category, ""));
            var type_val = t.filters.prob_category.val();
            if ( Array.isArray(type_val) && type_val.length > 0) {
                type_val = type_val.map(Number);
                try {
                    $.each(problem_categories, function (i, v) {
                        sub_categories = v.sub;
                        if (type_val.includes(v.id)) {
                            if (Array.isArray(v.sub) && v.sub.length > 0) {
                                $.each(v.sub, function (j, k) {
                                    t.filters.sub_category.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                                });
                            }
                        }
                    });
                } catch (e) {
                    console.log(e);
                }
            }
        },
        reload_custom_field: function () {
            t.filters.filter_by_custom_field.empty();
            var url = (t.boardType == 2)
                ? t.config.url.get_kanban_fields + '/' + t.boardId.val()
                : t.config.url.customFieldsForFilter;

            t.filters.filter_by_custom_field.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.filter_by_custom_field.parent(),
                ajax: {
                    url: url,
                    dataType: "json",
                    delay: 300,
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                  processResults: function (data) {
                        if (t.boardType == 2) {
                            return {
                                results: $.map(data.data || [], function (item) {
                                    return {
                                        id: item.id,
                                        text: item.label || item.name
                                    };
                                }),
                                pagination: {
                                    more: false
                                }
                            };
                        }
                        return {
                            results: data.results || [],
                            pagination: data.pagination || {
                                more: false
                            }
                        };
                    }
                },
                allowClear: true,
                placeholder: t.config.translations.custom_field_set,
            }));
            t.filters.filter_by_custom_field.trigger("change");
        },
        reload_custom_field_value: function () {
            t.filters.filter_by_custom_field_value.append(new Option('No Filter',null, false, false));
            t.filters.filter_by_custom_field_value.trigger("change");
        },
    }
    t.content.on('click', '.btn-reload-list', $.proxy(t.load));
    t.content.on('click', '.btn-searchbox', $.proxy(t.search))
    t.content.on("click", "#clear-search", $.proxy(t.clearSearchText));
    t.content.on('click', '.btn-searchbox', $.proxy(t.search))
    t.content.on('click', '#advanced_filter', $.proxy(t.load))
    t.filters.department.on("change", $.proxy(t.fun.reload_filter_problem_category));
    t.filters.prob_category.on("change", $.proxy(t.fun.reload_filter_sub_category));
    t.content.on('click', '.download-report', $.proxy(t.downloadTicketReport));
    t.content.on('click', '.expend-view', $.proxy(t.visibleContent));
    t.content.on('click', '.btn-Add', $.proxy(t.customAdd));
    t.mdlCustomCard.on('click', '.btn-Filter', $.proxy(t.openFilter));
    t.content.off('click', '#btnSave').on('click', '#btnSave', $.proxy(t.btnSave));
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });
    t.fun.reload_custom_field();
    t.fun.reload_custom_field_value();
    t.filters.filter_by_custom_field.on('change', function () {
        t.filters.filter_by_custom_field_value.empty(); 
        var url = (t.boardType == 2) ? config.url.getKanbanCustomFieldsValueForFilter : t.config.url.getCustomFieldsValueForFilter;
        t.filters.filter_by_custom_field_value.select2($.extend({}, select2Opts, {
            dropdownParent: t.filters.filter_by_custom_field_value.parent(),
            ajax: {
                url: function () {
                    var custom_field = t.filters.filter_by_custom_field.val();
                    return url + '/' + custom_field;
                },
                dataType: "json",
                delay: 300,
                data: function (p) {
                    return {
                        search: p.term || '',
                        page: p.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data.results, function (s) {
                            return { id: s.text, text: s.text };
                        }),
                        pagination: { 
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                }
            },
            placeholder: t.config.translations.custom_field_value,
            allowClear: true
        }));
    });  
    $('#columnsPerRow').select2({
        width: '100%',
        dropdownParent: $('#columnsPerRow').parent()
    });
    t.load();
}

var CcMaster = function (config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');

    t.page = $('#kanban-boards');

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
            t.pane.addClass('d-none');
        } else {
            t.pane.removeClass('d-none');
        }
    }

    t.setShow = function (set_mode, emails) {
        var setval = typeof set_mode != "undefined" ? set_mode : 'set1';
        var setemails = typeof emails != "undefined" ? emails : t.config.data.cc_emails;

        if (setval == 'set2') {
            t.set1.addClass('d-none');
            t.set2.removeClass('d-none');
            t.cc_emails.val(setemails);
        } else {
            t.set1.removeClass('d-none');
            t.set2.addClass('d-none');
            var get_cc = (setemails == "" || setemails == null) ? "-- No CC E-Mails found" : setemails;
            t.cc_master_value.text(get_cc);
        }
    };

    t.handleSubmit = function (e) {
        e.preventDefault();

        try {
            var value = $.trim(t.cc_emails.val());
            // value = value.replace(/\s+/g, '');

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
    t.ticket = config.ticket;
    t.token = $('head meta[name="csrf-token"]');
    t.original_data = config.data;

    t.page = $("#kanban-boards");
    t.content = $(".content");
    t.mdl_popup_loader = t.page.find("#mdl_popup_loader");
    t.tkt_content = t.page.find('#tkt-content');
    t.main_attachments = t.tkt_content.find('.main_attachments');

    t.toggle_tiny_view = t.page.find('#toggle_tiny_view');
    t.toggle_tiny_view_read = t.page.find('#toggle_tiny_view_read');

    t.timeline = t.page.find("#ticket_timeline");
    t.expireInfo = t.page.find("#expireInfo");
    t.workaroundInfo = t.page.find("#workaroundInfo");
    t.responseInfo = t.page.find("#responseInfo");

    t.mdlViewCard = t.content.find("#viewKanbanMdl");
    t.mdlViewCard.header_btn = t.mdlViewCard.find('#header-button');
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        let regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        let results = regex.exec(window.location.href);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    let ticket = t.config.ticket;
    let userId = t.config.user.id;
    let companyId = t.config.data && t.config.data.company_id ? t.config.data.company_id : (t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null);

    t.mdlViewCard.on('shown.bs.modal', function () {
        t.mdlViewCard.header_btn.html('');

        let headerBtnHtml = '';
        if (t.config.serviceRequest) {
            headerBtnHtml += `
                <a href="${config.base_url}/tickets/requestInfo/${t.config.serviceRequest.id}?b=${getUrlParameter('b')}" 
                   id="link_service_request" 
                   style="padding: 0px 5px;color:#a29f9f;"
                   target="_blank" 
                   data-toggle="tooltip" 
                   data-placement="bottom"
                   data-title="Service Request">
                    <i class="fa fa-link"></i>
                </a>`;
        }

        if (ticket?.form_id && (t.config.isSuperUser || ticket.creator_id == userId || ticket.assigned_to == userId || ticket.myApproval)) {
            headerBtnHtml += `
                <a href="${config.base_url}/requested_form/view/${ticket.form_id}?b=${getUrlParameter('b')}" 
                   id="link_requested_form" 
                   style="padding: 0px 5px;color:#a29f9f;"
                   target="_blank" 
                   data-toggle="tooltip" 
                   data-placement="bottom"
                   data-title="View Form">
                    <i class="fa fa-wpforms"></i>
                </a>`;
        }

        if (headerBtnHtml) {
            t.mdlViewCard.header_btn.html(headerBtnHtml);
        }
    });

    t.frmComment = t.page.find("#frm_comment");
    t.frmComment.el = {};
    t.frmComment.el.id = t.frmComment.find("#id");
    t.frmComment.el.tmp_id = t.frmComment.find("#tmp_id");
    t.frmComment.el.comment = t.frmComment.find("#comment");
    t.frmComment.el.is_note = t.frmComment.find("#is_note");
    t.frmComment.el.comment_cc = t.frmComment.find('.comment_cc');
    t.frmComment.el.follow_cc = t.frmComment.find("#follow_cc");
    t.frmComment.el.cc_emails = t.frmComment.find('#cc_emails');
    t.frmComment.el.commentbtn = t.frmComment.find('#commentbtn');
    t.attachment = t.frmComment.find("#attachments");


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

    t.taskUpdateMdl = t.page.find("#task_update_details");
    t.frmUpdateTaskStatus = t.taskUpdateMdl.find("#frm_update_task_status");
    t.frmUpdateTaskStatus.el = {};
    t.frmUpdateTaskStatus.el.task_id = t.frmUpdateTaskStatus.find("#id");
    t.frmUpdateTaskStatus.el.statusWrapper = t.frmUpdateTaskStatus.find(".status-wrapper");
    t.frmUpdateTaskStatus.el.temp_id = t.frmUpdateTaskStatus.find("#tmp_id");
    t.frmUpdateTaskStatus.el.task_status_id = t.frmUpdateTaskStatus.find('#task_status_id');
    t.frmUpdateTaskStatus.el.task_comment = t.frmUpdateTaskStatus.find("#task_comment")
    t.frmUpdateTaskStatus.el.btnSubmit = t.taskUpdateMdl.find("#btnSubmit");

    t.frmUpdateTaskStatus.el.attachment_dropper_cover = t.frmUpdateTaskStatus.find('#update-dropper-cover');
    t.frmUpdateTaskStatus.el.attachment_dropper = t.frmUpdateTaskStatus.el.attachment_dropper_cover.find('#update-dropper');
    t.frmUpdateTaskStatus.el.attachment_updates = t.frmUpdateTaskStatus.find("#task_attachment_updates");
    t.frmUpdateTaskStatus.el.commentWrapper = t.frmUpdateTaskStatus.find('.final-comment-wrapper');


    function initCcEmailsSelect2($el) {
        $el.select2({
            dropdownParent: $el.parent(),
            width: "100%",
            placeholder: "Add CC",
            tags: true,
            maximumSelectionLength: 10,
            tokenSeparators: [','],
            ajax: {
                url: '/getUserCCByQuery',
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
                        user_id: t.config.data.creator_id,
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
    }

    if (t.data && t.data.cc_emails) {
        var emails = t.data.cc_emails.split(',').map(function (e) { return e.trim(); }).filter(Boolean);
        var data = emails.map(function (email) {
            return { id: email, text: email, selected: true };
        });
        t.frmComment.el.cc_emails.empty();
        data.forEach(function (item) {
            var option = new Option(item.text, item.id, true, true);
            t.frmComment.el.cc_emails.append(option);
        });
        t.frmComment.el.cc_emails.trigger('change');
    }

    initCcEmailsSelect2(t.frmComment.el.cc_emails);

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

    t.mdlTask = t.page.find('#taskModal');
    t.frmTask = t.mdlTask.find('#task');
    t.frmTask.ticketId = t.frmTask.find("#ticket_id");
    t.frmTask.task_id = t.frmTask.find("#task_id");
    t.frmTask.name = t.frmTask.find('#name');
    t.frmTask.company = t.frmTask.find('#company');
    t.frmTask.due_date = t.frmTask.find('#due_date');
    t.frmTask.status_id = t.frmTask.find('#status_id');
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

    t.frmTask.btnSubmit = t.frmTask.find("#taskSubmit");

    var assignToName = t.config.data.assignToName;

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
                            if (!t.frmTask.problemCategoryId.find("option[value='" + t.config.parent_Category_id + "']").length) {
                                const option = new Option(t.config.parent_Category, t.config.parent_Category_id, true, true);
                                t.frmTask.problemCategoryId.append(option);
                            }
                            t.frmTask.problemCategoryId.val(t.config.parent_Category_id).trigger('change');
                        }else if(t.config.edit_pc_id && t.config.edit_pc_name && t.config.edit){
                            if (!t.frmTask.problemCategoryId.find("option[value='" + t.config.edit_pc_id + "']").length) {
                                const option = new Option(t.config.edit_pc_name, t.config.edit_pc_id, true, true);
                                t.frmTask.problemCategoryId.append(option);
                            }
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

    t.frmTask.problemCategoryId.select2({ width: "100%",dropdownParent: t.frmTask.problemCategoryId.parent(),placeholder:"Select Problem Category" }).on("change",function(e){
        t.fun.reload_sub_category();
    });

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

    t.dueDatePicker = t.frmTask.due_date.flatpickr({
        enableTime: true,
        time_24hr: true,
        dateFormat: "d/m/Y H:i",
        minuteIncrement: 15,
        allowInput: false,
    });


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

    function getSummernoteText(element) {
        var $element = $(element);
        var content = $element.val() || "";

        if ($.fn.summernote && $element.next('.note-editor').length) {
            content = $element.summernote('code') || "";
        }

        return $('<div>').html(content).text().replace(/\u00a0/g, ' ').trim();
    }

    if ($.validator) {
        if (!$.validator.methods.summernotedescription) {
            $.validator.addMethod("summernotedescription", function (value, element) {
                return this.optional(element) || getSummernoteText(element).length > 0;
            }, "This field is required.");
        }

        if (!$.validator.methods.maxSummernoteChars) {
            $.validator.addMethod("maxSummernoteChars", function (value, element, maxLength) {
                return getSummernoteText(element).length <= maxLength;
            }, $.validator.format("Please enter no more than {0} characters."));
        }
    }

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
            ],
            callbacks: {
                onChange: function (contents) {
                    var $field = $(this);
                    $field.val(contents);

                    if ($field.closest('form').data('validator')) {
                        $field.valid();
                    }
                }
            }
        });
    }

    /* ============================================================
    Task History – Infinite Scroll with AJAX Pagination
    (includes all original action cases)
    ============================================================ */

    // t.taskHistoryMdl = t.page.find('#taskHistoryModal');

    // // ---------- helpers ----------
    // t.escapeHtml = function(str) {
    //     if (!str) return '';
    //     return String(str).replace(/[&<>"]/g, function(m) {
    //         if (m === '&') return '&amp;';
    //         if (m === '<') return '&lt;';
    //         if (m === '>') return '&gt;';
    //         if (m === '"') return '&quot;';
    //         return m;
    //     });
    // };

    // t.getAvatar = function(name, avatarUrl) {
    //     name = name || 'System';
    //     if (avatarUrl) {
    //         return '<img src="' + avatarUrl + '" alt="' + t.escapeHtml(name) + '" class="avatar-fallback">';
    //     }
    //     var initials = name.split(' ').map(function(w) { return w[0]; }).join('').toUpperCase().substring(0, 2);
    //     var colors = ['#F12F35', '#0e1d4d', '#12a78d', '#d9392f', '#2f5fd9', '#e08a1c', '#8e2f9c'];
    //     var colorIndex = name.length % colors.length;
    //     return '<span class="avatar-fallback" style="background:' + colors[colorIndex] + ';">' + initials + '</span>';
    // };

    // t.taskHistoryChangeHtml = function(label, oldVal, newVal) {
    //     oldVal = oldVal ? t.escapeHtml(oldVal) : null;
    //     newVal = newVal ? t.escapeHtml(newVal) : null;
    //     if (!oldVal && !newVal) return label + ' updated';
    //     if (!oldVal) return label + ' set to <b>' + newVal + '</b>';
    //     if (!newVal) return label + ' removed from <b>' + oldVal + '</b>';
    //     return label + ' changed from <b>' + oldVal + '</b> to <b>' + newVal + '</b>';
    // };

    // if (typeof t.buildTimelineItem !== 'function') {
    //     t.buildTimelineItem = function(toneClass, ribbonText, ribbonIcon, actionHtml, avatarHtml, commenterName, timestamp) {
    //         return `
    //             <div class="t-item ${toneClass}">
    //                 <div class="t-actor">
    //                     ${avatarHtml}
    //                     <a href="javascript:void(0)">${t.escapeHtml(commenterName)}</a>
    //                 </div>
    //                 <div class="t-rail-col">
    //                     <span class="rail-cap start"></span>
    //                     <span class="rail-cap end"></span>
    //                 </div>
    //                 <div class="t-card">
    //                     <div class="t-body">
    //                         <div class="t-time"><i class="bi bi-clock"></i> ${t.escapeHtml(timestamp)}</div>
    //                         <div class="t-text">${actionHtml}</div>
    //                     </div>
    //                     <div class="t-ribbon">
    //                         <i class="${ribbonIcon}"></i>
    //                         ${ribbonText}
    //                     </div>
    //                 </div>
    //             </div>
    //         `;
    //     };
    // }

    // // ---------- YOUR ORIGINAL FULL SWITCH CASE (21 cases) ----------
    // t.getTaskActionData = function(item) {
    //     var actionId = parseInt(item.action_id, 10);
    //     var toneClass = 'tone-task';
    //     var ribbonText = 'Update';
    //     var ribbonIcon = 'bi bi-pencil-square';
    //     var actionHtml = '';

    //     switch (actionId) {
    //         case 1:
    //             toneClass = 'tone-newticket';
    //             ribbonText = 'Created';
    //             ribbonIcon = 'bi bi-plus-circle';
    //             actionHtml = 'Task Created';
    //             break;
    //         case 2:
    //             toneClass = 'tone-company';
    //             ribbonText = 'Company';
    //             ribbonIcon = 'bi bi-building';
    //             actionHtml = t.taskHistoryChangeHtml('Company', item.old_company_name, item.company_name);
    //             break;
    //         case 3:
    //             toneClass = 'tone-task';
    //             ribbonText = 'Task';
    //             ribbonIcon = 'bi bi-pencil';
    //             if (item.old_name) {
    //                 actionHtml = t.taskHistoryChangeHtml('Task name', item.old_name, item.name);
    //             } else {
    //                 actionHtml = 'Task Deleted';
    //                 toneClass = 'tone-danger';
    //                 ribbonText = 'Deleted';
    //                 ribbonIcon = 'bi bi-trash';
    //             }
    //             break;
    //         case 4:
    //             toneClass = 'tone-comment';
    //             ribbonText = 'Comment';
    //             ribbonIcon = 'bi bi-chat-left-text';
    //             if (item.remarks) {
    //                 actionHtml = (item.is_note == 1) ? 'Marked as <b>Note</b> and commented on task' : 'Commented on task';
    //                 if (item.remarks) actionHtml += '<div class="mt-1">' + t.escapeHtml(item.remarks) + '</div>';
    //             } else {
    //                 actionHtml = t.taskHistoryChangeHtml('Related to', item.oldRelatedTo, item.relatedTo);
    //             }
    //             break;
    //         case 5:
    //             toneClass = 'tone-status';
    //             ribbonText = 'Status';
    //             ribbonIcon = 'bi bi-arrow-repeat';
    //             if (item.old_status_id) {
    //                 actionHtml = t.taskHistoryChangeHtml('Status', item.oldStatusName, item.statusName);
    //             } else {
    //                 actionHtml = 'Status changed to <b>' + t.escapeHtml(item.statusName) + '</b>';
    //                 if (item.remarks) actionHtml += '<div class="mt-1">' + item.remarks + '</div>';
    //             }
    //             var status = (item.statusName || '').toLowerCase().trim();
    //             if (status === 'completed' || status === 'resolved') {
    //                 toneClass = 'tone-resolved';
    //                 ribbonText = 'Completed';
    //                 ribbonIcon = 'bi bi-check2-square';
    //             } else if (status === 'in progress') {
    //                 toneClass = 'tone-open';
    //                 ribbonText = 'In Progress';
    //                 ribbonIcon = 'bi bi-arrow-repeat';
    //             } else if (status === 'hold' || status === 'on hold') {
    //                 toneClass = 'tone-hold';
    //                 ribbonText = 'On Hold';
    //                 ribbonIcon = 'bi bi-pause-circle';
    //             }
    //             break;
    //         case 6:
    //             toneClass = 'tone-priority';
    //             ribbonText = 'Priority';
    //             ribbonIcon = 'bi bi-flag';
    //             actionHtml = t.taskHistoryChangeHtml('Priority', item.oldPriorityName, item.priorityName);
    //             break;
    //         case 7:
    //             toneClass = 'tone-date';
    //             ribbonText = 'Start Date';
    //             ribbonIcon = 'bi bi-calendar-event';
    //             actionHtml = t.taskHistoryChangeHtml('Work start date', item.old_start_date_at, item.start_date_at);
    //             break;
    //         case 8:
    //             toneClass = 'tone-date';
    //             ribbonText = 'Due Date';
    //             ribbonIcon = 'bi bi-calendar-check';
    //             actionHtml = t.taskHistoryChangeHtml('Planned completion date', item.old_due_date_at, item.due_date_at);
    //             break;
    //         case 9:
    //             toneClass = 'tone-date';
    //             ribbonText = 'Completion';
    //             ribbonIcon = 'bi bi-calendar-check';
    //             actionHtml = t.taskHistoryChangeHtml('Actual completion date', item.old_end_date_at, item.end_date_at);
    //             break;
    //         case 10:
    //             toneClass = 'tone-cost';
    //             ribbonText = 'Cost';
    //             ribbonIcon = 'bi bi-currency-dollar';
    //             actionHtml = t.taskHistoryChangeHtml('Cost', item.old_cost, item.cost);
    //             break;
    //         case 11:
    //             toneClass = 'tone-assigned';
    //             ribbonText = 'Assigned';
    //             ribbonIcon = 'bi bi-person-check-fill';
    //             actionHtml = t.taskHistoryChangeHtml('Assigned to', item.oldAssignedTo, item.assignedTo);
    //             break;
    //         case 12:
    //             toneClass = 'tone-project';
    //             ribbonText = 'Project';
    //             ribbonIcon = 'bi bi-folder2-open';
    //             actionHtml = t.taskHistoryChangeHtml('Project', item.oldProjectName, item.projectName);
    //             break;
    //         case 13:
    //             toneClass = 'tone-change';
    //             ribbonText = 'Change';
    //             ribbonIcon = 'bi bi-arrow-left-right';
    //             actionHtml = t.taskHistoryChangeHtml('Change request', item.oldChangeName, item.changeName);
    //             break;
    //         case 14:
    //             toneClass = 'tone-description';
    //             ribbonText = 'Description';
    //             ribbonIcon = 'bi bi-file-text';
    //             actionHtml = 'Description updated';
    //             break;
    //         case 15:
    //             toneClass = 'tone-ticket';
    //             ribbonText = 'Ticket';
    //             ribbonIcon = 'bi bi-ticket-perforated';
    //             actionHtml = t.taskHistoryChangeHtml('Ticket', item.oldTicketId, item.ticketId);
    //             break;
    //         case 16:
    //             toneClass = 'tone-visibility';
    //             ribbonText = 'Visibility';
    //             ribbonIcon = 'bi bi-eye';
    //             var oldVis = item.old_is_visible_user == 1 ? 'Visible' : 'Hidden';
    //             var newVis = item.is_visible_user == 1 ? 'Visible' : 'Hidden';
    //             actionHtml = t.taskHistoryChangeHtml('User visibility', oldVis, newVis);
    //             break;
    //         case 17:
    //             toneClass = 'tone-department';
    //             ribbonText = 'Department';
    //             ribbonIcon = 'bi bi-building';
    //             actionHtml = t.taskHistoryChangeHtml('Department', item.old_dept_name, item.dept_name);
    //             break;
    //         case 18:
    //             toneClass = 'tone-category';
    //             ribbonText = 'Category';
    //             ribbonIcon = 'bi bi-folder2-open';
    //             actionHtml = t.taskHistoryChangeHtml('Problem category', item.old_pc_name, item.pc_name);
    //             break;
    //         case 19:
    //             toneClass = 'tone-subcategory';
    //             ribbonText = 'Sub Category';
    //             ribbonIcon = 'bi bi-diagram-3';
    //             actionHtml = t.taskHistoryChangeHtml('Problem subcategory', item.old_sc_name, item.sc_name);
    //             break;
    //         case 20:
    //             toneClass = 'tone-comment';
    //             ribbonText = 'Comment';
    //             ribbonIcon = 'bi bi-chat-left-text';
    //             actionHtml = (item.is_note == 1) ? 'Marked as <b>Note</b> and commented on task' : 'Commented on task';
    //             if (item.remarks) actionHtml += '<div class="mt-1">' + item.remarks + '</div>';
    //             break;
    //         case 21:
    //             toneClass = 'tone-danger';
    //             ribbonText = 'Deleted';
    //             ribbonIcon = 'bi bi-trash';
    //             actionHtml = 'Task Deleted';
    //             break;
    //         default:
    //             toneClass = 'tone-task';
    //             ribbonText = 'Updated';
    //             ribbonIcon = 'bi bi-pencil-square';
    //             actionHtml = 'Task Updated';
    //     }
    //     return { toneClass: toneClass, ribbonText: ribbonText, ribbonIcon: ribbonIcon, actionHtml: actionHtml };
    // };

    // // ---------- YOUR ORIGINAL HEADER POPULATOR ----------
    // t.populateTaskHistoryInfo = function(item, taskId) {
    //     $('#task_history_details_subject').text(item.name || '');
    //     $('#task_history_details_subject').attr('data-original-title', item.name || '');
    //     $('#task_history_details_id').text('#' + taskId);

    //     if (item.dept_name) {
    //         $('#task_history_details_department').text(item.dept_name).show();
    //     } else {
    //         $('#task_history_details_department').hide();
    //     }

    //     if (item.ticket_id) {
    //         $('.task-history-related-device-row').show();
    //         $('#task_history_details_device').text('#' + item.ticket_id).show();
    //     } else {
    //         $('.task-history-related-device-row').hide();
    //         $('#task_history_details_device').hide();
    //     }

    //     if (item.pc_name) {
    //         $('.task_history_details_category-row').show();
    //         $('#task_history_details_category').text(item.pc_name).show();
    //     } else {
    //         $('.task_history_details_category-row').hide();
    //         $('#task_history_details_category').hide();
    //     }
    //     if (item.sc_name) {
    //         $('.task-history-subcategory-row').show();
    //         $('#task_history_details_subcategory').text(item.sc_name).show();
    //     } else {
    //         $('.task-history-subcategory-row').hide();
    //         $('#task_history_details_subcategory').hide();
    //     }
    // };

    // // ---------- NEW PAGINATION STATE ----------
    // t.taskHistoryContainer = null;
    // t.taskHistoryTaskId   = null;
    // t.taskHistoryPerPage = 10;
    // t.taskHistoryCurrentPage = 1;
    // t.taskHistoryTotalPages  = 1;
    // t.taskHistoryHasMore = false;
    // t.taskHistoryIsLoading   = false;
    // t.taskHistoryScrollBound = false;

    // // ---------- RENDER ITEMS (append or replace) ----------
    // t.renderTaskHistoryItems = function(items, append) {
    //     var container = t.taskHistoryContainer;
    //     if (!container) return;

    //     var html = '';
    //     $.each(items, function(index, item) {
    //         var commenterName = item.change_by_name || 'System';
    //         var avatarHtml = t.getAvatar(commenterName, null);
    //         var timestamp = item.last_updated_at || item.updated_at || '';

    //         var actionData = t.getTaskActionData(item);
    //         html += t.buildTimelineItem(
    //             actionData.toneClass,
    //             actionData.ribbonText,
    //             actionData.ribbonIcon,
    //             actionData.actionHtml,
    //             avatarHtml,
    //             commenterName,
    //             timestamp
    //         );
    //     });

    //     if (append) {
    //         container.append(html);
    //     } else {
    //         container.html(html);
    //     }
    // };

    // // ---------- LOAD A PAGE VIA AJAX ----------
    // t.loadTaskHistoryPage = function(page, append) {
    //     if (t.taskHistoryIsLoading) return;
    //     t.taskHistoryIsLoading = true;

    //     var container = t.taskHistoryContainer;
    //     var loader = $('.task-history-loader');
    //     var bottomLoader = null;

    //     if (!append) {
    //         loader.removeClass('d-none');
    //     }

    //     if (append) {
    //         bottomLoader = $('<div class="text-center py-2 task-history-bottom-loader"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading more...</div>');
    //         container.append(bottomLoader);
    //     }

    //     $.ajax({
    //         url: t.config.url.Taskhistory,
    //         type: 'POST',
    //         data: {
    //             _token: t.config.token,
    //             id: t.taskHistoryTaskId,
    //             page: page,
    //             per_page: t.taskHistoryPerPage
    //         }
    //     }).done(function(response) {
    //         var tasks = response.tasks || [];
    //         var taskDetails = response.task_details || {};
    //         var currentPage = parseInt(response.current_page || page, 10);
    //         var lastPage = parseInt(response.last_page || currentPage, 10);

    //         if (!append) {
    //             t.populateTaskHistoryInfo(taskDetails, t.taskHistoryTaskId);
    //         }

    //         t.taskHistoryCurrentPage = currentPage;
    //         t.taskHistoryTotalPages = lastPage;
    //         t.taskHistoryHasMore = typeof response.has_more !== 'undefined'
    //             ? !!response.has_more
    //             : t.taskHistoryCurrentPage < t.taskHistoryTotalPages;

    //         if (tasks.length === 0) {
    //             if (!append) {
    //                 container.html('<div class="text-center p-4"><p>No history available</p></div>');
    //             }
    //             return;
    //         }

    //         if (bottomLoader) {
    //             bottomLoader.remove();
    //             bottomLoader = null;
    //         }

    //         t.renderTaskHistoryItems(tasks, append);

    //         if (!t.taskHistoryHasMore) {
    //             t.unbindTaskHistoryScroll();
    //         }

    //         if (!append) {
    //             if (t.taskHistoryHasMore) {
    //                 t.bindTaskHistoryScroll();
    //             }
    //         }

    //     }).fail(function(xhr) {
    //         if (xhr.status === 419) {
    //             sweetAlert('center', 'error', { msg: 'Session expired. Please refresh.' });
    //         } else {
    //             sweetAlert('center', 'error', { msg: config.translations.something_went_wrong || 'Failed to load task history' });
    //         }
    //     }).always(function() {
    //         if (bottomLoader) {
    //             bottomLoader.remove();
    //         }
    //         if (!append) {
    //             loader.addClass('d-none');
    //         }
    //         t.taskHistoryIsLoading = false;
    //     });
    // };

    // // ---------- SCROLL BINDING ----------
    // t.bindTaskHistoryScroll = function() {
    //     if (t.taskHistoryScrollBound) return;
    //     var container = t.taskHistoryContainer;
    //     if (!container) return;

    //     var scrollHandler = function() {
    //         var scrollTop = container.scrollTop();
    //         var clientHeight = container.innerHeight();
    //         var scrollHeight = container[0].scrollHeight;

    //         if (scrollTop + clientHeight >= scrollHeight - 50) {
    //             if (!t.taskHistoryIsLoading && t.taskHistoryHasMore) {
    //                 var nextPage = t.taskHistoryCurrentPage + 1;
    //                 t.loadTaskHistoryPage(nextPage, true);
    //             }
    //         }
    //     };

    //     container.on('scroll.taskHistory', scrollHandler);
    //     t.taskHistoryScrollBound = true;
    //     t._taskHistoryScrollHandler = scrollHandler;
    // };

    // t.unbindTaskHistoryScroll = function() {
    //     if (t.taskHistoryContainer) {
    //         t.taskHistoryContainer.off('scroll.taskHistory');
    //     }
    //     t.taskHistoryScrollBound = false;
    //     t._taskHistoryScrollHandler = null;
    // };

    // $('#taskHistoryModal').on('hidden.bs.modal', function() {
    //     t.unbindTaskHistoryScroll();
    //     t.taskHistoryContainer = null;
    //     t.taskHistoryIsLoading = false;
    // });

    // // ---------- MAIN OPEN FUNCTION ----------
    // t.openTaskHistory = function(e) {
    //     e.preventDefault();
    //     var taskId = $(e.currentTarget).data('id');
    //     var container = $('#task_history_container');
    //     var loader = $('.task-history-loader');

    //     t.unbindTaskHistoryScroll();
    //     container.empty();
    //     loader.removeClass('d-none');
    //     container.scrollTop(0);

    //     t.taskHistoryContainer = container;
    //     t.taskHistoryTaskId = taskId;
    //     t.taskHistoryCurrentPage = 1;
    //     t.taskHistoryTotalPages = 1;
    //     t.taskHistoryHasMore = false;
    //     t.taskHistoryIsLoading = false;

    //     $('#taskHistoryModal').modal('show');
    //     t.loadTaskHistoryPage(1, false);
    // };

    /* mdl update ticket tags */
    t.mdlManageTags = t.page.find("#mdl-add-tag");
    t.mdlManageTags.assigned_tags = t.mdlManageTags.find("#assigned_tags");
    t.mdlManageTags.getTicketTags = t.page.find(".getTicketTags");
    t.mdlManageTags.saveTags = t.mdlManageTags.find("#btnSubmit");
    t.mdlManageTags.assigned_tags.select2({
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

    t.ticketTypeSelect2 = t.page.find('.ticketType');
    t.ticket_type_fields_div = t.page.find('.ticket-type-fields-div');
    t.ticket_type_fieldset_forms = t.page.find('.ticket_type_fieldset_forms');
    t.ticketTypeDetailsForm = t.page.find('.ticket_type_fieldset_forms');


    t.btn_ticket_type_save = t.page.find('.btn_ticket_type_save');
    var getFieldDropdownParent = function (element) {
        var parent = element.closest('.tkd-detail-value, .col-md-9');

        return parent.length ? parent : element.parent();
    };
    t.frmTicketTypeValidator = t.ticketTypeDetailsForm.validate({
        onsubmit: false,
        ignore: [],
        errorPlacement: function (error, element) {
            if (element.is(':checkbox, :radio')) {
                var group = element.closest('.custom-option-group');

                if (group.length) {
                    error.appendTo(group);
                    return;
                }
            }

            if (element.hasClass('select2-hidden-accessible')) {
                var select2Container = element.next('.select2');
                var valueContainer = element.closest('.tkd-detail-value, .col-md-9');

                if (valueContainer.length) {
                    error.appendTo(valueContainer);
                } else {
                    error.insertAfter(select2Container);
                }
                return;
            }

            var inputGroup = element.closest('.input-group, .ticket-input-group');

            if (inputGroup.length) {
                error.insertAfter(inputGroup);
                return;
            }

            var valueContainer = element.closest('.tkd-detail-value, .col-md-9');

            if (valueContainer.length) {
                error.appendTo(valueContainer);
                return;
            }

            error.insertAfter(element);
        },
        highlight: function (element) {
            if ($(element).is(':checkbox,:radio')) {
                $(element).closest('.custom-option-group').addClass('has-error');
                return;
            }

            $(element).addClass('error');
        },
        unhighlight: function (element) {
            if ($(element).is(':checkbox,:radio')) {
                $(element).closest('.custom-option-group').removeClass('has-error');
                return;
            }

            $(element).removeClass('error');
        },
        success: function (label, element) {
            if ($(element).is(':checkbox,:radio')) {
                $(element).closest('.custom-option-group').removeClass('has-error');
            }

            label.remove();
        }
    });
    var select2Opts = { width: "100%" };

    t.fieldsetFormUpdated = false;

    t.ticket_type_fieldset_forms.on('chaneg', function () {
        t.fieldsetFormUpdated = true;
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

    t.ticketTypeSelect2.select2({
        width: '100%',
        placeholder: 'Select',
        allowClear: true,
        dropdownParent: t.ticketTypeSelect2.parent(),
    });

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

                                    inputHtml = '<div class="custom-option-group d-flex flex-wrap gap-3">';

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

                                    inputHtml += '</div>';

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
                                    var fieldSelector = $("#field" + v.id);
                                    fieldSelector.select2({
                                            width: "100%",
                                            allowClear: true,
                                            dropdownParent: getFieldDropdownParent(fieldSelector),
                                            placeholder: "Select"
                                        });
                                    } else {
                                        if (v.preDefinedOptions == 17) {
                                            var fieldSelector = $("#field" + v.id);
                                            fieldSelector.select2({
                                                width: "100%",
                                                allowClear: true,
                                                dropdownParent: getFieldDropdownParent(fieldSelector),
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
                                                dropdownParent: getFieldDropdownParent(selector),
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
                                                        if (v.preDefinedOptions == 1) {
                                                            return {
                                                                results: $.map(data.data, function (item) {
                                                                    return {
                                                                        text: item['a'].text,
                                                                        id: item['a'].text
                                                                    }
                                                                })
                                                            };
                                                        } else if (v.preDefinedOptions == 2) {
                                                            return {
                                                                results: $.map(data.results, function (item) {
                                                                    return {
                                                                        text: item.text,
                                                                        id: item.text,
                                                                        email: item.email,
                                                                        status: item.status,
                                                                        employee_num: item.employee_num,
                                                                        img_path: item.img_path,
                                                                        profile_img: item.profile_img,
                                                                        gravatar: item.gravatar
                                                                    }
                                                                })
                                                            };
                                                        }else{
                                                            return {
                                                                results: $.map(data.data, function (item) {
                                                                    return {
                                                                        text: item['a'].text,
                                                                        id: item['a'].text
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

                                            var fieldSelector = $("#field" + v.id);
                                            fieldSelector.select2({
                                                width: '100%',
                                                dropdownParent: getFieldDropdownParent(fieldSelector)
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
        });
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
        $.ajax({
            url: config.url.getCustomField + '/' + t.config.data.id,
            type: 'GET',

            success: function (res) {

                $('.customFieldset').empty();

                $.each(res.fields, function (i, v) {

                    let value = v.value || '';
                    let html = '';

                    html += `
                        <div class="row align-items-center mb-3 custom-field-row">

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

                        html += `<div class="d-flex align-items-center">`;

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

                        html += `<div class="custom-option-group d-flex flex-wrap gap-3">`;

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

                        html += `</div>`;
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

                           var customFieldSelector = $("#customField" + v.id);
                           customFieldSelector.select2({
                                width: "100%",
                                allowClear: true,
                                dropdownParent: getFieldDropdownParent(customFieldSelector),
                                placeholder: "Select"
                            }).val(value).trigger('change');
                        } else {

                            if (v.preDefinedOptions == 17) {

                                var customFieldSelector = $("#customField" + v.id);
                                customFieldSelector.select2({
                                    width: "100%",
                                    allowClear: true,
                                    dropdownParent: getFieldDropdownParent(customFieldSelector),

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
                                    dropdownParent: getFieldDropdownParent(selector),

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
                                            if (v.preDefinedOptions == 1) {
                                                return {
                                                    results: $.map(data.data, function (item) {
                                                        return {
                                                            text: item['a'].text,
                                                            id: item['a'].text
                                                        }
                                                    })
                                                };
                                            } else if (v.preDefinedOptions == 2) {
                                                return {
                                                    results: $.map(data.results, function (item) {
                                                        return {
                                                            text: item.text,
                                                            id: item.text,
                                                            email: item.email,
                                                            status: item.status,
                                                            employee_num: item.employee_num,
                                                            img_path: item.img_path,
                                                            profile_img: item.profile_img,
                                                            gravatar: item.gravatar
                                                        }
                                                    })
                                                };
                                            }else{
                                                return {
                                                    results: $.map(data.data, function (item) {
                                                        return {
                                                            text: item['a'].text,
                                                            id: item['a'].text
                                                        };
                                                    })
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

                                var customFieldSelector = $("#customField" + v.id);
                                customFieldSelector.select2({
                                    width: '100%',
                                    dropdownParent: getFieldDropdownParent(customFieldSelector)
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
                    $('#customField' + endID).datetimepicker({ format: 'DD-MM-YYYY HH:mm' });
                }
            }
        });
    };
    setEndValue = function (id) {
        $('#customValue' + id).val($('#customField' + id).val());
    }

    t.isImageExtension = function (ext) {
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].indexOf((ext || '').toLowerCase()) !== -1;
    };

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

    t.getTicketTagDetails = function () {
        $.get(t.config.url.get_data_for_transfer + "/" + t.data.id, function (result) {
            t.mdlManageTags.assigned_tags.html("");
            $.each(result.data.tags.tags, function (key, value) {
                var newOption = new Option(value.tags, value.tags, true, true);
                t.mdlManageTags.assigned_tags.append(newOption);
            });
        })
    }

    t.saveTicketDetailTags = function (e) {
        e.preventDefault();
        var myformData = new FormData();
        myformData.append('ticketId', t.mdlManageTags.find('#id').val());
        myformData.append('tags', t.mdlManageTags.assigned_tags.val());
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
                } else {
                    sweetAlert('center', 'error', res);
                }
            }
        })
    }

    t.mdlManageTags.getTicketTags.on('click', t.getTicketTagDetails);
    t.mdlManageTags.saveTags.on('click', t.saveTicketDetailTags);

    t.httpCall = true;
    t.attachment.removeAttach = function (e) {
        e.preventDefault();
        var p = $(this).closest(".attach");
        let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalFileSizeTicketComment - fileSize >= 0) {
            totalFileSizeTicketComment -= fileSize;
        }
        let fileName = p.find('.bord-btm p').text().split(' [')[0]; // adjust if not showing size in text
        const index = uploadFileTicketComment.indexOf(fileName);
        if (index > -1) {
            uploadFileTicketComment.splice(index, 1);
        }
        $.post(t.config.url.attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") });
        p.fadeOut("slow").remove();
    };


    $.validator.addMethod(
        "multipleemailaddress",
        function (value, element) {
            if (this.optional(element))
                return true;
            var emails = value.toString().split(/[;,]+/);
            $('#frm_comment').find('#shows_error_cc').html('');
            $('#frm_comment').find('#shows_error_cc').html('');
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

    t.frmCommentValidator = t.frmComment.validate({
        onsubmit: false,
        rules: {
            comment: {
                required: true
            },
            cc_emails: {
                //accept:"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}",
                multipleemailaddress: true
            },
        },
        messages: {
            cc_emails: {
                multipleemailaddress: "Please enter a valid E-mail address"
            }
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


    t.updateCCField = function () {
        var cc_emails = typeof t.data != "undefined" && typeof t.data.cc_emails != "undefined" ? t.data.cc_emails : "";
        t.frmComment.el.cc_emails.val(cc_emails);
    };
    t.configFun = function () {
        var wai = parseInt(t.config.wai),
            status = parseInt(t.data.status_id);

        t.page.find(".js-act-self-assign").addClass("hidden");

        t.frmComment.closest('.panel').addClass("hide");
        if ((status !== 5 && status !== 6 && t.config.access_privilege == "1" && !t.isSpamTicket()) || (status !== 5 && status !== 6 && wai === 24 && !t.isSpamTicket())) {
            t.frmComment.closest('.panel').removeClass("hide");
            t.frmComment.el.follow_cc.prop('checked', false);
            t.frmComment.el.cc_emails.val(t.data.cc_emails);
        }

        if (wai === 23) {

            if (status === 6 || status == t.config.statusRejected) {
                t.frmComment.closest('.panel').addClass('hide');
            }
        } else if (wai === 24 || wai === 25) {
            if (status == t.config.statusRejected) {
                t.frmComment.closest('.panel').addClass("hide");
            }
            if (wai != 25) {
                t.expireInfo.addClass("hide");
            }
        }
        // t.setCustomFieldForm();
    };

    t.attachmentView = function (e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        if (type == 1) {
            var images = [];

            $(".tri-view").each(function () {
                images.push({
                    href: $(this).attr("data-view"),
                    title: $(this).attr("data-name")
                });
            });

            var clickedIndex = $(".tri-view").index(this);
            $.swipebox(images, {
                initialIndexOnArray: clickedIndex,
                hideCloseButtonOnMobile: false,
                removeBarsOnMobile: false
            });
        }
    };

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
        var perPage = 3;

        function loadRecords(page) {
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
                        var exi_el_state = {};
                        t.timeline.find('.timeline-entry').each(function (exi_ind, exi_el) {
                            exi_el_state['e' + exi_el.id] = $(exi_el).hasClass('tiny-view-on');
                        });

                        if (page === 1) {
                            t.timeline.empty();
                        }

                        if (data.data.length > 0) {
                            var tiny_viewer_state = t.toggle_tiny_view.hasClass('tiny-view-on');
                            $.each(data.data, function (i, v) {
                                if (v.updated_by == v.assigned_to && v.assigned_to != null) {
                                    class_name = "color-code-bar color-code-blue-text";
                                } else if (v.updated_by == v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-rose-text";
                                } else if (v.updated_by != v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-yellow-text";
                                }
                                let feedback_comments = v.action_type == 3 ? '(This is a feedback)' : '';

                                var t2 = v.is_note == 1 ? "Note Added By" : "Commented By";
                                var profileImage = '<img src="' + v.profile_img + '" alt="Profile" class="rounded-circle" width="35" height="35">';
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

                                var cc_emails = v.cc_emails != "" && v.cc_emails != null ? " | CC: " + v.cc_emails : "";
                                var form = "";
                                if (typeof v.ticket_status_form_id != "undefined" && v.ticket_status_form_id != null) {
                                    form = '<a href="' + config.url.view_status_form + '/' + v.ticket_status_form_id + '" class="btn-ex-com" target="_blank" data-toggle="tooltip" data-title="View Form" data-placement="right"><i class="fa fa-wpforms fa-lg"></i></a>';
                                }
                                var toggleIcon = '';
                                if (v.is_workaround != null && v.is_workaround != '') {
                                    toggleIcon = `<span class="badge"> Is this workaround valid${data.tkt_data.ticket_creator == t.config.user.id ? `?<i class="fa fa-times invalid_workaround" data-tfid="${v.tfid}" data-toggle="tooltip" data-title="Click 'x' if creator not satisfied with the workaround sla" data-placement="bottom"></i>` : ''}</span>`;
                                } else if (v.is_workaround == 0) {
                                    toggleIcon = `<span class="badge">Is Workaround</span>&nbsp;<span class="badge">Is Invalid</span>`;
                                }
                                var t3 = '<p class="mar-no pad-btm txt1 force-br">' + profileImage + ' ' + ' <a href="#" class="btn-link text-main text-bold ' + class_name + '" style="font-size:medium;">' + v.commenter + '</a> ' + form + feedback_comments + toggleIcon + cc_emails + '</p>'; var t5 = "";
                                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                    var at = [];
                                    var totalAttachments = v.attachments.length;
                                    $.each(v.attachments, function (i, v) {
                                        var sext = v.ext.toLowerCase();
                                        var file_icon = "";
                                        var eye_link = "";

                                        if (sext === "png" || sext === "jpeg" || sext === "jpg") {
                                            file_icon = '<i class="fa fa-file-image-o file-icon image-icon"></i>';
                                            eye_link =
                                                '<span class="tri-view" data-view_mode="1" data-view="' +
                                                t.config.url.attachment_view +
                                                "/" +
                                                v.id +
                                                '" data-name="' +
                                                decodeURIComponent(v.name) +
                                                '" data-bs-toggle="tooltip" data-title="View"><i class="bi bi-eye"></i></span>';
                                        } else if (sext === "pdf") {
                                            file_icon = '<i class="fa fa-file-pdf-o file-icon pdf-icon"></i>';
                                        } else if (sext === "xlsx" || sext === "xls") {
                                            file_icon = '<i class="fa fa-file-excel-o file-icon excel-icon"></i>';
                                        } else {
                                            file_icon = '<i class="fa fa-file-o file-icon"></i>';
                                        }

                                        var fileSize = v.size ? (v.size / (1024 * 1024)).toFixed(2) + " MB" : "Unknown size";
                                        if (v.size < 1024 * 1024) {
                                            fileSize = (v.size / 1024).toFixed(2) + " KB";
                                        }

                                        at.push(
                                            '<div class="attachment-item">' +
                                            '<div class="attachment-content">' +
                                            file_icon +
                                            '<div class="file-details">' +
                                            '<span class="file-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span>' +
                                            '<div class="file-info-actions">' +
                                            '<span class="file-size">' + fileSize + '</span>' +
                                            '<div class="attachment-actions">' +
                                            eye_link +
                                            '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '" data-toggle="tooltip" data-title="Download">' +
                                            '<i class="bi bi-download"></i></span>' +
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
                                            '<div class="attachment-header">' +
                                            '<i class="bi bi-paperclip"></i> Attachments (' + totalAttachments + ')</div>' +
                                            '<div class="attachment-container">' +
                                            at.join("") +
                                            "</div>";
                                    }
                                }
                                var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                                var signle_tiny_viewer = tiny_viewer_state == true ? 'bi-arrows-angle-expand faa-fast animated' : 'bi-arrows-angle-contract';

                                if (typeof exi_el_state['e' + v.tfid] != 'undefined') {
                                    if (exi_el_state['e' + v.tfid] == true) {
                                        tiny_view_class = 'tiny-view-on';
                                        signle_tiny_viewer = 'bi-arrows-angle-expand faa-fast animated';
                                    }
                                    else {
                                        tiny_view_class = '';
                                        signle_tiny_viewer = 'bi-arrows-angle-contract';
                                    }
                                }
                                if (v.is_service_request == 1) {
                                    var s2 = '<div class="card-body ' + s + '">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="bi ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container">' + v.remarks + '</div>' + // Container to hold multiple remarks
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +  // Footer remains in place
                                        '<span style="font-size:14px; font-weight:550">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.tfid + '" class="card timeline-entry tiny-view ' + tiny_view_class + '">' + s2 + '</div>');
                                } else {
                                    var t4 = '<div class="card-body">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="bi ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container">' + v.remarks + '</div>' +
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +
                                        '<span style="font-size:14px; font-weight:550">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.tfid + '" class="card timeline-entry tiny-view ' + tiny_view_class + " " + c + '">' + t4 + '</div>');
                                }
                            });
                            if (data.data.length < perPage) {
                                $('.load-comment').addClass('hide');
                            } else {
                                $('.load-comment').removeClass('hide');
                            }

                            t.timeline.removeClass("hide");
                            t.timeline.css({ 'overflow-y': 'auto', 'max-height': '500px' });
                        } else {
                            if (page === 1) {
                                t.timeline.addClass("hide");
                            }
                            $('.load-comment').addClass('hide');
                        }
                    } else if (data.msg != "") {
                        sweetAlert('center', 'error', data);
                    }
                }
            });
        }

        loadRecords(currentPage);
        $('.load-comment').off("click").on('click', '.load-more', function () {
            currentPage++;
            loadRecords(currentPage);
        });
    };

    t.refreshExpireInfo = function () {
        // if (t.data.status_id == 5 || t.data.status_id == 6 || t.data.tat_halt == 1 || parseInt(t.config.wai) == 24 || t.isSpamTicket()) {
        if (parseInt(t.config.wai) == 24) {
            t.expireInfo.empty().addClass("hide");
            return;
        }
        if (t.data.expire_info != "" && t.data.expire_info.toLowerCase() == "overdue") {
            t.expireInfo.removeClass("hide").removeClass("bg-warning").addClass("bg-danger").html('<div class="box-container">' +
                '<div class="box-item">' +
                '<div class="flip-box">' +
                '<div class="flip-box-front text-center">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">Tat Expire At ' + t.data.expire_info + '</h3>' +
                '</div>' +
                '</div>' +
                '<div class="flip-box-back text-center" id="back">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">' + t.data.status.name + '</h3>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.expireInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire At</span></p>' + '<h3 class="flip-box-header" data-expire="' + t.data.expire_info + '">' + t.data.expire_info + '</h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">' + t.data.status.name + '</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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
                t.expireInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire In</span></p>' + '<h3 class="flip-box-header" data-countdown=" ' + t.data.expire_info + ' "></h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">' + t.data.status.name + '</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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
        if (parseInt(t.config.wai) == 24) {
            t.workaroundInfo.empty().addClass("hide");
            return;
        }
        if (t.data.work_around_info != undefined && t.data.work_around_info.toLowerCase() == "not available") {
            return false;
        }
        if (t.data.work_around_info != undefined && t.data.work_around_info != "" && t.data.work_around_info.toLowerCase() == "overdue") {
            t.workaroundInfo.removeClass("hide").removeClass("bg-warning").addClass("bg-danger").html('<div class="box-container">' +
                '<div class="box-item">' +
                '<div class="flip-box">' +
                '<div class="flip-box-front text-center">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">Tat Expire At ' + t.data.work_around_info + '</h3>' +
                '</div>' +
                '</div>' +
                '<div class="flip-box-back text-center" id="back">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">Workaround SLA</h3>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.workaroundInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire At</span></p>' + '<h3 class="flip-box-header" data-expire="' + t.data.work_around_info + '">' + t.data.work_around_info + '</h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">Workaround SLA</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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
                t.workaroundInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire In</span></p>' + '<h3 class="flip-box-header" data-countdown=" ' + t.data.work_around_info + ' "></h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">Workaround SLA</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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
        if (parseInt(t.config.wai) == 24) {
            t.responseInfo.empty().addClass("hide");
            return;
        }
        if (t.data.response_info != undefined && t.data.response_info.toLowerCase() == "not available") {
            return false;
        }
        if (t.data.response_info != undefined && t.data.response_info != "" && t.data.response_info.toLowerCase() == "overdue") {
            t.responseInfo.removeClass("hide").removeClass("bg-warning").addClass("bg-danger").html('<div class="box-container">' +
                '<div class="box-item">' +
                '<div class="flip-box">' +
                '<div class="flip-box-front text-center">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">Tat Expire At ' + t.data.response_info + '</h3>' +
                '</div>' +
                '</div>' +
                '<div class="flip-box-back text-center" id="back">' +
                '<div class="inner color-white">' +
                '<h3 class="flip-box-header">Response SLA</h3>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>');
        } else {
            if (t.config.tkt_config.tat_by_work_hour == 1) {
                t.responseInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire At</span></p>' + '<h3 class="flip-box-header" data-expire="' + t.data.response_info + '">' + t.data.response_info + '</h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">Response SLA</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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
                t.responseInfo.removeClass("hide").addClass("bg-warning").removeClass("bg-danger").html('<div class="box-container">' +
                    '<div class="box-item">' +
                    '<div class="flip-box">' +
                    '<div class="flip-box-front text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">TAT </span> <span class="expire">Expire In</span></p>' + '<h3 class="flip-box-header" data-countdown=" ' + t.data.response_info + ' "></h3>' +
                    '</div>' +
                    '</div>' +
                    '<div class="flip-box-back text-center">' +
                    '<div class="inner color-white">' +
                    '<p class="mar-no text-2x"><span class="tat">Response Info</span></p>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');
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

    t.frmCommentTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.frmComment.el.tmp_id.val(v);
    };

    t.frmCommentSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        if (t.frmCommentValidator.form() == false) {
            return false;
        }

        let s = t.frmComment.el.comment.val();
        if (s == '') {
            $('#frm_comment').find('#shows_error').html('This field is required.');
            $('#frm_comment').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            $('#frm_comment').find('#shows_error').html('');
        }
        let cc_checkbox = $('#frm_comment').find('#follow_cc').is(':checked');
        let cc_email_check = t.frmComment.el.cc_emails.val();
        if (cc_checkbox == true && cc_email_check == '') {
            $('#frm_comment').find('#shows_error_cc').html('This field is required.');
            $('#frm_comment').find('#shows_error_cc').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            $('#frm_comment').find('#shows_error_cc').html('');
        }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;

        var formData = new FormData(t.frmComment.get(0));
        formData.append('comment', s + " commented by kanban");
        formData.append('kanbanBoard', 'true');
        formData.append('board_id', $('#board_id').val());
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
                    t.refreshTimeLine();
                    t.refreshExpireInfo();
                    t.refreshworkAroundInfo();
                    t.refreshResponseInfo();
                    t.frmCommentTokenize();
                    t.cc_master.setData(data.data);
                    t.updateCCField();
                    sweetAlert('center', 'success', data);
                    t.frmComment.el.comment.val("").summernote('code', '');
                    $('#frm_comment').find('#shows_error').html("");
                    t.frmComment.el.is_note.prop("checked", false);
                    t.frmComment.el.follow_cc.prop("checked", false);
                    t.frmComment.el.cc_emails.val("");
                    var uploader = t.frmComment.find('#attachment-dropper-cover')[0]?.AMGDragDropUploader;
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
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.addProblemMgt = function (e) {
        t.frmProblemImpactTicektValidator.resetForm();
        t.frmaddProblem.frmEl.problem_mgt.empty().trigger('change');
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
                                    $('#add-problem-mgt').prop('checked', false);
                                }, 800);
                                if ($("#problem-impacted-ticket").length) {
                                    $("#problem-impacted-ticket").fadeOut(300, function () {
                                        $(this).remove();
                                    });
                                }
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
                                    $('#add-incident').prop('checked', false);
                                }, 800);
                                // console.log($("#incident-management-ticket").length,"vcasfhcv");
                                if ($("#incident-management-ticket").length) {
                                    $("#incident-management-ticket").fadeOut(300, function () {
                                        $(this).remove();
                                    });
                                }
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
    }

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
                if (data.status == "success" && data.card) {
                    t.mdladdIncident.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdladdIncident.modal("hide");
                    setTimeout(function () {
                        $('#add-incident').prop('checked', true);
                    }, 600);
                    if ($("#incident-management-ticket").length === 0) {
                        $(".custom-body .row").append(data.card);
                    } else {
                        $("#incident-management-ticket").replaceWith(data.card);
                    }
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
                if (data.status == "success" && data.card) {
                    t.mdladdProblem.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdladdProblem.modal("hide");
                    setTimeout(function () {
                        $('#add-problem-mgt').prop('checked', true);
                    }, 600);
                    if ($("#problem-impacted-ticket").length === 0) {
                        $(".custom-body .row").append(data.card);
                    } else {
                        $("#problem-impacted-ticket").replaceWith(data.card);
                    }
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

    // check problem category for ticket status form
    t.getStatusFormIdViaTicketProblemCategory = function () {
        var ticket_id = config.data.id;
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: "GET",
                url: t.config.url.getFormIdFromTicketProblemCategory,
                data: {
                    'ticket_id': ticket_id,
                },
                success: function (response) {
                    resolve(response.form_id);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                }
            });
        });
    }

    t.setSingleTinyViewer = function (el, target_val) {
        var card = $(el).closest('.card');
        var content = card.find('.tml-content');
        if (target_val == true) {
            card.css('height', '');
            card.css('height', 'auto');
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
        } else {
            card.css('height', '');
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-expand faa-fast animated"></i>').attr('title', 'Expand');
        }
    };


    t.setSingleTinyViewerRead = function (el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="bi bi-arrow-up"></i>').attr('title', 'Compress');
        } else {
            $(el).addClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="bi bi-arrow-down"></i>').attr('title', 'Expand');
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
        if (t.toggle_tiny_view.hasClass('tiny-view-on') == true) {
            t.toggle_tiny_view.removeClass('tiny-view-on').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, true);
            });
        }
        else {
            t.toggle_tiny_view.addClass('tiny-view-on').html('<i class="bi bi-arrows-angle-expand faa-fast animated"></i>').attr('title', 'Expand');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, false);
            });
        }
    };

    t.toggleTinyViewAllRead = function (e) {
        e.preventDefault();
        if (t.toggle_tiny_view_read.hasClass('tiny-view-on-read') == true) {
            t.toggle_tiny_view_read.removeClass('tiny-view-on-read').html('<i class="bi bi-arrow-up"></i>').attr('title', 'Compress');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, true);
            });
        }
        else {
            t.toggle_tiny_view_read.addClass('tiny-view-on-read').html('<i class="bi bi-arrow-down"></i>').attr('title', 'Expand');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, false);
            });
        }
    };

    t.setMainAttachments = function () {
        var at = [];
        var totalAttachments = t.config.main_attachments.length;

        $.each(t.config.main_attachments, function (i, v) {
            var sext = v.ext.toLowerCase();
            var file_icon = "";
            var eye_link = "";

            if (sext === "png" || sext === "jpeg" || sext === "jpg") {
                file_icon = '<i class="fa fa-file-image-o file-icon image-icon"></i>';
                eye_link =
                    '<span class="tri-view" data-view_mode="1" data-view="' +
                    t.config.url.attachment_view +
                    "/" +
                    v.id +
                    '" data-name="' +
                    decodeURIComponent(v.name) +
                    '" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></span>';
            } else if (sext === "pdf") {
                file_icon = '<i class="fa fa-file-pdf-o file-icon pdf-icon"></i>';
            } else if (sext === "xlsx" || sext === "xls") {
                file_icon = '<i class="fa fa-file-excel-o file-icon excel-icon"></i>';
            } else {
                file_icon = '<i class="fa fa-file-o file-icon"></i>';
            }

            var fileSize = v.size ? (v.size / (1024 * 1024)).toFixed(2) + " MB" : "Unknown size";
            if (v.size < 1024 * 1024) {
                fileSize = (v.size / 1024).toFixed(2) + " KB";
            }

            at.push(
                '<div class="attachment-item">' +
                '<div class="attachment-content">' +
                file_icon +
                '<div class="file-details">' +
                '<span class="file-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span>' +
                '<div class="file-info-actions">' +
                '<span class="file-size">' + fileSize + '</span>' +
                '<div class="attachment-actions">' +
                eye_link +
                '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '" data-toggle="tooltip" data-title="Download">' +
                '<i class="bi bi-download"></i></span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
            );

        });

        if (at.length > 0) {
            t.main_attachments.html(
                '<div class="attachment-header">' +
                '<i class="bi bi-paperclip"></i> Attachments (' + totalAttachments + ')</div>' +
                '<div class="attachment-container">' +
                at.join("") +
                "</div>"
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

    t.refreshTimeLine();

    t.setCustomFieldForm();

    t.page.on("click", ".add-problem-mgt", $.proxy(t.addProblemMgt));
    t.page.on("click", ".add-incident", $.proxy(t.addIncident));
    t.mdladdIncident.btnSubmit.off("click").on("click", $.proxy(t.submitIncident));
    t.toggle_tiny_view.off("click").on("click", function (e) { t.toggleTinyViewAll(e) });
    t.toggle_tiny_view_read.on("click", $.proxy(t.toggleTinyViewAllRead));
    t.page.off("click", ".single-tiny-viewer").on("click", ".single-tiny-viewer", (e) => t.toggleSingleTinyViewer(e));

    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    t.main_attachments.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.main_attachments.on("click", ".tri-download", $.proxy(t.attachmentDownload));

    /* format email address */
    var tktEmail = new TktEmail();
    t.frmComment.el.cc_emails.on("blur", $.proxy(tktEmail.formatEmailsAdapter));
    t.frmCommentTokenize();

    if (window.AMGDragDrop) {
        window.AMGDragDrop.initAll(document);
    }
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

        let targetSelector = $('#aiModal').attr("data-target");
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
            let taskTitle = sanitizeHtml(titleSelector);
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


    t.frmComment.el.comment.summernote({
        inheritPlaceholder: true,
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
                    contents: '<i class="fa fa-android"></i> <span class="ai-assist-text">AI Assist</span>',
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
        minHeight: 130,
        focus: false
    });

  

    t.frmComment.on("click", ".remove-attach", $.proxy(t.attachment.removeAttach));
    t.frmComment.el.commentbtn.on("click", $.proxy(t.frmCommentSubmit));
    t.mdladdProblem.btnSubmit.off("click").on("click", $.proxy(t.addImpactTicket));
    t.setMainAttachments();

    setInterval(function () {
        if (![5, 6].includes(t.data.status_id)) {
            t.refreshTimeLine();
        }
    }, 30000);
    
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

                var taskList = response.data || response.tasks || [];
                if (taskList.length > 0) {
                    var userDepartments = t.config.user_privilege_departments;

                    let visibleTasks = taskList.filter(function (task) {
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
                                    createdByModuleHtml = `<div>Created By Module: <b>System</b></div>`;
                                } else {
                                    let moduleLabel = task.change_by_module === 3 ? 'Problem Category': task.change_by_module === 2 ? 'Ticket' : task.change_by_module === 1 ? 'Task' : '';
                                    createdByModuleHtml = `<div>Created By Module: <b>${moduleLabel}</b></div>`;
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
                                            <div class="box-container flex-grow-1 top-right right-task-box">
                                                <div style="padding:22px;">
                                                    <p class="task-label" style="margin-bottom:5px;">Priority</p>
                                                    <span class="task-priority">${task.priority || 'NA'}</span>
                                                </div>
                                                <div class="border-bottom" style="padding:9px 0px 3px 10px">
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

    t.relatedTask();
    t.triggerFirstTab = function (taskCard) {
        var scope = taskCard && taskCard.length ? taskCard : t.page;
        const $firstTab = scope.find('.task-tab-link').first();
        if ($firstTab.length) {
            t.activateTaskTab($firstTab);
        }
    }

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

        let taskbutton = `<div class="task-actions mt-2 d-flex flex-row-reverse">`;

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
            taskbutton += `<button class="btn dtActbtn history-task" data-id="${t.taskNo}" data-bs-toggle="tooltip" title="Task History"><i class="bi bi-clock-history"></i></button>`;
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

        taskbutton += `<a href="${t.config.url.task_info}/${t.encrypt_task_id}" target="_blank" class="btn dtActbtn info-task" data-bs-toggle="tooltip" title="Task Info"><i class="bi bi-info-circle"></i></a>`;

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
                focus : false,
                callbacks: {
                    onChange: function (contents) {
                        var $comment = $(this);
                        $comment.val(contents);
                        if (!$comment.summernote('isEmpty')) {
                            $comment.closest('form').find('#shows_error').html('');
                        }
                    }
                }
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
            var $form = typeof e !== 'undefined' ? $(e.currentTarget).closest('#frm_task_comment') : t.frmTaskComment;
            var $comment = $form.find('#task_comment');
            let s = $comment.summernote('code');
            $comment.val(s);

            if ($comment.summernote('isEmpty')) {
                $form.find('#shows_error')
                    .html('This field is required.')
                    .css({ color: '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                return false;
            } else {
                $form.find('#shows_error').html('');
            }
            if (t.httpCall != true) { return false; }
            t.httpCall = false;
            var formData = new FormData($form.get(0));
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
                        $comment.val('').summernote('code', '');
                        $form.find('#shows_error').html('');
                        $form.find('#task_internal_note').prop('checked', false);
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

        t.taskAttachmentView = function (e) {
            e.preventDefault();
            const $clicked = $(e.currentTarget);
            if ($clicked.attr('data-view_mode') == '1') {
                const $items = $clicked.closest('.task-comment-list').find('.tri-view[data-view_mode="1"]');
                const images = [];
                $items.each(function () {
                    images.push({ href: $(this).attr('data-view'), title: $(this).attr('data-name') });
                });
                $.swipebox(images, {
                    initialIndexOnArray    : $items.index($clicked),
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
        t.taskTimelinePageSize = 5;

        t.taskCommentText = function (html) {
            return $('<div>').html(html || '').text().replace(/\u00a0/g, ' ').trim();
        };

        t.taskCommentPreview = function (html) {
            var text = t.taskCommentText(html);
            if (!text) { return '<span class="text-muted">Attachment added</span>'; }
            if (text.length > 160) { text = text.substring(0, 160) + '...'; }
            return $('<div>').text(text).html();
        };

        t.taskCommentAttachmentHtml = function (attachments) {
            if (typeof attachments === 'undefined' || !attachments || attachments.length === 0) {
                return '';
            }

            var at = [];
            $.each(attachments, function (ai, v2) {
                var ext = (v2.ext || '').toLowerCase();
                var fileName = decodeURIComponentSafe(unescape(v2.name || 'Attachment'));
                var viewUrl = v2.attach_view || (t.config.url.view_task_attachment + '/' + v2.id);
                var downloadUrl = v2.attach_file_path || (t.config.url.download_task_attachment + '/' + v2.id);
                var eyeLink = '';

                if (t.isImageExtension(ext)) {
                    eyeLink = `<span class="tri-view"
                        data-bs-toggle="tooltip"
                        data-bs-original-title="View"
                        data-view_mode="1"
                        data-view="${viewUrl}"
                        data-name="${fileName}">
                        <i class="bi bi-eye"></i></span>`;
                }

                var aiBg = v2.thumb == 1 ? 'ai-bg' : '';
                var bg = v2.thumb == 1 ? `background-image:url(${viewUrl}/1)` : '';

                at.push(`
                    <div class="attach-item ${aiBg}" style="${bg}">
                        <div class="attach-item-cntnt nobg">
                            ${t.getAttachmentIconHtml(ext)}
                            <span class="attach-name" style="color:#001f5b; background-color:transparent;">${fileName}</span>
                            <div class="icons">
                                <span class="attachment-header">${eyeLink}</span>
                                <span class="tri-download attachment-header"
                                    data-bs-toggle="tooltip"
                                    data-bs-original-title="Download"
                                    data-url="${downloadUrl}">
                                    <i class="bi bi-download"></i>
                                </span>
                            </div>
                        </div>
                    </div>`);
            });

            return `<div class="attachments comment-attachments">
                <div class="text-bold attachment-header" style="margin-bottom:.7rem;">
                   Attachments :
                </div>
                ${at.join('')}
            </div>`;
        };

        t.taskCommentHtml = function (v) {
            var className = '';
            if      (v.updated_by == v.assigned_to && v.assigned_to != null) { className = 'color-code-bar color-code-blue-text'; }
            else if (v.updated_by == v.creator_id  && v.creator_id  != null) { className = 'color-code-bar color-code-rose-text'; }
            else if (v.updated_by != v.creator_id  && v.creator_id  != null) { className = 'color-code-bar color-code-yellow-text'; }

            var commentHtml = v.comment || v.remarks || '';
            var previewHtml = t.taskCommentPreview(commentHtml);
            var attachmentsHtml = t.taskCommentAttachmentHtml(v.attachments);
            var internalClass = v.is_note != null && v.is_note == 1 ? 'internal' : '';
            var commentType = v.is_note != null && v.is_note == 1 ? 'Note Added By' : 'Commented By';
            var profileImg = v.profile_img || ((t.config.base_url || '') + '/imgs/profile-75.jpg');
            var commenter = v.commenter || 'Anonymous';
            var updatedAt = v.updated_at_format || '';

            return `
                <div id="${v.tfid}" class="card timeline-entry task-comment-entry comment-item ${internalClass}">
                    <div class="comment-avatar d-flex gap-1">
                        <img src="${profileImg}" alt="User Icon">
                        <div class="comment-meta">
                            ${commentType}
                            <strong class="${className}">${commenter}</strong>
                            <span class="text-muted"> | ${updatedAt}</span>
                        </div>
                    </div>
                    <div class="comment-text">
                        <div class="task-comment-preview">${previewHtml}</div>
                        <div class="task-comment-full">${commentHtml}</div>
                    </div>
                    <div class="comment-body">
                        <div class="task-comment-attachments">${attachmentsHtml}</div>
                    </div>
                </div>`;
        };

        t.scrollToTaskCommentSection = function () {
            if (!t.taskTimeline || !t.taskTimeline.length) { return; }
            if (t.taskTimeline.closest('.task-card').hasClass('collapsed')) { return; }

            $('html, body').stop(true).animate({
                scrollTop: t.taskTimeline.offset().top - 90
            }, 300);
        };

        t.checkTaskTimelineScroll = function (wrapper) {
            var $wrapper = wrapper && wrapper.length ? wrapper : t.taskTimeline.find('.task-comment-wrapper');
            if (!$wrapper.length || t.timelineDisplayIndex >= t.timelineData.length) { return; }

            while ($wrapper[0].scrollHeight <= $wrapper.innerHeight() + 5 && t.timelineDisplayIndex < t.timelineData.length) {
                var currentIndex = t.timelineDisplayIndex;
                t.loadMoreTimelineEntries(t.taskTimelinePageSize, $wrapper);
                if (currentIndex == t.timelineDisplayIndex) { break; }
            }
        };

        t.toggleTaskComments = function (e) {
            e.preventDefault();
            var $button = $(e.currentTarget);
            var $list = $button.closest('.task-comment-list');
            var expandAll = !$list.hasClass('is-expanded');

            $list.toggleClass('is-expanded', expandAll);
            $button.html(expandAll
                    ? '<i class="bi bi-arrows-angle-contract"></i>'
                    : '<i class="bi bi-arrows-angle-expand faa-fast animated"></i>')
                .attr('title', expandAll ? 'Compress' : 'Expand');
        };

        t.refreshTaskTimeLine = function () {
            var formData = new FormData();
            formData.append('id', t.taskNo);
            formData.append('ticketmodule', 'true');

            var http = $.ajax({
                url: t.config.url.get_task_timeline,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                processData: false,
                contentType: false,
                data: formData
            });

            http.done(function (data) {
                if (typeof data == 'object' && data.status == 'success') {
                    t.timelineData = data.data || [];
                    t.timelineDisplayIndex = 0;

                    t.taskTimeline.empty();
                    t.taskTimeline.removeClass('hide');

                    var canView = (t.config.user.id == t.taskCreator) ||
                                (t.config.user.id == assign_to) ||
                                (t.config.user.id == t.config.data.assigned_to) ||
                                (t.config.user.role == 'SuperAdmin') ||
                                (jQuery.inArray('TaskView', t.config.permissions) !== -1);

                    if (!canView) {
                        t.taskTimeline.html('<div class="text-muted">No permission to view comments.</div>');
                        t.scrollToTaskCommentSection();
                        return;
                    }

                    if (t.timelineData.length === 0) {
                        t.taskTimeline.html('<div class="text-muted">No comments yet.</div>');
                        t.scrollToTaskCommentSection();
                        return;
                    }

                    var wrapper = $('<div class="comment-wrapper task-comment-wrapper"></div>');
                    t.taskTimeline.append(`
                        <div class="task-comment-list">
                            <div class="d-flex align-items-center" id="timelineTitle" style="font-weight:600; margin-bottom:10px;">
                                <span>Task Comments (${t.timelineData.length})</span>
                                <button class="btn btn-white btn-ex-com task-toggle-comments ms-auto"
                                    type="button"
                                    title="Expand">
                                    <i class="bi bi-arrows-angle-expand faa-fast animated"></i>
                                </button>
                            </div>
                        </div>
                    `);
                    t.taskTimeline.find('.task-comment-list').append(wrapper);

                    t.loadMoreTimelineEntries(t.taskTimelinePageSize, wrapper);
                    t.checkTaskTimelineScroll(wrapper);

                    wrapper.off('scroll.timeline').on('scroll.timeline', function () {
                        var $this = $(this);
                        if (t.timelineDisplayIndex < t.timelineData.length) {
                            var scrollTop = $this.scrollTop();
                            var scrollHeight = $this[0].scrollHeight;
                            var clientHeight = $this.innerHeight();
                            if (scrollTop + clientHeight >= scrollHeight - 20) {
                                t.loadMoreTimelineEntries(t.taskTimelinePageSize, wrapper);
                            }
                        }
                    });

                    t.scrollToTaskCommentSection();
                } else if (data.msg != '') {
                    sweetAlert('center', 'error', data);
                }
            });
        };

        t.loadMoreTimelineEntries = function (count, wrapper) {
            var $wrapper = wrapper && wrapper.length ? wrapper : t.taskTimeline.find('.task-comment-wrapper');
            if (!$wrapper.length) { return; }

            var start = t.timelineDisplayIndex;
            var end   = Math.min(start + count, t.timelineData.length);

            if (
                t.config.user.id == t.taskCreator        ||
                t.config.user.id == assign_to             ||
                t.config.user.id == t.config.data.assigned_to ||
                t.config.user.role == 'SuperAdmin' ||
                jQuery.inArray('TaskView', t.config.permissions) !== -1
            ) {
                for (var i = start; i < end; i++) {
                    $wrapper.append(t.taskCommentHtml(t.timelineData[i]));
                }

                t.timelineDisplayIndex = end;
            }
        };

        t.taskTimeline.off('click.taskTimeline')
            .on('click.taskTimeline', '.tri-view',  $.proxy(t.taskAttachmentView))
            .on('click.taskTimeline', '.tri-download',  $.proxy(t.taskAttachmentDownload))
            .on('click.taskTimeline', '.task-toggle-comments', $.proxy(t.toggleTaskComments));
        t.frmTaskComment.el.btnSubmit.on('click', $.proxy(t.frmTaskCommentSubmit));
        t.frmTaskCommentTokenize();
        t.refreshTaskTimeLine();
    };

    $(document).on('click', '.task-tab-link', function () {
        t.activateTaskTab($(this));
        t.frmTaskCommentTokenize();
        if (t.frmTaskComment.el.comment.length) {
            t.frmTaskComment.el.comment.val("").summernote('code', '');
        }
        $('#frm_task_comment').find('#shows_error').html("");
        t.taskAttachment.empty();
    });

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
        formData.set('company_id', t.frmTask.company.val() || companyId || 1);
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
        t.frmTask.departmentId.val(null).trigger("change");
        t.frmTask.problemCategoryId.val(null).trigger("change");
        t.frmTask.subCategoryId.val(null).trigger("change");
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

    initSummernote(
        t.frmTask.description,
        'Enter Task Description',
        140
    );


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
                                            parseInt(date[2]),      
                                            parseInt(date[1]) - 1,  
                                            parseInt(date[0]),      
                                            parseInt(time[0]),     
                                            parseInt(time[1])    
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

    function hideError() {
        $("#due_date_error").hide();
    }

    t.frmTask.btnSubmit.off("click").on("click", function (e) {
        e.preventDefault();
        const dueDateVal = t.frmTask.due_date.val();
        // const isValidDate = validateDueDate(dueDateVal);
        t.frmTask.description.val(t.frmTask.description.summernote("code"));

        if (t.frmTaskValidator.form() == false) {
            return false;
        }
        let formData = new FormData(t.frmTask[0]);
        let isEditMode = t.frmTask.task_id && t.frmTask.task_id.val() !== "";
        formData.append('type_id', 4);
        formData.append('status_id', t.frmTask.status_id.val() || 1);
        formData.set('company_id', t.frmTask.company.val() || companyId || 1);
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

    $(document).on('click', '.delete-task', function () {
        let taskId = $(this).data('id');
        let deleteUrl = config.url.deleteTask;
        t.deleteTask(taskId, deleteUrl);
    });

    t.deleteTask = function (taskId, deleteUrl) {
        sweetAlertConfirmation({
            message: 'Are you sure you want to delete this task?',
            onConfirm: function () {
                $.ajax({
                    url: deleteUrl + '/' + taskId,
                    type: 'GET',
                    success: function (data) {
                        if (data.status === "success") {
                            sweetAlert('center', 'success', data);
                            // t.relevantTaskTbl.ajax.reload();
                            t.relatedTask();
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

    t.escapeHtml = function (str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str).replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };
    t.truncateHtml = function (html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#historyTable_wrapper .plain-search").validate_str_param();
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
    $(document).on("click", '.task-searchbox', $.proxy(t.tableSearch));
    
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
    t.frmTask.status_id.select2({
        width: '100%',
        dropdownParent: t.frmTask.status_id.parent(),
    });
    
    t.frmTask.priority_id.select2({
        width: '100%',
        dropdownParent: t.frmTask.priority_id.parent(),
    });

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
        if(t.config.department && t.config.department_id){
            let option = new Option(t.config.department, t.config.department_id, true, true);
            t.frmTask.departmentId.append(option).trigger('change');
        }
        t.optionsCompany();
        t.mdlTask.modal("show");
    });

    t.frmTask.assigned_to.select2($.extend({}, {
        dropdownParent: t.frmTask.assigned_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                const excludeUserId = creator_id;
                const filteredResults = data.results.filter(function (user) {
                    return user.id !== excludeUserId;
                });
                return {
                    results: filteredResults,
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
    }));

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        var truncatedText = s.text.length > 30 ? s.text.substring(0, 30) + "..." : s.text;
        a += "<div class='so-t' title='" + s.text + "'><i class='fa fa-user' style='padding-right: 3px;'></i>" + truncatedText + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            var truncatedEmail = s.email.length > 30 ? s.email.substring(0, 30) + "..." : s.email;
            a += "<div class='so-t' title='" + s.email + "'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + truncatedEmail + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    function formatTaskDateTime(date) {
        let dd = String(date.getDate()).padStart(2, '0');
        let mm = String(date.getMonth() + 1).padStart(2, '0');
        let yyyy = date.getFullYear();

        let hours = String(date.getHours()).padStart(2, '0');
        let minutes = String(date.getMinutes()).padStart(2, '0');

        return `${dd}/${mm}/${yyyy} ${hours}:${minutes}`;
    }

    t.frmUpdateTaskStatus.el.task_status_id.select2({
        width: '100%',
        dropdownParent: t.frmUpdateTaskStatus.el.task_status_id.parent(),
    })

    t.refillTaskStatus = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmUpdateTaskStatus.el.task_status_id.empty();
        $.each(t.config.task_statuses, function (i, k) {
            t.frmUpdateTaskStatus.el.task_status_id.append(new Option(k.name, k.id));
        });
        t.frmUpdateTaskStatus.el.task_status_id.trigger("change");
    }

    $(document).on('click', '.update-task-status', function () {
        t.task_id = $(this).data('id');
        t.status_id = $(this).data('status');
        t.refillTaskStatus();
        t.frmTaskCommentTokenize();
         t.frmUpdateTaskStatus.el.statusWrapper.show();
        t.frmUpdateTaskStatus.el.task_id.val(t.task_id);
        t.frmUpdateTaskStatus.el.task_status_id.val(t.status_id).trigger("change");
        t.frmUpdateTaskStatus.el.task_comment.val('').summernote('code', '');
        t.frmTaskStatusValidator.resetForm();
        t.frmUpdateTaskStatus.el.attachment_updates.empty();
        t.taskUpdateMdl.css('z-index', 9999);
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

    let uploadedUpdateTaskStatusFiles = [];
    let totalUpdateTaskStatusFileSize = 0;
    t.frmUpdateTaskStatus.el.attachment_dropper_cover.filedrop({
        fallback_id: "update_task_attachments",
        url: t.config.url.add_task_attachment,
        paramname: "attachment",
        data: {
            "_token": t.config.token,
            "id": function () { return t.frmUpdateTaskStatus.el.task_id.val(); },
            "tmp_id": function () { return t.frmUpdateTaskStatus.el.temp_id.val(); }
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
                t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i + " .name").text(decodeURIComponent(response.data.original_file_name));
                t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i).attr("data-id", response.data.id);
                t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i + " .progress").fadeOut("slow");
            } else {
                t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i + " .name").text(file.name + " upload failed");
                t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i + " .upload_length").addClass("progress-bar-danger");
            }
        },
        progressUpdated: function (i, file, progress) {
            t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i + " .upload_length").css("width", progress + "%");
        },
        beforeSend: function (file, i, done) {
            var matched = t.frmUpdateTaskStatus.find('.count_img')
            if (uploadedUpdateTaskStatusFiles.includes(file.name)) {
                sweetAlert('center', 'error', { 'msg': 'This file has been already uploaded.' });
                return;
            }
            uploadedUpdateTaskStatusFiles.push(file.name);
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
                if (totalUpdateTaskStatusFileSize + file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'Total File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return;
                }

                totalUpdateTaskStatusFileSize += file.size;
                let fileSizeKB = Math.ceil(file.size / 1024);
                let fileNameWithSize = file.name + ' [' + fileSizeKB + ' KB]';

                if (t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i).length) {
                    t.frmUpdateTaskStatus.el.attachment_updates.find("#attachs" + i).attr("id", "attachs" + (Math.random().toString()).substring(2, 15));
                }
                t.frmUpdateTaskStatus.el.attachment_updates.append('<div id="attachs' + i + '" class="attachs pad-top count_img" data-size="' + file.size + '"><div class="bord-btm clearfix"><p class="pull-left">' + fileNameWithSize + '</p><span style="cursor:pointer" class="remove-attach pull-right" data-size="' + file.size + '"><i class="fa fa-remove"></i> Remove</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div></div>');
                done();
            } else {
                var data = {
                    'msg': config.translations.upload_file,
                };
                sweetAlert('center', 'error', data);
            }
        },
        dragOver: function () {
            t.frmUpdateTaskStatus.el.attachment_dropper.show();
        },
        drop: function () {
            t.frmUpdateTaskStatus.el.attachment_dropper.show();
            $(".note-editor.panel-default").removeClass('dragover');
        }
    });

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

    t.frmUpdateTaskStatus.on("click", "#update-dropper", function (e) {
        e.preventDefault();
        document.getElementById('update_task_attachments').click();
    });

    t.frmUpdateTaskStatus.on("click", "#task_file_triggers", function (e) {
        e.preventDefault();
        document.getElementById('update_task_attachments').click();
    });

    initSummernote(
        t.frmUpdateTaskStatus.el.task_comment,
        'Enter comment',
        140
    );
    t.frmUpdateTaskStatus.on("click", ".remove-attach", $.proxy(t.frmUpdateTaskStatus.el.attachment_updates.removeAttach));

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

    t.cc_master = new CcMaster(config);
    t.cc_master.visibilityDecision();
};

var loadCardList = function (config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');

    t.page = $('.content');
    t.boardId = t.page.find('#board_id');
    t.priority = t.page.find('#filter_by_priority_id');
    t.assigned_to = t.page.find('#filter_by_assigned_to');
    t.creator_id = t.page.find('#filter_by_creator_id');
    t.department = t.page.find('#filter_by_department');
    t.prob_category = t.page.find('#filter_by_problem_category');
    t.sub_category = t.page.find('#filter_by_sub_category');
    t.status = t.page.find('#filter_by_status_id');
    t.date = t.page.find('#filter_by_date');
    t.dateRange = t.page.find('#daterange');
    t.customField = t.page.find('#filter_by_custom_field');
    t.customFieldValue = t.page.find('#filter_by_custom_field_value');
    t.searchData = t.page.find('#searchPlain');
    t.sortbtns = t.page.find('#srqSortDrop');
    t.shortItems = t.page.find('.amg-sort-menu');
    t.sortAction = t.sortbtns.find('.sort-action');
    t.dropdownAction = t.sortbtns.find('.dropdown-action');
    t.groupbybtns = t.page.find(".groupby-buttons");
    t.show_members = t.page.find('#show_members');
    t.memberList = t.page.find('#memberList');
    t.memberListDiv = t.page.find('#memberListDiv');
    t.table = t.page.find('#myTable');

    t.taskTableMdl = t.page.find('#taskDataModal');
    t.taskTable = t.taskTableMdl.find('#taskTable');
    t.descriptonMdl = t.page.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    t.groupbyMdl = $("section.content").find("#groupbyModal");
    t.groupbyMdl.title = t.groupbyMdl.find(".modal-title");
    t.groupbyMdl.close = t.groupbyMdl.find(".close");
    t.groupbyMdl.btnGroupby = t.groupbyMdl.find("#btnGroupby");
    t.groupbyMdl.btnClear = t.groupbyMdl.find("#btnClear");
    t.groupbyMdl.frm = t.groupbyMdl.find("#frm-groupbyModal");
    t.groupbyMdl.frmEl = {};
    t.groupbyMdl.frmEl.groupby_date = t.groupbyMdl.frm.find("#groupby_date");
    t.groupbyMdl.frmEl.groupBy_id = t.groupbyMdl.frm.find("#groupBy_id");

    t.groupbyMdl.frmEl.groupby_date.select2({ width: "100%", dropdownParent: t.groupbyMdl.frmEl.groupby_date.parent() });
    t.groupbyMdl.close.add(t.groupbyMdl.btnClear).on('click', function () {
        t.groupbyMdl.modal('hide');
    });
    t.groupbyMdl.btnGroupby.off('click').on('click', function () {
        t.config.groupby_date = t.groupbyMdl.frmEl.groupby_date.val();
        t.config.groupBy = t.groupbyMdl.frmEl.groupBy_id.val();
        t.config.selectedStatuses = [];
        t.renderSortDropdown();
        t.load();
        t.groupbyMdl.modal('hide');
        if (t.config.tempSelectedLi) {
            $('.group-by-item .like-radio').removeClass('active_radio');
            t.config.tempSelectedLi.children('.like-radio').addClass('active_radio');
            $('.group_by #groupby-act').removeClass('d-none');
            $('.group_by #groupby-unact').addClass('d-none');
            t.config.tempSelectedLi = null;
        }
    })
    const escapeHtmlAttr = (str) => {
        return str
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    };

    t.allMember = {};
    t.allMembers = {};
    t.load = async function (e) {

        if (!$('#kanban-light-bg-style').length) {
            $('<style id="kanban-light-bg-style">' +
                '.light-bg-card .card-body, ' +
                '.light-bg-card .card-body *, ' +
                '.light-bg-card .card-footer, ' +
                '.light-bg-card .card-footer * ' +
                '{ color:#000000 !important; }' +
            '</style>').appendTo('head');
        }

        if (localStorage.getItem('cardMove') == 1) {
            $('#api_loader').removeClass('d-none');
        }
        t.loadMemberImage(t.boardId.val());

        t.httpPostPath = t.config.url.KanbanBoardList;

        try {
            let data = await $.ajax({
                url: t.httpPostPath,
                type: "POST",
                data: {
                    id: t.boardId.val(),
                    filters: t.cache_filter_values(),
                    search: t.searchData.val(),
                    order: t.config.sort_dir,
                    groupBy: t.config.groupBy,
                    groupby_date: t.config.groupby_date ?? '',
                    archived: t.config.archived
                }
            });

            if (localStorage.getItem('cardMove') == 1) {
                $('#api_loader').addClass('d-none');
            }

            if (data.status == 'danger') {
                var data_sw = {
                    'msg': data.msg,
                };
                sweetAlert('center', 'error', data_sw);
            }
            if (typeof data === "object") {
                localStorage.removeItem('kanban_categories');
                if(data.mapped_categories != []){
                    localStorage.setItem('kanban_categories', JSON.stringify(data.mapped_categories));
                }
                let statusesData = data.data.statuses;
                let statuses = Object.values(statusesData);
                let grouped = data.grouped;
                let kanbanContainer = $("#kanban-card");
                kanbanContainer.empty();
                getDepartmentCustomFields = t.config.url.get_kanban_fields + "/" + t.boardId.val();
                var customField = new CustomField(t.config);
                customField.displayCustomField(getDepartmentCustomFields, 'kanban', null, null);
                if (grouped) {
                    statuses.forEach((status, index) => {
                        let statusName = String(status.Sprint_name);
                        let groupby_filter = status.groupBy;
                        $('li.group-by-item[data-id="' + groupby_filter + '"]').find('span.like-radio').addClass('active_radio');
                        let tooltip_data = status.tooltip ? String(status.tooltip) : statusName;
                        let scrollable = true;
                        if (status.groupby_date == 1 || status.groupby_date == 2 || status.item_type == 1 || t.config.archived == true) {
                            scrollable = false;
                        }
                        let tooltipText = "";
                        if (status.groupby_date == 1 || status.groupby_date == 2) {
                            tooltipText = "This board is grouped, so cards cannot be moved.";
                        }
                        else if (status.item_type == 1) {
                            tooltipText = "This is the ticket board, so these cards cannot be moved.";
                        }
                        else if (t.config.archived == true) {
                            tooltipText = "This is the archived mode, so these cards cannot be moved.";
                        }
                        t.config.selectedStatuses = t.config.selectedStatuses || [];
                        let isHidden = t.config.selectedStatuses.length > 0 && !t.config.selectedStatuses.includes(statusName) ? 'display: none;' : '';

                        let kanbanColumn = $(`
                            <div class="kanban-column scrollable" id="status-${status.id}" data-item_type="${status.item_type ?? ''}" style="${isHidden}">
                                <div class="specific_loadingContainer">
                                  <div class="specific_loading">
                                     <svg id="kanban-cards-loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="60" height="60" version="1.1" id="Layer_1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                                        <g>
                                            <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                            c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                            c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                            c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                            </path>
                                        </g>
                                    </svg>
                                  </div>
                                </div>
                                <h3>
                                   <span class="kanban-title" data-toggle="tooltip" data-placement="bottom" data-title="${tooltip_data}">
                                        ${statusName?.length > 10 ? statusName?.substring(0, 10) + "..." : statusName}
                                    </span>
                                    ${status.item_type === 1 ?
                                `<span class="kanban-count" style="margin-left:8px;" data-total-count="">
                                        </span>`
                                :
                                `<span class="kanban-count" style="margin-left:8px;" data-total-count="">
                                        </span>`
                            }
                                    ${scrollable == false ? `
                                        <i class="bi bi-info-circle-fill col-md-1" data-bs-toggle="tooltip" data-placement="bottom" title="${tooltipText}"></i>
                                    ` : ''}

                                    ${status.item_type === 2 && status.id != null && t.config.permissions.includes('KanbanCutomBoardCardAdd') && t.config.archived !== true ? `
                                        <span class="kanban-actions">
                                            <i class="ps-icon bi bi-plus btn-Add" style="color:#000000" 
                                                data-bs-toggle="tooltip" data-original-title="Add" data-placement="bottom" data-status='${JSON.stringify({ ...status })}'></i>
                                        </span>
                                    ` : ""}
                                </h3>
    
                                <div class="mainDiv ${scrollable == true ? "sortable" : ''}" id="scrollableDiv_${String(statusName).replace(/\s+/g, '_')}" data-status="${statusName}" data-statusdata='${JSON.stringify({ ...status })}'
                                   data-page="1" data-item_type="${status.item_type}" ${status.item_type === 1 ? `data-statusname="${statusName}"` : ""} data-grouped="${grouped}" data-groupedbydata='${JSON.stringify({ ...status })}'>
                                </div>
    
                                ${status.item_type === 2 && status.id != null && t.config.archived !== true ? `
                                    <div class="card-div" style="margin:16px 10px;" data-status='${escapeHtmlAttr(JSON.stringify({ ...status }))}' data-grouped="${grouped}">
                                        <span class="add-card-wrapper add-card"  style="font-size: 18px;font-weight: 400; margin-left: 10px">
                                            <i class="ps-icon bi bi-plus add-card-icon" 
                                                data-bs-toggle="tooltip" data-original-title="Add"></i>
                                            Add a Card
                                        </span>    
                                    </div>
                                ` : ""}
                            </div>
                        `);

                        kanbanContainer.append(kanbanColumn);
                        t.loadBoardData(status, true);
                    });
                } else {
                    statuses.forEach(status => {
                        if (status.length != undefined) {
                            return;
                        }
                        let statusName = status.Sprint_name;
                        let scrollable = true;
                        t.config.selectedStatuses = t.config.selectedStatuses || [];
                        let isHidden = t.config.selectedStatuses.length > 0 && !t.config.selectedStatuses.includes(statusName) ? 'display: none;' : '';

                        if (status.access_type == 3 || status.item_name == 6 || t.config.archived == true) {
                            scrollable = false;
                        }

                        let tooltipText = "";
                        if (status.access_type == 3) {
                            tooltipText = "you are viewer of this board so you can not move the card";
                        }
                        else if (status.item_name == 6) {
                            tooltipText = "This is close so can not move the card";
                        }
                        else if (t.config.archived == true) {
                            tooltipText = "This is the archived mode, so these cards cannot be moved.";
                        }
                        let kanbanColumn = $(`
                            <div class="kanban-column scrollable" id="status-${status.id}" data-item_type="${status.item_type}" style="${isHidden}">
                                <div class="specific_loadingContainer">
                                    <div class="specific_loading">
                                        <svg id="kanban-cards-loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="60" height="60" version="1.1" id="Layer_1"
                                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                                        <g>
                                            <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                            c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                            c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                            c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                            c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                            </path>
                                        </g>
                                    </svg>
                                   </div>
                                </div>
                                <h3>
                                   <span class="kanban-title" data-bs-toggle="tooltip" data-placement="bottom" data-title="${statusName}" data-status-id="${status.id}">
                                        ${statusName?.length > 10 ? statusName?.substring(0, 10) + "..." : statusName}
                                    </span>
                                    ${status.item_type === 1 ?
                                `<span class="kanban-count" style="margin-left:8px;" data-total-count="">
                                        </span>`
                                :
                                `<span class="kanban-count" style="margin-left:8px;" data-total-count="">
                                        </span>`
                            }
                                    ${scrollable == false ? `
                                        <i class="bi bi-info-circle-fill col-md-1" data-bs-toggle="tooltip" data-placement="bottom" title="${tooltipText}"></i>
                                    ` : ''}

                                    ${status.item_type === 2 ? `
                                        <span class="kanban-actions">
                                        ${t.config.permissions.includes('KanbanCutomBoardCardClone') && t.config.archived !== true ? `
                                            <i class="ps-icon bi bi-copy btn-Clone"
                                                data-bs-toggle="tooltip" title="Clone" data-placement="bottom"
                                                data-status='${escapeHtmlAttr(JSON.stringify({ ...status }))}'></i>
                                        ` : ''}
                                        
                                        ${t.config.permissions.includes('KanbanCutomBoardCardAdd') && t.config.archived !== true ? `
                                            <i class="ps-icon bi bi-plus btn-Add"
                                                data-bs-toggle="tooltip" title="Add" data-placement="bottom"
                                                data-status='${escapeHtmlAttr(JSON.stringify({ ...status }))}'></i>
                                        ` : ''}
                                        </span>
                                    ` : ""}
                                </h3>
    
                                <div class="mainDiv ${scrollable == true ? "sortable" : ''}" id="scrollableDiv_${status?.item_name?.replace(/\s+/g, '_')}" data-status="${status?.item_name}" data-statusdata='${escapeHtmlAttr(JSON.stringify({ ...status }))}'
                                  data-page="1"  data-item_type="${status.item_type}" ${status.item_type === 1 ? `data-statusname="${statusName}"` : ""}>
                                </div>
    
                                ${status.item_type === 2 ? `
                                    <div class="card-div" style="margin:16px 10px;" data-status='${escapeHtmlAttr(JSON.stringify({ ...status }))}'>
                                        ${t.config.permissions.includes('KanbanCutomBoardCardAdd') && t.config.archived !== true ? `
                                            <span class="add-card-wrapper add-card" style="font-size: 18px; font-weight: 400; margin-left: 10px;">
                                                <i class="ps-icon bi bi-plus add-card-icon"
                                                    data-toggle="tooltip" data-original-title="Add"></i>
                                                Add a Card
                                            </span>
                                        ` : ''}
                                    </div>
                                ` : ''}
                            </div>
                        `);

                        kanbanContainer.append(kanbanColumn);
                        t.loadBoardData(status);
                    });
                }
                t.initializeSortable();
                updateDropdownOptions();
                $('.kanban-column').each(function () {
                    var itemType = $(this).data('item_type');
                    if (itemType == 1) {
                        $('#btnCreateTicket').removeClass('d-none');
                        $('.btn-import').addClass('d-none');
                        $('.btn-getarchived').addClass('d-none');
                        $('.btn-markarchived').addClass('d-none');
                        $('.btn-Add').addClass('d-none');
                        $('.board-legends-items').empty().removeAttr('style');
                        if (grouped == false) {
                            t.renderStatuses(data.data.statuses, 1);
                        }
                    } else {
                        $('#btnCreateTicket').addClass('d-none');
                        $('.btn-getarchived').removeClass('d-none');
                        if (t.config.user.id == t.config.board_owner) {
                            $('.btn-markarchived').removeClass('d-none');
                        } else {
                            $('.btn-markarchived').addClass('d-none');
                        }
                        $('.btn-import').removeClass('d-none');
                        $('.btn-Add').removeClass('d-none');
                        if (grouped == false) {
                            t.renderStatuses(data.data.statuses.custom_status, 2);
                        }
                    }
                });
            }
        } catch (error) {
            console.error("Error loading Kanban board:", error);
        }
    };

    t.renderStatuses = function (statuses, type) {
        const container = $('.board-legends-items');
        container.empty().removeAttr('style');
        const getName = (item) => type == 1 ? item.Sprint_name : item.name;

        if (!statuses || !statuses.length) {
            container.append('<li><span>No statuses available</span></li>');
            return;
        }

        const visible = statuses.slice(0, 4);
        const hidden = statuses.slice(4);
        visible.forEach(item => {
            container.append(`
                <li class="board-legends-item">
                    <div class="board-legends-item-color-code"
                        style="background-color:${item.color_code ?? '#00008a'}; border:1px solid black;">
                    </div>
                    <span class="board-legends-item-label b5-text">
                        ${getName(item).length > 20 ? getName(item).slice(0, 20) + '...' : getName(item)}
                    </span>
                </li>
            `);
        });

        if (hidden.length) {
            container.append(`
                <div class="show-more-legends-dropdown">
                    <button class="show-more-btn">+${hidden.length}<span class="text-white"> more</span></button>
                    <div class="show-more-legends-content">
                        <ul class="more-status-list"></ul>
                    </div>
                </div>
            `);

            const dropdownList = container.find(".more-status-list");

            hidden.forEach(item => {
                dropdownList.append(`
                    <li class="board-legends-item">
                        <div class="board-legends-item-color-code"
                            style="background-color:${item.color_code ?? '#00008a'}; border:1px solid black;">
                        </div>
                        <span class="board-legends-item-label">
                            ${getName(item).length > 20 ? getName(item).slice(0, 20) + '...' : getName(item)}
                        </span>
                    </li>
                `);
            });
        }
    }
    function applyColumnLayout(columnsPerRow) {
        const kanbanCard = document.getElementById("kanban-card");
        if (!kanbanCard) {
            return;
        }

        kanbanCard.className = kanbanCard.className.replace(/columns-\d+|columns-all/g, '').trim();
        if (columnsPerRow === 'all') {
            kanbanCard.classList.add('columns-all');
        } else {
            kanbanCard.classList.add(`columns-${columnsPerRow}`);
        }
    }

    function updateDropdownOptions() {
        const kanbanCard = document.getElementById("kanban-card");
        const dropdown = document.getElementById('columnsPerRow');

        if (!kanbanCard || !dropdown) {
            return;
        }

        const columnCount = kanbanCard.querySelectorAll('.kanban-column').length;

        const maxColumns = Math.min(columnCount, 4);


        dropdown.innerHTML = '';

        const allOption = document.createElement('option');
        allOption.value = 'all';
        allOption.textContent = 'All Columns';
        dropdown.appendChild(allOption);

        for (let i = 1; i <= maxColumns; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = `${i} Column${i > 1 ? 's' : ''}`;
            dropdown.appendChild(option);
        }

        const savedLayout = localStorage.getItem('kanban-columns-per-row') || 'all';

        if (savedLayout !== 'all' && parseInt(savedLayout) > maxColumns) {
            dropdown.value = 'all';
            localStorage.setItem('kanban-columns-per-row', 'all');
        } else {
            dropdown.value = savedLayout;
        }

        applyColumnLayout(dropdown.value);

        return maxColumns;
    }

    function initializeViewportControl() {
        const viewportDropdownHTML = `
            <div class="viewport-control">
                <select id="columnsPerRow" class="form-select" style="display: inline-block; width: 200px;">
                    <option value="all" selected>All Columns</option>
                </select>
            </div>
        `;

        if (!document.getElementById('columnsPerRow')) {
            const controlContainer = document.querySelector('.kanban-board-controls');

            if (controlContainer) {
                controlContainer.insertAdjacentHTML('beforeend', viewportDropdownHTML);
            } else {
                const kanbanCard = document.getElementById('kanban-card');
                if (kanbanCard) {
                    kanbanCard.insertAdjacentHTML('beforebegin', viewportDropdownHTML);
                }
            }
        }

        const dropdown = document.getElementById('columnsPerRow');

        if (dropdown) {
            dropdown.addEventListener('change', function () {
                const selectedValue = this.value;
                localStorage.setItem('kanban-columns-per-row', selectedValue);
                applyColumnLayout(selectedValue);
            });
        }
    }

    initializeViewportControl();
    t.loadBoardData = function (status, grouped = false) {
        return new Promise((resolve, reject) => {
            var boardElement;
            if (grouped) {
                boardElement = $(`#scrollableDiv_${String(status.Sprint_name).replace(/\s/g, '_').replace(/([!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~])/g, '\\$1')}`);
                boardElement.html(`
                    <div class="board-loader" style="text-align:center; padding: 20px;">
                        <svg id="kanban-cards-loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="60" height="60" version="1.1" id="Layer_1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                                <g>
                                <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                    c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                    c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                    c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                </path>
                            </g>
                        </svg>
                    </div>
                `);
            } else {
                boardElement = $(`#scrollableDiv_${status?.item_name?.replace(/\s/g, '_').replace(/([!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~])/g, '\\$1')}`);
                boardElement.html(`
                    <div class="board-loader" style="text-align:center; padding: 20px;">
                        <svg id="kanban-sprint-loader" class="fa-spin" stroke="currentColor" fill="currentColor" width="40" height="40" version="1.1" id="Layer_1"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                            x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                            <g>
                                <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                    c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                    c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                    c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                </path>
                            </g>
                        </svg>
                    </div>
                `);
            }
            var load_url = load_data = null;
            if (grouped) {
                load_url = t.config.url.getGroupedBoardData;
                load_data = {
                    board_id: t.boardId.val(),
                    status: status,
                    filters: t.cache_filter_values(),
                    search: t.searchData.val(),
                    order: t.config.sort_dir,
                    groupBy: t.config.groupBy,
                    groupby_date: t.config.groupby_date ?? '',
                    archived: t.config.archived
                };
            } else {
                load_url = t.config.url.GetBoardData;
                load_data = {
                    board_id: t.boardId.val(),
                    status: status,
                    filters: t.cache_filter_values(),
                    search: t.searchData.val(),
                    order: t.config.sort_dir,
                    groupBy: t.config.groupBy,
                    groupby_date: t.config.groupby_date ?? '',
                    archived: t.config.archived
                };
            }
            $.ajax({
                url: load_url,
                type: "POST",
                data: load_data,
                success: function (data) {
                    t.config.kanban_problem_category_ids = data.pro && data.pro.length > 0 ? data.pro : [];
                    t.config.kanban_sub_category_ids  = data.sub && data.sub.length > 0 ? data.sub : [];
                    boardElement.empty();
                    $(`#status-${status.id} .kanban-count`)
                        .attr('data-total-count', data.totalCount)
                        .text(`(${data.count}/${data.totalCount})`);
                    if (data.results && data.results.length > 0) {
                        t.loadBoardCards(data.results, boardElement, status, data.action_controls);
                    } else {
                        boardElement.empty().addClass("empty");
                    }
                    // boardElement.find('.kanban-item').tooltip();
                    resolve();
                },
                error: function (data) {
                    boardElement.html(`<div style="color: red; text-align:center; padding: 10px;">Failed to load data</div>`);
                    reject();
                }
            });
        });
    };


    $('body').popover({
        selector: '[data-toggle="popover"]',
        trigger: 'hover',
        container: 'body',
        html: true,
        placement: 'top'
    });
    t.loadBoardCards = function (data, boardElement, status, action_controls) {
        data.forEach((kanban, index) => {
            let styles = ``;
            let lightBgClass = '';
            if (kanban.status_name === 'Closed') {
                styles += `background-color:#ECECEC;`;
                lightBgClass = 'light-bg-card';
                if (kanban.priority === 'Critical') styles += `border:1px solid #FFA5A5;`;
            } else if (kanban.status_name === 'Resolved') {
                styles += `background-color:#E5FFEC;`;
                lightBgClass = 'light-bg-card';
                if (kanban.priority === 'Critical') styles += `border:1px solid #FFA5A5;`;
            }
            if (kanban.priority === 'Critical') {
                styles += `border:1px solid #FFA5A5;`;
            }

            const created_at = kanban.created_at;
            const tat_expire = kanban.tat_expire;

            function parseFlexibleDate(s) {
                if (!s) return null;

                if (s.includes('T') || s.endsWith('Z') || /^\d{4}-\d{2}-\d{2}/.test(s)) {
                    return new Date(s.replace(' ', 'T'));
                }

                if (/^\d{4}-\d{2}-\d{2} /.test(s)) {
                    return new Date(s.replace(' ', 'T'));
                }

                const cleaned = s.replace(',', '').trim();
                const year = new Date().getFullYear();
                const candidate = new Date(`${cleaned} ${year}`);

                const now = new Date();
                const monthsDiff = (candidate.getFullYear() - now.getFullYear()) * 12 + (candidate.getMonth() - now.getMonth());
                if (monthsDiff > 6) {
                    return new Date(`${cleaned} ${year - 1}`);
                }
                return candidate;
            }

            function formatDiffFromNow(targetDate) {
                const now = new Date();
                const diffMs = targetDate - now;
                const abs = Math.abs(diffMs);

                const days = Math.floor(abs / (1000 * 60 * 60 * 24));
                const hours = Math.floor((abs / (1000 * 60 * 60)) % 24);
                const minutes = Math.floor((abs / (1000 * 60)) % 60);

                const suffix = diffMs >= 0 ? 'left' : 'ago';

                if (days >= 1) {
                    return `${days} day${days > 1 ? 's' : ''} ${suffix}`;
                }

                return `${hours}h ${minutes}m ${suffix}`;
            }

            let timeText = "";
            const expireDate = parseFlexibleDate(tat_expire);
            if (!expireDate || isNaN(expireDate)) {
                timeText = "";
            } else {
                timeText = formatDiffFromNow(expireDate);
            }

            let raw = kanban.embedded_attachments;
            let json = [];
            if (raw && raw.trim() !== "") {
                try {
                    json = JSON.parse("[" + raw + "]");
                } catch (e) {
                    console.error("Attachment JSON parse error:", raw, e);
                }
            }

            let imageSection = "";
            if (json.length > 0 && json[0].file_name) {
                let firstAttachment = json[0];
                let fileExt = firstAttachment.file_name.split('.').pop().toLowerCase();

                let fileUrl = '';
                if (kanban.item_type == 1) {
                    fileUrl = `${t.config.asset}ticket/attachment/view/${firstAttachment.id}/1`;
                } else if (kanban.task_type == 2) {
                    if (firstAttachment.attachmnt_type != undefined && firstAttachment.attachmnt_type == 'ticket') {
                        fileUrl = `${t.config.asset}ticket/attachment/view/${firstAttachment.id}`;
                    } else {
                        fileUrl = `${t.config.asset}ticket/kanban-board/card_attachment/view/${firstAttachment.id}`;
                    }
                }
                let overlay = json.length > 0 ? `<div class="more-attachments-icon" ${(kanban.item_type == 1) ? `data-ticketid="${kanban.ticketID}"` : kanban.task_type == 2 ? `data-cardid="${kanban.id}"` : ''}> <i class="bi bi-paperclip"></i> ${json.length}</div>` : '';
                if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExt)) {
                    imageSection = `
                        <div class="card-image  collapseSection${kanban.id}" style="position: relative; height: 100px;">
                            <img src="${fileUrl}" 
                            alt="${firstAttachment.file_name}" 
                            style="width:100%; height:100%; object-fit:cover;" />
                            ${overlay}
                            <span style="position: absolute; bottom: 10px; left: 10px; color: white; font-weight: 500; text-shadow: 0 0 5px black;">
                                ${firstAttachment.file_name.length > 20 ? firstAttachment.file_name.substring(0, 20) + '...' : firstAttachment.file_name}
                            </span>
                        </div>
                    `;
                } else {
                    let iconClass = 'fa-file text-muted';
                    if (fileExt === 'pdf') {
                        iconClass = 'fa-file-pdf-o text-danger';
                    } else if (['xls', 'xlsx'].includes(fileExt)) {
                        iconClass = 'fa-file-excel-o text-success';
                    } else if (['doc', 'docx'].includes(fileExt)) {
                        iconClass = 'fa-file-word-o text-primary';
                    } else if (['ppt', 'pptx'].includes(fileExt)) {
                        iconClass = 'fa-file-powerpoint-o text-warning';
                    } else if (['zip', 'rar', '7z'].includes(fileExt)) {
                        iconClass = 'fa-file-archive-o text-secondary';
                    }

                    imageSection = `
                    <div class="card-file collapseSection${kanban.id}" style="width:100%; position:relative; height:100px; display:flex; align-items:center;margin:10px 0px 10px 0px; justify-content:center;background:#f0f0f0;box-shadow: rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset;">
                        <span style="text-align:center; color:#333; text-decoration:none;">
                            <i class="fa ${iconClass}" style="font-size:48px;"></i>
                            ${overlay}
                        </span>
                         <span style="position: absolute; bottom: 10px; left: 10px; color: white; font-weight: 500; text-shadow: 0 0 5px black;">
                            ${firstAttachment.file_name.length > 20 ? firstAttachment.file_name.substring(0, 20) + '...' : firstAttachment.file_name}
                        </span>
                    </div>
                `;
                }
            }
            let attachmentSection = imageSection ? `<div style="width:100%;overflow:auto;">${imageSection}</div>` : "";
            let dataPlacement = index === 0 ? 'bottom' : 'top';

            let cardHtml = `
            <div class="card ${lightBgClass}" data-card-id="${kanban.id}" data-user-id="${t.config.user.id}" style="${styles}" data-task-type="${kanban.task_type}"
            ${kanban.ticketID != null
                    ? `data-ticket-id="${kanban.ticketID}" 
                    ${(kanban.item_type == 1)
                        ? `data-department-id="${kanban.allDept}" 
                           data-comment-id="${kanban.commentRequired}" 
                           data-tat-id="${kanban.tat}" 
                           data-assigned-id="${kanban.assigned_to}" 
                           data-priority-id="${kanban.priority_id}" 
                           data-cc-emails="${kanban.cc_emails}"`
                        : ''}`
                    : ''}
                ${kanban.item_type === 1 ? `data-statusoldname="${kanban.statusName}"` : ''}  data-username="${t.config.full_name}">

                    <div class="card-header" style="background:${kanban.color_code ? kanban.color_code : '#00008a'} ;" >                       
                        <div class="subject" data-bs-toggle='tooltip' data-placement='${dataPlacement}' data-container="body" data-title='${kanban.item_type == 1 ? kanban.subject : kanban.title}'>
                            <span 
                                ${(kanban.item_type === 1)
                    ? `id="ticket_card_id" data-id="${kanban.ticketID}"`
                    : ''} style="font-weight:500" >
                                    ${(kanban.item_type === 1)
                    ? (kanban.subject.length > 15 ? `${kanban.subject.substring(0, 15)}...` : kanban.subject)
                    : (kanban.title.length > 15 ? `${kanban.title.substring(0, 15)}...` : kanban.title)
                }
                            </span>
                            <span class="expand-collapse-icon collapsed" aria-expanded="false" data-target=".collapseSection${kanban.id}">  
                                <svg width="15" height="13" viewBox="0 0 18 11" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.704104 8.20406L8.2041 0.704065C8.30862 0.599185 8.43281 0.51597 8.56956 0.459189C8.7063 0.402408 8.85291 0.373178 9.00098 0.373178C9.14904 0.373178 9.29565 0.402408 9.4324 0.459189C9.56915 0.51597 9.69334 0.599185 9.79785 0.704066L17.2979 8.20407C17.5092 8.41541 17.6279 8.70206 17.6279 9.00094C17.6279 9.29983 17.5092 9.58647 17.2979 9.79782C17.0865 10.0092 16.7999 10.1279 16.501 10.1279C16.2021 10.1279 15.9154 10.0092 15.7041 9.79782L9.00004 3.09375L2.29598 9.79875C2.08463 10.0101 1.79799 10.1288 1.4991 10.1288C1.20022 10.1288 0.913574 10.0101 0.702229 9.79875C0.490885 9.58741 0.37215 9.30076 0.372151 9.00188C0.372151 8.70299 0.490885 8.41635 0.702229 8.205L0.704104 8.20406Z" fill="white"></path>
                                </svg>
                            </span>
                        </div>
                </div>
                <div class="card-body" ${kanban.item_type === 1 ? `data-id="${kanban.ticketID}"` : kanban.task_type === 2 ? `data-id="${kanban.id}" data-task-type="${kanban.task_type}"` : ''} >
                    <div class="view-card" ${kanban.item_type === 1 ? `data-id="${kanban.ticketID}"` : kanban.task_type === 2 ? `data-id="${kanban.id}" data-task-type="${kanban.task_type}"` : ''}>
                        <div class="card-row">
                            <div>
                                <div class=" left-column">
                                    ${kanban.priority ? `
                                            <span class="field priorityIcons label-${kanban.priority && kanban.priority.name ? kanban.priority.name.toLowerCase() : (kanban.priority ? kanban.priority : '-')} priority-box">
                                                <span data-toggle='tooltip' data-title='Priority'>
                                                    ${kanban.priority && kanban.priority.name ? kanban.priority.name : (kanban.priority != null ? (kanban.priority) : "-")}
                                                </span>
                                            </span>
                                    ` : ''}

                                    ${kanban.ticketID ? `
                                    <span class="ticket-id-box" data-toggle="tooltip" data-title="Ticket ID">
                                        <a href="${t.config.url.baseurl}ticket/${kanban.ticketID}" target="_blank" style="color:#000000;">
                                            #${kanban.ticketID}
                                        </a>
                                    </span>
                                    ` : ''} 

                                    ${(kanban.status_name && kanban.status_name.trim() && (kanban.task_type == 2 || kanban.item_type == 2)) ? `
                                        <div class="field tat-box" data-toggle="tooltip" data-title="Status">
                                            <span>
                                                ${kanban.status_name.length > 8 ? `${kanban.status_name.substring(0, 8)}...` : kanban.status_name}
                                            </span>
                                        </div>
                                    ` : ''}

                                    ${kanban.item_type == 1 && t.config.taskModule === 1 && jQuery.inArray("TaskRead", t.config.permissions) !== -1 && (kanban.task_count > 0)? `
                                        <div class="task-box ticket-id-box task-data" data-bs-toggle="tooltip" title="Tasks" data-ticket-id="${kanban.ticketID}">
                                            <span style="color:#E3200B;">
                                                ${kanban.completed_tasks_count + "/" + kanban.task_count}
                                            </span>
                                        </div>
                                    ` : ''}
                                </div>

                                ${(kanban.id && kanban.id && (kanban.task_type == 2 || kanban.item_type == 2)) ? `
                                    <div style="display:flex;flex-direction:row; align-items:center; margin-top:12px; gap:2px;font-size:smaller" class="collapseSection${ kanban.id }">
                                        <span class="ticket-id-box b5-text" data-bs-toggle="tooltip" title="Card Id">#${kanban.id}</span>
                                    </div>
                                ` : ''}
                                <div style="display:flex;flex-direction:row; align-items:center; margin-top:12px; gap:2px;font-size:smaller">
                                    <svg width="14" height="11" viewBox="0 0 14 13" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 0C5.71443 0 4.45772 0.381218 3.3888 1.09545C2.31988 1.80968 1.48676 2.82484 0.994786 4.01256C0.502816 5.20028 0.374095 6.50721 0.624899 7.76809C0.875703 9.02896 1.49477 10.1872 2.40381 11.0962C3.31285 12.0052 4.47104 12.6243 5.73192 12.8751C6.99279 13.1259 8.29973 12.9972 9.48745 12.5052C10.6752 12.0132 11.6903 11.1801 12.4046 10.1112C13.1188 9.04229 13.5 7.78558 13.5 6.5C13.4982 4.77665 12.8128 3.12441 11.5942 1.90582C10.3756 0.687224 8.72335 0.00181989 7 0ZM7 12C5.91221 12 4.84884 11.6774 3.94437 11.0731C3.0399 10.4687 2.33495 9.60975 1.91867 8.60476C1.50238 7.59977 1.39347 6.4939 1.60568 5.427C1.8179 4.36011 2.34173 3.3801 3.11092 2.61091C3.8801 1.84172 4.86011 1.3179 5.92701 1.10568C6.9939 0.893462 8.09977 1.00238 9.10476 1.41866C10.1098 1.83494 10.9687 2.53989 11.5731 3.44436C12.1774 4.34883 12.5 5.4122 12.5 6.5C12.4984 7.95818 11.9184 9.35617 10.8873 10.3873C9.85617 11.4184 8.45819 11.9983 7 12ZM11 6.5C11 6.63261 10.9473 6.75979 10.8536 6.85355C10.7598 6.94732 10.6326 7 10.5 7H7C6.8674 7 6.74022 6.94732 6.64645 6.85355C6.55268 6.75979 6.5 6.63261 6.5 6.5V3C6.5 2.86739 6.55268 2.74022 6.64645 2.64645C6.74022 2.55268 6.8674 2.5 7 2.5C7.13261 2.5 7.25979 2.55268 7.35356 2.64645C7.44733 2.74022 7.5 2.86739 7.5 3V6H10.5C10.6326 6 10.7598 6.05268 10.8536 6.14645C10.9473 6.24022 11 6.36739 11 6.5Z" fill="currentColor" opacity="0.6"/>
                                    </svg>
                                    <span class="b5-text">${timeText && timeText != "" ? timeText :  'No Expiry'}</span>
                                </div>  
                            </div>
                            <div class="progress-circle-container collapseSection${kanban.id}" style="--percentage: ${kanban.progress_data.per}; 
                                --border-color: ${kanban.progress_data.borderColor};" 
                                data-toggle="tooltip" data-title="${Math.round(kanban.progress_data.per)}%">
                                <div class="progress-circle">
                                    <div class="progress-bar-circle" style="display:flex;align-items:center;justify-content:center">
                                        <span style="font-weight:500;" class="b5-text text-black">${Math.round(kanban.progress_data.per)}%</span>
                                    </div>
                                </div>
                                <div class="b7-text mt-1 text-center" style="font-size:9px !important;">${kanban.progress_data.text}</div>
                            </div>
                        </div>
                        ${kanban.created_at ? `
                            <div style="display:flex;flex-direction:row; align-items:center; margin-top:10px; gap:2px;font-size:smaller" class="collapseSection${kanban.id} collapse">
                                <svg width="12" height="11" viewBox="0 0 12 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 1.5H9.5V1C9.5 0.867392 9.44732 0.740215 9.35355 0.646447C9.25979 0.552678 9.13261 0.5 9 0.5C8.86739 0.5 8.74021 0.552678 8.64645 0.646447C8.55268 0.740215 8.5 0.867392 8.5 1V1.5H3.5V1C3.5 0.867392 3.44732 0.740215 3.35355 0.646447C3.25979 0.552678 3.13261 0.5 3 0.5C2.86739 0.5 2.74021 0.552678 2.64645 0.646447C2.55268 0.740215 2.5 0.867392 2.5 1V1.5H1C0.734784 1.5 0.48043 1.60536 0.292893 1.79289C0.105357 1.98043 0 2.23478 0 2.5V12.5C0 12.7652 0.105357 13.0196 0.292893 13.2071C0.48043 13.3946 0.734784 13.5 1 13.5H11C11.2652 13.5 11.5196 13.3946 11.7071 13.2071C11.8946 13.0196 12 12.7652 12 12.5V2.5C12 2.23478 11.8946 1.98043 11.7071 1.79289C11.5196 1.60536 11.2652 1.5 11 1.5ZM2.5 2.5V3C2.5 3.13261 2.55268 3.25979 2.64645 3.35355C2.74021 3.44732 2.86739 3.5 3 3.5C3.13261 3.5 3.25979 3.44732 3.35355 3.35355C3.44732 3.25979 3.5 3.13261 3.5 3V2.5H8.5V3C8.5 3.13261 8.55268 3.25979 8.64645 3.35355C8.74021 3.44732 8.86739 3.5 9 3.5C9.13261 3.5 9.25979 3.44732 9.35355 3.35355C9.44732 3.25979 9.5 3.13261 9.5 3V2.5H11V4.5H1V2.5H2.5ZM11 12.5H1V5.5H11V12.5Z" fill="currentColor" opacity="0.6" />
                                </svg>
                                <div>
                                    <span class="b5-text" data-toggle="tooltip" data-title="Created Date">
                                        ${new Date(kanban.created_at).toLocaleString('en-GB', {
                                            day: '2-digit',
                                            month: 'short',
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            hour12: true
                                        })}
                                    </span>
                                ${(kanban.expected_date || kanban.tat_expire) ? `-
                                ${(kanban.item_type == 1) ? `
                                    <span class="b5-text" data-toggle="tooltip" data-title="TAT Expire Date">
                                        ${new Date(kanban.tat_expire).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: true })}
                                    </span>
                                ` : ''}

                                    ${(kanban.task_type == 2) ? `
                                        <span class="b5-text" data-toggle="tooltip" data-title="Expected Date">
                                            ${new Date(kanban.expected_date).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit', hour12: true })}
                                        </span>
                                    ` : ''}
                                    ` : ''}
                                </div>
                           
                            </div>
                         ` : ''}

                    </div>
                </div>

                ${attachmentSection}
                
                <div class="card-footer">
                    <div style="
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: normal;
                        ">                                    
                            <div class="card-row" style="margin-top:0px">
                                <div>
                                    <div class="left-column">
                                       <div style="display:flex;flex-direction:row;">
                                            ${kanban.assigned_name ? (() => {
                    const assignedNames = kanban.assigned_name.split(',');
                    const assignedAvatars = kanban.profile_image?.split(',') || [];

                    const displayNames = assignedNames.join(', ').length > 10
                        ? `${assignedNames.join(', ').slice(0, 10)}...`
                        : assignedNames.join(', ');

                    const totalUsers = assignedNames.length;

                    let visibleCount = totalUsers;
                    let extraCount = 0;

                    if (totalUsers >= 4) {
                        visibleCount = 2;
                        extraCount = totalUsers - 2;
                    }

                    let tooltipContent = assignedNames.map(name => `${name}`).join(', <br>');
                    tooltipContent = tooltipContent.replace(/'/g, '&#39;');

                    const avatarsHtml = assignedAvatars.slice(0, visibleCount).map((avatar, index) => {
                        if (avatar) {
                            return `<img src="${avatar}" alt="${assignedNames[index]}"  class="user-image main-user" data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top" data-content="${assignedNames[index]}" style="width:20px; height:20px; border-radius:50%; ${totalUsers > 1 ? 'margin-right:-8px; border:2px solid #fff;' : ''}  object-fit:cover;">`;
                        } else {
                            return `<div class="userInitials main-user" style="width:20px; height:20px; border-radius:50%; background:#ccc; color:#fff; font-size:8px; display:flex; align-items:center; justify-content:center; margin-right:-8px; border:2px solid #fff;">${assignedNames[index][0] || ''}</div>`;
                        }
                    }).join('');

                    const extraBadge = extraCount > 0 ? `<div style="width:20px; height:20px; border-radius:50%; background:#E5F9FF; color:#0044FF; font-size:8px; display:flex; align-items:center; justify-content:center; border:2px solid #fff; margin-right:-8px;" title="+${extraCount} more">+${extraCount} </div>` : '';
                    return `<div style="display:flex; align-items:center;">${avatarsHtml}${extraBadge}</div><span data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top" data-content='${tooltipContent}' style="margin-left:${totalUsers == 1 ? '0px' : '10px'}"> ${displayNames}</span>`;
                })() : ''}</div></div></div><div style="display: flex; flex-direction: row;align-items: center;gap:6px;" >`
            if (t.config.archived !== true) {
                if (kanban.status_id != 6) {
                    if (status.access_type == 2 || status.access_type == 1) {
                        if (kanban.task_type == 2) {
                            cardHtml += `${t.config.permissions.includes('KanbanCutomBoardCardEdit') ? `<span id="editcard" data-id="${kanban.id}" data-toggle="tooltip" data-bs-original-title="Edit" data-status='${JSON.stringify({ ...status })}'>
                                <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                    <svg width="16" height="16" viewBox="0 0 19 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.8167 3.62219L14.5556 0.344414C14.3401 0.130006 14.0485 0.00964355 13.7445 0.00964355C13.4405 0.00964355 13.1489 0.130006 12.9334 0.344414L1.37225 11.8889L0.316698 16.4444C0.280285 16.6109 0.281533 16.7835 0.320352 16.9495C0.35917 17.1155 0.434578 17.2707 0.541067 17.4038C0.647555 17.5369 0.782435 17.6446 0.935852 17.7189C1.08927 17.7932 1.25735 17.8323 1.42781 17.8333C1.50721 17.8419 1.5873 17.8419 1.6667 17.8333L6.27225 16.7777L17.8167 5.24441C18.0311 5.02892 18.1515 4.73729 18.1515 4.4333C18.1515 4.12931 18.0311 3.83769 17.8167 3.62219ZM5.7167 15.7777L1.40003 16.6833L2.38336 12.45L11.0334 3.8333L14.3667 7.16664L5.7167 15.7777ZM15.1111 6.36108L11.7778 3.02775L13.7111 1.10553L16.9889 4.43886L15.1111 6.36108Z" fill="currentColor"/>
                                    </svg>
                                </span>
                            </span>`: ''}`
                        }
                    }
                }
                if (kanban.status_id != 5 && kanban.status_id != 6) {
                    if (status.access_type == 2 || status.access_type == 1) {
                        if (kanban.item_type == 1) {
                            cardHtml += `${t.config.permissions.includes('EditServiceTicket') ? `<span class="js-act-edit-ticket" id="editButton" data-id="${kanban.ticketID}" data-bs-toggle="tooltip" data-bs-original-title="Edit" data-status='${JSON.stringify({ ...status })}'>
                                <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">
                                    <svg width="16" height="16" viewBox="0 0 19 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.8167 3.62219L14.5556 0.344414C14.3401 0.130006 14.0485 0.00964355 13.7445 0.00964355C13.4405 0.00964355 13.1489 0.130006 12.9334 0.344414L1.37225 11.8889L0.316698 16.4444C0.280285 16.6109 0.281533 16.7835 0.320352 16.9495C0.35917 17.1155 0.434578 17.2707 0.541067 17.4038C0.647555 17.5369 0.782435 17.6446 0.935852 17.7189C1.08927 17.7932 1.25735 17.8323 1.42781 17.8333C1.50721 17.8419 1.5873 17.8419 1.6667 17.8333L6.27225 16.7777L17.8167 5.24441C18.0311 5.02892 18.1515 4.73729 18.1515 4.4333C18.1515 4.12931 18.0311 3.83769 17.8167 3.62219ZM5.7167 15.7777L1.40003 16.6833L2.38336 12.45L11.0334 3.8333L14.3667 7.16664L5.7167 15.7777ZM15.1111 6.36108L11.7778 3.02775L13.7111 1.10553L16.9889 4.43886L15.1111 6.36108Z" fill="currentColor"/>
                                    </svg>
                                </span> 
                            </span>`: ''}`
                        }
                    }

                    if (status.access_type == 2 || status.access_type == 1) {
                        if (kanban.item_type == 1) {
                            if (status.item_type != 5 && status.item_type != 6 && action_controls.ctrl_transfer == 1) {
                                cardHtml += `<span class="js-act-transfer" data-id="${kanban.ticketID}" data-bs-toggle="tooltip" data-bs-original-title="Transfer" data-bs-placement="bottom" data-status='${JSON.stringify({ ...status })}'>
                                    <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                        <svg width="14" height="25" viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17 7.03888H1L6.5 1.03888M1 11.0389H17L11.5 17.0389" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </span>`
                            }
                        }
                    }
                    if (status.access_type != 3) {
                        if (kanban.item_type == 1) {
                            if (status.item_type != 5 && status.item_type != 6 && action_controls.ctrl_assign == 1) {
                                cardHtml += `<span id="assignedToButton" data-id="${kanban.ticketID}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Assigned To" data-status='${JSON.stringify({ ...status })}'>
                                    <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                        <svg width="12" height="21" viewBox="0 0 18 21" fill="currentColor"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                d="M9 8.96124C9.39397 8.96124 9.78407 8.88365 10.1481 8.73288C10.512 8.58212 10.8427 8.36114 11.1213 8.08256C11.3999 7.80399 11.6209 7.47327 11.7716 7.10929C11.9224 6.74532 12 6.35521 12 5.96124C12 5.56728 11.9224 5.17717 11.7716 4.81319C11.6209 4.44922 11.3999 4.1185 11.1213 3.83992C10.8427 3.56135 10.512 3.34037 10.1481 3.1896C9.78407 3.03884 9.39397 2.96124 9 2.96124C8.20435 2.96124 7.44129 3.27731 6.87868 3.83992C6.31607 4.40253 6 5.16559 6 5.96124C6 6.75689 6.31607 7.51995 6.87868 8.08256C7.44129 8.64517 8.20435 8.96124 9 8.96124ZM9 10.9612C10.3261 10.9612 11.5979 10.4345 12.5355 9.49678C13.4732 8.55909 14 7.28732 14 5.96124C14 4.63516 13.4732 3.36339 12.5355 2.42571C11.5979 1.48803 10.3261 0.961243 9 0.961243C7.67392 0.961243 6.40215 1.48803 5.46447 2.42571C4.52678 3.36339 4 4.63516 4 5.96124C4 7.28732 4.52678 8.55909 5.46447 9.49678C6.40215 10.4345 7.67392 10.9612 9 10.9612ZM1.639 14.4092C2.784 12.8912 4.509 11.9612 6.714 11.9612H11.286C13.491 11.9612 15.216 12.8912 16.361 14.4092C17.482 15.8962 18 17.8772 18 19.9612C18 20.2265 17.8946 20.4808 17.7071 20.6684C17.5196 20.8559 17.2652 20.9612 17 20.9612C16.7348 20.9612 16.4804 20.8559 16.2929 20.6684C16.1054 20.4808 16 20.2265 16 19.9612C16 18.1792 15.554 16.6612 14.765 15.6132C14 14.5992 12.867 13.9612 11.285 13.9612H6.715C5.133 13.9612 4 14.5992 3.235 15.6132C2.445 16.6612 2 18.1792 2 19.9612C2 20.2265 1.89464 20.4808 1.70711 20.6684C1.51957 20.8559 1.26522 20.9612 1 20.9612C0.734784 20.9612 0.48043 20.8559 0.292893 20.6684C0.105357 20.4808 0 20.2265 0 19.9612C0 17.8772 0.518 15.8962 1.639 14.4092Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                </span>`
                            }
                        }
                    }
                    if (kanban.task_type == 2) {
                        if (status.access_type == 2) {
                            cardHtml += `${t.config.permissions.includes('KanbanCutomBoardCardDelete') ? `<span class="delete-card" data-id="${kanban.id}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Delete" data-status='${escapeHtmlAttr(JSON.stringify({ ...status }))}'>
                                <span style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">            
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="25" fill="currentColor"
                                        viewBox="0 0 256 256">
                                        <path
                                            d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z">
                                        </path>
                                    </svg>
                                </span>
                            </span>`: ''}`
                        }
                    }
                }
            }
            if (kanban.item_type == 1) {
                cardHtml += `<span id="ticketHistoryButton" data-id="${kanban.ticketID}" data-bs-toggle="tooltip" title="Ticket History" data-bs-placement="bottom" data-status='${JSON.stringify({ ...status })}'>
                    <svg width="17" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg" aria-labelledby="historyIconTitle" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" color="currentColor"> <title id="historyIconTitle">History</title> <polyline points="1 12 3 14 5 12"/> <polyline points="12 7 12 12 15 15"/> <path d="M12,21 C16.9705627,21 21,16.9705627 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,11.975305 3,12.3086383 3,13"/> </svg>
                </span>`;
            } else {
                cardHtml += `<span class="card-history" data-id="${kanban.id}" data-bs-toggle="tooltip" title="Card History">
                    <svg width="17" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg" aria-labelledby="historyIconTitle" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" color="currentColor"> <title id="historyIconTitle">History</title> <polyline points="1 12 3 14 5 12"/> <polyline points="12 7 12 12 15 15"/> <path d="M12,21 C16.9705627,21 21,16.9705627 21,12 C21,7.02943725 16.9705627,3 12,3 C7.02943725,3 3,7.02943725 3,12 C3,11.975305 3,12.3086383 3,13"/> </svg>
                </span>`;
            }
            cardHtml += `<div class="view-card" data-bs-placement="bottom" data-bs-toggle="tooltip" title="View Card"
                    ${kanban.item_type === 1 ? `data-id="${kanban.ticketID}"` : kanban.task_type === 2 ? `data-id="${kanban.id}" data-task-type="${kanban.task_type}"` : ''} >
                    <span style="display:flex; align-items:center;justify-content:center;height:25px; width:20px;">            
                        <svg width="16" height="16" viewBox="0 0 20 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.1649 6.63213C19.1375 6.57041 18.4758 5.10244 17.0047 3.63135C15.0445 1.67119 12.5688 0.635254 9.84375 0.635254C7.11875 0.635254 4.64296 1.67119 2.68281 3.63135C1.21171 5.10244 0.546868 6.57275 0.522649 6.63213C0.487112 6.71206 0.46875 6.79856 0.46875 6.88603C0.46875 6.97351 0.487112 7.06001 0.522649 7.13994C0.549993 7.20166 1.21171 8.66885 2.68281 10.1399C4.64296 12.0993 7.11875 13.1353 9.84375 13.1353C12.5688 13.1353 15.0445 12.0993 17.0047 10.1399C18.4758 8.66885 19.1375 7.20166 19.1649 7.13994C19.2004 7.06001 19.2188 6.97351 19.2188 6.88603C19.2188 6.79856 19.2004 6.71206 19.1649 6.63213ZM9.84375 11.8853C7.43906 11.8853 5.33828 11.011 3.59921 9.2876C2.88565 8.57798 2.27858 7.76881 1.79687 6.88525C2.27845 6.00161 2.88554 5.19242 3.59921 4.48291C5.33828 2.75947 7.43906 1.88525 9.84375 1.88525C12.2484 1.88525 14.3492 2.75947 16.0883 4.48291C16.8032 5.19225 17.4116 6.00144 17.8945 6.88525C17.3313 7.93682 14.8773 11.8853 9.84375 11.8853ZM9.84375 3.13525C9.10207 3.13525 8.37704 3.35519 7.76036 3.76724C7.14367 4.1793 6.66303 4.76497 6.3792 5.45019C6.09537 6.13541 6.02111 6.88941 6.1658 7.61684C6.3105 8.34427 6.66765 9.01246 7.1921 9.53691C7.71654 10.0614 8.38473 10.4185 9.11216 10.5632C9.83959 10.7079 10.5936 10.6336 11.2788 10.3498C11.964 10.066 12.5497 9.58533 12.9618 8.96864C13.3738 8.35196 13.5938 7.62693 13.5938 6.88525C13.5927 5.89101 13.1973 4.93778 12.4943 4.23475C11.7912 3.53171 10.838 3.13629 9.84375 3.13525ZM9.84375 9.38525C9.3493 9.38525 8.86595 9.23863 8.45482 8.96393C8.0437 8.68922 7.72327 8.29878 7.53405 7.84196C7.34483 7.38515 7.29532 6.88248 7.39178 6.39753C7.48825 5.91258 7.72635 5.46712 8.07598 5.11749C8.42561 4.76786 8.87107 4.52975 9.35602 4.43329C9.84098 4.33683 10.3436 4.38634 10.8005 4.57555C11.2573 4.76477 11.6477 5.08521 11.9224 5.49633C12.1971 5.90745 12.3438 6.3908 12.3438 6.88525C12.3438 7.5483 12.0804 8.18418 11.6115 8.65302C11.1427 9.12186 10.5068 9.38525 9.84375 9.38525Z" fill="currentColor"/>
                        </svg>
                    </span>
                </div>`;
            `${cardHtml}   
                        <div style="display:flex; align-items:center;justify-content:center;height:20px; width:20px;">
                            <svg width="16" height="16" viewBox="0 0 19 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.8167 3.62219L14.5556 0.344414C14.3401 0.130006 14.0485 0.00964355 13.7445 0.00964355C13.4405 0.00964355 13.1489 0.130006 12.9334 0.344414L1.37225 11.8889L0.316698 16.4444C0.280285 16.6109 0.281533 16.7835 0.320352 16.9495C0.35917 17.1155 0.434578 17.2707 0.541067 17.4038C0.647555 17.5369 0.782435 17.6446 0.935852 17.7189C1.08927 17.7932 1.25735 17.8323 1.42781 17.8333C1.50721 17.8419 1.5873 17.8419 1.6667 17.8333L6.27225 16.7777L17.8167 5.24441C18.0311 5.02892 18.1515 4.73729 18.1515 4.4333C18.1515 4.12931 18.0311 3.83769 17.8167 3.62219ZM5.7167 15.7777L1.40003 16.6833L2.38336 12.45L11.0334 3.8333L14.3667 7.16664L5.7167 15.7777ZM15.1111 6.36108L11.7778 3.02775L13.7111 1.10553L16.9889 4.43886L15.1111 6.36108Z" fill="currentColor"/>
                            </svg>
                        </div>  
                        </div>
                    </div>
                </div>
            </div>`;
            boardElement.append(cardHtml);
        });

        boardElement.find('[data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this, {
                boundary: document.body,
                offset: [0, 10],
                container: 'body',
            });
        });
    }

    $(document).off("click", ".more-attachments-icon").on("click", ".more-attachments-icon", function (e) {
        e.preventDefault();
        let ticketid = $(this).data("ticketid");
        let cardId = $(this).data("cardid");

        $.ajax({
            url: t.config.url.getItemsAttachments,
            type: "GET",
            data: { ticketid: ticketid, cardId: cardId },
            success: function (res) {
                var collection = [];

                res.forEach(file => {
                    let ext = file.ext ? file.ext.toLowerCase() : "";
                    if (["jpg", "jpeg", "png", "gif", "webp"].includes(ext)) {
                        collection.push({
                            href: file.url,
                            title: file.file_name
                        });
                    } else {
                        let iconUrl = t.config.asset + "imgs/icons/default-file.jpg";
                        if (ext === "pdf") iconUrl = t.config.asset + "imgs/icons/pdf-icon.png";
                        if (["doc", "docx"].includes(ext)) iconUrl = t.config.asset + "imgs/icons/doc-icon.png";
                        if (["xls", "xlsx"].includes(ext)) iconUrl = t.config.asset + "imgs/icons/xls-icon.png";
                        if (["ppt", "pptx"].includes(ext)) iconUrl = t.config.asset + "imgs/icons/ppt-icon.png";
                        if (["zip", "rar"].includes(ext)) iconUrl = t.config.asset + "imgs/icons/zip-icon.png";

                        collection.push({
                            href: iconUrl,
                            title: `<a href="${file.url}" target="_blank" style="color:#fff;">${file.file_name} (<i class="bi bi-download"></i>)</a>`
                        });
                    }
                });
                if (collection.length > 0) {
                    $.swipebox(collection, {
                        afterClose: function () {
                        }
                    });
                }
            },
            error: function (err) {
                console.error("Unable to fetch attachments", err);
            }
        });
    });

    $(document).on('click', '.btn-Clone', function () {
        const status = $(this).data('status');

        Swal.fire({
            title: "Do you want to clone Item?",
            icon: "question",
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: "Yes, clone with task",
            denyButtonText: "No, clone only name",
            cancelButtonText: "Close",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                t.cloneKanbanItem(status, true);
            } else if (result.isDenied) {
                t.cloneKanbanItem(status, false);
            }
        });
    });

    t.cloneKanbanItem = function (status, cloneTasks) {
        $.ajax({
            url: t.config.url.cloneKanbanItem,
            method: 'POST',
            data: {
                item_id: status.id,
                clone_tasks: cloneTasks ? 1 : 0
            },
            success: function (res) {
                if (res.status === 'success') {
                    sweetAlert('center', 'success', res);
                    t.load();
                } else {
                    sweetAlert('center', 'error', res);
                }
            },
            error: function () {
                var data = {
                    'msg': config.translations.something_went_wrong,
                };
                sweetAlert('center', 'error', data);
            }
        });
    }


    $(document).on('click', '.kanban-title', function () {
        const $title = $(this);
        const currentText = $title.data('title') || $title.text().trim();

        const $column = $title.closest('.kanban-column');
        const $mainDiv = $column.find('.mainDiv');

        const itemType = $column.data('item_type');
        const isGrouped = $mainDiv.data('grouped') === true;

        if (itemType === 2 && (!isGrouped || isGrouped === false)) {
            $title.next().removeClass('in')
            const statusData = $mainDiv.data('statusdata') || {};
            const statusId = statusData.id ?? null;

            const input = $('<input type="text" class="edit-kanban-title-input" style="width:50%; font-size: 16px;" />')
                .val(currentText)
                .attr('data-original-text', currentText)
                .attr('data-status-id', statusId)
                .attr('data-saved', 'false');

            $title.replaceWith(input);
            input.focus();
        }
    });

    $(document).on('blur', '.edit-kanban-title-input', function () {
        const $input = $(this);
        if ($input.attr('data-saved') === 'false') {
            $input.attr('data-saved', 'true');
            t.saveKanbanTitle($input);
        }
    });

    $(document).on('keydown', '.edit-kanban-title-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const $input = $(this);
            if ($input.attr('data-saved') === 'false') {
                $input.attr('data-saved', 'true');
                t.saveKanbanTitle($input);
            }
        }
    });

    t.saveKanbanTitle = function ($input) {
        const newTitle = $input.val().trim();
        const HtmlPattern = /<[^>]*>?/gm;
        const original = $input.attr('data-original-text');
        const statusId = $input.data('status-id');
        if (newTitle === '' || HtmlPattern.test(newTitle)) {
            if (newTitle === '') {
                alert('Title cannot be blank.');
            } else {
                alert('Invalid characters or Html Tags are not allowed!')
            }
            const input_field = $('<input type="text" class="edit-kanban-title-input" style="width:50%; font-size: 16px;" />')
                .val(original)
                .attr('data-status-id', statusId)
                .attr('data-original-text', original)
                .attr('data-saved', 'false');

            $input.replaceWith(input_field);
            input_field.focus();
            return;
        }

        const titleElement = $(`<span class="kanban-title" data-toggle="tooltip" data-placement="bottom" data-title="${newTitle}">
            ${newTitle.length > 15 ? newTitle.substring(0, 15) + "..." : newTitle}
        </span>`);

        $input.replaceWith(titleElement);
        if (newTitle !== original) {
            $.ajax({
                url: t.config.url.editKanbanItem,
                method: 'POST',
                data: {
                    id: statusId,
                    new_title: newTitle,
                    _token: $('meta[name="csrf-token"]').attr('content') // if CSRF is needed
                },
                success: function (response) {
                    if (response.status !== 'success') {
                        alert(response.message);
                        const input_field = $('<input type="text" class="edit-kanban-title-input" style="width:50%; font-size: 16px;" />')
                            .val(original)
                            .attr('data-status-id', statusId)
                            .attr('data-original-text', original)
                            .attr('data-saved', 'false');

                        titleElement.replaceWith(input_field);
                        input_field.focus();
                    }
                },
                error: function () {
                    alert('Failed to update title');
                    const input_field = $('<input type="text" class="edit-kanban-title-input" style="width:50%; font-size: 16px;" />')
                        .val(original)
                        .attr('data-status-id', statusId)
                        .attr('data-original-text', original)
                        .attr('data-saved', 'false');

                    titleElement.replaceWith(input_field);
                    input_field.focus();
                },
            });
        }
    };

    let $kanbanBoard = $("#kanban-board");
    let scrollSpeed = 5;
    let maxScrollSpeed = 30;
    let acceleration = 0.5;
    let scrollDirection = 0;
    let scrollAnimationFrame;

    t.loadRequestForm_onStatus = function (newStatus, ticketId) {
        var formAttr = $('#ticket_status_card').find('option[value="' + newStatus + '"]').attr('form');
        if (formAttr != undefined && formAttr != 0) {
            var status_form = formAttr;
            var status_id = newStatus;
            if (status_form != 0) {
                t.getStatusFormIdViaTicketProblemCategory(newStatus, ticketId).then(function (form_id) {
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
                                    $('#status_requested_form').find("#form_id").val(form_id);
                                    $('#mdl-status-servicerequest').find("#Status_saveData").text("Save");
                                    var form_name = result.data.form_name ? result.data.form_name : "Service Request Form";
                                    $('#mdl-status-servicerequest').find(".modal-title").html(form_name);
                                    $('#status_requested_form').find("#status_id").val(status_id);
                                    $('#status_requested_form').find("#ticket_id").val(ticketId);
                                    t.loadStatusRequestedForm(result.data.fields);
                                    $('#status_requested_form').find('#field_values').val(result.data.fields);
                                    $('#mdl-status-servicerequest').modal("show");
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
    };

    t.loadStatusRequestedForm = function (fields) {
        var test = fields;
        var fbTemplate = document.getElementById('status-build-wrap'),
            $formContainer = $(document.getElementById('field_values')),

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
    $(document).on('click', '.task-data', function () {
        const ticketId = $(this).data('ticket-id');

        if (!ticketId) {
            console.error('Missing ticket ID');
            return;
        }
        t.taskTableMdl.modal('show');
        if ($.fn.DataTable.isDataTable('#taskTable')) {
            t.taskTable.DataTable().clear().destroy();
        }

        taskTable = t.taskTable.DataTable({
            processing: true,
            bDestroy: true,
            paging: true,
            pageLength: 10,
            scrollX: true,
            autoWidth: false,
            scrollCollapse: true,

            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1
            },
            ajax: {
                url: t.config.url.getRelatedTask,
                type: 'GET',
                data: {
                    _token: t.config.token,
                    ticket_id: ticketId,
                },
            },
            columns: [
                {data: 'task_company_name'},
                { data: 'task_no' },
                { data: 'task_name',
                    render: function(data, type, row){
                            if (!data) {
                                return '';
                            }
                            data = String(data);
                            if (data.length > 30) {
                                var truncated = t.truncateHtml(data, 30);
                                return `<p data-toggle="tooltip" data-placement="right" title="${data}">${truncated + '...'}</p>`;
                            } else {
                                return `<p>${data}</p>`;
                            }
                        }
                },
                {data: 'department_name'},
                {data: 'problem_category_name'},
                {data: 'sub_category_name'},
                { data: 'description',
                    render: function(data, type, row) {
                            if (!data) {
                                return '';
                            }
                            data = String(data);

                            if (data.length > 30) {
                                var truncated = t.truncateHtml(data, 30);
                                return truncated+ '<a class="read-more" style="cursor:pointer" data-full-text="' + t.escapeHtml(data) + '">...Read More</a>';
                            } else {
                                return  data ;
                            }
                        }
                },
                { data: 'status' },
                { data: 'priority' },  
                { data: 'start_date' },
                { data: 'due_date' },
                { data: 'end_date' },
                { data: 'cost' },
                 { data: 'creator' },
                { data: 'assigned_to' },
                {data:'change_by_module',
                    render: function(data,type,row){
                        if(data == 3){
                            return 'Problem Category';
                        }else if(data == 2){
                            return 'Ticket';
                        }else if(data == 1){
                            return 'Task';
                        }else{
                            return '';
                        }
                    }
                },
                { data: 'created_at' },
                { data: 'updated_at' },
            ],
            initComplete: function () {

                let customFilter = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="taskTable_length_wrapper"></div>
                       <div class="input-group table-search-btns searchbox_cover" style="max-width: 300px; border: 1px solid #ccc; border-radius: 10px;">
                            <span class="input-group-text border-0 bg-transparent ps-1 pe-0" style="color:#aaa;">
                                <i class="bi bi-search" style="font-size: 15px;"></i>
                            </span>
                            <input type="text"
                                class="form-control border-0 shadow-none searchbox"
                                placeholder="Search..."
                                style="border-radius: 0; background: transparent; padding: 4px 6px;">
                        </div>
                    </div>
                `;

            $("#taskTable_wrapper .dataTables_filter").hide();
            $("#taskTable_wrapper").prepend(customFilter);

                $("#taskTable_length").appendTo("#taskTable_length_wrapper");

            $("#taskTable_filter").remove();

                $("#taskTable_wrapper .searchbox").on("keyup", function (e) {
                    if (e.keyCode == 13 || this.value.length == 0) {
                        taskTable.search(this.value).draw();
                    }
                });

                $(".task-search-btn").on("click", function (e) {
                    let val = $("#taskTable_wrapper .searchbox").val();
                    taskTable.search(val).draw();
                });
            }
        });
    });

    t.escapeHtml = function (str) {
        if (typeof str !== 'string') return '';
        return str.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };

    t.truncateHtml = function (html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    t.page.on("click", ".read-more-task-data", function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    });

    t.getStatusFormIdViaTicketProblemCategory = function (newStatus, ticketId) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: "GET",
                url: t.config.url.getFormByTicketProblemCategory,
                data: {
                    'ticket_id': ticketId,
                    'status_id': newStatus,
                },
                success: function (response) {
                    resolve(response.form_id);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                }
            });
        });
    }
    function smoothScroll() {
        if (scrollDirection !== 0) {
            let currentScroll = $kanbanBoard.scrollLeft();
            let newSpeed = Math.min(scrollSpeed + acceleration, maxScrollSpeed);
            $kanbanBoard.scrollLeft(currentScroll + newSpeed * scrollDirection);
            scrollSpeed = newSpeed;
            scrollAnimationFrame = requestAnimationFrame(smoothScroll);
        } else {
            scrollSpeed = 5; // Reset speed
            cancelAnimationFrame(scrollAnimationFrame);
        }
    }

    $("#kanban-board").sortable({
        items: ".kanban-column",
        handle: "h3",
        axis: "x",
        containment: "#kanban-wrapper",
        placeholder: "ui-state-highlight-column",
        start: function (event, ui) {
            clearInterval(scrollAnimationFrame);
            scrollDirection = 0;
        },
        sort: function (event, ui) {
            let mouseX = event.pageX;
            let kanbanOffset = $kanbanBoard.offset();
            let kanbanWidth = $kanbanBoard.width();

            if (mouseX < kanbanOffset.left + 100) {
                if (scrollDirection !== -1) {
                    scrollDirection = -1;
                    scrollSpeed = 5;
                    smoothScroll();
                }
            } else if (mouseX > kanbanOffset.left + kanbanWidth - 100) {
                if (scrollDirection !== 1) {
                    scrollDirection = 1;
                    scrollSpeed = 5;
                    smoothScroll();
                }
            } else {
                scrollDirection = 0;
            }
        },

        stop: function (event, ui) {
            scrollDirection = 0;
            clearInterval(scrollAnimationFrame);

            let columnOrder = [];
            $(".kanban-column").each(function () {
                columnOrder.push($(this).attr("id"));
            });
            $.ajax({
                url: t.config.url.updateColumnOrder,
                type: "POST",
                data: {
                    column_order: columnOrder,
                    _token: t.config.token,
                },
            });
        }
    }).disableSelection();

    t.initializeSortable = function () {
        $(".sortable").sortable({
            connectWith: ".sortable",
            placeholder: "ui-state-highlight",
            start: function (event, ui) {
                $('[data-toggle="popover"], [data-bs-toggle="popover"]').popover('hide');
                $('[data-toggle="tooltip"], [data-bs-toggle="tooltip"]').tooltip('hide');
                $('.popover, .tooltip').remove();
                ui.placeholder.height(ui.item.height());
                ui.placeholder.width(ui.item.width());
                let oldStatus = ui.item.parent().data("status");
                ui.item.data("statusold", oldStatus);
                let clone = ui.item.clone();
                clone.css({
                    "opacity": "0.5",
                    "box-shadow": "0px 4px 10px rgba(0, 0, 0, 0.3)",
                    "border": "2px dashed rgb(255, 17, 0)"
                });
                ui.placeholder.html(clone);
                clearInterval(scrollAnimationFrame);
                scrollDirection = 0;
            },
            sort: function (event, ui) {
                $('.popover, .tooltip').remove();
                let mouseX = event.pageX;
                let kanbanOffset = $kanbanBoard.offset();
                let kanbanWidth = $kanbanBoard.width();

                if (mouseX < kanbanOffset.left + 100) { // Scroll Left
                    if (scrollDirection !== -1) {
                        scrollDirection = -1;
                        scrollSpeed = 5;
                        smoothScroll();
                    }
                } else if (mouseX > kanbanOffset.left + kanbanWidth - 100) { // Scroll Right
                    if (scrollDirection !== 1) {
                        scrollDirection = 1;
                        scrollSpeed = 5;
                        smoothScroll();
                    }
                } else {
                    scrollDirection = 0;
                }
            },
            stop: function (event, ui) {
                $('.popover').remove();
                $('[data-toggle="popover"]').popover('hide');
                var commentText = '';
                var ticketId = ui.item.data('ticket-id');
                var newStatus = ui.item.parent().data('status');
                var tat = ui.item.data('tat-id');
                var priority = ui.item.data('priority-id');
                var assigned_to = ui.item.data('assigned-id');
                var authuser = ui.item.data('user-id');
                var comment = ui.item.data('comment-id');
                var cc_emails = ui.item.data('cc_emails');
                var department = ui.item.data('department-id');
                localStorage.setItem('cardMove', 1);
                scrollDirection = 0;
                t.config.oldColumn = $('[data-status="' + ui.item.data("statusold") + '"]');
                t.config.newColumn = $('[data-status="' + ui.item.parent().data("status") + '"]');
                if (t.config.oldColumn.children().length === 0) {
                    t.config.oldColumn.empty().addClass("empty");

                }

                if (t.config.newColumn.children().length > 1) {
                    t.config.newColumn.find(".no_data").remove();
                }
                if (ui.item.parent().data('item_type') == 1) {
                    if (authuser != assigned_to) {
                        Swal.fire({
                            title: "Fail",
                            text: "You cannot move this card because it is not assigned to you",
                            icon: "warning"
                        });
                        return false;
                    }
                    if (ui.item.data("statusold") == 5) {
                        if (newStatus != 2) {
                            Swal.fire({
                                title: "Fail",
                                text: "Resolved tickets can only be reopened.",
                                icon: "warning"
                            });
                            return false;
                        }
                    }
                    if (newStatus == 6) {
                        Swal.fire({
                            title: "Fail",
                            text: "You cannot close this ticket directly.",
                            icon: "warning"
                        });
                        return false;
                    }
                    if (comment != 1) {
                        $('#comment').val('').summernote('code', '');
                        $('#ticket_id_card').val(ticketId);
                        $('#ticket_status_card').val(newStatus);
                        $('#priority_id_card').val(priority);
                        $('#statusKanbanMdl #tat').val(tat);
                        $('#cc_emails').val(cc_emails);
                        $('#attachment_updates').empty();
                        $('#statusKanbanMdl').modal('show');
                        t.loadRequestForm_onStatus(newStatus, ticketId);
                        localStorage.setItem('cardMove', 0);
                        return false;
                    }
                    // Send AJAX request to update the task's status
                    if (comment == 1) {
                        t.loadRequestForm_onStatus(newStatus, ticketId);
                        commentText = "status change from " + ui.item.data('statusoldname') + " to " + ui.item.parent().data('statusname') + " by " + ui.item.data('username') + ' via kanban board';
                        $.ajax({
                            url: t.config.url.update_status,
                            method: 'POST',
                            data: {
                                status_id: newStatus,
                                tat: tat,
                                follow_cc: 0,
                                add_back_trail: 0,
                                cc_emails: 0,
                                tmp_id: Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6),
                                id: ticketId,
                                priority_id: priority,
                                comment: commentText,
                                assigned_to: assigned_to,
                                authUser: authuser,
                                ticket_status_form_id: $("#ticket_status_form_id").val(),
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                board_id: $("#board_id").val(),
                                kanbanBoard: 'true',
                            },
                            success: function (response) {
                                if (response.status != "success") {
                                    sweetAlert('center', 'error', response);
                                } else {
                                    t.updateBoardData(t.config.oldColumn, t.config.newColumn);
                                }
                            },
                            error: function () {
                                alert('Failed to update task status');
                            }
                        });
                    }
                    ui.item.data('statusoldname', ui.item.parent().data('statusname'));
                } else if (ui.item.parent().data('item_type') == 2) {
                    var statusDataStr = ui.item.parent().attr('data-statusdata');
                    var statusData = JSON.parse(statusDataStr);
                    var itemId = statusData.id;
                    var grouped_action = ui.item.parent().data('grouped') != undefined ? ui.item.parent().data('grouped') : false;
                    var groupedbydata = ui.item.parent().data('groupedbydata') != undefined ? ui.item.parent().data('groupedbydata') : {};
                    var itemData = {
                        itemId: itemId,
                        itemName: newStatus,
                        tmp_id: Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6),
                        id: ui.item.data('card-id'),
                        board_id: $("#board_id").val(),
                        grouped: grouped_action,
                        groupedBy: groupedbydata,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    };
                    if (itemData['groupedBy']['groupBy'] == 1) {
                        if (ui.item.data("statusold") == 'Resolved' && itemId != 2) {
                            Swal.fire({
                                title: "Fail",
                                text: 'Resolved cards can only change to reopen status.',
                                icon: "warning"
                            })
                            return false;
                        }

                        if (itemData['groupedBy']['id'] == 6) {
                            Swal.fire({
                                title: "Fail",
                                text: "You cannot close this card directly.",
                                icon: "warning"
                            });
                            return false;
                        }

                        if (ui.item.data("statusold") == 'Closed') {
                            Swal.fire({
                                title: "Fail",
                                text: "You cannot move closed cards.",
                                icon: "warning"
                            });
                            return false;
                        }

                        if (ui.item.data("statusold") != 'Resolved' && itemData['groupedBy']['id'] == 2) {
                            Swal.fire({
                                title: "Fail",
                                text: "Cannot move this card to reopen status.",
                                icon: "warning"
                            });
                            return false;
                        }
                    }

                    $.ajax({
                        url: t.config.url.board_item_ticket_update,
                        method: 'POST',
                        data: itemData,
                        success: function (response) {
                            if (response.status != "success") {
                                sweetAlert('center', 'error', response)
                                t.updateBoardData(t.config.oldColumn, t.config.newColumn);
                            } else {
                                t.updateBoardData(t.config.oldColumn, t.config.newColumn);

                            }
                        },
                        error: function () {
                            alert('Failed to update item');
                        }
                    });

                }
                $(".sortable").each(function () {
                    if ($(this).children().length === 0) {
                        $(this).addClass("empty");
                    } else {
                        $(this).removeClass("empty");
                    }
                });
            }
        }).disableSelection();
    }

    t.updateBoardData = function ($oldColumn, $newColumn) {
        var isgrouped = $oldColumn?.data("grouped") ?? false;
        var oldColumnStatus = $oldColumn?.data("statusdata");
        var newColumnStatus = $newColumn?.data("statusdata");

        if (isgrouped) {
            oldColumnStatus = $oldColumn?.data("groupedbydata");
            newColumnStatus = $newColumn?.data("groupedbydata");
        }
        if (oldColumnStatus !== null) {
            t.loadBoardData(oldColumnStatus, isgrouped);
        }
        if (newColumnStatus !== null) {
            t.loadBoardData(newColumnStatus, isgrouped);
        }
    };

    $(document).on('mouseenter', '.mainDiv', function () {
        var scrolledDiv = $(this).attr('id');
        var $scrolledDiv = $('[id="' + scrolledDiv + '"]');
        if (!$scrolledDiv.data('scroll-bound')) {
            $scrolledDiv.on('scroll', handleScroll);
            $scrolledDiv.data('scroll-bound', true);
        }
    });

    function handleScroll(event) {
        const container = event.target;
        const $container = $(container);
        const $kanbanColumn = $container.closest('.kanban-column');
        const totalCount = $kanbanColumn.find('.kanban-count').data('total-count');
        const totalCards = $container.find('.card').length;
        if (totalCards >= totalCount) {
            return;
        }
        if (Math.abs(container.scrollHeight - container.scrollTop - container.clientHeight) <= 2) {
            const itemType = $container.data('item_type');
            const itemName = $container.data('status');
            var page = $container.attr('data-page');
            var grouped = $container.attr('data-grouped') != undefined ? $container.attr('data-grouped') : false;
            if (grouped) {
                var groupedbydata = $container.attr('data-groupedbydata') != undefined ? $container.attr('data-groupedbydata') : {};
                var status = JSON.parse(groupedbydata);
                var groupBy = status.groupBy;
                var url = t.config.url.getGroupedBoardData;
            } else {
                var url = itemType == 1 ? t.config.url.loadCards : t.config.url.loadCustomCards;
            }
            $kanbanColumn.find('.specific_loadingContainer').show();

            page++;
            $container.attr('data-page', page);
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    boardId: $('#board_id').val(),
                    itemName: itemName,
                    filters: t.cache_filter_values(),
                    page: page,
                    grouped: grouped,
                    order: t.config.sort_dir,
                    status: status ?? '',
                    groupBy: groupBy ?? '',
                    groupby_date: t.config.groupby_date ?? '',
                    archived: t.config.archived
                },
                success: function (data) {
                    if (grouped) {
                        t.loadBoardCards(data.results, $container, status, data.action_controls);
                    } else {
                        const $newContent = $(`<div class="content">${data.data}</div>`);
                        const cardsToAdd = totalCount - totalCards;
                        if (cardsToAdd > 0) {
                            const $cards = $newContent.find('.card');
                            const $cardsToAppend = $cards.slice(0, cardsToAdd);
                            $container.append($cardsToAppend);

                            $container.append($cardsToAppend);
                            $cardsToAppend.find('[data-bs-toggle="tooltip"]').each(function () {
                                bootstrap.Tooltip.getOrCreateInstance(this, {
                                    boundary: document.body,
                                    offset: [0, 10],
                                    container: 'body',
                                });
                            });
                        }
                    }
                    $kanbanColumn.find('.specific_loadingContainer').hide();
                    updateCount($container);
                }
            });
        }
    }

    function updateCount($container) {
        const totalCards = $container.find('.card').length;
        const $kanbanColumn = $container.closest('.kanban-column');
        const totalCount = $kanbanColumn.find('.kanban-count').attr('data-total-count');
        $kanbanColumn.find('.kanban-count').text(`(${totalCards}/${totalCount})`);
    }

    t.loadMemberImage = function (boardID) {
        var https = $.ajax({
            url: t.config.url.loadMemberImage,
            type: "POST",
            data: { id: boardID },
        });
        https.done(function (data) {
            var img = '';
            var count = 0;
            t.allMember = 0;
            t.allMember = data.data;
            t.show_members.empty();
            $.each(data.data, function (i, v) {
                if (i < 5) {
                    if (v.avatar == null) {
                    img += `<div class="userInitials member-item" data-toggle="tooltip" data-container="body" data-title="${v.username}">${v.initial}</div>`;
                    } else {
                    img += `<img class="userAvatar member-item" src="${v.image}" data-bs-toggle="tooltip" data-container="body" title="${v.username}">`;
                    }
                } else {
                    count++;
                }
            });
            var image = img + " " +
                (count != 0
                    ? `<div id="countMember" class="member-item"> 
                            <a href="javascript:void(0)" class="count-link">+${count}</a>
                    </div>`
                    : ''
                );
            t.show_members.html(image);
        });
    }
    t.resetFilterCount = function () {
        t.creator_id.val('null').trigger('change');
        t.assigned_to.val('null').trigger('change');
        t.priority.val('null').trigger('change');
        t.status.val('null').trigger('change');
        t.department.val('null').trigger('change');
        t.prob_category.val('null').trigger('change');
        t.sub_category.val('null').trigger('change');
        t.date.val('null').trigger('change');
        t.dateRange.val('').trigger('change');
        t.customField.val('').trigger('change');
        t.customFieldValue.val('').trigger('change');
        t.config.other_filters = {};
        resetFilterCount();
        resetDateRangeFilter();
        $('#advanceFilterModal').modal("hide");
        clearTimeout(t.loadTimeout);
        t.loadTimeout = setTimeout(() => {
            t.load();
        }, 300);
    }
    t.cache_filter_values = function () {
        t.config.other_filters = {};
        if (t.creator_id.val() != '' && t.creator_id.val() != null) {
            t.config.other_filters.creator_id = t.creator_id.val();
        }
        if (t.priority.val() != '' && t.priority.val() != null) {
            t.config.other_filters.priority = t.priority.val();
        }
        if (t.status.val() != '' && t.status.val() != null) {
            t.config.other_filters.status = t.status.val();
        }
        if (t.assigned_to.val() != '' && t.assigned_to.val() != null) {
            t.config.other_filters.assigned_to = t.assigned_to.val();
        }
        if (t.department.val() != '' && t.department.val() != null) {
            t.config.other_filters.department_id = t.department.val();
        }
        if (t.prob_category.val() != '' && t.prob_category.val() != null) {
            t.config.other_filters.problem_category_id = t.prob_category.val();
        }
        if (t.sub_category.val() != null && t.sub_category.val() != '') {
            t.config.other_filters.sub_category_id = t.sub_category.val();
        }
        if (t.date.val() != null && t.date.val() != 'null') {
            t.config.other_filters.filter_by_date = t.date.val();
        }
        if (t.date.val() != null && t.date.val() != 'null') {
            t.config.other_filters.dateRange = t.dateRange.val();
        }
        if (t.customField.val() != null && t.customField.val() != 'null') {
            t.config.other_filters.filter_by_custom_field = t.customField.val();
        }
        if (t.customField.val() != null && t.customField.val() != 'null') {
            t.config.other_filters.filter_by_custom_field_value = t.customFieldValue.val();
        }

        var jobj = {
            other_filters: t.config.other_filters,
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.date.val(), false);
        $('#advanceFilterModal').modal("hide");
        return t.config.export_filters;
    };

    t.renderSortDropdown = function () {
        var $dropdown =t.shortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            $dropdown.append(`
                <li>
                    <a href="javascript:void(0)"
                    class="dropdown-item ${isActive ? 'active' : ''}"
                    data-id="${d.id}"
                    data-bs-toggle="tooltip"
                    data-bs-placement="left"
                    data-bs-original-title="${d.text}">
                        <span class="like-radio"></span>
                        <span class="flex-grow-1">${d.text}</span>
                        ${isActive
                            ? `<i class="bi ${s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down'}"></i>`
                            : ''
                        }
                    </a>
                </li>
            `);
        });
        $dropdown.find('[data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this, {
                container: 'body'
            });
        });
    };

    /* Sort Direction */
    t.sortAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.updateSortDirectionIcon();

        t.renderSortDropdown();
        t.load();
    });

    /* Open Dropdown */
    t.dropdownAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.shortItems.toggleClass("show");
    });

    t.updateSortDirectionIcon = function () {
        $("#sortDirectionIcon").removeClass("bi-sort-up bi-sort-down").addClass(t.config.sort_dir.dir == 1? "bi-sort-up": "bi-sort-down");
    };

    /* Select Sort Field */
    t.shortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.config.sort_dir.id = $(this).data("id");
        t.shortItems.removeClass("show");
        t.renderSortDropdown();
        t.load();
    });

    /* Keep dropdown open when clicking inside */
    t.shortItems.on("click", function (e) {
        e.stopPropagation();
    });

    /* Close outside click */
    $(document).on("click", function () {
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.shortItems.removeClass("show");
    });
    t.renderSortDropdown();
    t.renderGroupby = function () {
        var grf = t.groupbybtns.find(".groupby-fields");
        grf.empty();
        $.each(t.config.group_fields, function (i, d) {
            $('<li class="group-by-item" data-id="' + d.id + '">' + '<span class="like-radio"></span>' + d.text + '<span class="clear-group" style="float:right"><i class="bi bi-x"></i></span></li>').appendTo(grf);
        });
    }

    $(document).off("click", "#columnVisibilityButton").on("click", "#columnVisibilityButton", function (e) {
        e.stopPropagation();

        let statusNames = new Map();
        $(".kanban-column").each(function () {
            let $kanbanColumn = $(this);
            let statusName = $kanbanColumn.find(".kanban-title").attr("data-title") ||
                $kanbanColumn.find(".mainDiv").data("statusname") ||
                $kanbanColumn.find(".kanban-title").text().trim();

            if (statusName) {
                statusNames.set(statusName, $kanbanColumn.is(":visible"));
            }
        });

        let dropdownMenu = $(".column-fields");
        dropdownMenu.empty();

        let index = 0;
        statusNames.forEach((isVisible, statusName) => {
            let checked = isVisible ? "checked" : "";
            let listItem = `
                <li class="list-group-item">
                    <input type="checkbox" class="status-checkbox" id="status-${index}" value="${statusName}" ${checked}>
                    <label for="status-${index}"> ${statusName} </label>
                </li>
            `;
            dropdownMenu.append(listItem);
            index++;
        });

        dropdownMenu.toggleClass("show");
    });

    $(document).off("change", ".status-checkbox").on("change", ".status-checkbox", function () {
        t.config.selectedStatuses = $(".status-checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        let allVisible = true;
        $(".kanban-column").each(function () {
            let $kanbanColumn = $(this);
            let statusName = $kanbanColumn.find(".kanban-title").attr("data-title") ||
                $kanbanColumn.find(".mainDiv").data("statusname") ||
                $kanbanColumn.find(".kanban-title").text().trim();

            if (t.config.selectedStatuses.includes(statusName)) {
                $kanbanColumn.show();
            } else {
                allVisible = false;
                $kanbanColumn.hide();
            }
        });
        if (!allVisible) {
            $('.btn-visible-content #col-vis-act').removeClass('d-none');
            $('.btn-visible-content #col-vis-unact').addClass('d-none');
        } else {
            $('.btn-visible-content #col-vis-act').addClass('d-none');
            $('.btn-visible-content #col-vis-unact').removeClass('d-none');
        }
    });

    $(document).on("click", function (event) {
        if (!$(event.target).closest("#columnVisibilityButton, .column-fields").length) {
            $(".column-fields").removeClass("show");
        }
    });

    t.page.off('click', '#advanced_filterClrFilter').on('click', '#advanced_filterClrFilter', $.proxy(t.resetFilterCount));
    t.page.off("click", ".btn-getarchived").on("click", ".btn-getarchived", function (e) {
        e.preventDefault();

        if (t.config.archived === true) {
            t.config.archived = false;
            $('.btn-getarchived #archive-unact').removeClass('d-none');
            $('.btn-getarchived #archive-act').addClass('d-none');
        } else {
            t.config.archived = true;
            $('.btn-getarchived #archive-unact').addClass('d-none');
            $('.btn-getarchived #archive-act').removeClass('d-none');
        }
      t.renderSortDropdown();
        t.load();
    });

    t.page.off("click", ".btn-markarchived").on("click", ".btn-markarchived", function (e) {
        e.preventDefault();
        var archived_board_id = t.boardId.val();
        if (t.config.user.id != t.config.board_owner) {
            var data = {
                msg: "You are not the owner of this board.",
            };
            sweetAlert('center', 'warning', data);
            return true;
        }
        $('#api_loader').removeClass('d-none');
        $.ajax({
            url: t.config.url.archived_card + '/' + archived_board_id,
            type: "POST",
            data: {
                _token: t.config.token
            },
            success: function (response) {
                $('#api_loader').addClass('d-none');
                if (response.status == 'success') {
                    sweetAlert('center', 'success', response);
                    t.renderSortDropdown();
                    t.load();
                } else {
                    alert("Something went wrong.");
                }
            }
        });
    });

    t.page.off("click", "li.group-by-item").on("click", "li.group-by-item", function (e) {
        e.preventDefault();
        t.config.groupby_date = {};
        var groupBy_id = $(this).data('id');

        // $('.group_by svg').removeClass('board-icon-active');
        if (![4, 5, 6].includes(groupBy_id)) {
            $('.group-by-item .like-radio').removeClass('active_radio');
            $('.group_by #groupby-act').addClass('d-none');
            $('.group_by #groupby-unact').removeClass('d-none');
        }

        if ([4, 5, 6].includes(groupBy_id)) {
            t.config.tempSelectedLi = $(this);
            t.groupbyMdl.frmEl.groupBy_id.val(groupBy_id);
            t.groupbyMdl.frmEl.groupby_date.val('1').trigger('change');
            t.groupbyMdl.modal('show');
        } else {
            $(this).children('.like-radio').addClass('active_radio');
            $('.group_by #groupby-act').removeClass('d-none');
            $('.group_by #groupby-unact').addClass('d-none');
            t.config.groupBy = groupBy_id;
            t.config.selectedStatuses = [];
            t.renderSortDropdown();
            t.load();
        }
    });
    t.page.off("click", ".clear-group").on("click", ".clear-group", function (e) {
        e.stopPropagation();
        $('.group-by-item .like-radio').removeClass('active_radio');
        $('.group_by #groupby-act').addClass('d-none');
        $('.group_by #groupby-unact').removeClass('d-none');
        $(this).closest('.group-by-item').find('.like-radio').removeClass('active_radio');
        t.config.groupBy = {};
        t.config.groupby_date = {};
        t.renderSortDropdown();
        t.load();
    });
    t.renderSortDropdown();
    t.renderGroupby();

    var dTable = t.table.DataTable({
        bDestroy: true,
        processing: true,
        serverSide: false,
        initComplete: function () {
            let customFilter = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="myTable_length_wrapper"></div>
                       <div class="input-group table-search-btns searchbox_cover" style="max-width: 300px; border: 1px solid #ccc; border-radius: 10px;">
                            <span class="input-group-text border-0 bg-transparent ps-1 pe-0" style="color:#aaa;">
                                <i class="bi bi-search" style="font-size: 15px;"></i>
                            </span>
                            <input type="text"
                                class="form-control border-0 shadow-none searchbox member-search"
                                placeholder="Search..."
                                style="border-radius: 0; background: transparent; padding: 4px 6px;">
                        </div>
                    </div>
                `;
            $("#myTable_wrapper .dataTables_filter").hide();
            $("#myTable_wrapper").prepend(customFilter);
            $("#myTable_wrapper select").select2({
                dropdownParent: $("#myTable_wrapper")
            });
            $("#myTable_length").appendTo("#myTable_length_wrapper");
            $("#myTable_filter").remove();

            $("#myTable_wrapper .member-search").on("keyup", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    dTable.search(this.value).draw();
                }
            });
        }
    });
   

    isTableLoaded = false;
    t.page.off('click', '#countMember').on('click', '#countMember', function () {
        if (!isTableLoaded) {
            t.loadTable();
            isTableLoaded = true;
        }
        t.memberList.modal('show');
    });
    t.loadTable = function () {
        $.ajax({
            url: t.config.url.loadMemberImage,
            type: "POST",
            data: { id: t.boardId.val() },
            success: function (data) {
                var members = data.data;
                dTable.clear().draw();
                members.forEach(function (item) {
                    let removeLink = '';

                    if (jQuery.inArray("KanbanBoardEdit", t.config.permissions) !== -1) {
                        if (item.access_type != 2) {
                            removeLink = `<a href="javascript:void(0)" class="removeMember" data-user-id="${item.id}"><i class="bi bi-trash"></i></a>`;
                        }
                    }

                    dTable.row.add([
                        `<div class="assigned-users"><img src="${item.image}"></div>`,
                        item.username,
                        item.first_name + ' ' + item.last_name,
                        removeLink,
                    ]).draw();
                });
            }
        });
    }
    t.page.off('click', '.removeMember').on('click', '.removeMember', function () {
        var user_id = $(this).data('user-id');
        sweetAlertConfirmation({
            message: t.config.translations.are_you_delete,
            onConfirm: function () {
                $('#api_loader').removeClass('d-none');
                $.ajax({
                    type: "POST",
                    url: t.config.url.removeBoardMember,
                    data: { board_id: t.boardId.val(), user_id: user_id },
                    success: function (data) {
                        $('#api_loader').addClass('d-none');
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.loadTable();
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    }

                });

            }
        });


    });
}

var cardMoveToChangeStatus = function (config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');

    t.page = $('#kanban-boards');

    t.mdl = t.page.find("#statusKanbanMdl");
    t.frmUpdateStatus = t.mdl.find("#frm_update_status_card");
    t.frmUpdateStatus.cr = {};
    t.frmUpdateStatus.cr.id = t.frmUpdateStatus.find("#id");
    t.frmUpdateStatus.cr.comment = t.frmUpdateStatus.find("#comment");
    t.frmUpdateStatus.cr.tmp_id = t.frmUpdateStatus.find("#tmp_id");
    t.frmUpdateStatus.ticketID = t.frmUpdateStatus.find('#ticket_id_card');
    t.frmUpdateStatus.status = t.frmUpdateStatus.find('#ticket_status_card');
    t.frmUpdateStatus.priority = t.frmUpdateStatus.find('#priority_id_card');
    t.frmUpdateStatus.tat = t.frmUpdateStatus.find('#tat');
    t.frmUpdateStatus.follow_cc = t.frmUpdateStatus.find("#follow_cc");
    t.frmUpdateStatus.add_back_trail = t.frmUpdateStatus.find('#add_back_trail');
    t.frmUpdateStatus.cc_emails = t.frmUpdateStatus.find('#cc_emails');
    t.attachment_update = t.frmUpdateStatus.find("#attachment_updates");
    t.frmUpdateStatus.btnSubmit = t.frmUpdateStatus.find("#btnSubmit");
    var totalFileSize = 0;
    t.frmUpdateStatus.commentWrapper = t.frmUpdateStatus.find('.final-comment-wrapper');


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

    $.each(t.config.statuses, function (i, v) {
        t.frmUpdateStatus.status.append($('<option>').val(v.id).text(v.name).attr('form', v.status_form));
    });

    t.attachmentView = function (e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        if (type == 1) {
            var images = [];

            $(".tri-view").each(function () {
                images.push({
                    href: $(this).attr("data-view"),
                    title: $(this).attr("data-name")
                });
            });

            var clickedIndex = $(".tri-view").index(this);
            $.swipebox(images, {
                initialIndexOnArray: clickedIndex,
                hideCloseButtonOnMobile: false,
                removeBarsOnMobile: false
            });
        }
    };

    t.attachmentDownload = function (e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }

    t.frmUpdateStatus.cr.comment.summernote({
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
    t.cardTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.frmUpdateStatus.cr.tmp_id.val(v);
    };
    t.cardTokenize();
    $.validator.addMethod(
        "multipleemailaddress",
        function (value, element) {
            if (this.optional(element))
                return true;
            var emails = value.toString().split(/[;,]+/);
            $('#frm_comment').find('#shows_error_cc').html('');
            $('#frm_update_status_card').find('#shows_error_cc').html('');
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
    t.frmUpdateValidator = t.frmUpdateStatus.validate({
        onsubmit: false,
        rules: {
            cc_emails: {
                //accept:"[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}",
                multipleemailaddress: true
            },
        },
        messages: {
            cc_emails: {
                multipleemailaddress: "Please enter a valid E-mail address"
            }
        }
    });
    t.submitCardComment = function (e) {
        var selectedValue = t.frmUpdateStatus.status.val();
        var formAttr = t.frmUpdateStatus.status.find('option[value="' + selectedValue + '"]').attr('form');
        if (typeof formAttr !== 'undefined' && formAttr != 0 && $("#ticket_status_form_id").val() != 'not_found' && $("#ticket_status_form_id").val() == '') {
            t.loadRequestForm_onStatus();
            t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
        } else {
            if (t.frmUpdateValidator.form() == false) {
                return false;
            }
            let s = t.frmUpdateStatus.cr.comment.val();
            if (s == '') {
                $('#frm_update_status_card').find('#shows_error').html('This field is required.');
                $('#frm_update_status_card').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                return false;
            } else {
                $('#frm_update_status_card').find('#shows_error').html('');
            }
            let cc_email_check = t.frmUpdateStatus.cc_emails.val() ? t.frmUpdateStatus.cc_emails.val().toString() : "";
            $.ajax({
                url: t.config.url.update_status,
                method: 'POST',
                data: {
                    status_id: t.frmUpdateStatus.status.val(),
                    tat: t.frmUpdateStatus.tat.val(),
                    follow_cc: t.frmUpdateStatus.follow_cc.val(),
                    add_back_trail: t.frmUpdateStatus.add_back_trail.val(),
                    cc_emails: cc_email_check,
                    tmp_id: t.frmUpdateStatus.cr.tmp_id.val(),
                    id: t.frmUpdateStatus.ticketID.val(),
                    priority_id: t.frmUpdateStatus.priority.val(),
                    comment: t.frmUpdateStatus.cr.comment.val(),
                    ticket_status_form_id: $("#ticket_status_form_id").val(),
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    board_id: $("#board_id").val(),
                    kanbanBoard: 'true',
                },
                success: function (response) {
                    if (response.status != "success") {
                        Swal.fire({
                            title: "Fail",
                            text: response.msg,
                            icon: "warning"
                        });
                    }
                    $("#ticket_status_form_id").val("");
                    $("#edit_status_form").html("");
                    localStorage.setItem('cardMove', 0);
                    t.loadCardList = new loadCardList(config);
                    t.loadCardList.updateBoardData(t.config.oldColumn, t.config.newColumn);
                    t.mdl.modal('hide');
                    t.attachment_update.empty();
                    localStorage.setItem('cardMove', 1);
                },
                error: function () {
                    alert('Failed to update task status');
                }
            });
        }
    }
    t.updateCCField = function () {
        var cc_emails = typeof t.data != "undefined" && typeof t.data.cc_emails != "undefined" ? t.data.cc_emails : "";
        t.frmUpdateStatus.cc_emails.val(cc_emails);
    };


    t.loadRequestForm_onStatus = function () {
        var selectedValue = t.frmUpdateStatus.status.val();
        var formAttr = t.frmUpdateStatus.status.find('option[value="' + selectedValue + '"]').attr('form');
        if (formAttr != undefined && formAttr != 0) {
            var status_form = formAttr;
            var status_id = t.frmUpdateStatus.status.val();
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
                                    t.frmStatusServiceRequest.el.ticket_id.val(t.frmUpdateStatus.ticketID.val());
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
                        t.frmUpdateStatus.btnSubmit.click();
                        $("#ticket_status_form_id").val('not_found');
                    }
                }).catch(function (error) {
                    console.error('Error fetching form ID:', error);
                });
            }
        } else {
            $("#ticket_status_form_id").val('');
        }
    };
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

    t.getStatusFormIdViaTicketProblemCategory = function () {
        return new Promise(function (resolve, reject) {
            $.ajax({
                type: "GET",
                url: t.config.url.getFormByTicketProblemCategory,
                data: {
                    'ticket_id': t.frmUpdateStatus.ticketID.val(),
                    'status_id': t.frmUpdateStatus.status.val(),
                },
                success: function (response) {
                    resolve(response.form_id);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                }
            });
        });
    }
    t.frmUpdateStatus.cc_emails.select2({
        dropdownParent:t.frmUpdateStatus,
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
                    q: params.term,
                    page: params.page,
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

    t.page.on('click', '#btnSubmit', $.proxy(t.submitCardComment));
    localStorage.setItem('cardMove', 1);
    t.updateCCField();
}

var ViewCustomCard = function (config) {
    var t = this;
    t.config = config;
    t.data = config.data;
    t.page = $("#kanban-boards");
    t.content = $(".content");
    t.mdl_popup_loader = t.page.find("#mdl_popup_loader");
    t.tkt_content = t.page.find('#tkt-content');
    t.main_attachments = t.tkt_content.find('.main_attachments');
    t.timeline = t.page.find("#card_timeline");

    t.httpCall = true;
    t.toggle_tiny_view = t.page.find('#toggle_tiny_view');
    t.toggle_tiny_view_read = t.page.find('#toggle_tiny_view_read');

    //custom card 
    t.frmCustomComment = t.page.find("#frm_custom_comment");
    t.frmCustomComment.el = {};
    t.frmCustomComment.el.id = t.frmCustomComment.find("#id");
    t.frmCustomComment.el.tmp_id = t.frmCustomComment.find("#tmp_id");
    t.frmCustomComment.el.comment = t.frmCustomComment.find("#comment");
    t.attachment = t.frmCustomComment.find("#attachments");
    t.frmCustomComment.el.submitComment = t.frmCustomComment.find("#submitComment");
    t.frmCustomComment.el.comment.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']]
        ],
        minHeight: 130,
        focus: false
    });
    t.frmCustomCommentTokenize = function () {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
        t.frmCustomComment.el.tmp_id.val(v);
    };

    t.setSingleTinyViewer = function (el, target_val) {
        var card = $(el).closest('.card');
        var content = card.find('.tml-content');
        if (target_val == true) {
            card.css('height', '');
            card.css('height', 'auto');
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
        } else {
            card.css('height', '');
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-expand faa-fast animated"></i>').attr('title', 'Expand');
        }
    };


    t.setSingleTinyViewerRead = function (el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="fa fa-arrow-up"></i>').attr('title', 'Compress');
        } else {
            $(el).addClass('tiny-view-on-read').find('.single-tiny-viewer').html('<i class="fa fa-arrow-down"></i>').attr('title', 'Expand');
        }
    }

    t.toggleSingleTinyViewer = function (e) {
        e.preventDefault();
        var el = $(this).closest('.card').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };

    t.toggleTinyViewAll = function (e) {
        e.preventDefault();
        if (t.toggle_tiny_view.hasClass('tiny-view-on') == true) {
            t.toggle_tiny_view.removeClass('tiny-view-on').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
            t.page.find('.tiny-view').each(function (i, j) {
                t.setSingleTinyViewer(j, true);
            });
        }
        else {
            t.toggle_tiny_view.addClass('tiny-view-on').html('<i class="bi bi-arrows-angle-expand faa-fast animated"></i>').attr('title', 'Expand');
            t.page.find('.tiny-view').each(function (i, j) {
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

    t.setMainAttachments = function () {
        var at = [];
        var totalAttachments = t.config.main_attachments.length;

        $.each(t.config.main_attachments, function (i, v) {
            var sext = v.ext.toLowerCase();
            var file_icon = "";
            var eye_link = "";

            if (sext === "png" || sext === "jpeg" || sext === "jpg") {
                file_icon = '<i class="fa fa-file-image-o file-icon image-icon"></i>';
                eye_link =
                    '<span class="tri-view" data-view_mode="1" data-view="' +
                    t.config.url.card_attachment_view +
                    "/" +
                    v.id +
                    '" data-name="' +
                    decodeURIComponent(v.name) +
                    '" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></span>';
            } else if (sext === "pdf") {
                file_icon = '<i class="fa fa-file-pdf-o file-icon pdf-icon"></i>';
            } else if (sext === "xlsx" || sext === "xls") {
                file_icon = '<i class="fa fa-file-excel-o file-icon excel-icon"></i>';
            } else {
                file_icon = '<i class="fa fa-file-o file-icon"></i>';
            }

            var fileSize = v.size ? (v.size / (1024 * 1024)).toFixed(2) + " MB" : "Unknown size";
            if (v.size < 1024 * 1024) {
                fileSize = (v.size / 1024).toFixed(2) + " KB";
            }

            at.push(
                '<div class="attachment-item">' +
                '<div class="attachment-content">' +
                file_icon +
                '<div class="file-details">' +
                '<span class="file-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span>' +
                '<div class="file-info-actions">' +
                '<span class="file-size">' + fileSize + '</span>' +
                '<div class="attachment-actions">' +
                eye_link +
                '<span class="tri-download" data-url="' + t.config.url.card_attachment_download + "/" + v.id + '" data-toggle="tooltip" data-title="Download">' +
                '<i class="bi bi-download"></i></span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
            );

        });

        if (at.length > 0) {
            t.main_attachments.html(
                '<div class="attachment-header">' +
                '<i class="bi bi-paperclip"></i> Attachments (' + totalAttachments + ')</div>' +
                '<div class="attachment-container">' +
                at.join("") +
                "</div>"
            );
        }
    };
    t.refreshTimeLineCustomCard = function () {
        var formData = new FormData();
        formData.append('_token', t.config.token);
        formData.append('id', t.data.id);
        var http = $.ajax({
            url: t.config.url.get_timeline_custom_card,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    var exi_el_state = {};
                    t.timeline.find('.timeline-entry').each(function (exi_ind, exi_el) {
                        exi_el_state['e' + exi_el.id] = $(exi_el).hasClass('tiny-view-on');
                    });

                    t.timeline.empty();
                    data.data.sort(function (a, b) {
                        return new Date(b.updated_at_format) - new Date(a.updated_at_format);
                    });
                    var currentIndex = 0;
                    var recordsPerLoad = 3;
                    function loadRecords(startIndex) {
                        var recordsToDisplay = data.data.slice(startIndex, startIndex + recordsPerLoad);
                        if (recordsToDisplay.length > 0) {
                            var tiny_viewer_state = t.toggle_tiny_view.hasClass('tiny-view-on');
                            $.each(recordsToDisplay, function (i, v) {
                                if (v.updated_by) {
                                    class_name = "color-code-bar color-code-blue-text";
                                }
                                var t2 = "Commented By";
                                var profileImage = '<img src="' + v.profile_img + '" alt="Profile" class="rounded-circle" width="35" height="35">';
                                var t3 = '<p class="mar-no pad-btm txt1 force-br">' + profileImage + ' ' + ' <a href="#" class="btn-link text-main text-bold ' + class_name + '" style="font-size:medium;">' + v.commenter + '</a></p>';
                                var t5 = "";
                                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                    var at = [];
                                    var totalAttachments = v.attachments.length;
                                    $.each(v.attachments, function (i, v) {
                                        var sext = v.ext.toLowerCase();
                                        var file_icon = "";
                                        var eye_link = "";

                                        if (sext === "png" || sext === "jpeg" || sext === "jpg") {
                                            file_icon = '<i class="fa fa-file-image-o file-icon image-icon"></i>';
                                            eye_link =
                                                '<span class="tri-view" data-view_mode="1" data-view="' +
                                                ((v.from == 'ticket') ? t.config.url.tkt_attachment_view : t.config.url.card_attachment_view) +
                                                "/" +
                                                v.id +
                                                '" data-name="' +
                                                decodeURIComponent(v.name) +
                                                '" data-bs-toggle="tooltip" title="View"><i class="bi bi-eye"></i></span>';
                                        } else if (sext === "pdf") {
                                            file_icon = '<i class="fa fa-file-pdf-o file-icon pdf-icon"></i>';
                                        } else if (sext === "xlsx" || sext === "xls") {
                                            file_icon = '<i class="fa fa-file-excel-o file-icon excel-icon"></i>';
                                        } else {
                                            file_icon = '<i class="fa fa-file-o file-icon"></i>';
                                        }

                                        var fileSize = v.size ? (v.size / (1024 * 1024)).toFixed(2) + " MB" : "Unknown size";
                                        if (v.size < 1024 * 1024) {
                                            fileSize = (v.size / 1024).toFixed(2) + " KB";
                                        }

                                        at.push(
                                            '<div class="attachment-item">' +
                                            '<div class="attachment-content">' +
                                            file_icon +
                                            '<div class="file-details">' +
                                            '<span class="file-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span>' +
                                            '<div class="file-info-actions">' +
                                            '<span class="file-size">' + fileSize + '</span>' +
                                            '<div class="attachment-actions">' +
                                            eye_link +
                                            '<span class="tri-download" data-url="' + ((v.from == 'ticket') ? t.config.url.tkt_attachment_download : t.config.url.card_attachment_download) + "/" + v.id + '" data-toggle="tooltip" data-title="Download">' +
                                            '<i class="bi bi-download"></i></span>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>'
                                        );
                                    });
                                    if (at.length > 0) {
                                        t5 =
                                            '<div class="attachment-header">' +
                                            '<i class="bi bi-paperclip"></i> Attachments (' + totalAttachments + ')</div>' +
                                            '<div class="attachment-container">' +
                                            at.join("") +
                                            "</div>";
                                    }
                                }
                                var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                                var signle_tiny_viewer = tiny_viewer_state == true ? 'bi-arrows-angle-expand faa-fast animated' : 'bi-arrows-angle-contract';

                                if (typeof exi_el_state['e' + v.tfid] != 'undefined') {
                                    if (exi_el_state['e' + v.tfid] == true) {
                                        tiny_view_class = 'tiny-view-on';
                                        signle_tiny_viewer = 'bi-arrows-angle-expand faa-fast animated';
                                    }
                                    else {
                                        tiny_view_class = '';
                                        signle_tiny_viewer = 'bi-arrows-angle-contract';
                                    }
                                }
                                var t4 = '<div class="card-body"><button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="bi ' + signle_tiny_viewer + '"></i></button>'
                                    + t3
                                    + '<div class="tml-content">' + v.comment + '</div>'
                                    + t5
                                    + '</div>'
                                    + '<div class="card-footer">'
                                    + '<span style="font-size:14px; font-weight:550">' + v.updated_at_format + '</span>'
                                    + '</div>';

                                t.timeline.append('<div id="' + v.tfid + '" class="card timeline-entry tiny-view ' + tiny_view_class + '">' + t4 + '</div>');
                            });
                            t.timeline.removeClass("hide");
                        } else if (data.data.length == 0 && (data.cardStatus == 5 || data.cardStatus == 6)) {
                            t.timeline.append('<p>No comment available</p>');
                            t.timeline.removeClass("hide");
                        } else {
                            t.timeline.addClass("hide");
                        }
                    }
                    loadRecords(currentIndex);
                    currentIndex += recordsPerLoad;
                    $('.load-comment').on('click', '.load-more', function () {
                        if (currentIndex < data.data.length) {
                            loadRecords(currentIndex);
                            currentIndex += recordsPerLoad;
                            if (currentIndex >= data.data.length) {
                                $('.load-comment').addClass('hide');
                            }
                        } else {
                            $('.load-comment').addClass('hide');
                        }
                        t.timeline.css({
                            'overflow-y': 'scroll',
                            'max-height': '500px'
                        });
                    });
                    if (currentIndex < data.data.length) {
                        $('.load-comment').removeClass('hide');
                    }
                } else if (data.msg != "") {
                    sweetAlert('center', 'error', data);
                }
            }
        });
    };

    t.frmCustomCommentSubmit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        let s = t.frmCustomComment.el.comment.val();
        if (s == '') {
            $('#frm_custom_comment').find('#shows_error').html('This field is required.');
            $('#frm_custom_comment').find('#shows_error').css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
            return false;
        } else {
            $('#frm_custom_comment').find('#shows_error').html('');
        }

        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;

        var formData = new FormData(t.frmCustomComment[0]);
        var http = $.ajax({
            url: t.config.url.add_custom_comment,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.data = data.data;
                    t.refreshTimeLineCustomCard();
                    t.frmCustomCommentTokenize();
                    sweetAlert('center', 'success', data);
                    t.frmCustomComment.el.comment.val("").summernote('code', '');
                    $('#frm_custom_comment').find('#shows_error').html("");
                    var uploader = t.frmCustomComment.find('#attachment-dropper-cover-custom-comment')[0]?.AMGDragDropUploader;
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
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
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
            var images = [];

            $(".tri-view").each(function () {
                images.push({
                    href: $(this).attr("data-view"),
                    title: $(this).attr("data-name")
                });
            });

            var clickedIndex = $(".tri-view").index(this);
            $.swipebox(images, {
                initialIndexOnArray: clickedIndex,
                hideCloseButtonOnMobile: false,
                removeBarsOnMobile: false
            });
        }
    };

    t.attachmentDownload = function (e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }


    t.setMainAttachments();
    t.frmCustomCommentTokenize();
    t.refreshTimeLineCustomCard();
    t.frmCustomComment.el.submitComment.on("click", $.proxy(t.frmCustomCommentSubmit));
    t.toggle_tiny_view.on("click", $.proxy(t.toggleTinyViewAll));
    t.toggle_tiny_view_read.on("click", $.proxy(t.toggleTinyViewAllRead));
    t.page.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    if (window.AMGDragDrop) {
        window.AMGDragDrop.initAll(document);
    }
    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    t.main_attachments.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.main_attachments.on("click", ".tri-download", $.proxy(t.attachmentDownload));
}

var KanbanConfig = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.mdl = t.content.find('#settingsMdl');
    t.frm = t.mdl.find("#general_config");

    t.frmEl = {};
    t.frmEl.overdue_mail_before_hours = t.frm.find('#before_overdue');

    t.btn = {};
    t.btn.update = t.frm.find('#update');

    t.load = function () {
        if (t.config.overdue_mail_before_id && t.config.overdue_mail_before_id != 'null') {
            t.frmEl.overdue_mail_before_hours.val(t.config.overdue_mail_before_id);
        }
    }

    t.handlesubmit = function (e) {
        e.preventDefault();
        var frmData = new FormData(t.frm[0]);
        var frmData = new FormData;
        frmData.append('before_overdue', t.frmEl.overdue_mail_before_hours.val());
        frmData.append('old_before_overdue', t.config.overdue_mail_before_id);

        var http = $.ajax({
            url: t.config.url.update,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (data.status === "success") {
                sweetAlert('center', 'success', { msg: data.message });
                setTimeout(function () { location.reload(); }, 1000);
            } else {
                sweetAlert('center', 'error', { msg: data.message });
                setTimeout(function () { location.reload(); }, 1000);
            }
        });
    };
    t.load();
    t.btn.update.on("click", $.proxy(t.handlesubmit));
}