var CategoryApp = function (config) {
  var t = this;

  t.config = config;
  t.table = $("#mytable");
  t.content = $("section.content");
  t.modal = $("#categoryMdl");
  t.form = $("#categoryMdlForm");
  t.show_entries = $(".userModulePageLenth");

  t.dTable = null;
  t.submitUrl = "";

  t.btn = {
    reload: ".btn-reload-list",
    add: ".open-add-modal",
    download: ".btn-download",
  };
  t.init();
};

CategoryApp.prototype.init = function () {
  var t = this;

  t.mapElements();
  t.initValidation();
  t.initSelect2();

  setTimeout(() => t.initDataTable(), 50);

  t.bindEvents();
};

CategoryApp.prototype.mapElements = function () {
  var t = this;
  t.frmEl = {
    name: $("#name"),
    threshold: $("#threshold"),
    category_type: $("#category_type"),
    account_type_id: $("#account_type_id"),
    departments_id: $("#departments_id"),
    fieldset_id: $("#fieldset_id"),
    category_image: $("#category_image"),
    eula_text: $("#eula_text"),
    service_cycle_period: $("#service_cycle_period"),
  };
  t.btn.submit = $("#btnSubmit");
};

CategoryApp.prototype.initValidation = function () {
  var t = this;

  t.validator = t.form.validate({
    rules: {
      name: {
        required: true,
        clean_text_only: true,
        minlength: 2,
        maxlength: 50,
        normalizer: function (value) {
          return $.trim(value);
        },
      },

      category_type: {
        required: true,
      },

      eula_text: {
        clean_text_only:true,
      },

      threshold: {
        digits: true,
        min: 0,
        max: 50
      },

      service_cycle_period: {
          number: true,
      },
      alerts_enabled: {
        maxlength: 1,
      },
      account_type_id: {
          digits: true,
      },
      category_image: {
        extension: "jpg|jpeg|png",
        filesize: 2048,
      },
    },

    messages: {
      name: {
        required: "Category name is required",
        minlength: "Minimum 2 characters required",
        maxlength: "Maximum 50 characters allowed",
      },

      category_type: {
        required: "Please select category type",
      },

      threshold: {
        digits: "Only numbers are allowed",
        min: "Threshold must be at least 0",
        max: "Threshold must not exceed 50",
      },

      category_image: {
        extension: "Only JPG, JPEG, PNG allowed",
        filesize: "File must be less than 2MB",
      },
      alerts_enabled: {
        maxlength: "Invalid value",
      },
      account_type_id: {
        digits: "Please select a valid account type",
      },
      service_cycle_period: {
        number: "Please enter a valid number",
      },
    },

    errorElement: "span",
    errorClass: "text-danger",

    errorPlacement: function (error, element) {
      error.appendTo(element.attr("id") === "eula_text"? element.parent(): element.parent().parent());
    },
    highlight: function (element, errorClass) {
      $(element).closest(".select-div").addClass(errorClass);
    },
    unhighlight: function (element, errorClass) {
      $(element).closest(".select-div").removeClass(errorClass);
    },

    submitHandler: function () {
      // only submit if valid
      t.submitForm();
    },
  });
  $.validator.addMethod("filesize", function (value, element, param) {
    if (element.files.length === 0) return true;
    return element.files[0].size <= param * 1024;
  });
};

CategoryApp.prototype.initSelect2 = function () {
  var t = this;
  function init(el, options = {}) {
    if (el.hasClass("select2-hidden-accessible")) {
      el.select2("destroy");
    }
    el.select2({
      allowClear:true,
      width: "100%",
      dropdownParent: t.modal,
      ...options,
    });
  }

  init(t.frmEl.departments_id, {
    placeholder: config.translations.Select_the_Department,
    ajax: {
      url: t.config.url.departments,
      dataType: "json",
      delay: 300,
      data: (p) => ({
        search: p.term || "",
        page: p.page || 1,
      }),
      processResults: function (data) {
        return { results: data.results };
      },
    },
  });

  init(t.frmEl.category_type,{
    placeholder: t.config.translations.select_type
  })

  init(t.frmEl.fieldset_id, {
    placeholder: t.config.translations.select_the_custom_fieldset,
    allowClear: true,
  });
  init(t.frmEl.account_type_id, {
    placeholder: t.config.translations.select_account_type,
    data: t.config.account_types,
  });

  t.frmEl.account_type_id.val(null).trigger("change");

  t.frmEl.category_type.on("change", function () {
    var id = $(this).find(":selected").data("id");
    t.frmEl.fieldset_id.val(null).empty().trigger("change");

    if (t.frmEl.fieldset_id.hasClass("select2-hidden-accessible")) {
      t.frmEl.fieldset_id.select2("destroy");
    }

    t.frmEl.fieldset_id.select2({
      width: "100%",
      dropdownParent: t.modal,
      // allowClear: true,
      placeholder: t.config.translations.select_the_custom_fieldset,

      ajax: {
        url: t.config.url.getCustomFieldsetByModule + "/" + id,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        processResults: function (data) {
          return {
            results: data.results || [],
          };
        },
      },
    });
  });
};

CategoryApp.prototype.renderText = function (cls) {
  return function (data, type) {
    if (type !== "display") return data;
    return `<span class="${cls}">${data ?? ""}</span>`;
  };
};

CategoryApp.prototype.initDataTable = function () {
  var t = this;
  t.dTable = t.table.DataTable({
    language: datatable_footer_translations(t.config.datatable_translations),
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    lengthChange: false,
    scrollX: true,
    scrollCollapse: true,
    autoWidth: false,
    pageLength: 10,
    order: [[13, "desc"]],
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

    columns: [
      {
        data: "a.id",
        render: t.renderText("b5-text"),
      },
      {
        data: "a.name",
        render: t.renderText("b5-text"),
      },
            {
        data: "a.depart_name",
        render: t.renderText("b5-text"),
      },
      // { data: "a.depart_name" },
      { data: "a.category_type", render: t.renderText("b5-text") },
      { data: "a.account_type", render: t.renderText("b5-text") },
      { data: "a.threshold_count", render: t.renderText("b5-text") },

      {
        data: "a.require_acceptance",
        render: (c) => (c == 1 ? t.CHECK_ICO() : ""),
     
      },
      {
        data: "a.eula",
        render: (c) => (c == 1 ? t.CHECK_ICO() : ""),
      },
      {
        data: "a.checkin_email",
        render: (c) => (c == 1 ? t.CHECK_ICO() : ""),
      },
      {
        data: "a.checkout_email_accept",
        render: (c) => (c == 1 ? t.CHECK_ICO() : ""),
      },

      { data: "a.last_updated_at", render: t.renderText("b5-text") },

      {
        data: "a.image_thumbnail",
        orderable: false,
        render: function (data, type, row) {
          if (type !== "display") return data;

          let name = row.a.name || "Category";
          let url = data ? `${t.config.imgviewpath}/${data}` : "";

          return `
      <div class="d-flex align-items-center justify-content-center">
        ${t.getImageHtml(url, name)}
      </div>
      `;
        },
      },

      {
        data: "a",
        className: "users-col-actions",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return t.buildActions(row.a || {}, type);
        },
      },

      { data: "a.last_updated_at", visible: false },
    ],
    fnInitComplete: function () {
      var api = this.api();

      $("#user-list-search")
        .off("keyup")
        .on("keyup", function (e) {
          if (e.key === "Enter" || e.keyCode === 13) {
          let value = this.value;
            api.search(value).draw();
          }
          });
      // $(".amg-list-searchbar__icon")
      //   .off("click")
      //   .on("click", function () {
      //     api.search($("#user-list-search").val()).draw();
      //   });
    },
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });
};

CategoryApp.prototype.CHECK_ICO = function () {
  return `
  <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
    <path fill-rule="evenodd" clip-rule="evenodd" 
    d="M9 18C10.1819 18 11.3522 17.7672 12.4442 17.3149C13.5361 16.8626 14.5282 16.1997 15.364 15.364C16.1997 14.5282 16.8626 13.5361 17.3149 12.4442C17.7672 11.3522 18 10.1819 18 9C18 7.8181 17.7672 6.64778 17.3149 5.55585C16.8626 4.46392 16.1997 3.47177 15.364 2.63604C14.5282 1.80031 13.5361 1.13738 12.4442 0.685084C11.3522 0.232792 10.1819 0 9 0C6.61305 0 4.32387 0.948211 2.63604 2.63604C0.948212 4.32387 0 6.61305 0 9C0 11.3869 0.948212 13.6761 2.63604 15.364C4.32387 17.0518 6.61305 18 9 18ZM8.768 12.64L13.768 6.64L12.232 5.36L7.932 10.519L5.707 8.293L4.293 9.707L7.293 12.707L8.067 13.481L8.768 12.64Z" 
    fill="#186B43"/>
  </svg>
  `;
};

CategoryApp.prototype.getImageHtml = function (imageUrl, name) {
  if (imageUrl) {
    return `
      <a href="${imageUrl}" target="_blank">
        <img src="${imageUrl}" 
             alt="${name}" 
             class="user-list-avatar">
      </a>
    `;
  }

  let initials = name ? name.substring(0, 2).toUpperCase() : "NA";

  return `<span class="user-list-avatar user-list-avatar-fallback">
            ${initials}
          </span>`;
};
CategoryApp.prototype.getIcon = function (key) {
  return this.icons[key] ? this.icons[key]() : "";
};

CategoryApp.prototype.icons = {
  edit: function () {
    return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg> ';
  },
  delete: function () {
    return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>';
  },
  history: function () {
    return (
      '<svg viewBox="0 0 16 16" fill="none">' +
      '<path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>' +
      '<path d="M3 1.5v3h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
      '<path d="M8 4.5v3l2 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
      "</svg>"
    );
  },
};

CategoryApp.prototype.check = function (val) {
  return val == 1
    ? "<span class='bi bi-check-circle-fill text-success'></span>"
    : "";
};

CategoryApp.prototype.buildActions = function (d) {
  var t = this;

  let actions = [];

  if (t.hasPermission("CategoryEdit")) {
    actions.push(`
      <button class="user-list-action-btn open-edit-modal"
          data-id="${d.id}" data-bs-toggle='tooltip' title='${t.config.translations.edit}'>
          ${t.getIcon("edit")}
      </button>
    `);
  }

  if (t.hasPermission("CategoryDelete")) {
    actions.push(`
      <button class="user-list-action-btn go-del is-delete"
          data-id="${d.id}" data-bs-toggle='tooltip' title="${t.config.translations.delete}">
          ${t.getIcon("delete")}
      </button>
    `);
  }

  actions.push(`
    <a href="${t.config.url.history}/${d.id}" 
       target="_blank"
       class="user-list-action-btn" data-bs-toggle='tooltip'
       title="${t.config.translations.history}">
        ${t.getIcon("history")}
    </a>
  `);

  if (!actions.length) {
    return `<span class="user-list-empty">-</span>`;
  }

  return `
    <div class="user-list-actions d-flex justify-content-center gap-2">
      ${actions.join("")}
    </div>
  `;
};

CategoryApp.prototype.hasPermission = function (name) {
  return this.config.permissions.includes(name);
};

CategoryApp.prototype.bindEvents = function () {
  var t = this;

  t.content.on("click", t.btn.reload, () => t.reload());
  t.content.on("click", t.btn.add, (e) => t.create(e));
  t.content.on("click", ".open-edit-modal", (e) => t.edit(e));
  t.content.on("click", ".go-del", (e) => t.delete(e));
  t.content.on("click", ".open-history-modal", (e) => t.history(e));

  // t.content.on("click", t.btn.download, () => {
  //     window.location = t.config.url.download_url;
  // });

  t.content.on("click", t.btn.download, function () {
    let search = $("#user-list-search").val();
    let obj = {
      search: search,
      other_filters: {},
    };

    let encoded = btoa(JSON.stringify(obj));

    let url = t.config.url.download_url + "?q=" + encoded;

    console.log("Export URL:", url);

    window.location = url;
  });

  t.content.on("click", ".img-expand", function () {
    let $img = $(this);

    if ($img.hasClass("expanded")) {
      $img.removeClass("expanded").css({
        width: "40px",
        height: "40px",
        position: "",
        zIndex: "",
      });
    } else {
      $(".img-expand").removeClass("expanded").css({
        width: "40px",
        height: "40px",
        position: "",
        zIndex: "",
      });

      $img.addClass("expanded").css({
        width: "120px",
        height: "120px",
        position: "relative",
        zIndex: "999",
      });
    }
  });

  t.content.on("click", ".del_attach", function () {
    $(".img_attach-class").attr("src", "").hide();
    $(".img_name-class").text("");
    $(".del_attach").hide();

    $("#delete_img").val(1);
  });
  t.btn.submit.on("click", (e) => t.submit(e));

  t.show_entries.on("change", function () {
    let value = parseInt($(this).val(), 10);
    if (t.dTable) {
      t.dTable.page.len(value).draw();
    }
  });
};

CategoryApp.prototype.create = function (e) {
  var t = this;
  e.preventDefault();

  t.submitUrl = t.config.url.add;

  t.resetForm();
  t.modal.find('.modal-title').text(t.config.translations.add_category_details);
  new bootstrap.Modal(t.modal[0]).show();
};

CategoryApp.prototype.history = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  window.location.href = t.config.url.history + "/" + id;
};

CategoryApp.prototype.edit = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  t.editId = id;
  t.submitUrl = t.config.url.edit + "/" + id;

  $.get(t.config.url.get + "/" + id, function (res) {
    if (res.status === "success") {
      let d = res.data;

      t.resetForm();

      t.frmEl.name.val(d.name);
      t.frmEl.threshold.val(
        d.threshold_count ?? d.threshold ?? d.thresold ?? 0,
      );
      t.frmEl.eula_text.val(d.eula_text || "");
      t.frmEl.service_cycle_period.val(d.service_cycle_period || "");

      if (res.dropdown && res.dropdown.assetDepartments) {
        $.each(res.dropdown.assetDepartments, function (i, v) {
          var option = new Option(v.text, v.id, true, true);
          t.frmEl.departments_id.append(option);
        });
        t.frmEl.departments_id.trigger("change");
      }

      t.frmEl.account_type_id.val(d.account_type_id).trigger("change");

      t.frmEl.category_type.val(d.category_type).trigger("change");

      setTimeout(() => {
        if (d.fieldset_id) {
          let url =
            t.config.url.getCustomFieldsetByModule +
            "/" +
            t.frmEl.category_type.find(":selected").data("id");

          $.get(url, { page: 1 }, function (res) {
            let text = "Selected";

            if (res.results && res.results.length) {
              let found = res.results.find((x) => x.id == d.fieldset_id);
              if (found) {
                text = found.text;
              }
            }
            let option = new Option(text, d.fieldset_id, true, true);

            t.frmEl.fieldset_id.empty().append(option).trigger("change");
          });
        }
      }, 300);

      $("#use_default_eula").prop("checked", d.use_default_eula == 1);
      $("#require_acceptance").prop("checked", d.require_acceptance == 1);
      $("#checkin_email").prop("checked", d.checkin_email == 1);
      $("#checkout_email_accept").prop("checked", d.checkout_email_accept == 1);
      $("#alerts_enabled").prop("checked", d.alerts_enabled == 1);

      if (d.image_thumbnail) {
        let url = `${t.config.imgviewpath}/${d.image_thumbnail}`;

        $(".img_attach-class").attr("src", url).show();
        $(".img_name-class").text(d.image_thumbnail);
        $(".del_attach").show();
      } else {
        $(".img_attach-class").attr("src", "").hide();
        $(".img_name-class").text("");
        $(".del_attach").hide();
      }
      t.modal.find('.modal-title').text(t.config.translations.edit_category_details);
      new bootstrap.Modal(t.modal[0]).show();
    }
  });
};
CategoryApp.prototype.submit = function (e) {
  var t = this;
  e.preventDefault();
  if (!t.form.valid()) {
    return false;
  }

  var formData = new FormData(t.form[0]);
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
        t.dTable.ajax.reload();
        Swal.fire("Success", res.msg, "success");
      } else {
        Swal.fire("Error", res.msg, "error");
      }
    },
    complete: function () {
      t.btn.submit.prop("disabled", false);
    },
  });
};

CategoryApp.prototype.delete = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");
  let url = t.config.url.delete + "/" + id;

  Swal.fire({
    title: t.config.translations.are_you_delete,
    icon: "warning",
    showCancelButton: true,
  }).then((r) => {
    if (r.isConfirmed) {
      $.get(url, (res) =>{ 
        if (res.status === "success") {
            sweetAlert("center", "success", res);
            setTimeout(() => {
                t.dTable.ajax.reload();
            }, 800);
        } else {
            sweetAlert("center", "error", res);
        }
       
      });
    }
  });
};

CategoryApp.prototype.resetForm = function () {
  var t = this;

  t.form[0].reset();
  if (t.validator) {
    t.validator.resetForm();
  }

  t.frmEl.departments_id.val(null).empty().trigger("change");
  t.frmEl.fieldset_id.val(null).trigger("change");
  t.frmEl.account_type_id.val(null).trigger("change");

  $(".img_attach-class").attr("src", "").hide();
  $(".img_name-class").text("");
  $(".del_attach").hide();

  $("#delete_img").val(0);
};

CategoryApp.prototype.reload = function () {
  var t = this;


  if (t.dTable) {
    t.dTable.ajax.reload(null, true);
  }
};

$(function () {
  new CategoryApp(config);
});
