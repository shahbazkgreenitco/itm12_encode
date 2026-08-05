var PendencyTicketReport = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    
    // Ticket Tab Table
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

    // Technician Tab Table
    t.table2 = t.tab.find("#lgTbl2");
    t.tableCover2 = t.table2.closest(".gtable-cover");
    t.lg2 = t.tab.find("#lg2");
    t.pageBtmSummary2 = t.tab.find("#page-btm-summary2");
    t.searchbox2 = t.tab.find(".searchbox2");
    t.searchbtn2 = t.tab.find(".btn-searchbox2");
    t.reload2 = t.tab.find(".btn-reload2");
    t.pageLimiter2 = t.tab.find("#pageLimiter2");
    t.dataTable2 = null;

    // Filters - Common for both tabs
    t.filters = {
        data: { problem_categories: {} },
        wrapper: t.content.find("#FilterModal")
    };

    t.filters.department = t.content.find("#filter_by_department");
    console.log(t.filters.department);
    t.filters.problem_category = t.content.find("#filter_by_problem_category");
    t.filters.sub_category = t.content.find("#filter_by_sub_category");
    t.filters.ticket_handlers = t.content.find("#filter_by_ticket_handlers");
    t.filters.location_id = t.content.find("#filter_location_id");
    t.filters.filter_by_date = t.content.find("#filter_by_date");
    t.filters.daterange = t.content.find("#daterange");
    t.filters.filter_by_company = t.content.find("#filter_by_company");

    // Button references
    t.btn = {};
    t.btn.clear = t.content.find("#btnClrFilter");
    t.btn.filter = t.content.find("#btnSubmitFilter");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || "0"; };

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_department: function() {
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function(data) {
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
                $.get(t.config.url.problem_categories_by_company + "/" + department[0], function(data) {
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
        reload_locations: function() {
            $.each(t.config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.text, k.id));
            });
            t.filters.location_id.trigger("change");
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

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            var handlerVal = t.filters.ticket_handlers.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (handlerVal.length > 0) {
                t.config.other_filters.ticket_handlers = handlerVal;
            }
        }

        // Location filter (multi-select)
        if (t.filters.location_id && t.filters.location_id.val() !== 'null' && t.filters.location_id.val() !== null && t.filters.location_id.val().length > 0) {
            var locVal = t.filters.location_id.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (locVal.length > 0) {
                t.config.other_filters.location_id = locVal;
            }
        }

        // Date Type filter (single select)
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() !== 'null' && t.filters.filter_by_date.val() !== null) {
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        }

        // Company filter (multi-select)
        if (t.filters.filter_by_company && t.filters.filter_by_company.val() !== 'null' && t.filters.filter_by_company.val() !== null && t.filters.filter_by_company.val().length > 0) {
            var companyVal = t.filters.filter_by_company.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (companyVal.length > 0) {
                t.config.other_filters.filter_by_company = companyVal;
            }
        }

        // Date Range filter
        var daterangeVal = t.filters.daterange.val();
        if (daterangeVal && daterangeVal !== '' && daterangeVal !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        filterCount(t.config.other_filters, t.filters.filter_by_date.val(), true);
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

    // ========== Init Ticket DataTable ==========
    t.initDataTable = function() {
        if (t.table.length === 0) {
            console.warn("Table #lgTbl not found");
            return;
        }

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
            order: [[0, 'asc']],
            ajax: {
                url: t.config.url.list_tickets,
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
                        // if (filtered != total) {
                        //     t.pageBtmSummary.html("Available " + filtered + " records (filtered from " + total + " total records)");
                        // } else {
                        //     // t.pageBtmSummary.html("Available Records: " + total);
                        // }
                        return json.data || [];
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover.removeClass("gload");
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="17">' + (config.translations.something_wrong || 'Error loading data') + '</td></tr>');
                }
            },
            columns: [
                { data: 'comp_name' },
                { data: 'department_name' },
                { data: 'category_name' },
                { data: 'sub_category_name' },
                {
                    data: 'assigned_upto_5',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_upto_5days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_5_to_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_5_to_10days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_above_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_10_to_15days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_15_to_30',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_15_to_30days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_above_1_month',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_above_1_monthdays + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var total = (Number(row.assigned_upto_5) || 0) + (Number(row.assigned_5_to_10) || 0) + 
                                       (Number(row.assigned_10_to_15) || 0) + (Number(row.assigned_15_to_30) || 0) + 
                                       (Number(row.assigned_above_1_month) || 0);
                            return '<a href="' + row.assigned_total + '" target="_blank" class="assigned_ticket underline">' + total + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_upto_5',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_upto_5days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_5_to_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_6_to_10days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_above_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_11_to_15days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_above_15',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_16_to_30days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_above_1_month',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_above_30days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var holdTotal = (Number(row.hold_upto_5) || 0) + (Number(row.hold_6_to_10) || 0) + 
                                            (Number(row.hold_11_to_15) || 0) + (Number(row.hold_16_to_30) || 0) + 
                                            (Number(row.hold_above_30) || 0);
                            return '<a href="' + row.hold_total + '" target="_blank" class="hold_ticket underline">' + holdTotal + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var assignedTotal = (Number(row.assigned_upto_5) || 0) + (Number(row.assigned_5_to_10) || 0) + 
                                                (Number(row.assigned_10_to_15) || 0) + (Number(row.assigned_15_to_30) || 0) + 
                                                (Number(row.assigned_above_1_month) || 0);
                            var holdTotal = (Number(row.hold_upto_5) || 0) + (Number(row.hold_6_to_10) || 0) + 
                                            (Number(row.hold_11_to_15) || 0) + (Number(row.hold_16_to_30) || 0) + 
                                            (Number(row.hold_above_30) || 0);
                            var total = assignedTotal + holdTotal;
                            return '<a href="' + row.total + '" target="_blank" class="hold_ticket underline">' + total + '</a>';
                        }
                        return data;
                    }
                }
            ],
            columnDefs: [
                {
                    targets: [4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="17">' + (config.translations.no_details || 'No records found') + '</td></tr>');
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
        window.location = t.config.url.export_ticket_xls + "?q=" + t.config.export_filters;
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
        t.filters.ticket_handlers.val(null).trigger("change");
        t.filters.location_id.val(null).trigger("change");
        t.filters.filter_by_date.val('null').trigger("change");
        t.filters.filter_by_company.val(null).trigger("change");
        t.filters.daterange.val('');
        $('#reportrange span').html('');
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

    // Company Select2 with AJAX
    t.filters.filter_by_company.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_company || "Filter by Company",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getCompanyByUserAccess,
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

    // Department Select2 with AJAX
    t.filters.department.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_department || "Filter by Department",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getDepartmentsWithCompanyByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.filters.filter_by_company.val(),
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.problem_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_Problem_Category || "Select Problem Category",
        dropdownParent: modalBody
    }));

    t.filters.sub_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_Sub_Category || "Select Sub Category",
        dropdownParent: modalBody
    }));

    t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_assign_to || "Select Assign To",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getUserByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.company
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.location_id.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_location || "Filter by Location",
        dropdownParent: modalBody
    }));

    t.filters.filter_by_date.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_date || "Filter by Date",
        dropdownParent: modalBody
    }));

    // ========== Date Range Picker ==========
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        t.filters.wrapper.find('#daterange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        t.filters.wrapper.find('#daterange').val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    if (t.filters.wrapper.find('#daterange').length > 0) {
        t.filters.wrapper.find('#daterange').daterangepicker({
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
    }

    cb(start, end);

    // ========== Reload Data ==========
    t.filters.fun.reload_locations();

    t.filters.department.on("change", function() {
        t.filters.problem_category.empty().trigger("change");
        t.filters.sub_category.empty().trigger("change");
        t.filters.fun.reload_problem_category();
    });
    t.filters.filter_by_company.on("change", function() {
        t.filters.department.empty().trigger("change");
    });

    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function() {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};

var VendorTicketReport = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    
    // Technician Tab Table
    t.table2 = t.tab.find("#lgTbl2");
    t.tableCover2 = t.table2.closest(".gtable-cover");
    t.lg2 = t.tab.find("#lg2");
    t.pageBtmSummary2 = t.tab.find("#page-btm-summary2");
    t.searchbox2 = t.tab.find(".searchbox2");
    t.searchbtn2 = t.tab.find(".btn-searchbox2");
    t.reload2 = t.tab.find(".btn-reload2");
    t.pageLimiter2 = t.tab.find("#pageLimiter2");
    t.export = t.content.find(".btn-download");
    t.dataTable2 = null;
    t.perPage = 10;

    // Filters - Common for both tabs
    t.filters = {
        data: { problem_categories: {} },
        wrapper: t.content.find("#FilterModal1")
    };

    t.filters.department = t.content.find("#filter_by_departments");
    t.filters.problem_category = t.content.find("#filter_by_problem_categorys");
    t.filters.sub_category = t.content.find("#filter_by_sub_categorys");
    t.filters.ticket_handlers = t.content.find("#filter_by_ticket_handler");
    t.filters.location_id = t.content.find("#filter_location_ids");
    t.filters.filter_by_date = t.content.find("#filter_by_dates");
    t.filters.daterange = t.content.find("#daterange");
    t.filters.filter_by_company = t.content.find("#filter_by_companys");

    // Button references
    t.btn = {};
    t.btn.clear = t.content.find("#btnClrFilter2");
    t.btn.filter = t.content.find("#btnSubmitFilter2");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || "0"; };

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_department: function() {
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function(data) {
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
                $.get(t.config.url.problem_categories_by_company + "/" + department[0], function(data) {
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
        reload_locations: function() {
            $.each(t.config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.text, k.id));
            });
            t.filters.location_id.trigger("change");
        }
    };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox2.val());
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

        // Ticket Handlers filter (multi-select)
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val() !== 'null' && t.filters.ticket_handlers.val() !== null && t.filters.ticket_handlers.val().length > 0) {
            var handlerVal = t.filters.ticket_handlers.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (handlerVal.length > 0) {
                t.config.other_filters.ticket_handlers = handlerVal;
            }
        }

        // Location filter (multi-select)
        if (t.filters.location_id && t.filters.location_id.val() !== 'null' && t.filters.location_id.val() !== null && t.filters.location_id.val().length > 0) {
            var locVal = t.filters.location_id.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (locVal.length > 0) {
                t.config.other_filters.location_id = locVal;
            }
        }

        // Date Type filter (single select)
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() !== 'null' && t.filters.filter_by_date.val() !== null) {
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        }

        // Company filter (multi-select)
        if (t.filters.filter_by_company && t.filters.filter_by_company.val() !== 'null' && t.filters.filter_by_company.val() !== null && t.filters.filter_by_company.val().length > 0) {
            var companyVal = t.filters.filter_by_company.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (companyVal.length > 0) {
                t.config.other_filters.filter_by_company = companyVal;
            }
        }

        // Date Range filter
        var daterangeVal = t.filters.daterange.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date !== '' && t.filters.filter_by_date !== 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        
        if(Object.keys(t.config.other_filters).length > 0) {
            t.content.find('#filter-count-badge-report').text(Number(Object.keys(t.config.other_filters).length) - 1);
            t.content.find('#filter-count-badge-report').removeClass('d-none');
        } else {
            t.content.find('#filter-count-badge-report').addClass('d-none');
        }

    };

    // ========== Search Function ==========
    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox2.val());
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            if (t.dataTable2) t.dataTable2.draw();
        } else if (target.tagName == "BUTTON" || target.closest(".btn-filter").length) {
            t.cache_filter_values();
            $("#FilterModal1").modal('hide');
            if (t.dataTable2) t.dataTable2.draw();
        }
    };

    // ========== Reset Function ==========
    t.reset = function() {
        t.perPage = parseInt(t.pageLimiter2.val()) || 10;
        if (t.dataTable2) {
            t.dataTable2.page.len(t.perPage);
        }
    };

    // ========== Init Technician DataTable ==========
    t.initDataTable = function() {
        if (t.table2.length === 0) {
            console.warn("Table #lgTbl2 not found");
            return;
        }

        t.dataTable2 = t.table2.DataTable({
            autoWidth: false,
            colReorder: true,
            stateSave: true,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
            lengthChange: false,
            searching: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [[0, 'asc']],
            ajax: {
                url: t.config.url.getTicketnicianPendencyTicketReport,
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
                        // if (filtered != total) {
                        //     t.pageBtmSummary2.html("Available " + filtered + " records (filtered from " + total + " total records)");
                        // } else {
                        //     // t.pageBtmSummary2.html("Available Recordsaaaaaa: " + total);
                        // }
                        return json.data || [];
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover2.removeClass("gload");
                    t.lg2.html('<tr class="no-record-found"><td class="text-center" colspan="15">' + (config.translations.something_wrong || 'Error loading data') + '</td></tr>');
                }
            },
            columns: [
                { data: 'comp_name' },
                { data: 'username' },
                {
                    data: 'assigned_upto_5',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_upto_5days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_5_to_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_5_to_10days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_10_to_15',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_10_to_15days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_15_to_30',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_15_to_30days + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'assigned_above_1_month',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.assigned_above_1_monthdays + '" target="_blank" class="assigned_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var total = (Number(row.assigned_upto_5) || 0) + (Number(row.assigned_5_to_10) || 0) + 
                                       (Number(row.assigned_10_to_15) || 0) + (Number(row.assigned_15_to_30) || 0) + 
                                       (Number(row.assigned_above_1_month) || 0);
                            return '<a href="' + row.assigned_total + '" target="_blank" class="assigned_ticket underline">' + total + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_upto_5',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_upto_5days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_6_to_10',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_6_to_10days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_11_to_15',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_11_to_15days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_16_to_30',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_16_to_30days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'hold_above_30',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.hold_above_30days + '" target="_blank" class="hold_ticket underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var holdTotal = (Number(row.hold_upto_5) || 0) + (Number(row.hold_6_to_10) || 0) + 
                                            (Number(row.hold_11_to_15) || 0) + (Number(row.hold_16_to_30) || 0) + 
                                            (Number(row.hold_above_30) || 0);
                            return '<a href="' + row.hold_total + '" target="_blank" class="hold_ticket underline">' + holdTotal + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var assignedTotal = (Number(row.assigned_upto_5) || 0) + (Number(row.assigned_5_to_10) || 0) + 
                                                (Number(row.assigned_10_to_15) || 0) + (Number(row.assigned_15_to_30) || 0) + 
                                                (Number(row.assigned_above_1_month) || 0);
                            var holdTotal = (Number(row.hold_upto_5) || 0) + (Number(row.hold_6_to_10) || 0) + 
                                            (Number(row.hold_11_to_15) || 0) + (Number(row.hold_16_to_30) || 0) + 
                                            (Number(row.hold_above_30) || 0);
                            var total = assignedTotal + holdTotal;
                            return '<a href="' + row.total + '" target="_blank" class="hold_ticket underline">' + total + '</a>';
                        }
                        return data;
                    }
                }
            ],
            columnDefs: [
                {
                    targets: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
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
                    t.lg2.html('<tr class="no-record-found"><td class="text-center" colspan="15">' + (config.translations.no_details || 'No records found') + '</td></tr>');
                }
                t.tableCover2.removeClass("gload");
            },
            preDrawCallback: function(settings) {
                t.tableCover2.addClass("gload");
            }
        });

        t.dataTable2.page.len(t.perPage);
    };

    // ========== Download Functions ==========
    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.getTicketnicianPendencyTicketDownload + "?q=" + t.config.export_filters;
    };

    // ========== Load Function ==========
    t.load = function() {
        if (t.dataTable2) {
            t.dataTable2.draw();
        }
    };

    // ========== Clear Filters ==========
    t.btn.clear.click(function() {
        t.filters.department.val(null).trigger("change");
        t.filters.problem_category.val(null).trigger("change");
        t.filters.sub_category.val(null).trigger("change");
        t.filters.ticket_handlers.val(null).trigger("change");
        t.filters.location_id.val(null).trigger("change");
        t.filters.filter_by_date.val('null').trigger("change");
        t.filters.filter_by_company.val(null).trigger("change");
        t.filters.daterange.val('');
        t.filters.wrapper.find('#reportrange span').html('');
        t.config.other_filters = {};
        t.config.search = '';
        t.searchbox2.val('');
        t.content.find('#filter-count-badge-report').addClass('d-none');
        resetFilterCount();
        if (t.dataTable2) t.dataTable2.draw();
        t.filters.wrapper.modal('hide');
    });

    // ========== Event Bindings ==========
    t.searchbox2.on("keypress", $.proxy(t.search, t));
    t.reload2.on("click", $.proxy(t.load, t));
    t.searchbtn2.on("click", $.proxy(t.search, t));
    t.content.on("click", ".btn-download-report", $.proxy(t.download, t));
    t.btn.filter.on("click", $.proxy(t.search, t));

    t.pageLimiter2.on("change", function() {
        t.reset();
        if (t.dataTable2) {
            t.dataTable2.draw();
        }
    });
    // ========== Date Range Picker ==========
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        t.filters.wrapper.find('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        t.filters.daterange.val(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
    }

    if (t.filters.wrapper.find('#reportrange').length > 0) {
        t.filters.wrapper.find('#reportrange').daterangepicker({
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
    }

    cb(start, end);

    t.content.on('click','.btn-open-filter2', function() {
        t.filters.wrapper.modal('show');
    });
    
    // ========== Initialize ==========
    t.filters.fun.reload_locations();
    t.initDataTable();

        t.filters.filter_by_company.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_company || "Filter by Company",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getCompanyByUserAccess,
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
    var select2Opts = { width: "100%" };
    var modalBody = $("#FilterModal1 .modal-body");

    // Department Select2 with AJAX
    t.filters.department.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_department || "Filter by Department",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getDepartmentsWithCompanyByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.filters.filter_by_company.val(),
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.problem_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_Problem_Category || "Select Problem Category",
        dropdownParent: modalBody
    }));

    t.filters.sub_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_Sub_Category || "Select Sub Category",
        dropdownParent: modalBody
    }));

    t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
        placeholder: config.translations.select_assign_to || "Select Assign To",
        dropdownParent: modalBody,
        ajax: {
            url: t.config.getUserByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.company
                };
            },
            delay: 300
        },
        allowClear: true
    }));

    t.filters.location_id.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_location || "Filter by Location",
        dropdownParent: modalBody
    }));

    t.filters.filter_by_date.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_date || "Filter by Date",
        dropdownParent: modalBody
    }));

        // ========== Reload Data ==========
    t.filters.fun.reload_locations();

    t.filters.department.on("change", function() {
        t.filters.problem_category.empty().trigger("change");
        t.filters.sub_category.empty().trigger("change");
        t.filters.fun.reload_problem_category();
    });
    t.filters.filter_by_company.on("change", function() {
        t.filters.department.empty().trigger("change");
    });

    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));
};