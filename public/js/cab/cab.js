var CabAdd = function (config) {
    var t = this;
    t.config = config;
    t.wrapper = $("#main-change-cab-list-wrapper");
    if (!t.wrapper.length) {
        t.wrapper = t.content; 
    }

    t.content = t.wrapper;  
    t.actionStyleScope = t.wrapper.find(".js-change-cab-list-view-panel").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t.mdl = t.wrapper.find('#cabfrmModal');
    t.frm = t.mdl.find('#cabModal');
    t.frmEl = {};
    t.frmEl.company_id = t.frm.find('#company_id');
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.description = t.frm.find('#description');
    t.frmEl.hierarchy_approval = t.frm.find('#hierarchy_approval');
    t.frmEl.required_minimum_approvals = t.frm.find('#required_minimum_approvals');
    
    t.mdltitle = t.frm.find('.modal-title');
    t.btnSubmit = t.frm.find('#btnSubmit');
    t.filterModal = t.wrapper.find('#cabFilterModal');
    t.advFilters = {
        wrapper: t.filterModal.find("#advance-filters"),
    };
    t.advFilters.filter_by_approver = t.advFilters.wrapper.find("#filter_by_approver");
    t.advFilters.filter_by_approver.select2({
        width: '100%',
        placeholder: 'Select Approval Mode',
        allowClear: true,
        dropdownParent:$("#cabFilterModal"),
    });

    t.mdlHistory = t.wrapper.find('#cabHistoryModal');
    t.descriptonMdl = t.wrapper.find("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    var table = t.wrapper.find("#mytable");
    var cabtable = t.wrapper.find("#cabtable"); 

    t.searchInput = t.wrapper.find(".cab-search");
    t.pageLength = t.wrapper.find(".cab-page-length");
    t.refreshBtn = t.wrapper.find(".btn-reload-list");
    t.exportBtn = t.wrapper.find(".btn-export-cabs");
    t.addBtn = t.wrapper.find(".btn-add-cab");
    t.filterBtn = t.wrapper.find(".btn-open-filter");
    t.filterCountBadge = t.wrapper.find(".filter-count-badge");

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

    function filterCount(other_filters, dateType, isFilter) {
        var count = Object.keys(other_filters).length;
        var badge = t.filterCountBadge;
        if (count > 0) {
            badge.removeClass('d-none').text(count);
        } else {
            badge.addClass('d-none');
        }
    }

    t.icons = {
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.55027 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        },
        members: function () {
            return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class='bi bi-people' viewBox='0 0 16 16'>
            <path d='M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4'/>
            </svg>`;
        },
        history: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M8 0C3.58172 0 0 3.58172 0 8C0 12.4183 3.58172 16 8 16C12.4183 16 16 12.4183 16 8C16 3.58172 12.4183 0 8 0ZM8 14C4.68629 14 2 11.3137 2 8C2 4.68629 4.68629 2 8 2C11.3137 2 14 4.68629 14 8C14 11.3137 11.3137 14 8 14ZM8.5 4H7.5V8.5L11 10.5L11.5 9.5L8.5 7.5V4Z" fill="currentColor"/></svg>';
        }
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup, dataAttr) {
        var safeLabel = t.escapeHtml ? t.escapeHtml(label) : label;
        var safeId = t.escapeHtml ? t.escapeHtml(id) : id;
        var safeClasses = t.escapeHtml ? t.escapeHtml(classes || "") : classes || "";
        var attrs = '';
        if (dataAttr) {
            for (var key in dataAttr) {
                if (dataAttr.hasOwnProperty(key)) {
                    attrs += ' data-' + key + '="' + t.escapeHtml(dataAttr[key]) + '"';
                }
            }
        }
        return [
            '<button type="button" class="user-list-action-btn cab-list-action-btn ', safeClasses,
            '" data-id="', safeId, '"',
            attrs,
            ' title="', safeLabel, '" aria-label="', safeLabel, '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    t.renderActionButtons = function (record, type) {
        if (type && type !== "display") {
            return record && record.id ? record.id : "";
        }
        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }

        var buttons = [];

        buttons.push(t.actionButtonHtml(
            config.translations.Edit_CAB || "Edit",
            "dtActEdit edtCab",
            record.id,
            t.icons.edit()
        ));

        if (![4, 8].includes(record.hierarchy_approval_id)) {
            buttons.push(t.actionButtonHtml(
                config.translations.Member_List || "Members",
                "dtActMembers",
                record.id,
                t.icons.members(),
                { href: config.url.manage_users + "/" + record.id }
            ));
        }

        buttons.push(t.actionButtonHtml(
            config.translations.Delete_CAB || "Delete",
            "dtActDel deleteCab is-delete",
            record.id,
            t.icons.delete()
        ));

        buttons.push(t.actionButtonHtml(
            config.translations.History || "History",
            "dtActHistory viewHistory",
            record.id,
            t.icons.history()
        ));

        return ['<div class="user-list-actions cab-list-actions justify-content-start">', buttons.join(""), '</div>'].join("");
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.dTbl = table.DataTable({
        autoWidth: false,
        aoColumnDefs: [
            {
                bSortable: false,
                aTargets: [7], 
                sDecimal: true,
            },
            {
                targets: 3,
                render: function (d) {
                    if (d.member_count != null) {
                        return "<a target='_blank' href='" + config.url.manage_users + "/" + d.id + "' class='btn' id='" + d.id + "'>" + d.member_count + "</a>";
                    } else {
                        return '';
                    }
                }
            },
            {
                targets: 2, 
                render: function (data, type, row) {
                    if (row.a.hierarchy_approval === "Minimum Approval") {
                        return row.a.hierarchy_approval + '<br><div><span class="ie">Required Approvals: ' + row.a.required_minimum_approvals + '</span></div>';
                    } else {
                        return data;
                    }
                }
            },
            {
                targets: [0, 1, 2, 3, 4, 7],
                class: "text-center",
            },
            {
                targets: 7,
                render: function (data, type, row) {
                    return t.renderActionButtons(row.a, type);
                }
            }
        ],
        order: [[6, 'desc']], 
        processing: true,
        serverSide: true,
        searching:false,
        bLengthChange:false,
        ajax: {
            url: t.config.url.getlist,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || "";
                d.filters = t.config.other_filters || {};
            },
        },
        columns: [
            { data: "a.name", 
                render: function (data, type, row) {
                    if (!data || data === "") {
                        return "N/A";
                    }
                    const cab_name = data.length > 20 ? data.substr(0, 20) + "..." : data;
                    return `<span data-toggle="tooltip" data-original-title="${data}">${cab_name}</span>`;
                }
            },
            { data: "a.company_name" },
            { data: "a.hierarchy_approval" }, 
            { data: "a" },
            { data: "a.description", 
                render: function (data, type, row) {
                    if (!data) {
                        return '';
                    }
                    data = String(data);
                    if (data.length > 30) {
                        var truncated = truncateHtml(data, 30);
                        return truncated + '<a class="read-more" style="cursor:pointer" data-full-text="' + escapeHtml(data) + '">...Read More</a>';
                    } else {
                        return data;
                    }
                }
            },
            { data: "a.created_at" },
            { data: "a.updated_at" }, 
            { data: "a" },
        ],
        fnInitComplete: function (oSettings, json) {
            $("#mytable_filter").remove();
            $("#mytable_length").remove();
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    t.cabdTbl = null; 

    t.pageLength.on('change', function () {
        var length = $(this).val();
        t.dTbl.page.len(length).draw();
    });

    t.searchInput.on('keydown', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            t.config.search = $.trim($(this).val());
            t.dTbl.ajax.reload();
        }
    });

    t.wrapper.find('.amg-list-searchbar__icon').on('click', function () {
        t.config.search = $.trim(t.searchInput.val());
        t.dTbl.ajax.reload();
    });

    t.refreshBtn.on('click', function (e) {
        e.preventDefault();
        t.dTbl.ajax.reload();
    });

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export + "?q=" + t.config.export_filters;
    };
    t.exportBtn.on('click', t.export);

    t.openCABModal = function () {
        t.resetFrm();
        t.prefillDefaultCompany();
        t.mdltitle.text("Add New CAB");
        t.frm.attr('action', config.url.addCab);
        t.mdl.modal('show');
        t.btnSubmit.text("Create");
    };
    t.addBtn.on('click', t.openCABModal);

    t.filterBtn.on('click', function (e) {
        e.preventDefault();
        t.filterModal.modal('show');
    });

    t.advFilters.wrapper.on('click', '#btnFilter', function (e) {
        e.preventDefault();
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        t.filterModal.modal('hide');
    });

    t.content.on('click', '#btnClrFilter', function (e) {
        e.preventDefault();
        t.advFilters.filter_by_approver.val("").trigger("change");
        t.config.other_filters = {};
        t.config.search = '';
        t.searchInput.val('');
        filterCount({}, '', false);
        t.dTbl.ajax.reload();
        t.filterModal.modal('hide');
    });

    t.cache_filter_values = function () {
        var v = $.trim(t.searchInput.val());
        t.config.search = v;
        t.config.other_filters = {};
        if (t.advFilters.filter_by_approver.val() && t.advFilters.filter_by_approver.val() != '') {
            t.config.other_filters.hierarchyApproval = t.advFilters.filter_by_approver.val();
        }
        var jobj = {
            search: t.config.search,
            other_filters: t.config.other_filters,
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, '', false);
    };

    t.deleteCAB = function (e) {
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + id;
        sweetAlertConfirmation({
            message: t.config.translations.are_you_delete_problemcategory,
            onConfirm: function () {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function () {
                    var data = { 'msg': config.translations.something_went_wrong };
                    sweetAlert('center', 'error', data);
                });
                http.always(function () {
                    t.httpCall = true;
                });
            }
        });
    };

    t.editCAB = function (e) {
        e.preventDefault();
        t.resetFrm();
        var cabID = $(this).attr("data-id");
        var http = $.get(t.config.url.getCabForEdit + "/" + cabID);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdltitle.text("Edit CAB");
                    t.btnSubmit.text("Update");
                    t.frm.attr('action', config.url.updateCAB + "/" + data.data.id);
                    t.frmEl.company_id.empty();
                    if (data.data.company_id && data.data.company_name) {
                        t.frmEl.company_id.append(new Option(data.data.company_name, data.data.company_id, true, true)).trigger("change", [true]);
                    }
                    t.frmEl.name.val(data.data.name);
                    t.mdl.find("textarea[name='description']").summernote('code', data.data.description);
                    t.frmEl.hierarchy_approval.val(data.data.hierarchy_approval).trigger('change');
                    t.frmEl.required_minimum_approvals.val(data.data.required_minimum_approvals);
                    t.mdl.modal("show");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.handleSubmit = function (e) {
        e.preventDefault();
        let descText = $('<div>').html(t.frmEl.description.val()).text();
        let $descriptionField = t.frmEl.description;
        let $descriptionContainer = $descriptionField.closest('div.form-group');
        $descriptionContainer.find('label.error.description-error').remove();

        if (descText.length > 2000) {
            $('<label class="error description-error">Description must not exceed 2000 characters.</label>')
                .appendTo($descriptionField.parent('div'));
            return false;
        }
        if (t.frmValidator.form() == false) {
            return false;
        }

        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.frm.attr('action'),
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
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
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.viewHistory = function () {
        var id = $(this).attr('data-id');
        t.cabdTbl = cabtable.DataTable({
            bLengthChange: false,
            autoWidth: false,
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [0, 1, 2, 3, 4],
                    sDecimal: true,
                },
                {
                    targets: 3,
                    render: function (d) {
                        let mode = '';
                        if (d.hierarchy_approval != null) {
                            switch (d.hierarchy_approval) {
                                case '1': mode = "Level By Level"; break;
                                case '2': mode = "Minimum Approval"; break;
                                case '3': mode = "Group Approval"; break;
                                case '4': mode = "Manager Approval"; break;
                                case '5': mode = "Location Approval"; break;
                                case '6': mode = "Department Approval"; break;
                                case '7': mode = "User Approval"; break;
                                case '8': mode = "System Approval"; break;
                            }
                        }
                        return mode;
                    }
                },
                {
                    targets: [1],
                    class: "text-center",
                },
            ],
            order: [[5, 'desc']],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.viewHistory + "/" + id,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.search = $(".plain-search").val(); 
                },
            },
            columns: [
                { data: "performed_by" },
                { data: "action" },
                {
                    data: "a.name",
                    render: function (data, type, row) {
                        if (!data) return '';
                        var data = String(data);
                        if (data.length > 20) {
                            var truncated = truncateHtml(data, 20);
                            return '<div data-toggle="tooltip" data-original-title="' + escapeHtml(data) + '">' +
                                escapeHtml(truncated) + '...' +
                                '</div>';
                        } else {
                            return data;
                        }
                    }
                },
                { data: "a" },
                {
                    data: "a.description",
                    render: function (data, type, row) {
                        if (!data) return '';
                        data = String(data);
                        if (data.length > 30) {
                            var truncated = truncateHtml(data, 30);
                            return truncated + '<a class="read-more" style="cursor:pointer" data-full-text="' + escapeHtml(data) + '">...Read More</a>';
                        } else {
                            return data;
                        }
                    }
                },
                { data: "createdAt" },
            ],
            fnInitComplete: function (oSettings, json) {
                $("#cabtable_filter").remove();
                $("#cabtable_length").remove();
                $('[data-toggle="tooltip"]').tooltip();
            },
            bDestroy: true
        });
        t.mdlHistory.modal('show');
    };

    function initializeSummernote(element) {
        element.summernote({
            toolbar: [
                ['color', ['color']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['']]
            ],
            height: 150,
            focus: false,
            callbacks: {
                onImageUpload: function (data) {
                    var errorLog = { msg: "Image not allowed" };
                    sweetAlert('center', 'error', errorLog);
                    data.pop();
                }
            }
        });
    }
    initializeSummernote(t.frmEl.description);

    t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        if (!row.length) {
            return $();
        }
        var controlCol = row.children(".col-md-9").last();
        if (!controlCol.length) {
            controlCol = row;
        }
        var wrap = controlCol.children(".amg-form-error-wrap").first();
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>').appendTo(controlCol);
        }
        return wrap;
    };
    t.updateValidationState = function (element, hasError) {
        hasError = !!hasError;
        element.closest(".amg-form-field-row")
            .toggleClass("amg-form-field-error", hasError);

        element.closest(".input-group")
            .toggleClass("amg-form-invalid", hasError);

        // Select2
        if (element.hasClass("select2-hidden-accessible")) {
            element.next(".select2-container, .select2")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", hasError);
        }

        // Summernote
        element.next(".note-editor")
            .toggleClass("amg-form-select-error", hasError);
    };

    var commonErrorPlacement = function (error, element) {
        var errorWrap = t.getModalErrorWrap(element);

        if (errorWrap.length) {
            errorWrap.empty().append(error);
        } else if (element.closest(".input-group").length) {
            error.insertAfter(element.closest(".input-group"));
        } else {
            error.appendTo(element.parent());
        }

        t.updateValidationState(element, true);
    };

    t.frmValidator = t.frm.validate({
        ignore: [],
        onsubmit: false,
        rules: {
            name: {
                required: true,
                str_name: true,
                maxlength: 30
            },
            company_id: {
                required: true
            },
            hierarchy_approval: {
                required: true,
                digits: true,
                min: 1,
                max: 8
            },
            required_minimum_approvals: {
                digits: true,
                min: 1,
                max: 15
            }
        },
        errorPlacement: commonErrorPlacement,
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },

        unhighlight: function (element) {
            var $element = $(element);
            t.updateValidationState($element, false);
            t.getModalErrorWrap($element).empty();
        }
    });

    t.fitRequiredMinimamApprovals = function (e) {
        var v = parseInt(t.frmEl.hierarchy_approval.val());
        if (!isNaN(v) && v == 2) {
            t.frmEl.required_minimum_approvals.rules('add', { required: true });
            t.frmEl.required_minimum_approvals.closest(".cover").removeClass("hide");
        } else {
            t.frmEl.required_minimum_approvals.rules('remove', "required");
            t.frmEl.required_minimum_approvals.closest(".cover").addClass("hide");
        }
    };

    var select2Opts = { width: "100%" };
    t.prefillDefaultCompany = function () {
        if (Array.isArray(t.config.company_defulte) && t.config.company_defulte.length === 1 && t.config.company_defulte[0].id) {
            t.frmEl.company_id.empty().append(new Option(t.config.company_defulte[0].text, t.config.company_defulte[0].id, true, true)).trigger("change", [true]);
        }
    };

    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.company_id.parent(),
        placeholder: t.config.translations.select_company || "Select Company",
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    }));

    t.frmEl.hierarchy_approval.select2($.extend({}, select2Opts, { dropdownParent: t.mdl })).on("change", $.proxy(t.fitRequiredMinimamApprovals, t));
    t.frmEl.hierarchy_approval.trigger('change');

    t.resetFrm = function () {
        t.frmEl.company_id.val('').empty().trigger("change", [true]);
        t.frmEl.name.val('');
        t.frmEl.description.val("").summernote('code', '');
        t.frmEl.hierarchy_approval.val(3).trigger("change");
        t.frmEl.required_minimum_approvals.val('');
        t.frmValidator.resetForm();
        $('.description-error').remove();
    };

    t.content.on('click', '#btnSubmit', t.handleSubmit);
    t.content.on('click', '.edtCab', t.editCAB);
    t.content.on('click', '.deleteCab', t.deleteCAB);
    t.content.on('click', '.viewHistory', t.viewHistory);
    t.content.on('click', '.read-more', function (e) {
        e.preventDefault();
        var fullText = $(this).data("full-text");
        t.descriptonMdl.body.html(fullText);
        t.descriptonMdl.modal("show");
    });
    t.content.on('click', '.dtActMembers', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        if (href) {
            window.open(href, '_blank');
        }
    });

    t.cache_filter_values(); 
    t.dTbl.ajax.reload();
};
