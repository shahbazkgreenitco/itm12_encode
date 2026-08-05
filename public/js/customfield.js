var CustomField = function(config) {
    this.config = config;
    var preDefinedOptionsArray = {
        1: this.config.url.getQueryLocation,
        2: this.config.url.getQueryUser,
        3: this.config.url.getByQueryDevice,
        4: this.config.url.getQueryPlace,
        5: this.config.url.getQueryManufacture,
        6: this.config.url.getQueryModel,
        7: this.config.url.getQueryComponent,
        8: this.config.url.getQueryTicket,
        9: this.config.url.getQueryTicketProcureRequest,
        10: this.config.url.getQueryRecord,
        11: this.config.url.getQueryTask,
        12: this.config.url.getQueryLicense,
        13: this.config.url.getQueryProject,
        14: this.config.url.getQueryPurchase,
        15: this.config.url.getQuerySupplier,
        16: this.config.url.getQueryContract,
        17: this.config.url.getQueryEsms,
        18: this.config.url.getQueryDepartment,
    };

    this.displayCustomField = function(getDepartmentCustomFields,type,problem_category_id,device_id,pc = null,sc=null) {
        var enableUsbRequest = {};
        if (getDepartmentCustomFields != null) {
            var requestData = {};
            if (pc !== null) {
                requestData.pc_id = pc;
            }
            if (sc !== null) {
                requestData.sc_id = sc;
            }
            $.ajax({
                method: 'get',
                url: getDepartmentCustomFields,  
                data: requestData, 
                success: function(data) {

                    let departmentWrapper = ".departmentCustomFieldWrapper";
                    let categoryWrapper = ".categoryCustomFieldWrapper";
                    if (type == "TicketByNotPrivUsers") {
                        departmentWrapper = ".departmentCustomFieldWrapperNonPvlg";
                    } else if (type == "kanban") {
                        departmentWrapper = ".kanbanCustomFieldWrapper";
                    }
                    $(departmentWrapper).html("");
                    $(categoryWrapper).html("");
                    if (typeof data.enableUsbRequests !== "undefined") {
                        enableUsbRequest = data.enableUsbRequests;
                        localStorage.setItem('enableUsbRequest',data.enableUsbRequests);
                    } else {
                        localStorage.setItem('enableUsbRequest', 0);
                    }

                    function renderFields(fields, wrapper) {
                        if (!fields || fields.length === 0) {
                            return;
                        }

                        fields.forEach(function(v) {
                            var mandatory = v.required === 1 ? "mandatory" : "";
                            var fieldName = 'fields[' + v.name + ']';
                            var html = `
                                <div class="row align-items-center mb-3 amg-form-field-row custom-field-row">
                                    <div class="col-md-3 text-start text-md-end">
                                        <label class="form-label mb-0 ${mandatory}">
                                            ${v.label}
                                        </label>
                                    </div>

                                    <div class="col-md-9">
                                        <div class="input-group ticket-input-group">
                            `;

                            if (v.element !== 'radio' && v.element !== 'checkbox') {
                                html += `
                                    <span class="input-group-text">
                                        <i class="bi bi-tag"></i>
                                    </span>
                                `;
                            }

                            // TEXT
                            if (v.element === 'text') {
                                html += `
                                    <input type="text"
                                        name="${fieldName}"
                                        placeholder="Enter ${v.label}"
                                        class="form-control filter-input fields_${v.name}">
                                `;
                            }

                            // DATE
                            else if (v.element === 'date') {

                                html += `
                                    <input type="date"
                                        name="${fieldName}"
                                        class="form-control filter-input fields_${v.name}">
                                `;
                            }

                            // TIME
                            else if (v.element === 'time') {
                                html += `
                                    <input type="time"
                                        name="${fieldName}"
                                        class="form-control filter-input fields_${v.name}">
                                `;
                            }

                            // DATETIME
                            else if (v.element === 'datetime') {
                                html += `
                                    <input type="datetime-local"
                                        name="${fieldName}"
                                        class="form-control filter-input fields_${v.name}">
                                `;
                            }

                            // DROPDOWN
                            else if (v.element === 'dropdown') {
                                html += `
                                    <select
                                        name="${fieldName}"
                                        class="form-select filter-input fields_${v.name}">
                                `;

                                html += `
                                    <option value="">
                                        Select ${v.label}
                                    </option>
                                `;
                                if (v.custom_options != null) {
                                    if (v.option_type === 2) {
                                        v.custom_options.forEach(function(d) {
                                            html += `
                                                <option value="${d}">
                                                    ${d}
                                                </option>
                                            `;
                                        });
                                    } else if (v.option_type === 1) {
                                        html += `
                                            <option value="${v.custom_options}">
                                                ${v.custom_options}
                                            </option>
                                        `;
                                    }
                                }
                                html += `</select>`;
                            }

                            // RADIO
                            else if (v.element === 'radio') {
                                html += `
                                <div class="custom-option-group">
                                    <div class="d-flex flex-wrap gap-3 align-items-center pt-2">
                                `;

                                if (v.custom_label != null && v.option_type === 2) {
                                    let labels = JSON.parse(v.custom_label);
                                    labels.forEach(function(d, index) {

                                        html += `
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input fields_${v.name}"
                                                    type="radio"
                                                    name="${fieldName}"
                                                    id="${v.name}_${index}"
                                                    value="${d}">

                                                <label
                                                    class="form-check-label"
                                                    for="${v.name}_${index}">
                                                    ${d}
                                                </label>
                                            </div>
                                        `;
                                    });
                                }

                                html += `</div></div>`;
                            }

                            // CHECKBOX
                            else if (v.element === 'checkbox') {

                                html += `
                                <div class="custom-option-group">
                                    <div class="d-flex flex-wrap gap-3 align-items-center pt-2">
                                `;

                                if (v.custom_label != null &&v.option_type === 2) {
                                    let labels = JSON.parse(v.custom_label);
                                    labels.forEach(function(d, index) {
                                        html += `
                                            <div class="form-check">
                                                <input
                                                    class="form-check-input fields_${v.name}"
                                                    type="checkbox"
                                                    name="${fieldName}[]"
                                                    id="${v.name}_${index}"
                                                    value="${d}">

                                                <label
                                                    class="form-check-label"
                                                    for="${v.name}_${index}">
                                                    ${d}
                                                </label>
                                            </div>
                                        `;
                                    });
                                }

                                html += `</div></div>`;
                            }

                            // DEFAULT
                            else {
                                html += `
                                    <input type="text"
                                        name="${fieldName}"
                                        placeholder="Enter ${v.label}"
                                        class="form-control filter-input fields_${v.name}">
                                `;
                            }
                            // HELP NOTE
                            if (v.help_note != null) {
                                html += `
                                    <span class="input-group-text custom_field_info"
                                        data-bs-toggle="tooltip"
                                        title="Help Note"
                                        data-id="${v.id}">
                                        <i class="bi bi-info-circle"></i>
                                    </span>
                                `;
                            }

                            html += `
                                        </div>
                                    </div>
                                </div>
                            `;
                            $(wrapper).append(html);
                            // Validation
                           if (mandatory === "mandatory") {
                                if (v.element === 'radio') {
                                    $('[name="' + fieldName + '"]').rules('add', {
                                        required: true,
                                        messages: {
                                            required: 'Please select an option'
                                        }
                                    });
                                } else if (v.element === 'checkbox') {
                                    $('[name="' + fieldName + '[]"]').first().rules('add', {
                                        required: true,
                                        minlength: 1,
                                        messages: {
                                            required: 'Please select at least one option'
                                        }
                                    });

                                } else {
                                    $('[name="' + fieldName + '"]').rules('add', {
                                        required: true
                                    });
                                }
                            }
                            // Select2
                            if ( v.element === 'dropdown' && v.option_type != null) {
                                let selector = $('[name="' + fieldName + '"]');
                                if (v.preDefinedOptions == 17) {
                                    selector.select2({
                                        allowClear: true,
                                        width: '100%',
                                        dropdownParent: selector.parent(),
                                        ajax: {
                                            url: "http://164.52.201.124:8083/post_view_call_text",
                                            method: "POST",
                                            timeout: 0,
                                            headers: {
                                                "Content-Type": "application/json"
                                            },
                                            data: function() {
                                                return "call esms_n.s_aa_process_at_sw_list()";
                                            },
                                            processResults: function(data) {
                                                if (data.status === "pass" &&Array.isArray(data.message[0])
                                                ) {
                                                    return {
                                                        results: data.message[0].map(function(item) {
                                                            return {
                                                                text: item.sw,
                                                                id: item.sw
                                                            };
                                                        })
                                                    };
                                                }
                                                return {
                                                    results: []
                                                };
                                            },
                                            delay: 300
                                        },
                                        placeholder: 'Select ' + v.label,
                                    });
                                } else if (v.preDefinedOptions != null && v.preDefinedOptions != 0) {
                                    selector.select2({
                                        allowClear: true,
                                        width: '100%',
                                        dropdownParent: selector.parent(),
                                        ajax: {
                                            url: preDefinedOptionsArray[v.preDefinedOptions],
                                            dataType: "json",
                                            data: function(p) {
                                                console.log($('#company_id').val());
                                                return {
                                                    search: p.term,
                                                    page: p.page || 1,
                                                    company_id : $('#company_id').val(),
                                                };
                                            },
                                            processResults: function(data) {
                                                const responseData = data.data || data.results || [];
                                                return {
                                                    results: responseData.map(function(item) {
                                                        const record = item.a || item;
                                                        // return {
                                                        //     text: item['a'].text,
                                                        //     id: item['a'].text,
                                                        // };
                                                        return {
                                                            text: record.text,
                                                            id: record.text
                                                        };
                                                    })
                                                };
                                            },
                                            delay: 300
                                        },
                                        placeholder: 'Select ' + v.label,
                                    });
                                } else {
                                    selector.select2({width: '100%',dropdownParent: selector.parent()});
                                }
                            }
                        });
                    }
                    // Department Fields
                    renderFields(data.data, departmentWrapper);

                    // Problem Category Fields
                    renderFields( data.problem_category_fields,categoryWrapper);

                    // Renewal Readonly
                    if (localStorage.getItem('renewal_readyonly') == 1) {
                        config.customFieldsFromTableForDepartments.forEach(function(v, i) {
                            if (v.value != null) {
                                $('[name="fields[' + i + ']"]')
                                    .val(v.value)
                                    .attr("readonly", true);
                            }
                        });

                        if (config.client === "ltts" &&config.device != '') {
                            var problemCategoryId = config.pcs_name.id;
                            $("#deviceIdCover").addClass("hide");
                            if (problemCategoryId > 0 &&!isNaN(problemCategoryId) && $.inArray(problemCategoryId,enableUsbRequest) !== -1) {
                                $("#deviceIdCover").removeClass("hide");
                                setTimeout(function() {
                                    device_id.append(
                                        $('<option>')
                                            .val(config.device.id)
                                            .text(config.device.asset_tag)
                                    );

                                }, 1500);
                            }
                        }
                    }
                },
                fail: function(data) {
                    console.log(data);
                }
            });
        }
    };
};