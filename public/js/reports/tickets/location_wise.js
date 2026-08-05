var TableList = function(config, otherConfig) {
    var t = this;
    t.config = config;
    t.content = $(".location-report-list-wrapper");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find("#lgTbl");
    
    t.searchInput = t.tab.find("#tableSearch");
    t.lengthSelect = t.tab.find("#showSelect");
    t.reloadBtn = t.tab.find(".btn-reload-list");
    t.exportBtn = t.content.find(".btn-download");
    t.sortbtns = t.content.find('#srqSortDrop');
    t.shortItems = t.content.find('.amg-sort-menu');
    t.sortAction = t.sortbtns.find('.sort-action');
    t.dropdownAction = t.sortbtns.find('.dropdown-action');
    t.filterBtn = t.content.find(".btn-filter-open");
    t.filterModal = $("#filterModal");

    t.filters = {
        location: $("#filter_by_location"),
        based_on: $("#filter_by_date"),
        daterange: $("#daterange"),
        applyBtn: t.filterModal.find(".btn-filter-apply"),
        clearBtn: t.filterModal.find("#btnClrFilter"),
        countBadge: t.filterBtn.find("#filter_count")
    };

    t.filters.location.select2({
        width: "100%",
        placeholder: config.translations.filter_by_location || "Select Location",
        dropdownParent: t.filterModal
    });

    $.each(t.config.locations, function(i, k) {
        t.filters.location.append(new Option(k.name, k.id));
    });
    t.filters.location.trigger("change");

    $('#reportrange').daterangepicker({
        autoUpdateInput: true,
        opens: "center",
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: "Apply",
            cancelLabel: "Clear",
            customRangeLabel: "Custom Range"
        },
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start, end, label) {
        var formattedStart = start.format('DD/MM/YYYY');
        var formattedEnd = end.format('DD/MM/YYYY');
        $('#reportrange span').html(formattedStart + ' - ' + formattedEnd);
        $('#daterange').val(formattedStart + ' - ' + formattedEnd);
    });

    $('#reportrange span').html('<span style="color: #999;">Select Date Range</span>');

    t.cache_filter_values = function() {
        var v = t.searchInput.val() || "";
        t.config.search = v;
        console.log(t.config.search);
        t.config.other_filters = {};
        
        var locationVal = t.filters.location.val();
        if(locationVal && locationVal.length > 0) {
            t.config.other_filters.location = locationVal;
        }
        var basedOnVal = t.filters.based_on.val();
        if(basedOnVal && basedOnVal != 'null') {
            t.config.other_filters.based_on = basedOnVal;
        }
        var daterangeVal = t.filters.daterange.val();
        if(daterangeVal && (daterangeVal != '' || daterangeVal != 'null') &&  basedOnVal != 'null') {
            t.config.other_filters.daterange = daterangeVal;
        }
        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.based_on.val(), false);
    };

    t.initDataTable = function(columns) {
        t.dataTable = t.table.DataTable({
            serverSide: true,
            processing: true,
            scrollX: true,
            fixedColumns: {
                leftColumns: 1
            },
            ajax: {
                url: otherConfig.url,
                type: "POST",
                headers: { 'X-CSRF-TOKEN': t.config.token },
                dataSrc: function(json) {
                    json.recordsTotal = json.total || 0;
                    json.recordsFiltered = json.filtered || 0;
                    return json.data || [];
                },
                data: function(d) {
                    d.search = t.config.search;
                    d.page = (d.start / d.length) + 1;
                    d.size = d.length;
                    d.order = t.config.sort_dir,
                    d.main_filter = t.config.main_filter;
                    d.filters = t.config.other_filters || {};
                  

                    var jobj = {"search":t.config.search,"other_filters":d.filters};
                    t.config.export_filters = btoa(JSON.stringify(jobj));
                }
            },
            columns: columns,
            ordering: false,
            searching: false,
            paging: true,
            pagingType: "simple_numbers",
            info: true,
            lengthChange: false,
            pageLength: parseInt(t.lengthSelect.val()) || 10,
            dom: '<"top"lf>rt<"dt-bottom"<"left"i><"right"p>>',
            language: {
                emptyTable: "No Locations Found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            drawCallback: function(settings) {
                var pagination = t.tab.find('.dataTables_paginate .paginate_button');
                pagination.each(function() {
                    if($(this).hasClass('previous')) {
                        $(this).attr('id', 'lgTbl_previous');
                    } else if($(this).hasClass('next')) {
                        $(this).attr('id', 'lgTbl_next');
                    }
                });
            }
        });
    };

    t.loadColumnsAndInit = function() {
        $.ajax({
            url: otherConfig.url,
            type: "POST",
            headers: { 'X-CSRF-TOKEN': t.config.token },
            data: {
                search: t.config.search,
                page: 1,
                size: 1,
                filters: {}
            },
            success: function(res) {
                var columns = [];
                if(res.data && res.data.length > 0) {
                    $.each(res.data[0], function(key, val) {
                        var col = { data: key, title: "", orderable: false };
                        if(key === 'id') {
                            col.visible = false;
                        } else if(key === 'loc_name') {
                            col.title = "Location";
                        } else if(key === 'tot') {
                            col.title = "Total";
                        } else {
                            var split = key.split("_");
                            col.title = split[0];
                        }
                        
                        col.render = function(data, type, row) {
                            if(key === 'id') return data;
                            if(key === 'loc_name') return data;
                            
                            var baseUrl = (typeof baseURL !== 'undefined' ? baseURL : '') + '/reports/tickets/info/location_info?';
                            var filters = [
                                'location_id=' + encodeURIComponent(row.id),
                                'location_filters=' + encodeURIComponent(t.config.export_filters || '')
                            ];
                            
                            if (key === 'tot') {
                                return '<a href="' + baseUrl + filters.join('&') + '" target="_blank" class="underline">' + (data == null ? "" : data) + '</a>';
                            } else {
                                var split = key.split("_");
                                if(split.length === 3) {
                                    return '<a href="' + baseUrl + 'ticket_type=' + encodeURIComponent(split[2]) + '&' + filters.join('&') + '" target="_blank" class="underline">' + (data == null ? "" : data) + '</a>';
                                } else {
                                    return '<a href="' + baseUrl + 'status_id=' + encodeURIComponent(split[1]) + '&' + filters.join('&') + '" target="_blank" class="underline">' + (data == null ? "" : data) + '</a>';
                                }
                            }
                        };
                        columns.push(col);
                    });
                } else {
                    columns = [{ data: "loc_name", title: "Location", orderable: false }];
                }
                t.initDataTable(columns);
            },
            error: function() {
                alert("Failed to load table structure.");
            }
        });
    };

    $(document).on("keyup", ".amg-list-searchbar__input", function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            var v = $(this).val().trim();
            if (v === false) {
                alert("Please enter a valid value for search");
                return false;
            }
            t.cache_filter_values();
            t.dataTable.draw();
        }
    });

    t.lengthSelect.on("change", function() {
        t.dataTable.page.len($(this).val()).draw();
    });

    t.reloadBtn.on("click", function() {
        t.dataTable.ajax.reload();
    });

    t.exportBtn.on("click", function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket + "?q=" + t.config.export_filters;
    });

    t.filterBtn.on("click", function() {
        var modal = new bootstrap.Modal(t.filterModal[0]);
        modal.show();
    });

    // Apply filter
    t.filters.applyBtn.on("click", function() {
        t.cache_filter_values();
        t.dataTable.draw();
        var modal = bootstrap.Modal.getInstance(t.filterModal[0]);
        modal.hide();
    });

    // Clear filter - FIXED VERSION
    t.filters.clearBtn.on("click", function() {
        t.filters.location.val("").trigger("change");
        t.filters.based_on.val("null").trigger("change");
        var daterangepicker = $('#reportrange').data('daterangepicker');
        if(daterangepicker) {
            daterangepicker.setStartDate(moment());
            daterangepicker.setEndDate(moment());
        }
        t.filters.daterange.val("");
        $('#reportrange span').html('<span style="color: #999;">Select Date Range</span>');
        t.cache_filter_values();
        t.dataTable.draw();
        resetFilterCount();
        t.filterModal.modal("hide");
    });

    t.renderSortDropdown = function () {
        var $dropdown =t.shortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            $dropdown.append(`
                <li>
                    <a href="javascript:void(0)"
                    class="dropdown-item ${isActive ? 'active' : ''}"
                    data-id="${d.id}"
                    data-bs-toggle="tooltip"
                    data-bs-placement="left"
                    data-bs-original-title="${d.text}">
                        <span class="like-radio"></span>
                        <span class="flex-grow-1">${d.text}</span>
                        ${isActive
                            ? `<i class="bi ${s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down'}"></i>`
                            : ''
                        }
                    </a>
                </li>
            `);
        });
        $dropdown.find('[data-bs-toggle="tooltip"]').each(function () {
            bootstrap.Tooltip.getOrCreateInstance(this, {
                container: 'body'
            });
        });
    };
    
    /* Sort Direction */
    t.sortAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();

        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;

        t.updateSortDirectionIcon();

        t.renderSortDropdown();
        t.dataTable.draw();
    });

    /* Open Dropdown */
    t.dropdownAction.on("click",function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.shortItems.toggleClass("show");
    });

    t.updateSortDirectionIcon = function () {
        $("#sortDirectionIcon").removeClass("bi-sort-up bi-sort-down").addClass(t.config.sort_dir.dir == 1? "bi-sort-up": "bi-sort-down");
    };

    /* Select Sort Field */
    t.shortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.config.sort_dir.id = $(this).data("id");
        t.shortItems.removeClass("show");
        t.renderSortDropdown();
        t.dataTable.draw();
    });

    /* Keep dropdown open when clicking inside */
    t.shortItems.on("click", function (e) {
        e.stopPropagation();
    });

    /* Close outside click */
    $(document).on("click", function () {
        const tooltip = bootstrap.Tooltip.getInstance(this);
        if (tooltip) {
            tooltip.hide();
        }
        t.shortItems.removeClass("show");
    });
    t.renderSortDropdown();
    t.loadColumnsAndInit();
    t.filters.based_on.select2($.extend({},{dropdownParent:t.filterModal,placeholder: config.translations.No_Filter,width:'100%'}));

};

var MyApp = function(config) {
    var t = this;
    t.deviceTab = new TableList(config, {
        tab: "#mainContent", 
        url: config.url.get_tickets, 
        sort_fields: config.sort_fields, 
        tbl_fields: config.tbl_fields
    });
};