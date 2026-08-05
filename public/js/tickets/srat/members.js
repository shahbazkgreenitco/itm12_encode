var TicketPABMembers = function (config) {
    var t = this;
    var search = '';
    t.config = config;

    t.content = $('#srat-members-wrapper');
    
    t.mdl = t.content.find('#main-content');
    
    t.frm = t.mdl.find('#addForm');
    t.membersList = t.content.find(".members-list");
    t.addMember = t.content.find(".add-member");
    t.user = t.content.find('#user');
    t.hierarchyLevel = t.content.find("#hierarchy_level");
    t.frmEl = {};
    t.frmEl.locationApproverDiv = t.content.find('.locationApproverDiv');
    t.frmEl.departmentApproverDiv = t.content.find('.departmentApproverDiv');
    t.locationApproverTable = t.content.find('#locationApproverTable');
    t.departmentApproverTable = t.content.find('#departmentApproverTable');

    t.httpCall = false;

    t.tblHelpers = {
        actions: function () {
            return function (d) {
                return '<label id="location_' + d.id + '" class="control-label"><select name="approverName" value="' + d.id + '" id="' + d.id + '" class="a_location"></select><span class="text-info updateInfo"></span></label>';
            };
        }
    };

    t.getToken = function() {
        return $("head meta[name='csrf-token']").attr('content');
    };
    var select2Opts = { width: "120%" };
    t.dTbl = t.locationApproverTable.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: false,
        lengthChange: false,
        searching: false,
        aoColumnDefs: [{
            targets: 1,
            orderable: false,
            render: t.tblHelpers.actions()
        }],
        processing: true,
        serverSide: true,
        ajax: { 
            url: t.config.url.get_locations, 
            type: "get",
            data: function(d) {
                if(search) {
                    d.search = search;
                }
            }
        },
        columns: [
            {data: 'a.text'},
            {data: 'a.'}
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
			var searchBox  = '<div class="input-group table-search-btns">'
			+'<input type="text" class="form-control searchbox plain-search" placeholder="Press enter with search text" />'
			+'<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="left" data-original-title="Search"><i class="ps-icon plain-search-icon"></i></span>'
			+'<span class="input-group-addon btn-reload-list" data-toggle="tooltip" data-placement="left" data-original-title="Refresh List"><i class="ps-icon fa fa-refresh"></i></span>'
			+'</div>';
        },
        drawCallback:function(){
            t.frmEl.locationApproverSelect2 = t.frmEl.locationApproverDiv.find('.a_location');
            t.frmEl.locationApproverSelect2.select2($.extend({}, select2Opts, {
                ajax: {
                    width: '300px',
                    url: t.config.url.getUserByAjax,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,
                            company_id: config.company_id
                        };
                    },
                    delay: 300
                },
                allowClear:true,
                //minimumInputLength: 1,
                placeholder: 'select user',
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    return t.userDropdownFormat(data); 
                },
            }));
            $('[data-bs-toggle="tooltip"]').tooltip();
            // t.frmEl.locationApproverSelect2.append(new Option('Select User',null));
            if (typeof t.config.pab.member != "undefined" &&  t.config.pab.member.length > 0) {

                var array = t.config.pab.member == null ? [] : (t.config.pab.member);
                if(array.length != 0) {
                    $.each(array, function(i,j) {
                        var op = new Option(j.text, j.id, true, true);
                        t.frmEl.locationApproverDiv.find("#"+j.location_id).append(op);
                    });
                }
            }
        }
    });

    t.locationApproverTable.on("change", ".a_location", function (e) {
        var value = $(this);
        value.parent().find('.updateInfo').text('Updating...');
        var hierarchy_approval = parseInt(t.config.pab.hierarchy_approval);
        var data = {
            _token: t.config.token,
            pabId: t.config.pab.id,
            hierarchyApproval: hierarchy_approval,
            values: {
                locationId: value.attr('id'),
                value: value.val()
            }
        };

        $.ajax({
            url: t.config.url.actionUpdate,
            method: 'POST',
            data: data,
            success: function (res) {
                value.parent().find('.updateInfo').text(res.msg);
                setTimeout(function () {
                    value.parent().find('.updateInfo').text('');
                }, 3000);
            }
        });

    });

    t.content.find(".srat-group-list-page-length").on("change", function () {
        t.dTbl.page.len($(this).val()).draw();
    });
    
    $(document).on("keyup", ".user-list-search", function (e) {
        if (e.keyCode === 13 || this.value.length === 0) {
            search = $(this).val().trim();
            if (search === false) {
                alert("Please enter a valid value for search");
                return false;
            }
            console.log(v);
            t.dTbl.search(v).draw();
        }
    });

    $(".go-add").on("click", function(e) {  
        t.membersList.addClass("hide");
        t.addMember.removeClass("hide");
    });

    $("#show-members").on("click", function(e) {
        t.membersList.removeClass("hide");
        t.addMember.addClass("hide");
    });

    $(".go-list").on("click", function(e) {
        window.location = t.config.url.userList;
    });


    t.user.select2({
        width: "100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page || 1,
                    company_id: config.company_id
                };
            },
            processResults: function (data) {
                return {
                    results: data.results
                };
            }
        },
        templateResult: function (data) {
            if (!data) return $("<div>No data</div>");
            return t.userDropdownFormat(data); 
        }
    });

    t.userDropdownFormat = function (s) {
        if (s && typeof s.loading !== "undefined" && s.loading) {
            return $("<div>" + s.text + "</div>");
        }

        var email = s.email == null ? "" : s.email;
        var a = '';
        a += "<div class='row'>";
        a += "<div class='col-sm-10'>";
        var truncatedText = s.text.length > 30 ? s.text.substring(0, 30) + "..." : s.text;
        a += "<div class='so-t'><i class='fa fa-user' style='padding-right: 3px;'></i>" + truncatedText + " ";
        a += s.status == 1 ? "<span class='active-user'></span>" : "<span class='inactive-user'></span>";
        a += "</div>";
        if (s.email != null && s.email != "") {
            var truncatedEmail = s.email.length > 30 ? s.email.substring(0, 30) + "..." : s.email;
            a += "<div class='so-t'><i class=\"fa fa-envelope-o\" style='padding-right: 3px;'></i>" + truncatedEmail + "</div>";
        }
        if (s.employee_num != null && s.employee_num != "") {
            a += "<div class='so-t'><i class=\"fa fa-credit-card\" style='padding-right: 3px;'></i>" + s.employee_num + "</div>";
        }
        a += "</div>";
        a += "<div class='col-sm-2'>";
        a += "<div><img class='img-u' src='" + s.img_path + "'/></div>";
        a += "</div>";
        a += "</div>";
        return $("<div>" + a + "</div>");
    };

    t.hierarchyLevel.select2({width:"100%"});


    /*t.departmentTblHelpers = {
        actions: function () {
            return function (d) {
                return '<label id="department_' + d.id + '" class="control-label"><select name="approverName" value="' + d.id + '" id="' + d.id + '" class="a_department"></select><span class="text-info updateInfo"></span></label>';
            };
        }
    };

    t.departmentdTbl = t.departmentApproverTable.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            targets: 1,
            bSortable: true,
            render: t.departmentTblHelpers.actions()
        }],
        order: [[0, 'asc']],
        processing: true,
        serverSide: true,
        ajax: { 
            url: t.config.url.get_departments, 
            type: "post",
            data: function(d) {
                d._token = t.getToken();
            }
        },
        columns: [
            {data: 'a.text'},
            {data: 'a.'}
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
			var searchBox  = '<div class="input-group table-search-btns">'
			+'<input type="text" class="form-control searchbox plain-search" placeholder="Press enter with search text" />'
			+'<span class="input-group-addon btn-searchbox" data-toggle="tooltip" data-placement="left" data-original-title="Search"><i class="ps-icon plain-search-icon"></i></span>'
			+'<span class="input-group-addon btn-reload-list" data-toggle="tooltip" data-placement="left" data-original-title="Refresh List"><i class="ps-icon fa fa-refresh"></i></span>'
			+'</div>';

            
        },
        drawCallback:function(){
            t.frmEl.departmentApproverSelect2 = t.frmEl.departmentApproverDiv.find('.a_department');
            t.frmEl.departmentApproverSelect2.select2({width:"100%", placeholder:'Select User'});
            t.frmEl.departmentApproverSelect2.append(new Option('Select User',null));
            if (typeof t.config.users != "undefined" && Array.isArray(t.config.users) == true && t.config.users.length > 0) {
                var selectOptions = t.frmEl.departmentApproverSelect2;
                var array = t.config.ticketPabMembers == null ? [] : t.config.ticketPabMembers;
                $.each(selectOptions,function(i,j){
                    if(array.length == 0){
                        $.each(t.config.users, function(i, d) {
                            var op = new Option(d.text, d.id);
                            t.frmEl.departmentApproverDiv.find("#"+j.id).append(op);
                        });
                    }else{
                        var index = array.findIndex(function(item) {
                            return item.department_id == j.id;
                        });
                        $.each(t.config.users, function(i, d) {
                            var op = (index != -1 && array[index].user_id == d.id) ? new Option(d.text, d.id, true, true) : new Option(d.text, d.id);
                            t.frmEl.departmentApproverDiv.find("#"+j.id).append(op);
                        });
                    }
                });
            }
        }
    });

    t.departmentApproverTable.on("change", ".a_department", function(e) {
        var value = $(this);
        value.parent().find('.updateInfo').text('Updating...');
        var hierarchy_approval = parseInt(t.config.pab.hierarchy_approval);
        var data = {
            'pabId' : t.config.pab.id,
            'hierarchyApproval' : hierarchy_approval,
            'values': {
                'departmentId' : value.attr('id'),
                'value' : value.val()
            }
        };
        $.ajax({
            url:t.config.url.actionUpdate,
            method:'post',
            data:data,
            success:function(res){
                value.parent().find('.updateInfo').text(res.msg).delay(3000).text('');
            }
        })
    });*/


    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
    };
    
    var v = parseInt(t.config.pab.hierarchy_approval);
    if( isNaN(v) == false && v == 5) {
        t.frmEl.locationApproverDiv.removeClass('hide')
        t.frmEl.locationApproverDiv.show();
        t.dTbl.ajax.reload();
    }

    $(document).on("click", ".read-more", function (e) {
        e.preventDefault();

        var fullText = $(this).attr("data-full-text");

        $("#descriptionModal .modal-body").text(fullText);

        var modal = bootstrap.Modal.getOrCreateInstance(
            document.getElementById("descriptionModal")
        );

        modal.show();
    });

    $('#members-list').DataTable({
        paging: false,
        searching: false,
        info: false
    });
    /*if( isNaN(v) == false && v == 6) {
        t.frmEl.departmentApproverDiv.removeClass('hide')
        t.frmEl.departmentApproverDiv.show();
        t.departmentdTbl.ajax.reload();
    }*/
};
