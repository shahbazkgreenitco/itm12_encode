var TableList = function (config, otherConfig) {
    var t = this;
    t.config = config;
    t.otherConfig = otherConfig;
    t.tab = $(".content");
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.table.find("tbody");
    t.sortbtns = t.tab.find(".sort-buttons");
    t.searchbox = t.tab.find(".searchbox");
    t.btnReload = t.tab.find(".btn-reload");
    t.btnDownload = t.tab.find(".btn-download");
    t.btnOpenFilter = t.tab.find(".btn-open-filter");
    t.btnApplyFilter = t.tab.find(".btn-filter");
    t.btnClrFilter = t.tab.find("#btnClrFilter");
    t.filterBadge = t.tab.find(".filter-count-badge");
    t.tableFooter = t.tab.find(".table-footer");
    t.filterModal = t.tab.find("#FilterModal");
    t.pageLimiter = t.tab.find("#pageLimiter");

    t.perPage = 10;
    t.currentPage = 1;
    t.totalRecords = 0;
    t.filteredRecords = 0;

    t.filters = {
        data: { problem_categories: {} },
        wrapper: t.filterModal,
        department: t.tab.find("#filter_by_department"),
        problem_category: t.tab.find("#filter_by_problem_category"),
        sub_category: t.tab.find("#filter_by_sub_category"),
        priority: t.tab.find("#filter_by_priority"),
        location_id: t.tab.find("#filter_by_location"),
        ticket_handlers: t.tab.find("#filter_by_handler"),
        status: t.tab.find("#filter_by_status"),
        based_on: t.tab.find("#filter_by_date"),
        daterange: t.tab.find("#daterange"),
        ticket_type: t.tab.find("#filter_by_ticket_type"),
        btnfilterclr: t.tab.find("#btnClrFilter"),
        btnApply: t.tab.find(".btn-filter")
    };

    t.isEmpty = function (val) {
        return val === null || val === "" || val === "null" || val === undefined ||
            (Array.isArray(val) && val.length === 0);
    };

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

    t.renderSortFields = function () {
        var sf = t.sortbtns.find(".sort-fields");
        var s = t.config.sort_dir;
        var $dirSort = t.sortbtns.find(".dir-sort");

        $dirSort.find("#sortAscSvg").toggleClass("hide", s.dir != 1);
        $dirSort.find("#sortDescSvg").toggleClass("hide", s.dir != 2);
        $dirSort.attr({ "data-id": s.id, "data-dir": s.dir == 1 ? "asc" : "desc" });

        sf.empty();
        $.each(otherConfig.sort_fields, function (i, d) {
            var isSelected = d.id == s.id;
            var iconHtml = '';
            if (isSelected) {
                iconHtml = s.dir == 1
                    ? '<i class="fa fa-sort-amount-asc pull-right"></i>'
                    : '<i class="fa fa-sort-amount-desc pull-right"></i>';
            }
            $('<li class="list-group-item ' + (isSelected ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text + iconHtml + '</li>').appendTo(sf);
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
            t.loadUserSelect(t.filters.ticket_handlers, config.translations.Filter_By_Ticket_Handler);
        },
        reload_priority: function () {
            t.filters.priority.empty();
            $.each(config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.name || k.text, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_locations: function () {
            t.filters.location_id.empty();
            $.each(config.locations, function (i, k) {
                t.filters.location_id.append(new Option(k.name, k.id, false, false));
            });
            t.filters.location_id.trigger("change");
        },
        reload_status: function () {
            t.filters.status.empty();
            $.each(config.status, function (i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        }
    };

    t.cache_filter_values = function () {
        var v = t.searchbox.validate_str_param ? t.searchbox.validate_str_param() : t.searchbox.val();
        t.config.search = (v || "").trim();
        t.config.other_filters = {};

        var filterList = {
            department: t.filters.department,
            problem_category: t.filters.problem_category,
            sub_category: t.filters.sub_category,
            priority: t.filters.priority,
            ticket_handlers: t.filters.ticket_handlers,
            location_id: t.filters.location_id,
            status: t.filters.status,
            ticket_type: t.filters.ticket_type
        };

        $.each(filterList, function (key, $el) {
            var val = $el.val();
            if (!t.isEmpty(val) && val !== "null") {
                t.config.other_filters[key] = val;
            }
        });

        if (!t.isEmpty(t.filters.based_on.val()) && t.filters.based_on.val() !== "null") {
            t.config.other_filters.based_on = t.filters.based_on.val();
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

    t.load = function () {
        t.tableFooter = $(".table-footer");
        t.tableCover.addClass("gload");
        var colCount = t.table.find("thead th").length || 12;

        t.lg.html('<tr><td colspan="' + colCount + '">' +
            '<div class="dot-loader"><span></span><span></span><span></span></div></td></tr>');

        $.ajax({
            url: otherConfig.url,
            type: "POST",
            data: {
                _token: config.token,
                search: t.config.search,
                page: t.currentPage,
                size: t.perPage,
                main_filter: t.config.main_filter,
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
                        (config.translations.no_details || 'No records found') + '</td></tr>');
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

    t.renderList = function (i, d) {
        var s = t._s;
        var url = config.url.ticket_info + '/' + d.id;

        var fc = "<tr>" +
            "<td><a href='" + url + "' target='_blank' class='ticket-id-link'>#" + s(d.id) + "</a></td>" +
            "<td>" + s(d.dept_name) + "</td>" +
            "<td>" + s(d.cat_name) + "</td>" +
            "<td>" + s(d.sub_cat_name) + "</td>" +
            "<td>" + s(d.created_at_date) + "</td>" +
            "<td>" + s(d.updated_at_date) + "</td>" +
            "<td>" + s(d.status) + "</td>" +
            "<td>" + s(d.tat) + "</td>" +
            "<td>" + s(d.priority) + "</td>" +
            "<td>" + s(d.assigned_to_name) + "</td>" +
            "<td>" + s(d.location_name) + "</td>" +
            "<td>" + s(d.ticket_type) + "</td>";

        if (d.ticketTypeFields) {
            $.each(d.ticketTypeFields, function (i, v) { fc += "<td>" + s(v.value) + "</td>"; });
        }
        if (d.customFieldsFromTable) {
            $.each(d.customFieldsFromTable, function (i, v) { fc += "<td>" + s(v.value) + "</td>"; });
        }
        fc += "</tr>";
        return fc;
    };

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
        $pg.append('<button class="page-btn" data-page="' + (t.currentPage - 1) + '"' +
            (t.currentPage === 1 ? ' disabled' : '') + ' title="Previous">' +
            svg + '<polyline points="15 18 9 12 15 6"/></svg></button>');

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

        $pg.append('<button class="page-btn" data-page="' + (t.currentPage + 1) + '"' +
            (t.currentPage === totalPages ? ' disabled' : '') + ' title="Next">' +
            svg + '<polyline points="9 18 15 12 9 6"/></svg></button>');
    };

    t.search = function () {
        clearTimeout(t.searchTimer);
        t.searchTimer = setTimeout(function () {
            var v = t.searchbox.validate_str_param ? t.searchbox.validate_str_param() : t.searchbox.val();
            if (v === false) {
                alert("Please enter a valid value for search");
                return;
            }
            t.currentPage = 1;
            t.cache_filter_values();
            t.load();
        }, 400);
    };

    t.searchKeypress = function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            clearTimeout(t.searchTimer);
            var v = t.searchbox.validate_str_param ? t.searchbox.validate_str_param() : t.searchbox.val();
            if (v === false) {
                alert("Please enter a valid value for search");
                return;
            }
            t.currentPage = 1;
            t.cache_filter_values();
            t.load();
        }
    };

    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };

    t.downloadPDF = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_pdf + "?q=" + t.config.export_filters;
    };


    t.btnClrFilterAction = function () {
        var multiSelects = [
            t.filters.department,
            t.filters.problem_category,
            t.filters.sub_category,
            t.filters.priority,
            t.filters.location_id,
            t.filters.ticket_handlers,
            t.filters.status,
            t.filters.ticket_type
        ];
        $.each(multiSelects, function (i, $el) { $el.val("").trigger("change"); });

        t.filters.based_on.val("null").trigger("change");
        t.searchbox.val('');

        t.config.search = "";
        t.config.other_filters = {};
        t.updateFilterBadge();

        t.currentPage = 1;
        t.load();
        t.closeFilterModal();
    };

    t.init = function () {
        if (t.btnOpenFilter.length) {
            t.btnOpenFilter.on("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                t.openFilterModal();
            });
        }

        t.filterModal.on("click", "[data-bs-dismiss='modal'], .modal-close", function (e) {
            e.preventDefault();
            t.closeFilterModal();
        });

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

        t.searchbox.on("input", t.search);
        t.searchbox.on("keypress", t.searchKeypress);

        t.pageLimiter.on("change", function () {
            t.perPage = parseInt($(this).val()) || 10;
            t.currentPage = 1;
            t.load();
        });

        t.btnReload.on("click", function () { t.load(); });
        t.btnDownload.on("click", t.download);

        $(document).on("click", ".table-footer .page-btn:not(:disabled)", function () {
            var page = parseInt($(this).data("page"));
            if (page && page !== t.currentPage && page >= 1) {
                t.currentPage = page;
                t.load();
                var cover = t.tableCover[0];
                if (cover) cover.scrollTo({ top: 0, behavior: "smooth" });
            }
        });

        t.btnApplyFilter.on("click", function () {
            t.cache_filter_values();
            t.currentPage = 1;
            t.load();
            t.closeFilterModal();
        });

        t.btnClrFilter.on("click", t.btnClrFilterAction);
        t.filters.department.on("change", t.filters.fun.reload_problem_category);
        t.filters.problem_category.on("change", t.filters.fun.reload_sub_category);
        var select2ModalOpts = { width: "100%", dropdownParent: t.filterModal };
        t.filters.department.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.filter_by_department }));
        t.filters.problem_category.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Problem_Category }));
        t.filters.sub_category.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Sub_Category }));
        t.filters.priority.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Priority }));
        t.filters.location_id.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.Filter_By_Locations }));
        t.filters.status.select2($.extend({}, select2ModalOpts, { placeholder: config.translations.filter_by_status }));
        t.filters.ticket_type.select2($.extend({}, select2ModalOpts, { placeholder: 'Select Ticket Type' }));
        t.filters.based_on.select2(select2ModalOpts);

        t.filters.fun.reload_department();
        t.filters.fun.reload_priority();
        t.filters.fun.reload_locations();
        t.filters.fun.reload_status();
        t.filters.fun.reload_handler();
        t.load();
    };
    t.init();
};

var TicketReport = function (config) {
    var t = this;
    t.content = $(".content");
    t.deviceTab = new TableList(config, {
        url: config.url.list_tickets,
        sort_fields: config.sort_fields.ticket,
        tbl_fields: config.tbl_fields.ticket
    });
};

                    