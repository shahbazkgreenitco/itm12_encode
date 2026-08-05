var rolePermission = function (config) {
    var t = this;
    t.config = config || {};
    t.page = $("#main-role-permission-wrapper");
    t.wrapper = t.page.length ? t.page : $("#mainContent");
    if (!t.wrapper.length) {
        t.wrapper = $(document);
    }
    t.table = $("#role-permission");
    t.mdlWrapper = $("#add-rolepermission-modal");
    t.mdlFrm = t.mdlWrapper.find("#roleAddModal");
    t.mdlSbmtBtn = t.mdlWrapper.find(".btn-add-role");
    t.changeMap = {};
    t.searchTimer = null;
    t.initHelpers();
    if (t.table.length) {
        t.initTable();
    }
    t.bindEvents();
    t.bindPermissionEvents();
    if (t.mdlFrm.length) {
        t.addRole();
    }
    t.deleteRole();
    t.cloneRole();
};

rolePermission.prototype.tr = function (key, fallback) {
    if (this.config && this.config.translations && this.config.translations[key]) {
        return this.config.translations[key];
    }
    return fallback;
};

rolePermission.prototype.escapeHtml = function (value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
};

rolePermission.prototype.icons = {
    dots: `<svg width="5" height="14" viewBox="0 0 5 20" fill="none" >
                <path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/>
            </svg>`,

    edit: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/>
            </svg>`,

    trash: `<svg viewBox="0 0 15 17" fill="none" >
                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
            </svg>`,

    clone: `<svg viewBox="0 0 16 16" fill="none" >
                <path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"/>
            </svg>`
};

rolePermission.prototype.initHelpers = function () {
    var t = this;
    t.tblHelpers = {
        actions: function () {
            return function (d) {
                return t.renderActionButtons(d);
            };
        }
    };
};

rolePermission.prototype.actionButtonHtml = function (label, classes, id, iconMarkup) {
    var safeLabel = this.escapeHtml(label);
    var safeId = this.escapeHtml(id);
    var safeClasses = this.escapeHtml(classes || "");

    return [
        '<button type="button" class="user-list-action-btn role-list-action-btn ',
        safeClasses,
        '" data-id="',
        safeId,
        '" title="',
        safeLabel,
        '" aria-label="',
        safeLabel,
        '">',
        iconMarkup,
        '</button>'
    ].join("");
};

rolePermission.prototype.renderActionButtons = function (record) {
    if (!record || record.id == null) {
        return '<span class="user-list-empty">-</span>';
    }

    return [
        '<div class="user-list-actions role-list-actions ">',
        this.actionButtonHtml(this.tr("action_edit_role", "Edit Role"), "editRole", record.id, this.icons.edit),
        this.actionButtonHtml(this.tr("action_delete_role", "Delete Role"), "deleteRole is-delete", record.id, this.icons.trash),
        this.actionButtonHtml(this.tr("action_clone_role", "Clone Role"), "cloneRole", record.id, this.icons.clone),
        '</div>'
    ].join("");
};

rolePermission.prototype.initTable = function () {
    var t = this;
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
        processing: true,
        serverSide: true,
        autoWidth: false,
        searching: true,
        searchDelay: 350,
        dom: "rt<'bottom'ip><'clear'>",
        ajax: {
            url: t.config.url.getRoles,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
            }
        },
        aoColumnDefs: [
            { bSortable: false, aTargets: [0, 4] },
            {
                targets: 0,
                render: function (data, type, row) {
                    return `<input type="checkbox" class="row-check form-check-input" value="${row.id}">`;
                }
            },
            {
                targets: 4,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
            }
        ],
        columns: [
            { data: 'id' },
            { data: 'name' },
            {
                data: 'users_count',
                render: function (data, type, row) {
                    return `<a href="users?role=${row.id}" target="_blank">${data}</a>`;
                }
            },
            { data: 'created_at_format' },
            { data: null }
        ],
        order: [[3, 'desc']],
        drawCallback: function () {
            t.table.find("#select-all").prop("checked", false);
        }
    });
};

rolePermission.prototype.getSearchShell = function () {
    var searchShell = this.wrapper.find(".role-list-search-shell");
    if (searchShell.length) {
        return searchShell;
    }

    return this.wrapper.find(".role-list-toolbar-search");
};

rolePermission.prototype.getSearchInput = function () {
    return this.wrapper.find(".dt-search-input");
};

rolePermission.prototype.syncSearchShell = function (forceOpen) {
    var searchShell = this.getSearchShell();
    var searchInput = this.getSearchInput();
    var hasValue = $.trim(searchInput.val() || "") !== "";
    searchShell.toggleClass("is-open", !!forceOpen || hasValue || searchInput.is(":focus"));
};

rolePermission.prototype.applyTableSearch = function (value) {
    if (!this.dTbl) {
        return;
    }

    this.dTbl.search($.trim(value || "")).draw();
    this.syncSearchShell(false);
};

rolePermission.prototype.queueTableSearch = function (value) {
    var t = this;

    if (!t.dTbl) {
        return;
    }

    clearTimeout(t.searchTimer);
    t.searchTimer = setTimeout(function () {
        t.applyTableSearch(value);
    }, 300);
};

rolePermission.prototype.closeSearchShellIfEmpty = function () {
    var t = this;

    setTimeout(function () {
        var searchInput = t.getSearchInput();
        var hasValue = $.trim(searchInput.val() || "") !== "";

        if (!hasValue && !searchInput.is(":focus")) {
            t.getSearchShell().removeClass("is-open");
        }
    }, 0);
};

rolePermission.prototype._openModal = function (title, mode, cloneId) {
    var t = this;
    if (!t.mdlFrm.length) {
        return;
    }
    t.mdlFrm[0].reset();
    t.mdlFrm.find(".is-invalid").removeClass("is-invalid");
    t.mdlFrm.find(".invalid-feedback").remove();
    t.mdlWrapper.find("#myLargeModalLabel").html(title);
    t.mdlFrm.removeAttr("data-mode");
    t.mdlFrm.find("#clone_role_id").val("");

    if (mode) {
        t.mdlFrm.attr("data-mode", mode);
    }
    if (cloneId) {
        t.mdlFrm.find("#clone_role_id").val(cloneId);
    }

    t.mdlWrapper.modal("show");
};

rolePermission.prototype.addRole = function () {
    var t = this;
    t.mdlFrm.on("submit", function (e) {
        e.preventDefault();
        e.stopPropagation();

        if (t.mdlSbmtBtn.prop("disabled")) return false;

        // clear previous errors
        t.mdlFrm.find(".is-invalid").removeClass("is-invalid");
        t.mdlFrm.find(".invalid-feedback").remove();

        var roleNameInput = t.mdlFrm.find('[name="name"]');
        var roleName = roleNameInput.val().trim();

        if (roleName === '') {
            roleNameInput.addClass("is-invalid")
                .after('<div class="invalid-feedback">' + t.tr("role_name_required", "Role name is required.") + '</div>');
            return false;
        }

        var cleanTextRegex =/^([a-zA-Z0-9 _-]+)$/;

        if (!cleanTextRegex.test(roleName) || /<[^>]*>/g.test(roleName)) {
            roleNameInput
                .addClass("is-invalid")
                .after('<div class="invalid-feedback">Alphabets, numbers and limited characters only. HTML tags are not allowed.</div>');
            return false;
        }

        var mode = t.mdlFrm.attr("data-mode");
        var ajaxUrl = (mode === "clone") ? t.config.url.cloneRole : t.config.url.addRole;

        t.mdlSbmtBtn.prop("disabled", true);

        $.ajax({
            url: ajaxUrl,
            type: "POST",
            data: t.mdlFrm.serialize(),
            success: function (res) {
                if (res.status === "success") {
                    Swal.fire({ icon: "success", title: "Success", text: res.msg, timer: 1500, showConfirmButton: false });
                    t.mdlFrm[0].reset();
                    t.mdlFrm.removeAttr("data-mode");
                    t.mdlFrm.find("#clone_role_id").val("");
                    t.mdlWrapper.modal("hide");
                    t.reload();
                } else {
                    roleNameInput.addClass("is-invalid")
                        .after('<div class="invalid-feedback">' + res.msg + '</div>');
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        var input = t.mdlFrm.find('[name="' + key + '"]');
                        input.addClass("is-invalid")
                            .after('<div class="invalid-feedback">' + value[0] + '</div>');
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: t.tr("server_error_title", "Server Error"),
                        text: t.tr("something_went_wrong", "Something went wrong.")
                    });
                }
            },
            complete: function () {
                t.mdlSbmtBtn.prop("disabled", false);
            }
        });
    });
};

rolePermission.prototype.deleteRole = function () {
    var t = this;
    t.wrapper.on("click", ".deleteRole", function () {
        var id = $(this).data("id");
        Swal.fire({
            title: t.tr("confirm_delete_title", "Confirm?"),
            text: t.tr("confirm_delete_text", "Are you sure you want to delete this role?"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: t.tr("confirm_delete_button", "Yes")
        }).then(function (result) {
            if (result.isConfirmed) {
                $.get(t.config.url.deleteRole + '/' + id, function (res) {
                    if (res.status === "success") {
                        Swal.fire(t.tr("deleted_title", "Deleted!"), res.msg, "success");
                        t.reload();
                    } else {
                        Swal.fire(t.tr("error_title", "Error"), res.msg, "error");
                    }
                });
            }
        });
    });
};

rolePermission.prototype.cloneRole = function () {
    var t = this;
    t.wrapper.on("click", ".cloneRole", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t._openModal(t.tr("modal_clone_role_title", "Clone Role"), "clone", $(this).data("id"));
    });
};

rolePermission.prototype.bindEvents = function () {
    var t = this;

    // select all checkboxes
    t.wrapper.on("change", "#select-all", function () {
        t.table.find(".row-check").prop("checked", $(this).prop("checked"));
    });

    // edit role → redirect
    t.wrapper.on("click", ".editRole", function () {    
        window.location = t.config.url.role_edit + "/" + $(this).data("id");
    });

    // open add role modal
    t.wrapper.on("click", ".addRoleBtn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t._openModal(t.tr("modal_add_role_title", "Add Role"));
    });

    t.wrapper.on("click", ".dt-search-btn", function (e) {
        e.preventDefault();
        clearTimeout(t.searchTimer);
        t.applyTableSearch(t.getSearchInput().val());
    });

    t.wrapper.on("input", ".dt-search-input", function () {
        t.syncSearchShell(true);
        t.queueTableSearch($(this).val());
    });

    t.wrapper.on("keydown", ".dt-search-input", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            clearTimeout(t.searchTimer);
            t.applyTableSearch($(this).val());
        }

        if (e.which === 27) {
            e.preventDefault();
            $(this).val("");
            clearTimeout(t.searchTimer);
            t.refreshTable();
        }
    });

    t.wrapper.on("click", ".dt-refresh-btn", function () {
        t.refreshTable();
    });
};

rolePermission.prototype.bindPermissionEvents = function () {
    var t = this;
    $(document).on("change", ".permissions", function () {
        var name = $(this).attr("name");
        var value = $(this).prop("checked");
        var match = name.match(/\[(.*?)\]/);
        var permission = match ? match[1] : null;
        if (permission !== null) {
            t.changeMap[permission] = value;
        }
    });
    $(document).on("change", ".selectAllPermission", function () {
        var tab = $(this).closest(".tab-pane");
        var checked = $(this).prop("checked");
        tab.find(".permissions").each(function () {
            if ($(this).prop("checked") !== checked) {
                $(this).prop("checked", checked).trigger("change");
            }
        });
    });

    $(document).on("click", ".updateRole", function () {
        $("#popup_loader").addClass("active");
        $(".updateRole").attr("disabled", true);

        var changeArray = Object.keys(t.changeMap).map(function (key) {
            return { permission: key, value: t.changeMap[key] };
        });

        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
        formData.append("permissionArray", JSON.stringify(changeArray));
        formData.append("roleId", $("#roleId").val());

        $.ajax({
            url: t.config.url.updateRole,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                if (data.status === "success") {
                    toastr.success(data.msg);
                    t.changeMap = {};
                } else {
                    toastr.error(data.msg);
                }
            },
            error: function (xhr) {
                toastr.error(t.tr("something_went_wrong", "Something went wrong"));
                console.error(xhr.responseText);
            },
            complete: function () {
                $(".updateRole").removeAttr("disabled");
                $("#popup_loader").removeClass("active");
            }
        });
    });
    $(document).on("keyup", ".global-permission-search", function () {
        t._filterPermissions($(this).val().toLowerCase().trim());
    });
};

rolePermission.prototype._filterPermissions = function (keyword) {
    var allPermissions = $(".permissions");
    if (keyword === "") {
        allPermissions.closest(".col-md-4").show();
        $(".blue-pills .nav-link").removeClass("active").first().addClass("active");
        $(".permission-tab-wrapper .tab-pane").removeClass("show active").first().addClass("show active");
        $(".gray-pills .nav-link").removeClass("active").first().addClass("active");
        $(".gray-pills").each(function () {
            $(this).next(".tab-content")
                .find(".tab-pane")
                .removeClass("show active")
                .first()
                .addClass("show active");
        });
        return;
    }
    allPermissions.closest(".col-md-4").hide();
    var firstMatchMainTab = null;
    var firstMatchChildTab = null;
    allPermissions.each(function () {
        var label = $(this).siblings("label").text().toLowerCase();
        if (!label.includes(keyword)) return;

        $(this).closest(".col-md-4").show();

        var mainTab = $(this).closest('.tab-pane[id^="v-pills-"]');
        if (!firstMatchMainTab) firstMatchMainTab = mainTab;

        var childTab = $(this).closest('.tab-pane[id^="ticket-child-"]');
        if (childTab.length && !firstMatchChildTab) firstMatchChildTab = childTab;
    });

    if (firstMatchMainTab) {
        var mainTabId = firstMatchMainTab.attr("id");
        $(".blue-pills .nav-link").removeClass("active");
        $('.blue-pills .nav-link[href="#' + mainTabId + '"]').addClass("active");
        $('.permission-tab-wrapper .tab-pane[id^="v-pills-"]').removeClass("show active");
        firstMatchMainTab.addClass("show active");
    }

    if (firstMatchChildTab) {
        var childTabId = firstMatchChildTab.attr("id");
        $(".gray-pills .nav-link").removeClass("active");
        $('.gray-pills .nav-link[href="#' + childTabId + '"]').addClass("active");
        firstMatchChildTab.siblings(".tab-pane").removeClass("show active");
        firstMatchChildTab.addClass("show active");
    }
};
rolePermission.prototype.reload = function () {
    if (this.dTbl) {
        this.dTbl.ajax.reload(null, false);
    }
};
rolePermission.prototype.refreshTable = function () {
    if (this.dTbl) {
        clearTimeout(this.searchTimer);
        this.getSearchInput().val("");
        this.getSearchShell().removeClass("is-open");
        // this.dTbl.search("");
        this.dTbl.ajax.reload(null, false);
    }
};
