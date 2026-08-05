var BulkImportPhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.tabBar = t.content.find('.tab-bar');
    t.infoTab = $("section.content").find("#info-tab");

    var form = $("#importForm");
    var file = form.find("#hiddenFileInput");
    
    form.on("submit", function (e) {
        var file_name = file.val();
        if (file_name == "" || file[0].type != "file") {
            var data = {
                'msg': config.translations.import_file,
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }
        var name_arr = file_name.split(".");
        name_arr.reverse();
        if (name_arr[0] != "xlsx" && name_arr[0] != "XLSX") {
            var data = {
                'msg': config.translations.Please_upload_valid_xlsx,
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }
    });

};
var BulkImportInfoPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find('#import-info-tab');
    t.table = t.tab.find('#tblHistory');
    t.showentries = t.tab.find('#showSelect');

    t.dTbl = t.table.DataTable({
        language: datatable_footer_translations(t.config.datatable_translations),
        autoWidth: false,
        lengthChange: false,
        dom: "lrtip",
        pageLength: 10,
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
                d.module_id = 5;
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
                render: function(d) {
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
            $(document).on('click', '.amg-list-searchbar__icon', function() {
                var v = $("#tableSearch").val().trim();
                t.dTbl.search(v).draw();
            });
            
            $(document).on('keyup', '#tableSearch', function(e) {
                if (e.keyCode === 13) {
                    t.search(e);
                }
            });

            $(document).on("click", '.btn-reload-list' ,function(e) {
                e.preventDefault();
                t.reload();
            });
        }
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


    t.reload = function () {
        $("#tableSearch").val('');
        t.dTbl.search('').ajax.reload(null, false);
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#tableSearch").val().trim();
            if (v === false) {
                t.config.search = "";
                alert(config.translations.please_enter_valid_search);
                return false;
            }
            t.config.search = v;
            t.dTbl.search(v).draw();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
        }
    };

};

var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.bulkDeletePhase = new BulkImportPhase(config);
    t.bulkImportInfoPhase = new BulkImportInfoPhase(config);

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
}