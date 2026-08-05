var UserPhase = function(config, openpop) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find("#checkouts-tab");
    t.table = t.tab.find("#checkoutsTable");

    t.chkin = {};
    t.chkin.mdl = $("section.content").find("#consumable-checkin-mdl");
    t.chkin.mdl.btnSubmit = t.chkin.mdl.find('#btnSubmit');
    t.chkin.mdl.btnClear = t.chkin.mdl.find('#btnClear');
    t.chkin.mdl.frm = t.chkin.mdl.find("#consumable-checkin-mdl-frm");
    t.chkin.mdl.frmEl = {};
    t.chkin.mdl.frmEl.id = t.chkin.mdl.frm.find("#id");
    t.chkin.mdl.frmEl.scrap_qty = t.chkin.mdl.frm.find("#scrap_qty");
    t.chkin.mdl.lblUserName = t.chkin.mdl.find("#lblUserName");
    t.httpCall = true;

    t.show_entries = $("section.content").find('#showSelectCheckouts');

    dTbl = t.table.DataTable({
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        order: [
            [2, 'desc']
        ],
        processing: true,
        serverSide: true,
        // deferLoading: 0,
        ajax: {
            url: config.url.consumableUsers,
            type: "post",
            data: function(d) {
                d._token = config.token;
            }
        },
        columns: [
             {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `${id}`;
                },
            },
            { data: 'a.target_type' },
            { data: 'a.target_name' },
            { data: 'a.created_at' },
            { data: 'a.actioner_name' },
            { data: 'a.ticket_id' },
            {
                data: "a",
                className: "amg-col-actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let buttonHtml = `
                    <button class="btn dtActbtn dtActRev p-0" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-title="Check in Consumable" action="revoke" data-id="${data.id}" data-target="${data.target_name}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z" fill="#000000"></path>
                        </svg>
                    </button>`;
                    return buttonHtml;
                }
            }
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $("#checkoutsSearch").on("keyup.DT", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
            $('.amg-list-searchbar__icon-checkouts').click(function (e) {
                e.preventDefault();
                var v = $("#checkoutsSearch").validate_str_param();
                if (v === false) {
                    alert(config.translations.please_enter_valid_search);
                    return false;
                }
                api.search(v).draw();
            });
        }
    });

    t.cache_filter_values = function() {
        var v = $("#checkoutsSearch").validate_str_param();
        t.config.search = v;
        var jobj = { "search": t.config.search};
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };
    t.reload = function() {
        dTbl.ajax.reload();
    }; 

    t.export = function(e) {
		e.preventDefault();
        t.cache_filter_values();
		window.location = t.config.url.export + "?q=" + t.config.export_filters;
    };

    t.exportpdf = function(e) {
		e.preventDefault();
        t.cache_filter_values();
		window.location = t.config.url.export_pdf + "?q=" + t.config.export_filters;
    };

    $(document).on('click', '.dtActRev', function(e) {
        e.preventDefault();
        t.chkin.resetFrm();
        t.httpPostPath = t.config.url.revoke;
        t.chkin.mdl.lblUserName.html($(this).attr("data-target"));
        t.chkin.mdl.frmEl.id.val($(this).attr("data-id"));
        t.chkin.mdl.frmEl.scrap_qty.val();
        t.chkin.mdl.modal("show");
    });

    t.chkin.resetFrm = function() {
        t.chkin.mdl.frm.trigger("reset");
        t.chkin.frmValidator.resetForm();
        t.chkin.mdl.lblUserName.text("");
    };

    t.chkin.frmValidator = t.chkin.mdl.frm.validate({
        onsubmit: false,
        rules: {
            note: {
                remarks: true,
                clean_text_only : true,
                required: function () {
                    return config.client === "knightfrank";
                }
            }
        }
    });

    t.handleCheckinSubmit = function(e) {
        e.preventDefault();

        if (t.chkin.frmValidator.form() == false) {
            return false;
        }
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.chkin.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                t.chkin.mdl.btnSubmit.prop('disabled', true).text('Please wait...');
            },
            complete: function() {
                t.chkin.mdl.btnSubmit.prop('disabled', false).text('Save');
            },
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.chkin.mdl.modal("hide");
                    if (typeof t.config.directChkin != "undefined" && t.config.directChkin) {
                        window.location.href = t.config.url.infourl+'/'+ t.config.consumable_id;
                    } else {
                        t.reload();
                    }
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg':'Something went wrong. Please check given details are correct',
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        dTbl.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.tab.on("click", "#btn_pdf", $.proxy(t.exportpdf));
    t.tab.on("click", "#btn_export", $.proxy(t.export));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.chkin.mdl.btnSubmit.on("click", $.proxy(t.handleCheckinSubmit));
    if (typeof t.config.directChkin != "undefined" && t.config.directChkin) {
        t.chkin.resetFrm();
        t.httpPostPath = t.config.url.revoke;
        t.chkin.mdl.lblUserName.html(t.config.directChkin.checkoutTo);
        t.chkin.mdl.frmEl.id.val(t.config.directChkin.id);
        t.chkin.mdl.modal("show");
    }
}

var DocumentUploadPhase = function(config) {
    var t = this;
    t.config = config;
    t.httpCall = true;
    t.httpPostPath = "";
    t.mdl = $("section.content").find("#document-mdl");
    t.mdl.title = t.mdl.find('.modal-title');
    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');
    t.mdl.frm = t.mdl.find("#document-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.token = t.mdl.frm.find("input[name='_token']");
    t.mdl.frmEl.note = t.mdl.frm.find("#note");
    t.mdl.frmEl.asset_id = t.mdl.frm.find("input[name='asset_id']");
    t.mdl.frmEl.asset_type = t.mdl.frm.find("input[name='asset_type']");

    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
    };

    t.addDocument = function(e) {
        e.preventDefault();
        t.frmValidator.resetForm();
        t.resetFrm();
        t.httpPostPath = t.config.url.document_upload;
        t.mdl.title.html(config.translations.New_Document_Upload);
        t.mdl.btnSubmit.text("Save");
        t.mdl.frmEl.token.val(t.config.token);
        t.mdl.frmEl.asset_id.val(t.config.consumable_id);
        t.mdl.frmEl.asset_type.val("consumable");
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
            data: formData,
            beforeSend: function() {
                t.mdl.btnSubmit.prop('disabled', true).text('Please wait...');
            },
            complete: function() {
                t.mdl.btnSubmit.prop('disabled', false).text('Save');
            },
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.mdl.modal("hide");
                    if (typeof t.config.tbl !== "undefined") {
                        t.config.tbl.dTbl.ajax.reload();
                    }
                } else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function() {
            var data ={
                'msg':config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
            // alert(config.translations.something_went_wrong);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        errorClass: 'error amg-form-invalid',
        rules: {
            note: {
                clean_text_only: true
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
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        },
        highlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').addClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.addClass("amg-form-invalid");
            }
        },
        unhighlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').removeClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.removeClass("amg-form-invalid");
            }
        }
    });

    $('#document').on('change', function () {
        $(this).valid();
    });

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    $("section.content").on("click", "#btn_upload_document", $.proxy(t.addDocument));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};

var DocumentPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find("#documents-tab");
    t.table = t.tab.find("#tblDocument");
    t.show_entries = $("section.content").find('#showSelectDocuments');

    t.tblHelpers = {
        actions: function() {
            return function(d) {
                var a = [];
                var validExtensions = ["pdf", "jpg", "jpeg", "png", "gif"];
                var fileExtension = d.org_name.split('.').pop().toLowerCase();
                if(jQuery.inArray("ConsumableDocumentsDelete", t.config.permissions) !== -1) {
                    a.push("<button class='btn dtActbtn dtActDel' data-placement='right' data-toggle='tooltip' data-original-title= '" + config.translations.Delete_Document + "' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                }
                if (validExtensions.includes(fileExtension)) {
                    a.push("<button class='btn dtActbtn tri-view' data-toggle='tooltip' data-placement='right' data-original-title='View' data-id=\"" + d.id + "\" data-type='consumable'><i class=\"fa fa-eye\"></i></button>");
                }
                // a.push("<button class='btn dtActbtn tri-view' data-toggle='tooltip' data-placement='right' data-original-title='View' data-id=\"" + d.id + "\" data-type='consumable'><i class=\"fa fa-eye\"></i></button>");
                if(d.status == 0) {
                    a.push("<a href=\"" + t.config.url.document_download + "/" + d.file_name + "\" class='btn dtActbtn' download data-placement='right' data-toggle='tooltip' data-original-title='"+config.translations.Download_Document+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                } else {
                    a.push("<a href=\"" + t.config.url.purchase_document_download + "/" + d.file_name + "\" class='btn dtActbtn' download data-placement='right' data-toggle='tooltip' data-original-title='"+config.translations.Download_Document+"' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                }

                if(jQuery.inArray("ConsumableEdit", t.config.permissions) == -1) {
                    t.dTbl.column(0).visible(false);
                }
                return a.join(' ');
            };
        },
    };

    t.deleteDocument = function(e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.document_delete;

        var data = {
            'msg':config.translations.something_went_wrong,
        };
        var send_data = {
            "asset_id": t.config.consumable_id, 
            "asset_type": "consumable",
            "id": docId,
            "_token": t.config.token 
        }
        sweetAlertPost('You could not recover it after delete. Are you sure to delete the document permanently?','warning',  t.httpPostPath, t.dTbl.ajax, data, send_data);
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        order: [
            [2, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.documents,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.device_id = t.config.consumable_id;
                d.asset_type = "consumable";
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            { data: 'a.org_name' },
            { data: 'a.created_at_format' },
            { data: 'a.note',
                render: function(data, type, row) {
                    if (data && data.length > 20) {
                        return `<span>${data.substring(0, 20)}</span> <a href="#" class="read-more" style="cursor: pointer; color: #00a1ff;" data-full-note="${data}">...Read More</a>`;
                    } else {
                        return `<span>${data || ""}</span>`;
                    } 
                }
            },
            {
                data: "a.id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
            },
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $("#documentsSearch").on("keyup.DT", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
            $('.amg-list-searchbar__icon-documents').click(function (e) {
                e.preventDefault();
                var v = $("#documentsSearch").validate_str_param();
                if (v === false) {
                    alert(config.translations.please_enter_valid_search);
                    return false;
                }
                api.search(v).draw();
            });
        }
    });

    $('#tblDocument').on('click', '.read-more', function(event) {
        event.preventDefault();
        var fullNote = $(this).data('full-note'); 
        // console.log(fullNote);
        $('#fullNote').html(fullNote);           
        $('#noteModal').modal("show"); 
    });
    
    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.attachmentView = function(e) {
        e.preventDefault();
        window.open(
            t.config.url.attachment_view + "/" + $(this).attr("data-id") + "?type=" + $(this).attr("data-type"),
            '_blank'
        );
    };

    t.renderActionsCell = function (record, type) {
        var t = this;
        var actionState = t.getRowActionState(record);
        var quickActions = [];
        if (type !== "display") return "";
        if (actionState.canDelete) {
            quickActions.push(t.quickActionButtonHtml(
                (t.config.translations || {}).action_delete_supplier || "Delete",
                "dtActDel",
                record.id,
                "delete",
                "dtActbtn"
            ));
        }

        var validExtensions = ["pdf", "jpg", "jpeg", "png", "gif"];
        var fileExtension = record.org_name.split('.').pop().toLowerCase();
        if(validExtensions.includes(fileExtension)) {
            quickActions.push(t.quickActionButtonHtml(
                (t.config.translations || {}).action_delete_supplier || "View",
                "dtActbtn",
                record.id,
                "view",
                "tri-view",
                "data-type='consumable'"
            ));
        }

        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_download_supplier || "Download",
            "dtActbtn",
            record.id,
            "download",
            `go-download`,
            `onclick="downloadFile('${t.config.url.document_download}/${record.file_name}')"`
        ));

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.getRowActionState = function (record) {
        return {
            canDelete: t.hasPermission("ConsumableDocumentsDelete"),
        };
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass, extraAttributes = null) {
        return [
            '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '"', extraAttributes, '> ',
            this.getIcon(iconKey),
            '</button>'
        ].join("");
    };

    t.hasPermission = function (name) {
        return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
    };

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getIcon = function (key) {
        return (this.icons && this.icons[key]) ? this.icons[key]() : "";
    };

    t.icons = {
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        download: function() {
            return '<svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>'
        },
        view: function () {
            return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
        },
    }

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.tab.on("click", ".tri-view", $.proxy(t.attachmentView));

    t.tab.on("click", ".dtActDel", $.proxy(t.deleteDocument));
    t.tab.on("click", "#documents-button-reload", $.proxy(t.reload));
};

var PurchasePhase = function(config) {
    var t = this;
    t.config = config;
    var prefix = t.config.client == 'etherealmachines' ? 'CN': 'CNS';
    t.tab = $("section.content").find("#purchase-history-tab");
    t.table = t.tab.find("#tblPurchase");
    t.btn = {};
    t.mdl = $("section.content").find("#purchasemodal");
    t.mdl.frm = t.mdl.find("#PurchaseForm");
    t.mdl.title = t.mdl.frm.find('.modal-title');
    t.btn.btnSubmit = t.mdl.frm.find('#btnSubmit');
    t.btn.btnClear = t.mdl.frm.find('#btnClear');
    t.mdl.frmEl = {};
    t.mdl.frmEl.purchase_date = t.mdl.frm.find("#purchase_date");
    t.mdl.frmEl.received_date = t.mdl.frm.find("#received_date");
    t.mdl.frmEl.exp_date = t.mdl.frm.find("#exp_date");
    t.mdl.frmEl.po_number = t.mdl.frm.find("#po_number");
    t.mdl.frmEl.invoice_no = t.mdl.frm.find("#invoice_no");
    t.mdl.frmEl.batch_no = t.mdl.frm.find("#batch_no");
    t.mdl.frmEl.purchase_by = t.mdl.frm.find("#purchase_by");
    t.mdl.frmEl.qty = t.mdl.frm.find("#qty");
    t.mdl.frmEl.currency = t.mdl.frm.find("#currency");
    t.mdl.frmEl.bill_amount = t.mdl.frm.find("#bill_amount");
    t.mdl.frmEl.imgviewcover = t.mdl.frm.find(".imgviewcoverpur");
    t.mdl.frmEl.imgviewpur = t.mdl.frm.find(".imgviewpur");
    t.show_entries = $("section.content").find('#showSelectPurchase');

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        lengthChange: false,
        pageLength: 10,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [0]
            },
            {
                targets: 0,
                render: function(d) {
                    var a = [];
                    if(jQuery.inArray("PurchaseEdit", t.config.permissions) !== -1) {
                        a.push("&nbsp;<button class='btn dtActbtn btn-edit-purchase' data-toggle='tooltip' data-placement='right' data-original-title='"+config.translations.edit+"' action=\"delete\" data-id=\"" + d.consumable_purchases_id + "\" ><i class=\"fa fa-pencil\"></i></button>");
                    }
                    if(jQuery.inArray("PurchaseDelete", t.config.permissions) !== -1) {
                        a.push("&nbsp;<button class='btn dtActbtn dtActDel' data-toggle='tooltip' data-placement='right' data-original-title='"+config.translations.Delete+"' action=\"delete\" data-id=\"" + d.consumable_purchases_id + "\" ><i class=\"fa fa-trash\"></i></button>");
                    }
                    if(jQuery.inArray("PurchaseDownload", t.config.permissions) !== -1 && d.attachment != null) {
                        if(d.attachment) {
                            var validExtensions = ["pdf", "jpg", "jpeg", "png", "gif"];
                            var fileExtension = d.attachment.split('.').pop().toLowerCase();
                            if (validExtensions.includes(fileExtension)) {
                                a.push("<button class='btn dtActbtn tri-view' data-toggle='tooltip' data-placement='right' data-original-title='View' data-id=\"" + d.consumable_purchases_id + "\" data-type='consumable'><i class=\"fa fa-eye\"></i></button>");
                            }
                            a.push("<a href=\"" + t.config.url.purchase_attachment_download + "/" + d.attachment + "\" class='btn dtActbtn' download data-placement='right' data-toggle='tooltip' data-original-title='"+config.translations.Download_Document+"' data-id=\"" + d.consumable_purchases_id + "\" style=\"cursor: pointer;color: white;\"><i class=\"fa fa-download\"></i></a>");
                        }
                    }
                    return(
                        `<div class="popup-toolbox"><div class="btn-toolbar popup-toolbox-status"><i class="fa fa-cog"></i></div>
                        <div class="popup-toolbox-bar" style="min-width: 40px;border-radius: 11px;color: white;background-color: #454b4e;">${a.join("")} </div></div>`
                    );
                    
                }
            },
        ],
        order: [
            [11, 'desc']
        ],
        colResize: {
            resizeTable: true
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.list,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.location = t.config.location_filter;
                d.filters = t.config.other_filters;
                d.consumable_id = t.config.consumable_id;
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        columns: [
            // { data: 'a' },
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            { data: 'a.con_batch_no' },
            { data: 'a.purchase_date_on' },
            { data: 'a.po_no' },
            { data: 'a.supplier_name' },
            { data: 'a.received_date_on' },
            { data: 'a.exp_date_on' },
            { data: 'a.qty' },
            { data: 'a.purchase_cost_format' },
            { data: 'a.loc_name' },
            { data: 'a.dep_name' },
            { data: 'a.last_updated_at' },
            {
                data: "a.consumable_purchases_id",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
            },
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $("#purchaseSearch").on("keyup.DT", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(v).draw();
                }
            });
            buildColumnVisibilityControls(api);
        }
    });

    t.dTbl.on('column-reorder', function(e, settings, details) {
        var api = t.table.DataTable();
        buildColumnVisibilityControls(api);
    });

    function buildColumnVisibilityControls(api) {
        $("#columnVisibilityControls").empty();
        api.columns().every(function(index) {
            var column = this;
            var columnTitle = $(column.header()).text().trim();
            var checkboxHtml = `
                <div class="dropdown-item" style="padding:6px">
                    <label>
                        <input type="checkbox" data-column="${index}" ${column.visible() ? 'checked' : ''}> ${columnTitle}
                    </label>
                </div>`;
            $("#columnVisibilityControls").append(checkboxHtml);
        });
        $('#columnVisibilityControls input[type="checkbox"]').on('change', function() {
            var columnIndex = $(this).data('column');
            var column = api.column(columnIndex);
            column.visible($(this).prop('checked'));
        });
    }

    t.mdl.optionsCurrency = function() {
        // t.mdl.frmEl.currency.empty().append(new Option(config.translations.Select_Currency_Format, ""));
        $.each(t.config.currencies, function(i,v) {
            var opt = t.config.default_currency_format == i ? new Option("", i, true, true) : new Option(config.translations.Select_Currency_Format, i);
            opt.innerHTML = v.name + " (" + v.symbol_html + ")";
            t.mdl.frmEl.currency.append(opt);
        });
        t.mdl.frmEl.currency.trigger("change");
    };

    t.addPurchase = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.add_purchase;
        var title = t.config.consumable_unique_tag == '' ? ('Add Purchase '+prefix+t.config.consumable_id) : ('Add Purchase '+t.config.consumable_unique_tag);
        t.mdl.title.html(title);
        t.btn.btnSubmit.text(config.translations.save);
        t.mdl.frmEl.imgviewcover.addClass('hide');
        t.mdl.frmEl.imgviewpur.attr("src", "");
        t.mdl.forAction = "";
        t.resetFrm();
        t.frmValidator.resetForm();
        t.mdl.modal("show");
        t.mdl.frmEl.purchase_date.on('changeDate', function (e) {
            var selectedDate = e.date;
            t.mdl.frmEl.received_date.datepicker('setStartDate', selectedDate);
            t.mdl.frmEl.received_date.datepicker('update', '');
            t.mdl.frmEl.exp_date.datepicker('setStartDate', selectedDate);
            t.mdl.frmEl.exp_date.datepicker('update', '');
        });
    };

    t.editPurchase = function (e) {
        e.preventDefault();
        var purchaseId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.update_purchase + "/" + purchaseId;
        var http = $.get(t.config.url.get_purchase + "/" + purchaseId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {   
                    t.resetFrm();
                    t.frmValidator.resetForm();
                    var title = t.config.consumable_unique_tag == '' ? ('Edit Purchase '+prefix+t.config.consumable_id) : ('Edit Purchase '+t.config.consumable_unique_tag);
                    t.mdl.title.html(title);
                    t.btn.btnSubmit.text(config.translations.save);
                    t.mdl.modal("show");
                    t.loadForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.loadForm = function (data) {
        t.resetFrm();
        if (typeof data[0].dev == "object" && typeof data[0].dev.dropdown == "object" && data[0].dev.dropdown.purchase_by != null) {
            t.mdl.frmEl.purchase_by.append(new Option(data[0].dev.dropdown.purchase_by.text, data[0].dev.dropdown.purchase_by.id, true, true)).trigger("change");
        }
        if (typeof data[0].dev == "object" && typeof data[0].dev.dropdown == "object" && data[0].dev.dropdown.currency != null) {
            t.mdl.frmEl.currency.val(data[0].dev.dropdown.currency.id).trigger("change");
        }

        t.mdl.frmEl.purchase_date.val(data[0].purchase_date);
        t.mdl.frmEl.received_date.val(data[0].received_date);
        t.mdl.frmEl.exp_date.val(data[0].exp_date);
        t.mdl.frmEl.po_number.val(data[0].po_no);
        t.mdl.frmEl.invoice_no.val(data[0].invoice_no);
        t.mdl.frmEl.batch_no.val(data[0].batch_no);
        t.mdl.frmEl.qty.val(data[0].qty);
        t.mdl.frmEl.bill_amount.val(data[0].purchase_price);
        t.mdl.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
        t.mdl.frmEl.received_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
        t.mdl.frmEl.exp_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });

        var purchaseDate = t.mdl.frmEl.purchase_date.val();
        if (purchaseDate) {
            t.mdl.frmEl.received_date.datepicker('setStartDate', purchaseDate);
            t.mdl.frmEl.exp_date.datepicker('setStartDate', purchaseDate);
        }

        t.mdl.frmEl.purchase_date.on('changeDate', function (e) {
            var selectedDate = e.date;
            t.mdl.frmEl.received_date.datepicker('setStartDate', selectedDate);
            t.mdl.frmEl.received_date.datepicker('update', '');

            t.mdl.frmEl.exp_date.datepicker('setStartDate', selectedDate);
            t.mdl.frmEl.exp_date.datepicker('update', '');
        });

        if(data[0].attachment != null){
            t.mdl.frmEl.imgviewcover.addClass('hide');
            $('#imgviewpur').attr("src", t.config.imgviewpathpur + "/" + data[0].attachment);
        }

    };
    t.mdl.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    t.mdl.frmEl.received_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    t.mdl.frmEl.exp_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    t.resetFrm = function () {
        t.mdl.frmEl.purchase_date.val('');
        t.mdl.frmEl.received_date.val('');
        t.mdl.frmEl.exp_date.val('');
        t.mdl.frmEl.po_number.val('');
        t.mdl.frmEl.invoice_no.val('');
        t.mdl.frmEl.batch_no.val(t.config.consumable_id);
        t.mdl.frmEl.qty.val('');
        t.mdl.frmEl.bill_amount.val('');
        t.mdl.frmEl.purchase_by.val("").trigger("change");
        t.mdl.frmEl.currency.val("").trigger("change");
        t.frmValidator.resetForm();
        t.mdl.optionsCurrency();
        t.mdl.frmEl.imgviewcover.addClass('hide');
        t.mdl.frmEl.imgviewpur.attr("src", "");
        t.mdl.find(".amg-form-invalid").removeClass("amg-form-invalid");
    };

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.handleSubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var formData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                t.btn.btnSubmit.prop('disabled', true).text('Please wait...');
            },
            complete: function() {
                t.btn.btnSubmit.prop('disabled', false).text('Save');
            },
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deletePurchase = function(e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.purchase_delete;
        var data = {
            'msg':config.translations.something_went_wrong,
        };
        var send_data = {
            "id": docId,
            "_token": t.config.token 
        }
        sweetAlertPost('You could not recover it after delete. Are you sure to delete the purchase permanently?','warning',  t.httpPostPath, t.dTbl.ajax, data, send_data);
    };

    t.attachmentViewPur = function(e) {
        e.preventDefault();
        window.open(
            t.config.url.purchase_attachment_view + "/" + $(this).attr("data-id") + "?type=" + $(this).attr("data-type"),
            '_blank'
        );
    };

    window.downloadFile = function (url) {
        $('<a>').attr('href', url).attr('download', '')[0].click();
    }

    t.renderActionsCell = function (record, type) {
        var t = this;
        var actionState = t.getRowActionState(record);
        var quickActions = [];
        if (type !== "display") return "";
        if (actionState.canEdit) {
            quickActions.push(t.quickActionButtonHtml(
                (t.config.translations || {}).action_edit_supplier || "Edit",
                "dtActEdit",
                record.consumable_purchases_id,
                "edit",
                "btn-edit-purchase"
            ));
        }
        if (actionState.canDelete) {
            quickActions.push(t.quickActionButtonHtml(
                (t.config.translations || {}).action_delete_supplier || "Delete",
                "dtActDel",
                record.consumable_purchases_id,
                "delete",
                "go-del"
            ));
        }
        if (actionState.canDownload) {
            if(record.attachment) {
                quickActions.push(t.quickActionButtonHtml(
                    (t.config.translations || {}).action_download_supplier || "view",
                    "dtActView",
                    record.consumable_purchases_id,
                    "view",
                    "tri-view",
                    "data-type='consumable'"
                ));

                quickActions.push(t.quickActionButtonHtml(
                    (t.config.translations || {}).action_download_supplier || "Download",
                    "dtActDownload",
                    record.consumable_purchases_id,
                    "download",
                    `go-download`,
                    `onclick="downloadFile('${t.config.url.purchase_attachment_download}/${record.attachment}')"`
                ));
            }
        }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions justify-content-start">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.getRowActionState = function (record) {
        return {
            canEdit: t.hasPermission("PurchaseEdit"),
            canDelete: t.hasPermission("PurchaseDelete"),
            canDownload: t.hasPermission('PurchaseDownload')
        };
    };

    t.isFilledValue = function (value) {
        var text;
        if (value === null || value === undefined) return false;
        text = String(value).trim();
        return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass, extraAttributes = null) {
        return [
            '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '"', extraAttributes, '> ',
            this.getIcon(iconKey),
            '</button>'
        ].join("");
    };

    t.hasPermission = function (name) {
        return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
    };

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.getIcon = function (key) {
        return (this.icons && this.icons[key]) ? this.icons[key]() : "";
    };

    t.icons = {
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        view: function () {
            return '<svg viewBox="0 0 19 13" fill="none" ><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"/></svg>';
        },
        download: function() {
            return '<svg viewBox="0 0 18 18" fill="none"><path d="M18 11.25V17.25C18 17.4489 17.921 17.6397 17.7803 17.7803C17.6397 17.921 17.4489 18 17.25 18H0.75C0.551088 18 0.360322 17.921 0.21967 17.7803C0.0790178 17.6397 0 17.4489 0 17.25V11.25C0 11.0511 0.0790178 10.8603 0.21967 10.7197C0.360322 10.579 0.551088 10.5 0.75 10.5C0.948912 10.5 1.13968 10.579 1.28033 10.7197C1.42098 10.8603 1.5 11.0511 1.5 11.25V16.5H16.5V11.25C16.5 11.0511 16.579 10.8603 16.7197 10.7197C16.8603 10.579 17.0511 10.5 17.25 10.5C17.4489 10.5 17.6397 10.579 17.7803 10.7197C17.921 10.8603 18 11.0511 18 11.25ZM8.46937 11.7806C8.53903 11.8504 8.62175 11.9057 8.7128 11.9434C8.80384 11.9812 8.90144 12.0006 9 12.0006C9.09856 12.0006 9.19616 11.9812 9.2872 11.9434C9.37825 11.9057 9.46097 11.8504 9.53063 11.7806L13.2806 8.03063C13.3503 7.96094 13.4056 7.87822 13.4433 7.78717C13.481 7.69613 13.5004 7.59855 13.5004 7.5C13.5004 7.40145 13.481 7.30387 13.4433 7.21283C13.4056 7.12178 13.3503 7.03906 13.2806 6.96937C13.2109 6.89969 13.1282 6.84442 13.0372 6.8067C12.9461 6.76899 12.8485 6.74958 12.75 6.74958C12.6515 6.74958 12.5539 6.76899 12.4628 6.8067C12.3718 6.84442 12.2891 6.89969 12.2194 6.96937L9.75 9.43969V0.75C9.75 0.551088 9.67098 0.360322 9.53033 0.21967C9.38968 0.0790176 9.19891 0 9 0C8.80109 0 8.61032 0.0790176 8.46967 0.21967C8.32902 0.360322 8.25 0.551088 8.25 0.75V9.43969L5.78063 6.96937C5.63989 6.82864 5.44902 6.74958 5.25 6.74958C5.05098 6.74958 4.86011 6.82864 4.71937 6.96937C4.57864 7.11011 4.49958 7.30098 4.49958 7.5C4.49958 7.69902 4.57864 7.88989 4.71937 8.03063L8.46937 11.7806Z" fill="currentColor"></path></svg>'
        }
    }

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        ignore: ':hidden:not(.select2-hidden-accessible)',
        onkeyup: function (element, event) {
            this.element(element);
        },
        errorClass: 'error amg-form-invalid',
        rules: {
            purchase_date: {
                required: true,
            },
            currency: {
                required: true,
                clean_text_only: true
            },
            po_number: {
                required: false,
                clean_text_only: true,
                maxlength: 30,
            },
            invoice_no: {
                required: false,
                clean_text_only: true,
                maxlength: 30,
            },
            bill_amount: {
                required: true,
                number: true ,
                validAmountFormat: true,
                maxLength15: true
            },
            qty:{
                required: true,
                digits :true,
                maxlength: 5,
                min:1,
            },
            attachment: {
                extension: "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
                filesize: 2000000
            }
        },
        messages: {
            attachment: {
                extension: "Invalid file extension",
                filesize: "File size must be less than 2MB."
            }
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
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

    $('.datepicker').on('changeDate change', function () {
        $(this).valid();
    });
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');
    $.validator.addMethod("validAmountFormat", function(value, element) {
        return this.optional(element) || /^\d+(\.\d{1,4})?$/.test(value);
    }, "Please enter a valid amount (up to 4 decimal places).");
    
    $.validator.addMethod("maxLength15", function(value, element) {
        return this.optional(element) || value.length <= 15;
    }, "Maximum 15 characters allowed.");

    var select2Opts = { width: "100%" };
    t.mdl.frmEl.purchase_by.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frmEl.purchase_by.parent(),
        ajax: {
            url: t.config.getSupplierByAjax,
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
        placeholder: config.translations.select_the_purchase_from,
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    }));
    t.mdl.frmEl.purchase_by.trigger("change");
    t.mdl.frmEl.currency.select2($.extend({}, select2Opts, { placeholder: config.translations.no_filter, dropdownParent:t.mdl.frmEl.currency.parent()}));

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $("#purchaseSearch").validate_str_param();
        if(v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };
    t.tab.on("click", '.btn-add-purchase', $.proxy(t.addPurchase));
    t.tab.on("click", ".btn-edit-purchase", $.proxy(t.editPurchase));
    t.btn.btnSubmit.on("click", $.proxy(t.handleSubmit));
    t.tab.on("click", '.btn-reload-list', $.proxy(t.tableSearch));
    t.tab.on("click", '.amg-list-searchbar__icon-purchase', $.proxy(t.tableSearch));
    t.tab.on("click", ".dtActDel", $.proxy(t.deletePurchase));
    t.tab.on("click", ".tri-view", $.proxy(t.attachmentViewPur));
};

var CheckOut = function(config, requestOngoing) {
    var t = this;
    t.config = config;
    t.requestOngoing = requestOngoing;

     var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };

    var ChkoutConsumableMdl = $("#checkoutconsumablemodal");
        ChkoutConsumableMdl.frm = ChkoutConsumableMdl.find("#ConsumableCheckoutForm");
        ChkoutConsumableMdl.frmEl = {};
        ChkoutConsumableMdl.frmEl.assigned_for = ChkoutConsumableMdl.find("#assigned_for");
        ChkoutConsumableMdl.frmEl.assigned_to = ChkoutConsumableMdl.find("#assigned_to");
        ChkoutConsumableMdl.frmEl.device_id = ChkoutConsumableMdl.find("#device_id");
        ChkoutConsumableMdl.frmEl.assigned_place = ChkoutConsumableMdl.find("#assigned_place");
        ChkoutConsumableMdl.frmEl.note = ChkoutConsumableMdl.find("#note");
        ChkoutConsumableMdl.frmEl.assigned_to.select2({width:'100%', allowClear: true});
        ChkoutConsumableMdl.frmValidator = ChkoutConsumableMdl.frm.validate({
            onsubmit: false,
            ignore: ':hidden:not(.select2-hidden-accessible)',
            onkeyup: function (element, event) {
                this.element(element);
            },
            errorClass: 'error amg-form-invalid',
            rules: {
                assigned_to: {
                    required: true,
                    clean_text_only: true
                },
                note: {
                    required: function () {
                        return config.client === "knightfrank";
                    },
                    clean_text_only: true
                },
            },
            errorPlacement: function (error, element) {
                error.appendTo(element.parent().parent());
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

    $("#checkoutconsumablemodal").on('show.bs.modal', function(e){
        $('#ConsumableCheckoutForm')[0].reset();
        ChkoutConsumableMdl.resetForm();
        ChkoutConsumableMdl.frmValidator.resetForm();
        ChkoutConsumableMdl.frmEl.assigned_to.val(null).trigger("change");
        ChkoutConsumableMdl.frmEl.note.val(null);
        var company_id = $(e.relatedTarget).attr('data-company_id');
        var entityId = $(e.relatedTarget).attr('data-id');
        var select2Opts = { width: "100%" };
        ChkoutConsumableMdl.frmEl.assigned_to.select2($.extend({}, select2Opts, {
            dropdownParent: ChkoutConsumableMdl.frmEl.assigned_to.parent(),
            ajax: {
                url: t.config.getUserByAjax,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
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
                        company_id: company_id
                    };
                },
                delay: 300
            },
            allowClear:true,
            placeholder: t.config.translations.Select_the_User,
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
            },
            templateResult: function(s) {
                if(typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                var email = s.email == null ? "" : s.email;
                var name = t.safeDisplayValue(s.text, "-");
                var imageUrl = t.safeDisplayValue(s.img_path, "");
                var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");

                return $(
                    '<div class="d-flex align-items-center gap-2">' +
                        avatarHtml +
                        '<span>' + t.escapeHtml(name) + '</span>' +
                    '</div>'
                );
            },
        }));

        if (ChkoutConsumableMdl.frmEl.assigned_place.hasClass("select2-hidden-accessible")) {
            ChkoutConsumableMdl.frmEl.assigned_place.select2("destroy");
        }

        ChkoutConsumableMdl.frmEl.assigned_place.empty();
        ChkoutConsumableMdl.frmEl.assigned_place.select2( $.extend({}, select2Opts, {
            dropdownParent: ChkoutConsumableMdl.frmEl.assigned_place.parent(),
            ajax: {
                url: t.config.ajaxGetInternalPlace,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
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
                        company_id: company_id
                    };
                },
                delay: 300,
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                }
            },
            allowClear: true,
            placeholder: t.config.translations.Select_Place,
            templateResult: function (s) {
                    if (typeof s.loading != "undefined" && s.loading) {
                        return $("<div>" + s.text + "</div>");
                    }
                    if(!s.id) {
                        return s.text;
                    }
                    var $container = $("<div>");
                $container.append(`<svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M20 10C20 14.4183 12 22 12 22C12 22 4 14.4183 4 10C4 5.58172 7.58172 2 12 2C16.4183 2 20 5.58172 20 10Z" stroke="#131927" stroke-width="1.5" />
                                        <path d="M12 11C12.5523 11 13 10.5523 13 10C13 9.44772 12.5523 9 12 9C11.4477 9 11 9.44772 11 10C11 10.5523 11.4477 11 12 11Z" fill="#131927" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg> ${s.text}`);
                    return $container;
                },
            templateSelection: function(data, container) { 
                if (container) {
                    $(container).attr('title', data.text);
                }
                return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
            },
            allowClear: true,
            placeholder: "Select The Place"
        }));

        ChkoutConsumableMdl.frmEl.assigned_for.select2($.extend({}, select2Opts, { dropdownParent: t.config.assignedForOptions}));
        ChkoutConsumableMdl.frmEl.device_id.select2($.extend({}, select2Opts, {
            dropdownParent: ChkoutConsumableMdl.frmEl.device_id.parent(),
            ajax: {
                url: t.config.getDeviceForCheckoutDropDown,
                dataType: "json",
                transport: function (params, success, failure) {
                    if (!company_id) {
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
                            return company_id;
                        }
                    };
                },
                delay: 300
            },
            allowClear: true,
            //minimumInputLength: 1,
            placeholder: config.translations.select_the_device,
            templateResult: function(s) {
                if(typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }
                a = `<div class="so-t">
                        <svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' viewBox='0 0 24 24'>
                            <path d='M0 0h24v24H0z' fill='none' />
                            <path fill='currentColor' d='M5.5 7A1.5 1.5 0 0 1 4 5.5A1.5 1.5 0 0 1 5.5 4A1.5 1.5 0 0 1 7 5.5A1.5 1.5 0 0 1 5.5 7m15.91 4.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.11 0-2 .89-2 2v7c0 .55.22 1.05.59 1.41l8.99 9c.37.36.87.59 1.42.59s1.05-.23 1.41-.59l7-7c.37-.36.59-.86.59-1.41c0-.56-.23-1.06-.59-1.42' />
                        </svg>
                        ${s.asset_tag}
                    </div>`;

                    if (s.asset_name != null) {
                        a += `<div class="so-t">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor" d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2zM4 6h16v10H4z" />
                                    </svg>
                                    ${s.asset_name}
                                </div>`;
                }

                    a += `
                        <div class="so-m">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path fill="currentColor" d="M12.588 6.086q.22-.222.22-.549t-.222-.547t-.549-.22t-.548.22t-.22.55t.222.547t.549.22t.548-.22M6 22V2h12.077v4.83H19v3.686h-.923V22zm1-1h10.077V3H7zm0 0V3z" />
                            </svg>
                            ${s.name} ${s.modelno}
                        </div>
                        <div class="so-t">
                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path fill="currentColor" d="M1 21v-5h2v3h3v2zm17 0v-2h3v-3h2v5zM4 18V6h2v12zm3 0V6h1v12zm3 0V6h2v12zm3 0V6h3v12zm4 0V6h1v12zm2 0V6h1v12zM1 8V3h5v2H3v3zm20 0V5h-3V3h5v5z" />
                            </svg>
                            ${s.serial}
                        </div>`;
                return $("<div>" + a + "</div>");
            },
            templateSelection: function(data, container) { 
                if (container) {
                    $(container).attr('title', data.text);
                }
                return data.text.length > 50 ? data.text.substring(0, 55) + '...' : data.text;
            }
        }));
        ChkoutConsumableMdl.frmEl.assigned_for.select2($.extend({}, select2Opts, {
            dropdownParent: $('#checkoutconsumablemodal'),
            data: t.config.assignedForOptions,
            width: '100%',
            allowClear: true
        }));

        ChkoutConsumableMdl.frmEl.assigned_for.val("1").trigger('change');
            // {{-- $.getJSON( '{{ url('consumable-checkout-users') }}' + '/' + entityId, function( data ) {
            //     ChkoutConsumableMdl.frmEl.assigned_to.children(':not(:first-child)').remove();
            //     $.each(data, function(index, value) {
            //         ChkoutConsumableMdl.frmEl.assigned_to.append('<option value="' + value.id + '">' + value.first_name + ' ' + value.last_name + '</option>');
            //     });
            //     ChkoutConsumableMdl.frmEl.assigned_to.trigger("change");
            // }); --}}

            $('label.msg').remove();
            $('#checkoutmodalHead').html('Checkout');
            $('#checkoutsubmitBtn').html('Checkout');
            $('#chechout-consumable-name').html($(e.relatedTarget).attr('data-name'));
            $('#ConsumableCheckoutForm').attr('action', t.config.url.checkout + '/' + entityId);
            ChkoutConsumableMdl.frmEl.assigned_for.val("1").trigger('change');
        });

        ChkoutConsumableMdl.frm.submit(function(e){
            e.preventDefault();
            if(t.requestOngoing) return false;

            if( ChkoutConsumableMdl.frmValidator.form() == false ) {
                return false;
            }

            t.requestOngoing = true;
            var formData = new FormData(this);
            var submitUrl =  $(this).attr("action");

            var submitBtn = $("#checkoutsubmitBtn");
            submitBtn.prop("disabled", true);

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    submitBtn.prop('disabled', true).text('Please wait...');
                },
                success: function (data) {
                    $('label.msg').remove();
                    if(data.status == 'error'){
                        if(typeof data.section != 'undefined'){
                            sweetAlert('center', data.status, data);
                            // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                        }else{
                            $.each(data.errors, function( index, value ) {
                                var parent = $( "[name='"+index+"']" ).parent();
                                if(parent.hasClass( "input-group" ))
                                    parent.after('<label class="error msg" for="">'+value+'</label>')
                                else
                                    $( "[name='"+index+"']" ).after('<label class="error msg" for="">'+value+'</label>')
                            });
                        }

                    }
                    else if(typeof data[0] != 'undefined'){
                        if(data[0].status == 'success'){
                            sweetAlert('center', 'success', data[0]);
                            // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data[0].msg + '.</p></div>'});
                            $('#consumablemodal').modal('hide');

                            //for clone
                            if(typeof data[0].id != "undefined")
                            window.location.href = t.config.url.infourl+'/'+data[0].id
                            //for clone
                            if (typeof dtblRef != "undefined")
                                {   dtblRef.dTbl.ajax.reload();   }
                            else {
                                $.get(t.config.url.infocontent, function(data) {
                                    $('#basic-info-tab').html(data);
                                })
                            }
                        }
                    }
                    else if(data.status == 'success'){
                        sweetAlert('center', 'success', data);
                        // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data.msg + '.</p></div>'});
                        $('#checkoutconsumablemodal').modal('hide');
                        // window.location.href = t.config.url.infourl+'/'+data.id
                        if (typeof dtblRef != "undefined")
                        dtblRef.dTbl.ajax.reload();
                        if(window.location.pathname.includes(new URL(config.url.infourl).pathname))
                        {
                            $.get(t.config.url.infocontent, function(data) {
                                $('#basic-info-tab').html(data);
                            });

                            // this is not required since on tab click we are calling the api
                            // dTbl.ajax.reload()
                            // t.dTblHis.ajax.reload()
                        }
                    }
                    t.requestOngoing = false;
                },
                complete: function() {
                    submitBtn.prop("disabled", false).text('Checkout');
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });
        ChkoutConsumableMdl.resetForm = function () {
            ChkoutConsumableMdl.frm[0].reset();
            ChkoutConsumableMdl.frmEl.assigned_for.val('').trigger('change');
            ChkoutConsumableMdl.frmEl.assigned_to.val('').trigger('change');
            ChkoutConsumableMdl.frmEl.device_id.val('').trigger('change');
            ChkoutConsumableMdl.frmEl.assigned_place.val('').trigger('change');
            ChkoutConsumableMdl.frmEl.note.val('');
        };

        t.switchCheckTarget = function() {
            if(ChkoutConsumableMdl.frmEl.assigned_for.val() == "1"){
                ChkoutConsumableMdl.frmEl.device_id.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").show();
                ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.assigned_to.rules("add", {required: true});
                ChkoutConsumableMdl.frmEl.assigned_place.rules("remove", "required");
                ChkoutConsumableMdl.frmEl.device_id.rules("remove", "required");
            } else if (ChkoutConsumableMdl.frmEl.assigned_for.val() == "2") {
                ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").show();
                ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.device_id.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.assigned_place.rules("add", {required: true});
                ChkoutConsumableMdl.frmEl.assigned_to.rules("remove", "required");
                ChkoutConsumableMdl.frmEl.device_id.rules("remove", "required");
            } else {
                ChkoutConsumableMdl.frmEl.device_id.closest(".cover").show();
                ChkoutConsumableMdl.frmEl.assigned_to.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.assigned_place.closest(".cover").hide();
                ChkoutConsumableMdl.frmEl.device_id.rules("add", {required: true});
                ChkoutConsumableMdl.frmEl.assigned_to.rules("remove", "required");
                ChkoutConsumableMdl.frmEl.assigned_place.rules("remove", "required");
            }
        };

        t.safeDisplayValue = function(value, fallback) {
            return CheckOut.safeDisplayValue(value, fallback);
        };

        t.escapeHtml = function(text) {
            return CheckOut.escapeHtml(text);
        };

        t.getAvatarHtml = function(name, imageUrl, className) {
            return CheckOut.getAvatarHtml(name, imageUrl, className);
        };

        t.getUserInitials = function(name) {
            return CheckOut.getUserInitials(name);
        };

        t.isFilledValue = function(value) {
            return CheckOut.isFilledValue(value);
        };

        ChkoutConsumableMdl.frmEl.assigned_for.on("change", $.proxy(t.switchCheckTarget));
}

CheckOut.safeDisplayValue = function(value, fallback) {
    return value !== null && value !== undefined && value !== ""
        ? String(value).trim()
        : (fallback !== undefined ? fallback : "-");
};

CheckOut.getAvatarHtml = function(name, imageUrl, className) {
    var cssClass = className || "user-list-avatar";
    var safeName = CheckOut.escapeHtml(CheckOut.safeDisplayValue(name, "User"));

    if (CheckOut.isFilledValue(imageUrl)) {
        return '<img src="' + CheckOut.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + CheckOut.escapeHtml(cssClass) + '">';
    }

    return '<span class="' + CheckOut.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' +
        CheckOut.escapeHtml(CheckOut.getUserInitials(name)) +
        '</span>';
};

CheckOut.escapeHtml = function (value) {
   return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
};

CheckOut.getUserInitials = function (name) {
    var value = String(this.safeDisplayValue(name, "")).trim();
    if (!value) return "NA";
    return value.charAt(0).toUpperCase();
};

CheckOut.isFilledValue = function (value) {
    var text;
    if (value === null || value === undefined) return false;
    text = String(value).trim();
    return text !== "" && text !== "0" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
};

var ConsumableForm = function(config, requestOngoing) {
    var t = this;
    t.config = config;
    t.requestOngoing = requestOngoing;

    var consumableModal = $('#consumablemodal');
    var consumabletabs = consumableModal.find('#consumableTabs');

    var ConsumableForm = {};
        ConsumableForm.frm = $("#ConsumableForm");
        ConsumableForm.frmEl = {};
        ConsumableForm.frmEl.id = ConsumableForm.frm.find("#id");
        ConsumableForm.frmEl.supplier = ConsumableForm.frm.find("#supplier_id");
        ConsumableForm.frmEl.company_id = ConsumableForm.frm.find("#company_id");
        ConsumableForm.frmEl.category_id = ConsumableForm.frm.find("#category_id");
        ConsumableForm.frmEl.manufacturer_id = ConsumableForm.frm.find("#manufacturer_id");
        ConsumableForm.frmEl.location_id = ConsumableForm.frm.find("#location_id");
        ConsumableForm.frmEl.internal_place = ConsumableForm.frm.find("#internal_place");
        ConsumableForm.frmEl.invoice_id = ConsumableForm.frm.find("#invoice_id");
        ConsumableForm.frmEl.currency = ConsumableForm.frm.find("#currency");
        ConsumableForm.frmEl.name = ConsumableForm.frm.find("#name");
        ConsumableForm.frmEl.order_number = ConsumableForm.frm.find("#order_number");
        ConsumableForm.frmEl.purchase_date = ConsumableForm.frm.find("#purchase_date");
        ConsumableForm.frmEl.purchase_cost = ConsumableForm.frm.find("#purchase_cost");
        ConsumableForm.frmEl.qty = ConsumableForm.frm.find("#qty");
        ConsumableForm.frmEl.notes = ConsumableForm.frm.find("#notes");
        ConsumableForm.frmEl.customFieldsPrvEl = ConsumableForm.frm.find(".custom-fields-follow");
        ConsumableForm.frmEl.department_id = ConsumableForm.frm.find("#department_id");
        ConsumableForm.frmEl.unit = ConsumableForm.frm.find("#unit");
        ConsumableForm.frmEl.imgviewcover = ConsumableForm.frm.find(".imgviewcover");
        ConsumableForm.frmEl.imgview = ConsumableForm.frm.find("#imgview");
        ConsumableForm.frmEl.clone_img = ConsumableForm.frm.find("#clone_img");
        ConsumableForm.frmEl.consumable_thresholds = ConsumableForm.frm.find("#consumable_thresholds");
        ConsumableForm.frmEl.thresholds_alerts = ConsumableForm.frm.find("#thresholds_alerts");
        ConsumableForm.frmEl.threshold_alert_users = ConsumableForm.frm.find("#threshold_alert_users");
        ConsumableForm.frmEl.reorder_limits = ConsumableForm.frm.find("#reorder_limits");
        ConsumableForm.frmEl.unique_tag = ConsumableForm.frm.find("#unique_tag");
        ConsumableForm.frmEl.image = ConsumableForm.frm.find("#image");
        ConsumableForm.frmEl.images_list = ConsumableForm.frm.find('#consumables-image-list');

        var count = 0;
        var current_purchase_date = "";
        var current_supplier_name = current_supplier_id = "";
        t.action ='';
        t.internal_places = [];
        ConsumableForm.frmValidator = ConsumableForm.frm.validate({
            onsubmit: false,
            errorClass: 'error amg-form-invalid',
            rules: {
                company_id: {
                    required: true,
                },
                unique_tag: {
                    maxlength: 100
                },
                name: {
                    maxlength: 100,
                    required: true,
                    str_name: false,
                    clean_text_only: true
                },
                category_id: {
                    required: true,
                },
                manufacturer_id: {
                    required: false
                },
                department_id: {
                    required: false,
                },
                location_id: {
                    required: true
                },
                supplier_id: {
                    required: false
                },
                order_number: {
                    maxlength: 100,
                    required: false,
                    clean_text_only: true
                },
                invoice_id: {
                    required: false,
                    clean_text_only: true
                },
                purchase_date: {
                    remarks: true
                },
                currency: {
                    required: false
                },
                purchase_cost: {
                    min: 0,
                    number: true
                },
                qty: {
                    min: 1,
                    required: true,
                    digits: true,
                },
                notes: {
                    remarks: true,
                    clean_text_only: true
                },
                consumable_thresholds: {
                    digits: true,
                    min: 0,
                    max:10000,
                },
                reorder_limits: {
                    digits: true,
                    min: 0,
                    max:10000,
                },
                image: {
                    customImageType: true,
                    maxfiles: 1,
                    filesize: 2000000
                }
            },
            messages: {
                image: {
                    customImageType: "Only png, jpg, jpeg and bmp files are allowed.",
                    filesize: "File size must be less than 2MB."
                }
            },
            errorPlacement: function (error, element) {
                error.appendTo(element.parent().parent());
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

    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'File size must be less than {0} bytes.');

    $.validator.addMethod("customImageType", function (value, element) {
        if (element.files.length === 0) {
            return true;
        }
        var allowedMimeTypes = [
            "image/png",
            "image/jpeg",
            "image/bmp"
        ];
        return allowedMimeTypes.includes(element.files[0].type);
    }, "Only png, jpg, jpeg and bmp files are allowed.");

    // function checkThresholdValidation() {
    //     var thresholdValue = ConsumableForm.frmEl.consumable_thresholds.val();
    //     var thresholdAlertValue = ConsumableForm.frmEl.threshold_alert_users.val();
    //     if((thresholdValue > 0 && thresholdValue != null) && thresholdAlertValue === null){
    //         $("#threshold_alert_users").rules("add", { 
    //             required: true, 
    //             messages: { required: "This field is required when thresholds are set." } 
    //         });
    //     }else{
    //         $("#threshold_alert_users").rules("remove", "required");
    //     }
    //     ConsumableForm.frmValidator.element("#threshold_alert_users");
    // }

    // $("#consumable_thresholds").on("input", function () {
    //     checkThresholdValidation();
    // });

    // $("#submitBtn").on("click", function (e) {
    //     checkThresholdValidation(); 

    //     if (!ConsumableForm.frm.valid()) {
    //         e.preventDefault();
    //     }
    // });

    ConsumableForm.frm.submit(function(e){
        e.preventDefault();
        if(t.requestOngoing) return false;

        if( ConsumableForm.frmValidator.form() == false ) {
            return false;
        }
        t.requestOngoing = true;
        var formData = new FormData(this);
        var submitUrl =  $(this).attr("action");

        $.ajax({
            url: submitUrl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#submitBtn').prop('disabled', true).text('Please wait...');
            },
            success: function (data) {
                $('label.msg').remove();

                if(data.status == 'error'){
                    if(typeof data.section != 'undefined'){
                        sweetAlert('center', 'error', data);
                        // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                    }else{
                        $.each(data.errors, function( index, value ) {
                            var parent = $( "[name='"+index+"']" ).parent();
                            if(parent.hasClass( "input-group" ))
                                parent.after('<label class="error msg" for="">'+value+'</label>')
                            else
                                $( "[name='"+index+"']" ).after('<label class="error msg" for="">'+value+'</label>')
                        });
                    }
                }
                else if(typeof data[0] != 'undefined'){
                    if(data[0].status == 'success'){
                        sweetAlert('center', 'success', data[0]);
                        // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data[0].msg + '.</p></div>'});
                        $('#consumablemodal').modal('hide');

                        //for clone
                        if(typeof data[0].id != "undefined")
                        window.location.href = t.config.url.infourl+'/'+data[0].id
                        //for clone
                        if (typeof dtblRef != "undefined")
                            {   dtblRef.dTbl.ajax.reload();   }
                        else {
                            $.get(t.config.url.infocontent, function(data) {
                                $('#basic-info-tab').html(data);
                            })
                        }
                    }
                }
                else if(data.status == 'success'){
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>success!!</h3><p>' + data.msg + '.</p></div>'});
                    $('#checkoutconsumablemodal').modal('hide');
                    if (typeof dtblRef != "undefined") dtblRef.dTbl.ajax.reload();
                    if(window.location.pathname.includes(new URL(config.url.infourl).pathname))
                    {
                        $.get(t.config.url.infocontent, function(data) {
                            $('#basic-info-tab').html(data);
                        })
                        dTbl.ajax.reload()
                        t.dTblHis.ajax.reload()
                    }
                }
                t.requestOngoing = false;
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).text('Edit');
                t.requestOngoing = false;
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });

    function showImage(imageData, entityId, isClone) {
        let $li = $("<li>").addClass("list-group-item d-flex align-items-center justify-content-between").attr("data-file", imageData);
        let $img = $("<img>").attr("src", baseURL + "/storage/uploads/consumable/" + imageData).addClass("img-thumbnail").css({ width: "80px", height: "80px", objectFit: "cover", marginRight: "10px" });
        let $link = $("<a>").attr("href", baseURL + "/storage/uploads/consumable/" + imageData).attr("target", "_blank").text(imageData);
        let $left = $("<div>").append($img).append("<br>").append($link);
        let data_action = "edit";
        if(isClone) {
            data_action = "clone";
        }
        let $del = $("<button>").addClass("btn btn-sm btn-danger del-link").attr("data-id", entityId).attr('data-action', data_action).html("Delete");
        $li.append($left).append($del);
        ConsumableForm.frmEl.images_list.html($li);
    }

    function populateWithEntity(id,action) {
        //  current_Purchase_date = "";
        ConsumableForm.frmValidator.resetForm();
        $.getJSON( config.url.edit + '/' + id, function(data) {
            ConsumableForm.frmEl.id.val(id);
            ConsumableForm.frmEl.company_id.val(data.data.company_id);
            ConsumableForm.frmEl.name.val(data.data.name);
            // ConsumableForm.frmEl.category_id.val(data.data.category_id);
            ConsumableForm.frmEl.manufacturer_id.val(data.data.manufacturer_id);
            ConsumableForm.frmEl.invoice_id.empty();
            if (typeof data.dropdown == "object" && typeof data.dropdown.invoice == "object" && data.dropdown.invoice != null) {
                ConsumableForm.frmEl.invoice_id.append(new Option(data.dropdown.invoice.text, data.dropdown.invoice.id, true, true)).trigger("change");
            }
            ConsumableForm.frmEl.invoice_id.trigger("change");
            order_no = data.data.order_number;
            ConsumableForm.frmEl.order_number.val(order_no);
            current_Purchase_date = data.data.purchase_date;
            ConsumableForm.frmEl.purchase_date.datepicker("update", current_Purchase_date);
            if(data.data.currency != "" && data.data.currency != null) {
                ConsumableForm.frmEl.currency.val(data.data.currency);
            }
            if (data.data.requestable == 1) {
                ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", true);
            } else {
                ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", false);
            }
            purchase_cost = data.data.purchase_cost;
            ConsumableForm.frmEl.purchase_cost.val(data.data.purchase_cost);
            ConsumableForm.frmEl.notes.val($.trim(data.data.notes));
            ConsumableForm.frmEl.qty.attr("readonly",false).val(data.data.qty);
            // ConsumableForm.frmEl.qty.prop("disabled",false).val(data.data.qty);
            ConsumableForm.frmEl.reorder_limits.val(data.data.reorder_limits);
            ConsumableForm.frmEl.unique_tag.val(data.data.unique_tag);
            ConsumableForm.frmEl.consumable_thresholds.val(data.data.consumable_thresholds);
            if (data.data.thresholds_alerts == 1) {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", true);
            } else {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", false);
            }
            ConsumableForm.frmEl.consumable_thresholds.val(data.data.consumable_thresholds);
            if (data.data.thresholds_alerts == 1) {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", true);
            } else {
                ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", false);
            }
            ConsumableForm.frmEl.threshold_alert_users.trigger("change");
            ConsumableForm.frmEl.threshold_alert_users.empty();
            if (typeof data.dropdown == "object" && Array.isArray(data.dropdown.thresholdUserEmails) && data.dropdown.thresholdUserEmails.length > 0) {            
                data.dropdown.thresholdUserEmails.forEach(user => {
                    if (user.text) { // Avoid adding null values
                        ConsumableForm.frmEl.threshold_alert_users.append(
                            new Option(user.text, user.id, true, true)
                        );
                    }
                });
                ConsumableForm.frmEl.threshold_alert_users.trigger("change");
            }

            var option = new Option( data.dropdown.company.text, data.dropdown.company.id, true, true);
            ConsumableForm.frmEl.company_id.append(option).trigger("change");
            // ConsumableForm.frmEl.category_id.trigger("change");
            ConsumableForm.frmEl.manufacturer_id.trigger("change");
            ConsumableForm.frmEl.currency.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.department == "object" && data.dropdown.department != null) {
                ConsumableForm.frmEl.department_id.append(new Option(data.dropdown.department.text, data.dropdown.department.id, true, true)).trigger("change");
            }
            if (typeof data.dropdown == "object" && typeof data.dropdown.internal_place == "object" && data.dropdown.internal_place != null) {
                t.internal_places.push(data.dropdown.internal_place.id);
            }
            ConsumableForm.frmEl.internal_place.trigger("change");

            if (typeof data.dropdown == "object" && typeof data.dropdown.supplier == "object" && data.dropdown.supplier != null) {
                ConsumableForm.frmEl.supplier.append(new Option(data.dropdown.supplier.text, data.dropdown.supplier.id, true, true));
            }
            ConsumableForm.frmEl.supplier.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.location == "object" && data.dropdown.location != null) {
                ConsumableForm.frmEl.location_id.append(new Option(data.dropdown.location.text, data.dropdown.location.id, true, true)).trigger("change");
            }
            if (typeof data.dropdown == "object" && typeof data.dropdown.manufacturer == "object" && data.dropdown.manufacturer != null) {
                ConsumableForm.frmEl.manufacturer_id.append(new Option(data.dropdown.manufacturer.text, data.dropdown.manufacturer.id, true, true)).trigger("change");
            }
            if (typeof data.dropdown == "object" && typeof data.dropdown.unit == "object" && data.dropdown.unit != null) {
                ConsumableForm.frmEl.unit.append(new Option(data.dropdown.unit.text, data.dropdown.unit.id, true, true));
            }
            ConsumableForm.frmEl.unit.trigger("change");
            if (typeof data.dropdown == "object" && typeof data.dropdown.consumable == "object" && data.dropdown.consumable != null) {
                ConsumableForm.frmEl.category_id.attr('data-noLoadField', true);
                ConsumableForm.frmEl.category_id.append(new Option(data.dropdown.consumable.text, data.dropdown.consumable.id, true, true));
                ConsumableForm.frmEl.category_id.attr('data-noLoadField', false);
                ConsumableForm.frmEl.category_id.trigger("change");
            }
            clearCustomFields();
            if (typeof data.custom_fields == "object" && typeof data.custom_fields.html != "") {
                fillCustomFields(data.custom_fields);
            }
            if(data.data.image != null) {
                if ((action == "clone")) {
                    // ConsumableForm.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + data.data.image);
                    // ConsumableForm.frmEl.imgviewcover.removeClass("hide");
                    // if (action == "clone") {
                    //     ConsumableForm.frmEl.clone_img.val(data.data.image);
                    // }
                    showImage(data.data.image, id, true);
                    $('#preview-image').show();
                    ConsumableForm.frmEl.clone_img.val(data.data.image);
                }
            } else {
                // ConsumableForm.frmEl.imgview.attr("src", "");
                // ConsumableForm.frmEl.imgviewcover.addClass("hide");
                ConsumableForm.frmEl.images_list.html("<li class='list-group-item'>No files uploaded</li>");
            }
        });
        return true;
    }

    clearCustomFields = function () {
        ConsumableForm.frm.find(".custom-field-row").remove();
    }

    fillCustomFields = function (data) {
        if (typeof data.html == "undefined") {
            return;
        }
        clearCustomFields();
        ConsumableForm.frmEl.customFieldsPrvEl.after(data.html);
        if (data.required_fields.length > 0) {
            $.each(data.required_fields, function (i, d) {
                ConsumableForm.frm.find("#" + d).rules("add", { required: true });
            });
        }
        $.each(data.all_fields, function (i, d) {
            ConsumableForm.frm.find("#" + d).rules("add", { remarks: false });
        });
        $(".cf-select2").each(function () {
            var $field = $(this);
            var fieldId = $field.attr("id");
        
            if (fieldId) {       
                $field.select2($.extend({}, select2Opts, { dropdownParent: $field.parent() }));
                if ($field.hasClass("custFieldUser")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getUser',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_User,
                        templateSelection: function(data, container) {
                            let user = (data.text || '').trim();
                            $(container).attr('title', user);
                            return user.length > 50 ? user.substring(0, 50) + '...' : user;
                        },
                        templateResult: function(data) {
                            if(typeof data.loading != "undefined" && data.loading) {
                                return $("<div>" + data.text + "</div>");
                            }

                            var name = CheckOut.safeDisplayValue(data.text, "-");
                            var imageUrl = CheckOut.safeDisplayValue(data.img_path, "");
                            var avatarHtml = CheckOut.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");
                            return $(
                                '<div class="d-flex align-items-center gap-2">' +
                                    avatarHtml +
                                    '<span>' + CheckOut.escapeHtml(name) + '</span>' +
                                '</div>'
                            );
                        }
                    }));
                }
        
                if ($field.hasClass("custFieldLocation")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getLocation',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_location,
                        templateSelection: function(data, container) {
                            let location = (data.text || '').trim();
                            $(container).attr('title', location);
                            return location.length > 50 ? location.substring(0, 50) + '...' : location;
                        }
                    }));
                }

                if ($field.hasClass("custFieldDevice")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getDevice',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_device,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        },
                        templateResult: function(data, container) {
                            if(typeof data.loading != "undefined" && data.loading) {
                                return $("<div>" + data.text + "</div>");
                            }
                            
                            a = `<div class="so-t">
                                <svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' viewBox='0 0 24 24'>
                                    <path d='M0 0h24v24H0z' fill='none' />
                                    <path fill='currentColor' d='M5.5 7A1.5 1.5 0 0 1 4 5.5A1.5 1.5 0 0 1 5.5 4A1.5 1.5 0 0 1 7 5.5A1.5 1.5 0 0 1 5.5 7m15.91 4.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.11 0-2 .89-2 2v7c0 .55.22 1.05.59 1.41l8.99 9c.37.36.87.59 1.42.59s1.05-.23 1.41-.59l7-7c.37-.36.59-.86.59-1.41c0-.56-.23-1.06-.59-1.42' />
                                </svg>
                                ${data.asset_tag}
                            </div>`;

                            if (data.asset_name != null) {
                                a += `<div class="so-t">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                                <path d="M0 0h24v24H0z" fill="none" />
                                                <path fill="currentColor" d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2zM4 6h16v10H4z" />
                                            </svg>
                                            ${data.asset_name}
                                        </div>`;
                            }

                            a += `
                                <div class="so-m">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor" d="M12.588 6.086q.22-.222.22-.549t-.222-.547t-.549-.22t-.548.22t-.22.55t.222.547t.549.22t.548-.22M6 22V2h12.077v4.83H19v3.686h-.923V22zm1-1h10.077V3H7zm0 0V3z" />
                                    </svg>
                                    ${data.name} ${data.modelno}
                                </div>
                                <div class="so-t">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                                        <path d="M0 0h24v24H0z" fill="none" />
                                        <path fill="currentColor" d="M1 21v-5h2v3h3v2zm17 0v-2h3v-3h2v5zM4 18V6h2v12zm3 0V6h1v12zm3 0V6h2v12zm3 0V6h3v12zm4 0V6h1v12zm2 0V6h1v12zM1 8V3h5v2H3v3zm20 0V5h-3V3h5v5z" />
                                    </svg>
                                    ${data.serial}
                                </div>`;

                            return $("<div>" + a + "</div>");
                        }
                    }));
                }

                if ($field.hasClass("custFieldModels")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getModel',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_model,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldSupplier")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getSupplier',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_Supplier,
                        templateSelection: function(data, container) {
                            let supplier = (data.text || '').trim();
                            $(container).attr('title', supplier);
                            return supplier.length > 50 ? supplier.substring(0, 50) + '...' : supplier;
                        }
                    }));
                }

                if ($field.hasClass("custFieldProjects")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getProject',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_project,
                        templateSelection: function(data, container) {
                            let project = (data.text || '').trim();
                            $(container).attr('title', project);
                            return project.length > 50 ? project.substring(0, 50) + '...' : project;
                        }
                    }));
                }

                if ($field.hasClass("custFieldInternalPlace")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.ajaxGetInternalPlace,
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_internal_place,
                        templateSelection: function(data, container) {
                            let internalPlace = (data.text || '').trim();
                            $(container).attr('title', internalPlace);
                            return internalPlace.length > 50 ? internalPlace.substring(0, 50) + '...' : internalPlace;
                        }
                    }));
                }

                if ($field.hasClass("custFieldContract")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            // url: t.config.getLeaseByAjax,
                            url:  t.config.getPredefinedDropdownByQuery+'/'+'getContract',
                            dataType: "json",
                            data: function (p) {
                                return { 
                                    search: p.term,
                                    page: p.page || 1,
                                    company_id: function () {
                                        return ConsumableForm.frmEl.company_id.val();
                                    }
                                };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_contract,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldComponent")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getComponent',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_component,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldLicense")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getLicense',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_license,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldTasks")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getTask',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_task,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldChangeManagement")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getRecord',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_change_management,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }
   
                if ($field.hasClass("custFieldManufacturers")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getManufacture',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_manufacturer,
                        templateSelection: function(data, container) {
                            let manufacturer = (data.text || '').trim();
                            $(container).attr('title', manufacturer);
                            return manufacturer.length > 50 ? manufacturer.substring(0, 50) + '...' : manufacturer;
                        }
                    }));
                }
                
                if ($field.hasClass("custFieldTickets")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getTicket',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_ticket,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldRequest")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getTicketProcureRequest',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_ticket_procure_request,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }

                if ($field.hasClass("custFieldPurchase")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.getPredefinedDropdownByQuery+'/'+'getPurchase',
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.Select_the_Purchase_Invoice,
                        templateSelection: function(data, container) {
                            let purchase = (data.text || '').trim();
                            $(container).attr('title', purchase);
                            return purchase.length > 50 ? purchase.substring(0, 50) + '...' : purchase;
                        }
                    }));
                }
        
                if ($field.hasClass("custFieldDept")) {
                    $field.select2($.extend({}, select2Opts, {
                        dropdownParent: $field.parent(),
                        ajax: {
                            url: t.config.url.getAssetDepartments,
                            dataType: "json",
                            data: function (p) {
                                return { search: p.term, page: p.page || 1 };
                            },
                            delay: 300
                        },
                        allowClear: true,
                        placeholder: config.translations.select_the_department,
                        templateSelection: function(data, container) {
                            $(container).attr('title', data.text);
                            return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                        }
                    }));
                }
            } else {
                console.warn("Select2 field without ID found. Skipping initialization.");
            }
        });
    }

    getCustomFields = function (e) {
        e.preventDefault();
        if ( ConsumableForm.frmEl.category_id.attr('data-noLoadField') == "true") {
            return;
        }

        clearCustomFields();
        if (typeof config.custom_fields == "object" && typeof config.custom_fields.html != "") {
            fillCustomFields(config.custom_fields);
        }
        var category_id = parseInt( ConsumableForm.frmEl.category_id.val());
        if (category_id < 1 || isNaN(category_id)) {
            return;
        }
        // var type_id = ConsumableForm.frmEl.id.val();
        // var http = $.get(config.url.getCustomFieldsByCategory + "/" + category_id + "/" + 'consumable' + "/" + type_id);
        // http.done(function (data) {
        //     if (typeof data == "object") {
        //         if (data.status == "success") {
        //             fillCustomFields(data);
        //         }
        //     }
        // });
        // http.fail(function () {
        //     alert(config.translations.something_went_wrong);
        // });
        // http.always(function () {
        //     httpCall = true;
        // });
    };

    ConsumableForm.frmEl.category_id .on("change", $.proxy(getCustomFields));

    t.Categoryfill = function() {
        if (typeof t.config.categories != "undefined" ) {
            ConsumableForm.frmEl.category_id.empty();
        $.each(t.config.categories, function(i, v) {
            // console.log(v);
            ConsumableForm.frmEl.category_id.append(new Option(v.text, v.id));
        });
        ConsumableForm.frmEl.category_id.closest(".row").show();
        ConsumableForm.frmEl.category_id.trigger("change");
        }
    };

    t.Unitfill = function() {
        if (typeof t.config.units != "undefined" ) {
            ConsumableForm.frmEl.unit.empty();
        $.each(t.config.units, function(i, v) {
            ConsumableForm.frmEl.unit.append(new Option(v.text, v.id));
        });
        ConsumableForm.frmEl.unit.closest(".row").show();
        ConsumableForm.frmEl.unit.trigger("change");
        }
    };

    $("#consumablemodal").on('show.bs.modal', function(e) {
        $('label.msg').remove();
        current_Purchase_date = "";
        order_no = currency_default = purchase_cost = "";
        var action = $(e.relatedTarget).attr('action');
        ConsumableForm.frmValidator.resetForm();
        ConsumableForm.frmEl.images_list.empty();
        if(action == 'add' || action == 'clone') {
            // t.Categoryfill();
            // t.Unitfill();
            ConsumableForm.frmEl.company_id.prop("disabled", false);
            $('#modalHead').html('Add');
            $('#submitBtn').html(t.config.translations.add);
            $('#ConsumableForm').attr('action', t.config.url.add);
            $('#ConsumableForm')[0].reset();
            $('#ConsumableForm').find("#currency").val(t.config.default_currency_format);
            $('#ConsumableForm').find("input[name='unique_tag']").attr('readonly', false);
            ConsumableForm.frmEl.qty.removeAttr("readonly");
            $('#ConsumableForm').find("input[name='unique_tag']").prop('disabled', false);
            // ConsumableForm.frmEl.qty.prop("disabled", false);
            clearCustomFields();
            if (typeof config.custom_fields == "object" && typeof config.custom_fields.html != "") {
                fillCustomFields(config.custom_fields);
            }
            if(action == 'clone') {
                populateWithEntity($(e.relatedTarget).attr('data-id'),action);
            }

            ConsumableForm.frmEl.id.val(null);
            ConsumableForm.frmEl.unit.empty().trigger("change");
            ConsumableForm.frmEl.company_id.trigger("change");
            // ConsumableForm.frmEl.category_id.trigger("change");
            // ConsumableForm.frmEl.manufacturer_id.trigger("change");
            ConsumableForm.frmEl.location_id.empty().trigger("change");
            ConsumableForm.frmEl.internal_place.empty().trigger("change");
            ConsumableForm.frmEl.currency.trigger("change");
            ConsumableForm.frmEl.invoice_id.empty().trigger("change");
            ConsumableForm.frmEl.department_id.empty().trigger("change");
            ConsumableForm.frmEl.supplier.empty().trigger("change");
            ConsumableForm.frmEl.threshold_alert_users.empty().trigger("change");
            $('#preview-image').hide();

            // if (typeof data.dropdown == "object" && typeof data.dropdown.consumable == "object") {
            //     ConsumableForm.frmEl.category_id.attr('data-noLoadField', true);
            //     ConsumableForm.frmEl.category_id.append(new Option(data.dropdown.consumable.text, data.dropdown.consumable.id, true, true)).trigger("change");
            //     ConsumableForm.frmEl.category_id.attr('data-noLoadField', false);
            // }
        }
        else if(action == 'edit') {
            // t.Categoryfill();
            /*clearCustomFields();
            if (typeof config.custom_fields == "object" && typeof config.custom_fields.html != "") {
                fillCustomFields(config.custom_fields);
            }*/
            count = 0;
            var entityId = $(e.relatedTarget).attr('data-id');
            $('#modalHead').html(t.config.translations.edit);
            $('#ConsumableForm').attr('action',t.config.url.update + '/' + entityId);
            $('#submitBtn').html(t.config.translations.edit);
            $('#ConsumableForm').find("#currency").val(t.config.default_currency_format);
            $('#ConsumableForm').find("input[name='unique_tag']").attr('readonly', true);
            $('#ConsumableForm').find("input[name='unique_tag']").prop('disabled', true);

            //populate values
            $.ajax({
                url: t.config.url.edit + '/' + entityId,
                data: {},
                async: false,
                dataType: "json",
                success: function(data) {
                    if(typeof data.status != "undefined"){
                        e.preventDefault();
                        if (data.status == 'error') {
                            sweetAlert('center', 'error', data);
                            // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>' });
                        }
                        return false;
                    }
                    ConsumableForm.frmEl.id.val(entityId);
                    var option = new Option( data.dropdown.company.text, data.dropdown.company.id, true, true);
                    ConsumableForm.frmEl.company_id.val(data.data.company_id);
                    ConsumableForm.frmEl.company_id.append(option).trigger("change");
                    if(data.data.totalAssignedQty != 0) {
                        ConsumableForm.frmEl.company_id.prop("disabled", true);
                        $('<input>').attr({
                            type: 'hidden',
                            id: 'company_id_hidden',
                            name: 'company_id',
                            value: data.data.company_id
                        }).appendTo(ConsumableForm.frm);
                    } else {
                        ConsumableForm.frmEl.company_id.prop("disabled", false);
                    }
                    ConsumableForm.frmEl.name.val(data.data.name);
                    ConsumableForm.frmEl.image.val('');
                    if (data.data.image) {
                        showImage(data.data.image, entityId);
                        $('#preview-image').show();
                    } else {
                        ConsumableForm.frmEl.images_list.html("<li class='list-group-item'>No files uploaded</li>");
                    }
                    // ConsumableForm.frmEl.manufacturer_id.val(data.data.manufacturer_id);
                    ConsumableForm.frmEl.internal_place.val(data.data.internal_place);
                    order_no = data.data.order_number;
                    ConsumableForm.frmEl.order_number.val(order_no);
                    purchase_cost = data.data.purchase_cost;
                    ConsumableForm.frmEl.purchase_cost.val(purchase_cost);
                    currency_default = data.data.currency;
                    ConsumableForm.frmEl.currency.val(currency_default).trigger("change");
                    current_Purchase_date = data.data.purchase_date;
                    ConsumableForm.frmEl.purchase_date.datepicker("update", current_Purchase_date);
                    if(data.data.currency != "" && data.data.currency != null) {
                        ConsumableForm.frmEl.currency.val(data.data.currency);
                    }
                    ConsumableForm.frmEl.purchase_cost.val(data.data.purchase_cost);
                    ConsumableForm.frmEl.notes.val($.trim(data.data.notes));
                    ConsumableForm.frmEl.qty.attr("readonly",true).val(data.data.qty);
                    // ConsumableForm.frmEl.qty.prop("disabled",true).val(data.data.qty);
                    ConsumableForm.frmEl.reorder_limits.val(data.data.reorder_limits);
                    ConsumableForm.frmEl.unique_tag.val(data.data.unique_tag);
                    ConsumableForm.frmEl.consumable_thresholds.val(data.data.consumable_thresholds);
                    if (data.data.thresholds_alerts == 1) {
                        ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", true);
                    } else {
                        ConsumableForm.frm.find("input[name='thresholds_alerts']").prop("checked", false);
                    }
                    ConsumableForm.frmEl.threshold_alert_users.trigger("change");
                    ConsumableForm.frmEl.threshold_alert_users.empty();
                    if (typeof data.dropdown == "object" && Array.isArray(data.dropdown.thresholdUserEmails) && data.dropdown.thresholdUserEmails.length > 0) {
                        data.dropdown.thresholdUserEmails.forEach(user => {
                            if (user.text) { 
                                ConsumableForm.frmEl.threshold_alert_users.append(
                                    new Option(user.text, user.id, true, true)
                                );
                            }
                        });
                        ConsumableForm.frmEl.threshold_alert_users.trigger("change");
                    }
                    // ConsumableForm.frmEl.manufacturer_id.trigger("change");
                    ConsumableForm.frmEl.currency.trigger("change");
                    if (typeof data.dropdown == "object" && typeof data.dropdown.department == "object" && data.dropdown.department != null) {
                        ConsumableForm.frmEl.department_id.append(new Option(data.dropdown.department.text, data.dropdown.department.id, true, true)).trigger("change");
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.location == "object" && data.dropdown.location != null) {
                        ConsumableForm.frmEl.location_id.append(new Option(data.dropdown.location.text, data.dropdown.location.id, true, true)).trigger("change");
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.internal_place == "object" && data.dropdown.internal_place != null) {
                        t.internal_places.push(data.dropdown.internal_place.id);
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.manufacturer == "object" && data.dropdown.manufacturer != null) {
                        ConsumableForm.frmEl.manufacturer_id.append(new Option(data.dropdown.manufacturer.text, data.dropdown.manufacturer.id, true, true)).trigger("change");
                    }
                    ConsumableForm.frmEl.internal_place.trigger("change");
                    if (data.dropdown.supplier != null) {
                        current_supplier_name  = data.dropdown.supplier.text;
                        current_supplier_id = data.dropdown.supplier.id;
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.supplier == "object" && data.dropdown.supplier != null) {
                        ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true));
                    }
                    ConsumableForm.frmEl.supplier.trigger("change");
                    if (data.data.requestable == 1) {
                        ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", true);
                    } else {
                        ConsumableForm.frm.find("input[name='requestable_consumables']").prop("checked", false);
                    }
                    if (typeof data.dropdown == "object" && typeof data.dropdown.unit == "object" && data.dropdown.unit != null) {
                        ConsumableForm.frmEl.unit.append(new Option(data.dropdown.unit.text, data.dropdown.unit.id, true, true));
                    }
                    ConsumableForm.frmEl.unit.trigger("change");

                    ConsumableForm.frmEl.invoice_id.empty();
                    if (typeof data.dropdown == "object" && typeof data.dropdown.invoice == "object" && data.dropdown.invoice != null) {
                        ConsumableForm.frmEl.invoice_id.append(new Option(data.dropdown.invoice.text, data.dropdown.invoice.id, true, true)).trigger("change");
                    }
                    ConsumableForm.frmEl.invoice_id.trigger("change");
                    if (typeof data.dropdown == "object" && typeof data.dropdown.consumable == "object" && data.dropdown.consumable != null) {
                        ConsumableForm.frmEl.category_id.attr('data-noLoadField', true);
                        ConsumableForm.frmEl.category_id.append(new Option(data.dropdown.consumable.text, data.dropdown.consumable.id, true, true));
                        ConsumableForm.frmEl.category_id.attr('data-noLoadField', false);
                        ConsumableForm.frmEl.category_id.trigger("change");
                    }
                    clearCustomFields();
                    if (typeof data.custom_fields == "object" && typeof data.custom_fields.html != "") {
                        fillCustomFields(data.custom_fields);
                    }
                    if(data.data.image != null) {
                        if ((action == "edit" || action == "clone")) {
                            ConsumableForm.frmEl.imgview.attr("src", t.config.imgviewpath + "/" + data.data.image);
                            ConsumableForm.frmEl.imgviewcover.removeClass("hide");
                            if (action == "clone") {
                                ConsumableForm.frmEl.clone_img.val(data.data.image);
                            }
                        }
                    } else {
                        ConsumableForm.frmEl.imgview.attr("src", "");
                        ConsumableForm.frmEl.imgviewcover.addClass("hide");
                    }
                }
            });
        }
    });

    var select2Opts = {width:"100%"};
    ConsumableForm.frmEl.purchase_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    ConsumableForm.frmEl.company_id.select2($.extend({}, select2Opts, { 
        dropdownParent: ConsumableForm.frmEl.company_id.parent(),
         ajax: {
            url: t.config.url.getCompanyUsers,
            dataType: "json",
            delay: 300,
            data: function (p) { return { search: p.term, page: p.page || 1 }; }
        },
    }));
    ConsumableForm.frmEl.category_id.select2($.extend({}, select2Opts, { dropdownParent: ConsumableForm.frmEl.category_id.parent(),}));
    ConsumableForm.frmEl.manufacturer_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.manufacturer_id.parent(),
        ajax: {
            url: t.config.url.getManufacturerByQuery,
            dataType: "json",
            delay: 300,
            data: function (p) { return { search: p.term, page: p.page || 1 }; }
        },
    }));
    ConsumableForm.frmEl.currency.select2($.extend({}, select2Opts, { dropdownParent: ConsumableForm.frmEl.currency.parent(),}));
    // ConsumableForm.frmEl.company_id.select2(select2Opts);
    // ConsumableForm.frmEl.category_id.select2(select2Opts);
    // ConsumableForm.frmEl.manufacturer_id.select2(select2Opts);
    // ConsumableForm.frmEl.currency.select2(select2Opts);
    ConsumableForm.frmEl.company_id.on("change", function() {
        var companyId = $(this).val();
        
        if (companyId > 0) {
            ConsumableForm.frmEl.location_id.empty().trigger("change");
            ConsumableForm.frmEl.department_id.empty().trigger("change");
            ConsumableForm.frmEl.invoice_id.empty().trigger("change");
            ConsumableForm.frmEl.internal_place.empty().trigger("change");
            t.internal_places = [];
        }
    });
    ConsumableForm.frmEl.supplier.select2($.extend({}, {width:"100%"}, {
        dropdownParent: ConsumableForm.frmEl.supplier.parent(),
        ajax: {
            url: t.config.url.getSupplierByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        placeholder:t.config.translations.Select_the_Supplier
    }));

    ConsumableForm.frmEl.threshold_alert_users.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.threshold_alert_users.parent(),
        ajax: {
            url: config.url.getActivatedUsers,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: function () {
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: "Select The User",
        templateResult: function(s) {
            if(typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }

            var name = CheckOut.safeDisplayValue(s.text, "-");
            var imageUrl = CheckOut.safeDisplayValue(s.img_path, "");
            var avatarHtml = CheckOut.getAvatarHtml(name, imageUrl, "user-list-avatar user-dropdown-image");
            return $(
                '<div class="d-flex align-items-center gap-2">' +
                    avatarHtml +
                    '<span>' + CheckOut.escapeHtml(name) + '</span>' +
                '</div>'
            );
        }
    }));

    ConsumableForm.frmEl.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.department_id.parent(),
        ajax: {
            url: t.config.url.getAssetDepartments,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!ConsumableForm.frmEl.company_id.val()) {
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
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear:true,
        // minimumInputLength: 1,
        placeholder: config.translations.select_the_department,
        language: {
            noResults: function () {
                if(!ConsumableForm.frmEl.company_id.val()){
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        },
    }));
    ConsumableForm.frmEl.location_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.location_id.parent(),
        ajax: {
            url: t.config.getLocationByAjax,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!ConsumableForm.frmEl.company_id.val()) {
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
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear:true,
        // minimumInputLength: 1,
        placeholder: config.translations.select_the_location,
        language: {
            noResults: function () {
                if(!ConsumableForm.frmEl.company_id.val()){
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        },
    })).on("change", function (e) {
        t.getInternalPlace();
    });

    // Internal Place Dropdown Initialization (Single Initialization)
    ConsumableForm.frmEl.internal_place.select2($.extend({}, select2Opts, {
        placeholder: "Select the Internal Place",
        allowClear: true,
        dropdownParent: ConsumableForm.frmEl.internal_place.parent()
    }));
    // Function to Fetch Internal Places
    t.getInternalPlace = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }

        ConsumableForm.frmEl.internal_place.empty().append(new Option(config.translations.select_internal_place, "", true, true));
        ConsumableForm.frmEl.internal_place.trigger("change");
        var location_id = ConsumableForm.frmEl.location_id.val();

        if (location_id > 0) {
            $.get(t.config.getInternalPlaceByAjax + '/' + location_id).done(function (data) {
                if (typeof data == "object" && data.results.length) {
                    $.each(data.results, function (i, v) {
                        if ($.inArray(v.id, t.internal_places) !== -1) {
                            ConsumableForm.frmEl.internal_place.append(new Option(v.text, v.id, true, true));
                        } else {
                            ConsumableForm.frmEl.internal_place.append(new Option(v.text, v.id, false, false));
                        }
                    });

                }
            }).always(function () {
                ConsumableForm.frmEl.internal_place.trigger("change");
                t.internal_places = [];
            });
        }
    };

    ConsumableForm.frmEl.category_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.category_id.parent(),
        ajax: {
            url: t.config.getCategoryByQuery,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    type: 'consumable',
                    all: 'all'
                };
            },
            delay: 300
        },
        allowClear:true,
        //minimumInputLength: 1,
        placeholder: config.translations.select_the_category,
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
        }
    }));
    ConsumableForm.frmEl.unit.select2($.extend({}, {width:"100%"}, {
        dropdownParent: ConsumableForm.frmEl.unit.parent(),
        ajax: {
            url: config.url.getUnitsByAjax,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        allowClear:true,
        placeholder: config.translations.select_the_unit,
    }));
    ConsumableForm.frmEl.invoice_id.select2($.extend({}, select2Opts, {
        dropdownParent: ConsumableForm.frmEl.invoice_id.parent(),
        ajax: {
            url: t.config.url.getInvoiceByAjax,
            dataType: "json",
            transport: function (params, success, failure) {
                if (!ConsumableForm.frmEl.company_id.val()) {
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
                        return ConsumableForm.frmEl.company_id.val();
                    }
                };
            },
            delay: 300
        },
        allowClear:true,
        placeholder:t.config.translations.Select_the_Purchase_Invoice,
        language: {
            noResults: function () {
                if(!ConsumableForm.frmEl.company_id.val()){
                    return "Please select the company then fetch data";
                }
                return "No Data Found";
            }
        },
        templateSelection: function(s,container) {
            if(typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            // $(s.element).attr({'data-invoice_date':s.invoice_date, 'data-name':s.supplier_name, 'data-id':s.supplier_id});
            $(s.element).attr({'data-currency':s.currency,'data-purchase-cost':s.purchase_cost,'data-invoice_date': s.invoice_date, 'data-name':s.supplier_name, 'data-id':s.supplier_id,'data-order-number': s.order_number,'data-po-number': s.po_number});
            return s.text;
        }
    }))
    ConsumableForm.frmEl.invoice_id.on("change", function(e) {
        if (count >= 2 || ConsumableForm.frmEl.invoice_id.val() == null) {
            if (ConsumableForm.frmEl.invoice_id.val() == null) {
                count = count+2;
                if (current_Purchase_date != null) {
                    purchasedate = current_Purchase_date.split('-').join('/')
                    ConsumableForm.frmEl.purchase_date.val(purchasedate);
                } else {
                    ConsumableForm.frmEl.purchase_date.val(null);
                }
                if (current_supplier_name != "" && current_supplier_id != "") {
                    ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
                } else {
                    ConsumableForm.frmEl.supplier.empty('').trigger('change');
                }
                if (purchase_cost != null && purchase_cost != null) {
                    ConsumableForm.frmEl.purchase_cost.val(purchase_cost);
                } else {
                    ConsumableForm.frmEl.purchase_cost.val('');
                }
            } else {
                t.getPurchaseDate();
            }
        }
        count++;
    });

    t.getPurchaseDate = function(e) {
        //this part is added to handle re-selection from Select2 dropdown
        var selectedOption = ConsumableForm.frmEl.invoice_id.find(':selected');   
        if (!selectedOption.attr('data-invoice_date')) {
            // Use stored default values for options without data attributes
            if (current_Purchase_date != null) {
                purchasedate = current_Purchase_date.split('-').join('/');
                ConsumableForm.frmEl.purchase_date.val(purchasedate);
            } else {
                ConsumableForm.frmEl.purchase_date.val('');
            }
            ConsumableForm.frmEl.supplier.empty();
            if (current_supplier_name && current_supplier_id) {
                ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
            }
            if (order_no != null) {
                ConsumableForm.frm.find("input[name='order_number']").val(order_no);
            } else {
                ConsumableForm.frm.find("input[name='order_number']").val('');
            }
            //for select2 Purchase Reference dropdown error fix
            if (purchase_cost != null) {
                ConsumableForm.frm.find("input[name='purchase_cost']").val(purchase_cost);
            } else {
                ConsumableForm.frm.find("input[name='purchase_cost']").val('');
            }
            if (currency_default != null) {
                ConsumableForm.frmEl.currency.val(currency_default).trigger('change');
            } else {
                ConsumableForm.frmEl.currency.val('').trigger('change');
            }
            return;
        }
        //end this part is added to handle re-selection from Select2 dropdown
        var date = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-invoice_date');
        ConsumableForm.frmEl.purchase_date.val(date);
        var supplier_name = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-name');
        var supplier_id = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-id');
        ConsumableForm.frmEl.supplier.append(new Option(supplier_name, supplier_id, true, true)).trigger("change");
        if (supplier_name == undefined  && supplier_id == undefined && t.action == "edit") {
            ConsumableForm.frmEl.supplier.append(new Option(current_supplier_name, current_supplier_id, true, true)).trigger("change");
        }
        var order_number = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-po-number');
        ConsumableForm.frmEl.order_number.val(order_number);
        var purchase_cost_value = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-purchase-cost');
        ConsumableForm.frmEl.purchase_cost.val(purchase_cost_value || '0.00');
        var purchase_currency = ConsumableForm.frmEl.invoice_id.find(':selected').attr('data-currency');
        ConsumableForm.frmEl.currency.val(purchase_currency).trigger("change");
    }

    $(document).on('click', '.dtActDel', function(e) {
        e.preventDefault();
        if( $(this).attr("data-indent") != "consumable" ) {
            return false;
        }
        url = t.config.url.delete + "/" + $(this).attr('data-id');

        // vex.dialog.confirm({
        //     message: t.config.translations.are_you_Want_delete,
        //     callback: function (value) {
        //         if (value) {
        //             $.getJSON(url, function(data) {
        //                 if (data.status == 'error') {
        //                     vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Error!!</h3><p>' + data.msg + '</p></div>'});
        //                 }
        //                 if (data.status == 'success') {
        //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
        //                         window.location.href =  "{{ url('consumables') }}";
        //                 }
        //                 if (typeof dtblRef != "undefined")
        //                     dtblRef.dTbl.ajax.reload();

        //             });
        //         }
        //     }
        // });

        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(t.config.translations.are_you_Want_delete,'warning', url, config.url.add, data,'url_yes');
    });

    consumabletabs.find('a[data-bs-toggle="tab"]').on('hide.bs.tab', function (e) {
        const $modal = $(this).closest('.modal');
        $modal.find('select.select2-hidden-accessible').each(function () {
            $(this).select2('close');
        });
        $modal.find('.modal-body').off('scroll.select2');
    });
}
var ActivityPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find("#activity-tab");
    t.table = t.tab.find("#tblActivity");
    t.show_entries = $("section.content").find('#showSelectActivity');

    t.btn = {};

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        responsive: false,
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        colReorder: true,
        stateSave: true,
        aoColumnDefs: [
            {
                'bSortable': false,
                'aTargets': [0]
            }
        ],
        order: [
            [6, 'desc']
        ],
        colResize: {
            resizeTable: true
        },
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.activity_history_list,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.location = t.config.location_filter;
                d.filters = t.config.other_filters;
                d.consumable_id = t.config.consumable_id;
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].data;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b1-text">${id ?? ''}</span>`;
                },
            },
            { data: 'a.change_type'},
            { data: 'a.unique_tag'},
            { data: 'a.cat_name'},
            { data: 'a.loc_name'},
            { data: 'a.internal_place'},
            { data: 'a.order_number'},
            { data: 'a.purchase_cost'},
            { data: 'a.qty'},
            { data: 'a.created_at' },
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            // $("#tblActivityHistory_filter input").off(".DT");
            // var searchBox  = '<div class="input-group table-search-btns">'
            //     +'<input type="text" class="form-control searchbox plain-search" placeholder="'+config.translations.press_enter_with_Search+'" />'
            //     +'<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="'+config.translations.Search+'"><i class="ps-icon plain-search-icon"></i></span>'
            //     +'<span class="input-group-addon btn-reload" data-toggle="tooltip" data-placement="bottom" data-original-title="'+config.translations.Refresh_List+'"><i class="ps-icon fa fa-refresh"></i></span>'
            //     searchBox +='<span class="input-group-addon btn-visible-content" aria-controls="advance-filters" style="color:gray">';
            //     searchBox +='<span class="" id="columnVisibilityButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class=" ps-icon fa fa-columns"></i></span>';
            //     searchBox +='<span class="dropdown-menu" style="margin:0px -117px" id="columnVisibilityControls"></span></span>';
            //      +'</div>';
            // $("#tblActivityHistory_wrapper").removeClass("form-inline");
            t.table.closest("div").addClass("table-responsive");

            // $(searchBox).insertBefore("#tblActivityHistory_filter");
            // $("#tblActivityHistory_filter").remove();
            // $("#tblActivityHistory_length").find("select").select2();
            $("#activitySearch").on("keyup.DT", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(v).draw();
                }
            });
            buildColumnVisibilityControls(api);
        }
    });

    t.dTbl.on('column-reorder', function(e, settings, details) {
        var api = t.table.DataTable();
        buildColumnVisibilityControls(api);
    });

    function buildColumnVisibilityControls(api) {
        $("#columnVisibilityControls").empty();
        api.columns().every(function(index) {
            var column = this;
            var columnTitle = $(column.header()).text().trim();
            var checkboxHtml = `
                <div class="dropdown-item" style="padding:6px">
                    <label>
                        <input type="checkbox" data-column="${index}" ${column.visible() ? 'checked' : ''}> ${columnTitle}
                    </label>
                </div>`;
            $("#columnVisibilityControls").append(checkboxHtml);
        });
        $('#columnVisibilityControls input[type="checkbox"]').on('change', function() {
            var columnIndex = $(this).data('column');
            var column = api.column(columnIndex);
            column.visible($(this).prop('checked'));
        });
    }

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $("#activitySearch").validate_str_param();
        if(v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
		t.dTbl.search(v).draw();
	};

    t.tab.on("click", ".btn-reload-list-activity", function (e) {
        t.reload(e);
    });

    t.tab.on("click", ".amg-list-searchbar__icon-activity", function (e) {
        t.tableSearch(e);
    });
    // t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
};

var TmpSln = function(config, openpop, requestOngoing) {
    var t = this;
    t.config = config;
    t.openpop = openpop;
    t.requestOngoing = requestOngoing
    t.consumable = t.config.consumable_name;
    t.content = $("#history-tab");
    t.dTblHis = null;

    // calling it here only instead of on tab switch since in this case, the function for checkin from device info page is written inside this. 
    t.userPhase = new UserPhase(t.config, t.openpop);
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if ($(e.target).attr('href') === '#documents-tab') {
            if (!t.documentPhase) {
    t.documentPhase = new DocumentPhase(t.config);
            } else {
                t.documentPhase.reload();
            }
        }

        if ($(e.target).attr('href') === '#activity-tab') {
            if (!t.ActivityPhase) {
    t.ActivityPhase = new ActivityPhase(t.config);
            } else {
                t.ActivityPhase.reload();
            }
        }

        if ($(e.target).attr('href') === '#purchase-history-tab') {
            if (!t.PurchasePhase) {
                t.PurchasePhase = new PurchasePhase(t.config);
            } else {
                t.PurchasePhase.reload();
            }
        }

        if ($(e.target).attr('href') === '#checkouts-tab') {
            if (!t.userPhase) {
                t.userPhase = new UserPhase(t.config, t.openpop);
            } else {
                t.userPhase.reload();
            }
        }

        if ($(e.target).attr('href') === '#history-tab') {
            if (!t.dTblHis) {
                t.dTblHis = $('#tblHistory').DataTable({
        autoWidth: false,
        lengthChange: false,
        pageLength: 10,
        responsive: false,
        // scrollX: true,
        // scrollCollapse: true,
        // fixedColumns: {
        //     rightColumns: 1
        // },
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        aoColumnDefs: [
            {
                'bSortable': false,
                'aTargets': []
            }, 
            {
                targets: [2],
            },
            {
                targets: 1,
                render: function(d) {
                    var a = [];
                    if (d.adminuserid)
                        a.push('<a href="' + baseURL + '/user/info/' + d.adminuserid + '" target="_blank">' + d.adminUserName + '</a>')
                    return a.join("");
                }
            },
        ],
        order: [
            [0, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: config.url.consumableHistory,
            type: "post",
            data: function(d) {
                d._token = config.token;
            }
        },
        columns: [
            { data: 'a.created_at' },
            { data: 'a' },
            { data: 'a.action_type' },
            { data: 'a.target_type' },
            { data: 'a.target_name' },
            { data: 'a.note' },
            { data: 'a.ticket_id'},
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $("#historySearch").on("keyup", function(e) {
                if(e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(v).draw();
                }
            });
        }
                });
            } else {
                t.dTblHis.ajax.reload();
            }
        }

        setTimeout(adjustVisibleTables, 100);
    });

    t.documentUploadPhase = new DocumentUploadPhase($.extend({}, t.config, { tbl: t.documentPhase }));
    t.checkOut = new CheckOut(t.config);
    t.Scrap = new Scrap(t.config);
    t.RevertScrap = new RevertScrap(t.config);
    t.consumableForm = new ConsumableForm(t.config, t.requestOngoing);

    t.show_entries = $("section.content").find('#showSelectHistory');
    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    t.content.on("click", ".black-slide-links a", function(e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function() {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });

    t.reload = function () {
        if (t.dTblHis) {
            t.dTblHis.ajax.reload();
        }
    };

	t.tableSearch = function(e) {
        e.preventDefault();
        if (!t.dTblHis) {
            return;
        }
        var v = $("#historySearch").validate_str_param();
        if(v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
		t.dTblHis.search(v).draw();
	};

    t.show_entries.on('change', function () {
        if (!t.dTblHis) {
            return;
        }

        var value = parseInt($(this).val(), 10);
        t.dTblHis.page.len(value).draw();
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
  
    t.content.on("click", '.amg-list-searchbar__icon-history', $.proxy(t.tableSearch));
    t.content.on("click", '.btn-reload-list-history', $.proxy(t.reload));

    $(document).on('click', '.btnActPrintLabel', function(){
        $('#hismenu').click();
        $("#tblHistory").print({
        	globalStyles: true,
        	mediaPrint: true,
        	stylesheet: null,
        	noPrintSelector: ".no-print",
        	iframe: true,
        	append: null,
        	prepend: null,
        	manuallyCopyFormValues: true,
        	deferred: $.Deferred(),
        	timeout: 750,
        	title: t.consumable + ' History ( Consumable )',
        	doctype: '<!doctype html>'
	    });
    })

    function adjustVisibleTables() {
        $('.tab-pane.active.show table.dataTable:visible').each(function () {
            const dt = $(this).DataTable();
            if (dt && dt.columns && typeof dt.columns.adjust === 'function') {
                dt.columns.adjust();
            }
        });
    }

    // using this for adjusting whenever any api is called for all datatables
    $(document).on('draw.dt', function (e, settings) {
        new $.fn.dataTable.Api(settings).columns.adjust();
    });

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            adjustVisibleTables();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        adjustVisibleTables();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

    t.refreshInfoTab = function() {
        t.infoTab.load(t.config.url.consumable_basic_info + "/" + t.config.consumable_id);
    };

    $(document).on('click', '.del-link', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var deleteUrl = config.url.image_delete + '/' + id;
        var $currentLi = $(this).closest('li');

        Swal.fire({
            title: config.translations.are_you_delete_attachment,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#5bd810",
            cancelButtonColor: "#cc3333",
            confirmButtonText: "Yes, Delete",
            cancelButtonText: "Cancel",
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                if($(this).attr('data-action') == "clone") {
                    $currentLi.fadeOut(300, function () {
                        $(this).remove();
                        
                        if ($('#consumables-image-list').children().length === 0) {
                            $('#consumables-image-list').append("<li class='list-group-item'>No files uploaded</li>");
                        }
                        $('#delete_img').val("1");
                    });
                    return;
                }
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: { _token: config.token },
                    success: function (response) {
                        if (response.status === 'success') {
                            $currentLi.fadeOut(300, function () {
                                $(this).remove();
                                if ($('#consumables-image-list').children().length === 0) {
                                    $('#consumables-image-list').append("<li class='list-group-item'>No files uploaded</li>");
                                }
                            });
                            // sweetAlert('center', 'success', response);
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: response.msg || "Deleted successfully",
                                confirmButtonText: "OK",
                            });

                            $.get(t.config.url.infocontent, function(data) {
                                $('#basic-info-tab').html(data);
                            });
                        } else {
                            // sweetAlert('center', 'error', response);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: response.msg || "Delete failed",
                                confirmButtonText: "OK",
                            });
                        }
                    },
                    error: function () {
                        var data = { msg: config.translations.something_went_wrong };
                        // sweetAlert('center', 'error', data);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: data.msg || "Delete failed",
                            confirmButtonText: "OK",
                        });
                    }
                });
            }
        });
    });
};

var Scrap = function(config, openpop) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.sectionHeader = $("section.content-header");
    t.infoTab = $("section.content").find("#basic-info-tab");

    t.httpCall = true;
    t.httpPostPath = "";
    t.btn = {};

    t.scrap = {};
    t.scrap.mdl = $("section.content").find("#consumable-scrap-mdl");
    t.scrap.mdl.btnSubmit = t.scrap.mdl.find('#btnSubmit');
    t.scrap.mdl.btnClear = t.scrap.mdl.find('#btnClear');
    t.scrap.mdl.frm = t.scrap.mdl.find("#consumable-scrap-mdl-frm");
    t.scrap.mdl.frmEl = {};
    t.scrap.mdl.frmEl.id = t.scrap.mdl.frm.find("#id");
    t.scrap.mdl.frmEl.scrap_qty = t.scrap.mdl.frm.find("#scrap_qty");

    t.scrap.resetFrm = function() {
        t.scrap.mdl.frm.trigger("reset");
        t.scrap.frmValidator.resetForm();
        t.scrap.mdl.frmEl.scrap_qty.val("");
    };

    t.scrapConsumable = function(e) {
        e.preventDefault();
        t.scrap.resetFrm();
        t.scrap.mdl.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.httpPostPathScrap = t.config.url.scrap;
        t.scrap.mdl.frmEl.id.val($(this).attr("data-id"));
        t.scrap.mdl.modal("show");
    };

    t.scrap.frmValidator = t.scrap.mdl.frm.validate({
        onsubmit: false,
        onkeyup: function (element, event) {
            this.element(element);
        },
        errorClass: 'error amg-form-invalid',
        rules: {
            scrap_qty: {
                required: true,
                digits: true
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        },
        highlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').addClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.addClass("amg-form-invalid");
            }
        },
        unhighlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').removeClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.removeClass("amg-form-invalid");
            }
        }
    });

    t.handleScrapSubmit = function(e) {
        e.preventDefault();

        if (t.scrap.frmValidator.form() == false) {
            return false;
        }
        
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.scrap.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPathScrap,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                t.scrap.mdl.btnSubmit.prop('disabled', true).text('Please wait...');
            },
            complete: function() {
                t.scrap.mdl.btnSubmit.prop('disabled', false).text('Save');
            },
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.scrap.mdl.modal("hide");
                    // window.location.href = t.config.url.infourl+'/'+ t.config.consumable_id;
                    $.get(t.config.url.infocontent, function(data) {
                        $('#basic-info-tab').html(data);
                    });                
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': 'Something went wrong. Please check given details are correct',
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.scrap.mdl.btnSubmit.on("click", $.proxy(t.handleScrapSubmit));
    t.infoTab.on("click", ".dtActScrap", $.proxy(t.scrapConsumable));
}


var RevertScrap = function(config, openpop) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.sectionHeader = $("section.content-header");
    t.infoTab = $("section.content").find("#basic-info-tab");

    t.httpCall = true;
    t.httpPostPath = "";
    t.btn = {};

    t.revertScrap = {};
    t.revertScrap.mdl = $("section.content").find("#consumable-revertscrap-mdl");
    t.revertScrap.mdl.btnSubmit = t.revertScrap.mdl.find('#btnSubmit');
    t.revertScrap.mdl.btnClear = t.revertScrap.mdl.find('#btnClear');
    t.revertScrap.mdl.frm = t.revertScrap.mdl.find("#consumable-revertscrap-mdl-frm");
    t.revertScrap.mdl.frmEl = {};
    t.revertScrap.mdl.frmEl.id = t.revertScrap.mdl.frm.find("#id");
    t.revertScrap.mdl.frmEl.scrap_qty = t.revertScrap.mdl.frm.find("#scrap_qty");

    t.revertScrap.resetFrm = function() {
        t.revertScrap.mdl.frm.trigger("reset");
        t.revertScrap.frmValidator.resetForm();
        t.revertScrap.mdl.frmEl.scrap_qty.val("");
    };

    t.revertScrapConsumable = function(e) {
        e.preventDefault();
        t.revertScrap.resetFrm();
        t.revertScrap.mdl.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.httpPostPathScrap = t.config.url.revertScrap;
        t.revertScrap.mdl.frmEl.id.val($(this).attr("data-id"));
        t.revertScrap.mdl.modal("show");
    };

    /* revertScrap */
    t.revertScrap.frmValidator = t.revertScrap.mdl.frm.validate({
        onsubmit: false,
        errorClass: 'error amg-form-invalid',
        onkeyup: function (element, event) {
            this.element(element);
        },
        rules: {
            scrap_qty: {
                required: true,
                digits: true
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.parent());
        },
        highlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').addClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.addClass("amg-form-invalid");
            }
        },
        unhighlight: function(element, errorClass) {
            var $element = $(element);
            $element.closest('.select-div').removeClass(errorClass);
            var group = $element.closest(".input-group");
            if (group.length) {
                group.removeClass("amg-form-invalid");
            }
        }
    });

    t.handleScrapSubmit = function(e) {
        e.preventDefault();

        if (t.revertScrap.frmValidator.form() == false) {
            return false;
        }
        
        if (t.httpCall != true) {
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.revertScrap.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPathScrap,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            beforeSend: function() {
                t.revertScrap.mdl.btnSubmit.prop('disabled', true).text('Please wait...');
            },
            complete: function() {
                t.revertScrap.mdl.btnSubmit.prop('disabled', false).text('Save');
            },
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.revertScrap.mdl.modal("hide");
                    // window.location.href = t.config.url.infourl+'/'+ t.config.consumable_id;
                    $.get(t.config.url.infocontent, function(data) {
                        $('#basic-info-tab').html(data);
                    });
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': 'Something went wrong. Please check given details are correct',
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };


    t.revertScrap.mdl.btnSubmit.on("click", $.proxy(t.handleScrapSubmit));
    t.infoTab.on("click", ".dtActScrapRevert", $.proxy(t.revertScrapConsumable));
}
