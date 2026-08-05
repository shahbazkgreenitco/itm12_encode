var PabModal = function (config) {
    var t = this;
    t.config = config;
    t.modal = $("#addPABModal");
    t.form = $("#pabForm");
    t.loader = t.modal.find("#pab-loader");
    t.submitBtn = t.modal.find("#btnSubmit");
    t.clearBtn = t.modal.find("#btnClear");
    t.hierarchySelect = t.modal.find("#hierarchy_approval");
    t.requiredField = t.modal.find(".cover");

    t.init = function () {
        t.bindEvents();
    };

    t.bindEvents = function () {
        t.submitBtn.on("click", t.handleSubmit);
        t.clearBtn.on("click", t.handleClear);
        t.hierarchySelect.on("change", t.toggleRequiredField);
    };

    t.toggleRequiredField = function () {
        if (t.hierarchySelect.val() == "3") {
            t.requiredField.removeClass("hide");
        } else {
            t.requiredField.addClass("hide");
        }
    };

    t.handleSubmit = function (e) {
        e.preventDefault();
        if (t.form.valid()) {
            t.loader.removeClass("d-none");
            t.submitBtn.prop("disabled", true);

            var formData = new FormData(t.form[0]);
            formData.append("_token", config.token);

            $.ajax({
                url: config.url.save,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    t.loader.addClass("d-none");
                    t.submitBtn.prop("disabled", false);
                    t.modal.modal("hide");
                    // Assuming a global refresh or specific refresh function exists
                    if (typeof refreshTable === 'function') refreshTable();
                },
                error: function (xhr) {
                    t.loader.addClass("d-none");
                    t.submitBtn.prop("disabled", false);
                    if (xhr.status === 422) {
                        // Handle validation errors if needed
                        console.log(xhr.responseJSON);
                    }
                }
            });
        }
    };

    t.handleClear = function () {
        t.form[0].reset();
        t.toggleRequiredField();
    };

    t.init();
};
