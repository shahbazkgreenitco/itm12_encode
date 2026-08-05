var POCompanyList = function (config) {
  var t = this;
  t.config = config;
  t.currentEditId = "";
  t.currentImage = null;
  t.httpPostPath = "";
  t.httpCall = true;

  t.content = $("#main-po-company-list-wrapper");
  t.table = t.content.find("#po_comapny");
  t.dTable = null;
  t.searchTimer = null;

  t.btn = {
    add: ".btn-add-po-company",
    reload: ".btn-reload-list",
    download: ".btn-download-report",
    openFilter: ".btn-open-filter",
    clearFilter: ".btn-clear-filter",
    filter: ".btn-filter"
  };

  t.modal = $("#po-company-mdl");
  t.modalTitle = t.modal.find("#po-company-mdl-title");
  t.frm = t.modal.find("#po-company-mdl-frm");
  t.loader = t.modal.find("#po-company-mdl-loader");

  /* form fields — same set as the old file, one shared modal for add + edit */
  t.el = {
    company: t.frm.find("#company"),
    logo: t.frm.find("#logo"),
    country: t.frm.find("#country"),
    state: t.frm.find("#state"),
    city: t.frm.find("#city"),
    zip: t.frm.find("#zip"),
    gstin: t.frm.find("#gstin"),
    email_id: t.frm.find("#email_id"),
    fax_id: t.frm.find("#fax_id"),
    address: t.frm.find("#address"),
    notes: t.frm.find("#notes"),
    contact_no: t.frm.find("#contact_no"),
    cin_number: t.frm.find("#cin_number"),
    payment_terms: t.frm.find("#payment_terms"),
    terms_conditions: t.frm.find("#terms_conditions")
  };
  if (t.config.client === "knightfrank") {
    t.el.delivery_terms = t.frm.find("#delivery_terms");
    t.el.warranty_and_support = t.frm.find("#warranty_and_support");
  }

  t.filters = {
    wrapper: $("#advanceFilterModal"),
    country: null,
    state: null,
    city: null,
    dateBasis: t.content.add($("#advanceFilterModal")).find("#filter_by_date"),
    daterange: $("#daterange")
  };

  t.init();
};

/* ============ init ============ */
POCompanyList.prototype.init = function () {
  var t = this;
  setTimeout(function () {
    t.initDataTable();
  }, 50);
  t.initFormSelect2();
  t.initFilterSelect2();
  t.initSummernote();
  t.initDateRange();
  t.bindEvents();
};

/* ============ events ============ */
POCompanyList.prototype.bindEvents = function () {
  var t = this;
  t.content.on("click", t.btn.add, $.proxy(t.handleAddClick, t));
  t.content.on("click", t.btn.reload, $.proxy(t.reload, t));
  t.content.on("click", t.btn.download, $.proxy(t.handleDownloadClick, t));
  t.content.on("click", t.btn.openFilter, $.proxy(t.handleOpenFilterClick, t));
  t.filters.wrapper.on("click", t.btn.filter, $.proxy(t.handleFilterClick, t));
  t.filters.wrapper.on("click", t.btn.clearFilter, $.proxy(t.handleClearFilterClick, t));
  t.content.on("keyup", ".po-company-list-search", $.proxy(t.handleSearchKeyup, t));

  t.table.on("click", ".btn-edit-po-company", $.proxy(t.handleEditClick, t));
  t.table.on("click", ".btn-delete-po-company", $.proxy(t.handleDeleteClick, t));

  t.modal.on("click", "#btnSubmit", $.proxy(t.handleSaveClick, t));
  t.modal.on("click", ".js-preview-logo", $.proxy(t.handlePreviewLogoClick, t));
  t.modal.find("#logo").on("change", $.proxy(t.handleLogoChange, t));
};

/* ============ DataTable — dom:"rtip" so the custom searchbar isn't duplicated ============ */
POCompanyList.prototype.initDataTable = function () {
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
    order: [[9, "desc"]],
    pagingType: "simple_numbers",
    ajax: {
      url: t.config.url.getPoCompanyByAjax,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.search = t.content.find(".po-company-list-search").val();
        d.countries = t.filters.country ? t.filters.country.val() : null;
        d.states = t.filters.state ? t.filters.state.val() : null;
        d.cities = t.filters.city ? t.filters.city.val() : null;
        d.based_on = t.filters.dateBasis.val();
        d.daterange = t.filters.daterange.val();
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
      { data: "a.company" },
      {
        data: "a",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return t.renderLogoCell(row.a || {}, type);
        }
      },
      { data: "a.co_name" },
      { data: "a.s_name" },
      { data: "a.c_name" },
      { data: "a.zip" },
      { data: "a.gstin" },
      { data: "a.created_date" },
      { data: "a.updated_date" }
    ]
  });
};

POCompanyList.prototype.renderActionsCell = function (record, type) {
  if (type !== "display") return "";
  var actions = "";
  // NOTE: old file had these permission checks commented out too — preserved as-is,
  // uncomment when config.permissions is actually enforced for this module.
  // if ($.inArray("PoCompanyEdit", this.config.permissions) !== -1) {
  actions += this.actionBtn("Edit", "btn-edit-po-company", record.id, "bi-pencil");
  // if ($.inArray("PoCompanyDelete", this.config.permissions) !== -1) {
  actions += this.actionBtn("Delete", "btn-delete-po-company", record.id, "bi-trash");
  return '<div class="d-flex gap-2 amg-row-actions">' + actions + "</div>";
};

POCompanyList.prototype.actionBtn = function (label, cls, id, icon) {
  return (
    '<button type="button" class="header-icon-btn-only header-icon-btn-only-sm ' + cls +
    '" data-id="' + id + '" data-bs-toggle="tooltip" title="' + label + '">' +
    '<i class="bi ' + icon + '"></i></button>'
  );
};

POCompanyList.prototype.renderLogoCell = function (record, type) {
  if (type !== "display") return "";
  if (!record.logo || record.logo === "undefined") return "";
  return '<img class="logo-thumb" src="' + this.config.url.path + "/" + record.logo + '">';
};

/* ============ list controls ============ */
POCompanyList.prototype.reload = function () {
  if (this.dTable) this.dTable.ajax.reload(null, false);
};

POCompanyList.prototype.handleSearchKeyup = function (e) {
  var t = this;
  if (e.keyCode !== 13 && e.target.value.length !== 0) return;
  clearTimeout(t.searchTimer);
  t.searchTimer = setTimeout(function () {
    t.reload();
  }, 300);
};

POCompanyList.prototype.handleDownloadClick = function (e) {
  var t = this;
  var jobj = { search: t.content.find(".po-company-list-search").val(), other_filters: t.cacheFilterValues() };
  var exportFilters = btoa(JSON.stringify(jobj));
  window.location = t.config.url.poCompanyExport + "?q=" + exportFilters;
};

/* ============ filters ============ */
POCompanyList.prototype.handleOpenFilterClick = function () {
  this.toggleModal("#advanceFilterModal", true);
};

POCompanyList.prototype.handleFilterClick = function () {
  var t = this;
  t.updateFilterCount();
  t.toggleModal("#advanceFilterModal", false);
  t.reload();
};

POCompanyList.prototype.handleClearFilterClick = function () {
  var t = this;
  if (t.filters.country) t.filters.country.val("").trigger("change");
  if (t.filters.state) t.filters.state.val("").trigger("change");
  if (t.filters.city) t.filters.city.val("").trigger("change");
  t.filters.dateBasis.val("null");
  t.filters.daterange.val("");
  $("#reportrange-label").text("");
  t.updateFilterCount();
  t.reload();
};

POCompanyList.prototype.cacheFilterValues = function () {
  var t = this;
  var out = {};
  var countryVal = t.filters.country ? t.filters.country.val() : null;
  var stateVal = t.filters.state ? t.filters.state.val() : null;
  var cityVal = t.filters.city ? t.filters.city.val() : null;
  if (countryVal && countryVal !== "null") out.country_id = countryVal;
  if (stateVal && stateVal !== "null") out.state_id = stateVal;
  if (cityVal && cityVal !== "null") out.city_id = cityVal;
  if (t.filters.dateBasis.val() && t.filters.dateBasis.val() !== "null") out.based_on = t.filters.dateBasis.val();
  if (t.filters.daterange.val() && t.filters.daterange.val() !== "null") out.daterange = t.filters.daterange.val();
  return out;
};

POCompanyList.prototype.updateFilterCount = function () {
  var count = Object.keys(this.cacheFilterValues()).length;
  this.content.find(".filter-count-badge").text(count).toggleClass("d-none", count === 0);
};

POCompanyList.prototype.initDateRange = function () {
  var t = this;
  if (!$.fn.daterangepicker) return;
  $("#reportrange").daterangepicker({ autoUpdateInput: false }, function (start, end) {
    $("#reportrange-label").text(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
    t.filters.daterange.val(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
  });
};

/* ============ filter select2 (country -> state -> city, cascading) ============ */
POCompanyList.prototype.initFilterSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;
  var tr = t.config.translations;
  var s2 = { width: "100%", dropdownParent: t.filters.wrapper };

  t.filters.country = t.filters.wrapper.find("#filter_country").select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.country,
      dataType: "json",
      method: "get",
      data: function (p) { return { search: p.term, page: p.page || 1 }; }
    },
    allowClear: true,
    placeholder: tr.Select_Country
  })).on("select2:select", function () {
    t.filters.state.val("").trigger("change");
    t.filters.city.val("").trigger("change");
  });

  t.filters.state = t.filters.wrapper.find("#filter_state").select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.state,
      dataType: "json",
      data: function (p) { return { search: p.term, country_id: t.filters.country.val(), page: p.page || 1 }; },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.Select_State
  })).on("select2:select", function () {
    t.filters.city.val("").trigger("change");
  });

  t.filters.city = t.filters.wrapper.find("#filter_city").select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.city,
      dataType: "json",
      data: function (p) { return { search: p.term, state_id: t.filters.state.val(), page: p.page || 1 }; },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.Select_City
  }));
};

/* ============ form select2 (add/edit modal, cascading) ============ */
POCompanyList.prototype.initFormSelect2 = function () {
  var t = this;
  if (!$.fn.select2) return;
  var tr = t.config.translations;
  var s2 = { width: "100%", dropdownParent: t.modal };

  t.el.country.select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.country,
      dataType: "json",
      method: "get",
      data: function (p) { return { search: p.term, page: p.page || 1 }; }
    },
    allowClear: true,
    placeholder: tr.Select_Country
  })).on("select2:select", function () {
    t.el.state.val("").trigger("change");
    t.el.city.val("").trigger("change");
  });

  t.el.state.select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.state,
      dataType: "json",
      data: function (p) { return { search: p.term, country_id: t.el.country.val(), page: p.page || 1 }; },
      beforeSend: function () {
        if (!t.el.country.val()) t.showToast("error", tr.Please_select_country);
      },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.Select_State
  })).on("select2:select", function () {
    t.el.city.val("").trigger("change");
  });

  t.el.city.select2($.extend({}, s2, {
    ajax: {
      url: t.config.url.city,
      dataType: "json",
      data: function (p) { return { search: p.term, state_id: t.el.state.val(), page: p.page || 1 }; },
      beforeSend: function () {
        var state = t.el.state.val();
        var country = t.el.country.val();
        if (!country && !state) t.showToast("error", tr.Please_select_country_and_state);
        else if (!state) t.showToast("error", tr.Please_select_state);
      },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.Select_City
  }));
};

/* ============ summernote (rich text fields) ============ */
POCompanyList.prototype.initSummernote = function () {
  var t = this;
  if (!$.fn.summernote) return;
  var opts = {
    toolbar: [["color", ["color"]], ["style", ["bold", "italic", "underline", "clear"]], ["para", ["ul", "ol"]]],
    minHeight: 200
  };
  t.el.address.summernote(opts);
  t.el.notes.summernote(opts);
  t.el.terms_conditions.summernote(opts);
  t.el.payment_terms.summernote(opts);
  if (t.config.client === "knightfrank") {
    t.el.delivery_terms.summernote(opts);
    t.el.warranty_and_support.summernote(opts);
  }
};

/* ============ Add ============ */
POCompanyList.prototype.handleAddClick = function () {
  var t = this;
  t.frm[0].reset();
  t.el.country.val("").trigger("change");
  t.el.state.empty().trigger("change");
  t.el.city.empty().trigger("change");
  t.el.address.summernote("code", "");
  t.el.notes.summernote("code", "");
  t.el.terms_conditions.summernote("code", "");
  t.el.payment_terms.summernote("code", "");
  if (t.config.client === "knightfrank") {
    t.el.delivery_terms.summernote("code", "");
    t.el.warranty_and_support.summernote("code", "");
  }
  t.modal.find("#shows_error").html("");
  t.modal.find("#img_edit").remove();
  t.modal.find("#btnSubmit").removeClass("disabled");
  t.currentImage = null;
  t.currentEditId = "";
  t.httpPostPath = t.config.url.add;
  t.modalTitle.text(t.config.translations.Add_po_company);
  t.toggleModal("#po-company-mdl", true);
};

/* ============ Edit ============ */
POCompanyList.prototype.handleEditClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");
  t.frm[0].reset();
  t.modal.find("#shows_error").html("");
  t.modal.find("#img_edit").remove();
  t.modal.find("#btnSubmit").removeClass("disabled");
  t.loader.addClass("d-none");
  t.modalTitle.text(t.config.translations.Edit_po_company);
  t.httpPostPath = t.config.url.update;

  $.get(t.config.url.getPoCompanyByAjaxForEdit + "/" + id).done(function (data) {
    if (typeof data !== "object") return;
    if (data.status !== "success") {
      t.showToast("error", data.msg);
      return;
    }
    var d = data.data;
    if (d.dropdown && d.dropdown.country) {
      t.el.country.empty().append(new Option(d.dropdown.country.text, d.dropdown.country.id)).trigger("change");
    }
    if (d.dropdown && d.dropdown.state) {
      t.el.state.empty().append(new Option(d.dropdown.state.text, d.dropdown.state.id)).trigger("change");
    }
    if (d.dropdown && d.dropdown.city) {
      t.el.city.empty().append(new Option(d.dropdown.city.text, d.dropdown.city.id)).trigger("change");
    }
    t.currentEditId = d.id;
    t.el.company.val(d.company);
    t.el.gstin.val(d.gstin);
    t.el.email_id.val(d.email_id);
    t.el.fax_id.val(d.fax_id);
    t.el.zip.val(d.zip);
    t.el.contact_no.val(d.contact_no);
    t.el.cin_number.val(d.cin_number);
    t.el.address.summernote("code", d.address || "");
    t.el.notes.summernote("code", d.notes || "");
    t.el.terms_conditions.summernote("code", d.terms_conditions || "");
    t.el.payment_terms.summernote("code", d.payment_terms || "");
    if (t.config.client === "knightfrank") {
      t.el.delivery_terms.summernote("code", d.delivery_terms || "");
      t.el.warranty_and_support.summernote("code", d.warranty_and_support || "");
    }
    if (d.logo && d.logo !== "undefined") {
      t.el.logo.closest(".input-group").after('<img src="' + t.config.url.path + "/" + d.logo + '" id="img_edit" width="70" class="mt-2">');
      t.currentImage = t.config.url.path + "/" + d.logo;
      t.modal.find("#eye-button-visiable").removeClass("d-none");
    } else {
      t.modal.find("#eye-button-visiable").addClass("d-none");
    }
    t.toggleModal("#po-company-mdl", true);
  }).fail(function () {
    t.showToast("error", t.config.translations.something_went_wrong);
  });
};

/* ============ Save (Add or Edit — same endpoint switch as old file via httpPostPath) ============ */
POCompanyList.prototype.handleSaveClick = function (e) {
  var t = this;
  e.preventDefault();

  var addressText = t.el.address.summernote("code")
    .replace(/<p><br><\/p>/gi, "").replace(/<br>/gi, "")
    .replace(/<\/?[^>]+(>|$)/g, "").replace(/&nbsp;/g, "").trim();
  if (!t.el.company.val() || !t.el.country.val() || !t.el.state.val() || !t.el.city.val() ||
      !t.el.zip.val() || !t.el.gstin.val() || addressText.length <= 0) {
    if (addressText.length <= 0) {
      t.modal.find("#shows_error").text("This field is required.");
    }
    return false;
  }
  t.modal.find("#shows_error").text("");

  t.loader.removeClass("d-none");
  t.modal.find("#btnSubmit").addClass("disabled");
  t.httpCall = false;

  var formData = new FormData(t.frm[0]);
  var photo = t.el.logo.prop("files")[0];
  formData.append("logo", photo);
  formData.append("company", t.el.company.val());
  formData.append("country", t.el.country.val());
  formData.append("state", t.el.state.val());
  formData.append("city", t.el.city.val());
  formData.append("address", t.el.address.summernote("code"));
  formData.append("payment_terms", t.el.payment_terms.summernote("code"));
  formData.append("terms_conditions", t.el.terms_conditions.summernote("code"));
  formData.append("notes", t.el.notes.summernote("code"));
  formData.append("zip", t.el.zip.val());
  formData.append("gstin", t.el.gstin.val());
  formData.append("email_id", t.el.email_id.val());
  formData.append("fax_id", t.el.fax_id.val());
  formData.append("id", t.currentEditId);
  if (t.config.client === "knightfrank") {
    formData.append("delivery_terms", t.el.delivery_terms.summernote("code"));
    formData.append("warranty_and_support", t.el.warranty_and_support.summernote("code"));
  }

  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    contentType: false,
    processData: false,
    cache: false,
    data: formData
  }).done(function (data) {
    if (typeof data === "object") {
      if (data.status === "success") {
        t.toggleModal("#po-company-mdl", false);
        t.showToast("success", data.msg);
        t.reload();
      } else {
        t.showToast("error", data.msg);
        t.reload();
      }
    }
  }).fail(function () {
    t.showToast("error", t.config.translations.something_went_wrong);
  }).always(function () {
    t.httpCall = true;
    t.loader.addClass("d-none");
    t.modal.find("#btnSubmit").removeClass("disabled");
  });
};

/* ============ Delete — preserves the old file's exact GET method (not switched to POST) ============ */
POCompanyList.prototype.handleDeleteClick = function (e) {
  var t = this;
  var id = $(e.currentTarget).data("id");
  var url = t.config.url.delete + "/" + id;

  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: "warning",
      title: t.config.translations.Filter ? undefined : undefined,
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

POCompanyList.prototype.doDelete = function (url) {
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

/* ============ logo preview ============ */
POCompanyList.prototype.handleLogoChange = function (e) {
  var t = this;
  var file = e.target.files && e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith("image/")) {
    t.showToast("warning", "Please select a valid image file");
    e.target.value = "";
    return;
  }
  var reader = new FileReader();
  reader.onload = function (ev) {
    t.currentImage = ev.target.result;
  };
  reader.readAsDataURL(file);
};

POCompanyList.prototype.handlePreviewLogoClick = function () {
  var t = this;
  if (!t.currentImage) {
    t.showToast("warning", t.config.translations.select_image);
    return;
  }
  t.modal.find("#image-preview").attr("src", t.currentImage);
  t.toggleModal("#imagePreviewModal", true);
};

/* ============ shared helpers ============ */
POCompanyList.prototype.toggleModal = function (selector, show) {
  var modal = $(selector);
  if (!modal.length) return;
  if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
    window.bootstrap.Modal.getOrCreateInstance(modal[0])[show ? "show" : "hide"]();
  } else if (typeof modal.modal === "function") {
    modal.modal(show ? "show" : "hide");
  }
};

POCompanyList.prototype.showToast = function (icon, message) {
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({ icon: icon, text: message, showConfirmButton: true, allowOutsideClick: false });
  } else {
    alert(message);
  }
};
