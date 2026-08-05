$(document).ready(function () {
    var table = $("#mytable");
    dTbl = table.DataTable({
        language: datatable_footer_translations(config.datatable_translations),
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        lengthChange: false,
        scrollX: true,
        scrollCollapse: true,
        pageLength: parseInt($(".user-list-page-length").val()) || 10,
        dom: "ltrip",
        order: [[9, "desc"]],
        fixedColumns: {
            rightColumns: 1,
            leftColumns: 1,
        },
        ajax: {
            url: config.url.info,
            type: "POST",
            data: function (d) {
                d._token = config.token;
                d._id = config.info_id;
                if (d.order && d.order.length) {
                    let order = d.order[0];
                    let col = d.columns[order.column];
                    d.sorted_column_name = col.name || col.data;
                    d.sorted_direction = order.dir;
                }
            },
        },
        columns: [
            { data: "a.leaser", name: "leaser" },
            { data: "a.contract_number", name: "contract_number" },
            { data: "a.lease_type_name", name: "lease_type" },
            { data: "a.maintenance_incharge_name", name: "maintenance_incharge" },
            { data: "a.description", name: "description" },
            { data: "a.start_date", name: "start_date" },
            { data: "a.end_date", name: "end_date" },
            { data: "a.status", name: "status" },
            { data: "a.updated_by", name: "updated_by" },
            { data: "a.updated_at", name: "updated_at" },
            {
                data: "a",
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (row) {
                    let actions = [];
                    if (row.attachment) {
                        actions.push(`
            <button class="user-list-action-btn open-download"
            data-id="${row.id}" data-bs-toggle="tooltip"
            title="${config.translations.download}">
             <svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>
            </button>
            `);
                    }
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
            $("#tableSearch")
                .off("keyup")
                .on("keyup", function (e) {
                    if(e.key === "Enter" || e.keyCode == '13'){
                        let value = this.value;
                        setTimeout(() => {
                            api.search(value).draw();
                        }, 400);
                    }
                });
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    $(".user-list-page-length")
        .off("change")
        .on("change", function () {
            let value = parseInt($(this).val(), 10);
            if (dTbl) {
                dTbl.page.len(value).draw();
            }
        });
    $(".btn-reload-list")
        .off("click")
        .on("click", function () {

            if (dTbl) {
                dTbl.draw();
            }
        });
    var lease = function (config) {
        var t = this;
        t.config = config;
        t.content = $("section.content");
        t.downloadLease = function () {
            var id = $(this).attr("data-id");
            window.location = t.config.url.download + "/" + id;
        };
        t.content.on("click", ".open-download", $.proxy(t.downloadLease));
    };
    new lease(config);
});
