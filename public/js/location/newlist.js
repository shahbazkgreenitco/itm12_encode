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

function escapeHtml(text) {
  return text.replace(/[&<>"'`=\/]/g, function (s) {
    return entityMap[s];
  });
}
var LocationApp = function (config) {
  var t = this;

  t.config = config;
  t.table = $("#mytable");
  t.content = $("section.content");

  t.modal = $("#locationmd1");
  t.form = $("#locationform");
  t.show_entries = $(".userModulePageLenth");

  t.dTable = null;
  t.isEdit = false;
  t.submitUrl = "";

  t.btn = {
    reload: ".btn-reload-list",
    add: ".open-add-modal",
    import: ".btn-import",
    download: ".btn-download",
  };

  t.init();
};

//    INIT
LocationApp.prototype.init = function () {

  var t = this;

  t.mapElements();
  t.initSelect2();
  t.initValidation();

  setTimeout(function () {
    t.initDataTable();
  }, 50);

  t.bindEvents();
};

LocationApp.prototype.mapElements = function () {
  var t = this;

  t.frmEl = {
    name: $("#name"),
    parent_id: $("#parent_id"),
    currency: $("#currency"),
    branch_code: $("#branch_code"),
    zone: $("#zone"),
    address: $("#address"),
    address2: $("#address2"),
    city_id: $("#city_id"),
    state_id: $("#state_id"),
    country_id: $("#country_id"),
    zip: $("#zip"),
    location_user: $("#location_user"),
    company_id: $("#company_id"),
  };

  t.btn.submit = $("#btnSubmit");
};

// select 2
LocationApp.prototype.initSelect2 = function () {
  var t = this;

  function init(el, options = {}) {
    if (el.hasClass("select2-hidden-accessible")) {
      el.select2("destroy");
    }

    el.select2({
      width: "100%",
      dropdownParent: t.modal.find(".modal-content"),
      ...options,
    });
  }

  init(t.frmEl.company_id, {
    placeholder: config.translations.select_company,
    ////allowClear: true,
    ajax: {
      url: t.config.url.getCompanyByUserAccess,//only get companies which the user has access to.
      dataType: "json",
      delay: 300,
      data: (p) => ({ search: p.term, page: p.page || 1 }),
    },
  });

  init(t.frmEl.parent_id, {
    placeholder: config.translations.parent_location,
    //allowClear: true,
    ajax: {
      url: t.config.url.parent_location,
      dataType: "json",
      delay: 300,
      data: (p) => ({ search: p.term, page: p.page || 1, company_id: t.frmEl.company_id.val() }),
    },
  });

  init(t.frmEl.currency, {
    placeholder: config.translations.location_currency,
    //allowClear: true,
  });

  init(t.frmEl.country_id, {
    placeholder: config.translations.select_country,
    //allowClear: true,
    ajax: {
      url: t.config.url.country,
      dataType: "json",
      delay: 300,
      data: (p) => ({ search: p.term, page: p.page || 1 }),
    },
  });

  init(t.frmEl.state_id, {
    placeholder: config.translations.select_state,
    //allowClear: true,
    ajax: {
      url: t.config.url.state,
      dataType: "json",
      delay: 300,
      data: (p) => ({
        search: p.term,
        page: p.page || 1,
        country_id: t.frmEl.country_id.val(),
      }),
    },
  });

  init(t.frmEl.city_id, {
    placeholder: config.translations.select_city,
    //allowClear: true,
    ajax: {
      url: t.config.url.city,
      dataType: "json",
      delay: 300,
      data: (p) => ({
        search: p.term,
        page: p.page || 1,
        state_id: t.frmEl.state_id.val(),
      }),
    },
  });

  init(t.frmEl.location_user, {
    placeholder: config.translations.location_admin,
    //allowClear: true,
    ajax: {
      url: t.config.url.getUserByAjax,
      dataType: "json",
      delay: 300,
      data: (p) => ({ search: p.term, page: p.page || 1 }),
    },
  });
};

// select 2 end

//Datatable
LocationApp.prototype.initDataTable = function () {
  var t = this;
  t.dTable = t.table.DataTable({
    language: datatable_footer_translations(t.config.datatable_translations),
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    lengthChange: false,
    pageLength: 10,
    dom: "ltrip",
    fixedColumns: {
      rightColumns: 1,
    },

    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        if (d.order && d.order.length > 0) {
          let orderInfo = d.order[0];
          let columnIndex = orderInfo.column;
          let direction = orderInfo.dir;
          let columnName = d.columns[columnIndex].data;
          d.sorted_column_name = columnName;
          d.sorted_direction = direction;
        }
      },
    },

    order: [[0, "asc"]],

    columns: [
      {
        data: "a.id",
       
      },
      { data: "a.name", render: t.renderText("b5-text") },
      { data: "a.company_name", render: t.renderText("b5-text") },
      {
        data: "a.parent_location.name",
        defaultContent: "",
        render: t.renderText("b5-text"),
      },
      { data: "a.branch_code", render: t.renderText("b5-text") },
      { data: "a.zone", render: t.renderText("b5-text") },

      {
        data: null,
        render: function (d, type) {
        if (type !== "display") return d;
            let fullAddress = `${d.a.address ?? ""} ${d.a.address2 ?? ""}`.trim();
            let shortAddress = fullAddress;

            if (shortAddress.length > 30) {
                shortAddress = shortAddress.substring(0, 30) + "...";

                return `
                    <span class="b5-text">
                        ${escapeHtml(shortAddress)}
                        <a href="#" class="read-more-address"
                          data-full-address="${escapeHtml(fullAddress)}">
                          Read More
                        </a>
                    </span>
                `;
            }
            return `<span class="b5-text">${escapeHtml(fullAddress || "-")}</span>`;
        },
      },
      {
        data: "a.city_name.name",
        defaultContent: "",
        render: t.renderText("b5-text"),
      },
      {
        data: "a.state_name.name",
        defaultContent: "",
        render: t.renderText("b5-text"),
      },
      {
        data: "a.country_name.name",
        defaultContent: "",
        render: t.renderText("b5-text"),
      },
      { data: "a.zip", render: t.renderText("b5-text") },
      {
        data: "a.updated_at",
        visible: false,
        searchable: false,
      },
      {
        data: "a",
        orderable: false,
        searchable: false,
        className: "users-col-actions",
        render: function (d) {
          return t.buildActions(d);
        },
      },
    ],
    fnInitComplete: function () {
      var api = this.api();


      $("#user-list-search")
        .off("keyup")
        .on("keyup", function (e) {
          if(e.keyCode == 13){
          let value = this.value;

            api.search(value).draw();
          };
        });

      $(".amg-list-searchbar__icon")
        .off("click")
        .on("click", function () {
          api.search($("#user-list-search").val()).draw();
        });
        
        function updateColumnVisibilityControls() {
          let controls = $("#columnVisibilityControls").empty();
          api.columns().every(function () {
              let columnIndex = this.index();
              // Skip hidden updated_at column and action column
              if (columnIndex === 11) {
                  return;
              }
              let columnTitle = $(this.header()).text().trim();
              let columnId = `column-toggle-${columnIndex}`;
              controls.append(`
                  <div class="dropdown-item">
                      <label>
                          <input
                              type="checkbox"
                              id="${columnId}"
                              data-column="${columnIndex}"
                              ${this.visible() ? "checked" : ""}
                          >
                          ${columnTitle}
                      </label>
                  </div>
              `);
          });
        }

        updateColumnVisibilityControls();

        $("#columnVisibilityControls").on(
            "change",
            'input[type="checkbox"]',
            function () {
                api.column(+$(this).data("column")).visible(this.checked);
            }
        );

        api.on("column-reorder", function () {
            setTimeout(updateColumnVisibilityControls, 10);
        });

        $(".show-hide-columns").on("click", function (e) {
            e.stopPropagation();
            $("#columnVisibilityControls").toggleClass("show");
        });

        $(document).on("click", function (e) {
            if (!$(e.target).closest(".column-toggle-wrapper").length) {
                $("#columnVisibilityControls").removeClass("show");
            }
        });
    },
    drawCallback: function() {
      $('[data-bs-toggle="tooltip"]').tooltip();
    }
  });
};

LocationApp.prototype.renderText = function (cls) {
  return function (data, type) {
    if (type !== "display") return data;
    return `<span class="${cls}">${data ?? ""}</span>`;
  };
};

LocationApp.prototype.getIcon = function (key) {
  return this.icons[key] ? this.icons[key]() : "";
};

LocationApp.prototype.icons = {
  edit: function () {
    return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg> ';
  },
  delete: function () {
    return ' <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg> ';
  },
};

LocationApp.prototype.buildActions = function (d) {
  var t = this;
  let actions = [];
  if (t.hasPermission("LocationEdit")) {
    actions.push(`
      <button class="user-list-action-btn open-edit-modal"
          data-id="${d.id}" data-bs-toggle="tooltip" title="${t.config.translations.edit}">
          ${t.getIcon("edit")}
      </button>
    `);
  }
  if (t.hasPermission("LocationDelete")) {
    actions.push(`
      <button class="user-list-action-btn go-del is-delete"
          data-id="${d.id}" data-bs-toggle="tooltip" title="${t.config.translations.edit}">
          ${t.getIcon("delete")}
      </button>
    `);
  }
  if (!actions.length) {
    return `<span class="user-list-empty">-</span>`;
  }
  return `
    <div class="gap-2 d-flex justify-content-center">
        ${actions.join("")}
    </div>
  `;
};

LocationApp.prototype.initValidation = function () {
  var t = this;
  t.validator = t.form.validate({
    rules: {
      company_id: {
        required: true,
      },
      name: {
        required: true,
        minlength: 2,
        maxlength: 100,
        normalizer: function (value) {
          return $.trim(value);
        },
        clean_text_only:true
      },
      address: {
        required: true,
        clean_text_only:true,
        maxlength: 80
      },
      country_id: {
        required: true,
      },
      branch_code: {
        required:false,
        clean_text_only:true
      },
      zone: {
        required:false,
        clean_text_only:true
      },
      address2: {
        required:false,
        clean_text_only:true,
        maxlength: 255
      },

      state_id: {
        required: true,
      },
      city_id: {
        required: true,
      },
      zip: {
        digits: true,
        maxlength: 20
      },
    },
  messages: {
    company_id: {
        required: t.config.translations.select_company,
    },
    name: {
          required: t.config.translations.location_name_required,
          minlength: t.config.translations.location_name_min,
          maxlength: t.config.translations.location_name_max,
      },
      address: {
          required: t.config.translations.address_required,
      },
      country_id: {
          required: t.config.translations.select_country,
      },
      state_id: {
          required: t.config.translations.select_state,
      },
      city_id: {
          required: t.config.translations.select_city,
      },
      zip: {
          digits: t.config.translations.zip_digits,
      },
  },

    errorElement: "span",
    errorClass: "error",

    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },

    highlight: function (element, errorClass) {
      $(element).closest(".select-div").addClass(errorClass);
    },

    unhighlight: function (element, errorClass) {
      $(element).closest(".select-div").removeClass(errorClass);
    },

    submitHandler: function () {
      t.submitForm();
    },
  });
};

LocationApp.prototype.hasPermission = function (name) {
  return this.config.permissions.includes(name);
};

LocationApp.prototype.bindEvents = function () {
  var t = this;

  t.content.on("click", t.btn.reload, () => t.reload());
  t.content.on("click", t.btn.add, (e) => t.create(e));
  t.content.on("click", ".open-edit-modal", (e) => t.edit(e));
  t.content.on("click", ".go-del", (e) => t.delete(e));
  t.content.on(
    "click",
    t.btn.import,
    () => (window.location = t.config.url.import),
  );
  t.content.on("click", t.btn.download, function () {
    let search = $("#user-list-search").val();

    let obj = {
      search: search,
      other_filters: {},
    };

    let encoded = btoa(JSON.stringify(obj));

    let url = t.config.url.download_url + "?q=" + encoded;

    window.location = url;
  });

  t.btn.submit.on("click", (e) => t.submit(e));

  t.show_entries.on("change", function () {
    let value = parseInt($(this).val(), 10);

    if (t.dTable) {
      t.dTable.page.len(value).draw();
    }
  });
  
  // on parent change, reset child fields to null
  t.frmEl.company_id.on('change',function(){
    t.frmEl.parent_id.val(null).trigger('change')
  });
  t.frmEl.country_id.on('change', function () {
      t.frmEl.state_id.val(null).trigger('change');
      t.frmEl.city_id.val(null).trigger('change');
      t.frmEl.zip.val('');
  });

  t.frmEl.state_id.on('change', function () {
      t.frmEl.city_id.val(null).trigger('change');
      t.frmEl.zip.val('');
  });

  t.frmEl.city_id.on('change', function () {
      t.frmEl.zip.val('');
  });

  // if error exist on dropdown fields, on selecting any option remove error messages. 
  $('select').on('change', function () {
      var element = $(this);
      if(element.val() && element.val() != null){
        var el2 = element.closest('.input-group').siblings('.error');

        if (el2.length) {
            el2.remove();
        }
      }
  });

  $(document).on("click", ".read-more-address", function(e) {
    e.preventDefault();
    let fullText = $(this).data("full-address");
    Swal.fire({
        title: "Full Address",
        html: fullText,
        width: 700,
    });
  });

};

LocationApp.prototype.create = function (e) {
  var t = this;
  e.preventDefault();
  t.isEdit = false;
  t.submitUrl = t.config.url.add;
  t.resetForm();
  t.modal.find(".modal-title").text(t.config.translations.create_location);
  t.modal.modal("show");
};

LocationApp.prototype.edit = function (e) {
  var t = this;
  e.preventDefault();

  var id = $(e.currentTarget).data("id");
  t.submitUrl = t.config.url.edit + "/" + id;
  $.get(t.config.url.get + "/" + id, function (res) {
    if (res.status === "success") {
      var d = res.data;

      t.isEdit = true;
      t.resetForm();

      t.frmEl.name.val(d.name);
      t.frmEl.branch_code.val(d.branch_code);
      t.frmEl.zone.val(d.zone);
      t.frmEl.address.val(d.address);
      t.frmEl.address2.val(d.address2);
  

      if (d.currency) {
        t.frmEl.currency.val(d.currency).trigger("change");
      }
      if (d.countries) {
        t.frmEl.country_id
          .append(new Option(d.countries.text, d.countries.id, true, true))
          .trigger("change");
      }

      if (d.states) {
        t.frmEl.state_id
          .append(new Option(d.states.text, d.states.id, true, true))
          .trigger("change");
      }

      if (d.cities) {
        t.frmEl.city_id
          .append(new Option(d.cities.text, d.cities.id, true, true))
          .trigger("change");
      }

      if (d.location_user_id && d.location_user_id.length > 0) {
        t.frmEl.location_user.empty();

        $.each(d.location_user_id, function (i, v) {
          t.frmEl.location_user.append(new Option(v.text, v.id, true, true));
        });

        t.frmEl.location_user.trigger("change");
      }
      if (d.company_id && d.company_id.id) {
        t.frmEl.company_id
          .empty()
          .append(new Option(d.company_id.text, d.company_id.id, true, true))
          .trigger("change");
      }

      if (d.parent_locations) {
        t.frmEl.parent_id
          .append(
            new Option(
              d.parent_locations.text,
              d.parent_locations.id,
              true,
              true,
            ),
          )
          .trigger("change");
      }
      t.frmEl.zip.val(d.zip);

      t.modal.find(".modal-title").text(t.config.translations.edit_location);
      t.modal.modal("show");
    }
  });
};

LocationApp.prototype.submit = function (e) {
  var t = this;
  e.preventDefault();

  if (!t.form.valid()) {
    return false;
  }

  var formData = new FormData(t.form[0]);
  formData.append("_token", t.config.token);

  $("#img").show();
  t.btn.submit.prop("disabled", true);

  $.ajax({
    url: t.submitUrl,
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,

    success: function (res) {
      if (res.status === "success") {
        t.modal.modal("hide");
        t.dTable.ajax.reload();

        Swal.fire({
          icon: "success",
          title: "Success",
          text: res.msg || "Saved successfully",
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: res.msg || "Something went wrong",
          confirmButtonText: "OK",
        });
      }
    },

    error: function () {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Something went wrong",
        confirmButtonText: "OK",
      });
    },

    complete: function () {
      $("#img").hide();
      t.btn.submit.prop("disabled", false);
    },
  });
};

LocationApp.prototype.delete = function (e) {
  var t = this;
  e.preventDefault();

  var id = $(e.currentTarget).data("id");
  var url = t.config.url.delete + "/" + id;

  Swal.fire({
    title: t.config.translations.are_you_delete || "Are you sure?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: url,
        type: "GET",

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
          Swal.close();

          if (res.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: res.msg || "Deleted successfully",
              confirmButtonText: "OK",
            });

            t.dTable.ajax.reload();
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: res.msg || "Delete failed",
              confirmButtonText: "OK",
            });
          }
        },

        error: function () {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Something went wrong",
            confirmButtonText: "OK",
          });
        },
      });
    }
  });
};

LocationApp.prototype.resetForm = function () {
  var t = this;

  t.form[0].reset();
  if (t.validator) {
    t.validator.resetForm();
  }

  t.form.find(".is-invalid").removeClass("is-invalid");
  t.form.find(".text-danger").html("");
  t.form.find(".select-div").removeClass("text-danger");

  t.frmEl.currency.val(null).trigger("change");
  t.frmEl.company_id.val(null).empty().trigger("change");
  t.frmEl.parent_id.val(null).empty().trigger("change");
  t.frmEl.country_id.val(null).empty().trigger("change");
  t.frmEl.state_id.val(null).empty().trigger("change");
  t.frmEl.city_id.val(null).empty().trigger("change");
  t.frmEl.location_user.val(null).empty().trigger("change");
};

LocationApp.prototype.reload = function () {
  $("#user-list-search").val("");

  if (this.dTable) {
    this.dTable.search("").draw();
    this.dTable.ajax.reload(null, true);
  }
};

$(function () {
  new LocationApp(config);
});
