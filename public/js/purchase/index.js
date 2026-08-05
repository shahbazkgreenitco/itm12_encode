var Purchase = function (config) {
  var t = this;

  t.config = config;
  t.table = $("#mytable");
  // t.content = $("section.content");
  // t.content = $("#main-purchase-list-wrapper");
  t.content = $(document);

  //
  t.modal = $("#purchaseModal");
  t.form = $("#purchaseForm");


  t.submitUrl = "";
  t.editId = null;

  t.dTable = null;

  t.btn = {
    reload: ".btn-reload-list",
    add: ".open-add-modal",
    submit: $(".btn-save-purchase"),
    download: ".btn-purchase-export",
    filter: ".btn-filter",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    import: ".btn-import-purchase",
  };

  // t.btn = {
  //   reload: ".btn-reload-list",
  //   add: ".open-add-modal",
  //   download: ".btn-purchase-export",
  //   filter: ".btn-filter",
  //   openFilter: ".btn-open-filter",
  //   clearFilter: ".btn-clear-filter",
  // };

  t.filters = {
    wrapper: $("#purchaseFilterModal"),
    location: null,
    supplier: null,
    status: null,
    date: null,
    daterange: null,
  };

  t.init();
};

Purchase.prototype.init = function () {
  var t = this;

  setTimeout(() => t.initDataTable(), 50);
  t.initFilters();
  t.initFormDropdowns();
  t.updateFilterCount();
  t.clearFilters();
  t.initValidation();

  $(".datepicker").datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
  });

  $("#invoice_date").on("changeDate", function (e) {
    let selectedDate = e.date;

    $("#received_date").datepicker("setStartDate", selectedDate);
    $("#received_date").datepicker("update", "");
  });

  t.bindEvents();
};

Purchase.prototype.initDataTable = function () {
  var t = this;

  if ($.fn.DataTable.isDataTable("#mytable")) {
    t.table.DataTable().clear().destroy();
    $("#mytable tbody").empty();
  }

  let defaultLength = parseInt($(".user-list-page-length").val()) || 10;
  $(".user-list-page-length").val(defaultLength);

  t.dTable = t.table.DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    stateSave: false,
    searchDelay: 500,

    // pageLength: defaultLength,
    // lengthChange: false,
    pageLength: defaultLength,
    lengthMenu: [10, 25, 50, 100],
    lengthChange: false,

    scrollX: true,
    autoWidth: false,
    scrollY: "60vh",
    scrollCollapse: true,
    ordering: true,
    order: [[10, "desc"]],
    dom: "lrtip",
    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.filters = t.getFilters();

        if (d.order && d.order.length > 0) {
          let order = d.order[0];

          let columnIndex = order.column;
          let direction = order.dir;

          // map column index → column name
          let columnMap = {
            1: "a.invoice_date_on",
            3: "a.received_date_on",
            4: "a.loc_name",
            5: "a.company_name",
            6: "a.supplier_name",
            8: "a.status",
            9: "a.doc_count",
            10: "a.created_at",
            11: "a.updated_on",
          };

          d.sorted_column_name = columnMap[columnIndex] || "a.created_at";
          d.sorted_direction = direction;
        }
      },
    },

    columns: [
      { data: "a.id", defaultContent: "-" },
      { data: "a.invoice_date_on", defaultContent: "-" },

      {
        data: null,
        render: function (d, type) {
          if (type !== "display") return d?.a?.invoice_no || "";

          return `
          <div class="user-list-stack">
          <span>${d?.a?.invoice_no || "-"}</span>
          <small>${d?.a?.po_number || ""}</small>
          </div>
          `;
        },
      },

      { data: "a.received_date_on", defaultContent: "-" },
      { data: "a.loc_name", defaultContent: "-" },
      { data: "a.company_name", defaultContent: "-" },
      { data: "a.supplier_name", defaultContent: "-" },

      {
        data: null,
        render: function (d, type) {
          if (type !== "display") return d?.a?.bill_amount || "";

          return `
          <div>
          <strong>${d?.a?.currency || ""} ${d?.a?.bill_amount || ""}</strong><br>
          <small>${d?.a?.total_amount || ""}</small>
          </div>
          `;
        },
      },
      {
        data: "a.status",
        render: function (d) {
          let cls =
            (d || "").toLowerCase() === "active" ? "is-active" : "is-inactive";

          return `
                        <span class="user-list-status ${cls}">
                            <span class="user-list-status-label">${d || "-"}</span>
                        </span>
                    `;
        },
      },

      { data: "a.doc_count", defaultContent: "0" },
      { data: "a.created_at", defaultContent: "-" },
      { data: "a.updated_on", defaultContent: "-" },

      {
        data: "a",
        orderable: false,
        searchable: false,
        render: function (d, type) {
          if (type !== "display") return "";
          return t.buildActions(d, type);
        },
      },
    ],

    initComplete: function () {
      t.bindListControls();
    },
  });
};

// FILTERS

Purchase.prototype.initFilters = function () {
  var t = this;

  t.filters.location = $("#filter_by_location");
  t.filters.supplier = $("#filter_by_supplier");
  t.filters.status = $("#filter_by_status");
  t.filters.date = $("#filter_by_date");
  t.filters.daterange = $("#daterange");

  t.loadLocationFilter();
  t.loadSupplierFilter();
};

Purchase.prototype.getFilters = function () {
  var t = this;

  return {
    location: t.filters.location?.val() || "",
    supplier: t.filters.supplier?.val() || "",
    status: t.filters.status?.val() || "",
    date: t.filters.date?.val() || "",
    daterange: t.filters.daterange?.val() || "",
  };
};

Purchase.prototype.applyFilters = function () {
  var t = this;

  t.reload();
  t.updateFilterCount();

  if (t.filters.wrapper.length) {
    t.filters.wrapper.modal("hide");
  }
};

Purchase.prototype.clearFilters = function () {
  var t = this;

  $.each(t.filters, function (key, el) {
    if (el && el.val) {
      el.val("").trigger("change");
    }
  });
  t.updateFilterCount();
  t.reload();
};

Purchase.prototype.loadLocationFilter = function () {
  var t = this;

  if (!t.filters.location.length) return;

  t.filters.location.select2({
    width: "100%",
    allowClear: true,
    placeholder: "Select Location",

    dropdownParent: $("#purchaseFilterModal"),

    ajax: {
      url: t.config.url.getLocationByAjax,
      dataType: "json",
      delay: 300,

      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        let items = data.data || [];

        return {
          results: items.map(function (item) {
            return {
              id: item.a.id,
              text: item.a.text,
            };
          }),
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
  });
};

Purchase.prototype.loadSupplierFilter = function () {
  var t = this;

  if (!t.filters.supplier.length) return;

  t.filters.supplier.select2({
    width: "100%",
    // allowClear: true,
    placeholder: "Select Supplier",

    dropdownParent: $("#purchaseFilterModal"),

    ajax: {
      url: t.config.url.getSupplierByAjax,
      dataType: "json",
      delay: 300,

      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        return {
          results: data.results || [],
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
  });
};

Purchase.prototype.initFormDropdowns = function () {
  var t = this;

  // COMPANY DROPDOWN
  $("#company_id").select2({
    width: "90%",
    dropdownParent: $("#purchaseModal"),
    ajax: {
      url: t.config.url.getCompanyByAjax,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        return {
          results: data.results || [],
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
    allowClear: true,
    placeholder: "Select Company",
  });

  $("#supplier_id").select2({
    width: "90%",
    dropdownParent: $("#purchaseModal"),
    minimumResultsForSearch: 0,
    ajax: {
      url: t.config.url.getSupplierByAjax,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        return {
          results: data.results || [],
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
    placeholder: "Select Supplier",
  });

  // LOCATION DROPDOWN
  $("#location_id").select2({
    width: "90%",
    dropdownParent: $("#purchaseModal"),
    ajax: {
      url: t.config.url.getLocationByAjax,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        let items = data.data || [];

        return {
          results: items.map(function (item) {
            return {
              id: item.a.id,
              text: item.a.text,
            };
          }),
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
    placeholder: "Select Location",
  });
  // TAX DROPDOWN

  $("#tax_id").select2({
    width: "100%",
    dropdownParent: $("#purchaseModal"),
    ajax: {
      url: t.config.url.custom_tax,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults: function (data) {
        return {
          results: data.results || [],
          pagination: {
            more: data.pagination?.more || false,
          },
        };
      },
    },
    placeholder: "Select Tax",
  });

  $("#currency").select2({
    width: "90%",
    dropdownParent: $("#purchaseModal"),
    placeholder: "Select Currency",
  });
};

// Purchase.prototype.loadTaxElements = function () {
//   var t = this;

//   let taxId = $("#tax_id").val();
//   let container = $(".customOptionsHolders");

//   container.empty();

//   if (!taxId) return;

//   $.get(t.config.url.custom_element + "/" + taxId, function (res) {
//     if (!res.data || !res.data.length) return;

//     let row = $('<div class="row g-4"></div>');

//     $.each(res.data, function (i, v) {
//       let prevVal = t.loadedTaxData?.[v.tax_element] || "";

//       row.append(`
//                 <div class="col-md-6">
//                     <label class="form-label fw-bold">${v.tax_element}</label>

//                     <div class="input-group input-group-lg">
//                         <span class="input-group-text red-bg">
//                             <i class="bi bi-percent"></i>
//                         </span>

//                         <input type="number"
//                             name="tax_percentage[${v.id}]"
//                             class="form-control"
//                             min="0"
//                             max="100"
//                             value="${prevVal}"
//                             placeholder="Enter %">
//                     </div>
//                 </div>
//             `);
//     });

//     container.append(row);
//   });
// };

Purchase.prototype.loadTaxElements = function () {
  var t = this;

  let taxId = $("#tax_id").val();
  let container = $(".customOptionsHolders");

  container.empty();

  if (!taxId) return;

  if (container.data("loading")) return;
  container.data("loading", true);

  $.get(t.config.url.custom_element + "/" + taxId, function (res) {
    container.data("loading", false);

    if (!res.data || !res.data.length) return;

    let row = $('<div class="row g-4"></div>');

    $.each(res.data, function (i, v) {
      let prevVal = t.loadedTaxData?.[v.tax_element] || "";

      row.append(`
        <div class="col-md-6">
          <label class="form-label fw-bold">${v.tax_element}</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text red-bg">
              <i class="bi bi-percent"></i>
            </span>
            <input type="number"
              name="tax_percentage[${v.id}]"
              class="form-control"
              min="0"
              max="100"
              value="${prevVal}"
              placeholder="Enter %">
          </div>
        </div>
      `);
    });

    container.append(row);
  });
};

//RELOAD
Purchase.prototype.reload = function () {
  if (!this.dTable) return;

  this.dTable.ajax.reload(null, false);
};

// ACTION DROPDOWN
Purchase.prototype.buildActions = function (d) {
  var t = this;

  let html = `<div class="dropdown">
        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">`;
  if (t.hasPermission("PurchaseEdit")) {
    html += `
    <li>
      <a class="dropdown-item btn-edit-purchase d-flex align-items-center gap-2" data-id="${d.id}">
        
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
        </svg>

        ${t.config.translations.edit}
      </a>
    </li>`;
  }

  if (t.hasPermission("PurchaseDelete")) {
    html += `
    <li>
      <a class="dropdown-item text-danger d-flex align-items-center gap-2 dtActDel" data-id="${d.id}">
        
        <svg width="16" height="16" viewBox="0 0 15 17" fill="none">
          <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
        </svg>

        ${t.config.translations.delete}
      </a>
    </li>`;
  }

  if (t.hasPermission("PurchaseHistory")) {
    html += `
    <li>
      <a class="dropdown-item d-flex align-items-center gap-2 btn-history-purchase" data-id="${d.id}">
        
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M8 1.33331C4.32 1.33331 1.33333 4.31998 1.33333 7.99998H0L2.66667 10.6666L5.33333 7.99998H3.33333C3.33333 5.42665 5.42667 3.33331 8 3.33331C10.5733 3.33331 12.6667 5.42665 12.6667 7.99998C12.6667 10.5733 10.5733 12.6666 8 12.6666C6.71333 12.6666 5.54667 12.1466 4.69333 11.2933L3.28 12.7066C4.49333 13.92 6.17333 14.6666 8 14.6666C11.68 14.6666 14.6667 11.68 14.6667 7.99998C14.6667 4.31998 11.68 1.33331 8 1.33331ZM7.33333 4.66665V8.66665L10.6667 10.6666L11.3333 9.55998L8.66667 7.99998V4.66665H7.33333Z" fill="currentColor"/>
        </svg>

        ${t.config.translations.history}
      </a>
    </li>`;
  }

  if (t.hasPermission("PurchaseView")) {
    html += `
    <li>
      <a class="dropdown-item d-flex align-items-center gap-2 btn-view-purchase" data-id="${d.id}">
        
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M8 3C3.5 3 1 8 1 8C1 8 3.5 13 8 13C12.5 13 15 8 15 8C15 8 12.5 3 8 3ZM8 11C6.343 11 5 9.657 5 8C5 6.343 6.343 5 8 5C9.657 5 11 6.343 11 8C11 9.657 9.657 11 8 11Z" fill="currentColor"/>
        </svg>

        ${t.config.translations.info || "info"}
      </a>
    </li>`;
  }

  html += `</ul></div>`;

  return html;
};

// PERMISSION
Purchase.prototype.hasPermission = function (name) {
  return this.config.permissions.includes(name);
};

// EVENTS
Purchase.prototype.bindEvents = function () {
  var t = this;

  t.content.on(
    "change",
    "#filter_by_location, #filter_by_supplier, #filter_by_status, #filter_by_date, #daterange",
    function () {
      t.updateFilterCount();
    },
  );

  t.content.off("click", t.btn.reload).on("click", t.btn.reload, function (e) {
    e.preventDefault();
    t.reload();
    t.clearFilters();
  });

  $(".user-list-search").on("keypress", function (e) {
    if (e.which === 13) {
      t.dTable.search(this.value).draw();
    }
  });

  $(document)
    .off("change", ".user-list-page-length")
    .on("change", ".user-list-page-length", function () {
      let val = parseInt($(this).val());

      if (val && t.dTable) {
        t.dTable.page.len(val).draw(false);
      }
    });

  t.content
    .off("click", t.btn.openFilter)
    .on("click", t.btn.openFilter, function (e) {
      e.preventDefault();
      t.openFilterModal();
    });

  t.content.off("click", t.btn.filter).on("click", t.btn.filter, function (e) {
    e.preventDefault();
    t.applyFilters();
  });

  t.content
    .off("click", t.btn.clearFilter)
    .on("click", t.btn.clearFilter, function (e) {
      e.preventDefault();
      t.clearFilters();
    });

  t.content.on("click", t.btn.download, function () {
    let filters = t.getFilters();

    let search = t.dTable.search();

    let obj = {
      search: search,
      other_filters: filters,
    };

    let encoded = btoa(JSON.stringify(obj));

    let url = t.config.url.download_url + "?q=" + encoded;

    console.log("Export URL:", url);

    window.location = url;
  });

  t.content
    .off("click", ".dtActDel")
    .on("click", ".dtActDel", (e) => t.handleDeleteClick(e));

  // ADD
  t.content.on("click", t.btn.add, (e) => t.create(e));

  t.content
    .off("click", t.btn.import)
    .on("click", t.btn.import, (e) => t.importPurchase(e));

  // EDIT
  t.content.on("click", ".btn-edit-purchase", (e) => t.edit(e));

  // SUBMIT
  t.btn.submit.on("click", (e) => t.submit(e));

  t.content.on("click", ".btn-history-purchase", function (e) {
    e.preventDefault();

    let id = $(this).data("id");

    let modalEl = document.getElementById("history-mdl");
    let modal = new bootstrap.Modal(modalEl);

    modal.show();

    $("#history-content").html(`
        <div class="text-center py-4">
            <i class="fa fa-spinner fa-spin"></i> Loading...
        </div>
    `);

    $.ajax({
      url: t.config.url.historyModalUrl + "/" + id,
      type: "GET",

      success: function (res) {
        $("#history-content").html(res);
      },

      error: function (xhr) {
        let msg = "Something went wrong";

        if (xhr.responseJSON && xhr.responseJSON.msg) {
          msg = xhr.responseJSON.msg;
        }

        $("#history-content").html(`
                <div class="alert alert-danger text-center">
                    <strong>Error:</strong><br>${msg}
                </div>
            `);
      },
    });
  });

  t.content.on("click", ".btn-view-purchase", function (e) {
    e.preventDefault();

    let id = $(this).data("id");

    // window.open(t.config.url.purchase_info + "/" + id, "_blank");
    window.open(t.config.url.purchase_info + "/" + $(this).attr("data-id"), "_blank");

  });

  $(document).on("change", "#include_taxes", function () {
    let val = $(this).val();

    if (val === "1") {
      $("#tax_wrapper").show();
    } else {
      $("#tax_wrapper").hide();
      $("select[name='tax_id']").val("").trigger("change");
    }
  });

  $(document).on("change", "#tax_id", function () {
    t.loadTaxElements();
  });
};

Purchase.prototype.openFilterModal = function () {
  if (this.filters.wrapper.length) {
    this.filters.wrapper.modal("show");
  }
};

Purchase.prototype.bindListControls = function () {
  var t = this;

  let timer;

  $(".user-list-search")
    .off()
    .on("input", function () {
      clearTimeout(timer);

      timer = setTimeout(() => {
        t.dTable.search(this.value).draw();
      }, 400);
    });

  // $(".user-list-page-length")
  //   .off()
  //   .on("change", function () {
  //     t.dTable.page.len(parseInt($(this).val())).draw();
  //   });
};

Purchase.prototype.create = function (e) {
  var t = this;
  e.preventDefault();

  t.submitUrl = t.config.url.add;
  t.editId = null;

  t.resetForm();

  $("#purchaseModalTitle").text("Add Purchase");

  new bootstrap.Modal(t.modal[0]).show();
};

//edit

Purchase.prototype.edit = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  t.editId = id;
  t.submitUrl = t.config.url.update + "/" + id;

  t.resetForm();

  $("#company_id, #supplier_id, #location_id, #tax_id")
    .val(null)
    .trigger("change");

  $("#purchaseModalTitle").text("Edit Purchase");

  $.get(t.config.url.edit + "/" + id, function (res) {
    if (res.status === "success") {
      let d = res.data[0];
      let dropdown = d.dev?.dropdown || {};

      $("#invoice_no").val(d.invoice_no || "");
      $("#po_number").val(d.po_number || "");
      $("input[name='bill_amount']").val(d.bill_amount || "");
      $("input[name='qty']").val(d.qty || "");

      $("#other_info").val(d.other_info || "");
      $("#other_info1").val(d.other_info1 || "");
      $("textarea[name='notes']").val(d.notes || "");

      $("#fully_received").val(d.fully_received).trigger("change");

      if (d.invoice_date) {
        $("#invoice_date").datepicker("update", d.invoice_date);
      }

      if (d.received_date) {
        $("input[name='received_date']").datepicker("update", d.received_date);
      }

      function setSelect2(selector, data) {
        if (!data) return;

        let option = new Option(data.text, data.id, true, true);
        $(selector).append(option).trigger("change");
      }

      setSelect2("#company_id", dropdown.company);
      setSelect2("#supplier_id", dropdown.supplier);
      setSelect2("#location_id", dropdown.location);
      setSelect2("#tax_id", dropdown.tax);

      if (dropdown.currency) {
        $("#currency").val(dropdown.currency.id).trigger("change");
      }

      $("#include_taxes").val(d.include_taxes).trigger("change");

      t.loadedTaxData = dropdown.taxElements || {};
      // t.loadTaxElements();

      new bootstrap.Modal(t.modal[0]).show();
    }
  });
};

Purchase.prototype.initValidation = function () {
  var t = this;

  t.validator = t.form.validate({
    ignore: [],

    rules: {
      invoice_date: { required: true },
      invoice_no: { required: true },
      company_id: { required: true },
      location_id: { required: true },
      currency: { required: true },
      bill_amount: { required: true, number: true },
      qty: { digits: true },

      other_info: { maxlength: 255 },
      other_info1: { maxlength: 255 },
      notes: { maxlength: 10000 },

      include_taxes: { required: true },

      tax_id: {
        required: function () {
          return $("#include_taxes").val() === "1";
        },
      },
    },

    messages: {
      invoice_date: "Purchase Date is required",
      invoice_no: "Invoice No is required",
      company_id: "Company is required",
      location_id: "Location is required",
      currency: "Currency is required",
      bill_amount: "Bill Amount is required",
      tax_id: "Please select tax",
    },

    // errorPlacement: function () {
    //   return false;
    // },

    // highlight: function () {},
    // unhighlight: function () {},
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },
    highlight: function (element, errorClass) {
      $(element).closest('.select-div').addClass(errorClass);
    },
    unhighlight: function (element, errorClass) {
      $(element).closest('.select-div').removeClass(errorClass);
    },
  });
};

Purchase.prototype.submit = function (e) {
  var t = this;
  e.preventDefault();

  // if (!t.form.valid()) {
  //   let errors = t.validator.errorList;

  //   let msg = errors.map((e, i) => `${i + 1}. ${e.message}`).join("<br>");

  //   Swal.fire({
  //     icon: "error",
  //     title: "Validation Error",
  //     html: msg,
  //   });

  //   return;
  // }

  e.preventDefault();
  if (!t.form.valid()) {
    return false;
  }

  let formData = new FormData(t.form[0]);
  formData.append("_token", t.config.token);

  t.btn.submit.prop("disabled", true);

  $.ajax({
    url: t.submitUrl,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,

    success: function (res) {
      if (res.status === "success") {
        bootstrap.Modal.getInstance(t.modal[0]).hide();

        t.resetForm();
        $("#tax_wrapper").hide();
        $(".customOptionsHolders").empty();

        t.reload();

        Swal.fire({
          icon: "success",
          title: "Success",
          text: res.msg || "Saved successfully",
        });
      } else {
        let msg = "Something went wrong";

        if (res.msg) {
          if (Array.isArray(res.msg)) {
            msg = res.msg.join("<br>");
          } else {
            msg = res.msg;
          }
        }

        Swal.fire({
          icon: "error",
          title: "Error",
          html: msg,
        });
      }
    },

    error: function (xhr) {
      let msg = "Server error";

      if (xhr.responseJSON) {
        if (xhr.responseJSON.msg) {
          if (Array.isArray(xhr.responseJSON.msg)) {
            msg = xhr.responseJSON.msg.join("<br>");
          } else {
            msg = xhr.responseJSON.msg;
          }
        } else if (xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join("<br>");
        }
      }

      Swal.fire({
        icon: "error",
        title: "Error",
        html: msg,
      });
    },

    complete: function () {
      t.btn.submit.prop("disabled", false);
    },
  });
};

Purchase.prototype.handleDeleteClick = function (e) {
  e.preventDefault();

  var t = this;
  let id = $(e.currentTarget).data("id");

  if (!id) return;

  Swal.fire({
    title: "Are you sure?",
    text:
      t.config.translations.are_you_delete || "You want to delete this record",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      t.deletePurchase(id);
    }
  });
};

Purchase.prototype.deletePurchase = function (id) {
  var t = this;

  $.ajax({
    url: t.config.url.delete + "/" + id,
    type: "GET",
    data: {
      _token: t.config.token,
    },

    beforeSend: function () {
      Swal.fire({
        title: "Deleting...",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });
    },

    success: function (res) {
      if (res.status === "success") {
        Swal.fire("Deleted!", res.msg || "Record deleted", "success");

        t.reload();
      } else {
        Swal.fire("Error", res.msg || "Delete failed", "error");
      }
    },

    error: function (xhr) {
      console.error(xhr.responseText);
      Swal.fire("Error", "Something went wrong!", "error");
    },
  });
};

Purchase.prototype.importPurchase = function (e) {
  e.preventDefault();

  window.location = this.config.url.purchase_import;
};

Purchase.prototype.resetForm = function () {
  var t = this;

  t.form[0].reset();

  $("#company_id, #supplier_id, #location_id, #tax_id, #currency")
    .val(null)
    .trigger("change");

  $("#invoice_date").datepicker("update", "");
  $("input[name='received_date']").datepicker("update", "");

  $(".customOptionsHolders").empty();

  $("#tax_wrapper").hide();

  t.loadedTaxData = {};

  $("#purchase_id").val("");
};

Purchase.prototype.updateFilterCount = function () {
  var t = this;
  let count = 0;

  let filters = t.getFilters();

  $.each(filters, function (key, val) {
    if (val && val !== "") {
      count++;
    }
  });

  let badge = $(".filter-count-badge");

  if (badge.length) {
    badge.text(count > 0 ? count : "");
    badge.toggleClass("d-none", count === 0);
  }
};

$(function () {
  if (typeof config !== "undefined") {
    new Purchase(config);
  }
});
