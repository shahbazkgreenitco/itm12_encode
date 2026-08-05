var problemlist = function (config) {
    var t = this;
    t.config = config;
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.content = $("section.content");
    t.table = t.content.find("#mytable");

    t.page = $("#content");
    t.searchbox = $("#problem-manage-list-search");
    t.pageLengthSelect = $("#user-list-page-length");
    t.refreshBtn = $(".btn-reload-list");
    t.addBtn = $(".open-add-modal");
    t.downloadBtn = $(".btn-download");

    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {
        sub_categories: [],
        problem_categories: {}
    };
    t.filters = {
        wrapper: $("#advance-filters"),
        data: {
            problem_categories: {}
        },
    };
    t.filters.btnfilterclr = t.filters.wrapper.find("#btnClrFilter");
    t.filters.department = t.filters.wrapper.find("#filter_by_department");
    t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category");
    t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category");
    t.filters.priority = t.filters.wrapper.find("#filter_by_priority");
    t.filters.handler = t.filters.wrapper.find("#filter_by_handler");
    t.filters.company = t.filters.wrapper.find("#filter_by_company");

    var select2Opts = { width: "100%" };

    // ------------------- Filter badge helper -------------------
    t.updateFilterCountBadge = function () {
        var filters = t.config.other_filters || {};
        var count = 0;
        $.each(filters, function (key, val) {
            if (val && val != 'null') {
                if (Array.isArray(val)) {
                    if (val.length > 0) count++;
                } else if (val !== "" && val !== null) {
                    count++;
                }
            }
        });
        var $badge = $(".btn-open-filter .filter-count-badge");
        if (count > 0) {
            $badge.text(count).removeClass("d-none");
        } else {
            $badge.addClass("d-none");
        }
    };
    // ---------------------------------------------------------

    t.filters.company.select2($.extend({}, select2Opts, {
        placeholder: "Filter By Company",
        dropdownParent: $('#advance-filters'),
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            }
        }
    }));

    if (config.company_defulte && config.company_defulte.length > 0) {
        let companies = config.company_defulte;
        companies.forEach(function (company) {
            let option = new Option(company.text, company.id, true, true);
            t.filters.company.append(option);
        });
        t.filters.company.trigger('change');
    }

    t.filters.company.on("change", function () {
        t.filters.department.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.fun.reload_department();
    });

    t.filters.fun = {
        reload_priority: function () {
            $.each(t.config.priorities, function (i, k) {
                t.filters.priority.append(new Option(k.name, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        reload_department: function () {
            t.filters.department.empty();
            let companyIds = t.filters.company.val();
            if (!companyIds || companyIds.length === 0) return;
            $.get(t.config.url.departments_by_company + "/" + companyIds.join(','), function (data) {
                if (typeof data == "object" && data.data.length > 0) {
                    $.each(data.data, function (i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
        },
        reload_problem_category: function () {
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data.length > 0) {
                        $.each(data.data, function (i, b) {
                            t.filters.problem_category.append(new Option(b.name, b.id, false, false));
                            if (typeof b.sub != "undefined" && Array.isArray(b.sub)) {
                                t.filters.data.problem_categories["sc" + b.id] = b.sub;
                            }
                        });
                        t.filters.problem_category.trigger("change");
                    }
                });
            }
            t.filters.problem_category.trigger("change");
        },
        reload_sub_category: function () {
            var prblm = t.filters.problem_category.val();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    $.each(t.filters.data.problem_categories["sc" + prblm], function (i, k) {
                        t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                    });
                } catch (e) {}
            }
            t.filters.sub_category.trigger("change");
        },
        reload_handler: function () {
            t.filters.handler.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.handler.parent(),
                ajax: {
                    url: t.config.getUserByQuery,
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
                placeholder: config.translations.Select_the_problem_Handler,
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    var imgPaddingLeft = "0px";
                    return t.userDropdownFormat(data, imgPaddingLeft);
                },
            }));
            t.filters.handler.trigger("change");
        },
        reload_category: function () {
            $.each(t.config.categories, function (i, k) {
                t.filters.category.append(new Option(k.text, k.id, false, false));
            });
            t.filters.category.trigger("change");
        },
        reload_manufacturer: function () {
            $.each(t.config.manufacturers, function (i, k) {
                t.filters.manufacturer.append(new Option(k.text, k.id, false, false));
            });
            t.filters.manufacturer.trigger("change");
        },
        reload_model: function () {
            $.each(t.config.models, function (i, k) {
                t.filters.model.append(new Option(k.text, k.id, false, false));
            });
            t.filters.model.trigger("change");
        },
    };

    t.offListen = false;
    t.btn = {};
    t.mdl = $("section.content").find("#addproblemModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("#addproblem-mdl-frm");
    t.mdl.el = {};
    t.mdl.el.id = t.mdl.find("#id");
    t.mdl.el.forAction = t.mdl.find("#forAction");
    t.mdl.el.name = t.mdl.find("#name");
    t.mdl.el.priority_id = t.mdl.find("#priority_id");
    t.mdl.el.department_id = t.mdl.find("#department_id");
    t.mdl.el.descriptions = t.mdl.find("#content");
    t.mdl.el.problem_category_ids = t.mdl.find("#problem_category_id");
    t.mdl.el.sub_category_ids = t.mdl.find("#sub_category_id");
    t.mdl.el.subCategoryIdCvr = t.mdl.frm.find("#sub_category_id_cvr");
    t.mdl.el.problem_handler_id = t.mdl.find("#problem_handler_id");
    t.mdl.el.device_id = t.mdl.find("#device_id");
    t.mdl.el.ticket_id = t.mdl.find("#ticket_id");
    t.mdl.el.company_id = t.mdl.find("#company_id");
    t.mdl.el.btnUpdate = t.mdl.find('#btnUpdate');
    t.mdl.filterImpactedDevices = t.mdl.find(".filterImpactedDevices");
    t.mdl.el.descriptions.summernote({
        toolbar: summernote_toolbar,
        icons: summernote_icons,
        styleTags: styleTags,
        minHeight: 120,
        focus: true,
        callbacks: {
            onInit: function () {
                $(this)
                    .next('.note-editor')
                    .addClass('amg-summernote-editor');

                if (typeof userOnInit === 'function') {
                    userOnInit.apply(this, arguments);
                }
                $('.note-style .dropdown-toggle').html(textResizeIcon);
            }
        }
    });
    t.mdl.btn = {};
    t.mdl.btnSubmit = t.mdl.find("#btnSubmit");
    t.mdl.btnClear = t.mdl.find("#btnClear");

    t.mdl.filterDeviceDiv = t.mdl.find(".filterDeviceDiv");
    t.filters.category = t.mdl.filterDeviceDiv.find("#filter_by_category");
    t.filters.manufacturer = t.mdl.filterDeviceDiv.find("#filter_by_manufacturer");
    t.filters.model = t.mdl.filterDeviceDiv.find("#filter_by_model");

    t.filters.category.select2({ width: '100%' });
    t.filters.manufacturer.select2({ width: '100%' });
    t.filters.model.select2({ width: '100%' });

    t.mdl.filterImpactedDevices.on('click', function () {
        t.mdl.filterDeviceDiv.toggle();
    });

    t.mdl.modal({
        backdrop: 'static',
        keyboard: false,
        show: false
    });

    t.mdl.on('hidden.bs.modal', function () {
        t.resetFrm();
        t.frmValidator.resetForm();
        t.mdl.find('.amg-form-invalid').removeClass('amg-form-invalid');
        t.mdl.find('.amg-form-select-error').removeClass('amg-form-select-error');
        t.mdl.find('.amg-form-error-wrap').empty();
    });

    t.mdlUpdate = t.page.find("#problemtable");
    t.frmUpdate = t.mdlUpdate.find("#table-problem-mdl-frm");
    t.frmUpdate.extraData = {};
    t.frmUpdate.el = {};
    t.mdlUpdate.title = t.frmUpdate.find('.modal-title');
    t.frmUpdate.el.btnUpdate = t.frmUpdate.find('#btnUpdate');
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
        var noteEditor = element.next(".note-editor");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }

        if (noteEditor.length) {
            noteEditor.toggleClass("amg-form-select-error", !!hasError);
        }
    };
    // --------------------------
    // Impact Devices DataTable
    // --------------------------
    t.deviceDTbl = $("#impactDevicesTable").DataTable({
        autoWidth: false,
        scrollX: true,
        scrollY: '300px',
        scrollCollapse: true,
        dom: 'drtip',
        paging: true,
        searching: true,
        info: true,
        order: [[0, 'asc']],
        columns: [
            { data: "asset_tag", title: config.translations.asset_tag },
            { data: "asset_name", title: config.translations.device_name },
            { data: "device_type", title: config.translations.device_type },
            { data: "model", title: config.translations.Model },
            { data: "serial", title: config.translations.serial_number },
            { data: "action", title: config.translations.action, orderable: false }
        ],
        data: []
    });

    var deviceSearchInput = $(".device-search-input");
    if (deviceSearchInput.length) {
        deviceSearchInput.on("keyup", function () {
            t.deviceDTbl.search(this.value).draw();
        });
    }

    t.refreshDeviceTable = function () {
        var selectedOptions = t.mdl.el.device_id.find('option:selected');
        var tableData = [];
        selectedOptions.each(function () {
            var $option = $(this);
            var device = $option.data('device');
            if (device) {
                tableData.push({
                    asset_tag: device.asset_tag || '',
                    asset_name: device.asset_name || '',
                    device_type: device.name || '',
                    model: device.modelno || '',
                    serial: device.serial || '',
                    action: '<button class="user-list-action-btn btn-remove-device-from-table me-1" data-toggle="tooltip" data-placement="top" title="Remove" data-id="' + device.id + '">' +
                        '<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg></button>'
                });
            } else {
                var select2Data = t.mdl.el.device_id.select2('data') || [];
                $.each(select2Data, function (i, d) {
                    if (d.id == $option.val()) {
                        tableData.push({
                            asset_tag: d.asset_tag || '',
                            asset_name: d.asset_name || '',
                            device_type: d.name || '',
                            model: d.modelno || '',
                            serial: d.serial || '',
                            action: '<button class="user-list-action-btn btn-remove-device-from-table me-1" data-toggle="tooltip" data-placement="top" title="Remove" data-id="' + d.id + '">' +
                                '<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg></button>'
                        });
                        return false;
                    }
                });
            }
        });

        t.deviceDTbl.clear();
        t.deviceDTbl.rows.add(tableData);
        t.deviceDTbl.draw();

        $(".btn-remove-device-from-table").off("click").on("click", function () {
            var deviceId = $(this).data("id");
            var currentVal = t.mdl.el.device_id.val() || [];
            var newVal = Array.isArray(currentVal) ? currentVal.filter(function (id) {
                return id != deviceId;
            }) : [];
            t.mdl.el.device_id.val(newVal).trigger("change");
        });
    };

    t.loadAndDisplayDevices = function (problemId, callback) {
        $.ajax({
            url: t.config.url.filterdevice + "/" + problemId,
            type: "get",
            success: function (response) {
                if (response.status === "success" && response.data && response.data.length) {
                    t.mdl.el.device_id.empty();
                    response.data.forEach(function (device) {
                        var option = new Option(device.asset_tag, device.id, true, true);
                        $(option).data('device', device);
                        t.mdl.el.device_id.append(option);
                    });
                    t.mdl.el.device_id.trigger('change');
                    if (callback) callback();
                } else {
                    if (callback) callback();
                }
            },
            error: function () {
                if (callback) callback();
            }
        });
    };

    t.filterproblemdevice = function (impact_id) {
        t.loadAndDisplayDevices(impact_id, function () {
            t.refreshDeviceTable();
        });
    };

    // --------------------------
    // Impact Tickets DataTable
    // --------------------------
    t.ticketDTbl = $("#impactTicketsTable").DataTable({
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
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        autoWidth: false,
        scrollX: true,
        scrollCollapse: true,
        dom: 'drtip',
        paging: true,
        searching: true,
        info: true,
        order: [[0, 'desc']],
        columns: [
            { data: "id", title: config.translations.id || "ID" },
            { data: "ticket_tag", title: config.translations.tag || "Tag" },
            { data: "subject", title: config.translations.subject || "Subject" },
            { data: "department", title: config.translations.department || "Department" },
            { data: "category", title: config.translations.category || "Category" },
            { data: "subcategory", title: config.translations.subcategory || "Subcategory" },
            { data: "expires", title: config.translations.expires || "Expires" },
            { data: "updated_at", title: config.translations.updated_at || "Updated At" },
            { data: "status", title: config.translations.status || "Status" },
            { data: "action", title: config.translations.action || "Action", orderable: false }
        ],

    });

    var ticketSearchInput = $(".ticket-search-input");
    if (ticketSearchInput.length) {
        ticketSearchInput.on("keyup", function () {
            t.ticketDTbl.search(this.value).draw();
        });
    }

    t.refreshTicketTable = function () {
        var selectedOptions = t.mdl.el.ticket_id.find('option:selected');
        var tableData = [];

        selectedOptions.each(function () {
            var $option = $(this);
            var ticket = $option.data('ticket');
            if (ticket) {
                tableData.push({
                    id: ticket.id,
                    ticket_tag: ticket.ticket_tag || '',
                    subject: ticket.subject || '',
                    department: ticket.department || '',
                    category: ticket.category || '',
                    subcategory: ticket.subcategory || '',
                    expires: ticket.tat_expire || '',
                    updated_at: ticket.updated_at || '',
                    status: ticket.status || '',
                    action: '<button class="user-list-action-btn btn-remove-ticket-from-table me-1" data-toggle="tooltip" data-placement="top" title="Remove" data-id="' + ticket.id + '">' +
                        '<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg></button>'
                });
            } else {
                var select2Data = t.mdl.el.ticket_id.select2('data') || [];
                $.each(select2Data, function (i, d) {
                    if (d.id == $option.val()) {
                        tableData.push({
                            id: d.id,
                            ticket_tag: d.ticket_tag || '',
                            subject: d.text || '',
                            department: d.department || '',
                            category: d.category || '',
                            subcategory: d.subcategory || '',
                            expires: d.tat_expire || '',
                            updated_at: d.updated_at || '',
                            status: d.status || '',
                            action: '<button class="user-list-action-btn btn-remove-ticket-from-table me-1" data-toggle="tooltip" data-placement="top" title="Remove" data-id="' + d.id + '">' +
                                '<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg></button>'
                        });
                        return false;
                    }
                });
            }
        });

        t.ticketDTbl.clear();
        t.ticketDTbl.rows.add(tableData);
        t.ticketDTbl.draw();

        $(".btn-remove-ticket-from-table").off("click").on("click", function () {
            var ticketId = $(this).data("id");
            var currentVal = t.mdl.el.ticket_id.val() || [];
            var newVal = Array.isArray(currentVal) ? currentVal.filter(function (id) {
                return id != ticketId;
            }) : [];
            t.mdl.el.ticket_id.val(newVal).trigger("change");
        });
    };
    function initTicketSelect2() {
        if (t.mdl.el.ticket_id.data('select2')) {
            t.mdl.el.ticket_id.select2('destroy');
        }
        t.mdl.el.ticket_id.select2({
            width: "100%",
            placeholder: config.translations.select_impacted_ticket,
            dropdownParent: t.mdl.el.ticket_id.parent(),
            allowClear: true,
            ajax: {
                url: t.config.url.getTicketDetails,
                dataType: "json",
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term || '',
                        department: t.mdl.el.department_id.val(),
                        pc: t.mdl.el.problem_category_ids.val(),
                        sc: t.mdl.el.sub_category_ids.val(),
                        page: params.page || 1
                    };
                },
                processResults: function (response) {
                    var tickets = response.results || response.data || [];
                    return {
                        results: tickets.map(function (ticket) {
                            return {
                                id: ticket.id,
                                text: ticket.text || ticket.subject,
                                ticket_tag: ticket.ticket_tag || '',
                                subject: ticket.text || ticket.subject,
                                department: ticket.department || '',
                                category: ticket.category || '',
                                subcategory: ticket.subcategory || '',
                                tat_expire: ticket.tat_expire || '',
                                updated_at: ticket.updated_at || '',
                                status: ticket.status || ''
                            };
                        }),
                        pagination: {
                            more: response.pagination ? response.pagination.more : false
                        }
                    };
                }
            },
            templateResult: function (data) {
                if (data.loading) return data.text;
                return $('<span>' + (data.text || data.id) + '</span>');
            },
            templateSelection: function (data) {
                return data.text || data.id;
            }
        }).on('select2:select', function (e) {
            var ticket = e.params.data;
            if (t.mdl.el.ticket_id.find('option[value="' + ticket.id + '"]').length === 0) {
                var option = new Option(ticket.text, ticket.id, true, true);
                $(option).data('ticket', ticket);
                t.mdl.el.ticket_id.append(option);
            }
            t.mdl.el.ticket_id.trigger('change');
        }).on('select2:unselect', function (e) {
            t.mdl.el.ticket_id.find('option[value="' + e.params.data.id + '"]').remove();
            t.mdl.el.ticket_id.trigger('change');
        });
    }

    t.loadTicketsForEdit = function (ticketsArray) {
        if (!ticketsArray || !ticketsArray.length) return;
        t.mdl.el.ticket_id.empty();
        ticketsArray.forEach(function (ticket) {
            var option = new Option(ticket.text || ticket.subject, ticket.id, true, true);
            var ticketData = {
                id: ticket.id,
                ticket_tag: ticket.ticket_tag || '',
                subject: ticket.subject || ticket.text || '',
                department: ticket.department || '',
                category: ticket.category || '',
                subcategory: ticket.subcategory || '',
                tat_expire: ticket.tat_expire || '',
                updated_at: ticket.updated_at || '',
                status: ticket.status || ''
            };
            $(option).data('ticket', ticketData);
            t.mdl.el.ticket_id.append(option);
        });
        t.mdl.el.ticket_id.trigger('change'); 
    };

    t.fillSla = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var tmp = t.mdl.el.problem_category_ids.val();
        var sub_category_id = t.mdl.el.sub_category_ids.val();
        var found = false;

        if (Array.isArray(t.data.sub_categories) == true && t.data.sub_categories.length) {
            $.each(t.data.sub_categories, function (i, k) {
                if (k.id == sub_category_id) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.mdl.el.priority_id.val(k.priority_id).trigger("change");
                        t.offListen = false;
                    } else {
                        t.mdl.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        } else if (typeof t.data.problem_categories != "undefined" && t.data.problem_categories.length) {
            $.each(t.data.problem_categories, function (i, k) {
                if (k.id == tmp) {
                    var tmp_tat = parseInt(k.tat);
                    if (k.tat === tmp_tat && !isNaN(tmp_tat)) {
                        t.offListen = true;
                        t.mdl.el.priority_id.val(k.priority_id).trigger("change");
                        t.offListen = false;
                    } else {
                        t.mdl.el.priority_id.val(k.priority_id).trigger("change");
                    }
                    found = true;
                    return false;
                }
            });
        }

        if (!found) {
            t.offListen = true;
            t.mdl.el.priority_id.val("").trigger("change");
            t.offListen = false;
        }
    };

    t.refillTat = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        var pro_val = t.mdl.el.priority_id.val();
        var val = 0;
        $.each(t.config.priorities, function (i, k) {
            if (k.id == pro_val) {
                val = k.service_time;
                return false;
            }
        });
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            name: { required: true, clean_text_only: true, maxlength : 250 },
            content: { required: true },
            priority_id: { required: true },
            department_id: { required: true },
            problem_category_id: { required: true },
            sub_category_id: { required: true },
           'problem_handler_id[]': { required: true },
            company_id: { required: true }
        },// end of rules
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.appendTo(element.closest("div"));
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

    t.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.problem_categories = [];
        t.mdl.el.problem_category_ids.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        $("#selectAllTickets").prop('checked', false);
        var type_val = parseInt($.trim(t.mdl.el.department_id.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val).done(function (data) {
                if (typeof data == "object" && data.data.length) {
                    t.data.problem_categories = data.data;
                    $.each(data.data, function (i, v) {
                        t.mdl.el.problem_category_ids.append(new Option(v.name, v.id));
                    });
                }
            }).always(function () {
                t.mdl.el.problem_category_ids.trigger("change");
            });
        } else {
            t.mdl.el.problem_category_ids.trigger("change");
        }
    };

    t.refillSubCategory = function (e) {
        t.mdl.el.sub_category_ids.empty();
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.data.sub_categories = [];
        var type_val = parseInt($.trim(t.mdl.el.problem_category_ids.val()));
        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.data.problem_categories, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.data.sub_categories = v.sub;
                            $.each(v.sub, function (j, k) {
                                t.mdl.el.sub_category_ids.append(new Option(k.name, k.id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) {
                console.log(e);
            }
        }
        t.updateSubCategoryVisibility();
        t.mdl.el.sub_category_ids.trigger("change");
    };

    t.updateSubCategoryVisibility = function () {
        if (t.data.sub_categories && t.data.sub_categories.length > 0) {
            t.mdl.el.sub_category_ids.rules("add", { required: true, str_name: true });
            t.mdl.el.subCategoryIdCvr.show();
        } else {
            t.mdl.el.sub_category_ids.rules("remove");
            t.mdl.el.subCategoryIdCvr.hide();
        }
    };
    t.mdl.el.company_id.on('change', function () {
        if(!t.mdl.el.forAction.val()){
            t.mdl.el.department_id.val(null).trigger('change');
            t.mdl.el.problem_category_ids.val(null).trigger('change');
            t.mdl.el.sub_category_ids.val(null).trigger('change');
            t.mdl.el.problem_handler_id.val(null).trigger('change');
        }
        t.data.problem_categories = [];
        t.data.sub_categories = [];
        t.fillDepartment();
    });

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                let buttons = '';
                if (jQuery.inArray("EditProblemManagement", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActEdit" data-id="${d}"  data-bs-toggle="tooltip" title="Edit">
                            <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
                        </button>
                    `;
                }
                if (jQuery.inArray("DeleteProblemManagement", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn dtActDel me-1" data-toggle="tooltip" data-placement="top"  data-bs-toggle="tooltip" title="Delete" data-id="${d}" >
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                        </button>
                    `;
                }
                if (jQuery.inArray("ManageProblemManagement", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn view-user me-1" data-toggle="tooltip" data-placement="top"  data-bs-toggle="tooltip" title="Manage" data-id="${d}" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </button>
                    `;
                }
                return buttons || '';
            };
        },
    };

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
        autoWidth: false,
        scrollX: true,
        dom: 'drtip',
        colResize: { resizeTable: true },
        aoColumnDefs: [
            { 'bSortable': false, 'aTargets': [10], render: t.tblHelpers.actions() },
            {
                targets: [7],
                render: function (d) {
                    html = d + "<br>";
                    html += '<div class="outer">' + "<div class='inner' style='width:" + d + ";'></div>" + "</div>";
                    return html;
                }
            },
            {
                targets: [8],
                render: function (d) {
                    html = d + "<br>";
                    html += '<div class="outer">' + "<div class='inner' style='width:" + d + ";'></div>" + "</div>";
                    return html;
                }
            }
        ],
        order: [[9, 'desc']],
        processing: true,
        serverSide: true,
        deferLoading: 1,
        ajax: {
            url: t.config.url.ajaxlist + "/" + t.config.listview,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.filters = t.config.other_filters;
                d.main_filter = t.config.main_filter;
                d.company_id = companyId;
            }
        },
        fixedColumns: { rightColumns: 1 },
        columns: [
            { data: 'company_name', className: "amg-table-col-188" },
            {
                data: "problem_name",
                className: "amg-table-col-264",
                render: function (data) {

                    if (!data) return "";

                    data = String(data);

                    if (data.length <= 30) {
                        return data;
                    }

                    return `
                        <span class="short-text">${data.substring(0,30)}...</span>
                        <span class="full-text d-none">${data}</span>
                        <a href="javascript:void(0)" class="toggle-read-more ms-1">Read More</a>
                    `;
                }
            },
            { data: 'priority_name', className: "amg-table-col-188" },
            { data: 'deparment_name', className: "amg-table-col-188" },
            { data: 'pc_categorry', className: "amg-table-col-188" },
            { data: 'sc_category', className: "amg-table-col-188" },
            { 'bSortable': false, data: 'devicecountnum' },
            { 'bSortable': false, data: 'ticketcountnum' },
            { data: 'created_at', className: "amg-table-col-176" },
            { data: 'updated_at', className: "amg-table-col-176" },
            { data: 'id', className: "amg-table-col-144" },
        ],
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    $(document).off("click", ".toggle-read-more").on("click", ".toggle-read-more", function (e) {
        e.preventDefault();

        var $link = $(this);
        var $short = $link.siblings(".short-text");
        var $full = $link.siblings(".full-text");

        if ($full.hasClass("d-none")) {
            // Expand
            $short.addClass("d-none");
            $full.removeClass("d-none");
            $link.text("Read Less");
        } else {
            // Collapse
            $full.addClass("d-none");
            $short.removeClass("d-none");
            $link.text("Read More");
        }
    });

    // UI event bindings
    t.pageLengthSelect.on("change", function () {
        var newLength = parseInt($(this).val(), 10);
        t.dTbl.page.len(newLength).draw();
    });

    t.refreshBtn.on("click", function (e) {
        e.preventDefault();
        t.reload();
    });

    t.addBtn.on("click", function (e) {
        e.preventDefault();
        t.addProblem(e);
    });

    t.downloadBtn.on("click", function (e) {
        e.preventDefault();
        t.export(e);
    });

    t.searchbox.on("keypress", function (e) {
        if (e.which === 13) {
            e.preventDefault();
            var v = $(this).validate_str_param();
            if (v === false) {
                alert("Please enter a valid value for search");
                return false;
            }
            t.dTbl.search(v).draw();
        }
    });

    $(".btn-open-filter").on("click", function () {
        $("#advance-filters").modal("show");
    });

    t.cache_filter_values = function () {
        var v = $.trim(t.searchbox.val());
        t.config.search = v;
        t.config.other_filters = {};
        t.config.other_filters.priority = t.filters.priority.val();
        t.config.other_filters.department = t.filters.department.val();
        t.config.other_filters.problem_category = t.filters.problem_category.val();
        t.config.other_filters.sub_category = t.filters.sub_category.val();
        t.config.other_filters.handler = t.filters.handler.val();
        t.config.other_filters.company = t.filters.company.val();
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        t.updateFilterCountBadge();
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = t.searchbox.validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.searchFilteredDevice = function (e) {
        $('.filterDeviceBtn').text('Please Wait..').attr('disabled', true);
        $.ajax({
            method: 'post',
            url: t.config.url.searchFilteredDevice,
            data: {
                'category': t.filters.category.val(),
                'manufacturer': t.filters.manufacturer.val(),
                'model': t.filters.model.val(),
            },
            success: function (data) {
                t.mdl.el.device_id.empty();
                if (data.results.length > 0) {
                    $.each(data.results, function (i, k) {
                        t.mdl.el.device_id.append(new Option(k.asset_tag, k.id, true, true));
                    });
                    t.mdl.el.device_id.trigger("change");
                }
                $('.filterDeviceBtn').text('Filter').attr('disabled', false);
            },
            fail: function () {
                $('.filterDeviceBtn').text('Filter').attr('disabled', false);
            }
        });
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = t.searchbox.val();
        if (v === false) {
            alert("Please enter a valid value for search");
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.reload = function () {
        var v = t.searchbox.val();
        if (v !== false) {
            t.dTbl.search(v).draw();
            return;
        }
        t.dTbl.ajax.reload();
    };

    t.resetFrm = function () {
        t.mdl.frm.trigger("reset");
        t.mdl.el.forAction.val("");
        t.mdl.el.id.val("");
        t.mdl.el.name.val("");
        t.mdl.el.priority_id.val("").trigger("change").attr('disabled', false);
        t.mdl.el.ticket_id.val("").trigger("change").attr('disabled', false);
        t.mdl.el.device_id.val("").trigger("change").attr('enable', false);
        t.mdl.filterDeviceDiv.hide();
        t.mdl.el.company_id.val("").trigger("change");
        t.filters.category.val("").trigger("change");
        t.filters.model.val("").trigger("change");
        t.filters.manufacturer.val("").trigger("change");
        t.mdl.el.department_id.val("").empty().trigger("change").attr('disabled', false);
        t.mdl.el.problem_category_ids.val("").empty().trigger("change").attr('disabled', false);
        t.mdl.el.sub_category_ids.val("").empty().trigger("change").attr('disabled', false);
        t.mdl.el.problem_handler_id.val("").empty().trigger("change").attr('disabled', false);
        t.mdl.el.descriptions.val("").summernote('code', '');
        $('.summernote').summernote('enable');
        t.updateSubCategoryVisibility();
        t.deviceDTbl.clear().draw();
        t.ticketDTbl.clear().draw();
    };

    t.loadForm = function (data, forAction) {
        t.resetFrm();
        var impact_id = data.id;
        t.filterproblem(impact_id);
        t.refillPriority();
        if (data.department_id != "" || data.department_id != null) {
            t.mdl.el.department_id.val(data.department_id).empty().append(new Option(data.deparment_name, data.department_id, true, true)).trigger("change");
        }
        if (data.priority_id != "" || data.priority_id != null) {
            t.mdl.el.priority_id.val(data.priority_id).trigger("change");
        }
        if (data.problem_category_id != "" || data.problem_category_id != null) {
            t.mdl.el.problem_category_ids.val(data.problem_category_id).empty().append(new Option(data.pc_category, data.problem_category_id, true, true)).trigger("change");
        }
        if (data.sub_category_id != "" || data.sub_category_id != null) {
            t.mdl.el.sub_category_ids.val(data.sub_category_id).empty().append(new Option(data.sc_category, data.sub_category_id, true, true)).trigger("change");
        }
        if (data.problem_handler_id != "" || data.problem_handler_id != null) {
            t.mdl.el.problem_handler_id.val(data.problem_handler_id).empty();
            $.each(data.problem_handler_id, function (i, v) {
                t.mdl.el.problem_handler_id.append(new Option(v.text, v.id, true, true)).trigger("change");
            });
        }
        if (data.company_id != null && data.company_id != null) {
            t.mdl.el.company_id.val(data.company_id).empty().append(new Option(data.company_name, data.company_id, true, true));
        }
        t.mdl.el.name.val(data.subject_name);
        t.mdl.el.descriptions.summernote('code', data.content);
        t.mdl.el.id.val(data.id);
        t.loadAndDisplayDevices(data.id, function () {
            t.refreshDeviceTable();
        });
        // console.log(data,'dfata for edit')
        // if (data.tickets && Array.isArray(data.tickets) && data.tickets.length > 0) {
        //     t.loadTicketsForEdit(data.tickets);
            t.refreshTicketTable();
        // }
    };

    t.refillDeviceImpact = function (e) {
        t.mdl.el.device_id.val("");
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.mdl.el.device_id.empty().append(new Option(config.translations.select_impacted_device, ""));
        $.each(t.config.devices, function (i, d) {
            t.mdl.el.device_id.append(new Option(d.name, d.id));
        });
        t.mdl.el.device_id.trigger("change");
    };

    t.refillTicketImpact = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.mdl.el.ticket_id.empty().append(new Option(config.translations.select_impacted_ticket, ""));
        $.each(t.config.impact_ticket, function (i, tk) {
            t.mdl.el.ticket_id.append(new Option(tk.subject, tk.id));
        });
        t.mdl.el.ticket_id.val("");
        t.mdl.el.ticket_id.trigger("change");
    };

    t.refillPriority = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.mdl.el.priority_id.empty().append(new Option(config.translations.select_priority, ""));
        $.each(t.config.priorities, function (i, k) {
            t.mdl.el.priority_id.append(new Option(k.name, k.id));
        });
        t.mdl.el.priority_id.trigger("change");
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
        var formData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.dTbl.ajax.reload();
                    t.mdl.el.descriptions.val('').summernote('code', '');
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = { 'msg': t.config.translations.something_went_wrong };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.fillDepartment = function () {
        if (typeof t.config.departments != "undefined" && Array.isArray(t.config.departments) == true && t.config.departments.length > 0) {
            t.mdl.el.department_id.empty();
            $.each(t.config.departments, function (i, v) {
                t.mdl.el.department_id.append(new Option(v.name, v.id));
            });
            t.mdl.el.department_id.closest(".row").show();
            t.mdl.el.department_id.trigger("change");
        }
    };

    t.addProblem = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.frmValidator.resetForm();
        t.httpPostPath = t.config.url.add_problem;
        t.refillPriority();
        t.fillDepartment();
        t.mdl.title.html(config.translations.add_problem_heading);
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.mdl.el.company_id.append(option).trigger('change');
        }
        initTicketSelect2();
        $('#addproblemModal').modal('show');
    };

    t.editStatus = function (e) {
        t.resetFrm();
        e.preventDefault();
        var problemId = $(this).attr("data-id");
        t.httpPostPath = config.url.update;
        var http = $.get(t.config.url.edit + "/" + problemId);
        http.done(function (response) {
            if (typeof response == "object") {
                if (response.status == "success") {
                    t.mdl.title.html(config.translations.up_heading);
                    t.mdl.btnSubmit.text("Update");
                    t.mdl.el.forAction.val("edit");
                    t.mdl.el.id.val("edit");
                    var problemData = response.data;
                    if (!problemData.tickets) problemData.tickets = [];
                    initTicketSelect2();
                    t.loadForm(problemData);
                    t.mdl.modal('show');
                } else {
                    sweetAlert('center', 'error', response);
                }
            }
        });
        http.fail(function () {
            var data = { 'msg': t.config.translations.something_went_wrong };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.updateStatus = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        t.httpPostPath = t.config.url.add_problem;
        var formData = new FormData($('#addproblem-mdl-frm')[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    setTimeout(function () {
                        window.location = t.config.url.listview;
                    }, 800);
                } else {
                    vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            alert("Something went wrong. Please refresh page and try again");
        });
    };

    t.deleteProblem = function (e) {
        e.preventDefault();
        var Id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + Id;
        sweetAlertConfirmation({
            message: config.translations.delete_record,
            onConfirm: function () {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.dTbl.ajax.reload();
                        } else {
                            sweetAlert('center', 'error', data);
                            t.refreshInfoTab();
                        }
                    }
                });
                http.fail(function () {
                    var data = { 'msg': t.config.translations.something_went_wrong };
                    sweetAlert('center', 'error', data);
                });
                http.always(function () {
                    t.httpCall = true;
                });
            }
        });
    };

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + "&company_id=" + companyId;
    };
    t.viewProblemUsers = function(e) {
        e.preventDefault();
        var problemId = $(e.currentTarget).attr("data-id");
        if (problemId && t.config.url.Problem_Manage) {
            window.location.href = t.config.url.Problem_Manage + "/" + problemId;
        } else {
            console.error("Problem ID or view URL is missing");
            alert("Unable to open problem view. Please try again.");
        }
    };
    // Department select2 in modal
    t.mdl.el.department_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.el.department_id.parent(),
        allowClear: true,
        placeholder: config.translations.Departement,
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            delay: 300,
            data: function (params) {
                var companyId = t.mdl.el.company_id.val();
                if (!companyId) return false;
                return {
                    search: params.term || '',
                    page: params.page || 1,
                    company_id: companyId
                };
            }
        }
    })).on("change", $.proxy(t.refillProblemCategory));

    // Filters select2
    t.filters.department.select2($.extend({}, select2Opts, {dropdownParent: t.filters.department.parent(), placeholder: config.translations.filter_by_department }));
    t.filters.priority.select2($.extend({}, select2Opts, {dropdownParent: t.filters.priority.parent(), placeholder: config.translations.Filter_By_Priority }));
    t.filters.problem_category.select2($.extend({}, select2Opts, {dropdownParent: t.filters.problem_category.parent(), placeholder: config.translations.Filter_By_Problem_Category }));
    t.filters.sub_category.select2($.extend({}, select2Opts, {dropdownParent: t.filters.sub_category.parent(), placeholder: config.translations.Filter_By_Sub_Category }));
    t.filters.handler.select2($.extend({}, select2Opts, {dropdownParent: t.filters.handler.parent(), placeholder: config.translations.Select_the_problem_Handler }));
    t.filters.fun.reload_department();
    t.filters.fun.reload_priority();
    t.filters.fun.reload_handler();
    t.filters.department.on("change", $.proxy(t.filters.fun.reload_problem_category));
    t.filters.problem_category.on("change", $.proxy(t.filters.fun.reload_sub_category));

    t.filters.wrapper.find(".btn-filter").on("click", function() {
        t.cache_filter_values();
        t.reload();
        $("#advance-filters").modal("hide");
    });

    t.filters.btnfilterclr.on("click", function() {
        t.btnClrFilter();
        $("#advance-filters").modal("hide");
    });

    t.mdl.filterDeviceDiv.on("click", "button", function (ev) {
        ev.preventDefault();
        t.searchFilteredDevice();
    });

    t.checkTechnicalAvabilityForTransfer = function () {
        var attendarId = t.mdl.el.problem_handler_id.val();
        t.mdl.find(".availabilityError, .availabilitySuccess").html("");
        if (attendarId == '') return false;
        $.ajax({
            url: t.config.url.getTechCurrentStatusById + '/' + attendarId,
            method: 'GET',
            success: function (result) {
                if (result.is_logged_in == false || result.is_logged_in == undefined) {
                    t.mdl.find(".availabilityError").html("The technician is unavailable but still you can assign ticket. Technician will revert only once he/she will available.");
                } else {
                    t.mdl.find(".availabilitySuccess").html("The technician is available.");
                }
            }
        });
    };

    t.mdl.el.problem_handler_id.select2($.extend({}, {
        width: "100%",
        dropdownParent: t.mdl.el.problem_handler_id.parent(),
        ajax: {
            url: t.config.url.get_users_to_assign_by_dep_by_availability,
            dataType: "json",
            data: function (p) {
                return {
                    department_id: t.mdl.el.department_id.val(),
                    search: p.term,
                    page: p.page || 1
                };
            },
        },
        placeholder: "Select User",
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            var imgPaddingLeft = "57px";
            return t.userDropdownFormat(data, imgPaddingLeft);
        },
    })).on('change', t.checkTechnicalAvabilityForTransfer);

    t.userDropdownFormat = function (s, imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }
        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + s.text + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + s.email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    t.filterproblem = function (impact_id) {
        var Id = impact_id;
        var http = $.ajax({
            url: t.config.url.filters + "/" + Id,
            type: "get",
            data: function (d) {
                d._token = t.config.token;
            }
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    if (data.data != "") {
                        $.each(data.data, function (i, ticket) {
                            var option = new Option(ticket.text, ticket.id, true, true);
                            $(option).data('ticket', ticket); // store full ticket data for refreshTicketTable
                            t.mdl.el.ticket_id.append(option).trigger('change');
                        });
                    }
                } else {
                }
            }
        });
        http.fail(function () {
            alert("Something went wrong. Please refresh page and try again");
        });
    };

    // Device select2
    t.mdl.el.device_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.el.device_id.parent(),
        ajax: {
            url: t.config.url.getUserDeviceByAjax,
            dataType: "json",
            data: function (p) {
                return {
                    problemManagementApiCall: true,
                    search: p.term,
                    page: p.page || 1,
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: config.translations.select_impacted_device,
        templateResult: function (s) {
            if (typeof s.loading != "undefined" && s.loading) {
                return $("<div>" + s.text + "</div>");
            }
            a = "<div class='so-t'><i class=\"fa fa-tag\"></i> " + s.asset_tag + "</div>";
            if (s.asset_name != null) {
                a += "<div class='so-t'><i class=\"fa fa-laptop\"></i> " + s.asset_name + "</div>";
            }
            a += "<div class='so-m'><i class=\"fa fa-tablet\"></i> " + s.name + " " + s.modelno + "</div>";
            a += "<div class='so-t'><i class=\"fa fa-barcode\"></i> " + s.serial + "</div>";
            return $("<div>" + a + "</div>");
        }
    })).on('change', function () {
        t.refreshDeviceTable();
    });

    t.mdl.el.department_id.on('change', function () {
        if ($('#addproblemModal').is(':visible')) {
            initTicketSelect2();
            t.mdl.el.ticket_id.val(null).trigger('change');
        }
    });

    t.mdl.el.ticket_id.on('change', function () {
        t.refreshTicketTable();
    });

    $('#selectAllTickets').on('change', function () {
        if ($(this).is(':checked')) {
            if (!t.mdl.el.department_id.val()) {
                alert('Please select a department first.');
                $(this).prop('checked', false);
                return;
            }
            $.ajax({
                url: t.config.url.getTicketDetails,
                data: {
                    department: t.mdl.el.department_id.val(),
                    per_page: 1000
                },
                success: function (res) {
                        var tickets = res.results || res.data || [];
                        t.mdl.el.ticket_id.empty();
                        $.each(tickets, function (i, ticket) {
                            var option = new Option(ticket.text || ticket.subject, ticket.id, true, true);
                            $(option).data('ticket', ticket);
                            t.mdl.el.ticket_id.append(option);
                        });
                        var allIds = tickets.map(function (ticket) { return ticket.id; });
                        t.mdl.el.ticket_id.val(allIds).trigger('change.select2');
                        t.refreshTicketTable();
                },
                error: function () {
                    alert('Could not fetch tickets. Please try again.');
                    $('#selectAllTickets').prop('checked', false);
                }
            });
        } else {
            t.mdl.el.ticket_id.val(null).trigger('change');
        }
    });

    // Company select2
    t.mdl.el.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.el.company_id.parent(),
        placeholder: "Select Company",
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text?.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    }));

    t.btnClrFilter = function () {
        t.filters.company.val("").trigger("change");
        t.filters.department.val("").trigger("change");
        t.filters.problem_category.val("").trigger("change");
        t.filters.sub_category.val("").trigger("change");
        t.filters.priority.val("").trigger("change");
        t.filters.handler.val("").trigger("change");
        // Also clear other_filters and update badge
        t.cache_filter_values();
        t.reload();
    };

    t.filters.fun.reload_category();
    t.filters.fun.reload_manufacturer();
    t.filters.fun.reload_model();

    t.table.on("click", ".dtActEdit", $.proxy(t.editStatus));
    t.table.on("click", ".dtActDel", $.proxy(t.deleteProblem));
    t.table.on("click", ".view-user", $.proxy(t.viewProblemUsers));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
    t.mdl.el.problem_category_ids.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.el.problem_category_ids.parent() })).on("change", $.proxy(t.fillSla));
    t.mdl.el.problem_category_ids.on("change", $.proxy(t.refillSubCategory));
    t.mdl.el.sub_category_ids.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.el.sub_category_ids.parent() })).on("change", $.proxy(t.fillSla));
    t.mdl.el.priority_id.select2($.extend({}, select2Opts, { dropdownParent: t.mdl.el.priority_id.parent() })).on("change", $.proxy(t.refillTat));
    t.dTbl.ajax.reload();
};