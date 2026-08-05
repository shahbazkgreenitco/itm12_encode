var Holiday = function (config) {
    let defaultCompany = localStorage.getItem('Default_Company');
    let companyId = config.company_user_detail ? config.company_user_detail.dashboard_company_id : null;    
    var isEdit = false;
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.calendar = t.content.find('#dd');
    t.mdl = t.content.find('#holidaysMdl');
    t.mdltitle = t.mdl.find('.modal-title');
    t.loader = t.mdl.find('#loader_img');
    t.frm = t.mdl.find('#HolidaysForm');

    t.frmEl = {};
    t.frmEl.date_name = t.frm.find('#date_name');
    t.frmEl.name = t.frm.find('#name');
    t.frmEl.location_id = t.frm.find("#location_id");
    t.frmEl.alllocations_id = t.frm.find("#alllocations_id");
    t.frmEl.location_country_id = t.frm.find("#location_country_id");
    t.frmEl.company_id = t.frm.find('#company_id');
    t.resetFrm = {};
    t.btn = {};
    t.btn.submit = t.frm.find('#btnSubmit');
    t.btn.update = t.frm.find('#btnupdate');
    t.btn.clear = t.frm.find('#btnClear');

    t.descriptonMdl = t.content.find("#descriptionModal");
    t.descriptonMdl.title = t.descriptonMdl.find(".modal-title")
    t.descriptonMdl.body = t.descriptonMdl.find(".modal-body");

    t.searchbox = t.content.find(".searchbox");

    t.data = {
        countries: null,
    };
    /* make table as dataTable */
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        scrollX: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        colResize: {
            resizeTable: true
        },
        responsive: true,
        stateSave: true,
        stateSaveParams: function (settings, data) { 
            data.search.search = '';
        },
        aoColumnDefs: [
            {
                targets: 8,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {

                    let buttons = '';

                    if (jQuery.inArray("HolidaysEdit", t.config.permissions) !== -1) {
                        buttons += `                           
                            <button class="user-list-action-btn open-edit-modal me-1" data-toggle="tooltip" data-placement="top" title="Edit" data-id="${row.a.id}">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M15.2586 3.85762L11.768 0.366217C11.6519 0.250114 11.5141 0.158014 11.3624 0.0951779C11.2107 0.0323417 11.0482 0 10.884 0C10.7198 0 10.5572 0.0323417 10.4056 0.0951779C10.2539 0.158014 10.1161 0.250114 10 0.366217L0.366412 10.0006C0.249834 10.1162 0.157407 10.2539 0.0945056 10.4056C0.0316038 10.5573 -0.000518312 10.72 6.32418e-06 10.8842V14.3756C6.32418e-06 14.7071 0.131702 15.0251 0.366123 15.2595C0.600543 15.4939 0.918486 15.6256 1.25001 15.6256H4.74141C4.90563 15.6261 5.0683 15.594 5.21999 15.5311C5.37168 15.4682 5.50935 15.3758 5.62501 15.2592L15.2586 5.62559C15.3747 5.50952 15.4668 5.3717 15.5296 5.22003C15.5925 5.06835 15.6248 4.90578 15.6248 4.74161C15.6248 4.57743 15.5925 4.41486 15.5296 4.26319C15.4668 4.11151 15.3747 3.9737 15.2586 3.85762ZM4.74141 14.3756H1.25001V10.8842L8.12501 4.00919L11.6164 7.50059L4.74141 14.3756ZM12.5 6.61622L9.0086 3.12559L10.8836 1.25059L14.375 4.74122L12.5 6.61622Z"/></svg>
                            </button>
                        `;
                    }                    
                    if (jQuery.inArray("HolidaysDelete", t.config.permissions) !== -1) {
                        buttons += `
                            <button class="user-list-action-btn open-delete me-1" data-toggle="tooltip" data-placement="top" title="Delete" data-id="${row.a.id}" >
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="currentColor"><path d="M14.375 2.5H11.25V1.875C11.25 1.37772 11.0525 0.900806 10.7008 0.549175C10.3492 0.197544 9.87228 0 9.375 0H5.625C5.12772 0 4.65081 0.197544 4.29917 0.549175C3.94754 0.900806 3.75 1.37772 3.75 1.875V2.5H0.625C0.45924 2.5 0.300269 2.56585 0.183058 2.68306C0.0658481 2.80027 0 2.95924 0 3.125C0 3.29076 0.0658481 3.44973 0.183058 3.56694C0.300269 3.68415 0.45924 3.75 0.625 3.75H1.25V15C1.25 15.3315 1.3817 15.6495 1.61612 15.8839C1.85054 16.1183 2.16848 16.25 2.5 16.25H12.5C12.8315 16.25 13.1495 16.1183 13.3839 15.8839C13.6183 15.6495 13.75 15.3315 13.75 15V3.75H14.375C14.5408 3.75 14.6997 3.68415 14.8169 3.56694C14.9342 3.44973 15 3.29076 15 3.125C15 2.95924 14.9342 2.80027 14.8169 2.68306C14.6997 2.56585 14.5408 2.5 14.375 2.5ZM5 1.875C5 1.70924 5.06585 1.55027 5.18306 1.43306C5.30027 1.31585 5.45924 1.25 5.625 1.25H9.375C9.54076 1.25 9.69973 1.31585 9.81694 1.43306C9.93415 1.55027 10 1.70924 10 1.875V2.5H5V1.875ZM12.5 15H2.5V3.75H12.5V15ZM6.25 6.875V11.875C6.25 12.0408 6.18415 12.1997 6.06694 12.3169C5.94973 12.4342 5.79076 12.5 5.625 12.5C5.45924 12.5 5.30027 12.4342 5.18306 12.3169C5.06585 12.1997 5 12.0408 5 11.875V6.875C5 6.70924 5.06585 6.55027 5.18306 6.43306C5.30027 6.31585 5.45924 6.25 5.625 6.25C5.79076 6.25 5.94973 6.31585 6.06694 6.43306C6.18415 6.55027 6.25 6.70924 6.25 6.875ZM10 6.875V11.875C10 12.0408 9.93415 12.1997 9.81694 12.3169C9.69973 12.4342 9.54076 12.5 9.375 12.5C9.20924 12.5 9.05027 12.4342 8.93306 12.3169C8.81585 12.1997 8.75 12.0408 8.75 11.875V6.875C8.75 6.70924 8.81585 6.55027 8.93306 6.43306C9.05027 6.31585 9.20924 6.25 9.375 6.25C9.54076 6.25 9.69973 6.31585 9.81694 6.43306C9.93415 6.55027 10 6.70924 10 6.875Z"/></svg>
                            </button>
                        `;
                    }

                    return buttons;
                }
            }
        ],
        order: [
            [7, 'desc']
        ],
        deferLoading: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.url.holidays,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.company_id = companyId;

                if (d.order && d.order.length > 0) {
                    let orderInfo = d.order[0];
                    let columnIndex = orderInfo.column;
                    let direction = orderInfo.dir;

                    let columnName = d.columns[columnIndex].name; 
                    d.sorted_column_name = columnName;
                    d.sorted_direction = direction;
                }
            }
        },
        fixedColumns: {
           rightColumns: 1 
        },
        columns: [
            { data: 'a.company_name', name: 'company_name', className: "amg-table-col-188" },
            { data: 'a.name', name: 'name',
                className: "amg-table-col-264",
                render: function(data,type,row){
                    if(!data) return '';
                    data = String(data);
                    if(data.length > 20){
                        var truncated = truncateHtml(data, 50);
                        return '<div data-toggle="tooltip" data-original-title="' + data + '">' +
                            truncated + '...' +
                        '</div>';
                    }
                    return data;
                }
            },
            { data: 'a.date_name', name: 'date_name',className: "amg-table-col-176" },
            {
                data: 'a.location_name',
                className: "amg-table-col-144",
                render: function(data) {
                    if (!data) return '-';

                    let arr = data.split(',').map(x => x.trim());

                    if (arr.length > 3) {
                       return `
                            <button type="button" class="view-details btn btn-link p-0 text-decoration-none"
                                data-values='${JSON.stringify(arr)}'
                                data-column="location">

                                ${arr.slice(0, 3).map(x => `<span class="role-badge role-badge-user me-1">${x}</span>`).join('')}
                                +${arr.length - 3}
                            </button>
                        `;                                                                  
                    } else {
                        return arr.map(x => `<span class="role-badge role-badge-user me-1">${x}</span>`).join('');
                    }
                }
            },
            {
                data: 'a.countries_name',
                className: "amg-table-col-144",
                render: function(data) {
                    if (!data) return '-';

                    let arr = data.split(',').map(x => x.trim());

                    if (arr.length > 3) {
                        return `
                            <button type="button" class="view-details btn btn-link p-0 text-decoration-none"
                                data-values='${JSON.stringify(arr)}'
                                data-column="location">

                                ${arr.slice(0, 3).map(x => `<span class="role-badge role-badge-admin me-1">${x}</span>`).join('')}
                                +${arr.length - 3}
                            </button>
                        `;
                    } else {
                        return arr.map(x => `<span class="role-badge role-badge-admin me-1">${x}</span>`).join('');
                    }
                }
            },
            { 
                data: 'a.created_by',
                className: "amg-table-col-188", 
                name: 'created_by',
                render: function(data, type, row){

                    if(type !== 'display'){
                        return data || '-';
                    }

                    let creatorName = data || '-';
                    let imageUrl = row.a.profile_img || row.a.created_by_gravatar || '';

                    function getUserInitials(name) {
                        let words = String(name || '').split(/\s+/).filter(Boolean);

                        if (!words.length) return "NA";

                        if (words.length === 1) {
                            return words[0].substring(0, 2).toUpperCase();
                        }

                        return (
                            words[0].charAt(0) +
                            words[1].charAt(0)
                        ).toUpperCase();
                    }

                    function getAvatarHtml(name, imageUrl) {

                        if (imageUrl) {
                            return `
                                <img 
                                    src="${imageUrl}" 
                                    alt="${name}" 
                                    class="user-list-avatar user-list-avatar--sm"
                                >
                            `;
                        }

                        return `
                            <span class="user-list-avatar user-list-avatar--sm user-list-avatar-fallback">
                                ${getUserInitials(name)}
                            </span>
                        `;
                    }

                    return `
                        <div class="d-flex align-items-start gap-3 w-100 user-list-person--compact">
                            ${getAvatarHtml(creatorName, imageUrl)}
                            
                            <div class="d-flex flex-column w-100 gap-1 min-w-0">
                                <span class="b1-text" title="${creatorName}">
                                    ${creatorName}
                                </span>
                            </div>
                        </div>
                    `;
                }
            },
            { data: 'a.formatted_created_at', name: 'created_at',className: "amg-table-col-176" },
            { data: 'a.last_updated_at', name: 'updated_at',className: "amg-table-col-176" }, 
            { 
                data: 'a', 
                orderable: false, 
                searchable: false,  
                className: "amg-table-col-144",
            }
        ],
        fnInitComplete: function () {
            var api = this.api();
            $('#mytable_length').hide();
            $('#holiday-list-search').on('keyup', function (e) {
                if (e.key === 'Enter') {
                    let value = $(this).val().trim();
                    api.search(value).draw();
                }
            });
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                let length = parseInt($(this).val());
                api.page.len(length).draw();
            });

            function updateColumnVisibilityControls() {
                let controls = $("#columnVisibilityControls").empty();
            
                api.columns().every(function () {
                    let columnIndex = this.index();
                    let columnTitle = $(this.header()).text().trim();
                    let columnId = `column-toggle-${columnIndex}`;
            
                    controls.append(`
                        <div class="dropdown-item" style="padding:6px">
                            <label for="${columnId}">
                                <input type="checkbox" id="${columnId}" data-column="${columnIndex}" ${this.visible() ? 'checked' : ''}> ${columnTitle}
                            </label>
                        </div>
                    `);
                });
            }
            t.content.on("click", ".location-read-more, .country-read-more", function (e) {
                e.preventDefault();
                let text = $(this).data("full-text");
                let title = $(this).hasClass("location-read-more") ? "Location Names" : "Location Countries";
                showReadMoreModal(title, text);
            });
            updateColumnVisibilityControls();
            $('#columnVisibilityControls').on('change', 'input[type="checkbox"]', function () {
                api.column(+$(this).data('column')).visible(this.checked);
            });
            api.on('column-reorder', () => setTimeout(updateColumnVisibilityControls, 10));
        }

    });

    t.cache_filter_values = function() {

        let v = $("#holiday-list-search").val().trim();

        // validation
        if (!v || v.length < 2) {
            t.config.search = "";
        } else {
            t.config.search = v;    
        }

        let jobj = { search: t.config.search };

        t.config.export_filters = btoa(JSON.stringify(jobj));
    };

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    function showReadMoreModal(title, text) {
        // set content
        $('#descriptionMdl .modal-title').text(title);
        $('#modalBodyContent').html(text);

        // open modal (Bootstrap 5 way)
        let modalEl = document.getElementById('descriptionMdl');
        let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    function truncateHtml(html, maxLength) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var text = div.textContent || div.innerText || "";
        if (text.length <= maxLength) {
            return html;
        }
        return text.substring(0, maxLength);
    }

    t.search = function (e) {
        var target = e.target || e.currentTarget;

        if (e.keyCode === 13) {
            var input = $("#holiday-list-search");
            var v = input.val().trim();
            if (e.keyCode == 13 || this.value.length == 0) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(this.value).draw();
                }
            t.config.search = v;
            t.reload();
        } 
        else if (target.tagName === "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.tableSearch = function(e) {
        e.preventDefault();
        var v = $("#mytable_wrapper .plain-search").validate_str_param();
        
		t.dTbl.search(v).draw();
	};

    t.createHoliday = function (e) {
        e.preventDefault();
        if (t.frmEl.date_name[0]._flatpickr) {
            t.frmEl.date_name[0]._flatpickr.destroy();
        }
        t.frmEl.date_name.flatpickr({
            dateFormat: "d/m/Y",
            altInput: true,
            altFormat: "d M Y",
            minDate: "today",
            mode: "single"
        });

        if (t.frmEl.date_name[0]._flatpickr) {
            t.frmEl.date_name[0]._flatpickr.clear();
        }

        t.loader.hide();
        t.frmEl.name.val('');
        t.frmEl.date_name.val('');
        t.frmEl.location_id.val('').trigger("change");
        t.frmEl.location_country_id.val('').trigger("change");

        $('label[for="company_id"]').removeClass('mandatory');

        isEdit = false;
        t.mdl.modal("show");
        t.mdltitle.text(t.config.translations.add_holiday);
        t.btn.submit.text(t.config.translations.save);
        t.httpPostPath = t.config.url.add;

        t.resetFrm();

        if (Array.isArray(config.company_defulte) && config.company_defulte.length === 1 && config.company_defulte[0].id) {
            let option = new Option(config.company_defulte[0].text, config.company_defulte[0].id, true, true);
            t.frmEl.company_id.append(option).trigger('change');
        }
    };
    t.handlesubmit = function (e) {
        e.preventDefault();
        if (t.frmValidator.form() == false) {
            return false;
        }
        var frmData = new FormData();
        frmData.append('_token', t.config.token);
        frmData.append('date_name', t.frmEl.date_name.val());
        frmData.append('name', t.frmEl.name.val());
        frmData.append('company_id', t.frmEl.company_id.val());
        if(t.frmEl.location_id.val() != null) {
            frmData.append('location_id', t.frmEl.location_id.val());
        } else {
            frmData.append('location_id', '');
        }
        if (t.frmEl.location_country_id.val() != null) {
            frmData.append("location_country_id", t.frmEl.location_country_id.val());
        } else {
            frmData.append("location_country_id", "");
        }
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            processData: false,
            contentType: false,
            data: frmData,
            beforeSend: function() {
                t.btn.submit.prop('disabled', true); // disable button
            },
        });

        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("hide");
                    sweetAlert('center', 'success', data);
                    t.dTbl.ajax.reload();
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.loader.hide();
            t.btn.submit.prop('disabled', false);
        });
    };

    t.loadForm = function (obj) {
        t.resetFrm();
        isEdit = true;
        t.frmEl.name.val(obj.name);
        if (t.frmEl.date_name[0]._flatpickr) {
            t.frmEl.date_name[0]._flatpickr.destroy();
        }
        t.frmEl.date_name.val('');                
        t.frmEl.date_name.flatpickr({
            dateFormat: "d/m/Y",
            minDate: "today",
            mode: "single" 
        });
        if (obj.date_name && obj.date_name.length > 0) {
            const dateStr = obj.date_name; 
            t.frmEl.date_name.val(dateStr);
        }
        $.each(obj.country_ids, function (key, value) {
            t.frmEl.location_country_id.select2("trigger", "select", {
                data: { text: value.name, id: value.id, selected: true },
            });
        });
        $.each(obj.location_ids, function( key, value ) {
            t.frmEl.location_id.select2('trigger', 'select', {
                data: {text: value.name, id: value.id, selected: true}
            });
        });
        if (obj.company_id != "" && obj.company_id != null) {
            t.frmEl.company_id.val(obj.company_id).empty();
            t.frmEl.company_id.append(new Option(obj.company_id.text, obj.company_id.id, true,true));
        } else {
            $('label[for="company_id"]').addClass('mandatory');
        }
    };

    t.resetFrm = function () {
        t.frmEl.name.val('');
        t.frmEl.date_name.val('');
        // t.frmEl.location_id.val("").trigger("change");
        t.frmEl.location_id.val("").trigger("change");
        t.frmEl.location_country_id.val("").trigger("change");
        t.frmEl.company_id.val('').trigger("change");
        t.frmEl.alllocations_id.prop("checked", false);
        t.frmValidator.resetForm();
    };

    t.editHoliday = function (e) {
        e.preventDefault();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.edit + "/" + projectId;
        var http = $.get(t.config.url.get + "/" + projectId);
        http.done(function (data) {
            if (typeof data == "object") {
                if (data.status == "success") {
                    t.mdl.modal("show");
                    t.mdltitle.text("Edit Holiday");
                    t.btn.submit.text("Save Changes");
                    t.loadForm(data.data);
                }
                else {
                    sweetAlert('center', 'error', data);
                }
            }
        });
        http.fail(function () {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
        });
        http.always(function () {
            t.httpCall = true;
        });
    };

    t.deleteHoliday = function (e) {
        e.preventDefault();
        var projectId = $(this).attr("data-id");
        t.httpPostPath = t.config.url.delete + "/" + projectId;
        var data = {
            'msg': config.translations.something_went_wrong,
        };
        sweetAlerts(config.translations.are_you_delete,'warning', t.httpPostPath, t.dTbl, data);
       
    };   
    t.getHolidayErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) return $();

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };
    
    t.updateHolidayValidationState = function (element, hasError) {
        var group = element.closest(".input-group");
        var isSelect2 = element.hasClass("select2-hidden-accessible");

        if (group.length) {
            group.toggleClass("amg-form-invalid", !!hasError);
        }

        if (isSelect2) {
            element.next(".select2-container")
                .find(".select2-selection")
                .toggleClass("amg-form-select-error", !!hasError);
        }
    };   


    t.frmValidator = t.frm.validate({
        onsubmit: false,
        ignore: [],
        rules: {
            date_name: { required: true },
            name: {
                required: true,
                clean_text_only: true,
            },
            company_id: { required: true }
        },
        messages: {
            date_name: "Please Select  Holiday Date",
            name: "Please Enter Holiday Name",
            company_id: "Please Select Company"
        },

        errorPlacement: function (error, element) {
            var errorWrap = getErrorWrap(element);
            if (errorWrap.length) {
                error.appendTo(errorWrap);
            } else {
                error.insertAfter(element.closest(".input-group"));
        }
            updateValidationState(element, true);
        },
        highlight: function (element) {
            updateValidationState($(element), true);
        },
        unhighlight: function (element) {
            updateValidationState($(element), false);
        },
        invalidHandler: function (event, validator) {
            if (validator.numberOfInvalids()) {
                validator.errorList[0].element.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    });
    t.frmEl.date_name.on("change", function () {
        t.frmEl.date_name.valid();
    });

    t.frmEl.company_id.on("change", function () {
        t.frmEl.company_id.valid();
        t.frmEl.location_id.val(null).trigger("change");
        t.frmEl.location_country_id.val(null).trigger("change");
        t.data.countries = null;
        t.frmEl.alllocations_id.prop("checked", false);
    });

    t.frmEl.location_id.on("change", function () {
        t.frmEl.location_id.valid();
    });

    t.frmEl.location_country_id.on("change", function () {
        t.frmEl.location_country_id.valid();
    });
    t.frmEl.date_name.on("change", function () {
        t.frmEl.date_name.valid();
    });

    t.frmEl.company_id.on("change", function () {
        t.frmEl.company_id.valid();
        t.frmEl.location_id.val(null).trigger("change");
        t.frmEl.location_country_id.val(null).trigger("change");
        t.data.countries = null;
        t.frmEl.alllocations_id.prop("checked", false);
    });

    t.frmEl.location_id.on("change", function () {
        t.frmEl.location_id.valid();
    });

    t.frmEl.location_country_id.on("change", function () {
        t.frmEl.location_country_id.valid();
    });

    t.handleCountryChange = function (e) {
        var data = $(this).select2("data");
        if (t.config.locations && data && data.length > 0) {
            const filtered_locations = data.map((x) =>
                t.config.locations.filter((y) => y.country == x.country)
            );
            const selected_locations = [].concat(...filtered_locations);
            const locations = t.config.locations.filter((x) =>
                selected_locations.find((y) => x.id == y.id)
            );
            t.frmEl.location_id
                .select2("destroy")
                .empty()
                .select2(
                    $.extend({}, select2Opts, {
                        data: locations,
                        placeholder: "Select Locations",
                    })
                );
        } else {
            t.frmEl.location_id
                .select2("destroy")
                .empty()
                .select2(
                    $.extend({}, select2Opts, {
                        data: t.config.locations,
                        placeholder: "Select Locations",
                    })
                );
        }
    };

    t.handleLocationCheckbox = function (e) {
        let companyId = t.frmEl.company_id.val();
        if (!companyId) {
            $(this).prop("checked", false);
            var data = {'msg': "Please select Company first!"};
            sweetAlert('center', 'warning', data);
            return false;
        }
        if ($(this).is(":checked") == true) {
            t.frmEl.location_id.empty().append();
            $.get(t.config.url.locations, { countries: t.data.countries,company_id: t.frmEl.company_id.val() })
                .done(function (data) {
                    if (typeof data == "object" && data.results.length) {
                        $.each(data.results, function (i, v) {
                            t.frmEl.location_id.append(new Option(v.text, v.id));
                        });
                        const values = data.results.map((x) => x.id);
                        $("#location_id").val(values).trigger("change");
                    }
                })
                .always(function () {
                    t.frmEl.location_id.trigger("change");
                    // $("#location_id").attr('disabled', true);
                });
        } else {
            $("#location_id").val([]).trigger("change");
            // $("#location_id").attr('disabled', false);
        }
    };

    t.refillLocations = function (e) {
        if (typeof e !== "undefined") {
            e.preventDefault();
        }
        t.frmEl.location_id.empty();
        var data = $(this).select2("data");
        data = data.map((x) => x.country_code).join(",");
        t.data.countries = data;
        t.frmEl.alllocations_id.prop('checked', false);
        $("#location_id").attr('disabled', false);
    };

    t.ajaxLocations = function () {
        t.frmEl.location_id.empty().append(new Option(config.translations.Select_Problem_Category, ""));
        $.get(t.config.url.locations, { countries: t.data.countries, company_id:t.frmEl.company_id.val() }).done(function (data) {
                if (typeof data == "object" && data.results.length) {
                    $.each(data.results, function (i, v) {
                        t.frmEl.location_id.append(new Option(v.text, v.id));
                    });
                }
            })
            .always(function () {
                t.frmEl.location_id.trigger("change");
            });
    };

    var select2Opts = { width: "100%" };
    t.frmEl.location_country_id.select2(        
        $.extend({}, select2Opts, {
            dropdownParent: $('#holidaysMdl'),
            ajax: {
                url: t.config.url.countries,
                dataType: "json",
            },
            placeholder: "Select The Countries",
        })
    ).on("change", $.proxy(t.refillLocations));

    

    t.frmEl.location_id.select2(
    $.extend({}, select2Opts, {
        dropdownParent: $('#holidaysMdl'),
        ajax: {
            url: t.config.url.locations,
            dataType: "json",
            data: function (params) {
                return {
                    q: params.term,
                    countries: t.data.countries,
                    company_id: t.frmEl.company_id.val()
                };
            }
        },
        placeholder: "Select The Locations",
    })
);

    t.frmEl.location_id.on('select2:opening', function (e) {
        let companyId = t.frmEl.company_id.val();
        let countries = t.data.countries;

        if (!companyId || !countries || countries.length === 0) {
            e.preventDefault(); 
            var data = {
                'msg': "Please select Company and Country first!"
            };
            sweetAlert('center', 'warning', data);        
        }
    });

    t.frmEl.company_id.select2($.extend({}, select2Opts, {
        dropdownParent:t.frmEl.company_id.parent(),
        ajax: {
            url: config.url.get_company_by_user_access,
            dataType: "json",
            data: function(p) {
                return {
                    search: p.term,
                    page: p.page || 1
                };
            },
            delay: 300
        },
        placeholder: "Select Company"
    }));

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters + '&company_id=' +companyId;;
    }

    t.content.on('click', '.open-add-modal', $.proxy(t.createHoliday));
    t.content.on('click', '.open-edit-modal', $.proxy(t.editHoliday));
    t.content.on('click', '.open-delete', $.proxy(t.deleteHoliday));
    t.btn.submit.on('click', $.proxy(t.handlesubmit));
    t.frmEl.alllocations_id.on("change", $.proxy(t.handleLocationCheckbox));
    t.content.on('click', '.btn-reload-list', $.proxy(t.reload));
    t.content.on('click', '.btn-holiday-export', $.proxy(t.export));
    t.content.on("keyup", t.searchbox, $.proxy(t.search));
    t.content.on("click", '.btn-searchbox', $.proxy(t.tableSearch));
    t.content.on('click', '.view-details', function (e) {
        e.preventDefault();
        let values = $(this).attr('data-values');
        let column = $(this).attr('data-column');
        try {
            values = JSON.parse(values);
        } catch (err) {
            values = [];
        }
        let title = column === 'location' ? 'Location List' : 'Country List';
        $('#descriptionMdl .modal-title').text(title);
        let html = '<div class="d-flex flex-wrap gap-2">';
        values.forEach(function (item) {
            html += `<span class="badge bg-light text-dark border">${item}</span>`;
        });
        html += '</div>';
        $('#modalBodyContent').html(html);
        let modal = new bootstrap.Modal(document.getElementById('descriptionMdl'));
        modal.show();
    });
    t.dTbl.ajax.reload();
};

