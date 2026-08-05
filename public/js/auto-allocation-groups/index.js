var AutoAllocGroups = function (config) {
    var t = this;
    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.other_filters = t.config.other_filters || {};
    t.config.search = t.config.search || "";
    t.config.permissions = t.config.permissions || [];
    t.page = $("#main-user-list-wrapper");
    t.content = t.page.length ? t.page : $("section.content");
    t.table = t.content.find("#mytable");
    t.pageLength = t.content.find(".user-list-page-length");
    t.searchbox = t.content.find(".plain-search");
    t.filterBadge = t.content.find(".filter-count-badge");
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    t.companySwitcher = $("#amg-company-select");
    t.actionMenuHideTimer = null;

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.initTooltips = function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            let tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) tooltip.dispose();
            new bootstrap.Tooltip(el);
        });
    };

    t.tr = function (key, fallback) {
        return t.config.translations[key] || fallback;
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.hasPermission = function (permission) {
        return $.inArray(permission, t.config.permissions) !== -1;
    };

    t.icons = {
        ellipsis: '<svg width="5" height="14" viewBox="0 0 5 20" fill="none"><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>',
        edit: '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>',
        trash: '<i class="bi bi-trash"></i>',
        user: '<i class="bi bi-people"></i>',
        clone: '<i class="bi bi-files"></i>',
        history: '<i class="bi bi-info-circle"></i>'
    };

    t.getIcon = function (key) {
        return t.icons[key] || "";
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup, href) {
        var attrs = ' class="user-list-action-btn role-list-action-btn ' + t.escapeHtml(classes || "") + '"' +
            ' id="' + t.escapeHtml(id) + '"' +
            ' data-id="' + t.escapeHtml(id) + '"' +
            ' title="' + t.escapeHtml(label) + '"' +
            ' aria-label="' + t.escapeHtml(label) + '"'+
            ' data-bs-toggle="tooltip"' +
            ' data-bs-placement="top"';

        if (href) {
            return '<a href="' + href + '"' + attrs + ' target="_blank">' + iconMarkup + "</a>";
        }

        return '<button type="button"' + attrs + ">" + iconMarkup + "</button>";
    };

    t.actionItemHtml = function (label, classes, id, iconMarkup, href, target) {
        var attrs = href
            ? ' href="' + t.escapeHtml(href) + '"'
            : ' href="#" id="' + t.escapeHtml(id) + '" data-id="' + t.escapeHtml(id) + '"';

        if (target) {
            attrs += ' target="' + t.escapeHtml(target) + '"';
        }

        return [
            '<li>',
            '<a class="dropdown-item ', t.escapeHtml(classes || ""), '"', attrs, '>',
            iconMarkup,
            '<span class="b3-text">', t.escapeHtml(label), '</span>',
            '</a>',
            '</li>'
        ].join("");
    };

    t.renderActionButtons = function (record, type) {
        var id;
        var quickActions = [];
        var menuActions = [];
        var dropdownId;

        if (type && type !== "display") {
            return "";
        }

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        id = record.id;

        if (t.hasPermission("TicketAllocationGroupEdit")) {
            quickActions.push(t.actionButtonHtml(t.tr("edit_group", "Edit Group Details"), "open-edit-modal", id, t.getIcon("edit")));
        }
        if (t.hasPermission("TicketAllocationGroupDelete")) {
            quickActions.push(t.actionButtonHtml(t.tr("delete_group", "Delete Group"), "open-delete is-delete", id, t.getIcon("trash")));
        }

        if (t.hasPermission("TicketAllocationGroupUserRead")) {
            menuActions.push(t.actionItemHtml(t.tr("manage_users", "Manage Group Users"), "", id, t.getIcon("user"), t.config.url.manage_users + "/" + id, "_blank"));
        }
        if (t.hasPermission("TicketAllocationGroupAdd")) {
            menuActions.push(t.actionItemHtml(t.tr("group_clone", "Clone Group"), "cloneBtn", id, t.getIcon("clone")));
            menuActions.push(t.actionItemHtml(t.tr("history", "History"), "", id, t.getIcon("history"), t.config.url.history + "/" + id, "_blank"));
        }

        if (!quickActions.length && !menuActions.length) {
            return '<span class="user-list-empty">-</span>';
        }

        dropdownId = "auto-allocation-action-dropdown-" + id;

        return [
            '<div class="user-list-actions justify-content-start">',
            quickActions.join(""),
            menuActions.length ? [
                '<div class="">',
                '<button type="button" class="action-link user-list-menu-toggle" id="', dropdownId, '" aria-expanded="false" aria-haspopup="true" aria-label="More actions">',
                t.getIcon("ellipsis"),
                '</button>',
                '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="', dropdownId, '">',
                menuActions.join(""),
                '</ul>',
                '</div>'
            ].join("") : "",
            '</div>'
        ].join("");
    };

    t.showActionMenu = function (dropdown) {
        var actionCell;
        var actionRow;

        dropdown = $(dropdown);
        if (!dropdown.length) return;

        actionCell = dropdown.closest("td, th");
        actionRow = dropdown.closest("tr");

        t.clearActionMenuHideTimer();
        t.hideActionMenus(dropdown);
        actionCell.addClass("is-action-menu-open");
        actionRow.addClass("is-action-menu-open");
        dropdown.addClass("is-hover-open");
        dropdown.find(".user-list-menu-toggle").addClass("show").attr("aria-expanded", "true");
        dropdown.find(".action-dropdown-menu").addClass("show");
    };

    t.hideActionMenu = function (dropdown) {
        var actionCell;
        var actionRow;

        dropdown = $(dropdown);
        if (!dropdown.length) return;

        actionCell = dropdown.closest("td, th");
        actionRow = dropdown.closest("tr");
        actionCell.removeClass("is-action-menu-open");
        actionRow.removeClass("is-action-menu-open");
        dropdown.removeClass("is-hover-open");
        dropdown.find(".user-list-menu-toggle").removeClass("show").attr("aria-expanded", "false");
        dropdown.find(".action-dropdown-menu").removeClass("show");
    };

    t.scheduleActionMenuHide = function (dropdown) {
        dropdown = $(dropdown);
        if (!dropdown.length) return;

        t.clearActionMenuHideTimer();
        t.actionMenuHideTimer = window.setTimeout(function () {
            t.hideActionMenu(dropdown);
        }, 180);
    };

    t.clearActionMenuHideTimer = function () {
        if (!t.actionMenuHideTimer) return;

        window.clearTimeout(t.actionMenuHideTimer);
        t.actionMenuHideTimer = null;
    };

    t.hideActionMenus = function (exceptDropdown) {
        var exceptElement = exceptDropdown && $(exceptDropdown).length ? $(exceptDropdown).get(0) : null;

        t.content.find(".user-list-actions .dropdown.is-hover-open").each(function () {
            if (exceptElement && this === exceptElement) return;
            t.hideActionMenu($(this));
        });
    };

    t.mdl = t.content.find("#autoAllocationFormMdl");
    t.mdl.loader = t.mdl.find("#auto_allocation_mdl_loader");
    t.mdltitle = t.mdl.find(".modal-title");
    t.frm = t.mdl.find("form").first();
    t.btn = {};
    t.btn.submit = t.frm.find("#btnSubmit");
    t.btn.clear = t.frm.find("#btnClear");

    t.frmEl = {};
    t.frmEl.groupName = t.frm.find("#groupName");
    t.frmEl.groupDesc = t.frm.find("#groupDesc");
    t.frmEl.groupDepartmentId = t.frm.find("#groupDepartmentId");
    t.frmEl.companyId = t.frm.find("#company_id");
    t.frmEl.groupLocationApprovalRequired = t.frm.find("#location_approval_required");
    t.frmEl.groupLocationApprovalRequiredFor = t.frm.find("#location_approval_required_for");
    t.frm.groupLocalApprovalRequiredForDiv = t.frm.find(".groupLocalApprovalRequiredForDiv");

    t.cloneMdl = t.content.find("#cloneAutoAllocationFormMdl");
    t.cloneMdl.loader = t.cloneMdl.find("#clone_auto_allocation_mdl_loader");
    t.cloneFrm = t.cloneMdl.find("form").first();
    t.cloneMdltitle = t.cloneMdl.find(".modal-title");
    t.cloneBtn = {};
    t.cloneBtn.submit = t.cloneMdl.find(".cloneSubmit");

    t.filters = {
        wrapper: t.content.find("#advanceFilterModal")
    };
    t.filters.company = t.filters.wrapper.find("#filter_by_company");
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.locationAccess = t.filters.wrapper.find("#filter_by_location_access");
    t.filters.btnClear = t.filters.wrapper.find("#btnClrFilter");

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
        var isSelect2 = element.hasClass("select2-hidden-accessible");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }
        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    t.initFormCompanySelect = function () {
        t.frmEl.companyId.select2({
            width: "100%",
            dropdownParent: t.frmEl.companyId.parent(),
            ajax: {
                url: t.config.url.getCompanyByUserAccess,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: t.tr("select_company", "Select Company")
        });
    };

    t.initFormDepartmentSelect = function (companyId, selectedDepartment) {
        if (t.frmEl.groupDepartmentId.hasClass("select2-hidden-accessible")) {
            t.frmEl.groupDepartmentId.select2("destroy");
        }
        t.frmEl.groupDepartmentId.empty();

        if (!companyId) {
            t.frmEl.groupDepartmentId.select2({
                width: "100%",
                dropdownParent: t.frmEl.groupDepartmentId.parent(),
                allowClear: true,
                placeholder: t.tr("select_department", "Select Department")
            });
            return;
        }

        t.frmEl.groupDepartmentId.select2({
            width: "100%",
            dropdownParent: t.frmEl.groupDepartmentId.parent(),
            ajax: {
                url: t.config.url.autoAllocationDepartment + "/" + companyId,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                processResults: function (response) {
                    return {
                        results: $.map((response && response.data) || [], function (item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        })
                    };
                },
                delay: 300
            },
            allowClear: true,
            placeholder: t.tr("select_department", "Select Department")
        });

        if (selectedDepartment && selectedDepartment.id) {
            t.frmEl.groupDepartmentId
                .append(new Option(selectedDepartment.text, selectedDepartment.id, true, true))
                .trigger("change");
        }
    };

    t.initFilterSelects = function () {
        t.filters.company.select2({
            width: "100%",
            dropdownParent: t.filters.wrapper,
            placeholder: t.tr("select_company", "Select Company"),
            allowClear: true,
            ajax: {
                url: t.config.url.getCompanyByUserAccess,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                delay: 300
            }
        });

        t.filters.department.select2({
            width: "100%",
            dropdownParent: t.filters.wrapper,
            placeholder: t.tr("select_department", "Select Department"),
            allowClear: true,
            closeOnSelect: false
        });

        t.filters.locationAccess.select2({
            width: "100%",
            dropdownParent: t.filters.wrapper,
            placeholder: t.tr("select_location_type", "Select Location Type"),
            allowClear: true
        });
    };

    t.reloadFilterDepartments = function (selectedValues) {
        var companyId = $.trim(t.filters.company.val() || "");
        var fallbackCompanyId = $.trim(t.companySwitcher.val() || t.config.company_id || "");
        var requestCompanyId = companyId || fallbackCompanyId;

        t.filters.department.empty();
        if (!requestCompanyId) {
            t.filters.department.trigger("change");
            return;
        }

        $.get(t.config.url.autoAllocationDepartment + "/" + requestCompanyId).done(function (response) {
            var departments = (response && response.data) || [];
            $.each(departments, function (i, dep) {
                t.filters.department.append(new Option(dep.name, dep.id, false, false));
            });
            if ($.isArray(selectedValues) && selectedValues.length) {
                t.filters.department.val(selectedValues).trigger("change");
            } else {
                t.filters.department.trigger("change");
            }
        });
    };

    t.updateLocationApprovalVisibility = function () {
        var shouldShow = String(t.frmEl.groupLocationApprovalRequired.val()) === "1";
        t.frm.groupLocalApprovalRequiredForDiv.toggleClass("d-none", !shouldShow);
        t.frmEl.groupLocationApprovalRequiredFor.prop("disabled", !shouldShow);
        if (!shouldShow) {
            t.frmEl.groupLocationApprovalRequiredFor.val("").trigger("change");
        }
    };

    t.cacheFilterValues = function () {
        var search = $.trim(t.searchbox.val() || "");
        var departmentVals = t.filters.department.val();

        t.config.search = search;
        t.config.other_filters = {};

        if (t.filters.company.val()) {
            t.config.other_filters.filter_by_company = t.filters.company.val();
        }
        if ($.isArray(departmentVals) && departmentVals.length) {
            t.config.other_filters.filter_by_department = departmentVals;
        }
        if (t.filters.locationAccess.val()) {
            t.config.other_filters.filter_by_location_access = t.filters.locationAccess.val();
        }

        t.config.export_filters = btoa(JSON.stringify({
            search: t.config.search,
            other_filters: t.config.other_filters
        }));
        t.updateFilterBadge();
    };

    t.updateFilterBadge = function () {
        var count = 0;
        $.each(t.config.other_filters || {}, function (key, value) {
            if ($.isArray(value) && value.length) {
                count++;
                return;
            }
            if (value !== "" && value != null) {
                count++;
            }
        });
        t.filterBadge.text(count).toggleClass("d-none", count === 0);
    };

    t.reload = function (resetPaging) {
        t.dTbl.ajax.reload(null, resetPaging === true);
    };

    t.search = function (e) {
        if (e && e.type === "keyup") {
            var value = $.trim(t.searchbox.val() || "");
            if (e.keyCode !== 13 && value.length > 0) {
                return;
            }
        }

        var v = typeof t.searchbox.validate_str_param === "function"
            ? t.searchbox.validate_str_param()
            : $.trim(t.searchbox.val() || "");

        if (v === false) {
            t.config.search = "";
            alert(t.tr("please_enter_valid_search", "Please enter a valid value for search"));
            return false;
        }

        t.config.search = v;
        t.reload(true);
    };

    t.openFilter = function (e) {
        e.preventDefault();
        t.filters.wrapper.modal("show");
    };

    t.applyFilters = function (e) {
        e.preventDefault();
        t.cacheFilterValues();
        t.reload(true);
        t.filters.wrapper.modal("hide");
    };

    t.clearFilters = function (e) {
        e.preventDefault();
        t.filters.company.val("").trigger("change");
        t.filters.locationAccess.val("").trigger("change");
        t.filters.department.val("").trigger("change");
        t.config.other_filters = {};
        t.updateFilterBadge();
        t.reload(true);
        t.filters.wrapper.modal("hide");
    };

    t.download = function (e) {
        e.preventDefault();
        t.cacheFilterValues();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    };

    t.bulkImport = function (e) {
        e.preventDefault();
        window.location = t.config.url.import_url;
    };

    t.changePageLength = function () {
        t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw();
    };

    t.resetForm = function () {
        t.frm[0].reset();
        t.frmEl.groupName.val("");
        t.frmEl.groupDesc.val("");
        t.frmEl.companyId.empty().val("").trigger("change");
        t.frmEl.groupDepartmentId.empty().val("").trigger("change");
        t.frmEl.groupLocationApprovalRequired.val("").trigger("change");
        t.frmEl.groupLocationApprovalRequiredFor.val("").trigger("change");
        t.updateLocationApprovalVisibility();
        t.frmValidator.resetForm();
        t.frm.find("label.error").remove();
        t.frm.find(".amg-form-error-wrap").empty();
        t.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
    };

    t.loadForm = function (payload) {
        var data = payload.data || {};
        var dropdown = payload.dropdown || {};
        var selectedCompany = dropdown.company_id || null;
        var selectedDepartment = dropdown.department_id || null;

        t.frmEl.groupName.val(data.name || "");
        t.frmEl.groupDesc.val(data.desc || "");

        if (selectedCompany && selectedCompany.id) {
            t.frmEl.companyId
                .append(new Option(selectedCompany.text, selectedCompany.id, true, true))
                .trigger("change");
            t.initFormDepartmentSelect(selectedCompany.id, selectedDepartment);
        } else {
            t.initFormDepartmentSelect(null, null);
        }

        t.frmEl.groupLocationApprovalRequired.val(String(data.location_approval_required || 0)).trigger("change");
        t.frmEl.groupLocationApprovalRequiredFor.val(data.location_approval_required_for || "").trigger("change");
        t.updateLocationApprovalVisibility();
    };

    t.createGroup = function (e) {
        var defaultCompanyId;
        var defaultCompanyText;

        e.preventDefault();
        t.httpPostPath = t.config.url.add;
        t.resetForm();
        t.mdltitle.text(t.tr("add_group", "Add Group Details"));
        t.btn.submit.text(t.tr("save", "Save"));
        t.btn.submit.prop("disabled", false);
        t.mdl.loader.hide();

        defaultCompanyId = $.trim(t.companySwitcher.val() || "");
        defaultCompanyText = t.companySwitcher.find("option:selected").text() || "";

        if (defaultCompanyId && defaultCompanyId !== "0") {
            t.frmEl.companyId
                .append(new Option(defaultCompanyText, defaultCompanyId, true, true))
                .trigger("change");
        } else {
            t.initFormDepartmentSelect(null, null);
        }

        t.mdl.modal("show");
    };

    t.editGroup = function (e) {
        var id;
        var http;

        e.preventDefault();
        id = $(this).attr("id");
        t.httpPostPath = t.config.url.editGroupDetails + "/" + id;
        t.resetForm();
        t.mdltitle.text(t.tr("edit_group", "Edit Group Details"));
        t.btn.submit.text(t.tr("save_changes", "Save Changes"));
        t.btn.submit.prop("disabled", true);
        t.mdl.loader.show();
        t.mdl.modal("show");

        http = $.get(t.config.url.getGroupDetails + "/" + id);
        http.done(function (response) {
            if (response && response.status === "success" && response.data) {
                t.loadForm(response.data);
                return;
            }
            sweetAlert("center", "error", response || { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.fail(function () {
            sweetAlert("center", "error", { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.always(function () {
            t.btn.submit.prop("disabled", false);
            t.mdl.loader.hide();
        });
    };

    t.handleSubmit = function (e) {
        var frmData;
        var http;

        e.preventDefault();
        if (t.frmValidator.form() === false) {
            return false;
        }

        frmData = new FormData(t.frm[0]);
        frmData.append("_token", t.config.token);

        t.btn.submit.prop("disabled", true);
        t.mdl.loader.show();

        http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (response) {
            if (response && response.status === "success") {
                sweetAlert("center", "success", response);
                t.mdl.modal("hide");
                t.reload();
                return;
            }
            sweetAlert("center", "error", response || { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.fail(function () {
            sweetAlert("center", "error", { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.always(function () {
            t.btn.submit.prop("disabled", false);
            t.mdl.loader.hide();
        });
    };

    t.cloneGroup = function (e) {
        var id;

        e.preventDefault();
        id = $(this).attr("id");
        t.cloneFrm[0].reset();
        t.cloneFrm.find("#groupId").val(id);
        t.cloneMdltitle.text(t.tr("group_clone", "Clone Group"));
        t.cloneBtn.submit.text(t.tr("save", "Save"));
        t.cloneBtn.submit.prop("disabled", false);
        t.cloneMdl.loader.hide();
        t.cloneMdl.modal("show");
    };

    t.cloneSubmit = function (e) {
        var frmData;
        var http;

        e.preventDefault();
        if (t.cloneFrmValidator.form() === false) {
            return false;
        }

        frmData = new FormData(t.cloneFrm[0]);
        frmData.append("_token", t.config.token);

        t.cloneBtn.submit.prop("disabled", true);
        t.cloneMdl.loader.show();

        http = $.ajax({
            url: t.config.url.cloneAllocationGroup,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (response) {
            if (response && response.status === "success") {
                sweetAlert("center", "success", response);
                t.cloneMdl.modal("hide");
                t.reload();
                return;
            }
            sweetAlert("center", "error", response || { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.fail(function () {
            sweetAlert("center", "error", { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.always(function () {
            t.cloneBtn.submit.prop("disabled", false);
            t.cloneMdl.loader.hide();
        });
    };

    t.deleteGroup = function (e) {
        var recId;
        var path;
        var data;

        e.preventDefault();
        recId = $(this).attr("id");
        path = t.config.url.delete + "/" + recId;
        data = {
            msg: t.tr("something_went_wrong", "Something went wrong. Please try again.")
        };
        sweetAlerts(t.tr("confirmation_message", "Are you sure to delete this Group?"), "warning", path, t.dTbl, data);
    };

    t.frmValidator = t.frm.validate({
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {
            groupName: {
                required: true,
                maxlength: 200,
                clean_text_only: true
            },
            groupDesc: {
                required: true,
                maxlength: 2000,
                clean_text_only: true
            },
            company_id: {
                required: true
            },
            groupDepartmentId: {
                required: true
            },
            location_approval_required: {
                required: true
            },
            location_approval_required_for: {
                required: {
                    depends: function () {
                        return String(t.frmEl.groupLocationApprovalRequired.val()) === "1";
                    }
                }
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.cloneFrmValidator = t.cloneFrm.validate({
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {
            groupName: {
                required: true,
                maxlength: 500,
                clean_text_only: true
            },
            groupDesc: {
                required: true,
                maxlength: 2000,
                clean_text_only: true
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        scrollX: true,
        scrollCollapse: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        order: [[6, "desc"]],
        columnDefs: [
            {
                targets: 7,
                orderable: false,
                searchable: false,
                width: "148px",
                className: "amg-col-actions role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return t.renderActionButtons(row, type);
                }
            }
        ],
        ajax: {
            url: t.config.url.getUserAllocationGroups,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || $.trim(t.searchbox.val() || "");
                d.advFilters = t.config.other_filters || {};
            }
        },
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        language: {
            emptyTable: t.tr("no_records_found", "No matching records found"),
            zeroRecords: t.tr("no_records_found", "No matching records found")
        },
        drawCallback: function () {
            t.initTooltips();
            t.updateFilterBadge();
        },
        columns: [
            { data: "name", 
                render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            { data: "company_name", render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            { data: "dept_name", render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            {
                data: "group_member_count",
                defaultContent: "0",
                render: function (data, type, row) {
                    var count = data || 0;
                    if (t.hasPermission("TicketAllocationGroupUserRead")) {
                        return '<a class="b5-text" href="' + t.config.url.manage_users + "/" + row.id + '" target="_blank">' + count + "</a>";
                    }
                    return count;
                }
            },
            { data: "location", render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            {
                data: "location_approval_required",
                defaultContent: 0,
                render: function (data) {
                    return `<div class="b5-text">${parseInt(data, 10) === 1 ? t.tr("yes", "Yes") : t.tr("no", "No")}</div>`;
                }

            },
            { data: "updated_at_formatted",className: "amg-table-col-144", render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            { data: "id", defaultContent: null }
        ]
    });

    t.initFormCompanySelect();
    t.initFormDepartmentSelect(null, null);
    t.frmEl.groupLocationApprovalRequired.select2({
        width: "100%",
        dropdownParent: t.frmEl.groupLocationApprovalRequired.parent(),
        allowClear: true,
        placeholder: t.tr("location_approval_required", "Location Approval Required")
    });
    t.frmEl.groupLocationApprovalRequiredFor.select2({
        width: "100%",
        dropdownParent: t.frmEl.groupLocationApprovalRequiredFor.parent(),
        allowClear: true,
        placeholder: t.tr("select_location_type", "Select Location Type")
    });
    t.updateLocationApprovalVisibility();

    t.initFilterSelects();
  

    t.frmEl.companyId.on("change", function () {
        t.initFormDepartmentSelect($(this).val(), null);
    });
    t.frmEl.groupLocationApprovalRequired.on("change", $.proxy(t.updateLocationApprovalVisibility));
    t.filters.company.on("change", function () {
        t.filters.department.val("");
        t.reloadFilterDepartments([]);
    });
    t.companySwitcher.on("change", function () {
        t.filters.company.val("");
        t.filters.department.val("");
        t.reloadFilterDepartments([]);
        t.reload(true);
    });

    t.content.on("click", ".open-add-modal", $.proxy(t.createGroup));
    t.content.on("click", ".open-edit-modal", $.proxy(t.editGroup));
    t.content.on("click", ".open-delete", $.proxy(t.deleteGroup));
    t.content.on("click", ".cloneBtn", $.proxy(t.cloneGroup));
    var menuCloseTimer;

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#detached-action-menu').length && !$(e.target).closest('.user-list-menu-toggle').length) {
            $('#detached-action-menu').remove();
            $('.user-list-menu-toggle').data('menu-open', false);
        }
    });

    $(document).on('mouseenter', '.user-list-menu-toggle, #detached-action-menu', function () {
        clearTimeout(menuCloseTimer);
    });

    $(document).on('mouseleave', '.user-list-menu-toggle, #detached-action-menu', function () {
        menuCloseTimer = setTimeout(function () {
            var btnHovered = $('.user-list-menu-toggle:hover').length;
            var menuHovered = $('#detached-action-menu:hover').length;
            if (!btnHovered && !menuHovered) {
                $('#detached-action-menu').remove();
                $('.user-list-menu-toggle').data('menu-open', false);
            }
        }, 150);
    });

    t.content.on('mouseenter', '.user-list-menu-toggle', function (e) {
        if ($('#detached-action-menu').length) {
            clearTimeout(menuCloseTimer);
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        var $btn = $(this);
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);

        var $menu = $btn.siblings('.dropdown-menu').clone(true);
        $menu.attr('id', 'detached-action-menu').addClass('show');
        $('body').append($menu);

        var btnRect = $btn[0].getBoundingClientRect();
        $menu.css({
            position: 'fixed',
            top: btnRect.bottom + 'px',
            left: btnRect.left + 'px',
            zIndex: 99999,
            display: 'block'
        });

        setTimeout(function () {
            var menuHeight = $menu.outerHeight();
            var menuWidth = $menu.outerWidth();
            var windowHeight = $(window).height();
            var windowWidth = $(window).width();

            if (btnRect.bottom + menuHeight > windowHeight) {
                $menu.css('top', (btnRect.top - menuHeight) + 'px');
            }
            if (btnRect.left + menuWidth > windowWidth) {
                $menu.css('left', (windowWidth - menuWidth - 10) + 'px');
            }
            if (parseFloat($menu.css('left')) < 0) {
                $menu.css('left', '10px');
            }
        }, 0);

        $btn.data('menu-open', true);
    });

    $(document).on('click', '#detached-action-menu .dropdown-item', function (e) {
        var $item = $(this);
        var id = $item.data('id') || $item.attr('id');
        var href = $item.attr('href');
        var target = $item.attr('target') || '_self';
        var isRealLink = href && href !== '#';

        e.preventDefault();
        e.stopPropagation();

        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);

        if (isRealLink) {
            
            window.open(href, target, 'noopener');
            return;
        }

        var actionClass = null;
        ['cloneBtn'].forEach(function (cls) {
            if ($item.hasClass(cls)) actionClass = cls;
        });

        if (actionClass && id) {
            t.content.find('.' + actionClass + '[id="' + id + '"]').first().trigger('click');
        }
    });
    $(document).on('scroll', function () {
        $('#detached-action-menu').remove();
        $('.user-list-menu-toggle').data('menu-open', false);
    });
    t.content.on("click", ".cloneSubmit", $.proxy(t.cloneSubmit));
    t.btn.submit.on("click", $.proxy(t.handleSubmit));
    t.content.on("keyup", ".plain-search", $.proxy(t.search));
    t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
    t.filters.wrapper.on("click", ".btn-filter", $.proxy(t.applyFilters));
    t.filters.btnClear.on("click", $.proxy(t.clearFilters));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.content.on("click", ".btn-download", $.proxy(t.download));
    t.content.on("click", ".escalation-bulk-import", $.proxy(t.bulkImport));
    t.content.on("change", ".user-list-page-length", $.proxy(t.changePageLength));
};
