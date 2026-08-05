var Listing = function (config) {
    var t = this;
    t.config = config;
    t.config.sort_dir = t.config.sort_dir || { id: 9, dir: 2 };
    let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
    t.page = $("#page_boxed");
    t.lg = t.page.find("#lg");
    t.pagebtns = t.page.find("#pagebtns");
    t.pageBtmSummary = t.page.find("#page-btm-summary");
    t.searchbox = t.page.find(".acr-search");
    t.export = t.page.find(".btn-download");   
    t.pageLimiter = t.page.find("#pageLimiter");
    t.total = 0;
    t.perPage = 10;
    t.currentPage = 1;
    t.filteredData = [];
    t.totalData = 0;
    t.activeFilters = {};

    t.frm = $('#changeRequestForm');
    t.frmEl = {};
    t.frmEl.company_id = t.frm.find('#company_id');
    t.frmEl.subject = t.frm.find('#subject');
    t.frmEl.change_description = t.frm.find('#change_description');
    t.frmEl.change_requester = t.frm.find('#change_requester');
    t.frmEl.change_manager = t.frm.find('#change_manager');
    t.frmEl.reason_description = t.frm.find('#reason_description');
    t.frmEl.status_id = t.frm.find('#status_id');
    t.frmEl.priority_id = t.frm.find('#priority_id');
    t.frmEl.impact_id = t.frm.find('#impact_id');
    t.frmEl.risk_id = t.frm.find('#risk_id');
    t.frmEl.change_type_id = t.frm.find('#change_type_id');
    t.frmEl.currency = t.frm.find('#currency');
    t.frmEl.cost = t.frm.find('#cost');
    t.frmEl.category_id = t.frm.find('#category_id');
    t.frmEl.change_implementer = t.frm.find('#change_implementer');
    t.frmEl.change_reviewer = t.frm.find('#change_reviewer');
    t.frmEl.scheduledStartDate = t.frm.find('#scheduled_start_date');
    t.frmEl.scheduledEndDate = t.frm.find('#scheduled_end_date');
    t.frmEl.risk_description = t.frm.find('#risk_description');
    t.frmEl.impact_description = t.frm.find('#impact_description');
    t.frmEl.rollout_plan = t.frm.find('#rollout_plan');
    t.frmEl.fallback_plan = t.frm.find('#fallback_plan');
    t.frmEl.tmp_id = t.frm.find('#tmp_id');
    t.frmEl.downtime_id = t.frm.find('#downtime_id');
    t.frmEl.start_down_time = t.frm.find('#start_down_time');
    t.frmEl.end_down_time = t.frm.find('#end_down_time');
    t.frmEl.attachment_id = t.frm.find('#attachment_id');
    t.frmEl.attachment_dropper_cover = t.frm.find("#attachment-dropper-cover");
    t.frmEl.attachment_dropper = t.frmEl.attachment_dropper_cover.find("#attachment-dropper");
    if (t.config.client === "safari") {
        t.frmEl.supplier_id = t.frm.find('#supplier_id');
        t.frmEl.po_number = t.frm.find('#po_number');
        t.frmEl.po_date = t.frm.find('#po_date');
    }

    if (t.config.client === "ltts") {
        t.frmEl.device_id = t.frm.find('#device_id');
        t.frmEl.ticket_id = t.frm.find('#ticket_id');
        t.frmEl.ticket_subcategory_id = t.frm.find('#ticket_subcategory_id');
        t.frmEl.ticket_category_id = t.frm.find('#ticket_category_id');
        t.frmEl.ticket_department_id = t.frm.find('#ticket_department_id');
        t.frmEl.select_all = t.frm.find('#selectAllTickets');
        t.data = {};
        var ticket_ids = [];
    }
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
        var noteEditor = element.next(".note-editor");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }

        if (noteEditor.length) {
            noteEditor.toggleClass("amg-form-select-error", !!hasError);
        }
    };

    var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
    t.frmEl.tmp_id.val(v);

    function initializeSummernote(element) {
        if (element.length && element.summernote) {
            element.summernote({
                toolbar: [
                    ['color', ['color']],
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link']]
                ],
                focus: false,
                placeholder: t.config.translations.Enter_text || 'Enter text...',
            });
            element.next('.note-editor').css({
                width: '100%'
            });
        }
    }

    function destroySummernote() {
        $('.summernote-editor').each(function() {
            if ($(this).summernote) {
                $(this).summernote('destroy');
            }
        });
    }

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }
        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        a += "<div class='so-t'><i class='bi bi-person' style='padding-right: 3px;'></i>" + s.text + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"bi bi-envelope\" style='padding-right: 3px;'></i>" + s.email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"bi bi-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    function decodeURIComponentSafe(uri, mod) {
        var out = new String(),
            arr,
            i = 0,
            l,
            x;
        typeof mod === "undefined" ? mod = 0 : 0;
        arr = uri.split(/(%(?:d0|d1)%.{2})/);
        for (l = arr.length; i < l; i++) {
            try {
                x = decodeURIComponent(arr[i]);
            } catch (e) {
                x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
            }
            out += x;
        }
        return out;
    }

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            company: {
                required: true,
            },
            subject: {
                required: true,
                maxlength: 500,
                noSpace: true
            },
            change_description: {
                required: true,
            },
            change_manager: {
                required: true,
                number: true
            },
            impact_id: {
                required: true,
                number: true
            },
            risk_id: {
                required: true,
                number: true
            },
            change_type_id: {
                required: true,
                number: true
            },
            cost: {
                digits: true
            },
            category_id: {
                required: true,
                number: true
            },
            'change_implementer[]': {
                required: true,
                implementerIDs: true
            },
            'change_reviewer[]': {
                required: function() {
                    return t.config.client == "ltts";
                },
                number: true
            },
            reason_description: {
                required: true,
            },
            risk_description: {
                required: true,
            },
            impact_description: {
                required: true,
            },
            rollout_plan: {
                required: true,
            },
            fallback_plan: {
                required: true,
            },
            scheduled_start_date: {
                required: true,
            },
        },
        messages: {
            subject: {
                required: "Please enter a subject.",
                maxlength: "Please enter no more than 500 characters."
            },
            change_description: {
                required: "Please enter a change description.",
            },
            change_manager: {
                required: "Please select a change manager.",
                number: "Please enter a valid change manager ID."
            },
            impact_id: {
                required: "Please select an impact.",
                number: "Please enter a valid impact ID."
            },
            risk_id: {
                required: "Please select a risk.",
                number: "Please enter a valid risk ID."
            },
            change_type_id: {
                required: "Please select a change type.",
                number: "Please enter a valid change type ID."
            },
            cost: {
                digits: "Please enter a valid cost value."
            },
            category_id: {
                required: "Please select a category.",
                number: "Please enter a valid category ID."
            },
            'change_implementer[]': {
                required: "Please select change implementers."
            },
            change_reviewer: {
                number: "Please enter a valid change reviewer ID."
            },
            reason_description: {
                required: "Please enter a reason description.",
            },
            risk_description: {
                required: "Please enter a risk description.",
            },
            impact_description: {
                required: "Please enter an impact description.",
            },
            rollout_plan: {
                required: "Please enter a rollout plan.",
            },
            fallback_plan: {
                required: "Please enter a fallback plan.",
            }
        },
        errorPlacement: function (error, element) {
            var errorWrap = t.getModalErrorWrap(element);

            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else if (element.closest(".input-group").length) {
                error.insertAfter(element.closest(".input-group"));
            } else {
                error.appendTo(element.closest("div"));
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

    $.validator.addMethod("implementerIDs", function(value, element) {
        if (!value || (Array.isArray(value) && value.length === 0)) {
            return false;
        }
        var implementerIDs = Array.isArray(value) ? value : [value];
        for (var i = 0; i < implementerIDs.length; i++) {
            if (!$.isNumeric(implementerIDs[i])) {
                return false;
            }
        }
        return true;
    }, "Please enter valid change implementer ID(s).");

    t.handleSubmit = function (e) {
        const fields = [
            'change_description',
            'reason_description',
            'risk_description',
            'impact_description',
            'rollout_plan',
            'fallback_plan'
        ];

        let isValid = true;

        for (let fieldName of fields) {
            let value = t.frmEl[fieldName].val().replaceAll("&nbsp;", "").trim();
            let errorDiv = $(`.${fieldName}`);
            let errorMessage = '';

            if (value.length <= 0) {
                errorMessage = 'This field is required.';
            }

            if (errorMessage !== '') {
                $('.error-message').remove();
                $('label.error').remove();
                errorDiv.html(errorMessage)
                    .css({ 'color': '#c53030', 'font-size': '13px', 'font-family': 'Arial, sans-serif' });
                isValid = false;
            } else {
                errorDiv.html('');
            }
        }

        if (!isValid || !t.frmValidator.form()) {
            e.preventDefault();
            return false;
        }
        return true;
    };

    t.loadCategories = function(companyId) {
        var category = t.frmEl.category_id;
        category.empty().append('<option value="">Select Category</option>');
        if (!companyId) {
            category.trigger('change');
            return;
        }

        $.ajax({
            url: t.config.url.get_categories_by_company_access,
            type: "GET",
            dataType: "json",
            data: { company_id: companyId },
            success: function(response) {
                var items = response.results || response.categories || [];
                $.each(items, function(index, item) {
                    category.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
                category.trigger('change');
            },
            error: function() {}
        });
    };

    t.initSelect2 = function() {
        t.fixedDropDowns = t.frm.find(".plgn-select2");
        t.fixedDropDowns.select2({ width: "100%", minimumResultsForSearch: -1 });
        t.userDropDowns = t.frm.find(".plgn-select2-user");
        t.userDropDowns.select2({
            width: "100%",
            dropdownParent: $('#changeRequestModal'),
            ajax: {
                url: t.config.url.getSelectedUser,
                dataType: "json",
                data: function(params) {
                    const changeManager = t.frmEl.change_manager.val() || [];
                    const changeReviewer = t.frmEl.change_reviewer.val() || [];
                    const changeImplementer = t.frmEl.change_implementer.val() || [];

                    let exclude = [];
                    if ($(this).attr('id') === 'change_implementer') {
                        exclude = [changeManager, changeReviewer];
                    } else if ($(this).attr('id') === 'change_manager') {
                        exclude = [...changeImplementer];
                    } else if ($(this).attr('id') === 'change_reviewer') {
                        exclude = [...changeImplementer];
                    }

                    return {
                        search: params.term,
                        page: params.page || 1,
                        exclude: exclude,
                        company_id: t.config.companyId || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || data.data || [],
                        pagination: data.pagination || { more: false }
                    };
                }
            },
            placeholder: t.config.translations.Select_user || 'Select user',
            templateResult: function(data) {
                if (!data) return $("<div>No data</div>");
                return t.userDropdownFormat(data);
            },
            templateSelection: function(data, container) {
                $(container).attr('title', data.text);
                return data.text ? (data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text) : 'Select user';
            },
            allowClear: true
        });
        t.frmEl.company_id.select2({
            width: "100%",
            dropdownParent: $('#changeRequestModal'),
            ajax: {
                url: t.config.url.get_company_by_user_access,
                dataType: "json",
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || data.data || [],
                        pagination: data.pagination || { more: false }
                    };
                }
            },
            placeholder: t.config.translations.select_company,
            allowClear: true
        });

        t.frmEl.category_id.select2({
            width: "100%",
            dropdownParent: $('#changeRequestModal'),
            ajax: {
                url: t.config.url.get_categories_by_company_access,
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                        company_id: t.frmEl.company_id.val() || ''
                    };
                },
                processResults: function(data) {
                    var items = data.results || data.categories || [];
                    return {
                        results: $.map(items, function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        }),
                        pagination: {
                            more: false
                        }
                    };
                }
            },
            placeholder: t.config.translations.Select_the_Category,
            allowClear: true
        });

        var modalParent = $('#changeRequestModal');
        t.frmEl.status_id.select2({ width: "100%" });
        t.frmEl.priority_id.select2({ width: "100%", dropdownParent: modalParent , placeholder: t.config.translations.Select_the_Priority});
        t.frmEl.impact_id.select2({ width: "100%", dropdownParent: modalParent ,placeholder: t.config.translations.Select_the_Impact});
        t.frmEl.risk_id.select2({ width: "100%", dropdownParent: modalParent , placeholder: t.config.translations.Select_the_Risk});
        t.frmEl.change_type_id.select2({ width: "100%", dropdownParent: modalParent , placeholder:t.config.translations.Select_the_Change_Type });
        t.frmEl.currency.select2({ width: "100%", dropdownParent: modalParent,placeholder:t.config.translations.select_currency});
        t.frmEl.downtime_id.select2({ width: "100%", dropdownParent: modalParent });

        // Client‑specific
        if (t.config.client === "ltts") {
            t.frmEl.device_id.select2({
                width: "100%",
                dropdownParent: t.frmEl.device_id.parent(),
                ajax: {
                    url: t.config.url.getDevices,
                    data: function(p) {
                        return {
                            search: p.term,
                            page: p.page || 1
                        };
                    },
                    dataType: "json"
                },
                allowClear: true,
                placeholder: t.config.translations.Select_the_Device || 'Select the Device',
            });
        }

        if (t.config.client === "safari") {
            t.frmEl.supplier_id.select2({
                width: "100%",
                dropdownParent: t.frmEl.supplier_id.parent(),
                ajax: {
                    url: t.config.url.getSupplierByQuery,
                    data: function(p) {
                        return {
                            search: p.term,
                            category: null,
                            page: p.page || 1
                        };
                    },
                    dataType: "json"
                },
                allowClear: true,
                placeholder: 'Select Supplier',
            });
        }
    };

    t.frmEl.company_id.on('change', function() {
        var categorySelect = t.frmEl.category_id;
        categorySelect.val(null).trigger('change');
        categorySelect.select2('data', null);
    });

    t.loadFormDropdowns = function() {
        // Change Type
        if (t.config.change_type && Array.isArray(t.config.change_type)) {
            var typeSelect = $('#change_type_id');
            typeSelect.empty().append('<option value="">' + t.config.translations.Select_the_Change_Type + '</option>');
            t.config.change_type.forEach(function(type) {
                typeSelect.append('<option value="' + type.id + '">' + type.name + '</option>');
            });
        }

        // Priority
        if (t.config.priorities && Array.isArray(t.config.priorities)) {
            var prioSelect = $('#priority_id');
            prioSelect.empty().append('<option value=""> '+ t.config.translations.Select_the_Priority +'</option>');
            t.config.priorities.forEach(function(prio) {
                prioSelect.append('<option value="' + prio.id + '">' + prio.name + '</option>');
            });
        }

        // Currency
        if (t.config.currency) {
            var currencySelect = $('#currency');
            currencySelect.empty().append('<option value="">Select Currency</option>');
            if (Array.isArray(t.config.currency)) {
                t.config.currency.forEach(function(currency) {
                    currencySelect.append('<option value="' + currency.code + '">' + currency.name + '</option>');
                });
            } else if (typeof t.config.currency === 'object') {
                Object.keys(t.config.currency).forEach(function(code) {
                    var name = t.config.currency[code];
                    currencySelect.append('<option value="' + code + '">' + name + '</option>');
                });
            }
        }

        // Impact
        if (t.config.impact && Array.isArray(t.config.impact)) {
            var impactSelect = $('#impact_id');
            impactSelect.empty().append('<option value="">' + t.config.translations.Select_the_Impact + '</option>');
            t.config.impact.forEach(function(item) {
                impactSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
            });
        }

        // Risk
        if (t.config.risk && Array.isArray(t.config.risk)) {
            var riskSelect = $('#risk_id');
            riskSelect.empty().append('<option value="">' + t.config.translations.Select_the_Risk +'</option>');
            t.config.risk.forEach(function(item) {
                riskSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
            });
        }
    };

    // ============================================================
    // SORTING FUNCTIONS (updated to match Ticket style)
    // ============================================================
    t.renderSortFields = function() {
        var $dropdown = $("#short_items");
        $dropdown.empty();
        var s = t.config.sort_dir;

        if (!t.config.sort_fields || !Array.isArray(t.config.sort_fields)) {
            return;
        }

        $.each(t.config.sort_fields, function(i, d) {
            var isActive = (d.id == s.id);
            var $li = $('<li>');
            var $a = $('<a>')
                .attr('href', 'javascript:void(0)')
                .addClass('dropdown-item' + (isActive ? ' active' : ''))
                .attr('data-id', d.id);

            // Add the radio-like span (optional, but matches Ticket code)
            $a.append('<span class="like-radio"></span>');
            $a.append('<span class="flex-grow-1">' + d.text + '</span>');

            if (isActive) {
                var icon = s.dir == 1 ? 'bi-sort-up' : 'bi-sort-down';
                $a.append('<i class="bi ' + icon + '"></i>');
            }

            $li.append($a);
            $dropdown.append($li);
        });

        // Update the sort direction icon on the button
        var iconClass = s.dir == 1 ? 'bi-sort-down' : 'bi-sort-up';
        $('#sortDirectionIcon').removeClass('bi-sort-down bi-sort-up').addClass(iconClass);
    };

    t.sortFieldChanged = function(e) {
        var id = $(this).data('id');
        if (t.config.sort_dir.id == id) {
            // Toggle direction if same field
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            // New field, set to ascending (1)
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 1;
        }
        t.renderSortFields();
        t.load();
    };
    // ============================================================

    t.loadFilterDropdowns = function() {
        if (t.config.statuses && Array.isArray(t.config.statuses)) {
            var statusSelect = $('#filter_by_status');
            statusSelect.empty();
            t.config.statuses.forEach(function(status) {
                statusSelect.append('<option value="' + status.id + '">' + status.name + '</option>');
            });
        }
        if (t.config.priorities && Array.isArray(t.config.priorities)) {
            var prioritySelect = $('#filter_by_priority');
            prioritySelect.empty();
            t.config.priorities.forEach(function(prio) {
                prioritySelect.append('<option value="' + prio.id + '">' + prio.name + '</option>');
            });
        }
        if (t.config.change_type && Array.isArray(t.config.change_type)) {
            var typeSelect = $('#filter_by_type');
            typeSelect.empty();
            t.config.change_type.forEach(function(type) {
                typeSelect.append('<option value="' + type.id + '">' + type.name + '</option>');
            });
        }
        var categorySelect = $('#filter_by_category');
        categorySelect.empty().append('<option value="">Select Category</option>');
        $.ajax({
            url: t.config.url.get_categories_by_company_access,
            type: "GET",
            dataType: "json",
            data: { company_id: companyId },
            success: function(response) {
                var items = response.results || response.categories || [];
                $.each(items, function(index, item) {
                    categorySelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                });
                categorySelect.trigger('change');
            },
            error: function() {}
        });
        var userSelectIds = ['filter_by_requester', 'filter_by_manager', 'filter_by_implementer', 'filter_by_reviewer'];
        userSelectIds.forEach(function(id) {
            var select = $('#' + id);
            if (!select.data('select2')) {
                select.select2({
                    width: '100%',
                    dropdownParent: select.closest('.modal-body'),
                    multiple: true,
                    ajax: {
                        url: t.config.url.getSelectedUser,
                        dataType: "json",
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page || 1,
                                exclude: [],
                                company_id: t.config.companyId || ''
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.results || data.data || [],
                                pagination: data.pagination || { more: false }
                            };
                        }
                    },
                    placeholder: t.config.translations.Select_user,
                    templateResult: function(data) {
                        if (!data) return $("<div>No data</div>");
                        return t.userDropdownFormat(data);
                    },
                    templateSelection: function(data, container) {
                        $(container).attr('title', data.text);
                        return data.text ? (data.text.length > 50 ? data.text.substring(0, 50) + '...' : data.text) : 'Select...';
                    },
                    allowClear: true
                });
            }
        });
    };

    t.initDatePickers = function() {
        t.scheduled_start_date = t.frm.find("#scheduled_start_date");
        t.scheduled_end_date = t.frm.find("#scheduled_end_date");
        var newDate = new Date();

        t.scheduled_end_date.flatpickr({
            dateFormat: "d/m/Y h:i K",
            enableTime: true,
            time_24hr: false,
            minuteIncrement: 30,
            minDate: newDate
        });

        t.scheduled_start_date.flatpickr({
            dateFormat: "d/m/Y h:i K",
            enableTime: true,
            time_24hr: false,
            minuteIncrement: 30,
            minDate: newDate
        });

        t.scheduled_start_date.on("dp.change", function(e) {
            t.scheduled_end_date.data("DateTimePicker").minDate(e.date);
        });

        t.scheduled_end_date.on("dp.change", function(e) {
            t.scheduled_start_date.data("DateTimePicker").maxDate(e.date);
        });
        t.frmEl.start_down_time.flatpickr({
            dateFormat: "d/m/Y h:i K",
            enableTime: true,
            time_24hr: false,
            minuteIncrement: 30,
            minDate: newDate
        });

        t.frmEl.end_down_time.flatpickr({
            dateFormat: "d/m/Y h:i K",
            enableTime: true,
            time_24hr: false,
            minuteIncrement: 30,
            minDate: newDate
        });
        if (t.config.client === "safari") {
            t.frmEl.po_date.flatpickr({
                dateFormat: "d/m/Y h:i K",
                enableTime: true,
                time_24hr: false,
                minuteIncrement: 30,
                minDate: newDate
            });
        }
    };

    t.cache_filter_values = function () {
        t.config.search = $.trim(t.searchbox.find("input").val());
        var filters = {
            search: t.config.search || '',
            other_filters: t.activeFilters || {}
        };
        t.config.export_filters = btoa(JSON.stringify(filters));
    };

    t.search = function(e) {
        if (e.keyCode == 13) {
            var v = t.searchbox.find("input").val();
            t.config.search = v || '';
            t.currentPage = 1;
            t.load();
        }
    };

    t.reload = function() {
        t.config.search = t.searchbox.find("input").val() || '';
        t.currentPage = 1;
        t.load();
    };

    t.showCardLoader = function() {
        if (t.lg.find(".card-loader-wrapper").length) {
            return;
        }
        t.lg.empty();
        var cardLoaderHtml = `
            <div class="card-loader-wrapper pt-5">
                <div class="text-center p-5">
                    <svg id="kanban-board-loader" class="fa-spin" stroke="#dc2626" fill="#dc2626" width="80" height="80" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" xml:space="preserve">
                        <style>
                            @keyframes spin {
                                0% { transform: rotate(0deg); }
                                100% { transform: rotate(360deg); }
                            }
                            .fa-spin {
                                animation: spin 1s linear infinite;
                            }
                        </style>
                        <g>
                            <path class="st1" d="M176.22,85.97c6.63,0,12.81,3.57,16.12,9.31l26.22,45.42c3.32,5.74,3.32,12.88,0,18.62l-26.22,45.42
                                c-3.32,5.74-9.49,9.31-16.12,9.31h-52.44c-6.63,0-12.81-3.57-16.12-9.31l-26.22-45.41c-3.32-5.74-3.32-12.88,0-18.62l26.22-45.42
                                c3.32-5.74,9.49-9.31,16.12-9.31H176.22 M176.22,76.97h-52.44c-9.87,0-18.98,5.26-23.92,13.81l-26.22,45.42
                                c-4.93,8.55-4.93,19.07,0,27.62l26.22,45.41c4.93,8.55,14.05,13.81,23.92,13.81h52.44c9.87,0,18.98-5.26,23.92-13.81l26.22-45.41
                                c4.93-8.55,4.93-19.07,0-27.62l-26.22-45.42C195.21,82.23,186.09,76.97,176.22,76.97L176.22,76.97z">
                            </path>
                        </g>
                    </svg>
                    <p style="margin-top:12px;color:#dc2626;font-size:14px;font-weight:500;">  ${t.config.translations.Loading_Data} </p>
                </div>
            </div>
        `;
        t.lg.append(cardLoaderHtml);
    };

    t.hideCardLoader = function() {
        t.lg.find(".card-loader-wrapper").remove();
    };

    // ============================================
    // CARD RENDER FUNCTIONS
    // ============================================
    t.getPriorityColorByName = function(priorityName) {
        if (!priorityName) return '#6b7280';
        var lower = priorityName.toLowerCase();
        if (lower.includes('critical')) return '#ef4444';
        if (lower.includes('high')) return '#f97316';
        if (lower.includes('medium')) return '#eab308';
        if (lower.includes('low')) return '#22c55e';
        return '#6b7280';
    };

    t.getPriorityColorFromConfig = function(priorityName) {
        if (!priorityName || !t.config.priorities || !Array.isArray(t.config.priorities)) {
            return t.getPriorityColorByName(priorityName);
        }
        var found = t.config.priorities.find(function(p) {
            return p.name && p.name.toLowerCase() === priorityName.toLowerCase();
        });
        if (found && found.color_code) {
            return found.color_code;
        }
        return t.getPriorityColorByName(priorityName);
    };

    t.getChangeTypeDisplay = function(changeTypeId) {
        if (!changeTypeId) return 'Standard';
        var found = (t.config.change_type || []).find(function(ct) {
            return ct.id == changeTypeId;
        });
        return found ? found.name : 'Standard';
    };

    t.getInitials = function(name) {
        if (!name || name === 'N/A' || name === 'null') return '??';
        var parts = name.split(' ');
        return parts.map(function(p) { return p[0] || ''; }).join('').substring(0, 2).toUpperCase();
    };

    t.adjustColorOpacity = function(color, opacity) {
        if (!color) return 'rgba(139, 92, 246, ' + opacity + ')';
        if (color.startsWith('#')) {
            var hex = color.replace('#', '');
            var r = parseInt(hex.substring(0, 2), 16);
            var g = parseInt(hex.substring(2, 4), 16);
            var b = parseInt(hex.substring(4, 6), 16);
            return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + opacity + ')';
        }
        return color;
    };

    t.initTooltips = function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    };

    t.getCabMemberHtml = function(member) {
        if (!member) return '';
        var initials = t.getInitials(member.name);
        var avatarHtml = '';
        if (member.avatar && member.avatar !== '') {
            avatarHtml = '<img src="' + member.avatar + '" alt="' + member.name + '" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">';
        } else {
            avatarHtml = initials;
        }
        return '<div class="acr-uav" style="background:linear-gradient(135deg,#14b8a6,#0891b2);" data-bs-toggle="tooltip" title="' + member.name + '">' + avatarHtml + '</div>';
    };

    t.renderCard = function(d, index) {
        var changeType = d.changeTypeName || t.getChangeTypeDisplay(d.change_type_id);
        var priorityName = d.priorityName || 'Normal';
        var statusName = d.statusName || 'New';
        var impactName = d.impactName || 'Medium';
        var riskName = d.riskName || 'Low';
        var categoryName = d.categoryName || 'N/A';
        var cab_name = d.cab_name || 'N/A';
        var closeState = d.close_state || '';
        var isClosed = closeState === 'Success' || closeState === 'Failed';
        var priorityColor = t.getPriorityColorFromConfig(priorityName);
        var changeTypeColor = d.color_code || '#8b5cf6';
        var borderColorLight = t.adjustColorOpacity(changeTypeColor, 0.3);
        var borderColorMedium = t.adjustColorOpacity(changeTypeColor, 0.5);
        var creatorInitials = d.changeRequester ? t.getInitials(d.changeRequester) : '??';
        var managerInitials = d.changeManager ? t.getInitials(d.changeManager) : '??';
        var searchData = (d.subject || '') + ' ' + (d.changeRequester || '') + ' ' + (d.statusName || '');

        var html = '';
        html += '<div class="acr-card" data-search="' + searchData.toLowerCase() + '" style="border-color: ' + changeTypeColor + ' !important;">';
        html += '<div class="acr-card-left" style="background: ' + changeTypeColor + ' !important;">';
        html += '<input type="checkbox" class="acr-cb acr-row-cb form-check-input">';
        html += '<span class="acr-id-badge h4-text">' + (d.record_tag || 'CR00000') + '</span>';
        html += '</div>';
        html += '<div class="acr-card-body" style="border-color: ' + borderColorLight + ' !important;">';
        html += '<div class="acr-card-details">';
        html += '<h4 class="h4-text">' + (d.subject || 'Change Request') + '</h4>';
        html += '<div class="acr-meta-row">';
        html += '<div class="acr-uav" style="background:linear-gradient(135deg,#6366f1,#4338ca);">' + creatorInitials + '</div>';
        html += '<span class="fw-normal b5-text" data-bs-toggle="tooltip" title="'+ t.config.translations.creator +'">' + (d.changeRequester || 'N/A') + '</span>';
        html += '<svg width="1" height="20" viewBox="0 0 1 31" fill="none"><path d="M0.5 0L0.499999 30.2148" stroke="' + borderColorMedium + '"/></svg>';
        var statusClass = isClosed ? 'acr-chip-closed' : 'acr-chip-plain';
        html += '<span class="acr-chip ' + statusClass + '" data-bs-toggle="tooltip" title="'+ t.config.translations.Status +'">' + statusName + '</span>';
        html += '<svg width="1" height="20" viewBox="0 0 1 31" fill="none"><path d="M0.5 0L0.499999 30.2148" stroke="' + borderColorMedium + '"/></svg>';
        html += '<span class="b5-text" data-bs-toggle="tooltip" title="'+ t.config.translations.Category +'">' + categoryName + '</span>';
        html += '<svg width="1" height="20" viewBox="0 0 1 31" fill="none"><path d="M0.5 0L0.499999 30.2148" stroke="' + borderColorMedium + '"/></svg>';
        html += '<span class="b5-text" data-bs-toggle="tooltip" title="'+ t.config.translations.Change_Type +'">' + changeType + '</span>';
        html += '<svg width="1" height="20" viewBox="0 0 1 31" fill="none"><path d="M0.5 0L0.499999 30.2148" stroke="' + borderColorMedium + '"/></svg>';
        html += '<div style="display:flex;align-items:center;gap:6px;">';
        html += '<div class="acr-group-avs">';
        if (d.cab_members && Array.isArray(d.cab_members) && d.cab_members.length > 0) {
            d.cab_members.forEach(function(member) {
                html += t.getCabMemberHtml(member);
            });
        } else {
            html += '<div class="acr-uav" style="background:linear-gradient(135deg,#9ca3af,#6b7280);font-size:7px;font-weight:600;color:#fff;">N/A</div>';
        }
        html += '</div>';
        html += '<span style="font-size:12px;color:#6b7280;" data-bs-toggle="tooltip" title="'+ t.config.translations.Cab_name +'">' + cab_name + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '<div class="acr-info-row" style="border-color: ' + borderColorLight + ' !important;">';
        html += '<div class="acr-info-item" data-bs-toggle="tooltip" title="'+ t.config.translations.priority +'">';
        html += '<span class="acr-prio-sq" style="background: ' + priorityColor + ';"></span>';
        html += '<span>' + priorityName + '</span>';
        html += '</div>';

        html += '<div class="acr-info-item" data-bs-toggle="tooltip" title="'+ t.config.translations.impact +'">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>';
        html += '<span>' + impactName + '</span>';
        html += '</div>';

        html += '<div class="acr-info-item" data-bs-toggle="tooltip" title="'+ t.config.translations.risk +'">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>';
        html += '<span>' + riskName + '</span>';
        html += '</div>';

        html += '<div class="acr-info-item b5-text" data-bs-toggle="tooltip" title="'+ t.config.translations.Created_at +'">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
        html += '<span>' + (d.created_at_format || 'N/A') + '</span>';
        html += '</div>';

        html += '<div class="acr-info-item b5-text" data-bs-toggle="tooltip" title="'+ t.config.translations.updated_at +'">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
        html += '<span>' + (d.last_updated_at || 'N/A') + '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="acr-card-right" style="border-color: ' + borderColorLight + ' !important;">';
        html += '<div class="acr-mgr-row">';
        html += '<div class="acr-mgr-item">';
        html += '<span class="b5-text fw-medium">'+ t.config.translations.manager +'</span>';
        html += '<div class="acr-mgr-val">';
        html += '<div class="acr-uav" style="background:linear-gradient(135deg,#6366f1,#4338ca);">' + managerInitials + '</div>';
        html += (d.changeManager || 'N/A');
        html += '</div>';
        html += '</div>';
        html += '<div class="acr-mgr-item">';
        html += '<span class="b5-text fw-medium">'+ t.config.translations.start_date +'</span>';
        html += '<div class="acr-date-val b5-text">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>';
        html += (d.scheduled_start_date || 'N/A');
        html += '</div>';
        html += '</div>';
        html += '</div>';

        html += '<div class="acr-action-btns" style="border-color: ' + borderColorLight + ' !important;">';
        html += '<button class="acr-btn-outline" onclick="alert(\'History for ' + d.record_tag + '\')">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>';
        html += ''+ t.config.translations.history +'';
        html += '</button>';
        html += '<a href="' + t.config.url.info + "/" + d.param_record_tag + '" target="_blank" class="acr-btn-outline">';
        html += '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
        html += ''+ t.config.translations.View +'';
        html += '</a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        return html;
    };

    t.renderList = function () {
        t.lg.empty();
        var data = t.filteredData || [];
        if (data.length === 0) {
            t.lg.html('<div class="no-record-found">No records found</div>');
            t.pageBtmSummary.html('Available Records : 0');
            t.renderPagination();
            return;
        }
        $.each(data, function (i, d) {
            t.lg.append(t.renderCard(d, i));
        });
        var start = ((t.currentPage - 1) * t.perPage) + 1;
        var end = start + data.length - 1;
        t.pageBtmSummary.html(
            t.config.translations.available_records + start + " - " + end + " of " + t.totalData + t.config.translations.records
        );
        t.renderPagination();
    };
    // t.renderPagination = function () {
    //     t.pagebtns.empty();
    //     var totalPages = Math.ceil(t.totalData / t.perPage);
    //     if (totalPages <= 1) {
    //         return;
    //     }
    //     t.pagebtns.append(
    //         '<li class="page-item ' + (t.currentPage == 1 ? 'disabled' : '') + '">' +
    //         '<a href="#" class="page-link" data-page="' + (t.currentPage - 1) + '">&laquo;</a>' +
    //         '</li>'
    //     );
    //     for (var i = 1; i <= totalPages; i++) {
    //         t.pagebtns.append(
    //             '<li class="page-item ' + (i == t.currentPage ? 'active' : '') + '">' +
    //             '<a href="#" class="page-link" data-page="' + i + '">' + i + '</a>' +
    //             '</li>'
    //         );

    //     }
    //     t.pagebtns.append(
    //         '<li class="page-item ' + (t.currentPage == totalPages ? 'disabled' : '') + '">' +
    //         '<a href="#" class="page-link" data-page="' + (t.currentPage + 1) + '">&raquo;</a>' +
    //         '</li>'
    //     );
    //     t.pagebtns.find("a.page-link").click(function (e) {
    //         e.preventDefault();
    //         var page = parseInt($(this).data("page"));
    //         if (page >= 1 && page <= totalPages) {
    //             t.currentPage = page;
    //             t.load();
    //         }
    //     });
    // };

    t.renderPagination = function () {
    var $container = t.pagebtns;   // your existing jQuery element
    $container.empty();

    var totalPages = Math.ceil(t.totalData / t.perPage);
    if (totalPages <= 1) {
        $container.hide();
        return;
    } else {
        $container.show();
    }

    // Translations (fallback)
    var prevLabel = (t.config && t.config.translations && t.config.translations.previous) || 'Previous';
    var nextLabel = (t.config && t.config.translations && t.config.translations.next) || 'Next';

    // ---- Previous button ----
    var prevDisabled = t.currentPage <= 1 ? 'disabled' : '';
    var prevButton = `
        <li class="page-item ${prevDisabled}">
            <button class="tkt-pagination btn-prev-next" data-page="${t.currentPage - 1}" ${prevDisabled ? 'disabled' : ''}>
                <svg width="12" height="12" viewBox="0 0 8 12" fill="none">
                    <path d="M6.5 11L1.5 6L6.5 1" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                ${prevLabel}
            </button>
        </li>
    `;
    $container.append(prevButton);

    // ---- Visible page range (max 5) ----
    var maxVisible = 5;
    var startPage = Math.max(1, t.currentPage - Math.floor(maxVisible / 2));
    var endPage = Math.min(totalPages, startPage + maxVisible - 1);

    if (endPage - startPage + 1 < maxVisible) {
        startPage = Math.max(1, endPage - maxVisible + 1);
    }

    // ---- First page + ellipsis ----
    if (startPage > 1) {
        $container.append(`
            <li class="page-item">
                <a class="page-link" href="#" data-page="1">1</a>
            </li>
        `);
        if (startPage > 2) {
            $container.append(`
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            `);
        }
    }

    // ---- Page numbers ----
    for (var i = startPage; i <= endPage; i++) {
        var activeClass = (i === t.currentPage) ? 'active' : '';
        $container.append(`
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }

    // ---- Last page + ellipsis ----
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            $container.append(`
                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>
            `);
        }
        $container.append(`
            <li class="page-item">
                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
            </li>
        `);
    }

    var nextDisabled = t.currentPage >= totalPages ? 'disabled' : '';
    var nextButton = `
        <li class="page-item ${nextDisabled}">
            <button class="tkt-pagination btn-prev-next" data-page="${t.currentPage + 1}" ${nextDisabled ? 'disabled' : ''}>
                ${nextLabel}
                <svg width="12" height="12" viewBox="0 0 8 12" fill="none">
                    <path d="M1.5 11L6.5 6L1.5 1" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </li>
    `;
    $container.append(nextButton);
    $container.find("a.page-link, button.tkt-pagination").off('click').on('click', function (e) {
        e.preventDefault();
        var page = parseInt($(this).data("page"));
        if (page >= 1 && page <= totalPages && page !== t.currentPage) {
            t.currentPage = page;
            t.load();
        }
    });
};
    t.countActiveFilters = function() {
        var count = 0;
        var filters = t.activeFilters || {};
        if (filters.status && filters.status.length > 0) count++;
        if (filters.priority && filters.priority.length > 0) count++;
        if (filters.requester && filters.requester.length > 0) count++;
        if (filters.manager && filters.manager.length > 0) count++;
        if (filters.implementer && filters.implementer.length > 0) count++;
        if (filters.reviewer && filters.reviewer.length > 0) count++;
        if (filters.change_type && filters.change_type.length > 0) count++;
        if (filters.category && filters.category.length > 0) count++;
        if (filters.date_type && filters.date_type !== 'null' && filters.date_range && filters.date_range !== '') {
            count++;
        }
        if (filters.close_state && filters.close_state !== 'null' && filters.close_state !== null) count++;
        return count;
    };

    t.updateFilterBadge = function() {
        var count = t.countActiveFilters();
        var badge = $('.filter-count-badge');
        badge.text(count);
        if (count > 0) {
            badge.removeClass('d-none');
        } else {
            badge.addClass('d-none');
        }
    };

    t.applyFilters = function() {
        t.activeFilters = {
            status: $('#filter_by_status').val() || [],
            priority: $('#filter_by_priority').val() || [],
            requester: $('#filter_by_requester').val() || [],
            manager: $('#filter_by_manager').val() || [],
            implementer: $('#filter_by_implementer').val() || [],
            reviewer: $('#filter_by_reviewer').val() || [],
            change_type: $('#filter_by_type').val() || [],
            category: $('#filter_by_category').val() || [],
            date_type: $('#filter_by_date').val() || 'null',
            date_range: ($('#filter_by_date').val() !== 'null') ? ($('#daterange').val() || '') : '',
            close_state: $('#filter_by_close_state').val() || 'null'
        };
        t.updateFilterBadge();
        t.currentPage = 1;
        t.load();
        $('#changeFilterModal').modal('hide');
    };

    t.clearFilters = function() {
        $('#filter_by_status, #filter_by_priority, #filter_by_requester, #filter_by_manager, #filter_by_implementer, #filter_by_reviewer, #filter_by_type, #filter_by_category').val([]).trigger('change');
        $('#filter_by_date').val('null').trigger('change');
        $('#filter_by_close_state').val('null').trigger('change');
        $('#daterange').val('');
        $('#reportrange span').html('');
        t.activeFilters = {};
        t.updateFilterBadge();
        t.currentPage = 1;
        t.load();
        $('#changeFilterModal').modal('hide');
    };
    var originalLoad = t.load;
    t.load = function() {
        t.showCardLoader();
        t.cache_filter_values();
        var filterParams = {};
        var f = t.activeFilters || {};
        if (f.status && f.status.length) filterParams.status = f.status;
        if (f.priority && f.priority.length) filterParams.priority = f.priority;
        if (f.requester && f.requester.length) filterParams.requester = f.requester;
        if (f.manager && f.manager.length) filterParams.manager = f.manager;
        if (f.implementer && f.implementer.length) filterParams.implementer = f.implementer;
        if (f.reviewer && f.reviewer.length) filterParams.reviewer = f.reviewer;
        if (f.change_type && f.change_type.length) filterParams.change_type = f.change_type;
        if (f.category && f.category.length) filterParams.category = f.category;
        if (f.date_type && f.date_type !== 'null' && f.date_range && f.date_range !== '') {
            filterParams.date_type = f.date_type;
            filterParams.date_range = f.date_range;
        }
        if (f.close_state && f.close_state !== 'null') filterParams.close_state = f.close_state;
        var http = $.ajax({
            url: t.config.url.requests,
            type: "post",
            data: $.extend({
                search: t.config.search || '',
                page: t.currentPage || 1,
                size: t.perPage,
                order: t.config.sort_dir || { id: 9, dir: 2 },
                main_filter: t.config.main_filter || '',
            }, filterParams)
        });

        http.done(function(data) {
            t.hideCardLoader();
            if (typeof data == "object" && typeof data.total != "undefined") {
                t.totalData = data.filtered || 0;
                t.filteredData = data.data || [];
                t.currentPage = data.page || 1;
                t.renderList();
                t.initTooltips();
            }
        });

        http.fail(function(xhr, status, error) {
            t.hideCardLoader();
            var errorMessage = "Something went wrong. Please check given details are correct";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.statusText) {
                errorMessage = xhr.statusText;
            }
            t.lg.html('<div class="no-record-found" style="color:#dc2626;padding:40px 20px;text-align:center;">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" width="48" height="48" style="margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
                '<p style="font-size:16px;font-weight:500;">' + errorMessage + '</p>' +
                '<p style="font-size:13px;color:#6b7280;margin-top:4px;">Please try again or contact support.</p>' +
                '</div>');
            t.pageBtmSummary.html((t.config.translations.Available_Records || 'Available Records') + ' 0');
        });
    };

    var currentStep = 1;
    var totalSteps = 4;

    function updateStepButtons(step) {
        $('#btnPrevStep').toggle(step > 1);
        $('#btnNextStep').toggle(step < totalSteps);
        $('#btnSubmitChange').toggle(step === totalSteps);
    }

    function validateStep(step) {
        var isValid = true;
        function setFieldError(fieldEl, message) {
            var errorWrap = t.getModalErrorWrap(fieldEl);
            if (message) {
                errorWrap.html('<label class="error">' + message + '</label>');
                t.updateValidationState(fieldEl, true);
            } else {
                errorWrap.html('');
                t.updateValidationState(fieldEl, false);
            }
        }

        if (step === 1) {
            var requiredFields = [
                'company_id', 'subject', 'change_type_id', 'category_id',
                'priority_id', 'impact_id', 'risk_id', 'downtime_id'
            ];

            if ($('#downtime_id').val() == '2') {
                requiredFields.push('start_down_time', 'end_down_time');
            }

            if (t.config.client == 'ltts') {
                requiredFields.push('scheduled_start_date', 'scheduled_end_date');
            }

            requiredFields.forEach(function(field) {
                var fieldEl = $('#' + field);
                var value = fieldEl.val();
                var hasError = !value || value === '' || value === null || value === '';
                setFieldError(fieldEl, hasError ? t.config.translations.required_fields_info : '');
                if (hasError) isValid = false;
            });

            // Special date validation
            if (isValid && $('#scheduled_start_date').val() && $('#scheduled_end_date').val()) {
                var startDate = new Date($('#scheduled_start_date').val());
                var endDate = new Date($('#scheduled_end_date').val());
                if (startDate >= endDate) {
                    var endField = $('#scheduled_end_date');
                    setFieldError(endField, 'End date must be after start date');
                    isValid = false;
                }
            }

        } else if (step === 2) {
            var textareaFields = [
                'change_description', 'reason_description', 'risk_description',
                'impact_description', 'rollout_plan', 'fallback_plan'
            ];

            textareaFields.forEach(function(field) {
                var fieldEl = $('#' + field);
                var hasError = false;
                var errorMsg = '';

                if (fieldEl.hasClass('summernote-editor') && fieldEl.summernote) {
                    var content = fieldEl.summernote('code');
                    if (!content || content === '<p><br></p>' || content.trim() === '') {
                        hasError = true;
                        errorMsg = t.config.translations.required_fields_info;
                    }
                } else {
                    var value = fieldEl.val();
                    if (!value || value.trim() === '') {
                        hasError = true;
                        errorMsg = t.config.translations.required_fields_info;
                    }
                }

                setFieldError(fieldEl, hasError ? errorMsg : '');
                if (hasError) isValid = false;
            });

        } else if (step === 3) {
            isValid = true;
        } else if (step === 4) {
            var requiredFields = ['change_requester', 'change_manager', 'change_implementer'];

            requiredFields.forEach(function(field) {
                var fieldEl = $('#' + field);
                var hasError = false;
                var errorMsg = '';

                if (fieldEl.is('select[multiple]')) {
                    var value = fieldEl.val();
                    if (!value || value.length === 0) {
                        hasError = true;
                        errorMsg = 'Please select at least one option';
                    }
                } else {
                    var value = fieldEl.val();
                    if (!value || value === '' || value === null) {
                        hasError = true;
                        errorMsg = t.config.translations.required_fields_info;
                    }
                }

                setFieldError(fieldEl, hasError ? errorMsg : '');
                if (hasError) isValid = false;
            });

            if (t.config.client == 'ltts') {
                var reviewerField = $('#change_reviewer');
                var reviewerValue = reviewerField.val();
                if (!reviewerValue || reviewerValue.length === 0) {
                    setFieldError(reviewerField, 'Please select at least one reviewer');
                    isValid = false;
                } else {
                    setFieldError(reviewerField, '');
                }
            }
        }

        return isValid;
    }

    t.searchbox.on("keypress", "input", function(e) {
        if (e.keyCode == 13) {
            t.currentPage = 1;
            t.load();
        }
    });

    // Refresh
    $(".btn-reload-list").on("click", function() {
        t.currentPage = 1;
        t.searchbox.find("input").val('');
        t.load();
    });

    $(".btn-add-record").on("click", function(e) {
        e.preventDefault();
        $('#changeRequestForm')[0].reset();
        $('.amg-form-error-wrap').html('');
        $('.error-message').remove();
        $('.subject').html('');
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.frmEl.tmp_id.val(v);
        $('#change_requester').val(t.config.userId || '');
        $('#changeRequestModal').modal('show');
    });

    $('#changeRequestModal').on('shown.bs.modal', function() {
        t.loadFormDropdowns();
        setTimeout(function() {
            $('.summernote-editor').each(function() {
                if ($(this).summernote) {
                    $(this).summernote('destroy');
                }
                initializeSummernote($(this));
            });
            t.initSelect2();
            t.initDatePickers();
            currentStep = 1;
            $('#step1-tab').tab('show');
            updateStepButtons(1);
        }, 300);
    });

    function resetValidation() {
        t.frm.find("label.error").remove();
        t.frm.find(".error").removeClass("error");
        t.frm.find(".is-invalid").removeClass("is-invalid");
        $(".amg-form-error-wrap").empty();
        $(".select2-selection").removeClass("amg-form-select-error");
        $(".input-group").removeClass("amg-form-invalid");
        $(".note-editor").removeClass("amg-form-select-error");
        if (t.frmValidator) {
            t.frmValidator.resetForm();
        }
    }

    $('#changeRequestModal').on('hidden.bs.modal', function() {
        t.frm[0].reset();
        resetValidation();
        t.frmValidator.resetForm();
        t.frm.find('.is-invalid, .error').removeClass('is-invalid error');
        $('.amg-form-error-wrap').empty();
        $('.error-message').remove();
        t.frm.find('select').each(function() {
            $(this).val(null).trigger('change');
        });
        t.frmEl.category_id.empty().append('<option value="">'+ t.config.translations.Select_the_Category +'</option>').trigger('change');
        $('.summernote-editor').each(function() {
            if ($(this).next('.note-editor').length) {
                $(this).summernote('code', '');
                $(this).summernote('destroy');
            }
        });
        t.frm.find('input[type="text"], input[type="datetime-local"]').not('#tmp_id').val('');
        $('#downtime_hide_show').hide();
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.frmEl.tmp_id.val(v);
        currentStep = 1;
        var uploader = $('#attachment-dropper-cover').data('amg-uploader');
        if (uploader && typeof uploader.reset === 'function') {
            uploader.reset();
        } else {
            $('#attachments').empty();
            $('#attachment_id').val('');
            $('#attachment-dropper-cover').find('input[type="hidden"]').not('#tmp_id').remove();
        }
        $('#step1-tab').tab('show');
        updateStepButtons(1);
    });

    // Next Step
    $('#btnNextStep').on('click', function() {
        $('.summernote-editor').each(function() {
            if ($(this).summernote) {
                var content = $(this).summernote('code');
                $(this).val(content);
            }
        });

        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                $('#step' + currentStep + '-tab').tab('show');
                updateStepButtons(currentStep);
                setTimeout(function() {
                    $('.summernote-editor').each(function() {
                        if ($(this).summernote) {
                            $(this).summernote('destroy');
                        }
                        initializeSummernote($(this));
                    });
                }, 300);
            }
        }
    });

    $('#btnPrevStep').on('click', function() {
        if (currentStep > 1) {
            currentStep--;
            $('#step' + currentStep + '-tab').tab('show');
            updateStepButtons(currentStep);
            setTimeout(function() {
                $('.summernote-editor').each(function() {
                    if ($(this).summernote) {
                        $(this).summernote('destroy');
                    }
                    initializeSummernote($(this));
                });
            }, 300);
        }
    });

    $('#changeRequestSteps').on('show.bs.tab', function(e) {
        var targetTab = $(e.target).data('bs-target') || $(e.target).attr('href');
        if (!targetTab) return;
        var targetStep = parseInt(targetTab.replace('#step', ''));
        if (targetStep > currentStep) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Tab switch
    $('#changeRequestSteps .nav-link').on('shown.bs.tab', function(e) {
        var target = $(e.target).data('bs-target') || $(e.target).attr('href');
        var stepId = target ? target.replace('#step', '') : '';
        currentStep = parseInt(stepId);
        updateStepButtons(currentStep);
        setTimeout(function() {
            $('.summernote-editor').each(function() {
                if ($(this).summernote) {
                    $(this).summernote('destroy');
                }
                initializeSummernote($(this));
            });
        }, 300);
    });

    // Downtime toggle
    $('#downtime_id').on('change', function() {
        var value = $(this).val();
        if (value == '2') {
            $('#downtime_hide_show').slideDown(300);
        } else {
            $('#downtime_hide_show').slideUp(300);
            $('#start_down_time').val('');
            $('#end_down_time').val('');
        }
    });
    if ($('#downtime_id').val() == '2') {
        $('#downtime_hide_show').show();
    } else {
        $('#downtime_hide_show').hide();
    }

    // Submit Change
    $("#btnSubmitChange").on("click", function() {
        $('.summernote-editor').each(function() {
            if ($(this).summernote) {
                var content = $(this).summernote('code');
                $(this).val(content);
            }
        });
        if (!t.handleSubmit(event)) {
            return;
        }
        var loader = $('#changeRequestLoader');
        loader.show();

        var attachmentIds = t.frmEl.attachment_id.val() || [];

        var formData = {
            company_id: $('#company_id').val(),
            subject: $('#subject').val(),
            priority_id: $('#priority_id').val(),
            change_type_id: $('#change_type_id').val(),
            impact_id: $('#impact_id').val(),
            risk_id: $('#risk_id').val(),
            downtime: $('#downtime_id').val(),
            start_down_time: $('#start_down_time').val(),
            end_down_time: $('#end_down_time').val(),
            scheduled_start_date: $('#scheduled_start_date').val(),
            scheduled_end_date: $('#scheduled_end_date').val(),
            currency: $('#currency').val(),
            cost: $('#cost').val(),
            category_id: $('#category_id').val(),
            change_description: $('#change_description').val(),
            reason_description: $('#reason_description').val(),
            risk_description: $('#risk_description').val(),
            impact_description: $('#impact_description').val(),
            rollout_plan: $('#rollout_plan').val(),
            fallback_plan: $('#fallback_plan').val(),
            change_requester: $('#change_requester').val(),
            change_manager: $('#change_manager').val(),
            change_implementer: $('#change_implementer').val(),
            change_reviewer: $('#change_reviewer').val(),
            enable_communication: $('#enable_communication').is(':checked') ? 1 : 0,
            attachment_id: attachmentIds,
            tmp_id: t.frmEl.tmp_id.val(),
            _token: t.config.token || $('meta[name="csrf-token"]').attr('content') || '',
        };

        $.ajax({
            url: t.config.url.add,
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                loader.hide();
                if (response.status === 'success' || response.status === true) {
                    $('#changeRequestModal').modal('hide');
                    toastr.success( t.config.translations.New_change_request + ' ' + t.config.translations.has_been_created_successfully);
                    t.reload();
                } else {
                    var errorMsg = response.message || 'Failed to create change request';
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            var fieldEl = $('#' + key);
                            if (fieldEl.length) {
                                var errorWrap = fieldEl.closest('.amg-form-field').find('.amg-form-error-wrap');
                                errorWrap.html('<span class="text-danger">' + value[0] + '</span>');
                            }
                        });
                    }
                    toastr.error(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                loader.hide();
                var errorMsg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                toastr.error(errorMsg);
            }
        });
    });

    // Page limiter
    t.pageLimiter.on("change", function () {
        t.perPage = parseInt($(this).val()) || 10;
        t.currentPage = 1;
        t.load();
    });

    t.resetForm = function() {
        $('#changeRequestForm')[0].reset();
        $('.amg-form-error-wrap').html('');
        $('.amg-form-invalid').removeClass('amg-form-invalid');
        $('.amg-form-select-error').removeClass('amg-form-select-error');
        $('.select2-hidden-accessible').each(function() {
            $(this).val(null).trigger('change');
        });
        $('.summernote-editor').each(function() {
            if ($(this).summernote) {
                $(this).summernote('code', '');
            }
        });
        $('.error-message').remove();
        $('.subject').html('');
        $('[class*="__error"]').html('');
    };

    // Select All - global
    window.acrCheckAll = function(cb) {
        document.querySelectorAll('.acr-row-cb').forEach(function(c) {
            c.checked = cb.checked;
        });
    };

    // Manual file trigger
    $("#manual_file_trigger").on("click", function(e) {
        e.preventDefault();
        $('#attachment_input').trigger('click');
    });

    // ----- SORT EVENTS (updated to match Ticket style) -----
    // Click on sort icon to toggle direction
    $('#sortDirectionIcon').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        t.renderSortFields();
        t.load();
    });

    // Click on dropdown action (caret) to open/close dropdown
    $('.dropdown-action').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#short_items').toggleClass('show');
    });

    // Click on a sort field in the dropdown
    $('#short_items').on('click', '.dropdown-item', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var id = $(this).data('id');
        if (t.config.sort_dir.id == id) {
            t.config.sort_dir.dir = t.config.sort_dir.dir == 1 ? 2 : 1;
        } else {
            t.config.sort_dir.id = id;
            t.config.sort_dir.dir = 1;
        }
        $('#short_items').removeClass('show');
        t.renderSortFields();
        t.load();
    });

    $(document).on('click', function() {
        $('#short_items').removeClass('show');
    });

    $('#short_items').on('click', function(e) {
        e.stopPropagation();
    });
    $("#btnOpenFilter").on("click", function(e) {
        e.preventDefault();
        $('#changeFilterModal').modal('show');
    });

    $('#changeFilterModal').on('shown.bs.modal', function() {
        t.loadFilterDropdowns();
        $(this).find('select.filter-input').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    width: '100%',
                    dropdownParent: $(this).closest('.modal-body'),
                    allowClear: true,
                    placeholder: $(this).attr('placeholder') || 'Select...'
                });
            }
        });
        if ($('#reportrange', this).length && typeof $.fn.daterangepicker !== 'undefined') {
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
        var f = t.activeFilters || {};
        if (f.status) $('#filter_by_status').val(f.status).trigger('change');
        if (f.priority) $('#filter_by_priority').val(f.priority).trigger('change');
        if (f.requester) $('#filter_by_requester').val(f.requester).trigger('change');
        if (f.manager) $('#filter_by_manager').val(f.manager).trigger('change');
        if (f.implementer) $('#filter_by_implementer').val(f.implementer).trigger('change');
        if (f.reviewer) $('#filter_by_reviewer').val(f.reviewer).trigger('change');
        if (f.change_type) $('#filter_by_type').val(f.change_type).trigger('change');
        if (f.category) $('#filter_by_category').val(f.category).trigger('change');
        if (f.date_type && f.date_type !== 'null') $('#filter_by_date').val(f.date_type).trigger('change');
        if (f.close_state && f.close_state !== 'null') $('#filter_by_close_state').val(f.close_state).trigger('change');
        if (f.date_range && f.date_range !== '') {
            $('#daterange').val(f.date_range);
            var parts = f.date_range.split(' - ');
            if (parts.length === 2) {
                $('#reportrange span').html(moment(parts[0]).format('MMMM D, YYYY') + ' - ' + moment(parts[1]).format('MMMM D, YYYY'));
            }
        }
    });

    $(document).on('click', '#btnFilter', function(e) {
        e.preventDefault();
        t.applyFilters();
    });

    $(document).on('click', '#btnClrFilter', function(e) {
        e.preventDefault();
        t.clearFilters();
    });

    t.download = function(e) {
        e.preventDefault();
        t.cache_filter_values();
        var url = t.config.url.export_change_management + "?q=" + t.config.export_filters +"&main_filter=" + (t.config.main_filter || '');
        if (t.config.statusFilter) {
            url += "&status=" + t.config.statusFilter;
        }
        if (t.config.categoryFilter) {
            url += "&category=" + t.config.categoryFilter;
        }
        window.location = url;
    };
    t.export.on("click", t.download);
    t.searchbox.find("input").on("keydown", function(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        t.search(e);
    }
});
    t.renderSortFields();
    t.load();
};