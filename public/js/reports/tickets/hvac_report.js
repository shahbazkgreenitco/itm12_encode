var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.content = $(".content");
    t.table = t.content.find("#lgTbl");
    t.lg = t.table.find("#lg");
    t.sortbtns = t.content.find('#srqSortDrop');
    t.shortItems = t.content.find('#short_items');
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
    t.pagebtns = t.content.find("#pagebtns");
    t.pageBtmSummary = t.content.find("#page-btm-summary");
    t.isLoading = false;

    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.content.find("#FilterModal")
    };

    t.filters.btnSubmitFilter =t.filters.wrapper.find("#btnSubmitFilter"),
    t.filters.btnfilterclr =t.filters.wrapper.find("#btnClrFilter"),
    t.filters.department =t.filters.wrapper.find("#filter_by_department"),
    t.filters.status =t.filters.wrapper.find("#filter_by_status"),
    t.filters.problem_category =t.filters.wrapper.find("#filter_by_problem_category"),
    t.filters.sub_category =t.filters.wrapper.find("#filter_by_sub_category"),
    t.filters.filter_by_date =t.filters.wrapper.find("#filter_by_date"),
    t.filters.daterange =t.filters.wrapper.find("#daterange"),
    t.filters.location_id =t.filters.wrapper.find("#filter_location_id"),
    t.filters.base_location_id =t.filters.wrapper.find("#filter_base_location_id"),
    t.filters.ticket_handlers = t.filters.wrapper.find("#filter_by_ticket_handlers"),

    t.filters.fun = {
        reload_department: function() {
            // t.filters.department.empty().append(new Option(config.translations.No_Filter, null, false, false));
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
            // t.filters.problem_category.empty().append(new Option(config.translations.No_Filter, null, false, false));
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
            // t.filters.sub_category.empty().append(new Option(config.translations.No_Filter, null, false, false));
            var prblm = t.filters.problem_category.val();
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
            //t.filters.status.empty().append(new Option("No Filter", null, true, true));
            $.each(t.config.status, function(i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        reload_locations: function() {
            $.each(t.config.locations, function(i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        },
        reload_base_locations: function() {
            $.each(t.config.base_locations, function(i, k) {
                t.filters.base_location_id.append(new Option(k.name, k.id));
            });
            t.filters.base_location_id.trigger("change");
        },
        reload_ticket_handlers: function() {
            t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.ticket_handlers.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id : t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Filter_By_Ticket_Handler
            }));
            t.filters.ticket_handlers.trigger("change");
        },
    };

    t.reset = function () {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        if (t.pagebtns && t.pagebtns.data && t.pagebtns.data('pagination')) {
            t.pagebtns.pagination("updateItemsOnPage", t.perPage);
        } else {
            console.warn('Pagination not initialized yet, skipping reset');
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
        if(t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null)
            t.config.other_filters.department = t.filters.department.val();
        if(t.filters.status && t.filters.status.val() != 'null' && t.filters.status.val() !== null)
            t.config.other_filters.status = t.filters.status.val();
        if(t.filters.problem_category && t.filters.problem_category.val() != 'null' && t.filters.problem_category.val() !== null)
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        if(t.filters.sub_category && t.filters.sub_category.val() != 'null' && t.filters.sub_category.val() !== null)
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        if(t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        if(t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.daterange = t.filters.daterange.val();
        if(t.filters.location_id && t.filters.location_id.val() != 'null' && t.filters.location_id.val() !== null)
            t.config.other_filters.location_id = t.filters.location_id.val();
        if(t.filters.base_location_id && t.filters.base_location_id.val() != 'null' && t.filters.base_location_id.val() !== null)
            t.config.other_filters.base_location_id = t.filters.base_location_id.val();
        if(t.filters.ticket_handlers.val()) {
            t.config.other_filters.ticket_handlers = t.filters.ticket_handlers.val();
        }
        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
        t.filters.wrapper.modal("hide");
    };

    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if(e.keyCode == 13 || $(this).is("span")) {
            var v = $.trim(t.searchbox.val());
            if(v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.load();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.load();
        }
    };

    t._s = function (v) {
        return v || "";
    };
    t.renderList = function(i, d) {
        var fc = `<tr>
        <td><a href=${t.config.url.ticket_info +'/'+ t._s(d.id)} target='_blank' style="color:blue;">#${t._s(d.id)} </a></td>
        <td>${t._s(d.subject)} </td>
        <td>${t._s(d.status_name)} </td>
        <td>${t._s(d.tat)} </td>
        <td> ${t._s(d.tat_expire)} </td>
        <td> ${t._s(d.username)} </td>
        <td> ${t._s(d.assigned_at)} </td>
        <td>${t._s(d.resolved_by)} </td>
        <td>${t._s(d.resolved_at)} </td>
        <td>${t._s(d.ageing)} </td>
        <td>${t._s(d.to_be_hold_date_time)} </td>
        <td>${t._s(d.expected_release_date_time)} </td>
        <td>${t._s(d.release_date_time)} </td>`;
        fc += "</tr>";
        t.lg.append(fc);
    };

    t.showLoader = function () {
        if (t.isLoading) return;
        t.isLoading = true;

        var colCount = $("#tHeadeRow").find("th").length || 10;

        var loaderHtml = `
            <tr>
                <td colspan="${colCount}" class="text-center">
                    <div class="dot-loader">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </td>
            </tr>
        `;

        t.lg.html(loaderHtml);
    };

    t.hideLoader = function () {
        t.isLoading = false;
    };

    t.load = function() {
        t.showLoader();

        if (t.pagebtns.length === 0) {
            console.error('Pagination container #pagebtns not found!');
            t.hideLoader();
            return;
        }

        if (!t.pagebtns.data('pagination')) {
            t.pagebtns.pagination({
                itemsOnPage: t.perPage,
                edges: 2,
                displayedPages: 2,
                onPageClick: t.pageBtnClicked
            });
        }

        var currentPage = 1;
        try {
            currentPage = t.pagebtns.pagination("getCurrentPage") || 1;
        } catch (e) {
            console.warn('Could not get current page, using default 1');
            currentPage = 1;
        }

        var http = $.ajax({
            url: config.url.list_tickets, 
            type: "POST",
            data: {
                search: t.config.search,
                page: currentPage,
                main_filter: t.config.main_filter,
                filters: t.config.other_filters,
                size: t.perPage,
                order: t.config.sort_dir,
            },
        });
        http.done(function(data) {
            t.hideLoader();

            if(typeof data == "object" && typeof data.total != "undefined") {
                t.data = data;

                try {
                    t.pagebtns.pagination("updateItems", parseInt(data.filtered) || 0);
                    t.pagebtns.pagination("drawPage", parseInt(data.page) || 1);
                } catch (e) {
                    console.warn('Pagination update failed:', e);
                }

                t.lg.empty();

                let currentPage = parseInt(data.page);
                let perPage = t.perPage;
                let startEntry = ((currentPage - 1) * perPage) + 1;
                let endEntry = Math.min(currentPage * perPage, data.filtered);
                if (data.filtered == 0) startEntry = 0;

                var summaryHtml = `<div class="showing-info">Showing ${startEntry} to ${endEntry} of ${data.filtered} entries`;
                if (data.filtered != data.total) {
                    summaryHtml += ` (filtered from ${data.total} total entries)`;
                }
                summaryHtml += `</div>`;

                if (t.pageBtmSummary.length > 0) {
                    t.pageBtmSummary.html(summaryHtml);
                }

                if(data.filtered < 1) {
                    var tableCountHead = $("#tHeadeRow").find("th").length;
                    t.lg.html('<tr class="no-record-found"><td colspan='+tableCountHead+'>'+config.translations.no_details+'</td></tr>');
                    return;
                }
                $.each(data.data, t.renderList);
            }
        });
        http.fail(function() {
            t.hideLoader();
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

    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };

    t.downloadPDF = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_pdf + "?q=" + t.config.export_filters;
    };

    // reset filter option
    t.btnClrFilter = function() {
        t.filters.department.val("").trigger("change");
        t.filters.status.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.filter_by_date.val('null').trigger("change");
        t.filters.location_id.val("").trigger("change");
        t.filters.base_location_id.val("").trigger("change");
        t.filters.ticket_handlers.val("").trigger("change");
        resetDateRangeFilter();
        t.filters.wrapper.modal('hide');
        setTimeout(function () {
            t.load();
        }, 300);
    }

    if (t.pagebtns.length > 0) {
        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            edges: 2,
            displayedPages: 2,
            onPageClick: t.pageBtnClicked
        });
    } else {
        console.error('Pagination container #pagebtns not found!');
    }

    var select2Opts = { width: "100%" };
    t.filters.department.select2($.extend({}, select2Opts, {dropdownParent:t.filters.wrapper,placeholder: config.translations.select_department }));
    t.filters.status.select2($.extend({}, select2Opts, {dropdownParent:t.filters.wrapper, placeholder: config.translations.select_status }));
    t.filters.problem_category.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.select_Problem_Category }));
    t.filters.sub_category.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.select_Sub_Category }));
    t.filters.location_id.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.select_Location }));
    t.filters.base_location_id.select2($.extend({}, select2Opts, { dropdownParent:t.filters.wrapper,placeholder: config.translations.select_base_location }));
    t.filters.filter_by_date.select2($.extend({}, select2Opts, {dropdownParent:t.filters.wrapper}));
    t.filters.fun.reload_department();
    t.filters.fun.reload_status();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_base_locations();
    t.filters.fun.reload_ticket_handlers();
    t.filters.department.on("change", function () {
        t.filters.problem_category.empty().trigger("change");
        t.filters.sub_category.empty().trigger("change");
        t.filters.fun.reload_problem_category();
    });
    
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    t.filters.wrapper.on("click", "button", t.search);
    t.searchbox.on("keypress", $.proxy(t.search));
    t.reload.on("click", $.proxy(t.load));
    t.export.on("click", $.proxy(t.download));
    t.export_pdf.on("click", $.proxy(t.downloadPDF));
    t.pageLimiter.on("change", function(e) {
        t.reset();
        t.load();
    });
    t.reset();
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });

    setTimeout(function () {
        t.load();
    }, 500);
};
