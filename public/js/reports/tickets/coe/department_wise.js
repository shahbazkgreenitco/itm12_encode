var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $(".content");
    t.table = t.content.find("#lgTbl");
    t.lg = t.table.find("#lg");
    t.sortbtns = t.content.find('#srqSortDrop');
    t.shortItems = t.content.find('.amg-sort-menu');
    t.sortAction = t.sortbtns.find('.sort-action');
    t.dropdownAction = t.sortbtns.find('.dropdown-action');
    t.searchbox = t.content.find(".searchbox");
    t.reload = t.content.find(".btn-reload");
    t.export = t.content.find(".btn-download");
    t.export_pdf = t.content.find(".btn-download-pdf");
    t.filterbtn = t.content.find(".btn-open-filter");
    t.perPage = 10;
    t.pageLimiter = t.content.find("#pageLimiter");
    t.parent_category_wise_report = t.content.find("#parent_category_wise_report");
    t.category_wise_report = t.content.find("#category_wise_report");
    t.currentPage = 1;
    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.content.find("#FilterModal")
    };
    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter"),
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date"),
    t.filters.daterange = t.filters.wrapper.find("#daterange"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category"),
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category"),
    t.filters.fun = {
        reload_department: function() {
            t.filters.department.empty().append(new Option(config.translations.No_Filter, null, false, false));
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function(data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
            t.filters.department.trigger("change");
        },
        reload_problem_category: function() {
            t.filters.problem_category.empty();
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
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
            t.filters.problem_category.trigger("change");
        },
        reload_sub_category: function() {
            t.filters.sub_category.empty();
            var prblm = t.filters.problem_category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(prblm, function(i, v) {
                        $.each(t.filters.data.problem_categories["sc" + v], function(i, k) {
                            t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                        });
                    });
                } catch (e) {

                }
            }
            t.filters.sub_category.trigger("change");
        },
    };

    t.toggleParentCategoryWiseReport = function(e) {
        if(typeof e != undefined) {
            e.preventDefault();
        }
        if( t.parent_category_wise_report.hasClass("pcategory-on") == true) {
            t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="bi bi-eye"></i> '+ config.translations.show_parent_category);
            t.table.find(".parent_category_wise_report").hide();
        }
        else {
            t.parent_category_wise_report.addClass("pcategory-on").html('<i class="bi bi-eye-slash"></i> '+ config.translations.hide_parent_category);
            t.table.find(".parent_category_wise_report").show();
            t.category_wise_report.removeClass("category-on").html('<i class="bi bi-eye"></i> '+ config.translations.show_sub_category);
            t.table.find(".category_wise_report").hide();
        }
        t.load();
    };

    t.toggleCategoryWiseReport = function(e) {
        if(typeof e != undefined) {
            e.preventDefault();
        }

        if(t.category_wise_report.hasClass("category-on") == true) {
            t.category_wise_report.removeClass("category-on").html('<i class="bi bi-eye"></i> '+ config.translations.show_sub_category);
            t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="bi bi-eye"></i> '+ config.translations.show_parent_category);
            t.table.find(".category_wise_report").hide();
        }
        else {
            t.category_wise_report.addClass("category-on").html('<i class="bi bi-eye-slash"></i> '+ config.translations.hide_sub_category);
            t.table.find(".category_wise_report").show();
            t.parent_category_wise_report.removeClass("pcategory-on").html('<i class="bi bi-eye"></i> '+ config.translations.show_parent_category);
            t.table.find(".parent_category_wise_report").hide();
        }

        t.load();
    };

    t.reset = function () {
        t.perPage = parseInt(t.pageLimiter.val());
        t.currentPage = 1;
        t.load();
    };

    t.renderSortDropdown = function () {
        var $dropdown =t.shortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            $dropdown.append(`
                <li>
                    <a href="javascript:void(0)"
                    class="dropdown-item ${isActive ? 'active' : ''}"
                    data-id="${d.id}"
                    data-bs-toggle="tooltip"
                    data-bs-placement="left"
                    data-bs-original-title="${d.text}">
                        <span class="like-radio"></span>
                        <span class="flex-grow-1">${d.text}</span>
                        ${isActive
                            ? `<i class="bi ${s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down'}"></i>`
                            : ''
                        }
                    </a>
                </li>
            `);
        });
        $dropdown.find('[data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this, {
                container: 'body'
            });
        });
    };

       /* Sort Direction */
    t.sortAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();

        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;

        t.updateSortDirectionIcon();

        t.renderSortDropdown();
        t.load();
    });

    /* Open Dropdown */
    t.dropdownAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.shortItems.toggleClass("show");
    });

    t.updateSortDirectionIcon = function () {
        $("#sortDirectionIcon").removeClass("bi-sort-up bi-sort-down").addClass(t.config.sort_dir.dir == 1? "bi-sort-up": "bi-sort-down");
    };

    /* Select Sort Field */
    t.shortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.config.sort_dir.id = $(this).data("id");
        t.shortItems.removeClass("show");
        t.renderSortDropdown();
        t.load();
    });

    /* Keep dropdown open when clicking inside */
    t.shortItems.on("click", function (e) {
        e.stopPropagation();
    });

    /* Close outside click */
    $(document).on("click", function () {
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.shortItems.removeClass("show");
    });
    t.renderSortDropdown();
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v;
        t.config.other_filters = {};
        if( t.filters.department.val() && t.filters.department.val() != 'null') {
            t.config.other_filters.department = t.filters.department.val();
        }
        if( t.filters.problem_category.val() && t.filters.problem_category.val() != 'null') {
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        }
        if( t.filters.sub_category.val() && t.filters.sub_category.val() != 'null') {
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if( t.filters.based_on.val() && t.filters.based_on.val() != 'null') {
            t.config.other_filters.based_on = t.filters.based_on.val();
        }
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null'){
            t.config.other_filters.daterange = t.filters.daterange.val();
        }
        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters,"category":t.category_wise_report.hasClass("category-on"),"pcategory":t.parent_category_wise_report.hasClass("pcategory-on")};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
        t.filters.wrapper.modal("hide");
    };

    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if(e.keyCode == 13 || $(this).is("span")) {
            var v = $.trim(t.searchbox.val());
            if(v === false) {
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.currentPage = 1;
            t.load();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.currentPage = 1;
            t.load();
        }
    };

    t._s = function(v) {
        if(v == 0) return v;
        return v || "";
    };

    t.renderList = function(i, d) {
        var fc = "<tr>";
        if(t.parent_category_wise_report.hasClass("pcategory-on") == true) {
            $.each(d, function(i,v) {
                if(i != 'id' && i !='pc_id') {
                    if(i == 'tot') {
                            fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?department_id=' + d.id + '&parent_id=' + d.pc_id + '&department_filters=' + t.config.export_filters + '&pcategory-on=' + t.parent_category_wise_report.hasClass("pcategory-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                    } else {
                        if(i == 'dep_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";

                        }else if(i == 'responsetime'){
                            fc += "<td class='b5-text'>"+d.averageResponseTime+"</td>";
                        }else if(i == 'openMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.openMoreThan7DaysTickets+"</td>";
                        }else if( i == 'ClosedMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.ClosedMoreThan7DaysTickets+"</td>";
                        }else if(i =='pc_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";
                        } else {
                            if((i == 'averageResponseTime') || (i == 'ClosedMoreThan7DaysTickets') || (i == 'openMoreThan7DaysTickets')){
                                fc += "";
                            }else{
                                var split = i.split("_");
                                if(split.length == 3) {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?ticket_type='+split[2]+'&department_id=' + d.id + '&parent_id=' + d.pc_id + '&department_filters=' + t.config.export_filters +'&pcategory-on=' + t.parent_category_wise_report.hasClass("pcategory-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                } else {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?status_id='+split[1]+'&&department_id=' + d.id + '&parent_id=' + d.pc_id + '&department_filters=' + t.config.export_filters +'&pcategory-on=' + t.parent_category_wise_report.hasClass("pcategory-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                }
                            }

                        }
                    }
                }
            });
        }
        else if(t.category_wise_report.hasClass("category-on") == true) {
            $.each(d, function(i,v) {
                if(i != 'id' && i !='sc_id' && i !='pc_id') {
                    if(i == 'tot') {
                            fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?department_id=' + d.id + '&parent_id=' + d.pc_id + '&sub_id=' + d.sc_id + '&department_filters=' + t.config.export_filters + '&category-on=' + t.category_wise_report.hasClass("category-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                    }
                    else{
                        if(i == 'dep_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";

                        }else if(i == 'responsetime'){
                            fc += "<td class='b5-text'>"+d.averageResponseTime+"</td>";
                        }else if(i == 'openMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.openMoreThan7DaysTickets+"</td>";
                        }else if( i == 'ClosedMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.ClosedMoreThan7DaysTickets+"</td>";
                        } else if(i =='pc_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";
                        } else if(i =='sc_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";
                        } else {
                            if((i == 'averageResponseTime') || (i == 'ClosedMoreThan7DaysTickets') || (i == 'openMoreThan7DaysTickets')){
                                fc += "";
                            }else{
                                var split = i.split("_");
                                if(split.length == 3) {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?ticket_type='+split[2]+'&department_id=' + d.id + '&parent_id=' + d.pc_id + '&sub_id=' + d.sc_id + '&department_filters=' + t.config.export_filters + '&category-on=' + t.category_wise_report.hasClass("category-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                } else {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?status_id='+split[1]+'&&department_id=' + d.id + '&parent_id=' + d.pc_id + '&sub_id=' + d.sc_id + '&department_filters=' + t.config.export_filters + '&category-on=' + t.category_wise_report.hasClass("category-on") +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                }
                            }
                        }
                    }
                }
            });
        }else{
            $.each(d, function(i,v) {
                if(i != 'id' && i !='sc_id' && i !='pc_id') {
                    if(i == 'tot') {
                            fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?department_id=' + d.id + '&department_filters=' + t.config.export_filters +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                    }
                    else{
                        if(i == 'dep_name') {
                            fc += "<td class='b5-text'>"+(v == null ? "" : v)+"</td>";

                        }else if(i == 'responsetime'){
                            fc += "<td class='b5-text'>"+d.averageResponseTime+"</td>";
                        }else if(i == 'openMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.openMoreThan7DaysTickets+"</td>";
                        }else if( i == 'ClosedMoreThan7Days'){
                            fc += "<td class='b5-text'>"+d.ClosedMoreThan7DaysTickets+"</td>";
                        } else {
                            if((i == 'averageResponseTime') || (i == 'ClosedMoreThan7DaysTickets') || (i == 'openMoreThan7DaysTickets')){
                                fc += "";
                            }else{
                                var split = i.split("_");
                                if(split.length == 3) {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?ticket_type='+split[2]+'&department_id=' + d.id + '&department_filters=' + t.config.export_filters +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                } else {
                                    fc += "<td class='b5-text'>"+t._s('<a href="' + baseURL + '/reports/tickets/info/department_info?status_id='+split[1]+'&&department_id=' + d.id + '&department_filters=' + t.config.export_filters +'"  target="_blank" class="underline">'+(v == null ? "" : v)+'</a>')+"</td>";
                                }
                            }

                        }
                    }
                }
            });
        }
        fc += "</tr>";
        t.lg.append(fc);
    };

    t.load = function() {
        var http = $.ajax({
            url: config.url.get_tickets, 
            type: "POST",
            data: {
                search: t.config.search,
                page: t.currentPage,
                main_filter: t.config.main_filter,
                filters: t.config.other_filters,
                size: t.perPage,
                order: t.config.sort_dir,
                category_wise_report: function() { return t.category_wise_report.hasClass("category-on"); },
                parent_category_wise_report: function() { return t.parent_category_wise_report.hasClass("pcategory-on"); }
            },
            beforeSend: function () {
                let colCount = $('#tHeadeRow th').length || 10;

                t.lg.html(`
                    <tr>
                        <td colspan="${colCount}" class="text-center">
                            <div class="dot-loader">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </td>
                    </tr>
                `);

                $('.table-footer').html('');
            },
        });
        http.done(function(data) {
            if(typeof data == "object" && typeof data.total != "undefined") {
                t.data = data;
                t.lg.empty();
                let currentPage = parseInt(data.page);
                let perPage = t.perPage;

                let startEntry = ((currentPage - 1) * perPage) + 1;
                let endEntry = Math.min(currentPage * perPage, data.filtered);

                if (data.filtered == 0) {
                    startEntry = 0;
                }

                let recordText = `
                    Showing ${startEntry} to ${endEntry} of ${data.filtered} entries
                `;

                if (data.filtered != data.total) {
                    recordText += ` (filtered from ${data.total} total records)`;
                }

                let footerHtml = `
                    <span class="srq-record-count">
                        ${recordText}
                    </span>
                    <div class="srq-pagination">
                `;

                let lastPage = Math.ceil(data.filtered / perPage);

                footerHtml += `
                    <button type="button"
                        class="page-btn pagination-btn"
                        data-page="${currentPage - 1}"
                        ${currentPage == 1 ? 'disabled' : ''}>

                        Previous
                    </button>
                `;

                for (let i = 1; i <= lastPage; i++) {

                    footerHtml += `
                        <button type="button"
                            class="page-btn pagination-btn
                            ${currentPage == i ? 'active' : ''}"
                            data-page="${i}">
                            ${i}
                        </button>
                    `;
                }

                footerHtml += `
                    <button type="button"
                        class="page-btn pagination-btn"
                        data-page="${currentPage + 1}"
                        ${currentPage == lastPage || lastPage == 0 ? 'disabled' : ''}>
                        Next
                    </button>
                `;
                footerHtml += `</div>`;
                $('.table-footer').html(footerHtml);
                var head = "";
                var count = 0;
                if(data.data[0] != null && Object.keys(data.data[0]).length !=0 && Object.keys(data.data[0]).length !="") {
                    t.content.find("#tHeadeRow").html("");
                }
                $.each(data.data[0], function(i,v) {
                    if(i == 'id') {
                        head += "<th class='hide'>ID</th>";
                    } else if(typeof i !== 'undefined' && i == 'pc_id') {
                        head += "<th class='hide'>PCId</th>";
                    } else if(typeof i !== 'undefined' && i == 'sc_id') {
                        head += "<th class='hide'>SCID</th>";
                    } else if(i == 'dep_name') {
                        head += "<th>Department</th>";
                    } else if(typeof i !== 'undefined' && i == 'pc_name') {
                        head += "<th>Problem Category</th>";
                    } else if(typeof i !== 'undefined' && i == 'sc_name') {
                        head += "<th>Sub Category</th>";
                    } else if(i == 'tot') {
                        head += "<th>Total</th>";
                    } else if(i == 'responsetime'){
                        head += "<th>Median First Response Time</th>";
                    } else if(i == 'ClosedMoreThan7Days'){
                        head += "<th>Median Days (Closed < 7)</th>";
                    } else if(i == 'openMoreThan7Days'){
                        head += "<th>Median Days (Open > 7)</th>";
                    } else {
                        if((i == 'averageResponseTime') || (i == 'ClosedMoreThan7DaysTickets') || (i == 'openMoreThan7DaysTickets')){
                            head += "";
                        } else {
                            var split = i.split("_");
                            head += "<th>"+(split[0])+"</th>";
                        }
                    }
                    count++;
                });
                t.content.find("#tHeadeRow").append(head);
                $.each(data.data, t.renderList);
                if(data.filtered < 1) {
                    var tableCountHead = $("#tHeadeRow").find("th").length;
                    t.lg.html('<tr class="no-record-found"><td colspan='+tableCountHead+'>No Departments Found</td></tr>');
                    return;
                }
            }
        });
        http.fail(function() {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket + "?q=" + t.config.export_filters;
    };

    t.downloadPDF = function(e) {
        e.preventDefault();
        window.location = t.config.url.export_ticket_pdf + "?q=" + t.config.export_filters;
    };

    // reset filter option
    t.btnClrFilter=function() {
        t.filters.based_on.val("null").trigger("change");
        t.filters.department.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        resetDateRangeFilter();
    };

    var select2Opts = { width: "100%" };
    t.filters.based_on.select2($.extend({}, select2Opts, {dropdownParent:t.filters.wrapper,placeholder: config.translations.Filter_Based_on}));
    t.filters.department.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.filter_by_department }));
    t.filters.problem_category.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.Filter_By_Problem_Category }));
    t.filters.sub_category.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.Filter_By_Sub_Category }));


    t.filters.fun.reload_department();
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    t.filters.wrapper.on("click", "button", t.search);
    t.searchbox.on("keypress", $.proxy(t.search));
    t.reload.on("click", $.proxy(t.load));
    t.export.on("click", $.proxy(t.download));
    t.export_pdf.on("click", $.proxy(t.downloadPDF));
    t.pageLimiter.on("change", function(e) {
        t.reset();
    });
    t.category_wise_report.on("click", $.proxy(t.toggleCategoryWiseReport));
    t.parent_category_wise_report.on("click", $.proxy(t.toggleParentCategoryWiseReport));
    t.reset();
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    $(document).on('click', '.pagination-btn', function () {
        let page = parseInt($(this).data('page'));

        if ($(this).prop('disabled')) {
            return;
        }
        t.currentPage = page;
        t.load();
    });
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });
}; 
