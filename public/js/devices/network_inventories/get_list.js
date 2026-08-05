var NI = function (config) {
    var t = this;
    t.config = config;
    t.table = $("#mytable");
    t.content = $("section.content");
  
    t.dTbl = null;
    t.submitUrl = "";
    t.editId = null;
    t.showDeleted = 0;
    t.btn = {
        reload: ".btn-reload-list",
        export: ".btn-ni-export",
        export_pdf: ".btn-ni-export-pdf",
        openFilter: ".btn-open-filter",
        filter: ".btn-filter",
        clearFilter: ".btn-clear-filter"
    };

    t.filterMdl = $("#filterMdl");
    t.filter = {
        manufacturer: "#filter_by_manufacture",
        category: "#filter_by_category",
        model: "#filter_by_model",
        processor: "#filter_by_processor_name",
        os: "#filter_by_os",
        platform: "#filter_by_platform",
        systemType: "#filter_by_computer_system_type",
        genuineStatus: "#filter_by_genuine_status",
        hardDisk: "#filter_by_hardisk",
        hardDiskCondition: "#filter_by_diskcondn",
        ram: "#filter_by_ram",
        ramCondition: "#filter_by_ramcondn",
        licenseStatus: "#filter_by_license_status",
        version: "#filter_by_version",
        location: "#filter_by_location",
        internalPlace: "#filter_by_internal_place",
        date: "#filter_by_date",
        dateRange: "#daterange",
        adAgent: "#filter_by_ad_nonad_agent",

        category: "#filter_by_category"
    };
    t.config.search = t.config.search || "";
    t.config.other_filters = t.config.other_filters || {};
    t.config.export_filters = t.config.export_filters || "";

    t.init();
};

NI.prototype.init = function () {
    var t = this;
    t.bindEvents();
    t.initFilters();
    t.loadFilterData();
    setTimeout(function () {
        t.initDataTable();
    }, 50);

};

NI.prototype.initDataTable = function () {
    var t = this;
    let defaultLength = parseInt($(".ni-list-page-length").val()) || 10;
    $(".ni-list-page-length").val(defaultLength);
    t.dTbl = t.table.DataTable({
        language: datatable_footer_translations(t.config.datatable_translations),
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        stateSave: false,
        scrollX: true,
        scrollCollapse: true,
        deferRender: true,
        searchDelay: 500,
        pageLength: defaultLength,
        lengthMenu: [10, 25, 50, 100],
        lengthChange: false,
        dom: "lrtip",  
        fixedColumns: {
            right: 1,
        },
        aoColumnDefs: [{
            targets: 7,
            bSortable: false,
            render: t.tblHelpers.actions(t.config)
        },
        {
            targets: 0,
            className: "text-center",
            render: function (d) {
                var a = [];
                if (d.asset_tag && d.is_virtual == null) {
                    a.push("<div><a style = 'color: #eb2521' href=\"" + config.url.device_info + "/" + d.asset_id + "\">" + d.asset_tag + "</a></div>");
                    if (d.is_AD == 1) {
                        var tooltipHtml = '<strong>' + config.translations.device_from_network_is_AD + '</strong>';

                        if (d.agentVersion) {
                            tooltipHtml += '<br>Agent Version: <span class="text-primary">' + d.agentVersion + '</span>';
                        }

                        a.push("<div><svg width='17' height='14' viewBox='0 0 17 14' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M0.75 12.4167H8.25M8.25 12.4167H15.75M8.25 12.4167V7.41667M8.25 7.41667H13.25V0.75H3.25V7.41667H8.25ZM5.75 4.09167L5.75833 4.0825M8.25 4.09167L8.25833 4.0825' stroke='#7F7F7F' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'></path>svg></div>");
                        


                    } else {
                        var tooltipHtml = '<strong>' + config.translations.device_from_network_is_non_AD + '</strong>';

                        if (d.agentVersion) {
                            tooltipHtml += '<br>Agent Version: <span class="text-primary">' + d.agentVersion + '</span>';
                        }

                        a.push(
                            '<div>' +
                                "<svg width='17' height='14' viewBox='0 0 17 14' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M0.75 12.4167H8.25M8.25 12.4167H15.75M8.25 12.4167V7.41667M8.25 7.41667H13.25V0.75H3.25V7.41667H8.25ZM5.75 4.09167L5.75833 4.0825M8.25 4.09167L8.25833 4.0825' stroke='#7F7F7F' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'></path>svg>"+
                            '</div>'
                        );
                    }
                }
                else if (d.is_virtual == 1) {
                    a.push("<div><span class='label label-danger'>Virtual Device</span></div>");
                }
            
                return a.join(" ");
            }
        },

        {
            targets: 1,
            render: function (d) {
                var a = [];
                if (d.ComputerName) {
                    a.push('<div class="fw-bold" data-bs-toggle="tooltip" title="'+t.config.translations.name+'" >' + d.ComputerName + '</div>');
                }
                if (d.ComputerManufacturer) {
                    a.push('<div  data-bs-toggle="tooltip" title="'+config.translations.manufacturer+'">' + d.ComputerManufacturer + '</div>');
                }
                if (d.ComputerModel) {
                    a.push('<div class="fw-bold"  data-bs-toggle="tooltip" title="'+config.translations.model+'">' + d.ComputerModel + '</div>');
                }
                if (d.BIOSSerialNumber) {
                    a.push('<div data-bs-toggle="tooltip" title="'+config.translations.serial_no+'">' + d.BIOSSerialNumber + '</div>');
                }
                if (d.ProcessorName) {
                    a.push('<div class="fw-bold" data-bs-toggle="tooltip" title="'+config.translations.processor_name+'">' + d.ProcessorName + '</div>');
                }
                if (d.ComputerSystemType) {
                    a.push('<div data-bs-toggle="tooltip" title="'+config.translations.system_type+'">' + d.ComputerSystemType + '</div>');
                }
                return a.join(" ");
            },
        }, {
            targets: 5,
            render: function (d) {
                var a = [];
                if (d.IPv4) {
                    a.push('<div data-bs-toggle="tooltip" title="'+ config.translations.ip_address  +'">' + d.IPv4 + '</div>');
                }
                 if (d.IPv6) {
                    a.push('<div data-bs-toggle="tooltip" title="'+ config.translations.ip_address_v6  +'">' + d.IPv6 + '</div>');
                }
                if (d.ActiveMACAddress) {
                    a.push('<div data-bs-toggle="tooltip" title="'+ config.translations.mac_address  +'">' + d.ActiveMACAddress + '</div>');
                }
                return a.join(" ");
            }
        }],
        order: [[6, 'desc']],
        ajax: {
            url: t.config.url.list,
            type: "post",
            data: function (d) {
                d.filters = t.config.other_filters || {};
                d._token = t.config.token;
            },
        },
        columns: [
            { data: 'a' },
            { data: 'a' },
            { data: 'a.ComputerDomain' },
            { data: 'a.OSCaption' },
            {
                data: 'a',
                render: function (d) {
                    var a = [];

                    if (d.RamSize) {
                        a.push('<div data-bs-toggle="tooltip" title="'+ config.translations.ram_size+'"><strong>RAM:</strong> ' + d.RamSize + '</div>');
                    }

                    if (d.HddSize) {
                        a.push('<div data-bs-toggle="tooltip" title="'+ config.translations.hard_disk_size +'"><strong>Storage:</strong> ' + d.HddSize + '</div>');
                    }

                    return a.join('');
                }
            },
            { data: 'a' },
            { data: 'a.updated_at_format' },
            { data: 'a' },

        ],
        drawCallback: function() {
            $('#mytable tbody td').addClass('b5-text');
            $('[data-bs-toggle="tooltip"]').tooltip();
            t.scheduleTableLayoutSync();
        },
        initComplete: function () {
            t.scheduleTableLayoutSync();
        },
        error: function(xhr) {
            console.log(xhr.status);
            console.log(xhr.responseText);
        }
    });
};

NI.prototype.scheduleTableLayoutSync = function () {
    var t = this;

    setTimeout(function () {
        if (!t.dTbl) return;

        t.dTbl.columns.adjust();

        if (typeof t.dTbl.fixedColumns === "function") {
            var fc = t.dTbl.fixedColumns();

            if (fc && fc.relayout) {
                fc.relayout();
            } else if (fc && fc.update) {
                fc.update();
            }
        }
    }, 100);
};

NI.prototype.tblHelpers = {
    actions: function (config) {
        return function (d) {
            var str = [];

            str.push(
                "<button type='button' class='amg-btn amg-btn-primary amg-btn-sm open-add-modal d-flex' " +
                "onclick=\"window.location.href='" + config.url.info + "/" + d.basic_id + "'\">" +
                    "<svg xmlns='http://www.w3.org/2000/svg' width='1em' height='1em' viewBox='0 0 24 24'><path d='M0 0h24v24H0z' fill='none' /><path fill='currentColor' d='M12.713 16.713Q13 16.425 13 16v-4q0-.425-.288-.712T12 11t-.712.288T11 12v4q0 .425.288.713T12 17t.713-.288m0-8Q13 8.425 13 8t-.288-.712T12 7t-.712.288T11 8t.288.713T12 9t.713-.288M12 22q-2.075 0-3.9-.788t-3.175-2.137T2.788 15.9T2 12t.788-3.9t2.137-3.175T8.1 2.788T12 2t3.9.788t3.175 2.137T21.213 8.1T22 12t-.788 3.9t-2.137 3.175t-3.175 2.138T12 22m0-2q3.35 0 5.675-2.325T20 12t-2.325-5.675T12 4T6.325 6.325T4 12t2.325 5.675T12 20m0-8' /></svg> " +
                    config.translations.view +
                "</button>"
            );

            return str.join(" ");
        };
    }
};

NI.prototype.bindEvents = function () {
    var t = this;

    t.content.on("click", t.btn.reload, () => t.reload());

    t.content.on("click", t.btn.export, $.proxy(t.handleExportClick, t));
    t.content.on("click", t.btn.export_pdf, $.proxy(t.handleExportPdfClick, t));

    t.content.on('click',t.btn.openFilter,function(e){
        t.filterMdl.modal('show');
    })

    t.filterMdl.on("click", t.btn.filter, $.proxy(t.applyFilters, t));
    t.filterMdl.on("click", t.btn.clearFilter, $.proxy(t.clearFilters, t));
    
    $(".ni-list-search").on("keyup", function (e) {
        if(e.keyCode == 13){

        t.dTbl.search(this.value).draw();
        }
    });

    $(document).off("change", ".ni-list-page-length").on("change", ".ni-list-page-length", function () {
        let val = parseInt($(this).val());

        if (val && t.dTbl) {
            t.dTbl.page.len(val).draw();
        }
    });
};

NI.prototype.reload = function () {
    if (this.dTbl) {
        this.dTbl.ajax.reload(null, false);
    }
};

NI.prototype.handleExportClick = function (e) {
    let obj = {
      search: $(".ni-list-search").val(),
      other_filters: this.config.other_filters,
    };
    let encoded = btoa(JSON.stringify(obj));
    let url = this.config.url.download_url + "?q=" + encoded;
    window.location = url;
};

NI.prototype.handleExportPdfClick = function (e) {
    if (e) e.preventDefault();
    let obj = {
        search: $(".ni-list-search").val(),
        other_filters: this.config.other_filters,
    };
    let encoded = btoa(JSON.stringify(obj));
    window.location = this.config.url.download_pdf_url+ "?q=" + encoded
};

NI.prototype.initFilters = function (e){
    let t = this;
    t.initSelect2(t.filter.manufacturer, "Filter By Manufacturer");
    t.initSelect2(t.filter.model, "Filter By Model");
    t.initSelect2(t.filter.processor, "Filter By Processor");
    t.initSelect2(t.filter.os, "Filter By OS");
    t.initSelect2(t.filter.platform, "Filter By Platform");
    t.initSelect2(t.filter.systemType, "Filter By System Type");
    t.initSelect2(t.filter.genuineStatus, "Filter By License Activated");
    t.initSelect2(t.filter.hardDiskCondition, "Hard Disk Condition");
    t.initSelect2(t.filter.ramCondition, "RAM Condition");
    t.initSelect2(t.filter.licenseStatus, "License Status");
    t.initSelect2(t.filter.version, "Version");
    t.initSelect2(t.filter.location, "Location");
    t.initSelect2(t.filter.internalPlace, "Internal Place");
    t.initSelect2(t.filter.date, "Filter By Date");
    t.initSelect2(t.filter.adAgent, "AD/Non AD Agent");

    t.initCategoryFilter();
    t.initLocationFilter();
    t.initInternalPlaceFilter();

}
NI.prototype.initSelect2 = function (selector, placeholder) {
    let t = this;
    $(selector).select2({
        width: "100%",
        allowClear: true,
        placeholder: placeholder,
        dropdownParent: t.filterMdl
    });
};

NI.prototype.initCategoryFilter = function () {
    var t = this;

    $(t.filter.category).select2({
        width: "100%",
        allowClear: true,
        placeholder: "Filter By Category",
        dropdownParent: t.filterMdl,
        ajax: {
            url: t.config.url.get_filter_categories,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data,params) {
                                params.page = params.page || 1;

                return {
                    results: $.map(data.results || data.data || data, function (item) {
                        return {
                            id: item.id,
                            text: item.text || item.name
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });
};

NI.prototype.initLocationFilter = function () {
    var t = this;
    $(t.filter.location).select2({
        width: "100%",
        allowClear: true,
        placeholder: "Filter By Location",
        dropdownParent: t.filterMdl,
        ajax: {
            url: t.config.url.getLocationByAjax,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            }
        }
    });
};

NI.prototype.initInternalPlaceFilter = function () {
    var t = this;
    var $select = $(t.filter.internalPlace);
    $select.empty();
    $.each(t.config.internal_place, function (_, item) {
        $select.append(
            new Option(item.text, item.id, false, false)
        );
    });
    $select.select2({
        width: "100%",
        allowClear: true,
        placeholder: "Filter By Internal Place",
        dropdownParent: t.filterMdl
    });
    $select.trigger("change");
};

NI.prototype.cacheFilterValues = function () {
    var t = this;
    t.config.other_filters = {};
    t.filter.search = $('.ni-list-search') && $('.ni-list-search').val ? $('.ni-list-search').val() || "" : "";

    if ($(t.filter.manufacturer).val() && $(t.filter.manufacturer).val() != '')
        t.config.other_filters.manufacturer = $(t.filter.manufacturer).val();

    if ($(t.filter.category).val() && $(t.filter.category).val() != '')
        t.config.other_filters.category = $(t.filter.category).val();

    if ($(t.filter.model).val())
        t.config.other_filters.model = $(t.filter.model).val();

    if ($(t.filter.processor).val())
        t.config.other_filters.processor_name = $(t.filter.processor).val();

    if ($(t.filter.os).val())
        t.config.other_filters.os = $(t.filter.os).val();

    if ($(t.filter.platform).val())
        t.config.other_filters.platform = $(t.filter.platform).val();

    if ($(t.filter.systemType).val())
        t.config.other_filters.computer_system_type = $(t.filter.systemType).val();

    if ($(t.filter.genuineStatus).val())
        t.config.other_filters.genuine_status = $(t.filter.genuineStatus).val();

    if ($(t.filter.hardDisk).val())
        t.config.other_filters.hdd = $(t.filter.hardDisk).val();

    if ($(t.filter.hardDiskCondition).val())
        t.config.other_filters.hdd_cond = $(t.filter.hardDiskCondition).val();

    if ($(t.filter.ram).val())
        t.config.other_filters.ram = $(t.filter.ram).val();

    if ($(t.filter.ramCondition).val())
        t.config.other_filters.ram_cond = $(t.filter.ramCondition).val();

    if ($(t.filter.licenseStatus).val())
        t.config.other_filters.license_status = $(t.filter.licenseStatus).val();

    if ($(t.filter.version).val())
        t.config.other_filters.version = $(t.filter.version).val();

    if ($(t.filter.location).val())
        t.config.other_filters.location = $(t.filter.location).val();

    if ($(t.filter.internalPlace).val())
        t.config.other_filters.internal_place = $(t.filter.internalPlace).val();

    if ($(t.filter.date).val())
        t.config.other_filters.based_on = $(t.filter.date).val();

    if ($(t.filter.dateRange).val() && $(t.filter.date).val())
        t.config.other_filters.date_range = $(t.filter.dateRange).val();

    if ($(t.filter.adAgent).val())
        t.config.other_filters.ad_nonad_agent = $(t.filter.adAgent).val();
};

NI.prototype.reloadSelectOptions = function (select, data) {
    select.empty().append(new Option(this.config.translations.No_Filter, "", false, false));

    $.each(data, function (_, item) {//ignoring first param
        select.append(new Option(item, item, false, false));
    });

    select.trigger("change");
};

NI.prototype.loadFilterData = function () {
    const t = this;
    const filter_mappings = [
        { selector: t.filter.manufacturer, data: t.config.manufacturers },
        { selector: t.filter.model, data: t.config.models },
        { selector: t.filter.os, data: t.config.oss },
        { selector: t.filter.version, data: t.config.version },
        { selector: t.filter.systemType, data: t.config.computerSystemTypes },
        { selector: t.filter.processor, data: t.config.processorNames },
        // { selector: t.filter.internalPlace, data: t.config.internal_place}
    ];

    filter_mappings.forEach(({ selector, data }) => {
        t.reloadSelectOptions($(selector), data);
    });
};


NI.prototype.applyFilters = function () {
    this.cacheFilterValues();
    this.reload();
    this.filterMdl.modal("hide");
    this.updateFilterCount();
};

NI.prototype.updateFilterCount = function () {
    var filters = this.config.other_filters || {};

    var count = Object.values(filters).filter(function (value) {
        return value !== null && value !== "" && value !== "null" && !(Array.isArray(value) && value.length === 0);//count filters which has real value
    }).length;

    var badge = $(".filter-count-badge");

    if (count > 0) {
        badge.removeClass("d-none").text(count);
    } else {
        badge.addClass("d-none").text("0");
    }
};

NI.prototype.clearFilters = function () {
    var t = this;

    $.each(t.filter, function (key, selector) {
        if ($(selector).hasClass("select2-hidden-accessible")) {
            $(selector).val(null).trigger("change");
        } else {
            $(selector).val("");
        }
    });

    t.config.other_filters = {};
    t.reload();
    t.filterMdl.modal("hide");
    t.updateFilterCount();
};


$(function () {
  new NI(config);
});