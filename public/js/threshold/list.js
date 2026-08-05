
var Threshold = function (config) {
    var t = this;
    t.config = config;
    t.content = $('main#mainContent');
    t.mdl = t.content.find('#thresholdMdl');
    t.frm = t.mdl.find('#thresholdFrm');
    
    t.frmEl = {};
    t.frmEl.threshold_enabled = t.frm.find('#threshold_enabled');
    t.frmEl.alerts_enabled = t.frm.find('#alerts_enabled');
    t.frmEl.send_alerts = t.frm.find('#send_alerts');
    t.mdl.email = t.frm.find('#email');
    t.mdl.threshold_alert = t.frm.find(".threshold_alert");

    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');
    t.btn.swal2_ok = t.content.find('.swal2-confirm');

    t.httpCall = false;
    t.httpPostPath = t.config.url.edit
    t.resetFrm = {};

    // $(document).one('click', '.swal2-confirm', function () {
    //     t.reload();
    // });

    function onSwalConfirmClick(e) {
        if (e.target.closest('.swal2-confirm')) {
            t.reload();
        }
    }

    // this click is used in this case so that in this case, on click of ok only, it refreshes the page
    document.addEventListener('click', onSwalConfirmClick, true);

    $('#btnSubmit').click(function () {
        $('#img').show();
    });

    t.reload = function () {
        window.location = t.config.url.list;
    };

    t.addThresold = function (e) {
        e.preventDefault();
        //t.mdl.frm.trigger("reset");
        t.frmValidator.resetForm();
        t.frmEl.threshold_enabled = t.frm.find('#threshold_enabled');
        t.frmEl.alerts_enabled = t.frm.find('#alerts_enabled');
        t.frmEl.send_alerts = t.frm.find('#send_alerts');
        t.frmEl.email = t.frm.find('#email');
        t.mdl.modal("show");
        t.httpPostPath = t.config.url.edit;
    };

    if (config.ts.send_alerts === 0 || (config.ts.alerts_enabled === 0 || config.ts.alerts_enabled === null) || (config.ts.threshold_enabled === 0 || config.ts.threshold_enabled === null)) {
        $('.threshold_alert ').css('display', 'none');
    } else {
        $('.threshold_alert').css('display', 'block');
    }

    t.frmEl.send_alerts.on('change', function () {
        let sendAlerts = t.frmEl.send_alerts.val();
        if (sendAlerts === '1') {
            $('.threshold_alert').css('display', 'block');
        } else {
            $('.threshold_alert').css('display', 'none');
        }
    });

    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            $('#img').hide();
            return false;
        }
        var frmData = new FormData(t.frm[0]);
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Success</h3><p>' + data.msg + '</p></div>' });
                    // t.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                    // vex.dialog.alert({ unsafeMessage: '<div style="text-align: center"><h3>Failure</h3><p>' + data.msg + '.</p></div>' });
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': 'Something went wrong. Please check given details are correct',
            };
            sweetAlert('center', 'error', data);
            // vex.dialog.alert("Something went wrong. Please check given details are correct");
        });
        http.always(function () {
            $('#img').hide();
        });
    };

    t.changeAlertEmail = function () {
        var threshold_value = t.frmEl.send_alerts.val();
        if (threshold_value == "1") {
            t.mdl.threshold_alert.removeClass("hide");
        } else if (threshold_value == "0") {
            t.mdl.threshold_alert.addClass("hide");
        }
        //t.frmEl.email.text("");
    };

    $('#email').on('change', function () {
        $(this).valid();
    });

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        errorClass: 'error',
        rules: {
            send_alerts: {
                str_name: true
            },
            'email[]': {
                required: true,
                password: true,
                clean_text_only: true,
            }
        },
        // errorPlacement: function (error, element) {
        //     if (element.attr("name") === "email[]") {
        //         error.insertAfter(element.next('.select2')); 
        //     } else {
        //         error.insertAfter(element);
        //     }
        // }
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        },
        highlight: function (element, errorClass) {
            $(element).closest('.select-div').addClass(errorClass);
        },
        unhighlight: function (element, errorClass) {
            $(element).closest('.select-div').removeClass(errorClass);
        },
    });
    var select2Opts = { width: "100%" };
    $(function () {
        $('#email').select2({
            width: "100%",
            dropdownParent: t.mdl,
            placeholder: "Enter Email",
            tags: true,
            maximumSelectionLength: 10,
            tokenSeparators: [','],
            ajax: {
                url: t.config.url.getUserNIByQuery,
                type: "POST",
                dataType: "json",
                delay: 250,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    return {
                        results: data.results.map(item => ({
                            id: item.text,
                            text: item.text
                        })),
                        pagination: {
                            more: data.pagination?.more
                        }
                    };
                },
                cache: true
            },
            createTag: function (params) {
                const term = $.trim(params.term);
                if (term === '') return null;
                return {
                    id: term,
                    text: term,
                    newTag: true
                };
            },
            insertTag: function (data, tag) {
                data.push(tag);
            }
        });
    });

    t.frmEl.send_alerts.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmEl.send_alerts.parent()
    }));
    t.mdl.email.select2($.extend({}, select2Opts));
    t.frmEl.send_alerts.on("change", $.proxy(t.changeAlertEmail));
    t.content.on('click', '.open-add-modal', $.proxy(t.addThresold));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};