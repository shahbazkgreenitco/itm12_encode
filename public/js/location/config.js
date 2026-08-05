var TicketConfig = function (config) {
    var t = this;
    t.config = config;
    t.section = $('main#mainContent');

    t.user = new User(t.config);
};


var User = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main#mainContent');

    t.table = t.content.find('#mytable');
    t.show_entries = $('#showSelect')
    t.btn = {};
    t.btn.reload = t.content.find(".btn-reload-list");
    t.btn.search = t.content.find(".amg-list-searchbar__icon");
    t.btn.export = t.content.find(".btn-export-maintenance");
    t.no_data = t.content.find('#no-data');
    let $loader = t.content.find("#loader");
    let $noData = t.content.find("#no-data");
    let $locationsList = t.content.find("#locationsList");
    let $loadMore = t.content.find('#loadMoreTrigger');
    let $usersContainer = t.content.find('#usersContainer');

    let page = 0;
    let limit = parseInt(localStorage.getItem('user_locations_page_limit')) || 10;
    let loading = false;
    let hasMore = true;
    let search = '';
    let usersCache = {};
    let renderedCache = {};

    /* t.tblHelpers = {
        user_info: function () {
            return function (d) {
                return "<div class='mb-2'><strong>" + d.username + "</strong></div>" + "<div class='mb-2 text-danger'>" + (d.user_full_name || "") + "</div>" + "<div>"
                    + "<div class='mb-2' style='color: #0044cc'>" + (d.useremail || "") + "</div><div class='text-success'>" + (d.emp_code || "") + "</div>" + "<div><em>" + (d.location || "") + "</em></div> <div class='checkbox-container'>"
                    + "<label for='check_all_" + d.id + "' class='control-label mar-rgt mb-2'><input type='checkbox' class='checkall_previlege' id='check_all_" + d.id + "' data-belongs='location_" + d.id + "' data-user='" + d.id + "' class=''><span class=''> " + config.translations.check_all + "</span></label></div>";
            }
        },
        actions: function (locations) {
            return function (d) {
                var r = [];
                var privileges = (d.permitted_locations != "" && d.permitted_locations != null && d.permitted_locations != "null") ? d.permitted_locations.split(",") : [];

                $.each(locations, function (i, loc) {
                    var checked = $.inArray(loc.id.toString(), privileges) >= 0 ? "checked" : "";
                    var a_loc = '<label for="location_' + d.id + '_' + loc.id + '" class="control-label mar-rgt mb-2"><input type="checkbox" name="location" value="' + loc.id + '" id="location_' + d.id + '_' + loc.id + '" data-belongs="location_' + d.id + '" class="location_previlege" ' + checked + ' /><span class=""> ' + loc.name + '</span></label>';
                    r.push(a_loc);
                });

                //  var str = '<div><strong>Locations</strong></div><div>' + r.join(' ') + '</div><div class="pad-top"><button type="button" id="location_' + d.id + '" class="update_user_privilege hide btn btn-theme-red" data-user="' + d.id + '">Update Changes</button></div>';
                var str = '<div class="mb-2"><strong>Locations</strong></div>' +
                    '<div class="checkbox-grid checkbox-container">' + r.join(' ') + '</div>' +
                    '<div class="pad-top">' +
                    '<button type="button" id="location_' + d.id + '" class="update_user_privilege d-none amg-btn amg-btn-outline" data-user="' + d.id + '" data-original="' + privileges.join(',') + '">' + config.translations.update_changes + '</button>' +
                    '</div>';
                return str;
            };
        }
    }; */

    t.getToken = function () {
        return config.token;
    };

    t.reload = function () {
        // t.dTbl.ajax.reload();
        t.loadUsers(true);
    };

    t.renderUsers = function (users) {
        let html = '';

        $.each(users, function (index, user) {
            usersCache[user.a.id] = user;

            let activeClass = (page === 0 && index === 0) ? 'active' : '';
            let email = user.a.useremail ?? "";

            html += `
                <a href="#" class="nav-link ${activeClass} d-flex align-items-center w-100" data-id="${user.a.id}">
                    <div>${user.a.user_full_name ?? "-"} (${user.a.username ?? "-"})</div>
                    ${email ? `<i class="bi bi-copy copy-email-btn ms-auto flex-shrink-0" data-email="${email}" title=${config.translations.copy_email} style="cursor: pointer; z-index: 10;"></i>` : ''}
                </a>
            `;
        });

        $usersContainer.append(html);
    }

    t.loadUsers = function (reset = false) {
        if (loading) return;
        if (!reset && !hasMore) return;

        loading = true;
        $noData.addClass('d-none');

        if (reset) {
            page = 0;
            hasMore = true;
            $usersContainer.html('');
            $locationsList.addClass('d-none');
            $loadMore.addClass('d-none');
            // $loader.removeClass('d-none');
        } else {
            $loadMore.removeClass('d-none');
            // $loader.addClass('d-none');
        }

        Pace.restart();

        let searchValue = $("#tableSearch").val();

        $.ajax({
            url: t.config.url.get_users,
            type: "POST",
            data: {
                _token: t.getToken(),
                start: page * limit,
                length: limit,
                search: { value: searchValue }
            },
            success: function (res) {
                if (!res.data || res.data.length === 0) {
                    if (page === 0) {
                        $noData.removeClass('d-none');
                        $locationsList.addClass('d-none');
                        $loadMore.addClass('d-none');
                    }
                    hasMore = false;
                    $("#loadMoreTrigger").hide();
                    return;
                }

                t.renderUsers(res.data);
                $locationsList.removeClass('d-none');

                if (page === 0) {
                    $usersContainer.find(".nav-link:first").trigger("click");
                }

                if (res.data.length < limit) {
                    hasMore = false;
                    $loadMore.addClass('d-none');
                } else {
                    $loadMore.removeClass('d-none');
                }

                page++;

                t.ensureScrollable();
            },
            error: function () {
                alert(config.translations.something_went_wrong);
            },
            complete: function () {
                loading = false;
                // $loader.addClass('d-none');
                Pace.stop();
            }
        });
    };

    $(document).on("click", "#usersList .nav-link", function (e) {
        e.preventDefault();

        if ($(e.target).closest('.copy-email-btn').length) {
            return;
        }

        let userId = $(this).data("id");

        if (!renderedCache[userId]) {
            let user = usersCache[userId];
            renderedCache[userId] = t.renderUserLocations(user, t.config.locations);
        }

        $("#v-pills-tabContent").html(renderedCache[userId]);

        $("#usersList .nav-link").removeClass("active");
        $(this).addClass("active");
    });

    t.renderUserLocations = function (user, locations) {

        let privileges = (user.a.permitted_locations && user.a.permitted_locations !== "null")
            ? user.a.permitted_locations.split(",")
            : [];

        let html = `
            <div class="tab-pane fade show active" id="user-${user.id}" role="tabpanel">

                <div class="mb-3">
                    <h5 class="fw-semibold mb-0">${user.a.user_full_name || ''}</h5>
                </div>

                <!-- Select All -->
                <div class="row px-3">
                    <div class="form-check py-2">
                        <input type="checkbox"
                            class="form-check-input checkall_previlege"
                            data-user="${user.a.id}">
                        <label class="form-check-label b1-text opacity-50">
                            ${config.translations.check_all}
                        </label>
                    </div>
                </div>

                <div class="row">
                    ${t.renderLocationsGrid(locations, user.a.id, privileges)}
                </div>

                <div class="amg-btn-group mt-3">
                    <button type="button"
                            class="amg-btn amg-btn-primary update_user_privilege"
                            data-user="${user.a.id}"
                            data-original="${privileges.join(',')}">
                        ${config.translations.update_changes}
                    </button>
                </div>

            </div>`;

        $("#v-pills-tabContent").html(html);
    }

    t.renderLocationsGrid = function (locations, userId, privileges) {
        if (!locations || locations.length === 0) {
            return `<div class="text-muted p-3">${config.translations.no_locations}</div>`;
        }

        let html = '';

        $.each(locations, function (i, loc) {

            let checked = $.inArray(loc.id.toString(), privileges) >= 0 ? 'checked' : '';

            html += `
                <div class="col-md-4">
                    <div class="form-check py-2">
                        <input type="checkbox"
                            class="form-check-input location_previlege"
                            value="${loc.id}"
                            data-user="${userId}"
                            id="location_${userId}_${loc.id}"
                            ${checked}>
                        <label class="form-check-label b1-text">${loc.name}</label>
                    </div>
                </div>
            `;
        });

        return html;
    }

    t.loadUsers(true);

    /* t.dTbl = t.table.DataTable({
        autoWidth: false,
        dom: "lrtip",
        lengthChange: false,
        pageLength: 10,
        aoColumnDefs: [{
            targets: 1,
            bSortable: false,
            render: t.tblHelpers.actions(t.config.locations)
        }, {
            targets: 0,
            render: t.tblHelpers.user_info()
        }],
        order: [[0, 'asc']],
        processing: true,
        serverSide: true,
        deferLoading: 0,
        ajax: {
            url: t.config.url.get_users,
            type: "post",
            data: function(d) {
                d._token = t.getToken();
            }
        },
        columns: [
            {data: 'a', width: "20%"},
            {data: 'a'}
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            // var searchBox = '<div class="input-group table-search-btns">' +
            //     '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.serach_option + '" />' +
            //     '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
            //     '<span class="input-group-addon  btn-reload-list" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.Refresh_List + '"><i class="ps-icon fa fa-refresh"></i></span>' +
            //     '<span class="input-group-addon  btn-export-maintenance" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.Download + '"><i class="ps-icon fa fa-download"></i></span>' +
            //     '</div>';
            // $("#mytable_wrapper").removeClass("form-inline");
            // t.table.closest("div").addClass("table-responsive");
            // $(searchBox).insertBefore("#mytable_filter");
            // $("#mytable_filter").remove();
            // $("#mytable_length").find("select").select2();
            $("#tableSearch").on("keyup", function(e) {
                if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert(config.translations.valid_search);
                        return false;
                    }
                    api.search(v).draw();
                }
            });

            t.btn.reload = t.content.find(".btn-reload-list");
            t.btn.search = t.content.find(".amg-list-searchbar__icon");
            t.btn.export = t.content.find(".btn-export-maintenance");
        }
    }); */

    $("#tableSearch").on("keyup", function (e) {
        if (e.keyCode == 13 || this.value.length == 0) {
            var v = $(this).validate_str_param();
            if (v === false) {
                alert(config.translations.valid_search);
                return false;
            }
            // api.search(v).draw();

            t.loadUsers(true);
        }
    });



    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#tableSearch").validate_str_param();
        if (v === false) {
            alert(config.translations.valid_search);
            return false;
        }
        // t.dTbl.search(v).draw();

        t.loadUsers(true);
    };

    t.cache_filter_values = function () {
        var v = $.trim($('#tableSearch').validate_str_param());
        t.config.search = v;
        t.config.other_filters = {};
        var jobj = { "search": t.config.search, "other_filters": config.other_filters };
        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = config.url.export_excel + "?q=" + t.config.export_filters;
    }

    t.getCurrentPrivileges = function (userId) {
        return $('.location_previlege[data-belongs="location_' + userId + '"]:checked')
            .map(function () {
                return $(this).val().toString();
            })
            .get()
            .sort()
            .join(',');
    };

    t.toggleUpdateButton = function (userId) {
        var btn = $('#location_' + userId);
        var original = (btn.data('original') || '').toString().split(',').filter(Boolean).sort().join(',');
        var current = t.getCurrentPrivileges(userId);

        if (current === original) {
            btn.addClass('d-none');
        } else {
            btn.removeClass('d-none');
        }
    }

    $(document).on("change", ".checkall_previlege", function () {
        let container = $(this).closest('.tab-pane');
        let isChecked = $(this).is(":checked");
        container.find(".location_previlege").prop("checked", isChecked).trigger("change");
    });

    $(document).on("click", ".update_user_privilege", function (e) {
        var b = $(this).attr('data-belongs');
        var user_id = $(this).attr('data-user');

        var frmData = new FormData;
        frmData.append('_token', t.getToken());
        frmData.append('id', user_id);

        $.each(t.config.locations, function (i, m) {
            var el_id = '#location_' + user_id + '_' + m.id;
            var get_value = t.content.find(el_id).prop('checked') ? 1 : 0;
            frmData.append('permitted_locations[' + m.id + ']', get_value);
        });

        var http = $.ajax({
            url: t.config.url.update_privilege,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    // t.dTbl.ajax.reload();
                } else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            // vex.dialog.alert("Something went wrong. Please check given details are correct");
            var data = {
                'msg': config.translations.something_went_wrong_details,
            };
            sweetAlert('center', 'error', data);
        });
    });

    // t.dTbl.ajax.reload();
    t.btn.search.on("click", $.proxy(t.tableSearch));
    t.btn.reload.on("click", $.proxy(t.reload));
    t.btn.export.on("click", $.proxy(t.export));

    t.show_entries.select2({
        theme: 'custom',
        minimumResultsForSearch: Infinity,
        width: 'auto'
    });

    t.show_entries.on('change', function () {
        var value = parseInt($(this).val(), 10);
        limit = value;
        localStorage.setItem('user_locations_page_limit', limit);
        t.loadUsers(true);
        // t.dTbl.page.len(value).draw();
    });

    $(document).on('click', '.copy-email-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const email = $btn.data('email');

        if (email) {
            navigator.clipboard.writeText(email).then(() => {
                $btn.removeClass('bi-copy').addClass('bi-check-lg text-success');
                setTimeout(() => {
                    $btn.removeClass('bi-check-lg text-success').addClass('bi-copy');
                }, 2000);
            }).catch(err => {
                console.error('Copy failed', err);
            });
        }
    });

    const observer = new IntersectionObserver(function (entries) {
        let entry = entries[0];

        if (entry.isIntersecting) {
            t.loadUsers();
        }
    }, {
        root: document.querySelector(".show-permissions"),
        threshold: 0.1
    });

    observer.observe(document.querySelector("#loadMoreTrigger"));

    t.ensureScrollable = function () {
        let container = document.querySelector(".show-permissions");

        setTimeout(() => {
            if (hasMore && !loading && container.scrollHeight <= container.clientHeight) {
                t.loadUsers();
            }
        }, 100);
    }
};
