var MemberReport = function (config) {
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

    t.filters.ticket_handlers = $("#filter_by_ticket_handlers");
    t.filters.pab = $("#filter_by_pab");
    t.filters.based_on = $("#filter_by_date");
    t.filters.daterange = $("#daterange");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function (v) { return v || ""; };

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_ticket_handlers: function () {
            var select2Opts = { width: "100%" };
            t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.ticket_handlers.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: 'Filter By Creator'
            }));
            t.filters.ticket_handlers.trigger("change");
        },
        reload_pab: function () {
            $.each(t.config.pabs, function (i, k) {
                t.filters.pab.append(new Option(k.name, k.id, false, false));
            });
            t.filters.pab.trigger("change");
        }
    };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            var handlerVal = t.filters.ticket_handlers.val().filter(function (v) {
                return v != 0 && v != 'null';
            });
            if (handlerVal.length > 0) {
                t.config.other_filters.ticket_handlers = handlerVal;
            }
        }

        // PAB filter (multi-select)
        if (t.filters.pab && t.filters.pab.val() !== 'null' && t.filters.pab.val() !== null && t.filters.pab.val().length > 0) {
            var pabVal = t.filters.pab.val().filter(function (v) {
                return v != 0 && v != 'null';
            });
            if (pabVal.length > 0) {
                t.config.other_filters.pab = pabVal;
            }
        }

        // Based On filter
        var basedOnVal = t.filters.based_on ? t.filters.based_on.val() : null;
        if (basedOnVal && basedOnVal !== 'null') {
            t.config.other_filters.based_on = basedOnVal;
        }

        // Date Range filter
        var daterangeVal = t.filters.daterange ? $.trim(t.filters.daterange.val()) : "";
        if (daterangeVal && daterangeVal !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters
        };

        t.config.export_filters = btoa(JSON.stringify(jobj));

        // ===== Count Fix =====
        var countFilters = $.extend({}, t.config.other_filters);

        // Based On + Date Range ko ek hi filter count karo
        if (countFilters.based_on || countFilters.daterange) {
            delete countFilters.based_on;
            delete countFilters.daterange;
            countFilters.date_filter = 1;
        }

        filterCount(countFilters, "", false);
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="6">' + config.translations.something_wrong + '</td></tr>');
                }
            },
            columns: [
                { data: 'requestor_name' },
                { data: 'pab_name' },           // Changed from 'name' to match 'pab_name'
                { data: 'hierarchy_approval' },
                {
                    data: 'Approved',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            var url = baseURL + '/tickets/requestList/all?created_by=' + row.user_id + '&pab_id=' + row.pab_id + '&approval_id=1';
                            return '<a href="' + url + '" target="_blank" class="underline">' + (data || '') + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'Rejected',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            var url = baseURL + '/tickets/requestList/all?created_by=' + row.user_id + '&pab_id=' + row.pab_id + '&approval_id=2';
                            return '<a href="' + url + '" target="_blank" class="underline">' + (data || '') + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'Waiting_for_Prior_Level_Approval',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            var url = baseURL + '/tickets/myRequestList/all?created_by=' + row.user_id + '&pab_id=' + row.pab_id + '&approval_id=4';
                            return '<a href="' + url + '" target="_blank" class="underline">' + (data || '') + '</a>';
                        }
                        return data;
                    }
                }
            ],
            createdRow: function(row, data, dataIndex) {
               $(row).find('td').addClass('b5-text');
            },
            columnDefs: [
                {
                    targets: [3, 4, 5],
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
            drawCallback: function (settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="6">' + config.translations.no_details + '</td></tr>');
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
        t.filters.pab.val(null).trigger("change");
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
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getUserByQuery,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.company,
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

    // ========== Reload Data ==========
    t.filters.fun.reload_ticket_handlers();
    t.filters.fun.reload_pab();

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};