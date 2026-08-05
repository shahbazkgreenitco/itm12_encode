var RequestReport = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");
    t.sortbtns = t.tab.find(".sort-buttons");
    t.searchbox = t.tab.find(".searchbox");
    t.searchbtn = t.tab.find(".btn-searchbox");
    t.reload = t.tab.find(".btn-reload");
    t.export = t.content.find(".btn-download");
    t.export_pdf = t.tab.find(".btn-download-pdf");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.filterbtn = t.content.find(".btn-open-filter");
    t.dataTable = null;

    // Filters
    t.filters = {
        data: { problem_categories: {} },
        wrapper: $("#FilterModal")
    };

    t.filters.department = $("#filter_by_department");
    t.filters.problem_category = $("#filter_by_problem_category");
    t.filters.sub_category = $("#filter_by_sub_category");
    t.filters.status = $("#filter_by_status");
    t.filters.location_id = $("#filter_location_id");
    t.filters.ticket_handlers = $("#filter_by_ticket_handlers");
    t.filters.pab = $("#filter_by_pab");
    t.filters.based_on = $("#filter_by_date");
    t.filters.daterange = $("#daterange");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || ""; };

    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};

        // Department filter (multi-select)
        if (t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null && t.filters.department.val().length > 0) {
            t.config.other_filters.department = t.filters.department.val();
        }

        // Problem Category filter (multi-select)
        if (t.filters.problem_category && t.filters.problem_category.val() !== 'null' && t.filters.problem_category.val() !== null && t.filters.problem_category.val().length > 0) {
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        }

        // Sub Category filter (multi-select)
        if (t.filters.sub_category && t.filters.sub_category.val() !== 'null' && t.filters.sub_category.val() !== null && t.filters.sub_category.val().length > 0) {
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }

        // Status filter (multi-select)
        if (t.filters.status && t.filters.status.val() !== 'null' && t.filters.status.val() !== null && t.filters.status.val().length > 0) {
            t.config.other_filters.status = t.filters.status.val();
        }

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            t.config.other_filters.ticket_handlers = t.filters.ticket_handlers.val();
        }

        // PAB filter (multi-select)
        if (t.filters.pab && t.filters.pab.val() !== 'null' && t.filters.pab.val() !== null && t.filters.pab.val().length > 0) {
            t.config.other_filters.pab = t.filters.pab.val();
        }

        // Based On filter
        if (t.filters.based_on && t.filters.based_on.val() !== 'null' && t.filters.based_on.val() !== null) {
            t.config.other_filters.filter_by_date = t.filters.based_on.val();
        }

        // Date Range filter
        var daterangeVal = t.filters.daterange.val();
        if (daterangeVal && daterangeVal !== '' && daterangeVal !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters
        };

        t.config.export_filters = btoa(JSON.stringify(jobj));

        // ===== Count Fix =====
        var countFilters = $.extend({}, t.config.other_filters);

        // Filter By Date + Date Range = 1 filter
        if (countFilters.filter_by_date || countFilters.daterange) {
            delete countFilters.filter_by_date;
            delete countFilters.daterange;
            countFilters.date_filter = true;
        }

        filterCount(countFilters, "", false);
    };
    // ========== Search Function ==========
    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox.val());
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            if (t.dataTable) t.dataTable.draw();
        } else if (target.tagName == "BUTTON" || target.closest(".btn-filter").length) {
            t.cache_filter_values();
            $("#FilterModal").modal('hide');
            if (t.dataTable) t.dataTable.draw();
        }
    };

    // ========== Reset Function ==========
    t.reset = function() {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        if (t.dataTable) {
            t.dataTable.page.len(t.perPage);
        }
    };

    // ========== Init DataTable ==========
    t.initDataTable = function() {
        t.dataTable = t.table.DataTable({
            autoWidth: false,
            colReorder: true,
            stateSave: true,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
            lengthChange: false,
            searching: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [[0, 'desc']],
            ajax: {
                url: config.url.list_tickets,
                type: 'POST',
                data: function(d) {
                    d.search = t.config.search || '';
                    d.filters = t.config.other_filters || {};
                    d.order_by = t.config.sort_dir.id || 1;
                    d.order_dir = t.config.sort_dir.dir || 2;
                    d.main_filter = t.config.main_filter || '';
                    d.order = d.order || [{ column: 0, dir: 'desc' }];
                    d.draw = d.draw || 1;
                },
                dataSrc: function(json) {
                    if (json && json.data !== undefined) {
                        var total = json.recordsTotal || json.total || 0;
                        var filtered = json.recordsFiltered || json.filtered || 0;
                        if (filtered != total) {
                            t.pageBtmSummary.html("Available " + filtered + " records (filtered from " + total + " total records)");
                        } else {
                            t.pageBtmSummary.html("Available Records: " + total);
                        }
                        return json.data || [];
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover.removeClass("gload");
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="12">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                { 
                    data: 'procure_tag',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + config.url.service_request_info + '/' + row.id + '" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                { data: 'subject' },
                { data: 'dep_name' },
                { data: 'problem_category' },
                { data: 'sub_category' },
                { data: 'creator_name' },
                { data: 'created_at_format' },
                { data: 'pab' },
                { data: 'status' },
                { data: 'approved_at_format' },
                { 
                    data: 'approved_ticket_id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data) {
                                return '<a href="' + config.url.ticket_info + '/' + data + '" target="_blank" class="underline">' + t._s(data) + '</a>';
                            }
                            return '';
                        }
                        return data;
                    }
                },
                { data: 'ticket_status' }
            ],
            pageLength: t.perPage,
            lengthChange: false,
            ordering: true,
            stateSave: false,
            language: {
                emptyTable: config.translations.no_details || "No records found",
                zeroRecords: config.translations.no_details || "No records found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            createdRow: function(row, data, dataIndex) {
               $(row).find('td').addClass('b5-text');
            },
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="12">' + config.translations.no_details + '</td></tr>');
                }
                t.tableCover.removeClass("gload");
            },
            preDrawCallback: function(settings) {
                t.tableCover.addClass("gload");
            }
        });

        t.dataTable.page.len(t.perPage);
    };

    // ========== Download Functions ==========
    t.download = function(e) {
        alert("adcsd");
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };

    // ========== Load Function ==========
    t.load = function() {
        if (t.dataTable) {
            t.dataTable.draw();
        }
    };

    // ========== Clear Filters ==========
    t.btn.clear.click(function() {
        t.filters.department.val(null).trigger("change");
        t.filters.problem_category.val(null).trigger("change");
        t.filters.sub_category.val(null).trigger("change");
        t.filters.status.val(null).trigger("change");
        t.filters.ticket_handlers.val(null).trigger("change");
        t.filters.location_id.val(null).trigger("change");
        t.filters.pab.val(null).trigger("change");
        t.filters.based_on.val('null').trigger("change");
        t.filters.daterange.val('');
        t.config.other_filters = {};
        t.config.search = '';
        t.searchbox.val('');
        $('#filter_count').css('display', 'none').addClass('d-none');
        resetFilterCount();
        if (t.dataTable) t.dataTable.draw();
        t.filters.wrapper.modal('hide');
    });

    // ========== Event Bindings ==========
    t.searchbox.on("keypress", $.proxy(t.search, t));
    t.reload.on("click", $.proxy(t.load, t));
    t.searchbtn.on("click", $.proxy(t.search, t));
    t.export.on("click", $.proxy(t.download, t));
    t.btn.filter.on("click", $.proxy(t.search, t));

    t.pageLimiter.on("change", function() {
        t.reset();
        if (t.dataTable) {
            t.dataTable.draw();
        }
    });

    // ========== Initialize Select2s ==========
    var select2Opts = { width: "100%" };
    var modalBody = $("#FilterModal .modal-body");

    t.filters.department.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_department || "Filter by Department",
        dropdownParent: modalBody,
        ajax: {
            url: config.url.departments_by_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: config.company,
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.problem_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Problem_Category || "Filter by Problem Category",
        dropdownParent: modalBody
    }));

    t.filters.sub_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Sub_Category || "Filter by Sub Category",
        dropdownParent: modalBody
    }));

    t.filters.status.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Status",
        dropdownParent: modalBody
    }));

    t.filters.location_id.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_location_id || "Filter by Location",
        dropdownParent: modalBody
    }));

    t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Ticket_Handler || "Filter by Ticket Handler",
        dropdownParent: modalBody,
        ajax: {
            url: config.getUserByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: config.company
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.pab.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Authority Board",
        dropdownParent: modalBody
    }));

    t.filters.based_on.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Date",
        dropdownParent: modalBody
    }));

    // ========== Date Range Picker ==========
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#daterange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            weekLabel: 'W',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    }, cb);

    cb(start, end);

    // ========== Reload Filter Data ==========
    t.filters.fun = {
        reload_department: function() {
            // Already using AJAX in select2
        },
        reload_problem_category: function() {
            t.filters.problem_category.empty();
            var department = t.filters.department.val();
            if (department && department.length > 0 && department[0] != "null") {
                $.get(config.url.problem_categories_by_company + "/" + department[0], function(data) {
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
        },
        reload_sub_category: function() {
            t.filters.sub_category.empty();
            var prblm = t.filters.problem_category.val();
            if (prblm && prblm.length > 0 && prblm[0] != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm[0]], function(i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {}
            }
        },
        reload_status: function() {
            $.each(config.status, function(i, k) {
                t.filters.status.append(new Option(k.name, k.id, false, false));
            });
        },
        reload_locations: function() {
            $.each(config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
        },
        reload_pab: function() {
            $.each(config.pabs, function(i, k) {
                t.filters.pab.append(new Option(k.name, k.id, false, false));
            });
        }
    };

    t.filters.fun.reload_status();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_pab();

    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category, t));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function() {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};