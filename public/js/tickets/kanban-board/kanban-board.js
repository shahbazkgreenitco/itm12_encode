var KanbanBoard = function (config) {

    var t = this;
    t.showDeletedUsers = false;
    t.config = config;
    t.config.archived = false;
    t.content = $("#all-kanban-list-wrapper");
    t.table = t.content.find("#mytable");
    t.searchbox = t.table.find(".searchbox");
    t.searchbtn = t.table.find(".btn-searchbox");

    t.mdl = $("#all-kanban-list-wrapper").find("#addKanbanFormMdl");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");
    t.mdl.frm = t.mdl.find("#addKanbanForm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.company = t.mdl.frm.find("#company_id");
    t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.problem_category_id = t.mdl.frm.find("#problem_category_id");
    t.mdl.frmEl.sub_category_id = t.mdl.frm.find("#sub_category_id");
    t.mdl.frmEl.board_items_type = t.mdl.frm.find("#board_items_type");
    t.mdl.frmEl.board_custom_field_set = t.mdl.frm.find("#custom_fieldset");
    t.mdl.frmEl.archive_days = t.mdl.frm.find("#archive_days");
    t.mdl.frmEl.item = t.mdl.frm.find("#item");
    t.mdl.frmEl.auto_comment_on_card_change = t.mdl.frm.find("#auto_comment_on_card_change");
    t.mdl.frmEl.access_to_all_department_technician = t.mdl.frm.find("#access_to_all_department_technician");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.description = t.mdl.frm.find("#description");

    t.mdlviewMoreModal = $("#all-kanban-list-wrapper").find("#viewMoreModal");
    t.mdlviewMoreModal.title = t.mdlviewMoreModal.find("#modalTitle");
    t.mdlviewMoreModal.body = t.mdlviewMoreModal.find("#modalBody");

    t.mdlKanbanHistory = $("#all-kanban-list-wrapper").find("#mdl-history");
    t.mdlKanbanHistory.title = t.mdlKanbanHistory.find(".modal-title");
    t.mdlKanbanHistory.board_details = t.mdlKanbanHistory.find("#board_details");
    t.result = t.mdlKanbanHistory.find("#result");
    t.boardName=t.mdlKanbanHistory.find("#subject");
    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};
    t.openFilter=t.content.find(".btn-open-filter");
    t.statuses = [];

    t.filters = {
        wrapper: t.content.find("#advance-filters")
    };
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.filters.btnfilterclr = t.content.find("#btnClrFilter"),
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.prob_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
    t.filters.date_range = t.filters.wrapper.find("#daterange");
    var problem_categories = [];
    var sub_categories = [];

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.icons = {
        edit: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
               </svg>`,
        trash: `<svg viewBox="0 0 15 17" fill="none" >
                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                </svg>`,
        user: `<i class="bi bi-person-fill-add"></i>`,
        history:  `<i class="bi bi-clock-history"></i>`
    };
    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        var safeLabel = t.escapeHtml(label);
        var safeId = t.escapeHtml(id);
        var safeClasses = t.escapeHtml(classes || "");

        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ',
            safeClasses,
            '" data-id="',
            safeId,
            '" title="',
            safeLabel,
            '" aria-label="',
            safeLabel,
            '">',
            iconMarkup,
            '</button>'
        ].join("");
    };
    

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                var a = [];
                if (jQuery.inArray("KanbanBoardEdit", t.config.permissions) !== -1) {
                    a.push( t.actionButtonHtml(t.config.translations.edit || "Edit","edit-kanban", d.id, t.icons.edit));
                }
                if (jQuery.inArray("KanbanBoardDelete", t.config.permissions) !== -1) {
                    a.push(t.actionButtonHtml(t.config.translations.delete || "Delete","delete-kanban",d.id,t.icons.trash)
                    );
                }
                if (jQuery.inArray("KanbanBoardViewHistory", t.config.permissions) !== -1) {
                    a.push(t.actionButtonHtml("Kanban History" || "History","kanban-history",d.id,t.icons.history)
                    );
                }
                if (jQuery.inArray("KanbanBoardMembers", t.config.permissions) !== -1) {
                    var memberUrl = t.config.url.member_kanban + "/" + d.id;
                    var btnHtml = ['<a href="', memberUrl,'" target="_blank" class="user-list-action-btn role-list-action-btn open-member" data-id="', d.id,'" title="', t.config.translations.members || "Manage Users",'" aria-label="', t.config.translations.members || "Manage Users",'">', t.icons.user,'</a>'].join("");
                    a.push(btnHtml);
                }
                if (a.length === 0) {
                    return '<span class="user-list-empty">-</span>';
                }
                return [
                    '<div class="user-list-actions role-list-actions justify-content-start">',
                    a.join(""),
                    '</div>'
                ].join("");
            };
        },

        renderListWithMore: function (items, type, id) {
         if (!items || !Array.isArray(items) || items.length === 0) {
                console.warn('Items is empty or not an array for', type);
                return "";
            }

            var a = [];
            var moreItemsCount = items.length - 3;
            $.each(items.slice(0, 3), function (i, v) {
                a.push('<span class="p-1 mt-1 badge bg-dark">' + v.name + '</span>');
            });

            if (moreItemsCount > 0) {
                a.push('<a href="javascript:void(0)" class="view-more-detail" data-id="' + id + '" data-type="' + type + '">+' + moreItemsCount + '</a>');
            }

            var result = a.join(" ");
            return result;

        },
        department: function () {
            return function (d) {
                return t.tblHelpers.renderListWithMore(d.department, "department", d.id);
            };
        },
        categories: function () {
            return function (d) {
                return t.tblHelpers.renderListWithMore(d.categories, "categories", d.id);
            };
        },
        subCategories: function () {
            return function (d) {
                return t.tblHelpers.renderListWithMore(d.subCategories, "subCategories", d.id);
            };
        }
    };

    $(document).on("click", ".view-more-detail", function () {
        var id = $(this).data("id");
        var type = $(this).data("type");
        var rowElement = $(this).closest("tr");
        var data = t.dTbl.row(rowElement).data();

        if (!data) {
            console.error("No data found for row:", rowElement);
            return;
        }

        var list = [];
        if (data.a && data.a.hasOwnProperty(type)) {
            list = data.a[type];
        }

        var content = list.map(function (v) {
            return `<span class="p-2 mt-1 badge bg-dark">${v.name || v}</span>`;
        }).join(" ");

        var titleMap = {
            "categories": "Problem Category",
            "subCategories": "Sub Categories",
            "department": "Department"
        };
        var modalTitle = titleMap[type] || type.charAt(0).toUpperCase() + type.slice(1);
        t.mdlviewMoreModal.title.text(modalTitle);
        t.mdlviewMoreModal.body.html(content);
        t.mdlviewMoreModal.modal("show");
    });


    t.fun = {
        reload_problem_category: function (obj = null) {
            var department = t.mdl.frmEl.department_id.val();
            t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
            t.mdl.frmEl.problem_category_id.empty().append(new Option("", "", false, false));
            t.mdl.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            if (k.status != 0) {
                                let truncatedName = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                                let option = new Option(truncatedName, k.id, false, false);
                                $(option).attr("title", k.name);
                                t.mdl.frmEl.problem_category_id.append(option);
                            }
                        });
                        // 🔽 Force select2 to re‑read the new options
                        t.mdl.frmEl.problem_category_id.trigger('change');
                    }
                });
            }
        },
        reload_filter_department: function () {
            t.filters.department.empty().append(new Option("Select Department", "", false, false));
            $.get(t.config.url.departments_based_on_privilage + "/" + t.config.user.company_id, function (data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function (i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
            t.filters.department.trigger("change");
        },
        reload_filter_problem_category: function () {
            var department = t.filters.department.val();
            t.filters.prob_category.empty().append(new Option(t.config.translations.select_prob_category, "", false, false));
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            t.filters.prob_category.append(new Option(k.name, k.id, false, false));
                        });
                        t.filters.prob_category.trigger("change");
                    }
                });

            }
            t.filters.prob_category.trigger("change");
        },
        reload_filter_sub_category: function () {
            t.filters.sub_category.empty().append(new Option(config.translations.select_sub_category, ""));
            var type_val = parseInt($.trim(t.filters.prob_category.val()));
            if (type_val > 0 && !isNaN(type_val)) {
                try {
                    $.each(problem_categories, function (i, v) {
                        sub_categories = v.sub;
                        if (v.id == type_val) {
                            if (Array.isArray(v.sub) && v.sub.length > 0) {
                                $.each(v.sub, function (j, k) {
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

    t.mdl.frmEl.company.select2($.extend({}, select2Opts, {
        dropdownParent: $("#addKanbanFormMdl"),
        placeholder: "Select Company",
        width:'100%',
        ajax: {
            url: t.config.url.get_company_by_user_access,
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
    }))

    t.mdl.frmEl.department_id.select2({ width: "100%", dropdownParent: t.mdl });
    t.mdl.frmEl.problem_category_id.select2({ width: "100%",dropdownParent: t.mdl, placeholder: t.config.translations.select_prob_category });
    t.mdl.frmEl.sub_category_id.select2({ width: "100%", dropdownParent: t.mdl, });
    t.mdl.frmEl.board_items_type.select2({ width: "100%", dropdownParent: t.mdl.frmEl.board_items_type.parent() });
    t.mdl.frmEl.item.select2({ width: "100%", dropdownParent: t.mdl.frmEl.item.parent(),  placeholder: "Select item" });
    t.filters.department.select2({ width: "100%",  dropdownParent: t.filters.wrapper });
    t.filters.prob_category.select2({ width: "100%" ,dropdownParent: t.filters.wrapper });
    t.filters.sub_category.select2({ width: "100%",dropdownParent: t.filters.wrapper });
    t.filters.based_on.select2({ width: "100%",dropdownParent: t.filters.wrapper });

    t.mdl.frmEl.company.on('select2:select select2:unselect', function() {
        t.mdl.frmEl.department_id.val(null).trigger('change');
    });

    t.loadStatuses = function () {
       $.ajax({
            url: config.url.getStatuses,
            type: "GET",
            data: {
                company_id: $('#company_id').val()
            },
            success: function (response) {
                t.statuses = response;
                if (t.mdl.frmEl.board_items_type.val() == "1") {
                    t.itemSetting();
                }
            }
        });
    };

    t.mdl.frmEl.company.on("change", function () {
        t.loadStatuses();
    });

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [10],
            },
            {
                targets: 3,
                render: function (data, type, row) {
                    if (type === "display") {
                        return "<a href='" + config.url.member_kanban + "/" + row.a.id + "' target='_blank'>" + data + "</a>";
                    }
                    return data;
                },
            },
            {
                targets: 4,
                render: t.tblHelpers.department(),
            },
            {
                targets: 5,
                render: t.tblHelpers.categories(),
            },
            {
                targets: 6,
                render: t.tblHelpers.subCategories(),
            },

            {
                targets: 10,
                render: t.tblHelpers.actions(),
            },
        ],
        order: [[9, "desc"]],
        processing: true,
        serverSide: true,
        searching:false,
        lengthChange:false,
        ajax: {
            url: t.config.url.KanbanBoardList,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search = $(".search_box").val();
                d.filters = t.config.export_filters;
            },
        },
        columns: [
            {
                data: "a.name",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        let truncated = data.length > 30 ? data.substring(0, 30) + "..." : data;
                        return `<span title="${data}">${truncated}</span>`;
                    }
                    return data;
                }
            },
            { data: "a.company_name" },
            {
                data: "a.board_items_type",
                render: function(data, type, row){
                    if(data===1){
                        return t.config.translations.board_type_ticket
                    }
                    else if(data===2){
                        return  t.config.translations.board_type_custom
                    }
                    else{
                        return "-"
                    }
                }
            },
            { data: "a.member_count" },
            { data: "a" },
            { data: "a" },
            { data: "a" },
            { data: "a.owner" },
            { data: "a.created_at_format" },
            { data: "a.updated_at_format" },
            { data: "a" },
        ],
        scrollX: true,
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1,
            },
    });

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.frmValidator = $('#addKanbanForm').validate({
        onsubmit: false,
        ignore: [],
        rules: {
            name: {
                required: true,
                clean_text_only: true,
            },
            board_items_type: {
                required: true,
            },
            archive_days: {
                min: 1
            },
            'item[]': {
                required: true,
                clean_text_only: true
            },
            description: {
                maxSummernoteChars: 2000
            },

        },
        errorPlacement: function(error, element) {
         error.insertAfter(element.parent());
        }
    });

    // save ticket type
    t.saveData = function (e) {
        if (t.frmValidator.form() == false) {
            return false;
        }
        if (!t.httpCall) return;
        t.httpCall = false;

        var frmData = new FormData(t.mdl.frm[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.mdl.frm.attr('action'),
            type: "POST",
            data: frmData,
            processData: false,
            contentType: false
        });
        http.done(function (data) {

            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.httpCall = true;
        });
        http.always(function () {
            t.httpCall = true;
        });
    };
    
    t.editKanbanBoard = function (e) {
        e.preventDefault();
        t.resetForm();
        var id = $(this).attr("data-id");
        var http = $.get(t.config.url.edit_kanaban + "/" + id);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.frm.attr('action', t.config.url.update_kanban + "/" + id);
                    t.mdl.title.text(t.config.translations.edit);
                    t.config.board_creator = data.data.created_by;

                    if (data.opts.company != null) {
                        var existingOption = t.mdl.frmEl.company.find('option[value="' + data.opts.company.id + '"]');
                        if (!existingOption.length) {
                            t.mdl.frmEl.company.append(new Option(data.opts.company.text, data.opts.company.id, true, true));
                        }
                        t.mdl.frmEl.company.val(data.opts.company.id).prop('disabled', true);
                    }

                    if (data.opts.department != null) {
                        t.mdl.frmEl.department_id.empty();
                        var deptIds = [];
                        $.each(data.opts.department, function (i, d) {
                            t.mdl.frmEl.department_id.append(new Option(d.name, d.id, false, false));
                            deptIds.push(d.id);
                        });
                        t.mdl.frmEl.department_id.val(deptIds);
                    }

                    if (data.opts.problem_categories != null) {
                        problem_categories = data.opts.problem_categories;
                        t.mdl.frmEl.problem_category_id.empty().append(new Option(t.config.translations.prob_category, "", false, false));
                        var catIds = [];
                        $.each(data.opts.problem_categories, function (i, d) {
                            t.mdl.frmEl.problem_category_id.append(new Option(d.name, d.id, false, false));
                            catIds.push(d.id);
                        });
                        t.mdl.frmEl.problem_category_id.val(catIds);
                    }

                    if (data.opts.sub && data.opts.sub.length > 0) {
                        t.mdl.frmEl.subCategoryIdCvr.removeClass('hide');
                        t.mdl.frmEl.sub_category_id.empty();
                        t.mdl.frmEl.sub_category_id.append(new Option('Select Sub Category', '', false, false));
                        var selectedSubIds = data.data.sub_category_id ? String(data.data.sub_category_id).split(',').map(function(v){ return v.trim(); }) : [];
                        $.each(data.opts.sub, function (i, d) {
                            var isSelected = selectedSubIds.indexOf(String(d.id)) !== -1;
                            t.mdl.frmEl.sub_category_id.append(new Option(d.name, d.id, isSelected, isSelected));
                        });
                        if (selectedSubIds.length > 0) {
                            t.mdl.frmEl.sub_category_id.val(selectedSubIds).trigger('change');
                        }
                    } else {
                        t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
                    }
                    t.loadForm(data);
                    t.mdl.modal("show");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = { 'msg': config.translations.something_went_wrong };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };
    const escapeHtmlAttr = (str) => {
        return str
            .replace(/&/g, "&amp;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;");
    };

    t.loadForm = function (objs) {
        var obj = objs.data;
        t.mdl.frmEl.name.val(obj.name);
        t.mdl.frmEl.board_items_type.val(obj.board_items_type).trigger("change");

        if (objs.opts.fieldset && Array.isArray(objs.opts.fieldset)) {
            objs.opts.fieldset.forEach(function (fieldset) {
                if (!t.mdl.frmEl.board_custom_field_set.find("option[value='" + fieldset.id + "']").length) {
                    t.mdl.frmEl.board_custom_field_set.append(
                        new Option(fieldset.name, fieldset.id, true, true)
                    );
                }
            });
            let ids = objs.opts.fieldset.map(fieldset => fieldset.id);
            t.mdl.frmEl.board_custom_field_set.val(ids).trigger("change");
        }

        var itemID = [];
        $.each(objs.opts.items, function (i, j) {
            itemID.push(j.item_name);
            if (t.mdl.frmEl.item.find("option[value='" + escapeHtmlAttr(j.item_name) + "']").length == 0) {
                t.mdl.frmEl.item.append(new Option(j.item_name, j.item_name, true, true));
            }
        });

        t.mdl.frmEl.item.val(itemID).trigger("change");
        // t.mdl.frmEl.sub_category_id.val(obj.sub_category_id).trigger("change");
        if (obj.auto_comment_on_card_change == 1) {
            t.mdl.frmEl.auto_comment_on_card_change.prop('checked', true);
        }
        if (obj.access_to_all_department_technician == 1) {
            t.mdl.frmEl.access_to_all_department_technician.prop('checked', true);
        }
        t.mdl.frmEl.description.summernote('code', obj.Description);
        t.mdl.frmEl.archive_days.val(obj.archive_days);
        // t.mdl.frmEl.sub_category_id.val(obj.sub_category_id).trigger('change');


    };

    // reset form
    t.resetForm = function () {
        t.frmValidator.resetForm();
        t.mdl.frm.trigger("reset");

        t.mdl.frmEl.name.val('');
        t.mdl.frmEl.archive_days.val('');
        t.mdl.frmEl.department_id.val("").trigger('change');
        t.mdl.frmEl.problem_category_id.val('').trigger('change');
        t.mdl.frmEl.sub_category_id.val('').trigger('change');
        t.mdl.frmEl.board_items_type.val('').trigger('change');
        t.mdl.frmEl.board_custom_field_set.val('').trigger('change');
        t.mdl.frmEl.item.val('').trigger('change');
        t.mdl.frmEl.auto_comment_on_card_change.prop('checked', false);
        t.mdl.frmEl.access_to_all_department_technician.prop('checked', false);
        t.mdl.frmEl.access_to_all_department_technician.prop('checked', false);
        t.mdl.frmEl.description.val("").summernote('code', '');

    }
    var select2Opts = {
        width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 25 ? data.text.substring(0, 25) + "..." : data.text;
        }
    };
    t.mdl.frmEl.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.department_id.parent(),
        ajax: {
            url: function(params) {
                // Read the current company value; fallback to user's company if empty
                var companyId = t.mdl.frmEl.company.val() || t.config.user.company_id;
                return t.config.url.departments_based_on_privilage + "/" + companyId;
            },
            dataType: "json",
            delay: 200,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.data, function (item) {
                        return { id: item.id, text: item.name };
                    })
                };
            }
        },
        placeholder: config.translations.select_department
    }));


    t.mdl.frmEl.sub_category_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.sub_category_id.parent(),
        ajax: {
            url: t.config.url.sub_category,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: t.mdl.frmEl.problem_category_id.val()
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
        placeholder: config.translations.select_sub_category,

    }))

    t.mdl.frmEl.board_custom_field_set.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.board_custom_field_set.parent(),
        ajax: {
            url: t.config.url.get_kanban_fieldsets,
            type: 'POST',
            dataType: "json",
            delay: 200,
            data: function (params) {
                return {
                    search: params.term || '',
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            }
        },
        placeholder: "Kanban Board Custom Fields",
        multiple: true,
    }));

    t.searchData = function (e) {
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        $("#advance-filters").modal("hide");
    }

    t.reload = function (e) {
        t.dTbl.ajax.reload();
    }
    t.setDefaultCompany = function () {
        let companyId = parseInt(t.config.company_defulte);
        if (!companyId || companyId <= 0) {
            t.mdl.frmEl.company.val(null).trigger('change');
            return;
        }
        $.ajax({
            url: t.config.url.get_company_by_user_access,
            type: 'GET',
            dataType: 'json',
            data: {
                search: ''
            },
            success: function (response) {
                if (response && response.results) {
                    let company = response.results.find(function(item){
                        return parseInt(item.id) === companyId;
                    });
                    if (company) {
                        let option = new Option( company.text, company.id, true, true);
                        t.mdl.frmEl.company.append(option).trigger('change');
                    }
                }
            }
        });
    };

    t.addKanban = function () {
        t.resetForm();
        if (t.config.isNormalUser === true) {
            $('#board_items_type').val("2").trigger("change");
        } else {
            // Optionally set a default for non-normal users
            t.mdl.frmEl.board_items_type.val("1").trigger("change"); // or "2"
        }
        t.mdl.frm.attr('action', t.config.url.store_kanaban);
        t.mdl.title.text(t.config.translations.add_kanaban);
        t.config.board_creator = t.config.user.id;
        t.mdl.frmEl.company.prop('disabled', false);
        t.setDefaultCompany();
        t.mdl.modal("show");
    };

    t.openFilterMdl = function (e) {
        $("#advance-filters").modal("show");
        e.preventDefault();

    }

    t.itemSetting = function () {
        var type = t.mdl.frmEl.board_items_type.val();
        t.mdl.frmEl.item.empty();
        if (type == 1) {
            t.mdl.frmEl.item.select2({ width: "100%", dropdownParent: t.mdl.frmEl.item.parent(), placeholder: config.translations.select_sprint });
            t.mdl.frmEl.item.empty().append(new Option("Select Sprint", "", false, false));
            $.each(t.statuses, function (i, status) {
                t.mdl.frmEl.item.append(new Option(status.name, status.id, false, false))
            });
            t.mdl.frm.find("#comment_checkbox").show();
            t.mdl.frm.find("#technician_checkbox").show();
            t.mdl.frm.find("#custom_fieldset").closest(".row").hide();
            t.mdl.frmEl.archive_days.closest(".row").hide();
        }
        if (type == 2) {
            t.mdl.frmEl.item.select2({
                width: "100%",
                dropdownParent: t.mdl.frmEl.item.parent(),
                tags: true,
                placeholder: config.translations.enter_sprint,
                tokenSeparators: [','],
            });
            t.mdl.frm.find("#comment_checkbox").hide();
            t.mdl.frm.find("#technician_checkbox").hide();
            t.mdl.frm.find("#custom_fieldset").closest(".row").show();
            if (t.config.user.id === t.config.board_creator) {
                t.mdl.frmEl.archive_days.closest(".row").show();
            }
        }
        if (type == '') {
            t.mdl.frm.find("#custom_fieldset").closest(".row").hide();
            t.mdl.frmEl.archive_days.closest(".row").hide();
        }
    }

    // delete kanban board
    t.deleteKanbanBoard = function (e) {
        e.preventDefault();
        var kanbanID = $(this).attr("data-id");

        t.httpPostPath = t.config.url.delete_kanban + "/" + kanbanID;
        sweetAlertConfirmation({
            message: config.translations.are_you_delete,
            onConfirm: function () {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert('center', 'error', data);
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
            }
        });
    }

    function stripHTML(input) {
        return input ? input.replace(/<\/?[^>]+(>|$)/g, "") : "";
    }
    t.kanban_board_history = function (value, type) {
        return '<div class="timeline-entry tiny-view tiny-view-on">' +
            '<div class="timeline-stat">' +
            '<div class="timeline-icon"></div>' +
            '<div class="timeline-time">' + value.updated_at_formatted + '</div>' +
            '</div>' +
            '<div class="timeline-label">' +
            ' <button class="btn btn-white btn-ex-com single-tiny-viewer"><i class="fa fa-compress"></i></button>' +
            '<p class="mar-no pad-btm ">' + type + ' By ' +
            '<a href="' + t.config.url.user_info + '/' + value.user_id + '" target="_blank" class="btn-link text-main text-bold" target="_blank">' +
            value.updated_by +
            '</a>' +
            '</p>';
    }

    // Helper: time ago text for badge (parses "24 Jun 2026 10:50 AM")
    t.getTimeAgoText = function(dateTimeStr) {
        if (!dateTimeStr) return 'Just<br>now';
        function parseCustomDate(str) {
            var parts = str.split(' ');
            if (parts.length < 5) return null;
            var day = parseInt(parts[0]);
            var monthNames = {
                'Jan':0,'Feb':1,'Mar':2,'Apr':3,'May':4,'Jun':5,
                'Jul':6,'Aug':7,'Sep':8,'Oct':9,'Nov':10,'Dec':11
            };
            var month = monthNames[parts[1]];
            if (month === undefined) return null;
            var year = parseInt(parts[2]);
            var timeParts = parts[3].split(':');
            var hour = parseInt(timeParts[0]);
            var minute = parseInt(timeParts[1]);
            var ampm = parts[4];
            if (ampm === 'PM' && hour !== 12) hour += 12;
            if (ampm === 'AM' && hour === 12) hour = 0;
            return new Date(year, month, day, hour, minute);
        }
        var date = parseCustomDate(dateTimeStr);
        if (!date || isNaN(date.getTime())) {
            date = new Date(dateTimeStr);
            if (isNaN(date.getTime())) return 'Just<br>now';
        }
        var now = new Date();
        var diffMs = now - date;
        var diffMins = Math.floor(diffMs / 60000);
        var diffHours = Math.floor(diffMins / 60);
        var diffDays = Math.floor(diffHours / 24);
        var diffWeeks = Math.floor(diffDays / 7);
        var diffMonths = Math.floor(diffDays / 30);
        var diffYears = Math.floor(diffDays / 365);
        if (diffMins < 1) return 'Just<br>now';
        if (diffMins < 60) return diffMins + ' min<br>ago';
        if (diffHours < 24) return diffHours + ' hr<br>ago';
        if (diffDays === 1) return '1 day<br>ago';
        if (diffDays < 7) return diffDays + ' days<br>ago';
        if (diffWeeks === 1) return '1 week<br>ago';
        if (diffWeeks < 4) return diffWeeks + ' weeks<br>ago';
        if (diffMonths === 1) return '1 month<br>ago';
        if (diffMonths < 12) return diffMonths + ' months<br>ago';
        if (diffYears === 1) return '1 year<br>ago';
        return diffYears + ' years<br>ago';
    };

    // Helper: get avatar HTML
    t.getAvatar = function(commenter, avatar) {
        if (commenter === "System" || commenter === "system") {
            return '<img style="width:18px;height:18px;border-radius:50%;object-fit:cover;" src="' + (t.config.url.base || '') + '/assets/mati.png" alt="System">';
        }
        if (avatar && avatar !== '' && avatar !== null) {
            return '<img style="width:18px;height:18px;border-radius:50%;object-fit:cover;" src="' + (t.config.url.base || '') + '/storage/avatar/' + avatar + '" alt="' + commenter + '">';
        } else {
            var name = commenter || 'User';
            var initials = name.split(' ').map(function(n) { return n[0]; }).join('').toUpperCase().substring(0, 2);
            return '<span class="tkt-hst-avatar-initials" style="display:inline-flex;align-items:center;justify-content:center;width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#a78bfa,#7c3aed);color:white;font-size:10px;font-weight:600;text-transform:uppercase;">' + initials + '</span>';
        }
    };

    t.kanbanHistory = function(e) {
        e.preventDefault();
        var board_id = $(this).attr('data-id');
        var history_url = t.config.url.board_history_page + "/" + board_id;

        t.result.html('');
        $("#result").addClass('timeline');

        var http = $.ajax({
            url: history_url,
            type: "POST",
            data: { _token: t.config.token },
            success: function(data) {
                if (data.status == "fail") {
                    sweetAlert('center', 'error', data);
                    return;
                }

                var historyData = data.data;
                var boardInfo = historyData[0] || {};

                var boardName = boardInfo.board_name || '';
                var boardId = boardInfo.board_id || board_id;
                var companyName = boardInfo.company_name || '';
                var ownerName = boardInfo.owner_name || '';

                $('#subject').text(boardName).attr('data-original-title', boardName);
                $('#board_id').text('#' + boardId);
                $('#board_company').text(companyName);
                $('#board_owner').text(ownerName);

                var timelineHtml = '';
                if (historyData.length > 0) {
                    $.each(historyData, function(index, value) {
                        var actionHtml = '';
                        var badgeClass = 'green';
                        var actionType = parseInt(value.action_type);
                        if ([2,3,4,5,6,7,8,9,10,11].indexOf(actionType) !== -1) {
                            badgeClass = 'purple';
                        }

                        // ---- Build action description (same switch as before) ----
                        switch (actionType) {
                            case 1:
                                actionHtml = 'Kanban Board Created';
                                break;
                            case 2:
                                actionHtml = 'Name Changed From <b>' + t.escapeHtml(value.old_name || '') + '</b> To <b>' + t.escapeHtml(value.new_name || '') + '</b>';
                                break;
                            case 3:
                                var oldDesc = stripHTML(value.old_description);
                                var newDesc = stripHTML(value.new_description);
                                if (!oldDesc && newDesc) {
                                    actionHtml = 'Description Added <b>' + t.escapeHtml(newDesc) + '</b>';
                                } else if (oldDesc && !newDesc) {
                                    actionHtml = 'Description Removed (was: <b>' + t.escapeHtml(oldDesc) + '</b>)';
                                } else if (oldDesc && newDesc) {
                                    actionHtml = 'Description Changed From <b>' + t.escapeHtml(oldDesc) + '</b> To <b>' + t.escapeHtml(newDesc) + '</b>';
                                } else {
                                    actionHtml = 'Description Updated';
                                }
                                break;
                            case 4:
                                if (!value.old_dept_name && value.new_dept_name) {
                                    actionHtml = 'Department <b>' + t.escapeHtml(value.new_dept_name) + '</b> was Added';
                                } else if (value.old_dept_name && !value.new_dept_name) {
                                    actionHtml = 'Department <b>' + t.escapeHtml(value.old_dept_name) + '</b> was Removed';
                                } else if (value.old_dept_name && value.new_dept_name && value.old_dept_name !== value.new_dept_name) {
                                    actionHtml = 'Department Changed From <b>' + t.escapeHtml(value.old_dept_name) + '</b> To <b>' + t.escapeHtml(value.new_dept_name) + '</b>';
                                } else {
                                    actionHtml = 'Department Updated';
                                }
                                break;
                            case 5:
                                if (!value.old_problem_category_name && value.new_problem_category_name) {
                                    actionHtml = 'Problem Category <b>' + t.escapeHtml(value.new_problem_category_name) + '</b> was Added';
                                } else if (value.old_problem_category_name && !value.new_problem_category_name) {
                                    actionHtml = 'Problem Category <b>' + t.escapeHtml(value.old_problem_category_name) + '</b> was Removed';
                                } else if (value.old_problem_category_name && value.new_problem_category_name && value.old_problem_category_name !== value.new_problem_category_name) {
                                    actionHtml = 'Problem Category Changed From <b>' + t.escapeHtml(value.old_problem_category_name) + '</b> To <b>' + t.escapeHtml(value.new_problem_category_name) + '</b>';
                                } else {
                                    actionHtml = 'Problem Category Updated';
                                }
                                break;
                            case 6:
                                if (!value.old_sub_category_name && value.new_sub_category_name) {
                                    actionHtml = 'Sub Category <b>' + t.escapeHtml(value.new_sub_category_name) + '</b> was Added';
                                } else if (value.old_sub_category_name && !value.new_sub_category_name) {
                                    actionHtml = 'Sub Category <b>' + t.escapeHtml(value.old_sub_category_name) + '</b> was Removed';
                                } else if (value.old_sub_category_name && value.new_sub_category_name && value.old_sub_category_name !== value.new_sub_category_name) {
                                    actionHtml = 'Sub Category Changed From <b>' + t.escapeHtml(value.old_sub_category_name) + '</b> To <b>' + t.escapeHtml(value.new_sub_category_name) + '</b>';
                                } else {
                                    actionHtml = 'Sub Category Updated';
                                }
                                break;
                            case 7:
                                var oldVal = value.old_auto_comment_on_card_change === 1 ? 'Enabled' : 'Disabled';
                                var newVal = value.new_auto_comment_on_card_change === 1 ? 'Enabled' : 'Disabled';
                                actionHtml = 'Auto comment on card change changed from <b>' + oldVal + '</b> to <b>' + newVal + '</b>';
                                break;
                            case 8:
                                var oldAccess = value.old_access_to_all_dept_tech === 1 ? 'Enabled' : 'Disabled';
                                var newAccess = value.new_access_to_all_dept_tech === 1 ? 'Enabled' : 'Disabled';
                                actionHtml = 'Access to all department technician changed from <b>' + oldAccess + '</b> to <b>' + newAccess + '</b>';
                                break;
                            case 9:
                                var oldType = value.old_board_item_type === 1 ? 'Ticket' : 'Custom';
                                var newType = value.new_board_item_type === 1 ? 'Ticket' : 'Custom';
                                actionHtml = 'Board item type changed from <b>' + oldType + '</b> to <b>' + newType + '</b>';
                                break;
                            case 10:
                                if (!value.old_custom_fieldset_name && value.new_custom_fieldset_name) {
                                    actionHtml = 'Custom Fieldset <b>' + t.escapeHtml(value.new_custom_fieldset_name) + '</b> was Added';
                                } else if (value.old_custom_fieldset_name && !value.new_custom_fieldset_name) {
                                    actionHtml = 'Custom Fieldset <b>' + t.escapeHtml(value.old_custom_fieldset_name) + '</b> was Removed';
                                } else if (value.old_custom_fieldset_name && value.new_custom_fieldset_name && value.old_custom_fieldset_name !== value.new_custom_fieldset_name) {
                                    actionHtml = 'Custom Fieldset Changed From <b>' + t.escapeHtml(value.old_custom_fieldset_name) + '</b> To <b>' + t.escapeHtml(value.new_custom_fieldset_name) + '</b>';
                                } else {
                                    actionHtml = 'Custom Fieldset Updated';
                                }
                                break;
                            case 11:
                                if (!value.old_item_id && value.new_item_id) {
                                    actionHtml = 'Board Item(s) <b>' + t.escapeHtml(value.new_item_name) + '</b> was Added';
                                } else if (value.old_item_id && !value.new_item_id) {
                                    actionHtml = 'Board Item(s) <b>' + t.escapeHtml(value.old_item_name) + '</b> was Removed';
                                } else {
                                    actionHtml = 'Board Item(s) Updated';
                                }
                                break;
                            case 12:
                                actionHtml = 'Kanban Board Deleted';
                                break;
                            default:
                                actionHtml = 'Update performed on board';
                                break;
                        }

                        // ---- Time and avatar ----
                        var timeStr = value.updated_at_formatted || value.created_at_formatted || '';
                        var timeAgoText = t.getTimeAgoText(timeStr);
                        var commenterName = value.updated_by || 'System';
                        var avatarHtml = t.getAvatar(commenterName, value.commenter_avatar || '');

                        // ---- Expand/collapse logic ----
                        var plainText = stripHTML(actionHtml);
                        var isLong = plainText.length > 100;
                        var descId = 'desc-' + index + '-' + Math.random().toString(36).substr(2, 6);

                        // We'll build the description container separately
                        var descHtml = '<div class="tkt-hst-desc-content" id="' + descId + '">' + actionHtml + '</div>';

                        // Toggle button (only if long)
                        var toggleHtml = '';
                        if (isLong) {
                            toggleHtml = '<button type="button" class="tkt-hst-toggle-desc btn btn-link btn-sm p-0 ms-2" data-target="' + descId + '">' +
                                '<i class="bi bi-arrows-angle-contract"></i> <span class="toggle-label"></span>' +
                            '</button>';
                        }

                        // ---- Build timeline item ----
                        timelineHtml += '<div class="tkt-hst-timeline-item d-flex gap-3 align-items-start">' +
                            '<div class="d-flex flex-column align-items-center flex-shrink-0 align-self-stretch">' +
                                '<div class="rounded-circle d-flex flex-column align-items-center justify-content-center text-center text-white flex-shrink-0 tkt-hst-badge ' + badgeClass + '">' + timeAgoText + '</div>' +
                                '<div class="tkt-hst-connector-line"></div>' +
                            '</div>' +
                            '<div class="flex-fill mt-1 tkt-hst-card mb-1">' +
                                '<div class="d-flex align-items-center gap-1 mb-1 meta-time">' +
                                    '<svg width="12" height="12" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                                        '<path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="#7F7F7F"/>' +
                                    '</svg>' +
                                    '<span class="b7-text" style="color:#7F7F7F">' + timeStr + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-center gap-2 mb-1">' +
                                    '<div class="rounded-circle overflow-hidden flex-shrink-0 tkt-hst-avatar d-flex align-items-center justify-content-center">' + avatarHtml + '</div>' +
                                    '<span class="b6-text fw-bold">' + commenterName + '</span>' +
                                    '<span class="b6-text fw-bold">•</span>' +
                                    '<span class="b6-text fw-light" style="color:#7F7F7F">Changes done by ' + commenterName + '</span>' +
                                '</div>' +
                                '<div class="d-flex align-items-start">' +
                                    descHtml +
                                    toggleHtml +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    });
                } else {
                    timelineHtml = '<div class="text-center p-4"><p>' + (config.translations.Ticket_History_Not_Available || 'No history available') + '</p></div>';
                }

                $('#result').append(timelineHtml);

                // ---- Attach toggle click handler ----
                $('#result').on('click', '.tkt-hst-toggle-desc', function(e) {
                    e.preventDefault();
                    var targetId = $(this).data('target');
                    var $desc = $('#' + targetId);
                    var $icon = $(this).find('i');
                    var $label = $(this).find('.toggle-label');

                    $desc.toggleClass('expanded');
                    if ($desc.hasClass('expanded')) {
                        $icon.removeClass('bi bi-arrows-angle-contract"').addClass('bi bi-arrows-angle-expand');
                    } else {
                        $icon.removeClass('bi bi-arrows-angle-expand').addClass('bi bi-arrows-angle-contract"');
                    }
                });

                $('#mdl-history').modal("show");
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    sweetAlert('center', 'error', {msg: 'Session expired. Please refresh the page.'});
                } else {
                    sweetAlert('center', 'error', {msg: config.translations.something_went_wrong || 'Something went wrong'});
                }
            }
        });
    };

    // summernotes 
    t.mdl.frmEl.description.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.enter_description,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true
    });
    // filter 
    t.cache_filter_values = function () {
        var v = $.trim($('.searchbox').val());
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        if (t.filters.department.val() && t.filters.department.val() != '') {
            t.config.other_filters.department = t.filters.department.val();
        }
        if (t.filters.prob_category.val() && t.filters.prob_category.val() != '') {
            t.config.other_filters.prob_category = t.filters.prob_category.val();
        }
        if (t.filters.sub_category.val() && t.filters.sub_category.val() != '') {
            t.config.other_filters.sub_category = t.filters.sub_category.val();
        }
        if (t.filters.based_on.val() && t.filters.based_on.val() !== 'null' && t.filters.based_on.val() !== '') {
            t.config.other_filters.based_on = t.filters.based_on.val();
        }
        if (t.filters.based_on.val() && t.filters.based_on.val() !== 'null' && t.filters.based_on.val() !== '') {
            t.config.other_filters.date_range = $('#daterange').val();
        }
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.based_on, false);
    };

    // reset filter
    t.resetFilter = function () {
        t.filters.department.val('').trigger("change");
        t.filters.prob_category.val('').trigger("change");
        t.filters.sub_category.val('').trigger("change");
        t.filters.based_on.val("null").trigger("change");
        resetDateRangeFilter();
        t.config.other_filters = {};
        resetFilterCount();
    }


    t.toggleSingleTinyViewer = function (e) {
        e.preventDefault();
        var el = $(this).closest('.tiny-view').get(0);
        var target_val = $(el).hasClass('tiny-view-on');
        t.setSingleTinyViewer(el, target_val);
    };
    t.setSingleTinyViewer = function (el, target_val) {
        if (target_val == true) {
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-contract"></i>').attr('title', 'Compress');
        }
        else {
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="bi bi-arrows-angle-expand"></i>').attr('title', 'Expand');
        }
    };

    t.mdlKanbanHistory.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    t.content.on("click", ".add-kanban-board", $.proxy(t.addKanban));
    t.content.on("click", ".btn-open-filter", $.proxy(t.openFilterMdl));
    t.table.on("click", ".edit-kanban", $.proxy(t.editKanbanBoard));
    t.table.on("click", ".delete-kanban", $.proxy(t.deleteKanbanBoard));
    t.table.on("click", ".kanban-history", $.proxy(t.kanbanHistory));
    t.content.on("click", "#btnSubmit", $.proxy(t.saveData));
    t.content.on("click", ".btn-searchbox", $.proxy(t.searchData));
    t.content.on("click", ".btn-reload", $.proxy(t.reload));
    t.mdl.frmEl.department_id.on('change', $.proxy(t.fun.reload_problem_category));
    t.mdl.frmEl.problem_category_id.on("change", function () {
        let selectedPcIds = $(this).val() || [];
        t.mdl.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
        if (!selectedPcIds.length) {
            t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
            return;
        }
        let hasSubCategory = selectedPcIds.some(function (id) {
            let item = problem_categories.find(pc => pc.id == id);
            return item && Array.isArray(item.sub) && item.sub.length > 0;
        });
        if (hasSubCategory) {
            t.mdl.frmEl.subCategoryIdCvr.removeClass('hide');
            t.mdl.frmEl.sub_category_id.trigger('change');
        } else {
            t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
            t.mdl.frmEl.sub_category_id.empty().append('Select Sub Category', "", false, false);
        }
    });
    t.loadStatuses();
    t.mdl.frmEl.board_items_type.on("change", $.proxy(t.itemSetting));
    t.fun.reload_filter_department();
    t.filters.department.on("change", $.proxy(t.fun.reload_filter_problem_category));
    t.filters.prob_category.on("change", $.proxy(t.fun.reload_filter_sub_category));
    t.filters.wrapper.on("click", "button", t.searchData);
    t.filters.btnfilterclr.on('click', $.proxy(t.resetFilter));
}
