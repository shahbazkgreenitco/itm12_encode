var MyApp = function (config) {
    var t = this;
    t.config = config;

    t.content = $('.content'); 
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#bulkResolveMdl');
    t.mdl.frm = t.mdl.find('#bulkResolve_ticket');
    t.mdl.frm.ticketID = t.mdl.frm.find('#ticket_id');
    t.mdl.frm.content = t.mdl.frm.find('#content');
    t.mdl.frm.assign_ticket = t.mdl.frm.find('.assign_ticket');
    t.submitBtn = t.mdl.frm.find('#btnBulkResolve');

    // Search & Control Buttons
    t.searchbox = t.content.find(".plain-search");
    t.pageLimiter = t.content.find("#pageLimiter");
    t.btnReload = t.content.find(".btn-reload");
    t.btnDownload = t.content.find(".btn-download");
    t.btnOpenFilter = t.content.find(".btn-open-filter");
    t.btnApplyFilter = t.content.find(".btn-filter");
    t.filterBadge = t.content.find(".filter-count-badge");

    t.filterModal = $('#FilterModal');

    t.filters = {
        status: t.content.find("#filter_by_status"),
        handler: t.content.find("#filter_by_handler"),
        date: t.content.find("#filter_by_based_on"),
        daterange: t.content.find("#daterange")
    };

    var select2Opts = { width: "100%"};
    t.reload_status = function () {
        t.filters.status.empty();
        $.each(config.status, function (i, k) {
            t.filters.status.append(new Option(k.name, k.id, false, false));
        });
        t.filters.status.trigger("change");
    };

    t.reload_status();
    t.filters.status.select2($.extend({}, select2Opts, {dropdownParent: t.filterModal, placeholder: config.translations.select_status || "Select Status"}));
    t.filters.date.select2($.extend({}, select2Opts, {dropdownParent: t.filterModal}));
    
    t.filters.handler.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterModal,
        ajax: {
            url: t.config.url.getUserByQuery,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    status: 0,
                    company_id: t.config.company
                };
            },
            delay: 300
        },
        placeholder: config.translations.select_user
    }));
    
    t.config.other_filters = {};

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        dom: 'rt<"table-footer"ip>', 
        aoColumnDefs: [{'bSortable': false,'aTargets': [0]}, 
        {
            targets: 0,
            render: function (d, type, row) {
                if(jQuery.inArray("InactiveUserReportBulkResolve", t.config.permissions) !== -1) {
                    return '<div class="checkAll"><input type="checkbox" class="checkall row_selector" data-id="'+d+'"></div>';
                } else {
                    return '';
                }
            }
        }, {
            targets: 2,
            render: function (d, type, row) {
                return '<a href="' + t.config.url.ticket_info + "/" + d + '"  target="_blank">' + d + '</a>';
            }
        }],
        order: [[1, 'asc']],
        deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.get_tickets,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.filters = t.config.other_filters;
            },
        },
        columns: [
            { data: 'a.ticket_id', orderable: false, searchable: false },
            { data: 'a.creator' },
            { data: 'a.ticket_id' },
            { data: 'a.status_name' },
            { data: 'a.assigned_to' },
            { data: 'a.created_at' }
        ],
        initComplete: function(oSettings, json) {
            var api = this.api();
            
            $("#lgTbl_length").hide(); 
            $("#lgTbl_filter").hide();

            t.pageLimiter.on('change', function() {
                api.page.len(parseInt($(this).val())).draw();
            });

            t.searchbox.on("keyup", function(e) {
                if (e.keyCode == 13) {
                    api.search(this.value).draw();
                }
            });

            t.content.find('.amg-list-searchbar__icon').on('click', function() {
                api.search(t.searchbox.val()).draw();
            });
        }
    });

    t.cache_filter_values = function() {
        t.config.other_filters = {};
        var activeCount = 0;
        var dateVal = t.filters.date.val();
        var rangeVal = t.filters.daterange.val();
        var hasDateFilter = false;

        if(dateVal && dateVal !== '' && dateVal !== 'null') {
            t.config.other_filters['filter_by_based_on'] = dateVal;
            hasDateFilter = true;
        }

        if(rangeVal && rangeVal !== '') {
            t.config.other_filters['daterange'] = rangeVal;
            hasDateFilter = true;
        }

        if(hasDateFilter) {
            activeCount++;
        }

        $.each(t.filters, function(key, el) {
            if(key === 'daterange' || key === 'date') {
                return;
            }
            
            var val = el.val();
            if(val && val !== '' && val !== 'null' && !(Array.isArray(val) && val.length === 0)) {
                t.config.other_filters['filter_by_' + key] = val;
                activeCount++;
            }
        });

        if(activeCount > 0) {
            t.filterBadge.text(activeCount).removeClass('d-none');
        } else {
            t.filterBadge.addClass('d-none');
        }

        var jobj = { "search": t.searchbox.val(), "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.refresh = function() {
        t.dTbl.ajax.reload();
    }

    t.checkAll = function(e) {
        var isChecked = $(this).prop("checked");
        $('.checkAll').find('input[type="checkbox"]').prop('checked', isChecked);
    }

    t.resetFrmFilter = function() {
        $.each(t.filters, function(key, el) {
            if(key === 'daterange') {
                el.val('');
            } else {
                el.val('null').trigger("change");
            }
        });
        t.cache_filter_values();
        t.dTbl.ajax.reload();
    }

    t.bulk_resolved = function(e) {
        var selected = [];
        $('.checkAll').find(".row_selector").each(function(i, element) {
            if($(element).is(":checked") && !$(element).prop('disabled')) {
                selected.push($(element).attr("data-id"));
            }
        });
        t.mdl.frm.ticketID.val(selected);
        if(selected.length == 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: t.config.translations.select_any_one_ticket
            }); 
            return false;
        }
        t.mdl.frm.assign_ticket.html("");
        $.each(selected, function(i, v) {
            t.mdl.frm.assign_ticket.append("<li>Ticket Id #"+v+"</li>");
        });
        
        t.mdl.frm.content.summernote({
            inheritPlaceholder: true,
            toolbar: [
                ['color', ['color']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']]
            ],
            minHeight: 200,
            focus: true
        });
        t.mdl.modal('show');
    }

    t.btnBulkResolve = function(e) {
        e.preventDefault();
        var s = t.mdl.frm.content.val();
        var count = s.replaceAll("&nbsp;", "").trim();
        
        if(count.length <= 0) {
            $('#bulkResolve_ticket').find('#shows_error').html('This field is required.').css({'color':'#c53030','font-size':'13px'});
            return false;
        }

        t.submitBtn.prop("disabled", true);
        $.ajax({
            method : "POST",
            url : t.config.url.bulk_resolved,
            data : {'content' : t.mdl.frm.content.val(), 'ticket_id' : t.mdl.frm.ticketID.val(), '_token': t.config.token},
            success : function(data) {
                t.submitBtn.prop("disabled" , false);
                if(data.status == 'success') {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal('hide');
                    setTimeout(function() {
                        t.dTbl.ajax.reload();
                    }, 600);
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            error : function(xhr, status, error) {
                t.submitBtn.prop("disabled", false);
                sweetAlert('center', 'error', {
                    status: 'error',
                    message: 'Request failed'
                });
                return false;
            },
        });
    }

    t.btnOpenFilter.on('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('FilterModal'));
        modal.show();
    });

    t.btnApplyFilter.on('click', function() {
        t.cache_filter_values();
        t.refresh();
        bootstrap.Modal.getInstance(document.getElementById('FilterModal')).hide();
    });

    $(document).on('click', '#btnClrFilter', function() {
        t.resetFrmFilter();
        bootstrap.Modal.getInstance(document.getElementById('FilterModal')).hide();
    });

    t.btnReload.on('click', function() { 
        t.refresh(); 
    });

    t.btnDownload.on('click', function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_ticket + "?q=" + t.config.export_filters;
    });

    t.content.on('click', '#checkAll', $.proxy(t.checkAll));
    t.content.on('click', '.bulk_resolved', $.proxy(t.bulk_resolved));
    t.content.on('click', '#btnBulkResolve', $.proxy(t.btnBulkResolve));
    t.dTbl.ajax.reload();
};