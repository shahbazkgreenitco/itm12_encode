$(document).ready(function () {
    var table = $("#mytable");
    dTbl = table.DataTable({
        language: datatable_footer_translations(config.datatable_translations),
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        lengthChange: false,
        pageLength: 10,
        dom: "ltrip",
        order: [[7, "desc"]],
        fixedColumns: {
            rightColumns: 1,
            leftColumns: 1,
        },

        ajax: {
            url: config.url.lease,
            type: "POST",
            data: function (d) {
                d._token = config.token;
                if (d.order && d.order.length) {
                    let order = d.order[0];
                    let col = d.columns[order.column];
                    d.sorted_column_name = col.name || col.data;
                    d.sorted_direction = order.dir;
                }
            },
            error: function (xhr) {
                console.error("DataTable Error:", xhr.responseText);
            },
        },

        columns: [
            {
                data: "a.id",
            },
            { data: "a.leaser" },
            { data: "a.contract_number" },
            { data: "a.start_date_txt" },
            { data: "a.end_date_txt" },
            { data: "a.lease_type_name" },
            { data: "a.maintenance_incharge_name" },
            { data: "a.last_updated_at" },
            {
                data: "a",
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (row) {
                    let actions = [];

                    if (config.permissions.includes("ContractAgreementEdit")) {
                        actions.push(`
                        <button class="user-list-action-btn open-edit-modal"
                            data-id="${row.id}"
                            data-bs-toggle="tooltip" title="${config.translations.edit}">
                          <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg> 
                        </button>
                    `);
                    }
                    if (config.permissions.includes("ContractAgreementDelete")) {
                        actions.push(`
                        <button class="user-list-action-btn open-delete is-delete"
                            data-id="${row.id}"
                            data-bs-toggle="tooltip" title="${config.translations.delete}">
                           <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
                        </button>
                    `);
                    }
                    if (
                        row.attachment &&
                        config.permissions.includes("ContractAgreementDownload")
                    ) {
                        actions.push(`
                        <button class="user-list-action-btn open-download"
                            data-id="${row.id}"
                            data-bs-toggle="tooltip" title="${config.translations.download}">
                           <svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>
                        </button>
                    `);
                    }
                    actions.push(`
                    <a href="${config.url.leases_info}/${row.id}"
                        target="_blank"
                        class="user-list-action-btn"
                        data-bs-toggle="tooltip" title="${config.translations.history}">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M3 1.5v3h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M8 4.5v3l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        </a>
                `);
                    return `
                    <div class="user-list-actions d-flex justify-content-center gap-2">
                        ${actions.join("")}
                    </div>
                `;
                },
            },
        ],
        fnInitComplete: function () {
            var api = this.api();
            let timer;
            $("#user-list-search")
                .off("keyup")
                .on("keyup", function (e) {
                    if(e.keyCode == '13'){
                        clearTimeout(timer);

                        let value = this.value;

                        timer = setTimeout(() => {
                            api.search(value).draw();
                        }, 400);
                    }
                });
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    $(".user-list-page-length").on("change", function () {
        let value = parseInt($(this).val(), 10);

        if (dTbl) {
            dTbl.page.len(value).draw();
        }
    });
    $(".btn-reload-list")
        .off("click")
        .on("click", function () {
            if (dTbl) {
                dTbl.ajax.reload(null, true);
            }
        });

    var leaseAdd = function (config) {
        var t = this;
        t.config = config;
        t.content = $("section.content");
        t.mdl = t.content.find("#leasemd1");
        t.frm = t.mdl.find("#leaseform");
        t.mdl.forAction = "";
        t.frmEl = {};
        t.frmEl.company_id = t.frm.find("#company_id");
        t.frmEl.leaser = t.frm.find("#leaser");
        t.frmEl.start_date = t.frm.find("#start_date");
        t.frmEl.end_date = t.frm.find("#end_date");
        t.frmEl.lease_type = t.frm.find("#lease_type");
        t.frmEl.category_id = t.frm.find("#category_id");
        t.frmEl.model_id = t.frm.find("#model_id");
        t.frmEl.device_id = t.frm.find("#device_id");
        t.frmEl.currency_format = t.frm.find("#currency_format");
        t.frmEl.cost = t.frm.find("#cost");
        t.frmEl.maintenance_incharge = t.frm.find("#maintenance_incharge");
        t.frmEl.img = t.frm.find("#img");
        t.frmEl.contract_number = t.frm.find("#contract_number");
        t.frmEl.description = t.frm.find("#description");
        t.frmEl.status = t.frm.find("#active_status");
        t.frmEl.attachment = t.frm.find("#attachment");
        t.frmEl.imgviewcover = t.frm.find(".imgviewcover");
        t.frmEl.imgview = t.frm.find("#imgview");
        t.btn = {};
        t.btn.submit = t.frm.find("#btnSubmit");
        t.btn.update = t.frm.find("#btnupdate");
        t.btn.clear = t.frm.find("#btnClear");
        t.mdltitle = t.mdl.find(".modal-title");
        t.frmEl.start_date.on("changeDate", function (e) {
            t.frmEl.end_date.datepicker("setStartDate", e.date);
            try {
                if (t.frmEl.end_date.datepicker("getDate") < e.date) {
                    t.frmEl.end_date.datepicker("update", start_date);
                }
            } catch (err) {
                t.frmEl.end_date.datepicker("update", "");
            }
        });
        t.data = {
            models: [],
        };
        t.createlease = function (e) {
            e.preventDefault();
            t.frmEl.leaser.val("");
            t.frmEl.start_date.val("");
            t.frmEl.end_date.val("");
            t.frmEl.company_id.val("");
            t.frmEl.lease_type.val("");
            t.frmEl.maintenance_incharge.val("");
            t.frmEl.contract_number.val("");
            t.frmEl.description.val("");
            t.frmEl.cost.val("");
            t.mdl.forAction = "";
            t.mdl.modal("show");
            t.mdltitle.text(t.config.translations.add_contract);
            t.btn.submit.text(t.config.translations.save);
            t.httpPostPath = t.config.url.add;
            t.resetFrm();
        };

        t.handlesubmit = function (e) {
            e.preventDefault();
            if (t.frmValidator.form() == false) {
                $("#img").hide();
                return false;
            }

            var frmData = new FormData();
            frmData.append("_token", t.config.token);
            frmData.append("company_id", t.frmEl.company_id.val());
            frmData.append("leaser", t.frmEl.leaser.val());
            frmData.append("start_date", t.frmEl.start_date.val());
            frmData.append("end_date", t.frmEl.end_date.val());
            frmData.append("lease_type", t.frmEl.lease_type.val());
            frmData.append(
                "maintenance_incharge",
                t.frmEl.maintenance_incharge.val(),
            );
            frmData.append("contract_number", t.frmEl.contract_number.val());
            frmData.append("description", t.frmEl.description.val());
            if ($("#attachment")[0].files.length > 0) {
                frmData.append("attachment", $("#attachment")[0].files[0]);
            }
            if ($("#delete_img").is(":checked")) {
                frmData.append("delete_img", 1);
            }
            frmData.append("category_id", t.frmEl.category_id.val());
            frmData.append("model_id", t.frmEl.model_id.val());
            frmData.append("device_id", t.frmEl.device_id.val());
            frmData.append("currency_format", t.frmEl.currency_format.val());
            frmData.append("status", t.frmEl.status.val());
            frmData.append("cost", t.frmEl.cost.val());
            t.btn.submit.prop("disabled", true);

            $.ajax({
                url: t.httpPostPath,
                type: "POST",
                processData: false,
                contentType: false,
                data: frmData,

                success: function (data) {
                    if (typeof data === "object") {
                        if (data.status === "success") {
                            if (typeof bootstrap !== "undefined") {
                                bootstrap.Modal.getInstance(t.mdl[0]).hide();
                            } else {
                                t.mdl.modal("hide");
                            }
                            $("#hand").hide();
                            dTbl.ajax.reload(null, false);
                            Swal.fire({
                                icon: "success",
                                title: t.config.translations.success || "Success",
                                text: data.msg,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: t.config.translations.error || "Error",
                                text: data.msg,
                            });
                        }
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: t.config.translations.error || "Error",
                        text: t.config.translations.something_went_wrong,
                    });
                },

                complete: function () {
                    $("#img").hide();
                    t.btn.submit.prop("disabled", false);
                },
            });
        };

        t.loadImageViewer = function (img) {
            if (img != "" && img != null && t.mdl.forAction == "edit") {
                t.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + img);
                t.frmEl.imgviewcover.removeClass("d-none");
            } else {
                t.frmEl.imgview.attr("src", "");
                t.frmEl.imgviewcover.addClass("d-none");
            }
        };
        t.loadForm = function (objLease) {
            // t.resetFrm();
            if (objLease.opts.category != null) {
                t.frmEl.category_id
                    .empty()
                    .append(
                        new Option(
                            objLease.opts.category.name,
                            objLease.opts.category.id,
                            true,
                            true,
                        ),
                    )
                    .trigger("change");
            } else {
                t.frmEl.category_id
                    .empty()
                    .append(new Option(config.translations.select_category, null));
            }
            if (objLease.opts.model != null) {
                t.frmEl.model_id
                    .empty()
                    .append(
                        new Option(
                            objLease.opts.model.name,
                            objLease.opts.model.id,
                            true,
                            true,
                        ),
                    )
                    .trigger("change");
            }
            if (objLease.opts.device != null) {
                t.frmEl.device_id.empty();
                if (typeof objLease.opts.device != "undefined") {
                    $.each(objLease.opts.device, function (i, value) {
                        t.frmEl.device_id.append(
                            new Option(value.text, value.id, true, true),
                        );
                    });
                }
                t.frmEl.device_id.trigger("change");
            }

            if (
                objLease.data.currency_format != "" &&
                objLease.data.currency_format != null
            ) {
                t.frmEl.currency_format
                    .val(objLease.data.currency_format)
                    .trigger("change");
            } else {
                t.mdl.optionsCurrency();
            }
            if (objLease.data.company_id > 0) {
                t.frmEl.company_id.val(objLease.data.company_id).trigger("change");
            }
            if (objLease.data.lease_type > 0) {
                t.frmEl.lease_type.val(objLease.data.lease_type).trigger("change");
            }
            if (objLease.data.maintenance_incharge > 0) {
                t.frmEl.maintenance_incharge
                    .val(objLease.data.maintenance_incharge)
                    .trigger("change");
            }
            if (
                typeof objLease.dropdown == "object" &&
                typeof objLease.dropdown.leaser == "object"
            ) {
                t.frmEl.leaser
                    .append(
                        new Option(
                            objLease.dropdown.leaser.text,
                            objLease.dropdown.leaser.id,
                        ),
                    )
                    .trigger("change");
            }
            t.loadImageViewer(objLease.data.attachment);
            //   t.frm.find("input[name='cost']").val(objLease.data.cost);
            if(objLease.data.cost && objLease.data.cost != null){
                t.frm
                .find("input[name='cost']")
                .val(parseFloat(objLease.data.cost).toString().replace(/\.00$/, ""));
            }
            t.frmEl.status.val(objLease.data.status).trigger("change");
            t.frmEl.start_date.datepicker("setDate", objLease.data.start_date);
            t.frmEl.end_date.datepicker("setDate", objLease.data.end_date);
            t.frmEl.contract_number.val(objLease.data.contract_number);
            t.frmEl.description.val(objLease.data.description);
            //   t.frmEl.attachment.val(objLease.data.attachment);
            t.frmEl.attachment.val("");
            t.mdl.modal("show");
        };

        t.mdl.optionsCurrency = function () {
            t.frmEl.currency_format
                .empty()
                .append(new Option(config.translations.select_currency_format, ""));
            $.each(t.config.currencies, function (i, v) {
                var opt =
                    t.config.default_currency_format == i
                        ? new Option("", i, true, true)
                        : new Option("", i);
                opt.innerHTML = v.name + " (" + v.symbol_html + ")";
                t.frmEl.currency_format.append(opt);
            });
            t.frmEl.currency_format.trigger("change");
        };
        t.resetFrm = function () {
            t.frmEl.start_date.empty();
            t.frmEl.end_date.empty();
            t.frmEl.company_id.val("1").trigger("change");
            t.frmEl.maintenance_incharge.trigger("change");
            t.frmEl.leaser.empty().trigger("change");
            //t.frmEl.leaser.trigger("change");
            t.frmEl.category_id.empty().trigger("change");
            t.frmEl.model_id.empty().trigger("change");
            t.frmEl.device_id.empty().trigger("change");
            t.frmEl.lease_type.trigger("change");
            t.frmEl.contract_number.trigger("change");
            t.frmEl.description.trigger("change");
            t.frmEl.status.val("1").trigger("change");
            t.frmEl.attachment.val("").trigger("change").attr("disabled", false);
            t.loadImageViewer("");
            t.mdl.optionsCurrency();
            t.frmValidator.resetForm();
        };

        $("#btnSubmit").click(function () {
            $("#img").show();
        });

        t.editlease = function (e) {
            e.preventDefault();
            t.resetFrm();
            var accId = $(this).attr("data-id");
            t.httpPostPath = t.config.url.edit + "/" + accId;
            var http = $.get(t.config.url.get + "/" + accId);
            http.done(function (data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.mdl.modal("show");
                        t.mdltitle.text(t.config.translations.edit_contract_agreement);
                        t.btn.submit.text(t.config.translations.save);
                        t.mdl.forAction = "edit";
                        t.loadForm(data.data);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.msg,
                        });
                    }
                }
            });
            http.fail(function () {
                var data = {
                    msg: config.translations.something_went_wrong,
                };
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.msg,
                });
            });
            http.always(function () {
                $("#img").hide();
                t.httpCall = true;
            });
        };

        t.deleteLease = function (e) {
            e.preventDefault();

            var leaseId = $(this).attr("data-id");
            var url = t.config.url.delete + "/" + leaseId;

            Swal.fire({
                title: t.config.translations.are_you_delete,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: t.config.translations.yes || "Yes",
                cancelButtonText: t.config.translations.cancel || "Cancel",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.get(url, function (data) {
                        if (typeof data === "object" && data.status === "success") {
                            dTbl.ajax.reload(null, false);
                            Swal.fire({
                                icon: "success",
                                title: t.config.translations.success || "Success",
                                text: data.msg,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: t.config.translations.error || "Error",
                                text: data.msg,
                            });
                        }
                    }).fail(function () {
                        Swal.fire({
                            icon: "error",
                            title: t.config.translations.error || "Error",
                            text: t.config.translations.something_went_wrong,
                        });
                    });
                }
            });
        };

        t.downloadLease = function () {
            var id = $(this).attr("data-id");
            window.location = t.config.url.download + "/" + id;
        };

        $.validator.addMethod(
            "clean_text_only",
            function (value, element) {
                return this.optional(element) || /^[a-zA-Z0-9\s]+$/.test(value);
            },
            "Only letters and numbers allowed",
        );
    
        t.frmValidator = t.frm.validate({
            onsubmit: false,
            rules: {
                company_id: {
                    required: true,
                },
                leaser: {
                    required: true,
                },
                maintenance_incharge: {
                    str_name: true,
                },
                start_date: {
                    required: true,
                },
                end_date: {
                    required: true,
                },
                contract_number: {
                    required: true,
                    clean_text_only: true,
                    maxlength: 100
                },
                cost: {
                    digits: true,
                },
                description: {
                    maxlength: 2000,
                    clean_text_only: true,
                },
                lease_type: {
                    required: true,
                },
                maintenance_incharge: {
                    required: true,
                },
                cost: {
                    number: true,
                    min: 0,
                    max: 9999999.99
                },
                attachment: {
                    extension: "jpeg|bmp|png|gif|jpg|pdf|doc|docx|xls|xlsx",
                    filesize: 10240,
                },
            },
            messages: {
                company_id: {
                    required: config.translations.company_required,
                },
                leaser: {
                    required: config.translations.leaser_required,
                },
                maintenance_incharge: {
                    required: config.translations.maintenance_incharge_required,
                    str_name: config.translations.invalid_name,
                },
                start_date: {
                    required: config.translations.start_date_required,
                },
                end_date: {
                    required: config.translations.end_date_required,
                },
                contract_number: {
                    required: config.translations.contract_number_required,
                    clean_text_only: config.translations.clean_text_only,
                    maxlength: $.validator.format(config.translations.maxlength, 100),
                },
                description: {
                    clean_text_only: config.translations.clean_text_only,
                    maxlength: $.validator.format(config.translations.maxlength, 2000),
                },
                lease_type: {
                    required: config.translations.lease_type_required,
                },
                cost: {
                    number: config.translations.invalid_number,
                    min: $.validator.format(config.translations.min_value, 0),
                    max: $.validator.format(config.translations.max_value, 9999999.99),
                }, 
                attachment: {
                    extension: config.translations.invalid_file_format
                }
            },
            errorElement: "span",
            errorClass: "text-danger",

            errorPlacement: function (error, element) {
                error.appendTo(element.parent().parent());
            },
            highlight: function (element, errorClass) {
                $(element).closest(".select-div").addClass(errorClass);
            },
            unhighlight: function (element, errorClass) {
                $(element).closest(".select-div").removeClass(errorClass);
            },

            submitHandler: function () {
                t.submitForm();
            },
        });
        var select2Opts = { width: "100%" };
        // t.config.companies.unshift({text:"Select Company"});
        t.frmEl.company_id.select2(
            $.extend({}, select2Opts, {
                data: t.config.companies,
                dropdownParent: t.frmEl.company_id.parent(),
            }),
        );
        t.frmEl.lease_type.select2(
            $.extend({}, select2Opts, {
                data: t.config.lease_type,
                dropdownParent: t.frmEl.lease_type.parent(),
                placeholder: config.translations.contract_type,
            }),
        );
        t.frmEl.maintenance_incharge.select2(
            $.extend({}, select2Opts, {
                data: t.config.maintenance_incharge,
                dropdownParent: t.frmEl.maintenance_incharge.parent(),
            }),
        );
        t.frmEl.currency_format.select2(
            $.extend({}, select2Opts, {
                dropdownParent: t.frmEl.currency_format.parent(),
            }),
        );

        t.frmEl.leaser.select2(
            $.extend({}, select2Opts, {
                dropdownParent: t.frmEl.leaser.parent(),
                ajax: {
                    url: t.config.getSupplierByAjax,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300,
                },
                placeholder: config.translations.select_supplier,
            }),
        );

        var select2Opts = { width: "100%" };
        $("#leaseform").find("#model_ids").hide();
        $("#leaseform").find("#device_ids").hide();

        t.frmEl.category_id
            .select2(
                $.extend({}, select2Opts, {
                    dropdownParent: t.frmEl.category_id.parent(),
                    ajax: {
                        url: t.config.getCategoryByAjax,
                        dataType: "json",
                    },
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                    delay: 300,
                    processResults: function (data) {
                        return {
                            results: data.results || data.items || [],
                        };
                    },
                    allowClear: true,
                    placeholder: config.translations.select_category,

                    templateResult: function (s) {
                        if (s.loading) {
                            return $("<div>" + s.text + "</div>");
                        }

                        return $("<div>" + s.text + "</div>");
                    },
                }),
            )
            .on("change", function (e) {
                t.refillModel();
            });
        t.frmEl.model_id.select2(
            $.extend({}, select2Opts, {
                dropdownParent: t.frmEl.model_id.parent(),
                allowClear: true,
                placeholder: "Select Model",
            }),
        );
        t.frmEl.status.select2(
            $.extend({}, select2Opts, {
                data: [
                    { id: 1, text: t.config.translations.active },
                    { id: 0, text: t.config.translations.inactive },
                ],
                dropdownParent: t.frmEl.status.parent(),
            }),
        );
        t.refillModel = function (e) {
            if (typeof e !== "undefined") {
                e.preventDefault();
            }
            t.data.models = [];
            t.frmEl.model_id.empty().append(new Option("Select the Model", ""));
            t.frmEl.device_id.empty().append(new Option("Select Device", ""));
            var type_val = t.frmEl.category_id.val();
            if (type_val > 0 && !isNaN(type_val)) {
                $.get(t.config.getModelByAjax + "/?cat_id=" + type_val)
                    .done(function (data) {
                        if (typeof data == "object" && data.results.length) {
                            t.data.models = data.results;
                            $.each(data.results, function (i, v) {
                                t.frmEl.model_id.append($("<option>").val(v.id).text(v.text));
                            });
                            $("#leaseform").find("#model_ids").show();
                        }
                    })
                    .fail(function (xhr) {})
                    .always(function () {
                        t.frmEl.model_id.trigger("change");
                    });
            } else {
                $("#leaseform").find("#model_ids").hide();
            }
        };

        // for device display
        $(t.frmEl.model_id).change(function () {
            if (t.frmEl.model_id.val() != null && t.frmEl.model_id.val() != "") {
                $("#leaseform").find("#device_ids").show();
            } else {
                $("#leaseform").find("#device_ids").hide();
            }

            t.frmEl.device_id.select2(
                $.extend({}, select2Opts, {
                    multiple: true,
                    dropdownParent: t.frmEl.device_id.parent(),
                    ajax: {
                        url: t.config.getDeviceByModel,
                        dataType: "json",
                        data: function (p) {
                            return {
                                model_id: $(t.frmEl.model_id).val(),
                                search: p.term,
                                page: p.page || 1,
                            };
                        },
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: params.page * 30 < data.total,
                            },
                        };
                    },
                    allowClear: false,
                    placeholder: "Select the Device",
                    templateResult: function (s) {
                        if (typeof s.loading != "undefined" && s.loading) {
                            return $("<div>" + s.text + "</div>");
                        }
                        a =
                            "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
                            s.asset_tag +
                            "</div>";
                        if (s.asset_name != null) {
                            a +=
                                "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
                                s.asset_name +
                                "</div>";
                        }
                        a +=
                            "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
                            s.serial +
                            "</div>";
                        return $("<div>" + a + "</div>");
                    },
                }),
            );
        });

        // //t.config.leaser.unshift({id:0, text:"Select Parent"});
        //t.frmEl.leaser.select2($.extend({}, select2Opts, { data: t.config.leaser }));
        t.frmEl.start_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
        t.frmEl.end_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
        t.content.on("click", ".open-add-modal", $.proxy(t.createlease));
        t.content.on("click", ".open-edit-modal", $.proxy(t.editlease));
        t.content.on("click", ".open-delete", $.proxy(t.deleteLease));
        t.content.on("click", ".open-download", $.proxy(t.downloadLease));
        t.btn.submit.on("click", $.proxy(t.handlesubmit));
        //dTbl.ajax.reload();
    };

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= (param*1024));
    }, 'File size must be less than 10 MB');

    new leaseAdd(config);
});
