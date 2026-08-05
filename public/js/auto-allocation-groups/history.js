var AutoAllocGroupsHistory = function (config) {
    var t = this;

    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.search = t.config.search || "";

    t.page = $("#main-user-list-wrapper");
    t.content = t.page.length ? t.page : $("section.content");
    t.table = t.content.find("#mytable");
    t.pageLength = t.content.find(".history-page-length");
    t.searchbox = t.content.find(".plain-search");

    t.tr = function (key, fallback) {
        var keys = key.split(".");
        var value = t.config.translations;

        for (var i = 0; i < keys.length; i++) {
            if (value == null || typeof value[keys[i]] === "undefined") {
                return fallback;
            }
            value = value[keys[i]];
        }

        return value;
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.truncate = function (value, length) {
        var text = String(value == null ? "" : value);

        if (text.length <= length) {
            return t.escapeHtml(text || "-");
        }

        return [
            '<span title="', t.escapeHtml(text), '">',
            t.escapeHtml(text.substring(0, length)),
            '...</span>'
        ].join("");
    };

    t.renderDescription = function (data) {
        var text = String(data == null ? "" : data);

        if (!text) {
            return "-";
        }

        if (text.length <= 40) {
            return t.escapeHtml(text);
        }

        return [
            t.escapeHtml(text.substring(0, 40)),
            '<a class="read-more" style="cursor:pointer" data-full-text="',
            t.escapeHtml(text),
            '">...Read More</a>'
        ].join("");
    };

    t.renderValue = function (data) {
        return data == null || data === "" ? "-" : t.escapeHtml(data);
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        order: [[8, "desc"]],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        searching: false,
        scrollX: true,
        scrollCollapse: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        lengthChange: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        ajax: {
            url: t.config.url.historyAjax,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || $.trim(t.searchbox.val() || "");
                d.allocation_id = t.config.allocation_id;
            }
        },
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        language: {
            emptyTable: t.tr("no_records_found", "No matching records found"),
            zeroRecords: t.tr("no_records_found", "No matching records found")
        },
        columns: [
            {
                data: "name",
                render: function (data, type) {
                    return type === "display" ? t.truncate(data, 24) : (data || "");
                }
            },
            {
                data: "company_name",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "dept_name",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "desc",
                render: function (data, type) {
                    return type === "display" ? t.renderDescription(data) : (data || "");
                }
            },
            {
                data: "location_approval_required",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "location_approval_required_for",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "remark",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            { data: "last_created_at", defaultContent: "-" },
            { data: "last_updated_at", defaultContent: "-" },
            {
                data: "created_by",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "updated_by",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            },
            {
                data: "deleted_by",
                render: function (data, type) {
                    return type === "display" ? t.renderValue(data) : (data || "");
                }
            }
        ]
    });

    t.reload = function () {
        var v = typeof t.searchbox.validate_str_param === "function"
            ? t.searchbox.validate_str_param()
            : $.trim(t.searchbox.val() || "");

        if (v === false) {
            t.config.search = "";
            alert(t.tr("please_enter_valid_search", "Please enter a valid value for search"));
            return false;
        }

        t.config.search = v;
        t.dTbl.ajax.reload(null, false);
    };

    t.search = function (e) {
        if (e && e.type === "keyup" && e.keyCode !== 13 && $.trim(t.searchbox.val() || "") !== "") {
            return;
        }

        t.reload();
    };

    t.changePageLength = function () {
        t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw();
    };

    t.showDescription = function (e) {
        var fullText;

        e.preventDefault();
        fullText = $(e.currentTarget).data("full-text") || "";

        if (window.Swal && typeof Swal.fire === "function") {
            Swal.fire({
                title: t.tr("form.group_description", "Group Description"),
                html: t.escapeHtml(fullText),
                width: 700
            });
            return;
        }

        alert(fullText);
    };

    t.dTbl.ajax.reload();

    t.content.on("keyup", ".plain-search", $.proxy(t.search));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.content.on("change", ".history-page-length", $.proxy(t.changePageLength));
    t.content.on("click", ".read-more", $.proxy(t.showDescription));
};
