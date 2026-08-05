var HistoryApp = function (config) {
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

HistoryApp.prototype.init = function () {
  var t = this;
  setTimeout(() => t.initDataTable(), 50);
  t.bindEvents();
};

HistoryApp.prototype.initDataTable = function () {
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
    order: [[0, "desc"]],
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
          let columnName =
            d.columns[columnIndex].name || d.columns[columnIndex].data;

          d.sorted_column_name = columnName;
          d.sorted_direction = direction;
        }
      },
    },

    columns: [
      { data: "a.id", name: "a.id", render: t.renderText("b2-text") },
      { data: "a.name", name: "a.name", render: t.renderText("b1-text") },
      {
        data: "a.category_type",
        name: "a.category_type",
        render: t.renderText("b1-text"),
      },
      {
        data: "a.account_type",
        name: "a.account_type",
        render: t.renderText("b1-text"),
      },
      {
        data: "a.require_acceptance",
        name: "a.require_acceptance",
        render: (d) => (d == 1 ? t.CHECK_ICO() : ""),
      },

      {
        data: "a.eula",
        name: "a.eula",
        render: (d) => (d == 1 ? t.CHECK_ICO() : ""),
      },

      {
        data: "a.checkin_email",
        name: "a.checkin_email",
        render: (d) => (d == 1 ? t.CHECK_ICO() : ""),
      },

      {
        data: "a.checkout_email_accept",
        name: "a.checkout_email_accept",
        render: (d) => (d == 1 ? t.CHECK_ICO() : ""),
      },

      {
        data: "a.threshold_count",
        name: "a.threshold_count",
        render: t.renderText("b1-text"),
      },
      {
        data: "a.updated_by",
        name: "a.updated_by",
        render: t.renderBadge(),
      },
      {
        data: "a.updated_at",
        name: "a.updated_at",
        render: t.renderText("b1-text"),
      },
    ],
  });
};

HistoryApp.prototype.renderBadge = function (cls = "badge bg-primary me-1") {
  return function (data, type) {
    if (type !== "display") return data;

    if (!data) return "";

    return `<span class="${cls}">${data}</span>`;
  };
};

HistoryApp.prototype.CHECK_ICO = function () {
  return `
  <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
    <path fill-rule="evenodd" clip-rule="evenodd" 
    d="M9 18C10.1819 18 11.3522 17.7672 12.4442 17.3149C13.5361 16.8626 14.5282 16.1997 15.364 15.364C16.1997 14.5282 16.8626 13.5361 17.3149 12.4442C17.7672 11.3522 18 10.1819 18 9C18 7.8181 17.7672 6.64778 17.3149 5.55585C16.8626 4.46392 16.1997 3.47177 15.364 2.63604C14.5282 1.80031 13.5361 1.13738 12.4442 0.685084C11.3522 0.232792 10.1819 0 9 0C6.61305 0 4.32387 0.948211 2.63604 2.63604C0.948212 4.32387 0 6.61305 0 9C0 11.3869 0.948212 13.6761 2.63604 15.364C4.32387 17.0518 6.61305 18 9 18ZM8.768 12.64L13.768 6.64L12.232 5.36L7.932 10.519L5.707 8.293L4.293 9.707L7.293 12.707L8.067 13.481L8.768 12.64Z" 
    fill="#186B43"/>
  </svg>
  `;
};

HistoryApp.prototype.renderText = function (cls) {
  return function (data, type) {
    if (type !== "display") return data;
    return `<span class="${cls}">${data ?? ""}</span>`;
  };
};

HistoryApp.prototype.bindEvents = function () {
  var t = this;

  t.content.on("click", t.btn.reload, () => t.reload());

  $(".user-list-search").on("keydown", function (e) {
    if(e.key == 'Enter' || e.keyCode == '13'){
      setTimeout(() => {
        if (t.dTable) {
          t.dTable.search(this.value).draw();
        }
      }, 400);
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

HistoryApp.prototype.reload = function () {
  var t = this;


  if (t.dTable) {
    t.dTable.ajax.reload(null, false);
  }
};

$(function () {
  new HistoryApp(config);
});
