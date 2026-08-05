var TableList = function (config, otherConfig) {
    var t = this;
    t.config = config;
    t.tab = $("#tech-log-report-wrapper");
    t.table = t.tab.find("#tech-log-table");

    t.searchInput  = t.tab.find(".tech-log-list-search");
    t.lengthSelect = t.tab.find(".tech-log-list-page-length");
    t.reloadBtn    = t.tab.find(".btn-reload-list");
    t.exportBtn    = t.tab.find(".btn-download");

    t.sortbtns     = t.tab.find("#srqSortDrop");
    t.shortItems   = t.tab.find("#short_items");
    t.sortAction   = t.sortbtns.find(".sort-action");
    t.dropdownAction = t.sortbtns.find(".dropdown-action");

    t.filterBtn        = t.tab.find(".btn-open-filter");
    t.filterModal      = $("#FilterModal");
    t.filterCountBadge = t.filterBtn.find(".filter-count-badge");

    t.filters = {
        ticket_handlers: $("#filter_by_ticket_handlers"),
        daterange:       $("#daterange"),
        applyBtn:        t.filterModal.find(".btn-filter"),
        clearBtn:        t.filterModal.find("#btnClrFilter")
    };

    t.filters.ticket_handlers.select2({
        width: "100%",
        placeholder: config.translations.by_ticket_handler,
        dropdownParent: t.filterModal,
        ajax: {
            url: t.config.getUserByQuery,
            dataType: "json",
            data: function (p) {
                return { search: p.term, page: p.page || 1, company_id: t.config.company };
            },
            delay: 300
        },
        allowClear: true
    });

    var drpStart = moment();
    var drpEnd   = moment();

    function updateDateRange(start, end) {
        $("#reportrange span").html(
            start.format("DD-MM-YYYY 00:00:00") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
        );
        $(".drp-selected").hide();
        $("#daterange").val(
            start.format("DD-MM-YYYY 00:00:00") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
        );
    }

    $("#reportrange").daterangepicker({
        opens: "center",
        timePicker: true,
        timePicker24Hour: true,
        startDate: drpStart,
        endDate: drpEnd,
        ranges: {
            "Today":      [moment(), moment()],
            "Yesterday":  [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Last 7 Days":  [moment().subtract(6, "days"), moment()],
            "Last 30 Days": [moment().subtract(29, "days"), moment()],
            "This Month": [moment().startOf("month"), moment().endOf("month")],
            "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        }
    }, updateDateRange);

    updateDateRange(drpStart, drpEnd);

    t.cache_filter_values = function () {
        t.config.search = t.searchInput.val() || "";
        t.config.other_filters = {};

        var handlersVal = t.filters.ticket_handlers.val();
        if (handlersVal && handlersVal.length) {
            t.config.other_filters.ticket_handlers = handlersVal;
        }
        var daterangeVal = t.filters.daterange.val();
        if (daterangeVal && daterangeVal !== "") {
            t.config.other_filters.daterange = daterangeVal;
        }

        var jobj = { search: t.config.search, other_filters: t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        var count = Object.keys(t.config.other_filters).length;
        if (count > 0) {
            t.filterCountBadge.removeClass("d-none").text(count);
        } else {
            t.filterCountBadge.addClass("d-none").text(0);
        }
    };

    t._s = function (v) {
        return v === undefined || v === null ? "" : v;
    };

    t.initDataTable = function () {
        var columns = [
            { data: "username"},
            { data: "activity"},
            { data: "start_date"},
            { data: "start_time"},
            { data: "end_date"},
            { data: "end_time"},
            { data: "duration"},
            { data: "comment"}
        ];

        t.dataTable = t.table.DataTable({
            serverSide: true,
            processing: true,
            scrollX: true,
            ajax: {
                url: otherConfig.url,
                type: "POST",
                headers: { "X-CSRF-TOKEN": t.config.token },
                dataSrc: function (json) {
                    json.recordsTotal    = json.total    || 0;
                    json.recordsFiltered = json.filtered || 0;
                    return json.data || [];
                },
                data: function (d) {
                    d.search  = t.config.search;
                    d.page    = (d.start / d.length) + 1;
                    d.size    = d.length;
                    d.order   = t.config.sort_dir;
                    d.filters = t.config.other_filters || {};

                    var jobj = { search: t.config.search, other_filters: d.filters };
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
            pageLength: parseInt(t.lengthSelect.val(), 10) || 10,
            dom: '<"top"lf>rt<"dt-bottom"<"left"i><"right"p>>',
            language: {
                emptyTable: "No Details Found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                processing: "",
                paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" }
            }
        });
    };

    t.searchInput.on("keyup", function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            t.cache_filter_values();
            t.dataTable.draw();
        }
    });

    t.lengthSelect.on("change", function () {
        t.dataTable.page.len(parseInt($(this).val(), 10)).draw();
    });

    t.reloadBtn.on("click", function () {
        t.dataTable.ajax.reload();
    });

    t.exportBtn.on("click", function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_ticket + "?filters=" + t.config.export_filters;
    });

    t.filterBtn.on("click", function () {
        var modal = new bootstrap.Modal(t.filterModal[0]);
        modal.show();
    });

    t.filters.applyBtn.on("click", function () {
        t.cache_filter_values();
        t.dataTable.draw();
        var modal = bootstrap.Modal.getInstance(t.filterModal[0]);
        if (modal) modal.hide();
    });

    t.filters.clearBtn.on("click", function () {
        t.filters.ticket_handlers.val(null).trigger("change");

        var drp = $("#reportrange").data("daterangepicker");
        if (drp) {
            drp.setStartDate(moment());
            drp.setEndDate(moment());
        }
        $("#reportrange span").html('<span style="color:#999;">Select Date Range</span>');
        $("#daterange").val("");

        t.cache_filter_values();
        t.dataTable.draw();

        var modal = bootstrap.Modal.getInstance(t.filterModal[0]);
        if (modal) modal.hide();
    });

    t.renderSortDropdown = function () {
        var $dropdown = t.shortItems;
        var s = t.config.sort_dir;
        $dropdown.empty();
        $.each(t.config.sort_fields, function (i, d) {
            var isActive = d.id == s.id;
            $dropdown.append(
                "<li><a href='javascript:void(0)' class='dropdown-item " + (isActive ? "active" : "") + "' data-id='" + d.id + "'>" +
                "<span class='like-radio'></span><span class='flex-grow-1'>" + d.text + "</span>" +
                (isActive ? "<i class='bi " + (s.dir == 1 ? "bi-sort-up" : "bi-sort-down") + "'></i>" : "") +
                "</a></li>"
            );
        });
    };

    t.updateSortDirectionIcon = function () {
        $("#sortDirectionIcon").removeClass("bi-sort-up bi-sort-down")
            .addClass(t.config.sort_dir.dir == 1 ? "bi-sort-up" : "bi-sort-down");
    };

    t.sortAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.updateSortDirectionIcon();
        t.renderSortDropdown();
        t.dataTable.draw();
    });

    t.dropdownAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.shortItems.toggleClass("show");
    });

    t.shortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.id = $(this).data("id");
        t.shortItems.removeClass("show");
        t.renderSortDropdown();
        t.dataTable.draw();
    });

    t.shortItems.on("click", function (e) { e.stopPropagation(); });

    $(document).on("click", function () {
        t.shortItems.removeClass("show");
    });

    t.renderSortDropdown();
    t.initDataTable();
};

var MyApp = function (config) {
    var t = this;
    t.deviceTab = new TableList(config, {
        tab: "#tech-log-report-wrapper",
        url: config.url.list_tech_logs,
        sort_fields: config.sort_fields
    });
};