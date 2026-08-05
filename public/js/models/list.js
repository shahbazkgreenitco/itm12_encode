var ModelApp = function (config) {
  var t = this;
  t.config = config;
  t.table = $("#mytable");
  t.content = $("section.content");
  t.modal = $("#modelMdl");
  t.form = $("#modelMdlForm");
  t.dTable = null;
  t.submitUrl = "";
  t.editId = null;
  t.showDeleted = 0;
  t.btn = {
    reload: ".btn-reload-list",
    download: ".btn-models-export",
    showDeleted: ".btn-show-models",
    add: ".open-add-modal",
    submit: $("#btnSubmit"),
  };
  t.init();
};

ModelApp.prototype.init = function () {
  var t = this;
  t.initValidation();
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.bindEvents();
};

ModelApp.prototype.initValidation = function () {
  var t = this;

  t.validator = t.form.validate({
    rules: {
      name: {
        required: true,
        clean_text_only:true,
        minlength: 2,
        maxlength: 50,
        normalizer: function (value) {
          return $.trim(value);
        },
      },

      manufacturer_id: {
        required: true,
      },

      category_id: {
        required: true,
      },

      depreciation_id: {
        required: false,
      },

      modelno: {
        required: false,
        minlength: 2,
        maxlength: 50,
        clean_text_only:true,
      },

      eol: {
        required: false,
        min:0,
        digits: true
      },

      attachment: {
        extension: "jpg|jpeg|png",
        filesize: 2048,
      },
    },

    messages: {
      name: {
            required: t.config.translations.model_name_required,
            minlength: t.config.translations.minimum_2_characters_required,
            maxlength: t.config.translations.maximum_50_characters_allowed,
      },

      manufacturer_id: {
            required: t.config.translations.please_select_manufacturer,
      },

      category_id: {
            required: t.config.translations.please_select_category,
      },

      depreciation_id: {
            required: t.config.translations.please_select_depreciation,
      },

      modelno: {
            required: t.config.translations.model_number_required,
            minlength: t.config.translations.minimum_2_characters_required,
            maxlength: t.config.translations.maximum_50_characters_allowed,
      },

      eol: {
            digits: t.config.translations.only_numbers_allowed,
      },

      attachment: {
            extension: t.config.translations.only_jpg_jpeg_png_allowed,
            filesize: t.config.translations.file_must_be_less_than_2mb,
      },
    },

    errorElement: "span",
    errorClass: "text-danger",

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
      // only submit if valid
      t.submitForm();
    },
  });

  $.validator.addMethod("filesize", function (value, element, param) {
    if (element.files.length === 0) return true;
    return element.files[0].size <= param * 1024;
  });
};

ModelApp.prototype.initDataTable = function () {
  var t = this;
  let defaultLength = parseInt($(".user-list-page-length").val()) || 10;
  $(".user-list-page-length").val(defaultLength);
  t.dTable = t.table.DataTable({
    language: datatable_footer_translations(t.config.datatable_translations),
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    stateSave: false,
    scrollX: true,
    scrollCollapse: true,
    deferRender: true,
    searchDelay: 500,
    pageLength: defaultLength,
    lengthMenu: [10, 25, 50, 100],
    lengthChange: false,

    dom: "lrtip",

    ajax: {
      url: t.config.url.list,
      type: "GET",
      data: function (d) {
        d._token = t.config.token;
        d.deletedRecords = t.showDeleted ? "true" : "false";
      },
    },

    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },

    columns: [
      { data: "a.id" },
      { data: "a.mnu_name" },
      { data: "a.model_name" },
      { data: "a.model_no" },
      { data: "a.tot" },
      { data: "a.dep_name" },
      { data: "a.cat_name" },
      { data: "a.eol" },

      {
        data: "a.image_thumbnail",
        orderable: false,
        render: function (img) {
          if (!img) return "";

          let url = `${t.config.imgviewpath}/${img}`;

          return `<img src="${url}" width="40" height="40" style="border-radius:6px;">`;
        },
      },

      {
        data: "a",
        orderable: false,
        render: (d) => t.buildActions(d),
      },
    ],
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });
};

ModelApp.prototype.buildActions = function (d) {
  var t = this;

  let actions = [];

  if (!d.deleted_at) {
    // EDIT
    if (t.hasPermission("ModelEdit")) {
      actions.push(`
        <button class="user-list-action-btn open-edit" 
            data-id="${d.id}" data-bs-toggle="tooltip" title="${t.config.translations.edit}">

         <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>

        </button>
      `);
    }

    // DELETE
    if (t.hasPermission("ModelDelete")) {
      actions.push(`
        <button class="user-list-action-btn go-del" 
            data-id="${d.id}" data-bs-toggle="tooltip" title=" ${t.config.translations.delete}">

         <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>

        </button>
      `);
    }
  } else {
    // RESTORE
    if (t.hasPermission("ModelRestore")) {
      actions.push(`
        <button class="user-list-action-btn text-success go-restore" 
            data-id="${d.id}" data-bs-toggle="tooltip" title=" ${t.config.translations.restore}">

          <i class="bi bi-arrow-clockwise"></i>

        </button>
      `);
    }
  }

  // HISTORY (always show)
  actions.push(`
    <a href="${t.config.url.info}/${d.id}" 
       target="_blank" 
       class="user-list-action-btn" data-bs-toggle="tooltip"
       title=" ${t.config.translations.history}">

      <i class="bi bi-clock-history"></i>

    </a>
  `);

  if (!actions.length) {
    return `<span class="text-muted">-</span>`;
  }

  return `
    <div class="user-list-actions d-flex justify-content-center gap-2">
      ${actions.join("")}
    </div>
  `;
};

ModelApp.prototype.hasPermission = function (name) {
  return this.config.permissions.includes(name);
};

ModelApp.prototype.bindEvents = function () {
  var t = this;

  t.content.on("click", t.btn.reload, () => t.reload());

  t.content.on("click", t.btn.add, (e) => t.create(e));

  t.content.on("click", ".open-edit", (e) => t.edit(e));
  t.content.on("click", ".go-restore", (e) => t.restore(e));

  t.content.on("click", ".go-del", (e) => t.delete(e));

  t.content.on("click", ".del_attach", function () {
    var id = t.editId;

    if (!id) {
      Swal.fire("Error", "Invalid ID", "error");
      return;
    }

    var url = t.config.url.deleteImage + "/" + id;

    Swal.fire({
      title: "Delete image?",
      icon: "warning",
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: "GET",
          data: {
            _token: t.config.token,
          },

          success: function (res) {
            if (res.status === "success") {
              $(".img_attach-class").attr("src", "").hide();
              $(".img_name-class").text("");
              $(".del_attach").hide();

              Swal.fire("Deleted!", res.msg, "success");
            } else {
              Swal.fire("Error", res.msg, "error");
            }
          },

          error: function () {
            Swal.fire("Error", "Request failed", "error");
          },
        });
      }
    });
  });
  t.content.on("change", "#category_id", function () {
    let fieldset = $("#fieldset_id");

    fieldset.val(null).trigger("change");

    if (fieldset.hasClass("select2-hidden-accessible")) {
      fieldset.select2("destroy");
    }

    t.initFieldset();
  });

  t.btn.submit.on("click", (e) => t.submit(e));

  t.content.on("click", t.btn.showDeleted, function () {
    t.showDeleted = t.showDeleted ? 0 : 1;

    $(this).toggleClass("active");

    let title = t.showDeleted
      ? t.config.translations.show_present_model
      : t.config.translations.show_deleted_models;
    $(this).attr("data-bs-original-title", title);

    t.dTable.ajax.reload();
  });

  t.content.on("click", t.btn.download, function () {
    let obj = {
      search: t.dTable.search(),
      deletedRecords: t.showDeleted ? "true" : "false",
      other_filters: {},
    };
    let encoded = btoa(JSON.stringify(obj));
    let url = t.config.url.download_url + "?q=" + encoded;
    window.location = url;
  });

  $(".search-icon-button").on("click", function () {
    t.dTable.search($("#user-list-search").val()).draw();
  });


  $(".user-list-search").on("keyup", function (e) {
    if(e.keyCode == 13){
        t.dTable.search(this.value).draw();
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
  t.content.on("click", ".img-expand", function () {
    let $img = $(this);

    if ($img.hasClass("expanded")) {
      $img.removeClass("expanded").css({ width: "40px", height: "40px" });
    } else {
      $(".img-expand")
        .removeClass("expanded")
        .css({ width: "40px", height: "40px" });

      $img.addClass("expanded").css({
        width: "120px",
        height: "120px",
        zIndex: "999",
      });
    }
  });
};

//create
ModelApp.prototype.create = function (e) {
  var t = this;
  e.preventDefault();

  t.submitUrl = t.config.url.add;
  t.editId = null;

  t.resetForm();

  t.loadFormData();

  $("#modelMdlTitle").text(t.config.translations.add_model);

  new bootstrap.Modal(t.modal[0]).show();
};

ModelApp.prototype.loadFormData = function (callback) {
  var t = this;

  $.get(t.config.url.formData, function (res) {
    let m = $("#manufacturer_id");
    m.html(
      `<option value="">${t.config.translations.select_mannufacturer}</option>`,
    );
    $.each(res.manufacturers, (i, v) => {
      m.append(`<option value="${v.id}">${v.name}</option>`);
    });

    let c = $("#category_id");
    c.html(
      `<option value="">${t.config.translations.select_category}</option>`,
    );
    $.each(res.categories, (i, v) => {
      c.append(`<option value="${v.id}">${v.name}</option>`);
    });

    let d = $("#depreciation_id");
    d.html(
      `<option value="">${t.config.translations.select_depreciation}</option>`,
    );
    $.each(res.depreciations, (i, v) => {
      d.append(`<option value="${v.id}">${v.name}</option>`);
    });

    t.initSelect2();

    if (callback) callback();
  });
};

ModelApp.prototype.edit = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  t.editId = id;
  t.submitUrl = t.config.url.update + "/" + id;

  t.resetForm();
  t.loadFormData();

  $("#modelMdlTitle").text(t.config.translations.edit);

  $.get(t.config.url.get + "/" + id, function (res) {
    if (res.status === "success") {
      let d = res.data;

      $("#name").val(d.name);
      $("#modelno").val(d.modelno);
      $("#eol").val(d.eol);

      t.loadFormData(function () {
        let m = $("#manufacturer_id");
        let mText =
          m.find(`option[value="${d.manufacturer_id}"]`).text() || "Selected";
        m.append(new Option(mText, d.manufacturer_id, true, true)).trigger(
          "change",
        );

        let c = $("#category_id");
        let cText =
          c.find(`option[value="${d.category_id}"]`).text() || "Selected";
        c.append(new Option(cText, d.category_id, true, true)).trigger(
          "change",
        );

        let dep = $("#depreciation_id");
        let dText =
          dep.find(`option[value="${d.depreciation_id}"]`).text() || "Selected";
        dep
          .append(new Option(dText, d.depreciation_id, true, true))
          .trigger("change");

        t.initFieldset();

        if (d.fieldset_id) {
          let catId = $("#category_id").val();
          let url = t.config.url.getCustomFieldsetByModule + "/" + 1;

          $.get(url, { page: 1 }, function (res) {
            let text = d.fieldset_name || "Selected";

            if (!d.fieldset_name && res.results) {
              let found = res.results.find((x) => x.id == d.fieldset_id);
              if (found) text = found.text;
            }

            let option = new Option(text, d.fieldset_id, true, true);
            $("#fieldset_id").append(option).trigger("change");
          });
        }
      });

      // image
      if (d.image_thumbnail) {
        let url = t.config.imgviewpath + "/" + d.image_thumbnail;

        $(".img_attach-class").attr("src", url).show();
        $(".img_name-class").text(d.image_thumbnail);
        $(".del_attach").show();
      }

      new bootstrap.Modal(t.modal[0]).show();
    }
  });
};

//submit
ModelApp.prototype.submit = function (e) {
  var t = this;
  e.preventDefault();
  if (!t.form.valid()) {
    return false;
  }
  var formData = new FormData(t.form[0]);

  if (!$("#fieldset_id").val()) {
    formData.delete("fieldset_id");
  }

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

        Swal.fire(t.config.translations.success, res.msg, "success");
      } else {
        Swal.fire(t.config.translations.error, res.msg, "error");
      }
    },

    complete: function () {
      t.btn.submit.prop("disabled", false);
    },
  });
};
//delete
ModelApp.prototype.delete = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  Swal.fire({
    title: t.config.translations.are_you_delete,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: t.config.translations.ok,
    cancelButtonText: t.config.translations.cancel,
  }).then((r) => {
    if (r.isConfirmed) {
      $.get(t.config.url.delete + "/" + id, (res) => {
        if (res.status === "success") {
          sweetAlert("center", "success", res);
        } else {
          sweetAlert("center", "error", res);
        }
        setTimeout(() => {
        t.dTable.ajax.reload();
        }, 800);
      });
    }
  });
};
//reset
ModelApp.prototype.resetForm = function () {
  var t = this;

  t.form[0].reset();

  if (t.validator) {
    t.validator.resetForm();
  }

  t.form.find(".is-invalid").removeClass("is-invalid");
  t.form.find(".text-danger").html("");

  t.form.find(".amg-form-error-wrap").html("");

  t.form.find("select").val(null).trigger("change");

  $(".img_attach-class").attr("src", "").hide();
  $(".img_name-class").text("");
  $(".del_attach").hide();

  $("#delete_img").val(0);
};

//reload
ModelApp.prototype.reload = function () {
  var t = this;


  if (t.dTable) {
    t.dTable.ajax.reload(null, false);
  }
};
//restore
ModelApp.prototype.restore = function (e) {
  var t = this;

  let id = $(e.currentTarget).data("id");

  Swal.fire({
    title: t.config.translations.restore,
    text: t.config.translations.restore_desc,
    icon: "question",
    showCancelButton: true,
    confirmButtonText: t.config.translations.yes_restore,
  }).then((r) => {
    if (r.isConfirmed) {
      $.get(t.config.url.restore + "/" + id, function (res) {
        if (res.status === "success") {
          Swal.fire(t.config.translations.success, res.msg, "success");
          t.dTable.ajax.reload();
        } else {
          Swal.fire(t.config.translations.error, res.msg, "error");
        }
      });
    }
  });
};

//fieldset
ModelApp.prototype.initFieldset = function () {
  var t = this;

  let el = $("#fieldset_id");

  if (el.hasClass("select2-hidden-accessible")) {
    el.select2("destroy");
  }
  el.empty();
  el.select2({
    width: "100%",
    dropdownParent: t.modal.find(".modal-content"),
    allowClear: true,
    placeholder: "Select Fieldset",
    placeholder: t.config.translations.select_fieldset,
    ajax: {
      url: function () {
        let catId = $("#category_id").val() || 0;
        return t.config.url.getCustomFieldsetByModule + "/" + 1;
      },
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
};

ModelApp.prototype.initSelect2 = function () {
  var t = this;

  function init(el, placeholder) {
    if (el.hasClass("select2-hidden-accessible")) {
      el.select2("destroy");
    }

    el.select2({
      width: "100%",
      dropdownParent: t.modal.find(".modal-content"),
      // allowClear: true,
      placeholder: placeholder,
    });
  }

  init($("#manufacturer_id"), t.config.translations.select_mannufacturer);
  init($("#category_id"), t.config.translations.select_category);
  init($("#depreciation_id"), t.config.translations.select_depreciation);
};

$(function () {
  new ModelApp(config);
});
