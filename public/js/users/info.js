var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main#mainContent');
    t.infoPhase = new InfoPhase(t.config);
    t.permission = new Permission(t.config);
    t.deviceTab  = new DeviceTab(t.config);
    t.licenseTab = new LicenseTab(t.config);
    t.accessoriesTab = createAccessoriesTab(t.config);
    t.consumablesTab = createConsumablesTab(t.config);
    t.componentsTab  = createComponentsTab(t.config);
    t.documentsTab = createDocumentsTab(t.config);
    t.historyTab = createHistoryTab(t.config);
    t.userHistoryTab = createUserHistoryTab(t.config);

    t.applyFilter = t.content.find('#apply-filter');
    t.historyFilter = $('.btn-open-filter');
    t.historyModal = $('#historyFilter');
    t.filters = {};
    t.filters.asset_type = t.content.find("#filter_by_asset_type"),
    t.filters.device_tag = t.content.find("#filter_by_device_tag");
    t.filters.device_name = t.content.find("#filter_by_device_name");
    t.filters.license_tag = t.content.find("#filter_by_license_tag");
    t.filters.license_name = t.content.find("#filter_by_license_name");
    t.filters.consumable_tag = t.content.find("#filter_by_consumable_tag");
    t.filters.consumable_name = t.content.find("#filter_by_consumable_name");
    t.filters.accessory_tag = t.content.find("#filter_by_accessory_tag");
    t.filters.accessory_name = t.content.find("#filter_by_accessory_name");

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    $('.history-table').on('draw.dt', function () {
        $('[data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this);
        });
    });

    t.historyFilter.on('click', function() {
        t.historyModal.modal('show');
    });

    t.applyFilter.on("click", function () {
        t.applyFilters();
    });

    t.content.find('.btn-clear-filter').on('click', function () {
        t.clearFilters();
    });

    t.updateFilterBadge = function () {
        var count = 0;
        $.each(t.config.other_filters, function (key, val) {
            if (val && $.trim(val) !== '') count++;
        });
        var badge = t.historyFilter.find('.filter-count-badge');
        if (count > 0) {
            badge.text(count).removeClass('d-none');
        } else {
            badge.text(0).addClass('d-none');
        }
    };

    t.clearFilters = function () {
        t.filters.asset_type.val("null");
        t.filters.device_tag.val('');
        t.filters.device_name.val('');
        t.filters.license_tag.val('');
        t.filters.license_name.val('');
        t.filters.consumable_tag.val('');
        t.filters.consumable_name.val('');
        t.filters.accessory_tag.val('');
        t.filters.accessory_name.val('');
        t.config.other_filters = {};
        t.updateFilterBadge();
        t.historyTab.dTable.ajax.reload(null, false);
        t.historyModal.modal('hide');
    };

    t.applyFilters = function () {
        t.cacheFilterValues();
        t.updateFilterBadge();
        t.historyTab.dTable.ajax.reload(null, false);
        t.historyModal.modal('hide');
    };

    t.cacheFilterValues = function() {
        var v = $.trim($('.history-search-input').val());
        t.config.search = v;
        t.config.other_filters = {};
        t.config.other_filters.asset_type = t.filters.asset_type.val();
        t.config.other_filters.device_tag = t.filters.device_tag.val();
        t.config.other_filters.device_name = t.filters.device_name.val();
        t.config.other_filters.license_tag = t.filters.license_tag.val();
        t.config.other_filters.license_name = t.filters.license_name.val();
        t.config.other_filters.consumable_tag = t.filters.consumable_tag.val();
        t.config.other_filters.consumable_name = t.filters.consumable_name.val();
        t.config.other_filters.accessory_name = t.filters.accessory_name.val();
        t.config.other_filters.accessory_tag = t.filters.accessory_tag.val();
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    // changes for adjusting datatable when opening and closing sidebar
    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    // changes for adjusting datatable when zooming in and out
    $(window).on('resize', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
};


/* ==========================================================================
   SHARED HELPER — animate tab pane on show
   ========================================================================== */
var animateTabSection = function (pane) {
    var holder = pane.find(".table-responsive");
    holder.stop(true, true).css({ opacity: 0, marginTop: "-14px" }).animate({ opacity: 1, marginTop: "0px" }, 220);
};


/* ==========================================================================
   SHARED HELPER — build "open in new tab" icon link
   ========================================================================== */
var buildOpenIcon = function (url, title) {
    return '<a href="' + url + '" target="_blank"' +
        ' class="btn btn-sm btn-light d-inline-flex align-items-center justify-content-center"' +
        ' title="' + (title || "Open") + '">' +
        '<svg width="16" height="16" fill="#1765f3" viewBox="0 0 16 16" aria-hidden="true">' +
        '<path d="M3.5 3A1.5 1.5 0 0 0 2 4.5v7A1.5 1.5 0 0 0 3.5 13h4a.5.5 0 0 0 0-1h-4a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5h4a.5.5 0 0 0 0-1h-4z"/>' +
        '<path d="M8.146 11.354a.5.5 0 0 1 0-.708L10.793 8 8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0z"/>' +
        '<path d="M5.5 8a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"/>' +
        '</svg>' +
        '</a>';
};

var buildTabControlSelector = function (controlName, tabKey) {
    return '.js-user-detail-' + controlName + '[data-tab-key="' + tabKey + '"]';
};

var createTabControlSelectors = function (tabKey) {
    return {
        pageLength: buildTabControlSelector("page-length", tabKey),
        search: buildTabControlSelector("search-input", tabKey),
        searchButton: buildTabControlSelector("search-button", tabKey),
        refresh: buildTabControlSelector("refresh-button", tabKey)
    };
};

var getTabSearchValue = function (tab) {
    return $.trim(tab.pane.find(tab.controls.search).val() || "");
};

var applyTableSearch = function (tab) {
    if (!tab.dTable) return;

    tab.dTable.search(getTabSearchValue(tab)).draw();
    tab.dTable.columns.adjust();
};

var resetTableSearch = function (tab) {
    if (!tab.dTable) return;

    if (tab.serverSide !== false && tab.dTable.ajax) {
        tab.dTable.ajax.reload(null, false);
    } else {
        tab.dTable.draw(false);
    }
    tab.dTable.columns.adjust();
};

var reloadTablePane = function (tab) {
    if (!tab.dTable) return;

    if (tab.serverSide !== false && tab.dTable.ajax) {
        tab.dTable.ajax.reload(null, false);
        tab.dTable.columns.adjust();
        return;
    }

    tab.dTable.columns.adjust().draw(false);
};

var getInitialPageLength = function (tab) {
    var len = parseInt(tab.pane.find(tab.controls.pageLength).val(), 10);
    return len > 0 ? len : 10;
};

var getTableDomLayout = function () {
    return "rt<'d-flex flex-wrap align-items-center justify-content-between gap-2 mt-3 mb-3'ip>";
};

var bindTableToolbar = function (tab) {
    tab.pane.on("change", tab.controls.pageLength, function () {
        var len = parseInt($(this).val(), 10);
        if (tab.dTable && len > 0) tab.dTable.page.len(len).draw(false);
    });

    tab.pane.on("keydown", tab.controls.search, function (e) {
        if (e.key !== "Enter") return;

        e.preventDefault();
        applyTableSearch(tab);
    });

    tab.pane.on("click", tab.controls.searchButton, function (e) {
        if (e) e.preventDefault();
        applyTableSearch(tab);
    });

    tab.pane.on("click", tab.controls.refresh, function (e) {
        if (e) e.preventDefault();
        resetTableSearch(tab);
    });
};


/* ==========================================================================
   InfoPhase — Overview tab
   ========================================================================== */
var InfoPhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $(".main-content");
    t.tabBar = t.content.find(".tab-bar");
    t.pane = t.content.find("#overview");
    t.tabTrigger = t.content.find("#overview-tab");
    t.searchTimer = null;
    t.bindEvents();
    t.tabTrigger.trigger("click");
    t.userMdl = { initialized: false, selectsInitialized: false };
    t.httpCall = true;
    t.initPrintModal();
};

InfoPhase.prototype.bindEvents = function () {
    var t = this;
  t.tabBar.on("click", "#overview-tab", function () {
    t.loadContent();
  });
    t.pane.on("input", ".device-list-search", function () {
        var input = $(this);
        clearTimeout(t.searchTimer);
    t.searchTimer = setTimeout(function () {
      t.filterContent(input.val());
    }, 250);
    });

    t.pane.on("click", ".refresh-button", function () {
        t.pane.find(".device-list-search").val("");
        t.loadContent();
    });

  $(document).on("click", ".open-tab", function () {
    var tabId = $(this).data("tab");
    var tabEl = document.getElementById(tabId);
    if (tabEl) {
      new bootstrap.Tab(tabEl).show();
    }
  });

  /* ==========================================
       USER ACTION BUTTONS
  ========================================== */

  $(document).on("click", ".dtActPrintLabel", $.proxy(t.printLabelModal, t));
  $(document).on("click", ".dtActEditUser", $.proxy(t.editUser, t));
  $(document).on("click", ".dtActClone", $.proxy(t.cloneUser, t));
  $(document).on("click", ".dtActPrint", $.proxy(t.printUser, t));
  $(document).on("click", ".dtActDel", $.proxy(t.deleteUser, t));
  $(document).on("click","#printModal #btnSubmit",$.proxy(t.handlePrintSubmitClick, t),);
  $(document).on("click","#togglePassword",$.proxy(t.togglePasswordVisibility, t));
  $(document).on("click",".read-more-company",$.proxy(t.handleAccessibleCompanyClick, t));
  $(document).on("click",".make-logout", $.proxy(t.makeLogout,t));
};
InfoPhase.prototype.makeLogout = function (e) {
    if (e) e.preventDefault();
    var t = this;
    var url = t.config.url.make_logout.replace(':session_id', $(e.currentTarget).attr('data-session'));
    Swal.fire({
    title: 'Are you sure you want to logout?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes',
    cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            $.get(url, function(response) {
                if (typeof response === "object" && typeof response.msg !== "undefined") {
                    Swal.fire({
                        icon: response.status === 'success' ? 'success' : 'error',
                        title: response.msg
                    });

                    if (response.status === 'success') {
                        t.loadContent();
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: config.translations.something_went_wrong
                    });
                }
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: config.translations.something_went_wrong
                });
            });
        }
    });
}

InfoPhase.prototype.loadContent = function () {
    var t = this;
    $.ajax({
    url: t.config.url.user_basic_info + "/" + t.config.user_id,
    type: "GET",
    success: function (res) {
      t.pane.html(res);
    },
    error: function (xhr) {
      console.error("InfoPhase load error:", xhr.responseText);
    },
    });
};

/* Text-node filter — shows/hides table rows and list items that match */
InfoPhase.prototype.filterContent = function (query) {
    var t = this;
    var term = $.trim(query).toLowerCase();

    t.pane.find("tr").each(function () {
        var row = $(this);
        row.toggle(!term || row.text().toLowerCase().indexOf(term) !== -1);
    });

    t.pane.find("li").each(function () {
        var item = $(this);
        item.toggle(!term || item.text().toLowerCase().indexOf(term) !== -1);
    });
};

InfoPhase.prototype.getCsrfToken = function () {
  if (this.config && this.config.token) return this.config.token;
  var meta = $('meta[name="csrf-token"]');
  return meta.length ? meta.attr("content") : "";
};

InfoPhase.prototype.confirmAndPost = function (
  confirmText,
  url,
  payload,
  options,
) {
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
    }).then(function (result) {
      if (result.isConfirmed) doPost();
    });
  } else {
    if (confirm(confirmText)) doPost();
  }
};

InfoPhase.prototype.showToast = function (icon, message) {
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

InfoPhase.prototype.resolveMessage = function (message) {
  if (typeof message !== "string") return message;
  var tr = (this.config && this.config.translations) || {};
  if (tr[message]) return tr[message];
  return message;
};

InfoPhase.prototype.reload = function () {
  if (!this.dTable) return;
  this.dTable.search(this.config.search || "").draw();
};

// for modal edit

InfoPhase.prototype.initUserModalSelects = function () {
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
          return {
            search: p.term,
            page: p.page || 1,
            type: "ticket",
            company_id: el.company.val(),
          };
        },
      },
    }),
  );

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

InfoPhase.prototype.getUserModalSelectDropdownParent = function (element) {
  var mdl =
    this.userMdl && this.userMdl.mdl ? this.userMdl.mdl : $("#user-mdl");
  if (!element || !element.length) return mdl;

  var parent = element.closest(".input-group");
  if (parent.length) return parent;

  parent = element.closest(".row-cover");
  return parent.length ? parent : mdl;
};

InfoPhase.prototype._initFormValidator = function () {
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

  $.validator.addMethod(
    "must_have_alphabet",
    function (v, el) {
      return this.optional(el) || /[a-zA-Z]/.test(v);
    },
    "Name must contain at least one alphabet.",
  );

  $.validator.addMethod(
    "fileExtension",
    function (value, element, param) {
      if (element.files.length === 0) {
        return true;
      }

      var extension = value.split(".").pop().toLowerCase();

      return $.inArray(extension, param) !== -1;
    },
    function (params, element) {
      return (
        "Only files with these extensions are allowed: " + params.join(", ")
      );
    },
  );

  $.validator.addMethod(
    "filesize",
    function (value, element, param) {
      if (element.files.length === 0) {
        return true;
      }

      return element.files[0].size <= param;
    },
    "File size must be less than 2 MB.",
  );

  t.userMdl.frmValidator = t.userMdl.frm.validate({
    onsubmit: false,
    rules: {
      first_name: {
        required: true,
        name_format: true,
        must_have_alphabet: true,
        maxlength: 255,
      },
      last_name: {
        required: true,
        name_format: true,
        must_have_alphabet: true,
        maxlength: 255,
      },
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
        clean_text_only: true,
      },
      business_unit: {
        clean_text_only: true,
      },
      delivery_unit: {
        clean_text_only: true,
      },
      seat_no: {
        required: false,
        clean_text_only: true,
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
        required: true,
      },
      notes: {
        maxlength: 255,
        remarks: false,
        clean_text_only: true,
      },
      address: {
        maxlength: 255,
        remarks: false,
        clean_text_only: true,
      },
      jobtitle: {
        clean_text_only: true,
      },
      password: {
        str_name_format: false,
        strongPassword: true,
        notEqualToFields: {
          username: "username",
          email: "email",
        },
      },
      ex_user_company: {
        str_name_format: true,
        clean_text_only: true,
      },
      password: {
        strongPassword: true,
        notEqualToFields: { username: "username", email: "email" },
      },
      avatar: {
        fileExtension: ["jpeg", "jpg", "bmp", "png"],
        filesize: 2097152,
      },
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
    },
    unhighlight: function (element) {
      t.updateUserModalValidationState($(element), false);
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

InfoPhase.prototype.prepareUserModalFieldLayout = function () {
  var frm =
    this.userMdl && this.userMdl.frm ? this.userMdl.frm : $("#user-mdl-frm");

  if (!frm.length) return;

  frm.find(".input-group").each(function () {
    $(this).closest(".amg-form-field").addClass("amg-form-field-row");
  });
};

InfoPhase.prototype._loadRolesSelect = function () {
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
  });
};

InfoPhase.prototype.isFilledValue = function (value) {
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

InfoPhase.prototype.safeDisplayValue = function (value, fallback) {
  return this.isFilledValue(value)
    ? String(value).trim()
    : fallback !== undefined
      ? fallback
      : "-";
};
InfoPhase.prototype.escapeHtml = function (value) {
  return String(value === null || value === undefined ? "" : value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};

InfoPhase.prototype.initPhoneDropdown = function (selector, selectedCode) {
  let element = $(selector);
  if (element.hasClass("select2-hidden-accessible")) {
    element.select2("destroy");
  }
  let countryOptions = '<option value=""></option>';
  $.each(config.countries_data, function (index, country) {
    let phoneCode = country.phonecode || "";
    if (!phoneCode.startsWith("+")) {
      phoneCode = "+" + phoneCode;
    }
    countryOptions += `
                <option value="${country.id}">
                    ${phoneCode}
                </option>
            `;
  });
  element.html(countryOptions);
  element.select2({
    dropdownParent: element.parent(),
    placeholder: "Enter Code",
    allowClear: true,
  });
  element.val(selectedCode).trigger("change");
};

InfoPhase.prototype.getUserInitials = function (name) {
  var words = String(this.safeDisplayValue(name, ""))
    .split(/\s+/)
    .filter(Boolean);
  if (!words.length) return "NA";
  if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
  return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
};

InfoPhase.prototype.getAvatarHtml = function (name, imageUrl, className) {
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

InfoPhase.prototype.initUserModal = function () {
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
    request_approval_delegated_user: frm.find(
      "#request_approval_delegated_user",
    ),
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
    avatar: frm.find(".user_profile_avatar"),
    phone_country_code: frm.find("#phone_country_id").trigger("change"),
    phone2_country_code: frm.find("#phone2_country_id").trigger("change"),
    work_phone_country_code: frm
      .find("#work_phone_country_id")
      .trigger("change"),
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

InfoPhase.prototype.updateUserModalValidationState = function (
  element,
  hasError,
) {
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

InfoPhase.prototype.loadInternalPlaces = function () {
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

InfoPhase.prototype._loadImageViewer = function (img, forAction) {
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

InfoPhase.prototype.syncPasswordRequirement = function () {
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

InfoPhase.prototype._loadGroupsOptions = function (selectedGroups) {
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

InfoPhase.prototype.resetUserModal = function () {
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
  el.manager
    .empty()
    .append(new Option(tr.Select_the_Manager || "Select Manager", ""))
    .trigger("change");
  el.location
    .empty()
    .append(new Option(tr.Select_the_Location || "Select Location", ""))
    .trigger("change");
  el.internal_place
    .empty()
    .append(new Option(tr.select_place || "Select Place", ""))
    .trigger("change");
  el.base_location
    .empty()
    .append(new Option(tr.Select_base_Location || "Select Base Location", ""))
    .trigger("change");
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

InfoPhase.prototype.toggleModal = function (selector, show) {
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

InfoPhase.prototype.syncUserModalSelect2Width = function (element) {
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

InfoPhase.prototype.getUserModalErrorWrap = function (element) {
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

InfoPhase.prototype.loadForm = function (user, forAction) {
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
    t.userMdl.frm
      .find("input[name='username']")
      .val(user.data.username || "")
      .prop("disabled", true);
    t.userMdl.frm.find("input[name='email']").val(user.data.email || "");
    t.userMdl.frm.find("input[name='phone']").val(user.data.phone || "");
    t.userMdl.frm.find("input[name='phone2']").val(user.data.phone2 || "");
    t.userMdl.frm
      .find("input[name='work_phone']")
      .val(user.data.work_phone || "");
    t.userMdl.frm.find("textarea[name='address']").val(user.data.address || "");
  } else {
    t.userMdl.frm.find("input[name='username']").prop("disabled", false);
  }
  this.initPhoneDropdown(
    t.userMdl.el.phone_country_code,
    user.data.phone_country_id,
  );
  this.initPhoneDropdown(
    t.userMdl.el.phone2_country_code,
    user.data.phone2_country_id,
  );
  this.initPhoneDropdown(
    t.userMdl.el.work_phone_country_code,
    user.data.work_phone_country_id,
  );

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

  if (user.dropdown && user.dropdown.sevice_ticket_department) {
    el.ticket_department.empty();
    $.each(user.dropdown.sevice_ticket_department, function (i, v) {
      el.ticket_department.append(new Option(v.text, v.id, true, true));
    });
    el.ticket_department.trigger("change");
  }

  if (user.dropdown && user.dropdown.user_companies) {
    el.user_company_id.empty();
    $.each(user.dropdown.user_companies, function (i, v) {
      el.user_company_id.append(new Option(v.text, v.id, true, true));
    });
    el.user_company_id.trigger("change");
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

InfoPhase.prototype._userDropdownFormat = function (s) {
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

InfoPhase.prototype.handleSubmit = function (e) {
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
          //   t.reload();
          t.loadContent();
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

InfoPhase.prototype.addUser = function () {
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
};

InfoPhase.prototype.editUser = function (e) {
  if (e) e.preventDefault();

  var t = this;
  var tr = t.config.translations || {};

  var userId = $(e.currentTarget).data("id");

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
        t.showToast("error", data?.msg || "Failed to load user.");
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.userMdl.loader.hide();
    });
};

InfoPhase.prototype.cloneUser = function (e) {
  if (e) e.preventDefault();

  var t = this;
  var tr = t.config.translations || {};

  var userId = $(e.currentTarget).data("id");

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

        // clone specific
        t.userMdl.frm
          .find("input[name='username']")
          .val("")
          .prop("disabled", false);
        t.userMdl.frm.find("input[name='email']").val("");
        t.userMdl.frm.find("input[name='password']").val("");

        t.toggleModal("#user-mdl", true);
      } else {
        t.showToast("error", data?.msg || "Failed to load user.");
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    })
    .always(function () {
      t.userMdl.loader.hide();
    });
};
InfoPhase.prototype.deleteUser = function (e) {
  e.preventDefault();

  var userId = $(e.currentTarget).data("id");
  var t = this;
  var tr = t.config.translations || {};

  t.confirmAndPost(
    tr.delete_user_confirm || "Are you sure you want to delete this user?",
    t.config.url.userDelete + "/" + userId,
    {},
    {
      successMessage: tr.delete_user_success || "User deleted successfully.",
      errorMessage:
        tr.delete_user_error ||
        tr.something_went_wrong ||
        "Something went wrong.",
    },
  );
};

InfoPhase.prototype.printUser = function (e) {
  e.preventDefault();
  var userId = $(e.currentTarget).attr("data-id");
    var t = this;

    $.ajax({
        url: t.config.url.print + "/" + userId,
        type: "GET",
        dataType: "json",
        success: function (res) {

            if (res.status === "failure") {
                t.showToast("error", res.msg);
                return;
            }

            window.location = t.config.url.print + "/" + userId;
        },
        error: function (xhr) {

            if (xhr.responseJSON && xhr.responseJSON.msg) {
                t.showToast("error", xhr.responseJSON.msg);
            } else {
                window.location = t.config.url.print + "/" + userId;
            }
        }
    });
};


InfoPhase.prototype.initPrintModal = function () {
  this.print = {
    mdl: $("#printModal"),
    form: $("#print-modal-form"),
    select: $("#print_opt"),
    starter: $("#print_label_starter"),
  };
};

InfoPhase.prototype.printLabelModal = function (e) {
    e.preventDefault();

    var userId = $(e.currentTarget).data("id");
  var t = this;

    $.get(t.config.url.checkuser + "/" + userId)
        .done(function (res) {

            if (res.status === "failure") {
                t.showToast("error", res.msg);
                return;
            }

            t.print.mdl.modal("show");
        })
        .fail(function () {
            t.showToast("error", "Unable to verify user.");
        });
};

InfoPhase.prototype.handlePrintSubmitClick = function (e) {
  if (e) e.preventDefault();
  this.submitPrintLabel();
};

InfoPhase.prototype.submitPrintLabel = function () {
  var t = this,
    tr = t.config.translations || {};
  var selected = t.selectedUserIds || [t.config.user_id];
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
  window.open(url + "/" + btoa(selected.join(",")), "_blank");
  t.toggleModal("#printModal", false);
};

InfoPhase.prototype.togglePasswordVisibility = function (e) {
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


// InfoPhase.prototype.handleAccessibleCompanyClick = function (e) {
//     e.preventDefault();

//     var companies = $(e.currentTarget).data("companies");

//     if (typeof companies === "string") {
//         companies = companies.split(",");
//     }

//     var html = '<div class="d-flex flex-wrap gap-2">';

//     $.each(companies, function (i, company) {
//         html +=
//             '<span class="badge bg-secondary">' +
//             company +
//             "</span>";
//     });

//     html += "</div>";

//     $("#companyListModalBody").html(html);
//     $("#companyListModal").modal("show");
// };

InfoPhase.prototype.handleAccessibleCompanyClick = function (e) {

    if (e) {
        e.preventDefault();
    }

    var companies = $(e.currentTarget).data('companies').split(',');
    var html = '<div class="d-flex flex-wrap gap-1">';

    companies.forEach(function(company) {
        html += '<span class="badge bg-dark text-white px-2 py-1">' +
                $('<div>').text(company.trim()).html() +
                '</span>';
    });

    html += '</div>';

    $('#companyListModalBody').html(html);
    $('#companyListModal').modal('show');
};

/* ==========================================================================
   Permission — Permissions tab
   ========================================================================== */
var Permission = function (config) {
    var t = this;
    t.config = config;
    t.content = $(".main-content");
    t.tabBar = t.content.find(".tab-bar");
    t.pane = t.content.find("#permissions");
    t.tabTrigger = "#permissions-tab";
    t.searchTimer = null;
    t.bindEvents();
};

Permission.prototype.bindEvents = function () {
    var t = this;
    t.tabBar.on("click", t.tabTrigger, function () {t.loadContent();});
    t.pane.on("input", ".permission-search", function () {
        var input = $(this);
        clearTimeout(t.searchTimer);
        t.searchTimer = setTimeout(function () {t.filterContent(input.val());}, 250);
    });

    t.pane.on("click", ".btn-reload-list", function (e) {
        e.preventDefault();
        t.pane.find(".permission-search").val("");
        t.loadContent();
    });
};

Permission.prototype.loadContent = function (e) {
    var t = this;
    $.ajax({
        url : t.config.url.user_permissions,
        type : "GET",
        data : { user_id: t.config.user_id },
        beforeSend: function () {
            t.pane.html(`<div class="tab-loader d-flex justify-content-center"><div class="spinner"></div></div>`);
        },
        success: function (res) { t.pane.html(res); },
        error : function (xhr) { console.error("Permission load error:", xhr.responseText); }
    });
};

Permission.prototype.filterContent = function (query) {
    var t = this;
    var term = $.trim(query).toLowerCase();

    t.pane.find('.permissions-container .col-md-4').each(function () {
        var col = $(this);
        col.toggle(!term || col.text().toLowerCase().indexOf(term) !== -1);
    });

    var firstVisibleNavLink = null;

    $('#v-pills-tab .nav-link').each(function () {
        var navLink = $(this);
        var targetId = navLink.attr('href');
        var targetPane = $(targetId);

        var hasMatch = false;
        if (!term) {
            hasMatch = true;
        } else {
            targetPane.find('.permissions-container .col-md-4').each(function () {
                if ($(this).text().toLowerCase().indexOf(term) !== -1) {
                    hasMatch = true;
                    return false;
                }
            });
        }

        navLink.toggle(hasMatch);

        if (hasMatch && !firstVisibleNavLink) {
            firstVisibleNavLink = navLink;
        }
    });

    if (term && firstVisibleNavLink) {
        firstVisibleNavLink.tab('show');
    } else if (!term) {
        $('#v-pills-tab .nav-link').first().tab('show');
    }
};
/* ==========================================================================
   DeviceTab — Assigned Devices DataTable
   ========================================================================== */

var DeviceTab = function (config) {
    var t = this;
    t.config = config;
    t.content = $(".main-content");
    t.tabBar = t.content.find(".tab-bar");
    t.pane = t.content.find("#assets");
    t.table = t.content.find("#device-tab");
    t.controls = createTabControlSelectors("assets");
    t.serverSide = true;
    t.dTable = null;
    t.isLoaded = false;
    t.bindEvents();
};

DeviceTab.prototype.bindEvents = function () {
    var t = this;
    t.tabBar.on("shown.bs.tab", "#assets-tab", function () {
        animateTabSection(t.pane);
        if (!t.isLoaded) {
            t.initDataTable();
            t.isLoaded = true;
            return;
        }
        reloadTablePane(t);
    });

    bindTableToolbar(t);
};

DeviceTab.prototype.initDataTable = function () {
    var t = this;
    t.dTable = t.table.DataTable({
        processing : true,
        serverSide : true,
        deferRender : true,
        stateSave : false,
        searchDelay : 600,
        pageLength  : getInitialPageLength(t),
        lengthChange: false,
        searching : true,
        dom : getTableDomLayout(),
        scrollX : true,
        autoWidth : false,
        ordering : true,
        order : [[7, "desc"]],
        ajax: {
            url : t.config.url.assigned_devices,
            type: "POST",
            data: function (d) {
                d._token  = t.config.token;
                d.user_id = t.config.user_id;
            },
            error: function (xhr) { console.error("DeviceTab DataTable error:", xhr.responseText); }
        },
        columns: [
            {
                data : "a.asset_tag",
                defaultContent: "-",
                render : function (data, type, row) {
                    var device = row.a || {};
                    if (!device.id) return data || "-";
                    return '<a href="' + t.config.url.device_info + "/" + device.id + '" target="_blank" class="text-primary fw-semibold text-decoration-none">' + (data || "-") + "</a>";
                }
            },
            { data: "a.name", defaultContent: "-" },
            { data: "a.mdl_name", defaultContent: "-" },
            { data: "a.mnf_name", defaultContent: "-" },
            { data: "a.serial", defaultContent: "-" },
            { data: "a.project_name", defaultContent: "-" },
            { data: "a.last_checkout_format", defaultContent: "-" },
            { data: "a.ni_updated_at", defaultContent: "-" },
            {
                data : "a.id",
                searchable: false,
                orderable: false,
                render : function (data) { return buildOpenIcon(t.config.url.device_info + "/" + data, "Open Device");}
            }
        ],
        drawCallback: function () {
            if (t.dTable) t.dTable.columns.adjust();
        }
    });
};


/* ==========================================================================
   LicenseTab — Assigned Licenses DataTable
   ========================================================================== */
var LicenseTab = function (config) {
    var t = this;
    t.config = config;
    t.content = $(".main-content");
    t.tabBar = t.content.find(".tab-bar");
    t.pane = t.content.find("#license");
    t.table = t.content.find("#licenses-tab");
    t.controls = createTabControlSelectors("license");
    t.serverSide = true;
    t.dTable = null;
    t.isLoaded = false;
    t.bindEvents();
};

LicenseTab.prototype.bindEvents = function () {
    var t = this;
    t.tabBar.on("shown.bs.tab", "#license-tab", function () {
        animateTabSection(t.pane);
        if (!t.isLoaded) {
            t.initDataTable();
            t.isLoaded = true;
            return;
        }
        reloadTablePane(t);
    });

    bindTableToolbar(t);
};

LicenseTab.prototype.initDataTable = function () {
    var t = this;
    t.dTable = t.table.DataTable({
        processing : true,
        serverSide : true,
        deferRender : true,
        stateSave : false,
        searchDelay : 500,
        pageLength  : getInitialPageLength(t),
        lengthChange: false,
        searching : true,
        dom : getTableDomLayout(),
        scrollX : true,
        autoWidth : false,
        ordering : true,
        ajax: {
            url : t.config.url.assigned_licenses,
            type: "POST",
            data: function (d) {
                d._token  = t.config.token;
                d.user_id = t.config.user_id;
            },
            error: function (xhr) { console.error("LicenseTab DataTable error:", xhr.responseText); }
        },
        columns: [
              {
                data : "a.name",
                defaultContent: "-",
                render : function (data, type, row) {
                    var license = row.a || {};
                    var batch = license.lic_batch_no || "";
                    var name = data || "-";
                    var detailUrl = t.config.url.license_detail + "/" + (license.license_id || "");
                    if (!license.license_id) return batch ? (batch + " - " + name) : name;
                    return '<a href="' + detailUrl + '" target="_blank" class="text-primary text-decoration-none fw-semibold">' +
                        (batch ? (batch + " - ") : "") + name + "</a>";
                }
            },
            { data: "a.serial_no",     defaultContent: "-" },
            { data: "a.checkout_info", defaultContent: "-" },
            {
                data : "a",
                searchable: false,
                orderable: false,
                render : function (d) { return buildOpenIcon(config.url.licenseSeatCheckin + "/" + d.id, "Open License"); }
            },
        ],
        drawCallback: function () {
            if (t.dTable) t.dTable.columns.adjust();
        }
    });
};

/* ==========================================================================
   ApiTableTab — Reusable DataTable tab base constructor
   ========================================================================== */
var ApiTableTab = function (options) {
    var t = this;
    t.config = options.config;
    t.content = options.content;
    t.trigger = options.trigger;
    t.pane = t.content.find(options.pane);
    t.table = t.content.find(options.table);
    t.controls = options.controls || createTabControlSelectors(options.controlKey);
    t.ajax = options.ajax;
    t.columns = options.columns || [];
    t.order = options.order || [];
    t.serverSide  = options.serverSide !== false;
    t.searchDelay = options.searchDelay || 500;
    t.pageLength  = options.pageLength  || 10;
    t.ordering = options.ordering !== false;
    t.dTable = null;
    t.isLoaded = false;
    t.bindEvents();
};

ApiTableTab.prototype.bindEvents = function () {
    var t = this;
    t.content.find(".tab-bar").on("shown.bs.tab", t.trigger, function () {
        animateTabSection(t.pane);
        if (!t.isLoaded) {t.initDataTable();
            t.isLoaded = true;
            return;
        }
        reloadTablePane(t);
    });

    bindTableToolbar(t);
};

ApiTableTab.prototype.initDataTable = function () {
    var t = this;
    t.dTable = t.table.DataTable({
        processing  : true,
        serverSide  : t.serverSide,
        deferRender : true,
        stateSave : false,
        searchDelay : t.searchDelay,
        pageLength  : getInitialPageLength(t),
        lengthChange: false,
        searching : true,
        dom : getTableDomLayout(),
        scrollX : true,
        autoWidth : false,
        ordering : t.ordering,
        order : t.order,
        ajax : t.ajax,
        columns : t.columns,
        drawCallback: function () {if (t.dTable) t.dTable.columns.adjust();}
    });
};


/* ==========================================================================
   TAB FACTORIES — one per ApiTableTab instance
   ========================================================================== */

function createAccessoriesTab(config) {
    return new ApiTableTab({
        content : $(".main-content"),
        trigger : "#accessories-tab-link",
        pane : "#accessories",
        table : "#accessories-tab",
        controlKey: "accessories",
        config : config,
        serverSide: true,
        ajax: {
            url : config.url.assigned_accessories,
            type: "POST",
            data: function (d) {
                d._token  = config.token;
                d.user_id = config.user_id;
            }
        },
        columns: [
            {
                data : "a",
                render: function (d) {
                    var tag = d.acc_batch_no ? '<span class="textCategory">' + d.acc_batch_no + "</span> - ": "";
                    return tag + '<a href="' + config.url.accessory_info + "/" + d.id + '" target="_blank" class="text-decoration-none">' + (d.name || "-") + "</a>";
                }
            },
            { data: "a.expected_checkin_format", defaultContent: "-" },
            {
                data : "a.note",
                defaultContent: "-",
                render: function (data) { return data || "-"; }
            },
            {
                data : "a",
                searchable: false,
                orderable: false,
                render    : function (d) {return buildOpenIcon(config.url.accessory_info + "/" + d.id, "Open Accessory");}
            },
        ]
    });
}

function createConsumablesTab(config) {
    return new ApiTableTab({
        content : $(".main-content"),
        trigger : "#consumables-tab-link",
        pane : "#consumables",
        table : "#consumables-tab",
        controlKey: "consumables",
        config : config,
        serverSide: true,
        ajax: {
            url : config.url.assigned_consumables,
            type: "POST",
            data: function (d) {
                d._token  = config.token;
                d.user_id = config.user_id;
            }
        },
        columns: [
            {
                data  : "a",
                render: function (d) {
                    var tag = d.con_batch_no ? '<span class="textCategory">' + d.con_batch_no + "</span> - " : "";
                    return tag + '<a href="' + config.url.consumable_info + "/" + d.id + '" target="_blank" class="text-decoration-none">' + (d.name || "-") + "</a>";
                }
            },
            { data: "a.updated_at", defaultContent: "-" },
            {
                data : "a",
                searchable: false,
                orderable: false,
                render : function (d) { return buildOpenIcon(config.url.consumable_info + "/" + d.id, "Open Consumable"); }
            },
        ]
    });
}

function createComponentsTab(config) {
    return new ApiTableTab({
        content : $(".main-content"),
        trigger: "#components-tab-link",
        pane : "#components",
        table : "#components-tab",
        controlKey: "components",
        config : config,
        serverSide: true,
        ajax: {
            url : config.url.assigned_components,
            type: "POST",
            data: function (d) {
                d._token  = config.token;
                d.user_id = config.user_id;
            }
        },
        columns: [
          {
                data : "a",
                render: function (d) {
                    var tag = d.unique_tag ? '<span class="textCategory">' + d.unique_tag + "</span> - ": "";
                    return tag + '<a href="' + config.url.component_info + "/" + d.id + '" target="_blank" class="text-decoration-none">' + (d.name || "-") + "</a>";
                }
            },
            { data: "a.expected_checkin_format", defaultContent: "-" },
            {
                data : "a",
                searchable: false,
                orderable: false,
                render : function (d) {
                    return buildOpenIcon(config.url.component_info + "/" + d.id, "Open Component");
                }
            },
            
        ]
    });
}

function createDocumentsTab(config) {
    var t = this;
    t.httpCall = true;
    t.mdl = $("main#mainContent").find("#document-mdl");
    t.mdl.title = t.mdl.find('.modal-title');
    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');
    t.mdl.frm = t.mdl.find("#document-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.token = t.mdl.frm.find("input[name='_token']");
    t.mdl.frmEl.note = t.mdl.frm.find("#note");
    t.mdl.frmEl.asset_id = t.mdl.frm.find("input[name='asset_id']");
    t.mdl.frmEl.asset_type = t.mdl.frm.find("input[name='asset_type']");

    t.uploadButton = $('#btn_upload_document');
    t.documentsTab = $('#documents');

    t.addDocument = function(e) {
        e.preventDefault();
        t.frmValidator.resetForm();
        t.resetFrm();
        t.httpPostPath = config.url.document_upload;
        t.mdl.title.html(config.translations.New_Document_Upload);
        t.mdl.btnSubmit.text("Save");
        t.mdl.frmEl.token.val(t.config.token);
        t.mdl.frmEl.asset_id.val(t.config.user_id);
        t.mdl.frmEl.asset_type.val("user");
        t.mdl.modal("show");
    };

    t.handleSubmit = function(e) {
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
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    tab.dTable.ajax.reload(null, false);
                    t.mdl.modal("hide");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            alert(config.translations.something_went_wrong);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            note: {
                remarks: true,
            },
            document: {
                required: true,
                extension: "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
                filesize: 2000000
            }
        },
        messages: {
            document: {
                required: "Please upload a document.",
                extension: "Invalid file extension",
                filesize: "File size must be less than 2MB."
            }
        }
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    t.deleteDocumentFunction = function(e) {
        e.preventDefault();
        var docId = $(e.currentTarget).attr('data-id');
        t.httpPostPath = t.config.url.document_delete;
        var data = {
            'msg':config.translations.something_went_wrong,
        };
        var send_data = {
            "asset_id": t.config.user_id,
            "asset_type": "user",
            "id": docId,
            "_token": t.config.token
        }

        sweetAlertPost(config.translations.are_you_sure_delete_msg, 'warning', t.httpPostPath, tab.dTable.ajax, data, send_data);
    };

    t.uploadButton.on('click', function(e){
        t.addDocument(e);
    });

    t.mdl.btnSubmit.on('click', function(e) {
        t.handleSubmit(e);
    })

    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
    };

    t.documentsTab.on('click', '.dtActDel', function (e) {
        e.preventDefault();
        t.deleteDocumentFunction(e);
    });

    var tab = new ApiTableTab({
        content: $(".main-content"),
        trigger : "#documents-tab-link",
        pane : "#documents",
        table : "#documents-tab",
        controlKey: "documents",
        config : config,
        serverSide: true,
        ajax: {
            url : config.url.documents,
            type: "POST",
            data: function (d) {
                d._token = config.token;
                d.device_id  = config.user_id;
                d.asset_type = "user";
            }
        },
         order: [[1, 'desc']],
        columns: [
            { data: "a.org_name", defaultContent: "-" },
            { data: "a.created_at_format", defaultContent: "-" },
            { data: "a.note", defaultContent: "-" },
            { 
                data: null,
                render: function(data, type, row) {
                    var assetId = row.a.asset_id || '';
                    var uploaderId = row.a.uploader_id || '';
                    var assetType = row.a.asset_type || '';
                    if(assetId==uploaderId && assetType=="user"){
                        return '<div class="bg-muav personal-badge">Personal</div>';
                    } else {
                        return "";
                    }
                },
            },
            {
                data : "a",
                searchable: false,
                orderable: false,
                render : function (d) {
                    let deleteButton = `<button type="button" class="user-list-action-btn dtActDel" data-id="${d.id}" title="${config.translations.Download_Document}" aria-label="${config.translations.Download_Document}">
                    <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
                    </button>`;

                    let downloadButton = `<button type="button" class="user-list-action-btn">
                        <a href="${config.url.document_download}/${d.file_name}" class="btn dtActbtn" data-bs-placement="right" data-bs-toggle="tooltip" data-id="${d.id}" data-original-title="${config.translations.Download_Document}" download>
                            <svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>
                        </a>
                    </button>`;
                    return `<div class="user-list-actions justify-content-start">${deleteButton} ${downloadButton}</div>`
                }
            },
        ]
    });

    return tab;
}

function createHistoryTab(config) {
    return new ApiTableTab({
        content : $(".main-content"),
        trigger : "#history-tab-link",
        pane : "#history",
        table : "#history-tab",
        controlKey: "history",
        config : config,
        serverSide: true,
        ajax: {
            url : config.url.user_history,
            type: "POST",
            data: function (d) {
                d._token  = config.token;
                d.user_id = config.user_id;
                d.filters = config.other_filters || {};
            }
        },
        columns: [
            { data: "a.created_at_format", defaultContent: "-" },
            {
                data  : "a",
                render: function (d) {
                    if (!d.adminuserid) return "-";
                    return '<a href="' + config.url.user_info + "/" + d.adminuserid + '" target="_blank" class="text-decoration-none">' + (d.adm_full_name || "-") + "</a>";
                }
            },
            { data: "a.action_type", defaultContent: "-" },
            { data: "a.asset_type", defaultContent: "-" },
            {
                data  : "a",
                render: function (d) {
                    if (d.asset_type === "accessory"){ 
                        return `<span data-bs-toggle="tooltip" aria-label="${config.translations.Accessory_Tag}" data-bs-original-title="${config.translations.Accessory_Tag}" title="${config.translations.Accessory_Tag}">
                            ${d.acc_tag || '-'}
                        </span>
                        -
                        <span data-bs-toggle="tooltip" aria-label="${config.translations.Accessory_Name}" data-bs-original-title="${config.translations.Accessory_Name}" title="${config.translations.Accessory_Name}">
                            ${d.accessory_name || ''}
                        </span>`;
                    };
                    if (d.asset_type === "hardware"){
                        return `<span data-bs-toggle="tooltip" aria-label="${config.translations.Device_Tag}" data-bs-original-title="${config.translations.Device_Tag}" title="${config.translations.Device_Tag}">
                            ${d.asset_tag || '-'}
                        </span>
                        -
                        <span data-bs-toggle="tooltip" aria-label="${config.translations.Device_Model}" data-bs-original-title="${config.translations.Device_Model}" title="${config.translations.Device_Model}">
                            ${d.device_model || ''}
                        </span>`;
                    };
                    if (d.asset_type === "software"){
                        return `<span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.License_Tag}" aria-label="${config.translations.License_Tag}" title="${config.translations.License_Tag}">
                            ${d.lic_batch_no || '-'}
                        </span>
                        -
                        <span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.License_Name}" aria-label="${config.translations.License_Name}" title="${config.translations.License_Name}">
                            ${d.lic_name || ''}
                        </span>`;
                    };
                    if (d.asset_type === "consumable"){
                        return `<span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.Consumable_Tag}" aria-label="${config.translations.Consumable_Tag}" data-bs-placement="top" title="${config.translations.Consumable_Tag}">
                            ${d.con_batch_no || '-'}
                        </span>
                        -
                        <span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.Consumable_Name}" title="${config.translations.Consumable_Name}" aria-label="${config.translations.Consumable_Name}" data-bs-placement="top">
                            ${d.consumable_name || ''}
                        </span>`;
                    };
                    if (d.asset_type === "component"){
                        return `<span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.Component_Tag}" aria-label="${config.translations.Component_Tag}" data-bs-placement="top" title="${config.translations.Component_Tag}">
                            ${d.comp_id || '-'}
                        </span>
                        -
                        <span data-bs-toggle="tooltip" data-bs-original-title="${config.translations.Component_Name}" title="${config.translations.Component_Name}" aria-label="${config.translations.Component_Name}" data-bs-placement="top">
                            ${d.comp_name || ''}
                        </span>`;
                    };
                    return "-";
                }
            },
            { data: "a.note", defaultContent: "-" }
        ]
    });
}

function createUserHistoryTab(config) {
    var userHistoryModal = {
        mdl: null,
        mdltitle: null,
        mdltbody: null
    };
    var pageLengthSelect = $(".user-history-page-length");
    var tab = new ApiTableTab({
        content : $(".main-content"),
        trigger : "#user-history-tab-link",
        pane : "#user-history",
        table : "#user-history-tab",
        controlKey: "user-history",
        config : config,
        serverSide: true,
        lengthChange: false, 
        pageLength: parseInt(pageLengthSelect.val(), 10) || 10, 
        order: [
                [2, 'desc']
            ],
        ajax: {
            url : config.url.user_base_history,
            type: "GET",
            data: function (d) { 
                d.user_id = config.user_id;
                d.length = pageLengthSelect.val() || 10;
                d.start = d.start || 0;
            }
        },
        columns: [
          {
              data: "a.action",
              defaultContent: "-",
              render: function (data, type, row) {
                  if (type !== 'display') return data;
                  if (!data) return "-";

                  return `<span class="b5-text" style="color:#515151">${data}</span>`;
              }
          },
          {
              data: "a.full_name",
              defaultContent: "-",
              render: function (data, type, row) {
                  if (type !== 'display') return data;

                  var name = data || "-";
                  var img = row.a.performed_by_profile_img;

                  var avatarHtml = img
                      ? `<img src="${img}" class="rounded-circle me-2" width="28" height="28" alt="${name}">`
                      : `<span class="rounded-circle me-2 avatar-placeholder d-inline-flex align-items-center justify-content-center" style="width:28px;height:28px;">${name.charAt(0).toUpperCase()}</span>`;

                  return `<div class="d-flex align-items-center">${avatarHtml}<span>${name}</span></div>`;
              }
          },
          {
              data: "a.formatted_created_at",
              defaultContent: "-",
              render: function (data, type, row) {
                  if (type !== 'display') return data;
                  if (!data) return "-";

                  return `<span class="b5-text" style="color:#515151">${data}</span>`;
              }
          },
          {
              data: "a",
              searchable: false,
              orderable: false,
              render: function (data, type, row) {
                  return `<button type="button" class='amg-action-btn primary open-user-detail' data-bs-toggle='tooltip' data-placement="left" data-original-title='View Details' data-id="${data.id}">
                      <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                          <path d="M0 0h24v24H0z" fill="none" />
                          <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5" />
                      </svg>
                  </button>`;
              }
          },
      ],
        initComplete: function () {
            $("#user-history-tab_filter").hide();
            $("#user-history-tab_length").hide();
        }
    }); 
    
    var dataTableInstance = null;
    setTimeout(function() {
        if ($.fn.DataTable.isDataTable('#user-history-tab')) {
            dataTableInstance = $('#user-history-tab').DataTable();
        }
    }, 200);
    
    function handlePageLengthChange(e) {
        if (e) e.preventDefault();
        
        var length = parseInt(pageLengthSelect.val(), 10);
        if (!length) return;
        
        var dt = $('#user-history-tab').DataTable();
        if (dt) {
            dt.page.len(length).draw();
        }
    }
    
    $(document).on('change', '.user-history-page-length', function(e) {
        handlePageLengthChange(e);
    });
    
    setTimeout(function() {
        userHistoryModal.mdl = $("#user_history_modal");
        userHistoryModal.mdltitle = userHistoryModal.mdl.find('.modal-title');
        userHistoryModal.mdltbody = userHistoryModal.mdl.find('#user-history-table tbody');
    }, 100);
    
    var showUserHistory = function() {
    var user_id = $(this).data("id");
    
    if (!userHistoryModal.mdl || !userHistoryModal.mdl.length) {
        userHistoryModal.mdl = $("#user_history_modal");
        userHistoryModal.mdltitle = userHistoryModal.mdl.find('#title'); 
        userHistoryModal.mdltbody = userHistoryModal.mdl.find('#user-history-table tbody');
    }
    
    $.ajax({
        type: "GET",
        url: config.url.user_history_details,
        data: {
            user_id: user_id,
        },
        success: function (response) {
            var data = response.data;
            var action = response.action;
            userHistoryModal.mdltbody.empty();
            userHistoryModal.mdltitle.text('User History');
            
            if(action === 'updated') {
                var updateData = JSON.parse(data);
                for (var key in updateData) {
                    if (updateData.hasOwnProperty(key)) {
                        var oldVal = updateData[key].old;
                        var newVal = updateData[key].new;
                        var row = $('<tr class="table-border">');
                        row.append($('<td class="table-border">').html(key));
                        row.append($('<td class="table-border">').html(oldVal));
                        row.append($('<td class="table-border">').html(newVal));
                        userHistoryModal.mdltbody.append(row);
                    }
                }
            } else if(action === 'created') {
                var createdData = JSON.parse(data);
                for (var key in createdData) {
                    if (createdData.hasOwnProperty(key)) {
                        var oldVal = 'N/A';
                        var newVal = createdData[key] || '';
                        var row = $('<tr class="table-border">');
                        row.append($('<td class="table-border">').html(key));
                        row.append($('<td class="table-border">').html(oldVal));
                        row.append($('<td class="table-border">').html(newVal));
                        userHistoryModal.mdltbody.append(row);
                    }
                }
            } else if(action === 'deleted') {
                var deleteData = JSON.parse(data);
                for (var key in deleteData) {
                    if (deleteData.hasOwnProperty(key)) {
                        var oldVal = 'N/A';
                        var newVal = deleteData[key] || '';
                        var row = $('<tr class="table-border">');
                        row.append($('<td class="table-border">').html(key));
                        row.append($('<td class="table-border">').html(oldVal));
                        row.append($('<td class="table-border">').html(newVal));
                        userHistoryModal.mdltbody.append(row);
                    }
                }
            }
            userHistoryModal.mdl.modal('show');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error retrieving user history:", textStatus, errorThrown);
        }
    });
};
    
    $(document).on('click', '.open-user-detail', showUserHistory);
    $(document).on('click', '#user_history_modal .close', function() {
        $('#user_history_modal').modal('hide');
    });
    return tab;
}
