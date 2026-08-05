var FailedJobs = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#main-failed-jobs-wrapper");
    t.table = t.content.find("#failedJobTable");
    t.searchbox = t.content.find(".searchbox");
    t.pageLength = t.content.find(".failed-jobs-page-length");
    t.actionStyleScope = t.content.find(".list-view-panel").first();
    t.page = t.content;

    t.httpCall = true;
    t.httpPostPath = "";
    t.data = {};

    // ─── FILTERS (Modal) ──────────────────────────────────────────────────────────
    t.filters = {
        wrapper: $('#failedJobsFilterModal'),
        trigger: t.content.find(".btn-open-filter"),
        reportrange: $('#failedJobsFilterModal').find('#reportrange'),
        daterange: $('#failedJobsFilterModal').find('#daterange'),
        reportrangeText: $('#failedJobsFilterModal').find('#reportrange span'),
        based_on: $('#failedJobsFilterModal').find('#filter_by_date'),
        btnFilter: $('#failedJobsFilterModal').find('.btn-filter'),
        btnfilterclr: $('#failedJobsFilterModal').find('.btn-clear-filter'),
        badge: t.content.find(".filter-count-badge"),
        rangePicker: null
    };

    // ─── TRANSLATION & TOAST HELPERS ──────────────────────────────────────────────
    t.tr = function(key, fallback) {
        return (t.config.translations && t.config.translations[key]) ? t.config.translations[key] : fallback;
    };

    t.resolveMessage = function(message) {
        if (message && typeof message === "object" && message.msg) return message.msg;
        if (typeof message === "string" && $.trim(message).length) return message;
        return t.tr("something_went_wrong", "Something went wrong.");
    };

    t.showToast = function(icon, message) {
        var msg = t.resolveMessage(message);
        var dfd = $.Deferred();
        if (window.Swal && typeof Swal.fire === "function") {
            Swal.fire({
                icon: icon,
                title: icon === "success" ? "Success" : "Error",
                text: msg,
                confirmButtonText: "OK",
                allowOutsideClick: false
            }).then(function(result) { dfd.resolve(result); });
            return dfd.promise();
        }
        alert(msg);
        dfd.resolve();
        return dfd.promise();
    };

    // ─── DATE RANGE HELPERS ──────────────────────────────────────────────────────
    t.composeDateRange = function() {
        return $.trim(t.filters.daterange.val() || "");
    };

    t.clearDateRange = function() {
        if (t.filters.rangePicker && typeof moment !== "undefined") {
            t.filters.rangePicker.setStartDate(moment().startOf("day"));
            t.filters.rangePicker.setEndDate(moment().endOf("day"));
        }
        if (t.filters.reportrangeText.length) {
            t.filters.reportrangeText.text(t.tr("select_date_range", "Select date range"));
        }
        t.filters.daterange.val("");
    };

    t.setDateRange = function(start, end) {
        if (!start || !end) { t.clearDateRange(); return; }
        if (t.filters.reportrangeText.length) {
            t.filters.reportrangeText.text(
                start.format("DD-MM-YYYY HH:mm:ss") + " - " + end.format("DD-MM-YYYY HH:mm:ss")
            );
        }
        t.filters.daterange.val(
            start.format("YYYY-MM-DD HH:mm:ss") + " - " + end.format("YYYY-MM-DD HH:mm:ss")
        );
    };

    t.syncDateRangePicker = function() {
        if (!t.filters.rangePicker) return;
        var val = t.filters.daterange.val();
        if (!val) { t.clearDateRange(); return; }
        var parts = val.split(" - ");
        if (parts.length !== 2) return;
        var start = moment(parts[0], "YYYY-MM-DD HH:mm:ss");
        var end   = moment(parts[1], "YYYY-MM-DD HH:mm:ss");
        t.setDateRange(start, end);
    };

    t.initDateRangePicker = function() {
        if (!t.filters.reportrange.length || typeof moment === "undefined" || !$.fn.daterangepicker) return;
        if (t.filters.rangePicker) { t.syncDateRangePicker(); return; }
        var start = moment().startOf("day");
        var end   = moment().endOf("day");
        function cb(start, end) {
            t.filters.reportrangeText.html(
                start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format('DD-MM-YYYY HH:mm:ss')
            );
            t.filters.daterange.val(
                start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss')
            );
        }
        t.filters.reportrange.daterangepicker({
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
                cancelLabel: "Clear"
            },
            ranges: {
                "Today": [moment().startOf("day"), moment().endOf("day")],
                "Yesterday": [moment().subtract(1,"days").startOf("day"), moment().subtract(1,"days").endOf("day")],
                "Last 7 Days": [moment().subtract(6,"days").startOf("day"), moment().endOf("day")],
                "Last 30 Days": [moment().subtract(29,"days").startOf("day"), moment().endOf("day")],
                "This Month": [moment().startOf("month"), moment().endOf("month")],
                "Last Month": [moment().subtract(1,"month").startOf("month"), moment().subtract(1,"month").endOf("month")]
            }
        }, cb);
        cb(start, end);
    };

    // ─── FILTER BADGE ────────────────────────────────────────────────────────────
    t.updateFilterBadge = function() {
        var filterCount = 0;
        var other = t.config.other_filters || {};
        if (other.based_on && other.based_on !== "null" && other.daterange) filterCount += 1;
        if (!t.filters.badge.length) return;
        if (filterCount > 0) {
            t.filters.badge.text(filterCount).removeClass("d-none");
        } else {
            t.filters.badge.addClass("d-none").text("0");
        }
    };

    // ─── CACHE FILTER VALUES (for export) ──────────────────────────────────────
    // FIXED: always set based_on, even if "null"
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v;

        // Always include based_on (default to "null")
        t.config.other_filters = {
            based_on: t.filters.based_on.val() || "null"
        };

        // Add daterange only if a date range is selected
        if (t.filters.based_on.val() && t.filters.based_on.val() !== "null" && t.filters.based_on.val() !== "") {
            var daterange = t.composeDateRange();
            if (daterange.length) {
                t.config.other_filters.daterange = daterange;
            }
        }

        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        t.updateFilterBadge();
    };

    // ─── FILTER MODAL EVENTS ─────────────────────────────────────────────────────
    t.openFilterModal = function(e) {
        e.preventDefault();
        var modalEl = document.getElementById("failedJobsFilterModal");
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        modalEl.addEventListener("shown.bs.modal", function() {
            t.initDateRangePicker();
            t.syncDateRangePicker();
        }, { once: true });
    };

    t.applyFilters = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        var modalInstance = bootstrap.Modal.getInstance(document.getElementById("failedJobsFilterModal"));
        if (modalInstance) modalInstance.hide();
    };

    // ─── CLEAR FILTERS ──────────────────────────────────────────────────────────
    // FIXED: set based_on to "null" (string) to match old code
    t.btnClrFilter = function() {
        t.config.other_filters = { based_on: "null" };
        t.filters.based_on.val("null").trigger("change");
        t.clearDateRange();
        t.updateFilterBadge();
        t.searchbox.val("");
        t.dTbl.ajax.reload();
        var modalInstance = bootstrap.Modal.getInstance(document.getElementById("failedJobsFilterModal"));
        if (modalInstance) modalInstance.hide();
    };

    // ─── SEARCH / RELOAD ──────────────────────────────────────────────────────────
    t.tableSearch = function(e) {
        e.preventDefault();
        t.dTbl.ajax.reload();
    };

    t.reload = function(e) {
        if (e) e.preventDefault();
        t.dTbl.ajax.reload();
    };

    t.changePageLength = function(e) {
        var length = parseInt(t.pageLength.val(), 10);
        if (e) e.preventDefault();
        if (length > 0) t.dTbl.page.len(length).draw(false);
    };

    // ─── EXPORT ──────────────────────────────────────────────────────────────────
    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location.href = t.config.url.export_failed_jobs + "?q=" + t.config.export_filters;
    };

    // ─── DATATABLE ──────────────────────────────────────────────────────────────
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        stateSave: true,
        lengthChange: false,
        searching: false,
        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        dom: 't<"row"<"col-sm-6"i><"col-sm-6"p>>',
        aoColumnDefs: [
            { targets: 0, bSortable: true },
            { targets: 1, bSortable: false },
            { targets: 2, bSortable: true }
        ],
        order: [[2, "desc"]],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.failed_jobs,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = t.searchbox.val() || '';
                // Send filters exactly as old code: a single 'filters' object
                d.filters = t.config.other_filters || { based_on: "null" };
            }
        },
        columns: [
            { data: "payload" },
            { data: "exception" },
            { data: "failed_at" }
        ]
    });

    // ─── SELECT2 INIT ────────────────────────────────────────────────────────────
    var select2Opts = { width: "100%" };
    t.filters.based_on.select2($.extend({}, select2Opts, {
        placeholder: t.tr("filter_by_date", "Filter by date"),
        dropdownParent: t.filters.wrapper,
        allowClear: true
    }));

    // ─── EVENT BINDINGS ───────────────────────────────────────────────────────────
    t.content.on("click", ".btn-open-filter", $.proxy(t.openFilterModal, t));
    t.content.on("click", ".btn-filter", $.proxy(t.applyFilters, t));
    t.content.on("click", ".btn-clear-filter", $.proxy(t.btnClrFilter, t));
    t.content.on("click", ".btn-searchbox", $.proxy(t.tableSearch, t));
    t.content.on("click", ".btn-reload-list", $.proxy(t.reload, t));
    t.content.on("click", ".btn-download", $.proxy(t.download, t));
    t.content.on("keyup", ".searchbox", function(e) {
        if (e.keyCode === 13) {
            t.tableSearch(e);
        }
    });
    t.content.on("change", ".failed-jobs-page-length", $.proxy(t.changePageLength, t));

    // ─── INITIAL LOAD ────────────────────────────────────────────────────────────
    t.updateFilterBadge();
    t.dTbl.ajax.reload();
};