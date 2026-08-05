var AutoAllocGroupMembersHistory = function (config) {
    var t = this;

    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.search = t.config.search || "";

    t.page = $("#main-user-list-wrapper");
    t.content = t.page.length ? t.page : $("section.content");
    t.table = t.content.find("#mytable");
    t.pageLength = t.content.find(".historyPageLength");
    t.searchbox = t.content.find(".plain-search");

    t.locationMdl = t.content.find("#users_modal");
    t.locationMdl.body = t.locationMdl.find(".modal-body");

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

    t.renderLocationBadges = function (data) {
        var locations;
        var visibleLocations;
        var html = "";

        if (!data) {
            return "-";
        }

        locations = String(data).split(",").map(function (item) {
            return $.trim(item);
        }).filter(Boolean);

        visibleLocations = locations.slice(0, 2);
        $.each(visibleLocations, function (index, location) {
            var label = location.length > 20 ? location.substring(0, 20) + "..." : location;
            html += '<span class="location-access-item" style="margin:1.5px" title="' + t.escapeHtml(location) + '">' + t.escapeHtml(label) + "</span>";
            if (index < visibleLocations.length - 1 || locations.length > visibleLocations.length) {
                html += ", ";
            }
        });

        if (locations.length > 2) {
            html += '<a class="read-more" style="cursor:pointer" data-full-text="' + t.escapeHtml(data) + '"> +' + (locations.length - 2) + "</a>";
        }

        return html || "-";
    };

    t.renderUserLocation = function (record) {
        var parts = [];

        if (record && record.current_location_name) {
            parts.push("<b>C: </b>" + t.escapeHtml(record.current_location_name));
        }
        if (record && record.base_location_name) {
            parts.push("<b>B: </b>" + t.escapeHtml(record.base_location_name));
        }

        return parts.length ? parts.join("<br/>") : "-";
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        deferLoading: 0,
        searching: false,
        lengthChange: false,
        scrollX: true,
        scrollCollapse: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        order: [[6, "desc"]],
        ajax: {
            url: t.config.url.historyAjax,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || $.trim(t.searchbox.val() || "");
                d.groupId = t.config.groupId;
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
            { data: "user_name", defaultContent: "-" },
            {
                data: null,
                render: function (data, type, row) {
                    return type === "display" ? t.renderUserLocation(row) : "";
                }
            },
            {
                data: "location_name",
                render: function (data, type) {
                    return type === "display" ? t.renderLocationBadges(data) : (data || "");
                }
            },
            { data: "max_ticket_per_hr", defaultContent: "-" },
            { data: "max_ticket_per_day", defaultContent: "-" },
            { data: "remark", defaultContent: "-" },
            { data: "last_created_at", defaultContent: "-" },
            { data: "last_updated_at", defaultContent: "-" },
            { data: "created_by", defaultContent: "-" },
            { data: "updated_by", defaultContent: "-" },
            { data: "deleted_by", defaultContent: "-" }
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).find('td').addClass('b5-text');
        },
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

    t.showLocations = function (e) {
        var fullText;
        var locationArray;

        e.preventDefault();
        fullText = $(e.currentTarget).data("full-text") || "";
        locationArray = String(fullText).split(",").map(function (item) {
            return $.trim(item);
        }).filter(Boolean);

        t.locationMdl.body.empty();
        $.each(locationArray, function (index, location) {
            var label = location.length > 20 ? location.substring(0, 20) + "..." : location;
            t.locationMdl.body.append('<span class="badge" style="margin:1.5px" title="' + t.escapeHtml(location) + '">' + t.escapeHtml(label) + "</span>");
            if (index < locationArray.length - 1) {
                t.locationMdl.body.append(", ");
            }
        });
        t.locationMdl.modal("show");
    };

    t.dTbl.ajax.reload();

    t.content.on("keyup", ".plain-search", $.proxy(t.search));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.content.on("change", ".historyPageLength", $.proxy(t.changePageLength));
    t.content.on("click", ".read-more", $.proxy(t.showLocations));
};
