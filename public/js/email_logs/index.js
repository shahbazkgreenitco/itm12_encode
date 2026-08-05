
var Emails = function (config) {
    var t = this;
    t.config = config;
    t.content = $('section.content');
    t.table = t.content.find('#mytable');
    t.btn = {};
    t.filters = {
        wrapper: t.content.find("#FilterModal")
    };
    t.filters.based_on = t.filters.wrapper.find("#filter_by_date");
    t.filters.date_range = t.filters.wrapper.find("#daterange");
    t.btn.clear = t.filters.wrapper.find('.btn-clear-filter');
    t.searchbox = t.content.find(".searchbox");
    t.searchbtn = t.content.find(".btn-searchbox");
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        colReorder: true,
        order: [[1, "asc"]],
        deferLoading: true,
        responsive: true,
        processing: true,
        serverSide: true,
        scrollX: true,
        dom: 'rt<"d-flex justify-content-between align-items-center flex-md-nowrap flex-wrap mt-2"i p>',
        fixedColumns: {
            leftColumns: 1,
            rightColumns: 1
        },
        ajax: {
            url: t.config.url.email,
            type: "POST",
            data: function (d) {
                d._token = t.config.token;
                d.filters = t.config.other_filters || {};
            }
        },

        columns: [
            { data: "a.to_value" },
            { data: "a.subject" },
            { data: "a.sent_at" },
            { data: "a.created_at" },
            { data: "a.updated_at" }
        ],
        initComplete: function () {
            var api = this.api();
            // Search on Enter
            $("#email-log-list-search").off(".DT").on("keyup.DT", function (e) {
                if (e.keyCode === 13 || this.value.length === 0) {
                    var v = $.trim($(this).val());
                    if (v === false) {
                        alert(parent.config.translations.please_enter_valid_search);
                        return;
                    }
                    t.config.search = v;
                    api.search(v).draw();
                }
            });

            t.cache_filter_values = function () {
               var v = $.trim($("#email-log-list-search").val());
                t.config.search = v || "";
                t.config.other_filters = {};
                if (t.filters.based_on.val() && t.filters.based_on.val() !== "null") {
                    t.config.other_filters.based_on = t.filters.based_on.val();
                }
                if (
                    t.filters.date_range.val() &&
                    t.filters.date_range.val() !== "null" &&
                    t.filters.based_on.val() &&
                    t.filters.based_on.val() !== "null"
                ) {
                    t.config.other_filters.date_range = t.filters.date_range.val();
                }
                var jobj = {
                    search: t.config.search,
                    other_filters: t.config.other_filters
                };

                t.config.export_filters = btoa(JSON.stringify(jobj));
                filterCount(t.config.other_filters, false);
                t.filters.wrapper.modal("hide");
            };

            t.search = function (e) {
                var target = e.target || e.currentTarget;
                if (e.keyCode === 13 || (e.type === "click" && target.tagName !== "BUTTON")) {
                    var v = $.trim($("#email-log-list-search").val());
                    if (v === false) {
                        t.config.search = "";
                        alert(parent.config.translations.please_enter_valid_search);
                        return;
                    }
                    t.cache_filter_values();
                    api.search(v).draw();
                }
                else if (target.tagName === "BUTTON") {
                    t.cache_filter_values();
                    api.ajax.reload(null, true);
                }
            };

            function updateColumnVisibilityControls() {
                var controls = $("#columnVisibilityControls").empty();
                api.columns().every(function () {
                    var columnIndex = this.index();
                    var columnTitle = $(this.header()).text().trim();
                    var columnId = "column-toggle-" + columnIndex;
                    controls.append(`
                        <div class="mb-2">
                            <input
                                class="form-check-input column-toggle"
                                type="checkbox"
                                id="column_${columnIndex}"
                                data-column="${columnIndex}"
                                ${this.visible() ? "checked" : ""}>

                            <label class="form-check-label ms-1" for="column_${columnIndex}">
                                ${columnTitle}
                            </label>
                        </div>
                    `);
                });
            }
            updateColumnVisibilityControls();
            $("#columnVisibilityControls").off("change").on("change", 'input[type="checkbox"]', function () {
                api.column($(this).data("column")).visible(this.checked);
            });

            api.on("column-reorder", function () {
                setTimeout(updateColumnVisibilityControls, 10);
            });

            t.btn.clear.off("click").on("click", function () {
                t.config.other_filters = {};
                t.config.search = "";
                t.filters.based_on.val("null").trigger("change");
                resetDateRangeFilter();
                resetFilterCount();
                $("#email-log-list-search").val("");
                t.cache_filter_values();
                api.search("").ajax.reload(null, true);
            });
            $('#user-list-page-length').val(api.page.len());
            $('#user-list-page-length').on('change', function () {
                api.page.len(parseInt($(this).val())).draw();
            });
        }
    });

    t.reload = function() {
        t.dTbl.ajax.reload();
    };

    t.export = function (e) {
        e.preventDefault();
        t.cache_filter_values();
        window.location = t.config.url.download_url + "?q=" + t.config.export_filters;
    }

    var select2Opts = { width: "100%" };
    t.filters.based_on.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_Based_on, dropdownParent: t.filters.wrapper }));
    t.content.on('click', '.btn-export-emails', $.proxy(t.export));
    t.content.on("click", '.btn-searchbox',  $.proxy(t.search));
    t.content.on("click", '.btn-reload-list',  $.proxy(t.reload));
    t.searchbox.on("keypress", $.proxy(t.search));
    t.searchbtn.on("click", $.proxy(t.search));
    t.filters.wrapper.on("click", ".btn-filter", t.search);
    $(document).on('click', '.btn-open-filter', function () {
        t.filters.wrapper.modal('show');
    });
    t.dTbl.ajax.reload();

};