var BulkImportPhase = function (config) {
  var t = this;
  t.config = config;

  var form = $("#confirmForm");
  var file = form.find("#import_file");

  form.on("submit", function (e) {
    var file_name = file.val();

    if (file_name === "" || file[0].files.length === 0) {
      sweetAlert("center", "error", {
        msg: config.translations.import_file,
      });
      e.preventDefault();
      return;
    }
    var ext = file_name.split(".").pop().toLowerCase();
    if (ext !== "xlsx") {
      sweetAlert("center", "error", {
        msg: config.translations.Please_upload_valid_xlsx,
      });
      e.preventDefault();
      return;
    }
  });
};

var BulkImportInfoPhase = function (config) {
  var t = this;
  t.config = config;

  t.tab = $("section.content").find("#import-info-tab");
  t.table = t.tab.find("#tblHistory");
  t.showentries = t.tab.find("#showSelect");
  t.dTbl = t.table.DataTable({
    autoWidth: false,
    lengthChange: false,
    dom: "lrtip",

    order: [[5, "desc"]],
    processing: true,
    serverSide: true,

    ajax: {
      url: t.config.bulk_import_info,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.module_id = 6;
        d.action_type = 1;
      },
    },

    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
      },

      { data: "a.doc_name" },
      { data: "a.tot_imported" },
      { data: "a.tot_success" },
      { data: "a.tot_failure" },
      { data: "a.last_updated_at" },
      { data: "a.user_name" },

      //   {
      //     data: "a",
      //     orderable: false,
      //     searchable: false,
      //     render: function (d) {
      //       return `
      //                     <a download="${d.doc_name}" 
      //                        href="${t.config.document_download}/${d.doc_path}" 
      //                        class="btn dtActbtn"
      //                        data-toggle="tooltip"
      //                        title="Download"
      //                        data-id="${d.id}">
      //                        <i class="fa fa-download"></i>
      //                     </a>
      //                 `;
      //     },
      //   },

      {
        data: "a",
        orderable: false,
        searchable: false,
        render: function (d) {
          return `
      <a download="${d.doc_name}" 
         href="${t.config.document_download}/${d.doc_path}" 
         class="btn dtActbtn d-flex align-items-center justify-content-center"
         data-toggle="tooltip"
         title="Download"
         data-id="${d.id}">
         
         <svg xmlns="http://www.w3.org/2000/svg" 
              width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="m12 16l-5-5l1.4-1.45l2.6 2.6V4h2v8.15l2.6-2.6L17 11zm-6 4q-.825 0-1.412-.587T4 18v-3h2v3h12v-3h2v3q0 .825-.587 1.413T18 20z"/>
         </svg>

      </a>
    `;
        },
      },
    ],

    fnInitComplete: function () {
      var api = this.api();

      $("#tableSearch")
        .off()
        .on("keyup", function (e) {
          if (e.keyCode === 13) {
            var v = $(this).validate_str_param();

            if (v === false) {
              alert("Please enter a valid value for search");
              return false;
            }

            api.search(v).draw();
          }
        });
    },
  });

  t.reload = function () {
    $("#tableSearch").val("");
    t.dTbl.search("").ajax.reload(null, false);
  };

  t.tab.on("click", ".amg-list-searchbar__icon", function () {
    var v = $("#tableSearch").validate_str_param();

    if (v === false) {
      alert("Please enter a valid value for search");
      return false;
    }

    t.dTbl.search(v).draw();
  });

  if (t.showentries.length) {
    t.showentries.select2({
      theme: "custom",
      minimumResultsForSearch: Infinity,
      width: "auto",
    });

    t.showentries.on("change", function () {
      var value = parseInt($(this).val(), 10);
      t.dTbl.page.len(value).draw();
    });
  }

  t.tab.on("click", ".btn-reload-list", function () {
    t.reload();
  });
};

var MyApp = function (config) {
  var t = this;

  t.content = $("#content-container");

  t.bulkImportPhase = new BulkImportPhase(config);
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
};
