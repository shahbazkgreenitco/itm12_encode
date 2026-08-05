var MyApp = function (config) {
  var t = this;
  t.config = config;
  t.content = $("section.content");
  t.table = t.content.find("#mytable");

  t.httpCall = true;
  t.httpPostPath = "";

  t.showDeletedAccessories = false;
  t.forAction = "";
  t.note = $("section.content").find("#noteModal");
  t.note.noteContent = t.note.find("#noteContent");
  t.mdl = $("section.content").find("#accessory-mdl");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");

  t.mdl.frm = t.mdl.find("#accessory-mdl-frm");
  t.mdl.frmEl = {};
  t.mdl.frmEl.id = t.mdl.frm.find("#id");
  t.mdl.frmEl.unique_tag = t.mdl.frm.find("#unique_tag");
  t.mdl.frmEl.company = t.mdl.frm.find("#company_id");
  t.mdl.frmEl.category = t.mdl.frm.find("#category_id");
  t.mdl.frmEl.location = t.mdl.frm.find("#location_id");
  t.mdl.frmEl.internal_place = t.mdl.frm.find("#internal_place");
  t.mdl.frmEl.manufacturer = t.mdl.frm.find("#manufacturer_id");
  //t.mdl.frm.manu_deselect = t.mdl.frm.find(".deselect");
  //t.mdl.frm.sup_deselect = t.mdl.frm.find(".deselect");
  //t.mdl.frm.in_deselect = t.mdl.frm.find(".deselect");
  t.mdl.frmEl.purchase_date = t.mdl.frm.find("#purchase_date");
  t.mdl.frmEl.purchase_currency = t.mdl.frm.find("#purchase_currency");
  t.mdl.frmEl.supplier = t.mdl.frm.find("#supplier_id");
  t.mdl.frmEl.invoice_id = t.mdl.frm.find("#invoice_id");
  t.mdl.frmEl.notes = t.mdl.frm.find("#notes");
  t.mdl.frmEl.customFieldsPrvEl = t.mdl.frm.find(".custom-fields-follow");
  t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
  t.mdl.frmEl.file = t.mdl.frm.find("#file");
  t.mdl.frmEl.imgviewcover = t.mdl.frm.find(".imgviewcover");
  t.mdl.frmEl.imgview = t.mdl.frm.find("#imgview");
  t.mdl.frmEl.clone_img = t.mdl.frm.find("#clone_img");
  t.mdl.frmEl.foraction = t.mdl.frm.find("#forAction");
  t.mdl.frmEl.accessory_thresholds = t.mdl.frm.find("#accessory_thresholds");
  t.mdl.frmEl.thresholds_alerts = t.mdl.frm.find("#thresholds_alerts");
  t.mdl.frmEl.requestable_accessory = t.mdl.frm.find("#requestable_accessory");
  t.mdl.frmEl.threshold_alert_users = t.mdl.frm.find("#threshold_alert_users");
  t.mdl.frmEl.reorder_limits = t.mdl.frm.find("#reorder_limits");
  t.btn = {};
  var current_purchase_date = "";
  var current_supplier_name = (current_supplier_id = "");
  var order_no = (purchase_cost = currency_default = "");
  // checkout
  t.chkout = {};
  t.chkout.mdl = $("section.content").find("#accessory-checkout-mdl");
  t.chkout.mdl.btnSubmit = t.chkout.mdl.find("#btnSubmit");
  t.chkout.mdl.btnClear = t.chkout.mdl.find("#btnClear");
  t.chkout.mdl.frm = t.chkout.mdl.find("#accessory-checkout-mdl-frm");
  t.chkout.mdl.frmEl = {};
  t.chkout.mdl.frmEl.id = t.chkout.mdl.frm.find("#id");
  t.chkout.mdl.frmEl.assigned_for = t.chkout.mdl.frm.find("#assigned_for");
  t.chkout.mdl.frmEl.assigned_to = t.chkout.mdl.frm.find("#assigned_to");
  t.chkout.mdl.frmEl.assigned_place = t.chkout.mdl.frm.find("#assigned_place");
  t.chkout.mdl.frmEl.device_id = t.chkout.mdl.frm.find("#device_id");
  t.chkout.mdl.frmEl.expected_checkin =
    t.chkout.mdl.frm.find("#expected_checkin");
  t.chkout.mdl.lblAccessoryName = t.chkout.mdl.find("#lblAccessoryName");
  t.chkout.mdl.lblAccessoryCategory = t.chkout.mdl.find(
    "#lblAccessoryCategory",
  );

  t.filters = {
    // wrapper: t.content.find("#advance-filters"),
    wrapper: $("#accessoryFilterModal"),
  };
  t.filters.location = t.filters.wrapper.find("#filter_by_location");
  t.filters.categories = t.filters.wrapper.find("#filter_by_category");
  t.filters.purchase_reference = t.filters.wrapper.find(
    "#filter_by_purchase_reference",
  );
  t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
  ((t.filters.date_range = t.filters.wrapper.find("#reportrange")),
    (t.filters.daterange = t.filters.wrapper.find("#daterange")),
    (t.filters.assetDepartment = t.filters.wrapper.find(
      "#filter_by_department",
    )),
    (t.filters.assigned_user = t.filters.wrapper.find("#filter_by_user")),
    (t.filters.filter_by_assigned_place =
      t.filters.wrapper.find("#assigned_place")),
    (t.filters.filter_by_device_assigned =
      t.filters.wrapper.find("#asset_assigned_id")),
    (t.filters.available_accessories = t.filters.wrapper.find(
      "#filter_by_available_accessories",
    )),
    (t.filters.internal_place = t.filters.wrapper.find(
      "#filter_by_internal_place",
    )));
  t.filters.assigned_for = t.filters.wrapper.find("#assigned_for");
  t.btn.filter = t.filters.wrapper.find(".filter");
  t.btn.clear = t.filters.wrapper.find(".clear");
  t.data = {};
  t.internal_places = [];

  t.filters.fun = {
    reload_location: function () {
      t.filters.location.empty();
      t.filters.location.append(new Option("No Filter", "", false, false));
      $.each(t.config.location, function (i, k) {
        t.filters.location.append(new Option(k.name, k.id, false, false));
      });
      t.filters.location.select2({
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        allowClear: true,
        placeholder: config.translations.select_location,
      });
    },
    reload_categories: function () {
      t.filters.categories.empty();

      t.filters.categories.append(new Option("No Filter", "", false, false));

      $.each(t.config.categories, function (i, k) {
        t.filters.categories.append(new Option(k.text, k.id, false, false));
      });

      t.filters.categories.select2({
        width: "100%",

        dropdownParent: $("#accessoryFilterModal"),

        allowClear: true,

        placeholder: "Select Category",
      });
    },

    reload_internal_place: function () {
      t.filters.internal_place.empty();
      t.filters.internal_place.append(
        new Option("No Filter", "", false, false),
      );
      $.each(t.config.internalPlaces, function (i, k) {
        t.filters.internal_place.append(new Option(k.text, k.id, false, false));
      });

      t.filters.internal_place.select2({
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        allowClear: true,
        placeholder: config.translations.select_internal_place,
      });
    },

    reload_static_filters: function () {
      t.filters.available_accessories.select2({
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        minimumResultsForSearch: Infinity,
      });

      t.filters.based_on.select2({
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        minimumResultsForSearch: Infinity,
      });

      t.filters.assigned_for.select2({
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        minimumResultsForSearch: Infinity,
      });
    },

    bindFilterCountEvents: function () {
      $("#accessoryFilterModal")
        .find("select, input")
        .off(".filterCount")
        .on("change.filterCount keyup.filterCount", function () {
          t.filters.fun.updateFilterCount();
        });
    },

    updateFilterCount: function () {
      let count = 0;
      const filters = t.filters.fun.getFilters();
      $.each(filters, function (key, value) {
        if (Array.isArray(value)) {
          if (value.length > 0) {
            count++;
          }
        } else if (value !== null && value !== undefined && value !== "") {
          count++;
        }
      });

      let badge = $(".filter-count-badge");
      if (count > 0) {
        badge.removeClass("d-none");
        badge.text(count);
      } else {
        badge.addClass("d-none");
        badge.text("0");
      }
    },

    purchase_reference: function () {
      t.filters.purchase_reference.select2(
        $.extend({}, select2Opts, {
          dropdownParent: t.filters.purchase_reference.parent(),
          ajax: {
            url: t.config.getInvoiceByAjax,
            dataType: "json",
            data: function (p) {
              return {
                search: p.term,
                page: p.page || 1,
              };
            },
            delay: 300,
          },
          allowClear: true,
          placeholder: config.translations.Select_the_Purchase_Invoice,
          templateSelection: function (s, container) {
            if (typeof s.loading != "undefined" && s.loading) {
              return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr({
              "data-invoice_date": s.invoice_date,
              "data-name": s.supplier_name,
              "data-id": s.supplier_id,
              "data-order-number": s.order_number,
              "data-po-number": s.po_number,
              "data-purchase-cost": s.purchase_cost,
              "data-currency": s.currency,
            });
            return s.text;
          },
        }),
      );
    },

    initStaticFilters: function () {
      const base = {
        width: "100%",
        dropdownParent: $("#accessoryFilterModal"),
        allowClear: true,
      };

      if (t.filters.location.length) {
        t.filters.location.select2({
          ...base,
          placeholder: config.translations.select_location,
        });
      }

      if (t.filters.categories.length) {
        t.filters.categories.select2({
          ...base,
          placeholder: config.translations.Select_Category,
        });
      }

      if (t.filters.internal_place.length) {
        t.filters.internal_place.select2({
          ...base,
          placeholder: config.translations.select_the_internal_place,
        });
      }

      if (t.filters.assetDepartment.length) {
        t.filters.assetDepartment.select2({
          ...base,
          placeholder: config.translations.Select_the_Department,
        });
      }

      // if (t.filters.available_accessories.length) {
      //   t.filters.available_accessories.select2({
      //     ...base,
      //     placeholder: "Available Accessories",
      //   });
      // }

      // if (t.filters.based_on.length) {
      //   t.filters.based_on.select2({
      //     ...base,
      //     placeholder: "Select Date Type",
      //   });
      // }

      // if (t.filters.assigned_for.length) {
      //   t.filters.assigned_for.select2({
      //     ...base,
      //     placeholder: "Assigned For",
      //   });
      // }
    },

    getFilters: function () {
      return {
        location: t.filters.location.val() ? [t.filters.location.val()] : [],

        categories: t.filters.categories.val()
          ? [t.filters.categories.val()]
          : [],

        purchase_reference: t.filters.purchase_reference.val()
          ? [t.filters.purchase_reference.val()]
          : [],

        based_on: t.filters.based_on.val() || "",

        date_range: t.filters.daterange.val() || "",

        asset_department: t.filters.assetDepartment.val()
          ? [t.filters.assetDepartment.val()]
          : [],

        assigned_user: t.filters.assigned_user.val()
          ? [t.filters.assigned_user.val()]
          : [],

        assigned_place: t.filters.filter_by_assigned_place.val()
          ? [t.filters.filter_by_assigned_place.val()]
          : [],

        device_assigned: t.filters.filter_by_device_assigned.val()
          ? [t.filters.filter_by_device_assigned.val()]
          : [],

        available_accessories: t.filters.available_accessories.val() || "",

        internal_place: t.filters.internal_place.val()
          ? [t.filters.internal_place.val()]
          : [],

        assigned_for: t.filters.assigned_for.val() || "",
      };
    },

    applyFilters: function () {
      t.config.other_filters = t.filters.fun.getFilters();
      t.dTbl.ajax.reload(null, false);
      t.filters.wrapper.modal("hide");
    },
  };

  t.syncDateRangePicker = function () {
    if (!t.filters.rangePicker) return;
    var val = t.filters.daterange.val();
    if (!val) {
      t.clearDateRange();
      return;
    }
    var parts = val.split(" - ");
    if (parts.length !== 2) return;
    var start = moment(parts[0], "YYYY-MM-DD HH:mm:ss");
    var end = moment(parts[1], "YYYY-MM-DD HH:mm:ss");
    t.setDateRange(start, end);
  };

  t.initDateRangePicker = function () {
    if (
      !t.filters.date_range.length ||
      typeof moment === "undefined" ||
      !$.fn.daterangepicker
    ) {
      return;
    }
    if (t.filters.date_range.data("daterangepicker")) {
      t.syncDateRangePicker();
      return;
    }
    if (t.filters.rangePicker) {
      t.syncDateRangePicker();
      return;
    }
    var start = moment().startOf("day");
    var end = moment().endOf("day");

    t.filters.date_range.daterangepicker(
      {
        parentEl: t.filters.wrapper,
        startDate: start,
        endDate: end,
        timePicker: true,
        timePicker24Hour: true,
        timePickerSeconds: true,
        timePickerIncrement: 1,
        autoApply: false,
        autoUpdateInput: false,
        opens: "right",
        drops: "down",
        locale: {
          format: "DD-MM-YYYY HH:mm:ss",
          cancelLabel: "Clear",
        },
        ranges: {
          Today: [moment().startOf("day"), moment().endOf("day")],
          Yesterday: [
            moment().subtract(1, "days").startOf("day"),
            moment().subtract(1, "days").endOf("day"),
          ],
          "Last 7 Days": [
            moment().subtract(6, "days").startOf("day"),
            moment().endOf("day"),
          ],
          "Last 30 Days": [
            moment().subtract(29, "days").startOf("day"),
            moment().endOf("day"),
          ],
          "This Month": [moment().startOf("month"), moment().endOf("month")],
          "Last Month": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
        },
      },
      cb,
    );
    cb(start, end);

    t.setDateRange = function (start, end) {
      if (!start || !end) {
        t.clearDateRange();
        return;
      }
      if (t.filters.date_range.length) {
        t.filters.date_range.text(
          start.format("DD-MM-YYYY HH:mm:ss") +
          " - " +
          end.format("DD-MM-YYYY HH:mm:ss"),
        );
      }
      t.filters.daterange.val(
        start.format("YYYY-MM-DD HH:mm:ss") +
        " - " +
        end.format("YYYY-MM-DD HH:mm:ss"),
      );
    };
  };

  t.btn.filter.on("click", function (e) {
    e.preventDefault();
    t.filters.fun.applyFilters();
  });

  t.btn.clear.on("click", function (e) {
    e.preventDefault();
    $("#accessoryFilterModal").find("select").val("").trigger("change");
    $("#daterange").val("");
    $("#reportrange span").html("Select Date Range");
    t.config.other_filters = {};
    t.dTbl.ajax.reload();
  });

  t.tblHelpers = {
    actions: function () {
      return function (d) {
        let quick = [];
        let menu = [];
        if (t.showDeletedAccessories) {
          if (
            jQuery.inArray("AccessoriesRestore", t.config.permissions) !== -1
          ) {
            quick.push(`
          <button class="user-list-action-btn dtActRestore"
            data-id="${d.id}"
            title="${config.translations.Restore_Accessory}">
            
            <!-- restore -->
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6a6 6 0 01-6 6 6 6 0 01-5.65-4H4.26A8 8 0 0012 21a8 8 0 000-16z" fill="currentColor"/>
            </svg>

          </button>
        `);
          }

          return `<div class="user-list-actions">${quick.join("")}</div>`;
        }

        if (jQuery.inArray("AccessoriesEdit", t.config.permissions) !== -1) {
          quick.push(`
        <button class="user-list-action-btn dtActEdit"
          data-id="${d.id}"
          title="${config.translations.Edit_Accessory}">

          <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>

        </button>
      `);
        }

        if (jQuery.inArray("AccessoriesDelete", t.config.permissions) !== -1) {
          quick.push(`
        <button class="user-list-action-btn dtActDel is-delete"
          data-id="${d.id}"
          title="${config.translations.Delete_Accessory}">

         <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>

        </button>
      `);
        }

        if (jQuery.inArray("AccessoriesView", t.config.permissions) !== -1) {
          menu.push(`
        <li>
          <a class="dropdown-item dtActView" href="#" data-id="${d.id}">
            <svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg>
            ${config.translations.view_accessory}
          </a>
        </li>
      `);
        }

        if (jQuery.inArray("AccessoriesAdd", t.config.permissions) !== -1) {
          menu.push(`
        <li>
          <a class="dropdown-item dtActClone" href="#" data-id="${d.id}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M16 1H4v12h2V3h10V1zm4 4H8v16h12V5z" fill="currentColor"/>
            </svg>
            ${config.translations.Clone_Accessory}
          </a>
        </li>
      `);
        }

        if (
          jQuery.inArray("AccessoriesCheckout", t.config.permissions) !== -1 &&
          d.remaining > 0
        ) {
          menu.push(`
        <li>
          <a class="dropdown-item dtActCheckOut" href="#"
            data-id="${d.id}"
            data-accessory-name="${d.name}"
            data-category-name="${d.cat_name}"
            data-company_id="${d.company_id}">
            
           <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
           <path d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z" fill="currentColor"></path>
           </svg>
             ${t.config.translations.Check_Out}
          </a>
        </li>
      `);
        }

        menu.push(`
      <li>
        <a class="dropdown-item dtActNote" href="#" data-id="${d.id}">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
            <path d="M4 2h16v20l-4-4H4V2z" fill="currentColor"/>
          </svg>
          ${config.translations.view_notes}
        </a>
      </li>
    `);

        if (!quick.length && !menu.length) {
          return `<span class="user-list-empty">-</span>`;
        }

        let dropdownId = "acc-actions-" + d.id;
        return `
  <div class="user-list-actions d-flex align-items-center gap-2">

    ${quick.join("")}

    ${
      menu.length
            ? `
      <div class="">
        <button type="button"
          class="action-link user-list-menu-toggle"
          id="${dropdownId}">

        <svg width="5" height="14" viewBox="0 0 5 20" fill="none"><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"></path></svg>

        </button>

        <ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
          aria-labelledby="${dropdownId}">
          ${menu.join("")}
        </ul>
      </div>
    `
            : ""
          }

  </div>
`;
      };
    },
    accessory: function (url) {
      return function (d) {
        var a = [];

        if (d.name) {
          a.push(
            '<div class="deviceModelText" data-bs-toggle="tooltip"  data-bs-title="' +
            config.translations.accessory_name +
            '"><a href="' +
            url.info +
            "/" +
            d.id +
            '" target="_blank">' +
            d.name +
            "</a></div>",
          );
        }
        if (d.batch_no) {
          a.push(
            '<div class="textCategory" style="cursor: pointer; color: ' +
            (t.showDeletedAccessories === true ? "#ef0707" : "#309ef2ff") +
            '" data-bs-toggle="tooltip"  data-bs-title="' +
            config.translations.batch_no +
            '">' +
            d.batch_no +
            "</div>",
          );
        }
        if (d.unique_tag) {
          a.push(
            '<div class="uniqueText" data-bs-toggle="tooltip"  data-bs-title="' +
            config.translations.unique_tag +
            '">' +
            d.unique_tag +
            "</div>",
          );
        }
        if (d.cat_name) {
          a.push(
            '<div class="textCategory" data-bs-toggle="tooltip"  data-bs-title="' +
            config.translations.category +
            '">' +
            d.cat_name +
            "</div>",
          );
        }
        if (d.cmp_name) {
          a.push(
            '<div class="deviceCompanyText" data-bs-toggle="tooltip"  data-bs-title="' +
            config.translations.Company_Name +
            '">' +
            d.cmp_name +
            "</div>",
          );
        }

        // if (d.accessories_img != null) {
        //   a.push(`<div><img class="img-sm" src="${d.accessories_img}"/></div>`);
        // }
        if (d.accessories_img != null) {
          a.push(`
            <div>
           <img src="${d.accessories_img}"
           style="width:80px; height:80px; object-fit:cover; border-radius:6px;" />
           </div>
        `);
        }
        return a.join(" ");
      };
    },
    // currecyWithFormat: function() {
    //     return function(d) {
    //         if(! d.purchase_cost_format) {
    //             return "";
    //         }
    //         return d.currency_symbol + " " + d.purchase_cost_format;
    //     }
    // },
    accessoryPurchaseDate: function (url) {
      return function (d) {
        var a = [];
        if (d.purchase_date_on) {
          a.push(
            '<div class="deviceModelText" data-bs-toggle="tooltip"  data-bs-title="Purchase Date">' +
            d.purchase_date_on +
            "</div>",
          );
        }
        if (d.purchase_cost_format) {
          a.push(
            '<div class="textCategory text-success" data-bs-toggle="tooltip"  data-bs-title="Purchase Cost">' +
            d.currency_symbol +
            " " +
            d.purchase_cost_format +
            "</div>",
          );
        }
        if (d.order_number) {
          a.push(
            '<div class="deviceCompanyText" data-bs-toggle="tooltip"  data-bs-title="Order No">' +
            d.order_number +
            "</div>",
          );
        }
        return a.join(" ");
      };
    },
    location_name: function () {
      return function (d) {
        var a = [];
        if (d.loc_name != null) {
          a.push(
            '<a href="' +
            baseURL +
            "/locations/" +
            '" target="_blank"><div data-bs-toggle="tooltip" data-placement="top" data-bs-title="' +
            config.translations.location +
            '">' +
            d.loc_name +
            "</div></a>",
          );
        }
        if (d.place_name != null) {
          a.push(
            '<a href="' +
            baseURL +
            "/internal-places/" +
            '" target="_blank" style="cursor: pointer;" ><div class="deviceModelText" data-bs-toggle="tooltip" data-placement="top" data-bs-title="' +
            config.translations.internal_place +
            '">' +
            d.place_name +
            "</div></a>",
          );
        }
        return a.join(" ");
      };
    },
  };

  t.dTbl = t.table.DataTable({
    processing: true,
    serverSide: true,
    deferRender: true,
    stateSave: false,
    searchDelay: 600,

    pageLength: 10,
    lengthChange: false,
    dom: "ltrip",
    scrollX: true,
    scrollCollapse: true,
    autoWidth: false,

    ordering: true,
    order: [[8, "desc"]],
    pagingType: "simple_numbers",

    ajax: {
      url: t.config.url.accessories,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.showDeletedAccessories = t.showDeletedAccessories;
        d.location = t.config.location_filter;
        d.department = t.config.department_filter;
        d.cat_id = t.config.cat_id_filter;
        d.type = t.config.type;
        d.filters = t.config.other_filters;
        d.main_filter = t.config.main_filter;
        d.q = t.config.accessoryfilter;
      },
    },

    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },

    columns: [
      {
        data: null,
        sortable: false,
        render: function (d, t, r, meta) {
          return meta.row + 1;
        },
      },
      {
        data: "a",
        render: t.tblHelpers.accessory(t.config.url),
      },
      {
        data: "a",
        render: t.tblHelpers.location_name(),
      },
      {
        data: "a",
        className: "text-center",
        render: (d) => d.qty ?? "",
      },
      {
        data: "a",
        className: "text-center",
        // render: (d) => d.remaining ?? "",
        render: function (d) {
          var a = [];
          if (
            (d.accessory_thresholds > 0 &&
              d.remaining <= d.accessory_thresholds) ||
            (d.catthreshold > 0 && d.remaining <= d.catthreshold)
          ) {
            a.push(
              "<span class='text-danger fw-bold'>" + d.remaining + "</span>",
            );
          } else {
            a.push("<span>" + d.remaining + "</span>");
          }
          return a.join("");
        },
      },

      {
        data: "a",
        className: "text-center",
        render: (d) => d.scrap_qty ?? "",
      },
      // {
      //   data: "a",
      //   className: "text-center",
      //   render: (d) => d.accessory_thresholds ?? "",
      // },
      {
        data: "a",
        className: "text-center",
        render: function (d) {
          let value = d.accessory_thresholds ?? 0;
          if (parseInt(value) === 0) {
            return "<span class='text-danger fw-bold'>" + value + "</span>";
          }
          return "<span>" + value + "</span>";
        },
      },
      {
        data: "a",
        render: t.tblHelpers.accessoryPurchaseDate(),
      },

      // {
      //   data: "a",
      //   render: function (d) {
      //     return d.last_updated_at ?? "";
      //   },
      // },
      {
        data: "a",
        width: "120px",
        className: "text-nowrap small-column",
        render: function (d) {
          return d.last_updated_at ?? "";
        },
      },

      {
        data: "a",
        orderable: false,
        searchable: false,
        render: t.tblHelpers.actions(),
      },
    ],

    drawCallback: function () {
      $('[data-bs-toggle="tooltip"]').each(function () {
        bootstrap.Tooltip.getOrCreateInstance(this);
      });
    },
  });

  t.table.on("click", ".dtActEdit", function (e) {
    t.editAccessory.call(this, e);
  });
  t.table.on("click", ".dtActDel", function (e) {
    t.deleteAccessory.call(this, e);
  });
  t.table.on("click", ".dtActRestore", function (e) {
    t.restoreAccessory.call(this, e);
  });
  t.table.on("click", ".dtActClone", function (e) {
    t.cloneAccessory.call(this, e);
  });
  t.table.on("click", ".dtActNote", function (e) {
    t.noteAccessory.call(this, e);
  });

  t.table.on("click", ".dtActCheckOut", function (e) {
    t.checkoutAccessory.call(this, e);
  });

  t.table.on("click", ".dtActView", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    window.open(t.config.url.info + "/" + id, "_blank");
  });

  t.mdl.btnSubmit.on("click", function (e) {
    t.handleSubmit.call(this, e);
  });

  t.chkout.mdl.btnSubmit.on("click", function (e) {
    t.handleCheckoutSubmit.call(this, e);
  });

  //   t.btn.search = t.content.find(".btn-searchbox");
  t.btn.add = t.content.find(".btn-add-accessory");
  t.btn.export = t.content.find(".btn-users-export");
  t.btn.export_pdf = t.content.find(".btn-export-accessories-pdf");
  t.btn.bulkcheckout = t.content.find(".btn-bulk-checkout");
  t.btn.bulkcheckin = t.content.find(".btn-bulk-checkin");
  t.btn.reload = t.content.find(".btn-reload-list");
  t.btn.import = t.content.find(".btn-import-accessory");
  //   t.btn.deletedAccessory = t.content.find(".btn-deleted-accessory");
  t.btn.openFilter = t.content.find(".btn-open-filter");
  t.btn.showDeleted = t.content.find(".btn-show-users");

  t.getExportFilters = function () {
    let filters = {
      other_filters: t.filters.fun.getFilters(),
      search: t.dTbl.search(),
      showDeletedAccessories: t.showDeletedAccessories,
      dashboard_filters: {
        location: t.config.location_filter || null,
        department: t.config.department_filter || null,
        cat_id: t.config.cat_id_filter || null,
        type: t.config.type || null,
      },
    };
    return btoa(JSON.stringify(filters));
  };
  t.export = function (e) {
    e.preventDefault();
    window.location =
      t.config.url.download_url +
      "?q=" +
      encodeURIComponent(t.getExportFilters());
  };

  t.exportPDF = function (e) {
    e.preventDefault();
    window.location =
      t.config.url.download_url_pdf +
      "?q=" +
      encodeURIComponent(t.getExportFilters());
  };

  t.import = function (e) {
    e.preventDefault();
    window.location = t.config.url.import_url;
  };

  t.bulkcheckin = function (e) {
    e.preventDefault();
    var v = $.trim($("#mytable_wrapper .plain-search").val());
    window.location = t.config.url.bulkcheckin + escape(v);
  };

  t.bulkcheckout = function (e) {
    e.preventDefault();
    var v = $.trim($("#mytable_wrapper .plain-search").val());
    window.location = t.config.url.bulkcheckout + escape(v);
  };

  // t.btn.showDeleted.on("click", function (e) {
  //   e.preventDefault();
  //   t.showDeletedAccessories = !t.showDeletedAccessories;
  //   t.dTbl.ajax.reload();
  // });
  t.toggleDeletedAccessories = function () {
    t.showDeletedAccessories = !t.showDeletedAccessories;
    $(".non-deleted-icon").toggleClass("d-none");
    $(".deleted-icon").toggleClass("d-none");
    var title = t.showDeletedAccessories
      ? config.translations.show_non_deleted_accessory
      : config.translations.show_deleted_accessory;
    t.btn.showDeleted.attr("data-original-title", title);
    t.dTbl.ajax.reload();
  };
  t.btn.showDeleted.on("click", function (e) {
    e.preventDefault();
    t.toggleDeletedAccessories();
  });
  //binding
  t.btn.export.off("click").on("click", t.export);
  t.btn.export_pdf.on("click", $.proxy(t.exportPDF));
  t.btn.import.on("click", $.proxy(t.import));
  t.btn.bulkcheckin.on("click", $.proxy(t.bulkcheckin, t));
  t.btn.bulkcheckout.on("click", $.proxy(t.bulkcheckout));

  /* checkout box */
  t.chkout.frmValidator = t.chkout.mdl.frm.validate({
    onsubmit: false,
    rules: {
      assigned_for: {
        required: false,
        str_name: true,
        clean_text_only: true,
      },
      assigned_to: {
        str_name: true,
      },
      assigned_place: {
        str_name: true,
      },
      device_id: {
        str_name: true,
        clean_text_only: true,
      },
      expected_checkin: {
        remarks: true,
        clean_text_only: true,
      },
      note: {
        required: function () {
          return config.client === "knightfrank";
        },
        remarks: true,
        clean_text_only: true,
      },
    },
    errorPlacement: function (error, element) {
      if (element.closest(".input-group").length) {
        error.insertAfter(element.closest(".input-group").parent());
      } else {
        error.insertAfter(element);
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

  t.chkout.resetFrm = function () {
    t.chkout.mdl.frm.trigger("reset");
    t.chkout.frmValidator.resetForm();
    t.chkout.mdl.frmEl.assigned_to.empty().trigger("change");
    t.chkout.mdl.frmEl.assigned_for.val("").trigger("change");
    t.chkout.mdl.frmEl.assigned_place.empty().trigger("change");
    t.chkout.mdl.frmEl.device_id.empty().trigger("change");
    t.chkout.mdl.frmEl.expected_checkin.val("");
    t.chkout.mdl.lblAccessoryName.text("");
    t.chkout.mdl.lblAccessoryCategory.text("");
  };

  t.chkout.switchCheckTarget = function () {
    if (t.chkout.mdl.frmEl.assigned_for.val() == "2") {
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_place.rules("add", { required: true });
      t.chkout.mdl.frmEl.assigned_to.rules("remove", "required");
      t.chkout.mdl.frmEl.device_id.rules("remove", "required");
    } else if (t.chkout.mdl.frmEl.assigned_for.val() == "3") {
      t.chkout.mdl.frmEl.device_id.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.rules("add", { required: true });
      t.chkout.mdl.frmEl.assigned_to.rules("remove", "required");
      t.chkout.mdl.frmEl.assigned_place.rules("remove", "required");
    } else {
      t.chkout.mdl.frmEl.assigned_place.closest(".cover").hide();
      t.chkout.mdl.frmEl.device_id.closest(".cover").hide();
      t.chkout.mdl.frmEl.assigned_to.closest(".cover").show();
      t.chkout.mdl.frmEl.assigned_place.rules("remove");
      t.chkout.mdl.frmEl.device_id.rules("remove", "required");
      t.chkout.mdl.frmEl.assigned_to.rules("add", { required: true });
    }
  };

  t.checkoutAccessory = function (e) {
    e.preventDefault();
    t.chkout.resetFrm();
    t.httpPostPath = t.config.url.checkout;
    t.chkout.mdl.lblAccessoryName.html($(this).attr("data-accessory-name"));
    t.chkout.mdl.lblAccessoryCategory.html($(this).attr("data-category-name"));
    t.chkout.mdl.frmEl.id.val($(this).attr("data-id"));
    t.chkout.mdl.frmEl.device_id.empty();
    t.chkout.mdl.frmEl.assigned_place.empty();
    t.chkout.mdl.frmEl.assigned_place.append(
      new Option(config.translations.Select_Place, ""),
    );
    var company_id = $(this).attr("data-company_id");
    if (typeof t.config.places == "object" && t.config.places.length > 0) {
      $.each(t.config.places, function (i, s) {
        if (company_id == s.company_id) {
          t.chkout.mdl.frmEl.assigned_place.append(new Option(s.text, s.id));
        }
      });
    }
    t.chkout.mdl.frmEl.assigned_place.trigger("change");
    t.chkout.mdl.frmEl.assigned_for.val("1").trigger("change");
    t.chkout.mdl.modal("show");
  };

  t.handleCheckoutSubmit = function (e) {
    e.preventDefault();
    t.chkout.mdl.btnSubmit.prop("disabled", true);
    if (t.chkout.frmValidator.form() == false) {
      t.chkout.mdl.btnSubmit.prop("disabled", false);
      return false;
    }
    if (t.httpCall != true) {
      t.chkout.mdl.btnSubmit.prop("disabled", false);
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.chkout.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
          t.chkout.mdl.modal("hide");
          t.dTbl.ajax.reload();
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
        }
      }
    });
    http.fail(function () {
      // alert(config.translations.something_went_wrong);
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
      t.chkout.mdl.btnSubmit.prop("disabled", false);
    });
  };
  /* ------------- */

  t.loadForm = function (acc, forAction) {
    t.resetFrm();
    if (forAction == "note") {
      t.note.modal("show");
      t.note.noteContent.text($.trim(acc.data.notes));
      return;
    }
    t.mdl.frmEl.id.val(acc.data.id);
    t.mdl.frmEl.unique_tag.val(acc.data.unique_tag);
    if (acc.data.company_id > 0) {
      t.mdl.frmEl.company.val(acc.data.company_id).trigger("change");
    }
    /*if(acc.data.category_id > 0) {
            t.mdl.frmEl.category.val(acc.data.category_id).trigger("change");
        }*/
    if (
      acc.data.purchase_currency != "" &&
      acc.data.purchase_currency != null
    ) {
      t.mdl.frmEl.purchase_currency
        .val(acc.data.purchase_currency)
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.location == "object"
    ) {
      t.mdl.frmEl.location
        .append(
          new Option(acc.dropdown.location.text, acc.dropdown.location.id),
        )
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.internal_place == "object" &&
      acc.dropdown.internal_place != null
    ) {
      t.internal_places.push(acc.dropdown.internal_place.id);
    }
    t.mdl.frmEl.internal_place.trigger("change");

    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.manufacturer == "object"
    ) {
      t.mdl.frmEl.manufacturer.select2("trigger", "select", {
        data: {
          text: acc.dropdown.manufacturer.text,
          id: acc.dropdown.manufacturer.id,
          selected: true,
        },
      });
    }
    if (acc.dropdown.supplier != null) {
      current_supplier_name = acc.dropdown.supplier.text;
      current_supplier_id = acc.dropdown.supplier.id;
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.supplier == "object" &&
      acc.dropdown.supplier != null
    ) {
      t.mdl.frmEl.supplier.select2("trigger", "select", {
        data: {
          text: current_supplier_name,
          id: current_supplier_id,
          selected: true,
        },
      });
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.invoice == "object" &&
      acc.dropdown.invoice != null
    ) {
      t.mdl.frmEl.invoice_id.select2("trigger", "select", {
        data: {
          text: acc.dropdown.invoice.text,
          id: acc.dropdown.invoice.id,
          selected: true,
        },
      });
    }

    t.mdl.frmEl.threshold_alert_users.trigger("change");
    t.mdl.frmEl.threshold_alert_users.empty();
    if (
      typeof acc.dropdown == "object" &&
      Array.isArray(acc.dropdown.thresholdUserEmails) &&
      acc.dropdown.thresholdUserEmails.length > 0
    ) {
      acc.dropdown.thresholdUserEmails.forEach((user) => {
        if (user.text) {
          t.mdl.frmEl.threshold_alert_users.append(
            new Option(user.text, user.id, true, true),
          );
        }
      });
      t.mdl.frmEl.threshold_alert_users.trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.department == "object" &&
      acc.dropdown.department != null
    ) {
      t.mdl.frmEl.department_id
        .append(
          new Option(
            acc.dropdown.department.text,
            acc.dropdown.department.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    if (
      typeof acc.dropdown == "object" &&
      typeof acc.dropdown.accessory == "object" &&
      acc.dropdown.accessory != null
    ) {
      t.mdl.frmEl.category.attr("data-noLoadField", true);
      t.mdl.frmEl.category.append(
        new Option(
          acc.dropdown.accessory.text,
          acc.dropdown.accessory.id,
          true,
          true,
        ),
      );
      t.mdl.frmEl.category.attr("data-noLoadField", false);
      t.mdl.frmEl.category.trigger("change");
    }

    t.clearCustomFields();
    if (
      typeof acc.custom_fields == "object" &&
      typeof acc.custom_fields.html != ""
    ) {
      t.fillCustomFields(acc.custom_fields);
    }
    if (acc.data.requestable_accessory == 1) {
      t.mdl.frm
        .find("input[name='requestable_accessory']")
        .prop("checked", true);
    } else {
      t.mdl.frm
        .find("input[name='requestable_accessory']")
        .prop("checked", false);
    }
    if (acc.data.thresholds_alerts == 1) {
      t.mdl.frm.find("input[name='thresholds_alerts']").prop("checked", true);
    } else {
      t.mdl.frm.find("input[name='thresholds_alerts']").prop("checked", false);
    }
    t.mdl.frm
      .find("input[name='accessory_thresholds']")
      .val(acc.data.accessory_thresholds);
    t.mdl.frm.find("input[name='name']").val(acc.data.name);
    t.mdl.frm.find("input[name='qty']").val(acc.data.qty);
    current_purchase_date = acc.data.purchase_date;
    order_no = acc.data.order_number;
    purchase_cost = acc.data.purchase_cost;
    currency_default = acc.data.purchase_currency;
    t.mdl.frm.find("input[name='purchase_cost']").val(acc.data.purchase_cost);
    t.mdl.frm.find("input[name='order_number']").val(order_no);
    t.mdl.frmEl.purchase_currency.val(currency_default).trigger("change");
    t.mdl.frmEl.purchase_date.datepicker("update", current_purchase_date);
    t.mdl.frmEl.notes.val($.trim(acc.data.notes));
    t.mdl.frmEl.reorder_limits.val(acc.data.reorder_limits);
    t.loadImageViewer(acc.data.image);
    t.mdl.modal("show");
  };

  t.loadImageViewer = function (img) {
    if (
      img != "" &&
      img != null &&
      img != "null" &&
      (t.forAction == "edit" || t.forAction == "clone")
    ) {
      t.mdl.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + img);
      t.mdl.frmEl.imgviewcover.removeClass("hide");
      if (t.forAction == "clone") {
        t.mdl.frmEl.clone_img.val(img);
        t.mdl.frmEl.foraction.val("clone");
      }
    } else {
      t.mdl.frmEl.imgview.attr("src", "");
      t.mdl.frmEl.imgviewcover.addClass("hide");
    }
  };

  t.addAccessory = function (e) {
    e.preventDefault();
    t.resetFrm();
    t.httpPostPath = t.config.url.add;
    t.mdl.title.html(t.config.translations.add_new_accessory);
    t.mdl.btnSubmit.text(t.config.translations.save);
    t.clearCustomFields();
    if (
      typeof t.config.custom_fields == "object" &&
      typeof t.config.custom_fields.html != ""
    ) {
      t.fillCustomFields(t.config.custom_fields);
    }
    t.forAction = "add";
    t.mdl.frmEl.id.val(null);
    t.mdl.frmEl.threshold_alert_users.empty().trigger("change");
    t.mdl.frm.find("input[name='qty']").attr("readonly", false);
    t.mdl.frm.find("input[name='unique_tag']").attr("readonly", false);
    t.mdl.modal("show");
    t.mdl.frmEl.imgviewcover.addClass("hide");
  };

  t.fillCustomFields = function (data) {
    if (typeof data.html == "undefined") {
      return;
    }
    t.clearCustomFields();
    t.mdl.frmEl.customFieldsPrvEl.after(data.html);
    if (data.required_fields.length > 0) {
      $.each(data.required_fields, function (i, d) {
        t.mdl.frm.find("#" + d).rules("add", { required: true });
      });
    }
    $.each(data.all_fields, function (i, d) {
      t.mdl.frm.find("#" + d).rules("add", { remarks: false });
    });
    $(".cf-select2").each(function () {
      var $field = $(this);
      var fieldId = $field.attr("id");

      if (fieldId) {
        $field.select2(
          $.extend({}, select2Opts, { dropdownParent: $field.parent() }),
        );
        if ($field.hasClass("custFieldUser")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getUser",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_User,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
              templateResult: function (data) {
                if (typeof data.loading != "undefined" && data.loading) {
                  return $("<div>" + data.text + "</div>");
                }
                var email = data.email == null ? "" : data.email;
                var a = "";
                a += "<div class='row'>";
                a += "<div class='col-sm-10'>";
                if (data.displayName != null && data.displayName != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-user\"></i>" +
                    data.displayName;
                } else {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-user\"></i>" +
                    data.first_name +
                    " " +
                    data.last_name;
                }
                var active = "inactive-user";
                if (data.user_status == "Active") {
                  active = "active-user";
                }
                a += "<span class='" + active + "'></span>";
                a += "</div>";
                if (data.email != null && data.email != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" +
                    data.email +
                    "</div>";
                }
                if (data.employee_num != null && data.employee_num != "") {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" +
                    data.employee_num +
                    "</div>";
                }
                a += "</div>";
                a += "<div class='col-sm-2'>";
                a +=
                  "<div><img class='img-u' src= '" +
                  data.img_path +
                  "'/></div>";
                a += "</div>";
                a += "</div>";
                return $("<div>" + a + "</div>");
              },
            }),
          );
        }

        if ($field.hasClass("custFieldLocation")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getLocation",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Location,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldDevice")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getDevice",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Device,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
              templateResult: function (data, container) {
                if (typeof data.loading != "undefined" && data.loading) {
                  return $("<div>" + data.text + "</div>");
                }
                a =
                  "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
                  data.asset_tag +
                  "</div>";
                if (data.asset_name != null) {
                  a +=
                    "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
                    data.asset_name +
                    "</div>";
                }
                a +=
                  "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
                  data.name +
                  " " +
                  data.modelno +
                  "</div>";
                a +=
                  "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
                  data.serial +
                  "</div>";
                return $("<div>" + a + "</div>");
              },
            }),
          );
        }

        if ($field.hasClass("custFieldModels")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getModel",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_model,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldSupplier")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getSupplier",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Supplier,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldProjects")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getProject",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_project,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldInternalPlace")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.ajaxGetInternalPlace,
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_internal_place,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldContract")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                // url: t.config.getLeaseByAjax,
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getContract",
                dataType: "json",
                data: function (p) {
                  return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                      return t.mdl.frmEl.company.val();
                    },
                  };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_contract,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldComponent")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getComponent",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_component,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldLicense")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getLicense",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_license,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldTasks")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getTask",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_task,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldChangeManagement")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getRecord",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_change_management,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldManufacturers")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery +
                  "/" +
                  "getManufacture",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_manufacturer,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldTickets")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.getPredefinedDropdownByQuery + "/" + "getTicket",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.select_the_ticket,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldRequest")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery +
                  "/" +
                  "getTicketProcureRequest",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder:
                config.translations.select_the_ticket_procure_request,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldPurchase")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url:
                  t.config.getPredefinedDropdownByQuery + "/" + "getPurchase",
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Purchase_Invoice,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }

        if ($field.hasClass("custFieldDept")) {
          $field.select2(
            $.extend({}, select2Opts, {
              dropdownParent: $field.parent(),
              ajax: {
                url: t.config.url.getAssetDepartments,
                dataType: "json",
                data: function (p) {
                  return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
              },
              allowClear: true,
              placeholder: config.translations.Select_the_Department,
              templateSelection: function (data, container) {
                $(container).attr("title", data.text);
                return data.text.length > 50
                  ? data.text.substring(0, 50) + "..."
                  : data.text;
              },
            }),
          );
        }
      } else {
        console.warn(
          "Select2 field without ID found. Skipping initialization.",
        );
      }
    });
  };

  t.clearCustomFields = function () {
    t.mdl.find(".custom-field-row").remove();
  };

  t.getCustomFields = function (e) {
    e.preventDefault();
    if (t.mdl.frmEl.category.attr("data-noLoadField") == "true") {
      return;
    }

    t.clearCustomFields();
    if (
      typeof t.config.custom_fields == "object" &&
      typeof t.config.custom_fields.html != ""
    ) {
      t.fillCustomFields(t.config.custom_fields);
    }
    var category_id = parseInt(t.mdl.frmEl.category.val());
    if (category_id < 1 || isNaN(category_id)) {
      return;
    }
    var type_id = t.mdl.frmEl.id.val();
    var http = $.get(
      t.config.getCustomFieldsByCategory +
      "/" +
      category_id +
      "/" +
      "accessory" +
      "/" +
      type_id,
    );
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.fillCustomFields(data);
        }
      }
    });
    http.fail(function () {
      alert(config.translations.something_went_wrong);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.editAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.edit + "/" + accId;
    var http = $.get(t.config.url.get + "/" + accId);
    t.forAction = "edit";
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(t.config.translations.edit_accessory);
          t.mdl.btnSubmit.text(t.config.translations.save);
          // if (typeof t.config.custom_fields == "object" && typeof t.config.custom_fields.html != "") {
          //     t.fillCustomFields(t.config.custom_fields);
          // }
          t.mdl.frm.find("input[name='qty']").attr("readonly", true);
          t.mdl.frm.find("input[name='unique_tag']").attr("readonly", true);
          t.loadForm(data.accessory, "edit");
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
        }
      }
    });
    http.fail(function () {
      // alert(config.translations.something_went_wrong);
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.noteAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    var http = $.get(t.config.url.get + "/" + accId);
    t.forAction = "note";
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.loadForm(data.accessory, "note");
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.cloneAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.add;
    var http = $.get(t.config.url.get + "/" + accId);
    t.forAction = "clone";
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(config.translations.Clone_Accessory);
          t.mdl.btnSubmit.text(t.config.translations.Clone_Accessory);
          t.mdl.frm.find("input[name='qty']").attr("readonly", false);
          t.mdl.frm.find("input[name='unique_tag']").attr("readonly", false);
          t.loadForm(data.accessory, "clone");
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
        }
      }
    });
    http.fail(function () {
      // alert(config.translations.something_went_wrong);
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };

  t.deleteAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.delete + "/" + accId;
    var data = {
      msg: config.translations.something_went_wrong,
    };
    sweetAlerts(
      config.translations.are_you_want,
      "warning",
      t.httpPostPath,
      t.dTbl,
      data,
    );
    // vex.dialog.confirm({
    //     message: config.translations.are_you_want,
    //     callback: function (value) {
    //         if(value == true) {
    //             var http = $.get(t.httpPostPath);
    //             http.done(function(data) {
    //                 if(typeof data == "object") {
    //                     if(data.status == "success") {
    //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>'});
    //                         t.dTbl.ajax.reload();
    //                     }
    //                     else {
    //                         vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
    //                     }
    //                 }
    //             });
    //             http.fail(function() {
    //                 alert(config.translations.something_went_wrong);
    //             });
    //             http.always(function() {
    //                 t.httpCall = true;
    //             });
    //         }
    //     }
    // });
  };

  t.restoreAccessory = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.restore + "/" + accId;
    var data = {
      msg: config.translations.something_went_wrong,
    };
    sweetAlerts(
      config.translations.are_you_restore,
      "warning",
      t.httpPostPath,
      t.dTbl,
      data,
    );
  };

  t.checkUrlHash = function () {
    var hashVal = window.location.hash || "0";
    var val = hashVal.search("#") !== -1 ? hashVal.replace("#", "") : hashVal;
    t.tblStatusLbl
      .find("option[value='" + val + "']")
      .attr("selected", "selected");
  };

  t.chooseStatus = function () {
    var val = t.tblStatusLbl.val();
    window.location.hash = val > 0 ? val : "";
    t.dTbl.ajax.reload();
  };

  t.reload = function () {
    $(".user-list-search").val("");
    t.dTbl.search("").draw();
    t.dTbl.ajax.reload(null, true);
  };

  function cb(start, end) {
    $("#reportrange span").html(
      start.format("DD-MM-YYYY HH:mm:ss") +
      " - " +
      end.format("DD-MM-YYYY HH:mm:ss"),
    );
    $("#daterange").val(
      start.format("YYYY-MM-DD HH:mm:ss") +
      " - " +
      end.format("YYYY-MM-DD HH:mm:ss"),
    );
  }

  t.cache_filter_values = function () {
    var v = $("#mytable_wrapper .plain-search").validate_str_param();
    t.config.search = v;
    t.config.other_filters = {};
    t.config.dashboard_filters = {};
    t.config.dashboard_filters.location = t.config.location_filter;
    t.config.dashboard_filters.department = t.config.department_filter;
    t.config.dashboard_filters.cat_id = t.config.cat_id_filter;
    t.config.dashboard_filters.type = t.config.type;
    if (t.filters.categories.val() && t.filters.categories.val() != "null")
      t.config.other_filters.categories = t.filters.categories.val();
    if (
      t.filters.purchase_reference.val() &&
      t.filters.purchase_reference.val() != "null"
    )
      t.config.other_filters.purchase_reference =
        t.filters.purchase_reference.val();
    if (t.filters.location.val() && t.filters.location.val() != "null")
      t.config.other_filters.location = t.filters.location.val();
    if (t.filters.based_on.val() && t.filters.based_on.val() != "null")
      t.config.other_filters.based_on = t.filters.based_on.val();
    if (
      t.filters.daterange.val() &&
      t.filters.daterange.val() != "null" &&
      t.filters.based_on.val() &&
      t.filters.based_on.val() != "null"
    )
      t.config.other_filters.date_range = t.filters.daterange.val();
    if (
      t.filters.assetDepartment.val() &&
      t.filters.assetDepartment.val() != "null"
    )
      t.config.other_filters.asset_department = t.filters.assetDepartment.val();
    if (t.filters.assigned_for.val() && t.filters.assigned_for.val() != "null")
      t.config.other_filters.assigned_for = t.filters.assigned_for.val();
    if (
      t.filters.assigned_for.val() == "1" &&
      t.filters.assigned_user.val() &&
      t.filters.assigned_user.val() != "null"
    )
      t.config.other_filters.assigned_user = t.filters.assigned_user.val();
    if (
      t.filters.assigned_for.val() == "2" &&
      t.filters.filter_by_device_assigned.val() &&
      t.filters.filter_by_device_assigned.val() != "null"
    )
      t.config.other_filters.device_assigned =
        t.filters.filter_by_device_assigned.val();
    if (
      t.filters.assigned_for.val() == "3" &&
      t.filters.filter_by_assigned_place.val() &&
      t.filters.filter_by_assigned_place.val() != "null"
    )
      t.config.other_filters.assigned_place =
        t.filters.filter_by_assigned_place.val();
    if (
      t.filters.available_accessories.val() &&
      t.filters.available_accessories.val() != "null"
    )
      t.config.other_filters.available_accessories =
        t.filters.available_accessories.val();
    if (
      t.filters.internal_place.val() &&
      t.filters.internal_place.val() != "null"
    )
      t.config.other_filters.internal_place = t.filters.internal_place.val();
    var jobj = {
      search: t.config.search,
      other_filters: t.config.other_filters,
      dashboard_filters: t.config.dashboard_filters,
      showDeletedAccessories: t.showDeletedAccessories,
      q: t.config.accessoryfilter,
    };
    t.config.export_filters = btoa(JSON.stringify(jobj));
    filterCount(t.config.other_filters, false);
  };

  t.tableSearch = function (e) {
    e.preventDefault();
    var v = $("#mytable_wrapper .plain-search").validate_str_param();
    if (v === false) {
      alert(config.translations.please_enter_valid_search);
      return false;
    }
    t.dTbl.search(v).draw();
  };

  // reset options for purchase currency
  t.mdl.optionsCurrency = function () {
    t.mdl.frmEl.purchase_currency
      .empty()
      .append(new Option(config.translations.Select_Currency_Format, ""));
    $.each(t.config.currencies, function (i, v) {
      var opt =
        t.config.default_currency_format == i
          ? new Option("", i, true, true)
          : new Option("", i);
      opt.innerHTML = v.name + " (" + v.symbol_html + ")";
      t.mdl.frmEl.purchase_currency.append(opt);
    });
    t.mdl.frmEl.purchase_currency.trigger("change");
  };

  t.resetFrm = function () {
    t.mdl.frm.trigger("reset");
    t.frmValidator.resetForm();
    t.mdl.frm.find(".amg-form-field").removeClass("error");
    t.mdl.frmEl.foraction.val("");

    t.mdl.frm.find(".input-group").removeClass("amg-form-invalid");

    // clear all error messages
    t.mdl.frm.find(".amg-form-error-wrap").empty();

    // reset select2 error ui
    t.mdl.frm.find(".select2-selection").removeClass("amg-form-select-error");
    t.mdl.frmEl.location.empty().trigger("change");
    t.mdl.frmEl.manufacturer
      .empty()
      .append(new Option(config.translations.Select_the_manufacturer, ""));
    t.mdl.frmEl.company.val(0).trigger("change");
    t.mdl.frmEl.category.val(0).trigger("change");
    t.mdl.frmEl.department_id.empty().trigger("change");
    t.mdl.frmEl.supplier
      .empty()
      .append(new Option(config.translations.Select_the_Supplier, ""));
    t.mdl.frmEl.invoice_id
      .empty()
      .append(new Option(config.translations.Select_the_Purchase_Invoice, ""));
    t.mdl.optionsCurrency();
    t.getInternalPlace();
  };

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    rules: {
      unique_tag: {
        clean_text_only: true,
      },
      company_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
        str_name: true,
        clean_text_only: true,
      },
      name: {
        required: true,
        str_name: true,
        clean_text_only: true,
        maxlength: 50,
      },
      category_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
        str_name: true,
      },
      qty: {
        required: true,
        digits: true,
        min: 1,
        remarks: true,
      },
      location_id: {
        required: true,
        digits: true,
        nonZeroInteger: true,
      },
      purchase_cost: {
        number: true,
      },
      notes: {
        remarks: true,
        clean_text_only: true,
      },
      order_number: {
        str_name: false,
        clean_text_only: true,
      },
      accessory_thresholds: {
        required: true,
        digits: true,
        min: 0,
      },
      note: {
        remarks: true,
      },
      image: {
        accept: "image/jpeg, image/jpg, image/png",
        extension: "jpg|jpeg|png",
        filesize: 2048000,
      },
    },
    messages: {
      image: {
        required: "Please select an image.",
        accept: "Only JPEG, JPG, and PNG images are allowed.",
        extension: "Only JPEG, JPG, and PNG images are allowed.",
        filesize: "Image must be less than 2 MB.",
      },
    },
    errorPlacement: function (error, element) {
      element
        .closest(".amg-form-field")
        .find(".amg-form-error-wrap")
        .html(error);
    },

    highlight: function (element) {
      let $element = $(element);
      let $field = $element.closest(".amg-form-field");
      let $inputGroup = $field.find(".input-group");

      $field.addClass("error");
      $inputGroup.addClass("amg-form-invalid");

      // select2 error ui
      if ($element.next(".select2").length) {
        $element
          .next(".select2")
          .find(".select2-selection")
          .addClass("amg-form-select-error");
      }
    },

    unhighlight: function (element) {
      let $element = $(element);
      let $field = $element.closest(".amg-form-field");
      let $inputGroup = $field.find(".input-group");

      // remove normal error
      $field.removeClass("error");
      $inputGroup.removeClass("amg-form-invalid");

      // remove select2 error
      if ($element.next(".select2").length) {
        $element
          .next(".select2")
          .find(".select2-selection")
          .removeClass("amg-form-select-error");
      }

      // clear message
      $field.find(".amg-form-error-wrap").empty();
    },

    success: function (label, element) {
      let $element = $(element);
      let $field = $element.closest(".amg-form-field");
      $field.removeClass("error");
      $field.find(".input-group").removeClass("amg-form-invalid");
      if ($element.next(".select2").length) {
        $element
          .next(".select2")
          .find(".select2-selection")
          .removeClass("amg-form-select-error");
      }
      $field.find(".amg-form-error-wrap").empty();
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

  t.mdl.frm.on("select2:select select2:unselect", "select", function () {
    t.frmValidator.element(this);
  });

  $.validator.addMethod(
    "filesize",
    function (value, element, param) {
      return this.optional(element) || element.files[0].size <= param;
    },
    "File size must be less than or equal to {0} bytes.",
  );

  // function checkThresholdValidation() {
  //     var thresholdValue = t.mdl.frmEl.accessory_thresholds.val();
  //     var thresholdAlertValue = t.mdl.frmEl.threshold_alert_users.val();
  //     if((thresholdValue > 0 && thresholdValue != null) && thresholdAlertValue === null){
  //         $("#threshold_alert_users").rules("add", {
  //             required: true,
  //             messages: { required: "This field is required when thresholds are set." }
  //         });
  //     } else{
  //         $("#threshold_alert_users").rules("remove", "required");
  //     }
  //     t.frmValidator.element("#threshold_alert_users");
  // }

  // $("#accessory_thresholds").on("input", function () {
  //     checkThresholdValidation();
  // });

  // $("#btnSubmit").on("click", function (e) {
  //     checkThresholdValidation();

  //     if (!t.mdl.frm.valid()) {
  //         e.preventDefault();
  //     }
  // });

  t.handleSubmit = function (e) {
    e.preventDefault();

    if (t.frmValidator.form() == false) {
      return false;
    }

    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    t.mdl.btnSubmit.prop("disabled", true);
    // var http = $.post(t.httpPostPath, t.mdl.frm.serialize());
    var formData = new FormData(t.mdl.frm[0]);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
          t.mdl.modal("hide");
          t.dTbl.ajax.reload();
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      // alert(config.translations.something_went_wrong);
    });
    http.always(function () {
      t.httpCall = true;
      t.mdl.btnSubmit.prop("disabled", false);
    });
  };

  t.switchCheckTarget = function () {
    if (t.filters.assigned_for.val() == "3") {
      t.filters.filter_by_assigned_place.closest(".cover").show();
      t.filters.assigned_user.closest(".cover").hide();
      t.filters.filter_by_device_assigned.closest(".cover").hide();
      t.filters.filter_by_assigned_place.rules("add", { required: true });
      t.filters.assigned_user.rules("remove", "required");
      t.filters.filter_by_device_assigned.rules("remove", "required");
    } else if (t.filters.assigned_for.val() == "2") {
      t.filters.filter_by_device_assigned.closest(".cover").show();
      t.filters.filter_by_assigned_place.closest(".cover").hide();
      t.filters.assigned_user.closest(".cover").hide();
      t.filters.filter_by_device_assigned.rules("add", { required: true });
      t.filters.assigned_user.rules("remove", "required");
      t.filters.filter_by_assigned_place.rules("remove", "required");
    } else if (t.filters.assigned_for.val() == "1") {
      t.filters.filter_by_assigned_place.closest(".cover").hide();
      t.filters.filter_by_device_assigned.closest(".cover").hide();
      t.filters.assigned_user.closest(".cover").show();
      t.filters.filter_by_assigned_place.rules("remove", "required");
      t.filters.filter_by_device_assigned.rules("remove", "required");
      t.filters.assigned_user.rules("add", { required: true });
    } else {
      t.filters.filter_by_assigned_place.closest(".cover").hide();
      t.filters.filter_by_device_assigned.closest(".cover").hide();
      t.filters.assigned_user.closest(".cover").hide();
    }
  };

  var select2Opts = { width: "100%" };

  t.config.companies.unshift({
    id: 0,
    text: config.translations.Select_Company,
  });
  t.mdl.frmEl.company.select2(
    $.extend({}, select2Opts, {
      data: t.config.companies,
      dropdownParent: t.mdl.frmEl.company.parent(),
    }),
  );
  // t.mdl.frmEl.company
  //     .select2(
  //         $.extend({}, select2Opts, {
  //             data: t.config.companies,
  //             dropdownParent: t.mdl.frmEl.company.parent(),
  //         }),
  //     )
  //     .on("change", function () {

  //         let $field = $(this).closest(".amg-form-field");

  //         $(this).valid();
  //         $field.removeClass("error");
  //         $(this)
  //             .next(".select2")
  //             .find(".select2-selection")
  //             .removeClass("error");
  //         $field.find(".amg-form-error-wrap").empty();

  //     });
  t.filters.categories.select2(
    $.extend({}, select2Opts, { placeholder: config.translations.No_Filter }),
  );
  t.mdl.frmEl.category.select2(
    $.extend({}, select2Opts, {
      data: t.config.categories,
      placeholder: config.translations.Select_Category,
      dropdownParent: t.mdl.frmEl.category.parent(),
    }),
  );
  t.mdl.frmEl.purchase_currency.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.purchase_currency.parent(),
    }),
  );
  t.filters.location.select2(
    $.extend({}, select2Opts, { placeholder: config.translations.No_Filter }),
  );
  t.filters.internal_place.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.filter_by_internal_place,
    }),
  );
  t.filters.based_on.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.Filter_Based_on,
    }),
  );
  t.filters.available_accessories.select2(
    $.extend({}, select2Opts, { placeholder: config.translations.No_Filter }),
  );
  t.filters.assigned_for.select2(
    $.extend({}, select2Opts, { placeholder: config.translations.No_Filter }),
  );
  t.filters.assetDepartment.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.filters.assetDepartment.parent(),
      ajax: {
        url: t.config.url.getAssetDepartments,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: config.translations.No_Filter,
    }),
  );

  t.filters.assigned_user.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.filters.assigned_user.parent(),
      ajax: {
        url: t.config.getUserByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: t.config.translations.Select_the_User,
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = "";
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a +=
          "<div class='so-t'><i class=\"fa fa-user\"></i>" +
          s.first_name +
          " " +
          s.last_name;
        if (s.user_status == "Active") {
          a += "<span class='active-user'></span>";
        } else {
          a += "<span class='inactive-user'></span>";
        }
        a += "</div>";
        if (s.email != null && s.email != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" +
            s.email +
            "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" +
            s.employee_num +
            "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 30
          ? data.text.substring(0, 30) + "..."
          : data.text;
      },
    }),
  );

  t.mdl.frmEl.threshold_alert_users.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.threshold_alert_users.parent(),
      ajax: {
        url: config.url.getActivatedUsers,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: function () {
              return t.mdl.frmEl.company.val();
            },
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: t.config.translations.Select_the_User,
      templateResult: function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        var email = s.email == null ? "" : s.email;
        var a = "";
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        var truncatedText =
          s.text.length > 20 ? s.text.substring(0, 20) + "..." : s.text;
        a +=
          "<div class='so-t' title='" +
          s.text +
          "'><i class='fa fa-user' style='padding-right: 3px;'></i>" +
          truncatedText +
          " ";
        // a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
          var truncatedEmail =
            s.email.length > 20 ? s.email.substring(0, 20) + "..." : s.email;
          a +=
            "<div class='so-t' title='" +
            s.email +
            "'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" +
            truncatedEmail +
            "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
          var truncatedText =
            s.employee_num.length > 30
              ? s.employee_num.substring(0, 30) + "..."
              : s.employee_num;
          a +=
            "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" +
            truncatedText +
            "</div>";
        }
        a += "</div>";
        a += "<div style='margin-left: 10px;'>";
        a +=
          "<div><img class='img-u' style='margin-left: 0; padding-left: 0; float: left;' src='" +
          s.img_path +
          "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
      },
    }),
  );

  t.filters.filter_by_assigned_place.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.filters.filter_by_assigned_place.parent(),
      ajax: {
        url: config.ajaxGetInternalPlace,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: "Select the Place",
    }),
  );

  t.filters.filter_by_device_assigned.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.filters.filter_by_device_assigned.parent(),
      ajax: {
        url: t.config.getDeviceForDropDown,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: "Select the Device",
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        a =
          "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
          s.asset_tag +
          "</div>";
        if (s.asset_name != null) {
          a +=
            "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
            s.asset_name +
            "</div>";
        }
        a +=
          "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
          s.name +
          " " +
          s.modelno +
          "</div>";
        a +=
          "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
          s.serial +
          "</div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 50
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );

  t.filters.location.on("change", function () {
    t.filters.internal_place.empty();
    var location_id = $(this).val();
    if (location_id == null) {
      t.filters.fun.reload_internal_place();
    } else {
      $.get(t.config.getInternalPlaceByAjax + "/" + location_id).done(
        function (data) {
          if (typeof data == "object" && data.results.length) {
            $.each(data.results, function (i, s) {
              t.filters.internal_place.append(new Option(s.text, s.id));
            });
          }
          t.filters.internal_place.trigger("change");
        },
      );
    }
  });

  // Location Dropdown Initialization
  t.mdl.frmEl.location
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.location.parent(),
        ajax: {
          url: t.config.getLocationByAjax,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300,
        },
        allowClear: true,
        placeholder: config.translations.Select_the_Location,
        templateSelection: function (data, container) {
          if (container) {
            $(container).attr("title", data.text);
          }
          return data.text.length > 55
            ? data.text.substring(0, 55) + "..."
            : data.text;
        },
      }),
    )
    .on("change", function (e) {
      t.getInternalPlace();
    });

  t.mdl.frmEl.internal_place.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.select_the_internal_place,
      allowClear: true,
      dropdownParent: t.mdl.frmEl.internal_place.parent(),
    }),
  );

  t.getInternalPlace = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }

    t.mdl.frmEl.internal_place
      .empty()
      .append(
        new Option(config.translations.select_internal_place, "", true, true),
      );
    t.mdl.frmEl.internal_place.trigger("change");

    var location_id = t.mdl.frmEl.location.val();

    if (location_id > 0) {
      $.get(t.config.getInternalPlaceByAjax + "/" + location_id)
        .done(function (data) {
          if (typeof data == "object" && data.results.length) {
            $.each(data.results, function (i, v) {
              if ($.inArray(v.id, t.internal_places) !== -1) {
                t.mdl.frmEl.internal_place.append(
                  new Option(v.text, v.id, true, true),
                );
              } else {
                t.mdl.frmEl.internal_place.append(
                  new Option(v.text, v.id, false, false),
                );
              }
            });
          }
        })
        .always(function () {
          t.mdl.frmEl.internal_place.trigger("change");
          t.internal_places = [];
        });
    }
  };

  t.mdl.frmEl.invoice_id
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.invoice_id.parent(),
        ajax: {
          url: t.config.getInvoiceByAjax,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 300,
        },
        allowClear: true,
        //minimumInputLength: 1,
        placeholder: config.translations.Select_the_Purchase_Invoice,
        templateSelection: function (s, container) {
          if (typeof s.loading != "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
          }
          $(s.element).attr({
            "data-invoice_date": s.invoice_date,
            "data-name": s.supplier_name,
            "data-id": s.supplier_id,
            "data-order-number": s.order_number,
            "data-po-number": s.po_number,
            "data-purchase-cost": s.purchase_cost,
            "data-currency": s.currency,
          });
          return s.text;
        },
      }),
    )
    .on("change", function (e) {
      if (!t.mdl.frmEl.invoice_id.val()) {
        if (current_purchase_date != null) {
          purchasedate = current_purchase_date.split("-").join("/");
          t.mdl.frmEl.purchase_date.val(purchasedate);
        } else {
          t.mdl.frmEl.purchase_date.val(null);
        }
        if (current_supplier_name != "" && current_supplier_id != "") {
          t.mdl.frmEl.supplier
            .append(
              new Option(
                current_supplier_name,
                current_supplier_id,
                true,
                true,
              ),
            )
            .trigger("change");
        } else {
          t.mdl.frmEl.supplier.empty("").trigger("change");
        }
        if (order_no != null && order_no != "") {
          t.mdl.frm.find("input[name='order_number']").val(order_no);
        } else {
          t.mdl.frm.find("input[name='order_number']").val("");
        }
        if (purchase_cost != null && purchase_cost != null) {
          t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost);
        } else {
          t.mdl.frm.find("input[name='purchase_cost']").val("");
        }
      } else {
        t.getPurchaseDate();
      }
    });

  t.getPurchaseDate = function (e) {
    var selectedOption = t.mdl.frmEl.invoice_id.find(":selected");
    if (!selectedOption.attr("data-invoice_date")) {
      if (current_purchase_date != null) {
        purchasedate = current_purchase_date.split("-").join("/");
        t.mdl.frmEl.purchase_date.val(purchasedate);
      } else {
        t.mdl.frmEl.purchase_date.val("");
      }
      t.mdl.frmEl.supplier.empty();
      if (current_supplier_name && current_supplier_id) {
        t.mdl.frmEl.supplier
          .append(
            new Option(current_supplier_name, current_supplier_id, true, true),
          )
          .trigger("change");
      }
      if (order_no != null) {
        t.mdl.frm.find("input[name='order_number']").val(order_no);
      } else {
        t.mdl.frm.find("input[name='order_number']").val("");
      }
      if (purchase_cost != null) {
        t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost);
      } else {
        t.mdl.frm.find("input[name='purchase_cost']").val("");
      }
      if (currency_default != null) {
        t.mdl.frmEl.purchase_currency.val(currency_default).trigger("change");
      } else {
        t.mdl.frmEl.purchase_currency.val("").trigger("change");
      }
      return;
    }
    //end this part is added to handle re-selection from Select2 dropdown
    var date = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-invoice_date");
    t.mdl.frmEl.purchase_date.val(date);
    var supplier_name = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-name");
    var supplier_id = t.mdl.frmEl.invoice_id.find(":selected").attr("data-id");
    t.mdl.frmEl.supplier
      .append(new Option(supplier_name, supplier_id, true, true))
      .trigger("change");
    if (
      supplier_name == undefined &&
      supplier_id == undefined &&
      t.forAction == "edit"
    ) {
      t.mdl.frmEl.supplier
        .append(
          new Option(current_supplier_name, current_supplier_id, true, true),
        )
        .trigger("change");
    }
    var order_number = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-po-number");
    t.mdl.frm.find("input[name='order_number']").val(order_number);
    var purchase_cost = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-purchase-cost");
    t.mdl.frm.find("input[name='purchase_cost']").val(purchase_cost || "0.00");
    var purchase_currency = t.mdl.frmEl.invoice_id
      .find(":selected")
      .attr("data-currency");
    t.mdl.frmEl.purchase_currency.val(purchase_currency).trigger("change");
  };

  t.mdl.frmEl.department_id.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.department_id.parent(),
      ajax: {
        url: t.config.url.getAssetDepartments,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: t.config.translations.select_the_department,
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 55
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );
  t.mdl.frmEl.manufacturer.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.manufacturer.parent(),
      ajax: {
        url: t.config.getManufacturerByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      placeholder: config.translations.Select_the_manufacturer,
    }),
  );
  t.chkout.mdl.frmEl.assigned_to.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.assigned_to.parent(),
      ajax: {
        url: t.config.getUserByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: config.translations.Select_the_User,
      templateResult: function (s) {
        // console.log(s);
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = "";
        // a = "<div class='row'><div class='col-sm-10'><div class='so-t'><i class=\"fa fa-user\"></i> " + s.first_name + " " + s.last_name + "</div><div class='so-t'><i class=\"fa fa-envelope-o\"></i> " + email + "</div><div class='so-t'><i class=\"fa fa-credit-card\"></i> " +s.employee_num + "</div> </div><div class='col-sm-2'><div><img class='img-u' src= '"  + s.img_path  + "'/></div></div></div>";
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        // a += "<div class='so-t'><i class=\"fa fa-user\"></i>"  + s.first_name + " " + s.last_name;
        if (s.displayName != null && s.displayName != "") {
          a += "<div class='so-t'><i class=\"fa fa-user\"></i>" + s.displayName;
        } else {
          a +=
            "<div class='so-t'><i class=\"fa fa-user\"></i>" +
            s.first_name +
            " " +
            s.last_name;
        }
        a += "<span class='active-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-envelope-o\"></i>" +
            s.email +
            "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
          a +=
            "<div class='so-t'><i class=\"fa fa-credit-card\"></i>" +
            s.employee_num +
            "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 30
          ? data.text.substring(0, 30) + "..."
          : data.text;
      },
    }),
  );

  t.chkout.mdl.frmEl.device_id.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.device_id.parent(),
      ajax: {
        url: t.config.getDeviceForCheckoutDropDown,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: config.translations.Select_the_Device,
      templateResult: function (s) {
        if (typeof s.loading != "undefined" && s.loading) {
          return $("<div>" + s.text + "</div>");
        }
        a =
          "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
          s.asset_tag +
          "</div>";
        if (s.asset_name != null) {
          a +=
            "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
            s.asset_name +
            "</div>";
        }
        a +=
          "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
          s.name +
          " " +
          s.modelno +
          "</div>";
        a +=
          "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
          s.serial +
          "</div>";
        return $("<div>" + a + "</div>");
      },
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 50
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );
  t.mdl.frmEl.supplier.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.supplier.parent(),
      ajax: {
        url: t.config.getSupplierByAjax,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
        delay: 300,
      },
      allowClear: true,
      //minimumInputLength: 1,
      placeholder: config.translations.Select_the_Supplier,
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 50
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );

  t.chkout.mdl.frmEl.assigned_for.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.assigned_for.parent(),
      data: t.config.assignedForOptions,
    }),
  );

  t.chkout.mdl.frmEl.assigned_place.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.chkout.mdl.frmEl.assigned_place.parent(),
      templateSelection: function (data, container) {
        if (container) {
          $(container).attr("title", data.text);
        }
        return data.text.length > 50
          ? data.text.substring(0, 55) + "..."
          : data.text;
      },
    }),
  );

  t.mdl.frmEl.purchase_date.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
    container: $("#accessory-mdl"),
  });
  t.chkout.mdl.frmEl.expected_checkin.datepicker({
    autoclose: true,
    format: "dd/mm/yyyy",
    container: $("#accessory-checkout-mdl"),
  });
  t.chkout.mdl.frmEl.assigned_for.on(
    "change",
    $.proxy(t.chkout.switchCheckTarget),
  );

  t.search = function () {
    let value = $(".user-list-search").val().trim();
    t.dTbl.search(value).draw();
  };

  t.btn.clear.click(function () {
    t.config.other_filters = {};
    t.filters.location.val(0).trigger("change");
    t.filters.assigned_for.val("null").trigger("change");
    t.filters.filter_by_assigned_place.val(0).trigger("change");
    t.filters.filter_by_device_assigned.val(0).trigger("change");
    t.filters.assigned_user.val(0).trigger("change");
    t.filters.available_accessories.val("null").trigger("change");
    t.filters.categories.val(0).trigger("change");
    t.filters.purchase_reference.val("null").trigger("change");
    t.filters.based_on.val("null").trigger("change");
    t.filters.assetDepartment.empty("").trigger("change");
    // resetDateRangeFilter();
    resetFilterCount();
    // $('#advance-filters').collapse('hide');
    t.cache_filter_values();
  });

  t.toggleDeletedAccessory = function () {
    t.showDeletedAccessories = t.showDeletedAccessories == false ? true : false;
    var lblTxt =
      t.showDeletedAccessories == false
        ? '<i class="ps-icon fa fa-recycle"></i>'
        : '<i class="ps-icon fa fa-bolt deleted"></i>';
    var title =
      t.showDeletedAccessories == false
        ? config.translations.show_deleted_accessory
        : config.translations.show_non_deleted_accessory;
    t.btn.deletedAccessory.html(lblTxt);
    t.btn.deletedAccessory.attr("data-original-title", title);
    t.reload();
  };

  t.filters.fun.reload_location();
  t.filters.fun.reload_categories();
  t.filters.fun.reload_internal_place();
  t.filters.fun.reload_static_filters();
  t.initDateRangePicker();
  t.filters.fun.purchase_reference();
  t.filters.fun.bindFilterCountEvents();
  t.filters.wrapper.on("click", "button", t.search);

  t.btn.add.on("click", $.proxy(t.addAccessory));

  t.btn.openFilter.on("click", function () {
    let filterModal = new bootstrap.Modal(
      document.getElementById("accessoryFilterModal"),
    );

    filterModal.show();
    t.initDateRangePicker();
  });

  // t.btn.reload.on("click", $.proxy(t.reload));
  t.content.on("click", ".btn-reload-list", $.proxy(t.reload, t));
  t.content.on("keyup", ".user-list-search", function (e) {
    if (e.key === "Enter") t.search();
  });

  // page length
  t.content.on("change", ".user-list-page-length", function () {
    let len = parseInt($(this).val(), 10);
    t.dTbl.page.len(len).draw();
  });

  // the entire js written below is for clicking the all options section in actions.

  // this function is used so that anywhere other than the options button is clicked then the options should become hidden in this case
  $(document).on("click", function (e) {
    if (
      !$(e.target).closest("#detached-action-menu").length &&
      !$(e.target).closest(".user-list-menu-toggle").length
    ) {
      $("#detached-action-menu").remove();
      $(".user-list-menu-toggle").data("menu-open", false);
    }
  });

  // this function is used for what happens when we click on the all options button
  $(document).on("click", ".user-list-menu-toggle", function (e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    var $btn = $(this);

    if ($btn.data("menu-open")) {
      $("#detached-action-menu").remove();
      $btn.data("menu-open", false);
      return;
    }

    $("#detached-action-menu").remove();
    $(".user-list-menu-toggle").data("menu-open", false);

    var $menu = $btn.siblings(".dropdown-menu").clone(true);
    $menu.attr("id", "detached-action-menu").addClass("show");

    $("body").append($menu);

    var btnRect = $btn[0].getBoundingClientRect();
    var menuWidth = 200;

    $menu.css({
      position: "fixed",
      top: btnRect.bottom + "px",
      left: btnRect.left - menuWidth + btnRect.width + "px",
      zIndex: 99999,
      display: "block",
    });

    setTimeout(function () {
      var menuHeight = $menu.outerHeight();
      var windowHeight = $(window).height();
      if (btnRect.bottom + menuHeight > windowHeight) {
        $menu.css("top", btnRect.top - menuHeight + "px");
      }

      var menuLeft = parseFloat($menu.css("left"));
      if (menuLeft < 0) {
        $menu.css("left", btnRect.left + "px");
      }
    }, 0);

    $btn.data("menu-open", true);
  });

  // this function is used for what happens when we click on the elements inside the all options like edit, delete, clone, checkout etc.
  $(document).on("click", "#detached-action-menu .dropdown-item", function (e) {
    e.preventDefault();
    e.stopPropagation();

    var $item = $(this);
    var id = $item.data("id");
    var bsTarget = $item.attr("data-bs-target");
    var action = $item.attr("action");

    $("#detached-action-menu").remove();
    $(".user-list-menu-toggle").data("menu-open", false);

    if (bsTarget) {
      var $modal = $(bsTarget);
      if ($modal.length) {
        $modal.data("id", id);
        $modal.data("action", action);
        var modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
        modalInstance.show();
      }
      return;
    }

    var actionClass = null;
    ["dtActView", "dtActClone", "dtActCheckOut", "dtActNote"].forEach(
      function (cls) {
        if ($item.hasClass(cls)) actionClass = cls;
      },
    );

    if (actionClass && id) {
      $("#mytable")
        .find("." + actionClass + '[data-id="' + id + '"]')
        .first()
        .trigger("click");
    }
  });

  // this function is used such that when we scroll, the extra options should become hidden if it is visible in this case.
  $(document).on("scroll", function () {
    $("#detached-action-menu").remove();
    $(".user-list-menu-toggle").data("menu-open", false);
  });

  t.btn.search.on("click", $.proxy(t.tableSearch));
  // t.btn.import.on("click", $.proxy(t.import));
  // t.btn.export.on("click", $.proxy(t.export));
  // t.btn.export_pdf.on("click", $.proxy(t.exportPDF));
  t.btn.deletedAccessory.on("click", $.proxy(t.toggleDeletedAccessory));
  // t.btn.bulkcheckout.on("click", $.proxy(t.bulkcheckout));
  // t.btn.bulkcheckin.on("click", $.proxy(t.bulkcheckin));
  // t.table.on("click", ".dtActEdit", $.proxy(t.editAccessory));
  t.table.on("click", ".dtActEdit", $.proxy(t.editAccessory, t));
  t.table.on("click", ".dtActClone", $.proxy(t.cloneAccessory));
  t.table.on("click", ".dtActDel", $.proxy(t.deleteAccessory));
  t.table.on("click", ".dtActRestore", $.proxy(t.restoreAccessory));
  t.table.on("click", ".dtActCheckOut", $.proxy(t.checkoutAccessory));
  t.table.on("click", ".dtActNote", $.proxy(t.noteAccessory));
  // t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
  //t.mdl.frm.deselect.on("click", $.proxy(t.clearfrm));
  t.chkout.mdl.btnSubmit.on("click", $.proxy(t.handleCheckoutSubmit));
  t.mdl.frmEl.category.on("change", $.proxy(t.getCustomFields));
  t.filters.assigned_for.on("change", $.proxy(t.switchCheckTarget));
  t.filters.assigned_for.val("null").trigger("change");
  t.dTbl.ajax.reload();
};
