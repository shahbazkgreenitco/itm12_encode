var TableList = function(config, otherConfig) {
    var t = this;
    t.config = config;
    t.otherConfig = otherConfig;
    t.content = $("#mainContent");
    t.tab =  t.content;
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pagebtns = t.tab.find("#pagebtns");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");
    
    // Updated Sort elements for new layout
    t.sortbtns = t.tab.find('#srqSortDrop');
    t.shortItems = t.tab.find('#short_items');
    t.sortAction = t.sortbtns.find('.sort-action');
    t.dropdownAction = t.sortbtns.find('.dropdown-action');

    // Updated Search and Reload elements
    t.searchbox = t.tab.find("#tableSearch");
    t.reload = t.tab.find("#btn-reload-list");
    
    t.export = t.tab.find(".btn-download");
    t.export_pdf = t.tab.find(".btn-download-pdf");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#showSelect");

    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.content.find("#FilterModal")
    };
    
    // Filter modal elements
    t.filterModal = $("#filterModal");
    t.filterBtn = $(".btn-filter-open");
    t.filters.filter_non_handler_modal = $("#filter_non_handler_modal");
    t.filters.department = $("#filter_by_department");
    t.filters.category = $("#filter_by_category");
    t.filters.sub_category = $("#filter_by_sub_category");
    t.filters.location = $("#filter_by_location");
    t.filters.creator = $("#filter_by_creator");
    t.filters.status = $("#filter_by_status");
    t.btnApplyFilter = $("#btnApplyFilterModal");
    t.btnClrFilterModal = $("#btnClrFilterModal");
    t.filterCountBadge = $(".filter-count-badge");

    t.filters.fun = {
        reload_department: function() {
            t.filters.department.empty().append(new Option(t.config.translations.No_Filter, null, false, false));
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
            t.filters.category.empty().append(new Option(t.config.translations.No_Filter, null, false, false));
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function(i, k) {
                            t.filters.category.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                                t.filters.data.problem_categories["sc" + k.id] = k.sub;
                            }
                        });
                        t.filters.category.trigger("change");
                    }
                });
            }
            t.filters.category.trigger("change");
        },
        reload_sub_category: function() {
            t.filters.sub_category.empty().append(new Option(t.config.translations.No_Filter, null, false, false));
            var prblm = t.filters.category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm], function(i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {

                }
            }
            t.filters.sub_category.trigger("change");
        },
        reload_status: function() {
            t.filters.status.empty().append(new Option(t.config.translations.No_Filter, null, false, false));
            $.each(t.config.status, function(i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        reload_locations: function () {
            t.filters.location.empty().append(new Option(t.config.translations.No_Filter, null, false, false));
            $.each(t.config.locations, function(i, k) {
                t.filters.location.append(new Option(k.text, k.id));
            });
            t.filters.location.trigger("change");
        },
    };

    t.reset = function () {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        // Check if pagination is initialized before calling methods
        if (t.pagebtns && t.pagebtns.data && t.pagebtns.data('pagination')) {
            t.pagebtns.pagination("updateItemsOnPage", t.perPage);
        } else {
            console.warn('Pagination not initialized yet, skipping reset');
        }
    };
   
    t.sortFieldChanged = function(e) {
        e.preventDefault();
        var target = $(e.currentTarget);
        var id = target.attr("data-id");
        
        if (!id && (target.is("#sortDirectionIcon") || target.closest("#sortDirectionIcon").length)) {
            id = t.config.sort_dir.id;
        }

        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
        }
        t.renderSortFields();
        t.load();
    };

    t.renderSortFields = function() {
        var sf = t.sortFieldsList;
        var s = t.config.sort_dir;
        
        var mainIconClass = s.dir == 1 ? 'bi bi-sort-down' : 'bi bi-sort-up';
        t.sortDirIcon.attr('class', mainIconClass);
        
        var dirIcon = s.dir == 1 ? '<i class="bi bi-sort-down float-end"></i>' : '<i class="bi bi-sort-up float-end"></i>';
        
        sf.empty();
        $.each(otherConfig.sort_fields, function(i,d) {
            $('<li><a class="dropdown-item ' + (d.id == s.id ? "active" : "") + '" href="#" data-id="' + d.id + '">' + d.text + (d.id == s.id ? dirIcon : "") + '</a></li>').appendTo(sf);
        });
    };

    t.cache_filter_values = function() {
        var v = t.searchbox.val();
        if (typeof t.searchbox.validate_str_param === 'function') {
            v = t.searchbox.validate_str_param();
        } else {
            v = t.searchbox.val().trim();
        }
        
        t.config.search = v;
        t.config.other_filters = {};
        t.config.other_filters.department_id = t.filters.department.val();
        t.config.other_filters.problem_category_id = t.filters.category.val();
        t.config.other_filters.sub_category_id = t.filters.sub_category.val();
        t.config.other_filters.location_id = t.filters.location.val();
        t.config.other_filters.status_id = t.filters.status.val();
        
        var jobj = {
            "assigned_to": t.config.assigned_to,
            "status_id": t.config.status_id,
            "search": t.config.search,
            "other_filters": t.config.other_filters,
            "action_type": t.config.action_type
        };
        
        t.updateFilterCount();
        
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.updateFilterCount = function() {
        var count = 0;
        if (t.filters.department.val() && t.filters.department.val() != 'null') {
            count++;
        }
        if (t.filters.category.val() && t.filters.category.val() != 'null') {
            count++;
        }
        if (t.filters.sub_category.val() && t.filters.sub_category.val() != 'null') {
            count++;
        }
        if (t.filters.location.val() && t.filters.location.val() != 'null') {
            count++;
        }
        if (t.filters.status.val() && t.filters.status.val() != 'null') {
            count++;
        }
        
        if (count > 0) {
            t.filterCountBadge.text(count).removeClass('d-none');
        } else {
            t.filterCountBadge.text('0').addClass('d-none');
        }
    };

    t.openFilterModal = function(e) {
        e.preventDefault();
        t.filterModal.modal('show');
    };

    t.applyFilter = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        t.filterModal.modal('hide');
        t.load();
    };

    t.clearFilterModal = function(e) {
        e.preventDefault();
        t.filters.department.val("null").trigger("change");
        t.filters.category.val("null").trigger("change");
        t.filters.sub_category.val("null").trigger("change");
        t.filters.location.val("null").trigger("change");
        t.filters.status.val("null").trigger("change");
        t.cache_filter_values();
        t.load();
        t.filterModal.modal('hide');
    };

    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = t.searchbox.val();
            if (typeof t.searchbox.validate_str_param === 'function') {
                v = t.searchbox.validate_str_param();
            } else {
                v = t.searchbox.val().trim();
            }

            if(v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.load();
        } else if(target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.load();
        }
    };

    t.btnClrFilter = function(){
        t.filter_non_handler_modal.val("null").trigger("change");
        t.cache_filter_values();
        t.load();
    }

    t._s = function (v) {
        return v || "";
    };

    t.renderList = function(i, d) {
        var fc = "<tr><td>" + t._s('<a href="' + t.config.url.view_ticket + "/" + d.id + "?b=" + "all-tickets" + '" target="_blank" class="underline">#'+ d.id +'</a>') + "</td><td>" + t._s('<a href="' + t.config.url.view_ticket + "/" + d.id + "?b=" + "all-tickets" + '" target="_blank" class="underline">'+ d.subject +'</a>') + "</td><td>" + t._s(d.dept_name) + "</td><td>" + t._s(d.problem_category) + "</td><td>" + t._s(d.sub_category) + "</td><td>" + t._s(d.location_name) + "</td><td>" + t._s(d.status) + "</td><td><div>" + t._s(d.creator) + "</div><div>" + t._s(d.creatoremail) + "</div></td><td><div>" + t._s(d.handler_full_name) + "</div><div>" + t._s(d.handler_email) + "</div></td><td>" + (d.tat !== null && d.tat !== undefined ? d.tat : '') + "</td><td>" + t._s(d.priority) + "</td><td>" + t._s(d.updated_at_format) + "</td><td>" + t._s(d.sla_breached) + "</td></tr>";
        t.lg.append(fc);
    };

    t.load = function() {
        // Check if pagebtns exists
        if (t.pagebtns.length === 0) {
            console.error('Pagination container #pagebtns not found!');
            return;
        }

        // Check if pagination is initialized
        if (!t.pagebtns.data('pagination')) {
            console.warn('Pagination not initialized, initializing now...');
            t.pagebtns.pagination({
                itemsOnPage: t.perPage,
                onPageClick: t.pageBtnClicked
            });
        }
        if (t.tableCover.length > 0) {
            t.tableCover.addClass("gload");
        }  
        var currentPage = 1;
        try {
            currentPage = t.pagebtns.pagination("getCurrentPage") || 1;
        } catch (e) {
            console.warn('Could not get current page, using default 1');
            currentPage = 1;
        }
        
        var http = $.ajax({
            url: otherConfig.url, 
            type: "POST",
            data: {
                search: t.config.search,
                page: currentPage,
                size: t.perPage,
                main_filter: t.config.main_filter,
                filters: t.config.other_filters,
                order: t.config.sort_dir,
                assigned_to: t.config.assigned_to,
                status_id: t.config.status_id,
                ticket_type: t.config.ticket_type,
                department_id: t.config.department_id,
                parent_id: t.config.parent_id,
                sub_id: t.config.sub_id,
                sla: t.config.sla,
                comman_filter: t.config.comman_filter,
                location_id: t.config.location_id,
                based_on: t.config.based_on,
                daterange: t.config.daterange,
                action_type: t.config.action_type,
                open_ticket: t.config.openTicket,
                pendingTicket: t.config.pendingTicket,
                openMoreThanDaysTickets: t.config.openMoreThanDaysTickets,
                ClosedMoreThanDaysTickets: t.config.ClosedMoreThanDaysTickets,
                techboard: t.config.techboard,
            },
            beforeSend: function () {
                let colCount = $('#mytable thead th').length;

                t.lg.html(`
                    <tr class="loading-row">
                        <td colspan="${colCount}" class="text-center py-5">
                            <div class="dot-loader">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </td>
                    </tr>
                `);

                t.pageBtmSummary.empty();
                t.pagebtns.empty();
            },
        });
        
        http.done(function(data) {
            if(typeof data == "object" && typeof data.total != "undefined") {
                if (t.config.main_filter === 'location_info' && data.data.length > 0 && data.data[0].location_name) {
                    $('.h3-text').first().text('Location wise Tickets Info - ' + data.data[0].location_name);
                }             
                t.data = data;
                t.totalRecords = data.total;
                t.filteredRecords = data.filtered;
                // Safely update pagination
                try {
                    t.pagebtns.pagination("updateItems", parseInt(data.filtered) || 0);
                    t.pagebtns.pagination("drawPage", parseInt(data.page) || 1);
                } catch (e) {
                    console.warn('Pagination update failed:', e);
                }
                
                t.lg.empty();
                var start = (data.page - 1) * t.perPage + 1;
                var end = start + data.data.length - 1;
                
                if(data.filtered != data.total) {
                    t.pageBtmSummary.html("Showing " + start + " to " + end + " of " + data.filtered + " entries (filtered from " + data.total + " total entries)");
                } else {
                    t.pageBtmSummary.html("Showing " + start + " to " + end + " of " + data.total + " entries");
                }
                
                if(data.filtered < 1) {
                    t.lg.html('<tr class="no-record-found"><td colspan="14" style="text-align: center; font-weight: bold; padding: 20px; color: #6c757d;">No Details Found</td></tr>');
                    t.pagebtns.empty();
                    return;
                }
                
                $.each(data.data, t.renderList);
            }
        });
        
        http.fail(function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert("Something went wrong. Please check given details are correct");
        });
        
        http.always(function() {
            t.httpCall = true;
        });
    };
    t.pageBtnClicked = function (n, e) {
        if (typeof e !== "undefined") e.preventDefault();
        t.load();
    };
    
    // FIX: Pagination initialize karo
    if (t.pagebtns.length > 0) {
        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            onPageClick: t.pageBtnClicked
        });
    } else {
        console.error('Pagination container #pagebtns not found!');
    }
    t.pageLimiter.on("change", function (e) {
        t.reset();
        t.load();
    });

    t.download = function (e) {
        e.preventDefault();
        if(t.config.main_filter == "hand-tic-info") {
            if (t.config.status_id != '' && t.config.status_id != null) {
                if(t.config.openMoreThanDaysTickets != '' && t.config.openMoreThanDaysTickets != null) {
                    window.location = t.config.url.export_ticket + "?openMoreThanDaysTickets="+t.config.openMoreThanDaysTickets+ "&&assigned_to=" + t.config.assigned_to +"&&status_id=" + t.config.status_id +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter +"&&techboard="+t.config.techboard;
                }else if(t.config.ClosedMoreThanDaysTickets != '' && t.config.ClosedMoreThanDaysTickets != null) {
                    window.location = t.config.url.export_ticket + "?ClosedMoreThanDaysTickets="+t.config.ClosedMoreThanDaysTickets+ "&&assigned_to=" + t.config.assigned_to +"&&status_id=" + t.config.status_id +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter  +"&&techboard="+t.config.techboard;
                } else {
                    window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to + "&&status_id=" + t.config.status_id +"&&search=" + t.config.search  +"&&comman_filter=" + t.config.comman_filter +"&&techboard="+t.config.techboard;
                }
            } else if(t.config.ticket_type != '' && t.config.ticket_type != null) {
                window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to + "&&ticket_type=" + t.config.ticket_type +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter  +"&&techboard="+t.config.techboard;
            }else if(t.config.openTicket != '' && t.config.openTicket != null){
                window.location = t.config.url.export_ticket + "?openTicket="+t.config.openTicket+ "&&assigned_to=" + t.config.assigned_to +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter  +"&&techboard="+t.config.techboard;
            }else if(t.config.pendingTicket != '' && t.config.pendingTicket != null){
                window.location = t.config.url.export_ticket + "?pendingTicket="+t.config.pendingTicket+ "&&assigned_to=" + t.config.assigned_to +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter  +"&&techboard="+t.config.techboard;
            }else {
                if(t.config.assigned_to != null && t.config.action_type != '') {
                    window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to +"&&action_type="+t.config.action_type +"&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter +"&&techboard"+t.config.techboard;
                } else {
                    window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to + "&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter +"&&techboard="+t.config.techboard;
                }
            }
        }

        if(t.config.main_filter == "day-hand-tic-info") {
            if (t.config.status_id != '' && t.config.status_id != null) {
                window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to + "&&status_id=" + t.config.status_id + "&&sla=" + t.config.sla + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  +"&&comman_filter=" + t.config.comman_filter;
            }  else {
                window.location = t.config.url.export_ticket + "?assigned_to=" + t.config.assigned_to + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search +"&&comman_filter=" + t.config.comman_filter;
            }
        }

        if (t.config.pcategory && t.config.subcategory == "") {
            if (t.config.status_id != '' && t.config.status_id != null && t.config.parent_id != '' && t.config.parent_id != null ) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&status_id=" + t.config.status_id + "&&parent_id=" + t.config.parent_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            if(t.config.ticket_type != '' && t.config.ticket_type != null && t.config.parent_id != '' && t.config.parent_id != null) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&ticket_type=" + t.config.ticket_type + "&&parent_id=" + t.config.parent_id +"&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            if(t.config.status_id == '' && t.config.ticket_type == '') {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&ticket_type=" + t.config.ticket_type + "&&parent_id=" + t.config.parent_id +"&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
        }

        if (t.config.subcategory && t.config.pcategory == "") {
            if(t.config.status_id != '' && t.config.status_id != null && t.config.sub_id != '' && t.config.sub_id != null) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&status_id=" + t.config.status_id + "&&parent_id=" + t.config.parent_id + "&&sub_id=" + t.config.sub_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            if(t.config.ticket_type != '' && t.config.ticket_type != null && t.config.parent_id != '' && t.config.parent_id != null && t.config.sub_id != '' && t.config.sub_id != null) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&ticket_type=" + t.config.ticket_type + "&&parent_id=" + t.config.parent_id + "&&sub_id=" + t.config.sub_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            if(t.config.status_id == '' && t.config.ticket_type == '') {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&parent_id=" + t.config.parent_id + "&&sub_id=" + t.config.sub_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
        }

        if (t.config.subcategory == "" && t.config.pcategory == "" && t.config.main_filter != "hand-tic-info" && t.config.main_filter != "day-hand-tic-info") {
            if(t.config.parent_id != '' && t.config.parent_id != null ) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&parent_id=" + t.config.parent_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            else if(t.config.sub_id != '' && t.config.sub_id != null) {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&parent_id=" + t.config.parent_id + "&&sub_id=" + t.config.sub_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            else if(t.config.status_id != '' && t.config.status_id != null){
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&status_id=" + t.config.status_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            else if(t.config.ticket_type != '' && t.config.ticket_type != null){
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&ticket_type=" + t.config.ticket_type + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            } else {
                window.location = t.config.url.export_ticket + "?department_id=" + t.config.department_id + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
        }
        if(t.config.main_filter == "location_info") {
            if(t.config.status_id != '' && t.config.status_id != null){
                window.location = t.config.url.export_ticket + "?location_id=" + t.config.location_id + "&&status_id=" + t.config.status_id + "&&based_on=" + t.config.based_on + "&&daterange=" + t.config.daterange+ "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
            else if(t.config.ticket_type != '' && t.config.ticket_type != null){
                window.location = t.config.url.export_ticket + "?location_id=" + t.config.location_id + "&&ticket_type=" + t.config.ticket_type + "&&based_on=" + t.config.based_on + "&&daterange=" + t.config.daterange + "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            } else {
                window.location = t.config.url.export_ticket + "?location_id=" + t.config.location_id + "&&based_on=" + t.config.based_on + "&&daterange=" + t.config.daterange+ "&&main_filter=" + t.config.main_filter + "&&search=" + t.config.search  + "&&comman_filter=" + t.config.comman_filter +"&&q=" + t.config.export_filters;
            }
        }
    };

    t.downloadPDF = function(e) {
        e.preventDefault();
        if (t.config.status_id != '' && t.config.status_id != null) {
            window.location = t.config.url.export_ticket_pdf + "?assigned_to=" + t.config.assigned_to + "&&status_id=" + t.config.status_id + "&&search=" + t.config.search;
        } else if(t.config.ticket_type != '' && t.config.ticket_type != null) {
            window.location = t.config.url.export_ticket_pdf + "?assigned_to=" + t.config.assigned_to + "&&ticket_type=" + t.config.ticket_type + "&&search=" + t.config.search;
        } else {
            window.location = t.config.url.export_ticket_pdf + "?assigned_to=" + t.config.assigned_to + "&&search=" + t.config.search;
        }
    };

    // Initial setup    
    t.renderSortDropdown = function () {
        var $dropdown = $("#short_items");
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            $dropdown.append(`
                <li>
                    <a href="javascript:void(0)"
                    class="dropdown-item ${isActive ? 'active' : ''}"
                    data-id="${d.id}">
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
        t.shortItems.removeClass("show");
    });
    
    t.renderSortDropdown();
    
    t.searchbox.on("keypress", $.proxy(t.search));
    t.reload.on("click", $.proxy(t.load));
    t.export.on("click", $.proxy(t.download));
    
    if(t.export_pdf.length) {
        t.export_pdf.on("click", $.proxy(t.downloadPDF));
    }
    var select2Opts = { width: "100%" };
    t.filters.filter_non_handler_modal.select2($.extend({}, select2Opts, {
        placeholder:  "Select to Non Handler filter",
        dropdownParent: t.filters.filter_non_handler_modal.parent(),
    }));
    
    t.filters.department.select2({
        width: "100%",
        placeholder:  "Filter by Department",
        dropdownParent: $('#filterModal')
    });
    t.filters.category.select2({
        width: "100%",
        placeholder: "Filter by Category",
        dropdownParent: t.filters.category.parent(),
    });
    t.filters.sub_category.select2({
        width: "100%",
        placeholder: "Filter by Sub Category",
        dropdownParent: t.filters.sub_category.parent(),
    });
    t.filters.location.select2({
        width: "100%",
        placeholder:  "Filter by Location",
        dropdownParent: t.filters.location.parent(),
    });

    // Company Select2 with AJAX
    t.filters.creator.select2($.extend({}, select2Opts, {
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

    t.filters.status.select2({
        width: "100%",
        placeholder:  "Filter by Status",
        dropdownParent: t.filters.status.parent(),
    });
    // Filter modal event bindings
    t.filterBtn.on("click", t.openFilterModal);
    t.btnApplyFilter.on("click", t.applyFilter);
    t.btnClrFilterModal.on("click", t.clearFilterModal);
    t.filters.fun.reload_department();
    t.filters.fun.reload_status();
    t.filters.fun.reload_locations();
    t.filters.department.on("change", function () {
        t.filters.category.empty().trigger("change");
        t.filters.sub_category.empty().trigger("change");
        t.filters.fun.reload_problem_category();
    });
    t.filters.category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    // Initial load
    t.load();
};

var MyApp = function(config) {
    var t = this;
    t.content = $("#mainContent");
    t.deviceTab = new TableList(config, {
        tab: "#main-user-list-wrapper",
        url: config.url.list_handler_tickets, 
        sort_fields: config.sort_fields.ticket, 
        tbl_fields: config.tbl_fields.ticket
    });
    
    t.content.on("click", ".black-slide-links a", function(e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function() {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });
};