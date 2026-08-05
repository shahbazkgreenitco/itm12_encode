// TASK HISTORY – Simple function: TaskHistory.open(id, url, token)
var TaskHistory = (function($) {
    'use strict';
    var modal = $('#taskHistoryModal');
    var container = $('#task_history_container');
    var loader = $('.task-history-loader');
    var taskId = null;
    var apiUrl = null;
    var token = null;
    var currentPage = 1;
    var perPage = 10;
    var hasMore = false;
    var isLoading = false;
    var scrollBound = false;
    var scrollHandler = null;

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>"]/g, function(m) {
            var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' };
            return map[m] || m;
        });
    }

    function getAvatar(name, avatarUrl) {
        name = name || 'System';
        if (avatarUrl) {
            return '<img src="' + avatarUrl + '" alt="' + escapeHtml(name) + '" class="avatar-fallback">';
        }
        var initials = name.split(' ').map(function(w) { return w[0]; }).join('').toUpperCase().substring(0, 2);
        var colors = ['#F12F35', '#0e1d4d', '#12a78d', '#d9392f', '#2f5fd9', '#e08a1c', '#8e2f9c'];
        var colorIndex = name.length % colors.length;
        return '<span class="avatar-fallback" style="background:' + colors[colorIndex] + ';">' + initials + '</span>';
    }

    function taskHistoryChangeHtml(label, oldVal, newVal) {
        oldVal = oldVal ? escapeHtml(oldVal) : null;
        newVal = newVal ? escapeHtml(newVal) : null;
        if (!oldVal && !newVal) return label + ' updated';
        if (!oldVal) return label + ' set to <b>' + newVal + '</b>';
        if (!newVal) return label + ' removed from <b>' + oldVal + '</b>';
        return label + ' changed from <b>' + oldVal + '</b> to <b>' + newVal + '</b>';
    }

    function getTaskActionData(item) {
        var actionId = parseInt(item.action_id, 10);
        var toneClass = 'tone-task';
        var ribbonText = 'Update';
        var ribbonIcon = 'bi bi-pencil-square';
        var actionHtml = '';

        switch (actionId) {
            case 1:
                toneClass = 'tone-newticket';
                ribbonText = 'Created';
                ribbonIcon = 'bi bi-plus-circle';
                actionHtml = 'Task Created';
                break;
            case 2:
                toneClass = 'tone-company';
                ribbonText = 'Company';
                ribbonIcon = 'bi bi-building';
                actionHtml = taskHistoryChangeHtml('Company', item.old_company_name, item.company_name);
                break;
            case 3:
                toneClass = 'tone-task';
                ribbonText = 'Task';
                ribbonIcon = 'bi bi-pencil';
                if (item.old_name) {
                    actionHtml = taskHistoryChangeHtml('Task name', item.old_name, item.name);
                } else {
                    actionHtml = 'Task Deleted';
                    toneClass = 'tone-danger';
                    ribbonText = 'Deleted';
                    ribbonIcon = 'bi bi-trash';
                }
                break;
            case 4:
                toneClass = 'tone-comment';
                ribbonText = 'Comment';
                ribbonIcon = 'bi bi-chat-left-text';
                if (item.remarks) {
                    actionHtml = (item.is_note == 1) ? 'Marked as <b>Note</b> and commented on task' : 'Commented on task';
                    if (item.remarks) actionHtml += '<div class="mt-1">' + escapeHtml(item.remarks) + '</div>';
                } else {
                    actionHtml = taskHistoryChangeHtml('Related to', item.oldRelatedTo, item.relatedTo);
                }
                break;
            case 5:
                toneClass = 'tone-status';
                ribbonText = 'Status';
                ribbonIcon = 'bi bi-arrow-repeat';
                if (item.old_status_id) {
                    actionHtml = taskHistoryChangeHtml('Status', item.oldStatusName, item.statusName);
                } else {
                    actionHtml = 'Status changed to <b>' + escapeHtml(item.statusName) + '</b>';
                    if (item.remarks) actionHtml += '<div class="mt-1">' + item.remarks + '</div>';
                }
                var status = (item.statusName || '').toLowerCase().trim();
                if (status === 'completed' || status === 'resolved') {
                    toneClass = 'tone-resolved';
                    ribbonText = 'Completed';
                    ribbonIcon = 'bi bi-check2-square';
                } else if (status === 'in progress') {
                    toneClass = 'tone-open';
                    ribbonText = 'In Progress';
                    ribbonIcon = 'bi bi-arrow-repeat';
                } else if (status === 'hold' || status === 'on hold') {
                    toneClass = 'tone-hold';
                    ribbonText = 'On Hold';
                    ribbonIcon = 'bi bi-pause-circle';
                }
                break;
            case 6:
                toneClass = 'tone-priority';
                ribbonText = 'Priority';
                ribbonIcon = 'bi bi-flag';
                actionHtml = taskHistoryChangeHtml('Priority', item.oldPriorityName, item.priorityName);
                break;
            case 7:
                toneClass = 'tone-date';
                ribbonText = 'Start Date';
                ribbonIcon = 'bi bi-calendar-event';
                actionHtml = taskHistoryChangeHtml('Work start date', item.old_start_date_at, item.start_date_at);
                break;
            case 8:
                toneClass = 'tone-date';
                ribbonText = 'Due Date';
                ribbonIcon = 'bi bi-calendar-check';
                actionHtml = taskHistoryChangeHtml('Planned completion date', item.old_due_date_at, item.due_date_at);
                break;
            case 9:
                toneClass = 'tone-date';
                ribbonText = 'Completion';
                ribbonIcon = 'bi bi-calendar-check';
                actionHtml = taskHistoryChangeHtml('Actual completion date', item.old_end_date_at, item.end_date_at);
                break;
            case 10:
                toneClass = 'tone-cost';
                ribbonText = 'Cost';
                ribbonIcon = 'bi bi-currency-dollar';
                actionHtml = taskHistoryChangeHtml('Cost', item.old_cost, item.cost);
                break;
            case 11:
                toneClass = 'tone-assigned';
                ribbonText = 'Assigned';
                ribbonIcon = 'bi bi-person-check-fill';
                actionHtml = taskHistoryChangeHtml('Assigned to', item.oldAssignedTo, item.assignedTo);
                break;
            case 12:
                toneClass = 'tone-project';
                ribbonText = 'Project';
                ribbonIcon = 'bi bi-folder2-open';
                actionHtml = taskHistoryChangeHtml('Project', item.oldProjectName, item.projectName);
                break;
            case 13:
                toneClass = 'tone-change';
                ribbonText = 'Change';
                ribbonIcon = 'bi bi-arrow-left-right';
                actionHtml = taskHistoryChangeHtml('Change request', item.oldChangeName, item.changeName);
                break;
            case 14:
                toneClass = 'tone-description';
                ribbonText = 'Description';
                ribbonIcon = 'bi bi-file-text';
                actionHtml = 'Description updated';
                break;
            case 15:
                toneClass = 'tone-ticket';
                ribbonText = 'Ticket';
                ribbonIcon = 'bi bi-ticket-perforated';
                actionHtml = taskHistoryChangeHtml('Ticket', item.oldTicketId, item.ticketId);
                break;
            case 16:
                toneClass = 'tone-visibility';
                ribbonText = 'Visibility';
                ribbonIcon = 'bi bi-eye';
                var oldVis = item.old_is_visible_user == 1 ? 'Visible' : 'Hidden';
                var newVis = item.is_visible_user == 1 ? 'Visible' : 'Hidden';
                actionHtml = taskHistoryChangeHtml('User visibility', oldVis, newVis);
                break;
            case 17:
                toneClass = 'tone-department';
                ribbonText = 'Department';
                ribbonIcon = 'bi bi-building';
                actionHtml = taskHistoryChangeHtml('Department', item.old_dept_name, item.dept_name);
                break;
            case 18:
                toneClass = 'tone-category';
                ribbonText = 'Category';
                ribbonIcon = 'bi bi-folder2-open';
                actionHtml = taskHistoryChangeHtml('Problem category', item.old_pc_name, item.pc_name);
                break;
            case 19:
                toneClass = 'tone-subcategory';
                ribbonText = 'Sub Category';
                ribbonIcon = 'bi bi-diagram-3';
                actionHtml = taskHistoryChangeHtml('Problem subcategory', item.old_sc_name, item.sc_name);
                break;
            case 20:
                toneClass = 'tone-comment';
                ribbonText = 'Comment';
                ribbonIcon = 'bi bi-chat-left-text';
                actionHtml = (item.is_note == 1) ? 'Marked as <b>Note</b> and commented on task' : 'Commented on task';
                if (item.remarks) actionHtml += '<div class="mt-1">' + item.remarks + '</div>';
                break;
            case 21:
                toneClass = 'tone-danger';
                ribbonText = 'Deleted';
                ribbonIcon = 'bi bi-trash';
                actionHtml = 'Task Deleted';
                break;
            default:
                toneClass = 'tone-task';
                ribbonText = 'Updated';
                ribbonIcon = 'bi bi-pencil-square';
                actionHtml = 'Task Updated';
        }
        return { toneClass: toneClass, ribbonText: ribbonText, ribbonIcon: ribbonIcon, actionHtml: actionHtml };
    }

    function buildTimelineItem(item) {
        var commenterName = item.change_by_name || 'System';
        var avatarHtml = getAvatar(commenterName, null);
        var timestamp = item.last_updated_at || item.updated_at || '';
        var actionData = getTaskActionData(item);

        return `
            <div class="t-item ${actionData.toneClass}">
                <div class="t-actor">
                    ${avatarHtml}
                    <a href="javascript:void(0)">${escapeHtml(commenterName)}</a>
                </div>
                <div class="t-rail-col">
                    <span class="rail-cap start"></span>
                    <span class="rail-cap end"></span>
                </div>
                <div class="t-card">
                    <div class="t-body">
                        <div class="t-time"><i class="bi bi-clock"></i> ${escapeHtml(timestamp)}</div>
                        <div class="t-text">${actionData.actionHtml}</div>
                    </div>
                    <div class="t-ribbon">
                        <i class="${actionData.ribbonIcon}"></i>
                        ${actionData.ribbonText}
                    </div>
                </div>
            </div>
        `;
    }

    function populateHeader(item, taskId) {
        $('#task_history_details_subject').text(item.name || '').attr('data-original-title', item.name || '');
        $('#task_history_details_id').text('#' + taskId);
        if (item.dept_name) {
            $('#task_history_details_department').text(item.dept_name).show();
        } else {
            $('#task_history_details_department').hide();
        }
        if (item.ticket_id) {
            $('.task-history-related-device-row').show();
            $('#task_history_details_device').text('#' + item.ticket_id).show();
        } else {
            $('.task-history-related-device-row').hide();
            $('#task_history_details_device').hide();
        }
        if (item.pc_name) {
            $('.task_history_details_category-row').show();
            $('#task_history_details_category').text(item.pc_name).show();
        } else {
            $('.task_history_details_category-row').hide();
            $('#task_history_details_category').hide();
        }
        if (item.sc_name) {
            $('.task-history-subcategory-row').show();
            $('#task_history_details_subcategory').text(item.sc_name).show();
        } else {
            $('.task-history-subcategory-row').hide();
            $('#task_history_details_subcategory').hide();
        }
    }

    function loadPage(page, append) {
        if (isLoading) return;
        isLoading = true;

        var $bottomLoader = null;
        if (!append) {
            loader.removeClass('d-none');
        } else {
            $bottomLoader = $('<div class="text-center py-2 task-history-bottom-loader"><span class="spinner-border spinner-border-sm text-primary" role="status"></span> Loading more...</div>');
            container.append($bottomLoader);
        }

        $.ajax({
            url: apiUrl,
            type: 'POST',
            data: {
                _token: token,
                id: taskId,
                page: page,
                per_page: perPage
            }
        }).done(function(response) {
            var tasks = response.tasks || [];
            var taskDetails = response.task_details || {};
            currentPage = parseInt(response.current_page || page, 10);
            var lastPage = parseInt(response.last_page || currentPage, 10);

            if (!append) {
                populateHeader(taskDetails, taskId);
            }

            hasMore = (response.has_more !== undefined) ? !!response.has_more : (currentPage < lastPage);

            if (tasks.length === 0 && !append) {
                container.html('<div class="text-center p-4"><p>No history available</p></div>');
                return;
            }

            if ($bottomLoader) {
                $bottomLoader.remove();
                $bottomLoader = null;
            }

            var html = '';
            $.each(tasks, function(i, item) {
                html += buildTimelineItem(item);
            });
            if (append) {
                container.append(html);
            } else {
                container.html(html);
            }

            if (!hasMore) {
                unbindScroll();
            } else if (!append) {
                bindScroll();
            }

        }).fail(function(xhr) {
            var msg = xhr.status === 419 ? 'Session expired. Please refresh.' : 'Failed to load history';
            if (typeof sweetAlert === 'function') {
                sweetAlert('center', 'error', { msg: msg });
            } else {
                alert(msg);
            }
            if (!append) {
                container.html('<div class="text-center p-4"><p>Unable to load history.</p></div>');
            }
        }).always(function() {
            if ($bottomLoader) {
                $bottomLoader.remove();
            }
            if (!append) {
                loader.addClass('d-none');
            }
            isLoading = false;
        });
    }

    function bindScroll() {
        if (scrollBound) return;
        var handler = function() {
            if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 50) {
                if (!isLoading && hasMore) {
                    loadPage(currentPage + 1, true);
                }
            }
        };
        container.on('scroll.taskHistory', handler);
        scrollBound = true;
        scrollHandler = handler;
    }

    function unbindScroll() {
        container.off('scroll.taskHistory');
        scrollBound = false;
        scrollHandler = null;
    }

    modal.on('hidden.bs.modal', function() {
        unbindScroll();
        container.empty();
        taskId = null;
        isLoading = false;
    });

    return {
        open: function(id, url, csrf) {
            if (!id || !url) {
                console.error('TaskHistory: id and url are required.');
                return;
            }
            unbindScroll();
            container.empty().scrollTop(0);
            loader.removeClass('d-none');
            taskId = id;
            apiUrl = url;
            token = csrf || '';
            currentPage = 1;
            hasMore = false;
            isLoading = false;
            modal.modal('show');
            loadPage(1, false);
        }
    };
})(jQuery);