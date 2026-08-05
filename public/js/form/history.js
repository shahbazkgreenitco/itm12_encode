var FormHistory = function(config) {
    console.log("FormHistory loaded!");

    var t = this;
    t.config = config;

    t.content   = $("#form-history-wrapper");
    t.table     = $("#form-history-table");
    t.searchbox = t.content.find(".history-search");

    t.httpCall     = true;
    t.httpPostPath = "";
    t.data         = {};
    t.btn          = {};

    // ── Action buttons ────────────────────────────────────────────────────────
    t.buildActions = function(d) {
        var actions = [];

        actions.push(`
            <a href="${t.config.url.view}/${d.id}" target="_blank"
                class="amg-action-btn primary viewinfo" title="${t.config.translations.view}">
                <i class="bi bi-eye"></i>
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

    // ── DataTable ─────────────────────────────────────────────────────────────
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
                d.form_id     = t.config.form_id;
                d.search_query = t.searchbox.val();
            }
        },
        columns: [
            { data: 'a.id' },
            { data: 'a.form_name' },
            { data: 'a.descriptions' },
            { data: 'a.remark' },
            { data: 'a.last_created_at' },
            { data: 'a.created_by' },
            { data: 'a.updated_by' },
            { data: 'a.deleted_by' },
            {
                data: null,
                width: "80px",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return t.buildActions(row.a || row);
                }
            }
        ]
    });

    // ── Initial load ──────────────────────────────────────────────────────────
    t.dTbl.ajax.reload();

    // ── Search — Enter key or clear ───────────────────────────────────────────
    t.content.on("keyup", ".history-search", function(e) {
        if (e.key === "Enter" || this.value.length === 0) {
            t.dTbl.search($(this).val().trim()).draw();
        }
    });

    // Search icon click
    t.content.on("click", ".amg-list-searchbar__icon", function() {
        t.dTbl.search(t.searchbox.val().trim()).draw();
    });

    // ── Refresh ───────────────────────────────────────────────────────────────
    t.content.on("click", ".btn-reload-list", function(e) {
        e.preventDefault();
        t.searchbox.val("");
        t.dTbl.search("").draw();
        t.dTbl.ajax.reload();
    });

    // ── Page length ───────────────────────────────────────────────────────────
    t.content.on("change", ".form-history-page-length", function(e) {
        e.preventDefault();
        var length = parseInt($(this).val());
        if (!length) return;
        t.dTbl.page.len(length).draw(false);
    });
};