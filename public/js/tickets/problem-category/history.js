var ProblemCategory = function(config) {
    var t = this;

    t.config = config || {};
    t.page = $("#main-user-list-wrapper");
    t.table = t.page.find("#mytable");
    t.tableHead = t.table.find("thead");
    t.searchInput = t.page.find(".plain-search");
    t.pageLength = t.page.find(".userModulePageLenth");
    t.btn = {
        reload: t.page.find(".btn-reload-list")
    };
    t.remarks = t.page.find("#remarksModal");
    t.remarks.body = t.remarks.find(".modal-body");
    t.searchTimer = null;

    t.escapeHtml = function(value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    t.safeText = function(value, fallback) {
        var text = $.trim(String(value == null ? "" : value));
        return text === "" || text === "null" || text === "undefined" ? (fallback || "") : text;
    };

    t.toggleModal = function(target, shouldShow) {
        var modal = target && target.jquery ? target : $(target);

        if (!modal.length) {
            return;
        }

        if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
            window.bootstrap.Modal.getOrCreateInstance(modal[0])[shouldShow ? "show" : "hide"]();
        } else if (typeof modal.modal === "function") {
            modal.modal(shouldShow ? "show" : "hide");
        }
    };

    t.renderTextCell = function(value, type, fallback) {
        var text = t.safeText(value, fallback);

        if (type !== "display") {
            return text;
        }

        if (!text) {
            return "";
        }

        return '<span class="b5-text" title="' + t.escapeHtml(text) + '">' + t.escapeHtml(text) + "</span>";
    };

    t.renderProblemCategoryCell = function(record, type) {
        var name = t.safeText(record.pt_name, "-");
        var blocks = [];

        if (type !== "display") {
            return [
                name,
                t.safeText(record.auto_allocation_group, ""),
                t.safeText(record.pab, ""),
                t.safeText(record.form_name, "")
            ].join(" ");
        }

        blocks.push('<div class="d-flex flex-column gap-1">');
        blocks.push('<span class="b5-text" title="' + t.escapeHtml(name) + '">' + t.escapeHtml(name) + "</span>");

        if (record.parent_id) {
            blocks.push('<span class="opacity-60 small">' + t.escapeHtml(t.config.translations.SUB_CATEGORY || "Sub Category") + "</span>");
        }
        if (record.esc_tot) {
            blocks.push('<span class="opacity-60 small">' + t.escapeHtml(record.esc_tot) + " Escalations</span>");
        }
        if (record.approval_required === "Required") {
            blocks.push('<span class="opacity-60 small">Approval: ' + t.escapeHtml(record.approval_required) + "</span>");
            if (t.safeText(record.pab, "")) {
                blocks.push('<span class="opacity-60 small">Authority Board: ' + t.escapeHtml(record.pab) + "</span>");
            }
        }
        if (record.form_id != null && t.safeText(record.form_name, "")) {
            blocks.push('<span class="opacity-60 small">Form Name: ' + t.escapeHtml(record.form_name) + "</span>");
        }
        if (t.safeText(record.auto_allocation_group, "")) {
            blocks.push('<span class="opacity-60 small">Allocation Group: ' + t.escapeHtml(record.auto_allocation_group) + "</span>");
        }

        blocks.push("</div>");
        return blocks.join("");
    };

    t.renderRemarksCell = function(value, type) {
        var plainText;

        if (!value) {
            return "";
        }

        plainText = $('<div class="b5-text text-truncate">').html(value).text();
        if (type !== "display") {
            return plainText;
        }

        if (plainText.length <= 60) {
            return '<div class="b5-text text-truncate">' + value + '</div>';
        }

        return  '<div class="b5-text text-truncate">' + plainText.substring(0, 60) + '... <a href="#" class="read-more" data-full-text="' + t.escapeHtml(value) + '">Read More</a></div>';
    };

    t.columnRegistry = function() {
        return {
            company: {
                key: "company",
                title: t.config.columnLabels.company,
                data: "a.company",
                width: "14%",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            problem_category: {
                key: "problem_category",
                title: t.config.columnLabels.problem_category,
                data: "a",
                className:'amg-table-col-224',
                width: "24%",
                render: function(data, type, row) {
                    return t.renderProblemCategoryCell(row.a || {}, type);
                }
            },
            ticket_attender: {
                key: "ticket_attender",
                title: t.config.columnLabels.ticket_attender,
                data: "a.ticket_attender",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            department: {
                key: "department",
                title: t.config.columnLabels.department,
                data: "a.department",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            parent_category: {
                key: "parent_category",
                title: t.config.columnLabels.parent_category,
                data: "a.pc_name",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "");
                }
            },
            priority: {
                key: "priority",
                title: t.config.columnLabels.priority,
                data: "a.priority",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            tat: {
                key: "tat",
                title: t.config.columnLabels.tat,
                data: "a.tat",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            response_sla: {
                key: "response_sla",
                title: t.config.columnLabels.response_sla,
                data: "a.response_sla",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            workaround_sla: {
                key: "workaround_sla",
                title: t.config.columnLabels.workaround_sla,
                data: "a.workaround_sla",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            close_ticket_after_days: {
                key: "close_ticket_after_days",
                title: t.config.columnLabels.close_ticket_after_days,
                data: "a.close_ticket_after_days",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            reopen_ticket_until_days: {
                key: "reopen_ticket_until_days",
                title: t.config.columnLabels.reopen_ticket_until_days,
                data: "a.reopen_ticket_until_days",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            remarks: {
                key: "remarks",
                title: t.config.columnLabels.remarks,
                data: "a.remarks",
                className: "amg-table-col-264",
                render: function(data, type) {
                    return t.renderRemarksCell(data, type);
                }
            },
            last_updated_at: {
                key: "last_updated_at",
                title: t.config.columnLabels.last_updated_at,
                data: "a.last_updated_at",
                className: "amg-table-col-176",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            },
            updated_by_name: {
                key: "updated_by_name",
                title: t.config.columnLabels.updated_by_name,
                data: "a.updated_by_name",
                render: function(data, type) {
                    return t.renderTextCell(data, type, "-");
                }
            }
        };
    };

    t.buildColumnsMeta = function() {
        var registry = t.columnRegistry();
        var visibleColumns = $.isArray(t.config.visibleColumns) && t.config.visibleColumns.length
            ? t.config.visibleColumns
            : [
                "company",
                "problem_category",
                "ticket_attender",
                "department",
                "parent_category",
                "priority",
                "tat",
                "response_sla",
                "workaround_sla",
                "close_ticket_after_days",
                "reopen_ticket_until_days",
                "remarks",
                "last_updated_at",
                "updated_by_name"
            ];

        return $.map(visibleColumns, function(key) {
            return registry[key] ? $.extend({}, registry[key]) : null;
        });
    };

    t.renderTableHead = function(columnsMeta) {
        var cells = $.map(columnsMeta, function(column) {
            var widthAttr = column.width ? ' width="' + t.escapeHtml(column.width) + '"' : "";
            return "<th" + widthAttr + '><h4 class="b2-text">' + t.escapeHtml(column.title || "") + "</h4></th>";
        });

        t.tableHead.html("<tr>" + cells.join("") + "</tr>");
    };

    t.createDataTableColumns = function(columnsMeta) {
        return $.map(columnsMeta, function(column) {
            var definition = {
                data: column.data
            };

            if (column.width) {
                definition.width = column.width;
            }
            if (column.className) {
                definition.className = column.className;
            }
            if (column.orderable === false) {
                definition.orderable = false;
            }
            if (column.searchable === false) {
                definition.searchable = false;
            }
            if ($.isFunction(column.render)) {
                definition.render = column.render;
            }

            return definition;
        });
    };

    t.findColumnIndex = function(columnsMeta, key, fallbackIndex) {
        var index = -1;

        $.each(columnsMeta, function(i, column) {
            if (column.key === key) {
                index = i;
                return false;
            }
        });

        return index > -1 ? index : fallbackIndex;
    };

    t.columnsMeta = t.buildColumnsMeta();
    t.renderTableHead(t.columnsMeta);

    t.dTbl = t.table.DataTable({
        language: {
            info: `${t.config.datatable_translations.showing} _START_ ${t.config.datatable_translations.to} _END_ ${t.config.datatable_translations.of} _TOTAL_ ${t.config.datatable_translations.records}`,
            infoEmpty: `${t.config.datatable_translations.showing} 0 ${t.config.datatable_translations.to} 0 ${t.config.datatable_translations.of} 0 ${t.config.datatable_translations.records}`,
            emptyTable: t.config.datatable_translations.empty_result,
            zeroRecords: t.config.datatable_translations.empty_result,
            infoFiltered: `(${t.config.datatable_translations.filtered} ${t.config.datatable_translations.from} _MAX_ ${t.config.datatable_translations.total_entries})`,
            paginate: {
                previous: t.config.datatable_translations.prev,
                next: t.config.datatable_translations.next
            }
        },
        autoWidth: false,
        order: [[t.findColumnIndex(t.columnsMeta, "last_updated_at", t.columnsMeta.length - 2), "desc"]],
        processing: true,
        serverSide: true,
        scrollX: true,

        pageLength: parseInt(t.pageLength.val(), 10) || 10,
        lengthChange: false,
        searching: false,
        ajax: {
            url: t.config.url.historyAjax,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.search = $.trim(t.searchInput.val());
                d.category_id = t.config.category_id;
            }
        },
        fixedColumns: {
            leftColumns: 1, rightColumns:1
        },

        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',

        columns: t.createDataTableColumns(t.columnsMeta),

        fnInitComplete: function() {
            if (t.pageLength.length) {
                t.pageLength.val(String(parseInt(t.pageLength.val(), 10) || 10));
            }

            // Hide loader after initial load
            t.table
                .closest('.dataTables_wrapper')
                .find('.dataTables_processing')
                .css('display', 'none');
        },

        drawCallback: function() {
            // Hide loader after every DataTable draw
            t.table
                .closest('.dataTables_wrapper')
                .find('.dataTables_processing')
                .css('display', 'none');
        }
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.handleSearchInput = function() {
        window.clearTimeout(t.searchTimer);
        t.searchTimer = window.setTimeout(function() {
            t.reload();
        }, 250);
    };

    t.page.on("click", ".read-more", function(e) {
        e.preventDefault();
        t.remarks.body.html($(this).data("full-text"));
        t.toggleModal(t.remarks, true);
    });

    t.btn.reload.on("click", $.proxy(t.reload, t));

    t.searchInput.on("input", $.proxy(t.handleSearchInput, t));
    t.searchInput.on("keydown", function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            window.clearTimeout(t.searchTimer);
            t.reload();
        }
    });

    t.pageLength.on("change", function() {
        var length = parseInt($(this).val(), 10) || 10;
        t.dTbl.page.len(length).draw();
    });

    t.dTbl.ajax.reload();
};
