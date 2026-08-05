var LeadershipBoard = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.pageTitle = $('#date-ranges');
    t.techListEl = t.content.find('#lbdList');
    t.techListLoader = t.content.find('#techListLoader');
    t.searchbox = t.content.find('#searchInput');
    t.refreshList = t.content.find('.btn-refresh');
    t.shortList = t.content.find('#short-list');
    t.printList = t.content.find('#print-list');
    t.rankOrder = 'asc';
    t.escalationTable = $('#escalation-table');
    t.slaTable = $('#sla-table');
    t.feedbackList = $('#feedbackList');
    t.chart = {};
    t.dark_color = ['#cd3333'];
    t.color1 = ['rgba(205,51,51,0.3)'];
    t.color2 = ['rgba(205,51,51,0.5)'];
    t.color3 = ['rgba(205,51,51,0.7)'];
    t.color4 = ['rgba(205,51,51,0.9)'];
    t.escalationScroll = $('#escScrollBox');
    t.mdl = { filter: {} };
    t.filters = { data: { problem_categories: {} } };
    t.modal = t.content.find("#filterModal");
    t.mdl.filterForm = t.modal.find("#filterForm");
    t.mdl.filter.department = t.mdl.filterForm.find("#department");
    t.mdl.filter.category = t.mdl.filterForm.find("#category");
    t.mdl.filter.subcategory = t.mdl.filterForm.find("#subcategory");
    t.mdl.filter.daterange = t.mdl.filterForm.find("#daterange");
    t.mdl.filter.based_on = t.mdl.filterForm.find("#based_on");
    t.mdl.filter.reset = t.mdl.filterForm.find(".btn-clear-filter");
    t.mdl.filter.apply = t.mdl.filterForm.find(".btn-filter");
    t.escTbl = t.slaTbl = null;
    t.ticketChart = null;

    

    let page = 1;
    let isLoading = false;
    let hasMoreData = true;
    let isFirstLoad = true;
    let autoClickedFirstTech = false;
    let searchKeyword = '';

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // ---------- Date Range Picker ----------
    function initDateRangePicker() {
        var start = moment().subtract(29, 'days');
        var end = moment();
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
        cb(start, end);
    }

    window.resetDateRangeFilter = function() {
        var start = moment().subtract(29, 'days');
        var end = moment();
        $('#reportrange').data('daterangepicker').setStartDate(start);
        $('#reportrange').data('daterangepicker').setEndDate(end);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
    };

    initDateRangePicker();

    // ---------- Filter logic ----------
    t.cache_filter_values = function () {
        t.config.other_filters = {};
        if (t.mdl.filter.department.val() && t.mdl.filter.department.val() != 'null')
            t.config.other_filters.department = t.mdl.filter.department.val();
        if (t.mdl.filter.category.val() && t.mdl.filter.category.val() != 'null')
            t.config.other_filters.problem_category = t.mdl.filter.category.val();
        if (t.mdl.filter.subcategory.val() && t.mdl.filter.subcategory.val() != 'null')
            t.config.other_filters.sub_category = t.mdl.filter.subcategory.val();
        if (t.mdl.filter.based_on.val() && t.mdl.filter.based_on.val() != 'null')
            t.config.other_filters.based_on = t.mdl.filter.based_on.val();
        if (t.mdl.filter.daterange.val() && t.mdl.filter.daterange.val() != 'null')
            t.config.other_filters.date_range = t.mdl.filter.daterange.val();
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    var select2Opts = { width: "100%" };
    t.mdl.filter.subcategory.select2($.extend({}, select2Opts, { placeholder: "select", dropdownParent: t.mdl.filter.subcategory.parent(), allowClear: true }));
    t.mdl.filter.category.select2($.extend({}, select2Opts, { placeholder: "select", dropdownParent: t.mdl.filter.category.parent(), allowClear: true }));
    t.fun = {
        reload_sub_category: function () {
            t.mdl.filter.subcategory.empty();
            var prblm = [t.mdl.filter.category.val()];
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(prblm, function (i, v) {
                        $.each(t.filters.data.problem_categories["sc" + v], function (i, k) {
                            t.mdl.filter.subcategory.append(new Option(k.name, k.id, false, false));
                        });
                    });
                } catch (e) {}
            }
            t.mdl.filter.subcategory.trigger("change");
        },
        reload_problem_category: function () {
            t.mdl.filter.category.empty();
            var department = t.mdl.filter.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function (i, k) {
                            t.mdl.filter.category.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub))
                                t.filters.data.problem_categories["sc" + k.id] = k.sub;
                        });
                        t.mdl.filter.category.trigger("change");
                    }
                });
            }
            t.mdl.filter.category.trigger("change");
        },
    };
    t.mdl.filter.department.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.filter.department.parent(),
        ajax: {
            url: t.config.url.departments_based_on_privilage + "/" + t.config.user.company_id,
            dataType: "json",
            delay: 200,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) { return { results: $.map(data.data, function(item) { return { id: item.id, text: item.name }; }) }; }
        },
        placeholder: 'select department',
        allowClear: true,
    }));
    t.mdl.filter.department.on("change", $.proxy(t.fun.reload_problem_category));
    t.mdl.filter.category.on("change", $.proxy(t.fun.reload_sub_category));

    // ---------- Load technician list (left panel) ----------
    t.loadTechList = function () {
        if (isLoading || !hasMoreData) return;
        isLoading = true;
        if (isFirstLoad) {
            t.techListLoader.show();
            $('#noTechFound').addClass('d-none');
        } else {
            $('#scrollLoader').show();
        }
        var url = (Object.keys(t.config.other_filters || {}).length > 0) ? t.config.url.fetchLeaderboard : t.config.url.fetchLeaderboardCache;
        $.ajax({
            type: "get",
            url: url,
            data: {
                page: page,
                search: searchKeyword,
                filters: t.config.other_filters,
                order_by: 'rank',
                order_dir: t.rankOrder,
            },
            success: function (res) {
                isLoading = false;
                t.techListLoader.hide();
                $('#scrollLoader').hide();
                isFirstLoad = false;
                t.pageTitle.empty();
                if (res.data && res.data.length > 0 && res.data[0].date_range) {
                    t.pageTitle.text(`(${res.data[0].date_range})`);
                }

                if (!res.data || res.data.length === 0) {
                    if (page === 1) {
                        t.techListEl.empty();
                        $('#techListLoader').hide();
                        $('#noTechFound').removeClass('d-none');
                    }
                    hasMoreData = false;
                    return;
                }
                let html = '';
                $.each(res.data, function (index, tech) {
                    let rankCircleClass = 'lbd-rc-default';
                    if (tech.rank == 1) rankCircleClass = 'lbd-rc-1';
                    else if (tech.rank == 2) rankCircleClass = 'lbd-rc-2';
                    else if (tech.rank == 3) rankCircleClass = 'lbd-rc-3';
                    let initials = (tech.full_name || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
                    let avatarGradient = tech.rank == 1 ? 'linear-gradient(135deg,#f59e0b,#d97706)': (tech.rank == 2 ? 'linear-gradient(135deg,#22c55e,#16a34a)': (tech.rank == 3 ? 'linear-gradient(135deg,#6366f1,#4338ca)': 'linear-gradient(135deg,#9ca3af,#6b7280)'));
                    let score = tech.total_score || '0.00';
                    let avatarHtml = '';
                    if (tech.avatar) {
                        avatarHtml =`
                        <div class="lbd-av avatar-fallback" style="background:${avatarGradient};">
                            <img src="/storage/avatar/${tech.avatar}" alt="${escapeHtml(tech.full_name)}" class="img-fluid rounded-circle w-100 h-100" onerror=" this.style.display='none'; this.parentNode.classList.add('show-initials'); this.parentNode.innerHTML='${initials}';">
                        </div>`;
                    } else {
                        avatarHtml = `
                            <div class="lbd-av show-initials"
                                style="background:${avatarGradient};">
                                ${initials}
                            </div>`;
                    }

                    html += `
                        <div class="lbd-item top3" data-tech-id="${tech.id}" data-rank="${tech.rank}" data-total-ticket="${tech.total_tickets || 0}">
                            <div class="lbd-rank-circle ${rankCircleClass}">
                                ${String(tech.rank).padStart(2, '0')}
                            </div>
                            ${avatarHtml}
                            <div style="flex:1;min-width:0;">
                                <div class="lbd-name">${escapeHtml(tech.full_name)}</div>
                                <div class="lbd-tid">${tech.username}</div>
                            </div>
                            <div class="lbd-score-wrap">
                                <span class="lbd-score-pill">${score}</span>
                                
                            </div>
                        </div>`;
                });
                t.techListEl.append(html);
                if (!autoClickedFirstTech && page === 1) {
                    setTimeout(function () { t.techListEl.find('.lbd-item:first').trigger('click'); autoClickedFirstTech = true; }, 100);
                }
                page++;
            },
            error: function () { isLoading = false; t.techListLoader.hide(); $('#scrollLoader').hide(); isFirstLoad = false; }
        });
    };

    t.techListEl.on('scroll', function () {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) t.loadTechList();
    });

    // ---------- Load technician details ----------
    t.loadTechDetails = function (techId) {
        if (!techId) return;
        $('#techDetailsLoader').fadeIn(150);
        var url = (Object.keys(t.config.other_filters || {}).length > 0) ? t.config.url.leaderboardDetails + '/' + techId : config.url.getTechnicianDetailsCache + '/' + techId;
        $.ajax({
            type: 'GET', url: url, data: { filters: t.config.other_filters },
            success: function (data) {
                $('#techDetailsLoader').fadeOut(150);
                if (!data) { $('#summary_text').text('No technician details found.'); return; }
                t.updateTechDetailsUI(data);
            },
            error: function () { $('#techDetailsLoader').fadeOut(150); $('#summary_text').text('Failed to load technician details.'); }
        });
    };

    t.TechDetail = function (e) {
        e.preventDefault();
        t.techListEl.find('.lbd-item').removeClass('active');
        $(this).addClass('active');
        let techId = $(this).data('tech-id');
        let rank = $(this).data('rank');
        t.currentRank = rank;
        $('#detail_rank').text('#' + rank);
        t.printList.attr('data-techid', techId);
        t.printList.attr('data-rankno', rank);
        t.loadTechDetails(techId);
        t.initTabs(techId);
        $('.nav-link[data-bs-toggle="tab"]').each(function () {
            $(this).attr('data-techid', techId);
        });
    };

    t.updateTechDetailsUI = function (data) {
        let initials = (data.full_name || 'U').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
        if (data.avatar) {
            $('#profileAvatarInit').html(`
                <img src="/storage/avatar/${data.avatar}" alt="${data.full_name}" class="img-fluid rounded-circle w-100 h-100" onerror="this.parentElement.innerHTML='${initials}'">
            `);
        } else {
            $('#profileAvatarInit').text(initials);
        }
        $('#detail_name').html('<a href="' + t.config.url.user_info + '/' + data.technician_id + '" target="_blank">' + (data.full_name || '-') + '</a>');
        if (parseInt(t.currentRank) === 1) {
            $('#topPerformerBadge').removeClass('d-none');
        } else {
            $('#topPerformerBadge').addClass('d-none');
        }
        $('#detail_tech_id').html('<a href="' + t.config.url.user_info + '/' + data.technician_id + '" target="_blank">' + (data.username || '-') + '</a>');
        $('#detail_email').html('<a href="' + t.config.url.user_info + '/' + data.technician_id + '" target="_blank">' + (data.email || '-') + '</a>');
        $('#detail_phone').html('<a href="' + t.config.url.user_info + '/' + data.technician_id + '" target="_blank">' + (data.phone || '-') + '</a>');
        let avgFeedback = data.avg_feedback ? data.avg_feedback.toFixed(2) : '0.00';
        let totalTickets = data.total_tickets ?? 0;
        $('#stat_feedback').html(`${avgFeedback} <small>(From ${totalTickets} Tickets)</small>`);
        let filters = encodeURIComponent(JSON.stringify(t.config.other_filters));
        $('#stat_escalations').html('<a href="' + t.config.url.esclate_tickets + '?tech_id=' + data.technician_id + '&filter=' + filters + '" target="_blank">' + (data.escalation_count ?? 0) + '</a>');
        $('#stat_sla').html('<a href="' + t.config.url.ticket_list + '/sla_breached?tech_id=' + data.technician_id + '&filter=' + filters + '" target="_blank">' + (data.sla_breached ?? 0) + '</a>');
        $('#stat_ticket_not_responded').text(data.not_responded_tickets ?? 0);
        $('#stat_response').text((data.avg_response_time ?? 0) + ' hrs');
        $('#summary_text').html(data.ai_summary || 'No AI summary available.');
    };

    // ---------- Feedback tab infinite scroll ----------
    t.feedbackPage = 1;
    t.feedbackSize = 10;
    t.feedbackLoading = false;
    t.feedbackHasMore = true;
    t.currentTechId = null;

    t.loadFeedback = function () {
        if (t.feedbackLoading || !t.feedbackHasMore || !t.currentTechId) return;
        t.feedbackLoading = true;
        $('#tabLoader').show();
        $.ajax({
            type: 'GET',
            url: t.config.url.leaderboard_feedback + '/' + t.currentTechId,
            data: { filters: t.config.other_filters, page: t.feedbackPage, size: t.feedbackSize },
            success: function (data) {
                $('#tabLoader').hide();
                t.feedbackLoading = false;
                if (!data || !data.data || data.data.length === 0) {
                    if (t.feedbackPage === 1) {
                        $('#feedbackList').html('<div class="text-center" style="padding:40px;"><i class="bi bi-chat-dots" style="font-size:40px;color:#ccc;"></i><p style="margin-top:10px;color:#777;">No feedback found</p></div>');
                    } else {
                        $('#feedbackList').append('<div class="text-center text-muted" style="padding:15px;">No more feedback available</div>');
                    }
                    t.feedbackHasMore = false;
                    return;
                }
                let html = '';
                data.data.forEach(f => {
                    let avatar = (f.avatar && f.avatar !== 'null') ? t.config.base_url + '/' + f.avatar : t.config.default_image;
                    let stars = '';
                    for (let i = 1; i <= 5; i++) {
                        stars += i <= f.feedback ? '<i class="bi bi-star-fill text-warning"></i>': '<i class="bi bi-star text-warning"></i>';
                    }
                    html += `
                        <div class="lbd-ai-card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-center">
                                        <img src="${avatar}" alt="Customer" class="rounded-circle me-3" width="40" height="40" onerror="this.onerror=null;this.src='${t.config.default_image}';">
                                        <div>
                                            <h6 class="mb-0 fw-semibold">
                                                <a href="${t.config.url.user_info}/${t.currentTechId}" target="_blank" class="text-decoration-none text-dark">
                                                    ${f.username ?? 'N/A'}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                #${f.ticket_id ?? f.id}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="d-flex align-items-center gap-1">
                                            ${stars}
                                            <span class="role-badge role-badge-user ms-1">
                                                ${Number(f.feedback).toFixed(1)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <p class="lbd-ai-body mt-2">
                                    ${f.remarks ?? 'No remarks available'}
                                </p>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    ${f.updated_at_format}
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#feedbackList').append(html);
                t.feedbackPage++;
            },
            error: function () {
                $('#tabLoader').hide();
                t.feedbackLoading = false;
                $('#feedbackList').html('<div class="text-center text-danger" style="padding:40px;">Failed to load feedback.</div>');
            }
        });
    };

    $('#feedbackList').on('scroll', function () {
        if (this.scrollTop + this.clientHeight >= this.scrollHeight - 20) t.loadFeedback();
    });

    // ---------- Tab data loading ----------
    t.loadTabData = function (techId, tab) {
        if (!techId || !tab) return;
        let filters = encodeURIComponent(JSON.stringify(t.config.other_filters));
        $('#tabLoader').show();
        if (tab === 'tickets') {
            $('#tab_tickets .chart-container, #tab_tickets .ticket-counters').hide();
            $.ajax({
                type: 'GET', url: t.config.url.leaderboard_tickets + '/' + techId,
                data: { filters: t.config.other_filters, page: t.currentPage ?? 1, size: t.pageSize ?? 10 },
                success: function (data) {
                    $('#tabLoader').hide();
                    if (!data) {
                        $('#tab_tickets').html('<p class="text-center">No data found.</p>');
                        return;
                    }
                    $('#ticket_total').html('<a href="' + t.config.url.ticket_list +'/all-tickets?tech_id=' + techId +'&filter=' + filters +'" target="_blank">' + (data.total ?? 0) + '</a>');
                    $('#ticket_resolved').html('<a href="' + t.config.url.ticket_list +'/resolved-ticket?tech_id=' + techId +'&filter=' + filters +'" target="_blank">' + (data.resolved ?? 0) + '</a>');
                    $('#ticket_pending').html('<a href="' + t.config.url.ticket_list +'/closed?tech_id=' + techId +'&filter=' + filters +'" target="_blank">' + (data.closed ?? 0) + '</a>');
                    $('#ticket_open').html('<a href="' + t.config.url.ticket_list +'/open-ticket?tech_id=' + techId +'&filter=' + filters +'" target="_blank">' + (data.open ?? 0) + '</a>');
                    if (t.ticketChart) {
                        t.ticketChart.destroy();
                    }
                    var options = {
                        chart: {
                            type: 'donut',
                            height: 180,
                        },
                        series: [
                            data.resolved ?? 0,
                            data.closed ?? 0,
                            data.open ?? 0
                        ],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '40%',
                                }
                            }
                        },
                                
                        labels: ['Resolved', 'Closed', 'Open'],    
                        colors: [
                            '#28a745', 
                            '#fd142f', 
                            '#3578dc'  
                        ],

                        legend: {
                            position: 'bottom',
                            fontSize: '11px'
                        },

                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    height: 250
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };
                    t.ticketChart = new ApexCharts(
                        document.querySelector("#ticketChart"),
                        options
                    );
                    t.ticketChart.render();
                    $('#tab_tickets .chart-container, #tab_tickets .ticket-counters').fadeIn();
                },
                error: function () {
                    $('#tabLoader').hide();
                    $('#tab_tickets').html(
                        '<p class="text-center text-danger">Failed to load tickets.</p>'
                    );
                }
            });
        } else if (tab === 'escalations') {
            t.initEscalationTable(techId);
            $('#tabLoader').hide();
        } else if (tab === 'feedback') {
            $('#feedbackList').empty();
            t.feedbackPage = 1; t.feedbackHasMore = true; t.feedbackLoading = false;
            t.currentTechId = techId;
            t.loadFeedback();
            $('#tabLoader').hide();
        } else if (tab === 'sla_breache') {
            t.initSlaTable(techId);
            $('#tabLoader').hide();
        } else if (tab === 'handler') {
            t.handlerWise(techId);
            $('#tabLoader').hide();
        }
    };

    // ---------- DataTable for Escalations ----------
    t.initEscalationTable = function (techId) {
        if (t.escTbl) {
            t.escTbl.destroy();
            t.escalationTable.find('tbody').empty();
        }
        t.escTbl = t.escalationTable.DataTable({
            autoWidth: false,
            aoColumnDefs: [{ targets: 0, bSortable: true }],
            order: [[3, "desc"]],
            processing: true,
            pageLength: parseInt($('#tab_escalations-page-length').val(), 10),
            lengthChange: false,
            serverSide: true,
            dom:'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            ajax: {
                url: t.config.url.leaderboard_escalations + '/' + techId,
                type: "GET",
                data: function (d) {
                    d.filters = t.config.other_filters || {};
                    d.main_filter = t.config.main_filter || null;
                }
            },
            columns: [
                { data: "a.id", render: d => `<a href="${t.config.url.ticket_info}/${d}" target="_blank" style="color:blue;">#${d}</a>` },
                { data: "a.subject" },
                { data: "a.status_name" },
                { data: "a.created_at_format" },
                { data: "a.updated_at_format" }
            ]
        });

        $('#tab_escalations-page-length').off('change').on('change', function () {
            let len = parseInt($(this).val(), 10);
            t.escTbl.page.len(len).draw(false);
            let select2 = $(this).data('select2');
            if (select2) {
                select2.$container.find('.select2-selection__rendered').text($(this).find('option:selected').text());
            }
        });
        var searchInput = $('.tab_escalations-list-search');
        searchInput.off('keyup').on('keyup', function (e) {
            if (e.key === 'Enter') t.escTbl.search($(this).val()).draw();
        });
        $('.tab_escalations-btn-reload').off('click').on('click', function () {
            searchInput.val('');
            t.escTbl.search('').draw();
        });
    };

    // ---------- DataTable for SLA Breached ----------
    t.initSlaTable = function (techId) {
        if (t.slaTbl) {
            t.slaTbl.destroy();
            t.slaTable.find('tbody').empty();
        }
        t.slaTbl = t.slaTable.DataTable({
            autoWidth: false,
            aoColumnDefs: [{ targets: 0, bSortable: true }],
            order: [[3, "desc"]],
            processing: true,
            pageLength: parseInt($('#sla_breache-page-length').val(), 10),
            lengthChange: false,
            serverSide: true,
            dom:'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            ajax: {
                url: t.config.url.leaderboard_sla_table + '/' + techId,
                type: "GET",
                data: function (d) {
                    d.filters = t.config.other_filters || {};
                    d.main_filter = t.config.main_filter || null;
                }
            },
            columns: [
                { data: "a.id", render: d => `<a href="${t.config.url.ticket_info}/${d}" target="_blank" style="color:blue;">#${d}</a>` },
                { data: "a.subject" },
                { data: "a.status_name" },
                { data: "a.created_at_format" },
                { data: "a.updated_at_format" }
            ]
        });

        $('#sla_breache-page-length').off('change').on('change', function () {
            let len = parseInt($(this).val(), 10);
            t.slaTbl.page.len(len).draw(false);
            let select2 = $(this).data('select2');
            if (select2) {
                select2.$container.find('.select2-selection__rendered').text($(this).find('option:selected').text());
            }
        });
        var searchInput = $('.sla_breache-list-search');
        searchInput.off('keyup').on('keyup', function (e) {
            if (e.key === 'Enter') t.slaTbl.search($(this).val()).draw();
        });
        $('.sla_breache-btn-reload').off('click').on('click', function () {
            searchInput.val('');
            t.slaTbl.search('').draw();
        });
    };

    // ---------- Handler‑wise (Overall) ----------
    t.shimmerRequests = [];
    t.abortShimmerRequests = function () { if (t.shimmerRequests.length) t.shimmerRequests.forEach(req => req.abort && req.abort()); t.shimmerRequests = []; };
    t.renderVerticalMatrix = function (rowData) {
        let html = `<tr><td colspan="100%"><div class="vertical-matrix">`;
        $.each(rowData, function (key, val) {
            if (key === "id") return;
            let label = key, value = val ?? "";
            if (key === "full_name") label = "Handler Name";
            else if (key === "tot") label = "Assigned Ticket";
            else if (key === "handler_tot_count") label = "Handled Ticket";
            else if (key === "SlaBreached") label = "SLA Breached";
            else if (key == "username") label = "Username";
            else {
                let split = key.split("_");
                if (split[0] === "AverageResponseTime") label = "Average Response (Hr)";
                else if (split[0] === "AverageResolutionTime") label = "Average Resolution (Hr)";
                else if (split[0] === "OpenTicketCount") label = "Pending Ticket (If not Resolved & closed)";
                else label = split[0];
            }
            if (key === "AverageResponseTime") {
                label = "Average Response Time";
                value = `<span class="avg-response-cell shimmer" data-id="${rowData.id}">Loading...</span>`;
            } else if (key === "AverageResolutionTime") {
                label = 'Average Resolution Time';
                value = `<span class="avg-response-cell-last-rstime shimmer" data-id="${rowData.id}">Loading...</span>`;
            } else if (key === "LastFBComment") {
                label = 'Last Feedback Comment';
                value = `<span class="avg-response-cell-lastfbc shimmer" data-id="${rowData.id}">Loading...</span>`;
            } else if (key === "LastFeedback") {
                label = 'Last Feedback';
                value = `<span class="avg-response-cell-lastfb shimmer" data-id="${rowData.id}">Loading...</span>`;
            } else if (key === "FeedbackCount") {
                label = "Feedback Count";
            } else if (key === "openMoreThan7DaysTickets") {
                label = 'open More Than 7 Days Tickets';
            } else if (key === "ClosedMoreThan7DaysTickets") {
                label = 'Closed More Than 7 Days Tickets';
            } else {
                let url = "", split = key.split("_");
                if (key === "tot") url = baseURL + "/reports/tickets/info/hand-tic-info?assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "handler_tot_count") url = baseURL + "/reports/tickets/info/hand-tic-info?action_type=1&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "username") url = baseURL + "/reports/tickets/info/hand-tic-info?assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "SlaBreached") url = baseURL + "/reports/tickets/info/hand-tic-info?action_type=2&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "OpenTicketCount") url = baseURL + "/reports/tickets/info/hand-tic-info?PendingTicketCount=true&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "openMoreThan7DaysTickets") url = baseURL + "/reports/tickets/info/hand-tic-info?openMoreThan7DaysTickets=true&status_id=1&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (key === "ClosedMoreThan7DaysTickets") url = baseURL + "/reports/tickets/info/hand-tic-info?ClosedMoreThan7DaysTickets=true&status_id=6&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                else if (split.length > 1) url = baseURL + "/reports/tickets/info/hand-tic-info?status_id=" + split[1] + "&assigned_to=" + rowData.id + "&handler_filters=" + t.config.export_filters;
                if (url) value = `<a href="${url}" target="_blank" class="underline">${value}</a>`;
            }
            html += `<div class="vm-cell vm-label">${label}</div><div class="vm-cell vm-value">${value}</div>`;
        });
        html += `</div></td></tr>`;
        return html;
    };

    t.handlerWise = function (techId) {
        t.abortShimmerRequests();
        var $cover = $("#handler_wise .gtable-cover"), $thead = $("#handlerHeaderRow"), $tbody = $("#handlerBody"), $summary = $("#handler-page-btm-summary");
        $cover.addClass("gload");
        var http = $.ajax({
            url: t.config.url.get_tickets,
            type: "POST",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            data: { filters: { ticket_handlers: [techId] } }
        });
        http.done(function (data) {
            if (!data || typeof data.total === "undefined") return;
            if (data.filtered != data.total) $summary.html("Available " + data.filtered + " records (filtered from " + data.total + " total records)");
            else $summary.html("Available Records: " + data.total);
            $tbody.empty();
            if (!data.data || !data.data.length) { $tbody.html('<tr class="no-record-found"><td colspan="21">No Handler Wise Data Found</td></tr>'); return; }
            $thead.empty();
            $.each(data.data, function (i, d) { $tbody.append(t.renderVerticalMatrix(d)); });
            $tbody.find("tr").each(function () {
                var $row = $(this);
                var userId = $row.find(".avg-response-cell").data("id");
                if (!userId) return;
                const req_shimmer = $.ajax({
                    url: t.config.urlBase+"reports/tickets/average-response-details",
                    type: "POST",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { tkt_handler: userId },
                    success: function (res) {
                        if (res) {
                            $row.find(".avg-response-cell").removeClass("shimmer").text(res.AverageResponseTime ?? 0);
                            $row.find(".avg-response-cell-last-rstime").removeClass("shimmer").text(res.AverageResolutionTime ?? 0);
                            $row.find(".avg-response-cell-lastfb").removeClass("shimmer").text(res.LastFeedback ?? 0);
                            $row.find(".avg-response-cell-lastfbc").removeClass("shimmer").text(res.LastFBComment ?? "N/A");
                        }
                    },
                    error: function () {
                        $row.find(".avg-response-cell, .avg-response-cell-last-rstime, .avg-response-cell-lastfb, .avg-response-cell-lastfbc")
                            .removeClass("shimmer").text("N/A");
                    }
                });
                t.shimmerRequests.push(req_shimmer);
            });
            if (data.filtered < 1) { var colCount = $thead.find("th").length || 21; $tbody.html('<tr class="no-record-found"><td colspan="' + colCount + '">No Handler Wise Data Found</td></tr>'); }
        });
        http.fail(function () { alert("Something went wrong. Please check given details are correct"); });
        http.always(function () { t.httpCall = true; $cover.removeClass("gload"); });
    };

    t.initTabs = function (techId) {
        if (!techId) return;
        const activeTab = $('.nav-link.active').attr('id');
        let tabName = 'tickets';
        if (activeTab === 'escalations-tab') tabName = 'escalations';
        else if (activeTab === 'feedback-tab') tabName = 'feedback';
        else if (activeTab === 'sla-tab') tabName = 'sla_breache';
        else if (activeTab === 'overall-tab') tabName = 'handler';
        t.loadTabData(techId, tabName);
    };

    $(document).on('click', '.nav-link[data-bs-toggle="tab"]', function (e) {
        e.preventDefault();
        const tab = $(this).attr('id');
        let tabName = 'tickets';
        if (tab === 'escalations-tab') tabName = 'escalations';
        else if (tab === 'feedback-tab') tabName = 'feedback';
        else if (tab === 'sla-tab') tabName = 'sla_breache';
        else if (tab === 'overall-tab') tabName = 'handler';
        const selectedTechId = $(this).attr('data-techid');
        if (selectedTechId) {
            if (tabName === 'handler') t.handlerWise(selectedTechId);
            else t.loadTabData(selectedTechId, tabName);
        }
        $(this).tab('show');
    });

    t.searchbox.on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchKeyword = $(this).val().trim();
            page = 1;
            hasMoreData = true;
            isFirstLoad = true;
            autoClickedFirstTech = false;
            t.techListEl.empty();
            $('#noTechFound').addClass('d-none');
            $('#techListLoader').show();
            t.loadTechList();
        }
    }); 
    t.ResetList = function (e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        page = 1; hasMoreData = true; isFirstLoad = true; autoClickedFirstTech = false;
        t.techListEl.empty();
        t.config.other_filters = {};
        t.config.export_filters = "";
        t.mdl.filter.based_on.val("").trigger("change");
        t.mdl.filter.department.val(null).trigger("change");
        t.mdl.filter.category.val(null).trigger("change");
        t.mdl.filter.subcategory.val(null).trigger("change");
        if (typeof resetDateRangeFilter === 'function') resetDateRangeFilter();
        t.mdl.filter.daterange.val('');
        t.loadTechList();
        $('#techListLoader').show();
        t.modal.modal("hide");
    };

    t.filterList = function (e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        page = 1; hasMoreData = true; isFirstLoad = true; autoClickedFirstTech = false;
        t.techListEl.empty();
        t.cache_filter_values();
        $('#techListLoader').show();
        t.loadTechList();
        t.modal.modal("hide");
    };

    t.printTechnicianList = function (e) {
        e.preventDefault();
        var tech_id = $(this).attr('data-techid');
        var rank_no = $(this).attr('data-rankno');
        window.open(t.config.url.print_tech_performance + '/' + tech_id + '?filter=' + t.config.export_filters + (rank_no ? '&rankno=' + rank_no : ''), '_blank');
    };

    t.shortList.on('click', function () {
        t.rankOrder = (t.rankOrder === 'asc') ? 'desc' : 'asc';
        t.shortList.find('i').toggleClass('fa-sort-amount-desc fa-sort-amount-asc');
        page = 1; hasMoreData = true; isFirstLoad = true; autoClickedFirstTech = false;
        t.techListEl.empty();
        t.loadTechList();
    });

    t.reafresLoadTechList = function () {
        $('#techListLoader').show();
        $('#noTechFound').addClass('d-none');
        $.ajax({
            type: "GET",
            url: t.config.url.leaderboardCalculate,
            success: function (res) {
                $('#techListLoader').hide();
                if (res && res.length > 0) {
                    $('#noTechFound').addClass('d-none');
                    t.ResetList();
                }
            },
            error: function () {
                $('#techListLoader').hide();
                $('#noTechFound').removeClass('d-none');
            }
        });
    };

    t.refreshList.on('click', $.proxy(t.reafresLoadTechList));
    t.printList.on('click', $.proxy(t.printTechnicianList));
    t.mdl.filter.apply.on('click', $.proxy(t.filterList));
    t.mdl.filter.reset.on('click', $.proxy(t.ResetList));
    t.techListEl.on('click', '.lbd-item', $.proxy(t.TechDetail));

    t.loadTechList();
};

