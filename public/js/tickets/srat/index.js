var TicketPAB = function (config) {
    var t = this;
    t.config = config;

    t.content = $('#srat-group-list-wrapper');
    t.mdl = t.content.find('#mainContent');
    t.frm = t.mdl.find('#addForm');

    t.frmEl = {};
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.description = t.frm.find('#description');
    t.frmEl.hierarchy_approval = t.frm.find('#hierarchy_approval');
    t.frmEl.required_minimum_approvals = t.frm.find('#required_minimum_approvals');
    t.frmEl.required_manager_level = t.frm.find('#required_manager_level');
    t.frmEl.minimum_approval_required = t.frm.find('#minimum_approval_required');
    t.frmEl.company_id = t.frm.find('#company_id');
    
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');

    t.httpCall = false;

    t.handlesubmit = function (e) {
        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;
    };

    t.fitRequiredMinimamApprovals = function(e) {
        var v = parseInt(t.frmEl.hierarchy_approval.val());
        if( isNaN(v) == false && (v == 2 )) {
            t.frmEl.required_minimum_approvals.rules('add', {required:true});
            t.frmEl.required_minimum_approvals.closest(".cover").removeClass("d-none");
            t.frmEl.minimum_approval_required.removeClass("d-none");
            t.frmEl.required_minimum_approvals.attr("placeholder",t.config.translations.manager_approval);
            t.frmEl.required_manager_level.addClass("d-none");
        }else if( isNaN(v) == false && (v == 12 )) {
            t.frmEl.required_minimum_approvals.rules('add', {required:true});
            t.frmEl.required_minimum_approvals.closest(".cover").removeClass("d-none");
            t.frmEl.minimum_approval_required.addClass("d-none");
            t.frmEl.required_minimum_approvals.attr("placeholder", t.config.translations.next_level_approval);
            t.frmEl.required_manager_level.removeClass("d-none");
        } else {
            t.frmEl.required_minimum_approvals.rules('remove', "required");
            t.frmEl.required_minimum_approvals.closest(".cover").addClass("d-none");
            t.frmEl.minimum_approval_required.addClass("d-none");
            t.frmEl.required_manager_level.addClass("d-none");
        }
    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            name: {
                required: true,
                maxlength:30,
                str_name: true,
                acceptable_spcl_chr: true
            },
            description: {
                required: true,
                maxlength:2000,
                acceptable_spcl_chr: true
            },
            hierarchy_approval:{
                required: true,
                digits: true,
                min:1,
                max:14
            },
            required_minimum_approvals: {
                digits: true,
                min: 1,
                max: function () {
                    let maxLimit = 15;
                    if (t.config && t.config.member_count && t.frmEl.hierarchy_approval.val() == 2) {
                        maxLimit = Math.min(t.config.member_count, 15);
                        if(maxLimit == 0){
                            maxLimit = 15;
                        }
                    }
                    return maxLimit;
                }
            },
            company_id: {
                required: true,
            },
        }
    });

    $(document).on('change keyup', '.content input, .content select, .content textarea', function() {
            $(this).valid();
            if ($(this).valid()) {
                $(this).closest('.form-group').find('label.error').hide();
            } else {
                $(this).closest('.form-group').find('label.error').show();
            }
    });

    var select2Opts = { width: "100%" };
    $("#company_id").select2($.extend({}, select2Opts, {
        dropdownParent: $("#company_id").parent(),
        placeholder: "Select Company",
        ajax: {
            url: t.config.url.get_company_by_user_access,
            dataType: "json",
            delay: 300,
            data: function (p) {
                return {
                    search: p.term,
                    page: p.page || 1,
                };
            }
        },
        templateSelection: function (data, container) {
            $(container).attr('title', data.text);
            return data.text?.length > 50 ? data.text.substring(0, 50) + '...' : data.text;
        },
    })).on('change', function () {
        t.frmValidator.element(this);
    });

    if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
      let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
      $("#company_id").append(option).trigger('change');
    }
    t.frmEl.hierarchy_approval.select2(select2Opts).on("change", $.proxy(t.fitRequiredMinimamApprovals));
    if(typeof t.config.pab == "object" && t.config.pab != "undefined" && t.config.pab.hierarchy_approval == 12){
        t.frmEl.required_minimum_approvals.rules('add', {required:true});
        t.frmEl.required_minimum_approvals.closest(".cover").removeClass("d-none");
        t.frmEl.minimum_approval_required.addClass("d-none");
        t.frmEl.required_manager_level.removeClass("d-none");
    }else if(typeof t.config.pab == "object" && t.config.pab.hierarchy_approval == 2){
        t.frmEl.required_minimum_approvals.rules('add', {required:true});
        t.frmEl.required_minimum_approvals.closest(".cover").removeClass("d-none");
        t.frmEl.minimum_approval_required.removeClass("d-none");
        t.frmEl.required_manager_level.addClass("d-none");
    }
   t.btn.submit.on('click',  $.proxy(t.handlesubmit));
};
