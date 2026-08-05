var Article = function (config) {
	var t = this;
	t.config = config;
    t.page = $("#page_boxed");
	t.httpCall = true;
	t.httpPostPath = "";
	t.pagebtns = t.page.find("#pagebtns1");
	t.pageBtmSummary = t.page.find("#page-btm-summary1");
	t.total = 0;
	t.perPage = 10;
	t.data = {};

	t.offListen = false;
    t.toggle_tiny_view = t.page.find('#toggle_tiny_view');
    t.toggle_tiny_view_read = t.page.find('#toggle_tiny_view_read');
	t.timeline = t.page.find("#ticket_timeline");
    // t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    // t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));

    t.mdlUpdate = $("#articleupdateModal");
	t.frmUpdate = t.mdlUpdate.find("#knowledge_document_update_form");
	t.frmUpdate.extraData = {};
	t.frmUpdate.el = {};
	t.mdlUpdate.title = t.mdlUpdate.find('.modal-title');
	t.frmUpdate.el.id = t.frmUpdate.find("#id");
	t.frmUpdate.el.forAction = t.frmUpdate.find("#forAction, #for_action");
	t.frmUpdate.el.token = t.frmUpdate.find("#token");
	t.frmUpdate.el.company = t.frmUpdate.find("#company");
	t.frmUpdate.el.status = t.frmUpdate.find("#status");
	t.frmUpdate.el.departmentId = t.frmUpdate.find("#department_id");
	t.frmUpdate.el.parent_category_id = t.frmUpdate.find("#parent_category_id");
	t.frmUpdate.el.sub_category_id_cvr = t.frmUpdate.find("#sub_category_id_cvr");
	t.frmUpdate.el.sub_category_id = t.frmUpdate.find("#sub_category_id");
	t.frmUpdate.el.title = t.frmUpdate.find("#title");
	t.frmUpdate.el.tags= t.frmUpdate.find("#tags");
	t.frmUpdate.el.content = t.frmUpdate.find("#content");
	t.frmUpdate.el.btnUpdate= t.frmUpdate.find('#btnUpdate');
	t.frmUpdate.el.tmp_id = t.frmUpdate.find("#tmp_id");
    t.frmUpdate.attachment_dropper_cover = t.frmUpdate.find("#update-dropper-cover");
    t.frmUpdate.attachment_dropper = t.frmUpdate.attachment_dropper_cover.find("#update-dropper");
    t.attachment_update = t.frmUpdate.find("#attachment_updates");

    t.showModal = function(modal) {
        if (!modal || !modal.length) {
            return;
        }
        if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
            window.bootstrap.Modal.getOrCreateInstance(modal[0]).show();
            return;
        }
        modal.modal("show");
    };

    t.hideModal = function(modal) {
        if (!modal || !modal.length) {
            return;
        }
        if (window.bootstrap && typeof window.bootstrap.Modal === "function") {
            var instance = window.bootstrap.Modal.getInstance(modal[0]);
            if (instance) {
                instance.hide();
            }
            return;
        }
        modal.modal("hide");
    };

    t.resetUpdateForm = function() {
        if (!t.frmUpdate.length || !t.frmUpdate[0]) {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
            return false;
        }
        t.frmUpdate[0].reset();
        t.frmUpdate.extraData = {};
        t.frmUpdate.el.id.val("");
        t.frmUpdate.el.forAction.val("");
        t.frmUpdate.el.tmp_id.val("");
        t.frmUpdate.el.title.val("");
        t.frmUpdate.el.company.empty().val("").trigger("change");
        t.frmUpdate.el.status.val("").trigger("change");
        t.frmUpdate.el.departmentId.empty().val("").trigger("change");
        t.frmUpdate.el.parent_category_id.empty().val("").trigger("change");
        t.frmUpdate.el.sub_category_id.empty().val("").trigger("change");
        t.frmUpdate.el.sub_category_id_cvr.hide();
        t.frmUpdate.el.tags.empty().val("").trigger("change");
        t.frmUpdate.el.content.summernote("code", "");
        t.frmUpdate.find("#card_img").val("");
        t.frmUpdate.find("#delete_img").prop("checked", false);
        t.frmUpdate.find("#shows_error, .amg-form-error-wrap").empty();
        if (t.updateUploader) {
            t.updateUploader.clear();
        } else {
            t.attachment_update.empty();
        }
        if (t.frmupdValidator) {
            t.frmupdValidator.resetForm();
            t.frmUpdate.find(".error").removeClass("error");
        }
        return true;
    };

    t.frmUpdate.el.content.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
        ],
        minHeight: 200,
        focus: true
    });

	t.refreshTimeLine = function() {
        if (typeof t.config.ticket == "undefined" || t.config.ticket == "" || t.config.ticket == null) {
            return false;
        }
        var class_name = '';
        var formData = new FormData();
        formData.append('_token', t.config.token);
        formData.append('ticket_id', t.config.ticket);
        var http = $.ajax({
            url: t.config.url.get_timeline,
            type: "POST",
            processData: false,
            contentType: false,
            data: formData
        });
        http.done(function(data) {
            if (typeof data == "object") {
                if (data.status == "success") {

                    var exi_el_state = {};
                    t.timeline.find('.timeline-entry').each(function(exi_ind, exi_el) {
                        exi_el_state['e' + exi_el.id] = $(exi_el).hasClass('tiny-view-on');
                    });

                    t.timeline.empty();
                    if (data.data.length > 0) {
                        var tiny_viewer_state = t.toggle_tiny_view.hasClass('tiny-view-on');
                        $.each(data.data, function(i, v) {
                            if(v.updated_by == v.assigned_to && v.assigned_to != null){
                                class_name = "color-code-bar color-code-blue-text";
                            } else if(v.updated_by == v.creator_id && v.creator_id != null){
                                class_name = "color-code-bar color-code-rose-text";
                            } else if(v.updated_by != v.creator_id && v.creator_id != null){
                                class_name = "color-code-bar color-code-yellow-text";
                            }

                            var t1 = '<div class="timeline-stat"><div class="timeline-icon"></div><div class="timeline-time">' + v.updated_at_format + '</div></div>';
                            var t2 = v.is_note == 1 ? "Note Added By" : "Commented By";
                            var c = v.is_note == 1 ? "tl-note" : "";
                            var s = v.is_service_request == 1 ? "service-note" : "";

                            var cc_emails = v.cc_emails != "" && v.cc_emails != null ? " | CC: " + v.cc_emails : "";

                            var t3 = '<p class="mar-no pad-btm txt1 force-br">' + t2 + ' <a href="#" class="btn-link text-main text-bold '+class_name+'">' + v.commenter + '</a> ' + cc_emails + '</p>';
                            var t5 = "";
                            if (typeof v.attachments != "undefined" && v.attachments.length > 0) {
                                var at = [];
                                $.each(v.attachments, function(i, v) {
                                    var sext = v.ext.toLowerCase();
                                    var eye_link = "";
                                    if (sext == "png" || sext == "jpeg" || sext == "jpg") {
                                        eye_link = '<span class="tri-view" data-view_mode="1" data-view="' + t.config.url.ticket_attachment_view + '/' + v.id + '" data-name="' + decodeURIComponent(v.name) + '"><i class="fa fa-eye"></i></span>';
                                    }
                                    else if( sext == "xls" || sext == "xlsx" || sext == "docs" || sext == "doc" ) {
                                        eye_link = '<span class="tri-view" data-view_mode="2" data-view="' + t.config.url.ticket_attachment_view + '/' + v.id + '" data-name="' + v.name  + '"><i class="fa fa-eye"></i> View</span>';
                                    }

                                    var ai_bg = v.thumb == 1 ? "ai-bg" : "";
                                    var bg = v.thumb == 1 ? "background-image: url(" + t.config.url.ticket_attachment_view + "/" + v.id + "/1" : "";

                                    at.push('<div class="attach-item ' + ai_bg + '" style="' + bg + '" ><div class="attach-item-cntnt"><span class="attach-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span><div class="icons">' + eye_link + '<span class="tri-download" data-url="' + t.config.url.ticket_attachment_download + "/" + v.id + '"><i class="fa fa-download"></i></span></div></div></div>');

                                });
                                t5 = '<div class="attachments mar-top"><div class="text-bold mar-rgt">Attachments:</div>' + at.join("") + '</div>';
                            }
                            var tiny_view_class = tiny_viewer_state == true ? 'tiny-view-on' : '';
                            var signle_tiny_viewer = tiny_viewer_state == true ? 'fa-expand faa-fast animated' : 'fa-compress';

                            if( typeof exi_el_state['e' + v.tfid] != 'undefined' ) {
                                if( exi_el_state['e' + v.tfid] == true ) {
                                    tiny_view_class = 'tiny-view-on';
                                    signle_tiny_viewer = 'fa-expand faa-fast animated';
                                }
                                else {
                                    tiny_view_class = '';
                                    signle_tiny_viewer = 'fa-compress';
                                }
                            }
                            if(v.is_service_request == 1) {
                                var s2 = '<div class="timeline-label ' + s + '"><button class="btn btn-white btn-ex-com single-tiny-viewer"><i class="fa ' + signle_tiny_viewer + '"></i></button>' + t3 + '<div class="tml-content">' + v.remarks + '</div>' + t5 + '</div>';
                                t.timeline.append('<div id="' + v.tfid + '" class="timeline-entry tiny-view ' + tiny_view_class + '">' + t1 + s2 + '</div>');
                            } else {
                                var t4 = '<div class="timeline-label ' + c + '"><button class="btn btn-white btn-ex-com single-tiny-viewer"><i class="fa ' + signle_tiny_viewer + '"></i></button>' + t3 + '<div class="tml-content">' + v.remarks + '</div>' + t5 + '</div>';
                                t.timeline.append('<div id="' + v.tfid + '" class="timeline-entry tiny-view ' + tiny_view_class + '">' + t1 + t4 + '</div>');
                            }
                        });
                        t.timeline.removeClass("hide");
                    } else {
                        t.timeline.addClass("hide");
                    }
                } else if (data.msg != "") {
                    sweetAlert('center', 'error', data);
                }
            }
        });
    };

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

    t.setSingleTinyViewer = function(el, target_val) {
        if( target_val == true ) {
            $(el).removeClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-compress"></i>').attr('title', 'Compress');
        }
        else {
            $(el).addClass('tiny-view-on').find('.single-tiny-viewer').html('<i class="fa fa-expand faa-fast animated"></i>').attr('title', 'Expand');
        }
    }

    t.toggleSingleTinyViewer = function(e) {
        e.preventDefault();
        var el = $(this).closest('.tiny-view').get(0);
        var target_val = $(el).hasClass('tiny-view-on');        
        t.setSingleTinyViewer( el, target_val );
    };

    t.mdlManageTags = $("#mdl-add-tag");
    t.mdlManageTags.assigned_tags = t.mdlManageTags.find("#assigned_tags");
    t.mdlManageTags.getArticleTags = t.page.find(".getTicketTags");
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

    t.getArticleTagDetails = function() {
        $.get(t.config.url.get_tag + "/" + config.id, function(result) {
            t.mdlManageTags.assigned_tags.html("");
            $.each( result.data.tags.tags, function( key, value ) {
                var newOption = new Option(value.tags, value.tags, true, true);
                t.mdlManageTags.assigned_tags.append(newOption);
            });
        })
    }

    t.saveArticleDetailTags = function(e) {
        e.preventDefault();
        var myformData = new FormData();
        myformData.append('id',config.id);
        myformData.append('tags',t.mdlManageTags.assigned_tags.val());
        $.ajax({
            method: 'post',
            processData: false,
            contentType: false,
            cache: false,
            data: myformData,
            enctype: 'multipart/form-data',
            url: t.config.url.updateArticleTags,
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

    t.staring = function(e) {
        e.preventDefault();
        if (t.httpCall != true) {
            return false;
        }
        sweetAlertConfirm({
            message: config.translations.are_you_star,
            url: t.config.url.staring,
            data: {
                "_token": t.config.token,
                "id": config.id
            },
            onSuccess: function(data) {
                t.staringUi(data.star);
            },
            errorMsg: config.translations.something_went_wrong,
            beforeSend: function() {
                t.httpCall = false;
            },
            complete: function() {
                t.httpCall = true;
            }
        });
    };

    t.staringUi = function(val) {
        var s = (typeof val != "undefined") ? val : t.config.star_exsist;
        var e = t.page.find(".js-act-staring");
        if (s == true) {
            e.html('<i class="bi bi-star-fill"></i> '+ config.translations.Starred);
        } else {
            e.html('<i class="bi bi-star"></i> '+config.translations.Add_Star);
        }
    };

    t.frmupdValidator = t.frmUpdate.validate({
        rules: {
            title: {
                required: true,
            },
            company: {
                required: true,
            },
            department_id: {
                required: true,
            },
            parent_category_id: {
                required: true,
            },
            "tags[]":{
                required: true,
                clean_text_only: true,
            },
            content:{
                required: true,
            },
            status:{
                required: true,
            }
        },
        errorPlacement: function (error, element) {
            // Display the error message next to the form field
            error.appendTo( element.parent("div").parent('div'));
        }
	});

	t.problem_categories_data = {};

    t.reload_department = function() {
		t.frmUpdate.el.sub_category_id_cvr.show();
		t.frmUpdate.el.departmentId.empty().append(new Option(config.translations.No_Filter, '', false, false));
		$.get(t.config.url.departments_based_on_privilage + "/" + t.config.user.company_id, function(data) {
			if (typeof data == "object" && data.data.length > 0) {
				$.each(data.data, function(i, k) {
					t.frmUpdate.el.departmentId.append(new Option(k.name, k.id, false, false));
				});
				t.frmUpdate.el.departmentId.trigger("change");
			}
		});
	};

	t.reload_problem_category = function(updateChange) {
		t.frmUpdate.el.sub_category_id_cvr.hide();
		t.frmUpdate.el.parent_category_id.empty().append(new Option('', '', false, false));
		const department = t.frmUpdate.el.departmentId.val();
		let url = t.config.url.problem_categories;
		if (department != "" && department != null && department != "null") {
			url = t.config.url.problem_categories_by_dept + "/" + department;
			$.get(url,function(data) {
				if (typeof data == "object" && data.data.length > 0) {
					$.each(data.data, function(i, k) {
						t.frmUpdate.el.parent_category_id.append(new Option(k.category_name, k.id, false, false));
						if (typeof k.sub != "undefined" && Array.isArray(k.sub)) {
							t.problem_categories_data["sc" + k.id] = k.sub;
						}
					});
					if(updateChange && t.firstUpdateChange && t.isUpdate && t.frmUpdate.extraData && t.frmUpdate.extraData.parent_category_id){
						t.frmUpdate.el.parent_category_id.val(t.frmUpdate.extraData.parent_category_id).trigger("change");
						t.firstUpdateChange = false;
					}
					t.frmUpdate.el.parent_category_id.trigger("change");
				}
			});
		}
	};

	t.reload_sub_category = function(updateChange) {
		t.frmUpdate.el.sub_category_id.empty();
		const prblm = t.frmUpdate.el.parent_category_id.val();
		if (prblm != "" && prblm != null && prblm != "null") {
			try {
				const subCats = t.problem_categories_data["sc" + prblm];
				if(Array.isArray(subCats) && subCats.length > 0){
					t.frmUpdate.el.sub_category_id_cvr.show();
					t.frmUpdate.el.sub_category_id.append(new Option('', '', false, false));
					$.each(subCats, function(i, k) {
						t.frmUpdate.el.sub_category_id.append(new Option(k.category_name, k.id, false, false));
					});

					if(updateChange && t.isUpdate && t.frmUpdate.extraData && t.frmUpdate.extraData.sub_category_id){
						t.frmUpdate.el.sub_category_id.val(t.frmUpdate.extraData.sub_category_id).trigger("change");
					}
				}
			} catch (e) {
				console.log("err", e);
			}
		}
		t.frmUpdate.el.sub_category_id.trigger("change");
	};

    t.goBack = function(e) {
        e.preventDefault();
        if (t.config.url.back_to != "") {
            window.location = t.config.url.back_to;
        } else {
            window.history.back();
        }
    }

    t.deleteArticle = function (e) {
        e.preventDefault();
		t.httpPostPath = t.config.url.delete + "/" + config.id;
        sweetAlertConfirmation({
            message: config.translations.are_you_want_Delete,
            onConfirm: function() {
              var http = $.get(t.httpPostPath);
              http.done(function(data) {
                 if (typeof data == "object") {
                    if (data.status == "success") {
                        sweetAlert('center', 'success', data);
                        window.location = t.config.url.create_document;
                    } else {
                        sweetAlert('center', 'error', data);
                    }
                 }
              });
              http.fail(function() {
                var data = {
                    'msg': config.translations.something_went_wrong,
                };
                sweetAlert('center', 'error', data);
              });
              http.always(function() {
                 t.httpCall = true;
              });
           }
        });
    };

    t.editDocument = function(e) {
		e.preventDefault();
        var articleId = $(this).data("id") || config.id;
        if (!articleId) {
            sweetAlert('center', 'error', { msg: config.translations.something_went_wrong });
            return false;
        }
		t.isUpdate = true;
		t.httpPostPath = t.config.url.edit;
        if (t.resetUpdateForm() === false) {
            return false;
        }
		var http = $.get(t.httpPostPath + "/" + articleId);
		http.done(function(data) {
			if (typeof data == "object") {
				if (data.status == "success") {
					t.mdlUpdate.title.html(config.translations.Edit_Article);
					t.frmUpdate.el.btnUpdate.text("Update");
					t.frmUpdate.el.forAction.val("edit");
					t.frmCommentTokenize();
					t.loadForm(data.data);
					t.showModal(t.mdlUpdate);
				} else {
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
            var data = {
                'msg': config.translations.something_went_wrong,
            };
            sweetAlert('center', 'error', data);
		});
		http.always(function() {
			t.httpCall = true;
		});
	};

    t.updateDocument = function(e) {
		e.preventDefault();
        let s = t.frmUpdate.el.content.summernote('code');
        t.frmUpdate.el.content.val(s);

        var contentHolder = $("<div>").html(s);
        if ($.trim(contentHolder.text()) == '' && contentHolder.find('img, iframe, video').length === 0) {
            $('#knowledge_document_update_form').find('#shows_error').html('This field is required.');
            $('#knowledge_document_update_form').find('#shows_error').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
            return false;
        }
		if (t.frmupdValidator.form() == false) {
			return false;
		}
		t.httpPostPath = t.config.url.update;
		t.frmUpdate.el.btnUpdate.prop('disabled', true);
		var formData = new FormData($('#knowledge_document_update_form')[0]);
		var http = $.ajax({
			url: t.httpPostPath,
			type: "POST",
			processData: false,
			contentType: false,
			data: formData
		});
		http.done(function(data) {
			if (typeof data == "object") {
				if (data.status == "success") {
					sweetAlert('center', 'success', data);
					t.frmUpdate.el.btnUpdate.removeAttr('disabled', true);
                    t.hideModal(t.mdlUpdate);
					setTimeout(function() {
						window.location.reload();
					}, 800);
				} else {
					t.frmUpdate.el.btnUpdate.removeAttr('disabled', true);
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
			t.frmUpdate.el.btnUpdate.removeAttr('disabled', true);
			alert("Something went wrong. Please refresh page and try again");
		});
	};

	t.loadForm = function(data, forAction) {
		t.firstUpdateChange = true;
		t.frmUpdate.extraData = data;
		t.frmUpdate.el.title.val(data.title);
		t.frmUpdate.el.status.val(data.status).trigger("change");
		t.frmUpdate.el.id.val(data.id);
		t.frmUpdate.el.content.summernote('code', data.content);
		t.frmUpdate.el.id.val(data.id);
        if (data.company_id) {
            t.frmUpdate.el.company.empty().append(new Option(data.company_name, data.company_id, true, true)).trigger("change");
        }
		$.each( data.tags || [], function( key, value ) {
			if(value.tags != '') {
				t.frmUpdate.el.tags.select2('trigger', 'select', {
					data: {text: value.tags, id: value.id, selected: true}
				});
			}
		});
		if (data.attachment_data && data.attachment_data.length) {
            if (t.updateUploader) {
                t.updateUploader.clear();
                $.each(data.attachment_data, function(index, value) {
                    t.updateUploader.addExisting(value);
                });
            } else {
            $.each(data.attachment_data, function(index, value) {
                t.attachment_update.append('<div id="attachs' + index + '" class="attachs pad-top count_img"><div class="bord-btm clearfix">' + '<p class="name pull-left">' + value.original_file_name + '</p><span style="cursor:pointer" class="remove-attach pull-right"><i class="fa fa-remove"></i> Remove</span></div>');
                t.attachment_update.find("#attachs" + index + " .name").text(decodeURIComponent(value.original_file_name));
                t.attachment_update.find("#attachs" + index).attr("data-id", value.id);
            });
            }
        }

        if (data.department_id) {
           t.frmUpdate.el.departmentId.val(data.department_id).empty().append(new Option(data.dep_name, data.department_id, true, true)).trigger("change");
        }
        if (data.parent_category_id) {
            t.frmUpdate.el.parent_category_id.val(data.parent_category_id).empty().append(new Option(data.kcategory, data.parent_category_id, true, true)).trigger("change");
        }
        if (data.sub_category_id) {
            t.frmUpdate.el.sub_category_id_cvr.show();
            t.frmUpdate.el.sub_category_id.val(data.sub_category_id).empty().append(new Option(data.ksub_category, data.sub_category_id, true, true)).trigger("change");
		}

        if (data.card_img) {
            $(".imgviewcover").removeClass("hide");
            $("#imgview").attr("src", data.card_img);
        } else {
            $(".imgviewcover").addClass("hide");
            $("#imgview").attr("src", "");
        }
		t.data.id = data.id;
	};

    t.frmCommentTokenize = function() {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
		t.frmUpdate.el.tmp_id.val(v);
    };

	t.frmUpdate.el.tags.select2({
		width: "100%",
		placeholder: "Add Tags",
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
    if (window.AMGDragDrop) {
        window.AMGDragDrop.initAll(t.mdlUpdate[0]);
    }
    t.updateUploader = t.frmUpdate.attachment_dropper_cover.length ? t.frmUpdate.attachment_dropper_cover[0].AMGDragDropUploader : null;
    let totalFileSize = 0;
    let uploadedFiles = [];
    if (!t.updateUploader && $.isFunction(t.frmUpdate.attachment_dropper_cover.filedrop)) {
    t.frmUpdate.attachment_dropper_cover.filedrop({
        fallback_id: "update_attachments",
        fallback_dropzoneClick: true,
        url: t.config.url.kd_attachment_add,
        paramname: "update_attachments",
        data: {
            "_token": t.config.token,
            "tmp_id": function() { return t.frmUpdate.el.tmp_id.val() }
        },
        maxfiles: 5,
        maxfilesize: 10,
        error: function(err, file) {
            switch(err) {
                case 'TooManyFiles':
					var data = {
                        'msg': config.translations.upload_file,
                    };
                    sweetAlert('center', 'error', data);
                    break;
				case 'FileTooLarge':
					var data = {
						'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.',
					};
					sweetAlert('center', 'error', data);
					break;
                default:
                    break;
            }
        },
        uploadFinished: function(i, file, response, time) {
            if (response.status === "success") {
                t.attachment_update.find("#attachs" + i + " .name").text(decodeURIComponentSafe(unescape(response.data.original_file_name)));
                t.attachment_update.find("#attachs" + i).attr("data-id", response.data.id);
                t.attachment_update.find("#attachs" + i + " .progress").fadeOut("slow");
            } else {
                t.attachment_update.find("#attachs" + i + " .name").text(file.name + " upload failed");
                t.attachment_update.find("#attachs" + i + " .upload_length").addClass("progress-bar-danger");
            }
        },
        progressUpdated: function(i, file, progress) {
            t.attachment_update.find("#attachs" + i + " .upload_length").css("width", progress + "%");
        },
        beforeSend: function(file, i, done) {
            var matched = $('.count_img');

            if (uploadedFiles.includes(file.name)) {
                sweetAlert('center', 'error', { 'msg': 'This file has been already uploaded.' });
                return; 
            }
            uploadedFiles.push(file.name);

			const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.xls', '.xlsx', '.doc', '.docx', '.ppt', '.pdf', '.txt', '.msg', '.zip', '.psd', '.csv', '.eml'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            if ($.inArray(fileExtension, allowedExtensions) === -1) {
                sweetAlert('center', 'error', { 'msg': 'This file type is not allowed. Please upload a valid file.' });
                return; 
            }
            if (matched.length < 5) {
                if (file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return; 
                }
                if (totalFileSize + file.size > 10 * 1024 * 1024) {
                    var data = {
                        'msg': 'Total File size exceeds 10 MB(10240 KB). Please upload a smaller file.'
                    };
                    sweetAlert('center', 'error', data);
                    return; 
                }

                totalFileSize += file.size; 
                let fileSizeKB = Math.ceil(file.size / 1024);
                let fileNameWithSize = file.name + ' [' + fileSizeKB + ' KB]'; 
           
                if (t.attachment_update.find("#attachs" + i).length) {
                    t.attachment_update.find("#attachs" + i).attr("id", "attachs" + (Math.random().toString()).substring(2, 15));
                }
                t.attachment_update.append('<div id="attachs' + i + '" class="attachs pad-top count_img" data-size="' + file.size + '"><div class="bord-btm clearfix"><p class="pull-left">' + fileNameWithSize + '</p><span style="cursor:pointer" class="remove-attach pull-right" data-size="' + file.size + '"><i class="fa fa-remove"></i> Remove</span></div><div class="progress"><div style="width: 1%;" class="progress-bar upload_length"></div></div></div>');
                done();
            } else {
				var data = {
					'msg': config.translations.upload_file,
				};
                sweetAlert('center', 'error', data);
            }
        },
        dragOver: function() {
            t.frmUpdate.attachment_dropper.show();
        },
        drop: function() {
            t.frmUpdate.attachment_dropper.show();
            $(".note-editor.panel-default").removeClass('dragover');
        }
    });
    }

    t.attachment_update.removeAttach = function(e) {
        e.preventDefault();
        var p = $(this).closest(".attachs");
		let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalFileSize - fileSize >= 0) {
            totalFileSize -= fileSize; 
        }   
        let fileName = p.find('.bord-btm p').text().split(' [')[0]; 
        const index = uploadedFiles.indexOf(fileName);
        if (index > -1) {
            uploadedFiles.splice(index, 1); 
        }  
        $.post(t.config.url.kd_attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") }, function(d) {});
        p.fadeOut("slow").remove();
    };

    t.attachmentView = function(e) {
        e.preventDefault();
        var type = $(this).attr('data-view_mode');
        if (type == 1) {
            if (window.KDImagePreview && $.isFunction(window.KDImagePreview.show)) {
                window.KDImagePreview.show($(this).attr('data-view'), $(this).attr('data-name'));
            } else {
                window.open($(this).attr('data-view'), '_blank');
            }
        } else {
            window.open($(this).attr('data-view'), '_blank');
        }
    };

    t.attachmentDownload = function(e) {
        e.preventDefault();
        window.location = $(this).attr('data-url');
    }
    
    $(document).on("click", "#file_triggers", function (e) {
        e.preventDefault();
    });

    t.articleSearch = t.page.find(".search-container");
    t.articleSearch.input = t.articleSearch.find("#search-box");
    t.articleSearch.loader = t.articleSearch.find("#input-loader");
    t.articleSearch.suggestions = t.articleSearch.find("#suggestions");
    t.articleSearch.request = null;
    t.articleSearch.results = [];

    t.articleSearch.escape = function(value) {
        return $("<div/>").text(value == null ? "" : value).html();
    };

    t.articleSearch.goTo = function(id) {
        if (id) {
            window.location.href = t.config.url.view + "/" + id;
        }
    };
    t.articleSearch.render = function(items) {
        if (items.length > 0) {
            t.articleSearch.suggestions.html(items.map(function(item) {
                return '<div class="suggestion-item" data-id="' + item.id + '">' + t.articleSearch.escape(item.title) + '</div>';
            }).join("")).show();
            return;
        }
        t.articleSearch.suggestions.html('<div class="no-results">No results found</div>').show();
    };

    t.articleSearch.search = function(goToFirst) {
        var query = $.trim(t.articleSearch.input.val());

        if (query.length < 2) {
            t.articleSearch.results = [];
            t.articleSearch.suggestions.hide().empty();
            return;
        }

        if (t.articleSearch.request) {
            t.articleSearch.request.abort();
        }

        t.articleSearch.loader.css('display', 'block');
        t.articleSearch.request = $.ajax({
            url: t.config.url.searchurl,
            method: 'GET',
            data: { q: query },
            success: function(data) {
                t.articleSearch.results = Array.isArray(data) ? data : [];
                if (goToFirst === true && t.articleSearch.results.length > 0) {
                    t.articleSearch.goTo(t.articleSearch.results[0].id);
                    return;
                }
                t.articleSearch.render(t.articleSearch.results);
            },
            complete: function() {
                t.articleSearch.loader.css('display', 'none');
                t.articleSearch.request = null;
            }
        });
    };

    t.articleSearch.input.on('input', function() {
        t.articleSearch.search(false);
    });

    t.articleSearch.input.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            t.articleSearch.search(true);
        }
    });

    t.articleSearch.on("click", ".btn-searchbox", function(e) {
        e.preventDefault();
        t.articleSearch.search(true);
    });

    t.articleSearch.on('click', '.suggestion-item', function () {
        t.articleSearch.input.val($(this).text());
        t.articleSearch.suggestions.hide();
        t.articleSearch.goTo($(this).data('id'));
    });

    $(document).click(function (e) {
        if (!$(e.target).closest('.search-container').length) {
            t.articleSearch.suggestions.hide();
        }
    });

    t.frmUpdate.on("click", ".remove-attach", $.proxy(t.attachment_update.removeAttach));
    t.mdlManageTags.getArticleTags.on('click',t.getArticleTagDetails);
    t.mdlManageTags.saveTags.on('click',t.saveArticleDetailTags);
    t.staringUi();
	t.refreshTimeLine();
    t.page.on("click", ".js-act-staring", $.proxy(t.staring));
	t.page.on("click", ".single-tiny-viewer", $.proxy(t.toggleSingleTinyViewer));
    t.timeline.on("click", ".tri-view", $.proxy(t.attachmentView));
    t.timeline.on("click", ".tri-download", $.proxy(t.attachmentDownload));
    // t.reload_department();
	var select2Opts = { width: "100%" };
    if (t.frmUpdate.el.company.length && t.config.url.get_company_by_user_access) {
        t.frmUpdate.el.company.select2($.extend({}, select2Opts, {
            dropdownParent: t.frmUpdate.el.company.parent(),
            ajax: {
                url: t.config.url.get_company_by_user_access,
                dataType: "json",
                method: "get",
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                }
            },
            allowClear: true,
            placeholder: t.config.translations.select_company || "Select Company"
        })).on("select2:select select2:clear", function() {
            t.frmUpdate.el.departmentId.val("").trigger("change");
            t.frmUpdate.el.parent_category_id.val("").trigger("change");
            t.frmUpdate.el.sub_category_id.val("").trigger("change");
        });
    }
    t.frmUpdate.el.departmentId.select2($.extend({}, select2Opts, {
        dropdownParent: t.frmUpdate.el.departmentId.parent(),
        ajax: {
            url: t.config.url.departments_with_company,
            dataType: "json",
            method: 'get',
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page || 1,
                    company_id: t.frmUpdate.el.company.length ? t.frmUpdate.el.company.val() : "",
                }
                return query;
            }
        },
        allowClear: true,
        placeholder: t.config.translations.select_department,
    })).on('select2:select', function (e) {
		t.frmUpdate.el.parent_category_id.val('').trigger('change');
        t.frmUpdate.el.sub_category_id.val('').trigger('change');
    });

	t.frmUpdate.el.parent_category_id.select2($.extend({}, select2Opts, {
		dropdownParent: t.frmUpdate.el.parent_category_id.parent(),
		ajax: {
            url: function() { return  t.config.url.fetch_category_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    department: t.frmUpdate.el.departmentId.val(),
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
							sub_cat: item.sub_category || [],
                        };
                    }),
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: t.config.translations.Parent_category,
        tags: true,
        createTag: function (params) {
            let term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newOption: true
            };
        },
        templateResult: function (data) {
            if (data.newOption) {
                return $('<span>' + data.text + ' (New)</span>');
            }
            return data.text;
        }
    })).on('select2:select', function (e) {
		var selectedData = e.params.data;
        if (!selectedData.newOption) {
            var subCatCount = selectedData.sub_cat.length;
            if (subCatCount > 0) {
                t.frmUpdate.el.sub_category_id_cvr.show();
            } else {
                t.frmUpdate.el.sub_category_id_cvr.hide();
            }
            t.frmUpdate.el.sub_category_id.val('').trigger('change');
        } else {
            t.frmUpdate.el.sub_category_id_cvr.hide();
            t.frmUpdate.el.sub_category_id.val('').trigger('change');
        }
    });

	t.frmUpdate.el.sub_category_id.select2($.extend({}, select2Opts, {
		dropdownParent: t.frmUpdate.el.sub_category_id.parent(),
        ajax: {
            url: function () { return t.config.url.fetch_subcategory_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    sub_category: t.frmUpdate.el.parent_category_id.val(),
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                if (data.results.length === 0) {
                    t.frmUpdate.el.sub_category_id_cvr.hide();
                } else {
                    t.frmUpdate.el.sub_category_id_cvr.show();
                }
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: (params.page * 10) < data.total_count
                    }
                };
            },
            delay: 300
        },
        allowClear: true,
        placeholder: t.config.translations.select_sub_catgeory,
    }));

    t.frmUpdate.on("click", "#manual_file_trigger", function(e) {
        e.preventDefault();
        if (!t.updateUploader) {
            t.frmUpdate.find("#update_attachments").trigger("click");
        }
    });
	// t.frmUpdate.el.parent_category_id.select2($.extend({}, select2Opts, { placeholder: config.translations.Parent_category,dropdownParent:t.mdlUpdate }));
	// t.frmUpdate.el.parent_category_id.on("change", $.proxy(t.reload_sub_category));
	// t.frmUpdate.el.sub_category_id.select2($.extend({}, select2Opts, { placeholder: config.translations.Sub_Category,dropdownParent:t.mdlUpdate }));
    t.frmUpdate.el.status.select2($.extend({},select2Opts,{ placeholder: config.translations.select_status,allowClear: true,dropdownParent:t.mdlUpdate}));

    t.page.on("click", ".js-act-go-back", $.proxy(t.goBack));
    t.page.on("click", ".js-act-delete", $.proxy(t.deleteArticle));
    t.page.on("click", ".js-act-edit-ticket", $.proxy(t.editDocument));
	t.frmUpdate.on("submit", $.proxy(t.updateDocument));
	t.frmUpdate.on("click", ".js-act-update", $.proxy(t.updateDocument));
    t.frmUpdate.el.sub_category_id_cvr.hide();

    t.initTooltips = function () {
		$('[data-bs-toggle="tooltip"]').tooltip();
	}
    t.initTooltips();
};
