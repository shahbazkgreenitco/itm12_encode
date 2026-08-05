var MyApp = function(config) {
    var t = this;
    t.showDeletedUsers = false;
    t.config = config;
    t.content = $("section.content");
    t.table = t.content.find("#myTable");
    t.mdl = t.content.find('#add-category-modal');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#categoryAddModal');

    // add/edit form elements
    t.frmEl = {};
    t.frmEl.company = t.frm.find('#company');
    t.frmEl.category_name = t.frm.find('#category_name');
    t.frmEl.department = t.frm.find("#department");
    t.frmEl.parent_category = t.frm.find("#parent_category");
    t.frmEl.content = t.frm.find("#content");
    var company_selected = t.config.selected_company.dashboard_company_id ? t.config.selected_company.dashboard_company_id : null
    // filter elements
    t.filters = {
        wrapper: t.content.find("#categoryFilterModal"),
    };
    t.filters.filter_by_company = t.filters.wrapper.find("#filter_by_company"),
    t.filters.filter_by_department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.filter_by_category = t.filters.wrapper.find("#filter_by_category"),
    t.filters.filter_by_parent_category = t.filters.wrapper.find("#filter_by_parent_category"),
    t.btn = {};
    t.btn.submit = t.frm.find('#btn-submit-category');
    t.btn.clear = t.frm.find('#btnClear');

    // t.getModalErrorWrap = function (element) {
    //     var row = element.closest(".amg-form-field-row");
    //     var wrap;

    //     if (!row.length) {
    //         return $();
    //     }

    //     wrap = row.children(".amg-form-error-wrap");
    //     if (!wrap.length) {
    //         wrap = $('<div class="amg-form-error-wrap"></div>');
    //         row.append(wrap);
    //     }

    //     return wrap;
    // };

    // t.updateValidationState = function (element, hasError) {
    //     var group = element.closest(".input-group");
    //     var isSelect2 = element.hasClass("select2-hidden-accessible");
    //     var noteEditor = element.next(".note-editor");

    //     if (group.length) {
    //         group.toggleClass("amg-form-invalid", !!hasError);
    //     }

    //     if (isSelect2) {
    //         element.next(".select2-container")
    //             .find(".select2-selection")
    //             .toggleClass("amg-form-select-error", !!hasError);
    //     }

    //     if (noteEditor.length) {
    //         noteEditor.toggleClass("amg-form-select-error", !!hasError);
    //     }
    // };

    t.updateValidationState = function (element, hasError) {

        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");
        // input group border + icon
        if (group.length && !element.is("textarea")) {//on edit proper edit message on content summernote
            group.toggleClass("amg-form-invalid", !!hasError);

            // icon bg + border
            group.find(".input-group-text")
                .toggleClass("amg-form-icon-error", !!hasError);
        }

        // select2 error
        if (isSelect2) {

            var select2Container = element.next(".select2-container");

            select2Container
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);

            // select2 arrow + icon wrapper
            select2Container
                .closest(".input-group")
                .find(".input-group-text")
                .toggleClass("amg-form-icon-error", !!hasError);
        }
    };
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

    t.isResetting = false;//flag for not triggering element change when add/edit modal opens.
    var select2Opts = { width: "100%" };

    t.dTbl = t.table.DataTable({
        language: datatable_footer_translations(t.config.datatable_translations),
        processing: true,
        serverSide: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        ajax: {
            url: t.config.url.getList,
            type: "POST",

            data: function (d) {
                d._token = t.config.token;
                d.filters = t.config.other_filters;
            }
        },
          aoColumnDefs: [
            {
                targets: 5,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let buttons = '';

                    if (jQuery.inArray("KnowledgeDocumentCategoryEdit", t.config.permissions) !== -1) {
                        buttons += `                           
                            <button class="amg-action-btn open-edit-modal me-1" data-bs-toggle="tooltip" data-placement="bottom" data-bs-title="${t.config.translations.edit}" data-id="${row.id}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"/></svg>
                            </button>
                        `;
                    }                    
                    if (jQuery.inArray("KnowledgeDocumentCategoryDelete", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="amg-action-btn open-delete open-delete me-1" data-bs-toggle="tooltip" data-placement="bottom" data-bs-title="${t.config.translations.delete}" data-id="${row.id}">
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                            </button>
                        `;
                    }

                    return buttons;
                }
            }
        ],
        order: [
            [4, 'desc']
        ],
        columns: [
        {
            data: 'category_name',
            render: function (data) {
                return data
            }
        },

        {
            data: 'parent_category',
            render: function (data) {
                return data
            }
        },
        {
            data: 'company_name',
            render: function (data) {
                return data
            }
        },
        {
            data: 'department',
            render: function (data) {
                return data
            }
        },

        {
            data: 'formatted_updated_at',
            render: function (data) {
                return data
            }
        },
        { 
            data: 'a', 
            orderable: false, 
            searchable: false
        }
        ],
           fnInitComplete: function () {
            var api = this.api();
            $('#myTable_length').hide();
            $('#kd-category-list-search').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    let value = $(this).val().trim();
                    api.search(value).draw();
                }
            });
            $('#kd-category-page-length').val(api.page.len());
            $('#kd-category-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });
        },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        },
    });

    t.cache_filter_values = function() {
        var v = $('#kd-category-list-search').val();
        t.config.search = v;
        t.config.other_filters = {};
   
        if (t.filters.filter_by_company.val() && t.filters.filter_by_company.val() != 'null') {
            t.config.other_filters.company = t.filters.filter_by_company.val();
        }
        if (t.filters.filter_by_department.val() && t.filters.filter_by_department.val() != 'null') {
            t.config.other_filters.department = t.filters.filter_by_department.val();
        }
        if (t.filters.filter_by_category.val() && t.filters.filter_by_category.val() != 'null') {
            t.config.other_filters.category = t.filters.filter_by_category.val();
        }
        if (t.filters.filter_by_parent_category.val() && t.filters.filter_by_parent_category.val() != 'null') {
            t.config.other_filters.parent_category = t.filters.filter_by_parent_category.val();
        }

        var jobj = {"search":t.config.search, "other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.frmEl.content.summernote({
        inheritPlaceholder: true,
        placeholder: "Write content here",
        toolbar: [
            ["color",["color"]],
        ["style", ["bold", "italic", "underline", "clear"]],
        ["para", ["ul", "ol"]],
        ],
        minHeight: 100,
        focus: true,
    });
    
    t.resetFrm = function() {
        t.isResetting = true;
        t.frmEl.company.val(null).trigger('change');
        t.frmEl.category_name.val('');
        t.frmEl.department.val(null).trigger('change');
        t.frmEl.parent_category.val(null).trigger('change');
        t.frmEl.content.summernote('code', '');
        if (t.frmValidator) {
            t.frmValidator.resetForm();
        }
        setTimeout(function() {
            t.isResetting = false;
        }, 100);
    };

    t.loadForm = function (obj,id) {
        t.resetFrm();
        t.config.isEdit = true;
        t.config.category_id = id;

        // category name
        t.frmEl.category_name.val(obj.data.category_name);

        //company dd
        if (obj.dropdown.company) {
            let companyOption = new Option(obj.dropdown.company.text, obj.dropdown.company.id,true,true);
            t.frmEl.company.append(companyOption).trigger('change')
        }

        if (obj.dropdown.department) {
            let departmentOption = new Option(obj.dropdown.department.text,obj.dropdown.department.id,true, true);
            t.frmEl.department.append(departmentOption).trigger('change');
        }

        t.frmEl.parent_category.empty();
        t.frmEl.parent_category.append(new Option('No Parent Category', '', false, false));

        if (obj.dropdown.parents && obj.dropdown.parents.length > 0 ) {
            $.each(obj.dropdown.parents, function (i, v) {
                let selected = obj.data.parent_category_id == v.id;
                let option = new Option(v.text,v.id,selected,selected);
                t.frmEl.parent_category.append(option);
            });
        }

        t.frmEl.parent_category.trigger('change');
        t.frmEl.content.summernote('code',obj.data.content ?? '');
        t.frm.append(` <input type="hidden" name="id" id="id"> `);
        t.frm.find('#id').val(id);
    };

    t.frmEl.parent_category.select2($.extend({}, select2Opts, {
        placeholder: "Select Parent Category",
        dropdownParent: t.mdl,
        allowClear: true
    }));

    t.frmEl.company.select2($.extend({}, select2Opts, {
        dropdownParent:t.frmEl.company.parent(),
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
    }));

    t.frmEl.department.select2($.extend({}, select2Opts, {
        dropdownParent:t.frmEl.department.parent(),
        ajax: {
            url: t.config.url.department,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.frmEl.company.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            }
        },
        placeholder: "Select Departments"
    }));

    // filter select2's
    t.filters.filter_by_company.select2($.extend({}, select2Opts, {
        dropdownParent:t.filters.filter_by_company.parent(),
        allowClear:true,
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
    }));

    t.filters.filter_by_department.select2($.extend({},select2Opts,{
        dropdownParent:t.filters.filter_by_department.parent(),
        allowClear:true,
        ajax: {
            url: config.url.department,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.filters.filter_by_company.val() || t.config.selected_company.dashboard_company_id
                };
            },
            delay: 300
        },
        placeholder: "Select Department"
    }));

    t.filters.filter_by_category.select2($.extend({},select2Opts,{
        dropdownParent:t.filters.filter_by_category.parent(),
        allowClear:true,
        ajax: {
            url: config.url.fetch_subcategory_by_ajax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    sub_category: t.filters.filter_by_parent_category.val()
                };
            },
            delay: 300
        },
        placeholder: "Select Sub Category"
    }));

    t.filters.filter_by_parent_category.select2($.extend({},select2Opts,{
        dropdownParent:t.filters.filter_by_parent_category.parent(),
        allowClear:true,
        ajax: {
            url: config.url.fetch_category_by_ajax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    department: t.filters.filter_by_department.val()
                };
            },
            delay: 300
        },
        placeholder: "Select Parent Category"
    }));

    // on change reset dependent dropdown to null
    function resetSelect2(select) {
        select.val(null).trigger('change');
    }

    t.filters.filter_by_company.on('change', function () {
        resetSelect2(t.filters.filter_by_department);
        resetSelect2(t.filters.filter_by_parent_category);
        resetSelect2(t.filters.filter_by_category);
    });

    t.filters.filter_by_department.on('change', function () {
        resetSelect2(t.filters.filter_by_parent_category);
        resetSelect2(t.filters.filter_by_category);
    });

    t.filters.filter_by_parent_category.on('change', function () {
        resetSelect2(t.filters.filter_by_category);
    });

    // end

    t.reload = function() {
        t.dTbl.ajax.reload();
    };
    t.createCategory = function (e) {
        t.config.isEdit = false;
        e.preventDefault();
        t.resetFrm();
        let title = "";
        title = t.config.translations.add_category_title;
        t.btn.submit.text(t.config.translations.add_category);
        t.mdltitle.text(title);
        if(t.config.dashboard_company_id && t.config.dashboard_company_id.length > 0){
            let option = new Option(t.config.dashboard_company_id[0]['text'],t.config.dashboard_company_id[0]['id'], true,true);
            t.frmEl.company.append(option).trigger('change');
        }

        t.mdl.modal("show");
    };


    t.config.isEdit = false;
    t.config.category_id = null;
    t.editCategory = function (e) {
        e.preventDefault();
        var categoryId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.editCategory + "/" + categoryId;
        var http = $.get(t.config.url.editCategory + "/" + categoryId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.config.isEdit = true;
                    let title = t.config.translations.edit_category_title;
                    t.mdltitle.text(title)
                    t.btn.submit.text(t.config.translations.update_category);
                    t.mdl.modal("show");
                    t.loadForm(data.type,categoryId);
                }
                else {
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
    };
    
    t.frmEl.company.on('change', function () {
        t.isResetting = true;
        t.frmEl.department.val(null);
        t.isResetting = false;
    });

    // form validation
    t.frmValidator = t.frm.validate({
        ignore: [],
        debug: false,
        rules: {
            company: {
                required: true,
            },
            category_name: {
                required: true,
                clean_text_only: true,
            },
            department: {
                required: true
            },
            parent_category: {
                required: false
            },
            content: {
                required: true,
                summernote: true
            },
        },
        errorPlacement: function(error, element) {
			var errorWrap = t.getModalErrorWrap(element);

			if (errorWrap.length) {
				error.appendTo(errorWrap);
			} else if (element.closest(".input-group").length) {
				error.insertAfter(element.closest(".input-group"));
			} else {
                error.appendTo(element.closest("div"));
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

    t.frm.find("input[name='category_name'], select[name='company'], select[name='department']").on("input change blur", function () {
        if (t.isResetting) return;
        var element = $(this);
        element.closest(".amg-form-field-row").find(".amg-form-error-wrap").empty();
        element.valid();
    });

    t.frmEl.content.off("summernote.change.categoryForm").on("summernote.change.categoryForm", function () {
        if (t.isResetting) return;
        var element = $(this);
        element.closest(".amg-form-field-row").find(".amg-form-error-wrap").empty();
        element.valid();
    });

    // start of 'on dept change fill parent category code'
    t.onDepartmentChange = function(e) {
        e.preventDefault();
        if (t.isResetting) return;
        t.frmEl.parent_category.empty();
        t.frmEl.parent_category.append(new Option('No Parent Category', 0));
        t.frmEl.parent_category.trigger("change");
    
        // t.frmParentCategoryUpdate.el.parent_category_id.empty();
        // t.frmParentCategoryUpdate.el.parent_category_id.append(new Option(config.translations.No_ParentCategory, 0));
        // t.frmParentCategoryUpdate.el.parent_category_id.trigger("change");
    
        var d = t.frmEl.department.val();
        // var c = t.frmParentCategoryUpdate.el.departmentId.val();

        if (d > 0) {
            fetchAndAppendCategories(t.config.url.problem_categories_by_dept + '/' + d + '?parent_only=true',t.frmEl.parent_category);
        }

        // if (c > 0) {
        //     fetchAndAppendCategories(t.config.url.problem_categories_by_dept + '/' + c + '?parent_only=true',t.frmParentCategoryUpdate.el.parent_category_id);
        // }
    };

    function fetchAndAppendCategories(url, selectElement) {
        $.get(url, function(result) {
            if (typeof result === "object" && result.status === "success" && result.data.length > 0) {
                $.each(result.data, function(i, v) {
                    selectElement.append(new Option(v.category_name, v.id));
                });
                selectElement.trigger("change");
            }
        });
    }

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        let url = ""

        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);

        if(t.config.isEdit){
            url = t.config.url.updateCategory;
            frmData.append('id', t.config.category_id);
        }else{
            url = t.config.url.store;
            t.config.category_id = null;
        }

        var http = $.ajax({
            url: url,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function () {
                t.btn.submit.prop('disabled', true);
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });

        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });

        http.always(function () {
            t.httpCall = true;
            t.btn.submit.prop('disabled', false);
        });
    };

    t.openFilter = function (e) {
        e.preventDefault();
        var modal = t.filters.wrapper;
        modal.modal('show');
    }
    
    t.deleteCategory = function(e) {
		e.preventDefault();
		var Id = $(this).attr("data-id");
		t.httpPostPath = t.config.url.delete_category + "/" + Id;
        sweetAlertConfirmation({
			message: t.config.translations.are_you_want_Delete_cat,
			onConfirm: function() {
                var http = $.get(t.httpPostPath);
                http.done(function(data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                        sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        } else {
                        sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function() {
                var data = {
                    'msg': 'something went wrong'
                }
                sweetAlert('center','error',data);
                });
                http.always(function() {
                    t.httpCall = true;
                });
            }
		});
	};

    // on filter click
    t.content.on('click', '.btn-filter', function () {
        t.filters.wrapper.modal('hide');
        t.cache_filter_values();
        updateFilterCount(t.config.other_filters);
        t.reload();
    });

    function updateFilterCount(filters) {
        let count = 0;
        if (filters.company && filters.company.length > 0) {
            count++;
        }
        if (filters.category && filters.category.length > 0) {
            count++;
        }
        if (filters.department && filters.department.length > 0) {
            count++;
        }
        if (filters.parent_category && filters.parent_category.length > 0) {
            count++;
        }
        let badge = $('.filter-count-badge');
        if (count > 0) {
            badge.removeClass('d-none').text(count);
        } else {
            badge.addClass('d-none').text(0);
        }
    }

    t.btnClrFilter = function () {
        t.filters.filter_by_company.val(null).trigger("change");
        t.filters.filter_by_department.val(null).trigger("change");
        t.filters.filter_by_category.val(null).trigger("change");
        t.filters.filter_by_parent_category.val(null).trigger("change");
        t.config.other_filters = {};
        $('.filter-count-badge').addClass('d-none').text(0);
        t.filters.wrapper.modal('hide');
        t.reload();
    };

    t.export = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    };
    t.frmEl.department.on("change", $.proxy(t.onDepartmentChange, t));

    t.content.on('click', '.open-add-modal', $.proxy(t.createCategory));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
    t.content.on('click','#btn-submit-category',$.proxy(t.handlesubmit));
    t.content.on('click','.open-edit-modal',$.proxy(t.editCategory));
    t.content.on('click','.open-delete',$.proxy(t.deleteCategory));
    $('#btn-kd-category-export').on('click',$.proxy(t.export, t));
    t.content.on('click', '.btn-open-filter', $.proxy(t.openFilter));
    t.content.on('click', '.btn-clear-filter', $.proxy(t.btnClrFilter));

    $('#categoryFilterModal .modal-close').on('click', function () {
        let filterCount = $('.filter-count-badge').text().trim();
        if (filterCount == '0') {
            t.filters.filter_by_company.val(null).trigger("change");
            t.filters.filter_by_department.val(null).trigger("change");
            t.filters.filter_by_category.val(null).trigger("change");
            t.filters.filter_by_parent_category.val(null).trigger("change");
        }
    });
}