console.log("WebRTC JS Loaded");

window.WebRTC = {

    peer: null,
    localStream: null,
    remoteStream: null,
    isCaller: false,
    pendingCandidates: [],
    remoteDescriptionSet: false,
    callId: null,
    remoteUserId: null,
    timer: null,
    seconds: 0,
    async init(callId, remoteUserId, isCaller = false) {

        this.callId = callId;
        this.remoteUserId = remoteUserId;
        this.isCaller = isCaller;

        await this.createPeer();

        await this.getLocalStream();

    },

    async createPeer() {

        this.peer = new RTCPeerConnection({

            iceServers: [

                {
                    urls: [
                        "stun:stun.l.google.com:19302",
                        "stun:stun1.l.google.com:19302"
                    ]
                }

            ]

        });

        console.log("Peer Connection Created");

        this.registerEvents();

    },

    async getLocalStream() {

        this.localStream = await navigator.mediaDevices.getUserMedia({

            audio: true,
            video: false

        });

        this.localStream.getTracks().forEach(track => {

            this.peer.addTrack(track, this.localStream);

        });

        console.log("Local Stream Ready");

    },

    registerEvents() {

        this.remoteStream = new MediaStream();

        this.peer.ontrack = (event) => {

            event.streams[0].getTracks().forEach(track => {

                this.remoteStream.addTrack(track);

            });

            const audio = document.getElementById("remoteAudio");

            if (audio) {

                audio.srcObject = this.remoteStream;

            }

        };

       //this.peer.onconnectionstatechange = () => {

        //     console.log("Connection :", this.peer.connectionState);

        //     if (this.peer.connectionState === "connected") {

        //         console.log("🎉 Audio Connected");

        //         $(".calling-status").text("Connected");

        //     }

        //     if (
        //         this.peer.connectionState === "disconnected" ||
        //         this.peer.connectionState === "failed"
        //     ) {

        //         WebRTC.stop();

        //     }

        // };

        this.peer.onconnectionstatechange = () => {

            console.log("Connection State :", this.peer.connectionState);

            switch (this.peer.connectionState) {

                case "connected":

                    console.log("Call Connected");

                    this.startTimer();

                    $(".calling-status").text("Connected");

                    break;

                case "disconnected":
                case "failed":
                case "closed":

                    this.stop();

                    break;
            }

        };

        this.peer.oniceconnectionstatechange = () => {

            console.log("ICE State :", this.peer.iceConnectionState);

        };

        this.peer.onicecandidate = (event) => {

            if (!event.candidate) return;

            console.log("ICE Candidate");

            console.log(event.candidate);

            Signal.send(

                this.callId,

                this.remoteUserId,

                "ice",

                event.candidate

            );

        };

    },

    async createOffer() {

        const offer = await this.peer.createOffer({

            offerToReceiveAudio: true

        });

        await this.peer.setLocalDescription(offer);

        console.log("Offer Created");

        console.log(offer);

        Signal.send(

            this.callId,

            this.remoteUserId,

            "offer",

            offer

        );

        return offer;

    },

    async createAnswer() {

        const answer = await this.peer.createAnswer();

        await this.peer.setLocalDescription(answer);

        console.log("Answer Created");

        console.log(answer);

        Signal.send(

            this.callId,

            this.remoteUserId,

            "answer",

            answer

        );

        return answer;

    },

    async setRemoteDescription(description) {

        await this.peer.setRemoteDescription(
            new RTCSessionDescription(description)
        );

        this.remoteDescriptionSet = true;

        // Process queued ICE candidates
        while (this.pendingCandidates.length > 0) {

            const candidate = this.pendingCandidates.shift();

            await this.peer.addIceCandidate(
                new RTCIceCandidate(candidate)
            );
        }
    },

    async addIceCandidate(candidate) {

        if (!this.remoteDescriptionSet) {

            this.pendingCandidates.push(candidate);

            return;
        }

        await this.peer.addIceCandidate(
            new RTCIceCandidate(candidate)
        );
    },

    // startTimer() {

    //     if (this.timer) {
    //         return;
    //     }

    //     this.seconds = 0;

    //     this.timer = setInterval(() => {

    //         this.seconds++;

    //         let minutes = Math.floor(this.seconds / 60);
    //         let seconds = this.seconds % 60;

    //         $("#callTimer").text(
    //             String(minutes).padStart(2, "0") +
    //             ":" +
    //             String(seconds).padStart(2, "0")
    //         );

    //     }, 1000);

    // },
startTimer() {

    if (this.timer) return;

    this.seconds = 0;

    this.timer = setInterval(() => {

        this.seconds++;

        let min = Math.floor(this.seconds / 60);
        let sec = this.seconds % 60;

        let time =
            String(min).padStart(2, "0") +
            ":" +
            String(sec).padStart(2, "0");

        $("#callTimer").text(time);
        $("#bubbleTimer").text(time);

    },1000);

},
stopTimer() {

    if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
    }

    this.seconds = 0;

    $("#callTimer").text("00:00");
    $("#bubbleTimer").text("00:00");

},
   stop() {

    WebRTC.stopTimer();

    if (this.localStream) {

        this.localStream.getTracks().forEach(track => {

            track.stop();

        });

    }

    if (this.peer) {

        this.peer.close();

    }

    this.peer = null;
    this.localStream = null;
    this.remoteStream = null;
    this.pendingCandidates = [];
    this.remoteDescriptionSet = false;

    CallWidget.hide();

    console.log("Call Ended");

},

    // stop() {

    //     if (this.localStream) {

    //         this.localStream.getTracks().forEach(track => {

    //             track.stop();

    //         });

    //     }

    //     if (this.peer) {

    //         this.peer.close();

    //     }

    //     this.peer = null;

    //     console.log("Call Ended");

    // }

    

};