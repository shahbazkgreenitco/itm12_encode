var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.content = $('#main-user-list-wrapper');
    var table = $('#mytable');
    t.validationAttempted = false;

    // ── Modal & Form Elements ──
    t.mdl = t.content.find("#taskModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#taskform");
    t.mdl.frmEl = {};
    t.mdl.frmEl.name = t.mdl.frm.find("#name");
    t.mdl.frmEl.description = t.mdl.frm.find("#description");
    t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
    t.mdl.frmEl.problemCategoryId = t.mdl.frm.find("#problem_category_id");
    t.mdl.frmEl.subCategoryId = t.mdl.frm.find("#sub_category_id");
    t.mdl.frmEl.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.frmEl.assignToType = t.mdl.frm.find("input[name='assignToType']");
    t.mdl.frmEl.assignUser = t.mdl.frm.find("#assignUser");
    t.mdl.frmEl.assignGroup = t.mdl.frm.find("#assignGroup");
    t.mdl.frmEl.status = t.mdl.frm.find("#status");
    t.mdl.frmEl.tat = t.mdl.frm.find("#tat");
    t.mdl.frmEl.task_flow_type = t.mdl.frm.find("#task_flow_type");
    t.mdl.frmEl.is_visible_user = t.mdl.frm.find("#is_visible_user");
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");

    // ── Table Controls ──
    t.searchInput = t.content.find(".plain-search");
    t.pageLength = t.content.find(".userModulePageLenth");
    t.columnVisibilityButton = t.content.find("#columnVisibilityButton");
    t.columnVisibilityControls = t.content.find("#columnVisibilityControls");

    // ── Description Modal ──
    t.descriptonMdl = t.content.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    // ══════════════════════════════════════════════
    // Icons (SVG inline — no external dependency)
    // ══════════════════════════════════════════════
    t.icons = {
        ellipsis: function () {
            return '<svg width="5" height="14" viewBox="0 0 5 20" fill="none"><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"/></svg>';
        },
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        history: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"/></svg>';
        },
        dragHandle: function () {
            return '<i class="bi bi-hand-index-thumb"></i>';
        },
        active: function(){
        return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
        },
        inactive: function(){
        return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
        }    
       
    };

    // ══════════════════════════════════════════════
    // Utility Functions
    // ══════════════════════════════════════════════
    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.isFilledValue = function (value) {
        if (value === null || value === undefined) return false;
        var text = String(value).trim();
        return text !== "" && text.toLowerCase() !== "null" && text.toLowerCase() !== "undefined";
    };

    t.safeDisplayValue = function (value, fallback) {
        return t.isFilledValue(value) ? String(value).trim() : (fallback !== undefined ? fallback : "-");
    };

    t.getUserInitials = function (name) {
        var parts;
        var first;
        var last;

        if (!t.isFilledValue(name)) {
            return "";
        }

        parts = String(name).trim().split(/\s+/);
        first = parts[0] ? parts[0].charAt(0) : "";
        last = parts.length > 1 ? parts[parts.length - 1].charAt(0) : "";
        return (first + last).toUpperCase();
    };

    t.getAvatarHtml = function (name, imageUrl, className) {
        var cssClass = className || "user-list-avatar";

        if (t.isFilledValue(imageUrl)) {
            return '<img src="' + t.escapeHtml(imageUrl) + '" class="' + t.escapeHtml(cssClass) + '" />';
        }

        return '<span class="' + t.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' +
            t.escapeHtml(t.getUserInitials(name)) +
            '</span>';
    };

    t.getPlainText = function (html) {
        var div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    };

    t.truncateHtml = function (html, maxLength) {
        var text = t.getPlainText(html);
        return text.length > maxLength ? text.substring(0, maxLength) : text;
    };

    t.renderExpandableContent = function (html, maxLength) {
        var text = t.getPlainText(html);
        var truncated;

        if (text.length <= maxLength) {
            return '<div class="b5-text">' + html + '</div>';
        }

        truncated = text.substring(0, maxLength);
        return [
            '<div class="b5-text">',
                t.escapeHtml(truncated),
                '<a href="#" class="read-more" data-full-text="', t.escapeHtml(html), '">...Read More</a>',
            '</div>'
        ].join("");
    };

    t.getIcon = function (key) {
        return (t.icons && t.icons[key]) ? t.icons[key]() : "";
    };

    t.isEnabledStatus = function (value) {
        var normalized = String(value == null ? "" : value).trim().toLowerCase();
        return value === 1 || value === true || normalized === "1" || normalized === "enable" || normalized === "enabled" || normalized === "active" || normalized === "true";
    };

    t.renderStatusCell = function (value, type) {
        var isEnabled = t.isEnabledStatus(value);
        var label = isEnabled ? "Enable" : "Disable";
        var icon = isEnabled ? t.getIcon("active") : t.getIcon("inactive");

        if (type !== "display") {
            return label;
        }

        return '<span class="d-flex justify-content-center align-items-center">' + icon + '<span class="ms-1">' + label + '</span></span>';
    };

    t.hasPermission = function (permission) {
        return $.inArray(permission, t.config.permissions) !== -1;
    };

    // ══════════════════════════════════════════════
    // Action Menu (Dropdown Hover/Focus)
    // ══════════════════════════════════════════════
    t.content.on("mouseenter", ".user-list-actions .dropdown", function (e) {
        t.showActionMenu(e.currentTarget);
    });
    t.content.on("mouseleave", ".user-list-actions .dropdown", function (e) {
        var dropdown = $(e.currentTarget);
        if (!dropdown.length || dropdown.find(document.activeElement).length) return;
        t.scheduleActionMenuHide(dropdown);
    });
    t.content.on("focusin", ".user-list-actions .dropdown", function (e) {
        t.showActionMenu(e.currentTarget);
    });
    t.content.on("focusout", ".user-list-actions .dropdown", function (e) {
        var dropdown = $(e.currentTarget);
        window.setTimeout(function () {
            if (!dropdown.length || dropdown.find(document.activeElement).length) return;
            t.scheduleActionMenuHide(dropdown);
        }, 0);
    });
    t.content.on("click", ".user-list-menu-toggle", function (e) {
        if (e) e.preventDefault();
        if (e && e.currentTarget && typeof e.currentTarget.blur === "function") {
            e.currentTarget.blur();
        }
    });
    t.content.on("click", ".user-list-actions .action-dropdown-menu .dropdown-item", function (e) {
        t.hideActionMenu($(e.currentTarget).closest(".dropdown"));
    });

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

    // ══════════════════════════════════════════════
    // Action Cell Renderer & Action Item HTML
    // ══════════════════════════════════════════════
    t.actionItemHtml = function (label, cls, id, iconKey, href, target) {
        var attrs = href
            ? ' href="' + t.escapeHtml(href) + '"'
            : ' href="#" data-id="' + t.escapeHtml(id) + '"';

        if (target) {
            attrs += ' target="' + t.escapeHtml(target) + '"';
        }

        return [
            '<li>',
            '<a class="dropdown-item ', cls, '"', attrs, '>',
            t.getIcon(iconKey),
            '<span class="b3-text">', t.escapeHtml(label), '</span>',
            '</a>',
            '</li>'
        ].join("");
    };

    t.renderActionsCell = function (record, type) {
        var allActions = [];
        var dropdownId;

        if (type !== "display") {
            return "";
        }

        if (t.hasPermission("TaskEdit")) {
            allActions.push(t.actionItemHtml(
                t.config.translations.Edit_Category || "Edit Task",
                "dtActEdit open-edit-modal",
                record.id,
                "edit"
            ));
        }
        if (t.hasPermission("TaskDelete")) {
            allActions.push(t.actionItemHtml(
                t.config.translations.Delete_Category || "Delete Task",
                "dtActDel open-delete",
                record.id,
                "delete"
            ));
        }
        if (t.hasPermission("TaskHistory")) {
            allActions.push(t.actionItemHtml(
                t.config.translations.History || "History Task",
                "dtActEscl dtActhistory",
                record.id,
                "history",
                config.url.taskHistory + "/" + record.id,
                "_blank"
            ));
        }

        // ── No permissions at all — show drag handle only ──
        if (!allActions.length) {
            return [
                '<div class="user-list-actions d-flex align-items-center gap-2">',
                    '<button type="button" class="user-list-action-btn action-link handle drag_content user-list-menu-toggle" style="color:unset;" ',
                        'data-id="', t.escapeHtml(record.id), '" ',
                        'data-order="', t.escapeHtml(record.order || 0), '" ',
                        'data-bs-toggle="tooltip" ',
                        'title="Drag to reorder">',
                        t.getIcon("dragHandle"),
                    '</button>',
                    '<span class="user-list-empty">-</span>',
                '</div>'
            ].join("");
        }

        dropdownId = "user-table-action-dropdown-" + record.id;

        return [
            '<div class="user-list-actions d-flex align-items-center gap-2">',
                '<button type="button" class="user-list-action-btn action-link handle drag_content" ',
                    'data-id="', t.escapeHtml(record.id), '" ',
                    'data-order="', t.escapeHtml(record.order || 0), '" ',
                    'data-bs-toggle="tooltip"',
                    'title="Drag to reorder">',
                    t.getIcon("dragHandle"),
                '</button>',

                '<div class="dropdown">',
                    '<button type="button" ',
                        'class="action-link user-list-menu-toggle" ',
                        'id="', dropdownId, '" ',
                        'aria-expanded="false" ',
                        'aria-haspopup="true" ',
                        'aria-label="More actions">',
                        t.getIcon("ellipsis"),
                    '</button>',
                    '<ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up" ',
                        'aria-labelledby="', dropdownId, '">',
                        allActions.join(""),
                    '</ul>',
                '</div>',

            '</div>'
        ].join("");
    };

    // ══════════════════════════════════════════════
    // TAT Validation on Keyup
    // ══════════════════════════════════════════════
    t.mdl.frmEl.tat.on('keyup change', function () {
        var taskTat = parseFloat($(this).val());
        var pcTat = parseFloat(t.config.pc.tat);

        if (!isNaN(pcTat) && !isNaN(taskTat)) {
            if (taskTat > pcTat) {
                sweetAlert(
                    'center',
                    'error',
                    { msg: 'Task TAT (' + taskTat + ' hrs) cannot be greater than Category TAT (' + pcTat + ' hrs).' }
                );
                $(this).focus();
            }
        }
    });

    // ══════════════════════════════════════════════
    // DataTable Initialization
    // ══════════════════════════════════════════════
    t.dTbl = table.DataTable({
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
        autoWidth: false,
        colReorder: true,
        scrollX: true,
        rowReorder: {
            selector: '.handle',
            update: false,
        },
        aoColumnDefs: [
            { "orderable": true, "targets": 0 }
        ],
        order: [[12, 'desc']],
        processing: true,
        serverSide: true,
      
        ajax: {
            url: t.config.url.getTaskList,
            type: "get",
            data: function (d) {
                d.token = t.config.token;
                d.pc_id = t.config.pc.id;
            }
        },
          fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',

        lengthChange: false,
        searching: true,
        columns: [
            {
                data: 'a.id',
                name: 'td.id',
                orderable: true
            },
            {
                data: 'a.task_name',
                name: 'td.name',
                render: function (data, type, row) {
                    if (!data) {
                        return '';
                    }
                    var str = String(data);
                    if (str.length > 30) {
                        var truncated = str.substring(0, 30);
                        return '<div class= "b5-text" data-bs-toggle="tooltip" data-bs-original-title="' + t.escapeHtml(str) + '">' +
                            t.escapeHtml(truncated) + '...' +
                            '</div>';
                    } else {
                        return '<div class= "b5-text" >' + t.escapeHtml(str) + '</div>';
                    }
                }
            },
            { data: 'a.department_name',
                name: 'dept.name',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }                
            },
            { data: 'a.problem_category_name',
                name: 'pc.name',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            { data: 'a.sub_category_name',
                name: 'spc.name',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            {
                data: 'a.description',
                name: 'td.description',
                className: "amg-table-col-264",
                render: function (data, type, row) {
                    if (!data) {
                        return '';
                    }
                    var str = String(data);
                    return t.renderExpandableContent(str, 30);
                }
            },
            { data: 'a.assign_to_text',
                name: 'assign_to_text',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            { data: 'a.assigned_to_name',
                name: 'assigned_to_name',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            { data: 'a.tat',
                name: 'td.tat',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            {
                data: 'a.task_flow_type',
                name: 'td.task_flow_type',
                render: function (data, type, row) {
                    if (data == 1) {
                        return '<span class="b5-text">Sequential</span>';
                    } else if (data == 2) {
                        return '<span class="b5-text">Bulk</span>';
                    }
                    return '<span>-</span>';
                }
            },
            { data: 'a.status',
                name: 'status',
                render: function (data, type) {
                    return t.renderStatusCell(data, type);
                }
            },
            { data: 'a.created_at',
                name: 'td.created_at',
                className: "amg-table-col-176",
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            { data: 'a.updated_at',className: "amg-table-col-176",
                name: 'td.updated_at',
                render: function (data) {
                    return `<div class="b5-text">${data || ''}</div>`;
                }
            },
            {
                data: 'a',
                orderable: false,
                searchable: false,
                width: "148px",
                className: "amg-col-actions",
                render: function (data, type, row) {
                    return t.renderActionsCell(row.a || {}, type);
                }
            }
        ],
        drawCallback: function (settings) {
            var $wrapper = $(this).closest('.dataTables_wrapper');

            $wrapper.find('.dataTables_info').css({
                'float': 'left',
                'margin-top': '15px'
            });

            $(this).find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            $("#mytable_wrapper").removeClass("form-inline");
            $('#mytable').wrap('<div class="table-responsive-custom"></div>');
            $("#mytable_filter, #mytable_length").hide();

            // ── Page Length ──
            t.pageLength.val(api.page.len());
            t.pageLength.off("change.taskTable").on("change.taskTable", function () {
                var length = parseInt($(this).val(), 10);
                if (!isNaN(length)) {
                    api.page.len(length).draw();
                }
            });

            // ── Search Input ──
            t.searchInput.off("keyup.taskTable").on("keyup.taskTable", function (e) {
                if (e.keyCode === 13 || this.value.length === 0) {
                    if (typeof $(this).validate_str_param === 'function') {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert(config.translations.please_enter_valid_search || "Please enter valid search text.");
                            return false;
                        }
                    }
                    api.search(this.value).draw();
                }
            });

            // ── Column Visibility ──
            function updateColumnVisibilityControls() {
                var controls = t.columnVisibilityControls.empty();

                api.columns().every(function () {
                    var columnIndex = this.index();
                    var columnTitle = $(this.header()).text().trim();
                    var columnId = 'column-toggle-' + columnIndex;

                    controls.append(
                        '<div class="dropdown-item" style="padding:6px">' +
                            '<label for="' + columnId + '">' +
                                '<input type="checkbox" class="form-check-input" id="' + columnId + '" data-column="' + columnIndex + '" ' + (this.visible() ? 'checked' : '') + '> ' + columnTitle +
                            '</label>' +
                        '</div>'
                    );
                });
            }

            updateColumnVisibilityControls();

            t.columnVisibilityControls.off("change.taskTable").on("change.taskTable", 'input[type="checkbox"]', function (e) {
                e.stopPropagation();
                api.column(+$(this).data('column')).visible(this.checked);
                api.columns.adjust().draw(false);
            });

            api.on("column-reorder.dt.taskTable column-visibility.dt.taskTable", function () {
                setTimeout(updateColumnVisibilityControls, 10);
            });
        }
    });

    // ══════════════════════════════════════════════
    // Row Reorder Handler
    // ══════════════════════════════════════════════
    t.dTbl.on('row-reorder.dt', function (e, diff) {
        if (!diff.length) return;
        let updatedData = [];
        diff.forEach(function (item) {
            let rowData = t.dTbl.row(item.node).data();

            updatedData.push({
                id: rowData.a.id,
                order: item.newPosition + 1,
                position: item.newPosition + 1
            });
        });

        $.ajax({
            url: t.config.url.reorder,
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || t.config.token
            },
            data: JSON.stringify(updatedData),
            success: function (response) {
                if (response.status === "success") {
                    sweetAlert('center', 'success', {
                        msg: 'Order updated.'
                    });

                    t.dTbl.ajax.reload(null, false);
                } else {
                    sweetAlert('center', 'error', {
                        msg: response.msg || 'Failed to update order.'
                    });

                    t.dTbl.ajax.reload(null, false);
                }
            },
            error: function () {
                sweetAlert('center', 'error', {
                    msg: 'Error updating order.'
                });

                t.dTbl.ajax.reload(null, false);
            }
        });
    });

    // ══════════════════════════════════════════════
    // Modal Form Validation Setup
    // ══════════════════════════════════════════════
    t.prepareModalFieldLayout = function () {
        if (!t.mdl || !t.mdl.frm || !t.mdl.frm.length) return;

        t.mdl.frm.find(".amg-form-field").each(function () {
            var field = $(this);
            field.addClass("amg-form-field-row");

            if (!field.children(".amg-form-error-wrap").length) {
                field.append('<div class="amg-form-error-wrap"></div>');
            }
        });
    };

    t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        if (!row.length) {
            return $();
        }
        return row.children(".amg-form-error-wrap");
    };

    t.updateValidationState = function (element, hasError) {
        var field = element.closest(".amg-form-field-row");
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");
        var noteEditor = field.find(".note-editor");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }
        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
        if (noteEditor.length) {
            noteEditor.toggleClass("amg-form-invalid", !!hasError);
        }
    };

    t.clearFieldError = function (element) {
        if (!element || !element.length) {
            return;
        }

        var fieldName = element.attr("name");
        var errorWrap = t.getModalErrorWrap(element);

        if (errorWrap.length) {
            errorWrap.empty();
        }

        t.updateValidationState(element, false);

        if (t.frmValidator && fieldName) {
            delete t.frmValidator.invalid[fieldName];
            delete t.frmValidator.submitted[fieldName];
        }
    };

    t.clearValidationErrors = function () {
        if (t.frmValidator) {
            t.frmValidator.resetForm();
        }

        t.validationAttempted = false;
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frm.find(".amg-form-error-wrap").empty();
    };

    t.shouldValidateField = function (element) {
        var fieldName = $(element).attr("name");

        if (t.validationAttempted) {
            return true;
        }

        return !!(t.frmValidator && fieldName && t.frmValidator.invalid[fieldName]);
    };

    t.showServerValidationErrors = function (errors) {
        var normalizedErrors = {};
        t.validationAttempted = true;

        $.each(errors || {}, function (field, messages) {
            if ($.isArray(messages) && messages.length) {
                normalizedErrors[field] = messages[0];
            } else if (typeof messages === "string" && messages.length) {
                normalizedErrors[field] = messages;
            }
        });

        if ($.isEmptyObject(normalizedErrors)) {
            return;
        }

        t.frmValidator.showErrors(normalizedErrors);

        var firstFieldName = Object.keys(normalizedErrors)[0];
        var firstField = t.mdl.frm.find('[name="' + firstFieldName + '"]').first();

        if (firstField.length) {
            if (firstField.attr("name") === "description") {
                t.mdl.find(".note-editable").focus();
            } else {
                firstField.trigger("focus");
            }
        }
    };

    t.prepareModalFieldLayout();

    t.frmValidator = t.mdl.frm.validate({
        ignore: [],
        debug: false,
        rules: {
            name: {
                required: true,
                maxlength: 255,
                clean_text_only: true,
                noSpecialStart: true,
            },
            description: {
                required: true,
                summernotes: true,
                maxSummernoteChars: 2000
            },
            department_id: {
                required: true,
            },
            problem_category_id: {
                required: true,
            },
            status: {
                required: true,
            },
            tat: {
                required: true,
                digits: true,
                min: 0,
                max: 1000,
            },
            assignUser: {
                required: {
                    depends: function () {
                        return $('input[name="assignToType"]:checked').val() == 1;
                    }
                }
            },
            assignGroup: {
                required: {
                    depends: function () {
                        return $('input[name="assignToType"]:checked').val() == 2;
                    }
                }
            },
            task_flow_type: {
                required: true,
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                errorWrap.empty();
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

    t.mdl.frm.on("change keyup", "input, select, textarea", function () {
        if ($(this).attr("name") && t.shouldValidateField(this)) {
            t.frmValidator.element(this);
        }
    });

    // ══════════════════════════════════════════════
    // Summernote Editor
    // ══════════════════════════════════════════════
    t.mdl.frmEl.description.summernote({
        inheritPlaceholder: true,
        placeholder: "Enter Description",
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        callbacks: {
            onChange: function () {
                if (t.shouldValidateField(t.mdl.frmEl.description[0])) {
                    t.frmValidator.element(t.mdl.frmEl.description[0]);
                }
            }
        }
    });

    // ══════════════════════════════════════════════
    // Select2 Dropdowns
    // ══════════════════════════════════════════════
    t.mdl.frmEl.departmentId.select2($.extend({ width: "100%", placeholder: "Select Department" }, {
        dropdownParent: t.mdl.frmEl.departmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    pc_id: t.config.pc.id
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.has_more === true
                    }
                };
            },
        },
        allowClear: true,
        delay: 200,
        placeholder: config.translations.connect,
        templateSelection: function (s, container) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            $(s.element).attr('tkt_auto_creation_id', s.tkt_auto_creation_id);
            return s.text;
        },
    })).on("change", function (e) {
        t.fun.reload_problem_category();
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.problemCategoryId.select2({
        width: "100%",
        dropdownParent: t.mdl.frmEl.problemCategoryId.parent(),
        placeholder: "Select Problem Category"
    }).on("change", function (e) {
        t.fun.reload_sub_category();
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.subCategoryId.select2($.extend({ width: "100%", placeholder: "Select Sub Category" }, {
        dropdownParent: t.mdl.frmEl.subCategoryId.parent(),
        ajax: {
            url: t.config.url.sub_category,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    id: [t.mdl.frmEl.problemCategoryId.val()]
                };
            },
            delay: 200,
            processResults: function (data) {
                return {
                    results: $.map(data.results, function (item) {
                        if (item.status === 0) {
                            return null;
                        }
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }
        },
        placeholder: config.translations.select_sub_category,
    })).on("change", function () {
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.status.select2({
        width: '100%',
        dropdownParent: t.mdl.frmEl.status.parent(),
        allowClear: true,
        placeholder: "Select Status"
    }).on('select2:select select2:clear change', function () {
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.task_flow_type.select2({
        width: '100%',
        dropdownParent: t.mdl.frmEl.task_flow_type.parent(),
        allowClear: true,
        placeholder: "Select Task Flow"
    }).on('select2:select select2:clear change', function () {
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.assignUser.select2($.extend({}, {
        dropdownParent: t.mdl.frmEl.assignUser.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
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
        placeholder: "Select Assign User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data);
        },
        templateSelection: function (data) {
            return t.safeDisplayValue(data && data.text, "");
        }
    })).on('change', function () {
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.mdl.frmEl.assignGroup.select2($.extend({}, {
        dropdownParent: t.mdl.frmEl.assignGroup.parent(),
        width: "100%",
        ajax: {
            url: t.config.url.getAllocationGroups,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    department_id: t.config.pc.department_id
                };
            },
            delay: 200
        },
        allowClear: true,
        placeholder: 'Select Assign Group'
    })).on('change', function () {
        if (t.shouldValidateField(this)) {
            $(this).valid();
        }
    });

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        if (!s) {
            return $("<div>No data</div>");
        }

        var name = t.safeDisplayValue(s.text, "-");
        var email = t.safeDisplayValue(s.email, "");
        var empNo = t.safeDisplayValue(s.employee_num, "");
        var tName = name.length > 35 ? name.substring(0, 35) + "..." : name;
        var tEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
        var imageUrl = t.safeDisplayValue(s.img_path || s.profile_img, "");
        var avatarHtml = t.getAvatarHtml(name, imageUrl, "user-list-avatar");

        var html = [
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + t.escapeHtml(tName) + '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-60"><i class="bi bi-envelope me-1"></i>' + t.escapeHtml(tEmail) + '</span>' : "",
                    empNo ? '<span class="b1-text opacity-60"><i class="bi bi-credit-card me-1"></i>' + t.escapeHtml(empNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join("");

        return $(html);
    };

    
    t.config.add = false;
    t.config.edit = false;

    t.openModel = function (e) {
        t.config.add = true;
        t.config.edit = false;
        t.resetForm();
        t.httpPostPath = config.url.ajaxAddTask;
        t.mdl.title.html('Add Task');
        if (t.config.pc) {
            var option = new Option(t.config.pc.department.name, t.config.pc.department.id, true, true);
            t.mdl.frmEl.departmentId.append(option).trigger('change');
        }
        $("#taskAssignToNone").removeClass('hide');
        t.mdl.modal('show');
    };

    t.assigntoSetting = function () {
        $('.assign-assignee-row').show();
        if ($(this).val() == 1) {
            $('.assign-user').removeClass('hide');
            $('.assign-group').addClass('hide');
        } else if ($(this).val() == 2) {
            $('.assign-user').addClass('hide');
            $('.assign-group').removeClass('hide');
        } else {
            $('.assign-user').addClass('hide');
            $('.assign-group').addClass('hide');
            $('.assign-assignee-row').hide();
        }

        t.clearFieldError(t.mdl.frmEl.assignUser);
        t.clearFieldError(t.mdl.frmEl.assignGroup);
    };

    t.loadForm = function (obj) {
        t.config.add = false;
        t.config.edit = true;
        t.config.edit_pc_name = '';
        t.config.edit_pc_id = '';
        t.config.edit_spc_name = '';
        t.config.edit_spc_id = '';
        t.mdl.frmEl.name.val(obj.name);
        t.mdl.frmEl.description.summernote("code", obj.description);
        t.mdl.frmEl.status.val(obj.status_id).trigger('change');
        t.mdl.frmEl.tat.val(obj.tat);
        t.mdl.frmEl.task_flow_type.val(obj.task_flow_type).trigger('change');
        t.mdl.frmEl.assignToType.filter('[value="' + obj.assign_to + '"]').prop('checked', true).trigger('change');
        if (obj.assign_to == 1) {
            var newOption = new Option(obj.assigned_to_name, obj.assigned_to_id, true, true);
            t.mdl.frmEl.assignUser.append(newOption).trigger('change');
            t.mdl.frmEl.assignGroup.val(null).trigger('change');
        } else if (obj.assign_to == 2) {
            var newOption = new Option(obj.assigned_to_name, obj.assigned_to_id, true, true);
            t.mdl.frmEl.assignGroup.append(newOption).trigger('change');
            t.mdl.frmEl.assignUser.val(null).trigger('change');
        }
        if (obj.is_visible_user == 1) {
            t.mdl.frmEl.is_visible_user.prop('checked', true);
        } else {
            t.mdl.frmEl.is_visible_user.prop('checked', false);
        }
        if (obj.department && obj.department) {
            var newOption = new Option(obj.department.name, obj.department.id, true, true);
            t.mdl.frmEl.departmentId.empty().append(newOption).trigger('change');
        }
        if (obj.problem_category) {
            t.config.edit_pc_name = obj.problem_category.name;
            t.config.edit_pc_id = obj.problem_category.id;
        }
        if (obj.sub_category) {
            t.config.edit_spc_id = obj.sub_category.id;
            t.config.edit_spc_name = obj.sub_category.name;
        }

        t.httpPostPath = t.config.url.updateTask + '/' + obj.id;
    };

    t.editModel = function (e) {
        e.preventDefault();
        t.resetForm();
        var id = $(this).data('id');

        var http = $.ajax({
            url: config.url.editTask,
            type: 'GET',
            data: { id: id }
        });
        http.done(function (response) {
            if (response.success) {
                t.mdl.title.html('Edit Task');
                t.loadForm(response.data);
                $("#taskAssignToNone").addClass('hide');
                t.mdl.modal('show');
            } else {
                alert('No data found!');
            }
        });
        http.fail(function (xhr) {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
        });
        http.always(function () {
            console.log('Request completed.');
        });
    };

    t.handleSubmit = function (e) {
        e.preventDefault();
        var taskTat = parseFloat(t.mdl.frmEl.tat.val());
        var pcTat = parseFloat(t.config.pc.tat);
        t.validationAttempted = true;

        if (!isNaN(pcTat) && !isNaN(taskTat)) {
            if (taskTat > pcTat) {
                sweetAlert(
                    'center',
                    'error',
                    { msg: 'Task TAT (' + taskTat + ' hrs) cannot be greater than Category TAT (' + pcTat + ' hrs).' }
                );
                t.mdl.frmEl.tat.focus();
                return false;
            }
        }
        if (t.frmValidator.form() == false) {
            return false;
        }

        t.mdl.btnSubmit.prop("disabled", true);

        var formData = new FormData(t.mdl.frm[0]);
        formData.append('pc_id', t.config.pc.id);

        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.refreshTaskList();
                } else if (data.errors) {
                    t.showServerValidationErrors(data.errors);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function (xhr) {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
        });
        http.always(function () {
            t.mdl.btnSubmit.prop("disabled", false);
        });
    };

    t.refreshTaskList = function () {
        if (t.dTbl && t.dTbl.ajax) {
            t.dTbl.ajax.reload(null, false);
        }
    };

    t.resetForm = function () {
        t.mdl.frm[0].reset();
        t.mdl.frmEl.name.val("");
        t.mdl.frmEl.description.summernote("code", "");
        t.mdl.frmEl.departmentId.val(null).trigger('change');
        t.mdl.frmEl.problemCategoryId.val(null).trigger('change');
        t.mdl.frmEl.subCategoryId.val(null).trigger('change');
        t.mdl.frmEl.tat.val("");
        t.mdl.frmEl.status.val(null).trigger("change");
        t.mdl.frmEl.assignToType.prop('checked', false).trigger('change');
        t.mdl.frmEl.assignUser.val(null).trigger('change');
        t.mdl.frmEl.assignGroup.val(null).trigger('change');
        t.mdl.frmEl.is_visible_user.prop('checked', false);
        t.mdl.frmEl.task_flow_type.val(null).trigger("change");
        $(".assign-user").addClass("hide");
        $(".assign-group").addClass("hide");
        $('.assign-assignee-row').hide();
        t.clearValidationErrors();
    };

    // ══════════════════════════════════════════════
    // Reload & Delete & Download
    // ══════════════════════════════════════════════
    t.getValidatedSearchValue = function (showError) {
        var value = "";

        if (typeof t.searchInput.validate_str_param === 'function') {
            value = t.searchInput.validate_str_param();
            if (value === false) {
                if (showError) {
                    alert(config.translations.please_enter_valid_search || "Please enter valid search text.");
                }
                return false;
            }
        } else {
            value = $.trim(t.searchInput.val() || "");
        }

        return $.trim(value || "");
    };

    t.applySearch = function (e) {
        var searchVal;

        if (e) {
            e.preventDefault();
        }

        searchVal = t.getValidatedSearchValue(true);
        if (searchVal === false) {
            return false;
        }

        t.searchInput.val(searchVal);
        t.dTbl.search(searchVal).draw();
        return false;
    };

    t.reload = function (e) {
        var searchVal;

        if (e) {
            e.preventDefault();
        }

        searchVal = t.getValidatedSearchValue(false);
        if (searchVal === false) {
            t.dTbl.ajax.reload(null, false);
            return false;
        }

        t.searchInput.val(searchVal);
        t.dTbl.search(searchVal);
        t.dTbl.ajax.reload(null, false);
        return false;
    };

    t.delete = function () {
        var id = $(this).data('id');
        var url = t.config.url.deleteTask + '/' + id;
        var data = {
            msg: config.translations.something_went_wrong
        };
        sweetAlerts('Are you sure you want to delete?', 'warning', url, t.dTbl, data);
    };

    t.download = function (e) {
        e.preventDefault();
        var current_search = t.searchInput.val();
        var export_filters = {
            search: current_search,
            other_filters: {
                pc_id: t.config.pc.id,
            }
        };
        var encoded_filters = btoa(JSON.stringify(export_filters));
        window.location = t.config.url.exportTasks + "?q=" + encoded_filters;
    };

    // ══════════════════════════════════════════════
    // Problem Category / Sub Category Reload
    // ══════════════════════════════════════════════
    var problem_categories = [];

    t.fun = {
        reload_problem_category: function (obj) {
            var department = t.mdl.frmEl.departmentId.val();
            t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
            t.mdl.frmEl.problemCategoryId.empty().append(new Option("", "", false, false));
            t.mdl.frmEl.subCategoryId.empty().append(new Option("", "", false, false));

            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        problem_categories = data.data;
                        $.each(data.data, function (i, k) {
                            if (k.status != 0) {
                                var truncatedName = k.name.length > 40 ? k.name.substring(0, 40) + "..." : k.name;
                                var option = new Option(truncatedName, k.id, false, false);
                                $(option).attr("title", k.name);
                                t.mdl.frmEl.problemCategoryId.append(option);
                            }
                        });
                        if (t.config.pc && t.config.pc.parent_id == null && t.config.add) {
                            t.mdl.frmEl.problemCategoryId.val(t.config.pc.id).trigger('change');
                        } else if (t.config.pc && t.config.pc.problem_category && t.config.pc.problem_category.id && t.config.add) {
                            t.mdl.frmEl.problemCategoryId.val(t.config.pc.problem_category.id).trigger('change');
                        } else if (t.config.edit_pc_id && t.config.edit_pc_name && t.config.edit) {
                            t.mdl.frmEl.problemCategoryId.val(t.config.edit_pc_id).trigger('change');
                        }
                    }
                });
            }
        },
        reload_sub_category: function () {
            var selectedPcIds = t.mdl.frmEl.problemCategoryId.val() || [];
            t.mdl.frmEl.subCategoryId.empty().append(new Option("", "", false, false));

            if (!selectedPcIds.length) {
                t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
                return;
            }

            var item = problem_categories.find(function (pc) { return pc.id == selectedPcIds; });
            var hasSubCategory = item && Array.isArray(item.sub) && item.sub.length > 0;

            if (hasSubCategory) {
                t.mdl.frmEl.subCategoryIdCvr.removeClass('hide');
                if (t.config.pc.parent_id == null && t.config.add) {
                    t.mdl.frmEl.subCategoryId.val(null).trigger('change');
                } else if (t.config.pc.name != null && t.config.pc.id != null && t.config.add) {
                    t.config.add = false;
                    if (!t.mdl.frmEl.subCategoryId.find("option[value='" + t.config.pc.id + "']").length) {
                        var option = new Option(t.config.pc.name, t.config.pc.id, true, true);
                        t.mdl.frmEl.subCategoryId.append(option);
                        t.mdl.frmEl.subCategoryId.val(t.config.pc.id).trigger('change');
                    }
                } else if (t.config.edit_spc_name && t.config.edit_spc_id && t.config.edit) {
                    t.config.edit = false;
                    if (!t.mdl.frmEl.subCategoryId.find("option[value='" + t.config.edit_spc_id + "']").length) {
                        var option = new Option(t.config.edit_spc_name, t.config.edit_spc_id, true, true);
                        t.mdl.frmEl.subCategoryId.append(option);
                    }
                    t.mdl.frmEl.subCategoryId.val(t.config.edit_spc_id).trigger('change');
                } else {
                    t.mdl.frmEl.subCategoryId.trigger('change');
                }
            } else {
                t.mdl.frmEl.subCategoryIdCvr.addClass('hide');
                t.mdl.frmEl.subCategoryId.empty().append(new Option("Select Sub Category", "", false, false));
            }
        }
    };

    // ══════════════════════════════════════════════
    // Column Visibility Toggle
    // ══════════════════════════════════════════════
    t.toggleColumnVisibilityMenu = function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.columnVisibilityControls.toggleClass("show");
    };

    t.closeColumnVisibilityMenu = function () {
        t.columnVisibilityControls.removeClass("show");
    };

    // ══════════════════════════════════════════════
    // Event Bindings
    // ══════════════════════════════════════════════
    t.content.on('click', '.open-add-modal', $.proxy(t.openModel));
    t.content.on('click', '.btn-searchbox', $.proxy(t.applySearch));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editModel));
    t.content.on('click', '.open-delete', $.proxy(t.delete));
    t.mdl.frmEl.assignToType.on('change', $.proxy(t.assigntoSetting));
    t.content.on("click", '.btn-download', $.proxy(t.download));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));

    t.content.on("click", ".read-more", function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        if (window.bootstrap && bootstrap.Modal) {
            (bootstrap.Modal.getInstance(t.descriptonMdl[0]) || new bootstrap.Modal(t.descriptonMdl[0])).show();
            return;
        }
        t.descriptonMdl.modal("show");
    });

    t.columnVisibilityButton.on("click", $.proxy(t.toggleColumnVisibilityMenu, t));
    t.columnVisibilityControls.on("click", function (e) {
        e.stopPropagation();
    });
    $(document).on("click.taskColumnVisibility", function (e) {
        if (!$(e.target).closest(".column-visibility-dropdown").length) {
            t.closeColumnVisibilityMenu();
        }
    });
};

// ══════════════════════════════════════════════════
// History Module
// ══════════════════════════════════════════════════
var History = function (config) {
    var t = this;
    t.config = config;
    t.content = $('#main-user-list-wrapper');
    var table = $('#mytable');
    t.searchInput = t.content.find(".plain-search");
    t.pageLength = t.content.find(".userModulePageLenth");

    t.descriptonMdl = t.content.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    // ── Entity Map (declared BEFORE escapeHtml) ──
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

    t.icons={
        active: function(){
            return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
            },
        inactive: function(){
            return `<svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>`
        }    
    }

    t.escapeHtml = function (text) {
        return String(text == null ? "" : text).replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
    };

    t.getPlainText = function (html) {
        var div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    };

    t.truncateHtml = function (html, maxLength) {
        var text = t.getPlainText(html);
        return text.length > maxLength ? text.substring(0, maxLength) : text;
    };

    t.renderExpandableContent = function (html, maxLength) {
        var text = t.getPlainText(html);
        var truncated;

        if (text.length <= maxLength) {
            return html;
        }

        truncated = text.substring(0, maxLength);
        return [
                t.escapeHtml(truncated),
                '<a href="#" class="read-more" data-full-text="', t.escapeHtml(html), '">...Read More</a>',
        ].join("");
    };

    t.isEnabledStatus = function (value) {
        var normalized = String(value == null ? "" : value).trim().toLowerCase();
        return value === 1 || value === true || normalized === "1" || normalized === "enable" || normalized === "enabled" || normalized === "active" || normalized === "true";
    };

    t.renderStatusCell = function(value, type) {
        var isEnabled = t.isEnabledStatus(value);
        var label = isEnabled ? "Enable" : "Disable";
        var icon = isEnabled ? t.icons.active() : t.icons.inactive();

        if (type !== "display") {
            return label;
        }

        return '<span class="d-flex justify-content-center align-items-center">' + icon + '<span class="ms-1">' + label + '</span></span>';
    };

    t.dTbl = table.DataTable({
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
        autoWidth: false,
        colReorder: true,
        order: [
            [7, 'desc']
        ],
        processing: true,
        serverSide: true,
        lengthChange:false,
        searching:false,
        scrollX: true,
        
        ajax: {
            url: t.config.url.getTaskHistory,
            type: "get",
            data: function (d) {
                d.task_id = t.config.id;
            }
        },
        columns: [
            { data: 'a.id' },
            {
                data: 'a.task_name',
                render: function (data, type, row) {
                    if (!data) {
                        return '';
                    }
                    var str = String(data);
                    if (str.length > 30) {
                        var truncated = t.truncateHtml(str, 30);
                        return '<div data-bs-toggle="tooltip" data-bs-original-title="' + t.escapeHtml(str) + '">' +
                            t.escapeHtml(truncated) + '...' +
                            '</div>';
                    } else {
                        return t.escapeHtml(str);
                    }
                }
            },
            { data: 'a.department_name' },
            { data: 'a.problem_category_name' },
            { data: 'a.sub_category_name' },
            {
                data: 'a.description',
                render: function (data, type, row) {
                    if (!data) {
                        return '';
                    }
                    var str = String(data);
                    return t.renderExpandableContent(str, 30);
                }
            },
            { data: 'a.assign_to_text' },
            { data: 'a.assigned_to_name' },
            { data: 'a.tat' },
            {
                data: 'a.task_flow_type',
                render: function (data, type, row) {
                    if (data == 1) {
                        return '<span>Sequential</span>';
                    } else if (data == 2) {
                        return '<span>Bulk</span>';
                    }
                    return '<span>-</span>';
                }
            },
            { data: 'a.status',
                render: function (data, type) {
                return t.renderStatusCell(data, type);
            }
             },
            { data: 'a.updated_at' },
            { data: 'a.updated_by_name' }
        ],
        drawCallback: function (settings) {
            var $wrapper = $(this).closest('.dataTables_wrapper');

            $wrapper.find('.dataTables_info').css({
                'float': 'left',
                'margin-top': '15px'
            });

            $(this).find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        dom: "<'dt-top'<'left'f><'right'l>>" +
            "tr" +
            "<'dt-bottom'<'left'i><'right'p>>",
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            $("#mytable_wrapper").removeClass("form-inline");
            $("#mytable_filter, #mytable_length").hide();

            t.pageLength.val(api.page.len());
            t.pageLength.off("change.taskHistory").on("change.taskHistory", function () {
                var length = parseInt($(this).val(), 10);
                if (!isNaN(length)) {
                    api.page.len(length).draw(false);
                }
            });

            t.searchInput.off("keyup.taskHistory").on("keyup.taskHistory", function (e) {
                if (e.keyCode === 13 || this.value.length === 0) {
                    if (typeof $(this).validate_str_param === 'function') {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert(config.translations.please_enter_valid_search || "Please enter valid search text.");
                            return false;
                        }
                    }
                    api.search(this.value).draw();
                }
            });
        }
    });

    t.getValidatedSearchValue = function (showError) {
        var value = "";

        if (typeof t.searchInput.validate_str_param === 'function') {
            value = t.searchInput.validate_str_param();
            if (value === false) {
                if (showError) {
                    alert(config.translations.please_enter_valid_search || "Please enter valid search text.");
                }
                return false;
            }
        } else {
            value = $.trim(t.searchInput.val() || "");
        }

        return $.trim(value || "");
    };

    t.applySearch = function (e) {
        var searchVal;

        if (e) {
            e.preventDefault();
        }

        searchVal = t.getValidatedSearchValue(true);
        if (searchVal === false) {
            return false;
        }

        t.searchInput.val(searchVal);
        t.dTbl.search(searchVal).draw();
        return false;
    };

    t.reload = function (e) {
        var searchVal;

        if (e) {
            e.preventDefault();
        }

        searchVal = t.getValidatedSearchValue(false);
        if (searchVal === false) {
            t.dTbl.ajax.reload(null, false);
            return false;
        }

        t.searchInput.val(searchVal);
        t.dTbl.search(searchVal);
        t.dTbl.ajax.reload(null, false);
        return false;
    };

    t.content.on("click", ".read-more", function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        if (window.bootstrap && bootstrap.Modal) {
            (bootstrap.Modal.getInstance(t.descriptonMdl[0]) || new bootstrap.Modal(t.descriptonMdl[0])).show();
            return;
        }
        t.descriptonMdl.modal("show");
    });

    t.content.on('click', '.btn-searchbox', $.proxy(t.applySearch, t));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
};
