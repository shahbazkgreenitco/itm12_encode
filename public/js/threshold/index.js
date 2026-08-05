var Threshold = function (config) {
    var t = this;
    t.config = config;

    t.content = $('section.content');

    t.mdl = t.content.find('#threshold');

    t.frm = t.mdl.find('#thresholdFrm');

    t.frmEl = {};
    t.frmEl.send_alerts = t.frm.find('#send_alerts');
    t.frmEl.email = t.frm.find('#email');
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.clear = t.frm.find('#btnClear');

    t.httpCall = false;
    t.httpPostPath = t.config.url.add
    t.resetFrm = {};

    t.handlesubmit = function (e) {

        if (t.frmValidator.form() == false) {
            e.preventDefault();
            return false;
        }
        return true;

    };

    t.frmValidator = t.frm.validate({
        onsubmit: false,
        rules: {
            send_alerts: {
                str_name: true
            },
            email: {
                password: true
            },

        },
        errorPlacement: function (error, element) {
            error.appendTo(element.parent().parent());
        }
    });
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
};