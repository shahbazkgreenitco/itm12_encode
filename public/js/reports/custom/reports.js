var TitleMdl = function (config, parent) {
    var t = this;
    t.config = config;
    t.parent = parent;
    t.token = $('head meta[name="csrf-token"]');

    t.section = t.parent.section;
    t.mdl = t.section.find('#titleMdl');
    t.mdl.title = t.mdl.find('.modal-title');

    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');

    t.mdl.frm = t.mdl.find("#frm");
    t.mdl.frmEl = {};
    t.mdl.frmEl.companyId = t.mdl.frm.find("#company_id");
    t.mdl.frmEl.report_name = t.mdl.frm.find("#report_name");
    t.mdl.frmEl.status = t.mdl.frm.find("#status");
    t.mdl.frmEl.report_type = t.mdl.frm.find("#report_type");

    t.httpCall = true;
    t.httpPostPath = "";

    t.mdl.frmEl.companyId.select2({
        dropdownParent: t.mdl.frmEl.companyId.parent(),
        ajax: {
            url: function (params) {
                return t.config.url.getCompanyByUserAccess;
            },
            dataType: "json",
            delay: 250,
            data: function (params) {
                return {
                    search: params.term,
                    page: params.page,
                }
            },
            processResults: function (data) {
                return {
                    results: data.results,
                }
            },
            cache: true
        },
        width: "100%",
        allowClear: true,
        placeholder: "Select Company"
    })

    t.frmValidator = t.mdl.frm.validate({
        onsubmit: false,
        rules: {
            report_name: {
                required: true,
                maxlength: 100,
                str_name: true
            },
            company_id: {
                required: true,
            },
            status: {
                required: true,
            },
            report_type: {
                required: true,
            }
        }, errorPlacement: function (error, element) {
            if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.appendTo(element.closest('div'));
            }
        }
    });

    t.editTitle = function (e) {
        e.preventDefault();
        t.reset();
        var id = $(this).attr("data-id");
        t.httpPostPath = t.config.url.update_name + "/" + id;
        $.ajax({
            url: t.config.url.edit_report,
            type: 'get',
            data: {
                _token: t.config.token,
                id: id
            },
            success: function (response) {
                if (response.success) {
                    if (response.data.company_id != "" || response.data.company_id != null) {
                        t.mdl.frmEl.companyId.val(response.data.company_id).empty().append(new Option(response.data.company_name, response.data.company_id, true, true)).trigger("change");
                    }
                    t.mdl.frmEl.report_name.val(response.data.report_name);
                    t.mdl.frmEl.status.val(response.data.status).trigger('change');
                    t.mdl.frmEl.report_type.val(response.data.report_type).trigger('change');
                    t.mdl.btnSubmit.text('Update');
                    t.mdl.modal("show");
                } else {
                    alert('Error fetching report details');
                }
            },
            error: function () {
                alert('Error occurred while fetching the report details');
            }
        });
    };

    t.addTitle = function (e) {
        e.preventDefault();
        t.reset();
        t.httpPostPath = t.config.url.add_report;
        t.mdl.title.text(config.translations.New_Custom_Report);
        t.mdl.btnSubmit.text('Create');
        t.mdl.frmEl.companyId.val("").empty().trigger("change");
        var default_company_id = $("#config-company").val();
        var default_company_text = $("#config-company option:selected").text();
        if (default_company_id != 0) {
            let option = new Option(default_company_text, default_company_id, true, true);
            t.mdl.frmEl.companyId.empty().append(option).trigger("change");
        } else {
            t.mdl.frmEl.companyId.val("").trigger("change")
        }
        t.mdl.modal("show");
    };

    t.reset = function () {
        t.mdl.frm.trigger("reset");
        t.frmValidator.resetForm();
        t.mdl.frmEl.report_name.val("");
        t.mdl.frmEl.status.val('').trigger('change');
        t.mdl.frmEl.companyId.val("").empty().trigger("change");
        t.mdl.frmEl.report_type.val('').trigger('change');
    };

    t.handleSubmit = function (e) {
        e.preventDefault();

        if (t.httpCall != true) {
            return false;
        }

        if (t.frmValidator.form() == false) {
            return false;
        }

        var frmData = new FormData(t.mdl.frm[0]);
        frmData.append('_token', t.token.attr('content'));

        t.httpCall = false;
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
                    t.parent.reload();
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };
    $('#titleMdl').on('change keyup', '#frm input, #frm select', function () {
        var $this = $(this);
        if ($this.closest('.modal').is(':visible')) {
            $this.valid();
            if ($this.valid()) {
                $this.closest('.form-group').find('label.error').hide();
            } else {
                $this.closest('.form-group').find('label.error').show();
            }
        }
    });

    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    var select2Opts = { width: "100%" };
    t.mdl.frmEl.status.select2($.extend({}, select2Opts, { allowClear: true, dropdownParent: t.mdl.frmEl.status.parent(), placeholder: "Select Status" }));
    t.mdl.frmEl.report_type.select2($.extend({}, select2Opts, { allowClear: true, dropdownParent: t.mdl.frmEl.report_type.parent(), placeholder: "Select Report Type" }));
};

var FieldMdl = function (config, parent) {
    var t = this;
    t.config = config;
    t.parent = parent;
    t.token = $('head meta[name="csrf-token"]');

    t.section = t.parent.section;
    t.mdl = t.section.find('#fieldMdl');
    t.mdl.title = t.mdl.find('.modal-title');

    t.mdl.btnSubmit = t.mdl.find('#btnSubmit');
    t.mdl.btnClear = t.mdl.find('#btnClear');

    t.mdl.frm = t.mdl.find("#frm");
    t.mdl.fields_a = t.mdl.find("#possible_fields");
    t.mdl.fields_b = t.mdl.find("#report_fields");

    t.httpCall = true;
    t.httpPostPath = "";

    t.loadFieldBoxes = function (report_type, existing_fields) {
        existing_fields = existing_fields && existing_fields !== "null" && existing_fields.length > 0 ? existing_fields.split(",") : [];
        let existing_field_names = {};
        let availableFieldsArray = Array.isArray(t.config.available_fields) ? t.config.available_fields : Object.values(t.config.available_fields || {});

        t.mdl.fields_a.empty();
        let matchingFields = availableFieldsArray.filter(field => field.report_module == report_type);

        matchingFields.forEach(field => {
            let cleanedName = field.field_name.replace(/^_?itm_/, '').replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
            let isExisting = existing_fields.includes(field.id.toString()) ? 'na' : '';
            let listItem = `<li class="field list-group-item list-item-sm d-flex justify-content-between align-items-center ${isExisting}" id="${field.id}" data-name="${field.field_name}"> ${cleanedName} <span class="push-to-right"><i class="bi bi-arrow-right-square push-to-right"></i></span></li>`;
            t.mdl.fields_a.append(listItem);

            if (isExisting === 'na') {
                existing_field_names["i" + field.id] = field.field_name;
            }
        });

        t.mdl.fields_b.empty();

        existing_fields.forEach(fieldId => {
            let rawName = existing_field_names["i" + fieldId];
            if (rawName) {
                let cleanedName = rawName.replace(/^_?itm_/, '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                let listItem = `<li class="field list-group-item list-item-sm d-flex justify-content-between align-items-center" id="${fieldId}">${cleanedName}<span class="push-to-right"><i class="bi bi-grip-vertical handle"></i><i class="bi bi-x-square push-to-left"></i></span></li>`;
                t.mdl.fields_b.append(listItem);
            } else {
                console.warn("Missing field name for:", "i" + fieldId);
            }
        });
    }

    t.feedField = function (e) {
        e.preventDefault();
        var el = $(this).closest('.field');
        if (t.mdl.fields_b.find('#' + el.attr('id')).length != 0) {
            return;
        }
        t.mdl.fields_b.append('<li class="field list-group-item list-item-sm d-flex justify-content-between align-items-center" id="' + el.attr('id') + '">' + el.attr('data-name') + '<span class="push-to-right"><i class="bi bi-grip-vertical handle"></i><i class="bi bi-x-square push-to-left"></i></span></li>');
        el.addClass('na');
    };

    t.unfeedField = function (e) {
        e.preventDefault();
        var el = $(this).closest('.field');
        if (t.mdl.fields_a.find('#' + el.attr('id')).length == 0) {
            return;
        }
        t.mdl.fields_a.find('#' + el.attr('id')).removeClass('na');
        el.remove();
    };

    t.editField = function (e) {
        e.preventDefault();
        t.reset();
        var id = $(this).attr("data-id");
        var reportType = $(this).attr("data-report-type");
        t.fetchFields(id, reportType);
    };

    t.fetchFields = function (id, reportType) {
        t.httpPostPath = t.config.url.get_fields + "/" + id;

        t.httpCall = false;
        var http = $.ajax({
            url: t.httpPostPath,
            type: "GET"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    var existing_fields = data.data && data.data.fields ? data.data.fields : [];
                    t.loadFieldBoxes(reportType, existing_fields);
                    t.mdl.modal("show");
                    t.httpPostPath = t.config.url.update_fields + "/" + id;
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.reset = function () {
        t.mdl.frm.trigger("reset");
        t.mdl.fields_a.empty();
        t.mdl.fields_b.empty();
    };

    t.handleSubmit = function (e) {
        e.preventDefault();

        if (t.httpCall != true) {
            return false;
        }

        var fields = t.mdl.fields_b.find('.field');
        if (fields.length == 0) {
            let data = { msg: 'Please add some fields' };
            sweetAlert('center', 'error', data);
            return false;
        }
        if (fields.length > 20) {
            let data = { msg: config.translations.system_not_allowed };
            sweetAlert('center', 'error', data);
            return false;
        }

        var ordered_fields = [];
        $.each(fields, function (i, j) {
            ordered_fields.push(j.id);
        });

        var frmData = new FormData();
        frmData.append('_token', t.token.attr('content'));
        frmData.append('fields', ordered_fields.join(','));

        t.httpCall = false;
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
                    t.parent.reload();
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.mdl.frm.on("submit", $.proxy(t.handleSubmit));
    t.mdl.fields_a.on("click", ".push-to-right", $.proxy(t.feedField));
    t.mdl.fields_b.on("click", ".push-to-left", $.proxy(t.unfeedField));
    t.mdl.fields_b.sortable({ handle: ".handle" });
};

var RuleMdl = function (config, parent) {
    var t = this;
    t.config = config;
    t.parent = parent;
    t.token = $('head meta[name="csrf-token"]');
    t.report_id = 0;
    t.dataSet = [];

    t.section = t.parent.section;
    t.mdl = t.section.find('#ruleMdl');
    t.mdl.title = t.mdl.find('.modal-title');

    t.feeder = t.mdl.find('#feeder');
    t.lister = t.mdl.find('#lister');
    t.rule_dictation_part = t.mdl.find('#rule_dictation_part');
    t.rule_dictation = t.rule_dictation_part.find('#rule_dictation');
    t.rule_dictation.logics = { "l1": "AND", "l2": "OR", "l3": "(", "l4": ")" };
    t.rule_dictation.buttons_area = t.rule_dictation_part.find('.buttons-area');

    t.feeder.frm = t.feeder.find("#frm");
    t.feeder.field_id = t.feeder.find('#field_id');
    t.feeder.condition_code = t.feeder.find('#condition_code');
    t.feeder.value_box_cover = t.feeder.find('#value_box_cover');
    t.feeder.value_label = t.feeder.find('#value_label');
    t.feeder.btnClear = t.feeder.find('#btnClear');

    t.lister.table = t.lister.find("#rules");

    t.btn = {};
    t.btn.add_rule = t.mdl.find('.btn-add-rule');

    t.httpCall = true;
    t.httpPostPath = "";

    t.possible_checks = [];
    t.value_boxes = [];

    t.feeder.frmValidator = t.feeder.frm.validate({
        onsubmit: false,
        rules: {
            field_id: {
                required: true,
                str_name: true
            },
            condition_code: {
                required: true,
                str_name: true
            }
        }
    });

    t.add_logic = function (e) {
        e.preventDefault();
        t.rule_dictation.buttons_area.removeClass("d-none");
        if ($(this).hasClass('act-rule-dictate')) {
            t.rule_dictation.append('<li data-type="R" data-code="' + $(this).attr('data-rule_no') + '"><span class="code">' + $(this).attr('data-rule_no') + '</span><span class="rmvbox logic-remover"><i class="bi bi-x-square"></i></span></li>');
            return;
        }
        t.rule_dictation.append('<li data-type="l" data-code="' + $(this).attr('data-logic') + '"><span class="code">' + $(this).text() + '</span><span class="rmvbox logic-remover"><i class="bi bi-x-square"></i></span></li>');
    };

    t.remove_logic = function (e) {
        e.preventDefault();
        t.rule_dictation.buttons_area.removeClass("d-none");
        $(this).closest("li").remove();
    };

    t.sort_change_detected = function (e) {
        t.rule_dictation.buttons_area.removeClass("d-none");
    };

    t.render_dictation_box = function () {
        t.rule_dictation.empty();
        if (typeof t.dataSet.dictated_rules == "string" && t.dataSet.dictated_rules != null) {
            console.log("Str Order ", t.dataSet.dictated_rules);
            var dr = t.dataSet.dictated_rules.split(",");
            console.log("Arr Order ", dr);
            for (i in dr) {
                var j = dr[i];
                if (j.indexOf("l") > -1) {
                    t.rule_dictation.append('<li data-type="l" data-code="' + j + '"><span class="code">' + t.rule_dictation.logics[j] + '</span><span class="rmvbox logic-remover"><i class="bi bi-x-square"></i></span></li>');
                    console.log("Logic ", j);
                }
                else if (j.indexOf("R") > -1) {
                    console.log("Rule ", j);
                    t.rule_dictation.append('<li data-type="R" data-code="' + j + '"><span class="code">' + j + '</span><span class="rmvbox logic-remover"><i class="bi bi-x-square"></i></span></li>');
                }
            }
        }
    };

    t.rule_dictation_cancel = function (e) {
        e.preventDefault();
        t.render_dictation_box();
    };

    t.rule_dictation_save = function (e) {
        e.preventDefault();

        if (t.httpCall != true) {
            return false;
        }

        var new_rule = [];
        t.rule_dictation.children().each(function (m, n) {
            new_rule.push($(n).attr("data-code"));
        });

        var frmData = new FormData();
        frmData.append('_token', t.token.attr('content'));
        frmData.append('dictated_rules', new_rule.join(","));

        t.httpPostPath = t.config.url.update_dictation + "/" + t.report_id;

        t.httpCall = false;
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
                    t.dataSet = data.dataSet;
                    t.render_dictation_box();
                    t.lister.reload();
                    t.rule_dictation.buttons_area.addClass("d-none");
                    sweetAlert('center', 'success', data);
                    t.parent.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.loadFields = function () {
        t.feeder.field_id.empty().append('<option value="">Select Field</option>');
        var reportType = t.feeder.report_type;
        var excludeIds = [94, 93, 92, 91, 89, 88, 86, 85, 84, 83, 82, 78, 77, 76, 75, 67];
        $.each(t.config.available_fields, function (i, field) {
            if (field.report_module == reportType && !excludeIds.includes(field.id)) {
                t.possible_checks['i' + field.id] = field.possible_checks;
                t.value_boxes['i' + field.id] = JSON.parse(field.value_box);
                t.feeder.field_id.append('<option value="' + field.id + '">' + field.field_name + '</option>');
            }
        });

        if (typeof t.feeder.loaded_rule === "object") {
            t.feeder.field_id.val(t.feeder.loaded_rule.field_id);
        }
        t.feeder.field_id.trigger("change");
    };

    t.editRules = function (e) {
        e.preventDefault();
        t.feeder.reset();
        t.report_id = $(this).attr("data-id");
        t.report_type = $(this).attr("data-report-type");
        t.report_company = $(this).attr("data-report-company");
        t.httpPostPath = t.config.url.get_criteria + "/" + t.report_id;

        t.httpCall = false;
        var http = $.ajax({
            url: t.httpPostPath,
            type: "GET"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.dataSet = data.dataSet;
                    t.render_dictation_box();
                    t.lister.reload();
                    t.mdl.modal("show");
                    t.feeder.value_label.hide();
                    t.feeder.report_type = t.report_type;
                    $('#rule_dictation_part').attr('company_id', t.report_company);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.feeder.edit = function (e) {
        e.preventDefault();
        var rule_id = $(this).attr("data-id");
        if (t.httpCall != true) {
            return false;
        }

        t.httpCall = false;
        t.httpPostPath = t.config.url.get_rule + "/" + rule_id;

        var http = $.ajax({
            url: t.httpPostPath,
            type: "GET"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.feeder.reset();
                    t.httpPostPath = t.config.url.update_rule + "/" + rule_id;
                    t.feeder.removeClass("d-none");
                    t.lister.addClass("d-none");
                    t.feeder.loaded_rule = data.data;
                    t.loadFields();
                    if (data.data.field_id == 53) {
                        var selectedValue = data.data.compare_values;
                        var selectBox = t.feeder.compare_values;
                        selectBox.val(selectedValue).trigger('change');
                    }
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.loadCondition = function (e) {
        e.preventDefault();
        t.feeder.condition_code.empty().append('<option value="">Select Comparison</option>');

        if (t.feeder.field_id.val() > 0) {
            var poss_codes = (t.possible_checks['i' + t.feeder.field_id.val()]).split(',');
            $.each(t.config.comparison_codes, function (i, j) {
                if (poss_codes.indexOf(j.id.toString()) > -1) {
                    t.feeder.condition_code.append('<option value="' + j.id + '">' + j.text_code + '</option>');
                }
            });
        }

        if (typeof t.feeder.loaded_rule == "object") {
            t.feeder.condition_code.val(t.feeder.loaded_rule.condition_code);
        }

        t.feeder.condition_code.trigger("change");
    };

    t.loadCompareValues = function (opts) {
        t.feeder.compare_values.empty().append('<option value="">Select Value</option>');
        $.each(opts, function (i, j) {
            t.feeder.compare_values.append('<option value="' + j.id + '">' + j.text + '</option>');
        });
        if (typeof t.feeder.loaded_rule == "object" && t.feeder.loaded_rule.compare_values) {
            if (t.feeder.compare_values.is("select") && t.feeder.compare_values.prop("multiple")) {
                var values = t.feeder.loaded_rule.compare_values.split(',');
                t.feeder.compare_values.val(values).trigger('change');
            } else {
                t.feeder.compare_values.val(t.feeder.loaded_rule.compare_values).trigger('change');
            }
        }
        t.feeder.compare_values.trigger("change");
    };

    t.get_remote_opts = function (url) {
        $.get(url, function (data) {
            if (typeof data != "undefined" && typeof data.data == "object") {
                t.loadCompareValues(data.data);
            }
        });
    };

    t.preload_text_type_inputs = function (as_datepicker) {
        if (typeof t.feeder.loaded_rule == "object") {
            if (typeof as_datepicker != "undefined") {
                t.feeder.compare_values.datepicker("update", t.feeder.loaded_rule.compare_values);
                return;
            }
            t.feeder.compare_values.val(t.feeder.loaded_rule.compare_values);
        }
    };

    t.get_bind = function () {
        t.feeder.compare_values = t.feeder.find('#compare_values');
    };

    t.loadValueBox = function (e) {
        e.preventDefault();
        t.feeder.value_label.show();
        var field_id, condition_code, value_boxes, input_box, select_box;
        field_id = t.feeder.field_id.val();
        condition_code = t.feeder.condition_code.val();
        value_boxes = typeof t.value_boxes['i' + field_id] != "undefined" ? t.value_boxes['i' + field_id] : false;
        input_box = '<input type="text" id="compare_values" name="compare_values" class="form-control" autocomplete="off" />';
        select_box = '<select id="compare_values" name="compare_values" class="form-control"></select>';
        if (value_boxes != false) {
            t.feeder.value_box_cover.empty();
            var target_value_box = typeof value_boxes['c' + condition_code] != "undefined" ? parseInt(value_boxes['c' + condition_code]) : parseInt(value_boxes['fb']);
            switch (target_value_box) {
                case 1:
                    t.feeder.value_box_cover.append(input_box);
                    t.get_bind();
                    t.preload_text_type_inputs();
                    $('#value_box_cover').find("input").each(function () {
                        $(this).rules("add", {
                            required: true,
                            messages: {
                                required: "This field is required"
                            }
                        });
                    });
                    break;
                case 2:
                    t.feeder.value_box_cover.append(select_box);
                    t.get_bind();
                    t.feeder.compare_values.attr("name", "compare_values").select2({
                        width: "100%",
                        dropdownParent:t.feeder.compare_values.parent(),
                        templateSelection: function (data) {
                            if (data.text.length > 45) {
                                return data.text.substr(0, 45) + '...';
                            }
                            return data.text;
                        },
                    });
                    $('#value_box_cover').find("select").each(function () {
                        $(this).rules("add", {
                            required: true,
                            messages: {
                                required: "This field is required"
                            }
                        });
                    });
                    break;
                case 3:
                    t.feeder.value_box_cover.append(input_box);
                    t.get_bind();
                    t.preload_text_type_inputs();
                    $('#value_box_cover').find("input").each(function () {
                        $(this).rules("add", {
                            required: true,
                            messages: {
                                required: "This field is required"
                            }
                        });
                    });
                    break;
                case 4:
                    t.feeder.value_box_cover.append(input_box);
                    t.get_bind();
                    t.feeder.compare_values.addClass("datepicker").datepicker({ format: "dd/mm/yyyy" });
                    t.preload_text_type_inputs(true);
                    $('#value_box_cover').find("input").each(function () {
                        $(this).rules("add", {
                            required: true,
                            messages: {
                                required: "This field is required"
                            }
                        });
                    });
                    break;
                case 5:
                    t.feeder.value_box_cover.append(select_box);
                    t.get_bind();
                    t.feeder.compare_values.attr("multiple", "multiple").attr("name", "compare_values[]");
                    t.feeder.compare_values.select2({
                        width: "100%",
                        dropdownParent:t.feeder.compare_values.parent(),
                        templateSelection: function (data) {
                            if (data.text.length > 45) {
                                return data.text.substr(0, 45) + '...';
                            }
                            return data.text;
                        },
                    });
                    $('#value_box_cover').find("select").each(function () {
                        $(this).rules("add", {
                            required: true,
                            messages: {
                                required: "This field is required"
                            }
                        });
                    });
            }

            if (!condition_code) {
                return;
            }

            if (field_id == 20) {
                t.loadCompareValues(t.config.opts_lease_type);
            }
            else if (field_id == 21) {
                t.loadCompareValues(t.config.opts_maintenance_incharges);
            }
            else if (field_id == 26) {
                t.loadCompareValues(t.config.opts_device_from);
            }
            else if (field_id == 31) {
                t.loadCompareValues(t.config.opts_assigned_for);
            }
            else if (field_id == 6) {
                t.get_remote_opts(t.config.url.get_models);
            }
            else if (field_id == 7 || field_id == 56 || field_id == 66) {
                t.get_remote_opts(t.config.url.get_locations);
            }
            else if (field_id == 8) {
                t.get_remote_opts(t.config.url.get_status_labels);
            }
            else if (field_id == 9) {
                t.get_remote_opts(t.config.url.get_purchase_references);
            }
            else if (field_id == 17) {
                t.get_remote_opts(t.config.url.get_manufacturers);
            }
            else if (field_id == 18) {
                t.get_remote_opts(t.config.url.get_categories);
            }
            else if (field_id == 19) {
                t.get_remote_opts(t.config.url.get_account_types);
            }
            else if (field_id == 27 || field_id == 34 || field_id == 35) {
                t.get_remote_opts(t.config.url.get_suppliers);
            }
            else if (field_id == 32) { /* users */
                t.get_remote_opts(t.config.url.get_models);
            }
            else if (field_id == 33 || field_id == 50) {
                t.get_remote_opts(t.config.url.get_departments);
            }
            else if (field_id == 43) { /* currencies */
                t.get_remote_opts(t.config.url.get_models);
            }
            else if (field_id == 24 || field_id == 37 || field_id == 41) {
                t.loadCompareValues(t.config.opts_expired_not);
            }
            else if (field_id == 25 || field_id == 28) {
                t.loadCompareValues(t.config.opts_yes_no);
            }
            else if (field_id == 42) {
                t.get_remote_opts(t.config.url.get_places);
            }
            else if (field_id == 48) {
                t.get_remote_opts(t.config.url.get_status_list);
            }
            else if (field_id == 49) {
                t.get_remote_opts(t.config.url.get_priority);
            }
            else if (field_id == 51) {
                t.get_remote_opts(config.url.get_problemCategory);
            }
            else if (field_id == 52) {
                t.get_remote_opts(config.url.get_subCategory);
            }
            else if (field_id == 54 || field_id == 59 || field_id == 60 || field_id == 61 || field_id == 68 || field_id == 72) {
                t.get_remote_opts(config.url.getUser);
            }
            else if (field_id == 58) {
                t.get_remote_opts(config.url.getDevice);
            }
            else if (field_id == 67) {
                t.get_remote_opts(config.url.getTags);
            }
            else if (field_id == 57) {
                t.get_remote_opts(config.url.getTicketType);
            } else if (field_id == 53) {
                var options = [
                    { value: 1, label: 'Portal' },
                    { value: 2, label: 'Chat' },
                    { value: 3, label: 'Mail' },
                    { value: 4, label: 'Mobile' },
                    { value: 5, label: 'Call' },
                    { value: 6, label: 'BOT' }
                ];
                var select = t.feeder.compare_values;
                select.empty();
                options.forEach(function (option) {
                    select.append('<option value="' + option.value + '">' + option.label + '</option>');
                });
                select.select2({ width: "100%" })
            }
        }
    };

    t.openFeeder = function (e) {
        e.preventDefault();
        t.feeder.reset();
        t.feeder.report_type = t.report_type;
        t.loadFields();
        t.httpPostPath = t.config.url.update_rule;
        t.feeder.removeClass("d-none");
        t.lister.addClass("d-none");
        t.feeder.field_id.val('').trigger('change');
        t.feeder.condition_code.val('').trigger('change');
        t.feeder.loaded_rule = {};
        if (t.feeder.value_label) {
            t.feeder.value_label.hide();
        }

        if (t.feeder.compare_values) {
            t.feeder.compare_values.hide();
        }
    };

    t.closeFeeder = function (e) {
        e.preventDefault();
        t.feeder.reset();
        t.feeder.addClass("d-none");
        t.lister.removeClass("d-none");
    }

    t.feeder.reset = function () {
        t.feeder.frm.trigger("reset");
        t.feeder.frmValidator.resetForm();
        t.feeder.field_id.val('').trigger('change');
        t.feeder.condition_code.val('').trigger('change');
        if (typeof t.feeder.compare_values != "undefined") {
            if (t.feeder.compare_values.is("select")) {
                t.feeder.compare_values.val("").trigger("change");
            }
            else if (t.feeder.compare_values.is("text")) {
                t.feeder.compare_values.val("").trigger("change");
            }
        }
    };

    t.feeder.handleSubmit = function (e) {
        e.preventDefault();

        if (t.httpCall != true) {
            return false;
        }

        if (t.feeder.frmValidator.form() == false) {
            return false;
        }

        var frmData = new FormData(t.feeder.frm[0]);
        frmData.append('_token', t.token.attr('content'));
        frmData.append('report_id', t.report_id);

        t.httpCall = false;
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
                    t.dataSet = data.dataSet;
                    t.lister.reload();
                    t.feeder.addClass("d-none");
                    t.lister.removeClass("d-none");
                    sweetAlert('center', 'success', data);
                    t.parent.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    t.lister.tblHelpers = {
        actions: function () {
            return function (d) {
                return `
                    <div class="user-list-actions role-list-actions justify-content-start">
                        <button class="user-list-action-btn btn dtActbtn act-rule-dictate" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="right" 
                                data-bs-original-title="Add to dictate box" 
                                data-id="${d.id}" 
                                data-rule_no="${d.rule_no}">
                            <i class="bi bi-pin"></i>
                        </button>
                        <button class="user-list-action-btn btn dtActbtn act-rule-edit" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="right" 
                                data-bs-original-title="Edit Rule" 
                                data-id="${d.id}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="user-list-action-btn btn dtActbtn act-rule-del" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="right" 
                                data-bs-original-title="Delete Rule" 
                                data-id="${d.id}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                `;
            }
        }
    };

    t.lister.dTbl = t.lister.table.DataTable({
        autoWidth: false,
        searching: false,
        lengthChange: false,
        scrollX: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0, 3, 4]
        }, {
            'targets': 0,
            'render': t.lister.tblHelpers.actions()
        }],
        order: [[1, 'desc']],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.get_rules,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.report_id = t.report_id;
            }
        },
        columns: [
            { data: 'a' },
            { data: 'a.rule_no' },
            { data: 'a.field_name' },
            { data: 'a.text_code' },
            { data: 'a.compare_values' }
        ],
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
        }
    });

    t.lister.reload = function () {
        t.lister.dTbl.ajax.reload();
    };

    t.lister.delete = function (e) {
        e.preventDefault();

        var rule_id = $(this).attr('data-id');

        if (t.httpCall != true) {
            return false;
        }

        t.httpCall = false;
        t.httpPostPath = t.config.url.delete_rule + "/" + rule_id;
        var http = $.ajax({
            url: t.httpPostPath,
            type: "GET"
        });
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.lister.reload();
                    t.feeder.addClass("d-none");
                    t.lister.removeClass("d-none");
                    sweetAlert('center', 'success', data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            let data = { msg: 'Something went wrong. Please check given details are correct' };
            sweetAlert('center', 'error', data);
        });
        http.always(function (data) {
            t.httpCall = true;
        });
    };

    var select2Opts = { width: "100%",dropdownParent: t.feeder.closest('.modal-content')  };
    t.feeder.field_id.select2(select2Opts);
    t.feeder.condition_code.select2(select2Opts);
    t.btn.add_rule.on("click", $.proxy(t.openFeeder));
    t.feeder.btnClear.on("click", $.proxy(t.closeFeeder));
    t.feeder.field_id.on("change", $.proxy(t.loadCondition));
    t.feeder.condition_code.on("change", $.proxy(t.loadValueBox));
    t.feeder.frm.on("submit", $.proxy(t.feeder.handleSubmit));

    t.lister.on("click", ".act-rule-del", $.proxy(t.lister.delete));
    t.lister.on("click", ".act-rule-edit", $.proxy(t.feeder.edit));
    t.lister.on("click", ".act-rule-dictate", $.proxy(t.add_logic));
    t.rule_dictation.on("sortchange", $.proxy(t.sort_change_detected));
    t.mdl.on("click", ".logic-adder", $.proxy(t.add_logic));
    t.mdl.on("click", ".logic-remover", $.proxy(t.remove_logic));
    t.rule_dictation.buttons_area.on("click", "#btnClear", $.proxy(t.rule_dictation_cancel));
    t.rule_dictation.buttons_area.on("click", "#btnSubmit", $.proxy(t.rule_dictation_save));
};

var MyApp = function (config) {
    var t = this;
    t.config = config;
    t.section = $("#customReportAdd");
    t.table = t.section.find("#mytable");
    t.httpCall = true;
    t.httpPostPath = "";
    t.filters = {
        wrapper: t.section.find("#advance-filters"),
    };
    t.filters.filter_by_status = t.filters.wrapper.find("#filter_by_status");
    t.filters.filter_by_report_type = t.filters.wrapper.find("#filter_by_report_type");
    t.searchbox = t.section.find(".searchbox");
    t.searchbtn = t.section.find(".btn-searchbox");
    t.reportList = t.section.find("#report-list");
    t.btn = {};
    t.isDataTableInitialized = false;

    t.initTooltips = function () {
        $('[data-bs-toggle="tooltip"]').each(function () {
            var tooltip = bootstrap.Tooltip.getInstance(this);
            if (tooltip) {
                tooltip.dispose();
            }
        });

        // Create new tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'top',
                container: 'body',
                boundary: 'window',
                delay: { show: 200, hide: 100 }
            });
        });
    };

    const reportTypeMapping = {
        1: 'Device',
        2: 'Ticket',
        3: 'Service Request',
        4: 'Change Management',
        5: 'Procurement'
    };

    t.tblHelpers = {
        status: function () {
            return function (d) {
                var active_icon = '<svg width="18" height="18" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 6px;"><rect width="16" height="16" rx="8" fill="#186B43"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
                var inactive_icon = '<svg width="18" height="18" viewBox="0 0 16 16" fill="none" style="vertical-align: middle; margin-right: 6px;"><rect width="16" height="16" rx="8" fill="#F12F35"/><circle cx="8" cy="8" r="3" fill="white"/></svg>';
                return d.status === 1
                    ? active_icon + "<span style='vertical-align: middle; font-weight: 500;'>Active</span>"
                    : inactive_icon + "<span style='vertical-align: middle; font-weight: 500;'>Inactive</span>";
            }
        },
        report_type: function () {
            return function (d) {
                return reportTypeMapping[d.report_type] ?? '';
            }
        },
        field_count: function () {
            return function (d) {
                return "<a href='#' class='field-count-btn' data-id='" + d.id + "' data-report-type='" + d.report_type + "'>" + d.field_count + "</a>";
            }
        },
        actions: function () {
            return function (d) {
                var id = (typeof d === 'object' && d !== null && d.id !== undefined) ? d.id : d;
                var buttons = '';
                if (jQuery.inArray("CustomReportEdit", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActEditTitle" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Report" data-id="${id}">
                            <svg viewBox="0 0 16 16" fill="none" width="14" height="14">
                                <path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z" fill="currentColor"></path>
                            </svg>
                        </button>
                    `;
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActEditFields" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Report Fields" data-id="${id}" data-report-type="${d.report_type}">
                            <svg viewBox="0 0 16 16" fill="none" width="14" height="14">
                                <path d="M4.33333 4.66667C4.33333 4.29848 4.63181 4 5 4H11C11.3682 4 11.6667 4.29848 11.6667 4.66667C11.6667 5.03486 11.3682 5.33333 11 5.33333H5C4.63181 5.33333 4.33333 5.03486 4.33333 4.66667ZM4.33333 8C4.33333 7.63181 4.63181 7.33333 5 7.33333H11C11.3682 7.33333 11.6667 7.63181 11.6667 8C11.6667 8.36819 11.3682 8.66667 11 8.66667H5C4.63181 8.66667 4.33333 8.36819 4.33333 8ZM5 10.6667C4.63181 10.6667 4.33333 10.9651 4.33333 11.3333C4.33333 11.7015 4.63181 12 5 12H11C11.3682 12 11.6667 11.7015 11.6667 11.3333C11.6667 10.9651 11.3682 10.6667 11 10.6667H5ZM13.772 2.228C13.9072 2.36323 13.9828 2.5465 13.9828 2.73733V13.2627C13.9828 13.4535 13.9072 13.6368 13.772 13.772C13.6368 13.9072 13.4535 13.9828 13.2627 13.9828H2.73733C2.5465 13.9828 2.36323 13.9072 2.228 13.772C2.09277 13.6368 2.0172 13.4535 2.0172 13.2627V2.73733C2.0172 2.5465 2.09277 2.36323 2.228 2.228C2.36323 2.09277 2.5465 2.0172 2.73733 2.0172H13.2627C13.4535 2.0172 13.6368 2.09277 13.772 2.228ZM14.9 1.1C14.3408 0.540843 13.5853 0.224 12.7975 0.224H3.2025C2.4147 0.224 1.65925 0.540843 1.1 1.1C0.540843 1.65925 0.224 2.4147 0.224 3.2025V12.7975C0.224 13.5853 0.540843 14.3408 1.1 14.9C1.65925 15.4592 2.4147 15.776 3.2025 15.776H12.7975C13.5853 15.776 14.3408 15.4592 14.9 14.9C15.4592 14.3408 15.776 13.5853 15.776 12.7975V3.2025C15.776 2.4147 15.4592 1.65925 14.9 1.1Z" fill="currentColor"/>
                            </svg>
                        </button>
                    `;
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActEditRules" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Report Rules" data-id="${id}" data-report-type="${d.report_type}" data-report-company="${d.report_type}">
                            <svg viewBox="0 0 18 18" fill="none" width="14" height="14">
                                <path d="M16.8375 8.36812C16.8681 8.23489 16.8712 8.09646 16.8468 7.962C16.8223 7.82754 16.7708 7.70021 16.6956 7.58775L15.3481 5.55325C15.2774 5.44842 15.1818 5.36233 15.0698 5.30239C14.9578 5.24245 14.833 5.21062 14.7063 5.2095C14.5795 5.20837 14.4542 5.23797 14.341 5.29587C14.2278 5.35376 14.1305 5.43804 14.0578 5.5415C13.7296 6.01237 13.3085 6.3964 12.8226 6.67168C12.3368 6.94697 11.7972 7.1078 11.2425 7.143C11.0691 7.15466 10.895 7.13491 10.7296 7.08468L5.68563 5.05325C5.50311 4.98818 5.33435 4.88767 5.18896 4.75675C5.04356 4.62583 4.92429 4.46688 4.83725 4.2885C4.77278 4.15517 4.75096 4.00526 4.77468 3.85896C4.7984 3.71265 4.86656 3.57643 4.97013 3.4695L6.03813 2.394C6.21352 2.21124 6.32475 1.97568 6.35561 1.72282C6.38647 1.46996 6.33527 1.21324 6.20963 0.992L4.81413 0.30375C4.67776 0.242741 4.53157 0.208904 4.38306 0.204C4.23456 0.199096 4.08659 0.223191 3.94663 0.274875L2.73963 0.69525C2.20204 0.884818 1.75709 1.27219 1.48384 1.77716C1.2106 2.28213 1.12627 2.86975 1.24525 3.4335C1.36424 3.99725 1.67912 4.49976 2.13266 4.85154C2.5862 5.20332 3.14733 5.37955 3.72063 5.347C3.87812 5.33684 4.03475 5.31112 4.18775 5.27013L9.29013 7.32225C9.525 7.41012 9.73088 7.56312 9.88388 7.76075C9.97442 7.87396 10.0399 8.00428 10.0765 8.14325C10.1132 8.28222 10.1202 8.42707 10.0973 8.56875C10.0743 8.71044 10.0217 8.84586 9.94288 8.96687C9.864 9.08789 9.76058 9.19191 9.63913 9.272C9.51768 9.35209 9.3805 9.40646 9.23663 9.4315C9.09275 9.45654 8.94517 9.45173 8.80325 9.41738L3.688 7.36125C3.24785 7.25473 2.78559 7.25518 2.34567 7.36258C1.90575 7.46998 1.50228 7.68091 1.17025 7.9765C0.83971 8.27285 0.59081 8.64487 0.44625 9.06075C0.30169 9.47663 0.265883 9.92339 0.342 10.3585C0.418118 10.7936 0.603711 11.203 0.88125 11.549C1.15879 11.8951 1.5199 12.1677 1.93213 12.343L7.16025 14.445C7.37172 14.5304 7.59687 14.5761 7.82488 14.5798C8.05288 14.5835 8.27951 14.5452 8.49375 14.4668C8.68111 14.3983 8.85393 14.2951 9.003 14.1625L9.77175 13.3865L9.75063 12.9215L9.63913 12.4015L8.62575 11.9L7.79525 11.843C7.50375 11.817 7.25738 11.6285 7.15 11.3628L6.666 10.3553L11.7915 12.4183C12.1769 12.5643 12.5934 12.6183 13.005 12.576C13.4166 12.5337 13.8115 12.3964 14.1533 12.176C14.495 11.9556 14.7748 11.6586 14.9685 11.3108L16.3086 9.27863C16.4282 9.08076 16.5005 8.85778 16.5202 8.62681C16.54 8.39583 16.5067 8.16298 16.4228 7.947L16.3069 7.67663C16.3147 7.76113 16.3187 7.846 16.3189 7.931C16.3191 8.07933 16.296 8.22668 16.2506 8.36725L16.8375 8.36812ZM17.502 5.3205L17.4323 5.05125C17.3707 4.75726 17.27 4.47308 17.1327 4.20575C16.9954 3.93842 16.8233 3.69067 16.6211 3.4695L15.6847 2.4335L15.8579 1.97625C16.0722 1.40105 16.0966 0.772337 15.9274 0.182687C15.7582 -0.406963 15.4038 -0.924718 14.9194 -1.297L9.62763 -4.459C9.24564 -4.71118 8.79773 -4.84468 8.338 -4.842C7.87827 -4.83932 7.43202 -4.70059 7.05325 -4.444L4.57563 -2.75975C4.15067 -2.44529 3.84321 -1.99806 3.6975 -1.489L3.5715 -1.045L5.77275 -0.0275L6.69063 1.46325L8.04213 1.68575C8.35146 1.73366 8.65391 1.8154 8.94363 1.9295C9.13808 2.00702 9.32021 2.11249 9.48475 2.243L13.3746 5.3265C13.6155 5.47475 13.8314 5.65875 14.0153 5.873L15.5436 7.6605L17.502 5.3205ZM0.58275 11.6163L0.5265 11.818C0.273688 12.7837 0.291075 13.7978 0.576515 14.7537C0.861954 15.7096 1.40488 16.5706 2.14313 17.24C2.88138 17.9094 3.78348 18.3611 4.759 18.5385L7.8265 18.899C9.23307 19.0585 10.653 18.7565 11.883 18.0385L13.9926 16.6723C14.4164 16.3802 14.7353 15.96 14.9063 15.4725L15.1613 14.726C14.9391 14.577 14.6517 14.293 14.4042 13.9833C14.1568 13.6735 13.9475 13.3203 13.7696 12.9445L12.2594 12.662L6.92525 14.9545C6.54253 15.202 6.09489 15.3315 5.6365 15.3257L4.87563 15.3155L3.9865 14.5533L2.79513 12.2553L0.58275 11.6163Z" fill="currentColor"/>
                            </svg>
                        </button>
                    `;
                }
                if (jQuery.inArray("CustomReportDelete", t.config.permissions) !== -1) {
                    buttons += `
                        <button class="user-list-action-btn me-1 dtActDelReport" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Report" data-id="${id}">
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor">
                                <path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/>
                            </svg>
                        </button>
                    `;
                }
                return '<div class="dt-actions btn-group " role="group">' + buttons + '</div>';
            };
        }
    };

    // Check if DataTable already exists and destroy it
    if ($.fn.DataTable.isDataTable('#mytable')) {
        $('#mytable').DataTable().destroy();
        $('#mytable').empty();
    }

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        searching: false,
        lengthChange: false,
        scrollX: true,
        scrollCollapse:true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1,
        },
        aoColumnDefs: [
            {
                'bSortable': true,
                'aTargets': [0, 5]
            },
            {
                'bSortable': false,
                'aTargets': [6]
            },
            {
                'targets': 2,
                'render': t.tblHelpers.status()
            },
            {
                'targets': 3,
                'render': t.tblHelpers.report_type()
            },
            {
                'targets': 4,
                'render': t.tblHelpers.field_count()
            },
            {
                targets: 6,
                className: "role-col-actions app-table-col-actions app-table-col-actions--wide",
                render: t.tblHelpers.actions()
            }
        ],
        order: [[5, 'desc']],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.get_reports,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.search = t.config.search || '';
                d.other_filters = t.config.other_filters || {};
                d.length = t.config.length || 10;
            }
        },
        columns: [
            { data: 'a.company_name' },
            { data: 'a.report_name' },
            { data: 'a' },
            { data: 'a' },
            { data: 'a' },
            { data: 'a.updated_at_format' },
            { data: 'a' },
        ],
        createdRow: function(row, data, dataIndex) {
            $(row).find('td').addClass('b5-text');
        },
        fnInitComplete: function (oSettings, json) {
            var api = this.api();
            t.table.parent().addClass('table-responsive');
            t.initTooltips();
            t.isDataTableInitialized = true;
        },
        fnDrawCallback: function (oSettings) {
            t.initTooltips();
        }
    });

    t.section.off('keyup', '.searchbox');
    t.section.off('click', '.btn-searchbox');

    t.section.on('keyup', '.searchbox', function (e) {
        if (e.keyCode === 13) {
            var searchValue = $.trim($(this).val());
            t.config.search = searchValue;
            t.reload();
            e.preventDefault();
            return false;
        }
    });

    t.section.on('click', '.btn-searchbox', function (e) {
        var searchValue = $.trim($('.searchbox').val());
        t.config.search = searchValue;
        t.reload();
        e.preventDefault();
    });

    t.reload = function () {
        if (t.isDataTableInitialized && t.dTbl) {
            t.dTbl.ajax.reload(function () {
                t.initTooltips();
            });
        }
    };

    t.table.on('click', '.field-count-btn', function (e) {
        var id = $(this).attr("data-id");
        var reportType = $(this).attr("data-report-type");
        t.fieldMdl.fetchFields(id, reportType);
    });

    t.reportList.on('change', function () {
        t.config.length = $(this).val();
        t.reload();
    });

    t.deleteReport = function (e) {
        e.preventDefault();

        var report_id = $(this).attr('data-id');

        if (t.httpCall != true) {
            return false;
        }

        sweetAlertConfirmation({
            message: config.translations.are_you_delete || 'Are you sure you want to delete this report?',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            onConfirm: function () {
                t.httpCall = false;
                t.httpPostPath = t.config.url.delete_report + "/" + report_id;

                var http = $.ajax({
                    url: t.httpPostPath,
                    type: "GET"
                });

                http.done(function (data) {
                    if (typeof data == "object") {
                        if (data.status == "success") {
                            sweetAlert('center', 'success', data);
                            t.reload();
                        }
                        else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                });

                http.fail(function () {
                    let data = { msg: 'Something went wrong. Please check given details are correct' };
                    sweetAlert('center', 'error', data);
                });

                http.always(function (data) {
                    t.httpCall = true;
                });
            }
        });
    };

    t.openFilter = function (e) {
        if (t.section.find('.advance-filters').hasClass('collapse')) {
            t.section.find('.advance-filters').slideDown(300).removeClass('collapse');
        } else {
            t.section.find('.advance-filters').slideUp(300).addClass('collapse');
        }
    }

    t.cache_filter_values = function () {
        var v = $.trim($("#mytable_wrapper .searchbox").val());
        t.config.search = v;
        t.config.other_filters = {};
        if (t.filters.filter_by_status.val() && t.filters.filter_by_status.val() != 'null') {
            t.config.other_filters.status = t.filters.filter_by_status.val();
        }
        if (t.filters.filter_by_report_type.val() && t.filters.filter_by_report_type.val() != 'null') {
            t.config.other_filters.report_type = t.filters.filter_by_report_type.val();
        }
        filterCount(t.config.other_filters, true);
    };

    t.search = function (e) {

        var target = e.target || e.currentTarget;

        if (
            e.type === 'keyup' &&
            (e.key === 'Enter' || e.keyCode === 13)
        ) {

            t.config.search = $.trim($(target).val());
            t.reload();
        }

        if ($(target).closest('.btn-searchbox').length) {

            t.config.search = $.trim($('.searchbox').val());
            t.reload();
        }
    };


    $('#btnFilter').on('click', function () {
        t.section.find('.advance-filters').slideUp(300).addClass('collapse');
    });

    t.btnClrFilter = function () {
        t.filters.filter_by_status.val('').trigger("change");
        t.filters.filter_by_report_type.val('').trigger("change");
        t.cache_filter_values();
        resetFilterCount();
        t.section.find('.advance-filters').slideUp(300).addClass('collapse');
        t.reload();
    }

    t.section.on("click", ".btn-reload-list", $.proxy(t.reload));

    t.titleMdl = new TitleMdl(config, t);
    t.fieldMdl = new FieldMdl(config, t);
    t.ruleMdl = new RuleMdl(config, t);

    var select2Opts = {
        width: "100%"
    };
    t.filters.filter_by_status.select2($.extend({}, select2Opts, { allowClear: true, placeholder: "Filter By Status" }));
    t.filters.filter_by_report_type.select2($.extend({}, select2Opts, { allowClear: true, placeholder: "Filter By Report Type" }));

    t.section.on('keyup', '.searchbox', $.proxy(t.search));
    t.section.on('click', '.btn-searchbox', $.proxy(t.search));
    t.section.on('click', '#btnClrFilter', $.proxy(t.btnClrFilter));
    t.section.on('click', '.btn-visible-content', $.proxy(t.openFilter));
    t.section.on("click", "#act-add-report", $.proxy(t.titleMdl.addTitle));
    t.table.on("click", ".dtActEditTitle", $.proxy(t.titleMdl.editTitle));
    t.table.on("click", ".dtActEditFields", $.proxy(t.fieldMdl.editField));
    t.table.on("click", ".dtActEditRules", $.proxy(t.ruleMdl.editRules));
    t.table.on("click", ".dtActDelReport", $.proxy(t.deleteReport));
};