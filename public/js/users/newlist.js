var MyApp = function (config) {
  var t = this;

  t.config = config;
  t.showDeletedUsers = false;
  t.httpCall = true;
  t.currentEditingUserId = null;
  t.internal_places = [];
  t.httpPostPath = "";

  t.config.search = t.config.search || "";
  t.config.other_filters = t.config.other_filters || {};
  t.config.export_filters = t.config.export_filters || "";
  t.config.dashboard_filters = t.config.dashboard_filters || {};

  t.content = $("#main-user-list-wrapper");
  t.table = t.content.find("#default_order");
  t.usertable = t.table;
  t.dTable = null;
  t.searchTimer = null;
  t.actionMenuHideTimer = null;

  t.btn = {
    add: ".btn-add-user",
    showusers: ".btn-show-users",
    nonDeleted: ".non-deleted-icon",
    search: ".btn-search",
    export: ".btn-users-export",
    export_pdf: ".btn-users-export-pdf",
    reload: ".btn-reload-list",
    filter: ".btn-filter",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    import: ".btn-users-import",
    update: ".btn-users-update",
    users_log: ".btn-users-log",
    bulk_notify: ".btn-users-notification",
    user_sync_azure: ".btn-user-sync-azure",
    bulk_activate: ".btna-active",
    bulk_deactivate: ".btnd-inactive",
    bulk_delete: ".btn-bulk-delete",
    merge: ".btn-user-merge",
    print_label: ".btn-print-label",
    search_icon: ".search-icon"
  };

  t.filters = {
    wrapper: $("#advanceFilterModal"),
    company: null,
    job_type: null,
    status: null,
    department: null,
    location: null,
    role: null,
    manager: null,
    service_ticket: null,
    search: null,
    fun: {},
  };

  t.mdlNotify = null;
  t.frmNotify = null;
  t.frmNotifyValidator = null;
  t.btnNotify = {};
  t.selectedUserIds = [];
  t.selectedIds = {};
  t.isBulkAddMode = false;
  t.mergeObj = null;
  t.print = null;
  t.userMdl = { initialized: false, selectsInitialized: false };
  t.listUi = {
    pageLength: $(),
    searchInput: $(),
    viewButtons: $(),
    listPanel: $(),
    cardPanel: $(),
    activeView: "list"
  };

  t.init();
};

MyApp.prototype.init = function () {
  var t = this;
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.initListControls();
  t.initFilters();
  t.initMerge();
  t.initNotifyModal();
  t.initPrintModal();
  t.updateFilterCount();
  t.bindEvents();
};

MyApp.prototype.bindEvents = function () {
  var t = this;
  t.content.on("click", t.btn.reload, $.proxy(t.handleReloadClick, t));
  // t.content.on("click", t.btn.filter, $.proxy(t.handleFilterClick, t));
  t.filters.wrapper.on("click", t.btn.filter, $.proxy(t.handleFilterClick, t));
  t.content.on("click", t.btn.openFilter, $.proxy(t.handleOpenFilterClick, t));
  // t.content.on("click", t.btn.clearFilter, $.proxy(t.handleClearFilterClick, t));
  t.filters.wrapper.on(
    "click",
    t.btn.clearFilter,
    $.proxy(t.handleClearFilterClick, t),
  );
  t.content.on("click", t.btn.export, $.proxy(t.handleExportClick, t));
  t.content.on("click", t.btn.export_pdf, $.proxy(t.handleExportPdfClick, t));
  t.content.on("click", t.btn.import, $.proxy(t.handleImportClick, t));
  t.content.on("click", t.btn.update, $.proxy(t.handleUpdateClick, t));
  t.content.on("click", t.btn.showusers, $.proxy(t.handleShowUsersClick, t));
  t.content.on("click", t.btn.add, $.proxy(t.handleAddClick, t));
  t.content.on("click", t.btn.users_log, $.proxy(t.handleUsersLogClick, t));
  t.content.on("click", t.btn.bulk_notify, $.proxy(t.handleBulkNotifyClick, t));
  t.content.on(
    "click",
    t.btn.user_sync_azure,
    $.proxy(t.handleUserSyncAzureClick, t),
  );
  t.content.on("click", "#togglePassword", $.proxy(t.togglePasswordVisibility, t));
  t.content.on(
    "click",
    t.btn.bulk_activate,
    $.proxy(t.handleBulkActivateClick, t),
  );
  t.content.on(
    "click",
    t.btn.bulk_deactivate,
    $.proxy(t.handleBulkDeactivateClick, t),
  );
  t.content.on("click", t.btn.bulk_delete, $.proxy(t.handleBulkDeleteClick, t));
  t.content.on("click", t.btn.merge, $.proxy(t.handleMergeClick, t));
  t.content.on("click", t.btn.print_label, $.proxy(t.handlePrintLabelClick, t));
  t.content.on("click", t.btn.search, $.proxy(t.handleSearchClick, t));
  t.content.on("click", t.btn.search_icon, $.proxy(t.handleListSearchInput, t));
  t.content.on(
    "change",
    ".user-list-page-length",
    $.proxy(t.handlePageLengthChange, t),
  );

  // searching on enter or when input is empty
  t.content.on('keyup', '.user-list-search', function (e) {
    if (e.which === 13 || !this.value.trim()) {
      $.proxy(t.handleListSearchInput, t)(e);
    }
  });
  t.content.on(
    "click",
    ".js-user-view-toggle",
    $.proxy(t.handleViewModeClick, t),
  );
  t.content.on(
    "mouseenter",
    ".user-list-actions .dropdown",
    $.proxy(t.handleActionMenuMouseEnter, t),
  );
  t.content.on(
    "mouseleave",
    ".user-list-actions .dropdown",
    $.proxy(t.handleActionMenuMouseLeave, t),
  );
  t.content.on(
    "focusin",
    ".user-list-actions .dropdown",
    $.proxy(t.handleActionMenuFocusIn, t),
  );
  t.content.on(
    "focusout",
    ".user-list-actions .dropdown",
    $.proxy(t.handleActionMenuFocusOut, t),
  );
  t.content.on(
    "click",
    ".user-list-menu-toggle",
    $.proxy(t.handleActionMenuToggleClick, t),
  );
  t.content.on(
    "click",
    ".user-list-actions .action-dropdown-menu .dropdown-item",
    $.proxy(t.handleActionMenuItemClick, t),
  );
  $(window)
    .off("resize.userListTable")
    .on("resize.userListTable", $.proxy(t.scheduleTableLayoutSync, t));

  t.content.on(
    "click",
    "#printModal #btnSubmit",
    $.proxy(t.handlePrintSubmitClick, t),
  );
  t.content.on("click", "#printModal #btnClear", function (e) {
    if (e) e.preventDefault();
    t.toggleModal("#printModal", false);
  });

  t.content.on("click", ".dtActEdit", $.proxy(t.handleEditClick, t));
  t.content.on("click", ".dtActClone", $.proxy(t.handleCloneClick, t));
  t.content.on("click", ".dtActDel", $.proxy(t.handleDeleteClick, t));
  t.content.on("click", ".dtActRestore", $.proxy(t.handleRestoreClick, t));
  t.content.on("click", ".dtActView", $.proxy(t.handleViewClick, t));
  t.content.on(
    "click",
    ".dtActSendCredential",
    $.proxy(t.handleSendCredentialClick, t),
  );
  t.content.on("click", ".add_to_merge", $.proxy(t.handleAddToMergeClick, t));
  t.content.on("click", ".dtActNotify", $.proxy(t.handleNotifyClick, t));
  t.content.on("click", ".read-more-company", $.proxy(t.handleAccessibleCompanyClick, t));
};

MyApp.prototype.handleReloadClick = function () {
  this.reload();
};
MyApp.prototype.handleFilterClick = function () {
  this.applyFilters();
};
MyApp.prototype.handleOpenFilterClick = function (e) {
  if (e) e.preventDefault();
  this.openFilterModal();
};
MyApp.prototype.handleClearFilterClick = function () {
  this.clearFilters();
};
MyApp.prototype.handleExportClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(".btn-users-export");
  this.exportList();
};
MyApp.prototype.handleExportPdfClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(".btn-users-export-pdf");
  this.exportListPdf();
};
MyApp.prototype.handleImportClick = function (e) {
  if (e) e.preventDefault();
  this.importUsers();
};
MyApp.prototype.handleUpdateClick = function (e) {
  if (e) e.preventDefault();
  this.updateUsers();
};
MyApp.prototype.handleShowUsersClick = function (e) {
  if (e) e.preventDefault();
  this.toggleDeletedUsers();
};
MyApp.prototype.handleAddClick = function (e) {
  if (e) e.preventDefault();
  this.addUser();
};
MyApp.prototype.handleUsersLogClick = function (e) {
  if (e) e.preventDefault();
  this.openUsersLog();
};
MyApp.prototype.handleBulkNotifyClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(".btn-users-notification");
  this.addBulkUsersForAnnouncement(e);
};
MyApp.prototype.handleUserSyncAzureClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(".btn-user-sync-azure");
  this.userSyncAzure();
};
MyApp.prototype.handleBulkActivateClick = function (e) {
  if (e) e.preventDefault();
  this.bulkUserActivate();
};
MyApp.prototype.handleBulkDeactivateClick = function (e) {
  if (e) e.preventDefault();
  this.bulkUserDeactivate();
};
MyApp.prototype.handleBulkDeleteClick = function (e) {
  if (e) e.preventDefault();
  this.bulkUserDelete();
};
MyApp.prototype.handleMergeClick = function (e) {
  if (e) e.preventDefault();
  this.openMergeModal();
};
MyApp.prototype.handlePrintLabelClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(".btn-print-label");
  this.printLabelModal();
};
MyApp.prototype.handleSearchClick = function (e) {
  if (e) e.preventDefault();
  this.focusListSearch();
};
MyApp.prototype.handlePageLengthChange = function (e) {
  if (e) e.preventDefault();

  var length = this.getSelectedPageLength();
  if (!this.dTable || !length) return;

  $("#select-all").prop("checked", false);
  this.dTable.page.len(length).draw(false);
};
MyApp.prototype.handleListSearchInput = function () {
  var t = this;

  window.clearTimeout(t.searchTimer);
  t.searchTimer = window.setTimeout(function () {
    t.cacheFilterValues();
    t.reload();
  }, 250);
};
MyApp.prototype.handleViewModeClick = function (e) {
  var btn;
  var view;

  if (e) e.preventDefault();

  btn = $(e.currentTarget);
  view = btn.attr("data-view") || "list";
  this.setListViewMode(view);
};
MyApp.prototype.togglePasswordVisibility = function (e) {
  e.preventDefault();
  let password = $('#password');
  let eyeIcon = $('#eyeIcon');

  if (password.attr('type') === 'password') {
    password.attr('type', 'text');
    eyeIcon.html(`
            <path d="M2 12C2 12 5.636 5 12 5C18.364 5 22 12 22 12C22 12 18.364 19 12 19C5.636 19 2 12 2 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
        `);
  } else {
    password.attr('type', 'password');
    eyeIcon.html(`
            <path d="M17.94 17.94C16.18 19.24 14.15 20 12 20C5 20 1 12 1 12C2.24 9.69 3.93 7.73 5.94 6.06M9.9 4.24C10.58 4.08 11.29 4 12 4C19 4 23 12 23 12C22.39 13.14 21.66 14.21 20.82 15.18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1L23 23"stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M9.88 9.88C9.57 10.19 9.4 10.59 9.4 11C9.4 12.1 10.3 13 11.4 13C11.81 13 12.21 12.83 12.52 12.52" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        `);
  }
};
MyApp.prototype.handleActionMenuMouseEnter = function (e) { this.showActionMenu($(e.currentTarget)); };
MyApp.prototype.handleActionMenuMouseLeave = function (e) { this.scheduleActionMenuHide($(e.currentTarget)); };
MyApp.prototype.handleActionMenuFocusIn = function (e) { this.showActionMenu($(e.currentTarget)); };
MyApp.prototype.handleActionMenuFocusOut = function (e) {
  var t = this;
  var dropdown = $(e.currentTarget);

  window.setTimeout(function () {
    if (!dropdown.length || dropdown.find(document.activeElement).length)
      return;
    t.scheduleActionMenuHide(dropdown);
  }, 0);
};
MyApp.prototype.handleActionMenuToggleClick = function (e) {
  if (e) e.preventDefault();
  if (e && e.currentTarget && typeof e.currentTarget.blur === "function")
    e.currentTarget.blur();
};
MyApp.prototype.handleActionMenuItemClick = function (e) {
  this.hideActionMenu($(e.currentTarget).closest(".dropdown"));
};
MyApp.prototype.handlePrintSubmitClick = function (e) {
  if (e) e.preventDefault();
  this.submitPrintLabel();
};

MyApp.prototype.handleEditClick = function (e) {
  if (e) e.preventDefault();
  this.editUser($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleCloneClick = function (e) {
  if (e) e.preventDefault();
  this.cloneUser($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleDeleteClick = function (e) {
  if (e) e.preventDefault();
  this.deleteUser($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleRestoreClick = function (e) {
  if (e) e.preventDefault();
  this.restoreUser($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleViewClick = function (e) {
  if (e) e.preventDefault();
  this.viewUser($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleSendCredentialClick = function (e) {
  if (e) e.preventDefault();
  this.sendCredential($(e.currentTarget).attr("data-id"));
};
MyApp.prototype.handleAddToMergeClick = function (e) {
  if (e) e.preventDefault();
  this.addToMerge(e);
};
MyApp.prototype.handleNotifyClick = function (e) {
  if (e) e.preventDefault();
  this.getUserForAnnouncement(e);
};
MyApp.prototype.openMergeModal = function () {
  this.hideTooltip(".btn-user-merge");
  if (!this.mergeObj) return;

  if (typeof this.mergeObj.show === "function") {
    this.mergeObj.show();
    return;
  }

  if (this.mergeObj.mdl && this.mergeObj.mdl.length) {
    this.toggleModal("#mergeMdl", true);
  }
};

MyApp.prototype.initListControls = function () {
  var t = this;
  t.listUi.pageLength = t.content.find(".user-list-page-length");
  t.listUi.searchInput = t.content.find(".user-list-search");
  t.listUi.viewButtons = t.content.find(".js-user-view-toggle");
  t.listUi.listPanel = t.content.find(".js-user-list-view-panel");
  t.listUi.cardPanel = t.content.find(".js-user-card-view-panel");
  t.syncListPageLength();
  t.syncListSearch();
  t.setListViewMode(t.listUi.activeView);
};

MyApp.prototype.getSelectedPageLength = function () {
  var select =
    this.listUi && this.listUi.pageLength
      ? this.listUi.pageLength
      : this.content.find(".user-list-page-length");
  var length = parseInt(select.val(), 10);

  return length > 0 ? length : 10;
};

MyApp.prototype.syncListPageLength = function () {
  if (!this.listUi || !this.listUi.pageLength.length) return;

  this.listUi.pageLength.val(String(this.getSelectedPageLength()));
};

MyApp.prototype.syncListSearch = function () {
  if (!this.listUi || !this.listUi.searchInput.length) return;

  this.listUi.searchInput.val(this.config.search || "");
};

MyApp.prototype.setListViewMode = function (view) {
  var isCardView = view === "card";

  this.listUi.activeView = isCardView ? "card" : "list";

  if (this.listUi.viewButtons.length) {
    this.listUi.viewButtons.each(function () {
      var btn = $(this);
      var isActive = btn.attr("data-view") === (isCardView ? "card" : "list");

      btn.toggleClass("is-active", isActive);
      btn.attr("aria-pressed", isActive ? "true" : "false");
    });
  }

  if (this.listUi.listPanel.length) {
    this.listUi.listPanel.toggleClass("d-none", isCardView);
  }

  if (this.listUi.cardPanel.length) {
    this.listUi.cardPanel.toggleClass("d-none", !isCardView);
  }

  if (!isCardView) {
    this.scheduleTableLayoutSync();
  }
};

MyApp.prototype.initDataTable = function () {
  var t = this;
  var defaultPageLength = t.getSelectedPageLength();

  $.fn.dataTable.ext.pager.numbers_length = 5;

  t.dTable = t.usertable.DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    stateSave: false,
    searchDelay: 600,
    pageLength: defaultPageLength,
    lengthChange: false,
    scrollX: true,
    scrollCollapse: true,
    autoWidth: false,
    ordering: true,
    order: [[9, "desc"]],
    pagingType: "simple_numbers",
    ajax: {
      url: t.config.url.list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.showDeletedUsers = t.showDeletedUsers;
        d.filters = t.config.other_filters || {};
        d.main_filter = t.config.main_filter;
        d.role = t.config.role;
        d.location = t.config.locations;
        d.department = t.config.departments;
        d.user_staff = t.config.user_staff;
        if (d.order && d.order.length > 0) {
          let orderInfo = d.order[0];
          let columnIndex = orderInfo.column;
          let direction = orderInfo.dir;
          let columnName = d.columns[columnIndex].data;
          d.sorted_column_name = columnName;
          d.sorted_direction = direction;
        }
      },
      error: function (xhr) {
        console.error("DataTable Error:", xhr.responseText);
      },
    },
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },
    columns: [
      {
        data: "a.id",
        className: "amg-table-checkbox-col",
        orderable: false,
        createdCell: function (td) {
          $(td).css({ "z-index": "9" });
        },

        searchable: false,
        render: function (data, type) {
          return t.renderRowCheckbox(data, type);
        },
      },
      {
        data: "a.full_name",
        render: function (data, type, row) {
          return t.renderUserInfoCell(row.a || {}, type);
        },
      },
      {
        data: "a.company",
        render: function (data, type, row) {
          return t.renderCompanyCell(row.a || {}, type);
        },
      },
      {
        data: "a.job_type",
        render: function (data, type, row) {
          return t.renderJobTypeCell(row.a || {}, type);
        },
      },
      {
        data: "a.email",
        render: function (data, type, row) {
          return t.renderContactCell(row.a || {}, type);
        },
      },
      {
        data: "a.manager_name",
        render: function (data, type, row) {
          return t.renderManagerCell(row.a || {}, type);
        },
      },
      {
        data: "a.location_name",
        render: function (data, type, row) {
          return t.renderLocationCell(row.a || {}, type);
        },
      },
      {
        data: "a.user_status",
        render: function (data, type, row) {
          return t.renderStatusCell(row.a || {}, type);
        },
      },
      {
        data: "a.company_list",
        render: function (data, type, row) {
          return t.renderAccessibleCompanyCell(row.a || {}, type);
        },
      },
      {
        data: "a.last_updated_at",
        render: function (data, type, row) {
          return t.renderUpdatedOnCell(row.a || {}, type);
        },
      },
      {
        data: "a.id",
        className: "amg-col-actions",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return t.renderActionsCell(row.a || {}, type);
        },
      },
    ],
    initComplete: function () {
      t.syncListPageLength();
      t.syncListSearch();
      t.setListViewMode(t.listUi.activeView || "list");
      t.scheduleTableLayoutSync();
    },
    drawCallback: function () {
      $("#select-all").prop("checked", false);
      t.bindCheckboxEvents();
      t.scheduleTableLayoutSync();
      t.toolTip($(this));
    },
  });
};

MyApp.prototype.toolTip = function (e) {
  e.find('[data-bs-toggle="tooltip"]').each(function () {
    var existing = bootstrap.Tooltip.getInstance(this);
    if (existing) existing.dispose();
  });

  e.find('[data-bs-toggle="tooltip"]').each(function () {
    new bootstrap.Tooltip(this);
  });
}

MyApp.prototype.renderRowCheckbox = function (data, type) {
  if (type !== "display") return data;
  var checked = this.selectedIds[data] ? "checked" : "";

  return [
    '<label class="d-inline-flex align-items-center justify-content-center">',
    '<input type="checkbox" class="row-checkbox form-check-input" value="', this.escapeHtml(data), '" ', checked, '>',
    '</label>'
  ].join("");
};


MyApp.prototype.truncateText = function (text, maxLength) {
  if (!text) return "";
  return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
};

MyApp.prototype.renderUserInfoCell = function (record, type) {
  var name = this.safeDisplayValue(record.full_name, "-");
  if (type !== "display") return name;

  var detailUrl = (this.config.url && this.config.url.user_info && this.isFilledValue(record.id))
    ? this.config.url.user_info + "/" + record.id
    : "";
  var username = this.safeDisplayValue(record.username, "");
  var imageUrl = this.safeDisplayValue(record.profile_img || record.gravatar, "");
  var avatarHtml = this.getAvatarHtml(name, imageUrl, "user-list-avatar");

  var displayName = this.truncateText(name, 15);
  var displayUsername = this.truncateText(username, 15);

  var titleHtml = detailUrl
    ? '<a class="b5-text fw-medium" data-bs-toggle="tooltip" title="' + this.escapeHtml(name) + '" data-bs-original-title="' + this.escapeHtml(name) + '" aria-label="' + this.config.translations.name + '" href="' + this.escapeHtml(detailUrl) + '" target="_blank" rel="noopener noreferrer">' + this.escapeHtml(displayName) + '</a>'
    : '<span class="b5-text fw-medium" data-bs-toggle="tooltip" title="' + this.escapeHtml(name) + '" data-bs-original-title="' + this.escapeHtml(name) + '" aria-label="' + this.config.translations.name + '">' + this.escapeHtml(displayName) + '</span>';

  return [
    '<div class="d-flex align-items-start gap-2 w-100">',
    avatarHtml,
    '<div class="d-flex flex-column w-100 min-w-0">',
    titleHtml,
    username
      ? '<span class="fw-normal b5-text" style="line-height:13px;color:#7F7F7F" data-bs-toggle="tooltip" title="' + this.escapeHtml(username) + '" data-bs-original-title="' + this.escapeHtml(username) + '" aria-label="' + this.config.translations.username + '" >' +
      this.escapeHtml(displayUsername) +
      "</span>"
      : "",
    "</div>",
    "</div>",
  ].join("");
};

MyApp.prototype.renderCompanyCell = function (record, type) {
  var company = this.safeDisplayValue(record.company, "-");
  if (type !== "display") return company;

  return (
    '<div class="b5-text" style="color:#515151" title="' +
    this.escapeHtml(company) +
    '">' +
    this.escapeHtml(company) +
    "</div>"
  );
};

MyApp.prototype.renderJobTypeCell = function (record, type) {
  var jobType = this.safeDisplayValue(record.job_type, "-");
  var roleName = record.roleDetails
    ? this.safeDisplayValue(record.roleDetails.name, "")
    : "";

  if (type !== "display") {
    return roleName ? jobType + " " + roleName : jobType;
  }

  return [
    '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
    '<span class="b5-text fw-normal" ' +
    'data-bs-toggle="tooltip" ' +
    'title="' + this.config.translations.user_type + '" ' +
    'data-bs-original-title="' + this.config.translations.user_type + '" ' +
    'aria-label="' + this.config.translations.user_type + '">' +
    this.escapeHtml(jobType) +
    '</span>',
    this.renderRolePill(roleName),
    '</div>'
  ].join("");
};

MyApp.prototype.renderAccessibleCompanyCell = function (record, type) {
  var companyList = record.company_list || "";
  if (type !== "display") {
    return companyList;
  }
  if (!companyList) {
    return "-";
  }
  var companies = companyList.split(',');
  var visibleCompanies = companies.slice(0, 2);
  var html = '<div class="d-flex flex-wrap gap-1">';
  visibleCompanies.forEach(function (company) {
    html += '<span class="badge bg-dark text-white px-2 py-1 fw-normal b5-text">' +
      $('<div>').text(company.trim()).html() +
      '</span>';
  });
  if (companies.length > 2) {
    html += '<a href="javascript:void(0)" ' +
      'class="read-more-company ms-1" ' +
      'data-companies="' + $('<div>').text(companyList).html() + '">' +
      '+' + (companies.length - 2) + 'more'
    '</a>';
  }
  html += '</div>';
  return html;
};

MyApp.prototype.handleAccessibleCompanyClick = function (e) {

  if (e) {
    e.preventDefault();
  }

  var companies = $(e.currentTarget).data('companies').split(',');
  var html = '<div class="d-flex flex-wrap gap-1">';

  companies.forEach(function (company) {
    html += '<span class="badge bg-dark text-white px-2 py-1">' +
      $('<div>').text(company.trim()).html() +
      '</span>';
  });

  html += '</div>';

  $('#companyListModalBody').html(html);
  $('#companyListModal').modal('show');
};

MyApp.prototype.renderContactCell = function (record, type) {
  var country_code_values = [record.phone_country_code, record.phone2_country_code, record.work_phone_country_code];
  var email = this.safeDisplayValue(record.email, "");
  var phoneData = this.getPrimaryPhone(record);
  var phone = phoneData ? phoneData[0] : "";
  var phoneIndex = phoneData ? phoneData[1] : -1;
  var countryCode = "";
  if (phoneIndex >= 0 && country_code_values[phoneIndex]) {
    countryCode = String(country_code_values[phoneIndex]).trim();
    if (!countryCode.startsWith('+')) {
      countryCode = '+' + countryCode;
    }
    countryCode = '(' + countryCode + ') ';
  }
  var formattedPhone = phone ? countryCode + phone : "";

  if (type !== "display") {
    return $.trim([email, formattedPhone].join(" "));
  }

  if (!email && !formattedPhone) {
    return '<span class="user-list-empty">-</span>';
  }

  var primaryLine = email || formattedPhone;
  var secondaryLine = email && formattedPhone ? formattedPhone : "";

  return [
    '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
    '<span  class="b5-text fw-normal" data-bs-toggle="tooltip" title="' + this.config.translations.email + '" data-bs-original-title="' + this.config.translations.email + '" aria-label="' + this.config.translations.email + '" title="' + this.escapeHtml(primaryLine) + '">' + this.escapeHtml(primaryLine) + '</span>',
    secondaryLine ? '<span class="fw-normal b5-text opacity-50" data-bs-toggle="tooltip" title="' + this.config.translations.phone + '" data-bs-original-title="' + this.config.translations.phone + '" aria-label="' + this.config.translations.phone + '" title="' + this.escapeHtml(secondaryLine) + '">' + this.escapeHtml(secondaryLine) + '</span>' : "",
    '</div>'
  ].join("");
};
MyApp.prototype.renderManagerCell = function (record, type) {
  var managerName = this.safeDisplayValue(record.manager_name, "");
  if (type !== "display") return managerName || "-";
  if (!managerName) { return '<span class="user-list-empty">-</span>'; }
  var imageUrl = this.safeDisplayValue(record.manager_profile_img || record.manager_gravatar || "", "");
  return [
    '<div class="d-flex align-items-center gap-2 w-100 user-list-person--compact">',
    this.getAvatarHtml(managerName, imageUrl, "user-list-avatar"),
    '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
    '<span class="b5-text fw-normal text-truncate d-block" ' +
    'style="max-width:144px;" ' +
    'title="' + this.escapeHtml(managerName) + '">' +
    this.escapeHtml(managerName) +
    '</span>',
    '</div>',
    '</div>'
  ].join("");
};

MyApp.prototype.renderLocationCell = function (record, type) {
  var location = this.safeDisplayValue(record.location_name, "-");
  if (type !== "display") return location;

  return (
    '<div class="b5-text text-truncate" ' +
    'style="color:#515151; max-width:80px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" ' +
    'title="' + this.escapeHtml(location) + '">' +
    this.escapeHtml(location) +
    '</div>'
  );
};

MyApp.prototype.renderStatusCell = function (record, type) {
  const status = this.safeDisplayValue(record.user_status, "-");
  if (type !== "display") return status;
  if (status === "-") {
    return '<span class="user-list-empty">-</span>';
  }

  const normalized = String(status).toLowerCase();
  const isActive = normalized === "active";

  return `
        <span class="user-list-status ${isActive ? "is-active" : "is-inactive"}">
            ${this.getStatusIcon(normalized)}
            <span class="fw-normal b5-text">${this.escapeHtml(status)}</span>
        </span>
    `;
};

MyApp.prototype.renderUpdatedOnCell = function (record, type) {
  var updatedOn = this.safeDisplayValue(record.last_updated_at, "-");
  if (type !== "display") return updatedOn;

  if (updatedOn === "-") {
    return '<span class="user-list-empty">-</span>';
  }

  var parts = this.splitUpdatedOn(updatedOn);
  return [
    '<div class="d-flex flex-column w-100 min-w-0">',
    '<span class="b5-text" style="color:#515151">' +
    this.escapeHtml(parts.date) +
    "</span>",
    parts.time
      ? '<span class="b5-text" style="color:#515151">' +
      this.escapeHtml(parts.time) +
      "</span>"
      : "",
    "</div>",
  ].join("");
};

MyApp.prototype.renderActionsCell = function (record, type) {
  var t = this;
  var actionState = t.getRowActionState(record);
  var menuActions = t.buildRowActions(record);
  var quickActions = [];
  var dropdownId;

  if (type !== "display") return "";

  if (actionState.canRestore) {
    quickActions.push(
      t.quickActionButtonHtml(
        (t.config.translations || {}).action_restore_user || "Restore User",
        "dtActRestore",
        record.id,
        "restore",
      ),
    );
    menuActions = [];
  } else {
    if (actionState.canEdit) {
      quickActions.push(
        t.quickActionButtonHtml(
          (t.config.translations || {}).action_edit_user || "Edit User",
          "dtActEdit",
          record.id,
          "edit",
        ),
      );
    }
    if (actionState.canDelete) {
      quickActions.push(
        t.quickActionButtonHtml(
          (t.config.translations || {}).action_delete_user || "Delete User",
          "dtActDel",
          record.id,
          "delete",
          "is-delete",
        ),
      );
    }
  }

  if (!quickActions.length && !menuActions.length) {
    return '<span class="user-list-empty">-</span>';
  }

  dropdownId = "user-table-action-dropdown-" + record.id;

  return [
    '<div class="user-list-actions">',
    quickActions.join(""),
    menuActions.length
      ? [
        '<div class="">',
        '<button type="button" class="action-link user-list-menu-toggle" id="',
        dropdownId,
        '" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="More actions">',
        t.getIcon("ellipsis"),
        "</button>",
        '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="',
        dropdownId,
        '">',
        menuActions.join(""),
        "</ul>",
        "</div>",
      ].join("")
      : "",
    "</div>",
  ].join("");
};

MyApp.prototype.getRowActionState = function (record) {
  var hasMergePrimary = this.isFilledValue(record.merge_primary);
  var isMergePrimary = String(record.is_merge_primary || "") === "1";
  var isMergedSecondary = !isMergePrimary && hasMergePrimary;
  var canMerge =
    this.hasPermission("UserMerge") &&
    (!this.config.action_controls ||
      Number(this.config.action_controls.merge_privilege) === 1) &&
    !isMergePrimary &&
    !hasMergePrimary;

  if (this.showDeletedUsers) {
    return {
      canRestore: this.hasPermission("UserDelete"),
    };
  }

  return {
    canEdit: this.hasPermission("UserEdit") && !isMergedSecondary,
    canDelete: this.hasPermission("UserDelete"),
    canClone: this.hasPermission("UserAdd"),
    canView: this.hasPermission("UserView"),
    canSendCredential:
      this.hasPermission("UserSendCredential") &&
      this.isFilledValue(record.email),
    canMerge: canMerge,
    canNotify: this.hasPermission("AnnouncementAdd"),
    canRestore: false,
  };
};

MyApp.prototype.quickActionButtonHtml = function (
  label,
  cls,
  id,
  iconKey,
  extraClass,
) {
  return [
    '<button type="button" class="user-list-action-btn ',
    this.escapeHtml(extraClass || ""),
    " ",
    cls,
    '" data-id="',
    this.escapeHtml(id),
    '" title="',
    this.escapeHtml(label),
    '" aria-label="',
    this.escapeHtml(label),
    '">',
    this.getIcon(iconKey),
    "</button>",
  ].join("");
};

MyApp.prototype.renderRolePill = function (roleName) {
  if (!roleName) return "";

  return [
    '<span data-bs-toggle="tooltip" title="' + this.config.translations.user_role + '" data-bs-original-title="' + this.config.translations.user_role + '" aria-label="' + this.config.translations.user_role + '" class="role-badge ',
    this.getRoleToneClass(roleName),
    '">',
    this.escapeHtml(roleName),
    "</span>",
  ].join("");
};

MyApp.prototype.getRoleToneClass = function (roleName) {
  var normalized = String(roleName || "").toLowerCase();

  if (normalized.indexOf("superadmin") !== -1) return "role-badge-super-admin";
  if (normalized.indexOf("admin") !== -1) return "role-badge-admin";
  if (normalized.indexOf("user") !== -1) return "role-badge-user";
  if (normalized.indexOf("technician") !== -1) return "role-badge-technician";


  return "role-badge-default";
};

MyApp.prototype.getStatusIcon = function (status) {
  return String(status || "").toLowerCase() === "active"
    ? this.icons.active_icon()
    : this.icons.inactive_icon();
};

MyApp.prototype.getPrimaryPhone = function (record) {
  var values = [record.phone, record.phone2, record.work_phone];
  var i;

  for (i = 0; i < values.length; i++) {
    if (this.isFilledValue(values[i])) {
      return [String(values[i]).trim(), i];
    }
  }
  return "";
};

MyApp.prototype.getAvatarHtml = function (name, imageUrl, className) {
  var cssClass = className || "user-list-avatar";
  var safeName = this.escapeHtml(this.safeDisplayValue(name, "User"));

  if (this.isFilledValue(imageUrl)) {
    return (
      '<img src="' +
      this.escapeHtml(imageUrl) +
      '" alt="' +
      safeName +
      '" class="' +
      this.escapeHtml(cssClass) +
      '">'
    );
  }

  return (
    '<span class="' +
    this.escapeHtml(cssClass + " user-list-avatar-fallback") +
    '" aria-hidden="true">' +
    this.escapeHtml(this.getUserInitials(name)) +
    "</span>"
  );
};

MyApp.prototype.getUserInitials = function (name) {
  var value = String(this.safeDisplayValue(name, "")).trim();

  if (!value) return "NA";

  return value.charAt(0).toUpperCase();
};

MyApp.prototype.splitUpdatedOn = function (value) {
  var text = this.safeDisplayValue(value, "");
  var matched = text.match(/^(.*?)(\d{1,2}:\d{2}\s*[AP]M)$/i);
  if (!matched) {
    return { date: text || "-", time: "" };
  }
  return { date: $.trim(matched[1]) || text, time: $.trim(matched[2]) };
};

MyApp.prototype.isFilledValue = function (value) {
  var text;
  if (value === null || value === undefined) return false;
  text = String(value).trim();
  return (
    text !== "" &&
    text !== "0" &&
    text.toLowerCase() !== "null" &&
    text.toLowerCase() !== "undefined"
  );
};

MyApp.prototype.safeDisplayValue = function (value, fallback) {
  return this.isFilledValue(value)
    ? String(value).trim()
    : fallback !== undefined
      ? fallback
      : "-";
};

MyApp.prototype.escapeHtml = function (value) {
  return String(value === null || value === undefined ? "" : value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};

MyApp.prototype.hasPermission = function (name) {
  return (
    Array.isArray(this.config.permissions) &&
    this.config.permissions.indexOf(name) !== -1
  );
};

MyApp.prototype.buildRowActions = function (record) {
  var t = this;
  var id = record.id;
  var actionState = t.getRowActionState(record);
  var actions = [];
  var tr = t.config.translations || {};

  if (actionState.canRestore) {
    actions.push(
      t.actionItemHtml(
        tr.action_restore_user || "Restore User",
        "dtActRestore",
        id,
        "restore",
      ),
    );
    return actions;
  }

  // if (actionState.canEdit) actions.push(t.actionItemHtml(tr.action_edit_user || "Edit User", "dtActEdit", id, "edit"));
  // if (actionState.canDelete) actions.push(t.actionItemHtml(tr.action_delete_user || "Delete User", "dtActDel", id, "delete"));
  // if (actions.length && (actionState.canClone || actionState.canView || actionState.canSendCredential || actionState.canMerge || actionState.canNotify)) {
  //     actions.push(t.actionDividerHtml());
  // }
  if (actionState.canClone) actions.push(t.actionItemHtml(tr.action_clone_user || "Clone User", "dtActClone", id, "clone"));
  if (actionState.canView) actions.push(t.actionItemHtml(tr.action_view_details || "View Details", "dtActView", id, "view"));
  if (actionState.canSendCredential) actions.push(t.actionItemHtml(tr.action_send_credential || "Send Credential", "dtActSendCredential", id, "send_credential"));
  if (actionState.canMerge) actions.push(t.actionItemHtml(tr.action_add_for_merge || "Add for Merge", "add_to_merge", id, "add_for_merge"));
  if (actionState.canNotify) actions.push(t.actionItemHtml(tr.action_notification || "Notification", "dtActNotify", id, "notification"));

  return actions;
};

MyApp.prototype.actionItemHtml = function (label, cls, id, iconKey) {
  return [
    '<li>',
    '<a class="dropdown-item ', cls, '" href="#" data-id="', id, '">',
    this.getIcon(iconKey),
    '<span class="b3-text text-off-white">', label, '</span>',
    '</a>',
    '</li>'
  ].join("");
};

MyApp.prototype.actionDividerHtml = function () {
  return '<li><hr class="dropdown-divider"></li>';
};

MyApp.prototype.getIcon = function (key) {
  return this.icons && this.icons[key] ? this.icons[key]() : "";
};

MyApp.prototype.icons = {
  ellipsis: function () {
    return '<svg width="5" height="14" viewBox="0 0 5 20" fill="none" ><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>';
  },
  edit: function () {
    return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
  },
  delete: function () {
    return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
  },
  clone: function () {
    return '<svg viewBox="0 0 16 16" fill="none" ><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"/></svg>';
  },
  view: function () {
    return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
  },
  send_credential: function () {
    return '<svg viewBox="0 0 18 17" fill="none" ><path d="M15.0445 1.83367C14.0518 0.839533 12.753 0.20888 11.3579 0.0434748C9.9627 -0.12193 8.55249 0.187562 7.3548 0.922008C6.15712 1.65645 5.24182 2.77301 4.75661 4.09151C4.2714 5.41 4.24458 6.85353 4.68048 8.18914L0.366413 12.5032C0.249834 12.6189 0.157408 12.7565 0.0945058 12.9082C0.0316039 13.0599 -0.000518312 13.2226 6.32415e-06 13.3868V15.6282C6.32415e-06 15.9597 0.131702 16.2777 0.366123 16.5121C0.600543 16.7465 0.918486 16.8782 1.25001 16.8782H3.75001C3.91577 16.8782 4.07474 16.8124 4.19195 16.6951C4.30916 16.5779 4.37501 16.419 4.37501 16.2532V15.0032H5.62501C5.79077 15.0032 5.94974 14.9374 6.06695 14.8201C6.18416 14.7029 6.25001 14.544 6.25001 14.3782V13.1282H7.50001C7.58211 13.1283 7.66342 13.1122 7.73929 13.0808C7.81516 13.0494 7.88411 13.0034 7.94219 12.9454L8.68907 12.1977C9.31422 12.4011 9.96762 12.5042 10.625 12.5032H10.6328C11.8683 12.5017 13.0757 12.1341 14.1023 11.4467C15.129 10.7594 15.9289 9.78314 16.401 8.64139C16.8731 7.49965 16.9962 6.24359 16.7548 5.03191C16.5133 3.82022 15.9182 2.70727 15.0445 1.83367ZM15.625 6.41726C15.5399 9.08054 13.3008 11.2501 10.6336 11.2532H10.625C9.99232 11.2543 9.36524 11.1347 8.77735 10.9009C8.66235 10.8509 8.535 10.8368 8.41184 10.8602C8.28868 10.8836 8.1754 10.9435 8.08673 11.0321L7.24141 11.8782H5.62501C5.45925 11.8782 5.30028 11.944 5.18307 12.0613C5.06586 12.1785 5.00001 12.3374 5.00001 12.5032V13.7532H3.75001C3.58425 13.7532 3.42528 13.819 3.30806 13.9363C3.19085 14.0535 3.12501 14.2124 3.12501 14.3782V15.6282H1.25001V13.3868L5.8461 8.79148C5.93472 8.7028 5.99462 8.58953 6.01803 8.46637C6.04143 8.34321 6.02726 8.21586 5.97735 8.10085C5.74278 7.511 5.62318 6.88173 5.62501 6.24695C5.62501 3.57976 7.79766 1.3407 10.4609 1.25554C11.1451 1.23271 11.8266 1.35059 12.4633 1.60188C13.1 1.85317 13.6783 2.23252 14.1624 2.71642C14.6466 3.20033 15.0262 3.77848 15.2778 4.41507C15.5293 5.05166 15.6475 5.73313 15.625 6.41726ZM13.125 4.6907C13.125 4.87612 13.07 5.05737 12.967 5.21154C12.864 5.36572 12.7176 5.48588 12.5463 5.55683C12.375 5.62779 12.1865 5.64636 12.0046 5.61018C11.8228 5.57401 11.6557 5.48472 11.5246 5.35361C11.3935 5.2225 11.3042 5.05545 11.268 4.8736C11.2318 4.69174 11.2504 4.50324 11.3214 4.33193C11.3923 4.16063 11.5125 4.01421 11.6667 3.91119C11.8208 3.80818 12.0021 3.7532 12.1875 3.7532C12.4361 3.7532 12.6746 3.85197 12.8504 4.02779C13.0262 4.2036 13.125 4.44206 13.125 4.6907Z" fill="currentColor"/></svg>';
  },
  add_for_merge: function () {
    return '<svg viewBox="0 0 15 17" fill="none" ><path d="M12.5029 6.87448C11.9629 6.87542 11.4377 7.05089 11.0056 7.37471C10.5735 7.69853 10.2577 8.15336 10.1052 8.67136L6.80755 8.20261C6.6572 8.18135 6.51974 8.10609 6.42083 7.99089L3.6302 4.72995C4.15232 4.46533 4.56502 4.02589 4.7964 3.48821C5.02778 2.95054 5.06315 2.34873 4.89636 1.78765C4.72958 1.22657 4.3712 0.741805 3.88368 0.417834C3.39617 0.0938626 2.81043 -0.0487723 2.22855 0.014786C1.64666 0.0783444 1.10553 0.344066 0.69945 0.765643C0.293371 1.18722 0.0480939 1.73792 0.00636406 2.32178C-0.0353658 2.90563 0.129097 3.48562 0.471092 3.96066C0.813087 4.43571 1.31093 4.77569 1.87786 4.92136V11.3276C1.2887 11.4797 0.77524 11.8415 0.433732 12.3451C0.0922247 12.8487 -0.0538873 13.4596 0.0227842 14.0632C0.0994558 14.6669 0.393647 15.2218 0.850213 15.6241C1.30678 16.0263 1.89437 16.2482 2.50286 16.2482C3.11134 16.2482 3.69894 16.0263 4.1555 15.6241C4.61207 15.2218 4.90626 14.6669 4.98293 14.0632C5.0596 13.4596 4.91349 12.8487 4.57198 12.3451C4.23048 11.8415 3.71702 11.4797 3.12786 11.3276V6.06432L5.47161 8.7987C5.76782 9.14428 6.17962 9.37031 6.6302 9.43464L10.0677 9.92526C10.1715 10.3847 10.4029 10.8054 10.7353 11.139C11.0677 11.4727 11.4876 11.7056 11.9467 11.811C12.4057 11.9165 12.8852 11.8901 13.3298 11.7349C13.7745 11.5798 14.1663 11.3022 14.4602 10.9341C14.754 10.566 14.9378 10.1224 14.9906 9.65438C15.0433 9.18636 14.9627 8.71297 14.7582 8.28874C14.5537 7.8645 14.2334 7.50666 13.8344 7.25645C13.4354 7.00625 12.9738 6.87384 12.5029 6.87448ZM1.25286 2.49948C1.25286 2.25225 1.32617 2.01058 1.46352 1.80502C1.60087 1.59946 1.7961 1.43924 2.0245 1.34463C2.25291 1.25002 2.50425 1.22527 2.74672 1.2735C2.9892 1.32173 3.21193 1.44078 3.38674 1.6156C3.56156 1.79041 3.68061 2.01314 3.72884 2.25562C3.77707 2.49809 3.75232 2.74943 3.65771 2.97783C3.5631 3.20624 3.40288 3.40147 3.19732 3.53882C2.99176 3.67617 2.75008 3.74948 2.50286 3.74948C2.17134 3.74948 1.8534 3.61778 1.61898 3.38336C1.38455 3.14894 1.25286 2.831 1.25286 2.49948ZM3.75286 13.7495C3.75286 13.9967 3.67955 14.2384 3.5422 14.4439C3.40484 14.6495 3.20962 14.8097 2.98121 14.9043C2.7528 14.9989 2.50147 15.0237 2.259 14.9755C2.01652 14.9272 1.79379 14.8082 1.61898 14.6334C1.44416 14.4585 1.32511 14.2358 1.27688 13.9933C1.22865 13.7509 1.2534 13.4995 1.34801 13.2711C1.44262 13.0427 1.60283 12.8475 1.8084 12.7101C2.01396 12.5728 2.25563 12.4995 2.50286 12.4995C2.83438 12.4995 3.15232 12.6312 3.38674 12.8656C3.62116 13.1 3.75286 13.418 3.75286 13.7495ZM12.5029 10.6245C12.2556 10.6245 12.014 10.5512 11.8084 10.4138C11.6028 10.2765 11.4426 10.0812 11.348 9.85284C11.2534 9.62443 11.2286 9.37309 11.2769 9.13062C11.3251 8.88814 11.4442 8.66541 11.619 8.4906C11.7938 8.31578 12.0165 8.19673 12.259 8.1485C12.5015 8.10027 12.7528 8.12502 12.9812 8.21963C13.2096 8.31424 13.4048 8.47446 13.5422 8.68002C13.6795 8.88558 13.7529 9.12725 13.7529 9.37448C13.7529 9.706 13.6212 10.0239 13.3867 10.2584C13.1523 10.4928 12.8344 10.6245 12.5029 10.6245Z" fill="currentColor"></path></svg>';
  },
  notification: function () {
    return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.8272 11.8703C14.3936 11.1234 13.7491 9.01016 13.7491 6.25C13.7491 4.5924 13.0906 3.00269 11.9185 1.83058C10.7464 0.65848 9.1567 0 7.49909 0C5.84149 0 4.25178 0.65848 3.07968 1.83058C1.90757 3.00269 1.24909 4.5924 1.24909 6.25C1.24909 9.01094 0.603782 11.1234 0.170188 11.8703C0.0594621 12.0602 0.000761949 12.2759 7.36794e-06 12.4957C-0.000747213 12.7155 0.0564706 12.9316 0.16589 13.1223C0.27531 13.3129 0.433063 13.4713 0.623239 13.5815C0.813416 13.6917 1.02929 13.7498 1.24909 13.75H4.43738C4.58157 14.4556 4.96505 15.0897 5.52295 15.5451C6.08085 16.0006 6.77892 16.2493 7.49909 16.2493C8.21927 16.2493 8.91734 16.0006 9.47524 15.5451C10.0331 15.0897 10.4166 14.4556 10.5608 13.75H13.7491C13.9688 13.7497 14.1846 13.6915 14.3747 13.5812C14.5647 13.471 14.7224 13.3125 14.8317 13.1219C14.941 12.9313 14.9982 12.7153 14.9974 12.4955C14.9966 12.2758 14.9379 12.0601 14.8272 11.8703ZM7.49909 15C7.11145 14.9999 6.73338 14.8796 6.41691 14.6558C6.10043 14.4319 5.86112 14.1155 5.73191 13.75H9.26628C9.13707 14.1155 8.89776 14.4319 8.58128 14.6558C8.26481 14.8796 7.88674 14.9999 7.49909 15ZM1.24909 12.5C1.85066 11.4656 2.49909 9.06875 2.49909 6.25C2.49909 4.92392 3.02588 3.65215 3.96356 2.71447C4.90124 1.77678 6.17301 1.25 7.49909 1.25C8.82518 1.25 10.0969 1.77678 11.0346 2.71447C11.9723 3.65215 12.4991 4.92392 12.4991 6.25C12.4991 9.06641 13.146 11.4633 13.7491 12.5H1.24909Z" fill="currentColor"/></svg>';
  },
  placeholder: function () {
    return '<svg viewBox="0 0 16 16" fill="none" ><rect x="2" y="2" width="12" height="12" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M5 8H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
  },
  restore: function () {
    return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><g clip-path="url(#clip0_2515_18046)"><path d="M6.6784 20.5673C2.53239 18.0212 0.759002 12.7584 2.71775 8.1439C4.8757 3.0601 10.7463 0.68822 15.8301 2.84617C20.9139 5.00412 23.2858 10.8747 21.1278 15.9585C20.2846 17.945 18.8746 19.5174 17.1661 20.5673" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 16V20.4C17 20.7314 17.2686 21 17.6 21H22" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22.01L12.01 21.9989" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_2515_18046"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>';
  },
  active_icon: function () {
    return `<svg  width="14" height="14" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`;
  },
  inactive_icon: function () {
    return `<svg  width="14" height="14" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`;
  },
};

MyApp.prototype.initFilters = function () {
  var t = this;
  var s2Opts = { width: "100%" };
  var wrapper = t.filters.wrapper;
  var tr = t.config.translations || {};
  t.filters.based_on_company = wrapper.find("#filter_by_based_on_company");
  t.filters.company = wrapper.find("#filter_by_company");
  t.filters.job_type = wrapper.find("#filter_by_job_type");
  t.filters.status = wrapper.find("#filter_by_status");
  t.filters.department = wrapper.find("#filter_by_department");
  t.filters.location = wrapper.find("#filter_by_location");
  t.filters.role = wrapper.find("#filter_by_user_role");
  t.filters.manager = wrapper.find("#filter_by_manager");
  t.filters.service_ticket = wrapper.find("#filter_by_service_ticket");
  t.filters.search = t.content.find(".user-list-search");

  t.filters.fun = {
    reload_company: function () {
      if (!t.filters.company.length) return;
      t.filters.company
        .select2(
          $.extend({}, s2Opts, {
            dropdownParent: wrapper,
            allowClear: true,
            // placeholder: tr.Filter_By_Company || "Filter By Company",
            placeholder: tr.No_Filter || "No Filter",
            ajax: {
              url: t.config.url.get_company_by_user_access,
              dataType: "json",
              method: "get",
              delay: 300,
              data: function (params) {
                return {
                  search: params.term,
                  page: params.page || 1,
                };
              },
            },
          }),
        )
        .on("select2:select select2:clear", function () {
          t.filters.department.val(null).trigger("change");
        });
    },
    reload_department: function () {
      if (!t.filters.department.length) return;
      t.filters.department.select2(
        $.extend({}, s2Opts, {
          dropdownParent: wrapper,
          allowClear: true,
          placeholder: tr.No_Filter || "No Filter",
          ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            method: "get",
            delay: 300,
            data: function (params) {
              return {
                search: params.term,
                company_id: t.filters.company.val(),
                page: params.page || 1,
              };
            },
          },
        }),
      );
    },
    reload_location: function () {
      if (!t.filters.location.length) return;
      t.filters.location
        .empty()
        .append(new Option(tr.No_Filter || "No Filter", "", false, false));
      t.filters.location.select2(
        $.extend({}, s2Opts, {
          dropdownParent: wrapper,
          allowClear: true,
          placeholder: tr.Select_the_Location || "Select the Location",
          ajax: {
            url: t.config.getLocationByAjax,
            dataType: "json",
            delay: 300,
            data: function (p) {
              return { search: p.term, page: p.page || 1 };
            },
          },
        }),
      );
    },
    reload_roles: function () {
      if (!t.filters.role.length) return;
      t.filters.role
        .empty()
        .append(new Option(tr.No_Filter || "No Filter", "", true, true));
      if (Array.isArray(t.config.roles)) {
        $.each(t.config.roles, function (i, k) {
          t.filters.role.append(new Option(k.name, k.id));
        });
      }
      t.filters.role.trigger("change");
    },
    reload_username: function () {
      if (!t.filters.manager.length) return;
      t.filters.manager
        .empty()
        .append(new Option(tr.No_Filter || "No Filter", "", false, false));
      t.filters.manager.select2(
        $.extend({}, s2Opts, {
          allowClear: true,
          dropdownParent: wrapper,
          placeholder: tr.Select_the_Manager || "Select the Manager",
          ajax: {
            url: t.config.url.getManagerByAjax,
            dataType: "json",
            delay: 300,
            data: function (p) {
              return { search: p.term, page: p.page || 1 };
            },
          },
        }),
      );
    },
  };

  MyApp.prototype.initPhoneDropdown = function (selector, selectedCode) {
    let element = $(selector);
    if (element.hasClass("select2-hidden-accessible")) {
      element.select2('destroy');
    }
    let countryOptions = '<option value=""></option>';
    $.each(t.config.countries_data, function (index, country) {
      let phoneCode = country.phonecode || '';
      if (!phoneCode.startsWith('+')) {
        phoneCode = '+' + phoneCode;
      }
      countryOptions += `
                <option value="${country.id}">
                    ${phoneCode}
                </option>
            `;
    });
    element.html(countryOptions);
    element.select2({ dropdownParent: element.parent(), placeholder: "Enter Code", allowClear: true });
    element.val(selectedCode).trigger('change');
  }

  $.each(
    ["job_type", "status", "service_ticket", "company", "department", "role", "based_on_company"],
    function (i, key) {
      if (t.filters[key] && t.filters[key].length) {
        t.filters[key].select2(
          $.extend({}, s2Opts, { dropdownParent: wrapper, allowClear: true, placeholder: tr.No_Filter || "No Filter" }),
        );
      }
    },
  );

  t.filters.fun.reload_company();
  t.filters.fun.reload_department();
  t.filters.fun.reload_location();
  t.filters.fun.reload_roles();
  t.filters.fun.reload_username();
};

MyApp.prototype.cacheFilterValues = function () {
  var t = this;
  t.config.search =
    t.filters.search && t.filters.search.val
      ? t.filters.search.val() || ""
      : "";
  t.config.other_filters = {};
  t.config.dashboard_filters = {
    location: t.config.locations,
    department: t.config.departments,
    user_staff: t.config.user_staff,
  };

  var map = {
    company: "company",
    job_type: "job_type",
    department: "department",
    location: "location",
    status: "status",
    role: "role",
    manager: "manager",
    service_ticket: "service_tickets",
    based_on_company: "based_on_company",
  };

  $.each(map, function (filterKey, configKey) {
    var el = t.filters[filterKey];
    if (el && el.length) {
      var val = el.val();
      if (val && val !== "null") t.config.other_filters[configKey] = val;
    }
  });

  t.config.export_filters = btoa(
    JSON.stringify({
      search: t.config.search,
      other_filters: t.config.other_filters,
      dashboard_filters: t.config.dashboard_filters,
      showDeletedUsers: t.showDeletedUsers,
    }),
  );

  t.updateFilterCount();
};

MyApp.prototype.applyFilters = function () {
  this.cacheFilterValues();
  this.reload();
  this.closeFilterModal();
};

MyApp.prototype.clearFilters = function () {
  var t = this;
  $.each(
    [
      "company",
      "job_type",
      "status",
      "department",
      "location",
      "role",
      "manager",
      "service_ticket",
      "based_on_company",
    ],
    function (i, key) {
      if (t.filters[key] && t.filters[key].length)
        t.filters[key].val("null").trigger("change");
    },
  );
  t.cacheFilterValues();
  t.reload();
  t.closeFilterModal();
};

MyApp.prototype.getActiveFilterCount = function () {
  return Object.keys(this.config.other_filters || {}).length;
};

MyApp.prototype.updateFilterCount = function () {
  var badge = this.content.find(".filter-count-badge");
  if (!badge.length) return;
  var count = this.getActiveFilterCount();
  count > 0
    ? badge.text(count).removeClass("d-none")
    : badge.text("0").addClass("d-none");
};

MyApp.prototype.initUserModal = function () {
  var t = this;
  if (t.userMdl.initialized) return;

  t.userMdl.mdl = $("#user-mdl");
  if (!t.userMdl.mdl.length) return;

  t.userMdl.frm = t.userMdl.mdl.find("#user-mdl-frm");
  t.userMdl.btnSubmit = t.userMdl.mdl.find("#btnSubmit");
  t.userMdl.loader = t.userMdl.mdl.find("#user_mdl_loader");
  t.userMdl.tabs = t.userMdl.mdl.find("#mytabs");

  var frm = t.userMdl.frm;
  t.userMdl.el = {
    company: frm.find("#company_id"),
    manager: frm.find("#manager_id"),
    department: frm.find("#department_id"),
    role: frm.find("#role_id"),
    job_type: frm.find("#job_type"),
    location: frm.find("#location_id"),
    internal_place: frm.find("#internal_place_id"),
    base_location: frm.find("#base_location_id"),
    user_company_id: frm.find("#user_company_id"),
    technician_company_access: frm.find("#technician_company_access"),
    ticket_department: frm.find("#ticket_department_id"),
    asset_departments_id: frm.find("#asset_departments_id"),
    request_approval_delegated_user: frm.find("#request_approval_delegated_user"),
    doj: frm.find("#doj"),
    last_working_date: frm.find("#last_working_date"),
    phone: frm.find("input[name='phone']"),
    phone2: frm.find("#phone2"),
    work_phone: frm.find("#work_phone"),
    address: frm.find("textarea[name='address']"),
    email: frm.find("#email"),
    active_status: frm.find("#active_status"),
    password: frm.find("input[name='password']"),
    groups: frm.find("#groups"),
    ex_user_company: frm.find("#ex_user_company"),
    user_permission: frm.find(".user_permission"),
    edit_mdl: frm.find(".edit_mdl"),
    notes: frm.find("textarea[name='notes']"),
    avatar: frm.find('.user_profile_avatar'),
    phone_country_code: frm.find('#phone_country_id').trigger('change'),
    phone2_country_code: frm.find("#phone2_country_id").trigger('change'),
    work_phone_country_code: frm.find("#work_phone_country_id").trigger('change')
  };

  t.userMdl.lblPassword = frm.find("label[for='password']");
  t.userMdl.imgPreview = t.userMdl.mdl.find("#imgview");
  t.userMdl.imgWrapper = t.userMdl.mdl.find(".imgviewcover");

  t.prepareUserModalFieldLayout();

  if (typeof t.userMdl.el.doj.datepicker === "function") {
    t.userMdl.el.doj.datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",
      container: "#user-mdl",
      orientation: "bottom auto",
    });
    t.userMdl.el.last_working_date.datepicker({
      autoclose: true,
      format: "dd/mm/yyyy",
      container: "#user-mdl",
      orientation: "bottom auto",
    });
    $("#user-mdl .modal-body").on("scroll", function () {
      t.userMdl.el.doj.datepicker("hide");
      t.userMdl.el.last_working_date.datepicker("hide");
    });
    t.userMdl.el.doj.on("changeDate", function (e) {
      t.userMdl.el.last_working_date.datepicker("setStartDate", e.date);
      try {
        if (t.userMdl.el.last_working_date.datepicker("getDate") < e.date) {
          t.userMdl.el.last_working_date.datepicker("update", "");
        }
      } catch (err) {
        t.userMdl.el.last_working_date.datepicker("update", "");
      }
    });
  }

  t.userMdl.el.user_permission.on("click", function () {
    var val = $(this).val();
    if (val == 1) {
      $("#technicianCompanyAccess").show();
      t.userMdl.el.user_company_id.find("option:selected").each(function () {
        var v = $(this).val();
        var txt = $(this).text();
        if (
          !t.userMdl.el.technician_company_access.find(
            'option[value="' + v + '"]',
          ).length
        ) {
          t.userMdl.el.technician_company_access.append(
            new Option(txt, v, true, true),
          );
        }
      });
      t.userMdl.el.technician_company_access.trigger("change");
    } else {
      t.userMdl.el.technician_company_access
        .empty()
        .val(null)
        .trigger("change");
      $("#technicianCompanyAccess").hide();
    }
  });

  t.userMdl.mdl.on("change", "#send_email", function () {
    t.syncPasswordRequirement();
  });

  t._initFormValidator();

  t.userMdl.btnSubmit.on("click", $.proxy(t.handleSubmit, t));

  t.userMdl.mdl.on("hide.bs.modal", function () {
    if (t.userMdl.tabs && t.userMdl.tabs.find) {
      t.userMdl.tabs.find("a:first").tab("show");
    }
    t.userMdl.btnSubmit.prop("disabled", false);
    t.userMdl.loader.hide();
  });

  t.userMdl.initialized = true;
};

MyApp.prototype.prepareUserModalFieldLayout = function () {
  var frm =
    this.userMdl && this.userMdl.frm ? this.userMdl.frm : $("#user-mdl-frm");

  if (!frm.length) return;

  frm.find(".input-group").each(function () {
    $(this).closest(".amg-form-field").addClass("amg-form-field-row");
  });
};

MyApp.prototype.syncPasswordRequirement = function () {
  var isRequired;

  if (!this.userMdl || !this.userMdl.el || !this.userMdl.el.password.length)
    return;

  isRequired = !this.currentEditingUserId;

  this.userMdl.lblPassword.toggleClass("required", isRequired);
  this.userMdl.el.password.prop("required", isRequired);

  if (!this.userMdl.frmValidator) return;

  this.userMdl.frmValidator.settings.rules.password.required = isRequired;
  this.userMdl.el.password.rules("remove", "required");

  if (isRequired) {
    this.userMdl.el.password.rules("add", { required: true });
  }

  this.userMdl.el.password.valid();
};

MyApp.prototype.getUserModalErrorWrap = function (element) {
  var row = element.closest(".amg-form-field-row");
  var wrap;

  if (!row.length) return $();

  wrap = row.children(".amg-form-error-wrap");
  if (!wrap.length) {
    wrap = $('<div class="amg-form-error-wrap"></div>');
    row.append(wrap);
  }

  return wrap;
};

MyApp.prototype.updateUserModalValidationState = function (element, hasError) {
  var group = element.closest(".input-group");
  var isSelect2 = element.hasClass("select2-hidden-accessible");

  if (group.length) {
    group.toggleClass("amg-form-invalid", !!hasError);
  }

  if (isSelect2) {
    element
      .next(".select2-container")
      .find(".select2-selection")
      .toggleClass("amg-form-select-error", !!hasError);
  }
};

MyApp.prototype.getUserModalSelectDropdownParent = function (element) {
  var mdl =
    this.userMdl && this.userMdl.mdl ? this.userMdl.mdl : $("#user-mdl");
  if (!element || !element.length) return mdl;

  var parent = element.closest(".input-group");
  if (parent.length) return parent;

  parent = element.closest(".row-cover");
  return parent.length ? parent : mdl;
};

MyApp.prototype.syncUserModalSelect2Width = function (element) {
  var t = this;
  if (!element || !element.length) return;

  var parent = t.getUserModalSelectDropdownParent(element);
  var container = element.next(".select2-container");
  if (!parent.length || !container.length) return;

  window.requestAnimationFrame(function () {
    var dropdownContainer = parent.children(".select2-container--open").last();
    if (!dropdownContainer.length)
      dropdownContainer = parent.find(".select2-container--open").last();
    if (!dropdownContainer.length) return;

    var width = container.outerWidth();
    if (!width) return;

    dropdownContainer.css("width", width + "px");
    dropdownContainer.find(".select2-dropdown").css({
      width: width + "px",
      minWidth: width + "px",
    });
  });
};

MyApp.prototype.initUserModalSelects = function () {
  var t = this;
  if (t.userMdl.selectsInitialized) return;

  var el = t.userMdl.el;
  var mdl = t.userMdl.mdl;
  var tr = t.config.translations || {};

  var base = {
    width: "100%",
    allowClear: true,
    templateSelection: function (data, container) {
      $(container).attr("title", data.text || "");
      var txt = data.text || "";
      return txt.length > 50 ? txt.substring(0, 50) + "..." : txt;
    },
  };

  function s2(element, opts) {
    if (!element || !element.length) return;
    opts = $.extend({}, opts);
    if (
      !opts.dropdownParent ||
      (mdl && opts.dropdownParent && opts.dropdownParent[0] === mdl[0])
    ) {
      opts.dropdownParent = t.getUserModalSelectDropdownParent(element);
    }
    if (element.hasClass("select2-hidden-accessible"))
      element.select2("destroy");
    element.select2(opts);
    element
      .off("select2:open.userModalSelect2")
      .on("select2:open.userModalSelect2", function () {
        t.syncUserModalSelect2Width($(this));
      });
  }

  s2(
    el.company,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_Company || "Select Company",
      ajax: {
        url: t.config.url.getCompanyUsers,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return { search: p.term, page: p.page || 1 };
        },
      },
    }),
  );

  el.company.on("change", function () {
    el.department.empty().trigger("change");
    el.ticket_department.empty().trigger("change");
    el.location.empty().trigger("change");
    el.internal_place.empty().trigger("change");
    el.base_location.empty().trigger("change");
  });

  s2(
    el.department,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_the_Department || "Select Department",
      ajax: {
        url: t.config.url.departments_new,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: el.company.val(),
          };
        },
      },
    }),
  );

  s2(
    el.ticket_department,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_the_Department || "Select Department",
      multiple: true,
      ajax: {
        url: t.config.url.departments_new,
        dataType: "json",
        delay: 300,
        data: function (p) {
          const userCompanies = el.user_company_id.val() || [];
          const companyId = el.company.val();

          // used for merging user companies and the company in which user works and taking unique value from both
          const requestCompanies = [
            ...new Set([
              ...userCompanies,
              ...(companyId ? [companyId] : [])
            ])
          ];
          return {
            search: p.term,
            page: p.page || 1,
            type: "ticket",
            company_id: requestCompanies,
          };
        },
      },
    }),
  );

  el.user_company_id.on('change', function() {
    el.ticket_department.val(null).trigger('change');
  });

  t._loadRolesSelect();

  s2(
    el.job_type,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.user_type || "User Type",
    }),
  );

  el.job_type.on("change", function () {
    var cover = el.ex_user_company.closest(".row");
    var val = el.job_type.val();
    if (!val || val == 0) {
      el.ex_user_company.val("");
      if (cover.length) cover.hide();
    } else {
      if (cover.length) cover.show();
    }
  });

  // s2(el.manager, $.extend({}, base, {
  //     dropdownParent: mdl,
  //     placeholder: tr.Select_the_Manager || "Select Manager",
  //     ajax: {
  //         url: t.config.getManagerByAjax,
  //         dataType: "json",
  //         delay: 300,
  //         data: function (p) { return { search: p.term, page: p.page || 1 }; },
  //         processResults: function (data) {
  //             if (t.currentEditingUserId && Array.isArray(data.results)) {
  //                 data.results = data.results.filter(function (item) {
  //                     return item.id != t.currentEditingUserId;
  //                 });
  //             }
  //             return data;
  //         }
  //     }
  // }));

  s2(
    el.manager,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_the_Manager || "Select Manager",
      templateResult: function (item) {
        return t._userDropdownFormat(item);
      },
      templateSelection: function (item) {
        // if (!item || !item.text) return item.text;
        if (!item.id) {
          return item.text;
        }

        var name = t.safeDisplayValue(item.text, "-");
        var imageUrl = t.safeDisplayValue(item.img_path, "");
        var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar");
        return $(
          '<div class="d-flex align-items-center gap-2">' +
          avatarHtml +
          "<span>" +
          t.escapeHtml(name) +
          "</span>" +
          "</div>",
        );
      },
      escapeMarkup: function (m) {
        return m;
      }, // IMPORTANT
      ajax: {
        url: t.config.getManagerByAjax,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: el.company.val(),
          };
        },
        processResults: function (data) {
          if (t.currentEditingUserId && Array.isArray(data.results)) {
            data.results = data.results.filter(function (item) {
              return item.id != t.currentEditingUserId;
            });
          }
          data.results = (data.results || []).map(function (item) {
            return {
              id: item.id,
              text: item.text || item.full_name || "",
              email: item.email || "",
              employee_num: item.employee_num || "",
              status: item.status,
              img_path: item.img_path || item.profile_img || "", // IMPORTANT
            };
          });
          return data;
        },
      },
    }),
  );

  s2(
    el.location,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_the_Location || "Select Location",
      ajax: {
        url: t.config.getLocationByAjax,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: el.company.val(),
          };
        },
      },
    }),
  );

  el.location.on("change", function () {
    t.loadInternalPlaces();
  });

  s2(
    el.internal_place,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.select_place || "Select Place",
    }),
  );

  s2(
    el.base_location,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_base_Location || "Select Base Location",
      ajax: {
        url: t.config.getLocationByAjax,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: el.company.val(),
          };
        },
      },
    }),
  );

  s2(
    el.user_company_id,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: tr.Select_the_User_Company || "Select User Company",
      multiple: true,
      ajax: {
        url: t.config.url.getCompanyUsers,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return { search: p.term, page: p.page || 1 };
        },
        processResults: function (data) {
          return {
            results: data.results || [],
            pagination: data.pagination || { more: false },
          };
        },
      },
    }),
  );

  s2(
    el.technician_company_access,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder:
        tr.select_the_technician_company || "Select Technician Company",
      multiple: true,
      ajax: {
        url: t.config.url.getCompanyUsers,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return { search: p.term, page: p.page || 1 };
        },
      },
    }),
  );

  if (el.asset_departments_id && el.asset_departments_id.length) {
    s2(
      el.asset_departments_id,
      $.extend({}, base, {
        dropdownParent: mdl,
        placeholder: tr.Select_the_Department || "Select Department",
        multiple: true,
        ajax: {
          url: t.config.url.getAssetDepartments,
          dataType: "json",
          delay: 300,
          data: function (p) {
            return { search: p.term, page: p.page || 1 };
          },
        },
      }),
    );
  }

  s2(
    el.active_status,
    $.extend({}, base, {
      dropdownParent: mdl,
      allowClear: false,
      data: [
        { id: 1, text: tr.Yes || "Yes" },
        { id: 0, text: tr.No || "No" },
      ],
    }),
  );

  s2(
    el.request_approval_delegated_user,
    $.extend({}, base, {
      dropdownParent: mdl,
      placeholder: "Select user for delegation",
      ajax: {
        url: t.config.url.getUsersParameterBase,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            exclude_logged_user: 0,
            exclude_user_id: t.currentEditingUserId, // adding this key for excluding the user from service request delegation
            user_status: 1,
          };
        },
      },
      templateResult: function (data) {
        if (!data || data.loading)
          return $("<div>" + (data ? data.text : "") + "</div>");
        return t._userDropdownFormat(data);
      },
    }),
  );

  if (el.groups && el.groups.length) {
    s2(el.groups, $.extend({}, base, { dropdownParent: mdl }));
  }

  this.initPhoneDropdown(el.phone_country_code);
  this.initPhoneDropdown(el.phone2_country_code);
  this.initPhoneDropdown(el.work_phone_country_code);

  t.userMdl.selectsInitialized = true;
};

MyApp.prototype.loadInternalPlaces = function () {
  var t = this;
  var el = t.userMdl.el.internal_place;
  var locationId = t.userMdl.el.location.val();
  var tr = t.config.translations || {};

  el.empty().append(
    new Option(tr.select_place || "Select Place", "", false, false),
  );
  el.trigger("change");

  if (!locationId || locationId < 1 || !t.config.getInternalPlaceByAjax) return;

  $.get(t.config.getInternalPlaceByAjax + "/" + locationId)
    .done(function (data) {
      if (data && data.results && data.results.length) {
        $.each(data.results, function (i, v) {
          var preselect = $.inArray(v.id, t.internal_places) !== -1;
          el.append(new Option(v.text, v.id, preselect, preselect));
        });
      }
    })
    .always(function () {
      el.trigger("change");
      t.internal_places = [];
    });
};

MyApp.prototype._loadRolesSelect = function () {
  var t = this;
  var el = t.userMdl.el.role;
  var tr = t.config.translations || {};

  if (!el || !el.length) return;

  el.empty().append(new Option(tr.select_role || "Select Role", ""));

  if (t.config.roles && Object.keys(t.config.roles).length > 0) {
    $.each(t.config.roles, function (i, v) {
      el.append(new Option(v.name, v.id));
    });
  }

  if (el.hasClass("select2-hidden-accessible")) el.select2("destroy");
  el.select2({
    width: "100%",
    allowClear: true,
    dropdownParent: t.getUserModalSelectDropdownParent(el),
    placeholder: tr.select_role || "Select Role",
  });

  el.off("select2:open.userModalSelect2").on(
    "select2:open.userModalSelect2",
    function () {
      t.syncUserModalSelect2Width($(this));
    },
  );

  el.on("change", function () {
    var selected = el.find("option:selected");
    var val = selected.val();
    var name = selected.text().trim().toLowerCase();
    $("#userPermission")[val == "4" && name === "user" ? "hide" : "show"]();
    // changes for automatically selecting service ticket access as deny in add user modal
    if (!(val == "4" && name === "user") && !$('input[name="user_permission[service_tickets]"]:checked').length) {
      $('input[name="user_permission[service_tickets]"][value="0"]').prop('checked', true);
    }
  });
};

MyApp.prototype._loadGroupsOptions = function (selectedGroups) {
  var t = this;
  var el = t.userMdl.el.groups;
  if (!el || !el.length || !t.config.getGroupByAjax) return;

  el.empty();
  $.getJSON(t.config.getGroupByAjax, function (data) {
    if (typeof data === "object") {
      $.each(data.results, function (name, v) {
        var isSelected =
          typeof selectedGroups !== "undefined" &&
          $.inArray(v.id, selectedGroups) !== -1;
        el.append(
          $("<option>")
            .attr("value", v.id)
            .text(v.text)
            .prop("selected", isSelected),
        );
      });
    }
    el.trigger("change");
  });
};

MyApp.prototype._initFormValidator = function () {
  var t = this;
  if (!$.validator) return;

  $.validator.addMethod(
    "customEmail",
    function (v, el) {
      return (
        this.optional(el) ||
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v)
      );
    },
    "Please enter a valid email address.",
  );

  $.validator.addMethod(
    "strongPassword",
    function (v, el) {
      return (
        this.optional(el) ||
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^])[A-Za-z\d@$!%*?&#^]{8,}$/.test(
          v,
        )
      );
    },
    "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.",
  );

  $.validator.addMethod(
    "notEqualToFields",
    function (v, el, params) {
      var username = $("[name='" + params.username + "']").val() || "";
      var email = $("[name='" + params.email + "']").val() || "";
      return (
        this.optional(el) ||
        (v.toLowerCase() !== username.toLowerCase() &&
          v.toLowerCase() !== email.toLowerCase())
      );
    },
    "Password should not match username or email.",
  );

  $.validator.addMethod(
    "name_format",
    function (v, el) {
      return this.optional(el) || /^([a-zA-Z0-9& _.,\/\-']+)$/.test(v);
    },
    "Please enter a valid value.",
  );

  $.validator.addMethod("must_have_alphabet", function (v, el) {
    return this.optional(el) || /[a-zA-Z]/.test(v);
  }, "Name must contain at least one alphabet.");

  $.validator.addMethod("fileExtension", function (value, element, param) {

    if (element.files.length === 0) {
      return true;
    }

    var extension = value.split('.').pop().toLowerCase();

    return $.inArray(extension, param) !== -1;

  }, function (params, element) {

    return "Only files with these extensions are allowed: " + params.join(", ");
  });

  $.validator.addMethod("filesize", function (value, element, param) {

    if (element.files.length === 0) {
      return true;
    }

    return element.files[0].size <= param;

  }, "File size must be less than 2 MB.");

  t.userMdl.frmValidator = t.userMdl.frm.validate({
    onsubmit: false,
    rules: {
      first_name: { required: true, name_format: true, must_have_alphabet: true, maxlength: 255 },
      last_name: { required: true, name_format: true, must_have_alphabet: true, maxlength: 255 },
      username: { required: true, must_have_alphabet: true },
      email: { required: false, customEmail: true },
      company_id: { required: true },
      location_id: { required: true },
      role_id: { required: true },
      phone: { minlength: 6, maxlength: 20 },
      phone2: { minlength: 6, maxlength: 20 },
      work_phone: { minlength: 6, maxlength: 20 },
      notes: { maxlength: 255, clean_text_only: true },
      address: { maxlength: 255, clean_text_only: true },
      employee_num: {
        str_name_format: true,
        clean_text_only: true
      },
      business_unit: {
        clean_text_only: true
      },
      delivery_unit: {
        clean_text_only: true
      },
      seat_no: {
        required: false,
        clean_text_only: true
      },
      phone: {
        number: false,
        minlength: 6,
        maxlength: 20,
        phoneWithCountryCode: true,
      },
      phone2: {
        number: false,
        minlength: 6,
        maxlength: 20,
        phoneWithCountryCode: true,
      },
      work_phone: {
        number: false,
        minlength: 6,
        maxlength: 20,
        phoneWithCountryCode: true,
      },
      role_id: {
        required: true
      },
      notes: {
        maxlength: 255,
        remarks: false,
        clean_text_only: true
      },
      address: {
        maxlength: 255,
        remarks: false,
        clean_text_only: true
      },
      jobtitle: {
        clean_text_only: true
      },
      password: {
        str_name_format: false,
        strongPassword: true,
        notEqualToFields: {
          username: "username",
          email: "email"
        },
      },
      ex_user_company: {
        str_name_format: true,
        clean_text_only: true
      },
      password: {
        strongPassword: true,
        notEqualToFields: { username: "username", email: "email" }
      },
      avatar: {
        fileExtension: ["jpeg", "jpg", "bmp", "png"],
        filesize: 2097152
      },
      rules: {
        "user_permission[service_tickets]": {
          required: true
        }
      },
    },
    messages: {
      "user_permission[service_tickets]": {
        required: "Please select service tickets access access or deny."
      }
    },
    errorPlacement: function (error, element) {
      var errorWrap = t.getUserModalErrorWrap(element);

      if (errorWrap.length) {
        error.appendTo(errorWrap);
      } else {
        error.insertAfter(element.closest(".input-group"));
      }

      t.updateUserModalValidationState(element, true);
      $("#fillthedata").html(
        (t.config.translations || {}).fill_users_details || "",
      );
    },
    highlight: function (element) {
      t.updateUserModalValidationState($(element), true);
        const phoneMap = {
            phone: "#phone_country_id",
            phone2: "#phone2_country_id",
            work_phone: "#work_phone_country_id"
        };

        const selectId = phoneMap[element.id];

        if (selectId) {
            $(selectId).next(".select2").find(".select2-selection").addClass("amg-form-select-error");
        }

    },
    unhighlight: function (element) {
      t.updateUserModalValidationState($(element), false);
        
      const phoneMap = {
          phone: "#phone_country_id",
          phone2: "#phone2_country_id",
          work_phone: "#work_phone_country_id"
      };

      const selectId = phoneMap[element.id];

      if (selectId) {
          $(selectId).next(".select2").find(".select2-selection").removeClass("amg-form-select-error");
      }

    },
    invalidHandler: function (event, validator) {
      if (validator.numberOfInvalids()) {
        validator.errorList[0].element.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });
      }
    },
  });
};

MyApp.prototype.resetUserModal = function () {
  var t = this;
  var el = t.userMdl.el;
  var tr = t.config.translations || {};

  t.userMdl.frm.trigger("reset");
  $("#fillthedata").html("");
  if (t.userMdl.frmValidator) t.userMdl.frmValidator.resetForm();
  t.userMdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
  t.userMdl.frm
    .find(".amg-form-select-error")
    .removeClass("amg-form-select-error");
  t.userMdl.frm.find(".error").removeClass("error");
  t.userMdl.frm.find(".amg-form-error-wrap").empty();

  el.company.val(null).trigger("change");
  el.department.empty().trigger("change");
  el.ticket_department.empty().trigger("change");
  el.manager.empty().append(new Option(tr.Select_the_Manager || "Select Manager", "")).trigger("change");
  el.location.empty().append(new Option(tr.Select_the_Location || "Select Location", "")).trigger("change");
  el.internal_place.empty().append(new Option(tr.select_place || "Select Place", "")).trigger("change");
  el.base_location.empty().append(new Option(tr.Select_base_Location || "Select Base Location", "")).trigger("change");
  el.user_company_id.empty().val(null).trigger("change");
  el.technician_company_access.empty().val(null).trigger("change");
  this.initPhoneDropdown(t.userMdl.el.phone_country_code);
  this.initPhoneDropdown(t.userMdl.el.phone2_country_code);
  this.initPhoneDropdown(t.userMdl.el.work_phone_country_code);
  $("#technicianCompanyAccess").hide();

  if (el.asset_departments_id && el.asset_departments_id.length) {
    el.asset_departments_id.empty().trigger("change");
  }

  el.request_approval_delegated_user.empty().trigger("change");

  if (typeof el.doj.datepicker === "function") {
    el.doj.datepicker("update", "");
    el.last_working_date.datepicker("update", "");
  }

  t._loadImageViewer("", "");
  el.edit_mdl.addClass("hide");
};

MyApp.prototype.loadForm = function (user, forAction) {
  var t = this;
  var el = t.userMdl.el;
  var tr = t.config.translations || {};

  t.resetUserModal();

  if (user.data.device_read === true) el.edit_mdl.removeClass("hide");

  t._loadImageViewer(user.data.avatar, forAction);

  t.userMdl.frm
    .find("input[name='first_name']")
    .val(user.data.first_name || "");
  t.userMdl.frm.find("input[name='last_name']").val(user.data.last_name || "");
  t.userMdl.frm
    .find("input[name='employee_num']")
    .val(user.data.employee_num || "");
  t.userMdl.frm.find("input[name='seat_no']").val(user.data.seat_no || "");
  t.userMdl.frm.find("input[name='jobtitle']").val(user.data.jobtitle || "");
  t.userMdl.frm
    .find("input[name='business_unit']")
    .val(user.data.business_unit || "");
  t.userMdl.frm
    .find("input[name='delivery_unit']")
    .val(user.data.delivery_unit || "");
  t.userMdl.frm.find("textarea[name='notes']").val(user.data.notes || "");

  if (typeof el.doj.datepicker === "function") {
    el.doj.datepicker("update", user.data.doj || "");
    el.last_working_date.datepicker(
      "update",
      user.data.last_working_date || "",
    );
  }

  if (forAction === "edit") {
    t.userMdl.frm.find("input[name='username']").val(user.data.username || "").prop("disabled", true);
    t.userMdl.frm.find("input[name='email']").val(user.data.email || "");
    t.userMdl.frm.find("input[name='phone']").val(user.data.phone || "");
    t.userMdl.frm.find("input[name='phone2']").val(user.data.phone2 || "");
    t.userMdl.frm.find("input[name='work_phone']").val(user.data.work_phone || "");
    t.userMdl.frm.find("textarea[name='address']").val(user.data.address || "");
  } else {
    t.userMdl.frm.find("input[name='username']").prop("disabled", false);
  }
  this.initPhoneDropdown(t.userMdl.el.phone_country_code, user.data.phone_country_id);
  this.initPhoneDropdown(t.userMdl.el.phone2_country_code, user.data.phone2_country_id);
  this.initPhoneDropdown(t.userMdl.el.work_phone_country_code, user.data.work_phone_country_id);

  t.syncPasswordRequirement();

  var jobTypeVal =
    !user.data.job_type || user.data.job_type < 1 ? 0 : user.data.job_type;
  el.job_type.val(jobTypeVal).trigger("change");
  if (jobTypeVal > 0) el.ex_user_company.val(user.data.ex_user_company || "");

  if (
    user.data.roles &&
    user.data.roles[0] &&
    user.data.roles[0].id == 4 &&
    user.data.roles[0].name.toLowerCase() === "user"
  ) {
    $("#userPermission").hide();
  } else {
    $("#userPermission").show();
  }

  t._loadRolesSelect();
  el.role.val(parseInt(user.data.role_id) || "").trigger("change");

  var baseCompany =
    user.dropdown && user.dropdown.base_company
      ? Array.isArray(user.dropdown.base_company)
        ? user.dropdown.base_company[0]
        : user.dropdown.base_company
      : null;
  if (baseCompany && baseCompany.id != null) {
    var bc = baseCompany;
    el.company
      .empty()
      .append(new Option(bc.text, bc.id, true, true))
      .trigger("change");
  }

  var userDepartment =
    user.dropdown && user.dropdown.user_department
      ? Array.isArray(user.dropdown.user_department)
        ? user.dropdown.user_department[0]
        : user.dropdown.user_department
      : null;
  if (userDepartment && userDepartment.id != null) {
    var ud = userDepartment;
    el.department
      .empty()
      .append(new Option(ud.text, ud.id, true, true))
      .trigger("change");
  }

  if (user.dropdown && user.dropdown.user_companies) {
    el.user_company_id.empty();
    $.each(user.dropdown.user_companies, function (i, v) {
      el.user_company_id.append(new Option(v.text, v.id, true, true));
    });
    el.user_company_id.trigger("change");
  }

  if (user.dropdown && user.dropdown.sevice_ticket_department) {
    el.ticket_department.empty();
    $.each(user.dropdown.sevice_ticket_department, function (i, v) {
      el.ticket_department.append(new Option(v.text, v.id, true, true));
    });
    el.ticket_department.trigger("change");
  }


  if (user.dropdown && user.dropdown.tkt_company_previledge) {
    el.technician_company_access.empty();
    $.each(user.dropdown.tkt_company_previledge, function (i, v) {
      el.technician_company_access.append(new Option(v.text, v.id, true, true));
    });
    el.technician_company_access.trigger("change");
  }

  if (
    el.asset_departments_id &&
    user.dropdown &&
    user.dropdown.assetDepartments
  ) {
    $.each(user.dropdown.assetDepartments, function (i, v) {
      el.asset_departments_id.append(new Option(v.text, v.id, true, true));
    });
    el.asset_departments_id.trigger("change");
  }

  if (
    user.dropdown &&
    user.dropdown.manager &&
    typeof user.dropdown.manager === "object"
  ) {
    el.manager
      .empty()
      .append(
        new Option(
          user.dropdown.manager.text,
          user.dropdown.manager.id,
          true,
          true,
        ),
      )
      .trigger("change");
  }

  if (
    user.dropdown &&
    user.dropdown.location &&
    typeof user.dropdown.location === "object"
  ) {
    if (user.dropdown.place && user.dropdown.place !== null) {
      t.internal_places = [user.dropdown.place.id];
    }
    el.location
      .empty()
      .append(
        new Option(
          user.dropdown.location.text,
          user.dropdown.location.id,
          true,
          true,
        ),
      )
      .trigger("change");
  }

  if (
    user.dropdown &&
    user.dropdown.base_location &&
    typeof user.dropdown.base_location === "object"
  ) {
    el.base_location
      .empty()
      .append(
        new Option(
          user.dropdown.base_location.text,
          user.dropdown.base_location.id,
          true,
          true,
        ),
      )
      .trigger("change");
  }

  if (user.data.request_approval_delegated_user) {
    var delegateTxt =
      (user.data.delegate_name || "") +
      " (" +
      (user.data.delegate_username || "") +
      ")";
    el.request_approval_delegated_user
      .append(
        new Option(
          delegateTxt,
          user.data.request_approval_delegated_user,
          true,
          true,
        ),
      )
      .trigger("change");
  }

  el.active_status.val(parseInt(user.data.activated)).trigger("change");

  t._loadGroupsOptions(user.groups);

  t.userMdl.frm
    .find("input[name='user_permission[admin]'][value=0]")
    .prop("checked", true);
  t.userMdl.frm
    .find("input[name='user_permission[service_tickets]'][value=-1]")
    .prop("checked", true);

  var stVal = user.permissions && user.permissions.service_tickets;
  if (stVal == 1) {
    $("#technicianCompanyAccess").show();
  } else {
    el.technician_company_access.empty().val(null).trigger("change");
    $("#technicianCompanyAccess").hide();
  }

  $.each(user.permissions || {}, function (k, v) {
    t.userMdl.frm
      .find("input[name='user_permission[" + k + "]'][value=" + v + "]")
      .prop("checked", true)
      .trigger("change");
  });

  if (user.data.is_vip_user == 1) {
    t.userMdl.frm.find("input[name='is_vip_user']").prop("checked", true);
  }
};

MyApp.prototype._loadImageViewer = function (img, forAction) {
  var t = this;
  var hasExistingAvatar =
    img !== null && img !== undefined && String(img).trim() !== "";

  if (t.userMdl.el && t.userMdl.el.avatar && t.userMdl.el.avatar.length) {
    t.userMdl.el.avatar.toggleClass(
      "d-none",
      !(forAction === "edit" && hasExistingAvatar),
    );
    t.userMdl.el.avatar.toggleClass(
      "d-flex",
      forAction === "edit" && hasExistingAvatar,
    );
  }

  if (
    t.userMdl.frm &&
    t.userMdl.frm.length &&
    !(forAction === "edit" && hasExistingAvatar)
  ) {
    t.userMdl.frm.find("#delete_img").prop("checked", false);
  }

  if (!t.userMdl.imgPreview) return;
  if (hasExistingAvatar && forAction === "edit") {
    t.userMdl.imgPreview.attr("src", (t.config.imgviewpath || "") + "/" + img);
    t.userMdl.imgWrapper.removeClass("hide");
  } else {
    t.userMdl.imgPreview.attr("src", "");
    t.userMdl.imgWrapper.addClass("hide");
  }
};

MyApp.prototype._userDropdownFormat = function (s) {
  if (!s) return $("<div>No data</div>");
  var name = this.safeDisplayValue(s.text, "-");
  var email = this.safeDisplayValue(s.email, "");
  var empNo = this.safeDisplayValue(s.employee_num, "");
  var tName = name.length > 35 ? name.substring(0, 35) + "..." : name;
  var tEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
  var imageUrl = this.safeDisplayValue(s.img_path, "");
  var avatarHtml = this.getAvatarHtml(name, imageUrl, "user-list-avatar");

  var html = [
    '<div class="d-flex align-items-start gap-3 w-100">',
    avatarHtml,
    '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
    '<div class="d-flex align-items-center gap-2">',
    '<span class="b2-text">' + this.escapeHtml(tName) + "</span>",
    s.status == 1
      ? '<span class="active-user"></span>'
      : '<span class="inactive-user"></span>',
    "</div>",
    email
      ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' +
      this.escapeHtml(tEmail) +
      "</span>"
      : "",
    empNo
      ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' +
      this.escapeHtml(empNo) +
      "</span>"
      : "",
    "</div>",
    "</div>",
  ].join("");
  return $(html);
};

MyApp.prototype.handleSubmit = function (e) {
  if (e) e.preventDefault();
  var t = this;
  var tr = t.config.translations || {};
  var isEditAction = !!t.currentEditingUserId;

  if (t.userMdl.frmValidator && t.userMdl.frmValidator.form() === false)
    return false;
  if (!t.httpCall) return false;

  t.httpCall = false;
  t.userMdl.btnSubmit.prop("disabled", true);
  t.userMdl.loader.show();

  var formData = new FormData(t.userMdl.frm[0]);
  formData.set("_token", t.getCsrfToken());

  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
  })
    .done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          t.showToast(
            "success",
            data.msg ||
            (isEditAction ? tr.edit_user_success : tr.add_user_success) ||
            "Saved.",
          );
          t.toggleModal("#user-mdl", false);
          t.reload();
        } else {
          t.showToast(
            "error",
            data.msg ||
            (isEditAction ? tr.edit_user_error : tr.add_user_error) ||
            tr.something_went_wrong ||
            "Something went wrong.",
          );
        }
      } else {
        t.showToast(
          "error",
          (isEditAction ? tr.edit_user_error : tr.add_user_error) ||
          tr.something_went_wrong ||
          "Something went wrong.",
        );
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.httpCall = true;
      t.userMdl.btnSubmit.prop("disabled", false);
      t.userMdl.loader.hide();
    });
};

MyApp.prototype.addUser = function () {
  var t = this;
  var tr = t.config.translations || {};

  t.initUserModal();
  t.initUserModalSelects();

  t.currentEditingUserId = null;
  t.httpPostPath = t.config.url.add;

  t.resetUserModal();

  var el = t.userMdl.el;
  var frm = t.userMdl.frm;
  var authParentCompany = t.config.authParentCompany || null;
  var defaultCompanyId =
    authParentCompany && authParentCompany.id
      ? authParentCompany.id
      : $("#config-company").val();
  var defaultCompanyText =
    authParentCompany && authParentCompany.text
      ? authParentCompany.text
      : $("#config-company option:selected").text();

  if (defaultCompanyId && defaultCompanyId != 0) {
    el.company
      .empty()
      .append(new Option(defaultCompanyText, defaultCompanyId, true, true))
      .trigger("change");
    el.user_company_id
      .empty()
      .append(new Option(defaultCompanyText, defaultCompanyId, true, true))
      .trigger("change");
    el.technician_company_access
      .empty()
      .append(new Option(defaultCompanyText, defaultCompanyId, true, true))
      .trigger("change");
  }

  t._loadGroupsOptions();
  t._loadRolesSelect();
  t.userMdl.frm.find("input[name='username']").prop("disabled", false);
  frm
    .find("input[name='user_permission[superuser]'][value=-1]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[admin]'][value=0]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[reports]'][value=0]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[license_keys]'][value=0]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[service_tickets]'][value=0]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[status_board]'][value=0]")
    .prop("checked", true);
  frm
    .find("input[name='user_permission[rdp]'][value=-1]")
    .prop("checked", true);
  frm.find("input[name='user_permission[kd]'][value=-1]").prop("checked", true);

  t.userMdl.mdl.find(".modal-title").text(tr.add_user || "Add User");
  t.userMdl.btnSubmit.text(tr.create_user || "Create User");
  el.job_type.val("0").trigger("change");
  el.active_status.val("1").trigger("change");

  t.syncPasswordRequirement();
  if (t.userMdl.frmValidator) t.userMdl.frmValidator.resetForm();

  t.toggleModal("#user-mdl", true);
  $('#technician_company_access').on('select2:unselecting', function (e) {
    let parentCompanyId = $('#company_id').val();
    if (e.params.args.data.id == parentCompanyId) {
        Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        }).fire({
          icon: "error",
          title: "Can't remove the parent company"
        });
        e.preventDefault();
      }

    });
};

MyApp.prototype.editUser = function (userId) {
  var t = this;
  var tr = t.config.translations || {};
  t.initUserModal();
  t.initUserModalSelects();

  t.currentEditingUserId = userId;
  t.httpPostPath = t.config.url.edit + "/" + userId;
  t.userMdl.loader.show();

  $.get(t.config.url.getUser + "?id=" + userId)
    .done(function (data) {
      if (typeof data === "object" && data.status === "success") {
        t.userMdl.mdl.find(".modal-title").text(tr.edit_user || "Edit User");
        t.userMdl.btnSubmit.text(tr.save_changes || "Save Changes");
        t.loadForm(data.user, "edit");
        t.toggleModal("#user-mdl", true);
      } else {
        t.showToast(
          "error",
          data && data.msg
            ? data.msg
            : tr.load_user_error || "Failed to load user.",
        );
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.userMdl.loader.hide();
    });
    
    
    // Prevent removing parent company

  el.technician_company_access.on('select2:unselecting', function (e) {
    let parentCompanyId = el.company.val();
    if (e.params.args.data.id == parentCompanyId) {
        Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        }).fire({
          icon: "error",
          title: "Can't remove the parent company"
        });
        e.preventDefault();
      }

    });

};

MyApp.prototype.cloneUser = function (userId) {
  var t = this;
  var tr = t.config.translations || {};

  t.initUserModal();
  t.initUserModalSelects();

  t.currentEditingUserId = null;
  t.httpPostPath = t.config.url.add;

  t.userMdl.loader.show();

  $.get(t.config.url.getUser + "?id=" + userId)
    .done(function (data) {
      if (typeof data === "object" && data.status === "success") {
        t.userMdl.mdl.find(".modal-title").text(tr.clone_user || "Clone User");
        t.userMdl.btnSubmit.text(tr.create_user || "Create User");
        t.loadForm(data.user, "clone");
        t.toggleModal("#user-mdl", true);
      } else {
        t.showToast(
          "error",
          data && data.msg
            ? data.msg
            : tr.load_user_error || "Failed to load user.",
        );
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.userMdl.loader.hide();
    });
};

MyApp.prototype.deleteUser = function (userId) {
  var t = this;
  var tr = t.config.translations || {};
  t.confirmAndPost(
    tr.delete_user_confirm || "Are you sure you want to delete this user?",
    t.config.url.delete + "/" + userId,
    { _token: t.getCsrfToken() },
    {
      successMessage: tr.delete_user_success || "User deleted successfully.",
      errorMessage:
        tr.delete_user_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

MyApp.prototype.restoreUser = function (userId) {
  var t = this;
  var tr = t.config.translations || {};
  $.get(t.config.url.restore + "/" + userId)
    .done(function (data) {
      if (typeof data === "object" && data.status === "success") {
        t.showToast(
          "success",
          data.msg || tr.restore_user_success || "Restored.",
        );
        t.reload();
      } else {
        t.showToast(
          "error",
          data && data.msg
            ? data.msg
            : tr.restore_user_error || "Restore failed.",
        );
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    });
};

MyApp.prototype.sendCredential = function (userId) {
  var t = this;
  var tr = t.config.translations || {};
  var confirmText =
    tr.send_credential_confirm || "Are you sure you want to send credentials?";
  t.confirmAndPost(
    confirmText,
    t.config.url.sendCredential + "/" + userId,
    { _token: t.getCsrfToken() },
    {
      successMessage:
        tr.send_credential_success || "Credentials sent successfully.",
      errorMessage:
        tr.send_credential_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

MyApp.prototype.viewUser = function (userId) {
  window.open(this.config.url.user_info + "/" + userId, "_blank");
};

MyApp.prototype.getSelectedUserIds = function () {
  return Object.keys(this.selectedIds);
};

MyApp.prototype.hideTooltip = function (selector) {
  const btn = document.querySelector(selector);
  const tooltip = bootstrap.Tooltip.getInstance(btn);
  $(btn).blur();
  if (tooltip) {
    tooltip.hide();
  }
  $(".tooltip").remove();
};

MyApp.prototype.hideTooltip = function (selector) {
  const btn = document.querySelector(selector);
  const tooltip = bootstrap.Tooltip.getInstance(btn);
  $(btn).blur();
  if (tooltip) {
    tooltip.hide();
  }
  $(".tooltip").remove();
};

MyApp.prototype.bulkUserActivate = function () {
  this.hideTooltip(".btna-active");
  var t = this,
    selected = t.getSelectedUserIds(),
    tr = t.config.translations || {};
  if (!selected.length) {
    t.showToast("error", tr.select_users_error || "Please select users.");
    return;
  }
  if (!t.httpCall) return;
  t.confirmAndPost(
    tr.activate_users_confirm || "Activate selected users?",
    t.config.url.bulkUserActivate,
    { token: t.config.token, id: selected },
    {
      successMessage:
        tr.activate_users_success || "Selected users activated successfully.",
      errorMessage:
        tr.activate_users_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

MyApp.prototype.bulkUserDeactivate = function () {
  this.hideTooltip(".btnd-inactive");
  var t = this,
    selected = t.getSelectedUserIds(),
    tr = t.config.translations || {};
  if (!selected.length) {
    t.showToast("error", tr.select_users_error || "Please select users.");
    return;
  }
  if (!t.httpCall) return;
  t.confirmAndPost(
    tr.deactivate_users_confirm || "Deactivate selected users?",
    t.config.url.bulkUserDeactivate,
    { token: t.config.token, id: selected },
    {
      successMessage:
        tr.deactivate_users_success ||
        "Selected users deactivated successfully.",
      errorMessage:
        tr.deactivate_users_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

MyApp.prototype.bulkUserDelete = function () {
  var t = this,
    selected = t.getSelectedUserIds(),
    tr = t.config.translations || {};
  if (!selected.length) {
    t.showToast("error", tr.select_users_error || "Please select users.");
    return;
  }
  t.confirmAndPost(
    tr.delete_users_confirm || "Delete selected users?",
    t.config.url.bulk_delete,
    { token: t.config.token, id: selected },
    {
      successMessage: tr.delete_user_success || "User deleted successfully.",
      errorMessage:
        tr.delete_user_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

MyApp.prototype.toggleDeletedUsers = function () {
  var t = this,
    tr = t.config.translations || {};
  t.showDeletedUsers = !t.showDeletedUsers;
  var $deletedBtn = t.content.find(t.btn.showusers).not(t.btn.nonDeleted);
  var $nonDeletedBtn = t.content.find(t.btn.nonDeleted);
  $deletedBtn.toggleClass("d-none", t.showDeletedUsers);
  $nonDeletedBtn.toggleClass("d-none", !t.showDeletedUsers);
  var title = t.showDeletedUsers
    ? tr.toolbar_show_non_deleted_users || "Show Non-Deleted Users"
    : tr.toolbar_show_deleted_users || "Show Deleted Users";
  $deletedBtn.add($nonDeletedBtn).attr("title", title);
  t.reload();
};

MyApp.prototype.exportList = function () {
  this.cacheFilterValues();
  window.location =
    this.config.url.download_url +
    "?q=" +
    (this.config.export_filters || "") +
    "&main_filter=" +
    (this.config.main_filter || "") +
    "&role=" +
    (this.config.role || "") +
    "&showDeletedUsers=" + (this.showDeletedUsers ? "true" : "false");
};

MyApp.prototype.exportListPdf = function () {
  this.cacheFilterValues();
  window.location =
    this.config.url.download_url_pdf +
    "?q=" +
    (this.config.export_filters || "") +
    "&showDeletedUsers=" + (this.showDeletedUsers ? "true" : "false");
};

MyApp.prototype.importUsers = function () {
  if (this.config.url.import) {
    window.location = this.config.url.import;
    return;
  }
  this.openStubModal("Import Users", "Import route missing in config.");
};

MyApp.prototype.updateUsers = function () {
  if (this.config.url.bulk_update) {
    window.location = this.config.url.bulk_update;
    return;
  }
  this.openStubModal("Bulk Update", "Bulk update route missing in config.");
};

MyApp.prototype.openUsersLog = function () {
  if (this.config.url.users_log) {
    window.location = this.config.url.users_log;
    return;
  }
  this.openStubModal("Users Log", "Users log route missing in config.");
};

MyApp.prototype.userSyncAzure = function () {
  var t = this,
    tr = t.config.translations || {};
  if (!t.config.url.userSyncAzure) {
    t.openStubModal("Azure Sync", "Azure sync route missing.");
    return;
  }
  if (!t.httpCall) return;
  t.httpCall = false;
  $.ajax({
    url: t.config.url.userSyncAzure,
    type: "POST",
    data: { _token: t.getCsrfToken() },
  })
    .done(function (data) {
      t.showToast(
        "success",
        data && data.msg
          ? data.msg
          : tr.sync_triggered_success || "Sync triggered.",
      );
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.httpCall = true;
    });
};

MyApp.prototype.initPrintModal = function () {
  this.print = {
    mdl: $("#printModal"),
    form: $("#print-modal-form"),
    select: $("#print_opt"),
    starter: $("#print_label_starter"),
  };
};

MyApp.prototype.printLabelModal = function () {
  var t = this,
    tr = t.config.translations || {};
  var selected = t.getSelectedUserIds();
  if (!selected.length) {
    t.showToast(
      "error",
      tr.select_at_least_one_user || "Please select at least one user.",
    );
    return;
  }
  if (t.print && t.print.select && t.print.select.length)
    t.print.select.val("1");
  t.toggleModal("#printModal", true);
};

MyApp.prototype.submitPrintLabel = function () {
  var t = this,
    tr = t.config.translations || {};
  var selected = t.getSelectedUserIds();
  if (!selected.length) {
    t.showToast(
      "error",
      tr.select_at_least_one_user || "Please select at least one user.",
    );
    return;
  }

  var opt = String(t.print.select ? t.print.select.val() || "" : "");
  var urlMap = {
    1: t.config.url.print_user_barcode,
    2: t.config.url.print_user_onecol,
    3: t.config.url.print_user_twocol,
    4: t.config.url.print_user_verticalcol,
  };
  var url = urlMap[opt];

  if (!url) {
    t.showToast("error", tr.something_went_wrong || "Print option missing.");
    return;
  }
  if (t.print.starter && t.print.starter.length) {
    t.print.starter
      .attr("href", url + "/" + btoa(selected.join(",")))
      .get(0)
      .click();
  }
  t.toggleModal("#printModal", false);
};

MyApp.prototype.initNotifyModal = function () {
  var t = this;
  var tr = t.config.translations || {};

  t.mdlNotify = $("#announcementMdl");
  if (!t.mdlNotify.length) return;

  t.loader = t.mdlNotify.find("#loader_img");
  t.frmNotify = t.mdlNotify.find("#announcementForm");

  t.frmNotify.el = {
    title: t.frmNotify.find("#title"),
    announcement: t.frmNotify.find("#content"),
    start_date: t.frmNotify.find("#start_date"),
    end_date: t.frmNotify.find("#end_date"),
    users: t.frmNotify.find("#users"),
    status: t.frmNotify.find("#status_id"),
    all_user_check: t.frmNotify.find("#all_user_check"),
  };

  t.btnNotify = {
    submit: t.frmNotify.find("#btnSubmit"),
    update: t.frmNotify.find("#btnupdate"),
    clear: t.frmNotify.find("#btnClear"),
  };

  if (
    t.frmNotify.el.announcement.length &&
    typeof $.fn.summernote !== "undefined"
  ) {

    t.frmNotify.el.announcement.summernote({
      height: 200,
      width: "100%",
      placeholder: "Enter announcement...",
      toolbar: [
        ["style", ["bold", "italic", "underline"]],
        ["color", ["color"]],
        ["para", ["ul", "ol"]],
      ],
      dropdownParent: t.mdl,
    });
  }

  if (t.frmNotify.el.start_date.length && typeof flatpickr !== "undefined") {
    var now = new Date();
    t.frmNotify.el.start_date.flatpickr({
      dateFormat: "d/m/Y H:i",
      enableTime: true,
      time_24hr: true,
      step: 30,
      minDate: now,
      allowInput: true,
      onChange: function (selectedDates, dateStr, instance) {
        instance.close();
      },
    });
    t.frmNotify.el.end_date.flatpickr({
      dateFormat: "d/m/Y H:i",
      time_24hr: true,
      enableTime: true,
      step: 30,
      minDate: now,
      allowInput: true,
      onChange: function (selectedDates, dateStr, instance) {
        instance.close();
      },
    });
  }

  if (typeof $.fn.select2 !== "undefined") {
    t.frmNotify.el.status.select2({
      width: "100%",
      dropdownParent: t.frmNotify.el.status.parent(),
    });
    t.frmNotify.el.users.select2({ width: "100%" });
  }

  if (typeof t.frmNotify.validate !== "undefined") {
    t.frmNotifyValidator = t.frmNotify.validate({
      ignore: [],
      debug: false,
      rules: {
        title: { required: true },
        content: { required: true },
        start_date: { required: true },
        end_date: { required: true },
      },
      errorPlacement: function (error, element) {
        var errorWrap = t.getUserModalErrorWrap(element);

        if (errorWrap.length) {
          error.appendTo(errorWrap);
        } else if (element.closest(".input-group").length) {
          error.insertAfter(element.closest(".input-group"));
        } else {
          error.appendTo(element.closest("div"));
        }

        t.updateUserModalValidationState(element, true);
      },
      highlight: function (element) {
        t.updateUserModalValidationState($(element), true);
      },
      unhighlight: function (element) {
        t.updateUserModalValidationState($(element), false);
      },
    });
  }

  t.btnNotify.submit.on("click", $.proxy(t.submitAnnouncement, t));

  t.mdlNotify.on("hidden.bs.modal", function () {
    if (t.isBulkAddMode && t.mergeObj) {
      t.selectedUserIds = [];
      t.mergeObj.clearList();
      t.reload();
    }
  });
};

MyApp.prototype.resetNotifyForm = function () {
  var t = this;
  if (!t.frmNotify) return;
  t.frmNotify.find("input[name='title']").val("");
  if (
    t.frmNotify.el.announcement.length &&
    typeof $.fn.summernote !== "undefined"
  ) {
    t.frmNotify.el.announcement.summernote("code", "");
  }
  t.frmNotify.el.start_date.val("");
  t.frmNotify.el.end_date.val("");
  t.frmNotify.el.status.val("").trigger("change");
  if (t.frmNotifyValidator) t.frmNotifyValidator.resetForm();
  t.frmNotify.find(".amg-form-invalid").removeClass("amg-form-invalid");
  t.frmNotify
    .find(".amg-form-select-error")
    .removeClass("amg-form-select-error");
  t.frmNotify.find(".error").removeClass("error");
  t.frmNotify.find(".amg-form-error-wrap").empty();
};

MyApp.prototype._fillNotifyDates = function () {
  var t = this;
  var now = new Date();
  var eod = new Date(now);
  eod.setHours(23, 59, 59);
  t.frmNotify.el.start_date.val(t._formatDatetime(now));
  t.frmNotify.el.end_date.val(t._formatDatetime(eod));
};

MyApp.prototype._formatDatetime = function (d) {
  var z = function (n) {
    return n < 10 ? "0" + n : n;
  };
  return (
    z(d.getDate()) +
    "/" +
    z(d.getMonth() + 1) +
    "/" +
    d.getFullYear() +
    " " +
    z(d.getHours()) +
    ":" +
    z(d.getMinutes())
  );
};

MyApp.prototype._formatNotifyDatetimeForSubmit = function (value) {
  if (!value) return value;

  value = $.trim(value);

  return /^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}$/.test(value)
    ? value + ":00"
    : value;
};

MyApp.prototype.getUserForAnnouncement = function (e) {
  if (e) e.preventDefault();
  var t = this;
  var userId = $(e.currentTarget).attr("data-id");
  if (!t.mdlNotify || !t.mdlNotify.length) return;

  t.resetNotifyForm();
  t.isBulkAddMode = false;
  t.frmNotify.el.users.empty();

  $.get(t.config.url.getUser + "?id=" + userId, function (data) {
    if (data && data.user && data.user.data) {
      if (data.user.data.activated === 0) {
        t.showToast(
          "error",
          (t.config.translations || {}).select_active_user ||
          "Please select an active user.",
        );
        return;
      }
      var info =
        data.user.data.first_name +
        " " +
        data.user.data.last_name +
        " (" +
        data.user.data.username +
        ")";
      t.frmNotify.el.users
        .append(new Option(info, userId, true, true))
        .trigger("change");
      t.frmNotify.el.users.prop("disabled", true);
      t._fillNotifyDates();
      t.frmNotify.el.status.val("1").trigger("change");
      t.toggleModal("#announcementMdl", true);
    }
  });
};

MyApp.prototype.addBulkUsersForAnnouncement = function (e) {
  if (e) e.preventDefault();
  var t = this;
  var selected = t.getSelectedUserIds().concat(t.selectedUserIds);
  selected = selected.filter(function (v, i, a) {
    return a.indexOf(v) === i;
  });

  if (!selected.length) {
    t.showToast(
      "error",
      (t.config.translations || {}).select_users_error ||
      "Please select users.",
    );
    return;
  }
  if (!t.mdlNotify || !t.mdlNotify.length) return;

  t.resetNotifyForm();
  t.isBulkAddMode = true;
  var userInfoArray = [];

  var requests = selected.map(function (userId) {
    return $.get(t.config.url.getUser + "?id=" + userId, function (data) {
      if (
        data &&
        data.user &&
        data.user.data &&
        data.user.data.activated !== 0
      ) {
        var info =
          data.user.data.first_name +
          " " +
          data.user.data.last_name +
          " (" +
          data.user.data.username +
          ")";
        var exists = userInfoArray.some(function (u) {
          return u.id === userId;
        });
        if (!exists) userInfoArray.push({ id: userId, info: info });
      }
    });
  });

  $.when.apply($, requests).done(function () {
    if (!userInfoArray.length) {
      t.showToast(
        "error",
        (t.config.translations || {}).select_active_user ||
        "Please select active users.",
      );
      return;
    }
    t.frmNotify.el.users.empty();
    $.each(userInfoArray, function (i, u) {
      t.frmNotify.el.users
        .append(new Option(u.info, u.id, true, true))
        .trigger("change");
    });
    t.frmNotify.el.users.prop("disabled", true);
    t._fillNotifyDates();
    t.frmNotify.el.status.val("1").trigger("change");
    t.toggleModal("#announcementMdl", true);
  });
};

MyApp.prototype.submitAnnouncement = function (e) {
  if (e) e.preventDefault();
  var t = this;
  if (t.frmNotifyValidator && t.frmNotifyValidator.form() === false) return;

  var startStr = t.frmNotify.find("[name='start_date']").val();
  var endStr = t.frmNotify.find("[name='end_date']").val();
  var toDate = function (s) {
    var p = s.split(/[\s/:]/);
    return new Date(p[2], p[1] - 1, p[0], p[3], p[4], p[5] || 0);
  };
  if (toDate(endStr) < toDate(startStr)) {
    t.showToast(
      "error",
      (t.config.translations || {}).end_date_invalid ||
      "End date cannot be earlier than start date.",
    );
    return;
  }

  var formObj = {};
  $.each(t.frmNotify.serializeArray(), function (i, f) {
    formObj[f.name] = f.value;
  });
  formObj.start_date = t._formatNotifyDatetimeForSubmit(formObj.start_date);
  formObj.end_date = t._formatNotifyDatetimeForSubmit(formObj.end_date);
  var users = [];
  t.frmNotify.el.users.find("option").each(function () {
    users.push($(this).val());
  });
  formObj.users = JSON.stringify(users);

  t.btnNotify.submit.attr("disabled", true);
  $.ajax({
    url: t.config.userNotificationForAnnouncement,
    method: "POST",
    data: $.param(formObj),
    _token: t.getCsrfToken(),
  })
    .done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          t.showToast(
            "success",
            data.msg ||
            (t.config.translations || {}).announcement_sent_success ||
            "Sent.",
          );
          t.toggleModal("#announcementMdl", false);
          t.reload();
        } else {
          t.showToast(
            "error",
            data.msg ||
            (t.config.translations || {}).announcement_sent_error ||
            (t.config.translations || {}).something_went_wrong ||
            "Something went wrong.",
          );
        }
      }
    })
    .fail(function () {
      t.showToast(
        "error",
        (t.config.translations || {}).something_went_wrong ||
        "Something went wrong.",
      );
    })
    .always(function () {
      if (t.loader) t.loader.hide();
      t.btnNotify.submit.attr("disabled", false);
    });
};

MyApp.prototype.initMerge = function () {
  var t = this;
  if (typeof MergeMdl === "function") {
    t.mergeObj = new MergeMdl(t.config);
    t.mergeObj.eventDispatcher.on("merge_happend", function () {
      t.reload();
    });
  }
};

MyApp.prototype.addToMerge = function (e) {
  if (e) e.preventDefault();
  var t = this;
  var userId = $(e.currentTarget).attr("data-id");
  if (!t.mergeObj) return;

  var found = false;
  t.dTable.rows().every(function () {
    var row = this.data();
    if (row && row.a && String(row.a.id) === String(userId)) {
      t.mergeObj.addToMerge(row.a);
      found = true;
      if (t.selectedUserIds.indexOf(userId) === -1)
        t.selectedUserIds.push(userId);
      return false;
    }
  });
  if (!found)
    t.showToast(
      "error",
      (t.config.translations || {}).unable_to_add_user_merge ||
      "Unable to add user to merge.",
    );
};

MyApp.prototype.reload = function () {
  if (!this.dTable) return;
  this.dTable.search(this.config.search || "").draw();
};

MyApp.prototype.bindCheckboxEvents = function () {
  var self = this;
  var table = this.dTable;

  $("#select-all").off("click").on("click", function () {
    var checked = this.checked;
    var rows = table.rows({ search: "applied" }).nodes();

    $("input.row-checkbox", rows).each(function () {
      $(this).prop("checked", checked);

      var id = $(this).val();

      if (checked) {
        self.selectedIds[id] = true;
      } else {
        delete self.selectedIds[id];
      }
    });
  });

  $(document).off("change.rowCheckbox").on("change.rowCheckbox", ".row-checkbox", function () {
    var id = $(this).val();

    if ($(this).is(":checked")) {
      self.selectedIds[id] = true;
    } else {
      delete self.selectedIds[id];
    }
  });
};

MyApp.prototype.confirmAndPost = function (confirmText, url, payload, options) {
  var t = this;
  var tr = (t.config && t.config.translations) || {};
  options = options || {};
  payload = payload || {};
  payload._token =
    t.config.token || $('meta[name="csrf-token"]').attr("content");

  var doPost = function () {
    $.ajax({
      url: url,
      type: "POST",
      data: payload,
      headers: { "X-CSRF-TOKEN": payload._token },
    })
      .done(function (res) {
        var ok =
          res && (String(res.status) === "success" || res.status === true);
        var msg =
          res && res.msg
            ? res.msg
            : ok
              ? options.successMessage || tr.success_title || "Success"
              : options.errorMessage ||
              tr.something_went_wrong ||
              "Something went wrong.";
        t.showToast(ok ? "success" : "error", msg);
        if (ok) t.reload();
      })
      .fail(function () {
        t.showToast(
          "error",
          options.errorMessage ||
          tr.something_went_wrong ||
          "Something went wrong.",
        );
      });
  };

  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: "warning",
      title: options.confirmTitle || tr.confirm_title || "Confirm",
      text: confirmText,
      showCancelButton: true,
      confirmButtonText: options.confirmButtonText || tr.confirm_yes || "Yes",
      cancelButtonText: options.cancelButtonText || tr.cancel || "Cancel",
      confirmButtonColor: options.confirmButtonColor || "#001B51", 
      cancelButtonColor: options.cancelButtonColor || "#F12F35", 
    }).then(function (result) {
      if (result.isConfirmed) doPost();
    });
  } else {
    if (confirm(confirmText)) doPost();
  }
};

MyApp.prototype.getCsrfToken = function () {
  if (this.config && this.config.token) return this.config.token;
  var meta = $('meta[name="csrf-token"]');
  return meta.length ? meta.attr("content") : "";
};

MyApp.prototype.showToast = function (icon, message) {
  var msg = this.resolveMessage(message);
  var tr = (this.config && this.config.translations) || {};
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: icon,
      title:
        icon === "success"
          ? tr.success_title || "Success"
          : tr.error_title || "Error",
      text: msg,
      showConfirmButton: true,
      allowOutsideClick: false,
    });
  } else if (typeof sweetAlert === "function") {
    sweetAlert("center", icon === "success" ? "success" : "error", {
      msg: msg,
    });
  } else {
    alert(msg);
  }
};

MyApp.prototype.resolveMessage = function (message) {
  if (typeof message !== "string") return message;
  var tr = (this.config && this.config.translations) || {};
  if (tr[message]) return tr[message];
  return message;
};

MyApp.prototype.openStubModal = function (title, message) {
  var modal = $("#actionStubModal");
  if (!modal.length) {
    alert(message);
    return;
  }
  modal.find(".modal-title").text(title);
  modal.find(".stub-message").text(message);
  this.toggleModal("#actionStubModal", true);
};

MyApp.prototype.openFilterModal = function () {
  this.toggleModal("#advanceFilterModal", true);
};
MyApp.prototype.closeFilterModal = function () {
  this.toggleModal("#advanceFilterModal", false);
};
MyApp.prototype.focusListSearch = function () {
  var input =
    this.listUi && this.listUi.searchInput
      ? this.listUi.searchInput
      : this.content.find(".user-list-search");

  if (!input.length) return;

  input.trigger("focus");
  input.get(0).select();
};

MyApp.prototype.showActionMenu = function (dropdown) {
  var actionCell;
  var actionRow;
  var menu;

  dropdown = $(dropdown);
  if (!dropdown.length) return;

  actionCell = dropdown.closest("td, th");
  actionRow = dropdown.closest("tr");
  menu = dropdown.find('.action-dropdown-menu');
  row = dropdown.closest('tr');
  totalRows = row.parent().children('tr:visible').length;
  rowIndex = row.index();

  this.clearActionMenuHideTimer();
  this.hideActionMenus(dropdown);
  actionCell.addClass("is-action-menu-open");
  actionRow.addClass("is-action-menu-open");
  dropdown.addClass("is-hover-open");
  dropdown.find(".user-list-menu-toggle").addClass("show").attr("aria-expanded", "true");
  dropdown.find(".action-dropdown-menu").addClass("show");
  menu.removeClass('dropdown-left-up');

  if (rowIndex >= totalRows - 2) {
    menu.addClass('dropdown-left-up');
  }
};

MyApp.prototype.hideActionMenu = function (dropdown) {
  var actionCell;
  var actionRow;
  dropdown = $(dropdown);
  if (!dropdown.length) return;
  actionCell = dropdown.closest("td, th");
  actionRow = dropdown.closest("tr");
  actionCell.removeClass("is-action-menu-open");
  actionRow.removeClass("is-action-menu-open");
  dropdown.removeClass("is-hover-open");
  dropdown
    .find(".user-list-menu-toggle")
    .removeClass("show")
    .attr("aria-expanded", "false");
  dropdown.find(".action-dropdown-menu").removeClass("show");
};

$('#employment-organization, #location-contact, #access-permission').on('shown.bs.collapse hidden.bs.collapse', function () {
  var targetId = '#' + this.id;
  var trigger = $('[data-bs-target="' + targetId + '"]');
  if ($(this).hasClass('show')) {
    trigger.find('svg').replaceWith(`<svg width="17" height="9" viewBox="0 0 17 9" fill="none" class="mt-2"><path d="M0.7188 7.71937L8.21878 0.219372C8.28844 0.149642 8.37115 0.0943171 8.4622 0.0565814C8.55325 0.0188457 8.65085 -0.000593441 8.74941 -0.000593433C8.84797 -0.000593424 8.94557 0.0188457 9.03661 0.0565815C9.12766 0.0943173 9.21038 0.149643 9.28003 0.219373L16.78 7.71937C16.885 7.82427 16.9566 7.95796 16.9856 8.10352C17.0145 8.24908 16.9997 8.39998 16.9429 8.53709C16.8861 8.67421 16.7898 8.79139 16.6664 8.87379C16.543 8.95619 16.3978 9.00012 16.2494 9L1.24941 9C1.10097 9.00012 0.955851 8.95619 0.832406 8.87379C0.708961 8.79139 0.612741 8.67421 0.555926 8.53709C0.499112 8.39998 0.513965 8.24908 0.542953 8.10352C0.571942 7.95796 0.643468 7.82427 0.7188 7.71937Z" fill="#7F7F7F"></path></svg>`);
  } else {
    trigger.find('svg').replaceWith(`<svg width="17" height="9" viewBox="0 0 17 9" fill="none" class="mt-2"><path d="M16.2812 1.28063L8.78122 8.78063C8.71156 8.85036 8.62885 8.90568 8.5378 8.94342C8.44675 8.98116 8.34915 9.00059 8.25059 9.00059C8.15203 9.00059 8.05443 8.98116 7.96339 8.94342C7.87234 8.90568 7.78962 8.85036 7.71997 8.78063L0.219966 1.28063C0.114957 1.17573 0.0434315 1.04204 0.0144437 0.89648C-0.0145441 0.750917 0.00030937 0.600025 0.0571237 0.462907C0.113938 0.32579 0.210159 0.208613 0.333604 0.12621C0.457049 0.0438069 0.602169 -0.000116571 0.750591 2.3235e-07L15.7506 2.3235e-07C15.899 -0.000116571 16.0441 0.0438069 16.1676 0.12621C16.291 0.208613 16.3872 0.32579 16.4441 0.462907C16.5009 0.600025 16.5157 0.750917 16.4867 0.89648C16.4578 1.04204 16.3862 1.17573 16.2812 1.28063Z" fill="#7F7F7F"></path></svg>`);
  }
});

MyApp.prototype.scheduleActionMenuHide = function (dropdown) {
  var t = this;

  dropdown = $(dropdown);
  if (!dropdown.length) return;

  t.clearActionMenuHideTimer();
  t.actionMenuHideTimer = window.setTimeout(function () {
    t.hideActionMenu(dropdown);
  }, 180);
};

MyApp.prototype.clearActionMenuHideTimer = function () {
  if (!this.actionMenuHideTimer) return;

  window.clearTimeout(this.actionMenuHideTimer);
  this.actionMenuHideTimer = null;
};

MyApp.prototype.hideActionMenus = function (exceptDropdown) {
  var t = this;
  var exceptElement =
    exceptDropdown && $(exceptDropdown).length
      ? $(exceptDropdown).get(0)
      : null;

  t.content
    .find(".user-list-actions .dropdown.is-hover-open")
    .each(function () {
      if (exceptElement && this === exceptElement) return;
      t.hideActionMenu($(this));
    });
};

MyApp.prototype.scheduleTableLayoutSync = function () {
  var t = this;

  window.setTimeout(function () {
    if (!t.dTable || (t.listUi && t.listUi.activeView === "card")) return;

    try {
      t.dTable.columns.adjust();

      if (typeof t.dTable.fixedColumns === "function") {
        var fixedColumnsApi = t.dTable.fixedColumns();

        if (fixedColumnsApi && typeof fixedColumnsApi.relayout === "function") {
          fixedColumnsApi.relayout();
        } else if (
          fixedColumnsApi &&
          typeof fixedColumnsApi.update === "function"
        ) {
          fixedColumnsApi.update();
        }
      }
    } catch (err) {
      console.warn("User list table relayout skipped:", err);
    }
  }, 0);
};

MyApp.prototype.toggleModal = function (selector, show) {
  var modal = $(selector);
  if (!modal.length) return;
  if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
    window.bootstrap.Modal.getOrCreateInstance(modal[0])[
      show ? "show" : "hide"
    ]();
  } else if (typeof modal.modal === "function") {
    modal.modal(show ? "show" : "hide");
  }
};


var MergeMdl = function (config) {
  var t = this;
  t.config = config;
  t.page = $("#page_boxed");
  t.mdl = t.page.length ? t.page.find("#mergeMdl") : $();
  if (!t.mdl.length) t.mdl = $("#mergeMdl");
  t.temp = {};

  if (typeof Handlebars !== "undefined" && Handlebars.templates) {
    t.temp.primary = Handlebars.templates["primary.hbs"];
    t.temp.others = Handlebars.templates["others.hbs"];
  }

  t.ui = {
    primary_ticket: t.mdl.find(".primary_ticket"),
    others_ul: t.mdl.find(".others ul"),
    no_ticket: t.mdl.find(".no_ticket"),
    comments: t.mdl.find("#comments"),
    count_shower: t.mdl.find(".count_shower"),
  };

  t.eventDispatcher = $({});
  t.httpCall = true;

  t.toggleModal = function (show) {
    if (!t.mdl.length) return;

    if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
      window.bootstrap.Modal.getOrCreateInstance(t.mdl[0])[
        show ? "show" : "hide"
      ]();
    } else if (typeof t.mdl.modal === "function") {
      t.mdl.modal(show ? "show" : "hide");
    }
  };

  t.merge_items = {
    department_id: null,
    primary: null,
    others: [],
    get_length: function () {
      var len = this.primary != null ? 1 : 0;
      if (Array.isArray(this.others)) len += this.others.length;
      return len;
    },
    canAddItem: function (id) {
      if (this.primary && this.primary.id == id) return false;
      var found = false;
      if (Array.isArray(this.others))
        $.each(this.others, function (i, v) {
          if (v.id == id) {
            found = true;
            return false;
          }
        });
      return !found;
    },
  };

  t.show = function (e) {
    if (e) e.preventDefault();
    t.refreshUi();
    t.toggleModal(true);
  };

  t.make_primary = function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    var earlyPrimary = t.merge_items.primary;
    var isMade = false;
    if (Array.isArray(t.merge_items.others) && t.merge_items.others.length) {
      $.each(t.merge_items.others, function (i, v) {
        if (v.id == id) {
          t.merge_items.primary = v;
          t.merge_items.others.splice(i, 1);
          isMade = true;
          return false;
        }
      });
    }
    if (isMade && earlyPrimary && earlyPrimary.id !== undefined)
      t.merge_items.others.unshift(earlyPrimary);
    t.refreshUi();
  };

  t.remove = function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    if (t.merge_items.primary && t.merge_items.primary.id == id) {
      t.merge_items.primary = null;
    } else if (Array.isArray(t.merge_items.others)) {
      $.each(t.merge_items.others, function (i, v) {
        if (v.id == id) {
          t.merge_items.others.splice(i, 1);
          return false;
        }
      });
    }
    if (t.merge_items.get_length() < 1) t.merge_items.department_id = null;
    t.refreshUi();
  };

  t.refreshUi = function () {
    t.ui.primary_ticket.empty().addClass("hide");
    t.ui.others_ul.empty();
    t.ui.count_shower.text("");
    if (t.ui.comments.next(".comment-error").length)
      t.ui.comments.next(".comment-error").remove();
    var len = t.merge_items.get_length();
    if (len < 1) {
      t.ui.no_ticket.removeClass("hide");
      return;
    }
    if (t.merge_items.primary) {
      t.ui.primary_ticket.append(
        t.temp.primary ? t.temp.primary(t.merge_items.primary) : "",
      );
      t.ui.no_ticket.addClass("hide");
      t.ui.primary_ticket.removeClass("hide");
    }
    if (Array.isArray(t.merge_items.others) && t.merge_items.others.length) {
      $.each(t.merge_items.others, function (i, v) {
        t.ui.others_ul.append(t.temp.others ? t.temp.others(v) : "");
      });
    }
    t.ui.count_shower.text(len + " user(s) added for merge");
  };

  t.addToMerge = function (user) {
    var tr = config.translations || {};
    try {
      if (user.user_status !== "Active") {
        if (typeof PNotify !== "undefined")
          new PNotify({
            text: tr.deactive || "User is inactive.",
            type: "error",
            delay: 2000,
          });
        return false;
      }
      if (t.merge_items.get_length() === 10) {
        if (typeof PNotify !== "undefined")
          new PNotify({
            text: tr.maximum_10 || "Maximum 10 users.",
            type: "error",
            delay: 2000,
          });
        return false;
      }
      if (!t.merge_items.canAddItem(user.id)) {
        if (typeof PNotify !== "undefined")
          new PNotify({
            text:
              (tr.user || "User") +
              " " +
              user.id +
              " " +
              (tr.is_already_added_in_merge_list || "already added."),
            type: "error",
            delay: 2000,
          });
        return false;
      }
      t.merge_items.get_length() > 0
        ? t.merge_items.others.length > 0
          ? t.merge_items.others.unshift(user)
          : t.merge_items.others.push(user)
        : (t.merge_items.primary = user);
      if (typeof PNotify !== "undefined")
        new PNotify({
          text:
            (tr.user || "User") +
            " " +
            user.id +
            " " +
            (tr.has_been_added || "added."),
          type: "success",
          delay: 2000,
        });
      return true;
    } catch (err) {
      console.error(err);
      if (typeof PNotify !== "undefined")
        new PNotify({
          text: tr.unable_to_add_merge || "Unable to add.",
          type: "error",
          delay: 2000,
        });
      return false;
    }
  };

  t.initMerge = function (e) {
    e.preventDefault();
    var tr = config.translations || {};
    if (!t.httpCall) return;
    if (t.merge_items.get_length() < 2) {
      if (typeof PNotify !== "undefined")
        new PNotify({
          text: tr.atleast_2 || "Add at least 2 users.",
          type: "error",
          delay: 2000,
        });
      return;
    }
    if (!t.merge_items.primary) {
      if (typeof PNotify !== "undefined")
        new PNotify({
          text: tr.please_select_primary || "Select a primary user.",
          type: "error",
          delay: 2000,
        });
      return;
    }
    var comments = $.trim(t.ui.comments.val());
    if (!comments || comments.length <= 1) {
      if (!t.ui.comments.next(".comment-error").length) {
        t.ui.comments.after(
          '<div class="comment-error">This Field is required!</div>',
        );
      }
      if (typeof PNotify !== "undefined")
        new PNotify({
          text: tr.please_enter_comments || "Enter comments.",
          type: "error",
          delay: 2000,
        });
      return;
    }
    t.httpCall = false;
    var formData = new FormData();
    formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    formData.append("primary", t.merge_items.primary.id);
    var others_str = [];
    $.each(t.merge_items.others, function (i, v) {
      others_str.push(v.id);
    });
    formData.append("others", others_str.join(","));
    formData.append("remarks", comments);

    $.ajax({
      url: config.url.merge_users,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    })
      .done(function (data) {
        if (typeof data === "object") {
          if (data.status === "success") {
            t.eventDispatcher.trigger("merge_happend");
            if (typeof sweetAlert === "function")
              sweetAlert("center", "success", data);
            t.toggleModal(false);
            t.clearList();
          } else {
            if (typeof sweetAlert === "function")
              sweetAlert("center", "error", data);
          }
        }
      })
      .fail(function () {
        if (typeof sweetAlert === "function")
          sweetAlert("center", "error", {
            msg: tr.something_went_wrong || "Something went wrong.",
          });
      })
      .always(function () {
        t.httpCall = true;
      });
  };

  t.clearList = function (e) {
    if (e) e.preventDefault();
    t.merge_items.department_id = null;
    t.merge_items.primary = null;
    t.merge_items.others = [];
    t.ui.comments.val("");
    t.toggleModal(false);
  };

  t.toggleModal(false);
  t.mdl.on("click", ".merge_item_remove", $.proxy(t.remove, t));
  t.mdl.on("click", ".merge_item_make_primary", $.proxy(t.make_primary, t));
  t.mdl.on("click", "#btnSubmit", $.proxy(t.initMerge, t));
  t.mdl.on("click", "#btnClear", $.proxy(t.clearList, t));
  // the entire js written below is for clicking the all options section in actions.

  var menuCloseTimer;

  // this function is used so that anywhere other than the options button is clicked then the options should become hidden in this case
  $(document).on('click', function (e) {
    if (!$(e.target).closest('#detached-action-menu').length && !$(e.target).closest('.user-list-menu-toggle').length) {
      $('#detached-action-menu').remove();
      $('.user-list-menu-toggle').data('menu-open', false);
    }
  });

  $(document).on('mouseenter', '.user-list-menu-toggle, #detached-action-menu', function () {
    clearTimeout(menuCloseTimer);
  });

  $(document).on('mouseleave', '.user-list-menu-toggle, #detached-action-menu', function () {
    menuCloseTimer = setTimeout(function () {
      var btnHovered = $('.user-list-menu-toggle:hover').length;
      var menuHovered = $('#detached-action-menu:hover').length;

      if (!btnHovered && !menuHovered) {
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);
      }
    }, 150);
  });

  // this function is used for what happens when we click on the all options button
  $(document).on('mouseenter', '.user-list-menu-toggle', function (e) {
    if ($('#detached-action-menu').length) {
      clearTimeout(menuCloseTimer);
      return;
    }

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    var $btn = $(this);

    $('#detached-action-menu').remove();
    $('.user-list-menu-toggle').data('menu-open', false);

    var $menu = $btn.siblings('.dropdown-menu').clone(true);
    $menu.attr('id', 'detached-action-menu').addClass('show');

    $('body').append($menu);

    var btnRect = $btn[0].getBoundingClientRect();

    $menu.css({
      position: 'fixed',
      top: btnRect.bottom + 'px',
      left: btnRect.left + 'px',
      zIndex: 99999,
      display: 'block'
    });

    setTimeout(function () {
      var menuHeight = $menu.outerHeight();
      var menuWidth = $menu.outerWidth();

      var windowHeight = $(window).height();
      var windowWidth = $(window).width();

      // If menu goes below viewport, show above button
      if (btnRect.bottom + menuHeight > windowHeight) {
        $menu.css('top', (btnRect.top - menuHeight) + 'px');
      }

      // Prevent right overflow
      if (btnRect.left + menuWidth > windowWidth) {
        $menu.css('left', (windowWidth - menuWidth - 10) + 'px');
      }

      // Prevent left overflow
      if (parseFloat($menu.css('left')) < 0) {
        $menu.css('left', '10px');
      }
    }, 0);

    $btn.data('menu-open', true);
  });

  // this function is used for what happens when we click on the elements inside the all options like edit, delete, clone, checkout etc.
  $(document).on('click', '#detached-action-menu .dropdown-item', function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $item = $(this);
    var id = $item.data('id');
    var action = $item.attr('action');

    $('#detached-action-menu').remove();
    $('.user-list-menu-toggle').data('menu-open', false);

    var actionClass = null;
    ['dtActClone', 'dtActView', 'dtActSendCredential', 'dtActNotify', 'add_to_merge'].forEach(function (cls) {
      if ($item.hasClass(cls)) actionClass = cls;
    });

    if (actionClass && id) {
      $('#default_order').find('.' + actionClass + '[data-id="' + id + '"]').first().trigger('click');
    }
  });

  // this function is used such that when we scroll, the extra options should become hidden if it is visible in this case.
  $(document).on('scroll', function () {
    $('#detached-action-menu').remove();
    $('.user-list-menu-toggle').data('menu-open', false);
  });
};
