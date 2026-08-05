var Project = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main#mainContent');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#project_modal');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#project_form');

    t.frmEl = {};
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.project_no = t.frm.find('#project_no');
    t.frmEl.project_head = t.frm.find('#project_head');
    t.frmEl.company_id = t.frm.find('#company_id');
    t.frmEl.start_date = t.frm.find('#start_date');
    t.frmEl.description = t.frm.find('#description');
    t.mdl.description = t.content.find("#remarksModal");
    t.mdl.description.body = t.mdl.description.find("#text-data");
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClr');
    t.show_entries = $('#showSelect');
    t.showHideButton = t.content.find('.show-hide-columns');
    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    const iconsList = {
        icons: {
            edit: function () {
                return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
            },
            delete: function () {
                return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
            }
        }
    }


    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        pageLength: 10,
        lengthChange: false,
        colReorder: true,
        stateSaveParams: function (settings, data) {
            data.search.search = '';
            data.length = 10;
        },
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        },
        {
            targets: 0,
            render: function (d) {
                var x = d;
                var a = [];
                if (jQuery.inArray("ProjectEdit", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn open-edit-modal' data-toggle='tooltip' data-placement='right' data-original-title='Edit Project' data-id=\"" + d.id + "\"  data-name=\"" + d.name + "\"  ><i class=\"fa fa-pencil\"></i></button>");
                }
                if (jQuery.inArray("ProjectDelete", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn open-delete' data-toggle='tooltip' data-original-title='Delete Project' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                }
                return a.join("");
            }
        },
        ],
        order: [
            [5, 'desc']
        ],
        processing: true,
        serverSide: true,
        colResize: {
            resizeTable: true
        },
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        stateSave: false,
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
                }
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            {
                data: null,
                name: 'project_info',
                render: function (d) {
                    if (!d.a) return '';
                    var a = [];

                    a.push('<div class="deviceModelText b1-text" data-toggle="tooltip" data-original-title="Project Name">' + d.a.name + '</div>');

                    if (d.a.project_no) {
                        a.push('<div class="b1-text opacity-50" data-toggle="tooltip" data-original-title="Project Number">' + d.a.project_no + '</div>');
                    }

                    if (d.a.description) {
                        var description = String(d.a.description);

                        if (description.length > 50) {
                            var truncated = truncateHtml(description, 50);
                            a.push('<div data-toggle="tooltip" data-original-title="Description">' + truncated +
                                '<a class="read-more" style="cursor: pointer; color: #00a1ff;" data-full-text="' + escapeHtml(description) + '">...Read More</a></div>');
                        } else {
                            a.push('<div data-toggle="tooltip" data-original-title="Description">' + description + '</div>');
                        }
                    }
                    return a.join("");
                }
            },
            {
                data: 'a.project_head',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.company',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.start_date_format',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.updated_at_format',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a.id",
                className: "users-col-actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return renderActionsCell(row.a || {}, type); }
            },
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#tableSearch").off(".DT");
            // var searchBox  = '<div class="input-group table-search-btns">'
            //     +'<input type="text" class="form-control searchbox plain-search" placeholder="'+config.translations.press_enter_with_Search+'" />'
            //     +'<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="'+config.translations.Search+'"><i class="ps-icon plain-search-icon"></i></span>';
            //     searchBox += '<span class="input-group-addon btn-reload-list" data-toggle="tooltip" data-placement="bottom" data-original-title="'+config.translations.Refresh_List+'"><i class="ps-icon fa fa-refresh"></i></span>';
            //     if(jQuery.inArray("ProjectAdd", config.permissions) !== -1) {
            //         searchBox += '<span class="input-group-addon btn-add" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.add_new_project + '"><i class="ps-icon fa fa-plus"></i></span>';
            //     }
            //     searchBox +='<span class="input-group-addon btn-export" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.Download + '"><i class="ps-icon fa fa-download"></i></span>';
            //     searchBox += '<span class="input-group-addon btn-import_devices" data-toggle="tooltip" data-placement="bottom" data-original-title="'+config.translations.import_project+'"><i class="ps-icon fa fa-upload"></i></span>';
            //     searchBox +='<span class="input-group-addon btn-visible-content" aria-controls="advance-filters" style="color:gray">';
            //     searchBox +='<span class="" id="columnVisibilityButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-columns"></i></span>';
            //     searchBox +='<span class="dropdown-menu" style="margin:0px -117px" id="columnVisibilityControls"></span></span>';
            //     searchBox +='</div>';
            // $("#mytable_wrapper").removeClass("form-inline");
            // t.table.closest("div").addClass("table-responsive");

            // $(searchBox).insertBefore("#mytable_filter");
            // $("#mytable_filter").remove();
            // $("#mytable_length").find("select").select2();
            $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.valid_value_search);
                        return false;
                    }
                    api.search(v).draw();
                }
            });

            $('.amg-list-searchbar__icon').click(function (e) {
                e.preventDefault();
                var v = $("#tableSearch").validate_str_param();
                if (v === false) {
                    alert(config.translations.valid_value_search);
                    return false;
                }
                api.search(v).draw();
            });

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();

                api.columns().every(function () {
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;

                    controls.append(`
                        <div class="dropdown-item text-nowrap">
                            <label class="d-inline-flex align-items-center gap-2 mb-0">
                                <input type="checkbox" id="${columnId}" data-column="${columnIndex}" ${this.visible() ? 'checked' : ''}> ${columnTitle}
                            </label>
                        </div>
                    `);
                });
            }
            updateColumnVisibilityControls();
            $('#columnVisibilityControls').on('change', 'input[type="checkbox"]', function () {
                api.column(+$(this).data('column')).visible(this.checked);
            });
            api.on('column-reorder', () => setTimeout(updateColumnVisibilityControls, 10));
        }
    });

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#mytable_wrapper .plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };
    t.import = function (e) {
        e.preventDefault();
        window.location = t.config.url.project_import;
    };

    t.content.on('click', '.read-more', function (e) {
        e.preventDefault();
        var fullText = $(this).data('full-text');
        $(t.mdl.description.body).html(fullText);
        $(t.mdl.description).modal("show");
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
        return String(text === null || text === undefined ? "" : text).replace(
            /[&<>"'`=\/]/g,
            function (s) {
                return entityMap[s];
            }
        );
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

    t.frmEl.description.summernote({
        width: '100%',
        width: '100%',
        inheritPlaceholder: true,
        placeholder: "write content here",
        toolbar: [
            ["style", ["bold", "italic", "underline", "clear"]],
            ["para", ["ul", "ol"]],
        ],
        minHeight: 100,
        focus: true,
    });

    t.createProject = function (e) {
        e.preventDefault();
        t.frmEl.name.val('');
        t.frmEl.project_no.val('');
        t.frmEl.project_head.val('');
        t.frmEl.start_date.val('');
        t.frmEl.description.val('');
        t.frmEl.company_id.val('');
        t.loader.hide();
        t.frmEl.description.summernote('code', "");
        t.resetFrm();
        if (Array.isArray(config.company_default) && config.company_default.length === 1 && config.company_default[0].id) {
            let option = new Option(config.company_default[0].text, config.company_default[0].id, true, true);
            t.frmEl.company_id.empty().append(option).trigger('change');
        }
        t.mdltitle.text(t.config.translations.add_new_project);
        t.mdl.modal("show");
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }

        t.loader.show();

        var frmData = new FormData;
        frmData.append('_token', t.config.token);
        frmData.append('name', t.frmEl.name.val());
        frmData.append('project_no', t.frmEl.project_no.val());
        frmData.append('company_id', t.frmEl.company_id.val());
        if (t.frmEl.project_head.val() > 0) {
            frmData.append('project_head', t.frmEl.project_head.val());
        }
        else {
            frmData.append('project_head', null);
        }
        frmData.append('start_date', t.frmEl.start_date.val());
        frmData.append('description', t.frmEl.description.val());
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
                        icon: "success",
                        title: "Success!",
                        text: data.msg || config.translations.project_added_successfully,
                        confirmButtonText: "OK",
                    });
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    // sweetAlert('center', 'error', data);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.msg || config.translations.project_add_failed,
                        confirmButtonText: "OK",
                    });
                }
            }
        });
        http.fail(function () {
            var data = {
                msg: config.translations.something_went_wrong_details,
            }
            // sweetAlert('center', 'error', data);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.msg || config.translations.something_went_wrong,
                confirmButtonText: "OK",
            });
        });
        http.always(function () {
            t.loader.hide();
        });
    };

    t.loadForm = function (project) {
        t.resetFrm();

        if (typeof project.dropdown == "object" && typeof project.dropdown.project_head == "object") {
            t.frmEl.project_head.append(new Option(project.dropdown.project_head.text, project.dropdown.project_head.id)).trigger("change");
        }

        if (typeof project.dropdown == "object" && typeof project.dropdown.company_id == "object") {
            t.frmEl.company_id.append(new Option(project.dropdown.company_id.text, project.dropdown.company_id.id)).trigger("change");
        }

        t.frmEl.start_date.datepicker('setDate', project.data.start_date);
        // console.log(project.data.start_date);
        t.frm.find("input[name='name']").val(project.data.name);
        t.frm.find("input[name='project_no']").val(project.data.project_no);
        t.frm.find("input[name='company_id']").val(project.data.project_no);
        // t.frm.find("textarea[name='description']").val(project.data.description);
        t.frmEl.description.summernote('code', project.data.description);

        t.mdl.modal("show");
    };

    t.resetFrm = function () {
        t.frmEl.name.val('');
        t.frmEl.project_no.val('');
        t.frmEl.project_head.empty().trigger("change");
        t.frmEl.start_date.val('');
        t.frmEl.description.val('');
        t.frmEl.company_id.empty().trigger("change");
        t.frmValidator.resetForm();
    };

    t.editProject = function (e) {
        e.preventDefault();
        t.resetFrm();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + projectId;
        var http = $.get(t.config.url.get + "/" + projectId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text(t.config.translations.edit_project);
                    t.btn.submit.text(t.config.translations.save);
                    t.loadForm(data.data);
                }
                else {
                    // sweetAlert('center', 'error', data);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.msg || config.translations.something_went_wrong,
                        confirmButtonText: "OK",
                    });
                }
            }
        });
        http.fail(function () {
            alert(config.translations.something_went_wrong_details);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };
    t.deleteProject = function (e) {
        e.preventDefault();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + projectId;
        let deleteUrl = t.httpPostPath;
        /* sweetAlertConfirmation({
            message: config.translations.are_you_sure_delete,
            onConfirm: function() {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        }
                        else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function () {
                    alert(config.translations.something_went_wrong_details);
                });
                http.always(function () {
                    // t.httpCall = true;
                });
            }
        }); */
        Swal.fire({
            title: config.translations.are_you_delete || "Are you sure?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: config.translations.modal_yes_button,
            cancelButtonText: config.translations.modal_cancel_button,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: "GET",

                    beforeSend: function () {
                        Swal.fire({
                            title: "Deleting...",
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                        });
                    },

                    success: function (res) {
                        Swal.close();

                        if (res.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: res.msg || config.translations.deleted_successfully,
                                confirmButtonText: "OK",
                            });

                            t.dTbl.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: res.msg || config.translations.deleted_fail,
                                confirmButtonText: "OK",
                            });
                        }
                    },

                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: config.translations.something_went_wrong,
                            confirmButtonText: "OK",
                        });
                    },
                });
            }
        });
    };
    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        let company_id = config.company_default && config.company_default.length ? config.company_default[0].id : "";
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + "&company_id=" + company_id;
    };

    t.cache_filter_values = function () {
        var v = $("#mytable_wrapper .plain-search").validate_str_param();
        t.config.search = v;
        var jobj = { "search": t.config.search };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            name: {
                required: true,
                maxlength: 30,
                str_name: true
            },
            project_no: {
                remarks: true
            },
            project_head: {
                str_name: true
            },
            description: {
                long_text: true
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            $(element).closest('.select-div').addClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).closest('.select-div').removeClass(errorClass);
        },
    });

    $('#deselect').click(function () {
        t.frmEl.project_head.val(0).trigger("change");
    });

    var select2Opts = { width: "100%" };
    t.frmEl.project_head.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.project_head.parent(),
        ajax: {
            url: t.config.getUsers,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select the Project Head",
        templateSelection: function (data, container) {
            if (data && data.text) {
                $(container).attr('title', data.text);
                return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
            }
            return data.text || "";
        }
    }));

    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
        // data: t.config.company_default,
        ajax: {
            url: t.config.getCompanyUsers,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select Company Name",
        templateSelection: function (data, container) {
            if (data && data.text) {
                $(container).attr('title', data.text);
                return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
            }
            return data.text || "";
        }
    }));

    t.mdl.on('shown.bs.modal', function () {
        t.frmEl.start_date.datepicker({ autoclose: true, format: "dd/mm/yyyy", todayHighlight: true, orientation: "bottom auto", container: "#project_modal" });
    });
    t.content.on("click", '.btn-projects-import', $.proxy(t.import));
    t.content.on("click", '.btn-reload-list', $.proxy(t.reload));
    t.content.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.content.on('click', '.open-add-modal', $.proxy(t.createProject));
    t.content.on('click', '.go-edit', $.proxy(t.editProject));
    t.content.on('click', '.dtActDel', $.proxy(t.deleteProject));
    t.content.on('click', '.btn-projects-export', $.proxy(t.export));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    // t.dTbl.ajax.reload();

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.showHideButton.on("click", (e) => t.showHide(e));

    t.showHide = function (e) {
        e.preventDefault();
        $('#columnVisibilityControls').toggle();
    };

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
            $('#columnVisibilityControls').hide();
        }
    });

    function renderActionsCell(record, type) {
        var t = this;
        var actionState = getRowActionState(record);
        var quickActions = [];
        // var dropdownId;
        if (type !== "display") return "";
        if (actionState.canEdit) {
            quickActions.push(quickActionButtonHtml(
                (config.translations || {}).edit || "Edit",
                "dtActEdit",
                record.id,
                "edit",
                "go-edit"
            ));
        }
        if (actionState.canDelete) {
            quickActions.push(quickActionButtonHtml(
                (config.translations || {}).delete || "Delete",
                "dtActDel",
                record.id,
                "delete",
                "go-del"
            ));
        }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions d-flex justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    function quickActionButtonHtml(label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn ', escapeHtml(extraClass || ""), ' ', cls, '" data-id="', escapeHtml(id), '" title="', escapeHtml(label), '" aria-label="', escapeHtml(label), '">',
            getIcon(iconKey),
            '</button>'
        ].join("");
    }

    function getRowActionState(record) {
        return {
            canEdit: hasPermission("ProjectEdit"),
            canDelete: hasPermission("ProjectDelete"),
        };
    }

    function hasPermission(name) {
        return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
    }

    function getIcon(key) {
        return (iconsList.icons && iconsList.icons[key]) ? iconsList.icons[key]() : "";
    }

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            t.dTbl.columns.adjust();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        t.dTbl.columns.adjust();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

};

$(function () {
    new Project(config);
});