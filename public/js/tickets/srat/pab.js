$(document).ready(function () {
    var PabAdd = function (config) {
        var t = this;
        t.config = config;
        let companyId = t.config.company_user_detail ? t.config.company_user_detail.dashboard_company_id : null;
        t.content = $("#srat-group-list-wrapper");
        t.table = t.content.find("#mytable");
        t.filterContainer = $("#filtercontainer");
        t.page =  t.content.find("#page_boxed");
        t.btn = {};
        t.httpCall = true;
        t.httpApi = "";
        t.resetFrm = {};
        t.httpCall = true;
        t.descriptonMdl = t.page.find("#descriptionModal");
        t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");
        
        t.filterMdl = t.content.find("#sratFilterModal");


        t.filterMdl.modal({
            backdrop: "static",
            keyboard: false,
            show: false,
        });

        t.filters = {
            wrapper: $("#sratFilterModal"),
        };

        t.filters.clear = t.filters.wrapper.find("#clear");
        t.filters.company = $("#filter_by_company");
        t.filters.member = $("#filter_by_member");
        t.filters.filter_by_approver = $("#filter_by_approver");
        var select2Opts = {
            width: "100%"
        };

        t.openFilter = function () {
            t.filterMdl.modal("show");
        };

        t.filters.member.select2(
            $.extend({}, select2Opts, {
                placeholder: config.translations.filter_by_company || "Filter by Member",
                allowClear: true,
                dropdownParent: t.filterMdl,
                ajax: {
                    url: t.config.url.getActiveUsersByQuery,
                    dataType: "json",
                    delay: 300,
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                },
            }),
        );

        t.filters.company.select2(
            $.extend({}, select2Opts, {
                placeholder: config.translations.filter_by_company || "Filter by Company",
                allowClear: true,
                dropdownParent: t.filterMdl,
                ajax: {
                    url: t.config.url.getCompanyByUserAccess,
                    dataType: "json",
                    delay: 300,
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                        };
                    },
                },
            }),
        );
        $('#filter_by_approver').select2({width: '100%', dropdownParent: $('#sratFilterModal'),placeholder: 'Select Approval Mode',allowClear: true});
        t.cache_filter_values = function () {
            t.config.other_filters = {};
            t.config.search = $(".user-list-search").val();
            var company = $("#filter_by_company").val();
            var approvel = $("#filter_by_approver").val();
            var srat_member = $("#filter_by_member").val();
            if (company && company.length && company[0] !== "null" && company[0] !== null) {
                t.config.other_filters.filter_by_company = company;
            }
            if (approvel && approvel.length && approvel[0] !== "null" && approvel[0] !== null) {
                t.config.other_filters.approvel = approvel;
            }
            if (srat_member && srat_member.length && srat_member[0] !== "null" && srat_member[0] !== null) {
                t.config.other_filters.srat_member = srat_member;
            }
            t.config.export_filters = btoa(JSON.stringify({
                search: t.config.search,
                other_filters: t.config.other_filters
            }));
            t.updateFilterCount();
        };

        t.updateFilterCount = function () {
            var count = 0;
            var f = t.config.other_filters || {};
            if (f.filter_by_company && f.filter_by_company.length) {
                count++;
            }
            if ($("#filter_by_approver").val() && $("#filter_by_approver").val().length) {
                count++;
            }
            if(f.srat_member && f.srat_member.length){
                count++;
            }
            var $badge = t.content.find(".filter-count-badge");
            if (count > 0) {
                $badge.text(count).removeClass("d-none");
            } else {
                $badge.text("0").addClass("d-none");
            }

        };

        t.filterMdl.on("click", "#advanced_filter", function (e) {
            e.preventDefault();
            t.cache_filter_values();
            t.dTbl.ajax.reload(null, false);
            t.filterMdl.modal("hide");
        });

        t.filters.clear.on("click", function (e) {
            e.preventDefault();
            $("#filter_by_company").val(null).trigger('change.select2');
            $("#filter_by_approver").val(null).trigger('change.select2');
            $("#filter_by_member").val(null).trigger('change.select2'); // missing — add this line
            t.config.other_filters = {};
            t.updateFilterCount();
            t.filterMdl.modal("hide");
            t.dTbl.ajax.reload(null, false);
        });

        t.createGroup = function (e) {
            e.preventDefault();
            if (t.frmValidator.form() == false) return false;
            t.mdl.btnSubmit.prop("disabled", true);
            t.httpPostPath = t.mdl.frm.attr("action");
            var formData = new FormData(t.mdl.frm[0]);
            $.ajax({
                url: t.httpPostPath,
                type: "POST",
                processData: false,
                contentType: false,
                data: formData,
            }).done(function (data) {
                if (typeof data == "object") {
                    if (data.status == "success") {
                        t.bsModal.hide();
                        sweetAlert("center", "success", data);
                        setTimeout(function () {
                            t.dTbl.ajax.reload();
                        }, 800);
                    } else {
                        sweetAlert("center", "error", data);
                    }
                }
            }).fail(function () {
                sweetAlert("center", "error", {
                    msg: config.translations.something_went_wrong,
                });
            }).always(function () {
                t.mdl.btnSubmit.prop("disabled", false);
            });
        };

        t.buildActions = function(d) {
            var actions = [];
            actions.push(`
                <a href="${t.config.url.edit}/${d.id}"
                class="amg-action-btn btn-edit-group"
                data-bs-toggle="tooltip" title="${t.config.translations.Edit_SRAT}">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"
                        fill="currentColor"/>
                    </svg>
                </a>
            `);
            actions.push(`
                <button class="amg-action-btn btn-delete-member dtActDel"
                    data-id="${d.id}" data-bs-toggle="tooltip" title="${t.config.translations.Delete}">
                    <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z" fill="currentColor"/>
                    </svg>
                </button>
            `);
            if(![4, 8, 9, 10, 11, 12, 13, 14].includes(d.hierarchy_approval_id)) {
                actions.push(`
                    <a href="${config.url.manage_users}/${d.id}/${d.comp_id}" class="amg-action-btn success btn-add-member" data-bs-toggle="tooltip" title="${t.config.translations.ADD_SRAT_MEMBER}">
                        <i class="bi bi-people"></i>
                    </a>
                `);
            }
            actions.push(`
                <a href="${config.url.history}/${d.id}"
                class="amg-action-btn btn-edit-group"
                data-bs-toggle="tooltip" title="${t.config.translations.HISTORY_SRAT}">
                <i class="bi bi-clock-history"></i>
                </a>
            `);
            return `<div class="amg-datatable-actions d-flex justify-content-center gap-2">${actions.join("")}</div>`;
        };

        t.dTbl = t.table.DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
            scrollX: true,
            lengthChange: false,
            searching: false,
            aoColumnDefs: [
                {
                    bSortable: false,
                    aTargets: [3],
                    sDecimal: true,
                },
                {
                targets: 7,
                orderable: false,
                searchable: false,
                width: "120px",
                render: function(data, type, row) {
                    return t.buildActions(row.a || {});
                }
            },
            {
                targets: 2,
                render: function (data, type, row) {
                    if (row.a.hierarchy_approval === "Minimum Approval") {
                        return row.a.hierarchy_approval + '<br><div><span class="ie">Required Approvals: ' + row.a.required_minimum_approvals + '</span></div>';
                    }else if(row.a.hierarchy_approval === 'Next Level Manager Approval'){
                        return row.a.hierarchy_approval + '<br><div><span class="ie">Required Manager level: ' + row.a.required_minimum_approvals + '</span></div>';
                    }
                    else {
                        return data;
                    }
                }
            },
            {
                targets: [4,6],
                class: "text-center",
            },
            ],
            order: [[6, "desc"]],
            processing: true,
            serverSide: true,
            fixedColumns: false,
            ajax: {
                url: t.config.url.getlist,
                type: "post",
                data: function (d) {
                    d._token = t.config.token;
                    d.search = $(".user-list-search").val();
                    d.filters = t.config.other_filters;
                    d.company_id = companyId;
                },
            },
            dom: "<'dt-top'<'left'f><'right'l>>" +
            "tr" +
            "<'dt-bottom'<'left'i><'right'p>>",
            columns: [
                {data: "a.name",
                    render: function(data,type,row){
                        if(!data){
                            return '';
                        }
                        var data=String(data);
                        if(data.length > 20){
                            var truncated = truncateHtml(data, 20);
                            return '<div data-bs-toggle="tooltip" title="' + escapeHtml(data) + '">' +
                            escapeHtml(truncated) + '...' +
                            '</div>';
                        } else {
                            return  data ;
                        }
                    }
                },
                {data: "a.company_name"},
                {data: "a.hierarchy_approval"},
                {
                    data: 'a.description',
                    render: function(data, type, row) {
                        if (!data) {
                            return '';
                        }
                        data = String(data);
                        if (data.length > 30) {
                            var truncated = truncateHtml(data, 30);
                            return truncated +
                                ' <a href="javascript:void(0)" class="read-more" data-full-text="' +
                                escapeHtml(data) +
                                '">...Read More</a>';
                        }
                        return data;
                    }
                },
                {data: "a.member_count",
                    render: function(d,type,row){
                        if(d != null){
                            return "<a target='_blank' href='" + config.url.manage_users + "/" + row.a.id + "/" + row.a.comp_id + "' class='btn' id='" + row.a.id + "' data-id='" + row.a.id + "'>" + d + "</a>";                        }else{
                            return '';
                        }
                    }

                },
                {
                    data: "a.created_at",
                    width: "140px",
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: "a.updated_at",
                    width: "140px",
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {data: "a"},
            ],
            fnInitComplete: function (oSettings, json) {
                var api = this.api();
                $("#mytable_wrapper").removeClass("form-inline");
                // t.table.closest("div").addClass("table-responsive");;
                $("#mytable_filter").remove();
                $("#mytable_length").find("select").select2();
                $("#mytable_filter input").off(".DT");
                $("#mytable_wrapper .user-list-search").on("keyup", function(e) {
                    if (e.keyCode == 13 || this.value.length == 0 ) {
                        var v = $(this).validate_str_param();
                        if (v === false) {
                            alert("Please enter a valid value for search");
                            return false;
                        }
                        api.search(v).draw();
                    }
                });
                $(document).on("click", ".read-more", function(e) {
                    e.preventDefault();
                    var fullText = $(this).data("full-text");
                    $("#descriptionModal .modal-body").html(fullText);
                    var modal = new bootstrap.Modal(
                        document.getElementById("descriptionModal")
                    );
                    modal.show();
                });
                t.scheduleTableLayoutSync();
            },
            drawCallback: function() {
                var api = this.api();

                setTimeout(function() {
                    api.columns.adjust();

                    if (api.fixedColumns) {
                        var fc = api.fixedColumns();

                        if (fc && fc.relayout) {
                            fc.relayout();
                        }
                    }
                }, 100);
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });

        t.scheduleTableLayoutSync = function() {
            setTimeout(function() {
                if (!t.dTbl) return;
                t.dTbl.columns.adjust().draw(false);
                if (t.dTbl.fixedColumns) {
                    var fc = t.dTbl.fixedColumns();
                    if (fc && fc.relayout) {
                        fc.relayout();
                    }
                }
            }, 100);
        };

        function truncateHtml(html, maxLength) {
            var div = document.createElement("div");
            div.innerHTML = html;
            var text = div.textContent || div.innerText || "";
            if (text.length <= maxLength) {
                return html;
            }
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


        t.reload = function (e) {
            e.preventDefault();
            t.dTbl.ajax.reload();
        };

        t.export = function(e){
            e.preventDefault();
            t.cache_filter_values();
            window.location = t.config.url.export + "?q=" + t.config.export_filters +'&company_id=' +companyId;
        }

        $(document).on("keyup", ".user-list-search", function (e) {
            if (e.keyCode === 13 || this.value.length === 0) {
                var v = $(this).val().trim();
                if (v === false) {
                    alert("Please enter a valid value for search");
                    return false;
                }
                t.dTbl.search(v).draw();
                t.cache_filter_values();
            }
        });

    
        // t.F = function () {
        //     var v = $.trim($(".user-list-search").val());
        //     t.config.search = v;
        //     console.log(t.config.search);
        //     t.config.other_filters = {};
        //     if(t.advFilters.name.val() != '')
        //     t.config.other_filters.name = t.advFilters.name.val();
        //     if(t.advFilters.description.val() != '' && t.advFilters.description.val() != null)
        //     t.config.other_filters.description = t.advFilters.description.val();
        //     if(t.advFilters.hierarchyApproval.val() != '')
        //     t.config.other_filters.hierarchyApproval = t.advFilters.hierarchyApproval.val();
        //     var jobj = {
        //         search: t.config.search,
        //         other_filters: t.config.other_filters,
        //     };
        //     t.config.export_filters = btoa(JSON.stringify(jobj));
        //     filterCount(t.config.other_filters, '', false);
        // };

        t.search = (e) => {
            var target = e.target || e.currentTarget;
            if (e.keyCode == 13 || $(this).is("span")) {
                var v = t.searchbox.find("input").validate_str_param();
                if (v === false) {
                    t.config.search = "";
                    alert("Please enter a valid value for search");
                    return false;
                }
                t.config.search = v;
                t.dTbl.ajax.reload();
            } else if (target.tagName == "BUTTON") {
                t.cache_filter_values();
                t.dTbl.ajax.reload();
            }
        };

        t.filters = {
            wrapper: t.content.find("#advance-filters"),
        };
        (t.filters.btnfilterclr = t.content.find(".advance-filters #btnClrFilter")),
        (t.btnClrFilter = function () {
            t.advFilters.name.val("").trigger("change");
            t.advFilters.description.val("").trigger("change");
            t.advFilters.hierarchyApproval.val("").trigger("change");
            t.config.other_filters = {};
            resetFilterCount();
            t.dTbl.ajax.reload();
        });

        t.deleteStatus = function(e) {
            e.preventDefault();
            var Id = $(this).attr("data-id");
            t.httpPostPath = t.config.url.delete + "/" + Id;
            sweetAlertConfirmation({
                message: config.translations.are_you_delete_pab,
                onConfirm: function() {
                    var http = $.get(t.httpPostPath);
                    http.done(function(data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                toastr.success(data.msg);
                                t.dTbl.ajax.reload();
                            } else {
                                toastr.error(data.msg);
                            }
                        }
                    });
                    http.fail(function() {
                        toastr.error(config.translations.something_went_wrong);
                    });
                    http.always(function() {
                        t.httpCall = true;
                    });
                }
            });
        };

        t.content.find(".srat-group-list-page-length").on("change", function () {
            t.dTbl.page.len($(this).val()).draw();
        });

        t.content.on("click",'.btn-reload-list', $.proxy(t.reload));
        t.filterContainer.on("keyup", "#searchboxtext", $.proxy(t.onSearchChange));
        t.filters.btnfilterclr.on("click", t.btnClrFilter);
        t.table.on("click", ".dtActDel", $.proxy(t.deleteStatus));
        t.content.on("click",'.btn-export', $.proxy(t.export));
        t.content.on("click", ".btn-open-filter", $.proxy(t.openFilter));
        // t.dTbl.ajax.reload();
    };
    new PabAdd(config);
});

