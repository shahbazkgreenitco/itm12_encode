var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $(".content");
    t.table = t.content.find("#lgTbl");
    t.lg = t.table.find("#lg");
    t.tableCover = t.table.closest(".gtable-cover");
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
    t.pagebtns = t.content.find("#pagebtns");
    t.pageBtmSummary = t.content.find("#page-btm-summary");
    t.currentPage = 1;
    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.content.find("#FilterModal")
    };
    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.creatorcover = t.filters.wrapper.find(".creatorcover");
    t.filters.based_on_cre_log = t.filters.wrapper.find("#filter_based_on_cre_log"),
    t.filters.creator = t.filters.wrapper.find("#filter_by_creator"),
    t.filters.status = t.filters.wrapper.find("#filter_by_status"),
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category"),
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category"),
    t.filters.handler = t.filters.wrapper.find("#filter_by_handler"),
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority"),
    t.filters.escalate_to = t.filters.wrapper.find("#filter_by_escalate_to"),
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date"),
    t.filters.location_id = t.filters.wrapper.find("#filter_location_id"),
    t.filters.ticket_type = t.filters.wrapper.find("#filter_by_ticket_type"),
    t.filters.daterange = t.filters.wrapper.find("#daterange"),

    t.filters.fun = {
        reload_department: function() {
            t.filters.department.empty().append(new Option(config.translations.No_Filter, null, false, false));
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function(data) {
                if(typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i,k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
            t.filters.department.trigger("change");
        },
        reload_problem_category: function() {
            // t.filters.problem_category.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.problem_category.empty();
            var department = t.filters.department.val();
            if(department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if(typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function(i,k) {
                            t.filters.problem_category.append(new Option(k.name, k.id, false, false));
                            if(typeof k.sub != "undefined" && Array.isArray(k.sub)) {
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
            // t.filters.sub_category.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.sub_category.empty();
            var prblm = t.filters.problem_category.val();
            if(prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm], function(i,k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                }
                catch(e) {

                }
            }
            t.filters.sub_category.trigger("change");
        },
        reload_creator: function () {
            t.filters.creator.select2($.extend({}, select2Opts, {
                dropdownParent:t.filters.wrapper,
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id : t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_Ticket_Creator
            }));
            t.filters.creator.trigger("change");
        },
        reload_status: function () {
            //t.filters.status.empty().append(new Option("No Filter", null, true, true));
            $.each(t.config.status, function (i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        reload_escalate_to: function () {
            t.filters.escalate_to.select2($.extend({}, select2Opts, {
                dropdownParent:t.filters.wrapper,
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id : t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_Escalate_To
            }));
            t.filters.escalate_to.trigger("change");
        },
        reload_handler: function () {
            t.filters.handler.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.handler.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id : t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_Ticket_Handler
            }));
            t.filters.handler.trigger("change");
        },
        reload_priority: function () {
            //t.filters.priority.empty().append(new Option("No Filter", null, true, true));
            $.each(t.config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.text, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_locations: function() {
            // t.filters.location_id.empty().append(new Option("No Filter", null, true, true));
            $.each(t.config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        }
    };

     t.reset = function () {
        t.perPage = t.pageLimiter.val();
        t.currentPage = 1;
        t.load();
    };

    // t.reset = function () {
    //     t.perPage = parseInt(t.pageLimiter.val()) || 10;
    //     console.log(t.pagebtns.data('pagination'), 'ffffffffffffffffff');
    //     // Check if pagination is initialized before calling methods
    //     if (t.pagebtns && t.pagebtns.data && t.pagebtns.data('pagination')) {
    //         t.pagebtns.pagination("updateItemsOnPage", t.perPage);
    //     } else {
    //         console.warn('Pagination not initialized yet, skipping reset');
    //     }
    // }

    t.filters.filteroption = function(){
        if (t.filters.based_on_cre_log.val() == "1" || t.filters.based_on_cre_log.val() == "2") {
             t.filters.creatorcover.removeClass("hide");
        }
    };

    t.renderSortDropdown = function () {
        var $dropdown =t.shortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields.ticket, function (i, d) {
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
        if(t.filters.department.val() && t.filters.department.val() != 'null') {
            t.config.other_filters.department = t.filters.department.val();
        }
        if(t.filters.creator.val() && t.filters.creator.val() != 'null') {
            t.config.other_filters.creator = t.filters.creator.val();
        }
        if(t.filters.status.val() && t.filters.status.val() != 'null') {
            t.config.other_filters.status = t.filters.status.val();
        }
        if(t.filters.problem_category.val() && t.filters.problem_category.val() != 'null') {
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        }
        if(t.filters.sub_category.val() && t.filters.sub_category.val() != 'null') {
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if(t.filters.handler.val() && t.filters.handler.val() != 'null') {
            t.config.other_filters.handler = t.filters.handler.val();
        }
        if(t.filters.priority.val() && t.filters.priority.val() != 'null') {
            t.config.other_filters.priority = t.filters.priority.val();
        }
        if(t.filters.escalate_to.val() && t.filters.escalate_to.val() != 'null') {
            t.config.other_filters.escalate_to = t.filters.escalate_to.val();
        }
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null') {
            t.config.other_filters.based_on = t.filters.based_on.val();
        }
        if(t.filters.based_on_cre_log.val() && t.filters.based_on_cre_log.val() != 'null') {
            t.config.other_filters.based_on_cre_log = t.filters.based_on_cre_log.val();
        }
        if(t.filters.location_id.val() && t.filters.location_id.val() != 'null') {
            t.config.other_filters.location_id = t.filters.location_id.val();
        }
        if(t.filters.ticket_type.val() && t.filters.ticket_type.val() != 'null') {
            t.config.other_filters.ticket_type = t.filters.ticket_type.val();
        }
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null') {
            t.config.other_filters.daterange = t.filters.daterange.val();
        }
        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
        t.filters.wrapper.modal("hide");
    };

    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox.val());

            if(v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.currentPage = 1;
            t.load();
        }
        else if(target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.currentPage = 1;
            t.load();
        }
        
    };


    t._s = function (v) {
        return v || "";
    };

    t.renderList = function(i, d) {
        var fc = "<tr><td class='b5-text'><a href='"+t.config.url.ticket_view_url+"/"+d.id+"' target='_blank' class='underline'>" + t._s(d.ticket_id) + "</a></td><td class='b5-text'><a href='"+t.config.url.ticket_view_url+"/"+d.id+"' target='_blank' class='underline'>"+ t._s(d.subject) + "</a></td><td class='b5-text'>" + t._s(d.updated_at_format) + "</td><td class='b5-text'>" + t._s(d.dept_name) + "</td><td class='b5-text'>" + t._s(d.problem_category) + "</td><td class='b5-text'>" + t._s(d.sub_category) + "</td><td class='b5-text'>" + t._s(d.handler_name) + "</td><td class='b5-text'>" + t._s(d.creator) + "</td><td class='b5-text'>" + t._s(d.escalate_for) + "</td><td class='b5-text'><div>" + t._s(d.escalated_user) + "</div><div>" + t._s(d.escalated_email) + "</div></td><td class='b5-text'>" + t._s(d.status) + "</td><td class='b5-text'>" + t._s(d.priority) + "</td><td class='b5-text'>" + t._s(d.tat_time) + "</td><td class='b5-text'>" + t._s(d.location_name) + "</td><td class='b5-text'>" + t._s(d.created_date_on) + "</td><td class='b5-text'>" + t._s(d.ticket_type) + "</td>";
        if(d.ticketTypeFields != undefined) {
            $.each(d.ticketTypeFields, function(i,v) {
                fc += "<td class='b5-text'>"+(v.value)+"</td>";
            });
        }
        fc += "</tr>";
        t.lg.append(fc);
    };

    t.load = function () {
        
        // Check if pagination is initialized
        console.log(t.pagebtns[0], 'pppsss');
        if (!t.pagebtns.data('pagination')) {
             t.pagebtns.pagination({
                itemsOnPage: t.perPage,
                onPageClick: t.pageBtnClicked
            });

            console.log(t.pagebtns[0]);
            console.log($('#pagebtns')[0]);
            console.log(t.pagebtns.is('#pagebtns'));
        }

        t.tableCover.addClass("gload");

        // Safely get current page
        var currentPage = 1;
        try {
            currentPage = t.pagebtns.pagination("getCurrentPage") || 1;
        } catch (e) {
            console.warn('Could not get current page, using default 1');
            currentPage = 1;
        }


        var http = $.ajax({
            url: config.url.list_esclate_tickets, 
            type: "POST",
            data: {
                search: t.config.search,
                page: currentPage,
                size: t.perPage || 10,
                main_filter: t.config.main_filter,
                filters: t.config.other_filters,
                order: t.config.sort_dir,
                techId: t.config.techID,
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

            },
        });
        http.done(function(data) {
            if(typeof data == "object" && typeof data.total != "undefined") {
                t.data = data;
                console.log(data);
                t.lg.empty();
                // let currentPage = parseInt(data.page);
                // let perPage = t.perPage;

                // let startEntry = ((currentPage - 1) * perPage) + 1;
                // let endEntry = Math.min(currentPage * perPage, data.filtered);

                // if (data.filtered == 0) {
                //     startEntry = 0;
                // }

                // let recordText = `
                //     Showing ${startEntry} to ${endEntry} of ${data.filtered} entries
                // `;

                // if (data.filtered != data.total) {
                //     recordText += ` (filtered from ${data.total} total records)`;
                // }

                // let footerHtml = `
                //     <span class="srq-record-count">
                //         ${recordText}
                //     </span>
                //     <div class="srq-pagination">
                // `;

                // let lastPage = Math.ceil(data.filtered / perPage);

                // footerHtml += `
                //     <button type="button"
                //         class="page-btn pagination-btn"
                //         data-page="${currentPage - 1}"
                //         ${currentPage == 1 ? 'disabled' : ''}>

                //         Previous
                //     </button>
                // `;

                // for (let i = 1; i <= lastPage; i++) {

                //     footerHtml += `
                //         <button type="button"
                //             class="page-btn pagination-btn
                //             ${currentPage == i ? 'active' : ''}"
                //             data-page="${i}">
                //             ${i}
                //         </button>
                //     `;
                // }

                // footerHtml += `
                //     <button type="button"
                //         class="page-btn pagination-btn"
                //         data-page="${currentPage + 1}"
                //         ${currentPage == lastPage || lastPage == 0 ? 'disabled' : ''}>
                //         Next
                //     </button>
                // `;
                // footerHtml += `</div>`;
                // $('.table-footer').html(footerHtml);

                try {
                    t.pagebtns.pagination("updateItems", parseInt(data.filtered) || 0);
                    t.pagebtns.pagination("drawPage", parseInt(data.page) || 1);
                } catch (e) {
                    console.warn('Pagination update failed:', e);
                }

                let start = (parseInt(data.page) - 1) * 10 + 1;
                let end = start + data.data.length - 1;

                // Display total records count with proper formatting
                var summaryHtml = '';
                if (t.data.filtered != data.total) {
                    summaryHtml =
                        `${t.config.datatable_translations.showing} ${start} ${t.config.datatable_translations.to} ${end} ` +
                        `${t.config.datatable_translations.of} ${data.filtered} ${t.config.datatable_translations.records} ` +
                        `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} ${data.total} ${t.config.datatable_translations.total_entries})`;
                } else {
                    summaryHtml =
                        `${t.config.datatable_translations.showing} ${start} ${t.config.datatable_translations.to} ${end} ` +
                        `${t.config.datatable_translations.of} ${data.total} ${t.config.datatable_translations.records}`;
                }

                // Update the summary if element exists
                if (t.pageBtmSummary.length > 0) {
                    t.pageBtmSummary.html(summaryHtml);
                } else {
                    console.warn('pageBtmSummary element not found!');
                    // Try to find it globally
                    var summaryElement = $("#page-btm-summary");
                    if (summaryElement.length > 0) {
                        summaryElement.html(summaryHtml);
                    }
                }

                if(data.filtered < 1) {
                    var tableCountHead = $("#tHeadeRow").find("th").length;
                    t.lg.html('<tr class="no-record-found"><td class="text-center b5-text" colspan='+tableCountHead+'>No Details Found</td></tr>');
                    return;
                }
                $.each(data.data, t.renderList);
            }
        });
        http.fail(function() {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.pageBtnClicked = function(n, e) {
        if(typeof e !== "undefined") e.preventDefault();
        t.load();
    };

    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket + "?q=" + t.config.export_filters +  "&techId="+ t.config.techID;
    };

    t.downloadPDF = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_pdf + "?q=" + t.config.export_filters;
    };
    t.searchbox.on("keypress", $.proxy(t.search));
    t.reload.on("click", $.proxy(t.load));
    t.export.on("click", $.proxy(t.download));
    t.export_pdf.on("click", $.proxy(t.downloadPDF));
    t.export.on("click", ".btn-download", t.download);
    t.pageLimiter.on("change", function(e) {
        t.reset();
    });
    t.reset();

    // FIX: Pagination initialize karo
    if (t.pagebtns.length > 0) {
        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            onPageClick: t.pageBtnClicked
        });
    } else {
        console.error('Pagination container #pagebtns not found!');
    }

    t.btnClrFilter = function() {
        t.filters.department.val("").trigger("change");
        t.filters.creatorcover.val("").trigger("change");
        t.filters.based_on_cre_log.val("").trigger("change");
        t.filters.creator.val("").trigger("change");
        t.filters.status.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.handler.val("").trigger("change");
        t.filters.priority.val("").trigger("change");
        t.filters.escalate_to.val("").trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.filters.location_id.val("").trigger("change");
        t.filters.ticket_type.val("").trigger("change");
        resetDateRangeFilter();
    }

    var select2Opts = { width: "100%" };
    t.filters.department.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.filter_by_department}));
    t.filters.ticket_type.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:'Select Ticket Type'}));
    t.filters.status.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.filter_by_status}));
    t.filters.problem_category.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.Filter_By_Problem_Category}));
    t.filters.sub_category.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.Filter_By_Sub_Category}));
    t.filters.handler.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.Filter_By_Ticket_Handler}));
    t.filters.priority.select2($.extend({}, select2Opts,{dropdownParent:t.filters.wrapper,placeholder:config.translations.Filter_By_Priority}));
    t.filters.based_on.select2($.extend({}, select2Opts,{dropdownParent: t.filters.wrapper}));
    t.filters.location_id.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.Filter_By_Locations}));
    t.filters.based_on_cre_log.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper})).on("change", $.proxy(t.filters.filteroption));    
    t.filters.fun.reload_escalate_to();
    t.filters.fun.reload_department();
    t.filters.fun.reload_creator();
    t.filters.fun.reload_status();
    t.filters.fun.reload_handler();
    t.filters.fun.reload_priority();
    t.filters.fun.reload_locations();
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    t.filters.wrapper.on("click", "button", t.search);
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });

    t.pageLimiter.on("change", function (e) {
        t.reset();
    });

    // FIX: Reset ko pagination ke baad call karo
    t.reset();
    // $(document).on('click', '.pagination-btn', function () {
    //     let page = parseInt($(this).data('page'));

    //     if ($(this).prop('disabled')) {
    //         return;
    //     }
    //     t.currentPage = page;
    //     t.load();
    // });
};



