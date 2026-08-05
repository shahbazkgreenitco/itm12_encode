 <!-- jQuery -->
 {{-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> --}}
 <script src="{{ asset('newjs/jquery-3.7.1.min.js') }}"></script>
 <script src="{{ asset('newjs/moment.min.js') }}"></script>
 <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('assets/libs/datatables.net/js/dataTables.fixedColumns.min.js') }}"></script>
 <!-- <script src="@@webRoot/node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script> -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
 <!-- <script src="@@webRoot/node_modules/apexcharts/dist/apexcharts.min.js"></script> -->
 <script type="text/javascript" src="{!! CommonHelper::asset('js/apex-chart/apexchart.js') !!}"></script>
 <script src="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}"></script>
 <script src="{{ asset('assets/libs/select2/dist/js/select2.min.js') }}"></script>
 <script src="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.js') }}"></script>
 <script src="{{ asset('assets/scripts/forms/sweet-alert.init.js') }}"></script>
 <script src="{{ asset('assets/scripts/plugins/toastr-init.js') }}"></script>
 <script src="{{ asset('js/jquery.validate.min.js') }}"></script>
 <script src="{{ asset('js/support_validate.js') }}"></script>
 <script src="{{ asset('js/sweetAlert.js') }}"></script>
 {{-- <script src="../../libs/datatables.net/js/jquery.dataTables.min.js"></script> --}}
 <script src="{{ asset('assets/js/datatable/datatable-advanced.init.js') }}"></script>
 <script src="{{ asset('main_asset/scripts/index.js') }}" defer></script>
 <script src="{{ asset('main_asset/scripts/theme-toggle.js') }}"></script>
 <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery-validation/dist/additional-methods.min.js') }}"></script>
 <script src="{{ asset('assets/libs/jquery.filedrop.js') }}"></script>
 <script src="{{ asset('js/utility/summernote-config.js') }}"></script>
 <script src="{{ asset('js/call/webrtc.js') }}"></script>
 <script src="{{ asset('js/call/signal.js') }}"></script>
 <script src="{{ asset('js/call/wedget.js') }}"></script>

 <script type="text/javascript" src="{!! CommonHelper::asset('nfy/plugins/daterangepicker/daterangepicker.min.js') !!}"></script>
@vite(['resources/js/app.js'])
 
<script>
    var SysAlert = function(shelf) {
        var t = this;
        t.jqShelf = $('body').find(shelf);
        t.jqParent = t.jqShelf.parent();
        t.show = function(data) {
            if(typeof data != "object" || data == null) {
                return;
            }
            t.jqParent.find(".masalert").remove();
            var ac = data.status != "" ? "alert-" + data.status : "alert-info";
            t.jqShelf.prepend(
                '<div class="masalert alert ' + ac + ' alert-dismissible fade show">'+
                    '<span>' + data.msg + '</span>' +
                '</div>'
            );

            setTimeout(function () {
                t.jqShelf.find(".masalert").fadeOut("slow", function () {
                    $(this).remove();
                });
            }, 5000);
        }
        return t;
    };
    var sysAlert = new SysAlert("#mainContent");
    @if(Session::has('msg'))
        sysAlert.show({!! json_encode(Session::get('msg')) !!});
        {{ Session::forget('msg') }}
    @endif
    document.querySelectorAll('.copy-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            let textToCopy;
            if (btn.dataset.copy) { // Case 1: data-copy attribute exists
                textToCopy = btn.dataset.copy;
            } else { // Case 2: no data-copy, grab nearest <pre><code>
                const codeBlock = btn.previousElementSibling.querySelector('code');
                textToCopy = codeBlock.innerText;
            }
            navigator.clipboard.writeText(textToCopy);
            const original = btn.textContent;
            btn.textContent = 'Copied ✓';
            setTimeout(() => {
                btn.textContent = original;
            }, 1200);
        });
    });

     document.addEventListener("DOMContentLoaded", function() {
         "use strict";
         // =================================
         // Tooltip
         // =================================
         const tooltipTriggerList = Array.from(
             document.querySelectorAll('[data-bs-toggle="tooltip"]')
         );
         tooltipTriggerList.forEach((tooltipTriggerEl) => {
             new bootstrap.Tooltip(tooltipTriggerEl);
         });

         // =================================
         // Popover
         // =================================
         var popoverTriggerList = [].slice.call(
             document.querySelectorAll('[data-bs-toggle="popover"]')
         );
         var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
             return new bootstrap.Popover(popoverTriggerEl);
         });
     });
 </script>

 <script>
     document.addEventListener('DOMContentLoaded', function() {
         if (window.$) {
             $('#data-table-ini').DataTable({
                 responsive: true
             });
             $("#date-range").datepicker({
                 toggleActive: true,
             });
             var options_simple = {
                 series: [44, 55, 13, 43, 22],
                 chart: {
                     fontFamily: "inherit",
                     width: 380,
                     type: "pie",
                 },
                 colors: [
                     "var(--bs-primary)",
                     "var(--bs-secondary)",
                     "#ffae1f",
                     "#fa896b",
                     "#39b69a",
                 ],
                 labels: ["Team A", "Team B", "Team C", "Team D", "Team E"],
                 responsive: [{
                     breakpoint: 480,
                     options: {
                         chart: {
                             width: 200,
                         },
                         legend: {
                             position: "bottom",
                         },
                     },
                 }, ],
                 legend: {
                     labels: {
                         colors: ["#a1aab2"],
                     },
                 },
             };
            var el = document.querySelector("#chart-pie-simple");
            if (el) {
                var chart_pie_simple = new ApexCharts(el, options_simple);
                chart_pie_simple.render();
            }

         } else {
             console.error('jQuery not loaded yet');
         }
     });
     /* User Module Sele */
     $(function() {
         $('.userModulePageLenth').select2({
             theme: 'custom',
             minimumResultsForSearch: Infinity,
             width: 'auto'
         });
     });

     /* User Module Sele */
     $(function() {
         $('.amgTablePageLenth').select2({
             theme: 'custom',
             minimumResultsForSearch: Infinity,
             width: 'auto'
         });
     });
 </script>
 {{-- Announcement js --}}

 <script type="text/javascript">
     const marquee = document.getElementById("marqueeWrapper");
     let currentSpeed = 20;

     function updateScrollAmount() {
         const el = document.getElementById("marqueeContent");
         el.style.animationDuration = currentSpeed + "s";
         $('.speed-controls .icon-btn').prop('disabled', true);
         $.ajax({
                 url: "{{ url('save-announcement-speed') }}",
                 type: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 data: {
                     scrollamount: currentSpeed
                 },
                 dataType: 'json'
             })
             .done(function(data) {
                 $('.speed-controls .icon-btn').prop('disabled', false);
                 if (data.success == true) {
                     var msgData = {
                         'msg': "Notification speed updated successfully!"
                     };
                     sweetAlert('center', 'success', msgData);

                 } else {
                     console.log('Failed to save speed:', data.message);
                     var msgData = {
                         'msg': data.message
                     };
                     sweetAlert('center', 'error', msgData);
                 }
             });
     }

     function increaseSpeed() {
         if (currentSpeed > 5) {
             currentSpeed -= 1; // faster
             updateScrollAmount();
         }
     }

     function decreaseSpeed() {
         currentSpeed += 1;
         updateScrollAmount();
     }


     function fetchInitialSpeed() {
         $.ajax({
                 url: "{{ url('get-announcement-speed') }}",
                 type: 'GET',
                 dataType: 'json'
             })
             .done(function(data) {
                 if (data.success && data.announcement_speed) {
                     currentSpeed = parseInt(data.announcement_speed);
                 } else {
                     currentSpeed = 20;
                 }
                 const el = document.getElementById("marqueeContent");
                 el.style.animationDuration = currentSpeed + "s";
             });
     }

     fetchInitialSpeed();

     marquee.addEventListener("mouseover", () => {
         document.getElementById("marqueeContent").style.animationPlayState = "paused";
     });

     marquee.addEventListener("mouseout", () => {
         document.getElementById("marqueeContent").style.animationPlayState = "running";
     });

     function refreshContentHeight() {
         document.getElementById("body_wrapper").style.minHeight = window.innerHeight - 150 > 500 ? window.innerHeight -
             150 + "px" : "500px"
     }
     refreshContentHeight(), window.onresize = function() {
         refreshContentHeight()
     };
     var baseURL = '{{ URL::to('/') }}';
     var baseConfig = {
         today: "{{ date('d/m/Y') }}"
     };
     $(document).ready(function() {
         function parseIfString(data) {
             if (typeof data === "string") {
                 try {
                     return JSON.parse(data);
                 } catch (e) {
                     console.error("Invalid JSON:", data);
                     return [];
                 }
             }
             return data;
         }

         function cleanText(text) {
             if (!text) return '';
             return text
                 .replace(/style\s*=\s*(['"])(?:(?!\bbackground(?:-color)?\b)[^'"])*\1/gi, '')
                 .replace(/padding\s*:\s*[^;]+;?/gi, '')
                 .replace(/margin\s*:\s*[^;]+;?/gi, '')
                 .replace(/<(\/?(br|li|ol|ul|div))[^>]*>/gi, ' ')
                 .replace(/<\/?(h[1-6])[^>]*>/gi, '')
                 .replace(/<\/?(b)>/gi, function(match) {
                     return match.toLowerCase();
                 });
         }
         let announcements = @json(CommonHelper::loadAnnouncement());
         announcements = parseIfString(announcements);
         let combinedAnnouncements = [];
         if (Array.isArray(announcements) && announcements.length > 0) {

             let parentElement = $("#marqueeContent");
             parentElement.empty();
             $.each(announcements, function(i, v) {
                 let isMeeting = v.meeting !== undefined && v.meeting !== null;
                 let announcementText = (v.announcement ?? v.meeting) || '';
                 announcementText = cleanText(announcementText);
                 let icon = isMeeting ?
                     `<span class='meeting-icon'><i class="bi bi-calendar2"></i></span>` :
                     `<span class='announcement-icon'>&#10148;</span>`;
                 let extraAttr = isMeeting ?
                     `data-id="${v.id}" style="cursor:pointer;" class="announcement-item meetingDetails"` :
                     `class="announcement-item"`;
                 let html = `<span ${extraAttr}>${icon} ${announcementText}</span>`;
                 combinedAnnouncements.push(html + '&nbsp;&nbsp;&nbsp;&nbsp;');
             });
             parentElement.append(combinedAnnouncements.join(' '));
             $(".ticker").show();

         } else {
             $(".ticker").hide();
         }

         let incidents = @json(CommonHelper::loadIncidents());
         incidents = parseIfString(incidents);
         let combinedIncidents = [];
         if (Array.isArray(incidents) && incidents.length > 0) {
             let parentElement = $(".announcement").find("marquee");
             parentElement.empty();
             $.each(incidents, function(i, v) {
                 let announcementText =
                     (v.website_name ?? '') + ": " +
                     (v.alert_type_data ?? '') + " " +
                     (v.comparison ?? '') + " " +
                     (v.limit_value ?? '') + " " +
                     (v.is_status ?? '');
                 announcementText = cleanText(announcementText);
                 let iconClass = v.status == 3 ? "danger-icon" : "warning-icon";
                 let icon = `
                <span class="${iconClass}">
                    <i class="fa fa-exclamation-triangle"></i> ${announcementText}
                </span>`;
                 let html = `<p class="incident-link" data-id="${v.id}" style="cursor:pointer;">${icon}</p>`;
                 combinedIncidents.push(html);
             });
             parentElement.append(combinedIncidents.join(' '));
             $(".ticker").show();
             $(document).on("click", ".incident-link", function() {
                 let incidentId = $(this).data("id");
                 let baseUrl = "{{ url('websites/live-monitor/incident-list/opened ') }}";
                 let redirectUrl = baseUrl + '?announcement_incident=' + incidentId;
                 window.location.href = redirectUrl;
            });
         }
     });
    setTimeout(function() {
         var start = moment().startOf("day");
         var end = moment().endOf("day");

         function cb(start, end) {
             $("#reportrange span").html(
                 start.format("DD-MM-YYYY HH:mm:ss") +
                 " - " +
                 end.format("DD-MM-YYYY HH:mm:ss"),
             );
             $('#daterange').val(
                 start.format("YYYY-MM-DD HH:mm:ss") +
                 " - " +
                 end.format("YYYY-MM-DD HH:mm:ss"),
             );
         }

        $("#reportrange").daterangepicker({
                 startDate: start,
                 endDate: end,
                 drops: "up",
                 timePicker: true,
                 timePicker24Hour: true,
                 timePickerSeconds: true,
                 timePickerIncrement: 1,
                 autoApply: false,
                 autoUpdateInput: false,
                 opens: "right",
                 locale: {
                     format: "DD-MM-YYYY HH:mm:ss",
                     cancelLabel: "Clear",
                 },
                 ranges: {
                     Today: [moment().startOf("day"), moment().endOf("day")],
                     Yesterday: [
                         moment().subtract(1, "days").startOf("day"),
                         moment().subtract(1, "days").endOf("day"),
                     ],
                     "Last 7 Days": [
                         moment().subtract(6, "days").startOf("day"),
                         moment().endOf("day"),
                     ],
                     "Last 30 Days": [
                         moment().subtract(29, "days").startOf("day"),
                         moment().endOf("day"),
                     ],
                     "This Month": [moment().startOf("month"), moment().endOf("month")],
                     "Last Month": [
                         moment().subtract(1, "month").startOf("month"),
                         moment().subtract(1, "month").endOf("month"),
                     ],
                 },
             },
             cb,
        );

         cb(start, end);

         $("#reportrange").on("cancel.daterangepicker", function() {
             $(this).find("span").html("");
             $('#daterange').val("");
         });
     }, 300);

     function filterCount(params) {
         let count = 0;
         $.each(params, function(key, value) {

             if (
                 value === null ||
                 value === undefined ||
                 value === '' ||
                 value === 'null'
             ) {
                 return;
             }

             if (Array.isArray(value)) {

                 let validValues = value.filter(v =>
                     v !== null &&
                     v !== undefined &&
                     v !== '' &&
                     v !== 'null'
                 );

                 if (validValues.length > 0) {
                     count++;
                 }

             } else {
                 count++;
             }
             if(key== "date_range" || key == "daterange"){
                count--;
             }

         });
         var $badge = $(".filter-count-badge");
         if (count > 0) {
             $badge.text(count).removeClass("d-none");
         } else {
             $badge.text("0").addClass("d-none");
         }
     }

     function resetFilterCount() {
         $(".filter-count-badge").text("0").addClass("d-none");
     }
     $('.amg-modal').on('show.bs.modal', function() {
         $(this).find('label.error').hide();
     });

     $(document).on(
         'change keyup change.select2',
         '.remove-validation-errors .amg-modal input, .remove-validation-errors .amg-modal select, .remove-validation-errors .amg-modal textarea',
         function() {

             if (!$(this).closest('.amg-modal').is(':visible')) {
                 return;
             }

             var form = $(this).closest('form');

             if (!form.length || !form.data('validator')) {
                 return;
             }

             var isValid = $(this).valid();

             if (isValid) {
                 $(this).siblings('label.error').hide();
                 $(this).closest('.amg-form-field-row').find('label.error').hide();
             } else {
                 $(this).siblings('label.error').show();
                 $(this).closest('.amg-form-field-row').find('label.error').show();
             }
         }
     );


     // Date range picker code start
    var startDate = moment().startOf('day');
    var end_datetime = "DD-MM-YYYY HH:mm:ss";

    function cbs(start, end, label) {
        $('#reportrange span, .reportrange span').html(start.format('DD-MM-YYYY HH:mm:ss ') + ' - ' + end.format(end_datetime));
        $('.drp-selected').hide();
        $('#daterange, .daterange').val(start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format(end_datetime));
        $('#selected-range').text(start.format('DD-MM-YYYY HH:mm:ss') + ' - ' + end.format(end_datetime));
    }

    // Function to reset date range filter
    function resetDateRangeFilter() {
        var start = moment().startOf('day'); // Set start to the beginning of "Today"
        var end = moment().endOf('day');     // Set end to the end of "Today"

        // Update picker start and end dates
        $('#reportrange, .reportrange').data('daterangepicker').setStartDate(start);
        $('#reportrange, .reportrange').data('daterangepicker').setEndDate(end);

        // Reset the selected range to "Today"
        $('#reportrange, .reportrange').data('daterangepicker').chosenLabel = "Today";

        cbs(start, end); // Call the callback to update the UI
    }
 </script>