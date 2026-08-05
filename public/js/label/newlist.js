var LabelApp = function (config) {
    var t = this;

    t.config = config;
    t.table = $("#mytable");
    t.content = $("#status-labels-list-wrapper");

    t.modal = $("#leasemd1");
    t.form = $("#leaseform");
    t.mdl = {};
    t.mdl.description = $('#remarksModal');
    t.mdl.description.body = t.mdl.description.find('#text-data');

    t.dTable = null;
    t.isEdit = false;
    t.submitUrl = "";

    let resizeTimer;
    let sidebarTimer;
    const sidebar = document.getElementById('expanded-asset');

    t.frm = t.modal.find('#leaseform');

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            name: {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            $(element).closest('.input-group').addClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).closest('.input-group').removeClass(errorClass);
        },
    });

    t.btn = {
        reload: ".btn-reload-list",
        add: ".open-add-modal",
        search: "#tableSearch",
        search_icon: ".amg-list-searchbar__icon",
        showcolumns: ".show-hide-columns",
        showColumnsInput: '#columnVisibilityControls input[type="checkbox"]'
    };

    t.init();

    $(document).on('click', '.read-more', function (e) {
        e.preventDefault();
        var fullText = $(this).data('full-text');
        $(t.mdl.description.body).html(fullText);
        $(t.mdl.description).modal("show");
    });

    $(window).on('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            t.dTable.columns.adjust();
        }, 150);
    });

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            for (const mutation of mutations) {
                if (mutation.attributeName === 'class') {
                    clearTimeout(sidebarTimer);
                    sidebarTimer = setTimeout(function () {
                        t.dTable.columns.adjust();
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

LabelApp.prototype.init = function () {
    var t = this;

    t.mapElements();
    t.initSelect2();

    setTimeout(function () {
        t.initDataTable();
    }, 50);

    t.bindEvents();
};

LabelApp.prototype.mapElements = function () {
    var t = this;

    t.frmEl = {
        status_label: $("#status_label"),
        status_type: $("#status_type"),
        show_entries: $('#showSelect'),
        notes: $("#notes"),
    };

    t.btn.submit = $("#btnSubmit");
};

LabelApp.prototype.initSelect2 = function () {
    var t = this;
    var select2Opts = { width: "88%" };

    t.frmEl.status_type.select2({
        ...select2Opts,
        dropdownParent: t.frmEl.status_type.parent(),
        placeholder: config.translations.select_label,
        allowClear: true,
        data: [
            { id: 1, text: config.translations.deployable },
            { id: 2, text: config.translations.non_deployable },
            { id: 3, text: config.translations.archived },
            { id: 4, text: config.translations.pending }
        ]
    });

    t.frmEl.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });
};

LabelApp.prototype.initDataTable = function () {
    var t = this;

    t.dTable = t.table.DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        // stateSave: true,
        lengthChange: false,
        pageLength: 10,
        // dom: "lrtip",
        dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        scrollX: true,
        scrollCollapse: true,
        fixedColumns: {
            rightColumns: 1
        },
        language: {
            paginate: {
                previous: t.config.translations.previous || "Previous",
                next: t.config.translations.next || "Next"
            },
            info: t.config.translations.showing_entries || "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: t.config.translations.no_entries || "Showing 0 to 0 of 0 entries",
            infoFiltered: t.config.translations.filtered_from || "(filtered from _MAX_ total entries)",
            zeroRecords: t.config.translations.no_matching_records || "No matching records found",
            emptyTable: t.config.translations.no_data || "No data available in table",
            search: t.config.translations.search || "Search:",
            lengthMenu: t.config.translations.length_menu || "Show _MENU_ entries"
        },
        drawCallback: function () {
            t.table.find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        ajax: {
            url: t.config.url.list,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;

                // doing like this for sorting passing keys in the request.
                if (d.order && d.order.length) {
                    let orderColIndex = parseInt(d.order[0].column, 10);
                    let orderDir = d.order[0].dir;

                    const columnsMap = {
                        0: 'a.id',
                        1: 'a.name',
                        2: 'a.status_types',
                        3: 'a.notes',
                        5: 'a.updated_at'
                    };

                    if (columnsMap[orderColIndex]) {
                        d.sorted_column_name = columnsMap[orderColIndex];
                        d.sorted_direction = orderDir;
                    } else {
                        d.sorted_column_name = '';
                        d.sorted_direction = '';
                    }
                } else {
                    d.sorted_column_name = '';
                    d.sorted_direction = '';
                }
            },
        },

        // doing like this since the search value should not persist even after refreshing the page.
        stateLoadCallback: function (settings) {
            let state = JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance + '_' + settings.sInstance));
            if (state) {
                state.search = { search: "", smart: true };
            }
            return state;
        },
        order: [[5, 'desc']],
        columns: [
            // { data: "a.id" },
            {
                data: null,
                render: function (data, type, row, meta) {
                    let id = meta.row + meta.settings._iDisplayStart + 1;
                    return `<span>${id ?? ''}</span>`;
                },
            },
            {
                data: "a.name",
                render: function (data, type, row) {
                    if (type === 'display') {
                        return `<span>${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a.status_types",
                render: function (data, type, row) {
                    let badgeClass = 'role-badge-user';

                    switch (data) {
                        case 'Undeployable':
                            badgeClass = 'role-badge-admin';
                            break;
                        case 'Deployable':
                            badgeClass = 'role-badge-user';
                            break;
                        case 'Pending':
                            badgeClass = 'role-badge-super-admin';
                            break;
                        case 'Archived':
                            badgeClass = 'role-badge-super-admin';
                            break;
                    }

                    if (type === 'display') {
                        return `<span class="role-badge ${badgeClass}">${data ?? ''}</span>`;
                    }

                    return data;
                }
            },
            {
                data: "a.notes",
                render: function (data, type, row) {
                    const notes = data ? String(data) : '';
                    if (!notes) return '';
                    if (notes.length > 50) {
                        const truncated = truncateHtml(notes, 50);
                        return `<span class="b1-text opacity-50 text-truncate">${truncated}</span><a href="#" class="read-more" style="cursor:pointer; color:#00a1ff;" data-full-text="${notes}">...Read More</a >`;
                    }

                    return `<span class="opacity-50 text-truncate">${notes}</span>`;
                }
            },
            {
                data: "a",
                orderable: false,
                width: "5%",
                render: function (data, type, row) {
                    if (row.a.is_edit_disabled == 1) {
                        return "";
                    }

                    // return t.buildActions(d);
                    return t.renderActionsCell(row.a || {}, type);
                },
            },
            {
                data: 'a.updated_at', visible: false, searchable: false
            },
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();
                api.columns().every(function () {
                    if (!this.visible()) return;
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column - toggle - ${columnIndex} `;
                    controls.append(`
                        <div class="dropdown-item">
                            <label>
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
        }
    });
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

LabelApp.prototype.showHide = function (e) {
    e.preventDefault();
    $('#columnVisibilityControls').toggle();
};

LabelApp.prototype.renderActionsCell = function (record, type) {
    var t = this;
    var actionState = t.getRowActionState(record);
    var quickActions = [];
    // var dropdownId;
    if (type !== "display") return "";
    if (actionState.canEdit) {
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_edit_supplier || "Edit",
            "dtActEdit",
            record.id,
            "edit",
            "open-edit-modal"
        ));
    }
    if (actionState.canDelete) {
        quickActions.push(t.quickActionButtonHtml(
            (t.config.translations || {}).action_delete_supplier || "Delete",
            "dtActDel",
            record.id,
            "delete",
            "go-del"
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

LabelApp.prototype.quickActionButtonHtml = function (label, cls, id, iconKey, extraClass) {
    return [
        '<button type="button" class="user-list-action-btn ', this.escapeHtml(extraClass || ""), ' ', cls, '" data-id="', this.escapeHtml(id), '" data-bs-toggle="tooltip" data-bs-original-title="', this.escapeHtml(label), '" aria-label="', this.escapeHtml(label), '">',
        this.getIcon(iconKey),
        '</button>'
    ].join("");
};

LabelApp.prototype.getRowActionState = function (record) {
    return {
        canEdit: this.hasPermission("StatusLabelEdit"),
        canDelete: this.hasPermission("StatusLabelDelete"),
    };
};

LabelApp.prototype.hasPermission = function (name) {
    return Array.isArray(this.config.permissions) && this.config.permissions.indexOf(name) !== -1;
};

LabelApp.prototype.escapeHtml = function (value) {
    return String(value === null || value === undefined ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
};

LabelApp.prototype.getIcon = function (key) {
    return (this.icons && this.icons[key]) ? this.icons[key]() : "";
};

LabelApp.prototype.icons = {
    edit: function () {
        return '<svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
    },
    delete: function () {
        return '<svg viewBox="0 0 15 17" fill="none" ><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
    },
}

LabelApp.prototype.bindEvents = function () {
    var t = this;

    t.content.on("click", t.btn.reload, () => t.reload());
    t.content.on("click", t.btn.add, (e) => t.create(e));
    t.content.on("click", t.btn.showcolumns, (e) => t.showHide(e));
    t.content.on("click", ".open-edit-modal", (e) => t.edit(e));
    t.content.on("click", ".go-del", (e) => t.delete(e));
    t.content.on('click', t.btn.search_icon, (e) => t.search(e));
    t.content.on('keyup', t.btn.search, (e) => {
        let value = $(e.currentTarget).val().trim();
        if (e.key === 'Enter' || e.keyCode === 13) {
            t.search(e);
        }
    });
    $(t.btn.showColumnsInput).on("change", (e) => t.showColumns(e));
    t.content.on('click', '#table-search-button', (e) => t.search(e));
    t.btn.submit.on("click", (e) => t.submit(e));
    t.frmEl.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        t.dTable.page.len(value).draw();
    });
    t.form.find('[data-required]').on('input', (e) => t.formValidation(e));
};

LabelApp.prototype.create = function (e) {
    var t = this;
    e.preventDefault();

    t.isEdit = false;
    t.submitUrl = t.config.url.add;

    t.resetForm();

    t.form.find('.field-error').text('');

    t.modal.find(".modal-title").text(t.config.translations.create_label);

    t.modal.modal("show");
};

LabelApp.prototype.showHide = function (e) {
    e.preventDefault();
    $('#columnVisibilityControls').toggle();
};

LabelApp.prototype.formValidation = function (e) {
    var $field = $(e.currentTarget);
    var value = $field.val().trim();
    var $error = $field.siblings('.field-error');

    if (value) {
        $error.text('');
    } else {
        $error.text($field.data('required'));
    }
};

LabelApp.prototype.showColumns = function (e) {
    e.preventDefault();
    var $checkbox = $(e.currentTarget);
    var colIndex = $checkbox.data('column');
    this.dTable.column(colIndex).visible($checkbox.is(':checked'));
};

LabelApp.prototype.edit = function (e) {
    var t = this;
    e.preventDefault();

    var id = $(e.currentTarget).data("id");
    t.submitUrl = t.config.url.edit + "/" + id;

    $.get(t.config.url.get + "/" + id, function (res) {
        if (res.status === "success") {
            var d = res.data;

            t.isEdit = true;
            t.resetForm();

            t.frmEl.status_label.val(d.name);
            t.frmEl.notes.val(d.notes);
            t.frmEl.status_type.val(res.status_value).trigger('change');

            t.modal
                .find(".modal-title")
                .text(t.config.translations.edit_label);
            t.modal.modal("show");
        }
    });
};

LabelApp.prototype.submit = function (e) {
    var t = this;
    e.preventDefault();

    var isValid = true;

    t.form.find('.field-error').text('');

    if (this.frmValidator.form() == false) {
        return false;
    }

    var formData = new FormData(t.form[0]);
    formData.append("_token", t.config.token);

    $("#img").show();
    t.btn.submit.prop("disabled", true);

    $.ajax({
        url: t.submitUrl,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function (res) {
            if (res.status === "success") {
                t.modal.modal("hide");
                t.dTable.ajax.reload();

                Swal.fire({
                    icon: "success",
                    title: config.translations.success,
                    text: res.msg || config.translations.saved_successfully,
                    confirmButtonText: "OK",
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: config.translations.error,
                    text: res.msg || config.translations.something_went_wrong_short,
                    confirmButtonText: "OK",
                });
            }
        },

        error: function () {
            Swal.fire({
                icon: "error",
                title: config.translations.error,
                text: config.translations.something_went_wrong_short,
                confirmButtonText: "OK",
            });
        },

        complete: function () {
            $("#img").hide();
            t.btn.submit.prop("disabled", false);
        },
    });
};

LabelApp.prototype.delete = function (e) {
    var t = this;
    e.preventDefault();

    var id = $(e.currentTarget).data("id");
    var url = t.config.url.delete + "/" + id;

    Swal.fire({
        title: t.config.translations.are_you_delete || config.translations.are_you_sure,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: config.translations.yes_delete,
        cancelButtonText: config.translations.cancel,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: "GET",

                beforeSend: function () {
                    Swal.fire({
                        title: config.translations.deleting,
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
                            title: config.translations.deleted,
                            text: res.msg || config.translations.deleted_successfully,
                            confirmButtonText: config.translations.ok,
                        });

                        t.dTable.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: config.translations.error,
                            text: res.msg || config.translations.delete_failed,
                            confirmButtonText: config.translations.ok,
                        });
                    }
                },

                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: config.translations.error,
                        text: config.translations.something_went_wrong_short,
                        confirmButtonText: config.translations.ok,
                    });
                },
            });
        }
    });
};

LabelApp.prototype.resetForm = function () {
    var t = this;
    t.form[0].reset();
    t.frmEl.status_type.val(null).trigger("change");
    t.frmValidator.resetForm();
    t.frm.find('.input-group').removeClass('error');
    t.frm.find('label.error').remove();
    t.frm.find('.field-error').empty();
};

LabelApp.prototype.reload = function () {
    if (this.dTable) {
        var currentSearch = $(this.btn.search).val().trim();
        this.dTable.search(currentSearch).ajax.reload(null, false);
    }
};

LabelApp.prototype.search = function () {
    var t = this;
    let value = $(t.btn.search).val().trim();
    t.dTable.search(value).draw();
}

$(document).on('click', function (e) {
    if (!$(e.target).closest('#columnVisibilityControls, .show-hide-columns').length) {
        $('#columnVisibilityControls').hide();
    }
});

$(function () {
    new LabelApp(config);
});