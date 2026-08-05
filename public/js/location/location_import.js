var ImportPhase = function (config) {
    var t = this;

    t.config = config;
    t.form = $("#importForm");
    t.file = t.form.find('input[name="import_file"]');

    t.init();
};

ImportPhase.prototype.init = function () {
    this.bindEvents();
};

ImportPhase.prototype.bindEvents = function () {
    var t = this;

    t.form.on("submit", function (e) {

        var file = t.file[0].files[0];

        if (!file) {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                text: "Please choose the file for Bulk Import."
            });
            return false;
        }

        var ext = file.name.split('.').pop().toLowerCase();

        if (ext !== "xlsx" && ext !== "xls") {
            e.preventDefault();
            Swal.fire({
                icon: "error",
                text: t.config.translations.Please_upload_valid_xlsx
            });
            return false;
        }
    });
};

var ImportHistoryPhase = function (config) {
    var t = this;

    t.config = config;
    t.table = $("#tblHistory");

    t.init();
};

ImportHistoryPhase.prototype.init = function () {
    this.initTable();
};

ImportHistoryPhase.prototype.initTable = function () {
    var t = this;

    t.dTable = t.table.DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        destroy: true,

        ajax: {
            url: t.config.bulk_import_info,
            type: "GET"
        },

        order: [[5, 'desc']],

        columns: [
            {
                data: null,
                orderable: false,
                render: function (d) {
                    return `
                        <a href="${t.config.document_download}/${d.doc_path}"
                           class="btn btn-sm btn-light">
                           <i class="fa fa-download"></i>
                        </a>
                    `;
                }
            },
            { data: "document_name" },
            { data: "total_upload" },
            { data: "total_success" },
            { data: "total_failure" },
            { data: "updated_at" },
            { data: "user_name" }
        ]
    });
};

var MyApp = function (config) {
    this.importPhase = new ImportPhase(config);
    this.importHistoryPhase = new ImportHistoryPhase(config);
};
