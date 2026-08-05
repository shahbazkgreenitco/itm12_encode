/* ===========================================================
   Ticket Handler Wise Report
   - Sort dropdown: checkbox style (single-select)
   - Filter modal: apply closes modal then loads
   - Search: Enter key triggers load with filters
   - Download: includes current search + filters
   =========================================================== */

var MyApp = function (config) {
    var t = this;
    t.content   = $("#content-container");
    t.deviceTab = new TableList(config, {
        tab : "#device-tab",
        url : config.url.get_tickets,
        sort_fields: config.sort_fields,
        tbl_fields : config.tbl_fields
    });
};

var TableList = function (config, otherConfig) {
    var t = this;
    t.config  = config;
    t.content = $("#content-container");
    t.tab = t.content.find(otherConfig.tab);
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pagebtns = t.tab.find("#pagebtns");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.searchbox = t.tab.find(".list-search");
    t.reloadBtn = t.tab.find(".btn-reload-list");
    t.exportBtn = t.content.find(".btn-download");

    /* Sort */
    t.sortbtns = t.content.find("#srqSortDrop");
    t.sortItems = t.content.find("#short_items");
    t.sortAction = t.sortbtns.find(".sort-action");
    t.dropdownAction = t.sortbtns.find(".dropdown-action");
    t.sortDirIcon = t.content.find("#sortDirectionIcon");

    /* Filter modal */
    t.filterModal = $("#advanceFilterModal");
    t.openFilterBtn = t.content.find(".btn-open-filter");

    t.filters = {
        ticket_handlers  : t.filterModal.find("#filter_by_ticket_handlers"),
        location_id : t.filterModal.find("#filter_location_id"),
        based_on : t.filterModal.find("#filter_by_date"),
        date_range : t.filterModal.find("#daterange"),
        days_more_than : t.filterModal.find("#more_days"),
        btnApply : t.filterModal.find(".btn-filter"),
        btnClear : t.filterModal.find(".btn-clear-filter"),
        filterCountBadge : t.content.find("#filter_count")
    };

    /* ─── Populate filter dropdowns ─── */
    t.filters.fun = {
        reload_ticket_handlers: function () {
            t.filters.ticket_handlers.empty();
            $.each(t.config.ticket_handlers, function (i, k) {
                t.filters.ticket_handlers.append(new Option(k.full_name, k.id));
            });
            t.filters.ticket_handlers.trigger("change");
        },
        reload_locations: function () {
            t.filters.location_id.empty();
            $.each(t.config.locations, function (i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        }
    };

    /* ─── Sort ─── */
    t.renderSortDropdown = function () {
        var $dropdown = t.sortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            var dirIcon = isActive ? (s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down') : '';
            // Truncate text if longer than 10 characters
            var displayText = d.text.length > 10 ? d.text.substring(0, 15) + '...' : d.text;
            $dropdown.append(`
                <li>
                    <a href="javascript:void(0)"
                    class="dropdown-item ${isActive ? 'active' : ''}"
                    data-id="${d.id}"
                    data-bs-toggle="tooltip"
                    title="${d.text}">
                        <span class="like-radio"></span>
                        <span class="flex-grow-1">${displayText}</span>
                        ${isActive ? `<i class="bi ${dirIcon}"></i>` : ''}
                    </a>
                </li>
            `);
        });
    };

    t.updateSortDirectionIcon = function () {
        t.sortDirIcon
            .removeClass("bi-sort-up bi-sort-down")
            .addClass(t.config.sort_dir.dir == 1 ? "bi-sort-up" : "bi-sort-down");
    };

    t.sortAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.updateSortDirectionIcon();
        t.renderSortDropdown();
        t.loadTicketList();
    });

    t.dropdownAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.sortItems.toggleClass("show");
    });

    t.sortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.id = $(this).data("id");
        t.sortItems.removeClass("show");
        t.renderSortDropdown();
        t.loadTicketList();
    });

    t.sortItems.on("click", function (e) {
        e.stopPropagation();
    });

    $(document).on("click", function () {
        t.sortItems.removeClass("show");
    });

    /* ─── Cache filter values ─── */
    t.cache_filter_values = function () {
        t.config.search = t.searchbox.val().trim();
        t.config.other_filters = {};
        var fo = t.config.other_filters;

        var basedOn  = t.filters.based_on.val();
        var dateRange = t.filters.date_range.val();
        var handlers  = t.filters.ticket_handlers.val();
        var days      = t.filters.days_more_than.val();
        var location  = t.filters.location_id.val();

        if (basedOn && basedOn !== "null")  fo.based_on   = basedOn;
        if ((basedOn && basedOn !== "null") && (dateRange && dateRange !== ""))  fo.date_range  = dateRange;
        if (handlers && handlers.length)    fo.ticket_handlers = handlers;
        if (days && days !== "")            fo.days_more_than  = days;
        if (location && location.length)    fo.location_id = location;

        var jobj = { search: t.config.search, other_filters: t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        // Pass based_on raw value so filterCount can handle its own decrement logic
        filterCount(t.config.other_filters, 'null', false);
    };

    /* ─── Search (Enter) ─── */
    t.search = function (e) {
        if (e.type === "keypress" && e.which !== 13) return;
        t.abortShimmerRequests();
        t.cache_filter_values();
        t.pagebtns.pagination("selectPage", 1);   // FIX: was "goToPage"
        t.loadTicketList();
    };
    t.searchbox.on("keypress", t.search);

    /* ─── Shimmer abort ─── */
    t.shimmerRequests = [];
    t.abortShimmerRequests = function () {
        $.each(t.shimmerRequests, function (i, req) {
            if (req && req.abort) req.abort();
        });
        t.shimmerRequests = [];
    };

    /* ─── Main load ─── */
    t.loadTicketList = function () {
        t.abortShimmerRequests();
        t.tableCover.addClass("gload");

        var http = $.ajax({
            url : otherConfig.url,
            type: "POST",
            data: {
                _token : t.config.token,
                search : t.config.search || "",
                page : t.pagebtns.pagination("getCurrentPage"),
                size : t.perPage,
                main_filter: t.config.main_filter || "",
                filters : t.config.other_filters || {},
                order : t.config.sort_dir,
                assigned_to: t.config.assigned_to  || "",
                status_id  : t.config.status_id    || ""
            }
        });

        http.done(function (data) {
            if (typeof data !== "object" || typeof data.total === "undefined") return;

            t.data = data;
            t.pagebtns.pagination("updateItems", data.filtered);
            t.pagebtns.pagination("drawPage", data.page);
            t.lg.empty();

            let startEntry = ((data.page - 1) * t.perPage) + 1;
            let endEntry = startEntry + data.data.length - 1;

            if (data.filtered === 0) {
                startEntry = 0;
                endEntry = 0;
            }

            if (data.filtered !== data.total) {
                t.pageBtmSummary.html(
                    "Showing " + startEntry + " to " + endEntry + " of " + data.filtered +
                    " entries (filtered from " + data.total + " total entries)"
                );
            } else {
                t.pageBtmSummary.html(
                    "Showing " + startEntry + " to " + endEntry + " of " + data.total + " entries"
                );
            }
            if (!data.data || data.filtered < 1) {
                var colCount = t.content.find("#tHeadeRow th").length || 1;
                t.lg.html(
                    '<tr class="no-record-found"><td colspan="' + colCount +
                    '">No Handler Wise Data Found</td></tr>'
                );
                return;
            }

            var firstRow = data.data[0];
            if (firstRow && Object.keys(firstRow).length) {
                var head = "";
                $.each(firstRow, function (key) {
                    if      (key === "id")                    head += "<th class='d-none'>ID</th>";
                    else if (key === "full_name")             head += "<th>Handler Name</th>";
                    else if (key === "username")              head += "<th class='text-center'>Username</th>";
                    else if (key === "tot")                   head += "<th>Assigned Ticket</th>";
                    else if (key === "handler_tot_count")     head += "<th>Handled Ticket</th>";
                    else if (key === "SlaBreached")           head += "<th>SLA Breached</th>";
                    else if (key === "AverageResponseTime")   head += "<th>Avg Response Time (Hr)</th>";
                    else if (key === "AverageResolutionTime") head += "<th>Avg Resolution Time (Hr)</th>";
                    else if (key === "OpenTicketCount")       head += "<th>Pending Ticket (not Resolved &amp; Closed)</th>";
                    else if (key === "AverageFeedback")       head += "<th>Avg Feedback</th>";
                    else if (key === "FeedbackCount")         head += "<th>Feedback Count</th>";
                    else if (key === "LastFeedback")          head += "<th>Last Feedback</th>";
                    else if (key === "LastFBComment")         head += "<th>Last FB Comment</th>";
                    else                                      head += "<th>" + key.split("_")[0] + "</th>";
                });
                t.content.find("#tHeadeRow").html(head);
            }

            // Build rows
            var ef = t.config.export_filters;
            $.each(data.data, function (i, d) {
                var fc = "<tr>";
                $.each(d, function (key, val) {
                    var safeVal = (val === null || val === undefined) ? "" : val;

                    if (key === "id") {
                        fc += "<td class='b5-text d-none'>" + safeVal + "</td>";
                    } else if (key === "full_name") {
                        fc += "<td class='b5-text'>" + safeVal + "</td>";
                    }else if (key === "username") {
                        fc += "<td class='b5-text'><a href='" + baseURL +
                              "/user/info/" + d.id +
                              "' target='_blank' class='underline'>" + safeVal + "</a></td>";
                    } else if (key === "tot") {
                        fc += "<td class='b5-text'><a href='" + baseURL +
                              "/reports/tickets/info/hand-tic-info?assigned_to=" + d.id +
                              "&handler_filters=" + ef +
                              "' target='_blank' class='underline'>" + t._s(val) + "</a></td>";
                    } else if (key === "handler_tot_count") {
                        fc += "<td class='b5-text'><a href='" + baseURL +
                              "/reports/tickets/info/hand-tic-info?action_type=1&assigned_to=" + d.id +
                              "&handler_filters=" + ef +
                              "' target='_blank' class='underline'>" + t._s(val) + "</a></td>";
                    } else if (key === "SlaBreached") {
                        fc += "<td class='b5-text'><a href='" + baseURL +
                              "/reports/tickets/info/hand-tic-info?action_type=2&assigned_to=" + d.id +
                              "&handler_filters=" + ef +
                              "' target='_blank' class='underline'>" + t._s(val) + "</a></td>";
                    } else if (key === "AverageResponseTime") {
                        fc += "<td class='b5-text avg-response-cell' data-id='" + d.id +
                              "'><span class='shimmer'>Loading...</span></td>";
                    } else if (key === "AverageResolutionTime") {
                        fc += "<td class='b5-text avg-response-cell-last-rstime' data-id='" + d.id +
                              "'><span class='shimmer'>Loading...</span></td>";
                    } else if (key === "LastFBComment") {
                        fc += "<td class='b5-text avg-response-cell-lastfbc' data-id='" + d.id +
                              "'><span class='shimmer'>Loading...</span></td>";
                    } else if (key === "LastFeedback") {
                        fc += "<td class='b5-text avg-response-cell-lastfb' data-id='" + d.id +
                              "'><span class='shimmer'>Loading...</span></td>";
                    } else if (key === "AverageFeedback" || key === "FeedbackCount") {
                        fc += "<td class='b5-text'>" + safeVal + "</td>";
                    } else {
                        // Status-based columns with drill-down
                        var url;
                        if (key === "OpenTicketCount") {
                            url = baseURL + "/reports/tickets/info/hand-tic-info?PendingTicketCount=true&assigned_to=" + d.id + "&handler_filters=" + ef;
                        } else if (key === "openMoreThan7DaysTickets") {
                            url = baseURL + "/reports/tickets/info/hand-tic-info?openMoreThan7DaysTickets=true&status_id=1&assigned_to=" + d.id + "&handler_filters=" + ef;
                        } else if (key === "ClosedMoreThan7DaysTickets") {
                            url = baseURL + "/reports/tickets/info/hand-tic-info?ClosedMoreThan7DaysTickets=true&status_id=6&assigned_to=" + d.id + "&handler_filters=" + ef;
                        } else {
                            var split = key.split("_");
                            url = baseURL + "/reports/tickets/info/hand-tic-info?status_id=" + (split[1] || "") + "&assigned_to=" + d.id + "&handler_filters=" + ef;
                        }
                        fc += "<td class='b5-text'><a href='" + url + "' target='_blank' class='underline'>" + t._s(val) + "</a></td>";
                    }
                });
                fc += "</tr>";
                t.lg.append(fc);
            });

            // Shimmer async – one AJAX per row
            t.lg.find("tr").each(function () {
                var $row = $(this);
                var userId = $row.find(".avg-response-cell").data("id");
                if (!userId) return;

                var req = $.ajax({
                    url : baseURL + "/reports/tickets/average-response-details",
                    type: "POST",
                    data: { _token: t.config.token, tkt_handler: userId },
                    success: function (res) {
                        if (res) {
                            $row.find(".avg-response-cell span.shimmer")
                                .removeClass("shimmer").text(res.AverageResponseTime  ?? 0);
                            $row.find(".avg-response-cell-last-rstime span.shimmer")
                                .removeClass("shimmer").text(res.AverageResolutionTime ?? 0);
                            $row.find(".avg-response-cell-lastfb span.shimmer")
                                .removeClass("shimmer").text(res.LastFeedback          ?? 0);
                            $row.find(".avg-response-cell-lastfbc span.shimmer")
                                .removeClass("shimmer");
                            const fullHtml = res.LastFBComment || "";
                            const plainText = $('<div>').html(fullHtml).text().trim() || " ";
                            const shortComment = plainText.length > 50
                                ? plainText.substring(0, 50) + "..."
                                : plainText;

                            const $span = $row.find(".avg-response-cell-lastfbc span");
                            if (plainText.length > 50) {
                                $span
                                    .html(
                                        shortComment +
                                        ' <a href="javascript:void(0);" class="read-more-comment text-primary">Read More</a>'
                                    )
                                    .attr("data-title", "Last FB Comment")
                                    .attr("data-content", encodeURIComponent(fullHtml));
                            } else {
                                $span
                                    .text(plainText)
                                    .attr("data-title", "Last FB Comment")
                                    .attr("data-content", encodeURIComponent(fullHtml));
                            }
                        }
                    },
                    error: function () {
                        $row.find(
                            ".avg-response-cell span.shimmer," +
                            ".avg-response-cell-last-rstime span.shimmer," +
                            ".avg-response-cell-lastfb span.shimmer," +
                            ".avg-response-cell-lastfbc span.shimmer"
                        ).removeClass("shimmer").text("N/A");
                    }
                });
                t.shimmerRequests.push(req);
            });
        });

        http.fail(function (xhr) {
            if (xhr.statusText !== "abort") {
                alert("Something went wrong. Please check given details are correct.");
            }
        });

        http.always(function () {
            t.httpCall = true;
            t.tableCover.removeClass("gload");
        });
    };


    $(document).on("click", ".read-more-comment", function (e) {
        e.preventDefault();
        const span = $(this).closest("span");
        const content = decodeURIComponent(span.attr("data-content") || "");
        $("#descriptionModal .modal-title").text(span.attr("data-title"));
        $("#descriptionModalBody").html(content);
        const modal = new bootstrap.Modal(document.getElementById("descriptionModal"));
        modal.show();
    });
    t._s = function (v) { return (v === 0 || v === "0") ? 0 : (v || ""); };

    t.applyFilters = function () {
        t.cache_filter_values();
        t.pagebtns.pagination("selectPage", 1);   // FIX: was "goToPage"
        t.filterModal.one("hidden.bs.modal", function () {
            t.loadTicketList();
        });
        t.filterModal.modal("hide");
    };

    t.clearFilters = function () {
        t.filters.based_on.val("null").trigger("change");
        t.filters.ticket_handlers.val([]).trigger("change");
        t.filters.location_id.val([]).trigger("change");
        t.filters.days_more_than.val("");
        t.filters.date_range.val("");

        if ($.fn.daterangepicker && $("#reportrange").data("daterangepicker")) {
            var picker = $("#reportrange").data("daterangepicker");
            picker.setStartDate(moment().startOf("month"));
            picker.setEndDate(moment().endOf("month"));
            $("#reportrange").find("span.flex-grow-1").text("");
        }

        t.cache_filter_values();
        t.pagebtns.pagination("selectPage", 1);   // FIX: was "goToPage"
        t.filterModal.one("hidden.bs.modal", function () {
            t.loadTicketList();
        });
        resetDateRangeFilter();
        t.filterModal.modal("hide");
    };

    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket + "?q=" + t.config.export_filters;
    };

    t.pageBtnClicked = function (n, e) {
        if (typeof e !== "undefined") e.preventDefault();
        t.loadTicketList();
    };

    t.init = function () {
        // Select2
        var s2Opts = { width: "100%", dropdownParent: t.filterModal };
        t.filters.based_on.select2($.extend({}, s2Opts, {
            placeholder: config.translations.Filter_Based_on || "Select"
        }));
        t.filters.ticket_handlers.select2($.extend({}, s2Opts, {
            placeholder: config.translations.Filter_By_Ticket_Handler || "Select Handler"
        }));
        t.filters.location_id.select2($.extend({}, s2Opts, {
            placeholder: config.translations.filter_by_location || "Select Location"
        }));

        // Populate
        t.filters.fun.reload_ticket_handlers();
        t.filters.fun.reload_locations();

        // Pre-select from URL
        if (t.config.assigned_to) {
            t.filters.ticket_handlers.val([t.config.assigned_to]).trigger("change");
        }

        // Pagination
        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            onPageClick: t.pageBtnClicked,
            prevText: "Previous", 
        });

        // Sort initial render
        t.renderSortDropdown();
        t.updateSortDirectionIcon();

        // Reload button
        t.reloadBtn.on("click", function () {
            t.cache_filter_values();
            t.loadTicketList();
        });

        // Export button
        t.exportBtn.on("click", t.download);

        // Page length
        t.pageLimiter.on("change", function () {
            t.perPage = parseInt($(this).val(), 10) || 10;
            t.pagebtns.pagination("updateItemsOnPage", t.perPage);
            t.pagebtns.pagination("selectPage", 1);   // FIX: was "goToPage"
            t.loadTicketList();
        });

        t.openFilterBtn.on("click", function () {
            t.filterModal.modal("show");
        });

        t.filters.btnApply.on("click", t.applyFilters);
        t.filters.btnClear.on("click", t.clearFilters);

        t.cache_filter_values();
        t.loadTicketList();
    };

    t.init();
};