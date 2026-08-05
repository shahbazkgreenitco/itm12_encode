var PoCompany = function (config) {
    var t = this;
    t.config = config;
    t.content = $("#po-company-wrapper");
    t.table = $("#po-company-table");
    t.modal = $("#po-company-mdl");
    t.modalTitle = $("#po-company-mdl-title");
    t.form = $("#po-company-mdl-frm");
    t.loader = $("#po-company-mdl-loader");
    t.previewModal = $("#imagePreviewModal");
    t.previewImage = $("#image-preview");
    t.eyeButton = $(".js-preview-logo");
    t.countrySelect = $("#country");
    t.stateSelect = $("#state");
    t.citySelect = $("#city");

    // Initialize DataTable
    t.initTable = function () {
        t.table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: t.config.url.list,
                type: "POST",
                data: function (d) {
                    d._token = t.config.token;
                }
            },
            columns: [
                { data: "company" },
                { data: "logo" },
                { data: "contact_no" },
                { data: "cin_number" },
                { data: "country" },
                { data: "state" },
                { data: "city" },
                { data: "zip" },
                { data: "gstin" },
                { data: "email_id" },
                { data: "fax_id" },
                { data: "address" },
                { data: "terms_conditions" },
                { data: "payment_terms" },
                { data: "notes" },
                @if(config('app.client') == 'knightfrank')
                { data: "delivery_terms" },
                { data: "warranty_and_support" },
                @endif
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="d-flex gap-2">
                                <button type="button" class="header-icon-btn-only header-icon-btn-only-sm" data-bs-toggle="tooltip" title="{{ trans('button.edit') }}" onclick="editPoCompany(${row.id})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                    </svg>
                                </button>
                                <button type="button" class="header-icon-btn-only header-icon-btn-only-sm" data-bs-toggle="tooltip" title="{{ trans('button.delete') }}" onclick="deletePoCompany(${row.id})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            dom: "rtip",
            language: {
                processing: '<div class="dataTables_processing"><i class="fa fa-spinner fa-spin"></i></div>'
            }
        });
    };

    // Initialize Select2 for Country, State, City
    t.initSelect2 = function () {
        if ($.fn.select2) {
            t.countrySelect.select2({
                placeholder: "Select country",
                allowClear: true
            });

            t.stateSelect.select2({
                placeholder: "Select state",
                allowClear: true
            });

            t.citySelect.select2({
                placeholder: "Select city",
                allowClear: true
            });
        }
    };

    // Handle Country Change
    t.countrySelect.on("change", function () {
        var countryId = $(this).val();
        if (countryId) {
            $.ajax({
                url: t.config.url.states,
                type: "POST",
                data: { country_id: countryId, _token: t.config.token },
                success: function (response) {
                    t.stateSelect.empty().append('<option value="">Select state</option>');
                    $.each(response, function (key, value) {
                        t.stateSelect.append('<option value="' + key + '">' + value + '</option>');
                    });
                    t.stateSelect.trigger("change");
                }
            });
        } else {
            t.stateSelect.empty().append('<option value="">Select state</option>');
            t.citySelect.empty().append('<option value="">Select city</option>');
        }
    });

    // Handle State Change
    t.stateSelect.on("change", function () {
        var stateId = $(this).val();
        if (stateId) {
            $.ajax({
                url: t.config.url.cities,
                type: "POST",
                data: { state_id: stateId, _token: t.config.token },
                success: function (response) {
                    t.citySelect.empty().append('<option value="">Select city</option>');
                    $.each(response, function (key, value) {
                        t.citySelect.append('<option value="' + key + '">' + value + '</option>');
                    });
                    t.citySelect.trigger("change");
                }
            });
        } else {
            t.citySelect.empty().append('<option value="">Select city</option>');
        }
    });

    // Handle Image Preview
    t.eyeButton.on("click", function (e) {
        e.preventDefault();
        var fileInput = $(this).prev("input[type='file']");
        var file = fileInput[0].files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                t.previewImage.attr("src", e.target.result);
                t.previewModal.modal("show");
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle Form Submit
    t.form.on("submit", function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append("_token", t.config.token);

        t.loader.removeClass("d-none");

        $.ajax({
            url: t.form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                t.loader.addClass("d-none");
                t.modal.modal("hide");
                t.table.DataTable().ajax.reload();
                alert(response.message);
            },
            error: function (xhr) {
                t.loader.addClass("d-none");
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    for (var field in errors) {
                        $("#" + field).addClass("is-invalid");
                        $("#" + field + "_error").text(errors[field][0]);
                    }
                } else {
                    alert("An error occurred.");
                }
            }
        });
    });

    // Initialize
    t.initTable();
    t.initSelect2();
};
