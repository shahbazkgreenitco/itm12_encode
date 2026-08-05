var KanbanMember = function (config) {

    var t = this;
    t.config = config;
    t.content = $("#all-kanban-member-wrapper");
    t.table = t.content.find("#mytable");  
    // t.searchbox = t.table.find(".searchbox");
    
    t.searchbtn = t.table.find(".btn-searchbox");

    t.mdl = $("#all-kanban-member-wrapper").find("#addKanbanMemberFormMdl");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");
    t.mdl.frm = t.mdl.find("#addKanbanMemberForm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.user_id_add = t.mdl.frm.find("#user_id_add");
    t.mdl.frmEl.user_id_edit = t.mdl.frm.find("#user_id_edit");
    t.mdl.frmEl.add_user = t.mdl.frm.find(".adduser");
    t.mdl.frmEl.edit_user = t.mdl.frm.find(".edituser");
    t.mdl.frmEl.access_type = t.mdl.frm.find("#access_type");

    t.mdlHistory = $("#all-kanban-member-wrapper").find("#mdl-history");
    t.mdlHistory.title = t.mdlHistory.find(".modal-title");
    t.result = t.mdlHistory.find("#result");

    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.filters = {
        wrapper: t.content.find("#advance-filters")
    };

    // ---- Helper: escape HTML (same as KanbanBoard) ----
    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    // ---- Icons (same as KanbanBoard) ----
    t.icons = {
        edit: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
               </svg>`,
        trash: `<svg viewBox="0 0 15 17" fill="none" >
                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.500 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                </svg>`,
        user: `<i class="bi bi-person-fill-add"></i>`,
        history: `<i class="bi bi-clock-history"></i>`
    };

    // ---- Helper: action button HTML (same as KanbanBoard) ----
    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        var safeLabel = t.escapeHtml(label);
        var safeId = t.escapeHtml(id);
        var safeClasses = t.escapeHtml(classes || "");
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

    // ---- Table Helpers ----
    t.tblHelpers = {
        actions: function() {
            return function(d) {
                // If owner (access_type == 2) – no actions (owner cannot be edited/deleted)
                if (d.access_type == 2) {
                    return '';
                }
                var a = [];
                if (jQuery.inArray("KanbanBoardEditMembers", t.config.permissions) !== -1) {
                    a.push(t.actionButtonHtml(config.translations.edit || "Edit", "edit-kanban-member", d.id, t.icons.edit));
                }
                if (jQuery.inArray("KanbanBoardDeleteMembers", t.config.permissions) !== -1) {
                    a.push(t.actionButtonHtml(config.translations.delete || "Delete", "delete-kanban-member", d.id, t.icons.trash));
                }
                if (a.length === 0) {
                    return '<span class="user-list-empty">-</span>';
                }
                return [
                    '<div class="user-list-actions role-list-actions justify-content-start">',
                    a.join(""),
                    '</div>'
                ].join("");
            };
        },

        userType: function() {
            return function(d) {
                var badge = [];
                if (d.access_type == 1) {
                    badge.push('<span class="label label-warning p-1 rounded" style="background-color: #fef3c7; color: #f59e0b;"><i class="bi bi-pencil-square"></i> Editor</span>');
                } else if (d.access_type == 2) {
                    badge.push('<span class="label label-danger p-1 rounded" style="background-color: #e0eee8; color: #178754;"><i class="bi bi-person-fill"></i> Owner</span>');
                } else if (d.access_type == 3) {
                    badge.push('<span class="label label-default p-1 rounded" style="background-color:#e2e3e5; color:#6c757d;"><i class="bi bi-eye-fill"></i> Viewer</span>');
                }
                return badge.join('');
            };
        }
    };

    // ---- DataTable ----
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [4],  
            },
            {
                targets: 4,
                render: t.tblHelpers.actions(),
            },
            {
                targets: 1,
                render: t.tblHelpers.userType(),
            },
        ],
        order: [[4, "desc"]],
        processing: true,
        serverSide: true,
        searching: false,     
        lengthChange: false,  
        ajax: {
            url: t.config.url.KanbanBoardMemberList,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.kanbanID = t.config.board.id;
                d.search = $('.searchbox').val();
            },
        },
        columns: [
            { data: "a.username" },
            { data: "a" },         
            { data: "a.created_at_format" },
            { data: "a.updated_at_format" },
            { data: "a" },          
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");
            $("#mytable_filter").remove();
            $("#mytable_length").remove();

            var $lengthSelect = $('.amg-table-pagination-dropdown');
            $lengthSelect.on('change', function() {
                api.page.len(parseInt($(this).val(), 10)).draw();
            });
            $lengthSelect.val(api.page.len());

            $(".amg-list-searchbar__input").on("keyup", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this);
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(v).draw();
                }
            });
        }
    });

    var select2Opts = { width: "100%" };
    function initUserSelect2($element, isMultiple = false, parentElement = null) {
        var dropdownParent = parentElement ? $(parentElement) : $element.parent();
        $element.select2($.extend({}, select2Opts, {
            dropdownParent: dropdownParent,
            ajax: {
                url: t.config.url.getUserByAjax,
                type: 'GET',
                dataType: "json",
                delay: 300,
                data: function(p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                        company_id: config.companyIds,
                        board_type: t.config.board.board_items_type ? t.config.board.board_items_type : null,
                        board_departments: t.config.board.department_id ? t.config.board.department_id : null,
                    };
                }
            },
            multiple: isMultiple,
            allowClear: true,
            placeholder: config.translations.select_user
        }));
    }

    initUserSelect2(t.mdl.frmEl.user_id_add, true, t.mdl);  
    initUserSelect2(t.mdl.frmEl.user_id_edit, false, t.mdl);       

    t.mdl.frmEl.access_type.select2({width:'100%', dropdownParent:t.mdl});

    // ---- Validation ----
    t.frmValidator = $('#addKanbanMemberForm').validate({
        onsubmit: false,
        ignore: ":hidden",
        rules: {
            access_type: { required: true },
        },
        errorPlacement: function(error, element) {
         error.insertAfter(element.parent());
        }
    });

    // ---- Save / Add / Edit / Delete / History methods ----
    // (these are mostly unchanged, but we keep them as in original)
    t.saveData = function (e) {
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.httpCall = false;
        t.mdl.btnSubmit.prop("disabled" , true);
        var frmData = new FormData(t.mdl.frm[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.mdl.frm.attr('action'),
            type: "POST",
            data: frmData,
            processData: false,
            contentType: false
        });
        http.done(function (data) {
            if (typeof data == "object") {
                t.mdl.btnSubmit.prop("disabled" , false);
                if (data.status == "success") {
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () { t.httpCall = true; });
        http.always(function () {
            t.httpCall = true;
            t.mdl.btnSubmit.prop("disabled" , false);
        });
    };

    t.editKanbanMember = function(e){
        e.preventDefault();
        t.mdl.frmEl.edit_user.show();
        t.mdl.frmEl.add_user.hide();
        var id = $(this).attr("data-id");
        var http = $.get(t.config.url.edit_kanabanMember + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {             
                    t.setValidationRules('edit');                   
                    t.mdl.frm.attr('action', t.config.url.update_kanbanMember + "/" + id);                    
                    t.mdl.title.text(t.config.translations.edit);
                    t.loadForm(data.data);
                    t.mdl.modal("show");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = { 'msg': config.translations.something_went_wrong };
            sweetAlert('center', 'error', data);
        });
        http.always(function () { t.httpCall = true; });
    };

    t.loadForm = function (obj) {
        t.mdl.frmEl.user_id_edit.empty();
        t.mdl.frmEl.user_id_edit.append(new Option(obj.user.first_name + " " + obj.user.last_name, obj.user.id, false, false));
        t.mdl.frmEl.access_type.val(obj.access_type).trigger("change");
    };

    t.resetForm = function(){
        t.frmValidator.resetForm();
        t.mdl.frm.trigger("reset");
        t.mdl.frmEl.user_id_add.val('').trigger("change");
        t.mdl.frmEl.user_id_edit.val('').trigger("change");
        t.mdl.frmEl.access_type.val('').trigger("change");
    };

    t.searchData = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = t.searchbox;
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.dTbl.ajax.reload();
        } else if (target.tagName == "BUTTON") {
            var v = t.searchbox;
            t.config.search = v;
            t.dTbl.ajax.reload();
        }
        else{
            t.dTbl.ajax.reload();
        }
    };

    t.setValidationRules = function (mode) {
        if (mode === 'add') {
            $('#user_id_add').rules('add', { required: true });
            $('#user_id_edit').rules('remove');
        } else if (mode === 'edit') {
            $('#user_id_edit').rules('add', { required: true });
            $('#user_id_add').rules('remove');
        }
    };

    t.addKanbanMember = function () {
        t.resetForm();
        t.setValidationRules('add');
        t.mdl.frmEl.edit_user.hide();
        t.mdl.frmEl.add_user.show();
        t.mdl.frm.attr('action', t.config.url.store_member);
        t.mdl.title.text(t.config.translations.add_kanban_member);
        t.mdl.frmEl.access_type.val('3').trigger('change');
        t.mdl.modal("show");
    };

    t.deleteKanbanBoardMember = function(e){
        e.preventDefault();
        var kanbanID = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete_kanbanMember + "/" + kanbanID;
        sweetAlertConfirmation({
            message: config.translations.are_you_delete,
            onConfirm: function () {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function () {
                    var data = { 'msg': config.translations.something_went_wrong };
                    sweetAlert('center', 'error', data);
                });
                http.always(function () { t.httpCall = true; });
            }
        });
    };

    t.toggleSingleTinyViewer = function(e) {
        e.preventDefault();
        var el = $(this).closest('.tiny-view').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };

    t.setSingleTinyViewer = function(el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-compress"></i>').attr('title', 'Compress');
        } else {
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-expand"></i>').attr('title', 'Expand');
        }
    };


    // ---- Helper: time ago text (same as board) ----
    t.getTimeAgoText = function(dateTimeStr) {
        if (!dateTimeStr) return 'Just<br>now';
        function parseCustomDate(str) {
            var parts = str.split(' ');
            if (parts.length < 5) return null;
            var day = parseInt(parts[0]);
            var monthNames = {
                'Jan':0,'Feb':1,'Mar':2,'Apr':3,'May':4,'Jun':5,
                'Jul':6,'Aug':7,'Sep':8,'Oct':9,'Nov':10,'Dec':11
            };
            var month = monthNames[parts[1]];
            if (month === undefined) return null;
            var year = parseInt(parts[2]);
            var timeParts = parts[3].split(':');
            var hour = parseInt(timeParts[0]);
            var minute = parseInt(timeParts[1]);
            var ampm = parts[4];
            if (ampm === 'PM' && hour !== 12) hour += 12;
            if (ampm === 'AM' && hour === 12) hour = 0;
            return new Date(year, month, day, hour, minute);
        }
        var date = parseCustomDate(dateTimeStr);
        if (!date || isNaN(date.getTime())) {
            date = new Date(dateTimeStr);
            if (isNaN(date.getTime())) return 'Just<br>now';
        }
        var now = new Date();
        var diffMs = now - date;
        var diffMins = Math.floor(diffMs / 60000);
        var diffHours = Math.floor(diffMins / 60);
        var diffDays = Math.floor(diffHours / 24);
        var diffWeeks = Math.floor(diffDays / 7);
        var diffMonths = Math.floor(diffDays / 30);
        var diffYears = Math.floor(diffDays / 365);
        if (diffMins < 1) return 'Just<br>now';
        if (diffMins < 60) return diffMins + ' min<br>ago';
        if (diffHours < 24) return diffHours + ' hr<br>ago';
        if (diffDays === 1) return '1 day<br>ago';
        if (diffDays < 7) return diffDays + ' days<br>ago';
        if (diffWeeks === 1) return '1 week<br>ago';
        if (diffWeeks < 4) return diffWeeks + ' weeks<br>ago';
        if (diffMonths === 1) return '1 month<br>ago';
        if (diffMonths < 12) return diffMonths + ' months<br>ago';
        if (diffYears === 1) return '1 year<br>ago';
        return diffYears + ' years<br>ago';
    };

    // ---- Helper: get avatar (same as board) ----
    t.getAvatar = function(commenter, avatar) {
        if (commenter === "System" || commenter === "system") {
            return '<img style="width:18px;height:18px;border-radius:50%;object-fit:cover;" src="' + (t.config.url.base || '') + '/assets/mati.png" alt="System">';
        }
        if (avatar && avatar !== '' && avatar !== null) {
            return '<img style="width:18px;height:18px;border-radius:50%;object-fit:cover;" src="' + (t.config.url.base || '') + '/storage/avatar/' + avatar + '" alt="' + commenter + '">';
        } else {
            var name = commenter || 'User';
            var initials = name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 2);
            return '<span class="tkt-hst-avatar-initials" style="display:inline-flex;align-items:center;justify-content:center;width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#a78bfa,#7c3aed);color:white;font-size:10px;font-weight:600;text-transform:uppercase;">' + initials + '</span>';
        }
    };


    t.history = function(e) {
    e.preventDefault();
    t.mdlHistory.title.text("Kanban Member History");

    // Board info card (unchanged)
    var boardTypeLabel = '';
    if (t.config.board.board_items_type == 1) {
        boardTypeLabel = 'Ticket';
    } else if (t.config.board.board_items_type == 2) {
        boardTypeLabel = 'Custom';
    }
    $('#board_name_display').text(t.config.board.name || '');
    $('#board_type_display').text(boardTypeLabel || '');

    // ---- Pagination state (client-side) ----
    var allItems = [];           // all history records
    var currentPage = 1;
    var limit = 10;
    var isLoading = false;
    var allLoaded = false;
    var container = t.result;    // the scrollable div (#result)

    // Clear previous results and remove old scroll listener
    t.result.html('');
    container.off('scroll.history');

    // Function to append a chunk of items
    function appendHistoryItems(items) {
        if (!items || items.length === 0) return;
        var member_history = '';
        $.each(items, function(index, value) {
            var actionMessage = '';
            var badgeClass = 'green';
            switch (parseInt(value.action_id)) {
                case 1:
                    badgeClass = 'green';
                    actionMessage = 'Member <a href="' + t.config.url.user_info + '/' + value.new_user_id + '" target="_blank" class="btn-link text-main text-bold">' +
                        value.new_user_name + '</a> added to the board as <span class="text-bold">' + value.new_access_type + '</span>';
                    break;
                case 2:
                    badgeClass = 'green';
                    actionMessage = 'Member <a href="' + t.config.url.user_info + '/' + value.old_user_id + '" target="_blank" class="btn-link text-main text-bold">' +
                        value.old_user_name + '</a> with role <span class="text-bold">' + value.old_access_type + '</span> was removed from the board';
                    break;
                case 3:
                    badgeClass = 'purple';
                    actionMessage = 'Member changed from <a href="' + t.config.url.user_info + '/' + value.old_user_id + '" target="_blank" class="btn-link text-main text-bold">' +
                        value.old_user_name + '</a> to <a href="' + t.config.url.user_info + '/' + value.new_user_id + '" target="_blank" class="btn-link text-main text-bold">' +
                        value.new_user_name + '</a>';
                    break;
                case 4:
                    badgeClass = 'purple';
                    actionMessage = 'Member <a href="' + t.config.url.user_info + '/' + value.new_user_id + '" target="_blank" class="btn-link text-main text-bold">' +
                        value.new_user_name + '</a> role changed from <span class="text-bold">' + value.old_access_type + '</span> to <span class="text-bold">' +
                        value.new_access_type + '</span>';
                    break;
                default:
                    actionMessage = 'Update performed on member';
                    break;
            }
            var timeStr = value.updated_at || '';
            var timeAgoText = t.getTimeAgoText(timeStr);
            var commenterName = value.commenter || 'System';
            var avatarHtml = t.getAvatar(commenterName, '');
            member_history += '<div class="tkt-hst-timeline-item d-flex gap-3 align-items-start">' +
                '<div class="d-flex flex-column align-items-center flex-shrink-0 align-self-stretch">' +
                    '<div class="rounded-circle d-flex flex-column align-items-center justify-content-center text-center text-white flex-shrink-0 tkt-hst-badge ' + badgeClass + '">' + timeAgoText + '</div>' +
                    '<div class="tkt-hst-connector-line"></div>' +
                '</div>' +
                '<div class="flex-fill mt-1 tkt-hst-card mb-1">' +
                    '<div class="d-flex align-items-center gap-1 mb-1 meta-time">' +
                        '<svg width="12" height="12" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                            '<path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="#7F7F7F"/>' +
                        '</svg>' +
                        '<span class="b7-text" style="color:#7F7F7F">' + timeStr + '</span>' +
                    '</div>' +
                    '<div class="d-flex align-items-center gap-2 mb-1">' +
                        '<div class="rounded-circle overflow-hidden flex-shrink-0 tkt-hst-avatar d-flex align-items-center justify-content-center">' + avatarHtml + '</div>' +
                        '<span class="b6-text fw-bold">' + commenterName + '</span>' +
                        '<span class="b6-text fw-bold">•</span>' +
                        '<span class="b6-text fw-light" style="color:#7F7F7F">Changes done by ' + commenterName + '</span>' +
                    '</div>' +
                    '<div class="b6-text fw-normal">' + actionMessage + '</div>' +
                '</div>' +
            '</div>';
        });
        t.result.append(member_history);
    }

    // Function to display a page from the local array
    function displayPage(page) {
        var start = (page - 1) * limit;
        var end = start + limit;
        var chunk = allItems.slice(start, end);
        if (chunk.length === 0) {
            allLoaded = true;
            return;
        }
        appendHistoryItems(chunk);
        currentPage = page;
        if (end >= allItems.length) {
            allLoaded = true;
        }
    }

    // Load all history data (one AJAX call)
    function loadAllHistory() {
        if (isLoading) return;
        isLoading = true;
        var http = $.ajax({
            url: config.url.getBoardMemberHistory,
            type: "POST",
            data: {
                "_token": t.config.token,
                "board_id": t.config.board.id
                // No page/limit – backend returns all records
            }
        });

        http.done(function(data) {
            isLoading = false;
            if (typeof data == "object") {
                if (data.status == "success") {
                    allItems = data.data || [];
                    if (allItems.length === 0) {
                        t.result.html('<div class="text-center p-4"><p>' + (config.translations.Ticket_History_Not_Available || 'No history available') + '</p></div>');
                        allLoaded = true;
                    } else {
                        // Display first page
                        displayPage(1);
                        // Attach scroll listener if there are more items than limit
                        if (allItems.length > limit) {
                            container.on('scroll.history', function() {
                                var $this = $(this);
                                if ($this.scrollTop() + $this.innerHeight() >= $this[0].scrollHeight - 10) {
                                    if (!isLoading && !allLoaded && currentPage * limit < allItems.length) {
                                        displayPage(currentPage + 1);
                                    }
                                }
                            });
                        }
                    }
                } else {
                    sweetAlert('center','error',data);
                }
            }
        });
        http.fail(function() {
            isLoading = false;
            var data = { 'msg': config.translations.something_went_wrong };
            sweetAlert('center', 'error', data);
        });
    }

    loadAllHistory();
    t.mdlHistory.modal("show");
};

    // ---- Event bindings ----
    t.mdlHistory.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));

    t.content.on("click", ".add-kanban-member", $.proxy(t.addKanbanMember));
    t.table.on("click", ".edit-kanban-member", $.proxy(t.editKanbanMember));
    t.table.on("click", ".delete-kanban-member", $.proxy(t.deleteKanbanBoardMember));
    t.content.on("click", "#btnSubmit", $.proxy(t.saveData));
    t.content.on("click", ".btn-searchbox", $.proxy(t.searchData));
    t.content.on("click", ".btn-reload", $.proxy(t.searchData));
    t.content.on("click", ".view-member-history", $.proxy(t.history));
};