
var TicketIncident = function (config) {
  var t = this;
  t.config = config;
  t.data = {};
  t.editdata = false;
  t.httpCall = true;
  t.httpPostPath = "";
  t.isInitializing = false;
  t.content = $("#ticket-incident-list-wrapper");
  t.table = t.content.find("#ticket-incident");
  t.searchbox = t.content.find(".searchbox");
  t.searchbtn = t.content.find(".btn-searchbox");
  t.searchbtn = t.content.find(".btn-searchbox");
  t.btn = {};
  t.filterMdl = t.content.find("#incidentFilterModal");

  t.filterMdl.modal({
    backdrop: "static",
    keyboard: false,
    show: false,
  });

  t.filters = {
    wrapper: t.content.find("#incidentFilterModal"),
    data: {
      problem_categories: {},
    },
  };
  ((t.filters.clear = t.content.find("#incidentFilterModal #clear")),
    (t.filters.completion_status = t.content.find(
      "#incidentFilterModal #filter_by_status",
    )),
    (t.filters.incident_status = t.content.find(
      "#incidentFilterModal #filter_by_incident_status",
    )),
    (t.filters.priority = t.content.find(
      "#incidentFilterModal #filter_by_priority",
    )),
    (t.filters.based_on = t.filters.wrapper.find("#filter_by_date")),
    (t.filters.daterange = t.filters.wrapper.find("#daterange")));
  ((t.filters.department = t.filters.wrapper.find("#filter_by_department")),
    (t.filters.problem_category = t.filters.wrapper.find(
      "#filter_by_problem_category",
    )),
    (t.filters.sub_category = t.filters.wrapper.find(
      "#filter_by_sub_category",
    )),
    (t.filters.creator = t.filters.wrapper.find("#filter_by_creator")),
    (t.filters.sla_breached = t.filters.wrapper.find(
      "#filter_by_sla_breached",
    )),
    (t.filters.creator = t.filters.wrapper.find("#filter_by_creator")),
    (t.filters.sla_breached = t.filters.wrapper.find(
      "#filter_by_sla_breached",
    )),
    (t.filters.fun = {
      reload_status: function () {
        $.each(t.config.statuses, function (i, k) {
          t.filters.completion_status.append(
            new Option(k.name, k.id, false, false),
          );
        });
        t.filters.completion_status.trigger("change");
      },
      reload_priority: function () {
        t.filters.priority
          .empty()
          .append(
            new Option(config.translations.No_Filter, null, false, false),
          );
        $.each(t.config.priorities, function (i, k) {
          t.filters.priority.append(new Option(k.name, k.id, false, false));
        });
        t.filters.priority.trigger("change");
      },
      reload_department: function () {
        t.filters.department
          .empty()
          .append(
            new Option(config.translations.No_Filter, null, false, false),
          );
        t.filters.department.select2({
          ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
              return {
                search: p.term,
                page: p.page || 1,
              };
            },
          },
          width: "100%",
          allowClear: true,
          minimumResultsForSearch: Infinity,
          placeholder: config.translations.No_Filter,
        });
        t.filters.department.trigger("change");
      },
      reload_problem_category: function () {
        t.filters.problem_category.empty();
        var department = t.filters.department.val();
        if (department != "" && department != null && department != "null") {
          $.get(
            t.config.url.problem_categories_by_company + "/" + department,
            function (data) {
              if (typeof data == "object" && data.data.length > 0) {
                $.each(data.data, function (i, k) {
                  t.filters.problem_category.append(
                    new Option(k.name, k.id, false, false),
                  );
                  if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                    t.filters.data.problem_categories["sc" + k.id] = k.sub;
                  }
                });
                t.filters.problem_category.trigger("change");
              }
            },
          );
        }
        t.filters.problem_category.trigger("change");
      },
      reload_sub_category: function () {
        t.filters.sub_category.empty();
        var prblm = t.filters.problem_category.val();
        if (prblm != "" && prblm != null && prblm != "null") {
          try {
            $.each(
              t.filters.data.problem_categories["sc" + prblm],
              function (i, k) {
                t.filters.sub_category.append(
                  new Option(k.name, k.id, false, false),
                );
              },
            );
          } catch (e) {}
        }
        t.filters.sub_category.trigger("change");
      },
    }));

  t.mdl = t.content.find("#ticket-incident-modal");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.frm = t.mdl.find("#incident-mdl-frm");
  t.mdl.frmEl = {};
  t.mdl.frmEl.id = t.mdl.frm.find("#id");
  t.mdl.frmEl.forAction = t.mdl.frm.find("#for_action");
  t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
  t.mdl.frmEl.problemCategoryId = t.mdl.frm.find("#problem_category_id");
  t.mdl.frmEl.subCategoryId = t.mdl.frm.find("#sub_category_id");
  t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
  t.mdl.frmEl.priorityId = t.mdl.frm.find("#priority_id");
  t.mdl.frmEl.man_hour_loss = t.mdl.frm.find("#man_hour_loss");
  t.mdl.frmEl.financial_loss_amount = t.mdl.frm.find("#financial_loss_amount");
  t.mdl.frmEl.creatorId = t.mdl.frm.find("#creator_id");
  t.mdl.frmEl.incident_start_date = t.mdl.frm.find("#incident_start_date");
  t.mdl.frmEl.incident_end_date = t.mdl.frm.find("#incident_end_date");
  t.mdl.frmEl.subject = t.mdl.frm.find("#subject");
  t.mdl.frmEl.service_impacted = t.mdl.frm.find("#service_impacted");
  t.mdl.frmEl.sla_breaches = t.mdl.frm.find("#sla_breaches");
  t.mdl.frmEl.status = t.mdl.frm.find("#status");
  t.mdl.frmEl.content = t.mdl.frm.find("#content");
  t.mdl.frmEl.rca = t.mdl.frm.find("#rca");
  t.mdl.frmEl.why_incident_happened = t.mdl.frm.find("#why_incident_happened");
  t.mdl.frmEl.preventive_measure_taken = t.mdl.frm.find(
    "#preventive_measure_taken",
  );
  t.mdl.frmEl.locationId = t.mdl.frm.find("#location_id");
  t.mdl.frmEl.internalPlace = t.mdl.frm.find("#internal_place");
  t.mdl.frmEl.ticketId = t.mdl.frm.find("#ticket_id");
  t.mdl.frmEl.tmp_id = t.mdl.frm.find("#tmp_id");
  t.mdl.frmEl.company_id = t.mdl.frm.find("#company_id");
  t.mdl.btn = {};
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");
  t.btn.export = t.content.find(".btn-export-ticket-incident");

  t.resetFrm = function () {
    t.mdl.frmEl.id.val("");
    t.mdl.frmEl.company_id
      .val("")
      .empty()
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.departmentId
      .val("")
      .empty()
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.problemCategoryId
      .val("")
      .empty()
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.subCategoryId
      .val("")
      .empty()
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.priorityId.val("").trigger("change").attr("disabled", false);
    t.mdl.frmEl.ticketId.val("").trigger("change");
    t.mdl.frmEl.man_hour_loss.val("").attr("disabled", false);
    t.mdl.frmEl.financial_loss_amount
      .val("")
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.creatorId
      .val("")
      .empty()
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.subject.val("").attr("disabled", false);
    t.mdl.frmEl.service_impacted
      .val("")
      .trigger("change")
      .attr("disabled", false);
    t.mdl.frmEl.sla_breaches.val("").attr("disabled", false);
    t.mdl.frmEl.status.val("").attr("disabled", false);
    $('input[name="sla_breaches"]').prop("checked", false);
    $("#incident_start_date").attr("disabled", false);
    $("#incident_end_date").attr("disabled", false);
    $("#inlineRadio1").attr("disabled", false);
    $("#inlineRadio2").attr("disabled", false);
    t.mdl.frmEl.sla_breaches.attr("disabled", false);
    t.mdl.frmEl.content.val("").summernote("code", "");
    t.mdl.frmEl.rca.val("").summernote("code", "");
    t.mdl.frmEl.preventive_measure_taken.val("").summernote("code", "");
    t.mdl.frmEl.why_incident_happened.val("").summernote("code", "");
    $("#manual_file_trigger").attr("disabled", false);

    $(".summernote").summernote("enable");
    t.mdl.frm.find("textarea[name='rca']").summernote("enable");
    t.mdl.frm
      .find("textarea[name='preventive_measure_taken']")
      .summernote("enable");
    t.mdl.frm
      .find("textarea[name='why_incident_happened']")
      .summernote("enable");
    t.updateSubCategoryVisibility();
    t.frmValidator.resetForm();
    t.mdl.btnSubmit.text(config.translations.Update).attr("disabled", false);
  };

  t.fillDepartment = function () {
    if (
      typeof t.config.departments != "undefined" &&
      Array.isArray(t.config.departments) == true &&
      t.config.departments.length > 0
    ) {
      t.mdl.frmEl.departmentId.empty();
      $.each(t.config.departments, function (i, v) {
        t.mdl.frmEl.departmentId.append(new Option(v.name, v.id));
      });
      t.mdl.frmEl.departmentId.closest(".row").show();
      // FIXED: Initialization time par change trigger nahi karein
      if (!t.isInitializing) {
        t.mdl.frmEl.departmentId.trigger("change");
      }
    }
  };

  t.frmCommentTokenize = function () {
    var v =
      Math.random().toString(36).substring(2, 6) +
      Math.random().toString(36).substring(2, 6) +
      Date.now();
    t.mdl.frmEl.tmp_id.val(v);
  };

  t.addIncident = function (e) {
    e.preventDefault();
    t.isInitializing = true;
    t.resetFrm();
    t.mdl.frmEl.status.val(1).trigger("change");
    $("#inlineRadio1").attr("checked", false);
    $("#inlineRadio2").attr("checked", false);
    [
      "shows_error",
      "rca_error",
      "why_incident_happened_error",
      "preventive_measure_taken_error",
    ].forEach(function (id) {
      $("#" + id).text("");
    });
    $('#incident-tabs a[href="#basic-details"]').tab("show");
    t.httpPostPath = t.config.url.create_incident;
    t.mdl.title.html(config.translations.New_incident);
    t.mdl.btnSubmit.text(config.translations.create).show();
    t.mdl.title.html(t.config.translations.New_incident);
    t.mdl.frmEl.forAction.val("1");
    let userText = t.config.user.displayName
      ? t.config.user.displayName
      : t.config.user.first_name +
        " " +
        t.config.user.last_name +
        " (" +
        t.config.user.username +
        ")";
    t.mdl.frmEl.creatorId
      .empty()
      .append(new Option(userText, t.config.user.id, true, true))
      .trigger("change");
    t.refillPriority();
    t.fillDepartment();

    if (t.config.company && t.config.company.company_id) {
      let option = new Option(
        t.config.company.company_name,
        t.config.company.company_id,
        true,
        true,
      );

      t.mdl.frmEl.company_id.empty().append(option).trigger("change");
    }
    t.frmCommentTokenize();

    t.mdl.modal("show");

    setTimeout(function() {
      t.isInitializing = false;
      t.frmValidator.resetForm();
      // t.mdl.frm.find("label.error").remove();
      t.mdl.frm.find(".error").removeClass("error");
    }, 500);
  };

  t.loadForm = function (data, forAction) {
    t.isInitializing = true;
    
    t.resetFrm();
    t.refillPriority();
    
    if (data[0].company_id) {
      var option = new Option(
        data[0].company_name,
        data[0].company_id,
        true,
        true,
      );
      t.mdl.frmEl.company_id.append(option).trigger("change", [true]);
    }
    
    if (data[0].department_id != "" || data[0].department_id != null) {
      t.mdl.frmEl.departmentId
        .val(data[0].department_id)
        .empty()
        .append(
          new Option(data[0].dept_name, data[0].department_id, true, true),
        )
        .trigger("change");
    }
    
    if (data[0].problem_category_id != "" || data[0].problem_category_id != null) {
      $.get(t.config.url.problem_categories_by_company + "/" + data[0].department_id)
        .done(function (responseData) {
          if (typeof responseData == "object" && responseData.data.length) {
            t.data.problem_categories = responseData.data;
            
            t.mdl.frmEl.problemCategoryId
              .empty()
              .append(new Option(config.translations.Select_Problem_Category, ""));
            
            $.each(responseData.data, function (i, v) {
              t.mdl.frmEl.problemCategoryId.append(new Option(v.name, v.id));
            });
            
            t.mdl.frmEl.problemCategoryId
              .val(data[0].problem_category_id)
              .trigger("change");
            
            if (data[0].sub_category_id != "" && data[0].sub_category_id != null) {
              setTimeout(function() {
                t.mdl.frmEl.subCategoryId
                  .empty()
                  .append(new Option("Select Sub Category", ""));
                
                $.each(t.data.problem_categories, function (i, v) {
                  if (v.id == data[0].problem_category_id) {
                    if (Array.isArray(v.sub) && v.sub.length > 0) {
                      t.data.sub_categories = v.sub;
                      $.each(v.sub, function (j, k) {
                        t.mdl.frmEl.subCategoryId.append(new Option(k.name, k.id));
                      });
                    }
                  }
                });
                
                t.mdl.frmEl.subCategoryId
                  .val(data[0].sub_category_id)
                  .trigger("change");
                
                t.updateSubCategoryVisibility();
              }, 100);
            }
          }
        });
    }
    
    if (data[0].priority_id != "" || data[0].priority_id != null) {
      t.mdl.frmEl.priorityId.val(data[0].priority_id).trigger("change");
    }
    
    if (data[0].creator_id != "" || data[0].creator_id != null) {
      t.mdl.frmEl.creatorId
        .empty()
        .append(
          new Option(
            data[0].first_name + " " + data[0].last_name + " (" + data[0].username + ")",
            t.config.user.id,
            true,
            true,
          ),
        )
        .trigger("change");
    }
    
    if (data[0].location_id != "" && data[0].location_id != null) {
      t.mdl.frmEl.locationId
        .empty()
        .append(
          new Option(data[0].location_name, data[0].location_id, true, true),
        )
        .trigger("change");
    }
    
    if (data[0].place_id !== "" && data[0].place_id !== null && data[0].place_id !== "null" && typeof data[0].place_id !== "undefined") {
      t.mdl.frmEl.internalPlace
        .empty()
        .append(new Option(data[0].place_name, data[0].place_id, true, true))
        .trigger("change");
    }
    
    if (data[0].status != "" || data[0].status != null) {
      t.mdl.frmEl.status.val(data[0].status).trigger("change");
    }
    
    if (data[0].incident_start_date != "" || data[0].incident_start_date != null) {
      t.mdl.frmEl.incident_start_date.val(data[0].incident_start_date);
    }
    
    if (data[0].incident_end_date != "" || data[0].incident_end_date != null) {
      t.mdl.frmEl.incident_end_date.val(data[0].incident_end_date);
    }
    
    t.mdl.frm.find('input[name="sla_breaches"]').prop("checked", false);
    t.mdl.frm.find(`input[name="sla_breaches"][value="${data[0].sla_breaches}"]`).prop("checked", true);

    t.mdl.frm.find("input[name='subject']").val(data[0].subject);
    
    var impactedServices = Array.isArray(data[0].service_impacted) ? data[0].service_impacted : data[0].service_impacted.split(",");

    $.each(impactedServices, function (index, value) {
      let exists = t.mdl.frmEl.service_impacted.find("option").filter(function () {
        return $(this).val() == value;
      }).length > 0;
      if (!exists) {
        t.mdl.frmEl.service_impacted.append(new Option(value, value, true, true));
      }
    });
    t.mdl.frmEl.service_impacted.val(impactedServices).trigger("change");
    
    t.mdl.frm.find("input[name='man_hour_loss']").val(data[0].man_hour_loss);
    t.mdl.frm.find("input[name='financial_loss_amount']").val(data[0].financial_loss_amount);
    
    t.mdl.frm.find("textarea[name='content']").summernote("code", data[0].content);
    t.mdl.frm.find("textarea[name='rca']").summernote("code", data[0].rca);
    t.mdl.frm.find("textarea[name='why_incident_happened']").summernote("code", data[0].why_incident_happen);
    t.mdl.frm.find("textarea[name='preventive_measure_taken']").summernote("code", data[0].preventive_measure_taken);
    
    t.mdl.frmEl.id.val(data[0].id);
    t.data.id = data[0].id;

    t.mdl.modal("show");
    
    setTimeout(function() {
      t.isInitializing = false;
      t.frmValidator.resetForm();
      // t.mdl.frm.find("label.error").remove();
      t.mdl.frm.find(".error").removeClass("error");
    }, 500);
  };

  t.editIncident = function (e) {
    t.editdata = true;
    e.preventDefault();
    [
      "shows_error",
      "rca_error",
      "why_incident_happened_error",
      "preventive_measure_taken_error",
    ].forEach(function (id) {
      $("#" + id).text("");
    });
    $('#incident-tabs a[href="#basic-details"]').tab("show");
    var shedId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.create_incident;
    var http = $.get(t.config.url.edit + "/" + shedId);
    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status == "success") {
          t.mdl.title.html(config.translations.edit_incident);
          t.mdl.btnSubmit.text("Update").show();
          t.mdl.frmEl.forAction.val("edit");
          t.mdl.modal("show");
          t.frmCommentTokenize();
          t.loadForm(data.edit_incident, "edit");
          if (data.tickets && data.tickets.length > 0) {
            data.tickets.forEach(function (ticket) {
              let option = new Option(
                ticket.ticket_text,
                ticket.ticket_id,
                false,
                true,
              );
              t.mdl.frmEl.ticketId.append(option).trigger("change");
            });
          }
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
    http.always(function () {
      t.httpCall = true;
      $("#img").hide();
      $("#img").hide();
    });
  };

  t.deleteIncident = function (e) {
    e.preventDefault();
    var Id = $(this).attr("data-id");
    t.httpPostPath = t.config.url.delete + "/" + Id;
    sweetAlertConfirmation({
      message: config.translations.delete_record,
      onConfirm: function () {
        var http = $.get(t.httpPostPath);
        http.done(function (data) {
          if (typeof data == "object") {
            if (data.status == "success") {
              sweetAlert("center", "success", data);
              t.dTbl.ajax.reload();
            } else {
              sweetAlert("center", "error", data);
              t.refreshInfoTab();
            }
          }
        });
        http.fail(function () {
          alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
          t.httpCall = true;
        });
      },
    });
  };

  t.cleanEditorValue = function (field) {
    let code = t.mdl.frmEl[field].summernote("code");
    return $("<div>").html(code).text().trim();
  };

  t.viewIncident = function (e) {
    e.preventDefault();
    t.editdata = true;
    var shedId = $(this).attr("data-id");
    var url = t.config.url.view + "/" + shedId;
    window.open(url, "_blank");
  };

  t.mdl.on("hidden.bs.modal", function () {
    t.frmValidator.resetForm();
    t.mdl.frm.find(".error").removeClass("error");
    t.mdl.find("#shows_error").html("");
  });

  t.handleSubmit = function (e) {
    e.preventDefault();

    if (t.httpCall !== true) return false;
    t.httpCall = false;

    var status = t.mdl.frmEl.status.val();
    var rca = t.cleanEditorValue("rca");
    var whyIncident = t.cleanEditorValue("why_incident_happened");
    var preventive = t.cleanEditorValue("preventive_measure_taken");

    if (status == 2 && (!rca || !whyIncident || !preventive)) {
      sweetAlert("center", "warning", {
        msg: "The incident cannot be marked as closed until it's completed.",
      });
      t.httpCall = true;
      return false;
    }

    var content = t.mdl.frmEl.content.summernote("code");
    var plainContent = $("<div>").html(content).text().trim();
    if (!plainContent) {
        $("#shows_error").html("This field is required.").css({
            "margin-left": "23px",
            color: "#f12f35",
            "font-size": "11px"
        });
    } else {
        $("#shows_error").html("");
    }

    // Summernote value textarea me sync karo
    t.mdl.frmEl.content.val(content);
    t.mdl.frmEl.content.trigger("change");

    // Ab frmValidation chalega
    var formValid = t.frmValidator.form();

    if (!formValid || !plainContent) {
        t.httpCall = true;
        return false;
    }

    var formData = new FormData(t.mdl.frm[0]);

    formData.set("content", content);
    formData.set("rca", t.mdl.frmEl.rca.summernote("code"));
    formData.set(
      "why_incident_happened",
      t.mdl.frmEl.why_incident_happened.summernote("code"),
    );
    formData.set(
      "preventive_measure_taken",
      t.mdl.frmEl.preventive_measure_taken.summernote("code"),
    );

    t.mdl.btnSubmit.prop("disabled", true);

    $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    })
      .done(function (data) {
        if (data && data.status === "success") {
          t.dTbl.ajax.reload(null, false);
          t.mdl.frmEl.content.summernote("reset");
          t.mdl.frmEl.rca.summernote("reset");
          t.mdl.frmEl.why_incident_happened.summernote("reset");
          t.mdl.frmEl.preventive_measure_taken.summernote("reset");
          sweetAlert("center", "success", data);
          t.mdl.modal("hide");
        } else {
          sweetAlert("center", "error", data);
        }
      })
      .fail(function () {
        sweetAlert("center", "error", {
          msg: t.config.translations.something_went_wrong,
        });
      })
      .always(function () {
        t.httpCall = true;
        t.mdl.btnSubmit.prop("disabled", false);
        $("#img").hide();
      });
  };

  t.renderTruncated = function (data, type, title) {
    if (!data) return "";
    if (type !== "display") return data;
    data = String(data);

    var plain = t.getPlainText(data);

    if (plain.length > 50) {
      return (
        plain.substring(0, 50) +
        "..." +
        '<a href="javascript:void(0)" class="read-more text-primary"' +
        ' data-title="' +
        t.escapeHtml(title) +
        '"' +
        ' data-full-text="' +
        t.escapeHtml(data) +
        '"> Read More</a>'
      );
    }
    return plain;
  };

  t.openContentModal = function () {
    var fullText = $(this).data("full-text");
    var title = $(this).data("title");

    $("#readMoreModalTitle").text(title);
    $("#readMoreModalBody").html(fullText);

    var modal = new bootstrap.Modal(document.getElementById("readMoreModal"));
    modal.show();
  };

  t.buildActions = function (d) {
    var t = this;

    let actions = [];

    if (jQuery.inArray("TicketIncidentView", t.config.permissions) !== -1) {
      actions.push(`
                <button class="amg-action-btn primary btn-view-incident" data-id="${d.id}" 
                    data-bs-toggle="tooltip"
                   data-bs-original-title="${t.config.translations.View}">
                    <i class="bi bi-eye"></i>
                </button>
            `);
    }

    if (
      d.status == "Opened" &&
      jQuery.inArray("TicketIncidentEdit", t.config.permissions) !== -1
    ) {
      actions.push(`
                <button class="amg-action-btn btn-edit-incident"
                    data-id="${d.id}" title="${t.config.translations.Edit}" data-bs-toggle="tooltip"
                   data-bs-original-title="Edit Incident">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
                    </svg>
                </button>
            `);
    }

    if (jQuery.inArray("TicketIncidentDelete", t.config.permissions) !== -1) {
      actions.push(`
            <button class="amg-action-btn danger btn-delete-incident" data-id="${d.id}" data-bs-toggle="tooltip"
                   data-bs-original-title="${t.config.translations.Delete}">
                    <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                    </svg>
            </button>
            `);
    }

    if (!actions.length) {
      return `<span class="text-muted">-</span>`;
    }

    return `
            <div class="amg-datatable-actions d-flex justify-content-center gap-2">
            ${actions.join("")}
            </div>
        `;
  };

  $.validator.addMethod(
    "summernoteRequired",
    function (value, element) {
      let code = $(element).summernote("code");
      let text = $("<div>").html(code).text().trim();

      return text.length > 0;
    },
    "This field is required.",
  );

  t.frmValidator = t.mdl.frm.validate({
    onsubmit: false,
    onfocusout: function(element) {
      if (t.isInitializing) return;
      this.element(element);
    },
    onkeyup: false,
    onclick: false,
    ignore: ".select2-input, :hidden:not([name^='sla_breaches'])",
    rules: {
      department_id: {
        required: true,
      },
      creator_id: {
        required: true,
      },
      problem_category_id: {
        required: true,
      },
      "service_impacted[]": {
        required: true,
        clean_text_only: true,
      },
      subject: {
        required: true,
        // acceptable_spcl_chr: true
      },
      priority_id: {
        required: true,
      },
      incident_start_date: {
        required: false,
      },
      incident_end_date: {
        required: false,
      },
      man_hour_loss: {
        required: true,
        digits: true,
      },
      status: {
        required: true,
      },
      financial_loss_amount: {
        required: true,
        number: true,
        min: 0
      },
      company_id: {
        required: true,
      },
      location: {
        required: true,
      },
    },
    errorPlacement: function (error, element) {
      let group = element.closest(".input-group");

      if (group.length) {
        error.insertAfter(group);
      } else {
        error.insertAfter(element);
      }
    },
  });

  t.mdl.modal({
    backdrop: "static",
    keyboard: false,
    show: false,
  });

  t.creatorinfo = {};
  t.creatorinfo.wrapper = t.mdl.find("#creator-info");
  t.creatorinfo.fullname = t.creatorinfo.wrapper.find("#fullname");
  t.creatorinfo.username = t.creatorinfo.wrapper.find("#username");
  t.creatorinfo.emp_code = t.creatorinfo.wrapper.find("#emp_code");
  t.creatorinfo.job_title = t.creatorinfo.wrapper.find("#job_title");
  t.creatorinfo.location_name = t.creatorinfo.wrapper.find("#location_name");
  t.creatorinfo.mobile = t.creatorinfo.wrapper.find("#mobile");
  t.creatorinfo.email = t.creatorinfo.wrapper.find("#email");
  t.creatorinfo.company = t.creatorinfo.wrapper.find("#company");
  t.creatorinfo.profile = t.creatorinfo.wrapper.find("#profile");
  t.creatorinfo.reload = function (e) {
    if (typeof e !== "undefined") e.preventDefault();
    var u = t.mdl.frmEl.creatorId.val();
    if (!u) {
      return;
    }
    $.get(t.config.url.user_basic_info + "/" + u + "/true", function (d) {
      if (typeof d !== "undefined" && d.status === "success") {
        t.creatorinfo.fullname.html(d.data.fullname);
        t.creatorinfo.username.html(d.data.username);
        t.creatorinfo.emp_code.html(d.data.employee_code);
        t.creatorinfo.job_title.html(d.data.jobtitle);
        t.creatorinfo.location_name.html(d.data.location);
        t.creatorinfo.mobile.html(d.data.phone);
        t.creatorinfo.email.html(d.data.email);
        t.creatorinfo.company.html(d.data.company);
        t.creatorinfo.profile.attr("src", d.data.profile);
      }
    });
  };

  t.resetModalForm = function () {
    if (t.mdl.frm.length && t.mdl.frm[0]) {
      t.mdl.frm[0].reset();
    }

    t.mdl.frmEl.company_id.empty().val(null).trigger("change");
  };

  t.escapeHtml = function (text) {
    return text.replace(/[&<>"'`=\/]/g, function (s) {
      return entityMap[s];
    });
  };

  var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#39;",
    "/": "&#x2F;",
    "`": "&#x60;",
    "=": "&#x3D;",
  };

  t.getPlainText = (html) => {
    var div = document.createElement("div");
    div.innerHTML = html;
    return div.textContent || div.innerText || "";
  };

  function truncateHtml(html, maxLength) {
    var div = document.createElement("div");
    div.innerHTML = html;
    var text = div.textContent || div.innerText || "";
    if (text.length <= maxLength) {
      return html;
    }
    return text.substring(0, maxLength);
  }

  t.dTbl = t.table.DataTable({
    autoWidth: false,
    processing: true,
    serverSide: true,
    order: [[23, "desc"]],
    preDrawCallback: function () {
        t.table.find('[data-bs-toggle="tooltip"]').each(function () {
            var existing = bootstrap.Tooltip.getInstance(this);
            if (existing) {
                existing.hide();    
                existing.dispose();
            }
        });
    },
    drawCallback: function () {
        t.table.find('[data-bs-toggle="tooltip"]').each(function () {
            var existing = bootstrap.Tooltip.getInstance(this);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(this);
        });
    },
    dom: "rtip",
    scrollX: true,
    fixedColumns: {
      leftColumns: 1,
      rightColumns: 1,
    },
    ajax: {
      url: t.config.url.incident_list,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.filters = t.config.other_filters;
        d.main_filter = t.config.main_filter;
        d.search.value = t.content.find(".user-list-search").val();
      },
    },

    columns: [
      { data: "a.id" },
      { data: "a.subject" },
      { data: "a.company_name" },
      {
        data: "a.ticket_id",
        orderable: false,
        render: function (data, type, row) {
          let ticketCount = 0;
          if (row.a.ticket_id) {
            ticketCount = row.a.ticket_id.split(",").length;
          }
          return `<a href="#" class="open-ticket-modal" data-id="${row.a.ticket_id}">${ticketCount}</a>`;
        },
      },
      { data: "a.dept_name" },
      { data: "a.username" },
      { data: "a.cat_name" },
      { data: "a.sub_cat_name" },
      { data: "a.priority_name" },
      { data: "a.financial_loss_amount" },
      { data: "a.man_hour_loss" },
      {
        data: "a.service_impacted",
        orderable: true,
        render: function (data, type, row) {
          if (Array.isArray(data) && data.length > 0) {
            // already array
          } else if (typeof data === "string" && data.length > 0) {
            data = data.split(",");
          } else {
            data = [];
          }

          var renderBadge = function (x) {
            x = x.trim();
            return `<span class="badge bg-dark text-white px-2 py-1" style="margin:2px" title="${x}">${x}</span>`;
          };

          if (data.length > 3) {
            var encoded = JSON.stringify(data)
              .replace(/'/g, "&#39;")
              .replace(/"/g, "&quot;");
            return `
                            ${data.slice(0, 3).map(renderBadge).join("")}
                            <a href="javascript:void(0)" class="read-more-service text-primary ms-1"
                                data-title="Service Impacted"
                                data-values="${encoded}">
                                +${data.length - 3} more
                            </a>`;
          } else {
            return data.map(renderBadge).join("");
          }
        },
      },
      { data: "a.sla_breaches" },
      { data: "a.place_name" },
      { data: "a.location_name" },
      {
        data: "a.content",
        orderable: true,
        render: function (data, type, row) {
          if (!data) return "";
          data = String(data);

          var plainText = data.replace(/<[^>]*>/g, "").trim();
          var textarea = document.createElement("textarea");
          textarea.innerHTML = plainText;
          plainText = textarea.value;

          if (plainText.length > 50) {
            var truncated = plainText.substring(0, 50);
            return (
              truncated +
              '<a href="javascript:void(0)" class="read-more text-primary" data-title="Content" data-full-text="' +
              t.escapeHtml(plainText) +
              '">...Read More</a>'
            );
          }
          return plainText;
        },
      },
      {
        data: "a.rca",
        orderable: true,
        render: function (data, type, row) {
          return t.renderTruncated(data, type, "RCA");
        },
      },
      {
        data: "a.why_incident_happen",
        orderable: true,
        render: function (data, type, row) {
          return t.renderTruncated(data, type, "Why Incident Happened");
        },
      },
      {
        data: "a.preventive_measure_taken",
        orderable: true,
        render: function (data, type, row) {
          return t.renderTruncated(data, type, "Preventive Measure Taken");
        },
      },
      {
        data: "a.status",
        orderable: true,
        render: function (data, type, row) {
          if (row.a.completion_status === "Completed") {
            return '<span class="amg-status-badge amg-status-badge-completed">Completed</span>';
          } else {
            return '<span class="amg-status-badge amg-status-badge-incompleted">Incomplete</span>';
          }
        },
      },
      { data: "a.status" },
      { data: "a.incident_start_date" },
      { data: "a.incident_end_date" },
      { data: "a.updated_at" },
      {
        data: "a",
        width: "148px",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return t.buildActions(row.a || {});
        },
      },
    ],
  });

  t.tr = function (key, fallback) {
    if (t.config.translations && t.config.translations[key]) {
      return t.config.translations[key];
    }

    return fallback;
  };

  t.fillSla = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    var tmp = t.mdl.frmEl.problemCategoryId.val();
    var sub_category_id = t.mdl.frmEl.subCategoryId.val();
    var found = false;

    if (
      Array.isArray(t.data.sub_categories) == true &&
      t.data.sub_categories.length
    ) {
      $.each(t.data.sub_categories, function (i, k) {
        if (k.id == sub_category_id) {
          var tmp_tat = parseInt(k.tat);
          if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
            t.offListen = true;
            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
            t.offListen = false;
          } else {
            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
          }
          found = true;
          return false;
        }
      });
    } else if (
      typeof t.data.problem_categories != "undefined" &&
      t.data.problem_categories.length
    ) {
      $.each(t.data.problem_categories, function (i, k) {
        if (k.id == tmp) {
          var tmp_tat = parseInt(k.tat);
          if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
            t.offListen = true;
            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
            t.offListen = false;
          } else {
            t.mdl.frmEl.priorityId.val(k.priority_id).trigger("change");
          }
          found = true;
          return false;
        }
      });
    }

    if (!found) {
      t.offListen = true;
      t.mdl.frmEl.priorityId.val("").trigger("change");
      t.offListen = false;
    }
  };

  t.refillProblemCategory = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    t.data.problem_categories = [];
    t.mdl.frmEl.problemCategoryId
      .empty()
      .append(new Option(config.translations.Select_Problem_Category, ""));
    var type_val = parseInt($.trim(t.mdl.frmEl.departmentId.val()));
    if (type_val > 0 && !isNaN(type_val)) {
      $.get(t.config.url.problem_categories_by_company + "/" + type_val)
        .done(function (data) {
          if (typeof data == "object" && data.data.length) {
            t.data.problem_categories = data.data;
            $.each(data.data, function (i, v) {
              t.mdl.frmEl.problemCategoryId.append(new Option(v.name, v.id));
            });
          }
        })
        .always(function () {
          if (!t.isInitializing) {
            t.mdl.frmEl.problemCategoryId.trigger("change");
          }
        });
    } else {
      if (!t.isInitializing) {
        t.mdl.frmEl.problemCategoryId.trigger("change");
      }
    }
    t.mdl.frmEl.ticketId.val("").trigger("change");
  };

  t.refillSubCategory = function (e) {
    t.mdl.frmEl.subCategoryId.val(null).empty().trigger("change");
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    t.data.sub_categories = [];
    t.mdl.frmEl.subCategoryId.empty().append(new Option("Select Sub Category", ""));
    var type_val = parseInt($.trim(t.mdl.frmEl.problemCategoryId.val()));
    if (type_val > 0 && !isNaN(type_val)) {
      try {
        $.each(t.data.problem_categories, function (i, v) {
          if (v.id == type_val) {
            if (Array.isArray(v.sub) && v.sub.length > 0) {
              t.data.sub_categories = v.sub;
              $.each(v.sub, function (j, k) {
                t.mdl.frmEl.subCategoryId.append(new Option(k.name, k.id));
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
    t.mdl.frmEl.subCategoryId.trigger("change");
  };

  t.resetTicketId = function () {
    t.mdl.frmEl.ticketId.val("").trigger("change");
  };

  t.updateSubCategoryVisibility = function () {
    if (t.data.sub_categories.length > 0) {
      t.mdl.frmEl.subCategoryId.rules("add", {
        required: true,
        str_name: true,
      });
      t.mdl.frmEl.subCategoryIdCvr.show();
    } else {
      t.mdl.frmEl.subCategoryId.rules("remove");
      t.mdl.frmEl.subCategoryIdCvr.hide();
    }
  };

  t.refillPriority = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    t.mdl.frmEl.priorityId
      .empty()
      .append(new Option(config.translations.select_priority, ""));
    $.each(t.config.priorities, function (i, k) {
      t.mdl.frmEl.priorityId.append(new Option(k.name, k.id));
    });
    if (!t.isInitializing) {
      t.mdl.frmEl.priorityId.trigger("change");
    }
  };

  t.refillTat = function (e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    var pro_val = t.mdl.frmEl.priorityId.val();
    var val = 0;
    $.each(t.config.priorities, function (i, k) {
      if (k.id == pro_val) {
        val = k.service_time;
        return false;
      }
    });
  };

  t.mdl.frmEl.incident_start_date.flatpickr({
      enableTime: true,
      time_24hr: true,
      dateFormat: "d/m/Y H:i",
      minuteIncrement: 15,
      allowInput: false,
  });
  t.mdl.frmEl.incident_end_date.flatpickr({
      enableTime: true,
      time_24hr: true,
      dateFormat: "d/m/Y H:i",
      minuteIncrement: 15,
      allowInput: false,
  });

  var select2Opts = { width: "100%" };

  t.filters.completion_status.select2(
    $.extend({}, select2Opts, {
      placeholder:
        config.translations.filter_by_ticket_incident_rca_status ||
        "Filter by RCA Status",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.incident_status.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.filter_by_status || "Filter by Status",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.priority.select2(
    $.extend({}, select2Opts, {
      placeholder:
        config.translations.Filter_By_Priority || "Filter by Priority",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.based_on.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.Filter_Based_on || "Filter Based On",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.department.select2(
    $.extend({}, select2Opts, {
      placeholder:
        config.translations.filter_by_department || "Filter by Department",
      allowClear: true,
      dropdownParent: t.filterMdl,
      ajax: {
        url: t.config.url.departments_with_company,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            // company_id: t.config.company.company_id,
          };
        },
      },
    }),
  );

  t.filters.problem_category.select2(
    $.extend({}, select2Opts, {
      placeholder:
        config.translations.Filter_By_Problem_Category ||
        "Filter by Problem Category",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.sub_category.select2(
    $.extend({}, select2Opts, {
      placeholder:
        config.translations.Filter_By_Sub_Category || "Filter by Sub Category",
      allowClear: true,
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.creator.select2(
    $.extend({}, select2Opts, {
      placeholder: config.translations.filter_by_creator || "Filter by Creator",
      allowClear: true,
      dropdownParent: t.filterMdl,
      ajax: {
        url: t.config.url.getUserByAjax,
        dataType: "json",
        data: function (params) {
          return {
            q: params.term,
            page: params.page || 1,
            // company_id: t.config.company.company_id
          };
        },
      },
      templateResult: function (data) {
        if (!data) return $("<div>No data</div>");
        return t.userDropdownFormat(data, "0px");
      },
    }),
  );

  t.filters.sla_breached.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.filterMdl,
    }),
  );

  t.filters.fun.reload_status();
  t.filters.fun.reload_priority();

  t.filters.department.on("change", function () {
    t.filters.problem_category.empty().trigger("change");
    t.filters.sub_category.empty().trigger("change");
    t.filters.data.problem_categories = {};

    var department = t.filters.department.val();
    if (!department || department === "null") return;

    $.get(
      t.config.url.problem_categories_by_company + "/" + department,
      function (data) {
        if (typeof data === "object" && data.data && data.data.length > 0) {
          $.each(data.data, function (i, k) {
            t.filters.problem_category.append(
              new Option(k.name, k.id, false, false),
            );
            if (typeof k.sub !== "undefined" && Array.isArray(k.sub)) {
              t.filters.data.problem_categories["sc" + k.id] = k.sub;
            }
          });
          t.filters.problem_category.trigger("change");
        }
      },
    );
  });

  t.filters.problem_category.on("change", function () {
    t.filters.sub_category.empty().trigger("change");

    var prblm = t.filters.problem_category.val();
    if (!prblm || prblm === "null") return;

    try {
      var subs = t.filters.data.problem_categories["sc" + prblm];
      if (Array.isArray(subs)) {
        $.each(subs, function (i, k) {
          t.filters.sub_category.append(new Option(k.name, k.id, false, false));
        });
        t.filters.sub_category.trigger("change");
      }
    } catch (e) {
      console.error("Sub-category load error:", e);
    }
  });

  t.cache_filter_values = function () {
    t.config.other_filters = {};

    var completionStatus = t.filters.completion_status.val();
    if (
      completionStatus &&
      completionStatus.length &&
      completionStatus[0] !== ""
    ) {
      t.config.other_filters.completion_status = completionStatus;
    }

    var incidentStatus = t.filters.incident_status.val();
    if (incidentStatus && incidentStatus.length && incidentStatus[0] !== "") {
      t.config.other_filters.incident_status = incidentStatus;
    }

    var priority = t.filters.priority.val();
    if (
      priority &&
      priority.length &&
      priority[0] !== "null" &&
      priority[0] !== null
    ) {
      t.config.other_filters.priority = priority;
    }

    var basedOn = t.filters.based_on.val();
    if (basedOn && basedOn !== "null") {
      t.config.other_filters.based_on = basedOn;
      var daterange = t.filters.daterange.val();
      if (daterange) {
        t.config.other_filters.date_range = daterange;
      }
    }

    var department = t.filters.department.val();
    if (
      department &&
      department.length &&
      department[0] !== "null" &&
      department[0] !== null
    ) {
      t.config.other_filters.department = department;
    }

    var problemCategory = t.filters.problem_category.val();
    if (
      problemCategory &&
      problemCategory.length &&
      problemCategory[0] !== "null" &&
      problemCategory[0] !== null
    ) {
      t.config.other_filters.problem_category = problemCategory;
    }

    var subCategory = t.filters.sub_category.val();
    if (
      subCategory &&
      subCategory.length &&
      subCategory[0] !== "null" &&
      subCategory[0] !== null
    ) {
      t.config.other_filters.sub_category = subCategory;
    }

    var creator = t.filters.creator.val();
    if (
      creator &&
      creator.length &&
      creator[0] !== "null" &&
      creator[0] !== null
    ) {
      t.config.other_filters.creator = creator;
    }

    var slaBreached = t.filters.sla_breached.val();
    if (slaBreached !== "" && slaBreached !== null) {
      t.config.other_filters.sla_breached = slaBreached;
    }

    var jobj = {
      search: t.content.find(".user-list-search").val() || "",
      other_filters: t.config.other_filters,
    };
    t.config.export_filters = btoa(JSON.stringify(jobj));

    t.updateFilterCount();
  };

  t.updateFilterCount = function () {
    var count = 0;
    var f = t.config.other_filters || {};

    if (f.completion_status && f.completion_status.length) count++;
    if (f.incident_status && f.incident_status.length) count++;
    if (f.priority && f.priority.length) count++;
    if (f.based_on) count++;
    if (f.department && f.department.length) count++;
    if (f.problem_category && f.problem_category.length) count++;
    if (f.sub_category && f.sub_category.length) count++;
    if (f.creator && f.creator.length) count++;
    if (f.sla_breached !== undefined && f.sla_breached !== "") count++;

    var $badge = t.content.find(".filter-count-badge"); 
    if (count > 0) {
      $badge.text(count).removeClass("d-none"); 
    } else {
      $badge.text("0").addClass("d-none");
    }
  };
  t.mdl.frmEl.company_id.select2({
    placeholder: "Select Company",
    allowClear: true,
    width: "100%",
    dropdownParent: t.mdl.frmEl.company_id.parent(),
    ajax: {
      url: t.config.url.getCompany,
      type: "GET",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          search: params.term || "",
          page: params.page || 1,
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        return {
          results: data.results,
          pagination: {
            more: data.pagination.more,
          },
        };
      },
      cache: true,
    },
  });

  t.mdl.frmEl.company_id.on("change", function () {

    t.mdl.frmEl.creatorId
      .val(null).empty().trigger("change");

    t.mdl.frmEl.departmentId
      .val(null).empty().trigger("change");

    t.mdl.frmEl.problemCategoryId
      .val(null).empty()
      .append(new Option(config.translations.Select_Problem_Category, ""))
      .trigger("change");
    t.data.problem_categories = [];

    t.mdl.frmEl.subCategoryId
      .val(null).empty().trigger("change");
    t.data.sub_categories = [];
    t.updateSubCategoryVisibility();

    t.mdl.frmEl.locationId
      .val(null).empty().trigger("change");

    t.mdl.frmEl.internalPlace
      .val(null).empty().trigger("change");

    t.mdl.frmEl.ticketId
      .val(null).empty().trigger("change");

    t.refillPriority();
  });

  t.userDropdownFormat = function (s, imgPaddingLeft) {
    if (s && typeof s.loading !== "undefined" && s.loading) {
      return $("<div>" + s.text + "</div>");
    }

    var email = s.email == null ? "" : s.email;
    var a = "";
    a += "<div class='row'>";
    a += "<div class='col-sm-10'>";
    a +=
      "<div class='so-t'><span style='padding-right:3px;'>👤</span></i>" +
      s.text +
      " ";
    a +=
      s.status == 1
        ? "<span class='active-user'></span>"
        : "<span class='inactive-user'></span>";
    a += "</div>";
    if (s.email != null && s.email != "") {
      a +=
        "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" +
        s.email +
        "</div>";
    }
    if (s.employee_num != null && s.employee_num != "") {
      a +=
        "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" +
        s.employee_num +
        "</div>";
    }
    a += "</div>";
    a += "<div class='col-sm-2'>";
    a +=
      "<div style='padding-left: " +
      imgPaddingLeft +
      ";'><img class='img-u' src='" +
      s.img_path +
      "'/></div>";
    a += "</div>";
    a += "</div>";
    return $("<div>" + a + "</div>");
  };

  t.mdl.frmEl.creatorId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.creatorId.parent(),
        ajax: {
          url: t.config.url.getUserByAjax,
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
        placeholder: config.translations.enter_first_few_letter,
        templateResult: function (data) {
          if (!data) return $("<div>No data</div>");
          var imgPaddingLeft = "30px";
          return t.userDropdownFormat(data, imgPaddingLeft);
        },
      }),
    )
    .on("change", $.proxy(t.creatorinfo.reload));

  t.mdl.frmEl.creatorId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.creatorId.parent(),
        ajax: {
          url: t.config.url.getUserByAjax,
          dataType: "json",
          delay: 300,
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
              company_id: t.mdl.frmEl.company_id.val(),
            };
          },
          transport: function (params, success, failure) {
            let companyId = t.mdl.frmEl.company_id.val();
            if (!companyId) {
              var data = { msg: "Please select a company first." };
              sweetAlert("center", "warning", data);
              return;
            }
            let request = $.ajax(params);
            request.then(success);
            request.fail(failure);
            return request;
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results,
              pagination: {
                more: data.pagination.more,
              },
            };
          },
          cache: true,
        },
        allowClear: true,
        placeholder: config.translations.enter_first_few_letter,
        templateResult: function (data) {
          if (!data) return $("<div>No data</div>");
          var imgPaddingLeft = "30px";
          return t.userDropdownFormat(data, imgPaddingLeft);
        },
      }),
    )
    .on("change", $.proxy(t.creatorinfo.reload));

  t.mdl.frmEl.departmentId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        ajax: {
          url: t.config.url.departments_with_company,
          dataType: "json",
          data: function (p) {
            return {
              search: p.term,
              page: p.page || 1,
              company_id: t.mdl.frmEl.company_id.val(),
            };
          },
          transport: function (params, success, failure) {
            let companyId = t.mdl.frmEl.company_id.val();
            if (!companyId) {
              var data = {
                msg: "Please select a company first.",
              };
              sweetAlert("center", "warning", data);
              return;
            }
            let request = $.ajax(params);
            request.then(success);
            request.fail(failure);
            return request;
          },
        },
        width: "100%",
        allowClear: true,
        placeholder: "Select the department",
      }),
    )
    .on("change", $.proxy(t.refillProblemCategory));

  t.mdl.frmEl.locationId.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.locationId.parent(),
      ajax: {
        url: t.config.url.getLocationByQuery,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            company_id: t.mdl.frmEl.company_id.val(),
          };
        },
        transport: function (params, success, failure) {
          let companyId = t.mdl.frmEl.company_id.val();
          if (!companyId) {
            var data = {
              msg: "Please select a company first.",
            };
            sweetAlert("center", "warning", data);
            return;
          }
          let request = $.ajax(params);
          request.then(success);
          request.fail(failure);
          return request;
        },
      },
      width: "100%",
      allowClear: true,
      placeholder: "Select the location",
    }),
  );

  t.mdl.frmEl.internalPlace.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.internalPlace.parent(),
      ajax: {
        url: t.config.url.ajaxGetInternalPlace,
        dataType: "json",
        data: function (p) {
          return {
            search: p.term,
            page: p.page || 1,
            location_id: t.mdl.frmEl.locationId.val(),
          };
        },
        transport: function (params, success, failure) {
          let locationId = t.mdl.frmEl.locationId.val();
          if (!locationId) {
            var data = {
              msg: "Please select a location first.",
            };
            sweetAlert("center", "warning", data);
            return;
          }
          let request = $.ajax(params);
          request.then(success);
          request.fail(failure);
          return request;
        },
      },
      width: "100%",
      allowClear: true,
      placeholder: "Select internal place",
    }),
  );

  t.export = function (e) {
    e.preventDefault();
    t.cache_filter_values();
    window.location =
      t.config.url.download_url + "?q=" + t.config.export_filters;
  };

  t.openFilter = function (e) {
    e.preventDefault();
    t.filterMdl.modal("show");
  };

  t.mdl.frmEl.ticketId.select2({
    width: "100%",
    placeholder: "Select the ticket number",
    allowClear: true,
    dropdownParent: t.mdl.frmEl.ticketId.closest(".input-group").parent(),
    ajax: {
      url: function (params) {
        return t.config.url.getTicketDetails;
      },
      dataType: "json",
      delay: 250,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
      },
      type: "POST",
      data: function (params) {
        return {
          q: params.term,
          page: params.page,
          department: t.mdl.frmEl.departmentId.val(),
          problem_category: t.mdl.frmEl.problemCategoryId.val(),
          sub_category: t.mdl.frmEl.subCategoryId.val(),
          company_id: t.mdl.frmEl.company_id.val(),
          location_id: t.mdl.frmEl.locationId.val(),
          internal_place_id: t.mdl.frmEl.internalPlace.val(),
        };
      },
      processResults: function (data, params) {
        params.page = params.page || 1;
        return {
          results: data.items,
          pagination: {
            more: params.page * 30 < data.total,
          },
        };
      },
      cache: true,
    },
    templateSelection: function (data) {
      if (!data.id) {
        return data.text;
      }
      let text =
        data.text && data.text.length > 50
          ? data.text.substr(0, 50) + "..."
          : data.text;
      return $('<span title="' + data.text + '">' + text + "</span>");
    },
  });

  t.mdl.frmEl.service_impacted.select2({
    width: "100%",
    tags: true,
    placeholder: "Enter service impacted",
    maximumSelectionLength: 10,
    tokenSeparators: [","],
    dropdownParent: t.mdl.frmEl.service_impacted
      .closest(".input-group")
      .parent(),
  });

  $.validator.addMethod(
    "summernoteRequired",
    function (value, element) {
      var code = $(element).summernote("code");
      var text = $("<div>").html(code).text().trim();
      return text.length > 0;
    },
    "This field is required.",
  );

  t.mdl.frmEl.content.summernote({
    inheritPlaceholder: true,
    placeholder: "write content here",
    toolbar: summernote_toolbar,
    icons: summernote_icons,
    styleTags: styleTags,
    minHeight: 120,
    focus: true,
    callbacks: {
      onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
      },
      onChange: function () {
        var code = t.mdl.frmEl.content.summernote("code");
        var text = $("<div>").html(code).text().trim();
        if (text.length > 0) {
          t.mdl.find("#shows_error").html("");
        }
      },
    },
  });

  t.mdl.frmEl.rca.summernote({
    inheritPlaceholder: true,
    placeholder: "write content here",
    toolbar: summernote_toolbar,
    icons: summernote_icons,
    styleTags: styleTags,
    minHeight: 120,
    focus: true,
    callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
    }
  });

  t.mdl.frmEl.why_incident_happened.summernote({
    inheritPlaceholder: true,
    placeholder: "write content here",
    toolbar: summernote_toolbar,
    icons: summernote_icons,
    styleTags: styleTags,
    minHeight: 120,
    focus: true,
    callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
        }
  });

  t.mdl.frmEl.preventive_measure_taken.summernote({
    inheritPlaceholder: true,
    placeholder: "write content here",
    toolbar: summernote_toolbar,
    icons: summernote_icons,
    styleTags: styleTags,
    minHeight: 120,
    focus: true,
    callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
    }
  });

  t.handlePageLengthChange = function (e) {
    if (e) e.preventDefault();

    var length = this.getSelectedPageLength();
    if (!this.dTable || !length) return;

    this.dTable.page.len(length).draw(false);
  };

  t.mdl.frmEl.status.select2(
    $.extend({}, select2Opts, {
      dropdownParent: t.mdl.frmEl.priorityId.parent(),
    }),
  );
  t.mdl.frmEl.problemCategoryId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.problemCategoryId.parent(),
        placeholder: config.translations.Select_Problem_Category,
      }),
    )
    .on("change", $.proxy(t.fillSla));
  t.mdl.frmEl.problemCategoryId.on("change", $.proxy(t.refillSubCategory));
  t.mdl.frmEl.subCategoryId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.subCategoryId.parent(),
      }),
    )
    .on("change", $.proxy(t.fillSla));
  t.mdl.frmEl.priorityId
    .select2(
      $.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.priorityId.parent(),
      }),
    )
    .on("change", $.proxy(t.refillTat));
  t.mdl.frmEl.problemCategoryId.on("select2:select", $.proxy(t.resetTicketId));
  t.mdl.frmEl.subCategoryId.on("select2:select", $.proxy(t.resetTicketId));

  t.content.on("click", ".btn-add-incident", t.addIncident);
  t.content.on("click", ".btn-edit-incident", $.proxy(t.editIncident));
  t.content.on("click", ".btn-delete-incident", $.proxy(t.deleteIncident));
  t.btn.export.off("click").on("click", $.proxy(t.export));
  t.filterMdl.on("click", "#advanced_filter", function (e) {
    e.preventDefault();
    t.cache_filter_values();
    t.dTbl.ajax.reload(null, false);
    t.filterMdl.modal("hide");
  });

  t.filters.clear.on("click", function (e) {
    e.preventDefault();

    t.filters.completion_status.val(null).trigger("change");
    t.filters.incident_status.val(null).trigger("change");
    t.filters.priority.val(null).trigger("change");
    t.filters.based_on.val(null).trigger("change");
    t.filters.department.val(null).trigger("change");
    t.filters.problem_category.empty().trigger("change");
    t.filters.sub_category.empty().trigger("change");
    t.filters.creator.val(null).trigger("change");
    t.filters.sla_breached.val("").trigger("change");

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

  t.table.on("click", ".btn-view-incident", $.proxy(t.viewIncident));
  t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
  t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
  t.table.on("click", ".read-more", $.proxy(t.openContentModal));
  t.content.on("change", ".incident-list-page-length", function (e) {
    e.preventDefault();
    var length = parseInt($(this).val());
    if (!length) return;
    t.dTbl.page.len(length).draw(false);
  });
  t.table.on("click", ".read-more-service", function () {
    var raw = $(this).attr("data-values");
    var title = $(this).attr("data-title");

    var values = [];
    try {
      raw = raw.replace(/&quot;/g, '"').replace(/&#39;/g, "'");
      values = JSON.parse(raw);
    } catch (e) {
      values = raw ? [raw] : [];
    }

    $("#readMoreModalTitle").text(title);
    $("#readMoreModalBody").html(
      '<div class="d-flex flex-wrap gap-1">' +
        values
          .map(function (v) {
            return `<span class="badge bg-dark text-white px-2 py-1">${v}</span>`;
          })
          .join("") +
        "</div>",
    );

    var modal = new bootstrap.Modal(document.getElementById("readMoreModal"));
    modal.show();
  });
  t.content.on("keyup", ".user-list-search", function (e) {
    if (e.key === "Enter") {
      t.dTbl.ajax.reload(null, false);
    }
  });
  t.content.on("click", ".btn-reload-list", function (e) {
    e.preventDefault();
    t.dTbl.ajax.reload(null, false);
  });
    t.mdl.frmEl.incident_end_date.on("change", function () {
      var startDate = t.mdl.frmEl.incident_start_date.val();
      var endDate = t.mdl.frmEl.incident_end_date.val();
      if (startDate && endDate) {
        var start = moment(startDate, "DD-MM-YYYY");
        var end = moment(endDate, "DD-MM-YYYY");
        if (end.isBefore(start)) {
            var data = {
              msg: "Incident end date can not less than incident start date",
            };
          t.mdl.frmEl.incident_end_date.val("");
            sweetAlert("center", "error", data);
          t.httpCall = true;
          return false;
        }
      }
    });

  t.mdl.frmEl.subCategoryIdCvr.hide();
};
