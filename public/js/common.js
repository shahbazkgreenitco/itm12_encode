var showAlert = function(msgData, cssClass) {
    $(".masalert").remove();
    var str = '<div id="alertMsg" class="masalert alert alert-' + cssClass + '"><a class="close" data-dismiss="alert">X</a>' + msgData + '</div>';
    $("#page-wrapper").prepend(str);
    $('body').scrollTo('#alertMsg', 1000, {offset:-60});
};

(function($) {
    $.fn.extend({
        validate_str_param: function(rtn_bool) {
            var v = $.trim($(this).val());
            if(v == "") {
                return v;
            }
            var r = /^[a-zA-Z0-9\s\r\n -/_%&{}",@'’.)(:?^]{0,500}$/.test(v);
            return r == false ? r : (typeof rtn_bool != "undefined" ? r : v);
        }
    });
})(jQuery);

// toastr.options = { closeButton: true, "progressBar": true };

$(document).ready(function($){
    $.navigation = $('nav ul.list-group');
    $.navigation.find('a').each(function() {
        var cUrl = String(window.location);

        if (cUrl.substr(cUrl.length - 1) == '#') {
            cUrl = cUrl.slice(0,-1);
        }

        if ($($(this))[0].href==cUrl) {
            $(this).parents('.collapse').addClass('in');

            if($(this).offset().top > 700) {
                $('.nano-content').stop().animate({
                    scrollTop: $(this).offset().top - $('#mainnav-profile').height() - $('.nav-red').height() - 50
                }, 400);
            }
        }
    });

    $(document).on( 'click', '.btnAction ', function () {
        $('[data-toggle="tooltip"]').tooltip('destroy');
    }).click();

});

function addSearchEventListener(elementId, dataTableInstance) {
    const searchInput = document.getElementById(elementId);
    if (searchInput) {
        searchInput.addEventListener('search', function (event) {
            if (event.target.value === '') {
                dataTableInstance.search('').draw();
            }

        });
    } else {
        console.error('Element with id ' + elementId + ' not found.');
    }
}

function resetDateRangeFilter() {
    var start = moment().startOf('day'); // Set start to the beginning of "Today"
    var end = moment().endOf('day');     // Set end to the end of "Today"

    // Update picker start and end dates
    $('#reportrange, .reportrange').data('daterangepicker').setStartDate(start);
    $('#reportrange, .reportrange').data('daterangepicker').setEndDate(end);
    $('#reportrange, .reportrange').data('daterangepicker').chosenLabel = "Today";

    cb(start, end);
}

function cb(start, end) {
    $('#reportrange span').html(
        start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format('DD-MM-YYYY HH:mm:ss')
    );
    $('#daterange').val(
        start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss')
    );
}

// common function for datatable bootstrap translation properties.
 function datatable_footer_translations (translations) {
    return {
        info: `${translations.showing} _START_ ${translations.to} _END_ ${translations.of} _TOTAL_ ${translations.records}`,
        infoEmpty: `${translations.showing} 0 ${translations.to} 0 ${translations.of} 0 ${translations.records}`,
        emptyTable: translations.empty_result,
        zeroRecords: translations.empty_result,
        infoFiltered: `(${translations.filtered} ${translations.from} _MAX_ ${translations.total_entries})`,
        paginate: {
            previous: translations.prev,
            next: translations.next
        }
    };
};