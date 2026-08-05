var KanbanConfig = function (config) {
    var t = this;
    t.config = config;
    t.httpCall = true;

    t.frm = $("#general_config");
    t.frmEl = {};
    t.frmEl.before_overdue = t.frm.find("#before_overdue");
    t.btnUpdate = t.frm.find("#btnSubmit");

    t.frmEl.before_overdue.val(t.config.overdue_mail_before_id);

    t.saveData = function (e) {
        e.preventDefault();

        if (!t.httpCall) return;
        t.httpCall = false;
        t.btnUpdate.prop("disabled", true);

        $.ajax({
            url: t.config.url.update,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                before_overdue: t.frmEl.before_overdue.val(),
                old_before_overdue: t.config.overdue_mail_before_id ?? 'null',
            },
            success: function (data) {
                if (data.message && !data.msg) {
                    data.msg = data.message;
                }
                if (data.status == "success") {
                    sweetAlert('center', 'success', data);
                } else {
                    sweetAlert('center', 'error', data);
                }
            },
            error: function (xhr, status, error) { 
                sweetAlert('center', 'error', { msg:t.config.translations.something_went_wrong });
            },
            complete: function () {
                t.httpCall = true;
                t.btnUpdate.prop("disabled", false);
            }
        });
    };

    t.btnUpdate.on("click", $.proxy(t.saveData));
};