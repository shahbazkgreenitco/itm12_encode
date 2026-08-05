var EscalationHistory = function(config) {
    var t = this;

    t.config = config || {};
    t.page = $("#main-user-list-wrapper");
    t.table = t.page.find("#escalationTable");
    t.searchInput = t.page.find(".plain-search");
    t.pageLength = t.page.find(".userModulePageLenth");
    t.btn = {
        reload: t.page.find(".btn-reload-list")
    };
    t.searchTimer = null;

    t.safeText = function(value, fallback) {
        var text = $.trim(String(value == null ? "" : value));
        return text === "" || text === "null" || text === "undefined" ? (fallback || "") : text;
    };

    t.getEscalateFor = function(record) {
        return t.safeText(record.esclate_group_name, "") ? "Group" : "User";
    };

    t.getEscalateTo = function(record) {
        return t.safeText(record.esclate_group_name, t.safeText(record.esclate_to, "-"));
    };

    t.reload = function() {
        if (t.dTbl) {
            t.dTbl.ajax.reload();
        }
    };

    t.handleSearchInput = function() {
        window.clearTimeout(t.searchTimer);
        t.searchTimer = window.setTimeout(function() {
            t.reload();
        }, 250);
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        deferLoading: 0,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        lengthChange: false,
        searching: false,
        order: [[8, "desc"]],
        ajax: {
            url: t.config.url.esclationHistoryAjax,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $.trim(t.searchInput.val());
                d.category_id = t.config.category_id;
            }
        },
        columns: [
            { data: "a.esclation_id" },
            { data: "a.problem_category" },
            {
                data: "a",
                render: function(data, type, row) {
                    return t.getEscalateFor(row.a || {});
                }
            },
            { data: "a.esclate_stage_no" },
            { data: "a.esclate_trigger_time_hrs" },
            {
                data: "a",
                render: function(data, type, row) {
                    return t.getEscalateTo(row.a || {});
                }
            },
            {
                data: "a.sla_count",
                render: function(data) {
                    return String(data) === "1" ? "Yes" : "No";
                }
            },
            {
                data: "a.technician_mark_cc",
                render: function(data) {
                    return String(data) === "1" ? "Yes" : "No";
                }
            },
            { data: "a.updated_on" },
            {
                data: "a.updated_by",
                render: function(data) {
                    return t.safeText(data, "-");
                }
            },
            {
                data: "a.deleted_on",
                render: function(data) {
                    return t.safeText(data, "-");
                }
            },
            {
                data: "a.deleted_by",
                render: function(data) {
                    return t.safeText(data, "-");
                }
            }
        ]
    });

    t.btn.reload.on("click", $.proxy(t.reload, t));

    t.searchInput.on("input", $.proxy(t.handleSearchInput, t));
    t.searchInput.on("keydown", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            window.clearTimeout(t.searchTimer);
            t.reload();
        }
    });

    t.pageLength.on("change", function() {
        var length = parseInt($(this).val(), 10) || 10;
        t.dTbl.page.len(length).draw();
    });

    t.reload();
};
