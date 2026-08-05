var BudgetIndex = function (config) {
    var t = this;
    t.config = config;
    t.content = $("#main-budget-wrapper");
    t.modal = $('#budgetMdl');
    t.form = $('#budgetForm');
    t.nameInput = $('#name');
    t.submitBtn = $('#btnSubmit');
    t.clearBtn = $('#btnClear');
    t.imgSpinner = $('#img');

    // Initialize
    t.init = function () {
        t.bindEvents();
    };

    // Bind events
    t.bindEvents = function () {
        t.modal.on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var action = button.data('action');
            if (action === 'create') {
                t.resetForm();
                t.form.attr('action', t.config.url.create);
            }
        });

        t.submitBtn.on('click', function () {
            t.saveBudget();
        });

        t.clearBtn.on('click', function () {
            t.modal.modal('hide');
        });
    };

    // Reset form
    t.resetForm = function () {
        t.form[0].reset();
        t.nameInput.removeClass('is-invalid');
        $('.invalid-feedback').remove();
    };

    // Save budget
    t.saveBudget = function () {
        var formData = t.form.serialize();
        var actionUrl = t.form.attr('action');

        t.imgSpinner.show();

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': t.config.token
            },
            success: function (response) {
                t.imgSpinner.hide();
                if (response.success) {
                    t.modal.modal('hide');
                    // Reload table or show success message
                    window.location.reload();
                } else {
                    // Handle validation errors
                    if (response.errors && response.errors.name) {
                        t.nameInput.addClass('is-invalid');
                        t.nameInput.after('<div class="invalid-feedback">' + response.errors.name[0] + '</div>');
                    }
                }
            },
            error: function () {
                t.imgSpinner.hide();
                alert('An error occurred while saving the budget.');
            }
        });
    };

    // Initialize on load
    t.init();
};
