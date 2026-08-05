var MyApp = function(config) {
    var t = this;
    t.config = config;
    t.token = $('head meta[name="csrf-token"]');

    t.page = $("#page_boxed");
    t.content = $(".content");
    t.mdl_popup_loader = t.page.find("#mdl_popup_loader");
    t.tkt_content = t.page.find('#tkt-content');
    t.main_attachments = t.tkt_content.find('.main_attachments');

    function decodeURIComponentSafe(uri, mod) {
        var out = new String(),
            arr,
            i = 0,
            l,
            x;
        typeof mod === "undefined" ? mod = 0 : 0;
        arr = uri.split(/(%(?:d0|d1)%.{2})/);
        for (l = arr.length; i < l; i++) {
            try {
                x = decodeURIComponent(arr[i]);
            } catch (e) {
                x = mod ? arr[i].replace(/%(?!\d+)/g, '%25') : arr[i];
            }
            out += x;
        }
        return out;
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    window.KDImagePreview = {
        show: function(viewUrl, fileName) {
            var preview = $("#kd-image-preview-modal");
            var previewUrl = viewUrl + (viewUrl.indexOf("?") === -1 ? "?" : "&") + "_=" + Date.now();
            if (preview.length == 0) {
                $("body").append(
                    '<div id="kd-image-preview-modal" class="amg-modal modal fade kd-image-modal" tabindex="-1" aria-hidden="true">' +
                        '<div class="modal-dialog modal-xl modal-dialog-centered">' +
                            '<div class="modal-content rounded-4">' +
                                '<div class="modal-header">' +
                                    '<h5 class="modal-title kd-image-preview-title"></h5>' +
                                    '<button type="button" class="modal-close kd-image-preview-close" data-bs-dismiss="modal" aria-label="Close" title="Close"><i class="bi bi-x-lg"></i></button>' +
                                '</div>' +
                                '<div class="modal-body kd-image-preview-body"><div class="kd-image-preview-message">Loading preview...</div><img src="" alt=""></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
                preview = $("#kd-image-preview-modal");
            }

            preview.find(".kd-image-preview-title").text(fileName || "");
            preview.find(".kd-image-preview-message").removeClass("error").text("Loading preview...").show();
            preview.find("img")
                .hide()
                .off("load.kdImagePreview error.kdImagePreview")
                .on("load.kdImagePreview", function() {
                    preview.find(".kd-image-preview-message").hide();
                    $(this).show();
                })
                .on("error.kdImagePreview", function() {
                    preview.find(".kd-image-preview-message").addClass("error").text("Unable to load image preview.").show();
                    $(this).hide().attr("src", "");
                })
                .attr("alt", fileName || "")
                .attr("src", previewUrl);
            preview.off("hidden.bs.modal.kdImagePreview").on("hidden.bs.modal.kdImagePreview", function() {
                $(this).find("img").attr("src", "");
            });

            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(preview[0]).show();
            } else if ($.isFunction(preview.modal)) {
                preview.modal("show");
            } else {
                preview.addClass("show").css("display", "block").attr("aria-modal", "true").removeAttr("aria-hidden");
            }
        },
        hide: function() {
            var preview = $("#kd-image-preview-modal");
            if (!preview.length) {
                return;
            }

            if (window.bootstrap && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(preview[0]);
                if (modal) {
                    modal.hide();
                    return;
                }
            } else if ($.isFunction(preview.modal)) {
                preview.modal("hide");
                return;
            }

            preview.removeClass("show").css("display", "none").attr("aria-hidden", "true").removeAttr("aria-modal").find("img").attr("src", "");
        }
    };

    t.setMainAttachments = function() {
        var at = [];
        $.each(t.config.main_attachments, function(i, v) {
            var sext = $.trim(v.ext || "").toLowerCase().replace(/^\./, "");
            var fileName = decodeURIComponentSafe(unescape(v.name || v.original_file_name || ""));
            var safeFileName = escapeHtml(fileName);
            var viewUrl = t.config.url.attachment_view + "/" + v.id;
            var previewUrl = viewUrl + "?preview=1";
            var eye_link = '';
            var download_link = '<button type="button" class="kd-attach-action tri-download" + data-bs-toggle="tooltip" + data-bs-placement="top"+ data-url="' + t.config.url.attachment_download + "/" + v.id + '" + title="Download"><i class="bi bi-download"></i></button>';

            if (["png", "jpeg", "jpg", "gif", "webp", "bmp"].includes(sext)) {
                eye_link = '<button type="button" class="kd-attach-action image-view" data-view_mode="1" data-view="' + previewUrl + '" data-name="' + safeFileName + '" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="bi bi-eye-fill"></i></button>';
            }

            at.push('<div class="attach-item kd-attachment-card">' + '<div class="attach-item-cntnt">' + '<span class="attach-name" data-bs-toggle="tooltip" data-bs-placement="top" title="' + safeFileName + '">' + safeFileName + '</span>' + '<div class="icons">' + eye_link + '<button type="button" class="kd-attach-action tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '" data-bs-toggle="tooltip" data-bs-placement="top"  title="Download"><i class="bi bi-download"></i></button>' + "</div></div></div>");
        });

        if( at.length > 0 ) {
            t.main_attachments.html('<div class="kd-attachment-list">' + at.join("") + '</div>');
            $("#article-info-page").find(".attach-doc").removeClass("d-none");
        }
    }

    t.attachmentView = function(e) {
        e.preventDefault();
        var viewUrl = $(this).attr('data-view');
        var fileName = $(this).attr('data-name');

        t.showImagePreview(viewUrl, fileName);
    };

    t.showImagePreview = function(viewUrl, fileName) {
        window.KDImagePreview.show(viewUrl, fileName);
    };

    t.closeImagePreview = function(e) {
        e.preventDefault();
        window.KDImagePreview.hide();
    };

    t.fileView = function(e) {
        e.preventDefault();
        window.open($(this).attr('data-view'), '_blank');
    };

    t.attachmentDownload = function(e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }
    
    /* mdl update ticket tags */
    t.mdlManageTags = t.page.find("#mdl-add-tag");
    t.mdlManageTags.assigned_tags = t.mdlManageTags.find("#assigned_tags");
    t.mdlManageTags.getTicketTags = t.page.find(".getTicketTags");
    t.mdlManageTags.saveTags = t.mdlManageTags.find("#btnSubmit");
    t.mdlManageTags.assigned_tags.select2({
        width:"100%",
        tags: true,
        maximumSelectionLength: 10,
        tokenSeparators: [','],
        ajax: {
            url: function (params) {
                return t.config.url.getTagDetails;
            },
            dataType: "json",
            delay: 250,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page,
                }
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total
                    }
                }
            },
            cache: true
        }
    });

    t.getTicketTagDetails = function() {
        $.get(t.config.url.get_data_for_transfer + "/" + t.data.id, function(result) {
            t.mdlManageTags.assigned_tags.html("");
            $.each( result.data.tags.tags, function( key, value ) {
                var newOption = new Option(value.tags, value.tags, true, true);
                t.mdlManageTags.assigned_tags.append(newOption);
            });
        })
    }

    t.saveTicketDetailTags = function(e) {
        e.preventDefault();
        var myformData = new FormData();
        myformData.append('ticketId',t.mdlManageTags.find('#id').val());
        myformData.append('tags',t.mdlManageTags.assigned_tags.val());
        $.ajax({
            method: 'post',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            url: t.config.url.updateTicketTags,
            success:function(res) {
                if(res.status == 'success') {
                    sweetAlert('center', 'success', res);
                    window.location.reload();
                } else {
                    sweetAlert('center', 'error', res);
                }
            }
        })
    }

    t.mdlManageTags.getTicketTags.on('click',t.getTicketTagDetails);
    t.mdlManageTags.saveTags.on('click',t.saveTicketDetailTags);

    t.setMainAttachments();
    t.main_attachments.on("click", ".image-view", $.proxy(t.attachmentView));
    t.main_attachments.on("click", ".file-view", $.proxy(t.fileView));
    t.main_attachments.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    $(document).on("click", "#kd-image-preview-modal .kd-image-preview-close", $.proxy(t.closeImagePreview));
};
