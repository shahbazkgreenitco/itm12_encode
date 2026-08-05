var DuBuHeadUser = function (config) {
    var t = this;
    t.config = config;
    t.content = $("#content-container");
    t.tab = t.content.find("#mainContent");
    t.table = t.tab.find('#mytableDuBuHeadUser');
    t.tableCover = t.table.closest(".gtable-cover");
    t.filterbtn = t.content.find(".btn-open-filter");
    t.dataTable = null;
    t.export = t.content.find(".btn-download");
    t.reloadBtn = t.content.find(".btn-reload");
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");

    // Filters
    t.filters = {
        wrapper: $("#FilterModal")
    };

    // Button references
    t.btn = {};
    t.btn.filter = $(".btn-filter");
    t.btn.clear = $("#btnClrFilter");

    t.tblHelpers = {
        buHead: function() {
            return function(data, type, row) {
                var a = [];
                a.push(row.department_bu_head);
                return a.join('');
            };
        },
        duHead: function () {
            return function (data, type, row) {
                var a = [];
                a.push(row.department_du_head);
                return a.join('');
            };
        }
    };

    // ========== Cache Filter Values ==========
    t.cache_filter_values = function() {
        var v = $.trim($('.searchbox').val());
        t.config.search = v;
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
            order: [[0, 'asc']],
            ajax: {
                url: t.config.url.dubuheaduser,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                    d.search = t.config.search || '';
                    d.filters = t.config.other_filters || {};
                    d.draw = d.draw || 1;
                },
                dataSrc: function(json) {
                    if (json && json.data !== undefined) {
                        var total = json.recordsTotal || json.total || 0;
                        var filtered = json.recordsFiltered || json.filtered || 0;
                        if (filtered != total) {
                            $('#page-btm-summary').html("Available " + filtered + " records (filtered from " + total + " total records)");
                        } else {
                            $('#page-btm-summary').html("Available Records: " + total);
                        }
                        return json.data || [];
                    }
                    return [];
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", error);
                    t.tableCover.removeClass("gload");
                    t.table.find('tbody').html('<tr><td colspan="4" class="text-center">' + config.translations.something_wrong + '</td></tr>');
                }
            },
            columns: [
                { data: 'head' },
                { data: 'department_bu_head' },
                { data: 'department_du_head' },
                { data: 'status' }
            ],
            columnDefs: [
                {
                    targets: [1],
                    render: t.tblHelpers.buHead()
                },
                {
                    targets: [2],
                    render: t.tblHelpers.duHead()
                },
                {
                    targets: [3],
                    orderable: false
                }
            ],
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            stateSave: false,
            language: {
                emptyTable: config.translations.no_records || "No records found",
                zeroRecords: config.translations.no_records || "No records found",
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
                    t.table.find('tbody').html('<tr><td colspan="4" class="text-center b5-text">' + config.translations.no_records + '</td></tr>');
                }
                t.tableCover.removeClass("gload");
            },
            preDrawCallback: function(settings) {
                t.tableCover.addClass("gload");
            }
        });

        // ========== Custom Search ==========
        var api = t.dataTable;
        
        // Search on Enter key - using the existing searchbox
        t.searchbox.on("keyup", function(e) {
            if (e.keyCode == 13) {
                var v = $.trim($(this).val());
                t.config.search = v;
                api.search(v).draw();
            }
        });

        // Search button click
        t.searchbtn.on("click", function(e) {
            e.preventDefault();
            var v = $.trim(t.searchbox.val());
            t.config.search = v;
            api.search(v).draw();
        });

        // Reload button
        t.reloadBtn.on("click", function(e) {
            e.preventDefault();
            t.reload();
        });

        // Export button
        t.export.on("click", function(e) {
            e.preventDefault();
            t.exportData(e);
        });

        // Page Limiter change
        $('#pageLimiter').on("change", function() {
            var length = parseInt($(this).val()) || 10;
            t.dataTable.page.len(length).draw();
        });
    };

    // ========== Reload Function ==========
    t.reload = function() {
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
    };

    // ========== Export Function ==========
    t.exportData = function(e) {
        if (e) e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.dubuheaduserexport + "?q=" + t.config.export_filters;
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