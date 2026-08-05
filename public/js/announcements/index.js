var Announcement = function (config) {
    let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;
    var t = this;
    t.isEditing = false;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.calendar = t.content.find('#dd');
    t.mdl = t.content.find('#announcementMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#announcementForm');

    t.frmEl = {};
    t.frmEl.title = t.frm.find('#title');
    t.frmEl.announcement = t.frm.find('#content');
    t.frmEl.announcement.summernote({
        height: 200,
        placeholder: "Enter announcement...",
        toolbar: [
            ['style', ['bold', 'italic', 'underline']],
            ['color', ['color']],
            ['para', ['ul', 'ol']]
        ],
        dropdownParent: t.mdl
    });
    t.frmEl.start_date = t.frm.find('#start_date');
    t.frmEl.end_date = t.frm.find('#end_date');
    t.frmEl.department = t.frm.find('#department');
    t.frmEl.location = t.frm.find('#location');
    t.frmEl.users = t.frm.find('#users');
    t.frmEl.status = t.frm.find('#status_id');
    t.frmEl.userSection = t.frm.find('.userSection');
    t.frmEl.checkAllBox = t.frm.find('.checkAllBox');
    t.frmEl.all_user_check = t.frm.find('#all_user_check');
    t.frmEl.company_id = t.frm.find('#company_id');

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

    t.getFormSelectDropdownParent = function (element) {
        var parent;

        if (!element || !element.length) {
            return t.mdl;
        }

        parent = element.closest(".input-group");
        return parent.length ? parent : t.mdl;
    };

    t.syncFormSelect2Width = function (element) {
        var parent;
        var container;

        if (!element || !element.length) {
            return;
        }

        parent = t.getFormSelectDropdownParent(element);
        container = element.next(".select2-container");

        if (!parent.length || !container.length) {
            return;
        }

        window.requestAnimationFrame(function () {
            var dropdownContainer = parent.children(".select2-container--open").last();
            var width;

            if (!dropdownContainer.length) {
                dropdownContainer = parent.find(".select2-container--open").last();
            }

            if (!dropdownContainer.length) {
                return;
            }

            width = container.outerWidth();
            if (!width) {
                return;
            }

            dropdownContainer.css("width", width + "px");
            dropdownContainer.find(".select2-dropdown").css({
                width: width + "px",
                minWidth: width + "px"
            });
        });
    };

    t.initFormSelect2 = function (element, opts) {
        if (!element || !element.length || typeof $.fn.select2 === "undefined") {
            return;
        }

        opts = $.extend({}, opts);
        opts.dropdownParent = opts.dropdownParent || t.getFormSelectDropdownParent(element);

        if (element.hasClass("select2-hidden-accessible")) {
            element.select2("destroy");
        }

        element.select2(opts);
        element.off("select2:open.announcementModal").on("select2:open.announcementModal", function () {
            t.syncFormSelect2Width($(this));
        });
    };

    var formSelect2Base = { width: "100%" };
    t.initFormSelect2(t.frmEl.company_id, $.extend({}, formSelect2Base, {
        ajax: {
            url: config.url.get_company_by_user_access,
            dataType: "json",
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select Company"
    }));
    t.frmEl.company_id.on('change', function () {
        if (t.isEditing) {
            t.isEditing = false;
            return;
        }
        t.frmEl.department.val(null).trigger('change');
        t.frmEl.location.val(null).trigger('change');
        t.frmEl.users.val(null).trigger('change');
    });

    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');
    t.filters = {
        wrapper: t.content.find("#announcementFilterModal"),
    };
    t.filters.filter_by_company = t.filters.wrapper.find("#filter_by_company"),
    t.filters.filter_by_department = t.filters.wrapper.find("#filter_by_department"),
    t.filters.filter_by_location = t.filters.wrapper.find("#filter_by_location"),
    t.filters.filter_by_user = t.filters.wrapper.find("#filter_by_user"),
    t.filters.date_range = t.filters.wrapper.find("#daterange"),
    t.filters.filter_by_date = t.filters.wrapper.find("#filter_by_date");
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");
    var select2Opts = { width: "100%" };
    t.filters.filter_by_date.select2($.extend({}, select2Opts, {
        dropdownParent: $('#announcementFilterModal')
    }));


    t.data = {
        countries: null,
    };

    /* make table as dataTable */

    t.dTbl = t.table.DataTable({
        autoWidth: true,
        responsive: true,
        processing: true,
        serverSide: true,
        scrollX: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        order: [[8, 'desc']],
         columnDefs: [
            { targets: 0, name: 'a.company_name' },
            { targets: 8, name: 'a.update_at_format' },
            { targets: 9, orderable: false }
        ],

        ajax: {
            url: t.config.url.announcements,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.company_id = companyId;
                d.search = $("#announcement-list-search").val();
                d.filters = t.config.other_filters;
                d.query_filters = t.config.query_filters;
                if (d.order && d.order.length) {
                    d.sorted_direction = d.order[0].dir;
                    d.order_column = d.order[0].column;
                }
            }
        },
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },

        columns: [
            { data: 'a.company_name_text',className: "amg-table-col-188"},
            
            { data: 'a.title',className: "amg-table-col-264" },
            { data: 'a.start_date',className: "amg-table-col-176"},
            { data: 'a.end_date' ,className: "amg-table-col-176"},
           {
                data: 'a.department_name',
                className: "amg-table-col-188",
                render: function (d) {
                    if (!d) return '-';

                    if (d.length > 3) {
                        return `
                            <a href="#" class="view-details"
                            data-values='${JSON.stringify(d)}'
                            data-column="department">

                            ${d.slice(0, 3).map(x => `<span class="role-badge role-badge-admin me-1">${x}</span>`).join('')}
                            +${d.length - 3}
                            </a>
                        `;
                    } else {
                        return d.map(x => `<span class="role-badge role-badge-admin me-1">${x}</span>`).join('');
                    }
                }
            },
           {
                data: 'a.location_name',
                className: "amg-table-col-188",
                render: function (d) {
                    if (!d) return '-';

                    if (d.length > 3) {
                        return `
                            <a href="#" class="view-details"
                            data-values='${JSON.stringify(d)}'
                            data-column="location">

                            ${d.slice(0, 3).map(x => `<span class="role-badge role-badge-user me-1">${x}</span>`).join('')}
                            +${d.length - 3}
                            </a>
                        `;
                    } else {
                        return d.map(x => `<span class="role-badge role-badge-user me-1">${x}</span>`).join('');
                    }
                }
            },
            {
                data: 'a.announcement_members_count',
                className: "amg-table-col-128",
                render: function (data, type, row) {

                    // Access full row using row.a
                    if (row.a?.all_user_check == 1 && row.a?.department_id === null && row.a?.location_id === null) {
                        return "Broadcast";
                    } else {
                        const encodedId = btoa(JSON.stringify({ id: row.a.id }));

                        if (jQuery.inArray("AnnouncementMemberRead", t.config.permissions) !== -1) {
                            return `<a href="${config.url.base_url}/${encodedId}" target="_blank">${data}</a>`;
                        } else {
                            return data ?? "-";
                        }
                    }
                }
            },
            {
                data: 'a.status_id',
                className: "amg-table-col-128",
                render: function (d, type, row) {

                    let status = d == 1 ? "Active" : "Inactive";

                    if (type !== "display") {
                        return status;
                    }

                    let normalized = status.toLowerCase();
                    let isActive = normalized === "active";

                    function getStatusIcon(status) {

                        if (status === "active") {
                            return `
                               <svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                            `;
                        }

                        return `
                            <svg  width="18" height="18" viewBox="0 0 16 16" fill="none"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>
                        `;
                    }

                    return `
                        <span class="user-list-status ${isActive ? 'is-active' : 'is-inactive'}">
                            ${getStatusIcon(normalized)}
                            <span class="b1-text">${status}</span>
                        </span>
                    `;
                }
            },
            { data: 'a.update_at_format',className: "amg-table-col-176" },
            {
                data: 'a',
                className: "amg-table-col-144",
                orderable: false,
                searchable: false,
                render: function (d) {

                    let buttons = '';

                    if (jQuery.inArray("AnnouncementEdit", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="user-list-action-btn me-1 open-edit-modal" data-id="${d.id}" title="Edit">
                                <svg viewBox="0 0 16 16" fill="none"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path></svg>
                            </button>
                        `;
                    }


                    if (jQuery.inArray("AnnouncementDelete", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="user-list-action-btn open-delete me-1" data-toggle="tooltip" data-placement="top" title="Delete" data-id="${d.id}" >
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                            </button>
                        `;
                    }

                    if (
                        jQuery.inArray("AnnouncementMemberRead", t.config.permissions) !== -1 &&
                        (d.all_user_check == 0 || d.department_id != null || d.location_id != null)
                    ) {
                        buttons += `                          
                            <button class="user-list-action-btn view-user me-1" data-toggle="tooltip" data-placement="top" title="Users" data-id="${d.id}">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 20V19C5 15.134 8.13401 12 12 12C15.866 12 19 15.134 19 19V20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                    <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                </svg>
                            </button>
                        `;
                    }

                    return buttons || '';
                }
            }
        ],
        fnInitComplete: function () {
            var api = this.api();
            $('#mytable_length').hide();
            $('#announcement-list-search').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    let value = $(this).val().trim();
                    api.search(value).draw();
                }
            });
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });
        }
    });


    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    $(document).on('click', '.view-details', function (e) {
        e.preventDefault();

        let values = $(this).attr('data-values');
        let column = $(this).attr('data-column');

        try {
            values = JSON.parse(values);
        } catch (err) {
            values = [];
        }

        let title = column === 'department' ? 'Department List' : 'Location List';
        $('#myModal .modal-title').text(title);

        let html = '';

        if (values.length > 0) {
            html += '<div class="d-flex flex-wrap gap-2">';
            values.forEach(function (item) {
                html += `<span class="badge bg-light text-dark border">${item}</span>`;
            });
            html += '</div>';
        } else {
            html = '<p class="text-muted">No data available</p>';
        }

        $('#modalBodyContent').html(html);

        let modal = new bootstrap.Modal(document.getElementById('myModal'));
        modal.show();
    });

    t.cache_filter_values = function () {
        var v = $.trim($("#announcement-list-search").val());
        t.config.search = v;
        t.config.other_filters = {};
        if (t.filters.filter_by_company.val() && t.filters.filter_by_company.val() != 'null') {
            t.config.other_filters.company = t.filters.filter_by_company.val();
        }
        if (t.filters.filter_by_department.val() && t.filters.filter_by_department.val() != 'null') {
            t.config.other_filters.department = t.filters.filter_by_department.val();
        }
        if (t.filters.filter_by_location.val() && t.filters.filter_by_location.val() != 'null') {
            t.config.other_filters.location = t.filters.filter_by_location.val();
        }
        if (t.filters.filter_by_user.val() && t.filters.filter_by_user.val() != 'null') {
            t.config.other_filters.user = t.filters.filter_by_user.val();
        }
        if (t.filters.filter_by_date.val() && t.filters.filter_by_date.val() != 'null') {
            t.config.other_filters.filter_by_date = t.filters.filter_by_date.val();
        }
        if (t.filters.date_range && t.filters.date_range.val() != 'null') {
            t.config.other_filters.date_range = t.filters.date_range.val();
        }
        var jobj = { "search": t.config.search, "other_filters": t.config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, t.filters.filter_by_date.val(), false);
    };

    t.search = function (e) {
        e.preventDefault();
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#announcement-list-search").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert("Please enter a valid value for search");
                return false;
            }
            t.config.search = v;
            t.reload();
        }
        else if (target.tagName == "BUTTON") {
            t.content.find('.advance-filters').addClass('collapse');
            t.cache_filter_values();
            t.reload();
        }
    };

    function padWithZero(number) {
        return number < 10 ? '0' + number : number;
    }

    t.config.userDropdownFormat = function (s, imgPaddingLeft) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        function truncateText(text, length) {
            return text && text.length > length ? text.substring(0, length) + "..." : text;
        }

        var name = truncateText(s.text, 50);
        var email = truncateText(s.email ? s.email : "", 50);

        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-9'>";
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + name + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + email + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div style='padding-left: " + imgPaddingLeft + ";'><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    t.createAnnouncement = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.loader.hide();
        t.mdl.modal("show");
        t.mdltitle.text(t.config.translations.add_announcement);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;
        t.frm.attr('action', config.url.add);
        t.frmEl.userSection.show();
        t.frmEl.checkAllBox.show();
        t.frmEl.all_user_check.prop('checked', false);
        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.frmEl.company_id.append(option).trigger('change');
        }

        var newDate = new Date();
        var endOfDay = new Date(newDate);
        endOfDay.setHours(23, 59, 59);

        var startDateFormatted = padWithZero(newDate.getDate()) + '/' + padWithZero(newDate.getMonth() + 1) + '/' + newDate.getFullYear() + ' ' + padWithZero(newDate.getHours()) + ':' + padWithZero(newDate.getMinutes()) ;
        var endDateFormatted = padWithZero(endOfDay.getDate()) + '/' + padWithZero(endOfDay.getMonth() + 1) + '/' + endOfDay.getFullYear() + ' ' + padWithZero(endOfDay.getHours()) + ':' + padWithZero(endOfDay.getMinutes());

        t.frmEl.start_date.val(startDateFormatted);
        t.frmEl.end_date.val(endDateFormatted);
        t.frmEl.status.val(0).trigger("change");
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var startDateStr = t.frm.find('[name="start_date"]').val();
        var endDateStr = t.frm.find('[name="end_date"]').val();
        var startDateParts = startDateStr.split(/[\s/:]/);
        var endDateParts = endDateStr.split(/[\s/:]/);
        var startDate = new Date(startDateParts[2], startDateParts[1] - 1, startDateParts[0], startDateParts[3], startDateParts[4], startDateParts[5]);
        var endDate = new Date(endDateParts[2], endDateParts[1] - 1, endDateParts[0], endDateParts[3], endDateParts[4], endDateParts[5]);
        if (endDate < startDate) {
            sweetAlert('center', 'error', { msg: 'End date cannot be earlier than start date.' });
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);
        var http = $.ajax({
            url: t.frm.attr('action'),
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function () {
                t.btn.submit.prop('disabled', true);
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    t.mdl.modal("hide");
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
            t.btn.submit.prop('disabled', false);
        });
    };
    t.loadForm = function (obj) {

        t.frmEl.title.val(obj.title);
        t.frmEl.announcement.summernote('code', obj.announcement);


        t.frmEl.start_date.val(moment(obj.start_date).format('DD/MM/YYYY HH:mm'));

            if (obj.end_date != null) {
                t.frmEl.end_date.val(moment(obj.end_date).format('DD/MM/YYYY HH:mm'));
            }

        t.frmEl.status.val(obj.status_id).trigger("change");


        if (obj.company_name) {
            let option = new Option(obj.company_name.name, obj.company_name.id, true, true);
            t.frmEl.company_id.append(option).trigger('change');
        }


        $.each(obj.department_name, function (key, value) {
            let option = new Option(value.name, value.id, true, true);
            t.frmEl.department.append(option).trigger('change');
        });


        $.each(obj.location_name, function (key, value) {
            let option = new Option(value.name, value.id, true, true);
            t.frmEl.location.append(option).trigger('change');
        });

        // USERS
        $.each(obj.users, function (key, value) {
            let option = new Option(value.name, value.id, true, true);
            t.frmEl.users.append(option).trigger('change');
        });

        // CHECKBOX
        if (obj.all_user_check == 1) {
            t.frmEl.all_user_check.prop('checked', true);
            t.frmEl.userSection.hide();
        } else {
            t.frmEl.all_user_check.prop('checked', false);
            t.frmEl.userSection.show();
        }

        t.frm.attr('action', config.url.update + "/" + obj.id);
    };

    t.resetFrm = function () {
        t.frmEl.company_id.val(null).trigger("change");
        t.mdl.find("input[name='title']").val("");
        t.mdl.find("textarea[name='content']").summernote('code', "");
        t.frmEl.end_date.val('');
        t.frmEl.start_date.val('');
        t.frmEl.location.val(null).trigger('change');
        t.frmEl.department.val(null).trigger('change');
        t.frmEl.users.val(null).trigger('change');
        t.frmEl.status.val('null').trigger('change');
        t.frmEl.all_user_check.prop('checked', false);
        t.frmEl.userSection.show();
        t.frmEl.checkAllBox.show();
        t.frmValidator.resetForm();
        t.frm.find(".amg-form-error-wrap").empty();
        t.frm.find(".amg-form-invalid").removeClass("amg-form-invalid");
        t.frm.find(".amg-form-select-error").removeClass("amg-form-select-error");
        t.frm.find(".error").removeClass("error");
    };

    t.editAnnouncement = function (e) {
        e.preventDefault();
        t.resetFrm();
        var announcementId = $(this).attr("data-id");
        var http = $.get(t.config.url.get + "/" + announcementId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.isEditing = true;
                    t.mdltitle.text(config.translations.edit_announcement);
                    t.btn.submit.text(config.translations.save_changes);
                    t.loadForm(data.data);
                    t.mdl.modal("show");
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteAnnouncement = function (e) {
        e.preventDefault();
        var announcementId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + announcementId;

        sweetAlerts('Are you sure to delete this announcement?', 'warning', t.httpPostPath, t.dTbl, { msg: 'Something went wrong. Please check given details are correct' });
    };

    t.frmValidator = t.frm.validate({
        ignore: [],
        debug: false,
        rules: {
            company_id: {
                required: true,
                clean_text_only: true,
            },
            title: {
                required: true,
                clean_text_only: true,
            },
            content: {
                required: true,
                summernote: true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true
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

    t.frm.find("select").on("change", function () {
        var element = $(this);

        if (element.hasClass("error") || element.closest(".input-group").hasClass("amg-form-invalid")) {
            element.valid();
        }
    });

    t.frm.find("input[name='title'], input[name='start_date'], input[name='end_date']").on("input change blur", function () {
        var element = $(this);

        if (element.hasClass("error")) {
            element.valid();
        }
    });

    t.frmEl.announcement.off("summernote.change.announcementForm").on("summernote.change.announcementForm", function () {
        var element = $(this);

        if (
            element.hasClass("error") ||
            element.closest(".input-group").hasClass("amg-form-invalid") ||
            element.next(".note-editor").hasClass("amg-form-select-error")
        ) {
            element.valid();
        }
    });

    t.handleCountryChange = function (e) {
        var data = $(this).select2("data");
        if (t.config.locations && data && data.length > 0) {
            const filtered_locations = data.map((x) =>
                t.config.locations.filter((y) => y.country == x.country)
            );
            const selected_locations = [].concat(...filtered_locations);
            const locations = t.config.locations.filter((x) =>
                selected_locations.find((y) => x.id == y.id)
            );
            t.frmEl.location_id
                .select2("destroy")
                .empty()
                .select2(
                    $.extend({}, select2Opts, {
                        data: locations,
                        placeholder: config.translations.select_location,
                    })
                );
        } else {
            t.frmEl.location_id
                .select2("destroy")
                .empty()
                .select2(
                    $.extend({}, select2Opts, {
                        data: t.config.locations,
                        placeholder: config.translations.select_location,
                    })
                );
        }
    };

    t.refillLocations = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEl.location_id.empty();
        var data = $(this).select2("data");
        data = data.map((x) => x.country_code).join(",");
        t.data.countries = data;
        t.frmEl.alllocations_id.prop('checked', false);
        $("#location_id").attr('disabled', false);
    };

    t.ajaxLocations = function () {
        t.frmEl.location_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        $.get(t.config.url.locations, { countries: t.data.countries }).done(function (data) {
            if (typeof data == "object" && data.results.length) {
                $.each(data.results, function (i, v) {
                    t.frmEl.location_id.append(new Option(v.text, v.id));
                });
            }
        })
            .always(function () {
                t.frmEl.location_id.trigger("change");
            });
    };

    t.initFormSelect2(t.frmEl.department, $.extend({}, formSelect2Base, {
        ajax: {
            url: t.config.url.department,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.frmEl.company_id.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results,
                    pagination: {
                        more: data.pagination.more
                    }
                };
            }
        },
        placeholder: "Select Department"
    }));
    t.initFormSelect2(t.frmEl.location, $.extend({}, formSelect2Base, {
        ajax: {
            url: t.config.url.location,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.frmEl.company_id.val()
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results || [],
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            },
            cache: true
        },
        placeholder: config.translations.select_location
    }));
    var userSelect2Opts = {
        width: "100%",
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 80 ? data.text.substring(0, 80) + '...' : data.text;
        }
    };
    t.initFormSelect2(t.frmEl.users, $.extend({}, userSelect2Opts, {
        ajax: {
            url: t.config.url.users,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    department: t.frmEl.department.val(),
                    location: t.frmEl.location.val(),
                    company_id: t.frmEl.company_id.val(),
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                let results = [];
                if (data.results) {
                    results = data.results;
                } else if (data.data) {
                    results = data.data;
                } else if (Array.isArray(data)) {
                    results = data;
                }
                results = results.map(function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name || item.email,
                    };
                });
                return {
                    results: results,
                    pagination: {
                        more: data.pagination && data.pagination.more
                    }
                };
            },
            cache: true
        },
        placeholder: config.translations.select_users,
    }));

    t.filters.filter_by_department.select2({
        width: "100%",
        dropdownParent: $('#announcementFilterModal'),
        ajax: {
            url: t.config.url.department,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.filters.filter_by_company.val(),
                };
            },
            processResults: function (data) {
                let results = [];
                if (data.results) {
                    results = data.results;
                } else if (Array.isArray(data)) {
                    results = data;
                } else if (data.data) {
                    results = data.data;
                }
                results = results.map(function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name || item.department_name
                    };
                });
                return {
                    results: results
                };
            }
        },

        placeholder: config.translations.select_departments
    });

    t.filters.filter_by_location.select2({
        width: "100%",
        dropdownParent: $('#announcementFilterModal'),

        ajax: {
            url: t.config.url.location,
            dataType: "json",
            delay: 300,

            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.filters.filter_by_company.val(),
                };
            },

            processResults: function (data) {
                let results = [];

                if (data.results) {
                    results = data.results;
                } else if (Array.isArray(data)) {
                    results = data;
                } else if (data.data) {
                    results = data.data;
                }

                results = results.map(function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name || item.location_name
                    };
                });

                return {
                    results: results
                };
            }
        },

        placeholder: config.translations.select_location
    });


    t.filters.filter_by_user.select2({
        width: "100%",
        dropdownParent: $('#announcementFilterModal'),
        ajax: {
            url: t.config.url.users,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page || 1,
                    company_id: t.filters.filter_by_company.val(),
                };
            },
            processResults: function (data) {
                let results = [];
                if (data.results) {
                    results = data.results;
                } else if (data.data) {
                    results = data.data;
                } else if (Array.isArray(data)) {
                    results = data;
                }
                results = results.map(function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name || item.full_name || item.email
                    };
                });

                return {
                    results: results
                };
            }
        },
        placeholder: config.translations.select_users
    });

    t.filters.filter_by_company.select2({
        width: "100%",
        dropdownParent: $('#announcementFilterModal'),
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                let results = [];
                if (data.results) {
                    results = data.results;
                } else if (Array.isArray(data)) {
                    results = data;
                } else if (data.data) {
                    results = data.data;
                }
                results = results.map(function (item) {
                    return {
                        id: item.id,
                        text: item.text || item.name || item.company_name
                    };
                });

                return {
                    results: results
                };
            }
        },
        placeholder: "Select Company"
    });

    if (config.company_defulte && config.company_defulte.length > 0) {
        let companies = config.company_defulte;

        companies.forEach(function (company) {
            let option = new Option(company.text, company.id, true, true);
            t.filters.filter_by_company.append(option);
        });

        t.filters.filter_by_company.trigger('change');
    }

    t.initFormSelect2(t.frmEl.status, $.extend({}, formSelect2Base, {}));

    t.openFilter = function (e) {
        e.preventDefault();
        var modal = new bootstrap.Modal(document.getElementById('announcementFilterModal'));
        modal.show();
        setTimeout(function () {
        if (!$('#reportrange').data('daterangepicker')) {

            let start = moment().startOf('day');
            let end = moment().endOf('day');
            function cb(start, end) {
                $('#reportrange span').html(
                    start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format('DD-MM-YYYY HH:mm:ss')
                );
                $('#daterange').val(
                    start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss')
                );
            }
            $('#reportrange').daterangepicker({
                parentEl: '#announcementFilterModal',
                startDate: start,
                endDate: end,
                timePicker: true,
                timePicker24Hour: true,
                timePickerSeconds: true,
                timePickerIncrement: 1,
                autoApply: false,
                autoUpdateInput: false,
                opens: 'right',
                drops: 'down',
                locale: {
                    format: 'DD-MM-YYYY HH:mm:ss',
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment().startOf('day'), moment().endOf('day')],
                    'Yesterday': [
                        moment().subtract(1, 'days').startOf('day'),
                        moment().subtract(1, 'days').endOf('day')
                    ],
                    'Last 7 Days': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                    'Last 30 Days': [moment().subtract(29, 'days').startOf('day'), moment().endOf('day')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ]
                }

            }, cb);
            cb(start, end);
        }
    }, 300);


    }

    t.changeStatus = function (e) {
        var value;
        if ($(this).prop('checked') == true) {
            var value = 1;
        } else {
            value = 0;
        }

        $.ajax({
            type: "POST",
            url: t.config.url.changeStatus,
            data: { id: $(this).attr('data-id'), value: value },
            success: function (data) {
                Swal.fire({
                    title: '',
                    text: data.msg,
                    icon: data.status
                });
            }
        })
    }

    t.downloadExcel = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        let selectedCompanyId = t.filters.filter_by_company.val();
        let companyId = (selectedCompanyId && selectedCompanyId !== 'null') ? selectedCompanyId : (config.company_defulte && config.company_defulte.length ? config.company_defulte[0].id : "");
        let exportUrl = t.config.url.export + "?q=" + encodeURIComponent(t.config.export_filters) + "&company_id=" + companyId;
        window.location = exportUrl;
    };

    t.btnClrFilter = function () {

        t.filters.filter_by_company.val(null).trigger("change");
        t.filters.filter_by_department.val(null).trigger("change");
        t.filters.filter_by_location.val(null).trigger("change");
        t.filters.filter_by_user.val(null).trigger("change");
        t.filters.filter_by_date.val("null").trigger("change");
        t.filters.date_range.val('');
        t.config.other_filters = {};
        $('.filter-count-badge').addClass('d-none').text(0);
        $('#announcementFilterModal').modal('hide');
        t.reload();
    };
    t.content.on('click', '.btn-filter', function () {
        $('#announcementFilterModal').modal('hide');
        t.cache_filter_values();
        updateFilterCount(t.config.other_filters);
        t.reload();
    });

    function updateFilterCount(filters) {
        let count = 0;
        if (filters.company && filters.company.length > 0) {
            count++;
        }
        if (filters.user && filters.user.length > 0) {
            count++;
        }
        if (filters.department && filters.department.length > 0) {
            count++;
        }
        if (filters.location && filters.location.length > 0) {
            count++;
        }
        if (
            filters.filter_by_date &&
            filters.filter_by_date !== 'null' &&
            filters.date_range &&
            filters.date_range !== ''
        ) {
            count++;
        }
        let badge = $('.filter-count-badge');
        if (count > 0) {
            badge.removeClass('d-none').text(count);
        } else {
            badge.addClass('d-none').text(0);
        }
    }


    t.checkAll = function () {
        if ($(this).is(":checked") == true) {
            t.frmEl.userSection.hide();
            t.frmEl.users.empty().append();
        } else {
            t.frmEl.userSection.show();
            t.frmEl.users.val([]).trigger("change");
        }
    }


    var newDate = new Date();
    t.frmEl.start_date.flatpickr({
        dateFormat: "d/m/Y H:i",
        enableTime: true,
        time_24hr: true,
        step: 30,
        "minDate": newDate
    });
    t.frmEl.end_date.flatpickr({
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        enableTime: true,
        step: 30,
        "minDate": newDate
    });

    t.frmEl.company_id.on('change', function () {
        t.frmEl.department.val(null).trigger('change');
    });
    t.frmEl.department.on('change', function () {
        t.frmEl.users.val(null).trigger('change');
    })
    t.frmEl.location.on('change', function () {
        t.frmEl.users.val(null).trigger('change');
    })

    t.viewMember = function (e) {
        var jobj = { "id": $(this).attr('data-id') };
        let ID = btoa(JSON.stringify(jobj));
        window.open(t.config.url.base_url + '/' + ID, '_blank');
    }

    t.openModal = function () {
        var values = $(this).data('values');
        var column = $(this).data('column');
        var modalBody = $('#modalBodyContent');
        var modalTitle = $('#myModal .modal-title');

        if (column === 'department') {
            modalTitle.text(t.config.translations.all_department);
        } else if (column === 'location') {
            modalTitle.text(t.config.translations.all_location);
        }
        modalBody.empty();

        values.forEach(function (value) {
            modalBody.append(`<span class='badge'>${value}</span>`);
        });
    }
    t.content.on('click', '.open-add-modal', $.proxy(t.createAnnouncement));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editAnnouncement));
    t.content.on('click', '.open-delete', $.proxy(t.deleteAnnouncement));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
    t.content.on('click', '.btn-searchbox', $.proxy(t.search));
    t.content.on('click', '#btnFilter', $.proxy(t.search));
    t.content.on('click', '.btn-open-filter', $.proxy(t.openFilter));
    t.content.on('click', '.chkParent', $.proxy(t.changeStatus));
    t.content.on('click', '.btn-download', $.proxy(t.downloadExcel));
    t.content.on('click', '.btn-clear-filter', $.proxy(t.btnClrFilter));
    t.content.on('click', '#all_user_check', $.proxy(t.checkAll));
    t.content.on('click', '.view-user', $.proxy(t.viewMember));
    t.content.on('click', '.view-details', $.proxy(t.openModal));
    t.mdl.on('hidden.bs.modal', function () {
        t.resetFrm();
    });



};

