$.validator.addMethod(
  "str_name",
  function (value, element) {
    return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
  },
  "Only letters allowed",
);

$.validator.addMethod(
  "clean_text_only",
  function (value, element) {
    return this.optional(element) || /^[0-9a-zA-Z .,-]+$/.test(value);
  },
  "Invalid characters",
);

var InfoPhase = function (config) {
  var t = this;
  t.config = config;
  t.sectionHeader = $("section.content-header");
  t.content = $("section.content");
  t.tabBar = t.content.find(".tab-bar");
  t.infoTab = $("section.content").find("#info-tab");

  t.mdl = $("section.content").find("#user-mdl");
  t.mdl.title = t.mdl.find(".modal-title");
  t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
  t.mdl.btnClear = t.mdl.find("#btnClear");

  t.mdl.frm = t.mdl.find("#user-mdl-frm");
  t.mdl.frmEl = {};
  t.mdl.frmEl.company = t.mdl.frm.find("#company_id");
  t.mdl.frmEl.department = t.mdl.frm.find("#department_id");
  t.mdl.frmEl.manager = t.mdl.frm.find("#manager_id");
  t.mdl.frmEl.location = t.mdl.frm.find("#location_id");
  t.mdl.frmEl.doj = t.mdl.frm.find("#doj");
  t.mdl.frmEl.last_working_date = t.mdl.frm.find("#last_working_date");
  t.mdl.frmEl.activeStatus = t.mdl.frm.find("#active_status");
  t.mdl.frmEl.groups = t.mdl.frm.find("#groups");
  t.mdl.frmEl.job_type = t.mdl.frm.find("#job_type");
  t.mdl.frmEl.ex_user_company = t.mdl.frm.find("#ex_user_company");
  t.mdl.frmEl.password = t.mdl.frm.find("input[name=password]");
  t.mdl.lblPassword = t.mdl.frm.find("label[for=password]");
  t.mdl.frmEl.doj.on("changeDate", function (e) {
    t.mdl.frmEl.last_working_date.datepicker("setStartDate", e.date);
    try {
      if (t.mdl.frmEl.last_working_date.datepicker("getDate") < e.date) {
        t.mdl.frmEl.last_working_date.datepicker("update", doj);
      }
    } catch (err) {
      t.mdl.frmEl.last_working_date.datepicker("update", "");
    }
  });

  t.httpCall = true;
  t.httpPostPath = "";
  t.forAction = "";

  t.refreshInfoTab = function () {
    t.infoTab.load(t.config.url.purchase_info_tab + "/" + t.config.purchase_id);
  };
  t.refreshInfoTab();
};

var PurchaseMedia = function (config) {
  var t = this;
  t.config = config;
  t.tab = $("section.content").find("#document-tab");
  t.table = t.tab.find("#mytabledocument");
  t.attach_download = t.tab.find("#attach_download");
  t.btn = t.tab.find("#add");
  t.dTbl = t.table.DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,

    pageLength: 10,
    lengthChange: false,

    // dom: "t<'row mt-2'<'col-md-6'i><'col-md-6'p>>",
    dom: "lrtip",
    ajax: {
      url: t.config.url.attachment_list,
      type: "POST",
      data: function (d) {
        d._token = t.config.token;
        d.reference_id = t.config.purchase_id;
      },
    },

    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
        className: "text-center",
        width: "50px",
      },

      { data: "a.original_file_name" },

      { data: "a.updated_at_format", className: "text-center" },

      {
        data: "a",
        orderable: false,
        searchable: false,
        className: "text-center",
        render: function (d) {
          let actions = [];

          //DELETE
          if (
            jQuery.inArray("PurchaseDocumentDelete", t.config.permissions) !==
            -1
          ) {
            actions.push(`
        <button class="user-list-action-btn get-confirm-att-del" 
          data-id="${d.id}" title="Delete">
          <svg viewBox="0 0 15 17" fill="none" width="16">
            <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375Z" fill="currentColor"/>
          </svg>
        </button>
      `);
          }

          // DOWNLOAD
          if (
            jQuery.inArray("PurchaseDocumentDownload", t.config.permissions) !==
            -1
          ) {
            actions.push(`
        <button class="user-list-action-btn download-att" 
          data-id="${d.id}" title="Download">
          <svg viewBox="0 0 16 16" fill="none" width="16">
            <path d="M8 1V10M8 10L5 7M8 10L11 7M2 14H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      `);
          }

          //VIEW
          const ext = (d.file_name || "").split(".").pop().toLowerCase();

          if (
            jQuery.inArray("PurchaseDocumentView", t.config.permissions) !==
              -1 &&
            ["jpg", "jpeg", "png", "pdf"].includes(ext)
          ) {
            actions.push(`
        <button class="user-list-action-btn tri-view" 
          data-id="${d.id}" title="View">
          <svg viewBox="0 0 16 16" fill="none" width="16">
            <path d="M8 3C4 3 1.73 7.11 1.5 8C1.73 8.89 4 13 8 13C12 13 14.27 8.89 14.5 8C14.27 7.11 12 3 8 3Z" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </button>
      `);
          }

          // EMPTY CASE
          if (!actions.length) {
            return `<span class="text-muted">-</span>`;
          }

          return `
      <div class="user-list-actions d-flex justify-content-center gap-2">
        ${actions.join("")}
      </div>
    `;
        },
      },
    ],

    order: [[2, "desc"]],
  });

  var api = t.dTbl;

  // SEARCH
  t.tab
    .find("#documentSearch")
    .off("keyup")
    .on("keyup", function (e) {
      if (e.keyCode == 13 || this.value.length == 0) {
        api.search(this.value).draw();
      }
    });
  // SEARCH BUTTON
  t.tab
    .find("#documentRefresh")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      api.search("").draw();
    });

  // PAGE LENGTH
  $("#showSelect").on("change", function () {
    var value = parseInt($(this).val(), 10);
    api.page.len(value).draw();
  });

  /* attachment */
  t.attachment = t.tab.find("#attachments");
  t.attachment_dropper_cover = t.tab.find("#attachment-dropper-cover");
  t.attachment_dropper = t.tab.find("#attachment-dropper");

  // t.deleteAttachment = function (e) {
  //   e.preventDefault();
  //   var attachment_id = $(this).attr("data-id");
  //   var data = {
  //     msg: config.translations.something_went_wrong,
  //   };
  //   var send_data = {
  //     token: t.config.token,
  //     id: attachment_id,
  //   };
  //   sweetAlertPost(
  //     "Are you sure to delete this attachment?",
  //     "warning",
  //     t.config.url.attachment_remove,
  //     t.dTbl.ajax,
  //     data,
  //     send_data,
  //   );
  // };

  t.deleteAttachment = function (e) {
  e.preventDefault();

  var attachment_id = $(this).attr("data-id");

  Swal.fire({
    title: "Are you sure?",
    text: "Delete this attachment?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {

      $.ajax({
        url: t.config.url.attachment_remove,
        type: "POST", // ⚠️ change to GET if backend needs
        data: {
          _token: t.config.token,
          id: attachment_id,
        },

        beforeSend: function () {
          Swal.fire({
            title: "Deleting...",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
          });
        },

        success: function (res) {
          Swal.close();

          if (res.status === "success") {

            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: res.msg || "Attachment deleted successfully",
              timer: 1500,
              showConfirmButton: false,
            });

            // ✅ reload datatable
            t.dTbl.ajax.reload(null, false);

          } else {
            Swal.fire("Error", res.msg, "error");
          }
        },

        error: function () {
          Swal.fire("Error", "Something went wrong", "error");
        },
      });

    }
  });
};

  t.attachmentView = function (e) {
    e.preventDefault();
    window.open(
      t.config.url.attachment_view + "/" + $(this).attr("data-id"),
      "_blank",
    );
  };

  t.attachment_dropper_cover.filedrop({
    fallback_id: "attachment",
    fallback_dropzoneClick: true,
    url: t.config.url.attachment_add,
    paramname: "attachment",
    data: {
      _token: t.config.token,
      reference_id: function () {
        return t.config.purchase_id;
      },
    },
    maxfiles: 5,
    maxfilesize: 20,
    allowedfileextensions: [".jpg", ".jpeg", ".png", ".pdf", ".mp4", ".xlsx"],
    uploadFinished: function (i, file, response, time) {
      if (response.status === "success") {
        t.attachment
          .find("#attach" + i + " .progress")
          .fadeOut("slow")
          .closest(".attach")
          .remove();
        t.dTbl.ajax.reload();
      } else {
        t.attachment
          .find("#attach" + i + " .name")
          .text(file.name + " upload failed");
        t.attachment
          .find("#attach" + i + " .upload_length")
          .addClass("progress-bar-danger");
      }
    },
    progressUpdated: function (i, file, progress) {
      t.attachment
        .find("#attach" + i + " .upload_length")
        .css("width", progress + "%");
    },
    beforeSend: function (file, i, done) {
      if (t.attachment.find("#attach" + i).length) {
        t.attachment
          .find("#attach" + i)
          .attr("id", "attach" + Math.random().toString().substring(2, 15));
      }
      t.attachment.append(
        '<div id="attach' +
          i +
          '" class="attach pad-top"><div class="bord-btm clearfix">' +
          '<p class="name pull-left">' +
          file.name +
          '</p><span style="cursor:pointer" class="remove-attach pull-right"><i class="fa fa-spinner"></i>' +
          t.config.translations.uploading +
          '</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div>',
      );
      done();
    },
    dragOver: function () {
      t.attachment_dropper.show();
    },
    drop: function () {
      t.attachment_dropper.hide();
    },
    afterAll: function () {
      $("#attachment").val("");
    },
  });
  t.tab.on("click", ".tri-view", $.proxy(t.attachmentView));
  t.tab.on("click", ".get-confirm-att-del", $.proxy(t.deleteAttachment));
  t.tab.on("click", ".download-att", function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");
    window.location = t.config.url.attachment_download + "/" + id;
  });

  t.dTbl.ajax.reload();
};

var PurchaseItem = function (config) {
  var t = this;
  t.config = config;
  t.content = $("section.content");
  t.table = t.content.find("#mytableitem");
  // t.mdl = t.content.find("#purchases_item_modal");
  t.mdl = $("#purchases_item_modal");
  t.mdltitle = t.mdl.find(".modal-title");
  t.frm = t.mdl.find("#itemForm");

  t.frmEl = {};
  t.frmEl.name = t.frm.find("#name");
  t.frmEl.qty = t.frm.find("#qty");
  t.frmEl.units = t.frm.find("#units");
  t.frmEl.price = t.frm.find("#price");
  t.frmEl.description = t.frm.find("#description");
  // t.resetFrm = {};

  t.btn = {};
  t.btn.submit = t.frm.find("#btnSubmit");
  t.btn.update = t.frm.find("#btnupdate");
  t.btn.clear = t.frm.find("#btnClr");
  t.httpCall = true;
  t.httpPostPath = "";

  t.dTbl = t.table.DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    autoWidth: false,

    pageLength: 10,
    lengthChange: false,

    dom: "lrtip",
    ajax: {
      url: config.url.purchaseItemList,
      type: "post",
      data: function (d) {
        d._token = t.config.token;
        d.purchase_id = t.config.purchase_id;
      },
    },

    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        },
        className: "text-center",
        width: "50px",
      },
      { data: "a.name" },
      { data: "a.qty", className: "text-center" },
      { data: "a.units", className: "text-center" },
      { data: "a.price", className: "text-end" },
      { data: "a.total_price", className: "text-end fw-bold" },
      {
        data: "a.id",
        orderable: false,
        searchable: false,
        className: "text-center",
        render: function (data) {
          return `
      <div class="user-list-actions d-flex justify-content-center gap-2">

        <!-- EDIT -->
        <button class="user-list-action-btn open-edit-modal" 
          data-id="${data}" title="Edit">
         <svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>
        </button>

        <!-- DELETE -->
        <button class="user-list-action-btn open-delete" 
          data-id="${data}" title="Delete">
          <svg viewBox="0 0 15 17" fill="none" width="16">
            <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375Z" fill="currentColor"/>
          </svg>
        </button>

      </div>
    `;
        },
      },
    ],

    order: [[5, "desc"]],
  });

  // ================= SEARCH / REFRESH / LENGTH =================
  var api = t.dTbl;

  // SEARCH
  t.content
    .find("#tableSearch")
    .off("keyup")
    .on("keyup", function (e) {
      if (e.keyCode === 13 || this.value.length === 0) {
        api.search(this.value).draw();
      }
    });

  // REFRESH
  t.content
    .find("#itemRefresh")
    .off("click")
    .on("click", function (e) {
      e.preventDefault();
      t.content.find("#tableSearch").val("");
      api.search("").draw();
    });

  // PAGE LENGTH
  t.content
    .find("#itemLength")
    .off("change")
    .on("change", function () {
      var val = parseInt($(this).val(), 10);
      api.page.len(val).draw();
    });

  // var select2Opts = { width: "100%" };
  // $(document).ready(function () {
  //   t.frmEl.units.select2(
  //     $.extend({}, select2Opts, {
  //       dropdownParent: t.frmEl.units.parent(),
  //       ajax: {
  //         url: t.config.getUnitsByAjax,
  //         dataType: "json",
  //         data: function (p) {
  //           return {
  //             search: p.term,
  //             page: p.page || 1,
  //           };
  //         },
  //         delay: 300,
  //       },
  //       allowClear: true,
  //       placeholder: config.translations.select_unit,
  //     }),
  //   );
  //   t.frmEl.units.trigger("change");
  // });

  t.frmEl.units.select2({
    width: "90%",
    dropdownParent: t.mdl,
    placeholder: config.translations.select_unit,
    allowClear: true,

    ajax: {
      url: t.config.getUnitsByAjax,
      dataType: "json",
      delay: 300,
      data: function (p) {
        return {
          search: p.term,
          page: p.page || 1,
        };
      },
    },
  });

  // t.add = function (e) {
  //   t.resetFrm();
  //   e.preventDefault();
  //   t.mdltitle.text(config.translations.add_item);
  //   t.mdl.modal("show");
  //   t.httpPostPath = t.config.url.save;
  // };

  // t.add = function (e) {
  //   e.preventDefault();
  //   e.stopPropagation(); // IMPORTANT

  //   t.resetFrm();

  //   t.mdltitle.text(config.translations.add_item);

  //   t.mdl.modal("show");

  //   t.httpPostPath = t.config.url.save;
  // };

  t.add = function (e) {
    e.preventDefault();

    t.resetFrm();

    t.mdltitle.text(config.translations.add_item);

    new bootstrap.Modal(t.mdl[0]).show();

    t.httpPostPath = t.config.url.save;
  };

  t.editItem = function (e) {
    e.preventDefault();
    var accId = $(this).attr("data-id");
    t.httpPostPath = t.config.url.editPurchaseItem + "/" + accId;
    var http = $.get(t.config.url.getPurchaseItem + "/" + accId);
    http.done(function (result) {
      if (typeof result == "object") {
        if (result.status == "success") {
          t.mdl.modal("show");
          t.mdltitle.text(config.translations.edit_itme);
          t.btn.submit.text(t.config.translations.save);
          t.loadForm(result.data, "edit");
        } else {
          sweetAlert("center", "error", data);
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        }
      }
    });
    http.fail(function () {
      // alert("Something went wrong. Please check given details are correct");
      var data = {
        msg: config.translations.something_went_wrong,
      };
      sweetAlert("center", "error", data);
    });
    http.always(function () {
      t.httpCall = true;
    });
  };
  t.deleteItem = function (e) {
    e.preventDefault();

    var id = $(this).attr("data-id");
    var url = t.config.url.delete + "/" + id;

    Swal.fire({
      title: config.translations.are_you_delete_item || "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Yes, delete it!",
      cancelButtonText: "Cancel",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: url,
          type: "GET",
          data: {
            _token: t.config.token,
          },

          beforeSend: function () {
            Swal.fire({
              title: "Deleting...",
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            });
          },

          success: function (res) {
            Swal.close();

            if (res.status === "success") {
              Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: res.msg || "Item deleted successfully",
                timer: 1500,
                showConfirmButton: false,
              });

              t.dTbl.ajax.reload(null, false);
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: res.msg || "Delete failed",
              });
            }
          },

          error: function () {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Something went wrong",
            });
          },
        });
      }
    });
  };

  t.loadForm = function (obj) {
    t.resetFrm();
    t.frmEl.name.val(obj.data.name);
    t.frmEl.qty.val(obj.data.qty);
    t.frmEl.price.val(obj.data.price);
    // if (obj.data.units > 0) {
    //     t.frmEl.units.val(obj.data.units).trigger("change");
    // }
    if (typeof obj.data == "object" && typeof obj.data.dev != null) {
      t.frmEl.units
        .append(new Option(obj.data.dev.text, obj.data.dev.id, true, true))
        .trigger("change");
    }
    // t.frmEl.description.summernote("code", obj.data.description);
  };
  t.resetFrm = function () {
    t.frmEl.name.val("");
    t.frmEl.qty.val("");
    t.frmEl.price.val("");
    // t.frmEl.description.val("").summernote("code", "");
    t.frmEl.units.val("").trigger("change");
    // t.frmValidator.resetForm();
  };

  t.frmValidator = t.frm.validate({
    onsubmit: false,
    rules: {
      name: {
        required: true,
        str_name: true,
        clean_text_only: true,
      },
      price: {
        required: true,
        clean_text_only: true,
        // digits:true
      },
      qty: {
        required: true,
        clean_text_only: true,
      },
      units: {
        required: true,
      },
    },
    errorPlacement: function (error, element) {
      error.appendTo(element.parent().parent());
    },
  });

  $(".form-group select, .form-group .datepicker").on("change", function () {
    $(this).valid();
  });

  t.reload = function (e) {
    e.preventDefault();
    t.dTbl.ajax.reload();
  };

  t.handleSubmit = function (e) {
    e.preventDefault();

    if (t.frmValidator.form() == false) {
      return false;
    }
    if (t.httpCall != true) {
      return false;
    }
    t.httpCall = false;
    var formData = new FormData(t.frm[0]);
    formData.append("_token", t.config.token);
    formData.append("purchase_id", t.config.purchase_id);
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
          // sweetAlert("center", "success", data);
          Swal.fire({
            icon: "success",
            title: "Success!",
            text: data.msg || "Supplier added successfully",
            confirmButtonText: "OK",
          });
          // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
          t.mdl.modal("hide");
          t.dTbl.ajax.reload();
        } else {
          // sweetAlert("center", "error", data);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: data.msg || "Add Supplier failed",
            confirmButtonText: "OK",
          }); // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
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
  var select2Opts = { width: "100%" };
  // t.frmEl.units.select2({width: '100%', allowclear : true});
  //   t.frmEl.description.summernote({
  //     toolbar: [
  //       ["color", ["color"]],
  //       ["style", ["bold", "italic", "underline", "clear"]],
  //       ["para", ["ul", "ol"]],
  //     ],
  //     minHeight: 200,
  //     focus: true,
  //   });
  // t.content.on("click", ".open-item-modal", $.proxy(t.add));
  $(document).off("click", ".open-item-modal");
  $(document).on("click", ".open-item-modal", function (e) {
    t.add(e);
  });
  t.content.on("click", ".open-edit-modal", $.proxy(t.editItem));
  t.content.on("click", ".open-delete", $.proxy(t.deleteItem));
  t.btn.submit.on("click", $.proxy(t.handleSubmit));
};

var MyApp = function (config) {
  var t = this;
  t.content = $("#content-container");
  // t.devicePhase = new DevicePhase(config);
  // t.licensePhase = new LicensePhase(config);
  // t.accessoryPhase = new AccessoryPhase(config);
  // t.consumablePhase = new ConsumablePhase(config);
  // t.componentphase = new ComponentPhase(config);
  // t.documentPhase = new DocumentPhase(config);
  // t.documentUploadPhase = new DocumentUploadPhase($.extend({}, config, { tbl: t.documentPhase }));
  // t.historyPhase = new HistoryPhase(config);
  t.purchasemedia = new PurchaseMedia(config);
  t.infoPhase = new InfoPhase(config);
  t.purchaseitem = new PurchaseItem(config);
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
};
