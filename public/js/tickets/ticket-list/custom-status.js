var CustomStatus = function (t, select2Opts) {

    t.customStatusModal = t.content.find('#customStatusModal');
    t.customStatusModal.frm = t.customStatusModal.find('#custom-status-mdl-frm');
    t.customStatusModal.title = t.customStatusModal.find('#customStatusModalTitle');
    t.editid = t.customStatusModal.find('#editid');
    t.tabName = t.customStatusModal.find('#tab_name');
    t.statusCustom = t.customStatusModal.find('#tab_status');
    t.customStatusBtnSubmit = t.customStatusModal.find('#btnSubmit');
    t.btnClear = t.customStatusModal.find('#btnClear');
    t.customStatusTable = t.customStatusModal.find('#custom-status');

    t.statusCustom.select2(
        $.extend({}, select2Opts, {
            placeholder: "Select Status",
            allowClear: true,
            dropdownParent: t.customStatusModal,
            ajax: {
                url: t.config.url.getStatusByAjax,
                dataType: "json",
                data: function (p) {
                    return {
                        search: p.term,
                        page: p.page || 1
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        })
    );

    // dynamic load the custome statuses here and did page filter
    t.tktPagecustomStatus.select2({
        theme: 'custom',
        width: 'auto',
        placeholder: 'select custome status',
        ajax: {
            url: t.config.url.getStatusTabs,
            type: 'GET',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: $.map(response.data, function (item) {
                        return {
                            id: item.tab_status,
                            text: item.tab_name
                        };
                    })
                };
            },
        }
    });

    t.resetFrm = function () {
        t.frmValidator.resetForm();
        t.tabName.empty();
        t.editid.empty();
        t.statusCustom.empty().trigger("change");
    };

    t.addStatus = function (e) {
        e.preventDefault();
        t.frmValidator.resetForm();
        t.tabName.empty();
        t.httpPostPath = t.config.url.tabs;
        t.statusCustom.empty().trigger("change");
        t.customStatusBtnSubmit.text('Create');
        // t.statusCustom.prop("disabled", false);
        t.customStatusModal.title.html(t.config.translations.add_view_tab);
        t.customStatusModal.modal("show");
        t.load();
    }

    t.frmValidator = t.customStatusModal.frm.validate({
        onsubmit: false,
        rules: {
            tab_status: {
                required: true,
            },
            tab_name: {
                required: true,
                maxlength: 255,
                clean_text_only: true,
            },
        },
        errorPlacement: function (error, element) {
            let group = element.closest(".input-group");
            if (group.length) {
                error.insertAfter(group);
            } else {
                error.insertAfter(element);
            }
        },
    });

    t.renderList = function (i, d) {
        var taction = "";
        switch (parseInt(d.tab_action)) {
            case 1:
                taction = "All";
                break;
            case 2:
                taction = "Status";
                break;
            default:
                taction = "-";
                break;
        }

        var st = '';
        if (d.tab_status == null || d.tab_status == '') {
            st = '-';
        } else {
            st = d.status_name || d.tab_status || '-';
        }

        var html = `
        <tr>
            <td>
                <button class="btn edit-ctab"  data-bs-toggle="tooltip" title="${t.config.translations.edit}" data-id="${d.id}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path>
                    </svg>
                </button>
                <button class="btn delete-ctab" data-bs-toggle="tooltip" title="${t.config.translations.delete}" data-id="${d.id}">
                    <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor">
                         <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/>
                    </svg>
                </button>
            </td>
            <td>${d.tab_name || '-'}</td>
            <td>${taction}</td>
            <td>${st}</td>
        </tr>
    `;
        $('#customStatusList').append(html);
       document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    t.load = function () {
        t.frmValidator.resetForm();
        t.resetFrm();
        // t.statusCustom.prop("disabled", false);
        $('#customStatusList').html('')
        t.customStatusModal.frm[0].reset();
        t.customStatusModal.modal("show");
        var http = $.ajax({
            url: t.config.url.tabList,
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': t.config.csrf
            },
            processData: false,
            contentType: false,
        });
        http.done(function (data) {
            $('#tab_tbody').html('');
            if (data.data) {
                $.each(data.data, t.renderList);
            }
        });
    }

    t.deleteTab = function (e) {
        var id = $(this).data("id");
        sweetAlertConfirm({
            message: t.config.translations.deleteListItem,
            url: t.config.url.tabDelete,
            data: {
                "id": id,
                _token: t.config.csrf
            },
            onSuccess: function (data) {
                t.load();
            },
            errorMsg: t.config.translations.somethingWentWrong,
            beforeSend: function () {
                t.httpCall = false;
            },
            complete: function () {
                t.httpCall = true;
            }
        });
    }

    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        t.customStatusBtnSubmit.prop("disabled", true);
        var formData = new FormData(t.customStatusModal.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.customStatusBtnSubmit.prop("disabled", false);
                    sweetAlert('center', 'success', data);
                    setTimeout(function () {
                        t.load();
                    }, 800);
                } else {
                    t.customStatusBtnSubmit.prop("disabled", false);
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            t.customStatusBtnSubmit.prop("disabled", false);
            var data = {
                'msg': t.config.translations.somethingWentWrong,
            };
            sweetAlert('center', 'error', data);
        });
        t.frmValidator.resetForm();
        t.customStatusModal.frm[0].reset();
        t.openForm();
        t.httpPostPath = t.config.url.tabs;
    };

    t.openForm = function () {
        t.resetFrm();
        t.httpPostPath = t.config.url.tabs;
        t.customStatusModal.title.html('Custom Status Tab');
        t.customStatusBtnSubmit.text("Add Status");
        // t.statusCustom.prop("disabled", false);
    }

    t.loadFormCustomTab = function (data, forAction) {
        t.frmValidator.resetForm();
        t.resetFrm();
        t.editid.val(data.id);
        t.tabName.empty();
        t.tabName.val(data.tab_name);
        t.statusCustom.empty();
        var newOption = new Option(data.status.name, data.tab_status, true, true);
        t.statusCustom.append(newOption).trigger('change');
        // t.statusCustom.prop("disabled", true);
    }

    t.editTab = function (e) {
        e.preventDefault();
        var tabId = $(this).data('id');
        t.httpPostPath = t.config.url.updateTab;
        var http = $.get(t.config.url.editTab + "/" + tabId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.customStatusModal.title.html(t.config.translations.edit_tab_name);
                    t.customStatusBtnSubmit.text("Update");
                    t.customStatusModal.modal("show");
                    t.loadFormCustomTab(data.data);
                } else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': t.config.translations.somethingWentWrong
            };
            sweetAlert('center', 'error', data);
        });
    }

    t.content.on("click", ".edit-ctab", $.proxy(t.editTab));
    t.addCustomStatusBtn.on('click', (e) => t.addStatus(e));
    t.customStatusBtnSubmit.on('click', (e) => t.handlesubmit(e));
    t.content.on("click", ".delete-ctab", $.proxy(t.deleteTab));
};