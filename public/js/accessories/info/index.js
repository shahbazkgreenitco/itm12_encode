var HistoryPhase = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#history-tab");
  t.table = t.tab.find("#tblHistory");
  t.searchbtn = t.tab.find(".btn-searchbox");
  t.accessory = t.config.accessory_name;
  t.tblHelpers = {};

  t.dTbl = t.table.DataTable({
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    deferRender: true,
    processing: true,
    serverSide: true,
    searchDelay: 400,
    pageLength: 10,
    lengthChange: false,
    dom: `<"top"<"history-custom-length"><"history-custom-search">>rt<"bottom"ip>`,
    order: [[0, "desc"]],
    ajax: {
      url: t.config.url.history,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.accessory_id = t.config.accessory_id;
      },
    },
    aoColumnDefs: [
      {
        targets: 0,

        width: "180px",

        className: "align-middle",

        render: function (d) {
          return `
                    <span class="b1-text text-muted">

                        ${d || "-"}

                    </span>
                `;
        },
      },
      {
        targets: 1,

        className: "align-middle",

        render: function (d) {
          if (!d.adm_full_name) {
            return `<span class="b1-text">-</span>`;
          }

          return `
                    <a href="${config.url.user_info}/${d.adminuserid}"
                       target="_blank"
                       class="fw-semibold b1-text text-decoration-none">

                        ${d.adm_full_name}

                    </a>
                `;
        },
      },
      {
        targets: 2,
        className: "align-middle",
        render: function (d) {
          return `
                    <span class="history-badge">

                        ${d || "-"}

                    </span>
                `;
        },
      },

      {
        targets: 3,
        className: "align-middle",
        render: function (d) {
          return `
                    <span class="b1-text fw-medium">

                        ${d || "-"}

                    </span>
                `;
        },
      },
      {
        targets: 4,
        className: "align-middle",
        render: function (d) {
          return `
                    <span class="b1-text text-muted">

                        ${d || "-"}

                    </span>
                `;
        },
      },
      {
        targets: 5,
        className: "align-middle",
        render: function (data) {
          if (!data) {
            return `
                        <span class="b1-text text-muted">-</span>
                    `;
          }
          if (data.length > 35) {
            return `
                        <span class="b1-text">
                            ${data.substring(0, 35)}
                            <a href="#"
                               class="read-more text-primary"
                               data-full-note="${escapeHtml(data)}">
                                ...
                            </a>
                        </span>
                    `;
          }
          return `
                    <span class="b1-text">

                        ${data}

                    </span>
                `;
        },
      },
      {
        targets: 6,
        orderable: false,
        searchable: false,
        className: "text-center align-middle",
        render: function (d) {
          if (d.interact_type == "i2" && d.interact_module == "m2" && d.accessory_record_id) {
            return `<button class="user-list-action-btn btn-view-change" data-id="${d.accessory_record_id}" title="View Changes">
              <svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg>
            </button>`;
          }
          return "";
        },
      },
    ],

    columns: [
      { data: "a.created_at_format" },
      { data: "a" },
      { data: "a.action_type" },
      { data: "a.target_name" },
      { data: "a.target_type" },
      { data: "a.note" },
      { data: "a" }, // Action column
    ],

    language: {
      emptyTable: `
            <div class="py-4 text-center text-muted">

                No history found

            </div>
        `,
    },

    drawCallback: function () {
      $('[data-toggle="tooltip"]').tooltip();
    },

    fnInitComplete: function () {
      var api = this.api();

      let timer;

      $(".history-search-input")
        .off("keyup")
        .on("keyup", function () {
          clearTimeout(timer);

          let value = this.value;

          timer = setTimeout(() => {
            api.search(value).draw();
          }, 400);
        });

      $(".history-search-btn")
        .off("click")
        .on("click", function () {
          api.search($(".history-search-input").val()).draw();
        });
      $(".history-refresh-button")
        .off("click")
        .on("click", function () {
          $(".history-search-input").val("");

          api.search("").draw();

          api.ajax.reload(null, true);
        });
    },
  });

  t.tab.on("change", ".history-page-length", function () {
    let value = parseInt($(this).val(), 10) || 10;

    t.dTbl.page.len(value).draw();
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
      // t.reload();
    } else if (target.tagName == "BUTTON") {
      t.cache_filter_values();
      // t.reload();
    }
  };

  t.tableSearch = function (e) {
    e.preventDefault();
    var v = $("#tblHistory_filter .plain-search").validate_str_param();
    if (v === false) {
      alert(config.translations.please_enter_valid_search);
      return false;
    }
    t.dTbl.search(v).draw();
  };

  $("#tblHistory").on("click", ".read-more", function (event) {
    event.preventDefault();
    var fullNote = $(this).data("full-note");
    $("#noteModal .modal-body").html(fullNote);
    $("#noteModal").modal("show");
  });

  $("#tblHistory").on("click", ".btn-view-change", function () {
    const id = $(this).data("id");
    $.ajax({
      url: `${config.url.change_info}/${id}`,
      type: "GET",
      dataType: "json",
      success: function (res) {
        const lang = config.historyTranslations;
        const oldData = res.old || {};
        const newData = res.new || {};
        let body = "";
        const addRow = (label, oldValue, newValue) => {
          oldValue = (oldValue ?? "").toString().trim();
          newValue = (newValue ?? "").toString().trim();
          if (oldValue === newValue) {return;}
          body += `<tr><td><strong>${label}</strong></td><td class="">${newValue}</td><td class="">${oldValue}</td></tr>`;
        };

        const fields = [
          { label: lang.name, old: oldData.name, new: newData.name },
          { label: lang.quantity, old: oldData.qty, new: newData.qty },
          { label: lang.purchase_date, old: oldData.purchase_date, new: newData.purchase_date },
          { label: lang.purchase_cost, old: oldData.purchase_cost, new: newData.purchase_cost },
          { label: lang.purchase_currency, old: oldData.purchase_currency, new: newData.purchase_currency },
          { label: lang.order_number, old: oldData.order_number, new: newData.order_number },
          { label: lang.batch_no, old: oldData.batch_no, new: newData.batch_no },
          { label: lang.notes, old: oldData.notes, new: newData.notes },
          { label: lang.requestable, old: oldData.requestable, new: newData.requestable },
          { label: lang.requestable_accessory, old: oldData.requestable_accessory, new: newData.requestable_accessory },
          { label: lang.threshold, old: oldData.accessory_thresholds, new: newData.accessory_thresholds },
          { label: lang.threshold_alerts, old: oldData.thresholds_alerts, new: newData.thresholds_alerts },
          { label: lang.reorder_limit, old: oldData.reorder_limits, new: newData.reorder_limits },
          { label: lang.scrap_qty, old: oldData.scrap_qty, new: newData.scrap_qty },
          { label: lang.category, old: oldData.category_name_old, new: oldData.category_name },
          { label: lang.location, old: oldData.location_name_old, new: oldData.location_name },
          { label: lang.department, old: oldData.department_name_old, new: oldData.department_name },
          { label: lang.manufacturer, old: oldData.manufacturer_name_old, new: oldData.manufacturer_name },
          { label: lang.supplier, old: oldData.supplier_name_old, new: oldData.supplier_name },
          { label: lang.company, old: oldData.company_name_old, new: oldData.company_name },
          { label: lang.purchase, old: oldData.purchase_name_old, new: oldData.purchase_name },
          { label: lang.place, old: oldData.place_name_old, new: oldData.place_name },
        ];

        fields.forEach((field) => {
          addRow(field.label, field.old, field.new);
        });

        (res.customFieldChanges || []).forEach((field) => {
          addRow(field.name, field.old, field.new);
        });

        if (!body) {
          body = `<tr><td colspan="3" class="text-center text-muted">${lang.no_changes_found}</td></tr>`;
        }
        $("#accessory-change-info-body").html(body);
        $("#accessoryChangeInfoModal").modal("show");
      },
      error: function () {
        alert(lang.unable_to_load_change_information);
      },
    });
  });

  t.cache_filter_values = function () {
    var v = $("#tblHistory_filter input").validate_str_param();
    t.config.search = v;
    var jobj = { search: t.config.search };
    t.config.export_filters = btoa(JSON.stringify(jobj));
  };

  $(document).on("click", ".btnActPrintLabel", function () {
    $("#tblHistory").print({
      globalStyles: true,
      mediaPrint: true,
      stylesheet: null,
      noPrintSelector: ".no-print",
      iframe: true,
      append: null,
      prepend: null,
      manuallyCopyFormValues: true,
      deferred: $.Deferred(),
      timeout: 750,
      title: t.accessory + " History ( Accessory )",
      doctype: "<!doctype html>",
    });
  });
  t.tab.on("click", ".btn_reload", $.proxy(t.reload));
  t.tab.on("click", ".btn-searchbox", t.search);
};

var UserPhase = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#users-tab");
  t.table = t.tab.find("#tblUser");

  t.buildActions = function (d) {
    var actions = [];
    if (jQuery.inArray("AccessoriesCheckin", t.config.permissions) !== -1) {
      actions.push(`
            <button
                class="user-list-action-btn dtActCheckIn"
                data-id="${d.id}"
                data-checkoutTo="${d.target_name}"
                title="${config.translations.check_in}">

                ${t.getIcon("checkin")}

            </button>
        `);
    }

    actions.push(`
        <button
            class="user-list-action-btn open-edit-modal"
            data-id="${d.id}"
            title="${config.translations.edit_checkin_date}">

            ${t.getIcon("edit")}

        </button>
    `);

    return `
        <div class="user-list-actions d-flex justify-content-center gap-2">
            ${actions.join("")}
        </div>
    `;
  };

  t.getIcon = function (key) {
    return t.icons[key] ? t.icons[key]() : "";
  };

  t.icons = {
    checkin: function () {
      return `
            <svg viewBox="0 0 16 16" fill="none">
                <path d="M6 3L11 8L6 13"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>

                <path d="M11 8H2"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"/>
            </svg>
        `;
    },

    edit: function () {
      return `
          <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
        `;
    },
  };

  t.dTbl = t.table.DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    dom: "ltrip",
    order: [[2, "desc"]],
    pageLength: 10,
    lengthChange: false,
    searchDelay: 400,
    deferRender: true,
    //   fixedColumns: {
    //     rightColumns: 1,
    //   },

    aoColumnDefs: [
      {
        targets: 4,
        bSortable: false,
        searchable: false,
        className: "text-center",
        render: function (data, type, row) {
          return t.buildActions(row.a);
        },
      },
      {
        targets: 3,
        render: function (d) {
          var a = [];
          if (d.note) {
            var note = String(d.note);
            if (note.length > 20) {
              var truncated = truncateHtml(note, 20);
              a.push(
                `<span class="b1-text">${truncated}<a class="read-more"style="cursor:pointer"data-full-text="${escapeHtml(note)}">...</a></span>`,
              );
            } else {
              a.push(`<span class="b1-text">${note}</span>`);
            }
          }
          return a.join("");
        },
      },
    ],
    order: [[2, "desc"]],
    processing: true,
    serverSide: true,
    ajax: {
      url: t.config.url.users,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.accessory_id = t.config.accessory_id;
      },
    },
    columns: [
      {
        data: "a",
        render: function (data) {
          return `<a href="#"class="target-name-link"data-id="${data.target_id}" data-type="${data.target_type}">${data.target_name}</a>`;
        },
      },
      {
        data: "a.target_type",
        render: function (data) {
          return `<span class="b1-text">${data ?? "-"}</span>`;
        },
      },
      {
        data: "a.expected_checkin_format",
        render: function (data) {
          return `<span class="b1-text">${data ?? "-"}</span>`;
        },
      },

      {
        data: "a",
      },

      {
        data: "a",
      },
    ],

    fnInitComplete: function () {
      var api = this.api();
      let timer;

      $(".users-search-input")
        .off("keyup")
        .on("keyup", function () {
          clearTimeout(timer);
          let value = this.value;
          timer = setTimeout(() => {
            api.search(value).draw();
          }, 400);
        });

      $(".amg-list-searchbar__icon-btn")
        .off("click")
        .on("click", function () {
          api.search($(".users-search-input").val()).draw();
        });
      $(".users-refresh-button")
        .off("click")
        .on("click", function () {
          $(".users-search-input").val("");
          api.search("").draw();
          api.ajax.reload(null, true);
        });
    },
  });

    t.tab.on("change", ".users-page-length", function () {
    let value = parseInt($(this).val(), 10) || 10;
    t.dTbl.page.len(value).draw();
  });

  $(document).on("click", ".target-name-link", function (e) {
    e.preventDefault();
    var table = $("#tblUser").DataTable();
    var rowData = table.row($(this).closest("tr")).data();
    if (rowData) {
      var id = rowData.a.target_id;
      var type = rowData.a.target_type;
      if (type === "User") {
        window.open(baseURL + "/user/info/" + id, '" target="_blank"');
      } else if (type === "Device") {
        window.open(baseURL + "/device/info/" + id, '" target="_blank"');
      } else if (type === "Place") {
        window.open(baseURL + "/internal-places/", '" target="_blank"');
      } else {
        alert("Invalid target type for checkout.");
      }
    }
  });

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

  t.tab.on("click", ".read-more", function (e) {
    e.preventDefault();
    var fullText = $(this).data("full-text");
    $("#noteModal .modal-body").html(fullText);
    $("#noteModal").modal("show");
  });

  var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
    "/": "&#x2F;",
    "`": "&#x60;",
    "=": "&#x3D;",
  };

  t.reload = function () {
    t.dTbl.ajax.reload();
  };

  // t.cache_filter_values = function () {
  //   var v = $("#tblUser_filter input").validate_str_param();
  //   t.config.search = v;
  //   var jobj = { search: t.config.search };
  //   t.config.export_filters = btoa(JSON.stringify(jobj));
  // };
  t.cache_filter_values = function () {
    var v = $(".users-search-input").val();
    t.config.search = v;
    var jobj = { search: t.config.search };
    t.config.export_filters = btoa(JSON.stringify(jobj));
  };

  t.export = function (e) {
    e.preventDefault();
    t.cache_filter_values();
    window.location = t.config.url.export + "?q=" + t.config.export_filters;
  };
  t.exportpdf = function (e) {
    e.preventDefault();
    t.cache_filter_values();
    window.location = t.config.url.exportpdf + "?q=" + t.config.export_filters;
  };
  t.search = function (e) {
    var target = e.target || e.currentTarget;
    if (e.keyCode == 13 || $(this).is("span")) {
      var v = $("#tblUser_filter .plain-search").validate_str_param();
      if (v === false) {
        t.config.search = "";
        alert(config.translations.please_enter_valid_search);
        return false;
      }
      t.config.search = v;
      t.dTbl.search(v).draw();
      t.reload();
    } else if (target.tagName == "BUTTON") {
      t.reload();
    }
  };

  t.editExpectedCheckinDate = function (e) {
    e.preventDefault();
    var checkoutId = $(this).attr("data-id");

    var table = $("#tblUser").DataTable();
    var rowData = table.row($(this).closest("tr")).data();

    if (rowData) {
      t.config.parent.accessoryPhase.chkindate.mdl.frmEl.id.val(checkoutId);
      t.config.parent.accessoryPhase.chkindate.mdl
        .find("#lblAccessoryName")
        .text(rowData.a.name);
      t.config.parent.accessoryPhase.chkindate.mdl
        .find("#lblAccessoryCategory")
        .text(rowData.a.cat_name);

      if (rowData.a.expected_checkin_format) {
        t.config.parent.accessoryPhase.chkindate.mdl
          .find("#expected_checkin")
          .val(rowData.a.expected_checkin_format);
      } else {
        t.config.parent.accessoryPhase.chkindate.mdl
          .find("#expected_checkin")
          .val("");
      }
      if (rowData.a.acc_created_date) {
        var accCreated = rowData.a.acc_created_date.split(" ")[0];
        var parts = accCreated.split("-");
        var minDate = new Date(parts[0], parts[1] - 1, parts[2]);

        t.config.parent.accessoryPhase.chkindate.mdl
          .find("#expected_checkin")
          .datepicker("destroy")
          .datepicker({
            autoclose: true,
            format: "dd/mm/yyyy",
            startDate: minDate,
          });
      } else {
        t.config.parent.accessoryPhase.chkindate.mdl
          .find("#expected_checkin")
          .datepicker("destroy")
          .datepicker({
            autoclose: true,
            format: "dd/mm/yyyy",
            todayHighlight: true,
          });
      }
      t.config.parent.accessoryPhase.chkindate.mdl.modal("show");
    }
  };

  t.tab.on("click", ".btn-searchbox", $.proxy(t.search));
  t.tab.on("click", "#btn_pdf", $.proxy(t.exportpdf));
  t.tab.on("click", "#btn_export", $.proxy(t.export));
  t.tab.on("click", ".btn_reload", $.proxy(t.reload));
  t.tab.on(
    "click",
    ".dtActCheckIn",
    $.proxy(t.config.parent.accessoryPhase.checkinAccessory),
  );
  t.tab.on("click", ".open-edit-modal", $.proxy(t.editExpectedCheckinDate));
};

var DocumentUploadPhase = function (config) {
  var t = this;
  t.config = config;
  t.httpCall = true;
  t.httpPostPath = "";
  t.mdl = $("section.content").find("#document-mdl");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");
  t.mdl.frm = t.mdl.find("#document-mdl-frm");
  t.mdl.frmEl = {};
  t.mdl.frmEl.token = t.mdl.frm.find("input[name='_token']");
  t.mdl.frmEl.note = t.mdl.frm.find("#note");
  t.mdl.frmEl.asset_id = t.mdl.frm.find("input[name='asset_id']");
  t.mdl.frmEl.asset_type = t.mdl.frm.find("input[name='asset_type']");

  t.resetFrm = function () {
    t.mdl.frm.trigger("reset");
  };

  t.addDocument = function (e) {
    e.preventDefault();
    t.frmValidator.resetForm();
    t.resetFrm();
    t.httpPostPath = t.config.url.document_upload;
    t.mdl.title.html("New Document Upload");
    t.mdl.btnSubmit.text("Save");
    t.mdl.frmEl.token.val(t.config.token);
    t.mdl.frmEl.asset_id.val(t.config.accessory_id);
    t.mdl.frmEl.asset_type.val("accessory");
    t.mdl.modal("show");
  };

  t.handleSubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
          t.mdl.modal("hide");
          if (typeof t.config.tbl !== "undefined") {
            t.config.tbl.dTbl.ajax.reload();
          }
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  $(document).on("click", ".btnUploadDocument", $.proxy(t.addDocument, t));

  t.mdl.frm.on("submit", $.proxy(t.handleSubmit, t));

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    rules: {
      note: {
        remarks: true,
        clean_text_only: true,
      },
      document: {
        required: true,
        extension:
          "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
        filesize: 2000000,
      },
    },
    messages: {
      document: {
        required: "Please upload a document.",
        extension: "Invalid file extension",
        filesize: "File size must be less than 2MB.",
      },
    },
  });

  $.validator.addMethod(
    "filesize",
    function (value, element, param) {
      return this.optional(element) || element.files[0].size <= param;
    },
    "File size must be less than {0} bytes.",
  );

  $("section.content").on(
    "click",
    "#btn_upload_document",
    $.proxy(t.addDocument),
  );
  t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};

var DocumentPhase = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#documents-tab");
  t.table = t.tab.find("#tblDocument");
  t.searchbox = t.tab.find(".searchbox");
  t.search = t.tab.find(".btn-searchbox");

  t.buildActions = function (d) {
    let actions = [];
    let downloadUrl =
      d.status == 0
        ? t.config.url.document_download + "/" + d.file_name
        : t.config.url.purchase_document_download + "/" + d.file_name;

    actions.push(`
    <a href="${downloadUrl}"target="_blank"download class="user-list-action-btn" title="${t.config.translations.Download}">
    <svg width="16" height="16"viewBox="0 0 16 16"fill="none"xmlns="http://www.w3.org/2000/svg"><path d="M8 1V10"stroke="currentColor"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round"/><path d="M4.5 7.5L8 11L11.5 7.5"stroke="currentColor"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round"/><path d="M2 13H14"stroke="currentColor"stroke-width="1.5"stroke-linecap="round"/></svg>
    </a>
`);
    // view
    if (["jpg", "jpeg", "png", "pdf"].includes(d.file_extension)) {
      actions.push(`<button class="user-list-action-btn tri-view" data-id="${d.id}"data-type="accessory" title="${t.config.translations.view}">
         <svg width="16"height="16"viewBox="0 0 16 16"fill="none"xmlns="http://www.w3.org/2000/svg"><path d="M1 8C1 8 3.5 3.5 8 3.5C12.5 3.5 15 8 15 8C15 8 12.5 12.5 8 12.5C3.5 12.5 1 8 1 8Z"stroke="currentColor"stroke-width="1.5"stroke-linecap="round"stroke-linejoin="round"/><circle cx="8"cy="8"r="2"stroke="currentColor"stroke-width="1.5"/></svg></button>
        `);
    }

    // delete
    actions.push(`
        <button class="user-list-action-btn dtActDel is-delete" data-id="${d.id}" title="${t.config.translations.Delete}">
            <svg viewBox="0 0 15 17"
                 fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"
                      fill="currentColor"></path>
            </svg>
        </button>
    `);
    return `<div class="user-list-actions d-flex justify-content-center align-items-center gap-2">${actions.join("")}</div>`;
  };
  t.attachmentView = function (e) {
    e.preventDefault();
    window.open(
      t.config.url.attachment_view + "/" + $(this).attr("data-id"),
      "_blank",
    );
  };

  t.deleteDocument = function (e) {
    e.preventDefault();
    var docId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.document_delete;
    var data = {
      msg: "Something went wrong. Please try again later.",
    };
    var send_data = {
      asset_id: t.config.accessory_id,
      asset_type: "accessory",
      id: docId,
      _token: t.config.token,
    };
    sweetAlertPost(
      "You could not recover it after delete. Are you sure to delete the document permanently?",
      "warning",
      t.httpPostPath,
      t.dTbl.ajax,
      data,
      send_data,
    );
  };

  t.dTbl = t.table.DataTable({
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    deferRender: true,
    searchDelay: 400,
    processing: true,
    serverSide: true,
    pageLength: 10,
    lengthChange: false,
    dom: "rt<'d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3'ip>",
    order: [[2, "desc"]],
    ajax: {
      url: t.config.url.documents,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.device_id = t.config.accessory_id;
        d.asset_type = "accessory";
      },
    },

    columnDefs: [
      {
        targets: 3,
        orderable: false,
        searchable: false,
        className: "text-center align-middle",
        render: function (data, type, row) {
          return t.buildActions(row.a);
        },
      },

      {
        targets: 0,
        className: "align-middle",
        render: function (data) {
          return `<div class="d-flex align-items-center gap-2"><div class="document-file-icon"><i class="fa fa-file-text-o"></i></div>
        <div class="fw-semibold b1-text text-break">
                        ${data ?? "-"}
        </div>
        </div>
          `;
        },
      },

      {
        targets: 1,
        className: "align-middle",
        render: function (data) {
          return `<span class="b1-text">${data ?? "-"}</span>`;
        },
      },

      {
        targets: 2,
        className: "align-middle",
        render: function (data) {
          if (!data) {
            return `<span class="b1-text">-</span>`;
          }
          if (data.length > 30) {
            return `<span class="b1-text">${data.substring(0, 30)}
                        <a href="#"
                           class="read-more"
                           data-full-note="${escapeHtml(data)}">
                           ...
                        </a>
                      </span>
                `;
          }
          return `<span class="b1-text">${data}</span>`;
        },
      },
    ],
    columns: [
      {
        data: "a.org_name",
      },
      {
        data: "a.created_at_format",
      },
      {
        data: "a.note",
      },
      {
        data: "a",
      },
    ],

    fnInitComplete: function () {
      var api = this.api();
      let timer;

      $(".documents-search-input")
        .off("keyup")
        .on("keyup", function () {
          clearTimeout(timer);
          let value = this.value;
          timer = setTimeout(() => {
            api.search(value).draw();
          }, 400);
        });

      $(".documents-search-btn")
        .off("click")
        .on("click", function () {
          api.search($(".documents-search-input").val()).draw();
        });
      $(".documents-refresh-button")
        .off("click")
        .on("click", function () {
          $(".documents-search-input").val("");
          api.search("").draw();
          api.ajax.reload(null, true);
        });
    },
  });

  t.tab.on("change", ".documents-page-length", function () {
    let value = parseInt($(this).val(), 10) || 10;

    t.dTbl.page.len(value).draw();
  });

  t.reload = function () {
    t.dTbl.ajax.reload();
  };
  t.attachmentView = function (e) {
    e.preventDefault();
    window.open(
      t.config.url.attachment_view +
        "/" +
        $(this).attr("data-id") +
        "?type=" +
        $(this).attr("data-type"),
      "_blank",
    );
  };

  t.search = function (e) {
    var target = e.target || e.currentTarget;
    if (e.keyCode == 13 || $(this).is("span")) {
      var v = $("#tblDocument_filter .plain-search").validate_str_param();
      if (v === false) {
        t.config.search = "";
        alert(t.config.translations.please_enter_valid_search);
        return false;
      }
      t.config.search = v;
      t.dTbl.search(v).draw();
      // t.reload();
    } else if (target.tagName === "BUTTON") {
      t.reload();
    }
  };

  $("#tblDocument").on("click", ".read-more", function (event) {
    event.preventDefault();
    var fullNote = $(this).data("full-note");
    $("#noteModal .modal-body").html(fullNote);
    $("#noteModal").modal("show");
  });

  t.tab.on("click", ".tri-view", $.proxy(t.attachmentView));
  t.tab.on("click", ".dtActDel", $.proxy(t.deleteDocument));
  t.tab.on("click", ".btn_reload", $.proxy(t.reload));
  t.tab.on("click", ".tri-view", $.proxy(t.attachmentView));
  t.tab.on("click", ".btn-searchbox", $.proxy(t.search));
};

var PurchasePhase = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#purchase-tab");
  t.table = t.tab.find("#tblPurchase");
  t.btn = {};
  t.mdl = $("section.content").find("#purchasemodal");
  t.mdl.frm = t.mdl.find("#PurchaseForm");
  t.mdl.title = t.mdl.frm.find(".modal-title");
  t.btn.btnSubmit = t.mdl.frm.find("#btnSubmit");
  t.btn.btnClear = t.mdl.frm.find("#btnClear");
  t.mdl.frmEl = {};
  t.mdl.frmEl.purchase_date = t.mdl.frm.find("#purchase_date");
  t.mdl.frmEl.exp_date = t.mdl.frm.find("#exp_date");
  t.mdl.frmEl.po_number = t.mdl.frm.find("#po_number");
  t.mdl.frmEl.invoice_no = t.mdl.frm.find("#invoice_no");
  t.mdl.frmEl.batch_no = t.mdl.frm.find("#batch_no");
  t.mdl.frmEl.purchase_by = t.mdl.frm.find("#purchase_by");
  t.mdl.frmEl.qty = t.mdl.frm.find("#qty");
  t.mdl.frmEl.currency = t.mdl.frm.find("#currency");
  t.mdl.frmEl.bill_amount = t.mdl.frm.find("#bill_amount");
  t.mdl.frmEl.imgviewcover = t.mdl.frm.find(".imgviewcoverpur");
  t.mdl.frmEl.imgviewpur = t.mdl.frm.find(".imgviewpur");

  t.buildActions = function (d) {
    let actions = [];
    if (jQuery.inArray("PurchaseEdit", t.config.permissions) !== -1) {
      actions.push(`
            <button class="user-list-action-btn btn-edit-purchase" data-id="${d.accessory_purchases_id}" title="${t.config.translations.edit}">
                <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
            </button>
        `);
    }
    if (jQuery.inArray("PurchaseDelete", t.config.permissions) !== -1) {
      actions.push(`
            <button class="user-list-action-btn dtActDel is-delete" data-id="${d.accessory_purchases_id}" title="${t.config.translations.Delete}">
               <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
            </button>
        `);
    }
    // View
    if (
      jQuery.inArray("PurchaseDownload", t.config.permissions) !== -1 &&
      d.attachment != null
    ) {
      let validExtensions = ["pdf", "jpg", "jpeg", "png", "gif"];
      let fileExtension = d.attachment.split(".").pop().toLowerCase();
      if (validExtensions.includes(fileExtension)) {
        actions.push(`
                <button class="user-list-action-btn tri-view" data-id="${d.accessory_purchases_id}" data-type="accessory" title="${t.config.translations.view}">
               <svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg>
                </button>
            `);
      }

      actions.push(`
            <a href="${t.config.url.purchase_attachment_download}/${d.attachment}"
               class="user-list-action-btn" download title="${t.config.translations.Download}">
                <svg viewBox="0 0 20 20">
                        <path d="M10 3V13M10 13L6 9M10 13L14 9M3 17H17" stroke="currentColor"></path>
                    </svg>
            </a>
        `);
    }
    return `
        <div class="user-list-actions d-flex justify-content-center align-items-center gap-2">
            ${actions.join("")}
        </div>
    `;
  };

  t.dTbl = t.table.DataTable({
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    deferRender: true,
    processing: true,
    serverSide: true,
    searchDelay: 400,
    pageLength: 10,
    lengthChange: false,
    dom: `<"top"<"purchase-custom-length"><"purchase-custom-search">>rt<"bottom"ip>`,
    order: [[9, "desc"]],
    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.location = t.config.location_filter;
        d.filters = t.config.other_filters;
        d.accessory_id = t.config.accessory_id;
        if (d.order && d.order.length > 0) {
          let orderInfo = d.order[0];
          d.sorted_column_name = d.columns[orderInfo.column].data;
          d.sorted_direction = orderInfo.dir;
        }
      },
    },

    columnDefs: [
      {
        targets: 10,
        orderable: false,
        searchable: false,
        className: "text-center align-middle",

        render: function (data, type, row) {
          return t.buildActions(row.a);
        },
      },

      {
        targets: "_all",
        className: "align-middle",
        render: function (data) {
          return `<span class="b1-text">${data ?? "-"}</span>`;
        },
      },

      {
        targets: 6,
        render: function (data) {
          return `<span class="fw-semibold text-success b1-text">${data ?? "-"}</span>`;
        },
      },
    ],

    columns: [
      { data: "a.acc_batch_no" },
      { data: "a.purchase_date_on" },
      { data: "a.po_no" },
      { data: "a.supplier_name" },
      { data: "a.exp_date_on" },
      { data: "a.qty" },
      { data: "a.purchase_cost_format" },
      { data: "a.loc_name" },
      { data: "a.dep_name" },
      { data: "a.last_updated_at" },
      { data: "a" },
    ],

    language: {
      emptyTable: `<div class="py-4 text-center text-muted">No purchases found</div>`,
    },

    drawCallback: function () {
      $('[data-toggle="tooltip"]').tooltip();
    },

    fnInitComplete: function () {
      let api = this.api();
      let timer;
      $(".purchase-search-input")
        .off("keyup")
        .on("keyup", function () {
          clearTimeout(timer);
          let value = this.value;
          timer = setTimeout(() => {
            api.search(value).draw();
          }, 400);
        });
      $(".purchase-search-btn")
        .off("click")
        .on("click", function () {
          api.search($(".purchase-search-input").val()).draw();
        });

      $(".purchase-refresh-button")
        .off("click")
        .on("click", function () {
          $(".purchase-search-input").val("");
          api.search("").draw();
          api.ajax.reload(null, true);
        });
    },
  });

  t.tab.on("change", ".purchase-page-length", function () {
    let value = parseInt($(this).val(), 10) || 10;

    t.dTbl.page.len(value).draw();
  });

  t.dTbl.on("column-reorder", function (e, settings, details) {
    var api = t.table.DataTable();
    buildColumnVisibilityControls(api);
  });

  function buildColumnVisibilityControls(api) {
    $("#columnVisibilityControls").empty();
    api.columns().every(function (index) {
      var column = this;
      var columnTitle = $(column.header()).text().trim();
      var checkboxHtml = `
                <div class="dropdown-item" style="padding:6px">
                    <label>
                        <input type="checkbox" data-column="${index}" ${column.visible() ? "checked" : ""}> ${columnTitle}
                    </label>
                </div>`;
      $("#columnVisibilityControls").append(checkboxHtml);
    });
    $('#columnVisibilityControls input[type="checkbox"]').on(
      "change",
      function () {
        var columnIndex = $(this).data("column");
        var column = api.column(columnIndex);
        column.visible($(this).prop("checked"));
      },
    );
  }

  t.mdl.optionsCurrency = function () {
    // t.mdl.frmEl.currency.empty().append(new Option(config.translations.Select_Currency_Format, ""));
    $.each(t.config.currencies, function (i, v) {
      var opt =
        t.config.default_currency_format == i
          ? new Option("", i, true, true)
          : new Option(config.translations.Select_Currency_Format, i);
      opt.innerHTML = v.name + " (" + v.symbol_html + ")";
      t.mdl.frmEl.currency.append(opt);
    });
    t.mdl.frmEl.currency.trigger("change");
  };

  t.addPurchase = function (e) {
    e.preventDefault();
    t.httpPostPath = t.config.url.add_purchase;
    if (t.config.client == "etherealmachines") {
      t.mdl.title.html("Add Purchase AC" + t.config.accessory_id);
    } else {
      t.mdl.title.html("Add Purchase A" + t.config.accessory_id);
    }
    t.btn.btnSubmit.text(config.translations.save);
    t.mdl.frmEl.imgviewcover.addClass("hide");
    t.mdl.frmEl.imgviewpur.attr("src", "");
    t.mdl.forAction = "";
    t.resetFrm();
    t.frmValidator.resetForm();
    t.mdl.modal("show");
    t.mdl.frmEl.purchase_date.on("changeDate", function (e) {
      var selectedDate = e.date;
      t.mdl.frmEl.exp_date.datepicker("setStartDate", selectedDate);
      t.mdl.frmEl.exp_date.datepicker("update", "");
    });
  };

  t.editPurchase = function (e) {
    e.preventDefault();
    var purchaseId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.update_purchase + "/" + purchaseId;
    var http = $.get(t.config.url.get_purchase + "/" + purchaseId);
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.resetFrm();
          t.frmValidator.resetForm();
          if (t.config.client == "etherealmachines") {
            t.mdl.title.html("Edit Purchase AC" + t.config.accessory_id);
          } else {
            t.mdl.title.html("Edit Purchase A" + t.config.accessory_id);
          }
          t.btn.btnSubmit.text(config.translations.save);
          t.mdl.modal("show");
          t.loadForm(data.data);
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.loadForm = function (data) {
    t.resetFrm();
    if (
      typeof data[0].dev == "object" &&
      typeof data[0].dev.dropdown == "object" &&
      data[0].dev.dropdown.purchase_by != null
    ) {
      t.mdl.frmEl.purchase_by
        .append(
          new Option(
            data[0].dev.dropdown.purchase_by.text,
            data[0].dev.dropdown.purchase_by.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    if (
      typeof data[0].dev == "object" &&
      typeof data[0].dev.dropdown == "object" &&
      data[0].dev.dropdown.currency != null
    ) {
      t.mdl.frmEl.currency
        .val(data[0].dev.dropdown.currency.id)
        .trigger("change");
    }

    t.mdl.frmEl.purchase_date.val(data[0].purchase_date);
    t.mdl.frmEl.exp_date.val(data[0].exp_date);
    t.mdl.frmEl.po_number.val(data[0].po_no);
    t.mdl.frmEl.invoice_no.val(data[0].invoice_no);
    t.mdl.frmEl.batch_no.val(data[0].batch_no);
    t.mdl.frmEl.qty.val(data[0].qty);
    t.mdl.frmEl.bill_amount.val(data[0].purchase_price);
    t.mdl.frmEl.purchase_date.datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",
    });
    t.mdl.frmEl.exp_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });

    var purchaseDate = t.mdl.frmEl.purchase_date.val();
    if (purchaseDate) {
      t.mdl.frmEl.exp_date.datepicker("setStartDate", purchaseDate);
    }

    t.mdl.frmEl.purchase_date.on("changeDate", function (e) {
      var selectedDate = e.date;
      t.mdl.frmEl.exp_date.datepicker("setStartDate", selectedDate);
      t.mdl.frmEl.exp_date.datepicker("update", "");
    });

    if (data[0].attachment != null) {
      t.mdl.frmEl.imgviewcover.addClass("hide");
      $("#imgviewpur").attr(
        "src",
        t.config.imgviewpathpur + "/" + data[0].attachment,
      );
    }
  };
  t.mdl.frmEl.purchase_date.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
  });
  t.mdl.frmEl.exp_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.resetFrm = function () {
    t.mdl.frmEl.purchase_date.val("");
    t.mdl.frmEl.exp_date.val("");
    t.mdl.frmEl.po_number.val("");
    t.mdl.frmEl.invoice_no.val("");
    t.mdl.frmEl.batch_no.val(t.config.accessory_id);
    t.mdl.frmEl.qty.val("");
    t.mdl.frmEl.bill_amount.val("");
    t.mdl.frmEl.purchase_by.val("").trigger("change");
    t.mdl.frmEl.currency.val("").trigger("change");
    t.frmValidator.resetForm();
    t.mdl.optionsCurrency();
    t.mdl.frmEl.imgviewcover.addClass("hide");
    t.mdl.frmEl.imgviewpur.attr("src", "");
  };

  t.reload = function () {
    t.dTbl.ajax.reload();
  };

  t.handleSubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var formData = new FormData(t.mdl.frm[0]);
    t.btn.btnSubmit.prop("disabled", true);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.mdl.modal("hide");
          t.dTbl.ajax.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
      t.btn.btnSubmit.prop("disabled", false);
    });
  };

  t.deletePurchase = function (e) {
    e.preventDefault();
    var docId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.purchase_delete;
    var data = {
      msg: config.translations.something_went_wrong,
    };
    var send_data = {
      id: docId,
      _token: t.config.token,
    };
    sweetAlertPost(
      "You could not recover it after delete. Are you sure to delete the purchase permanently?",
      "warning",
      t.httpPostPath,
      t.dTbl.ajax,
      data,
      send_data,
    );
  };

  t.attachmentViewPur = function (e) {
    e.preventDefault();
    window.open(
      t.config.url.purchase_attachment_view +
        "/" +
        $(this).attr("data-id") +
        "?type=" +
        $(this).attr("data-type"),
      "_blank",
    );
  };

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    rules: {
      purchase_date: {
        required: true,
      },
      currency: {
        required: true,
        str_name: true,
      },
      po_number: {
        required: false,
        maxlength: 30,
        clean_text_only: true,
      },
      invoice_no: {
        required: false,
        maxlength: 30,
        clean_text_only: true,
      },
      bill_amount: {
        required: true,
        number: true,
        validAmountFormat: true,
        maxLength15: true,
      },
      qty: {
        required: true,
        digits: true,
        maxlength: 5,
        min: 1,
      },
      attachment: {
        extension:
          "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
        filesize: 2000000,
      },
    },
    messages: {
      attachment: {
        extension: "Invalid file extension",
        filesize: "File size must be less than 2MB.",
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },
  });
  $.validator.addMethod(
    "filesize",
    function (value, element, param) {
      return this.optional(element) || element.files[0].size <= param;
    },
    "File size must be less than {0} bytes.",
  );
  $.validator.addMethod(
    "validAmountFormat",
    function (value, element) {
      return this.optional(element) || /^\d+(\.\d{1,4})?$/.test(value);
    },
    "Please enter a valid amount (up to 4 decimal places).",
  );

  $.validator.addMethod(
    "maxLength15",
    function (value, element) {
      return this.optional(element) || value.length <= 15;
    },
    "Maximum 15 characters allowed.",
  );
  var select2Opts = { width: "100%" };
  t.mdl.frmEl.purchase_by.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.purchase_by.parent(),
      ajax: {
        url: t.config.getSupplierByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: config.translations.select_the_purchase_by,
      templateSelection: function (data, container) {
        $(container).attr("title", data.text);
        return data.text.length > 50
          ? data.text.substring(0, 50) + "..."
          : data.text;
      },
    }),
  );
  t.mdl.frmEl.purchase_by.trigger("change");
  t.mdl.frmEl.currency.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.no_filter,
      dropdownParent: t.mdl.frmEl.currency.parent(),
    }),
  );
  t.tableSearch = function (e) {
    e.preventDefault();
    var v = $("#tblPurchase_wrapper .plain-search").validate_str_param();
    if (v === false) {
      alert(config.translations.please_enter_valid_search);
      return false;
    }
    t.dTbl.search(v).draw();
  };
  t.tab.on("click", ".btn-add-purchase", $.proxy(t.addPurchase));
  t.tab.on("click", ".btn-edit-purchase", $.proxy(t.editPurchase));
  t.btn.btnSubmit.on("click", $.proxy(t.handleSubmit));
  t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
  t.tab.on("click", ".btn-searchbox", $.proxy(t.tableSearch));
  t.tab.on("click", ".dtActDel", $.proxy(t.deletePurchase));
  t.tab.on("click", ".tri-view", $.proxy(t.attachmentViewPur));
};

var AccessoryPhase = function (config) {
  var t = this;
  t.config = config;
  t.content = $("section.content");
  t.sectionHeader = $("section.content-header");
  t.infoTab = $("section.content").find("#info-tab");

  t.httpCall = true;
  t.httpPostPath = "";

  t.mdl = $("section.content").find("#accessory-mdl");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");

  t.mdl.frm = t.mdl.find("#accessory-mdl-frm");
  t.mdl.forAction = "";
  t.mdl.frmEl = {};
  t.mdl.frmEl.id = t.mdl.frm.find("#id");
  t.mdl.frmEl.company = t.mdl.frm.find("#company_id");
  t.mdl.frmEl.unique_tag = t.mdl.frm.find("#unique_tag");
  t.mdl.frmEl.category = t.mdl.frm.find("#category_id");
  t.mdl.frmEl.location = t.mdl.frm.find("#location_id");
  t.mdl.frmEl.internal_place = t.mdl.frm.find("#internal_place");
  t.mdl.frmEl.manufacturer = t.mdl.frm.find("#manufacturer_id");
  t.mdl.frmEl.purchase_date = t.mdl.frm.find("#purchase_date");
  t.mdl.frmEl.purchase_currency = t.mdl.frm.find("#purchase_currency");
  t.mdl.frmEl.supplier = t.mdl.frm.find("#supplier_id");
  t.mdl.frmEl.invoice_id = t.mdl.frm.find("#invoice_id");
  t.mdl.frmEl.notes = t.mdl.frm.find("#notes");
  t.mdl.frmEl.customFieldsPrvEl = t.mdl.frm.find(".custom-fields-follow");
  t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
  t.mdl.frmEl.imgviewcover = t.mdl.frm.find(".imgviewcover");
  t.mdl.frmEl.imgview = t.mdl.frm.find("#imgview");
  t.mdl.frmEl.clone_img = t.mdl.frm.find("#clone_img");
  t.mdl.frmEl.accessory_thresholds = t.mdl.frm.find("#accessory_thresholds");
  t.mdl.frmEl.thresholds_alerts = t.mdl.frm.find("#thresholds_alerts");
  t.mdl.frmEl.threshold_alert_users = t.mdl.frm.find("#threshold_alert_users");
  t.mdl.frmEl.reorder_limits = t.mdl.frm.find("#reorder_limits");
  var current_purchase_date = "";
  var current_supplier_name = (current_supplier_id = "");
  var order_no = (currency_default = bill_amount = purchase_cost = "");
  t.internal_places = [];
  t.btn = {
    editAccessory: t.content.find(".dtActEdit"),
    cloneAccessory: t.content.find(".dtActClone"),
    deleteAccessory: t.content.find(".dtActDel"),
  };

  // checkout
  t.chkout = {};
  t.chkout.mdl = $("section.content").find("#accessory-checkout-mdl");
  t.chkout.mdl.btnSubmit = t.chkout.mdl.find("#btnSubmit");
  t.chkout.mdl.btnClear = t.chkout.mdl.find("#btnClear");
  t.chkout.mdl.frm = t.chkout.mdl.find("#accessory-checkout-mdl-frm");
  t.chkout.mdl.frmEl = {};
  t.chkout.mdl.frmEl.id = t.chkout.mdl.frm.find("#id");
  t.chkout.mdl.frmEl.assigned_for = t.chkout.mdl.frm.find("#assigned_for");
  t.chkout.mdl.frmEl.assigned_to = t.chkout.mdl.frm.find("#assigned_to");
  t.chkout.mdl.frmEl.assigned_place = t.chkout.mdl.frm.find("#assigned_place");
  t.chkout.mdl.frmEl.device_id = t.chkout.mdl.frm.find("#device_id");
  t.chkout.mdl.frmEl.expected_checkin =
    t.chkout.mdl.frm.find("#expected_checkin");
  t.chkout.mdl.lblAccessoryName = t.chkout.mdl.find("#lblAccessoryName");
  t.chkout.mdl.lblAccessoryCategory = t.chkout.mdl.find(
    "#lblAccessoryCategory",
  );

  //scrap
  t.scrap = {};
  t.scrap.mdl = $("section.content").find("#accessory-scrap-mdl");
  t.scrap.mdl.btnSubmit = t.scrap.mdl.find("#btnSubmit");
  t.scrap.mdl.btnClear = t.scrap.mdl.find("#btnClear");
  t.scrap.mdl.frm = t.scrap.mdl.find("#accessory-scrap-mdl-frm");
  t.scrap.mdl.frmEl = {};
  t.scrap.mdl.frmEl.id = t.scrap.mdl.frm.find("#id");
  t.scrap.mdl.frmEl.scrap_qty = t.scrap.mdl.frm.find("#scrap_qty");

  //revert scrap
  t.revertScrap = {};
  t.revertScrap.mdl = $("section.content").find("#accessory-scraprevert-mdl");
  t.revertScrap.mdl.btnSubmit = t.revertScrap.mdl.find("#btnSubmit");
  t.revertScrap.mdl.btnClear = t.revertScrap.mdl.find("#btnClear");
  t.revertScrap.mdl.frm = t.revertScrap.mdl.find(
    "#accessory-scraprevert-mdl-frm",
  );
  t.revertScrap.mdl.frmEl = {};
  t.revertScrap.mdl.frmEl.id = t.revertScrap.mdl.frm.find("#id");
  t.revertScrap.mdl.frmEl.scrap_qty = t.revertScrap.mdl.frm.find("#scrap_qty");

  // checkin
  t.chkin = {};
  t.chkin.mdl = $("section.content").find("#accessory-checkin-mdl");
  t.chkin.mdl.btnSubmit = t.chkin.mdl.find("#btnSubmit");
  t.chkin.mdl.btnClear = t.chkin.mdl.find("#btnClear");
  t.chkin.mdl.frm = t.chkin.mdl.find("#accessory-checkin-mdl-frm");
  t.chkin.mdl.frmEl = {};
  t.chkin.mdl.frmEl.id = t.chkin.mdl.frm.find("#id");
  t.chkin.mdl.frmEl.scrap_qty = t.chkin.mdl.frm.find("#scrap_qty");
  t.chkin.mdl.lblUserName = t.chkin.mdl.find("#lblUserName");

  //expected checkin date
  t.chkindate = {};
  t.chkindate.mdl = $("section.content").find(
    "#accessory-expected-checkin-date",
  );
  t.chkindate.mdl.btnSubmit = t.chkindate.mdl.find("#btnSubmit");
  t.chkindate.mdl.btnClear = t.chkindate.mdl.find("#btnClear");
  t.chkindate.mdl.frm = t.chkindate.mdl.find(
    "#accessory-expected-checkin-date-frm",
  );
  t.chkindate.mdl.frmEl = {};
  t.chkindate.mdl.frmEl.id = t.chkindate.mdl.frm.find("#id");
  t.chkindate.mdl.lblUserName = t.chkindate.mdl.find("#lblUserName");

  /* checkout box */
  t.chkout.frmValidator = t.chkout.mdl.frm.validate({
    onsubmit: false,
    rules: {
      assigned_for: {
        required: false,
        str_name: true,
        clean_text_only: true,
      },
      assigned_to: {
        str_name: true,
      },
      assigned_place: {
        str_name: true,
      },
      device_id: {
        str_name: true,
      },
      expected_checkin: {
        remarks: true,
      },
      note: {
        remarks: true,
        clean_text_only: true,
        required: function () {
          return config.client === "knightfrank";
        },
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },
  });

  t.chkout.resetFrm = function () {
    t.chkout.mdl.frm.trigger("reset");
    t.chkout.frmValidator.resetForm();
    t.chkout.mdl.frmEl.assigned_to.empty().trigger("change");
    t.chkout.mdl.frmEl.assigned_for.val("").trigger("change");
    t.chkout.mdl.frmEl.assigned_place.empty().trigger("change");
    t.chkout.mdl.frmEl.device_id.empty().trigger("change");
    t.chkout.mdl.frmEl.expected_checkin.val("");
    t.chkout.mdl.lblAccessoryName.text("");
    t.chkout.mdl.lblAccessoryCategory.text("");
  };

  t.chkout.switchCheckTarget = function () {
    if (t.chkout.mdl.frmEl.assigned_for.val() == "2") {
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_place.rules("add", { required: true });
      t.chkout.mdl.frmEl.assigned_to.rules("remove", "required");
      t.chkout.mdl.frmEl.device_id.rules("remove", "required");
    } else if (t.chkout.mdl.frmEl.assigned_for.val() == "3") {
      t.chkout.mdl.frmEl.device_id.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.rules("add", { required: true });
      t.chkout.mdl.frmEl.assigned_to.rules("remove", "required");
      t.chkout.mdl.frmEl.assigned_place.rules("remove", "required");
    } else {
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_place.rules("remove");
      t.chkout.mdl.frmEl.device_id.rules("remove", "required");
      t.chkout.mdl.frmEl.assigned_to.rules("add", { required: true });
    }
  };

  t.checkoutAccessory = function (e) {
    e.preventDefault();
    t.chkout.resetFrm();
    t.httpPostPath = t.config.url.checkout;
    t.chkout.mdl.lblAccessoryName.html($(this).attr("data-accessory-name"));
    t.chkout.mdl.lblAccessoryCategory.html($(this).attr("data-category-name"));
    t.chkout.mdl.frmEl.id.val($(this).attr("data-id"));
    t.chkout.mdl.frmEl.device_id.empty();
    t.chkout.mdl.frmEl.assigned_place.empty();
    t.chkout.mdl.frmEl.assigned_place.append(
      new Option("Select The Place", ""),
    );
    var company_id = $(this).attr("data-company_id");
    if (typeof t.config.places == "object" && t.config.places.length > 0) {
      $.each(t.config.places, function (i, s) {
        if (company_id == s.company_id) {
          t.chkout.mdl.frmEl.assigned_place.append(new Option(s.text, s.id));
        }
      });
    }
    t.chkout.mdl.frmEl.assigned_place.select2({
      width: "100%",
      placeholder: "Select The Place",
      templateSelection: function (data, container) {
        $(container).attr("title", data.text);
        return data.text.length > 50
          ? data.text.substring(0, 50) + "..."
          : data.text;
      },
    });

    t.chkout.mdl.frmEl.assigned_place.trigger("change");
    t.chkout.mdl.frmEl.assigned_for.val("1").trigger("change");
    t.chkout.mdl.modal("show");
  };

  t.scrap.resetFrm = function () {
    t.scrap.mdl.frm.trigger("reset");
    t.scrap.frmValidator.resetForm();
    t.scrap.mdl.frmEl.scrap_qty.val("");
  };

  t.revertScrap.resetFrm = function () {
    t.revertScrap.mdl.frm.trigger("reset");
    t.revertScrap.frmValidator.resetForm();
    t.revertScrap.mdl.frmEl.scrap_qty.val("");
  };

  t.scrapAccessory = function (e) {
    e.preventDefault();
    t.scrap.resetFrm();
    t.httpPostPathScrap = t.config.url.scrap;
    t.scrap.mdl.frmEl.id.val($(this).attr("data-id"));
    t.scrap.mdl.modal("show");
  };

  t.revertScrapAccessory = function (e) {
    e.preventDefault();
    t.revertScrap.resetFrm();
    t.httpPostPathRevertScrap = t.config.url.revertScrap;
    t.revertScrap.mdl.frmEl.id.val($(this).attr("data-id"));
    t.revertScrap.mdl.modal("show");
  };

  t.handleScrapSubmit = function (e) {
    e.preventDefault();

    if (t.scrap.frmValidator.form() == false) {
      return false;
    }

    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.scrap.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPathScrap,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.scrap.mdl.modal("hide");
          t.refreshInfoTab();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.handleRevertScrapSubmit = function (e) {
    e.preventDefault();

    if (t.revertScrap.frmValidator.form() == false) {
      return false;
    }

    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.revertScrap.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPathRevertScrap,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.revertScrap.mdl.modal("hide");
          t.refreshInfoTab();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.handleCheckoutSubmit = function (e) {
    e.preventDefault();

    if (t.chkout.frmValidator.form() == false) {
      return false;
    }

    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.chkout.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
          t.chkout.mdl.modal("hide");
          t.refreshInfoTab();
          t.config.parent.userPhase.reload();
          t.config.parent.historyPhase.reload();
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };
  /* ------------- */

  /* checkin */
  t.chkin.frmValidator = t.chkin.mdl.frm.validate({
    onsubmit: false,
    rules: {
      id: {
        required: true,
        digits: true,
      },
      note: {
        remarks: true,
        clean_text_only: true,
        required: function () {
          return config.client === "knightfrank";
        },
      },
    },
  });

  /* scrap */
  t.scrap.frmValidator = t.scrap.mdl.frm.validate({
    onsubmit: false,
    rules: {
      scrap_qty: {
        required: true,
        digits: true,
      },
    },
    errorPlacement: function (error, element) {
      element
        .closest(".amg-form-field")
        .find(".amg-form-error-wrap")
        .html(error);
    },
  });

  // revertScrap
  t.revertScrap.frmValidator = t.revertScrap.mdl.frm.validate({
    onsubmit: false,
    rules: {
      scrap_qty: {
        required: true,
        digits: true,
      },
    },
     errorPlacement: function (error, element) {
      element
        .closest(".amg-form-field")
        .find(".amg-form-error-wrap")
        .html(error);
    },
  });

  t.chkin.resetFrm = function () {
    t.chkin.mdl.frm.trigger("reset");
    t.chkin.frmValidator.resetForm();
    t.chkin.mdl.lblUserName.text("");
  };

  t.checkinAccessory = function (e) {
    e.preventDefault();
    if (t.config.accessory_block_checkin == 1) {
      var data = {
        msg: "Sorry. Accessory checkin is not allowed.",
      };
      sweetAlert("center", "error", data);
      // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center">Sorry. Accessory checkin is not allowed.</div>' });
      return false;
    }
    t.chkin.resetFrm();
    t.httpPostPath = t.config.url.checkin;
    t.chkin.mdl.lblUserName.html($(this).attr("data-checkoutTo"));
    t.chkin.mdl.frmEl.id.val($(this).attr("data-id"));
    t.chkin.mdl.frmEl.scrap_qty.val();
    t.chkin.mdl.modal("show");
  };

  t.directCheckinAccessory = function () {
    if (t.config.accessory_block_checkin == 1) {
      var data = {
        msg: "Sorry. Accessory checkin is not allowed.",
      };
      sweetAlert("center", "error", data);
      // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center">Sorry. Accessory checkin is not allowed.</div>' });
      return false;
    }
    t.chkin.resetFrm();
    t.httpPostPath = t.config.url.checkin;
    t.chkin.mdl.lblUserName.html(t.config.directChkin.checkoutTo);
    t.chkin.mdl.frmEl.id.val(t.config.directChkin.id);
    t.chkin.mdl.modal("show");
  };

  t.handleCheckinSubmit = function (e) {
    e.preventDefault();

    if (t.chkin.frmValidator.form() == false) {
      return false;
    }
    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.chkin.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
          t.chkin.mdl.modal("hide");
          t.refreshInfoTab();
          t.config.parent.userPhase.reload();
          t.config.parent.historyPhase.reload();
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };
  /* ------------- */
  t.fillCustomFields = function (data) {
    if (typeof data.html == "undefined") {
      return;
    }
    t.clearCustomFields();
    t.mdl.frmEl.customFieldsPrvEl.after(data.html);
    if (data.required_fields.length > 0) {
      $.each(data.required_fields, function (i, d) {
        t.mdl.frm.find("#" + d).rules("add", { required: true });
      });
    }
    $.each(data.all_fields, function (i, d) {
      t.mdl.frm.find("#" + d).rules("add", { remarks: false });
    });
    $(".cf-select2").each(function () {
      var $field = $(this);
      var fieldId = $field.attr("id");

      if (fieldId) {
        $field.select2(
          $.extend({}, select2Opts, { dropdownParent: $field.parent() }),
        );
        if ($field.hasClass("custFieldUser")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getUser",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_User,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
              templateResult: function (data) {
                if (typeof data.loading != "undefined" && data.loading) {
                  return $("<div>" + data.text + "</div>");
                }
                var email = data.email == null ? "" : data.email;
                var a = "";
                a += "<div class='row'>";
                a += "<div class='col-sm-10'>";
                if (data.displayName != null && data.displayName != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-user\"></i>" +
                    data.displayName;
                } else {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-user\"></i>" +
                    data.first_name +
                    " " +
                    data.last_name;
                }
                var active = "inactive-user";
                if (data.user_status == "Active") {
                  active = "active-user";
                }
                a += "<span class='" + active + "'></span>";
                a += "</div>";
                if (data.email != null && data.email != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" +
                    data.email +
                    "</div>";
                }
                if (data.employee_num != null && data.employee_num != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" +
                    data.employee_num +
                    "</div>";
                }
                a += "</div>";
                a += "<div class='col-sm-2'>";
                a +=
                  "<div><img class='img-u' src= '" +
                  data.img_path +
                  "'/></div>";
                a += "</div>";
                a += "</div>";
                return $("<div>" + a + "</div>");
              },
            }),
          );
        }

        if ($field.hasClass("custFieldLocation")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getLocation",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Location,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldDevice")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getDevice",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Device,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
              templateResult: function (data, container) {
                if (typeof data.loading != "undefined" && data.loading) {
                  return $("<div>" + data.text + "</div>");
                }
                a =
                  "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
                  data.asset_tag +
                  "</div>";
                if (data.asset_name != null) {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
                    data.asset_name +
                    "</div>";
                }
                a +=
                  "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
                  data.name +
                  " " +
                  data.modelno +
                  "</div>";
                a +=
                  "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
                  data.serial +
                  "</div>";
                return $("<div>" + a + "</div>");
              },
            }),
          );
        }

        if ($field.hasClass("custFieldModels")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getModel",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_model,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldSupplier")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getSupplier",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Supplier,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldProjects")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getProject",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_project,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldInternalPlace")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.ajaxGetInternalPlace,
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_internal_place,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldContract")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                // url: t.config.getLeaseByAjax,
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getContract",
                dataType: "json",
                data: function (p) {
                  return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                      return t.mdl.frmEl.company.val();
                    },
                  };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_contract,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldComponent")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getComponent",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_component,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldLicense")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getLicense",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_license,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldTasks")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getTask",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_task,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldChangeManagement")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getRecord",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_change_management,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldManufacturers")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery +
                  "/" +
                  "getManufacture",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_manufacturer,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldTickets")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getTicket",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_ticket,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldRequest")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery +
                  "/" +
                  "getTicketProcureRequest",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder:
                config.translations.select_the_ticket_procure_request,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldPurchase")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getPurchase",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Purchase_Invoice,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldDept")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.url.getAssetDepartments,
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Department,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }
      } else {
        console.warn(
          "Select2 field without ID found. Skipping initialization.",
        );
      }
    });
    // t.mdl.frm.find(".cf-select2").select2(select2Opts);
    // t.mdl.frm.find(".custFieldUser").select2($.extend({}, select2Opts, {
    //     // dropdownParent: t.mdl.frm.custFieldUser.parent(),
    //     ajax: {
    //         url: t.config.getUserByAjax,
    //         dataType: "json",
    //         data: function (p) {
    //             return {
    //                 search: p.term,
    //                 page: p.page || 1
    //             };
    //         },
    //         delay: 300
    //     },
    //     allowClear:true,
    //     placeholder: 'Select The User',
    //     templateSelection: function(data, container) {
    //         $(container).attr('title', data.text);
    //         return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    //     },
    // }));

    // t.mdl.frm.find(".custFieldLocation").select2($.extend({}, select2Opts, {
    //     // dropdownParent: t.mdl.frm.custFieldLocation.parent(),
    //     ajax: {
    //         url: t.config.getLocationByAjax,
    //         dataType: "json",
    //         data: function (p) {
    //             return {
    //                 search: p.term,
    //                 page: p.page || 1
    //             };
    //         },
    //         delay: 300
    //     },
    //     allowClear:true,
    //     placeholder: 'Select The Location'
    // }));
  };

  t.clearCustomFields = function () {
    t.mdl.find(".custom-field-row").remove();
  };

  t.loadForm = function (acc, forAction) {
    t.resetFrm();
    t.mdl.frmEl.id.val(acc.data.id);
    t.mdl.frmEl.unique_tag.val(acc.data.unique_tag);
    if (acc.data.company_id > 0) {
      t.mdl.frmEl.company.val(acc.data.company_id).trigger("change");
    }
    /*if (acc.data.category_id > 0) {
            t.mdl.frmEl.category.val(acc.data.category_id).trigger("change");
        }*/
    if (
      acc.data.purchase_currency != "" &&
      acc.data.purchase_currency != null
    ) {
      t.mdl.frmEl.purchase_currency
        .val(acc.data.purchase_currency)
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.location == "object"
    ) {
      t.mdl.frmEl.location
        .append(
          new Option(acc.dropdown.location.text, acc.dropdown.location.id),
        )
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.internal_place == "object" &&
      acc.dropdown.internal_place != null
    ) {
      t.internal_places.push(acc.dropdown.internal_place.id);
    }
    t.mdl.frmEl.internal_place.trigger("change");

    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.manufacturer == "object"
    ) {
      t.mdl.frmEl.manufacturer
        .append(
          new Option(
            acc.dropdown.manufacturer.text,
            acc.dropdown.manufacturer.id,
          ),
        )
        .trigger("change");
    }
    if (acc.dropdown.supplier != null) {
      current_supplier_name = acc.dropdown.supplier.text;
      current_supplier_id = acc.dropdown.supplier.id;
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.supplier == "object" &&
      acc.dropdown.supplier != null
    ) {
      t.mdl.frmEl.supplier
        .append(
          new Option(
            acc.dropdown.supplier.text,
            acc.dropdown.supplier.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.invoice == "object" &&
      acc.dropdown.invoice != null
    ) {
      t.mdl.frmEl.invoice_id
        .append(
          new Option(
            acc.dropdown.invoice.text,
            acc.dropdown.invoice.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    t.mdl.frmEl.threshold_alert_users.trigger("change");
    t.mdl.frmEl.threshold_alert_users.empty();
    if (
      typeof acc.dropdown == "object" &&
      Array.isArray(acc.dropdown.thresholdUserEmails) &&
      acc.dropdown.thresholdUserEmails.length > 0
    ) {
      acc.dropdown.thresholdUserEmails.forEach((user) => {
        if (user.text) {
          t.mdl.frmEl.threshold_alert_users.append(
            new Option(user.text, user.id, true, true),
          );
        }
      });
      t.mdl.frmEl.threshold_alert_users.trigger("change");
    }
    t.mdl.frmEl.reorder_limits.val(acc.data.reorder_limits);
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.accessory == "object" &&
      acc.dropdown.accessory != null
    ) {
      t.mdl.frmEl.category.attr("data-noLoadField", true);
      t.mdl.frmEl.category.append(
        new Option(
          acc.dropdown.accessory.text,
          acc.dropdown.accessory.id,
          true,
          true,
        ),
      );
      t.mdl.frmEl.category.attr("data-noLoadField", false);
      t.mdl.frmEl.category.trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.department == "object" &&
      acc.dropdown.department != null
    ) {
      t.mdl.frmEl.department_id
        .append(
          new Option(
            acc.dropdown.department.text,
            acc.dropdown.department.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    t.clearCustomFields();
    if (
      typeof acc.custom_fields == "object" &&
      typeof acc.custom_fields.html != ""
    ) {
      t.fillCustomFields(acc.custom_fields);
    }
    if (acc.data.requestable_accessory == 1) {
      t.mdl.frm
        .find("input[name='requestable_accessory']")
        .prop("checked", true);
    } else {
      t.mdl.frm
        .find("input[name='requestable_accessory']")
        .prop("checked", false);
    }
    if (acc.data.thresholds_alerts == 1) {
      t.mdl.frm.find("input[name='thresholds_alerts']").prop("checked", true);
    } else {
      t.mdl.frm.find("input[name='thresholds_alerts']").prop("checked", false);
    }
    t.mdl.frm
      .find("input[name='accessory_thresholds']")
      .val(acc.data.accessory_thresholds);
    t.mdl.frm.find("input[name='name']").val(acc.data.name);
    t.mdl.frm.find("input[name='qty']").val(acc.data.qty);
    order_no = acc.data.order_number;
    t.mdl.frm.find("input[name='order_number']").val(acc.data.order_number);
    //for select2 Purchase Reference dropdown error fix
    currency_default = acc.data.purchase_currency;
    //for select2 Purchase Reference dropdown error fix
    t.mdl.frmEl.purchase_currency.val(currency_default).trigger("change");
    current_purchase_date = acc.data.purchase_date;
    purchase_cost = acc.data.purchase_cost;
    t.mdl.frm.find("input[name='purchase_cost']").val(acc.data.purchase_cost);
    t.mdl.frmEl.purchase_date.datepicker("update", current_purchase_date);
    t.mdl.frmEl.notes.val($.trim(acc.data.notes));
    t.loadImageViewer(acc.data.image);
    t.mdl.modal("show");
  };

  t.loadImageViewer = function (img) {
    if (
      img != "" &&
      img != null &&
      (t.forAction == "edit" || t.forAction == "clone")
    ) {
      t.mdl.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + img);
      t.mdl.frmEl.imgviewcover.removeClass("hide");
      if (t.forAction == "clone") {
        t.mdl.frmEl.clone_img.val(img);
      }
    } else {
      t.mdl.frmEl.imgview.attr("src", "");
      t.mdl.frmEl.imgviewcover.addClass("hide");
    }
  };

  t.editAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.edit + "/" + accId;
    var http = $.get(t.config.url.get + "/" + accId);
    t.forAction = "edit";
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(t.config.translations.edit_accessory);
          t.mdl.btnSubmit.text(t.config.translations.save);
          t.mdl.forAction = "edit";
          if (
            typeof t.config.custom_fields == "object" &&
            typeof t.config.custom_fields.html != ""
          ) {
            t.fillCustomFields(t.config.custom_fields);
          }
          t.mdl.frm.find("input[name='qty']").attr("readonly", true);
          t.mdl.frm.find("input[name='unique_tag']").attr("readonly", true);
          t.loadForm(data.accessory, "edit");
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.cloneAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.add;
    var http = $.get(t.config.url.get + "/" + accId);
    t.forAction = "clone";
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(t.config.translations.add_new_accessory);
          t.mdl.btnSubmit.text(t.config.translations.save);
          t.mdl.forAction = "clone";
          t.mdl.frm.find("input[name='qty']").attr("readonly", false);
          t.mdl.frm.find("input[name='unique_tag']").attr("readonly", false);
          t.loadForm(data.accessory, "clone");
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: "Something went wrong. Please check given details are correct",
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.deleteAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.delete + "/" + accId;
    var data = {
      msg: "Something went wrong. Please check given details are correct",
    };
    sweetAlerts(
      "Are you sure to delete this accessory?",
      "warning",
      t.httpPostPath,
      t.config.url.accessories,
      data,
      "url_yes",
    );
    // vex.dialog.confirm({
    //     message: 'Are you sure to delete this accessory?',
    //     callback: function(value) {
    //         if (value == true) {
    //             var http = $.get(t.httpPostPath);
    //             http.done(function(data) {
    //                 if (typeof data == "object") {
    //                     if (data.status == "success") {
    //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
    //                         window.location = t.config.url.accessories;
    //                     } else {
    //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
    //                     }
    //                 }
    //             });
    //             http.fail(function() {
    //                 // alert("Something went wrong. Please check given details are correct");
    //             });
    //             http.always(function() {
    //                 t.httpCall = true;
    //             });
    //         }
    //     }
    // });
  };

  t.refreshInfoTab = function () {
    t.infoTab.load(
      t.config.url.accessory_basic_info + "/" + t.config.accessory_id,
    );
  };

  // reset options for purchase currency
  t.mdl.optionsCurrency = function () {
    t.mdl.frmEl.purchase_currency
      .empty()
      .append(new Option("Select Currency Format", ""));
    $.each(t.config.currencies, function (i, v) {
      var opt =
        t.config.default_currency_format == i
          ? new Option("", i, true, true)
          : new Option("", i);
      opt.innerHTML = v.name + " (" + v.symbol_html + ")";
      t.mdl.frmEl.purchase_currency.append(opt);
    });
    t.mdl.frmEl.purchase_currency.trigger("change");
  };

  t.resetFrm = function () {
    t.mdl.frm.trigger("reset");
    t.frmValidator.resetForm();
    t.mdl.frmEl.location.empty().trigger("change");
    t.mdl.frmEl.manufacturer.empty().trigger("change");
    t.mdl.frmEl.supplier.empty().trigger("change");
    t.mdl.frmEl.company.val(0).trigger("change");
    t.mdl.frmEl.category.val(0).trigger("change");
    t.mdl.frmEl.invoice_id.empty().trigger("change");
    t.mdl.frmEl.department_id.empty().trigger("change");
    t.mdl.optionsCurrency();
    t.getInternalPlace();
  };

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    rules: {
      company_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
        str_name: true,
      },
      name: {
        required: true,
        str_name: true,
        clean_text_only: true,
      },
      category_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
        str_name: true,
      },
      qty: {
        required: true,
        digits: true,
        min: 1,
        remarks: true,
      },
      location_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
      },
      purchase_cost: {
        number: true,
      },
      notes: {
        remarks: true,
        clean_text_only: true,
      },
      order_number: {
        str_name: true,
        clean_text_only: true,
      },
      accessory_thresholds: {
        required: true,
        digits: true,
        min: 0,
      },
      image: {
        accept: "image/jpeg, image/jpg, image/png",
        extension: "jpg|jpeg|png",
        filesize: 2048000,
      },
    },
    messages: {
      image: {
        required: "Please select an image.",
        accept: "Only JPEG, JPG, and PNG images are allowed.",
        extension: "Only JPEG, JPG, and PNG images are allowed.",
        filesize: "Image must be less than 2 MB.",
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },
  });
  // function checkThresholdValidation() {
  //     var thresholdValue = t.mdl.frmEl.accessory_thresholds.val();
  //     var thresholdAlertValue = t.mdl.frmEl.threshold_alert_users.val();
  //     if((thresholdValue > 0 && thresholdValue != null) && thresholdAlertValue === null){
  //         $("#threshold_alert_users").rules("add", {
  //             required: true,
  //             messages: { required: "This field is required when thresholds are set." }
  //         });
  //     } else{
  //         $("#threshold_alert_users").rules("remove", "required");
  //     }
  //     t.frmValidator.element("#threshold_alert_users");
  // }

  // $("#accessory_thresholds").on("input", function () {
  //     checkThresholdValidation();
  // });

  // $("#btnSubmit").on("click", function (e) {
  //     checkThresholdValidation();

  //     if (!t.mdl.frm.valid()) {
  //         e.preventDefault();
  //     }
  // });
  t.handleSubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }

    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    // var http = $.post(t.httpPostPath, t.mdl.frm.serialize());
    var formData = new FormData(t.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
          t.mdl.modal("hide");
          if (t.mdl.forAction == "clone") {
            window.location = t.config.url.info + "/" + data.accessory_id;
          } else {
            t.refreshInfoTab();
          }
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlerts(
        config.translations.are_you_delete,
        "warning",
        t.httpPostPath,
        t.dTbl,
        data,
      );
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  var select2Opts = { width: "100%" };
  t.config.companies.unshift({ id: 0, text: "Select Company" });
  t.mdl.frmEl.company.select2(
    $.extend({}, select2Opts, { data: t.config.companies }),
  );
  t.config.categories.unshift({ id: 0, text: "Select Category" });
  t.mdl.frmEl.category.select2(
    $.extend({}, select2Opts, { data: t.config.categories }),
  );
  t.mdl.frmEl.purchase_currency.select2(select2Opts);

  t.mdl.frmEl.threshold_alert_users.select2(
    $.extend({}, select2Opts, {
      // dropdownParent: t.mdl.frmEl.threshold_alert_users.parent(),
      dropdownParent: t.mdl,
      ajax: {
        url: config.url.getActivatedUsers,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            // company_id: function () {
            //   return t.mdl.frmEl.company.val();
            // },
            company_id: t.mdl.frmEl.company.val(),
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: "Select The User",
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        var email = s.email == null ? "" : s.email;
        var a = "";
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        if (s.displayName != null && s.displayName != "") {
          a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.displayName;
        } else {
          a +=
            "<div class='so-t'><i class=\"fa fa-user\"></i>" +
            s.first_name +
            " " +
            s.last_name;
        }
        a += "<span class='active-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" +
            s.email +
            "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" +
            s.employee_num +
            "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
      },
    }),
  );
  t.mdl.frmEl.department_id.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.department_id.parent(),
      ajax: {
        url: t.config.url.getAssetDepartments,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: config.translations.Select_the_Department,
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 55
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );
  t.mdl.frmEl.location
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.location.parent(),
        ajax: {
          url: t.config.getLocationByAjax,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300,
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: "Select the Location",
      }),
    )
    .on("change", function (e) {
      t.getInternalPlace();
    });

  //  Internal Place Dropdown Initialization (Fix Applied)
  t.mdl.frmEl.internal_place.select2(
    $.extend({}, select2Opts, {
      placeholder: "Select the Internal Place", // Fix: Directly pass string
      allowClear: true,
      dropdownParent: t.mdl.frmEl.internal_place.parent(),
    }),
  );

  //  Function to Fetch Internal Places (Fixed)
  t.getInternalPlace = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }

    t.mdl.frmEl.internal_place
      .empty()
      .append(
        new Option(config.translations.select_internal_place, "", true, true),
      );
    t.mdl.frmEl.internal_place.trigger("change");

    var location_id = t.mdl.frmEl.location.val();

    if (location_id > 0) {
      $.get(t.config.getInternalPlaceByAjax + "/" + location_id)
        .done(function (data) {
          if (typeof data == "object" && data.results.length) {
            $.each(data.results, function (i, v) {
              if ($.inArray(v.id, t.internal_places) !== -1) {
                t.mdl.frmEl.internal_place.append(
                  new Option(v.text, v.id, true, true),
                );
              } else {
                t.mdl.frmEl.internal_place.append(
                  new Option(v.text, v.id, false, false),
                );
              }
            });
          }
        })
        .always(function () {
          t.mdl.frmEl.internal_place.trigger("change");
          t.internal_places = [];
        });
    }
  };

  t.mdl.frmEl.invoice_id
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.invoice_id.parent(),
        ajax: {
          url: t.config.getInvoiceByAjax,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300,
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: "Select the Purchase Invoice",
        templateSelection: function (s, container) {
          if (typeof s.loading != "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
          }
          $(s.element).attr({
            "data-currency": s.currency,
            "data-purchase-cost": s.purchase_cost,
            "data-invoice_date": s.invoice_date,
            "data-name": s.supplier_name,
            "data-id": s.supplier_id,
            "data-po-number": s.po_number,
            "data-order-number": s.order_number,
          });
          return s.text;
        },
      }),
    )
    .on("change", function (e) {
      if (!t.mdl.frmEl.invoice_id.val()) {
        if (current_purchase_date != null) {
          purchasedate = current_purchase_date.split("-").join("/");
          t.mdl.frmEl.purchase_date.val(purchasedate);
        } else {
          t.mdl.frmEl.purchase_date.val(null);
        }
        if (current_supplier_name != "" && current_supplier_id != "") {
          t.mdl.frmEl.supplier
            .append(
              new Option(
                current_supplier_name,
                current_supplier_id,
                true,
                true,
              ),
            )
            .trigger("change");
        } else {
          t.mdl.frmEl.supplier.empty("").trigger("change");
        }
        if (purchase_cost != null && purchase_cost != null) {
          t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost);
        } else {
          t.mdl.frm.find("input[name='purchase_cost']").val("");
        }
      } else {
        t.getPurchaseDate();
      }
    });

  t.getPurchaseDate = function (e) {
    //this part is added to handle re-selection from Select2 dropdown
    var selectedOption = t.mdl.frmEl.invoice_id.find(":selected");
    if (!selectedOption.attr("data-invoice_date")) {
      // Use stored default values for options without data attributes
      if (current_purchase_date != null) {
        purchasedate = current_purchase_date.split("-").join("/");
        t.mdl.frmEl.purchase_date.val(purchasedate);
      } else {
        t.mdl.frmEl.purchase_date.val("");
      }
      t.mdl.frmEl.supplier.empty();
      if (current_supplier_name && current_supplier_id) {
        t.mdl.frmEl.supplier
          .append(
            new Option(current_supplier_name, current_supplier_id, true, true),
          )
          .trigger("change");
      }
      if (order_no != null) {
        t.mdl.frm.find("input[name='order_number']").val(order_no);
      } else {
        t.mdl.frm.find("input[name='order_number']").val("");
      }
      if (purchase_cost != null) {
        t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost);
      } else {
        t.mdl.frm.find("input[name='purchase_cost']").val("");
      }
      if (currency_default != null) {
        t.mdl.frmEl.purchase_currency.val(currency_default).trigger("change");
      } else {
        t.mdl.frmEl.purchase_currency.val("").trigger("change");
      }
      return;
    }
    //end this part is added to handle re-selection from Select2 dropdown
    var date = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-invoice_date");
    t.mdl.frmEl.purchase_date.val(date);
    var supplier_name = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-name");
    var supplier_id = t.mdl.frmEl.invoice_id.find(":selected").attr("data-id");
    t.mdl.frmEl.supplier
      .append(new Option(supplier_name, supplier_id, true, true))
      .trigger("change");
    if (
      supplier_name == undefined &&
      supplier_id == undefined &&
      t.forAction == "edit"
    ) {
      t.mdl.frmEl.supplier
        .append(
          new Option(current_supplier_name, current_supplier_id, true, true),
        )
        .trigger("change");
    }
    var order_number = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-po-number");
    t.mdl.frm.find("input[name='order_number']").val(order_number);
    var purchase_cost = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-purchase-cost");
    t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost || "");
    var purchase_currency = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-currency");
    t.mdl.frmEl.purchase_currency.val(purchase_currency).trigger("change");
  };

  t.mdl.frmEl.manufacturer.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.manufacturer.parent(),
      ajax: {
        url: t.config.getManufacturerByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: "Select the manufacturer",
    }),
  );
  t.chkout.mdl.frmEl.assigned_to.select2(
    $.extend({}, select2Opts, {
      ajax: {
        url: t.config.getUserByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: "Select the User",
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        a =
          "<div class='row'><div class='col-sm-10'><div class='so-t'><i class=\"fa fa-user\"></i> " +
          s.username +
          "</div><div class='so-t'><i class=\"fa fa-user\"></i> " +
          s.first_name +
          " " +
          s.last_name +
          "</div><div class='so-t'><i class=\"fa fa-envelope-o\"></i> " +
          s.email +
          "</div><div class='so-t'><i class=\"fa fa-card\"></i> " +
          s.employee_num +
          "</div> </div><div class='col-sm-2'><div><img class='img-u' src='" +
          s.img_path +
          "'/></div> </div></div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (s, container) {
        $(container).attr("title", s.text);
        return s.text.length > 50 ? s.text.substring(0, 50) + "..." : s.text;
      },
    }),
  );
  t.chkout.mdl.frmEl.device_id.select2(
    $.extend({}, select2Opts, {
      // dropdownParent: t.chkout.mdl.frmEl.device_id.parent(),
      ajax: {
        url: t.config.getDeviceByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: "Select the Device",
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        a =
          "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
          s.asset_tag +
          "</div>";
        if (s.asset_name != null) {
          a +=
            "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
            s.asset_name +
            "</div>";
        }
        a +=
          "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
          s.name +
          " " +
          s.modelno +
          "</div>";
        a +=
          "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
          s.serial +
          "</div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (s, container) {
        $(container).attr("title", s.text);
        return s.text.length > 50 ? s.text.substring(0, 50) + "..." : s.text;
      },
    }),
  );
  t.mdl.frmEl.supplier.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.supplier.parent(),
      ajax: {
        url: t.config.getSupplierByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: "Select the Supplier",
    }),
  );

  t.getCustomFields = function (e) {
    e.preventDefault();
    if (t.mdl.frmEl.category.attr("data-noLoadField") == "true") {
      return;
    }

    t.clearCustomFields();
    if (
      typeof t.config.custom_fields == "object" &&
      typeof t.config.custom_fields.html != ""
    ) {
      t.fillCustomFields(t.config.custom_fields);
    }
    var category_id = parseInt(t.mdl.frmEl.category.val());
    if (category_id < 1 || isNaN(category_id)) {
      return;
    }
    var type_id = t.mdl.frmEl.id.val();
    var http = $.get(
      t.config.getCustomFieldsByCategory +
        "/" +
        category_id +
        "/" +
        "accessory" +
        "/" +
        type_id,
    );
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.fillCustomFields(data);
        }
      }
    });
    http.fail(function () {
      alert(config.translations.something_went_wrong);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.handleExpectedCheckinDateSubmit = function (e) {
    e.preventDefault();

    if (t.httpCall != true) {
      return false;
    }

    t.httpCall = false;
    var checkoutId = t.chkindate.mdl.frmEl.id.val();

    var formData = new FormData();
    formData.append("_token", t.config.token);
    formData.append(
      "expected_checkin",
      t.chkindate.mdl.frmEl.expected_checkin.val(),
    );
    var url = t.config.url.update_expected_checkin;

    if (url.indexOf("{id}") === -1 && url.indexOf(":id") === -1) {
      url = url + "/" + checkoutId;
    } else {
      url = url.replace("{id}", checkoutId).replace(":id", checkoutId);
    }

    var http = $.ajax({
      url: url,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.chkindate.mdl.modal("hide");
          t.config.parent.userPhase.reload();
          t.config.parent.historyPhase.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });

    http.fail(function () {
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });

    http.always(function () {
      t.httpCall = true;
    });
  };

  t.chkindate.mdl.frmEl.expected_checkin =
    t.chkindate.mdl.frm.find("#expected_checkin");
  t.chkindate.mdl.frmEl.expected_checkin.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
  });

  t.chkout.mdl.frmEl.assigned_for.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.assigned_for.parent(),
      data: [
        { id: 1, text: "User" },
        { id: 2, text: "Place" },
        { id: 3, text: "Device" },
      ],
    }),
  );

  t.chkout.mdl.frmEl.assigned_place.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.assigned_place.parent(),
    }),
  );

  t.mdl.frmEl.purchase_date.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
  });
  t.chkout.mdl.frmEl.expected_checkin.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
  });
  t.chkout.mdl.frmEl.assigned_for.on(
    "change",
    $.proxy(t.chkout.switchCheckTarget),
  );

  t.btn.editAccessory.on("click", $.proxy(t.editAccessory));
  t.btn.cloneAccessory.on("click", $.proxy(t.cloneAccessory));
  t.btn.deleteAccessory.on("click", $.proxy(t.deleteAccessory));
  t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
  t.chkout.mdl.btnSubmit.on("click", $.proxy(t.handleCheckoutSubmit));
  t.chkin.mdl.btnSubmit.on("click", $.proxy(t.handleCheckinSubmit));
  t.infoTab.on("click", ".dtActCheckOut", $.proxy(t.checkoutAccessory));
  t.infoTab.on("click", ".dtActScrap", $.proxy(t.scrapAccessory));
  t.infoTab.on("click", ".dtActScrapRevert", $.proxy(t.revertScrapAccessory));
  t.scrap.mdl.btnSubmit.on("click", $.proxy(t.handleScrapSubmit));
  t.revertScrap.mdl.btnSubmit.on("click", $.proxy(t.handleRevertScrapSubmit));
  t.mdl.frmEl.category.on("change", $.proxy(t.getCustomFields));
  t.chkindate.mdl.btnSubmit.on(
    "click",
    $.proxy(t.handleExpectedCheckinDateSubmit),
  );
  t.refreshInfoTab();

  if (typeof t.config.directChkin != "undefined" && t.config.directChkin) {
    t.directCheckinAccessory();
  }
};

var MyApp = function (config) {
  var t = this;
  config.parent = t;
  t.content = $("section.content");
  t.accessoryPhase = new AccessoryPhase(config);
  t.userPhase = null;
  t.documentPhase = null;
  t.PurchasePhase = null;
  t.historyPhase = null;
  t.documentUploadPhase = new DocumentUploadPhase(config);
  $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
    var target = $(e.target).attr("href");
    if (target === "#users-tab" && !t.userPhase) {
  t.userPhase = new UserPhase(config);
    }
    if (target === "#documents-tab" && !t.documentPhase) {
  t.documentPhase = new DocumentPhase(config);
    }
    if (target === "#purchase-tab" && !t.PurchasePhase) {
  t.PurchasePhase = new PurchasePhase(config);
    }
    if (target === "#history-tab" && !t.historyPhase) {
  t.historyPhase = new HistoryPhase(config);
    }
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
};