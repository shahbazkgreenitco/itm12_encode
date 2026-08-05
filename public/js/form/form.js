var Form = function(config) {
    console.log("form is loaded!");

    var t = this;
    t.config = config;

    t.content    = $("#dynamic-form-wrapper");
    console.log(t.content)
    t.table      = $("#dynamic-form");
    t.searchbox  = t.content.find(".dynamic-form-search");

    t.httpCall     = true;
    t.httpPostPath = "";
    t.data         = {};
    t.offListen    = false;
    t.btn          = {};

    t.buildActions = function(d) {
        var actions = [];

        if (jQuery.inArray("DynamicFormEdit", t.config.permissions) !== -1) {
            actions.push(`
                <button class="amg-action-btn btn-edit dtActEdit" data-id="${d.id}" title="${t.config.translations.edit_form}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                    </svg>
                </button>
            `);
        }

        if (jQuery.inArray("DynamicFormDelete", t.config.permissions) !== -1) {
            actions.push(`
                <button class="amg-action-btn danger dtActDel" data-id="${d.id}" title="${t.config.translations.delete}">
                    <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                    </svg>
                </button>
            `);
        }
        actions.push(`
            <a href="${t.config.url.history}/${d.id}" target="_blank"
                class="amg-action-btn primary viewinfo" title="History">
                <svg fill="currentColor" height="15" width="15" version="1.1" id="XMLID_136_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
                    viewBox="0 0 24 24" xml:space="preserve">
                <g id="history">
                    <g>
                        <path d="M12,24C5.4,24,0,18.6,0,12h2c0,5.5,4.5,10,10,10s10-4.5,10-10S17.5,2,12,2C8.4,2,5.1,3.9,3.3,7H8v2H0V1h2v4.4
                            C4.2,2.1,8,0,12,0c6.6,0,12,5.4,12,12S18.6,24,12,24z M15.3,17.8L11,13.4V6h2v6.6l3.7,3.8L15.3,17.8z"/>
                    </g>
                </g>
                </svg>
            </a>
        `);

        if (!actions.length) {
            return `<span class="text-muted">-</span>`;
        }

        return `
            <div class="amg-datatable-actions d-flex justify-content-center gap-2">
                ${actions.join("")}
            </div>
        `;
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        dom: 'rtip',
        order: [[4, 'desc']],  
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.form_list,
            type: "post",
            data: function(d) {
                d._token      = t.config.token;
                d.filters     = t.config.other_filters;
                d.main_filter = t.config.main_filter;
                d.search_query  = t.content.find(".dynamic-list-search").val();
            }
        },
        columns: [
            { data: 'a.id' },
            { data: 'a.form_name' },
            { data: 'a.descriptions' },
            { data: 'a.created_at_format' },
            { data: 'a.updated_at_format' },
            {
                data: null,         
                width: "148px",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return t.buildActions(row.a || row);
                }
            }
        ]
    });

    t.addForm = function(e) {
        e.preventDefault(); 
        window.open(t.config.url.add);
    };

    t.editForm = function(e) {
        e.preventDefault();
        var id = $(e.currentTarget).attr("data-id");
        window.open(t.config.url.edit + "/" + id);
    };

    t.deleteForm = function(e) {
        e.preventDefault();
        var id = $(e.currentTarget).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + id;

        sweetAlertConfirmation({
            message: config.translations.are_you_delete,
            onConfirm: function() {
                t.httpCall = false;
                var http = $.get(t.httpPostPath);

                http.done(function(data) {
                    if (typeof data === "object") {
                        if (data.status === "success") {
                            sweetAlert("center", "success", data);
                            setTimeout(function() {
                                window.location = t.config.url.list_form;
                            }, 800);
                        } else {
                            sweetAlert("center", "error", data);
                        }
                    }
                });

                http.fail(function() {
                    sweetAlert("center", "error", {
                        msg: config.translations.something_wrong,
                    });
                });

                http.always(function() {
                    t.httpCall = true;
                });
            }
        });
    };

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.dTbl.ajax.reload();

    t.content.on("keyup", ".dynamic-list-search", function(e) {
        if (e.key === "Enter") {
            t.dTbl.ajax.reload(null, false);
        }
    });

    t.content.on("click", ".btn-reload-list", function(e) {
        e.preventDefault();
        t.dTbl.ajax.reload(null, false);
    });

    t.content.on("change", ".form-list-page-length", function (e) {
        e.preventDefault();
        var length = parseInt($(this).val());
        if (!length) return;
        t.dTbl.page.len(length).draw(false);
    });

    t.content.on("click", ".btn-add-form", t.addForm);
    t.content.on("click", ".dtActDel",     t.deleteForm.bind(t));
    t.content.on("click", ".dtActEdit",    t.editForm.bind(t));
};