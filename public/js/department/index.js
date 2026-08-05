var leaseAdd = function (config) {
    var t = this;
    t.parent = parent;
    t.config = parent.config;
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.content = $('section.content');
    t.mdl = t.content.find('#departmentmodal');
    t.frm = t.mdl.find('#DepartmentForm');
    t.frmEl = {};
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.department_tag = t.frm.find('#department_tag');
    t.frmEl.company_id = t.frm.find('#company_name');
    t.frmEl.department_head_id = t.frm.find('#department_head_id');
    t.frmEl.attender_id = t.frm.find('#attender_id');
    t.frmEl.description = t.frm.find('#description');
    t.frmEl.tkt_auto_creation_id = t.frm.find('#tkt_auto_creation_id');
    t.frmEl.department_admin = t.frm.find('#department_admin');
    t.frmEl.asset_department = t.frm.find('#asset_department');
    t.frmEl.asset_department_admin = t.frm.find('#asset_department_admin');
    t.frmEl.department_custom_fieldset = t.frm.find('#department_custom_fieldset');
    t.frmEl.pc_custom_fieldset = t.frm.find('#pc_custom_fieldset');
    t.frmEl.sc_custom_fieldset = t.frm.find('#sc_custom_fieldset');
    t.ticketFieldset = t.frm.find('#ticketFieldset');
    t.assetFieldset = t.frm.find('#assetFieldset');
    t.frmEl.access_role_id = t.frm.find('#access_role_id');
    t.frmEl.img = t.frm.find('#img');
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');
    t.btn.clr = t.frm.find("#btnClr");
    t.mdltitle = t.mdl.find('.modal-title');
    t.table = $('#mytable');
    t.cache_filter_values = function() {
        var v = $.trim($('.searchbox').val());
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.tblHelpers = {
        actions: function() {
            return function(d) {
                var buttons = [];

                // Edit button (if permission granted)
                if (jQuery.inArray("DepartmentEdit", config.permissions) !== -1) {
                    buttons.push(`
                        <button type="button"
                                class="user-list-action-btn me-1 open-edit-modal"
                                data-id="${d.a.id}"
                                data-bs-toggle="tooltip" 
                                data-bs-original-title="${config.translations.edit}">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                            </svg>
                        </button>
                    `);
                }

                // Delete button (if permission granted)
                if (jQuery.inArray("DepartmentDelete", config.permissions) !== -1) {
                    buttons.push(`
                        <button class="user-list-action-btn open-delete me-1" data-bs-toggle="tooltip" data-bs-original-title="${config.translations.delete}" data-id="${d.a.id}" >
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                        </button>
                    `);
                }

                if (buttons.length === 0) {
                    return '';
                }

                // Return buttons side by side (no dropdown wrapper)
                return `<div class="action-buttons">${buttons.join('')}</div>`;
            };
        },
        alignRight: function() {
            return function(d) {
                if (d == "" || d == null) return null;
                return '<div class="text-right">' + d + '</div>';
            }
        }
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        searching: true,
        dom: 'rtip',
        scrollX: true,
        scrollCollapse: true,
        aoColumnDefs: [
            {
                targets: 8,
                bSortable: false,
                className: "amg-table-col-144",
                render: t.tblHelpers.actions()
            }
        ],
        order: [
            [7, 'desc']
        ],
        processing: true,
        serverSide: true,
        colResize: {
            resizeTable: true
        },
        responsive: true,
        stateSave: true,
        stateSaveParams: function (settings, data) {
            data.search.search = '';
        },
        language: {
            paginate: {
                previous: t.config.translations.previous || "Previous",
                next: t.config.translations.next || "Next"
            },
            info: t.config.translations.showing_entries || "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: t.config.translations.no_entries || "Showing 0 to 0 of 0 entries",
            infoFiltered: t.config.translations.filtered_from || "(filtered from _MAX_ total entries)",
            zeroRecords: t.config.translations.no_matching_records || "No matching records found",
            emptyTable: t.config.translations.no_data || "No data available in table",
            search: t.config.translations.search || "Search:",
            lengthMenu: t.config.translations.length_menu || "Show _MENU_ entries"
        },
        drawCallback: function () {
            t.table.find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        ajax: {
            url: config.url.list,
            type: "get",
            data: function (d) {
                d._token = config.token;
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                    d.company_id = companyId;
                }
            }
        },
        columns: [
            { data: 'a.id' },
            { data: 'a.name', className: "amg-table-col-188"  },
            { data: 'a.department_tag', className: "amg-table-col-188"  },
            { data: 'a.company_name', className: "amg-table-col-188"  },
            {
                data: 'a.attender_name',
                className: "amg-table-col-188",
                render: function(data, type, row){

                    if(type !== 'display'){
                        return data || '-';
                    }

                    let name = data || '-';
                    let imageUrl = row.a.attender_profile_img || row.a.attender_gravatar || '';

                    function getUserInitials(name) {
                        let words = String(name || '').trim().split(/\s+/).filter(Boolean);
                        if (!words.length) return "NA";
                        return words[0].charAt(0).toUpperCase();
                    }

                    function getAvatarHtml(name, imageUrl) {

                        if (imageUrl) {
                            return `
                                <img 
                                    src="${imageUrl}" 
                                    alt="${name}"
                                    class="user-list-avatar user-list-avatar--sm"
                                >
                            `;
                        }

                        return `
                            <span class="user-list-avatar user-list-avatar--sm user-list-avatar-fallback">
                                ${getUserInitials(name)}
                            </span>
                        `;
                    }

                    return `
                        <div class="d-flex align-items-center gap-3 w-100 user-list-person--compact">

                            ${getAvatarHtml(name, imageUrl)}

                            <div class="d-flex flex-column min-w-0">
                                <span title="${name}">
                                    ${name}
                                </span>
                            </div>

                        </div>
                    `;
                }
            },
            {
                data: 'a.department_head_name',
                className: "amg-table-col-188",
                render: function(data, type, row){

                    if(type !== 'display'){
                        return data || '-';
                    }

                    let name = data || '-';

                    let imageUrl =
                        row.a.department_head_profile_img ||
                        row.a.department_head_gravatar ||
                        '';

                    function getUserInitials(name) {
                        let words = String(name || '').trim().split(/\s+/).filter(Boolean);

                        if (!words.length) return "NA";

                        return words[0].charAt(0).toUpperCase();
                    }

                    function getAvatarHtml(name, imageUrl) {

                        if (imageUrl) {
                            return `
                                <img 
                                    src="${imageUrl}" 
                                    alt="${name}"
                                    class="user-list-avatar user-list-avatar--sm"
                                >
                            `;
                        }

                        return `
                            <span class="user-list-avatar user-list-avatar--sm user-list-avatar-fallback">
                                ${getUserInitials(name)}
                            </span>
                        `;
                    }

                    return `
                        <div class="d-flex align-items-center gap-3 w-100 user-list-person--compact">

                            ${getAvatarHtml(name, imageUrl)}

                            <div class="d-flex flex-column min-w-0">
                                <span title="${name}">
                                    ${name}
                                </span>
                            </div>

                        </div>
                    `;
                }
            },
            { data: 'a.customefieldset', className: "amg-table-col-188"  },
            { data: 'a.updated_at' , className: "amg-table-col-188" },
            { data: null }
        ],
        fixedColumns: {
            leftColumns: 1,   
            rightColumns: 1 
        },

        fnInitComplete: function(oSettings, json) {
            var api = this.api();

            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");

            $("#mytable_length").find("select");
            $("#tableSearch").on("keyup", function (e) {
                if (e.keyCode === 13) {
                    t.handleSearch();
                }
            });
            t.content.on("click", ".btn-searchbox", function () {
                t.handleSearch();
            });
            t.handleSearch = function () {
                let v = $("#tableSearch").val().trim();
                if (v === "") {
                    t.dTbl.search("").draw();
                } else {
                    t.dTbl.search(v).draw();
                }
            };
            t.btn.add = t.content.find(".open-add-modal");
            t.btn.search = t.content.find(".btn-searchbox");
            t.btn.reload = t.content.find(".btn-reload-list");
            t.btn.export = t.content.find(".btn-departments-export");
            t.btn.import = t.content.find(".btn-import");
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });

        }
    });



    t.createDepartment = function (e) {
        e.preventDefault();
        t.frmEl.tkt_auto_creation_id.parents(".tkt_auto_ceration").hide();
        t.frmEl.department_admin.parents(".tkt_auto_ceration").hide();
        t.frmEl.pc_custom_fieldset.parents(".pc_row").hide();
        t.frmEl.sc_custom_fieldset.parents(".pc_row").hide();
        t.frmEl.name.val('');
        t.frmEl.department_tag.val('');
        t.frmEl.company_id.val('');
        t.frmEl.attender_id.val('');
        t.frmEl.description.val('');
        t.frmEl.department_admin.val('');
        t.frmEl.department_head_id.val('');
        t.frmEl.access_role_id.val('').trigger("change");
        t.mdl.modal("show");
        t.mdltitle.text(t.config.translations.create_department);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        t.resetFrm();
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.frmEl.company_id.append(option).trigger('change');
        }

    };
    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            $('#img').hide();
            return false;
        }
        var frmData = new FormData;
        if(t.frmEl.attender_id.val() > 0){
            frmData.append('attender_id', t.frmEl.attender_id.val());
        } else{
            frmData.append('attender_id', "");
        }
        if(t.frmEl.department_head_id.val() > 0){
            frmData.append('department_head_id', t.frmEl.department_head_id.val());
        } else{
            frmData.append('department_head_id', "");
        }
        frmData.append('_token', t.config.token);
        frmData.append('name', t.frmEl.name.val());
        frmData.append('department_tag', t.frmEl.department_tag.val());
        frmData.append('company_id', t.frmEl.company_id.val());
        frmData.append('description', t.frmEl.description.val());
        frmData.append('tkt_auto_creation_id', t.frmEl.tkt_auto_creation_id.val());
        frmData.append('department_admin', t.frmEl.department_admin.val());
        frmData.append('asset_department', t.frmEl.asset_department.val());
        frmData.append('asset_department_admin', t.frmEl.asset_department_admin.val());
        frmData.append('department_custom_fieldset', t.frmEl.department_custom_fieldset.val());
        frmData.append('pc_id',t.frmEl.pc_custom_fieldset.val());
        frmData.append('sc_id',t.frmEl.sc_custom_fieldset.val());
        frmData.append('access_role_id',t.frmEl.access_role_id.val());

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
                    // sweetAlert('center', 'success', data);
                    Swal.fire({
                        icon: 'success',
                        title: config.translations.success,
                        text: data.msg || config.translations.saved_successfully,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    t.mdl.modal("hide");
                    $('#hand').hide();
                    t.dTbl.ajax.reload();
                }
                else {
                        Swal.fire({
                        icon: 'error',
                        title: config.translations.error,
                        text: data.msg || config.translations.something_went_wrong
                    });

                }
            }
        });
        http.fail(function () {
            Swal.fire({
                icon: 'error',
                title: config.translations.error,
                text: config.translations.something_went_wrong
            });
        });
        http.always(function () {
            $('#img').hide();
        });
    };
    t.loadForm = function (objdept) {
        t.resetFrm();
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.company == "object" && objdept.dropdown.company != null) {
            t.frmEl.company_id.append(new Option(objdept.dropdown.company.text, objdept.dropdown.company.id, true, true)).trigger("change").prop("disabled", true);
        }
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.attender_id == "object" && objdept.dropdown.attender_id != null) {
            t.frmEl.attender_id.append(new Option(objdept.dropdown.attender_id.text, objdept.dropdown.attender_id.id, true, true)).trigger("change");
        }

        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.pc == "object" && objdept.dropdown.pc != null) {
            t.frmEl.pc_custom_fieldset.html("");
            $.each(objdept.dropdown.pc, function(i, v) {
                let isSelected = objdept.data.pc_id == v.id;
                t.frmEl.pc_custom_fieldset.append(new Option(v.text, v.id, false, isSelected));
            });


            t.frmEl.pc_custom_fieldset.val(objdept.data.pc_id).trigger("change");
        }

        t.preselectedScId = objdept.data.sc_id || null;

        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.sc == "object" && objdept.dropdown.sc != null) {
            t.frmEl.sc_custom_fieldset.html("");
            $.each(objdept.dropdown.sc, function(i, v) {
                let isSelected = objdept.data.sc_id == v.id;
                t.frmEl.sc_custom_fieldset.append(new Option(v.text, v.id, true, isSelected));
            });
        }
        if (t.preselectedScId != null) {
            t.frmEl.pc_custom_fieldset.trigger('change');
        }

        if (objdept.data.tkt_auto_creation_id > 0) {
            t.frmEl.tkt_auto_creation_id.val(objdept.data.tkt_auto_creation_id).trigger("change");
        }
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.department_admin == "object" && objdept.dropdown.department_admin != null) {
            t.frmEl.department_admin.html("");
            $.each(objdept.dropdown.department_admin, function(i,v) {
                t.frmEl.department_admin.append(new Option(v.text, v.id, true, true)).trigger("change");
            })
        }
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.asset_department_admin == "object" && objdept.dropdown.asset_department_admin != null) {
            t.frmEl.asset_department_admin.html("");
            $.each(objdept.dropdown.asset_department_admin, function(i,v) {
                t.frmEl.asset_department_admin.append(new Option(v.text, v.id, true, true)).trigger("change");
            })
        }
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.department_custom_fieldset == "object" && objdept.dropdown.department_custom_fieldset != null) {
            t.frmEl.department_custom_fieldset.html("");
            $.each(objdept.dropdown.department_custom_fieldset, function(i,v) {
                t.frmEl.department_custom_fieldset.append(new Option(v.text, v.id, true, true)).trigger("change");
            })
        }
        if(objdept.data.asset_department != null) {
            t.frmEl.asset_department.val(objdept.data.asset_department).trigger("change");
        }
        if (typeof objdept.dropdown == "object" && typeof objdept.dropdown.department_head_id == "object" && objdept.dropdown.department_head_id != null) {
            t.frmEl.department_head_id.append(new Option(objdept.dropdown.department_head_id.text, objdept.dropdown.department_head_id.id, true, true)).trigger("change");
        }
        t.frm.find("input[name='name']").val(objdept.data.name);
        t.frm.find("input[name='department_tag']").val(objdept.data.department_tag);
        t.frmEl.description.val(objdept.data.description);
        if(objdept.data.access_role_id != null) {
            var role = objdept.data.access_role_id.split(",")
            t.frmEl.access_role_id.val(role).trigger('change');
        }
        t.mdl.modal("show");
    };

    t.frmEl.pc_custom_fieldset.on("change", function () {
        let parentId = t.frmEl.pc_custom_fieldset.val();
        let scDropdown = t.frmEl.sc_custom_fieldset;
        scDropdown.html(`<option value="">${config.translations.select_subcategory}</option>`);

        if (!parentId) {
            t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".pc_row").hide();
            return;
        }

        $.ajax({
            url: t.config.url.getSubCategories,
            type: "GET",
            data: { parent_id: parentId },
            success: function (response) {
                if (response.dropdown.sc.length > 0) {
                    let existingValues = [];

                    scDropdown.find("option").each(function () {
                        existingValues.push($(this).val());
                    });

                    $.each(response.dropdown.sc, function (i, v) {
                        if (!existingValues.includes(v.id.toString())) {
                            let isSelected = (t.preselectedScId && t.preselectedScId == v.id) || false;
                            scDropdown.append(new Option(v.text, v.id, false, isSelected));
                        }
                    });

                    t.frmEl.sc_custom_fieldset.attr('disabled', false).parents(".row").show();
                } else {
                    t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".row").hide();
                }
            },
            error: function () {
                t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".row").hide();
            }
        });
    });
    t.resetFrm = function () {
        t.frmEl.name.empty();
        t.frmEl.department_tag.empty();
        t.frmEl.company_id.empty().trigger("change");
        t.frmEl.attender_id.empty().trigger("change");
        t.frmEl.description.trigger("change");
        t.frmEl.tkt_auto_creation_id.val('').trigger("change");
        t.frmEl.department_admin.empty().trigger("change");
        t.frmEl.asset_department.val('').trigger("change");
        t.frmEl.asset_department_admin.empty().trigger("change");
        t.frmEl.department_custom_fieldset.empty().trigger("change");
        t.frmEl.pc_custom_fieldset.empty().trigger("change");
        t.frmEl.sc_custom_fieldset.empty().trigger("change");
        t.frmEl.department_head_id.empty().trigger("change");
        t.frmEl.access_role_id.val('').trigger("change");
        t.frmEl.company_id.val('').trigger("change");
        t.frmEl.company_id.prop("disabled", false);
        t.frmValidator.resetForm();
    };

    $('#btnSubmit').click(function () {
        $('#img').show();
    });

    t.editDepartment = function (e) {
        e.preventDefault();

        var accId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + accId;
        var http = $.get(t.config.url.get + "/" + accId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    if(data.data.data.module_ticket_enabled == "1") {
                        t.ticketFieldset.show();
                        t.frmEl.tkt_auto_creation_id.attr('disabled', false).parents(".tkt_auto_ceration").show();
                        t.frmEl.department_admin.attr('disabled', false).parents(".tkt_auto_ceration").show();
                    } else {
                        t.ticketFieldset.hide();
                        t.frmEl.tkt_auto_creation_id.attr('disabled', true).parents(".tkt_auto_ceration").hide();
                        t.frmEl.department_admin.attr('disabled', true).parents(".tkt_auto_ceration").hide();
                    }
                    t.mdl.modal("show");
                    t.mdltitle.text(t.config.translations.edit_department);
                    t.btn.submit.text(t.config.translations.save);
                    t.loadForm(data.data);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: config.translations.error,
                        text: data.msg || config.translations.something_went_wrong
                    });
                    if (t.dTbl) {
                        t.dTbl.ajax.reload(null, false);
                    }
                }
            }
        });
        http.fail(function (xhr) {
            var msg = (xhr.responseJSON && xhr.responseJSON.msg)
                ? xhr.responseJSON.msg
                : config.translations.something_went_wrong;

            Swal.fire({
                icon: 'error',
                title: config.translations.error,
                text: msg
            });

            if (xhr.status === 404 && t.dTbl) {
                t.dTbl.ajax.reload(null, false);
            }
        });
        http.always(function () {
            $('#img').hide();
            t.httpCall = true;
        });
    };

    t.frmEl.department_custom_fieldset.on("change", function () {
        if ($(this).val() !== null && $(this).val() !== "") {
            t.frmEl.pc_custom_fieldset.attr('disabled', false).parents(".pc_row").show();
            t.frmEl.sc_custom_fieldset.attr('disabled', false).parents(".pc_row").show();
        } else {
            t.frmEl.pc_custom_fieldset.empty();
            t.frmEl.sc_custom_fieldset.empty();
            t.frmEl.pc_custom_fieldset.attr('disabled', true).parents(".pc_row").hide();
            t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".pc_row").hide();
        }
    });

    t.frmEl.department_custom_fieldset.trigger("change");

    t.deleteDepartment = function (e) {
        e.preventDefault();

        let deptId = $(this).data("id");
        let url = t.config.url.delete + "/" + deptId;

        Swal.fire({
            title: config.translations.are_you_delete,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: config.translations.confirm_yes,
            cancelButtonText: config.translations.confirm_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: t.config.token
                    },
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: config.translations.deleted,
                                text: res.msg || config.translations.deleted_successfully,
                                confirmButtonText: config.translations.ok
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: config.translations.error,
                                text: res.msg || config.translations.something_went_wrong,
                                confirmButtonText: config.translations.ok
                            });
                        }
                        if (t.dTbl) {
                            t.dTbl.ajax.reload(null, false);
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: config.translations.error,
                            text: config.translations.something_went_wrong
                        });
                    }
                });
            }
        });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        ignore: [],                     
        rules: {
            company_id: {
                required: true,
                str_name: true
            },
            name: {
                required: true,
                str_name_format: true,
                charLimit: [2, 40]
            },
            department_tag: {
                clean_text_only: true,
            },
            attender_id: {
                str_name: true
            },
            description: {
                clean_text_only: true,
            },
        },
        messages: {
            company_id: config.translations.company_required,
            name: config.translations.valid_department
        },
        errorPlacement: function (error, element) {
            var errorWrap = getErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else {
                error.insertAfter(element.closest(".input-group"));
        }
            updateValidationState(element, true);
        },
        highlight: function (element) {
            updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            updateValidationState($(element), false);
        },
        invalidHandler: function (event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });

    // Trigger validation on change for important fields
    t.frmEl.company_id.on("change", function () {
        t.frmEl.company_id.valid();
    });
    t.frmEl.name.on("input", function () {
        t.frmEl.name.valid();
    });
    t.frmEl.department_tag.on("input", function () {
        t.frmEl.department_tag.valid();
    });
    t.frmEl.description.on("input", function () {
        t.frmEl.description.valid();
    });
    t.frmEl.attender_id.on("change", function () {
        t.frmEl.attender_id.valid();
    });
    t.frmEl.department_head_id.on("change", function () {
        t.frmEl.department_head_id.valid();
    });
    t.frmEl.department_custom_fieldset.on("change", function () {
        t.frmEl.department_custom_fieldset.valid();
    });
    t.frmEl.pc_custom_fieldset.on("change", function () {
        t.frmEl.pc_custom_fieldset.valid();
    });
    t.frmEl.sc_custom_fieldset.on("change", function () {
        t.frmEl.sc_custom_fieldset.valid();
    });
    t.frmEl.tkt_auto_creation_id.on("change", function () {
        t.frmEl.tkt_auto_creation_id.valid();
    });
    t.frmEl.department_admin.on("change", function () {
        t.frmEl.department_admin.valid();
    });
    t.frmEl.asset_department.on("change", function () {
        t.frmEl.asset_department.valid();
    });

    // Trigger validation on change for important fields
    t.frmEl.company_id.on("change", function () {
        t.frmEl.company_id.valid();
    });
    t.frmEl.name.on("input", function () {
        t.frmEl.name.valid();
    });
    t.frmEl.department_tag.on("input", function () {
        t.frmEl.department_tag.valid();
    });
    t.frmEl.description.on("input", function () {
        t.frmEl.description.valid();
    });
    t.frmEl.attender_id.on("change", function () {
        t.frmEl.attender_id.valid();
    });
    t.frmEl.department_head_id.on("change", function () {
        t.frmEl.department_head_id.valid();
    });
    t.frmEl.department_custom_fieldset.on("change", function () {
        t.frmEl.department_custom_fieldset.valid();
    });
    t.frmEl.pc_custom_fieldset.on("change", function () {
        t.frmEl.pc_custom_fieldset.valid();
    });
    t.frmEl.sc_custom_fieldset.on("change", function () {
        t.frmEl.sc_custom_fieldset.valid();
    });
    t.frmEl.tkt_auto_creation_id.on("change", function () {
        t.frmEl.tkt_auto_creation_id.valid();
    });
    t.frmEl.department_admin.on("change", function () {
        t.frmEl.department_admin.valid();
    });
    t.frmEl.asset_department.on("change", function () {
        t.frmEl.asset_department.valid();
    });

    // Trigger validation on change for important fields
    t.frmEl.company_id.on("change", function () {
        t.frmEl.company_id.valid();
    });
    t.frmEl.name.on("input", function () {
        t.frmEl.name.valid();
    });
    t.frmEl.department_tag.on("input", function () {
        t.frmEl.department_tag.valid();
    });
    t.frmEl.description.on("input", function () {
        t.frmEl.description.valid();
    });
    t.frmEl.attender_id.on("change", function () {
        t.frmEl.attender_id.valid();
    });
    t.frmEl.department_head_id.on("change", function () {
        t.frmEl.department_head_id.valid();
    });
    t.frmEl.department_custom_fieldset.on("change", function () {
        t.frmEl.department_custom_fieldset.valid();
    });
    t.frmEl.pc_custom_fieldset.on("change", function () {
        t.frmEl.pc_custom_fieldset.valid();
    });
    t.frmEl.sc_custom_fieldset.on("change", function () {
        t.frmEl.sc_custom_fieldset.valid();
    });
    t.frmEl.tkt_auto_creation_id.on("change", function () {
        t.frmEl.tkt_auto_creation_id.valid();
    });
    t.frmEl.department_admin.on("change", function () {
        t.frmEl.department_admin.valid();
    });
    t.frmEl.asset_department.on("change", function () {
        t.frmEl.asset_department.valid();
    });
    var buildSelect2Language = function (){
        return {
            noResults: function () {
                return config.translations.select2_no_results;
            },
            searching: function () {
                return config.translations.select2_searching;
            },
            inputTooShort: function (args) {
                return config.translations.select2_input_too_short_template.replace(
                    '{count}', args.minimum - args.input.length
                );
            },
            loadingMore: function () {
                return config.translations.select2_loading_more;
            },
            errorLoading: function () {
                return config.translations.select2_error_loading;
            }
        };
    }

    var select2Opts = { 
        language: buildSelect2Language(),
        dir: config.localization === 'ar' ? 'rtl' : 'ltr',
        width: "100%" 
    };
    t.frmEl.asset_department.select2($.extend({}, select2Opts, { placeholder:config.translations.select_ast_dept,
        dropdownParent: t.frmEl.asset_department.parent()
    }));
    t.frmEl.access_role_id.select2($.extend({}, select2Opts, { placeholder: config.translations.select_role,
        dropdownParent: t.frmEl.access_role_id.parent()
    }));

    t.frmEl.asset_department_admin .select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.asset_department_admin.parent(),
        ajax: {
            url: t.config.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    checkTech: 1,
                    company_id: function() {
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_dept_ast_admin
    }));
    
    t.frmEl.department_admin.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.department_admin.parent(),
        ajax: {
            url: t.config.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function() {
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_dept_admin
    }));

    t.frmEl.department_custom_fieldset.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.department_custom_fieldset.parent(),
        ajax: {
            url: t.config.url.getCustomFieldsetByModule + "/2",
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
        placeholder: config.translations.select_custom_fieldset
    }));

    t.frmEl.attender_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.attender_id.parent(),
        ajax: {
            url: t.config.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    checkTech: 1,
                    company_id: function() {
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_attender
    }));

    t.frmEl.department_head_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.department_head_id.parent(),
        ajax: {
            url: t.config.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function() {
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_dept_head
    }));
    t.frmEl.tkt_auto_creation_id.select2($.extend({},select2Opts, {dropdownParent: t.frmEl.tkt_auto_creation_id.parent(),placeholder: config.translations.select_account}));
    t.frmEl.pc_custom_fieldset.select2($.extend({}, select2Opts, {placeholder: config.translations.select_prob_cat,dropdownParent: t.frmEl.pc_custom_fieldset.parent(),allowClear:true}));
    t.frmEl.sc_custom_fieldset.select2($.extend({}, select2Opts, {placeholder: config.translations.select_sub_cat,dropdownParent: t.frmEl.sc_custom_fieldset.parent(),allowClear:true}));
    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
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
        placeholder: config.translations.select_company
    })).on("change", function (e) {
        t.frmEl.department_head_id.empty().val("").trigger("change");
        t.frmEl.attender_id.empty().val("").trigger("change");
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    }

    t.import = function(e) {
        e.preventDefault();
        window.location = t.config.url.import;
    }
    t.exportDepartment = function(e) {
        e.preventDefault();
        let searchValue = $('.searchbox').val().trim();
        let filterData = JSON.stringify({ search: searchValue });
        let encodedFilter = btoa(filterData);
        window.location = config.url.download_url + "?q=" + encodeURIComponent(encodedFilter);
    }
    t.content.on('click', '.open-add-modal', $.proxy(t.createDepartment));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editDepartment));
    t.content.on('click', '.open-delete', $.proxy(t.deleteDepartment));
    t.content.on('click','.btn-reload-list',$.proxy(t.reload));
    t.content.on('click','.btn-import',$.proxy(t.import));
    t.content.on('click','.btn-departments-export',$.proxy(t.exportDepartment));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    // t.dTbl.ajax.reload();
};
