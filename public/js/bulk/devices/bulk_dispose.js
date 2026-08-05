var BulkCheckInPhase = function(config) {
    var t = this;
    t.config = config;
    t.content = $("section.content");
    t.tabBar = t.content.find('.tab-bar');
    t.infoTab = $("section.content").find("#info-tab");

    var form = $("#confirmForm");
    var file = form.find("#import_file");

    form.on("submit", function(e) {
        var file_name = file.val();
        if(file_name == "" || file[0].type != "file") {
            var data = {
                'msg': "Please choose the file for Bulk Checkin."
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }
        var name_arr = file_name.split(".");
        name_arr.reverse();
        if(name_arr[0] != "xlsx" && name_arr[0] != "XLSX") {
            var data = {
                'msg': config.translations.Please_upload_valid_xlsx,
            }
            sweetAlert('center', 'error', data);
            e.preventDefault();
            return;
        }       
    });

};
var CheckInInfoPhase = function(config) {
    var t = this;
    t.config = config;
    t.tab = $("section.content").find('#check-out-info-tab');
    t.table = t.tab.find('#tblHistory');
  
    t.dTbl = t.table.DataTable({
        autoWidth: false,
        aoColumnDefs: [{
            'bSortable': false,
            'aTargets': [0]
        }, {
            targets: 0,
                render: function (d) {
                    var a = [];
                    a.push("<a download = "+ d.doc_name +" href=\"" + t.config.document_download + "/" + d.doc_path + "\" class='btn dtActbtn' data-toggle='tooltip' data-original-title="+ config.translations.Download_Document +" data-placement='right' data-id=\"" + d.id + "\" ><i class=\"fa fa-download\"></i></a>");
                    return a.join(' ');

            }
       
         }],
        order: [
            [5, 'desc']
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: t.config.bulk_dispose_info,
            type: "post",
            data: function(d) {
                d._token = t.config.token;
                d.device_id = 1;
                d.action_type = 6;
            }
        },
        columns: [
            { data: 'a' },
            { data: 'a.doc_name' },
            { data: 'a.tot_imported' },
            { data: 'a.tot_success' },
            { data: 'a.tot_failure' },
            { data: 'a.last_updated_at' },
            { data: 'a.user_name' },
           
        ],
        fnInitComplete: function(oSettings, json) {
            var api = this.api();
            $("#tblHistory_filter input").off(".DT");
            var searchBox  = '<div class="input-group table-search-btns">'
            searchBox +='<input type="text" class="form-control searchbox plain-search"  placeholder="'+config.translations.press_enter_with_Search+'" />';
            searchBox += '<span class="input-group-addon btn-searchbox" data-placement="bottom" data-toggle="tooltip" data-original-title="'+config.translations.search+'"><i class="ps-icon plain-search-icon"></i></span>';
            searchBox += '<span class="input-group-addon btn-reload-list btn_reload" data-placement="bottom" data-toggle="tooltip"  data-original-title="'+config.translations.reload+'"><i class="ps-icon fa fa-refresh"></i></span></div>';
            $("#tblHistory_filter").empty().html(searchBox);
            $("#tblHistory_filter input").on("keyup.DT", function(e) {
                if(e.keyCode == 13) {
                    var v = $(this).validate_str_param();
                    if(v === false) {
                        alert("Please enter a valid value for search");
                        return false;
                    }
                    api.search(v).draw();
                }
            });
        }
    });

    t.reload = function() {
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
        }
        else if (target.tagName == "BUTTON") {
            t.cache_filter_values();
            t.reload();
        }
    };

    t.tab.on("click", ".btn_reload", $.proxy(t.reload));
    t.tab.on("click", ".btn-searchbox", $.proxy(t.search));
   
};

var MyApp = function(config) {
    var t = this;
    t.content = $("#content-container");
    t.bulkCheckoutPhase = new BulkCheckInPhase(config);
    t.checkoutInfoPhase = new CheckInInfoPhase(config);

    t.content.on("click", ".black-slide-links a", function(e) {
        e.preventDefault();
        var thisNav = $(this);
        t.content.find(".black-slide-links .active").removeClass("active");
        thisNav.parent().addClass("active");
        t.content.find(".black-slide-view.active").slideUp("fast", function() {
            $(this).removeClass("active");
            t.content.find(thisNav.attr("href")).addClass("active").slideDown("slow");
        });
    });
}