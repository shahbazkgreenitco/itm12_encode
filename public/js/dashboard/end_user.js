var MyApp = function(config) {
    var t = this;
    t.section = $("section.content");
    t.config = {};
    var companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;

    t.techDash = t.section.find("#techDash");
    t.userDash = t.section.find("#userDash");
    t.assetDash = t.section.find("#assetDash");
    t.clearFilter = t.section.find('.clearFilter');
    t.tncContent = $('#tncContent');
    t.page = $("#page_boxed");
    t.home = t.userDash.find("#home");
    t.pagebtns = t.section.find("#pagebtns");
	t.pageBtmSummary = t.section.find("#page-btm-summary");
    t.total = 0;
	t.perPage = 4;
    t.btn = {};

    t.btnSearch =  t.section.find(".btn-searchbox");
    t.btnSearchParent = t.section.find(".amg-list-searchbar")
    

    t.clearFilter.on('click',function(){
        t.filters.wrapper.find('input').val('');
        t.load();
    });

    t.filters = {
        wrapper: t.section.find("#advance-filters")
    };
    t.filters.from_date = t.section.find("#filter_by_from_date"),
        t.filters.to_date = t.section.find("#filter_by_to_date"),
        t.filters.filter_by_department = t.section.find("#filter_by_department"),

        t.filters.filter_by_department.select2({
            width:"100%",
            placeholder : 'No Filter',
        })

    // t.filters.to_date.datetimepicker({  });
    // t.search = function(e) {
    //     var target = e.target || e.currentTarget;
    //     if(target.tagName == "BUTTON") {
    //         t.cache_filter_values();
    //         t.load();

    //     }
    // };
    // t.filters.from_date.datetimepicker({  }).on('changeDate', function(e) {
    //     t.filters.to_date.datetimepicker('setStartDate', e.date);
    //     t.filters.to_date.datetimepicker('setDate', e.date);
    // });
    t.cache_filter_values = function() {
        t.config.other_filters = {};
        // t.config.other_filters.from_date = t.filters.from_date.val();
        // t.config.other_filters.to_date = t.filters.to_date.val();
        // t.config.other_filters.filter_by_department = t.filters.filter_by_department.val();


        var jobj = {"other_filters":t.config.other_filters};
        t.config.export_filters = JSON.stringify(jobj);
    };

    t.statusBoardList = function(data, target) {
        var me = this;
        me.target = target;
        me.el = {};
        me.el.overall = me.target.find("#overall");
        me.el.data = me.target.find(".data");

        if (typeof data == "undefined" || data == null || data.tot_items < 1) {
            me.el.overall.text("0%");
            me.el.data.html("<div class='nda'>No Data Available</div>");
            return;
        }

        if (typeof lt != "undefined" && typeof rt != "undefined") {
            me.el.data.append('<div class="clearfix ls-title"><p class="pull-left mar-no">' + lt + '</p><p class="pull-right mar-no">' + rt + '</p></div>');
        }

        var overall = parseInt((data.tot_active / data.tot_items) * 100);
        me.el.overall.text(overall + "%");
        me.el.data.append('<div class="progress progress-sm">' + '<div class="progress-bar" role="progressbar" style="width: ' + overall + '%;"></div></div>');
        var labels = { "tot_active": "Total Active Services", "tot_per_iss": "Total Performance Issues", "tot_par_out": "Total Partial Outages", "tot_major": "Total Major Outages" };
        $.each(data, function(i, j) {
            if (typeof labels[i] == "undefined") { return; }
            me.el.data.append('<div class="col-xs-6"><div class="text-center eu-bb"><span class="text-thin text-yellow eu-bb-count">' + j + '</span><p class="text-semibold text-red eu-bb-text">' + labels[i] + '</p></div></div>');
        });
    };

    t.ticketBoardList = function(data, target) {
        var me = this;
        me.target = target;
        me.el = {};
        me.el.overall = me.target.find("#overall");
        me.el.data = me.target.find(".data");

        if (typeof data == "undefined" || data == null || data.tot_items < 1) {
            me.el.overall.text("0%");
            me.el.data.html("<div class='nda'>No Data Available</div>");
            return;
        }

        if (typeof lt != "undefined" && typeof rt != "undefined") {
            me.el.data.append('<div class="clearfix ls-title"><p class="pull-left mar-no">' + lt + '</p><p class="pull-right mar-no">' + rt + '</p></div>');
        }

        var overall = parseInt((data.tot_resolved / data.tot) * 100) || 0;
        me.el.overall.text(overall + "%");
        me.el.data.append('<div class="progress progress-sm">' + '<div class="progress-bar" role="progressbar" style="width: ' + overall + '%;"></div></div>');
        var labels = {"tot_resolved": "Total Resolved", "tot_not_resolved": "Total Not Resolved", "tot_critical": "Total Critical", "tot_high": "Total High", "tot_medium": "Total Medium", "tot_low": "Total Low"};
        $.each(data, function(i, j) {
            if (typeof labels[i] == "undefined") { return; }

            if(i == "tot_not_resolved" || i == "tot_resolved") {
                me.el.data.append('<div class="col-xs-6"><div class="text-center eu-bb"><span class="text-thin text-yellow eu-bb-count">' + parseInt(j || 0)  + '</span><p class="text-semibold text-red eu-bb-text">' + labels[i] + '</p></div></div>');
            }
            else {
                me.el.data.append('<div class="col-xs-3"><div class="mar-no-btm"><div class="text-center eu-bb"><span class="text-thin text-yellow eu-bb-count">' + parseInt(j || 0)+ '</span><p class="text-semibold text-red eu-bb-text">' + labels[i]  + '</p></div></div></div>');
            }
        });
    };

    t.load = function(){
        $('#pageLoader').show();
        var http = $.ajax({
            url: config.getDashboardData,
            type: "POST",
            data: {
                filters: t.config.other_filters,
                companyId: companyId,
                _token: config.token
            }
        });
        http.done(function(data) {
            if (typeof data == "object") {
                t.loadTechDash(data);
                t.loadUserDash(data);
                t.loaderviceRequestData(data);
                t.loadServiceRequest(data);
            }
        });
        http.fail(function() {
            alert("Something went wrong. Please check given details are correct");
        });
        http.always(function(){
            $('#pageLoader').hide(); // ajax complete hone par hide
        });
    }

    t.loadstatusBoardData = function(data){
        if(data.data.statusBoardInfo[0] != undefined) {
            t.section.find('#sb_total').html(data.data.statusBoardInfo[0].tot_items == null ? 0 :  data.data.statusBoardInfo[0].tot_items);
            t.section.find('#sb_active').html(data.data.statusBoardInfo[0].tot_active == null ? 0 :  data.data.statusBoardInfo[0].tot_active);
            t.section.find('#sb_major').html(data.data.statusBoardInfo[0].tot_major == null ? 0 :  data.data.statusBoardInfo[0].tot_major);
            t.section.find('#sb_partial').html(data.data.statusBoardInfo[0].tot_par_out == null ? 0 :  data.data.statusBoardInfo[0].tot_par_out);
            t.section.find('#sb_persistant').html(data.data.statusBoardInfo[0].tot_per_iss == null ? 0 :  data.data.statusBoardInfo[0].tot_per_iss);
        }
    }

    t.loaderviceRequestData = function(data) {
        if(data.data.dataserviceRequestcounts[0] != undefined) {
            t.section.find('#servicereq_total').html(data.data.dataserviceRequestcounts[0].total == null ? 0 :  data.data.dataserviceRequestcounts[0].total);
            t.toggleCardVisibility('#servicereq_total', data.data.dataserviceRequestcounts[0].total);
            t.section.find('#servicereq_approved').html(data.data.dataserviceRequestcounts[0].approved == null ? 0 :  data.data.dataserviceRequestcounts[0].approved);
            t.toggleCardVisibility('#servicereq_approved', data.data.dataserviceRequestcounts[0].approved);
            t.section.find('#servicereq_waiting').html(data.data.dataserviceRequestcounts[0].waiting == null ? 0 : data.data.dataserviceRequestcounts[0].waiting);
            t.toggleCardVisibility('#servicereq_waiting', data.data.dataserviceRequestcounts[0].waiting);
            t.section.find('#awaiting_my_approval').html(data.data.tot_awaiting_my_approval == null ? 0 : data.data.tot_awaiting_my_approval);
            t.toggleCardVisibility('#awaiting_my_approval', data.data.tot_awaiting_my_approval);
        }
    }

    t.loadTechDash = function(data) {
        if(data.data.ticketsOverallForMyDept[0] != undefined){
            t.techDash.find('#department_total_tickets').html(data.data.ticketsOverallForMyDept[0].tot == null ? 0 :  data.data.ticketsOverallForMyDept[0].tot);
            t.techDash.find('#department_total_open').html(data.data.ticketsOverallForMyDept[0].open == null ? 0 : data.data.ticketsOverallForMyDept[0].open);
            t.techDash.find('#department_total_in_progress').html(data.data.ticketsOverallForMyDept[0].in_progress == null ? 0 : data.data.ticketsOverallForMyDept[0].in_progress);
            t.techDash.find('#department_total_resolved').html(data.data.ticketsOverallForMyDept[0].resolved == null ? 0 : data.data.ticketsOverallForMyDept[0].resolved);
            t.techDash.find('#department_waiting_for_user').html(data.data.ticketsOverallForMyDept[0].waiting_for_the_user == null ? 0 : data.data.ticketsOverallForMyDept[0].waiting_for_the_user);
            t.techDash.find('#department_waiting_for_vendor').html(data.data.ticketsOverallForMyDept[0].waiting_for_the_vendor == null ? 0 : data.data.ticketsOverallForMyDept[0].waiting_for_the_vendor);
            t.techDash.find('#department_total_closed').html(data.data.ticketsOverallForMyDept[0].closed == null ? 0 : data.data.ticketsOverallForMyDept[0].closed);
            t.techDash.find('#department_total_spam').html(data.data.ticketsOverallForMyDept[0].spam == null ? 0 : data.data.ticketsOverallForMyDept[0].spam);
            t.techDash.find('#department_reopened').html(data.data.ticketsOverallForMyDept[0].reopened == null ? 0 : data.data.ticketsOverallForMyDept[0].reopened);
            if(data.data.ticketsOverallForMyDept[0].waiting_for_the_user != null && data.data.ticketsOverallForMyDept[0].waiting_for_the_user > 0) {
                $("#techDash .fa-universal-access.social").parent().addClass("anim");
            }

            t.toggleCardVisibility('#department_total_tickets', data.data.ticketsOverallForMyDept[0].tot);
            t.toggleCardVisibility('#department_total_open', data.data.ticketsOverallForMyDept[0].open);
            t.toggleCardVisibility('#department_total_in_progress',data.data.ticketsOverallForMyDept[0].in_progress);
            t.toggleCardVisibility('#department_total_resolved', data.data.ticketsOverallForMyDept[0].resolved);
            t.toggleCardVisibility('#department_waiting_for_user', data.data.ticketsOverallForMyDept[0].waiting_for_the_user);
            t.toggleCardVisibility('#department_waiting_for_vendor', data.data.ticketsOverallForMyDept[0].waiting_for_the_vendor);
            t.toggleCardVisibility('#department_total_closed', data.data.ticketsOverallForMyDept[0].closed);
            t.toggleCardVisibility('#department_total_spam', data.data.ticketsOverallForMyDept[0].spam);
            t.toggleCardVisibility('#department_reopened', data.data.ticketsOverallForMyDept[0].reopened);
            let totals = {
                tot: parseInt(data.data.ticketsOverallForMyDept[0].tot) || 0,
                tot_open: parseInt(data.data.ticketsOverallForMyDept[0].open) || 0,
                tot_ip: parseInt(data.data.ticketsOverallForMyDept[0].in_progress) || 0,
                tot_resolved: parseInt(data.data.ticketsOverallForMyDept[0].resolved) || 0,
                tot_wfu: parseInt(data.data.ticketsOverallForMyDept[0].waiting_for_the_user) || 0,
                tot_wfv: parseInt(data.data.ticket_info[0].tot_wfv) || 0,
                tot_closed: parseInt(data.data.ticketsOverallForMyDept[0].waiting_for_the_vendor) || 0,
                tot_spam: parseInt(data.data.ticketsOverallForMyDept[0].spam) || 0,
                tot_reopened: parseInt(data.data.ticketsOverallForMyDept[0].reopened) || 0,
                tot_clsoed: parseInt(data.data.ticketsOverallForMyDept[0].closed) || 0,
                tot_approval: parseInt(data.data.tot_approval) || 0,
                tot_myRequest: parseInt(data.data.tot_myRequest) || 0,
                tot_awaiting_my_approval: parseInt(data.data.tot_awaiting_my_approval) || 0,
            };
            let allZero = Object.values(totals).every(val => val === 0)
            if(allZero) {
                $('#noRecordsTicketMsg').removeClass('hide');
            } else {
                $('#noRecordsTicketMsg').addClass('hide');
            }
        }

        if(data.data.assignedTicket_info[0] != undefined) {
            t.techDash.find('#assigned_total_tickets').html(data.data.assignedTicket_info[0].tot == null ? 0 :  data.data.assignedTicket_info[0].tot);
            t.techDash.find('#assigned_total_open').html(data.data.assignedTicket_info[0].tot_open == null ? 0 : data.data.assignedTicket_info[0].tot_open);
            t.techDash.find('#assigned_total_in_progress').html(data.data.assignedTicket_info[0].tot_ip == null ? 0 : data.data.assignedTicket_info[0].tot_ip);
            t.techDash.find('#assigned_total_resolved').html(data.data.assignedTicket_info[0].tot_resolved == null ? 0 : data.data.assignedTicket_info[0].tot_resolved);
            t.techDash.find('#assigned_waiting_for_user').html(data.data.assignedTicket_info[0].tot_wfu == null ? 0 : data.data.assignedTicket_info[0].tot_wfu);
            t.techDash.find('#assigned_waiting_for_vendor').html(data.data.assignedTicket_info[0].tot_wfv == null ? 0 : data.data.assignedTicket_info[0].tot_wfv);
            t.techDash.find('#assigned_total_closed').html(data.data.assignedTicket_info[0].tot_closed == null ? 0 : data.data.assignedTicket_info[0].tot_closed);
            t.techDash.find('#assigned_total_spam').html(data.data.assignedTicket_info[0].tot_spam == null ? 0 : data.data.assignedTicket_info[0].tot_spam);
            t.techDash.find('#assigned_reopened').html(data.data.assignedTicket_info[0].tot_reopened == null ? 0 : data.data.assignedTicket_info[0].tot_reopened);

            t.toggleCardVisibility('#assigned_total_tickets', data.data.assignedTicket_info[0].tot);
            t.toggleCardVisibility('#assigned_total_open', data.data.assignedTicket_info[0].tot_open);
            t.toggleCardVisibility('#assigned_total_in_progress', data.data.assignedTicket_info[0].tot_ip);
            t.toggleCardVisibility('#assigned_total_resolved', data.data.assignedTicket_info[0].tot_resolved);
            t.toggleCardVisibility('#assigned_waiting_for_user', data.data.assignedTicket_info[0].tot_wfu);
            t.toggleCardVisibility('#assigned_waiting_for_vendor', data.data.assignedTicket_info[0].tot_wfv);
            t.toggleCardVisibility('#assigned_total_closed', data.data.assignedTicket_info[0].tot_closed);
            t.toggleCardVisibility('#assigned_total_spam', data.data.assignedTicket_info[0].tot_spam);
            t.toggleCardVisibility('#assigned_reopened', data.data.assignedTicket_info[0].tot_reopened);
            
            let totals = {
                tot: parseInt(data.data.assignedTicket_info[0].tot) || 0,
                tot_open: parseInt(data.data.assignedTicket_info[0].tot_open) || 0,
                tot_ip: parseInt(data.data.assignedTicket_info[0].tot_ip) || 0,
                tot_resolved: parseInt(data.data.assignedTicket_info[0].tot_resolved) || 0,
                tot_wfu: parseInt(data.data.assignedTicket_info[0].tot_wfu) || 0,
                tot_wfv: parseInt(data.data.ticket_info[0].tot_wfv) || 0,
                tot_closed: parseInt(data.data.assignedTicket_info[0].tot_wfv) || 0,
                tot_spam: parseInt(data.data.assignedTicket_info[0].tot_closed) || 0,
                tot_reopened: parseInt(data.data.assignedTicket_info[0].tot_reopened) || 0,
            };
            let allZero = Object.values(totals).every(val => val === 0)
            if(allZero) {
                $('#noRecordsTicketAssignMsg').removeClass('hide');
            } else {
                $('#noRecordsTicketAssignMsg').addClass('hide');
            }
        }

        if(data.data.myAssignedTicketsRatingInfo[0] != undefined) {
            t.techDash.find('#assigned_rating_overall').html(data.data.myAssignedTicketsRatingInfo[0].overall_rating == null ? '-' : data.data.myAssignedTicketsRatingInfo[0].overall_rating);
            t.techDash.find('#assigned_rating_highest').html(data.data.myAssignedTicketsRatingInfo[0].highest_rating == null ? '-' : data.data.myAssignedTicketsRatingInfo[0].highest_rating);
            t.techDash.find('#assigned_rating_last').html(data.data.lastFeedbackAssigned == null ? '-' : data.data.lastFeedbackAssigned);
            if(data.data.lastFeedbackAssigned != '-') {
                if(data.data.lastFeedbackAssigned < 3) {
                    t.techDash.find('#assigned_rating_last').parent().parent().find('#lap').removeClass('fa-meh-o').addClass('fa-frown-o')
                } else {
                    t.techDash.find('#assigned_rating_last').parent().parent().find('#lap').removeClass('fa-meh-o').addClass('fa-smile-o')
                }
            }
        }

        t.techDash.find("#myassignedTicketsLits").html("");
        t.techDash.find("#myslaBreachedTicketLits").html("");
        t.techDash.find("#myslaABoutToBreachTicketLits").html("");
        if(data.data.myAssignedTicketsInfo.length == 0) {
            t.techDash.find("#myassignedTicketsLits").append("<tr><td colspan='5' style='text-align: center;'>No Records Found</td></tr>");
        } else {
            $.each(data.data.myAssignedTicketsInfo,function(index, ticket){
                t.techDash.find("#myassignedTicketsLits").append("\
                    <tr>\
                        <td>\
                            <a href='ticket/"+ticket.id+"' target='_blank' style='color:blue;' >#"+ticket.id+"</a>\
                        </td>\
                        <td>\
                            "+ticket.subject+"\
                        </td>\
                        <td>\
                            "+ticket.priority+"\
                        </td>\
                        <td>\
                            "+ticket.status+"\
                        </td>\
                        <td>\
                            "+ticket.updated_at_format+"\
                        </td>\
                    </tr>\
                ")
            })
        }

        if(data.data.myAssignedTicketsBreachedInfo.length == 0) {
            t.techDash.find("#myslaBreachedTicketLits").append("<tr><td colspan='5' style='text-align: center;'>No Records Found</td></tr>");
        } else {
            $.each(data.data.myAssignedTicketsBreachedInfo,function(index, ticket) {
                t.techDash.find("#myslaBreachedTicketLits").append("\
                    <tr>\
                        <td>\
                            <a href='ticket/"+ticket.id+"' target='_blank' style='color:blue;' >#"+ticket.id+"</a>\
                        </td>\
                        <td>\
                            "+ticket.subject+"\
                        </td>\
                        <td>\
                            "+ticket.priority+"\
                        </td>\
                        <td>\
                            "+ticket.status+"\
                        </td>\
                        <td>\
                            "+ticket.updated_at_format+"\
                        </td>\
                    </tr>\
                ")
            })
        }

        if(data.data.myAssignedTicketsAboutToBreachedInfo.length == 0) {
            t.techDash.find("#myslaABoutToBreachTicketLits").append("<tr><td colspan='5' style='text-align: center;'>No Records Found</td></tr>");
        } else {
            $.each(data.data.myAssignedTicketsAboutToBreachedInfo,function(index, ticket) {
                t.techDash.find("#myslaABoutToBreachTicketLits").append("\
                    <tr>\
                        <td>\
                            <a href='ticket/"+ticket.id+"' target='_blank' style='color:blue;' >#"+ticket.id+"</a>\
                        </td>\
                        <td>\
                            "+ticket.subject+"\
                        </td>\
                        <td>\
                            "+ticket.priority+"\
                        </td>\
                        <td>\
                            "+ticket.status+"\
                        </td>\
                        <td>\
                            "+ticket.updated_at_format+"\
                        </td>\
                    </tr>\
                ")
            });
        }
    }

    t.loadServiceRequest = function(data) {
        t.userDash.find('#myTicket_total_approval').html(data.data.tot_approval == null ? 0 : data.data.tot_approval);
        t.userDash.find('#myTicket_total_request').html(data.data.tot_myRequest == null ? 0 : data.data.tot_myRequest);
        t.userDash.find('#awaiting_my_approval').html(data.data.tot_awaiting_my_approval == null ? 0 : data.data.tot_awaiting_my_approval);
    }

    t.acceptance = function () {        
        $('#tncModalBody').html(t.tncContent.html());

        let modal = new bootstrap.Modal(
            document.getElementById('tncModal')
        );

        modal.show();
        $('#acceptTnc').off('click').on('click', function () {

            t.httpCall = false;

            $.ajax({
                url: config.tncAcceptance,
                type: "POST",
                data: {
                    "_token": config.token
                }
            })
            .done(function (data) {

                if (typeof data === "object" && data.status === "success") {

                    modal.hide();

                    bootstrap.Modal.getOrCreateInstance(
                        document.getElementById('successModal')
                    ).show();

                    $('#successMessage').html(data.msg);
                }
            })
            .fail(function () {
                alert("Something went wrong. Please check given details are correct");
            })
            .always(function () {
                t.httpCall = true;
            });
        });

        $('#rejectTnc').off('click').on('click', function () {
            window.location.href = config.logOut;
        });
    };

    t.toggleCardVisibility = function (selector, count) {
        var cardTarget = $(selector);
        if (cardTarget.length === 0) {
            return;
        }
        var card = cardTarget.closest('.col-md-2, .m1');
        if (parseInt(count) === 0 || !count) {
            card.addClass('hide');
        } else {
            card.removeClass('hide');
        }
    };
    t.loadUserDash = function(data) {
        if(data.data.ticket_info[0] != undefined){
            t.userDash.find('#myTicket_total_tickets').html(data.data.ticket_info[0].tot);
            t.userDash.find('#myTicket_total_open').html(data.data.ticket_info[0].tot_open == null ? 0 : data.data.ticket_info[0].tot_open);
            t.userDash.find('#myTicket_total_in_progress').html(data.data.ticket_info[0].tot_ip == null ? 0 : data.data.ticket_info[0].tot_ip);
            t.userDash.find('#myTicket_total_resolved').html(data.data.ticket_info[0].tot_resolved  == null ? 0 : data.data.ticket_info[0].tot_resolved);
            t.userDash.find('#myTicket_waiting_for_user').html(data.data.ticket_info[0].tot_wfu == null ? 0 : data.data.ticket_info[0].tot_wfu);
            t.userDash.find('#myTicket_waiting_for_vendor').html(data.data.ticket_info[0].tot_wfv == null ? 0 : data.data.ticket_info[0].tot_wfv);
            t.userDash.find('#myTicket_total_closed').html(data.data.ticket_info[0].tot_closed == null ? 0 : data.data.ticket_info[0].tot_closed);
            t.userDash.find('#myTicket_total_spam').html(data.data.ticket_info[0].tot_spam == null ? 0 : data.data.ticket_info[0].tot_spam);
            t.userDash.find('#myTicket_total_reopened').html(data.data.ticket_info[0].tot_reopened == null ? 0 : data.data.ticket_info[0].tot_reopened);
            if(data.data.ticket_info[0].tot_wfu != null && data.data.ticket_info[0].tot_wfu > 0) {
                $("#userDash .fa-universal-access.social").parents('.pic').addClass("anim");
            }
            t.toggleCardVisibility('#myTicket_total_tickets', data.data.ticket_info[0].tot);
            t.toggleCardVisibility('#myTicket_total_open', data.data.ticket_info[0].tot_open);
            t.toggleCardVisibility('#myTicket_total_in_progress', data.data.ticket_info[0].tot_i);
            t.toggleCardVisibility('#myTicket_total_resolved', data.data.ticket_info[0].tot_resolve);
            t.toggleCardVisibility('#myTicket_waiting_for_user', data.data.ticket_info[0].tot_wfu);
            t.toggleCardVisibility('#myTicket_waiting_for_vendor', data.data.ticket_info[0].tot_wfv);
            t.toggleCardVisibility('#myTicket_total_closed', data.data.ticket_info[0].tot_close);
            t.toggleCardVisibility('#myTicket_total_spam', data.data.ticket_info[0].tot_spa);
            t.toggleCardVisibility('#myTicket_total_reopened', data.data.ticket_info[0].tot_reopened);
            
            let totals = {
                tot: parseInt(data.data.ticket_info[0].tot) || 0,
                tot_open: parseInt(data.data.ticket_info[0].tot_open) || 0,
                tot_ip: parseInt(data.data.ticket_info[0].tot_ip) || 0,
                tot_resolved: parseInt(data.data.ticket_info[0].tot_resolved) || 0,
                tot_wfu: parseInt(data.data.ticket_info[0].tot_wfu) || 0,
                tot_wfv: parseInt(data.data.ticket_info[0].tot_wfv) || 0,
                tot_closed: parseInt(data.data.ticket_info[0].tot_closed) || 0,
                tot_spam: parseInt(data.data.ticket_info[0].tot_spam) || 0,
                tot_reopened: parseInt(data.data.ticket_info[0].tot_reopened) || 0,
                servicereq_total: parseInt(data.data.dataserviceRequestcounts[0].total) || 0,
                servicereq_approved: parseInt(data.data.dataserviceRequestcounts[0].approved) || 0,
                servicereq_waiting: parseInt(data.data.dataserviceRequestcounts[0].waiting) || 0,
            };
            let allZero = Object.values(totals).every(val => val === 0)
           
            if(allZero && data.data.tot_approval == 0 && data.data.tot_myRequest == 0 && data.data.tot_awaiting_my_approval == 0) {
                $('#noRecordsMsg').removeClass('hide');
            } else {
                $('#noRecordsMsg').addClass('hide');
            }
        }

        if(data.data.myTicketsRatingInfo[0] != undefined) {
            t.userDash.find('#myTickets_rating_overall').html(data.data.myTicketsRatingInfo[0].overall_rating == null ? '-' : data.data.myTicketsRatingInfo[0].overall_rating);
            t.userDash.find('#myTickets_rating_highest').html(data.data.myTicketsRatingInfo[0].highest_rating == null ? '-' : data.data.myTicketsRatingInfo[0].highest_rating);
            t.userDash.find('#myTickets_rating_last').html(data.data.lastFeedback == null ? 'NA' : data.data.lastFeedback);
            if(data.data.lastFeedback != '-') {
                if(data.data.lastFeedback < 3) {
                    t.userDash.find('#myTickets_rating_last').parent().parent().find('#lap').removeClass('fa-meh-o').addClass('fa-frown-o')
                }else{
                    t.userDash.find('#myTickets_rating_last').parent().parent().find('#lap').removeClass('fa-meh-o').addClass('fa-smile-o')
                }
            }
        }
        t.userDash.find("#myTicketLits").html("");
        if(data.data.myTicketsInfo.length == 0) {
            t.userDash.find("#myTicketLits").append("<tr><td colspan='7' style='text-align: center;'>No Records Found</td></tr>");
        } else {
            $.each(data.data.myTicketsInfo,function(index, ticket) {
                var reqLink = '';
                if(ticket.request_id != null){
                    reqLink = "(<a href='tickets/requestInfo/"+ticket.id+"' target='_blank' style='color:blue;' >SR"+ticket.id+"</a>)"
                }

                t.userDash.find("#myTicketLits").append("\
                    <tr>\
                        <td>\
                            <a href='ticket/"+ticket.id+"' target='_blank' style='color:blue;' >#"+ticket.id+"</a> "+ reqLink +"\
                        </td>\
                        <td>\
                            "+ticket.subject+"\
                        </td>\
                        <td>\
                            "+ticket.department_name+"\
                        </td>\
                        <td>\
                            "+ticket.priority+"\
                        </td>\
                        <td>\
                            "+ticket.status+"\
                        </td>\
                        <td>\
                            "+ticket.created_at_format+"\
                        </td>\
                        <td>\
                            "+ticket.updated_at_format+"\
                        </td>\
                    </tr>\
                ")
            });
        }
    }

    t.loadAssetDash = function(data) {
        if(data.data.tot_devices != undefined){
            t.assetDash.find('#total_devices').html(data.data.tot_devices);
            t.toggleCardVisibility('#total_devices', data.data.tot_devices);
        }
        if(data.data.tot_accessories != undefined){
            t.assetDash.find('#total_accessories').html(data.data.tot_accessories);
            t.toggleCardVisibility('#total_accessories', data.data.tot_accessories);
        }
        if(data.data.tot_licenses != undefined){
            t.assetDash.find('#total_licence').html(data.data.tot_licenses);
            t.toggleCardVisibility('#total_licence', data.data.tot_licenses);
        }
        if(data.data.tot_consumable != undefined){
            t.assetDash.find('#total_consumable').html(data.data.tot_consumable);
              t.toggleCardVisibility('#total_consumable', data.data.tot_consumable);
        }
        let assetTotals = {
            tot: parseInt(data.data.tot_devices) || 0,
            tot_accessories: parseInt(data.data.tot_accessories) || 0,
            tot_licenses: parseInt(data.data.tot_licenses) || 0,
            tot_consumable: parseInt(data.data.tot_consumable) || 0,
        };
        let allAssetZero = Object.values(assetTotals).every(val => val === 0)
        if(allAssetZero) {
            $('#noRecordsMsgAsset').removeClass('hide');
        } else {
            $('#noRecordsMsgAsset').addClass('hide');
        }
    }

    t.loadKdDocs = function(data) {
        t.section.find('.kdDocsLists').html("");
        if(data.data.length == 0){
            t.section.find(".kdDocsLists").append("<p style='font-size:19px; margin:1px 11px; text-align:center;'>No Document Found</p>");
        } else {
            $.each(data.data, function (index, kd) {
                var tags = '';
                var cardImage = '';
                for (i = 0; i < kd.tag.length; i++) {
                    tags += "<div class='tags'><span class='badge'>" + kd.tag[i].tags + "</span></div>"
                }
                if (kd.card_img == null) {
                    cardImage = config.defaultImage;
                } else {
                    cardImage = kd.card_img;
                }
                t.section.find(".kdDocsLists").append("\
                    <div class='col-md-4'>\
                        <div class='card'>\
                            <div class='top'>\
                                <div class='tags_image'>\
                                    <img src=" + cardImage + " style='height:260px;width:476px;'>\
                                    " + tags + "\
                                </div>\
                            </div>\
                            <div class='bottom'>\
                                <h5 class='card-title'><a href=" + config.kdViewUrl + '/' + kd.id + "' class='card-link' target='_blank'>" + kd.title + "</a></h5>\
                                <div class='detail_content'>\
                                    <h4 class='text'>\
                                        <p>" + kd.content + "</p>\
                                    </h4>\
                                </div>\
                            </div>\
                            <div class='author'>\
                                <p class='kdname'><a href=" + config.kdViewUrl + '/' + kd.id + " class='card-link' target='_blank'>Read More</a></p>\
                            </div>\
                            <div class='info'>\
                                <ion-icon name='chatboxes'><i class='fa fa-clock-o' aria-hidden='true'></i></ion-icon>\
                                <p class='info_date'><a>" + kd.updated_at_format + "</a></p>\
                            </div>\
                        </div>\
                    </div>\
                ")
            })
        }
    }

    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href");
        if (target === '#userDash') {
            $('#kdrelated').removeClass('hide');
        } else {
            $('#kdrelated').addClass('hide');
        }
    });
    t.staringUi = function(element, val) {
        var s = (typeof val != "undefined") ? val : t.data.self_star;
        if (s == true) {
            $(element).html('<i class="fa fa-star"></i>');
        }
        else {
            $(element).html('<i class="fa fa-star-o larger-icon" style="color: white;"></i>');
        }
    };

    t.staring = function(e) {
        e.preventDefault();
        if (t.httpCall != true) {
            return false;
        }
        var id = this.id;
        var element = this;
        vex.dialog.confirm({
            message: config.translations.are_you_star,
            callback: function(value) {
                if (value == true) {
                    t.httpCall = false;
                    var http = $.ajax({
                        url: config.staring,
                        type: "POST",
                        data: {
                            "_token": t.config.token,
                            "id": id
                        }
                    });
                    http.done(function(data) {
                        if (typeof data == "object") {
                            if (data.status == "success") {
                                t.staringUi(element,data.star);
                            } else {
                                vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                            }
                        }
                    });
                    http.fail(function() {
                        alert("Something went wrong. Please check given details are correct");
                    });
                    http.always(function() {
                        t.httpCall = true;
                    });
                }
            }
        });
    };

    t.getKdDocs = function() {
        t.httpPostPath = config.getKdUrl;
        var searchText = t.section.find('.kdsearch').val();
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            data: {
                search: searchText,
                page: typeof(t.pagebtns.pagination("getCurrentPage")) == NaN ? 1 : t.pagebtns.pagination("getCurrentPage"),
                size: t.perPage,
                filters: t.config.other_filters,
				tag_id: t.config.tag_id,
                _token: config.token
            }
        });
        http.done(function(data) {
            if (typeof data == "object" && typeof data.total != "undefined") {
                t.data = data;
                t.pagebtns.pagination("updateItems", data.filtered);
                t.pagebtns.pagination("drawPage", data.page);
                t.home.empty();
                if (data.filtered != data.total) {
                    t.pageBtmSummary.html(config.translations.Available + data.filtered + " records (filtered from " + data.total + " total records)");
                } else {
                    t.pageBtmSummary.html(config.translations.Available_Records + data.total);
                }
                if (data.filtered < 1) {
                    t.home.html('<div class="no-record-found">' + config.translations.No_records_Found + '</div>');
                    return;
                }
                var html = '';

                html += `<div class="kd_details_cards" style="margin: 10px;"><div class="row card-container">`;
                $.each(data.data.list, function(key, d) {
    				const baseUrl = config.articleImagePath;
    				const img = d.card_img ? `${baseUrl}/${d.card_img}` : config.defaultImage;
    				const departmentName = d.dep_name ? d.dep_name : '';
                   
                    html += `
                    <div class="col-md-3">
                        <article class="kd-stack">
                            <a href="${config.kdViewUrl}/${d.id}" target="_blank">
                                <div class="kd-card-image" style="background-image:url('${img}')">
                                    <div class="kd-overlay">
                                        <h4 class="h4-text">${d.title && d.title.length > 30 ? d.title.substr(0, 30) + "..." : d.title}</h4>
                                        ${departmentName ? `<span class="kd-pill text-truncate light">${departmentName}</span>` : ''}
                                    </div>
                                    <span class="kd-arrow" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="View" data-bs-original-title="View">
                                        <svg class="hex-icon-color" width="39" height="39" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"></path>
                                            <path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"></path>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        </article>
                    </div>`;
                });
                html += `</div></div>`;
                // console.log(html);
                t.home.append(html);
            }
        });
    };

    t.btnSearch.on("click", function (e) {
        e.preventDefault();
        var searchText = $.trim(t.btnSearchParent.find(".kdsearch").val());
        t.config.search = searchText;
        t.getKdDocs();
    });

    if(config.isKdEnable == 1 && jQuery.inArray("KnowledgeDocumentRead", config.permissions) !== -1) {
        var searchInput = document.querySelector('input[type="search"]');
        if(searchInput != null && searchInput != undefined) {
            searchInput.addEventListener('search', function (event) {
                t.config.search = t.section.find('.kdsearch').val();
                t.getKdDocs();
            });
        }
    }

    function openModal(tagData) {
        let html = '<div class="tags" style="display: flex; z-index:5;">';
        $.each(tagData, function(key, value) {
            html += '<span class="badge">' + value.tags + '</span>';
        });
        html += '</div>';
        $('#myModal .modal-body').html(html);
        $('#myModal').modal('show');
    }

    if(config.tncAccepted == '') {
        // t.acceptance();
    }
    t.load();
    if(config.isKdEnable == 1 && jQuery.inArray("KnowledgeDocumentRead", config.permissions) !== -1) {
        t.pageBtnClicked = function(n, e) {
            e.preventDefault();
            t.getKdDocs();
        };

        t.pagebtns.pagination({
            itemsOnPage: t.perPage,
            onPageClick: t.pageBtnClicked
        });

        t.getKdDocs();
    }
    $('#myitems').click(function() {
        $('#statusBoard').addClass('hide');
        $('#statusBoarddata').addClass('hide');
        $('#itemscontent').find('#myitems').addClass('active');
        $('#itemscontent').find('#userDash').removeClass('active');
    });
    if ($('#tabActive').hasClass('active')) {
        $('#statusBoard').addClass('hide');
        $('#statusBoarddata').addClass('hide');
        $('#itemscontent').find('#myitems').addClass('active');
        $('#itemscontent').find('#userDash').removeClass('active');
    }

    $('#userDash').click(function() {
        $('#statusBoard').removeClass('hide');
        $('#statusBoarddata').removeClass('hide');
        $('#itemscontent').find('#my_items').removeClass('active');
        $('#itemscontent').find('#userDash').addClass('active');
    });
    t.section.find(".kdsearch").on("keyup", function (e) {
        if (e.key === "Enter" || e.keyCode === 13) {
            t.getKdDocs();
        }
    });
    t.filters.wrapper.find('.filterBtn').on("click", t.search);
    //t.statusBoardList(config.board_info, t.section.find("#sbChart1"));
    // t.ticketBoardList(config.ticket_info, t.section.find("#tbChart2"));
    t.setupTicketStatusSummary=function () {
        var $tiles = $('#myTicketStatusTiles');
        function syncCount($tile) {
            var sourceSelector = $tile.data('count-source');
            var $source = $(sourceSelector);
            var value = $.trim($source.text()) || '0';
            $tile.find('.ai-count-value').text(value);
        }

        $tiles.find('.ai-tile').each(function() {
            var $tile = $(this);
            var $source = $($tile.data('count-source'));
            syncCount($tile);

            if ($source.length && window.MutationObserver) {
                new MutationObserver(function() {
                    syncCount($tile);
                }).observe($source[0], {
                    childList:true,
                    characterData:true,
                    subtree:true
                });
            }
        });

        $('.ai-prev, .ai-next').on('click', function() {
            var tile = $tiles.find('.ai-tile:not(.hide)').get(0);
            if (!tile) {
                return;
            }

            var styles = window.getComputedStyle($tiles[0]);
            var gap = parseFloat(styles.columnGap || styles.gap) || 16;
            var amount = tile.getBoundingClientRect().width + gap;

            $tiles[0].scrollBy({
                left:$(this).hasClass('ai-prev') ? -amount : amount,
                behavior:'smooth'
            });
        });
    }
    t.setupTicketStatusSummary();
};
