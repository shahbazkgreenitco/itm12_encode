var AccessoriesCheckFilePhase = function (config) {
    var t = this;
    t.config = config;
    t.form = $("#confirmForm");
    t.file = t.form.find("#import_file");
    t.validate = function (e) {
        var file_name = t.file.val();
        if (file_name === "" || t.file[0].files.length === 0) {
            sweetAlert("center", "error", {
                msg: t.config.translations.import_file,
            });
            e.preventDefault();
            return false;
        }

        var ext = file_name.split(".").pop().toLowerCase();
        if (ext !== "xlsx") {
            sweetAlert("center", "error", {
                msg: t.config.translations.Please_upload_valid_xlsx,
            });
            e.preventDefault();
            return false;
        }
    };

    t.bindEvents = function () {
        t.form.on("submit", $.proxy(t.validate, t));
    };
    t.bindEvents();
};



var AccessoriesImportPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find("#import-info-tab");
    t.table = t.tab.find("#tblHistory");
    t.showentries = t.tab.find("#showSelect");
    t.btn = {};
    t.btn.search = t.tab.find(".amg-list-searchbar__icon");
    t.btn.reload = t.tab.find(".btn-reload-list");
    t.btn.searchInput = $("#tableSearch");


    t.initDataTable = function () {
        t.dTbl = t.table.DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            lengthChange: false,
            searching: true,
            dom: "lrtip",
            order: [[5, "desc"]],
            ajax: {
                url: t.config.accessories_import_info,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = 3;
                    d.action_type = 4;
                }
            },

            columns: [
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: "a.doc_name",
                    name: "doc_name"
                },
                {
                    data: "a.tot_imported",
                    name: "tot_imported"
                },
                {
                    data: "a.tot_success",
                    name: "tot_success"
                },
                {
                    data: "a.tot_failure",
                    name: "tot_failure"
                },
                {
                    data: "a.last_updated_at",
                    name: "last_updated_at"
                },
                {
                    data: "a.user_name",
                    name: "user_name"
                },
                {
                    data: "a",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    render: function (d) {
                        return `
                            <a 
                                download="${d.doc_name}"
                                href="${t.config.document_download}/${d.doc_path}"
                                class="btn dtActbtn"
                                data-toggle="tooltip"
                                title="Download">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width="18"
                                     height="18"
                                     viewBox="0 0 24 24"
                                     fill="currentColor">
                                    <path d="m12 16l-5-5l1.4-1.45l2.6 2.6V4h2v8.15l2.6-2.6L17 11zm-6 4q-.825 0-1.412-.587T4 18v-3h2v3h12v-3h2v3q0 .825-.587 1.413T18 20z"/>
                                </svg>
                            </a>
                        `;
                    }
                }
            ],

            fnInitComplete: function () {
                var api = this.api();
                t.btn.searchInput.off().on("keyup", function (e) {
                    if (e.keyCode === 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert(t.config.translations.please_enter_valid_search);
                            return false;
                        }
                        api.search(v).draw();
                    }
                });
            }
        });
    };


    t.search = function () {
        var v = t.btn.searchInput.validate_str_param();
        if (v === false) {
            alert(t.config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.reload = function () {
        t.btn.searchInput.val("");
        t.dTbl.search("").ajax.reload(null, false);
    };

    t.bindEvents = function () {
        t.btn.search.on("click", $.proxy(t.search, t));
        t.btn.reload.on("click", $.proxy(t.reload, t));
        if (t.showentries.length) {
            t.showentries.select2({
                theme: "custom",
                minimumResultsForSearch: Infinity,
                width: "auto"
            });
            t.showentries.on("change", function () {
                t.dTbl.page.len($(this).val()).draw();
            });
        }
    };
    t.initDataTable();
    t.bindEvents();
};




var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.AccessoriesCheckFilePhase = new AccessoriesCheckFilePhase(config);
    t.AccessoriesImportPhase = new AccessoriesImportPhase(config);

    t.content.on("click", ".black-slide-links a", function (e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active")
            .removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active")
            .slideUp("fast", function () {
                $(this).removeClass("active");
                t.content.find(thisNav.attr("href"))
                    .addClass("active")
                    .slideDown("slow");
            });
    });
};