
var MyApp = function (config) {
  var t = this;

  t.config = config || {};
  t.showDeletedDevices = false;
  t.httpCall = true;
  t.selectedIds = {};
  t.isLoading = false;
  t.currentPage = 1;
  t.totalPages = 1;
  t.totalRecords = 0;
  t.perPage = 10;
  t.sortField = 7;
  t.sortDirection = 2;
  t.expandedRows = {};
  t.rowDetailCache = {};
  t.internal_places = [];
  t.current_purchase_date = "";
  t.current_supplier_name = "";
  t.current_supplier_id = "";
  t.current_status = "";
  t.temp_id = "";
  t.order_no = "";
  t.purchase_cost = "";
  t.currency_default = "";
  t.d_id = "";
  t.httpPostPath = "";
  t.mdl_forAction = "";
  t.config.search = t.config.search || "";
  t.config.other_filters = t.config.other_filters || {};
  t.config.export_filters = t.config.export_filters || "";
  t.config.print_device_array = t.config.print_device_array || [];
  t.config.device_id_array = t.config.device_id_array || [];
  t._printSelectedIds = [];
  t.content = $("#main-user-list-wrapper");
  t.tableBody = t.content.find("#deviceTableBody");
  t.searchTimer = null;
  t.actionMenuHideTimer = null;
  t.print_label_starter = $("#print_label_starter");
  t.selected_company_id = null;
  t.selectedTransferIds = [];
  t.transferHttpCall = true;
  t.transferHttpPostPath = "";


  t.btn = {
    add: ".btn-add-device",
    deleted: ".btn-deleted-devices",
    reload: ".btn-reload-list",
    print_label: ".btn-print-label",
    import: ".btn-import-device",
    bulk_checkout: ".btn-bulk-checkout",
    bulk_checkin: ".btn-bulk-checkin",
    bulk_update: ".btn-bulk-update",
    bulk_reminder: ".btn-bulk-send-reminder",
    export_excel: ".btn-export-device",
    export_pdf: ".btn-export-device-pdf",
    bulk_delete: ".btn-bulk-delete",
    filter_toggle: ".btn-open-filter",
    filter_apply: "#filter",
    filter_clear: "#clear",
    search_input: ".device-list-search",
    page_length: ".device-list-page-length",
    expand: ".device-list-expand",
    showAddedToPrintDevices: ".btn-show-print-devices",
    bulk_transfer: ".btn-bulk-transfer",
  };

  t.mdl.device = $("#device-mdl");
  t.mdl.checkout = $("#device-checkout-mdl");
  t.mdl.checkin = $("#device-checkin-mdl");
  t.mdl.resale = $("#device-resale-mdl");
  t.mdl.expense = $("#dev-main-mdl");
  t.mdl.print = $("#print-modal");
  t.mdl.supplier = $("#supplier-mdl-box");
  t.mdl.transfer = $("#transer-mdl");
  t.mdl.merge = $("#mergeMdl");

  t.filters = {
    wrapper: $("#deviceFilterModal"),
    //  filters
    manufacturer: null,
    model: null,
    location: null,
    category: null,
    assigned_to: null,
    filter_by_date: null,
    from_date: null,
    to_date: null,
    assigned_place: null,
    stock_place: null,
    asset_type: null,
    device_occure_type: null,
    last_checkout_project: null,
    filter_by_dept: null,
    filter_asset_owner: null,
    filter_by_assigned_to: null,
    filter_by_added_from: null,
    filter_by_warranty_status: null,
    filter_by_allocation_type: null,
    filter_by_audit_confirmation: null,
    filter_by_asset_tag: null,
    filter_by_user_location: null,
    filter_by_user_base_location: null,
    stock_location: null,
    user_internalplace: null,
    purchase_reference: null,
    assigned_user: null,
    department: null,
    based_on: null,
    date_range: null,
    detected_on: null,
    sez_device: null,
    high_priority_device: null,
    under_transfer_device: null,
    requestable: null,
    condition_by_user_assign: null,
    filter_by_device_size: null,
    rdp_status: null,
    rfid: null,
    assetDepartment: null,
    internal_place: null,
    custom_field: null,
    custom_field_value: null,
    mapped_location: null,
    filter_by_device_with_rfid: null,
    filter_by_reason_type: null,
    filter_by_reason: null,
    search: null,
  };


  t.listUi = {
    pageLength: $(),
    searchInput: $(),
    activeView: "list",
    pagination: $(),
    summary: $(),
  };

  t.init();
  t.mergeObj = new MergeMdl({
    config: t.config,
    mdl: t.mdl.merge
  });

};

MyApp.prototype.init = function () {
  var t = this;
  t.initListControls();
  t.initFilters();
  t.bindFilterEvents();
  t.initModals();
  t.bindEvents();
  t.loadDevices();
  // t.initPrintModalEvents()
};

MyApp.prototype.initModals = function () {
  var t = this;
  var select2Opts = { width: "100%" };

  //Device Modal
  t.mdl.device.frm = t.mdl.device.find("#device-mdl-frm");
  t.mdl.device.frmEl = {};
  t.mdl.device.frmEl.id = t.mdl.device.frm.find("#id");
  t.mdl.device.frmEl.forAction = t.mdl.device.frm.find("#forAction");
  t.mdl.device.frmEl.clone_img = t.mdl.device.frm.find("#clone_img");
  t.mdl.device.frmEl.company = t.mdl.device.frm.find("#company_id");
  t.mdl.device.frmEl.model = t.mdl.device.frm.find("#model_id");
  t.mdl.device.frmEl.asset_owner = t.mdl.device.frm.find("#asset_owner");
  t.mdl.device.frmEl.supplier = t.mdl.device.frm.find("#supplier_id");
  t.mdl.device.frmEl.invoice_id = t.mdl.device.frm.find("#invoice_id");
  t.mdl.device.frmEl.uuid = t.mdl.device.frm.find("#uuid");
  t.mdl.device.frmEl.product_number = t.mdl.device.frm.find("#product_number");
  t.mdl.device.frmEl.stock_place = t.mdl.device.frm.find("#stock_place");
  t.mdl.device.frmEl.location = t.mdl.device.frm.find("#rtd_location_id");
  t.mdl.device.frmEl.internal_place = t.mdl.device.frm.find("#internal_place");
  t.mdl.device.frmEl.statusLabel = t.mdl.device.frm.find("#status_id");
  t.mdl.device.frmEl.checkout_reason = t.mdl.device.frm.find("#checkout_reason");
  t.mdl.device.frmEl.asset_type_id = t.mdl.device.frm.find("#asset_type_id");
  t.mdl.device.frmEl.device_occure_type = t.mdl.device.frm.find("#device_occure_type");
  t.mdl.device.frmEl.lease_id = t.mdl.device.frm.find("#lease_id");
  t.mdl.device.frmEl.purchase_date = t.mdl.device.frm.find("#purchase_date");
  t.mdl.device.frmEl.assigned_for = t.mdl.device.frm.find("#assigned_for");
  t.mdl.device.frmEl.assigned_to = t.mdl.device.frm.find("#assigned_to");
  t.mdl.device.frmEl.warranty_start_date = t.mdl.device.frm.find("#warranty_start_date");
  t.mdl.device.frmEl.warrenty_end_date = t.mdl.device.frm.find("#warrenty_end_date");
  t.mdl.device.frmEl.last_checkout_project = t.mdl.device.frm.find("#last_checkout_project");
  t.mdl.device.frmEl.assigned_place = t.mdl.device.frm.find("#assigned_place");
  t.mdl.device.frmEl.asset_tag = t.mdl.device.frm.find("#asset_tag");
  t.mdl.device.frmEl.purchase_currency = t.mdl.device.frm.find("#purchase_currency");
  t.mdl.device.frmEl.amc_supplier_id = t.mdl.device.frm.find("#amc_supplier_id");
  t.mdl.device.frmEl.amc_expire_date = t.mdl.device.frm.find("#amc_expire_date");
  t.mdl.device.frmEl.ip = t.mdl.device.frm.find("#ip");
  t.mdl.device.frmEl.mac = t.mdl.device.frm.find("#mac");
  t.mdl.device.frmEl.scanner = t.mdl.device.frm.find("#scanner_id");
  t.mdl.device.frmEl.deviceRfid = t.mdl.device.frm.find("#device_rfid");
  t.mdl.device.frmEl.inAntenna = t.mdl.device.frm.find("#in_antenna");
  t.mdl.device.frmEl.outAntenna = t.mdl.device.frm.find("#out_antenna");
  t.mdl.device.frmEl.department_id = t.mdl.device.frm.find("#department_id");
  t.mdl.device.frmEl.expected_checkin = t.mdl.device.frm.find("#expected_checkin");
  t.mdl.device.frmEl.project_name = t.mdl.device.frm.find("#project_name");
  t.mdl.device.frmEl.allocation_type = t.mdl.device.frm.find("#allocation_type");
  t.mdl.device.frmEl.temp_id = t.mdl.device.frm.find("#temp_id");
  t.mdl.device.frmEl.notes = t.mdl.device.frm.find("#notes");
  t.mdl.device.frmEl.name = t.mdl.device.frm.find("#name");
  t.mdl.device.frmEl.serial = t.mdl.device.frm.find("#serial");
  t.mdl.device.frmEl.order_number = t.mdl.device.frm.find("#order_number");
  t.mdl.device.frmEl.purchase_cost = t.mdl.device.frm.find("#purchase_cost");
  t.mdl.device.frmEl.warranty_months = t.mdl.device.frm.find("#warranty_months");
  t.mdl.device.frmEl.image = t.mdl.device.frm.find("#image");
  t.mdl.device.frmEl.requestable = t.mdl.device.frm.find("#requestable");
  t.mdl.device.frmEl.sez_device = t.mdl.device.frm.find("#sez_device");
  t.mdl.device.frmEl.live_monitor = t.mdl.device.frm.find("#live_monitor");
  t.mdl.device.frmEl.high_pririty = t.mdl.device.frm.find("#high_pririty");
  t.mdl.device.forAction = "";
  t.mdl.device.imgview = t.mdl.device.find("#imgview");
  t.mdl.device.imgviewcover = t.mdl.device.find(".imgviewcover");
  t.mdl.device.customFieldsPrvEl = t.mdl.device.find(".custom-fields-follow");
  t.mdl.device.btnSubmit = t.mdl.device.find('#btnSubmit');
  t.mdl.device.btnClear = t.mdl.device.find('#btnClear');
  t.mdl.device.title = t.mdl.device.find('.modal-title');
  t.mdl.device.cover = t.mdl.device.frm.find(".cover");
  t.mdl.device.checkoutcover = t.mdl.device.frm.find(".checkoutcover");
  t.mdl.device.leasecover = t.mdl.device.frm.find(".leasecover");
  t.mdl.device.purchasecover = t.mdl.device.frm.find(".purchasecover");
  t.mdl.device.stockcover = t.mdl.device.frm.find(".stockcover");
  t.mdl.device.usercover = t.mdl.device.frm.find(".usercover");
  t.mdl.device.allocationcover = t.mdl.device.frm.find(".allocationcover");
  t.mdl.device.placecover = t.mdl.device.frm.find(".placecover");
  t.mdl.device.expected_checkin_div = t.mdl.device.frm.find('.expected_checkin_cover');
  t.mdl.device.project_name_cover = t.mdl.device.frm.find('.project_name_cover');
  t.mdl.device.allocation_type_cover = t.mdl.device.frm.find('.allocation_type_cover');
  t.mdl.device.reasoncover = t.mdl.device.frm.find('.reasoncover');
  t.mdl.device.live_monitor_field = t.mdl.device.find('.live_monitor_hidden_field');
  t.mdl.device.tabs = $("section.content").find("#mytabs");

  //Checkout Modal
  t.mdl.checkout.frm = t.mdl.checkout.find("#device-checkout-mdl-frm");
  t.mdl.checkout.frmEl = {};
  t.mdl.checkout.frmEl.id = t.mdl.checkout.frm.find("#id");
  t.mdl.checkout.frmEl.name = t.mdl.checkout.frm.find("#name");
  t.mdl.checkout.frmEl.assigned_for = t.mdl.checkout.frm.find("#assigned_for");
  t.mdl.checkout.frmEl.last_checkout_project = t.mdl.checkout.frm.find("#last_checkout_project");
  t.mdl.checkout.frmEl.checkout_reason = t.mdl.checkout.frm.find("#checkout_reason");
  t.mdl.checkout.frmEl.allocation_status = t.mdl.checkout.frm.find("#allocation_status");
  t.mdl.checkout.frmEl.assigned_to = t.mdl.checkout.frm.find("#assigned_to");
  t.mdl.checkout.frmEl.assigned_place = t.mdl.checkout.frm.find("#assigned_place");
  t.mdl.checkout.frmEl.checkout_at = t.mdl.checkout.frm.find("#checkout_at");
  t.mdl.checkout.frmEl.expected_checkin = t.mdl.checkout.frm.find("#expected_checkin");
  t.mdl.checkout.frmEl.rate = t.mdl.checkout.frm.find("#rate");
  t.mdl.checkout.frmEl.note = t.mdl.checkout.frm.find("#note");
  t.mdl.checkout.lblDeviceTag = t.mdl.checkout.find("#lblDeviceTag");
  t.mdl.checkout.btnSubmit = t.mdl.checkout.find('#btnSubmit');
  t.mdl.checkout.btnClear = t.mdl.checkout.find('#btnClear');
  t.mdl.checkout.billable = t.mdl.checkout.find('input[name=billable]:radio');
  t.mdl.checkout.billable_div = t.mdl.checkout.find("#billable_div");
  t.mdl.checkout.rate_cost = t.mdl.checkout.find("#rate_cost");
  t.mdl.checkout.company_id = null;
  t.mdl.checkout.location_id = null;
  t.mdl_popup_loader_checkout = t.mdl.checkout.find("#mdl_popup_loader");

  // Checkin Modal
  t.mdl.checkin.frm = t.mdl.checkin.find("#device-checkin-mdl-frm");
  t.mdl.checkin.frmEl = {};
  t.mdl.checkin.frmEl.id = t.mdl.checkin.frm.find("#id");
  t.mdl.checkin.frmEl.status_id = t.mdl.checkin.frm.find("#status_id");
  t.mdl.checkin.frmEl.stock_place = t.mdl.checkin.frm.find("#stock_place");
  t.mdl.checkin.frmEl.checkin_reason = t.mdl.checkin.frm.find("#checkin_reason");
  t.mdl.checkin.frmEl.checkin_at = t.mdl.checkin.frm.find("#checkin_at");
  t.mdl.checkin.frmEl.note = t.mdl.checkin.frm.find("#note");
  t.mdl.checkin.lblDeviceTag = t.mdl.checkin.find("#lblDeviceTag");
  t.mdl.checkin.lblDeviceName = t.mdl.checkin.find("#lblDeviceName");
  t.mdl.checkin.btnSubmit = t.mdl.checkin.find('#btnSubmit');
  t.mdl.checkin.btnClear = t.mdl.checkin.find('#btnClear');
  t.mdl.checkin.company_id = null;
  t.mdl_popup_loader_checkin = t.mdl.checkin.find("#mdl_popup_loader");

  //Resale Modal
  t.mdl.resale.frm = t.mdl.resale.find("#device-resale-mdl-frm");
  t.mdl.resale.frmEl = {};
  t.mdl.resale.frmEl.id = t.mdl.resale.frm.find("#id");
  t.mdl.resale.frmEl.sold_by = t.mdl.resale.frm.find("#sold_by");
  t.mdl.resale.frmEl.sold_at = t.mdl.resale.frm.find("#sold_at");
  t.mdl.resale.frmEl.sold_value_format = t.mdl.resale.frm.find("#sold_value_format");
  t.mdl.resale.frmEl.sold_value = t.mdl.resale.frm.find("#sold_value");
  t.mdl.resale.frmEl.asset_status = t.mdl.resale.frm.find("#asset_status");
  t.mdl.resale.frmEl.addSupplier = t.mdl.resale.frm.find("#addSupplier");
  t.mdl.resale.frmEl.device_tag = t.mdl.resale.frm.find("#device_tag");
  t.mdl.resale.frmEl.vendor = t.mdl.resale.frm.find("#vendor");
  t.mdl.resale.frmEl.org_name = t.mdl.resale.frm.find("#org_name");
  t.mdl.resale.frmEl.reference_no = t.mdl.resale.frm.find("#reference_no");
  t.mdl.resale.frmEl.resale_notes = t.mdl.resale.frm.find("#resale_notes");
  t.mdl.resale.btnSubmit = t.mdl.resale.find('#btnSubmit');
  t.mdl.resale.btnClear = t.mdl.resale.find('#btnClear');
  t.mdl.resale.company_id = null;

  //Print Modal
  t.mdl.print.frm = t.mdl.print.find("#print-modal-form");
  t.mdl.print.frmEl = {};
  t.mdl.print.frmEl.print_opt = t.mdl.print.frm.find("#print_opt");
  t.mdl.print.frmEl.print_usr_plc = t.mdl.print.frm.find("#print_usr_plc");
  t.mdl.print.frmEl.print_usr_info = t.mdl.print.frm.find("#print_usr_info");
  t.mdl.print.frmEl.print_usr_info_cvr = t.mdl.print.frmEl.print_usr_info.closest('.cvr');
  t.mdl.print.btnSubmit = t.mdl.print.find('#btnSubmit');
  t.mdl.print.btnClear = t.mdl.print.find('#btnClear');
  t.mdl.print.title = t.mdl.print.find('.modal-title');

  //Expense Modal
  t.mdl.expense.frm = t.mdl.expense.find("#dev-main-mdl-frm");
  t.mdl.expense.frmEl = {};
  t.mdl.expense.frmEl.id = t.mdl.expense.frm.find("#id");
  t.mdl.expense.frmEl.forAction = t.mdl.expense.frm.find("#forAction");
  t.mdl.expense.frmEl.device = t.mdl.expense.frm.find("input[name='asset_id']");
  t.mdl.expense.frmEl.supplier = t.mdl.expense.frm.find("#supplier_id");
  t.mdl.expense.frmEl.types = t.mdl.expense.frm.find("#expense_type");
  t.mdl.expense.frmEl.expense_date = t.mdl.expense.frm.find("#expense_date");
  t.mdl.expense.frmEl.currency_format = t.mdl.expense.frm.find("#currency_format");
  t.mdl.expense.frmEl.end_date = t.mdl.expense.frm.find("#completion_date");
  t.mdl.expense.frmEl.notes = t.mdl.expense.frm.find("#notes");
  t.mdl.expense.frmEl.cost = t.mdl.expense.frm.find("#cost");
  t.mdl.expense.frmEl.temp_id = t.mdl.expense.frm.find("#temp_id");
  t.mdl.expense.title = t.mdl.expense.find('.modal-title');
  t.mdl.expense.btnSubmit = t.mdl.expense.find('#btnSubmit');
  t.mdl.expense.btnClear = t.mdl.expense.find('#btnClear');

  //Supplier Modal
  t.mdl.supplier.frm = t.mdl.supplier.find("#supplier-mdl-box-frm");
  t.mdl.supplier.frmEl = {};
  t.mdl.supplier.frmEl.supplier_name = t.mdl.supplier.frm.find("#supplier_name");
  t.mdl.supplier.frmEl.suppler_email = t.mdl.supplier.frm.find("#supplier_email");
  t.mdl.supplier.btnSubmit = t.mdl.supplier.find('#btnSubmit');
  t.mdl.supplier.btnClear = t.mdl.supplier.find('#btnClear');

  //Transfer Modal
  t.mdl.transfer.frm = t.mdl.transfer.find("#transer-mdl-frm");
  t.mdl.transfer.frmEl = {};
  t.mdl.transfer.frmEl.transfer_to = t.mdl.transfer.frm.find("#transfer_to");
  t.mdl.transfer.frmEl.expected_received_date = t.mdl.transfer.frm.find("#expected_received_date");
  t.mdl.transfer.frmEl.internal_place = t.mdl.transfer.frm.find("#internal_place");
  t.mdl.transfer.frmEl.cc_users = t.mdl.transfer.frm.find("#cc_users");
  t.mdl.transfer.frmEl.notes = t.mdl.transfer.frm.find("#notes");
  t.mdl.transfer.frmEl.responsible_user = t.mdl.transfer.frm.find("#responsible_user");
  t.mdl.transfer.btnSubmit1 = t.mdl.transfer.find('#btnSubmit1');
  t.mdl.transfer.btnClear = t.mdl.transfer.find('#btnClear1');
  t.mdl.transfer.title = t.mdl.transfer.find('.modal-title');

  //Datepickers
  t.mdl.device.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.device.frmEl.amc_expire_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.device.frmEl.warrenty_end_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.device.frmEl.warranty_start_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.device.frmEl.expected_checkin.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy"
  });
  t.mdl.checkout.frmEl.checkout_at.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.checkout.frmEl.expected_checkin.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.checkin.frmEl.checkin_at.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.resale.frmEl.sold_at.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
  t.mdl.expense.frmEl.expense_date.datepicker({ autoclose: true, format: "dd/mm/yyyy", endDate: "today" });
  t.mdl.transfer.frmEl.expected_received_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });

  //Select2
  t.mdl.device.frmEl.company.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.device.frmEl.company.parent() }));
  t.mdl.device.frmEl.model.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.model.parent(),
    ajax: {
      url: t.config.getModelByAjax,
      dataType: "json",
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1
        };
      },
      delay: 300
    },
    allowClear: true,
    //minimumInputLength: 1,
    placeholder: "Select the Model"
  }));
  t.mdl.device.frmEl.statusLabel.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.device }));
  t.mdl.device.frmEl.project_name.select2({
    placeholder: t.config.translations.select_project,
    dropdownParent: t.mdl.device,
    allowClear: true,
    width: '100%',
    ajax: {
      url: t.config.url.getProjectsByQuery,
      dataType: "json",
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1
        };
      },
      delay: 300
    },
  });
  t.mdl.device.frmEl.assigned_for.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.device.frmEl.assigned_for.parent(), data: t.config.assignedForOptions }));
  t.mdl.device.frmEl.device_occure_type.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.device.frmEl.device_occure_type.parent() }));
  t.mdl.device.frmEl.asset_type_id.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.asset_type_id.parent(),
    ajax: {
      url: t.config.url.getDeviceTypeByQuery,
      dataType: "json",
      delay: 300,
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1
        };
      }
    }
  }));
  t.mdl.device.frmEl.allocation_type
  t.mdl.device.frmEl.allocation_type.select2({
    placeholder: t.config.translations.select_project,
    dropdownParent: t.mdl.device,
    allowClear: true,
    width: '100%',
    ajax: {
      url: t.config.url.getAllocationByQuery,
      dataType: "json",
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1
        };
      },
      delay: 300
    },
  });
  t.mdl.device.frmEl.purchase_currency.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.device.frmEl.purchase_currency.parent() }));
  t.mdl.device.frmEl.deviceRfid.select2({ width: "100%", placeholder: "Enter RFID tags", tags: true });
  t.mdl.device.frmEl.inAntenna.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.inAntenna.parent(),
    placeholder: "Select In Antenna",
    allowClear: true,
    closeOnSelect: false
  }));

  t.mdl.device.frmEl.outAntenna.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.outAntenna.parent(),
    placeholder: "Select Out Antenna",
    allowClear: true,
    closeOnSelect: false
  }));

  t.mdl.device.frmEl.location.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.location.parent(),
    ajax: {
      url: t.config.getLocationByAjax,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: function () {
            return t.mdl.device.frmEl.company.val();
          }
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.select_the_location : "Select Location",
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
    },
  })).on("change", function (e) {
    t.getInternalPlace();
  });

  t.mdl.device.frmEl.stock_place.select2({
    width: "100%",
    dropdownParent: t.mdl.device.frmEl.stock_place.parent(),
    ajax: {
      url: t.config.ajaxGetInternalPlace,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          location_id: t.mdl.device.frmEl.location.val()
        };
      }
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.select_the_stock_place : "Select Stock Place"
  });

  t.mdl.device.frmEl.lease_id.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.lease_id.parent(),
    ajax: {
      url: t.config.getLeaseByAjax,
      dataType: "json",
      transport: function (params, success, failure) {

        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }

        var request = $.ajax(params);
        request.then(success);
        request.fail(failure);

        return request;
      },
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          company_id: t.mdl.device.frmEl.company.val()
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.Select_the_Contract_Agreement_Reference
      : "Select Contract Agreement Reference",
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    }
  }));

  t.mdl.device.frmEl.asset_owner.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.asset_owner.parent(),
    ajax: {
      url: t.config.getUserByAjax,
      dataType: "json",
      transport: function (params, success, failure) {

        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }

        var request = $.ajax(params);
        request.then(success);
        request.fail(failure);

        return request;
      },
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          company_id: t.mdl.device.frmEl.company.val()
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.Select_the_Owner
      : "Select Owner",
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr("title", data.text);
      return data.text && data.text.length > 50
        ? data.text.substring(0, 50) + "..."
        : data.text;
    }
  }));

  t.mdl.device.frmEl.supplier.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.supplier.parent(),
    ajax: {
      url: t.config.getSupplierByAjax,
      dataType: "json",
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.Select_the_Supplier
      : "Select Supplier"
  }));

  t.mdl.device.frmEl.invoice_id.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.invoice_id.parent(),
    ajax: {
      url: t.config.getInvoiceByAjax,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: function () {
            return t.mdl.device.frmEl.company.val();
          },
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.Select_the_Purchase_Invoice : "Select Purchase Invoice",
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (s, container) {
      if (typeof s.loading != "undefined" && s.loading) {
        return $("<div>" + s.text + "</div>");
      }
      $(s.element).attr({
        'data-currency': s.currency,
        'data-purchase-cost': s.purchase_cost,
        'data-invoice_date': s.invoice_date,
        'data-name': s.supplier_name,
        'data-id': s.supplier_id,
        'data-order-number': s.order_number,
        'data-po-number': s.po_number
      });
      return s.text;
    }
  })).on("change", function (e) {
    if (t.mdl.device.frmEl.invoice_id.val() == null) {
      if (t.current_purchase_date != null) {
        var purchasedate = t.current_purchase_date.split('-').join('/');
        t.mdl.device.frmEl.purchase_date.val(purchasedate);
      } else {
        t.mdl.device.frmEl.purchase_date.val(null);
      }
      if (t.current_supplier_name != null && t.current_supplier_id != null) {
        t.mdl.device.frmEl.supplier.append(new Option(t.current_supplier_name, t.current_supplier_id, true, true)).trigger("change");
      } else {
        t.mdl.device.frmEl.supplier.empty('').trigger('change');
      }
      if (t.order_no != null) {
        t.mdl.device.frm.find("input[name='order_number']").val(t.order_no);
      } else {
        t.mdl.device.frm.find("input[name='order_number']").val('');
      }
    } else {
      t.getPurchaseDate();
    }
  });
  t.mdl.device.frmEl.amc_supplier_id.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.amc_supplier_id.parent(),
    ajax: {
      url: t.config.getSupplierByAjax,
      dataType: "json",
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.Select_the_AMC_Supplier
      : "Select AMC Supplier"
  }));


  t.mdl.device.frmEl.assigned_to.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.assigned_to.parent(),
    ajax: {
      url: t.config.getActivatedUsers,
      dataType: "json",
      transport: function (params, success, failure) {

        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }

        var request = $.ajax(params);
        request.then(success);
        request.fail(failure);

        return request;
      },
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          company_id: t.mdl.device.frmEl.company.val()
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.select_the_user
      : "Select User",
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateResult: function (s) {

      if (s.loading) {
        return $("<div>" + s.text + "</div>");
      }

      var html = "";
      html += "<div class='row'>";
      html += "<div class='col-sm-10'>";
      if (s.displayName) {
        html += "<div class='so-t'><i class='fa fa-user'></i> " + s.displayName;
      } else {
        html += "<div class='so-t'><i class='fa fa-user'></i> " +
          (s.first_name || "") + " " + (s.last_name || "");
      }
      html += "<span class='active-user'></span>";
      html += "</div>";
      if (s.email) {
        html += "<div class='so-t'><i class='fa fa-envelope-o'></i> " + s.email + "</div>";
      }
      if (s.employee_num) {
        html += "<div class='so-t'><i class='fa fa-credit-card'></i> " + s.employee_num + "</div>";
      }
      if (s.company_name) {
        html += "<div class='so-t'><i class='fa fa-building'></i> " + s.company_name + "</div>";
      }
      html += "</div>";
      html += "<div class='col-sm-2'>";
      if (s.img_path) {
        html += "<img class='img-u' src='" + s.img_path + "' />";
      }
      html += "</div>";
      html += "</div>";
      return $(html);
    },
    templateSelection: function (data, container) {
      $(container).attr("title", data.text);
      return data.text && data.text.length > 50
        ? data.text.substring(0, 50) + "..."
        : data.text;
    }
  }));

  t.mdl.device.frmEl.checkout_reason.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.checkout_reason.parent(),
    ajax: {
      url: t.config.url.getReasonOptions,
      dataType: "json",
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
          action_type: 1 // Checkout = 1
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations
      ? t.config.translations.select_checkout_reason
      : "Select Checkout Reason",
    templateSelection: function (data, container) {
      $(container).attr("title", data.text);

      return data.text && data.text.length > 55
        ? data.text.substring(0, 55) + "..."
        : data.text;
    }
  }));

  t.mdl.device.frmEl.department_id.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.department_id.parent(),
    placeholder: t.config.translations ? t.config.translations.select_the_department : "Select Department",
    ajax: {
      url: t.config.url.getAssetDepartments,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.device.frmEl.company.val()) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: t.mdl.device.frmEl.company.val()
        };
      },
      delay: 300
    },
    allowClear: true,
    language: {
      noResults: function () {
        if (!t.mdl.device.frmEl.company.val()) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
    }
  }));



  t.mdl.checkout.frmEl.assigned_for.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.checkout.frmEl.assigned_for.parent(), data: t.config.assignedForOptions }));
  t.mdl.checkout.frmEl.last_checkout_project.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.checkout.frmEl.last_checkout_project.parent() }));
  t.mdl.checkin.frmEl.status_id.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.checkin.frmEl.status_id.parent() }));
  t.mdl.resale.frmEl.asset_status.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.resale.frmEl.asset_status.parent() }));
  t.mdl.resale.frmEl.sold_value_format.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.resale.frmEl.sold_value_format.parent() }));
  t.mdl.print.frmEl.print_opt.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.print }));
  t.mdl.print.frmEl.print_usr_plc.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.print }));
  t.mdl.print.frmEl.print_usr_info.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.print }));
  t.mdl.transfer.frmEl.transfer_to.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.transfer.frmEl.transfer_to.parent() }));
  t.mdl.transfer.frmEl.internal_place.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.transfer.frmEl.internal_place.parent(), placeholder: "Select Internal Place" }));
  t.mdl.transfer.frmEl.responsible_user.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.transfer.frmEl.responsible_user.parent(), placeholder: "Select User" }));
  t.mdl.transfer.frmEl.cc_users.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.transfer.frmEl.cc_users.parent() }));
  t.initFormValidations();
  t.setupModalEventHandlers();
};


// FORM VALIDATION
MyApp.prototype.initFormValidations = function () {
  var t = this;
  t.frmValidator = t.mdl.device.frm.validate({
    onsubmit: false,
    rules: {
      model_id: {
        required: true,
        str_name: true
      },
      asset_owner: {
        str_name: true,
        clean_text_only: true
      },
      company_id: {
        required: true,
        str_name: true
      },
      rtd_location_id: {
        required: true,
        str_name: true
      },
      asset_tag: {
        str_name: false,
        clean_text_only: true
      },
      status_id: {
        required: true,
        digits: true
      },
      serial: {
        required: true,
        str_name: false,
        clean_text_only: true
      },
      checkout_reason: {
        required: false,
        clean_text_only: true
      },
      product_number: {
        str_name: false,
        clean_text_only_with_hash: true
      },
      uuid: {
        str_name: true,
        clean_text_only: true
      },
      name: {
        str_name: false,
        clean_text_only: true
      },
      purchase_date: {
        remarks: true
      },
      device_occure_type: {
        str_name: true
      },
      purchase_cost: {
        number: true
      },
      order_number: {
        remarks: true
      },
      warranty_months: {
        digits: true
      },
      amc_expire_date: {
        remarks: true
      },
      ip: {
        required: false,
        str_address: true
      },
      mac: {
        mac_format: true
      },
      notes: {
        remarks: true,
        clean_text_only: true
      },
      billing_unit: {
        number: true
      },
      rate_hr: {
        number: true
      },
      rate_week: {
        number: true
      },
      rate_day: {
        number: true
      },
      rate_month: {
        number: true
      },
      rate_quarterly: {
        number: true
      },
      rate_half_yearly: {
        number: true
      },
      rate_yearly: {
        number: true
      },
      'device_rfid[]': {
        clean_text_only: true
      },
      scanner_id: {
        clean_text_only: true
      },
      image: {
        accept: "image/jpeg, image/jpg, image/png",
        extension: "jpg|jpeg|png",
        filesize: 2048000
      }
    },

    messages: {
      image: {
        required: "Please select an image.",
        accept: "Only JPEG, JPG, and PNG images are allowed.",
        extension: "Only JPEG, JPG, and PNG images are allowed.",
        filesize: "Image must be less than 2 MB."
      }
    },

    errorPlacement: function (error, element) {
      var $field = element.closest(".amg-form-field");
      if ($field.length && $field.find(".amg-form-error-wrap").length) {
        $field.find(".amg-form-error-wrap").html(error);
      } else {
        error.appendTo(element.parent().parent());
      }
    },
    highlight: function (element) {
      var $element = $(element);
      var $field = $element.closest(".amg-form-field");
      var $inputGroup = $field.find(".input-group");
      $field.addClass("error");
      $inputGroup.addClass("amg-form-invalid");
      if ($element.next(".select2").length) {
        $element.next(".select2")
          .find(".select2-selection")
          .addClass("amg-form-select-error");
      }
    },
    unhighlight: function (element) {
      var $element = $(element);
      var $field = $element.closest(".amg-form-field");
      var $inputGroup = $field.find(".input-group");
      $field.removeClass("error");
      $inputGroup.removeClass("amg-form-invalid");
      if ($element.next(".select2").length) {
        $element.next(".select2")
          .find(".select2-selection")
          .removeClass("amg-form-select-error");
      }
      $field.find(".amg-form-error-wrap").empty();
    },
    success: function (label, element) {
      var $element = $(element);
      var $field = $element.closest(".amg-form-field");
      $field.removeClass("error");
      $field.find(".input-group").removeClass("amg-form-invalid");
      if ($element.next(".select2").length) {
        $element.next(".select2")
          .find(".select2-selection")
          .removeClass("amg-form-select-error");
      }
      $field.find(".amg-form-error-wrap").empty();
    },
    invalidHandler: function (event, validator) {
      if (validator.numberOfInvalids()) {
        validator.errorList[0].element.scrollIntoView({
          behavior: "smooth",
          block: "center"
        });
      }
    }
  });

  $.validator.addMethod("numeric100", function (value, element) {
    return this.optional(element) || /^\d{1,100}$/.test(value);
  }, "Please enter a numeric value with up to 100 digits.");

  $.validator.addMethod("filesize", function (value, element, param) {
    return this.optional(element) || (element.files[0].size <= param);
  }, "File size must be less than or equal 2 MB.");

  // Checkout Form Validation
  t.chkoutFrmValidator = t.mdl.checkout.frm.validate({
    onsubmit: false,
    rules: {
      assigned_for: { required: true },
      assigned_to: { required: function () { return t.mdl.checkout.frmEl.assigned_for.val() === "1"; } },
      assigned_place: { required: function () { return t.mdl.checkout.frmEl.assigned_for.val() === "2"; } }
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    }
  });

  // Checkin Form Validation
  t.chkinFrmValidator = t.mdl.checkin.frm.validate({
    onsubmit: false,
    rules: {
      status_id: { required: true },
      note: {
        required: function () {
          return t.config.client === "knightfrank";
        },
        maxlength: 500,
        clean_text_only: true,
      },
      checkin_reason: {
        required: function () {
          return t.config.client === "knightfrank" || t.config.client === "rolepermission";
        },
      },
      checkin_attachment: {
        accept: "image/jpeg, image/jpg, image/png",
        extension: "jpg|jpeg|png",
        filesize: 2048000,
      },
    },
    errorPlacement: function (error, element) {
      error.insertAfter(element.closest(".input-group"));
    },
    highlight: function (element, errorClass) {
      var $element = $(element);
      $element.closest('.select-div').addClass(errorClass);
      var group = $element.closest(".input-group");
      if (group.length) {
        group.addClass("amg-form-invalid");
      }
      var isSelect2 = $element.hasClass("select2-hidden-accessible");
      if (isSelect2) {
        $element.next(".select2-container").find(".select2-selection").addClass("amg-form-select-error");
      }
    },
    unhighlight: function (element, errorClass) {
      var $element = $(element);
      $element.closest('.select-div').removeClass(errorClass);
      var group = $element.closest(".input-group");
      if (group.length) {
        group.removeClass("amg-form-invalid");
      }
      var isSelect2 = $element.hasClass("select2-hidden-accessible");
      if (isSelect2) {
        $element.next(".select2-container").find(".select2-selection").removeClass("amg-form-select-error");
      }
    }
  });

  // Resale Form Validation
  t.resaleFrmValidator = t.mdl.resale.frm.validate({
    onsubmit: false,
    rules: {
      sold_at: { required: true },
      sold_by: { required: true },
      sold_value: { required: true, regex: /^\d{1,11}(\.\d{1,2})?$/ }
    },
    errorPlacement: function (error, element) {
      if (element.closest(".input-group").length) {
        error.insertAfter(element.closest(".input-group"));
      } else {
        error.insertAfter(element);
      }
    }
  });

  // Expense Form Validation
  t.expenseFrmValidator = t.mdl.expense.frm.validate({
    onsubmit: false,
    rules: {
      asset_id: { required: true },
      expense_type: { required: true },
      title: { required: true },
      expense_date: { required: true },
      cost: { number: true }
    }
  });

  // Transfer Form Validation
  t.transferFrmValidator = t.mdl.transfer.frm.validate({
    onsubmit: false,
    rules: {
      transfer_to: {
        required: true
      },
      expected_received_date: {
        required: true
      },
      responsible_user: {
        required: true
      }
    },
    errorPlacement: function (error, element) {
      if (element.closest(".input-group").length) {
        error.insertAfter(element.closest(".input-group"));
      } else {
        error.insertAfter(element);
      }
    },
    highlight: function (element, errorClass) {
      var $element = $(element);

      $element.closest(".select-div").addClass(errorClass);

      var group = $element.closest(".input-group");
      if (group.length) {
        group.addClass("amg-form-invalid");
      }

      if ($element.hasClass("select2-hidden-accessible")) {
        $element
          .next(".select2-container")
          .find(".select2-selection")
          .addClass("amg-form-select-error");
      }
    },
    unhighlight: function (element, errorClass) {
      var $element = $(element);

      $element.closest(".select-div").removeClass(errorClass);

      var group = $element.closest(".input-group");
      if (group.length) {
        group.removeClass("amg-form-invalid");
      }

      if ($element.hasClass("select2-hidden-accessible")) {
        $element
          .next(".select2-container")
          .find(".select2-selection")
          .removeClass("amg-form-select-error");
      }
    }
  });
};


//MODAL EVENT HANDLERS
MyApp.prototype.setupModalEventHandlers = function () {
  var t = this;

  //Device Form Events
  t.mdl.device.frmEl.company.on("change", function () {
    t.mdl.device.frmEl.assigned_to.empty().val("").trigger("change");
    t.mdl.device.frmEl.assigned_place.empty().val("").trigger("change");
    t.mdl.device.frmEl.asset_owner.empty().val("").trigger("change");
    t.mdl.device.frmEl.supplier.empty().val("").trigger("change");
    t.mdl.device.frmEl.invoice_id.empty().val("").trigger("change");
    t.mdl.device.frmEl.stock_place.empty().val("").trigger("change");
    t.mdl.device.frmEl.location.empty().val("").trigger("change");
    t.mdl.device.frmEl.internal_place.empty().val("").trigger("change");
    t.mdl.device.frmEl.lease_id.empty().val("").trigger("change");
    t.mdl.device.frmEl.department_id.empty().val("").trigger("change");
  });

  t.mdl.device.frmEl.statusLabel.on("change", $.proxy(t.mdl.stockPlace, t));
  t.mdl.device.frmEl.statusLabel.on("change", $.proxy(t.statusChanged, t));
  t.mdl.device.frmEl.device_occure_type.on("change", $.proxy(t.mdl.leaseDevice, t));
  t.mdl.device.frmEl.model.on("change", $.proxy(t.getCustomFields, t));
  t.mdl.device.frmEl.assigned_for.on("change", $.proxy(t.initialCheckoutSelectedFor, t));
  t.mdl.device.frmEl.purchase_date.on('changeDate', $.proxy(t.updateAmcStartDate, t));
  t.mdl.device.frmEl.warranty_start_date.on('changeDate', $.proxy(t.updateAmcStartDate, t));
  t.mdl.device.frmEl.warranty_start_date.on('changeDate', function (e) {
    t.mdl.device.frmEl.warrenty_end_date.datepicker('setStartDate', e.date);
  });

  // Checkout Form Events 
  t.mdl.checkout.frmEl.assigned_for.on("change", $.proxy(t.chkout.switchCheckTarget, t));
  t.mdl.checkout.billable.on('change', $.proxy(t.billableCheck, t));
  t.mdl.checkout.frmEl.rate.on("change", $.proxy(t.getCost, t));
  t.mdl.checkout.frmEl.checkout_at.on('changeDate', function (e) {
    t.mdl.checkout.frmEl.expected_checkin.datepicker('setStartDate', e.date);
  });

  // Print Form Events
  t.mdl.print.frmEl.print_usr_plc.on("change", function () {
    if ($(this).val() === "1") {
      t.mdl.print.frmEl.print_usr_info_cvr.removeClass("d-none");
    } else {
      t.mdl.print.frmEl.print_usr_info_cvr.addClass("d-none");
    }
  });

  // Transfer Form Events
  t.mdl.transfer.frmEl.transfer_to.on("change", function () {
    t.loadTransferInternalPlaces();
    t.loadTransferResponsibleUsers();
  });

  //Supplier Add Events
  t.mdl.resale.frmEl.addSupplier.on("click", function () {
    t.resetSupplierForm();
    t.mdl.supplier.modal("show");
  });

  // Modal Submit Events
  t.mdl.device.btnSubmit.on("click", $.proxy(t.handleDeviceSubmit, t));
  t.mdl.checkout.btnSubmit.on("click", $.proxy(t.handleCheckoutSubmit, t));
  t.mdl.checkin.btnSubmit.on("click", $.proxy(t.handleCheckinSubmit, t));
  t.mdl.resale.btnSubmit.on("click", $.proxy(t.handleResaleSubmit, t));
  t.mdl.expense.btnSubmit.on("click", $.proxy(t.handleExpenseSubmit, t));
  t.mdl.supplier.btnSubmit.on("click", $.proxy(t.handleSupplierSubmit, t));
  // t.mdl.transfer.btnSubmit1.on("click", $.proxy(t.handleTransferSubmit, t));
  t.mdl.transfer.btnSubmit1.on("click", $.proxy(t.handleTransferSubmit, t));
  t.mdl.print.btnSubmit.on("click", $.proxy(t.handlePrintSubmit, t));
};


MyApp.prototype.bindEvents = function () {
  var t = this;
  // Header buttons
  t.content.on("click", t.btn.reload, $.proxy(t.handleReloadClick, t));
  t.content.on("click", t.btn.add, $.proxy(t.handleAddClick, t));
  t.content.on("click", t.btn.deleted, $.proxy(t.handleToggleDeletedClick, t));
  t.content.on("click", t.btn.print_label, $.proxy(t.handlePrintLabelClick, t));
  t.content.on("click", t.btn.import, $.proxy(t.handleImportClick, t));
  t.content.on("click", t.btn.bulk_checkout, $.proxy(t.handleBulkCheckoutClick, t));
  t.content.on("click", t.btn.bulk_checkin, $.proxy(t.handleBulkCheckinClick, t));
  t.content.on("click", t.btn.bulk_update, $.proxy(t.handleBulkUpdateClick, t));
  t.content.on("click", t.btn.bulk_reminder, $.proxy(t.handleBulkReminderClick, t));
  t.content.on("click", t.btn.export_excel, $.proxy(t.handleExportExcelClick, t));
  t.content.on("click", t.btn.export_pdf, $.proxy(t.handleExportPdfClick, t));
  t.content.on("click", t.btn.bulk_delete, $.proxy(t.handleBulkDeleteClick, t));
  t.content.on("click", t.btn.filter_toggle, $.proxy(t.handleFilterToggleClick, t));
  t.content.on("click", t.btn.expand, $.proxy(t.handleExpandAllToggle, t));
  t.content.on("click", ".dt-row-expand-toggle", $.proxy(t.handleRowExpandToggle, t));
  $(document).on("click", t.btn.filter_apply, $.proxy(t.applyFilters, t));
  $(document).on("click", t.btn.filter_clear, $.proxy(t.clearFilters, t));
  t.content.on("input", t.btn.search_input, $.proxy(t.handleSearchInput, t));
  t.content.on("change", t.btn.page_length, $.proxy(t.handlePageLengthChange, t));
  t.content.on("click", ".dtActEdit", $.proxy(t.handleEditClick, t));
  t.content.on("click", ".dtActDel", $.proxy(t.handleDeleteClick, t));
  t.content.on("click", ".dtActRestore", $.proxy(t.handleRestoreClick, t));
  t.content.on("click", ".dtActClone", $.proxy(t.handleCloneClick, t));
  t.content.on("click", ".dtActView", $.proxy(t.handleViewClick, t));
  t.content.on("click", ".dtActCheckout", $.proxy(t.handleCheckoutDeviceClick, t));
  t.content.on("click", ".dtActCheckin", $.proxy(t.handleCheckinDeviceClick, t));
  t.content.on("click", ".sortable-header", $.proxy(t.handleSortClick, t));
  t.content.on("click", ".paginate_button", $.proxy(t.handlePageClick, t));
  t.content.on("mouseenter", ".device-list-actions .dropdown", $.proxy(t.handleActionMenuMouseEnter, t));
  t.content.on("mouseleave", ".device-list-actions .dropdown", $.proxy(t.handleActionMenuMouseLeave, t));
  t.content.on("focusin", ".device-list-actions .dropdown", $.proxy(t.handleActionMenuFocusIn, t));
  t.content.on("focusout", ".device-list-actions .dropdown", $.proxy(t.handleActionMenuFocusOut, t));
  t.content.on("click", ".device-list-menu-toggle", $.proxy(t.handleActionMenuToggleClick, t));
  t.content.on("click", ".device-list-actions .action-dropdown-menu .dropdown-item", $.proxy(t.handleActionMenuItemClick, t));
  t.content.on("click", "#checkAll", $.proxy(t.handleSelectAllClick, t));

  t.content.on("click", ".dvl-badge", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $badge = $(this);
    var $row = $badge.closest("tr");
    var recordId = $row.data("row-id");
    if (!recordId) return;
    var statusText = $badge.text().trim().toLowerCase();
    var $actionsCell = $row.find(".amg-col-actions");
    var $checkoutBtn = $actionsCell.find(".dtActCheckout");
    var $checkinBtn = $actionsCell.find(".dtActCheckin");
    if (statusText === "deployed" && $checkinBtn.length) {
      t.handleCheckinDeviceClick({
        preventDefault: function () { },
        currentTarget: $checkinBtn[0]
      });
    } else if (statusText === "ready to deploy" && $checkoutBtn.length) {
      t.handleCheckoutDeviceClick({
        preventDefault: function () { },
        currentTarget: $checkoutBtn[0]
      });
    }
  });

  // Send reminder
  t.content.on("click", ".send-reminder-icon", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this);
    var deviceId = $this.attr("data-id");
    if (deviceId) {
      t.sendChkoutAcceptReminder(deviceId, $this);
    }
  });
  t.content.on("click", ".dtActPrintLabel", $.proxy(t.handleActionPrintLabelClick, t));
  t.content.on("click", t.btn.showAddedToPrintDevices, $.proxy(t.handleShowAddedToPrintDevices, t));
  $(document).on("click", t.btn.showAddedToPrintDevices, $.proxy(t.handleShowAddedToPrintDevices, t));
  t.content.on("click", t.btn.bulk_transfer, $.proxy(t.showTransferModal, t));
};


//load listing
MyApp.prototype.loadDevices = function () {
  var t = this;
  if (t.isLoading) return;
  t.isLoading = true;
  t.showLoading();
  var orderId = parseInt(t.sortField, 10) || 7;
  var orderDir = parseInt(t.sortDirection, 10) || 2;
  var data = {
    _token: t.config.token,
    page: t.currentPage,
    size: t.perPage,
    search: { value: t.config.search || "" },
    showDeletedDevices: t.showDeletedDevices,
    order: { id: orderId, dir: orderDir },
    filters: t.config.other_filters || {},
    location: t.config.location_filter,
    department: t.config.department_filter,
    company_id: t.config.company_id,
    status_id: t.config.status_id,
  };

  $.ajax({
    url: t.config.url.devices,
    type: "POST",
    data: data,
    dataType: "json",
    success: function (response) {
      t._deviceData = response.data || [];
      t.config.data_print = response.data || [];
      t.renderDevices(response);
      t.updatePagination(response);
      t.isLoading = false;
      t.hideLoading();
      t.updateSortIcon();
    },
    error: function (xhr) {
      console.error("Error loading devices:", xhr.responseText);
      t.showError("Failed to load devices. Please try again.");
      t.isLoading = false;
      t.hideLoading();
    }
  });
};

MyApp.prototype.updateSortIcon = function () {
  var t = this;
  var orderIdToField = {
    1: 'asset_tag',
    2: 'mdl_name',
    3: 'loc_name',
    4: 'lbl_name',
    5: 'checkout',
    6: 'last_checkout',
    7: 'updated_at',
    8: 'purchase_date'
  };

  var orderId = parseInt(t.sortField, 10);
  var fieldKey = orderIdToField[orderId];
  if (!fieldKey) return;
  t.content.find('.sortable-header').removeClass('sort-asc sort-desc');
  var $header = t.content.find('.sortable-header[data-sort="' + fieldKey + '"]');
  if ($header.length) {
    var directionClass = parseInt(t.sortDirection, 10) === 1 ? 'sort-asc' : 'sort-desc';
    $header.addClass(directionClass);
  }
};

MyApp.prototype.renderDevices = function (response) {
  var t = this;
  var data = response.data || [];
  var html = '';
  if (!data.length) {
    html = t.getEmptyStateHtml();
  } else {
    $.each(data, function (index, record) {
      html += t.renderDeviceRow(record);
    });
  }
  t.tableBody.html(html);
  t.initTooltips(t.tableBody);
  t.bindCheckboxEvents();
  t.restoreExpandedRows();
  //reminder icons
  t.tableBody.find('.send-reminder-icon').each(function () {
    if ($(this).data('bs-toggle') === 'tooltip') {
      var tooltip = bootstrap.Tooltip.getInstance(this);
      if (!tooltip) {
        new bootstrap.Tooltip(this);
      }
    }
  });
};

// for data in datatable 
MyApp.prototype.renderDeviceRow = function (record) {
  var t = this;
  var isExpanded = t.expandedRows[record.id] || false;
  var expandIcon = isExpanded ? t.getExpandIcon("minus") : t.getExpandIcon("plus");
  var isInMergeQueue = false;
  if (t.mergeObj && t.mergeObj.merge_items && Array.isArray(t.mergeObj.merge_items.others)) {
    $.each(t.mergeObj.merge_items.others, function (i, v) {
      if (String(v.id) === String(record.id)) {
        isInMergeQueue = true;
        return false;
      }
    });
  }
  var checked = (t.selectedIds[record.id] || isInMergeQueue) ? "checked" : "";
  if (isInMergeQueue && !t.selectedIds[record.id]) {
    t.selectedIds[record.id] = true;
  }

  return [
    '<tr id="row-' + record.id + '" data-row-id="' + record.id + '" class="' + (isExpanded ? 'row-expanded' : '') + '">',
    '<td class="amg-table-checkbox-col">',
    '<label class="d-inline-flex align-items-center justify-content-center">',
    '<input type="checkbox" class="row-checkbox form-check-input" value="' + record.id + '" ' + checked + '>',
    '</label>',
    '</td>',
    '<td>', t.renderDeviceTagCell(record, "display"), '</td>',
    '<td>', t.renderDeviceInfoCell(record, "display"), '</td>',
    '<td>', t.renderStatusCell(record, "display"), '</td>',
    '<td>', t.renderFlagsCell(record, "display"), '</td>',
    '<td>', t.renderCurrentlyInCell1(record, "display"), '</td>',
    '<td>', t.renderCurrentlyInCell(record, "display"), '</td>',
    '<td>', t.renderUpdatedOnCell(record, "display"), '</td>',
    '<td class="amg-col-actions">', t.renderActionsCell(record, "display"), '</td>',
    '</tr>'
  ].join('');
};

MyApp.prototype.getEmptyStateHtml = function () {
  return [
    '<tr>',
    '<td colspan="10" class="text-center py-5">',
    '<div class="empty-state">',
    '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1">',
    '<rect x="2" y="4" width="20" height="16" rx="2"/>',
    '<path d="M22 17H2"/>',
    '<circle cx="8" cy="11" r="1.5" fill="#d1d5db"/>',
    '<circle cx="16" cy="11" r="1.5" fill="#d1d5db"/>',
    '</svg>',
    '<p class="mt-3 text-muted">No devices found</p>',
    '<small class="text-muted">Try adjusting your filters or search terms</small>',
    '</div>',
    '</td>',
    '</tr>'
  ].join('');
};

MyApp.prototype.showLoading = function () {
  var t = this;
  t.tableBody.html(
    '<tr><td colspan="10" class="text-center py-5">' +
    '<div class="spinner-border text-primary" role="status">' +
    '<span class="visually-hidden">Loading...</span>' +
    '</div>' +
    '</td></tr>'
  );
};

MyApp.prototype.hideLoading = function () { };
MyApp.prototype.showError = function (message) {
  var t = this;
  t.tableBody.html(
    '<tr><td colspan="10" class="text-center py-5 text-danger">' +
    '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">' +
    '<circle cx="12" cy="12" r="10"/>' +
    '<line x1="12" y1="8" x2="12" y2="12"/>' +
    '<line x1="12" y1="16" x2="12.01" y2="16"/>' +
    '</svg>' +
    '<p class="mt-2">' + t.escapeHtml(message) + '</p>' +
    '</td></tr>'
  );
};


//pagniantion
MyApp.prototype.updatePagination = function (response) {
  var t = this;
  var total = response.filtered || response.total || response.recordsTotal || 0;
  t.currentPage = parseInt(response.current_page || response.page || 1, 10);
  t.perPage = parseInt(response.size || response.perPage || t.perPage, 10);
  t.totalRecords = total;
  t.totalPages = Math.max(1, Math.ceil(total / t.perPage));
  var $paginationContainer = t.content.find(".pagination-container");
  if ($paginationContainer.length) {
    $paginationContainer.html(t.buildPaginationHtml());
  }
  var from = ((t.currentPage - 1) * t.perPage) + 1;
  var to = Math.min(t.currentPage * t.perPage, total);
  if (total === 0) { from = 0; to = 0; }
  var $summary = t.content.find(".table-summary");
  if ($summary.length) {
    $summary.text("Showing " + from + " to " + to + " of " + total + " entries");
  }
  $("#deviceTotalCount").text(total);
};

MyApp.prototype.buildPaginationHtml = function () {
  var t = this;
  if (t.totalPages <= 1) return "";
  var current = t.currentPage;
  var total = t.totalPages;
  var html = [];
  html.push('<div class="dataTables_paginate paging_simple_numbers" id="deviceTable_paginate">');
  html.push(
    '<a class="paginate_button previous ' + (current <= 1 ? 'disabled' : '') + '"' +
    ' data-page="' + (current - 1) + '" role="link">Previous</a>'
  );
  html.push('<span>');
  var start = Math.max(1, current - 2);
  var end = Math.min(total, current + 2);
  if (start > 1) {
    html.push('<a class="paginate_button" data-page="1" role="link">1</a>');
    if (start > 2) html.push('<span class="ellipsis">…</span>');
  }
  for (var i = start; i <= end; i++) {
    var active = i === current ? 'current' : '';
    html.push(
      '<a class="paginate_button ' + active + '" data-page="' + i + '" role="link"' +
      (i === current ? ' aria-current="page"' : '') + '>' + i + '</a>'
    );
  }

  if (end < total) {
    if (end < total - 1) html.push('<span class="ellipsis">…</span>');
    html.push('<a class="paginate_button" data-page="' + total + '" role="link">' + total + '</a>');
  }

  html.push('</span>');
  html.push(
    '<a class="paginate_button next ' + (current >= total ? 'disabled' : '') + '"' +
    ' data-page="' + (current + 1) + '" role="link">Next</a>'
  );

  html.push('</div>');
  return html.join("");
};


//sorting 
MyApp.prototype.handleSortClick = function (e) {
  e.preventDefault();
  var $header = $(e.currentTarget);
  var field = $header.data('sort');
  var self = this;
  var fieldOrderMap = {
    'asset_tag': 1,
    'mdl_name': 2,
    'loc_name': 3,
    'lbl_name': 4,
    'checkout': 5,
    'last_checkout': 6,
    'updated_at': 7,
    'purchase_date': 8,
    'model': 2,
    'status': 4,
    'location': 3,
    'assigned_to': 5,
    'full_name': 5
  };
  var orderId = fieldOrderMap[field];
  if (!orderId) return;
  var currentOrderId = parseInt(self.sortField, 10) || 7;
  var currentDirection = parseInt(self.sortDirection, 10) || 2;
  if (currentOrderId === orderId) {
    self.sortDirection = currentDirection === 1 ? 2 : 1;
  } else {
    self.sortField = orderId;
    self.sortDirection = 1;
  }
  self.content.find('.sortable-header').removeClass('sort-asc sort-desc');
  var directionClass = parseInt(self.sortDirection, 10) === 1 ? 'sort-asc' : 'sort-desc';
  $header.addClass(directionClass);

  self.currentPage = 1;
  self.loadDevices();
};


// all crud handle
MyApp.prototype.handleAddClick = function (e) {
  if (e) e.preventDefault();
  this.addDevice();
};

// SEND CHECKOUT ACCEPTANCE REMINDER
MyApp.prototype.sendChkoutAcceptReminder = function (deviceId, $element) {
  var t = this;
  var tr = t.config.translations || {};
  if (!t.httpCall) return;
  if ($element && $element.length) {
    $element.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
    $element.css('pointer-events', 'none');
  }
  Swal.fire({
    icon: 'warning',
    title: 'Send Acceptance Reminder',
    text: 'Are you sure you want to send the acceptance reminder for this device?',
    showCancelButton: true,
    confirmButtonText: 'Yes, send it',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    allowOutsideClick: false
  }).then(function (result) {
    if (!result.isConfirmed) {
      if ($element && $element.length) {
        $element.html(t.getReminderIconHtml());
        $element.css('pointer-events', '');
      }
      return;
    }

    t.httpCall = false;
    $.ajax({
      url: t.config.url.send_chkout_accept_reminder + '/' + deviceId,
      type: 'GET',
      success: function (data) {
        if (typeof data == 'object') {
          if (data.status == 'success') {
            t.showToast('success', data.msg || 'Reminder sent successfully');
            t.loadDevices();
          } else {
            t.showToast('error', data.msg || 'Failed to send reminder');
            if ($element && $element.length) {
              $element.html(t.getReminderIconHtml());
              $element.css('pointer-events', '');
            }
          }
        }
      },
      error: function () {
        t.showToast('error', tr.something_went_wrong || 'Unable to send reminder');
        if ($element && $element.length) {
          $element.html(t.getReminderIconHtml());
          $element.css('pointer-events', '');
        }
      },
      complete: function () {
        t.httpCall = true;
      }
    });
  });
};

MyApp.prototype.addDevice = function () {
  var t = this;
  t.resetDeviceForm();
  t.httpPostPath = t.config.url.add;
  t.mdl.device.title.html(t.config.translations.add_new_device || "Add New Device");
  t.mdl.device.btnSubmit.text(t.config.translations.add_new_device || "Add New Device");
  t.mdl.device.forAction = "";
  t.mdl.device.frmEl.forAction.val("");
  t.mdl.device.frmEl.clone_img.val("");
  t.mdl.device.live_monitor_field.attr("hidden", "true");
  t.mdl.device.frmEl.statusLabel.prop('disabled', false);
  t.mdl.device.frmEl.company.prop('disabled', false);
  t.mdl.device.frmEl.model.val(null).trigger('change.select2');
  t.mdl.device.frm.find("input[name='serial']").attr('readonly', false);
  t.clearCustomFields();
  if (typeof t.config.custom_fields == "object" && typeof t.config.custom_fields.html != "") {
    t.fillCustomFields(t.config.custom_fields);
  }
  t.mdl.device.frmEl.company.val("").trigger("change");
  t.mdl.device.modal("show");
  t.mdl.device.tabs.find(".nav-tabs a:first").tab("show");
};

MyApp.prototype.handleEditClick = function (e) {
  if (e) e.preventDefault();
  this.editDevice($(e.currentTarget).attr("data-id"));
};

MyApp.prototype.editDevice = function (id) {
  var t = this;
  t.httpPostPath = t.config.url.edit + "/" + id;

  $.get(t.config.url.get + "/" + id + "/edit")
    .done(function (data) {
      if (typeof data == "object" && data.status == "success") {
        t.mdl.device.title.html(t.config.translations.edit_device || "Edit Device");
        t.mdl.device.btnSubmit.text(t.config.translations.save || "Save");
        t.mdl.device.forAction = "edit";
        t.loadFormData(data.device, "edit");
      } else {
        t.showToast("error", data.msg || "Failed to load device details");
      }
    })
    .fail(function () {
      t.showToast("error", "Something went wrong");
    });
};

MyApp.prototype.handleCloneClick = function (e) {
  if (e) e.preventDefault();
  this.cloneDevice($(e.currentTarget).attr("data-id"));
};

MyApp.prototype.cloneDevice = function (id) {
  var t = this;
  console.log('Clone URL:', t.config.url.get + "/" + id + "/clone");
  t.httpPostPath = t.config.url.add;
  $.get(t.config.url.get + "/" + id + "/clone")
    .done(function (data) {
      if (typeof data == "object" && data.status == "success") {
        t.mdl.device.title.html(t.config.translations.Clone_Device || "Clone Device");
        t.mdl.device.btnSubmit.text(t.config.translations.save || "Save");
        t.mdl.device.forAction = "clone";
        t.loadFormData(data.device, "clone");
        t.mdl.device.frmEl.asset_tag.val("");
      } else {
        t.showToast("error", data.msg || "Failed to clone device");
      }
    })
    .fail(function () {
      t.showToast("error", "Something went wrong");
    });
};

MyApp.prototype.handleViewClick = function (e) {
  if (e) e.preventDefault();
  var devId = $(e.currentTarget).attr("data-id");
  if (this.config.url.info) {
    window.open(this.config.url.info + "/" + devId, "_blank");
  }
};

MyApp.prototype.handleDeleteClick = function (e) {
  if (e) e.preventDefault();
  this.deleteDevice($(e.currentTarget).attr("data-id"));
};

MyApp.prototype.deleteDevice = function (id) {
  var t = this;
  var tr = t.config.translations || {};
  t.confirmAndPost(
    tr.are_you_delete || "Are you sure you want to delete this device?",
    t.config.url.delete + "/" + id,
    { _token: t.getCsrfToken() },
    {
      successMessage: tr.success || "Device deleted.",
      errorMessage: tr.something_went_wrong || "Something went wrong."
    }
  );
};

MyApp.prototype.handleRestoreClick = function (e) {
  if (e) e.preventDefault();
  this.restoreDevice($(e.currentTarget).attr("data-id"));
};

MyApp.prototype.restoreDevice = function (id) {
  var t = this;
  var tr = t.config.translations || {};
  $.get(t.config.url.restore + "/" + id)
    .done(function (data) {
      if (data && data.status === "success") {
        t.showToast("success", data.msg || tr.success || "Restored.");
        t.loadDevices();
      } else {
        t.showToast("error", data && data.msg ? data.msg : tr.something_went_wrong || "Failed.");
      }
    })
    .fail(function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    });
};

MyApp.prototype.handleCheckoutDeviceClick = function (e) {
  if (e) e.preventDefault();
  this.openCheckoutModal($(e.currentTarget));
};


MyApp.prototype.openCheckoutModal = function ($btn) {
  var t = this;
  t.resetCheckoutForm();
  t.httpPostPath = t.config.url.checkout;
  let assetTag = $btn.attr("data-asset_tag") || "";
  let company = $btn.attr("data-cmp_name") || "";
  let html = assetTag;
  if (company && company !== assetTag) {
    html += ` (<strong>${company}</strong>)`;
  }
  t.mdl.checkout.lblDeviceTag.html(html);
  t.mdl.checkout.frmEl.id.val($btn.attr("data-id"));
  t.mdl.checkout.frmEl.name.val($btn.attr("data-name"));
  t.mdl.checkout.company_id = $btn.attr("data-company_id");
  t.mdl.checkout.location_id = $btn.attr("data-device_loc_id");
  t.mdl.checkout.frmEl.checkout_at.datepicker("setDate", new Date());
  t.mdl.checkout.frmEl.assigned_place.empty();
  if (t.config.client === "knightfrank" || t.config.client === "rolepermission") {
    t.mdl.checkout.find("label[for=checkout_reason_id]").addClass("mandatory");
  }

  t.mdl.checkout.frmEl.assigned_to.select2($.extend({}, { width: "100%" }, {
    dropdownParent: t.mdl.checkout.frmEl.assigned_to.parent(),
    ajax: {
      url: t.config.getActivatedUsers,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.checkout.company_id) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: function () {
            return t.mdl.checkout.company_id;
          }
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.select_the_user : "Select User",
    language: {
      noResults: function () {
        if (!t.mdl.checkout.company_id) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateResult: function (s) {
      if (s.loading) {
        return $("<div>" + s.text + "</div>");
      }

      var html = "";
      html += "<div class='row'>";
      html += "<div class='col-sm-10'>";
      if (s.displayName) {
        html += "<div class='so-t'><i class='fa fa-user'></i> " + s.displayName;
      } else {
        html += "<div class='so-t'><i class='fa fa-user'></i> " +
          (s.first_name || "") + " " + (s.last_name || "");
      }
      html += "<span class='active-user'></span>";
      html += "</div>";
      if (s.email) {
        html += "<div class='so-t'><i class='fa fa-envelope-o'></i> " + s.email + "</div>";
      }
      if (s.employee_num) {
        html += "<div class='so-t'><i class='fa fa-credit-card'></i> " + s.employee_num + "</div>";
      }
      if (s.company_name) {
        html += "<div class='so-t'><i class='fa fa-building'></i> " + s.company_name + "</div>";
      }
      html += "</div>";
      html += "<div class='col-sm-2'>";
      if (s.img_path) {
        html += "<img class='img-u' src='" + s.img_path + "' />";
      }
      html += "</div>";
      html += "</div>";

      return $(html);
    },
    templateSelection: function (data, container) {
      $(container).attr("title", data.text);
      return data.text && data.text.length > 50
        ? data.text.substring(0, 50) + "..."
        : data.text;
    }
  }));

  t.mdl.checkout.frmEl.assigned_place.select2($.extend({}, { width: "100%" }, {
    dropdownParent: t.mdl.checkout.frmEl.assigned_place.parent(),
    ajax: {
      url: t.config.ajaxGetInternalPlace,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.checkout.company_id) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        let data = {
          search: p.term,
          page: p.page || 1,
          company_id: t.mdl.checkout.company_id
        };
        if (t.config.client == "rashmi") {
          data.location_id = t.mdl.checkout.location_id;
        }
        return data;
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.Select_Place : "Select Place",
    language: {
      noResults: function () {
        if (!t.mdl.checkout.company_id) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    },
    templateResult: function (s) {
      if (typeof s.loading != "undefined" && s.loading) {
        return $("<div>" + s.text + "</div>");
      }
      var a = '';
      if (s.text != null && s.text != "") {
        a += "<div class='so-t'><i class=\"fa fa-map-marker\"></i> " + s.text + "</div>";
      }
      if (s.company_name != null && s.company_name != "") {
        a += "<div class='so-t'><i class=\"fa fa-building\"></i> " + s.company_name + "</div>";
      }
      return $("<div>" + a + "</div>");
    }
  }));
  t.mdl.checkout.frmEl.assigned_place.trigger('change');

  t.mdl.checkout.frmEl.assigned_for.val("1").trigger('change');

  t.mdl.checkout.frmEl.last_checkout_project.empty();
  t.mdl.checkout.frmEl.last_checkout_project.append(new Option(
    t.config.translations ? t.config.translations.Select_Project : "Select Project",
    ''
  ));
  if (typeof t.config.project == 'object' && t.config.project.length > 0) {
    $.each(t.config.project, function (i, k) {
      t.mdl.checkout.frmEl.last_checkout_project.append(new Option(k.text, k.id, false, false));
    });
  }
  t.mdl.checkout.frmEl.last_checkout_project.select2({
    placeholder: t.config.translations ? t.config.translations.Select_Project : "Select Project",
    allowClear: true,
    width: '100%',
    dropdownParent: t.mdl.checkout.frmEl.last_checkout_project.parent(),
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    },
    templateResult: function (data) {
      return data.text;
    }
  });
  t.mdl.checkout.frmEl.last_checkout_project.trigger('change');
  t.mdl.checkout.frmEl.checkout_reason.select2({
    width: "100%",
    dropdownParent: t.mdl.checkout,
    placeholder: t.config.translations?.select_checkout_reason || "Select Checkout Reason",
    allowClear: true,
    ajax: {
      url: t.config.url.getReasonOptions,
      dataType: "json",
      delay: 300,
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          action_type: 1
        };
      }
    },
    templateSelection: function (data) {
      return data.text || "";
    }
  });
  t.mdl.checkout.frmEl.checkout_reason.trigger('change');
  t.mdl.checkout.frmEl.allocation_status.empty();
  t.mdl.checkout.frmEl.allocation_status.append(new Option("Select Allocation Type", ''));
  if (typeof t.config.status == 'object' && t.config.status.length > 0) {
    $.each(t.config.status, function (i, k) {
      t.mdl.checkout.frmEl.allocation_status.append(new Option(k.text, k.id, false, false));
    });
  }
  t.mdl.checkout.frmEl.allocation_status.select2({
    placeholder: "Select Allocation Type",
    allowClear: true,
    width: '100%',
    dropdownParent: t.mdl.checkout.frmEl.allocation_status.parent(),
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    },
    templateResult: function (data) {
      return data.text;
    }
  });
  t.mdl.checkout.frmEl.allocation_status.trigger('change');
  t.mdl.checkout.modal("show");
};

MyApp.prototype.handleCheckoutSubmit = function (e) {
  e.preventDefault();
  var t = this;
  if (t.chkoutFrmValidator.form() == false) return false;
  if (t.httpCall != true) return false;
  t.mdl.checkout.btnSubmit.attr("disabled", true);
  t.mdl_popup_loader_checkout.addClass('active');
  t.httpCall = false;
  var formData = new FormData(t.mdl.checkout.frm[0]);

  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Device checked out successfully");
          t.mdl.checkout.modal("hide");
          t.loadDevices();
        } else {
          t.showToast("error", data.msg || "Failed to check out device");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    complete: function () {
      t.mdl.checkout.btnSubmit.removeAttr("disabled");
      t.mdl_popup_loader_checkout.removeClass('active');
      t.httpCall = true;
    }
  });
};

// CHECKIN DEVICE
MyApp.prototype.handleCheckinDeviceClick = function (e) {
  if (e) e.preventDefault();
  this.openCheckinModal($(e.currentTarget));
};

MyApp.prototype.openCheckinModal = function ($btn) {
  var t = this;
  t.resetCheckinForm();
  t.httpPostPath = t.config.url.checkin;
  let assetTag = $btn.attr("data-asset_tag") || "";
  let company = $btn.attr("data-cmp_name") || "";
  let html = assetTag;
  if (company && company !== assetTag) {
    html += ` (<strong>${company}</strong>)`;
  }
  t.mdl.checkin.lblDeviceTag.html(html);
  t.mdl.checkin.lblDeviceName.html($btn.attr("data-name") || "");
  t.mdl.checkin.frmEl.id.val($btn.attr("data-id"));
  t.mdl.checkin.company_id = $btn.attr("data-company_id");
  t.mdl.checkin.frmEl.stock_place.empty().trigger('change');
  if (t.config.client === "knightfrank" || t.config.client === "rolepermission") {
    t.mdl.checkin.find("label[for=checkin_reason_id]").addClass("mandatory");
  }

  t.mdl.checkin.frmEl.stock_place.select2($.extend({}, { width: "100%" }, {
    dropdownParent: t.mdl.checkin.frmEl.stock_place.parent(),
    ajax: {
      url: t.config.ajaxGetInternalPlace,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.mdl.checkin.company_id) {
          success({ results: [] });
          return;
        }
        let request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: function () {
            return t.mdl.checkin.company_id;
          }
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.Select_Place : "Select Place",
    language: {
      noResults: function () {
        if (!t.mdl.checkin.company_id) {
          return "Please select the company to fetch data";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    }
  }));
  t.mdl.checkin.frmEl.stock_place.trigger('change');
  t.mdl.checkin.frmEl.checkin_reason.select2($.extend({}, { width: "100%" }, {
    dropdownParent: t.mdl.checkin.frmEl.checkin_reason.parent(),
    ajax: {
      url: t.config.url.getReasonOptions,
      dataType: "json",
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          action_type: 2
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: t.config.translations ? t.config.translations.select_checkin_reason : "Select Checkin Reason",
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
    }
  }));
  t.mdl.checkin.frmEl.checkin_reason.trigger('change');
  var checkout_date = $btn.attr("data-checkout_date");
  if (checkout_date) {
    t.mdl.checkin.frmEl.checkin_at.datepicker('setStartDate', checkout_date);
  }
  t.mdl.checkin.frmEl.checkin_at.datepicker("setDate", new Date());
  if (t.config.firstDeployable) {
    t.mdl.checkin.frmEl.status_id.val(t.config.firstDeployable).trigger("change");
  }
  t.mdl.checkin.modal("show");
};

MyApp.prototype.handleCheckinSubmit = function (e) {
  e.preventDefault();
  var t = this;
  if (t.chkinFrmValidator.form() == false) return false;
  if (t.httpCall != true) return false;
  t.mdl.checkin.btnSubmit.attr("disabled", true);
  t.mdl_popup_loader_checkin.addClass('active');
  t.httpCall = false;
  var formData = new FormData(t.mdl.checkin.frm[0]);
  if (!formData.has('stock_place')) {
    formData.append('stock_place', '');
  }
  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Device checked in successfully");
          t.mdl.checkin.modal("hide");
          t.loadDevices();
        } else {
          t.showToast("error", data.msg || "Failed to check in device");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    complete: function () {
      t.mdl.checkin.btnSubmit.removeAttr("disabled");
      t.mdl_popup_loader_checkin.removeClass('active');
      t.httpCall = true;
    }
  });
};

//LOAD FORM DATA
MyApp.prototype.loadFormData = function (dev, forAction) {
  var t = this;
  t.resetDeviceForm();
  if (dev.data.company_id > 0) t.mdl.device.frmEl.company.val(dev.data.company_id).trigger("change");
  if (dev.data.device_occure_type > 0) t.mdl.device.frmEl.device_occure_type.val(dev.data.device_occure_type).trigger("change");
  if (dev.data.asset_type_id > 0) t.mdl.device.frmEl.asset_type_id.val(dev.data.asset_type_id).trigger("change");
  if (dev.data.status_id > 0) t.mdl.device.frmEl.statusLabel.val(dev.data.status_id).trigger("change");
  if (dev.data.purchase_currency != "" && dev.data.purchase_currency != null) {
    t.mdl.device.frmEl.purchase_currency.val(dev.data.purchase_currency).trigger("change");
  }
  if (typeof dev.dropdown == "object") {
    if (dev.dropdown.lease_id != null) {
      t.mdl.device.frmEl.lease_id.append(new Option(dev.dropdown.lease_id.text, dev.dropdown.lease_id.id, true, true)).trigger("change");
    }
    if (dev.dropdown.location != null) {
      t.mdl.device.frmEl.location.append(new Option(dev.dropdown.location.text, dev.dropdown.location.id, true, true)).trigger("change");
    }
    if (dev.dropdown.internal_place != null) {
      t.internal_places.push(dev.dropdown.internal_place.id);
    }
    if (dev.dropdown.stock_place != null) {
      t.mdl.device.frmEl.stock_place.append(new Option(dev.dropdown.stock_place.text, dev.dropdown.stock_place.id, true, true)).trigger("change");
    }
    if (dev.dropdown.invoice != null) {
      t.mdl.device.frmEl.invoice_id.append(new Option(dev.dropdown.invoice.text, dev.dropdown.invoice.id, true, true)).trigger("change");
    }
    if (dev.dropdown.asset_owner != null) {
      t.mdl.device.frmEl.asset_owner.append(new Option(dev.dropdown.asset_owner.text, dev.dropdown.asset_owner.id, true, true)).trigger("change");
    }
    if (dev.dropdown.assigned_to != null) {
      t.mdl.device.frmEl.assigned_to
        .append(
          new Option(
            dev.dropdown.assigned_to.text,
            dev.dropdown.assigned_to.id,
            true,
            true
          )
        )
        .trigger("change");
    }
    if (dev.dropdown.assigned_place != null) {
      t.mdl.device.frmEl.assigned_place
        .append(
          new Option(
            dev.dropdown.assigned_place.text,
            dev.dropdown.assigned_place.id,
            true,
            true
          )
        )
        .trigger("change");
    }
    if (dev.dropdown.checkout_reason != null) {
      t.mdl.device.frmEl.checkout_reason
        .append(
          new Option(
            dev.dropdown.checkout_reason.text,
            dev.dropdown.checkout_reason.id,
            true,
            true
          )
        )
        .trigger("change");
    }
    if (dev.dropdown.supplier != null) {
      t.mdl.device.frmEl.supplier.append(new Option(dev.dropdown.supplier.text, dev.dropdown.supplier.id, true, true)).trigger("change");
    }
    if (dev.dropdown.model != null) {
      t.mdl.device.frmEl.model.attr('data-noLoadField', true);
      t.mdl.device.frmEl.model.append(new Option(dev.dropdown.model.text, dev.dropdown.model.id, true, true));
      t.mdl.device.frmEl.model.attr('data-noLoadField', false);
      t.mdl.device.frmEl.model.trigger("change");
    }
    if (dev.dropdown.department != null) {
      t.mdl.device.frmEl.department_id
        .append(new Option(
          dev.dropdown.department.text,
          dev.dropdown.department.id,
          true,
          true
        ))
        .trigger("change");
    }
    if (dev.dropdown.amc_supplier != null) {
      t.mdl.device.frmEl.amc_supplier_id
        .append(new Option(
          dev.dropdown.amc_supplier.text,
          dev.dropdown.amc_supplier.id,
          true,
          true
        ))
        .trigger("change");
    }
    t.mdl.device.frmEl.internal_place
    if (dev.dropdown.internal_place != null) {
      t.mdl.device.frmEl.internal_place
        .append(new Option(
          dev.dropdown.internal_place.text,
          dev.dropdown.internal_place.id,
          true,
          true
        ))
        .trigger("change");
    }
  }

  t.loadImageViewer(dev.data.image);
  if (typeof dev.data.block_device_movement !== "undefined") {
    t.mdl.device.frm.find("input[name='block_device_movement']")
      .prop("checked", dev.data.block_device_movement == 1);
  }
  if (typeof dev.dropdown === "object" && Array.isArray(dev.dropdown.device_rfid)) {
    $.each(dev.dropdown.device_rfid, function (i, value) {
      t.mdl.device.frmEl.deviceRfid
        .append(new Option(value, value, true, true))
        .trigger("change");
    });
  }
  t.mdl.device.frmEl.scanner.val(dev.data.scanner_id);
  if (typeof dev.dropdown === "object" && Array.isArray(dev.dropdown.in_antenna)) {
    $.each(dev.dropdown.in_antenna, function (i, value) {
      t.mdl.device.frmEl.inAntenna
        .append(new Option(value, value, true, true))
        .trigger("change");
    });
  }
  if (typeof dev.dropdown === "object" && Array.isArray(dev.dropdown.out_antenna)) {
    $.each(dev.dropdown.out_antenna, function (i, value) {
      t.mdl.device.frmEl.outAntenna
        .append(new Option(value, value, true, true))
        .trigger("change");
    });
  }

  t.mdl.device.frm.find("input[name='id']").val(dev.data.id);
  t.mdl.device.frm.find("input[name='asset_tag']").val(dev.data.asset_tag);
  t.mdl.device.frm.find("input[name='serial']").val(dev.data.serial);
  t.mdl.device.frm.find("input[name='product_number']").val(dev.data.product_number);
  t.mdl.device.frm.find("input[name='uuid']").val(dev.data.uuid);
  t.mdl.device.frm.find("input[name='name']").val(dev.data.name);
  t.mdl.device.frm.find("input[name='order_number']").val(dev.data.order_number);
  t.mdl.device.frm.find("input[name='purchase_cost']").val(dev.data.purchase_cost);
  t.mdl.device.frm.find("input[name='warranty_months']").val(dev.data.warranty_months);
  t.mdl.device.frmEl.ip.val(dev.data.ip);
  t.mdl.device.frmEl.mac.val(dev.data.mac);
  t.mdl.device.frm.find("textarea[name='notes']").html(dev.data.notes);
  t.mdl.device.frmEl.purchase_date.datepicker("update", dev.data.purchase_date);
  t.mdl.device.frmEl.warranty_start_date.datepicker("update", dev.data.warranty_start_date);
  t.mdl.device.frmEl.warrenty_end_date.datepicker("update", dev.data.warrenty_end_date);
  t.mdl.device.frmEl.amc_expire_date.datepicker("update", dev.data.amc_expire_date);
  t.mdl.device.frmEl.expected_checkin.datepicker(
    "update",
    dev.data.expected_checkin
  );
  if (dev.data1 != null) {
    t.mdl.device.frm.find("input[name='rate_hr']").val(dev.data1.rate_hr);
    t.mdl.device.frm.find("input[name='rate_day']").val(dev.data1.rate_day);
    t.mdl.device.frm.find("input[name='rate_week']").val(dev.data1.rate_week);
    t.mdl.device.frm.find("input[name='rate_month']").val(dev.data1.rate_month);
    t.mdl.device.frm.find("input[name='rate_quarterly']").val(dev.data1.rate_quarterly);
    t.mdl.device.frm.find("input[name='rate_half_yearly']").val(dev.data1.rate_half_yearly);
    t.mdl.device.frm.find("input[name='rate_yearly']").val(dev.data1.rate_yearly);
  }

  if (dev.data.requestable == 1) t.mdl.device.frm.find("input[name='requestable']").attr("checked", "checked");
  if (typeof dev.data.sez_device !== "undefined") {
    t.mdl.device.frmEl.sez_device.prop(
      "checked",
      dev.data.sez_device == 1
    );
  }
  if (typeof dev.data.high_pririty !== "undefined") {
    t.mdl.device.frmEl.high_pririty.prop(
      "checked",
      dev.data.high_pririty == 1
    );
  }
  t.clearCustomFields();
  if (typeof dev.custom_fields == "object" && typeof dev.custom_fields.html != "") {
    t.fillCustomFields(dev.custom_fields);
  }
  t.mdl.device.modal("show");
};

MyApp.prototype.getPurchaseDate = function () {
  var t = this;
  var selectedOption = t.mdl.device.frmEl.invoice_id.find(':selected');
  if (!selectedOption.attr('data-invoice_date')) {
    if (t.current_purchase_date != null) {
      var purchasedate = t.current_purchase_date.split('-').join('/');
      t.mdl.device.frmEl.purchase_date.val(purchasedate);
    } else {
      t.mdl.device.frmEl.purchase_date.val('');
    }
    t.mdl.device.frmEl.supplier.empty();
    if (t.current_supplier_name && t.current_supplier_id) {
      t.mdl.device.frmEl.supplier.append(new Option(t.current_supplier_name, t.current_supplier_id, true, true)).trigger("change");
    }
    if (t.order_no != null) {
      t.mdl.device.frm.find("input[name='order_number']").val(t.order_no);
    } else {
      t.mdl.device.frm.find("input[name='order_number']").val('');
    }
    if (t.purchase_cost != null) {
      t.mdl.device.frm.find("input[name='purchase_cost']").val(t.purchase_cost);
    } else {
      t.mdl.device.frm.find("input[name='purchase_cost']").val('');
    }
    if (t.currency_default != null) {
      t.mdl.device.frmEl.purchase_currency.val(t.currency_default).trigger('change');
    } else {
      t.mdl.device.frmEl.purchase_currency.val('').trigger('change');
    }
    return;
  }

  var date = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-invoice_date');
  t.mdl.device.frmEl.purchase_date.val(date);
  var supplier_name = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-name');
  var supplier_id = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-id');
  t.mdl.device.frmEl.supplier.append(new Option(supplier_name, supplier_id, true, true)).trigger("change");
  if (supplier_name == undefined && supplier_id == undefined && t.mdl.device.forAction == "edit") {
    t.mdl.device.frmEl.supplier.append(new Option(t.current_supplier_name, t.current_supplier_id, true, true)).trigger("change");
  }
  var order_number = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-po-number');
  t.mdl.device.frm.find("input[name='order_number']").val(order_number);
  var purchase_cost = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-purchase-cost');
  t.mdl.device.frm.find("input[name='purchase_cost']").val(purchase_cost || '0.00');
  var purchase_currency = t.mdl.device.frmEl.invoice_id.find(':selected').attr('data-currency');
  t.mdl.device.frmEl.purchase_currency.val(purchase_currency).trigger("change");
};

//DEVICE SUBMIT
MyApp.prototype.handleDeviceSubmit = function (e) {
  e.preventDefault();
  if (this.frmValidator.form() == false) return false;
  if (this.httpCall != true) return false;
  var t = this;
  t.mdl.device.btnSubmit.prop("disabled", true);
  t.httpCall = false;
  var formData = new FormData(t.mdl.device.frm[0]);
  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Device saved successfully");
          t.mdl.device.modal("hide");
          t.loadDevices();
        } else {
          t.showToast("error", data.msg || "Failed to save device");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    complete: function () {
      t.mdl.device.btnSubmit.prop("disabled", false);
      t.httpCall = true;
    },
    always: function () {
      t.mdl.device.btnSubmit.prop("disabled", false);
      t.httpCall = true;
    }
  });
};


// RESALE / DISPOSE FUNCTIONS
MyApp.prototype.handleResaleSubmit = function (e) {
  e.preventDefault();
  if (this.resaleFrmValidator.form() == false) return false;
  if (this.httpCall != true) return false;
  var t = this;
  t.httpCall = false;
  var formData = new FormData(t.mdl.resale.frm[0]);
  $.ajax({
    url: t.httpPostPath,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Device disposed successfully");
          t.mdl.resale.modal("hide");
          t.loadDevices();
        } else {
          t.showToast("error", data.msg || "Failed to dispose device");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    always: function () {
      t.httpCall = true;
    }
  });
};


// BULK ACTIONS
MyApp.prototype.handleBulkCheckoutClick = function (e) {
  if (e) e.preventDefault();
  if (this.config.url.bulk_checkout) {
    window.location = this.config.url.bulk_checkout;
  }
};

MyApp.prototype.handleBulkCheckinClick = function (e) {
  if (e) e.preventDefault();
  if (this.config.url.bulk_checkin) {
    window.location = this.config.url.bulk_checkin;
  }
};

MyApp.prototype.handleBulkUpdateClick = function (e) {
  if (e) e.preventDefault();
  if (this.config.url.bulk_update) {
    window.location = this.config.url.bulk_update;
  }
};

MyApp.prototype.handleBulkDeleteClick = function (e) {
  if (e) e.preventDefault();
  if (this.config.url.bulk_delete) {
    window.location = this.config.url.bulk_delete;
  }
};

MyApp.prototype.handleBulkReminderClick = function (e) {
  if (e) e.preventDefault();
  var selected = this.getSelectedDeviceIds();
  var tr = this.config.translations || {};

  if (!selected.length) {
    this.showToast("error", tr.Please_select_atleast_one_device || "Please select at least one device.");
    return;
  }
  if (!this.httpCall) return;
  this.httpCall = false;

  var t = this;
  $.ajax({
    url: t.config.url.bulk_send_chkout_accept_reminder,
    type: "POST",
    data: { _token: t.getCsrfToken(), ids: selected },
    success: function (data) {
      t.showToast(data && data.status === "success" ? "success" : "error", data && data.msg ? data.msg : tr.something_went_wrong || "Something went wrong.");
    },
    error: function () {
      t.showToast("error", tr.something_went_wrong || "Something went wrong.");
    },
    always: function () {
      t.httpCall = true;
    }
  });
};


// EXPORT FUNCTIONS
MyApp.prototype.handleExportExcelClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(this.btn.export_excel);
  this.exportExcel();
};
MyApp.prototype.handleExportPdfClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(this.btn.export_pdf);
  this.exportPdf();
};
MyApp.prototype.exportExcel = function () {
  this.cacheFilterValues();
  window.location = this.config.url.export_devices + "?q=" + (this.config.export_filters || "") + "&showDeletedDevices=" + (this.showDeletedDevices ? "true" : "false");
};
MyApp.prototype.exportPdf = function () {
  this.cacheFilterValues();
  window.location = this.config.url.export_devices_pdf + "?q=" + (this.config.export_filters || "") + "&showDeletedDevices=" + (this.showDeletedDevices ? "true" : "false");
};

MyApp.prototype.initPrintModalEvents = function () {
  var t = this;
  t.mdl.print.frmEl.print_usr_plc.on("change", function () {
    if ($(this).val() === "1") {
      t.mdl.print.frmEl.print_usr_info_cvr.removeClass("hide");
    } else {
      t.mdl.print.frmEl.print_usr_info_cvr.addClass("hide");
      t.mdl.print.frmEl.print_usr_info.val("").trigger("change");
    }
  });
  var select2Opts = { width: "100%" };
  t.mdl.print.frmEl.print_opt.select2($.extend({}, select2Opts, {
    placeholder: t.config.translations.select_print_Option || "Select Print Option",
    allowClear: false
  }));
  t.mdl.print.frmEl.print_usr_plc.select2($.extend({}, select2Opts, {
    placeholder: t.config.translations.select_option || "Select Option",
    allowClear: false
  }));
  t.mdl.print.frmEl.print_usr_info.select2($.extend({}, select2Opts, {
    minimumInputLength: 0,
    placeholder: t.config.translations.select_the_user || "Select User",
    allowClear: true,
    ajax: {
      url: t.config.getActivatedUsers || t.config.getUserByAjax,
      dataType: "json",
      delay: 300,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1
        };
      },
      processResults: function (data) {
        return {
          results: data.results || data.data || []
        };
      }
    },
    templateResult: function (s) {
      if (s.loading) {
        return $("<div>" + s.text + "</div>");
      }
      var html = '<div class="row">';
      html += '<div class="col-sm-10">';
      if (s.displayName) {
        html += '<div class="so-t"><i class="fa fa-user"></i> ' + s.displayName;
      } else {
        html += '<div class="so-t"><i class="fa fa-user"></i> ' + (s.first_name || "") + " " + (s.last_name || "");
      }
      html += '<span class="active-user"></span></div>';
      if (s.email) {
        html += '<div class="so-t"><i class="fa fa-envelope-o"></i> ' + s.email + '</div>';
      }
      if (s.employee_num) {
        html += '<div class="so-t"><i class="fa fa-credit-card"></i> ' + s.employee_num + '</div>';
      }
      if (s.company_name) {
        html += '<div class="so-t"><i class="fa fa-building"></i> ' + s.company_name + '</div>';
      }
      html += '</div>';
      html += '<div class="col-sm-2">';
      if (s.img_path) {
        html += '<img class="img-u" src="' + s.img_path + '" />';
      }
      html += '</div></div>';

      return $(html);
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    }
  }));
};

// PRINT LABEL
MyApp.prototype.handlePrintLabelClick = function (e) {
  if (e) e.preventDefault();
  this.hideTooltip(this.btn.print_label);
  this.printLabelModal();
};

MyApp.prototype.printLabelModal = function () {
  var selected = [];
  var t = this;
  t.tableBody.find(".row-checkbox:checked").each(function () {
    selected.push($(this).val());
  });
  if (selected.length === 0) {
    if (t.mergeObj && t.mergeObj.merge_items && t.mergeObj.merge_items.others.length > 0) {
      $.each(t.mergeObj.merge_items.others, function (i, v) {
        selected.push(v.id);
      });
    } else if (t.config.print_device_array && t.config.print_device_array.length > 0) {
      $.each(t.config.print_device_array, function (i, v) {
        selected.push(v.id);
      });
    }
  }
  if (selected.length === 0) {
    t.showToast('error', t.config.translations.Please_select_atleast_one_device || "Please select at least one device.");
    return;
  }
  t.mdl.print.frm.trigger("reset");
  t.mdl.print.frmEl.print_usr_info_cvr.addClass("hide");
  t.mdl.print.title.text(t.config.translations.select_print_Option || "Select Print Option");
  t.mdl.print.btnSubmit.text(t.config.translations.print || "Print");
  t._printSelectedIds = selected;
  t.mdl.print.modal("show");
};

MyApp.prototype.handlePrintSubmit = function (e) {
  e.preventDefault();
  var t = this;
  var selected = t._printSelectedIds || [];
  if (selected.length === 0) {
    t.tableBody.find(".row-checkbox:checked").each(function () {
      selected.push($(this).val());
    });
  }
  if (selected.length === 0) {
    if (t.mergeObj && t.mergeObj.merge_items && t.mergeObj.merge_items.others.length > 0) {
      $.each(t.mergeObj.merge_items.others, function (i, v) {
        selected.push(v.id);
      });
    } else if (t.config.print_device_array && t.config.print_device_array.length > 0) {
      $.each(t.config.print_device_array, function (i, v) {
        selected.push(v.id);
      });
    }
  }
  if (selected.length === 0) {
    t.showToast('error', t.config.translations.Please_select_atleast_one_device || "Please select at least one device.");
    return;
  }
  var option_value = t.mdl.print.frmEl.print_usr_plc.val();
  var user_info = '';
  if (option_value === "1") {
    user_info = t.mdl.print.frmEl.print_usr_info.val();
    if (!user_info) {
      t.showToast('error', t.config.translations.please_select_user || "Please select a user.");
      return;
    }
  }
  var print_opt = t.mdl.print.frmEl.print_opt.val();
  if (!print_opt) {
    t.showToast('error', t.config.translations.please_select_print_option || "Please select a print option.");
    return;
  }

  var url = '';
  var encodedIds = btoa(selected.join(","));
  switch (print_opt) {
    case '1':
      url = t.config.url.print_device_barcode + "/" + option_value + "/" + encodedIds + "/" + user_info;
      break;
    case '2': 
      url = t.config.url.print_device_onecol + "/" + option_value + "/" + encodedIds + "/" + user_info;
      break;
    case '3':
      url = t.config.url.print_device_twocol + "/" + option_value + "/" + encodedIds + "/" + user_info;
      break;
    case '4': 
      url = t.config.url.print_verticalcol + "/" + option_value + "/" + encodedIds + "/" + user_info;
      break;
    default:
      t.showToast('error', t.config.translations.invalid_print_option || "Invalid print option selected.");
      return;
  }

  if (url) {
    t.print_label_starter.attr("href", url);
    t.print_label_starter.get(0).click();
    t.mdl.print.modal("hide");
    if (t.mergeObj && t.mergeObj.merge_items) {
      t.mergeObj.merge_items.others = [];
    }
    if (t.config.print_device_array) {
      t.config.print_device_array = [];
    }
    t._printSelectedIds = [];
    if (t.mergeObj) {
      t.mergeObj.refreshUi();
    }
  }
};

var MergeMdl = function (options) {
  var t = this;
  t.config = options.config;
  t.mdl = options.mdl;
  t.ui = {};
  t.ui.tableBody = t.mdl.find('#deviceTable tbody');
  t.ui.no_ticket = t.mdl.find('.no_ticket');
  t.ui.count_shower = t.mdl.find('.count_shower');
  t.ui.others_wrapper = t.mdl.find('.others');
  t.dTbl = null;
  t.eventDispatcher = $({});
  t.httpCall = true;
  t.merge_items = {
    department_id: null,
    others: [],
    get_length: function () {
      try {
        var len = 0;
        if (Array.isArray(this.others)) {
          len = len + this.others.length;
        }
        return len;
      } catch (e) {
        console.log(e);
        return 0;
      }
    },
    canAddItem: function (id) {
      var found = false;
      if (Array.isArray(this.others)) {
        $.each(this.others, function (i, v) {
          if (v.id == id) {
            found = true;
            return false;
          }
        });
      }
      return found ? false : true;
    }
  };

  t.show = function (e) {
    if (typeof e != "undefined") {
      e.preventDefault();
    }
    if (t.merge_items.others.length === 0 && t.config.print_device_array && t.config.print_device_array.length > 0) {
      t.merge_items.others = t.config.print_device_array.slice();
    }
    t.mdl.modal("show");
    t.refreshUi();
  };

  t.remove = function (e) {
    e.preventDefault();
    var $this = $(this);
    var id = $this.attr('data-id');

    if (!id) {
      id = $this.closest('.merge_item_remove').attr('data-id');
    }

    if (id) {
      if (Array.isArray(t.merge_items.others) && t.merge_items.others.length > 0) {
        var removed = false;
        t.merge_items.others = t.merge_items.others.filter(function (item) {
          if (String(item.id) === String(id)) {
            removed = true;
            return false;
          }
          return true;
        });

        if (removed) {
          if (Array.isArray(t.config.print_device_array)) {
            t.config.print_device_array = t.config.print_device_array.filter(function (item) {
              return String(item.id) !== String(id);
            });
          }
          if (Array.isArray(t.config.device_id_array)) {
            t.config.device_id_array = t.config.device_id_array.filter(function (item) {
              return String(item) !== String(id);
            });
          }
          var $checkbox = $('.row-checkbox[value="' + id + '"]');
          if ($checkbox.length && $checkbox.is(':checked')) {
            $checkbox.prop('checked', false);
            if (window.myApp && window.myApp.selectedIds) {
              delete window.myApp.selectedIds[id];
            }
          }
        }
      }

      if (t.merge_items.get_length() < 1) {
        t.merge_items.department_id = null;
      }

      t.refreshUi();
    }
  };

  t.refreshUi = function () {
    t.ui.tableBody.empty();
    t.ui.count_shower.text("");
    var len = t.merge_items.get_length();
    if (t.dTbl) {
      try {
        t.dTbl.destroy();
      } catch (e) {
        console.log('Error destroying DataTable:', e);
      }
      t.dTbl = null;
    }

    if (len > 0) {
      t.ui.others_wrapper.removeClass('d-none');
      t.ui.no_ticket.addClass('d-none');

      var tableData = '';
      $.each(t.merge_items.others, function (i, v) {
        tableData += '<tr style="text-align:center">' +
          '<td>' + t.escapeHtml(v.asset_tag || v.id) + '</td>' +
          '<td>' + t.escapeHtml(v.name || v.serial || '') + '</td>' +
          '<td>' + t.escapeHtml(v.loc_name || '') + '</td>' +
          '<td>' + t.escapeHtml(v.cmp_name || '') + '</td>' +
          '<td>' + t.escapeHtml(v.lbl_name || '') + '</td>' +
          '<td><a href="#" data-id="' + v.id + '" class="merge_item_remove text-danger" data-placement="right" data-toggle="tooltip" title="Remove"><i class="bi bi-trash"></i></a></td>' +
          '</tr>';
      });

      t.ui.tableBody.html(tableData);
      t.ui.tableBody.find('[data-toggle="tooltip"]').each(function () {
        if (!$(this).data('bs.tooltip')) {
          $(this).tooltip();
        }
      });
      t.dTbl = $("#deviceTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50],
        responsive: true,
        autoWidth: false,
        columnDefs: [{
          orderable: false,
          targets: 5
        }],
        dom: "rtip",
        language: {
          paginate: {
            previous: t.config.translations.previous || "Previous",
            next: t.config.translations.next || "Next"
          },
          info: t.config.translations.showing_entries || "Showing _START_ to _END_ of _TOTAL_ entries",
          infoEmpty: t.config.translations.no_entries || "Showing 0 to 0 of 0 entries",
          infoFiltered: t.config.translations.filtered_from || "(filtered from _MAX_ total entries)",
          zeroRecords: t.config.translations.no_matching_records || "No matching records found",
          emptyTable: t.config.translations.no_device_added || "No devices added"
        },

        drawCallback: function () {
          var $wrapper = $(this).closest(".dataTables_wrapper");

          $wrapper.find(".dataTables_info").css({
            float: "left",
            marginTop: "15px"
          });
          t.bindRemoveEvents();
        },
        initComplete: function () {
          var api = this.api();
          t.bindRemoveEvents();
          $(".merge-table-search")
            .off("keyup")
            .on("keyup", function (e) {
              if (e.key === "Enter" || this.value.length === 0) {
                var v = $(this).validate_str_param();
                if (v === false && this.value.length > 0) {
                  alert("Please enter a valid value for search");
                  return;
                }
                api.search(this.value).draw();
              }
            });
          $(".merge-show-entries")
            .val(api.page.len())
            .off("change")
            .on("change", function () {
              api.page.len(parseInt($(this).val(), 10)).draw();
            });
          $(".merge-reload-btn")
            .off("click")
            .on("click", function () {
              $(".merge-table-search").val("");
              api.search("").draw();
            });
        }
      });

    } else {
      t.ui.others_wrapper.addClass('d-none');
      t.ui.no_ticket.removeClass('d-none');
    }

    t.ui.count_shower.text(len + " " + (t.config.translations.device || "device(s)") + " added for Label Print/Transfer");
    $(".device_transfer--count").text(len);
  };

  t.bindRemoveEvents = function () {
    t.mdl.off('click.mergeRemove', '.merge_item_remove');
    t.mdl.on('click.mergeRemove', '.merge_item_remove', function (e) {
      e.preventDefault();
      e.stopPropagation();
      t.remove.call(this, e);
    });
  };

  t.addToMerge = function (device, collection = false) {
    try {
      if (t.merge_items.canAddItem(device.id) == false) {
        if (collection !== true) {
          t.showToast('info', (t.config.translations.device || "Device") + " " + (device.asset_tag || "") + " " + (t.config.translations.is_already_added_in_merge_list || "is already added in merge list"));
        }
        return false;
      }

      t.merge_items.others.push(device);

      if (!t.config.print_device_array) {
        t.config.print_device_array = [];
      }
      t.config.print_device_array.push(device);

      if (!t.config.device_id_array) {
        t.config.device_id_array = [];
      }
      t.config.device_id_array.push(device.id);
      if (collection == false) {
        t.showToast('success', (t.config.translations.device || "Device") + " " + (device.asset_tag || "") + " " + (t.config.translations.has_been_added || "has been added"));
      }

      t.eventDispatcher.trigger("merge_happend");
      t.refreshUi();

      return true;
    } catch (e) {
      console.log(e);
      if (collection !== true) {
        t.showToast('error', t.config.translations.unable_to_add_device_label_print || "Unable to add device");
      }
      return false;
    }
  };

  t.escapeHtml = function (value) {
    return String(value === null || value === undefined ? "" : value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  };
  t.showToast = function (icon, message) {
    var tr = (t.config && t.config.translations) || {};
    if (window.Swal && typeof Swal.fire === "function") {
      Swal.fire({
        icon: icon,
        title: icon === "success" ? tr.success_title || "Success" : icon === "error" ? tr.error_title || "Error" : "Info",
        text: message,
        showConfirmButton: true,
        allowOutsideClick: false
      });
    } else if (window.toastr) {
      toastr[icon](message);
    } else {
      alert(message);
    }
  };

  t.mdl.modal("hide");
  t.bindRemoveEvents();
};

MyApp.prototype.handleActionPrintLabelClick = function (e) {
  var t = this;
  try {
    e.preventDefault();
    var deviceId = $(e.currentTarget).attr("data-id");
    var found = false;
    if (t._deviceData && Array.isArray(t._deviceData)) {
      $.each(t._deviceData, function (i, v) {
        if (String(v.id) === String(deviceId)) {
          if (t.mergeObj) {
            console.log("Device Data:", v);
            t.mergeObj.addToMerge(v);
          }
          found = true;
          return false;
        }
      });
    }

    if (!found) {
      var $row = $("#row-" + deviceId);
      if ($row.length) {
        var deviceData = {
          id: deviceId,
          asset_tag: $(e.currentTarget).attr("data-asset_tag") || "",
          name: $(e.currentTarget).attr("data-name") || "",
          company_id: $(e.currentTarget).attr("data-company_id") || "",
          cmp_name: $(e.currentTarget).attr("data-cmp_name") || ""
        };
        if (t.mergeObj) {
          t.mergeObj.addToMerge(deviceData);
        }
        found = true;
      }
    }

    if (found == false) {
      t.showToast('error', t.config.translations.unable_to_add_device_label_print || "Unable to add device for label print");
    }
  } catch (e) {
    console.log(e);
    t.showToast('error', t.config.translations.unable_to_add_device_label_print || "Unable to add device for label print");
  }
};

MyApp.prototype.addToPrintQueue = function (device) {
  var t = this;
  try {
    if (!t.config.print_device_array) {
      t.config.print_device_array = [];
    }

    var exists = false;
    $.each(t.config.print_device_array, function (i, v) {
      if (String(v.id) === String(device.id)) {
        exists = true;
        return false;
      }
    });

    if (exists) {
      t.showToast('info', t.config.translations.is_already_added_in_merge_list || "Device already added");
      return false;
    }

    t.config.print_device_array.unshift(device);
    t.showToast('success', t.config.translations.device + " " + (device.asset_tag || "") + " " + (t.config.translations.has_been_added || "added for label print"));
    return true;
  } catch (e) {
    console.log(e);
    t.showToast('error', t.config.translations.unable_to_add_device_label_print || "Unable to add device");
    return false;
  }
};


MyApp.prototype.handleShowAddedToPrintDevices = function (e) {
  if (e) e.preventDefault();
  // console.log(this.mergeObj);
  this.mdl.merge.css('z-index', '1060');
  if (this.mergeObj) {
    this.mergeObj.show(e);
  }
};

MyApp.prototype.handleReloadClick = function () { this.reload(); };
MyApp.prototype.handleToggleDeletedClick = function (e) { if (e) e.preventDefault(); this.toggleDeletedDevices(); };
MyApp.prototype.handleImportClick = function (e) { if (e) e.preventDefault(); this.importDevices(); };
MyApp.prototype.handleFilterToggleClick = function (e) { if (e) e.preventDefault(); $("#deviceFilterModal").modal("show"); };

MyApp.prototype.toggleDeletedDevices = function () {
  var t = this;
  var tr = t.config.translations || {};
  t.showDeletedDevices = !t.showDeletedDevices;
  var $btn = t.content.find(t.btn.deleted);
  $btn.attr(
    "title",
    t.showDeletedDevices ? tr.view_non_deleted || "View Active Devices" : tr.view_deleted_device || "View Deleted Devices"
  );
  $btn.toggleClass("is-active", t.showDeletedDevices);
  t.currentPage = 1;
  t.loadDevices();
};

MyApp.prototype.importDevices = function () {
  if (this.config.url.import_devices) {
    window.location = this.config.url.import_devices;
  }
};



MyApp.prototype.bindFilterEvents = function () {
  var t = this;
  $("#filter_by_manufacturer").on("change", function () {
    var manufacturerId = $(this).val();
    var $modelSelect = $("#filter_by_model");
    $modelSelect.empty().append(new Option(t.config.translations.select_the_model || "Select Model", ""));

    if (manufacturerId && manufacturerId !== "null" && manufacturerId !== "") {
      $.ajax({
        url: t.config.getModelByManufacturer + "/" + manufacturerId,
        type: "GET",
        dataType: "json",
        success: function (data) {
          if (data && data.results) {
            $.each(data.results, function (i, v) {
              $modelSelect.append(new Option(v.text, v.id));
            });
          }
          $modelSelect.trigger("change");
        },
        error: function () { loadAllModels(); }
      });
    } else {
      loadAllModels();
    }

    function loadAllModels() {
      if (t.config.models) {
        $.each(t.config.models, function (i, v) {
          $modelSelect.append(new Option(v.text, v.id));
        });
      }
      $modelSelect.trigger("change");
    }
  });
  $("#filter_by_location").on("change", function () {
    var locationId = $(this).val();
    var $internalPlace = $("#filter_by_internal_place");
    var $stockPlace = $("#f_stock_place");
    var $userInternalPlace = $("#filter_by_user_internalplace");
    var $mappedLocation = $("#filter_by_mapped_location");
    var placeholder = t.config.translations.Select_Place || "Select Place";

    $internalPlace.empty().append(new Option(placeholder, ""));
    $stockPlace.empty().append(new Option(placeholder, ""));
    $userInternalPlace.empty().append(new Option(placeholder, ""));
    $mappedLocation.empty().append(new Option(placeholder, ""));

    if (locationId && locationId !== "null" && locationId !== "") {
      $.ajax({
        url: t.config.getInternalPlaceByAjax + "/" + locationId,
        type: "GET",
        dataType: "json",
        success: function (data) {
          if (data && data.results) {
            $.each(data.results, function (i, v) {
              $internalPlace.append(new Option(v.text, v.id));
              $stockPlace.append(new Option(v.text, v.id));
              $userInternalPlace.append(new Option(v.text, v.id));
              $mappedLocation.append(new Option(v.text, v.id));
            });
          }
          $internalPlace.trigger("change");
          $stockPlace.trigger("change");
          $userInternalPlace.trigger("change");
          $mappedLocation.trigger("change");
        }
      });
    }
  });

  $("#filter_by_custom_field").on("change", function () {
    var customField = $(this).val();
    var $customFieldValue = $("#filter_by_custom_field_value");

    if (customField && customField !== "null" && customField !== "") {
      $.ajax({
        url: t.config.getCustomFieldValue + "/" + customField,
        type: "GET",
        dataType: "json",
        success: function (data) {
          $customFieldValue.empty().append(new Option("Select Custom Field Value", ""));
          if (data && data.length > 0) {
            $.each(data, function (i, v) {
              $customFieldValue.append(new Option(v.text, v.id));
            });
          }
          $customFieldValue.trigger("change");
        }
      });
    } else {
      $customFieldValue.empty().append(new Option("Select Custom Field Value", ""));
      $customFieldValue.trigger("change");
    }
  });

  $("#filter_by_reason_type").on("change", function () {
    var actionType = $(this).val();
    var $reason = $("#filter_by_reason");
    $reason.val("").trigger("change");

    if (actionType && actionType !== "null" && actionType !== "") {
      $reason.select2($.extend({}, { width: "100%" }, {
        dropdownParent: $reason.parent(),
        ajax: {
          url: t.config.url.getReasonOptions,
          dataType: "json",
          data: function (p) {
            return {
              action_type: actionType,
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300
        },
        allowClear: true,
        placeholder: t.config.translations.select_the_reason || "Select Reason",
        templateSelection: function (data, container) {
          $(container).attr('title', data.text);
          return data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
        }
      }));
    } else {
      $reason.select2($.extend({}, { width: "100%" }, {
        dropdownParent: $reason.parent(),
        allowClear: true,
        placeholder: t.config.translations.select_the_reason || "Select Reason"
      }));
      $reason.empty().append(new Option(t.config.translations.select_the_reason || "Select Reason", ""));
      $reason.trigger("change");
    }
  });

  $("#filter_by_stock_location").on("change", function () {
    var locationId = $(this).val();
    var $stockPlace = $("#f_stock_place");
    var placeholder = t.config.translations.Select_Place || "Select Place";
    $stockPlace.empty().append(new Option(placeholder, ""));
    if (locationId && locationId !== "null" && locationId !== "") {
      $.ajax({
        url: t.config.getInternalPlaceByAjax + "/" + locationId,
        type: "GET",
        dataType: "json",
        success: function (data) {
          if (data && data.results) {
            $.each(data.results, function (i, v) {
              $stockPlace.append(new Option(v.text, v.id));
            });
          }
          $stockPlace.trigger("change");
        }
      });
    }
  });
  $("#tblStatusLbl").on("change", function () {
    t.cacheFilterValues();
    t.currentPage = 1;
    t.loadDevices();
  });
};

MyApp.prototype.populateFilterDropdowns = function () {
  var t = this;
  var tr = t.config.translations || {};

  function populateSelect(selector, data, placeholder) {
    var $el = $(selector);
    if (!$el.length || !data || !Array.isArray(data)) return;
    $el.empty();
    $el.append(new Option(placeholder || "Select", ""));
    $.each(data, function (i, v) {
      $el.append(new Option(v.text, v.id));
    });
    $el.val("").trigger("change");
  }

  function populateStaticSelect(selector, data, placeholder, defaultVal) {
    var $el = $(selector);
    if (!$el.length || !data || !Array.isArray(data)) return;
    $el.empty();
    $el.append(new Option(placeholder || "No Filter", defaultVal || "null"));
    $.each(data, function (i, v) {
      $el.append(new Option(v.text, v.id));
    });
    $el.val(defaultVal || "null").trigger("change");
  }

  function populateAjaxSelect(selector, url, placeholder, options) {
    options = options || {};
    var $el = $(selector);
    $el.select2({
      width: "100%",
      dropdownParent: options.dropdownParent || $el.parent(),
      allowClear: true,
      placeholder: placeholder,
      ajax: {
        url: url,
        dataType: "json",
        delay: 300,
        transport: function (params, success, failure) {
          if (options.transport) {
            return options.transport(params, success, failure);
          }
          var request = $.ajax(params);
          request.then(success);
          request.fail(failure);
          return request;
        },
        data: function (params) {
          if (options.data) {
            return options.data(params);
          }
          return {
            search: params.term,
            page: params.page || 1
          };
        },
        processResults: function (data) {
          if (options.processResults) {
            return options.processResults(data);
          }
          return data;
        }
      },

      language: options.language || {},
      templateResult: options.templateResult,
      templateSelection: options.templateSelection
    });

    return $el;
  }

  populateSelect("#filter_by_manufacturer", t.config.manufacturer, tr.select_the_manufacturer || "Select Manufacturer");
  populateSelect("#filter_by_model", t.config.models, tr.select_the_model || "Select Model");
  if (t.config.locations && Array.isArray(t.config.locations) && t.config.locations.length > 0) {
    populateSelect("#filter_by_location", t.config.locations, tr.select_the_location || "Select Location");
  } else {
    var $location = $("#filter_by_location");
    if ($location.length) {
      $location.select2($.extend({}, { width: "100%" }, {
        dropdownParent: $location.parent(),
        ajax: {
          url: t.config.getLocationByAjax,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300
        },
        allowClear: true,
        placeholder: tr.select_the_location || "Select Location",
        templateSelection: function (data, container) {
          $(container).attr('title', data.text);
          return data.text.length > 55 ? data.text.substring(0, 55) + '...' : data.text;
        }
      }));
      $location.trigger("change");
    }
  }
  populateSelect("#filter_by_category", t.config.category, tr.select_the_category || "Select Category");
  populateAjaxSelect("#f_assigned_to", t.config.getUserByAjax || t.config.getUserByAjax, tr.select_the_user || "Select User");
  populateAjaxSelect("#assigned_place", t.config.ajaxGetInternalPlace || t.config.getInternalPlaceByAjax, tr.Select_Place || "Select Place");
  populateAjaxSelect("#f_stock_place", t.config.ajaxGetInternalPlace || t.config.getInternalPlaceByAjax, tr.Select_Place || "Select Place");
  populateSelect("#asset_type_id", t.config.assetType, tr.asset_type || "Select Device Type");
  populateSelect("#last_checkout_project", t.config.project, tr.Select_Project || "Select Project");
  populateAjaxSelect("#filter_by_dept", t.config.url.getAssetDepartments || t.config.getAssetDepartments, tr.select_the_department || "Select Department");
  populateAjaxSelect("#filter_asset_owner", t.config.getUserByAjax, tr.select_the_asset_owner || "Select Asset Owner");
  populateSelect("#filter_by_user_location", t.config.locations, tr.select_the_location || "Select User Location");
  populateSelect("#filter_by_user_base_location", t.config.locations, tr.select_the_location || "Select User Base Location");

  populateSelect("#filter_by_stock_location", t.config.locations, tr.select_the_location || "Select Stock Location");
  populateSelect("#filter_by_user_internalplace", t.config.internalPlaces, tr.Select_Place || "Select User Internal Place");
  populateAjaxSelect("#filter_by_internal_place", t.config.ajaxGetInternalPlace, tr.Select_Place || "Select Internal Place");
  populateAjaxSelect("#filter_by_asset_tag", t.config.getDeviceByAjax, tr.asset_tag || "Asset Tag");
  populateAjaxSelect("#filter_by_user_internalplace", t.config.ajaxGetInternalPlace, tr.Select_Place || "Select Internal Place");
  populateAjaxSelect("#filter_by_mapped_location", t.config.getLocationByAjax, tr.select_the_location || "Select Mapped Location");
  populateSelect("#filter_by_custom_field", t.config.customField, tr.select_custom_field || "Select Custom Field");
  populateAjaxSelect("#filter_by_purchase_reference", t.config.getInvoiceByAjax, tr.Select_the_Purchase_Invoice || "Select Purchase Invoice");
  populateAjaxSelect("#filter_by_department", t.config.url.getAssetDepartments, tr.select_the_department || "Select Department");
  var rfidData = [];
  if (t.config.rfids && Array.isArray(t.config.rfids)) {
    $.each(t.config.rfids, function (index, value) {
      if (typeof value === 'object' && value !== null) {
        rfidData.push(value);
      } else {
        rfidData.push({ id: value, text: value });
      }
    });
  } else if (t.config.rfids && typeof t.config.rfids === 'object') {
    $.each(t.config.rfids, function (key, value) {
      rfidData.push({ id: key, text: value });
    });
  }

  populateSelect("#filter_by_rfid", rfidData, tr.no_filter || "No Filter", "");
  populateStaticSelect("#f_device_occure_type", t.config.deviceOccureTypes, tr.no_filter || "No Filter", "");
  populateStaticSelect("#filter_by_assigned_to", t.config.assignedToOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_added_from", t.config.addedFromOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_warranty_status", t.config.warrantyStatusOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_allocation_type", t.config.allocationType, tr.no_filter || "No Filter", "");
  populateStaticSelect("#filter_by_audit_confirmation", t.config.auditConfirmationOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_detected_from", t.config.detectedFromOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_sez_device", t.config.sezDeviceOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_high_priority_device", t.config.highPriorityOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_under_transfer_device", t.config.underTransferOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_request_able_device", t.config.requestableOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_rdp_status", t.config.rdpStatusOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_condition_by_user_assign", t.config.conditionOptions, tr.no_filter || "No Filter", "null");
  populateStaticSelect("#filter_by_device_with_rfid", t.config.deviceWithRfidOptions, tr.no_filter || "No Filter", "null");
  var $customFieldValue = $("#filter_by_custom_field_value");
  if ($customFieldValue.length) {
    $customFieldValue.empty().append(new Option("Select Custom Field Value", ""));
    $customFieldValue.trigger("change");
  }
  populateStaticSelect("#filter_by_reason_type", t.config.reasonTypeOptions, tr.select_the_reason_type || "Select Reason Type", "null");
  var $reason = $("#filter_by_reason");
  if ($reason.length) {
    $reason.empty().append(new Option(tr.select_the_reason || "Select Reason", ""));
    $reason.trigger("change");
  }
};

MyApp.prototype.initFilters = function () {
  var t = this;
  var select2Opts = { width: "100%", dropdownParent: t.filters.wrapper };
  var tr = t.config.translations || {};

  var multipleSelectIds = [
    "filter_by_model",
    "filter_by_location",
    "filter_by_category",
    "assigned_place",
    "f_stock_place",
    "filter_by_internal_place",
    "filter_by_user_internalplace",
    "filter_by_mapped_location",
    "filter_by_purchase_reference",
    "filter_by_department",
    "f_device_occure_type"
  ];

  var singleSelectIds = [
    "filter_by_manufacturer",
    "f_assigned_to",
    "filter_by_date",
    "last_checkout_project",
    "filter_by_dept",
    "filter_asset_owner",
    "filter_by_assigned_to",
    "filter_by_added_from",
    "filter_by_warranty_status",
    "filter_by_allocation_type",
    "filter_by_audit_confirmation",
    "filter_by_asset_tag",
    "filter_by_user_location",
    "filter_by_user_base_location"
  ];

  $.each(multipleSelectIds, function (i, id) {
    var $el = $("#" + id);
    if ($el.length) {
      if (!$el.hasClass('select2-hidden-accessible')) {
        $el.select2($.extend({}, select2Opts, {
          allowClear: true,
          placeholder: tr.no_filter || "No Filter",
          multiple: true
        }));
      }
    }
  });

  $.each(singleSelectIds, function (i, id) {
    var $el = $("#" + id);
    if ($el.length) {
      if (!$el.hasClass('select2-hidden-accessible')) {
        $el.select2($.extend({}, select2Opts, {
          allowClear: true,
          placeholder: tr.no_filter || "No Filter"
        }));
      }
    }
  });

  var $statusLbl = $("#tblStatusLbl");
  if ($statusLbl.length) {
    $statusLbl.select2($.extend({}, select2Opts, {
      allowClear: true,
      placeholder: tr.all_status || "All Status",
      data: t.config.filterLabels || []
    }));
  }

  t.populateFilterDropdowns();
  t.initDateRangePicker();
  t.filters.search = t.content.find(t.btn.search_input);
};

MyApp.prototype.initDateRangePicker = function () {
  var t = this;
  var start = moment().subtract(29, 'days');
  var end = moment();
  function cb(start, end) {
    $('#deviceReportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    var dateRange = start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
    $('input[name="daterange"]').val(dateRange);
    t.config.date_range = dateRange;
  }

  $('#deviceReportrange').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
      'Today': [moment(), moment()],
      'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days': [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month': [moment().startOf('month'), moment().endOf('month')],
      'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
  }, cb);

  cb(start, end);
};


MyApp.prototype.cacheFilterValues = function () {
  var t = this;
  var tr = t.config.translations || {};
  t.config.other_filters = {};
  t.config.search = t.filters.search && t.filters.search.val ? t.filters.search.val() || "" : "";

  var statusLbl = $("#tblStatusLbl").val();
  if (statusLbl && statusLbl !== "null") {
    t.config.other_filters.status_id = statusLbl;
  }
  // for mutiple select
  var arrayFilters = [
    "filter_by_model",
    "filter_by_location",
    "filter_by_category",
    "assigned_place",
    "f_stock_place",
    "filter_by_internal_place",
    "filter_by_user_internalplace",
    "filter_by_mapped_location",
    "filter_by_purchase_reference",
    "filter_by_department",
    "f_device_occure_type"
  ];

  var filterMap = {
    // for single
    "filter_by_manufacturer": "manufacturer",
    "filter_by_model": "model",
    "filter_by_location": "location",
    "filter_by_category": "category",
    "f_assigned_to": "assigned_user",
    "filter_by_date": "based_on",
    "assigned_place": "assigned_place",
    "f_stock_place": "stock_place",
    "asset_type_id": "asset_type_id",
    "f_device_occure_type": "device_occure_type",
    "last_checkout_project": "last_checkout_project",
    "filter_by_dept": "department",
    "filter_asset_owner": "asset_owner",
    "filter_by_assigned_to": "device_assigned_to",
    "filter_by_added_from": "added_from",
    "filter_by_warranty_status": "warranty_status",
    "filter_by_allocation_type": "allocation_type",
    "filter_by_audit_confirmation": "audit_confirmation",
    "filter_by_asset_tag": "asset_tag",
    "filter_by_user_location": "user_location",
    "filter_by_user_base_location": "user_base_location",
    "filter_by_stock_location": "stock_location",
    "filter_by_user_internalplace": "user_internalplace",
    "filter_by_purchase_reference": "purchase_reference",
    "filter_by_detected_from": "detected_on",
    "filter_by_sez_device": "sez_device",
    "filter_by_high_priority_device": "high_priority_device",
    "filter_by_under_transfer_device": "under_transfer_device",
    "filter_by_request_able_device": "requestable",
    "filter_by_condition_by_user_assign": "condition_by_user_assign",
    "filter_by_device_size": "device_size",
    "filter_by_rdp_status": "rdp_status",
    "filter_by_rfid": "rfid",
    "filter_by_internal_place": "internal_place",
    "filter_by_custom_field": "custom_field",
    "filter_by_custom_field_value": "custom_field_value",
    "filter_by_mapped_location": "mapped_location",
    "filter_by_device_with_rfid": "filter_by_device_with_rfid",
    "filter_by_reason_type": "filter_by_reason_type",
    "filter_by_reason": "filter_by_reason",
    "filter_by_department": "asset_department"
  };

  $.each(filterMap, function (id, key) {
    var $el = $("#" + id);
    if (!$el.length) return;
    var val = $el.val();
    if (!val || val === null || val === "null" || val === "") {
      return;
    }
    if (arrayFilters.indexOf(id) !== -1) {
      var values = [];
      if ($el.prop('multiple')) {
        values = $el.val() || [];
        values = values.filter(function (v) {
          return v && v !== "" && v !== "null";
        });
        if (values.length > 0) {
          t.config.other_filters[key] = values;
        }
      } else {
        t.config.other_filters[key] = [val];
      }
    } else {
      t.config.other_filters[key] = val;
    }
  });

  if (t.config.date_range) {
    t.config.other_filters.date_range = t.config.date_range;
  }

  t.config.export_filters = btoa(JSON.stringify({
    search: t.config.search,
    other_filters: t.config.other_filters,
    showDeletedDevices: t.showDeletedDevices
  }));

  t.updateFilterCount();
};

MyApp.prototype.updateFilterCount = function () {
  var count = 0;
  var t = this;
  $.each(t.config.other_filters, function (key, value) {
    if (value && value !== "null" && value !== "") {
      count++;
    }
  });

  var excludeKeys = ['status_id', 'date_range'];
  $.each(excludeKeys, function (i, key) {
    if (t.config.other_filters[key]) {
      count--;
    }
  });

  var $badge = $('.filter-count-badge');
  if (count > 0) {
    $badge.text(count).removeClass('d-none');
  } else {
    $badge.addClass('d-none');
  }
};

MyApp.prototype.applyFilters = function (e) {
  if (e) e.preventDefault();
  var t = this;
  t.cacheFilterValues();
  t.currentPage = 1;
  t.loadDevices();
  $("#deviceFilterModal").modal("hide");
};

MyApp.prototype.clearFilters = function (e) {
  if (e) e.preventDefault();
  var t = this;

  var filterIds = [
    "filter_by_manufacturer",
    "filter_by_model",
    "filter_by_location",
    "filter_by_category",
    "f_assigned_to",
    "filter_by_date",
    "assigned_place",
    "f_stock_place",
    "asset_type_id",
    "device_occure_type",
    "last_checkout_project",
    "filter_by_dept",
    "filter_asset_owner",
    "filter_by_assigned_to",
    "filter_by_added_from",
    "filter_by_warranty_status",
    "filter_by_allocation_type",
    "filter_by_audit_confirmation",
    "filter_by_asset_tag",
    "filter_by_user_location",
    "filter_by_user_base_location",
    "filter_by_stock_location",
    "filter_by_user_internalplace",
    "filter_by_purchase_reference",
    "filter_by_detected_from",
    "filter_by_sez_device",
    "filter_by_high_priority_device",
    "filter_by_under_transfer_device",
    "filter_by_request_able_device",
    "filter_by_condition_by_user_assign",
    "filter_by_device_size",
    "filter_by_rdp_status",
    "filter_by_rfid",
    "filter_by_internal_place",
    "filter_by_custom_field",
    "filter_by_custom_field_value",
    "filter_by_mapped_location",
    "filter_by_device_with_rfid",
    "filter_by_reason_type",
    "filter_by_reason",
    "filter_by_department"
  ];

  $.each(filterIds, function (i, id) {
    var $el = $("#" + id);
    if ($el.length) {
      if ($el.prop('multiple')) {
        $el.val([]).trigger("change");
      } else {
        $el.val(null).trigger("change");
      }
    }
  });

  t.config.date_range = null;
  $("#daterange").val("");
  $('#deviceReportrange span').html('Select Date Range');

  t.config.other_filters = {};
  t.config.search = "";
  if (t.filters.search) {
    t.filters.search.val("");
  }

  $("#tblStatusLbl").val(null).trigger("change");
  t.currentPage = 1;
  t.loadDevices();
  $("#deviceFilterModal").modal("hide");
  $('.filter-count-badge').addClass('d-none');
};


MyApp.prototype.initListControls = function () {
  var t = this;
  t.listUi.pageLength = t.content.find(t.btn.page_length);
  t.listUi.searchInput = t.content.find(t.btn.search_input);
  t.listUi.pagination = t.content.find('.pagination-container');
  t.listUi.summary = t.content.find('.table-summary');
};

MyApp.prototype.handleSearchInput = function () {
  var t = this;
  window.clearTimeout(t.searchTimer);
  t.searchTimer = window.setTimeout(function () {
    t.cacheFilterValues();
    t.currentPage = 1;
    t.loadDevices();
  }, 300);
};

MyApp.prototype.handlePageLengthChange = function (e) {
  if (e) e.preventDefault();
  var length = parseInt($(e.currentTarget).val(), 10);
  if (length > 0) {
    this.perPage = length;
    this.currentPage = 1;
    this.loadDevices();
  }
};

MyApp.prototype.handlePageClick = function (e) {
  e.preventDefault();
  var $btn = $(e.currentTarget);
  if ($btn.hasClass("disabled") || $btn.hasClass("current")) return;
  var page = parseInt($btn.attr("data-page"), 10);
  if (!page || page < 1 || page > this.totalPages) return;
  this.currentPage = page;
  this.loadDevices();
};



MyApp.prototype.handleSelectAllClick = function () {
  var self = this;
  var checked = $("#checkAll").prop("checked");
  self.tableBody.find("tr").each(function () {
    var $checkbox = $(this).find(".row-checkbox");
    if ($checkbox.length) {
      $checkbox.prop("checked", checked);
      var id = $checkbox.val();
      if (checked) self.selectedIds[id] = true;
      else delete self.selectedIds[id];
    }
  });
};


//  EXPAND / COLLAPSE Handle
MyApp.prototype.handleRowExpandToggle = function (e) {
  e.preventDefault();
  e.stopPropagation();
  var $btn = $(e.currentTarget);
  var rowId = $btn.data("id");
  if (!rowId) return;
  var $row = $("#row-" + rowId);
  if (!$row.length) return;
  if (this.expandedRows[rowId]) {
    this.collapseRow(rowId);
  } else {
    this.expandRow(rowId);
  }
};

MyApp.prototype.expandRow = function (rowId) {
  var t = this;
  if (t.expandedRows[rowId]) return;
  var $row = $("#row-" + rowId);
  if (!$row.length) return;
  $row.find('.toggle-element').removeClass('collapsed');
  t.expandedRows[rowId] = true;
  $row.addClass('row-expanded');
  $row.find(".dt-row-expand-toggle .expand-icon").html(t.getExpandIcon("minus"));
  $row.find(".dt-row-expand-toggle").attr("title", "Collapse row");
};

MyApp.prototype.collapseRow = function (rowId) {
  var t = this;
  if (!t.expandedRows[rowId]) return;
  var $row = $("#row-" + rowId);
  if ($row.length) {
    $row.find('.toggle-element').addClass('collapsed');
    $row.removeClass('row-expanded');
    $row.find(".dt-row-expand-toggle .expand-icon").html(t.getExpandIcon("plus"));
    $row.find(".dt-row-expand-toggle").attr("title", "Expand row");
  }
  delete t.expandedRows[rowId];
};

MyApp.prototype.restoreExpandedRows = function () {
  var t = this;
  var rowIds = Object.keys(t.expandedRows);
  $.each(rowIds, function (index, rowId) {
    var $row = $("#row-" + rowId);
    if ($row.length && !$row.hasClass('row-expanded')) {
      $row.find('.toggle-element').removeClass('collapsed');
      $row.addClass('row-expanded');
      $row.find(".dt-row-expand-toggle .expand-icon").html(t.getExpandIcon("minus"));
      $row.find(".dt-row-expand-toggle").attr("title", "Collapse row");
    }
  });
};

MyApp.prototype.handleExpandAllToggle = function (e) {
  e.preventDefault();
  var t = this;
  var $toggleElements = $(".toggle-element");
  if (!$toggleElements.length) return;
  var allCollapsed = $toggleElements.filter(':not(.collapsed)').length === 0;
  if (allCollapsed) {
    $toggleElements.removeClass('collapsed');
    t.expandAllRows();
  } else {
    $toggleElements.addClass('collapsed');
    t.collapseAllRows();
  }
  t.updateExpandAllButton();
};

MyApp.prototype.expandAllRows = function () {
  var t = this;
  t.tableBody.find("tr").each(function () {
    var rowId = $(this).data("row-id");
    if (rowId) {
      $(this).find('.toggle-element').removeClass('collapsed');
      $(this).addClass('row-expanded');
      $(this).find(".dt-row-expand-toggle .expand-icon").html(t.getExpandIcon("minus"));
      $(this).find(".dt-row-expand-toggle").attr("title", "Collapse row");
      t.expandedRows[rowId] = true;
    }
  });
};

MyApp.prototype.collapseAllRows = function () {
  var t = this;
  var rowIds = Object.keys(t.expandedRows);
  $.each(rowIds, function (index, rowId) {
    var $row = $("#row-" + rowId);
    if ($row.length) {
      $row.find('.toggle-element').addClass('collapsed');
      $row.removeClass('row-expanded');
      $row.find(".dt-row-expand-toggle .expand-icon").html(t.getExpandIcon("plus"));
      $row.find(".dt-row-expand-toggle").attr("title", "Expand row");
    }
  });
  t.expandedRows = {};
};

MyApp.prototype.updateExpandAllButton = function () {
  var t = this;
  var $btn = t.content.find(t.btn.expand);
  var $icon = $btn.find('.dvl-expand-icon');
  if ($('.toggle-element:not(.collapsed)').length > 0) {
    $icon.html(t.getExpandIcon("minus-all"));
    $btn.attr('title', 'Collapse All Rows');
  } else {
    $icon.html(t.getExpandIcon("plus-all"));
    $btn.attr('title', 'Expand All Rows');
  }
};

MyApp.prototype.getExpandIcon = function (type) {
  var icons = {
    plus: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    minus: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
    "plus-all": '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 14 10 14 10 20"></polyline><polyline points="20 10 14 10 14 4"></polyline><line x1="10" y1="14" x2="3" y2="21"></line><line x1="21" y1="3" x2="14" y2="10"></line></svg>',
    "minus-all": '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>'
  };
  return icons[type] || icons.plus;
};


MyApp.prototype.renderRowCheckbox = function (data, type) {
  if (type !== "display") return data;
  var checked = this.selectedIds[data] ? "checked" : "";
  return [
    '<label class="d-inline-flex align-items-center justify-content-center">',
    '<input type="checkbox" class="row-checkbox form-check-input" value="',
    this.escapeHtml(data), '" ', checked, '>',
    '</label>'
  ].join("");
};

MyApp.prototype.renderDeviceTagCell = function (record, type) {
  var tag = this.safeDisplayValue(record.asset_tag, "-");
  if (type !== "display") return tag;
  var ip = this.safeDisplayValue(record.ip, "");
  var mac = this.safeDisplayValue(record.mac, "");
  var deviceImg = this.safeDisplayValue(record.device_img, "");
  var category = this.safeDisplayValue(record.cat_name, "");
  var tagHtml = tag;
  if (tag !== "-" && this.config.url.info) {
    var truncatedText = tag.length > 20 ? tag.substring(0, 20) + "..." : tag;
    tagHtml = '<a href="' + this.config.url.info + '/' + record.id + '" data-toggle="tooltip" data-original-title="' + this.escapeHtml(tag) + '" class="dvl-dev-tag-link">' + this.escapeHtml(truncatedText) + '</a>';
  }

  return [
    '<div class="dvl-dev-cell">',
    '<div class="dvl-dev-thumb">',
    deviceImg
      ? '<img src="' + this.escapeHtml(deviceImg) + '" alt="' + this.escapeHtml(category || "Device") + '" class="dvl-dev-thumb-img">'
      : '<svg viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="1.2">' +
      '<rect x="2" y="4" width="20" height="13" rx="2"></rect>' +
      '<path d="M22 17H2"></path>' +
      '<rect x="6" y="20" width="12" height="1" rx=".5"></rect>' +
      '<line x1="1" y1="20" x2="23" y2="20"></line>' +
      '</svg>',
    '</div>',
    '<div class="dvl-dev-info">',
    tagHtml ? '<div class="dvl-dev-name" data-bs-toggle="tooltip" data-bs-title="Device Tag">' + tagHtml + "</div>" : "",
    ip ? '<div class="dvl-dev-ip toggle-element collapsed" data-bs-toggle="tooltip" data-bs-title="IP Address">' + this.escapeHtml(ip) + "</div>" : "",
    mac ? '<div class="dvl-dev-mac toggle-element collapsed" style="font-size:11px;color:#7F7F7F;" data-bs-toggle="tooltip" data-bs-title="MAC Address">' + this.escapeHtml(mac) + "</div>" : "",
    this.renderDetectionIcons(record),
    '</div>',
    '</div>'
  ].join("");
};

MyApp.prototype.renderDetectionIcons = function (record) {
  var html = [];
  html.push('<div class="d-flex align-items-center gap-2 toggle-element collapsed">');
  if (record.addItmDetectedMode && record.addItmDetectedMode.icon) {
    html.push('<i class="fa ' + this.escapeHtml(record.addItmDetectedMode.icon) + '" data-bs-toggle="tooltip" data-bs-title="' + this.escapeHtml(record.addItmDetectedMode.from || "Network") + '"></i>');
  }
  if (record.addItmAzureDetectedMode && record.addItmAzureDetectedMode.icon) {
    html.push('<i class="fa ' + this.escapeHtml(record.addItmAzureDetectedMode.icon) + '" data-bs-toggle="tooltip" data-bs-title="' + this.escapeHtml(record.addItmAzureDetectedMode.from || "Azure") + '"></i>');
  }
  if (record.addRdpDetectedMode && record.addRdpDetectedMode.icon) {
    html.push('<i class="fa ' + this.escapeHtml(record.addRdpDetectedMode.icon) + '" data-bs-toggle="tooltip" data-bs-title="' + this.escapeHtml(record.addRdpDetectedMode.from || "RDP") + '"></i>');
  }
  html.push('</div>');
  if (record.device_transfer_status) {
    html.push(
      '<div class="toggle-element collapsed mt-2">',
      '<span class="dvl-transfer-badge">' + this.escapeHtml(record.device_transfer_status) + '</span>',
      '</div>'
    );
    if (record.transfer_from || record.transfer_to) {
      html.push('<div class="dvl-transfer-route toggle-element collapsed">');
      if (record.transfer_from) html.push(this.escapeHtml(record.transfer_from));
      if (record.transfer_from && record.transfer_to) html.push(' <span class="mx-1">to</span> ');
      if (record.transfer_to) html.push(this.escapeHtml(record.transfer_to));
      html.push('</div>');
    }
    if (record.batch_code) {
      html.push('<div class="dvl-dev-ip toggle-element collapsed" style="font-size:11px;">' + this.escapeHtml(record.batch_code) + '</div>');
    }
  }
  return html.join("");
};


MyApp.prototype.renderDeviceInfoCell = function (record, type) {
  var serialNo = this.safeDisplayValue(record.serial, "");
  var deviceName = this.safeDisplayValue(record.name, "");
  var model = this.safeDisplayValue(record.mdl_name, "");
  var manufacturer = this.safeDisplayValue(record.manu_name, "");
  var category = this.safeDisplayValue(record.cat_name, "");
  var companyName = this.safeDisplayValue(record.cmp_name, "");
  var added_from = this.safeDisplayValue(record.added_from_text, "");
  if (type !== "display") return model;
  var getAddedFromIcon = function (source) {
    var sourceLower = source.toLowerCase();
    if (sourceLower.includes('azure')) {
      return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="flex-shrink:0;">' +
        '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="#0078D4"/>' +
        '<path d="M6.5 14.5L8.5 9.5L10.5 14.5H6.5Z" fill="white"/>' +
        '<path d="M10.5 14.5L12.5 9.5L14.5 14.5H10.5Z" fill="white"/>' +
        '<path d="M14.5 14.5L16.5 9.5L18.5 14.5H14.5Z" fill="white"/>' +
        '</svg>';
    } else if (sourceLower.includes('network') || sourceLower.includes('detected')) {
      return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#00BCD4;">' +
        '<rect x="2" y="2" width="20" height="20" rx="2.18"/>' +
        '<line x1="8" y1="2" x2="8" y2="22"/>' +
        '<line x1="16" y1="2" x2="16" y2="22"/>' +
        '<line x1="2" y1="8" x2="22" y2="8"/>' +
        '<line x1="2" y1="16" x2="22" y2="16"/>' +
        '</svg>';
    } else if (sourceLower.includes('manual') || sourceLower.includes('added')) {
      return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#4CAF50;">' +
        '<path d="M12 5v14M5 12h14"/>' +
        '</svg>';
    } else {
      return '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#999;">' +
        '<circle cx="12" cy="12" r="10"/>' +
        '<path d="M12 8v8M8 12h8"/>' +
        '</svg>';
    }
  };

  var addedFromIcon = added_from ? getAddedFromIcon(added_from) : '';
  return [
    '<div class="d-flex flex-column min-w-0">',
    serialNo ? '<span class="b5-text fw-medium" data-bs-toggle="tooltip" data-bs-title="Serial No">' + this.escapeHtml(serialNo) + "</span>" : "",
    deviceName ? '<span class="b5-text fw-medium" data-bs-toggle="tooltip" data-bs-title="Device Name">' + this.escapeHtml(deviceName) + "</span>" : "",
    model ? '<span class="b5-text" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Model">' + this.escapeHtml(model) + "</span>" : "",
    manufacturer ? '<span class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Manufacturer">' + this.escapeHtml(manufacturer) + "</span>" : "",
    category ? '<span class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Category">' + this.escapeHtml(category) + "</span>" : "",
    companyName ? '<span class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Company">' + this.escapeHtml(companyName) + "</span>" : "",
    added_from ? '<div class="d-flex align-items-center toggle-element collapsed" style="margin-top:2px;">' +
      addedFromIcon +
      '<span class="b5-text fw-medium" style="font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Added From">' + this.escapeHtml(added_from) + "</span>" +
      '</div>' : "",

    "</div>"
  ].join("");
};

MyApp.prototype.renderStatusCell = function (record, type) {
  var status = this.safeDisplayValue(record.lbl_name, "-");
  if (type !== "display") return status;
  if (status === "-") return '<span class="user-list-empty">-</span>';
  var statusClasses = {
    "ready to deploy": "dvl-badge-ready-deploy",
    "deployed": "dvl-badge-deployed",
    "ready on stock": "dvl-badge-ready-stock",
    "pending": "dvl-badge-pending",
    "out for repair": "dvl-badge-repair",
    "lost-stolen": "dvl-badge-lost",
    "repair-inhouse": "dvl-badge-house-repair",
    "scrap": "dvl-badge-scrap"
  };
  var badgeClass = statusClasses[status.toLowerCase()] || "dvl-badge-default";
  return '<span class="dvl-badge ' + badgeClass + '" style="cursor:pointer;" title="Click to ' + (status.toLowerCase() === "deployed" ? "Check In" : "Check Out") + '">' + this.escapeHtml(status) + "</span>";
};

MyApp.prototype.renderFlagsCell = function (record, type) {
  if (type !== "display") return "";
  var html = '<div class="d-flex flex-column gap-1">';
  if (record.audit_accept_status) {
    var cls = {
      accepted: "accept_status_accepted",
      pending: "accept_status_pending",
      declined: "accept_status_declined"
    }[record.audit_accept_status] || "";
    html += '<span class="' + this.escapeHtml(cls) + '">' + this.escapeHtml(record.audit_accept_status) + "</span>";
  }
  if (record.warranty_end_date) {
    html += '<div class="b5-text" style="color:#7F7F7F;font-size:11px;">' + this.escapeHtml(record.warranty_end_date) + "</div>";
  }
  if (record.warranty_status_text) {
    html += '<div class="b5-text toggle-element collapsed">' + this.escapeHtml(record.warranty_status_text) + "</div>";
  }
  html += "</div>";
  return html;
};


MyApp.prototype.renderCurrentlyInCell = function (record, type) {
  var assignedUser = this.safeDisplayValue(record.assigned_user_name, "");
  var assignedPlace = this.safeDisplayValue(record.loc_name, "");
  var stockPlace = this.safeDisplayValue(record.stock_place, "");

  if (type !== "display") return assignedUser || assignedPlace || stockPlace || "-";
  if (!assignedPlace && !stockPlace) return '<span class="user-list-empty">-</span>';
  var html = [];
  html.push('<div class="d-flex flex-column min-w-0">');
  if (assignedPlace) {
    html.push('<span class="b5-text fw-medium" style="color:#515151;" data-bs-toggle="tooltip" data-bs-title="Device Location">' + this.escapeHtml(assignedPlace) + "</span>");
  }
  if (stockPlace) {
    html.push('<span class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Stock Place">' + this.escapeHtml(stockPlace) + "</span>");
  }
  html.push('</div>');
  return html.join("");
};

MyApp.prototype.renderCurrentlyInCell1 = function (record, type) {
  var assignedUser = this.safeDisplayValue(record.checkout, "");
  var full_name = this.safeDisplayValue(record.full_name, "");
  var checkout_text = this.safeDisplayValue(record.checkout_text, "");
  var last_checkout_date = this.safeDisplayValue(record.last_checkout_date, "");
  var checkout_accept_status = this.safeDisplayValue(record.accepted, "");
  if (type !== "display") return assignedUser || "";
  if (!checkout_text) return '<span class="user-list-empty"></span>';
  var pendingHtml = "";
  if (checkout_accept_status === "pending") {
    var dateHtml = last_checkout_date
      ? '<span class="b5-text me-1 toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" title="Checkout Date">' + this.escapeHtml(last_checkout_date) + "</span>"
      : "";
    pendingHtml = [
      '<div class="d-flex align-items-center gap-1">',
      dateHtml,
      '<svg class="toggle-element collapsed" xmlns="http://www.w3.org/2000/svg" width="1.2em" height="1.2em" viewBox="0 0 24 24">',
      '<path d="M0 0h24v24H0z" fill="none"/>',
      '<path fill="#dd2216" d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12s4.48 10 10 10s10-4.48 10-10m-10 1H8v-2h4V8l4 4l-4 4z"/>',
      '</svg>',
      '<span class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;">Pending</span>',
      '</div>'
    ].join("");
  }

  if (checkout_text === "Checkout to User") {
    var imageUrl = this.safeDisplayValue(record.profile_img);
    var avatarHtml = this.getAvatarHtml(full_name, imageUrl, "user-list-avatar");
    var reminderIconHtml = checkout_accept_status === "pending"
      ? '<span class="dvl-user-email-icon send-reminder-icon" data-id="' + record.id + '" data-bs-toggle="tooltip" data-bs-title="Send Reminder" style="cursor:pointer;">' +
      this.getReminderIconHtml() +
      '</span>'
      : '';

    return [
      '<div class="dvl-user-wrap">',
      '<div class="dvl-user-row">',
      avatarHtml,
      full_name
        ? '<a href="' + this.config.url.user_info + '/' + record.assigned_to +
        '" class="dvl-user-name" target="_blank" data-bs-toggle="tooltip" data-bs-title="Assigned User">' +
        this.escapeHtml(full_name) +
        '</a>'
        : "",

      reminderIconHtml,
      '</div>',
      checkout_text
        ? '<div class="dvl-checkout-type">' +
        this.escapeHtml(checkout_text) +
        '</div>'
        : "",
      record.cmp_name
        ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;"data-bs-toggle="tooltip" data-bs-title="Company">' +
        '<i class=""></i>' +
        this.escapeHtml(record.cmp_name) +
        '</div>'
        : "",
      record.project_details
        ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Project">' +
        '<i class="fa fa-folder-o me-1"></i>' +
        this.escapeHtml(record.project_details) +
        '</div>'
        : "",
      pendingHtml,
      '</div>'
    ].join("");
  }

  if (checkout_text === "Checkout to Place") {
    var placeName = this.safeDisplayValue(record.loc_name, "");
    var placeImage = this.safeDisplayValue(record.location_img, "");
    var placeAvatar = placeImage
      ? '<img src="' + this.escapeHtml(placeImage) + '" class="user-list-avatar" alt="' + this.escapeHtml(placeName) + '">'
      : '<span class="user-list-avatar place-avatar">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">' +
      '<path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z"/>' +
      '</svg>' +
      '</span>';
    var placeDateHtml = last_checkout_date
      ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;margin-top:2px;"><i style="margin-right:4px;"></i>' + this.escapeHtml(last_checkout_date) + '</div>'
      : "";
    return [
      '<div class="dvl-user-wrap">',
      '<div class="dvl-user-row">',
      placeAvatar,
      '<a href="#" class="dvl-user-name" data-bs-toggle="tooltip" data-bs-title="Assigned Place">' +
      this.escapeHtml(placeName) +
      '</a>',
      '</div>',
      '<div class="dvl-checkout-type">' +
      this.escapeHtml(checkout_text) +
      '</div>',
      record.place_loc
        ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Location">' +
        '<i class=""></i>' +
        this.escapeHtml(record.place_loc) +
        '</div>'
        : "",
      record.project_details
        ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Project">' +
        '<i class=""></i>' +
        this.escapeHtml(record.project_details) +
        '</div>'
        : "",

      pendingHtml,
      last_checkout_date
        ? '<div class="b5-text toggle-element collapsed" style="color:#7F7F7F;font-size:11px;" data-bs-toggle="tooltip" data-bs-title="Checkout Date">' +
        '<i class=""></i>' +
        this.escapeHtml(last_checkout_date) +
        '</div>'
        : "",

      '</div>'
    ].join("");
  }
  return '<span class="user-list-empty"></span>';
};

MyApp.prototype.getReminderIconHtml = function () {
  return '<svg width="17" height="14" viewBox="0 0 17 14" fill="none" xmlns="http://www.w3.org/2000/svg">' +
    '<path d="M1.66667 13.3333C1.20833 13.3333 0.816111 13.1703 0.49 12.8442C0.163889 12.5181 0.000555555 12.1256 0 11.6667V1.66667C0 1.20833 0.163333 0.816111 0.49 0.49C0.816667 0.163889 1.20889 0.000555556 1.66667 0H15C15.4583 0 15.8508 0.163333 16.1775 0.49C16.5042 0.816666 16.6672 1.20889 16.6667 1.66667V11.6667C16.6667 12.125 16.5036 12.5175 16.1775 12.8442C15.8514 13.1708 15.4589 13.3339 15 13.3333H1.66667ZM8.33333 7.35417C8.40278 7.35417 8.47583 7.34361 8.5525 7.3225C8.62916 7.30139 8.70194 7.27028 8.77083 7.22917L14.6667 3.54167C14.7778 3.47222 14.8611 3.38556 14.9167 3.28167C14.9722 3.17778 15 3.06306 15 2.9375C15 2.65972 14.8819 2.45139 14.6458 2.3125C14.4097 2.17361 14.1667 2.18056 13.9167 2.33333L8.33333 5.83333L2.75 2.33333C2.5 2.18056 2.25694 2.17722 2.02083 2.32333C1.78472 2.46944 1.66667 2.67417 1.66667 2.9375C1.66667 3.07639 1.69444 3.19806 1.75 3.3025C1.80556 3.40694 1.88889 3.48667 2 3.54167L7.89583 7.22917C7.96528 7.27083 8.03833 7.30222 8.115 7.32333C8.19167 7.34444 8.26444 7.35472 8.33333 7.35417Z" fill="black"/>' +
    '</svg>';
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
  name = $.trim(name || "");
  if (!name) return "U";
  var parts = name.split(/\s+/);
  if (parts.length === 1) return parts[0].charAt(0).toUpperCase();
  return (parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase();
};

MyApp.prototype.renderUpdatedOnCell = function (record, type) {
  var updatedOn = this.safeDisplayValue(record.last_updated_at || record.created_at, "-");
  if (type !== "display") return updatedOn;
  if (updatedOn === "-") return '<span class="user-list-empty">-</span>';
  var parts = this.splitUpdatedOn(updatedOn);
  return [
    '<div class="d-flex flex-column min-w-0">',
    '<span class="b5-text" style="color:#515151;">' + this.escapeHtml(parts.date) + "</span>",
    parts.time ? '<span class="b5-text toggle-element collapsed" style="color:#515151;">' + this.escapeHtml(parts.time) + "</span>" : "",
    "</div>"
  ].join("");
};


MyApp.prototype.getRowActionState = function (record) {
  var t = this;
  var isDeleted = !!t.showDeletedDevices;
  // Check permissions
  var hasCheckoutPermission = jQuery.inArray("DeviceCheckoutCheckin", t.config.permissions || []) !== -1;
  var hasResalePermission = jQuery.inArray("DeviceResale", t.config.permissions || []) !== -1;
  var hasPrintPermission = jQuery.inArray("DevicePrintLabel", t.config.permissions || []) !== -1;
  var hasNetworkViewPermission = jQuery.inArray("NIView", t.config.permissions || []) !== -1;
  var hasRdpAccessPermission = jQuery.inArray("RdpAccess", t.config.permissions || []) !== -1;
  var isInTransfer = record.device_transfer_status && record.device_transfer_status !== "";
  var hasGatepass = record.gatepass_id && record.gatepass_id !== null;
  var checkAction = parseInt(record.check_action) || 0;
  var isSold = record.sold == 1;
  var canDispose = record.dispose_allowed == 1;

  // console.log(record);
  var hasNetwork = record.itm_id != null && record.is_dupe == null && record.is_virtual == null;
  var isNetworkAd = record.itm_is_AD == 1;
  var hasAzure = record.itm_azure_id != null && record.is_dupe == null && record.is_virtual == null;
  var hasRdp = record.rdp_status == 1 && record.node_id != null;

  if (isDeleted) {
    return {
      canRestore: true,
      canEdit: false,
      canDelete: false,
      canClone: false,
      canView: false,
      canCheckout: false,
      canCheckin: false,
      canResale: false,
      canPrint: false,
      canNetwork: false,
      isNetworkAd: false,
      canAzure: false,
      canRdp: false,
      canTransfer: false,
      canRestoreSold: false
    };
  }

  return {
    canEdit: true,
    canDelete: true,
    canClone: true,
    canView: true,
    canCheckout: hasCheckoutPermission && !isInTransfer && !hasGatepass && checkAction === 1,
    canCheckin: hasCheckoutPermission && !isInTransfer && !hasGatepass && checkAction === 2,
    canResale: hasResalePermission && !isSold && canDispose && !isInTransfer && !hasGatepass,
    canPrint: hasPrintPermission,
    canNetwork: hasNetworkViewPermission && hasNetwork,
    isNetworkAd: isNetworkAd,
    canAzure: hasNetworkViewPermission && hasAzure,
    canRdp: hasRdpAccessPermission && hasRdp,
    canTransfer: false, // Set to true if you want to enable transfer
    canRestoreSold: t.config.cp == 1 && isSold,
    canRestore: false
  };
};

MyApp.prototype.buildRowActions = function (record) {
  var t = this;
  var tr = t.config.translations || {};
  var state = t.getRowActionState(record);
  var actions = [];
  if (state.canRestore) {
    actions.push(t.actionItemHtml(tr.Restore_Device || "Restore", "dtActRestore", record, "restore"));
    return actions;
  }
  if (state.canClone) {
    actions.push(t.actionItemHtml(tr.Clone_Device || "Clone", "dtActClone", record, "clone"));
  }
  if (state.canView) {
    actions.push(t.actionItemHtml(tr.View_Details || "View Details", "dtActView", record, "view"));
  }
  if (state.canCheckout) {
    actions.push(t.actionItemHtml(tr.check_out || "Check Out", "dtActCheckout", record, "checkout"));
  }
  if (state.canCheckin) {
    actions.push(t.actionItemHtml(tr.check_in || "Check In", "dtActCheckin", record, "checkin"));
  }
  if (state.canResale) {
    actions.push(t.actionItemHtml(tr.dispose || "Dispose", "dtActResale", record, "resale"));
  }
  if (state.canPrint) {
    actions.push(t.actionItemHtml(tr.add_for_label_print || "Print Label/Transfer", "dtActPrintLabel", record, "print"));
  }
  if (state.canRestoreSold) {
    actions.push(t.actionItemHtml(tr.restore_dispose_divice || "Restore Sold", "dtActSoldRestore", record, "restoreSold"));
  }
  return actions;
};



MyApp.prototype.renderActionsCell = function (record, type) {
  if (type !== "display") return "";
  var t = this;
  var tr = t.config.translations || {};
  var state = t.getRowActionState(record);
  var quickActions = [];
  var menuActions = [];
  if (state.canRestore) {
    quickActions.push(t.quickActionButtonHtml(tr.Restore_Device || "Restore", "dtActRestore", record, "restore"));
    menuActions = [];
  } else {
    if (state.canEdit) {
      quickActions.push(t.quickActionButtonHtml(tr.Edit_Device || "Edit Device", "dtActEdit", record, "edit"));
    }
    if (state.canDelete) {
      quickActions.push(t.quickActionButtonHtml(tr.Delete_Device || "Delete Device", "dtActDel", record, "delete", "is-delete"));
    }
    menuActions = t.buildRowActions(record);
    // console.log(state);
    if (state.canNetwork && !state.isNetworkAd) {
      menuActions.push(
        t.actionItemHtml("Network Info", "dtActNetwork", record, "network")
      );
    }
    if (state.canNetwork && state.isNetworkAd) {
      menuActions.push(
        t.actionItemHtml("Network AD Info", "dtActNetworkAd", record, "networkAd")
      );
    }
    if (state.canAzure) {
      menuActions.push(
        t.actionItemHtml("Azure Info", "dtActAzure", record, "azure")
      );
    }
    if (state.canRdp) {
      menuActions.push(
        t.actionItemHtml("RDP Access", "dtActRdp", record, "rdp")
      );
    }
  }

  if (!quickActions.length && !menuActions.length) {
    return '<span class="user-list-empty">-</span>';
  }

  var dropdownId = "device-action-dropdown-" + record.id;
  return [
    '<div class="device-list-actions user-list-actions">',
    quickActions.join(""),
    menuActions.length && !state.canRestore
      ? [
        '<div class="">',
        '<button type="button" class="action-link device-list-menu-toggle user-list-menu-toggle"',
        ' id="' + dropdownId + '" aria-expanded="false" aria-haspopup="true" aria-label="More actions">',
        t.getIcon("ellipsis"),
        "</button>",
        '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up"',
        ' aria-labelledby="' + dropdownId + '">',
        menuActions.join(""),
        "</ul>",
        "</div>",
      ].join("")
      : "",
    "</div>"
  ].join("");
};

MyApp.prototype.quickActionButtonHtml = function (label, cls, record, iconKey, extraClass) {
  var t = this;
  var dataAttrs = '';
  if (record.id) dataAttrs += ' data-id="' + t.escapeHtml(record.id) + '"';
  if (record.asset_tag) dataAttrs += ' data-asset_tag="' + t.escapeHtml(record.asset_tag) + '"';
  if (record.cmp_name) dataAttrs += ' data-cmp_name="' + t.escapeHtml(record.cmp_name) + '"';
  if (record.name) dataAttrs += ' data-name="' + t.escapeHtml(record.name) + '"';
  if (record.company_id) dataAttrs += ' data-company_id="' + t.escapeHtml(record.company_id) + '"';
  if (record.location_id) dataAttrs += ' data-device_loc_id="' + t.escapeHtml(record.location_id) + '"';
  if (record.last_checkout_date) dataAttrs += ' data-checkout_date="' + t.escapeHtml(record.last_checkout_date) + '"';
  return [
    '<button type="button" class="user-list-action-btn ',
    t.escapeHtml(extraClass || ""),
    " ",
    cls,
    '"',
    dataAttrs,
    ' title="',
    t.escapeHtml(label),
    '" aria-label="',
    t.escapeHtml(label),
    '">',
    t.getIcon(iconKey),
    "</button>"
  ].join("");
};

MyApp.prototype.actionItemHtml = function (label, cls, record, iconKey) {
  var t = this;
  var dataAttrs = '';
  if (record.id) dataAttrs += ' data-id="' + t.escapeHtml(record.id) + '"';
  if (record.asset_tag) dataAttrs += ' data-asset_tag="' + t.escapeHtml(record.asset_tag) + '"';
  if (record.cmp_name) dataAttrs += ' data-cmp_name="' + t.escapeHtml(record.cmp_name) + '"';
  if (record.name) dataAttrs += ' data-name="' + t.escapeHtml(record.name) + '"';
  if (record.company_id) dataAttrs += ' data-company_id="' + t.escapeHtml(record.company_id) + '"';
  if (record.location_id) dataAttrs += ' data-device_loc_id="' + t.escapeHtml(record.location_id) + '"';
  if (record.last_checkout_date) dataAttrs += ' data-checkout_date="' + t.escapeHtml(record.last_checkout_date) + '"';
  return [
    "<li>",
    '<a class="dropdown-item ',
    cls,
    '" href="#"',
    dataAttrs,
    '>',
    t.getIcon(iconKey),
    '<span class="b3-text text-off-white">',
    label,
    "</span>",
    "</a>",
    "</li>"
  ].join("");
};

MyApp.prototype.getIcon = function (key) {
  return this.icons && this.icons[key] ? this.icons[key]() : "";
};

MyApp.prototype.icons = {
  ellipsis: function () {
    return '<svg width="5" height="14" viewBox="0 0 5 20" fill="none"><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>';
  },
  edit: function () {
    return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
  },
  delete: function () {
    return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
  },
  clone: function () {
    return '<svg viewBox="0 0 16 16" fill="none"><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"/></svg>';
  },
  view: function () {
    return '<svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg>';
  },
  restore: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M6.678 20.567C2.532 18.021.759 12.758 2.718 8.144 4.876 3.06 10.746.688 15.83 2.846c5.084 2.158 7.456 8.029 5.298 13.112-.843 1.987-2.253 3.56-3.961 4.609" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M17 16v4.4c0 .331.269.6.6.6H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  },
  checkout: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M14 8L18 12M18 12L14 16M18 12H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 4H5C3.895 4 3 4.895 3 6V18C3 19.105 3.895 20 5 20H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
  },
  checkin: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M10 8L6 12M6 12L10 16M6 12H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 4H19C20.105 4 21 4.895 21 6V18C21 19.105 20.105 20 19 20H18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
  },
  resale: function () {
    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="100%" height="100%">
              <path d="M21 3L3 10.5L11.5 13L14 21.5L21 3Z" fill="currentColor" stroke="currentColor" stroke-width="1" stroke-linejoin="round"></path>
              </svg>`;
  },

  print: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M6 9V3H18V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="4" y="9" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 15H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M8 12H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
  },
  network: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="2.18"/><line x1="8" y1="2" x2="8" y2="22"/><line x1="16" y1="2" x2="16" y2="22"/><line x1="2" y1="8" x2="22" y2="8"/><line x1="2" y1="16" x2="22" y2="16"/></svg>';
  },
  azure: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="#0078D4"/><path d="M6.5 14.5L8.5 9.5L10.5 14.5H6.5Z" fill="white"/><path d="M10.5 14.5L12.5 9.5L14.5 14.5H10.5Z" fill="white"/><path d="M14.5 14.5L16.5 9.5L18.5 14.5H14.5Z" fill="white"/></svg>';
  },
  rdp: function () {
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M8 10H16M8 14H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M2 8H22" stroke="currentColor" stroke-width="1.5"/></svg>';
  }
};


MyApp.prototype.reload = function () {
  this.expandedRows = {};
  this.rowDetailCache = {};
  this.currentPage = 1;
  this.loadDevices();
};

MyApp.prototype.initTooltips = function (ctx) {
  ctx.find('[data-bs-toggle="tooltip"]').each(function () {
    var existing = bootstrap.Tooltip.getInstance(this);
    if (existing) existing.dispose();
  });
  ctx.find('[data-bs-toggle="tooltip"]').each(function () {
    new bootstrap.Tooltip(this);
  });
};

MyApp.prototype.hideTooltip = function (selector) {
  var btn = document.querySelector(selector);
  if (!btn) return;
  var tooltip = bootstrap.Tooltip.getInstance(btn);
  $(btn).blur();
  if (tooltip) tooltip.hide();
  $(".tooltip").remove();
};

MyApp.prototype.scheduleTableLayoutSync = function () { };

MyApp.prototype.confirmAndPost = function (confirmText, url, payload, options) {
  var t = this;
  var tr = (t.config && t.config.translations) || {};
  options = options || {};
  payload = payload || {};
  payload._token = t.getCsrfToken();

  var doPost = function () {
    $.ajax({
      url: url,
      type: "POST",
      data: payload,
      headers: { "X-CSRF-TOKEN": payload._token }
    })
      .done(function (res) {
        var ok = res && (String(res.status) === "success" || res.status === true);
        var msg = res && res.msg ? res.msg : ok ? options.successMessage || "Success" : options.errorMessage || tr.something_went_wrong || "Something went wrong.";
        t.showToast(ok ? "success" : "error", msg);
        if (ok) t.loadDevices();
      })
      .fail(function () {
        t.showToast("error", options.errorMessage || tr.something_went_wrong || "Something went wrong.");
      });
  };

  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: "warning",
      title: tr.confirm_title || "Confirm",
      text: confirmText,
      showCancelButton: true,
      confirmButtonText: tr.confirm_yes || "Yes",
      cancelButtonText: tr.cancel || "Cancel"
    }).then(function (result) {
      if (result.isConfirmed) doPost();
    });
  } else {
    if (confirm(confirmText)) doPost();
  }
};

MyApp.prototype.getCsrfToken = function () {
  if (this.config && this.config.token) return this.config.token;
  return $('meta[name="csrf-token"]').attr("content") || "";
};


MyApp.prototype.showToast = function (icon, message) {
  var tr = (this.config && this.config.translations) || {};
  if (window.Swal && typeof Swal.fire === "function") {
    Swal.fire({
      icon: icon,
      title: icon === "success" ? tr.success_title || "Success" : icon === "error" ? tr.error_title || "Error" : "Info",
      text: message,
      showConfirmButton: true,
      allowOutsideClick: false
    });
  } else if (window.toastr) {
    toastr[icon](message);
  } else {
    alert(message);
  }
};



MyApp.prototype.safeDisplayValue = function (value, fallback) {
  return this.isFilledValue(value) ? String(value).trim() : fallback !== undefined ? fallback : "-";
};

MyApp.prototype.isFilledValue = function (value) {
  if (value === null || value === undefined) return false;
  var text = String(value).trim();
  return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
};

MyApp.prototype.escapeHtml = function (value) {
  return String(value === null || value === undefined ? "" : value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
};

MyApp.prototype.splitUpdatedOn = function (value) {
  var text = this.safeDisplayValue(value, "");
  var matched = text.match(/^(.*?)(\d{1,2}:\d{2}\s*[AP]M)$/i);
  if (!matched) return { date: text || "-", time: "" };
  return { date: $.trim(matched[1]) || text, time: $.trim(matched[2]) };
};

MyApp.prototype.convertDateFormat = function (dateString) {
  if (!dateString) return '';
  return dateString.replace(/-/g, '/');
};

MyApp.prototype.resetDeviceForm = function () {
  var t = this;
  t.mdl.device.btnSubmit.prop("disabled", false);
  t.httpCall = true;
  t.mdl.device.frm.get(0).reset();
  t.mdl.device.frm.trigger("reset");
  t.frmValidator.resetForm();
  t.mdl.device.frmEl.location.empty().trigger("change");
  t.mdl.device.frmEl.supplier.empty().trigger("change");
  t.mdl.device.frmEl.asset_owner.empty().trigger("change");
  t.mdl.device.frmEl.invoice_id.empty().trigger("change");
  t.mdl.device.frmEl.device_occure_type.val("0").trigger("change");
  t.mdl.device.frmEl.asset_type_id.empty().trigger("change");
  t.mdl.device.frmEl.lease_id.empty().trigger("change");
  t.mdl.device.frmEl.amc_supplier_id.empty().trigger("change");
  t.mdl.device.frmEl.assigned_to.empty().trigger("change");
  t.mdl.device.frmEl.checkout_reason.empty().trigger("change");
  t.mdl.device.frmEl.department_id.empty().trigger("change");
  t.mdl.device.frm.find("#scanner_id").val("");
  t.mdl.device.frmEl.deviceRfid.empty().trigger("change");
  t.mdl.device.frmEl.inAntenna.val([]).trigger("change");
  t.mdl.device.frmEl.outAntenna.val([]).trigger("change");
  // t.mdl.device.frmEl.expected_checkin_div.addClass('hide');
  t.mdl.device.expected_checkin_div.addClass('hide');
  t.mdl.device.frmEl.amc_expire_date.datepicker('setStartDate', null);
  t.mdl.device.frmEl.warrenty_end_date.datepicker('setStartDate', null);
  // t.mdl.device.frmEl.project_name_cover.addClass('hide');
  // t.mdl.device.frmEl.allocation_type_cover.addClass('hide');
  // t.mdl.device.frmEl.reasoncover.addClass('hide');
  // t.mdl.optionsCurrency();
  MyApp.prototype.mdl.optionsCurrency.call(t);
  // t.mdl.optionsAssetType();
  MyApp.prototype.mdl.optionsAssetType.call(t);
  // t.mdl.optionsCompany();
  MyApp.prototype.mdl.optionsCompany.call(t);
  // t.mdl.optionsStatusLable();
  MyApp.prototype.mdl.optionsStatusLable.call(t);
  MyApp.prototype.mdl.optionsModel.call(t);
  // MyApp.prototype.mdl.optionsDepartment.call(t); 
  // MyApp.prototype.mdl.optionsLocation.call(t);  
  t.getInternalPlace();
  // t.mdl.leaseDevice();
  MyApp.prototype.mdl.leaseDevice.call(t);
  // t.mdl.stockPlace();
  t.loadImageViewer("");
  t.mdl.device.frmEl.forAction.val("");
  t.mdl.device.frmEl.clone_img.val("");
  t.mdl.device.frm.find("#id").val("");
  t.clearCustomFields();
};

MyApp.prototype.resetCheckoutForm = function () {
  var t = this;
  t.httpCall = true;
  t.mdl.checkout.frm.trigger("reset");
  t.chkoutFrmValidator.resetForm();
  t.mdl.checkout.frmEl.assigned_to.empty().trigger("change");
  t.mdl.checkout.frmEl.assigned_for.val("").trigger("change");
  t.mdl.checkout.frmEl.assigned_place.empty().trigger("change");
  t.mdl.checkout.frmEl.last_checkout_project.empty().trigger("change");
  t.mdl.checkout.frmEl.checkout_reason.empty().trigger("change");
  t.mdl.checkout.frmEl.allocation_status.empty().trigger("change");
  t.mdl.checkout.frmEl.rate.empty().trigger("change");
  t.mdl.checkout.lblDeviceTag.text("");
  t.mdl.checkout.frmEl.note.val("Device Checkout");
};

MyApp.prototype.resetCheckinForm = function () {
  var t = this;
  t.httpCall = true;
  t.mdl.checkin.frm.trigger("reset");
  t.chkinFrmValidator.resetForm();
  t.mdl.checkin.frmEl.status_id.empty();
  if (t.config.statusLabels) {
    $.each(t.config.statusLabels, function (i, v) {
      if (t.config.deployedLabel != v.id) {
        t.mdl.checkin.frmEl.status_id.append(new Option(v.text, v.id));
      }
    });
  }
  t.mdl.checkin.frmEl.status_id.val(t.config.firstDeployable).trigger("change");
  t.mdl.checkin.lblDeviceTag.text("");
  t.mdl.checkin.lblDeviceName.text("");
  t.mdl.checkin.frmEl.note.val("Device Checkin");
};

MyApp.prototype.resetSupplierForm = function () {
  var t = this;
  t.mdl.supplier.frm.trigger("reset");
  t.mdl.supplier.frmEl.supplier_name.empty();
  t.mdl.supplier.frmEl.suppler_email.empty();
};

MyApp.prototype.mdl = {
  stockPlace: function () {
    var t = this;
    if (t.mdl.device.frmEl.statusLabel.val() == "7" || t.mdl.device.frmEl.statusLabel.val() == t.config.deployedLabel) {
      t.mdl.device.frmEl.stock_place.val(null).trigger("change");
      t.mdl.device.stockcover.addClass("hide");
    } else {
      t.mdl.device.stockcover.removeClass("hide");
    }
  },
  leaseDevice: function () {
    var t = this;
    if (t.mdl.device.frmEl.device_occure_type.val() == "0") {
      t.mdl.device.purchasecover.removeClass("hide");
      t.mdl.device.leasecover.addClass("hide");
      t.mdl.device.frmEl.lease_id.empty().trigger("change");
    } else if (t.mdl.device.frmEl.device_occure_type.val() == "1") {
      t.mdl.device.purchasecover.addClass("hide");
      t.mdl.device.leasecover.removeClass("hide");
      t.mdl.device.frmEl.lease_id.empty().trigger("change");
    }
  },
  optionsCurrency: function () {
    var t = this;
    t.mdl.device.frmEl.purchase_currency.empty().append(new Option("Select Currency", ""));
    $.each(t.config.currencies, function (i, v) {
      var opt = t.config.default_currency_format == i ? new Option("", i, true, true) : new Option("", i);
      opt.innerHTML = v.name + " (" + v.symbol_html + ")";
      t.mdl.device.frmEl.purchase_currency.append(opt);
    });
    t.mdl.device.frmEl.purchase_currency.trigger("change");
  },
  optionsAssetType: function () {
    var t = this;
    t.mdl.device.frmEl.asset_type_id.empty().append(new Option("Asset Type", "", true, true));
    $.each(t.config.assetType, function (i, v) {
      t.mdl.device.frmEl.asset_type_id.append(new Option(v.text, v.id));
    });
    t.mdl.device.frmEl.asset_type_id.val("").trigger("change");
  },
  optionsCompany: function () {
    var t = this;
    t.mdl.device.frmEl.company.empty().append(new Option(t.config.translations.select_company, "", true, true));
    $.each(t.config.companies, function (i, v) {
      t.mdl.device.frmEl.company.append(new Option(v.text, v.id));
    });
    t.mdl.device.frmEl.company.val("").trigger("change");
  },
  optionsStatusLable: function () {
    var t = this;
    t.mdl.device.frmEl.statusLabel.empty().append(new Option(t.config.translations.select_status, "", true, true));
    $.each(t.config.statusLabels, function (i, v) {
      t.mdl.device.frmEl.statusLabel.append(new Option(v.text, v.id));
    });
    t.mdl.device.frmEl.statusLabel.trigger("change");
  },
  optionsModel: function () {
    var t = this;
    var $modelSelect = t.mdl.device.frmEl.model;

    if (!$modelSelect || !$modelSelect.length) {
      return;
    }
    $modelSelect.empty().append(new Option(t.config.translations.select_the_model, "", true, true));

    if (t.config.models && Array.isArray(t.config.models)) {
      $.each(t.config.models, function (i, v) {
        $modelSelect.append(new Option(v.text, v.id));
      });
    }

    $modelSelect.val("").trigger("change");
  },
  optionsLocation: function () {
    var t = this;
    var $locationSelect = t.mdl.device.frmEl.location;

    if (!$locationSelect || !$locationSelect.length) {
      console.log('Location select not found');
      return;
    }

    $locationSelect.empty().append(new Option("Select Location", "", true, true));

    if (t.config.locations && Array.isArray(t.config.locations)) {
      $.each(t.config.locations, function (i, v) {
        var id = v.id || v.value || i;
        var text = v.text || v.name || v.label || v;
        $locationSelect.append(new Option(text, id));
      });
    } else {
      console.warn('No locations data found or not an array');
    }
    $locationSelect.val("").trigger("change");
    if ($locationSelect.data('select2')) {
      $locationSelect.trigger('change');
    }
  }
};

MyApp.prototype.statusChanged = function () {
  var t = this;
  var status = t.mdl.device.frmEl.statusLabel.val();
  t.mdl.device.checkoutcover.addClass("hide");
  t.mdl.device.usercover.addClass("hide");
  t.mdl.device.placecover.addClass("hide");
  t.mdl.device.reasoncover.addClass("hide");
  t.mdl.device.expected_checkin_div.addClass("hide");
  t.mdl.device.project_name_cover.addClass("hide");
  t.mdl.device.allocation_type_cover.addClass("hide");
  t.mdl.device.allocationcover.addClass("hide");
  if (status == t.config.deployedLabel) {
    t.mdl.device.checkoutcover.removeClass("hide");
    t.mdl.device.allocationcover.removeClass("hide");
    t.mdl.device.reasoncover.removeClass("hide");
    t.mdl.device.expected_checkin_div.removeClass("hide");
    t.mdl.device.project_name_cover.removeClass("hide");
    t.mdl.device.allocation_type_cover.removeClass("hide");
    var tmp = t.mdl.device.frmEl.assigned_for.find("option:first").val();
    t.mdl.device.frmEl.assigned_for.val(tmp).trigger("change");
  } else {
    t.mdl.device.frmEl.assigned_to.empty().trigger("change");
    t.mdl.device.frmEl.assigned_place.empty().trigger("change");
    t.mdl.device.frmEl.checkout_reason.val("").trigger("change");
  }
};

MyApp.prototype.chkout = {
  switchCheckTarget: function () {
    var t = this;
    if (t.mdl.checkout.frmEl.assigned_for.val() == "2") {
      t.mdl.checkout.frmEl.assigned_place.closest(".cover").show();
      t.mdl.checkout.frmEl.assigned_to.closest(".cover").hide();
    } else {
      t.mdl.checkout.frmEl.assigned_place.closest(".cover").hide();
      t.mdl.checkout.frmEl.assigned_to.closest(".cover").show();
    }
  }
};

MyApp.prototype.initialCheckoutSelectedFor = function () {
  var t = this;
  var status_id = t.mdl.device.frmEl.statusLabel.val();
  var checkout_for = t.mdl.device.frmEl.assigned_for.val();
  t.mdl.device.usercover.addClass("hide");
  t.mdl.device.placecover.addClass("hide");
  t.mdl.device.frmEl.assigned_to.rules("remove");
  t.mdl.device.frmEl.assigned_place.rules("remove");
  t.mdl.device.frmEl.assigned_to.empty();
  t.mdl.device.frmEl.assigned_place.empty();

  if (checkout_for == "2" && status_id == t.config.deployedLabel && t.mdl.device.forAction != "edit") {
    t.initialCheckoutPlaceOpts();
    t.mdl.device.frmEl.assigned_place.rules("add", { required: true });
    t.mdl.device.placecover.removeClass("hide");
  } else if (checkout_for == "1" && status_id == t.config.deployedLabel && t.mdl.device.forAction != "edit") {
    t.mdl.device.frmEl.assigned_to.rules("add", { required: true });
    t.mdl.device.usercover.removeClass("hide");
  }
  t.mdl.device.frmEl.assigned_to.trigger("change");
  t.mdl.device.frmEl.assigned_place.trigger("change");
};

MyApp.prototype.initialCheckoutPlaceOpts = function () {
  var t = this;
  var select2Opts = { width: "100%" };
  t.mdl.device.frmEl.assigned_place.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.device.frmEl.assigned_place.parent(),
    ajax: {
      url: t.config.ajaxGetInternalPlace,
      dataType: "json",
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: function () { return t.mdl.device.frmEl.company.val(); }
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: "Select Place"
  }));
};

MyApp.prototype.updateAmcStartDate = function () {
  var t = this;
  var purchaseDate = t.mdl.device.frmEl.purchase_date.datepicker('getDate');
  var warrantyDate = t.mdl.device.frmEl.warranty_start_date.datepicker('getDate');
  var minDate = null;
  if (purchaseDate && warrantyDate) {
    minDate = purchaseDate > warrantyDate ? purchaseDate : warrantyDate;
  } else if (purchaseDate) {
    minDate = purchaseDate;
  } else if (warrantyDate) {
    minDate = warrantyDate;
  }

  if (minDate) {
    t.mdl.device.frmEl.amc_expire_date.datepicker('setStartDate', minDate);
  } else {
    t.mdl.device.frmEl.amc_expire_date.datepicker('setStartDate', null);
  }
};

MyApp.prototype.getInternalPlace = function () {
  var t = this;
  t.mdl.device.frmEl.internal_place.empty().append(new Option("Select Internal Place", "", true, true));
  t.mdl.device.frmEl.internal_place.trigger("change");
  var location_id = t.mdl.device.frmEl.location.val();
  if (location_id > 0) {
    t.mdl.device.frmEl.internal_place.select2({
      dropdownParent: t.mdl.device.frmEl.internal_place.parent(),
      ajax: {
        url: t.config.getInternalPlaceByAjax + '/' + location_id,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1
          };
        },
        delay: 300
      },
      width: "100%",
      placeholder: t.config.translations.select_internal_place,
      allowClear: true,
    });
  } else {
    t.mdl.device.frmEl.internal_place.select2({
      dropdownParent: t.mdl.device.frmEl.internal_place.parent(),
      width: "100%",
      placeholder: "Please select the location to fetch data",
      allowClear: false,
      language: {
        noResults: function () {
          return "Please select a location first";
        }
      }
    });
  }
};

MyApp.prototype.getTransferInternalPlace = function () {
  var t = this;
  t.mdl.transfer.frmEl.internal_place.empty().append(new Option("Select Internal Place", "", true, true));
  t.mdl.transfer.frmEl.internal_place.trigger("change");
  var location_id = t.mdl.transfer.frmEl.transfer_to.val();
  if (location_id > 0) {
    $.get(t.config.getInternalPlaceByAjax + '/' + location_id).done(function (data) {
      if (typeof data == "object" && data.results.length) {
        $.each(data.results, function (i, v) {
          t.mdl.transfer.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
        });
      }
    }).always(function () {
      t.mdl.transfer.frmEl.internal_place.trigger("change");
    });
    t.mdl.transfer.frmEl.internal_place.select2({
      dropdownParent: t.mdl.transfer.frmEl.internal_place.parent(),
      width: "100%",
      placeholder: "Select Internal Place",
      allowClear: true
    });
  }
};

MyApp.prototype.getTransferResponsibleUser = function () {
  var t = this;
  t.mdl.transfer.frmEl.responsible_user.empty().append(new Option("Select User", "", true, true));
  t.mdl.transfer.frmEl.responsible_user.trigger("change");
  var location_id = t.mdl.transfer.frmEl.transfer_to.val();
  if (location_id > 0) {
    t.mdl.transfer.frmEl.responsible_user.select2({
      dropdownParent: t.mdl.transfer.frmEl.responsible_user.parent(),
      width: "100%",
      ajax: {
        url: t.config.getUserByLocation + '/' + location_id,
        dataType: "json",
        data: function (p) {
          return { search: p.term, page: p.page || 1 };
        },
        delay: 300
      },
      allowClear: true,
      placeholder: "Select User"
    });
  }
};

MyApp.prototype.getSelectedTransferDevices = function () {
  var t = this;
  var selected = [];
  var selectedCompanyIds = [];
  t.tableBody.find(".row-checkbox:checked").each(function () {
    var id = $(this).val();
    if (id) {
      selected.push(id);
      var $row = $(this).closest('tr');
      var companyId = $row.find('.dtActEdit').attr('data-company_id') ||
        $row.find('[data-company_id]').attr('data-company_id');
      if (companyId) {
        selectedCompanyIds.push(companyId);
      }
    }
  });

  if (selected.length === 0) {
    if (t.config.print_device_array && t.config.print_device_array.length > 0) {
      $.each(t.config.print_device_array, function (i, v) {
        if (v && v.id) {
          selected.push(v.id);
          if (v.company_id) {
            selectedCompanyIds.push(v.company_id);
          }
        }
      });
    }
  }
  if (selected.length === 0) {
    if (t.mergeObj && t.mergeObj.merge_items &&
      t.mergeObj.merge_items.others &&
      t.mergeObj.merge_items.others.length > 0) {
      $.each(t.mergeObj.merge_items.others, function (i, v) {
        if (v && v.id) {
          selected.push(v.id);
          if (v.company_id) {
            selectedCompanyIds.push(v.company_id);
          }
        }
      });
    }
  }

  return { ids: selected, companyIds: selectedCompanyIds };
};

MyApp.prototype.showTransferModal = function (e) {
  if (e) e.preventDefault();
  var t = this;
  var result = t.getSelectedTransferDevices();
  var selected = result.ids;
  var selectedCompanyIds = result.companyIds;

  if (selected.length === 0) {
    t.showToast('error', t.config.translations.Please_select_atleast_one_device || "Please select at least one device");
    return;
  }
  var uniqueCompanies = [...new Set(selectedCompanyIds)];
  if (uniqueCompanies.length !== 1) {
    t.showToast('error', "Please select devices from the same company");
    return;
  }

  t.selected_company_id = uniqueCompanies[0];
  t.selectedTransferIds = selected;

  t.mdl.transfer.title.html(t.config.translations.bulk_transfer_device || "Bulk Transfer Devices");
  $(t.mdl.transfer.frmEl.device_count).html(selected.length);
  t.transferHttpPostPath = t.config.url.transfer_device || "/devices/transfer";

  t.resetTransferForm();
  t.mdl.transfer.modal("show");
  t.initTransferSelect2();
  setTimeout(function () {
    t.loadTransferInternalPlaces();
    t.loadTransferResponsibleUsers();
  }, 100);
};

MyApp.prototype.resetTransferForm = function () {
  var t = this;
  t.mdl.transfer.frmEl.transfer_to.val(null).trigger("change");
  t.mdl.transfer.frmEl.expected_received_date.val("");
  t.mdl.transfer.frmEl.cc_users.val(null).trigger("change");
  t.mdl.transfer.frmEl.internal_place.val(null).trigger("change");
  t.mdl.transfer.frmEl.responsible_user.val(null).trigger("change");

  if (t.mdl.transfer.frmEl.notes.length && t.mdl.transfer.frmEl.notes.summernote) {
    t.mdl.transfer.frmEl.notes.summernote('code', '');
  }
  if (t.mdl.transfer.frmEl.expected_received_date.datepicker) {
    t.mdl.transfer.frmEl.expected_received_date.datepicker("setDate", new Date());
  }
  if (t.mdl.transfer.frm && t.mdl.transfer.frm.validate) {
    t.mdl.transfer.frm.validate().resetForm();
  }

  t.mdl.transfer.btnSubmit1.prop('disabled', false);
};

MyApp.prototype.initTransferSelect2 = function () {
  var t = this;
  var select2Opts = { width: "100%" };
  var tr = t.config.translations || {};
  t.mdl.transfer.frmEl.transfer_to.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.transfer.frmEl.transfer_to.parent(),
    ajax: {
      url: t.config.getLocationByAjax,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.selected_company_id) {
          success({ results: [] });
          return;
        }
        var request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: t.selected_company_id
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.select_the_location || "Select Location",
    language: {
      noResults: function () {
        if (!t.selected_company_id) {
          return "Please select devices from the same company";
        }
        return "No Data Found";
      }
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    }
  }));

  t.mdl.transfer.frmEl.internal_place.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.transfer.frmEl.internal_place.parent(),
    placeholder: tr.select_internal_place || "Select Internal Place",
    allowClear: true
  }));
  t.mdl.transfer.frmEl.responsible_user.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.transfer.frmEl.responsible_user.parent(),
    placeholder: tr.select_the_user || "Select User",
    allowClear: true
  }));
  t.mdl.transfer.frmEl.cc_users.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.transfer.frmEl.cc_users.parent(),
    ajax: {
      url: t.config.getActivatedUsers,
      dataType: "json",
      transport: function (params, success, failure) {
        if (!t.selected_company_id) {
          success({ results: [] });
          return;
        }
        var request = $.ajax(params);
        request.then(success);
        request.fail(failure);
        return request;
      },
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
          company_id: t.selected_company_id
        };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: tr.select_the_user || "Select User",
    language: {
      noResults: function () {
        if (!t.selected_company_id) {
          return "Please select devices from the same company";
        }
        return "No Data Found";
      }
    },
    templateResult: function (s) {
      if (s.loading) {
        return $("<div>" + s.text + "</div>");
      }
      var a = '';
      a += "<div class='row'>";
      a += "<div class='col-sm-10'>";
      if (s.displayName) {
        a += "<div class='so-t'><i class='fa fa-user'></i>" + s.displayName;
      } else {
        a += "<div class='so-t'><i class='fa fa-user'></i>" + (s.first_name || "") + " " + (s.last_name || "");
      }
      a += "<span class='active-user'></span></div>";
      if (s.email) {
        a += "<div class='so-t'><i class='fa fa-envelope-o'></i>" + s.email + "</div>";
      }
      if (s.employee_num) {
        a += "<div class='so-t'><i class='fa fa-credit-card'></i>" + s.employee_num + "</div>";
      }
      if (s.company_name) {
        a += "<div class='so-t'><i class='fa fa-building'></i> " + s.company_name + "</div>";
      }
      a += "</div>";
      a += "<div class='col-sm-2'>";
      if (s.img_path) {
        a += "<img class='img-u' src='" + s.img_path + "' />";
      }
      a += "</div></div>";
      return $(a);
    },
    templateSelection: function (data, container) {
      $(container).attr('title', data.text);
      return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
    }
  }));
};

MyApp.prototype.loadTransferInternalPlaces = function () {
  var t = this;
  var tr = t.config.translations || {};
  t.mdl.transfer.frmEl.internal_place.empty().append(new Option(tr.select_internal_place || "Select Internal Place", "", true, true));
  t.mdl.transfer.frmEl.internal_place.trigger("change");

  var location_id = t.mdl.transfer.frmEl.transfer_to.val();

  if (location_id && location_id > 0) {
    $.get(t.config.getInternalPlaceByAjax + '/' + location_id)
      .done(function (data) {
        if (typeof data == "object" && data.results && data.results.length) {
          $.each(data.results, function (i, v) {
            t.mdl.transfer.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
          });
        }
        t.mdl.transfer.frmEl.internal_place.trigger("change");
      })
      .fail(function () {
        t.mdl.transfer.frmEl.internal_place.trigger("change");
      });
  }
};


MyApp.prototype.loadTransferResponsibleUsers = function () {
  var t = this;
  var tr = t.config.translations || {};
  t.mdl.transfer.frmEl.responsible_user.empty().append(new Option(tr.select_the_user || "Select User", "", true, true));
  t.mdl.transfer.frmEl.responsible_user.trigger("change");
  var location_id = t.mdl.transfer.frmEl.transfer_to.val();
  if (location_id && location_id > 0) {
    t.mdl.transfer.frmEl.responsible_user.select2({
      dropdownParent: t.mdl.transfer.frmEl.responsible_user.parent(),
      width: "100%",
      ajax: {
        url: t.config.getUserByLocation + '/' + location_id,
        dataType: "json",
        transport: function (params, success, failure) {
          if (!t.selected_company_id) {
            success({ results: [] });
            return;
          }
          var request = $.ajax(params);
          request.then(success);
          request.fail(failure);
          return request;
        },
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: t.selected_company_id
          };
        },
        delay: 300
      },
      allowClear: true,
      placeholder: tr.select_the_user || "Select User",
      language: {
        noResults: function () {
          if (!t.selected_company_id) {
            return "Please select devices from the same company";
          }
          return "No Data Found";
        }
      },
      templateResult: function (item) {
        return t._userDropdownFormat(item);
      },
      templateSelection: function (data, container) {
        if (!data || !data.id) {
          return data.text || "";
        }
        var name = data.displayName || data.text || data.first_name + " " + data.last_name || "-";
        var imageUrl = data.img_path || "";
        var avatarHtml = '';
        if (imageUrl) {
          avatarHtml = '<img src="' + t.escapeHtml(imageUrl) + '" class="user-list-avatar rounded-circle" style="width:28px;height:28px;object-fit:cover;" />';
        } else {
          var initials = name.charAt(0).toUpperCase();
          avatarHtml = '<span class="user-list-avatar user-list-avatar-fallback d-inline-flex align-items-center justify-content-center rounded-circle" style="width:28px;height:28px;background:#e9ecef;color:#495057;font-weight:600;font-size:12px;">' + initials + '</span>';
        }
        $(container).attr('title', name);
        return $(
          '<div class="d-flex align-items-center gap-2">' +
          avatarHtml +
          '<span>' + t.escapeHtml(name.length > 40 ? name.substring(0, 40) + '...' : name) + '</span>' +
          '</div>'
        );
      },
      escapeMarkup: function (m) {
        return m;
      }
    });
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
  console.log(imageUrl);
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

MyApp.prototype.billableCheck = function (e) {
  var t = this;
  if ($(e.currentTarget).val() == '1') {
    t.mdl.checkout.billable_div.removeClass("hide");
    t.getRate();
  } else {
    t.mdl.checkout.billable_div.addClass("hide");
  }
};

MyApp.prototype.getRate = function () {
  var t = this;
  var device_id = t.mdl.checkout.frmEl.id.val();
  var select2Opts = { width: "100%" };
  t.mdl.checkout.frmEl.rate.select2($.extend({}, select2Opts, {
    dropdownParent: t.mdl.checkout.frmEl.rate.parent(),
    ajax: {
      url: t.config.getDropDown + "/" + device_id,
      dataType: "json",
      data: function (p) {
        return { search: p.term, page: p.page || 1 };
      },
      delay: 300
    },
    allowClear: true,
    placeholder: 'Select Rate'
  }));
};

MyApp.prototype.getCost = function () {
  var t = this;
  var device_id = t.mdl.checkout.frmEl.id.val();
  var type_val = t.mdl.checkout.frmEl.rate.val();
  $.get(t.config.getCostByAjax + "/" + type_val + "/" + device_id).done(function (data) {
    if (typeof data != undefined && typeof data == "object" && data.status != 'fail') {
      t.mdl.checkout.rate_cost.val(Object.values(data)[0]);
    } else {
      t.mdl.checkout.rate_cost.val(0);
    }
  });
};

MyApp.prototype.loadImageViewer = function (img) {
  var t = this;
  if (img != "" && img != null && (t.mdl.device.forAction == "edit" || t.mdl.device.forAction == "clone")) {
    t.mdl.device.imgview.attr("src", t.config.imgviewpath + "/" + img);
    t.mdl.device.imgviewcover.removeClass("hide");
    if (t.mdl.device.forAction == "clone") {
      t.mdl.device.frmEl.clone_img.val(img);
    }
  } else {
    t.mdl.device.imgview.attr("src", "");
    t.mdl.device.imgviewcover.addClass("hide");
  }
};

MyApp.prototype.clearCustomFields = function () {
  this.mdl.device.find(".custom-field-row").remove();
};

MyApp.prototype.fillCustomFields = function (data) {
  var t = this;
  if (typeof data.html == "undefined") return;
  t.clearCustomFields();
  t.mdl.device.customFieldsPrvEl.after(data.html);
  if (data.required_fields.length > 0) {
    $.each(data.required_fields, function (i, d) {
      t.mdl.device.find("#" + d).rules("add", { required: true });
    });
  }
  $.each(data.all_fields, function (i, d) {
    t.mdl.device.find("#" + d).rules("add", { remarks: false });
  });
  $(".cf-select2").each(function () {
    var $field = $(this);
    var fieldId = $field.attr("id");
    if (fieldId) {
      $field.select2($.extend({}, { width: "100%" }, { dropdownParent: $field.parent() }));
    }
  });
};

MyApp.prototype.getCustomFields = function (e) {
  e.preventDefault();
  var t = this;
  if (t.mdl.device.frmEl.model.attr('data-noLoadField') == "true") return;
  t.clearCustomFields();
  var model_id = parseInt(t.mdl.device.frmEl.model.val());
  var device_id = t.mdl.device.frm.find("#id").val();
};

MyApp.prototype.handleExpenseSubmit = function (e) {
  e.preventDefault();
  if (this.expenseFrmValidator.form() == false) return false;
  if (this.httpCall != true) return false;
  var t = this;
  var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
  t.mdl.expense.frmEl.temp_id.val(v);
  t.temp_id = v;
  t.httpCall = false;
  var formData = new FormData(t.mdl.expense.frm[0]);

  $.ajax({
    url: t.config.url.dmSave,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Expense saved successfully");
          t.mdl.expense.modal("hide");
        } else {
          t.showToast("error", data.msg || "Failed to save expense");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    always: function () {
      t.httpCall = true;
    }
  });
};

MyApp.prototype.handleSupplierSubmit = function (e) {
  e.preventDefault();
  var t = this;
  var name = t.mdl.supplier.frmEl.supplier_name.val();
  if (!name) {
    t.showToast("error", "Supplier name is required");
    return;
  }

  if (t.httpCall != true) return;
  t.httpCall = false;
  t.mdl.supplier.btnSubmit.attr("disabled", true);
  var formData = new FormData(t.mdl.supplier.frm[0]);

  $.ajax({
    url: t.config.url.supplier_add,
    type: "POST",
    processData: false,
    contentType: false,
    data: formData,
    success: function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.showToast("success", data.msg || "Supplier added successfully");
          t.mdl.supplier.modal("hide");
          var latestSupplier = data.latestSupplier;
          if (latestSupplier) {
            var newOption = new Option(latestSupplier.name, latestSupplier.id, true, true);
            $("#vendor").append(newOption).trigger('change');
          }
          t.loadDevices();
        } else {
          t.showToast("error", data.msg || "Failed to add supplier");
        }
      }
    },
    error: function () {
      t.showToast("error", "Something went wrong");
    },
    always: function () {
      t.mdl.supplier.btnSubmit.removeAttr("disabled");
      t.httpCall = true;
    }
  });
};

MyApp.prototype.handleTransferSubmit = function (e) {
  e.preventDefault();
  var t = this;
  if (t.transferFrmValidator.form() == false) {
    return false;
  }
  var result = t.getSelectedTransferDevices();
  var selected = result.ids;

  if (selected.length === 0) {
    t.showToast('error', "No devices selected for transfer");
    return;
  }
  if (t.transferHttpCall !== true) {
    return;
  }
  t.transferHttpCall = false;
  t.mdl.transfer.btnSubmit1.prop('disabled', true);

  var frmData = new FormData();
  frmData.append('transfer_to', t.mdl.transfer.frmEl.transfer_to.val());
  frmData.append('internal_place', t.mdl.transfer.frmEl.internal_place.val() || '');
  frmData.append('responsible_user', t.mdl.transfer.frmEl.responsible_user.val());
  frmData.append('expected_received_date', t.mdl.transfer.frmEl.expected_received_date.val());
  frmData.append('cc_users', t.mdl.transfer.frmEl.cc_users.val() || '');
  frmData.append('notes', t.mdl.transfer.frmEl.notes.summernote ? t.mdl.transfer.frmEl.notes.summernote('code') : t.mdl.transfer.frmEl.notes.val());
  frmData.append('ids', selected.join(','));

  $.ajax({
    url: t.transferHttpPostPath || t.config.url.transfer_device,
    type: "POST",
    processData: false,
    contentType: false,
    data: frmData,
    success: function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          t.showToast('success', data.msg || "Devices transferred successfully");
          t.mdl.transfer.modal("hide");
          if (t.config.print_device_array) {
            t.config.print_device_array = [];
          }
          if (t.mergeObj && t.mergeObj.merge_items) {
            t.mergeObj.merge_items.others = [];
            t.mergeObj.refreshUi();
          }
          t.loadDevices();
          t.selectedTransferIds = [];
        } else {
          t.showToast('error', data.msg || "Failed to transfer devices");
        }
      }
    },
    error: function (xhr) {
      var msg = t.config.translations.something_went_wrong || "Something went wrong";
      if (xhr.responseJSON && xhr.responseJSON.msg) {
        msg = xhr.responseJSON.msg;
      }
      t.showToast('error', msg);
    },
    complete: function () {
      t.mdl.transfer.btnSubmit1.prop('disabled', false);
      t.transferHttpCall = true;
    }
  });
};


MyApp.prototype.handleActionMenuMouseEnter = function (e) {
  this.showActionMenu($(e.currentTarget));
};

MyApp.prototype.handleActionMenuMouseLeave = function (e) {
  this.scheduleActionMenuHide($(e.currentTarget));
};

MyApp.prototype.handleActionMenuFocusIn = function (e) {
  this.showActionMenu($(e.currentTarget));
};

MyApp.prototype.handleActionMenuFocusOut = function (e) {
  var t = this;
  var dropdown = $(e.currentTarget);
  window.setTimeout(function () {
    if (!dropdown.length || dropdown.find(document.activeElement).length) return;
    t.scheduleActionMenuHide(dropdown);
  }, 0);
};

MyApp.prototype.handleActionMenuToggleClick = function (e) {
  if (e) e.preventDefault();
  if (e && e.currentTarget && typeof e.currentTarget.blur === "function") e.currentTarget.blur();
};

MyApp.prototype.handleActionMenuItemClick = function (e) {
  this.hideActionMenu($(e.currentTarget).closest(".dropdown"));
};

MyApp.prototype.showActionMenu = function (dropdown) {
  dropdown = $(dropdown);
  if (!dropdown.length) return;
  var menu = dropdown.find(".action-dropdown-menu");
  this.clearActionMenuHideTimer();
  this.hideActionMenus(dropdown);
  dropdown.closest("td, th").addClass("is-action-menu-open");
  dropdown.closest("tr").addClass("is-action-menu-open");
  dropdown.addClass("is-hover-open");
  dropdown.find(".device-list-menu-toggle").addClass("show").attr("aria-expanded", "true");
  menu.addClass("show").removeClass("dropdown-left-up dropdown-left-down");
  var rect = dropdown[0].getBoundingClientRect();
  var menuHeight = menu.outerHeight();
  var spaceBelow = window.innerHeight - rect.bottom;
  var spaceAbove = rect.top;
  if (spaceAbove < menuHeight) {
    menu.addClass("dropdown-left-down");
  } else if (spaceBelow < menuHeight) {
    menu.addClass("dropdown-left-up");
  }
};

MyApp.prototype.hideActionMenu = function (dropdown) {
  dropdown = $(dropdown);
  if (!dropdown.length) return;
  dropdown.closest("td, th").removeClass("is-action-menu-open");
  dropdown.closest("tr").removeClass("is-action-menu-open");
  dropdown.removeClass("is-hover-open");
  dropdown.find(".device-list-menu-toggle").removeClass("show").attr("aria-expanded", "false");
  dropdown.find(".action-dropdown-menu").removeClass("show");
};

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
  var exceptEl = exceptDropdown && $(exceptDropdown).length ? $(exceptDropdown).get(0) : null;
  t.content.find(".device-list-actions .dropdown.is-hover-open").each(function () {
    if (exceptEl && this === exceptEl) return;
    t.hideActionMenu($(this));
  });
};


MyApp.prototype.getSelectedDeviceIds = function () {
  return Object.keys(this.selectedIds);
};

MyApp.prototype.bindCheckboxEvents = function () {
  var self = this;
  $(document).off("change.deviceRowCheckbox").on("change.deviceRowCheckbox", ".row-checkbox", function () {
    var id = $(this).val();
    if ($(this).is(":checked")) {
      self.selectedIds[id] = true;
      self.addCheckedDeviceToMerge(id);
    } else {
      delete self.selectedIds[id];
      self.removeCheckedDeviceFromMerge(id);
    }
  });

  $("#checkAll").off("click").on("click", function () {
    var checked = this.checked;
    self.tableBody.find(".row-checkbox").each(function () {
      $(this).prop("checked", checked);
      var id = $(this).val();
      if (checked) {
        self.selectedIds[id] = true;
        self.addCheckedDeviceToMerge(id);
      } else {
        delete self.selectedIds[id];
        self.removeCheckedDeviceFromMerge(id);
      }
    });
  });
};

MyApp.prototype.addCheckedDeviceToMerge = function (deviceId) {
  var t = this;
  if (!t.mergeObj) return;
  if (!t.mergeObj.merge_items.canAddItem(deviceId)) {
    return;
  }
  var deviceData = null;
  if (t._deviceData && Array.isArray(t._deviceData)) {
    $.each(t._deviceData, function (i, v) {
      if (String(v.id) === String(deviceId)) {
        deviceData = v;
        return false;
      }
    });
  }
  if (!deviceData) {
    var $row = $("#row-" + deviceId);
    if ($row.length) {
      deviceData = {
        id: deviceId,
        asset_tag: $row.find('.dvl-dev-tag-link').text() || "",
        name: $row.find('.b5-text.fw-medium').eq(1).text() || "",
        cmp_name: $row.find('.b5-text.toggle-element.collapsed[data-bs-title="Company"]').text() || "",
        loc_name: $row.find('.b5-text.fw-medium[data-bs-title="Device Location"]').text() || "",
        lbl_name: $row.find('.dvl-badge').text() || "",
        company_id: $row.find('.dtActEdit').attr('data-company_id') || ""
      };
    }
  }

  if (deviceData) {
    t.mergeObj.addToMerge(deviceData, true);
  }
};

MyApp.prototype.removeCheckedDeviceFromMerge = function (deviceId) {
  var t = this;
  if (!t.mergeObj) return;
  if (Array.isArray(t.mergeObj.merge_items.others)) {
    t.mergeObj.merge_items.others = t.mergeObj.merge_items.others.filter(function (item) {
      return String(item.id) !== String(deviceId);
    });
  }

  if (Array.isArray(t.config.print_device_array)) {
    t.config.print_device_array = t.config.print_device_array.filter(function (item) {
      return String(item.id) !== String(deviceId);
    });
  }
  if (Array.isArray(t.config.device_id_array)) {
    t.config.device_id_array = t.config.device_id_array.filter(function (item) {
      return String(item) !== String(deviceId);
    });
  }

  t.mergeObj.refreshUi();
};


$(document).ready(function () {
  if ($("#main-user-list-wrapper").length && typeof config !== "undefined") {
    var app = new MyApp(config);
    window.myApp = app;
  }
});

var menuCloseTimer;
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
    if (btnRect.bottom + menuHeight > windowHeight) {
      $menu.css('top', (btnRect.top - menuHeight) + 'px');
    }
    if (btnRect.left + menuWidth > windowWidth) {
      $menu.css('left', (windowWidth - menuWidth - 10) + 'px');
    }
    if (parseFloat($menu.css('left')) < 0) {
      $menu.css('left', '10px');
    }
  }, 0);

  $btn.data('menu-open', true);
});



$(document).on('click', '#detached-action-menu .dropdown-item', function (e) {
  e.preventDefault();
  e.stopPropagation();
  var $item = $(this);
  var id = $item.data('id');
  $('#detached-action-menu').remove();
  $('.user-list-menu-toggle').data('menu-open', false);
  var actionClass = null;
  ['dtActClone', 'dtActView', 'dtActSendCredential', 'dtActNotify', 'add_to_merge', 'dtActEdit', 'dtActDel', 'dtActRestore', 'dtActCheckout', 'dtActCheckin', 'dtActPrintLabel'].forEach(function (cls) {
    if ($item.hasClass(cls)) actionClass = cls;
  });

  if (actionClass && id) {
    var $target = $('#main-user-list-wrapper').find('.' + actionClass + '[data-id="' + id + '"]');
    if (!$target.length) {
      $target = $('#deviceTableBody').find('.' + actionClass + '[data-id="' + id + '"]');
    }
    if (!$target.length) {
      $target = $('.' + actionClass + '[data-id="' + id + '"]').first();
    }
    if ($target.length) {
      $target.first().trigger('click');
    } else {
      if (actionClass === 'dtActClone' && window.myApp) {
        window.myApp.cloneDevice(id);
      }
    }
  }
});

$(document).on('scroll', function () {
  $('#detached-action-menu').remove();
  $('.user-list-menu-toggle').data('menu-open', false);
});