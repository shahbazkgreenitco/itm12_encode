var ProblemCategory = function(config) {
    var t = this;
    t.config = config;
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.page = $("#page-content");
    t.table = t.page.find("#mytable");
    t.mdl = t.page.find("#TicketProblemTypeModal");
    t.mdl.title = t.mdl.find(".modal-title");

    t.mdl.frm = t.mdl.find("#frm_problem_type");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.parentId = t.mdl.frm.find("#parent_id");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    // t.mdl.frmEl.serviceTypeId = t.mdl.frm.find("#service_type_id");
    t.mdl.frmEl.ticketAttender = t.mdl.frm.find("#ticket_attender");
    t.mdl.frmEl.autoAllocationGroup = t.mdl.frm.find("#auto_allocation_group");
    t.mdl.frmEl.custom_fieldset = t.mdl.frm.find("#custom_fieldset");
    t.mdl.frmEl.number_of_days = t.mdl.frm.find("#number_of_days");
    t.mdl.frmEl.privilege_access = t.mdl.frm.find("#privilege_access");
    t.mdl.frmEl.auto_resolve_ticket = t.mdl.frm.find("#auto_resolve_ticket");
    t.mdl.frmEl.no_of_approval_days = t.mdl.frm.find("#no_of_approval_days");
    t.mdl.frmEl.priorityId = t.mdl.frm.find("#priority_id");
    t.mdl.frmEl.tat = t.mdl.frm.find("#tat");
    t.mdl.frmEl.workaround_sla = t.mdl.frm.find("#workaround_sla");
    t.mdl.frmEl.response_sla = t.mdl.frm.find("#response_sla");
    t.mdl.frmEl.close_ticket_after_days = t.mdl.frm.find("#close_ticket_after_days");
    t.mdl.frmEl.reopen_ticket_until_days = t.mdl.frm.find("#reopen_ticket_until_days");
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.category_tag = t.mdl.frm.find("#category_tag");
    t.mdl.frmEl.approval_required = t.mdl.frm.find('#approval_required');
    t.mdl.frmEl.pab_id = t.mdl.frm.find('#pab_id');
    t.mdl.frmEl.remarks = t.mdl.frm.find("#remarks");
    t.mdl.frmEl.is_form_required = t.mdl.frm.find('#is_form_required');
    t.mdl.frmEl.form_id = t.mdl.frm.find("#form_id");
    t.mdl.frmEl.status = t.mdl.frm.find("#status");
    t.mdl.frmEl.company_id = t.mdl.frm.find('#company_id');
    t.mdl.frmEl.role_id = t.mdl.frm.find('#role_id');
    t.mdl.btn = {};
    t.mdl.btn.submit = t.mdl.find("#btnSubmit");
    t.mdl.btn.clear = t.mdl.find("#btnClose");
    t.mdl.btn.clr = t.mdl.find("#btnClr");

    t.mdl.remarks = t.page.find("#remarksModal");
    t.mdl.remarks.body = t.mdl.remarks.find(".modal-body");

    t.httpCall = true;
    t.httpPostPath = "";
    t.searchbox = t.table.find(".searchbox");
    t.btn = {};

    const requiredCheck = function(element) {
        return (t.mdl.frmEl.approval_required.val() == '1');
    };

    t.filters = {
        wrapper: t.page.find("#advance-filters"),
        data: {
            problem_categories: {}
        }
    };
    t.filters.btnfilterclr = t.page.find(".advance-filters #btnClrFilter"),
    t.filters.priority = t.page.find(".advance-filters #filter_by_priority"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.allocation_group = t.filters.wrapper.find("#filter_allocation_group"),
    t.filters.authority_approval = t.filters.wrapper.find("#authority_approval"),
    t.filters.pab = t.filters.wrapper.find("#filter_by_pab"),
    t.filters.form_name = t.filters.wrapper.find("#filter_by_form"),
    t.filters.based_on_category = t.filters.wrapper.find("#filter_based_on_category"),
    t.filters.filter_by_escalation = t.filters.wrapper.find("#filter_by_escalation"),
    t.filters.filter_by_escalation_for = t.filters.wrapper.find("#filter_by_escalation_for"),
    t.filters.filter_by_escalation_to = t.filters.wrapper.find("#filter_by_escalation_to"),
    t.filters.filter_by_status = t.filters.wrapper.find("#filter_by_status"),
    t.filters.filter_by_role = t.filters.wrapper.find("#filter_by_role"),

    t.filters.fun = {
        reload_allocation_group: function() {
            t.filters.allocation_group.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.allocation_group.select2({
                ajax: {
                    url: t.config.url.getAllocationGroups,
                    dataType: "json"
                },
                width: "100%",
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: "Select auto allocation group"
            });
            t.filters.allocation_group.trigger("change");
        },
        reload_priority: function() {
            //t.filters.priority.empty().append(new Option(config.translations.No_Filter, null, false, false));
            $.each(t.config.priorities, function(i, k) {
                t.filters.priority.append(new Option(k.name, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_priority: function() {
            //t.filters.priority.empty().append(new Option(config.translations.No_Filter, null, false, false));
            $.each(t.config.priorities, function(i, k) {
                t.filters.priority.append(new Option(k.name, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_department: function() {
            // t.filters.department.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.department.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.department.select2({
                ajax: {
                    // url: t.config.url.departments_based_on_privilage,
                    url: t.config.url.departments_with_company,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1
                        };
                    },
                },
                width: "100%",
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_department
            });
            t.filters.department.trigger("change");
        },
        reload_pab: function() {
            t.filters.pab.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.pab.select2({
                ajax: {
                    url: t.config.url.getPabApproved,
                    dataType: "json"
                },
                width: "100%",
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_authority_board
            });
            t.filters.pab.trigger("change");
        },
        reload_formname: function() {
            t.filters.form_name.empty().append(new Option(config.translations.No_Filter, null, false, false));
            t.filters.form_name.select2({
                ajax: {
                    url: t.config.url.getformname,
                    dataType: "json"
                },
                width: "100%",
                allowClear: true,
                minimumResultsForSearch: Infinity,
                placeholder: config.translations.filter_by_formname
            });
            t.filters.form_name.trigger("change");
        }
    };

    $.validator.addMethod("greaterThan", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) > parseFloat(otherValue);
    }, "Please enter a value greater than {0}");

    $.validator.addMethod("greaterThanOrEqual", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) >= parseFloat(otherValue);
    }, "Please enter a value greater than or equal to {0}");

    $.validator.addMethod("lessThan", function(value, element, params) {
        var otherValue = $(params).val();
        if (value === "" || otherValue === "") {
            return true;
        }
        return parseFloat(value) < parseFloat(otherValue);
    }, "Please enter a value less than {0}");

    $.validator.addMethod("lessThanOrEqual", function(value, element, param) {
        var parts = param.split(":");
        var selector = parts[0];
        var defaultMax = parts[1] ? parseInt(parts[1]) : 3;
        
        var compareValue = $(selector).val();
        
        if (compareValue && compareValue !== '') {
            return parseInt(value) <= parseInt(compareValue);
        } else {
            return parseInt(value) <= defaultMax;
        }
    }, function(param, element) {
        var parts = param.split(":");
        var selector = parts[0];
        var defaultMax = parts[1] ? parseInt(parts[1]) : 3;
        
        var compareValue = $(selector).val();
        
        if (compareValue && compareValue !== '') {
            return "Value must be equal to or less than " + compareValue;
        } else {
            return "Value must be equal to or less than " + defaultMax;
        }
    });
        $.validator.addMethod("reopenLimit", function (value, element, selector) {
        if (!value) {
            return true;
        }

        var closeVal = $(selector).val();

        if (!closeVal) {
            return parseInt(value) <= 3;
        }
        return parseInt(value) <= parseInt(closeVal);

    }, function (params, element) {

        var closeVal = $(params).val();
        if (!closeVal) {
            return "Value must be less than or equal to 3";
        }
        return "Value must be less than or equal to Close Ticket After Days";
    });

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            service_type_id: {
                required: true,
                digits: true,
                str_name: true
            },
            parent_id: {
                str_name: true
            },
            department_id: {
                required: true,
                digits: true,
                str_name: true
            },
            name: {
                required: true,
                str_name: false,
                clean_text_only: true,
            },
            priority_id:{
                required:true
            },
            number_of_days:{
                digits: true,
                min: 1,
                max: 90,
                remarks: true,   
            },
            tat: {
                digits: true,
                min: 0,
                max: 1000,
                remarks: true,
                greaterThan: "#response_sla",
                greaterThan: "#workaround_sla"
            },
            close_ticket_after_days: {
                digits: true,
                min: 1,
                max: 10,
                remarks: true,
            },
            reopen_ticket_until_days: {
                digits: true,
                min: 1,
                max: 10,
                remarks: true,
                reopenLimit: "#close_ticket_after_days"
            },
            response_sla: {
                remarks: true,
                min: 0,
                max: 1000,
                lessThan: "#tat",
                lessThan: "#workaround_sla"
            },
            workaround_sla: {
                remarks: true,
                min: 0,
                max: 1000,
                lessThan: "#tat",
                greaterThan: "#response_sla"
            },
            'pab_id[]': {
                required: function(element) {
                    return t.mdl.frmEl.approval_required.val() == 1
                },
            },
            form_id: {
                required: function(element) {
                    return t.mdl.frmEl.is_form_required.val() != 0
                },
            },
            company_id: {
                required:true,
            }
        },
        messages: {
            tat: {
                greaterThan: "TAT must be greater than both Response SLA and Workaround SLA."
            },
            response_sla: {
                lessThan: "Response SLA must be less than TAT.",
                greaterThan: "Response SLA must be less than Workaround SLA."
            },
            workaround_sla: {
                lessThan: "Workaround SLA must be less than TAT.",
                greaterThan: "Workaround SLA must be greaterThan Response SLA."
            }
        }
    });

    t.tblHelpers = {
        problemCategory: function() {
            return function(d) {
                var a = [];
                a.push('<div' + (d.pt_name?.length > 30 ? ' data-toggle="tooltip" data-placement="right" data-original-title="' + d.pt_name + '"' : '') + '>' + (d.pt_name?.length > 30 ? d.pt_name.substring(0, 15) + '...' : d.pt_name || '') + '</div>');
                if(d.parent_id > 0 && d.parent_id != "" && d.parent_id != null) {
                    a.push('<div><span class="subcat"><span></span> '+config.translations.SUB_CATEGORY+'</span></div>');
                }
                if(d.esc_tot > 0 && d.esc_tot != "" && d.esc_tot != null) {
                    a.push('<div><span class="esc_tot"><span></span> ' + d.esc_tot + ' ESCLATIONS</span></div>');
                }
                if (d.approval_required == "Required") {
                    a.push('<div><strong><span class=""><span>Approval: </span> ' + d.approval_required + '</span><span class=""><br><span>Authority Board: </span> ' + d.pab + '</span></strong></div>');
                    if(d.form_id != null){
                        if (d.is_form_required == 1) {
                            a.push('<div><strong>Form Name: ' + d.form_name + '</strong></div>')
                        }else if(d.is_form_required == 2){
                            a.push('<div><strong>Custom Form Name: ' + d.form_name + '</strong></div>')
                        }
                    }
                }
                if(d.auto_allocation_group != "")
                {
                    a.push('<div><strong><span>Allocation Group: </span>' + d.auto_allocation_group + '</strong></div>');
                }
                return a.join('');
            }
        },
        actions: function () {
            return function (d) {
                var a = [];
                if(jQuery.inArray("ProblemCategoriesEdit", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn dtActEdit' data-toggle='tooltip' data-placement='right'  data-original-title='" + config.translations.Edit_Category + "' data-id=\"" + d.id + "\" ><i class=\"fa fa-pencil\"></i></button>");
                }
                if(jQuery.inArray("ProblemCategoriesDelete", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn dtActDel' data-toggle='tooltip' data-placement='right' data-original-title='" + config.translations.Delete_Category + "'data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                }
                if(jQuery.inArray("ProblemCategoriesEsclationAdd", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn dtActEscl' data-toggle='tooltip' data-placement='right' data-original-title='" + config.translations.Esclation + "' data-id=\"" + d.id + "\" ><i class=\"fa fa-rocket\"></i></button>");
                }
                if(jQuery.inArray("ProblemCategoriesHistory", t.config.permissions) !== -1) {
                    a.push('<a href="'+config.url.history+'/'+d.id+'" class="btn dtActbtn viewinfo" target="_blank" data-toggle="tooltip" data-placement="right" data-toggle="modal" data-original-title="History" data-id="' + d.id + '"><i class=\"fa fa-info\"></i></a>');
                }
                if(t.config.taskModule === 1 && jQuery.inArray("TaskRead", t.config.permissions) !== -1){
                    a.push('<a href="'+config.url.getTaskList+'/'+d.id+'" class="btn dtActbtn tasklist" target="_blank" data-toggle="tooltip" data-placement="right" data-toggle="modal" data-original-title="Define Tasks" data-id="' + d.id + '"><i class=\"fa fa-tasks\"></i></a>');
                }
                const isEmpty = !Array.isArray(a) || !a.length;
                if(isEmpty) {
                    return '';
                }
                return '<div class="popup-toolbox">' + '<div class="btn-toolbar popup-toolbox-status">' + '<i class="fa fa-cog"></i>' + '</div>' + '<div class="popup-toolbox-bar itm_actionToolBar">' + a.join('') + '</div>' + '</div>';
            };
        }
    };

    let columns = [
        { data: 'a' },
        { data: 'a.company' },
        { data: 'a' },
        { data: 'a.category_tag' },
        { data: 'a.ticket_attender' },
        { data: 'a.department' },
        {
            data: 'a.pc_name',
            render: function (data) {
                if (!data) return '';
                data = String(data);
                if (data.length > 20) {
                    var truncated = truncateHtml(data, 20);
                    return '<div data-toggle="tooltip" data-placement="right" data-original-title="' + escapeHtml(data) + '">' + truncated + '...</div>';
                } else {
                    return data;
                }
            }
        },
        { data: 'a.priority' },
        { data: 'a.tat' }
    ];

    if (t.config.taskModule === 1 && jQuery.inArray("TaskRead", t.config.permissions) !== -1) {
        columns.push({
            data: 'a.task_count',
            render: function (d, type, row) {
                if (d >= 0) {
                    return "<a target='_blank' style='cursor:pointer' class='view-task' id='" + row.a.id + "' data-id='" + row.a.id + "'>" + d + "</a>";
                }
                return '';
            }
        });
    }
    columns = columns.concat([
        {
            data: 'a.status',
            render: function (data, type, row) {
                return data == 1 
                    ? '<i class="fa fa-check-circle text-success"></i> Enable'
                    : '<i class="fa fa-times-circle text-danger"></i> Disable';
            }
        },
        { data: 'a.response_sla' },
        { data: 'a.workaround_sla' },
        { data: 'a.close_ticket_after_days' },
        { data: 'a.reopen_ticket_until_days' },
        {
            data: 'a.remarks',
            render: function (data, type, row) {
                if (!data) return '';
                data = String(data);
                if (data.length > 50) {
                    var truncated = truncateHtml(data, 50);
                    return truncated + '<a class="read-more" data-full-text="' + escapeHtml(data) + '">...Read More</a>';
                } else {
                    return data;
                }
            }
        },
        { data: 'a.last_updated_at' }
    ]);
    let lastUpdatedIndex = (t.config.taskModule === 1) && (jQuery.inArray("TaskRead", t.config.permissions) !== -1) ? 16 : 15;
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        }, 
        {
            targets: 2,
            render: t.tblHelpers.problemCategory()
        }, {
            targets: 0,
            render: t.tblHelpers.actions()
        }],
        order: [[lastUpdatedIndex, 'desc']],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: { 
            url: t.config.url.types, 
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $("#mytable_wrapper .plain-search").val();
                d.filters = t.config.other_filters;
                d.main_filter = t.config.main_filter;
                d.company_id = companyId;
            },
            error: function (reason) {
                // location.reload();
            }
        },
        columns: columns, 
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            var searchBox = '<div class="input-group table-search-btns">' +
                '<input type="text" class="form-control searchbox plain-search" placeholder="'+config.translations.serach_option+'" />'+
                '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="top" data-original-title="Search"><i class="ps-icon plain-search-icon"></i></span>';
            if(jQuery.inArray("ProblemCategoriesAdd", t.config.permissions) !== -1) {
                searchBox += '<span class="input-group-addon  btn-add-type" data-toggle="tooltip" data-original-title="' + config.translations.add_category + '"><i class="ps-icon fa fa-plus"></i></span>';
            }
            searchBox += '<span class="input-group-addon  btn-reload-list" data-toggle="tooltip" data-original-title="'+config.translations.refresh_list+'"><i class="ps-icon fa fa-refresh"></i></span>'+
                '<span class="input-group-addon  btn-visible-content" data-toggle="collapse" href="#advance-filters" aria-controls="advance-filters"><i class="ps-icon fa fa-filter" data-toggle="tooltip" data-original-title='+config.translations.Filter+'></i><div id="filter_count" class="count-badge"></div></span>';
            if(jQuery.inArray("ProblemCategoriesImport", t.config.permissions) !== -1) {
                searchBox += '<span class="input-group-addon btn-import-problem-category" data-toggle="tooltip" data-original-title="Import Problem Categories"><i class="ps-icon fa fa-upload"></i></span>';
            }
            if(jQuery.inArray("ProblemCategoriesDownload", t.config.permissions) !== -1) {
                searchBox += '<span class="input-group-addon  btn-export-problem-category" data-toggle="tooltip" data-original-title="' + config.translations.download_excel + '"><i class="ps-icon fa fa-download"></i></span>' +
                    '<span class="input-group-addon  btn-exports-pdf hide" data-toggle="tooltip" data-original-title="' + config.translations.download_pdf + '"><i class="fa fa-file-pdf-o"></i></span>';
            }
            searchBox += '</div>';
            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");
            $(searchBox).insertBefore("#mytable_filter");
            $("#mytable_filter").remove();
            $("#mytable_length").find("select").select2();
            $("#mytable_wrapper .plain-search").on("keyup", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    t.config.search = v;
                    api.search(this.value).draw();
                }
            });
            t.btn.reload = t.page.find(".btn-reload-list");
            t.btn.search = t.page.find(".btn-searchbox");
            t.btn.export = t.page.find(".btn-export-problem-category");
            t.btn.exports = t.page.find(".btn-exports-pdf");
            t.btn.add = t.page.find(".btn-add-type");
            t.btn.import = t.page.find('.btn-import-problem-category');
            t.page.on("click", ".read-more", function(e) {
                e.preventDefault();
                var fullText = $(this).data("full-text");
                $(t.mdl.remarks.body).html(fullText);
                $(t.mdl.remarks).modal("show");
            });
        }
    });

    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    function escapeHtml(text) {
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


    t.cache_filter_values = function() {
        var v = $.trim($("#mytable_wrapper .plain-search").val());
        t.config.search = v;
        t.config.other_filters = {};
        if(t.filters.priority.val() && t.filters.priority.val() != 'null') {
            t.config.other_filters.priority = t.filters.priority.val();
        }
        if(t.filters.department.val() && t.filters.department.val() != 'null') {
            t.config.other_filters.department = t.filters.department.val();
        }
        if(t.filters.allocation_group.val() && t.filters.allocation_group.val() != 'null') {
            t.config.other_filters.allocation_group = t.filters.allocation_group.val();
        }
        if(t.filters.authority_approval.val() && t.filters.authority_approval.val() != 'null') {
            t.config.other_filters.authority_approval = t.filters.authority_approval.val();
        }
        if(t.filters.based_on_category.val() && t.filters.based_on_category.val() != 'null') {
            t.config.other_filters.based_on_category = t.filters.based_on_category.val();
        }
        if(t.filters.pab.val() && t.filters.pab.val() != 'null') {
            t.config.other_filters.pab = t.filters.pab.val();
        }
        if(t.filters.form_name.val() && t.filters.form_name.val() != 'null') {
            t.config.other_filters.form_name = t.filters.form_name.val();
        }
        if(t.filters.filter_by_escalation.val() && t.filters.filter_by_escalation.val() != 'null') {
            t.config.other_filters.filter_by_escalation = t.filters.filter_by_escalation.val();
        }
        if(t.filters.filter_by_escalation_for.val() && t.filters.filter_by_escalation_for.val() != 'null') {
            t.config.other_filters.filter_by_escalation_for = t.filters.filter_by_escalation_for.val();
        }
        if(t.filters.filter_by_escalation_to.val() && t.filters.filter_by_escalation_to.val() != 'null') {
            t.config.other_filters.filter_by_escalation_to = t.filters.filter_by_escalation_to.val();
        }
        if(t.filters.filter_by_status.val() && t.filters.filter_by_status.val() != 'null') {
            t.config.other_filters.filter_by_status = t.filters.filter_by_status.val();
        }
        const allowedClients = ["rolepermission", "ril","grdemo"];
        if(allowedClients.includes(t.config.client)) {
            if(t.filters.filter_by_role.val() && t.filters.filter_by_role.val() != 'null') {
                t.config.other_filters.filter_by_role = t.filters.filter_by_role.val();
            }
        }
        var jobj = {"search":t.config.search, "other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        if(Object.keys(t.config.other_filters).length > 0){
            $('#filter_count').css('display', 'flex');
            $('#filter_count').text(Object.keys(t.config.other_filters).length);
        } else {
            $('#filter_count').css('display', 'none');
        }
        $('#advance-filters').collapse('hide')
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#mytable_wrapper .plain-search").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $.trim($("#mytable_wrapper .plain-search").val());
        t.dTbl.search(v).draw();
    };

    t.import = function(e) {
        e.preventDefault();
        window.location = t.config.url.import_url;
    }

    t.export = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + '&company_id=' +companyId;
    };

    t.exportPDF = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.download_pdf + "?q=" + t.config.export_filters;
    };
    
    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        t.frmValidator.resetForm();
        t.mdl.frmEl.id.val("");
        t.mdl.priorityId();
        t.mdl.form_id();
        t.mdl.frmEl.approval_required.val(0).trigger("change");
        t.mdl.frmEl.pab_id.val("").trigger("change");
        t.mdl.frmEl.is_form_required.val(0).trigger("change");
        t.mdl.frmEl.custom_fieldset.val(0).trigger("change");
        // t.mdl.frmEl.priorityId.empty();
        // $.each(t.config.priorities, function(i,v) {
        //     t.mdl.frmEl.priorityId.append(new Option(v.name, v.id));
        // });
        // t.mdl.frmEl.priorityId.trigger("change");
        t.mdl.frmEl.auto_resolve_ticket.prop('checked',false);
        t.mdl.frmEl.departmentId.empty().trigger("change");
        t.mdl.frmEl.parentId.empty().trigger("change");
        t.mdl.frmEl.company_id.val('').trigger("change");
        t.mdl.frmEl.remarks.summernote('reset');
    };

    t.handleSubmit = function(e) {
        e.preventDefault();

        if( t.frmValidator.form() == false ) {
            return false;
        }

        if(t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath, 
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if(typeof data == "object") {
                if(data.status == "success") {
                    t.reload();
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
                    t.mdl.modal("hide");
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
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

    t.addType = function(e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.add;
        t.mdl.title.html(config.translations.New_Problem_Category);
        t.mdl.btn.submit.text(config.translations.Create);
        t.mdl.frmEl.no_of_approval_days.addClass('hide');
        t.mdl.frmEl.status.val("1").trigger("change");
        t.mdl.modal("show");
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.mdl.frmEl.company_id.append(option).trigger('change');
        }
    };

    t.editType = function(e) {
        e.preventDefault();
        t.resetFrm();
        var typeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + typeId;
        var http = $.get(t.config.url.get + "/" + typeId);
        http.done(function(data) {
            if(typeof data == "object") {
                if(data.status == "success") {
                    t.mdl.title.html(config.translations.Edit_Problem_Category);
                    t.mdl.btn.submit.text(config.translations.Save_Changes);
                    t.toggleListen();
                    t.mdl.frmEl.id.val(data.type.data.id);
                    t.mdl.frmEl.name.val(data.type.data.name);
                    t.mdl.frmEl.category_tag.val(data.type.data.category_tag);
                    t.mdl.frmEl.number_of_days.val(data.type.data.number_of_days);
                    if(data.type.data.privilege_access == 1){
                        t.mdl.frmEl.privilege_access.prop('checked', true);
                        t.mdl.frmEl.no_of_approval_days.removeClass('hide');
                    } else {
                        t.mdl.frmEl.no_of_approval_days.addClass('hide');
                    }
                    if(data.type.data.auto_resolve_ticket == 1){
                        t.mdl.frmEl.auto_resolve_ticket.prop('checked', true);
                    }

                    t.mdl.frmEl.tat.val(data.type.data.tat);
                    t.mdl.frmEl.response_sla.val(data.type.data.response_sla);
                    t.mdl.frmEl.close_ticket_after_days.val(data.type.data.close_ticket_after_days);
                    t.mdl.frmEl.reopen_ticket_until_days.val(data.type.data.reopen_ticket_until_days);
                    t.mdl.frmEl.workaround_sla.val(data.type.data.workaround_sla);
                    if(typeof data.type.data.priority_id != "undefined") {
                        t.mdl.frmEl.priorityId.val(data.type.data.priority_id);
                        //console.log(data.type.data.priority_id);
                    }
                    t.mdl.frmEl.priorityId.trigger("change");
                    t.mdl.frmEl.status.val(data.type.data.status).trigger("change");

                    t.mdl.frmEl.departmentId.empty();
                    if(typeof data.type.dropdown.department != "undefined") {
                        t.mdl.frmEl.departmentId.append(new Option(data.type.dropdown.department.text, data.type.dropdown.department.id, true, true));
                    }
                    t.mdl.frmEl.departmentId.trigger("change");

                    t.mdl.frmEl.ticketAttender.empty();
                    if(typeof data.type.dropdown.ticket_attender != "undefined") {
                        t.mdl.frmEl.ticketAttender.append(new Option(data.type.dropdown.ticket_attender.text, data.type.dropdown.ticket_attender.id, true, true));
                    }
                    t.mdl.frmEl.ticketAttender.trigger("change");

                    t.mdl.frmEl.autoAllocationGroup.empty();
                    if(typeof data.type.dropdown.auto_allocation_group != "undefined") {
                        $.each(data.type.dropdown.auto_allocation_group,function(i,d){
                            t.mdl.frmEl.autoAllocationGroup.append(new Option(d.text, d.id, true, true));
                        })
                    }
                    t.mdl.frmEl.autoAllocationGroup.trigger("change");
                    if(data.type.dropdown.custom_fieldset != "undefined" && data.type.data.custom_fieldset != null) {
                        t.mdl.frmEl.custom_fieldset.empty().append(new Option("", "", true, true));
                        $.each(data.type.dropdown.custom_fieldset, function(i,v) {
                            t.mdl.frmEl.custom_fieldset.append(new Option(v.name,v.id,true,true));
                        });
                        t.mdl.frmEl.custom_fieldset.val((data.type.data.custom_fieldset).split(",")).trigger('change');
                    }
            
                    if(typeof data.type.dropdown.parents != "undefined") {
                        t.fillOptsParentDropDown(data.type.dropdown.parents, data.type.data.parent_id);    
                    }
                    t.mdl.frmEl.approval_required.val(data.type.data.approval_required).trigger("change");

                    t.mdl.frmEl.is_form_required.val(data.type.data.is_form_required).trigger("change");
                    if(typeof data.type.data.form_id != "undefined") {
                        t.mdl.frmEl.form_id.val(data.type.data.form_id).trigger("change");
                        //console.log(data.type.data.form_id);
                    }

                    // t.mdl.frmEl.pab_id.val(data.type.data.pab_id).trigger("change");
                    $.each(data.type.dropdown.pab_id, function( key, value ) {
                        var option = new Option(value.text, value.id, true, true);
                        t.mdl.frmEl.pab_id.append(option).trigger('change');
                    });

                    if (typeof data.type.dropdown.company != "" && typeof data.type.dropdown.company != null) {
                        t.mdl.frmEl.company_id.val(data.type.dropdown.company).empty();
                        t.mdl.frmEl.company_id.append(new Option(data.type.dropdown.company.text, data.type.dropdown.company.id, true,true));
                    }
                    const allowedClients = ["rolepermission","ril","grdemo"];
                    if(allowedClients.includes(t.config.client) && data?.type?.dropdown?.roles?.length) {
                        data.type.dropdown.roles.forEach(function (value) {
                            t.mdl.frmEl.role_id.select2('trigger', 'select', {data: {id: value.id, text: value.text, selected: true}});
                        });
                    }

                    t.mdl.frmEl.remarks.summernote('code', data.type.data.remarks);
                    t.approvalChanged();

                    t.toggleListen(true);
                    t.mdl.modal("show");
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    /* to fill the parent id drop down */
    t.fillOptsParentDropDown = function(options, id) {
        try {
            t.mdl.frmEl.parentId.empty();
            t.mdl.frmEl.parentId.append(new Option(config.translations.No_ParentCategory, ''));

            if(Array.isArray(options) == true && options.length > 0) {
                $.each(options, function(i, v) {
                    var option = typeof id != "undefined" && id == v.id ? new Option(v.name, v.id, true, true) : new Option(v.name, v.id);
                    t.mdl.frmEl.parentId.append(option);
                });

                t.mdl.frmEl.parentId.trigger("change");
            }
            else {
                t.mdl.frmEl.parentId.trigger("change");
            }
        }
        catch(e) {
            console.log(e);
        }
    }

    t.deleteType = function(e) {
        e.preventDefault();
        var typeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + typeId;
        // vex.dialog.confirm({
        //     message: config.translations.are_you_delete_problemcategory,
        //     callback: function (value) {
        //         if(value == true) {
        //             var http = $.get(t.httpPostPath);
        //             http.done(function(data) {
        //                 if(typeof data == "object") {
        //                     if(data.status == "success") {
        //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
        //                         t.dTbl.ajax.reload();
        //                     }
        //                     else {
        //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '</p></div>'});
        //                     }
        //                 }
        //             });
        //             http.fail(function() {
        //                 alert("Something went wrong. Please check given details are correct");
        //             });
        //             http.always(function() {
        //                 t.httpCall = true;
        //             });
        //         }
        //     }
        // });
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete_problemcategory, 'warning', t.httpPostPath, t.dTbl, data);
    };
    
    t.onDepartmentChange = function(e) {
        e.preventDefault();
        t.mdl.frmEl.ticketAttender.empty().trigger("change");
        t.mdl.frmEl.autoAllocationGroup.empty().trigger("change");
        t.mdl.frmEl.parentId.empty();
        t.mdl.frmEl.parentId.append(new Option(config.translations.No_ParentCategory, ''));
        t.mdl.frmEl.parentId.trigger("change");
        
        var d = t.mdl.frmEl.departmentId.val();
        var c = t.mdl.frmEl.company_id.val();
        if(d > 0) {
            $.get(t.config.url.problem_categories_by_dept + '/' + d + '/' +c, function(result) {
                if(typeof result == "object" && result.status == "success" && result.data.length > 0) {
                    $.each(result.data, function(i, v) {
                        t.mdl.frmEl.parentId.append(new Option(v.name, v.id));
                    });
                    t.mdl.frmEl.parentId.trigger("change");
                }
            });
        }
    };
    
    t.toggleListen = function(s) {
        t.mdl.frmEl.departmentId.off("change", $.proxy(t.onDepartmentChange));
        if(typeof s != "undefined" && s == true) {
            t.mdl.frmEl.departmentId.on("change", $.proxy(t.onDepartmentChange));
        }
    };

    t.approvalChanged = function() {
        if(parseInt(t.mdl.frmEl.approval_required.val()) === 1) {
            t.mdl.frmEl.pab_id.closest(".parentcover").removeClass("hide");
            t.mdl.frmEl.is_form_required.closest(".parentcover").removeClass("hide");
            if(t.mdl.frmEl.is_form_required.val() == 1 || t.mdl.frmEl.is_form_required.val() == 2) {
                t.mdl.frmEl.form_id.closest(".parentcover").removeClass("hide");
            }
        }
        else {
            t.mdl.frmEl.pab_id.val('').trigger("change");
            // t.mdl.frmEl.pab_id.valid();
            t.mdl.frmEl.form_id.val('').trigger("change");
            // t.mdl.frmEl.form_id.valid();
            t.mdl.frmEl.is_form_required.val(0).trigger("change");
            t.mdl.frmEl.is_form_required.valid();
            t.mdl.frmEl.pab_id.closest(".parentcover").addClass("hide");
            t.mdl.frmEl.form_id.closest(".parentcover").addClass("hide");
            t.mdl.frmEl.is_form_required.closest(".parentcover").addClass("hide");
        }
    };

    var select2Opts = {width:"100%"};
    t.mdl.priorityId = function () {
        t.mdl.frmEl.priorityId.empty().append(new Option(config.translations.Select_Priority_Type, "", true, true));
        $.each(t.config.priorities, function (i, v) {
            t.mdl.frmEl.priorityId.append(new Option(v.name,v.id));
        });
        t.mdl.frmEl.priorityId.trigger("change");
    };
    t.mdl.frmEl.status.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.status.parent()}));
    t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.priorityId.parent(),data: t.config.priorities}));
    t.mdl.frmEl.departmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                let companyId = t.mdl.frmEl.company_id.val();
                if (!companyId) {
                    return false;
                }
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: companyId
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_department
    }));
    t.mdl.frmEl.parentId.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.parentId.parent(),placeholder: config.translations.Select_Parent_Category, allowClear: true}));
    t.mdl.frmEl.approval_required.select2($.extend({}, select2Opts,{dropdownParent: t.mdl.frmEl.approval_required.parent()}));
    var message = t.config.client == 'ltts' ? "You can select maximum four" : "You can select maximum two";
    // t.mdl.frmEl.pab_id.select2($.extend({}, select2Opts));
    t.mdl.frmEl.pab_id.select2({
        width: "100%",
        placeholder: "Authority Board (SRAT)",
        allowClear: false,
        multiple: true,
        closeOnSelect : true,
        maximumSelectionLength: t.config.client == 'ltts' ? 4 : 2,
        language: {
            maximumSelected: function () {
                return message;
            }
        }
    });
    t.mdl.frmEl.pab_id.on('select2:select', function(e){
        var elm = e.params.data.element;
        $elm = jQuery(elm);
        $t = jQuery(this);
        $t.append($elm);
        $t.trigger('change.select2');
    });

    t.mdl.form_id = function () {
        t.mdl.frmEl.form_id.empty().append(new Option("Form Name", "", true, true));
        if (parseInt(t.mdl.frmEl.is_form_required.val()) == 1) {
            $.each(t.config.form, function (i, v) {
                t.mdl.frmEl.form_id.append(new Option(v.form_name, v.id));
            });
        } else if (parseInt(t.mdl.frmEl.is_form_required.val()) == 2) {
            $.each(t.config.custom_form, function (i, v) {
                t.mdl.frmEl.form_id.append(new Option(v.name, v.id));
            });
        }
    
        t.mdl.frmEl.form_id.trigger("change");
    };
    t.mdl.frmEl.is_form_required.on('change', function () { t.mdl.form_id(); });
    t.mdl.frmEl.form_id.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.form_id.parent(),data: t.config.form}));
    t.mdl.frmEl.is_form_required.select2($.extend({}, select2Opts,{dropdownParent: t.mdl.frmEl.is_form_required.parent()}));
    // t.mdl.frmEl.serviceTypeId.select2(select2Opts);
    //t.mdl.frmEl.priorityId.select2($.extend({}, select2Opts, {placeholder: "Select Priority"}));
    t.mdl.frmEl.ticketAttender.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.ticketAttender.parent(),
        ajax: {
            url: t.config.url.getTicketAttendersByDepartment,
            dataType: "json",
            data: function (p) {
                let companyId = t.mdl.frmEl.company_id.val();
                if (!companyId) {
                    return false;
                }
                return {
                    search: p.term,
                    page: p.page || 1,
                    department_id: t.mdl.frmEl.departmentId.val(),
                    company_id: companyId
                };
            },
            delay: 200
        },
        //minimumInputLength: 1,
        allowClear:true,
        placeholder: config.translations.Select_the_User,
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "24px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }   
     }));

    t.mdl.frmEl.autoAllocationGroup.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.autoAllocationGroup.parent(),
        ajax: {
            url: t.config.url.getAllocationGroups,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    department_id: t.mdl.frmEl.departmentId.val()
                };
            },
            delay: 200
        },
        allowClear:true,
        placeholder: 'Select auto allocation group'
    }));
    t.filters.filter_by_role.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.getRoles,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 200
        },
        allowClear:true,
        placeholder: t.config.translations.filter_by_roles
    }));
    t.mdl.frmEl.role_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.role_id.parent(),
        ajax: {
            url: t.config.url.getRoles,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 200
        },
        allowClear:true,
        placeholder: t.config.translations.select_role
    }));

    t.mdl.frmEl.custom_fieldset.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.fetchFieldSet+"/2",
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: t.config.translations.select_fieldset,
    }));


    t.mdl.btn.clr.click(function() {
        t.mdl.frmEl.name.val(null).trigger("change");
        t.mdl.frmEl.category_tag.val(null);
        t.mdl.frmEl.departmentId.val(0).trigger("change");
        t.mdl.frmEl.ticketAttender.val(0).trigger("change");
        t.mdl.frmEl.autoAllocationGroup.val(0).trigger("change");
        t.mdl.frmEl.priorityId.val(null).trigger("change");
        t.mdl.frmEl.form_id.val(null).trigger("change");
        t.mdl.frmEl.parentId.val(null).trigger("change");
        t.mdl.frmEl.tat.val(null).trigger("change");
        t.mdl.frmEl.close_ticket_after_days.val(null).trigger("change");
        t.mdl.frmEl.reopen_ticket_until_days.val(null).trigger("change");
        t.mdl.frmEl.response_sla.val(null).trigger("change");
        t.mdl.frmEl.workaround_sla.val(null).trigger("change");
        t.mdl.frmEl.remarks.summernote('reset');
        t.mdl.frmEl.is_form_required.val(0).trigger("change");
        t.mdl.frmEl.pab_id.val(null).trigger("change");
        t.mdl.frmEl.number_of_days.val(null).trigger("change");
        t.mdl.frmEl.role_id.val(null).trigger("change");
        t.mdl.frmEl.number_of_days.prop('checked', false);
    });

    t.mdl.frmEl.remarks.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            // ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link', 'video']]
        ],
        minHeight: 200,
        focus: true
    });

    t.formChanged = function(e, val) {
        if(parseInt(t.mdl.frmEl.is_form_required.val()) == 1 || parseInt(t.mdl.frmEl.is_form_required.val()) == 2) {
            t.mdl.frmEl.form_id.closest(".parentcover").removeClass("hide");
        }
        else {
            t.mdl.frmEl.form_id.val("").trigger("change");
            t.mdl.frmEl.form_id.valid();
            t.mdl.frmEl.form_id.closest(".parentcover").addClass("hide");
        }
    };

    t.btnClrFilter = function() {
        t.filters.priority.val("").trigger("change");
        t.filters.department.val("").trigger("change");
        t.filters.allocation_group.val("").trigger("change");
        t.filters.authority_approval.val("null").trigger("change");
        t.filters.pab.val("").trigger("change");
        t.filters.form_name.val("").trigger("change");
        t.filters.based_on_category.val("null").trigger("change");
        t.filters.filter_by_escalation.val("null").trigger("change");
        t.filters.filter_by_escalation_for.val("null").trigger("change");
        t.filters.filter_by_escalation_to.val("null").trigger("change");
        t.filters.filter_by_status.val('null').trigger('change');
        t.filters.filter_by_role.val('null').trigger('change');
    };

    t.escalate_to = function() {
        var efor = $(this).val();
        $.ajax({
            url: t.config.url.getescalate_to,
            type:"GET",
            dataType: "json",
            data:{id:efor},
            success:function(data){
                t.filters.filter_by_escalation_to.empty().append(new Option(config.translations.filter_by_escalation_to, null, false, false));
                $.each(data, function(i, k) {
                    t.filters.filter_by_escalation_to.append(new Option(k.text, k.id, false, false));
                });
            }
        });
    }
    if(t.config.client == 'ltts' || t.config.client == 'grdemo' || t.config.client == 'rolepermission' ){
        document.getElementById('privilege_access').addEventListener("change", () => {
            if (document.getElementById('privilege_access').checked) {
                t.mdl.frmEl.no_of_approval_days.removeClass('hide');
            } else {
                t.mdl.frmEl.no_of_approval_days.addClass('hide');
                t.mdl.frmEl.number_of_days.val('');
            }
        });
    }
    var select2Opts = { width: "100%" };
    t.filters.priority.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Priority }));
    t.filters.department.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_department }));
    t.filters.allocation_group.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_allocation_group }));
    t.filters.authority_approval.select2($.extend({}, select2Opts, { placeholder: config.translations.nfilter_by_authority_approval }));
    t.filters.based_on_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Creator_Logger }));
    t.filters.filter_by_escalation.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Creator_Logger }));
    t.filters.filter_by_escalation_for.select2($.extend({}, select2Opts, false));
    t.filters.filter_by_escalation_to.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.getescalate_to,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: t.filters.filter_by_escalation_for.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.filter_by_escalation_to,
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "0px";
            return t.config.userDropdownFormat(data, imgPaddingLeft);
        }
    }));
     t.viewTask = function(e) {
        var ID = $(this).attr("data-id");
        window.open(config.url.getTaskList + '/' + ID, '_blank');
    }

    t.mdl.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.mdl.frmEl.company_id.parent(),
        ajax: {
            url: config.url.get_company_by_user_access,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select Company"
    })).on("change", function (e) {
        t.mdl.frmEl.departmentId.empty().trigger("change");
    });

    t.filters.filter_by_status.select2(select2Opts);
    t.filters.fun.reload_priority();
    t.filters.fun.reload_department();
    t.filters.fun.reload_allocation_group();
    t.filters.fun.reload_pab();
    t.filters.fun.reload_formname();
    t.filters.wrapper.on("click", "button", t.search);
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.mdl.btn.submit.on("click", $.proxy(t.handleSubmit));
    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    t.dTbl.ajax.reload();
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.search.on("click", $.proxy(t.search));
    t.btn.export.on("click", $.proxy(t.export));
    t.btn.exports.on("click", $.proxy(t.exportPDF));
    t.btn.add.on("click", $.proxy(t.addType));
    t.btn.import.on("click", $.proxy(t.import));
    t.page.on("click", ".dtActEdit", $.proxy(t.editType));
    t.page.on("click", ".dtActDel", $.proxy(t.deleteType));
    t.mdl.frmEl.approval_required.on("change", $.proxy(t.approvalChanged)).trigger("change");
    t.mdl.frmEl.departmentId.on("change", $.proxy(t.onDepartmentChange));
    t.mdl.frmEl.is_form_required.on("change", $.proxy(t.formChanged)).trigger("change");
    t.filters.filter_by_escalation_to.on("click", $.proxy(t.escalate_to));
    t.page.on('click','.view-task', $.proxy(t.viewTask));
    /*$("input[name='pab_id']").on('change', function() {
        $(this).valid();
    });*/
};

var EsclationClass = function(config) {
    var t = this;
    t.config = config;
    t.page = $("#page-content");
    t.table = t.page.find("#esclations");
    t.tbody = t.table.find("tbody");
    t.mdl = t.page.find("#EsclationModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.pbm_cat_name = t.mdl.find("#pbm_cat_name");
    t.mdl.history_link = t.mdl.find("#history_link");

    t.mdl.frm = t.mdl.find("#frm_esclation");

    t.mdl.frmEl = {};
    t.mdl.frmEl.esclate_to = t.mdl.frm.find('#esclate_to');
    t.mdl.frmEl.esclate_need_at = t.mdl.frm.find('#esclate_need_at');
    t.mdl.frmEl.sla_count = t.mdl.frm.find('#sla_count');
    t.mdl.frmEl.escalate_for = t.mdl.frm.find('#escalate_for');
    t.mdl.frmEl.escalate_group = t.mdl.frm.find('#esclate_group');
    t.mdl.frmEl.escalate_to_group = t.mdl.frm.find('.escalate_to_group');
    t.mdl.frmEl.escalate_to_user = t.mdl.frm.find('.escalate_to_user');
    t.mdl.frmEl.groups = t.mdl.frm.find('#groups');
    t.mdl.frmEl.users = t.mdl.frm.find('#users');
    t.mdl.frmEl.technician_mark_cc = t.mdl.frm.find('#technician_mark_cc');

    t.mdl.btn = {};
    t.mdl.btn.btnSubmit = t.mdl.frm.find('#btnSubmit');
    t.mdl.btn.btnClear = t.mdl.frm.find('#btnClear');

    t.prob_cat_id = null;
    t.escl_id = null;
    t.data = {};
    t.httpCall = true;

    t.showTable = function() {
        t.tbody.empty();
        try {
            if( typeof t.data.esclations != undefined && t.data.esclations instanceof Array && t.data.esclations.length > 0 ) {
                $.each(t.data.esclations, function(i,r) {
                    var sla_count =  (r.sla_count == 1) ? 'Yes' : 'No';
                    var technician_mark_cc = (r.technician_mark_cc == 1) ? 'Yes' : 'No';
                    var user_or_group = r.user_fname != "" ? r.user_fname : r.escalation_group;
                    var email = r.email != null ? r.email : "";
                    var row = "<tr>";
                    // row += "<td>" + r.esclate_stage_no + "</td>";
                    row += "<td><div>" + user_or_group + "</div><div>" + email + "</div>";
                    if(jQuery.inArray("ProblemCategoriesEsclationEdit", t.config.permissions) !== -1) {
                        row += "<a href='#' data-id='" + r.id + "' class='escl-edit btn-link mar-rgt'>Edit</a>";
                    }
                    if(jQuery.inArray("ProblemCategoriesEsclationDelete", t.config.permissions) !== -1) {
                        row += "<a href='#' data-id='" + r.id + "' class='escl-del btn-link'>Del</a></td>";
                    }
                    row += "<td>" + r.esclate_need_at + "</td>";
                    row += "<td>" + sla_count + "</td>";
                    row += "<td>" + technician_mark_cc + "</td>";
                    row += "</tr>";
                    t.tbody.append(row);
                });
            }
            else {
                t.tbody.append("<tr><td colspan='4' class='text-center'>No Esclation configured</td></tr>");
            }
        }
        catch(e) {
            console.log(e);
        }
    };

    t.loadForEdit = function(e) {
        try {
            t.clearFrm();
            t.escl_id = parseInt($(this).attr('data-id'));
            if(t.escl_id < 0) {
                throw Error("Unable to load the esclation to edit.");
            }

            var esclation = null;
            $.each(t.data.esclations, function(i,r) {
                // console.log(i, " - ", r);
                if( r.id == t.escl_id ) {
                    esclation = r;
                    return false;
                }
            });

            if(esclation.user_fname == "") {
                t.mdl.frmEl.escalate_to_user.addClass("hide");
                t.mdl.frmEl.escalate_to_group.removeClass("hide");
                t.mdl.frmEl.groups.prop("checked",true);
                t.mdl.frmEl.escalate_group.append(new Option(esclation.escalation_group, esclation.escalation_group_id, true, true));
            } else {
                t.mdl.frmEl.escalate_to_user.removeClass("hide");
                t.mdl.frmEl.escalate_to_group.addClass("hide");
                t.mdl.frmEl.users.prop("checked",true);
                t.mdl.frmEl.esclate_to.append(new Option(esclation.user_fname + (esclation.email ? ' - ' + esclation.email : ''),esclation.esclate_to, true, true));
            }

            // t.mdl.frmEl.esclate_to.append(new Option(esclation.user_fname + ' - ' + esclation.email, esclation.esclate_to, true, true));
            t.mdl.frmEl.esclate_need_at.val(esclation.esclate_need_at);
            t.mdl.frmEl.sla_count.val(esclation.sla_count).trigger('change');
            t.mdl.frmEl.technician_mark_cc.val(esclation.technician_mark_cc).trigger('change');
        }
        catch(e) {
            alert(e);
        }
    };

    t.loadEsclation = function() {
        t.clearFrm();
        try {
            t.prob_cat_id = parseInt($(this).attr('data-id'));
            if(t.prob_cat_id < 0) {
                throw Error("Unable to load the esclations of choosen problem category.");
            }

            if( t.mdl.frmEl.esclate_to.hasClass('select2-hidden-accessible') ) {
                t.mdl.frmEl.esclate_to.select2('destroy');
            }

            var select2Opts = { width:"100%"};
            t.mdl.frmEl.esclate_to.select2($.extend({}, select2Opts, {
                dropdownParent: t.mdl.frmEl.esclate_to.parent(),
                ajax: {
                    url: t.config.url.get_esclatable_users + '/' + t.prob_cat_id,
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
                placeholder: config.translations.Select_the_User,
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "24px";
                    return t.config.userDropdownFormat(data, imgPaddingLeft);
                }
            }));
            t.mdl.frmEl.escalate_group.select2($.extend({}, select2Opts, {
                dropdownParent: t.mdl.frmEl.escalate_group.parent(),
                ajax: {
                    url: t.config.url.get_esclatable_group + '/'+ t.prob_cat_id,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 200
                },
                allowClear: true,
                placeholder: "Select group",
            }));
            t.mdl.frmEl.sla_count.select2($.extend({}, select2Opts, {dropdownParent: t.mdl.frmEl.sla_count.parent(),allowClear: true, placeholder: config.translations.sla_count}));

            $.get(t.config.url.get_esclations_info + '/' + t.prob_cat_id).then(function(resp) {
                // console.log(resp);
                if(resp.status == "success") {
                    t.data.pbm_cat = resp.pbm_cat;
                    t.data.esclations = resp.esclations;
                    t.mdl.pbm_cat_name.text(t.data.pbm_cat.name);
                    t.mdl.history_link.attr('href', t.config.url.base_url +'/tickets/problem-categories/esclation-history/' + t.data.pbm_cat.id);
                    t.showTable();
                    t.mdl.frmEl.escalate_to_user.removeClass("hide");
                    t.mdl.frmEl.escalate_to_group.addClass("hide");
                    t.mdl.modal("show");
                }
            })
            .fail(function(e) {
                throw Error("Please refresh page and try again" + e);
            });
        }
        catch(e) {
            alert(e);
        }
    };

    t.clearFrm = function() {
        t.mdl.frmEl.users.prop("checked", true);
        t.mdl.frmEl.esclate_to.val("").trigger('change');
        t.mdl.frmEl.esclate_need_at.val("");
        t.mdl.frmEl.escalate_group.val("").trigger("change");
        t.mdl.frmEl.sla_count.val("").trigger('change');
        t.mdl.frmEl.technician_mark_cc.val("").trigger('change');
        t.escl_id = null;
        t.mdl.frmEl.escalate_to_user.removeClass("hide");
        t.mdl.frmEl.escalate_to_group.addClass("hide");
    };

    t.deleteEsclation = function(e) {
        e.preventDefault();
        try{
            t.escl_id = parseInt($(this).attr('data-id'));

            if(t.escl_id == "" || t.escl_id < 1) {
                throw Error("Unable to delete the esclation. Please refresh page and try again");
            }

            $.get(t.config.url.del_esclation + '/' + t.escl_id).then(function(resp) {
                t.escl_id = null;
                if(resp.status == "success") {
                    if( typeof resp.esclations != undefined && resp.esclations instanceof Array ) {
                        t.data.esclations = resp.esclations;
                    }
                }

                t.showTable();
                if( resp.msg != "" ) {
                    sweetAlert('center', 'success', resp);
                }
            })
            .fail(function(e) {
                throw Error("Please refresh page and try again" + e);
            });

        }
        catch(e) {
            alert(e);
        }
    }

    t.handleSubmit = function(e) {
        e.preventDefault();

        if(t.httpCall != true) {
            return false;
        }
        t.httpCall = false;

        var path = t.escl_id != null ? t.config.url.update_esclation + '/' + t.escl_id : t.config.url.add_esclation + '/' + t.prob_cat_id;

        var formData = new FormData(t.mdl.frm[0]);
        formData.append("problem_category_id", t.prob_cat_id);
        
        var http = $.ajax({
            url: path,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.clearFrm();
                    if( typeof data.esclations != undefined && data.esclations instanceof Array ) {
                        t.data.esclations = data.esclations;
                    }
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                } else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }

                t.showTable();
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.escalateFor = function(e) {
        var val = $(this).val();
        if(val == "groups") {
            t.mdl.frmEl.escalate_to_user.addClass("hide");
            t.mdl.frmEl.escalate_to_group.removeClass("hide");
            t.mdl.frmEl.groups.prop("checked",true);
        } else {
            t.mdl.frmEl.users.prop("checked", true);
            t.mdl.frmEl.escalate_to_user.removeClass("hide");
            t.mdl.frmEl.escalate_to_group.addClass("hide");
        }
    }

    t.mdl.frmEl.technician_mark_cc.select2($.extend({}, {
        dropdownParent: t.mdl.frmEl.technician_mark_cc.parent(),
        width:"100%",
        placeholder:"Select"
    }));
    
    t.page.on("click", ".dtActEscl", $.proxy(t.loadEsclation));
    t.tbody.on("click", ".escl-edit", $.proxy(t.loadForEdit));
    t.tbody.on("click", ".escl-del", $.proxy(t.deleteEsclation));
    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    t.mdl.btn.btnSubmit.on("click", $.proxy(t.handleSubmit));
    t.mdl.btn.btnClear.on("click", $.proxy(t.clearFrm));
    t.page.on("click",'.escalate_for', $.proxy(t.escalateFor));
};

var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.config.myApp = this;
    t.page = $("#page-content");
    t.table = t.page.find("#mytable");
    new ProblemCategory(t.config);
    new EsclationClass(t.config);

    t.config.userDropdownFormat = function (s,imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + s.text + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + s.email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left:" + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };
   
};
