var GroupsProblem = function (config) {
        var t = this;
        t.config = config;
        t.content = $('section.content');
        t.ticketTableElem = t.content.find('#impactTicketTable');
        t.deviceTableElem = t.content.find('#impactDevices');
        t.container = t.content;        
        t.httpCall = true;      
        t.historyModal = t.content.find('#mdl-pm-history'); 
        t.historyResult = t.content.find('#historyResult');
        
        function syncSelectAll(tableId, selectAllCheckbox) {
            var totalRows = $(tableId + ' tbody tr').length;
            var checkedRows = $(tableId + ' tbody input.row_selector:checked').length;
            if (totalRows === 0) {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            } else if (checkedRows === 0) {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            } else if (checkedRows === totalRows) {
                selectAllCheckbox.prop('checked', true).prop('indeterminate', false);
            } else {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', true);
            }
        }
        
        t.ticketTable = t.ticketTableElem.DataTable({
            autoWidth: false,
            dom: 'drtip',           
            aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [0]
            }, {
                targets: 0,
                render: function (d) {
                    return '<div class="checkSelectTicket"><input type="checkbox" class="row_selector form-check-input" data-id="' + d + '" /></div>';
                }
            }],
            order: [[3, 'asc']],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.getProlemManageTicket,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                    d.ticketId = t.config.groupTicketId;
                }
            },
            columns: [
                { data: 'ticket_id' },     
                { data: 'ticket_id' },      
                { data: 'ticket_problem_status' }, 
                { data: 'created_at' }      
            ],
            initComplete: function () {
                var lengthSelect = t.content.find('#impactTicket-page-length');
                if (lengthSelect.length) {
                    lengthSelect.off('change').on('change', function () {
                        var newLength = parseInt($(this).val(), 10);
                        t.ticketTable.page.len(newLength).draw();
                    });
                }
                var searchInput = t.content.find('#impactTicket .holiday-list-search');
                if (searchInput.length) {
                    searchInput.off('keyup').on('keyup', function (e) {
                        if (e.keyCode === 13) {
                            t.ticketTable.search($(this).val()).draw();
                        }
                    });
                }
                var selectAllTicket = t.content.find('.select-all-ticket');
                if (selectAllTicket.length) {
                    selectAllTicket.off('click').on('click', function () {
                        var isChecked = $(this).prop('checked');
                        $('#impactTicketTable tbody .row_selector').prop('checked', isChecked);
                    });
                    t.ticketTableElem.off('change', '.row_selector').on('change', '.row_selector', function () {
                        syncSelectAll('#impactTicketTable', selectAllTicket);
                    });
                    t.ticketTable.on('draw', function () {
                        syncSelectAll('#impactTicketTable', selectAllTicket);
                    });
                }
            }
        });
        
        
        t.deviceTable = t.deviceTableElem.DataTable({
            autoWidth: false,
            dom: 'drtip',
            aoColumnDefs: [{
                'bSortable': false,
                'aTargets': [0]
            }, {
                targets: 0,
                render: function (d) {
                    return '<div class="checkSelectDevice"><input type="checkbox" class="row_selector form-check-input" data-id="' + d + '" /></div>';
                }
            }],
            order: [[3, 'asc']],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.getProlemManage,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                    d.deviceId = t.config.groupDeviceId;
                }
            },
            columns: [
                { data: 'device_id' },     
                { data: 'asset_tag' },      
                { data: 'device_problem_status' }, 
                { data: 'created_at' },      
                { data: 'handler_name' }     
            ],
            initComplete: function () {
                var lengthSelect = t.content.find('#impactDevice-page-length');
                if (lengthSelect.length) {
                    lengthSelect.off('change').on('change', function () {
                        var newLength = parseInt($(this).val(), 10);
                        t.deviceTable.page.len(newLength).draw();
                    });
                }
                var searchInput = t.content.find('#impactDevice .holiday-list-search');
                if (searchInput.length) {
                    searchInput.off('keyup').on('keyup', function (e) {
                        if (e.keyCode === 13) {
                            t.deviceTable.search($(this).val()).draw();
                        }
                    });
                }
                var selectAllDevice = t.content.find('.select-all-device');
                if (selectAllDevice.length) {
                    selectAllDevice.off('click').on('click', function () {
                        var isChecked = $(this).prop('checked');
                        $('#impactDevices tbody .row_selector').prop('checked', isChecked);
                    });
                    t.deviceTableElem.off('change', '.row_selector').on('change', '.row_selector', function () {
                        syncSelectAll('#impactDevices', selectAllDevice);
                    });
                    t.deviceTable.on('draw', function () {
                        syncSelectAll('#impactDevices', selectAllDevice);
                    });
                }
            }
        });
        
        t.completeMultipleTickets = function (e) {
            e.preventDefault();
            var selected = [];
            $(".checkSelectTicket").find(".row_selector").each(function () {
                if ($(this).is(":checked") && !$(this).prop('disabled')) {
                    selected.push($(this).attr("data-id"));
                }
            });
            if (selected.length === 0) {
                sweetAlert('center', 'warning', { msg: "Please select at least one Impacted Ticket." });
                return false;
            }
            if (t.httpCall) {
                t.httpCall = false;
                sweetAlertConfirmation({
                    message: config.translations.are_you_update,
                    onConfirm: function () {
                        $.ajax({
                            url: t.config.url.solveMultipleProblem,
                            type: "POST",
                            data: {
                                _token: t.config.token,
                                ticket_Id: selected,
                                problem_Id: t.config.groupTicketId,
                            },
                            success: function (data) {
                                if (data.status === "success") {
                                    sweetAlert('center', 'success', data);
                                    setTimeout(function () { t.ticketTable.ajax.reload(); }, 800);
                                } else {
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function () {
                                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
                            },
                            complete: function () { t.httpCall = true; }
                        });
                    }
                });
            }
        };
        
        t.incompleteMultipleTickets = function (e) {
            e.preventDefault();
            var selected = [];
            $(".checkSelectTicket").find(".row_selector").each(function () {
                if ($(this).is(":checked") && !$(this).prop('disabled')) {
                    selected.push($(this).attr("data-id"));
                }
            });
            if (selected.length === 0) {
                sweetAlert('center', 'warning', { msg: "Please select at least one Impacted Ticket." });
                return false;
            }
            if (t.httpCall) {
                t.httpCall = false;
                sweetAlertConfirmation({
                    message: config.translations.are_you_update,
                    onConfirm: function () {
                        $.ajax({
                            url: t.config.url.unsolveMultipleProblem,
                            type: "POST",
                            data: {
                                _token: t.config.token,
                                ticket_Id: selected,
                                problem_Id: t.config.groupTicketId,
                            },
                            success: function (data) {
                                if (data.status === "success") {
                                    sweetAlert('center', 'success', data);
                                    setTimeout(function () { t.ticketTable.ajax.reload(); }, 800);
                                } else {
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function () {
                                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
                            },
                            complete: function () { t.httpCall = true; }
                        });
                    }
                });
            }
        };

        t.completeMultipleDevices = function (e) {
            e.preventDefault();
            var selected = [];
            $(".checkSelectDevice").find(".row_selector").each(function () {
                if ($(this).is(":checked") && !$(this).prop('disabled')) {
                    selected.push($(this).attr("data-id"));
                }
            });
            if (selected.length === 0) {
                sweetAlert('center', 'warning', { msg: "Please select at least one Impacted Device." });
                return false;
            }
            if (t.httpCall) {
                t.httpCall = false;
                sweetAlertConfirmation({
                    message: config.translations.are_you_update,
                    onConfirm: function () {
                        $.ajax({
                            url: t.config.url.solveDeviceMultipleProblem,
                            type: "POST",
                            data: {
                                _token: t.config.token,
                                device_id: selected,
                                problemId: t.config.groupDeviceId,
                            },
                            success: function (data) {
                                if (data.status === "success") {
                                    sweetAlert('center', 'success', data);
                                    setTimeout(function () { t.deviceTable.ajax.reload(); }, 800);
                                } else {
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function () {
                                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
                            },
                            complete: function () { t.httpCall = true; }
                        });
                    }
                });
            }
        };
        
        t.incompleteMultipleDevices = function (e) {
            e.preventDefault();
            var selected = [];
            $(".checkSelectDevice").find(".row_selector").each(function () {
                if ($(this).is(":checked") && !$(this).prop('disabled')) {
                    selected.push($(this).attr("data-id"));
                }
            });
            if (selected.length === 0) {
                sweetAlert('center', 'warning', { msg: "Please select at least one Impacted Device." });
                return false;
            }
            if (t.httpCall) {
                t.httpCall = false;
                sweetAlertConfirmation({
                    message: config.translations.are_you_update,
                    onConfirm: function () {
                        $.ajax({
                            url: t.config.url.unsolveDeviceMultipleProblem,
                            type: "POST",
                            data: {
                                _token: t.config.token,
                                device_id: selected,
                                problemId: t.config.groupDeviceId,
                            },
                            success: function (data) {
                                if (data.status === "success") {
                                    sweetAlert('center', 'success', data);
                                    setTimeout(function () { t.deviceTable.ajax.reload(); }, 800);
                                } else {
                                    sweetAlert('center', 'error', data);
                                }
                            },
                            error: function () {
                                sweetAlert('center', 'error', { msg: t.config.translations.something_went_wrong });
                            },
                            complete: function () { t.httpCall = true; }
                        });
                    }
                });
            }
        };

       
        t.openMdlPmHistory = function (e) {
            e.preventDefault();
            if (!t.httpCall) return false;
            t.httpCall = false;
            var pm_id = $(this).data('id');
            $.ajax({
                url: t.config.url.pm_history,
                type: "POST",
                data: { _token: t.config.token, pm_id: pm_id },
                success: function (data) {
                    if (data.status === "success") {
                        t.historyResult.html('');
                        if (data.data.length) {
                            let html = '<div class="history-timeline">';
                            $.each(data.data, function (idx, val) {
                                let avatarHtml = '';
                                if (val.avatar_url) {
                                    avatarHtml = `<img src="${val.avatar_url}" class="history-avatar" onerror="this.style.display='none'">                                    `;
                                } else {
                                    let name = val.updater_name || '';
                                    let initials = name.split(' ').filter(n => n.length > 0).map(n => n.charAt(0).toUpperCase()).slice(0, 2).join('');
                                    avatarHtml = `<div class="history-avatar-initials">${initials}</div>`;
                                }
                                html += `
                                <div class="history-timeline-item">
                                    <div class="history-icon-container">
                                        <div class="history-timeline-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div class="history-timeline-line"></div>
                                    </div>
                                    <div class="history-detail-info card-style">
                                        <div class="history-detail-info-time-row">
                                            <span>
                                                <i class="bi bi-clock"></i>
                                                ${val.updated_at_format}
                                            </span>
                                            <span class="accordion-toggle">
                                                <i class="bi bi-chevron-down"></i>
                                            </span>
                                        </div>
                                        <div class="history-detail-info-modified-status">
                                            <div class="d-flex align-items-center gap-2">
                                                ${avatarHtml}
                                                <span class="history-user-name">
                                                    ${val.updater_name}
                                                </span>
                                            </div>
                                            <span class="history-action">
                                                • Updated
                                            </span>
                                        </div>
                                        <div class="history-detail-info-changed">
                                            <div>${val.comments}</div>
                                        </div>
                                        <div class="accordion-content">
                                            <div class="history-notes">
                                                ${val.comments}
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                            });
                            html += '</div>';
                            t.historyResult.append(html);
                        } else {
                            t.historyResult.append(
                                '<div class="text-center py-3">History Not Available</div>'
                            );
                        }
                        t.historyModal.modal("show");
                    }
                },
                error: function () {
                    alert("Something went wrong.");
                },
                complete: function () {
                    t.httpCall = true;
                }
            });
        };
        t.content.find('.pmHistory').off('click').on('click', t.openMdlPmHistory);
        
        t.container.on('click', '.btnt-complete', $.proxy(t.completeMultipleTickets, t));
        t.container.on('click', '.btnt-incomplete', $.proxy(t.incompleteMultipleTickets, t));
        t.container.on('click', '.btnd-complete', $.proxy(t.completeMultipleDevices, t));
        t.container.on('click', '.btnd-incomplete', $.proxy(t.incompleteMultipleDevices, t));
        
        
    };
