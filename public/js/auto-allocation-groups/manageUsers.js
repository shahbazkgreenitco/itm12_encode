var AutoAllocGroupsMembers = function (config) {
    var t = this;

    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.search = t.config.search || "";
    t.config.permissions = t.config.permissions || [];

    t.page = $("#main-user-list-wrapper");
    t.content = t.page.length ? t.page : $("section.content");
    t.table = t.content.find("#mytable");
    t.pageLength = t.content.find(".user-list-page-length");
    t.searchbox = t.content.find(".plain-search");
    t.actionStyleScope = t.content.find(".list-view-panel").first();

    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.mdl = t.content.find("#addGroupUserFormMdl");
    t.mdl.loader = t.mdl.find("#add_group_user_mdl_loader");
    t.mdltitle = t.mdl.find(".modal-title");
    t.frm = t.mdl.find("#addGroupUserForm");

    t.frmEl = {};
    t.frmEl.maxTicketPerHr = t.frm.find("#max_ticket_per_hr");
    t.frmEl.maxTicketPerDay = t.frm.find("#max_ticket_per_day");
    t.frmEl.groupUserId = t.frm.find("#groupUserId");
    t.frmEl.locationAccess = t.frm.find("#locationaccess");
    t.frmEl.internalLocation = t.frm.find("#internal_location");
    t.frmEl.selectAllLocation = t.frm.find("#selectAllLocation");

    t.btn = {};
    t.btn.submit = t.frm.find("#btnSubmit");

    t.locationMdl = t.content.find("#users_modal");
    t.locationMdl.body = t.locationMdl.find(".modal-body");
    t.initTooltips = function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            let tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) tooltip.dispose();
            new bootstrap.Tooltip(el);
        });
    };

    t.tr = function (key, fallback) {
        var keys = key.split(".");
        var value = t.config.translations;

        for (var i = 0; i < keys.length; i++) {
            if (value == null || typeof value[keys[i]] === "undefined") {
                return fallback;
            }
            value = value[keys[i]];
        }

        return value;
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
        edit: '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>',
        trash: '<i class="bi bi-trash"></i>'
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        return '<button type="button" class="user-list-action-btn role-list-action-btn ' + t.escapeHtml(classes || "") + '"' +
            ' id="' + t.escapeHtml(id) + '"' +
            ' data-id="' + t.escapeHtml(id) + '"' +
            ' title="' + t.escapeHtml(label) + '"' +
            ' data-bs-toggle="tooltip"' +
            ' data-bs-placement="top"'+
            ' aria-label="' + t.escapeHtml(label) + '">' + iconMarkup + '</button>';
    };

    t.renderActionButtons = function (record) {
        var buttons = [];

        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        if (t.hasPermission("TicketAllocationGroupUserEdit")) {
            buttons.push(t.actionButtonHtml(t.tr("manage_user.edit_group_member", "Edit Group Member"), "open-edit-modal", record.id, t.icons.edit));
        }

        if (t.hasPermission("TicketAllocationGroupUserDelete")) {
            buttons.push(t.actionButtonHtml(t.tr("manage_user.delete_group_member", "Delete Group Member"), "open-delete is-delete", record.id, t.icons.trash));
        }

        return buttons.length
            ? '<div class="user-list-actions role-list-actions justify-content-start">' + buttons.join("") + "</div>"
            : '<span class="user-list-empty">-</span>';
    };

    t.renderLocationBadges = function (data) {
        var locations;
        var visibleLocations;
        var html = "";

        if (!data) {
            return "-";
        }

        locations = String(data).split(",").map(function (item) {
            return $.trim(item);
        }).filter(Boolean);

        visibleLocations = locations.slice(0, 2);
        $.each(visibleLocations, function (index, location) {
            var label = location.length > 20 ? location.substring(0, 20) + "..." : location;
            html += '<span class="location-access-item" style="margin:1.5px" title="' + t.escapeHtml(location) + '">' + t.escapeHtml(label) + "</span>";
            if (index < visibleLocations.length - 1 || locations.length > visibleLocations.length) {
                html += ", ";
            }
        });

        if (locations.length > 2) {
            html += '<a class="read-more" style="cursor:pointer" data-full-text="' + t.escapeHtml(data) + '"> +' + (locations.length - 2) + "</a>";
        }

        return html || "-";
    };

    t.renderUserLocation = function (record) {
        var parts = [];

        if (record && record.current_location_name) {
            parts.push("<b>C: </b>" + t.escapeHtml(record.current_location_name));
        }
        if (record && record.base_location_name) {
            parts.push("<b>B: </b>" + t.escapeHtml(record.base_location_name));
        }

        return parts.length ? parts.join("<br/>") : "-";
    };

    t.renderStatus = function (record) {
        if (record && parseInt(record.activity_id, 10) === 0) {
            return '<span class="user-list-status fw-normal b5-text"><svg width="14" height="14" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"></rect><circle cx="8" cy="8" r="3" fill="white"></circle></svg>' + t.escapeHtml(t.tr("active", "Active"));'</span>'
        }
        if (record && parseInt(record.activity_id, 10) === 1) {
            return '<span class="fw-normal b5-text">' + t.escapeHtml(record.activity || t.tr("break", "Break")); '</span>'
        }
        return '<span class="inactive-user"></span> ' + t.escapeHtml(t.tr("logged_out", "Logged Out"));
    };

    t.getModalSelectDropdownParent = function (element) {
        var parent = element.closest(".input-group");
        return parent.length ? parent : t.mdl;
    };

    t.userDropdownFormat = function (s) {
        var name;
        var email;
        var employeeNo;
        var shortName;
        var shortEmail;

        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + t.escapeHtml(s.text) + "</div>");
        }

        name = (s && s.text) || "-";
        email = (s && s.email) || "";
        employeeNo = (s && s.employee_num) || "";
        shortName = name.length > 35 ? name.substring(0, 35) + "..." : name;
        shortEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;

        return $([
            '<div class="d-flex align-items-start gap-3 w-100">',
                s && s.img_path ? '<img class="user-list-avatar" src="' + t.escapeHtml(s.img_path) + '" alt="' + t.escapeHtml(name) + '">' : '<span class="user-list-avatar user-list-avatar-fallback">' + t.escapeHtml(name.substring(0, 2).toUpperCase()) + '</span>',
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + t.escapeHtml(shortName) + '</span>',
                        s && s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' + t.escapeHtml(shortEmail) + '</span>' : "",
                    employeeNo ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' + t.escapeHtml(employeeNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join(""));
    };

    t.initSelects = function () {
        t.frmEl.groupUserId.select2({
            width: "100%",
            ajax: {
                url: t.config.url.getUsers,
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
            placeholder: t.tr("manage_user.select_group_user", "Select Group User"),
            dropdownParent: t.getModalSelectDropdownParent(t.frmEl.groupUserId),
            templateResult: t.userDropdownFormat,
            templateSelection: function (data, container) {
                $(container).attr("title", data.text || "");
                return data.text || "";
            }
        });

        if (t.frmEl.locationAccess.length) {
            t.frmEl.locationAccess.select2({
                width: "100%",
                placeholder: t.tr("manage_user.select_locations", "Select Locations"),
                allowClear: true,
                dropdownParent: t.getModalSelectDropdownParent(t.frmEl.locationAccess),
                ajax: {
                    url: t.config.url.getCompanyWiseLocation,
                    dataType: "json",
                    delay: 300,
                    data: function (params) {
                        return {
                            search: params.term,
                            page: params.page || 1,
                            company_id: t.config.company_id
                        };
                    },
                    processResults: function (response, params) {
                        params.page = params.page || 1;
                        return {
                            results: response.results || [],
                            pagination: {
                                more: response.pagination ? response.pagination.more : false
                            }
                        };
                    }
                }
            });
        }

        if (t.frmEl.internalLocation.length) {
            t.frmEl.internalLocation.select2({
                width: "100%",
                placeholder: t.tr("manage_user.select_internal_place", "Select Internal Place"),
                allowClear: true,
                dropdownParent: t.getModalSelectDropdownParent(t.frmEl.internalLocation)
            });
        }
    };

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

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        ignore: ":hidden:not(.select2-hidden-accessible)",
        rules: {
            groupUserId: {
                required: true
            },
            max_ticket_per_hr: {
                required: true,
                number: true,
                min: 1,
                max: 999,
                lessThanDaily: true
            },
            max_ticket_per_day: {
                required: true,
                number: true,
                min: 1,
                max: 999
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

    $.validator.addMethod("lessThanDaily", function(value, element) {
        var dailyValue = $('input[name="max_ticket_per_day"]').val();
        if (dailyValue === "" || dailyValue === undefined) {
            return true;
        }
        return parseFloat(value) < parseFloat(dailyValue);
    }, "Hourly limit must be less than daily limit");
    $('input[name="max_ticket_per_day"]').on('change keyup', function() {
        $('input[name="max_ticket_per_hr"]').valid();
    });

    t.buildColumns = function () {
        var columns = [
            { data: "user_name", render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            {
                data: null,
                render: function (data, type, row) {
                    return type === "display" ? t.renderUserLocation(row) : "";
                }
            },
            {
                data: "location_name",
                render: function (data, type) {
                    return type === "display" ? t.renderLocationBadges(data) : (data || "");
                }
            }
        ];

        if (parseInt(t.config.autoAllocationFor, 10) === 3) {
            columns.push({
                data: "location_internal_name",
                render: function (data, type) {
                    return type === "display" ? t.renderLocationBadges(data) : (data || "");
                }
            });
        }

        columns.push(
            { data: "max_ticket_per_hr",render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            { data: "max_ticket_per_day",render: function (data) {
                    return `<div class="b5-text">${data || '-'}</div>`;
                } 
            },
            {
                data: null,
                render: function (data, type, row) {
                    return type === "display" ? t.renderStatus(row) : "";
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                width: "96px",
                className: "amg-col-actions role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return type === "display" ? t.renderActionButtons(row) : "";
                }
            }
        );

        return columns;
    };

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        scrollX:true,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        order: [[0, "asc"]],
        deferLoading: 0,
        ajax: {
            url: t.config.url.getUserAllocationGroupMembers,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.groupId = t.config.groupId;
                d.search = {
                    value: t.config.search || $.trim(t.searchbox.val() || "")
                };
            }
        },
        language: {
            emptyTable: t.tr("no_records_found", "No matching records found"),
            zeroRecords: t.tr("no_records_found", "No matching records found")
        },
        columns: t.buildColumns(),
        drawCallback: function () {
            t.initTooltips();
        },
    });

    t.reload = function () {
        var v = typeof t.searchbox.validate_str_param === "function"
            ? t.searchbox.validate_str_param()
            : $.trim(t.searchbox.val() || "");

        if (v === false) {
            t.config.search = "";
            alert(t.tr("please_enter_valid_search", "Please enter a valid value for search"));
            return false;
        }

        t.config.search = v;
        t.dTbl.ajax.reload(null, false);
    };

    t.search = function (e) {
        if (e && e.type === "keyup" && e.keyCode !== 13 && $.trim(t.searchbox.val() || "") !== "") {
            return;
        }

        t.reload();
    };

    t.changePageLength = function () {
        t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw();
    };

    t.resetFrm = function () {
        t.frm[0].reset();
        t.frmValidator.resetForm();
        t.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.frmEl.groupUserId.empty().val(null).trigger("change");
        t.frmEl.locationAccess.empty().val(null).trigger("change");
        t.frmEl.internalLocation.empty().val(null).trigger("change");
        t.frmEl.selectAllLocation.prop("checked", false);
    };

    t.addGroupUser = function (e) {
        if (e) e.preventDefault();

        t.httpPostPath = t.config.url.addGroupUser;
        t.resetFrm();
        t.mdltitle.text(t.tr("add_group_user", "Add Group User"));
        t.btn.submit.text(t.tr("save", "Save"));
        t.btn.submit.prop("disabled", false);
        t.mdl.loader.hide();
        t.frmEl.maxTicketPerHr.val("10");
        t.frmEl.maxTicketPerDay.val("999");
        t.mdl.modal("show");
    };

    t.handleUsersubmit = function (e) {
        var frmData;
        var http;

        e.preventDefault();
        if (t.frmValidator.form() === false) {
            return false;
        }

        frmData = new FormData(t.frm[0]);
        frmData.append("company_id", t.config.company_id);
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

        http.done(function (data) {
            if (data && data.status === "success") {
                sweetAlert("center", "success", data);
                t.mdl.modal("hide");
                t.dTbl.ajax.reload(null, false);
                return;
            }
            sweetAlert("center", "error", data || { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.fail(function () {
            sweetAlert("center", "error", { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.always(function () {
            t.btn.submit.prop("disabled", false);
            t.mdl.loader.hide();
        });
    };

    t.selectLocations = function (items, element) {
        element.empty();
        $.each(items || [], function (_, item) {
            var text = item.text || item.name || item.place;
            if (item.id == null || text == null) return;
            element.append(new Option(text, item.id, true, true));
        });
        element.trigger("change");
    };

    t.getInternalPlace = function (callback) {
        var locationIds = t.frmEl.locationAccess.val();

        if (!t.frmEl.internalLocation.length) {
            if (typeof callback === "function") callback();
            return;
        }

        if (!locationIds || !locationIds.length) {
            t.frmEl.internalLocation.empty().trigger("change");
            if (typeof callback === "function") callback();
            return;
        }

        $.get(t.config.url.getInternalPlaceByAjax + "/" + locationIds.join(",")).done(function (data) {
            t.frmEl.internalLocation.empty();
            $.each((data && data.results) || [], function (_, item) {
                t.frmEl.internalLocation.append(new Option(item.text, String(item.id), false, false));
            });
            t.frmEl.internalLocation.trigger("change");
            if (typeof callback === "function") callback();
        });
    };

    t.loadForm = function (obj) {
        var user;

        t.resetFrm();
        t.frmEl.maxTicketPerHr.val(obj.data.max_ticket_per_hr);
        t.frmEl.maxTicketPerDay.val(obj.data.max_ticket_per_day);

        user = obj.dropdown && obj.dropdown.user_id ? obj.dropdown.user_id : null;
        if (user) {
            t.frmEl.groupUserId
                .append(new Option(user.text, user.id, true, true))
                .trigger("change");
        }

        t.selectLocations(obj.data.location_ids, t.frmEl.locationAccess);
        t.getInternalPlace(function () {
            var ids = $.map(obj.data.location_internal_ids || [], function (item) {
                return String(item.id);
            });
            t.frmEl.internalLocation.val(ids).trigger("change");
        });
    };

    t.editGroupMember = function (e) {
        var groupMemberId;
        var http;

        e.preventDefault();
        groupMemberId = $(e.currentTarget).data("id") || $(e.currentTarget).attr("id");
        t.httpPostPath = t.config.url.editGroupMember + "/" + groupMemberId;
        t.resetFrm();
        t.mdltitle.text(t.tr("manage_user.edit_group_member", "Edit Group Member"));
        t.btn.submit.text(t.tr("save_changes", "Save Changes"));
        t.btn.submit.prop("disabled", true);
        t.mdl.loader.show();
        t.mdl.modal("show");

        http = $.get(t.config.url.getGroupMember + "/" + groupMemberId);
        http.done(function (data) {
            if (data && data.status === "success" && data.data) {
                t.loadForm(data.data);
                return;
            }
            sweetAlert("center", "error", data || { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.fail(function () {
            sweetAlert("center", "error", { msg: t.tr("something_went_wrong", "Something went wrong. Please try again.") });
        });
        http.always(function () {
            t.btn.submit.prop("disabled", false);
            t.mdl.loader.hide();
        });
    };

    t.deleteGroupUser = function (e) {
        var userId;
        var data;

        e.preventDefault();
        userId = $(e.currentTarget).data("id") || $(e.currentTarget).attr("id");
        data = {
            msg: t.tr("something_went_wrong", "Something went wrong. Please try again.")
        };
        sweetAlerts(t.tr("manage_user.confirmation_user", "Are you sure to delete user from group?"), "warning", t.config.url.delete + "/" + userId, t.dTbl, data);
    };

    t.showLocations = function (e) {
        var fullText;
        var locationArray;

        e.preventDefault();
        fullText = $(e.currentTarget).data("full-text") || "";
        locationArray = String(fullText).split(",").map(function (item) {
            return $.trim(item);
        }).filter(Boolean);

        t.locationMdl.body.empty();
        $.each(locationArray, function (index, location) {
            var label = location.length > 20 ? location.substring(0, 20) + "..." : location;
            t.locationMdl.body.append('<span class="badge" style="margin:1.5px" title="' + t.escapeHtml(location) + '">' + t.escapeHtml(label) + "</span>");
            if (index < locationArray.length - 1) {
                t.locationMdl.body.append(", ");
            }
        });
        t.locationMdl.modal("show");
    };

    t.selectAllLocations = function () {
        if (!t.frmEl.selectAllLocation.prop("checked")) {
            t.frmEl.locationAccess.val(null).trigger("change");
            return;
        }

        $.ajax({
            url: t.config.url.getCompanyWiseLocation,
            type: "GET",
            dataType: "json",
            data: {
                company_id: t.config.company_id,
                all: 1
            },
            success: function (response) {
                t.selectLocations((response && response.results) || [], t.frmEl.locationAccess);
            }
        });
    };

    t.initSelects();
    t.dTbl.ajax.reload();

    t.content.on("click", ".open-addGroupUserForm-modal", $.proxy(t.addGroupUser));
    t.content.on("click", ".open-edit-modal", $.proxy(t.editGroupMember));
    t.content.on("click", ".open-delete", $.proxy(t.deleteGroupUser));
    t.content.on("keyup", ".plain-search", $.proxy(t.search));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.content.on("change", ".user-list-page-length", $.proxy(t.changePageLength));
    t.content.on("click", ".read-more", $.proxy(t.showLocations));
    t.frmEl.selectAllLocation.on("change", $.proxy(t.selectAllLocations));
    t.frmEl.locationAccess.on("change", function () {
        if (parseInt(t.config.autoAllocationFor, 10) === 3) {
            t.getInternalPlace();
        }
    });
    t.btn.submit.on("click", $.proxy(t.handleUsersubmit));
};
