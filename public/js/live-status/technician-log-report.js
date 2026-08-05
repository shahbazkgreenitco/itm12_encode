class MyApp {
    constructor(config) {
        this.config = config || {};
        this.translations = this.config.translations || {};
        this.table = null;
        this.isAllExpanded = false;
        this.searchKeyword = "";
        this.fromDate = "";
        this.toDate = "";
        this.defaultColumns = ["date", "start_time_format", "end_time_format", "work", "break", "total"];

        $(document).ready(() => this.init());
    }

    tr(key, fallback) {
        return this.translations[key] || fallback;
    }

    escapeHtml(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    init() {
        this.buildMainTable();
        this.getCurrentStatus();
        this.initFilterModal();
        this.initFilterActions();
        this.initExport();
        this.initToggleAll();
    }

    initFilterActions() {
        $("#btnTechFilter").off("click.techLog").on("click.techLog", () => {
            this.searchKeyword = $("#techLogSearch").val().trim();
            this.table.draw();
        });

        $("#btnTechClearFilter, #modalFilterClear").off("click.techLog").on("click.techLog", () => {
            this.searchKeyword = "";
            this.fromDate = "";
            this.toDate = "";
            $("#techLogSearch").val("");
            $("#daterange, #daterange_modal").val("");
            $("#modalDateRange span").html("");
            const placeholder = this.tr("select_date_range", "Select Date Range");
            $("#modalDateRange span").html(placeholder).addClass("text-muted");
            $(".filter-count-badge").addClass("d-none").text("0");
            this.table.search("").draw();
            this.hideFilterModal();
        });

        $("#logPageLength").off("change.techLog").on("change.techLog", () => {
            this.table.page.len(parseInt($("#logPageLength").val(), 10) || 10).draw();
        });

        // if ($.fn.select2) {
        //     $("#logPageLength").select2({
        //         width: "resolve",
        //         minimumResultsForSearch: Infinity
        //     });
        // }
    }

    buildMainTable() {
        if ($.fn.DataTable.isDataTable("#mainTable")) {
            $("#mainTable").DataTable().destroy();
        }

        this.table = $("#mainTable").DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: true,
            ordering: true,
            pageLength: parseInt($("#logPageLength").val(), 10) || 10,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            language: {
                emptyTable: this.tr("no_records_found", "No records found"),
                processing: this.tr("processing", "Processing..."),
                zeroRecords: this.tr("no_matching_records", "No matching records found")
            },
            ajax: {
                url: this.config.urls.logs,
                type: "GET",
                data: d => {
                    d.search_value = this.searchKeyword;
                    d.from_date = this.fromDate;
                    d.to_date = this.toDate;
                    d.order_column = d.order && d.order[0] ? d.order[0].column : 1;
                    d.order_dir = d.order && d.order[0] ? d.order[0].dir : "desc";
                },
                dataSrc: res => res.data || []
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    render: () => '<button type="button" class="tech-log-expand-btn header-icon-btn" aria-label="' + this.escapeHtml(this.tr("expand", "Expand")) + '"><i class="bi bi-chevron-down expand-icon"></i></button>'
                },
                {
                    data: "date",
                    render: d => `<div class="b5-text">${d || this.tr("na", "NA")}</div>`
                },
                {
                    data: "start_time_format",
                    render: d => `<div class="b5-text">${d || this.tr("na", "NA")}</div>`
                },
                {
                    data: "end_time_format",
                    render: d => `<div class="b5-text">${d || this.tr("na", "NA")}</div>`
                },
                {
                    data: "tech_activity_total_log",
                    render: d => `<div class="b5-text">${this.getTotal(d, "Work")}</div>`
                },
                {
                    data: "tech_activity_total_log",
                    render: d => `<div class="b5-text">${this.getTotal(d, "Break")}</div>`
                },
                {
                    data: "tech_activity_total_log",
                    render: d => `<div class="b5-text">${this.getTotal(d, "Total")}</div>`
                }
            ],
            drawCallback: () => {
                this.isAllExpanded = false;
                this.updateToggleAllButton(false);
            },
            fnInitComplete: () => {
                $("#mainTable_filter").remove();
                $("#mainTable_wrapper").removeClass("form-inline");
                this.setupSearchListeners();
            }
        });

        $("#mainTable tbody")
            .off("click.techLog", "td:first-child")
            .on("click.techLog", "td:first-child", e => this.expandRow(e));
    }

    setupSearchListeners() {
        const wrapper = $("#tech-log-report-wrapper");
        let searchTimer = null;

        wrapper.off("keyup.techLog input.techLog", "#techLogSearch").on("keyup.techLog input.techLog", "#techLogSearch", e => {
            window.clearTimeout(searchTimer);
            if (e.keyCode === 13) {
                this.searchKeyword = $(e.target).val().trim();
                this.table.search(this.searchKeyword).draw();
                return;
            }

            searchTimer = window.setTimeout(() => {
                this.searchKeyword = $(e.target).val().trim();
                this.table.search(this.searchKeyword).draw();
            }, 350);
        });

        wrapper.off("click.techLog", ".btn-searchbox").on("click.techLog", ".btn-searchbox", () => {
            this.searchKeyword = $("#techLogSearch").val().trim();
            this.table.search(this.searchKeyword).draw();
        });
    }

    expandRow(event) {
        const cell = $(event.target).closest("td");
        const iconElement = cell.find("i.expand-icon");
        const row = this.table.row(cell.closest("tr"));
        const activities = row.data().tech_activity_log || [];

        if (row.child.isShown()) {
            row.child.hide();
            iconElement.removeClass("bi-chevron-up").addClass("bi-chevron-down");
            this.isAllExpanded = false;
            this.updateToggleAllButton(false);
            return;
        }

        this.table.rows({ page: "current" }).every(function () {
            if (this.child.isShown()) {
                this.child.hide();
                $(this.node()).find(".expand-icon").removeClass("bi-chevron-up").addClass("bi-chevron-down");
            }
        });

        iconElement.removeClass("bi-chevron-down").addClass("bi-chevron-up");
        row.child(this.buildChildRows(activities)).show();
        $(".extra-row").hide();
        $(".show-more").off("click.techLog").on("click.techLog", e => this.toggleExtraRows(e));
    }

    initToggleAll() {
        $(document).off("click.techLogToggle", ".btn-toggle-all").on("click.techLogToggle", ".btn-toggle-all", () => {
            const self = this;

            if (!this.isAllExpanded) {
                this.table.rows({ page: "current" }).every(function () {
                    if (!this.child.isShown()) {
                        const list = this.data().tech_activity_log || [];
                        this.child(self.buildChildRows(list)).show();
                        $(this.node()).find("td:first-child").html('<button type="button" class="tech-log-expand-btn header-icon-btn" aria-label="' + self.escapeHtml(self.tr("collapse", "Collapse")) + '"><i class="bi bi-chevron-up expand-icon"></i></button>');
                    }
                });

                $(".extra-row").hide();
                $(".show-more").off("click.techLog").on("click.techLog", e => this.toggleExtraRows(e));
                this.isAllExpanded = true;
                this.updateToggleAllButton(true);
                return;
            }

            this.table.rows({ page: "current" }).every(function () {
                if (this.child.isShown()) {
                    this.child.hide();
                }
                $(this.node()).find("td:first-child").html('<button type="button" class="tech-log-expand-btn header-icon-btn" aria-label="' + self.escapeHtml(self.tr("expand", "Expand")) + '"><i class="bi bi-chevron-down expand-icon"></i></button>');
            });

            this.isAllExpanded = false;
            this.updateToggleAllButton(false);
        });
    }

    updateToggleAllButton(expanded) {
        const button = $(".btn-toggle-all");
        button.find("i").removeClass("bi-arrows-expand bi-arrows-collapse").addClass(expanded ? "bi-arrows-collapse" : "bi-arrows-expand");
        button.find(".btn-toggle-all-text").text(expanded ? this.tr("collapse_all", "Collapse All") : this.tr("expand_all", "Expand All"));
        button.attr("title", expanded ? this.tr("collapse_all", "Collapse All") : this.tr("expand_all", "Expand All"));
    }

    buildChildRows(list) {
        const rows = list.length ? list.map((activity, index) => `
            <tr class="${index >= 4 ? "extra-row" : ""}">
                <td class= "b5-text">${this.escapeHtml(activity.activity_name || this.tr("na", "NA"))}</td>
                <td class= "b5-text">${this.escapeHtml(activity.activity_comment || this.tr("na", "NA"))}</td>
                <td class= "b5-text">${this.escapeHtml(activity.start_time_format || this.tr("na", "NA"))}</td>
                <td class= "b5-text">${this.escapeHtml(activity.end_time_format || this.tr("na", "NA"))}</td>
                <td class= "b5-text">${this.escapeHtml(this.calculateDuration(activity.start_time, activity.end_time))}</td>
            </tr>
        `).join("") : `
            <tr>
                <td colspan="5" class="text-center">${this.escapeHtml(this.tr("no_records_found", "No records found"))}</td>
            </tr>
        `;

        return `
            <div class="inner-container">
                <table class="inner-table table table-sm w-100">
                    <thead>
                        <tr>
                            <th><h4 class="b2-text">${this.escapeHtml(this.tr("activity", "Activity"))}</h4></th>
                            <th><h4 class="b2-text">${this.escapeHtml(this.tr("reason", "Reason"))}</h4></th>
                            <th><h4 class="b2-text">${this.escapeHtml(this.tr("start", "Start"))}</h4></th>
                            <th><h4 class="b2-text">${this.escapeHtml(this.tr("end", "End"))}</h4></th>
                            <th><h4 class="b2-text">${this.escapeHtml(this.tr("total", "Total"))}<h4></th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
                ${list.length > 4 ? `<button type="button" class="show-more">${this.escapeHtml(this.tr("show_more", "Show More"))} <i class="bi bi-chevron-down"></i></button>` : ""}
            </div>
        `;
    }

    toggleExtraRows(event) {
        const button = $(event.target).closest(".show-more");
        const container = button.closest(".inner-container");
        const rows = container.find(".extra-row");
        const isVisible = rows.is(":visible");

        rows.toggle();
        button.html(this.escapeHtml(isVisible ? this.tr("show_more", "Show More") : this.tr("show_less", "Show Less")) + ' <i class="bi ' + (isVisible ? "bi-chevron-down" : "bi-chevron-up") + '"></i>');
    }

    calculateDuration(start, end) {
        if (!start || !end) {
            return "--";
        }

        const diff = Math.round((new Date(start.replace(" ", "T")) - new Date(end.replace(" ", "T"))) / -60000);
        return this.formatMinutes(diff);
    }

    getTotal(items, type) {
        if (!items || !items.length) {
            return "--";
        }

        const entry = items.find(item => String(item.activity_name || "").toLowerCase() === type.toLowerCase());
        return entry ? (entry.display || this.formatMinutes(entry.minutes || 0)) : "--";
    }

    formatMinutes(minutes) {
        const value = parseInt(minutes || 0, 10);
        if (value <= 0) {
            return "0 " + this.tr("minute_short", "Min");
        }

        const hours = Math.floor(value / 60);
        const mins = value % 60;
        return (hours > 0 ? hours + " " + this.tr("hours", "Hours") + " " : "") + mins + " " + this.tr("minutes", "Mins");
    }

    getCurrentStatus() {
        $.get(this.config.urls.status, res => {
            const isBreak = res.activity_name && res.activity_name.toLowerCase() === "break";
            const isActive = !!res.is_logged_in;
            const statusKey = isActive ? "active" : (isBreak ? "break" : "inactive");
            const statusText = isActive ? this.tr("active", "Active") : (isBreak ? this.tr("break", "Break") : this.tr("inactive", "Inactive"));

            $("#liveStatus .status-text").text(statusText);
            $("#liveStatus .status-dot")
                .removeClass("active-dot inactive-dot break-dot")
                .addClass(statusKey + "-dot");

            $("#liveStatus")
                .removeClass("tech-status-pill--active tech-status-pill--inactive tech-status-pill--break")
                .addClass("tech-status-pill--" + statusKey);
        });
    }

    initFilterModal() {
        if (typeof moment === "undefined" || !$.fn.daterangepicker) {
            return;
        }
        
        const placeholder = this.tr("select_date_range", "Select Date Range");
        $("#modalDateRange span").html(placeholder).addClass("text-muted");
        const start = moment().subtract(6, "months").startOf("day");
        const end = moment().startOf("day");
        const dateRanges = {};
        dateRanges[this.tr("today", "Today")] = [moment(), moment()];
        dateRanges[this.tr("yesterday", "Yesterday")] = [moment().subtract(1, "days"), moment().subtract(1, "days")];
        dateRanges[this.tr("last_7_days", "Last 7 Days")] = [moment().subtract(6, "days"), moment()];
        dateRanges[this.tr("last_30_days", "Last 30 Days")] = [moment().subtract(29, "days"), moment()];
        dateRanges[this.tr("this_month", "This Month")] = [moment().startOf("month"), moment().endOf("month")];
        dateRanges[this.tr("last_month", "Last Month")] = [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")];

        $("#modalDateRange").daterangepicker({
            autoUpdateInput: false,
            startDate: start,
            endDate: end,
            locale: {
                applyLabel: this.tr("apply", "Apply"),
                cancelLabel: this.tr("clear_filter", "Clear Filter"),
                format: "YYYY-MM-DD"
            },
            ranges: dateRanges
        });

        $("#modalDateRange")
            .off("apply.daterangepicker")
            .on("apply.daterangepicker", (event, picker) => {
                const startDate = picker.startDate.format("YYYY-MM-DD");
                const endDate = picker.endDate.format("YYYY-MM-DD");
                $("#modalDateRange span").html(startDate + " - " + endDate);
                $("#daterange_modal").val(startDate + "|" + endDate);
            })
            .off("cancel.daterangepicker")
            .on("cancel.daterangepicker", () => {
                $("#modalDateRange span").html("");
                $("#modalDateRange span").html(placeholder).addClass("text-muted");
                $("#daterange_modal").val("");
            });

        $("#modalFilterSubmit").off("click.techLog").on("click.techLog", () => {
            this.applyDateFilter($("#daterange_modal").val());
        });
    }

    initExport() {
        $(document).off("click.techLogExport", ".btn-download-report").on("click.techLogExport", ".btn-download-report", () => {
            if (!this.config.urls || !this.config.urls.exportReport) {
                return;
            }

            this.searchKeyword = $("#techLogSearch").val().trim();
            $("#exportRangeText").text(this.getExportRangeText());
            this.showExportModal();
        });

        $(document).off("click.techLogExportSubmit", "#exportColumnsSubmit").on("click.techLogExportSubmit", "#exportColumnsSubmit", () => {
            const columns = this.getSelectedExportColumns();

            if (!columns.length) {
                alert(this.tr("select_at_least_one_column", "Select at least 1 column"));
                return;
            }

            this.searchKeyword = $("#techLogSearch").val().trim();
            const query = $.param({
                from_date: this.fromDate,
                to_date: this.toDate,
                search_value: this.searchKeyword,
                columns: columns.join(",")
            });

            const separator = this.config.urls.exportReport.indexOf("?") === -1 ? "?" : "&";
            window.location.href = this.config.urls.exportReport + separator + query;
            this.hideExportModal();
        });
    }

    getSelectedExportColumns() {
        const columns = [];
        $(".tech-export-column:checked").each(function () {
            columns.push($(this).val());
        });
        return columns;
    }

    getExportRangeText() {
        if (this.fromDate && this.toDate) {
            return this.fromDate + " - " + this.toDate;
        }

        return this.tr("default_export_note", "Default last 6 months data will be exported");
    }

    showExportModal() {
        const modalEl = document.getElementById("exportColumnsModal");
        if (modalEl && window.bootstrap && bootstrap.Modal) {
            const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            instance.show();
            return;
        }
        $("#exportColumnsModal").modal("show");
    }

    hideExportModal() {
        const modalEl = document.getElementById("exportColumnsModal");
        if (modalEl && window.bootstrap && bootstrap.Modal) {
            const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            instance.hide();
            return;
        }
        $("#exportColumnsModal").modal("hide");
    }

    applyDateFilter(value) {
        $("#daterange").val(value || "");

        if (value) {
            const parts = value.split("|");
            this.fromDate = parts[0] || "";
            this.toDate = parts[1] || "";
            $(".filter-count-badge").removeClass("d-none").text("1");
        } else {
            this.fromDate = "";
            this.toDate = "";
            $(".filter-count-badge").addClass("d-none").text("0");
        }

        this.table.draw();
        this.hideFilterModal();
    }

    hideFilterModal() {
        const modalEl = document.getElementById("filterModal");
        if (modalEl && window.bootstrap && bootstrap.Modal) {
            const instance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            instance.hide();
            return;
        }
        $("#filterModal").modal("hide");
    }
}
