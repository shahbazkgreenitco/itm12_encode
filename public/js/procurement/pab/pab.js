var PABList = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#main-pab-list-wrapper");
  t.table = t.content.find("#pab_table");
  t.dTable = null;
  t.searchTimer = null;

  t.btn = {
    add: ".btn-add-pab",
    reload: ".btn-reload-list",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    filter: ".btn-filter"
  };

  t.filters = {
    wrapper: $("#advanceFilterModal"),
    approver: null
  };

  t.init();
};

PABList.prototype.init = function () {
  var t = this;
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.initFilterSelect2();
  t.bindEvents();
};

PABList.prototype.bindEvents = function () {
  var t = this;
  t.content.on("click", t.btn.add, $.proxy(t.handleAddClick, t));
  t.content.on("click", t.btn.reload, $.proxy(t.reload, t));
  t.content.on("keyup", ".pab-list-search", $.proxy(t.handleSearchKeyup, t));
  t.filters.wrapper.on("click", t.btn.filter, $.proxy(t.handleFilterClick, t));
  t.filters.wrapper.on("click", t.btn.clearFilter, $.proxy(t.handleClearFilterClick, t));
  t.table.on("click", ".btn-edit-pab", $.proxy(t.handleEditClick, t));
  t.table.on("click", ".btn-delete-pab", $.proxy(t.handleDeleteClick, t));
};

PABList.prototype.initDataTable = function () {
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
    order: [[5, "desc"]],
    pagingType: "simple_numbers",
    ajax: {
      url: t.config.url.getlist,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.search = t.content.find(".pab-list-search").val();
        d.filter_by_approver = t.filters.approver ? t.filters.approver.val() : null;
      },
      error: function (xhr) {
        console.error("DataTable Error:", xhr.responseText);
      }
    },
    columns: [
      {
        data: "a",
        orderable: false,
        searchable: false,
        className: "amg-table-action-col",
        render: function (data, type, row) {
          return t.renderActionsCell(row.a || {}, type);
        }
      },
      { data: "a.name" },
      { data: "a.description" },
      { data: "a.hierarchy_approval" },
      { data: "a.member_count" },
      { data: "a.create_at_format" },
      { data: "a.update_at_format" }
    ]
  });
};

PABList.prototype.renderActionsCell = function (record, type) {
  if (type !== "display") return "";
  var actions = "";
  actions += this.actionBtn("Edit", "btn-edit-pab", record.id, "bi-pencil");
  actions += this.actionBtn("Delete", "btn-delete-pab", record.id, "bi-trash");
  return '<div class="d-flex gap-2 amg-row-actions">' + actions + "</div>";
};

PABList.prototype.actionBtn = function (label, cls, id, icon) {
  return (
    '<button type="button" class="header-icon-btn-only header-icon-btn-only-sm ' + cls +
    '" data-id="' + id + '" data-bs-toggle="tooltip" title="' + label + '">' +
    '<i class="bi ' + icon + '"></i></button>'
  );
};

PABList.prototype.reload = function () {
  if (this.dTable) this.dTable.ajax.reload(null, false);
};

PABList.prototype.handleSearchKeyup = function (e) {
  var t = this;
  if (e.keyCode !== 13 && e.target.value.length !== 0) return;
  clearTimeout(t.searchTimer);
  t.searchTimer = setTimeout(function () {
    t.reload();
  }, 300);
};

PABList.prototype.handleFilterClick = function () {
  var t = this;
  t.updateFilterCount();
  t.toggleModal("#advanceFilterModal", false);
  t.reload();
};

PABList.prototype.handleClearFilterClick = function () {
  var t = this;
  if (t.filters.approver) t.filters.approver.val("").trigger("change");
  t.updateFilterCount();
  t.reload();
};

PABList.prototype.updateFilterCount = function () {
  var count = Object.keys(this.cacheFilterValues()).length;
  this.content.find(".filter-count-badge").text(count).toggleClass("d-none", count === 0);
};

PABList.prototype.cacheFilterValues = function () {
  var t = this;
  var out = {};
  var approverVal = t.filters.approver ? t.filters.approver.val() : null;
  if (approverVal && approverVal !== "null") out.filter_by_approver = approverVal;
  return out;
};

PABList.prototype.initFilterSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;
  var tr = t.config.translations;
  var s2 = { width: "100%", dropdownParent: t.filters.wrapper };

  t.filters.approver = t.filters.wrapper.find("#filter_by_approver").select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.manage_users,
      dataType: "json",
      method: "get",
      data: function (p) { return { search: p.term, page: p.page || 1 }; }
    },
    allowClear: true,
    placeholder: tr.Select_Department
  }));
};

PABList.prototype.handleAddClick = function () {
  var t = this;
  t.toggleModal("#addPABModal", true);
};

PABList.prototype.handleEditClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");
  $.get(t.config.url.get + "/" + id).done(function (data) {
    if (typeof data !== "object") return;
    if (data.status !== "success") {
      t.showToast("error", data.msg);
      return;
    }
    var d = data.data;
    t.currentEditId = d.id;
    t.el.pab_name.val(d.pab_name);
    t.el.description.val(d.description);
    t.el.approval_mode.val(d.approval_mode);
    t.el.total_members.val(d.total_members);
    t.toggleModal("#addPABModal", true);
  }).fail(function () {
    t.showToast("error", t.config.translations.something_went_wrong);
  });
};

PABList.prototype.handleDeleteClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");
  var url = t.config.url.delete + "/" + id;

  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: "warning",
      text: t.config.translations.are_you_delete,
      showCancelButton: true,
      confirmButtonText: "Yes",
      cancelButtonText: "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) t.doDelete(url);
    });
  } else if (confirm(t.config.translations.are_you_delete)) {
    t.doDelete(url);
  }
};

PABList.prototype.doDelete = function (url) {
  var t = this;
  $.get(url).done(function (data) {
    if (typeof data === "object") {
      if (data.status === "success") {
        t.showToast("success", data.msg);
        t.reload();
      } else {
        t.showToast("error", data.msg);
      }
    }
  }).fail(function () {
    t.showToast("error", t.config.translations.something_went_wrong);
  });
};

PABList.prototype.toggleModal = function (selector, show) {
  var modal = $(selector);
  if (!modal.length) return;
  if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
    window.bootstrap.Modal.getOrCreateInstance(modal[0])[show ? "show" : "hide"]();
  } else if (typeof modal.modal === "function") {
    modal.modal(show ? "show" : "hide");
  }
};

PABList.prototype.showToast = function (icon, message) {
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({ icon: icon, text: message, showConfirmButton: true, allowOutsideClick: false });
  } else {
    alert(message);
  }
};
