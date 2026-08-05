var EscalationGroup = function (config) {
    var t = this;
    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.other_filters = t.config.other_filters || {};
    t.config.search = t.config.search || "";
    t.isLoadingForm = false;
    t.page = $("#main-escalation-group-wrapper");
    t.content = t.page.length ? t.page : $('section.content');
    t.table = t.content.find('#mytable');
    t.pageLength = t.content.find(".user-list-page-length");
    t.filterBadge = t.content.find(".filter-count-badge");
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    t.calendar = t.content.find('#dd');
    t.mdl = t.content.find('#esclationGroupMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.mdl.loader = t.mdl.find('#img');
    t.loader = t.mdl.loader;
    t.frm = t.mdl.find('#esclationGroup');
    
    t.mdl.addGroupUser = t.content.find('#addGroupUserFormMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.frm.addUser = t.mdl.addGroupUser.find('#addGroupUserForm');

    t.frmEl = {};
    t.frmEl.groupName = t.frm.find('#groupName');
    t.frmEl.groupDepartmentId = t.frm.find('#groupDepartmentId');
    t.frmEl.getCompanyId = t.frm.find('#getCompanyId');
    t.frmEl.groupCategory = t.frm.find('#groupCategory');
    t.frmEl.groupSubCategory = t.frm.find('#groupSubCategory');
    t.frmEl.location_based = t.mdl.find('#location_based');
    t.frmEl.groupID = t.mdl.find('#group_id');
    t.frm.groupLocalApprovalRequiredForDiv = t.mdl.find('.groupLocalApprovalRequiredForDiv');
    t.frmEl.subCategoryIdCvr = t.mdl.find('#sub_category_id_cvr');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.btn.submitUser = t.frm.addUser.find('#btnSubmit');
    
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");

    t.tr = function (key, fallback) {
        return t.config.translations[key] || fallback;
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.icons = {
        edit: '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>',
        trash: '<i class="bi bi-trash"></i>',
        user: '<i class="bi bi-people"></i>',
        clone: '<i class="bi bi-files"></i>'
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup, href) {
        var attrs = ' class="user-list-action-btn role-list-action-btn ' + t.escapeHtml(classes || "") + '"' +
            ' id="' + t.escapeHtml(id) + '"' +
            ' data-id="' + t.escapeHtml(id) + '"' +
            'data-bs-toggle="tooltip"'+
            ' title="' + t.escapeHtml(label) + '"' +
            ' aria-label="' + t.escapeHtml(label) + '"';

        if (href) {
            return '<a href="' + href + '"' + attrs + ' target="_blank">' + iconMarkup + '</a>';
        }

        return '<button type="button"' + attrs + '>' + iconMarkup + '</button>';
    };

    t.renderActionButtons = function (record) {
        var buttons = [];

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        buttons.push(t.actionButtonHtml(t.tr("edit_group", "Edit Group"), "open-edit-modal", record.id, t.icons.edit));
        buttons.push(t.actionButtonHtml(t.tr("manage_users", "Manage Users"), "", record.id, t.icons.user, t.config.url.manage_users + "/" + btoa(JSON.stringify({ group_id: record.id }))));
        buttons.push(t.actionButtonHtml(t.tr("delete_group", "Delete Group"), "open-delete is-delete", record.id, t.icons.trash));
        buttons.push(t.actionButtonHtml(t.tr("group_clone", "Clone Group"), "cloneBtn", record.id, t.icons.clone));

        return '<div class="user-list-actions role-list-actions justify-content-start">' + buttons.join("") + '</div>';
    };

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.filters = {
        wrapper: t.content.find("#advanceFilterModal")
    };

    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.prob_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.location_based = t.filters.wrapper.find("#filter_by_location_based");

    t.data = {};
    var select2Opts = { width: "100%" };
    t.frmEl.getCompanyId.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.getCompanyId.parent(),
        ajax: {
            url: t.config.url.getCompanyByUserAccess,
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
        placeholder: t.tr("select_company", "Select Company")
    }));

    t.frmEl.getCompanyId.on('change', function (e, isInit) {
        if (t.isLoadingForm || (t.editdata === true && isInit === true)) {
            return;
        }
        t.frmEl.groupDepartmentId.empty().val(null).trigger('change');
        t.frmEl.groupCategory.empty().val(null).trigger('change');
        t.frmEl.groupSubCategory.empty().val(null).trigger('change');
        problem_categories = [];
        sub_categories = [];
        t.updateSubCategoryVisibility();
    });


    t.filters.prob_category.select2({
        dropdownParent: t.filters.wrapper,
        width: "100%",
        placeholder: t.config.translations.prob_category,
    });
    t.filters.sub_category.select2({
        dropdownParent: t.filters.wrapper,
        width: "100%",
        placeholder: t.config.translations.sub_category,
    });
    t.filters.location_based.select2({
        dropdownParent: t.filters.wrapper,
        width: "100%",
        placeholder: t.config.translations.location_base,
    });
    t.frmEl.groupDepartmentId.select2({
        dropdownParent: t.frmEl.groupDepartmentId.parent(),
        width: "100%",
        placeholder: t.config.translations.department,
    });  
    t.frmEl.groupCategory.select2({
        dropdownParent: t.frmEl.groupCategory.parent(),
        width: "100%",
        placeholder: t.config.translations.prob_category,
    });  
    t.frmEl.groupSubCategory.select2({
        dropdownParent: t.frmEl.groupSubCategory.parent(),
        width: "100%",
        placeholder: t.config.translations.sub_category,
    });  
    t.frmEl.location_based.select2({
        dropdownParent: t.frmEl.location_based.parent(),
        width: "100%",
        placeholder: t.config.translations.location_base,
    });  
    var problem_categories = [];
    var sub_categories = [];
    t.fun = {
        reload_department : function() {
            t.frmEl.groupDepartmentId.empty().append(new Option(t.config.translations.department, "", false, false));
            $.get(t.config.url.getDepartments + "/" + t.config.company_id, function(data) {
                if(typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function(i,k) {
                        t.frmEl.groupDepartmentId.append(new Option(k.name, k.id, false, false));
                    });
                    t.frmEl.groupDepartmentId.trigger("change");
                }
            });
            t.frmEl.groupDepartmentId.trigger("change");
        },
        reload_problem_category : function(obj = null) {
           if (t.isLoadingForm) {
               return;
           }

           var department = t.frmEl.groupDepartmentId.val();
           var company_id = t.frmEl.getCompanyId.val();     
           t.frmEl.groupCategory.empty().append(new Option(t.config.translations.prob_category, "", false, false));
           t.frmEl.groupSubCategory.empty().append(new Option(config.translations.Select_Sub_Category, ""));
           problem_categories = [];
           sub_categories = [];
           t.updateSubCategoryVisibility();

            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department + "/" + company_id, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function(i, k) {
                            t.frmEl.groupCategory.append(new Option(k.name, k.id, false, false));                           
                        });
                    }
                    t.frmEl.groupCategory.val("").trigger("change");
                });
                
            }
            t.frmEl.groupCategory.trigger("change.select2");
            t.frmEl.groupSubCategory.trigger("change.select2");
        },
        reload_sub_category : function(selectedSubCategoryId) {
            if (t.isLoadingForm && (!selectedSubCategoryId || (typeof selectedSubCategoryId === "object" && selectedSubCategoryId.type))) {
                return;
            }

            if (selectedSubCategoryId && typeof selectedSubCategoryId === "object" && selectedSubCategoryId.type) {
                selectedSubCategoryId = null;
            }

            t.frmEl.groupSubCategory.empty().append(new Option(config.translations.Select_Sub_Category, ""));
            var type_val = parseInt($.trim(t.frmEl.groupCategory.val()));
            sub_categories = [];

            if (type_val > 0 && !isNaN(type_val)) {
                try {
                    $.each(problem_categories, function(i, v) {
                        if (v.id == type_val) {
                            sub_categories = Array.isArray(v.sub) ? v.sub : [];
                            if (Array.isArray(v.sub) && v.sub.length > 0) {                                                              
                                $.each(v.sub, function(j, k) {
                                    var isSelected = selectedSubCategoryId != null && String(k.id) === String(selectedSubCategoryId);
                                    t.frmEl.groupSubCategory.append(
                                        $('<option>')
                                            .val(k.id)
                                            .text(k.name)
                                            .attr('description', k.remarks)
                                            .attr('form', k.form_id)
                                            .prop('selected', isSelected)
                                    );                                     
                                });
                                return false;
                            }
                        }
                    });

                } catch (e) {
                    console.log(e);
                }
            }
            t.updateSubCategoryVisibility();            
            t.frmEl.groupSubCategory.val(selectedSubCategoryId || "").trigger("change");
        },
        reload_filter_department : function() {
            t.filters.department.select2({
                dropdownParent:  t.filters.wrapper,
                ajax: {
                    url: t.config.url.departments_with_company,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: $("#config-company").val(),
                        };
                    },
                },
                width: "100%",
                allowClear: true,
                placeholder: config.translations.filter_by_department
            });

            t.filters.department.trigger("change");
        },
        reload_filter_problem_category : function() {
            var department = t.filters.department.val();
            t.filters.prob_category.empty().append(new Option(t.config.translations.prob_category, "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function(data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function(i, k) {
                            t.filters.prob_category.append(new Option(k.name, k.id, false, false));
                        });
                        t.filters.prob_category.trigger("change");
                    }
                });

            }
            t.filters.prob_category.trigger("change");
            //  t.frmEl.groupSubCategory.val('').trigger('change');
        },
        reload_filter_sub_category : function() {
            t.filters.sub_category.empty().append(new Option(config.translations.Select_Sub_Category, ""));
            var type_val = parseInt($.trim(t.filters.prob_category.val()));
            // console.log(type_val);
            if (type_val > 0 && !isNaN(type_val)) {
                try {
                    $.each(problem_categories, function(i, v) {
                        sub_categories = v.sub;
                        if (v.id == type_val) {
                            if (Array.isArray(v.sub) && v.sub.length > 0) {
                                $.each(v.sub, function(j, k) {
                                    t.filters.sub_category.append($('<option>').val(k.id).text(k.name).attr('description', k.remarks).attr('form', k.form_id));
                                });
                                return false;
                            }
                        }
                    });
                } catch (e) {
                    console.log(e);
                }
            }
        },
    }
    /* to show hide the sub category field visibility */
    t.updateSubCategoryVisibility = function() {
        if (sub_categories.length > 0) {
            /*t.frmEl.groupSubCategory.rules("add", {
                required: true,
                str_name: true
            });*/
            t.frmEl.subCategoryIdCvr.removeClass("d-none");
        } else {
            t.frmEl.groupSubCategory.rules("remove");
            t.frmEl.subCategoryIdCvr.addClass("d-none");
        }
        
    };

    var select2Opts = {width:"100%"};
    t.frmEl.groupDepartmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.groupDepartmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.frmEl.getCompanyId.val()
                };
            },
            delay: 300
        },
        allowClear:true,
        placeholder: config.translations.select_the_department
    }));

    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        order: [[7, 'desc']],
        columnDefs: [
            {
                targets: 8,
                orderable: false,
                searchable: false,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return t.renderActionButtons(row.a || data);
                }
            }
        ],
        ajax: {
            url: t.config.url.getUserAllocationGroups,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || $.trim(t.searchbox.val() || "");
                d.department = t.filters.department.val();
                d.prob_category = t.filters.prob_category.val();
                d.sub_category = t.filters.sub_category.val();
                d.location_based = t.filters.location_based.val();
            },
        },
        language: {
            info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
            infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
            emptyTable: t.config.datatable_translations.empty_result,
            zeroRecords: t.config.datatable_translations.empty_result,
            infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
            paginate: {
                previous: t.config.datatable_translations.prev,
                next: t.config.datatable_translations.next
            }
        },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
            t.updateFilterBadge();
        },
        columns: [
            { data: 'a.name', defaultContent: "-" },
            { data: 'a.company_name', defaultContent: "-" },
            { data: 'a.dep_name', defaultContent: "-" },
            { data: 'a.pc_name', defaultContent: "-" },
            { data: 'a.sc_name', defaultContent: "-" },
            { data: 'a.group_member_count', defaultContent: "0" },
            { data: 'a.created_at_format', defaultContent: "-" },
            { data: 'a.updated_at_format', defaultContent: "-" },
            { data: 'a', defaultContent: null },
        ]
    });

    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val() || "");
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        if(t.filters.department.val() && t.filters.department.val() != '') {
            t.config.other_filters.department = t.filters.department.val();
        }
        if(t.filters.prob_category.val() && t.filters.prob_category.val() != '' ) {
            t.config.other_filters.prob_category = t.filters.prob_category.val();
        }
        if(t.filters.sub_category.val() && t.filters.sub_category.val() != '') {
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if(t.filters.location_based.val() && t.filters.location_based.val() != '') {
            t.config.other_filters.location_based = t.filters.location_based.val();
        }
        t.config.export_filters = btoa(JSON.stringify(jobj));
        t.updateFilterBadge();
    };

    t.updateFilterBadge = function () {
        var count = 0;

        $.each(t.config.other_filters || {}, function (key, value) {
            if (value !== "" && value != null) {
                count++;
            }
        });

        if (t.filterBadge.length) {
            t.filterBadge.text(count).toggleClass("d-none", count === 0);
        }
    };

    t.reload = function() {
        t.dTbl.ajax.reload(null, false);
    };

    t.search = function (e) {
        if (e && e.type === "keyup" && e.keyCode !== 13) {
            return;
        }

        var v = typeof t.searchbox.validate_str_param === "function"
            ? t.searchbox.validate_str_param()
            : $.trim(t.searchbox.val() || "");

        if (v === false) {
            t.config.search = "";
            alert(t.tr("please_enter_valid_search", "Please enter a valid search."));
            return false;
        }

        t.config.search = v;
        t.reload();
    };

    t.applyFilters = function () {
        t.cache_filter_values();
        t.reload();
        t.filters.wrapper.modal("hide");
    };

    t.openFilter = function (e) {
        e.preventDefault();
        t.filters.wrapper.modal("show");
    };

    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.download_url + "?q=" + t.config.export_filters;
    };

    t.changePageLength = function () {
        t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw();
    };

    t.createGroup = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath =  t.config.url.add;
        t.frm.addUser.trigger("reset");
        t.frmEl.groupDepartmentId.val("").trigger("change");
        t.frmEl.groupID.val("");
        var default_company_id   = $("#config-company").val();
        var default_company_text = $("#config-company option:selected").text();
        if (default_company_id !=0) {
            let option = new Option(default_company_text,default_company_id, true, true);
            t.frmEl.getCompanyId.empty().append(option).trigger("change");
        }else{
            t.frmEl.getCompanyId.val("").trigger("change")
        }
        t.mdltitle.text(t.config.translations.add_group);
        t.btn.submit.text(t.config.translations.save);
        t.mdl.loader.hide();
        t.btn.submit.prop('disabled', false);
        t.mdl.modal("show");
    };

    t.addGroupUser = function (e) {
        e.preventDefault();
        t.loader.hide();
        t.mdl.addGroupUser.modal("show");
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.addGroupUser;
        t.resetFrm();
    };

    t.handlesubmit = function (e) {
        e.preventDefault();

        if( t.frmValidator.form() == false ) {
            return false;
        }
        
        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);
        t.btn.submit.prop('disabled', true);
        t.mdl.loader.show();
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // vex.dialog.alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.btn.submit.prop('disabled', false);
            t.mdl.loader.hide();
        });
    };

    t.handleUsersubmit = function (e) {
        e.preventDefault();
        var frmData = new FormData(t.frm.addUser[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.mdl.addGroupUser.modal("hide");
                    t.userdTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            sweetAlert('center', 'error', data);
            // vex.dialog.alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.loader.hide();
        });
    };

    t.loadForm = function (obj) {
        t.isLoadingForm = true;
        t.resetFrm();
        t.frmEl.groupName.val(obj.data.name);        
        if (typeof obj.opts == "object" && typeof obj.opts.company_id == "object") {
            t.frmEl.getCompanyId.empty().append(new Option(obj.opts.company_id.text, obj.opts.company_id.id, true, true)).trigger("change");
        }
        if (typeof obj.opts == "object" && typeof obj.opts.department_id == "object") {
            t.frmEl.groupDepartmentId.empty().append(new Option(obj.opts.department_id.text, obj.opts.department_id.id, true, true)).trigger("change");
        }
        problem_categories = obj.opts.problem_categories || [];
        t.frmEl.groupCategory.empty().append(new Option(t.config.translations.prob_category, "", false, false));
        $.each(problem_categories, function(i, d) {
            var op = (d.id == obj.data.problem_category_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
            t.frmEl.groupCategory.append(op);
        });
        t.frmEl.groupCategory.val(obj.data.problem_category_id).trigger("change");
        t.fun.reload_sub_category(obj.data.sub_category_id);
        t.frmEl.location_based.val(obj.data.location_based).trigger("change"); 
        t.isLoadingForm = false;
    };
       
    t.resetFrm = function () {
        var wasLoadingForm = t.isLoadingForm;
        t.isLoadingForm = true;
        t.frmEl.groupName.val('');
        t.frmEl.getCompanyId.empty().val('').trigger("change");
        t.frmEl.groupDepartmentId.empty().val('').trigger("change");
        t.frmEl.groupCategory.empty().val('').trigger("change");
        t.frmEl.groupSubCategory.empty().val('').trigger("change");
        t.frmEl.location_based.val("").trigger("change");
        problem_categories = [];
        sub_categories = [];
        t.updateSubCategoryVisibility();
        t.frmValidator.resetForm();
        t.frm.find("label.error").remove();
        t.frm.find(".amg-form-error-wrap").empty();
        t.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.frm.find(".error").removeClass("error");
        t.isLoadingForm = wasLoadingForm;
    };

    t.editGroup = function (e) {
        e.preventDefault();
        var id = $(this).attr("id");
        t.httpPostPath = t.config.url.editGroupDetails + "/" + id;
        t.resetFrm();
        t.mdltitle.text(t.config.translations.edit_group);
        t.btn.submit.text(t.config.translations.save_changes);
        t.btn.submit.prop('disabled', true);
        t.mdl.loader.show();
        t.mdl.modal("show");
        var http = $.get(t.config.url.getGroupDetails + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.loadForm(data);
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.btn.submit.prop('disabled', false);
            t.mdl.loader.hide();
            t.httpCall = true;
        });
    };

    t.cloneGroup = function (e) {
        e.preventDefault();
        t.mdltitle.text(t.config.translations.group_clone);
        var id = $(this).attr("id");
        t.httpPostPath = t.config.url.editGroupDetails + "/" + id;
        var http = $.get(t.config.url.getGroupDetails + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.isLoadingForm = true;
                    t.resetFrm();
                    t.mdl.modal("show");
                    t.btn.submit.text(t.config.translations.save_changes);
                    t.frm.find('#group_id').val(id);
                    if (typeof data.opts == "object" && typeof data.opts.company_id == "object") {
                        t.frmEl.getCompanyId.empty().append(new Option(data.opts.company_id.text, data.opts.company_id.id, true, true)).trigger("change");
                    }
                    if (typeof data.opts == "object" && typeof data.opts.department_id == "object") {
                        t.frmEl.groupDepartmentId.empty().append(new Option(data.opts.department_id.text, data.opts.department_id.id, true, true)).trigger("change");
                    }

                    problem_categories = data.opts.problem_categories || [];
                    t.frmEl.groupCategory.empty().append(new Option(t.config.translations.prob_category, "", false, false));
                    $.each(problem_categories, function(i, d) {
                        var op = (d.id == data.data.problem_category_id) ? new Option(d.name, d.id, true, true) : new Option(d.name, d.id);
                        t.frmEl.groupCategory.append(op);
                    });
                    t.frmEl.groupCategory.val(data.data.problem_category_id).trigger("change");
                    t.fun.reload_sub_category(data.data.sub_category_id);
                    t.frmEl.location_based.val(data.data.location_based).trigger("change"); 
                    t.isLoadingForm = false;
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
            // alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.httpCall = true;
        });


        t.httpPostPath =  t.config.url.add;
        // t.mdl.modal('show');
       
    };

   

    t.deleteGroup = function (e) {
        e.preventDefault();
        var recId = $(this).attr("id");
        t.httpPostPath = t.config.url.delete + "/" + recId;
        // vex.dialog.confirm({
        //     message: t.config.translations.confirmation_message,
        //     callback: function (value) {
        //         if (value == true) {
        //             var http = $.get(t.httpPostPath);
        //             http.done(function (data) {
        //                 if (typeof data == "object") {
        //                     if (data.status == "success") {
        //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
        //                         t.dTbl.ajax.reload();
        //                     }
        //                     else {
        //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        //                     }
        //                 }
        //             });
        //             http.fail(function () {
        //                 alert(t.config.translations.something_wrong);
        //             });
        //             http.always(function () {
        //                 t.httpCall = true;
        //             });
        //         }
        //     }
        // });
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.confirmation_message,'warning', t.httpPostPath, t.dTbl, data);
    };

    t.showLocationApprovalForDiv = function(e) {
        e.preventDefault();
        if(t.frmEl.groupLocationApprovalRequired.val() == 1) {
            t.frm.groupLocalApprovalRequiredForDiv.removeClass('hide');
        } else {
            t.frm.groupLocalApprovalRequiredForDiv.addClass('hide');
        }
    }

    t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) {
            return $();
        }

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };

    t.updateValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.frmValidator = t.frm.validate({
        // onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {   

            groupName: {
                required: true,
                clean_text_only: true,
            },          
            getCompanyId: {
                required: true
            },
            groupDepartmentId: {
                required: true
            },
            groupCategory: {
                required: true,
            },
            groupSubCategory: {
                required: false,
            },
            location_based: {
                required: true,
            },
        },
        errorPlacement: function(error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }

            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.resetFrmFilter = function() {
        t.filters.department.val('').trigger("change");
        t.filters.prob_category.val('').trigger("change");
        t.filters.sub_category.val('').trigger("change");
        t.filters.location_based.val('').trigger("change");
        t.filters.wrapper.modal("hide");
        t.cache_filter_values();
        t.reload();
    }

    t.bulkImport = function(e){
        window.location = t.config.url.bulkImport;
    }

    t.content.on('click', '.open-add-modal', $.proxy(t.createGroup));
    t.content.on('click', '.open-addGroupUserForm-modal', $.proxy(t.addGroupUser));
    
    t.fun.reload_department();
    t.fun.reload_filter_department();
    t.content.on('click', '.open-edit-modal', $.proxy(t.editGroup));
    t.content.on('click', '.open-delete', $.proxy(t.deleteGroup));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));

    t.btn.submitUser.on('click', $.proxy(t.handleUsersubmit));

    t.content.on('click', '.cloneBtn', $.proxy(t.cloneGroup));
    t.content.on('keyup', '.plain-search', $.proxy(t.search));
    t.filters.btnfilterclr.on("click", t.resetFrmFilter);
    t.filters.wrapper.on("click", ".btn-filter", $.proxy(t.applyFilters));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
    t.content.on("click", ".btn-download", $.proxy(t.download));
    t.content.on("change", ".user-list-page-length", t.changePageLength);
    t.filters.department.on("change", $.proxy(t.fun.reload_filter_problem_category));
    t.filters.prob_category.on("change", $.proxy(t.fun.reload_filter_sub_category));
    t.frmEl.groupDepartmentId.on("change", $.proxy(t.fun.reload_problem_category));
    t.frmEl.groupCategory.on("change", $.proxy(t.fun.reload_sub_category));
    t.dTbl.ajax.reload();
    t.content.on('click','.escalation-bulk-import',$.proxy(t.bulkImport));
};

