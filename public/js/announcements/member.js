var AnnouncementMember = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.mdl = t.content.find('#announcementMemberMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#announcementMemberForm');

    t.frmEl = {};
    t.frmEl.users = t.frm.find('#users');
    t.frmEl.user = t.frm.find('#user');
    t.frmEl.addMember = t.frm.find('.addMember');
    t.frmEl.checkAllBox = t.frm.find('.checkAllBox');
    t.frmEl.all_user_check = t.frm.find('#all_user_check');
    t.frmEl.editMember = t.frm.find('.editMember');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.filters = {
        wrapper: t.content.find("#advance-filters"),
    };
    t.filters.filter_by_user = t.filters.wrapper.find("#filter_by_user"),

    t.searchbox = t.content.find("#announcement-list-search");
    t.searchbtn = t.content.find(".btn-searchbox");

    t.data = {
        countries: null,
    };

    t.icons = {
        edit: ` <svg viewBox="0 0 16 16" fill="none" ><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"/></svg>
        </svg>`,

        trash: `<svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>`
    };

    t.actionButtonHtml = function(label, classes, id, icon) {
        return `
            <button type="button"
                class="user-list-action-btn ${classes}"
                data-id="${id}"
                title="${label}">
                ${icon}
            </button>
        `;
    };

    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: true,
        responsive: true,
        processing: true,
        serverSide: true,
        scrollX: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        order: [[2, 'asc']],  
        columnDefs: [
            { targets: 0, name: 'u.first_name' },          
            { targets: 1, name: 'u.email' },                
            { targets: 2, name: 'announcement_users.updated_at' }, 
            { targets: 3, orderable: false, searchable: false, className: "amg-table-col-144" }
        ],
        ajax: {
            url: t.config.url.ajaxMember,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.search = $('#announcement-list-search').val();
                d.annoucement_id = t.config.announcementID.id;
                d.company_id = t.config.announcementID.company_id;
                if (d.order && d.order.length) {
                    d.sorted_direction = d.order[0].dir;
                    d.order_column = d.order[0].column;
                }
            }
        },
        columns: [
            { data: 'a.name', className: "amg-table-col-188" },
            { data: 'a.email', className: "amg-table-col-264" },
            { data: 'a.updated_at_format', className: "amg-table-col-176" },
            {
                data: 'a',
                className: "amg-table-col-144",
                render: function (d) {
                    let actions = [];
                    if (jQuery.inArray("AnnouncementMemberEdit", t.config.permissions) !== -1) {
                        actions.push(t.actionButtonHtml(config.translations.edit, "open-edit-modal", d.id, t.icons.edit));
                    }
                    if (jQuery.inArray("AnnouncementMemberDelete", t.config.permissions) !== -1) {
                        actions.push(t.actionButtonHtml(config.translations.delete, "open-delete is-delete", d.id, t.icons.trash));
                    }
                    return `<div class="user-list-actions">${actions.join("")}</div>`;
                }
            }
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $('#announcement-list-search').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    api.search($(this).val().trim()).draw();
                }
            });
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                api.page.len(parseInt($(this).val())).draw();
            });
        }
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.cache_filter_values = function() {
        var v = $.trim($('#announcement-list-search').val());
        t.config.search = v;
        t.config.other_filters = {};
        if(t.filters.filter_by_user.val() && t.filters.filter_by_user.val() != 'null'){
            t.config.other_filters.user = t.filters.filter_by_user.val();
        }
        var jobj = {"search":t.config.search,"other_filters":t.config.other_filters};
        t.config.export_filters = btoa(JSON.stringify(jobj));
        filterCount(t.config.other_filters, null, false);
    };

    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v =t.searchbox.validate_str_param();
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

    t.createAnnouncementMember = function (e) {
        e.preventDefault();
        t.resetFrm();
        t.loader.hide();
        t.mdl.modal("show");
        t.mdltitle.text(config.translations.add_announcement_member);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.addMember;
        t.frmEl.addMember.removeClass('d-none');
        t.frmEl.editMember.addClass('d-none');
        t.frmEl.checkAllBox.removeClass('d-none');
        t.frmEl.all_user_check.prop('checked', false)
        t.frmEl.users.prop('disabled', false);
        t.frm.attr('action',config.url.addMember);
    };

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        frmData.append('_token', t.config.token);``
        var http = $.ajax({
            url: t.frm.attr('action'),
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function() {
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
            let data = { msg: 'Something went wrong. Please check given details are correct'};
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.loader.hide();
            t.btn.submit.prop('disabled', false);
        });
    };

    t.loadForm = function (obj) {
        // show edit UI
        t.frmEl.editMember.removeClass('d-none');
        t.frmEl.addMember.addClass('d-none');
        t.frmEl.checkAllBox.addClass('d-none');

        // clear previous value
        t.frmEl.user.val(null).trigger('change');

        $.each(obj, function (key, value) {
            let option = new Option(value.name, value.id, true, true);
            t.frmEl.user.append(option).trigger('change');
        });
    };

    t.resetFrm = function () {
        t.frmEl.users.val(null).trigger('change');
        t.frmEl.user.val(null).trigger('change');
        t.frmValidator.resetForm();
    };

    t.editAnnouncementMember = function (e) {
        e.preventDefault();
        t.resetFrm();
        var announcementId = $(this).attr("data-id");
        var http = $.get(t.config.url.editMember + "/" + announcementId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdltitle.text(config.translations.edit_announcement_member);
                    t.btn.submit.text(config.translations.save_changes);
                    t.loadForm(data.data);
                    t.frm.attr('action',config.url.updateMember+"/"+ announcementId);
                    t.mdl.modal("show");
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct'};
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteAnnouncementMember = function (e) {
        e.preventDefault();
        var announcementId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.deleteMember + "/" + announcementId;

        sweetAlerts('Are you sure to delete this announcement member?', 'warning', t.httpPostPath, t.dTbl, {msg:'Something went wrong. Please check given details are correct'});
    };

    t.frmValidator = t.frm.validate({
        ignore: [],
        debug: false,
        rules: {
            title: {
                required: true
            },
            content: {
                required: true,
                summernote:true
            },
            start_date: {
                required: true
            },
            end_date: {
                required: true
            }
        },
        errorPlacement: function(error, element) {
            error.appendTo( element.parent("div").parent("div") );
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
                        placeholder: config.translations.select_users,
                    })
                );
        } else {
            t.frmEl.location_id
                .select2("destroy")
                .empty()
                .select2(
                    $.extend({}, select2Opts, {
                        data: t.config.locations,
                        placeholder: "Select Locations",
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


    var select2Opts = { width: "100%" };

    t.frmEl.all_user_check.on('click', function(){
        if ($(this).prop('checked')==true){
            t.frmEl.users.prop('disabled', true);
        }else{
            t.frmEl.users.prop('disabled', false);
        }
    });
    var select2Opts = {
        width: "100%",
        templateSelection: function(data, container) {
            $(container).attr('title', data.text);
            return data.text.length > 60 ? data.text.substring(0, 60) + '...' : data.text;
        }
    };
    t.frmEl.users.select2(
        $.extend({}, select2Opts, {
            dropdownParent: $('#announcementMemberMdl'),
            ajax: {
                url: t.config.url.users,
                dataType: "json",
                data: function (params) {
                    var query = {
                        q: params.term,
                        page: params.page || 1,
                        department : t.config.announcementID.department_id != null ? (t.config.announcementID.department_id).split(',') : null,
                        location : t.config.announcementID.location_id != null ? (t.config.announcementID.location_id).split(',') : null,
                        company_id:t.config.announcementID.company_id,
                    }
                    return query;
                }
            },
            placeholder: config.translations.select_users,
            templateResult: function(data) {
                if (!data) return $("<div>No data</div>");
                var imgPaddingLeft = "20px";
                return t.config.userDropdownFormat(data, imgPaddingLeft);
            }
        })
    );

    t.config.userDropdownFormat = function (s,imgPaddingLeft) {
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

    t.frmEl.user.select2(
        $.extend({}, select2Opts, {
            dropdownParent: $('#announcementMemberMdl'),
            ajax: {
                url: t.config.url.users,
                dataType: "json",
                data: function (params) {
                    var query = {
                        q: params.term,
                        page: params.page || 1,
                        department : t.config.announcementID.department_id != null ? (t.config.announcementID.department_id).split(',') : null,
                        location : t.config.announcementID.location_id != null ? (t.config.announcementID.location_id).split(',') : null,
                        company_id:t.config.announcementID.company_id,
                    }
                    return query;
                }
            },
            placeholder: config.translations.select_users,
        })
    );


    t.filters.filter_by_user.select2(
        $.extend({}, select2Opts, {
            ajax: {
                url: t.config.url.users,
                dataType: "json",
                data: function (params) {
                    var query = {
                        q: params.term,
                        page: params.page || 1,
                        company_id:config.company_defulte[0].id,
                    }
                    return query;
                }
            },
            placeholder: config.translations.select_users,
        })
    );


    t.openFilter = function(e) {
        if(t.content.find('.advance-filters').hasClass('collapse')){
            t.content.find('.advance-filters').removeClass('collapse');
        }else{
            t.content.find('.advance-filters').addClass('collapse');
        }

    }

    t.downloadExcel = function (e){
        e.preventDefault();
        t.cache_filter_values();
        window.location =t.config.url.export + "?q=" + t.config.export_filters;
    }

    t.btnClrFilter = function() {

        t.filters.filter_by_user.val('').trigger("change");
        t.content.find('.advance-filters').addClass('collapse');
        t.cache_filter_values();
        t.reload();
    }

    t.content.on('click', '.open-add-modal', $.proxy(t.createAnnouncementMember));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editAnnouncementMember));
    t.content.on('click', '.open-delete', $.proxy(t.deleteAnnouncementMember));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.content.on('click', '.btn-reload-list',$.proxy(t.reload));
    t.content.on('click', '.btn-searchbox',$.proxy(t.search));
    t.content.on('click', '#btnFilter',$.proxy(t.search));
    t.content.on('click','.btn-visible-content',$.proxy(t.openFilter));
    t.content.on('click','#btnClrFilter',$.proxy(t.btnClrFilter));
};

