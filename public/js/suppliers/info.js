var AddressPhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main.main-content');
    t.reloadTableName = null;

    t.mdl = $('#addAddressModal');
    t.frm = t.mdl.find('#AddAddressForm');
    t.frm.title = t.frm.find('.modal-title');
    t.frm.address = t.frm.find('#add_address');
    t.frm.address2 = t.frm.find('#add_address2');
    t.frm.city_id = t.frm.find('#add_city_id');
    t.frm.state_id = t.frm.find('#add_state_id');
    t.frm.country_id = t.frm.find('#add_country_id');
    t.frm.phone = t.frm.find('#add_phone');
    t.frm.fax = t.frm.find('#fax');
    t.frm.zip = t.frm.find('#add_zip');

    t.panTable = t.content.find('#panTable');
    t.gstTable = t.content.find('#gstTable');
    t.addressTable = t.content.find('#addressTable');
    t.accountTable = t.content.find('#accountTable');

    t.mdlItem = $('#addItemModal');
    t.frmItem = t.mdlItem.find('#addItemFrm');
    t.frmItem.item_value = t.frmItem.find('#item_value');
    t.frmItem.item_type_value = t.frmItem.find('#item_type_value');

    t.resetAccountFrm = {};
    t.accountMdl = $('#addSupplierAccountModal');
    t.accountForm = t.accountMdl.find("#AddSupplierAccountForm");
    t.resetAccountFrm.bank_acc_number = t.accountMdl.find('#bank_acc_number');
    t.resetAccountFrm.bank_name = t.accountMdl.find('#bank_name');
    t.resetAccountFrm.bank_ifsc = t.accountMdl.find('#bank_ifsc');
    t.resetAccountFrm.bank_branch = t.accountMdl.find('#bank_branch');
    t.resetAccountFrm.bank_acc_name = t.accountMdl.find('#bank_acc_name');
    t.resetAccountFrm.notes = t.accountMdl.find('#notes');
    t.resetAccountFrm.url = t.accountMdl.find('#url');
    t.resetAccountFrm.list = t.accountMdl.find("#supplier-image-list");
    
    t.supplierMdl = $("#addSupplierModal");
    t.supFrm = t.supplierMdl.find("#SupplierForm");
    t.supFrm.title = t.supFrm.find(".modal-title");
    t.supFrm.name = t.supFrm.find("#name");
    t.supFrm.business_category = t.supFrm.find("#business_category");
    t.supFrm.contact = t.supFrm.find("#contact");
    t.supFrm.address = t.supFrm.find("#address");
    t.supFrm.address2 = t.supFrm.find("#address2");
    t.supFrm.country_id = t.supFrm.find("#country_id");
    t.supFrm.state_id = t.supFrm.find("#state_id");
    t.supFrm.city_id = t.supFrm.find("#city_id");
    t.supFrm.zip = t.supFrm.find("#zip");
    t.supFrm.phone = t.supFrm.find("#phone");
    t.supFrm.email = t.supFrm.find("#email");
    t.supFrm.pan = t.supFrm.find("#pan");
    t.supFrm.bank_acc_number = t.supFrm.find("#bank_acc_number");
    t.supFrm.bank_name = t.supFrm.find("#bank_name");
    t.supFrm.bank_ifsc = t.supFrm.find("#bank_ifsc");
    t.supFrm.bank_branch = t.supFrm.find("#bank_branch");
    t.supFrm.bank_acc_name = t.supFrm.find("#bank_acc_name");
    t.supFrm.url = t.supFrm.find("#url");
    t.supFrm.notes = t.supFrm.find("#notes");
    t.supFrm.image = t.supFrm.find("input[name='image']");
    t.mdlCourier = $('#courier_info_modal');

    t.btn = {};
    t.btn.submit = t.mdl.find('#btnAddressSubmit');
    t.btn.accountSubmit = t.accountMdl.find('#btnAccountSubmit');
    // t.btn.editAddress = t.mdl.find('.btn-address-edit');
    t.btn.submitItem = t.mdlItem.find('#btnSubmitItem');
    var supplierId = t.config.supplier_id;

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    var select2Opts = { width: "100%" };
    t.frm.country_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        ajax: {
            url: config.url.country,
            dataType: "json",
            data: function (p) {
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
            data: function (p) {
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
        dropdownParent: t.frm.city_id.parent(),
        ajax: {
            url: config.url.city,
            dataType: "json",
            data: function (p) {
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

    t.handleSubmit = function (e, form) {
        e.preventDefault();

         form = form || t.frm;  

        if (form.attr("id") === "addItemFrm") {
            if (t.frmItemValidator && !t.frmItemValidator.form()) {
                return false;
            }
        } else {
            if (t.frmValidator && !t.frmValidator.form()) {
                return false;
            }
        }
        t.httpCall = false;
        var formData = new FormData(form[0]);
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
                        Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: data.msg,
                        confirmButtonColor: '#5bd810',
                        showCloseButton: true,
                        allowOutsideClick: false
                    }).then(() => {
                        // window.location.reload();
                        if (t.reloadTableName && t.reloadTableName.ajax && typeof t.reloadTableName.ajax.reload === 'function') {
                            t.reloadTableName.ajax.reload(null, false);
                        }
                    });
                    t.mdl.modal("hide");
                    t.mdlItem.modal("hide");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.accountFrmValidator = t.accountForm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            bank_acc_number: {
                digits: true,
                minlength: 10,
            },
            bank_name: {
                required: true,
                str_name: true,
                clean_text_only: true,
            },
            bank_ifsc: {
                required: true,
                str_name: true,
                clean_text_only: true
            },
            bank_branch: {
                required: true,
                str_name: true,
                clean_text_only: true
            },
            bank_acc_name: {
                required: true,
                str_name: true,
                clean_text_only: true
            },
            url: {
                url: true
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

    t.handleAccountSubmit = function (e) {
        e.preventDefault();

        var formData = new FormData(t.accountForm[0]);
        formData.append('_token', config.token);

        if (t.accountFrmValidator && !t.accountFrmValidator.form()) {
            return false;
        }

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
                    sweetAlert('center', 'success', data);
                    t.accountMdl.modal("hide");
                    accountDTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    }
    t.handleSupplierSubmit = function (e) {
        e.preventDefault();
        
        var formData = new FormData(t.supFrm[0]);
        formData.append('_token', config.token);
        
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.supplierMdl.modal("hide");
                    location.reload(); // Reload to show updated data
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    // Add this event binding at the end of your AddressPhase function
    t.supplierMdl.find('#btnSubmit').on("click", $.proxy(t.handleSupplierSubmit, t));

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            address:   { 
                required: true,
                clean_text_only: true,
                str_name: true 
            },
            address2:  { 
                str_name: true,
                clean_text_only: true 
            },
            city_id:   { 
                required: true, 
                str_name: true 
            },
            state_id:  { 
                required: true, 
                str_name: true 
            },
            country_id:{ 
                required: true, 
                str_name: true 
            },
            phone:     { 
                number: true 
            },
            fax: {
                str_name: true 
            },
            zip:{ 
                number: true 
            }
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

    t.addAddress = function (e) {
        e.preventDefault();
        t.mdlItem.modal("hide");
        t.httpPostPath = config.url.addAddress + "/" + config.supplier_id;
        t.mdl.modal("show");
        t.frm.title.text(config.translations.add_address_details);
        t.resetFrm();
        t.reloadTableName = addressDtbl;
    };

    t.addAccount = function (e) {
        e.preventDefault();
        t.mdlItem.modal("hide");
        t.httpPostPath = config.url.addAccount + "/" + config.supplier_id;
        t.accountMdl.modal("show");
        t.accountMdl.find('.modal-title').text(config.translations.add_account_details);
        t.accountFormReset();
    }

    t.editSupplierSubmit = function (e) {
        e.preventDefault();
        var supplierId = $(this).attr("data-id");
        t.httpPostPath = config.url.edit + "/" + supplierId;
        t.resetSupplierForm();
        var http = $.get(config.url.getInfo + "/" + supplierId);
        http.done(function(data) {
            if (typeof data == "object" && data.status == "success") {
                t.supplierMdl.modal('show');
                t.supFrm.title.text(config.translations.edit_supplier_details);
                t.loadSupplierForm(data.data); 
            } else {
                sweetAlert('center', 'error', data.msg || config.translations.error_loading_supplier);
            }
        });
        http.fail(function () {
            alert(config.translations.something_went_wrong_check_details_are_correct);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    var panDTbl = t.panTable.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        order: [[4, 'desc']],
        // order: [[10, 'desc']],
        colResize: { resizeTable: true },
        ajax: {
            url: `${config.url.panList}/${supplierId}`,
            type: "get",
            data: function (d) {
                d._token = config.token;
                d.search = $("#panSearch").validate_str_param();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            },
            dataSrc: function (json) {
                return json.data.pan_list;
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: 'is_primary',
                render: function (data, type, row) {
                    return parseInt(row.is_primary) === 1 ? config.translations.primary : config.translations.secondary;
                }
            },
            { data: 'item_value' },
            { data: 'user_name' },
            { data: 'updated_at', searchable: false },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCellPan(row || {}, type); }
            },
            // {
            //     data: null,
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     },
            // }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#panSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    panDTbl.reload = function () {
        this.ajax.reload(null, false);
    };

    var gstDTbl = t.gstTable.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        order: [[4, 'desc']],
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        // order: [[10, 'desc']],
        colResize: { resizeTable: true },
        ajax: {
            url: `${config.url.gstList}/${supplierId}`,
            type: "get",
            data: function (d) {
                d._token = config.token;
                d.search = $("#gstSearch").validate_str_param();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            },
            dataSrc: function (json) {
                return json.data.gst_list
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: 'is_primary',
                render: function (data, type, row) {
                    return parseInt(row.is_primary) === 1 ? config.translations.primary : config.translations.secondary;
                }
            },
            { data: 'item_value' },
            { data: 'user_name' },
            { data: 'updated_at', searchable: false },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCellGst(row || {}, type); }
            },            // {
            //     data: null,
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     },
            // }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#gstSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    gstDTbl.reload = function () {
        this.ajax.reload(null, false);
    };

    var addressDtbl = t.addressTable.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        order: [[11, 'desc']],
        colResize: { resizeTable: true },
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        ajax: {
            url: `${config.url.addressList}/${supplierId}`,
            type: "get",
            data: function (d) {
                d._token = config.token;
                d.search = $("#addressSearch").validate_str_param();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].name || d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            },
            dataSrc: function (json) {
                return json.data.address
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: 'is_primary',
                render: function (data, type, row) {
                    return parseInt(row.is_primary) === 1 ? config.translations.primary : config.translations.secondary;
                }
            },
            { data: 'address' },
            { data: 'address2' },
            { data: 'country' },
            { data: 'state' },
            { data: 'city' },
            { data: 'zip' },
            { data: 'phone' },
            { data: 'user_id' },
            { data: 'fax' },
            { data: 'updated_at_formatted', name: "updated_at", searchable: false },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCellAddress(row || {}, type); }
            },            // {
            //     data: null,
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     },
            // }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#addressSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    addressDtbl.reload = function () {
        this.ajax.reload(null, false);
    };

    var accountDTbl = t.accountTable.DataTable({
        processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        lengthChange: false,
        scrollX: true,
        scrollCollapse: true,
        pageLength: 10,
        order: [[9, 'desc']],
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        fixedColumns: {
            rightColumns: 1
        },
        // order: [[10, 'desc']],
        colResize: { resizeTable: true },
        ajax: {
            url: `${config.url.getAccountList}/${supplierId}`,
            type: "get",
            data: function (d) {
                d._token = config.token;
                d.search = $("#gstSearch").validate_str_param();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].name || d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            },
            dataSrc: function (json) {
                return json.data.address
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                },
            },
            {
                data: 'is_primary',
                render: function (data, type, row) {
                    return parseInt(row.is_primary) === 1 ? config.translations.primary : config.translations.secondary;
                }
            },
            { data: 'bank_acc_number' },
            { data: 'bank_name' },
            { data: 'bank_ifsc' },
            { data: 'bank_branch' },
            { data: 'bank_acc_name' },
            { data: 'url' },
            { data: 'notes' },
            { data: 'updated_at_formatted', name: "updated_at", searchable: false },
            {
                data: "id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCellAccount(row || {}, type); }
            },            // {
            //     data: null,
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     },
            // }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#addresssSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    accountDTbl.reload = function () {
        this.ajax.reload(null, false);
    };

    t.renderActionsCellPan = function (record, type) {
        var t = this;
        var quickActions = [];
        if (type !== "display") return "";
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_view_supplier || "View",
            "dtActDel",
            record.id,
            "view",
            "primary-pan",
            'data-number-type="pan"'
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || "Edit",
            "dtActEdit",
            record.id,
            "edit",
            "number-edit",
            `data-original-title="${config.translations.edit_pan}" data-item_type="pan" aria-label="${config.translations.edit_pan}" data-bs-original-title="${config.translations.edit_pan}"`
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || "Delete",
            "dtActDel",
            record.id,
            "delete",
            "remove-number",
            `data-number-type="pan"`
        ));
        // }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions hidden-icon-control d-flex justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }
    t.renderActionsCellAddress = function (record, type) {
        var t = this;
        var quickActions = [];
        if (type !== "display") return "";
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_view_supplier || "View",
            "dtActDel",
            record.id,
            "view",
            "primary-address"
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || "Edit",
            "dtActEdit",
            record.id,
            "edit",
            "btn-address-edit"
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || "Delete",
            "dtActDel",
            record.id,
            "delete",
            "go-address-delete"
        ));
        // }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions d-flex justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.renderActionsCellGst = function (record, type) {
        var t = this;
        var quickActions = [];
        if (type !== "display") return "";
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_view_supplier || "View",
            "dtActDel",
            record.id,
            "view",
            "primary-gst",
            'data-number-type="gst"'
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || "Edit",
            "dtActEdit",
            record.id,
            "edit",
            "number-edit",
            `data-original-title="${config.translations.edit_gst}" data-item_type="gstin" aria-label="${config.translations.edit_gst}" data-bs-original-title="${config.translations.edit_gst}"`
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || "Delete",
            "dtActDel",
            record.id,
            "delete",
            "remove-number",
            'data-number-type="gstin"'
        ));
        // }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions hidden-icon-control d-flex justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.renderActionsCellAccount = function (record, type) {
        var t = this;
        var quickActions = [];
        if (type !== "display") return "";
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_view_supplier || "View",
            "dtActDel",
            record.id,
            "view",
            "primary-account"
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || "Edit",
            "dtActEdit",
            record.id,
            "edit",
            "go-account",
            `data-original-title="${config.translations.edit_account}" data-item_type="account" aria-label="${config.translations.edit_account}" data-bs-original-title="${config.translations.edit_account}"`
        ));
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || "Delete",
            "dtActDel",
            record.id,
            "delete",
            "remove-account"
        ));
        // }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions hidden-icon-control d-flex justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass, extraAttributes = null) {
        return [
            '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '"', extraAttributes, '> ',
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

    t.reload = function (tableInstance) {
        if (tableInstance && $.isFunction(tableInstance.ajax.reload)) {
            tableInstance.ajax.reload(null, false);
        }
    };

    t.loadSupplierForm = function (obj) {
        t.supFrm.name.val(obj.supplier.name);

        // Business categories - same as index.js
        if (obj.business_categories_name && obj.supplier.business_categories) {
            let ids = obj.supplier.business_categories.split(",");
            obj.business_categories_name.forEach(function(cat) {
                if (ids.includes(cat.id.toString())) {
                    let option = new Option(cat.business_tag, cat.id, true, true);
                    t.supFrm.business_category.append(option);
                }
            });
            t.supFrm.business_category.trigger('change');
        } else {
            t.supFrm.business_category.val(null).trigger('change');
        }

        // Country, State, City - use supplier object
        if (obj.supplier.country_id && obj.supplier.country_name) {
            const option = new Option(obj.supplier.country_name, obj.supplier.country_id, true, true);
            t.supFrm.country_id.append(option).trigger('change');
        }

        if (obj.supplier.state_id && obj.supplier.state_name) {
            const option = new Option(obj.supplier.state_name, obj.supplier.state_id, true, true);
            t.supFrm.state_id.append(option).trigger('change');
        }

        if (obj.supplier.city_id && obj.supplier.city_name) {
            const option = new Option(obj.supplier.city_name, obj.supplier.city_id, true, true);
            t.supFrm.city_id.append(option).trigger('change');
        }

        // Fill other fields - use supplier object
        t.supFrm.contact.val(obj.supplier.contact);
        t.supFrm.address.val(obj.supplier.address);
        t.supFrm.address2.val(obj.supplier.address2);
        t.supFrm.zip.val(obj.supplier.zip);
        t.supFrm.phone.val(obj.supplier.phone);
        t.supFrm.email.val(obj.supplier.email);
        t.supFrm.pan.val(obj.supplier.pan);
        t.supFrm.bank_acc_number.val(obj.supplier.bank_acc_number);
        t.supFrm.bank_name.val(obj.supplier.bank_name);
        t.supFrm.bank_ifsc.val(obj.supplier.bank_ifsc);
        t.supFrm.bank_branch.val(obj.supplier.bank_branch);
        t.supFrm.bank_acc_name.val(obj.supplier.bank_acc_name);
        t.supFrm.url.val(obj.supplier.url);
        t.supFrm.notes.val(obj.supplier.notes);
    };

    t.resetSupplierForm = function() {
        // Clear all form fields
        t.supFrm.name.val('');
        t.supFrm.contact.val('');
        t.supFrm.address.val('');
        t.supFrm.address2.val('');
        t.supFrm.zip.val('');
        t.supFrm.phone.val('');
        t.supFrm.email.val('');
        t.supFrm.pan.val('');
        t.supFrm.bank_acc_number.val('');
        t.supFrm.bank_name.val('');
        t.supFrm.bank_ifsc.val('');
        t.supFrm.bank_branch.val('');
        t.supFrm.bank_acc_name.val('');
        t.supFrm.url.val('');
        t.supFrm.notes.val('');

        // Clear select2 dropdowns
        t.supFrm.business_category.empty().trigger('change');
        t.supFrm.country_id.empty().trigger('change');
        t.supFrm.state_id.empty().trigger('change');
        t.supFrm.city_id.empty().trigger('change');
    };

    var select2Opts = { width: "100%" };
    t.supFrm.country_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.supFrm.country_id.parent(),
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

    t.supFrm.state_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.supFrm.state_id.parent(),
        ajax: {
            url: config.url.state,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    country_id: t.supFrm.country_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_state
    }));

    t.supFrm.city_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.supFrm.city_id.parent(),
        ajax: {
            url: config.url.city,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    state_id: t.supFrm.state_id.val(),
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_city
    }));

    t.supFrm.country_id.on('change', function () {
        t.supFrm.state_id.val(null).trigger('change');
        t.supFrm.state_id.empty();

        t.supFrm.city_id.val(null).trigger('change');
        t.supFrm.city_id.empty();
    });

    t.supFrm.state_id.on('change', function () {
        t.supFrm.city_id.val(null).trigger('change');
        t.supFrm.city_id.empty();
    });

     t.supFrm.business_category.select2($.extend({}, select2Opts, {
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
        placeholder: config.translations.select_the_business_category,
        templateSelection: function (data, container) {
            if (container) {
                $(container).attr('title', data.text);
            }
            return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
        }
    }));

    t.addItems = function (e) {
        e.preventDefault();
        t.mdl.modal("hide");

        let $btn = $(e.currentTarget);

        let itemType = $btn.data("item_type");  
        t.httpPostPath = t.config.url.supplier + "/" + itemType + "/add/" + supplierId;
        

        // Set form action and hidden field
        $("#addItemFrm").attr("action", t.httpPostPath);
        $("#item_type").val(itemType);

        // Update modal UI text
        $("#modalTitle").text(config.translations.add_new + " " + itemType.toUpperCase());
        let requiredSpan = `<span class="required">*</span>`;
        $("#itemLabel").html(itemType.toUpperCase() + requiredSpan);
        $("#item_value").attr("placeholder", config.translations.enter + " " + itemType.toUpperCase());

        t.resetItemFrm();
        // Show modal
        t.mdlItem.modal("show");
        if (itemType == "pan") {
            t.reloadTableName = panDTbl;
        } else {
            t.reloadTableName = gstDTbl;
        }
    };

    t.editItems = function (e, data) {
        e.preventDefault();
        t.mdl.modal("hide");

        let $btn = $(e.currentTarget);

        let itemType = $btn.data("item_type");
        t.httpPostPath = t.config.url.supplier + "/" + itemType + "/edit/" + supplierId;


        // Set form action and hidden field
        $("#addItemFrm").attr("action", t.httpPostPath);
        $("#item_type").val(itemType);

        // Update modal UI text
        $("#modalTitle").text(config.translations.edit_new + " " + itemType.toUpperCase());
        $("#itemLabel").text(itemType.toUpperCase());
        $("#item_value").attr("placeholder", config.translations.enter + " " + itemType.toUpperCase());
        t.resetItemFrm();
        if (itemType == "pan") {
            t.reloadTableName = panDTbl;
        } else {
            t.reloadTableName = gstDTbl;
        }

        t.loadNumberForm(e);
    };

    t.editAddressSubmit = function (e) {
        e.preventDefault();
        let addressId = $(e.currentTarget).data("id");  
        t.httpPostPath = config.url.editAddress + "/" + supplierId + "/" + addressId;
        var http = $.get(config.url.editAddress + "/" + supplierId + "/" + addressId);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.mdl.modal("show");
                    t.frm.title.text(config.translations.edit_address_details);
                    t.loadForm(data);
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

    };

    t.makePrimaryNumber = function (e) {
        e.preventDefault();
        let numberId = $(e.currentTarget).data("id");
        let numberType = $(e.currentTarget).attr('data-number-type');
        var http = $.get(config.url.makePrimaryNumber + "/" + supplierId + "/" + numberId);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    var data = {
                        'msg': config.translations.marked_as_primary_successfully,
                    };
                    sweetAlert('center', 'success', data);
                    if (numberType == "pan") {
                        panDTbl.ajax.reload();
                    } else {
                        gstDTbl.ajax.reload();
                    }
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

    t.makePrimaryAddress = function (e) {
        e.preventDefault();
        let address = $(e.currentTarget).data("id");
        var http = $.get(config.url.makePrimaryAddress + "/" + supplierId + "/" + address);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    var data = {
                        'msg': config.translations.address_marked_as_primary_successfully,
                    };
                    sweetAlert('center', 'success', data);
                    addressDtbl.ajax.reload();
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

    t.makePrimaryAccount = function (e) {
        e.preventDefault();
        let accountId = $(e.currentTarget).data("id");
        var http = $.get(config.url.makePrimaryAccount + "/" + supplierId + "/" + accountId);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    var data = {
                        'msg': config.translations.account_marked_as_primary_successfully,
                    };
                    sweetAlert('center', 'success', data);
                    accountDTbl.ajax.reload();
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

    t.editAccountSubmit = function (e) {
        e.preventDefault();

        let accountId = $(e.currentTarget).data("id");

        t.httpPostPath = config.url.editAccount + "/" + supplierId + "/" + accountId;

        var http = $.get(t.httpPostPath);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    t.accountMdl.modal("show");
                    t.accountMdl.find('.modal-title').text(config.translations.edit_account_details);
                    t.loadAccountForm(data);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });

        http.fail(function () {
            var data = {
                msg: config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });

        http.always(function () {
            t.httpCall = true;
        });
    };

    t.loadAccountForm = function (data) {
        t.accountFormReset();

        if (!data.account) {
            console.error(config.translations.no_account_data_found);
            return;
        }

        let account = data.account;

        t.resetAccountFrm.bank_acc_number.val(account.bank_acc_number || "");
        t.resetAccountFrm.bank_name.val(account.bank_name || "");
        t.resetAccountFrm.bank_ifsc.val(account.bank_ifsc || "");
        t.resetAccountFrm.bank_branch.val(account.bank_branch || "");
        t.resetAccountFrm.bank_acc_name.val(account.bank_acc_name || "");
        t.resetAccountFrm.url.val(account.url || "");
        t.resetAccountFrm.notes.val(account.notes || "");
        t.resetAccountFrm.list.html("");
        if (data.attachments && data.attachments.length) {
            data.attachments.forEach(function (attachment) {
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

                t.resetAccountFrm.list.append($li);
            });
        } else {
            t.resetAccountFrm.list.append("<li class='list-group-item'>No files uploaded</li>");
        }
        $('#preview-image').show();
    };

    // load number form for pan and gst edit
    t.loadNumberForm = function (e) {
        e.preventDefault();
        let numberId = $(e.currentTarget).data("id");
        t.httpPostPath = config.url.getNumberDetails + "/" + supplierId + "/" + numberId;
        var http = $.get(t.httpPostPath);

        http.done(function (data) {
            if (typeof data === "object") {
                if (data.status === "success") {
                    if (!data.number) {
                        console.error(config.translations.no_number_data_found);
                        return;
                    }

                    let number = data.number;
                    t.frmItem.item_value.val(number.item_value);
                    t.mdlItem.modal("show");
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

    t.loadForm = function (data) {
        t.resetFrm();
        if (!data.address) {
            console.error(config.translations.no_address_data_found);
            return;
        }

        let address = data.address;

        t.frm.address.val(address.address || "");
        t.frm.address2.val(address.address2 || "");
        t.frm.zip.val(address.zip || "");
        t.frm.phone.val(address.phone || "");
        t.frm.fax.val(address.fax || "");

        let country = data.country[0];
        let state = data.state[0];
        let city = data.city[0];
        if (country && country.id && country.name) {
            t.frm.country_id.append(new Option(country.name, country.id, true, true)).trigger("change");
        }
        if (data.state && data.state[0] && data.state[0].id && data.state[0].name) {
            t.frm.state_id.append(new Option(data.state[0].name, data.state[0].id, true, true)).trigger("change.select2");
        }
        if (data.city && data.city[0] && data.city[0].id && data.city[0].name) {
            t.frm.city_id.append(new Option(data.city[0].name, data.city[0].id, true, true)).trigger("change.select2");
        }
    };

    t.loadNumberFormData = function (data) {
        t.resetItemFrm();
        if (!data.number) {
            console.error(config.translations.no_number_data_found);
            return;
        }

        let number = data.number;
        t.frmItem.item_value.val(number.item_value);

    }


    t.resetFrm = function () {
        t.frm.address.val('');
        t.frm.address2.val('');
        t.frm.zip.val('');
        t.frm.fax.val('');
        t.frm.phone.val('');
        t.frm.country_id.val(null).trigger('change');
        t.frm.state_id.val(null).trigger('change');
        t.frm.city_id.val(null).trigger('change');
    };

    t.accountFormReset = function () {
        t.resetAccountFrm.bank_acc_number.val('');
        t.resetAccountFrm.bank_name.val('');
        t.resetAccountFrm.bank_ifsc.val('');
        t.resetAccountFrm.bank_branch.val('');
        t.resetAccountFrm.bank_acc_name.val('');
        t.resetAccountFrm.url.val('');
    }

    t.frmItemValidator = t.frmItem.validate({
        errorClass: 'error',
        rules: {
            item_value: {
                required: true,
                str_name: true
            }
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

    
    t.resetItemFrm = function () {
        t.frmItem.item_value.val('');
    };


    t.removeNumber = function (e) {
        e.preventDefault();
        let addressId = $(e.currentTarget).data("id");
        let numberType = $(e.currentTarget).data('number-type');
        var url = config.url.deleteNumber + "/" + supplierId + "/" + addressId;
        let redirectUrl = config.url.info + "/" + supplierId;
        let reloadDataTable = gstDTbl;
        if (numberType == "pan") {
            reloadDataTable = panDTbl;
        }

        sweetAlerts(config.translations.are_you_delete_number, 'warning', url, reloadDataTable, { 'msg': t.config.translations.something_went_wrong }, 'reload');
    };

    t.goAccountDelete = function (e) {
        e.preventDefault();

        let accountId = $(e.currentTarget).data("id");

        t.httpPostPath = config.url.deleteAccount + "/" + supplierId + "/" + accountId;
        let redirectUrl = config.url.info + "/" + supplierId;
        sweetAlerts(config.translations.are_you_delete_account, 'warning', t.httpPostPath, accountDTbl, { msg: t.config.translations.something_went_wrong }, 'reload');
        // sweetAlerts(config.translations.are_you_delete_account, 'warning', t.httpPostPath, t, { msg: t.config.translations.something_went_wrong }, 'url_yes');
        // accountDTbl.ajax.reload();
    };

    t.goAddressDelete = function (e) {
        e.preventDefault();
        let addressId = $(e.currentTarget).data("id");  
        e.preventDefault();
        t.httpPostPath = config.url.deleteAddress + "/" + supplierId + "/" + addressId;
        let redirectUrl = config.url.info + "/" + supplierId;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete_address, 'warning', t.httpPostPath, addressDtbl, { 'msg': t.config.translations.something_went_wrong }, 'reload');

    };

    t.deleteLink = function (e) {
        e.preventDefault();
        var url = config.url.delete + "/" + supplierId;
        var data = { 'msg': config.translations.something_went_wrong };
        var dTbl = config.url.list;
        sweetAlerts(config.translations.are_you_delete, 'warning', url, dTbl, data, 'url_yes');
    };

    t.btn.submit.on("click", $.proxy(t.handleSubmit, t));
    t.btn.accountSubmit.on("click", $.proxy(t.handleAccountSubmit, t));
    $(document).on("click", ".go-add-address", $.proxy(t.addAddress, t));
    $(document).on("click", ".btn-add-pan", $.proxy(t.addItems, t));
    $(document).on("click", ".btn-add-gstin", $.proxy(t.addItems, t));
    $(document).on("click", ".btn-address-edit", t.editAddressSubmit);
    $(document).on("click", ".btn-supplier-edit", t.editSupplierSubmit);
    $(document).on("click", ".number-edit", $.proxy(t.editItems, t));
    $(document).on("click", ".go-add-account", $.proxy(t.addAccount, t));
    $(document).on("click", ".go-account", $.proxy(t.editAccountSubmit, t));
    $(document).on("click", ".remove-account", $.proxy(t.goAccountDelete, t));
    $(document).on("click", ".primary-pan", $.proxy(t.makePrimaryNumber, t));
    $(document).on("click", ".primary-gst", $.proxy(t.makePrimaryNumber, t));
    $(document).on("click", ".primary-account", $.proxy(t.makePrimaryAccount, t));
    $(document).on("click", ".primary-address", $.proxy(t.makePrimaryAddress, t));
    $(document).on("click", ".btn-reload-list", function () {
        let tableName = $(this).data('table');
        if (tableName === 'pan') t.reload(panDTbl);
        if (tableName === 'gst') t.reload(gstDTbl);
        if (tableName === 'address') t.reload(addressDtbl);
        if (tableName === 'accounts') t.reload(accountDTbl);
    });
    t.btn.submitItem.on("click", function (e) {
        e.preventDefault();
        t.handleSubmit(e, t.frmItem);
    });

    $(document).on("click", ".hidden-icon-control .remove-number", $.proxy(t.removeNumber, t));
    $(document).on("click", ".go-address-delete", $.proxy(t.goAddressDelete, t));
    // $(document).on("click", ".del-link", $.proxy(t.addImage, t));
    $(document).on("click", ".delete-link", $.proxy(t.deleteLink, t));
    t.supplierMdl.on('hidden.bs.modal', function () {
        t.resetSupplierForm();
    });

    $(document).on('click', '.copy-text-basic-info', function () {
        const text = $(this).data('copy-text') || '';

        navigator.clipboard.writeText(text).then(() => {
            console.log('Copied!');
        }).catch(() => {
            console.error('Copy failed');
        });
    });

    function adjustVisibleTables() {
        $('.tab-pane.active.show table.dataTable:visible').each(function () {
            const dt = $(this).DataTable();
            if (dt && dt.columns && typeof dt.columns.adjust === 'function') {
                dt.columns.adjust();
            }
        });
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
                    success: function (response) {
                        if (response.status === 'success') {
                            $currentLi.fadeOut(300, function () {
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
                    error: function () {
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

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            adjustVisibleTables();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        adjustVisibleTables();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        setTimeout(function () {
            adjustVisibleTables();
        }, 100);
    });
};