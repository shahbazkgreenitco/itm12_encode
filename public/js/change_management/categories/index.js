var Listing = function (config) {
    var t = this;
    t.config = config;
    t.page = $("#main-change-category-list-wrapper");
    t.content = t.page;
    t.table = t.content.find("#mytable");
    t.mdl = t.content.find("#cmCategoryModal");
    t.mdl.title = t.mdl.find(".modal-title");
    t.mdl.frm = t.mdl.find("form");
    t.mdl.frm.company_id = t.mdl.find("#company_id");
    t.mdl.frm.department_id = t.mdl.find("#department_id");
    t.mdl.frm.description = t.mdl.find("#description");
    t.mdl.frm.name = t.mdl.find("#name");
    t.mdl.frm.cab_id = t.mdl.find("#cab_id");
    t.mdl.frm.button = t.mdl.find("#btnSubmit");
    t.actionStyleScope = t.content.find(".js-change-category-list-view-panel").first();
    if (t.actionStyleScope.length) {
        t.actionStyleScope.attr("id", "main-role-permission-wrapper");
    }

    t._showMainModal = function () {
        var el = document.getElementById('cmCategoryModal');
        if (!el) return;
        var instance = bootstrap.Modal.getInstance(el);
        if (!instance) {
            instance = new bootstrap.Modal(el, { backdrop: 'static', keyboard: false });
        }
        instance.show();
    };
    t._hideMainModal = function () {
        var el = document.getElementById('cmCategoryModal');
        if (!el) return;
        var instance = bootstrap.Modal.getInstance(el);
        if (instance) instance.hide();
    };

    t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;
        if (!row.length) {
            return $();
        }
        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }
        return wrap;
    };

    t.updateValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }
        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };

    // FILTER MODULE
    t.filterModal = $("#cmCategoryFilter");
    t.filterDept = t.filterModal.find("#filter_by_department_id");
    t.filterDateType = t.filterModal.find("#filter_by_date");
    t.filterDateRange = t.filterModal.find("#daterange");
    t.filterApply = t.filterModal.find("#applyFilter");
    t.searchIcon = t.page.find('.amg-list-searchbar__icon');
    t.filterClear = t.filterModal.find("#clearFilter");

    t._showFilterModal = function () {
        var el = document.getElementById('cmCategoryFilter');
        if (!el) return;
        var instance = bootstrap.Modal.getInstance(el);
        if (!instance) {
            instance = new bootstrap.Modal(el, { backdrop: 'static', keyboard: false });
        }
        instance.show();
    };
    t._hideFilterModal = function () {
        var el = document.getElementById('cmCategoryFilter');
        if (!el) return;
        var instance = bootstrap.Modal.getInstance(el);
        if (instance) instance.hide();
    };

    t.cache_filter_values = function () {
        var v = $.trim(t.searchInput.val());
        t.config.search = v;
        t.config.other_filters = {};

        if (t.filterDept.val()) {
            t.config.other_filters.department_id = t.filterDept.val();
        }

        if (t.filterDateType.val() && t.filterDateType.val() !== 'null') {
            t.config.other_filters.based_on = t.filterDateType.val();

            if (t.filterDateRange.val()) {
                t.config.other_filters.daterange = t.filterDateRange.val();
            }
        }   
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));

        filterCount(t.config.other_filters, t.filterDateType.val(), false);
    };
    

    function resetDateRangeFilter() {
        if (typeof $('#reportrange').data('daterangepicker') !== 'undefined') {
            var dp = $('#reportrange').data('daterangepicker');
            var start = moment().subtract(29, 'days');
            var end = moment();
            dp.setStartDate(start);
            dp.setEndDate(end);
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#daterange').val('');
        } else {
            $('#reportrange span').html('');
            $('#daterange').val('');
        }
    }

    // CRUD
    t.addCategories = function (e) {
        e.preventDefault();
        t.mdl.title.text(t.config.translations.add_catgory);
        t.mdl.frm.attr('action', config.url.add);
        t.resetForm();
        t.prefillDefaultCompany();
        t.mdl.frm.button.text(t.config.translations.create);
        t._showMainModal();
    };

    t.editStatus = function (e) {
        var id = $(this).attr('data-id');
        t.mdl.title.text(t.config.translations.edit_catgory);
        t.resetForm();
        t.mdl.frm.attr('action', t.config.url.update_category + "/" + id);
        $.ajax({
            url: t.config.url.edit,
            type: 'POST',
            data: { id: id },
            success: function (data) {
                if (data.status == "success") {
                    t.mdl.frm.company_id.empty();
                    if (data.company) {
                        t.mdl.frm.company_id.append(new Option(data.company.text, data.company.id, true, true)).trigger('change', [true]);
                    }
                    t.mdl.frm.name.val(data.data.name);
                    t.mdl.frm.cab_id.empty();
                    if (data.department) {
                        t.mdl.frm.department_id.append(new Option(data.department.name, data.department.id, true, true));
                    }
                    if (data.data.cab_id != null) {
                        t.mdl.frm.cab_id.append(new Option(data.data.cab_name, data.data.cab_id, true, true));
                    }
                    t.mdl.frm.description.val(data.data.description).summernote('code', data.data.description);
                    t.mdl.frm.button.text(t.config.translations.update);
                    t._showMainModal();
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
    };

    t.deleteCategory = function (e) {
        e.preventDefault();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + id;
        sweetAlertConfirmation({
            message: config.translations.are_you_delete_problemcategory,
            onConfirm: function () {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.reload();
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });
                http.fail(function () {
                    var data = { 'msg': config.translations.something_went_wrong };
                    sweetAlert('center', 'error', data);
                });
                http.always(function () { t.httpCall = true; });
            }
        });
    };

    // ========== SUBMIT WITH MANUAL VALIDATION FOR DESCRIPTION ==========
    t.submitCategory = function (e) {
        e.preventDefault();
        let s = t.mdl.frm.description.val();
        count = s.replaceAll("&nbsp;", "").trim();
        if (t.frmValidator.form() == false) {
            if(count.length <= 0) {
                $('#shows_error').html('This field is required.');
                $('#shows_error').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
            }
            return false;
        }

        // 4. Submit
        t.mdl.frm.button.prop('disabled', true);
        var frmData = new FormData(t.mdl.frm[0]);
        var http = $.ajax({
            url: t.mdl.frm.attr('action'),
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.frm.button.removeAttr('disabled', true);
                    setTimeout(function () {
                        t._hideMainModal();
                        t.reload();
                    }, 800);
                } else {
                    t.mdl.frm.button.removeAttr('disabled', true);
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = { 'msg': config.translations.something_went_wrong };
            t.mdl.frm.button.removeAttr('disabled', true);
            sweetAlert('center', 'error', data);
        });
    };

    t.resetForm = function () {
        t.frmValidator.resetForm();
        t.mdl.frm.find(".amg-form-error-wrap").empty();
        t.mdl.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.mdl.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.mdl.frm.find(".amg-form-field-error").removeClass("amg-form-field-error");
        t.mdl.frm.name.val('');
        t.mdl.frm.company_id.val('').empty().trigger('change', [true]);
        t.mdl.frm.department_id.val('').empty().trigger('change');
        // Reset description
        t.mdl.frm.description.val('').summernote('code', '');
        t.mdl.frm.cab_id.val('').empty().trigger('change');
        // Clear manual error div
        $('.description').html('');
    };

    // Action button helpers
    t.icons = {
        edit: function () {
            return '<svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>';
        },
        delete: function () {
            return '<svg viewBox="0 0 15 17" fill="none"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.55027 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/></svg>';
        }
    };

    t.actionButtonHtml = function (label, classes, id, iconMarkup, dataEditable) {
        var safeLabel = t.escapeHtml ? t.escapeHtml(label) : label;
        var safeId = t.escapeHtml ? t.escapeHtml(id) : id;
        var safeClasses = t.escapeHtml ? t.escapeHtml(classes || "") : classes || "";
        var safeEditable = dataEditable == null ? "" : (t.escapeHtml ? t.escapeHtml(dataEditable) : dataEditable);
        return [
            '<button type="button" class="user-list-action-btn role-list-action-btn ', safeClasses,
            '" data-id="', safeId, '"',
            safeEditable.length ? ' data-editable="' + safeEditable + '"' : '',
            ' title="', safeLabel, '" aria-label="', safeLabel, '">',
            iconMarkup,
            '</button>'
        ].join("");
    };

    t.renderActionButtons = function (record, type) {
        if (type && type !== "display") {
            return record && record.id ? record.id : "";
        }
        if (!record || record.id == null) {
            return '<span class="user-list-empty">-</span>';
        }
        var buttons = [];
        buttons.push(t.actionButtonHtml(
            config.translations.edit_catgory || "Edit",
            "dtActEdit",
            record.id,
            t.icons.edit(),
            record.is_editable
        ));
        buttons.push(t.actionButtonHtml(
            config.translations.Delete || "Delete",
            "dtActDel is-delete",
            record.id,
            t.icons.delete()
        ));
        return ['<div class="user-list-actions role-list-actions justify-content-start">', buttons.join(""), '</div>'].join("");
    };

    t.escapeHtml = function (value) {
        return String(value == null ? "" : value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#39;");
    };

    // DataTable
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            targets: 7,
            bSortable: false,
            render: function (data, type, row) {
                return t.renderActionButtons(row, type);
            }
        }],
        order: [[6, 'desc']],
        processing: true,
        serverSide: true,
        searching: false,
        lengthChange: false,
        deferLoading: 0,
        ajax: {
            url: t.config.url.ajaxCategoryList,
            type: "post",
            data: function (d) {
                d.search = t.config.search;
                d._token = t.config.token;
                d.filters = t.config.other_filters;
                d.main_filter = t.config.main_filter;
            }
        },
        columns: [
            {
                data: 'id',
                render: function (data, type, row) {
                    return `<a href="${config.url.cr}?category=${data}" id="link-${data}" target="_blank">${data}</a>`;
                }
            },
            { data: 'name' },
            { data: 'company_name' },
            { data: 'cab_name' },
            { data: 'deprt_name' },
            { data: 'created_at' },
            { data: 'updated_on' },
            { data: null }
        ],
    });

    // SEARCH & PAGE LENGTH
    t.searchInput = t.content.find(".change-category-search");
    t.pageLength = t.content.find(".change-category-page-length");
    t.refreshBtn = t.content.find(".btn-reload-list");
    t.exportBtn = t.content.find(".btn-export-categories");
    t.addBtn = t.content.find(".btn-add-type");

    t.reload = function () {
        var v = $.trim(t.searchInput.val());
        if (v === false) {
            t.config.search = "";
            t.dTbl.ajax.reload();
            return;
        }
        t.config.search = v;
        t.dTbl.ajax.reload();
    };

    t.download = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.export_categories + "?q=" + t.config.export_filters;
    };

   

    // Note: we remove 'required' and custom rule for description – we handle it manually.
    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            name: { required: true },
            company_id: { required: true },
            department_id: { required: true },
            cab_id: { required: true },
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.insertAfter(element);
            }
            t.updateValidationState(element, true);
        },
        highlight: function (element) {
            t.updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            t.updateValidationState($(element), false);
        }
    });

    // ================== SUMMERNOTE INIT ==================
    t.mdl.frm.description.summernote({
        placeholder: config.translations.comment_summer || 'Enter description',
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']]
        ],
        minHeight: 200,
        focus: true,

    });

    // ================== SELECT2 INIT ==================
    var select2Opts = { width: "100%" };

    t.prefillDefaultCompany = function () {
        if (Array.isArray(t.config.company_defulte) && t.config.company_defulte.length === 1 && t.config.company_defulte[0].id) {
            t.mdl.frm.company_id
                .empty()
                .append(new Option(t.config.company_defulte[0].text, t.config.company_defulte[0].id, true, true))
                .trigger('change', [true]);
        }
    };

    t.mdl.frm.company_id.select2($.extend({}, select2Opts, {
        dropdownParent: t.mdl.frm.company_id.parent(),
        placeholder: t.config.translations.select_company || "Select Company",
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text && data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        }
    })).on("change", function (e, skipClear) {
        if (skipClear) {
            return;
        }
        t.mdl.frm.department_id.val('').empty().trigger('change');
        t.mdl.frm.cab_id.val('').empty().trigger('change');
    });

    t.mdl.frm.department_id.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.mdl.frm.company_id.val()
                };
            },
            delay: 300
        },
        allowClear: true,
        dropdownParent: t.mdl,
        placeholder: config.translations.select_the_department
    }));

    t.filterDept.select2($.extend({}, select2Opts, {
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            data: function (p) { return { search: p.term, page: p.page || 1 }; },
            delay: 300
        },
        dropdownParent: t.filterModal,
        allowClear: true,
        placeholder: config.translations.filter_by_department
    }));

    t.filterDateType.select2($.extend({}, select2Opts, {
        dropdownParent: t.filterModal
    }));

    t.mdl.frm.cab_id.select2($.extend({}, select2Opts, {
        ajax: {
            url: config.url.fetchCAB,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                    company_id: t.mdl.frm.company_id.val()
                };
            },
            delay: 300
        },
        allowClear: true,
        dropdownParent: t.mdl,
        placeholder: config.translations.Select_CAB
    }));

    // ================== EVENTS ==================
    t.mdl.frm.find("input, select").on("input change blur", function () {
        var element = $(this);
        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    t.pageLength.on('change', function () {
        var length = $(this).val();
        t.dTbl.page.len(length).draw();
    });

    var searchTimeout;
    t.searchInput.on('keydown', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            clearTimeout(searchTimeout);
            t.reload();
        }
    });
    t.searchIcon.on('click', function (e) {
        clearTimeout(searchTimeout);
        t.reload();
    });

    t.refreshBtn.on('click', function (e) {
        e.preventDefault();
        t.reload();
    });

    t.exportBtn.on('click', function (e) {
        t.download(e);
    });

    t.addBtn.on('click', function (e) {
        t.addCategories(e);
    });

    // --- Filter modal events ---
    t.filterApply.on('click', function (e) {
        e.preventDefault();
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        t._hideFilterModal();
    });

    t.filterClear.on('click', function (e) {
        e.preventDefault();
        t.filterDept.val(null).trigger('change');
        t.filterDateType.val('null').trigger('change');
        resetDateRangeFilter();
        t.config.other_filters = {};
        t.config.search = '';
        t.searchInput.val('');
        t.cache_filter_values();
        t.dTbl.ajax.reload();
        t._hideFilterModal();
    });

    $(document).on('click', '.btn-open-filter', function (e) {
        e.preventDefault();
        t._showFilterModal();
    });

    // --- Date range picker initialisation on modal show ---
    t.filterModal.on('shown.bs.modal', function () {
        if (typeof moment !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined' && !$('#reportrange').data('daterangepicker')) {
            var start = moment().subtract(29, 'days');
            var end = moment();
            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#daterange').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
            }
            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);
            cb(start, end);
        }
    });

    // --- Modal hidden reset ---
    t.mdl.on('hidden.bs.modal', function () {
        t.resetForm();
    });

    t.table.on("click", ".dtActEdit", $.proxy(t.editStatus));
    t.table.on("click", ".dtActDel", $.proxy(t.deleteCategory));

    t.mdl.frm.on('click', '#btnSubmit', $.proxy(t.submitCategory));

    // INITIAL LOAD
    t.cache_filter_values();
    t.dTbl.ajax.reload();
};