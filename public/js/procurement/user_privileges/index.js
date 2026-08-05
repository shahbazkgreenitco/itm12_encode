var PrivilegesList = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#main-privileges-wrapper");
  t.table = t.content.find("#privileges_table");
  t.dTable = null;
  t.searchTimer = null;

  t.btn = {
    reload: ".btn-reload-list",
    update: ".btn-update-privileges",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    filter: ".btn-filter"
  };

  t.filters = {
    wrapper: $("#advanceFilterModal"),
    userPrivilege: null
  };

  t.init();
};

/* ============ init ============ */
PrivilegesList.prototype.init = function () {
  var t = this;
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.initFilterSelect2();
  t.bindEvents();
};

/* ============ events ============ */
PrivilegesList.prototype.bindEvents = function () {
  var t = this;
  t.content.on("click", t.btn.reload, $.proxy(t.reload, t));
  t.content.on("click", t.btn.update, $.proxy(t.handleUpdateClick, t));
  t.content.on("click", t.btn.openFilter, $.proxy(t.handleOpenFilterClick, t));
  t.filters.wrapper.on("click", t.btn.filter, $.proxy(t.handleFilterClick, t));
  t.filters.wrapper.on("click", t.btn.clearFilter, $.proxy(t.handleClearFilterClick, t));
  t.content.on("keyup", ".privileges-list-search", $.proxy(t.handleSearchKeyup, t));
};

/* ============ DataTable — dom:"rtip" so the custom searchbar isn't duplicated ============ */
PrivilegesList.prototype.initDataTable = function () {
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
    order: [[0, "asc"]],
    pagingType: "simple_numbers",
    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.search = t.content.find(".privileges-list-search").val();
        d.user_privilege = t.filters.userPrivilege ? t.filters.userPrivilege.val() : null;
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
          return t.renderCheckboxCell(row.a || {}, type);
        }
      },
      { data: "a.full_name" },
      { data: "a.username" },
      { data: "a.department" },
      { data: "a.email" },
      { data: "a.procurement_team" },
      { data: "a.procurement_user" }
    ]
  });
};

PrivilegesList.prototype.renderCheckboxCell = function (record, type) {
  if (type !== "display") return "";
  var checked = (record.procurement_user == 1 || record.procurement_team == 1) ? 'checked' : '';
  return '<div class="d-flex align-items-center"><input type="checkbox" class="privileges-table-checkbox" data-id="' + record.id + '" ' + checked + '></div>';
};

/* ============ list controls ============ */
PrivilegesList.prototype.reload = function () {
  if (this.dTable) this.dTable.ajax.reload(null, false);
};

PrivilegesList.prototype.handleSearchKeyup = function (e) {
  var t = this;
  if (e.keyCode !== 13 && e.target.value.length !== 0) return;
  clearTimeout(t.searchTimer);
  t.searchTimer = setTimeout(function () {
    t.reload();
  }, 300);
};

/* ============ filters ============ */
PrivilegesList.prototype.handleOpenFilterClick = function () {
  this.toggleModal("#advanceFilterModal", true);
};

PrivilegesList.prototype.handleFilterClick = function () {
  var t = this;
  t.updateFilterCount();
  t.toggleModal("#advanceFilterModal", false);
  t.reload();
};

PrivilegesList.prototype.handleClearFilterClick = function () {
  var t = this;
  if (t.filters.userPrivilege) t.filters.userPrivilege.val("").trigger("change");
  t.updateFilterCount();
  t.reload();
};

PrivilegesList.prototype.updateFilterCount = function () {
  var count = Object.keys(this.cacheFilterValues()).length;
  this.content.find(".filter-count-badge").text(count).toggleClass("d-none", count === 0);
};

PrivilegesList.prototype.cacheFilterValues = function () {
  var t = this;
  var out = {};
  var userPrivilegeVal = t.filters.userPrivilege ? t.filters.userPrivilege.val() : null;
  if (userPrivilegeVal && userPrivilegeVal !== "null") out.user_privilege = userPrivilegeVal;
  return out;
};

PrivilegesList.prototype.initFilterSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;
  var tr = t.config.translations;
  var s2 = { width: "100%", dropdownParent: t.filters.wrapper };

  t.filters.userPrivilege = t.filters.wrapper.find("#filter_user_privilege").select2($.extend({}, s2, {
    allowClear: true,
    placeholder: tr.Privileges_For_Selected_User
  }));
};

/* ============ Update ============ */
PrivilegesList.prototype.handleUpdateClick = function (e) {
  var t = this;
  var selectedIds = [];
  t.content.find(".privileges-table-checkbox:checked").each(function () {
    selectedIds.push($(this).data("id"));
  });

  if (selectedIds.length === 0) {
    t.showToast("warning", "Please select at least one user.");
    return;
  }

  var formData = new FormData();
  formData.append("_token", t.config.token);
  formData.append("user_privilege", selectedIds.join(","));
  formData.append("user_privilege_type", t.filters.userPrivilege ? t.filters.userPrivilege.val() : null);

  $.ajax({
    url: t.config.url.update,
    type: "POST",
    contentType: false,
    processData: false,
    cache: false,
    data: formData
  }).done(function (data) {
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

/* ============ shared helpers ============ */
PrivilegesList.prototype.toggleModal = function (selector, show) {
  var modal = $(selector);
  if (!modal.length) return;
  if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
    window.bootstrap.Modal.getOrCreateInstance(modal[0])[show ? "show" : "hide"]();
  } else if (typeof modal.modal === "function") {
    modal.modal(show ? "show" : "hide");
  }
};

PrivilegesList.prototype.showToast = function (icon, message) {
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({ icon: icon, text: message, showConfirmButton: true, allowOutsideClick: false });
  } else {
    alert(message);
  }
};
