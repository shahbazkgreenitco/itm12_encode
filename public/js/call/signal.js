window.Signal = {

    send(callId, toUserId, type, data) {

        return $.ajax({

            url: "/itm12/call/signal",

            type: "POST",

            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            data: {

                call_id: callId,

                to_user_id: toUserId,

                type: type,

                data: JSON.stringify(data)

            }

        });

    }

};