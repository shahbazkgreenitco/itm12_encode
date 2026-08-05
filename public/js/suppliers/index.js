var MyApp = function (config) {

    var t = this;
    t.config = config;
    var table = $('#mytable');
    t.mdl = $("#addSupplierModal");
    t.frm = t.mdl.find("#SupplierForm");
    t.frm.name = t.frm.find("#name");
    t.frm.business_category = t.frm.find("#business_category");
    t.frm.contact = t.frm.find("#contact");
    t.frm.address = t.frm.find("#address");
    t.frm.address2 = t.frm.find("#address2");
    t.frm.country_id = t.frm.find("#country_id");
    t.frm.state_id = t.frm.find("#state_id");
    t.frm.city_id = t.frm.find("#city_id");
    t.frm.zip = t.frm.find("#zip");
    t.frm.phone = t.frm.find("#phone");
    t.frm.email = t.frm.find("#email");
    t.frm.pan = t.frm.find("#pan");
    t.frm.bank_acc_number = t.frm.find("#bank_acc_number");
    t.frm.bank_name = t.frm.find("#bank_name");
    t.frm.bank_ifsc = t.frm.find("#bank_ifsc");
    t.frm.bank_branch = t.frm.find("#bank_branch");
    t.frm.bank_acc_name = t.frm.find("#bank_acc_name");
    t.frm.url = t.frm.find("#url");
    t.frm.notes = t.frm.find("#notes");
    t.frm.image = t.frm.find("input[name='image']");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find('#btnClear');
    t.frm.list =  t.frm.find("#supplier-image-list");
    t.mdltitle = t.mdl.find('.modal-title');
    t.show_entries = $('#showSelect')
    t.showColumnsInput = $('#columnVisibilityControls input[type="checkbox"]')
    t.showHideButton = $('.show-hide-columns')
    t.cache_filter_values = function() {
        var v = $("#tableSearch").validate_str_param();
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    var dTbl = table.DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        // stateSave: true,
        lengthChange: false,
        pageLength: 10,
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        aoColumnDefs: [
            { 'bSortable': false, 'aTargets': [0] },
            {
                targets: 0,
                render: function (d) {
                    var a = [];
                    if(jQuery.inArray("SupplierView", config.permissions) !== -1) {
                        a.push("<button class='btn dtActbtn go-info' data-toggle='tooltip' data-original-title='"+config.translations.info+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-info\"></i></button>");
                    }
                    if(jQuery.inArray("SupplierEdit", config.permissions) !== -1) {
                        a.push("<button class='btn dtActbtn go-edit' data-toggle='tooltip' data-original-title='"+config.translations.edit+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-pencil\"></i></button>");
                    }
                    if(jQuery.inArray("SupplierDelete", t.config.permissions) == -1 && jQuery.inArray("LocationDelete", t.config.permissions) == -1) {
                        a.push("<button class='btn dtActbtn dtActDel go-del' data-toggle='tooltip' data-original-title='"+config.translations.delete+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                    }
                    return '<div class="popup-toolbox">' +
                        '<div class="btn-toolbar popup-toolbox-trigger"><i class="fa fa-cog"></i></div>' +
                        '<div class="popup-toolbox-bar" style="min-width: 40px;border-radius: 11px;color: white;background-color: #454b4e;">' +
                        a.join('') + '</div>' + '</div>';
                }
            }
        ],
        order: [[10, 'desc']],
        colResize: { resizeTable: true },
        ajax: {
            url: config.url.list,
            type: "post",
            data: function (d) {
                d._token = config.token;
                d.search = $("#tableSearch").validate_str_param();
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
                data: 'a.name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.contact',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.phone',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.email',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.address',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.city_name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.state_name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.country_name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a.id",
                width: "10%",
                className: "users-col-actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
            },
            { data: 'a.updated_at', visible: false, searchable: false },
            // {
            //     data: null,
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     },
            // }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            t.show_entries.select2({
                theme: 'custom',
                minimumResultsForSearch: Infinity,
                width: 'auto'
            });

            $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();
                api.columns().every(function () {
                    if (!this.visible()) return;
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;
                    controls.append(`
                        <div class="dropdown-item">
                            <label>
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

            $('.btn-suppliers-export').click(function (e) {
                e.preventDefault();
                t.cache_filter_values();
                window.location = config.url.download_url + "?q=" + t.config.export_filters;
            });
            $(".btn-redirection").on("click", function(e) {
                e.preventDefault();
                window.location.href = $(this).attr("data-href");
            });
        }
    });

    t.editSupplier = function(e) {
        e.preventDefault();
        $('#preview-image').show();
        var supplierId = $(this).attr("data-id");
        t.httpPostPath = config.url.edit + "/" + supplierId;
        var http = $.get(config.url.getInfo + "/" + supplierId);
        http.done(function(data) {
            if (typeof data == "object" && data.status == "success") {
                t.mdl.modal("show");
                t.mdltitle.text(config.translations.edit_supplier);
                // t.btn.submit.text("Save Changes");
                t.loadForm(data.data);
                
            } else {
                // sweetAlert('center', 'error', data.msg || "Error loading supplier");
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.msg || "Error loading supplier",
                    confirmButtonText: "OK",
                });
            }
        });
        http.fail(function() {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    $(document).on('click', '.go-info', function(e) {
        e.preventDefault();
        window.location.href = config.url.info + "/" + $(this).attr('data-id');
    });

    $(document).on('click', '.dtActDel', function(e) {
        e.preventDefault();
        var url = config.url.delete + "/" + $(this).attr('data-id');
        var data = { 'msg': config.translations.something_went_wrong };
        // sweetAlerts(config.translations.are_you_delete,'warning', url, dTbl, data);
        Swal.fire({
            title: t.config.translations.are_you_delete || "Are you sure?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
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

                            dTbl.ajax.reload();
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
    });

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $("#tableSearch").validate_str_param();
        if(v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        dTbl.ajax.reload();
    };

    t.addSupplier = function (e) {
        e.preventDefault();

        t.httpPostPath = config.url.add;
        t.mdl.modal("show");
        t.mdltitle.text(config.translations.add_supplier);
        // t.btn.submit.text("Save");
        t.resetFrm();
    };
        
    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        ignore: [],
        rules: {
            name: {
                required: true,
                maxlength: 255,
                str_name: true
            },
            contact: {
                maxlength: 100,
                str_name: true
            },
            address: {
                maxlength: 75,
                str_address: true
            },
            address2: {
                maxlength: 75,
                str_address: true
            },
            city: {
                maxlength: 255,
                str_address: true
            },
            state: {
                maxlength: 255,
                str_address: true
            },
            zip: {
                maxlength: 200,
                number: true
            },
            phone: {
                maxlength: 10,
                number: true
            },
            email: {
                maxlength: 150,
                email: true
            },
            pan:{
                str_name: true,
            },
            bank_acc_number:{
                number:true,
            },
            bank_name:{
                str_name:true,
            },
            bank_ifsc:{
                str_name:true,
            },
            bank_branch:{
                str_name:true,
            },
            bank_acc_name:{
                str_name:true,
            },
            url: {
                maxlength: 250,
                remarks: true,
                url: true
            },
            notes: {
                maxlength: 255,
                remarks: true,
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function(element, errorClass) {
            $(element).closest('.select-div').addClass(errorClass);
        },
        unhighlight: function(element, errorClass) {
            $(element).closest('.select-div').removeClass(errorClass);
        },
    });

    t.handleSubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.frm[0]);
        formData.append('_token', config.token);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    // sweetAlert('center', 'success', data);
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: data.msg || "Supplier added successfully",
                        confirmButtonText: "OK",
                    });
                    t.mdl.modal("hide");
                    if (typeof dTbl !== "undefined" && $.fn.DataTable.isDataTable(dTbl)) {
                        dTbl.ajax.reload(null, false);
                    }
                } else {
                    // sweetAlert('center', 'error', data);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: data.msg || "Add Supplier failed",
                        confirmButtonText: "OK",
                    });
                }
            }
        });
        http.fail(function (xhr) {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            var response = xhr.responseJSON;
            // sweetAlert('center', 'error', data);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: response?.msg || config.translations.something_went_wrong,
                confirmButtonText: "OK",
            });
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.loadForm = function (obj) {
        t.resetFrm();
        if (obj.attachments && obj.attachments.length) {
        obj.attachments.forEach(function (attachment) {
            // create li
            let $li = $("<li>")
                .addClass("list-group-item d-flex align-items-center justify-content-between")
                .attr("data-file", attachment.file_name);

            // image preview
            let $img = $("<img>")
                .attr("src", "/storage/supplier/" + attachment.thumbnail_file_name) // adjust path as per your disk
                .addClass("img-thumbnail")
                .css({ width: "80px", height: "80px", objectFit: "cover", marginRight: "10px" });

            // file link (optional under image)
            let $link = $("<a>")
                .attr("href", "/supplier-image-download/" + attachment.file_name)
                .attr("target", "_blank")
                .text(attachment.original_file_name);

            // left side container (image + link)
            let $left = $("<div>").append($img).append("<br>").append($link);

            // delete link styled as button
            let $del = $("<button>")
                .addClass("btn btn-sm btn-danger del-link")
                .attr("data-file", attachment.file_name)
                .html("Delete");

            // append to li
            $li.append($left).append($del);

            t.frm.list.append($li);
        });
    } else {
        t.frm.list.append("<li class='list-group-item'>No files uploaded</li>");
    }

        $(document).on('click', '.del-link', function (e) {
        e.preventDefault();
        var fileName = $(this).attr('data-file');
        var deleteUrl = config.url.image_delete + '/' + fileName;
        var $currentLi = $(this).closest('li');

        Swal.fire({
            title: config.translations.are_you_delete_attachment,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5bd810",
            cancelButtonColor: "#cc3333",
            confirmButtonText: "Yes, Delete",
            cancelButtonText: "Cancel",
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'GET',
                    data: { _token: config.token },
                    success: function(response) {
                        if (response.status === 'success') {
                            $currentLi.fadeOut(300, function() {
                                $(this).remove();
                                if (t.frm.list.children().length === 0) {
                                    t.frm.list.append("<li class='list-group-item'>No files uploaded</li>");
                                }
                            });
                            t.mdl.modal('hide');
                            // sweetAlert('center', 'success', response);
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: response.msg || "Deleted successfully",
                                confirmButtonText: "OK",
                            });
                        } else {
                            // sweetAlert('center', 'error', response);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.msg || "Delete failed",
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function() {
                        var data = { msg: config.translations.something_went_wrong };
                        // sweetAlert('center', 'error', data);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.msg || "Delete failed",
                            confirmButtonText: "OK",
                        });
                    }
                });
            }
        });
    });
        t.frm.name.val(obj.supplier.name);
        if (obj.business_categories_name && obj.supplier.business_categories) {
            let ids = obj.supplier.business_categories.split(",");
            obj.business_categories_name.forEach(function(cat) {
                if (ids.includes(cat.id.toString())) {
                    let option = new Option(cat.business_tag, cat.id, true, true);
                    t.frm.business_category.append(option);
                }
            });
            t.frm.business_category.trigger('change');
        } else {
            t.frm.business_category.val(null).trigger('change');
        }

        // Country
        if (obj.supplier.country_id && obj.supplier.country_name) {
            t.frm.country_id.append(new Option(obj.supplier.country_name, obj.supplier.country_id, true, true)).trigger('change');
        }

        // State
        if (obj.supplier.state_id && obj.supplier.state_name) {
            t.frm.state_id.append(new Option(obj.supplier.state_name, obj.supplier.state_id, true, true)).trigger('change');
        }

        // City
        if (obj.supplier.city_id && obj.supplier.city_name) {
            t.frm.city_id.append(new Option(obj.supplier.city_name, obj.supplier.city_id, true, true)).trigger('change');
        }

        t.frm.contact.val(obj.supplier.contact);
        t.frm.address.val(obj.supplier.address);
        t.frm.address2.val(obj.supplier.address2);
        t.frm.zip.val(obj.supplier.zip);
        t.frm.phone.val(obj.supplier.phone);
        t.frm.email.val(obj.supplier.email);
        t.frm.pan.val(obj.supplier.pan);
        t.frm.bank_acc_number.val(obj.supplier.bank_acc_number);
        t.frm.bank_name.val(obj.supplier.bank_name);
        t.frm.bank_ifsc.val(obj.supplier.bank_ifsc);
        t.frm.bank_branch.val(obj.supplier.bank_branch);
        t.frm.bank_acc_name.val(obj.supplier.bank_acc_name);
        t.frm.url.val(obj.supplier.url);
        t.frm.notes.val(obj.supplier.notes); 


    };

    var select2Opts = { width: "100%" };
    t.frm.country_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.frm.country_id.parent(),
        ajax: {
            url: config.url.country,
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
        placeholder: config.translations.select_country
    }));

    t.frm.state_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frm.state_id.parent(),
        ajax: {
            url: config.url.state,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    country_id: t.frm.country_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_state
    }));

    t.frm.city_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.frm.city_id.parent(),
        ajax: {
            url: config.url.city,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    state_id: t.frm.state_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_city
    }));

    t.frm.country_id.on('change', function () {
        t.frm.state_id.val(null).trigger('change');
        t.frm.state_id.empty();

        t.frm.city_id.val(null).trigger('change');
        t.frm.city_id.empty();
    });

    t.frm.state_id.on('change', function () {
        t.frm.city_id.val(null).trigger('change');
        t.frm.city_id.empty();
    });

    t.resetFrm = function () {
        // Text inputs
        t.frm.name.val('');
        t.frm.contact.val('');
        t.frm.address.val('');
        t.frm.address2.val('');
        t.frm.zip.val('');
        t.frm.phone.val('');
        t.frm.email.val('');
        t.frm.pan.val('');
        t.frm.bank_acc_number.val('');
        t.frm.bank_name.val('');
        t.frm.bank_ifsc.val('');
        t.frm.bank_branch.val('');
        t.frm.bank_acc_name.val('');
        t.frm.url.val('');
        t.frm.list.empty();

        // Summernote
        // if (t.frm.notes.hasClass('summernote')) {
        //     t.frm.notes.summernote('reset'); // or .code('') for older versions
        // } else {
        //     t.frm.notes.val('');
        // }

        // Select2 dropdowns
        t.frm.business_category.val(null).trigger('change');
        t.frm.country_id.val(null).trigger('change');
        t.frm.state_id.val(null).trigger('change');
        t.frm.city_id.val(null).trigger('change');

        // File input
        t.frm.find("input[name='image']").val('');
        t.frmValidator.resetForm();
    };

    t.frm.business_category.select2($.extend({}, select2Opts, {
        dropdownParent:t.frm.closest('.modal'),
        ajax: {
            url: config.url.getBusinessCategories,
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
        placeholder: "Select The Business Category",
        tags: true,
        createTag: function (params) {
            var term = $.trim(params.term);

            if (!term) {
                return null;
            }

            return {
                id: term,
                text: term,
                newTag: true
            };
        },
        insertTag: function (data, tag) {
            data.unshift(tag);
        },
        templateSelection: function (data, container) {
            if (container) {
                $(container).attr('title', data.text);
            }
            return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
        }
    }));

    $(document).on("click", ".amg-list-searchbar__icon", t.tableSearch);
    $(document).on("click", ".btn-reload-list", t.tableSearch);
    $(document).on("click", ".open-add-modal", t.addSupplier);
    $(document).on('click', '.go-edit', t.editSupplier);
    $(document).on('click', '.btn-import-suppliers', function (e) { t.import(e); });
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));


    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        dTbl.page.len(value).draw();
    });

    t.showHideButton.on("click", (e) => t.showHide(e));

    t.showHide = function (e) {
        e.preventDefault();
        $('#columnVisibilityControls').toggle();
    };

    t.renderActionsCell = function (record, type) {
        var t = this;
        var actionState = t.getRowActionState(record);
        var quickActions = [];
        // var dropdownId;
        if (type !== "display") return "";
        // if (actionState.canRestore) {
        //     quickActions.push(t.quickActionButtonHtml(
        //         (t.config.translations || {}).action_restore_user || "Supplier Info",
        //         "dtActRestore",
        //         record.id,
        //         "restore"
        //     ));
        // } else {
            if (actionState.canView) {
                quickActions.push(t.quickActionButtonHtml(
                    (t.config.translations || {}).action_view_supplier || "Supplier Info",
                    "dtActInfo",
                    record.id,
                    "view",
                    "go-info"
                ));
            }
            if (actionState.canEdit) {
                quickActions.push(t.quickActionButtonHtml(
                    (t.config.translations || {}).action_edit_supplier || "Edit",
                    "dtActEdit",
                    record.id,
                    "edit",
                    "go-edit"
                ));
            }
            if (actionState.canDelete) {
                quickActions.push(t.quickActionButtonHtml(
                    (t.config.translations || {}).action_delete_supplier || "Delete",
                    "dtActDel",
                    record.id,
                    "delete",
                    "go-del"
                ));
            }
        // }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        // dropdownId = "user-table-action-dropdown-" + record.id;

        // console.log(
        //     [
        //         '<div class="user-list-actions">',
        //         quickActions.join(""),
        //         '</div>'
        //     ].join("")
        // );

        return [
            '<div class="user-list-actions">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '">',
            this.getIcon(iconKey),
            '</button>'
        ].join("");
    };

    t.getRowActionState = function (record) {
        return {
            canEdit: t.hasPermission("SupplierEdit"),
            canDelete: t.hasPermission("SupplierDelete"),
            canView: t.hasPermission("SupplierView"),
        };
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.hasPermission = function (name) {
        return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
    };

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getIcon = function (key) {
        return (this.icons && this.icons[key]) ? this.icons[key]() : "";
    };

    t.icons = {
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

    t.import = function(e) {
        e.preventDefault();
        window.location = t.config.url.import_url;
    };

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
            $('#columnVisibilityControls').hide();
        }
    });

    t.mdl.on('hidden.bs.modal', function () {
        $('#preview-image').hide();
    });

    // t.mdl.on('show.bs.modal', function () {
    //     new bootstrap.Tab($('#basic-info-tab')[0]).show();
    // });

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            dTbl.columns.adjust();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        dTbl.columns.adjust();
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
    new MyApp(config);
});