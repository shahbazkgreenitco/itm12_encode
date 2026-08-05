var Ticket = function (config) {
    var t = this;
    t.config = config;
    t.data = {};
    t.httpCall = true;
    t.httpPostPath = "";
    t.config.star_data = false;
    t.content = $("#ticket-list-page");
    t.ticketCardlist = t.content.find("#list-card");
    t.toolbar = t.content.find("#toolbar");
    t.tktSelectAll = t.toolbar.find('#tktSelectAll');
    t.tktPageLen = t.toolbar.find('#tktPageLen');
    t.headerActions = t.content.find('.header-actions-wrapper');
    t.ticketSearchInput = t.headerActions.find('#ticket-search-input');
    t.tktPagecustomStatus = t.headerActions.find('#tktPagecustomStatus');
    t.addCustomStatusBtn = t.headerActions.find('#addCustomStatusBtn');
    t.bulkDelete = t.content.find('#bulk-delete');
    t.bulkAssign = t.content.find('#bulk-assign');
    t.bulkResolve = t.content.find('#bulk-resolve');
    t.bulkMerge = t.content.find('#bulk-merge');
    t.shortItems = t.headerActions.find('#short_items');
    t.sortbtns = t.headerActions.find('#srqSortDrop');
    t.sortAction = t.sortbtns.find('.sort-action');
    t.dropdownAction = t.sortbtns.find('.dropdown-action');
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.currentPage = 1;
    var ticketCmpId= null;
    t.perPage = 10;
    t.totalPages = 1;
    var select2Opts = {
    width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    };
    t.data = {
        problem_categories: [],
        sub_categories: []
    };
    t.btn = {};

    // Modal for the filter the list of ticket
    t.filterBtn = t.headerActions.find('#btnOpenFilter');
    t.filterMdl = t.content.find("#ticketFilterModal");
    t.filterBtn.on('click', function () {
        t.filterMdl.modal("show");
    });

    t.filters = {
        data: {
            problem_categories: {}
        }
    };

    const bulkMerge = document.getElementById('bulk-merge');

    if (bulkMerge) {
        const existingTooltip = bootstrap.Tooltip.getInstance(bulkMerge);

        if (existingTooltip) {
            existingTooltip.dispose();
        }

        new bootstrap.Tooltip(bulkMerge, {
            placement: 'bottom',
            container: 'body',
            fallbackPlacements: []
        });
    }

    $(document).on('click', '.bulk-action-trigger', function (e) {
            e.stopPropagation();
        var wrapper = $(this).closest('.bulk-action-wrapper');
        $('.bulk-action-wrapper').not(wrapper).removeClass('active');
        wrapper.toggleClass('active');
    });
    $(document).on('click', function () {
        $('.bulk-action-wrapper').removeClass('active');
    });

    $(document).on('click', '.bulk-action-popover', function (e) {
        e.stopPropagation();
    });

    t.filterMdl.status = t.filterMdl.find("#filter_by_status"),
    t.filterMdl.priority = t.filterMdl.find("#filter_by_priority"),
    t.filterMdl.tag = t.filterMdl.find("#filter_by_tag"),
    t.filterMdl.created_via = t.filterMdl.find("#filter_created_via"),
    t.filterMdl.location_id = t.filterMdl.find("#filter_by_location_id"),
    t.filterMdl.base_location_id = t.filterMdl.find("#filter_base_location_id"),
    t.filterMdl.department = t.filterMdl.find("#filter_by_department"),
    t.filterMdl.problem_category = t.filterMdl.find("#filter_by_problem_category"),
    t.filterMdl.sub_category = t.filterMdl.find("#filter_by_sub_category"),
    t.filterMdl.ticket_handlers = t.filterMdl.find("#filter_by_ticket_handlers"),
    t.filterMdl.ticket_creator = t.filterMdl.find("#filter_by_ticket_creator"),
    t.filterMdl.based_on = t.filterMdl.find("#filter_by_date"),
    t.filterMdl.from_date = t.filterMdl.find("#filter_by_from_date"),
    t.filterMdl.to_date = t.filterMdl.find("#filter_by_to_date"),
    t.filterMdl.date_range = t.filterMdl.find("#daterange")
    t.filterMdl.creatorcover = t.filterMdl.find(".creatorcover");
    t.filterMdl.based_on_cre_log = t.filterMdl.find("#filter_based_on_cre_log"),
    t.filterMdl.device = t.filterMdl.find("#filter_by_device"),
    t.filters.filter_by_ticket_or_SR  = t.filterMdl.find("#filter_by_ticket_or_SR"),
    t.filterMdl.filter_by_vip_tickets = t.filterMdl.find("#filter_by_vip_tickets"),
    t.filterMdl.sla_breached = t.filterMdl.find("#filter_by_sla_breached"),
    t.filterMdl.feedback = t.filterMdl.find("#filter_by_feedback"),
    t.filterMdl.ticket_type = t.filterMdl.find("#filter_by_ticket_type"),
    t.filterMdl.filter_by_merge = t.filterMdl.find("#filter_by_merge"),
    t.filterMdl.filter_by_custom_field = t.filterMdl.find("#filter_by_custom_field"),
    t.filterMdl.filter_by_custom_field_value = t.filterMdl.find("#filter_by_custom_field_value"),
    t.filterMdl.filter_based_star = t.filterMdl.find("#filter_based_star"),
    t.filterMdl.filter_by_task = t.filterMdl.find("#filter_task");
    t.btn.filter = t.filterMdl.find('#btnFilter'),
    t.btn.clear = t.filterMdl.find('#btnClrFilter'),

    t.filterMdl.status.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl.status.parent(),
        placeholder: t.config.translations.select_status,
        ajax: {
            url: t.config.url.getStatusByAjax,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text?.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    }));
    t.filters.filteroption = function() {
        if (t.filterMdl.based_on_cre_log.val() == "1" || t.filterMdl.based_on_cre_log.val() == "2") {
            t.filterMdl.creatorcover.removeClass("hide");
        } else {
            t.filterMdl.creatorcover.addClass("hide");
        }
    };
    t.filterMdl.ticket_creator.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_ticket_creator }));
    t.filterMdl.based_on_cre_log.select2($.extend({}, select2Opts, { dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_creator_logger })).on("change", $.proxy(t.filters.filteroption));
    t.filterMdl.department.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_department }));
    t.filterMdl.problem_category.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_problem_category }));
    t.filterMdl.sub_category.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_sub_category }));
    t.filterMdl.priority.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_priority }));
    t.filterMdl.tag.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_tag }));
    t.filterMdl.created_via.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_created_via }));
    t.filterMdl.location_id.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_location }));
    t.filterMdl.base_location_id.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_base_location }));
    t.filters.filter_by_ticket_or_SR .select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_ticket_or_service_request }));
    t.filterMdl.filter_by_vip_tickets.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_vip_tickets }));
    t.filterMdl.sla_breached.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_sla_breached }));
    t.filterMdl.feedback.select2($.extend({}, select2Opts, { dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_feedback }));
    t.filterMdl.ticket_type.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.select_ticket_type}));
    t.filterMdl.filter_by_merge.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_merge }));
    t.filterMdl.filter_by_custom_field.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.custom_field }));
    t.filterMdl.filter_by_custom_field_value.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterMdl,
        ajax: {
            url: function () {
                var custom_field = t.filterMdl.filter_by_custom_field.val();
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
        placeholder: t.config.translations.custom_field_value,
        allowClear: true
    }));
    t.filterMdl.filter_based_star.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_by_star }));
    t.filterMdl.based_on.select2($.extend({}, select2Opts, {dropdownParent: t.filterMdl, placeholder: t.config.translations.filter_based_on }));

    t.filterMdl.ticket_handlers.select2($.extend({}, select2Opts, { 
        placeholder: t.config.translations.filter_by_ticket_handler,
        dropdownParent: t.filterMdl.ticket_handlers.parent(),
        templateResult: function (data) {
            if (!data.id) {
                return data.text; 
            }
            let s = {
                text: data.text,
                email: $(data.element).data("email"),
                img_path: $(data.element).data("img-path"),
                status: $(data.element).data("status"),
                employee_num: $(data.element).data("employee-num"),
            };
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(s,imgPaddingLeft);
        },
        templateSelection: function (data) {
            return data.text; 
        }
    }));

    t.filterMdl.filter_by_task.select2($.extend({}, {
        width:'100%',
        allowClear:true,
        dropdownParent: t.filterMdl,
        placeholder: t.config.translations.filter_by_task,
        ajax: {
            url: t.config.url.getTasks,
            dataType: "json",
            method: "post",
            data: function (params) {
                return {
                    "_token": t.config.token,
                    page: params.page || 1,
                    requested_for: 'drop-down',
                    search: params.term || ''
                };
            },
            delay: 300,
            processResults: function (data) {
                return {
                    results: data.data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name,
                            ticket_id: item.ticketId
                        };
                    }),
                    pagination: {
                        more: data.pagination?.more || false
                    }
                };
            }
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            return $('<div>' + "#" + data.id + ' - '  + (data.text.length > 30 ? data.text.substring(0, 30) + '...' : data.text) + '</div>');
        },
        templateSelection: function (data, container) {
            if (!data || $.isEmptyObject(data) || !data.id) return 'Filter By Task';
            $(container).attr('title', data.text);
            return $('<div>' + "#" + data.id + ' - '  + (data.text.length > 30 ? data.text.substring(0, 30) + '...' : data.text) + '</div>');
        }
    }));


    // Pehle yeh run karein to check actual gap kitna hai
    t.filterMdl.on('select2:open', function(e) {
        setTimeout(function() {
            var $dropdown = $('.select2-dropdown');
            var $select = $('.select2-container--open');
            
            if ($dropdown.length) {
                var dropdownTop = $dropdown.offset().top;
                var selectBottom = $select.offset().top + $select.outerHeight();
                var gap = dropdownTop - selectBottom;
                
                console.log('Gap between select and dropdown:', gap + 'px');
                
                // Agar gap 2px se zyada hai to force set karein
                if (gap > 2) {
                    $dropdown.css('margin-top', '0px');
                    console.log('Gap fixed!');
                }
            }
        }, 50);
    });

    t.config.userDropdownFormat = function (s, imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        function truncateText(text, length) {
            return text && text.length > length ? text.substring(0, length) + "..." : text;
        }

        var name = truncateText(s.text, 50);
        var email = truncateText(s.email ? s.email : "", 50);

        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='bi bi-person' style='padding-right: 3px;'></i>" + name + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class='bi bi-envelope' style='padding-right: 3px;'></i>" + email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class='bi bi-credit-card' style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    t.filters.fun = {
        reload_priority: function() {
            $.each(t.config.priorities, function(i, k) {
                t.filterMdl.priority.append(new Option(k.name, k.id, false, false));
            });
            t.filterMdl.priority.trigger("change");
        },
        reload_department: function() {
            t.filterMdl.department.empty().append(new Option(t.config.translations.no_filter, null, false, false));
            console.log("companyId", companyId);
            $.get(t.config.url.departments_based_on_privilage + "/" + companyId, function(data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i, k) {
                        t.filterMdl.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filterMdl.department.trigger("change");
                }
            });
            t.filterMdl.department.trigger("change");
        },
        reload_problem_category: function() {
            t.filterMdl.problem_category.empty();
            var department = t.filterMdl.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function(i, k) {
                            t.filterMdl.problem_category.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                                t.filters.data.problem_categories["sc" + k.id] = k.sub;
                            }
                        });
                        t.filterMdl.problem_category.trigger("change");
                    }
                });
            }
            t.filterMdl.problem_category.trigger("change");
        },
        reload_sub_category: function() {
            t.filterMdl.sub_category.empty();
            var prblm = t.filterMdl.problem_category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(prblm, function(i, v) {
                        $.each(t.filters.data.problem_categories["sc" + v], function(i, k) {
                            t.filterMdl.sub_category.append(new Option(k.name, k.id, false, false));
                        });
                    });
                } catch (e) {

                }
            }
            t.filterMdl.sub_category.trigger("change");
        },
        reload_ticket_handlers: function() {
            $.each(t.config.ticket_handlers, function(i, k) {
                let optionData = new Option(k.full_name, k.id, false, false);
                optionData.setAttribute("data-email", k.email || "");
                optionData.setAttribute("data-img-path", k.img_path || "");
                optionData.setAttribute("data-status", k.status || "");
                optionData.setAttribute("data-employee-num", k.employee_num || "");
                t.filterMdl.ticket_handlers.append(optionData);
            });
            t.filterMdl.ticket_handlers.trigger("change");
        },
        reload_ticket_creator: function() {
            t.filterMdl.ticket_creator.select2($.extend({}, select2Opts, {
                dropdownParent: t.filterMdl.ticket_creator.parent(),
                ajax: {
                    url: t.config.url.getUserByAjax,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            user : 'all',
                            company_id: companyId,  
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: t.config.translations.select_the_user,
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "32px";
                    return t.config.userDropdownFormat(data, imgPaddingLeft);
                },
            }));
            t.filterMdl.ticket_creator.trigger("change");
        },
        reload_device: function() {
            t.filterMdl.device.select2($.extend({}, select2Opts, {
                dropdownParent: t.filterMdl.device.parent(),
                ajax: {
                    url: t.config.url.getDeviceFilterByAjax,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: t.config.translations.select_device,
                templateResult: function(s) {
                    if (typeof s.loading != "undefined" && s.loading) {
                        return $("<div>" + s.text + "</div>");
                    }
                    a = "<span class='so-t'>" + s.asset_tag + "</span>";
                    if (s.asset_name != null) {
                        a += "-<span class='so-t'>" + s.asset_name + "</span>";
                    }
                    return $("<div>" + a + "</div>");
                }
            }));
            t.filterMdl.device.trigger("change");
        },
        reload_tags: function() {
            $.each(t.config.tags, function(i, k) {
                t.filterMdl.tag.append(new Option(k.tags, k.id, false, false));
            });
            t.filterMdl.tag.trigger("change");
        },
        reload_created_via: function() {
            t.filterMdl.created_via.empty().append(new Option(t.config.translations.filter_created_via, null, false, false));
            $.each(t.config.created_via_filter, function(i, k) {
                t.filterMdl.created_via.append(new Option(k, i, false, false));
            });
            t.filterMdl.created_via.trigger("change");
        },
        reload_locations: function() {
            t.filterMdl.location_id.empty().append(new Option(t.config.translations.filter_by_location, null, false, false));
            $.each(t.config.locations, function(i, k) {
                t.filterMdl.location_id.append(new Option(k.name, k.id));
            });
            t.filterMdl.location_id.trigger("change");
        },
        reload_base_locations: function() {
            t.filterMdl.base_location_id.empty().append(new Option(t.config.translations.filter_by_location, null, false, false));
            $.each(t.config.base_locations, function(i, k) {
                t.filterMdl.base_location_id.append(new Option(k.name, k.id));
            });
            t.filterMdl.base_location_id.trigger("change");
        },
        reload_ticket_type: function() {
            var department = t.filterMdl.department.val();
            if (department != "" && department != null && department != "null") {
                t.filterMdl.ticket_type.empty().append(new Option(t.config.translations.no_filter, null, false, false));
                $.get(t.config.url.ticket_type + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function(i, k) {
                            t.filterMdl.ticket_type.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                                t.filters.data.ticket_type["sc" + k.id] = k.sub;
                            }
                        });
                        t.filterMdl.ticket_type.trigger("change");
                    }
                });
            }
            t.filterMdl.ticket_type.trigger("change");
        },
        reload_custom_field: function () {
            t.filterMdl.filter_by_custom_field.select2($.extend({}, select2Opts, {
                dropdownParent: t.filterMdl,           
                ajax: {
                    url: t.config.url.customFieldsForFilter,
                    dataType: "json",
                    delay: 300,
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || [],
                            pagination: data.pagination || {
                                more: false
                            }
                        };
                    }
                },
                allowClear:true,
                placeholder: t.config.translations.custom_field_set,
            }));
            t.filterMdl.filter_by_custom_field.trigger("change");
        },
        reload_custom_field_value: function () {
            t.filterMdl.filter_by_custom_field_value.append(new Option(t.config.translations.no_filter,null, false, false));
            t.filterMdl.filter_by_custom_field_value.trigger("change");
        },
    }

    t.filters.filteroption();
    t.filters.fun.reload_department();
    t.filters.fun.reload_priority();
    t.filters.fun.reload_ticket_type();
    t.filters.fun.reload_created_via();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_ticket_creator();
    t.filters.fun.reload_base_locations();
    t.filters.fun.reload_device();
    t.filters.fun.reload_ticket_handlers();
    t.filters.fun.reload_custom_field();
    t.filters.fun.reload_custom_field_value();
    t.filterMdl.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filterMdl.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));

    t.filterMdl.filter_by_custom_field.on('change', function () {
        t.filterMdl.filter_by_custom_field_value.val(null).trigger('change'); 
    });

    t.applyDashboardFilters = function() {
        t.config.other_filters = t.config.other_filters || {};

        if (t.config.dashboard_filter && t.config.dashboard_filter != '') {
            t.config.other_filters.dashboard_filter = t.config.dashboard_filter;

            if (t.config.dashboard_type && t.config.dashboard_type != '') {
                t.config.other_filters.dashboard_type = t.config.dashboard_type;
            }

            if (t.config.dashboard_status && t.config.dashboard_status != '') {
                t.config.other_filters.dashboard_status = t.config.dashboard_status;
            }

            if (t.config.dashboard_date && t.config.dashboard_date != '') {
                t.config.other_filters.dashboard_date = t.config.dashboard_date;
            }
        }

        if (t.config.dashboard_department_id && t.config.dashboard_department_id != '') {
            t.config.other_filters.department = [t.config.dashboard_department_id];
        }

        if (t.config.dashboard_date_range && t.config.dashboard_date_range != '') {
            t.config.other_filters.based_on = 1;
            t.config.other_filters.date_range = t.config.dashboard_date_range;
        }

        if (t.config.dashboard_ticket_type && t.config.dashboard_ticket_type != '') {
            t.config.other_filters.ticket_type = [t.config.dashboard_ticket_type];
        }

        if (t.config.dashboard_priority_id && t.config.dashboard_priority_id != '') {
            t.config.other_filters.priority = [t.config.dashboard_priority_id];
        }

        if (t.config.dashboard_source_id && t.config.dashboard_source_id != '') {
            t.config.other_filters.created_via = t.config.dashboard_source_id;
        }

        if (t.config.dashboard_city_id && t.config.dashboard_city_id != '') {
            t.config.other_filters.dashboard_city_id = t.config.dashboard_city_id;
        }
    };
    t.applyDashboardFilters();

    t.tktPagecustomStatus.on('change',function(){
        t.cache_filter_values();
        t.refreshTicketList();
    });

    t.cache_filter_values = function() {
        t.config.search = $.trim(t.ticketSearchInput.val());
        t.config.other_filters = {};
        let statusValue = t.filterMdl.status.val();
        if (statusValue && statusValue.length > 0) {
            t.config.other_filters.status = statusValue;
        } else {
            let customStatus = t.tktPagecustomStatus.val();

            if (customStatus && customStatus !== 'null') {
                t.config.other_filters.status = [customStatus];
            }
        }
        if(t.filterMdl.priority && t.filterMdl.priority.val() && t.filterMdl.priority.val() != 'null'){
            t.config.other_filters.priority = t.filterMdl.priority.val();
        }
        if(t.filterMdl.tag && t.filterMdl.tag.val() && t.filterMdl.tag.val() != 'null'){
            t.config.other_filters.tag = t.filterMdl.tag.val();
        }
        if(t.filterMdl.created_via && t.filterMdl.created_via.val() && t.filterMdl.created_via.val() != 'null'){
            t.config.other_filters.created_via = t.filterMdl.created_via.val();
        }
        if(t.filterMdl.location_id && t.filterMdl.location_id.val() && t.filterMdl.location_id.val() != 'null'){
            t.config.other_filters.location_id = t.filterMdl.location_id.val();
        }
        if(t.filterMdl.base_location_id && t.filterMdl.base_location_id.val() && t.filterMdl.base_location_id.val() != 'null'){
            t.config.other_filters.base_location_id = t.filterMdl.base_location_id.val();
        }
        if(t.filterMdl.department && t.filterMdl.department.val() && t.filterMdl.department.val() != 'null'){
            t.config.other_filters.department = t.filterMdl.department.val();
        }
        if(t.filterMdl.problem_category && t.filterMdl.problem_category.val() && t.filterMdl.problem_category.val() != 'null'){
            t.config.other_filters.problem_category = t.filterMdl.problem_category.val();
        }
        if(t.filterMdl.sub_category && t.filterMdl.sub_category.val() && t.filterMdl.sub_category.val() != 'null'){
            t.config.other_filters.sub_category = t.filterMdl.sub_category.val();
        }
        if(t.filterMdl.ticket_handlers && t.filterMdl.ticket_handlers.val() && t.filterMdl.ticket_handlers.val() != 'null'){
            t.config.other_filters.ticket_handlers = t.filterMdl.ticket_handlers.val();
        }
        if(t.filterMdl.ticket_creator && t.filterMdl.ticket_creator.val() && t.filterMdl.ticket_creator.val() != 'null'){
            t.config.other_filters.ticket_creator = t.filterMdl.ticket_creator.val();
        }
        if(t.filterMdl.based_on && t.filterMdl.based_on.val() && t.filterMdl.based_on.val() != 'null'){
            t.config.other_filters.based_on = t.filterMdl.based_on.val();
        }
        if(t.filterMdl.based_on && t.filterMdl.based_on.val() && t.filterMdl.based_on.val() != 'null'){
            t.config.other_filters.date_range = t.filterMdl.date_range.val();       
        }
        if(t.filterMdl.based_on_cre_log && t.filterMdl.based_on_cre_log.val() && t.filterMdl.based_on_cre_log.val() != 'null'){
            t.config.other_filters.based_on_cre_log = t.filterMdl.based_on_cre_log.val();
        }
        if(t.filterMdl.device && t.filterMdl.device.val() && t.filterMdl.device.val() != 'null'){
            t.config.other_filters.device = t.filterMdl.device.val();
        }
        if( t.filters.filter_by_ticket_or_SR  &&  t.filters.filter_by_ticket_or_SR .val() &&  t.filters.filter_by_ticket_or_SR .val() != 'null'){
            t.config.other_filters.filter_by_ticket_or_SR =  t.filters.filter_by_ticket_or_SR .val();
            console.log(t.config.other_filters.filter_by_ticket_or_SR);
            
        }
        if(t.filterMdl.filter_by_vip_tickets && t.filterMdl.filter_by_vip_tickets.val() && t.filterMdl.filter_by_vip_tickets.val() != 'null'){
            t.config.other_filters.filter_by_vip_tickets = t.filterMdl.filter_by_vip_tickets.val();
        }
        if(t.filterMdl.sla_breached && t.filterMdl.sla_breached.val() && t.filterMdl.sla_breached.val() != 'null'){
            t.config.other_filters.sla_breached = t.filterMdl.sla_breached.val();
        }
        if(t.filterMdl.feedback && t.filterMdl.feedback.val() && t.filterMdl.feedback.val() != 'null'){
            t.config.other_filters.feedback = t.filterMdl.feedback.val();
        }
        if(t.filterMdl.ticket_type && t.filterMdl.ticket_type.val() && t.filterMdl.ticket_type.val() != 'null'){
            t.config.other_filters.ticket_type = t.filterMdl.ticket_type.val();
        }
        if(t.filterMdl.filter_by_merge && t.filterMdl.filter_by_merge.val() && t.filterMdl.filter_by_merge.val() != 'null'){
            // t.config.other_filters.filter_by_merge = t.filterMdl.filter_by_merge.val();
            t.config.other_filters.merge = t.filterMdl.filter_by_merge.val();
        }
        if(t.filterMdl.filter_by_custom_field && t.filterMdl.filter_by_custom_field.val() && t.filterMdl.filter_by_custom_field.val() != 'null'){
            t.config.other_filters.filter_by_custom_field = t.filterMdl.filter_by_custom_field.val();
        }
        if(t.filterMdl.filter_by_custom_field_value && t.filterMdl.filter_by_custom_field_value.val() && t.filterMdl.filter_by_custom_field_value.val() != 'null'){
            t.config.other_filters.filter_by_custom_field_value = t.filterMdl.filter_by_custom_field_value.val();
        }
        if(t.filterMdl.filter_based_star && t.filterMdl.filter_based_star.val() && t.filterMdl.filter_based_star.val() != 'null'){
            t.config.other_filters.filter_based_star = t.filterMdl.filter_based_star.val();
        }
        if(t.filterMdl.filter_by_task && t.filterMdl.filter_by_task.val() && t.filterMdl.filter_by_task.val() != 'null'){
            t.config.other_filters.filter_by_task = t.filterMdl.filter_by_task.val();
        }

        t.applyDashboardFilters();
        var jobj = {"search": t.config.search, "other_filters": t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filterMdl.date_range.val(), false);
        t.filterMdl.modal("hide");
    };

    t.btnClrFilter = function() {
        if(t.filterMdl.status) t.filterMdl.status.val("").trigger("change");
        if(t.filterMdl.priority) t.filterMdl.priority.val("").trigger("change");
        if(t.filterMdl.tag) t.filterMdl.tag.val("").trigger("change");
        if(t.filterMdl.created_via) t.filterMdl.created_via.val("").trigger("change");
        if(t.filterMdl.location_id) t.filterMdl.location_id.val("").trigger("change");
        if(t.filterMdl.base_location_id) t.filterMdl.base_location_id.val("").trigger("change");
        if(t.filterMdl.department) t.filterMdl.department.val("").trigger("change");
        if(t.filterMdl.problem_category) t.filterMdl.problem_category.empty().val("").trigger("change");
        if(t.filterMdl.sub_category) t.filterMdl.sub_category.empty().val("").trigger("change");
        if(t.filterMdl.ticket_handlers) t.filterMdl.ticket_handlers.val("").trigger("change");
        if(t.filterMdl.ticket_creator) t.filterMdl.ticket_creator.val("").trigger("change");
        if(t.filterMdl.based_on) t.filterMdl.based_on.val("null").trigger("change");
        if(t.filterMdl.from_date) t.filterMdl.from_date.val("");
        if(t.filterMdl.to_date) t.filterMdl.to_date.val("");
        if(t.filterMdl.date_range) t.filterMdl.date_range.val('');
        if(t.filterMdl.based_on_cre_log) t.filterMdl.based_on_cre_log.val("").trigger("change");
        if(t.filterMdl.device) t.filterMdl.device.val("").trigger("change");
        if( t.filters.filter_by_ticket_or_SR )  t.filters.filter_by_ticket_or_SR .val("").trigger("change");
        if(t.filterMdl.filter_by_vip_tickets) t.filterMdl.filter_by_vip_tickets.val("").trigger("change");
        if(t.filterMdl.sla_breached) t.filterMdl.sla_breached.val("").trigger("change");
        if(t.filterMdl.feedback) t.filterMdl.feedback.val("").trigger("change");
        if(t.filterMdl.ticket_type) t.filterMdl.ticket_type.val("").trigger("change");
        if(t.filterMdl.filter_by_merge) t.filterMdl.filter_by_merge.val("").trigger("change");
        if(t.filterMdl.filter_by_custom_field) t.filterMdl.filter_by_custom_field.val("").trigger("change");
        if(t.filterMdl.filter_by_custom_field_value) t.filterMdl.filter_by_custom_field_value.val("").trigger("change");
        if(t.filterMdl.filter_based_star) t.filterMdl.filter_based_star.val("").trigger("change");
        if(t.filterMdl.filter_by_task) t.filterMdl.filter_by_task.val("").trigger("change");
        t.cache_filter_values();
        resetFilterCount();
        t.filterMdl.modal("hide");
        t.refreshTicketList();
    };

    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = t.ticketSearchInput.validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.refreshTicketList();
        }
        else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.refreshTicketList();
        }
    };

    t.btn.filter.on("click", t.search);
    t.btn.clear.on("click", t.btnClrFilter);

    // modal to add the custome statuses
    CustomStatus(t, select2Opts);

    t.showCardLoader = function () {

        if (!t.ticketCardlist.length) {
            return;
        }

        // Duplicate loader prevent
        if (t.ticketCardlist.find(".card-loader-wrapper").length) {
            return;
        }

        t.ticketCardlist.css("position", "relative");

        var cardLoaderHtml = `
            <div class="card-loader-wrapper pt-5">
                <div class="text-center p-5">
                    <svg id="kanban-board-loader" class="fa-spin" stroke="#dc2626" fill="#dc2626" width="80" height="80" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                            <style>
                                @keyframes spin {
                                    0% { transform: rotate(0deg); }
                                    100% { transform: rotate(360deg); }
                                }
                                .fa-spin {
                                    animation: spin 1s linear infinite;
                                }
                            </style>
                            <g>
                                <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                    c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                    c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                    c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                    c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                                </path>
                            </g>
                        </svg>
                    <p style="margin-top:12px;color:#dc2626;font-size:14px;font-weight:500;"> Loading tickets... </p>
                </div>
            </div>
        `;  

        // Inject keyframe once
        if (!document.getElementById("amg-loader-style")) {
            $("head").append(`
                <style id="amg-loader-style">
                    @keyframes amgSpin {
                        from { transform: rotate(0deg); }
                        to { transform: rotate(360deg); }
                    }
                </style>
            `);
        }

        t.ticketCardlist.append(cardLoaderHtml);
    };

    t.hideCardLoader = function () {
        t.ticketCardlist.find(".card-loader-wrapper").remove();
    };

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
        t.loadTicketList();
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
        t.loadTicketList();
    });

    /* Keep dropdown open when clicking inside */
    t.shortItems.on("click", function (e) {
        e.stopPropagation();
    });

    /* Close outside click */
    $(document).on("click", function () {
        t.shortItems.removeClass("show");
    });

    t.loadTicketList = function (e) {
        t.showCardLoader();

        $.ajax({
            type: "POST",
            url: t.config.url.jx_tickets,
            headers: { 'X-CSRF-TOKEN': t.config.csrf },
            data: {
                search: t.config.search,
                main_filter: t.config.main_filter,
                filters: t.config.other_filters,
                page: t.currentPage || 1,
                size: t.perPage,
                order: t.config.sort_dir,
                company_id: companyId,
                toggle: t.toggle,
                location: t.config.location_filter,
                department: t.config.department_filter,
                based_on: t.config.based_on_filter,
                daterange: t.config.daterange_filter,
                sub_filter: t.config.sub_filter,
                filterFromReport: t.config.filterFromReport,
                status: t.config.redirect_status,
                global_search_pc: t.config.global_search_pc,
                techId: t.config.tech_id,
                tagId: config.tag_id,
            },
            success: function (response) {
                // Clear existing cards
                t.hideCardLoader();

                // Get counts from response
                var totalRecords = response.total || 0;
                var filteredRecords = response.filtered || 0;
                var currentPage = parseInt(response.page) || t.currentPage;

                $('#customStatusCount').removeClass('d-none').text(filteredRecords);

                // Calculate total pages based on filtered records
                t.totalPages = Math.ceil(filteredRecords / t.perPage);
                t.currentPage = currentPage;

                // Check if search or filter is applied
                var isSearchOrFilterApplied = t.checkIfSearchOrFilterApplied();

                // Update available records text
                t.updateAvailableRecords(totalRecords, filteredRecords, isSearchOrFilterApplied);

                var html = '';

                // Loop through the data array and create cards
                if (response.data && response.data.length) {

                    $.each(response.data, function (index, ticket) {
                        html += t.generateTicketCard(ticket);
                    });

                } else {
                    html = `<div class="no-data text-center"> No tickets found</div>`;
                }
                t.ticketCardlist.html(html);
                $('.form-check-input').each(function () {
                    let ticketId = $(this).val();
                    if (t.selectedTickets[ticketId]) {
                        $(this).prop('checked', true);
                    }
                });

                // Update pagination UI
                t.updateCustomPagination(response);
                t.initTooltips();
            },
            error: function (xhr, status, error) {
                t.hideCardLoader();
                console.error("Error loading tickets:", error);
                t.ticketCardlist.html('<div class="error-message">Error loading tickets. Please try again.</div>');
            }
        });
    }

    // Function to check if search or any filter is applied
    t.checkIfSearchOrFilterApplied = function () {
        // Check if search term exists
        if (t.config.search && t.config.search.trim() !== '') {
            return true;
        }

        // Check if main filter is applied and not default
        if (t.config.main_filter && t.config.main_filter !== 'all' && t.config.main_filter !== '') {
            return true;
        }

        // Check if other filters are applied
        if (t.config.other_filters && Object.keys(t.config.other_filters).length > 0) {
            for (var key in t.config.other_filters) {
                if (t.config.other_filters[key] && t.config.other_filters[key] !== '' && t.config.other_filters[key] !== 'all') {
                    return true;
                }
            }
        }

        return false;
    }

    // Function to update available records text
    t.updateAvailableRecords = function (total, filtered, isFilterApplied) {
        var $infoSpan = $('.b5-text.text-muted');

        if (isFilterApplied && filtered !== total) {
            // When search or filter is applied
            var recordText = filtered === 1 ? 'record' : 'records';
            $infoSpan.html(`Available ${filtered} ${recordText} (filtered from ${total} total records)`);
        } else if (filtered !== total) {
            // When automatic filtering (not user applied)
            var recordText = filtered === 1 ? 'record' : 'records';
            $infoSpan.html(`Available ${filtered} ${recordText} (filtered from ${total} total records)`);
        } else {
            // No filters applied, show total
            var recordText = total === 1 ? 'record' : 'records';
            $infoSpan.html(`Available ${total} ${recordText}`);
        }
    }

    // Function to update custom pagination
    t.updateCustomPagination = function (response) {
        var totalItems = response.filtered ?? response.total ?? 0;
        var currentPage = parseInt(response.page) || t.currentPage;
        t.totalPages = Math.ceil(totalItems / t.perPage);
        t.currentPage = currentPage;

        // Generate pagination buttons
        t.generatePaginationButtons(currentPage, t.totalPages);
    }

    // Function to generate pagination buttons
    t.generatePaginationButtons = function (currentPage, totalPages) {
        var $paginationUl = $('#pagebtns');
        $paginationUl.empty();

        // Hide pagination if no pages or only 1 page
        if (totalPages <= 1) {
            $paginationUl.hide();
            return;
        } else {
            $paginationUl.show();
        }

        // Add Previous button
        var prevDisabled = currentPage <= 1 ? 'disabled' : '';
        var prevButton = `
        <li class="page-item ${prevDisabled}">
            <button class="tkt-pagination btn-prev-next" data-page="${currentPage - 1}" ${prevDisabled ? 'disabled' : ''}>
                <svg width="12" height="12" viewBox="0 0 8 12" fill="none">
                    <path d="M6.5 11L1.5 6L6.5 1" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                ${t.config.translations.previous}
            </button>
        </li>
    `;
        $paginationUl.append(prevButton);

        // Calculate visible page range
        var maxVisible = 5;
        var startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        var endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        // Add first page if not visible
        if (startPage > 1) {
            $paginationUl.append(`
            <li class="page-item">
                <a class="page-link" href="#" data-page="1">1</a>
            </li>
        `);
            if (startPage > 2) {
                $paginationUl.append(`
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `);
            }
        }

        // Add page numbers
        for (var i = startPage; i <= endPage; i++) {
            var activeClass = i === currentPage ? 'active' : '';
            $paginationUl.append(`
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
        }

        // Add last page if not visible
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                $paginationUl.append(`
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `);
            }
            $paginationUl.append(`
            <li class="page-item">
                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
            </li>
        `);
        }

        // Add Next button
        var nextDisabled = currentPage >= totalPages ? 'disabled' : '';
        var nextButton = `
        <li class="page-item ${nextDisabled}">
            <button class="tkt-pagination btn-prev-next" data-page="${currentPage + 1}" ${nextDisabled ? 'disabled' : ''}>
                ${t.config.translations.next}
                <svg width="12" height="12" viewBox="0 0 8 12" fill="none">
                    <path d="M1.5 11L6.5 6L1.5 1" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </li>
    `;
        $paginationUl.append(nextButton);

        // Bind click events to pagination buttons
        t.bindPaginationEvents();
    }

    // Function to bind pagination click events
    t.bindPaginationEvents = function () {
        // Handle previous/next buttons
        $('.btn-prev-next').off('click').on('click', function (e) {
            e.preventDefault();
            var $btn = $(this);
            if ($btn.hasClass('disabled') || $btn.prop('disabled')) {
                return;
            }
            var page = parseInt($btn.data('page'));
            if (!isNaN(page) && page >= 1 && page <= t.totalPages) {
                t.goToPage(page);
            }
        });

        // Handle page number links
        $('#pagebtns .page-link').off('click').on('click', function (e) {
            e.preventDefault();
            var $link = $(this);
            var page = parseInt($link.data('page'));
            if (!isNaN(page) && page >= 1 && page <= t.totalPages) {
                t.goToPage(page);
            }
        });
    }

    // Function to navigate to a specific page
    t.goToPage = function (page) {
        if (page === t.currentPage) return;
        if (page < 1 || page > t.totalPages) return;

        t.currentPage = page;
        t.loadTicketList();
    }

    // Page length change handler
    t.tktPageLen.select2({ width: "auto", theme: 'custom' }).on("change", function (e) {
        var newPerPage = parseInt(t.tktPageLen.val());

        if (!isNaN(newPerPage) && newPerPage > 0) {
            t.perPage = newPerPage;
            t.currentPage = 1; // Reset to first page
            t.loadTicketList();
        }
    });

    // Page button click handler (for backward compatibility)
    t.pageBtnClicked = function (event, page) {
        if (event && event.preventDefault) {
            event.preventDefault();
        }
        t.goToPage(page);
    };


    // Optional: Add refresh method
    t.refreshTicketList = function () {
        t.currentPage = 1;
        t.loadTicketList();
    };

    t.ticketSearchInput.on("keydown", function (e) {
        if (e.key === "Enter") {
            let searchTerm = t.ticketSearchInput.val().trim();
            t.searchTickets(searchTerm);
        }

    });
    // Optional: Add search with pagination reset
    t.searchTickets = function (searchTerm) {
        t.config.search = searchTerm;
        t.currentPage = 1; // Reset to first page on new search
        t.loadTicketList();
    };

    t.initTooltips = function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    t.truncateText = function (text, maxLength) {
        if (!text) return 'Unassigned';
        if (text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    // Function to get created via details
    t.getCreatedViaInfo = function (createdVia) {
        const viaMap = {
            1: {
                text: 'Portal',
                icon:`<svg width="18" class="opacity-30" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g><path d="M9 20H12M12 20H15M12 20V17M12 17H19C19.5304 17 20.0391 16.7893 20.4142 16.4142C20.7893 16.0391 21 15.5304 21 15V6C21 5.46957 20.7893 4.96086 20.4142 4.58579C20.0391 4.21071 19.5304 4 19 4H5C4.46957 4 3.96086 4.21071 3.58579 4.58579C3.21071 4.96086 3 5.46957 3 6V15C3 15.5304 3.21071 16.0391 3.58579 16.4142C3.96086 16.7893 4.46957 17 5 17H12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></g></svg>`,
                class: 'portal'
            },
            2: {
                text: 'Chat',
                icon: `<svg width="18" height="18" class="opacity-30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 11.5C21.0034 12.8199 20.6951 14.1219 20.1 15.3C19.3944 16.7118 18.3098 17.8992 16.9674 18.7293C15.6251 19.5594 14.0782 19.9994 12.5 20C11.1801 20.0035 9.87812 19.6951 8.7 19.1L3 21L4.9 15.3C4.30493 14.1219 3.99656 12.8199 4 11.5C4.00061 9.92179 4.44061 8.37488 5.27072 7.03258C6.10083 5.69028 7.28824 4.6056 8.7 3.9C9.87812 3.30493 11.1801 2.99656 12.5 3H13C15.0843 3.11499 17.053 3.99476 18.5291 5.47089C20.0052 6.94701 20.885 8.91568 21 11V11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 10H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M8 14H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>`,
                class: 'chat'
            },
            3: {
                text: 'Email',
                icon: `<svg width="18" height="18" class="opacity-30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 18L8 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M22 18L16 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>`,
                class: 'email'
            },
            4: {
                text: 'Mobile',
                icon: `<svg width="13" class="opacity-30" height="20" viewBox="0 0 13 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                       <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 0C1.37772 0 0.900806 0.197544 0.549175 0.549175C0.197544 0.900805 0 1.37772 0 1.875L0 18.125C0 18.6223 0.197544 19.0992 0.549175 19.4508C0.900806 19.8025 1.37772 20 1.875 20H10.625C11.1223 20 11.5992 19.8025 11.9508 19.4508C12.3025 19.0992 12.5 18.6223 12.5 18.125V1.875C12.5 1.37772 12.3025 0.900805 11.9508 0.549175C11.5992 0.197544 11.1223 0 10.625 0L1.875 0ZM1.25 1.875C1.25 1.70924 1.31585 1.55027 1.43306 1.43306C1.55027 1.31585 1.70924 1.25 1.875 1.25H10.625C10.7908 1.25 10.9497 1.31585 11.0669 1.43306C11.1842 1.55027 11.25 1.70924 11.25 1.875V5H1.25V1.875ZM1.25 16.25V18.125C1.25 18.2908 1.31585 18.4497 1.43306 18.5669C1.55027 18.6842 1.70924 18.75 1.875 18.75H10.625C10.7908 18.75 10.9497 18.6842 11.0669 18.5669C11.1842 18.4497 11.25 18.2908 11.25 18.125V16.25H1.25ZM1.25 15H11.25V6.25H1.25V15Z" fill="currentColor"/>
                    </svg>`,
                class: 'mobile'
            },
            5: {
                text: 'Call',
                icon: `<svg width="18" height="18" class="opacity-30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22 16.92V20C22 20.5304 21.7893 21.0391 21.4142 21.4142C21.0391 21.7893 20.5304 22 20 22C10.6113 21.9932 2.00679 13.3887 2 4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H7.08C7.47483 1.99969 7.85782 2.13431 8.16213 2.37828C8.46644 2.62224 8.67074 2.95797 8.737 3.33L9.292 6.73C9.35155 7.06061 9.30857 7.40157 9.16809 7.70698C9.02761 8.01239 8.79682 8.26374 8.508 8.421L6.64 9.488C7.78958 11.9023 9.62617 13.9326 11.928 15.288L13.171 13.386C13.3412 13.1074 13.5996 12.8932 13.9072 12.7795C14.2148 12.6658 14.5514 12.6591 14.863 12.76L18.333 13.809C18.7135 13.9197 19.0431 14.1552 19.2662 14.4742C19.4893 14.7933 19.5913 15.1756 19.553 15.555L19.026 18.946C18.9533 19.3064 18.7598 19.6299 18.4822 19.8628C18.2047 20.0958 17.8609 20.2235 17.504 20.224H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`,
                class: 'call'
            },
            6: {
                text: 'BOT',
                icon: `<svg width="18" height="18" class="opacity-30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="9" cy="9" r="1" fill="currentColor"/>
                    <circle cx="15" cy="9" r="1" fill="currentColor"/>
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2Z" fill="currentColor"/>
                    <path d="M19 8H16.5C16.5 8 16.49 8 16.48 8H7.52C7.51 8 7.5 8 7.5 8H5C3.9 8 3 8.9 3 10V16C3 17.1 3.9 18 5 18H6.5L5.5 21H7.5L8.5 18H15.5L16.5 21H18.5L17.5 18H19C20.1 18 21 17.1 21 16V10C21 8.9 20.1 8 19 8ZM6 15C5.45 15 5 14.55 5 14C5 13.45 5.45 13 6 13C6.55 13 7 13.45 7 14C7 14.55 6.55 15 6 15ZM8 12C7.45 12 7 11.55 7 11C7 10.45 7.45 10 8 10C8.55 10 9 10.45 9 11C9 11.55 8.55 12 8 12ZM12 15C11.45 15 11 14.55 11 14C11 13.45 11.45 13 12 13C12.55 13 13 13.45 13 14C13 14.55 12.55 15 12 15ZM16 12C15.45 12 15 11.55 15 11C15 10.45 15.45 10 16 10C16.55 10 17 10.45 17 11C17 11.55 16.55 12 16 12ZM18 15C17.45 15 17 14.55 17 14C17 13.45 17.45 13 18 13C18.55 13 19 13.45 19 14C19 14.55 18.55 15 18 15Z" fill="currentColor"/>
                </svg>`,
                class: 'bot'
            },
            default: {
                text: 'Portal',
                icon:`<svg width="18" class="opacity-30" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g><path d="M9 20H12M12 20H15M12 20V17M12 17H19C19.5304 17 20.0391 16.7893 20.4142 16.4142C20.7893 16.0391 21 15.5304 21 15V6C21 5.46957 20.7893 4.96086 20.4142 4.58579C20.0391 4.21071 19.5304 4 19 4H5C4.46957 4 3.96086 4.21071 3.58579 4.58579C3.21071 4.96086 3 5.46957 3 6V15C3 15.5304 3.21071 16.0391 3.58579 16.4142C3.96086 16.7893 4.46957 17 5 17H12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></g></svg>`,
                class: 'portal'
            }
        };

        return viaMap[createdVia] || viaMap.default;
    };

    // Helper function to check dark mode
    t.isDarkMode = function () {
        return document.documentElement.getAttribute('data-bs-theme') === 'dark' ||
            $('body').attr('data-bs-theme') === 'dark' ||
            $('html').attr('data-bs-theme') === 'dark';
    }

    // Function to lighten color for background
    t.getThemeBasedColor = function (color, percent) {
        if (!color) {
            // Return default based on theme
            return t.isDarkMode() ? '#272727' : '#f3f4f6';
        }

        var hex = color.replace('#', '');
        var r = parseInt(hex.substring(0, 2), 16);
        var g = parseInt(hex.substring(2, 4), 16);
        var b = parseInt(hex.substring(4, 6), 16);

        if (t.isDarkMode()) {
            // Dark mode: Darken the color
            return '#252525';
        } else {
            // Light mode: Lighten the color
            var lightenPercent = percent || 90;
            r = Math.min(255, Math.floor(r + (255 - r) * lightenPercent / 100));
            g = Math.min(255, Math.floor(g + (255 - g) * lightenPercent / 100));
            b = Math.min(255, Math.floor(b + (255 - b) * lightenPercent / 100));
        }

        return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    // feedback rendering 
    t.getFeedbackDisplay=function (feedback, basePath) {
        basePath = basePath+'/images/emo/';

        const map = {
            1: { img: '1.gif', label: 'Very Poor' },
            2: { img: '2.gif', label: 'Poor' },
            3: { img: '3.gif', label: 'Average' },
            4: { img: '4.gif', label: 'Good' },
            5: { img: '5.gif', label: 'Excellent' }
        };

        if (!feedback || feedback < 1 || feedback > 5) {
            return `
                <span class="d-inline-flex align-items-center gap-1">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.75 0C7.82164 0 5.93657 0.571828 4.33319 1.64317C2.72982 2.71451 1.48013 4.23726 0.742179 6.01884C0.00422452 7.80042 -0.188858 9.76082 0.187348 11.6521C0.563554 13.5434 1.49215 15.2807 2.85571 16.6443C4.21928 18.0079 5.95656 18.9365 7.84787 19.3127C9.73919 19.6889 11.6996 19.4958 13.4812 18.7578C15.2627 18.0199 16.7855 16.7702 17.8568 15.1668C18.9282 13.5634 19.5 11.6784 19.5 9.75C19.4973 7.16498 18.4692 4.68661 16.6413 2.85872C14.8134 1.03084 12.335 0.00272983 9.75 0ZM9.75 18C8.11831 18 6.52326 17.5161 5.16655 16.6096C3.80984 15.7031 2.75242 14.4146 2.128 12.9071C1.50358 11.3996 1.3402 9.74085 1.65853 8.1405C1.97685 6.54016 2.76259 5.07015 3.91637 3.91637C5.07016 2.76259 6.54017 1.97685 8.14051 1.65852C9.74085 1.34019 11.3997 1.50357 12.9071 2.12799C14.4146 2.75242 15.7031 3.80984 16.6096 5.16655C17.5161 6.52325 18 8.1183 18 9.75C17.9975 11.9373 17.1275 14.0343 15.5809 15.5809C14.0343 17.1275 11.9373 17.9975 9.75 18ZM5.25 7.875C5.25 7.6525 5.31598 7.43499 5.4396 7.24998C5.56322 7.06498 5.73892 6.92078 5.94449 6.83564C6.15005 6.75049 6.37625 6.72821 6.59448 6.77162C6.81271 6.81502 7.01317 6.92217 7.1705 7.0795C7.32783 7.23684 7.43498 7.43729 7.47839 7.65552C7.5218 7.87375 7.49952 8.09995 7.41437 8.30552C7.32922 8.51109 7.18503 8.68679 7.00002 8.8104C6.81502 8.93402 6.59751 9 6.375 9C6.07664 9 5.79049 8.88147 5.57951 8.6705C5.36853 8.45952 5.25 8.17337 5.25 7.875ZM14.25 7.875C14.25 8.0975 14.184 8.31501 14.0604 8.50002C13.9368 8.68502 13.7611 8.82922 13.5555 8.91436C13.35 8.99951 13.1238 9.02179 12.9055 8.97838C12.6873 8.93498 12.4868 8.82783 12.3295 8.6705C12.1722 8.51316 12.065 8.31271 12.0216 8.09448C11.9782 7.87625 12.0005 7.65005 12.0856 7.44448C12.1708 7.23891 12.315 7.06321 12.5 6.9396C12.685 6.81598 12.9025 6.75 13.125 6.75C13.4234 6.75 13.7095 6.86853 13.9205 7.0795C14.1315 7.29048 14.25 7.57663 14.25 7.875ZM14.1497 12.375C13.185 14.0428 11.5809 15 9.75 15C7.91907 15 6.31594 14.0437 5.35125 12.375C5.29699 12.2896 5.26055 12.1942 5.24413 12.0944C5.22772 11.9946 5.23166 11.8925 5.25573 11.7942C5.27979 11.696 5.32348 11.6036 5.38417 11.5227C5.44485 11.4417 5.52128 11.3739 5.60886 11.3233C5.69643 11.2727 5.79334 11.2403 5.89375 11.2281C5.99417 11.2159 6.09601 11.2242 6.19316 11.2523C6.2903 11.2805 6.38074 11.3281 6.45904 11.3921C6.53734 11.4562 6.60187 11.5354 6.64875 11.625C7.34907 12.8353 8.44969 13.5 9.75 13.5C11.0503 13.5 12.1509 12.8344 12.8503 11.625C12.9498 11.4527 13.1136 11.327 13.3058 11.2754C13.4979 11.2239 13.7027 11.2509 13.875 11.3503C14.0473 11.4498 14.1731 11.6136 14.2246 11.8058C14.2761 11.9979 14.2491 12.2027 14.1497 12.375Z" fill="#B3B3B3"/>
                    </svg>
                    <span class="b5-text opacity-70 tkt-text-black">Not Received</span>
                </span>
            `;
        }
        const item = map[feedback];
        return `
            <span class="d-inline-flex align-items-center gap-1">
                <img src="${basePath}${item.img}" alt="Feedback ${feedback}" style="width:18px;height:18px;border-radius:50%;">
                <span class="b5-text opacity-70 tkt-text-black">${item.label}</span>
            </span>
        `;
    }

    t.generateTicketCard = function (ticket) {

        var fullNameC = ticket.creator_name ?? '';
        var fullNameA = ticket.assigned_to_name ?? '';

        function getAvatar(ticket) {
            if (ticket.creator_avatar && ticket.creator_avatar !== '' && ticket.creator_avatar !== null) {
                return `<img src="${t.config.url.base_url}/storage/avatar/${ticket.creator_avatar}" style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">`;
            } else {
                var name = ticket.creator_name || 'Unassigned';
                var initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 1);
                return `<span class="tkt-avatar-sm" style="background:#E4570C;">${initials}</span>`;
            }
        }

        function getAssignedAvatar(ticket) {
            if (ticket.assigned_avatar && ticket.assigned_avatar !== '' && ticket.assigned_avatar !== null) {
                return `<img src="${t.config.url.base_url}/storage/avatar/${ticket.assigned_avatar}" style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">`;
            } else {
                var name = ticket.assigned_to_name || 'Unassigned';
                var initials = name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 1);
                return `<span class="tkt-avatar-sm">${initials}</span>`;
            }
        }

        var createdViaInfo = t.getCreatedViaInfo(ticket.created_via);

        var priorityColor = {
            'Critical': '#dc2626',
            'High': '#f97316',
            'Medium': '#eab308',
            'Low': '#10b981'
        }[ticket.priority] || '#7c3aed';

        // Truncate subject if too long
        var subjectShort = ticket.subject.length > 30 ? ticket.subject.substring(0, 30) + '...' : ticket.subject;
        var statusText = ticket.status;
        var statusColor = ticket.status_color_code;
        var statusBgColor = t.getThemeBasedColor(statusColor, 90);
        var tktInfoBar = t.getThemeBasedColor(ticket.color_code, 95);
        const controls = t.config.user.action_controls[ticket.company_id] || {};
        var is_merged_sec = ticket.is_merge_primary != 1 && (ticket.merge_primary != null && ticket.merge_primary != "null" && ticket.merge_primary != "") ? true : false;
        t.companyId = ticket.company_id;
        let mergeData = {
            id: ticket.id,
            subject: ticket.subject,
            status: ticket.status,
            status_id: ticket.status_id,
            status_color_code: statusColor,
            statusBgColor: statusBgColor,
            department_id: ticket.department_id,
            company_id: ticket.company_id,
            department: ticket.dep_name,
            company: ticket.comp_name
        };
        const radius = 15;
        const circumference = 2 * Math.PI * radius;
        const progress = (ticket.completionPercentage / 100) * circumference;
        const offset = circumference - progress;
        return `
            <div class="tkt-card-outer color-purple">
                <div class="tkt-id-col" style="background:${ticket.color_code};">
                    <label style="cursor:pointer;">
                        <input type="checkbox" class="form-check-input ticket-check-boxes" value="${ticket.id}" ticket-checkbox" data-ticket='${JSON.stringify(mergeData)}'>
                        <span class="tkt-chk-mark"></span>
                    </label>
                    <a href="${config.url.view_ticket}/${ticket.id}?b=${t.config.main_filter}" class="ticket-detail-link" target="_blank">
                        <span class="tkt-id-text">${ticket.ticket_tag ? ticket.ticket_tag : `#${ticket.id}`}</span>
                    </a>
                </div>
                <div class="tkt-body">
                    <div class="px-3 py-2">
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                            <a  href="${config.url.view_ticket}/${ticket.id}?=${t.config.main_filter}" target="_blank" class="ticket-detail-link">
                                <h4 class="tkt-title mb-0" data-bs-toggle="tooltip" title="${t.escapeHtml(ticket.subject)}">
                                    ${t.escapeHtml(subjectShort)}
                                </h4>
                            </a>

                            ${ticket.is_merge_primary == 1 ? `
                                <span class="label-merged b5-text">
                                    Merged Primary
                                </span>
                            ` : ''}
                            ${is_merged_sec ? `
                                <span class="label-merged b5-text">
                                    Merged -
                                    <a href="${config.url.view_ticket}/${ticket.id}?b=${t.config.main_filter}" target="_blank">
                                        #${ticket.merge_primary}
                                    </a>
                                </span>
                            ` : ''}
                            ${ticket.is_vip_user ? `
                                <span class="tkt-vip-badge">
                                    <svg width="36" height="20" viewBox="0 0 46 25" fill="none">
                                        <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" fill="#F6EEFF" />
                                        <rect x="0.5" y="0.5" width="45" height="24" rx="3.5" stroke="url(#vip1)" />
                                        <path d="M14.0229 7.31818L16.8567 15.6108H16.9711L19.8049 7.31818H21.4654L17.8013 17.5H16.0264L12.3624 7.31818H14.0229ZM24.1919 7.31818V17.5H22.6557V7.31818H24.1919ZM26.1492 17.5V7.31818H29.7784C30.5706 7.31818 31.2268 7.46236 31.7472 7.75071C32.2675 8.03906 32.657 8.43347 32.9155 8.93395C33.174 9.43111 33.3033 9.99124 33.3033 10.6143C33.3033 11.2408 33.1724 11.8042 32.9105 12.3047C32.652 12.8018 32.2609 13.1963 31.7372 13.4879C31.2169 13.7763 30.5623 13.9205 29.7734 13.9205H27.2777V12.6179H29.6342C30.1347 12.6179 30.5407 12.5317 30.8523 12.3594C31.1638 12.1837 31.3925 11.9451 31.5384 11.6435C31.6842 11.3419 31.7571 10.9988 31.7571 10.6143C31.7571 10.2299 31.6842 9.88849 31.5384 9.5902C31.3925 9.2919 31.1622 9.05824 30.8473 8.8892C30.5358 8.72017 30.1248 8.63565 29.6144 8.63565H27.6854V17.5H26.1492Z" fill="#EB0964" />
                                        <defs>
                                            <linearGradient id="vip1" x1="23" y1="0" x2="23" y2="25" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#4102FF" />
                                                <stop offset="1" stop-color="#FB0956" />
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </span>
                            ` : ''}
                            <span class="${ticket.article_ticket != null ? 'text-warning' : ''}">
                                <svg width="13" height="15" viewBox="0 0 17 20" fill="none">
                                    <path d="M15.75 0L3 0C0.87868 0.87868 0 3 0 3L0 18.75C0 19.5 0.75 19.5 0.75 19.5H14.25C15 19.5 15 18.75 15 18H1.5C1.5 17.6022 3 16.5 3 16.5H15.75C16.5 16.5 16.5 15.75 16.5 15.75V0.75C16.5 0 15.75 0 15.75 0ZM15 15H3C1.5 15.4022 1.5 15.4022 1.5 3V3C1.5 1.5 3 1.5 3 1.5H15V15Z" fill="#9ca3af" />
                                </svg>
                                <span class="tkt-sla-label ${ticket.article_ticket != null ? 'text-warning' : ''}">KB</span>
                            </span>
                            ${ticket && ticket.pr_status == "3" ? `
                                <a  href="${t.config.url.requestInfo}/${ticket.service_request.id}?b=${t.config.main_filter}" target="_blank" class="ticket-detail-link">
                                    <h4 class="tkt-title mb-0">
                                        (${ticket.procure_tag})
                                    </h4>
                                </a>`
                                : ""
                            }
                            ${t.config.taskModule === 1 &&
                                $.inArray("TaskRead", t.config.permissions) !== -1
                                ? (ticket.taskCount > 0 ? `
                                    <div style="position: relative; width: 36px; height: 36px; cursor:pointer;"
                                        data-bs-toggle="tooltip"
                                        title="Completed Task: ${ticket.completionPercentage}%">

                                        <svg width="36" height="36" viewBox="0 0 36 36">
                                            <!-- Background -->
                                            <circle
                                                cx="18"
                                                cy="18"
                                                r="15"
                                                fill="none"
                                                stroke="#e0e0e0"
                                                stroke-width="3"/>

                                            <!-- Progress -->
                                            <circle
                                                cx="18"
                                                cy="18"
                                                r="15"
                                                fill="none"
                                                stroke="#5bbe75"
                                                stroke-width="3"
                                                stroke-linecap="round"
                                                stroke-dasharray="${2 * Math.PI * 15}"
                                                stroke-dashoffset="${2 * Math.PI * 15 - (ticket.completionPercentage / 100) * (2 * Math.PI * 15)}"
                                                transform="rotate(-90 18 18)"
                                                style="transition: stroke-dashoffset .5s ease;" />
                                        </svg>

                                        <span style="
                                            position:absolute;
                                            top:50%;
                                            left:50%;
                                            transform:translate(-50%,-50%);
                                            font-size:8px;
                                            font-weight:bold;">
                                            ${ticket.completionPercentage}%
                                        </span>
                                    </div>
                                ` : '')
                                : ''
                            }
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1">
                                <span class="tkt-avatar-sm me-1">${getAvatar(ticket)}</span>
                                <span class="b5-text opacity-80 tkt-text-black" 
                                    data-bs-toggle="tooltip" 
                                    title="${t.config.translations.creator}">
                                    ${t.truncateText(t.escapeHtml(fullNameC), 20)}
                                </span>
                            </div>

                            <span class="tkc-info-divider ms-1 me-1">
                                <svg width="1" height="20" viewBox="0 0 1 31" fill="none">
                                    <path d="M0.5 0L0.499999 30.2148" stroke="currentColor"/>
                                </svg>
                            </span>

                            <span class="b5-text opacity-80 tkt-text-black" 
                                data-bs-toggle="tooltip" 
                                title="${t.config.translations.department}">
                                ${t.truncateText(t.escapeHtml(ticket.dep_name || 'N/A'), 20)}
                            </span>

                            ${t.config.taskModule === 1 && $.inArray("TaskRead", t.config.permissions) !== -1 ? `
                                <span class="tkc-info-divider ms-1 me-1">
                                    <svg width="1" height="20" viewBox="0 0 1 31" fill="none">
                                        <path d="M0.5 0L0.499999 30.2148" stroke="currentColor"/>
                                    </svg>
                                </span>
                                <span class="b5-text tkt-text-black">
                                    <span data-ticket-id="${ticket.id}" class="task-link" style="cursor:pointer;">
                                        <span class="fw-bold b5-text tkt-text-black">${ticket.taskCount || 0}</span>
                                        <span class="opacity-80 b5-text tkt-text-black">Tasks</span>
                                    </span>
                                    ${(ticket.status_id !== 5 && ticket.status_id !== 6 &&
                                    ticket.assigned_to == t.config.user.id &&
                                    $.inArray("TaskAdd", t.config.permissions) !== -1)
                                        ? `&nbsp;<i class="bi bi-plus-circle-fill addtask list_icon"
                                                style="cursor:pointer;"
                                                data-bs-toggle="tooltip" title="Add Task"
                                                data-id="${ticket.id}"
                                                data-assigned-to-name="${ticket.assigned_to_name || ''}"
                                                data-assigned-to-id="${ticket.assigned_to}"
                                                data-creator="${ticket.creator_id}">
                                            </i>`
                                        : ''}
                                </span>
                            ` : ''}

                            <span class="tkc-info-divider ms-1 me-1">
                                <svg width="1" height="20" viewBox="0 0 1 31" fill="none">
                                    <path d="M0.5 0L0.499999 30.2148" stroke="currentColor"/>
                                </svg>
                            </span>

                            <span>
                                <span class="" data-bs-toggle="tooltip" title= "${t.config.translations.tat}"><span class="b5-text tkt-text-black fw-bold">${ticket.tat || '0'}</span></span><span class="b5-text tkt-text-black opacity-80" >&nbsp;Hrs</span></span>
                            </span>

                            <span class="tkc-info-divider ms-1 me-1">
                                <svg width="1" height="20" viewBox="0 0 1 31" fill="none">
                                    <path d="M0.5 0L0.499999 30.2148" stroke="currentColor"/>
                                </svg>
                            </span>

                            <!-- "More info" trigger — shows Status, Created Via, Location on hover -->
                            <div class="tkc-info-divider ms-1 me-1"data-bs-toggle="tooltip" title="${t.config.translations.status}">
                                <span class="tkt-status-badge ms-2" data-color-code="${statusColor}">
                                    ${t.escapeHtml(ticket.status)}
                                </span>
                            </div>

                             <span class="tkc-info-divider ms-1 me-1">
                                <svg width="1" height="20" viewBox="0 0 1 31" fill="none">
                                    <path d="M0.5 0L0.499999 30.2148" stroke="currentColor"/>
                                </svg>
                            </span>

                            <span class="tkt-more-info-trigger position-relative" style="cursor:default;">
                                <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="opacity:0.4;vertical-align:middle;">
                                    <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M8 7v5M8 5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>

                                <div class="tkt-more-info-popover">
                                    <div class="tkt-more-info-row">
                                        <span class="tkt-more-info-label">${t.config.translations.via}</span>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            ${createdViaInfo.icon}
                                            <span class="b5-text opacity-80 tkt-text-black">${createdViaInfo.text}</span>
                                        </span>
                                    </div>
                                    <div class="tkt-more-info-row">
                                        <span class="tkt-more-info-label">${t.config.translations.location}</span>
                                        <span class="b5-text opacity-80  tkt-text-black">
                                            ${t.escapeHtml(ticket.location_name || 'N/A')}
                                        </span>
                                    </div>
                                    <div class="tkt-more-info-row">
                                        <span class="tkt-more-info-label">${t.config.translations.company}</span>
                                        <span class="b5-text opacity-80  tkt-text-black">
                                            ${t.escapeHtml(ticket.comp_name || 'N/A')}
                                        </span>
                                    </div>
                                </div>
                            </span>
                        </div>
                    </div>
                    <div class="tkt-info-bar" data-color-code="${ticket.color_code}"  style="background:${tktInfoBar}">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="${t.config.translations.priority}">
                                <span class="tkt-priority-dot" style="background:${priorityColor};"></span>
                                    <span class="b5-text opacity-70 tkt-text-black">${t.escapeHtml(ticket.priority)}</span>
                            </span>
                                <span class="d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="${t.config.translations.ticket_type}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M2.5 12L12 3L21.5 12H15.5V21H8.5V12H2.5Z" fill="#FE19BD" stroke="#FE19BD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                    <span class="b5-text opacity-70 tkt-text-black">${t.escapeHtml(ticket.ticket_type == 0 ? 'Normal' : t.truncateText(ticket.tkt_type_name, 15))}</span>
                            </span>
                                <span class="d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="${t.config.translations.created_at}">
                                    <svg width="18" class="opacity-30" height="18" viewBox="0 0 24 24" fill="none">
                                    <g>
                                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11 8V13H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                </svg>
                                    <span class="b5-text opacity-70 tkt-text-black">${ticket.created_at_format}</span>
                            </span>
                                <span class="d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="${t.config.translations.updated_at}">
                                    <svg width="18" class="opacity-30" height="18" viewBox="0 0 24 24" fill="none">
                                    <g>
                                        <path d="M12 21C16.9706 21 21 16.9706 21 12C21 7.02944 16.9706 3 12 3C7.02944 3 3 7.02944 3 12C3 16.9706 7.02944 21 12 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M11 8V13H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                </svg>
                                    <span class="b5-text opacity-70 tkt-text-black">${ticket.updated_at_format}</span>
                            </span>
                            <span class="d-inline-flex align-items-center gap-1" data-bs-toggle="tooltip" title="User Feedback">
                                <span class="b5-text opacity-70 tkt-text-black">${t.getFeedbackDisplay(ticket.feedback, t.config.url.base_url)}</span>
                            </span>
                            </div>
                    </div>
                </div>
                <div class="tkt-right-panel">
                    <div class="tkt-right-top d-flex space-between flex-fill">
                        <div class="px-3 py-2 flex-fill">
                            <div class="row align-items-center g-2 my-1">
                                <div class="col-5 m-0 m-0">
                                    <div>
                                        <span class="tkt-text-black b5-text">Assigned to</span>
                                        <span style="color:#E20505" class="fw-bold b5-text" >
                                            (${ticket.total_assigned || 0})
                                        </span>
                                </div>
                                </div>
                                <div class="col-7 m-0 d-flex align-items-center gap-1 m-0">
                                    <span class="tkt-avatar-sm">
                                        ${ (ticket.assigned_to == null && (ticket.status_id == 5 || ticket.status_id == 6))
                                            ? `<img src="${t.config.url.base_url}/imgs/profile-40.jpg" style="width: 18px; height: 18px; border-radius: 50%; object-fit: cover;">`
                                            : getAssignedAvatar(ticket)
                                        }
                                    </span>

                                    <span
                                        class="b5-text opacity-80"
                                        data-bs-toggle="tooltip"
                                        title="${t.config.translations.assign_to}: ${
                                            (ticket.assigned_to == null && (ticket.status_id == 5 || ticket.status_id == 6))
                                                ? 'Resolved by System'
                                                : t.escapeHtml(fullNameA)
                                        }"
                                    >
                                        ${ (ticket.assigned_to == null && (ticket.status_id == 5 || ticket.status_id == 6))
                                            ? 'Resolved by System'
                                            : t.truncateText(t.escapeHtml(fullNameA), 15)
                                        }
                                    </span>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-5 m-0">
                                    <span class="b5-text fw-normal tkt-text-black">
                                        Since
                                    </span>
                                </div>

                                <div class="col-6 px-1">
                                    <div class="d-flex align-items-center gap-2"
                                        data-bs-toggle="tooltip"
                                        title="${t.config.translations.assign_at}">

                                         <svg width="18" class="opacity-30" height="18" viewBox="0 0 24 24" fill="none">
                                        <g><path d="M19.4972 3.00044H17.2476V2.25022C17.2476 2.05125 17.1686 1.86043 17.028 1.71973C16.8873 1.57904 16.6966 1.5 16.4977 1.5C16.2988 1.5 16.1081 1.57904 15.9675 1.71973C15.8269 1.86043 15.7478 2.05125 15.7478 2.25022V3.00044H8.24911V2.25022C8.24911 2.05125 8.17011 1.86043 8.02948 1.71973C7.88885 1.57904 7.69812 1.5 7.49924 1.5C7.30036 1.5 7.10963 1.57904 6.969 1.71973C6.82837 1.86043 6.74937 2.05125 6.74937 2.25022V3.00044H4.49975C4.10199 3.00044 3.72052 3.15852 3.43927 3.43991C3.15801 3.7213 3 4.10294 3 4.50088V19.5053C3 19.9032 3.15801 20.2849 3.43927 20.5663C3.72052 20.8477 4.10199 21.0057 4.49975 21.0057H19.4972C19.895 21.0057 20.2764 20.8477 20.5577 20.5663C20.839 20.2849 20.997 19.9032 20.997 19.5053V4.50088C20.997 4.10294 20.839 3.7213 20.5577 3.43991C20.2764 3.15852 19.895 3.00044 19.4972 3.00044ZM6.74937 4.50088V5.2511C6.74937 5.45007 6.82837 5.6409 6.969 5.78159C7.10963 5.92228 7.30036 6.00132 7.49924 6.00132C7.69812 6.00132 7.88885 5.92228 8.02948 5.78159C8.17011 5.6409 8.24911 5.45007 8.24911 5.2511V4.50088H15.7478V5.2511C15.7478 5.45007 15.8269 5.6409 15.9675 5.78159C16.1081 5.92228 16.2988 6.00132 16.4977 6.00132C16.6966 6.00132 16.8873 5.92228 17.028 5.78159C17.1686 5.6409 17.2476 5.45007 17.2476 5.2511V4.50088H19.4972V7.50177H4.49975V4.50088H6.74937ZM19.4972 19.5053H4.49975V9.00221H19.4972V19.5053ZM13.4982 14.2538C13.4982 14.5505 13.4103 14.8406 13.2455 15.0874C13.0807 15.3341 12.8465 15.5264 12.5724 15.64C12.2984 15.7535 11.9968 15.7833 11.7059 15.7254C11.415 15.6675 11.1477 15.5246 10.938 15.3147C10.7283 15.1049 10.5854 14.8375 10.5276 14.5465C10.4697 14.2554 10.4994 13.9537 10.6129 13.6796C10.7264 13.4054 10.9186 13.1711 11.1653 13.0062C11.4119 12.8413 11.7019 12.7533 11.9985 12.7533C12.3962 12.7533 12.7777 12.9114 13.059 13.1928C13.3402 13.4742 13.4982 13.8558 13.4982 14.2538Z" fill="currentColor" /></g>
                                    </svg>

                                        <span class="b5-text fw-normal opacity-80 tkt-text-black">
                                    ${ticket.assigned_since || 'N/A'}
                                </span>
                            </div>
                        </div>
                            </div>
                        </div>
                        <div class="tkt-right-actions" data-color-code="${ticket.color_code}" style="background:${tktInfoBar}">
                            ${t.config.user.limits && typeof controls.ctrl_transfer != "undefined" && controls.ctrl_transfer === 1 && ticket.status_id != 5 && ticket.status_id != 6 && ticket.spam != 1 && is_merged_sec == "" && ticket.access_privilege == true ? 
                                `<button class="tkt-icon-btn transfer-btn js-act-transfer" data-id="${ticket.id}" data-company="${ticket.company_id}" data-bs-toggle="tooltip" title="${t.config.translations.transfer}">
                                    <svg width="14" height="14" class="opacity-60" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13.5677 11.0675L11.0677 13.5675C10.9504 13.6848 10.7913 13.7507 10.6255 13.7507C10.4596 13.7507 10.3006 13.6848 10.1833 13.5675C10.066 13.4503 10.0001 13.2912 10.0001 13.1253C10.0001 12.9595 10.066 12.8004 10.1833 12.6832L11.6169 11.2503H0.625492C0.459731 11.2503 0.30076 11.1845 0.18355 11.0673C0.0663399 10.9501 0.000491738 10.7911 0.000491738 10.6253C0.000491738 10.4596 0.0663399 10.3006 0.18355 10.1834C0.30076 10.0662 0.459731 10.0003 0.625492 10.0003H11.6169L10.1833 8.56754C10.066 8.45026 10.0001 8.2912 10.0001 8.12535C10.0001 7.9595 10.066 7.80044 10.1833 7.68316C10.3006 7.56588 10.4596 7.5 10.6255 7.5C10.7913 7.5 10.9504 7.56588 11.0677 7.68316L13.5677 10.1832C13.6258 10.2412 13.6719 10.3101 13.7033 10.386C13.7348 10.4619 13.751 10.5432 13.751 10.6253C13.751 10.7075 13.7348 10.7888 13.7033 10.8647C13.6719 10.9406 13.6258 11.0095 13.5677 11.0675ZM2.6833 6.06754C2.80058 6.18481 2.95964 6.2507 3.12549 6.2507C3.29134 6.25069 3.4504 6.18481 3.56768 6.06754C3.68495 5.95026 3.75084 5.7912 3.75084 5.62535C3.75084 5.4595 3.68495 5.30044 3.56768 5.18316L2.13409 3.75035H13.1255C13.2913 3.75035 13.4502 3.6845 13.5674 3.56729C13.6846 3.45008 13.7505 3.29111 13.7505 3.12535C13.7505 2.95959 13.6846 2.80062 13.5674 2.68341C13.4502 2.5662 13.2913 2.50035 13.1255 2.50035H2.13409L3.56768 1.06753C3.68495 0.95026 3.75084 0.7912 3.75084 0.625347C3.75084 0.459495 3.68495 0.300435 3.56768 0.18316C3.4504 0.0658846 3.29134 2.47139e-09 3.12549 0C2.95964 -2.47139e-09 2.80058 0.0658846 2.6833 0.18316L0.183304 2.68316C0.125194 2.74121 0.0790947 2.81014 0.047642 2.88601C0.0161893 2.96188 0 3.04321 0 3.12535C0 3.20748 0.0161893 3.28881 0.047642 3.36469C0.0790947 3.44056 0.125194 3.50949 0.183304 3.56754L2.6833 6.06754Z" fill="currentColor"/>
                                    </svg>
                                </button>` 
                            : ''}
                            ${jQuery.inArray("EditServiceTicket", t.config.permissions) !== -1 && ticket.status_id != 5 && ticket.status_id != 6 && ticket.spam != 1 && is_merged_sec == "" && t.config.isTechnician == true && ticket.access_privilege == true ?
                                `<button class="tkt-icon-btn edit-btn editTicket" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.edit}">
                                    <svg width="16" class="opacity-60" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M15.2594 3.85684L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 9.99981C0.249834 10.1155 0.157407 10.2531 0.0945056 10.4048C0.0316038 10.5565 -0.000518312 10.7192 6.32418e-06 10.8834L6.32418e-06 14.3748C6.32418e-06 14.7063 0.131702 15.0243 0.366123 15.2587C0.600543 15.4931 0.918486 15.6248 1.25001 15.6248H14.375C14.5408 15.6248 14.6997 15.559 14.8169 15.4418C14.9342 15.3245 15 15.1656 15 14.9998C15 14.8341 14.9342 14.6751 14.8169 14.5579C14.6997 14.4407 14.5408 14.3748 14.375 14.3748H6.50938L15.2594 5.62481C15.3755 5.50873 15.4676 5.37092 15.5304 5.21925C15.5933 5.06757 15.6256 4.905 15.6256 4.74083C15.6256 4.57665 15.5933 4.41408 15.5304 4.26241C15.4676 4.11073 15.3755 3.97292 15.2594 3.85684ZM4.74141 14.3748H1.25001V10.8834L8.12501 4.0084L11.6164 7.49981L4.74141 14.3748ZM12.5 6.61622L9.00938 3.12481L10.8844 1.24981L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                                    </svg>
                                </button>`
                            : ''}
                        </div>
                    </div>

                    <div class="tkt-action-bar" data-color-code="${ticket.color_code}" style="background:${tktInfoBar}">
                        <div class="d-flex align-items-center gap-2">
                            ${ticket.article_ticket == null && is_merged_sec == "" && jQuery.inArray("ConvertTicketToKnowledgeDocument", t.config.permissions) !== -1 && (ticket.status_id == 6 || ticket.status_id == 5) && t.config.main_filter != "archived" && t.config.isTechnician == true ? (t.config.aiEnabled == 1 ? 
                                    `<button class="tkt-act-btn btn-article ai" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.convert_kd_by_ai}">
                                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                    <g clip-path="url(#clip0_3327_12125)">
                                        <path d="M10.0006 1.66797C10.2881 1.66797 10.5215 1.9013 10.5215 2.1888V2.91797H13.6465C14.1161 2.91797 14.5666 3.10454 14.8987 3.43663C15.2307 3.76873 15.4173 4.21915 15.4173 4.6888V8.08463C15.2211 7.97161 14.9979 7.914 14.7715 7.91797C14.6361 7.91797 14.5023 7.93963 14.3756 7.98172V4.6888C14.3756 4.49541 14.2988 4.30995 14.1621 4.1732C14.0253 4.03646 13.8399 3.95964 13.6465 3.95964H6.35482C6.16143 3.95964 5.97596 4.03646 5.83922 4.1732C5.70247 4.30995 5.62565 4.49541 5.62565 4.6888V8.23047C5.62565 8.63297 5.95232 8.95963 6.35482 8.95963H13.5215L13.2256 9.8638C13.2114 9.91059 13.1944 9.9565 13.1748 10.0013H6.35482C5.88516 10.0013 5.43474 9.81473 5.10265 9.48264C4.77055 9.15054 4.58398 8.70012 4.58398 8.23047V4.6888C4.58398 4.21915 4.77055 3.76873 5.10265 3.43663C5.43474 3.10454 5.88516 2.91797 6.35482 2.91797H9.47982V2.1888C9.47982 1.9013 9.71315 1.66797 10.0006 1.66797ZM10.5881 11.668H5.10482C4.63516 11.668 4.18474 11.8545 3.85265 12.1866C3.52055 12.5187 3.33398 12.9691 3.33398 13.4388V13.8763C3.33398 15.1905 3.97357 16.3296 5.14065 17.1201C6.29232 17.9001 7.94232 18.3346 10.0006 18.3346C12.059 18.3346 13.709 17.9001 14.8607 17.1201C15.0729 16.9762 15.2673 16.8213 15.444 16.6555H14.8548L14.7986 16.6567C14.5263 16.6568 14.2615 16.568 14.0444 16.4038C13.1202 16.9471 11.774 17.293 10.0006 17.293C8.07273 17.293 6.6494 16.8838 5.72482 16.2576C4.81565 15.6417 4.37565 14.8121 4.37565 13.8763V13.4388C4.37565 13.0363 4.70232 12.7096 5.10482 12.7096H10.484L10.4732 12.6763C10.4207 12.5095 10.4039 12.3336 10.4237 12.1599C10.4435 11.9863 10.4995 11.8187 10.5881 11.668ZM8.95898 6.45963C8.95898 6.59643 8.93204 6.73188 8.87969 6.85826C8.82734 6.98464 8.75061 7.09948 8.65389 7.1962C8.55716 7.29293 8.44233 7.36966 8.31595 7.42201C8.18956 7.47436 8.05411 7.5013 7.91732 7.5013C7.78052 7.5013 7.64507 7.47436 7.51869 7.42201C7.39231 7.36966 7.27748 7.29293 7.18075 7.1962C7.08402 7.09948 7.00729 6.98464 6.95494 6.85826C6.90259 6.73188 6.87565 6.59643 6.87565 6.45963C6.87565 6.18337 6.9854 5.91842 7.18075 5.72307C7.3761 5.52771 7.64105 5.41797 7.91732 5.41797C8.19358 5.41797 8.45854 5.52771 8.65389 5.72307C8.84924 5.91842 8.95898 6.18337 8.95898 6.45963ZM12.084 7.5013C12.3602 7.5013 12.6252 7.39155 12.8206 7.1962C13.0159 7.00085 13.1256 6.7359 13.1256 6.45963C13.1256 6.18337 13.0159 5.91842 12.8206 5.72307C12.6252 5.52771 12.3602 5.41797 12.084 5.41797C11.8077 5.41797 11.5428 5.52771 11.3474 5.72307C11.1521 5.91842 11.0423 6.18337 11.0423 6.45963C11.0423 6.7359 11.1521 7.00085 11.3474 7.1962C11.5428 7.39155 11.8077 7.5013 12.084 7.5013ZM13.6811 13.798C13.4101 13.4477 13.0411 13.1859 12.6211 13.0459L11.5365 12.6938C11.4532 12.6642 11.3811 12.6094 11.3301 12.5372C11.2791 12.4649 11.2518 12.3787 11.2518 12.2903C11.2518 12.2018 11.2791 12.1156 11.3301 12.0433C11.3811 11.9711 11.4532 11.9164 11.5365 11.8867L12.6206 11.5346C12.9418 11.4239 13.2334 11.2413 13.4733 11.0009C13.7132 10.7605 13.8952 10.4685 14.0052 10.1471L14.0144 10.1205L14.3665 9.03714C14.3957 8.9532 14.4503 8.88044 14.5227 8.82895C14.5951 8.77746 14.6818 8.7498 14.7706 8.7498C14.8595 8.7498 14.9462 8.77746 15.0186 8.82895C15.091 8.88044 15.1456 8.9532 15.1748 9.03714L15.5269 10.1205C15.6366 10.45 15.8217 10.7494 16.0674 10.9949C16.3131 11.2404 16.6127 11.4252 16.9423 11.5346L18.0265 11.8867L18.0481 11.8921C18.1315 11.9218 18.2035 11.9765 18.2545 12.0487C18.3055 12.121 18.3329 12.2072 18.3329 12.2957C18.3329 12.3841 18.3055 12.4704 18.2545 12.5426C18.2035 12.6149 18.1315 12.6696 18.0481 12.6992L16.964 13.0513C16.6343 13.1607 16.3348 13.3455 16.089 13.591C15.8433 13.8365 15.6583 14.1359 15.5486 14.4655L15.1961 15.5488L15.1856 15.5755C15.1605 15.6341 15.1225 15.6864 15.0745 15.7285C15.0266 15.7706 14.9698 15.8014 14.9083 15.8187C14.8469 15.836 14.7823 15.8394 14.7194 15.8285C14.6566 15.8176 14.5969 15.7928 14.5448 15.7559C14.4727 15.7047 14.4181 15.6326 14.3886 15.5492L14.0361 14.4655C13.9562 14.2245 13.8362 13.9989 13.6811 13.798ZM19.8377 16.6613L19.2636 16.4746C19.0892 16.4166 18.9308 16.3188 18.8008 16.1889C18.6709 16.059 18.573 15.9006 18.5148 15.7263L18.3281 15.1526C18.3126 15.1082 18.2837 15.0697 18.2454 15.0425C18.207 15.0153 18.1612 15.0007 18.1142 15.0007C18.0672 15.0007 18.0213 15.0153 17.983 15.0425C17.9447 15.0697 17.9157 15.1082 17.9002 15.1526L17.7136 15.7263C17.6566 15.8994 17.5603 16.057 17.4324 16.1868C17.3045 16.3166 17.1483 16.4151 16.9761 16.4746L16.4019 16.6613C16.358 16.6772 16.3201 16.7062 16.2933 16.7444C16.2664 16.7826 16.2521 16.8282 16.2521 16.8748C16.2521 16.9215 16.2664 16.9671 16.2933 17.0053C16.3201 17.0435 16.358 17.0725 16.4019 17.0884L16.9761 17.2751C17.0391 17.2959 17.1 17.322 17.1586 17.3534L17.1598 17.3596C17.4273 17.5025 17.6297 17.7427 17.7252 18.0305L17.9119 18.6042C17.9273 18.6468 17.955 18.6838 17.9915 18.7105C18.0281 18.7372 18.0717 18.7524 18.117 18.7541C18.1622 18.7558 18.2069 18.7439 18.2453 18.7201C18.2838 18.6962 18.3142 18.6614 18.3327 18.6201V18.6167L18.3394 18.6L18.5261 18.0263C18.5842 17.852 18.6822 17.6936 18.8123 17.5637C18.9423 17.4338 19.1008 17.336 19.2752 17.278L19.8494 17.0913C19.8933 17.0754 19.9312 17.0464 19.958 17.0082C19.9849 16.97 19.9992 16.9244 19.9992 16.8778C19.9992 16.8311 19.9849 16.7855 19.958 16.7473C19.9312 16.7091 19.8933 16.6801 19.8494 16.6642L19.8377 16.6613Z" fill="currentColor" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3327_12125">
                                            <rect width="20" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span class="b5-text">AI</span>
                                    </button>` : 
                                    `<button class="tkt-act-btn btn-article ai" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.convert_kd}">
                                        <svg width="16" height="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                           <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5 14.464C17.4988 14.5119 17.4941 14.5597 17.486 14.607C17.5071 14.7156 17.5039 14.8274 17.4767 14.9346C17.4495 15.0418 17.3989 15.1417 17.3285 15.227C17.2582 15.3123 17.1698 15.3811 17.0698 15.4283C16.9698 15.4755 16.8606 15.5 16.75 15.5H2.75C2.58585 15.5 2.4233 15.5323 2.27165 15.5951C2.11999 15.658 1.98219 15.75 1.86612 15.8661C1.75004 15.9822 1.65797 16.12 1.59515 16.2716C1.53233 16.4233 1.5 16.5858 1.5 16.75C1.5 16.9142 1.53233 17.0767 1.59515 17.2284C1.65797 17.38 1.75004 17.5178 1.86612 17.6339C1.98219 17.75 2.11999 17.842 2.27165 17.9049C2.4233 17.9677 2.58585 18 2.75 18H16.75C16.9489 18 17.1397 18.079 17.2803 18.2197C17.421 18.3603 17.5 18.5511 17.5 18.75C17.5 18.9489 17.421 19.1397 17.2803 19.2803C17.1397 19.421 16.9489 19.5 16.75 19.5H2.75C2.02065 19.5 1.32118 19.2103 0.805456 18.6945C0.289731 18.1788 0 17.4793 0 16.75V2.75C0 2.02065 0.289731 1.32118 0.805456 0.805456C1.32118 0.289731 2.02065 0 2.75 0H16.15C16.896 0 17.5 0.604 17.5 1.35V14.464ZM5.75 4C5.55109 4 5.36032 4.07902 5.21967 4.21967C5.07902 4.36032 5 4.55109 5 4.75C5 4.94891 5.07902 5.13968 5.21967 5.28033C5.36032 5.42098 5.55109 5.5 5.75 5.5H11.75C11.9489 5.5 12.1397 5.42098 12.2803 5.28033C12.421 5.13968 12.5 4.94891 12.5 4.75C12.5 4.55109 12.421 4.36032 12.2803 4.21967C12.1397 4.07902 11.9489 4 11.75 4H5.75Z" fill="currentColor"/>
                                        </svg>
                                        <span class="b5-text">KD</span>
                                    </button>`
                                ) : ''
                            }
                            ${ticket.role_name !== 'User' ? ` 
                                <button class="tkt-act-btn sos" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.sentiment_analysis}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <g clip-path="url(#clip0_3327_12136)">
                                        <path d="M7.31314 0.498376C6.64439 0.567126 6.09439 0.826501 5.70064 1.25775C5.61626 1.34838 5.42876 1.59525 5.28814 1.80463C5.06939 2.12025 4.96314 2.239 4.64751 2.52025C3.77564 3.29213 3.69439 3.39213 3.78814 3.58275C3.84439 3.7015 4.13189 3.9265 4.42876 4.08275L4.67876 4.21713L4.69751 4.59838C4.71626 5.01088 4.77251 5.34213 4.89439 5.71713C4.95064 5.89213 4.96939 6.02338 4.96939 6.23275C4.96939 6.76713 5.11001 7.40775 5.31314 7.81088C5.67564 8.53275 6.71001 9.389 7.54126 9.65463C7.84751 9.75463 8.30064 9.73588 8.61001 9.61088C9.43814 9.2765 10.3725 8.464 10.7194 7.77963C10.9288 7.364 11.0413 6.77963 11.0381 6.12338C11.0381 5.69525 11.0444 5.61088 11.1131 5.40463C11.2788 4.87963 11.3256 4.55775 11.3256 3.90463C11.3256 3.23588 11.3006 3.08275 11.0944 2.57338C10.8788 2.03588 10.4913 1.5265 10.0319 1.17963C9.61626 0.864001 8.97876 0.614001 8.37564 0.532751C8.02251 0.485876 7.57564 0.470251 7.31314 0.498376ZM8.56939 1.214C9.64439 1.45775 10.3631 2.14213 10.6256 3.17025C10.6944 3.439 10.7319 4.0015 10.6975 4.24838L10.6725 4.42025L10.5163 4.14213C10.2569 3.67963 9.82876 3.27338 9.47876 3.15775C9.31314 3.1015 9.22251 3.1265 9.05064 3.2765C8.49751 3.75463 7.55689 4.039 6.53501 4.03588C6.11314 4.03275 6.04126 4.039 5.93189 4.09525C5.78189 4.17025 5.55376 4.37338 5.43501 4.539L5.34751 4.65775L5.33189 4.52338C5.31939 4.44838 5.31626 4.28275 5.31939 4.1515C5.33189 3.8515 5.31001 3.8265 4.90064 3.62025C4.73189 3.53588 4.59439 3.45463 4.59439 3.44213C4.59439 3.42963 4.74439 3.28588 4.93189 3.12338C5.42251 2.69213 5.55376 2.54525 5.84126 2.1265C6.26939 1.50463 6.60689 1.26088 7.19439 1.15463C7.45376 1.10775 8.25376 1.14213 8.56939 1.214ZM9.65689 4.0515C9.89126 4.29525 10.1069 4.69213 10.2975 5.24838L10.4131 5.5765L10.3975 6.15463C10.3756 7.00463 10.2569 7.41713 9.90064 7.864C9.61001 8.22963 9.03501 8.69838 8.61001 8.914C8.40376 9.01713 8.14439 9.09213 7.99439 9.09213C7.49751 9.09213 6.49126 8.39838 6.01314 7.72963C5.75064 7.36088 5.61314 6.82025 5.61314 6.15463C5.61626 5.56088 5.76939 5.08588 6.04751 4.81088L6.18189 4.6765L6.71001 4.67025C7.29126 4.664 7.68189 4.6015 8.21939 4.42963C8.56314 4.32025 9.01626 4.10775 9.23814 3.9515C9.32251 3.89213 9.40376 3.84213 9.42251 3.84213C9.43814 3.84213 9.54439 3.93588 9.65689 4.0515Z" fill="currentColor" />
                                        <path d="M5.86562 9.13984C5.76562 9.18984 5.1125 9.71797 5.00937 9.83047C4.96562 9.88047 4.84375 9.93359 4.67812 9.98359C3.89687 10.2086 3.03125 10.6711 1.98125 11.4242C1.425 11.8242 1.0875 12.2523 0.903125 12.7898C0.775 13.1617 0.75 13.4367 0.75 14.3773C0.75 15.3336 0.7625 15.4055 0.95 15.4836C1.11562 15.5523 14.8844 15.5523 15.05 15.4836C15.25 15.4023 15.2562 15.3398 15.2437 14.2211C15.2312 13.343 15.2219 13.2055 15.1625 12.9961C14.975 12.3117 14.6344 11.8555 13.9219 11.3523C12.9406 10.6555 11.8969 10.1148 11.25 9.96797C11.1437 9.94297 11.0531 9.89922 11.0156 9.85547C10.8906 9.71172 10.1437 9.13047 10.0656 9.11484C10.0219 9.10547 9.94375 9.11797 9.89062 9.13984C9.84062 9.16172 9.39687 9.54609 8.90625 9.99609C8.41562 10.443 8.00937 10.8117 8 10.8117C7.99062 10.8117 7.57812 10.4367 7.07812 9.98047C6.58125 9.52109 6.14062 9.13672 6.1 9.12109C6 9.08359 5.96875 9.08672 5.86562 9.13984ZM6.86875 10.6492C7.21562 10.968 7.49687 11.2367 7.49375 11.2492C7.4875 11.2617 7.24062 11.4711 6.94687 11.7148C6.4875 12.0898 6.40312 12.1461 6.38125 12.0992C6.27187 11.8867 5.62187 10.1461 5.64375 10.1242C5.65937 10.1086 5.74062 10.043 5.82812 9.97422L5.98437 9.84922L6.10937 9.96172C6.17812 10.0242 6.51875 10.3336 6.86875 10.6492ZM10.3656 10.1836C10.35 10.2367 9.71875 11.8648 9.64062 12.0492L9.59687 12.1586L9.05625 11.7148C8.75937 11.4711 8.51562 11.2586 8.51562 11.2398C8.51562 11.2242 8.85312 10.9055 9.2625 10.5305L10.0125 9.84922L10.1969 9.99297C10.3125 10.0836 10.375 10.1555 10.3656 10.1836ZM11.5844 10.7523C12.0094 10.918 12.5906 11.2398 13.2031 11.6461C14.0969 12.2398 14.3844 12.5617 14.5312 13.143C14.5844 13.3492 14.5937 13.4898 14.5937 14.1305V14.8742H12.0375H9.47812L9.20625 14.1211C9.05625 13.7086 8.94062 13.3492 8.95 13.3273C8.95625 13.3055 9.05312 13.1742 9.15937 13.0367L9.35625 12.7867L9.49687 12.893C9.70937 13.0555 9.925 13.0242 10.0281 12.8148C10.0594 12.7523 10.2719 12.2148 10.5 11.6211L10.9156 10.5367L11.1281 10.5961C11.2437 10.6273 11.45 10.6992 11.5844 10.7523ZM5.10625 10.6086C5.38125 11.3242 5.95625 12.7867 5.98437 12.8461C6.07812 13.0242 6.30625 13.0461 6.50625 12.893L6.64375 12.7867L6.8375 13.0305C6.94375 13.1648 7.04062 13.2961 7.05 13.3242C7.05937 13.3523 6.94687 13.7117 6.79687 14.1242L6.52187 14.8742H3.96562H1.40625V14.1305C1.40625 13.4898 1.41562 13.3492 1.46875 13.143C1.60937 12.5898 1.90312 12.2461 2.67187 11.7305C3.50625 11.1711 4.1 10.8523 4.65937 10.6586C4.95937 10.5555 5.07812 10.5398 5.10625 10.6086ZM8.42187 12.018C8.64687 12.1992 8.83125 12.3586 8.8375 12.3742C8.84375 12.3898 8.74687 12.5305 8.62187 12.6836L8.39375 12.968H8.00312H7.60937L7.39375 12.6961C7.275 12.5492 7.17812 12.4117 7.175 12.3898C7.17187 12.3555 7.95625 11.6867 8 11.6867C8.00937 11.6867 8.2 11.8367 8.42187 12.018ZM8.55625 14.2086C8.68125 14.5492 8.78125 14.8367 8.78125 14.8492C8.78125 14.8648 8.43125 14.8742 8 14.8742C7.57187 14.8742 7.21875 14.8648 7.21875 14.8492C7.21875 14.8367 7.31875 14.5492 7.44375 14.2086L7.66875 13.593H8H8.33125L8.55625 14.2086Z" fill="currentColor" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3327_12136">
                                            <rect width="16" height="16" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span class="b5-text">SOS</span>
                            </button>`:""
                            }
                            <a class="tkt-icon-btn view-btn ticket-detail-link" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.view}" href="${config.url.view_ticket}/${ticket.id}?=${t.config.main_filter}" target="_blank">
                                <svg width="15" class="opacity-60" height="16" viewBox="0 0 20 21" fill="none">
                                    <g clip-path="url(#clip0_3327_12173)">
                                        <path d="M9.99911 7.50829C10.662 7.50829 11.2978 7.77199 11.7666 8.24138C12.2353 8.71077 12.4987 9.3474 12.4987 10.0112C12.4987 10.675 12.2353 11.3117 11.7666 11.781C11.2978 12.2504 10.662 12.5141 9.99911 12.5141C9.33618 12.5141 8.7004 12.2504 8.23164 11.781C7.76287 11.3117 7.49953 10.675 7.49953 10.0112C7.49953 9.3474 7.76287 8.71077 8.23164 8.24138C8.7004 7.77199 9.33618 7.50829 9.99911 7.50829ZM9.99911 3.75391C14.1651 3.75391 17.7228 6.3486 19.1642 10.0112C17.7228 13.6738 14.1651 16.2685 9.99911 16.2685C5.83314 16.2685 2.27541 13.6738 0.833984 10.0112C2.27541 6.3486 5.83314 3.75391 9.99911 3.75391ZM2.65034 10.0112C3.32378 11.3881 4.36948 12.5481 5.66857 13.3595C6.96766 14.1709 8.46803 14.601 9.99911 14.601C11.5302 14.601 13.0306 14.1709 14.3296 13.3595C15.6287 12.5481 16.6744 11.3881 17.3479 10.0112C16.6744 8.63435 15.6287 7.47429 14.3296 6.66292C13.0306 5.85155 11.5302 5.42142 9.99911 5.42142C8.46803 5.42142 6.96766 5.85155 5.66857 6.66292C4.36948 7.47429 3.32378 8.63435 2.65034 10.0112Z" fill="currentColor" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_3327_12173">
                                            <rect width="19.9966" height="20.0234" fill="currentColor" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </a>
                            ${ticket.status_id != 6 && ticket.spam != 1 && ticket.status_id != 10 && ticket.assigned_to == t.config.auth.id && is_merged_sec == "" ? 
                                `<button class="tkt-icon-btn update-status-ticket-btn btn-update" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.update_status}">
                                <svg width="20" height="20" class="opacity-60" viewBox="0 0 18 18" fill="none">
                                    <path d="M15.75 4.5H10.8752L8.92477 3.0375C8.72975 2.89199 8.49308 2.8131 8.24977 2.8125H5.0625C4.76413 2.8125 4.47798 2.93103 4.267 3.142C4.05603 3.35298 3.9375 3.63913 3.9375 3.9375V5.0625H2.8125C2.51413 5.0625 2.22798 5.18103 2.017 5.392C1.80603 5.60298 1.6875 5.88913 1.6875 6.1875V14.0625C1.6875 14.3609 1.80603 14.647 2.017 14.858C2.22798 15.069 2.51413 15.1875 2.8125 15.1875H13.5626C13.8442 15.1871 14.1143 15.0751 14.3134 14.8759C14.5126 14.6768 14.6246 14.4067 14.625 14.1251V12.9375H15.8126C16.0942 12.9371 16.3643 12.8251 16.5634 12.6259C16.7626 12.4268 16.8746 12.1567 16.875 11.8751V5.625C16.875 5.32663 16.7565 5.04048 16.5455 4.8295C16.3345 4.61853 16.0484 4.5 15.75 4.5ZM13.5 14.0625H2.8125V6.1875H5.99977L8.1 7.7625C8.19737 7.83553 8.31579 7.875 8.4375 7.875H13.5V14.0625ZM15.75 11.8125H14.625V7.875C14.625 7.57663 14.5065 7.29048 14.2955 7.0795C14.0845 6.86853 13.7984 6.75 13.5 6.75H8.62523L6.67477 5.2875C6.47975 5.14199 6.24308 5.0631 5.99977 5.0625H5.0625V3.9375H8.24977L10.35 5.5125C10.4474 5.58553 10.5658 5.625 10.6875 5.625H15.75V11.8125Z" fill="currentColor" />
                                </svg>
                            </button>`
                            : ''}
                            ${t.config.user.limits && typeof controls.ctrl_delete != "undefined" && controls.ctrl_delete === 1 && ticket.status_id != 6 && ticket.spam != 1 && is_merged_sec == "" && ticket.access_privilege == true ? 
                                `<button class="tkt-icon-btn delete-btn" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.delete}">
                                <svg width="14" height="15" class="opacity-60" viewBox="0 0 14 15" fill="none">
                                    <path d="M12.8159 2.23184H10.0298V1.67388C10.0298 1.22994 9.85373 0.80418 9.54023 0.490267C9.22674 0.176355 8.80155 0 8.3582 0L5.01492 0C4.57158 0 4.14639 0.176355 3.83289 0.490267C3.5194 0.80418 3.34328 1.22994 3.34328 1.67388V2.23184H0.557214C0.409431 2.23184 0.267702 2.29062 0.163204 2.39526C0.0587064 2.4999 0 2.64182 0 2.7898C0 2.93778 0.0587064 3.07969 0.163204 3.18433C0.267702 3.28897 0.409431 3.34776 0.557214 3.34776H1.11443V13.391C1.11443 13.687 1.23184 13.9708 1.44084 14.1801C1.64983 14.3894 1.93329 14.5069 2.22885 14.5069H11.1443C11.4398 14.5069 11.7233 14.3894 11.9323 14.1801C12.1413 13.9708 12.2587 13.687 12.2587 13.391V3.34776H12.8159C12.9637 3.34776 13.1054 3.28897 13.2099 3.18433C13.3144 3.07969 13.3731 2.93778 13.3731 2.7898C13.3731 2.64182 13.3144 2.4999 13.2099 2.39526C13.1054 2.29062 12.9637 2.23184 12.8159 2.23184ZM4.45771 1.67388C4.45771 1.5259 4.51642 1.38398 4.62091 1.27934C4.72541 1.1747 4.86714 1.11592 5.01492 1.11592H8.3582C8.50599 1.11592 8.64772 1.1747 8.75222 1.27934C8.85671 1.38398 8.91542 1.5259 8.91542 1.67388V2.23184H4.45771V1.67388ZM11.1443 13.391H2.22885V3.34776H11.1443V13.391ZM5.57214 6.13755V10.6012C5.57214 10.7492 5.51343 10.8911 5.40893 10.9958C5.30443 11.1004 5.16271 11.1592 5.01492 11.1592C4.86714 11.1592 4.72541 11.1004 4.62091 10.9958C4.51642 10.8911 4.45771 10.7492 4.45771 10.6012V6.13755C4.45771 5.98957 4.51642 5.84765 4.62091 5.74301C4.72541 5.63838 4.86714 5.57959 5.01492 5.57959C5.16271 5.57959 5.30443 5.63838 5.40893 5.74301C5.51343 5.84765 5.57214 5.98957 5.57214 6.13755ZM8.91542 6.13755V10.6012C8.91542 10.7492 8.85671 10.8911 8.75222 10.9958C8.64772 11.1004 8.50599 11.1592 8.3582 11.1592C8.21042 11.1592 8.06869 11.1004 7.96419 10.9958C7.8597 10.8911 7.80099 10.7492 7.80099 10.6012V6.13755C7.80099 5.98957 7.8597 5.84765 7.96419 5.74301C8.06869 5.63838 8.21042 5.57959 8.3582 5.57959C8.50599 5.57959 8.64772 5.63838 8.75222 5.74301C8.85671 5.84765 8.91542 5.98957 8.91542 6.13755Z" fill="currentColor" />
                                </svg>
                            </button>`
                            : ''}
                            ${(() => {
                                let isStarred = false;
                                if (ticket.spam != 1 && ticket.starred) {
                                    const array = ticket.starred.split(",");
                                    isStarred = array.includes(config.auth.id.toString());
                                }
                                if (isStarred) {
                                    return `<button class="tkt-icon-btn star-ticket-btn" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.star}">
                                                <svg width="20" height="20" class="opacity-60" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M18.6854 7.59833C18.6072 7.35812 18.4598 7.1464 18.2615 6.98986C18.0633 6.83331 17.8231 6.73895 17.5713 6.71864L12.9619 6.34677L11.1823 2.04286C11.086 1.80834 10.9222 1.60774 10.7117 1.46656C10.5011 1.32538 10.2533 1.25 9.99983 1.25C9.74633 1.25 9.49856 1.32538 9.28801 1.46656C9.07747 1.60774 8.91366 1.80834 8.81741 2.04286L7.03929 6.34599L2.42757 6.71864C2.17536 6.73997 1.93506 6.83526 1.73677 6.99255C1.53847 7.14985 1.39101 7.36217 1.31285 7.6029C1.23469 7.84364 1.22931 8.10209 1.29738 8.34587C1.36546 8.58965 1.50396 8.80792 1.69554 8.97333L5.21116 12.0069L4.14007 16.5429C4.08016 16.7893 4.09483 17.048 4.18221 17.286C4.26958 17.5241 4.42573 17.7309 4.63082 17.88C4.83591 18.0292 5.0807 18.1141 5.33411 18.1239C5.58752 18.1337 5.83814 18.0679 6.05413 17.935L9.99944 15.5069L13.9471 17.935C14.1632 18.0664 14.4133 18.1308 14.6659 18.1203C14.9185 18.1098 15.1624 18.0247 15.3668 17.8759C15.5712 17.727 15.727 17.521 15.8145 17.2838C15.9021 17.0466 15.9175 16.7888 15.8588 16.5429L14.7838 12.0061L18.2994 8.97255C18.4926 8.80741 18.6323 8.58867 18.701 8.34403C18.7697 8.09939 18.7643 7.83987 18.6854 7.59833Z" fill="currentColor"/>
                                                </svg>
                                            </button>`;
                                } else {
                                    if (t.config.user.limits == 1) {
                                        return `<button class="tkt-icon-btn star-ticket-btn" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.star}">
                                <svg width="20" height="20" class="opacity-60" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.6854 7.59833C18.6072 7.35812 18.4598 7.1464 18.2615 6.98986C18.0633 6.83331 17.8231 6.73895 17.5713 6.71864L12.9619 6.34677L11.1823 2.04286C11.086 1.80834 10.9222 1.60774 10.7117 1.46656C10.5011 1.32538 10.2533 1.25 9.99983 1.25C9.74633 1.25 9.49856 1.32538 9.28801 1.46656C9.07747 1.60774 8.91366 1.80834 8.81741 2.04286L7.03929 6.34599L2.42757 6.71864C2.17536 6.73997 1.93506 6.83526 1.73677 6.99255C1.53847 7.14985 1.39101 7.36217 1.31285 7.6029C1.23469 7.84364 1.22931 8.10209 1.29738 8.34587C1.36546 8.58965 1.50396 8.80792 1.69554 8.97333L5.21116 12.0069L4.14007 16.5429C4.08016 16.7893 4.09483 17.048 4.18221 17.286C4.26958 17.5241 4.42573 17.7309 4.63082 17.88C4.83591 18.0292 5.0807 18.1141 5.33411 18.1239C5.58752 18.1337 5.83814 18.0679 6.05413 17.935L9.99944 15.5069L13.9471 17.935C14.1632 18.0664 14.4133 18.1308 14.6659 18.1203C14.9185 18.1098 15.1624 18.0247 15.3668 17.8759C15.5712 17.727 15.727 17.521 15.8145 17.2838C15.9021 17.0466 15.9175 16.7888 15.8588 16.5429L14.7838 12.0061L18.2994 8.97255C18.4926 8.80741 18.6323 8.58867 18.701 8.34403C18.7697 8.09939 18.7643 7.83987 18.6854 7.59833ZM17.4869 8.02567L13.6823 11.3069C13.5955 11.3817 13.5309 11.4789 13.4957 11.5879C13.4604 11.6969 13.4558 11.8134 13.4823 11.9249L14.6448 16.8311C14.6478 16.8379 14.6481 16.8456 14.6456 16.8525C14.6431 16.8595 14.6381 16.8653 14.6315 16.8686C14.6174 16.8796 14.6135 16.8772 14.6018 16.8686L10.3268 14.2397C10.2283 14.1792 10.115 14.1472 9.99944 14.1472C9.88387 14.1472 9.77055 14.1792 9.6721 14.2397L5.3971 16.8702C5.38538 16.8772 5.38225 16.8796 5.36741 16.8702C5.36082 16.8668 5.35576 16.8611 5.35329 16.8541C5.35082 16.8471 5.35112 16.8395 5.35413 16.8327L6.51663 11.9265C6.54312 11.815 6.53848 11.6984 6.50321 11.5894C6.46794 11.4805 6.4034 11.3833 6.31663 11.3085L2.51194 8.02724C2.50257 8.01942 2.49397 8.01239 2.50179 7.98817C2.5096 7.96395 2.51585 7.96708 2.52757 7.96552L7.52132 7.56239C7.63586 7.55257 7.74547 7.51134 7.83809 7.44325C7.93072 7.37516 8.00277 7.28285 8.04632 7.17645L9.96976 2.51942C9.976 2.50614 9.97835 2.49989 9.9971 2.49989C10.0158 2.49989 10.0182 2.50614 10.0244 2.51942L11.9526 7.17645C11.9965 7.28289 12.069 7.3751 12.1621 7.44293C12.2551 7.51076 12.3651 7.55156 12.4799 7.56083L17.4737 7.96395C17.4854 7.96395 17.4924 7.96395 17.4994 7.98661C17.5065 8.00927 17.4994 8.01786 17.4869 8.02567Z" fill="currentColor"/>
                                </svg>
                            </button>`;
                                    } else {
                                        return '';
                                    }
                                }
                            })()}
                            <button class="tkt-icon-btn history-ticket-btn" data-id="${ticket.id}" data-bs-toggle="tooltip" title="${t.config.translations.history}">
                                <svg width="16" height="15" class="opacity-60" viewBox="0 0 16 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8.75 3.75002V7.14612L11.5719 8.83909C11.714 8.92445 11.8164 9.06279 11.8566 9.22366C11.8967 9.38453 11.8713 9.55476 11.7859 9.6969C11.7006 9.83904 11.5622 9.94144 11.4014 9.98159C11.2405 10.0217 11.0703 9.99633 10.9281 9.91096L7.80312 8.03596C7.71063 7.98039 7.63411 7.90183 7.58099 7.80791C7.52787 7.71399 7.49997 7.60792 7.5 7.50002V3.75002C7.5 3.58426 7.56585 3.42529 7.68306 3.30808C7.80027 3.19087 7.95924 3.12502 8.125 3.12502C8.29076 3.12502 8.44973 3.19087 8.56694 3.30808C8.68415 3.42529 8.75 3.58426 8.75 3.75002ZM8.125 2.31338e-05C7.13906 -0.00243276 6.16242 0.190675 5.25161 0.568169C4.34079 0.945664 3.51389 1.50005 2.81875 2.19924C2.25078 2.77424 1.74609 3.32737 1.25 3.90627V2.50002C1.25 2.33426 1.18415 2.17529 1.06694 2.05808C0.949731 1.94087 0.79076 1.87502 0.625 1.87502C0.45924 1.87502 0.300268 1.94087 0.183058 2.05808C0.065848 2.17529 0 2.33426 0 2.50002L0 5.62502C0 5.79078 0.065848 5.94975 0.183058 6.06697C0.300268 6.18418 0.45924 6.25002 0.625 6.25002H3.75C3.91576 6.25002 4.07473 6.18418 4.19194 6.06697C4.30915 5.94975 4.375 5.79078 4.375 5.62502C4.375 5.45926 4.30915 5.30029 4.19194 5.18308C4.07473 5.06587 3.91576 5.00002 3.75 5.00002H1.95312C2.51172 4.34221 3.06797 3.72268 3.70234 3.08049C4.57098 2.21186 5.67633 1.61847 6.88029 1.37446C8.08424 1.13045 9.33341 1.24665 10.4717 1.70853C11.61 2.17041 12.5869 2.95749 13.2805 3.97144C13.974 4.98538 14.3533 6.18121 14.3711 7.40952C14.3889 8.63782 14.0443 9.84413 13.3804 10.8777C12.7165 11.9113 11.7627 12.7263 10.6382 13.2209C9.51379 13.7155 8.2685 13.8678 7.05799 13.6587C5.84749 13.4496 4.72543 12.8885 3.83203 12.0453C3.77232 11.9889 3.70208 11.9448 3.62532 11.9155C3.54856 11.8862 3.46679 11.8724 3.38467 11.8747C3.30254 11.877 3.22168 11.8955 3.1467 11.929C3.07172 11.9626 3.00408 12.0106 2.94766 12.0703C2.89123 12.13 2.84712 12.2003 2.81783 12.277C2.78855 12.3538 2.77467 12.4356 2.777 12.5177C2.77932 12.5998 2.79779 12.6807 2.83136 12.7557C2.86493 12.8306 2.91295 12.8983 2.97266 12.9547C3.86285 13.7948 4.94512 14.4042 6.125 14.7298C7.30489 15.0554 8.54653 15.0873 9.74157 14.8226C10.9366 14.558 12.0487 14.005 12.9809 13.2117C13.913 12.4184 14.6368 11.4091 15.0892 10.2718C15.5415 9.13442 15.7086 7.90366 15.5759 6.68689C15.4432 5.47011 15.0147 4.30431 14.3279 3.29122C13.641 2.27813 12.7166 1.44854 11.6354 0.874854C10.5542 0.301167 9.34899 0.000819796 8.125 2.31338e-05Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        // document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        //     new bootstrap.Tooltip(el);
        // });
    }

    // Update theme colors without reloading cards
    t.updateCardThemeColors = function () {
        var isDark = t.isDarkMode();

        // Update status badges
        $('.tkt-status-badge').each(function () {
            var $badge = $(this);
            var colorCode = $badge.data('color-code');

            if (colorCode) {
                var newBgColor = t.getThemeBasedColor(colorCode, 90);
                $badge.css({
                    'background-color': newBgColor,
                    'color': colorCode,
                    'border': '1px solid ' + colorCode + '30'
                });
            }
        });

        // Update tkt-info-bar
        $('.tkt-info-bar').each(function () {
            var $infoBar = $(this);
            var colorCode = $infoBar.data('color-code');

            if (colorCode) {
                var newBgColor = t.getThemeBasedColor(colorCode, 95);
                $infoBar.css('background-color', newBgColor);
            }
        });

        // Update tkt-right-actions
        $('.tkt-right-actions').each(function () {
            var $rightActions = $(this);
            var colorCode = $rightActions.data('color-code');

            if (colorCode) {
                var newBgColor = t.getThemeBasedColor(colorCode, 95);
                $rightActions.css('background-color', newBgColor);
            }
        });

        // Update tkt-action-bar
        $('.tkt-action-bar').each(function () {
            var $actionBar = $(this);
            var colorCode = $actionBar.data('color-code');

            if (colorCode) {
                var newBgColor = t.getThemeBasedColor(colorCode, 95);
                $actionBar.css('background-color', newBgColor);
            }
        });
    }

    $(document).on('click', '[data-theme-toggle]', function () {
        setTimeout(function () {
            if (typeof t !== 'undefined' && t.updateCardThemeColors) {
                t.updateCardThemeColors();
            }
        }, 50);
    });

    // Helper function to escape HTML
    t.escapeHtml = function (str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function (m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    t.renderSortDropdown();
    t.updateSortDirectionIcon();
    t.initTooltips();
    t.loadTicketList();

    // edit ticket code start from here 
    t.mdlEditTicket = t.content.find("#editTicketModal");
    t.frmEditTicket = t.mdlEditTicket.find("#edit-ticket-mdl-frm");
    t.frmEditTicket.extraData = {};
    t.frmEditTicket.el = {};
    t.mdlEditTicket.title = t.frmEditTicket.find('.modal-title');
    t.frmEditTicket.el.id = t.frmEditTicket.find("#id");
    t.frmEditTicket.el.department_id = t.frmEditTicket.find("#edit_department_id");
    t.frmEditTicket.el.problem_category_id = t.frmEditTicket.find("#edit_problem_category_id");
    t.frmEditTicket.el.sub_category_id_cvr = t.frmEditTicket.find("#sub_category_id_cvr");
    t.frmEditTicket.el.sub_category_id = t.frmEditTicket.find("#edit_sub_category_id");
    t.frmEditTicket.el.priority_id = t.frmEditTicket.find("#edit_priority_id");
    t.frmEditTicket.el.tat = t.frmEditTicket.find("#tat");
    t.frmEditTicket.el.token = t.frmEditTicket.find("#token");
    t.frmEditTicket.el.self_assign = t.frmEditTicket.find("#edit_self_assign");
    t.frmEditTicket.el.assign_to_cvr = t.frmEditTicket.find("#assigned_to_cvr");
    t.frmEditTicket.el.assign_to = t.frmEditTicket.find("#edit_assigned_to");
    t.frmEditTicket.el.device_id = t.frmEditTicket.find("#edit_device_id");
    t.frmEditTicket.el.creatorId = t.frmEditTicket.find("#edit_creator_id");
    t.frmEditTicket.el.delete_previous_tasks = t.frmEditTicket.find("#remove_tasks");
    t.frmEditTicket.el.btnSubmit = t.frmEditTicket.find('#btnSubmit');

    t.fillAssignedToForEdit = function(handlers, assign_mode, current_handler) {
        var temp;
        t.frmEditTicket.el.assign_to.empty().append(new Option(config.translations.Select_User, ""));
        if(assign_mode != 1) {
            $.each(handlers, function(i,v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmEditTicket.el.assign_to.append(temp);
            });
        }
        t.frmEditTicket.el.assign_to.trigger("change");
    };

    t.loadEditTicket = function(e, assign_mode) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.frmEditTicket.el.self_assign.val("");
        t.frmEditTicket.el.assign_to_cvr.removeClass("hide");
        t.mdlEditTicket.title.text("Edit Ticket");
        t.frmEditTicket.el.btnSubmit.text('Update');
        if (typeof assign_mode != "undefined") {
            // t.frmTransfer.el.self_assign.val(1);
        }
        $.get(t.config.url.get_data_for_transfer + "/" + id + "?from=edit", function(result) {
            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                let editActionControls = result.data?.edit_ticket_action_controls;
                if (editActionControls?.ctrl_tat === 1) {
                    t.frmEditTicket.el.tat.prop('disabled',false);
                } else {
                    t.frmEditTicket.el.tat.prop('disabled',true);
                }
                if (editActionControls?.ctrl_priority === 1) {
                    t.frmEditTicket.el.priority_id.prop('disabled',false);
                } else {
                    t.frmEditTicket.el.priority_id.prop('disabled',true);
                }
                t.frmEditTicket.el.id.val(id);
                t.frmEditTicket.el.token.val(t.config.token);
                t.frmEditTicket.el.creatorId.val(result.data.ticket.creator_id);
                t.frmEditTicket.find("#department_id").val(result.data.opts.departments.id);
                t.frmEditTicket.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                if(result.data.opts.devices.id != null) {
                    t.frmEditTicket.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmEditTicket.el.device_id.empty().append(new Option(config.translations.select_device, ""));
                }
                /* set problem category */
                t.frmEditTicket.el.problem_category_id.empty();
                t.data.trnsfer_problem_categories = result.data.opts.problem_categories;
                t.data.transfer_sub_categories = result.data.opts.problem_categories;
                $.each(result.data.opts.problem_categories, function(i, d) {
                    if(d.id == result.data.ticket.problem_category_id){
                        var op = new Option(d.name, d.id, true, true);
                        t.config.pc = d.id;
                    }else{
                        var op = new Option(d.name,d.id);
                    }
                    var $op = $(op);
                    $op.attr('form', d.form_id);
                    t.frmEditTicket.el.problem_category_id.append(op);
                });
                t.frmEditTicket.el.problem_category_id.trigger("change");

                /* set sub category */
                t.config.s_pc = result.data.ticket.sub_category_id;
                t.refillSubCategoryForEdit(undefined, result.data.ticket.sub_category_id);

                t.frmEditTicket.el.priority_id.empty();
                $.each(t.config.priorities, function(i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmEditTicket.el.priority_id.append(op);
                });

                t.frmEditTicket.el.tat.val(result.data.ticket.tat);
                /* set handlers to assign to */
                t.fillAssignedToForEdit(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

                t.updateSubCategoryVisibilityForEdit();

                t.mdlEditTicket.modal("show");
            } else {
                var data = {
                    'msg': 'Unable to load transfer form.',
                };
                sweetAlert('center', 'error', data);
            }
        });
    };

    t.fillSlaForEdit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmEditTicket.el.problem_category_id.val();
        var sub_category_id = t.frmEditTicket.el.sub_category_id.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.transfer_sub_categories) == true && t.data.transfer_sub_categories.length) {
            $.each(t.data.transfer_sub_categories, function(i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEditTicket.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.trnsfer_problem_categories != "undefined" && t.data.trnsfer_problem_categories.length) {
            $.each(t.data.trnsfer_problem_categories, function(i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmEditTicket.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmEditTicket.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.frmEditTicket.el.priority_id.val("").trigger("change");
            // t.frmTransfer.el.tat.val(0);
            t.offListen = false;
        }
        t.updateSubCategoryVisibilityForEdit();
    };

    t.refillPriorityForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEditTicket.el.priority_id.empty().append(new Option(config.translations.select_priority, ""));
        t.frmEditTicket.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmEditTicket.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmEditTicket.el.priority_id.trigger("change");
    };

    // to fill the tat by default priority service time
    t.refillTatForEdit = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.frmEditTicket.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmEditTicket.el.tat.val(val);
    }

    // refill the sub category for Ticket Transfer
    t.refillSubCategoryForEdit = function(e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.transfer_sub_categories = [];
        t.frmEditTicket.el.sub_category_id.empty().append(new Option(t.config.translations.select_sub_category, ""));
        var type_val = parseInt($.trim(t.frmEditTicket.el.problem_category_id.val()));
        if(t.config.pc != type_val){
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none');
        }else{
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none');
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.trnsfer_problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.transfer_sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
                                t.frmEditTicket.el.sub_category_id.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }

        if (typeof val != "undefined") {
            t.frmEditTicket.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibilityForEdit();
    };

    t.updateSubCategoryVisibilityForEdit = function() {
        if ( t.data.transfer_sub_categories.length > 0) {
            t.frmEditTicket.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmEditTicket.el.sub_category_id_cvr.show();
        } else {
            t.frmEditTicket.el.sub_category_id.rules("remove");
            t.frmEditTicket.el.sub_category_id_cvr.hide();
        }
    };

    t.frmEditTicket.el.sub_category_id.on('change',function(){
        let val = $(this).val();
        if((val != t.config.s_pc) && t.config.s_pc != 'undefined'){
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none');
        }else{
            t.frmEditTicket.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none');
        }
    })

    t.frmForEditValidator = t.frmEditTicket.validate({
        onsubmit: false,
        rules: {
            department_id: {
                required: true,
                str_name: true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
            }
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmEditTicketSubmit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        if (t.frmForEditValidator.form() == false) {
            return false;
        }

        if (t.httpCall != true) {
            return false;
        }

        if (t.frmEditTicket.el.priority_id.prop("disabled")) {
            let val =  t.frmEditTicket.el.priority_id.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmEditTicket);
        }
        if (t.frmEditTicket.el.tat.prop("disabled")) {
            let tatVal =  t.frmEditTicket.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmEditTicket);
        }
        t.frmEditTicket.el.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmEditTicket.get(0));
        var http = $.ajax({
            url: config.url.editTicket,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlEditTicket.modal("hide");
                    setTimeout(function() {
                       t.refreshTicketList();
                    }, 2000);
                } else {
                    t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateEditTicketform();
                    }
                }
            }
        });
        http.fail(function() {
            t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmEditTicket.el.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    t.checkTechnicalAvabilityForTicketEdit = function() {
        var attendarId = t.frmEditTicket.el.assign_to.val();
        t.frmEditTicket.find(".availabilityError, .availabilitySuccess").html("");
        if(attendarId == '') {
            return false;
        }
        $.ajax({
            url:t.config.url.getTechCurrentStatusById+'/'+attendarId,
            method:'GET',
            success:function(result) {
                if(result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmEditTicket.find(".availabilityError").html(t.config.translations.tecnician_is_available_or_not)
                } else {
                    t.frmEditTicket.find(".availabilitySuccess").html(t.config.translations.tecnician_is_available)
                }
            }
        });
    }

    var select2Opts = { width: "100%" };

    /* related to ticket edit */
    t.frmEditTicket.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEditTicket.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: companyId,
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.enter_starting
    }));
    
    t.frmEditTicket.el.department_id.on('select2:opening', function(e) {
        e.preventDefault();
        return false;
    });

    // Add readonly styling
    t.frmEditTicket.el.department_id.next('.select2-container').find('.select2-selection')
    .css({
        'pointer-events': 'none',
        'background-color': 'rgb(203 204 205 / 28%)',
        'cursor': 'not-allowed'
    });
    
    t.frmEditTicket.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEditTicket.el.device_id.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.frmEditTicket.el.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && config.client == "ltts") ? true : false,
                };
            },
            delay: 300
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_device,
        templateResult: function(s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            a = "<div class='so-t'><i class=\"fa fa-tag\"></i> " + s.asset_tag + "</div>";
            if (s.asset_name != null) {
                a += "<div class='so-t'><i class=\"fa fa-laptop\"></i> " + s.asset_name + "</div>";
            }
            a += "<div class='so-m'><i class=\"fa fa-tablet\"></i> " + s.name + " " + s.modelno + "</div>";
            a += "<div class='so-t'><i class=\"fa fa-barcode\"></i> " + s.serial + "</div>";
            return $("<div>" + a + "</div>");
        }
    }));

    t.frmEditTicket.el.problem_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmEditTicket.el.problem_category_id.parent()} )).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEditTicket.el.problem_category_id.on("change", $.proxy(t.refillSubCategoryForEdit));
    t.frmEditTicket.el.problem_category_id.on('select2:select',function(){
        t.updateEditTicketform();
    });
    t.frmEditTicket.el.sub_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmEditTicket.el.sub_category_id.parent()} )).on("change", $.proxy(t.fillSlaForEdit));
    t.frmEditTicket.el.sub_category_id.on('select2:select',function(){
        if($("#edit-ticket-mdl-frm").find("#request_submit_id").val() == 0) {
            t.updateEditTicketform();
        }
    });
    t.frmEditTicket.el.priority_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmEditTicket.el.priority_id.parent()} )).on("change", $.proxy(t.refillTatForEdit));
    t.frmEditTicket.el.assign_to.select2($.extend({},{
        dropdownParent: t.frmEditTicket.el.assign_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id : t.frmEditTicket.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id : t.frmEditTicket.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }
    })).on('change', t.checkTechnicalAvabilityForTicketEdit);

    t.updateEditTicketform = function() {
        var foundObject = null;
        if (t.frmEditTicket.el.problem_category_id.val() != null && t.frmEditTicket.el.problem_category_id.val() != '') {
            $.each(t.data.trnsfer_problem_categories, function(index, obj) {
                if (obj.id == t.frmEditTicket.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if (foundObject != null) {
                var found = false
                if (foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function(key, value) {
                        if (value.form_id != undefined && value.form_id != 0 && value.form_id != null) {
                            found = true;
                            return false; // exit the loop
                        } else if(value.form_id == undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') !=0) {
                            found = true;
                            return false;
                        }
                    });
                    if (found) {
                        if (t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                                // t.frmTransfer.el.form_info.show();
                            }
                        }
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmEditTicket.el.sub_category_id.val() != "") {
                            if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEditTicket.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': t.config.translations.unable_to_load_form
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmEditTicket.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                // t.frmTransfer.el.form.html($('<a target="_blank" class="formUrl">').attr('href', newUrl).text(newUrl));
                            }
                        }
                    }
                } else {
                    if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if (t.frmEditTicket.el.problem_category_id.find(':selected').attr('form') == null || t.frmEditTicket.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmEditTicket.el.problem_category_id.find(':selected').attr('form');
                            if (form_id !== 0) {
                                var request_id = btoa(t.frmEditTicket.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmEditTicket.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'unable to load Form.'
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        } else {
                            var form_id = t.frmEditTicket.el.sub_category_id.find(':selected').attr('form');
                            var request_id = btoa(t.frmEditTicket.el.id.val());
                            var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                        }
                    }
                }
                if (found) {
                    $("#mdl-edit").find("#request_submit_id").val(0);
                } else {
                    $("#mdl-edit").find("#request_submit_id").val("");
                }
            }
        }
    }

    t.frmEditTicket.el.btnSubmit.on("click", $.proxy(t.frmEditTicketSubmit));
    t.content.on("click", ".editTicket", $.proxy(t.loadEditTicket));
    // Edit ticket code end here

    // start ticket transfer code from here
    t.mdlTransfer = t.content.find("#ticketTransferModal");
    t.frmTransfer = t.mdlTransfer.find("#ticket-transfer-mdl-frm");
    t.frmTransfer.extraData = {};
    t.frmTransfer.el = {};
    t.mdlTransfer.title = t.frmTransfer.find('#customStatusModalTitle');
    t.frmTransfer.el.id = t.frmTransfer.find("#id");
    t.frmTransfer.el.token = t.frmTransfer.find("#token");
    t.frmTransfer.el.department_id = t.frmTransfer.find("#transfer_department_id");
    t.frmTransfer.el.problem_category_id = t.frmTransfer.find("#transfer_problem_category_id");
    t.frmTransfer.el.sub_category_id_cvr = t.frmTransfer.find("#transfer_sub_category_id_cvr");
    t.frmTransfer.el.sub_category_id = t.frmTransfer.find("#transfer_sub_category_id");
    t.frmTransfer.el.priority_id = t.frmTransfer.find("#transfer_priority_id");
    t.frmTransfer.el.tat = t.frmTransfer.find("#tat");
    t.frmTransfer.el.self_assign = t.frmTransfer.find(".self_assign");
    t.frmTransfer.el.assign_to_cvr = t.frmTransfer.find("#transfer_assigned_to_cvr");
    t.frmTransfer.el.assign_to = t.frmTransfer.find("#transfer_assigned_to");
    t.frmTransfer.el.device_id = t.frmTransfer.find("#transfer_device_id");
    t.frmTransfer.el.tags = t.frmTransfer.find("#transfer_tags");
    t.frmTransfer.el.delete_previous_tasks = t.frmTransfer.find("#remove_tasks");
    t.frmTransfer.el.is_note = t.frmTransfer.find("#is_note");
    t.frmTransfer.el.creatorId = t.frmTransfer.find("#creator_id");
    t.frmTransfer.el.btnSubmit = t.frmTransfer.find('#btnSubmit');

    t.getAssignableUsers = function(department_id, assign_mode) {
        if( department_id > 0 && !isNaN(department_id) ) {
            $.get(t.config.url.get_users_to_assign_by_dep + '/' + department_id).done(function(data) {
                t.fillAssignedTo(data, assign_mode, "");
            });
        }
        else {
            t.fillAssignedTo([], assign_mode, "");
        }
    };

    // refill the problem category for Ticket Transfer
    t.refillProblemCategoryTicketTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.trnsfer_problem_categories = [];
        t.frmTransfer.el.problem_category_id.empty().append(new Option("Select category",""));
        var type_val = parseInt($.trim(t.frmTransfer.el.department_id.val()));
        if(type_val === t.config.transfer_department_id){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            if(t.config.client === "ltts") {
                var selectedDept = t.frmTransfer.el.department_id.find(':selected').text();
                departmentName = selectedDept.substr(0, selectedDept.indexOf('(') );
                if(departmentName.trim() == "Admin - India") {
                    t.frmTransfer.el.problem_category_id.empty().append(new Option("Select Location", ""));
                }
            }
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function(data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.trnsfer_problem_categories = data.data;
                    $.each(data.data, function(i, v) {
                        t.frmTransfer.el.problem_category_id.append($('<option>').val(v.id).text(v.name).attr('description', v.remarks).attr('form', v.form_id));
                    });
                }
            }).always(function() {
                t.frmTransfer.el.problem_category_id.trigger("change");
            });
        } else {
            t.frmTransfer.el.problem_category_id.trigger("change");
        }
        t.getAssignableUsers(type_val, t.frmTransfer.el.self_assign.val());
    };

    t.frmTransfer.el.problem_category_id.on('change',function() {
        t.frmTransfer.el.sub_category_id.val('').trigger('change');
    });

    // refill the sub category for Ticket Transfer
    t.refillSubCategoryTicketTransfer = function(e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.transfer_sub_categories = [];

        t.frmTransfer.el.sub_category_id.empty().append(new Option("select SubCategory", ""));
        var type_val = parseInt($.trim(t.frmTransfer.el.problem_category_id.val()));
        if(type_val != t.config.transfer_pc){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.trnsfer_problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.transfer_sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
                                t.frmTransfer.el.sub_category_id.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }

        if (typeof val != "undefined") {
            t.frmTransfer.el.sub_category_id.val(val);
        }

        t.updateSubCategoryVisibilityTicketTransfer();
        // t.frmTransfer.el.sub_category_id.trigger("change");
    };

    t.frmTransfer.el.sub_category_id.on('change',function(){
        let sub_category_id = $(this).val();
        if(sub_category_id != 'undefined' && (sub_category_id != t.config.transfer_s_pc)){
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').removeClass('d-none')
        }else{
            t.frmTransfer.el.delete_previous_tasks.closest('.checkbox-row').addClass('d-none')
        }
    })

    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibilityTicketTransfer = function() {
        if ( t.data.transfer_sub_categories.length > 0) {
            t.frmTransfer.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmTransfer.el.sub_category_id_cvr.show();
        } else {
            t.frmTransfer.el.sub_category_id.rules("remove");
            t.frmTransfer.el.sub_category_id_cvr.hide();
        }
    };

    t.fillSlaTicketTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.frmTransfer.el.problem_category_id.val();
        var sub_category_id = t.frmTransfer.el.sub_category_id.val();
        var found = false;

        /* if sub category there */
        if (Array.isArray(t.data.transfer_sub_categories) == true && t.data.transfer_sub_categories.length) {
            $.each(t.data.transfer_sub_categories, function(i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmTransfer.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.trnsfer_problem_categories != "undefined" && t.data.trnsfer_problem_categories.length) {
            $.each(t.data.trnsfer_problem_categories, function(i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                        t.frmTransfer.el.tat.val(parseInt(k.tat) ? parseInt(k.tat) : 0);
                        t.offListen = false;
                    } else {
                        t.frmTransfer.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.frmTransfer.el.priority_id.val("").trigger("change");
            t.frmTransfer.el.tat.val(0);
            t.offListen = false;
        }
        // t.updateTransferform();
    };

    t.refillPriorityForTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmTransfer.el.priority_id.empty().append(new Option(t.config.translations.select_priority, ""));
        t.frmTransfer.el.tat.val("");
        $.each(t.config.priorities, function (i, k) {
            t.frmTransfer.el.priority_id.append(new Option(k.name, k.id));
        });
        t.frmTransfer.el.priority_id.trigger("change");
    };

    t.refillTatForTransfer = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val =  t.frmTransfer.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function(i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
        t.frmTransfer.el.tat.val(val);
    }

    t.frmTransferValidator = t.frmTransfer.validate({
        onsubmit: false,
        rules: {
            department_id: {
                required: true,
                str_name: true
            },
            problem_category_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
            },
            "tags[]":{
                clean_text_only: true,
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmTransferSubmit = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        let s = t.frmTransfer.find("#remarks").val();
        count = s.replaceAll("&nbsp;", "").trim();
        if (t.frmTransferValidator.form() == false) {
            if(count.length <= 0) {
                $('#ticketTransferModal').find('#shows_error').html('This field is required.');
                $('#ticketTransferModal').find('#shows_error').css({'color':'#c53030','font-size':'13px','margin-left': '-13px'});
            }
            return false;
        }
        if(count.length <= 0) {
            $('#ticketTransferModal').find('#shows_error').html('This field is required');
            $('#ticketTransferModal').find('#shows_error').css({'color':'#c53030','font-size':'13px','margin-left': '-13px'});
            return false;
        } else {
            $('#ticketTransferModal').find('#shows_error').html('');
        }

        if (t.httpCall != true) {
            return false;
        }
        if (t.frmTransfer.el.priority_id.prop("disabled")) {
            let val = t.frmTransfer.el.priority_id.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmTransfer);
        }
        if (t.frmTransfer.el.tat.prop("disabled")) {
            let tatVal =  t.frmTransfer.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmTransfer);
        }
        t.frmTransfer.el.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmTransfer.get(0));
        var http = $.ajax({
            url: t.config.url.transfer,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlTransfer.modal("hide");
                    setTimeout(function() {
                        t.refreshTicketList();
                    }, 2000);
                } else {
                    t.frmTransfer.el.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                    if (data.requestForm == 0) {
                        t.updateTransferform();
                    }
                }
            }
        });
        http.fail(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': t.config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmTransfer.el.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };

    t.frmTransfer.toggleListen = function(s) {
        t.frmTransfer.el.department_id.off("change", $.proxy(t.refillProblemCategoryTicketTransfer));
        t.frmTransfer.el.problem_category_id.off("change", $.proxy(t.fillSlaTicketTransfer));
        t.frmTransfer.el.sub_category_id.off("change", $.proxy(t.fillSlaTicketTransfer));
        t.frmTransfer.el.priority_id.off("change", $.proxy(t.refillTatForTransfer));

        if (typeof s != "undefined" && s == true) {
            t.frmTransfer.el.department_id.on("change", $.proxy(t.refillProblemCategoryTicketTransfer));
            // t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.fillSlaTicketTransfer));
            t.frmTransfer.el.sub_category_id.on("change", $.proxy(t.fillSlaTicketTransfer));
            t.frmTransfer.el.priority_id.on("change", $.proxy(t.refillTatForTransfer));
        }
    };

        /* set assigned to handlers on transfer ticket */
    t.fillAssignedTo = function(handlers, assign_mode, current_handler) {
        var temp;
        t.frmTransfer.el.assign_to.empty().append(new Option(t.config.translations.Select_User, ""));
        if(assign_mode != 1) {
            $.each(handlers, function(i,v) {
                temp = v.id == current_handler ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                t.frmTransfer.el.assign_to.append(temp);
            });
        }
        t.frmTransfer.el.assign_to.trigger("change");
    };

    t.loadTransfer = function(e, assign_mode) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        ticketCmpId = $(this).attr("data-company");
        t.frmTransfer.find("#remarks").summernote('code', '');
        t.frmTransfer.find("#shows_error").html(""); 
        t.frmTransfer.el.self_assign.val("");
        t.frmTransfer.el.assign_to_cvr.removeClass("hide");
        t.mdlTransfer.title.text(t.config.translations.add_view_tab);
        t.frmTransfer.el.btnSubmit.text('Transfer');
        if (typeof assign_mode != "undefined") {
            t.frmTransfer.el.self_assign.val(1);
            t.frmTransfer.el.assign_to_cvr.addClass("hide");
            t.mdlTransfer.title.text('Ticket Self Assign');
            t.frmTransfer.el.btnSubmit.text('Assign');
        }
        $.get(t.config.url.get_data_for_transfer + "/" + id, function(result) {

            if (typeof result == "object") {
                if (result.status != "success") {
                    sweetAlert('center', 'error', result);
                    return;
                }
                let editActionControls = result.data?.edit_ticket_action_controls;
                if (editActionControls?.ctrl_tat === 1) {
                    t.frmTransfer.el.tat.prop('disabled',false);
                } else {
                    t.frmTransfer.el.tat.prop('disabled',true);
                }
                if (editActionControls?.ctrl_priority === 1) {
                    t.frmTransfer.el.priority_id.prop('disabled',false);
                } else {
                    t.frmTransfer.el.priority_id.prop('disabled',true);
                }
                t.frmTransfer.toggleListen();
                t.frmTransfer.el.id.val(id);
                t.frmTransfer.el.token.val(t.config.token);
                t.frmTransfer.el.creatorId.val(result.data.ticket.creator_id);
                t.frmTransfer.el.department_id.empty().append(new Option(result.data.opts.departments.text, result.data.opts.departments.id, true, true)).trigger("change");
                t.config.transfer_department_id = result.data.opts.departments.id;
                if(result.data.opts.devices.id != null) {
                    t.frmTransfer.el.device_id.append(new Option(result.data.opts.devices.text, result.data.opts.devices.id, true, true)).trigger("change");
                } else {
                    t.frmTransfer.el.device_id.empty().append(new Option(t.config.translations.select_device, ""));
                }
                /* set problem category */
                t.frmTransfer.el.problem_category_id.empty();
                $.each(result.data.opts.problem_categories, function(i, d) {
                    if(d.id == result.data.ticket.problem_category_id){
                        var op =  new Option(d.name, d.id, true, true);
                        t.config.transfer_pc = d.id;
                    }else{
                        var op = new Option(d.name, d.id)
                    }
                    if(typeof d.form_id != 'undefined' && d.form_id != null) {
                        $(op).attr('form', d.form_id);
                    }
                    t.frmTransfer.el.problem_category_id.append(op);
                });
                t.frmTransfer.el.problem_category_id.trigger("change");
                t.data.trnsfer_problem_categories = result.data.opts.problem_categories;

                /* set sub category */
                t.config.transfer_s_pc = result.data.ticket.sub_category_id;
                t.refillSubCategoryTicketTransfer(undefined, result.data.ticket.sub_category_id);

                t.frmTransfer.el.priority_id.empty();
                $.each(t.config.priorities, function(i, d) {
                    var op = (d.id == result.data.ticket.priority_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                    t.frmTransfer.el.priority_id.append(op);
                });

                t.frmTransfer.el.tat.val(result.data.ticket.tat);
                t.frmTransfer.el.tags.empty();
                $.each( result.data.tags.tags, function( key, value ) {
                    t.frmTransfer.el.tags.select2('trigger', 'select', {
                        data: {text: value.tags, id: value.id, selected: true}
                    });
                });

                /* set handlers to assign to */
                t.fillAssignedTo(result.data.opts.handlers, assign_mode, result.data.ticket.assigned_to);

                t.frmTransfer.toggleListen(true);
                t.mdlTransfer.modal("show");
            } else {
                var data = {
                    'msg': 'Unable to load transfer form.'
                };
                sweetAlert('center', 'error', data);
            }
        });
    };

    t.checkTechnicalAvabilityForTransfer = function() {
        var attendarId = t.frmTransfer.el.assign_to.val();
        t.frmTransfer.find(".availabilityError, .availabilitySuccess").html("");
        if(attendarId == '') {
            return false;
        }
        $.ajax({
            url:t.config.url.getTechCurrentStatusById+'/'+attendarId,
            method:'GET',
            success:function(result) {
                if(result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.frmTransfer.find(".availabilityError").html(t.config.translations.tecnician_is_available_or_not)
                } else {
                    t.frmTransfer.find(".availabilitySuccess").html(t.config.translations.tecnician_is_available)
                }
            }
        });
    }
    
    t.updateTransferform = function() {
        var foundObject = null;
        if(t.frmTransfer.el.problem_category_id.val() != null && t.frmTransfer.el.problem_category_id.val() != ''){
            $.each(t.data.trnsfer_problem_categories, function(index, obj) {
                if (obj.id == t.frmTransfer.el.problem_category_id.val()) {
                    foundObject = obj;
                    return false; // exit the loop
                }
            });

            if(foundObject != null) {
                var found = false;
                if(foundObject.sub != undefined && foundObject.sub.length != 0) {
                    $.each(foundObject.sub, function(key, value) {
                        if ( value.form_id != undefined && value.form_id != 0  && value.form_id != null ) {
                            found = true;
                            return false; // exit the loop
                        } else if(value.form_id == undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') !=0) {
                            found = true;
                            return false;
                        }
                    });

                    if(found) {
                        if(t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') != 0) {
                            var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                            if(form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'Unable to load form.',
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        }
                        if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0 && t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == undefined && t.frmTransfer.el.sub_category_id.val() != "") {
                            if (t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                if (form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'unable to load Form.'
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    } else {
                        if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                            if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                                var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                                if(form_id !== 0) {
                                    var request_id = btoa(t.frmTransfer.el.id.val());
                                    var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                    $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                        if (typeof result == "object") {
                                            if (result.status != "success") {
                                                sweetAlert('center', 'error', result);
                                                return;
                                            }
                                            t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                            t.frmServiceRequest.el.form_id.val(form_id);
                                            t.frmServiceRequest.el.field_values.val(result.data.fields);
                                            t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                            t.frmServiceRequest.el.for_action.val(2);
                                            t.loadRequestedForm(result.data.fields);
                                            t.mdlServiceRequest.modal("show");
                                        } else {
                                            var data = {
                                                'msg': 'Unable to load form.',
                                            };
                                            sweetAlert('center', 'error', data);
                                        }
                                    });
                                }
                            } else {
                                var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                            }
                        }
                    }
                } else {
                    if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != undefined && t.frmTransfer.el.problem_category_id.find(':selected').attr('form') != 0) {
                        if(t.frmTransfer.el.problem_category_id.find(':selected').attr('form') == null || t.frmTransfer.el.sub_category_id.find(':selected').attr('form') == null) {
                            var form_id = t.frmTransfer.el.problem_category_id.find(':selected').attr('form');
                            if(form_id !== 0) {
                                var request_id = btoa(t.frmTransfer.el.id.val());
                                var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                                $.get(t.config.url.service_request_form + "/" + form_id, function(result) {
                                    if (typeof result == "object") {
                                        if (result.status != "success") {
                                            sweetAlert('center', 'error', result);
                                            return;
                                        }
                                        t.frmServiceRequest.el.form_title.html(result.data.form_name);
                                        t.frmServiceRequest.el.form_id.val(form_id);
                                        t.frmServiceRequest.el.field_values.val(result.data.fields);
                                        t.frmServiceRequest.el.request_id.val(t.frmTransfer.el.id.val());
                                        t.frmServiceRequest.el.for_action.val(2);
                                        t.loadRequestedForm(result.data.fields);
                                        t.mdlServiceRequest.modal("show");
                                    } else {
                                        var data = {
                                            'msg': 'unable to load Form.'
                                        };
                                        sweetAlert('center', 'error', data);
                                    }
                                });
                            }
                        } else {
                            var form_id = t.frmTransfer.el.sub_category_id.find(':selected').attr('form');
                            var request_id = btoa(t.frmTransfer.el.id.val());
                            var newUrl = t.config.url.requested_form + "/" + form_id + "?q=" + request_id;
                        }
                    }
                }
                if (found) {
                    $("#ticketTransferModal").find("#request_submit_id").val(0);
                } else {
                    $("#ticketTransferModal").find("#request_submit_id").val("");
                }
            }
        }
    }

    t.frmTransfer.find("#remarks").summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.enter_your_message,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4'],
        minHeight: 120,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor')
                    .addClass('flex-fill');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(`
                    Tt
                    <span style="font-size:.65rem">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.6">
                                <path d="M3 4.5L6 7.5L9 4.5"
                                    stroke="#7F7F7F"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"/>
                            </g>
                        </svg>
                    </span>
                `);
            }
        }
    });

    /* related to transfer ticket */
    t.frmTransfer.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmTransfer.el.department_id.parent(),
        placeholder: t.config.translations.select_department,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: ticketCmpId,
                };
            },
            processResults: function(response) {
                return {
                    results: response.results,
                    pagination: {
                        more: response.pagination.more
                    }
                };
            },
            delay: 200
        },
        allowClear:true,
    }));

    t.frmTransfer.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmTransfer.el.device_id.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    user_id: t.frmTransfer.el.creatorId.val(),
                    problemManagementApiCall: (typeof t.enableUsbRequest != "undefined" && t.enableUsbRequest.length > 0 && t.config.client == "ltts") ? true : false,
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.select_device,
        templateResult: function(s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            a = "<div class='so-t'><i class=\"fa fa-tag\"></i> " + s.asset_tag + "</div>";
            if (s.asset_name != null) {
                a += "<div class='so-t'><i class=\"fa fa-laptop\"></i> " + s.asset_name + "</div>";
            }
            a += "<div class='so-m'><i class=\"fa fa-tablet\"></i> " + s.name + " " + s.modelno + "</div>";
            a += "<div class='so-t'><i class=\"fa fa-barcode\"></i> " + s.serial + "</div>";
            return $("<div>" + a + "</div>");
        }
    }));
    t.frmTransfer.el.tags.select2({
        dropdownParent:t.frmTransfer.el.tags.parent(),
        width: "100%",
        placeholder: "Add Tags",
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getTagDetails;
            },
            dataType: "json",
            delay: 200,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });
    var select2Opts = {
        width: "100%",
        placeholder: t.config.translations.enter_starting,
        allowClear: true,
    };
    t.frmTransfer.el.problem_category_id.select2($.extend({}, select2Opts, {placeholder: t.config.translations.select_problem_category, dropdownParent: t.frmTransfer.el.problem_category_id.parent()})).on("change", $.proxy(t.fillSlaTicketTransfer));
    t.frmTransfer.el.problem_category_id.on("change", $.proxy(t.refillSubCategoryTicketTransfer));
    t.frmTransfer.el.problem_category_id.on('select2:select', function() {
        t.updateTransferform();
    });
    t.frmTransfer.el.sub_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmTransfer.el.sub_category_id.parent()})).on("change", $.proxy(t.fillSlaTicketTransfer));
    t.frmTransfer.el.sub_category_id.on('select2:select', function() {
        if($("#ticketTransferModal").find("#request_submit_id").val() == 0) {
            t.updateTransferform();
        }
    });
    t.frmTransfer.el.priority_id.select2($.extend({}, select2Opts, {placeholder: t.config.translations.select_priority ,dropdownParent: t.frmTransfer.el.priority_id.parent()})).on("change", $.proxy(t.refillTatForTransfer));
    t.frmTransfer.el.assign_to.select2($.extend({},{
        dropdownParent: t.frmTransfer.el.assign_to.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id : t.frmTransfer.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1,
                    ticket_id : t.frmTransfer.el.id.val(),
                };
            },
        },
        placeholder: "Select User",
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    })).on('change', t.checkTechnicalAvabilityForTransfer);

    t.content.on("click", ".js-act-transfer", $.proxy(t.loadTransfer));
    t.frmTransfer.el.btnSubmit.on("click", $.proxy(t.frmTransferSubmit));
    // end ticket transfer code here

    // add star code start from here
    t.staringUi = function(element,val) {
        var s = (typeof val != "undefined") ? val : t.data.self_star;
        if (s == true) {
            t.config.star_data = true;
            $(element).html(`<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.6854 7.59833C18.6072 7.35812 18.4598 7.1464 18.2615 6.98986C18.0633 6.83331 17.8231 6.73895 17.5713 6.71864L12.9619 6.34677L11.1823 2.04286C11.086 1.80834 10.9222 1.60774 10.7117 1.46656C10.5011 1.32538 10.2533 1.25 9.99983 1.25C9.74633 1.25 9.49856 1.32538 9.28801 1.46656C9.07747 1.60774 8.91366 1.80834 8.81741 2.04286L7.03929 6.34599L2.42757 6.71864C2.17536 6.73997 1.93506 6.83526 1.73677 6.99255C1.53847 7.14985 1.39101 7.36217 1.31285 7.6029C1.23469 7.84364 1.22931 8.10209 1.29738 8.34587C1.36546 8.58965 1.50396 8.80792 1.69554 8.97333L5.21116 12.0069L4.14007 16.5429C4.08016 16.7893 4.09483 17.048 4.18221 17.286C4.26958 17.5241 4.42573 17.7309 4.63082 17.88C4.83591 18.0292 5.0807 18.1141 5.33411 18.1239C5.58752 18.1337 5.83814 18.0679 6.05413 17.935L9.99944 15.5069L13.9471 17.935C14.1632 18.0664 14.4133 18.1308 14.6659 18.1203C14.9185 18.1098 15.1624 18.0247 15.3668 17.8759C15.5712 17.727 15.727 17.521 15.8145 17.2838C15.9021 17.0466 15.9175 16.7888 15.8588 16.5429L14.7838 12.0061L18.2994 8.97255C18.4926 8.80741 18.6323 8.58867 18.701 8.34403C18.7697 8.09939 18.7643 7.83987 18.6854 7.59833Z" fill="currentColor"/></svg>`);
        } else {
            $(element).html(`<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.6854 7.59833C18.6072 7.35812 18.4598 7.1464 18.2615 6.98986C18.0633 6.83331 17.8231 6.73895 17.5713 6.71864L12.9619 6.34677L11.1823 2.04286C11.086 1.80834 10.9222 1.60774 10.7117 1.46656C10.5011 1.32538 10.2533 1.25 9.99983 1.25C9.74633 1.25 9.49856 1.32538 9.28801 1.46656C9.07747 1.60774 8.91366 1.80834 8.81741 2.04286L7.03929 6.34599L2.42757 6.71864C2.17536 6.73997 1.93506 6.83526 1.73677 6.99255C1.53847 7.14985 1.39101 7.36217 1.31285 7.6029C1.23469 7.84364 1.22931 8.10209 1.29738 8.34587C1.36546 8.58965 1.50396 8.80792 1.69554 8.97333L5.21116 12.0069L4.14007 16.5429C4.08016 16.7893 4.09483 17.048 4.18221 17.286C4.26958 17.5241 4.42573 17.7309 4.63082 17.88C4.83591 18.0292 5.0807 18.1141 5.33411 18.1239C5.58752 18.1337 5.83814 18.0679 6.05413 17.935L9.99944 15.5069L13.9471 17.935C14.1632 18.0664 14.4133 18.1308 14.6659 18.1203C14.9185 18.1098 15.1624 18.0247 15.3668 17.8759C15.5712 17.727 15.727 17.521 15.8145 17.2838C15.9021 17.0466 15.9175 16.7888 15.8588 16.5429L14.7838 12.0061L18.2994 8.97255C18.4926 8.80741 18.6323 8.58867 18.701 8.34403C18.7697 8.09939 18.7643 7.83987 18.6854 7.59833ZM17.4869 8.02567L13.6823 11.3069C13.5955 11.3817 13.5309 11.4789 13.4957 11.5879C13.4604 11.6969 13.4558 11.8134 13.4823 11.9249L14.6448 16.8311C14.6478 16.8379 14.6481 16.8456 14.6456 16.8525C14.6431 16.8595 14.6381 16.8653 14.6315 16.8686C14.6174 16.8796 14.6135 16.8772 14.6018 16.8686L10.3268 14.2397C10.2283 14.1792 10.115 14.1472 9.99944 14.1472C9.88387 14.1472 9.77055 14.1792 9.6721 14.2397L5.3971 16.8702C5.38538 16.8772 5.38225 16.8796 5.36741 16.8702C5.36082 16.8668 5.35576 16.8611 5.35329 16.8541C5.35082 16.8471 5.35112 16.8395 5.35413 16.8327L6.51663 11.9265C6.54312 11.815 6.53848 11.6984 6.50321 11.5894C6.46794 11.4805 6.4034 11.3833 6.31663 11.3085L2.51194 8.02724C2.50257 8.01942 2.49397 8.01239 2.50179 7.98817C2.5096 7.96395 2.51585 7.96708 2.52757 7.96552L7.52132 7.56239C7.63586 7.55257 7.74547 7.51134 7.83809 7.44325C7.93072 7.37516 8.00277 7.28285 8.04632 7.17645L9.96976 2.51942C9.976 2.50614 9.97835 2.49989 9.9971 2.49989C10.0158 2.49989 10.0182 2.50614 10.0244 2.51942L11.9526 7.17645C11.9965 7.28289 12.069 7.3751 12.1621 7.44293C12.2551 7.51076 12.3651 7.55156 12.4799 7.56083L17.4737 7.96395C17.4854 7.96395 17.4924 7.96395 17.4994 7.98661C17.5065 8.00927 17.4994 8.01786 17.4869 8.02567Z" fill="currentColor"/></svg>`);
        }
    };

    t.staring = function(star,id,btn) {
        if (t.httpCall != true) {
            return false;
        }
        var id = id;
        var element = btn;
        var star = t.config.translations.add_star;
        var message ='';
        if(t.config.star_data){
            message = t.config.translations.are_you_sure_you_want;
        }else{
            message = t.config.translations.are_you_sure_you_want_to_marked_starred;
        }
        sweetAlertConfirm({
            message: message,
            url: t.config.url.staring,
            data: {
                "_token": t.config.token,
                "id": id
            },
            onSuccess: function(data) {
                t.config.star_data = data.star;
                t.staringUi(element,data.star);
            },
            errorMsg: t.config.translations.somethingWentWrong,
            beforeSend: function() {
                t.httpCall = false;
            },
            complete: function() {
                t.httpCall = true;
            }
        });
    };

    t.content.on("click", ".star-ticket-btn",function(){
        let star = $(this).attr('data-id');
        let id =  $(this).attr('data-id');
        let btn = $(this);
        t.staring(star,id,btn);
    });
    // add start code end for here

    // delete ticket code start from here
    t.frmValidatorDelete = $('#deleteTicketForm').validate({
        ignore: [],
        debug: false,
        rules: {
            'delete-reason': {
                required: true
            }
        },
        errorPlacement: function(error, element) {
            error.addClass('text-danger');
            error.insertAfter(element);
        },
    });

    t.resetDeleteFrm = function(){
        $('#mdlDeleteTicket').find("#delete-reason").summernote('code', '');
        $('#mdlDeleteTicket').find("#shows_error_reason").html(""); 
    }

    $("#btndelsubmit").on("click", function(e) {
        e.preventDefault();
        let s = $("#mdlDeleteTicket").find("#delete-reason").val();
        count = s.replaceAll("&nbsp;", "").trim();
        if(count.length <= 0) {
            $('#mdlDeleteTicket').find('#shows_error_reason').html('This field is required');
            $('#mdlDeleteTicket').find('#shows_error_reason').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
            return false;
        } else {
            $('#mdlDeleteTicket').find('#shows_error_reason').html('');
        }
        if (t.httpCall != true) {
            return false;
        }
        var id = this.id;

        t.httpCall = false;
        var http = $.ajax({
            url: t.config.url.delete,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": $('#ticket_id').val(),
                "note": $('#delete-reason').val()
            }
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    $("#mdlDeleteTicket").modal('hide');
                    setTimeout(function() {
                        t.refreshTicketList();
                    }, 800);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    });

    t.deleteTicket = function(e) {
        t.resetDeleteFrm();
        var ticketId = $('#ticket_id').val($(this).attr('data-id'));
        $("#mdlDeleteTicket").modal('show');
    };

    $("#mdlDeleteTicket").find('#delete-reason').summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.enter_your_message,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags:styleTags,
        minHeight: 120,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
        }
    });
    t.content.on("click", ".delete-btn", $.proxy(t.deleteTicket));
    // delete ticket code end here

    // ticket history code start from here 
    function generateChangeHistoryTable(changeInfo) {
        try {
            let changeData = JSON.parse(changeInfo);
            if (changeData.hasOwnProperty("category") && Array.isArray(changeData.category)) {
                let html = `
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                changeData.category.forEach((category, index) => {
                    let item = changeData.item?.[index] || "N/A";
                    let quantity = changeData.quantity?.[index] || "N/A";
                    let remarks = changeData.remarks?.[index] || "N/A";
                    let image = changeData.item_img?.[index]
                        ? `<a href="#" data-toggle="modal" data-target="#imageModal" data-image="${changeData.item_img[index]}">
                                <img src="${changeData.item_img[index]}" alt="Item Image" width="50" height="50">
                           </a>`
                        : "N/A";

                    html += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${category}</td>
                            <td>${item}</td>
                            <td>${quantity}</td>
                            <td>${remarks}</td>
                        </tr>
                    `;
                });

                html += "</tbody></table>";

                return html;
            } else {
                return "<p>No valid data found</p>";
            }
        } catch (error) {
            console.error("Invalid JSON:", error);
            return "<p>Invalid JSON format</p>";
        }
    }
    t.buildTimelineItem = function(toneClass, ribbonText, ribbonIcon, actionHtml, avatarHtml, commenterName, updatedAt, updatedBy) {
        var userLink = (updatedBy && updatedBy != 0) ? `${t.config.url.user_info}/${updatedBy}`: null;
        return `
            <div class="t-item">

                <div class="t-actor">
                    ${avatarHtml}
                    ${
                        userLink
                            ? `<a href="${userLink}" target="_blank">${escapeHtml(commenterName)}</a>`
                            : `<a>${escapeHtml(commenterName)}</a>`
                    }
                </div>

                <div class="t-rail-col">
                    <span class="rail-cap start"></span>
                    <span class="rail-cap end"></span>
                </div>

                <div class="t-card ${toneClass}">
                    <div class="t-body">

                        <div class="t-time b5-text">
                            <i class="bi bi-clock"></i>
                            ${escapeHtml(updatedAt)}
                        </div>

                        <div class="t-text">
                            <p class="b4-text fw-normal">${actionHtml}</p>
                        </div>

                    </div>

                    <div class="t-ribbon b7-text fw-normal">
                        <i class="${ribbonIcon}"></i>
                        ${ribbonText}
                    </div>

                </div>

            </div>
        `;
    }
    t.ticket_details = function(e) {
        var url = t.config.url.get_tickets;
        var history_url = t.config.url.ticket_history;
        if(t.config.main_filter == "archived") {
            url = t.config.url.get_archived_tickets;
            history_url = t.config.url.ticket_history_archived;
        }
        e.preventDefault();
        
        // Store the ticket ID from the clicked element
        var ticketId = $(this).attr('data-id') || $(this).attr('id');
        
        // Pagination variables
        var currentPage = 1;
        var perPage = 10;
        var isHistoryLoading = false;
        var hasMoreHistory = true;
        var container = $('#ticket_history_container');
        var loader = $('.ticket-history-loader');

        // Clear container and reset
        container.empty();
        loader.removeClass('d-none');
        container.scrollTop(0);

        // First AJAX call for ticket details
        var http = $.ajax({
            url: url,
            type: "POST",
            data: {
                id: ticketId,
                _token: t.config.token
            },
            success: function(data) {
                var sub_cat = data.data.sub_cat == null ? "" : data.data.sub_cat;
                var asset_tag = data.data.asset_tag == null ? "" : data.data.asset_tag;
                
                // Update ticket details in the info card
                $('#ticket_details_subject').text(data.data.subject);
                $('#ticket_details_subject').attr('data-original-title', data.data.subject);
                $('#ticket_details_id').text('#' + data.data.id);
                $('#ticket_details_company').text(data.data.comp_name);
                
                if(asset_tag != '' && asset_tag != null && asset_tag != undefined){
                    $('.related-device-row').show();
                    $('#ticket_details_device').text(asset_tag);
                } else {
                    $('.related-device-row').hide();
                }
                
                $('#ticket_details_category').text(data.data.prob_cat);
                
                if(sub_cat != '' && sub_cat != null && sub_cat != undefined){
                    $('.subcategory-row').show();
                    $('#ticket_details_subcategory').text(sub_cat);
                } else {
                    $('.subcategory-row').hide();
                }
            },
            error: function(xhr, status, error) {
                if(xhr.status === 419) {
                    sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
                }
            }
        });
        
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        
        // Function to load history page
        function loadHistoryPage(page) {
            if (isHistoryLoading) {
                return;
            }
            
            isHistoryLoading = true;
            loader.removeClass('d-none');
            
            var http2 = $.ajax({
                url: history_url,
                type: "POST",
                data: {
                    "_token": t.config.token,
                    "id": ticketId,
                    "page": page,
                    "per_page": perPage
                }
            });
            
            http2.done(function(response) {
                if (typeof response == "object") {
                    if (response.status == "success") {
                        var ticket_history = '';
                        
                        if (response.data.length > 0) {
                            // Update pagination info
                            if (response.pagination) {
                                currentPage = response.pagination.current_page || page;
                                hasMoreHistory = response.pagination.more || false;
                                perPage = response.pagination.per_page || perPage;
                            } else {
                                // Fallback if pagination not returned
                                hasMoreHistory = response.data.length >= perPage;
                                currentPage = page;
                            }
                            
                            $.each(response.data, function(index, value) {
                                var actionHtml = '';
                                // Set commenter name (handle System user)
                                var commenterName = value.commenter == "System" ? "System" : value.commenter;
                                // Get avatar HTML
                                var avatarHtml = t.getAvatar(commenterName, value.commenter_avatar);
                                switch(parseInt(value.action_type)) {
        
                                case 1: // Assigned Ticket
                                    var toneClass = "tone-assigned";
                                    var ribbonText = "Assigned";
                                    var ribbonIcon = "bi bi-person-check-fill";
                                    if (value.assingned_user_fullname != null && value.assingned_user_fullname != '') {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold" ><a href="'+ t.config.url.user_info + '/' + value.assigned_to +'" target="_blank">' + value.assingned_user_fullname +'</a></span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.assigned_to +'" target="_blank">' + value.assingned_user_fullname +'</a></span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_assigned_to +'">' + value.old_assigned_user_fullname +'</a></span>';
                                        }
                                    } else if (value.assigned_to == 0 || value.assigned_to == null) {
                                        if (value.old_assigned_user_fullname == null) {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span>';
                                        } else {
                                            actionHtml = 'Assigned Ticket To <span class="fw-bold">System</span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_assigned_to +'">' + value.old_assigned_user_fullname +'</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Assigned Ticket Updated';
                                    }
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;  
                                case 2: // Status Change
                                    if (value.old_status_name != null && value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold">' + value.old_status_name +'</span> &gt; <span class="fw-bold">' + value.name + '</span>';
                                    } else if (value.name != null) {
                                        actionHtml = 'Status Updated : <span class="fw-bold">' + value.name + '</span>';
                                    } else {
                                        actionHtml = 'Status Updated';
                                    }
                                    var status = (value.name || '').toLowerCase().trim();
                                    switch (status) {
                                        case 'open':
                                            toneClass = 'tone-open';
                                            ribbonText = 'Open';
                                            ribbonIcon = 'bi bi-arrow-repeat';
                                            break;

                                        case 'in progress':
                                        case 'inprogress':
                                            toneClass = 'tone-open';
                                            ribbonText = 'In Progress';
                                            ribbonIcon = 'bi bi-arrow-repeat';
                                            break;

                                        case 'waiting for user':
                                            toneClass = 'tone-waiting';
                                            ribbonText = 'Waiting for User';
                                            ribbonIcon = 'bi bi-person-fill';
                                            break;

                                        case 'hold':
                                        case 'on hold':
                                            toneClass = 'tone-hold';
                                            ribbonText = 'On Hold';
                                            ribbonIcon = 'bi bi-pause-circle';
                                            break;

                                        case 'resolved':
                                            toneClass = 'tone-resolved';
                                            ribbonText = 'Resolved';
                                            ribbonIcon = 'bi bi-check2-square';
                                            break;

                                        case 'closed':
                                            toneClass = 'tone-closed';
                                            ribbonText = 'Closed';
                                            ribbonIcon = 'bi bi-lock-fill';
                                            break;

                                        case 'reopened':
                                        case 're-opened':
                                            toneClass = 'tone-reopen';
                                            ribbonText = 'Reopen';
                                            ribbonIcon = 'bi bi-check-lg';
                                            break;

                                        default:
                                            toneClass = 'tone-open';
                                            ribbonText = value.name || 'Status';
                                            ribbonIcon = 'bi bi-chat-left-text';
                                            break;
                                        }
                                        ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                        break;
                                case 3: // Feedback
                                    if (value.old_feedback != null && value.feedback != null) {
                                        actionHtml = 'Feedback Changed From <span class="fw-bold">' + value.old_feedback +'</span> To <span class="fw-bold">' + value.feedback + '</span>';
                                    } else if (value.feedback != null) {
                                        actionHtml = 'Feedback : <span class="fw-bold">' + value.feedback + '</span>';
                                    } else {
                                        actionHtml = 'Feedback Updated';
                                    }
                                    toneClass = 'tone-feedback';
                                    ribbonText = 'Feedback';
                                    ribbonIcon = 'bi bi-chat-left-text';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 4: // Added to Spam List
                                    actionHtml = 'Ticket Marked As <span class="fw-bold">Spam</span>';
                                    toneClass = 'tone-spam';
                                    ribbonText = 'Spam';
                                    ribbonIcon = 'bi bi-slash-circle-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 5: // Removed from Spam List
                                    actionHtml = 'Ticket Removed From <span class="fw-bold">Spam List</span>';
                                    toneClass = 'tone-success';
                                    ribbonText = 'Spam Removed';
                                    ribbonIcon = 'bi bi-shield-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 6: // Ticket Merge
                                    if (value.merge_primary != null) {
                                        actionHtml = 'Ticket Merged With <span class="fw-bold">' + value.merge_primary + '</span> ticket';
                                    } else if (value.merged_ids != null) {
                                        actionHtml = 'Primary Ticket Merged With <span class="fw-bold">' + value.merged_ids + '</span> tickets';
                                    } else {
                                        actionHtml = 'Ticket Merge Operation Performed';
                                    }

                                    toneClass = 'tone-merge';
                                    ribbonText = 'Merged';
                                    ribbonIcon = 'bi bi-diagram-3-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 7: // Add Comment
                                    if (value.is_note == 1) {
                                        actionHtml = 'Marked this as <span class="fw-bold">Note</span> and commented on ticket';
                                        toneClass = 'tone-note';
                                        ribbonText = 'Note';
                                        ribbonIcon = 'bi bi-stickies';
                                    } else if (value.auto_response == 1) {
                                        actionHtml = 'Auto responded by <span class="fw-bold">MATI-AI</span>';
                                        toneClass = 'tone-ai';
                                        ribbonText = 'AI Reply';
                                        ribbonIcon = 'bi bi-robot';
                                    } else {
                                        actionHtml = 'Commented on ticket';
                                        toneClass = 'tone-comment';
                                        ribbonText = 'Comment';
                                        ribbonIcon = 'bi bi-chat-left-text';
                                    }
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 8: // Changes Priority
                                    if (value.old_priority_name != null && value.priority_name != null) {
                                        actionHtml = 'Priority Changed From <span class="fw-bold">' + value.old_priority_name +'</span> To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else if (value.priority_name != null) {
                                        actionHtml = 'Priority Changed To <span class="fw-bold">' + value.priority_name + '</span>';
                                    } else {
                                        actionHtml = 'Priority Updated';
                                    }
                                    toneClass = 'tone-priority';
                                    ribbonText = 'Priority';
                                    ribbonIcon = 'bi bi-flag';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 9: // Changes TAT
                                    if (value.tat_changed != null && value.tat != null) {
                                        actionHtml = 'Changed TAT From <span class="fw-bold">' + value.tat_changed +' hours</span> To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else if (value.tat != null) {
                                        actionHtml = 'Changed TAT To <span class="fw-bold">' + value.tat + ' hours</span>';
                                    } else {
                                        actionHtml = 'TAT Updated';
                                    }
                                    toneClass = 'tone-tat';
                                    ribbonText = 'TAT';
                                    ribbonIcon = 'bi bi-clock';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 10: // Ticket Transfer Department
                                    if (value.old_dept_name != null && value.dept_name != null) {
                                        actionHtml = 'Department Changed From <span class="fw-bold">' + value.old_dept_name +'</span> To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else if (value.dept_name != null) {
                                        actionHtml = 'Department Changed To <span class="fw-bold">' + value.dept_name + '</span>';
                                    } else {
                                        actionHtml = 'Department Transferred';
                                    }
                                    toneClass = 'tone-department';
                                    ribbonText = 'Department';
                                    ribbonIcon = 'bi bi-building';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 11: // Ticket Transfer Category
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed From <span class="fw-bold">' + value.old_pbm_cat_name +'</span> To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else if (value.pbm_cat_name != null) {
                                        actionHtml = 'Category Changed To <span class="fw-bold">' + value.pbm_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Category Transferred';
                                    }
                                    toneClass = 'tone-category';
                                    ribbonText = 'Category';
                                    ribbonIcon = 'bi bi-folder2-open';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 12: // Ticket Transfer Sub Category
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed From <span class="fw-bold">' + value.old_sub_cat_name +'</span> To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else if (value.sub_cat_name != null) {
                                        actionHtml = 'Sub Category Changed To <span class="fw-bold">' + value.sub_cat_name + '</span>';
                                    } else {
                                        actionHtml = 'Sub Category Transferred';
                                    }
                                    toneClass = 'tone-subcategory';
                                    ribbonText = 'Sub Category';
                                    ribbonIcon = 'bi bi-diagram-2-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 14: // New Ticket History
                                    if (value.old_change_creator_user_fullname != null && value.updated_by != value.old_change_creator_id) {
                                        actionHtml = 'New ticket created for <span class="fw-bold"><a href="' + t.config.url.user_info + '/' + value.old_change_creator_id +'" target="_blank">' + value.old_change_creator_user_fullname +'</a></span>';
                                    } else {
                                        actionHtml = 'New ticket created';
                                    }
                                    toneClass = 'tone-newticket';
                                    ribbonText = 'New Ticket';
                                    ribbonIcon = 'bi bi-ticket-perforated-fill';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 15: // Ticket Type Update
                                    actionHtml = value.ticket_type_custom_fields || 'Ticket Type Updated';
                                    toneClass = 'tone-tickettype';
                                    ribbonText = 'Ticket Type';
                                    ribbonIcon = 'bi bi-list-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 16: // Ticket Creator Change
                                    if (value.creator_user_fullname != null) {
                                        if (value.old_change_creator_user_fullname != null) {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.change_creator_id +'" target="_blank">' +value.creator_user_fullname +'</a></span> From <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.old_change_creator_id +'">' +value.old_change_creator_user_fullname +'</a></span>';
                                        } else {
                                            actionHtml = 'Creator Changed To <span class="fw-bold"><a href="' +t.config.url.user_info + '/' + value.change_creator_id +'" target="_blank">' +value.creator_user_fullname +'</a></span>';
                                        }
                                    } else {
                                        actionHtml = 'Ticket Creator Changed';
                                    }
                                    toneClass = 'tone-creator';
                                    ribbonText = 'Creator';
                                    ribbonIcon = 'bi bi-person-fill-gear';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 17: // Custom Field Update
                                    actionHtml = value.custom_fields || 'Custom Field Updated';
                                    toneClass = 'tone-custom';
                                    ribbonText = 'Custom Field';
                                    ribbonIcon = 'bi bi-sliders';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 18: // Calendar Event
                                    actionHtml = `<span class="meetingDetails" data-id="${value.event_id}">Event calendar booked <i class="bi bi-calendar"></i></span>`;
                                    toneClass = 'tone-calendar';
                                    ribbonText = 'Calendar';
                                    ribbonIcon = 'bi bi-calendar2-day';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 19: // Device Change
                                    if (value.device_tag != null) {
                                        if (value.old_device_tag != null) {
                                            actionHtml = `Device Changed To <span class="fw-bold"><a href="${t.config.url.device_info}/${value.device_id}" target="_blank">${value.device_tag}</a></span>From<span class="fw-bold"><a href="${t.config.url.device_info}/${value.old_device_id}">${value.old_device_tag}</a></span>`;
                                        } else {
                                            actionHtml = `Device Changed To <span class="fw-bold"><a href="${t.config.url.device_info}/${value.device_id}" target="_blank">${value.device_tag}</a></span>`;
                                        }
                                    } else {
                                        actionHtml = 'Device Updated';
                                    }
                                    toneClass = 'tone-device';
                                    ribbonText = 'Device';
                                    ribbonIcon = 'bi bi-laptop';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 20: // Custom Form History
                                    if (value.new_custom_field_values != null) {
                                        if (value.old_custom_field_values == null) {
                                            actionHtml ='Form Data Changed<br>' + generateChangeHistoryTable(value.new_custom_field_values);
                                        } else {
                                            actionHtml ='Form Data Changed<br>' +generateChangeHistoryTable(value.new_custom_field_values) +'<br><span class="fw-bold">From</span><br>' +generateChangeHistoryTable(value.old_custom_field_values);
                                        }
                                    } else {
                                        actionHtml = 'Form Data Updated';
                                    }
                                    toneClass = 'tone-custom';
                                    ribbonText = 'Form';
                                    ribbonIcon = 'bi bi-file-earmark-text';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 21: // Custom Remove History
                                    if (value.new_custom_field_values != null) {
                                        if (value.old_custom_field_values == null) {
                                            actionHtml ='Item Removed<br>' + generateChangeHistoryTable(value.new_custom_field_values);
                                        } else {
                                            actionHtml ='Item Removed<br>' + generateChangeHistoryTable(value.new_custom_field_values) +'<br><span class="fw-bold">From</span><br>' + generateChangeHistoryTable(value.old_custom_field_values);
                                        }
                                    } else {
                                        actionHtml = 'Item Removed';
                                    }
                                    toneClass = 'tone-danger';
                                    ribbonText = 'Removed';
                                    ribbonIcon = 'bi bi-trash';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 22: // Task Operation
                                    if (value.remarks != null) {
                                        const parts = value.remarks.split(' ');
                                        const taskId = parts.shift().replace('#', '');
                                        const message = parts.join(' ');
                                        actionHtml = `<span class="fw-bold">#${taskId}</span> ${message}`;
                                    } else {
                                        actionHtml = 'Task Updated';
                                    }
                                    toneClass = 'tone-task';
                                    ribbonText = 'Task';
                                    ribbonIcon = 'bi bi-list-check';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 23: // Edit Category
                                    if (value.old_pbm_cat_name != null && value.pbm_cat_name != null) {
                                        actionHtml ='Category Changed From <span class="fw-bold">' +value.old_pbm_cat_name +'</span> To <span class="fw-bold">' +value.pbm_cat_name +'</span>';
                                    } else {
                                        actionHtml ='Category Changed To <span class="fw-bold">' +value.pbm_cat_name +'</span>';
                                    }
                                    toneClass = 'tone-category';
                                    ribbonText = 'Category';
                                    ribbonIcon = 'bi bi-folder2-open';

                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                case 24: // Edit Sub Category
                                    if (value.old_sub_cat_name != null && value.sub_cat_name != null) {
                                        actionHtml ='Sub Category Changed From <span class="fw-bold">' +value.old_sub_cat_name +'</span> To <span class="fw-bold">' +value.sub_cat_name +'</span>';
                                    } else {
                                        actionHtml ='Sub Category Changed To <span class="fw-bold">' +value.sub_cat_name +'</span>';
                                    }

                                    toneClass = 'tone-category';
                                    ribbonText = 'Sub Category';
                                    ribbonIcon = 'bi bi-diagram-3';
                                    ticket_history += t.buildTimelineItem(toneClass,ribbonText,ribbonIcon,actionHtml,avatarHtml,commenterName,value.updated_at_format,value.updated_by);
                                    break;
                                default:
                                        break;
                                }
                            });
                        } else {
                            if (page === 1) {
                                ticket_history = '<div class="text-center p-4"><p>' + (config.translations.Ticket_History_Not_Available || 'No history available') + '</p></div>';
                            } else {
                                hasMoreHistory = false;
                                ticket_history = '';
                            }
                        }
                        
                        if (page === 1) {
                            container.html(ticket_history);
                        } else {
                            container.append(ticket_history);
                        }
                        
                        loader.addClass('hide');
                        $('#ticketHistoryModal').modal("show");
                    }
                }
            });
            
            http2.fail(function(xhr, status, error) {
                if(xhr.status === 419) {
                    sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
                } else {
                    sweetAlert('center', 'error', {msg: config.translations.something_went_wrong || 'Something went wrong'});
                }
                loader.addClass('hide');
            });
            
            http2.always(function() {
                isHistoryLoading = false;
                t.httpCall = true;
            });
        }

        // Load first page
      loadHistoryPage(1);

        // Scroll event for infinite scroll / load more
        container.off('scroll.ticketHistory').on('scroll.ticketHistory', function () {
            if (isHistoryLoading || !hasMoreHistory) {
                return;
            }

            if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 20) {
                loadHistoryPage(currentPage + 1);
            }
        });
    };

    // Helper function to get avatar
    t.getAvatar = function(commenter, avatar) {
        // Check if it's System user
        if (commenter === "System" || commenter === "system") {
            return '<img src="' + t.config.url.base_url + '/assets/mati.png" alt="System">';
        }
        
        // For other users
        if (avatar && avatar !== '' && avatar !== null) {
            // If avatar exists in database
            return '<img src="' + t.config.url.base_url + '/storage/avatar/' + avatar + '" alt="' + escapeHtml(commenter) + '">';
        } else {
            // Otherwise show initials
            var name = commenter || 'User';
            var initials = name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 1);
            return '<span class="tkt-hst-avatar-initials" style="display: inline-flex; align-items: center; justify-content: center; width: 15px; height: 15px; border-radius: 50%; background: linear-gradient(135deg, #a78bfa, #7c3aed); color: white; font-size: 10px; font-weight: 600; text-transform: uppercase;">' + initials + '</span>';
        }
    }


    // Escape HTML function to prevent XSS
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    t.content.on("click", ".history-ticket-btn", $.proxy(t.ticket_details));
    // ticket history end here

    // ticket sentiment analysis start from here
    t.mdl_ticketsentiment = t.content.find("#ticketSentiment");
    t.mdlTicketsentiment_btnClose = t.mdl_ticketsentiment.find(".btn-close");
    t.mdl_ticketsentiment.sentimentText = t.mdl_ticketsentiment.find("#sentimentText");
    t.mdl_ticketsentiment.sentimentIcon = t.mdl_ticketsentiment.find("#sentimentIcon");
    t.mdl_ticketsentiment.explanationText = t.mdl_ticketsentiment.find("#explanationText");
    t.mdl_ticketsentiment.sentimentScore = t.mdl_ticketsentiment.find("#sentimentScore");
    t.mdl_ticketsentiment.sentimentIndicator = t.mdl_ticketsentiment.find("#sentimentIndicator");
    t.mdl_ticketsentiment.scoreValue = t.mdl_ticketsentiment.find("#scoreValue");
    t.mdl_ticketsentiment.scoreBadge = t.mdl_ticketsentiment.find("#scoreBadge");
    t.mdl_ticketsentiment.footerStatus = t.mdl_ticketsentiment.find("#footerStatus");
    t.mdl_ticketsentiment.takenAction = t.mdl_ticketsentiment.find("#takenAction");
    t.mdl_ticketsentiment.sentimentSubText = t.mdl_ticketsentiment.find("#sentimentSubText");

    t.Ticketsentiment = function (e) {
        e.preventDefault();
        var ticket_id = $(this).attr('data-id');
        $.ajax({
            type: 'GET',
            url: t.config.url.getSentiment + '/' + ticket_id,
            success: function (res) {
                if (res.status == 'danger') {
                    sweetAlert('center', 'error', res);
                    return;
                }
                if (res.status == 'success' && res.data.length > 0) {
                    var data = res.data[0];
                    let icon = '😐';
                    let subText = 'No clear sentiment was detected from the customer message.';
                    let title = 'Neutral Sentiment Detected';
                    let badge = 'Neutral';
                    let footer = 'Average';
                    let tone = 'Normal Conversation';
                    let score = parseFloat(data.sentiment_score);
                    if (data.ticket_sentiment == 1) {
                        icon = '😊';
                        subText = 'Customer message reflects positive engagement and satisfaction.';
                        title = 'Positive Sentiment Detected';
                        badge = 'Strong Positive';
                        footer = 'Very Favorable';
                        tone = 'Friendly • Appreciative • Positive';
                    } else if (data.ticket_sentiment == 2) {
                        icon = '😐';
                        title = 'Neutral Sentiment Detected';
                        badge = 'Neutral';
                        footer = 'Balanced';
                        tone = 'Neutral • Informative';
                        subText = 'Customer communication appears balanced and informational.';
                    } else if (data.ticket_sentiment == 3) {
                        icon = '😠';
                        title = 'Negative Sentiment Detected';
                        badge = 'Strong Negative';
                        footer = 'Very Unfavorable';
                        tone = 'Hostile • Frustrated • Complaint';
                        subText = 'Customer message reflects frustration and dissatisfaction.';
                    }
                    // progress indicator position
                    score = Math.max(-1, Math.min(1, score));
                    const position = ((score + 1) / 2) * 100;
                    t.mdl_ticketsentiment.sentimentIndicator.css('left', position + '%');
                    // set data
                    t.mdl_ticketsentiment.sentimentIcon.text(icon);
                    t.mdl_ticketsentiment.sentimentText.text(title);
                    t.mdl_ticketsentiment.explanationText.text(data.sentiment_reson);
                    t.mdl_ticketsentiment.sentimentScore.text('Score : ' + score );
                    t.mdl_ticketsentiment.scoreValue.text(score);
                    t.mdl_ticketsentiment.scoreBadge.text(badge);
                    t.mdl_ticketsentiment.footerStatus.text(footer);
                    t.mdl_ticketsentiment.takenAction.text(tone);
                    t.mdl_ticketsentiment.sentimentSubText.text(subText);
                    // dynamic colors
                    t.mdl_ticketsentiment.scoreBadge.removeClass('positive neutral negative');
                    if (data.ticket_sentiment == 1) {
                        t.mdl_ticketsentiment.scoreBadge.addClass('positive');
                    } else if (data.ticket_sentiment == 2) {
                        t.mdl_ticketsentiment.scoreBadge.addClass('neutral');
                    } else {
                        t.mdl_ticketsentiment.scoreBadge.addClass('negative');
                    }
                    t.mdl_ticketsentiment.modal("show");
                }
            }
        });
    };
    t.mdlTicketsentiment_btnClose.on('click', function () {
        t.mdl_ticketsentiment.modal("hide");
    });
    t.content.on("click", ".sos", $.proxy(t.Ticketsentiment));
    // ticket sentiment analysis end here


    // ticket status update list code start from here
    t.frmUpdateStatus = t.content.find("#frm_update_status");
    t.frmUpdateStatus.el = {};
    t.frmUpdateStatus.el.id = t.frmUpdateStatus.find("#id");
    t.frmUpdateStatus.el.ticketId = t.frmUpdateStatus.find("#ticket_id");
    t.frmUpdateStatus.el.statusId = t.frmUpdateStatus.find("#update_status_id");
    t.frmUpdateStatus.el.priorityId = t.frmUpdateStatus.find("#update_priority_id");
    t.frmUpdateStatus.el.tat = t.frmUpdateStatus.find("#tat");
    t.frmUpdateStatus.el.comment = t.frmUpdateStatus.find("#comment");
    t.frmUpdateStatus.el.follow_cc = t.frmUpdateStatus.find("#follow_cc");
    t.frmUpdateStatus.el.cc_emails = t.frmUpdateStatus.find('#us_cc_emails');
    t.frmUpdateStatus.el.tmp_id = t.frmUpdateStatus.find("#tmp_id");
    t.frmUpdateStatus.attachment_dropper_cover = t.frmUpdateStatus.find('#update-dropper-cover');
    t.frmUpdateStatus.attachment_dropper = t.frmUpdateStatus.attachment_dropper_cover.find('#update-dropper');
    t.attachment_update = t.frmUpdateStatus.find("#attachment_updates");
    t.frmUpdateStatus.btnSubmit = t.frmUpdateStatus.find("#btnSubmit");
    t.frmUpdateStatus.el.revoke_access_div = t.frmUpdateStatus.find("#revoke_access_div");
    t.frmUpdateStatus.el.need_time_duration = t.frmUpdateStatus.find("#need_time_duration");
    t.frmUpdateStatus.el.start_time = t.frmUpdateStatus.find("#start_time");
    t.frmUpdateStatus.el.end_time = t.frmUpdateStatus.find("#end_time");
    t.frmUpdateStatus.ticketData = {};
    t.frmUpdateStatus.checkRevoke = 0;
    t.frmUpdateStatus.commentWrapper = t.frmUpdateStatus.find('.final-comment-wrapper');

    t.mdlServiceRequest = t.content.find('#mdl-servicerequest');
    t.frmServiceRequest = t.mdlServiceRequest.find('#requested_form');
    t.frmServiceRequest.el = {};
    t.frmServiceRequest.el.form_title = t.frmServiceRequest.find(".form_title");
    t.frmServiceRequest.el.form_id = t.frmServiceRequest.find("#form_id");
    t.frmServiceRequest.el.for_action = t.frmServiceRequest.find("#for_action");
    t.frmServiceRequest.el.request_id = t.frmServiceRequest.find("#request_id");
    t.frmServiceRequest.el.tmp_id = t.frmServiceRequest.find("#tmp_id");
    t.frmServiceRequest.el.field_values = t.frmServiceRequest.find('#field_values');
    t.frmServiceRequest.btnSubmit = t.mdlServiceRequest.find("#btnSubmit");
    t.frmServiceRequest.el.field_form_required = t.frmServiceRequest.find('#field_form_required');
    
    // status serviceRequestForm Fields
    t.mdlStatusServiceRequest = t.content.find('#mdl-status-servicerequest');
    t.frmStatusServiceRequest = t.mdlStatusServiceRequest.find('#status_requested_form');
    t.frmStatusServiceRequest.el = {};
    t.frmStatusServiceRequest.el.form_id = t.frmStatusServiceRequest.find("#form_id");
    t.frmStatusServiceRequest.el.status_id = t.frmStatusServiceRequest.find("#status_id");
    t.frmStatusServiceRequest.el.ticket_id = t.frmStatusServiceRequest.find("#ticket_id");
    t.mdlStatusServiceRequest.btnSubmit = t.mdlStatusServiceRequest.find("#Status_saveData");
    t.mdlStatusServiceRequest.title = t.mdlStatusServiceRequest.find(".modal-title");
    t.frmStatusServiceRequest.el.field_values = t.frmStatusServiceRequest.find('#field_values');
    t.frmStatusServiceRequest.btnSubmit = t.mdlStatusServiceRequest.find("#btnSubmit");

    t.frmUpdateStatus.el.comment.summernote({
        inheritPlaceholder: true,
        placeholder: t.config.translations.enter_your_message,
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4'],
        minHeight: 120,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .css('width', '100%')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
            }
        }
    });

    t.frmCommentTokenize = function() {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.frmUpdateStatus.el.tmp_id.val(v);
        t.frmServiceRequest.el.tmp_id.val(v);
    };
    t.frmCommentTokenize();
    t.initUpdateForm = function(response) {
        t.frmUpdateStatus.el.id.val(t.frmUpdateStatus.ticketData.id);
        t.frmUpdateStatus.el.ticketId.val(t.frmUpdateStatus.ticketData.id);
        if (t.frmUpdateStatus.ticketData.status_id) {
            const option = new Option(t.frmUpdateStatus.ticketData.status,t.frmUpdateStatus.ticketData.status_id,true,true);
            $(option).attr('form', t.frmUpdateStatus.ticketData.status_form || 0);
            t.frmUpdateStatus.el.statusId.append(option).trigger('change');
        }
        t.frmUpdateStatus.el.priorityId.empty();
        $.each(t.config.priorities, function(i, v) {
            t.frmUpdateStatus.el.priorityId.append(new Option(v.name, v.id));
        });
        if (typeof response != "undefined") {
            t.frmUpdateStatus.el.priorityId.val(t.frmUpdateStatus.ticketData.priority_id);
            t.frmUpdateStatus.el.tat.val(t.frmUpdateStatus.ticketData.tat);
        }
        t.frmUpdateStatus.el.priorityId.trigger("change");
        var ticekt_cc_email = t.frmUpdateStatus.ticketData.cc_emails ? t.frmUpdateStatus.ticketData.cc_emails.split(',') : null;
        if (t.frmUpdateStatus.ticketData != "" || t.frmUpdateStatus.ticketData != null) {
            t.frmUpdateStatus.el.cc_emails.empty();
            $.each(ticekt_cc_email, function(i, v) {
                var op = new Option(v, v, true, true);
                t.frmUpdateStatus.el.cc_emails.append(op).trigger("change");
            });
        }
        if (t.frmUpdateStatus.el.priorityId.prop("disabled")) {
            let val = t.frmUpdateStatus.el.priorityId.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'priority_id',
                value: val
            }).appendTo(t.frmUpdateStatus);
        }
        if (  t.frmUpdateStatus.el.tat.prop("disabled")) {
            let tatVal =  t.frmUpdateStatus.el.tat.val();
            $('<input>').attr({
                type: 'hidden',
                name: 'tat',
                value: tatVal
            }).appendTo(t.frmUpdateStatus);
        }
        // t.frmUpdateStatus.el.follow_cc.prop('checked', false);
        t.data.id = t.frmUpdateStatus.ticketData.id;
    };
    t.frmUpdateStatus.el.priorityId.on("select2:select", function (e) {
        var data = e.params.data;
        var selectedId = parseInt(data.id, 10);

        var tatObj = t.config.priorities.find(function(item) {
            return item.id === selectedId;
        });

        if (tatObj) {
            t.frmUpdateStatus.el.tat.val(tatObj.service_time);
        }
    });

    t.updateCCField = function() {
        var cc_emails = typeof t.data != "undefined" && typeof t.data.cc_emails != "undefined" ? t.data.cc_emails : "";
        t.frmUpdateStatus.el.cc_emails.val(cc_emails);
    };
    t.frmUpdateStatusValidator = t.frmUpdateStatus.validate({
        onsubmit: false,
        rules: {
            status_id: {
                required: true,
                str_name: true
            },
            priority_id: {
                required: true,
                str_name: true
            },
            tat: {
                required: true,
                digits: true,
                min: 1,
                max: 1000
            },
            cc_emails: {
                multipleemailaddress: true
            },
        },
        messages: {
            cc_emails: {
                multipleemailaddress: "Please enter a valid E-mail address"
            }
        }
    });

    t.frmUpdateStatus.el.cc_emails.select2({
        width: "100%",
        placeholder: "Add CC",
        dropdownParent: t.frmUpdateStatus.el.cc_emails.parent(),
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getUserCCByAjax;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });

    t.updateTicket = function(e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        var http = $.ajax({
            url: t.config.url.get_tickets,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": id
            },
            success:function(data) {
                t.frmUpdateStatus.ticketData = data.data;
                if(config.client == "ltts") {
                    setCustomFieldForm(data.data.customFieldSet, data.data.customFields, data.data.pcName, id, data.data.custom_field, t.ticket_type_fields_div, t.config.url.getLocationByAjax, t.config.url.getUserByAjax, t.config.url.getTicketTypeFieldsets, t.custom_field_data_section, t.config.client, null, t.config.url.getDayForEndDate, data.data.category);
                        t.frmUpdateStatus.checkRevoke = data.data.category;
                        if(data.data.revoke_access_at != null && data.data.revoke_access_at != '') {
                            $('#revoke_access').prop('checked', true);
                        } else {
                            $('#revoke_access').prop('checked', false);
                        }
                }
                t.frmUpdateStatus.trigger("reset");
                $("#ticket_status_form_id").val('');
                $("#set_edit_status_form").html(``);
                t.frmUpdateStatus.el.cc_emails.val("").trigger("change").attr('disabled', false);
                $('#update_details').find("#attachments").empty();
                // Apply edit action controls for priority and TAT
                let updateActionControls = t.frmUpdateStatus.ticketData.update_ticket_action_controls;
                if (t.frmUpdateStatus.ticketData.update_ticket_action_controls?.ctrl_tat == 1) {
                    t.frmUpdateStatus.el.tat.prop('disabled', false);
                } else {
                    t.frmUpdateStatus.el.tat.prop('disabled', true);
                }
                if (t.frmUpdateStatus.ticketData.update_ticket_action_controls?.ctrl_priority == 1) {
                    t.frmUpdateStatus.el.priorityId.prop('disabled', false);
                } else {
                    t.frmUpdateStatus.el.priorityId.prop('disabled', true);
                }
                t.initUpdateForm(true);
                $('#update_details').modal('show');
                if(t.config.tkt_config.checked_cc_checkbox == 1){
                    $('#update_details').find('#follow_cc').prop('checked', true);
                }
                t.frmUpdateStatus.el.comment.summernote('code', '');
            }
        });
    };

    t.checkRevokeAccess = function () {
        if ((config.client == "ltts" || config.client == "rolepermission" || config.client == "grdemo") && t.frmUpdateStatus.checkRevoke != 0 && t.frmUpdateStatus.checkRevoke > 0) {
            if ($(this).val() == 5) {
                t.frmUpdateStatus.el.revoke_access_div.removeClass('hide');
            } else {
                t.frmUpdateStatus.el.revoke_access_div.addClass('hide');
                $('#revoke_access').prop('checked', false);
            }
        }
    }
    
    t.loadRequestedForm = function(fields) {
        $("#requested_form .modal-body").css("background", "none");
        $("#mdl-servicerequest").find(".modal-dialog").removeClass("custom-modal-xl");
        $("#mdl-servicerequest").find("#footer_button").removeClass("hide");
        t.frmServiceRequest.el.field_form_required.val(1);
        var test = fields;
        var fbTemplate = document.getElementById('build-wrap'),
            $fbEditor = $(document.getElementById('fb-editor')),
            $formContainer = $(document.getElementById('field_values')),
            $editContainers = $(document.getElementById('fb-rendered-form')),

            fbOptions = {
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            },
            options = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                },
                allowStageSort: false,
                showActionButtons: false,
                stickyControls: false,
                disabledFieldButtons: {
                    autocomplete: ['remove','edit','copy'],
                    text: ['remove','edit','copy'],
                    select:  ['remove','edit','copy'],
                    textarea: ['remove','edit','copy'],
                    paragraph: ['remove','edit','copy'],
                    number: ['remove','edit','copy'],
                    button: ['remove','edit','copy'],
                    date: ['remove','edit','copy'],
                    file: ['remove','edit','copy'],
                    header: ['remove','edit','copy'],
                    hidden: ['remove','edit','copy'],
                    'radio-group': ['remove','edit','copy'],
                    'checkbox-group': ['remove','edit','copy'],
                },
            },
            options2 = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            };
        $('#requested_form #build-wrap').empty();
        $(fbTemplate).formRender(options);
        setupDependsOn(document.getElementById('requested_form'));

        document.getElementById("saveData").addEventListener("click", () => {
            var outputHtml = $(fbTemplate).formRender("userData");
            $formContainer.val(JSON.stringify(outputHtml));
        });

        setTimeout( function() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            console.log("datepicker reload");
            var countrySelect2 = ['select-1706852583269-0', 'select-1707208866628-0'];
            $.each(countrySelect2, function(i,v){
                if($("#"+v).length > 0) {
                    $("#"+v).select2({
                        width : '100%',
                        placeholder: "Countries",
                        ajax: {
                            url: function (params) {
                                return t.config.url.countries;
                            },
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "GET",
                            data: function (params) {
                                return {
                                    q: params.term,
                                    page: params.page,
                                    customField : true,
                                }
                            },
                            processResults: function (data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.results,
                                    pagination: {
                                        more: (params.page * 30) < data.total
                                    }
                                }
                            },
                            cache: true
                        }
                    });
                }
            });
        }, 3000);
    }

    t.loadStatusRequestedForm = function(fields) {
        var test = fields;
        var fbTemplate = document.getElementById('status-build-wrap'),
            $formContainer = $(document.getElementById('status_field_values')),

            fbOptions = {
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            },
            options = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                },
                allowStageSort: false,
                showActionButtons: false,
                stickyControls: false,
                disabledFieldButtons: {
                    autocomplete: ['remove','edit','copy'],
                    text: ['remove','edit','copy'],
                    select:  ['remove','edit','copy'],
                    textarea: ['remove','edit','copy'],
                    paragraph: ['remove','edit','copy'],
                    number: ['remove','edit','copy'],
                    button: ['remove','edit','copy'],
                    date: ['remove','edit','copy'],
                    file: ['remove','edit','copy'],
                    header: ['remove','edit','copy'],
                    hidden: ['remove','edit','copy'],
                    'radio-group': ['remove','edit','copy'],
                    'checkbox-group': ['remove','edit','copy'],
                },
            },
            options2 = {
                formData: test,
                onSave: function() {
                    $formContainer.val(formBuilder.formData);
                }
            };
        $('#status_requested_form #status-build-wrap').empty();
        $(fbTemplate).formRender(options);
        $('#status-build-wrap .form-group').css('width', '100%');
        setupDependsOn(document.getElementById('status_requested_form'));

        document.getElementById("Status_saveData").addEventListener("click", () => {
            var outputHtml = $(fbTemplate).formRender("userData");
            $formContainer.val(JSON.stringify(outputHtml));
        });

        setTimeout( function() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' + dd;
            // $('input[type=date]').attr('min', today);
        }, 3000);
    }

    // check problem category for ticket status form
    t.getStatusFormIdViaTicketProblemCategory = function(){
        var ticket_id = t.frmUpdateStatus.ticketData.id;
        return new Promise(function(resolve, reject) {
            $.ajax({
                type: "GET",
                url: config.url.getFormByTicketProblemCategory,
                data: {
                    'ticket_id': ticket_id,
                    'status_id': t.frmUpdateStatus.el.statusId.val(),
                },
                success: function (response) {
                    resolve(response.form_id);
                    $("#ticket_status_form_id").val('');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    reject(errorThrown);
                    $("#ticket_status_form_id").val('');
                }
            });
        });
    }
    t.loadRequestForm_onStatus = function() {
        if (t.frmUpdateStatus.el.statusId.find(':selected').attr('form') != undefined && t.frmUpdateStatus.el.statusId.find(':selected').attr('form') != 0) {
            var status_form = t.frmUpdateStatus.el.statusId.find('option:selected').attr('form');
            var status_id = t.frmUpdateStatus.el.statusId.find('option:selected').attr('value');
            if (status_form !== 0) {
                t.getStatusFormIdViaTicketProblemCategory().then(function(form_id) {
                    if (form_id > 0 && form_id != null) {
                        $.ajax({
                            url: t.config.url.service_request_form + "/" + form_id,
                            method: 'GET',
                            success: function(result) {
                                if (typeof result === "object") {
                                    if (result.status !== "success") {
                                        sweetAlert('center', 'error', result);
                                        return;
                                    }
                                    t.frmStatusServiceRequest.el.form_id.val(form_id);
                                    t.mdlStatusServiceRequest.btnSubmit.text("Save");
                                    var form_name = result.data.form_name ? result.data.form_name : "Service Request Form";
                                    t.mdlStatusServiceRequest.title.html(form_name);
                                    t.frmStatusServiceRequest.el.status_id.val(status_id);
                                    t.frmStatusServiceRequest.el.ticket_id.val(t.frmUpdateStatus.ticketData.id);
                                    t.loadStatusRequestedForm(result.data.fields);
                                    t.frmStatusServiceRequest.el.field_values.val(result.data.fields);
                                    t.mdlStatusServiceRequest.modal("show");
                                } else {
                                    var data = {
                                        'msg': 'Unable to load form.',
                                    };
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                var data = {
                                    'msg': 'Error occurred: ' + textStatus + ' - ' + errorThrown,
                                };
                                sweetAlert('center', 'error', data);
                            }
                        });
                    } else {
                        $("#ticket_status_form_id").val('not_found');
                    }
                }).catch(function(error) {
                    console.error('Error fetching form ID:', error);
                });
            }
        } else {
            $("#ticket_status_form_id").val('');
        }
    };

    t.frmUpdateStatusReset = function(){
        t.frmUpdateStatus.el.need_time_duration.addClass('hide');
        t.frmUpdateStatus.el.comment.val('').summernote('code', '');
        t.frmUpdateStatus.el.end_time.val("");
        t.frmUpdateStatus.el.start_time.val("");
        t.frmUpdateStatus.el.end_time.rules("remove","required");
        t.frmUpdateStatus.el.start_time.rules("remove","required");
    }

    t.loadCheckStatusApproval = function(){
        var status_id = t.frmUpdateStatus.el.statusId.find('option:selected').attr('value');
        let ticket_id = t.frmUpdateStatus.ticketData.id;
        $.ajax({
            type: "get",
            url: t.config.url.getStatusApproval,
            data: {
                'status_id':status_id,
                'ticket_id':ticket_id,
            },
            beforeSend: function () {
                t.frmUpdateStatus.btnSubmit.attr("disabled", true);
                t.frmUpdateStatusReset();
            },
            success: function (res) {
                if(res.approval_required && res.need_time_duration){
                    t.frmUpdateStatus.el.need_time_duration.removeClass('hide');
                    t.frmUpdateStatus.el.end_time.rules("add",{ required:true });
                    t.frmUpdateStatus.el.start_time.rules("add",{ required:true });
                }
            },
            complete: function () {
                t.frmUpdateStatus.btnSubmit.attr("disabled", false);
            }
        });
    }
    
    t.statusIdChanged = function(e) {
        var status = parseInt($(this).val());
        t.frmUpdateStatus.ticketData;
        try {
            if ((t.frmUpdateStatus.ticketData.status_id != status && (status == 2 || status == 5)) || (typeof t.config.tkt_config != null && t.config.tkt_config.mail_all_status_changes == 1)) {
                t.frmUpdateStatus.el.comment.rules("add", { required: true });
                t.frmUpdateStatus.commentWrapper.removeClass("hide");
            } else {
                t.frmUpdateStatus.el.comment.rules("remove");
            }
        } catch (err) {
            console.log(err);
        }
        if (t.frmUpdateStatus.ticketData.status_id != 5) {
            $('#frm_update_status option[value="2"]').remove();
        }
    };

    var select2Opts = { width: "100%",
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    };

    t.frmUpdateStatus.el.statusId.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmUpdateStatus.el.statusId.parent(),
        ajax: {
            url: t.config.url.getStatusByAjaxForUpdateStatus,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term || '',
                    page: p.page || 1,
                    ticket_id: t.frmUpdateStatus.el.id.val()
                };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(s => ({
                        id: s.id,
                        text: s.text,
                        form: s.status_form
                    })),
                    pagination: {
                        more: data.pagination?.more || false
                    }
                };
            }
        }
    }))
    .on('select2:select', function (e) {

        const selected = e.params.data;

        $(this)
            .find('option:selected')
            .attr('form', selected.form);

    })
    .on("change", $.proxy(t.checkRevokeAccess))
    .on("change", $.proxy(t.statusIdChanged))
    .on("select2:select", $.proxy(t.loadRequestForm_onStatus))
    .on("select2:select", $.proxy(t.loadCheckStatusApproval));
    t.frmUpdateStatus.el.priorityId.select2($.extend({}, select2Opts, {dropdownParent: t.frmUpdateStatus.el.priorityId.parent()} ));

    t.frmUpdateStatusSubmit = function(e) {
        var formAttr = t.frmUpdateStatus.el.statusId.find(':selected').attr('form');
        console.log(formAttr);
        if (typeof formAttr !== 'undefined' && formAttr != 0 && $("#ticket_status_form_id").val() != 'not_found' && $("#ticket_status_form_id").val() == '') {
            t.loadRequestForm_onStatus();
            t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
        } else {
            if (typeof e !== "undefined") {
                e.preventDefault();
            }

            if (t.frmUpdateStatus.ticketData.wai != 23) {
                // console.log(t.frmUpdateStatus.ticketData.wai);
                var data = {
                    'msg': 'This ticket is not assigned to you. Please contact your manager.',
                };
                sweetAlert('center', 'error', data);
                return;
            }

            let s = t.frmUpdateStatus.el.comment.val();
            count = s.replaceAll("&nbsp;", "").trim();
            if (t.frmUpdateStatusValidator.form() == false) {
                if(count.length <= 0) {
                    $('#update_details').find('#shows_error').html('This field is required.');
                    $('#update_details').find('#shows_error').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
                }
                return false;
            }

            if(count.length <= 0) {
                $('#update_details').find('#shows_error').html('Please enter valid content.');
                $('#update_details').find('#shows_error').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
                return false;
            } else {
                $('#update_details').find('#shows_error').html('');
            }

            let cc_email_check =  t.frmUpdateStatus.el.cc_emails.val() ? t.frmUpdateStatus.el.cc_emails.val().toString() : "";
            if (t.httpCall != true) {
                return false;
            }
            t.frmUpdateStatus.btnSubmit.attr("disabled", true);
            t.httpCall = false;

            /* need to clean comment input based on setup */
            var status = parseInt(t.frmUpdateStatus.el.statusId.val());
            var formData = new FormData(t.frmUpdateStatus.get(0));
            formData.append('cc_emails', cc_email_check);
            var http = $.ajax({
                url: t.config.url.update_status,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function(data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        t.data = data.data;
                        if( t.frmUpdateStatus.ticketData.status_id == t.config.status_spam ) {
                            setTimeout(function() {
                                window.location = t.config.url.tkt;
                            }, 800);
                        }
                        t.updateCCField();
                        t.frmCommentTokenize();
                        t.frmUpdateStatus.el.comment.val('').summernote('code', '');
                        t.attachment_update.empty();
                        $("#ticket_status_form_id").val('');
                        $("#set_edit_status_form").html(``);
                        sweetAlert('center', 'success', data);
                        $('#update_details').modal('hide');
                        setTimeout(function() {
                            t.refreshTicketList();
                        }, 2000);
                    }else if(data.status == "info"){
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        t.frmUpdateStatusReset();
                        sweetAlert('center', 'info', data);
                    } else {
                        t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.fail(function() {
                t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                var data = {
                    'msg': config.translations.something_went_wrong,
                };
                sweetAlert('center', 'error', data);
            });
            http.always(function() {
                t.frmUpdateStatus.btnSubmit.removeAttr("disabled");
                t.httpCall = true;
            });
        }
    };

    t.content.on("click", ".btn-update", $.proxy(t.updateTicket));
    t.frmUpdateStatus.btnSubmit.on("click", $.proxy(t.frmUpdateStatusSubmit));
    // ticket status update code end here

    // select all checkbox
    t.selectedTickets = {};
    t.selectedMergeTickets = {};
    t.tktSelectAll.on('change', function () {
        $('.ticket-check-boxes').each(function () {
            let ticketId = $(this).val();
            let ticketData = $(this).data('ticket');
           
            $(this).prop('checked', t.tktSelectAll.is(':checked'));
            if (t.tktSelectAll.is(':checked')) {
                t.selectedTickets[ticketId] = true;
                t.selectedMergeTickets[ticketId] = ticketData;
            } else {
                delete t.selectedTickets[ticketId];
                delete t.selectedMergeTickets[ticketId];
            }
        });
        t.selectedTicketIds = Object.keys(t.selectedTickets).join(',');
    });

    $(document).on('change', '.ticket-check-boxes', function () {
        let ticketId = $(this).val();
        let ticketData = $(this).data('ticket');

        if ($(this).is(':checked')) {
            t.selectedTickets[ticketId] = true;
            t.selectedMergeTickets[ticketId] = ticketData;
        } else {
            delete t.selectedTickets[ticketId];
            delete t.selectedMergeTickets[ticketId];
        }
        const total = $('.ticket-check-boxes').length;
        const checked = $('.ticket-check-boxes:checked').length;
        t.tktSelectAll
            .prop('indeterminate', checked > 0 && checked < total)
            .prop('checked', total > 0 && checked === total);
        t.selectedTicketIds = Object.keys(t.selectedTickets).join(',');
    });

    // reload list button
    $(document).on('click', '.btn-reload-list', function () {
        t.refreshTicketList();
    });
    t.mdlAddTask = t.content.find('#taskModal'); 
    t.frmAddTask = t.mdlAddTask.find('#task');
    t.frmAddTask.ticketId = t.frmAddTask.find("#ticket_id");
    t.frmAddTask.name = t.frmAddTask.find('#name');
    t.frmAddTask.companyId = t.frmAddTask.find("#company");
    t.frmAddTask.due_date = t.frmAddTask.find('#due_date');
    t.frmAddTask.description = t.frmAddTask.find('#task_description');
    t.frmAddTask.department_id = t.frmAddTask.find("#task_department_id");
    t.frmAddTask.problem_category_id = t.frmAddTask.find("#task_problem_category_id");
    t.frmAddTask.sub_category_id = t.frmAddTask.find("#task_sub_category_id");
    t.frmAddTask.subCategoryIdCvr = t.frmAddTask.find("#task_sub_category_id_cvr");
    t.frmAddTask.assigned_to = t.frmAddTask.find('#task_assigned_to');
    t.frmAddTask.cost = t.frmAddTask.find('#cost');
    t.frmAddTask.is_visible_user = t.frmAddTask.find('#is_visible_user');
    t.frmAddTask.ticketData = {};
    t.frmAddTask.btnSubmit = t.frmAddTask.find("#taskSubmit");
    $.validator.addMethod("futureDate", function(value, element) {
        if (!value) {
            return true;
        }
        return moment(value, "DD/MM/YYYY HH:mm", true)
            .isSameOrAfter(moment());
    }, "Date/time cannot be in the past.");

    $.validator.addMethod("summernotedescription", function(value, element) {
        let content = $(element).summernote('code');
        content = content.replace(/(<([^>]+)>)/gi, "").trim();
        return content.length > 0;
    }, "This field is required.");

    t.addTask = function(e) {
        t.resetTaskForm(); 
        t.config.add = true;
        var $btn = $(e.currentTarget); 
        var ticketId = $btn.data("id");  
        var assignedToId = $btn.data("assigned-to-id");  
        var assignedToName = $btn.data("assigned-to-name"); 
        t.config.creator = $btn.data("creator");
        t.config.company_id = t.companyId;
        $.ajax({
            type: 'get',
            url: t.config.url.getDepartmentAndCategories,
            data:{
                ticketId: ticketId 
            },
            success: function (res) {
                if(res.status == 'success'){
                    if(res.data.department_id){
                        let option = new Option(res.data.department_name, res.data.department_id, true, true);
                        t.frmAddTask.department_id.append(option).trigger('change');
                    }
                    if(res.data.problem_category_id){
                        t.config.pc_id = res.data.problem_category_id;
                        t.config.pc_name = res.data.problem_category_name;
                    }
                    if(res.data.sub_category_id){
                        t.config.spc_id = res.data.sub_category_id;
                        t.config.spc_name = res.data.sub_category_name;
                    }
                }
            }
        });
        t.optionsCompany();
        t.frmAddTask.ticketId.val(ticketId);
        let tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        t.dueDatePicker.setDate(tomorrow, true);
        if (assignedToId) {
            var newOption = new Option(assignedToName, assignedToId, true, true);
            t.frmAddTask.assigned_to.append(newOption).trigger("change");
        }
        t.mdlAddTask.modal("show");
    };

    t.optionsCompany = function() {
        t.frmAddTask.companyId.empty().append(new Option("Select Company", ""));
        $.each(t.config.companies, function(i,v) {
            if (t.config.company_id && t.config.company_id == v.id) {
                t.frmAddTask.companyId.append(new Option(v.text, v.id,true,true)).prop('disabled',true).trigger("change");
            }else{
                t.frmAddTask.companyId.append(new Option(v.text, v.id));
            }
        });
    };

    t.resetTaskForm = function () {
        t.frmAddTask[0].reset();
        t.frmAddTask.name.val("");
        t.frmAddTask.due_date.val("");
        if (t.dueDatePicker) {
            t.dueDatePicker.clear();
        }
        t.frmAddTask.description.summernote("code", "");
        t.frmAddTask.assigned_to.val(null).trigger("change");
        t.frmAddTask.companyId
            .val(null)
            .prop('disabled', false)
            .trigger("change");
        t.frmAddTask.is_visible_user.prop('checked', false);
        t.frmTaskValidator.resetForm();
    };
    t.refreshTaskList = function () {
        taskTable.ajax.reload();
     }
    t.frmTaskValidator = t.frmAddTask.validate({
        ignore:[],
        onsubmit: false,
        rules: {
            name: {
                required: true,
                maxlength:255,
                clean_text_only: true,
                noSpecialStart: true
            },
            description: {
                required: true,
                summernotedescription: true,
                maxSummernoteChars: 2000
            },
            cost: {
                number:true,
            },
            due_date: {
                required: true,
                futureDate: true
            }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'task_description') {
                error.insertAfter(element.parent().find('.note-editor'));
            }
            else if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            }
            else {
                error.insertAfter(element);
            }
        }
    });
    t.dueDatePicker = t.frmAddTask.due_date.flatpickr({
        enableTime: true,
        time_24hr: true,
        dateFormat: "d/m/Y H:i",
        minuteIncrement: 15,
        minDate: "today",
        allowInput: false,
    });
    t.frmAddTask.description.summernote({
        placeholder: 'Enter Your Message',
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 100,
        focus: true,
        disableDragAndDrop: true,
        callbacks: {
            onInit: function () {
                $('.note-style .dropdown-toggle').html(
                    'Tt <span style="font-size:.65rem">▾</span>'
                );
            }
        }
    });
    t.fun = {
        reload_problem_category: function (element) {
            var department = t.frmAddTask.department_id.val();
            t.frmAddTask.subCategoryIdCvr.addClass('hide');
            t.frmAddTask.problem_category_id.empty().append(new Option("", "", false, false));
            t.frmAddTask.sub_category_id.empty().append(new Option("", "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            if (k.status != 0) {
                                let truncatedName = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                                let option = new Option(truncatedName, k.id, false, false);
                                $(option).attr("title", k.name);
                                t.frmAddTask.problem_category_id.append(option);
                            }
                        });
                        if (t.config.pc_id && t.config.add) {
                            t.frmAddTask.problem_category_id.val(t.config.pc_id).trigger('change');
                        }else{
                            t.frmAddTask.problem_category_id.val(null).trigger('change')
                        }
                    }
                });

            }
        },
        reload_sub_category: function (element){
            let selectedPcIds = t.frmAddTask.problem_category_id.val() || [];
            t.frmAddTask.sub_category_id.append(new Option("", "", false, false));
            if (!selectedPcIds.length) {
                t.frmAddTask.subCategoryIdCvr.addClass('hide');
                return;
            }
            let item = problem_categories.find(pc => pc.id == selectedPcIds);
            let hasSubCategory = item && Array.isArray(item.sub) && item.sub.length > 0;
            if (hasSubCategory) {
                t.frmAddTask.subCategoryIdCvr.removeClass('hide');
                if(t.config.spc_id && t.config.spc_id != null && t.config.add){
                    t.config.add = false;
                    if (!t.frmAddTask.sub_category_id.find("option[value='" + t.config.spc_id + "']").length) {
                        const option = new Option(t.config.spc_name, t.config.spc_id, true, true);
                        t.frmAddTask.sub_category_id.append(option);
                        t.frmAddTask.sub_category_id.val(t.config.spc_id).trigger('change');
                    }
                }else{
                    t.frmAddTask.sub_category_id.val(null).trigger('change');
                }
            } else {
                t.frmAddTask.subCategoryIdCvr.addClass('hide');
                t.frmAddTask.sub_category_id.append('Select Sub Category', "", false, false);
            }
        }
    }

    t.frmAddTask.assigned_to.select2($.extend({}, {
        dropdownParent: t.frmAddTask.assigned_to.parent(),
        width:"100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            delay: 300,
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    context: 'task',
                };
            },
            processResults: function (data, params) {
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            }
        },
        allowClear: true,
        placeholder : "Select Assign to",
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "10px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        },
    }));
    t.frmAddTask.department_id.select2($.extend({width: "100%",placeholder:"Select Department"}, {
        dropdownParent: t.frmAddTask.department_id.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.companyId,
                }
            }
        },
        allowClear: true,
        delay: 200,
        placeholder: config.translations.connect ?? "Select Department",
        templateSelection: function(s,container) {
            if(typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr('tkt_auto_creation_id', s.tkt_auto_creation_id);
            return s.text;
        }
    })).on("change", function(e) {
        t.fun.reload_problem_category();
    });

    t.frmAddTask.problem_category_id.select2({ width: "100%", dropdownParent: t.frmAddTask.problem_category_id.parent(),placeholder:"Select Problem Category" }).on("change",function(e){
        t.fun.reload_sub_category();
    });
    t.frmAddTask.sub_category_id.select2($.extend({width: "100%", placeholder:"Select Sub Category"}, {
        dropdownParent: t.frmAddTask.sub_category_id.parent(),
        ajax: {
            url: t.config.url.sub_category,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: [t.frmAddTask.problem_category_id.val()]
                };
            },
            delay: 200,
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        if (item.status === 0) {
                            return null;
                        }
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }
        },
        placeholder: config.translations.select_sub_category ?? "Select Sub Category",
    }));
    t.frmAddTask.btnSubmit.on("click", function (e) {
        e.preventDefault();

        if (t.frmTaskValidator.form() == false) {
            return false;
        }
    
        let formData = new FormData(t.frmAddTask[0]);
        formData.append('company_id', t.companyId);
        formData.append('type_id', 4);
        formData.append('status_id', 1);   
        formData.append('priority_id', 1); 
        let currentDate = new Date();
        formData.append('start_date',flatpickr.formatDate(currentDate, "d/m/Y H:i"));      
        formData.append('ticketmodule','true'); 
        t.frmAddTask.btnSubmit.prop("disabled", true);
    
        let http = $.ajax({
            url: t.config.url.addTask, 
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center','success',data);
                    setTimeout(function () {
                        t.mdlAddTask.modal("hide");
                        $('.btn-reload-list').trigger('click');
                    }, 2000);
                }  else {
                    sweetAlert('center','error',data);
                }
            }
        })
        http.fail(function(xhr) {
            var data = {
                'msg': config.translations.somethingWentWrong
            };
            sweetAlert('center', 'error', data);
        });
    
        http.always(function() {
            t.frmAddTask.btnSubmit.prop("disabled", false);
        });
    });

    t.frmAddTask.companyId.select2({width:"100%"});
    t.content.on("click", ".addtask", $.proxy(t.addTask));

    t.taskTableMdl = t.content.find('#taskDataModal');
    t.taskTable = t.taskTableMdl.find('#taskTable');
    t.descriptonMdl = t.content.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find("#descriptionModalBody");

      $(document).on('click', '.task-link', function () {
        const ticketId = $(this).data('ticket-id');

        if (!ticketId) {
            console.error('Missing ticket ID');
            return;
        }
        $('#ticket-id-display').text(` - #${ticketId}`);
        t.taskTableMdl.modal('show');

        if ($.fn.DataTable.isDataTable('#taskTable')) {
            t.taskTable.DataTable().clear().destroy();
        }

        taskTable = t.taskTable.DataTable({
            processing: true,
            bDestroy: true,
            paging: true,
            pageLength: 10,
            scrollX: true,
            autoWidth: false,
            scrollCollapse: true,

            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1
            },
            ajax: {
                url: t.config.url.getRelatedTask,
                type: 'GET',
                data: {
                    _token: t.config.token,
                    ticket_id: ticketId,
                }
            },
            columns: [
                {data: 'task_company_name'},
                { data: 'task_no' },
                { data: 'task_name',
                    render: function(data, type, row){
                            if (!data) {
                                return '';
                            }
                            data = String(data);
                            if (data.length > 30) {
                                var truncated = t.truncateHtml(data, 30);
                                return `<p data-toggle="tooltip" data-placement="right" title="${data}">${truncated + '...'}</p>`;
                            } else {
                                return `<p>${data}</p>`;
                            }
                        }
                },
                {data: 'department_name'},
                {data: 'problem_category_name'},
                {data: 'sub_category_name'},
                { data: 'description',
                    render: function(data, type, row) {
                            if (!data) {
                                return '';
                            }
                            data = String(data);

                            if (data.length > 30) {
                                var truncated = t.truncateHtml(data, 30);
                                return truncated+ '<a class="read-more" style="cursor:pointer" data-full-text="' + t.escapeHtmlText(data) + '">...Read More</a>';
                            } else {
                                return  data ;
                            }
                        }
                },
                { data: 'status' },
                { data: 'priority' },  
                { data: 'start_date' },
                { data: 'due_date' },
                { data: 'end_date' },
                { data: 'cost' },
                 { data: 'creator' },
                { data: 'assigned_to' },
                {data:'change_by_module',
                    render: function(data,type,row){
                        if(data == 3){
                            return 'Problem Category';
                        }else if(data == 2){
                            return 'Ticket';
                        }else if(data == 1){
                            return 'Task';
                        }else{
                            return '';
                        }
                    }
                },
                { data: 'created_at' },
                { data: 'updated_at' },
            ],
            initComplete: function () {

                let customFilter = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div id="taskTable_length_wrapper"></div>
                        <div class="flex-grow-1"></div>
                <div class="input-group table-search-btns searchbox_cover"
                        style="max-width:250px;border:1px solid #ccc;border-radius:10px;margin-right: 10px;">
                            <span class="input-group-text border-0 bg-transparent ps-1 pe-0" style="color:#aaa;">
                        <i class="bi bi-search" style="font-size:15px;"></i>
                            </span>
                            <input type="text"
                                class="form-control border-0 shadow-none searchbox"
                                placeholder="Search..."
                            style="height:30px;padding:4px 6px;">
                        </div>
                        
                        <button class="amg-refresh-btn btn-reload-task-list" style="height:32px;padding:4px 6px;">
                            <svg class="amg-refresh-btn__icon" width="20" height="30" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99867C19.5002 11.3648 18.5686 13.6359 16.9069 15.3202C15.2451 17.0046 12.9869 17.9669 10.6209 17.9987H10.5C8.20147 18.0044 5.98901 17.1248 4.32187 15.5424C4.25028 15.4747 4.19273 15.3936 4.1525 15.3036C4.11227 15.2136 4.09016 15.1166 4.08741 15.0181C4.08467 14.9196 4.10136 14.8216 4.13652 14.7295C4.17168 14.6375 4.22463 14.5532 4.29234 14.4816C4.36006 14.41 4.44121 14.3525 4.53116 14.3123C4.62112 14.272 4.71812 14.2499 4.81662 14.2472C4.91512 14.2444 5.0132 14.2611 5.10525 14.2963C5.19731 14.3314 5.28153 14.3844 5.35312 14.4521C6.42544 15.4634 7.77195 16.1363 9.22447 16.3867C10.677 16.6372 12.1711 16.4541 13.5202 15.8603C14.8692 15.2666 16.0134 14.2884 16.8098 13.0482C17.6062 11.8079 18.0195 10.3605 17.9981 8.88668C17.9766 7.41289 17.5214 5.97808 16.6892 4.76151C15.8571 3.54494 14.6849 2.60053 13.3192 2.04626C11.9534 1.492 10.4546 1.35245 9.00998 1.64506C7.56537 1.93766 6.239 2.64944 5.19656 3.69148C5.1889 3.69976 5.18076 3.70759 5.17219 3.71492L2.68031 5.99867H5.25C5.44891 5.99867 5.63968 6.07769 5.78033 6.21834C5.92098 6.35899 6 6.54976 6 6.74867C6 6.94758 5.92098 7.13835 5.78033 7.279C5.63968 7.41965 5.44891 7.49867 5.25 7.49867H0.75C0.551088 7.49867 0.360322 7.41965 0.21967 7.279C0.0790176 7.13835 0 6.94758 0 6.74867V2.24867C0 2.04976 0.0790176 1.85899 0.21967 1.71834C0.360322 1.57769 0.551088 1.49867 0.75 1.49867C0.948912 1.49867 1.13968 1.57769 1.28033 1.71834C1.42098 1.85899 1.5 2.04976 1.5 2.24867V5.04242L4.14844 2.62367C5.40842 1.36858 7.012 0.51492 8.75675 0.170448C10.5015 -0.174024 12.3092 0.00613689 13.9516 0.688188C15.5941 1.37024 16.9976 2.5236 17.9851 4.00268C18.9726 5.48176 19.4997 7.22024 19.5 8.99867Z" fill="currentColor"></path>
                            </svg>
                            <span>Refresh</span>
                        </button>
                    </div>
                    
                `;

                $("#taskTable_wrapper .dataTables_filter").hide();
                $("#taskTable_wrapper").prepend(customFilter);

                $("#taskTable_length").appendTo("#taskTable_length_wrapper");

            $("#taskTable_length label").contents().filter(function () {
                return this.nodeType === 3;
            }).remove();

            $("#taskTable_length label").prepend("Show ");

                $("#taskTable_filter").remove();

                $("#taskTable_wrapper .searchbox").on("keyup", function (e) {
                    if (e.keyCode == 13 || this.value.length == 0) {
                        taskTable.search(this.value).draw();
                    }
                });
            }
        });
    });
    t.truncateHtml = function(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }
    t.escapeHtmlText= function(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
    }

    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };
    t.content.on("click", ".read-more", function(e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    });
    
    t.deleteTicketmulitiple = function(e) {
        e.preventDefault();
        var ids = Object.keys(t.selectedTickets);
        if (ids.length === 0) {
            var data = {
                'msg' : 'Please select atleast one ticket to delete.'
            }
            sweetAlert('center','error',data);
            return;
        }
        if (t.httpCall != true) {
            return false;
        }
        sweetAlertConfirm({
            message: t.config.translations.are_you_delete,
            url: t.config.url.delete_multiple,
            data: {
                "_token": t.config.token,
                "id": ids
            },
            onSuccess: function(data) {
                setTimeout(function() { t.refreshTicketList();}, 800);
                t.selectedTickets = {};
                t.selectedMergeTickets = {};
                t.selectedTicketIds = '';
                t.tktSelectAll.prop('checked', false).prop('indeterminate', false);
            },
            errorMsg: t.config.translations.something_went_wrong,
            beforeSend: function() {
                t.httpCall = false;
            },
            complete: function() {
                t.httpCall = true;
            }
        });
    };

    t.resolvedTicketmultiple = function(e) {
        const selected = Object.keys(t.selectedTickets);
        if (selected.length === 0) {
            sweetAlert('center', 'error', {
                msg: config.translations.atleast_resolve
            });
            return false;
        }
        $('#resolved-reason').val('');
        $('#mdlBulkResolved').modal('show');
    };

    $("#btnResolvedSubmit").on("click", function(e) {
        e.preventDefault();
        const selected = Object.keys(t.selectedTickets);
        if (selected.length === 0) {
            sweetAlert('center', 'error', {
                msg: t.config.translations.atleast_resolve
            });
            return false;
        }
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        $("#btnResolvedSubmit").attr("disabled", true);
        $.ajax({
            url: t.config.url.resolved_multiple,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": selected,
                "comment": $('#resolved-reason').val()
            }
        }).done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    $("#btnResolvedSubmit").removeAttr("disabled");
                    $('#resolved-reason').val('');
                    t.selectedTickets = {};
                    t.selectedMergeTickets = {};
                    t.selectedTicketIds = '';
                    t.tktSelectAll.prop('checked', false).prop('indeterminate', false);
                    $('.ticket-check-boxes').prop('checked', false);
                    sweetAlert('center', 'success', data);
                    setTimeout(function() {
                        t.refreshTicketList();
                    }, 800);

                    $("#mdlBulkResolved").modal('hide');

                } else {

                    $("#btnResolvedSubmit").removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        }).fail(function() {
            $("#btnResolvedSubmit").removeAttr("disabled");
            sweetAlert('center', 'error', {
                msg: t.config.translations.something_went_wrong
            });
        }).always(function() {
            t.httpCall = true;
        });
    });

    t.bulkAssignModal = t.content.find('#bulkAssignMdl');
    t.bulkAssignTo = t.bulkAssignModal.find('#users');
    t.btnBulkAssignSubmit = t.bulkAssignModal.find("#btnBulkAssignSubmit");
    t.bulkTable = t.bulkAssignModal.find('#bulkAssignTicket');
    t.size = 1;
    t.lgi_temp = $(document).find("#lgi-temp");
    t.lgi_cmpil = null;

    t.bulkAssignTo.select2($.extend({}, select2Opts, {
        placeholder: 'Assign To User',
        dropdownParent: t.bulkAssignTo.parent(),
        ajax: {
            url: t.config.url.userTicketHandlerList,
            type: 'POST',
            delay: 250,
            dataType: 'json',
            data: function (params) {
                return {
                    _token: t.config.token,
                    search: params.term || '',
                    page: params.page || 1
                };
            },
            processResults: function (response) {
                return {
                    results: $.map(response.data, function (item) {
                        return {
                            id: item.id,
                            text: item.full_name,
                            email: item.email,
                            img_path: item.img_path,
                            status: item.status,
                            employee_num: item.employee_num
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (data.loading) {
                return data.text;
            }
            if (!data.id) {
                return data.text;
            }
            let s = {
                text: data.text,
                email: data.email,
                img_path: data.img_path,
                status: data.status,
                employee_num: data.employee_num
            };
            return t.config.userDropdownFormat(s, "23px");
        },
        templateSelection: function (data) {
            return data.text || data.name;
        }
    }));

   

    t.bulkAssignTickets = function() {
        var selected = Object.keys(t.selectedTickets);
        if (selected.length === 0) {
            sweetAlert('center', 'error', {
                msg: t.config.translations.atleast_assign
            });
            return false;
        }

        t.bulkTicket.ajax.reload();
        t.bulkAssignModal.modal('show');
    }

    t.bulkTicket = $('#bulkAssignTicket').DataTable({
        destroy: true,
        autoWidth: false,
        dom: 'rtip',
        searching: true,
        lengthChange: true,
        pageLength: 10,
        aoColumnDefs: [{
            bSortable: false,
            aTargets: [0]
        }],
        order: [
            [2, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.bulkAssignList,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.ticket = Object.keys(t.selectedTickets);
            },
            error: function (xhr, status, error) {
                console.error("Bulk Assign AJAX Error:", error);
            }
        },
        columns: [
            { data: 'a.id' },
            { data: 'a.subject' },
            { data: 'a.created_at_format' }
        ],

        initComplete: function () {
            var table = this.api();
            $('.bulk-ticket-page-length')
                .off('change.bulk')
                .on('change.bulk', function () {

                    table.page.len(parseInt($(this).val())).draw();

                });
            $('.bulk-ticket-search')
                .off('keyup.bulk')
                .on('keyup.bulk', function () {

                    table.search($(this).val()).draw();

                });
            $('.bulk-ticket-page-length').val(table.page.len());
        },
        drawCallback: function () {
            var table = this.api();
            $('.bulk-ticket-page-length').val(table.page.len());
        }
    });

    t.bulkAssignTicket = function() {
        $('.availabilityError').text('');
        var selected = Object.keys(t.selectedTickets);
        var user = t.bulkAssignTo.val();
        if ($.trim(user) == '') {
            $('.availabilityError').text('Please select a user.');
            t.bulkAssignTo.focus();
            return false;
        }
        var fd = new FormData();
        fd.append('selected_tickets', JSON.stringify(selected));
        fd.append('user', user);
        fd.append('_token', t.config.token);
        t.btnBulkAssignSubmit.prop('disabled', true);
        $.ajax({
            method: "POST",
            url: t.config.url.bulkAssignTicket,
            data: fd,
            processData: false,
            contentType: false,
            success: function(data) {
                t.btnBulkAssignSubmit.prop('disabled', false);
                if (data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    t.bulkAssignModal.modal('hide');
                    t.selectedTickets = {};
                    t.selectedMergeTickets = {};
                    t.selectedTicketIds = '';
                    t.tktSelectAll.prop('checked', false).prop('indeterminate', false);
                    setTimeout(function() { t.refreshTicketList();}, 800);
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            error: function(data) {
                t.btnBulkAssignSubmit.prop('disabled', false);
                sweetAlert('center', 'error', data);
            }
        });
    }

    t.bulkAssignTo.on('change', function () {
        $('.availabilityError').text('');
    });

    t.bulkDelete.on('click', $.proxy(t.deleteTicketmulitiple));
    t.bulkResolve.on("click",$.proxy(t.resolvedTicketmultiple));
    t.btnBulkAssignSubmit.on('click',t.bulkAssignTicket);
    t.bulkAssign.on("click",$.proxy(t.bulkAssignTickets));
    t.mergeMdl = t.content.find("#mergeMdl");
    t.mergeMdl.btnClear = t.mergeMdl.find("#btnClear");

    t.bulkMerge.on('click', function () {
        let mergeTickets = Object.values(t.selectedMergeTickets);
        if (mergeTickets.length < 2) {
            sweetAlert('center', 'error', {
                msg: 'Please select at least 2 tickets.'
            });
            return;
        }
          // Closed / Resolved check
        let invalidTickets = mergeTickets.filter(ticket =>
            [5, 6].includes(parseInt(ticket.status_id))
        );

        if (invalidTickets.length > 0) {

            let ticketIds = invalidTickets
                .map(ticket => '#' + ticket.id)
                .join(', ');

            sweetAlert('center', 'error', {
                msg: `${ticketIds} cannot be merged because they are Resolved or Closed.`
            });

            return;
        }
        let companies = [...new Set(mergeTickets.map(x => x.company_id))];
        let departments = [...new Set(mergeTickets.map(x => x.department_id))];
        if (companies.length > 1) {
            sweetAlert('center', 'error', {
                msg: 'Please select tickets from same company.'
            });
            return;
        }
        if (departments.length > 1) {
            sweetAlert('center', 'error', {
                msg: 'Please select tickets from same department.'
            });
            return;
        }
        t.mergeTickets = mergeTickets;
        t.primaryTicket = mergeTickets[0];

        t.renderMergeTickets();
        $('#merge-ticket-mdl-frm')[0].reset();
        t.mergeMdl.modal('show');
    });

    t.renderMergeTickets = function () {
        const truncate = (text, len = 60) => {
            return text.length > len
                ? text.substring(0, len) + '...'
                : text;
        };

        let primaryHtml = `
            <div class="border rounded-3 p-3 mb-3 bg-light shadow-sm">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-lg">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge bg-success">
                                PRIMARY
                            </span>
                            <span class="fw-bold text-primary">
                                #${t.primaryTicket.id}
                            </span>
                            <span
                                class="text-break"
                                data-bs-toggle="tooltip"
                                title="${t.primaryTicket.subject}">
                                ${truncate(t.primaryTicket.subject)}
                            </span>
                            <span
                                class="tkt-status-badge"
                                style="
                                    background:${t.primaryTicket.statusBgColor};
                                    color:${t.primaryTicket.status_color_code};
                                    border:1px solid ${t.primaryTicket.status_color_code};
                                ">
                                ${t.primaryTicket.status}
                            </span>
                        </div>
                        <div class="small text-muted mt-2">
                            ${t.primaryTicket.company}
                            â€¢
                            ${t.primaryTicket.department}
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <button
                            type="button"
                            data-bs-toggle="tooltip"
                            title="Remove"
                            class="tkt-icon-btn js-remove-ticket"
                            data-ticket-id="${t.primaryTicket.id}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        t.mergeMdl.find('.primary_ticket').html(primaryHtml);
        let othersHtml = '';
        $.each(t.mergeTickets, function (_, ticket) {
            if (ticket.id == t.primaryTicket.id) {
                return true;
            }
            othersHtml += `
                <li
                    class="border rounded-3 p-3 mb-2 shadow-sm list-unstyled"
                    data-ticket-id="${ticket.id}"
                >
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-lg">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="fw-bold text-primary">
                                    #${ticket.id}
                                </span>
                                <span
                                    class="text-break"
                                    data-bs-toggle="tooltip"
                                    title="${ticket.subject}">
                                    ${truncate(ticket.subject)}
                                </span>
                                <span
                                    class="tkt-status-badge"
                                    style="
                                        background:${ticket.statusBgColor};
                                        color:${ticket.status_color_code};
                                        border:1px solid ${ticket.status_color_code};
                                    ">
                                    ${ticket.status}
                                </span>
                            </div>
                            <div class="small text-muted mt-2">
                                ${ticket.company} • ${ticket.department}
                            </div>
                        </div>

                        <div class="col-12 col-lg-auto">
                            <div class="d-flex flex-column flex-sm-row gap-2">
                                <button type="button" class="tkt-icon-btn js-make-primary" data-bs-toggle="tooltip" title="Make Primary" data-ticket-id="${ticket.id}"><i class="bi bi-award"></i></button>
                                <button type="button" class="tkt-icon-btn js-remove-ticket" data-bs-toggle="tooltip" title="Remove" data-ticket-id="${ticket.id}"> <i class="bi bi-trash3"></i> </button>
                            </div>
                        </div>
                    </div>
                </li>
            `;
        });

        t.mergeMdl.find('.others ul').html(othersHtml);

        t.mergeMdl.find('.count_shower')
            .html(`(${t.mergeTickets.length})`);

        t.mergeMdl.find('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this);
        });
    };

    t.mergeMdl.on('click', '.js-make-primary', function () {
        let ticketId = $(this).data('ticket-id');
        let ticket = t.mergeTickets.find(
            x => x.id == ticketId
        );
        const tooltip = bootstrap.Tooltip.getInstance(this);

        if (tooltip) {
            tooltip.hide();
        }
        t.primaryTicket = ticket;
        t.renderMergeTickets();
    });

    t.mergeMdl.on('click', '.js-remove-ticket', function () {

        let ticketId = $(this).data('ticket-id');
        t.mergeTickets = t.mergeTickets.filter(
            x => x.id != ticketId
        );
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        if (t.mergeTickets.length < 2) {
            sweetAlert('center', 'error', {
                msg: 'Minimum 2 tickets required.'
            });
            t.mergeMdl.modal('hide');
            return;
        }
        if (t.primaryTicket.id == ticketId) {
            t.primaryTicket = t.mergeTickets[0];
        }
        t.renderMergeTickets();
    });

    t.mergeMdl.on('click', '#btnSubmit', function () {
        let comments = $('#comments').val().trim();
        if (!comments) {
            sweetAlert('center', 'error', {
                msg: 'Please enter merge comments.'
            });
            return false;
        }
        let payload = {
            _token: t.config.token,
            primary: t.primaryTicket.id,
            others: t.mergeTickets.filter(ticket => ticket.id != t.primaryTicket.id).map(ticket => ticket.id).join(','), // "4044,4051"
            remarks: comments
        };
        let btn = $(this);
        btn.prop('disabled', true);
        $.ajax({
            url: t.config.url.merge_tickets,
            type: 'POST',
            data: payload,
            success: function (data) {
                if (data.status === 'success') {
                    sweetAlert('center', 'success', data);
                    t.mergeMdl.modal('hide');
                    t.selectedTickets = {};
                    t.selectedMergeTickets = {};
                    t.selectedTicketIds = '';
                    $('.ticket-check-boxes').prop('checked', false);
                    t.tktSelectAll.prop('checked', false).prop('indeterminate', false);
                    setTimeout(function() { t.refreshTicketList();}, 800);
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            error: function () {
                sweetAlert('center', 'error', {
                    msg: config.translations.something_went_wrong
                });
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });

    // Clear button functionality
    t.mergeMdl.btnClear.on('click', function () {
        t.mergeMdl.modal('hide');
        // $('#merge-ticket-mdl-frm')[0].reset();
        t.mergeTickets = [];
        t.primaryTicket = null;
        t.selectedMergeTickets = {};
        t.selectedTickets = {};
        t.selectedTicketIds = '';
        
        $('.ticket-check-boxes').prop('checked', false);
        t.tktSelectAll.prop('checked', false).prop('indeterminate', false);
        $('#comments').val('');
        sweetAlert('center', 'success', {
            msg: 'Merge selection cleared successfully.'
        });
    });

    t.mdlArticleConvert = t.content.find('#article_convert')
    t.frmArticleConvert = t.content.find("#frm_article_convert");
    t.frmArticleConvert.el = {};
    t.frmArticleConvert.el.id = t.frmArticleConvert.find("#id");
    t.frmArticleConvert.el.kd_id = t.frmArticleConvert.find("#kd_id");
    t.frmArticleConvert.el.forAction = t.frmArticleConvert.find("#for_action");
    t.frmArticleConvert.el.title = t.frmArticleConvert.find("#title");
    t.frmArticleConvert.el.problem_category_id = t.frmArticleConvert.find("#problem_category_kd_id");
    t.frmArticleConvert.el.sub_category_id_cvr = t.frmArticleConvert.find("#sub_category_kd_id_cvr");
    t.frmArticleConvert.el.sub_category_id = t.frmArticleConvert.find("#sub_category_kd_id");
    t.frmArticleConvert.el.department_id = t.frmArticleConvert.find("#department_kd_id");
    t.frmArticleConvert.el.tags = t.frmArticleConvert.find("#kd_tags");
    t.frmArticleConvert.el.status = t.frmArticleConvert.find("#kd_status");
    t.frmArticleConvert.el.content = t.frmArticleConvert.find("#kd_content");
    t.frmArticleConvert.btnSubmit = t.frmArticleConvert.find("#btnSubmit");
    t.frmArticleConvert.attachment_dropper_cover = t.frmArticleConvert.find('#update-dropper-cover');
    t.frmArticleConvert.attachment_dropper = t.frmArticleConvert.attachment_dropper_cover.find('#update-dropper');
    t.attachment_update_kd = t.frmArticleConvert.find("#attachment_updates");
    t.frmArticleConvert.el.tmp_id = t.frmArticleConvert.find("#tmp_id");
    t.frmArticleConvert.el.attachCommentCheckBox = t.frmArticleConvert.find("#includeComment");
    t.frmArticleConvert.commentCover = t.frmArticleConvert.find(".commentCover")
    t.frmArticleConvert.commentTable = t.frmArticleConvert.find("table")
    t.frmArticleConvert.selectAllCommentTable = t.frmArticleConvert.find(".selectAllComment");
    t.frmArticleConvert.ticketData = {};

    t.frmArticleConvert.el.content.summernote({
        height: 300,
        placeholder: 'Enter article content here...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']]
        ]
    });

    t.frmArticleConvert.selectAllCommentTable.on('change', function() {
        if($(this).prop('checked') == true) {
            t.frmArticleConvert.commentTable.find(".commentsForKd").prop('checked', true);
        } else {
            t.frmArticleConvert.commentTable.find(".commentsForKd").prop('checked', false);
        }
    });

    t.frmArticleConvert.el.attachCommentCheckBox.on('change', function() {
        if(t.frmArticleConvert.el.attachCommentCheckBox.prop('checked') == true) {
            var formData = new FormData();
            formData.append('_token', t.config.token);
            formData.append('id', t.frmArticleConvert.el.id.val());
            var http = $.ajax({
                url: t.config.url.get_timeline,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function(data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.frmArticleConvert.commentCover.removeClass('hide');
                        t.frmArticleConvert.commentTable.find("tbody").html("");
                        if (data.data.length > 0) {
                            $.each(data.data, function(i, v) {
                                var text = "<tr>";
                                text += "<td><input type='checkbox' class='commentsForKd form-check-input' name='comment["+v.tfid+"]' /></td>";
                                text += "<td>"+v.remarks+"</td>";
                                text += "<td>"+v.commenter+"</td>";
                                text += "<td>"+v.updated_at_format+"</td>";
                                text += "</tr>";

                                t.frmArticleConvert.commentTable.find("tbody").append(text);
                            });
                        } else {
                            t.frmArticleConvert.commentTable.find("tbody").html("<tr><td class='text-center' colspan='4'>No Comments To Add</td></tr>");
                        }
                    } else if (data.msg != "") {
                        sweetAlert('center', 'error', data);
                    }
                }
            });

        } else {
            t.frmArticleConvert.commentCover.addClass('hide');
            t.frmArticleConvert.commentTable.find("tbody").html("");
        }
    });

    t.refillProblemCategoryTicketArtical = function(e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.artical_problem_categories = [];
        t.frmArticleConvert.el.problem_category_id.empty().append(new Option(config.translations.Select_Sub_Category, ""));
        var type_val = parseInt($.trim(t.frmArticleConvert.el.department_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function(data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.artical_problem_categories = data.data;
                    $.each(data.data, function(i, v) {
                        t.frmArticleConvert.el.problem_category_id.append(new Option(v.name, v.id));
                    });
                }
            }).always(function() {
                t.frmArticleConvert.el.problem_category_id.trigger("change");
            });
        } else {
            t.frmArticleConvert.el.problem_category_id.trigger("change");
        }
    };

    t.refillSubCategoryTicketArtical = function(e, val) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.artical_sub_categories = [];
        t.frmArticleConvert.el.sub_category_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        var type_val = parseInt($.trim(t.frmArticleConvert.el.problem_category_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.artical_problem_categories, function(i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.artical_sub_categories = v.sub;
                            $.each(v.sub, function(j, k) {
                                t.frmArticleConvert.el.sub_category_id.append(new Option(k.name, k.id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }
        if (t.data.sub_category_id != undefined) {
            t.frmArticleConvert.el.sub_category_id.val(t.data.sub_category_id).trigger("change");
        }
        t.updateSubCategoryVisibilityTicketArtical();
        t.frmArticleConvert.el.sub_category_id.trigger("change");
    };

    t.updateSubCategoryVisibilityTicketArtical = function() {
        if ( t.data.artical_sub_categories.length > 0) {
            t.frmArticleConvert.el.sub_category_id.rules("add", {
                required: true,
                str_name: true
            });
            t.frmArticleConvert.el.sub_category_id_cvr.show();
        } else {
            t.frmArticleConvert.el.sub_category_id.rules("remove");
            t.frmArticleConvert.el.sub_category_id_cvr.hide();
        }
    };

    t.loadForm = function(data, forAction) {
        t.frmArticleConvert.el.attachCommentCheckBox.prop('checked',false).trigger('change');
        t.frmArticleConvert.selectAllCommentTable.prop('checked',false).trigger('change');
        t.frmArticleConvert.el.title.val(data.subject);
        t.frmArticleConvert.el.id.val(data.id);
        t.frmArticleConvert.el.kd_id.val(data.id);
        t.frmArticleConvert.el.department_id.empty().append(new Option(data.dep_name, data.department_id, true, true)).trigger("change");
        
        /* set problem category */
        t.frmArticleConvert.el.problem_category_id.empty();
        t.frmArticleConvert.el.problem_category_id.append(new Option(data.prob_cat, data.problem_category_id, true, true));
        /* set sub category */
        t.refillSubCategoryTicketArtical(undefined, data.sub_category_id);
        if(data.ai_content != null) {
            let aiText = data.ai_content;
            let converter = new showdown.Converter();
            let formattedText = converter.makeHtml(aiText);
            t.frmArticleConvert.el.content.summernote('code', formattedText);
        } else {
            t.frmArticleConvert.el.content.summernote('code', data.content);
        }
        t.data.id = data.id;
        t.data.sub_category_id = data.sub_category_id;
        t.frmArticleConvert.el.tags.empty().trigger('change');
        $.each(data.tagIds, function( key, value ) {
            var newOption = new Option(value.tags, value.id, true, true);
            t.frmArticleConvert.el.tags.append(newOption).trigger("change");
        });
    };

    t.frmArticleConvert.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmArticleConvert.el.department_id.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 200
        },
        placeholder: config.translations.Enter_starting
    }));

    t.frmArticleConvert.el.tags.select2({
        width: "100%",
        placeholder: t.config.translations.select_tags,
        tags: true,
        dropdownParent:t.frmArticleConvert.el.tags.parent(),
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getTagDetails;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });

    t.frmArticleConvert.el.department_id.on("change", $.proxy(t.refillProblemCategoryTicketArtical));
    t.frmArticleConvert.el.problem_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmArticleConvert.el.problem_category_id.parent()} )).on("change", $.proxy(t.refillSubCategoryTicketArtical));
    t.frmArticleConvert.el.sub_category_id.select2($.extend({}, select2Opts, {dropdownParent: t.frmArticleConvert.el.sub_category_id.parent()} ));
    t.frmArticleConvert.el.status.select2($.extend({}, select2Opts, {dropdownParent: t.frmArticleConvert.el.status.parent()} ));

    t.convertArticles = function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var $btn = $(this);
        var originalIcon = $btn.html();
        $btn.prop('disabled', true);
        $btn.html(`
            <div class="spinner-border spinner-border-sm" role="status" style="width: 16px; height: 16px;">
                <span class="visually-hidden">Loading...</span>
            </div>
        `);
        var http = $.ajax({
            url: t.config.url.get_tickets,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": id,
                "ai_content" : t.config.aiEnabled
            },
            success:function(data) {
                t.frmArticleConvert.ticketData = data.data;
                t.attachment_update_kd.empty();
                t.loadForm(data.data);
                t.frmCommentTokenize();
                $('#article_convert').modal('show');
            },
            error: function(data) {
                console.log(data);
                sweetAlert('center', 'error', {
                    msg: config.translations.something_went_wrong
                });
            },
            complete: function() {
                // Restore original icon and enable button
                $btn.html(originalIcon);
                $btn.prop('disabled', false);
                if ($btn.attr('data-bs-toggle') === 'tooltip') {
                    new bootstrap.Tooltip($btn[0]);
                }
            }
        });
    };

    t.frmArticleConvertValidator = t.frmArticleConvert.validate({
        onsubmit: false,
        rules: {
            title: {
                required: true,
            },
        },
        errorPlacement: function(error, element) {
            error.insertAfter(element.parent());
        }
    });

    t.frmArticleConvertSubmit = function(e) {
        e.preventDefault();
        if (t.frmArticleConvertValidator.form() == false) {
            return false;
        }
        if (t.httpCall != true) {
            return false;
        }
        t.frmArticleConvert.btnSubmit.attr("disabled", true);
        t.httpCall = false;
        var formData = new FormData(t.frmArticleConvert.get(0));
        var http = $.ajax({
            url: t.config.url.get_articles,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.frmArticleConvert.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'success', data);
                    t.mdlArticleConvert.modal("hide");
                    setTimeout(function() {
                        t.refreshTicketList();
                    }, 2000);
                } else {
                    t.frmArticleConvert.btnSubmit.removeAttr("disabled");
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            t.frmArticleConvert.btnSubmit.removeAttr("disabled");
            var data = {
                'msg': t.config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.frmArticleConvert.btnSubmit.removeAttr("disabled");
            t.httpCall = true;
        });
    };
    t.download = function(e) {
        e.preventDefault();
        if (t.config.user.download_limits != "1") {
            return;
        }
        sweetAlertConfirmation({
            message: "By default 6 month data will be exported only. If any date filter is applied then max of 6 month data is exported only.",
            onConfirm: function() {
                t.cache_filter_values();
                var filter_more = '';
                if(t.config.department_filter && t.config.department_filter != 'null' && t.config.department_filter !== null){
                    filter_more = "&department="+t.config.department_filter;
                }
                if(t.config.location_filter && t.config.location_filter != 'null' && t.config.location_filter !== null){
                    filter_more = filter_more+"&location="+t.config.location_filter;
                }
                if(t.config.based_on_filter && t.config.based_on_filter !='null' && t.config.based_on_filter !== null){
                    filter_more = filter_more +"&based_on="+t.config.based_on_filter;
                }
                if(t.config.daterange_filter && t.config.daterange_filter !='null' && t.config.daterange_filter !== null){
                    filter_more = filter_more +"&daterange="+t.config.daterange_filter;
                }
                if(filter_more && filter_more != '' && filter_more !== null) {
                    filter_more = "&"+filter_more;
                }
                window.location = t.config.url.export_tickets + "?q=" + t.config.export_filters + "&main_filter=" + t.config.main_filter+filter_more + "&techId="+ t.config.tech_id + "&company_id="+ companyId;                    
                if($('.chkParent').prop('checked') == true) {
                    setTimeout(function() {
                        t.load();
                        $('.chkParent').prop('checked', false);
                    }, 800);
                }
            }
        });
    }
    t.content.on("click", ".btn-download", t.download);
    t.content.on("click", ".btn-article", $.proxy(t.convertArticles));
    t.frmArticleConvert.btnSubmit.on("click", $.proxy(t.frmArticleConvertSubmit));
    t.content.on('click','.btn-reload-task-list',$.proxy(t.refreshTaskList));
    $(document).on('click', '.ticket-detail-link', function () {
        localStorage.setItem('ticketBackUrl', window.location.href);
    });

}