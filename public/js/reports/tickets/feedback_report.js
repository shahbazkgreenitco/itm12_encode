
var FeedbackReport = function (config) {
    var t = this;
    t.config = config;
    t.tab = $(".content");
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.sortbtns = t.tab.find('#srqSortDrop');
    t.searchbox = t.tab.find("#announcement-list-search");
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.btnReload = t.tab.find(".btn-reload");
    t.btnDownload = t.tab.find(".btn-download");
    t.btnOpenFilter = t.tab.find(".btn-open-filter");
    t.btnApplyFilter = t.tab.find(".btn-filter");
    t.btnClrFilter = t.tab.find("#btnClrFilter");
    t.filterBadge = t.tab.find(".filter-count-badge");
    t.tableFooter = t.tab.find(".table-footer");
    t.filterModal = t.tab.find("#FilterModal");

    t.perPage = 10;
    t.currentPage = 1;
    t.totalRecords = 0;
    t.filteredRecords = 0;

    // Filters - sirf modal mein jo hain wahi
    t.filters = {
        data: { problem_categories: {} },
        department: t.tab.find("#filter_by_department"),
        problem_category: t.tab.find("#filter_by_problem_category"),
        sub_category: t.tab.find("#filter_by_sub_category"),
        priority: t.tab.find("#filter_by_priority"),
        handler: t.tab.find("#filter_by_handler"),
        location: t.tab.find("#filter_by_location"),
        ticket_type: t.tab.find("#filter_by_ticket_type"),
        based_on: t.tab.find("#filter_by_date"),
        daterange: t.tab.find("#daterange")
    };

    // ============ EMPTY CHECK ============
    t.isEmpty = function (val) {
        return val === null || val === "" || val === "null" ||
            (Array.isArray(val) && val.length === 0);
    };

    // ============ MODAL HELPERS ============
    t.openFilterModal = function () {
        var modalEl = t.filterModal[0];
        if (!modalEl) return;
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        } else if (typeof $.fn.modal !== "undefined") {
            t.filterModal.modal("show");
        }
    };

    t.closeFilterModal = function () {
        var modalEl = t.filterModal[0];
        if (!modalEl) return;
        if (typeof bootstrap !== "undefined" && bootstrap.Modal) {
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else if (typeof $.fn.modal !== "undefined") {
            t.filterModal.modal("hide");
        }
    };

    // ============ SORT ============
    t.renderSortFields = function () {
        var sf = t.sortbtns.find(".sort-fields");
        var s = t.config.sort_dir;
        var $dirSort = t.sortbtns.find(".dir-sort");

        $dirSort.find("#sortAscSvg").toggleClass("hide", s.dir != 1);
        $dirSort.find("#sortDescSvg").toggleClass("hide", s.dir != 2);
        $dirSort.attr({ "data-id": s.id, "data-dir": s.dir == 1 ? "asc" : "desc" });

        sf.empty();
        $.each(t.config.sort_fields, function (i, d) {
            $('<li class="list-group-item ' + (d.id == s.id ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text + '</li>').appendTo(sf);
        });
    };

    t.sortFieldChanged = function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var id = $(this).attr("data-id");
        var s = t.config.sort_dir;

        if (s.id == id) {
            s.dir = s.dir == 1 ? 2 : 1;
        } else {
            s.id = id;
            s.dir = 2;
        }

        t.renderSortFields();
        t.sortbtns.removeClass("open");

        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () { t.load(); }, 300);
    };

    // ============ FILTERS ============
    // Generic AJAX user loader (handler ke liye)
    t.loadUserSelect = function ($select, placeholder) {
        $select.select2({
            width: "100%",
            dropdownParent: t.filterModal,
            ajax: {
                url: config.getUserByQuery,
                dataType: "json",
                data: function (p) {
                    return { search: p.term, page: p.page || 1, company_id: config.company };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: placeholder
        });
    };

    // Generic data loader
    t.loadSelectData = function ($select, url, onSuccess) {
        $.get(url, function (data) {
            if (typeof data == "object" && data.data && data.data.length > 0) {
                $select.empty();
                $.each(data.data, function (i, k) {
                    $select.append(new Option(k.name, k.id, false, false));
                    if (onSuccess) onSuccess(k);
                });
                $select.trigger("change");
            }
        });
    };

    t.filters.fun = {
        reload_department: function () {
            t.loadSelectData(
                t.filters.department,
                config.url.departments_by_company + "/" + config.company
            );
        },
        reload_problem_category: function () {
            var department = t.filters.department.val();
            if (t.isEmpty(department)) return;

            t.filters.problem_category.empty();
            var deptArr = Array.isArray(department) ? department : [department];

            deptArr.forEach(function (dept) {
                t.loadSelectData(
                    t.filters.problem_category,
                    config.url.problem_categories_by_company + "/" + dept,
                    function (k) {
                        if (k.sub && Array.isArray(k.sub)) {
                            t.filters.data.problem_categories["sc" + k.id] = k.sub;
                        }
                    }
                );
            });
        },
        reload_sub_category: function () {
            var prblm = t.filters.problem_category.val();
            t.filters.sub_category.empty();

            if (!t.isEmpty(prblm)) {
                var prblmArr = Array.isArray(prblm) ? prblm : [prblm];
                prblmArr.forEach(function (p) {
                    var subs = t.filters.data.problem_categories["sc" + p];
                    if (subs) {
                        $.each(subs, function (i, k) {
                            t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                        });
                    }
                });
            }
            t.filters.sub_category.trigger("change");
        },
        reload_handler: function () {
            t.loadUserSelect(t.filters.handler, config.translations.Select_the_Ticket_Handler);
        },
        reload_priority: function () {
            t.filters.priority.empty();
            $.each(config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.text, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_locations: function () {
            t.filters.location.empty();
            $.each(config.locations, function (i, k) {
                t.filters.location.append(new Option(k.name, k.id, false, false));
            });
            t.filters.location.trigger("change");
        }
    };

    t.cache_filter_values = function () {
        t.config.search = (t.searchbox.val() || "").trim();
        t.config.other_filters = {};

        var filterList = {
            department: t.filters.department,
            problem_category: t.filters.problem_category,
            sub_category: t.filters.sub_category,
            priority: t.filters.priority,
            handler: t.filters.handler,
            location: t.filters.location,
            ticket_type: t.filters.ticket_type,
            based_on: t.filters.based_on
        };

        $.each(filterList, function (key, $el) {
            var val = $el.val();
            if (!t.isEmpty(val) && val !== "null") {
                t.config.other_filters[key] = val;
            }
        });

        // based_on select ho tabhi daterange add karo
        if (!t.isEmpty(t.filters.based_on.val()) && t.filters.based_on.val() !== "null") {
            if (!t.isEmpty(t.filters.daterange.val())) {
                t.config.other_filters.daterange = t.filters.daterange.val();
            }
        }

        t.config.export_filters = btoa(JSON.stringify({
            search: t.config.search,
            other_filters: t.config.other_filters
        }));

        t.updateFilterBadge();
    };

    t.updateFilterBadge = function () {
        var count = 0;

        $.each(t.config.other_filters || {}, function (k, v) {
            if (k === "daterange") return; // daterange ko count mat karo
            if (!t.isEmpty(v)) count++;
        });

        t.filterBadge.text(count).toggleClass("d-none", count === 0);
    };

    // ============ LOAD DATA ============
    t.load = function () {
        t.tableFooter = $(".table-footer");
        t.tableCover.addClass("gload");
        var colCount = t.table.find("thead th").length || 13;

        t.lg.html('<tr><td colspan="' + colCount + '">' +
            '<div class="dot-loader"><span></span><span></span><span></span></div></td></tr>');

        $.ajax({
            url: config.url.list_tickets,
            type: "POST",
            data: {
                _token: config.token,
                search: t.config.search,
                page: t.currentPage,
                size: t.perPage,
                filters: t.config.other_filters,
                order: t.config.sort_dir
            },
            success: function (data) {
                if (typeof data != "object") {
                    t.lg.html('<tr><td colspan="' + colCount + '" style="text-align:center;padding:40px;color:#dc2626;">Invalid response</td></tr>');
                    t.renderFooter(0, 0);
                    return;
                }

                t.totalRecords = parseInt(data.total) || 0;
                t.filteredRecords = data.filtered !== undefined ? parseInt(data.filtered) : t.totalRecords;
                var records = data.data || [];

                t.lg.empty();

                if (t.filteredRecords < 1 || records.length < 1) {
                    t.lg.html('<tr><td colspan="' + colCount + '" style="text-align:center;padding:40px;color:#94a3b8;">' +
                        config.translations.no_details + '</td></tr>');
                } else {
                    var html = "";
                    $.each(records, function (i, d) {
                        html += t.renderList(i, d);
                    });
                    t.lg.html(html);
                }

                t.renderFooter(t.filteredRecords, t.totalRecords);
            },
            error: function () {
                alert("Something went wrong. Please check given details are correct");
                t.lg.html('<tr><td colspan="' + colCount + '" style="text-align:center;padding:40px;color:#dc2626;">Error loading data</td></tr>');
                t.renderFooter(0, 0);
            },
            complete: function () {
                t.tableCover.removeClass("gload");
            }
        });
    };

    t._s = function (v) {
        return v !== undefined && v !== null ? v : "";
    };

    var feedbackComments = {};
    t.renderList = function (i, d) {
        var s = t._s;
        var url = config.url.ticket_info + '/' + d.id;
        var fullHtml = d.feedback_comment || "";
        var plainText = $('<div>').html(fullHtml).text().trim();
        feedbackComments[d.id] = fullHtml;
        var feedbackComment = plainText || "N/A";

        if (plainText.length > 30) {
            feedbackComment =
                plainText.substring(0, 30) +
            '... <div class="read-more-comment text-primary" data-id="' + d.id + '">Read More</div>';
        }

        var fc = "<tr>" +
            "<td class='b5-text'><a href='" + url + "' target='_blank' class='ticket-id-link'>#" + s(d.id) + "</a></td>" +
            "<td class='b5-text'><a href='" + url + "' target='_blank' class='ticket-id-link'>" + s(d.subject) + "</a></td>" +
            "<td class='b5-text'>" + s(d.priority_name) + "</td>" +
            "<td class='b5-text'>" + s(d.dept_name) + "</td>" +
            "<td class='b5-text'>" + s(d.cat_name) + "</td>" +
            "<td class='b5-text'>" + s(d.sub_cat_name) + "</td>" +
            "<td class='b5-text'>" + s(d.resolved_at) + "</td>" +
            "<td class='b5-text'>" + s(d.username) + "</td>" +
            "<td class='b5-text'>" + s(d.feedback_given_at) + "</td>" +
            "<td class='b5-text'>" + s(d.feedback) + "</td>" +
            "<td class='b5-text'>" + feedbackComment + "</td>" +
            "<td class='b5-text'>" + s(d.location_name) + "</td>" +
            "<td class='b5-text'>" + s(d.ticket_type) + "</td>";

        if (d.ticketTypeFields) {
            $.each(d.ticketTypeFields, function (i, v) { fc += "<td class='b5-text'>" + v.value + "</td>"; });
        }
        if (d.customFieldsFromTable) {
            $.each(d.customFieldsFromTable, function (i, v) { fc += "<td class='b5-text'>" + v.value + "</td>"; });
        }
        fc += "</tr>";
        return fc;
    };

    $(document).on('click', '.read-more-comment', function () {
        var content = feedbackComments[$(this).data('id')] || '';
        $('#descriptionModal .modal-title').text('Feedback Comment');
        $('#descriptionModal .modal-body').html(content);
        new bootstrap.Modal(document.getElementById('descriptionModal')).show();
    });

    // ============ FOOTER & PAGINATION ============
    t.renderFooter = function (filtered, total) {
        t.tableFooter = $(".table-footer");
        if (t.tableFooter.length === 0) return;

        filtered = parseInt(filtered) || 0;
        total = parseInt(total) || 0;

        var start = filtered > 0 ? (t.currentPage - 1) * t.perPage + 1 : 0;
        var end = Math.min(t.currentPage * t.perPage, filtered);
        var totalPages = filtered > 0 ? Math.ceil(filtered / t.perPage) : 1;

        var summaryText = 'Showing ' + start + ' to ' + end + ' of ' + filtered + ' entries';
        if (filtered != total && total > 0) {
            summaryText += ' (filtered from ' + total + ' total)';
        }

        t.tableFooter.html(
            '<div class="srq-record-count">' + summaryText + '</div>' +
            '<div class="srq-pagination" id="pagebtns"></div>'
        );

        t.buildPaginationButtons(t.tableFooter.find("#pagebtns"), totalPages);
    };

    t.buildPaginationButtons = function ($pg, totalPages) {
        $pg.empty();
        if (totalPages < 1) totalPages = 1;

        var svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';

        // Previous
        $pg.append('<button class="page-btn" data-page="' + (t.currentPage - 1) + '"' +
            (t.currentPage === 1 ? ' disabled' : '') + ' title="Previous">' +
            svg + '<polyline points="15 18 9 12 15 6"/></svg></button>');

        // Page range
        var startPage = Math.max(1, t.currentPage - 2);
        var endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        if (startPage > 1) {
            $pg.append('<button class="page-btn" data-page="1">1</button>');
            if (startPage > 2) $pg.append('<span class="page-ellipsis">...</span>');
        }

        for (var i = startPage; i <= endPage; i++) {
            $pg.append('<button class="page-btn' + (i === t.currentPage ? ' active' : '') +
                '" data-page="' + i + '">' + i + '</button>');
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) $pg.append('<span class="page-ellipsis">...</span>');
            $pg.append('<button class="page-btn" data-page="' + totalPages + '">' + totalPages + '</button>');
        }

        // Next
        $pg.append('<button class="page-btn" data-page="' + (t.currentPage + 1) + '"' +
            (t.currentPage === totalPages ? ' disabled' : '') + ' title="Next">' +
            svg + '<polyline points="9 18 15 12 9 6"/></svg></button>');
    };

    // ============ SEARCH ============
    t.search = function () {
        clearTimeout(t.searchTimer);
        t.searchTimer = setTimeout(function () {
            t.currentPage = 1;
            t.cache_filter_values();
            t.load();
        }, 400);
    };

    // ============ DOWNLOAD ============
    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_feedback_xls + "?q=" + t.config.export_filters;
    };

    // ============ CLEAR FILTER ============
    t.btnClrFilterAction = function () {
        // Multi-select filters
        var multiSelects = [
            t.filters.department,
            t.filters.problem_category,
            t.filters.sub_category,
            t.filters.priority,
            t.filters.handler,
            t.filters.location,
            t.filters.ticket_type
        ];
        $.each(multiSelects, function (i, $el) { $el.val("").trigger("change"); });

        // Single select
        t.filters.based_on.val("null").trigger("change");

        // Date range
        t.tab.find("#reportrange span").text("");
        resetFilterCount();
        t.filterModal.modal("hide");
    };

    // ============ INIT ============
    t.init = function () {
        // Filter modal open
        t.btnOpenFilter.on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            t.openFilterModal();
        });

        // Modal close
        t.filterModal.on("click", "[data-bs-dismiss='modal'], .modal-close", function (e) {
            e.preventDefault();
            t.closeFilterModal();
        });

        // Sort
        t.renderSortFields();
        t.sortbtns.on("mousedown", ".sort-fields li", t.sortFieldChanged);
        t.sortbtns.on("mousedown", ".dir-sort", t.sortFieldChanged);
        t.sortbtns.on("click", ".dropdown-toggle", function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            t.sortbtns.toggleClass("open");
        });

        $(document).on("click", function (e) {
            if (!$(e.target).closest(t.sortbtns).length) {
                t.sortbtns.removeClass("open");
            }
        });

        // Search
        t.searchbox.off("input");
        t.searchbox.on("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                clearTimeout(t.searchTimer);
                t.search.call(this, e);
            }
        });

        // Page limiter
        t.pageLimiter.on("change", function () {
            t.perPage = parseInt($(this).val()) || 10;
            t.currentPage = 1;
            t.load();
        });

        // Reload
        t.btnReload.on("click", function () { t.load(); });

        // Download
        t.btnDownload.on("click", t.download);

        // Pagination
        $(document).on("click", ".table-footer .page-btn:not(:disabled)", function () {
            var page = parseInt($(this).data("page"));
            if (page && page !== t.currentPage && page >= 1) {
                t.currentPage = page;
                t.load();
                var cover = t.tableCover[0];
                if (cover) cover.scrollTo({ top: 0, behavior: "smooth" });
            }
        });

        // Apply filter
        t.btnApplyFilter.on("click", function () {
            t.cache_filter_values();
            t.currentPage = 1;
            t.load();
            t.closeFilterModal();
        });

        // Clear filter
        t.btnClrFilter.on("click", t.btnClrFilterAction);

        // Department -> Problem -> Sub category chain
        t.filters.department.on("change", t.filters.fun.reload_problem_category);
        t.filters.problem_category.on("change", t.filters.fun.reload_sub_category);

        // Select2 init - sirf modal wale filters
        var select2ModalOpts = { width: "100%", dropdownParent: t.filterModal };

        // Multi-select filters with placeholder
        t.filters.department.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.filter_by_department }));
        t.filters.problem_category.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Problem_Category }));
        t.filters.sub_category.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Sub_Category }));
        t.filters.priority.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Priority }));
        t.filters.location.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.filter_by_location_id }));
        t.filters.ticket_type.select2($.extend({}, select2ModalOpts, { placeholder: 'Select Ticket Type' }));
        t.filters.based_on.select2($.extend({}, select2ModalOpts));
        // Load initial filter data
        t.filters.fun.reload_department();
        t.filters.fun.reload_priority();
        t.filters.fun.reload_locations();
        t.filters.fun.reload_handler(); // AJAX based handler

        // Initial load
        t.load();
    };

    t.init();
};
