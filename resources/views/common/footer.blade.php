{{-- @page-meta
{
  "page_no": "LYFTB-01",
  "file": "footer.blade.php",
  "versions": [
    {
      "version": "1.4",
      "writer": "Muzaffar Shaikh",
      "from": "2026-04",
      "reviewer": null,
      "description": "Redesign Footer to maintain screen ratio"
    }
  ]
}
--}}
<!-- Floating Call Button
<button type="button" id="callFab" class="btn btn-success rounded-circle shadow">

    <i class="bi bi-telephone-fill"></i>
</button> -->

<!-- Call Modal -->
<div class="modal fade" id="callModal" tabindex="-1" aria-labelledby="callModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="callModalLabel">
                    <i class="bi bi-telephone-fill text-success me-2"></i>
                    Available Technicians
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input type="text"
                           class="form-control"
                           id="searchTechnician"
                           placeholder="Search technician...">
                </div>

                <div id="technicianList">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1"></i>
                        <p class="mt-2 mb-0">No technicians found.</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>




<!-- <div id="callWidget" class="call-widget d-none">

    <div class="call-header">

        <div class="d-flex align-items-center">

            <img id="widgetImage"
                 src="/imgs/profile-75.jpg"
                 class="rounded-circle call-avatar">

            <div class="ms-2">

                <div id="widgetName" class="fw-bold">
                    Technician
                </div>

                <small class="calling-status">
                    Connecting...
                </small>

            </div>

        </div>

        <div>

            <button id="minimizeCall"
                    class="btn btn-sm btn-light">

                <i class="bi bi-dash-lg"></i>

            </button>

        </div>

    </div>

    <div class="call-body">

        <h2 id="callTimer">
            00:00
        </h2>

        <div class="call-controls">

            <button id="muteBtn"
                    class="btn btn-light rounded-circle">

                <i class="bi bi-mic-fill"></i>

            </button>

            <button id="speakerBtn"
                    class="btn btn-light rounded-circle">

                <i class="bi bi-volume-up-fill"></i>

            </button>

            <button id="endCall"
                    class="btn btn-danger rounded-circle">

                <i class="bi bi-telephone-x-fill"></i>

            </button>

        </div>

    </div>

</div> -->

<div id="callBubble" class="call-bubble d-none">

    <i class="bi bi-telephone-fill"></i>

    <div id="bubbleTimer">

        00:00

    </div>

</div>

<audio id="remoteAudio" autoplay playsinline></audio>

<div class="modal fade" id="incomingCallModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body text-center">

                <h4>📞 Incoming Call</h4>

                <p id="incomingCallerName">Someone is calling...</p>

                <!-- ADD THIS -->
                <input type="hidden" id="incomingCallId">

                <!-- Existing -->
                <input type="hidden" id="incomingCallerId">

                <button class="btn btn-success" id="acceptCall">
                    Accept
                </button>

                <button class="btn btn-danger" data-bs-dismiss="modal">
                    Reject
                </button>

            </div>

        </div>
    </div>
</div>

<footer class="app-footer d-flex align-items-center justify-content-between">
    <span class="b6-text opacity-40">© {{ date('Y') }} <a href="http://itassetmanagement.in"
            target="_blank">itassetmanagement.in</a> — All rights reserved.</span>
    @if (config('app.version'))
        <div class="d-flex gap-3 me-4">
            <div class="d-flex gap-2">
                <a href="https://apps.apple.com/in/app/it-asset-management-greenitco/id1607276457" target="_blank"
                    class="pad-rgt pull-right">

                    <svg fill="#7F7F7F" width="16px" height="16px" viewBox="0 0 32 32"
                        xmlns="http://www.w3.org/2000/svg">
                        <title />
                        <g id="Apple">
                            <path
                                d="M26.49,30H5.5A3.35,3.35,0,0,1,3,29a3.35,3.35,0,0,1-1-2.48V5.5A3.35,3.35,0,0,1,3,3,3.35,3.35,0,0,1,5.5,2h21A3.35,3.35,0,0,1,29,3,3.35,3.35,0,0,1,30,5.5v21A3.52,3.52,0,0,1,26.49,30ZM13.33,24.73a2.39,2.39,0,0,0,.76-.14l.67-.24.72-.23A3.46,3.46,0,0,1,16.4,24a4.09,4.09,0,0,1,1.6.35,3.64,3.64,0,0,0,1.47.35A2.52,2.52,0,0,0,21.1,24a8.81,8.81,0,0,0,1.26-1.44,8.25,8.25,0,0,0,1-1.7,4,4,0,0,0,.28-.82,3.88,3.88,0,0,1-1.22-.76,3.48,3.48,0,0,1-1.26-2.74,3.86,3.86,0,0,1,1.44-3,1.9,1.9,0,0,1,.57-.37,3.34,3.34,0,0,0-.91-.89,5.59,5.59,0,0,0-1-.52,3.83,3.83,0,0,0-.9-.25,5,5,0,0,0-.6-.07H19.4a5,5,0,0,0-1.73.36,6.27,6.27,0,0,1-1.53.44,3.69,3.69,0,0,1-1-.26,6.76,6.76,0,0,0-2.07-.47A4.56,4.56,0,0,0,9.2,13.74a5.63,5.63,0,0,0-.79,3A9.43,9.43,0,0,0,9,19.87a10.08,10.08,0,0,0,1.39,2.66A8.16,8.16,0,0,0,11.7,24a2.28,2.28,0,0,0,1.49.69Zm2.8-13.4A3.44,3.44,0,0,0,17.56,11a3.6,3.6,0,0,0,1.21-.92,3.57,3.57,0,0,0,1-2.4l0-.37A4,4,0,0,0,17,8.6a3.36,3.36,0,0,0-1,2.3,3.11,3.11,0,0,0,0,.43Z" />
                        </g>
                    </svg>
                </a>
                <a href="https://play.google.com/store/apps/details?id=com.greenitco.itm&pcampaignid=web_share"
                    target="_blank" class="pad-rgt pull-right">

                    <svg fill="#7F7F7F" height="16px" width="16px" version="1.1" id="Icons"
                        xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="0 0 32 32" xml:space="preserve">
                        <g>
                            <path d="M17,14.5l4.2-4.5L4.9,1.2C4.8,1.1,4.6,1.1,4.3,1L17,14.5z" />
                            <path d="M23,21l5.9-3.2c0.7-0.4,1.1-1,1.1-1.8s-0.4-1.5-1.1-1.8L23,11l-4.7,5L23,21z" />
                            <path d="M2.4,1.9C2.1,2.2,2,2.6,2,3V29c0,0.4,0.1,0.8,0.4,1.2L15.6,16L2.4,1.9z" />
                            <path d="M17,17.5L4.3,31c0.2,0,0.4-0.1,0.6-0.2L21.2,22L17,17.5z" />
                        </g>
                    </svg>
                </a>
            </div>
            <a href="{{ url('versions') }}" target="_blank"><span class="b6-text opacity-40">Version:
                    {{ config('app.version') }}</span></a>
        </div>
    @endif
</footer>

<style>
    #callFab{

        position:fixed;

        bottom:30px;

        right:30px;

        width:65px;

        height:65px;

        z-index:1080;

        font-size:24px;

        display:flex;

        align-items:center;

        justify-content:center;

        transition:.3s;
    }

    #callFab:hover{

        transform:scale(1.08);

    }

    #technicianList{

        max-height:500px;

        overflow-y:auto;

    }

    .call-widget{

position:fixed;
right:20px;
bottom:20px;

width:320px;

background:#fff;

border-radius:16px;

overflow:hidden;

box-shadow:0 10px 30px rgba(0,0,0,.20);

z-index:999999;

}

.call-header{

background:#198754;

padding:15px;

display:flex;

justify-content:space-between;

align-items:center;

color:#fff;

cursor:move;

}

.call-avatar{

width:48px;

height:48px;

object-fit:cover;

}

.call-body{

padding:25px;

text-align:center;

}

.call-controls{

display:flex;

justify-content:center;

gap:18px;

margin-top:25px;

}

.call-controls button{

width:55px;

height:55px;

}

.call-bubble{

position:fixed;

right:20px;

bottom:20px;

width:70px;

height:70px;

background:#198754;

color:#fff;

border-radius:50%;

display:flex;

flex-direction:column;

justify-content:center;

align-items:center;

cursor:pointer;

box-shadow:0 10px 25px rgba(0,0,0,.25);

z-index:999999;

}
</style>

<script>
    $(document).on('click', 'a[href]', function (e) {
        const url = $(this).attr('href');
        if (!url || url.startsWith('#') || url.startsWith('javascript:')) {
            return;
        }
        const destination = new URL(url, window.location.origin);
        if (destination.hostname === window.location.hostname) {
            return;
        }

        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Leaving ITM Portal',
            html: `
                <p>You are about to visit an external website.</p>
                <p><strong>${destination.hostname}</strong></p>
                <p>Please verify the destination before continuing.</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'Continue',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(destination.href, '_blank');
            }
        });

    });

    // document.addEventListener("DOMContentLoaded", function () {
    //     const callModal = new bootstrap.Modal(document.getElementById("callModal"));
    //     document.getElementById("callFab").addEventListener("click", function () {
    //         callModal.show();
    //         loadTechnicians();
    //     });

    //     function loadTechnicians(search = '', page = 1) {

    //         let storedCompany = JSON.parse(localStorage.getItem("Default_Company") || "null");

    //         let companyId = storedCompany ? storedCompany.id : 0;

    //         if (!companyId) {
    //             $("#technicianList").html(`
    //                 <div class="alert alert-warning mb-0">
    //                     Please select a company first.
    //                 </div>
    //             `);
    //             return;
    //         }

    //         $("#technicianList").html(`
    //             <div class="text-center p-4">
    //                 <div class="spinner-border spinner-border-sm text-success"></div>
    //             </div>
    //         `);

    //         $.ajax({

    //             url: "{{ url('getUserByQuery') }}",

    //             type: "GET",

    //             dataType: "json",

    //             data: {
    //                 search: search,
    //                 page: page,
    //                 company_id: companyId,
    //                 company_access_via: 1
    //             },

    //             success: function (response) {

    //                 renderTechnicians(response.results);

    //             },

    //             error: function () {

    //                 $("#technicianList").html(`
    //                     <div class="alert alert-danger">
    //                         Unable to load technicians.
    //                     </div>
    //                 `);

    //             }

    //         });

    //     }

    //     function renderTechnicians(users) {

    //         let html = '';

    //         if (!users || users.length === 0) {
    //             html = `
    //                 <div class="text-center text-muted py-4">
    //                     No technicians found.
    //                 </div>
    //             `;

    //             $("#technicianList").html(html);
    //             return;
    //         }

    //         $.each(users, function (index, user) {

    //             html += `
    //                 <div class="card mb-2 shadow-sm border-0">
    //                     <div class="card-body d-flex justify-content-between align-items-center">

    //                         <div class="d-flex align-items-center">

    //                             <img src="${user.img_path}"
    //                                 class="rounded-circle"
    //                                 width="45"
    //                                 height="45">

    //                             <div class="ms-3">

    //                                 <div class="fw-semibold">
    //                                     ${user.first_name} ${user.last_name}
    //                                 </div>

    //                                 <small class="text-muted">
    //                                     ${user.username}
    //                                 </small>

    //                             </div>

    //                         </div>

    //                         <button
    //                             class="btn btn-success btn-sm startCall"
    //                             data-id="${user.id}"
    //                             data-name="${user.first_name} ${user.last_name}"
    //                             data-image="${user.img_path}">
    //                             <i class="bi bi-telephone-fill"></i>
    //                         </button>

    //                     </div>
    //                 </div>
    //             `;
    //         });

    //         $("#technicianList").html(html);
    //     }

    //     let typingTimer;
    //     $("#searchTechnician").on("keyup", function () {

    //         clearTimeout(typingTimer);

    //         let keyword = $(this).val();

    //         typingTimer = setTimeout(function () {

    //             loadTechnicians(keyword);

    //         },300);

    //     });

    //     $(document).on("click", ".startCall", function () {

    //         let technicianId = $(this).data("id");
    //         let technicianName = $(this).data("name");
    //         let technicianImage = $(this).data("image");
    //         window.currentCallUser = {

    //             id: technicianId,
    //             name: technicianName,
    //             image: technicianImage

    //         };

    //         $.ajax({
    //             url: "{{ url('call/start') }}",
    //             type: "POST",
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             data: {
    //                 receiver_id: technicianId,
    //                 type: 'audio'
    //             },
    //             success: function (res) {
    //                 bootstrap.Modal.getInstance(document.getElementById("callModal")).hide();
    //                 window.currentCallUser = {
    //                     id: technicianId,
    //                     name: technicianName,
    //                     image: technicianImage
    //                 };
    //                 CallWidget.show(technicianName, technicianImage);

    //             }
    //         });

    //     });

    //     $("#acceptCall").click(function () {

    //         let callId = $("#incomingCallId").val();

    //         console.log("Call ID =", callId);

    //         $.ajax({
    //             url: "{{ url('call/accept') }}",
    //             type: "POST",
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             data: {
    //                 call_id: callId
    //             },
    //             success: function (res) {

    //                 console.log(res);

    //                 bootstrap.Modal.getInstance(
    //                     document.getElementById("incomingCallModal")
    //                 ).hide();

    //                 CallWidget.show(
    //                     window.currentCallUser.name,
    //                     window.currentCallUser.image
    //                 );

    //             }
    //         });

    //     });

    //     $("#endCall").click(function () {

    //         let callId = WebRTC.callId;

    //         if (!callId) {

    //             callId = $("#incomingCallId").val();

    //         }

    //         $.ajax({

    //             url: "{{ url('call/end') }}",

    //             type: "POST",

    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },

    //             data: {
    //                 call_id: callId
    //             },

    //             success: function (res) {

    //                 console.log(res);

    //                 // WebRTC Connection Stop
    //                 WebRTC.stop();

    //                 // Hide Floating Widget
    //                 CallWidget.hide();

    //                 // Agar Bubble minimize me ho to usko bhi hide kar do
    //                 $("#callBubble").addClass("d-none");

    //             },

    //             error: function (xhr) {

    //                 console.log(xhr.responseJSON);

    //             }

    //         });

    //     });
    // });
</script>
<script>

    // window.USER_ID = {{ Auth::id() }};

    // document.addEventListener("DOMContentLoaded", function () {

    //     const callChannel = window.Echo.private('call.' + window.USER_ID);

    //     // Incoming Call
    //     // callChannel.listen('.incoming.call', function (e) {

    //     //     console.log("Incoming Call");
    //     //     console.log(e);

    //     //     $("#incomingCallId").val(e.call_id);
    //     //     $("#incomingCallerId").val(e.caller_id);

    //     //     const modal = new bootstrap.Modal(
    //     //         document.getElementById("incomingCallModal")
    //     //     );

    //     //     modal.show();
    //     // });

    //     callChannel.listen('.incoming.call', function (e) {

    //         $("#incomingCallId").val(e.call_id);
    //         $("#incomingCallerId").val(e.caller_id);

    //         $("#incomingCallerName").text(e.caller_name);

    //         window.currentCallUser = {

    //             id: e.caller_id,
    //             name: e.caller_name,
    //             image: e.caller_image

    //         };

    //         const modal = new bootstrap.Modal(
    //             document.getElementById("incomingCallModal")
    //         );

    //         modal.show();

    //     });

    //     callChannel.listen('.call.accepted', async function (e) {

    //         console.log("Call Accepted");
    //         console.log(e);

    //         await WebRTC.init(
    //             e.call_id,
    //             e.receiver_id,
    //             true
    //         );

    //         await WebRTC.createOffer();
    //         CallWidget.show(
    //             window.currentCallUser.name,
    //             window.currentCallUser.image
    //         );

    //         CallWidget.setStatus("Connected");

    //         WebRTC.startTimer();
    //     });

    //     // WebRTC Signal
    //     callChannel.listen('.webrtc.signal', async function (e) {

    //         console.log("Signal Received");
    //         console.log(e);

    //         switch (e.type) {

    //             case "offer":

    //                 console.log("Offer Received");

    //                 await WebRTC.init(
    //                     e.call_id,
    //                     e.from_user_id,
    //                     false
    //                 );

    //                 let offer = typeof e.data === "string"
    //                     ? JSON.parse(e.data)
    //                     : e.data;

    //                 await WebRTC.setRemoteDescription(offer);

    //                 await WebRTC.createAnswer();
    //                 CallWidget.show(
    //                     window.currentCallUser.name,
    //                     window.currentCallUser.image
    //                 );

    //                 CallWidget.setStatus("Connected");

    //                 WebRTC.startTimer();
    //                 break;


    //             case "answer":

    //                 console.log("Answer Received");

    //                 let answer = typeof e.data === "string"
    //                     ? JSON.parse(e.data)
    //                     : e.data;

    //                 await WebRTC.setRemoteDescription(answer);

    //                 break;


    //             case "ice":

    //                 console.log("ICE Candidate Received");

    //                 let candidate = typeof e.data === "string"
    //                     ? JSON.parse(e.data)
    //                     : e.data;

    //                 await WebRTC.addIceCandidate(candidate);

    //                 break;
    //         }

    //     });

    //     callChannel.listen(".call.ended", function (e) {

    //         console.log("Call Ended Event");
    //         console.log(e);

    //         // Stop WebRTC
    //         WebRTC.stop();

    //         // Hide Incoming Modal (agar open ho)
    //         const incomingModal = bootstrap.Modal.getInstance(
    //             document.getElementById("incomingCallModal")
    //         );

    //         if (incomingModal) {
    //             incomingModal.hide();
    //         }

    //         // Hide Floating Call Widget
    //         CallWidget.hide();

    //     });

    // });
</script>
<script>
    // window.Signal = {
    //     send(callId, toUserId, type, data) {
    //         return $.ajax({
    //             url: "{{ url('call/signal') }}",
    //             type: "POST",
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             data: {
    //                 call_id: callId,
    //                 to_user_id: toUserId,
    //                 type: type,
    //                 data: JSON.stringify(data)
    //             },
    //             success: function(res) {
    //                 console.log("Signal Sent:", type);
    //             },
    //             error: function(xhr) {
    //                 console.log("Signal Error:", xhr.responseJSON);
    //             }
    //         });
    //     }
    // };
</script>