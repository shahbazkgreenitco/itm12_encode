var UserGroup = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#cc-email-group-list-wrapper");
  t.table = t.content.find("#cc-email-table");

  t.page = $("#page_boxed");

  t.httpCall = true;
  t.httpPostPath = "";
  t.data = {};

  t.btn = {};
  t.mdl = t.content.find("#cc-email-modal");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.frm = t.mdl.find("#cc-email-mdl-frm");
  t.mdl.frmEl = {};

  t.mdl.frmEl.name = t.mdl.frm.find("#name");
  t.mdl.frmEl.department_id = t.mdl.frm.find("#department_id");
  t.mdl.frmEl.problem_category = t.mdl.frm.find("#problem_category_id");
  t.mdl.frmEl.sub_category = t.mdl.frm.find("#sub_category_id");
  t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
  t.mdl.frmEl.company = t.mdl.frm.find("#company_id");
  t.mdl.btn = {};
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");

  t.bsModal = new bootstrap.Modal(document.getElementById("cc-email-modal"), {
    backdrop: "static",
    keyboard: false,
  });

  t.filterMdl = t.content.find("#ccEmailFilterModal");

  t.filterMdl.modal({
    backdrop: "static",
    keyboard: false,
    show: false,
  });

  t.filters = {
    wrapper: t.content.find("#ccEmailFilterModal"),
    data: {
      problem_categories: {},
    },
  };

  t.buildActions = function (d) {
    var actions = [];

    actions.push(`
            <button class="amg-action-btn btn-edit-group"  data-id="${d.id}"  type="button" data-bs-toggle="tooltip"
                   title="${config.translations.edit_group}">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                </svg>
            </button>
        `);

    actions.push(`
            <button class="amg-action-btn danger btn-delete-group" data-id="${d.id}" type="button" data-bs-toggle="tooltip" title="${config.translations.Delete}">
                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                </svg>
            </button>
        `);

    actions.push(` 
            <a href="${t.config.url.group_member}/${d.id}" type="button" data-bs-toggle="tooltip" title="${config.translations.manage_users}" target="_blank"
                class="amg-action-btn primary" title="Manage Users">
                <i class="bi bi-people"></i>
            </a>
        `);

    return `<div class="amg-datatable-actions d-flex justify-content-center gap-2">${actions.join("")}</div>`;
  };

  t.dTbl = t.table.DataTable({
    language: {
        info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
        infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
        emptyTable: t.config.datatable_translations.empty_result,
        zeroRecords: t.config.datatable_translations.empty_result,
        infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
        paginate: {
            previous: t.config.datatable_translations.prev,
            next: t.config.datatable_translations.next
        }
    },
    autoWidth: false,
    dom: "rtip", 
    responsive: true,
    scrollX: true,
    aoColumnDefs: [
      {
        targets: 6,
        orderable: true,
        render: function (d) {
          return (
            '<a href="' +
            t.config.url.group_member +
            "/" +
            d.id +
            '" target="_blank">' +
            d.member_count +
            "</a>"
          );
        },
      },
      {
        targets: 7,
        orderable: false,
        searchable: false,
        width: "148px",
        render: function (data, type, row) {
          return t.buildActions(row.a || {});
        },
      },
    ],
    order: [[5, "desc"]],
    processing: true,
    serverSide: true,
    ajax: {
      url: t.config.url.user_group_ajax,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.filters = t.config.other_filters;
      },
    },
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1
    },
    columns: [
      { data: "a.name" },
      { data: "a.company_name" },
      { data: "a.depart_name" },
      { data: "a.problem_name" },
      { data: "a.sub_name" },
      { data: "a.created_at_format" },
      { data: "a" }, 
      { data: "a" },
    ],
    drawCallback: function() {
      $('[data-bs-toggle="tooltip"]').tooltip();
    }
  });

  var searchTimer = null;
  t.content.find(".user-list-search").on("keyup", function () {
    var v = $(this).val();
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function () {
      t.dTbl.search(v).draw();
    }, 400);
  });

  t.content.find(".cc-email-group-list-page-length").on("change", function () {
    t.dTbl.page.len($(this).val()).draw();
  });

  t.reload = function () {
    t.dTbl.ajax.reload();
  };

  var select2Opts = { width: "100%" };

  t.mdl.frmEl.company.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl,
      placeholder: config.translations.select_company,
      allowClear: true,
      ajax: {
        url: t.config.url.getCompanyByUserAccess,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
      },
      templateSelection: function (data, container) {
        $(container).attr("title", data.text);
        return data.text?.length > 50
          ? data.text.substring(0, 50) + "..."
          : data.text;
      },
    }),
  );

  t.mdl.frmEl.department_id
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl,
        allowClear: true,
        placeholder: config.translations.select_department,
        ajax: {
          url: t.config.url.department,
          dataType: "json",
          delay: 300,
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
              company_id: t.mdl.frmEl.company.val() || "",
            };
          },
        },
        templateSelection: function (data, container) {
          $(container).attr("title", data.text);
          return data.text.length > 50
            ? data.text.substring(0, 50) + "..."
            : data.text;
        },
      }),
    )
    .on("change", function () {
      t.refillProblemCategory();
    });

  t.mdl.frmEl.problem_category.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl,
      placeholder: config.translations.Select_Problem_Category,
    }),
  );

  t.mdl.frmEl.sub_category.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl,
      placeholder: config.translations.Select_Sub_Category,
    }),
  );

  var default_company_id = $("#config-company").val();
  var default_company_text = $("#config-company option:selected").text();

  if (default_company_id != 0) {
    let option = new Option(
      default_company_text,
      default_company_id,
      true,
      true,
    );
    t.mdl.frmEl.company.empty().append(option).trigger("change");
  } else {
    t.mdl.frmEl.company.val("").trigger("change");
  }

  t.mdl.frmEl.company.on("change", function () {
    t.mdl.frmEl.department_id.empty().trigger("change");
  });

  t.refillProblemCategory = function (e) {
    if (typeof e !== "undefined") e.preventDefault();

    t.data.problem_categories = [];
    t.mdl.frmEl.problem_category
      .empty()
      .append(new Option(config.translations.Select_Problem_Category, ""));

    var type_val = parseInt($.trim(t.mdl.frmEl.department_id.val()));
    if (type_val > 0 && !isNaN(type_val)) {
      $.get(t.config.url.problem_categories_by_company + "/" + type_val)
        .done(function (data) {
          if (typeof data == "object" && data.data.length) {
            t.data.problem_categories = data.data;
            $.each(data.data, function (i, v) {
              t.mdl.frmEl.problem_category.append(
                $("<option>").val(v.id).text(v.name),
              );
            });
          }
        })
        .always(function () {
          t.mdl.frmEl.problem_category.trigger("change");
        });
    } else {
      t.mdl.frmEl.problem_category.trigger("change");
    }
  };

  t.refillSubCategory = function (e, val) {
    if (typeof e !== "undefined") e.preventDefault();

    t.data.sub_categories = [];
    t.mdl.frmEl.sub_category
      .empty()
      .append(new Option(config.translations.Select_Sub_Category, ""));

    var type_val = parseInt($.trim(t.mdl.frmEl.problem_category.val()));
    if (type_val > 0 && !isNaN(type_val)) {
      try {
        $.each(t.data.problem_categories, function (i, v) {
          if (v.id == type_val) {
            if (Array.isArray(v.sub) && v.sub.length > 0) {
              t.data.sub_categories = v.sub;
              $.each(v.sub, function (j, k) {
                t.mdl.frmEl.sub_category
                  .append(new Option(k.name, k.id, true, true))
                  .trigger("change");
              });
              return false;
            }
          }
        });
      } catch (e) {
        console.log(e);
      }
    }

    t.updateSubCategoryVisibility();
    t.mdl.frmEl.sub_category.trigger("change");
  };

  t.mdl.frmEl.problem_category.on("change", function () {
    t.refillSubCategory();
  });

  t.updateSubCategoryVisibility = function () {
    if (t.data.sub_categories.length > 0) {
      t.mdl.frmEl.sub_category.rules("add", {
        required: true,
        str_name: true,
      });
      t.mdl.frmEl.subCategoryIdCvr.show();
    } else {
      t.mdl.frmEl.sub_category.rules("remove");
      t.mdl.frmEl.subCategoryIdCvr.hide();
    }
  };

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    onfocusout: false,
    onkeyup: false,
    rules: {
      company_id: { required: true },
      name: { required: true, clean_text_only: true },
      department_id: { required: true },
      problem_category_id: { required: true },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent("div").parent("div"));
    },
  });

  t.mdl.frmEl.subCategoryIdCvr.hide();

  t.addUserGroup = function (e) {
    t.resetFrm();
    t.mdl.frm.attr("action", t.config.url.add);
    t.mdl.title.html(config.translations.add_cc_email_group);
    t.mdl.btnSubmit.text(config.translations.add);
    t.bsModal.show();
  };

  t.createGroup = function (e) {
    e.preventDefault();

    if (t.frmValidator.form() == false) return false;

    t.mdl.btnSubmit.prop("disabled", true);
    t.httpPostPath = t.mdl.frm.attr("action");

    var formData = new FormData(t.mdl.frm[0]);

    $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    })
      .done(function (data) {
        if (typeof data == "object") {
          if (data.status == "success") {
            t.bsModal.hide();
            sweetAlert("center", "success", data);
            setTimeout(function () {
              t.dTbl.ajax.reload();
            }, 800);
          } else {
            sweetAlert("center", "error", data);
          }
        }
      })
      .fail(function () {
        sweetAlert("center", "error", {
          msg: config.translations.something_went_wrong,
        });
      })
      .always(function () {
        t.mdl.btnSubmit.prop("disabled", false);
      });
  };

  t.loadForm = function (data, forAction) {
    t.resetFrm();
    t.mdl.frmEl.name.val(data.name);
    if (data.department.company_id != null) {
      t.mdl.frmEl.company
        .empty()
        .append(new Option(data.company.name, data.company.id, true, true))
        .trigger("change");
    }
    if (data.department.department_id != null) {
      t.mdl.frmEl.department_id
        .empty()
        .append(
          new Option(
            data.department.department_name,
            data.department.department_id,
            true,
            true,
          ),
        )
        .trigger("change");
      t.mdl.frmEl.problem_category
        .empty()
        .append(
          new Option(
            data.department.problem_category_name,
            data.department.problem_category_id,
            true,
            true,
          ),
        );
    }
    if (data.department.sub_category_id && data.department.sub_category_id != null) {
      t.refillSubCategory(undefined, data.department.sub_category_id);
    }
  };

  t.editGroup = function (e) {
    e.preventDefault();
    var GId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.edit;
    var http = $.get(t.httpPostPath + "/" + GId);
    t.mdl.frm.attr("action", t.config.url.update + "/" + GId);
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(config.translations.edit_cc_email_group);
          t.mdl.btnSubmit.text(config.translations.update);
          t.mdl.modal("show");
          t.loadForm(data.data);
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

  t.deleteGroup = function (e) {
    e.preventDefault();
    var Id = $(this).attr("data-id");
    t.httpPostPath = t.config.url.delete + "/" + Id;
    var data = {
      msg: config.translations.something_went_wrong,
    };
    sweetAlerts(
        config.translations.delete_record,
        "warning",
        t.httpPostPath,
        t.dTbl,
        data,
        'null',        
        '',             
        '',  
        config.translations.ok_button ,
        config.translations.cancel_button
    );
  };

  t.openFilter = function (e) {
    t.filterMdl.modal("show");
    setTimeout(function () {
      var start = moment().startOf("day");
      var end = moment().endOf("day");

      function cb(start, end) {
        $("#reportrange span", t.filterMdl).html(
          start.format("DD-MM-YYYY HH:mm:ss") +
            " - " +
            end.format("DD-MM-YYYY HH:mm:ss"),
        );
        t.filters.daterange.val(
          start.format("YYYY-MM-DD HH:mm:ss") +
            " - " +
            end.format("YYYY-MM-DD HH:mm:ss"),
        );
      }

      $("#reportrange", t.filterMdl).daterangepicker(
        {
          parentEl: "#incidentFilterModal",
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

      $("#reportrange", t.filterMdl).on("cancel.daterangepicker", function () {
        $(this).find("span").html("");
        t.filters.daterange.val("");
      });
    }, 300);
  };

  t.filters.company = t.filters.wrapper.find("#filter_by_company");
  t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
  t.filters.daterange = t.filters.wrapper.find("#daterange");
  t.filters.clear = t.filters.wrapper.find("#clear");

  t.filters.company.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.filter_by_company || "Filter by Company",
      allowClear: true,
      dropdownParent: t.filterMdl,
      ajax: {
        url: t.config.url.getCompanyByUserAccess,
        dataType: "json",
        delay: 300,
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
          };
        },
      },
    }),
  );

  t.filters.based_on.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.filter_based_on || "Filter Based On",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.cache_filter_values = function () {
    t.config.other_filters = {};

    var company = t.filters.company.val();
    if (
      company &&
      company.length &&
      company[0] !== "null" &&
      company[0] !== null
    ) {
      t.config.other_filters.filter_by_company = company; 
    }

    var basedOn = t.filters.based_on.val();
    if (basedOn && basedOn !== "null" && basedOn !== "") {
      t.config.other_filters.based_on = basedOn;

      var daterange = t.filters.daterange.val();
      if (daterange) {
        t.config.other_filters.daterange = daterange;
      }
    }

    t.updateFilterCount();
  };

  t.updateFilterCount = function () {
    var count = 0;
    var f = t.config.other_filters || {};

    if (f.filter_by_company && f.filter_by_company.length) count++; 
    if (f.based_on) count++;

    var $badge = t.content.find(".filter-count-badge");
    if (count > 0) {
      $badge.text(count).removeClass("d-none");
    } else {
      $badge.text("0").addClass("d-none");
    }
  };

  t.filterMdl.on("click", "#advanced_filter", function (e) {
    e.preventDefault();
    t.cache_filter_values();
    t.dTbl.ajax.reload(null, false);
    t.filterMdl.modal("hide");
  });

  t.filters.clear.on("click", function (e) {
    e.preventDefault();

    t.filters.company.val(null).trigger("change");
    t.filters.based_on.val(null).trigger("change");

    var start = moment().startOf("day");
    var end = moment().endOf("day");

    t.filterMdl
      .find("#reportrange span")
      .html(
        start.format("DD-MM-YYYY HH:mm:ss") +
          " - " +
          end.format("DD-MM-YYYY HH:mm:ss"),
      );
    t.filters.daterange.val(
      start.format("YYYY-MM-DD HH:mm:ss") +
        " - " +
        end.format("YYYY-MM-DD HH:mm:ss"),
    );

    var $rr = t.filterMdl.find("#reportrange");
    if ($rr.data("daterangepicker")) {
      $rr.data("daterangepicker").setStartDate(start);
      $rr.data("daterangepicker").setEndDate(end);
    }

    t.config.other_filters = {};
    t.updateFilterCount();
    t.filterMdl.modal("hide");
    t.dTbl.ajax.reload(null, false);
  });

  t.resetFrm = function () {
    t.frmValidator.resetForm();
    t.mdl.frm.trigger("reset");
    t.mdl.frmEl.name.val("");
    t.mdl.frmEl.company.val(null).empty().trigger("change"); 
    t.mdl.frmEl.department_id.val("").empty().trigger("change");
    t.mdl.frmEl.problem_category.val("").empty().trigger("change");
    t.mdl.frmEl.sub_category.val("").empty().trigger("change");
    t.mdl.frmEl.subCategoryIdCvr.hide();
  };

  t.content.on("click", ".btn-add-group", $.proxy(t.addUserGroup, t));
  t.content.on("click", ".btn-reload-list", $.proxy(t.reload, t));
  t.table.on("click", ".btn-edit-group", $.proxy(t.editGroup));
  t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
  t.table.on("click", ".btn-delete-group", $.proxy(t.deleteGroup));
  t.mdl.on("click", "#btnSubmit", $.proxy(t.createGroup, t));
};
