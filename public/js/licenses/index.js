var MyApp = function (config) {
    var t = this;

    t.config = config;
    t.httpCall = true;
    t.showDeletedLicenses = false;
    t.httpPostPath = "";
    t.content = $("section.content");
    t.table = t.content.find("#mytable");
    t.content = $("#main-user-list-wrapper");
    t.usertable = t.table;
    t.dTable = null;
    t.searchTimer = null;
    t.actionMenuHideTimer = null;
    t.showDeletedLicenses = false;
    t.chkout = {};
    t.chkout.mdl = $("section.content").find("#license-checkout-mdl");
    t.chkout.mdl.btnSubmit = t.chkout.mdl.find("#btnSubmit");
    t.chkout.mdl.frm = t.chkout.mdl.find("#license-checkout-mdl-frm");
    t.chkout.mdl.frmEl = {};
    t.chkout.mdl.frmEl.id = t.chkout.mdl.frm.find("#id");
    t.chkout.mdl.lblLicenseName = t.chkout.mdl.find("#lblLicenseName");
    t.chkout.mdl.frmEl.expected_checkin = t.chkout.mdl.frm.find("#expected_checkin");
    t.chkout.mdl.lblLicenseSerial = t.chkout.mdl.find("#lblLicenseSerial");
    t.chkout.mdl.frmEl.device_id = t.chkout.mdl.frm.find("#device_id");
    t.chkout.mdl.frmEl.assigned_for = t.chkout.mdl.frm.find("#assigned_for");
    t.chkout.mdl.frmEl.assigned_to = t.chkout.mdl.frm.find("#assigned_to");

    t.btn = {
        openFilter: t.content.find(".btn-open-filter"),
        deletedLicenses: $('.btn-deleted-license'),
        reload: t.content.find(".btn-reload-list"),
        add: t.content.find(".btn-add-license"),
        edit: t.content.find(".dtActEdit"),
        export: t.content.find(".btn-license-export"),
        export_pdf: t.content.find(".btn-export-license-pdf"),
        search: t.content.find(".btn-searchbox"),
        showDeleted: t.content.find(".btn-show-license"),
        showNonDeleted: t.content.find(".non-deleted-icon"),
    };
    t.btn.showNonDeleted.addClass('d-none');
    t.filters = { wrapper: $("#licensesFilterModal"), };
    t.filters.departments = t.filters.wrapper.find("#filter_by_department"),
        t.filters.manufacturers = t.filters.wrapper.find("#filter_by_manufacture"),
        t.filters.locations = t.filters.wrapper.find("#filter_by_location"),
        t.filters.internal_place = t.filters.wrapper.find("#filter_by_internal_place"),
        t.filters.categories = t.filters.wrapper.find("#filter_by_category"),
        t.filters.suppliers = t.filters.wrapper.find("#filter_by_supplier"),
        t.filters.based_on = t.filters.wrapper.find("#filter_by_date"),
        t.filters.date_range = t.filters.wrapper.find("#daterange"),
        t.filters.purchase_reference = t.filters.wrapper.find("#filter_by_purchase_reference");
    t.btn.filter = t.filters.wrapper.find("#filter");
    t.btn.clear = t.filters.wrapper.find('#clear'),

        t.search = function (e) {

            if (e) {
                e.preventDefault();
            }

            t.cache_filter_values();

            let value = $(".license-list-search").val().trim();

            if (t.dTable) {
                t.dTable.search(value).draw();
            }

            // Filter modal close karo
            var modal = bootstrap.Modal.getInstance(document.getElementById("licensesFilterModal"));
            if (modal) {
                t.config.other_filters = t.filters.fun.getFilters();
                modal.hide();
            }
        };

    t.btn.filter.on("click", $.proxy(t.search, t));
    t.filters.fun = {

        reload_manufacturers: function () {
            t.filters.manufacturers.select2(
                $.extend({}, select2Opts, {
                    dropdownParent: t.filters.manufacturers.parent(),
                    ajax: {
                        url: t.config.url.getManufacturerByAjax,
                        dataType: "json",
                        data: function (p) {
                            return {
                                search: p.term,
                                page: p.page || 1,
                            };
                        },
                        delay: 300,
                    },
                    allowClear: true,
                    placeholder: config.translations.placeholder_no_filter,
                }),
            );
        },

        reload_departments: function () {
            t.filters.departments.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.departments.parent(),
                ajax: {
                    url: t.config.url.getAssetDepartments,
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
                // minimumInputLength: 1,
                placeholder: config.translations.placeholder_no_filter,
            }));

        },

        reload_locations: function () {
            t.filters.locations.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.locations.parent(),
                ajax: {
                    url: t.config.getLocationByAjax,
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
                // minimumInputLength: 1,
                placeholder: config.translations.placeholder_no_filter,
            }));
        },

        reload_suppliers: function () {
            t.filters.suppliers.select2(
                $.extend({}, select2Opts, {
                    dropdownParent: t.filters.suppliers.parent(),
                    ajax: {
                        url: t.config.getSupplierByAjax,
                        dataType: "json",
                        data: function (p) {
                            return {
                                search: p.term,
                                page: p.page || 1,
                            };
                        },
                        delay: 300,
                    },
                    allowClear: true,
                    //minimumInputLength: 1,
                    placeholder: config.translations.placeholder_no_filter,
                    templateSelection: function (data, container) {
                        if (container) {
                            $(container).attr("title", data.text);
                        }
                        return data.text.length > 50
                            ? data.text.substring(0, 55) + "..."
                            : data.text;
                    },
                }),
            );
        },

        reload_categories: function () {
            t.filters.categories.select2(
                $.extend({}, select2Opts, {
                    dropdownParent: t.filters.categories.parent(),
                    width: "100%",
                    ajax: {
                        url: t.config.url.getCategoryByAjax,
                        dataType: "json",
                        data: function (p) {
                            return {
                                search: p.term,
                                page: p.page || 1,
                            };
                        },
                        delay: 300,
                    },
                    allowClear: true,
                    placeholder: config.translations.placeholder_no_filter || "Select Category",
                    language: {
                        noResults: function () {
                            return "No Data Found";
                        }
                    }
                })
            );
            t.filters.categories.trigger("change");
        },

        reload_internal_place: function () {
            t.filters.internal_place.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.internal_place.parent(),
                ajax: {
                    url: t.config.ajaxGetInternalPlace,
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
                placeholder: config.translations.placeholder_no_filter,
                templateSelection: function (data, container) {
                    $(container).attr('title', data.text);
                    return data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
                }
            }));
        },

        purchase_reference: function () {
            t.filters.purchase_reference.select2($.extend({}, select2Opts, {
                dropdownParent: t.filters.purchase_reference.parent(),
                ajax: {
                    url: t.config.url.getInvoiceByAjax,
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
                placeholder: config.translations.placeholder_no_filter,
                templateSelection: function (s, container) {
                    if (typeof s.loading != "undefined" && s.loading) {
                        return $("<div>" + s.text + "</div>");
                    }
                    $(s.element).attr({ 'data-invoice_date': s.invoice_date, 'data-name': s.supplier_name, 'data-id': s.supplier_id, 'data-order-number': s.order_number, 'data-po-number': s.po_number, 'data-purchase-cost': s.bill_amount, 'data-currency': s.currency });
                    return s.text;
                }
            }));
        },

        updateFilterCount: function () {
            let count = 0;
            const filters = t.filters.fun.getFilters();


            $.each(filters, function (key, value) {
                if (Array.isArray(value)) {
                    const hasValue = value.some(item => {
                        if (Array.isArray(item)) {
                            return item.length > 0; // Check if inner array has elements
                        }
                        return item !== null && item !== undefined && item !== "";
                    });

                    if (hasValue) {
                        count++;
                    }
                } else if (value !== null && value !== undefined && value !== "") {
                    count++;
                }
            });
            let badge = $(".filter-count-badge");
            if (count > 0) {
                badge.removeClass("d-none");
                badge.text(count);
            } else {
                badge.addClass("d-none");
                badge.text("0");
            }
        },
        getFilters: function () {
            var basedOn = t.filters.based_on.val() || "";
            var dateRange = basedOn ? (t.filters.daterange.val() || "") : "";
            return {
                location: t.filters.locations.val() ? t.filters.locations.val() : [],
                category: t.filters.categories.val() ? t.filters.categories.val() : [],
                purchase_reference: t.filters.purchase_reference.val() ? t.filters.purchase_reference.val() : [],
                based_on: basedOn,
                date_range: dateRange,
                // based_on: t.filters.based_on.val() || "",
                // date_range: t.filters.daterange.val() || "",
                department: t.filters.departments.val() ? t.filters.departments.val() : [],
                internal_place: t.filters.internal_place.val() ? t.filters.internal_place.val() : [],
                manufacturer: t.filters.manufacturers.val() ? t.filters.manufacturers.val() : [],
                supplier: t.filters.suppliers.val() ? t.filters.suppliers.val() : [],
            };
        },
        bindFilterCountEvents: function () {
            $("#licensesFilterModal")
                .find("select, input")
                .off(".filterCount")
                .on("change.filterCount keyup.filterCount", function () {
                    t.filters.fun.updateFilterCount();
                })
        }
    };


    ((t.filters.date_range = t.filters.wrapper.find("#reportrange")),
        (t.filters.daterange = t.filters.wrapper.find("#daterange")));

    t.cache_filter_values = function () {
        var v = $.trim($(".license-list-search").val());
        t.config.search = v;
        t.config.other_filters = {};
        t.config.dashboard_filters = {};
        if (t.filters.manufacturers.val() && t.filters.manufacturers.val() != 'null')
            t.config.other_filters.manufacturer = t.filters.manufacturers.val();
        if (t.filters.categories.val() && t.filters.categories.val() != 'null')
            t.config.other_filters.category = t.filters.categories.val();
        if (t.filters.suppliers.val() && t.filters.suppliers.val() != 'null')
            t.config.other_filters.supplier = t.filters.suppliers.val();
        if (t.filters.locations.val() && t.filters.locations.val() != 'null')
            t.config.other_filters.location = t.filters.locations.val();
        if (t.filters.purchase_reference.val() && t.filters.purchase_reference.val() != 'null')
            t.config.other_filters.purchase_reference = t.filters.purchase_reference.val();
        if (t.filters.internal_place.val() && t.filters.internal_place.val() != 'null')
            t.config.other_filters.internal_place = t.filters.internal_place.val();
        if (t.filters.departments.val() && t.filters.departments.val() != 'null')
            t.config.other_filters.department = t.filters.departments.val();
        if (t.filters.date_range && t.filters.date_range.val() != 'null')
            t.config.other_filters.date_range = t.filters.date_range.val();


        // FIX 2: Add based_on (date type) value
        var basedOnVal = t.filters.based_on.val();
        if (basedOnVal && basedOnVal != 'null' && basedOnVal.trim() !== '') {
            t.config.other_filters.based_on = basedOnVal;
            var dateRangeVal = t.filters.daterange.val();
            if (dateRangeVal && dateRangeVal != 'null' && dateRangeVal.trim() !== '') {
                t.config.other_filters.date_range = dateRangeVal;
            }
        }

        var jobj = {
            "search": t.config.search,
            "other_filters": t.config.other_filters,
            'dashboard_filters': t.config.dashboard_filters,
            'showDeletedLicenses': t.showDeletedLicenses,
            "over_all_purchase_filter": t.config.purchaseFilter
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        filterCount(t.config.other_filters, t.filters.based_on.val(), false);
    };

    t.addEdit = new AddEdit(config, t);
    t.mdlNotify = null;
    t.frmNotify = null;
    t.frmNotifyValidator = null;
    t.btnNotify = {};
    t.selectedUserIds = [];
    t.isBulkAddMode = false;
    t.mergeObj = null;
    t.print = null;
    t.userMdl = { initialized: false, selectsInitialized: false };
    t.listUi = {
        pageLength: $(),
        searchInput: $(),
        viewButtons: $(),
        listPanel: $(),
        cardPanel: $(),
        activeView: "list",
    };

    t.tblHelpers = {
        complience: function (d) {
            var a = [];
            var per = d.licensecountnumPercent >= 100 ? 100 : d.licensecountnumPercent;
            if (d.added_via == 2) {
                if (d.network_count > d.tot_seats) {
                    a.push('<div class="ddd"></div><div class="outer"><div class="inner" style="width:100%"></div></div>');
                } else {
                    a.push('<div>' + d.licensecountnum + '</div><div class="outer"><div class="network_full" style="width:' + per + '%"></div></div>');
                }
            } else {
                a.push('<div>' + d.licensecountnum + '</div><div class="outer"><div class="network_full" style="width:' + per + '%"></div></div>');
            }
            return a.join(" ");
        },
        actions: function () {
            return function (d) {
                let quick = [];
                let menu = [];
                if (t.showDeletedLicenses) {
                    if (jQuery.inArray("LicenseRestore", t.config.permissions) !== -1) {
                        quick.push(`
                        <button class="user-list-action-btn dtActRestore"
                            data-id="${d.id}"
                            title="${t.config.translations.restore_licenses}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6a6 6 0 01-6 6 6 6 0 01-5.65-4H4.26A8 8 0 0012 21a8 8 0 000-16z" fill="currentColor"/>
                            </svg>
                        </button>
                    `);
                    }
                    return `<div class="user-list-actions">${quick.join("")}</div>`;
                }

                if (jQuery.inArray("LicenseEdit", t.config.permissions) !== -1) {
                    quick.push(`
                        <button class="user-list-action-btn dtActEdit"
                        data-id="${d.id}"
                        title="${t.config.translations.edit_licenses}">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
                        </button>
                `);
                }

                if (jQuery.inArray("LicenseDelete", t.config.permissions) !== -1) {
                    quick.push(`
                    <button class="user-list-action-btn dtActDel is-delete"
                    data-id="${d.id}"
                    title="${t.config.translations.delete_licenses}">
                    <svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"></path></svg>
                    </button>
                `);
                }

                if (jQuery.inArray("LicenseView", t.config.permissions) !== -1) {
                    menu.push(`
                    <li>
                    <a class="dropdown-item dtActView" href="#" data-id="${d.id}">
                        <svg viewBox="0 0 19 13" fill="none"><path d="M18.6961 5.99688C18.6687 5.93516 18.007 4.46719 16.5359 2.99609C14.5758 1.03594 12.1 0 9.37499 0C6.64999 0 4.17421 1.03594 2.21405 2.99609C0.742961 4.46719 0.0781175 5.9375 0.0538988 5.99688C0.0183622 6.07681 0 6.16331 0 6.25078C0 6.33826 0.0183622 6.42476 0.0538988 6.50469C0.0812425 6.56641 0.742961 8.03359 2.21405 9.50469C4.17421 11.4641 6.64999 12.5 9.37499 12.5C12.1 12.5 14.5758 11.4641 16.5359 9.50469C18.007 8.03359 18.6687 6.56641 18.6961 6.50469C18.7316 6.42476 18.75 6.33826 18.75 6.25078C18.75 6.16331 18.7316 6.07681 18.6961 5.99688ZM9.37499 11.25C6.9703 11.25 4.86952 10.3758 3.13046 8.65234C2.4169 7.94273 1.80983 7.13356 1.32812 6.25C1.8097 5.36636 2.41679 4.55717 3.13046 3.84766C4.86952 2.12422 6.9703 1.25 9.37499 1.25C11.7797 1.25 13.8805 2.12422 15.6195 3.84766C16.3345 4.557 16.9429 5.36619 17.4258 6.25C16.8625 7.30156 14.4086 11.25 9.37499 11.25ZM9.37499 2.5C8.63331 2.5 7.90829 2.71993 7.2916 3.13199C6.67492 3.54404 6.19427 4.12971 5.91044 4.81494C5.62662 5.50016 5.55235 6.25416 5.69705 6.98159C5.84174 7.70902 6.19889 8.3772 6.72334 8.90165C7.24779 9.4261 7.91597 9.78325 8.6434 9.92795C9.37083 10.0726 10.1248 9.99838 10.8101 9.71455C11.4953 9.43072 12.0809 8.95007 12.493 8.33339C12.9051 7.7167 13.125 6.99168 13.125 6.25C13.124 5.25576 12.7285 4.30253 12.0255 3.59949C11.3225 2.89645 10.3692 2.50103 9.37499 2.5ZM9.37499 8.75C8.88054 8.75 8.39719 8.60338 7.98607 8.32867C7.57494 8.05397 7.25451 7.66352 7.06529 7.20671C6.87607 6.74989 6.82657 6.24723 6.92303 5.76227C7.01949 5.27732 7.25759 4.83186 7.60722 4.48223C7.95686 4.1326 8.40231 3.8945 8.88727 3.79804C9.37222 3.70157 9.87488 3.75108 10.3317 3.9403C10.7885 4.12952 11.179 4.44995 11.4537 4.86107C11.7284 5.2722 11.875 5.75555 11.875 6.25C11.875 6.91304 11.6116 7.54893 11.1428 8.01777C10.6739 8.48661 10.038 8.75 9.37499 8.75Z" fill="currentColor"></path></svg>
                        View License
                    </a>
                    </li>
                `);
                }

                if (jQuery.inArray("LicenseAdd", t.config.permissions) !== -1) {
                    menu.push(`
                    <li>
                    <a class="dropdown-item dtActClone dtActbtn" href="#" data-id="${d.id}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M16 1H4v12h2V3h10V1zm4 4H8v16h12V5z" fill="currentColor"/>
                        </svg>
                        Clone License
                    </a>
                    </li>
                `);
                }

                if (
                    jQuery.inArray("LicenseCheckout", t.config.permissions) !== -1 && d.available_seats > 0
                ) {
                    menu.push(`
                    <li>
                    <a class="dropdown-item dtActCheckOut" href="#"
                        data-id="${d.id}"
                        data-accessory-name="${d.name}"
                        data-category-name="${d.cat_name}"
                        data-company_id="${d.company_id}"
                        data-serial_num="${d.serial_value}">

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M10 17V14H3V10H10V7L15 12L10 17ZM21 19H11V21H21C22.1 21 23 20.1 23 19V5C23 3.9 22.1 3 21 3H11V5H21V19Z" fill="currentColor"></path>
                    </svg>
                        Checkout License
                    </a>
                    </li>
                `);
                }

                menu.push(`
                            <li>
                                <a class="dropdown-item d-none dtActNote" href="#" data-id="${d.id}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 2h16v20l-4-4H4V2z" fill="currentColor"/>
                                </svg>
                                View Note
                                </a>
                            </li>
                        `);

                if (!quick.length && !menu.length) {
                    return `<span class="user-list-empty">-</span>`;
                }

                let dropdownId = "acc-actions-" + d.id;
                return `
                        <div class="user-list-actions d-flex align-items-center gap-2">

                        ${quick.join("")}

                        ${menu.length
                        ? `
                        <div class="">
                            <button type="button"
                            class="action-link user-list-menu-toggle"
                            id="${dropdownId}">

                            <svg width="5" height="14" viewBox="0 0 5 20" fill="none"><path d="M5 10C5 10.5128 4.85338 11.014 4.57867 11.4404C4.30397 11.8667 3.91352 12.199 3.45671 12.3952C2.99989 12.5915 2.49723 12.6428 2.01227 12.5428C1.52732 12.4427 1.08187 12.1958 0.732234 11.8332C0.382603 11.4707 0.144501 11.0087 0.0480379 10.5058C-0.0484251 10.0029 0.00108321 9.48159 0.190302 9.00786C0.379521 8.53412 0.699952 8.12922 1.11108 7.84434C1.5222 7.55946 2.00555 7.40741 2.5 7.40741C3.16304 7.40741 3.79893 7.68056 4.26777 8.16676C4.73661 8.65297 5 9.3124 5 10ZM2.5 5.18519C2.99445 5.18519 3.4778 5.03313 3.88893 4.74826C4.30005 4.46338 4.62048 4.05847 4.8097 3.58474C4.99892 3.111 5.04843 2.58972 4.95196 2.0868C4.8555 1.58389 4.6174 1.12193 4.26777 0.759354C3.91814 0.396773 3.47268 0.149853 2.98773 0.0498171C2.50277 -0.0502186 2.00011 0.00112333 1.54329 0.19735C1.08648 0.393578 0.69603 0.725877 0.421327 1.15223C0.146624 1.57858 1.34665e-06 2.07983 1.34665e-06 2.59259C1.34665e-06 3.28019 0.263393 3.93963 0.732234 4.42583C1.20107 4.91204 1.83696 5.18519 2.5 5.18519ZM2.5 14.8148C2.00555 14.8148 1.5222 14.9669 1.11108 15.2517C0.699952 15.5366 0.379521 15.9415 0.190302 16.4153C0.00108321 16.889 -0.0484251 17.4103 0.0480379 17.9132C0.144501 18.4161 0.382603 18.8781 0.732234 19.2406C1.08187 19.6032 1.52732 19.8501 2.01227 19.9502C2.49723 20.0502 2.99989 19.9989 3.45671 19.8026C3.91352 19.6064 4.30397 19.2741 4.57867 18.8478C4.85338 18.4214 5 17.9202 5 17.4074C5 16.7198 4.73661 16.0604 4.26777 15.5742C3.79893 15.088 3.16304 14.8148 2.5 14.8148Z" fill="currentColor"></path></svg>

                            </button>

                            <ul class="dropdown-menu action-dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                            aria-labelledby="${dropdownId}">
                            ${menu.join("")}
                            </ul>
                        </div>
                        `
                        : ""
                    }

                        </div>
                     `;
            };
        },


        location_name: function (d) {
            var a = [];

            if (d.loc_name) {
                a.push(
                    '<div class="deviceLocationText" data-bs-toggle="tooltip" data-bs-title="' +
                    t.config.translations.location +
                    '">' +
                    d.loc_name +
                    '</div>'
                );
            }

            if (d.place_name) {
                a.push(
                    '<a href="/internal-places/" target="_blank" class="text-decoration-none">' +
                    '<div class="deviceModelText" data-bs-toggle="tooltip" data-bs-title="' +
                    t.config.translations.internal_place +
                    '">' +
                    d.place_name +
                    '</div>' +
                    '</a>'
                );
            }

            if (d.department) {
                a.push(
                    '<div class="deviceDepartmentText" data-bs-toggle="tooltip" data-bs-title="' +
                    t.config.translations.department +
                    '">' +
                    d.department +
                    '</div>'
                );
            }


            return a.join(" ");
        },

        license: function (d) {
            var a = [];
            if (d.batch_no) {
                a.push(
                    '<a href="' + t.config.url.deatil + '/' + d.id + '" target="_blank" class="' +
                    (t.showDeletedLicenses === true ? 'textCategory' : 'serial_num') +
                    '" data-bs-toggle="tooltip" data-bs-title="' +
                    t.config.translations.batch_number + '">' +
                    d.batch_no +
                    '</a>'
                );
            }
            if (d.unique_tag) {
                a.push('<div data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.unique_tag + '">' + d.unique_tag + '</div>');
            }
            a.push('<div class="deviceModelText" data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.license_name + '">' + d.name + '</div>');
            if (d.agreement_no) {
                a.push('<div data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.agreement_number + '">' + d.agreement_no + '</div>');
            }
            a.push('<div class="deviceCompanyText" data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.company_name + '">' + d.cmp_name + '</div>');
            if (d.cat_name) {
                a.push('<div class="deviceCategoryText" data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.category + '">' + d.cat_name + '</div>');
            }
            if (d.serial_value) {
                a.push('<div data-bs-toggle="tooltip" data-bs-title="' + t.config.translations.serial_number + '">' + d.serial_value + '</div>');
            }
            return a.join(" ");
        }

    };

    t.init = function () {
        setTimeout(function () {
            t.initDataTable();
        }, 50);
        t.bindEvents();
    };


    t.bindEvents = function () {
        t.btn.reload.on("click", $.proxy(t.handleReloadClick, t));
        t.btn.search.on("click", $.proxy(t.tableSearch, t));
        t.content.on("click", ".btn-show-license, .non-deleted-icon", $.proxy(t.toggleDeletedLicenses, t));
    };

    t.handleReloadClick = function (e) {
        e.preventDefault();
        t.reload();
    };

    // t.reload = function () {    
    //     $(".license-list-search").val("");
    //     t.dTable.search("").draw();
    //     if (t.dTable) {
    //         t.dTable.ajax.reload(null, true);
    //     }

    // };

    t.reload = function () {

        $(".license-list-search").val("");

        if (t.dTable) {
            t.dTable.search("");
            t.dTable.ajax.reload(null, true);
        }
    };

    t.toggleDeletedLicenses = function (e) {
        e.preventDefault();

        t.showDeletedLicenses = !t.showDeletedLicenses;

        // Toggle the flag
        // t.showDeletedLicenses = !t.showDeletedLicenses;

        // Update button visibility
        if (t.showDeletedLicenses) {
            // Show deleted licenses - hide the eye icon, show the crossed eye icon
            t.btn.showDeleted.addClass('d-none');
            t.btn.showNonDeleted.removeClass('d-none');

            // Update tooltips
            t.btn.showNonDeleted.attr('data-bs-original-title', 'Show Non-Deleted Licenses');
            t.btn.showNonDeleted.attr('title', 'Show Non-Deleted Licenses');

            // Add active class for visual feedback
            t.btn.showNonDeleted.addClass('active');
            t.btn.showDeleted.removeClass('active');
        } else {
            // Show non-deleted licenses - show the eye icon, hide the crossed eye icon
            t.btn.showDeleted.removeClass('d-none');
            t.btn.showNonDeleted.addClass('d-none');

            // Update tooltips
            t.btn.showDeleted.attr('data-bs-original-title', 'Show Deleted Licenses');
            t.btn.showDeleted.attr('title', 'Show Deleted Licenses');

            // Remove active class
            t.btn.showNonDeleted.removeClass('active');
            t.btn.showDeleted.addClass('active');
        }

        // Reinitialize tooltips for the buttons
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            [t.btn.showDeleted[0], t.btn.showNonDeleted[0]].forEach(function (el) {
                if (el) {
                    var tooltip = bootstrap.Tooltip.getInstance(el);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                    new bootstrap.Tooltip(el);
                }
            });
        }

        // Update filter count with deleted flag
        if (typeof filterCount === 'function') {
            filterCount(t.config.other_filters, t.filters.based_on.val(), false, t.showDeletedLicenses);
        }

        // Clear DataTable search to avoid confusion
        $(".license-list-search").val("");
        t.config.search = "";

        // Reload the DataTable with the updated flag
        if (t.dTable) {
            t.dTable.search("").draw();
            t.dTable.ajax.reload(null, true);
        }
    };

    t.getSelectedPageLength = function () {
        var select = t.listUi && t.listUi.pageLength
            ? t.listUi.pageLength
            : t.content.find(".user-list-page-length");
        var length = parseInt(select.val(), 10);
        return length > 0 ? length : 10;
    };

    t.renderRowCheckbox = function (data, type) {
        if (type !== "display") return data;
        return [
            '<label class="d-inline-flex align-items-center justify-content-center">',
            '<input type="checkbox" class="row-checkbox form-check-input" value="',
            '">',
            "</label>",
        ].join("");
    };

    t.initDataTable = function () {
        var defaultPageLength = t.getSelectedPageLength();

        $.fn.dataTable.ext.pager.numbers_length = 5;

        t.dTable = t.usertable.DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            stateSave: false,
            searchDelay: 600,
            pageLength: defaultPageLength,
            lengthChange: false,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            ordering: true,
            order: [[9, "desc"]],
            pagingType: "simple_numbers",

            ajax: {
                url: t.config.url.licenses,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                    d.filters = t.config.other_filters;
                    d.showDeletedLicenses = t.showDeletedLicenses;

                },
                dataSrc: function (json) {
                    return json.data;
                },
                error: function (xhr) {
                    console.error("DataTable Error:", xhr.responseText);
                },
            },
            fixedColumns: {
                leftColumns: 1,
                rightColumns: 1,
            },
            columns: [
                {
                    data: "a.id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type) {
                        return t.renderRowCheckbox(data, type);
                    }
                },

                {
                    data: null,
                    render: function (data, type, row) {
                        return t.tblHelpers.license(row.a);
                    }

                },
                {
                    data: "a.seats",
                    defaultContent: "-"
                },
                {
                    data: "a.available_seats",
                    defaultContent: "-"
                },
                {
                    data: "a.network_count",
                    defaultContent: "0"
                },
                {
                    data: "a.expire_date_on",
                    defaultContent: "-"
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return (row.a.purchase_date_on || "-") +
                            "<br>" +
                            (row.a.purchase_cost_format || "0.00");
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return t.tblHelpers.location_name(row.a);
                    }
                },
                {
                    data: "a.order_number",
                    defaultContent: "-"
                },
                {
                    data: "a.last_updated_at",
                    defaultContent: "-"
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return t.tblHelpers.complience(row.a);
                    }
                },
                {
                    data: "a",
                    orderable: false,
                    searchable: false,
                    render: t.tblHelpers.actions(),
                },
            ],
            drawCallback: function () {
                $('[data-bs-toggle="tooltip"]').each(function () {
                    bootstrap.Tooltip.getOrCreateInstance(this);
                });
            },
            dom: '<"row align-items-center mb-2"<"col-md-6"l><"col-md-6 text-end"r>>t<"row dt-footer align-items-center"<"col-md-6"i><"col-md-6 d-flex justify-content-end"p>>',
        });

        t.btn.export.on("click", $.proxy(t.export));
        t.btn.export_pdf.on("click", $.proxy(t.exportPDF));
        t.btn.add.on('click', $.proxy(t.showAddPopup, t));
        t.table.on("click", ".dtActEdit", $.proxy(t.showEditPopup));
        t.table.on("click", ".dtActClone", $.proxy(t.showClonePopup));
        t.table.on("click", ".dtActRestore", $.proxy(t.restoreAccessory));
        t.table.on("click", ".dtActNote", $.proxy(t.noteAccessory));
        t.table.on("click", ".dtActCheckOut", $.proxy(t.checkoutLicense));
        t.table.on("click", ".dtActDel", function (e) {
            t.deleteLicense.call(t, e);
        });

    };
    t.chkout.mdl.btnSubmit.on("click", $.proxy(t.handleCheckoutSubmit));
    t.chkout.mdl.btnSubmit.on("click", function (e) {
        t.handleCheckoutSubmit.call(this, e);
    });

    t.handleCheckoutSubmit = function (e) {

        e.preventDefault();
        t.chkout.mdl.btnSubmit.prop("disabled", true);
        if (t.chkout.frmValidator.form() == false) {
            t.chkout.mdl.btnSubmit.prop("disabled", false);
            return false;
        }
        if (t.httpCall != true) {
            t.chkout.mdl.btnSubmit.prop("disabled", false);
            return false;
        }
        t.httpCall = false;
        var formData = new FormData(t.chkout.mdl.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData,
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert("center", "success", data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>'});
                    t.chkout.mdl.modal("hide");
                    t.dTable.ajax.reload();
                } else {
                    sweetAlert("center", "error", data);
                    // vex.dialog.alert({unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'});
                }
            }
        });
        http.fail(function () {
            // alert(config.translations.something_went_wrong);
            var data = {
                msg: config.translations.something_went_wrong,
            };
            sweetAlert("center", "error", data);
        });
        http.always(function () {
            t.httpCall = true;
            t.chkout.mdl.btnSubmit.prop("disabled", false);
        });
    };

    t.content.on("keyup", ".license-list-search", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            t.search();
        }
    });

    t.checkoutLicense = function (e) {
        e.preventDefault();
        t.chkout.resetFrm();
        var $this = $(this);
        var licenseId = $this.attr("data-id");
        t.httpPostPath = t.config.url.checkout + '/' + licenseId;
        t.chkout.mdl.lblLicenseName.html($(this).attr("data-accessory-name"));
        t.chkout.mdl.lblLicenseSerial.html($(this).attr("data-serial_num"));
        t.chkout.mdl.frmEl.id.val($(this).attr("data-id"));
        t.chkout.mdl.frmEl.device_id.empty();
        t.chkout.mdl.frmEl.assigned_for.val("1").trigger("change");
        t.chkout.mdl.modal("show");
    };
    t.chkout.switchCheckTarget = function () {
        if (t.chkout.mdl.frmEl.assigned_for.val() == "1") {
            t.chkout.mdl.frmEl.device_id.closest(".cover").show();
            t.chkout.mdl.frmEl.assigned_to.closest(".cover").hide();
            t.chkout.mdl.frmEl.device_id.rules("add", { required: true });
            t.chkout.mdl.frmEl.assigned_to.rules("remove", "required");
        } else {
            t.chkout.mdl.frmEl.device_id.closest(".cover").hide();
            t.chkout.mdl.frmEl.assigned_to.closest(".cover").show();
            t.chkout.mdl.frmEl.device_id.rules("remove", "required");
            t.chkout.mdl.frmEl.assigned_to.rules("add", { required: true });
        }
    };

    t.chkout.frmValidator = t.chkout.mdl.frm.validate({
        onsubmit: false,
        rules: {
            assigned_for: {
                required: false,
                str_name: true,
                clean_text_only: true,
            },
            assigned_to: {
                str_name: true,
            },
            device_id: {
                str_name: true,
                clean_text_only: true,
            },
            expected_checkin: {
                remarks: true,
                clean_text_only: true,
            },
            note: {
                required: function () {
                    return config.client === "knightfrank";
                },
                remarks: true,
                clean_text_only: true,
            },
        },
        errorPlacement: function (error, element) {
            if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group").parent());
            } else {
                error.insertAfter(element);
            }
        },
        invalidHandler: function (event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                });
            }
        },
    });

    t.chkout.resetFrm = function () {
        t.chkout.mdl.frm.trigger("reset");
        t.chkout.frmValidator.resetForm();
        t.chkout.mdl.frmEl.assigned_to.empty().trigger("change");
        t.chkout.mdl.frmEl.assigned_for.val("").trigger("change");
        t.chkout.mdl.frmEl.device_id.empty().trigger("change");
        t.chkout.mdl.frmEl.expected_checkin.val("");
        t.chkout.mdl.lblLicenseName.text("");
        t.chkout.mdl.lblLicenseSerial.text("");
    };

    t.chkout.mdl.frmEl.expected_checkin.datepicker({
        autoclose: true,
        format: "dd/mm/yyyy",
        container: $("#license-checkout-mdl"),
    });

    t.chkout.mdl.frmEl.assigned_to.select2(
        $.extend({}, select2Opts, {
            width: "100%",
            dropdownParent: t.chkout.mdl.frmEl.assigned_to.parent(),
            ajax: {
                url: t.config.getUserByAjax,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                    };
                },
                delay: 300,
            },
            allowClear: true,
            //minimumInputLength: 1,
            placeholder: config.translations.Select_the_User,

            templateResult: function (s) {
                if (typeof s.loading != "undefined" && s.loading) {
                    return $("<div>" + s.text + "</div>");
                }

                var email = s.email == null ? "" : s.email;
                var a = "";

                // License-specific template
                a += "<div class='row'>";
                a += "<div class='col-9'>";

                // Show user display name
                if (s.displayName != null && s.displayName != "") {
                    a += "<div class='so-t'><i class=\"bi bi-person\"></i>" + s.displayName;
                } else {
                    a += "<div class='so-t'><i class=\"bi bi-person\"></i>" + s.first_name + " " + s.last_name;
                }
                a += "<span class='active-user'></span>";
                a += "</div>";

                // Show email if exists
                if (s.email != null && s.email != "") {
                    a += "<div class='so-t'><i class=\"bi bi-envelope\"></i>" + s.email + "</div>";
                }

                // Show employee number if exists
                if (s.employee_num != null && s.employee_num != "") {
                    a += "<div class='so-t'><i class=\"bi bi-credit-card\"></i>" + s.employee_num + "</div>";
                }

                // Show license-related info if applicable
                if (s.license_count != null && s.license_count != "") {
                    a += "<div class='so-t'><i class=\"fa fa-copyright\"></i> Licenses: " + s.license_count + "</div>";
                }

                a += "</div>";
                a += "<div class='col-3'>";
                a += "<div><img class='img-u' src= '" + s.img_path + "'/></div>";
                a += "</div>";
                a += "</div>";

                return $("<div>" + a + "</div>");
            },
            templateSelection: function (data, container) {
                if (container) {
                    $(container).attr("title", data.text);
                }
                // Increase to 50 characters for license module
                return data.text.length > 50
                    ? data.text.substring(0, 50) + "..."
                    : data.text;
            },

        }),
    );

    t.chkout.mdl.frmEl.assigned_for.select2(
        $.extend({}, select2Opts, {
            dropdownParent: t.chkout.mdl.frmEl.assigned_for.parent(),
            data: t.config.assignedForOptions,
        }),
    );

    t.chkout.mdl.frmEl.assigned_for.on(
        "change",
        $.proxy(t.chkout.switchCheckTarget),
    );
    
    t.chkout.mdl.frmEl.device_id.select2(
        $.extend({}, select2Opts, {
            dropdownParent: t.chkout.mdl.frmEl.device_id.parent(),
            ajax: {
                url: t.config.getDeviceForCheckoutDropDown,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1,
                    };
                },
                delay: 300,
            },
            allowClear: true,
            //minimumInputLength: 1,
            placeholder: config.translations.select_the_Device,
            // templateResult: function (s) {
            //     if (typeof s.loading != "undefined" && s.loading) {
            //     return $("<div>" + s.text + "</div>");
            //     }
            //     a =
            //     "<div class='so-t'><i class=\"fa fa-tag\"></i> " +
            //     s.asset_tag +
            //     "</div>";
            //     if (s.asset_name != null) {
            //     a +=
            //         "<div class='so-t'><i class=\"fa fa-laptop\"></i> " +
            //         s.asset_name +
            //         "</div>";
            //     }
            //     a +=
            //     "<div class='so-m'><i class=\"fa fa-tablet\"></i> " +
            //     s.name +
            //     " " +
            //     s.modelno +
            //     "</div>";
            //     a +=
            //     "<div class='so-t'><i class=\"fa fa-barcode\"></i> " +
            //     s.serial +
            //     "</div>";
            //     return $("<div>" + a + "</div>");
            // },
            // templateSelection: function (data, container) {
            //     if (container) {
            //     $(container).attr("title", data.text);
            //     }
            //     return data.text.length > 50
            //     ? data.text.substring(0, 55) + "..."
            //     : data.text;
            // },
        }),
    );

    t.showAddPopup = function (e) {
        e.preventDefault();
        t.addEdit.addLicenseModel();
    };

    t.showEditPopup = function (e) {
        e.preventDefault();
        t.addEdit.editLicenseModel($(this).attr('data-id'));
    };

    t.deleteLicense = function (e) {
        e.preventDefault();
        var accId = $(e.currentTarget).data("id");
        t.httpPostPath = t.config.url.delete + "/" + accId;
        var data = {
            msg: t.config.translations.something_went_wrong,
        };

        sweetAlerts(
            t.config.translations.are_you_want_delete,
            "warning",
            t.httpPostPath,
            t.dTable,
            data
        );
    };
    t.content.on("change", ".license-list-page-length", function () {
        let len = parseInt($(this).val(), 10);
        t.dTable.page.len(len).draw();
    });

    // Global event handlers (moved inside constructor but attached to document)
    $(document).on("click", function (e) {
        if (
            !$(e.target).closest("#detached-action-menu").length &&
            !$(e.target).closest(".user-list-menu-toggle").length
        ) {
            $("#detached-action-menu").remove();
            $(".user-list-menu-toggle").data("menu-open", false);
        }
    });

    $(document).on("click", ".user-list-menu-toggle", function (e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        var $btn = $(this);

        if ($btn.data("menu-open")) {
            $("#detached-action-menu").remove();
            $btn.data("menu-open", false);
            return;
        }

        $("#detached-action-menu").remove();
        $(".user-list-menu-toggle").data("menu-open", false);

        var $menu = $btn.siblings(".dropdown-menu").clone(true);
        $menu.attr("id", "detached-action-menu").addClass("show");

        $("body").append($menu);

        var btnRect = $btn[0].getBoundingClientRect();
        var menuWidth = 200;

        $menu.css({
            position: "fixed",
            top: btnRect.bottom + "px",
            left: btnRect.left - menuWidth + btnRect.width + "px",
            zIndex: 99999,
            display: "block",
        });

        setTimeout(function () {
            var menuHeight = $menu.outerHeight();
            var windowHeight = $(window).height();
            if (btnRect.bottom + menuHeight > windowHeight) {
                $menu.css("top", btnRect.top - menuHeight + "px");
            }

            var menuLeft = parseFloat($menu.css("left"));
            if (menuLeft < 0) {
                $menu.css("left", btnRect.left + "px");
            }
        }, 0);

        $btn.data("menu-open", true);
    });

    $(document).on("click", "#detached-action-menu .dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();

        var $item = $(this);
        var id = $item.data("id");
        var bsTarget = $item.attr("data-bs-target");
        var action = $item.attr("action");

        $("#detached-action-menu").remove();
        $(".user-list-menu-toggle").data("menu-open", false);

        if (bsTarget) {
            var $modal = $(bsTarget);
            if ($modal.length) {
                $modal.data("id", id);
                $modal.data("action", action);
                var modalInstance = bootstrap.Modal.getOrCreateInstance($modal[0]);
                modalInstance.show();
            }
            return;
        }

        var actionClass = null;
        ["dtActView", "dtActClone", "dtActCheckOut", "dtActNote"].forEach(
            function (cls) {
                if ($item.hasClass(cls)) actionClass = cls;
            }
        );

        if (actionClass && id) {
            $("#mytable")
                .find("." + actionClass + '[data-id="' + id + '"]')
                .first()
                .trigger("click");
        }
    });

    $(document).on("scroll", function () {
        $("#detached-action-menu").remove();
        $(".user-list-menu-toggle").data("menu-open", false);
    });

    t.buildRowActions = function (record) {
        var id = record.id;
        var actionState = t.getRowActionState(record);
        var actions = [];
        var tr = t.config.translations || {};

        if (actionState.canRestore) {
            actions.push(
                t.actionItemHtml(
                    tr.action_restore_user || "Restore User",
                    "dtActRestore",
                    id,
                    "restore"
                )
            );
            return actions;
        }

        return actions;
    };

    t.getRowActionState = function (record) {
        return { canRestore: false };
    };



    t.actionItemHtml = function (label, cls, id, action) {
        return '<li><a class="dropdown-item ' + cls + '" href="#" data-id="' + id + '" action="' + action + '">' + label + '</a></li>';
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".license-list-search").val().trim();
        if (v === false || v === "") {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        if (t.dTable) {
            t.dTable.search(v).draw();
            t.cache_filter_values();
        }
    };

    t.btn.openFilter.on("click", function () {
        let filterModal = new bootstrap.Modal(document.getElementById("licensesFilterModal"),);
        filterModal.show();
        t.initDateRangePicker();
    });

    t.syncDateRangePicker = function () {
        if (!t.filters.rangePicker) return;
        var val = t.filters.daterange.val();
        if (!val) {
            t.clearDateRange();
            return;
        }
        var parts = val.split(" - ");
        if (parts.length !== 2) return;
        var start = moment(parts[0], "YYYY-MM-DD HH:mm:ss");
        var end = moment(parts[1], "YYYY-MM-DD HH:mm:ss");
        t.setDateRange(start, end);
    };

    t.noteAccessory = function (e) {
        e.preventDefault();
        var accId = $(this).attr("data-id");
        var http = $.get(t.config.url.get + "/" + accId);
        t.forAction = "note";
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.loadForm(data.accessory, "note");
                } else {
                    sweetAlert("center", "error", data);
                }
            }
        });
        http.fail(function () {
            var data = {
                msg: config.translations.something_went_wrong,
            };
            sweetAlert("center", "error", data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };


    t.initDateRangePicker = function () {

        if (!t.filters.date_range.length || typeof moment === "undefined" || !$.fn.daterangepicker) {
            return;
        }

        if (t.filters.date_range.data("daterangepicker")) {
            t.syncDateRangePicker();
            return;
        }

        if (t.filters.rangePicker) {
            t.syncDateRangePicker();
            return;
        }

        var start = moment().startOf("day");
        var end = moment().endOf("day");

        t.filters.date_range.daterangepicker(
            {
                parentEl: t.filters.wrapper,
                startDate: start,
                endDate: end,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                timePickerIncrement: 1,
                autoApply: false,
                autoUpdateInput: false,
                opens: "right",
                drops: "down",
                locale: {
                    format: "DD-MM-YYYY HH:mm:ss",
                    cancelLabel: "Clear",
                },
                ranges: {
                    Today: [moment().startOf("day"), moment().endOf("day")],
                    Yesterday: [
                        moment().subtract(1, "days").startOf("day"),
                        moment().subtract(1, "days").endOf("day"),
                    ],
                    "Last 7 Days": [
                        moment().subtract(6, "days").startOf("day"),
                        moment().endOf("day"),
                    ],
                    "Last 30 Days": [
                        moment().subtract(29, "days").startOf("day"),
                        moment().endOf("day"),
                    ],
                    "This Month": [moment().startOf("month"), moment().endOf("month")],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                },
            },
            cb,
        );
        cb(start, end);

        t.setDateRange = function (start, end) {
            if (!start || !end) {
                t.clearDateRange();
                return;
            }
            if (t.filters.date_range.length) {
                t.filters.date_range.text(
                    start.format("DD-MM-YYYY HH:mm:ss") +
                    " - " +
                    end.format("DD-MM-YYYY HH:mm:ss"),
                );
            }
            t.filters.daterange.val(
                start.format("YYYY-MM-DD HH:mm:ss") +
                " - " +
                end.format("YYYY-MM-DD HH:mm:ss"),
            );
        };
    };

    t.restoreAccessory = function (e) {
        e.preventDefault();
        var accId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.restore + "/" + accId;
        var data = {
            msg: t.config.translations.something_went_wrong,
        };
        sweetAlerts(
            t.config.translations.are_you_restore,
            "warning",
            t.httpPostPath,
            t.dTable,
            data,
        );
    };

    function cb(start, end) {
        $("#reportrange span").html(
            start.format("DD-MM-YYYY HH:mm:ss") +
            " - " +
            end.format("DD-MM-YYYY HH:mm:ss"),
        );
        $("#daterange").val(
            start.format("YYYY-MM-DD HH:mm:ss") +
            " - " +
            end.format("YYYY-MM-DD HH:mm:ss"),
        );
    }

    t.exportPDF = function (e) {
        e.preventDefault();
        var v = $.trim($("#mytable_wrapper .plain-search").val());
        t.cache_filter_values();
        window.location = t.config.url.download_url_pdf + "?q=" + t.config.export_filters;
    }

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    }


    t.showClonePopup = function (e) {
        e.preventDefault();
        t.addEdit.cloneLicenseModel($(this).attr('data-id'));
    };

    t.btn.clear.on("click", function (e) {
        e.preventDefault();
        if (t.showDeletedLicenses) {
            t.showDeletedLicenses = false;
            t.btn.showDeleted.removeClass('d-none');
            t.btn.showNonDeleted.addClass('d-none');
            t.btn.showDeleted.attr('data-bs-original-title', 'Show Deleted Licenses');
            t.btn.showDeleted.attr('title', 'Show Deleted Licenses');
        }

        // Clear search
        $(".license-list-search").val("");
        t.config.search = "";

        // Clear all filters
        t.filters.manufacturers.val(null).trigger("change");
        t.filters.categories.val(null).trigger("change");
        t.filters.suppliers.val(null).trigger("change");
        t.filters.locations.val(null).trigger("change");
        t.filters.internal_place.val(null).trigger("change");
        t.filters.departments.val(null).trigger("change");
        t.filters.purchase_reference.val(null).trigger("change");
        t.filters.based_on.val(null).trigger("change");
        t.filters.date_range.val("");


        // Clear cached filters
        t.config.other_filters = {};
        t.config.dashboard_filters = {};

        // Reset filter count (if available)
        if (typeof resetFilterCount === "function") {
            resetFilterCount();
        }

        if (typeof resetDateRangeFilter === "function") {
            resetDateRangeFilter();
        }

        // Close filter modal
        var modal = bootstrap.Modal.getInstance(document.getElementById("licensesFilterModal"));
        if (modal) {
            modal.hide();
        }

        // Clear DataTable search
        t.dTable.search("");

        // Reload DataTable
        t.dTable.ajax.reload(null, true);
    });
    var select2Opts = { width: "100%" };
    t.filters.fun.reload_departments();
    t.filters.fun.reload_manufacturers();
    t.filters.fun.reload_locations();
    t.filters.fun.reload_internal_place();
    t.filters.fun.reload_suppliers();
    t.filters.fun.reload_categories();
    t.filters.fun.purchase_reference();
    t.filters.fun.bindFilterCountEvents();
    t.init();
};




