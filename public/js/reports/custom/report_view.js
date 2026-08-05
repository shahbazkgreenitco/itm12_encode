var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');
    t.section = $("#reportMainContent");
    t.table = t.section.find("#mytable");
    t.reportName = t.section.find("#reportName");
    t.export_form = null;
    t.dTbl = null;

    t.httpCall = true;
    t.httpPostPath = "";

    t.btn = {};
    t.btn.download = t.section.find('.btn-download-excel');
    t.tblHelpers = {
    };

    // Set default length if not set
    if (!t.config.length) {
        t.config.length = 10;
    }

    function initializeDataTable(selectedReport) {
        $('#maintable_waraper').hide();

        $.ajax({
            url: t.config.url.view_report + "/" + selectedReport,
            type: 'GET',
            success: function (response) {
                if (t.dTbl) {
                    t.dTbl.clear().destroy();
                }
                t.table.find('thead tr').empty();
                
                response.vd.fields.forEach(function (field) {
                    if (field.field_name.includes('_itm_')) {
                        let formattedFieldName = field.field_name
                            .replace('_itm_', '')
                            .replace(/_/g, ' ')
                            .replace(/\b\w/g, function (char) { return char.toUpperCase(); });

                        t.table.find('thead tr').append('<th><h4>' + formattedFieldName + '</h4></th>');
                    } else {
                        t.table.find('thead tr').append('<th><h4>' + field.field_name + '</h4></th>');
                    }
                });
                
                t.dTbl = t.table.DataTable({
                    autoWidth: false,
                    searching: false,
                    lengthChange: false,
                    scrollX: true,
                    dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
                    aoColumnDefs: [],
                    order: [],
                    processing: true,
                    serverSide: true,
                    pageLength: parseInt(t.config.length) || 10, // Add this
                    ajax: {
                        url: t.config.url.get_report_data,
                        type: "POST",
                        data: function (d) {
                            d._token = t.config.token;
                            d.report_id = selectedReport;
                            d.search = t.config.search || '';
                            d.length = parseInt(t.config.length) || 10;
                            d.start = d.start || 0;
                            d.draw = d.draw || 1;
                        }
                    },
                    columns: response.vd.table_cols.map(function (col) {
                        return { data: col.data };
                    }),
                    createdRow: function(row, data, dataIndex) {
                        $(row).find('td').addClass('b5-text');
                    },
                    fnInitComplete: function (oSettings, json) {
                        var api = this.api();
                        t.table.parent().addClass('table-responsive');
                        $('#maintable_waraper').show();
                    },
                    // Add this to handle draw events
                    drawCallback: function(settings) {
                        // This is called after every draw
                        $('#maintable_waraper').show();
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching report data:", error);
                // Show error message
                showErrorMessage('Failed to load report data. Please try again.');
            }
        });
    }

    // Error message function
    function showErrorMessage(message) {
        var errorHtml = `
            <div class="table-error-message" style="padding: 15px 20px; margin: 10px 0; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; color: #721c24; display: flex; justify-content: space-between; align-items: center;">
                <span>${message}</span>
                <button type="button" style="background: none; border: none; font-size: 20px; cursor: pointer; padding: 0 5px; color: #721c24;" onclick="$(this).parent().remove()">&times;</button>
            </div>
        `;
        $('#maintable_waraper').before(errorHtml);
        setTimeout(function() {
            $('.table-error-message').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    t.reportName.on('change', function () {
        var selectedReport = $(this).val();
        if (selectedReport) {
            // Reset pagination when changing report
            t.config.length = parseInt($('#customReportList').val()) || 10;
            initializeDataTable(selectedReport);
        } else {
            $('#maintable_waraper').hide();
        }
    });

    t.reload = function () {
        var selectedReport = t.reportName.val();
        if (selectedReport) {
            // Preserve current page when reloading
            if (t.dTbl) {
                var currentPage = t.dTbl.page();
                initializeDataTable(selectedReport);
                // Restore page after reload
                setTimeout(function() {
                    if (t.dTbl) {
                        t.dTbl.page(currentPage).draw(false);
                    }
                }, 100);
            } else {
                initializeDataTable(selectedReport);
            }
        } else {
            console.warn("No report selected for reloading.");
        }
    };

    t.export_data = function (e) {
        e.preventDefault();

        if (t.section.find('#export_form').length > 0) {
            t.section.find('#export_form').remove();
        }
        var selectedReport = t.reportName.val();
        t.section.append('<form name="export_form" id="export_form" method="post"></form>');
        t.export_form = t.section.find('#export_form');
        t.export_form.attr("action", t.config.url.export_report_data);
        t.export_form.append('<input type="hidden" name="_token" value="' + t.token.attr("content") + '" />');
        t.export_form.append('<input type="hidden" name="report_id" value="' + selectedReport + '" />');
        t.config.search = $(".searchbox").val();
        if (t.config.search !== null) {
            t.export_form.append('<input type="hidden" name="search" value="' + t.config.search + '" />');
        }
        var order = t.dTbl.order();
        if (typeof order == "object" && order.length > 0) {
            t.export_form.append('<input type="hidden" name="order[0][column]" value="' + t.config.token + '" />');
            t.export_form.append('<input type="hidden" name="order[0][dir]" value="' + t.config.token + '" />');
        }

        t.export_form.submit();
    };

    t.downloadExcel = function (e) {
        e.preventDefault();
        var selectedReport = t.reportName.val();
        t.section.append('<form name="export_form" id="export_form" method="post"></form>');
        t.export_form = t.section.find('#export_form');
        t.export_form.attr("action", t.config.url.export_custom_report);

        t.export_form.append('<input type="hidden" name="_token" value="' + t.token.attr("content") + '" />');
        t.export_form.append('<input type="hidden" name="report_id" value="' + selectedReport + '" />');
        t.config.search = $(".searchbox").val();
        if (t.config.search !== null) {
            t.export_form.append('<input type="hidden" name="search" value="' + t.config.search + '" />');
        }
        var order = t.dTbl.order();
        if (typeof order == "object" && order.length > 0) {
            t.export_form.append('<input type="hidden" name="order[0][column]" value="' + t.config.token + '" />');
            t.export_form.append('<input type="hidden" name="order[0][dir]" value="' + t.config.token + '" />');
        }

        t.export_form.submit();
    }

    t.section.off('keyup', '.searchbox');
    t.section.off('click', '.btn-searchbox');

    t.section.on('keyup', '.searchbox', function (e) {
        if (e.keyCode === 13) {
            var searchValue = $.trim($(this).val());
            t.config.search = searchValue;
            t.reload();
            e.preventDefault();
            return false;
        }
    });

    t.section.on('click', '.btn-searchbox', function (e) {
        var searchValue = $.trim($('.searchbox').val());
        t.config.search = searchValue;
        t.reload();
        e.preventDefault();
    });

    // Fix for length change - properly reload with new length
    $(document).on('change', '#customReportList', function () {
        var selectedLength = parseInt($(this).val()) || 10;
        t.config.length = selectedLength;
        
        // If DataTable exists, just reload with new length
        if (t.dTbl) {
            // Update the page length
            t.dTbl.page.len(selectedLength).draw();
            
            // Also update the AJAX data for next requests
            var ajaxData = t.dTbl.ajax.params();
            if (ajaxData) {
                ajaxData.length = selectedLength;
            }
        } else {
            // If no DataTable exists, reload the whole thing
            t.reload();
        }
    });

    // Initialize select2
    t.reportName.select2({ width: "50%", allowClear: true, placeholder: "Select Report" });
    
    // Event handlers
    t.section.on("click", ".act-reload", $.proxy(t.reload));
    t.section.on("click", ".btn-download", $.proxy(t.export_data));
    t.section.on("click", ".btn-download-excel", $.proxy(t.downloadExcel));

    // Initialize length from dropdown on page load
    setTimeout(function() {
        var initialLength = parseInt($('#customReportList').val()) || 10;
        t.config.length = initialLength;
    }, 100);
};