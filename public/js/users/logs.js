var UserLogs = function (config) {
    var t = this;
    t.config = config;
    t.content = $("#main-wrapper-user-log");
    t.table = t.content.find("#user-logs");
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");
    t.reloadbtn = t.content.find(".btn-reload-list");
    t.pageLengthSelect = t.content.find(".userlog-page-length");
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching : false,
        lengthChange: false,
        pageLength: parseInt(t.pageLengthSelect.val(), 10),
        ajax: {
            url: t.config.url.list,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.searchbox.val();
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        columns: [
            { data: 'a.action' },
            {
                data: "a.created_at_format",
                render: function (data, type, row) {
                    return `<span class="b5-text" style="color:#515151">${data}</span>`;
                }
            },
            {
                data: "a.target_user",
                defaultContent: "-",
                render: function (data, type, row) {
                    if (type !== 'display') return data;

                    var name = data || "-";
                    var img = row.a.performed_by_profile_img;

                    var avatarHtml = img
                        ? `<img src="${img}" class="rounded-circle" width="22" height="22" alt="${name}">`
                        : `<span class="user-list-avatar user-list-avatar-fallback">${name.charAt(0).toUpperCase()}</span>`;

                    return `<div class="d-flex gap-2 align-items-center">${avatarHtml}<span>${name}</span></div>`;
                }
            },
        ],
        drawCallback: function (settings) {
                var $wrapper = $(this).closest('.dataTables_wrapper');

                $wrapper.find('.dataTables_info').css({
                    'float': 'left',
                    'margin-top': '15px'
                });
        },
        order: [[0, 'desc']],
        language: {
            paginate: {
                previous: "<",
                next: ">"
            }
        },
        initComplete: function () {
            $("#user-logs_filter").hide();
            if ($.fn.select2) {
                $("#user-logs_length select").select2();
            }
        }
    });

    t.getSearchValue = function () {
        var v = t.searchbox.val();
        if (typeof $.fn.validate_str_param === "function") {
            var clean = t.searchbox.validate_str_param();
            return clean === false ? false : clean;
        }
        return v;
    };

    t.search = function () {
        var v = t.getSearchValue();
        if (v === false) {
            alert(t.config.translations.please_enter_valid_search || "Please enter a valid value for search");
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.handlePageLengthChange = function (e) {
        if (e) e.preventDefault();
        
        var length = parseInt(t.pageLengthSelect.val(), 10);
        if (!t.dTbl || !length) return;
        
        t.dTbl.page.len(length).draw();
    };

    t.searchbox.on("keyup", function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            t.search();
        }
    });

    t.searchbtn.on("click", function (e) {
        e.preventDefault();
        t.search();
    });

    t.reloadbtn.on("click", function (e) {
        e.preventDefault();
        t.reload();
    });

    t.pageLengthSelect.on("change", function (e) {
        t.handlePageLengthChange(e);
    });
};   
