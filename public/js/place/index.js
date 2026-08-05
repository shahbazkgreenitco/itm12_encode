var Place = function(config) {
    var t = this;
    var searchTimer;

    t.config = config;
    t.content = $('main#mainContent');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#place_modal');
    t.mdltitle = t.mdl.find('.modal-title');
    t.mdl.location = t.mdl.find('#location');
    t.mdl.company = t.mdl.find('#company');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#place_form');

    t.frmEl = {};
    t.frmEl.place = t.frm.find('#place_name');
    t.frmEl.location_id = t.frm.find('#location_id');
    t.frmEl.company_id = t.frm.find('#company_id');
    t.frmEl.branch_code = t.frm.find('#branch_code');
    t.frmEl.show_entries = $('#showSelect');
    t.frmEl.search = $('#tableSearch');

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.close = t.frm.find('#btnClose');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');
    t.btn.search_icon = t.content.find('.amg-list-searchbar__icon');
    t.searchbox = t.content.find(".searchbox");
    t.btn.showcolumns = t.content.find(".show-hide-columns");
    t.btn.showColumnsInput = t.content.find('#columnVisibilityControls1 input[type="checkbox"]')

    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        language: datatable_footer_translations(t.config.datatable_translations),
        autoWidth: false,
        processing: true,
        serverSide: true,
        stateLoadParams: function(settings, data) {
            data.length = 10;
        },
        lengthChange: false,
        colResize: {
            resizeTable: true
        },
        dom: "lrtip",
        colReorder: {
            realtime: true
        },
        responsive: true,
        stateSave: true,
        stateSaveParams: function (settings, data) { 
            data.search.search = '';
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
                    if(jQuery.inArray("PlaceEdit", t.config.permissions) !== -1) {
                        a.push("<button class='btn dtActbtn open-edit-modal' data-bs-toggle='tooltip' title='"+config.translations.edit_internal_place+"' data-id=\"" + d.id + "\"  data-name=\"" + d.name + "\"  ><i class=\"fa fa-pencil\"></i></button>");
                    }
                    if(jQuery.inArray("PlaceDelete", t.config.permissions) !== -1) {
                        a.push("<button class='btn dtActbtn open-delete' data-bs-toggle='tooltip' title='"+config.translations.delete_internal_place+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                    }
                    if(jQuery.inArray("PlaceEdit", t.config.permissions) == -1 && jQuery.inArray("PlaceDelete", t.config.permissions) == -1) {
                        t.dTbl.column(0).visible(false);
                    }
                    return(
                        `<div class="popup-toolbox"><div class="btn-toolbar popup-toolbox-status"><i class="fa fa-cog"></i></div>
                         <div class="popup-toolbox-bar" style="min-width: 40px;border-radius: 11px;color: white;background-color: #454b4e;">${a.join("")} </div></div>`
                    );
                }
            }
        ],
        order: [
            [5, 'desc']
        ],
        ajax: {
            url: config.url.list,
            type: "post",
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
                data:"a.id",
                orderable:true,
                render: function (data, type, row, meta) {
                    return `<span class="b5-text">${data ?? ''}</span>`;

                },
            },
            {
                data: 'a.place',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b5-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.branch_code',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b5-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.location',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b5-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.company',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b5-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.last_updated_at',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b5-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a",
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    // return t.buildActions(d);
                    return t.renderActionsCell(row.a || {}, type);
                },
            },
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            // $("#tableSearch").off(".DT");
            // var searchBox = '<div class="input-group table-search-btns">' +
            // '<input type="text" class="form-control searchbox plain-search" style="margin: 0;" placeholder="'+config.translations.press_enter_with_Search+'" />' +
            // '<span class="input-group-addon btn-searchbox" data-placement="bottom" data-toggle="tooltip" data-original-title="'+config.translations.Search+'"><i class="ps-icon plain-search-icon"></i></span>';
            // if(jQuery.inArray("PlaceAdd", t.config.permissions) !== -1) {
            //     searchBox += '<span class="input-group-addon open-add-modal" data-placement="bottom" action="add" data-toggle="tooltip" title="'+config.translations.create_place+'"><i class="ps-icon fa fa-plus"></i></span>';
            // }
            // searchBox += '<span class="input-group-addon btn-reload-list" data-toggle="tooltip" data-placement="left" data-original-title="'+config.translations.Refresh_List+'"><i class="ps-icon fa fa-refresh"></i></span>';
            // if(jQuery.inArray("PlaceImport", t.config.permissions) !== -1) {
            //     searchBox += '<span class="input-group-addon btn-import-place" data-placement="bottom" data-toggle="tooltip" data-original-title="'+config.translations.import_place+'"><i class="ps-icon fa fa-upload"></i></span>';
            // }
            // if(jQuery.inArray("PlaceDownload", t.config.permissions) !== -1) {
            //     searchBox += '<span class="input-group-addon btn-internal-place-export" data-toggle="tooltip" data-placement="bottom" data-original-title="'+config.translations.download+'"><i class="ps-icon fa fa-download"></i></span>';
            // }
            // searchBox +='<span class="input-group-addon btn-visible-content" aria-controls="advance-filters" style="color:gray">';
            // searchBox +='<span class="" id="columnVisibilityButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-columns"></i></span>';
            // searchBox +='<span class="dropdown-menu" style="margin:0px -117px" id="columnVisibilityControls"></span></span>';
            // searchBox += '</div>';
            // $("#tableSearch").empty().html(searchBox);
            /* $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            }); */
            $('.btn-searchbox').click(function (e) {
                e.preventDefault();
                var v = $("#mytable_wrapper .plain-search").validate_str_param();
                if (v === false) {
                    alert("Please enter a valid value for search");
                    return false;
                }
                api.search(v).draw();
            });
            $('.btn-reload-list').click(function (e) {
                e.preventDefault();
                var v = $(".amg-list-searchbar input").validate_str_param();
                if (v === false) {
                    return false;
                }
                api.search(v).draw();
            });
            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();
            
                api.columns().every(function () {
                    // if (!this.visible()) return; this line was not displaying the hidden columns/checkboxes
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;
            
                    controls.append(`
                        <div class="dropdown-item">
                            <label for="${columnId}">
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
        },
         drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        },
    });

    t.cache_filter_values = function() {
        var v = $("#tableSearch").validate_str_param();
        t.config.search = v;
        var jobj = { "search": t.config.search};
        t.config.export_filters = btoa(JSON.stringify(jobj));
    }

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        let company_id = config.company_default && config.company_default.length ? config.company_default[0].id : "";
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + "&company_id=" + company_id;
    }

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        let searchbox_value = $(e.target).val().trim();
        clearTimeout(searchTimer);
        if (e.keyCode == 13 || $(this).is("span") || searchbox_value.length >= 0) {
            var v = $("#tableSearch").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            searchTimer = setTimeout(function () {//using debouncer so only 1 ajax req get send on keyup, if users enters multiple key input quickly.
                t.config.search = v;
                // t.reload();
                t.dTbl.search(v).draw();
            }, 400);
        }
        else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.createlocation = function (e) {
        e.preventDefault();
        t.frmEl.place.val('');
        t.frmEl.location_id.val('');
        t.frmEl.company_id.val('');
        t.frmEl.branch_code.val('');
        t.loader.hide();
        t.mdltitle.text(t.config.translations.add_internal_place);
        if (Array.isArray(config.company_default) && config.company_default.length === 1 && config.company_default[0].id) {
            let option = new Option(config.company_default[0].text, config.company_default[0].id, true, true);
            t.frmEl.company_id.empty().append(option).trigger('change');
        }
        t.mdl.modal("show");
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        t.resetFrm();
    };

    t.import = function(e) {
        e.preventDefault();
        window.location = t.config.url.import_url;
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }

        t.loader.show();

        var frmData = new FormData;
        frmData.append('_token', t.config.token);
        frmData.append('place', t.frmEl.place.val());
        frmData.append('location_id', t.frmEl.location_id.val());
        frmData.append('company_id', t.frmEl.company_id.val());
        frmData.append('branch_code', t.frmEl.branch_code.val());
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function() {
                t.btn.submit.prop('disabled', true); // disable button
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    // sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: data.msg || "Saved successfully",
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
                        text: data.msg || "Something went wrong",
                        confirmButtonText: "OK",
                    });
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            // vex.dialog.alert("Something went wrong. Please check given details are correct");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            // sweetAlert('center', 'error', data);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.msg || "Something went wrong",
                confirmButtonText: "OK",
            });
        });
        http.always(function () {
            t.loader.hide();
            t.btn.submit.prop('disabled', false);
        });
    };

    t.loadForm = function(obj) {
        t.resetFrm();
        t.frmEl.place.val(obj.data.place);
        t.frmEl.branch_code.val(obj.data.branch_code);
        if (typeof obj.dropdown == "object" && typeof obj.dropdown.location == "object" && obj.dropdown.location != null) {
            t.frmEl.location_id.append(new Option(obj.dropdown.location.text, obj.dropdown.location.id, true, true)).trigger("change");
        }
        if(obj.data.company_id > 0) {
            t.frmEl.company_id.append(new Option(obj.dropdown.company.text, obj.dropdown.company.id, true, true)).trigger("change");
        }
    };

    t.resetFrm = function () {
        t.frmEl.place.val('');
        t.mdl.location_id();
        t.mdl.company_id();
        t.frmEl.branch_code.val('');
        t.frmValidator.resetForm();
        t.frm.find('.input-group').removeClass('error');
        t.frm.find('label.error').remove();
        t.frm.find('.field-error').empty();
    };

    t.editLocation = function (e) {
        e.preventDefault();
        t.resetFrm();
        var accId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + accId;
        var http = $.get(t.config.url.get + "/" + accId);
        http.done(function(result) {
            if (typeof result == "object") {
                if (result.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text(t.config.translations.edit_internal_place_modal_title);
                    t.btn.submit.text(t.config.translations.save);
                    t.loadForm(result.data, "edit");
                }
                else {
                    // sweetAlert('center', 'error', data);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: result.msg || "Something went wrong",
                        confirmButtonText: "OK",
                    });
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            // alert("Something went wrong. Please check given details are correct");
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            // sweetAlert('center', 'error', data);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: data.msg || "Something went wrong",
                confirmButtonText: "OK",
            });
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deletePlace = function (e) {
        e.preventDefault();
        var placeId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + placeId;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        // sweetAlerts(config.translations.are_you_delete,'warning', t.httpPostPath, t.dTbl, data);
        Swal.fire({
            title: t.config.translations.are_you_delete || "Are you sure?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: t.httpPostPath,
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
                                text: res.msg || "Deleted successfully",
                                confirmButtonText: "OK",
                            });

                            t.dTbl.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: res.msg || "Delete failed",
                                confirmButtonText: "OK",
                            });
                        }
                    },

                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Something went wrong",
                            confirmButtonText: "OK",
                        });
                    },
                });
            }
        });
        // vex.dialog.confirm({
        //     message: 'Are you sure to delete this Place?',
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
        //                 alert("Something went wrong. Please check given details are correct");
        //             });
        //             http.always(function () {
        //                 // t.httpCall = true;
        //             });
        //         }
        //     }
        // });
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            place_name: {
                required: true,
                clean_text_only: true,
            },
            location: {
                required: true,
            },
            company: {
                required: true,
            },
            branch_code:{
                required:true,
                clean_text_only: true,
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            $(element).closest('.input-group').addClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).closest('.input-group').removeClass(errorClass);
        },
    });

    var select2Opts = { width: "100%" };
    t.mdl.location_id = function () {
        t.frmEl.location_id.select2($.extend({}, select2Opts, {
            dropdownParent: t.frmEl.location_id.parent(),
            ajax: {
                url: t.config.getLocationByAjax,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 300
            },
            allowClear:true,
            // minimumInputLength: 1,
            placeholder: config.translations.select_place_placeholder,
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
            }
            
        }));
        // t.frmEl.location_id.empty().append(new Option("Select Location", "", true, true));
        // $.each(t.config.locations, function (i, v) {
        //     t.frmEl.location_id.append(new Option(v.text, v.id));
        // });
        // t.frmEl.location_id.trigger("change");
    };
    t.mdl.company_id = function () {
        // t.frmEl.company_id.empty().append(new Option("Select Company", "", true, true));
        $.each(t.config.companies, function (i, v) {
            t.frmEl.company_id.append(new Option(v.text, v.id));
        });
        t.frmEl.company_id.trigger("change");
    };
    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        // data: t.config.companies,
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
        placeholder: config.translations.select_company_placeholder,
        dropdownParent: t.mdl
    }));
    t.frmEl.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.content.on('click', '.open-add-modal', $.proxy(t.createlocation));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editLocation));
    t.content.on('click', '.open-delete', $.proxy(t.deletePlace));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.btn-import-place', $.proxy(t.import));
    t.content.on('click', '.btn-internal-place-export', $.proxy(t.export));
    t.content.on("keyup", t.searchbox, $.proxy(t.search));
    t.frmEl.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.btn.search_icon.on('click', function(){
        var v = $("#tableSearch").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.dTbl.search(v).draw();
    });

    $(t.btn.showColumnsInput).on("change", (e) => t.showColumns(e));
    t.btn.showcolumns.on("click", (e) => t.showHide(e));
    t.btn.close.on('click', function(){
        t.mdl.modal("hide");
    });
    // t.dTbl.ajax.reload();
};

Place.prototype.showColumns = function (e) {
    e.preventDefault();
    var $checkbox = $(e.currentTarget);
    var colIndex = $checkbox.data('column');
    this.dTbl.column(colIndex).visible($checkbox.is(':checked'));
};

Place.prototype.showHide = function (e) {
    e.preventDefault();
    $('#columnVisibilityControls').toggle();
};

/* Place.prototype.buildActions = function (d) {
    var t = this;

    let actions = `
        <div class="dropdown">
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
    `;

    if (t.hasPermission("PlaceEdit")) {
        actions += `
            <li>
                <a class="dropdown-item open-edit-modal" href="javascript:void(0)" data-id="${d.id}">
                    <i class="bi bi-pencil me-2"></i> ${t.config.translations.edit_internal_place}
                </a>
            </li>
        `;
    }

    if (t.hasPermission("StatusLabelDelete")) {
        actions += `
            <li>
                <a class="dropdown-item text-danger open-delete" href="javascript:void(0)" data-id="${d.id}">
                    <i class="bi bi-trash me-2"></i>  ${t.config.translations.delete_internal_place}
                </a>
            </li>
        `;
    }

    actions += `</ul></div>`;

    return actions;
};

Place.prototype.hasPermission = function (name) {
    return this.config.permissions.includes(name);
}; */

Place.prototype.renderActionsCell = function (record, type) {
    var t = this;
    var actionState = t.getRowActionState(record);
    var quickActions = [];
    // var dropdownId;
    if (type !== "display") return "";
    if (actionState.canEdit) {
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || t.config.translations.edit_internal_place,
            "dtActEdit",
            record.id,
            "edit",
            "open-edit-modal"
        ));
    }
    if (actionState.canDelete) {
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || t.config.translations.delete_internal_place,
            "dtActDel",
            record.id,
            "delete",
            "open-delete"
        ));
    }

    if (!quickActions.length) {
        return '<span class="user-list-empty">-</span>';
    }

    return [
        '<div class="user-list-actions">',
        quickActions.join(""),
        '</div>'
    ].join("");
}

Place.prototype.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass) {
    return [
        '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" data-bs-toggle="tooltip" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '">',
        this.getIcon(iconKey),
        '</button>'
    ].join("");
};

Place.prototype.getRowActionState = function (record) {
    return {
        canEdit: this.hasPermission("PlaceEdit"),
        canDelete: this.hasPermission("PlaceDelete"),
    };
};

Place.prototype.hasPermission = function (name) {
    return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
};

Place.prototype.escapeHtml = function (value) {
    return String(value === null || value === undefined ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
};

Place.prototype.getIcon = function (key) {
    return (this.icons && this.icons[key]) ? this.icons[key]() : "";
};

Place.prototype.icons = {
    edit: function () {
        return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
    },
    delete: function () {
        return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
    },
    view: function () {
        return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
    }
}

$(document).on('click', function (e) {
    if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
        $('#columnVisibilityControls').hide();
    }
});

$('#location_id, #company_id').on('change', function () {
    $(this).valid();
});

$(function () {
    new Place(config);
});