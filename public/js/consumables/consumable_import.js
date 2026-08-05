var ConsumableCheckFilePhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.tabBar = t.content.find('.tab-bar');
    t.infoTab = $("section.content").find("#info-tab");

    var form = $("#confirmForm");
    var file = form.find("#import_file");

    form.on("submit", function (e) {
        var file_name = file.val();
        if (file_name == "" || file[0].type != "file") {
            var data = {
                'msg': "Please choose the file for Bulk Import.",
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }
        var name_arr = file_name.split(".");
        name_arr.reverse();
        if (name_arr[0] != "xlsx" && name_arr[0] != "XLSX") {
            var data = {
                'msg': "Please upload valid xlsx type file",
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }
    });

};
var ConsumableImportPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find('#import-info-tab');
    t.table = t.tab.find('#tblHistory');
    t.showentries = t.tab.find('#showSelect');

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        lengthChange: false,
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        order: [
            [5, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.consumable_import_info,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.device_id = 4;
                d.action_type = 4;
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
            // var searchBox  = '<div class="input-group table-search-btns">'
            // searchBox +='<input type="text" class="form-control searchbox plain-search" id="search-holidays" placeholder="Press enter with search text" />';
            // searchBox += '<span class="input-group-addon btn-searchbox" data-toggle="tooltip"  data-placement="left" data-original-title="search"><i class="ps-icon plain-search-icon"></i></span>';
            // searchBox += '<span class="input-group-addon btn-reload-list" data-toggle="tooltip" data-placement="left" data-original-title="Refresh_List"><i class="ps-icon fa fa-refresh"></i></span>';
            // searchBox += '</div>';
            // $("#tblHistory_filter").empty().html(searchBox);
            // $("#tblHistory_length").find("select").select2();
            $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });

            $('.amg-list-searchbar__icon').on('click', function () {
                var v = $('#tableSearch').validate_str_param();
                if (v === false) {
                    alert("Please enter a valid value for search");
                    return false;
                }

                api.search(v).draw();
            });

            $('.btn-reload-list').click(function (e) {
                e.preventDefault();
                var v = $("#tableSearch").validate_str_param();
                if (v === false) {
                    return false;
                }
                api.search(v).draw();
            });
        }
    });

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#tblHistory_wrapper .plain-search").validate_str_param();
        if (v === false) {
            alert("Please enter valid search");
            return false;
        }
        t.dTbl.search(v).draw();
    };


    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.showentries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.showentries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.tab.on("click", "#btn_reload", $.proxy(t.reload));
    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));

};

var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.ConsumableCheckFilePhase = new ConsumableCheckFilePhase(config);
    t.ConsumableImportPhase = new ConsumableImportPhase(config);

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
