window.CallWidget = {

    show(name, image) {

        $("#widgetName").text(name);
        $("#widgetImage").attr("src", image);

        $(".calling-status").text("Connecting...");

        $("#callWidget").removeClass("d-none");
        $("#callBubble").addClass("d-none");
    },

    hide() {

        $("#callWidget").addClass("d-none");
        $("#callBubble").addClass("d-none");

        $("#callTimer").text("00:00");
        $("#bubbleTimer").text("00:00");

        $(".calling-status").text("Connecting...");
    },

    minimize() {

        $("#callWidget").addClass("d-none");
        $("#callBubble").removeClass("d-none");
    },

    restore() {

        $("#callBubble").addClass("d-none");
        $("#callWidget").removeClass("d-none");
    },

    setStatus(status) {

        $(".calling-status").text(status);

    }

};

$("#minimizeCall").click(function () {

    CallWidget.minimize();

});

$("#callBubble").click(function () {

    CallWidget.restore();

});

$("#muteBtn").click(function () {

    if (!WebRTC.localStream) return;

    let track = WebRTC.localStream.getAudioTracks()[0];

    track.enabled = !track.enabled;

    if (track.enabled) {

        $(this).html('<i class="bi bi-mic-fill"></i>');

    } else {

        $(this).html('<i class="bi bi-mic-mute-fill"></i>');

    }

});

$("#speakerBtn").click(function () {

    let audio = document.getElementById("remoteAudio");

    audio.muted = !audio.muted;

    if (audio.muted) {

        $(this).html('<i class="bi bi-volume-mute-fill"></i>');

    } else {

        $(this).html('<i class="bi bi-volume-up-fill"></i>');

    }

});