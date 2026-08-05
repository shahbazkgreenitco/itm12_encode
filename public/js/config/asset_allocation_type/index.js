var AllocationType = function(config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.table = t.content.find("#mytable");
    
    t.page = $("#page_boxed");
    t.searchbox = t.table.find(".searchbox");

    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};

    t.offListen = false;
    var searchTimer;

    t.btn = {};
    t.mdl = $("section.content").find("#allocationtypeModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#allocationtype-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.forAction = t.mdl.frm.find("#for_action");
    t.mdl.frmEl.allocationtype_name = t.mdl.frm.find("#name");
    t.mdl.btn = {};
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.modal({
       backdrop: 'static',
       keyboard: false,
       show: false
    });

    t.tblHelpers = {
        actions: function() {
            return function(d) {            
                let buttons = '';

                    if (jQuery.inArray("AllocationTypeEdit", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="user-list-action-btn dtActEdit me-1" data-bs-toggle="tooltip" data-bs-title="${config.translations.edit}" data-placement="top" title="${config.translations.edit}" data-id="${d.a.id}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"/></svg>
                            </button>
                        `;
                    }                    
                    if (jQuery.inArray("AllocationTypeDelete", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="user-list-action-btn dtActDel me-1" data-bs-toggle="tooltip" data-bs-title="${config.translations.delete}" data-placement="top" title="${config.translations.delete}" data-id="${d.a.id}" >
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                            </button>
                        `;
                    }

                return buttons;
            };
        },
        alignRight: function() {
            return function(d) {
                if (d == "" || d == null) return null;
                return '<div class="text-right">' + d + '</div>';
            }
        }
    };

    //DataTable
    t.dTbl = t.table.DataTable({
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
        autoWidth: true,
        scrollY: true,
        scrollCollapse: true,
        dom: 'drtip',
        searching: false,
        pageLength: parseInt($(".list-page-length").val(), 10) || 10,
        aoColumnDefs: [{
            targets: 4,
            bSortable: false,
            className: "users-col-actions dtfc-fixed-right",
            render: t.tblHelpers.actions()
        }],
        order: [
            [3, 'desc']
        ],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.allocationtype_list,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search.value = $(".searchbox").val().trim();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].name;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        columns: [
            { 
                data: 'a.id',
                name: "id",
            },
            { 
                data: 'a.name',
                name: "name",
            },
            { 
                data: 'a.created_at_format',
                name: "created_at",
            },
            { 
                data: 'a.updated_at_format',
                name: "updated_at",
            },
            { 
                data: null
            }
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();

            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");

            var $pageLengthSelect = $(".list-page-length");
                $pageLengthSelect.on("change", function() {
                var newLength = parseInt($(this).val(), 10);
                t.dTbl.page.len(newLength).draw();
            });

            $(".list-page-length").find("select");
            $(".searchbox").on("keyup", function(e) {//cleaner version with debouncer
                let value = $(this).val().trim();
                let v = $(this).validate_str_param();
                if (e.key === "Enter" || e.which === 13 || value == "") {
                    if(v === false) {
                        sweetAlert('center', 'error', { 'msg': config.translations.please_enter_valid_value_for_search });
                        return false;
                    }

                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        t.dTbl.draw();
                    }, 400);
                }
            });
            t.btn.add = t.content.find(".btn-add");
            t.btn.search = t.content.find(".btn-searchbox");
            t.btn.reload = t.content.find(".btn-reload-list");
            t.btn.export = t.content.find(".btn-allocations-export");
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        }
    });

    // commenting since this function is not used.
    /* t.search = function (e) {
        var target = e.target || e.currentTarget;
            if (e.keyCode == 13 || $(this).is("span")) {
                var v = t.searchbox.validate_str_param();
                if (v === false) {
                    t.config.search = "";
                    alert("Please enter a valid value for search");
                    return false;
                }
                t.config.search = v;
                t.reload();
            }
            else if (target.tagName == "BUTTON") {
            t.reload();
        }
    }; */

    t.tableSearch = function(e) {
       e.preventDefault();
        let v = $(".searchbox").validate_str_param();
        if (v === false) {
            sweetAlert('center', 'error', { 'msg': config.translations.please_enter_valid_value_for_search });
            return false;
        }
        if (v === "") {
            t.reload();
        } else {
            t.dTbl.search(v).draw();
        }
    };
    t.reload = function () {
        t.dTbl.ajax.reload(null, false);
    };

    t.addAllocationType = function(e) {
       e.preventDefault();
       t.resetFrm();
       t.mdl.btnSubmit.prop("disabled", false).text(config.translations.create);
       t.mdl.find(".modal-title").text(config.translations.create_allocation_type);
       t.mdl.btnSubmit.text(config.translations.create);
       t.mdl.modal("show")
    };

    t.exportAllocationType = function(e) {
        e.preventDefault();
        let searchValue = $('.searchbox').val().trim();
        let filterData = JSON.stringify({ search: searchValue });
        let encodedFilter = btoa(filterData);
        window.location = config.url.download_url + "?q=" + encodeURIComponent(encodedFilter);
    }
    t.createOrUpdateAllocationType = function(e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.mdl.btnSubmit.prop("disabled", true).text(config.translations.please_wait);
        let id = t.mdl.frmEl.id.val();
        t.httpPostPath = id ? t.config.url.update : t.config.url.create;
        let formData = new FormData(t.mdl.frm[0]);
        $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        })
        .done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', {
                        'msg': data.msg || (id ? config.translations.allocation_type_update : config.translations.allocation_type_create)
                    });
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', {
                        'msg': data.msg || config.translations.error
                    });
                }
            }
        })

       .fail(function() {
            var errorData = {
                'msg': config.translations.something_went_wrong || 'Something went wrong. Please try again.'
            };
            sweetAlert('center', 'error', errorData);
        })

       .always(function() {
            t.mdl.btnSubmit.prop('disabled', false).text(id ? config.translations.update_allocation_types : config.translations.create);
        })

    };

    t.loadForm = function(data, forAction) {
        t.mdl.frmEl.id.val(data.id);
        t.mdl.frmEl.allocationtype_name.val(data.name);
        t.mdl.frmEl.forAction.val(forAction);
        
    };

    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        t.mdl.frmEl.id.val("");
        t.mdl.frmEl.forAction.val("");
        t.mdl.frmEl.allocationtype_name.val("").attr('disabled', false);
        t.frmValidator.resetForm();
    };

    t.editAllocationType = function(e) {
        e.preventDefault();
        var staId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit;

        $.get(t.httpPostPath + "/" + staId).done(function(data) {
            if (typeof data === "object" && data.status === "success") {
                t.resetFrm();
                t.mdl.btnSubmit.prop("disabled", false)
                t.mdl.title.html(config.translations.edit_allocation_type);
                t.mdl.btnSubmit.text(config.translations.update_allocation_types);
                t.mdl.frmEl.id.val(data.data.id);
                t.mdl.frmEl.allocationtype_name.val(data.data.name);
                t.mdl.frmEl.forAction.val("edit");
                 t.frmValidator.resetForm();
                t.mdl.modal("show");
            } else {
                Swal.fire({
                    icon: 'error',
                    text: data.msg || config.translations.failed_to_load_datas
                });
            }

        })
        .fail(function() {
            Swal.fire({
                icon: 'error',
                text: config.translations.error
            });
        });
    };

    t.deleteAllocationType = function(e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + Id;
        Swal.fire({
            title: config.translations.delete_record,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: config.translations.yes_delete
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: t.httpPostPath,
                    type: "GET",
                    data: { _token: t.config.token }
                })
                .done(function(data) {

                    if (data.status === "success") {
                        Swal.fire({
                            icon: "success",
                            text: data.msg || config.translations.allocation_type_Delete,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        t.dTbl.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            icon: "error",
                            text: data.msg || config.translations.delete_failed
                        });
                    }
                })
                .fail(function() {
                    Swal.fire({
                        icon: "error",
                        text: config.translations.error
                    });
                });
            }
        });
    };

    $.validator.addMethod("clean_text_only", function(value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
    }, config.translations.letters_numbers_allowed);


    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: [],
        rules: {
            name: {
                required: true,
                clean_text_only: true,
                maxlength: 100,
            }
        },
        messages: {
            name: {
                required: config.translations.required,
                clean_text_only: config.translations.clean_text_only,
                maxlength: config.translations.maxlength,
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
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

    t.btn.add.on("click", $.proxy(t.addAllocationType ));
    t.btn.search.on("click", $.proxy(t.tableSearch));
    t.btn.reload.on("click", $.proxy(t.reload));
    t.table.on("click", ".dtActEdit", $.proxy(t.editAllocationType));
    t.table.on("click", ".dtActDel", $.proxy(t.deleteAllocationType));
    t.mdl.btnSubmit.on("click", $.proxy(t.createOrUpdateAllocationType));
    t.btn.export.on("click", $.proxy(t.exportAllocationType));
    t.dTbl.ajax.reload();
 };
