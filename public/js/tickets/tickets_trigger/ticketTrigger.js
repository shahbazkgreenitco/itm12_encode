var TicketTrigger = function (config) {
    var t = this;
    t.config = config;
    t.http = true;
    t.problem_categories = [];
    t.sub_categories = [];

    // DOM elements
    var el = {};
    el.btn = {};
    el.csrf = $("meta[name=csrf-token]");
    el.content = $("section.content");
    el.condition_id = el.content.find('.condition_id');
    el.operator_id = el.content.find('.operator_id');
    el.condition_value = el.content.find('.condition_value');
    el.action_id = el.content.find('.action_id');
    el.status_id = el.content.find('.status_id');
    el.user_id = el.content.find('.user_id');
    el.condition_repeater = el.content.find('.condition-repeater');
    el.action_repeater = el.content.find('.action-repeater');
    el.status = el.content.find("#status");
    el.table = el.content.find("#mytable");
    el.httpPostPath = "";

    // Modal elements
    el.mdl = el.content.find("#ticket_trigger_mdl");
    el.mdl.title = el.mdl.find('.modal-title');
    el.mdl.btnSubmit = el.mdl.find('#btnSubmit');
    el.mdl.btnClear = el.mdl.find('#btnClear');
    el.mdl.frm = el.mdl.find("#ticket_trigger_form");
    el.mdl.frmEl = {};
    el.mdl.frmEl.id = el.mdl.frm.find("#id");
    el.mdl.frmEl.forAction = el.mdl.frm.find("#forAction");
    el.mdl.frmEl.title = el.mdl.frm.find("#title");
    el.mdl.frmEl.status = el.mdl.frm.find("#status");
    el.mdl.frmEl.trigger_status = el.mdl.frm.find("#trigger_status");
    el.mdl.frmEl.company = el.mdl.frm.find("#company_id");

    // Other modals
    el.descriptonMdl = el.content.find("#descriptionModal");
    el.descriptonMdl.body = el.descriptonMdl.find(".modal-body");
    el.ticketHistoryMdl = el.content.find('#ticketTriggerHistoryModal');
    el.result = el.content.find("#result");

    // Helper: validate search input
    function getSearchValue($input) {
        var val = $.trim($input.val());
        return val === "" ? false : val;
    }

    function companyBasedAjaxTransport(companySelector) {
        return function (params, success, failure) {
            let companyId = $(companySelector).val();
            if (!companyId) {
                Swal.fire({
                    toast: true,
                    icon: 'warning',
                    title: 'Please select a company first.',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });

                return;
            }
            return $.ajax(params).then(success).fail(failure);
        };
    }

    // DataTable column helpers
    el.tblHelpers = {
        actions: function () {
            return function (d) {
                var id = (typeof d === 'object' && d !== null && d.id !== undefined) ? d.id : d;
                var buttons = '';
                if (jQuery.inArray("TicketTriggerEdit", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 open-edit-modal dtActEdit" data-bs-toggle="tooltip" title="${config.translations.Edit}" data-id="${id}">
                            <svg viewBox="0 0 16 16" fill="none" width="14" height="14">
                                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    `;
                }

                if (jQuery.inArray("TicketTriggerDelete", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 open-delete dtActDel" data-bs-toggle="tooltip" title="${config.translations.Delete}" data-id="${id}">
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor">
                                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/>
                            </svg>
                        </button>
                    `;
                }
                buttons += `
                    <button class="user-list-action-btn dtActHistory me-1" data-bs-toggle="tooltip" title="${config.translations.history}" data-id="${id}">
                        <i class="bi bi-clock-history"></i>
                    </button>
                `;
                if (jQuery.inArray("TicketTriggerEdit", t.config.permissions) === -1 && jQuery.inArray("TicketTriggerDelete", t.config.permissions) === -1) {
                    el.dTbl.column(0).visible(false);
                }
                return '<div class="dt-actions btn-group" role="group">' + buttons + '</div>';
            };
        },
        alignRight: function () {
            return function (d) {
                if (d == "" || d == null) return null;
                return '<div class="text-right">' + d + '</div>';
            }
        }
    };

    // DataTable initialization
    el.dTbl = el.table.DataTable({
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
        scrollX:true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        aoColumnDefs: [{
            targets: 6,
            bSortable: false,
            render: el.tblHelpers.actions()
        }],
        order: [[5, 'desc']],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.getTicketTriggers,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
            }
        },
        columns: [
            { data: 'company_name' },
            {
                data: 'name',
                render: function (data, type, row) {
                    if (!data) return '';
                    data = String(data);
                    if (data.length > 20) {
                        var truncated = truncateHtml(data, 20);
                        return '<div data-bs-toggle="tooltip" title="' + escapeHtml(data) + '">' +
                            escapeHtml(truncated) + '...' +
                            '</div>';
                    } else {
                        return data;
                    }
                }
            },
            {
                data: 'description',
                render: function (data, type, row) {
                    if (!data) return '';
                    data = String(data);
                    if (data.length > 30) {
                        var truncated = truncateHtml(data, 30);
                        return truncated + '<a class="read-more" style="cursor:pointer" data-full-text="' + escapeHtml(data) + '">...' + t.config.translations.read_more + '</a>';
                    } else {
                        return data;
                    }
                }
            },
            { data: 'status' },
            { data: 'last_created_at' },
            { data: 'last_updated_at' },
            { data: 'id' },
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            // External search
            var $searchInput = $(".plain-search");
            var $searchButton = $(".btn-search-mail-list");
            function doSearch() {
                var v = getSearchValue($searchInput);
                if (v === false) {
                    api.search('').draw();
                    return false;
                }
                api.search(v).draw();
            }
            $searchInput.off("keyup.search").on("keyup.search", function (e) {
                if (e.keyCode === 13 ) doSearch();
            });
            $searchButton.off("click.search").on("click.search", doSearch);

            // Page length
            var $lengthSelect = $(".userModulePageLenth");
            $lengthSelect.off("change.length").on("change.length", function () {
                api.page.len(parseInt($(this).val(), 10)).draw();
            });
            api.page.len(parseInt($lengthSelect.val(), 10)).draw();

            // Refresh button
            $(".btn-reload-list").off("click.refresh").on("click.refresh", function () {
                var v = getSearchValue($searchInput);
                if (v !== false) api.search(v).draw();
                else api.ajax.reload();
            });

            el.btn.add = el.content.find(".btn-add-trigger");

            // Hide DataTable default controls
            $("#mytable_filter, #mytable_length").hide();

            // Read more modal
            el.content.on("click", ".read-more", function (e) {
                e.preventDefault();
                var fullText = $(this).data("full-text");
                $(el.descriptonMdl.body).html(fullText);
                $(el.descriptonMdl).modal("show");
            });
       
        },
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });

    // Helper functions for text truncation and escaping
    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) return html;
        return text.substring(0, maxLength);
    }

    function escapeHtml(text) {
        return text.replace(/[&<>"'`=\/]/g, function (s) {
            return entityMap[s];
        });
    }

    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
        "/": "&#x2F;",
        "`": "&#x60;",
        "=": "&#x3D;"
    };

    // Reload function
    t.reload = function () {
        var $searchInput = $(".plain-search");
        var v = getSearchValue($searchInput);
        if (v !== false) {
            el.dTbl.search(v).draw();
            return;
        }
        el.dTbl.ajax.reload();
    };

    // Validation rules
    t.frmValidator = el.mdl.frm.validate({
        onsubmit: false,
        rules: {
            company_id: { required: true },
            name: { required: true, maxlength: 100, clean_text_only: true },
            description: { required: true, maxlength: 500, clean_text_only: true },
            matchType: { required: true }
        },
        messages: {
            company_id: {
                required: 'This field is required.'
            },
            name: {
                required: 'This field is required.',
                maxlength: 'Title cannot exceed 100 characters.'
            },
            description: {
                required: 'This field is required.',
                maxlength: 'Description cannot exceed 500 characters.'
            },
            matchType: {
                required: 'Please select a match type.'
            }
        },
        errorElement: 'div',
        errorClass: 'error_trigger',
        errorPlacement: function (error, element) {
            var $container = element.closest('.input-group').parent().find('.error-container');
            if ($container.length) {
                $container.empty().append(error);
            } else {
                var $fallbackContainer = element.closest('.col-12, .col-md-4, .col-md-3, .col-md-7').find('.error-container');
                if ($fallbackContainer.length) {
                    $fallbackContainer.empty().append(error);
                } else {
                    error.insertAfter(element.closest('.input-group') || element.parent());
                }
            }
        },
        highlight: function (element) {
            $(element).closest('.input-group').addClass('has-error');
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).closest('.input-group').removeClass('has-error');
            $(element).removeClass('is-invalid');
            var $container = $(element).closest('.input-group').parent().find('.error-container');
            if ($container.length) {
                $container.empty();
            }
        }
    });

    // =============================================
    // HELPER FUNCTIONS
    // =============================================

    function parseActionValue(actionValue) {
        if (!actionValue) return {};
        if (typeof actionValue === 'string') {
            try {
                return JSON.parse(actionValue);
            } catch (e) {
                console.log('Error parsing action value:', e);
                return {};
            }
        }
        if (typeof actionValue === 'object') {
            return actionValue;
        }
        return {};
    }

    function getActionId(action) {
        if (!action) return null;
        if (action.pivot && action.pivot.action_id) return action.pivot.action_id;
        if (action.action_id) return action.action_id;
        if (action.id) return action.id;
        return null;
    }

    function getConditionId(condition) {
        if (!condition) return null;
        if (condition.pivot && condition.pivot.condition_id) return condition.pivot.condition_id;
        if (condition.condition_id) return condition.condition_id;
        if (condition.id) return condition.id;
        return null;
    }

    function getOperatorId(condition) {
        if (!condition) return null;
        if (condition.pivot && condition.pivot.operator_id) return condition.pivot.operator_id;
        if (condition.operator_id) return condition.operator_id;
        return null;
    }

    function getConditionValue(condition) {
        if (!condition) return '';
        if (condition.pivot && condition.pivot.condition_value) return condition.pivot.condition_value;
        if (condition.condition_value) return condition.condition_value;
        return '';
    }

    function getMatchType(condition) {
        if (!condition) return 'all';
        if (condition.pivot && condition.pivot.match_type) return condition.pivot.match_type;
        if (condition.match_type) return condition.match_type;
        return 'all';
    }

    // Add Trigger
    t.addTicketTrigger = function (e) {
        e.preventDefault();
        t.resetFrm();
        var default_company_id = $("#config-company").val();
        var default_company_text = $("#config-company option:selected").text();
        if (default_company_id != 0 && default_company_id != null) {
            let option = new Option(default_company_text, default_company_id, true, true);
            el.mdl.frmEl.company.empty().append(option).trigger("change");
        } else {
            el.mdl.frmEl.company.val("").trigger("change");
        }
        el.httpPostPath = t.config.url.save;
        el.mdl.title.html(t.config.translations.add_new_trigger);
        el.mdl.btnSubmit.text(t.config.translations.save);
        el.mdl.frmEl.forAction.val("add");
        el.mdl.modal("show");
    };

    t.export = function (e) {
        e.preventDefault();
        var v = $.trim($(".plain-search").val());
        window.location = t.config.url.download_url + escape(v);
    };

    // Initial DataTable reload
    if (el.btn.add && el.btn.add.length) {
        el.btn.add.on("click", $.proxy(t.addTicketTrigger));
    }
    el.dTbl.ajax.reload();

    // Destroy Select2
    function destroyAllSelect2($container) {
        if (!$container || !$container.length) return;
        $container.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
        $container.find('.select2-container').remove();
        $container.find('select').removeClass('select2-hidden-accessible');
        $container.find('select').removeAttr('data-select2-id');
    }

    // Select2 initializations
    function initConditionSelect2($scope) {
        if (!$scope || !$scope.length) return;

        $scope.find('.condition_id, .operator_id').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $scope.find('.select2-container').remove();
        $scope.find('select').removeClass('select2-hidden-accessible');
        $scope.find('select').removeAttr('data-select2-id');

        var $conditionId = $scope.find('.condition_id').select2({
            width: "100%",
            placeholder: config.translations.Events || "Select Event",
            dropdownParent: $scope.closest('.modal-content'),
            allowClear: true
        });

        var $operatorId = $scope.find('.operator_id').select2({
            width: "100%",
            placeholder: config.translations.Conditions || "Select Condition",
            dropdownParent: $scope.closest('.modal-content'),
            allowClear: true,
            ajax: {
                url: function () {
                    return t.config.url.getOperators;
                },
                dataType: "json",
                delay: 250,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page,
                        condition_id: $conditionId.val()
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total
                        }
                    };
                },
                cache: true
            }
        });

        $conditionId.off('change.select2Condition').on('change.select2Condition', function () {
            $operatorId.val(null).trigger('change');
            $scope.find('.condition_value').val('');
        });
    }

    function initActionSelect2($item) {
        if (!$item || !$item.length) return;

        $item.find('select').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        $item.find('.select2-container').remove();
        $item.find('select').removeClass('select2-hidden-accessible');
        $item.find('select').removeAttr('data-select2-id');

        var $modalContent = $item.closest('.modal-content');

        $item.find('.action_id').select2({
            width: '100%',
            dropdownParent: $modalContent,
            placeholder: t.config.translations.select_actions,
            allowClear: true
        });

        $item.find('.status_id').select2({
            width: '100%',
            dropdownParent: $modalContent,
            placeholder: 'Search and select status',
            allowClear: true,
            ajax: {
                url: t.config.url.getStatusByAjax,
                dataType: "json",
                transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                data: function (params) {
                    return {
                        search: params.term || '',
                        page: params.page || 1,
                        company_id: el.mdl.frmEl.company.val(),
                        context: 'ticket-trigger-status'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
            },
            templateResult: function(data) {
                if (data.loading) {
                    return data.text;
                }
                return $('<span>').text(data.text);
            },
            templateSelection: function(data) {
                return data.text || data.id;
            }
        });

        $item.find('.user_id').select2({
            width: '100%',
            dropdownParent: $modalContent,
            ajax: {
                url: t.config.url.getUserByQuery,
                dataType: "json",
                transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        company_id: el.mdl.frmEl.company.val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data.items || data.data || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                }
            }
        });

        $item.find('.department_id').select2({
            width: '100%',
            dropdownParent: $modalContent,
            ajax: {
                url: t.config.url.getDepartmentsWithCompanyByQuery,
                dataType: "json",
                transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1,
                        company_id: el.mdl.frmEl.company.val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || data.items || data.data || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                }
            }
        });

        $item.find('.problem_category_id').select2({
            width: '100%',
            dropdownParent: $modalContent
        });

        $item.find('.sub_category_id').select2({
            width: '100%',
            dropdownParent: $modalContent
        });

        $item.find('.apidefination_id').select2({
            width: '100%',
            dropdownParent: $modalContent
        });

        $item.find('.priority_id').select2({
            width: '100%',
            dropdownParent: $modalContent
        });
    }

    // Delete buttons visibility
    function updateConditionDeleteButtons() {
        if (!el.condition_repeater.length) return;
        const items = el.condition_repeater.find('[data-repeater-item]');
        items.find('[data-repeater-delete]').show();
        if (items.length) {
            items.first().find('[data-repeater-delete]').hide();
        }
    }

    function updateActionDeleteButtons() {
        if (!el.action_repeater.length) return;
        const items = el.action_repeater.find('[data-repeater-item]');
        items.find('[data-repeater-delete]').show();
        if (items.length) {
            items.first().find('[data-repeater-delete]').hide();
        }
    }

    // Form reset
    t.resetFrm = function () {
        t.frmValidator.resetForm();
        el.mdl.frm.trigger("reset");
        el.mdl.frmEl.forAction.val("");
        el.mdl.frm.find("select[name='status']").trigger('change');

        el.mdl.frm.find('.error-container').empty();
        el.mdl.frm.find('.has-error').removeClass('has-error');
        el.mdl.frm.find('.is-invalid').removeClass('is-invalid');

        if (el.condition_repeater.length) {
            var $conditionItems = el.condition_repeater.find('[data-repeater-item]');
            if ($conditionItems.length > 1) {
                $conditionItems.slice(1).each(function () {
                    destroyAllSelect2($(this));
                    $(this).remove();
                });
            }
            var $firstCondition = $conditionItems.first();
            if ($firstCondition.length) {
                $firstCondition.find('.condition_id, .operator_id, .condition_value').val('');
                destroyAllSelect2($firstCondition);
                initConditionSelect2($firstCondition);
            }
            updateConditionDeleteButtons();
        }

        if (el.action_repeater.length) {
            var $actionItems = el.action_repeater.find('[data-repeater-item]');
            if ($actionItems.length > 1) {
                $actionItems.slice(1).each(function () {
                    destroyAllSelect2($(this));
                    $(this).remove();
                });
            }
            var $firstAction = $actionItems.first();
            if ($firstAction.length) {
                $firstAction.find('.action_id, .status_id, .user_id, .department_id, .problem_category_id, .sub_category_id, .apidefination_id, .priority_id').val('');
                $firstAction.find('.actions').addClass('d-none');
                $firstAction.find('.actions input, .actions select, .actions textarea').prop('disabled', true);
                destroyAllSelect2($firstAction);
                initActionSelect2($firstAction);
            }
            updateActionDeleteButtons();
        }
    };

    // Populate action fields
    function populateActionFields($item, actionId, actionVal, index, triggerData) {
        if (!$item || !$item.length) return;

        $item.find('.actions').addClass('d-none');
        $item.find('.actions input, .actions select, .actions textarea').prop('disabled', true);

        var actionIdStr = String(actionId);

        switch (actionIdStr) {
            case '2': // Mail
                $item.find('.mail').removeClass('d-none');
                $item.find('.mail_content').removeClass('d-none');
                $item.find('.mail .user_id, .mail_content input, .mail_content textarea').prop('disabled', false);

                var agentId = actionVal.agent_id || actionVal.user_id || actionVal.assignee_id || null;
                var subject = actionVal.subject || actionVal.email_subject || null;
                var message = actionVal.message || actionVal.body || actionVal.email_body || null;

                if (agentId) $item.find('.mail .user_id').val(agentId).trigger('change');
                if (subject) $item.find('.mail_content .subject').val(subject);
                if (message) $item.find('.mail_content .message').val(message);
                break;

            case '3': // Note
                $item.find('.note').removeClass('d-none');
                $item.find('.note textarea').prop('disabled', false);

                var noteText = actionVal.note_text || actionVal.note || actionVal.content || actionVal.text || null;
                if (noteText) {
                    $item.find('.note textarea').val(noteText);
                }
                break;

            case '4': // Status
                $item.find('.status').removeClass('d-none');
                $item.find('.status .status_id').prop('disabled', false);

                var statusValue = actionVal.status_name || actionVal.status_id || actionVal.status || actionVal.id || null;
                if (statusValue) {
                    $item.find('.status .status_id').val(statusValue).trigger('change');
                }
                break;

            case '5': // Assign To
                $item.find('.assign_to').removeClass('d-none');
                $item.find('.assign_to .user_id').prop('disabled', false);

                var userId = actionVal.agent_id || actionVal.user_id || actionVal.assignee_id || actionVal.id || null;
                if (userId) $item.find('.assign_to .user_id').val(userId).trigger('change');
                break;

            case '9': // Department & Category
                $item.find('.department').removeClass('d-none');
                $item.find('.category_fields').removeClass('d-none');
                $item.find('.department .department_id, .category_fields select').prop('disabled', false);

                var deptId = actionVal.department_id || actionVal.dept_id || actionVal.department || null;
                if (deptId) $item.find('.department .department_id').val(deptId).trigger('change');

                if (triggerData && triggerData.actions && triggerData.actions[index]) {
                    var actionData = triggerData.actions[index];

                    if (actionData.problem_categories && Array.isArray(actionData.problem_categories)) {
                        var $catField = $item.find('.category_fields .problem_category_id');
                        $catField.empty().append('<option value="">Select Category</option>');
                        $.each(actionData.problem_categories, function (i, cat) {
                            $catField.append(new Option(cat.name, cat.id));
                        });

                        var catId = actionVal.problem_category_id || actionVal.category_id || actionVal.category || null;
                        if (catId) {
                            $catField.val(catId).trigger('change');
                        }
                    }

                    var subCatId = actionVal.sub_category_id || actionVal.subcategory_id || actionVal.sub_category || null;
                    if (subCatId) {
                        setTimeout(function () {
                            $item.find('.sub_category_id').val(subCatId).trigger('change');
                        }, 300);
                    }
                }
                break;

            case '10': // API Definition
                $item.find('.apidefination').removeClass('d-none');
                $item.find('.apidefination .apidefination_id').prop('disabled', false);

                var apiId = actionVal.apidefination || actionVal.apidefination_id || actionVal.api_id || actionVal.api || null;
                if (apiId) $item.find('.apidefination .apidefination_id').val(apiId).trigger('change');
                break;

            case '11': // Priority
                $item.find('.priority').removeClass('d-none');
                $item.find('.priority .priority_id').prop('disabled', false);

                var priorityValue = actionVal.priority_name || actionVal.priority_id || actionVal.priority || actionVal.id || null;
                if (priorityValue) $item.find('.priority .priority_id').val(priorityValue).trigger('change');
                break;

            default:
                break;
        }
    }

    // Add validation rules
    function addActionValidationRules($item, actionId) {
        if (!$item || !$item.length) return;

        $item.find('.actions input, .actions select, .actions textarea').each(function () {
            $(this).rules('remove');
        });

        switch (String(actionId)) {
            case '2': // Mail
                $item.find('.mail .user_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a user." }
                });
                $item.find('.mail_content .subject').rules("add", {
                    required: true,
                    messages: { required: "Subject is required." }
                });
                $item.find('.mail_content .message').rules("add", {
                    required: true,
                    messages: { required: "Message is required." }
                });
                break;
            case '3': // Note
                $item.find('.note textarea').rules("add", {
                    required: true,
                    messages: { required: "Note text is required." }
                });
                break;
            case '4': // Status
                $item.find('.status .status_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a status." }
                });
                break;
            case '5': // Assign To
                $item.find('.assign_to .user_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a user to assign." }
                });
                break;
            case '9': // Department & Category
                $item.find('.department .department_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a department." }
                });
                $item.find('.category_fields .problem_category_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a category." }
                });
                if (!$item.find('.prob_sub_cat').hasClass('d-none')) {
                    $item.find('.category_fields .sub_category_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a sub category." }
                    });
                }
                break;
            case '10': // API Definition
                $item.find('.apidefination .apidefination_id').rules("add", {
                    required: true,
                    messages: { required: "Please select an API definition." }
                });
                break;
            case '11': // Priority
                $item.find('.priority .priority_id').rules("add", {
                    required: true,
                    messages: { required: "Please select a priority." }
                });
                break;
        }
    }

    // Load form for edit
    t.loadForm = function (trigger, forAction) {

        t.resetFrm();

        // Company - Check multiple possible structures
        el.mdl.frmEl.company.empty().val("").trigger("change");
        var companyData = trigger.company || trigger.company_data || null;
        if (companyData && typeof companyData == "object") {
            var companyId = companyData.id || companyData.company_id || null;
            var companyName = companyData.name || companyData.company_name || null;
            if (companyId && companyName) {
                el.mdl.frmEl.company.select2('trigger', 'select', {
                    data: { text: companyName, id: companyId, selected: true }
                });
            }
        }

        if (forAction && forAction != "") {
            el.mdl.frmEl.forAction.val(forAction);
            if (forAction == "edit") {
                $('<input>').attr('type', 'hidden').attr('name', 'id').appendTo(el.mdl.frm);
                el.mdl.frm.find("input[name='id']").val(trigger.id);

                // Check match_type from multiple possible locations
                if (trigger.conditions && trigger.conditions[0]) {
                    var matchType = getMatchType(trigger.conditions[0]);
                    if (matchType == 'all') {
                        el.mdl.frm.find("input[value='all']").prop('checked', true);
                    }
                    if (matchType == 'any') {
                        el.mdl.frm.find("input[value='any']").prop('checked', true);
                    }
                }
            }
        }

        var conditions = trigger.conditions || [];
        var actions = trigger.actions || [];

        // Set basic fields
        el.mdl.frm.find("input[name='name']").val(trigger.name || trigger.title || '');
        el.mdl.frm.find("textarea[name='description']").val(trigger.description || trigger.desc || '');
        el.mdl.frm.find("select[name='status']").val(trigger.status || trigger.status_id || 1).trigger('change');

        // ========== POPULATE CONDITIONS ==========
        var conditionData = [];
        $.each(conditions, function (key, value) {
            conditionData.push({
                condition_id: getConditionId(value),
                operator_id: getOperatorId(value),
                condition_value: getConditionValue(value),
                matchType: getMatchType(value),
            });
        });

        // Set conditions list
        el.condition_repeater.setList(conditionData);

        // ========== POPULATE ACTIONS ==========
        var actionData = [];
        $.each(actions, function (key, value) {
            actionData.push({
                action_id: getActionId(value),
                action_value: value.pivot?.action_value || value.action_value || '{}',
            });
        });

        // Set actions list
        el.action_repeater.setList(actionData);

        // ========== POPULATE ACTION FIELDS (Dynamic values) ==========
        $.each(actions, function (key, value) {
            var action_id = getActionId(value);
            var action_val = parseActionValue(value.pivot?.action_value || value.action_value || '{}');

            switch (parseInt(action_id)) {
                case 1:
                    break;
                case 2: // Mail
                    el.action_repeater.find('.mail:eq(' + key + ')').removeClass('hide');
                    el.action_repeater.find('.mail_content:eq(' + key + ')').removeClass('hide');
                    el.action_repeater.find('.mail:eq(' + key + ')').find('.form-control').prop('disabled', false);
                    el.action_repeater.find('.mail_content:eq(' + key + ')').find('.form-control').prop('disabled', false);
                    var $mailSelect = el.action_repeater.find('.mail:eq(' + key + ')').find('select');
                    if (action_val.mail_user) {
                        var option = new Option(action_val.mail_user.text, action_val.mail_user.id, true, true);
                        $mailSelect.empty().append(option).trigger('change');
                    }
                    el.action_repeater.find('.mail_content:eq(' + key + ')').find('.form-control.subject').val(action_val.subject || '');
                    el.action_repeater.find('.mail_content:eq(' + key + ')').find('.form-control.message').val(action_val.message || '');
                    break;
                case 3: // Note
                    el.action_repeater.find('.note:eq(' + key + ')').removeClass('hide');
                    el.action_repeater.find('.note:eq(' + key + ')').find('.form-control').prop('disabled', false);
                    el.action_repeater.find('.note:eq(' + key + ')').find('.form-control').val(action_val.note_text || action_val.note || '');
                    break;
                case 4: // Status
                    el.action_repeater.find('.status:eq(' + key + ')').removeClass('hide');
                    var $statusSelect = el.action_repeater.find('.status:eq(' + key + ')').find('select.status_id');
                    $statusSelect.prop('disabled', false);
                    if (action_val.status_data) {
                        var option = new Option(action_val.status_data.text, action_val.status_data.id, true, true);
                        $statusSelect.empty().append(option).trigger('change');
                    }
                    break;
                case 5: // Assign To
                    el.action_repeater.find('.assign_to:eq(' + key + ')').removeClass('hide');
                    var $assignSelect = el.action_repeater.find('.assign_to:eq(' + key + ')').find('select');
                    $assignSelect.prop('disabled', false);
                    if (action_val.assign_user) {
                        var option = new Option(action_val.assign_user.text, action_val.assign_user.id, true, true);
                        $assignSelect.empty().append(option).trigger('change');
                    }
                    break;
                case 9: // Department & Category

                    el.action_repeater.find('.department:eq(' + key + ')').removeClass('hide');
                    el.action_repeater.find('.category_fields:eq(' + key + ')').removeClass('hide');

                    var $department = el.action_repeater.find('.department:eq(' + key + ')').find('select');
                    var $category = el.action_repeater.find('.category_fields:eq(' + key + ')').find('.problem_category_id');
                    var $subcategory = el.action_repeater.find('.category_fields:eq(' + key + ')').find('.sub_category_id');
                    var $subWrapper = el.action_repeater.find('.category_fields:eq(' + key + ')').find('.prob_sub_cat');

                    $department.prop('disabled', false);
                    $category.prop('disabled', false);
                    $subcategory.prop('disabled', false);

                    // Department
                    if (action_val.department_data) {
                        var deptOption = new Option(action_val.department_data.text,action_val.department_data.id,true,true);
                        $department.empty().append(deptOption).trigger('change');
                    }

                    // Category
                    if (action_val.problem_category_data) {
                        var catOption = new Option(action_val.problem_category_data.text,action_val.problem_category_data.id,true,true);
                        $category.empty().append(catOption).trigger('change');
                    }

                    // Sub Category
                    if (action_val.sub_category_data) {
                        $subWrapper.removeClass('d-none');
                        setTimeout(function () {
                            var subOption = new Option(action_val.sub_category_data.text,action_val.sub_category_data.id,true,true);
                            $subcategory.off('change');
                            $subcategory.empty().append(subOption).trigger('change');
                        }, 2000);
                    }
                    break;
                case 10: // API Definition
                    el.action_repeater.find('.apidefination:eq(' + key + ')').removeClass('hide');
                    el.action_repeater.find('.apidefination:eq(' + key + ')').find('.form-control').prop('disabled', false);
                    el.action_repeater.find('.apidefination:eq(' + key + ')').find('.form-control').val(action_val.apidefination || action_val.api || '').trigger('change');
                    break;
                case 11: // Priority
                    el.action_repeater.find('.priority:eq(' + key + ')').removeClass('hide');
                    var $prioritySelect = el.action_repeater.find('.priority:eq(' + key + ')').find('select.priority_id');
                    $prioritySelect.prop('disabled', false);
                    if (action_val.priority_name) {
                        $prioritySelect.val(action_val.priority_name).trigger('change');
                    }
                    break;
                default:
                    break;
            }
        });

        updateConditionDeleteButtons();
        updateActionDeleteButtons();

        el.mdl.modal("show");
    };

    // Submit handler
    t.handleSubmit = function (e) {
        e.preventDefault();

        el.mdl.frm.find('.error-container').empty();
        el.mdl.frm.find('.has-error').removeClass('has-error');
        el.mdl.frm.find('.is-invalid').removeClass('is-invalid');

        el.condition_repeater.find('[data-repeater-item]').each(function () {
            var $item = $(this);
            $item.find('.condition_id').rules("add", {
                required: true,
                messages: { required: "This field is required." }
            });
            $item.find('.operator_id').rules("add", {
                required: true,
                messages: { required: "This field is required." }
            });
            $item.find('.condition_value').rules("add", {
                required: true,
                messages: { required: "This field is required." }
            });
        });

        el.action_repeater.find('[data-repeater-item]').each(function () {
            var $item = $(this);
            var actionId = $item.find('.action_id').val();

            $item.find('.action_id').rules("add", {
                required: true,
                messages: { required: "Please select an action." }
            });

            switch (actionId) {
                case '2': // Mail
                    $item.find('.mail .user_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a user." }
                    });
                    $item.find('.mail_content .subject').rules("add", {
                        required: true,
                        messages: { required: "Subject is required." }
                    });
                    $item.find('.mail_content .message').rules("add", {
                        required: true,
                        messages: { required: "Message is required." }
                    });
                    break;
                case '3': // Note
                    $item.find('.note textarea').rules("add", {
                        required: true,
                        messages: { required: "Note text is required." }
                    });
                    break;
                case '4': // Status
                    $item.find('.status .status_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a status." }
                    });
                    break;
                case '5': // Assign To
                    $item.find('.assign_to .user_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a user to assign." }
                    });
                    break;
                case '9': // Department & Category
                    $item.find('.department .department_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a department." }
                    });
                    $item.find('.category_fields .problem_category_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a category." }
                    });
                    if (!$item.find('.prob_sub_cat').hasClass('d-none')) {
                        $item.find('.category_fields .sub_category_id').rules("add", {
                            required: true,
                            messages: { required: "Please select a sub category." }
                        });
                    }
                    break;
                case '10': // API Definition
                    $item.find('.apidefination .apidefination_id').rules("add", {
                        required: true,
                        messages: { required: "Please select an API definition." }
                    });
                    break;
                case '11': // Priority
                    $item.find('.priority .priority_id').rules("add", {
                        required: true,
                        messages: { required: "Please select a priority." }
                    });
                    break;
            }
        });

        if (t.frmValidator.form() == false) {
            var $firstError = $('.error-container .help-block:first, .error-container .error:first');
            if ($firstError.length) {
                var $modalBody = el.mdl.find('.modal-body');
                $modalBody.animate({
                    scrollTop: $firstError.closest('.row').offset().top - $modalBody.offset().top + $modalBody.scrollTop() - 20
                }, 500);
            }
            return false;
        }

        if (t.http != true) return false;

        var fd = new FormData(el.mdl.frm[0]);
        t.http = false;
        var url = t.config.url.saveTicketTrigger;
        if (el.mdl.frmEl.forAction.val() == 'edit') {
            url = t.config.url.updateTicketTrigger;
        }

        $.ajax({
            url: url,
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status != "success") {
                    sweetAlert('center', 'error', res);
                    return;
                }
                sweetAlert('center', 'success', res);
                el.mdl.modal('hide');
                el.dTbl.ajax.reload();
            },
            error: function (xhr) {
                sweetAlert('center', 'error', { msg: 'Something went wrong!' });
            }
        }).always(function () { t.http = true; });
    };

    // Edit trigger
    t.editTrigger = function (e) {
        e.preventDefault();
        var triggetId = $(this).attr("data-id");
        $.get(t.config.url.editTicketTrigger, { id: triggetId })
            .done(function (data) {
                if (typeof data == "object" && data.status == "success") {
                    el.mdl.title.html(config.translations.edit_trigger);
                    el.mdl.btnSubmit.text(t.config.translations.save);
                    el.mdl.frmEl.forAction.val("edit");
                    t.loadForm(data.data, "edit");
                } else {
                    sweetAlert('center', 'error', data);
                }
            })
            .fail(function () {
                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
            });
    };

    // Delete trigger
    t.removeTrigger = function (e) {
        e.preventDefault();
        var triggetId = $(this).attr("data-id");
        var formData = new FormData();
        formData.append('_token', el.csrf.attr('content'));
        formData.append('id', triggetId);
        sweetAlertConfirmation({
            message: config.translations.delete_trigger,
            onConfirm: function () {
                $.ajax({
                    url: t.config.url.removeTicketTrigger,
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        if (res.status != "success") {
                            sweetAlert('center', 'error', res);
                            return;
                        }
                        sweetAlert('center', 'success', res);
                        el.dTbl.ajax.reload();
                    }
                }).always(function () { t.http = true; });
            }
        });
    };

    // =============================================
    // HISTORY FUNCTIONS
    // =============================================
    t.showHistoryLoader = function ($container) {
        if (!$container || !$container.length) {
            return;
        }

        // Prevent duplicate loader
        if ($container.find(".history-loader-wrapper").length) {
            return;
        }

        var loaderHtml = `
            <div class="card-loader-wrapper pt-5"
                style="
                    position:absolute;
                    top:0;
                    left:0;
                    right:0;
                    bottom:0;
                    z-index:999;
                    background:rgba(255,255,255,.7);
                    display:flex;
                    align-items:center;
                    justify-content:center;
                ">
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
                </div>
            </div>
        `;

        // Inject keyframe once
        if (!document.getElementById("history-loader-style")) {
            $("head").append(`
            <style id="history-loader-style">
                @keyframes historySpin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            </style>
        `);
        }

        $container.append(loaderHtml);
    };
    t.triggerHistory = function (e) {
        e.preventDefault();
        var trigger_id = $(this).attr('data-id');
        var history_url = t.config.url.history_page + "/" + trigger_id;

        var $historyModal = $('#ticketTriggerHistoryModal');
        var $historyContainer = $historyModal.find('#ticket_history_container');
        var $subjectField = $historyModal.find('#ticket_details_subject');

        $historyContainer.html('');
        $subjectField.text('');
        t.showHistoryLoader($historyContainer);

        $.ajax({
            url: history_url,
            type: "POST",
            data: { _token: t.config.token },
            success: function (response) {
                var historyData = response.data || [];
                console.log('History Data:', historyData);

                if (historyData.length > 0 && historyData[0].trigger_name) {
                    $subjectField.text(historyData[0].trigger_name || '');
                    $subjectField.attr('data-original-title', historyData[0].trigger_name || '');
                } else {
                    $subjectField.text('N/A');
                }

                var historyHtml = '';
                if (historyData.length > 0) {
                    $.each(historyData, function (index, value) {
                        historyHtml += t.buildHistoryItem(value, index);
                    });
                } else {
                    historyHtml = '<div class="text-center p-4"><p class="text-muted">No history available.</p></div>';
                }

                $historyContainer.html(historyHtml);
                $historyContainer.find('[data-toggle="tooltip"]').tooltip();
            },
            error: function () {
                $historyContainer.html('<div class="text-center py-4 text-danger">Failed to load history.</div>');
            }
        });

        $historyModal.modal('show');
    };

    t.buildHistoryItem = function (value, index) {
        var actionHtml = '';
        var badgeClass = 'green';
        var badgeText = t.getTimeAgoText(value.updated_at_formatted || value.created_at_formatted);

        // Get user name
        var userName = value.updated_by || value.created_by || 'System';
        var userId = value.user_id || 0;

        // Get avatar
        var avatarHtml = t.getAvatar(userName, value.avatar);

        // Build action message based on action_type
        switch (value.action_type) {
            case 1: // Create trigger
                actionHtml = 'Ticket Trigger Created Successfully';
                break;
            case 2: // Edit trigger name
                actionHtml = 'Name Changed From <b>' + (value.old_name || '') + '</b> To <b>' + (value.new_name || '') + '</b>';
                break;
            case 3: // Edit trigger description
                actionHtml = 'Description Changed From <b>' + (value.old_description || '') + '</b> To <b>' + (value.new_description || '') + '</b>';
                break;
            case 4: // Edit trigger status
                var oldStatus = value.old_status == 1 ? 'Enabled' : 'Disabled';
                var newStatus = value.new_status == 1 ? 'Enabled' : 'Disabled';
                actionHtml = 'Status Changed From <b>' + oldStatus + '</b> To <b>' + newStatus + '</b>';
                break;
            case 5: // Edit match type
                var oldMatch = value.old_match_type == 'all' ? 'AND' : 'OR';
                var newMatch = value.new_match_type == 'all' ? 'AND' : 'OR';
                actionHtml = 'Condition Type Changed From <b>' + oldMatch + '</b> To <b>' + newMatch + '</b>';
                break;
            case 6: // Event change
                actionHtml = 'Condition Event Changed From <b>' + (value.old_event || '') + '</b> To <b>' + (value.new_event || '') + '</b> on <b>row ' + (value.row || '') + '</b>';
                break;
            case 7: // Operator change
                actionHtml = 'Condition Operator Changed From <b>' + (value.old_operator || '') + '</b> To <b>' + (value.new_operator || '') + '</b> on <b>row ' + (value.row || '') + '</b>';
                break;
            case 8: // Condition value change
                actionHtml = 'Condition Value Changed From <b>' + (value.old_condition_value || '') + '</b> To <b>' + (value.new_condition_value || '') + '</b> on <b>row ' + (value.row || '') + '</b>';
                break;
            case 9: // Condition added
                actionHtml = 'Condition <b>' + (value.new_event || '') + '</b> added';
                break;
            case 10: // Condition removed
                actionHtml = 'Condition <b>' + (value.old_event || '') + '</b> removed';
                break;
            case 11: // Action added
                actionHtml = 'Action <b>' + (value.new_action_name || '') + '</b> added';
                break;
            case 12: // Action removed
                actionHtml = 'Action <b>' + (value.old_action_name || '') + '</b> removed';
                break;
            case 13: // Action changed
                actionHtml = 'Action Changed From <b>' + (value.old_action_name || '') + '</b> To <b>' + (value.new_action_name || '') + '</b>';
                break;
            case 14: // Action value changed
                var oldVal = value.old_action_value ? JSON.parse(value.old_action_value) : {};
                var newVal = value.new_action_value ? JSON.parse(value.new_action_value) : {};

                if (value.old_action_name) {
                    if (value.old_action_name == 'add_note_to_ticket') {
                        actionHtml = 'Note text changed from <b>' + (oldVal.note_text || '') + '</b> To <b>' + (newVal.note_text || '') + '</b>';
                    } else if (value.old_action_name == 'send_mail') {
                        if (oldVal.agent_id != newVal.agent_id) {
                            actionHtml = 'Mail User changed from <b>' + (value.old_mail_userName || '') + '</b> To <b>' + (value.new_mail_userName || '') + '</b>';
                        } else if (oldVal.subject != newVal.subject) {
                            actionHtml = 'Mail Subject changed from <b>' + (oldVal.subject || '') + '</b> To <b>' + (newVal.subject || '') + '</b>';
                        } else if (oldVal.message != newVal.message) {
                            actionHtml = 'Mail Message changed';
                        }
                    } else if (value.old_action_name == 'status_update') {
                        actionHtml = 'Status changed from <b>' + (value.old_status_name || '') + '</b> To <b>' + (value.new_status_name || '') + '</b>';
                    } else if (value.old_action_name == 'assign_ticket') {
                        actionHtml = 'Assigned User changed from <b>' + (value.old_mail_userName || '') + '</b> To <b>' + (value.new_mail_userName || '') + '</b>';
                    } else if (value.old_action_name == 'dept_category') {
                        if (oldVal.department_id != newVal.department_id) {
                            actionHtml = 'Department changed from <b>' + (value.old_department_name || '') + '</b> To <b>' + (value.new_department_name || '') + '</b>';
                        } else if (oldVal.problem_category_id != newVal.problem_category_id) {
                            actionHtml = 'Category changed from <b>' + (value.old_pc || '') + '</b> To <b>' + (value.new_pc || '') + '</b>';
                        } else if (oldVal.sub_category_id != newVal.sub_category_id) {
                            actionHtml = 'Sub Category changed from <b>' + (value.old_sub_pc || '') + '</b> To <b>' + (value.new_sub_pc || '') + '</b>';
                        }
                    } else if (value.old_action_name == 'api_call') {
                        actionHtml = 'API Definition changed from <b>' + (value.old_api_name || '') + '</b> To <b>' + (value.new_api_name || '') + '</b>';
                    } else {
                        actionHtml = 'Action <b>' + (value.old_action_name || '') + '</b> value changed';
                    }
                } else {
                    actionHtml = 'Action value changed';
                }
                break;
            case 16: // Other changes
                actionHtml = value.other_message || 'Updated ticket trigger';
                break;
            default:
                actionHtml = 'Updated ticket trigger';
                break;
        }

        // Build the timeline item
        return '<div class="tkt-hst-timeline-item d-flex gap-3 align-items-start">' +
            '<div class="d-flex flex-column align-items-center flex-shrink-0 align-self-stretch">' +
            '<div class="rounded-circle d-flex flex-column align-items-center justify-content-center text-center text-white flex-shrink-0 tkt-hst-badge" style="background-color: ' + badgeClass + ';">' + badgeText + '</div>' +
            '<div class="tkt-hst-connector-line"></div>' +
            '</div>' +
            '<div class="flex-fill mt-1 tkt-hst-card mb-1 ">' +
            '<div class="d-flex align-items-center gap-1 mb-1 meta-time">' +
            '<svg width="12" height="12" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M8.125 0C6.51803 0 4.94714 0.476523 3.611 1.36931C2.27485 2.2621 1.23344 3.53105 0.618482 5.0157C0.00352044 6.50035 -0.157382 8.13401 0.156123 9.71011C0.469628 11.2862 1.24346 12.7339 2.37976 13.8702C3.51606 15.0065 4.9638 15.7804 6.5399 16.0939C8.11599 16.4074 9.74966 16.2465 11.2343 15.6315C12.719 15.0166 13.9879 13.9752 14.8807 12.639C15.7735 11.3029 16.25 9.73197 16.25 8.125C16.2477 5.97081 15.391 3.90551 13.8677 2.38227C12.3445 0.85903 10.2792 0.00227486 8.125 0ZM8.125 15C6.76526 15 5.43605 14.5968 4.30546 13.8414C3.17487 13.0859 2.29368 12.0122 1.77333 10.7559C1.25298 9.49971 1.11683 8.11737 1.3821 6.78375C1.64738 5.45013 2.30216 4.22513 3.26364 3.26364C4.22513 2.30216 5.45014 1.64737 6.78376 1.3821C8.11738 1.11683 9.49971 1.25298 10.756 1.77333C12.0122 2.29368 13.0859 3.17487 13.8414 4.30545C14.5968 5.43604 15 6.76525 15 8.125C14.9979 9.94773 14.2729 11.6952 12.9841 12.9841C11.6952 14.2729 9.94773 14.9979 8.125 15ZM13.125 8.125C13.125 8.29076 13.0592 8.44973 12.9419 8.56694C12.8247 8.68415 12.6658 8.75 12.5 8.75H8.125C7.95924 8.75 7.80027 8.68415 7.68306 8.56694C7.56585 8.44973 7.5 8.29076 7.5 8.125V3.75C7.5 3.58424 7.56585 3.42527 7.68306 3.30806C7.80027 3.19085 7.95924 3.125 8.125 3.125C8.29076 3.125 8.44974 3.19085 8.56695 3.30806C8.68416 3.42527 8.75 3.58424 8.75 3.75V7.5H12.5C12.6658 7.5 12.8247 7.56585 12.9419 7.68306C13.0592 7.80027 13.125 7.95924 13.125 8.125Z" fill="#7F7F7F"/>' +
            '</svg>' +
            '<span class="b7-text" style="color:#7F7F7F">' + (value.updated_at_formatted || value.created_at_formatted || '') + '</span>' +
            '</div>' +
            '<div class="d-flex align-items-center gap-2 mb-1">' +
            '<div class="rounded-circle overflow-hidden flex-shrink-0 tkt-hst-avatar d-flex align-items-center justify-content-center">' +
            avatarHtml +
            '</div>' +
            '<span class="b6-text fw-bold">' + userName + '</span>' +
            '<span class="b6-text fw-bold">•</span>' +
            '<span class="b6-text fw-light" style="color:#7F7F7F">Changes done by ' + userName + '</span>' +
            '</div>' +
            '<div class="b6-text fw-normal">' + actionHtml + '</div>' +
            '</div>' +
            '</div>';
    };

    // Helper function to get time ago text for badge
    t.getTimeAgoText = function (dateTimeStr) {
        if (!dateTimeStr) return 'Just<br>now';

        var date = new Date(dateTimeStr);
        var now = new Date();
        var diffMs = now - date;
        var diffMins = Math.floor(diffMs / 60000);
        var diffHours = Math.floor(diffMins / 60);
        var diffDays = Math.floor(diffHours / 24);
        var diffWeeks = Math.floor(diffDays / 7);
        var diffMonths = Math.floor(diffDays / 30);
        var diffYears = Math.floor(diffDays / 365);

        if (diffMins < 1) return 'Just<br>now';
        if (diffMins < 60) return diffMins + ' min<br>ago';
        if (diffHours < 24) return diffHours + ' hr<br>ago';
        if (diffDays === 1) return '1 day<br>ago';
        if (diffDays < 7) return diffDays + ' days<br>ago';
        if (diffWeeks === 1) return '1 week<br>ago';
        if (diffWeeks < 4) return diffWeeks + ' weeks<br>ago';
        if (diffMonths === 1) return '1 month<br>ago';
        if (diffMonths < 12) return diffMonths + ' months<br>ago';
        if (diffYears === 1) return '1 year<br>ago';
        return diffYears + ' years<br>ago';
    };

    // Helper function to get avatar
    t.getAvatar = function (commenter, avatar) {
        // Check if it's System user
        if (commenter === "System" || commenter === "system") {
            return '<img class="w-100 h-100 object-fit-cover" src="' + t.config.url.base_url + '/assets/mati.png" alt="System">';
        }

        // For other users
        if (avatar && avatar !== '' && avatar !== null) {
            // If avatar exists in database
            return '<img class="w-100 h-100 object-fit-cover" src="' + t.config.url.base_url + '/storage/avatar/' + avatar + '" alt="' + commenter + '">';
        } else {
            // Otherwise show initials
            var name = commenter || 'User';
            var initials = name.split(' ').map(function (n) { return n[0]; }).join('').toUpperCase().substring(0, 2);
            return '<span class="tkt-hst-avatar-initials" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, #a78bfa, #7c3aed); color: white; font-size: 10px; font-weight: 600; text-transform: uppercase;">' + initials + '</span>';
        }
    };

    var select2Opts = { width: "100%" };
    if (el.status && el.status.length) {
        el.status.select2(select2Opts);
    }
    if (el.mdl.frmEl.trigger_status && el.mdl.frmEl.trigger_status.length) {
        el.mdl.frmEl.trigger_status.select2({
            dropdownParent: el.mdl.frmEl.trigger_status.parent(),
            width: "100%",
            placeholder: 'Select Status',
        });
    }

    // Company Select2
    if (el.mdl.frmEl.company && el.mdl.frmEl.company.length) {
        el.mdl.frmEl.company.select2($.extend({}, select2Opts, {
            dropdownParent: el.mdl.frmEl.company.parent(),
            ajax: {
                url: t.config.url.getCompanyByUserAccess,
                dataType: "json",
                data: function (p) {
                    return { search: p.term, page: p.page || 1 };
                },
                delay: 300,
                processResults: function (data) {
                    return {
                        results: data.results || [],
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                }
            },
            allowClear: true,
            placeholder: config.translations.company_placeholder
        })).on("change", function () {
            $('.department_id').empty().val("").trigger("change");
            $('.user_id').empty().val("").trigger("change");
            $('.status_id').empty().val("").trigger("change");
        });
    }

    // REPEATER INITIALIZATION
    $(function () {
        if (el.condition_repeater.length && $.fn.repeater) {
            el.condition_repeater.repeater({
                isFirstItemUndeletable: true,
                defaultValues: {
                    matchType: 'all'
                },

                show: function () {
                    var $item = $(this);
                    destroyAllSelect2($item);
                    $item.slideDown();
                    updateConditionDeleteButtons();
                    initConditionSelect2($item);
                },

                hide: function (deleteElement) {
                    var $item = $(this);
                    destroyAllSelect2($item);
                    $item.slideUp(function () {
                        deleteElement();
                        updateConditionDeleteButtons();
                    });
                },

                ready: function () {
                    updateConditionDeleteButtons();
                    el.condition_repeater.find('[data-repeater-item]').each(function () {
                        destroyAllSelect2($(this));
                        initConditionSelect2($(this));
                    });
                }
            });
        }

        if (el.action_repeater.length && $.fn.repeater) {
            // Event delegation for all action_id changes
            el.action_repeater.off('change.actionRepeater', '.action_id').on('change.actionRepeater', '.action_id', function () {
                var actionValue = $(this).val();
                var repeaterRow = $(this).closest('[data-repeater-item]');

                // Hide all action fields first
                repeaterRow.find('.actions').each(function () {
                    var $this = $(this);
                    $this.removeClass('select2-hidden-accessible');
                    $this.removeAttr('data-select2-id');
                    $this.removeAttr('tabindex');
                    $this.addClass('d-none');
                    $this.hide();
                    $this.find('input, select, textarea').prop('disabled', true);
                    $this.find('.select2-container').remove();
                });

                // Show the relevant action fields
                switch (actionValue) {
                    case '2': // Mail
                        var $mailDiv = repeaterRow.find('.mail, .mail_content');
                        $mailDiv.removeClass('d-none');
                        $mailDiv.removeClass('select2-hidden-accessible');
                        $mailDiv.removeAttr('data-select2-id');
                        $mailDiv.removeAttr('tabindex');
                        $mailDiv.show();
                        $mailDiv.find('input, select, textarea').prop('disabled', false);

                        var $mailUser = repeaterRow.find('.mail .user_id');
                        if ($mailUser.length) {
                            if ($mailUser.hasClass('select2-hidden-accessible')) {
                                $mailUser.select2('destroy');
                            }
                            $mailUser.parent().find('.select2-container').remove();
                            $mailUser.removeClass('select2-hidden-accessible');
                            $mailUser.removeAttr('data-select2-id');
                            $mailUser.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                ajax: {
                                    url: t.config.url.getUserByQuery,
                                    dataType: "json",
                                    transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                                    delay: 300,
                                    data: function (params) {
                                        return {
                                            search: params.term,
                                            page: params.page || 1,
                                            company_id: el.mdl.frmEl.company.val()
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.results || data.items || data.data || [],
                                            pagination: {
                                                more: data.pagination ? data.pagination.more : false
                                            }
                                        };
                                    }
                                }
                            });
                        }
                        break;

                    case '3': // Note
                        var $noteDiv = repeaterRow.find('.note');
                        $noteDiv.removeClass('d-none');
                        $noteDiv.removeClass('select2-hidden-accessible');
                        $noteDiv.removeAttr('data-select2-id');
                        $noteDiv.removeAttr('tabindex');
                        $noteDiv.show();
                        $noteDiv.find('textarea').prop('disabled', false);
                        break;

                    case '4': // Status
                        var $statusDiv = repeaterRow.find('.status');
                        var $statusSelect = $statusDiv.find('.status_id');

                        $statusDiv.removeClass('d-none');
                        $statusDiv.removeClass('select2-hidden-accessible');
                        $statusDiv.removeAttr('data-select2-id');
                        $statusDiv.removeAttr('tabindex');
                        $statusDiv.css('display', '');
                        $statusDiv.show();

                        $statusSelect.prop('disabled', false);

                        if ($statusSelect.hasClass('select2-hidden-accessible')) {
                            $statusSelect.select2('destroy');
                        }
                        $statusDiv.find('.select2-container').remove();
                        $statusDiv.find('.select2-container--default').remove();
                        $statusSelect.removeClass('select2-hidden-accessible');
                        $statusSelect.removeClass('select2-container');
                        $statusSelect.removeAttr('data-select2-id');
                        $statusSelect.removeAttr('aria-hidden');
                        $statusSelect.removeAttr('tabindex');
                        var currentStatusValue = $statusSelect.val();

                        $statusSelect.select2({
                            width: '100%',
                            dropdownParent: repeaterRow.closest('.modal-content'),
                            placeholder: 'Search and select status',
                            allowClear: true,
                            ajax: {
                                url: t.config.url.getStatusByAjax,
                                dataType: "json",
                                transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                                data: function (params) {
                                    return {
                                        search: params.term || '',
                                        page: params.page || 1,
                                        company_id: el.mdl.frmEl.company.val(),
                                        context:'ticket-trigger-status'
                                    };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.results || [],
                                        pagination: {
                                            more: data.pagination ? data.pagination.more : false
                                        }
                                    };
                                },
                            },
                            templateResult: function(data) {
                                if (data.loading) {
                                    return data.text;
                                }
                                return $('<span>').text(data.text);
                            },
                            templateSelection: function(data) {
                                return data.text || data.id;
                            }
                        });
                        if (currentStatusValue) {
                            setTimeout(function() {
                                $statusSelect.val(currentStatusValue).trigger('change');
                            }, 200);
                        }
                        setTimeout(function () {
                            $statusDiv.removeClass('select2-hidden-accessible');
                            $statusDiv.css('display', 'block');
                            var $select2Container = $statusDiv.find('.select2-container');
                            if ($select2Container.length) {
                                $select2Container.show();
                                $select2Container.css('display', 'block');
                            }
                            $statusDiv.show();
                            $statusSelect.trigger('change');
                        }, 150);
                        break;

                    case '5': // Assign To
                        var $assignDiv = repeaterRow.find('.assign_to');
                        $assignDiv.removeClass('d-none');
                        $assignDiv.removeClass('select2-hidden-accessible');
                        $assignDiv.removeAttr('data-select2-id');
                        $assignDiv.removeAttr('tabindex');
                        $assignDiv.show();
                        $assignDiv.find('.user_id').prop('disabled', false);

                        var $userSelect = repeaterRow.find('.assign_to .user_id');
                        if ($userSelect.length) {
                            if ($userSelect.hasClass('select2-hidden-accessible')) {
                                $userSelect.select2('destroy');
                            }
                            $userSelect.parent().find('.select2-container').remove();
                            $userSelect.removeClass('select2-hidden-accessible');
                            $userSelect.removeAttr('data-select2-id');
                            $userSelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                placeholder: "Select User",
                                ajax: {
                                    url: t.config.url.getUserByQuery,
                                    dataType: "json",
                                    transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                                    delay: 300,
                                    data: function (params) {
                                        return {
                                            search: params.term,
                                            page: params.page || 1,
                                            company_id: el.mdl.frmEl.company.val(),
                                            context:'ticket-trigger-assignee'//for fetchibng only technicians
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.results || data.items || data.data || [],
                                            pagination: {
                                                more: data.pagination ? data.pagination.more : false
                                            }
                                        };
                                    }
                                }
                            });
                        }
                        break;

                    case '9': // Department & Category
                        var $deptDiv = repeaterRow.find('.department');
                        $deptDiv.removeClass('d-none');
                        $deptDiv.removeClass('select2-hidden-accessible');
                        $deptDiv.removeAttr('data-select2-id');
                        $deptDiv.removeAttr('tabindex');
                        $deptDiv.show();
                        $deptDiv.find('.department_id').prop('disabled', false);

                        $deptDiv.find('.department_id').addClass('prob_cat');
                        var $deptSelect = repeaterRow.find('.department .department_id');
                        if ($deptSelect.length) {
                            if ($deptSelect.hasClass('select2-hidden-accessible')) {
                                $deptSelect.select2('destroy');
                            }
                            $deptSelect.parent().find('.select2-container').remove();
                            $deptSelect.removeClass('select2-hidden-accessible');
                            $deptSelect.removeAttr('data-select2-id');
                            $deptSelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                ajax: {
                                    url: t.config.url.getDepartmentsWithCompanyByQuery,
                                    dataType: "json",
                                    transport: companyBasedAjaxTransport(el.mdl.frmEl.company),
                                    delay: 300,
                                    data: function (params) {
                                        return {
                                            search: params.term,
                                            page: params.page || 1,
                                            company_id: el.mdl.frmEl.company.val()
                                        };
                                    },
                                    processResults: function (data) {
                                        return {
                                            results: data.results || data.items || data.data || [],
                                            pagination: {
                                                more: data.pagination ? data.pagination.more : false
                                            }
                                        };
                                    }
                                }
                            });

                            $deptSelect.unbind('change', function (e) {
                                window.refillProblemCategory.call(this, e);
                            });
                        }

                        var $catFields = repeaterRow.find('.category_fields');
                        $catFields.removeClass('d-none');
                        $catFields.removeClass('select2-hidden-accessible');
                        $catFields.removeAttr('data-select2-id');
                        $catFields.removeAttr('tabindex');
                        $catFields.show();
                        $catFields.find('select').prop('disabled', false);

                        var $catSelect = repeaterRow.find('.category_fields .problem_category_id');
                        if ($catSelect.length) {
                            if ($catSelect.hasClass('select2-hidden-accessible')) {
                                $catSelect.select2('destroy');
                            }
                            $catSelect.parent().find('.select2-container').remove();
                            $catSelect.removeClass('select2-hidden-accessible');
                            $catSelect.removeAttr('data-select2-id');
                            $catSelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                placeholder: 'Select Category',
                                allowClear: true
                            });

                            $catSelect.off('change').on('change', function (e) {
                                window.refillSubCategory.call(this, e);
                            });
                        }

                        var $subCatSelect = repeaterRow.find('.category_fields .sub_category_id');
                        if ($subCatSelect.length) {
                            if ($subCatSelect.hasClass('select2-hidden-accessible')) {
                                $subCatSelect.select2('destroy');
                            }
                            $subCatSelect.parent().find('.select2-container').remove();
                            $subCatSelect.removeClass('select2-hidden-accessible');
                            $subCatSelect.removeAttr('data-select2-id');
                            $subCatSelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                placeholder: 'Select Sub Category',
                                allowClear: true
                            });
                        }

                        var $subCatDiv = repeaterRow.find('.prob_sub_cat');
                        if ($subCatDiv.find('option').length > 1) {
                            $subCatDiv.removeClass('d-none');
                            $subCatDiv.removeClass('select2-hidden-accessible');
                            $subCatDiv.removeAttr('data-select2-id');
                            $subCatDiv.removeAttr('tabindex');
                            $subCatDiv.show();
                        }

                        setTimeout(function () {
                            $deptDiv.removeClass('select2-hidden-accessible');
                            $deptDiv.css('display', 'block');
                            $catFields.removeClass('select2-hidden-accessible');
                            $catFields.css('display', 'block');
                        }, 150);
                        break;

                    case '10': // API Definition
                        var $apiDiv = repeaterRow.find('.apidefination');
                        $apiDiv.removeClass('d-none');
                        $apiDiv.removeClass('select2-hidden-accessible');
                        $apiDiv.removeAttr('data-select2-id');
                        $apiDiv.removeAttr('tabindex');
                        $apiDiv.show();
                        $apiDiv.find('.apidefination_id').prop('disabled', false);

                        var $apiSelect = repeaterRow.find('.apidefination .apidefination_id');
                        if ($apiSelect.length) {
                            if ($apiSelect.hasClass('select2-hidden-accessible')) {
                                $apiSelect.select2('destroy');
                            }
                            $apiSelect.parent().find('.select2-container').remove();
                            $apiSelect.removeClass('select2-hidden-accessible');
                            $apiSelect.removeAttr('data-select2-id');
                            $apiSelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                placeholder: 'Select API Definition',
                                allowClear: true
                            });
                        }
                        break;

                    case '11': // Priority
                        var $priorityDiv = repeaterRow.find('.priority');
                        $priorityDiv.removeClass('d-none');
                        $priorityDiv.removeClass('select2-hidden-accessible');
                        $priorityDiv.removeAttr('data-select2-id');
                        $priorityDiv.removeAttr('tabindex');
                        $priorityDiv.show();
                        $priorityDiv.find('.priority_id').prop('disabled', false);

                        var $prioritySelect = repeaterRow.find('.priority .priority_id');
                        if ($prioritySelect.length) {
                            if ($prioritySelect.hasClass('select2-hidden-accessible')) {
                                $prioritySelect.select2('destroy');
                            }
                            $prioritySelect.parent().find('.select2-container').remove();
                            $prioritySelect.removeClass('select2-hidden-accessible');
                            $prioritySelect.removeAttr('data-select2-id');
                            $prioritySelect.select2({
                                width: '100%',
                                dropdownParent: repeaterRow.closest('.modal-content'),
                                placeholder: 'Select Priority',
                                allowClear: true
                            });
                        }
                        break;

                    default:
                        break;
                }
            });

            el.action_repeater.repeater({
                isFirstItemUndeletable: false,
                show: function () {
                    var $item = $(this);
                    destroyAllSelect2($item);
                    $item.slideDown();
                    updateActionDeleteButtons();
                    initActionSelect2($item);

                    $item.find('.prob_cat').off('change').on('change', function (e) {
                        window.refillProblemCategory.call(this, e);
                    });

                    $item.find('.problem_category_id').off('change').on('change', function (e) {
                        window.refillSubCategory.call(this, e);
                    });

                    setTimeout(function () {
                        $item.find('.action_id').trigger('change');
                    }, 200);
                },
                hide: function (deleteElement) {
                    var $item = $(this);
                    destroyAllSelect2($item);
                    $item.slideUp(function () {
                        deleteElement();
                        updateActionDeleteButtons();
                    });
                },
                ready: function () {
                    updateActionDeleteButtons();

                    el.action_repeater.find('[data-repeater-item]').each(function () {
                        var $item = $(this);
                        destroyAllSelect2($item);
                        initActionSelect2($item);

                        $item.find('.prob_cat').off('change').on('change', function (e) {
                            window.refillProblemCategory.call(this, e);
                        });

                        $item.find('.problem_category_id').off('change').on('change', function (e) {
                            window.refillSubCategory.call(this, e);
                        });
                    });

                    setTimeout(function () {
                        el.action_repeater.find('[data-repeater-item] .action_id').each(function () {
                            var $this = $(this);
                            if ($this.val()) {
                                $this.trigger('change');
                            }
                        });
                    }, 300);
                }
            });
        }
    });

    // Problem category & subcategory helper functions
    window.refillProblemCategory = function (e) {
        if (typeof e !== "undefined") e.preventDefault();

        var $art = $(this).closest('[data-repeater-item]');
        if (!$art.length) {
            console.log('No repeater item found');
            return;
        }

        t.problem_categories = [];
        var $catField = $art.find('.category_fields .problem_category_id');
        $catField.empty().append(new Option("Select Category", ""));

        var type_val = parseInt($.trim($(this).val()));

        if (type_val > 0 && !isNaN(type_val)) {
            $.get(t.config.url.problem_categories_by_company + "/" + type_val)
                .done(function (data) {
                    if (typeof data == "object" && data.data && data.data.length) {
                        t.problem_categories = data.data;
                        $.each(data.data, function (i, v) {
                            $catField.append(new Option(v.name, v.id));
                        });

                        var $subCatDiv = $art.find('.prob_sub_cat');
                        $subCatDiv.removeClass('d-none');
                        $subCatDiv.show();

                        if ($catField.hasClass('select2-hidden-accessible')) {
                            $catField.select2('destroy');
                        }
                        $catField.parent().find('.select2-container').remove();
                        $catField.removeClass('select2-hidden-accessible');
                        $catField.removeAttr('data-select2-id');
                        $catField.select2({
                            width: '100%',
                            dropdownParent: $art.closest('.modal-content'),
                            placeholder: 'Select Category',
                            allowClear: true
                        });

                        $catField.trigger('change');
                    } else {
                        $art.find('.prob_sub_cat').addClass('d-none');
                        $art.find('.prob_sub_cat').hide();
                    }
                })
                .fail(function () {
                    $art.find('.prob_sub_cat').addClass('d-none');
                    $art.find('.prob_sub_cat').hide();
                });
        } else {
            $art.find('.prob_sub_cat').addClass('d-none');
            $art.find('.prob_sub_cat').hide();
        }
    };

    window.refillSubCategory = function (e, val, el, scopeEl) {
        if (typeof e !== "undefined") e.preventDefault();

        var scope = scopeEl || $(this).closest('[data-repeater-item]');
        if (!scope.length) {
            console.log('No scope found for subcategory');
            return;
        }

        t.sub_categories = [];
        var $subCatField = scope.find('.sub_category_id');
        $subCatField.empty().append(new Option('Select SubCategory', ""));

        if (typeof val != "undefined" && typeof el != "undefined") {
            var type_val = parseInt($.trim(el));
        } else {
            var type_val = parseInt($.trim(scope.find('.problem_category_id').val()));
        }

        if (type_val > 0 && !isNaN(type_val)) {
            try {
                $.each(t.problem_categories, function (i, v) {
                    if (v.id == type_val) {
                        if (Array.isArray(v.sub) && v.sub.length > 0) {
                            t.sub_categories = v.sub;
                            $.each(v.sub, function (j, k) {
                                $subCatField.append(new Option(k.name, k.id));
                            });
                            return false;
                        }
                    }
                });
            } catch (e) { console.log(e); }
        }

        if (typeof val != "undefined") {
            $subCatField.val(val);
        }

        if ($subCatField.hasClass('select2-hidden-accessible')) {
            $subCatField.select2('destroy');
        }
        $subCatField.parent().find('.select2-container').remove();
        $subCatField.removeClass('select2-hidden-accessible');
        $subCatField.removeAttr('data-select2-id');
        $subCatField.select2({
            width: '100%',
            dropdownParent: scope.closest('.modal-content'),
            placeholder: 'Select Sub Category',
            allowClear: true
        });

        if (t.sub_categories.length > 0) {
            $subCatField.rules("add", { required: true, str_name: true });
            scope.find('.prob_sub_cat').removeClass('d-none');
            scope.find('.prob_sub_cat').show();
        } else {
            $subCatField.rules("remove");
            scope.find('.prob_sub_cat').addClass('d-none');
            scope.find('.prob_sub_cat').hide();
        }

        $subCatField.trigger('change');
    };

    if (el.mdl.btnSubmit && el.mdl.btnSubmit.length) {
        el.mdl.btnSubmit.on("click", $.proxy(t.handleSubmit));
    }
    if (el.table && el.table.length) {
        el.table.on("click", ".dtActEdit", $.proxy(t.editTrigger));
        el.table.on("click", ".dtActDel", $.proxy(t.removeTrigger));
        el.table.on('click', '.dtActHistory', $.proxy(t.triggerHistory));
    }
};