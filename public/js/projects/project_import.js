var BulkImportPhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.tabBar = t.content.find('.tab-bar');
    t.infoTab = $("section.content").find("#info-tab");

};
var BulkImportInfoPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find('#import-info-tab');
    t.table = t.tab.find('#tblHistory');
    t.showentries = t.tab.find('#showSelect');

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        lengthChange: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        }, {
            targets: 0,
            render: function (d) {
                var a = [];
                a.push("<a download = " + d.doc_name + " href=\"" + t.config.document_download + "/" + d.doc_path + "\" class='btn dtActbtn' data-toggle='tooltip' data-original-title='" + config.translations.Download_Document + "' data-placement='right' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                return a.join(' ');
            }

        }],
        order: [
            [5, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.bulk_import_info,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.module_id = 1;
                d.action_type = 1
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            {
                data: 'a.doc_name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.tot_imported',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.tot_success',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.tot_failure',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.last_updated_at',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a.user_name',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b1-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: 'a',
                orderable: false,
                searchable: false,
                render: function (d) {
                    return `
                        <a download="${d.doc_name}" 
                        href="${t.config.document_download}/${d.doc_path}" 
                        class="btn dtActbtn p-0" 
                        data-toggle="tooltip" 
                        data-original-title="${config.translations.Download_Document}" 
                        data-placement="right" 
                        data-id="${d.id}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m12 16l-5-5l1.4-1.45l2.6 2.6V4h2v8.15l2.6-2.6L17 11zm-6 4q-.825 0-1.412-.587T4 18v-3h2v3h12v-3h2v3q0 .825-.587 1.413T18 20z"/></svg>
                        </a>
                    `;
                }
            },
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#tableSearch").off(".DT");
            // var searchBox = '<div class="input-group table-search-btns">'
            // searchBox += '<input type="text" class="form-control searchbox plain-search"  placeholder="' + config.translations.press_enter_with_Search + '" />';
            // searchBox += '<span class="input-group-addon btn-searchbox" data-placement="bottom" data-toggle="tooltip" data-original-title="' + config.translations.search + '"><i class="ps-icon plain-search-icon"></i></span>';
            // searchBox += '<span class="input-group-addon btn-reload-list btn_reload" data-placement="bottom" data-toggle="tooltip"  data-original-title="' + config.translations.reload + '"><i class="ps-icon fa fa-refresh"></i></span></div>';
            // $("#tblHistory_filter").empty().html(searchBox);
            $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(v).draw();
                }
            });
        }
    });


    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#tblHistory_filter .plain-search").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert(config.translations.please_enter_valid_search);
                return false;
            }
            t.config.search = v;
            t.dTbl.search(v).draw();
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.tab.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.tab.on("click", ".amg-list-searchbar__icon", () => {
        var v = $('#tableSearch').validate_str_param();

        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }

        t.dTbl.search(v).draw();
    });

    t.showentries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.showentries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

};

var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.bulkDeletePhase = new BulkImportPhase(config);
    t.bulkImportInfoPhase = new BulkImportInfoPhase(config);

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    t.content.on("click", ".black-slide-links a", function (e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function () {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            adjustVisibleTables();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        adjustVisibleTables();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        setTimeout(function () {
            adjustVisibleTables();
        }, 100);
    });

    function adjustVisibleTables() {
        $('.tab-pane.active.show table.dataTable:visible').each(function () {
            const dt = $(this).DataTable();
            if (dt && dt.columns && typeof dt.columns.adjust === 'function') {
                dt.columns.adjust();
            }
        });
    }
}