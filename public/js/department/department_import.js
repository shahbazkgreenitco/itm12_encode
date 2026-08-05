var BulkImportPhase = function (config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.tabBar = t.content.find('#myTab');
    t.infoTab = $("section.content").find("#info-tab");
    var form = $("#confirmForm");
    var file = form.find("#import_file");
    form.on("submit", function (e) {
        var file_name = file.val();
        if (file_name == "" || file[0].type != "file") {
            Swal.fire({
                icon: 'error',
                title: config.translations.error,
                text: config.translations.import_file,
                confirmButtonText: config.translations.ok,
            });
            e.preventDefault();
            return;
        }
        var name_arr = file_name.split(".");
        name_arr.reverse();
        if (name_arr[0] != "xlsx" && name_arr[0] != "XLSX") {

            Swal.fire({
                icon: 'error',
                title: config.translations.invalid_file,
                text: config.translations.Please_upload_valid_xlsx,
                confirmButtonText: config.translations.ok,
            });

            e.preventDefault();
            return;
        }
    });

};
var BulkImportInfoPhase = function (config) {
    var t = this;
    t.config = config;
    t.tab = $("#import-info");   // correct tab content
    t.table = $("#tblHistory");

    t.dTbl = t.table.DataTable({
        autoWidth: false,
        dom: 'rtip',
        scrollX: true,
        scrollCollapse: true,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [6]
        },
        {
            targets: 6,
            render: function (d) {
                var a = [];
                a.push(`
                    <a download="${d.doc_name}" href="${t.config.document_download}/${d.doc_path}" class="btn dtActbtn" data-toggle="tooltip" data-original-title="${config.translations.Download_Document}" data-placement="right" data-id="${d.id}">
                        <svg width="14" height="17" viewBox="0 0 14 17" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 3.99935L5 3.99935C4.45 3.99935 4 4.44935 4 4.99935L4 9.99935L2.41 9.99935C1.52 9.99935 1.07 11.0794 1.7 11.7094L6.29 16.2994C6.38251 16.3921 6.4924 16.4656 6.61338 16.5158C6.73435 16.566 6.86403 16.5918 6.995 16.5918C7.12597 16.5918 7.25565 16.566 7.37662 16.5158C7.4976 16.4656 7.60749 16.3921 7.7 16.2994L12.29 11.7094C12.92 11.0794 12.48 9.99935 11.59 9.99935L10 9.99935L10 4.99935C10 4.44935 9.55 3.99935 9 3.99935ZM13 1.99935L1 1.99935C0.450001 1.99935 1.31505e-06 1.54935 1.36313e-06 0.999352C1.41122e-06 0.449353 0.450001 -0.000647776 1 -0.000647728L13 -0.000646679C13.55 -0.000646631 14 0.449354 14 0.999353C14 1.54935 13.55 1.99935 13 1.99935Z" fill="currentColor"></path>
                        </svg>
                    </a>
                `);

                return a.join(' ');
            }

        }],
        order: [
            [4, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.bulk_import_info,
            type: "post",
            data: function (d) {
                d._token = t.config.token;
                d.module_id = 4;
                d.action_type = 1
            }
        },
        fixedColumns: {
           rightColumns: 1 
        },
        language: {
            paginate: {
                previous: t.config.translations.previous || "Previous",
                next: t.config.translations.next || "Next"
            },
            info: t.config.translations.showing_entries || "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: t.config.translations.no_entries || "Showing 0 to 0 of 0 entries",
            infoFiltered: t.config.translations.filtered_from || "(filtered from _MAX_ total entries)",
            zeroRecords: t.config.translations.no_matching_records || "No matching records found",
            emptyTable: t.config.translations.no_data || "No data available in table",
            search: t.config.translations.search || "Search:",
            lengthMenu: t.config.translations.length_menu || "Show _MENU_ entries"
        },
        drawCallback: function () {
            t.table.find('[data-bs-toggle="tooltip"]').each(function () {
                var existing = bootstrap.Tooltip.getInstance(this);
                if (existing) existing.dispose();
                new bootstrap.Tooltip(this);
            });
        },
        columns: [
            {
                data: 'a.doc_name',
                className: "amg-table-col-264",
            },
            {
                data: 'a.tot_imported',
                className: "amg-table-col-144"
            },
            {
                data: 'a.tot_success',
                className: "amg-table-col-144"
            },
            {
                data: 'a.tot_failure',
                className: "amg-table-col-144"
            },
            {
                data: 'a.last_updated_at',
                className: "amg-table-col-144"
            },
            {
                data: 'a.user_name',
                className: "amg-table-col-188",
                render: function(data, type, row){

                    if(type !== 'display'){
                        return data || '-';
                    }

                    let name = data || '-';

                    // backend se image bhejni hogi
                    let imageUrl =
                        row.a.user_profile_img ||
                        row.a.user_gravatar ||
                        '';

                    function getUserInitials(name){

                        let words = String(name || '')
                            .split(/\s+/)
                            .filter(Boolean);

                        if(!words.length){
                            return 'NA';
                        }

                        if(words.length === 1){
                            return words[0]
                                .substring(0, 2)
                                .toUpperCase();
                        }

                        return (
                            words[0].charAt(0) +
                            words[1].charAt(0)
                        ).toUpperCase();
                    }

                    function getAvatarHtml(name, imageUrl){

                        if(imageUrl){
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
                        <div class="d-flex align-items-center gap-3 w-100 user-list-person--compact">

                            ${getAvatarHtml(name, imageUrl)}

                            <div class="d-flex flex-column min-w-0">
                                <span class="b1-text" title="${name}">
                                    ${name}
                                </span>
                            </div>

                        </div>
                    `;
                }
            },
            {
                data: 'a',
                className: "amg-table-col-144"
            },
        ],        
        fnInitComplete: function () {
            var api = this.api();
            $('#mytable_length').hide();
            $('#deparment-import-list-search').on('keyup', function (e) {
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

            
        }
    });


    t.reload = function () {
        t.dTbl.ajax.reload();
    };
    t.search = function (e) {
        var target = e.target || e.currentTarget;
        if (e.keyCode == 13 || $(this).is("span")) {
            var v = $("#tblHistory_filter .plain-search").validate_str_param();
            if (v === false) {
                t.config.search = "";
                alert(config.translations.please_enter_valid_search);
                return false;
            }
            t.config.search = v;
            t.dTbl.search(v).draw();
            t.reload();
        } else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.tab.on("click", ".btn-reload-list", $.proxy(t.reload));
    t.tab.on("click", ".btn-searchbox", $.proxy(t.search));

};

var MyApp = function (config) {
    var t = this;
    t.content = $("#content-container");
    t.bulkDeletePhase = new BulkImportPhase(config);
    t.content.on("click", ".black-slide-links a", function (e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function () {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });
}
