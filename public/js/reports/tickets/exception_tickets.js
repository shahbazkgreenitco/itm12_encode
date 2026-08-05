var ExceptionTickets = function(config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find("#lgTbl");
    t.tableCover = t.table.closest(".gtable-cover");
    t.lg = t.tab.find("#lg");
    t.pageBtmSummary = t.tab.find("#page-btm-summary");
    t.searchbox = t.tab.find(".searchbox");
    t.searchbtn = t.tab.find(".btn-searchbox");
    t.reload = t.tab.find(".btn-reload");
    t.export = t.content.find(".btn-download");
    t.export_pdf = t.content.find(".btn-download-pdf");
    t.perPage = 10;
    t.pageLimiter = t.tab.find("#pageLimiter");
    t.dataTable = null;

    // ========== Helper Functions ==========
    t._s = function(v) { return v || ""; };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function() {
        var v = $.trim(t.searchbox.val());
        t.config.search = v || "";
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    // ========== Search Function ==========
    t.search = function(e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = $.trim(t.searchbox.val());
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            if (t.dataTable) t.dataTable.draw();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            if (t.dataTable) t.dataTable.draw();
        }
    };

    // ========== Reset Function ==========
    t.reset = function() {
        t.perPage = parseInt(t.pageLimiter.val()) || 10;
        if (t.dataTable) {
            t.dataTable.page.len(t.perPage);
        }
    };

    // ========== Init DataTable ==========
    t.initDataTable = function() {
        t.dataTable = t.table.DataTable({
            autoWidth: false,
            colReorder: true,
            stateSave: true,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
            lengthChange: false,
            searching: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [[5, 'desc']],
            ajax: {
                url: config.url.list_tickets,
                type: 'POST',
                data: function(d) {
                    d.search = t.config.search || '';
                    d.filters = t.config.other_filters || {};
                    d.order_by = t.config.sort_dir.id || 6;
                    d.order_dir = t.config.sort_dir.dir || 2;
                    d.main_filter = t.config.main_filter || '';
                    d.order = d.order || [{ column: 5, dir: 'desc' }];
                    d.draw = d.draw || 1;
                },
                dataSrc: function(json) {
                    if (json && json.data !== undefined) {
                        var total = json.recordsTotal || json.total || 0;
                        var filtered = json.recordsFiltered || json.filtered || 0;
                        if (filtered != total) {
                            t.pageBtmSummary.html("Available " + filtered + " records (filtered from " + total + " total records)");
                        } else {
                            t.pageBtmSummary.html("Available Records: " + total);
                        }
                        return json.data || [];
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover.removeClass("gload");
                    t.lg.html('<tr class="no-record-found"><td class="text-center" colspan="6">' + (config.translations.something_wrong || 'Error loading data') + '</td></tr>');
                }
            },
            columns: [
                {
                    data: 'ticket_id',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var url = config.url.ticket_info + '/' + row.exception_ticket_id;
                            return '<a href="' + url + '" target="_blank" class="underline">' + t._s(data) + '</a>';
                        }
                        return data;
                    }
                },
                { data: 'subject' },
                { data: 'mail_from' },
                { data: 'mail_datetime_format' },
                { data: 'reason' },
                { data: 'created_at' }
            ],
            columnDefs: [
                {
                    targets: [5],
                    className: 'text-center'
                }
            ],
            pageLength: t.perPage,
            lengthChange: false,
            ordering: true,
            stateSave: false,
            language: {
                emptyTable: config.translations.no_details || "No records found",
                zeroRecords: config.translations.no_details || "No records found",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            },
            createdRow: function(row, data, dataIndex) {
                $(row).find('td').addClass('b5-text');
            },
            drawCallback: function(settings) {
                var api = this.api();
                var info = api.page.info();
                if (info.recordsDisplay === 0) {
                    t.lg.html('<tr class="no-record-found"><td class="text-center b5-text" colspan="6">' + (config.translations.no_details || 'No records found') + '</td></tr>');
                }
                t.tableCover.removeClass("gload");
            },
            preDrawCallback: function(settings) {
                t.tableCover.addClass("gload");
            }
        });

        // ========== Custom Search ==========
        var api = t.dataTable;
        
        // Search on Enter key
        t.searchbox.off('keyup').on("keyup", function(e) {
            if (e.keyCode == 13) {
                var v = $.trim($(this).val());
                t.config.search = v;
                api.search(v).draw();
            }
        });

        // Search button click
        t.searchbtn.off('click').on("click", function(e) {
            e.preventDefault();
            var v = $.trim(t.searchbox.val());
            t.config.search = v;
            api.search(v).draw();
        });

        // Reload button
        t.reload.off('click').on("click", function(e) {
            e.preventDefault();
            t.reload();
        });

        // Export Excel button
        t.export.off('click').on("click", function(e) {
            e.preventDefault();
            t.download(e);
        });

        // Export PDF button
        t.export_pdf.off('click').on("click", function(e) {
            e.preventDefault();
            t.downloadPDF(e);
        });

        // Page Limiter change
        t.pageLimiter.off('change').on("change", function() {
            var length = parseInt($(this).val()) || 10;
            t.dataTable.page.len(length).draw();
        });
    };

    // ========== Download Excel Function ==========
    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        var search = t.config.search || '';
        window.location = config.url.export_ticket_xls + "?q=" + t.config.export_filters + "&search=" + escape(search);
    };

    // ========== Download PDF Function ==========
    t.downloadPDF = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        var search = t.config.search || '';
        window.location = config.url.export_ticket_pdf + "?q=" + t.config.export_filters + "&search=" + escape(search);
    };

    // ========== Reload Function ==========
    t.reload = function() {
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
    };

    // ========== Load Function ==========
    t.load = function() {
        if (t.dataTable) {
            t.dataTable.draw();
        }
    };

    // ========== Initialize DataTable ==========
    t.initDataTable();
};