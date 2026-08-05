var MyApp = function (config) {
    var t = this;
    t.myApp = this;
    config.myApp = this;
    t.ticket = new TicketRequest(config);
}
var TicketRequest = function (config) {
    var t = this;
    t.config = config;
    t.perPage = 10;
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.content = $("#sr-list-wrapper");
    t.searchbox = t.content.find(".sr-list-search");
    t.pageLimiter = t.content.find("#pageLimiter");
    t.pagebtns = t.content.find("#pagebtns");
    t.pageBtmSummary = t.content.find("#page-btm-summary");
    t.sortbtns = t.content.find(".sort-buttons");
    t.export = t.content.find(".btn-download");

    t.filters = {
        wrapper: t.content.find("#srFilterModal"),
        data: {
            problem_categories: {}
        }
    };
    t.filters.status = t.filters.wrapper.find("#filter_by_status");
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority");
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.pab = t.filters.wrapper.find("#filter_by_pab");
    t.filters.filter_by_approver = t.filters.wrapper.find("#filter_by_approver");
    t.filters.hierarchy_approval = t.filters.wrapper.find("#hierarchy_approval");
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
    t.filters.daterange = t.filters.wrapper.find("#daterange");
    t.filters.vip_tickets = t.filters.wrapper.find("#filter_by_vip_tickets");
    t.filters.current_pab = t.filters.wrapper.find("#filter_by_current_pab");
    t.filters.btnfilterclr = t.filters.wrapper.find(".btn-clear-filter");
    t.filters.btnfilter = t.filters.wrapper.find(".btn-filter");

    t.bulkDecisionModal = t.content.find('#bulkDecisionMdl');
    t.bulkDecisionModal.frm = t.bulkDecisionModal.find("#bulkDecisionFrm");
    t.bulkDecisionModal.frmEl = {};
    t.bulkDecisionModal.frmEl.approve_status =  t.bulkDecisionModal.frm.find('#approve_status');
    t.bulkDecisionModal.frmEl.comments =  t.bulkDecisionModal.frm.find('#comments');
    t.bulkDecisionSubmit = t.bulkDecisionModal.find("#btnSubmit");

    t.bulkDecisionModal.frmEl.comments.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        width: '100%',
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 100,
        focus: true
    });

    if (t.pagebtns.length > 0) {
        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            edges: 2,
            displayedPages: 2,
            onPageClick: function (page, e) {
                if (typeof e !== "undefined") e.preventDefault();
                t.load(page);
            }
        });
    } else {
        console.error('Pagination container #pagebtns not found!');
    }

    t.filters.fun = {
        reload_status: function() {
            $.each(t.config.statuses, function(i, k) {
                t.filters.status.append(new Option(k.name, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        reload_department: function() {
            $.get(t.config.url.departments_service_request, function(data) {
                if(typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i,k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
            t.filters.department.trigger("change");
        },
        reload_problem_category: function() {
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function(i, k) {
                            t.filters.problem_category.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                                t.filters.data.problem_categories["sc" + k.id] = k.sub;
                            }
                        });
                        t.filters.problem_category.trigger("change");
                    }
                });
            }
            t.filters.problem_category.trigger("change");
        },
        reload_sub_category: function() {
            var prblm = t.filters.problem_category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm], function(i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {

                }
            }
            t.filters.sub_category.trigger("change");
        },
        reload_pab: function() {
            t.filters.pab.append(new Option('', '', false, false));
            $.each(t.config.pabs, function(i, k) {
                t.filters.pab.append(new Option(k.name, k.id, false, false));
            });
            t.filters.pab.trigger("change");
        },
        reload_current_pab: function() {
            t.filters.current_pab.empty().append(new Option('', '', false, false));
            $.each(t.config.pabs, function(i, k) {
                t.filters.current_pab.append(new Option(k.name, k.id, false, false));
            });
            t.filters.current_pab.trigger("change");
        },
        reload_approver: function() {
            t.filters.filter_by_approver.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.filter_by_approver.parent(),
                ajax: {
                    url: t.config.url.getApproverSR,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: companyId,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: t.config.translations.select_approver,
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "0px";
                    return t.userDropdownFormat(data, imgPaddingLeft);
                },
            }));
            t.filters.filter_by_approver.trigger("change");
        },
    };

    t.load = function (page = 1) {

        if (!page) {
            try {
                page = t.pagebtns.data('pagination') ? t.pagebtns.pagination("getCurrentPage") : 1;
            } catch (e) {
                page = 1;
            }
        }

        $.ajax({
            url: t.config.url.requests,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': t.config.token
            },
            data: {
                search: t.config.search,
                page: page,
                size: t.perPage,
                order: t.config.sort_dir,
                filters: t.config.other_filters,
                main_filter: t.config.main_filter,
                // sr_status : t.config.sr_status,
                created_by: t.config.created_by,
                pab_id: t.config.pab_id,
                approval_id: t.config.approval_id,
                // status: t.config.status,
                // pendingrequests: t.config.url.pendingrequests
            },
            dataType: "json",
            beforeSend: function () {
                $('.srq-body').html(`
                <div class="dot-loader">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `);
            },
            success: function (response) {
                let html = '';
                if (response.data.length > 0) {
                    $.each(response.data, function (index, req) {

                        html += `
                        <div class="srq-row-card">
                            <div class="srq-row-left">
                                <div class="srq-row-title-line">
                                    ${
                                        req.status_id == 2 &&
                                        jQuery.inArray("ServiceRequestBulkApproval", t.config.permissions) !== -1
                                        ? `<input type="checkbox" class="srq-checkbox" data-id="${req.id}">`
                                        : ''
                                    }
                                    <a href="${t.config.url.requestInfo + '/' + req.id}" class="srq-row-title-link">
                                        <span class="srq-row-title srq-tooltip-text" data-bs-toggle="tooltip" title="${req.subject ?? ''}">
                                            ${req.subject ?? ''},
                                        </span>
                                        <span class="srq-id-badge">
                                            #${req.procure_tag ?? ''}
                                        </span>
                                    </a>
                                    ${req.is_vip_user == 1 ?
                                        `<span class="tkt-vip-badge">
                                            <svg width="36" height="20" viewBox="0 0 46 25" fill="none">
                                                <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" fill="#F6EEFF" />
                                                <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" stroke="url(#vip1)" />
                                                <path d="M14.0229 7.31818L16.8567 15.6108H16.9711L19.8049 7.31818H21.4654L17.8013 17.5H16.0264L12.3624 7.31818H14.0229ZM24.1919 7.31818V17.5H22.6557V7.31818H24.1919ZM26.1492 17.5V7.31818H29.7784C30.5706 7.31818 31.2268 7.46236 31.7472 7.75071C32.2675 8.03906 32.657 8.43347 32.9155 8.93395C33.174 9.43111 33.3033 9.99124 33.3033 10.6143C33.3033 11.2408 33.1724 11.8042 32.9105 12.3047C32.652 12.8018 32.2609 13.1963 31.7372 13.4879C31.2169 13.7763 30.5623 13.9205 29.7734 13.9205H27.2777V12.6179H29.6342C30.1347 12.6179 30.5407 12.5317 30.8523 12.3594C31.1638 12.1837 31.3925 11.9451 31.5384 11.6435C31.6842 11.3419 31.7571 10.9988 31.7571 10.6143C31.7571 10.2299 31.6842 9.88849 31.5384 9.5902C31.3925 9.2919 31.1622 9.05824 30.8473 8.8892C30.5358 8.72017 30.1248 8.63565 29.6144 8.63565H27.6854V17.5H26.1492Z" fill="#EB0964" />
                                                <defs><linearGradient id="vip1" x1="23" y1="0" x2="23" y2="25" gradientUnits="userSpaceOnUse"><stop stop-color="#4102FF" /><stop offset="1" stop-color="#FB0956" /></linearGradient>
                                                </defs>
                                            </svg>
                                        </span>`
                                    :''}
                                </div>
                                <div class="srq-row-meta">
                                    <div class="srq-meta-item">
                                        <span class="tkt-avatar-sm me-1">${t.getAvatar(req)}</span>
                                        <span data-bs-toggle="tooltip" title="${t.config.translations.creator}">
                                            ${req.creator_name ?? ''}
                                        </span>
                                    </div>
                                    <div class="srq-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="3" width="7" height="7"/>
                                            <rect x="14" y="3" width="7" height="7"/>
                                            <rect x="3" y="14" width="7" height="7"/>
                                            <rect x="14" y="14" width="7" height="7"/>
                                        </svg>
                                        <span data-bs-toggle="tooltip" title="${t.config.translations.department}">
                                            ${req.dep_name ?? ''}
                                        </span>
                                    </div>
                                    <div class="srq-meta-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                            <line x1="3" y1="10" x2="21" y2="10"/>
                                        </svg>
                                        <span  data-bs-toggle="tooltip" title="${t.config.translations.created_at}">
                                            ${req.created_at_format ?? ''}
                                        </span>
                                    </div>
                                    ${req.company_name
                                    ?
                                    `
                                                <div class="srq-meta-item">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/>
                                                    </svg>
                                                    <span data-bs-toggle="tooltip" title="${t.config.translations.company_name}">
                                                        ${req.company_name}
                                                    </span>
                                                </div>
                                                `
                                :
                                ''
                            }
                                    <div class="srq-approval">
                                        Approval :
                                        <span>
                                            ${req.approval_required ?? ''}
                                        </span>
                                    </div>
                                    <button class="amg-btn amg-btn-primary amg-btn-sm" id="viewDetail" data-id="${req.id}">
                                       ${t.config.translations.view_details}
                                    </button>
                                </div>
                            </div>
                            <div class="srq-row-right">
                                <div class="srq-row-right-item">
                                    <div class="srq-right-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                         ${t.config.translations.status}
                                    </div>
                                    <span class="srq-status-badge">
                                        ${req.status ?? ''}
                                    </span>
                                </div>

                                <div class="srq-row-right-item">
                                    <div class="srq-right-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                            <polyline points="9 22 9 12 15 12 15 22"/>
                                        </svg>
                                        ${t.config.translations.authority_board}
                                    </div>
                                    <span class="srq-right-val srq-tooltip-text" data-bs-toggle="tooltip" title="${req.pab ?? req.pendingPab ?? ''}">
                                        ${req.pendingPab ?? req.pab ?? ''}
                                    </span>
                                </div>

                                <div class="srq-row-right-item">
                                    <div class="srq-right-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polyline points="12 6 12 12 16 14"/>
                                        </svg>
                                        ${t.config.translations.last_update}
                                    </div>
                                    <span class="srq-right-val" style="font-size:12px;">
                                        ${req.updated_at_format ?? ''}
                                    </span>
                                </div>
                            </div>
                        </div>
                        `;
                    });
                } else {
                    html = `
                        <div class="text-center p-4">
                            ${t.config.translations.no_request_found}
                        </div>
                    `;
                }
                $('.srq-body').html(html);

                let currentPage = parseInt(response.page);
                let perPage = t.perPage;

                let startEntry = ((currentPage - 1) * perPage) + 1;
                let endEntry = Math.min(currentPage * perPage, response.filtered);
                if (response.filtered == 0) startEntry = 0;

                let recordText = `Showing ${startEntry} to ${endEntry} of ${response.filtered} entries`;
                if (response.filtered != response.total) {
                    recordText += ` (filtered from ${response.total} total records)`;
                }
                if (t.pageBtmSummary.length > 0) {
                    t.pageBtmSummary.html(`<div class="showing-info">${recordText}</div>`);
                }

                try {
                    t.pagebtns.pagination("updateItems", parseInt(response.filtered) || 0);
                    t.pagebtns.pagination("drawPage", currentPage || 1);
                } catch (e) {
                    console.warn('Pagination update failed:', e);
                }

                t.initTooltips();
                if ($('#selectAll').is(':checked')) {
                    $('#selectAll').prop('checked', false);
                }
            },
            error: function () {
                $('.srq-body').html(`<div class="text-danger text-center p-4">${t.config.translations.something_went_wrong}</div>`);
            }
        });
    }

    // Helper function to get avatar
    t.getAvatar = function(req) {
        let commenter = req.creator_name;
        let avatar = req.commenter_avatar;
        // Check if it's System user
        if (commenter === "System" || commenter === "system") {
            return '<img style="width: 15px; height: 15px; border-radius: 50%; object-fit: cover;" src="' + t.config.url.base_url + '/assets/mati.png" alt="System">';
        }   
        
        // For other users
        if (avatar && avatar !== '' && avatar !== null) {
            // If avatar exists in database
            return '<img style="width: 15px; height: 15px; border-radius: 50%; object-fit: cover;" src="' + t.config.url.base_url + '/storage/avatar/' + avatar + '" alt="' + escapeHtml(commenter) + '">';
        } else {
            // Otherwise show initials
            var name = commenter || 'User';
            var initials = name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 1);
            return '<span class="tkt-hst-avatar-initials" style="display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; border-radius: 50%; background: linear-gradient(135deg, #a78bfa, #7c3aed); color: white; font-size: 10px; font-weight: 600; text-transform: uppercase;">' + initials + '</span>';
        }
    }
    
    // Escape HTML function to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    t.initTooltips = function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    t.initTooltips();
    $(document).on('change', '#selectAll', function () {
        $('.srq-checkbox').prop('checked', $(this).is(':checked'));
    });
    $(document).on('change', '.srq-checkbox', function () {
        let total = $('.srq-checkbox').length;
        let checked = $('.srq-checkbox:checked').length;
        $('#selectAll').prop('checked', total === checked);
    });
    t.pageLimiter.on("change", function () {
        t.perPage = parseInt($(this).val()) || 10;
        if (t.pagebtns.data('pagination')) {
            t.pagebtns.pagination("updateItemsOnPage", t.perPage);
        }
        t.load(1);
    });
    $(document).on('click', '.btn-reload-list', function () {
        t.config.search = $('.sr-list-search').val();
        t.load(1);
    });
    $(document).on('keypress', '.sr-list-search', function (e) {
        if (e.which == 13) {
            e.preventDefault();
            t.config.search = $(this).val();
            t.load(1);
        }
    });
    $(document).on('click', '.btn-open-filter', function () {
        t.filters.wrapper.modal('show');
    });

    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.other_filters = {};
        if(t.filters.department.val() && t.filters.department.val() != 'null'){
            t.config.other_filters.department = t.filters.department.val();
        }
        if(t.filters.problem_category.val() && t.filters.problem_category.val() != 'null'){
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        }
        if(t.filters.sub_category.val() && t.filters.sub_category.val() != 'null'){
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if(t.filters.status.val() && t.filters.status.val() != 'null'){
            t.config.other_filters.status = t.filters.status.val();
        }
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null'){
            t.config.other_filters.based_on = t.filters.based_on.val();
        }
        if(t.filters.pab.val() && t.filters.pab.val() != 'null'){
            t.config.other_filters.pab = t.filters.pab.val();
        }
        if(t.filters.filter_by_approver.val() && t.filters.filter_by_approver.val() != 'null'){
            t.config.other_filters.filter_by_approver = t.filters.filter_by_approver.val();
        }
        if(t.filters.hierarchy_approval.val() && t.filters.hierarchy_approval.val() != 'null'){
            t.config.other_filters.hierarchy_approval = t.filters.hierarchy_approval.val();
        }    
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null'){
            t.config.other_filters.daterange = t.filters.daterange.val();
        }
        if(t.filters.current_pab.val() && t.filters.current_pab.val() != 'null'){
            t.config.other_filters.current_pab = t.filters.current_pab.val();
        }
        if(t.filters.vip_tickets.val() && t.filters.vip_tickets.val() != 'null'){
            t.config.other_filters.vip_tickets = t.filters.vip_tickets.val();
        }

        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
        t.filters.wrapper.modal("hide");
    };
    
    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = t.searchbox.validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.load(1);
        }
        else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.load(1);
        }
    };
    t.btnClrFilter = function() {
        t.config.other_filters = {};
        t.filters.status.val("").trigger("change");
        t.filters.department.val("").trigger("change");
        t.filters.problem_category.empty().val("").trigger("change");
        t.filters.sub_category.empty().val("").trigger("change");
        t.filters.pab.val("").trigger("change");
        t.filters.filter_by_approver.val("").trigger("change");
        t.filters.hierarchy_approval.val("").trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.filters.current_pab.val("").trigger("change");
        t.filters.priority.val("").trigger("change");
        t.filters.daterange.val('');
        resetFilterCount();
        t.filters.wrapper.modal("hide");
        t.load(1);
    };

    t.renderSortFields = function () {
        var sf = t.sortbtns.find(".sort-fields");
        var s = t.config.sort_dir;
        var $dirSort = t.sortbtns.find(".dir-sort");

        if (s.dir == 1) {
            $dirSort.find("#sortAscSvg").removeClass("hide");
            $dirSort.find("#sortDescSvg").addClass("hide");
        } else {
            $dirSort.find("#sortAscSvg").addClass("hide");
            $dirSort.find("#sortDescSvg").removeClass("hide");
        }

        $dirSort.attr("data-id", s.id);
        $dirSort.attr("data-dir", s.dir == 1 ? "asc" : "desc");

        sf.empty();
        $.each(t.config.sort_fields, function (i, d) {
            $('<li class="list-group-item ' + (d.id == s.id ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text +
            '</li>').appendTo(sf);
        });
    };

    t.sortbtns.on("mousedown", ".sort-fields li", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var id = $(this).attr("data-id");

        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 2;
        }

        t.renderSortFields();
        t.sortbtns.removeClass("open");

        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () {
            t.load();
        }, 300);
    });

    t.sortbtns.on("mousedown", ".dir-sort", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;

        t.renderSortFields();

        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () {
            t.load();
        }, 300);
    });

    t.sortbtns.on("click", ".dropdown-toggle", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        t.sortbtns.toggleClass("open");
    });

    $(document).on("mousedown", function (e) {
        if (!$(e.target).closest(".sort-buttons").length) {
            t.sortbtns.removeClass("open");
        }
    });

    t.renderSortFields();

    t.sortbtns.on("click", ".dropdown-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.sortbtns.toggleClass("open");
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".sort-buttons").length) {
            t.sortbtns.removeClass("open");
        }
    });

    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_request + "?q=" + t.config.export_filters + "&main_filter=" + t.config.main_filter + "&created_by=" + t.config.created_by + "&pab_id=" + t.config.pab_id + "&approval_id=" + t.config.approval_id +"&status_id="+t.config.status +"&company_id="+companyId;
    }
    t.bulkDecision = function() {
        var selected = [];
        $(".srq-checkbox").each(function(i, element) {
            if($(element).is(":checked") && !$(element).prop('disabled')) {
                selected.push($(element).attr("data-id"));
            }
        });
        if(selected.length == 0) {
            var data = {
                'msg' : t.config.translations.service_request_atleast,
            };
            sweetAlert('center','error',data);
            return false;
        }
        t.bulkDecisionModal.find('.decision_ticket').html("");
        comments = t.bulkDecisionModal.frmEl.comments.val('').summernote('code', '');
        t.bulkDecisionModal.find('#shows_error_approve').html('');
        $.each(selected, function(i, v) {
            t.bulkDecisionModal.find('.decision_ticket').append("<li>My Approval Id #"+v+"</li>");
        });
        $('#decision_ticket').val(selected);
        t.bulkDecisionModal.modal('show');
    }
    t.bulkDecisionRequest = function(e) {
        e.preventDefault();
        let s = t.bulkDecisionModal.frmEl.comments.val();
        count = s.replaceAll("&nbsp;", "").trim();
        if(count.length <= 0 && t.config.client !== "etherealmachines") {
            t.bulkDecisionModal.find('#shows_error_approve').html('This field is required');
            t.bulkDecisionModal.find('#shows_error_approve').css({'color':'#F12F35','font-size':'13px','font-family':'Arial, sans-serif'});
            return false;
        } else {
            t.bulkDecisionModal.find('#shows_error_approve').html('');
        }
        var selected = [];
        $(".srq-checkbox").each(function(i, element) {
            if($(element).is(":checked") && !$(element).prop('disabled')) {
                selected.push($(element).attr("data-id"));
            }
        });
        var fd = new FormData(t.bulkDecisionModal.frm[0]);
        fd.append('decision_ticket', selected);
        $.ajax({
            method : "POST",
            url : t.config.url.bulkDecision,
            data : fd,
            processData: false,
            contentType: false,
            success : function(data) {
                if(data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    t.bulkDecisionModal.modal('hide');
                    t.load(1);
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            fail : function(data) {
                sweetAlert('center', 'error', data);
                return false;
            },
        });
    }

    t.bulkDecisionSubmit.on('click',t.bulkDecisionRequest);
    t.bulkDecisionModal.frmEl.approve_status.select2($.extend({}, {
        dropdownParent: t.bulkDecisionModal.frmEl.approve_status.parent(),
        width: '100%'
    }));
    var select2Opts = {
        width: "100%",
        dropdownParent: t.filters.wrapper,
    };
    t.filters.status.select2($.extend({}, select2Opts, {placeholder: t.config.translations.filter_by_status}));
    t.filters.department.select2($.extend({}, select2Opts, {placeholder: t.config.translations.filter_by_department}));
    t.filters.problem_category.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_by_problem_category}));
    t.filters.sub_category.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_by_sub_category }));
    t.filters.pab.select2($.extend({}, select2Opts, {placeholder: t.config.translations.filter_by_authority_board}));
    t.filters.based_on.select2($.extend({}, select2Opts, {placeholder: t.config.translations.filter_by_date}));
    t.filters.hierarchy_approval.select2($.extend({}, select2Opts, {placeholder:t.config.translations.filter_by_approval_mode}));
    t.filters.vip_tickets.select2($.extend({}, select2Opts, {placeholder:t.config.translations.filter_by_vip_tickets}));
    console.log(t.config.translations.filter_by_vip_tickets);
    t.filters.current_pab.select2($.extend({}, select2Opts, {placeholder: t.config.translations.filter_by_current_authority_board}));
    t.filters.fun.reload_department();
    t.filters.fun.reload_status();
    t.filters.fun.reload_pab();
    t.filters.fun.reload_current_pab();
    t.filters.fun.reload_approver();
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    t.filters.btnfilter.on("click", t.search);
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.export.on("click", $.proxy(t.download));
    t.content.on("click", ".btn-blk-decision", $.proxy(t.bulkDecision));

    t.userDropdownFormat = function (s,imgPaddingLeft) {
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
    $(document).on("click", "#viewDetail", function () {
        let id = $(this).data("id");
        window.open(`${t.config.url.requestInfo}/${id}`, "_blank");
    });
    
    t.load();
}