var CategoryAgeningReport = function(config) {
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

    // Category toggle buttons
    t.parent_category_wise_report = t.content.find("#parent_category_wise_report");
    t.category_wise_report = t.content.find("#category_wise_report");

    // Header elements
    t.prob_cate_header = t.table.find("#prob_cate_header");
    t.sub_cate_header = t.table.find("#sub_cate_header");

    // Filters
    t.filters = {
        data: { problem_categories: {} },
        wrapper: $("#FilterModal")
    };

    t.filters.department = $("#filter_by_department");
    t.filters.problem_category = $("#filter_by_problem_category");
    t.filters.sub_category = $("#filter_by_sub_category");
    t.filters.from_date = $("#filter_by_from_date");
    t.filters.to_date = $("#filter_by_to_date");

    // Button references
    t.btn = {};
    t.btn.clear = $("#btnClrFilter");
    t.btn.filter = $(".btn-filter");

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
        }
    };

    t._s = function(v) { return v || ""; };

    t.toggleParentCategoryWiseReport = function(e) {
        if (typeof e != undefined) {
            e.preventDefault();
        }
        
        var isPCategoryOn = t.parent_category_wise_report.hasClass("pcategory-on");
        
        if (isPCategoryOn) {

            t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="fa fa-eye"></i> Show Categories');

            t.dataTable.column(1).visible(false);
   
            t.prob_cate_header.addClass('hide');

            if (t.dataTable.column(2).visible()) {
                t.dataTable.column(2).visible(false);
                t.sub_cate_header.addClass('hide');
                t.category_wise_report.removeClass("category-on").html('<i class="fa fa-eye"></i> Show Sub Categories');
            }
        } else {

            t.parent_category_wise_report.addClass("pcategory-on").html('<i class="fa fa-eye-slash"></i> Hide Categories');
  
            t.dataTable.column(1).visible(true);
  
            t.prob_cate_header.removeClass('hide');

            if (t.dataTable.column(2).visible()) {
                t.dataTable.column(2).visible(false);
                t.sub_cate_header.addClass('hide');
                t.category_wise_report.removeClass("category-on").html('<i class="fa fa-eye"></i> Show Sub Categories');
            }
        }
        
        t.cache_filter_values();
        t.dataTable.draw();
    };

    t.toggleCategoryWiseReport = function(e) {
        if (typeof e != undefined) {
            e.preventDefault();
        }
        
        var isCategoryOn = t.category_wise_report.hasClass("category-on");
        
        if (isCategoryOn) {

            t.category_wise_report.removeClass("category-on").html('<i class="fa fa-eye"></i> Show Sub Categories');
    
            t.dataTable.column(1).visible(false);
   
            t.dataTable.column(2).visible(false);
    
            t.prob_cate_header.addClass('hide');
            t.sub_cate_header.addClass('hide');
         
            if (t.parent_category_wise_report.hasClass("pcategory-on")) {
                t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="fa fa-eye"></i> Show Categories');
            }
        } else {

            t.category_wise_report.addClass("category-on").html('<i class="fa fa-eye-slash"></i> Hide Sub Categories');
         
            t.dataTable.column(1).visible(true);
      
            t.dataTable.column(2).visible(true);
      
            t.prob_cate_header.removeClass('hide');
            t.sub_cate_header.removeClass('hide');
            if (t.parent_category_wise_report.hasClass("pcategory-on")) {
                t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="fa fa-eye"></i> Show Categories');
            }
        }
        
        t.cache_filter_values();
        t.dataTable.draw();
    };


    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};


        if (t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null && t.filters.department.val().length > 0) {
            var deptVal = t.filters.department.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (deptVal.length > 0) {
                t.config.other_filters.department = deptVal;
            }
        }


        if (t.filters.problem_category && t.filters.problem_category.val() !== 'null' && t.filters.problem_category.val() !== null && t.filters.problem_category.val().length > 0) {
            var probCatVal = t.filters.problem_category.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (probCatVal.length > 0) {
                t.config.other_filters.problem_category = probCatVal;
            }
        }


        if (t.filters.sub_category && t.filters.sub_category.val() !== 'null' && t.filters.sub_category.val() !== null && t.filters.sub_category.val().length > 0) {
            var subCatVal = t.filters.sub_category.val().filter(function(v) { return v != 0 && v != 'null'; });
            if (subCatVal.length > 0) {
                t.config.other_filters.sub_category = subCatVal;
            }
        }


        if (t.filters.from_date && t.filters.from_date.val() && t.filters.from_date.val() !== 'null') {
            t.config.other_filters.from_date = t.filters.from_date.val();
        }


        if (t.filters.to_date && t.filters.to_date.val() && t.filters.to_date.val() !== 'null') {
            t.config.other_filters.to_date = t.filters.to_date.val();
        }

        var jobj = { 
            "search": t.config.search, 
            "other_filters": t.config.other_filters,
            "category": t.category_wise_report.hasClass("category-on"),
            "pcategory": t.parent_category_wise_report.hasClass("pcategory-on")
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        var filterCount = Object.keys(t.config.other_filters).length;
        if(Object.keys(t.config.other_filters).length > 0) {

            $('.filter-count-badge').text(Object.keys(t.config.other_filters).length);
            $('.filter-count-badge').removeClass('d-none');
        } else {
            $('.filter-count-badge').addClass('d-none');
        }
    };


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


    t.reset = function() {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        if (t.dataTable) {
            t.dataTable.page.len(t.perPage);
        }
    };


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
                    d.category = t.category_wise_report.hasClass("category-on");
                    d.pcategory = t.parent_category_wise_report.hasClass("pcategory-on");
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="8">' + (config.translations.something_wrong || 'Error loading data') + '</td></tr>');
                }
            },
            columns: [
                { data: 'name' },
                { 
                    data: 'prob_cat_name',
                    visible: false // Hidden by default
                },
                { 
                    data: 'sub_cat_name',
                    visible: false // Hidden by default
                },
                {
                    data: 'total_tickets',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.total + '" target="_blank" class="category_wise_agening">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'active_created_at_less_than_7days',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.active_less_than_7days + '" target="_blank" class="category_wise_agening">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'inactive_created_at_less_than_7days',
                    render: function (data, type, row) {
                        console.log('Rendering inactive_created_at_less_than_7days:', data, type);
                        if (type === 'display') {
                            return '<a href="' + row.inactive_less_than_7days + '" target="_blank" class="category_wise_agening">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'active_created_at_more_than_7days',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.active_more_than_7days + '" target="_blank" class="category_wise_agening">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                {
                    data: 'inactive_created_at_more_than_7days',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            return '<a href="' + row.inactive_more_than_7days + '" target="_blank" class="category_wise_agening">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                }
            ],
            pageLength: t.perPage,
            lengthChange: false,
            ordering: false,
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
                    t.lg.html('<tr class="no-record-found"><td class="text-center b5-text" colspan="8">' + (config.translations.no_details || 'No records found') + '</td></tr>');
                }
                t.tableCover.removeClass("gload");
            },
            preDrawCallback: function(settings) {
                t.tableCover.addClass("gload");
            }
        });

        t.dataTable.page.len(t.perPage);
    };

  
    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };


    t.load = function() {
        if (t.dataTable) {
            t.dataTable.draw();
        }
    };


    t.btn.clear.click(function() {
        t.filters.department.val(null).trigger("change");
        t.filters.problem_category.val(null).trigger("change");
        t.filters.sub_category.val(null).trigger("change");
        t.filters.from_date.val(null).trigger("change");
        t.filters.to_date.val(null).trigger("change");
        t.config.other_filters = {};
        t.config.search = '';
        t.searchbox.val('');
        $('.filter-count-badge').addClass('d-none');
        if (t.dataTable) t.dataTable.draw();
        t.filters.wrapper.modal('hide');
    });


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


    if ($.fn.datetimepicker) {

        $('#filter_by_from_date').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-left",
            showMeridian: true,
            minuteStep: 5,
            startView: 2,
            maxView: 3
        }).on('changeDate', function(e) {
            var fromDate = new Date(e.date);
            $('#filter_by_to_date').datetimepicker('setStartDate', fromDate);
            $('#filter_by_to_date').datetimepicker('setDate', fromDate);
        });


        $('#filter_by_to_date').datetimepicker({
            format: 'DD-MM-YYYY HH:mm',
            autoclose: true,
            todayBtn: true,
            pickerPosition: "bottom-left",
            showMeridian: true,
            minuteStep: 5,
            startView: 2,
            maxView: 3
        });
        

        $('#fromDatePicker .input-group-addon').on('click', function() {
            $('#filter_by_from_date').focus();
        });
        
        $('#toDatePicker .input-group-addon').on('click', function() {
            $('#filter_by_to_date').focus();
        });
        
    } else if ($.fn.datepicker) {

        $('#filter_by_from_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayBtn: true
        }).on('changeDate', function(e) {
            var fromDate = new Date(e.date);
            $('#filter_by_to_date').datepicker('setStartDate', fromDate);
        });

        $('#filter_by_to_date').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayBtn: true
        });
        
        $('#fromDatePicker .input-group-addon').on('click', function() {
            $('#filter_by_from_date').focus();
        });
        
        $('#toDatePicker .input-group-addon').on('click', function() {
            $('#filter_by_to_date').focus();
        });
    } else {

        t.filters.from_date.attr('type', 'date');
        t.filters.to_date.attr('type', 'date');
    }


    t.filters.fun.reload_department();

    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category, t));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));


    t.parent_category_wise_report.on("click", $.proxy(t.toggleParentCategoryWiseReport, t));
    t.category_wise_report.on("click", $.proxy(t.toggleCategoryWiseReport, t));


    t.filterbtn.on('click', function () {
        // t.btn.clear.trigger('click');
        t.filters.wrapper.modal('show');
    });


    t.initDataTable();
};