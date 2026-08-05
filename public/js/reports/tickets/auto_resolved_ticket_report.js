var TicketReport = function (config) {
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

    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.status = t.filters.wrapper.find("#filter_by_status");
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority");
    t.filters.location_id = t.filters.wrapper.find("#filter_location_id");
    t.filters.filter_by_date = t.filters.wrapper.find("#filter_by_date");
    t.filters.daterange = t.filters.wrapper.find("#daterange");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_department: function () {
            $.get(config.url.departments_by_company + "/" + config.company, function (data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function (i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
        },
        reload_problem_category: function () {
            t.filters.problem_category.empty();
            var department = t.filters.department.val();
            if (department && department.length > 0 && department[0] != "null") {
                $.get(config.url.problem_categories_by_company + "/" + department[0], function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function (i, k) {
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
        reload_sub_category: function () {
            t.filters.sub_category.empty();
            var prblm = t.filters.problem_category.val();
            if (prblm && prblm.length > 0 && prblm[0] != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm[0]], function (i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) { }
            }
        },
        reload_status: function () {
            $.each(config.status, function (i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
        },
        reload_priority: function () {
            $.each(config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.text, k.id, false, false));
            });
        },
        reload_locations: function () {
            $.each(config.locations, function (i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
        }
    };

    // ========== Helper Functions ==========
    t._s = function (v) { return v || ""; };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v;
        t.config.other_filters = {};

        if (t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null && t.filters.department.val().length > 0)
            t.config.other_filters.department = t.filters.department.val();
        if (t.filters.problem_category && t.filters.problem_category.val() != 'null' && t.filters.problem_category.val() !== null && t.filters.problem_category.val().length > 0)
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        if (t.filters.sub_category && t.filters.sub_category.val() != 'null' && t.filters.sub_category.val() !== null && t.filters.sub_category.val().length > 0)
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        if (t.filters.status && t.filters.status.val() != 'null' && t.filters.status.val() !== null && t.filters.status.val().length > 0)
            t.config.other_filters.status = t.filters.status.val();
        if (t.filters.priority && t.filters.priority.val() != 'null' && t.filters.priority.val() !== null && t.filters.priority.val().length > 0)
            t.config.other_filters.priority = t.filters.priority.val();
        if (t.filters.location_id && t.filters.location_id.val() != 'null' && t.filters.location_id.val() !== null && t.filters.location_id.val().length > 0)
            t.config.other_filters.location_id = t.filters.location_id.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        var daterangeVal = t.filters.daterange.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null){
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
    };

    // ========== Search Function ==========
    t.search = function (e) {
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
    t.reset = function () {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        if (t.dataTable) {
            t.dataTable.page.len(t.perPage);
        }
    };

    // ========== Init DataTable ==========
    t.initDataTable = function () {
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
            order: [[10, 'desc']],
            ajax: {
                url: config.url.list_tickets,
                type: 'POST',
                data: function (d) {
                    d.search = t.config.search || '';
                    d.filters = t.config.other_filters || {};
                    d.order_by = t.config.sort_dir.id || 1;
                    d.order_dir = t.config.sort_dir.dir || 2;
                    d.main_filter = t.config.main_filter || '';
                    d.order = d.order || [{ column: 0, dir: 'desc' }];
                },
            },
            columns: [
                { data: 'id' },
                { data: 'subject' },
                { data: 'dept_name' },
                { data: 'problem_category' },
                { data: 'sub_category' },
                { data: 'status' },
                { data: 'creator' },
                { data: 'handler' },
                { data: 'tat_time' },
                { data: 'priority' },
                { data: 'updated_at_format' }
            ],
            columnDefs: [{
                targets: 0,
                render: function (data, type, row) {
                    if (type === 'display') {
                        return '<a href="' + config.url.view_ticket + "/" + row.id + '?b=all-tickets" target="_blank" class="underline">#' + row.id + '</a>';
                    }
                    return data;
                }
            }],
            createdRow: function(row, data, dataIndex) {
               $(row).find('td').addClass('b5-text');
            },
            pageLength: t.perPage,
            lengthChange: false,
            ordering: true,
            stateSave: false,
            drawCallback: function (settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="11">' + config.translations.no_details + '</td></tr>');
                }
                t.tableCover.removeClass("gload");
            },
            preDrawCallback: function (settings) {
                t.tableCover.addClass("gload");
            }
        });

        t.dataTable.page.len(t.perPage);
    };

    // ========== Download Functions ==========
    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };

    // ========== Load Function ==========
    t.load = function () {
        if (t.dataTable) {
            t.dataTable.draw();
        }
    };

    // ========== Clear Filters ==========
    t.btn.clear.click(function () {
        // Clear all select2 values - use null for multi-select
        t.filters.department.val(null).trigger("change");
        t.filters.status.val(null).trigger("change");
        t.filters.problem_category.val(null).trigger("change");
        t.filters.sub_category.val(null).trigger("change");
        t.filters.priority.val(null).trigger("change");
        t.filters.location_id.val(null).trigger("change");
        
        // Clear single select - use 'null' value
        t.filters.filter_by_date.val('null').trigger("change");
        
        // Clear date range
        t.filters.daterange.val('');
        $('#reportrange span').html('');
        
        // Clear search
        t.config.search = '';
        t.searchbox.val('');
        
        // Clear filters object
        t.config.other_filters = {};
        
        // Reset filter count using your existing function
        resetFilterCount();
        
        // Reload DataTable
        if (t.dataTable) t.dataTable.draw();
        
        // Close modal
        t.filters.wrapper.modal('hide');
    });

    // ========== Event Bindings ==========
    t.searchbox.on("keypress", $.proxy(t.search, t));
    t.reload.on("click", $.proxy(t.load, t));
    t.searchbtn.on("click", $.proxy(t.search, t));
    t.export.on("click", $.proxy(t.download));
    t.btn.filter.on("click", $.proxy(t.search, t));

    t.pageLimiter.on("change", function () {
        t.reset();
        if (t.dataTable) {
            t.dataTable.draw();
        }
    });

    // ========== Initialize Select2s with dropdownParent ==========
    var select2Opts = { width: "100%" };

    // Get the modal body as dropdown parent for all selects inside modal
    var modalBody = $("#FilterModal .modal-body");

    t.filters.department.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_department, dropdownParent: modalBody }));
    t.filters.status.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_status, dropdownParent: modalBody }));
    t.filters.problem_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Problem_Category, dropdownParent: modalBody }));
    t.filters.sub_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Sub_Category, dropdownParent: modalBody }));
    t.filters.priority.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Priority, dropdownParent: modalBody }));
    t.filters.location_id.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Location, dropdownParent: modalBody }));
    t.filters.filter_by_date.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_date || "Filter by Date", dropdownParent: modalBody }));

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

    // ========== Reload Data ==========
    t.filters.fun.reload_department();
    t.filters.fun.reload_status();
    t.filters.fun.reload_priority();
    t.filters.fun.reload_locations();
    
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category, t));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));

    // ========== Initialize DataTable ==========
    t.initDataTable();

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });
};