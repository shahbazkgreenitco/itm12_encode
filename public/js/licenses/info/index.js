var SeatsPhase = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#seats-tab");
  t.table = t.tab.find("#tblSeats");
  t.chkin = {};
  t.chkin.mdl = $("section.content").find("#license-checkin-mdl");
  t.chkin.mdl.btnSubmit = t.chkin.mdl.find("#btnSubmit");
  t.chkin.mdl.btnClear = t.chkin.mdl.find("#btnClear");
  t.chkin.mdl.frm = t.chkin.mdl.find("#license-checkin-mdl-frm");
  t.chkin.mdl.frmEl = {};
  t.chkin.mdl.frmEl.id = t.chkin.mdl.frm.find("#id");
  t.chkin.mdl.frmEl.seat_no = t.chkin.mdl.frm.find("#seat_no");
  t.chkin.mdl.frmEl.lic_serail = t.chkin.mdl.frm.find("#license_serial");
  t.chkin.mdl.frmEl.notes = t.chkin.mdl.frm.find("#note");

  t.edit_serial_no = {};
  t.edit_serial_no.mdl = $("section.content").find("#license-expected-checkin-date");
  t.edit_serial_no.mdl.frm = t.edit_serial_no.mdl.find("#license-expected-checkin-date-frm");
  t.edit_serial_no.mdl.frm.serial = t.edit_serial_no.mdl.frm.find("#license_serial");
  t.edit_serial_no.mdl.frm.submit = t.edit_serial_no.mdl.frm.find("#btnSubmit");
  t.edit_serial_no.mdl.frm.expected_checkin = t.edit_serial_no.mdl.frm.find("#expected_checkin");
  t.edit_serial_no.mdl.frm.expected_checkin.datepicker("destroy").datepicker({ autoclose: true, format: "dd/mm/yyyy",});
            
  t.tokenEl = $('html head meta[name="csrf-token"]');
  t.httpCall = true;
  
  t.buildActions = function (d) {
    var actions = [];
    if (jQuery.inArray("LicenseCheckin", t.config.permissions) !== -1) {
      actions.push(`
            <button class="user-list-action-btn dtActCheckIn" data-id="${d.id}" 
              data-seat_info="${d.seatcount}"
             title="${config.translations.check_in}"> ${t.getIcon("checkin")}
            </button>
        `);
    }
    actions.push(`
        <button class="user-list-action-btn open-edit-modal" 
         data-id="${d.id}" 
         action="update"
         title="${config.translations.edit_checkin_date}">
          ${t.getIcon("edit")}
        </button>
    `);

    return `
        <div class="user-list-actions d-flex justify-content-center gap-2">
            ${actions.join("")}
        </div>
    `;
  };

  t.getIcon = function (key) {
    return t.icons[key] ? t.icons[key]() : "";
  };

  t.icons = {
    checkin: function () {
      return `
            <svg viewBox="0 0 16 16" fill="none">
                <path d="M6 3L11 8L6 13"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>

                <path d="M11 8H2"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"/>
            </svg>
        `;
    },

    edit: function () {
      return `
          <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
        `;
    },
  };

  t.dTbl = t.table.DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,
    scrollX: true,
    scrollCollapse: true,
    dom: "ltrip",
    order: [[2, "desc"]],
    pageLength: 10,
    lengthChange: false,
    searchDelay: 400,
    deferRender: true,

    aoColumnDefs: [
        {
            targets: 7,
            bSortable: false,
            searchable: false,
            className: "text-center",
            render: function (data, type, row) {
                return t.buildActions(row.a);
            },
        },
        {
            targets: 6,
            render: function (d) {
                var a = [];

                if (d.notes) {
                    var note = String(d.notes);

                    if (note.length > 20) {
                        var truncated = truncateHtml(note, 20);
                        a.push(
                            `<span class="b1-text">${truncated}<a class="read-more" style="cursor:pointer" data-full-text="${escapeHtml(note)}">...</a></span>`
                        );
                    } else {
                        a.push(`<span class="b1-text">${note}</span>`);
                    }
                }

                return a.join("");
            },
        },
        {
            targets: 3,
            render: function (d) {
                var a = [];
                if (d.deviceid) {
                    a.push('<a href="' +baseURL +'/device/info/' +d.deviceid +'" target="_blank">' +d.asset_tag +'</a>');
                }
                return a.join("");
            },
        },
        {
            targets: 2,
            render: function (d) {
                var a = [];
                if (d.enduserid) {
                    a.push(
                        '<a href="' +baseURL +'/user/info/' +d.enduserid +'" target="_blank">' +d.endUserName +'</a>'
                    );
                }
                return a.join("");
            },
        },
    ],
    order: [[2, "desc"]],
    processing: true,
    serverSide: true,
    ajax: {
      url: t.config.url.licenseSeatlist,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        // d.accessory_id = t.config.accessory_id;
      },
    },
    columns: [
      {
        data: "a.seatcount",
      },
      { data: 'a.serial_no' },
      {
        data: "a",
      },
      {
        data: "a",
      },
      {
        data: "a.expected_checkin_format",
        render: function (data) {
          return `<span class="b1-text">${data ?? "-"}</span>`;
        },
      },
      { data: 'a.location' },
      {
        data: "a",
      },
       {
        data: "a",
      },
        
    ],

    fnInitComplete: function () {
      var api = this.api();
      let timer;

      $(".users-search-input")
        .off("keyup")
        .on("keyup", function () {
          clearTimeout(timer);
          let value = this.value;
          timer = setTimeout(() => {
            api.search(value).draw();
          }, 400);
        });

      $(".amg-list-searchbar__icon-btn")
        .off("click")
        .on("click", function () {
          api.search($(".users-search-input").val()).draw();
        });
      $(".users-refresh-button")
        .off("click")
        .on("click", function () {
          $(".users-search-input").val("");
          api.search("").draw();
          api.ajax.reload(null, true);
        });
    },
  });

  t.tab.on("change", ".users-page-length", function () {
    let value = parseInt($(this).val(), 10) || 10;
    t.dTbl.page.len(value).draw();
  });

  function truncateHtml(html, maxLength) {
    var div = document.createElement("div");
    div.innerHTML = html;
    var text = div.textContent || div.innerText || "";
    if (text.length <= maxLength) {
      return html;
    }
    return text.substring(0, maxLength);
  }
  
  function escapeHtml(text) {
    return text.replace(/[&<>"'`=\/]/g, function (s) {
      return entityMap[s];
    });
  }

  t.tab.on("click", ".read-more", function (e) {
    e.preventDefault();
    var fullText = $(this).data("full-text");
    $("#noteModal .modal-body").html(fullText);
    $("#noteModal").modal("show");
  });


  t.reload = function () {
    t.dTbl.ajax.reload();
  };

  t.cache_filter_values = function () {
    var v = $(".users-search-input").val();
    t.config.search = v;
    var jobj = { search: t.config.search };
    t.config.export_filters = btoa(JSON.stringify(jobj));
  };

  t.export = function (e) {
    e.preventDefault();
    t.cache_filter_values();
    window.location = t.config.url.export + "?q=" + t.config.export_filters;
  };
  t.exportpdf = function (e) {
    e.preventDefault();
    t.cache_filter_values();
    window.location = t.config.url.exportpdf + "?q=" + t.config.export_filters;
  };

  t.search = function (e) {
    var target = e.target || e.currentTarget;
    if (e.keyCode == 13 || $(this).is("span")) {
      var v = $("#tblUser_filter .plain-search").validate_str_param();
      if (v === false) {
        t.config.search = "";
        alert(config.translations.please_enter_valid_search);
        return false;
      }
      t.config.search = v;
      t.dTbl.search(v).draw();
      t.reload();
    } else if (target.tagName == "BUTTON") {
      t.reload();
    }
  };

  t.chkin.frmValidator = t.chkin.mdl.frm.validate({
    onsubmit: false,
    rules: {
      id: {
        required: true,
        digits: true,
      },
      checkinnotes: {
        remarks: true,
        clean_text_only: true,
      },
    },
  });

  t.checkinLicensce = function () {
    if (t.config.licensce_block_checkin == 1) {
      var data = {
        msg: "Sorry. Licensce checkin is not allowed.",
      };
      sweetAlert("center", "error", data);
      // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center">Sorry. License checkin is not allowed.</div>' });
      return false;
    }
    // t.chkin.resetFrm();
    t.httpPostPath = t.config.url.checkin;
    t.chkin.mdl.btnSubmit.prop("disabled", false);
    t.httpPostPath = t.config.url.checkin + '/' + $(this).attr("data-id");
    t.chkin.mdl.frmEl.seat_no.html($(this).attr("data-seat_info"));
    t.chkin.mdl.frmEl.id.val($(this).attr("data-id"));
    t.chkin.mdl.modal("show");
  }
  
  t.editExpectedCheckinDate = function(e) {

    var serial = $(this);

    if(serial.attr("action") != "update") {
        alert("Sorry. Unable to Update the Serial Number.");
        return false;
    }
    
    var id = serial.attr("data-id");
    t.serialhttpPostPath = t.config.url.add_serial + '/' + id;
    t.edit_serial_no.mdl.frm.attr('action', t.config.url.add_serial + '/' + id);
    t.edit_serial_no.mdl.frm.attr('method', 'POST');
    
    var rowData = t.dTbl.row(serial.closest('tr')).data();
    if (rowData) {
        var serialNo = rowData.a?.serial_no || rowData.serial_no || '';
        var expectedDate = rowData.a?.expected_checkin_format || rowData.expected_checkin_format || '';
        
        t.edit_serial_no.mdl.frm.serial.val(serialNo);
        t.edit_serial_no.mdl.frm.expected_checkin.val(expectedDate !== '-' ? expectedDate : '');
    } else {
        console.warn('No row data found for the clicked button');

        var serialNo = serial.attr("data-serial") || '';
        var expectedDate = serial.attr("data-expected-checkin") || '';
        
        t.edit_serial_no.mdl.frm.serial.val(serialNo);
        t.edit_serial_no.mdl.frm.expected_checkin.val(expectedDate !== '-' ? expectedDate : '');
    }
    
    t.edit_serial_no.mdl.frm.find("#id").val(id);
    t.edit_serial_no.mdl.modal("show");
  }

  t.handleSubmit = function(e) {
  
    e.preventDefault();

    if (t.chkin.frmValidator.form() == false) {
        return false;
    }
    t.chkin.mdl.btnSubmit.prop("disabled", true);
  
    if (t.httpCall != true) {
        return false;
    }
  
    t.httpCall = false;
    var formData = new FormData(t.chkin.mdl.frm[0]);

    formData.append('_token', t.tokenEl.attr('content'));
    var http = $.ajax({
        url: t.httpPostPath,
        type: "POST",
        processData: false,
        contentType: false,
        data: formData
    });
    http.done(function (data) {
        if (typeof data == "object") {
            if (data.status == "success") {
                sweetAlert('center', 'success', data);
                  // t.myapp.seatPhase.reload();
                  t.config.parent.seatsPhase.reload();
                  // t.chkin.resetFrm();
                  t.chkin.mdl.modal("hide");

            } else {
                sweetAlert('center', 'error', data);
            }
        }
    });
    http.fail(function () {
        var data = {
            'msg':"Something went wrong. Please check given details are correct",
        }
        sweetAlert('center', 'error', data);
    });
    http.always(function () {
        t.chkin.mdl.btnSubmit.prop("disabled", false);
        t.httpCall = true;
    });
  };

  t.handleSubmitCheckinDate= function(e) {
   e.preventDefault();
    
    // Validate the form
    if (t.frmSerialNoValidator && t.frmSerialNoValidator.form() == false) {
        return false;
    }
    
    // Disable submit button to prevent double submission
    t.edit_serial_no.mdl.frm.submit.prop("disabled", true);
    
    // Check if HTTP call is already in progress
    if (t.httpCall != true) {
        return false;
    }
    t.httpCall = false;

    // Prepare form data
    var frmData = new FormData(t.edit_serial_no.mdl.frm[0]);
    frmData.append('_token', t.config.token);
    
    // Make AJAX request
    var http = $.ajax({
        url: t.serialhttpPostPath,
        type: "POST",
        processData: false,
        contentType: false,
        data: frmData
    });

    http.done(function(data) {
        if (typeof data == "object") {
            if (data.status == "success") {
                sweetAlert('center', 'success', data);
                t.edit_serial_no.mdl.modal("hide");
                // Reload the DataTable
                t.reload();
                
                // t.config.parent.seatsPhase.reload();
            } else {
                sweetAlert('center', 'error', data);
            }
        }
    });
    
    http.fail(function() {
        var data = {
            'msg': "Something went wrong. Please check given details are correct",
        }
        sweetAlert('center', 'error', data);
    });
    
    http.always(function() {
        // Re-enable submit button
        t.edit_serial_no.mdl.frm.submit.prop("disabled", false);
        t.httpCall = true;
    });
  };

  t.tab.on("click", ".btn-searchbox", $.proxy(t.search));
  t.tab.on("click", "#btn_pdf", $.proxy(t.exportpdf));
  t.tab.on("click", "#btn_export", $.proxy(t.export));
  t.tab.on("click", ".btn_reload", $.proxy(t.reload));     
  t.tab.on("click", ".dtActCheckIn", $.proxy(t.checkinLicensce));
  t.tab.on("click", ".open-edit-modal", $.proxy(t.editExpectedCheckinDate));
  t.chkin.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit, t));
  t.edit_serial_no.mdl.frm.submit.on("click", $.proxy(t.handleSubmitCheckinDate, t));
};

var MyApp = function (config) {
  var t = this;
  config.parent = t;
  t.httpPostPath = "";
  t.content = $("section.content");
//t.licensePhase = new LicensePhase(config);
  t.seatsPhase = null;
  t.documentsPhase = null;
  t.purchasePhase = null;
  t.historyPhase = null;
  t.info = {};
  t.info.tab = t.content.find("#info-tab");
  t.info.tab.chkout = t.info.tab.find("#dtActCheckOut");

  t.chkout = {};
  t.chkout.mdl = t.content.find("#license-checkout-mdl");
  t.chkout.mdl.frm = t.chkout.mdl.find("#license-checkout-mdl-frm");
  t.chkout.mdl.frm.id = t.chkout.mdl.frm.find("#id");
  t.chkout.mdl.frm.lblLicenseName = t.chkout.mdl.frm.find("#lblLicenseName");
  t.chkout.mdl.frm.lblLicenseSerial = t.chkout.mdl.frm.find("#lblLicenseSerial");
  t.chkout.mdl.frm.expected_checkin = t.chkout.mdl.frm.find("#expected_checkin");
  t.chkout.mdl.frm.device_id = t.chkout.mdl.frm.find("#device_id");
  t.chkout.mdl.frm.assigned_for = t.chkout.mdl.frm.find("#assigned_for");
  t.chkout.mdl.frm.assigned_to = t.chkout.mdl.frm.find("#assigned_to");
  t.chkout.mdl.btnSubmit = t.chkout.mdl.frm.find("#btnSubmit");

  var select2Opts = { width: "100%" };

  t.chkout.mdl.frm.expected_checkin.datepicker("destroy").datepicker({ autoclose: true, format: "dd/mm/yyyy",});

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

  t.checkoutLicense = function (e) {
    e.preventDefault();
    //  t.chkout.resetFrm();

        var licenseId = t.info.tab.chkout.attr("data-id");
        var license_name = t.info.tab.chkout.attr("data-accessory-name");
        var Serial = t.info.tab.chkout.attr("data-serial_num");

        t.httpPostPath = config.url.checkout + '/' + licenseId;
        t.chkout.mdl.frm.lblLicenseName.text(license_name);
        t.chkout.mdl.frm.lblLicenseSerial.text(Serial);
        t.chkout.mdl.frm.id.text(licenseId);
        t.chkout.mdl.frm.device_id.empty();
        t.chkout.mdl.frm.assigned_for.val("1").trigger("change");
        t.chkout.mdl.modal("show");
  };

  t.chkout.switchCheckTarget = function () {
      if (t.chkout.mdl.frm.assigned_for.val() == "1") {
      t.chkout.mdl.frm.device_id.closest(".cover").show();
      t.chkout.mdl.frm.assigned_to.closest(".cover").hide();
      t.chkout.mdl.frm.device_id.rules("add", { required: true });
      t.chkout.mdl.frm.assigned_to.rules("remove", "required");
      } else {
      t.chkout.mdl.frm.device_id.closest(".cover").hide();
      t.chkout.mdl.frm.assigned_to.closest(".cover").show();
      t.chkout.mdl.frm.device_id.rules("remove", "required");
      t.chkout.mdl.frm.assigned_to.rules("add", { required: true });
      }
  };

  t.chkout.resetFrm = function () {
      t.chkout.mdl.frm.trigger("reset");
      t.chkout.frmValidator.resetForm();
      t.chkout.mdl.frm.assigned_to.empty().trigger("change");
      t.chkout.mdl.frm.assigned_for.val("").trigger("change");
      t.chkout.mdl.frm.device_id.empty().trigger("change");
      t.chkout.mdl.frm.expected_checkin.val("");
      t.chkout.mdl.frm.lblLicenseName.text("");
      t.chkout.mdl.frm.lblLicenseSerial.text("");
  };

  t.chkout.mdl.frm.assigned_to.select2(
      $.extend({}, select2Opts, {
      width: "100%",
      dropdownParent: t.chkout.mdl.frm.assigned_to.parent(),
      ajax: {
          url: config.getUserByAjax,
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
      placeholder: config.translations.select_the_user,
      
      templateResult: function (s) {
          if (typeof s.loading != "undefined" && s.loading) {
              return $("<div>" + s.text + "</div>");
          }

          var email = s.email == null ? "" : s.email;
          var a = "";
          
          // License-specific template
          a += "<div class='row'>";
          a += "<div class='col-9'>";
          
          // Show user display name
          if (s.displayName != null && s.displayName != "") { 
              a += "<div class='so-t'><i class=\"bi bi-person\"></i>" + s.displayName;
          } else {
              a += "<div class='so-t'><i class=\"bi bi-person\"></i>" + s.first_name + " " + s.last_name;
          }
          a += "<span class='active-user'></span>";
          a += "</div>";
          
          // Show email if exists
          if (s.email != null && s.email != "") {
              a += "<div class='so-t'><i class=\"bi bi-envelope\"></i>" + s.email + "</div>";
          }
          
          // Show employee number if exists
          if (s.employee_num != null && s.employee_num != "") {
              a += "<div class='so-t'><i class=\"bi bi-credit-card\"></i>" + s.employee_num + "</div>";
          }
          
          // Show license-related info if applicable
          if (s.license_count != null && s.license_count != "") {
              a += "<div class='so-t'><i class=\"fa fa-copyright\"></i> Licenses: " + s.license_count + "</div>";
          }
          
          a += "</div>";
          a += "<div class='col-3'>";
          a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
          a += "</div>";
          a += "</div>";
          
          return $("<div>" + a + "</div>");
      },
      templateSelection: function (data, container) {
          if (container) {
              $(container).attr("title", data.text);
          }
          // Increase to 50 characters for license module
          return data.text.length > 50
              ? data.text.substring(0, 50) + "..."
              : data.text;
      },

      }),
  );

  t.chkout.mdl.frm.device_id.select2(
    $.extend({}, select2Opts, {
    dropdownParent: t.chkout.mdl.frm.device_id.parent(),
    ajax: {
        url: config.getDeviceForCheckoutDropDown,
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
    placeholder: config.translations.select_the_Device,
    // templateResult: function (s) {
    //     if (typeof s.loading != "undefined" && s.loading) {
    //     return $("<div>" + s.text + "</div>");
    //     }
    //     a =
    //     "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
    //     s.asset_tag +
    //     "</div>";
    //     if (s.asset_name != null) {
    //     a +=
    //         "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
    //         s.asset_name +
    //         "</div>";
    //     }
    //     a +=
    //     "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
    //     s.name +
    //     " " +
    //     s.modelno +
    //     "</div>";
    //     a +=
    //     "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
    //     s.serial +
    //     "</div>";
    //     return $("<div>" + a + "</div>");
    // },
    // templateSelection: function (data, container) {
    //     if (container) {
    //     $(container).attr("title", data.text);
    //     }
    //     return data.text.length > 50
    //     ? data.text.substring(0, 55) + "..."
    //     : data.text;
    // },
    }),
  );
  
  t.chkout.mdl.frm.assigned_for.select2(
    $.extend({}, select2Opts, {
    dropdownParent: t.chkout.mdl.frm.assigned_for.parent(),
    data: config.assignedForOptions,
    }),
  );

  t.chkout.mdl.frm.assigned_for.on(
      "change",
      $.proxy(t.chkout.switchCheckTarget),
  );

  t.handleCheckoutSubmit = function (e) {
  
      e.preventDefault();
      t.chkout.mdl.btnSubmit.prop("disabled", true);
      if (t.chkout.frmValidator.form() == false) {
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
          t.chkout.mdl.modal("hide");
          t.dTable.ajax.reload();
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
      t.chkout.mdl.btnSubmit.prop("disabled", false);
      });
  };
  
  $('a[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
    var target = $(e.target).attr("href");
    if (target === "#seats-tab" && !t.seatsPhase) {
  t.seatsPhase = new SeatsPhase(config);
    }
    if (target === "#documents-tab" && !t.documentPhase) {
  t.documentsPhase = new DocumentsPhase(config);
    }
    if (target === "#purchase-tab" && !t.PurchasePhase) {
  t.purchasePhase = new PurchasePhase(config);
    }
    if (target === "#history-tab" && !t.historyPhase) {
  t.historyPhase = new HistoryPhase(config);
    }
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
  
  $(document).on("click", "#dtActCheckOut", $.proxy(t.checkoutLicense, t));
  $(document).on("click", "#btnSubmit", $.proxy(t.handleCheckoutSubmit, t));

};





