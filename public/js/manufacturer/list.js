var ManufacturerApp = function (config) {
  var t = this;
  t.config = config;
  t.table = $("#mytable");
  t.content = $(document);
  t.modal = $("#manufacturemd1");
  t.form = $("#Manufactureform");
  t.dTable = null;
  t.isEdit = false;
  t.submitUrl = "";
  t.btn = {
    reload: ".btn-reload-list",
    add: ".open-add-modal",
    download: ".btn-download",
  };
  t.init();
};

ManufacturerApp.prototype.init = function () {
  var t = this;
  t.mapElements();
  t.initValidation();
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.bindEvents();
};

ManufacturerApp.prototype.mapElements = function () {
  var t = this;
  t.frmEl = {
    name: $("#name"),
    attachment: $("#attachment"),
  };
  t.btn.submit = $("#btnSubmit");
};

ManufacturerApp.prototype.initValidation = function () {
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
      attachment: {
        extension: "jpg|jpeg|png",
        filesize: 2048,
      },
    },
    messages: {
      name: {
        minlength: t.config.translations.minlength_validation,
        maxlength: t.config.translations.maxlength_validation,
      },
      attachment: {
        extension: t.config.translations.extension_validation,
        filesize: t.config.translations.filesize_validation,
      },
    },

    errorElement: "span",
    errorClass: "text-error",

    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
      let group = element.closest(".input-group");

      if (group.length) {
        error.insertAfter(group);
      } else {
        error.insertAfter(element);
      }
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

  $.validator.addMethod("filesize", function (value, element, param) {
    if (element.files.length === 0) return true;
    return element.files[0].size <= param * 1024;
  });
};

ManufacturerApp.prototype.initDataTable = function () {
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
    responsive: true,
    autoWidth: false,
    stateSave: false,
    deferRender: true,
    searchDelay: 500,
    pageLength: defaultLength,
    lengthMenu: [10, 25, 50, 100],
    lengthChange: false,
    language: {
        paginate: {
            previous: t.config.translations.previous || "Previous",
            next: t.config.translations.next || "Next"
        },
        info: t.config.translations.showing_entries || "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: t.config.translations.no_entries || "Showing 0 to 0 of 0 entries",
        infoFiltered: t.config.translations.filtered_from || "(filtered from _MAX_ total entries)",
        zeroRecords: t.config.translations.no_matching_records || "No matching records found",
        emptyTable: t.config.translations.no_data || "No data available in table",
        search: t.config.translations.search || "Search:",
        lengthMenu: t.config.translations.length_menu || "Show _MENU_ entries"
    },
    dom: "lrtip",
    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
      },
    },
    columns: [
      {
        data: 'a.id',
      },
      {
        data: "a.name",
        render: function (data, type, row) {
          if (type !== "display") return data;

          return `<span>${data ?? "-"}</span>`;
        },
      },
      {
        data: "a.attachment",
        render: function (data, type, row) {
          if (type !== "display") return data;

          let name = row.a.name || t.config.translations.manufacturer;
          let url = data ? t.config.imgviewpath + '/' + data : "";

          return `
            <div class="d-flex align-items-center">
              ${t.getImageHtml(url, name)}
            </div>
          `;
        },
      },
      {
        data: "a",
        orderable: false,
        render: function (d) {
          return t.buildActions(d);
        },
      },
    ],
    drawCallback: function (settings) {
        $(this).closest('.dataTables_wrapper')
            .find('.dataTables_info')
            .css({
                'float': 'left',
                'margin-top': '15px'
            });

        t.table.find('[data-bs-toggle="tooltip"]').each(function () {
            var existing = bootstrap.Tooltip.getInstance(this);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(this);
        });

    }
  });
};

ManufacturerApp.prototype.getImageHtml = function (imageUrl, name) {
  if (imageUrl) {
    return `
      <a href="${imageUrl}" target="_blank">
        <img src="${imageUrl}" 
             alt="${name}" 
             class="user-list-avatar">
      </a>
    `;
  }

  let initials = name ? name.substring(0, 1).toUpperCase() : "NA";
  return `<span class="user-list-avatar user-list-avatar-fallback">
            ${initials}
          </span>`;
};

ManufacturerApp.prototype.getIcon = function (key) {
  return this.icons[key] ? this.icons[key]() : "";
};

ManufacturerApp.prototype.icons = {
  edit: function () {
    return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg> ';
  },
  delete: function () {
    return ' <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg> ';
  },
};

ManufacturerApp.prototype.buildActions = function (d) {
  var t = this;

  let actions = [];

  if (t.hasPermission("ManufactureEdit")) {
    actions.push(`
      <button class="user-list-action-btn open-edit-modal"
          data-id="${d.id}" data-bs-toggle="tooltip" data-bs-original-title="${t.config.translations.edit}">
          ${t.getIcon("edit")}
      </button>
    `);
  }

  if (t.hasPermission("ManufactureDelete")) {
    actions.push(`
      <button class="user-list-action-btn go-del is-delete"
          data-id="${d.id}" data-bs-toggle="tooltip" data-bs-original-title="${t.config.translations.delete}">
          ${t.getIcon("delete")}
      </button>
    `);
  }

  if (!actions.length) {
    return `<span class="user-list-empty">-</span>`;
  }

  return `
    <div class="gap-2">
        ${actions.join("")}
    </div>
  `;
};

ManufacturerApp.prototype.hasPermission = function (name) {
  return this.config.permissions.includes(name);
};

ManufacturerApp.prototype.bindEvents = function () {
  var t = this;
  t.content.on("click", t.btn.reload, () => t.reload());
  t.content.on("click", t.btn.add, (e) => t.create(e));
  t.content.on("click", ".open-edit-modal", (e) => t.edit(e));
  t.content.on("click", ".go-del", (e) => t.delete(e));

  t.content.on("click", t.btn.download, function () {
    let search = t.dTable ? t.dTable.search() : "";
    let pageLength = t.dTable ? t.dTable.page.len() : 10;

    let obj = {
      search: search,
      page_length: pageLength,
      other_filters: {},
    };

    let encoded = btoa(JSON.stringify(obj));
    let url = t.config.url.download_url + "?q=" + encoded;
    window.location = url;
  });

  let timer;

  $(".user-list-search").on("input", function () {
    clearTimeout(timer);

    timer = setTimeout(() => {
      if (t.dTable) {
        t.dTable.search(this.value).draw();
      }
    }, 400);
  });

  $(document)
    .off("change", ".user-list-page-length")
    .on("change", ".user-list-page-length", function () {
      let val = parseInt($(this).val());

      if (val && t.dTable) {
        t.dTable.page.len(val).draw();
      }
    });

  t.btn.submit.on("click", (e) => t.submit(e));
  t.content.on("click", ".del_attach", function () {
    var id = t.editId;
    if (!id) {
      Swal.fire("Error", "Invalid ID", t.config.translations.error);
      return;
    }
    var url = t.config.url.del_attach + "/" + id;
    Swal.fire({
      title: t.config.translations.delete_image_title,
      text: t.config.translations.delete_image_text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: t.config.translations.confirm_delete,
      cancelButtonText: t.config.translations.cancel,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: "POST",
          data: {
            _token: t.config.token,
          },
          success: function (res) {
            if (res.status === "success") {
              $(".attach_image").addClass("d-none");
              $(".img_attach-class").attr("src", "");
              $(".img_name-class").text("");

              Swal.fire("Deleted!", res.msg, "success");
            } else {
              Swal.fire("Error", res.msg, t.config.translations.error);
            }
          },
          error: function () {
            Swal.fire("Error", t.config.translations.request_failed, t.config.translations.error);
          },
        });
      }
    });
  });
};

ManufacturerApp.prototype.create = function (e) {
  var t = this;
  e.preventDefault();
  t.isEdit = false;
  t.submitUrl = t.config.url.add;
  t.resetForm();
  t.modal.find(".modal-title").text(t.config.translations.create);
  new bootstrap.Modal(t.modal[0]).show();
};

ManufacturerApp.prototype.edit = function (e) {
  var t = this;
  e.preventDefault();
  var id = $(e.currentTarget).data("id");
  t.editId = id;
  t.submitUrl = t.config.url.edit + "/" + id;
  $.get(t.config.url.get + "/" + id)
    .done(function (res) {
      if (res.status === "success") {
        var d = res.data;
        t.isEdit = true;
        t.resetForm();
        t.frmEl.name.val(d.name);
        if (d.attachment) {
          $(".attach_image").removeClass("d-none");
          let url = t.config.imgviewpath + d.attachment;
          $(".img_attach-class").attr("src", url);
          $(".img_name-class").text(d.attachment);
        }
        t.modal.find(".modal-title").text(t.config.translations.edit);
        new bootstrap.Modal(t.modal[0]).show();
      } else {
        Swal.fire({
          icon: "error",
          title: t.config.translations.error,
          text: res.msg || t.config.translations.record_deleted,
        });
        t.dTable.ajax.reload();
      }
    })
    .fail(function () {
      Swal.fire({
        icon: "error",
        title: t.config.translations.error,
        text: t.config.translations.something_went_wrong_refresh,
      });
    });
};
ManufacturerApp.prototype.submit = function (e) {
  var t = this;
  e.preventDefault();
  if (!t.form.valid()) {
    return false;
  }
  var formData = new FormData(t.form[0]);
  formData.append("_token", t.config.token);
  $("#icon").removeClass("d-none");
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
        Swal.fire({
          icon: "success",
          title: t.config.translations.success,
          text: res.msg || t.config.translations.saved_successfully,
        });
      } else {
        Swal.fire({
          icon: "error",
          title: t.config.translations.error,
          text: res.msg || t.config.translations.something_went_wrong,
        });
      }
    },
    error: function () {
      Swal.fire({
        icon: "error",
        title: t.config.translations.error,
        text: t.config.translations.something_went_wrong,
      });
    },
    complete: function () {
      $("#icon").addClass("d-none");
      t.btn.submit.prop("disabled", false);
    },
  });
};

ManufacturerApp.prototype.delete = function (e) {
  var t = this;
  e.preventDefault();
  var id = $(e.currentTarget).data("id");
  var url = t.config.url.delete + "/" + id;
  Swal.fire({
    title: t.config.translations.confirm_delete,
    icon: "warning",
    showCancelButton: true,
  }).then((result) => {
    if (result.isConfirmed) {
      $.get(url)
        .done(function (res) {
          if (res.status === "success") {
            t.dTable.ajax.reload();
            Swal.fire({
              icon: "success",
              title: t.config.translations.success,
              text: res.msg || t.config.translations.deleted_successfully,
            });
          } else {
            Swal.fire({
              icon: "error",
              title: t.config.translations.error,
              text: res.msg || t.config.translations.something_went_wrong,
            });
          }
        })
        .fail(function () {
          Swal.fire({
            icon: "error",
            title: t.config.translations.error,
            text: t.config.translations.something_went_wrong,
          });
        });
    }
  });
};

ManufacturerApp.prototype.resetForm = function () {
  var t = this;
  t.form[0].reset();
  if (t.validator) {
    t.validator.resetForm();
  }
  t.form.find(".is-invalid").removeClass("is-invalid");
  t.form.find(".text-error").html("");
  t.form.find(".select-div").removeClass("text-error");
  $(".attach_image").addClass("d-none");
  $(".img_attach-class").attr("src", "");
  $(".img_name-class").text("");
};

ManufacturerApp.prototype.reload = function () {
  var t = this;
  $("#tableSearch").val("");
  if (t.dTable) {
    // t.dTable.search("");
    localStorage.removeItem("DataTables_" + t.table.attr("id"));
    t.dTable.ajax.reload(null, true);
  }
};

$(function () {
  new ManufacturerApp(config);
});
