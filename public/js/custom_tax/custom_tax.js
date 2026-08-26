var TicketTrigger = function (config) {
    var t = this;
    t.config = config;

    var el = {};
    t.content = $("#custom-tax-list-wrapper");
    el.table = t.content.find("#mytable");

    el.mdl = $("#custom_tax_mdl");
    el.mdl.frm = $("#custom_tax_form");
    el.mdl.btnSubmit = $("#btnSubmit");
    el.mdl.title = el.mdl.find(".modal-title");

    el.btn = {
        reload: ".btn-reload-list",
        add: ".open-add-modal",
    };

    t.dTable = null;
    t.submitUrl = "";

    t.initDataTable = function () {
        let defaultLength = parseInt($(".user-list-page-length").val()) || 10;
        t.dTable = el.table.DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            scrollX: true,
            scrollCollapse: true,
            dom: "lrtip",
            order: [[0, "desc"]],
            pageLength: defaultLength,
            lengthChange: false,
            searchDelay: 400,
            deferRender: true,
            fixedColumns: {
                rightColumns: 1,
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
                search: t.config.translations.search_config || "Search:",
                lengthMenu: t.config.translations.length_menu || "Show _MENU_ entries"
            },
            ajax: {
                url: t.config.url.getCustomTax,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                },
                error: function (xhr) {
                    console.error("DataTable Error:", xhr.responseText);
                },
            },
            columns: [
                {
                    data: null,
                    // orderable: true,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        if (type !== "display") return data;

                        let index = meta.row + meta.settings._iDisplayStart + 1;

                        return `<span class="opacity-60">${index}</span>`;
                    },
                },
                { data: "tax_name", name: "tax_name"},
                {
                    data: "created_at",
                    name: "created_at",
                },
                {
                    data: "updated_at",
                    name: "updated_at",
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (id) {
                        return t.buildActions(id);
                    },
                },
            ],
            drawCallback: function (settings) {
                var $wrapper = $(this).closest('.dataTables_wrapper');

                $wrapper.find('.dataTables_info').css({
                    'float': 'left',
                    'margin-top': '15px'
                });

                $(this).find('[data-bs-toggle="tooltip"]').each(function () {
                    var existing = bootstrap.Tooltip.getInstance(this);
                    if (existing) existing.dispose();
                    new bootstrap.Tooltip(this);
                });
            },
            fnInitComplete: function () {
                var api = this.api();

                $("#user-list-search")
                    .off("keyup")
                    .on("keyup", function (e) {
                        if (e.key === "Enter" || e.keyCode === 13) {
                            api.search(this.value).draw();
                        }
                    });

                $(".amg-list-searchbar__icon")
                    .off("click")
                    .on("click", function () {
                        api.search($("#user-list-search").val()).draw();
                    });
            },
            
        });
    };

   t.buildActions = function (id) {
    var t = this;

    let actions = [];

    if (t.hasPermission("CustomTaxEdit")) {
      actions.push(`
                <button class="amg-action-btn primary edit" data-id="${id}" data-bs-toggle="tooltip"
                   data-bs-original-title="${t.config.translations.Edit}">
                    ${t.getIcon("edit")}
                </button>
            `);
    }

    if (t.hasPermission("CustomTaxDelete")) {
      actions.push(`
                <button class="amg-action-btn delete"
                    data-id="${id}" data-bs-toggle="tooltip"
                   data-bs-original-title="${t.config.translations.Delete}">
                     ${t.getIcon("delete")}
                </button>
            `);
    }

    return `
            <div class="amg-datatable-actions d-flex justify-content-start gap-2">
            ${actions.join("")}
            </div>
        `;
  };  

  t.getIcon = function (key) {
    return t.icons[key] ? t.icons[key]() : "";
  };

  t.icons = {
    edit: function () {
      return '<svg viewBox="0 0 16 16" height="16" width="16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>';
    },
    delete: function () {
      return '<svg viewBox="0 0 15 17" height="16" width="16" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>';
    },
  };

  // for rendering text with custom class in datatable
  t.renderText = function (cls) {
    return function (data, type) {
      if (type !== "display") return data;
      return `<span class="${cls}">${data ?? ""}</span>`;
    };
  };

    t.bindEvents = function () {
        t.content.on("click", el.btn.reload, () => t.reload());
        t.content.on("click", el.btn.add, (e) => t.create(e));
        t.content.on("click", ".edit", (e) => t.edit(e));
        t.content.on("click", ".delete", (e) => t.delete(e));

        el.mdl.on("click", "#btnSubmit", (e) => t.submit(e));
        el.mdl.on("click", ".addOptionDiv", function () {
            let index = $(".optionElementDiv").length;
            $(".customOptionsHolders").append(`
  <div class="optionElementDiv mt-3">

    <div class="col-md-12 amg-form-field amg-form-field-row gap-2 custom_tax_field_error">
                        <div class="col-md-10">
      <div class="input-group">
        <input 
          type="text"
          name="customOptions[${index}]"
            class="form-control customOptionInput"
          placeholder="${t.config.translations.add_tax}" />
      </div>
                        </div>
                        <div class="col-md-2">
                            <button 
                                type="button"
                                style="min-height: 40px;min-width: 40px;"
        class="amg-btn amg-btn-primary amg-btn-icon-only removeOption"
        title="${t.config.translations.remove}">
                                <i class="bi bi-dash" 
                                style="line-height: 10px;font-size: 26px;">
                                </i>
      </button>
                        </div>

    </div>

  </div>
`);

            // Dynamically added input validation
            let $input = $(`input[name="customOptions[${index}]"]`);

            $input.rules("add", {
                required: true,
                clean_text_only: true,
                maxlength: 100,
                messages: {
                    required: t.config.translations.required_field,
                    maxlength: t.config.translations.maximum_100_characters_required,
                    clean_text_only: t.config.translations.clean_text_only,
                }
            });

            // Move validation error below input-group
            let validator = $input.closest("form").validate();

            validator.settings.errorPlacement = function (error, element) {
                if (element.closest(".input-group").length) {
                    error.insertAfter(element.closest(".input-group"));
                } else {
                    error.insertAfter(element);
                }
            };
        });

        el.mdl.on("click", ".removeOption", function (e) {
            e.preventDefault();

            let row = $(this).closest(".optionElementDiv");
            let taxEleId = $(this).data("id");

            if (taxEleId) {
                Swal.fire({
                    title: t.config.translations.delete_element || "Delete?",
                    icon: "warning",
                    showCancelButton: true,
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.post(
                            t.config.url.removeElement,
                            {
                                _token: t.config.token,
                                id: taxEleId,
                            },
                            function (response) {
                                if (response.status === "success") {
                                    row.remove();

                                    Swal.fire(
                                        "Deleted",
                                        response.msg,
                                        "success"
                                    );
                                } else {
                                    t.showSubmitError(response.msg);
                                }
                            },
                        ).fail(function (xhr) {
                            let response = xhr.responseJSON || {};
                            t.showSubmitError(response.msg || response.message);
                        });
                    }
                });
            } else {
                row.remove();
            }
        });

        $(".user-list-page-length").on("change", function () {
            t.dTable.page.len(parseInt($(this).val())).draw();
        });
    };



    t.create = function (e) {
        e.preventDefault();
        t.resetForm();
        t.submitUrl = t.config.url.saveCustomTax;
        el.mdl.title.text(t.config.translations.Add_Tax);

        new bootstrap.Modal(el.mdl[0]).show();
    };

    t.edit = function (e) {
        let id = $(e.currentTarget).data("id");

        $.get(t.config.url.editCustomTax, { id }, function (res) {
            if (res.status === "success") {
                t.resetForm();
                t.submitUrl = t.config.url.updateCustomTax;
                el.mdl.title.text(t.config.translations.edit_trigger);

                if (!el.mdl.frm.find("input[name=id]").length) {
                    el.mdl.frm.append(`<input type="hidden" name="id" value="${id}">`);
                } else {
                    el.mdl.frm.find("input[name=id]").val(id);
                }
                el.mdl.frm.find("[name=name]").val(res.data.tax_name);

                $(".customOptionsHolders").html("");
                res.data.tax_details.forEach((elm) => {
                    $(".customOptionsHolders").append(`
                        <div class="optionElementDiv mt-3">
                        //sbz custom_tax_field_error
                            <div class="col-md-12 amg-form-field amg-form-field-row gap-2 custom_tax_field_error">
                                <div class="col-md-10">
                                    <div class="input-group">
                                        <input 
                                            type="text"
                                            name="customOptions[${elm.tax_ele_id}]"
                                            value="${elm.tax_element != null && elm.tax_element !== undefined && elm.tax_element !== 'null' ? elm.tax_element : ''}"
                                            class="form-control customOptionInput"
                                            placeholder="${t.config.translations.add_tax}"
                                        />
                                    </div>
                                </div>

                                <div class="col-md-2">
                                <button
                                    type="button"
                                    style="min-height: 40px;min-width: 40px;"
                                    class="amg-btn amg-btn-primary amg-btn-icon-only removeOption"
                                    data-id="${elm.tax_ele_id}"
                                    title="${t.config.translations.remove}">
                                    <i class="bi bi-dash" 
                                    style="line-height: 10px;font-size: 26px;">
                                    </i>
                                </button>
                                </div>
                            </div>

                        </div>
                    `);

                    let $input = $(`input[name="customOptions[${elm.tax_ele_id}]"]`);

                    // Remove any previous validation state
                    $input.removeClass("error is-invalid");
                    $input.removeAttr("aria-invalid");

                    // Add validation rules
                    $input.rules("add", {
                        required: true,
                        clean_text_only: true,
                        maxlength: 100,
                        messages: {
                            required: t.config.translations.required_field,
                            maxlength: t.config.translations.maximum_100_characters_required,
                            clean_text_only: t.config.translations.clean_text_only,
                        }
                    });
                });

                new bootstrap.Modal(el.mdl[0]).show();
            } else {
                Swal.fire({
                    icon: "error",
                    title: t.config.translations.oops,
                    text: res.msg,
                    confirmButtonText: t.config.translations.ok,
                }).then(() => {
                    t.dTable.ajax.reload(null, false);
                });
            }
        }).fail(function () {
            Swal.fire({
                icon: "error",
                title: t.config.translations.oops,
                text: t.config.translations.something_went_wrong || "Something went wrong. Please try again.",
                confirmButtonText: t.config.translations.ok,
            });
        });
    };

    t.delete = function (e) {
        let id = $(e.currentTarget).data("id");
        Swal.fire({
            title: t.config.translations.delete_custom_tax,
            text: t.config.translations.warning,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: t.config.translations.confirm_delete,
            cancelButtonText: t.config.translations.cancel,
        }).then((res) => {
            if (res.isConfirmed) {
                $.post(
                    t.config.url.removeCustomTax,
                    {
                        _token: t.config.token,
                        id,
                    },
                    function (r) {
                        if (r.status === "success") {
                            Swal.fire({
                                icon: "success",
                                title: t.config.translations.deleted,
                                text: r.msg,
                                confirmButtonText: t.config.translations.ok,
                            }).then(() => {
                                t.dTable.ajax.reload(null, false);
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: t.config.translations.oops,
                                text: r.msg,
                                confirmButtonText: t.config.translations.ok,
                            }).then(() => {
                                t.dTable.ajax.reload(null, false);
                            });
                        }
                    },
                ).fail(function () {
                    Swal.fire({
                        icon: "error",
                        title: t.config.translations.oops,
                        text: t.config.translations.something_went_wrong || "Something went wrong. Please try again.",
                        confirmButtonText: t.config.translations.ok,
                    });
                });
            }
        });
    };

    t.showSubmitError = function (message) {
        Swal.fire({
            icon: "error",
            title: t.config.translations.oops,
            text: message || t.config.translations.something_went_wrong || "Something went wrong. Please try again.",
            confirmButtonText: t.config.translations.ok,
        });
    };

    t.submit = function (e) {
        e.preventDefault();
        if (!el.mdl.frm.valid()) return;
        let fd = new FormData(el.mdl.frm[0]);
        $.ajax({
            url: t.submitUrl,
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status === "success") {
                    bootstrap.Modal.getInstance(el.mdl[0]).hide();
                    t.dTable.ajax.reload();
                    Swal.fire({
                        icon: "success",
                        title: t.config.translations.success,
                        text: res.msg,
                        confirmButtonText: t.config.translations.ok,
                    });
                } else {
                    t.showSubmitError(res.msg);
                }
            },
            error: function (xhr) {
                let response = xhr.responseJSON || {};
                let message = response.msg || response.message;

                if (!message && response.errors) {
                    let errors = Object.values(response.errors).flat();
                    message = errors.length ? errors[0] : null;
                }

                t.showSubmitError(message);
            },
        });
    };

    el.mdl.frm.validate({
        rules: {
            name: {
                required: true,
                clean_text_only: true,
                minlength: 2,
                maxlength: 100,
            },
        },
        messages: {
            name: {
                required: t.config.translations.name_required,
                minlength: t.config.translations.minimum_2_characters_required,
                maxlength: t.config.translations.maximum_100_characters_required,
                clean_text_only: t.config.translations.clean_text_only,
            },
        },
        errorElement: "label",
        errorClass: "error mt-2",
        errorPlacement: function (error, element) {
            let group = element.closest(".custom_tax_field_error");
            if(!group.length){
            let group = element.closest(".input-group");
            }

            if (group.length) {
                error.insertAfter(group);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            $(element).addClass("is-invalid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid");
            let container = $(element).closest(".col-md-12, .col-md-6");
            container.find(".amg-form-error-wrap").html("");
        },
        submitHandler: function () {
            t.submit();
        },
    });

    t.resetForm = function () {
        el.mdl.frm[0].reset();
        el.mdl.frm.find("input[name=id]").remove();
        $(".customOptionsHolders").html("");
        el.mdl.frm.validate().resetForm();
        el.mdl.frm.find(".is-invalid").removeClass("is-invalid");
    };

    t.reload = function () {
        $("#user-list-search").val("");
        if (t.dTable) {
            t.dTable.search("").draw();

            t.dTable.ajax.reload(null, false);
        }
    };

    t.hasPermission = function (name) {
        return t.config.permissions.includes(name);
    };

    setTimeout(() => t.initDataTable(), 50);
    t.bindEvents();
};