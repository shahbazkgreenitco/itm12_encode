var ExceptionAccessReport = function(config) {
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
        data: { problem_categories: {} },
        wrapper: $("#FilterModal")
    };

    t.filters.department = $("#filter_by_department");
    t.filters.problem_category = $("#filter_By_Problem_Category");
    t.filters.sub_category = $("#filter_By_Sub_Category");
    t.filters.status = $("#filter_by_status");
    t.filters.location_id = $("#filter_by_location");
    t.filters.ticket_handlers = $("#filter_by_ticket_handlers");
    t.filters.based_on = $("#filter_by_date");
    t.filters.daterange = $("#daterange");
    t.filters.filter_hierarchy_approval = $("#filter_hierarchy_approval");
    t.filters.filter_by_sr_status = $("#filter_by_sr_status");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || ""; };

    t.truncate = function(source, size) {
        return source.length > size ? source.slice(0, size - 1) + "…" : source;
    };

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_department: function() {
            $.get(t.config.url.departments_based_on_privilage + "/" + t.config.user.company_id, function(data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
        },
        reload_problem_category: function() {
            t.filters.problem_category.empty();
            var department = t.filters.department.val();
            if (department && department.length > 0 && department[0] != "null" && department[0] != 0) {
                $.get(t.config.url.problem_categories_by_company + "/" + department[0] + "?b=report", function(data) {
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
            if (prblm && prblm.length > 0 && prblm[0] != "null" && prblm[0] != 0) {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm[0]], function(i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {}
            }
        },
        reload_ticket_handlers: function() {
            var select2Opts = { width: "100%" };
            t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.ticket_handlers.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: 'Filter By Creator'
            }));
        },
        reload_locations: function() {
            $.each(t.config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        },
        reload_status: function() {
            $.each(t.config.statuses, function(i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        }
    };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};

        // Department filter (multi-select)
        if (t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null && t.filters.department.val().length > 0) {
            var deptVal = t.filters.department.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (deptVal.length > 0) {
                t.config.other_filters.department = deptVal;
            }
        }

        // Problem Category filter (multi-select)
        if (t.filters.problem_category && t.filters.problem_category.val() !== 'null' && t.filters.problem_category.val() !== null && t.filters.problem_category.val().length > 0) {
            var probCatVal = t.filters.problem_category.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (probCatVal.length > 0) {
                t.config.other_filters.problem_category = probCatVal;
            }
        }

        // Sub Category filter (multi-select)
        if (t.filters.sub_category && t.filters.sub_category.val() !== 'null' && t.filters.sub_category.val() !== null && t.filters.sub_category.val().length > 0) {
            var subCatVal = t.filters.sub_category.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (subCatVal.length > 0) {
                t.config.other_filters.sub_category = subCatVal;
            }
        }

        // Status filter (multi-select)
        if (t.filters.status && t.filters.status.val() !== 'null' && t.filters.status.val() !== null && t.filters.status.val().length > 0) {
            var statusVal = t.filters.status.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (statusVal.length > 0) {
                t.config.other_filters.status = statusVal;
            }
        }

        // Location filter (multi-select)
        if (t.filters.location_id && t.filters.location_id.val() !== 'null' && t.filters.location_id.val() !== null && t.filters.location_id.val().length > 0) {
            var locVal = t.filters.location_id.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (locVal.length > 0) {
                t.config.other_filters.location = locVal;
            }
        }

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            var handlerVal = t.filters.ticket_handlers.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (handlerVal.length > 0) {
                t.config.other_filters.ticket_handlers = handlerVal;
            }
        }

        // Based On filter (single select)
        if (t.filters.based_on && t.filters.based_on.val() !== 'null' && t.filters.based_on.val() !== null) {
            t.config.other_filters.based_on = t.filters.based_on.val();
        }

        // Date Range filter
        var daterangeVal = t.filters.daterange.val();
        if (daterangeVal && daterangeVal !== '' && daterangeVal !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        // SR Status filter (multi-select)
        if (t.filters.filter_by_sr_status && t.filters.filter_by_sr_status.val() !== 'null' && t.filters.filter_by_sr_status.val() !== null && t.filters.filter_by_sr_status.val().length > 0) {
            var srStatusVal = t.filters.filter_by_sr_status.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (srStatusVal.length > 0) {
                t.config.other_filters.sr_status = srStatusVal;
            }
        }

        // Hierarchy Approval filter (single select)
        if (t.filters.filter_hierarchy_approval && t.filters.filter_hierarchy_approval.val() !== 'null' && t.filters.filter_hierarchy_approval.val() !== null) {
            t.config.other_filters.hierarchy_approval = t.filters.filter_hierarchy_approval.val();
        }

        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters
        };

        t.config.export_filters = btoa(JSON.stringify(jobj));
        var countFilters = $.extend({}, t.config.other_filters);
        if (countFilters.based_on || countFilters.daterange) {
            delete countFilters.based_on;
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="12">' + config.translations.something_wrong + '</td></tr>');
                }
            },
            columns: [
                {
                    data: 'tag',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var requestInfo = config.url.requestInfo + "/" + row.id;
                            return '<a href="' + requestInfo + '" target="_blank" class="url_link">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<span data-toggle="tooltip" data-placement="top" title="Device name">' + t._s(row.device) + '</span><br><span data-toggle="tooltip" data-placement="top" title="Asset Tag">' + t._s(row.asset_tag) + '</span>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<span data-toggle="tooltip" data-placement="top" title="Employee Name">' + t._s(row.user_name) + '</span><br><span data-toggle="tooltip" data-placement="top" title="Email ID">' + t._s(row.email) + '</span><br><span data-toggle="tooltip" data-placement="top" title="Employee Number">' + t._s(row.employee_num) + '</span>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<span data-toggle="tooltip" data-placement="top" title="Department">' + t._s(row.depart_name) + '</span><br><span data-toggle="tooltip" data-placement="top" title="Problem Category">' + t._s(row.pc_name) + '</span><br><span data-toggle="tooltip" data-placement="top" title="Sub Category">' + t._s(row.sc_name) + '</span>';
                        }
                        return data;
                    }
                },
                { data: 'loc_name' },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var maxDays = (row.sc_days !== '' && row.sc_days !== null && row.sc_days !== undefined)  ? row.sc_days : row.pc_days;
                            return maxDays || '';
                        }
                        return data;
                    },
                    defaultContent: ''
                },
                { data: 'approved_day' },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var startDate = '';
                            var endDate = '';
                            if (row.custom_fields) {
                                try {
                                    var dates = JSON.parse(row.custom_fields);
                                    var values = Object.values(dates);
                                    startDate = values[0] || '';
                                    endDate = values[1] || '';
                                } catch(e) {}
                            }
                            return t._s(startDate) + '<br>' + t._s(endDate);
                        }
                        return data;
                    }
                },
                { data: 'created_at_format' },
                { data: 'approved_at_format' },
                { data: 'status_name' },
                { data: 'req_status_name' }
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
        t.filters.location_id.val(null).trigger("change");
        t.filters.ticket_handlers.val(null).trigger("change");
        t.filters.based_on.val('null').trigger("change");
        t.filters.daterange.val('');
        t.filters.filter_by_sr_status.val(null).trigger("change");
        t.filters.filter_hierarchy_approval.val('null').trigger("change");
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
            url: t.config.url.departments_by_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
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
            url: t.config.getUserByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.based_on.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Date",
        dropdownParent: modalBody
    }));

    t.filters.filter_by_sr_status.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Status",
        dropdownParent: modalBody
    }));

    t.filters.filter_hierarchy_approval.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Approval Mode",
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
    t.filters.fun.reload_department();
    t.filters.fun.reload_ticket_handlers();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_status();

    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category, t));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function() {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};