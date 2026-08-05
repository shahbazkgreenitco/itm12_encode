var ArchivedServiceRequestList = function (config) {
    var t = this;
    t.config = config;
    t.page = $("#request-archived-list-wrapper");
    t.lg = t.page.find("#lg");
    t.pagebtns = t.page.find("#pagebtns");
    t.pageBtmSummary = t.page.find("#page-btm-summary");
    t.visibleContent = t.page.find(".btn-visible-content");
    t.sortbtns = t.page.find(".sort-buttons");
    t.searchbox = t.page.find(".search");
    t.loader = t.page.find(".loader");
    t.nonloader = t.page.find(".nonloader");
    t.api_loader = t.page.find("#api_loader");
    t.detail_api_loader = t.page.find("#detail_api_loader");
    t.filterMdl = t.page.find("#srFilterModal");
    t.total = 0;
    t.perPage = 10;
    t.pageLimiter = t.page.find("#pageLimiter");

    t.filters = {
        wrapper: t.filterMdl,
        data: {
            problem_categories: {}
        }
    };
   
    t.filters.status = t.page.find("#filter_by_status"),
    t.filters.priority = t.page.find("#filter_by_priority"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category"),
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category"),
    t.filters.pab = t.filters.wrapper.find("#filter_by_pab"),
    t.filters.filter_by_approver = t.filters.wrapper.find("#filter_by_approver"),
    t.filters.hierarchy_approval = t.filters.wrapper.find("#hierarchy_approval"),
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date"),
    t.filters.approval = t.filters.wrapper.find("#filter_by_approval"),
    t.filters.from_date = t.filters.wrapper.find("#filter_by_from_date"),
    t.filters.to_date = t.filters.wrapper.find("#filter_by_to_date"),
    t.filters.daterange = t.filters.wrapper.find("#daterange"),
    t.filters.current_pab = t.filters.wrapper.find("#filter_by_current_pab"),
    t.filters.btnfilterclr = t.page.find(".btn-clear-filter"),
    t.filters.btnfilter = t.page.find(".btn-filter")

    t.filters.fun = {
        reload_status: function() {
            t.filters.status.empty().append(new Option("", "", false, false));
            $.each(t.config.statuses, function(i, k) {
                t.filters.status.append(new Option(k.name, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        reload_department: function() {
            t.filters.department.empty().append(new Option("", "", false, false));
            $.get(t.config.url.departments_service_request, function(data) {
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
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        t.filters.problem_category.empty().append(new Option("", "", false, false));
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
        reload_pab: function() {
            t.filters.pab.empty().append(new Option("","", true, true));
            $.each(t.config.pabs, function(i, k) {
                t.filters.pab.append(new Option(k.name, k.id, false, false));
            });
            t.filters.pab.trigger("change");
        },
        reload_current_pab: function() {
            t.filters.current_pab.empty().append(new Option("", "", true, true));
            $.each(t.config.pabs, function(i, k) {
                t.filters.current_pab.append(new Option(k.name, k.id, false, false));
            });
            t.filters.current_pab.trigger("change");
        },
        reload_approver: function() {
            t.filters.filter_by_approver.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.filter_by_approver.parent(),
                ajax: {
                    url: t.config.url.getApproverSR,
                    dataType: "json",
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300
                },
                allowClear: true,
                placeholder: "Select Approver",
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "0px";
                    return t.config.userDropdownFormat(data, imgPaddingLeft);
                },
            }));
            t.filters.filter_by_approver.trigger("change");
        },
    };

    t.pageLimiter.on("change", function () {
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
                    $('#ticket-data').addClass('d-none');
                    $('#no-data').removeClass('d-none');
                    $('#no-data').html(`
                        <div class="no-record-found text-center" style="padding: 30px;">
                            <img src="${config.url.nodataImage}" alt="No Data" 
                                style="max-width: 400px; margin-bottom: 15px; display: block; margin-left: auto; margin-right: auto;">
                            <div style="color:#777; font-size:16px;">
                                No Archived Service Request Found
                            </div>
                        </div>
                    `);  
                }                  
                if (data.data && data.data.length > 0) {
                    $('#ticket-data').removeClass('d-none');
                    $('#no-data').addClass('d-none');
                    $.each(data.data, t.renderList);
                    if (autoClickedFirstTicket === true && data.page === 1){
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

    t.config.refreshTimeLine = function () {
        url = t.config.url.get_timeline_archived;
        var class_name = '';
        var currentPage = 1;
        var perPage = 3;
        t.timeline = $("#ticket_timeline");
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
                            t.timeline.removeClass("d-none");
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
                                var profileImage = '<img src="' + v.profile_img + '" alt="Profile" class="rounded-circle" width="25" height="25">';
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
                                    form = '<a href="' + config.url.view_status_form + '/' + v.ticket_status_form_id + '?b=archived' + '" class="btn-ex-com" target="_blank" data-bs-toggle="tooltip" title="View Form" data-placement="right"><i class="bi bi-ui-checks fs-5"></i></a>';
                                }
                                var tec_location = "";
                                if (typeof v.latitude !== "undefined" && v.latitude !== null && typeof v.longitude !== "undefined" && v.longitude !== null) {
                                    tec_location = `<span class="btn-ex-com tech-location" target="_blank" data-toggle="tooltip" data-html="true" data-title="<i>Address:</i> ${v.tech_address ?? ''}" data-placement="bottom">&nbsp;<i class="bi bi-geo-alt-fill fs-5"></i></span>`;
                                }
                                var toggleIcon = '';
                                if (v.is_workaround != null && v.is_workaround != '') {
                                    toggleIcon = `<span class="badge"> Is this workaround valid${data.tkt_data.ticket_creator == t.config.user.id ? `?<i class="bi bi-x invalid_workaround" data-tfid="${v.tfid}" data-toggle="tooltip" data-title="Click 'x' if creator not satisfied with the workaround sla" data-placement="bottom"></i>` : ''}</span>`;
                                } else if (v.is_workaround == 0) {
                                    toggleIcon = `<span class="badge">Is Workaround</span>&nbsp;<span class="badge">Is Invalid</span>`;
                                }
                                var t3 = '<span class="mar-no pad-btm txt1 force-br">' + profileImage + ' ' + ' <span class="text-main text-bold ' + class_name + '" style="font-size:medium;">' + (v.auto_response ? ' MATI-AI' : v.commenter) + '</span> ' + form + feedback_comments + toggleIcon + cc_emails + '</span>'; var t5 = "";
                                var t5 = "";
                                if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                    var at = [];
                                    $.each(v.attachments, function (i, a) {
                                        var sext = a.ext.toLowerCase();
                                        var isImage = ["png", "jpeg", "jpg", "gif", "webp"].includes(sext);
                                        var fileIcon = 'bi bi-file-earmark text-secondary';
                                        if (sext == "pdf") {
                                            fileIcon = 'bi bi-file-earmark-pdf text-danger';
                                        } else if (["xls", "xlsx", "csv"].includes(sext)) {
                                            fileIcon = 'bi bi-file-earmark-excel text-success';
                                        } else if (["doc", "docx"].includes(sext)) {
                                            fileIcon = 'bi bi-file-earmark-word text-primary';
                                        } else if (isImage) {
                                            fileIcon = 'bi bi-image text-primary';
                                        }
                                        var actions = '';
                                        if (isImage) {
                                            actions +=
                                                '<span class="tri-view text-secondary small cursor-pointer" ' +
                                                'data-view_mode="1" ' +
                                                'data-view="' + t.config.url.attachment_view + '/' + a.id + '" ' +
                                                'data-name="' + decodeURIComponent(a.name) + '">' +
                                                '<i class="bi bi-eye"></i>' +
                                                '</span>';
                                        }
                                        actions +=
                                            '<span class="tri-download text-secondary small cursor-pointer ms-2" ' +
                                            'data-url="' + t.config.url.attachment_download + "/" + a.id + '">' +
                                            '<i class="bi bi-download"></i>' +
                                            '</span>';
                                        at.push(
                                            '<div class="d-flex align-items-center bg-light border rounded-4 px-2 py-2 mb-2 col-md-4">' +
                                            '<div class="bg-white border rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width:30px;height:30px;">' +
                                            '<i class="' + fileIcon + '"></i>' +
                                            '</div>' +
                                            '<div class="flex-grow-1 text-truncate fw-medium small text-dark pe-2">' +
                                            decodeURIComponentSafe(unescape(a.name)) +
                                            '</div>' +
                                            '<div class="d-flex align-items-center flex-shrink-0">' +
                                            actions +
                                            '</div>' +
                                            '</div>'
                                        );
                                    });

                                    t5 =
                                        '<div class="mt-2">' +
                                        '<div class="fw-semibold small mb-2">' +
                                        'Attachment:' +
                                        '</div><div class="row mx-0 gap-2 px-1">' +
                                        at.join("") +
                                        '</div></div>';
                                }
                                var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                                var signle_tiny_viewer = tiny_viewer_state == true
                                    ? 'bi bi-arrows-angle-expand'
                                    : 'bi bi-arrows-angle-contract';

                                if (typeof exi_el_state['e' + v.tfid] != 'undefined') {
                                    if (exi_el_state['e' + v.tfid] == true) {
                                        tiny_view_class = 'tiny-view-on';
                                        signle_tiny_viewer = 'bi bi-arrows-angle-expand';
                                    } else {
                                        tiny_view_class = '';
                                        signle_tiny_viewer = 'bi bi-arrows-angle-contract';
                                    }
                                }
                                if (v.is_service_request == 1) {
                                    var s2 = '<div class="card-body ' + s + '">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="fa ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container">' + v.remarks + '</div>' + // Container to hold multiple remarks
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +  // Footer remains in place
                                        '<span class="px-4">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.id + '" class="card timeline-entry tiny-view ' + tiny_view_class + '">' + s2 + '</div>');
                                } else {
                                    var t4 = '<div class="card-body">' +
                                        '<button class="btn btn-white btn-ex-com single-tiny-viewer" style="position:absolute;right:2px;"><i class="fa ' + signle_tiny_viewer + '"></i></button>' +
                                        t3 +
                                        '<div class="tml-content-container">' + v.remarks + '</div>' +
                                        t5 +
                                        '</div>' +
                                        '<div class="card-footer">' +
                                        '<span class="px-4">' + v.updated_at_format + '</span>' +
                                        '</div>';
                                    t.timeline.append('<div id="' + v.id + '" class="card timeline-entry tiny-view ' + tiny_view_class + " " + c + '">' + t4 + '</div>');
                                }
                            });
                            if (data.data.length < perPage) {
                                $('.load-comment').addClass('d-none');
                            } else {
                                $('.load-comment').removeClass('d-none');
                                $('.load-more').removeClass('d-none');
                            }
                        } else {
                            if (page === 1) {
                                t.timeline.removeClass("d-none");
                            }
                            $('.load-comment').addClass('d-none');
                        }
                    } else if (data.msg != "") {
                        sweetAlert('center', 'error', data);
                    }
                }
            });
            http.always(function () {
                $('.load-more-loader').addClass('d-none');
            });
        }
        t.loadRecords(currentPage);
        $('.load-comment').off("click").on('click', '.load-more', function () {
            currentPage++;
            $('.load-more').addClass('d-none');
            $('.load-more-loader').removeClass('d-none');
            t.loadRecords(currentPage);
            t.timeline.css({ 'overflow-y': 'scroll', 'max-height': '500px', 'scrollbar-width': 'thin' });
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
            t.load()
        }
    });
    const truncateText = (text, maxLength = 20) => {
        if (!text) return "";
        return text.length > maxLength ? text.slice(0, maxLength) + "…" : text;
    };

    t.renderList = function (i, d) {
        let creator = d.creator_name ?? "";
        let deptName = d.dep_name ?? "";
        let date = d.created_short ?? "";
        let created_at_format = d.created_at_format ?? "";
            let html = `
                <div class="ticket-card p-2 mb-2 border rounded cursor-pointer active" data-id="${d.id}">
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
                        <div class="d-flex justify-content-between align-items-start">

                            <div class="fw-semibold text-truncate pe-2 subject-text"
                                title="${d.subject}">
                                ${truncateText(d.subject)}
                            </div>

                            <div class="small text-muted text-nowrap"
                                title="Created Date: ${created_at_format}">
                                ${date}
                            </div>

                        </div>

                        <!-- DEPARTMENT -->
                        <div class="d-flex align-items-center gap-2 small text-muted">

                            <span class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-building"></i>
                            </span>

                            <span class="dep text-truncate"
                                title="${deptName}">
                                ${truncateText(deptName)}
                            </span>

                        </div>

                        <!-- ASSIGNED USER -->
                        <div class="d-flex align-items-center gap-2 small text-muted">

                            <span class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-person"></i>
                            </span>

                            <span class="text-truncate"
                                title="${creator}">
                                ${truncateText(creator)}
                            </span>

                        </div>

                        <!-- LAST UPDATE -->
                        <div class="d-flex align-items-center gap-2 small text-muted">

                            <span class="d-flex align-items-center justify-content-center">
                                <i class="bi bi-clock"></i>
                            </span>

                            <span class="tat_expiry text-truncate"
                                title="Last Update">
                                ${d.updated_at_format ?? ''}
                            </span>

                        </div>

                                </div>

                            </div>

                    </div>`;
        t.lg.append(html);
        $('[title]').tooltip();
    };

    t.renderField= function(label, value, alignRight = false) {
        if (!value || !value.toString().trim()) return '';

        return `
            <div class="col-md-6 ${alignRight ? 'text-right' : ''}">
                <strong>${label}:</strong> ${value}
            </div>
        `;
    }

    t.requestdata = function (e) {
        e.preventDefault();
        $('.ticket-card').removeClass('active');
        $(this).addClass('active');
        t.config.ticketId = $(this).data('id');
        t.detail_api_loader.addClass('active');
        $.ajax({
            url: t.config.url.getArchivedRequestDetails,
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
                    <div class="box-section ">
                        <div class="box-header">
                            <div class="box-title">Custom Fields Data</div>
                        </div>

                        <div class="box-content">
                            <div>
                                <div class="ticket-content" style="margin-top: 5px;">
                                    ${customFieldsHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }

                const metaItems = [];

                if (d.priority?.name) {
                    metaItems.push(`
                        <span class="text-success fw-semibold" title="Priority">
                            <i class="bi bi-arrow-up"></i> ${d.priority.name}
                        </span>
                    `);
                }

                if (d.company?.name) {
                    metaItems.push(`
                        <span class="border-start ps-3" title="Company">
                            ${d.company.name}
                        </span>
                    `);
                }

                if (d.department?.name) {
                    metaItems.push(`
                        <span class="border-start ps-3" title="Department">
                            ${d.department.name}
                        </span>
                    `);
                }

                if (d.category?.name) {
                    metaItems.push(`
                        <span class="border-start ps-3" title="Category">
                            ${d.category.name}
                        </span>
                    `);
                }

                if (d.subCategory?.name) {
                    metaItems.push(`
                        <span class="border-start ps-3" title="SubCategory">
                            ${d.subCategory.name}
                        </span>
                    `);
                }

                if (d.tat) {
                    metaItems.push(`
                        <span class="border-start ps-3" title="TAT Hrs">
                            ${d.tat} Hrs
                        </span>
                    `);
                }

                const metaHTML = `
                <div class="td-left">
                    
                    <h3 class="ticket-title fs-4 fw-bold mb-2 text-truncate"
                        data-toggle="tooltip"
                        data-title="${d.subject}"
                        data-placement="bottom">
                        ${d.subject}
                    </h3>

                    <div class="d-flex flex-wrap gap-2 text-muted small">
                        ${metaItems.join('')}
                    </div>

                </div>`
                let viewFormIcon = '';
                if (
                    d.requestedForm &&
                    d.requestedForm.form_id &&
                    d.serviceRequest.form_type != 2 &&
                    (
                        config.isSuperUser == true ||
                        d.creator_id == t.config.user.id ||
                        d.assigned_to == t.config.user.id
                    )
                ) {
                    viewFormIcon = `
                        <span class="icon-btn" data-bs-toggle="tooltip" title="View Form" data-placement="bottom">
                            <a href="${config.url.view_form}/${d.requestedForm.id}${d.b ? '?b=' + d.b : ''}" target="_blank">
                                <i class="bi bi-ui-checks"></i>
                            </a>
                        </span>
                    `;
                }
                if (
                    d.requestedForm &&
                    d.requestedForm.form_id &&
                    d.serviceRequest.form_type == 2 &&
                    (
                        config.isSuperUser == true ||
                        d.creator_id == t.config.user.id ||
                        d.assigned_to == t.config.user.id
                    )
                ) {
                    viewFormIcon = `
                        <span class="icon-btn">
                            <a href="${config.url.view_custom_form}/${d.requestedForm.id}${d.b ? '?b=' + d.b : ''}" target="_blank"  data-bs-toggle="tooltip" title="View Form" data-bs-placement="top">
                                <i class="bi bi-ui-checks"></i>
                            </a>
                        </span>
                    `;
                }

                let html = `
                <div class="ticket-detail-header">
                   ${metaHTML}
                    <div class="td-right">
                    ${viewFormIcon}
                        <span class="icon-btn btn-ticket-history" data-id="${d.id}" data-bs-toggle="tooltip" title="History" data-bs-placement="top">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </span>
                    </div>
                </div>
                 <hr class="divider">
                 <div class="ticket-detail-info mb-3 small text-muted">
                    <div class="row created-updated">
                        <div class="col-md-6">
                            <strong>Created:</strong> ${d.created_at_format}
                        </div>
                        <div class="col-md-6 text-right">
                            <strong>Updated:</strong> ${d.updated_at_format}
                        </div>
                    </div>
                     ${
                        (d.archived_at_format && d.archived_at_format != null)
                        ? `<div class="row created-updated">
                            <div class="col-md-6">
                                <strong>Archived:</strong> ${d.archived_at_format}
                            </div>
                        </div>`: ''
                    }
                    ${
                        (d.all_tags && d.all_tags.length)
                        ? `<div class="row created-updated">
                        <div class="col-md-6 text-left">
                                <strong>Tags:</strong>
                                ${
                                    (() => {
                                        let html = '';
                                        let limit = Math.min(3, d.all_tags.length);

                                        for (let i = 0; i < limit; i++) {
                                            html += `<span class="badge" style="margin-right:2px">${d.all_tags[i].tags}</span>`;
                                        }

                                        if (d.all_tags.length > 3) {
                                            let remainingTags = d.all_tags.slice(3).map(t => t.tags).join(', ');

                                            html += `<span class="badge badge-secondary" style="margin-right:2px; cursor:pointer" title="${remainingTags}">
                                                        +${d.all_tags.length - 3}
                                                    </span>`;
                                        }
                                        return html;
                                    })()
                                }
                            </div>
                            </div>`
                        : ''
                    }
                   ${customFieldsSection}
                </div>
          
                    <div class="mb-3 custom-accordion" id="descAccordion">
                                <div class="accordion-item border rounded overflow-hidden">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button fw-semibold py-2 px-3" type="button" id="toggleAccordion">
                                            <span>Description</span>
                                            <i class="bi bi-chevron-up ms-auto accordion-icon"></i>
                                        </button>
                                    </h2>

                                    <div class="accordion-body py-2 custom-body" id="accordionBody">
                                        <div class="ticket-content">
                                            ${d.rendered_content ?? ""}
                                        </div>
                                        <div id="main_attachments"
                                            class="main_attachments attachments mt-2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                   
                <div class="ticket-comment border">
                    <p class="mb-3 fs-6 fw-semibold">Ticket Comment :-</p>
                    <div id="ticket_timeline" class="d-none"></div>
                    <div class="load-comment d-none">
                        <button class="btn rounded-circle p-2 load-more"><svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3C12.5523 3 13 3.44772 13 4V17.5858L18.2929 12.2929C18.6834 11.9024 19.3166 11.9024 19.7071 12.2929C20.0976 12.6834 20.0976 13.3166 19.7071 13.7071L12.7071 20.7071C12.3166 21.0976 11.6834 21.0976 11.2929 20.7071L4.29289 13.7071C3.90237 13.3166 3.90237 12.6834 4.29289 12.2929C4.68342 11.9024 5.31658 11.9024 5.70711 12.2929L11 17.5858V4C11 3.44772 11.4477 3 12 3Z" fill="#000000"></path></g></svg>
                        </button> 
                        <div class="load-more-loader d-none">
                            <i class="bi bi-arrow-repeat spin"></i> Loading...
                        </div>
                    </div>
                </div>`;
                $('.ticket-detail-box').html(html);
                
                let assignedTo = d.assignedUser
                    ? `${d.assignedUser.name ?? "N/A"}`
                    : `N/A`;
                let locationRow = '';
                if (d.location && d.location.name) {
                    locationRow = `
                    <div class="info-row">
                        <span class="info-label">Location</span>
                        <span class="info-value" title="${d.location.name}">
                            ${truncateText(d.location.name)}
                        </span>
                    </div>`;
                }
                let assignedHtml = d.assignedUser
                    ? `
                        <img src="${d.assignedUser.avatar}" alt="avatar" class="avatar">
                        <a href="${t.config.url.user_url}/${d.assignedUser.id}" target="_blank" title="${d.assignedUser && d.assignedUser.name ? d.assignedUser.name : 'N/A'}" class="assigned-name text-decoration-none text-muted small">${truncateText(d.assignedUser.name,12)}</a>
                    `
                    : `<span class="assigned-name text-muted">N/A</span>`;
                let approvalHtml = t.renderApprovalPanel(d);
                let ticketInfo = `<div class="info-section small text-muted">
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-medium">ID</span>
                                <span class="badge bg-primary-subtle text-primary rounded-pill">#${d.procure_tag}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-medium">Assigned To</span>
                                <span class="d-flex align-items-center gap-2 assigned-user">
                                    ${assignedHtml}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-medium">Requester</span>
                                <span class="d-flex align-items-center gap-2" title="${d.creator.name}">
                                    <img src="${d.creator.avatar}" alt="avatar" class="avatar">
                                    <a href="${t.config.url.user_url}/${d.creator.id}" target="_blank" class="user-name text-decoration-none text-muted small">${truncateText(d.creator.name,12)}</a>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="fw-medium">Status</span>
                                <span class="info-value">${d.status.name}</span>
                            </div>
                            ${locationRow}
                        </div>
                        ${approvalHtml}`;
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
    t.renderApprovalPanel= function(d) {
       let html = '';

        html += `
            <div class="approval-section">
                <h4 class="approval-heading well border">Approvals</h4>
                <hr/>
        `;
        const hasSystemApproval = d.approvals?.systemApproved === true;
        const hasPabs = d.approvals?.pabs && d.approvals.pabs.length > 0;

        if (hasSystemApproval) {
            html += `
                    <h4>System Approved</h4>
                    <span class="text-dark">
                        Approved At : ${d.approvals.systemApprovedAt}
                    </span>
                    <hr/>
            `;
        }
        if (hasPabs) {
            d.approvals.pabs.forEach(pab => {

                html += `<h4 class="mb-0">${pab.name} (${pab.mode})</h4>`;

                pab.approvals.forEach(ar => {
                    
                    const userTitle = ar.user?.name?.length > 12 ? `title="${ar.user.name}"` : "";
                    const delegatedTitle = ar.delegatedUser?.name?.length > 12 ? `title="${ar.delegatedUser.name}"` : "";

                    html += `
                        <div class="pad-ver bord-btm small text-muted">
                            <div>
                                <i class="bi bi-person i1"></i>
                                <strong>User: </strong>
                                <a href="${t.config.url.user_url}/${ar.user.id}"
                                ${userTitle}
                                target="_blank"
                                class="pad-ver bord-btm text-muted">
                                    ${truncateText(ar.user.name,20)}
                                </a>
                                ${ ar.delegatedUser && ar.delegatedUser.id
                                    ? ` (Delegated To
                                        <a href="${t.config.url.user_url}/${ar.delegatedUser.id}"
                                        target="_blank"
                                        ${delegatedTitle}
                                        class="btn-link text-bold">
                                            ${truncateText(ar.delegatedUser.name,12)}
                                        </a>)`
                                    : ''
                                }
                            </div>

                            <div>
                                <i class="bi bi-toggle-on i1"></i>
                                <strong>Status:</strong> ${ar.status}
                            </div>

                            <div>
                                <i class="bi bi-calendar-event i1"></i>
                                <strong>Date:</strong> ${ar.date}
                            </div>
                        </div>`;

                    if (ar.comment) {
                        html += `
                            <div>
                                <strong>Comment:</strong>
                                ${ar.comment}
                            </div>
                        `;
                    }

                    if (ar.approved_days && ar.status_code === 1) {
                        html += `
                            <div>
                                <strong>Approved days:</strong>
                                ${ar.approved_days} days
                            </div>
                        `;
                    }

                    html += `</div>`;
                });
            });
        }
        if (!hasSystemApproval && !hasPabs) {
            html += `<div class="text-center">No Approval Requests found</div>`;
        }

        html += `</div>`; 
        return html;
    }

    t.setMainAttachments = function (attachments) {
        var at = [];
        $.each(attachments, function (i, v) {
            var sext = v.ext.toLowerCase();
            var isImage = ["png", "jpeg", "jpg", "gif", "webp"].includes(sext);
            var fileIcon = 'bi bi-file-earmark text-secondary';
            if (sext == "pdf") {
                fileIcon = 'bi bi-file-earmark-pdf text-danger';
            } else if (["xls", "xlsx", "csv"].includes(sext)) {
                fileIcon = 'bi bi-file-earmark-excel text-success';
            } else if (["doc", "docx"].includes(sext)) {
                fileIcon = 'bi bi-file-earmark-word text-primary';
            } else if (isImage) {
                fileIcon = 'bi bi-image text-primary';
            }
            var actions = '';
            if (isImage) {
                actions +=
                    '<span class="tri-view text-secondary small cursor-pointer" ' +
                    'data-view_mode="1" ' +
                    'data-view="' + t.config.url.attachment_view + '/' + v.id + '" ' +
                    'data-name="' + decodeURIComponent(v.name) + '">' +
                    '<i class="bi bi-eye"></i>' +
                    '</span>';
            }

            actions +=
                '<span class="tri-download text-secondary small cursor-pointer ms-2" ' +
                'data-url="' + t.config.url.attachment_download + "/" + v.id + '">' +
                '<i class="bi bi-download"></i>' +
                '</span>';

            at.push(
                '<div class="d-flex align-items-center bg-light border rounded-4 px-2 py-2 mb-2 col-md-4">' +
                '<div class="bg-white border rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 me-2" style="width:30px;height:30px;">' +
                '<i class="' + fileIcon + '"></i>' +
                '</div>' +
                '<div class="flex-grow-1 text-truncate fw-medium small text-dark pe-2">' +
                decodeURIComponentSafe(unescape(v.name)) +
                '</div>' +
                '<div class="d-flex align-items-center flex-shrink-0">' +
                actions +
                '</div>' +
                '</div>'
            );
        });
        if (at.length > 0) {
            $('#main_attachments').html(
                '<div>' +
                '<div class="fw-semibold mb-2">' +
                'Attachment:' +
                '</div><div class="row gap-2 px-3">' +
                at.join("") +
                '</div></div>'
            );
        }
    };
    function decodeURIComponentSafe(uri, mod) {
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

            $(el)
                .removeClass('tiny-view-on')
                .find('.single-tiny-viewer')
                .html('<i class="bi bi-arrows-angle-contract"></i>')
                .attr('title', 'Compress');

        } else {
            card.css('height', '');

            $(el)
                .addClass('tiny-view-on')
                .find('.single-tiny-viewer')
                .html('<i class="bi bi-arrows-angle-expand"></i>')
                .attr('title', 'Expand');
        }
    };
     
    document.addEventListener("click", function (e) {
        const header = e.target.closest(".box-header");
        if (!header) return;

        const section = header.closest(".box-section");
        const content = section.querySelector(".box-content");

        section.classList.toggle("collapsed");

        if (section.classList.contains("collapsed")) {
            content.style.maxHeight = "20px";
        } else {
            content.style.maxHeight = content.scrollHeight + "px";
        }
    });

    document.querySelectorAll('.box-section').forEach(section => {
        const content = section.querySelector('.box-content');
        content.style.maxHeight = content.scrollHeight + "px";
    });
    
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

   
    $(document).on('click', '.btn-open-filter', function () {
        t.filters.wrapper.modal('show');
    });

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
        $.each(t.config.sort_fields, function (i, d) {
            $('<li class="list-group-item ' + (d.id == s.id ? "selected" : "") + '" data-id="' + d.id + '">' +
                '<span class="like-radio"></span>' + d.text +
            '</li>').appendTo(sf);
        });
    };

    t.sortbtns.on("mousedown", ".sort-fields li", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        var id = $(this).attr("data-id");

        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 2;
        }
        t.lg.empty();
        t.pageNumber = 1;
        autoClickedFirstTicket = true;
        t.renderSortFields();
        t.sortbtns.removeClass("open");

        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () {
            t.load();
        }, 300);
    });

    t.sortbtns.on("mousedown", ".dir-sort", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.lg.empty();
        t.pageNumber = 1;
        autoClickedFirstTicket = true;
        t.renderSortFields();

        clearTimeout(t.sortTimeout);
        t.sortTimeout = setTimeout(function () {
            t.load();
        }, 300);
    });

    t.sortbtns.on("click", ".dropdown-toggle", function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        t.sortbtns.toggleClass("open");
    });

    $(document).on("mousedown", function (e) {
        if (!$(e.target).closest(".sort-buttons").length) {
            t.sortbtns.removeClass("open");
        }
    });

    t.renderSortFields();

    t.sortbtns.on("click", ".dropdown-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.sortbtns.toggleClass("open");
    });

    $(document).on("click", function (e) {
        if (!$(e.target).closest(".sort-buttons").length) {
            t.sortbtns.removeClass("open");
        }
    });

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox.find("input").val());
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

    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.other_filters = {};
        if(t.filters.department.val() && t.filters.department.val() != 'null'){
            t.config.other_filters.department = t.filters.department.val();
        }
        if(t.filters.problem_category.val() && t.filters.problem_category.val() != 'null'){
            t.config.other_filters.problem_category = t.filters.problem_category.val();
        }
        if(t.filters.sub_category.val() && t.filters.sub_category.val() != 'null'){
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if(t.filters.status.val() && t.filters.status.val() != 'null'){
            t.config.other_filters.status = t.filters.status.val();
        }
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null'){
            t.config.other_filters.based_on = t.filters.based_on.val();
        }
        if(t.filters.pab.val() && t.filters.pab.val() != 'null'){
            t.config.other_filters.pab = t.filters.pab.val();
        }
        if(t.filters.filter_by_approver.val() && t.filters.filter_by_approver.val() != 'null'){
            t.config.other_filters.filter_by_approver = t.filters.filter_by_approver.val();
        }
        if(t.filters.hierarchy_approval.val() && t.filters.hierarchy_approval.val() != 'null'){
            t.config.other_filters.hierarchy_approval = t.filters.hierarchy_approval.val();
        }    
        if(t.filters.based_on.val() && t.filters.based_on.val() != 'null'){
            t.config.other_filters.daterange = t.filters.daterange.val();
        }
        if(t.filters.current_pab.val() && t.filters.current_pab.val() != 'null'){
            t.config.other_filters.current_pab = t.filters.current_pab.val();
        }

        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.daterange.val(), false);
        t.filters.wrapper.modal("hide");
    };

    t.load();
    $(document).on('keydown', '.search', t.search);
    $(document).on('click', '.ticket-card', t.requestdata);
    t.page.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    $(document).on('click', '#ticket_timeline .tri-view', t.attachmentView);
    $(document).on('click', '#ticket_timeline .tri-download', t.attachmentDownload);
    $(document).on("click", ".main_attachments .tri-view", $.proxy(t.attachmentView));
    $(document).on("click", ".main_attachments .tri-download", $.proxy(t.attachmentDownload));
    $(document).on("click", ".btn-reload", function () {
        t.lg.empty();
        t.pageNumber = 1;              
        autoClickedFirstTicket = true; 
        t.load();
    });
   
    var select2Opts = {width:"100%"};
    t.filters.department.select2($.extend({}, select2Opts, {placeholder: "Filter By Department",allowClear:true}));
    t.filters.status.select2($.extend({}, select2Opts, {placeholder: "Filter By Status",allowClear: true,dropdownParent: t.filterMdl}));
    t.filters.problem_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Problem_Category,allowClear:true,dropdownParent: t.filterMdl }));
    t.filters.sub_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Sub_Category,allowClear:true,dropdownParent: t.filterMdl }));
    t.filters.pab.select2($.extend({}, select2Opts, {placeholder: "Filter By Authority Board",allowClear:true,dropdownParent: t.filterMdl}));
    t.filters.based_on.select2($.extend({}, select2Opts, {placeholder: "Filter By Date",allowClear:true,dropdownParent: t.filterMdl}));
    t.filters.approval.select2($.extend({}, select2Opts, {placeholder: "Filter By Approval",allowClear:true,dropdownParent: t.filterMdl}));
    t.filters.hierarchy_approval.select2($.extend({}, select2Opts, {placeholder: "Filter By Approval Mode",allowClear:true,dropdownParent: t.filterMdl}));
    t.filters.current_pab.select2($.extend({}, select2Opts, {placeholder: "Filter By Current Authority Board",allowClear:true,dropdownParent: t.filterMdl}));
    t.filters.department.select2($.extend({}, select2Opts, {placeholder: "Filter By Department", allowClear: true, dropdownParent: t.filterMdl}));
    
    t.filters.fun.reload_department();
    t.filters.fun.reload_status();
    t.filters.fun.reload_pab();
    t.filters.fun.reload_current_pab();

    t.filters.fun.reload_approver();
    t.btnClrFilter = function() {
        t.config.other_filters = {};
        t.filters.status.val("").trigger("change");
        t.filters.department.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.pab.val("").trigger("change");
        t.filters.hierarchy_approval.val("").trigger("change");
        t.filters.based_on.val("null").trigger("change");
        t.filters.current_pab.val("").trigger("change");
        t.filters.daterange.val('');
        t.filterMdl.modal("hide")
        t.lg.empty();
        t.pageNumber = 1;
        autoClickedFirstTicket = true;
        t.load();
    };
    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_request + "?q=" + t.config.export_filters;
    }
    $(document).on('click', '#toggleAccordion', function () {
        $('#accordionBody').toggleClass('open');
        $('.accordion-icon').toggleClass('rotate');
    });

    t.page.on("click", ".btn-export-sr-archive", t.download);
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.filters.btnfilter.on("click", t.search);
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
    }
    $(document).on('click', '.btn-ticket-history', function (e) {
        e.preventDefault();
        let ticketId = $(this).data('id');

        if (!ticketId) {
            console.error('Ticket ID not found');
            return;
        }

       $.ajax({
            url: t.config.url.getArchivedRequestHistory,
            type: 'GET',
            dataType: 'json',
            data: {
                id: ticketId
            },
            success: function (data) {
                let ticket_history = '';
                if (data.status == "success") {
                    $('#ticketHistoryModal').find('#modalTitle').text("Service Request History");
                    $('.tkt-hst-info-card').hide();
                    if (data.history.length > 0) {
                        $.each(data.history, function (key, value) {
                            let commenterName = value.user_name ?? 'System';
                            let updatedTime = value.updated_at_format ?? '';
                            let actionHtml = value.change_info ?? '';
                            let timeAgoText = value.time_ago ?? '';
                            let badgeClass = 'green';
                            let avatarHtml = '';
                            if (value.user_image != null && value.user_image != '') {
                                avatarHtml =
                                    '<img src="' + value.user_image + '" class="w-100 h-100 object-fit-cover">';
                            } else {
                                avatarHtml =
                                    '<span class="fw-bold text-uppercase">' +
                                    commenterName.charAt(0) +
                                    '</span>';
                            }
                            ticket_history += '<div class="tkt-hst-timeline-item d-flex gap-3 align-items-start">' +
                                '<div class="d-flex flex-column align-items-center flex-shrink-0 align-self-stretch">' +
                                '<div class="rounded-circle d-flex flex-column align-items-center justify-content-center text-center text-white flex-shrink-0 tkt-hst-badge ' + badgeClass + '">' + timeAgoText + '</div>' +
                                '<div class="tkt-hst-connector-line"></div>' +
                                '</div>' +
                                '<div class="flex-fill mt-1 tkt-hst-card mb-1">' +
                                '<div class="d-flex align-items-center gap-1 mb-1 meta-time">' +
                                '<svg width="12" height="12" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                '<path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="#7F7F7F"/>' +
                                '</svg>' +
                                '<span class="b7-text" style="color:#7F7F7F">' + value.updated_at_format + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center gap-2 mb-1">' +
                                '<div class="rounded-circle overflow-hidden flex-shrink-0 tkt-hst-avatar d-flex align-items-center justify-content-center">' +
                                avatarHtml +
                                '</div>' +
                                '<span class="b6-text fw-bold">' + commenterName + '</span>' +
                                '<span class="b6-text fw-bold">•</span>' +
                                '<span class="b6-text fw-light" style="color:#7F7F7F">Changes done by ' + commenterName + '</span>' +
                                '</div>' +
                                '<div class="b6-text fw-normal">' + actionHtml + '</div>' +
                                '</div>' +
                                '</div>';
                        });

                    } else {
                        ticket_history =
                            '<div class="text-center p-4">' +
                            '<p>No history available</p>' +
                            '</div>';
                    }
                    $('#ticket_history_container').html(ticket_history);
                    $('#ticketHistoryModal').modal('show');
                } else {
                    $('#ticket_history_container').html(`
                    <div class="text-center p-4">
                        <p>${data.msg}</p>
                    </div>
                `);
                }
            },
            error: function () {
                $('#ticket_history_container').html(`
                <div class="text-center p-4 text-danger">
                    Something went wrong
                </div>
            `);

            }
        });

    });
}
