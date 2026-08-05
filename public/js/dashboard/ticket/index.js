var MyApp = function (config) {

    var t = this;

    t.config = config;
    t.content = $('#mainContent');
    t.showCardLoader = function () {

        if (!t.content.length) {
            return;
        }
        if (t.content.find(".loader-wrapper").length) {
            return;
        }

        t.content.css("position", "relative");

        var LoaderHtml = `
            <div class="loader-wrapper pt-5"
                style="
                    position:absolute;
                    top:0;
                    left:0;
                    right:0;
                    bottom:0;
                    z-index:999;
                    background:rgba(255,255,255,.7);
                    display:flex;
                    align-items:top;
                    justify-content:center;
                ">
                <div class="text-center p-5">
                    <svg id="kanban-board-loader" class="fa-spin" stroke="#dc2626" fill="#dc2626" width="80" height="80" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                            <style>
                                @keyframes spin {
                                    0% { transform: rotate(0deg); }
                                    100% { transform: rotate(360deg); }
                                }
                                .fa-spin {
                                    animation: spin 1s linear infinite;
                                }
                            </style>
                            <g>
                                <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                    c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                    c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                    c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                </path>
                            </g>
                        </svg>
                    <p style="margin-top:12px;color:#dc2626;font-size:14px;font-weight:500;"> Loading... </p>
                </div>
            </div>
        `;

        // Inject keyframe once
        if (!document.getElementById("amg-loader-style")) {
            $("head").append(`
                <style id="amg-loader-style">
                    @keyframes amgSpin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                </style>
            `);
        }

        t.content.append(LoaderHtml);
    };

    // Make the Redirection according to graph
    t.redirectToTicketList = function (params = {}, newTab = true) {
        const url = t.config.url.ticketList + '?' + $.param(params);
        if (newTab) {
            window.open(url, '_blank', 'noopener,noreferrer');
        } else {
            window.location.href = url;
        }
    };

    t.getDashboardRedirectParams = function (params, withDateRange = false) {
        let filters = t.getFilters();

        if (filters.department_id) {
            params.dashboard_department_id = filters.department_id;
        }

        if (withDateRange && filters.daterange) {
            params.dashboard_date_range = filters.daterange;
        }

        return params;
    };

    t.hideCardLoader = function () {
        t.content.find('.loader-wrapper').remove();
    };
    t.loadChart = function () {

        t.showCardLoader();

        $.when(
            t.loadDailyTicketOverview(),
            t.loadStatusTrend(),
            t.loadTrendAnalytics(),
            t.loadTicketSourceTrend(),
            t.loadFeedbackDashboard(),
            t.loadIssueTypeChart(),
            t.loadSlaPriority(),
            t.loadTopIssues(),
            t.loadLeaderboard(),
            t.loadTicketLocationMap()
        ).always(function () {

            t.hideCardLoader();

        });
    };
    t.loadDailyTicketOverview = function () {
        return $.ajax({
            url: t.config.url.dailyTicketOverview,
            type: 'GET',
            data: t.getFilters(),

            success: function(response) {

                var open = 0;
                var resolved = 0;
                var about = 0;
                var breached = 0;

                response.data.forEach(function(item) {

                    switch(item.category) {

                        case 'Open Tickets':
                            open = item.value;
                            break;

                        case 'Resolved Tickets':
                            resolved = item.value;
                            break;

                        case 'SLA About to Breach':
                            about = item.value;
                            break;

                        case 'SLA Breached':
                            breached = item.value;
                            break;
                    }
                });

                if (t.ticketChart) {
                    t.ticketChart.destroy();
                }

                t.ticketChart = new ApexCharts(
                    document.querySelector('#ticketOverviewChart'),
                    {
                        series: [open, resolved, about, breached],

                        chart: {
                            type: 'donut',
                            height: 180,
                            toolbar: {
                                show: false
                            },
                            events: {

                                mounted: function(chartContext) {
                                        $(chartContext.el).css('cursor', 'pointer');
                                    },

                                dataPointSelection: function (event, chartContext, config) {

                                    switch (config.dataPointIndex) {

                                        case 0:
                                            t.redirectToTicketList(t.getDashboardRedirectParams({
                                                dashboard_filter: 'daily_overview',
                                                dashboard_type: 'open'
                                            }));
                                            break;

                                        case 1:
                                            t.redirectToTicketList(t.getDashboardRedirectParams({
                                                dashboard_filter: 'daily_overview',
                                                dashboard_type: 'resolved'
                                            }));
                                            break;

                                        case 2:
                                            t.redirectToTicketList(t.getDashboardRedirectParams({
                                                dashboard_filter: 'daily_overview',
                                                dashboard_type: 'about'
                                            }));
                                            break;

                                        case 3:
                                            t.redirectToTicketList(t.getDashboardRedirectParams({
                                                dashboard_filter: 'daily_overview',
                                                dashboard_type: 'breached'
                                            }));
                                            break;
                                    }

                                }
                            }
                        },

                        labels: [
                            'Open Tickets',
                            'Resolved Tickets',
                            'SLA About to Breach',
                            'SLA Breached'
                        ],

                        colors: [
                            '#050b3c',
                            '#14b8a6',
                            '#f59e0b',
                            '#6366f1'
                        ],

                        legend: {
                            show: false
                        },

                        dataLabels: {
                            enabled: false
                        },

                        stroke: {
                            width: 0
                        },

                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '55%',
                                    labels: {
                                        show: true,

                                        value: {
                                            show: true,
                                            fontSize: '16px',
                                            fontWeight: 600,
                                            color:'#959595',
                                            offsetY: -4
                                        },

                                        total: {
                                            show: true,
                                            showAlways: true,
                                            label: 'Total',
                                            color:'#959595',
                                            fontSize: '12px',
                                            fontWeight: 400,
                                            offsetY: 4,
                                            formatter: function () {
                                                return response.total;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                );

                t.ticketChart.render();

                $('#slaPercentage .value').text(response.sla_percentage + '%');
                $('#avgResponseTime .value').text(response.avg_response_time);
                $('#openCount').text(open);
                $('#resolvedCount').text(resolved);
                $('#aboutCount').text(about);
                $('#breachedCount').text(breached);

                $('#openCount').attr('data-dashboard-type', 'open').addClass('dashboard-ticket-link');
                $('#resolvedCount').attr('data-dashboard-type', 'resolved').addClass('dashboard-ticket-link');
                $('#aboutCount').attr('data-dashboard-type', 'about').addClass('dashboard-ticket-link');
                $('#breachedCount').attr('data-dashboard-type', 'breached').addClass('dashboard-ticket-link');
            }
        });
    };

    t.loadStatusTrend = function () {
        return $.ajax({
            url: t.config.url.ticketStatusTrend,
            type: 'GET',
            data: t.getFilters(),

            success: function(response) {

                if (t.statusTrendChart) {
                    t.statusTrendChart.destroy();
                }

                var maxValue = Math.max(
                    ...response.open,
                    ...response.inprogress,
                    ...response.resolved,
                    ...response.hold
                );

                t.statusTrendChart = new ApexCharts(
                    document.querySelector('#ticketStatusTrendChart'),
                    {
                        series: [
                            {
                                name: 'Open',
                                data: response.open
                            },
                            {
                                name: 'Inprogress',
                                data: response.inprogress
                            },
                            {
                                name: 'Resolved',
                                data: response.resolved
                            },
                            {
                                name: 'On Hold',
                                data: response.hold
                            }
                        ],

                        chart: {
                            type: 'bar',
                            height: 180,
                            stacked: true,
                            toolbar: {
                                show: false
                            },
                           events: {
                                mounted: function(chartContext) {
                                    $(chartContext.el).css('cursor', 'pointer');
                                },

                                dataPointSelection: function (event, chartContext, config) {

                                    const statusMap = {
                                        0: 'open',
                                        1: 'inprogress',
                                        2: 'resolved',
                                        3: 'hold'
                                    };

                                    t.redirectToTicketList(t.getDashboardRedirectParams({
                                        dashboard_filter: 'status_trend',
                                        dashboard_status: statusMap[config.seriesIndex],
                                        dashboard_date: response.day_dates[config.dataPointIndex]
                                    }));

                                }
                            },
                        },

                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                columnWidth: '15%',
                                borderRadiusApplication: 'end'
                            }
                        },

                        colors: [
                            '#050b3c',
                            '#14b8a6',
                            '#6366f1',
                            '#f59e0b'
                        ],

                        dataLabels: {
                            enabled: false
                        },

                        legend: {
                            show: false
                        },

                        xaxis: {
                            categories: response.days,
                            axisBorder: { show: false },
                            axisTicks: { show: false },

                            labels: {
                                style: {
                                    fontSize: '11px',
                                    colors: '#959595'
                                }
                            }
                        },

                        yaxis: {
                            show: false,
                            min: 0,
                            max: maxValue,
                        },

                        grid: {
                            show: false,
                            padding: {
                                top: 0,
                                right: 0,
                                bottom: 0,
                                left: 0
                            }
                        }
                    }
                );

                t.statusTrendChart.render();

                $('#openTicketsTotal').text(
                    response.totals.open.toLocaleString()
                ).attr('data-dashboard-status', 'open').addClass('dashboard-status-link');

                $('#inprogressTicketsTotal').text(
                    response.totals.inprogress.toLocaleString()
                ).attr('data-dashboard-status', 'inprogress').addClass('dashboard-status-link');

                $('#resolvedTicketsTotal').text(
                    response.totals.resolved.toLocaleString()
                ).attr('data-dashboard-status', 'resolved').addClass('dashboard-status-link');

                $('#holdTicketsTotal').text(
                    response.totals.hold.toLocaleString()
                ).attr('data-dashboard-status', 'hold').addClass('dashboard-status-link');
            },

            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    };

    let trendChart = null;

    t.loadTrendAnalytics = function () {

        return $.ajax({
            url: t.config.url.ticketTrendAnalytics,
            type: 'GET',
            data: t.getFilters(),

            success: function (res) {

                const options = {

                    chart: {
                        type: 'bar',
                        height: 130,
                        toolbar: {
                            show: false
                        },
                        events: {

                            mounted: function(chartContext) {
                                    $(chartContext.el).css('cursor', 'pointer');
                                },            
                            dataPointSelection: function (event, chartContext, config) {
                                const category = res.categories[config.dataPointIndex];
                                t.redirectToTicketList(t.getDashboardRedirectParams({
                                    dashboard_filter: 'trend_analytics',
                                    dashboard_type: category.toLowerCase()
                                }));
                            }
                        }
                    },

                    series: [{
                        name:'Count',
                        data: res.series
                    }],

                    xaxis: {
                        categories: res.categories,
                        labels: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },

                    yaxis: {
                        show: false
                    },

                    grid: {
                        show: false
                    },

                    legend: {
                        show: false
                    },

                    tooltip: {
                        enabled: true
                    },

                    dataLabels: {
                        enabled: true,
                        offsetY: -1,
                        style: {
                            fontSize: '13px',
                            fontWeight: 600,
                            colors: ['#111827']
                        }
                    },

                    colors: [
                        '#b7cce6',
                        '#83a9d8',
                        '#4d86d1',
                        '#0f5dc2'
                    ],

                    plotOptions: {
                        bar: {
                            distributed: true,
                            columnWidth: '90%',
                            borderRadius: 2,
                            dataLabels: {
                                position: 'top'
                            }, 
                            states: {
                                hover: {
                                    enabled: false,
                                }
                            }
                        }
                    }
                };

                if (trendChart && typeof trendChart.destroy === 'function') {
                    trendChart.destroy();
                }

                trendChart = new ApexCharts(
                    document.querySelector("#trendChart"),
                    options
                );

                trendChart.render();

                $('#trendAnalyticsStats').html(`
                    <div class="d-flex align-items-center justify-content-between px-3 py-1">
                        <span style="color:#7F7F7F" class="b7-text">Avg Response Time</span>
                        <span class="b7-text fw-bold dashboard-trend-link" data-dashboard-type="response">${res.labels['Avg Response Time']}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between px-3 py-1">
                        <span style="color:#7F7F7F" class="b7-text">Avg Resolution Time</span>
                        <span class="b7-text fw-bold dashboard-trend-link" data-dashboard-type="resolution">${res.labels['Avg Resolution Time']}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between px-3 py-1">
                        <span style="color:#7F7F7F" class="b7-text">Ticket Volume</span>
                        <span class="b7-text fw-bold dashboard-trend-link" data-dashboard-type="volume">${res.labels['Ticket Volume']}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between px-3 py-1">
                        <span style="color:#7F7F7F" class="b7-text">SLA Breached</span>
                        <span class="b7-text fw-bold dashboard-trend-link" data-dashboard-type="sla">${res.labels['SLA Breached']}</span>
                    </div>
                `);
            }
        });
    };

    t.loadTicketSourceTrend = function () {
        return $.ajax({
            url: t.config.url.ticketSourceTrend,
            type: "GET",
            data: t.getFilters(),
            success: function (res) {

                let labels = res.labels;

                let categories = [];
                let series = [];
                let colors = [];

                let total = 0;

                labels.forEach(v => {
                    categories.push(v.label);
                    series.push(v.count);
                    colors.push(v.color);
                    total += v.count;
                });
                let html = "";
                labels.forEach(v => {
                    html += `
                        <div class="d-flex align-items-center py-1 dashboard-source-link" data-id="${v.id}" style="cursor:pointer;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="amg-dashboard-legend" style="background:${v.color};"></span>
                                <span class="b7-text fw-semibold">${v.count} :</span>
                            </div>
                            <span style="color:#7F7F7F" class="b7-text">&nbsp;&nbsp;${v.label}</span>
                        </div>
                    `;
                });

                $("#sourceList").html(html);

                
                if (t.sourceChartInstance) {
                    t.sourceChartInstance.destroy();
                }

                
                let options = {
                    chart: {
                        type: 'donut',
                        height: 120,
                        width: 120,
                        events: {

                            mounted: function(chartContext) {
                                    $(chartContext.el).css('cursor', 'pointer');
                                },
                            dataPointSelection: function (event, chartContext, config) {
                                let source = labels[config.dataPointIndex];
                                t.redirectToTicketList(t.getDashboardRedirectParams({
                                    dashboard_source_id: source.id
                                }, true));
                            }
                        }
                    },
                    series: series,
                    labels: categories,
                    colors: colors,
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '50%',
                                labels: {
                                    show: false
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " tickets";
                            }
                        }
                    }
                };

                t.sourceChartInstance = new ApexCharts(
                    document.querySelector("#sourceChart"),
                    options
                );

                t.sourceChartInstance.render();
            }
        });
    };

    t.loadFeedbackDashboard = function () {

        return $.ajax({
            url: config.url.ticketFeedback,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                // ================= POSITIVE =================
                let posHtml = "";

                if (res.positive && res.positive.length > 0) {

                    res.positive.forEach(v => {

                        let full = "★★★★★".slice(0, v.rating);
                        let empty = "★★★★★".slice(0, 5 - v.rating);

                        // 👉 PROFILE / INITIALS
                         let avatar = v.profile
                        ? `<img src="${v.profile}" class="fb-av-img">`
                        : `<span class="fb-av-initials" style="background-color:${getAvatarColor(v.name)};">${v.initials ?? ''}</span>`;

                        posHtml += `
                            <div class="d-flex align-items-start gap-2 mb-3 border-bottom pb-3 fb-item">
                                <div class="fb-av rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center mt-1">
                                    ${avatar}
                                </div>

                                <div>
                                    <div class="fb-stars">
                                        <span>${full}</span>${empty}
                                    </div>

                                    <div class="b5-text lh-base">${v.remarks ?? ''}</div>

                                    <div class="b5-text lh-base" style="color:#7F7F7F">
                                        ${v.name ?? ''}
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                } else {
                    posHtml = `<div style="color:#7F7F7F" class="b7-text text-center">No Positive Feedback Found</div>`;
                }

                $("#positiveFeedback").html(posHtml);

                // ================= NEGATIVE =================
                let negHtml = "";

                if (res.negative && res.negative.length > 0) {

                    res.negative.forEach(v => {

                        let rating = v.rating || 0;

                        let full = "★★★★★".slice(0, rating);
                        let empty = "★★★★★".slice(0, 5 - rating);

                        // 👉 PROFILE / INITIALS
                       let avatar = v.profile
                        ? `<img src="${v.profile}" class="fb-av-img">`
                        : `<span class="fb-av-initials" style="background-color:${getAvatarColor(v.name)};">${v.initials ?? ''}</span>`;

                        negHtml += `
                            <div class="d-flex align-items-start gap-2 mb-3 border-bottom pb-3 fb-item">
                                <div class="fb-av rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center mt-1">
                                    ${avatar}
                                </div>

                                <div>
                                    <div class="fb-stars">
                                        <span>${full}</span>${empty}
                                    </div>

                                    <div class="b5-text lh-base">${v.remarks ?? ''}</div>

                                    <div class="b5-text lh-base" style="color:#7F7F7F">
                                        ${v.name ?? ''} 
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                } else {
                    negHtml = `<div style="color:#7F7F7F" class="b7-text text-center">No Negative Feedback Found</div>`;
                }

                $("#negativeFeedback").html(negHtml);
            },

            error: function () {
                $("#positiveFeedback").html(`<div style="color:#7F7F7F" class="b7-text text-center">Error loading data</div>`);
                $("#negativeFeedback").html(`<div style="color:#7F7F7F" class="b7-text text-center">Error loading data</div>`);
            }
        });
    };
    t.getAvatarColor = function (name) {
        const colors = ["#6C63FF", "#F5B971", "#5FE0D0", "#FF6B6B", "#4D96FF", "#9B59B6"];
        if (!name) return colors[0];

        let hash = 0;
        for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
        }

        return colors[Math.abs(hash) % colors.length];
    }

    const colorPalette = [
    "#07427A",
    "#99EBE2",
    "#FFCD85",
    "#6175DE",
    "#ef4444",
    "#10b981",
    "#8b5cf6",
    "#f97316",
    "#22c55e",
    "#eab308",
    "#64748b",
    "#ec4899"
    ];

    function getColor(index, label) {

    // First 12 colors fixed
    if (index < colorPalette.length) {
        return colorPalette[index];
    }

    // Additional labels => auto generate color
    let hash = 0;

    for (let i = 0; i < label.length; i++) {
        hash = label.charCodeAt(i) + ((hash << 5) - hash);
    }

    const hue = Math.abs(hash % 360);

    return `hsl(${hue}, 70%, 50%)`;
    }

    t.loadIssueTypeChart = function () {
        return $.ajax({
            url: t.config.url.issueType,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                let html = '';
                let colors = [];

                res.labels.forEach((label, i) => {

                    let color = getColor(i, label);

                    colors.push(color);

                    html += `
                        <div class="d-flex align-items-center justify-content-between dashboard-issue-link cursor-pointer" data-id="${res.ids[i]}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="amg-dashboard-legend" style="background:${color}"></span>
                                <span style="color:#7F7F7F" class="b7-text"> ${label}</span>
                            </div>
                            <span class="b7-text fw-bold">${res.series[i]}</span>
                        </div>
                    `;
                });

                $("#issueList").html(html);

                if (t.issueChart) {
                    t.issueChart.destroy();
                }

                let options = {
                    chart: {
                        type: 'donut',
                        height: 150,
                        width: 130,
                        events: {

                            mounted: function(chartContext) {
                                    $(chartContext.el).css('cursor', 'pointer');
                                },

                            dataPointSelection: function (event, chartContext, config) {
                                t.redirectToTicketList(t.getDashboardRedirectParams({
                                    dashboard_ticket_type: res.ids[config.dataPointIndex]
                                }, true));
                            }
                        }
                    },

                    series: res.series,
                    labels: res.labels,
                    colors: colors,

                    legend: {
                        show: false
                    },

                    dataLabels: {
                        enabled: false
                    },

                    stroke: {
                        width: 0
                    },

                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val;
                            }
                        }
                    },

                    plotOptions: {
                        pie: {
                            donut: {
                                size: '55%',

                                labels: {
                                        show: true,

                                        value: {
                                            show: true,
                                            fontSize: '16px',
                                            fontWeight: 600,
                                            color:'#959595',
                                            offsetY: -4
                                        },

                                        total: {
                                            show: true,
                                            showAlways: true,
                                            label: 'Total',
                                            fontSize: '12px',
                                            fontWeight: 400,
                                            color:'#959595',
                                            offsetY: 4,
                                            formatter: function () {
                                                return res.total;
                                            }
                                        }
                                    }
                            }
                        }
                    }
                };

                t.issueChart = new ApexCharts(
                    document.querySelector("#issueChart"),
                    options
                );

                t.issueChart.render();
            },

            error: function () {

                $("#issueList").html(
                    '<div class="text-center text-muted">No Data Found</div>'
                );

                if (t.issueChart) {
                    t.issueChart.destroy();
                }
            }
        });
    };

    t.loadSlaPriority = function () {

        const priorityColors = [
            "#ef4444",
            "#f59e0b",
            "#14b8a6",
            "#6366f1",
        ];

        return $.ajax({
            url:config.url.getSlaPriority,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                $(".sla-n").html(`
                    <span class="b5-text fw-medium">${res.total_breaching} Tickets</span>
                    <span class="b5-text fw-medium" style="color:#ef4444 !important">↑</span>
                `).addClass("dashboard-sla-link").attr("data-dashboard-type", "breaching");

                $(".sla-s").html(`
                    <span class="dashboard-sla-link b7-text" data-dashboard-type="breaching" style="color:#7F7F7F" >Breaching in next ${res.next_hours} hours</span>
                    <span class="ms-auto text-end dashboard-sla-link b7-text" data-dashboard-type="breached" style="color:#7F7F7F">${res.breached_count} Tickets Breached</span>
                `);

                let html = '';

                res.priorities.forEach(function (row, index) {

                    let color = priorityColors[index % priorityColors.length];

                      html += `
                        <div class="my-3 dashboard-priority-link" data-id="${row.id}" style="cursor:pointer;">
                            <div class="d-flex justify-content-between opacity-80 mb-1">
                                <span class="b7-text" >${row.label}</span>
                                <span class="b7-text" >${row.count}</span>
                            </div>

                            <div class="amg-progress-bar rounded-0 overflow-hidden opacity-80">
                                <div class="h-100 rounded-0"
                                    style="
                                        background:${color};
                                        width:${row.percentage}%;
                                    ">
                                </div>
                            </div>
                        </div>
                    `;
                });

                $("#slaPriorityContainer").html(html);

                if (t.slaTrendChart) {
                    t.slaTrendChart.destroy();
                }

                let chartColor =
                    res.total_breaching > 20 ? '#ef4444' :
                    res.total_breaching > 10 ? '#f59e0b' :
                    '#14b8a6';

                t.slaTrendChart = new ApexCharts(
                    document.querySelector("#slaTrendChart"),
                    {
                        chart: {
                            type: "line",
                            height: 70,
                            toolbar: {
                                show: false
                            },
                            sparkline: {
                                enabled: true
                            },
                            events: {
                                dataPointSelection: function (event, chartContext, config) {
                                    t.redirectToTicketList(t.getDashboardRedirectParams({
                                        dashboard_filter: 'sla_priority',
                                        dashboard_type: 'breaching',
                                        dashboard_date: res.day_dates[config.dataPointIndex]
                                    }, true));
                                }
                            }
                        },

                        series: [{
                            name: "Tickets",
                            data: res.trend
                        }],

                        stroke: {
                            curve: "smooth",
                            width: 1
                        },

                        colors: [chartColor],

                        markers: {
                            size: 0,
                            hover: {
                                size: 6
                            }
                        },

                        tooltip: {
                            theme:
                                document.documentElement.getAttribute("data-bs-theme") === "dark"
                                    ? "dark"
                                    : "light"
                        },

                        grid: {
                            show: false
                        },

                        xaxis: {
                            categories: res.days
                        },

                        yaxis: {
                            show: false
                        }
                    }
                );

                t.slaTrendChart.render();
            }
        });
    };

    t.loadTopIssues = function () {
        return $.ajax({
            url: t.config.url.getTopIssue,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                let html = '';

                if (res.issues.length) {

                    res.issues.forEach(function (item) {

                        html += `
                        <span class="itag issue-tag" data-id="${item.id}" style="cursor:pointer;">
                                ${item.name}
                            </span>
                        `;
                    });

                } else {

                    html = `
                        <span style="color:#7F7F7F" class="b7-text">No Issues Found</span>
                    `;
                }

                $("#topIssuesContainer").html(html);
            }
        });
    };

    t.loadLeaderboard = function () {
        return $.ajax({
            url: t.config.url.getTechnicianLeaderboard,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                let html = '';

                if (res.data.length) {

                    res.data.forEach(function (row) {

                        let avatar = row.avatar
                            ? row.avatar
                            : '/images/default-avatar.png';

                        html += `
                            <div class="tech-card">

                                <img class="avatar"
                                    src="${avatar}"
                                    alt="${row.name}">

                                <div class="b5-text fw-medium my-1">
                                    ${row.name}
                                </div>

                                <div style="color:#7F7F7F" class="b5-text fw-normal my-1">
                                    ${String(row.rank).padStart(2,'0')} Place
                                </div>

                                <div class="mb-2 b5-text fw-normal my-1">
                                    ${row.ticketsResolved} Tickets Resolved
                                </div>

                                <div class="sla-badge b7-text fw-medium">
                                    Score ${row.score}%
                                </div>

                            </div>
                        `;
                    });

                } else {

                    html = `
                        <div class="text-center text-muted w-100">
                            No Technician Found
                        </div>
                    `;
                }

                $("#leaderboardContainer").html(html);
            }
        });
    };

    $(document).on("click", ".issue-tag", function () {
        let tagId = $(this).data("id");
        t.redirectToTicketList(t.getDashboardRedirectParams({
            tag_id: tagId
        }, true));
    });

    $(document).on("click", ".dashboard-ticket-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_filter: 'daily_overview',
            dashboard_type: $(this).data("dashboard-type")
        }));
    });

    $(document).on("click", ".dashboard-status-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_filter: 'status_trend',
            dashboard_status: $(this).data("dashboard-status")
        }));
    });

    $(document).on("click", ".dashboard-trend-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_filter: 'trend_analytics',
            dashboard_type: $(this).data("dashboard-type")
        }));
    });

    $(document).on("click", ".dashboard-source-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_source_id: $(this).data("id")
        }, true));
    });

    $(document).on("click", ".dashboard-issue-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_ticket_type: $(this).data("id")
        }, true));
    });

    $(document).on("click", ".dashboard-priority-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_priority_id: $(this).data("id")
        }, true));
    });

    $(document).on("click", ".dashboard-sla-link", function () {
        t.redirectToTicketList(t.getDashboardRedirectParams({
            dashboard_filter: 'sla_priority',
            dashboard_type: $(this).data("dashboard-type")
        }, true));
    });

    t.loadTicketLocationMap = function () {
        return $.ajax({
            url: t.config.url.getTicketByPlaces,
            type: "GET",
            data: t.getFilters(),

            success: function (res) {

                if (window.ticketMapRoot) {
                    window.ticketMapRoot.dispose();
                }

                let data = res.data.map(item => ({
                    id: item.city_id,
                    latitude: parseFloat(item.lat),
                    longitude: parseFloat(item.lng),
                    name: item.city_name,
                    value: parseInt(item.total, 10) || 0
                }));

                let root = am5.Root.new("ticketLocationMap");
                window.ticketMapRoot = root;
                if (root._logo) {
                    root._logo.dispose();
                }
                root.setThemes([
                    am5themes_Animated.new(root)
                ]);

                let chart = root.container.children.push(
                    am5map.MapChart.new(root, {})
                );
                let zoomControl = chart.set(
                    "zoomControl",
                    am5map.ZoomControl.new(root, {})
                );

                let polygonSeries = chart.series.push(
                    am5map.MapPolygonSeries.new(root, {
                        geoJSON: am5geodata_worldLow,
                        exclude: ["AQ"]
                    })
                );

                let bubbleSeries = chart.series.push(
                    am5map.MapPointSeries.new(root, {
                        valueField: "value",
                        calculateAggregates: true,
                        polygonIdField: "id"
                    })
                );

                let circleTemplate = am5.Template.new({});

                bubbleSeries.bullets.push(function(root, series, dataItem) {

                    let container = am5.Container.new(root, {
                        interactive: true,
                        cursorOverStyle: "pointer"
                    });
                    container.events.on("click", function () {
                        t.redirectToTicketList(t.getDashboardRedirectParams({
                            dashboard_city_id: dataItem.dataContext.id
                        }, true));
                    });

                    let circle = container.children.push(
                        am5.Circle.new(root, {
                            radius: 15,
                            fill: am5.color(0xff3b30),
                            fillOpacity: 0.7,
                            tooltipText: "{name}: {value}"
                        })
                    );
                    circle.animate({
                        key: "scale",
                        from: 1,
                        to: 1.4,
                        duration: 1000,      
                        loops: Infinity,
                        easing: am5.ease.out(am5.ease.cubic)
                    });

                    circle.animate({
                        key: "fillOpacity",
                        from: 0.8,
                        to: 0.3,
                        duration: 1000,
                        loops: Infinity,
                        easing: am5.ease.out(am5.ease.cubic)
                    });

                    let label = container.children.push(
                        am5.Label.new(root, {
                            text: "{value}",
                            populateText: true,   
                            centerX: am5.p50,
                            centerY: am5.p50,
                            fill: am5.color(0xffffff),
                            fontSize: 11,
                            cursorOverStyle: "pointer"
                        })
                    );
                    circle.on("radius", function(radius) {
                        label.set("x", radius);
                    });

                    return am5.Bullet.new(root, {
                        sprite: container
                    });
                });


                let maxValue = Math.max(
                    ...data.map(x => x.value),
                    1
                );

                bubbleSeries.set("heatRules", [{
                    target: circleTemplate,
                    dataField: "value",
                    min: 10,
                    max: 50,
                    minValue: 0,
                    maxValue: maxValue,
                    key: "radius"
                }]);

                bubbleSeries.data.setAll(data);

                chart.appear(1000, 100);
            }
        });
    };

    t.getFilters = function () {
        return {
            department_id: $('#departmentFilter').val() || '',
            daterange: $('#daterange').val() || ''
        };
    };

    $(document).on('change', '#departmentFilter', function () {
        t.loadChart();
    });

    $('#departmentFilter').select2({
        width: '170px'
    }).val();

    $('#range').daterangepicker({
        autoUpdateInput: false,
        autoApply: false,
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        timePickerIncrement: 1,
        opens: 'right',
        drops: 'down',

        locale: {
            format: 'DD-MM-YYYY HH:mm:ss',
            cancelLabel: 'Clear'
        },

        ranges: {
            'Today': [
                moment().startOf('day'),
                moment().endOf('day')
            ],
            'Yesterday': [
                moment().subtract(1, 'days').startOf('day'),
                moment().subtract(1, 'days').endOf('day')
            ],
            'Last 7 Days': [
                moment().subtract(6, 'days').startOf('day'),
                moment().endOf('day')
            ],
            'Last 30 Days': [
                moment().subtract(29, 'days').startOf('day'),
                moment().endOf('day')
            ],
            'This Month': [
                moment().startOf('month'),
                moment().endOf('month')
            ],
            'Last Month': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        }
    });

    $('#range span').text('Select Date Range');
    $('#daterange').val('');

    $('#range').on('apply.daterangepicker', function (ev, picker) {
        const displayValue =
            picker.startDate.format('DD-MM-YYYY HH:mm:ss') +
            ' - ' +
            picker.endDate.format('DD-MM-YYYY HH:mm:ss');

        const filterValue =
            picker.startDate.format('YYYY-MM-DD HH:mm:ss') +
            ' - ' +
            picker.endDate.format('YYYY-MM-DD HH:mm:ss');

        $('#range span').text(displayValue);
        $('#daterange').val(filterValue);

        t.loadChart();
    });

    $('#range').on('cancel.daterangepicker', function () {
        $('#range span').text('Select Date Range');
        $('#daterange').val('');

        t.loadChart();
    });
    $(document).on('click', '.clear-date', function(e){
        e.stopPropagation();
        $('#daterange').val('');
        $('#range span').text('Select Date Range');
        t.loadChart();
    });

    t.loadChart();
};