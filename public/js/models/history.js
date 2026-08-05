var ModelHistoryApp = function (config) {
    var t = this;

    t.config = config;
    t.table = $("#mytable");
    t.content = $(document);

    t.dTable = null;

    t.btn = {
        reload: ".btn-reload-list",
    };

    t.init();
};

ModelHistoryApp.prototype.init = function () {
    var t = this;

    setTimeout(() => t.initDataTable(), 50);
    t.bindEvents();
};

ModelHistoryApp.prototype.initDataTable = function () {
    var t = this;

    let defaultLength = parseInt($(".user-list-page-length").val()) || 10;
    $(".user-list-page-length").val(defaultLength);

    t.dTable = t.table.DataTable({
        language: datatable_footer_translations(t.config.datatable_translations),
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        dom: "lrtip",
        order: [[1, "desc"]],
        pageLength: defaultLength,
        lengthMenu: [10, 25, 50, 100],
        lengthChange: false,
        searchDelay: 400,
        deferRender: true,

        ajax: {
            url: t.config.url.info,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d._id = t.config.info_id;

                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;

                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            },
        },

        columns: [
            { data: "a.id" },
            { data: "a.manufacturer.name" },
            { data: "a.name" },
            { data: "a.modelno" },
            { data: "a.assets_count" },
            { data: "a.depreciation.name" },
            { data: "a.category.name" },
            { data: "a.eol" },
            { data: "a.user.full_name" },
            { data: "a.updated_at_formated" },
        ],
    });
};

ModelHistoryApp.prototype.bindEvents = function () {
    var t = this;

    t.content.on("click", t.btn.reload, () => t.reload());

        $(".user-list-search").on("keyup", function (e) {
            if(e.keyCode == 13){
                t.dTable.search(this.value).draw();
            }
        });

    $(document)
        .off("change", ".user-list-page-length")
        .on("change", ".user-list-page-length", function () {
            let val = parseInt($(this).val());

            if (val && t.dTable) {
                t.dTable.page.len(val).draw();
            }
        });
};

ModelHistoryApp.prototype.reload = function () {
    var t = this;

    $(".user-list-search").val("");

    if (t.dTable) {
        t.dTable.search("").draw();
        t.dTable.ajax.reload(null, false);
    }
};

$(function () {
    new ModelHistoryApp(config);
});
