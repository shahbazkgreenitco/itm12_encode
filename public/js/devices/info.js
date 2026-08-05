var MyApp = function(config) {
    var t = this;

    function formatBytes(bytes) {
        if (bytes >= 1024 * 1024) {
            return (bytes / (1024 * 1024)) + ' MB';
        }

        if (bytes >= 1024) {
            return (bytes / 1024) + ' KB';
        }

        return bytes + ' bytes';
    }

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
        return Array.isArray(config.permissions) && config.permissions.indexOf(name) !== -1;
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
        },
        clone: function() {
            return `<svg viewBox="0 0 16 16" fill="none"><path d="M14.375 0H4.375C4.20924 0 4.05027 0.0658481 3.93306 0.183058C3.81585 0.300269 3.75 0.45924 3.75 0.625V3.75H0.625C0.45924 3.75 0.300269 3.81585 0.183058 3.93306C0.0658481 4.05027 0 4.20924 0 4.375V14.375C0 14.5408 0.0658481 14.6997 0.183058 14.8169C0.300269 14.9342 0.45924 15 0.625 15H10.625C10.7908 15 10.9497 14.9342 11.0669 14.8169C11.1842 14.6997 11.25 14.5408 11.25 14.375V11.25H14.375C14.5408 11.25 14.6997 11.1842 14.8169 11.0669C14.9342 10.9497 15 10.7908 15 10.625V0.625C15 0.45924 14.9342 0.300269 14.8169 0.183058C14.6997 0.0658481 14.5408 0 14.375 0ZM10 13.75H1.25V5H10V13.75ZM13.75 10H11.25V4.375C11.25 4.20924 11.1842 4.05027 11.0669 3.93306C10.9497 3.81585 10.7908 3.75 10.625 3.75H5V1.25H13.75V10Z" fill="currentColor"></path></svg>`;
        }
    }

    t.content = $("main.main-content");

    t.phases = {
        maintenances: new MaintenancePhase(config, t),
        accessories: new AccessoryPhase(config),
        consumable: new ConsumablePhase(config),
        components: new ComponentPhase(config),
        licenses: new LicensePhase(config),
        documents: new DocumentPhase(config, t),
        history: new HistoryPhase(config),
        info: new DevicePhase(config),
    };

    new DocumentUploadPhase($.extend({}, config, { tbl: t.phases.documents }), t);

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        t.phases[e.target.id]?.load();
    });

    // common pagelength select2 code
    $('.datatable-pagelength').select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    $(document).on('change', '.datatable-pagelength', function () {
        const table = $(this).data('select-table');
        t.phases[table]?.dTbl?.page.len(parseInt(this.value, 10)).draw();
    });

    // common reload code
    $(document).on('click', '.btn-reload-list', function () {
        const table = $(this).data('select-table');
        t.phases[table]?.dTbl.ajax.reload();
    });

    // common search code on click
    $(document).on('click', '.datatable-search-icon', function (e) {
        e.preventDefault();
        const table = $(this).data('select-table');
        const value = $(this).siblings('.datatable-search-input').validate_str_param();

        if (value === false) {
            alert(config.translations.please_enter_valid_search);
            return;
        }

        t.phases[table]?.dTbl.search(value).draw();
    });

    // common search code on enter
    $(document).on('keyup', '.datatable-search-input', function(e) {
        if (e.keyCode == 13 || this.value.length == 0) {
            const table = $(this).data('select-table');
            var value = $(this).validate_str_param();
            
            if (value === false) {
                alert(config.translations.please_enter_valid_search);
                return;
            }

            t.phases[table]?.dTbl.search(value).draw();
        }
    });

    window.downloadFile = function (url) {
        $('<a>').attr('href', url).attr('download', '')[0].click();
    }
}

var MaintenancePhase = function(config, app) {
    var t = this;
    t.app = app;
    t.config = config;
    t.tab = $("main.main-content").find("#maintenances-tab");
    t.table = t.tab.find("#tblMaintenance");

    t.httpCall = true;
    t.httpPostPath = "";

    t.mdl = $("main.main-content").find("#dev-main-mdl");
    t.mdl.title = t.mdl.find('.modal-title');
    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');

    t.mdl.frm = t.mdl.find("#dev-main-mdl-frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.id = t.mdl.frm.find("#id");
    t.mdl.frmEl.live_monitor_field = t.mdl.frm.find('.live_monitor_hidden_field');
    t.mdl.frmEl.forAction = t.mdl.frm.find("#forAction");
    t.mdl.frmEl.device = t.mdl.frm.find("#asset_id");
    t.mdl.frmEl.supplier = t.mdl.frm.find("#supplier_id");
    t.mdl.frmEl.types = t.mdl.frm.find("#expense_type");
    t.mdl.frmEl.expense_date = t.mdl.frm.find("#expense_date");
    t.mdl.frmEl.start_date = t.mdl.frm.find("#start_date");
    t.mdl.frmEl.currency_format = t.mdl.frm.find("#currency_format");
    t.mdl.frmEl.end_date = t.mdl.frm.find("#completion_date");
    t.mdl.frmEl.notes = t.mdl.frm.find("#notes");
    t.mdl.frmEl.cost = t.mdl.frm.find("#cost");
    t.mdl.frmEl.start_date.on('changeDate', function (e) {
        t.mdl.frmEl.end_date.datepicker('setStartDate', e.date);
        try {
            if(t.mdl.frmEl.end_date.datepicker('getDate') < e.date){
                t.mdl.frmEl.end_date.datepicker('update', start_date);
            }
        }
        catch(err) {
            t.mdl.frmEl.end_date.datepicker('update', '');
        }
    });

    t.cache_filter_values = function() {
        var v = $.trim($('.searchbox').val());
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };
    
    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
                [5, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.dm.list,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
                    d.call_from = "device_info";
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
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a.expense_types',
                    name: "expense_type",
                },
                {
                    data: 'a.title',
                    name: "title",
                },
                {
                    data: 'a.expense_date_format',
                    name: "expense_date",
                },
                {
                    data: null,
                    name: "cost",
                    render: function (data, type, row) {
                        return row.a.currency_symbol + ' ' + row.a.cost_format;
                    }
                },
                {
                    data: 'a.last_updated_at',
                    name: "updated_on",
                },
                {
                    data: "a.id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
                },
            ]
        });
    }
    
    t.resetFrm = function() {
        t.mdl.frm.trigger("reset");
        t.frmValidator.resetForm();
        t.mdl.frmEl.device.empty();
        t.mdl.frmEl.types.val("").trigger("change");
        t.mdl.frm.find("input[name='is_warranty']").attr("checked", false);
        t.mdl.frmEl.forAction.val("");
        t.mdl.frm.find("#id").val("");
        t.mdl.frmEl.notes.val("");
        t.mdl.optionsCurrency();
    };

    // Add a custom method for cost validation
    $.validator.addMethod("validCost", function(value, element) {
        return this.optional(element) || /^\d{1,11}(\.\d{1,2})?$/.test(value);
    }, config.translations.pls_enter_valid_cost_length);

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            asset_id: {
                required: true
            },
            expense_type: {
                required: true
            },
            title: {
                required: true,
                str_name: true,
                clean_text_only : true,
                maxlength: 100
            },
            expense_date: {
                required:true
            },
            cost: {
                number: true,
                validCost: true
            },
            notes: {
                remarks: true,
                clean_text_only : true,
                maxlength: 500
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
            var isSelect2 = $element.hasClass("select2-hidden-accessible");
            if (isSelect2) {
                $element.next(".select2-container").find(".select2-selection").addClass("amg-form-select-error");
            }
        },
        unhighlight: function(element, errorClass) {
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
        
    t.addDevice = function(e) {
        e.preventDefault();
        t.resetFrm();
        t.httpPostPath = t.config.url.dm.save;
        t.mdl.title.html(config.translations.add_expense);
        t.mdl.btnSubmit.text(config.translations.save_details);
        t.mdl.frmEl.forAction.val("add");
        t.mdl.frmEl.device.text(t.config.dropdown.device.text);
        t.mdl.modal("show");
    };

    t.editDevice = function(e) {
        e.preventDefault();
        var devId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.dm.save;
        var http = $.get(t.config.url.dm.get + "/" + devId + "/edit");
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.title.html(config.translations.edit_expense);
                    t.mdl.btnSubmit.text(config.translations.save_changes);
                    t.mdl.frmEl.forAction.val("edit");
                    t.loadForm(data.device_maintenance, "edit");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.cloneDevice = function(e) {
        e.preventDefault();
        var devId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.dm.save;
        var http = $.get(t.config.url.dm.get + "/" + devId + "/clone");
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.title.html(config.translations.add_expense);
                    t.mdl.btnSubmit.text(config.translations.save_changes);
                    t.mdl.frmEl.forAction.val("add");
                    t.loadForm(data.device_maintenance, "add");
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.deleteDevice = function(e) {
        e.preventDefault();
        var devId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.dm.delete + "/" + devId;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete_record, 'warning', t.httpPostPath, t.dTbl, data);
    };

    t.loadForm = function(dev, forAction) {
        t.resetFrm();
        if (dev.data.expense_type != "" || dev.data.expense_type != null) {
            t.mdl.frmEl.types.val(dev.data.expense_type).trigger("change");
        }

        if(dev.data.currency_format != "" && dev.data.currency_format != null) {
            t.mdl.frmEl.currency_format.val(dev.data.currency_format).trigger("change");
        }

        t.mdl.frmEl.device.append(dev.dropdown.device.text);
        t.mdl.frm.find("input[name='title']").val(dev.data.title);
        t.mdl.frm.find("input[name='cost']").val(dev.data.cost);
        t.mdl.frmEl.notes.val(dev.data.notes);
        t.mdl.frmEl.expense_date.datepicker("update", dev.data.expense_date);


        if (typeof dev.data.is_warranty != "undefined") {
            t.mdl.frm.find("input[name='is_warranty']").prop("checked", dev.data.is_warranty == 1);
        }

        if (typeof dev.data.is_amc != "undefined") {
            t.mdl.frm.find("input[name='is_amc']").prop("checked", dev.data.is_amc == 1);
        }

        if (typeof forAction != "undefined" && forAction != "") {
            t.mdl.frmEl.forAction.val(forAction);
            if (forAction == "edit") {
                t.mdl.frm.find("input[name='id']").val(dev.data.id);
            }
        }

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
            data: formData
        });
        http.done(function(data) {
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
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.fillMaintenanceTypeOpts = function() {
        t.mdl.frmEl.types.empty().append(new Option(config.translations.select_the_type, ""));

        // keeping maintenance types static in this case, instead of calling an api for the same since only 5 static values present from assetExpense model
        $.each(t.config.maintenanceTypes, function(i, d) {
            t.mdl.frmEl.types.append(new Option(d.text, d.id, true, true));
        });
        return t.mdl.frmEl.types;
    };

    t.exportExpense = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        var exportUrl = config.url.dm.export +"?q=" + t.config.export_filters + "&device_id=" + t.config.device_id;
        window.location = exportUrl;
    };

    t.renderActionsCell = function (record, type) {
        var t = this;
        var actionState = t.getRowActionState(record);
        var quickActions = [];
        if (type !== "display") return "";
        if (actionState.canEdit) {
            quickActions.push(t.app.quickActionButtonHtml(
                (config.translations || {}).action_edit_supplier || "Edit",
                "dtActEdit",
                record.id,
                "edit",
                "btn-edit-purchase"
            ));
        }
        if (actionState.canDelete) {
            quickActions.push(t.app.quickActionButtonHtml(
                (config.translations || {}).action_delete_supplier || "Delete",
                "dtActDel",
                record.id,
                "delete",
                "go-del"
            ));
        }
        if (actionState.canClone) {
            quickActions.push(t.app.quickActionButtonHtml(
                (config.translations || {}).action_download_supplier || "view",
                "dtActClone",
                record.id,
                "clone",
                "tri-view",
                "data-type='consumable'"
            ));
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
            canEdit: t.app.hasPermission("DeviceExpenseEdit"),
            canClone: t.app.hasPermission("DeviceExpenseAdd"),
            canDelete: t.app.hasPermission("DeviceExpenseDelete")
        };
    };

    var select2Opts = { width: "100%", allowClear: true };
    t.fillMaintenanceTypeOpts().select2($.extend({}, select2Opts, {placeholder: config.translations.select_the_type, dropdownParent: t.mdl.frmEl.types.parent() }));
    t.mdl.optionsCurrency = function() {
        t.mdl.frmEl.currency_format.empty().append(new Option(config.translations.select_currency_format, ""));

        // keeping currencies static as of now instead of calling an api for the same since there are only limited static values present from the currency model present in this case.
        $.each(t.config.currencies, function(i, v) {
            var opt = t.config.default_currency_format == i ? new Option("", i, true, true) : new Option("", i);
            opt.innerHTML = v.name + " (" + v.symbol_html + ")";
            t.mdl.frmEl.currency_format.append(opt);
        });
        t.mdl.frmEl.currency_format.trigger("change");
    };

    t.mdl.frmEl.currency_format.select2($.extend({},select2Opts,{placeholder: config.translations.select_currency_format, dropdownParent:t.mdl.frmEl.currency_format.parent()}));
    t.mdl.frmEl.start_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    t.mdl.frmEl.end_date.datepicker({ autoclose: true, format: "dd/mm/yyyy" });
    t.mdl.frmEl.expense_date.datepicker({ autoclose: true, format: "dd/mm/yyyy", endDate: "today" });

    t.tab.on("click", ".btn-add-maintenance", $.proxy(t.addDevice));
    t.tab.on("click", ".btn-export-maintenance", $.proxy(t.exportExpense));
    t.table.on("click", ".dtActEdit", $.proxy(t.editDevice));
    t.table.on("click", ".dtActClone", $.proxy(t.cloneDevice));
    t.table.on("click", ".dtActDel", $.proxy(t.deleteDevice));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};

var AccessoryPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("main.main-content").find("#accessories-tab");
    t.table = t.tab.find("#tblAccessory");

    t.tblHelpers = {
        name: function(url) {
            return function(d) {
                var acc = [];
                acc.push(`<a href=${url.accessory_info}/${d.accessory_id}>${d.name}</a>`);
                acc.push(`<div class='textCategory' data-bs-toggle='tooltip' data-bs-title='${config.translations.batch_no}' data-original-title='${config.translations.batch_no}'>${d.acc_tag}</div>`);
                return acc.join(' ');
            }
        }
    };

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            aoColumnDefs: [
                {
                    targets: 1,
                    render: t.tblHelpers.name(t.config.url)
                }
            ],
            order: [
                [5, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_accessories,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
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
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a',
                    name: 'accessory',
                },
                {
                    data: 'a.cat_name',
                    name: 'category',
                },
                {
                    data: 'a.cost',
                    name: 'cost',
                },
                {
                    data: 'a.order_number',
                    name: 'order_number',
                },
                {
                    data: 'a.checkout_at',
                    name: 'checkout_date',
                },
                {
                    data: 'a.expected_checkin_at',
                    name: 'checkin_date',
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let checkout_html = `
                            <div class="user-list-actions justify-content-start">
                                <a href="${t.config.url.accessory_info}/${row.a.accessory_id}?checkin=${row.a.id}" class="user-list-action-btn" data-bs-toggle="tooltip" data-id="${row.a.id}" data-bs-title="${config.translations.check_in}" aria-label="${config.translations.check_in}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z" fill="currentColor"></path>
                                </svg>
                                </a>
                            </div>
                        `;
                        return checkout_html;
                    }
                }
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            }
        });
    }
};

var ConsumablePhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("main.main-content").find("#consumable-tab");
    t.table = t.tab.find("#tblConsumable");

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            aoColumnDefs: [
                {
                    targets: [1, 2,3],
                    render: function (d) {
                        return d ? d : "";
                    }
                }
            ],
            order: [
                [5, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_consumables,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
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
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a.con_tag',
                    name: 'batch'
                },
                {
                    data: 'a.name',
                    name: 'consumable'
                },
                {
                    data: 'a.cat_name',
                    name: 'category'
                },
                {
                    data: 'a.cost',
                    name: 'cost'
                },
                {
                    data: 'a.checkout_at',
                    name: 'checkout_date'
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let checkout_html = `
                            <div class="user-list-actions justify-content-start">
                                <a href="${t.config.url.consumable_info}/${row.a.consumable_id}?open=${row.a.id}&popup=revoke" class="user-list-action-btn" data-bs-toggle="tooltip" data-id="${row.a.id}" data-bs-title="${config.translations.check_in}" aria-label="${config.translations.check_in}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z" fill="currentColor"></path>
                                </svg>
                                </a>
                            </div>
                        `;
                        return checkout_html;
                    }
                }
            ]
        });
    }
};

var ComponentPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("main.main-content").find("#components-tab");
    t.table = t.tab.find("#tblComponent");

    t.tblHelpers = {
        name: function(url) {
            return function(d) {
                var cc = [];
                cc.push(`<a href="${url.component_info}/${d.component_id}">${d.name}</a>`);
                cc.push(`<div class="textCategory", data-bs-toggle="tooltip", data-original-title="${config.translations.component_unique_tag}" data-bs-title="${config.translations.component_unique_tag}">${d.unique_tag}</div>`);
                return cc.join(' ');
            }
        }
    };

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            aoColumnDefs: [
                {
                targets: 1,
                render: t.tblHelpers.name(t.config.url)
            }, {
                targets: [1, 2, 3, 4],
                render: function (d) {
                    return d ? d : "";
                }
            }],
            order: [
                [4, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.component_list,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
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
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a',
                    name: "component",
                },
                {
                    data: 'a.co_cat_name',
                    name: "category",
                },
                {
                    data: 'a.cost',
                    name: "cost",
                },
                {
                    data: 'a.checkout_date',
                    name: "checkout_date",
                },
                {
                    data: 'a.checkin_date',
                    name: "checkin_date",
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let checkout_html = `
                            <div class="user-list-actions justify-content-start">
                                <a href="${t.config.url.component_info}/${row.a.component_id}?checkin=${row.a.id}" class="user-list-action-btn" data-bs-toggle="tooltip" data-id="${row.a.id}" data-bs-title="${config.translations.check_in}" aria-label="${config.translations.check_in}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z" fill="currentColor"></path>
                                </svg>
                                </a>
                            </div>
                        `;
                        return checkout_html;
                    }
                }
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            }
        });
    }
};

var LicensePhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("main.main-content").find("#licenses-tab");
    t.table = t.tab.find("#tblLicense");

    t.tblHelpers = {
        name: function(url) {
            return function(d) {
                return `<span class="textCategory" data-bs-toggle="tooltip" data-original-title="${config.translations.license_tag}" data-bs-title="${config.translations.license_tag}">${d.lic_batch_no}</span> - <a href="${url.license_detail}/${d.license_id}">${d.name}</a>`;
            };
        }
    };
    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            aoColumnDefs: [
                {
                    targets: 1,
                    render: t.tblHelpers.name(t.config.url)
                }
            ],
            order: [
                [1, 'asc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_licenses,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
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
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a',
                    name: 'license',
                },
                {
                    data: 'a.serial_no',
                    name: 'serial_no',
                },
                {
                    data: 'a.cost',
                    name: 'cost',
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let checkout_html = `
                            <div class="user-list-actions justify-content-start">
                                <a href="${t.config.url.license_checkin}/${row.a.id}" class="user-list-action-btn" data-bs-toggle="tooltip" data-id="${row.a.id}" data-bs-title="${config.translations.check_in}" aria-label="${config.translations.check_in}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M9 17L4 12L9 7V10H16V14H9V17ZM21 5H11V3H21C22.1 3 23 3.9 23 5V19C23 20.1 22.1 21 21 21H11V19H21V5Z" fill="currentColor"></path>
                                </svg>
                                </a>
                            </div>
                        `;
                        return checkout_html;
                    }
                }
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            }
        });
    }
};

var DocumentPhase = function(config, app) {
    var t = this;
    t.app = app;
    t.config = config;
    t.tab = $("main.main-content").find("#documents-tab");
    t.table = t.tab.find("#tblDocument");

    t.deleteDocument = function(e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.document_delete;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        var send_data = {
            "asset_id": t.config.device_id, "asset_type": "hardware", "id": docId, "_token": t.config.token, 
        }
        sweetAlertPost(config.translations.document_delete_permanently,'warning', t.httpPostPath, t.dTbl.ajax, data, send_data);
    };

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            ajax: {
                url: t.config.url.documents,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
                    d.asset_type = "hardware";
                }
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        let id = meta.row + meta.settings._iDisplayStart + 1;
                        return `${id}`;
                    },
                },
                {
                    data: 'a',
                    render: function (data) {
                        var a = [];
                        if(data.org_name != null) {
                            a.push('<div class="">' + data.org_name + '</div>');
                        } else if(data.checkin_attachment) {
                            a.push('<div class="">' + data.checkin_attachment + '</div>');
                        } else {
                            a.push("");
                        }

                        return a.join(' ');
                    }
                },
                {
                    data: 'a.created_at_format',
                    name: 'updated_on',
                },
                {
                    data: 'a.note',
                    name: 'notes',
                },
                {
                    data: "a.id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) { return t.renderActionsCell(row.a || {}, type); }
                },
            ],
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            }
        });
    }

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

        // using only for status 0 in this case since in backend only status 0 is given from backend
        const fileName = record.file_name ? record.file_name : record.checkin_attachment;
        if(fileName) {
            quickActions.push(t.app.quickActionButtonHtml(
                (t.config.translations || {}).action_download_asset || "Download",
                "dtActbtn",
                record.id,
                "download",
                `go-download`,
                `onclick="downloadFile('${t.config.url.document_download}/${fileName}')" data-bs-toggle="tooltip" data-bs-title="${(config.translations || {}).action_download_asset || "Download"}"`
            ));
        }

        if (actionState.canDelete) {
            quickActions.push(t.app.quickActionButtonHtml(
                (config.translations || {}).action_delete_asset || "Delete",
                "dtActDel",
                record.id,
                "delete",
                "go-del",
                `data-bs-toggle="tooltip" data-bs-title="${(config.translations || {}).action_delete_asset || "Delete"}"`
            ));
        }

        var validExtensions = ["pdf", "jpg", "jpeg", "png", "gif"];
        var fileExtension = record.org_name.split('.').pop().toLowerCase();
        if(validExtensions.includes(fileExtension)) {
            quickActions.push(t.app.quickActionButtonHtml(
                (t.config.translations || {}).action_view_asset || "View",
                "dtActbtn",
                record.id,
                "view",
                "tri-view",
                `data-type='hardware' data-bs-toggle="tooltip" data-bs-title="${(config.translations || {}).action_view_asset || "View"}"`
            ));
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
            canDelete: t.app.hasPermission("DeviceDocumentsDelete")
        };
    };

    t.tab.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.tab.on("click", ".dtActDel", $.proxy(t.deleteDocument));
};

var DocumentUploadPhase = function(config) {
    var t = this;
    t.config = config;
    t.httpCall = true;
    t.httpPostPath = "";
    t.mdl = $("main.main-content").find("#document-mdl");
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
        t.mdl.title.html(config.translations.new_document_upload);
        t.mdl.btnSubmit.text(config.translations.save);
        t.mdl.frmEl.token.val(t.config.token);
        t.mdl.frmEl.asset_id.val(t.config.device_id);
        t.mdl.frmEl.asset_type.val("hardware");
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
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    if (typeof t.config.tbl !== "undefined") {
                        t.config.tbl.dTbl.ajax.reload();
                    }
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            }
            sweetAlert('center', 'error', data);
        });
        http.always(function() {
            t.httpCall = true;
        });
    };

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            note: {
                remarks: true,
                clean_text_only : true
            },
            document: {
                required: true,
                extension: "png|gif|jpg|jpeg|doc|docx|pdf|txt|zip|rar|eml|msg|mbox|pst|xlsx|xls",
                filesize: 2000000
            }
        },
        messages: {
            document: {
                required: config.translations.pls_upload_document,
                extension: config.translations.invalid_file_extension,
                filesize: config.translations.filesize_validation
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

    $.validator.addMethod("filesize", function (value, element, param) {
        return this.optional(element) || (element.files.length > 0 && element.files[0].size <= param);
    }, function (param) {
        return config.translations.file_size_max.replace(':size', formatBytes(param));
    });

    $("main.main-content").on("click", ".btn_upload_document", $.proxy(t.addDocument));
    t.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
};

var HistoryPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("main.main-content").find("#history-tab");
    t.table = t.tab.find("#tblHistory");

    t.load = function() {
        t.dTbl = t.table.DataTable({
            destroy: true,
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
            aoColumnDefs: [
                {
                    // targets: 0,
                    // render: function (d) {
                    //     var a = [];
                    //     if (d.interact_type == "i2" && d.interact_module == "m1" && d.interact_id != null) {
                    //         a.push('<a href="' + baseURL + '/devices/change-info/' + d.interact_id + '" target="_blank"> <i class="fa fa-eye"></i> </a>')
                    //     }
                    //     if(d.accepted_via == 1) {
                    //         a.push('<a> <i class="fa fa-laptop"></i></a>')
                    //     } else if(d.accepted_via == 0) {
                    //         a.push('<a> <i class="fa fa-envelope"></i></a>')
                    //     }
                    //         if (config.client == "shyammetalics" && $.inArray((d.interact_type), ['i1', 'i4', 'i5']) !== -1) {
                    //         a.push("#"+d.id);
                    //     }
                    //     return a.join("");
                    // }
                },
                {
                    targets: 2,
                    render: function (d) {
                        var a = [];
                        if (d.adminuserid)
                            a.push('<a href="' + baseURL + '/user/info/' + d.adminuserid + '" target="_blank">' + d.adm_full_name + '</a>')
                        return a.join("");
                    }
                }, 
                {
                    targets: 4,
                    render: function (d) {
                        var a = [];
                        if (d.enduserid)
                            if(d.assigned_for == 2) {
                                a.push('<span class="checkout_place">' + d.full_name + '</span>');
                            } else if(d.assigned_for == 1) {
                                a.push('<a href="' + baseURL + '/user/info/' + d.enduserid + '" target="_blank">' + d.full_name + '</a>');
                            }
                        return a.join("");
                    }
                },
            ],
            order: [
                [1, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.device_history,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.device_id;
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
                    name: "id"
                },
                {
                    data: 'a.created_at_format',
                    name: "date"
                },
                {
                    data: 'a',
                    name: "admin"
                },
                {
                    data: 'a.action_type',
                    name: "action_type"
                },
                {
                    data: 'a',
                    name: "user_place"
                },
                {
                    data: 'a.project_name',
                    name: "project_name"
                },
                {
                    data: 'a.checkinout_reason',
                    name: "checkinout_reason"
                },
                {
                    data: 'a.note',
                    name: "notes"
                },
                {
                    orderable: false,
                    data: null,
                    render: function(data, type, row) {
                        var a = [];
                        if (data.a.interact_type == "i2" && data.a.interact_module == "m1" && data.a.interact_id != null) {
                            a.push(`<button type="button" class="btn btn-sm btn-light open-device-history user-list-action-btn" data-id="${data.a.interact_id}"><svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg></button>`);
                        }

                        if(data.a.accepted_via == 1) {
                            a.push(`<a class="user-list-action-btn">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.2 14.2222V4C3.2 2.89543 4.09543 2 5.2 2H18.8C19.9046 2 20.8 2.89543 20.8 4V14.2222M3.2 14.2222H20.8M3.2 14.2222L1.71969 19.4556C1.35863 20.7321 2.31762 22 3.64418 22H20.3558C21.6824 22 22.6414 20.7321 22.2803 19.4556L20.8 14.2222" stroke="#131927" stroke-width="1.5"/>
                                    <path d="M11 19L13 19" stroke="#131927" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg></a>`)
                        } else if(data.a.accepted_via == 0) {
                            a.push(`<a class="user-list-action-btn">
                                <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0.75 12.75V2.75C0.75 1.64543 1.64543 0.75 2.75 0.75H18.75C19.8546 0.75 20.75 1.64543 20.75 2.75V12.75C20.75 13.8546 19.8546 14.75 18.75 14.75H2.75C1.64543 14.75 0.75 13.8546 0.75 12.75Z" stroke="#131927" stroke-width="1.5"/>
                                </svg></a>`);
                        }

                        if (config.client == "shyammetalics" && $.inArray((data.a.interact_type), ['i1', 'i4', 'i5']) !== -1) {
                            a.push("#"+data.a.id);
                        }

                        let html = a.join("");

                        return `
                            <div class="user-list-actions justify-content-start">
                                ${html}
                            </div>  
                        `;
                    }
                }
            ],
        });
    };

    t.showDeviceHistory = function() {
        var deviceHistoryModal = {};
        var device_id = $(this).data("id");
        deviceHistoryModal.mdl = $("#device_history_modal");

        let history_url = `${config.url.devices_change_info}/${device_id}`;
        $.ajax({
            type: "GET",
            url: history_url,
            success: function(response) {
                $('#device_history_modal').html(response.html);
                $('#device_history_modal').modal('show');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error retrieving device history data:", textStatus, errorThrown);
            }
        });

    }

    t.table.on('click', '.open-device-history', t.showDeviceHistory);

};

var DevicePhase = function(config) {
    var t = this;
    t.config = config;
    t.content = $("main.main-content")


    t.infoTab = t.content.find("#device_info_tab");
    t.load = function() {
        t.refreshInfoTab();
    }

    t.refreshInfoTab = function() {
        var ajx = $.get(t.config.url.device_basic_info + "/" + t.config.device_id);
        ajx.done(function(d, status) {
            if (status == "success") {
                t.infoTab.html(d.html);
                // t.rightSideBox.find(".qrimgbox img").attr("src", d.qr ? d.qr : "");
                // t.rightSideBox.find(".devimgbox img").attr("src", d.device_img ? d.device_img : "");
                // t.rightSideBox.find(".compimgbox img").attr("src", d.company_logo ? d.company_logo : "");
                // if (typeof t.config.isCheckinCall != "undefined" && t.config.isCheckinCall) {
                //     t.infoTab.find(".dtActCheckIn").trigger("click");
                //     t.config.isCheckinCall = false;
                // }

                // development later
                // if(typeof d.map_loc != "undefined") {
                //     t.config.myApp.mapPhase.load(d.map_loc.location, d.map_loc.country);
                // }
            }
        });
    };

    t.getWarrantyDate = function () {
        t.warrantyDiv = t.content.find('#warranty-data-present');
        t.warrantyAbsentDiv = t.content.find('#warranty-data-absent');
        t.warrantyStartDate = t.content.find('#warranty-start-date');
        t.warrantyEndDate = t.content.find('#warranty-end-date');
        t.warrantyProgress = t.content.find('#warranty-progress');
        t.progress = t.content.find('.progress');

        $.get(config.url.getWarrantyDate + "/" + config.device_id, function (data) {

            if (typeof data !== "object" || !data.warranty) {
                return;
            }

            let warranty = data.warranty;

            if(warranty.start_date != "" && warranty.end_date != "") {
                t.warrantyAbsentDiv.addClass('d-none');
                t.warrantyDiv.removeClass('d-none');
                t.warrantyStartDate.text(warranty.start_date);
                t.warrantyEndDate.text(warranty.end_date);
                t.warrantyProgress.css("width", warranty.percent + "%").attr("aria-valuenow", warranty.percent);
                t.progress.attr("data-bs-title", warranty.percent + "%").tooltip("dispose").tooltip();
            } else {
                t.warrantyAbsentDiv.removeClass('d-none');
                t.warrantyDiv.addClass('d-none');
            }
        });
    };

    t.getWarrantyDate();

}