var ArchivedTicketList = function (config) {
    var t = this;
    t.config = config;
    t.page = $("#ticket-archived-list-wrapper");
    t.lg = t.page.find("#lg");
    t.pagebtns = t.page.find("#pagebtns");
    t.pageBtmSummary = t.page.find("#page-btm-summary");
    t.visibleContent = t.page.find(".btn-visible-content");
    t.sortbtns = t.page.find(".sort-buttons");
    t.searchbox = t.page.find(".searchbox_cover");
    t.loader = t.searchbox.find(".loader");
    t.nonloader = t.searchbox.find(".nonloader");
    t.api_loader = t.page.find("#api_loader");
    t.detail_api_loader = t.page.find("#detail_api_loader");
    t.mdlTktHistory = t.page.find("#mdl-ticket-history");
    t.result = t.page.find("#result");
    t.total = 0;
    t.perPage = 10;
    t.pageLimiter = t.page.find("#pageLimiter");

    t.drawerModel = t.page.find("#drawer");
    t.draweroverlay = t.page.find("#drawer-overlay");
    t.drawerModelHeader = t.drawerModel.find(".drawer-header");
    t.drawerModelBody = t.drawerModel.find(".drawer-body");

    var select2Opts = {
    width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    };

    t.btn = {};
    t.filterBtn = t.page.find('#btnOpenFilter');
    t.filterMdl = t.page.find("#ticketFilterModal");
    t.filterBtn.on('click', function () {
        t.filterMdl.modal("show");
    });

    let companyId = t.config.company_user_detail ? t.config.company_user_detail : null;

    t.filters = {
        data: {
            problem_categories: {}
        }
    };

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
            console.log(t.config.url.departments_based_on_privilage);
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
                placeholder: t.config.translations.Select_the_User,
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

    t.cache_filter_values = function() {
        t.config.search = $.trim($("#ticket-search-input").val());
        t.config.other_filters = {};
        let statusValue = t.filterMdl.status.val();
        if (statusValue && statusValue.length > 0) {
            t.config.other_filters.status = statusValue;
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
        if (t.filterMdl.based_on && t.filterMdl.based_on.val() && t.filterMdl.based_on.val() != 'null') {
            t.config.other_filters.based_on = t.filterMdl.based_on.val();
            if (t.filterMdl.date_range && $('#daterange').val() && $('#daterange').val() != 'null') {
                t.config.other_filters.date_range = $('#daterange').val();
            }
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
        if(t.filterMdl.feedback) t.filterMdl.feedback.val("").trigger("change");
        if(t.filterMdl.ticket_type) t.filterMdl.ticket_type.val("").trigger("change");
        if(t.filterMdl.filter_by_merge) t.filterMdl.filter_by_merge.val("").trigger("change");
        if(t.filterMdl.filter_by_custom_field) t.filterMdl.filter_by_custom_field.val("").trigger("change");
        if(t.filterMdl.filter_by_custom_field_value) t.filterMdl.filter_by_custom_field_value.val("").trigger("change");
        if(t.filterMdl.filter_based_star) t.filterMdl.filter_based_star.val("").trigger("change");
        if(t.filterMdl.filter_by_task) t.filterMdl.filter_by_task.val("").trigger("change");
        t.cache_filter_values();
        resetFilterCount();
        t.load();
    };

    t.pageLimiter.select2({ width: "65px" }).on("change", function () {
        let val = $(this).val();
        let rendered = $(this).next('.select2-container')
            .find('.select2-selection__rendered');
        rendered.removeAttr("title");

        rendered.attr("data-original-title", val);

        t.perPage = parseInt($(this).val(), 10)
        t.pageNumber = 1
        t.hasMoreData = true
        t.isLoading = false
        t.lg.empty()
        $('.ticket-detail-box').empty();
        autoClickedFirstTicket = true;
        t.load();
    });
    function removeTooltip() {
        let rendered = t.pageLimiter.next('.select2-container')
            .find('.select2-selection__rendered');

        rendered.removeAttr("title")
            .removeAttr("data-original-title");
    }
    removeTooltip();
    t.pageLimiter.on('select2:select select2:open', function () {
        setTimeout(removeTooltip, 0);
    });
    t.pageNumber = 1
    t.isLoading = false
    t.hasMoreData = true
    let autoClickedFirstTicket = true;
    t.load = function () {
        t.api_loader.addClass('active');
        var http = $.ajax({
            url: t.config.url.archivedAjaxRemote,
            type: "get",
            data: {
                search: t.config.search,
                filters: t.config.other_filters,
                page: t.pageNumber,
                limit: t.perPage,
                order: t.config.sort_dir,
                company_id: config.company_id,
                location: t.config.location_filter,
                department: t.config.department_filter,
                based_on: t.config.based_on_filter,
                daterange: t.config.daterange_filter,
                sub_filter: t.config.sub_filter,
                filterFromReport: t.config.filterFromReport,
                status: t.config.redirect_status,
                global_search_pc: t.config.global_search_pc,
            }
        });
        t.loader.show();
        t.nonloader.hide();
        http.done(function (data) {
            if (typeof data == "object" && typeof data.total != "undefined") {
                t.isLoading = false
                t.api_loader.removeClass('active');
                t.data = data;
                if (data.filtered != data.total) {
                    t.pageBtmSummary.html(config.translations.Available + " " + data.filtered + " records (filtered from " + data.total + " total records)");
                } else {
                    t.pageBtmSummary.html(config.translations.Available_Records + " " + data.total);
                }
                if (data.filtered < 1) {
                    $('#ticket-data').addClass('hide');
                    $('#no-data').removeClass('hide');
                    $('#no-data').html(`
                        <div class="no-record-found text-center" style="padding: 30px;">
                            <img src="${config.url.nodataImage}" alt="No Data" 
                                style="max-width: 400px; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
                            <div style="color:#777; font-size:16px;">
                                ${config.translations.No_Tickets_Found}
                            </div>
                        </div>
                    `);
                    $('.ticket-detail-box').html('');
                }
                if (data.data && data.data.length > 0) {
                    $('#ticket-data').removeClass('hide');
                    $('#no-data').addClass('hide');
                    $.each(data.data, t.renderList);
                    if (autoClickedFirstTicket === true && data.page === 1) {
                        setTimeout(function () {
                            $('.ticket-card:first').trigger('click');
                        }, 50);
                    }
                }
                if (data.page >= data.lastpage) {
                    t.hasMoreData = false
                }
            }
        });
        http.fail(function () {
            t.api_loader.removeClass('active');
            alert("Something went wrong. Please check given details are correct");
            return false;
        });
        http.always(function () {
            t.loader.hide();
            t.isLoading = false
            t.nonloader.show();
            t.api_loader.removeClass('active');
            t.httpCall = true;
        });
    };
    $('.ticket-list').off('scroll').on('scroll', function () {
        if (t.isLoading || !t.hasMoreData) return
        const el = this
        const bottomReached = el.scrollTop + el.clientHeight >= el.scrollHeight - 10
        if (bottomReached) {
            t.isLoading = true
            t.pageNumber++
            autoClickedFirstTicket = true;
            t.load();
        }
    });
    const truncateText = (text, maxLength = 20) => {
        if (!text) return "";
        return text.length > maxLength ? text.slice(0, maxLength) + "…" : text;
    };

    t.renderList = function (i, d) {
        let assignedTo = d.assignedUser?.name ?? "";
        let priorityName = d.priority?.name ?? "";
        let deptName = d.department?.name ?? "";
        let category = d.category?.name ?? "";
        let subCategory = d.subCategory?.name ?? "";
        let date = d.created_short ?? "";
        let created_at_format = d.created_at_format ?? "";
        let statusName = d.status?.name ?? "";
        let priorityColor = "";
        switch (statusName.toLowerCase()) {
            case "resolved":
                priorityColor = "#00b894";
                break;
            case "closed":
                priorityColor = "#00b894";
                break;
            case "reopened":
                priorityColor = "#ff3b30";
                break;
            case "open":
                priorityColor = "#cc00ff";
                break;
            case "spam":
                priorityColor = "#c7c8cc";
                break;
            default:
                priorityColor = "#FFA600";
        }

        let html = `
        <div class="ticket-card active p-2 mb-2 border rounded cursor-pointer bg-light" data-id="${d.id}">
            
            <div class="d-flex gap-2">

                <!-- TAT BOX -->
                <div class="tat-box color-code-rose d-flex align-items-center justify-content-center rounded text-white fw-bold currentColor"
                    data-toggle="tooltip"
                    data-original-title="TAT"
                    style="width: 45px; height: 65px;">

                    <div class="text-center tat-content">
                        <i class="fa fa-clock-o"></i>
                        <div class="tat-time">${d.tat}:00</div>
                    </div>

                </div>

                <!-- DETAILS -->
                <div class="flex-grow-1">

                    <!-- TITLE + DATE -->
                    <div class="d-flex justify-content-between align-items-start mb-1">

                        <div class="fw-semibold text-truncate pe-2 subject-text"
                            title="${d.subject}">
                            ${truncateText(d.subject)}
                        </div>

                        <div class="small text-muted text-nowrap"
                            title="Created Date: ${created_at_format}">
                            ${date}
                        </div>

                    </div>

                    <!-- DEPARTMENT + TASK -->
                    <div class="d-flex align-items-center gap-1 text-muted small mb-1">

                        ${deptName ? `
                            <span class="dep" title="${deptName}">
                                ${truncateText(deptName)} |
                            </span>
                        ` : ""}

                        <span title="Task">
                            <i class="fa fa-tasks"></i> ${d.taskCount} Tasks
                        </span>

                    </div>

                    <!-- ASSIGNED USER -->
                    <div class="d-flex align-items-center gap-1 small text-muted mb-1">

                        <span>
                            <i class="fa fa-user"></i>
                        </span>

                        <span title="${assignedTo}">
                            ${truncateText(assignedTo)}
                        </span>

                    </div>

                    <!-- TAT EXPIRY -->
                    <div class="d-flex align-items-center gap-1 small text-muted">

                        <span>
                            <i class="fa fa-clock-o"></i>
                        </span>

                        <span class="tat_expiry" title="TAT Expiry">
                            ${d.tat_expire_format ?? ''}
                        </span>

                    </div>

                </div>

            </div>

        </div>
        `;
        t.lg.append(html);
        $('[title]').tooltip();
    };

    t.config.ticketId = null;
    let ticketCreatorId = null;
    t.renderField = function (label, value, alignRight = false) {
        if (!value || !value.toString().trim()) return '';

        return `
            <div class="col-md-6 ${alignRight ? 'text-right' : ''}">
                <strong>${label}:</strong> ${value}
            </div>
        `;
    }

    t.ticketdata = function (e) {
        e.preventDefault();
        $('.ticket-card').removeClass('active');
        $(this).addClass('active');
        t.config.ticketId = $(this).data('id');
        t.detail_api_loader.addClass('active');
        $.ajax({
            url: t.config.url.getArchivedTicketDetails,
            type: "GET",
            data: { id: t.config.ticketId },
            success: function (res) {
                t.detail_api_loader.removeClass('active');
                let d = res.data;
                let customFieldsHtml = '';
                let hasData = false;
                const fields = d.customFieldsFromTableForDepartments;
                if (fields && Object.keys(fields).length) {
                    let rowHtml = '';
                    let index = 0;
                    Object.values(fields).forEach(field => {
                        if (!field.value || !field.value.toString().trim()) return;
                        hasData = true;
                        const isRight = index % 2 !== 0;
                        rowHtml += t.renderField(field.column, field.value, isRight);
                        if (index % 2 !== 0) {
                            customFieldsHtml += `<div class="row created-updated">${rowHtml}</div>`;
                            rowHtml = '';
                        }
                        index++;
                    });
                    if (rowHtml) {
                        customFieldsHtml += `<div class="row created-updated">${rowHtml}</div>`;
                    }
                }

                let customFieldsSection = '';
                if (hasData && customFieldsHtml) {
                    customFieldsSection = `
                        <div class="accordion mb-3" id="customFieldAccordion">
                            <div class="accordion-item border rounded overflow-hidden">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed fw-semibold py-2 px-3"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapseCustomField">
                                        Custom Fields Data
                                    </button>
                                </h2>
                                <div id="collapseCustomField"
                                    class="accordion-collapse collapse">

                                    <div class="accordion-body py-2">
                                        ${customFieldsHtml}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                let assignedTo = d.assignedUser ? `${d.assignedUser.name}` : `N/A`;
                const createdViaMap = {
                    1: { text: "Portal", icon: "🌐" },
                    2: { text: "Chat", icon: "💬" },
                    3: { text: "Email", icon: "📧" },
                    4: { text: "Mobile", icon: "📱" },
                    5: { text: "Call", icon: "📞" },
                    6: { text: "Bot", icon: "🤖" }
                };
                const createdVia = createdViaMap[d.created_via] || {
                    text: "N/A",
                    icon: ""
                };
                const metaItems = [];
                if (d.priority?.name) {
                    metaItems.push(`<span class="text-success fw-semibold"><i class="fa fa-arrow-up"></i> ${d.priority.name}</span>`);
                }

                if (d.department?.name) {
                    metaItems.push(`<span class="border-start ps-3">${d.department.name}</span>`);
                }

                if (d.category?.name) {
                    metaItems.push(`<span class="border-start ps-3">${d.category.name}</span>`);
                }

                if (d.subCategory?.name) {
                    metaItems.push(`<span class="border-start ps-3">${d.subCategory.name}</span>`);
                }

                if (d.tat) {
                    metaItems.push(`<span class="border-start ps-3">${d.tat} Hrs</span>`);
                }
                let viewFormIcon = '';
                if (d.requestedForm && d.requestedForm.form_id && d.serviceRequest.form_type != 2) {
                    viewFormIcon = `
                        <span class="icon-btn">
                            <a href="${config.url.view_form}/${d.requestedForm.id}${d.b ? '?b=' + d.b : ''}"
                                target="_blank">
                                <i class="fa fa-wpforms"></i>
                            </a>
                        </span>
                    `;
                }
                if (d.requestedForm && d.requestedForm.form_id && d.serviceRequest.form_type == 2) {
                    viewFormIcon = `<span class="icon-btn">
                            <a href="${config.url.view_custom_form}/${d.requestedForm.id}${d.b ? '?b=' + d.b : ''}"
                                target="_blank">

                                <i class="fa fa-wpforms"></i>
                            </a>
                        </span>
                    `;
                }

                let html = `
                        <div class="ticket-detail-box-inner p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <h3 class="ticket-title fs-4 fw-bold mb-2 text-truncate"
                                        title="${d.subject}">
                                        ${d.subject}
                                    </h3>
                                    <div class="d-flex flex-wrap gap-3 text-muted small">
                                        ${metaItems.join('')}
                                    </div>
                                </div>

                                <div class="d-flex gap-2 ms-3 flex-shrink-0">
                                    ${viewFormIcon}
                                    <span class="icon-btn open-drawer"
                                        data-id="${d.id}"
                                        data-toggle="tooltip"
                                        title="History">
                                        <i class="fa fa-history"></i>
                                    </span>
                                </div>
                            </div>

                            <hr class="my-3">
                            <div class="row mb-3 small text-muted">
                                <div class="col-md-6">
                                    <strong>Created:</strong>
                                    ${d.created_at_format}
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <strong>Updated:</strong>
                                    ${d.updated_at_format}
                                </div>
                                <div class="col-md-6 mt-1">
                                    <strong>Resolved:</strong>
                                    ${d.resolved_at_format || 'N/A'}
                                </div>
                                ${d.closed_at_format ? `
                                      <div class="col-md-6 text-md-end mt-1">
                                          <strong>Closed:</strong>
                                          ${d.closed_at_format}
                                      </div>
                                    `
                        : ''
                    }

                                ${d.archived_at_format
                        ? `
                                    <div class="col-12 mt-1">
                                        <strong>Archived:</strong>
                                        ${d.archived_at_format}
                                    </div>
                                    `
                        : ''
                    }

                                ${(d.all_tags && d.all_tags.length)
                        ? `
                                        <div class="col-12 mt-2">
                                            <strong>Tags:</strong>
                                            ${(() => {
                            let html = '';
                            let limit = Math.min(3, d.all_tags.length);
                            for (let i = 0; i < limit; i++) {
                                html += `
                                                            <span class="badge bg-light text-dark border me-1">
                                                                ${d.all_tags[i].tags}
                                                            </span>
                                                        `;
                            }
                            if (d.all_tags.length > 3) {
                                let remainingTags = d.all_tags.slice(3).map(t => t.tags).join(', ');
                                html += `
                                                            <span class="badge bg-secondary"
                                                                title="${remainingTags}">
                                                                +${d.all_tags.length - 3}
                                                            </span>
                                                        `;
                            }
                            return html;
                        })()
                        }
                                        </div>
                                    `
                        : ''
                    }
                            </div>
                            ${customFieldsSection}
                            <div class="accordion mb-3 custom-accordion" id="descAccordion">
                                <div class="accordion-item border rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-semibold py-2 px-3" type="button" id="toggleAccordion">
                                            <span>Description</span>
                                            <i class="fa fa-chevron-up ms-auto accordion-icon"></i>
                                        </button>
                                    </h2>

                                    <div class="accordion-body py-2 custom-body open" id="accordionBody">
                                        <div class="ticket-content">
                                            ${d.rendered_content ?? ""}
                                        </div>
                                        <div id="main_attachments"
                                            class="main_attachments attachments mt-2">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            ${d.taskCount > 0 ? `<div class="task-card collapsed current" data-id="${d.id}">
                                <div class="task-card-header">
                                    
                                    <h3 style="color:#4B4A4A;font-size:17px;display:flex;align-items:center;gap:10px;">
                                        Related Task 
                                        
                                        <span class="total-task badge badge-light">
                                            ${d.taskCount}
                                        </span>

                                        <span class="expand-icon"> 
                                            <svg width="18" height="10" viewBox="0 0 18 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.296 2.79594L9.79596 10.2959C9.69144 10.4008 9.56725 10.484 9.4305 10.5408C9.29376 10.5976 9.14715 10.6268 8.99908 10.6268C8.85102 10.6268 8.70441 10.5976 8.56766 10.5408C8.43092 10.484 8.30672 10.4008 8.20221 10.2959L0.702208 2.79594C0.490864 2.58459 0.372131 2.29795 0.372131 1.99906C0.372131 1.70018 0.490864 1.41353 0.702208 1.20219C0.913552 0.990843 1.2002 0.872112 1.49908 0.872112C1.79797 0.872112 2.08461 0.990843 2.29596 1.20219L9.00002 7.90625L15.7041 1.20125C15.9154 0.989906 16.2021 0.871174 16.501 0.871174C16.7998 0.871174 17.0865 0.989906 17.2978 1.20125C17.5092 1.4126 17.6279 1.69924 17.6279 1.99813C17.6279 2.29701 17.5092 2.58366 17.2978 2.795L17.296 2.79594Z" fill="#000000"/>
                                            </svg>
                                        </span>
                                    </h3>

                                    <div class="task-progress-right">
                                        <span class="progress-count">
                                            ${d.completionPercentage}%
                                        </span>

                                        <div class="progress-bar mini-progress">
                                            <div class="fill" style="width:${d.completionPercentage}%"></div>
                                        </div>
                                    </div>

                                </div>

                                <div class="task-section"></div>

                            </div>` : ``}

                            <div class="ticket-comment p-3 border rounded">
                                <h5 class="mb-3 fs-6 fw-semibold">
                                    Ticket Comment :-
                                </h5>
                                <div id="ticket_timeline"
                                    class="no-bg hide"
                                    style="margin-top:10px">
                                </div>
                                <div class="load-comment text-center mt-3 hide">
                                    <button class="btn btn-outline-secondary rounded-circle p-2 load-more">
                                        <svg viewBox="0 0 24 24"
                                            fill="none"
                                            width="18"
                                            height="18">
                                            <path d="M12 3C12.5523 3 13 3.44772 13 4V17.5858L18.2929 12.2929C18.6834 11.9024 19.3166 11.9024 19.7071 12.2929C20.0976 12.6834 20.0976 13.3166 19.7071 13.7071L12.7071 20.7071C12.3166 21.0976 11.6834 21.0976 11.2929 20.7071L4.29289 13.7071C3.90237 13.3166 3.90237 12.6834 4.29289 12.2929C4.68342 11.9024 5.31658 11.9024 5.70711 12.2929L11 17.5858V4C11 3.44772 11.4477 3 12 3Z"
                                                fill="currentColor"/>
                                        </svg>
                                    </button>
                                    <div class="load-more-loader hide d-none">
                                        <i class="fa fa-spinner fa-spin"></i>
                                        Loading...
                                    </div>
                                </div>
                            </div>
                        </div>
                `;

                $('.ticket-detail-box').html(html);
                let locationRow = '';
                if (d.location && d.location.name) {
                    locationRow = `
                        <div class="info-row d-flex justify-content-between py-2 border-bottom">
                            <span class="fw-medium">
                                Location
                            </span>
                            <span>
                                ${truncateText(d.location.name)}
                            </span>
                        </div>
                    `;
                }

                let knowledgeHtml = '';
                if (d.knowledge_documents && d.knowledge_documents.length) {
                    knowledgeHtml = `
                        <div class="artical-section mt-4">
                            ${t.renderKnowledgeSection(d.knowledge_documents)}
                        </div>
                    `;
                }
                let procureTag = d?.serviceRequest?.procure_tag
                    ? ` (${d.serviceRequest.procure_tag})`
                    : '';
                let isStarred = false;
                if (d.starred) {
                    let arr = d.starred.split(",");
                    isStarred = arr.includes(config.auth.id.toString());
                }

                let ticketTagRow = '';
                if (d.ticket_tag) {
                    ticketTagRow = `
                        <div class="info-row d-flex justify-content-between py-2 border-bottom">
                            <span class="fw-medium">
                                Ticket Tag
                            </span>
                            <span class="badge bg-info-subtle text-info">
                                ${d.ticket_tag}
                            </span>
                        </div>
                    `;
                }

                let ticketInfo = `
                        <div class="p-4">
                            <div class="info-section small text-muted">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="fw-medium">
                                        Ticket ID
                                    </span>
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                                        #${t.config.ticketId}${procureTag}
                                    </span>
                                </div>

                                ${ticketTagRow}
                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <span class="fw-medium">
                                        Assigned To
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        ${d.assignedUser && d.assignedUser.id
                        ? `
                                                <img src="${d.assignedUser.avatar}"
                                                    class="rounded-circle"
                                                    width="24"
                                                    height="24">
                                                <a href="${t.config.url.userInfo}/${d.assignedUser.id}"
                                                    target="_blank"
                                                    class="text-decoration-none text-muted small">
                                                    ${truncateText(assignedTo)}
                                                </a>
                                            `
                        : `<span class="text-muted small">N/A</span>`
                    }
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <span class="fw-medium">
                                        Creator
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        ${d.creator && d.creator.avatar
                        ? `
                                                <img src="${d.creator.avatar}"
                                                    class="rounded-circle"
                                                    width="24"
                                                    height="24">
                                            `
                        : ''
                    }

                                        ${d.creator && d.creator.id
                        ? `
                                                <a href="${t.config.url.userInfo}/${d.creator.id}"
                                                    target="_blank"
                                                    class="text-decoration-none text-muted small text-truncate"
                                                    style="max-width:120px;">

                                                    ${truncateText(d.creator.name)}
                                                </a>
                                            `
                        : `<span class="text-muted small">N/A</span>`
                    }
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="fw-medium">
                                        Created Via
                                    </span>
                                    <span class="small text-muted">
                                        ${createdVia.icon} ${createdVia.text}
                                    </span>

                                </div>

                                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                                    <span class="fw-medium">
                                        Ticket Logger
                                    </span>
                                    <div class="d-flex align-items-center gap-2">
                                        ${d.logger && d.logger.avatar
                        ? `
                                                <img src="${d.logger.avatar}"
                                                    class="rounded-circle"
                                                    width="24"
                                                    height="24">
                                            `
                        : ''
                    }
                                        ${d.logger.id == 0
                        ? `<span class="small text-muted">System</span>`
                        : `
                                                <a href="${t.config.url.userInfo}/${d.logger.id}"
                                                    target="_blank"
                                                    class="text-decoration-none text-muted small">

                                                    ${truncateText(d.logger.name)}
                                                </a>
                                            `
                    }
                                    </div>
                                </div>
                                ${locationRow}
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="fw-medium">
                                        Feedback
                                    </span>
                                    <span class="small text-muted">
                                        ${d.feedback || 'N/A'}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="fw-medium">
                                        Starred
                                    </span>
                                    <span>${isStarred ? '<i class="fa fa-star text-warning"></i>' : '<i class="fa fa-star-o text-muted"></i>'}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <span class="fw-medium">
                                        TAT Expire At
                                    </span>
                                    <span>${d.tat_expire_format}</span>
                                </div>
                            </div>

                            ${knowledgeHtml ? `<div class="artical-section mt-4">
                                        <h5 class="mb-3 fs-6 fw-semibold">
                                            Knowledge Base
                                        </h5>
                                        ${knowledgeHtml}
                                    </div>
                                `: ''
                    }
                        </div>
                `;

                $('.ticket-info').html(ticketInfo);
                t.setMainAttachments(d.attachments);
                $('[title]').tooltip();
                t.config.refreshTimeLine();
            },
            error: function () {
                t.detail_api_loader.removeClass('active');
            }
        });
    };

    t.renderKnowledgeSection = function (docs) {
        if (!docs || !docs.length) {
            return '';
        }

        const slidesHtml = docs.map((doc, index) => {
            const tagsHtml = (doc.tags || []).map(tag => `<div class="tag">${tag}</div>`).join('');
            return `
                <div class="carousel-item item ${index === 0 ? 'active' : ''}">
                    <div class="col-md-12">
                        <a href="${config.url.article_view}/${doc.id}" target="_blank" style="text-decoration: none;">
                            <div class="card"
                                style="background-image:url('${doc.card_img}')">
                                <div class="big-kd">KD</div>

                                <div class="card-content">
                                    <div class="title" title="${doc.title}">
                                        <strong>${truncateText(doc.title, 30)}</strong>
                                    </div>

                                    <div class="tags">
                                        ${tagsHtml}
                                    </div>
                                </div>

                                <div class="arro_back">
                                    <svg class="hex-icon-color" width="39" height="39" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"></path>
                                        <path d="M45.8962 33.1566L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            `;
        }).join('');

        return `
            <div>
                <div id="kd">
                    <div class="home-slideshow">
                        <div id="home_main-slider"
                            class="carousel slide main-slider"
                            data-bs-ride="carousel">
                            <div class="carousel-inner">
                                ${slidesHtml}
                            </div>
                            <button class="carousel-control-prev"
                                type="button"
                                data-bs-target="#home_main-slider"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next"
                                type="button"
                                data-bs-target="#home_main-slider"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    };

    document.addEventListener("click", function (e) {
        const toggleAccordion = e.target.closest("#toggleAccordion");
        if (!toggleAccordion) return;
        const accordion = toggleAccordion.closest(".custom-accordion");
        const accordionBody = accordion.querySelector(".custom-body");
        const accordionIcon = accordion.querySelector(".accordion-icon");
        accordionBody.classList.toggle("open");
        accordionIcon.classList.toggle("rotate");
    });

    $(document).on("click", ".open-drawer", function (e) {
        e.preventDefault();
        let id = $(this).data("id");
        t.ticket_history(id);
        $("#drawer .drawer-header h4").text("Ticket History");
        $("#drawer").addClass("open");
        $("#drawer-overlay").addClass("active");
    });
    $(document).on("click", ".close-drawer, #drawer-overlay", function () {
        $("#drawer").removeClass("open");
        $("#drawer-overlay").removeClass("active");
    });


    t.ticket_history = function (id) {
        history_url = config.url.getArchivedTicketHistory;
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var http = $.ajax({
            url: history_url,
            type: "POST",
            data: {
                "_token": t.config.token,
                "id": id
            }
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.drawerModelBody.html('');
                    t.drawerModelBody.addClass('history-timeline')
                    var ticket_history = '';
                    if (data.data.length > 0) {
                        $.each(data.data, function (index, value) {
                            switch (value.action_type) {

                                // 1. Assigned Ticket
                                case 1:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        action: "Assigned Ticket",
                                        userAvatar: value.commenter_profile_img,
                                        description: `
                                            ${value.old_assigned_user_fullname
                                                ? `${value.old_assigned_user_fullname} → <b>${value.assigned_user_fullname}</b>`
                                                : `Assigned to <b>${value.assigned_user_fullname ?? "System"}</b>`}
                                        `,
                                        icon: t.config.icons['Assigned To'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 2. Status Changed
                                case 2:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        action: "Status changed",
                                        userAvatar: value.commenter_profile_img,
                                        description: ` ${value.old_status_name ?? ""} → <b>${value.status_name}</b>`,
                                        icon: t.config.icons['Status'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 3. Feedback
                                case 3:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        action: "Feedback",
                                        userAvatar: value.commenter_profile_img,
                                        description: `Rating star <b>${value.feedback}</b>`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 4. Marked Spam
                                case 4:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        action: "Marked as Spam",
                                        userAvatar: value.commenter_profile_img,
                                        description: `Ticket marked as spam`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 5. Removed Spam
                                case 5:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Removed from Spam",
                                        description: `Ticket removed from spam list`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 6. Ticket Merge
                                case 6:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Ticket merged",
                                        description: value.merge_primary
                                            ? `Merged with <b>${value.merge_primary}</b>`
                                            : `Primary Ticket Merged with <b>${value.merged_ids}</b>`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 7. Comment
                                case 7:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        userAvatar: value.commenter_profile_img,
                                        user: value.auto_response ? "MATI-AI" : value.commenter,
                                        action: value.is_note ? "Note added" : "Comment added",
                                        description: value.auto_response
                                            ? "Auto response by MATI-AI"
                                            : "User commented on ticket",
                                        icon: t.config.icons["Comment Added"],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 8. Priority Changed
                                case 8:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Priority changed",
                                        description: `${value.old_priority_name ?? ""} → <b>${value.priority_name}</b> `,
                                        icon: t.config.icons['Priority'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 9. TAT Changed
                                case 9:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "TAT changed",
                                        description: `${value.tat_changed ?? ""} → <b>${value.tat}</b> `,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 10. Department Transfer
                                case 10:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Department changed",
                                        description: `${value.old_dept_name ?? ""} → <b>${value.dept_name}</b>`,
                                        icon: t.config.icons['Department'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 11. Category Transfer
                                case 11:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Category changed",
                                        description: ` ${value.old_pbm_cat_name ?? ""} → <b>${value.pbm_cat_name}</b> `,
                                        icon: t.config.icons['Category'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 12. Sub Category Transfer
                                case 12:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Sub-category changed",
                                        description: `${value.old_sub_cat_name ?? ""} → <b>${value.sub_cat_name}</b>`,
                                        icon: t.config.icons['Category'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 14. Ticket Created
                                case 14:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Ticket created",
                                        description: `New ticket created`,
                                        icon: t.config.icons["Created"],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 15. Ticket Type Updated
                                case 15:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Ticket type updated",
                                        description: value.ticket_type_custom_fields,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 16. Creator Changed
                                case 16:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Creator changed",
                                        description: `${value.old_change_creator_user_fullname ?? ""} → <b>${value.creator_user_fullname}</b>`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 17. Custom Field Updated
                                case 17:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Custom field updated",
                                        description: value.custom_fields,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 18. Calendar Event
                                case 18:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Calendar booked",
                                        description: `Event added to calendar`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 19. Device Changed
                                case 19:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Device changed",
                                        description: `${value.old_device_tag ?? ""} → <b>${value.device_tag}</b>`,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 20–21. Custom Form History
                                case 20:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Custom Form updated",
                                        description: generateChangeHistoryTable(value.new_custom_field_values),
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;
                                case 21:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Custom Form updated",
                                        description: generateChangeHistoryTable(value.new_custom_field_values),
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 22. Task Operation
                                case 22:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Task updated",
                                        description: value.remarks,
                                        icon: t.config.icons['Comment Added'],
                                        mode: 'ticket',
                                    });
                                    break;

                                // 23–24. Edit Category / Sub Category
                                case 23:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Ticket updated",
                                        description: `Category updated`,
                                        icon: t.config.icons['Category'],
                                        mode: 'ticket',
                                    });
                                    break;
                                case 24:
                                    ticket_history += t.config.renderTimeline({
                                        time: value.updated_at_format,
                                        user: value.commenter,
                                        userAvatar: value.commenter_profile_img,
                                        action: "Ticket updated",
                                        description: `Category / Sub-category updated`,
                                        icon: t.config.icons['Category'],
                                        mode: 'ticket',
                                    });
                                    break;
                            }

                        });
                    } else {
                        ticket_history += '<div class="tml-content"><p>' + config.translations.Ticket_History_Not_Available + '</p></div>';
                    }
                    t.drawerModelBody.append(ticket_history);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };
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
            return "<p>Invalid JSON format</p>";
        }
    }
    t.taskHistoryMdl = t.page.find("#taskHistoryModal");
    t.taskHistoryContainer = t.taskHistoryMdl.find("#task_history_container");
    t.taskHistoryList = t.taskHistoryMdl.find("#task_history_list");
    t.taskHistoryLoader = t.taskHistoryMdl.find(".task-history-loader");

    t.page.on("click", ".read-more", function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    });
    $(document).on("click", ".btn-mdl-reload", function (e) {
        e.preventDefault();
        t.dt1.ajax.reload();
    });
    document.addEventListener('click', function (e) {
        if (e.target.closest('.task-card-header')) {
            const taskCard = e.target.closest('.task-card');
            if (taskCard) {
                taskCard.classList.toggle('collapsed');
                t.relatedTask();
            }
        }
    });


    t.relatedTask = function (e) {
        $.ajax({
            url: t.config.url.getRelatedTask,
            type: "GET",
            data: {
                ticket_id: t.config.ticketId,
            },
            success: function (response) {
                // Override success for demo

                if (response.data && response.data.length > 0) {
                    var ticketCreatorId = 694;

                    var visibleTasks = response.data.filter(function (task) {
                        if (t.config.user.id === ticketCreatorId) {
                            return task.is_visible_user == 1;
                        }
                        return true;
                    });

                    if (visibleTasks.length > 0) {
                        var totalTask = visibleTasks.length || response.totaltask || 0;
                        var completedTask = response.completedtask || 0;

                        var taskTabs =
                            '<div class="task-tabs-container">' +
                            '<ul class="nav nav-tabs task-tabs" id="taskTabs">';

                        var taskContents1 = '<div class="box-container"><div class="tab-content" id="taskContents1">';
                        var taskContents2 = '<div class="tab-content" id="taskContents2">';
                        var taskComment = '<div class="tab-comment" id="taskComment">';
                        var taskbutton = '<div class="tab-button" id="taskbutton">';

                        visibleTasks.forEach(function (task, index) {
                            var activeClass = index === 0 ? 'show active' : '';
                            var iconClass = task.progress.iconClass || 'fa-circle';
                            var borderColor = task.progress.borderColor || '#000';
                            var per = task.progress.per || '0';

                            taskTabs += `
                                <li class="task-tab ${activeClass}">
                                    <a href="#task${task.task_no}" 
                                    class="task-tab-link ${activeClass}" 
                                    data-toggle="tab"
                                    data-task-no="${task.task_no}" 
                                    data-task-name="${task.task_name}" 
                                    data-assign-to="${task.assigned_to_id}" 
                                    data-status="${task.status}"
                                    data-color="${borderColor}"
                                    data-creator = "${task.creator_id}"
                                    data-status-id = "${task.status_id}"
                                    data-change_by_module="${task.change_by_module}"
                                    data-encrypt-task-id = "${task.task_encrypt_id}"
                                    data-icon-color="${borderColor}"> <!-- NEW LINE -->
                                    #${task.task_no}
                                    <i class="fa ${iconClass}" style="margin-left: 5px; color: ${borderColor};"
                                        data-toggle="tooltip" title="${task.progress.status || "No Status"}"></i>
                                    </a>
                                </li>`;

                            taskContents1 +=
                                '<div id="task' + task.task_no + '" class="tab-pane fade ' + activeClass + '">' +
                                '<div class="box top-left">' +
                                '<div class="task-details">' +
                                '<div class="col-md-5">' +
                                '<div class="task-info">' +
                                '<p class="task-label">Task Name</p>' +
                                '<p class="task-name" id="task-name" data-bs-toggle="tooltip" data-bs-title="' + (task.task_name || '') + '">' +
                                (task.task_name && task.task_name.length > 25 ? task.task_name.substring(0, 25) + '...' : (task.task_name || "No Task Name")) +
                                '</p>' +
                                '</div>' +
                                '<div style="font-size:12px;color:#666;margin-top:4px;">Created By Module :<b>' +
                                (task.change_by_module === 3 ? 'Problem Category' :
                                    task.change_by_module === 2 ? 'Ticket' :
                                        task.change_by_module === 1 ? 'Task' : '') +
                                '</b></div>' +
                                '</div>' +
                                '<div class="col-md-5">' +
                                '<div class="task-meta">' +
                                '<div class="task-progress">' +
                                '<span data-bs-toggle="tooltip" data-bs-title="' + per + '%" style="display:inline-flex;align-items:center;">' +
                                '<svg width="40" height="40" viewBox="0 0 36 36" class="circular-progress">' +
                                '<circle class="circle-bg" cx="18" cy="18" r="16" stroke-width="4"></circle>' +
                                '<circle class="circle" cx="18" cy="18" r="16" stroke-width="3.8" ' +
                                'stroke="' + (per > 0 ? borderColor : 'none') + '" ' +
                                'stroke-dasharray="' + (per > 0 ? per + ', 100' : '0, 100') + '" ' +
                                'stroke-dashoffset="' + (per > 0 ? 100 - per : 100) + '">' +
                                '</circle>' +
                                '<foreignObject x="10" y="10" width="16" height="16">' +
                                '<i class="fa ' + iconClass + '" style="color:' + borderColor + ';font-size:16px;display:block;text-align:center;"></i>' +
                                '</foreignObject>' +
                                '</svg>' +
                                '</span>' +
                                '<span class="progress-text">' + (task.progress.status || '') + '</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="task-details mt-3">' +
                                '<div class="col-md-5">' +
                                '<div class="task-info">' +
                                '<p class="task-label">Status</p>' +
                                '<span class="task-status" style="background:#DBFFEC;color:#0B8431">' + (task.status || "No Status") + '</span>' +
                                '</div>' +
                                '</div>' +
                                '<div class="col-md-5">' +
                                '<div class="task-assignee">' +
                                '<div class="assignee-info">' +
                                (task.assign_to_avatar ? '<img src="' + task.assign_to_avatar + '" alt="' + (task.assigned_to || '') + '" class="assignee-avatar">' : '') +
                                '<div class="assignee-details">' +
                                '<p class="task-label">Assign to</p>' +
                                '<span class="assignee-name" data-bs-toggle="tooltip" data-bs-title="' + (task.assigned_to && task.assigned_to.trim() !== '' ? task.assigned_to : 'NA') + '">' +
                                (task.assigned_to && task.assigned_to.trim() !== '' ? (task.assigned_to.length > 9 ? task.assigned_to.substring(0, 9) + '...' : task.assigned_to) : 'NA') +
                                '</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="box bottom-left">' +
                                '<div class="task-details">' +
                                '<div class="col-md-5">' +
                                '<div class="timeline-vertical">' +
                                '<div class="timeline-item">' +
                                '<div class="timeline-icon">' +
                                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none">' +
                                '<path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>' +
                                '</svg>' +
                                '</div>' +
                                '<div class="timeline-content">' +
                                '<p class="timeline-lebal">Start Date</p>' +
                                '<p class="timeline-date">' + (task.start_date || 'N/A') + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="timeline-item">' +
                                '<div class="timeline-icon">' +
                                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none">' +
                                '<path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>' +
                                '</svg>' +
                                '</div>' +
                                '<div class="timeline-content">' +
                                '<p class="timeline-lebal">End Date</p>' +
                                '<p class="timeline-date">' + (task.due_date || 'N/A') + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="col-md-5">' +
                                '<div class="timeline-vertical">' +
                                '<div class="timeline-item">' +
                                '<div class="timeline-icon">' +
                                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none">' +
                                '<path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>' +
                                '</svg>' +
                                '</div>' +
                                '<div class="timeline-content">' +
                                '<p class="timeline-lebal">Created At</p>' +
                                '<p class="timeline-date">' + (task.created_at || 'N/A') + '</p>' +
                                '</div>' +
                                '</div>' +
                                '<div class="timeline-item">' +
                                '<div class="timeline-icon">' +
                                '<svg width="12" height="12" viewBox="0 0 12 12" fill="none">' +
                                '<path d="M6 0C5.21207 0 4.43185 0.155195 3.7039 0.456723C2.97595 0.758251 2.31451 1.20021 1.75736 1.75736C0.632141 2.88258 0 4.4087 0 6C0 7.5913 0.632141 9.11742 1.75736 10.2426C2.31451 10.7998 2.97595 11.2417 3.7039 11.5433C4.43185 11.8448 5.21207 12 6 12C7.5913 12 9.11742 11.3679 10.2426 10.2426C11.3679 9.11742 12 7.5913 12 6C12 5.21207 11.8448 4.43185 11.5433 3.7039C11.2417 2.97595 10.7998 2.31451 10.2426 1.75736C9.68549 1.20021 9.02405 0.758251 8.2961 0.456723C7.56815 0.155195 6.78793 0 6 0ZM8.52 8.52L5.4 6.6V3H6.3V6.12L9 7.74L8.52 8.52Z" fill="#ff8080"/>' +
                                '</svg>' +
                                '</div>' +
                                '<div class="timeline-content">' +
                                '<p class="timeline-lebal">Updated at</p>' +
                                '<p class="timeline-date">' + (task.updated_at || 'N/A') + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>';

                            taskContents2 +=
                                '<div id="task_copy' + task.task_no + '" class="tab-pane fade ' + activeClass + '">' +
                                '<div class="box top-right">' +
                                '<div style="padding:20px 10px;">' +
                                '<p class="task-label" style="margin-bottom:6px;">Priority</p>' +
                                '<span class="task-priority">' + (task.priority || "NA") + '</span>' +
                                '</div>' +
                                '<div style="padding:6px 0 4px 10px;">' +
                                '<div class="assignee-info">' +
                                (task.creator_avatar ? '<img src="' + task.creator_avatar + '" alt="Creator" class="assignee-avatar">' : '') +
                                '<div class="assignee-details">' +
                                '<p class="task-label">Creator</p>' +
                                '<span class="assignee-name" data-bs-toggle="tooltip" data-bs-title="' + (task.creator && task.creator.trim() !== '' ? task.creator : 'NA') + '">' +
                                (task.creator && task.creator.trim() !== '' ? (task.creator.length > 9 ? task.creator.substring(0, 9) + '...' : task.creator) : 'NA') +
                                '</span>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '</div>' +
                                '<div class="box bottom-right">' +
                                '<div class="timeline-block">' +
                                '<div class="icon">' +
                                '<svg width="21" height="21" viewBox="0 0 21 21" fill="none">' +
                                '<g opacity="0.4"><circle cx="10.2144" cy="10.3281" r="9.5" fill="white" stroke="#E3200B"/><path d="M10.2144 4.32812C9.42642 4.32812 8.64621 4.48332 7.91825 4.78485C7.1903 5.08638 6.52887 5.52833 5.97171 6.08548C4.8465 7.2107 4.21436 8.73683 4.21436 10.3281C4.21436 11.9194 4.8465 13.4455 5.97171 14.5708C6.52887 15.1279 7.1903 15.5699 7.91825 15.8714C8.64621 16.1729 9.42642 16.3281 10.2144 16.3281C11.8057 16.3281 13.3318 15.696 14.457 14.5708C15.5822 13.4455 16.2144 11.9194 16.2144 10.3281C16.2144 9.54019 16.0592 8.75998 15.7576 8.03202C15.4561 7.30407 15.0141 6.64264 14.457 6.08548C13.8998 5.52833 13.2384 5.08638 12.5105 4.78485C11.7825 4.48332 11.0023 4.32813 10.2144 4.32812ZM12.7344 12.8481L9.61436 10.9281V7.32812H10.5144V10.4481L13.2144 12.0681L12.7344 12.8481Z" fill="#E3200B"/></g>' +
                                '</svg>' +
                                '</div>' +
                                '<div class="label">Actual End Date</div>' +
                                '<div class="date">' + (task.end_date && task.end_date != ' ' ? task.end_date : 'Not Available') + '</div>' +
                                '</div>' +
                                '<div class="timeline-block">' +
                                '<p class="icon"><svg width="21" height="21" viewBox="0 0 21 21" fill="none"><g opacity="0.4"><circle cx="10.2144" cy="10.3281" r="9.5" fill="white" stroke="#E3200B"/><path d="M14.0654 4.87109L13.4195 6.51739H11.7334C11.9631 6.7962 12.0751 7.10365 12.1325 7.4645H14.0654L13.4195 9.11068H12.1327C11.9895 9.84064 11.6151 10.3181 11.0758 10.7398C10.4516 11.1978 9.73822 11.4381 9.02853 11.5046L13.0561 15.785H10.4365L6.75309 11.6242V9.97748C7.5935 9.96075 8.23574 10.0517 8.97846 9.81855C9.4104 9.66767 9.77855 9.52676 9.90659 9.11079H6.36328L7.00917 7.46482H9.89929C9.74326 7.02998 9.39979 6.75148 9.00923 6.64768C8.47755 6.52243 7.93794 6.51814 7.43286 6.5176H6.36328L7.00917 4.87142L14.0654 4.87109Z" fill="#E3200B"/></g></svg></p>' +
                                '<p class="label">Cost</p>' +
                                '<p class="date">' + (task.cost || "0.00") + '</p>' +
                                '</div>' +
                                '</div>' +
                                '</div>';
                        });

                        taskTabs += '</ul></div>';
                        taskComment += '<div id="task_timeline" class="no-bg hide" style="margin-top:10px"></div></div>';
                        taskContents1 += '</div>';
                        taskContents2 += '</div></div>';
                        taskbutton += '</div>';

                        var completedPercentage = 0;
                        if (totalTask > 0) {
                            completedPercentage = Math.round((completedTask / totalTask) * 100);
                        }
                        $(".task-card-header").find('h4 span').html(completedPercentage + '%');
                        $(".progress-bar .fill").css('width', completedPercentage + '%');
                        $(".task-card-header").find('.total-task').html(totalTask);

                        $(".task-section").html(taskTabs + taskbutton + taskContents1 + taskContents2 + taskComment);
                        t.triggerFirstTab();
                        // Initialize Bootstrap 5 tooltips
                        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        tooltipTriggerList.forEach(function (el) {
                            new bootstrap.Tooltip(el, {
                                boundary: 'window',
                                offset: [0, 10],
                                container: 'body'
                            });
                        });
                    }
                }
            },
            error: function (error) {
                console.log("Error fetching tasks:", error);
            }
        });
    };

    $(document).on('click', '.task-tab-link', function () {
        t.activateTaskTab($(this));
        t.refreshTaskTimeLine();
    });


    t.activateTaskTab = function ($tab) {
        t.taskNo = $tab.data('task-no');
        assign_to = $tab.data('assign-to');
        t.taskStatus = $tab.data('status');
        t.statusId = $tab.data('status-id');
        t.taskCreator = $tab.data('creator');
        t.change_by_module = $tab.data('change_by_module');
        t.encrypt_task_id = $tab.data('encrypt-task-id');
        $(".comment-section").remove();
        $('#taskContents2 .tab-pane').removeClass('active in');
        $('#task_copy' + t.taskNo).addClass('active in');

        $('.task-tab-link').each(function () {
            const $link = $(this);

            // remove active state
            $link.removeClass('active');
            $link.closest('.task-tab').removeClass('active');

            // inactive icon color
            $link.find('i').css('color', '#9ca3af');
        });

        // active current tab
        $tab.addClass('active');
        $tab.closest('.task-tab').addClass('active');

        // active icon color
        $tab.find('i').css('color', '#16a34a');

        let taskbutton = `<div class="task-actions mt-2 d-flex gap-2 text-right">`;
        let taskComment = ``;
        if (jQuery.inArray("TaskHistory", t.config.permissions) !== -1) {
            taskbutton += `<button class="btn dtActbtn history-task" data-id="${t.taskNo}" data-toggle="tooltip" title="Task History"><i class="fa fa-history"></i></button>`;
        }
        taskbutton += `<a href="${t.config.url.archived_info}/${t.encrypt_task_id}" target="_blank" class="btn dtActbtn info-task" data-toggle="tooltip" title="Task Info"><i class="fa fa-info-circle"></i></a>`;
        taskbutton += `</div>`;
        $("#task_timeline").after(taskComment);
        $("#taskbutton").html(taskbutton);

        t.taskTimeline = $(document).find("#task_timeline");
        t.taskAttachmentView = function (e) {
            e.preventDefault();
            const $clicked = $(e.currentTarget);
            const type = $clicked.attr('data-view_mode');

            if (type == "1") {
                const images = [];

                $(".tri-view[data-view_mode='1']").each(function () {
                    images.push({
                        href: $(this).attr("data-view"),
                        title: $(this).attr("data-name")
                    });
                });

                const clickedIndex = $(".tri-view[data-view_mode='1']").index($clicked);
                $.swipebox(images, {
                    initialIndexOnArray: clickedIndex,
                    hideCloseButtonOnMobile: false,
                    removeBarsOnMobile: false
                });
            }
        };


        t.taskAttachmentDownload = function (e) {
            e.preventDefault();
            window.location = $(this).attr('data-url');
        }

        t.timelineData = [];
        t.timelineDisplayIndex = 0;

        t.refreshTaskTimeLine = function () {
            var url = t.config.url.get_task_timeline;
            var formData = new FormData();
            formData.append('id', t.taskNo);
            formData.append('ticketmodule', 'true');

            var http = $.ajax({
                url: url,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function (data) {
                if (typeof data == "object" && data.status == "success") {
                    t.taskTimeline.empty();
                    t.timelineData = data.data;
                    t.timelineDisplayIndex = 0;

                    t.loadMoreTimelineEntries(2);

                    if (t.timelineData.length > 2) {
                        if (!$('#loadMoreTimeline').length) {
                            $("#commentWrapper").append(`
                                   <div id="loadMoreTimeline">
                                        <button class="btn">
                                            View more
                                        </button>
                                    </div>
                                `);
                            $('#loadMoreTimeline button').on('click', function () {
                                t.loadMoreTimelineEntries(2);
                                t.taskTimeline.css({
                                    'overflow-y': 'scroll',
                                    'max-height': '350px',
                                    'scrollbar-width': 'thin',
                                    'scrollbar-color': '#D7D7D7 transparent',
                                });
                            });
                        }
                    } else {
                        $('#loadMoreTimeline').remove();
                    }
                    t.taskTimeline.off("click", ".tri-view").on("click", ".tri-view", $.proxy(t.taskAttachmentView));
                    t.taskTimeline.removeClass("hide");
                    t.taskTimeline.off("click", ".tri-view").on("click", ".tri-view", $.proxy(t.taskAttachmentView));
                } else if (data.msg != "") {
                    sweetAlert('center', 'error', data);
                }
            });
        };

        t.loadMoreTimelineEntries = function (count) {
            var start = t.timelineDisplayIndex;
            var end = Math.min(start + count, t.timelineData.length);
            if (!$('#commentWrapper').length && t.timelineData.length > 0) {
                const totalComments = t.timelineData.length;
                t.taskTimeline.append(`
                        <div id="timelineTitle" style="font-weight: 600; font-size: 20px; margin-bottom: 10px;color:#4A4B4B">
                            Task Comments (${totalComments})
                        </div>
                        <div id="commentWrapper" class="comment-wrapper" style="border: 1px solid #eee; border-radius: 10px; padding: 10px; background: #fff;"></div>
                    `);
            }
            for (var i = start; i < end; i++) {
                var v = t.timelineData[i];

                var class_name = "";
                if (v.updated_by == v.assigned_to && v.assigned_to != null) {
                    class_name = "color-code-bar color-code-blue-text";
                } else if (v.updated_by == v.creator_id && v.creator_id != null) {
                    class_name = "color-code-bar color-code-rose-text";
                } else if (v.updated_by != v.creator_id && v.creator_id != null) {
                    class_name = "color-code-bar color-code-yellow-text";
                }

                var previewText = v.remarks.length > 100 ? v.remarks.slice(0, 100) : v.remarks;

                var attachmentsHtml = "";
                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                    var at = [];
                    $.each(v.attachments, function (i, v2) {
                        var sext = v2.ext.toLowerCase();
                        var eye_link = "";
                        if (["png", "jpeg", "jpg"].includes(sext)) {
                            eye_link = '<span class="tri-view" data-toggle="tooltip" data-original-title="View" data-view_mode="1" data-view="' + t.config.url.view_task_attachment + '/' + v2.id + '" data-name="' + decodeURIComponent(v2.name) + '"><i class="fa fa-eye"></i></span>';
                        }
                        var ai_bg = v2.thumb == 1 ? "ai-bg" : "";
                        var bg = v2.thumb == 1 ? 'background-image: url(' + t.config.url.view_task_attachment + '/' + v2.id + '/1)' : '';

                        at.push(
                            `<div class="attach-item ${ai_bg}" style="${bg}">
                                    <div class="attach-item-cntnt">
                                        <span class="attach-name">${decodeURIComponent(v2.name)}</span>
                                        <div class="icons">
                                            ${eye_link}
                                            <span class="tri-download" data-toggle="tooltip" data-original-title="Download" data-url="${t.config.url.download_task_attachment}/${v2.id}"><i class="fa fa-download"></i></span>
                                        </div>
                                    </div>
                                </div>`
                        );
                    });

                    attachmentsHtml = `<div class="attachments" style="margin-top: 10px;"><div class="text-bold" style="margin-bottom: 5px;">Attachments:</div>${at.join("")}</div>`;
                }

                var singleComment = `
                    <div class="single-comment" style="display: flex; position: relative; padding: 10px 4px 20px 4px;margin-top:5px; ${v.is_note != null && v.is_note == 1 ? 'background-color:#fffcd3;box-shadow:0 1px 1px rgba(0,0,0,.05);' : ''}">
                    
                        <div class="left-column" style="width: 30px; display: flex; flex-direction: column; align-items: center; position: relative;">
                            <img src="${v.profile_img}" alt="User Icon" style="width: 32px; height: 32px; border-radius: 50%;">
                            <div class="vertical-line" style="flex: 1; width: 1px; background: #ddd; margin: 5px 0;"></div>
                        </div>

                        <div class="right-content" style="flex: 1; padding-left: 15px;">
                            <div style="margin-bottom: 5px; font-size: 14px; color: #000000;">
                                ${v.is_note != null && v.is_note == 1 ? 'Note Added By' : 'Commented By'}
                                <strong class="${class_name}">${v.commenter || 'Anonymous'}</strong>
                            </div>

                            <div class="comment-preview" style="font-size: 14px; color: #444; margin-bottom: 5px;">
                                ${previewText}
                            </div>

                            <div class="comment-full" style="display: none; font-size: 14px; color: #444; margin-bottom: 5px;">
                                ${v.remarks}
                                ${attachmentsHtml}
                            </div>
                        </div>

                        <div class="toggle-comment" style="cursor: pointer; margin-left: 10px; padding-top: 5px;">
                            <i class="fa fa-chevron-down" style="background:#F7F7F7;padding: 10px;border-radius: 20px;"></i>
                        </div>
                        <div class="comment-timestamp" style="position: absolute;margin:5px; bottom: -5px; left: 0; width: 100%; display: flex; justify-content: flex-start; font-size: 14px; color: #666; font-weight: 600;">
                            ${v.updated_at_format}
                        </div>
                    </div>`;
                $('#commentWrapper').append(singleComment);
                if ($('#loadMoreTimeline').length) {
                    $('#loadMoreTimeline').appendTo('#commentWrapper');
                }
                t.timelineDisplayIndex = end;
                if (t.timelineDisplayIndex >= t.timelineData.length) {
                    $('#loadMoreTimeline').remove();
                }
            }
        };

        t.taskTimeline.on("click", ".tri-view", $.proxy(t.taskAttachmentView));
        t.taskTimeline.on("click", ".tri-download", $.proxy(t.taskAttachmentDownload));
        t.refreshTaskTimeLine();
    }

    $(document).off('click', '.toggle-comment').on('click', '.toggle-comment', function () {
        const $comment = $(this).closest('.single-comment');
        const $preview = $comment.find('.comment-preview');
        const $full = $comment.find('.comment-full');

        if ($full.is(':visible')) {
            $full.hide();
            $preview.show();
            $(this).find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $full.show();
            $preview.hide();
            $(this).find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

    t.triggerFirstTab = function () {
        const $firstTab = $('.task-tab-link').first();
        if ($firstTab.length) {
            t.activateTaskTab($firstTab);
        }
    }


    $(document).on('click', ".history-task", function (e) {
        e.preventDefault();
        var taskId = $(this).data('id');
        var historyUrl = config.url.ArchivedTaskhistory;
        var token = t.config.token || $('meta[name="csrf-token"]').attr('content');
        TaskHistory.open(taskId, historyUrl, token);
    });


    t.truncateHtml = function (html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    t.escapeHtml = function (text) {
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

    t.page.on("click", ".read-more", function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    })

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#historyTable_wrapper .plain-search").validate_str_param();
        if (v === false) {
            alert(t.config.translations.please_enter_valid_search);
            return false;
        }
        t.dt1.search(v).draw();
    };
    var select2Opts = {
        width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 25 ? data.text.substring(0, 25) + '...' : data.text;
        }
    };
    var select2StatusOpts = $.extend({}, select2Opts, {
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 20 ? data.text.substring(0, 20) + '...' : data.text;
        }
    });

    t.renderSortFields = function () {
        var sf = t.sortbtns.find(".sort-fields");
        var s = t.config.sort_dir;
        var $dirSort = t.sortbtns.find(".dir-sort");
        if (s.dir == 1) {
            $dirSort.find("#sortAscSvg").removeClass("hidden");
            $dirSort.find("#sortDescSvg").addClass("hidden");
        } else {
            $dirSort.find("#sortAscSvg").addClass("hidden");
            $dirSort.find("#sortDescSvg").removeClass("hidden");
        }
        $dirSort.attr("data-id", s.id);
        sf.empty();

        $.each(t.config.sort_fields, function (i, d) {
            $('<li class="list-group-item ' + (d.id == s.id ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text +
                '</li>').appendTo(sf);
        });
    };
    t.sortFieldChanged = function (e) {
        e.preventDefault();
        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(() => {
            var id = $(this).attr("data-id");

            if (t.config.sort_dir.id == id) {
                t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
            } else {
                t.config.sort_dir.id = id;
            }
            t.lg.empty();
            t.pageNumber = 1;
            autoClickedFirstTicket = true;
            t.renderSortFields();
            t.load();
        }, 300);
    };
    t.download = function (e) {
        e.preventDefault();
        // if (t.config.user.download_limits != "1") {
        //     return;
        // }
        sweetAlertConfirmation({
            message: "By default 6 month data will be exported only. If any date filter is applied then max of 6 month data is exported only.",
            onConfirm: function () {
                t.cache_filter_values();
                window.location = t.config.url.export_tickets + "?q=" + t.config.export_filters + "&order_id=" + t.config.sort_dir.id + "&order_dir=" + t.config.sort_dir.dir + "&company_id=" + companyId;
            }
        });
    }
    t.setMainAttachments = function (attachments) {
        var at = [];
        $.each(attachments, function (i, v) {
            var sext = v.ext.toLowerCase();
            var eye_link = "";
            if (sext == "png" || sext == "jpeg" || sext == "jpg") {
                eye_link = '<span class="tri-view" data-view_mode="1" data-view="' + t.config.url.attachment_view + '/' + v.id + '" data-name="' + decodeURIComponent(v.name) + '"><i class="fa fa-eye"></i></span>';
            }

            var ai_bg = v.thumb == 1 ? "ai-bg" : "";
            var bg = v.thumb == 1 ? "background-image: url(" + t.config.url.attachment_view + "/" + v.id + "/1" : "";

            at.push('<div class="attach-item ' + ai_bg + '" style="' + bg + '" ><div class="attach-item-cntnt"><span class="attach-name">' + t.config.decodeURIComponentSafe(unescape(v.name)) + '</span><div class="icons">' + eye_link + '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '"><i class="fa fa-download"></i></span></div></div></div>');
        });

        if (at.length > 0) {
            $('#main_attachments').html('<div><div class="text-bold mar-rgt">Attachments:</div>' + at.join("") + '</div>');
        }
    }

    t.attachmentView = function (e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        if (type == 1) {
            $.swipebox([{ href: $(this).attr('data-view'), title: $(this).attr('data-name') }]);
        }
    };

    t.attachmentDownload = function (e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }

    t.toggleSingleTinyViewer = function (e) {
        e.preventDefault();
        var el = $(e.currentTarget).closest('.card').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };

    t.setSingleTinyViewer = function (el, target_val) {
        var card = $(el).closest('.card');
        var content = card.find('.tml-content');
        if (target_val == true) {
            card.css('height', '');
            card.css('height', 'auto');
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-compress"></i>').attr('title', 'Compress');
        } else {
            card.css('height', '');
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-expand faa-fast animated"></i>').attr('title', 'Expand');
        }
    };
    t.renderSortFields();
    t.sortbtns.find(".sort-fields").on("click", "li", t.sortFieldChanged);
    t.sortbtns.on("click", ".dir-sort", t.sortFieldChanged);
    $(document).on("click", ".btn-reload", function () {
        t.lg.empty();
        t.pageNumber = 1;
        autoClickedFirstTicket = true;
        t.load();
    });
    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = t.searchbox.find("input").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.lg.empty();
            t.pageNumber = 1;
            autoClickedFirstTicket = true;
            t.load();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.lg.empty();
            t.pageNumber = 1;
            autoClickedFirstTicket = true;
            t.load();
        }
    };

    t.btn.filter.on("click", t.search);
    t.btn.clear.on("click", t.btnClrFilter);
    $(document).on('click', '.ticket-card', t.ticketdata);
    t.searchbox.on("keypress", "input", t.search);
    $(document).on("click", ".btn-download", t.download);
    t.page.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    $(document).on('click', '#ticket_timeline .tri-view', t.attachmentView);
    $(document).on('click', '#ticket_timeline .tri-download', t.attachmentDownload);
    $(document).on("click", "#main_attachments .tri-view", $.proxy(t.attachmentView));
    $(document).on("click", "#main_attachments .tri-download", $.proxy(t.attachmentDownload));
    t.load();
};

var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.page = $("#page_boxed");
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
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + name + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };
    t.config.renderTimeline = function ({
        time,
        user,
        userAvatar,
        action,
        description,
        icon,
        mode = 'request'
    }) {
        const modifiedStatusHtml = (mode === 'ticket')
            ? `
                <div class="history-detail-info-modified-status">
                    <div class="history-detail-info-modified-by">
                        <div class="modfied-by-image">
                            <img src="${userAvatar}" alt="avatar">
                        </div>
                        <span class="modfied-by-name">${user}</span>
                    </div>
                    <span class="history-detail-info-modified-status-middot">ᐧ</span>
                    <a>${action}</a>
                </div>
            `
            : `
                <div class="history-detail-info-modified-status">
                    <a>${action}</a>
                    <span class="history-detail-info-modified-status-middot">ᐧ</span>
                    <div class="history-detail-info-modified-by">
                        <div class="modfied-by-image">
                            <img src="${userAvatar}" alt="avatar">
                        </div>
                        <span class="modfied-by-name">${user}</span>
                    </div>
                </div>
            `;

        return `
        <div class="history-timeline-item">
            <div class="history-icon-container">
                <div class="history-timeline-icon">${icon}</div>
                <div class="history-timeline-line"></div>
            </div>

            <div class="history-detail-info-container">
                <div class="history-detail-info">

                    <div class="history-detail-info-time-row">
                        <div class="history-detail-info-time-text">
                            <svg viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="gray"></path>
                            </svg>
                            <span class="history-detail-info-time-row-title">${time}</span>
                        </div>
                        <a class="accordion-toggle">
                            <svg class="history-detail-info-accrodian-icon" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 0C10.4288 0 7.91543 0.762437 5.77759 2.19089C3.63975 3.61935 1.97351 5.64968 0.989572 8.02511C0.0056327 10.4006 -0.251811 13.0144 0.249797 15.5362C0.751405 18.0579 1.98953 20.3743 3.80762 22.1924C5.6257 24.0105 7.94208 25.2486 10.4638 25.7502C12.9856 26.2518 15.5994 25.9944 17.9749 25.0104C20.3503 24.0265 22.3807 22.3603 23.8091 20.2224C25.2376 18.0846 26 15.5712 26 13C25.9964 9.5533 24.6256 6.24882 22.1884 3.81163C19.7512 1.37445 16.4467 0.00363977 13 0ZM13 24C10.8244 24 8.69767 23.3549 6.88873 22.1462C5.07979 20.9375 3.66989 19.2195 2.83733 17.2095C2.00477 15.1995 1.78693 12.9878 2.21137 10.854C2.6358 8.72021 3.68345 6.7602 5.22183 5.22183C6.76021 3.68345 8.72022 2.6358 10.854 2.21136C12.9878 1.78692 15.1995 2.00476 17.2095 2.83732C19.2195 3.66989 20.9375 5.07979 22.1462 6.88873C23.3549 8.69767 24 10.8244 24 13C23.9967 15.9164 22.8367 18.7123 20.7745 20.7745C18.7123 22.8367 15.9164 23.9967 13 24ZM18.7075 10.2925C18.8005 10.3854 18.8742 10.4957 18.9246 10.6171C18.9749 10.7385 19.0008 10.8686 19.0008 11C19.0008 11.1314 18.9749 11.2615 18.9246 11.3829C18.8742 11.5043 18.8005 11.6146 18.7075 11.7075L13.7075 16.7075C13.6146 16.8005 13.5043 16.8742 13.3829 16.9246C13.2615 16.9749 13.1314 17.0008 13 17.0008C12.8686 17.0008 12.7385 16.9749 12.6171 16.9246C12.4957 16.8742 12.3854 16.8005 12.2925 16.7075L7.2925 11.7075C7.10486 11.5199 6.99945 11.2654 6.99945 11C6.99945 10.7346 7.10486 10.4801 7.2925 10.2925C7.48015 10.1049 7.73464 9.99944 8 9.99944C8.26537 9.99944 8.51987 10.1049 8.70751 10.2925L13 14.5862L17.2925 10.2925C17.3854 10.1995 17.4957 10.1258 17.6171 10.0754C17.7385 10.0251 17.8686 9.99921 18 9.99921C18.1314 9.99921 18.2615 10.0251 18.3829 10.0754C18.5043 10.1258 18.6146 10.1995 18.7075 10.2925Z" fill="gray"></path>
                            </svg>
                        </a>
                    </div>
                 
                    ${modifiedStatusHtml}

                    <div class="history-detail-info-changed">
                        <span class="changed-from" style="font-size:13px;">
                            ${description}
                        </span>
                    </div>

                    <div class="accordion-content">
                        <div class="accordion-content-inner"></div>
                    </div>

                </div>
            </div>
        </div>`;
    };

    $(document).on("click", ".accordion-toggle", function (e) {
        e.preventDefault();
        const $timelineItem = $(this).closest(".history-timeline-item");
        $timelineItem.toggleClass("open");
    });
    t.config.icons = {
        "Created": `
                <svg width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.9991 11C21.99 7.8857 21.8915 6.23467 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H11.5"
                    stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M17.5 14V20" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>
                <path d="M14 17H21" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M10 16H6" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>

                <path d="M2 10L22 10" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>
                </svg>`,
        "Comment Added": `<svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.9363 6.86578C17.9363 3.57907 17.7957 2.69574 16.7916 1.67469C15.7875 0.653641 14.1712 0.653641 10.9389 0.653641H7.51044C4.27807 0.653641 2.6619 0.653641 1.65773 1.67469C0.653564 2.69574 0.653564 4.33909 0.653564 7.62581C0.653564 10.9125 0.653564 12.5559 1.65773 13.5769C2.6619 14.598 2.98858 14.5779 6.22095 14.5779H7.51853" stroke="#c8cbd3" stroke-width="1.30728" stroke-linecap="round"/>
                    <path d="M7.51047 11.1119H4.08203" stroke="#c8cbd3" stroke-width="1.30728" stroke-linecap="round"/>
                    <path d="M0.653564 5.88278H17.7957" stroke="#c8cbd3" stroke-width="1.30728" stroke-linecap="round"/>
                    <path d="M16.6446 7.76083C17.4063 7.76083 18.0238 8.34622 18.0238 9.06833V13.4266C18.0238 14.1488 17.4063 14.7341 16.6446 14.7341H11.2812L9.74879 15.8237C9.36996 16.093 8.82935 15.8368 8.82935 15.3879V9.06833C8.82935 8.34622 9.44682 7.76083 10.2085 7.76083H16.6446ZM16.6446 8.63249H10.2085C9.95461 8.63249 9.74879 8.82762 9.74879 9.06833V14.7341L10.7295 14.0368C10.8887 13.9237 11.0822 13.8625 11.2812 13.8625H16.6446C16.8985 13.8625 17.1043 13.6673 17.1043 13.4266V9.06833C17.1043 8.82762 16.8985 8.63249 16.6446 8.63249ZM12.9668 11.6833C13.2207 11.6833 13.4266 11.8784 13.4266 12.1191C13.4266 12.3427 13.2491 12.5269 13.0204 12.552L12.9668 12.555H11.5877C11.3338 12.555 11.1279 12.3598 11.1279 12.1191C11.1279 11.8956 11.3054 11.7114 11.5341 11.6862L11.5877 11.6833H12.9668ZM15.2654 9.93999C15.5193 9.93999 15.7252 10.1351 15.7252 10.3758C15.7252 10.6165 15.5193 10.8116 15.2654 10.8116H11.5877C11.3338 10.8116 11.1279 10.6165 11.1279 10.3758C11.1279 10.1351 11.3338 9.93999 11.5877 9.93999H15.2654Z" fill="#c8cbd3"/>
                    </svg> `,
        "Priority": `<svg width="18px" height="18px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21.9991 11C21.99 7.8857 21.8915 6.23467 20.8284 5.17157C19.6569 4 17.7712 4 14 4H10C6.22876 4 4.34315 4 3.17157 5.17157C2 6.34315 2 8.22876 2 12C2 15.7712 2 17.6569 3.17157 18.8284C4.34315 20 6.22876 20 10 20H11.5" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M15.5 14V20M15.5 20L17.5 18M15.5 20L13.5 18M20 20V14M20 14L22 16M20 14L18 16" stroke="#c8cbd3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 16H6" stroke="#c8cbd3" stroke-width="1.2" stroke-linecap="round"/>
                        <path d="M2 10L22 10" stroke="#c8cbd3" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>`,
        "Status": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2028_2)">
                        <path d="M7.69591 13.9222L5.94103 13.9222C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14412 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.0725 8.14014" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17251 10.6118H3.89835" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.7869 9.48873C16.0401 9.38536 16.3292 9.5068 16.4325 9.75996C17.2594 11.7853 16.2879 14.0975 14.2626 14.9244C13.0936 15.4017 11.8287 15.2793 10.8106 14.7025C10.5727 14.5677 10.4891 14.2655 10.6239 14.0276C10.7587 13.7897 11.0608 13.7061 11.2987 13.8409C12.0643 14.2747 13.0119 14.3654 13.8883 14.0076C15.4072 13.3874 16.1359 11.6533 15.5157 10.1343C15.4123 9.88111 15.5338 9.59209 15.7869 9.48873ZM9.89136 12.0108C9.92403 12.1346 9.96512 12.2579 10.015 12.3801C10.0649 12.5023 10.1218 12.6191 10.1852 12.7304C10.3204 12.9681 10.2374 13.2704 9.9997 13.4056C9.76203 13.5409 9.45973 13.4578 9.32449 13.2202C9.24003 13.0717 9.16431 12.9164 9.0982 12.7544C9.03209 12.5925 8.97741 12.4285 8.93386 12.2634C8.86411 11.999 9.02191 11.7281 9.28632 11.6583C9.55073 11.5886 9.82161 11.7464 9.89136 12.0108ZM10.0219 9.0812C10.2582 9.21896 10.338 9.52212 10.2002 9.75834C10.0693 9.98274 9.96817 10.2235 9.89948 10.4741C9.82718 10.7378 9.55478 10.893 9.29106 10.8207C9.02734 10.7484 8.87216 10.476 8.94447 10.2122C9.03591 9.87871 9.17056 9.55822 9.34479 9.25947C9.48255 9.02326 9.78572 8.94344 10.0219 9.0812ZM13.8103 7.43629C14.1439 7.52773 14.4643 7.66239 14.7631 7.83661C14.9993 7.97437 15.0791 8.27754 14.9414 8.51375C14.8036 8.74997 14.5005 8.82979 14.2642 8.69203C14.0398 8.56117 13.799 8.45999 13.5485 8.3913C13.2848 8.319 13.1296 8.0466 13.2019 7.78288C13.2742 7.51916 13.5466 7.36398 13.8103 7.43629ZM12.3642 7.77814C12.434 8.04255 12.2762 8.31343 12.0117 8.38318C11.8879 8.41585 11.7646 8.45694 11.6424 8.50681C11.5203 8.55669 11.4034 8.61366 11.2921 8.67699C11.0545 8.81223 10.7522 8.72919 10.6169 8.49152C10.4817 8.25385 10.5647 7.95155 10.8024 7.81631C10.9508 7.73185 11.1062 7.65613 11.2681 7.59002C11.4301 7.52391 11.594 7.46924 11.7592 7.42568C12.0236 7.35593 12.2945 7.51373 12.3642 7.77814Z" fill="#c8cbd3"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2028_2">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>`,
        "Assigned To": `<svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.1905 8.34122C19.1905 4.81527 19.0911 2.89197 18.0139 1.79659C16.9367 0.70122 15.2028 0.70122 11.7352 0.70122H8.05716C4.58951 0.70122 2.85569 0.70122 1.77843 1.79659C0.701172 2.89197 0.701172 4.65494 0.701172 8.18089C0.701172 11.7068 0.701172 13.4699 1.77843 14.5652C2.85569 15.6606 4.58951 15.6606 8.05716 15.6606H9.09019" stroke="#c8cbd3" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M8.05714 11.9207H4.37915" stroke="#c8cbd3" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M0.701172 6.31099H19.0911" stroke="#c8cbd3" stroke-width="1.40244" stroke-linecap="round"/>
                        <path d="M19.3593 15.9812C19.3593 15.1476 18.5603 14.4386 17.445 14.1757M16.4878 15.9812C16.4878 14.924 15.2021 14.0668 13.6162 14.0668C12.0303 14.0668 10.7446 14.924 10.7446 15.9812M16.4878 12.6311C17.545 12.6311 18.4022 11.7739 18.4022 10.7167C18.4022 9.65938 17.545 8.80229 16.4878 8.80229M13.6162 12.6311C12.5589 12.6311 11.7018 11.7739 11.7018 10.7167C11.7018 9.65938 12.5589 8.80229 13.6162 8.80229C14.6735 8.80229 15.5306 9.65938 15.5306 10.7167C15.5306 11.7739 14.6735 12.6311 13.6162 12.6311Z" stroke="#c8cbd3" stroke-width="0.957191" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>`, "Department": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_2032_22)">
                        <path d="M8 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571V6.78556" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17248 10.6118H3.89832" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M16.9764 7.79395H9.56935C9.29475 7.79395 9.07249 7.98121 9.07249 8.21132V14.433C9.07249 14.6631 9.29475 14.8503 9.56935 14.8503H16.9761C17.2504 14.8503 17.473 14.6631 17.473 14.433V8.21132C17.4733 7.98121 17.2504 7.79395 16.9764 7.79395ZM16.4796 14.0164H10.0662V8.62869H16.4793L16.4796 14.0164ZM15.6684 12.8275L10.8585 12.808L13.2815 9.73557L15.6684 12.8275Z" fill="#c8cbd3"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_2032_22">
                        <rect width="18.145" height="15.28" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
            `,
        "Category": `
                    <svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.00429 13.9222H5.94103C2.85412 13.9222 2.54214 13.9414 1.58315 12.9662C0.624176 11.9912 0.624176 10.4217 0.624176 7.28293C0.624176 4.14411 0.624176 2.57472 1.58315 1.59961C2.54214 0.624512 4.08558 0.624512 7.17249 0.624512H10.4467C13.5335 0.624512 15.0771 0.624512 16.036 1.59961C16.9949 2.57472 17.1292 3.4183 17.1292 6.5571L17.1008 7.34862" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M7.17251 10.6118H3.89835" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path d="M0.624176 5.61816H16.9949" stroke="#c8cbd3" stroke-width="1.24845" stroke-linecap="round"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.7271 7.4541C10.4945 7.4541 10.3059 7.64267 10.3059 7.87528C10.3059 8.10789 10.4945 8.29645 10.7271 8.29645H15.7813C16.0139 8.29645 16.2024 8.10789 16.2024 7.87528C16.2024 7.64267 16.0139 7.4541 15.7813 7.4541H10.7271ZM9.96077 8.71763C9.44025 8.71763 9.04431 9.18503 9.12988 9.69847L9.83184 13.9102C9.89953 14.3164 10.251 14.6141 10.6627 14.6141H15.8456C16.2574 14.6141 16.6088 14.3164 16.6765 13.9102L17.3785 9.69847C17.4641 9.18503 17.0681 8.71763 16.5476 8.71763H9.96077ZM9.96077 9.55998H16.5476L15.8456 13.7717H10.6627L9.96077 9.55998Z" fill="#c8cbd3"/>
                    </svg>`,
    }
    t.config.refreshTimeLine = function () {
        url = t.config.url.get_timeline_archived;
        var class_name = '';
        var currentPage = 1;
        var perPage = 3;
        t.timeline = $("#ticket_timeline");
        console.log(t.timeline);
        t.toggle_tiny_view = t.page.find('#toggle_tiny_view');
        t.loadRecords = function (page) {
            var formData = new FormData();
            formData.append('_token', t.config.token);
            formData.append('id', t.config.ticketId);
            formData.append('page', page);
            formData.append('per_page', perPage);
            var http = $.ajax({
                url: url,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData
            });
            http.done(function (data) {
                console.log(data);
                if (typeof data == "object") {
                    if (data.status == "success") {
                        var exi_el_state = {};
                        t.timeline.find('.timeline-entry').each(function (exi_ind, exi_el) {
                            exi_el_state['e' + exi_el.id] = $(exi_el).hasClass('tiny-view-on');
                        });

                        if (page === 1) {
                            t.timeline.empty();
                        }
                        if (data.data.length > 0) {
                            t.timeline.removeClass("hide");
                            var tiny_viewer_state = t.toggle_tiny_view.hasClass('tiny-view-on');
                            $.each(data.data, function (i, v) {
                                if (v.updated_by == v.assigned_to && v.assigned_to != null) {
                                    class_name = "color-code-bar color-code-blue-text";
                                } else if (v.updated_by == v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-rose-text";
                                } else if (v.updated_by != v.creator_id && v.creator_id != null) {
                                    class_name = "color-code-bar color-code-yellow-text";
                                }
                                let feedback_comments = v.action_type == 3 ? '(This is a feedback)' : '';

                                var t2 = v.is_note == 1 ? "Note Added By" : "Commented By";
                                var profileImage = '<img src="' + v.profile_img + '" alt="Profile" class="rounded-circle" width="35" height="35">';
                                var c = v.is_note == 1 ? "tl-note-background" : "";
                                var s = v.is_service_request == 1 ? "service-note" : "";
                                if (v.action_type == 6) {
                                    c = "tl-merge";
                                    if (v.is_merge_primary == 1) {
                                        try {
                                            var merged_items = [];
                                            $.each(t.config.data.merged_ids.split(","), function (j, x) {
                                                merged_items.push('<a target="_blank" href="' + t.config.url.current_page + '/' + x + '" class="btn-link text-main text-bold">#' + x + '</a>');
                                            });

                                            t2 = 'Merged with ' + merged_items.join(", ") + ' by ';
                                        } catch (e) {
                                            console.log("e :", e);
                                        }
                                    } else {
                                        t2 = 'Merged with <a target="_blank" href="' + t.config.url.current_page + '/' + v.merge_primary + '" class="btn-link text-main text-bold">#' + v.merge_primary + '</a> by ';
                                    }
                                }

                                var cc_emails = v.cc_emails != "" && v.cc_emails != null ? " | CC: " + v.cc_emails : "";
                                var form = "";
                                if (typeof v.ticket_status_form_id != "undefined" && v.ticket_status_form_id != null) {
                                    form = '<a href="' + config.url.view_status_form + '/' + v.ticket_status_form_id + '?b=archived' + '" class="btn-ex-com" target="_blank" data-toggle="tooltip" data-title="View Form" data-placement="right"><i class="fa fa-wpforms fa-lg"></i></a>';
                                }
                                var tec_location = "";
                                if (typeof v.latitude !== "undefined" && v.latitude !== null && typeof v.longitude !== "undefined" && v.longitude !== null) {
                                    tec_location = `<span class="btn-ex-com tech-location" target="_blank" data-toggle="tooltip" data-html="true" data-title="<i>Address:</i> ${v.tech_address ?? ''}" data-placement="bottom">&nbsp;<i class="fa fa-map-marker fa-lg"></i></span>`;
                                }
                                var toggleIcon = '';
                                if (v.is_workaround != null && v.is_workaround != '') {
                                    toggleIcon = `<span class="badge"> Is this workaround valid${data.tkt_data.ticket_creator == t.config.user.id ? `?<i class="fa fa-times invalid_workaround" data-tfid="${v.tfid}" data-toggle="tooltip" data-title="Click 'x' if creator not satisfied with the workaround sla" data-placement="bottom"></i>` : ''}</span>`;
                                } else if (v.is_workaround == 0) {
                                    toggleIcon = `<span class="badge">Is Workaround</span>&nbsp;<span class="badge">Is Invalid</span>`;
                                }
                                var t3 = '<p class="mar-no pad-btm txt1 force-br">' + profileImage + ' ' + ' <span class="text-main text-bold ' + class_name + '" style="font-size:medium;">' + (v.auto_response ? ' MATI-AI' : v.commenter) + '</span> ' + form + feedback_comments + toggleIcon + cc_emails + '</p>'; var t5 = "";
                                var t5 = "";
                                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                    var at = [];
                                    var totalAttachments = v.attachments.length;
                                    $.each(v.attachments, function (i, v) {
                                        var sext = v.ext.toLowerCase();
                                        var file_icon = "";
                                        var eye_link = "";

                                        if (sext === "png" || sext === "jpeg" || sext === "jpg") {
                                            file_icon = '<i class="fa fa-file-image-o file-icon image-icon"></i>';
                                            eye_link =
                                                '<span class="tri-view" data-view_mode="1" data-view="' +
                                                t.config.url.attachment_view +
                                                "/" +
                                                v.id +
                                                '" data-name="' +
                                                decodeURIComponent(v.name) +
                                                '" data-toggle="tooltip" data-title="View"><i class="fa fa-eye"></i></span>';
                                        } else if (sext === "pdf") {
                                            file_icon = '<i class="fa fa-file-pdf-o file-icon pdf-icon"></i>';
                                        } else if (sext === "xlsx" || sext === "xls") {
                                            file_icon = '<i class="fa fa-file-excel-o file-icon excel-icon"></i>';
                                        } else {
                                            file_icon = '<i class="fa fa-file-o file-icon"></i>';
                                        }

                                        var fileSize = v.size ? (v.size / (1024 * 1024)).toFixed(2) + " MB" : "Unknown size";
                                        if (v.size < 1024 * 1024) {
                                            fileSize = (v.size / 1024).toFixed(2) + " KB";
                                        }

                                        at.push(
                                            '<div class="attachment-item">' +
                                            '<div class="attachment-content">' +
                                            file_icon +
                                            '<div class="file-details">' +
                                            '<span class="file-name">' + t.config.decodeURIComponentSafe(unescape(v.name)) + '</span>' +
                                            '<div class="file-info-actions">' +
                                            '<span class="file-size">' + fileSize + '</span>' +
                                            '<div class="attachment-actions">' +
                                            eye_link +
                                            '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '" data-toggle="tooltip" data-title="Download">' +
                                            '<i class="fa fa-download"></i></span>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>'
                                        );

                                        // at.push('<div class="attach-item"><div>' + eye_link + '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '"><i class="fa fa-download"></i> Download</span></div><span class="attach-name">' + v.name + '</span></div>'); 
                                    });
                                    if (at.length > 0) {
                                        t5 =
                                            '<div class="attachment-header">' +
                                            '<i class="fa fa-paperclip"></i> Attachments (' + totalAttachments + ')</div>' +
                                            '<div class="attachment-container">' +
                                            at.join("") +
                                            "</div>";
                                    }
                                }
                                var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                                var signle_tiny_viewer = tiny_viewer_state == true ? 'fa-expand faa-fast animated' : 'fa-compress';

                                if (typeof exi_el_state['e' + v.tfid] != 'undefined') {
                                    if (exi_el_state['e' + v.tfid] == true) {
                                        tiny_view_class = 'tiny-view-on';
                                        signle_tiny_viewer = 'fa-expand faa-fast animated';
                                    }
                                    else {
                                        tiny_view_class = '';
                                        signle_tiny_viewer = 'fa-compress';
                                    }
                                }
                                if (v.is_service_request == 1) {
                                    var s2 = '<div class="card-body ' + s + '">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="fa ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container" style="font-size: larger;color: #3d3d3d;">' + v.remarks + '</div>' + // Container to hold multiple remarks
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +  // Footer remains in place
                                        '<span style="color:#000000; font-size:14px; font-weight:550">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.id + '" class="card timeline-entry tiny-view ' + tiny_view_class + '">' + s2 + '</div>');
                                } else {
                                    var t4 = '<div class="card-body">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="fa ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container" style="font-size: larger;color: #3d3d3d;">' + v.remarks + '</div>' +
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +
                                        '<span style="color:#000000; font-size:14px; font-weight:550">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.id + '" class="card timeline-entry tiny-view ' + tiny_view_class + " " + c + '">' + t4 + '</div>');
                                }
                            });
                            if (data.data.length < perPage) {
                                $('.load-comment').addClass('hide');
                            } else {
                                $('.load-comment').removeClass('hide');
                                $('.load-more').removeClass('hide');
                            }
                        } else {
                            if (page === 1) {
                                t.timeline.removeClass("hide");
                            }
                            $('.load-comment').addClass('hide');
                        }
                    } else if (data.msg != "") {
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.always(function () {
                $('.load-more-loader').addClass('hide');
            });
        }
        t.loadRecords(currentPage);
        $('.load-comment').off("click").on('click', '.load-more', function () {
            currentPage++;
            $('.load-more').addClass('hide');
            $('.load-more-loader').removeClass('hide');
            t.loadRecords(currentPage);
            t.timeline.css({ 'overflow-y': 'scroll', 'max-height': '500px', 'scrollbar-width': 'thin' });
        });
    };
    t.config.decodeURIComponentSafe = function (uri, mod) {
        var out = new String(),
            arr,
            i = 0,
            l,
            x;
        typeof mod === "undefined" ? mod = 0 : 0;
        arr = uri.split(/(%(?:d0|d1)%.{2})/);
        for (l = arr.length; i < l; i++) {
            try {
                x = decodeURIComponent(arr[i]);
            } catch (e) {
                x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
            }
            out += x;
        }
        return out;
    }
}
