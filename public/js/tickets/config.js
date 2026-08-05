var UserList = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;
  t.content = $(".main-content");
  t.mdl = t.content.find("#settingsmdl");
  t.frm = t.mdl.find("#userlist");
  t.notification = t.mdl.find("#notification");
  t.autoupdate = t.mdl.find("#autoupdate");
  t.duplicate_ticket = t.mdl.find("#duplicateTicket");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update;

  t.frmEl = {};
  t.frmEl.eu_hide_priority = t.frm.find("#eu_hide_priority");
  t.frmEl.eu_hide_assigned_to = t.frm.find("#eu_hide_assigned_to");
  t.frmEl.eu_hide_expire_at = t.frm.find("#eu_hide_expire_at");
  t.frmEl.eu_hide_tat = t.frm.find("#eu_hide_tat");
  t.frmEl.enable_vip_ticket = t.frm.find("#enable_vip_ticket");
  t.frmEl.mail_all_status_changes = t.frm.find("#mail_all_status_changes");
  t.frmEl.tat_by_work_hour = t.frm.find("#tat_by_work_hour");
  t.frmEl.department_config = t.frm.find("#department_config");
  t.frmEl.default_work_start = t.frm.find("#default_work_start");
  t.frmEl.default_work_end = t.frm.find("#default_work_end");
  t.frmEl.default_work_days = t.frm.find("#default_work_days");
  t.frmEl.kd_auto_suggestion = t.frm.find("#kd_auto_suggestion");
  t.frmEl.checked_cc_checkbox = t.frm.find("#checked_cc_checkbox");
  t.frmEl.ticket_id_initials = t.frm.find("#ticket_id_initials");
  t.frmEl.ticket_id_initial_separator = t.frm.find(
    "#ticket_id_initial_separator",
  );
  t.notification.sla_reminder = t.notification.find("#sla_reminder");
  t.notification.sla_reminder_hr = t.notification.find("#sla_reminder_hr");
  t.notification.sla_notification_type = t.notification.find(
    "#sla_notification_type",
  );
  t.autoupdate.min_before_escalation_to_handler = t.autoupdate.find(
    "#min_before_escalation_to_handler",
  );
  t.autoupdate.min_before_escalation_to_user = t.autoupdate.find(
    "#min_before_escalation_to_user",
  );
  t.autoupdate.min_before_breached_to_technician = t.autoupdate.find(
    "#min_before_breached_to_technician",
  );
  t.autoupdate.min_before_close_ticket_to_handler = t.autoupdate.find(
    "#min_before_close_ticket_to_handler",
  );
  t.notification.mark_technician_as_cc = t.notification.find(
    "#mark_technician_as_cc_in_ticket_create",
  );

  // dublicate ticket form
  t.duplicate_ticket.enable_duplicate_issues_check = t.duplicate_ticket.find(
    "#enable_duplicate_issues_check",
  );
  t.duplicate_ticket.auto_merge_duplicate_issues = t.duplicate_ticket.find(
    "#auto_merge_duplicate_issues",
  );
  t.duplicate_ticket.allow_user_to_continue_ticket_creation_on_duplicate =
    t.duplicate_ticket.find(
      "#allow_user_to_continue_ticket_creation_on_duplicate",
    );
  t.frmEl.feedback_required = t.frm.find("#feedback_required");
  t.frmEl.feedback_max_rating = t.frm.find("#feedback_max_rating");

  $(document).on('change', '#feedback_required', function () {
      if ($(this).val() === '1') {
          $('#feedback_max_rating_div').removeClass('d-none');
      } else {
          $('#feedback_max_rating_div').addClass('d-none');
          $('#feedback_max_rating').val('');
      }
  });

  t.frmEl.feedback_required.trigger('change');

  t.btn = {};
  t.btn.addate = t.frm.find("#addate");
  t.btn.update = t.frm.find("#update");

  t.btn.updateNotification = t.notification.find("#updateNotification");
  t.btn.autoUpdate = t.autoupdate.find("#autoUpdate");
  t.btn.duplicateTicketButton = t.duplicate_ticket.find(
    "#duplicateTicketButton",
  );
  t.frmValidator = t.notification.validate({
    rules: {
      sla_reminder_hr: {
        required: true,
        min: 1,
        decimal: true,
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent("div"));
    },
  });

  t.frmValidator = t.notification.validate({
    rules: {
      sla_reminder_hr: {
        required: true,
        min: 1,
        decimal: true,
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent("div"));
    },
  });

  t.updateNotification = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var frmData = new FormData(t.notification[0]);
    frmData.append("company_id", companyId);
    t.btn.updateNotification.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.updateNotification,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.updateNotification.prop("disabled", false);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.updateNotification.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.autoUpdate = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.autoupdate[0]);
    frmData.append("company_id", companyId);
    t.btn.autoUpdate.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.autoUpdate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.autoUpdate.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.autoUpdate.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.btn.autoUpdate.prop("disabled", false);
    });
    http.always(function () {
      t.btn.autoUpdate.prop("disabled", false);
    });
  };

  t.duplicateTicket = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.duplicate_ticket[0]);
    frmData.append("company_id", companyId);
    t.btn.duplicateTicketButton.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.duplicateTicketUpdate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.duplicateTicketButton.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.duplicateTicketButton.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.btn.duplicateTicketButton.prop("disabled", false);
    });
    http.always(function () {
      t.btn.duplicateTicketButton.prop("disabled", false);
    });
  };

  t.handlesubmit = function (e) {
    e.preventDefault();

    var separatorVal = $.trim(t.frmEl.ticket_id_initial_separator.val());
    if (separatorVal.length > 5) {
        sweetAlert("center", "error", {
            msg: "Ticket ID initial separator must be 5 characters or less."
        });
        t.frmEl.ticket_id_initial_separator.focus();
        return false;
    }

    var frmData = new FormData(t.frm[0]);
    frmData.append("company_id", companyId);
    frmData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          window.location.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.enableCheckBox = function () {
    const enableCheck =
      t.frmDuplicate.enable_duplicate_issues_check.prop("checked");

    // Set read-only state
    if (enableCheck === true) {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        false,
      );
    } else {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("checked", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "checked",
        false,
      );

      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", !enableCheck);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        !enableCheck,
      );
    }
  };
  t.duplicateTicketSUbmit = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.frmDuplicate[0]);
    t.frmDuplicate.duplicateTicketButton.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.autoDuplicate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.frmDuplicate.duplicateTicketButton.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
    });
    http.always(function () {
      t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
    });
  };

  t.content.on("click", ".black-slide-links a", function (e) {
    e.preventDefault();
    var thisNav = $(this);
    t.content.find(".black-slide-links .active").removeClass("active");
    thisNav.parent().addClass("active");
    t.content.find(".black-slide-view.active").slideUp("fast", function () {
      $(this).removeClass("active");
      t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
    });
  });
  t.notification.sla_reminder.select2({ width: "100%" });
  t.notification.sla_notification_type.select2({ width: "100%" });
  t.notification.mark_technician_as_cc.select2({ width: "100%" });
  var select2Opts = { width: "100%" };
  t.content.on("click", ".black-slide-links a", function (e) {
    e.preventDefault();
    var thisNav = $(this);
    t.content.find(".black-slide-links .active").removeClass("active");
    thisNav.parent().addClass("active");
    t.content.find(".black-slide-view.active").slideUp("fast", function () {
      $(this).removeClass("active");
      t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
    });
  });

  t.frmEl.ticket_id_initials.select2({
    placeholder: t.config.translations.Select_initials,
    allowClear: true,
    closeOnSelect: false,
    width: "100%",
  });
  t.frmEl.ticket_id_initials.on("select2:select", function (e) {
    const option = e.params.data.element;
    $(option).detach();
    $(this).append(option).trigger("change.select2");
  });
  t.frmEl.ticket_id_initials.on("select2:unselect", function (e) {
    const option = e.params.data.element;
    $(option).detach();
    $(this).append(option).trigger("change.select2");
  });
  t.notification.sla_reminder.select2({ width: "100%" });
  t.notification.sla_notification_type.select2({ width: "100%" });
  t.frmEl.mail_all_status_changes.select2($.extend({}, select2Opts));
  t.frmEl.tat_by_work_hour.select2($.extend({}, select2Opts));
  t.frmEl.kd_auto_suggestion.select2($.extend({}, select2Opts));
  t.frmEl.checked_cc_checkbox.select2($.extend({}, select2Opts));
  t.frmEl.department_config.select2($.extend({}, select2Opts));
  t.frmEl.default_work_start.mdtimepicker({ twelvehour: true });
  t.frmEl.default_work_end.mdtimepicker({ twelvehour: true });
  t.frmEl.feedback_required.select2($.extend({}, select2Opts));
  t.frmEl.feedback_max_rating.select2($.extend({}, select2Opts));
  t.btn.update.on("click", $.proxy(t.handlesubmit));
  t.btn.updateNotification.on("click", $.proxy(t.updateNotification));
  t.btn.autoUpdate.on("click", $.proxy(t.autoUpdate));
  t.btn.duplicateTicketButton.on("click", $.proxy(t.duplicateTicket));
};

var ReportFields = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;

  t.content = $(".main-content");
  t.mdl = t.content.find("#report_section");
  t.frm = t.mdl.find("#reportfields");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update_report_fields;

  t.frmEl = {};
  t.frmEl.ticket_id = t.frm.find("#ticket_id");
  t.frmEl.status = t.frm.find("#status");
  t.frmEl.problem_cat = t.frm.find("#problem_cat");
  t.frmEl.priority = t.frm.find("#priority");
  t.frmEl.department = t.frm.find("#department");
  t.frmEl.attender = t.frm.find("#attender");
  t.frmEl.created_at = t.frm.find("#created_at");
  t.frmEl.updated_at = t.frm.find("#updated_at");
  t.frmEl.resolved_at = t.frm.find("#resolved_at");

  t.btn = {};
  t.btn.updatelist = t.frm.find("#updatelist");

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  t.handlesubmit = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.frm[0]);
    frmData.append("_token", t.getToken());
    frmData.append("company_id", companyId);
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.btn.updatelist.on("click", $.proxy(t.handlesubmit));
};

var Department = function (config, userObj) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.selectedDepartments = [];
  t.departmentMap = {}; 
  t.config = config;
  t.content = $(".main-content");
  t.user = userObj;
  t.table = t.content.find("#departments");
  t.deptToggleStatus = t.content.find("#deptToggleStatus");
  t.mdl = t.content.find("#departmentmodal");
  t.frm = t.mdl.find("#DepartmentForm");
  t.frmEl = {};
  t.frmEl.name = t.frm.find('#name');
  t.frmEl.department_tag = t.frm.find('#department_tag');
  t.frmEl.company_id = t.frm.find('#company_name');
  t.frmEl.department_head_id = t.frm.find('#department_head_id');
  t.frmEl.attender_id = t.frm.find('#attender_id');
  t.frmEl.description = t.frm.find('#description');
  t.frmEl.tkt_auto_creation_id = t.frm.find('#tkt_auto_creation_id');
  t.frmEl.department_admin = t.frm.find('#department_admin');
  t.frmEl.asset_department = t.frm.find('#asset_department');
  t.frmEl.asset_department_admin = t.frm.find('#asset_department_admin');
  t.frmEl.department_custom_fieldset = t.frm.find('#department_custom_fieldset');
  t.frmEl.pc_custom_fieldset = t.frm.find('#pc_custom_fieldset');
  t.frmEl.sc_custom_fieldset = t.frm.find('#sc_custom_fieldset');
  t.ticketFieldset = t.frm.find('#ticketFieldset');
  t.assetFieldset = t.frm.find('#assetFieldset');
  t.frmEl.access_role_id = t.frm.find('#access_role_id');
  t.frmEl.img = t.frm.find('#img');
  t.btn = {};
  t.btn.submit = t.frm.find('#btnSubmit');
  t.btn.update = t.frm.find('#btnupdate');
  t.btn.clear = t.frm.find('#btnClear');
  t.btn.clr = t.frm.find("#btnClr");
  t.mdltitle = t.mdl.find('.modal-title');
  var select2Opts = { width: "100%" };
  t.btn = {};
  t.btn.reload = t.content.find(".btn-reload-list");
  t.getModalErrorWrap = function (element) {
      var row = element.closest(".amg-form-field-row");
      var wrap;

      if (!row.length) {
          return $();
      }

      wrap = row.children(".amg-form-error-wrap");
      if (!wrap.length) {
          wrap = $('<div class="amg-form-error-wrap"></div>');
          row.append(wrap);
      }

      return wrap;
  };

  t.updateValidationState = function (element, hasError) {
    var group = element.closest(".input-group");      
    if (group.length) {
        group.toggleClass("amg-form-invalid", !!hasError);
    }     
  };   

  t.tblHelpers = {
    actions: function () {
      return function (d) {
        var lbl = d.status == 1 ? "Enabled" : "Disabled";
        var checked = d.status == 1 ? "checked" : "";
        let html = `
                    <div class="d-flex align-items-center gap-2">
                        <label class="tcf-toggle dept-row-toggle" style="width:38px;height:22px;" id="department_${d.id}">
                            <input type="checkbox" ${checked ? "checked" : ""} name="department" value="${d.id}" id="department_${d.id}" class="a_department" />
                            <span class="tcf-toggle-slider"></span>
                        </label>
                        <span class="dept-toggle-label" style="font-size:12.5px;color:var(--bs-body-color);">
                            ${checked ? "Disable" : "Enable"}
                        </span>
                    </div>
                `;
        return html;
      };
    },
  };

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  t.dTbl = t.table.DataTable({
    autoWidth: false,
    aoColumnDefs: [
      {
        targets: 0,
        bSortable: false,
        render: function (data, type, row) {
          return `<input type="checkbox" class="dept-row-check chkChild form-check-input" style="width:14px;height:14px;accent-color:#3b82f6;cursor:pointer;" value="${row.a.id}">`;
        },
      },
     {
          targets: 3,
          bSortable: false,
          render: function (data, type, row) {
              return row.a.status == 1
                  ? `<span class="badge rounded-pill b5-text py-1" style="background:#E5FFE7;color:#006D1D">Enabled</span>`
                  : `<span class="badge rounded-pill b5-text py-1"  style="background:#F2E5FF;color:#5C00E5">Disabled</span>`;
          },
      },
      {
        targets: 4,
        bSortable: true,
        render: t.tblHelpers.actions(),
      },
    ],
    order: [[1, "asc"]],
    processing: true,
    serverSide: true,
    deferLoading: true,
    searching: false,
    lengthChange: false,
    ajax: {
      url: t.config.url.get_departments,
      type: "post",
      data: function (d) {
        d._token = t.getToken();
        d.company_id = companyId;
        d.search = t.content.find(".department-search").val();
      },
    },
    columns: [
      { data: "a" },
      { data: "a.department" },
      { data: "a.ebts_username" },
      { data: "a" },
      { data: "a" },
    ],
   
  });

   var $lengthDropdown = t.content.find('.deptShowCount');
  $lengthDropdown.off('change').on('change', function () {
    var newLength = parseInt($(this).val(), 10);
    t.dTbl.page.len(newLength).draw(false);
  });

  t.dTbl.on('draw', function () {
    t.departmentMap = {};
    var data = t.dTbl.rows().data();
    data.each(function (row) {
      t.departmentMap[row.a.id] = {
        name: row.a.department,
        status: row.a.status
      };
    });
  });

  t.dTbl.on("draw", function () {
    t.table.find(".chkParent").prop("checked", false);
    t.table.find(".chkChild").each(function () {
      if (t.selectedDepartments.includes($(this).val())) {
        $(this).prop("checked", true);
      }
    });
  });

  t.reload = function () {
    t.dTbl.ajax.reload();
  };

  t.tableSearch = function (e) {
    e.preventDefault();
    var v = $.trim($("#departments_wrapper .department-search").val());
    t.dTbl.search(v).draw();
  };
  t.resetFrm = function () {
    t.frmEl.name.val("");
    t.frmEl.department_tag.empty();
    t.frmEl.company_id.empty().trigger("change");
    t.frmEl.attender_id.empty().trigger("change");
    t.frmEl.description.trigger("change");
    t.frmEl.tkt_auto_creation_id.val('').trigger("change");
    t.frmEl.department_admin.empty().trigger("change");
    t.frmEl.asset_department.val('').trigger("change");
    t.frmEl.asset_department_admin.empty().trigger("change");
    t.frmEl.department_custom_fieldset.empty().trigger("change");
    t.frmEl.pc_custom_fieldset.empty().trigger("change");
    t.frmEl.sc_custom_fieldset.empty().trigger("change");
    t.frmEl.department_head_id.empty().trigger("change");
    t.frmEl.access_role_id.val('').trigger("change");
    t.frmEl.company_id.val('').trigger("change");
    t.frmValidator.resetForm();
  };
  t.addDepartment = function () {
    t.resetFrm();
    t.mdl.modal('show');
  }
  t.frmValidator = t.frm.validate({
      onsubmit: false,
      ignore: [],                     
      rules: {
          company_id: {
              required: true,
              str_name: true
          },
          name: {
              required: true,
              str_name_format: true,
              charLimit: [2, 40]
          },
          attender_id: {
              str_name: true
          },
          description: {
              clean_text_only: true,
          },
      },
      messages: {
          company_id: "Please select a company",
          name: "Please enter a valid department name (2-40 characters)"
      },
      errorPlacement: function (error, element) {
          var errorWrap = getErrorWrap(element);
          if (errorWrap.length) {
              error.appendTo(errorWrap);
          } else {
              error.insertAfter(element.closest(".input-group"));
      }
          updateValidationState(element, true);
      },
      highlight: function (element) {
          updateValidationState($(element), true);
      },
      unhighlight: function (element) {
          updateValidationState($(element), false);
      },
      invalidHandler: function (event, validator) {
          if (validator.numberOfInvalids()) {
              validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
          }
      }
  });
  t.handlesubmit = function (e) {
      e.preventDefault();
      if (t.frmValidator.form() == false) {
          $('#img').hide();
          return false;
      }
      var frmData = new FormData;
      if(t.frmEl.attender_id.val() > 0){
          frmData.append('attender_id', t.frmEl.attender_id.val());
      } else{
          frmData.append('attender_id', "");
      }
      if(t.frmEl.department_head_id.val() > 0){
          frmData.append('department_head_id', t.frmEl.department_head_id.val());
      } else{
          frmData.append('department_head_id', "");
      }
      frmData.append('_token', t.config.token);
      frmData.append('name', t.frmEl.name.val());
      frmData.append('department_tag', t.frmEl.department_tag.val());
      frmData.append('company_id', t.frmEl.company_id.val());
      frmData.append('description', t.frmEl.description.val());
      frmData.append('tkt_auto_creation_id', t.frmEl.tkt_auto_creation_id.val());
      frmData.append('department_admin', t.frmEl.department_admin.val());
      frmData.append('asset_department', t.frmEl.asset_department.val());
      frmData.append('asset_department_admin', t.frmEl.asset_department_admin.val());
      frmData.append('department_custom_fieldset', t.frmEl.department_custom_fieldset.val());
      frmData.append('pc_id',t.frmEl.pc_custom_fieldset.val());
      frmData.append('sc_id',t.frmEl.sc_custom_fieldset.val());
      frmData.append('access_role_id',t.frmEl.access_role_id.val());

      var http = $.ajax({
          url: t.config.url.add,
          type: "POST",
          processData: false,
          contentType: false,
          data: frmData
      });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.msg || 'Saved successfully',
            timer: 2000,
            showConfirmButton: false
          });
          t.mdl.modal("hide");
          $('#hand').hide();
          t.dTbl.ajax.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.msg || 'Something went wrong'
          });
        }
      }
    });
    http.fail(function () {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: config.translations.something_went_wrong
      });
    });
    http.always(function () {
      $('#img').hide();
    });
  };
  t.frmEl.asset_department.select2($.extend({}, select2Opts, { placeholder: "Select",
      dropdownParent: t.frmEl.asset_department.parent()
  }));
  t.frmEl.access_role_id.select2($.extend({}, select2Opts, { placeholder: "Select Role",
      dropdownParent: t.frmEl.access_role_id.parent()
  }));
  t.frmEl.pc_custom_fieldset.on("change", function () {
    let parentId = t.frmEl.pc_custom_fieldset.val();
    let scDropdown = t.frmEl.sc_custom_fieldset;
    scDropdown.html('<option value="">Select Subcategory</option>');

    if (!parentId) {
        t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".pc_row").hide();
        return;
    }

    $.ajax({
        url: t.config.url.getSubCategories,
        type: "GET",
        data: { parent_id: parentId },
        success: function (response) {
            if (response.dropdown.sc.length > 0) {
                let existingValues = [];

                scDropdown.find("option").each(function () {
                    existingValues.push($(this).val());
                });

                $.each(response.dropdown.sc, function (i, v) {
                    if (!existingValues.includes(v.id.toString())) {
                        let isSelected = (t.preselectedScId && t.preselectedScId == v.id) || false;
                        scDropdown.append(new Option(v.text, v.id, false, isSelected));
                    }
                });

                t.frmEl.sc_custom_fieldset.attr('disabled', false).parents(".row").show();
            } else {
                t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".row").hide();
            }
        },
        error: function () {
            console.error("Failed to load subcategories.");
            t.frmEl.sc_custom_fieldset.attr('disabled', true).parents(".row").hide();
        }
    });
  });
  t.frmEl.asset_department_admin .select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.asset_department_admin.parent(),
        ajax: {
            url: t.config.getUserByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function() {
                        return t.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: "Select the User"
  }));
  t.frmEl.department_admin.select2($.extend({}, select2Opts, {
      dropdownParent: t.frmEl.department_admin.parent(),
      ajax: {
          url: t.config.getUserByAjax,
          dataType: "json",
          data: function(p) {
              return {
                  search: p.term,
                  page: p.page || 1,
                  company_id: function() {
                      return t.frmEl.company_id.val();
                  }
              };
          },
          delay: 300
      },
      allowClear: true,
      placeholder: "Select the User"
  }));

  t.frmEl.department_custom_fieldset.select2($.extend({}, select2Opts, {
      dropdownParent: t.frmEl.department_custom_fieldset.parent(),
      ajax: {
          url: t.config.url.getCustomFieldsetByModule + "/2",
          dataType: "json",
          data: function(p) {
              return {
                  search: p.term,
                  page: p.page || 1
              };
          },
          delay: 300
      },
      allowClear: true,
      placeholder: "Select Custom Fieldset"
  }));

  t.frmEl.attender_id.select2($.extend({}, select2Opts, {
      dropdownParent: t.frmEl.attender_id.parent(),
      ajax: {
          url: t.config.getUserByAjax,
          dataType: "json",
          data: function(p) {
              return {
                  search: p.term,
                  page: p.page || 1,
                  company_id: function() {
                      return t.frmEl.company_id.val();
                  }
              };
          },
          delay: 300
      },
      allowClear: true,
      placeholder: "Select the User"
  }));

  t.frmEl.department_head_id.select2($.extend({}, select2Opts, {
      dropdownParent: t.frmEl.department_head_id.parent(),
      ajax: {
          url: t.config.getUserByAjax,
          dataType: "json",
          data: function(p) {
              return {
                  search: p.term,
                  page: p.page || 1,
                  company_id: function() {
                      return t.frmEl.company_id.val();
                  }
              };
          },
          delay: 300
      },
      allowClear: true,
      placeholder: "Select the User"
  }));

  t.frmEl.tkt_auto_creation_id.select2($.extend({}, select2Opts, {placeholder: "Select Account", dropdownParent: t.frmEl.tkt_auto_creation_id.parent()}));
  t.frmEl.pc_custom_fieldset.select2($.extend({}, select2Opts, {placeholder: "Select Problem category",dropdownParent: t.frmEl.pc_custom_fieldset.parent(),allowClear:true}));
  t.frmEl.sc_custom_fieldset.select2($.extend({}, select2Opts, {placeholder: "Select Sub category",dropdownParent: t.frmEl.sc_custom_fieldset.parent(),allowClear:true}));
  t.frmEl.company_id.select2($.extend({}, select2Opts, {
      dropdownParent: t.frmEl.company_id.parent(),
      ajax: {
          url: t.config.url.get_company_by_user_access,
          dataType: "json",
          data: function (p) {
              return {
                  search: p.term,
                  page: p.page || 1
              };
          },
          delay: 300
      },
      placeholder: "Select Company"
  }))
  .on("change", function () {
      t.frmEl.department_head_id
          .val(null)
          .trigger("change");
  });

  t.table.on("change", ".a_department", function (e) {
    var that_dep = $(this);
    var chked = that_dep.prop("checked") ? 1 : 0;
    var message =
      chked == 1
        ? " enable department for ticketing? "
        : " disable department for ticketing? ";
    sweetAlertConfirmation({
      message: "Are you sure to" + message,
      onConfirm: function () {
        $.get(
          t.config.url.update_department +
            "/" +
            that_dep.val() +
            "/" +
            chked +
            "/" +
            companyId,
          function (data) {
            if (typeof data == "object") {
              if (data.status == "success") {
                t.reload();
                if (t.user) {
                  t.user.reload();
                }
                sweetAlert("center", "success", data);
              } else {
                sweetAlert("center", "error", data);
              }
            }
            t.dTbl.ajax.reload();
          },
        );
      },
    });
  });
  t.table.on("change", ".chkChild", function () {
    var id = $(this).val();
    if ($(this).is(":checked")) {
      if (!t.selectedDepartments.includes(id)) {
        t.selectedDepartments.push(id);
      }
    } else {
      t.selectedDepartments = t.selectedDepartments.filter(function (v) {
        return v != id;
      });
    }
  });

  t.table.on("change", ".chkParent", function () {
    var checked = $(this).prop("checked");
    t.table.find(".chkChild").each(function () {
      var id = $(this).val();
      $(this).prop("checked", checked);
      if (checked) {
        if (!t.selectedDepartments.includes(id)) {
          t.selectedDepartments.push(id);
        }
      } else {
        t.selectedDepartments = t.selectedDepartments.filter(function (v) {
          return v != id;
        });
      }
    });
  });
  t.content.on("click", ".btna-active", function () {
    if (t.selectedDepartments.length === 0) {
      // alert("Please select at least one department");
      sweetAlert("center", "warning", {msg: t.config.translations.select_department_info});
      return;
    }

    var toEnable = [];
    var alreadyEnabled = [];

    t.selectedDepartments.forEach(function (id) {
      var dept = t.departmentMap[id];
      if (dept.status == 1) {
        alreadyEnabled.push(dept.name);
      } else {
        toEnable.push(id);
      }
    });

    if (toEnable.length === 0) {
        sweetAlert("center", "info", {
            status: "info",
            msg: "All selected departments are already enabled."
        });
        return;
    }

    var msg = "Are you sure you want to enable the selected departments?";
    if (alreadyEnabled.length > 0) {
      msg += "\nThe following departments are already enabled and will be skipped: " + alreadyEnabled.join(', ');
    }

    sweetAlertConfirmation({
      message: msg,
      onConfirm: function () {
        $.post(
          t.config.url.update_departments_bulk,
          {
            _token: t.getToken(),
            ids: toEnable,
            val: 1,
            company_id: companyId,
          },
          function (data) {
            if (data.status === "success") {
              t.selectedDepartments = [];
              t.reload();
              var successMsg = data.msg || "Departments enabled successfully.";
              if (alreadyEnabled.length > 0) {
                successMsg += "\nSkipped (already enabled): " + alreadyEnabled.join(', ');
              }
              sweetAlert("center", "success", { msg: successMsg });
            } else {
              t.selectedDepartments = [];
              t.reload();
              sweetAlert("center", "error", data);
            }
          }
        );
      },
    });
  });
  t.content.on("click", ".btnd-inactive", function () {
    if (t.selectedDepartments.length === 0) {
      console.log()
      // alert("Please select at least one department");
      sweetAlert("center", "warning", {msg: config.translations.select_department_info});
      return;
    }

    var toDisable = [];
    var alreadyDisabled = [];

    t.selectedDepartments.forEach(function (id) {
      var dept = t.departmentMap[id];
      if (dept.status == 0) {
        alreadyDisabled.push(dept.name);
      } else {
        toDisable.push(id);
      }
    });

    if (toDisable.length === 0) {
      alert("All selected departments are already disabled.");
      return;
    }

    var msg = "Are you sure you want to disable the selected departments?";
    if (alreadyDisabled.length > 0) {
      msg += "\nThe following departments are already disabled and will be skipped: " + alreadyDisabled.join(', ');
    }

    sweetAlertConfirmation({
      message: msg,
      onConfirm: function () {
        $.post(
          t.config.url.update_departments_bulk,
          {
            _token: t.getToken(),
            ids: toDisable,
            val: 0,
            company_id: companyId,
          },
          function (data) {
            if (data.status === "success") {
              t.selectedDepartments = [];
              t.reload();
              var successMsg = "Departments disabled successfully.";
              if (alreadyDisabled.length > 0) {
                successMsg += "\nSkipped (already disabled): " + alreadyDisabled.join(', ');
              }
              sweetAlert("center", "success", { msg: successMsg });
            } else {
              t.selectedDepartments = [];
              t.reload();
              sweetAlert("center", "error", data);
            }
          }
        );
      },
    });
  });
  t.content.on("click", ".refresh-department", $.proxy(t.reload));
  t.content.on("keyup", ".department-search", function (e) {
    if (e.which === 13) {
      t.tableSearch(e);
    }
    if (!this.value.length && e.which !== 13) {
      t.tableSearch(e);
    }
  });
  t.content.on('click', '.add-department', $.proxy(t.addDepartment));
  t.content.on('click','#btnSubmit' ,$.proxy(t.handlesubmit));
  t.dTbl.ajax.reload();
};

var TicketConfig = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;
  t.section = $("section.content");
  t.userlist = new UserList(t.config);
  t.reportfields = new ReportFields(t.config);
  t.user = new User(t.config);
  t.department = new Department(t.config, t.user);
  t.departmentSLA = new DepartmentSLA(t.config);
  t.notification = new Notification(t.config);
  t.autoUpdate = new AutoUpdate(t.config);
  t.duplicateTicket = new DuplicateTicket(t.config);

  t.loadTicketConfiguration = function () {
    $("#eu_hide_priority").prop("checked", false);
    $("#eu_hide_assigned_to").prop("checked", false);
    $("#eu_hide_expire_at").prop("checked", false);
    $("#eu_hide_tat").prop("checked", false);
    $("#enable_vip_ticket").prop("checked", false);
    $("#auto_response_for_ticket").prop("checked", false);
    $("#enable_duplicate_issues_check").prop("checked", false);
    $("#auto_merge_duplicate_issues").prop("checked", false);
    $("#allow_user_to_continue_ticket_creation_on_duplicate").prop(
      "checked",
      false,
    );
    $("#auto_resolve_ticket").prop("checked", false);
    $("#feedback_required").val("").trigger("change");
    $("#feedback_max_rating").val("").trigger("change");
    $("#feedback_max_rating_div").addClass("d-none");
    $.ajax({
      type: "get",
      url: t.config.url.ajaxIndex,
      data: {
        company_id: companyId,
      },
      beforeSend: function () {
        $("#pageLoader").show();
      },
      success: function (res) {
        console.log(res);
        if (res.status == "success" && res.data.ticket_config) {
          if (res.data.ticket_config.eu_hide_priority == 1) {
            $("#eu_hide_priority").prop("checked", true);
          }
          if (res.data.ticket_config.eu_hide_assigned_to == 1) {
            $("#eu_hide_assigned_to").prop("checked", true);
          }
          if (res.data.ticket_config.eu_hide_expire_at == 1) {
            $("#eu_hide_expire_at").prop("checked", true);
          }
          if (res.data.ticket_config.eu_hide_tat == 1) {
            $("#eu_hide_tat").prop("checked", true);
          }
          if (res.data.ticket_config.enable_vip_ticket == 1) {
            $("#enable_vip_ticket").prop("checked", true);
          }
          let mailStatus = res.data.ticket_config.mail_all_status_changes;
          let tat_by_work = res.data.ticket_config.tat_by_work_hour;
          let department_config = res.data.ticket_config.department_config;
          let checked_cc_checkbox = res.data.ticket_config.checked_cc_checkbox;
          let sla_reminder = res.data.ticket_config.sla_reminder;
          let mark_technician_as_cc_in_ticket_create = res.data.ticket_config.mark_technician_as_cc_in_ticket_create;
          let sla_notification_type =
            res.data.ticket_config.sla_notification_type;
          $("#mail_all_status_changes")
            .val(mailStatus == 1 ? "1" : 0)
            .trigger("change.select2");
          $("#tat_by_work_hour")
            .val(tat_by_work == 1 ? "1" : 0)
            .trigger("change.select2");
          $("#kd_auto_suggestion")
            .val(res.data.ticket_config.kd_auto_suggestion == 1 ? "1" : 0)
            .trigger("change.select2");
          $("#auto_archive_ticket_after_days").val(
            res.data.ticket_config.auto_archive_ticket_after_days
              ? res.data.ticket_config.auto_archive_ticket_after_days
              : "",
          );
          $("#checked_cc_checkbox")
            .val(checked_cc_checkbox == 1 ? "1" : 0)
            .trigger("change.select2");
          $("#auto_archive_user_activity_after_days").val(
            res.data.ticket_config.auto_archive_user_activity_after_days
              ? res.data.ticket_config.auto_archive_user_activity_after_days
              : "",
          );
          $("#sla_reminder")
            .val(sla_reminder == 1 ? "1" : "0")
            .trigger("change.select2");
          $("#sla_reminder_hr").val(
            res.data.ticket_config.sla_reminder_hr
              ? res.data.ticket_config.sla_reminder_hr
              : "",
          );
          $("#sla_notification_type")
            .val(sla_notification_type == 1 ? "1" : "2")
            .trigger("change.select2");
          $("#mark_technician_as_cc_in_ticket_create")
            .val(mark_technician_as_cc_in_ticket_create == 1 ? "1" : "2")
            .trigger("change.select2");

          let ticketInitials = res.data.ticket_config.ticket_initial;
          if (ticketInitials) {
            let initialsArray = JSON.parse(ticketInitials);
            $("#ticket_id_initials").val(initialsArray).trigger("change");
          } else {
            $("#ticket_id_initials").val([]).trigger("change");
          }
          $("#ticket_id_initial_separator").val(
            res.data.ticket_config.ticket_initial_separator
              ? res.data.ticket_config.ticket_initial_separator
              : "",
          );
          let feedback_required = res.data.ticket_config.feedback_required;
          let feedback_max_rating = res.data.ticket_config.feedback_max_rating;
          $("#feedback_required").val(feedback_required != null ? feedback_required.toString() : "").trigger("change");
          if (feedback_required == 1) {
              $("#feedback_max_rating_div").removeClass("d-none");
          } else {
              $("#feedback_max_rating_div").addClass("d-none");
          }
          $("#feedback_max_rating").val(feedback_max_rating != null ? feedback_max_rating.toString() : "").trigger("change");

          let startTimeAMPM = formatTimeToAMPM(
            res.data.ticket_config.default_work_start
              ? res.data.ticket_config.default_work_start
              : "",
          );
          let endTimeAMPM = formatTimeToAMPM(
            res.data.ticket_config.default_work_end
              ? res.data.ticket_config.default_work_end
              : "",
          );

          $("#default_work_start").val(startTimeAMPM);
          $("#default_work_end").val(endTimeAMPM);

          let workDays = res.data.default_work_days;
          if (workDays) {
            $.each(workDays, function (day, value) {
              let checkboxId = "#default_work_days_" + day;

              if (value == 1) {
                $(checkboxId).prop("checked", true);
              } else {
                $(checkboxId).prop("checked", false);
              }
            });
          }
        }
        if (
          res.status == "success" &&
          (res.data.report_fields_list || res.data.report_fields_map)
        ) {
          let fieldsList = res.data.report_fields_list;
          let fieldsMap = res.data.report_fields_map;
          let html = "";
          $.each(fieldsList, function (_, field) {
            let checked = fieldsMap[field.code] === 1 ? "checked" : "";
            $(`#${field.code}`).prop("checked", checked === "checked");
            // html += `
            //     <div class="col-lg-2 checkbox-container">
            //         <div>
            //             <label>
            //                 <input type="checkbox" class="blue" id="${field.code}" name="${field.code}" value="1" ${checked} /> ${field.lbl}
            //             </label>
            //         </div>
            //     </div>
            // `;
          });
          // $('#reportFieldsContainer').html(html);
        }
        if (res.status == "success" && res.data.auto_update) {
          let min_before_escalation_to_handler =
            res.data.auto_update.min_before_escalation_to_handler;
          let min_before_escalation_to_user =
            res.data.auto_update.min_before_escalation_to_user;
          let min_before_breached_to_technician =
            res.data.auto_update.min_before_breached_to_technician;
          let min_before_close_ticket_to_handler =
            res.data.auto_update.min_before_close_ticket_to_handler;
          $("#min_before_escalation_to_handler").val(
            min_before_escalation_to_handler
              ? min_before_escalation_to_handler
              : 0,
          );
          $("#min_before_escalation_to_user").val(
            min_before_escalation_to_user ? min_before_escalation_to_user : 0,
          );
          $("#min_before_breached_to_technician").val(
            min_before_breached_to_technician
              ? min_before_breached_to_technician
              : 0,
          );
          $("#min_before_close_ticket_to_handler").val(
            min_before_close_ticket_to_handler
              ? min_before_close_ticket_to_handler
              : 0,
          );

          if (res.data.auto_update.auto_response_for_ticket == 1) {
            $("#auto_response_for_ticket").prop("checked", true);
          }
          if (res.data.auto_update.auto_resolve_ticket == 1) {
            $("#auto_resolve_ticket").prop("checked", true);
          }
          if (res.data.auto_update.enable_duplicate_issues_check == 1) {
            $("#enable_duplicate_issues_check").prop("checked", true);
          }
          if (res.data.auto_update.auto_merge_duplicate_issues == 1) {
            $("#auto_merge_duplicate_issues").prop("checked", true);
          }
          if (
            res.data.auto_update
              .allow_user_to_continue_ticket_creation_on_duplicate == 1
          ) {
            $("#allow_user_to_continue_ticket_creation_on_duplicate").prop(
              "checked",
              true,
            );
          }
        }
      },
      complete: function () {
        $("#pageLoader").hide();
      },
      error: function () {
        $("#pageLoader").hide();
      },
    });
  };

  function formatTimeToAMPM(time24) {
    if (!time24) return "";
    let [hours, minutes] = time24.split(":");
    hours = parseInt(hours, 10);
    let ampm = hours >= 12 ? "PM" : "AM";
    hours = hours % 12 || 12; // 0 => 12
    return hours + ":" + minutes + " " + ampm;
  }

  t.loadTicketConfiguration();
  // t.emailres = new EmailRes(t.config);
};

var User = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;
  t.content = $(".main-content");
  t.userLists = t.content.find("#userLists");
  t.departmentList = t.content.find("#previllege_department_list");
  t.actionList = t.content.find("#previllege_action_list");
  t.ticketCreationList = t.content.find("#previllege_ticket_creation");
  t.ticketMergeList = t.content.find("#previllege_ticket_merge");
  t.userID = t.content.find("#tcf_selected_user");

  t.table = t.content.find("#users");
  t.handler_user_list = t.content.find("#handler_user_list");

  t.paginationWrapper = t.content.find(".tcf-pagination");
  t.currentPage = 1;
  t.perPage = 8;
  t.start = 0;
  t.totalRecords = 0;
  let techData = [];

  t.tblHelpers = {
    user_info: function () {
      return function (d) {
        return (
          "<div><strong>" +
          d.full_name +
          "</strong><br><span>" +
          d.username +
          "</span></div>" +
          "<div><em>" +
          (d.department || "") +
          "</em></div>" +
          '<div class="checkbox-container">' +
          '<label for="check_all_' +
          d.id +
          '" class="control-label mar-rgt"><input type="checkbox" class="checkall_previlege" id="check_all_' +
          d.id +
          '" data-belongs="department_' +
          d.id +
          '" data-user="' +
          d.id +
          '" class=""><span class=""> Check All</span></label></div>'
        );
      };
    },
    actions: function () {
      return function (d) {
        const departments = t.config.departments || [];
        const action_controls = t.config.action_controls || {};
        const ticket_creation = t.config.ticket_creation;
        const ticket_merge = t.config.ticket_merge;
        var r = [];
        var privileges =
          d.privileges != "" && d.privileges != null && d.privileges != "null"
            ? d.privileges.split(",")
            : [];

        $.each(departments, function (i, dep) {
          var checked =
            $.inArray(dep.id.toString(), privileges) >= 0 ? "checked" : "";
          var a_dep =
            '<label for="department_' +
            d.id +
            "_" +
            dep.id +
            '" class="control-label mar-rgt"><input type="checkbox" name="department" value="' +
            dep.id +
            '" id="department_' +
            d.id +
            "_" +
            dep.id +
            '" data-belongs="department_' +
            d.id +
            '" class="department_previlege" ' +
            checked +
            ' /><span class=""> ' +
            dep.name +
            "</span></label>";
          r.push(a_dep);
        });

        var ac = [];
        $.each(action_controls, function (action_code, action_name) {
          var checked = d.action_controls[action_code] === 1 ? "checked" : "";
          var a_ctrl =
            '<label for="action_control_' +
            d.id +
            "_" +
            action_code +
            '" class="control-label mar-rgt"><input type="checkbox" name="action_control" value="' +
            action_code +
            '" id="action_control_' +
            d.id +
            "_" +
            action_code +
            '" data-belongs="department_' +
            d.id +
            '" class="action_control" ' +
            checked +
            ' /><span class=""> ' +
            action_name +
            "</span></label>";
          ac.push(a_ctrl);
        });
        var ac2 = [];
        $.each(ticket_creation, function (action_code, action_name) {
          var checked = d.action_controls[action_code] === 1 ? "checked" : "";
          var a_ctrl =
            '<label for="action_control_' +
            d.id +
            "_" +
            action_code +
            '" class="control-label mar-rgt"><input type="checkbox" name="action_control" value="' +
            action_code +
            '" id="action_control_' +
            d.id +
            "_" +
            action_code +
            '" data-belongs="department_' +
            d.id +
            '" class="action_control" ' +
            checked +
            ' /><span class=""> ' +
            action_name +
            "</span></label>";
          ac2.push(a_ctrl);
        });
        var ac3 = [];
        $.each(ticket_merge, function (action_code, action_name) {
          var checked = d.action_controls[action_code] === 1 ? "checked" : "";
          var a_ctrl =
            '<label for="action_control_' +
            d.id +
            "_" +
            action_code +
            '" class="control-label mar-rgt"><input type="checkbox" name="action_control" value="' +
            action_code +
            '" id="action_control_' +
            d.id +
            "_" +
            action_code +
            '" data-belongs="department_' +
            d.id +
            '" class="action_control" ' +
            checked +
            ' /><span class=""> ' +
            action_name +
            "</span></label>";
          ac3.push(a_ctrl);
        });

        var str =
          '<div><strong>Departments</strong></div><div class="checkbox-grid checkbox-container">' +
          r.join(" ") +
          '</div><div style="margin-top: 15px;"><strong>Action Controls</strong></div><div class="checkbox-grid checkbox-container">' +
          ac.join(" ") +
          '</div><div style="margin-top: 15px;"><strong>Ticket Creation</strong></div><div class="checkbox-container">' +
          ac2.join(" ") +
          '</div><div style="margin-top: 15px;"><strong>Ticket Merge</strong></div><div class="checkbox-container">' +
          ac3.join(" ") +
          '</div><div><button type="button" id="department_' +
          d.id +
          '" class="update_user_privilege hide btn btn-theme-red" data-user="' +
          d.id +
          '">Save Changes</button></div>';
        return str;
      };
    },
  };

  t.table.on("change", ".checkall_previlege", function (e) {
    var user_id = $(this).attr("data-user");

    if ($(this).prop("checked")) {
      $.each(t.config.departments, function (i, m) {
        var el_id = "#department_" + user_id + "_" + m.id;
        t.table.find(el_id).prop("checked", true);
      });
    } else {
      $.each(t.config.departments, function (i, m) {
        var el_id = "#department_" + user_id + "_" + m.id;
        t.table.find(el_id).prop("checked", false);
      });
    }

    var actionSelector = '[id^="action_control_' + user_id + '"]';
    t.table.find(actionSelector).prop("checked", $(this).prop("checked"));

    var b = $(this).attr("data-belongs");
    t.table.find("#" + b).removeClass("hide");
  });

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  t.reload = function () {
    t.loadUsers();
  };

  t.export = function (e) {
    let userIds = techData;
    e.preventDefault();
    var v = $.trim($("#users_wrapper .plain-search").val());
    window.location =
      t.config.url.download_url +
      escape(v) +
      "&company_id=" +
      companyId ;
      // + "&user_ids=" +
      // userIds.join(",")
  };

  t.getInitials = function (fullName) {
    if (!fullName) return "";

    let words = fullName.trim().split(" ");
    if (words.length === 1) {
      return words[0].substring(0, 2).toUpperCase();
    }

    // first + last name initials
    let first = words[0][0];
    let last = words[words.length - 1][0];
    return (first + last).toUpperCase();
  };

  t.init = function () {
    t.loadUsers();
  };

  t.loadUsers = function () {
    techData = [];
    t.userLists.empty();
    t.departmentList.empty();
    t.actionList.empty();
    t.ticketCreationList.empty();
    t.ticketMergeList.empty();

    $.ajax({
      url: t.config.url.get_users,
      type: "POST",
      processing: true,
      serverSide: true,
      deferLoading: true,
      searching: false,
      lengthChange: false,
      data: {
        _token: t.getToken(),
        company_id: companyId,
        search: $.trim(t.content.find(".tcf-user-search-input").val()),
        page: t.currentPage,
        per_page: t.perPage,
        start: t.start,
      },
      beforeSend: function () {
        $("#pageLoader").show();
      },
      success: function (response) {
        let html = "";
        let prev_dept = "";
        let prev_ctrl = "";
        let prev_ticket_creation = "";
        let prev_ticket_merge = "";
        let activeClass = '';
        let setActive = 0;

        t.totalRecords = response.recordsFiltered; 
        t.totalRecordsAll = response.recordsTotal;

        if (!response.data || response.data.length === 0) {
          t.userLists.html(`
            <div class="text-center py-4">                  
              <div class="mt-2 text-muted fw-semibold">
                  ${t.config.translations.user_not_found}
              </div>
            </div>
          `);
          $("#user_details_panel").hide();
          $("#user_detail_empty").removeClass("d-none").show();

          // t.renderPagination(1, 0);
          return;         
        }
         $("#user_detail_empty").hide();
          $("#user_details_panel").show();
        $.each(response.data, function (i, user) {
          let initials = "";
          let techprivileges = {};

          $.each(user.a.action_controls, function (i, v) {
            techprivileges[i] = v;
          });
          techData.push(user.a.id);
          if (user.a.full_name) {
            initials = t.getInitials(user.a.full_name);
          }
          activeClass = (i == 0 ? "active" : "");

          html += `<div tech-data = "${user.a.id}" id="technician-list_${user.a.id}" data-userdeptprivileges="${user.a.privileges ?? ""}" data-useraccessprivileges='${JSON.stringify(techprivileges)}' class="technician_list tcf-user-item ${activeClass} && ${activeClass} ? 'active' : '' " onclick="tcfSelectUser(this,'${user.a.username}')">
                        <div class="tcf-user-av ${initials}">${initials}</div>
                        <div>
                            <div class="tcf-user-name">${user.a.full_name ?? ""}</div>
                            <div class="tcf-user-email">${user.a.username ?? ""}</div>
                        </div>
                        <div class="privelege-status" ></div>
                        </div>
                    </div>`;
          if (activeClass == "active") {
            setActive = 1;
          }
        });

        t.userLists.html(html);

        $.each(response.dept.departments, function (i, department) {
          prev_dept += `<label class="tcf-perm-item">
                        <input type="checkbox" class="tcf-dept-perm privellege_dept form-check-input" id="predepartment_${department.id}" data-id="${department.id}"  value="${department.id}"  name="department">
                        ${department.name}
                    </label>`;
        });
        t.departmentList.html(prev_dept);

        $.each(response.dept.action_controls, function (i, control) {
          prev_ctrl += `<label class="tcf-perm-item">
                        <input type="checkbox" class="tcf-dept-perm privellege_action form-check-input" id="action_control_${i}" data-key="${i}"  value="${i}"  name="department">
                        ${control}
                    </label>`;
        });
        t.actionList.html(prev_ctrl);

        $.each(response.dept.ticket_creation, function (i, ticket_creation) {
          prev_ticket_creation += `<label class="tcf-perm-item">
                        <input type="checkbox" class="tcf-dept-perm privellege_action form-check-input" id="action_control_${i}" data-key="${i}"  value="${i}"  name="department">
                        ${ticket_creation}
                    </label>`;
        });
        t.ticketCreationList.html(prev_ticket_creation);

        $.each(response.dept.ticket_merge, function (i, ticket_merge) {
          prev_ticket_merge += `<label class="tcf-perm-item">
                        <input type="checkbox" class="tcf-dept-perm privellege_action form-check-input" id="action_control_${i}" data-key="${i}"  value="${i}"  name="department">
                        ${ticket_merge}
                    </label>`;
        });
        t.ticketMergeList.html(prev_ticket_merge);
        // PAGINATION
        t.renderPagination(response.current_page, response.recordsFiltered);

        let tech = t.content.find('.technician_list').first();
        if (tech.length) {
            tech.trigger('click');
        }



      },
      complete: function () {
        $("#pageLoader").hide();
      },
      error: function () {
        $("#pageLoader").hide();
      },
    });
  };

  t.getToken = function () {
    return $('meta[name="csrf-token"]').attr("content");
  };

  t.init();
  t.fetchTechncianDepartmentAndControls = function () {
    let technicianId = $(this).attr("tech-data");
    $("#tcf_selected_user").val(technicianId);
    $(
      ".privellege_action:checked, .privellege_dept:checked, .priv_select_all:checked",
    )
      .prop("checked", false)
      .trigger("change");
    $.each($(this).attr("data-userdeptprivileges").split(","), function (i, v) {
      $(`#predepartment_${v}`).prop("checked", true);
    });

    let privileges = $(this).data("useraccessprivileges");
    $.each(privileges, function (key, value) {
      if (value == 1) {
        $(`#action_control_${key}`).prop("checked", true);
      }
    });
  };
  t.updateHandlerLimits = function () {
    let formData = new FormData();
    formData.append("_token", t.getToken());
    formData.append("user_id", t.userID.val());
    formData.append("company_id", companyId);
    $(".privellege_dept").each(function () {
      let id = $(this).data("id");
      formData.append(`departments[${id}]`, $(this).prop("checked") ? 1 : 0);
    });

    $(".privellege_action").each(function () {
      let key = $(this).data("key");
      formData.append(
        `action_controls[${key}]`,
        $(this).prop("checked") ? 1 : 0,
      );
    });
    // store the priv data for technician
    var http = $.ajax({
      url: t.config.url.update_privilege,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.dTbl.ajax.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
  };

  t.content.on("keypress", "#tcf-userprivileges-search-input", function (e) {
      if (e.which === 13) { 
          e.preventDefault();
          t.currentPage = 1;
          t.start = 0;
          t.loadUsers();
      }
  });
  
  t.renderPagination = function (currentPage, filteredCount) {
      let html = "";

      html += `
          <button class="tcf-pg-btn" ${t.currentPage == 1 ? "disabled" : ""} data-page="${t.currentPage - 1}">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" class="me-2">
                  <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Prev
          </button>`;

      var totalPage = Math.ceil(filteredCount / t.perPage);

      html += `
          <button class="tcf-pg-btn" ${t.currentPage == totalPage ? "disabled" : ""} data-page="${t.currentPage + 1}">
              Next
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" class="ms-2">
                  <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
          </button>`;

      t.paginationWrapper.html(html);

      let start = (t.currentPage - 1) * t.perPage + 1;
      let end = Math.min(t.currentPage * t.perPage, t.totalRecords);

      if (t.totalRecords === 0) {
          $("#priv_tech_user_count").text("Showing 0 entries");
      } else {
          $("#priv_tech_user_count").text(`Showing ${start}-${end} of ${t.totalRecords} entries`);
      }
  };

  // PAGINATION CLICK
  t.content.on("click", ".tcf-pg-btn", function () {
    let page = $(this).data("page");
    if (page) {
      t.currentPage = page;
      t.start = t.perPage * (page - 1);
      t.loadUsers();
    }
  });
  
  t.content.on("click", ".refresh_tech_prev", $.proxy(t.reload));
  // t.content.on("click", ".tcf-user-search-input", $.proxy(t.tableSearch));
  t.content.on("click", ".btn-export-config", $.proxy(t.export));
  t.content.on("click", "#handlerLimits", $.proxy(t.updateHandlerLimits));
  t.content.on("click",".technician_list",$.proxy(t.fetchTechncianDepartmentAndControls));

  t.content.on("keyup", ".search-prev-dept", function () {
    let value = $(this).val().toLowerCase();

    t.departmentList.find(".tcf-perm-item").each(function () {
      let name = $(this).text().toLowerCase();
      $(this).toggle(name.includes(value));
    });
    t.actionList.find(".tcf-perm-item").each(function () {
      let name = $(this).text().toLowerCase();
      $(this).toggle(name.includes(value));
    });
    t.ticketCreationList.find(".tcf-perm-item").each(function () {
      let name = $(this).text().toLowerCase();
      $(this).toggle(name.includes(value));
    });
    t.ticketMergeList.find(".tcf-perm-item").each(function () {
      let name = $(this).text().toLowerCase();
      $(this).toggle(name.includes(value));
    });
  });
};

var EmailRes = function (config) {
  var t = this;
  t.config = config;

  t.content = $("section.content");
  t.mdl = t.content.find("#email_restriction");
  t.frm = t.mdl.find("#email");

  t.frmEl = {};
  t.mdl.emailcover = t.frm.find(".emailcover");
  t.frmEl.auto_create_from_email = t.frm.find("#auto_create_from_email");
  t.frmEl.validate_cert = t.frm.find("#validate_cert");
  t.frmEl.ebts_username = t.frm.find("#ebts_username");
  t.frmEl.ebts_password = t.frm.find("#ebts_password");
  t.frmEl.ebts_host = t.frm.find("#ebts_host");
  t.frmEl.ebts_port = t.frm.find("#ebts_port");
  t.frmEl.ebts_encryption = t.frm.find("#ebts_encryption");
  t.mdl.domainallowcover = t.frm.find(".domainallowcover");
  t.mdl.domainallcover = t.frm.find(".domainallcover");
  t.frmEl.ticketing_restricted = t.frm.find("#ticketing_restricted");
  t.frmEl.ticketing_allowed_domains = t.frm.find("#ticketing_allowed_domains");
  t.frmEl.ticketing_blocked_domains = t.frm.find("#ticketing_blocked_domains");
  t.frmEl.restricted_words = t.frm.find("#restricted_words");
  t.mdl.lblemail = t.frm.find("label[for=email]");
  t.mdl.lblemailpass = t.frm.find("label[for=email_pass]");
  t.mdl.lblemailhos = t.frm.find("label[for=email_hos]");
  t.mdl.lblemailpor = t.frm.find("label[for=email_por]");

  t.btn = {};
  t.btn.update = t.frm.find("#update");
  t.httpCall = true;
  t.httpPostPath = t.config.url.email_based_ticketing;

  t.resetFrm = {};

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  t.mdl.domainRestriction = function () {
    var acfe = t.frmEl.auto_create_from_email.val();
    if (acfe == 1 && t.frmEl.ticketing_restricted.val() == 1) {
      t.mdl.domainallowcover.removeClass("hide");
      t.mdl.domainallcover.addClass("hide");
      t.frmEl.ticketing_allowed_domains.rules("add", { required: true });
    } else if (acfe == 1 && t.frmEl.ticketing_restricted.val() == 2) {
      t.mdl.domainallowcover.addClass("hide");
      t.mdl.domainallcover.removeClass("hide");
    } else {
      t.mdl.domainallowcover.addClass("hide");
      t.mdl.domainallcover.addClass("hide");
    }
  };

  t.mdl.emailRestriction = function () {
    if (t.frmEl.auto_create_from_email.val() == 2) {
      t.mdl.domainallowcover.addClass("hide");
      t.mdl.domainallcover.addClass("hide");
      t.mdl.emailcover.addClass("hide");
      // t.frmEl.ebts_username.val(null);
      // t.frmEl.ebts_password.val(null);
      // t.frmEl.ebts_host.val(null);
      // t.frmEl.ebts_port.val(null);
      // t.frmEl.ticketing_allowed_domains.val(null);
      // t.frmEl.ticketing_blocked_domains.val(null);
      // t.frmEl.restricted_words.val(null);

      // t.frmEl.ebts_username.rules("remove","required");
      // t.frmEl.ebts_host.rules("remove","required");
      // t.frmEl.ebts_port.rules("remove","required");
      // t.frmEl.ebts_password.rules("remove","required");
    } else if (t.frmEl.auto_create_from_email.val() == 1) {
      t.mdl.emailcover.removeClass("hide");
      t.mdl.lblemail.addClass("mandatory");
      t.mdl.lblemailpass.addClass("mandatory");
      t.mdl.lblemailhos.addClass("mandatory");
      t.mdl.lblemailpor.addClass("mandatory");
      t.mdl.domainRestriction();
    }
  };

  t.resetFrm = function () {
    t.mdl.emailRestriction();
  };

  t.handlesubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var frmData = new FormData(t.frm[0]);
    frmData.append("_token", t.getToken());
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.frmEl.ebts_password.val("");
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.frmValidator = t.frm.validate({
    onsubmit: false,
    rules: {
      ebts_username: {
        required: true,
        acceptable_spcl_chr: true,
      },
      ebts_host: {
        required: true,
        acceptable_spcl_chr: true,
      },
      ebts_port: {
        required: true,
        str_name: true,
      },
      ebts_password: {
        required: true,
        acceptable_spcl_chr: true,
      },
      ebts_encryption: {
        str_name: true,
      },
      validate_cert: {
        str_name: true,
      },
    },
  });

  var select2Opts = { width: "100%" };
  t.frmEl.auto_create_from_email
    .select2(select2Opts)
    .on("change", $.proxy(t.mdl.emailRestriction))
    .trigger("change");
  t.frmEl.validate_cert.select2(select2Opts);
  t.frmEl.ebts_encryption.select2(select2Opts);
  t.frmEl.ticketing_restricted
    .select2(select2Opts)
    .on("change", $.proxy(t.mdl.domainRestriction))
    .trigger("change");

  t.btn.update.on("click", $.proxy(t.handlesubmit));
};

var OutGoingMail = function (config) {
  var t = this;
  t.config = config;

  t.content = $("section.content");
  t.mdl = t.content.find("#out_going_email");
  t.frm = t.mdl.find("#out_going_email_form");

  t.frmEl = {};
  t.mdl.emailcover = t.frm.find(".emailcover");
  t.frmEl.mail_service_enabled = t.frm.find("#mail_service_enabled");
  t.frmEl.mail_driver = t.frm.find("#mail_driver");
  t.frmEl.mail_host = t.frm.find("#mail_host");
  t.frmEl.mail_port = t.frm.find("#mail_port");
  t.frmEl.mail_encryption = t.frm.find("#mail_encryption");
  t.frmEl.mail_username = t.frm.find("#mail_username");
  t.frmEl.mail_password = t.frm.find("#mail_password");
  t.frmEl.mail_from_address = t.frm.find("#mail_from_address");
  t.frmEl.mail_from_name = t.frm.find("#mail_from_name");

  t.btn = {};
  t.btn.update = t.frm.find("#update");
  t.httpCall = true;
  t.httpPostPath = t.config.url.outgoing_email;

  t.resetFrm = {};

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  t.mdl.emailRestriction = function () {
    if (t.frmEl.mail_service_enabled.val() == 1) {
      t.mdl.emailcover.addClass("hide");
    } else if (t.frmEl.mail_service_enabled.val() == 0) {
      t.mdl.emailcover.removeClass("hide");
    }
  };

  t.resetFrm = function () {
    t.mdl.emailRestriction();
  };

  t.handlesubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var frmData = new FormData(t.frm[0]);
    frmData.append("_token", t.getToken());
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.frmValidator = t.frm.validate({
    onsubmit: false,
    rules: {
      mail_host: {
        required: true,
        acceptable_spcl_chr: true,
      },
      mail_port: {
        required: true,
        acceptable_spcl_chr: true,
      },
      mail_username: {
        required: true,
        acceptable_spcl_chr: true,
      },
      mail_password: {
        required: true,
        acceptable_spcl_chr: true,
      },
      mail_from_address: {
        required: true,
        acceptable_spcl_chr: true,
      },
      mail_from_name: {
        required: true,
        acceptable_spcl_chr: true,
      },
    },
  });

  var select2Opts = { width: "100%" };
  t.frmEl.mail_service_enabled
    .select2(select2Opts)
    .on("change", $.proxy(t.mdl.emailRestriction))
    .trigger("change");
  t.frmEl.mail_driver.select2(select2Opts);
  t.frmEl.mail_encryption.select2(select2Opts);

  t.btn.update.on("click", $.proxy(t.handlesubmit));
};

var DepartmentSLA = function (config) {
  var t = this;
  t.config = config;
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  t.content = $("section.content");
  t.mdl = t.content.find("#departmentSLAModal");
  t.frm = t.mdl.find("#departmentSLA");
  t.notification = t.mdl.find("#notification");
  t.tab = t.content.find("#customSLAConfig-tab");
  t.table = t.tab.find("#lgTbl");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update;

  t.frmEl = {};
  t.frmEl.sla_id = t.frm.find("#sla_id");
  t.frmEl.company_id = t.frm.find("#company_id");
  t.frmEl.department_id = t.frm.find("#department_id");
  t.frmEl.location_id = t.frm.find("#location_id");
  t.frmEl.internal_place_id = t.frm.find("#internal_place_id");
  t.frmEl.tat_by_hr = t.frm.find("#tat_by_hr");
  t.frmEl.work_start = t.frm.find("#work_start");
  t.frmEl.work_end = t.frm.find("#work_end");
  t.frmEl.working_days = t.frm.find("#working_days");

  t.btn = {};

  t.getToken = function () {
    return $("head meta[name='csrf-token']").attr("content");
  };

  var select2Opts = { width: "100%" };
  t.frmEl.working_days.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.frmEl.working_days.parent(),
      placeholder: "Select days",
      allowclear: true,
    }),
  );
  t.frmEl.tat_by_hr.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.frmEl.tat_by_hr.parent(),
      placeholder: "Select TAT By Hour",
    }),
  );
  t.frmEl.work_start.mdtimepicker({ twelvehour: true });
  t.frmEl.work_end.mdtimepicker({ twelvehour: true });

  t.frmEl.company_id
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
        ajax: {
          url: t.config.url.getCompanyByUserAccess,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
            };
          },
          delay: 200,
        },
        placeholder: "select company",
      }),
    )
    .on("change", function () {
      t.frmEl.department_id.val(null).trigger("change");
      t.frmEl.location_id.val(null).trigger("change");
      t.frmEl.internal_place_id.val(null).trigger("change");
    });
  t.frmEl.department_id.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.frmEl.department_id.parent(),
      ajax: {
        url: t.config.url.department,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: t.frmEl.company_id.val(),
          };
        },
      },
      placeholder: "select department",
      allowClear: true,
    }),
  );
  t.frmEl.location_id.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.frmEl.location_id.parent(),
      ajax: {
        url: t.config.url.getLocationByQuery,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: t.frmEl.company_id.val(),
          };
        },
        delay: 200,
      },
      placeholder: "select location",
      allowClear: true,
    }),
  );
  t.frmEl.location_id.on("change", function () {
    let locationId = $(this).val();
    t.frmEl.internal_place_id.val(null).trigger("change");
    if (t.frmEl.internal_place_id.hasClass("select2-hidden-accessible")) {
      t.frmEl.internal_place_id.select2("destroy");
    }
    t.initInternalPlace(locationId);
  });
  t.initInternalPlace = function (locationId = null) {
    let location_ids = locationId || t.frmEl.location_id.val();
    if (!Array.isArray(location_ids)) {
      location_ids = location_ids ? [location_ids] : [];
    }
    if (!location_ids.length) {
      t.frmEl.internal_place_id.select2(
        $.extend({}, select2Opts, {
          dropdownParent: t.frmEl.internal_place_id.parent(),
          placeholder: "Select Internal Place",
        }),
      );
      return;
    }
    t.frmEl.internal_place_id.select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.frmEl.internal_place_id.parent(),
        ajax: {
          url:
            t.config.url.getInternalPlaceByLocation +
            "/" +
            location_ids.join(","),
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
              company_id: t.frmEl.company_id.val(),
            };
          },
          delay: 200,
        },
        placeholder: "Select Internal Place",
        allowClear: true,
      }),
    );
  };
  t.initInternalPlace();
  t.dTbl = t.table.DataTable({
    autoWidth: false,
    colReorder: true,
    aoColumnDefs: [
      {
        bSortable: false,
        aTargets: [0, 8],
      },
      {
        targets: 0,
        render: function (d) {
          var x = d;
          var a = [];
          a.push(
            "<button class='btn dtActbtn open-edit-modal' data-placement='right' data-toggle='tooltip' data-original-title='Edit' data-id=\"" +
              d.id +
              '"  data-name="' +
              d.name +
              '"  ><i class="fa fa-pencil"></i></button>',
          );
          a.push(
            "<button class='btn dtActbtn dtActDel open-delete' data-placement='right' data-toggle='tooltip' data-original-title='Delete' data-id=\"" +
              d.id +
              '" ><i class="fa fa-trash"></i></button>',
          );
          return a.join("");
        },
      },
    ],
    order: [[10, "desc"]],
    serverSide: true,
    processing: true,
    responsive: true,
    ajax: {
      url: config.url.ajaxDepartmentSLA,
      type: "POST",
      data: function (d) {
        d._token = t.getToken();
        d.search = $("#lgTbl_wrapper .plain-search").val();
        if (d.order && d.order.length > 0) {
          let orderInfo = d.order[0];
          let columnIndex = orderInfo.column;
          let direction = orderInfo.dir;
          let columnName =
            d.columns[columnIndex].name || d.columns[columnIndex].data;
          d.sorted_column_name = columnName;
          d.sorted_direction = direction;
        }
      },
    },
    columns: [
      { data: null },
      { data: "department_name" },
      { data: "location_name" },
      { data: "internal_place_name" },
      { data: "company_name" },
      {
        data: "tat_by_work_hour",
        render: function (data) {
          return data == 1 ? "Yes" : "No";
        },
      },
      { data: "default_work_start" },
      { data: "default_work_end" },
      { data: "default_work_days" },
      { data: "created_at" },
      { data: "updated_at" },
    ],
    fnInitComplete: function (oSettings, json) {
      var api = this.api();
      var searchBox =
        '<div class="input-group table-search-btns">' +
        '<input type="text" class="form-control searchbox plain-search" placeholder="Press enter with search text" />' +
        '<span class="input-group-addon" id="btn-searchbox" data-toggle="tooltip" data-placement="left" data-original-title="Search"><i class="ps-icon plain-search-icon"></i></span>' +
        '<span class="input-group-addon" id="btn-reload-list" data-toggle="tooltip" data-placement="left" data-original-title="Refresh List"><i class="ps-icon fa fa-refresh"></i></span>' +
        '<span class="input-group-addon btn-add-new-department-sla" data-toggle="tooltip" data-placement="left" data-original-title="Add"><i class="ps-icon fa fa-plus"></i></span>' +
        "</div>";

      $("#lgTbl_wrapper").removeClass("form-inline");
      t.table.closest("div").addClass("table-responsive");
      $(searchBox).insertBefore("#lgTbl_filter");
      $("#lgTbl_filter").remove();
      $("#lgTbl_length").find("select").select2();
      $("#lgTbl_wrapper .plain-search").on("keyup", function (e) {
        if (e.keyCode == 13 || this.value.length == 0) {
          var v = $(this).validate_str_param();
          if (v === false) {
            alert("Please enter a valid value for search");
            return false;
          }
          api.search(this.value).draw();
        }
      });
    },
  });

  t.frmValidator = t.frm.validate({
    onsubmit: false,
    rules: {
      company_id: {
        required: true,
      },
      department_id: {
        required: true,
      },
      tat_by_hr: {
        required: true,
      },
      work_start: {
        required: true,
      },
      work_end: {
        required: true,
      },
      "working_days[]": {
        required: true,
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent("div"));
    },
  });

  t.handlesubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var frmData = new FormData(t.frm[0]);
    frmData.append("token", t.getToken());
    let id = t.frmEl.sla_id.val();

    let url = id
      ? t.config.url.updateDepartmentSLA + "/" + id
      : t.config.url.departmentSLA;
    var http = $.ajax({
      url: url,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          t.mdl.modal("hide");
          t.dTbl.ajax.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        status: "error",
        message: "An error occurred while saving the department SLA.",
      };
      sweetAlert("center", "error", data);
    });
  };

  t.resetFrm = function () {
    t.frmEl.sla_id.val("");
    t.frmEl.company_id.val(null).trigger("change");
    t.frmEl.department_id.val(null).trigger("change");
    t.frmEl.location_id.val(null).trigger("change");
    t.frmEl.internal_place_id.val(null).trigger("change");
    t.frmEl.tat_by_hr.val(null).trigger("change");
    t.frmEl.work_start.val(null).trigger("change");
    t.frmEl.work_end.val(null).trigger("change");
    t.frmEl.working_days.val(null).trigger("change");
    t.frmValidator.resetForm();
  };

  t.addModel = function (e) {
    e.preventDefault();
    t.resetFrm();
    t.frmEl.company_id.val(companyId);
    t.mdl.modal("show");
  };
  t.reload = function (e) {
    e.preventDefault();
    t.dTbl.ajax.reload();
  };
  t.editModel = function (e) {
    e.preventDefault();
    t.resetFrm();
    let id = $(this).data("id");
    t.frmEl.sla_id.val(id);
    $.ajax({
      url: config.url.getDepartmentSLA + "/" + id,
      type: "GET",
      success: function (res) {
        let d = res.data;
        if (res.status !== "success") {
          sweetAlert("center", "error", res);
          return;
        }
        if (d.company_id) {
          let option = new Option(d.company_name, d.company_id, true, true);
          t.frmEl.company_id.append(option).trigger("change");
        }

        if (d.department_id) {
          let option = new Option(
            d.department_name,
            d.department_id,
            true,
            true,
          );
          t.frmEl.department_id.append(option).trigger("change");
        }

        if (d.location_id) {
          let option = new Option(d.location_name, d.location_id, true, true);
          t.frmEl.location_id.append(option).trigger("change");
        }
        setTimeout(function () {
          if (d.internal_place_id) {
            let option = new Option(
              d.internal_place_name,
              d.internal_place_id,
              true,
              true,
            );
            t.frmEl.internal_place_id.append(option).trigger("change");
          }
        }, 300);

        t.frmEl.tat_by_hr.val(d.tat_by_work_hour).trigger("change");

        t.frmEl.work_start.val(d.default_work_start);
        t.frmEl.work_end.val(d.default_work_end);

        if (d.default_work_days_array) {
          t.frmEl.working_days.val(d.default_work_days_array).trigger("change");
        }
        t.mdl.modal("show");
      },
      error: function () {
        let data = { msg: "Something went wrong. Please try again" };
        sweetAlert("center", "error", data);
      },
    });
  };
  t.tableSearch = function (e) {
    if (e) e.preventDefault();
    var v = $.trim($("#lgTbl_wrapper .plain-search").val());
    if (v === "") {
      t.dTbl.search("").draw();
    } else {
      var valid = $(this).validate_str_param
        ? $(this).validate_str_param()
        : true;
      if (valid === false) {
        alert("Please enter valid search text");
        return false;
      }
      t.dTbl.search(v).draw();
    }
  };
  t.deleteDepartmentSla = function (id) {
    sweetAlertConfirmation({
      message: "Are you sure delete this record ?",
      onConfirm: function () {
        $.ajax({
          url: t.config.url.deleteDepartmentSLA + "/" + id,
          type: "POST",
          data: {
            _token: t.getToken(),
          },
          success: function (res) {
            if (res.status === "success") {
              sweetAlert("center", "success", res);
              t.dTbl.ajax.reload();
            } else {
              sweetAlert("center", "error", res);
            }
          },
          error: function () {
            sweetAlert("center", "error", {
              message: "Something went wrong!",
            });
          },
        });
      },
    });
  };
  t.content.on("click", "#saveDepartmentSLA", $.proxy(t.handlesubmit));
  t.tab.on("click", ".btn-add-new-department-sla", $.proxy(t.addModel));
  t.tab.on("click", ".open-edit-modal", $.proxy(t.editModel));
  t.tab.on("click", "#btn-reload-list", $.proxy(t.reload));
  t.tab.on("click", "#btn-searchbox", $.proxy(t.tableSearch));
  t.tab.on("click", ".open-delete", function (e) {
    e.preventDefault();
    let id = $(this).data("id");
    t.deleteDepartmentSla(id);
  });
};

var Notification = function (config) {

  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;
  var t = this;
  t.config = config;
  t.content = $(".main-content");
  t.mdl = t.content.find("#notification-section");
  t.frm = t.mdl.find("#notify_setting");
  t.notification = t.mdl.find("#notification");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update;

  t.frmEl = {};

  t.notification.sla_reminder = t.notification.find("#sla_reminder");
  t.notification.sla_reminder_hr = t.notification.find("#sla_reminder_hr");
  t.notification.sla_notification_type = t.notification.find("#sla_notification_type");

  t.notification.mark_technician_as_cc = t.notification.find("#mark_technician_as_cc_in_ticket_create");

  t.btn = {};

  t.btn.updateNotification = t.notification.find("#updateNotification");

  t.frmValidator = t.notification.validate({
    rules: {
      sla_reminder_hr: {
        required: true,
        min: 1,
        decimal: true,
      },
    },
    // errorPlacement: function (error, element) {
    //   error.appendTo(element.parent("div"));
    // },
    errorPlacement: function (error, element) {
          var errorWrap = getErrorWrap(element);
          if (errorWrap.length) {
              error.appendTo(errorWrap);
          } else {
              error.insertAfter(element.closest(".input-group"));
      }
          updateValidationState(element, true);
      },
      highlight: function (element) {
          updateValidationState($(element), true);
      },
      unhighlight: function (element) {
          updateValidationState($(element), false);
      },
  });

  t.updateNotification = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() == false) {
      return false;
    }
    var frmData = new FormData(t.notification[0]);
    frmData.append("company_id", companyId);
    t.btn.updateNotification.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.updateNotification,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.updateNotification.prop("disabled", false);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.updateNotification.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.content.on("click", ".black-slide-links a", function (e) {
    e.preventDefault();
    var thisNav = $(this);
    t.content.find(".black-slide-links .active").removeClass("active");
    thisNav.parent().addClass("active");
    t.content.find(".black-slide-view.active").slideUp("fast", function () {
      $(this).removeClass("active");
      t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
    });
  });
  t.notification.sla_reminder.select2({ width: "100%" });
  t.notification.sla_notification_type.select2({ width: "100%" });
  t.notification.mark_technician_as_cc.select2({ width: "100%" });
  var select2Opts = { width: "100%" };

  t.notification.sla_reminder.select2({ width: "100%" });
  t.notification.sla_notification_type.select2({ width: "100%" });
  t.btn.updateNotification.on("click", $.proxy(t.updateNotification));


}

var AutoUpdate = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;
  t.content = $(".main-content");
  t.mdl = t.content.find("#auto-updates");
  t.frm = t.mdl.find("#autoUpdate_section");

  t.autoupdate = t.mdl.find("#auto-updates-section");
  t.duplicate_ticket = t.mdl.find("#duplicateTicket");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update;

  t.frmEl = {};

  t.autoupdate.min_before_escalation_to_handler = t.autoupdate.find("#min_before_escalation_to_handler");
  t.autoupdate.min_before_escalation_to_user = t.autoupdate.find("#min_before_escalation_to_user");
  t.autoupdate.min_before_breached_to_technician = t.autoupdate.find("#min_before_breached_to_technician");
  t.autoupdate.min_before_close_ticket_to_handler = t.autoupdate.find("#min_before_close_ticket_to_handler");


  // dublicate ticket form
  t.duplicate_ticket.enable_duplicate_issues_check = t.duplicate_ticket.find("#enable_duplicate_issues_check");
  t.duplicate_ticket.auto_merge_duplicate_issues = t.duplicate_ticket.find("#auto_merge_duplicate_issues");
  t.duplicate_ticket.allow_user_to_continue_ticket_creation_on_duplicate =
    t.duplicate_ticket.find("#allow_user_to_continue_ticket_creation_on_duplicate");

  t.btn = {};
  t.btn.update = t.frm.find("#update");

  t.btn.autoUpdate = t.autoupdate.find("#autoUpdate");
  t.btn.duplicateTicketButton = t.duplicate_ticket.find("#duplicateTicketButton");


  t.autoUpdate = function (e) {

    e.preventDefault();
    var frmData = new FormData(t.autoupdate[0]);
    frmData.append("company_id", companyId);
    t.btn.autoUpdate.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.autoUpdate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.autoUpdate.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.autoUpdate.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.btn.autoUpdate.prop("disabled", false);
    });
    http.always(function () {
      t.btn.autoUpdate.prop("disabled", false);
    });
  };

  t.handlesubmit = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.frm[0]);
    frmData.append("company_id", companyId);
    frmData.append("_token", $('meta[name="csrf-token"]').attr("content"));
    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          sweetAlert("center", "success", data);
          window.location.reload();
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {});
  };

  t.enableCheckBox = function () {
    const enableCheck =
      t.frmDuplicate.enable_duplicate_issues_check.prop("checked");

    // Set read-only state
    if (enableCheck === true) {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        false,
      );
    } else {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("checked", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "checked",
        false,
      );

      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", !enableCheck);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        !enableCheck,
      );
    }
  };
  t.duplicateTicketSUbmit = function (e) {
    e.preventDefault();
    var frmData = new FormData(t.frmDuplicate[0]);
    t.frmDuplicate.duplicateTicketButton.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.autoDuplicate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.frmDuplicate.duplicateTicketButton.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
    });
    http.always(function () {
      t.frmDuplicate.duplicateTicketButton.prop("disabled", false);
    });
  };

  t.btn.update.on("click", $.proxy(t.handlesubmit));
  t.btn.autoUpdate.on("click", $.proxy(t.autoUpdate));
  t.btn.duplicateTicketButton.on("click", $.proxy(t.duplicateTicket));
};

var DuplicateTicket = function (config) {
  let defaultCompany = localStorage.getItem("Default_Company");
  let companyId = config.company_user_detail
    ? config.company_user_detail.dashboard_company_id
    : null;
  var t = this;
  t.config = config;
  t.content = $(".main-content");
  t.mdl = t.content.find("#duplicate_section");
  t.frm = t.mdl.find("#duplicateTicket");

  t.duplicate_ticket = t.mdl.find("#dupl_section");

  t.httpCall = true;
  t.httpPostPath = t.config.url.update;

  t.frmEl = {};


  // dublicate ticket form
  t.duplicate_ticket.enable_duplicate_issues_check = t.duplicate_ticket.find("#enable_duplicate_issues_check");
  t.duplicate_ticket.auto_merge_duplicate_issues = t.duplicate_ticket.find("#auto_merge_duplicate_issues");
  t.duplicate_ticket.allow_user_to_continue_ticket_creation_on_duplicate = t.duplicate_ticket.find("#allow_user_to_continue_ticket_creation_on_duplicate");

  t.btn = {};

  t.btn.duplicateTicketButton = t.duplicate_ticket.find("#duplicateTicketButton");

  t.duplicateTicket = function (e) {
    e.preventDefault();
    console.log(t.duplicate_ticket[0]);
    var frmData = new FormData(t.duplicate_ticket[0]);
    frmData.append("company_id", companyId);
    t.btn.duplicateTicketButton.prop("disabled", true);
    var http = $.ajax({
      url: t.config.url.duplicateTicketUpdate,
      type: "POST",
      processData: false,
      contentType: false,
      data: frmData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.btn.duplicateTicketButton.prop("disabled", true);
          sweetAlert("center", "success", data);
        } else {
          sweetAlert("center", "error", data);
          t.btn.duplicateTicketButton.prop("disabled", false);
        }
      }
    });
    http.fail(function () {
      var data = {
        msg: t.config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
      t.btn.duplicateTicketButton.prop("disabled", false);
    });
    http.always(function () {
      t.btn.duplicateTicketButton.prop("disabled", false);
    });
  };

  t.enableCheckBox = function () {
    const enableCheck =
      t.frmDuplicate.enable_duplicate_issues_check.prop("checked");

    // Set read-only state
    if (enableCheck === true) {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        false,
      );
    } else {
      t.frmDuplicate.auto_merge_duplicate_issues.prop("checked", false);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "checked",
        false,
      );

      t.frmDuplicate.auto_merge_duplicate_issues.prop("disabled", !enableCheck);
      t.frmDuplicate.allow_user_to_continue_ticket_creation_on_duplicate.prop(
        "disabled",
        !enableCheck,
      );
    }
  };
  t.btn.duplicateTicketButton.on("click", $.proxy(t.duplicateTicket));
};
