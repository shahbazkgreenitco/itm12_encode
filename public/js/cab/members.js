var CABMembers = function (config) {
    var t = this;
    t.config = config;

    t.content = $('section.content');
    
    t.mdl = t.content.find('#pabRow');
    
    t.frm = t.mdl.find('#addForm');
 
    t.frmEl = {};
    t.frmEl.locationApproverDiv = t.content.find('.locationApproverDiv');
    t.frmEl.departmentApproverDiv = t.content.find('.departmentApproverDiv');
    t.locationApproverTable = t.content.find('#locationApproverTable');
    t.departmentApproverTable = t.content.find('#departmentApproverTable');
    t.membersList = t.content.find(".members-list");
    t.addMember = t.content.find(".add-member");
    t.user = t.content.find('#user');

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

    t.dTbl = t.locationApproverTable.DataTable({
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: false,
        lengthChange: false,
        searching: false,
        aoColumnDefs: [{
            targets: 1,
            bSortable: true,
            render: t.tblHelpers.actions()
        }],
        order: [[0, 'asc']],
        ajax: { 
            url: t.config.url.get_locations, 
            type: "get",
            data: function(d) {
                d._token = t.getToken();
            }
        },
        columns: [
            {data: 'a.text'},
            {data: 'a.'}
        ],
        
        drawCallback:function() {
            t.frmEl.locationApproverSelect2 = t.frmEl.locationApproverDiv.find('.a_location');
            var select2Opts = { width: "100%" };
            t.frmEl.locationApproverSelect2.select2($.extend({}, select2Opts, {
                ajax: {
                    url: t.config.url.getUserByAjax,
                    dataType: "json",
                    data: function (p) {
                        return {
                            search: p.term,
                            page: p.page || 1,

                        };
                    },
                    delay: 300
                },
                allowClear:true,
                // minimumInputLength: 1,
                placeholder: 'select user',
                templateResult: function(data) {
                    if (!data) return $("<div>No data</div>");
                    return t.userDropdownFormat(data); 
                },
            }));
            // t.frmEl.locationApproverSelect2.append(new Option('Select User',null));
            if (typeof t.config.cab.member != "undefined" &&  t.config.cab.member.length > 0) {
                var array = t.config.cab.member == null ? [] : (t.config.cab.member);
                if(array.length != 0) {
                    $.each(array, function(i,j) {
                        var op = new Option(j.text, j.id, true, true);
                        t.frmEl.locationApproverDiv.find("#"+j.location_id).append(op);
                    });
                }
            }
        }
    });

    t.locationApproverTable.on("change", ".a_location", function(e) {
        var value = $(this);
        value.parent().find('.updateInfo').text('Updating...');
        var hierarchy_approval = parseInt(t.config.cab.hierarchy_approval);
        var data = {
            'cabId' : t.config.cab.id,
            'hierarchyApproval' : hierarchy_approval,
            'values': {
                'locationId' : value.attr('id'),
                'value' : value.val()
            }
        };
        $.ajax({
            url:t.config.url.actionUpdate,
            method:'post',
            data:data,
            success:function(res) {
                value.parent().find('.updateInfo').text(res.msg).delay(3000).text('');
            }
        })
    });

    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
    };

    var v = parseInt(t.config.cab.hierarchy_approval);
    
    if( isNaN(v) == false && v == 5) {
        t.frmEl.locationApproverDiv.removeClass('hide')
        t.frmEl.locationApproverDiv.show();
        t.dTbl.ajax.reload();
    }
    /*if( isNaN(v) == false && v == 6) {
        t.frmEl.departmentApproverDiv.removeClass('hide')
        t.frmEl.departmentApproverDiv.show();
        t.departmentdTbl.ajax.reload();
    }*/

    t.tableSearch = function (e) {
        e.preventDefault();
        var v = $("#locationApproverTable_wrapper .plain-search").validate_str_param();
        if (v === false) {
            alert(t.config.translations.please_enter_valid_search);
            return false;
        }
        t.dTbl.search(v).draw();
    };

    t.reload = function (e) {
        e.preventDefault();
        t.dTbl.ajax.reload();
    };

    t.deleteMember = function(e) {
        t.httpPostPath = t.config.url.remove + "/" + $(this).attr('data-id');
        sweetAlertConfirmation({
            message: t.config.translations.confirmation_message,
            onConfirm: function() {
                var http = $.get(t.httpPostPath);
                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center','success',data);
                            location.reload();
                        }
                        else {
                            sweetAlert('center','error',data);
                        }
                    }
                });
                http.fail(function () {
                    alert(t.config.translations.something_wrong);
                });
                http.always(function () {
                    t.httpCall = true;
                });
            }
        });
    }

    t.content.on('click','.delete-member', t.deleteMember);
    $(".go-add").on("click", function(e) {
        t.membersList.addClass("hide");
        t.addMember.removeClass("hide");
    });

 
    $(".go-list").on("click", function(e) {
         window.location = t.config.url.cabList;
    });

    t.user.select2({
        width:"100%",
        ajax: {
            url: t.config.url.getUserByAjax,
            dataType: "json"
        },
        templateResult: function(s) {
            if (!s) return $("<div>No data</div>");
            return t.userDropdownFormat(s); 
        }
    });

    t.content.on("click",'.btn-reload-list', $.proxy(t.reload));
    t.content.on("click",'.btn-searchbox', $.proxy(t.tableSearch));

};
