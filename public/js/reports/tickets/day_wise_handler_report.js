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

    t.filters.ticket_handlers = $("#filter_by_ticket_handlers");
    t.filters.location_id = $("#filter_location_id");
    t.filters.filter_by_date = $("#filter_by_date");
    t.filters.daterange = $("#daterange");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function (v) { return v || ""; };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            t.config.other_filters.ticket_handlers = t.filters.ticket_handlers.val();
        }

        // Location filter (multi-select)
        if (t.filters.location_id && t.filters.location_id.val() !== 'null' && t.filters.location_id.val() !== null && t.filters.location_id.val().length > 0) {
            t.config.other_filters.location_id = t.filters.location_id.val();
        }

        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
       
        var daterangeVal = t.filters.daterange.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null) {
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
            order: [[0, 'desc']],
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
                    d.draw = d.draw || 1;
                },
                dataSrc: function (json) {
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
                error: function (xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover.removeClass("gload");
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="8">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                { data: 'location_name' },
                { data: 'full_name' },
                {
                    data: 'tot',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + baseURL + '/reports/tickets/info/day-hand-tic-info?assigned_to=' + row.id + '&handler_filters=' + t.config.export_filters + '" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'open_tickets',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + baseURL + '/reports/tickets/info/day-hand-tic-info?status_id=1&assigned_to=' + row.id + '&handler_filters=' + t.config.export_filters + '" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'resolved_with_in_sla',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + baseURL + '/reports/tickets/info/day-hand-tic-info?status_id=5&assigned_to=' + row.id + '&handler_filters=' + t.config.export_filters + '&sla=1" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'resolved_out_of_sla',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + baseURL + '/reports/tickets/info/day-hand-tic-info?status_id=5&assigned_to=' + row.id + '&handler_filters=' + t.config.export_filters + '&sla=2" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                { data: 'rr' },
                { data: 'feedback' }
            ],
            columnDefs: [
                {
                    targets: [6, 7],
                    className: 'text-center'
                }
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
            drawCallback: function (settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="8">' + config.translations.no_details + '</td></tr>');
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
        t.filters.ticket_handlers.val(null).trigger("change");
        t.filters.location_id.val(null).trigger("change");
        t.filters.filter_by_date.val('null').trigger("change");
        t.filters.daterange.val('');
        $('#reportrange span').html('');
        t.config.search = '';
        t.searchbox.val('');
        t.config.other_filters = {};
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

    t.pageLimiter.on("change", function () {
        t.reset();
        if (t.dataTable) {
            t.dataTable.draw();
        }
    });

    // ========== Initialize Select2s ==========
    var select2Opts = { width: "100%" };
    var modalBody = $("#FilterModal .modal-body");

    t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Ticket_Handler || "Filter by Ticket Handler",
        dropdownParent: modalBody
    }));

    t.filters.location_id.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_location || "Filter by Location",
        dropdownParent: modalBody
    }));

    t.filters.filter_by_date.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_location || "Filter by Location",
        dropdownParent: modalBody
    }));

    // ========== Date Range Picker ==========
    // Set default date range (last 30 days)
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

    // ========== Reload Data ==========
    t.filters.fun = {
        reload_ticket_handlers: function () {
            $.each(t.config.ticket_handlers, function (i, k) {
                t.filters.ticket_handlers.append(new Option(k.full_name, k.id, false, false));
            });
            t.filters.ticket_handlers.trigger("change");
        },
        reload_locations: function () {
            $.each(t.config.locations, function (i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        }
    };

    t.filters.fun.reload_ticket_handlers();
    t.filters.fun.reload_locations();

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};