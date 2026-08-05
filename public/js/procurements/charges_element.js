var ChargesElement = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#main-charges-element-wrapper");
  t.table = t.content.find("#chargesTable");
  t.dTable = null;
  t.searchTimer = null;
  t.currentEditId = "";
  t.httpPostPath = "";

  t.btn = {
    add: ".btn-add-charges-element",
    reload: ".btn-reload-list",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    filter: ".btn-filter",
    search: ".btn-searchbox",
  };

  t.modal = $("#charges_element_modal");
  t.modalTitle = t.modal.find("#charges_element_modal_title");
  t.frm = t.modal.find("#charges_element_frm");
  t.loader = t.modal.find("#charges-element-mdl-loader");

  t.el = {
    title: t.frm.find("#title"),
    description: t.frm.find("#description"),
    element_action: t.frm.find("#action"),
    status: t.frm.find("#status"),
  };

  t.filters = {
    wrapper: $("#advanceFilterModal"),
    element_action: null,
    status: null,
  };

  // ✅ Summernote initialize karein
  if (t.el.description.length && $.fn.summernote) {
    t.el.description.summernote({
      toolbar: [
        ["color", ["color"]],
        ["style", ["bold", "italic", "underline", "clear"]],
        ["para", ["ul", "ol"]],
      ],
      height: 200, // ✅ minHeight ki jagah height use karo
      width: "100%", // ✅ Width 100% set karo
      placeholder: t.config.translations.enter_desc || "Enter description",
      dialogsInBody: true, // ✅ Dialogs body mein render ho
      dialogsFade: true, // ✅ Smooth fade effect
    });
  }

  t.init();
};

ChargesElement.prototype.init = function () {
  var t = this;
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.initFormSelect2();
  t.initFilterSelect2();
  t.bindEvents();
  t.initFormValidation();
};

ChargesElement.prototype.bindEvents = function () {
  var t = this;

  t.content.on("click", t.btn.add, $.proxy(t.handleAddClick, t));
  t.content.on("click", t.btn.reload, $.proxy(t.reload, t));
  t.content.on(
    "keyup",
    ".charges-element-list-search",
    $.proxy(t.handleSearchKeyup, t),
  );
  t.content.on("click", t.btn.search, $.proxy(t.handleSearchClick, t));
  t.table.on(
    "click",
    ".btn-edit-charges-element",
    $.proxy(t.handleEditClick, t),
  );
  t.table.on(
    "click",
    ".btn-delete-charges-element",
    $.proxy(t.handleDeleteClick, t),
  );
  t.table.on(
    "click",
    ".btn-clone-charges-element",
    $.proxy(t.handleCloneClick, t),
  );
  t.modal.on("click", "#btnSubmit", $.proxy(t.handleSaveClick, t));

  t.modal.on("hidden.bs.modal", function () {
    t.frm[0].reset();
    t.currentEditId = "";
    t.modal.find(".amg-form-error-wrap").empty();
    t.modal.find("#shows_error").html("");
    t.modal.find("#btnSubmit").removeClass("disabled");

    // ✅ Summernote reset
    if (t.el.description.length && $.fn.summernote) {
      t.el.description.summernote("code", "");
    }
  });

  t.content.on("click", t.btn.openFilter, function () {
    t.filters.wrapper.modal("show");
  });

  t.content.on("click", t.btn.clearFilter, function () {
    if (t.filters.element_action) {
      t.filters.element_action.val(null).trigger("change");
    }
    if (t.filters.status) {
      t.filters.status.val(null).trigger("change");
    }
    t.reload();
  });

  t.content.on("click", t.btn.filter, function () {
    t.reload();
    t.filters.wrapper.modal("hide");
  });
};

ChargesElement.prototype.initDataTable = function () {
  var t = this;
  $.fn.dataTable.ext.pager.numbers_length = 5;

  t.dTable = t.table.DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    stateSave: false,
    searching: true,
    lengthChange: false,
    dom: "rtip",
    scrollX: true,
    scrollCollapse: true,
    autoWidth: false,
    order: [[7, "desc"]],
    pagingType: "simple_numbers",
    ajax: {
      url: t.config.url.ajaxlistCustomCharge,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.search = t.content.find(".charges-element-list-search").val();
        d.element_actions = t.filters.element_action
          ? t.filters.element_action.val()
          : null;
        d.statuses = t.filters.status ? t.filters.status.val() : null;
        d.main_filter = t.config.main_filter;
        d.other_filters = t.config.other_filters;
      },
      error: function (xhr) {
        console.error("DataTable Error:", xhr.responseText);
      },
    },
    columns: [
      {
        data: null,
        orderable: false,
        searchable: false,
        className: "amg-table-action-col",
        render: function (data, type, row) {
          return t.renderActionsCell(row || {}, type);
        },
      },
      { data: "title" },
      {
        data: "description",
        render: function (data, type, row) {
          // Description ko strip tags karke show karo
          if (type === "display") {
            var stripped = data
              ? data.replace(/<[^>]*>/g, "").substring(0, 50)
              : "";
            return stripped.length > 50 ? stripped + "..." : stripped;
          }
          return data;
        },
      },
      {
        data: "action",
        render: function (data, type, row) {
          if (type === "sort" || type === "type") {
            return data;
          }
          if (type === "display" || type === "filter") {
            if (
              data === 1 ||
              data === "1" ||
              data === "Add" ||
              data === "Add Charge"
            ) {
              return '<span class="badge bg-info">Add</span>';
            } else if (
              data === 2 ||
              data === "2" ||
              data === "Subtract" ||
              data === "Subtract Charge"
            ) {
              return '<span class="badge bg-warning">Subtract</span>';
            } else {
              return '<span class="badge bg-secondary">' + data + "</span>";
            }
          }
          return data;
        },
      },
      {
        data: "status",
        render: function (data, type, row) {
          if (type === "sort" || type === "type") {
            if (data === "Enabled" || data === 1 || data === "1") return 1;
            if (data === "Disabled" || data === 0 || data === "0") return 0;
            return -1;
          }
          if (type === "display") {
            if (data === "Enabled" || data === 1 || data === "1") {
              return '<i class="bi bi-toggle-on text-success fs-4" title="Enabled"></i>';
            } else if (data === "Disabled" || data === 0 || data === "0") {
              return '<i class="bi bi-toggle-off text-danger fs-4" title="Disabled"></i>';
            } else {
              return '<span class="badge bg-secondary">' + data + "</span>";
            }
          }
          return data;
        },
      },
      { data: "updated_by_name" },
      { data: "created_at_format" },
      { data: "updated_at_format" },
      {
        data: "id",
        visible: false,
        searchable: false,
      },
    ],
    initComplete: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').tooltip();
    },
  });
};

ChargesElement.prototype.renderActionsCell = function (record, type) {
  if (type !== "display") return "";

  if (!record || !record.id) {
    console.error("Record ID missing:", record);
    return "";
  }

  var actions = "";
  actions += this.actionBtn(
    "Edit",
    "btn-edit-charges-element",
    record.id,
    "bi-pencil",
  );
  actions += this.actionBtn(
    "Delete",
    "btn-delete-charges-element",
    record.id,
    "bi-trash",
  );
  actions += this.actionBtn(
    "Clone",
    "btn-clone-charges-element",
    record.id,
    "bi-copy",
  );

  return '<div class="d-flex gap-2 amg-row-actions">' + actions + "</div>";
};

ChargesElement.prototype.actionBtn = function (label, cls, id, icon) {
  return (
    '<button type="button" class="header-icon-btn-only header-icon-btn-only-sm ' +
    cls +
    '" data-id="' +
    id +
    '" data-bs-toggle="tooltip" title="' +
    label +
    '">' +
    '<i class="bi ' +
    icon +
    '"></i></button>'
  );
};

ChargesElement.prototype.reload = function () {
  if (this.dTable) this.dTable.ajax.reload(null, false);
};

ChargesElement.prototype.handleSearchKeyup = function (e) {
  var t = this;
  if (e.keyCode !== 13 && e.target.value.length !== 0) return;
  clearTimeout(t.searchTimer);
  t.searchTimer = setTimeout(function () {
    t.reload();
  }, 300);
};

ChargesElement.prototype.handleSearchClick = function (e) {
  var t = this;
  var searchVal = t.content.find(".charges-element-list-search").val();
  if (searchVal.length === 0) {
    t.reload();
  }
};

ChargesElement.prototype.handleAddClick = function () {
  var t = this;
  t.frm[0].reset();
  t.modal.find("#shows_error").html("");
  t.modal.find("#btnSubmit").removeClass("disabled");
  t.currentEditId = "";

  t.el.title.val("");

  // ✅ Summernote reset
  if (t.el.description.length && $.fn.summernote) {
    t.el.description.summernote("code", "");
  } else {
    t.el.description.val("");
  }

  t.el.element_action.val("").trigger("change");
  t.el.status.val("").trigger("change");

  t.httpPostPath = t.config.url.create_charges;
  t.modalTitle.text(t.config.translations.Add || "Add Charges Element");
  t.modal
    .find("#btnSubmit")
    .html('<i class="bi bi-check-circle me-1"></i>Create');

  t.toggleModal("#charges_element_modal", true);

  if (t.frmValidator) {
    t.frmValidator.resetForm();
  }
};

ChargesElement.prototype.handleEditClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");

  if (!id) {
    t.showToast("error", "Invalid record ID");
    return;
  }

  t.modalTitle.text(t.config.translations.Edit || "Edit Charges Element");
  t.httpPostPath = t.config.url.update_charges + "/" + id;
  t.modal
    .find("#btnSubmit")
    .html('<i class="bi bi-check-circle me-1"></i>Update');

  t.loader.removeClass("d-none");
  t.modal.find("#btnSubmit").addClass("disabled");

  $.get(t.config.url.edit_charge + "/" + id)
    .done(function (response) {
      if (typeof response !== "object" || response.status !== "success") {
        t.showToast("error", response.msg || "Failed to load data");
        return;
      }

      // ✅ Response structure: response.data.data
      var formData = response.data?.data || response.data || response;

      t.currentEditId = formData.id || id;

      // ✅ Pehle modal show karo
      t.toggleModal("#charges_element_modal", true);

      // ✅ Phir data load karo
      setTimeout(function () {
        t.frm[0].reset();
        t.modal.find("#shows_error").html("");

        if (t.frmValidator) {
          t.frmValidator.resetForm();
        }

        t.loadFormData(formData);
      }, 200);
    })
    .fail(function (xhr) {
      console.error("Edit Error:", xhr);
      t.showToast(
        "error",
        t.config.translations.Something_Went_Wrong || "Something went wrong",
      );
    })
    .always(function () {
      t.loader.addClass("d-none");
      t.modal.find("#btnSubmit").removeClass("disabled");
    });
};

ChargesElement.prototype.handleCloneClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");

  if (!id) {
    t.showToast("error", "Invalid record ID");
    return;
  }

  t.modalTitle.text(t.config.translations.Clone || "Clone Charges Element");
  t.httpPostPath = t.config.url.create_charges;
  t.modal
    .find("#btnSubmit")
    .html('<i class="bi bi-check-circle me-1"></i>Create');
  t.currentEditId = "";

  t.loader.removeClass("d-none");
  t.modal.find("#btnSubmit").addClass("disabled");

  $.get(t.config.url.edit_charge + "/" + id)
    .done(function (response) {
      if (typeof response !== "object" || response.status !== "success") {
        t.showToast("error", response.msg || "Failed to load data");
        return;
      }

      var formData = response.data?.data || response.data || response;

      if (formData.title) {
        formData.title = formData.title;
      }

      // ✅ Pehle modal show karo
      t.toggleModal("#charges_element_modal", true);

      // ✅ Phir data load karo
      setTimeout(function () {
        t.frm[0].reset();
        t.modal.find("#shows_error").html("");

        if (t.frmValidator) {
          t.frmValidator.resetForm();
        }

        t.loadFormData(formData);
      }, 200);
    })
    .fail(function (xhr) {
      t.showToast(
        "error",
        t.config.translations.Something_Went_Wrong || "Something went wrong",
      );
    })
    .always(function () {
      t.loader.addClass("d-none");
      t.modal.find("#btnSubmit").removeClass("disabled");
    });
};

// ✅ SINGLE loadFormData function - Summernote support ke saath
ChargesElement.prototype.loadFormData = function (data) {
  var t = this;
  // Title
  if (data.title !== undefined && data.title !== null) {
    t.el.title.val(data.title);
  }

  // ✅ Description - Summernote support
  if (data.description !== undefined) {
    if (
      t.el.description.length &&
      $.fn.summernote &&
      t.el.description.next(".note-editor").length
    ) {
      t.el.description.summernote("code", data.description || "");
    } else {
      t.el.description.val(data.description || "");
    }
  }

  // Action
  var actionValue =
    data.element_action !== undefined ? data.element_action : data.action;
  if (actionValue !== undefined && actionValue !== null && actionValue !== "") {
    var actionNum = parseInt(actionValue);
    if (!isNaN(actionNum)) {
      t.el.element_action.val(actionNum).trigger("change.select2");
    }
  }

  // Status
  if (data.status !== undefined && data.status !== null && data.status !== "") {
    var statusNum = parseInt(data.status);
    if (!isNaN(statusNum)) {
      t.el.status.val(statusNum).trigger("change.select2");
    }
  }
};

ChargesElement.prototype.handleSaveClick = function (e) {
  var t = this;
  e.preventDefault();

  if (t.frmValidator) {
    if (!t.frmValidator.form()) {
      return false;
    }
  }

  if (!t.el.title.val() || !t.el.title.val().trim()) {
    t.showToast("error", "Title is required");
    return false;
  }
  if (!t.el.element_action.val()) {
    t.showToast("error", "Action is required");
    return false;
  }
  if (t.el.status.val() === "" || t.el.status.val() === null) {
    t.showToast("error", "Status is required");
    return false;
  }

  t.modal.find("#shows_error").text("");
  t.loader.removeClass("d-none");
  t.modal.find("#btnSubmit").addClass("disabled");

  var formData = new FormData(t.frm[0]);
  formData.append("title", t.el.title.val());

  // ✅ Summernote se description lo
  formData.append(
    "description",
    t.el.description.length && $.fn.summernote
      ? t.el.description.summernote("code")
      : t.el.description.val(),
  );

  formData.append("element_action", t.el.element_action.val());
  formData.append("action", t.el.element_action.val());
  formData.append("status", t.el.status.val());

  if (t.currentEditId) {
    formData.append("id", t.currentEditId);
  }

  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    contentType: false,
    processData: false,
    cache: false,
    data: formData,
    headers: {
      "X-CSRF-TOKEN": t.config.token,
    },
  })
    .done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          t.toggleModal("#charges_element_modal", false);
          t.showToast("success", data.msg || "Saved successfully");
          t.currentEditId = "";
          t.reload();
        } else {
          t.showToast("error", data.msg || "Failed to save");
          if (data.errors) {
            $.each(data.errors, function (field, messages) {
              var errorEl = t.frm
                .find('[name="' + field + '"]')
                .closest(".amg-form-field")
                .find(".amg-form-error-wrap");
              if (errorEl.length) {
                errorEl.html(
                  '<span class="text-danger">' + messages[0] + "</span>",
                );
              }
            });
          }
        }
      }
    })
    .fail(function (xhr) {
      console.error("Save Error:", xhr);
      if (xhr.status === 422) {
        var errors = xhr.responseJSON.errors;
        if (errors) {
          $.each(errors, function (field, messages) {
            var errorEl = t.frm
              .find('[name="' + field + '"]')
              .closest(".amg-form-field")
              .find(".amg-form-error-wrap");
            if (errorEl.length) {
              errorEl.html(
                '<span class="text-danger">' + messages[0] + "</span>",
              );
            }
          });
        }
      } else {
        t.showToast(
          "error",
          t.config.translations.Something_Went_Wrong || "Something went wrong",
        );
      }
    })
    .always(function () {
      t.loader.addClass("d-none");
      t.modal.find("#btnSubmit").removeClass("disabled");
    });
};

ChargesElement.prototype.handleDeleteClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");
  var url = t.config.url.charge_delete + "/" + id;

  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: "warning",
      title: t.config.translations.Confirm || "Are you sure?",
      text:
        t.config.translations.Are_You_Delete || "Do you want to delete this?",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
    }).then(function (result) {
      if (result.isConfirmed) {
        t.doDelete(url);
      }
    });
  } else if (confirm(t.config.translations.Are_You_Delete || "Are you sure?")) {
    t.doDelete(url);
  }
};

ChargesElement.prototype.doDelete = function (url) {
  var t = this;

  $.ajax({
    url: url,
    type: "GET",
    data: {
      _token: t.config.token,
      _method: "DELETE",
    },
  })
    .done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          t.showToast("success", data.msg || "Deleted successfully");
          t.reload();
        } else {
          t.showToast("error", data.msg || "Failed to delete");
        }
      }
    })
    .fail(function () {
      t.showToast(
        "error",
        t.config.translations.Something_Went_Wrong || "Something went wrong",
      );
    });
};

ChargesElement.prototype.initFormValidation = function () {
  var t = this;

  if (!$.fn.validate) return;

  t.frmValidator = t.frm.validate({
    onsubmit: false,
    ignore: "",
    rules: {
      title: {
        required: true,
        maxlength: 100,
      },
      status: {
        required: true,
      },
      action: {
        required: true,
      },
    },
    errorPlacement: function (error, element) {
      var errorWrap = element
        .closest(".amg-form-field")
        .find(".amg-form-error-wrap");
      if (errorWrap.length) {
        errorWrap.html('<span class="text-danger">' + error.text() + "</span>");
      } else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      $(element).addClass("is-invalid");
    },
    unhighlight: function (element) {
      $(element).removeClass("is-invalid");
    },
  });
};

ChargesElement.prototype.initFormSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;

  var tr = t.config.translations;
  var s2 = {
    width: "100%",
    dropdownParent: t.modal,
  };

  t.el.element_action.select2(
    $.extend({}, s2, {
      allowClear: true,
      placeholder:
        tr.Select_the_element_action ||
        tr.select_the_element_action ||
        "Select Action",
    }),
  );

  t.el.status.select2(
    $.extend({}, s2, {
      allowClear: true,
      placeholder: tr.Select_the_status || "Select Status",
    }),
  );
};

ChargesElement.prototype.initFilterSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;

  var tr = t.config.translations;
  var s2 = {
    width: "100%",
    dropdownParent: t.filters.wrapper,
  };

  t.filters.element_action = t.filters.wrapper
    .find("#filter_element_action")
    .select2(
      $.extend({}, s2, {
        allowClear: true,
        placeholder:
          tr.Select_the_element_action ||
          tr.select_the_element_action ||
          "Select Action",
      }),
    );

  t.filters.status = t.filters.wrapper.find("#filter_status").select2(
    $.extend({}, s2, {
      allowClear: true,
      placeholder: tr.Select_the_status || "Select Status",
    }),
  );
};

ChargesElement.prototype.toggleModal = function (selector, show) {
  var modal = $(selector);
  if (!modal.length) return;

  if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
    var bsModal = window.bootstrap.Modal.getOrCreateInstance(modal[0]);
    if (show) {
      bsModal.show();
    } else {
      bsModal.hide();
    }
  } else if (typeof modal.modal === "function") {
    modal.modal(show ? "show" : "hide");
  }
};

ChargesElement.prototype.showToast = function (icon, message) {
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: icon,
      text: message,
      showConfirmButton: true,
      allowOutsideClick: false,
      timer: icon === "success" ? 3000 : undefined,
    });
  } else {
    alert(message);
  }
};
