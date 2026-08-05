var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#main-live-status-wrapper");
    t.table = t.content.find("#mytable");
    t.page = $("#page_boxed");
    if (!t.page.length) {
        t.page = t.content;
    }
    t.searchbox = t.content.find(".searchbox");
    t.loader = t.searchbox.find(".loader");
    t.nonloader = t.searchbox.find(".nonloader");

    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};
    t.btn = {};

    if (t.config.token) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': t.config.token
            }
        });
    }

    // Modal elements
    t.mdl = t.content.find("#techncianActivitiesModal");
    t.mdl.frm = t.mdl.find("#techncianActivitiesModal-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.statusname = t.mdl.frm.find("#activity_ids");
    t.mdl.frmEl.activity_comment = t.mdl.frm.find("#activity_comments");
    t.mdl.btn = {};
    t.mdl.btnSubmit = t.mdl.find("#btnLogActivitySubmit");

    // Parent checkbox click handler
    $('.chkParent').click(function() {
        var isChecked = $(this).prop("checked");
        $('.checkselect').find('input[type="checkbox"]').prop('checked', isChecked);
    });

    function escapeHtml(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function activeIconHtml() {
        return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
    }

    function inactiveIconHtml() {
        return '<svg width="18" height="18" viewBox="0 0 16 16" fill="none" aria-hidden="true"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
    }

    function idealIconHtml() {
        return '<span class="ideal-user" aria-hidden="true"></span>';
    }

    function buildStatusHtml(d) {
        var statusClass = "inactive";
        var statusIcon = inactiveIconHtml();
        var statusText = "Logged Out";
        var clickableClass = "";
        var dataAttr = "";
        if (d.activity_id != null && d.activity_id != 0) {
            statusClass = "idle";
            statusIcon = idealIconHtml();
            statusText = d.activity || d.current_status?.activity_name || "Break";

            clickableClass = "makeActiveBtn";
            dataAttr = `data-id="${d.id}"`;
        }
        else if (d.current_status?.is_logged_in === true && d.activity_id == 0) {
            statusClass = "active";
            statusIcon = activeIconHtml();
            statusText = "Active";
        }
        else {
            statusClass = "inactive";
            statusIcon = inactiveIconHtml();
            statusText = "Logged Out";
        }
        return `
            <div class="tech-status-wrap">
                <span class="tech-status-pill tech-status-pill--${statusClass} ${clickableClass}" ${dataAttr}>
                    <span class="tech-status-icon">${statusIcon}</span>
                    <span>${statusText}</span>
                </span>
            </div>
        `;
    }
    function getPageLength() {
        return parseInt(t.content.find(".userModulePageLenth").val(), 10) || 10;
    }

    function getSearchValue() {
        return t.searchbox.val().trim();
    }

    function drawWithSearch(api, resetPage) {
        api.search(getSearchValue()).draw(resetPage !== false);
    }
    t.initTooltips = function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            let tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
            tooltip = new bootstrap.Tooltip(el, {
                trigger: 'hover focus'
            });
            el.addEventListener('click', function () {
                tooltip.hide();
                this.blur();
            });
        });
    };
    // DataTable configuration
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        order: [
            [3, 'desc'] // Order by status column
        ],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        pageLength: getPageLength(),
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        lengthChange:false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0, 4, 5] 
        }, {
            targets: 0,
            render: function(d) {
                return '<div class="row">' +
                    '<span class="checkselect pad-lft ms-2"><input type="checkbox" class="form-check-input checkall row_selector" data-id="' + d.id + '" data-status="' + d.activity_id + '"  name="myCheckboxes"/>' +
                    '</div>';
            }
        }, {
            targets: 1, 
            render: function(d) {
                var a = [];
                if (d.technician_name != '' && d.technician_name != null) {
                    a.push("<a class='b5-text fw-medium' " +"href='" + t.config.url.logReport + '/' + d.id + "' " +"target='_blank' " +"data-bs-toggle='tooltip' " +"data-bs-placement='top' " + "title='Technician Log Report Details'>" +d.technician_name +"</a>");
                } else {
                    a.push("");
                }
                return a.join(' ');
            }
        }, {
            targets: 2, 
            render: function(d) {
                return d || "";
            }
        }, {
            targets: 3, 
            render: function(d) {
                var a = [];
                a.push(buildStatusHtml(d));
                return a.join(' ');
            }
        }, {
            targets: 4, 
            render: function(d) {
                if (d.current_status && typeof d.current_status.activity_comment != "undefined" && d.current_status.activity_comment) {
                    return d.current_status.activity_comment;
                }
                return "";
            }
        }, {
            targets: 5, // Location column
            render: function(d) {
                var a = [];
                if (d.current_location_name && d.current_location_name != "") {
                    a.push("<b>C: </b>" + d.current_location_name);
                }
                if (d.base_location_name && d.base_location_name != "") {
                    if (a.length > 0) a.push("<br/>");
                    a.push("<b>B: </b>" + d.base_location_name);
                }
                if (a.length === 0) {
                    a.push("NA");
                }
                return a.join(' ');
            }
        }],
        ajax: {
            url: t.config.url.livestatustUrl,
            type: "POST",
            data: function(d) {
                var searchValue = getSearchValue();
                d._token = t.config.token;
                d.length = getPageLength();
                d.search = d.search || {};
                d.search.value = searchValue;
                d.search_value = searchValue;
            },
            beforeSend: function() {
                // Show loader if needed
                if (t.loader) t.loader.show();
            },
            complete: function() {
                // Hide loader if needed
                if (t.loader) t.loader.hide();
            },
            error: function(xhr, status, error) {
                console.error("DataTable Ajax Error:", error);
                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
            }
        },
        columns: [
            { data: 'a' },
            { data: 'a' },
            { data: 'a.dept_name',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
             },
            { data: 'a' },
            { data: 'a.current_status.activity_comment',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            { data: 'a',className: "amg-table-col-144" },
            { data: 'a.current_status.updated_at',className: "amg-table-col-176",
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
             }
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            
            // Setup search and filter controls
            setupSearchAndFilters(api);
        },
        drawCallback: function(settings) {
            // Rebind event handlers after table redraw
            rebindEventHandlers();
            t.initTooltips();

        }
        
    });
    // Setup search and filter controls
    function setupSearchAndFilters(api) {
        $("#mytable_filter").remove();
        $("#mytable_wrapper").removeClass("form-inline");
        t.table.closest("div").addClass("table-responsive");

        // Setup search input handler
        var searchTimer = null;
        t.searchbox.off(".liveStatus").on("keyup.liveStatus", function (e) {
            if (e.key === "Enter" || e.keyCode === 13) {
                e.preventDefault();
                drawWithSearch(api);
            }
        });

        t.content.find(".userModulePageLenth").off("change.liveStatus").on("change.liveStatus", function() {
            api.page.len(getPageLength()).draw();
        });

        // Setup button references
        t.btn.search = t.content.find(".btn-searchbox");
        t.btn.reload = t.content.find(".btn-reload-list");
    }
    
    // Rebind event handlers after table redraw
    function rebindEventHandlers() {
        // Active button handler
        $(document).off('click.liveStatus', '.makeActiveBtn').on('click.liveStatus', '.makeActiveBtn', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            makeUserActive(userId);
        });
        
        // Inactive button handler
        $(document).off('click.liveStatus', '.makeInActiveBtn').on('click.liveStatus', '.makeInActiveBtn', function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            showInactiveModal(userId);
        });

        // $(document).off('change.liveStatus', '.toggleStatus')
        // .on('change.liveStatus', '.toggleStatus', function () {
        //     let id = $(this).data('id');
        //     makeUserActive(id);
        // });
        $(document).off('click.liveStatus', '.makeActiveBtn').on('click.liveStatus', '.makeActiveBtn', function(e) {
            e.preventDefault();
            let userId = $(this).data('id');

            var send_data = {
                '_token': t.config.token,
                'id': [userId] // single user bhi array me jayega
            };

            sweetAlertPost(
                t.config.translations.are_you_activated, 
                'warning', 
                t.config.url.bulkUserActivate, 
                t, 
                { msg: t.config.translations.something_went_wrong }, 
                send_data
            );
        });
    }
    
    // Make user active
    function makeUserActive(userId) {
        $.ajax({
            url: t.config.url.logUserActivity + "/" + userId,
            type: "POST",
            data: {
                _token: t.config.token,
                activity_id: 0 // Active status
            },
            success: function(res) {
                sweetAlert('center', 'success', res);
                t.dTbl.ajax.reload(null, false);
            },
            error: function(res) {
                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
            }
        });
    }
    
    // Show inactive modal for user
    function showInactiveModal(userId) {
        t.mdl.frmEl.activity_comment.val('');
        $('#logActivityForOtherUser').val(userId);
        loadActivities();
        t.mdl.modal('show');
    }

    function initActivitySelect2() {
        if (!$.fn.select2) {
            return;
        }

        if (t.mdl.frmEl.statusname.hasClass("select2-hidden-accessible")) {
            t.mdl.frmEl.statusname.select2("destroy");
        }

        t.mdl.frmEl.statusname.select2({
            width: "100%",
            placeholder: config.translations.select_activity,
            dropdownParent: t.mdl
        });
    }
    
    // Load activity statuses for dropdown
    function loadActivities() {
        $.ajax({
            url: t.config.url.status,
            method: "GET",
            success: function(res) {
                if (res && res.status == 'success') {
                    if (t.mdl.frmEl.statusname.hasClass("select2-hidden-accessible")) {
                        t.mdl.frmEl.statusname.select2("destroy");
                    }

                    t.mdl.frmEl.statusname.html('<option value=""></option>');
                    $.each(res.data, function(i, k) {
                        t.mdl.frmEl.statusname.append(new Option(k.name, k.id, false, false));
                    });
                    initActivitySelect2();
                }
            },
            error: function() {
                console.error("Failed to load activities");
            }
        });
    }
    
    // Search handler
    t.tableSearch = function(e) {
        e.preventDefault();
        drawWithSearch(t.dTbl);
    };
    
    // Reload handler
    t.reload = function() {
        drawWithSearch(t.dTbl, false);
    };
    
    // Bulk user logout
    t.bulkUserLogout = function(e) {
        e.preventDefault();
        var selected = getSelectedUsers();
        
        if (selected.length === 0) {
            sweetAlert('center', 'error', { msg: config.translations.select_technicien });
            return false;
        }
        
        $('#logActivityForOtherUser').val("");
        loadActivities();
        t.mdl.modal('show');
    };
    
    // Bulk user activate
    t.bulkUserActivate = function(e) {
        e.preventDefault();
        var selected = [];
        var alreadyActive = [];
        $(".checkselect").find(".row_selector").each(function () {
            if ($(this).is(":checked")) {
                let userId = $(this).data("id");
                let status = $(this).data("status");
                if (status == 0) {
                    alreadyActive.push(userId);
                } else {
                    selected.push(userId);
                }
            }
        });
        if (selected.length === 0 && alreadyActive.length > 0) {
            sweetAlert('center', 'error', { msg: config.translations.active_user });
            return false;
        }        
        if (selected.length === 0) {
            sweetAlert('center', 'error', { msg: config.translations.select_technicien  });
            return false;
        }
        
        var send_data = {
            '_token': t.config.token,
            'id': selected
        };
        
        sweetAlertPost(
            config.translations.are_you_activated, 
            'warning', 
            t.config.url.bulkUserActivate, 
            t, 
            { msg: config.translations.something_went_wrong }, 
            send_data
        );
    };
    
    // Get selected users from checkboxes
    function getSelectedUsers() {
        var selected = [];
        $(".checkselect").find(".row_selector").each(function(i, element) {
            if ($(element).is(":checked") && !$(element).prop('disabled')) {
                selected.push($(element).attr("data-id"));
            }
        });
        return selected;
    }
    
    // Activity change handler for bulk logout
    t.activityChange = function(e) {
        e.preventDefault();
        var selected = getSelectedUsers();
        var rowUserId = $('#logActivityForOtherUser').val();

        if (selected.length === 0 && rowUserId) {
            selected.push(rowUserId);
        }
        
        if (selected.length === 0) {
            sweetAlert('center', 'error', { msg: config.translations.select_technicien });
            return false;
        }
        
        if (t.httpCall != true) {
            return false;
        }
        
        t.httpPostPath = t.config.url.bulkUserLogout;
        var formData = new FormData();
        formData.append('activity_ids', t.mdl.frmEl.statusname.val() || '');
        formData.append('activity_comments', t.mdl.frmEl.activity_comment.val() || '');
        formData.append('logActivityForOtherUser', $('#logActivityForOtherUser').val() || '');
        formData.append("user_id", selected);
        formData.append("_token", t.config.token);
        
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
        });
        
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    setTimeout(function() {
                        t.reload();
                        t.mdl.modal('hide');
                        t.mdl.frmEl.activity_comment.val("");
                        $('#logActivityForOtherUser').val("");
                        $('.chkParent').prop('checked', false);
                    }, 800);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        
        http.fail(function() {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
        });
    };
    
    // Event binding
    $(document).on('technicianActivitySaved', function() {
        if (typeof t !== 'undefined' && t.dTbl) {
            t.dTbl.ajax.reload(null, false);
        }
    });
    
    // Modal save button handler
    $(document).on('click', '.saveActivityModal', function(e) {
        t.activityChange(e);
    });
    
    // Button event bindings
    t.btn.search = t.content.find(".btn-searchbox");
    t.btn.reload = t.content.find(".btn-reload-list");
    
    if (t.btn.search.length) {
        t.btn.search.on("click", $.proxy(t.tableSearch));
    }
    
    if (t.btn.reload.length) {
        t.btn.reload.on("click", $.proxy(t.reload));
    }
    t.mdl.find('.closeActivityModal').on('click', function () {
        t.mdl.frmEl.activity_comment.val('');
        t.mdl.frmEl.statusname.val('').trigger('change');
        $('#logActivityForOtherUser').val('');
    });
    
    // Bulk action handlers
    t.page.on("click", ".btnd-inactive", $.proxy(t.bulkUserLogout));
    t.page.on("click", ".btna-active", $.proxy(t.bulkUserActivate));
    
    // Initial data load
    if (t.dTbl) {
        t.dTbl.ajax.reload();
    }
};
