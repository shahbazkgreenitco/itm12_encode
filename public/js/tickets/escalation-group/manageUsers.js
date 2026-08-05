var EscalationGroupsMembers = function (config) {
    var t = this;
    t.config = config || {};
    t.config.translations = t.config.translations || {};
    t.config.search = t.config.search || "";
    t.page = $("#main-escalation-group-users-wrapper");
    t.content = t.page.length ? t.page : $('section.content');
    t.table = t.content.find('#groupoUsers');
    t.pageLength = t.content.find(".userModulePageLenth");
    t.mdl = t.content.find('#esclationGroupMdl');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#esclationGroup');    

    t.mdl.addGroupUser = t.content.find('#addGroupUserFormMdl');
    t.mdltitle = t.mdl.addGroupUser.find('.modal-title');
    t.frm.addUser = t.mdl.addGroupUser.find('#addGroupUserForm');

    t.frmEl = {};
    t.frmEl.groupUserId = t.frm.addUser.find('#groupUserId1');
    t.frmEl.locationAccess = t.frm.addUser.find('#locationaccess');
    t.frmEl.internal_location = t.frm.addUser.find('#internal_location');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.addUser.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.addUser.find('#btnClear');

    t.btn.submitUser = t.frm.addUser.find('#btnSubmit');
    

    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");

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

    t.icons = {
        edit: '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>',
        trash: '<i class="bi bi-trash"></i>'
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup) {
        return '<button type="button" class="user-list-action-btn role-list-action-btn ' + t.escapeHtml(classes || "") + '"' +
            ' id="' + t.escapeHtml(id) + '"' +
            ' data-id="' + t.escapeHtml(id) + '"' +
            'data-bs-toggle="tooltip"'+
            ' title="' + t.escapeHtml(label) + '"' +
            ' aria-label="' + t.escapeHtml(label) + '">' + iconMarkup + '</button>';
    };

    t.renderActionButtons = function (record) {
        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        return '<div class="user-list-actions role-list-actions justify-content-start">' + [
            t.actionButtonHtml(t.tr("edit_group_member", "Edit Details"), "open-edit-modal", record.id, t.icons.edit),
            t.actionButtonHtml(t.tr("escalation_group_user.delete_user", "Delete Details"), "open-delete is-delete", record.id, t.icons.trash)
        ].join("") + '</div>';
    };

    t.actionStyleScope = t.content.find(".list-view-panel").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.getUserInitials = function (name) {
        var words = String(name || "").split(/\s+/).filter(Boolean);
        if (!words.length) return "NA";
        if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
        return (words[0].charAt(0) + words[1].charAt(0)).toUpperCase();
    };

    t.getAvatarHtml = function (name, imageUrl, className) {
        var safeName = t.escapeHtml(name || "User");
        var cssClass = className || "user-list-avatar";

        if (imageUrl) {
            return '<img src="' + t.escapeHtml(imageUrl) + '" alt="' + safeName + '" class="' + t.escapeHtml(cssClass) + '">';
        }

        return '<span class="' + t.escapeHtml(cssClass + " user-list-avatar-fallback") + '" aria-hidden="true">' + t.escapeHtml(t.getUserInitials(name)) + '</span>';
    };

    t.getModalSelectDropdownParent = function (element) {
        var parent = element.closest(".input-group");
        return parent.length ? parent : t.mdl.addGroupUser;
    };

    t.syncSelect2Width = function (element) {
        var parent = t.getModalSelectDropdownParent(element);
        var container = element.next(".select2-container");
        if (!parent.length || !container.length) return;

        window.requestAnimationFrame(function () {
            var dropdownContainer = parent.children(".select2-container--open").last();
            if (!dropdownContainer.length) dropdownContainer = parent.find(".select2-container--open").last();
            if (!dropdownContainer.length) return;

            var width = container.outerWidth();
            if (!width) return;

            dropdownContainer.css("width", width + "px");
            dropdownContainer.find(".select2-dropdown").css({
                width: width + "px",
                minWidth: width + "px"
            });
        });
    };

    var select2Opts = { width: "100%" };

    t.frmEl.groupUserId.select2($.extend({}, {
        width: "100%",
        ajax: {
            url: t.config.url.getEscCompanyUsersByQuery,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.config.company_id,
                    company_access_via:1,
                };
            },
            delay: 300
        },
        allowClear:true,
        placeholder: t.config.translations.user_placeholder,
        dropdownParent: t.getModalSelectDropdownParent(t.frmEl.groupUserId),
        templateResult: function(data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data); 
        },
        templateSelection: function (data, container) {
            $(container).attr("title", data.text || "");
            var txt = data.text || "";
            return txt.length > 50 ? txt.substring(0, 50) + "..." : txt;
        }
    })); 
    t.frmEl.groupUserId.off("select2:open.escalationUser").on("select2:open.escalationUser", function () {
        t.syncSelect2Width($(this));
    });
    // t.frmEl.locationAccess.select2($.extend({}, {
    //     width: "100%",
    //     data: t.config.locationAccess,
    //     allowClear:true,
    //     placeholder: t.config.translations.location_placeholder,
    // }));
    let lastLoadedLocations = [];
    t.frmEl.locationAccess.select2($.extend({}, select2Opts, {
        placeholder: t.config.translations.location_placeholder,
        allowClear: true,
        dropdownParent: t.getModalSelectDropdownParent(t.frmEl.locationAccess),
        ajax: {
            url: t.config.url.getCompanyWiseLocation,
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.config.company_id
                };
            },
            processResults: function (response, params) {
                lastLoadedLocations = response.results;
                params.page = params.page || 1;

                return {
                    results: response.results,
                    pagination: {
                        more: response.pagination.more
                    }
                };
            }
        }
    }));
    t.frmEl.locationAccess.off("select2:open.escalationUser").on("select2:open.escalationUser", function () {
        t.syncSelect2Width($(this));
    });

    $('#selectAllLocation').on('click', function () {
        let isChecked = $(this).prop('checked');
        if (isChecked) {
            $.ajax({
                url: t.config.url.getCompanyWiseLocation,
                type: 'GET',
                dataType: 'json',
                data: {
                    company_id: t.config.company_id,
                    all: 1
                },
                success: function (response) {
                    let select = t.frmEl.locationAccess;
                    select.empty();
                    $.each(response.results, function (i, loc) {
                        let option = new Option(loc.text, loc.id, true, true);
                        select.append(option);
                    });
                    select.trigger('change');
                }
            });

        } else {
            t.frmEl.locationAccess.val(null).trigger('change');
        }
    });

    t.frmEl.locationAccess.on('select2:unselect', function (e) {
        let selectedValues = t.frmEl.locationAccess.val() || [];
        let checked_location_count = selectedValues.length;
        if (t.config.locationAccess.length > checked_location_count && $("#selectAllLocation").prop('checked')) {
            $("#selectAllLocation").prop('checked', false);
        }
    });

    t.frmEl.locationAccess.on('select2:select', function (e) {
        let checked_location_count = t.frmEl.locationAccess.val().length;
        if(t.config.locationAccess.length == checked_location_count && !$("#selectAllLocation").prop('checked')){
            $("#selectAllLocation").prop('checked',true);
        }
    });

    // $('#selectAllLocation').on('click', function () {
    //     if ($(this).is(':checked')) {
    //         let values = lastLoadedLocations.map(item => item.id);
    //         $('#locationaccess').val(values).trigger('change');
    //     } else {
    //         $('#locationaccess').val(null).trigger('change');
    //     }
    // });
    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        var name = s.text || "-";
        var email = s.email || "";
        var employeeNo = s.employee_num || "";
        var shortName = name.length > 35 ? name.substring(0, 35) + "..." : name;
        var shortEmail = email.length > 30 ? email.substring(0, 30) + "..." : email;
        var avatarHtml = t.getAvatarHtml(name, s.img_path || s.profile_img || "", "user-list-avatar");

        return $([
            '<div class="d-flex align-items-start gap-3 w-100">',
                avatarHtml,
                '<div class="d-flex flex-column w-100 gap-1 min-w-0">',
                    '<div class="d-flex align-items-center gap-2">',
                        '<span class="b2-text">' + t.escapeHtml(shortName) + '</span>',
                        s.status == 1 ? '<span class="active-user"></span>' : '<span class="inactive-user"></span>',
                    '</div>',
                    email ? '<span class="b1-text opacity-50"><i class="fa fa-envelope-o me-1"></i>' + t.escapeHtml(shortEmail) + '</span>' : "",
                    employeeNo ? '<span class="b1-text opacity-50"><i class="fa fa-credit-card me-1"></i>' + t.escapeHtml(employeeNo) + '</span>' : "",
                '</div>',
            '</div>'
        ].join(""));
    };

    t.frmValidator = t.frm.addUser.validate({
        onsubmit: false,
        rules: {
            groupUserId1: {
                required: true,
            },
            'locationaccess[]': {
                required: true,
            },
            
        },
        errorPlacement: function(error, element) {
            var errorWrap = element.closest('.amg-form-field-row').find('.amg-form-error-wrap');

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else {
                error.insertAfter(element);
            }
        }
    });

  
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        columnDefs: [
            {
                targets: 4,
                orderable: false,
                searchable: false,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: function (data, type, row) {
                    return t.renderActionButtons(row.a || data);
                }
            }
        ],
        order: [
            [0, 'asc']
        ],
        deferLoading: true,
        ajax: {
            url: t.config.url.manage_users,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.groupId = t.config.group_Id;
                d.search = {
                    value : t.config.search || $.trim(t.searchbox.val() || "")
                };
            },
            error: function (reason) {
                // location.reload();
            }
        },
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
        columns: [
            { data: 'a.user_name', defaultContent: "-" },
            { data: 'a.loc_name', defaultContent: "-" },
            { data: 'a.access_location', defaultContent: "-" },
            { data: 'a.location_internal_name', defaultContent: "-" },
            { data: 'a', defaultContent: null },
        ],
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });


    t.reload = function() {
        t.dTbl.ajax.reload(null, false);
    };

    t.search = function (e) {
        if (e && e.type === "keyup" && e.keyCode !== 13) {
            return;
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
        t.reload();
    };

    t.changePageLength = function () {
        t.dTbl.page.len(parseInt($(this).val(), 10) || 10).draw();
    };

    t.createGroup = function (e) {
        e.preventDefault();
        t.loader.hide();
        t.mdl.modal("show");
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        t.resetFrm();
    };

    t.addGroupUser = function (e) {
        e.preventDefault();
        t.httpPostPath = t.config.url.addGroupUser;
        t.frm.addUser.trigger("reset");
      
        t.mdltitle.text(t.config.translations.add_group_member);
        t.btn.submit.text(t.config.translations.save);
        t.mdl.addGroupUser.modal("show");
        t.resetFrm();
    };


    t.handleUsersubmit = function (e) {
        e.preventDefault();

        if( t.frmValidator.form() == false ) {
            return false;
        }

        var frmData = new FormData(t.frm.addUser[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    t.mdl.addGroupUser.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            sweetAlerts(config.translations.something_went_wrong, 'warning');
            // vex.dialog.alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.loader.hide();
        });
    };

    t.loadForm = function (obj) {
        t.frmEl.groupUserId.empty();
        $.each(t.config.ticketHandlers, function(i, d) {
            var op = (d.id == obj.dropdown.user_id.id) ? new Option(obj.dropdown.user_id.text, obj.dropdown.user_id.id, true, true) : new Option(d.text, d.id);
            t.frmEl.groupUserId.append(op);
        });
        $.each(obj.data.location_ids, function( key, value ) {
            t.frmEl.locationAccess.select2('trigger', 'select', {
                data: {text: value.name, id: value.id, selected: true}
            });
        });
        t.getInternalPlace(null, function () {
            const ids = (obj.data.location_internal_ids || []).map(item => item.id.toString());
            setTimeout(function () {
                t.frmEl.internal_location.val(ids).trigger('change');
            }, 50);
        });
    };

    t.resetFrm = function () {
        t.frmEl.locationAccess.val("").trigger("change");
        t.frmEl.groupUserId.val("").trigger("change");
        t.frmEl.internal_location.val("").trigger("change");
        t.frmValidator.resetForm();
    };

    t.getInternalPlace = function (e, callback) {
        if (e && e.preventDefault) {
            e.preventDefault();
        }
        var location_ids = t.frmEl.locationAccess.val();
        if (!location_ids || !location_ids.length) {
            t.frmEl.internal_location.empty().trigger("change");
            return;
        }

        $.get(t.config.url.getInternalPlaceByAjax + '/' + location_ids.join(',')).done(function (data) {
            t.frmEl.internal_location.empty();
            if (data?.results?.length) {
                $.each(data.results, function (i, v) {
                    var option = new Option(v.text,v.id.toString(),false,false);
                    t.frmEl.internal_location.append(option);
                });
            }
            t.frmEl.internal_location.trigger('change');
            if (typeof callback === "function") {
                callback();
            }
        });
    };

    t.editGroupMember = function (e) {
        e.preventDefault();
        t.resetFrm();
        var groupMemberId = $(this).data("id") || $(this).attr("id");
       
        t.httpPostPath = t.config.url.editGroupMember + "/" + groupMemberId;
        var http = $.get(t.config.url.getGroupMember + "/" + groupMemberId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.addGroupUser.modal("show");
                    t.mdltitle.text(t.config.translations.edit_group_member);
                    t.btn.submit.text(t.config.translations.save_changes);
                    t.loadForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            alert(t.config.translations.something_wrong);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteGroupUser = function (e) {
        e.preventDefault();
        var userId = $(this).data("id") || $(this).attr("id");
        t.httpPostPath = t.config.url.delete + "/" + userId;
        // vex.dialog.confirm({
        //     message: t.config.translations.confirmation_user,
        //     callback: function (value) {
        //         if (value == true) {
        //             var http = $.get(t.httpPostPath);
        //             http.done(function (data) {
        //                 if (typeof data == "object") {
        //                     if (data.status == "success") {
        //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>' });
        //                         t.dTbl.ajax.reload();
        //                     }
        //                     else {
        //                         vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
        //                     }
        //                 }
        //             });
        //             http.fail(function () {
        //                 alert(t.config.translations.confirmation_message);
        //             });
        //             http.always(function () {
        //                 t.httpCall = true;
        //             });
        //         }
        //     }
        // });
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.confirmation_user, 'warning', t.httpPostPath, t.dTbl, data);
    };

    $(document).ready(function() {
        $('#selectAllLocation').click(function() {
        var isChecked = $(this).prop("checked");
           if(isChecked == true) {
                $("#locationaccess > option").prop("selected", true);
                t.frmEl.locationAccess.trigger("change");
           } else {
                $("#locationaccess > option").prop("selected", false);
                t.frmEl.locationAccess.trigger("change");
                t.frmEl.internal_place_ids.val(null).trigger('change');
           }
        });
    });

    var select2Opts = { width: "100%" };
    t.frmEl.internal_location.select2($.extend({}, select2Opts, {dropdownParent: t.getModalSelectDropdownParent(t.frmEl.internal_location), placeholder: t.config.translations.location_placeholder}));
    t.frmEl.internal_location.off("select2:open.escalationUser").on("select2:open.escalationUser", function () {
        t.syncSelect2Width($(this));
    });
    t.content.on('click', '.open-add-modal', $.proxy(t.createGroup));
    t.content.on('click', '.open-addGroupUserForm-modal', $.proxy(t.addGroupUser));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editGroupMember));
    t.content.on('click', '.open-delete', $.proxy(t.deleteGroupUser));
    t.content.on("keyup", ".plain-search", $.proxy(t.search));
    t.content.on("click",'.btn-searchbox', $.proxy(t.search));
    t.content.on("click",'.btn-reload-list', $.proxy(t.reload));
    t.content.on("change", ".userModulePageLenth", t.changePageLength);
    t.btn.submit.on('click', $.proxy(t.handlesubmit));

    t.btn.submitUser.on('click', $.proxy(t.handleUsersubmit));

    t.dTbl.ajax.reload();
    t.frmEl.locationAccess.on("change", function() {
        if(t.config.location_based == 1) {
            t.getInternalPlace();
        }
    });
};
