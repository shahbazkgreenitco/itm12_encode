var TableList = function (config, otherConfig) {
    var t = this;
    t.config = config;
    t.otherConfig = otherConfig;
    t.content = $("#content-container");
    t.tab = t.content.find(otherConfig.tab);

    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pagebtns = t.tab.find("#pagebtns");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");

    t.sortbtns = t.content.find(".sort-buttons");
    t.searchbox = t.tab.find(".searchbox");
    t.searchbtn = t.tab.find(".btn-searchbox");
    t.reload = t.tab.find(".btn-reload");
    t.export = t.content.find(".btn-download");
    t.export_pdf = t.tab.find(".btn-download-pdf");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.filterbtn = t.content.find(".btn-open-filter");
    // Loader flag
    t.isLoading = false;
    t.sortTimeout = null; // Add timeout reference

    // FIX: select2Opts ko yahan define karo (sabse pehle)
    var select2Opts = { width: "100%" };

    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.content.find("#FilterModal")
    };

    t.filters.btnSubmitFilter = t.filters.wrapper.find(".btn-filter");
    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter");
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.create_via = t.filters.wrapper.find("#filter_by_create_via");
    t.filters.creatorcover = t.filters.wrapper.find(".creatorcover");
    t.filters.based_on_cre_log = t.filters.wrapper.find("#filter_based_on_cre_log");
    t.filters.creator = t.filters.wrapper.find("#filter_by_creator");
    t.filters.status = t.filters.wrapper.find("#filter_by_status");
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.handler = t.filters.wrapper.find("#filter_by_handler");
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority");
    t.filters.filter_by_date = t.filters.wrapper.find("#filter_by_date");
    t.filters.daterange = t.filters.wrapper.find("#daterange");
    t.filters.location_id = t.filters.wrapper.find("#filter_location_id");
    t.filters.base_location_id = t.filters.wrapper.find("#filter_base_location_id");
    t.filters.tag = t.filters.wrapper.find("#filter_by_tag");
    t.filters.ticket_type = t.filters.wrapper.find("#filter_by_ticket_type");
    t.filters.filter_by_custom_field = t.filters.wrapper.find("#filter_by_custom_field");
    t.filters.filter_by_custom_field_value = t.filters.wrapper.find("#filter_by_custom_field_value");
    t.filters.ticket_handlers = t.filters.wrapper.find("#filter_by_ticket_handlers");

    t.filters.fun = {
        reload_department: function () {
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function (data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function (i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
            t.filters.department.trigger("change");
        },
        reload_problem_category: function () {
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function (i, k) {
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
        reload_sub_category: function () {
            var prblm = t.filters.problem_category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm], function (i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {

                }
            }
            t.filters.sub_category.trigger("change");
        },
        reload_creator: function () {
            t.filters.creator.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.creator.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company
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
            $.each(t.config.status, function (i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
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
                            company_id: t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.Select_the_Ticket_Handler
            }));
            t.filters.handler.trigger("change");
        },
        reload_custom_field: function () {
            t.filters.filter_by_custom_field.select2($.extend({}, select2Opts, {
                ajax: {
                    url: t.config.url.customFieldsForFilter,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: config.translations.customFieldSet,
            }));
            t.filters.filter_by_custom_field.trigger("change");
        },
        reload_custom_field_value: function () {
            t.filters.filter_by_custom_field_value.append(new Option(config.translations.No_Filter, '', false, false));
            t.filters.filter_by_custom_field_value.trigger("change");
        },
        reload_priority: function () {
            $.each(t.config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.text, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_locations: function () {
            $.each(t.config.locations, function (i, k) {
                t.filters.location_id.append(new Option(k.name, k.id));
            });
            t.filters.location_id.trigger("change");
        },
        reload_tags: function () {
            $.each(t.config.tags, function (i, k) {
                t.filters.tag.append(new Option(k.tags, k.id, false, false));
            });
            t.filters.tag.trigger("change");
        },
        reload_base_locations: function () {
            $.each(t.config.base_locations, function (i, k) {
                t.filters.base_location_id.append(new Option(k.name, k.id));
            });
            t.filters.base_location_id.trigger("change");
        },
        reload_ticket_handlers: function () {
            t.filters.ticket_handlers.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.ticket_handlers.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company
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

    // FIX: Reset function with pagination check
    t.reset = function () {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        // Check if pagination is initialized before calling methods
        if (t.pagebtns && t.pagebtns.data && t.pagebtns.data('pagination')) {
            t.pagebtns.pagination("updateItemsOnPage", t.perPage);
        } else {
            console.warn('Pagination not initialized yet, skipping reset');
        }
    };

    t.filters.filteroption = function () {
        if (t.filters.based_on_cre_log.val() == "1" || t.filters.based_on_cre_log.val() == "2") {
            t.filters.creatorcover.removeClass("hide");
        }
    };

    // SINGLE sort handler - removed duplicate
    t.handleSort = function (id, dir) {
        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 2;
        }
        t.renderSortFields();

        // Clear any existing timeout and set new one
        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () {
            t.load();
        }, 300);
    };

    t.renderSortFields = function () {
        var sf = t.sortbtns.find(".sort-fields");
        var s = t.config.sort_dir;
        var $dirSort = t.sortbtns.find(".dir-sort");

        if (s.dir == 1) {
            $dirSort.find("#sortAscSvg").removeClass("hide");
            $dirSort.find("#sortDescSvg").addClass("hide");
        } else {
            $dirSort.find("#sortAscSvg").addClass("hide");
            $dirSort.find("#sortDescSvg").removeClass("hide");
        }

        $dirSort.attr("data-id", s.id);
        $dirSort.attr("data-dir", s.dir == 1 ? "asc" : "desc");

        sf.empty();
        $.each(otherConfig.sort_fields, function (i, d) {
            $('<li class="list-group-item ' + (d.id == s.id ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text +
                '</li>').appendTo(sf);
        });
    };

    // SINGLE event binding for sort - using mousedown to prevent dropdown closing issues
    t.sortbtns.on("mousedown", ".sort-fields li", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var id = $(this).attr("data-id");
        t.handleSort(id);
        t.sortbtns.removeClass("open");
    });

    // SINGLE event binding for dir-sort
    t.sortbtns.on("mousedown", ".dir-sort", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        var id = $(this).attr("data-id");
        t.handleSort(id);
    });

    // Dropdown toggle - prevent duplicate binding
    t.sortbtns.on("click", ".dropdown-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.sortbtns.toggleClass("open");
    });

    // Close dropdown on outside click
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".sort-buttons").length) {
            t.sortbtns.removeClass("open");
        }
    });

    t.renderSortFields();

    function decimalToHoursMinutes(decimalHours) {
        
        if (decimalHours === '' || isNaN(decimalHours) || decimalHours === null || decimalHours === undefined) {
            return '';
        }
        // const num = parseFloat(decimalHours);
        // console.log(num);
        // if (isNaN(num)) return '';
        // const hours = Math.floor(num);
        // console.log(hours);
        // const minutes = Math.round((num - hours) * 60);
        // console.log(minutes);
        // // console.log(`${hours} Hr ${minutes.toString().padStart(2, '0')} Min`);
        // return `${hours} Hr ${minutes.toString().padStart(2, '0')} Min`;

        const parts = decimalHours.toString().split('.');
        const hours = parseInt(parts[0], 10) || 0;
        const minutes = parseInt(parts[1], 10) || 0;

        return `${hours} Hr ${minutes.toString().padStart(2, '0')} Min`;
    }

    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v;
        t.config.other_filters = {};
        if (t.filters.department && t.filters.department.val() !== 'null' && t.filters.department.val() !== null)
            t.config.other_filters.department = t.filters.department.val();
        if (t.filters.status && t.filters.status.val() != 'null' && t.filters.status.val() !== null)
            t.config.other_filters.status = t.filters.status.val();
        if (t.filters.problem_category && t.filters.problem_category.val() != 'null' && t.filters.problem_category.val() !== null)
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        if (t.filters.sub_category && t.filters.sub_category.val() != 'null' && t.filters.sub_category.val() !== null)
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        if (t.filters.priority && t.filters.priority.val() != 'null' && t.filters.priority.val() !== null)
            t.config.other_filters.priority = t.filters.priority.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        if (t.filters.filter_by_date && t.filters.filter_by_date.val() != 'null' && t.filters.filter_by_date.val() !== null)
            t.config.other_filters.daterange = t.filters.daterange.val();
        if (t.filters.location_id && t.filters.location_id.val() != 'null' && t.filters.location_id.val() !== null)
            t.config.other_filters.location_id = t.filters.location_id.val();
        if (t.filters.ticket_type && t.filters.ticket_type.val() != 'null' && t.filters.ticket_type.val() !== null)
            t.config.other_filters.ticket_type = t.filters.ticket_type.val();
        if (t.filters.base_location_id && t.filters.base_location_id.val() != 'null' && t.filters.base_location_id.val() !== null)
            t.config.other_filters.base_location_id = t.filters.base_location_id.val();
        if (t.filters.filter_by_custom_field && t.filters.filter_by_custom_field.val() !== null && t.filters.filter_by_custom_field.val() !== '')
            t.config.other_filters.filter_by_custom_field = t.filters.filter_by_custom_field.val();
        if (t.filters.filter_by_custom_field_value && t.filters.filter_by_custom_field_value.val() !== null && t.filters.filter_by_custom_field_value.val() !== '')
            t.config.other_filters.filter_by_custom_field_value = t.filters.filter_by_custom_field_value.val();
        if (t.filters.ticket_handlers && t.filters.ticket_handlers.val()) {
            t.config.other_filters.ticket_handlers = t.filters.ticket_handlers.val();
        }
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $.trim(t.searchbox.val());
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.load();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.filters.wrapper.modal('hide');
            t.load();
        }
    };

    t._s = function (v) {
        return v || "";
    };

    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/[&<>"']/g, function (m) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[m];
        });
    }

    function truncateTag(tag, maxLength = 20) {
        if (!tag) return '';
        return tag.length > maxLength ? tag.slice(0, maxLength) + '...' : tag;
    }

    function renderTags(tags) {
        if (!tags) return '';
        const tagArray = tags.split(',').map(tag => tag.trim()).filter(tag => tag);
        let html = '';
        tagArray.slice(0, 3).forEach(tag => {
            html += `<span class="badge badge-info m-1" data-bs-toggle="tooltip" title="${escapeHtml(tag)}">${escapeHtml(truncateTag(tag))}</span>`;
        });
        if (tagArray.length > 3) {
            html += `<a href="javascript:void(0)" class="show-tags ms-1"  data-tags='${encodeURIComponent(JSON.stringify(tagArray))}'>+${tagArray.length - 3} More</a>`;
        }
        return html;
    }

    const client = config.client;

    t.renderList = function (i, d) {
        var fc = `<tr>
        <td class="b5-text"><a href=${t.config.url.ticket_info + '/' + t._s(d.id)} target='_blank'>#${t._s(d.id)} </a></td>
        ${t.config.client == 'ril' || t.config.client == 'rolepermission' ? `<td class="b5-text">${t._s(d.seat_no)}</td>` : ''}
        <td class="b5-text">${t._s(d.dept_name)} </td>
        <td class="b5-text">${t._s(d.cat_name)} </td>
        <td class="b5-text">${t._s(d.sub_cat_name)} </td>
        <td class="b5-text">${t._s(d.created_at)} </td>
        <td class="b5-text">${t._s(d.ticket_pickup_time)} </td>
        <td class="b5-text"> ${t._s(d.status_progress_time)} </td>
        <td class="b5-text"> ${t._s(d.resolved_at)} </td>
        <td class="b5-text"> ${t._s(d.username)} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_time_exc_nonwork_hours))} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_time_inc_work_hours))} </td>
        <td class="b5-text">${t._s(d.status_name)} </td>
        <td class="b5-text">${t._s(d.tat)} </td>
        <td class="b5-text">${t._s(d.tat_expire)} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.time_taken_by_resolver))} </td>
        <td class="b5-text"><span data-toggle="tooltip" data-placement="top" title="${t.config.translations.Ticket_Sla_Breached} ">${t._s(d.sla_breached)}</span> <br> <span data-toggle="tooltip" data-placement="top" title="${t.config.translations.Sla_breached_Time}">${t._s(decimalToHoursMinutes(d.sla_breach_time))} </span></td>
        <td class="b5-text">${t._s(d.response_sla)} </td>
        <td class="b5-text">${t._s(d.response_at)} </td>
        <td class="b5-text">${t._s(d.response_sla_breach)} </td>
        <td class="b5-text">${t._s(d.workaround_sla)} </td>
        <td class="b5-text">${t._s(d.workaround_at)} </td>
        <td class="b5-text">${t._s(d.workaround_sla_breach)} </td>
        <td class="b5-text">${t._s(d.no_ticket_resolver)} </td>
        <td class="b5-text"><span data-toggle="tooltip" data-placement="top" title="${d.resolver0_name != undefined ? t.config.translations.Resolver1_name : ''}">${t._s(d.resolver0_name)}</span> <br> <span data-toggle="tooltip" data-placement="top" title="${t.config.translations.resolver1_time}">${t._s(decimalToHoursMinutes(d.resolver0))} </span></td>
        <td class="b5-text"><span data-toggle="tooltip" data-placement="top" title="${d.resolver1_name ? t.config.translations.Resolver2_name : ''}">${t._s(d.resolver1_name)}</span> <br><span data-toggle="tooltip" data-placement="top" title="${t.config.translations.Resolver2_name}"> ${t._s(decimalToHoursMinutes(d.resolver1))} </span></td>
        <td class="b5-text"><span data-toggle="tooltip" data-placement="top" title="${d.resolver2_name ? t.config.translations.Resolver3_name : ''}">${t._s(d.resolver2_name)}</span> <br><span data-toggle="tooltip" data-placement="top" title="${t.config.translations.Resolver_3name}"> ${t._s(decimalToHoursMinutes(d.resolver2))} </span></td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_hold_time))} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_progress_time))} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_status_waiting_for_user))} </td>
        <td class="b5-text">${t._s(decimalToHoursMinutes(d.total_status_waiting_for_vendor))} </td>
        <td class="b5-text">${t._s(d.priority)} </td>
        <td class="b5-text">${t._s(d.location_name)} </td>
        <td class="b5-text">
            ${renderTags(d.t_tags)}
        </td>
        <td class="b5-text">${t._s(d.ticket_type)} </td>`;
        if (["rolepermission", "grdemo", "greenitco"].includes(client)) {
            fc += `<td class="b5-text">${t._s(d.total_task)} </td>
                <td class="b5-text">${t._s(d.completed_task)} </td>
                <td class="b5-text">${t._s(d.incompleted_task)} </td>
                <td class="b5-text">${t._s(d.per_completed_task)} </td>`;
        }
        if (d.ticketTypeFields != undefined && Object.keys(d.ticketTypeFields).length > 0) {
            $.each(d.ticketTypeFields, function (i, v) {
                fc += "<td class='b5-text'>" + (v.value || '') + "</td>";
            })
        }
        if (d.customFieldsFromTable != undefined && Object.keys(d.customFieldsFromTable).length > 0) {
            $.each(d.customFieldsFromTable, function (i, v) {
                fc += "<td class='b5-text'>" + (v.value || '') + "</td>";
            })
        }
        fc += "</tr>";
        t.lg.append(fc);
    };

    // ✅ Show Dot Loader (Same as MyApp)
    t.showLoader = function () {
        if (t.isLoading) return;
        t.isLoading = true;

        // Get table column count
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

        // Store current scroll position to prevent flicker
        var scrollPos = $(window).scrollTop();

        t.lg.html(loaderHtml);

        // Restore scroll position
        $(window).scrollTop(scrollPos);
    };

    // Hide loader function
    t.hideLoader = function () {
        t.isLoading = false;
        // Loader will be replaced by actual data in the done callback
    };

    t.load = function () {
        // Store current scroll position to prevent flicker
        var scrollPos = $(window).scrollTop();

        // Show loader
        t.showLoader();

        // Check if pagebtns exists
        if (t.pagebtns.length === 0) {
            console.error('Pagination container #pagebtns not found!');
            t.hideLoader();
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
            url: otherConfig.url,
            type: "POST",
            data: {
                search: t.config.search || '',
                page: currentPage,
                size: t.perPage || 10,
                main_filter: t.config.main_filter || '',
                filters: t.config.other_filters || {},
                order: t.config.sort_dir || { id: 'id', dir: 1 },
            }
        });

        http.done(function (data) {
            // Hide loader
            t.hideLoader();

            if (typeof data == "object" && typeof data.total != "undefined") {
                t.data = data;

                // Safely update pagination
                try {
                    t.pagebtns.pagination("updateItems", parseInt(data.filtered) || 0);
                    t.pagebtns.pagination("drawPage", parseInt(data.page) || 1);
                } catch (e) {
                    console.warn('Pagination update failed:', e);
                }

                t.lg.empty();
                let start = (parseInt(data.page) - 1) * 10 + 1;
                let end = start + data.data.length - 1;

                // Display total records count with proper formatting
                var summaryHtml = '';
                if (data.filtered != data.total) {
                    summaryHtml =
                        `${config.datatable_translations.showing} ${start} ${config.datatable_translations.to} ${end} ` +
                        `${config.datatable_translations.of} ${data.filtered} ${config.datatable_translations.records} ` +
                        `(${config.datatable_translations.filtered} ${config.datatable_translations.from} ${data.total} ${config.datatable_translations.total_entries})`;
                } else {
                    summaryHtml =
                        `${config.datatable_translations.showing} ${start} ${config.datatable_translations.to} ${end} ` +
                        `${config.datatable_translations.of} ${data.total} ${config.datatable_translations.records}`;
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

                if (data.filtered < 1) {
                    var tableCountHead = $("#tHeadeRow").find("th").length;
                    t.lg.html('<tr class="no-record-found"><td colspan=' + tableCountHead + '>' + config.datatable_translations.empty_result + '</td></tr>');
                    return;
                }
                $.each(data.data, t.renderList);
            }

            // Restore scroll position after data load
            $(window).scrollTop(scrollPos);
        });
        http.fail(function (jqXHR, textStatus, errorThrown) {
            // Hide loader on error
            t.hideLoader();

            console.error('Ajax failed:', {
                status: jqXHR.status,
                statusText: jqXHR.statusText,
                responseText: jqXHR.responseText,
                error: errorThrown
            });

            // Show error message in table
            var colCount = $("#tHeadeRow").find("th").length || 10;
            t.lg.html('<tr class="no-record-found"><td colspan="' + colCount + '" style="text-align: center; padding: 30px 0; color: #dc3545;">'+ config.datatable_translations.error_loading_data +'</td></tr>');

            alert(config.datatable_translations.something_went_wrong);
        });
        http.always(function () {
            t.httpCall = true;
            t.tableCover.removeClass("gload");
        });
    };

    t.pageBtnClicked = function (n, e) {
        if (typeof e !== "undefined") e.preventDefault();
        t.load();
    };

    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_xls + "?q=" + t.config.export_filters;
    };

    t.downloadPDF = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket_pdf + "?q=" + t.config.export_filters;
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

    // reset filter form
    t.btnClrFilter = function () {
        t.filters.department.val("").trigger("change");
        t.filters.status.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.priority.val("").trigger("change");
        t.filters.filter_by_date.val('null').trigger("change");
        t.filters.location_id.val("").trigger("change");
        t.filters.ticket_type.val("").trigger("change");
        t.filters.base_location_id.val("").trigger("change");
        t.filters.filter_by_custom_field.val(null).trigger("change");
        t.filters.filter_by_custom_field_value.val(null).trigger("change");
        t.filters.ticket_handlers.val("").trigger("change");
        t.filters.wrapper.modal('hide');
        // Reload data after clearing filters
        setTimeout(function () {
            t.load();
        }, 300);
    }

    // SINGLE event bindings - removed duplicates
    t.searchbox.on("keypress", $.proxy(t.search, t));
    t.reload.on("click", $.proxy(t.load, t));
    t.searchbtn.on("click", $.proxy(t.search, t));
    t.export.on("click", $.proxy(t.download, t));
    t.export_pdf.on("click", $.proxy(t.downloadPDF, t));
    t.pageLimiter.on("change", function (e) {
        t.reset();
        t.load();
    });

    // FIX: Reset ko pagination ke baad call karo
    t.reset();

    // FIX: Ab select2 initialize karo (select2Opts already defined at top)
    t.filters.department.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_department }));
    t.filters.create_via.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper }));
    t.filters.creator.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.Filter_By_Creator_Logger }));
    t.filters.status.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_status }));
    t.filters.problem_category.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_Problem_Category }));
    t.filters.sub_category.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_Sub_Category }));
    t.filters.handler.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.Filter_By_Ticket_Handler }));
    t.filters.priority.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_Priority }));
    t.filters.location_id.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_Location }));
    t.filters.base_location_id.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.select_base_location }));
    t.filters.tag.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: config.translations.filter_by_tag }));
    t.filters.ticket_type.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper, placeholder: 'Select Ticket Type' }));
    t.filters.based_on_cre_log.select2(select2Opts).on("change", $.proxy(t.filters.filteroption, t));
    t.filters.filter_by_date.select2($.extend({}, select2Opts, { dropdownParent: t.filters.wrapper }));

    t.filters.filter_by_custom_field_value.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.filter_by_custom_field_value.parent(),
        ajax: {
            url: function () {
                var custom_field = t.filters.filter_by_custom_field.val();
                return t.config.url.getCustomFieldsValueForFilter + '/' + custom_field;
            },
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term || '',
                    page: p.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (s) {
                        return { id: s.text, text: s.text };
                    }),
                    pagination: {
                        more: data.pagination ? data.pagination.more : false
                    }
                };
            }
        },
        placeholder: config.translations.customField,
        allowClear: true
    }));

    t.filters.fun.reload_department();
    t.filters.fun.reload_creator();
    t.filters.fun.reload_status();
    t.filters.fun.reload_handler();
    t.filters.fun.reload_priority();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_tags();
    t.filters.fun.reload_base_locations();
    t.filters.fun.reload_custom_field();
    t.filters.fun.reload_custom_field_value();
    t.filters.fun.reload_ticket_handlers();

    t.filters.department.on("change", function () {
        t.filters.problem_category.empty().trigger("change");
        t.filters.sub_category.empty().trigger("change");
        t.filters.fun.reload_problem_category();
    });

    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category, t));
    t.filters.wrapper.on("click", "button", $.proxy(t.search, t));
    t.filters.btnfilterclr.on("click", $.proxy(t.btnClrFilter, t));

    t.filters.filter_by_custom_field.on('change', function () {
        t.filters.filter_by_custom_field_value.val(null).trigger('change');
    });

    // Load initial data
    setTimeout(function () {
        t.load();
    }, 500);
    t.filterbtn.on('click', function () {
        t.filters.wrapper.modal('show');
    });
    t.lg.on("click", ".show-tags", function () {
        const tags = JSON.parse(decodeURIComponent($(this).attr("data-tags")));
        let html = '';
        tags.forEach(function (tag) {
            html += `<span class="badge badge-info m-1">${escapeHtml(tag)}</span>`;
        });
        $("#ticketReportTagListModalBody").html(html);
        bootstrap.Modal.getOrCreateInstance(document.getElementById("ticketReportTagListModal")).show();
    });
};

var TicketReport = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.deviceTab = new TableList(config, {
        tab: "#mainContent",
        url: config.url.list_tickets,
        sort_fields: config.sort_fields.ticket,
        tbl_fields: config.tbl_fields.ticket
    });
    t.content.on("click", ".black-slide-links a", function (e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function () {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });
};