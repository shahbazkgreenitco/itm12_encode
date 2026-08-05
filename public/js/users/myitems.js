function renderSpan(className = '') {
    return function (data) {
        return `<span class="${className}">${data ?? ''}</span>`;
    };
}

var LicensePhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#licenses-tab");
    t.table = t.tab.find("#tblLicense");

    t.tblHelpers = {
        actions: function (url) {
            return function (d) {
                var a = [];
                a.push("<a href=\"" + url.license_checkin + "/" + d.id + "\" class='btn dtActbtn dtActCheck' data-toggle='tooltip' data-original-title='Check In' data-id=\"" + d.id + "\" ><i class=\"fa fa-sign-in\"></i></a>");
                return a.join(' ');
            };
        },
        serial: function () {
            return function (d) {
                return (typeof d.serial_no === "string" && d.serial_no.length > 50) ? d.serial_no.substring(0, 50) + "..." : d.serial_no;
            }
        },
        name: function (url) {
            return function (d) {
                return "<span class='textCategory' data-placement='right' data-toggle='tooltip' data-original-title='" + config.translations.License_Tag + "'>" + d.lic_batch_no + "</span> - <span>" + d.name + "</span>";
            }
        }
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            dom: 'lrtip',
            lengthChange: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            aoColumnDefs: [
                {
                    targets: 0,
                    render: t.tblHelpers.name(t.config.url)
                },
                {
                    targets: 1,
                    render: t.tblHelpers.serial()
                }
            ],
            order: [
                [2, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_licenses,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.user_id = t.config.user_id;
                }
            },
            columns: [
                {
                    data: 'a',
                    render: t.tblHelpers.name(t.config.url)
                },
                {
                    data: 'a',
                    render: t.tblHelpers.serial()
                },
                {
                    data: 'a.checkout_info'
                }
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();

                var currentLength = api.page.len();
                $('#tblLicense_wrapper #licshowSelect').val(currentLength).trigger('change.select2');

                $('#tblLicense_wrapper').on('change', '#licshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });

                $("#tblLicense_filter input").off(".DT");
                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");
            }
        });

        $('#licshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var AccessoryPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#accessory-tab");
    t.table = t.tab.find("#tblAccessory");

    t.tblHelpers = {
        actions: function (url) {
            return function (d) {
                var a = [];
                a.push("<a href=\"" + url.accessory_info + "/" + d.id + "?checkin=" + d.au_id + "\" class='btn dtActbtn dtActCheck' data-toggle='tooltip' data-original-title='Check In' data-id=\"" + d.id + "\" ><i class=\"fa fa-sign-in\"></i></a>");
                return a.join(' ');
            };
        },
        name: function (url) {
            return function (d) {
                return "<span class='textCategory' data-placement='right' data-toggle='tooltip' data-original-title='" + config.translations.Accessory_Tag + "'>" + d.acc_batch_no + "</span> - <span>" + d.name + "</span>";
            }
        }
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            dom: 'lrtip',
            lengthChange: false,
            autoWidth: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            columnDefs: [{
                targets: 0,
                orderable: false,
                className: "d-none",
                render: t.tblHelpers.actions(t.config.url)
            },
            {
                targets: 1,
                render: t.tblHelpers.name()
            },
            {
                targets: 3,
                className: "d-none"
            }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_accessories,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.user_id = t.config.user_id;
                }
            },
            columns: [{
                data: 'a',
                defaultContent: ''
            }, // Actions
            {
                data: 'a',
                defaultContent: ''
            }, // Name
            {
                data: 'a.expected_checkin_format'
            }, // Expected Checkin
            {
                data: 'a.updated_at',
                defaultContent: ''
            } // Hidden Updated
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();
                // $("#tblAccessory_filter input").off(".DT");
                var currentLength = api.page.len();
                $('#tblAccessory_wrapper #astshowSelect').val(currentLength).trigger('change.select2');

                $('#tblAccessory_wrapper').on('change', '#astshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });

                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                //$("#tblAccessory_filter").empty().html(searchBox);
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");

            }
        });

        $('#astshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var ComponentPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#components-tab");
    t.table = t.tab.find("#tblComponents");

    t.tblHelpers = {
        actions: function (url) {
            return function (d) {
                var a = [];
                a.push("<a href=\"" + url.component_info + "/" + d.id + "?checkin=" + d.id + "\" class='btn dtActbtn dtActCheck' data-toggle='tooltip' data-original-title='Check In' data-id=\"" + d.id + "\" ><i class=\"fa fa-sign-in\"></i></a>");
                return a.join(' ');
            };
        },
        name: function (url) {
            return function (d) {
                return "<span class='textCategory' data-toggle='tooltip' data-original-title'" + config.translations.Component_Tag + "'>" + d.unique_tag + "</span> - <span>" + d.name + "</span>";
            }
        }
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            dom: 'lrtip',
            lengthChange: false,
            autoWidth: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            aoColumnDefs: [{
                targets: 0,
                bSortable: false,
                class: "hide"
                // render: t.tblHelpers.actions(t.config.url)
            }, {
                targets: 1,
                render: t.tblHelpers.name()
            }],
            order: [
                [2, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_components,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.user_id = t.config.user_id;
                }
            },
            columns: [{
                data: 'a'
            },
            {
                data: 'a'
            },
            {
                data: 'a.expected_checkin_format'
            },
            { data: 'a.updated_at' }
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();

                var currentLength = api.page.len();
                $('#tblComponents_wrapper #comshowSelect').val(currentLength).trigger('change.select2');

                $('#tblComponents_wrapper').on('change', '#comshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });
                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom" data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");
               
            }
        });

        $('#comshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var ConsumablePhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#consumable-tab");
    t.table = t.tab.find("#tblConsumable");

    t.tblHelpers = {
        actions: function (url) {
            return function (d) {
                var a = [];
                a.push("<a href=\"" + url.consumable_info + "/" + d.id + "?popup=revoke&open=" + d.cu_id + "\" class='btn dtActbtn dtActCheck' data-toggle='tooltip' data-original-title='Revoke' data-id=\"" + d.id + "\" ><i class=\"fa fa-sign-in\"></i></a>");
                return a.join(' ');
            };
        },
        name: function (url) {
            return function (d) {
                return "<span class='textCategory' data-toggle='tooltip' data-original-title='" + config.translations.batch_no + "'>" + d.con_batch_no + "</span> - <span>" + d.name + "</span>";
            }
        }
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            dom: 'lrtip',
            lengthChange: false,
            autoWidth: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            columnDefs: [{
                targets: 0,
                orderable: false,
                className: "d-none",
                render: t.tblHelpers.actions(t.config.url)
            },
            {
                targets: 1,
                render: t.tblHelpers.name()
            },
            {
                targets: 2,
                className: "d-none"
            }
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.assigned_consumables,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.user_id = t.config.user_id;
                }
            },
            columns: [{
                data: 'a',
                defaultContent: ''
            }, // Actions
            {
                data: 'a',
                defaultContent: ''
            }, // Name
            {
                data: 'a.updated_at',
                defaultContent: ''
            } // Hidden Updated
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();

                var currentLength = api.page.len();
                $('#tblConsumable_wrapper #conshowSelect').val(currentLength).trigger('change.select2');

                $('#tblConsumable_wrapper').on('change', '#conshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });

                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");
            }
        });

        $('#conshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var DevicePhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $("#mainContent");
    t.tab = $("#mainContent").find("#device-tab");
    t.table = t.tab.find("#tblDevice");

    t.tblHelpers = {
        actions: function (url) {
            return function (d) {
                var a = [];
                if (d.accepted == 'pending') {
                    a.push("<span class='accept_status_accepted ' style='cursor:pointer; margin:5px;' onclick=\"location.href='" + url.device_conformation + "/" + d.assets_log_id + "/" + 1 + "/" + d.assets_access_code + "/" + "via-portal" + "'\" data-placement='right' data-toggle='tooltip' data-original-title='Accept'> <i class=\"fa fa-check\"></i></span>");
                    a.push(
                        "<span class='accept_status_declined' style='cursor:pointer; margin:5px;' onclick=\"location.href='" + url.device_conformation + "/" + d.assets_log_id + "/" + 2 + "/" + d.assets_access_code + "/" + "via-portal" + "'\" data-placement='right' data-toggle='tooltip' data-original-title='Reject'> <i class=\"fa fa-times\"></i></span>");
                }
                if (d.accepted === 'declined' || d.accepted === 'accepted') {
                    a.push("<span class='status-badge status-badge-active b4-text accept_status_" + d.accepted + "'>" + d.accepted + "</span>");
                }
                return a.join(' ');
            };
        },
    };

    var columnDefs = [];
    if (config.appConfig.userAcceptance === true) {
        columnDefs.push({
            targets: 8,
            bSortable: false,
            render: t.tblHelpers.actions(t.config.url)
        });
    } else {
        columnDefs.push({
            targets: 8,
            bSortable: false,
            class: 'hide',
            render: t.tblHelpers.actions(t.config.url)
        });
    }
    t.dTbl = t.table.DataTable({
        dom: 'lrtip',
        lengthChange: false,
        autoWidth: false,
        scrollY: "300px",
        scrollX: true,
        scrollCollapse: true,
        aoColumnDefs: columnDefs,
        order: [
            [7, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.assigned_devices,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.user_id = t.config.user_id;
            }
        },
        columns: [{
            data: 'a.name',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.asset_tag',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.mdl_name',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.mnf_name',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.serial',
            render: renderSpan('role-badge role-badge-super-admin b4-text')
        },
        {
            data: 'a.project_name',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.last_checkout_format',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a.expected_checkin_date',
            render: renderSpan('b1-text text-muted')
        },
        {
            data: 'a',
            visible: t.config.appConfig.userAcceptance !== 'false'
        }
        ],

        fnInitComplete: function (oSettings, json) {
            var api = this.api();

            var currentLength = api.page.len();
            $('#tblDevice_wrapper #showSelect').val(currentLength).trigger('change.select2');

            $('#tblDevice_wrapper').on('change', '#showSelect', function () {
                var val = parseInt($(this).val(), 10);
                api.page.len(val).draw();
            });

            $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                if (e.keyCode == 13) {
                    var v = $(this).validate_str_param();
                    if (v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(v).draw();
                }
            });

            $('#tblDevice_filter').addClass('loaded');

            t.table.closest("div").addClass("table-responsive");
        }
    });

    $('#showSelect').on('change', function () {
        t.dTbl.page.len($(this).val()).draw();
    });

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var DocumentPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#documents-tab");
    t.table = t.tab.find("#tblDocument");

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                var a = [];
                // a.push("<button class='btn dtActbtn dtActDel' data-toggle='tooltip' data-original-title='Delete Document' data-id=\"" + d.id + "\" ><i class=\"fa fa-trash\"></i></button>");
                a.push("<a target='_blank' href=\"" + t.config.url.document_download + "/" + d.file_name + "\" class='btn dtActbtn' data-placement='right' data-toggle='tooltip' data-original-title='" + config.translations.Download_Document + "' data-id=\"" + d.id + "\" download><i class=\"fa fa-download\"></i></a>");
                return a.join(' ');
            };
        }
    };

    t.deleteDocument = function (e) {
        e.preventDefault();
        var docId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.document_delete;
        vex.dialog.confirm({
            message: 'You could not recover it after delete. Are you sure to delete the document permanently?',
            callback: function (value) {
                if (value == true) {
                    var http = $.post(t.httpPostPath, {
                        "asset_id": t.config.device_id,
                        "asset_type": "user",
                        "id": docId,
                        "_token": t.config.token
                    });
                    http.done(function (data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                vex.dialog.alert({
                                    unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '.</p></div>'
                                });
                                t.dTbl.ajax.reload();
                            } else {
                                vex.dialog.alert({
                                    unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>'
                                });
                            }
                        }
                    });
                    http.fail(function () {
                        alert("Something went wrong. Please try again later.");
                    });
                    http.always(function () {
                        t.httpCall = true;
                    });
                }
            }
        });
    };

    t.load = function () {
        t.dTbl = t.table.DataTable({
            destroy: true,
            dom: 'lrtip',
            lengthChange: false,
            autoWidth: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            aoColumnDefs: [{
                targets: 0,
                bSortable: false,
                render: t.tblHelpers.actions()
            }],
            order: [
                [2, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.documents,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.device_id = t.config.user_id;
                    d.asset_type = "user";
                }
            },
            columns: [{
                data: 'a'
            },
            {
                data: 'a.org_name'
            },
            {
                data: 'a.created_at_format'
            },
            {
                data: 'a.note'
            }
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();

                var currentLength = api.page.len();
                $('#tblDocument_wrapper #docshowSelect').val(currentLength).trigger('change.select2');

                $('#tblDocument_wrapper').on('change', '#docshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if(v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });

                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");
            }
        });

        $('#docshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", ".dtActDel", $.proxy(t.deleteDocument));
    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};

var HistoryPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent").find("#history-tab");
    t.table = t.tab.find("#tblHistory");

    t.load = function () {
        t.dTbl = t.table.DataTable({
            dom: 'lrtip',
            lengthChange: false,
            autoWidth: false,
            scrollY: "300px",
            scrollX: true,
            scrollCollapse: true,
            destroy: true,
            columnDefs: [{
                targets: 1,
                render: function (d) {

                    if (!d || !d.adminuserid) {
                        return '';
                    }

                    return '<a href="' + baseURL + '/user/info/' + d.adminuserid + '" target="_blank">' +
                        d.adm_full_name + '</a>';
                }
            }],
            order: [
                [0, 'desc']
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.user_history,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.user_id = t.config.user_id;
                }
            },
            columns: [{
                data: 'a.created_at_format'
            },
            {
                data: 'a'
            },
            {
                data: 'a.action_type'
            },
            {
                data: 'a.asset_type'
            },
            {
                data: 'a.note'
            }
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();

                var currentLength = api.page.len();
                $('#tblHistory_wrapper #histshowSelect').val(currentLength).trigger('change.select2');

                $('#tblHistory_wrapper').on('change', '#histshowSelect', function () {
                    var val = parseInt($(this).val(), 10);
                    api.page.len(val).draw();
                });

                $(".amg-list-searchbar__input").on("keyup.DT", function (e) {
                    if (e.keyCode == 13) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });
                var searchBox = '<div class="input-group table-search-btns">' +
                    '<input type="text" class="form-control searchbox plain-search" placeholder="' + config.translations.press_enter_with_Search + '" />' +
                    '<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="bottom"data-original-title="' + config.translations.Search + '"><i class="ps-icon plain-search-icon"></i></span>' +
                    '<button class="amg-btn amg-btn-outline  btn-reload-list">' + config.translations.Refresh_List + '</button>' +
                    '</div>';
                $('.dataTables_filter').addClass('loaded');
                t.table.closest("div").addClass("table-responsive");

            }
        });

        $('#histshowSelect').on('change', function () {
            t.dTbl.page.len($(this).val()).draw();
        });
    }

    t.reload = function () {
        t.dTbl.ajax.reload();
    };

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $(".plain-search").validate_str_param();
        if (v === false) {
            alert(config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.tab.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.tab.on("click", ".btn-reload-list", $.proxy(t.tableSearch));
};



var PrintEula = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#mainContent");
    t.printUser = function (e) {
        e.preventDefault();
        var userId = $(this).attr("data-id");
        window.location = t.config.url.print + "/" + userId;
    }
    t.tab.on("click", ".dtActPrint", $.proxy(t.printUser));
};

var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");

    t.devicePhase = new DevicePhase(config);
    t.licensePhase = new LicensePhase(config);
    $('.licenses-tab').click(function (e) {
        t.licensePhase.load();
    });
    t.accessoryPhase = new AccessoryPhase(config);
    $('.accessory-tab').click(function (e) {
        t.accessoryPhase.load();
    });
    t.consumablePhase = new ConsumablePhase(config);
    $('.consumable-tab').click(function (e) {
        t.consumablePhase.load();
    });
    t.componentPhase = new ComponentPhase(config);
    $('.components-tab').click(function (e) {
        t.componentPhase.load();
    });
    t.documentPhase = new DocumentPhase(config);
    $('.documents-tab').click(function (e) {
        t.documentPhase.load();
    });
    t.historyPhase = new HistoryPhase(config);
    $('.history-tab').click(function (e) {
        t.historyPhase.load();
    });

    t.PrintEula = new PrintEula(config);
    $('.dtActPrint').click(function (e) { });

    t.content.on("click", ".black-slide-links a", function (e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function () {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });

    function adjustTablesInMainContent() {
        var container = document.getElementById('mainContent');
        if (!container) return;


        var tables = $.fn.dataTable.tables({ api: true });

        tables.iterator('table', function () {
            if ($.contains(document.getElementById('mainContent'), this.table().node())) {
                this.columns.adjust();
            }
        });
    }

    const sidebar = document.getElementById('expanded-asset');

    if (sidebar) {
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === 'class') {

                    setTimeout(function () {
                        adjustTablesInMainContent();
                    }, 300);
                }
            });
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['class']
        });
    }

}