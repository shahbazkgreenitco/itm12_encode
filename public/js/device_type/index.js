var DeviceType = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main#mainContent');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#holidaysMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.frm = t.mdl.find('#DeviceType');

    t.frmEl = {};
    t.frmEl.name = t.frm.find('#name');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.showHideButton = t.content.find('.show-hide-columns');
    t.show_entries = t.content.find('#showSelect');

    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        lengthChange: false,
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        order: [
            [1, 'desc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        colResize: {
            resizeTable: true
        },
        responsive: false,
        ajax: {
            url: t.config.url.devicetype,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;
                    let columnName = d.columns[columnIndex].name;
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        columns: [
            {
                data: 'a.id',
                width: "5%",
                name: "id",
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span class="b4-text">${data ?? ''}</span>`;
                },
            },
            {
                data: 'a.name',
                name: 'device_type',
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span class="b4-text">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a.id",
                width: "5%",
                className: "users-col-actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
            },
            {
                data: 'a.updated_at',
                visible: false,
                searchable: false
            }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#tableSearch").off(".DT");
            $("#tableSearch").on("keyup.DT", function (e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.please_enter_valid_search);
                        return false;
                    }
                    api.search(this.value).draw();
                }
            });
            $('.amg-list-searchbar__icon').click(function (e) {
                e.preventDefault();
                var v = $("#tableSearch").validate_str_param();
                if (v === false) {
                    alert(config.translations.please_enter_valid_search);
                    return false;
                }
                api.search(v).draw();
            });

            $('.btn-reload-list').click(function (e) {
                e.preventDefault();
                var v = $("#tableSearch").validate_str_param();
                if (v === false) {
                    return false;
                }
                api.search(v).draw();
            });

            function updateColumnVisibilityControls() {

                let controls = $("#columnVisibilityControls").empty();

                api.columns().every(function () {
                    if (!this.visible()) return;
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;

                    controls.append(`
                        <div class="dropdown-item" style="padding:6px">
                            <label for="${columnId}">
                                <input type="checkbox" id="${columnId}" data-column="${columnIndex}" ${this.visible() ? 'checked' : ''}> ${columnTitle}
                            </label>
                        </div>
                    `);
                });
            }
            updateColumnVisibilityControls();
            $('#columnVisibilityControls').on('change', 'input[type="checkbox"]', function () {
                api.column(+$(this).data('column')).visible(this.checked);
            });
            api.on('column-reorder', () => setTimeout(updateColumnVisibilityControls, 10));
        },
        drawCallback: function() {
            $('[data-bs-toggle="tooltip"]').each(function () {
                bootstrap.Tooltip.getOrCreateInstance(this);
            });
        }
    });

    t.renderActionsCell = function (record, type) {
        var t = this;
        var actionState = t.getRowActionState(record);
        var quickActions = [];
        if (type !== "display") return "";
        if (actionState.canEdit) {
            quickActions.push(t.quickActionButtonHtml(
                (config.translations || {}).edit || "Edit",
                "dtActEdit",
                record.id,
                "edit",
                "open-edit-modal"
            ));
        }
        if (actionState.canDelete) {
            quickActions.push(t.quickActionButtonHtml(
                (config.translations || {}).delete || "Delete",
                "dtActDel",
                record.id,
                "delete",
                "open-delete"
            ));
        }

        if (!quickActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        return [
            '<div class="user-list-actions">',
            quickActions.join(""),
            '</div>'
        ].join("");
    }

    t.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass) {
        return [
            '<button type="button" class="user-list-action-btn ', t.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', t.escapeHtml(id), '" title="', t.escapeHtml(label), '" data-bs-toggle="tooltip" data-bs-title="', t.escapeHtml(label) ,'" aria-label="', t.escapeHtml(label), '">',
            t.getIcon(iconKey),
            '</button>'
        ].join("");
    }

    t.getRowActionState = function () {
        return {
            canEdit: t.hasPermission("DeviceTypeEdit"),
            canDelete: t.hasPermission("DeviceTypeDelete"),
        };
    }

    t.hasPermission = function (name) {
        return Array.isArray(t.config.permissions) && t.config.permissions.indexOf(name) !== -1;
    }

    t.escapeHtml = function (value) {
        return String(value === null || value === undefined ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    }

    t.getIcon = function (key) {
        return (iconsList.icons && iconsList.icons[key]) ? iconsList.icons[key]() : "";
    }

    var iconsList = {
        icons: {
            edit: function () {
                return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
            },
            delete: function () {
                return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
            }
        }
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.cache_filter_values = function () {
        var v = $("#tableSearch").validate_str_param();
        t.config.search = v;
        var jobj = { "search": t.config.search };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    }
    t.createDeviceType = function (e) {
        e.preventDefault();
        t.frmEl.name.val('');
        t.mdl.modal("show");
        t.mdltitle.text(t.config.translations.add_device);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        t.resetFrm();
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var frmData = new FormData;
        frmData.append('_token', t.config.token);
        frmData.append('name', t.frmEl.name.val());
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function () {
                t.btn.submit.prop('disabled', true); // disable button
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            vex.dialog.alert(config.translations.something_went_wrong_details);
        });
        http.always(function () {
            t.btn.submit.prop('disabled', false);
        });
    };

    t.loadForm = function (obj) {
        t.resetFrm();
        t.frmEl.name.val(obj.name);
    };

    t.resetFrm = function () {
        t.frmEl.name.val('');
        t.frm.find('.input-group').removeClass('error valid amg-form-invalid');
        t.frm.find('.field-error').empty();
        t.frmValidator.resetForm();
    };

    t.editDeviceType = function (e) {
        e.preventDefault();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + projectId;
        var http = $.get(t.config.url.get + "/" + projectId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text(t.config.translations.edit_device);
                    t.btn.submit.text(config.translations.save_changes);
                    t.loadForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            alert(config.translations.something_went_wrong_details);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteDeviceType = function (e) {
        e.preventDefault();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + projectId;
        var data = {
            'msg': config.translations.something_went_wrong_some_time,
        };
        sweetAlerts(config.translations.confirm_delete, 'warning', t.httpPostPath, t, data, 'reload');

    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        validClass: "amg-form-invalid",
        rules: {
            name: {
                required: true,
                maxlength: 200,
                clean_text_only: true,
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).closest('.input-group').addClass(validClass);
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.input-group').removeClass(validClass);
            $(element).closest('.input-group').find('.field-error').empty();
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        }
    });

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    }

    t.showHideButton.on("click", (e) => t.showHide(e));

    t.showHide = function (e) {
        e.preventDefault();
        $('#columnVisibilityControls').toggle();
    };

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
            $('#columnVisibilityControls').hide();
        }
    });

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTbl.page.len(value).draw();
    });

    var select2Opts = { width: "100%" };
    t.content.on('click', '.open-add-modal', $.proxy(t.createDeviceType));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editDeviceType));
    t.content.on('click', '.open-delete', $.proxy(t.deleteDeviceType));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.btn-devicetype-export', $.proxy(t.export));

    t.dTbl.ajax.reload();
    
    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            t.dTbl.columns.adjust();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        t.dTbl.columns.adjust();
                    }, 300);
                }
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
};