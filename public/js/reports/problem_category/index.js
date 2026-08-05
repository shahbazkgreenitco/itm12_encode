var CategoryReport = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find("#category_privillege");
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
    t.filters.problem_category = $("#filter_by_problem_category");
    t.filters.sub_category = $("#filter_by_sub_category");
    t.filters.authority_board = $("#filter_by_authority_board");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

    // ========== Helper Functions ==========
    t._s = function(v) { return v || ""; };

    // ========== Filter Functions ==========
    t.filters.fun = {
        reload_department: function() {
            $.get(t.config.url.departments_by_company + "/" + t.config.user.company_id, function(data) {
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
        reload_board: function() {
            $.each(t.config.pabs, function(i, k) {
                t.filters.authority_board.append(new Option(k.name, k.id, false, false));
            });
            t.filters.authority_board.trigger("change");
        },
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

        // Authority Board filter (multi-select)
        if (t.filters.authority_board && t.filters.authority_board.val() !== 'null' && t.filters.authority_board.val() !== null && t.filters.authority_board.val().length > 0) {
            var boardVal = t.filters.authority_board.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (boardVal.length > 0) {
                t.config.other_filters.authority_board = boardVal;
            }
        }

        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters);
    };

    // ========== Search Function ==========
    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox.val());
            if (v === false) {
                t.config.search = "";
                var data = {'msg': 'Please enter a valid value for search'};
                sweetAlert('center', 'success', data);
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="4">Error loading data. Please try again.</td></tr>');
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return row.parent_id == null ? row.pt_name : row.pc_name;
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return row.parent_id != null ? row.pt_name : '';
                        }
                        return data;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return row.pc_day != null ? row.pc_day : row.pt_day;
                        }
                        return data;
                    }
                },
                { data: 'pab' }
            ],
            columnDefs: [
                {
                    targets: [2],
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="4">' + config.translations.no_details + '</td></tr>');
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
        t.filters.authority_board.val(null).trigger("change");
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
        dropdownParent: modalBody
    }));

    t.filters.problem_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Problem_Category || "Filter by Problem Category",
        dropdownParent: modalBody
    }));

    t.filters.sub_category.select2($.extend({}, select2Opts, {
        placeholder: config.translations.Filter_By_Sub_Category || "Filter by Sub Category",
        dropdownParent: modalBody
    }));

    t.filters.authority_board.select2($.extend({}, select2Opts, {
        placeholder: config.translations.filter_by_authority_board || "Filter by Authority Board",
        dropdownParent: modalBody
    }));

    // ========== Reload Data ==========
    t.filters.fun.reload_department();
    t.filters.fun.reload_board();

    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category, t));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));

    // ========== Filter Button Click ==========
    t.filterbtn.on('click', function() {
        t.filters.wrapper.modal('show');
    });

    // ========== Initialize DataTable ==========
    t.initDataTable();
};