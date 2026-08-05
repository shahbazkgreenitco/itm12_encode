sweetAlert = function (position_data, icon_data, data, iconColor = '', iconSize = '8px') {
    Swal.fire({
        position: position_data,
        icon: icon_data,
        title: data.msg,
        confirmButtonColor: '#5bd810',
        showCloseButton: true,
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'swal2-confirm-custom'
        },
        didOpen: () => {
            const confirmButton = document.querySelector('.swal2-confirm');
            if (confirmButton) {
                confirmButton.style.minWidth = '50px';
            }
            const iconElement = document.querySelector('.swal2-icon');
            if (iconElement) {
                iconElement.style.fontSize = iconSize;
                iconElement.style.color = iconColor;
                iconElement.style.border = `2px solid ${iconColor}`;
            }
        }
    });
}

sweetAlerts = function (title_data, icon_data, url, table, data, option = 'null', iconColor = '', iconSize = '8px', confirmButtonText = 'Yes', cancelButtonText = 'Cancel') {
    if (typeof option === 'undefined') {
        option = {};
    }

    Swal.fire({
        title: title_data,
        icon: icon_data,
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#5bd810',
        cancelButtonColor: '#cc3333',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        },
        didOpen: () => {
            const confirmButton = document.querySelector('.swal2-confirm');
            const cancelButton = document.querySelector('.swal2-cancel');
            if (confirmButton && cancelButton) {
                confirmButton.style.minWidth = '50px';
                cancelButton.style.minWidth = '50px';
            }
            const iconElement = document.querySelector('.swal2-icon');
            if (iconElement) {
                iconElement.style.fontSize = iconSize;
                iconElement.style.color = iconColor;
                iconElement.style.border = `2px solid ${iconColor}`;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var http = $.get(url)
            http.done(function (data) {
                if (typeof data === "object") {
                    if (data.status === "success") {
                        // console.log(option);
                        sweetAlert('center', 'success', data)
                        if (option === 'url_yes') {
                            window.location = table;
                        } else if (option === 'remove_row') {
                            table.remove().draw();
                        }  else if (option === 'modal_hide') {
                            table.modal("hide");
                        } else if (option === 'reload') {
                            table.reload();
                        } else {
                            table.ajax.reload();
                        }
                    } else {
                        sweetAlert('center', 'error', data)
                    }
                }
            })
            .fail(function () {
                sweetAlert('center', 'error', data)
            })
            // .always(function () {
            //     t.httpCall = true;
            // });
        }
    });
}

sweetAlertPost = function (title_data, icon_data, url, table, data, send_data, option = 'null', iconColor = '', iconSize = '8px',) {
    if (typeof option === 'undefined') {
        option = {};
    }

    Swal.fire({
        title: title_data,
        icon: icon_data,
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#5bd810',
        cancelButtonColor: '#cc3333',
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        customClass: {
            confirmButton: 'swal2-confirm-custom',
            cancelButton: 'swal2-cancel-custom'
        },
        didOpen: () => {
            const confirmButton = document.querySelector('.swal2-confirm');
            const cancelButton = document.querySelector('.swal2-cancel');
            if (confirmButton && cancelButton) {
                confirmButton.style.minWidth = '50px';
                cancelButton.style.minWidth = '50px';
            }
            const iconElement = document.querySelector('.swal2-icon');
            if (iconElement) {
                iconElement.style.fontSize = iconSize;
                iconElement.style.color = iconColor;
                iconElement.style.border = `2px solid ${iconColor}`;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            var http = $.ajax({
                url: url,
                type: "POST",
                data: send_data,
            });

            http.done(function (data) {
                if (typeof data === "object") {
                    if (data.status === "success") {
                        sweetAlert('center', 'success', data);
                        if (option === 'url_yes') {
                            window.location = table;
                        } else {
                            table.reload();
                            $('.chkParent').prop('checked', false);
                        }
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                }
            }).fail(function () {
                sweetAlert('center', 'error', data);
            })
            // .always(function () {
            //     t.httpCall = true;
            // });
        }
    });
};

function sweetAlertConfirm(options) {
    Swal.fire({
        title: options.message,
        icon:'warning',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor:'#5bd810',
        cancelButtonColor:'#cc3333',
        confirmButtonText:'Yes',
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            if (options.beforeSend) {
                options.beforeSend();
            }

            $.ajax({
                url: options.url,
                type:'POST',
                data: options.data,
                success: function(data) {
                    if (typeof data === "object") {
                        if (data.status === "success") {
                            if (options.onSuccess) {
                                options.onSuccess(data);
                                sweetAlert('center', 'success', data);
                            }
                        } else {
                            sweetAlert('center', 'error', data);
                        }
                    }
                },
                error: function() {
                    var errorMsg = {
                        'msg': 'Something went wrong. Please check the given details are correct.'
                    };
                    sweetAlert('center', 'error', errorMsg);
                },
                complete: function() {
                    if (options.complete) {
                        options.complete();
                    }
                }
            });
        }
    });
}

function sweetAlertConfirmation(options) {
    Swal.fire({
        title: options.message,
        icon: 'warning',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#5bd810',
        cancelButtonColor: '#cc3333',
        confirmButtonText: options.confirmButtonText || 'Yes',
        cancelButtonText: options.cancelButtonText || 'Cancel',
        allowOutsideClick: false,
    }).then((result) => {
        if (result.isConfirmed) {
            if (options.onConfirm) {
                options.onConfirm();
            }
        } else if (result.isDismissed) {
            if (options.onCancel) {
                options.onCancel();
            }
        }

    });
}

function showConfirmationDialog(title, text, confirmButtonText, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#5bd810",
        cancelButtonColor: "#d33",
        confirmButtonText: confirmButtonText
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}
