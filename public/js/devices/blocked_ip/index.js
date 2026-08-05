var BlockedIp = function (config) {
    var t = this;

    t.config = config;
    t.content = $("section.content");
    t.table = t.content.find("#mytable");

    t.httpCall = true;
    t.httpPostPath = "";
    t.mdl = t.content.find("#block-mdl");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.frm = t.mdl.find("#block-mdl-frm");

    t.mdl.frmEl = {
        ip: t.mdl.find("#ip"),
        mac: t.mdl.find("#mac"),
        ip_div: t.mdl.find("#ip_div"),
        mac_div: t.mdl.find("#mac_div"),
        type: t.mdl.find("input[name=type]"),
    };

    t.dTbl = t.table.DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        dom: "ltrip",
        order: [[4, "desc"]],

        ajax: {
            url: t.config.url.block,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;

                if (d.order && d.order.length) {
                    let order = d.order[0];
                    let col = d.columns[order.column];
                    d.sorted_column_name = col.name || col.data;
                    d.sorted_direction = order.dir;
                }
            },
        },

        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row, meta) {
                    return meta.settings._iDisplayStart + meta.row + 1;
                },
            },
            { data: "a.type" },
            { data: "a.ip_mac" },
            { data: "a.created_at" },
            { data: "a.updated_at" },
            {
                data: "a",
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (row) {
                    let actions = [];

                    actions.push(`
                    <button class="user-list-action-btn open-edit-modal"
                        data-id="${row.id}"
                        title="${t.config.translations.Edit}">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
                    </button>
                `);

                    actions.push(`
                    <button class="user-list-action-btn open-delete"
                        data-id="${row.id}"
                        title="${t.config.translations.Delete}">
                       <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
                    </button>
                `);

                    return `
                    <div class="user-list-actions d-flex justify-content-center gap-2">
                        ${actions.join("")}
                    </div>
                `;
                },
            },
        ],
    });

    t.search = function () {
        let value = $(".user-list-search").val().trim();
        t.dTbl.search(value).draw();
    };

    t.reload = function () {
        $(".user-list-search").val("");
        t.dTbl.search("").draw();
        t.dTbl.ajax.reload(null, true);
    };

    t.cache_filter_values = function () {
        let v = $(".user-list-search").val().trim();

        t.config.search = v;
        t.config.other_filters = {};

        let jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters,
        };

        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.content.on("click", ".btn-download", function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.DownloadUrl + "?q=" + t.config.export_filters;
    });

    t.addBlockIp = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.add;
        t.mdl.title.text(t.config.translations.add_new_ip);
        t.mdl.btnSubmit.text(t.config.translations.save);
        t.mdl.modal("show");
    };

    t.handleSubmit = function (e) {
        e.preventDefault();

        if (!t.frmValidator.form()) return;
        if (!t.httpCall) return;

        t.httpCall = false;

        $.post(t.httpPostPath, t.mdl.frm.serialize())
            .done(function (data) {
                if (data.status === "success") {
                    sweetAlert("center", "success", data);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert("center", "error", data);
                }
            })
            .always(function () {
                t.httpCall = true;
            });
    };

    t.deleteBlockIp = function (e) {
        e.preventDefault();

        let id = $(this).data("id");

        Swal.fire({
            title: t.config.translations.delete_ip_msg,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: t.config.translations.yes_delete,
            cancelButtonText: t.config.translations.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: t.config.url.delete + "/" + id,
                    type: "GET",
                    data: { _token: t.config.token },
                })
                    .done(function (data) {
                        if (data.status === "success") {
                            Swal.fire({
                                icon: "success",
                                text: data.msg || t.config.translations.delete_success,
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            t.dTbl.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: "error",
                                text: data.msg || t.config.translations.delete_failed,
                            });
                        }
                    })
                    .fail(function () {
                        Swal.fire({
                            icon: "error",
                            text: t.config.translations.something_went_wrong,
                        });
                    });
            }
        });
    };

    t.editBlockIp = function (e) {
        e.preventDefault();

        let id = $(this).data("id");
        t.httpPostPath = t.config.url.edit + "/" + id;

        $.get(t.config.url.get + "/" + id).done(function (data) {
            if (data.status === "success") {
                t.mdl.modal("show");
                t.mdl.title.text(t.config.translations.Edit);

                t.loadForm(data.data);
                t.typeCheckEdit();
            }
        });
    };


    t.loadForm = function (obj) {
        t.mdl.frmEl.ip.val(obj.ip || "");
        t.mdl.frmEl.mac.val(obj.mac || "");

        if (obj.type == 1) $("#ip_check").prop("checked", true);
        if (obj.type == 2) $("#mac_check").prop("checked", true);
    };


    t.resetFrm = function () {

        t.mdl.frm[0].reset();

        if (t.mdl.frm.data("validator")) {
            t.mdl.frm.validate().resetForm();
        }

        t.mdl.frm.find(".is-invalid").removeClass("is-invalid");
        t.mdl.frm.find(".has-error").removeClass("has-error");
        t.mdl.frm.find(".amg-form-error-wrap").html("");

        t.mdl.frmEl.type.prop("checked", false);

        t.mdl.frmEl.ip_div.addClass("d-none");
        t.mdl.frmEl.mac_div.addClass("d-none");

        t.mdl.frmEl.ip.val("");
        t.mdl.frmEl.mac.val("");
    };

    t.typeCheck = function () {
        if ($(this).val() == "1") {
            t.mdl.frmEl.mac_div.addClass("d-none");
            t.mdl.frmEl.ip_div.removeClass("d-none");
            t.mdl.frmEl.mac.val("");
        } else {
            t.mdl.frmEl.ip_div.addClass("d-none");
            t.mdl.frmEl.mac_div.removeClass("d-none");
            t.mdl.frmEl.ip.val("");
        }
    };

    t.typeCheckEdit = function () {
        let selected = $("input[name=type]:checked").val();
        if (selected == "1") {
            t.mdl.frmEl.mac_div.addClass("d-none");
            t.mdl.frmEl.ip_div.removeClass("d-none");
        } else {
            t.mdl.frmEl.ip_div.addClass("d-none");
            t.mdl.frmEl.mac_div.removeClass("d-none");
        }
    };

    t.frmValidator = t.mdl.frm.validate({
        ignore: [],
        onsubmit: false,

        rules: {
            type: {
                required: true,
            },

            ip: {
                required: function () {
                    return $.trim(t.mdl.frmEl.mac.val()) === "";
                },
                clean_text_only: true,
            },

            mac: {
                required: function () {
                    return $.trim(t.mdl.frmEl.ip.val()) === "";
                },
                clean_text_only: true,
            },
        },

        messages: {
            type: {
                required: "Please select IP or MAC",
            },
            ip: {
                required: "Please enter IP",
            },
            mac: {
                required: "Please enter MAC",
            },
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") === "type") {
                let wrap = t.mdl.frm
                    .find("input[name='type']")
                    .closest(".amg-form-field")
                    .find(".amg-form-error-wrap");

                wrap.html(error);
                return;
            }

            let wrap = element
                .closest(".amg-form-field")
                .find(".amg-form-error-wrap");

            if (wrap.length) {
                wrap.html(error);
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) {
            if ($(element).attr("name") === "type") {
                $(element).closest(".amg-form-field").addClass("has-error");
            } else {
                $(element).addClass("is-invalid");
            }
        },

        unhighlight: function (element) {
            if ($(element).attr("name") === "type") {
                $(element).closest(".amg-form-field").removeClass("has-error");
            } else {
                $(element).removeClass("is-invalid");
            }

            let wrap = $(element)
                .closest(".amg-form-field")
                .find(".amg-form-error-wrap");

            wrap.html("");
        },
    });

    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
    t.content.on("click", ".open-add-modal", $.proxy(t.addBlockIp));
    t.content.on("click", ".open-delete", $.proxy(t.deleteBlockIp));
    t.content.on("click", ".open-edit-modal", $.proxy(t.editBlockIp));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));

    t.content.on("keyup", ".user-list-search", function (e) {
        if (e.key === "Enter") t.search();
    });

    t.content.on("change", ".user-list-page-length", function () {
        let len = parseInt($(this).val(), 10);
        t.dTbl.page.len(len).draw();
    });

    t.mdl.frmEl.type.on("click", $.proxy(t.typeCheck));
    t.dTbl.ajax.reload();
};
