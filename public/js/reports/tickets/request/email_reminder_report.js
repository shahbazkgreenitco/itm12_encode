var EmailReminderReport = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");
    t.searchbox = t.tab.find(".searchbox");
    t.searchbtn = t.tab.find(".btn-searchbox");
    t.reload = t.tab.find(".btn-reload");
    t.export = t.content.find(".btn-download");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.filterbtn = t.content.find(".btn-open-filter");
    t.dataTable = null;

    // Filters
    t.filters = {
        wrapper: $("#FilterModal")
    };

    t.filters.based_on = $("#filter_by_date");
    t.filters.daterange = $("#daterange");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || ""; };

    t.truncate = function(source, size) {
        return source.length > size ? source.slice(0, size - 1) + "…" : source;
    };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};

        if (t.filters.based_on && t.filters.based_on.val() !== 'null' && t.filters.based_on.val() !== null) {
            t.config.other_filters.filter_by_date = t.filters.based_on.val();
        }

        var daterangeVal = t.filters.daterange.val();
        if (daterangeVal && daterangeVal !== '' && daterangeVal !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters
        };

        t.config.export_filters = btoa(JSON.stringify(jobj));
        var countFilters = $.extend({}, t.config.other_filters);
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="7">' + config.translations.something_wrong + '</td></tr>');
                }
            },
            columns: [
                {
                    data: 'ticket_id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var ticketInfo = config.url.ticketInfo + "/" + data;
                            return '<a href="' + ticketInfo + '" target="_blank" class="url_link">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'procure_tag',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var requestInfo = config.url.requestInfo + "/" + row.ticket_id;
                            return '<a href="' + requestInfo + '" target="_blank" class="url_link">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                { data: 'ps_no' },
                { data: 'user_name' },
                { data: 'email_address' },
                { data: 'no_of_day' },
                { data: 'created_at_format' }
            ],
            columnDefs: [
                {
                    targets: [5],
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
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="7">' + config.translations.no_details + '</td></tr>');
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
        t.filters.based_on.val('null').trigger("change");
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

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function() {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};