var History = function(config) {
    var t = this;
    t.config = config;
    t.page = $("#srat-history-wrapper");
    t.table = t.page.find("#mytable");
    t.descriptonMdl = t.page.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    t.httpCall = true;
    t.httpPostPath = "";
    t.searchbox = t.table.find(".searchbox");
    t.btn = {};
    t.tblHelpers = {
        pablevel: function() {
            return function(d) {
                var a = [];
                a.push('<div><span>' + d.hierarchy_approval + '</span></div>');
                if(d.hierarchy_approval == 2) {
                    a.push('<div><span class="ie">'+d.required_minimum_approvals+'</span></div>');
                }
                return a.join('');
            }
        },
    };
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: false,
        lengthChange: false,
        searching: false,
        aoColumnDefs: [{
            targets: 2,
            render: t.tblHelpers.pablevel()
        }],
        order: [[5, "desc"]],
        serverSide: true,
        fixedColumns: false,
        ajax: { 
            url: t.config.url.historyAjax, 
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $(".user-list-search").val();
                d.pab_id = t.config.pab_id;
            }
        },
        dom: "<'dt-top'<'left'f><'right'l>>" +
        "tr" +
        "<'dt-bottom'<'left'i><'right'p>>",
        columns: [
            {data: "a.name",
                render: function(data,type,row){
                    if(!data){
                        return '';
                    }
                    var data=String(data);
                    if(data.length > 20){
                        var truncated = truncateHtml(data, 20);
                        return '<div data-bs-toggle="tooltip" title="' + escapeHtml(data) + '">' +
                        escapeHtml(truncated) + '...' +
                        '</div>';
                    } else {
                        return  data;
                    }
                },
                orderable: true 
            },
            {data: 'a.company_name'},
            {data: 'a'},
            {data: 'a.required_minimum_approvals'},
            {data: 'a.description',
                render: function(data, type, row) {
                    if (!data) {
                        return '';
                    }
                    data = String(data);

                    if (data.length > 30) {
                        var truncated = truncateHtml(data, 30);
                        return truncated+ '<a class="read-more" style="cursor:pointer" data-full-text="' + escapeHtml(data) + '">...Read More</a>';
                    } else {
                        return  data ;
                    }
                }
            },
            {data: 'a.last_updated_at'},
            {data: 'a.updated_by'},
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            var searchBox = '<div class="input-group table-search-btns">' +
                '<input type="text" class="form-control searchbox plain-search" placeholder="'+ config.translations.serach_option +'" />'+
                '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="top" data-original-title="Search"><i class="ps-icon plain-search-icon"></i></span>'+
                '<a href="'+t.config.url.historyList+'" class="input-group-addon" data-toggle="tooltip" data-placement="left" data-original-title="Back List"><i class="fa fa-arrow-left"></i></a>';
            
            searchBox += '</div>';
            $("#mytable_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");
            $(searchBox).insertBefore("#mytable_filter");
            $("#mytable_filter").remove();
            $("#mytable_length").find("select").select2();
            $("#mytable_wrapper .plain-search").on("keyup", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    t.config.search = v;
                    api.search(this.value).draw();
                }
            });
            t.page.on("click", ".read-more", function(e) {
                e.preventDefault();
                var fullText = $(this).data("full-text");
                $(t.descriptonMdl.body).html(fullText);
                $(t.descriptonMdl).modal("show");
            });
            t.scheduleTableLayoutSync();
        },
        drawCallback: function() {
            var api = this.api();
            setTimeout(function() {
                api.columns.adjust();
                if (api.fixedColumns) {
                    var fc = api.fixedColumns();
                    if (fc && fc.relayout) {
                        fc.relayout();
                    }
                }
            }, 100);
            t.scheduleTableLayoutSync();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    t.scheduleTableLayoutSync = function() {
        setTimeout(function() {
            if (!t.dTbl) return;
            t.dTbl.columns.adjust();
            if (t.dTbl.fixedColumns) {
                var fc = t.dTbl.fixedColumns();
                if (fc && fc.relayout) {
                    fc.relayout();
                }
            }
        }, 100);
    };

    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    function escapeHtml(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
    }

    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };

    $(document).on("keyup", ".user-list-search", function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            var v = $(this).val().trim();
            if (v === false) {
                alert("Please enter a valid value for search");
                return false;
            }
            t.dTbl.search(v).draw();
        }
    });

    t.reload = function () {
        t.dTbl.ajax.reload();
    };
    t.page.find(".srat-group-list-page-length").on("change", function () {
        t.dTbl.page.len($(this).val()).draw();
    });

    t.page.on("click",'.btn-reload-list', $.proxy(t.reload));
};

