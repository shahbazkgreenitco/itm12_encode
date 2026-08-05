var TableList = function (config, otherConfig) {
    var t = this;
    t.config = config;
    t.otherConfig = otherConfig;
    t.dataTable = null;
    t.table = $("#mytable");
    t.lg = $("#lg");
    t.searchbox = $("#tableSearch");
    t.reloadBtn = $(".btn-reload-list");
    t.exportBtn = $(".btn-download");
    t.exportPdfBtn = $(".btn-download-pdf");
    
    /* ─── Filter elements ─── */
    t.filterModal = $("#FilterModal");
    t.openFilterBtn = $(".btn-filter-open");
    
    t.filters = {
        data: {
            problem_categories: {}
        },
        wrapper: t.filterModal,
        
        // All filter selects
        non_handler: t.filterModal.find("#filter_non_handler_modal"),
        department: t.filterModal.find("#filter_by_department"),
        problem_category: t.filterModal.find("#filter_by_problem_category"),
        sub_category: t.filterModal.find("#filter_by_sub_category"),
        create_via: t.filterModal.find("#filter_by_create_via"),
        merged_by: t.filterModal.find("#filter_by_merged_by"),
        status: t.filterModal.find("#filter_by_status"),
        handler: t.filterModal.find("#filter_by_handler"),
        priority: t.filterModal.find("#filter_by_priority"),
        ticket_type: t.filterModal.find("#filter_by_ticket_type"),
        based_on: t.filterModal.find("#filter_by_date"),
        daterange: t.filterModal.find("#daterange"),
        creator_logger_type: t.filterModal.find("#filter_based_on_cre_log"),
        creator: t.filterModal.find("#filter_by_creator"),
        
        // Buttons
        btnApply: t.filterModal.find("#btnApplyFilterModal"),
        btnClear: t.filterModal.find("#btnClrFilterModal, #btnClrFilter"),
        btnFilter: t.filterModal.find(".btn-filter"),
        
        // Badge
        filterBadge: $(".filter-count-badge")
    };

    /* ─── Sort elements ─── */
    t.sortWrap = $("#short_wraper");
    t.sortItems = $("#short_items");
    t.sortAction = t.sortWrap.find(".sort-action");
    t.dropdownAction = t.sortWrap.find(".dropdown-action");
    t.sortDirIcon = $("#sortDirectionIcon");

    /* ─── Description modal ─── */
    t.descriptonMdl = $("#descriptionModal");
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    /* ─── Helpers ─── */
    t._s = function (v) { return (v === 0 || v === "0") ? 0 : (v || ""); };
    
    t.escapeHtml = function (str) {
        if (typeof str !== "string") return "";
        return str.replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    };
    
    t.truncateHtml = function (html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) return html;
        return text.substring(0, maxLength);
    };

    /* ─── Modal Helper Functions ─── */
    t.showModal = function ($modal) {
        if ($modal.length === 0) {
            console.warn("Modal element not found in DOM!");
            return;
        }
        
        if ($.fn.modal) {
            $modal.modal("show");
        } else if (window.bootstrap && bootstrap.Modal) {
            var modalEl = $modal[0];
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (!modalInstance) {
                modalInstance = new bootstrap.Modal(modalEl, {
                    backdrop: true,
                    keyboard: true
                });
            }
            modalInstance.show();
        }
    };

    t.hideModal = function ($modal) {
        if ($modal.length === 0) return;
        
        if ($.fn.modal) {
            $modal.modal("hide");
        } else if (window.bootstrap && bootstrap.Modal) {
            var modalEl = $modal[0];
            var modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
    };

    /* ─── Filter Reload Functions (from old code) ─── */
    t.filters.fun = {
        reload_department: function () {
            if (!t.filters.department.length) return;
            $.get(t.config.url.departments_by_company + "/" + t.config.company, function (data) {
                if (typeof data == "object" && data.data && data.data.length > 0) {
                    t.filters.department.empty();
                    $.each(data.data, function (i, k) {
                        t.filters.department.append(new Option(k.name, k.id, false, false));
                    });
                    t.filters.department.trigger("change");
                }
            });
        },
        
        reload_problem_category: function () {
            if (!t.filters.problem_category.length) return;
            var department = t.filters.department.val();
            if (department != "" && department != null && department != "null") {
                $.get(t.config.url.problem_categories_by_company + "/" + department, function (data) {
                    if (typeof data == "object" && data.data && data.data.length > 0) {
                        t.filters.problem_category.empty();
                        $.each(data.data, function (i, k) {
                            t.filters.problem_category.append(new Option(k.name, k.id, false, false));
                            if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
                                t.filters.data.problem_categories["sc" + k.id] = k.sub;
                            }
                        });
                        t.filters.problem_category.trigger("change");
                    }
                });
            } else {
                t.filters.problem_category.empty().trigger("change");
            }
        },
        
        reload_sub_category: function () {
            if (!t.filters.sub_category.length) return;
            var prblm = t.filters.problem_category.val();
            t.filters.sub_category.empty();
            if (prblm != "" && prblm != null && prblm != "null") {
                try {
                    // Handle multiple selection - take first value
                    var prblmId = Array.isArray(prblm) ? prblm[0] : prblm;
                    if (t.filters.data.problem_categories["sc" + prblmId]) {
                        $.each(t.filters.data.problem_categories["sc" + prblmId], function (i, k) {
                            t.filters.sub_category.append(new Option(k.name, k.id, false, false));
                        });
                    }
                } catch (e) {
                    console.error("Error loading sub categories:", e);
                }
            }
            t.filters.sub_category.trigger("change");
        },
        
        reload_status: function () {
            if (!t.filters.status.length || !t.config.status) return;
            t.filters.status.empty();
            $.each(t.config.status, function (i, k) {
                t.filters.status.append(new Option(k.text, k.id, false, false));
            });
            t.filters.status.trigger("change");
        },
        
        reload_priority: function () {
            if (!t.filters.priority.length || !t.config.priority) return;
            t.filters.priority.empty();
            $.each(t.config.priority, function (i, k) {
                t.filters.priority.append(new Option(k.text, k.id, false, false));
            });
            t.filters.priority.trigger("change");
        },
        
        reload_merged_by: function () {
            if (!t.filters.merged_by.length) return;
            // Similar to handler - AJAX based
            t.filters.merged_by.select2($.extend({}, { width: "100%" }, {
                dropdownParent: t.filterModal,
                placeholder: "Select Merged By",
                ajax: {
                    url: t.config.getUserByQuery || t.config.url.get_users,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true
            }));
        },
        
        reload_handler: function () {
            if (!t.filters.handler.length) return;
            t.filters.handler.select2($.extend({}, { width: "100%" }, {
                dropdownParent: t.filterModal,
                placeholder: config.translations.select_assign_to || "Select Handler",
                ajax: {
                    url: t.config.getUserByQuery || t.config.url.get_users,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true
            }));
            t.filters.handler.trigger("change");
        },
        
        reload_creator: function () {
            if (!t.filters.creator.length) return;
            t.filters.creator.select2($.extend({}, { width: "100%" }, {
                dropdownParent: t.filterModal,
                placeholder: "Select Creator/Logger",
                ajax: {
                    url: t.config.getUserByQuery || t.config.url.get_users,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: t.config.company
                        };
                    },
                    delay: 300
                },
                allowClear: true
            }));
        }
    };

    /* ─── Modal Search/Filter Functionality ─── */
    t.initModalSearch = function () {
        t.filterModal.on('keyup', '#modalFilterSearchInput', function () {
            var searchTerm = $(this).val().toLowerCase().trim();
            var visibleCount = 0;
            
            t.filterModal.find('.filter-group').each(function () {
                var $group = $(this);
                var label = $group.find('label').text().toLowerCase();
                var filterName = ($group.data('filter-name') || '').toLowerCase();
                
                if (searchTerm === '' || 
                    label.indexOf(searchTerm) > -1 || 
                    filterName.indexOf(searchTerm) > -1) {
                    $group.removeClass('hide');
                    visibleCount++;
                } else {
                    $group.addClass('hide');
                }
            });
            
            var $hint = t.filterModal.find('#filterSearchHint');
            if (searchTerm === '') {
                $hint.html('<i class="bi bi-info-circle me-1"></i>Type to filter visible options');
            } else {
                $hint.html('<i class="bi bi-check-circle me-1 text-success"></i>' + visibleCount + ' filter(s) found');
            }
        });
        
        t.filterModal.on('click', '#clearModalFilterSearch', function () {
            $('#modalFilterSearchInput').val('');
            t.filterModal.find('.filter-group').removeClass('hide');
            t.filterModal.find('#filterSearchHint').html('<i class="bi bi-info-circle me-1"></i>Type to filter visible options');
            $('#modalFilterSearchInput').focus();
        });
        
        t.filterModal.on('shown.bs.modal', function () {
            setTimeout(function() {
                $('#modalFilterSearchInput').focus();
            }, 300);
        });
        
        t.filterModal.on('hidden.bs.modal', function () {
            $('#modalFilterSearchInput').val('');
            t.filterModal.find('.filter-group').removeClass('hide');
            t.filterModal.find('#filterSearchHint').html('<i class="bi bi-info-circle me-1"></i>Type to filter visible options');
        });
    };

    /* ─── Creator/Logger Toggle ─── */
    t.initCreatorLoggerToggle = function () {
        t.filters.creator_logger_type.on('change', function () {
            var val = $(this).val();
            if (val && val !== 'null') {
                t.filterModal.find('.creatorcover').removeClass('hide');
            } else {
                t.filterModal.find('.creatorcover').addClass('hide');
                t.filters.creator.val('').trigger('change');
            }
        });
    };

    /* ─── Render Sort Dropdown ─── */
    t.renderSortDropdown = function () {
        var $dropdown = t.sortItems;
        var s = t.config.sort_dir || { id: 'created_at', dir: 2 };
        $dropdown.empty();
        
        $.each(otherConfig.sort_fields || [], function (i, d) {
            var isActive = d.id == s.id;
            var dirIcon = '';
            if (isActive) {
                dirIcon = s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down';
            }
            var displayText = d.text.length > 10 ? d.text.substring(0, 15) + '...' : d.text;
            
            $dropdown.append(
                '<li>' +
                    '<a href="javascript:void(0)" class="dropdown-item ' + (isActive ? 'active' : '') + '" ' +
                        'data-id="' + d.id + '" data-bs-toggle="tooltip" title="' + t.escapeHtml(d.text) + '">' +
                        '<span class="like-radio"></span>' +
                        '<span class="flex-grow-1">' + t.escapeHtml(displayText) + '</span>' +
                        (isActive ? '<i class="bi ' + dirIcon + '"></i>' : '') +
                    '</a>' +
                '</li>'
            );
        });
    };

    t.updateSortDirectionIcon = function () {
        var dir = t.config.sort_dir?.dir || 2;
        t.sortDirIcon
            .removeClass("bi-sort-up bi-sort-down")
            .addClass(dir == 1 ? "bi-sort-up" : "bi-sort-down");
    };

    /* ─── Sort Event Handlers ─── */
    t.sortAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.updateSortDirectionIcon();
        t.renderSortDropdown();
        
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
    });

    t.dropdownAction.on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        t.sortItems.toggleClass("show");
    });

    t.sortItems.on("click", ".dropdown-item", function (e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data("id");
        
        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 2;
        }
        
        t.sortItems.removeClass("show");
        t.renderSortDropdown();
        t.updateSortDirectionIcon();
        
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
    });

    t.sortItems.on("click", function (e) { e.stopPropagation(); });
    $(document).on("click", function () { t.sortItems.removeClass("show"); });

    /* ─── Cache Filter Values ─── */
    t.cache_filter_values = function () {
        var v = (t.searchbox.val() || "").trim();
        t.config.search = v;
        t.config.other_filters = {};
        var fo = t.config.other_filters;

        // Helper function to add filter if valid
        var addFilter = function (key, $el) {
            if (!$el || !$el.length) return;
            var val = $el.val();
            if (val !== null && val !== '' && val !== 'null' &&
                !(Array.isArray(val) && val.length === 0)) {
                fo[key] = val;
            }
        };

        addFilter('non_handler', t.filters.non_handler);
        addFilter('department', t.filters.department);
        addFilter('problem_category', t.filters.problem_category);
        addFilter('sub_category', t.filters.sub_category);
        addFilter('create_via', t.filters.create_via);
        addFilter('merged_by', t.filters.merged_by);
        addFilter('status', t.filters.status);
        addFilter('handler', t.filters.handler);
        addFilter('priority', t.filters.priority);
        addFilter('ticket_type', t.filters.ticket_type);

        // Based On selected ho tabhi Date Range add hoga
        if (t.filters.based_on.val() && t.filters.based_on.val() !== "null") {
            addFilter('filter_by_date', t.filters.based_on);
            addFilter('daterange', t.filters.daterange);
        }

        addFilter('creator_logger_type', t.filters.creator_logger_type);
        addFilter('creator', t.filters.creator);

        var jobj = {
            "search": t.config.search,
            "other_filters": t.config.other_filters
        };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        // daterange ko count me ignore karo
        var count = Object.keys(fo).filter(function (key) {
            return key !== "daterange";
        }).length;

        if (count > 0) {
            t.filters.filterBadge.removeClass("d-none").text(count);
        } else {
            t.filters.filterBadge.addClass("d-none").text("0");
        }
        console.log("FO:", fo);
        console.log("Keys:", Object.keys(fo));
        console.log("Count:", Object.keys(fo).filter(function (key) {
            return key !== "daterange";
        }).length);
        if (typeof filterCount === "function") {
            try {
                // filterCount(fo, t.filters.based_on.val(), false);
            } catch (e) { }
        }
    };

    /* ─── Search Handler ─── */
    t.search = function (e) {
        var target = e.target || e.currentTarget;
        
        if (e.keyCode == 13 || (e.type == "click" && target.tagName != "BUTTON")) {
            var v = (t.searchbox.val() || "").trim();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            if (t.dataTable) {
                t.dataTable.ajax.reload();
            }
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            if (t.dataTable) {
                t.dataTable.ajax.reload();
            }
        }
    };

    t.searchbox.on("keypress", t.search);

    /* ─── Initialize DataTable ─── */
    t.initDataTable = function () {
        if ($.fn.DataTable.isDataTable('#mytable')) {
            $('#mytable').DataTable().destroy();
        }

        var columnDefs = [
            { targets: 0, orderable: true },
            { targets: 1, orderable: true },
            { targets: 2, orderable: false },
            { targets: 3, orderable: true },
            { targets: 4, orderable: true },
            { targets: 5, orderable: true },
            { targets: 6, orderable: true },
            { targets: 7, orderable: true },
            { targets: 8, orderable: false },
            { targets: 9, orderable: true },
            { targets: 10, orderable: true },
            { targets: 11, orderable: true },
            { targets: 12, orderable: true },
            { targets: 13, orderable: true },
            { targets: 14, orderable: true },
            { targets: 15, orderable: true },
            { targets: 16, orderable: true }
        ];

        var totalCols = $("#tHeadeRow th").length;
        for (var i = 17; i < totalCols; i++) {
            columnDefs.push({ targets: i, orderable: false });
        }

        t.dataTable = $('#mytable').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            scrollX: true,
            lengthChange: false,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap"i p>',
            searching: false,
            ordering: false,
            pageLength: t.perPage || 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            fixedColumns: {
                leftColumns: 1,
            },
            ajax: {
                url: otherConfig.url,
                type: 'POST',
                data: function (d) {
                    d._token = t.config.token || "";
                    d.search = t.config.search || "";
                    d.main_filter = t.config.main_filter || "";
                    d.filters = t.config.other_filters || {};
                    d.order = t.config.sort_dir || { id: 'created_at', dir: 2 };
                    if (d.order && d.order.length) {
                        var colMap = {
                            0: 'task_id', 1: 'name', 3: 'status', 4: 'priority',
                            5: 'start_date', 6: 'due_date', 7: 'end_date',
                            9: 'assign_to', 10: 'ticket_id', 11: 'ticket_subject',
                            12: 'ticket_department', 13: 'ticket_pc', 14: 'ticket_sc',
                            15: 'task_created_at', 16: 'task_updated_at'
                        };
                        var colIdx = d.order[0].column;
                        var colDir = d.order[0].dir;
                        var sortField = colMap[colIdx] || 'created_at';
                        d.order = {
                            id: sortField,
                            dir: colDir == 'asc' ? 1 : 2
                        };
                    }
                    return d;
                },
                dataSrc: function (json) {
                    if (json.data) {
                        json.recordsTotal = json.total || 0;
                        json.recordsFiltered = json.filtered || json.total || 0;
                        return json.data;
                    }
                    return [];
                }
            },
            columns: t.getColumnDefinitions(),
            columnDefs: columnDefs,
            order: [[0, 'desc']],
            language: {
                processing: '<div class="dot-loader"><span></span><span></span><span></span></div>',
                emptyTable: 'No Details Found',
                zeroRecords: 'No matching records found',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoFiltered: '(filtered from _MAX_ total records)',
                paginate: { first: 'First', last: 'Last', next: 'Next', previous: 'Previous' }
            },
            drawCallback: function (settings) {
                t.cache_filter_values();
                t.attachReadMoreHandlers();
            },
            initComplete: function () {
                $('.dataTables_filter').hide();
            }
        });

        $('#showSelect').on('change', function () {
            var length = parseInt($(this).val(), 10);
            t.dataTable.page.len(length).draw();
        });
    };

    /* ─── Get Column Definitions for DataTable ─── */
    t.getColumnDefinitions = function () {
        return [
            { 
                data: 'task_id',
                render: function (data, type, row) {
                    return '<a href="' + t.config.url.task_info + '/' + t._s(row.task_id) + '" target="_blank" style="color:blue;">#' + data + '</a>';
                }
            },
            { 
                data: 'name',
                render: function (data) {
                    var full = t._s(data);
                    var short = full.length > 20 ? full.substring(0, 20) + "..." : full;
                    return '<span class="fixed-ellipsis" title="' + t.escapeHtml(full) + '">' + t.escapeHtml(short) + '</span>';
                }
            },
            { 
                data: 'description',
                render: function (data) {
                    var desc = t._s(data);
                    if (desc.length > 30) {
                        var truncated = t.truncateHtml(desc, 30);
                        return t.escapeHtml(truncated) +
                            '<a class="read-more" style="cursor:pointer; color:blue; margin-left:4px;" data-full-text="' + t.escapeHtml(desc) + '">...Read More</a>';
                    }
                    return t.escapeHtml(desc);
                }
            },
            { data: 'status' },
            { data: 'priority' },
            { data: 'start_date' },
            { data: 'due_date' },
            { data: 'end_date' },
            { data: 'cost', render: function (data) { return t._s(data); } },
            { data: 'assign_to', render: function (data) { return t.escapeHtml(t._s(data)); } },
            { 
                data: 'ticket_id',
                render: function (data, type, row) {
                    return '<a href="' + t.config.url.ticket_info + '/' + t._s(data) + '" target="_blank" style="color:blue;">#' + data + '</a>';
                }
            },
            { 
                data: 'ticket_subject',
                render: function (data) {
                    var full = t._s(data);
                    var short = full.length > 20 ? full.substring(0, 20) + "..." : full;
                    return '<span class="fixed-ellipsis" title="' + t.escapeHtml(full) + '">' + t.escapeHtml(short) + '</span>';
                }
            },
            { data: 'ticket_department' },
            { data: 'ticket_pc' },
            { data: 'ticket_sc' },
            { data: 'task_created_at' },
            { data: 'task_updated_at' }
        ];
    };

    /* ─── Attach Read More Handlers ─── */
    t.attachReadMoreHandlers = function () {
        if (t.descriptonMdl.length) {
            $(document).off('click', '.read-more').on('click', '.read-more', function (e) {
                e.preventDefault();
                var fullText = $(this).data('full-text');
                t.descriptonMdl.body.html(fullText);
                t.showModal(t.descriptonMdl);
            });
        }
    };

    /* ─── Apply Filters ─── */
    t.applyFilters = function () {
        t.cache_filter_values();
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
        t.hideModal(t.filterModal);
    };

    /* ─── Clear Filters ─── */
    t.clearFilters = function () {
        if (t.filters.non_handler.length) t.filters.non_handler.val('null').trigger('change');
        if (t.filters.department.length) t.filters.department.val('').trigger('change');
        if (t.filters.problem_category.length) t.filters.problem_category.val('').trigger('change');
        if (t.filters.sub_category.length) t.filters.sub_category.val('').trigger('change');
        if (t.filters.create_via.length) t.filters.create_via.val('').trigger('change');
        if (t.filters.merged_by.length) t.filters.merged_by.val('').trigger('change');
        if (t.filters.status.length) t.filters.status.val('').trigger('change');
        if (t.filters.handler.length) t.filters.handler.val('').trigger('change');
        if (t.filters.priority.length) t.filters.priority.val('').trigger('change');
        if (t.filters.ticket_type.length) t.filters.ticket_type.val('').trigger('change');
        if (t.filters.based_on.length) t.filters.based_on.val('null').trigger('change');
        if (t.filters.creator_logger_type.length) t.filters.creator_logger_type.val('null').trigger('change');
        if (t.filters.creator.length) t.filters.creator.val('').trigger('change');
        
        if (typeof resetDateRangeFilter === 'function') {
            resetDateRangeFilter();
        }
        if (t.filters.daterange.length) t.filters.daterange.val('');
        
        // Hide creator section
        t.filterModal.find('.creatorcover').addClass('hide');
        
        t.cache_filter_values();
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
        t.hideModal(t.filterModal);
    };

    /* ─── Download ─── */
    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_task_xls + '?q=' + t.config.export_filters;
    };

    /* ─── Download PDF ─── */
    t.downloadPDF = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        if (t.config.url.export_task_pdf) {
            window.location = t.config.url.export_task_pdf + '?q=' + t.config.export_filters;
        }
    };

    /* ─── Initialize Select2 ─── */
    t.initSelect2 = function () {
        var select2Opts = { width: "100%" };
        
        if ($.fn.select2) {
            // Non-Handler Filter
            if (t.filters.non_handler.length) {
                t.filters.non_handler.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_filter || "Select Filter"
                }));
            }
            
            // Department Filter
            if (t.filters.department.length) {
                t.filters.department.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_department || "Select Department"
                }));
            }
            
            // Problem Category
            if (t.filters.problem_category.length) {
                t.filters.problem_category.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_Problem_Category || "Select Problem Category"
                }));
            }
            
            // Sub Category
            if (t.filters.sub_category.length) {
                t.filters.sub_category.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_Sub_Category || "Select Sub Category"
                }));
            }
            
            // Create Via
            if (t.filters.create_via.length) {
                t.filters.create_via.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: "Select Created Via"
                }));
            }
            
            // Status Filter
            if (t.filters.status.length) {
                t.filters.status.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_status || "Select Status"
                }));
            }
            
            // Priority Filter
            if (t.filters.priority.length) {
                t.filters.priority.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: config.translations.select_Priority || "Select Priority"
                }));
            }
            
            // Ticket Type
            if (t.filters.ticket_type.length) {
                t.filters.ticket_type.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal,
                    placeholder: "Select Ticket Type"
                }));
            }
            
            // Based On Filter
            if (t.filters.based_on.length) {
                t.filters.based_on.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal
                }));
            }
            
            // Creator Logger Type
            if (t.filters.creator_logger_type.length) {
                t.filters.creator_logger_type.select2($.extend({}, select2Opts, { 
                    dropdownParent: t.filterModal
                }));
            }
        }
    };

    /* ─── Reload ─── */
    t.reload = function () {
        t.cache_filter_values();
        if (t.dataTable) {
            t.dataTable.ajax.reload();
        }
    };

    /* ─── Init ─── */
    t.init = function () {
        // Initialize Select2
        t.initSelect2();
        
        // Initialize Modal Search
        t.initModalSearch();
        
        // Initialize Creator/Logger Toggle
        t.initCreatorLoggerToggle();

        // Initialize Sort
        t.renderSortDropdown();
        t.updateSortDirectionIcon();

        // Initialize DataTable
        t.initDataTable();

        // Event Handlers
        t.reloadBtn.on("click", t.reload);
        t.exportBtn.on("click", t.download);
        if (t.exportPdfBtn.length) {
            t.exportPdfBtn.on("click", t.downloadPDF);
        }

        /* ─── Filter Modal Open - Event Delegation ─── */
        $(document).off('click.openFilterBtn').on('click.openFilterBtn', ".btn-filter-open", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (t.filterModal.length === 0) {
                console.warn("Filter modal (#FilterModal) not found in DOM!");
                return;
            }
            t.showModal(t.filterModal);
        });

        // Apply & Clear buttons
        t.filters.btnApply.on("click", t.applyFilters);
        t.filters.btnClear.on("click", t.clearFilters);
        if (t.filters.btnFilter.length) {
            t.filters.btnFilter.on("click", t.applyFilters);
        }

        /* ─── Filter Dependency Events (from old code) ─── */
        // Department change -> reload problem categories
        t.filters.department.on("change", function () {
            t.filters.problem_category.empty().trigger("change");
            t.filters.sub_category.empty().trigger("change");
            t.filters.fun.reload_problem_category();
        });
        
        // Problem category change -> reload sub categories
        t.filters.problem_category.on("change", function () {
            t.filters.fun.reload_sub_category();
        });

        // Initial filter cache
        t.cache_filter_values();
        
        // Load initial data for filters
        t.filters.fun.reload_department();
        t.filters.fun.reload_status();
        t.filters.fun.reload_priority();
        t.filters.fun.reload_handler();
        t.filters.fun.reload_merged_by();
        t.filters.fun.reload_creator();
    };

    t.init();
};

/* ─── Main Wrapper ─── */
var TicketTaskReport = function (config) {
    var t = this;
    t.root = $("#mainContent");

    t.deviceTab = new TableList(config, {
        url: config.url.list_tickets,
        sort_fields: config.sort_fields.task
    });
};