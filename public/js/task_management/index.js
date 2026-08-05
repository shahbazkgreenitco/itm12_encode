var Project = function (config, projectEl) {
  var t = this;
  t.config = config;
  t.content = $("#task-management-list-wrapper");
  t.mdl = t.content.find("#relevant-project-mdl");
  t.frm = t.content.find("#relevant-project-mdl-frm");

  t.meta_csrf = $("head").find('meta[name="csrf-token"]');
  t.frm.prepend(
    '<input type="hidden" name="_token" value="' +
      t.meta_csrf.attr("content") +
      '" />',
  );

  t.frmEl = {};
  t.frmEl.name = t.frm.find("#name");

  t.btn = {};
  t.btn.submit = t.frm.find("#btnSubmit");
  t.btn.clear = t.frm.find("#btnClear");

  t.httpCall = true;
  t.httpPostPath = t.config.url.addProject;

  t.open = function () {
    t.frmEl.name.val("");
    t.btn.submit.attr("disabled", false);
    t.mdl.modal("show");
  };

  t.handleSubmit = function (e) {
    e.preventDefault();
    if (t.frmValidator.form() === false) return false;
    if (t.httpCall !== true) return false;

    t.btn.submit.attr("disabled", true);
    t.httpCall = false;

    var http = $.ajax({
      url: t.httpPostPath,
      type: "POST",
      processData: false,
      contentType: false,
      data: new FormData(t.frm[0]),
    });

    http.done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          sweetAlert("center", "success", data);
          projectEl.val("");
          $(
            '<option value="' + data.project.id + '" selected>' + data.project.name + "</option>",
          ).appendTo(projectEl);
          projectEl.trigger("change");
          t.mdl.modal("hide");
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });
    http.fail(function () {
      sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
    });
    http.always(function () {
      t.httpCall = true;
      t.btn.submit.attr("disabled", false);
    });
  };

  t.btn.submit.on("click", $.proxy(t.handleSubmit, t));
};

var AddTaskModal = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#task-management-list-wrapper");

  t.mdl = t.content.find("#taskModal");
  t.frm = t.mdl.find("#task");
  t.frmEl = {};

  t.frmEl.company = t.mdl.find("#company_id");
  t.frmEl.taskId = t.mdl.find("#task_id");
  t.frmEl.name = t.mdl.find("#name");
  t.frmEl.status_id = t.mdl.find("#status_id");
  t.frmEl.priority_id = t.mdl.find("#priority_id");
  t.frmEl.type_id = t.mdl.find("#type_id");
  t.frmEl.ticket_id = t.mdl.find("#ticket_id");
  t.frmEl.department_id = t.mdl.find("#task_department_id");
  t.frmEl.problem_category_id = t.mdl.find("#task_problem_category_id");
  t.frmEl.sub_category_id = t.mdl.find("#task_sub_category_id");
  t.frmEl.subCategoryIdCvr = t.mdl.find("#task_sub_category_id_cvr");
  t.frmEl.project_id = t.mdl.find("#project_id");
  t.frmEl.change_id = t.mdl.find("#change_id");
  t.frmEl.start_date = t.mdl.find("#start_date");
  t.frmEl.due_date = t.mdl.find("#due_date");
  t.frmEl.end_date = t.mdl.find("#end_date");
  t.frmEl.description = t.mdl.find("#task_description");
  t.frmEl.assigned_to = t.mdl.find("#task_assigned_to");
  t.frmEl.is_visible_user = t.mdl.find("#is_visible_user");
  t.frmEl.cost = t.mdl.find("#cost");

  t.btn = {};
  t.btn.submit = t.mdl.find("#taskSubmit");

  t.httpCall = true;
  t.frmValidator = null;
  t.summernoteReady = false;
  t.flatpickrReady = false;
  t.problem_categories = [];
  t.ticket_data = null;
  t.ticket_add_mode = false;
  t.ticket_dept_id = null;
  t.ticket_dept_name = null;
  t.ticket_pro_id = null;
  t.ticket_pro_name = null;
  t.ticket_sub_id = null;
  t.ticket_sub_name = null;

  t.editMode   = false;   
  t.editTaskId = null;   
  t.editFlag   = false;

  // ✅ NEW: Added for preventing multiple API calls
  t._reloadTimer = null;
  t._lastLoadedDept = null;
  t._isLoadingTaskData = false;

  t.parentOf = function (el) {
    return el.parent();
  };
  t.rowOf = function (el) {
    return el.closest(".amg-form-field-row");
  };
  t.showRow = function (el) {
    t.rowOf(el).removeClass("d-none");
  };
  t.hideRow = function (el) {
    t.rowOf(el).addClass("d-none");
  };

  t.userDropdownFormat = function (s) {
    if (s && typeof s.loading !== "undefined" && s.loading) {
      return $("<div>" + s.text + "</div>");
    }
    var a = "<div class='row'><div class='col-sm-10'>";
    var truncText =
      s.text && s.text.length > 30
        ? s.text.substring(0, 30) + "..."
        : s.text || "";
    a +=
      "<div class='so-t'><i class='bi bi-person' style='padding-right:3px'></i>" +
      truncText;
    a +=
      s.status == 1
        ? "<span class='active-user'></span>"
        : "<span class='inactive-user'></span>";
    a += "</div>";
    if (s.email) {
      var truncEmail =
        s.email.length > 30 ? s.email.substring(0, 30) + "..." : s.email;
      a +=
        "<div class='so-t'><i class='bi bi-envelope' style='padding-right:3px'></i>" +
        truncEmail +
        "</div>";
    }
    if (s.employee_num) {
      a +=
        "<div class='so-t'><i class='bi bi-credit-card-2-front' style='padding-right:3px'></i>" +
        s.employee_num +
        "</div>";
    }
    a +=
      "</div><div class='col-sm-2'><img class='img-u' src='" +
      (s.img_path || "") +
      "'/></div></div>";
    return $("<div>" + a + "</div>");
  };

  t.initPriority = function () {
    t.frmEl.priority_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.priority_id),
      minimumResultsForSearch: -1,
    });
  };
  t.initStatus = function () {
    t.frmEl.status_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.status_id),
      minimumResultsForSearch: -1,
    });
  };

  t.initCompany = function () {
    t.frmEl.company.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.company),
      ajax: {
        url: t.config.url.getCompanies,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder: "Select Company",
      allowClear: true,
    });
  };

  t.initTypeSelect = function () {
    t.frmEl.type_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.type_id),
      placeholder: "Select Related To",
      allowClear: true,
    });
    t.frmEl.type_id.on("change", function () {
      t.typeIdChange();
    });
  };

  t.initAssignUserSelect = function (context) {
    if (t.frmEl.assigned_to.hasClass("select2-hidden-accessible")) {
      t.frmEl.assigned_to.val(null).trigger("change");
      t.frmEl.assigned_to.select2("destroy");
    }
    t.frmEl.assigned_to.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.assigned_to),
      ajax: {
        url: t.config.url.getUser,
        dataType: "json",
        delay: 250,
        data: function (p) {
          var d = { search: p.term || "", page: p.page || 1 };
          if (context === "task") {
            d.context = "task";
          }
          return d;
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.pagination && data.pagination.more },
          };
        },
      },
      templateResult: function (s) {
        if (!s || !s.id) return $("<div>No data</div>");
        return t.userDropdownFormat(s);
      },
      placeholder:
        t.config.translations.Select_task_assign || "Select Assigned To",
      allowClear: true,
    });
  };

  t.initProjectSelect = function () {
    t.frmEl.project_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.project_id),
      ajax: {
        url: t.config.url.getProjectsByQuery,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder:
        t.config.translations.select_task_project || "Select Project",
      allowClear: true,
    });
  };

  t.initChangeSelect = function () {
    t.frmEl.change_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.change_id),
      ajax: {
        url: t.config.url.getChangesByQuery,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { search: p.term || "", page: p.page || 1 };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          return {
            results: data.results || [],
            pagination: { more: data.has_more === true },
          };
        },
      },
      placeholder:
        t.config.translations.Select_task_record || "Select Change Record",
      allowClear: true,
    });
  };

  t.initTicketSelect = function () {
    t.frmEl.ticket_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.ticket_id),
      ajax: {
        url: t.config.url.getTickets,
        dataType: "json",
        delay: 250,
        data: function (p) {
          return { 
            search: p.term || "", 
            page: p.page || 1,
            company_id:t.frmEl.company.val(),
          };
        },
        processResults: function (data, params) {
          t.ticket_data = data.data;
          params.page = params.page || 1;
          return {
            results: (data.data || []).map(function (tk) {
              return { id: tk.id, text: tk.text };
            }),
            pagination: { more: data.next_page_url !== null },
          };
        },
      },
      placeholder: "Select Ticket",
      allowClear: true,
    });
  };

  // ✅ CHANGED: initDepartmentSelect with skip flag
  t.initDepartmentSelect = function () {
    t.frmEl.department_id
      .select2({
        width: "100%",
        dropdownParent: t.parentOf(t.frmEl.department_id),
        ajax: {
          url: t.config.url.departments_with_company,
          dataType: "json",
          delay: 200,
          data: function (p) {
            return { search: p.term || "", page: p.page || 1 };
          },
          processResults: function (data, params) {
            params.page = params.page || 1;
            return {
              results: data.results || [],
              pagination: { more: data.has_more === true },
            };
          },
        },
        placeholder: "Select Department",
        allowClear: true,
      })
      .on("change", function () {
        if (!t._isLoadingTaskData) {
          t.reloadProblemCategory();
        }
      });
  };

  // ✅ CHANGED: reloadProblemCategory with debounce and edit mode support
  t.reloadProblemCategory = function () {
    var dept = t.frmEl.department_id.val();
    var self = this;
    
    // Prevent duplicate calls for same department
    if (dept === self._lastLoadedDept && dept !== null) {
      return;
    }
    
    // Clear any pending timer
    clearTimeout(self._reloadTimer);
    
    self._reloadTimer = setTimeout(function() {
      self._lastLoadedDept = dept;
      
      self.frmEl.subCategoryIdCvr.addClass("d-none");
      self.frmEl.problem_category_id.empty().append(new Option("", "", false, false));
      self.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
      
      if (!dept || dept === "null") return;
      
      $.get(
        self.config.url.problem_categories_by_company + "/" + dept,
        function (data) {
          if (typeof data === "object" && data.data && data.data.length > 0) {
            self.problem_categories = data.data;
            $.each(data.data, function (i, k) {
              if (k.status != 0) {
                var label = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                var opt = new Option(label, k.id, false, false);
                $(opt).attr("title", k.name);
                self.frmEl.problem_category_id.append(opt);
              }
            });
            
            // ✅ Refresh select2
            self.frmEl.problem_category_id.trigger('change.select2');
            
            // Edit mode me problem category set karein
            if (self.editFlag && self._editData && self._editData.problem_category_id) {
              if (!self.frmEl.problem_category_id.find("option[value='" + self._editData.problem_category_id + "']").length) {
                var pcOpt = new Option(
                  self._editData.problem_category_name || self._editData.problem_category_id,
                  self._editData.problem_category_id,
                  true,
                  true
                );
                self.frmEl.problem_category_id.append(pcOpt);
              }
              self.frmEl.problem_category_id.val(self._editData.problem_category_id).trigger("change");
              self.frmEl.problem_category_id.trigger('change.select2');
            }
            
            if (self.ticket_pro_id && self.ticket_add_mode) {
              self.frmEl.problem_category_id.val(self.ticket_pro_id).trigger("change");
              self.frmEl.problem_category_id.trigger('change.select2');
            }
          }
        }
      );
    }, 200);
  };

  t.initProblemCategorySelect = function () {
    t.frmEl.problem_category_id
      .select2({
        width: "100%",
        dropdownParent: t.parentOf(t.frmEl.problem_category_id),
        placeholder: "Select Problem Category",
      })
      .on("change", function () {
        t.reloadSubCategory();
      });
  };

  // ✅ CHANGED: reloadSubCategory with sub array support
  t.reloadSubCategory = function () {
    var selectedPc = t.frmEl.problem_category_id.val();
    t.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
    
    if (!selectedPc) {
      t.frmEl.subCategoryIdCvr.addClass("d-none");
      return;
    }
    
    var item = t.problem_categories.find(function (pc) {
      return pc.id == selectedPc;
    });
    
    var hasSub = item && Array.isArray(item.sub) && item.sub.length > 0;
    
    if (hasSub) {
      t.frmEl.subCategoryIdCvr.removeClass("d-none");
      
      // ✅ Populate subcategories from the 'sub' array
      $.each(item.sub, function (i, subItem) {
        if (subItem.status != 0) {
          var label = subItem.name.length > 40 ? subItem.name.substring(0, 40) + "..." : subItem.name;
          var opt = new Option(label, subItem.id, false, false);
          $(opt).attr("title", subItem.name);
          t.frmEl.sub_category_id.append(opt);
        }
      });
      
      // ✅ Refresh select2
      t.frmEl.sub_category_id.trigger('change.select2');

      // ✅ Set subcategory value for edit mode
      if (t.editFlag && t._editData && t._editData.sub_category_id) {
        var subId = t._editData.sub_category_id;
        var subName = t._editData.sub_category_name || subId;
        
        // Check if option exists
        if (!t.frmEl.sub_category_id.find("option[value='" + subId + "']").length) {
          var opt = new Option(subName, subId, true, true);
          t.frmEl.sub_category_id.append(opt);
        }
        
        t.frmEl.sub_category_id.val(subId).trigger("change");
        t.frmEl.sub_category_id.trigger('change.select2');
      } 
      else if (t.ticket_sub_id && t.ticket_add_mode) {
        t.ticket_add_mode = false;
        if (!t.frmEl.sub_category_id.find("option[value='" + t.ticket_sub_id + "']").length) {
          var opt = new Option(t.ticket_sub_name, t.ticket_sub_id, true, true);
          t.frmEl.sub_category_id.append(opt);
        }
        t.frmEl.sub_category_id.val(t.ticket_sub_id).trigger("change");
        t.frmEl.sub_category_id.trigger('change.select2');
      } 
      else {
        t.frmEl.sub_category_id.trigger("change");
      }
    } else {
      t.frmEl.subCategoryIdCvr.addClass("d-none");
      t.frmEl.sub_category_id.empty();
    }
  };

  t.initSubCategorySelect = function () {
    t.frmEl.sub_category_id.select2({
      width: "100%",
      dropdownParent: t.parentOf(t.frmEl.sub_category_id),
      ajax: {
        url: t.config.url.sub_category,
        dataType: "json",
        delay: 200,
        data: function (p) {
          return {
            search: p.term || "",
            page: p.page || 1,
            id: [t.frmEl.problem_category_id.val()],
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data.results || [], function (item) {
              if (item.status === 0) return null;
              return { id: item.id, text: item.text };
            }),
          };
        },
      },
      placeholder:
        t.config.translations.select_sub_category || "Select Sub Category",
    });
  };

  t.initSummernote = function () {
    if (t.summernoteReady) return;
    t.frmEl.description.summernote({
      inheritPlaceholder: true,
      placeholder:t.config.translations.Enter_description || "Enter description",
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

    t.summernoteReady = true;
  };

  t.initDatepickers = function (allowPastDates) {
      if (t.flatpickrReady) return;
      
      // If allowPastDates is true, don't set minDate
      var minDate = allowPastDates ? null : new Date();
      
      t.frmEl.start_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.frmEl.due_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.frmEl.end_date.flatpickr({
          dateFormat: "d/m/Y H:i",
          enableTime: true,
          time_24hr: true,
          minuteIncrement: 15,
          minDate: minDate,
      });
      t.flatpickrReady = true;
  };

  t.setDateField = function (el, val) {
    if (!val) return;
    var fp = el[0] && el[0]._flatpickr;
    if (fp) {
        var dateObj = fp.parseDate(val, "d/m/Y H:i");
        if (dateObj) {
            fp.setDate(dateObj, true);
        } else {
            fp.setDate(val, true, "d/m/Y H:i");
        }
    } else {
      el.val(val);
    }
  };

  t.typeIdChange = function () {
    var v = t.frmEl.type_id.val();

    t.hideRow(t.frmEl.change_id);
    t.hideRow(t.frmEl.project_id);
    t.hideRow(t.frmEl.ticket_id);
    t.hideRow(t.frmEl.is_visible_user);
    t.hideRow(t.frmEl.department_id);
    t.hideRow(t.frmEl.problem_category_id);
    t.frmEl.subCategoryIdCvr.addClass("d-none");

    t.frmEl.change_id.val(null).trigger("change");
    t.frmEl.project_id.val(null).trigger("change");
    t.frmEl.ticket_id.val(null).trigger("change");
    t.frmEl.department_id.val(null).trigger("change");
    t.frmEl.problem_category_id.empty();
    t.frmEl.sub_category_id.val(null).trigger("change");

    if (v == 1) {
      t.initAssignUserSelect(null);
      t.showRow(t.frmEl.change_id);
    } else if (v == 2) {
      t.initAssignUserSelect(null);
      t.showRow(t.frmEl.project_id);
    } else if (v == 4) {
      t.initAssignUserSelect("task");
      t.showRow(t.frmEl.ticket_id);
      t.showRow(t.frmEl.is_visible_user);
      t.showRow(t.frmEl.department_id);
      t.showRow(t.frmEl.problem_category_id);
    } else {
      t.initAssignUserSelect(null);
    }
  };

  t.parseDate = function (val) {
      if (!val) return NaN;

      val = $.trim(val);

      var parts = val.split(' ');
      var dateParts = parts[0].split('/');

      if (dateParts.length !== 3) return NaN;

      var day = parseInt(dateParts[0], 10);
      var month = parseInt(dateParts[1], 10) - 1;
      var year = parseInt(dateParts[2], 10);

      var hour = 0;
      var minute = 0;

      if (parts.length > 1) {
          var timeParts = parts[1].split(':');

          hour = parseInt(timeParts[0], 10) || 0;
          minute = parseInt(timeParts[1], 10) || 0;
      }

      return new Date(year, month, day, hour, minute, 0);
  };

  $.validator.addMethod(
    "dueAfterStart",
    function (value) {
      var due = t.parseDate(value);
      var start = t.parseDate(t.frmEl.start_date.val());
      return !isNaN(due) && !isNaN(start) ? due >= start : true;
    },
    "Planned Complete Date must be on or after Work Start Date.",
  );

  $.validator.addMethod(
    "dueBeforeEnd",
    function (value) {
      var due = t.parseDate(value);
      var end = t.parseDate(t.frmEl.end_date.val());
      return !isNaN(due) && !isNaN(end) ? due >= end : true;
    },
    "Planned Complete Date must be on or after Actual Complete Date.",
  );

  $.validator.addMethod(
    "startBeforeEnd",
    function (value) {
      var start = t.parseDate(t.frmEl.start_date.val());
      var end = t.parseDate(value);
      return !isNaN(start) && !isNaN(end) ? start <= end : true;
    },
    "Actual Complete Date must be on or after Work Start Date.",
  );

  t.frmValidator = t.frm.validate({
    debug: false,
    rules: {
      company_id:{
        required: true
      },
      name: {
        required: true,
        maxlength:255,
        clean_text_only: true,
        noSpecialStart: true
      },
      status_id: {
        required: true,
        digits: true,
        str_name:true
      },
      priority_id: {
        required: true,
        digits: true,
        str_name:true
      },
      type_id: {
        required: true,
        digits: true,
        str_name:true
      },
      project_id: {
        str_name:true
      },
      change_id:{
        str_name:true
      },
      cost: {
        number: true,
      },
      description: {
        required: true,
        summernotes: true,
        maxSummernoteChars: 2000
      },
      due_date: {
        dueAfterStart: true,
        dueBeforeEnd: true
      },
      end_date: {
        dueBeforeEnd: true,
        startBeforeEnd: true
      },
    },
    errorPlacement: function (error, element) {
      if (element.attr('id') === 'task_description') {
        error.insertAfter(element.parent().find('.note-editor'));
      }
      else if (element.closest('.input-group').length) {
        error.insertAfter(element.closest('.input-group'));
      }
      else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element) {
      $(element).removeClass('is-invalid');
    },
    success: function (label) {
      label.remove();
    }
  });

  // ✅ CHANGED: resetForm with cleanup
  t.resetForm = function () {
    t._lastLoadedDept = null;
    clearTimeout(t._reloadTimer);
    t._isLoadingTaskData = false;
    t.flatpickrReady = false;
    t.frm[0].reset();
    t.editMode   = false;
    t.editTaskId = null;
    t.editFlag   = false;
    t._editData  = null;

    t.mdl.find(".modal-title").text(
      t.config.translations.add_task || "Add Task"
    );

    t.btn.submit.text(t.config.translations.create_btn || "Create");

    [
      t.frmEl.company, t.frmEl.type_id, t.frmEl.assigned_to,
      t.frmEl.project_id, t.frmEl.change_id, t.frmEl.ticket_id,
      t.frmEl.department_id, t.frmEl.problem_category_id, t.frmEl.sub_category_id,
    ].forEach(function (el) {
      if (el.hasClass("select2-hidden-accessible")) el.val(null).trigger("change");
    });

    t.frmEl.status_id.val(t.frmEl.status_id.find("option:first").val()).trigger("change");
    t.frmEl.priority_id.val(t.frmEl.priority_id.find("option:first").val()).trigger("change");

    if (t.summernoteReady) t.frmEl.description.summernote("code", "");

    [t.frmEl.start_date, t.frmEl.due_date, t.frmEl.end_date].forEach(function (el) {
      var fp = el[0] && el[0]._flatpickr;
      if (fp) fp.clear();
    });

    t.ticket_data = null; t.ticket_add_mode = false;
    t.ticket_dept_id = null; t.ticket_dept_name = null;
    t.ticket_pro_id = null; t.ticket_pro_name = null;
    t.ticket_sub_id = null; t.ticket_sub_name = null;
    t.problem_categories = [];

    t.typeIdChange();
    t.frm.find("label.error").remove();
    t.frm.find(".is-invalid, .has-error-group").removeClass("is-invalid has-error-group");
  };

  // ✅ CHANGED: loadTaskData with proper department/problem/subcategory handling
  t.loadTaskData = function (taskId) {
    t._isLoadingTaskData = true;
    t.btn.submit.prop("disabled", true);

    $.ajax({
      url: t.config.url.edit + "/" + taskId,
      type: "GET",
      dataType: "json",
      success: function (data) {
        if (!data || data.status !== "success") {
          sweetAlert("center", "error", { msg: "Could not load task data." });
          t.btn.submit.prop("disabled", false);
          t._isLoadingTaskData = false;
          return;
        }

        var task = data.task;          
        t._editData = task;             
        t.editFlag  = true;          

        t.frmEl.name.val(task.name || "");
        t.frmEl.cost.val(task.cost || "");
        t.frmEl.status_id.val(task.status_id).trigger("change");
        t.frmEl.priority_id.val(task.priority_id).trigger("change");

        t.frmEl.is_visible_user.prop("checked", task.is_visible_user == 1);

        if (task.company_id && task.company_name) {
          var companyOpt = new Option(task.company_name, task.company_id, true, true);
          t.frmEl.company.append(companyOpt).trigger("change");
        }

        t.frmEl.type_id.val(task.type_id).trigger("change");

        if (task.assigned_to && task.assigned_to_name) {
          var assignOpt = new Option(task.assigned_to_name, task.assigned_to, true, true);
          t.frmEl.assigned_to.append(assignOpt).trigger("change");
        }

        if (task.type_id == 1 && task.change_id && task.change_name) {
          var chgOpt = new Option(task.change_name, task.change_id, true, true);
          t.frmEl.change_id.append(chgOpt).trigger("change");
        } else if (task.type_id == 2 && task.project_id && task.project_name) {
          var projOpt = new Option(task.project_name, task.project_id, true, true);
          t.frmEl.project_id.append(projOpt).trigger("change");
        } else if (task.type_id == 4 && task.ticket_id && task.ticket_text) {
          var tickOpt = new Option(task.ticket_text, task.ticket_id, true, true);
          t.frmEl.ticket_id.append(tickOpt).trigger("change");
        }

        // ✅ FIXED: Department, Problem Category, Subcategory
        if (task.type_id == 4) {
          // STEP 1: Set Department
          if (task.department_id && task.department_name) {
            var deptOpt = new Option(task.department_name, task.department_id, true, true);
            t.frmEl.department_id.append(deptOpt);
            t.frmEl.department_id.val(task.department_id);
            
            // ✅ Refresh select2
            t.frmEl.department_id.trigger('change.select2');
            
            // STEP 2: Load categories and set values
            t._loadCategoriesAndSetValues(task);
          } else {
            // No department, try to set problem category directly
            t._setProblemAndSubCategoryDirect(task);
          }
        }

        t.setDateField(t.frmEl.start_date, task.start_date_formatted || "");
        t.setDateField(t.frmEl.due_date,   task.due_date_formatted   || "");
        t.setDateField(t.frmEl.end_date,   task.end_date_formatted   || "");
        var minDateForAll = null;
        if (task.start_date_formatted) {
            var startDateObj = t.parseDate(task.start_date_formatted);
            if (!isNaN(startDateObj)) {
                minDateForAll = startDateObj;
            }
        }
        if (!minDateForAll) {
            minDateForAll = new Date();
        }
        [t.frmEl.start_date, t.frmEl.due_date, t.frmEl.end_date].forEach(function(el) {
            var fp = el[0] && el[0]._flatpickr;
            if (fp) {
                fp.set('minDate', minDateForAll);
                console.log("minDate set for:", el.attr('id'), "to:", minDateForAll);
            }
        });

        if (t.summernoteReady) {
          t.frmEl.description.summernote("code", task.description || "");
        }

        t._isLoadingTaskData = false;
        t.btn.submit.prop("disabled", false);
      },
      error: function () {
        sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
        t.btn.submit.prop("disabled", false);
        t._isLoadingTaskData = false;
      },
    });
  };

  // ✅ NEW: Helper function to load categories and set values
  t._loadCategoriesAndSetValues = function (task) {
    var self = this;
    var dept = task.department_id;
    
    // Clear existing
    self.frmEl.subCategoryIdCvr.addClass("d-none");
    self.frmEl.problem_category_id.empty().append(new Option("", "", false, false));
    self.frmEl.sub_category_id.empty().append(new Option("", "", false, false));
    
    if (!dept || dept === "null") {
      self._setProblemAndSubCategoryDirect(task);
      return;
    }
    
    // Load categories from server
    $.get(
      self.config.url.problem_categories_by_company + "/" + dept,
      function (data) {
        if (typeof data === "object" && data.data && data.data.length > 0) {
          // ✅ Store full response with sub categories
          self.problem_categories = data.data;
          
          // Populate problem categories
          $.each(data.data, function (i, k) {
            if (k.status != 0) {
              var label = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
              var opt = new Option(label, k.id, false, false);
              $(opt).attr("title", k.name);
              self.frmEl.problem_category_id.append(opt);
            }
          });
          
          // ✅ Refresh select2
          self.frmEl.problem_category_id.trigger('change.select2');
          
          // ✅ Set problem category from task data
          if (task.problem_category_id) {
            // Check if option exists, if not create it
            if (!self.frmEl.problem_category_id.find("option[value='" + task.problem_category_id + "']").length) {
              var pcOpt = new Option(
                task.problem_category_name || task.problem_category_id, 
                task.problem_category_id, 
                true, 
                true
              );
              self.frmEl.problem_category_id.append(pcOpt);
            }
            
            self.frmEl.problem_category_id.val(task.problem_category_id).trigger("change");
            self.frmEl.problem_category_id.trigger('change.select2');
            
            // ✅ This will trigger reloadSubCategory which now handles sub from the response
            // Set subcategory after problem category change
            self._setSubCategoryAfterLoad(task);
          }
        } else {
          self._setProblemAndSubCategoryDirect(task);
        }
      }
    ).fail(function() {
      self._setProblemAndSubCategoryDirect(task);
    });
  };

  // ✅ NEW: Helper function to set problem and subcategory directly
  t._setProblemAndSubCategoryDirect = function (task) {
    var self = this;
    
    // Set problem category directly
    if (task.problem_category_id) {
      var pcOpt = new Option(task.problem_category_name || task.problem_category_id, task.problem_category_id, true, true);
      self.frmEl.problem_category_id.append(pcOpt);
      self.frmEl.problem_category_id.val(task.problem_category_id).trigger("change");
      self.frmEl.problem_category_id.trigger('change.select2');
      
      // Set subcategory
      self._setSubCategoryAfterLoad(task);
    }
  };

  // ✅ NEW: Helper function to set subcategory
  t._setSubCategoryAfterLoad = function (task) {
    var self = this;
    
    if (!task.sub_category_id) {
      console.log("No subcategory to set");
      return;
    }
    
    var attempts = 0;
    var maxAttempts = 15; // 15 * 200ms = 3 seconds max
    
    var checkAndSet = function() {
      attempts++;
      var subSelect = self.frmEl.sub_category_id;
      
      // ✅ Check if subcategory option exists
      var optionExists = subSelect.find("option[value='" + task.sub_category_id + "']").length > 0;
      
      if (optionExists) {
        // ✅ Option exists, set it
        subSelect.val(task.sub_category_id).trigger("change");
        subSelect.trigger('change.select2');
        self.frmEl.subCategoryIdCvr.removeClass("d-none");
        self.editFlag = false;
        
        console.log("✅ Subcategory set successfully:", task.sub_category_id, task.sub_category_name);
      } else if (attempts < maxAttempts) {
        // Wait and retry
        console.log("⏳ Waiting for subcategory option, attempt:", attempts);
        setTimeout(checkAndSet, 200);
      } else {
        // ✅ Max attempts reached, create option and set
        console.log("⚠️ Max attempts reached, creating subcategory option");
        
        // Find the subcategory name from problem_categories data
        var subName = task.sub_category_name || task.sub_category_id;
        
        // Try to find sub name from the stored data
        if (self.problem_categories && self.problem_categories.length > 0) {
          var pc = self.problem_categories.find(function(p) {
            return p.id == task.problem_category_id;
          });
          if (pc && pc.sub) {
            var sub = pc.sub.find(function(s) {
              return s.id == task.sub_category_id;
            });
            if (sub) {
              subName = sub.name;
            }
          }
        }
        
        // Create and set the option
        var subOpt = new Option(subName, task.sub_category_id, true, true);
        subSelect.append(subOpt);
        subSelect.val(task.sub_category_id).trigger("change");
        subSelect.trigger('change.select2');
        self.frmEl.subCategoryIdCvr.removeClass("d-none");
        self.editFlag = false;
        
        console.log("✅ Subcategory created and set:", task.sub_category_id, subName);
      }
    };
    
    // Start checking after a small delay
    setTimeout(checkAndSet, 300);
  };

  t.openForEdit = function (taskId) {
    t.resetForm();          
    t.editMode   = true;    
    t.editTaskId = taskId;

    t.mdl.find(".modal-title").text(
      t.config.translations.edit_task || "Edit Task"
    );
    t.btn.submit.text(t.config.translations.update_btn || "Update");
    t.frmEl.taskId.val(taskId);

    t.loadTaskData(taskId);
    t.mdl.modal("show");        
  };

  t.handleSubmit = function (e) {
    e.preventDefault();
    if (!t.frmValidator || !t.frmValidator.form()) return false;
    if (!t.httpCall) return false;

    t.btn.submit.prop("disabled", true);
    t.httpCall = false;

    var url = t.editMode
      ? t.config.url.updateTask + "/" + t.editTaskId
      : t.config.url.addTask;

    var http = $.ajax({
      url: url,
      type: "POST",
      processData: false,
      contentType: false,
      data: new FormData(t.frm[0]),
    });

    http.done(function (data) {
      if (typeof data === "object") {
        if (data.status === "success") {
          sweetAlert("center", "success", data);
          t.mdl.modal("hide");
          t.mdl.trigger("task:saved");
        } else {
          sweetAlert("center", "error", data);
        }
      }
    });

    http.fail(function () {
      sweetAlert("center", "error", { msg: t.config.translations.something_went_wrong });
    });

    http.always(function () {
      t.httpCall = true;
      t.btn.submit.prop("disabled", false);
    });
  };

  t.mdl.on("show.bs.modal", function () {
    t.initStatus();
    t.initPriority();
    t.initCompany();
    t.initTypeSelect();
    t.initAssignUserSelect(null);
    t.initProjectSelect();
    t.initChangeSelect();
    t.initTicketSelect();
    t.initDepartmentSelect();
    t.initProblemCategorySelect();
    t.initSubCategorySelect();
    t.initSummernote();
    t.initDatepickers(t.editMode);
    if (Array.isArray(t.config.company_defulte) && t.config.company_defulte.length === 1 && config.company_defulte[0].id) {
      let option = new Option(t.config.company_defulte[0].text, t.config.company_defulte[0].id, true, true);
      t.frmEl.company.append(option).trigger('change');
    }
  });

  t.frmEl.company.on('change',function(){
    t.frmEl.ticket_id.val(null).trigger("change")
    t.frmEl.department_id.val(null).trigger("change")
    t.frmEl.problem_category_id.val(null).trigger("change")
    t.frmEl.sub_category_id.val(null).trigger("change")
    t.frmEl.assigned_to.val(null).trigger("change");
  });

  t.btn.submit.off("click").on("click", function (e) {
    t.handleSubmit(e);
  });

  t.objProject = new Project(t.config, t.frmEl.project_id);
  t.mdl.on("click", ".show-add-proj-mdl", function (e) {
    e.preventDefault();
    t.objProject.open();
  });
};

// ============================================================
// LISTING FUNCTION (SAME AS BEFORE - NO CHANGES NEEDED)
// ============================================================
var Listing = function (config) {
  var t = this;
  t.config = config;
  t.content = $("#task-management-list-wrapper");
  t.body = t.content.find(".task-list-body");
  t.footer = t.content.find(".task-list-footer");
  t.search = t.content.find(".task-list-search");
  t.pageLimiter = t.content.find("#pageLimiter");
  t.sortbtns = t.content.find('#srqSortDrop');
  t.shortItems = t.content.find('.amg-sort-menu');
  t.sortAction = t.sortbtns.find('.sort-action');
  t.dropdownAction = t.sortbtns.find('.dropdown-action');
  t.perPage = 10;
  t.currentPage = 1;
  var select2Opts = { width: "100%" };

  t.taskModal = new AddTaskModal(config);

  t.mdl = t.content.find("#taskModal");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.frm = t.mdl.find("#task");
  t.mdl.frmEl = {};
  t.mdl.frmEl.id = t.mdl.frm.find("#task_id");

  t.filterMdl = t.content.find("#taskFilterModal");

  t.filterMdl.modal({
    backdrop: "static",
    keyboard: false,
    show: false,
  });

  t.mdl.on("task:saved", function () {
    t.load(t.currentPage || 1);
  });

  t.filterMdl = t.content.find("#taskFilterModal");
  t.filterMdl.modal({ backdrop: "static", keyboard: false, show: false });

  t.taskHistoryMdl = t.content.find("#taskHistoryModal");
  t.taskHistoryContainer = t.taskHistoryMdl.find("#task_history_container");
  t.taskHistoryList = t.taskHistoryMdl.find("#task_history_list");
  t.taskHistoryLoader = t.taskHistoryMdl.find(".task-history-loader");

  $(document).on('click', '.btn-history, .history-task', function(e) {
      e.preventDefault();
      var taskId = $(this).data('id');
      var historyUrl = t.config.mainfilter === 'archived'
          ? t.config.url.archived_history
          : t.config.url.history;
      var token = t.config.token || $('meta[name="csrf-token"]').attr('content');
      TaskHistory.open(taskId, historyUrl, token);
  });


  t.filters = {};
  t.filters.btnfilterclr = t.filterMdl.find("#clear");
  t.filters.status       = t.filterMdl.find("#filter_by_status");  
  t.filters.priority     = t.filterMdl.find("#filter_by_priority");
  t.filters.handlers     = t.filterMdl.find("#filter_by_task_handlers");
  t.filters.type         = t.filterMdl.find("#type_id");
  t.filters.based_on     = t.filterMdl.find("#filter_by_date");
  t.filters.daterange    = t.filterMdl.find("#daterange");

  /* task update Module */
  t.taskUpdateMdl = t.content.find("#task_update_details");
  t.frmUpdateTaskStatus = t.taskUpdateMdl.find("#frm_update_task_status");
  t.frmUpdateTaskStatus.el = {};
  t.frmUpdateTaskStatus.el.task_id = t.frmUpdateTaskStatus.find("#id");
  t.frmUpdateTaskStatus.el.statusWrapper = t.frmUpdateTaskStatus.find(".status-wrapper");
  t.frmUpdateTaskStatus.el.temp_id =  t.frmUpdateTaskStatus.find("#tmp_id");
  t.frmUpdateTaskStatus.el.task_status_id = t.frmUpdateTaskStatus.find('#task_status_id');
  t.frmUpdateTaskStatus.el.task_comment = t.frmUpdateTaskStatus.find("#task_comment");
  t.frmUpdateTaskStatus.el.attachment_updates = t.frmUpdateTaskStatus.find("#task_attachment_updates");
  t.frmUpdateTaskStatus.el.btnSubmit = t.taskUpdateMdl.find("#btnSubmit");
  t.taskStatusSummernoteReady = false;

  t.getTaskStatusComment = function () {
    var comment = "";

    if (t.taskStatusSummernoteReady) {
      comment = t.frmUpdateTaskStatus.el.task_comment.summernote('code') || "";
      if ($("<div>").html(comment.replace(/&nbsp;/g, " ")).text().trim().length === 0) {
        comment = t.frmUpdateTaskStatus.el.task_comment
          .next(".note-editor")
          .find(".note-editable")
          .html() || comment;
      }
    } else {
      comment = t.frmUpdateTaskStatus.el.task_comment.val() || "";
    }

    t.frmUpdateTaskStatus.el.task_comment.val(comment);
    return comment;
  };

  t.hasTaskStatusComment = function () {
    var comment = t.getTaskStatusComment();
    var text = $("<div>").html(comment.replace(/&nbsp;/g, " ")).text().trim();

    return text.length > 0;
  };

  t.showTaskStatusCommentError = function (message) {
    t.frmUpdateTaskStatus.find("#shows_error").html(
      typeof message === "undefined" ? "This field is required" : message
    );
  };

  t.initTaskStatusSummernote = function () {
    if (t.taskStatusSummernoteReady) return;

    t.frmUpdateTaskStatus.el.task_comment.summernote({
      inheritPlaceholder: true,
      placeholder: t.config.translations.Enter_description || "Enter description",
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
        onChange: function (contents) {
          t.frmUpdateTaskStatus.el.task_comment.val(contents || "");

          if (t.hasTaskStatusComment()) {
            t.showTaskStatusCommentError("");
          }
        }
      }
    });

    t.taskStatusSummernoteReady = true;
  };

  $.validator.addMethod("task_status_summernotes", function () {
    t.initTaskStatusSummernote();
    return t.hasTaskStatusComment();
  }, 'This field is required');

  t.frmTaskStatusValidator = t.frmUpdateTaskStatus.validate({
    ignore: [],
    rules: {
      task_update_comment: {
        task_status_summernotes: true,
      },
    },
    errorPlacement: function (error) {
      t.showTaskStatusCommentError(error.text());
    },
    success: function () {
      t.showTaskStatusCommentError("");
    },
  });

  t.frmUpdateTaskStatus.el.btnSubmit.on("click", function (e) {
    e.preventDefault();

    t.initTaskStatusSummernote();
    var taskComment = t.getTaskStatusComment();

    if (!t.hasTaskStatusComment()) {
      t.showTaskStatusCommentError("This field is required");
      return false;
    }

    t.showTaskStatusCommentError("");
    var formData = new FormData();
    formData.append('_token', t.config.token || "");
    formData.append('id', t.frmUpdateTaskStatus.el.task_id.val() || "");
    formData.append('status_id', t.frmUpdateTaskStatus.el.task_status_id.val() || 1);
    formData.append('task_comment', taskComment || "");
    formData.append('temp_id', t.frmUpdateTaskStatus.el.temp_id.val() || "");
    formData.append('type_id', 4);
    formData.append('ticketmodule', true);

    let http = $.ajax({
      url: t.config.url.ajaxStatusEditTask + "/" + t.frmUpdateTaskStatus.el.task_id.val(),
      type: "POST",
      processData: false,
      contentType: false,
      data: formData,
    });

    http.done(function (data) {
      if (typeof data == "object") {
        if (data.status === "success") {
          sweetAlert('center', 'success', data);
          t.taskUpdateMdl.modal("hide");
          t.frmUpdateTaskStatus.el.attachment_updates.empty();
          t.load(1);
        } else {
          sweetAlert('center', 'error', data);
        }
      }
    });
    http.fail(function (xhr) {
      var data = {
        'msg': config.translations.something_went_wrong
      };
      sweetAlert('center', 'error', data);
    });

    http.always(function () {
      t.frmUpdateTaskStatus.el.btnSubmit.prop("disabled", false);
    });
  });


  t.refillTaskStatus = function(e) {
    if (typeof e !== "undefined") {
      e.preventDefault();
    }
    t.frmUpdateTaskStatus.el.task_status_id.empty();
    $.each(t.config.task_statuses, function (i, k) {
      t.frmUpdateTaskStatus.el.task_status_id.append(new Option(k.name, k.id));
    });
    t.frmUpdateTaskStatus.el.task_status_id.trigger("change");
  }

  t.frmUpdateTaskStatus.el.task_status_id.select2({
    width: '100%',
    dropdownParent: t.frmUpdateTaskStatus.el.task_status_id.parent(),
  })

  t.frmTaskCommentTokenize = function () {
    var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6);
    t.frmUpdateTaskStatus.el.temp_id.val(v);
  };

  $(document).on('click','.update-task-status',function(){
    t.task_id = $(this).data('id');
    t.status_id = $(this).data('status_id') || $(this).attr('data-status_id');
    t.frmUpdateTaskStatus.el.statusWrapper.show();
    t.refillTaskStatus(); 
    t.frmTaskCommentTokenize();
    t.frmUpdateTaskStatus.el.task_id.val(t.task_id);
    t.frmUpdateTaskStatus.el.task_status_id.val(String(t.status_id)).trigger("change");
    t.initTaskStatusSummernote();
    t.frmUpdateTaskStatus.el.task_comment.summernote('code', '');
    t.showTaskStatusCommentError("");
    t.taskUpdateMdl.modal('show');
  })

  $(document).on('click','.incomplete-task',function(){
    t.task_id = $(this).data('id');
    t.frmTaskCommentTokenize();
    t.frmUpdateTaskStatus.el.statusWrapper.hide();
    t.frmUpdateTaskStatus.el.task_id.val(t.task_id);
    t.frmUpdateTaskStatus.el.task_status_id.val(1).trigger("change");
    t.initTaskStatusSummernote();
    t.frmUpdateTaskStatus.el.task_comment.summernote('code', '');
    t.showTaskStatusCommentError("");
    t.frmUpdateTaskStatus.el.attachment_updates.empty();
    t.taskUpdateMdl.modal('show');
  })

  t.filters.fun = {
    reload_getTaskStatus: function () {
      t.filters.status.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.status.parent(),
        ajax: {
          url: t.config.url.getTaskStatus,
          dataType: "json",
          delay: 300,
          data: function (p) { return { search: p.term, page: p.page || 1 }; },
        },
        allowClear: true,
        placeholder: config.translations.filter_by_status || "Filter by Status",
      }));
      t.filters.status.trigger("change");
    },

    reload_getTaskPriority: function () {
      t.filters.priority.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.priority.parent(),
        ajax: {
          url: t.config.url.getTaskPriority,
          dataType: "json",
          delay: 300,
          data: function (p) { return { search: p.term, page: p.page || 1 }; },
        },
        allowClear: true,
        placeholder: config.translations.Filter_By_Priority || "Filter by Priority",
      }));
      t.filters.priority.trigger("change");
    },

    reload_getTaskHandler: function () {
      t.filters.handlers.select2($.extend({}, select2Opts, {
        dropdownParent: t.filters.handlers.parent(),
        ajax: {
          url: t.config.url.getUserByAjax,
          dataType: "json",
          delay: 300,
          data: function (p) { return { search: p.term, page: p.page || 1 }; },
        },
        allowClear: true,
        placeholder: config.translations.By_Task_Handler || "Filter by Task Handler",
      }));
      t.filters.handlers.trigger("change");
    },
  };

  t.filters.based_on.select2($.extend({}, select2Opts, {
    dropdownParent: t.filters.based_on.parent(),
    allowClear: true,
    placeholder: "No Filter",
  }));

  t.filters.type.select2($.extend({}, select2Opts, {
    dropdownParent: t.filters.type.parent(),
    allowClear: true,             
    placeholder: "Filter by Related To",
  }));

  t.filters.fun.reload_getTaskStatus();
  t.filters.fun.reload_getTaskPriority();
  t.filters.fun.reload_getTaskHandler();

  var drStart = moment().startOf("day");
  var drEnd   = moment().endOf("day");
  t.filterMdl.find("#reportrange span").html(
    drStart.format("DD-MM-YYYY HH:mm:ss") + " - " + drEnd.format("DD-MM-YYYY HH:mm:ss")
  );
  t.filters.daterange.val(
    drStart.format("YYYY-MM-DD HH:mm:ss") + " - " + drEnd.format("YYYY-MM-DD HH:mm:ss")
  );
  t.filterMdl.find("#reportrange").daterangepicker(
    { startDate: drStart, endDate: drEnd, timePicker: true, locale: { format: "DD-MM-YYYY HH:mm:ss" } },
    function (s, e) {
      t.filterMdl.find("#reportrange span").html(
        s.format("DD-MM-YYYY HH:mm:ss") + " - " + e.format("DD-MM-YYYY HH:mm:ss")
      );
      t.filters.daterange.val(
        s.format("YYYY-MM-DD HH:mm:ss") + " - " + e.format("YYYY-MM-DD HH:mm:ss")
      );
    }
  );

  t.cache_filter_values = function () {
    t.config.other_filters = {};

    var status = t.filters.status.val();
    if (status && status != "null") {
      t.config.other_filters.status = status;
    }

    var priority = t.filters.priority.val();
    if (priority && priority != "null") {
      t.config.other_filters.priority = priority;
    }

    var handlers = t.filters.handlers.val();
    if (handlers && handlers != "null") {
      t.config.other_filters.handlers = handlers;
    }

    var typeVal = t.filters.type.val();
    if (typeVal && typeVal != "null" && typeVal !== "") {
      t.config.other_filters.type = typeVal;
    }

    var basedOn = t.filters.based_on.val();
    if (basedOn && basedOn != "null" && basedOn !== "") {
      t.config.other_filters.based_on = basedOn;
      var daterange = t.filters.daterange.val();
      if (daterange && daterange != "null") {
        t.config.other_filters.daterange = daterange;
      }
    }

    var jobj = { search: t.config.search || "", other_filters: t.config.other_filters };
    t.config.export_filters = btoa(JSON.stringify(jobj));
    t.updateFilterCount();
  };

  t.updateFilterCount = function () {
    var count = 0;
    var f = t.config.other_filters || {};
    if (f.status   && f.status.length)   count++;
    if (f.priority && f.priority.length) count++;
    if (f.handlers && f.handlers.length) count++;
    if (f.type)                          count++;   
    if (f.based_on)                      count++;

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
    t.load(1);
    t.filterMdl.modal("hide");
  });

  t.filters.btnfilterclr.on("click", function (e) {
    e.preventDefault();
    t.filters.status.val(null).trigger("change");
    t.filters.priority.val(null).trigger("change");
    t.filters.handlers.val(null).trigger("change");
    t.filters.type.val(null).trigger("change");    
    t.filters.based_on.val(null).trigger("change");

    var s  = moment().startOf("day");
    var e2 = moment().endOf("day");
    t.filterMdl.find("#reportrange span").html(
      s.format("DD-MM-YYYY HH:mm:ss") + " - " + e2.format("DD-MM-YYYY HH:mm:ss")
    );
    t.filters.daterange.val(
      s.format("YYYY-MM-DD HH:mm:ss") + " - " + e2.format("YYYY-MM-DD HH:mm:ss")
    );
    var $rr = t.filterMdl.find("#reportrange");
    if ($rr.data("daterangepicker")) {
      $rr.data("daterangepicker").setStartDate(s);
      $rr.data("daterangepicker").setEndDate(e2);
    }

    t.config.other_filters = {};
    t.updateFilterCount();
    t.filterMdl.modal("hide");
    t.load(1);
  });

  t.openFilter = function (e) {
    e.preventDefault();
    t.filterMdl.modal("show");
  };

  t.renderRow = function (task) {
    var taskStatusId = parseInt(task.task_status_id || task.status_id || 0, 10);
    var priorityColor = { 'Urgency': '#ef2424', 'High': '#fdb012', 'Medium': '#0c0dd8', 'Low': '#58c10f' };
    var pColor = priorityColor[task.priorityName] || '#6c757d';

    var tatHtml = '';
    if (taskStatusId == 5 || taskStatusId == 6 || taskStatusId == 7 || taskStatusId == 9) {
      tatHtml = '<span class="b5-text">Resolved: ' + (task.resolved_at_format || '') + '</span>';
    } else if (task.tat_expire_format === 'Overdue') {
      tatHtml = '<span class="b5-text" style="color:#ef4444;font-weight:600;">Overdue</span>';
    } else if (task.tat_expire_format) {
      tatHtml = '<span class="task-countdown b5-text" data-countdown="' + task.tat_expire_format + '"></span>';
    }

    var sColor = '#008D2D', sBg = '#E8FFEF';
    if (taskStatusId == 5 || taskStatusId == 6 || taskStatusId == 7 || taskStatusId == 9) { sColor = '#008D2D'; sBg = '#E8FFEF'; }
    else if (taskStatusId == 2 || taskStatusId == 3) { sColor = '#b45309'; sBg = '#fffbeb'; }
    else { sColor = '#008D2D'; sBg = '#E8FFEF'; }

    var editBtn = '', viewBtn = '', infoBtn = '', deleteBtn = '', historyBtn = '', updateBtn = '';

    if (config.permissions && config.permissions.indexOf('TaskEdit') !== -1) {
      editBtn = [
        '<div class="task-card-btn" data-bs-toggle="tooltip" title="' + t.config.translations.edit + '"',
        '    data-task-action="edit" data-id="' + task.id + '">',
        '  <i class="bi bi-pencil-square"></i> ',
        '</div>'
      ].join('');
    }

    var infoUrl = (config.mainfilter === 'archived') ? config.url.archived_info : config.url.info;
    infoBtn = [
      '<a class="task-card-btn" data-bs-toggle="tooltip" title="' + t.config.translations.info + '"',
      ' data-task-action="info" target="_blank" href="' + infoUrl + '/' + task.id + '">',
      ' <i class="bi bi-eye"></i>',
      '</a>'
    ].join('');

    deleteBtn = [
      '<div class="task-card-btn delete-task" data-bs-toggle="tooltip" title="' + t.config.translations.delete + '"',
      '    data-task-action="delete" data-id="' + task.id + '">',
      ' <i class="bi bi-trash3"></i> ',
      '</div>'
    ].join('');

    historyBtn = [
      '<div class="task-card-btn btn-history" data-bs-toggle="tooltip" title="' + t.config.translations.history + '"',
      'data-id="' + task.id + '">',
      ' <i class="bi bi-clock-history"></i> ',
      '</div>'
    ].join('');

    if (taskStatusId == 7 || taskStatusId == 9) {
      updateBtn = [
        '<div class="task-card-btn incomplete-task" data-bs-toggle="tooltip" title="' + (t.config.translations.reopen_task || "Reopen Task") + '"',
        '    data-id="' + task.id + '">',
        '  <i class="bi bi-folder-symlink"></i> ',
        '</div>'
      ].join('');
    } else {
      if (task.assigned_to == config.auth_user_id) {
        updateBtn = [
          '<div class="task-card-btn update-task-status" data-bs-toggle="tooltip" title="' + t.config.translations.update + '"',
          '    data-id="' + task.id + '" data-status_id="' + taskStatusId + '">',
          '  <i class="bi bi-folder-check"></i> ',
          '</div>'
        ].join('');
      }
    }

    var assignedName = task.assignedTo || '';
    var assignedImg = assignedName
      ? (task.assignedToImg
        ? '<img src="' + task.assignedToImg + '" alt="" style="width:20px;height:20px;border-radius:50%;">'
        : '<span style="display:inline-flex;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4338ca);align-items:center;justify-content:center;font-size:7px;font-weight:700;color:#fff;">'
          + assignedName.split(' ').map(function(w){ return w.charAt(0); }).join('').substring(0,2).toUpperCase()
          + '</span>')
      : '';

    var creatorName = task.creatorName || task.creator_name || '';
    var creatorInitials = creatorName.split(' ').map(function(w){ return w.charAt(0); }).join('').substring(0,2).toUpperCase();
    var creatorImg = creatorName
      ? (task.creatorImg
        ? '<img src="' + task.creatorImg + '" alt="" style="width:20px;height:20px;border-radius:50%;">'
        : '<span style="display:inline-flex;width:20px;height:20px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4338ca);align-items:center;justify-content:center;font-size:7px;font-weight:700;color:#fff;">'
          + creatorInitials
          + '</span>')
      : '';

    function metaItem(value, iconHtml, labelHtml, valueHtml) {
      if (!value) return '';
      return '<div class="meta-item">' + iconHtml + labelHtml + valueHtml + '</div>';
    }

    function withDividers(items) {
      return items.filter(Boolean).join('<div class="divider"></div>');
    }

    var calIcon = '<svg class="opacity-40" width="18" height="18" viewBox="0 0 22 21" fill="none"><path d="M0.75 10.25C0.75 6.479 0.75 4.593 1.922 3.422C3.094 2.251 4.979 2.25 8.75 2.25H12.75C16.521 2.25 18.407 2.25 19.578 3.422C20.749 4.594 20.75 6.479 20.75 10.25V12.25C20.75 16.021 20.75 17.907 19.578 19.078C18.406 20.249 16.521 20.25 12.75 20.25H8.75C4.979 20.25 3.093 20.25 1.922 19.078C0.751 17.906 0.75 16.021 0.75 12.25V10.25Z" stroke="currentColor" stroke-width="1.5"/><path d="M5.75 2.25V0.75M15.75 2.25V0.75M1.25 7.25H20.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>';
    var personBoxIcon = '<svg class="opacity-40" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7 18V17C7 14.2386 9.23858 12 12 12C14.7614 12 17 14.2386 17 17V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M12 12C13.6569 12 15 10.6569 15 9C15 7.34315 13.6569 6 12 6C10.3431 6 9 7.34315 9 9C9 10.6569 10.3431 12 12 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 3.6V20.4C21 20.7314 20.7314 21 20.4 21H3.6C3.26863 21 3 20.7314 3 20.4V3.6C3 3.26863 3.26863 3 3.6 3H20.4C20.7314 3 21 3.26863 21 3.6Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    var relatedIcon = '<svg width="18" height="18" class="opacity-40" viewBox="0 0 24 24" fill="none"><path d="M18 21C19.1046 21 20 20.1046 20 19C20 17.8954 19.1046 17 18 17C16.8954 17 16 17.8954 16 19C16 20.1046 16.8954 21 18 21Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 7C7.10457 7 8 6.10457 8 5C8 3.89543 7.10457 3 6 3C4.89543 3 4 3.89543 4 5C4 6.10457 4.89543 7 6 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18 17V7C18 7 18 5 16 5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 7V17C6 17 6 19 8 19H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 7.5L12.5 5L15 2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 16.5L11 19L8.5 21.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    var moduleIcon = '<svg class="opacity-40" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.6 1.2C9.12261 1.2 8.66477 1.38964 8.32721 1.72721C7.98964 2.06477 7.8 2.52261 7.8 3C7.8 3.47739 7.98964 3.93523 8.32721 4.27279C8.66477 4.61036 9.12261 4.8 9.6 4.8C10.0774 4.8 10.5352 4.61036 10.8728 4.27279C11.2104 3.93523 11.4 3.47739 11.4 3C11.4 2.52261 11.2104 2.06477 10.8728 1.72721C10.5352 1.38964 10.0774 1.2 9.6 1.2ZM6.6 3C6.6 2.20435 6.91607 1.44129 7.47868 0.87868C8.04129 0.316071 8.80435 0 9.6 0C10.3956 0 11.1587 0.316071 11.7213 0.87868C12.2839 1.44129 12.6 2.20435 12.6 3C12.6 3.79565 12.2839 4.55871 11.7213 5.12132C11.1587 5.68393 10.3956 6 9.6 6C8.80435 6 8.04129 5.68393 7.47868 5.12132C6.91607 4.55871 6.6 3.79565 6.6 3ZM16.2 2.4C15.8817 2.4 15.5765 2.52643 15.3515 2.75147C15.1264 2.97652 15 3.28174 15 3.6C15 3.91826 15.1264 4.22348 15.3515 4.44853C15.5765 4.67357 15.8817 4.8 16.2 4.8C16.5183 4.8 16.8235 4.67357 17.0485 4.44853C17.2736 4.22348 17.4 3.91826 17.4 3.6C17.4 3.28174 17.2736 2.97652 17.0485 2.75147C16.8235 2.52643 16.5183 2.4 16.2 2.4ZM13.8 3.6C13.8 2.96348 14.0529 2.35303 14.5029 1.90294C14.953 1.45286 15.5635 1.2 16.2 1.2C16.8365 1.2 17.447 1.45286 17.8971 1.90294C18.3471 2.35303 18.6 2.96348 18.6 3.6C18.6 4.23652 18.3471 4.84697 17.8971 5.29706C17.447 5.74714 16.8365 6 16.2 6C15.5635 6 14.953 5.74714 14.5029 5.29706C14.0529 4.84697 13.8 4.23652 13.8 3.6ZM1.8 3.6C1.8 3.28174 1.92643 2.97652 2.15147 2.75147C2.37652 2.52643 2.68174 2.4 3 2.4C3.31826 2.4 3.62348 2.52643 3.84853 2.75147C4.07357 2.97652 4.2 3.28174 4.2 3.6C4.2 3.91826 4.07357 4.22348 3.84853 4.44853C3.62348 4.67357 3.31826 4.8 3 4.8C2.68174 4.8 2.37652 4.67357 2.15147 4.44853C1.92643 4.22348 1.8 3.91826 1.8 3.6ZM3 1.2C2.36348 1.2 1.75303 1.45286 1.30294 1.90294C0.852856 2.35303 0.6 2.96348 0.6 3.6C0.6 4.23652 0.852856 4.84697 1.30294 5.29706C1.75303 5.74714 2.36348 6 3 6C3.63652 6 4.24697 5.74714 4.69706 5.29706C5.14714 4.84697 5.4 4.23652 5.4 3.6C5.4 2.96348 5.14714 2.35303 4.69706 1.90294C4.24697 1.45286 3.63652 1.2 3 1.2ZM3.72 15.5976L3.6 15.6C2.96348 15.6 2.35303 15.3471 1.90294 14.8971C1.45286 14.447 1.2 13.8365 1.2 13.2V8.7C1.2 8.62044 1.23161 8.54413 1.28787 8.48787C1.34413 8.43161 1.42044 8.4 1.5 8.4H3.6168C3.6648 7.9596 3.8208 7.5504 4.0548 7.2H1.5C0.672 7.2 2.14057e-08 7.872 2.14057e-08 8.7V13.2C-5.5999e-05 13.7137 0.109846 14.2215 0.322322 14.6893C0.534798 15.157 0.844925 15.5739 1.23187 15.9118C1.61881 16.2498 2.0736 16.501 2.56568 16.6486C3.05776 16.7962 3.57574 16.8368 4.0848 16.7676C3.92358 16.3911 3.80135 15.999 3.72 15.5976ZM15.1152 16.7676C15.2736 16.7892 15.4352 16.8 15.6 16.8C16.5548 16.8 17.4705 16.4207 18.1456 15.7456C18.8207 15.0705 19.2 14.1548 19.2 13.2V8.7C19.2 7.872 18.528 7.2 17.7 7.2H15.1452C15.3804 7.5504 15.5352 7.9596 15.5832 8.4H17.7C17.7796 8.4 17.8559 8.43161 17.9121 8.48787C17.9684 8.54413 18 8.62044 18 8.7V13.2C18.0001 13.5253 17.934 13.8473 17.8058 14.1463C17.6776 14.4453 17.49 14.7151 17.2543 14.9394C17.0186 15.1636 16.7398 15.3376 16.4348 15.4507C16.1298 15.5639 15.8049 15.6139 15.48 15.5976C15.3987 15.999 15.2764 16.3911 15.1152 16.7676ZM6.3 7.2C5.472 7.2 4.8 7.872 4.8 8.7V14.4C4.8 15.673 5.30571 16.8939 6.20589 17.7941C7.10606 18.6943 8.32696 19.2 9.6 19.2C10.873 19.2 12.0939 18.6943 12.9941 17.7941C13.8943 16.8939 14.4 15.673 14.4 14.4V8.7C14.4 7.872 13.728 7.2 12.9 7.2H6.3ZM6 8.7C6 8.62044 6.03161 8.54413 6.08787 8.48787C6.14413 8.43161 6.22044 8.4 6.3 8.4H12.9C12.9796 8.4 13.0559 8.43161 13.1121 8.48787C13.1684 8.54413 13.2 8.62044 13.2 8.7V14.4C13.2 15.3548 12.8207 16.2705 12.1456 16.9456C11.4705 17.6207 10.5548 18 9.6 18C8.64522 18 7.72955 17.6207 7.05442 16.9456C6.37928 16.2705 6 15.3548 6 14.4V8.7Z" fill="currentColor"></path></svg>';
    var companyIcon = '<svg width="18" height="18" class="opacity-40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 9.01L10.01 8.99889" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 9.01L14.01 8.99889" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10 13.01L10.01 12.9989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 13.01L14.01 12.9989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M10 17.01L10.01 16.9989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M14 17.01L14.01 16.9989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 20.4V5.6C6 5.26863 6.26863 5 6.6 5H12V3.6C12 3.26863 12.2686 3 12.6 3H17.4C17.7314 3 18 3.26863 18 3.6V20.4C18 20.7314 17.7314 21 17.4 21H6.6C6.26863 21 6 20.7314 6 20.4Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>';

    var allMetaItems = withDividers([
      metaItem(
        task.due_date_at,
        '<div class="d-flex gap-1 justify-content-center align-items-center">' + calIcon + '</div>',
        '<span class="b5-text fw-bold"> Due Date :</span>',
        '<span class="b5-text">' + (task.due_date_at || '') + '</span>'
      ),
      metaItem(
        task.start_date_at,
        calIcon,
        '<span class="b5-text fw-bold"> Start Date :</span>',
        '<span class="b5-text">' + (task.start_date_at || '') + '</span>'
      ),
      metaItem(
        task.assignedTo,
        personBoxIcon,
        '<span class="b5-text fw-bold"> Assigned To :</span>',
        '<div class="meta-item">' + assignedImg + '<span class="b5-text">' + (task.assignedTo || '') + '</span></div>'
      ),
      metaItem(
        task.relatedTo,
        relatedIcon,
        '<span class="b5-text fw-bold"> Related To :</span>',
        '<span class="b5-text">' + (task.relatedTo || '') + '</span>'
      ),
      metaItem(
        task.created_by_module,
        moduleIcon,
        '<span class="b5-text fw-bold"> Created By Module:</span>',
        '<span class="b5-text">' + (task.created_by_module || '') + '</span>'
      ),
      metaItem(
        task.companyName,
        companyIcon,
        '',
        '<span class="b5-text">' + (task.companyName || '') + '</span>'
      ),
      metaItem(
        creatorName,
        personBoxIcon,
        '<span class="b5-text fw-bold"> Creator :</span>',
        '<div class="meta-item">' + creatorImg + '<span class="b5-text">' + creatorName + '</span></div>'
      )
    ]);

    return [
      '<div class="ticket-card">',
      '  <div class="ticket-left">',
      '    <div class="ticket-content p-3">',
      '      <div class="d-flex align-items-center gap-2">',
      '        <a href="' + infoUrl + '/' + task.id + '" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none">',
      '          <p class="b3-text fw-bold mb-0">' + (task.name || '') + '</p>',
      '          <span class="ticket-id">#' + (task.task_id_formatted || task.id) + '</span>',
      '        </a>',
      '      </div>',
      allMetaItems ? ' <div class="ticket-meta d-flex align-items-center flex-wrap gap-2 mt-2">' + allMetaItems + '</div>' : '',
      '    </div>',
      '    <div class="ticket-right">',
      '      <div class="p-3">',
      '        <div class="info-row">',
      '          <span class="label">',
      '            <svg width="12" class="opacity-30" height="15" viewBox="0 0 12 20" fill="none"><path d="M12 20H0V14L4 10L0 6V0H12V6L8 10L12 14M2 5.5L6 9.5L10 5.5V2H2M6 10.5L2 14.5V18H10V14.5M8 16H4V15.2L6 13.2L8 15.2V16Z" fill="currentColor"/></svg>',
      '            <span class="b5-text fw-bold">Status</span>',
      '          </span>',
      '          <span class="b7-text rounder-1 px-2 task-status-badge" style="color:' + sColor + ';background:' + sBg + ';">' + (task.statusName || '') + '</span>',
      '        </div>',
      '        <div class="info-row">',
      '          <span class="label">',
      '            <svg class="opacity-30" width="14" height="14" viewBox="0 0 20 18" fill="none"><path d="M6 5H4.2C3.08 5 2.52 5 2.092 5.218C1.71569 5.40974 1.40974 5.71569 1.218 6.092C1 6.52 1 7.08 1 8.2V13.8C1 14.92 1 15.48 1.218 15.908C1.40974 16.2843 1.71569 16.5903 2.092 16.782C2.519 17 3.079 17 4.197 17H15.803C16.921 17 17.48 17 17.907 16.782C18.284 16.59 18.59 16.284 18.782 15.908C19 15.48 19 14.922 19 13.804V8.197C19 7.079 19 6.519 18.782 6.092C18.59 5.71554 18.2837 5.40957 17.907 5.218C17.48 5 16.92 5 15.8 5H14M6 5H14M6 5C6 3.93913 6.42143 2.92172 7.17157 2.17157C7.92172 1.42143 8.93913 1 10 1C11.0609 1 12.0783 1.42143 12.8284 2.17157C13.5786 2.92172 14 3.93913 14 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
      '            <span class="b5-text fw-bold"> Priority</span>',
      '          </span>',
      '          <span class="b5-text" style="color:' + pColor + ';font-weight:600;">' + (task.priorityName || '—') + '</span>',
      '        </div>',
      '        <div class="info-row mb-0">',
      '          <span class="label">',
      '            <svg class="opacity-30" width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M10 19C14.9706 19 19 14.9706 19 10C19 5.02944 14.9706 1 10 1C5.02944 1 1 5.02944 1 10C1 14.9706 5.02944 19 10 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 6V11H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
      '            <span class="b5-text fw-bold">Last Update</span>',
      '          </span>',
      '          <span class="b5-text">' + (task.last_updated_at || '—') + '</span>',
      '        </div>',
      '      </div>',
      '      <div class="task-card-actions d-flex justify-content-start align-items-center px-3 gap-2">',
      editBtn + infoBtn + deleteBtn + historyBtn + updateBtn,
      '      </div>',
      '    </div>',
      '  </div>',
      '</div>'
    ].join('');
  };

  t.renderFooter = function (response) {
    var currentPage = parseInt(response.page) || t.currentPage;
    var perPage = t.perPage;
    var filtered = response.filtered || 0;
    var total = response.total || 0;
    var lastPage = Math.ceil(filtered / perPage) || 1;

    var startEntry = filtered === 0 ? 0 : (currentPage - 1) * perPage + 1;
    var endEntry = Math.min(currentPage * perPage, filtered);

    var recordText =
      "Showing " +
      startEntry +
      " to " +
      endEntry +
      " of " +
      filtered +
      " entries";
    if (filtered !== total) {
      recordText += " (filtered from " + total + " total records)";
    }

    var html = '<span class="task-record-count">' + recordText + "</span>";
    html += '<div class="task-pagination">';
    html +=
      '<button type="button" class="task-page-btn task-pagination-btn" data-page="' +
      (currentPage - 1) +
      '"' +
      (currentPage == 1 ? " disabled" : "") +
      ">Previous</button>";

    for (var i = 1; i <= lastPage; i++) {
      html +=
        '<button type="button" class="task-page-btn task-pagination-btn' +
        (currentPage == i ? " active" : "") +
        '" data-page="' +
        i +
        '">' +
        i +
        "</button>";
    }

    html +=
      '<button type="button" class="task-page-btn task-pagination-btn" data-page="' +
      (currentPage + 1) +
      '"' +
      (currentPage == lastPage || lastPage == 0 ? " disabled" : "") +
      ">Next</button>";
    html += "</div>";

    t.footer.html(html);
  };

  t.load = function (page) {
    t.currentPage = page || t.currentPage || 1;
    t.body.html(
      '<div style="display:flex;justify-content:center;padding:35px 0;gap:12px;"><span style="width:13px;height:13px;border-radius:50%;background:#2f80ed;display:block;animation:dotPulse 0.6s infinite alternate ease-in-out;"></span><span style="width:13px;height:13px;border-radius:50%;background:#2f80ed;display:block;animation:dotPulse 0.6s 0.2s infinite alternate ease-in-out;"></span><span style="width:13px;height:13px;border-radius:50%;background:#2f80ed;display:block;animation:dotPulse 0.6s 0.4s infinite alternate ease-in-out;"></span></div>'
    );

    var url =
      t.config.mainfilter === "archived"
        ? t.config.url.archived_requests
        : t.config.url.requests;

    $.ajax({
      url: url,
      type: "POST",
      headers: { "X-CSRF-TOKEN": t.config.token },
      data: {
        mainfilter: t.config.mainfilter,
        search: t.config.search || "",
        filters: t.config.other_filters || {},
        page: t.currentPage,
        size: t.perPage,
        order: t.config.sort_dir,
      },
      dataType: "json",
      success: function (data) {
        if (typeof data !== "object" || typeof data.total === "undefined") {
          t.body.html(
            '<div style="text-align:center;padding:20px;">Invalid response.</div>'
          );
          return;
        }

        if (data.filtered < 1) {
          t.body.html(
            '<div style="background:#f3f4f6;padding:20px;text-align:center;border-radius:8px;">No Task details are Found</div>'
          );
          t.renderFooter(data);
          return;
        }

        var html = "";
        $.each(data.data, function (i, task) {
          html += t.renderRow(task);
        });
        t.body.html(html);
        t.body.find('[data-bs-toggle="tooltip"]').each(function () {
          bootstrap.Tooltip.getOrCreateInstance(this);
        });

        t.body.find(".task-countdown").each(function () {
          var $el = $(this);
          var finaldate = $el.data("countdown");
          $el.countdown(finaldate, function (event) {
            if (event.type === "finish") {
              $el.css("color", "#ef4444").text("Overdue");
            } else {
              $el.text(event.strftime("%D days %H:%M:%S"));
            }
          });
        });

        t.renderFooter(data);
      },
      error: function () {
        t.body.html(
          '<div style="color:#ef4444;text-align:center;padding:20px;">' +
            t.config.translations.something_went_wrong +
            "</div>"
        );
      },
    });
  };

  t.content.on("click", ".task-pagination-btn", function () {
    t.load(parseInt($(this).data("page")));
  });

  t.pageLimiter.on("change", function () {
    t.perPage = parseInt($(this).val());
    t.load(1);
  });

  t.content.on("keypress", ".task-list-search", function (e) {
    if (e.which === 13) {
      t.config.search = $(this).val();
      t.load(1);
    }
  });
  t.content.on("click", ".btn-reload", function () {
    t.config.search = t.search.val();
    t.load(1);
  });

  t.deleteTask = function (taskId, deleteUrl) {
    sweetAlertConfirmation({
      message: 'Are you sure you want to delete this task?',
      onConfirm: function () {
        $.ajax({
          url: deleteUrl + '/' + taskId,
          type: 'GET',
          success: function (data) {
            if (data.status === "success") {
              sweetAlert('center', 'success', data);
              t.load(t.currentPage);
            } else {
              sweetAlert('center', 'error', data);
            }
          },
          error: function () {
            var data = {
              'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
          }
        });
      },
    });
  }

  t.openFilter = function (e) {
    e.preventDefault();
    t.filterMdl.modal("show");
  };

  t.download = function(e) {
    e.preventDefault();
    t.cache_filter_values();
    var exportUrl = t.config.mainfilter === "archived" ? t.config.url.archivedExport : t.config.url.export;
    window.location = exportUrl + "?q=" + t.config.export_filters + "&mainfilter=" + encodeURIComponent(t.config.mainfilter);
  }

  t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
  t.content.on("click", ".btn-export-tasks", $.proxy(t.download));

  t.renderSortDropdown = function () {
    var $dropdown = t.shortItems;
    var s = t.config.sort_dir;
    $dropdown.empty();
    $.each(t.config.sort_fields, function (i, d) {
      var isActive = d.id == s.id;
      $dropdown.append(`
        <li>
          <a href="javascript:void(0)"
          class="dropdown-item ${isActive ? 'active' : ''}"
          data-id="${d.id}"
          data-bs-toggle="tooltip"
          data-bs-placement="left"
          data-bs-original-title="${d.text}">
            <span class="like-radio"></span>
            <span class="flex-grow-1">${d.text}</span>
            ${isActive
              ? `<i class="bi ${s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down'}"></i>`
              : ''
            }
          </a>
        </li>
      `);
    });
    $dropdown.find('[data-bs-toggle="tooltip"]').each(function () {
      bootstrap.Tooltip.getOrCreateInstance(this, {
        container: 'body'
      });
    });
  };

  t.sortAction.on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();

    t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;

    t.updateSortDirectionIcon();

    t.renderSortDropdown();
    t.load();
  });

  t.dropdownAction.on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    t.shortItems.toggleClass("show");
  });

  t.updateSortDirectionIcon = function () {
    $("#sortDirectionIcon").removeClass("bi-sort-up bi-sort-down").addClass(t.config.sort_dir.dir == 1 ? "bi-sort-up" : "bi-sort-down");
  };

  t.shortItems.on("click", ".dropdown-item", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const tooltip = bootstrap.Tooltip.getInstance(this);
    if (tooltip) {
      tooltip.hide();
    }
    t.config.sort_dir.id = $(this).data("id");
    t.shortItems.removeClass("show");
    t.renderSortDropdown();
    t.load();
  });

  t.shortItems.on("click", function (e) {
    e.stopPropagation();
  });

  $(document).on("click", function () {
    const tooltip = bootstrap.Tooltip.getInstance(this);
    if (tooltip) {
      tooltip.hide();
    }
    t.shortItems.removeClass("show");
  });
  t.renderSortDropdown();
  t.addTask = function (e) {
    if (e) e.preventDefault();
    t.taskModal.resetForm();
    t.mdl.modal("show");
  };

  t.content.on("click", "[data-task-action='edit']", function () {
    var taskId = $(this).data("id");   
    t.taskModal.openForEdit(taskId);
  });
  t.content.on("click", ".btn-add-task", $.proxy(t.addTask));

  $(document).on('click', '.delete-task', function () {
    let taskId = $(this).data('id');
    let deleteUrl = config.url.delete;
    t.deleteTask(taskId, deleteUrl);
  });
  t.load(1);
};