var Document = function (config) {
	$(document).ready(function() {
		$('.summernote').summernote({
			placeholder: config.translations.Enter_content,
		});
		$(".form-control").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".form-check").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});

		$('.btn').click(function(){
			$('.btn').removeClass('active').addClass('inactive');
			$(this).removeClass('inactive').addClass('active');
		});
	});

	$(".status").select2({
		placeholder: "Select Article Status"
	});

	$('.Show').click(function() {
		$(this).parent().find('.target').show(200);
		$(this).parent().find('.Show').hide(0);
		$(this).parent().find('.Hide').show(0);
	});
	$('.Hide').click(function() {
		$(this).parent().find('.target').hide(500);
		$(this).parent().find('.Show').show(0);
		$(this).parent().find('.Hide').hide(0);
	});

	$("a.tag-links").click(function(){
		$("a.tag-links").css("background-color", "");
		$(this).css("background-color", "#313131");
	});
};

var KnowledgeDocument = function (config) {
	var t = this;
	t.config = config;
	t.page = $("#page_boxed");
	t.article = t.page.find("#article");
	t.pagebtns = t.page.find("#pagebtns");
	t.pageBtmSummary = t.page.find("#page-btm-summary");
	t.home = t.page.find("#home");
	t.pagebtns1 = t.page.find("#pagebtns-homes");
	t.pageBtmSummary1 = t.page.find("#page-btm-summary-homes");
	t.loader = t.page.find('#loader_img');
	t.api_loader = t.page.find("#api_loader");
	t.total = 0;
	t.perPage = 10;
	t.httpCall = true;
	t.httpPostPath = "";
	t.data = {};
	t.reset = function() {
		t.total = 0;
		t.perPage = 10;
	};

	t.offListen = false;

	t.btn = {};
	t.status = t.page.find(".btn-group");
	t.category = t.page.find(".form-check");
	t.searchbox = t.page.find(".searchbox_cover");
	t.tag = t.page.find(".tags-nav");

	t.mdl = t.page.find("#articleModal");
	t.mdl.title = t.mdl.find(".modal-title");
	t.mdl.frm = t.mdl.find("#knowledge_document_form");
	t.mdl.frmEl = {};
	t.mdl.frmEl.id = t.mdl.frm.find("#id");
	t.mdl.frmEl.title = t.mdl.frm.find("#title");
	t.mdl.frmEl.company = t.mdl.frm.find("#company");
	t.mdl.frmEl.status= t.mdl.frm.find("#status");
	t.mdl.frmEl.departmentId = t.mdl.frm.find("#department_id");
	t.mdl.frmEl.parent_category_id = t.mdl.frm.find("#parent_category_id");
	t.mdl.frmEl.sub_category_container = t.mdl.frm.find("#sub_category_id_cvr");
	t.mdl.frmEl.sub_category_id = t.mdl.frm.find("#sub_category_id");
	t.mdl.frmEl.content = t.mdl.frm.find("#content");
	t.mdl.frmEl.tags= t.mdl.frm.find("#tags");
	t.mdl.frmEl.tmp_id = t.mdl.frm.find("#tmp_id");
	t.attachment = t.mdl.frm.find("#attachments");

	t.mdlUpdate = t.page.find("#articleupdateModal");
	t.frmUpdate = t.mdlUpdate.find("#knowledge_document_update_form");
	t.frmUpdate.extraData = {};
	t.frmUpdate.el = {};
	t.mdlUpdate.title = t.frmUpdate.find('.modal-title');
	t.frmUpdate.el.id = t.frmUpdate.find("#id");
	t.frmUpdate.el.forAction = t.frmUpdate.find("#forAction");
	t.frmUpdate.el.token = t.frmUpdate.find("#token");
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

	t.mdl.btn = {};
	t.mdl.btnSubmit = t.mdl.find("#btnSubmit");

	t.isUpdate = false;
	t.firstUpdateChange = true;

	/* attachment */
	t.attachment = t.mdl.find("#attachments");
	t.attachment_dropper_cover = t.mdl.find("#attachment-dropper-cover");
	t.attachment_dropper = t.mdl.find("#attachment-dropper");

	t.mdl.modal({
		backdrop: 'static',
		keyboard: false,
		show: false
	});
	t.mdl.on('hidden.bs.modal', function () {
		t.resetFrm();
		t.resetValidation();
	});

	t.mdlUpdate.on('hidden.bs.modal', function () {
		t.resetFrm();
		t.resetValidation();
	});

	t.mdl.frmEl.content.summernote({
        inheritPlaceholder: true,
        placeholder: config.translations.comment_summer,
        toolbar: [
            ['color', ['color']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']],
			['fontsize', ['fontsize']],
			['insert', ['link']],
        ],
		fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36', '48', '64', '82', '150'],
        minHeight: 200,
        focus: true,
		callbacks: {
			onChange: function(contents) {
				if ($(contents).text().trim().length > 0) {
					$("#content").valid();
				}
			}
		}
    });

	t.mdl.frmEl.content.on('summernote.change', function() {
		var $el = $(this);
		if ($el.hasClass('error') || $el.closest('.input-group').hasClass('amg-form-invalid') ||
			$el.next('.note-editor').hasClass('amg-form-select-error')) {
			$el.valid();
		}
	});

	t.mdl.frmEl.sub_category_container.hide();
	t.frmUpdate.el.sub_category_id_cvr.hide();

	t.frmCommentTokenize = function() {
        var v = Math.random().toString(36).substring(2, 6) + Math.random().toString(36).substring(2, 6) + Date.now();
        t.mdl.frmEl.tmp_id.val(v);
		t.frmUpdate.el.tmp_id.val(v);
    };
	//attachment
	let totalFileSize = 0;
	let uploadedFiles = [];

	t.attachment = t.mdl.find("#attachments");
	t.attachment_dropper_cover = t.mdl.find("#attachment-dropper-cover");
	t.attachment_dropper = t.mdl.find("#attachment-dropper");
	if (window.AMGDragDrop) {
		window.AMGDragDrop.initAll(t.mdl[0]);
	}
	t.uploader = t.attachment_dropper_cover.length ? t.attachment_dropper_cover[0].AMGDragDropUploader : null;
	t.attachment.removeAttach = function(e) {
		e.preventDefault();
		var attachId = $(this).closest(".attach");
		var p = $(this).siblings("p");
		$.post(t.config.url.schedular_attachment_remove, { "_token": t.config.token, "id": attachId.attr("data-id"), "name": p.text() }, function(d) {});
		attachId.fadeOut("slow").remove();
	};

	t.setMainAttachments = function() {
        var at = [];
        $.each(t.config.main_attachments, function(i, v) {
            var sext = v.ext.toLowerCase();
            var eye_link = "";
            if (sext == "png" || sext == "jpeg" || sext == "jpg") {
                eye_link = '<span class="tri-view" data-view_mode="1" data-view="' + t.config.url.attachment_view + '/' + v.id + '" data-name="' + decodeURIComponent(v.name) + '"><i class="fa fa-eye"></i></span>';
            }

            var ai_bg = v.thumb == 1 ? "ai-bg" : "";
            var bg = v.thumb == 1 ? "background-image: url(" + t.config.url.attachment_view + "/" + v.id + "/1" : "";

            at.push('<div class="attach-item ' + ai_bg + '" style="' + bg + '" ><div class="attach-item-cntnt"><span class="attach-name">' + decodeURIComponentSafe(unescape(v.name)) + '</span><div class="icons">' + eye_link + '<span class="tri-download" data-url="' + t.config.url.attachment_download + "/" + v.id + '"><i class="fa fa-download"></i></span></div></div></div>');
        });

        if( at.length > 0 ) {
            t.main_attachments.html('<div><div class="text-bold mar-rgt">Attachments:</div>' + at.join("") + '</div>');
        }
    }

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

    t.attachment.removeAttach = function(e) {
        e.preventDefault();
        var p = $(this).closest(".attach");
		let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalFileSize - fileSize >= 0) {
            totalFileSize -= fileSize; 
        }   
		let fileName = p.find('.bord-btm p').text().split(' [')[0]; 
        const index = uploadedFiles.indexOf(fileName);
        if (index > -1) {
            uploadedFiles.splice(index, 1); 
        }   
        $.post(t.config.url.kd_attachment_remove, {"_token": t.config.token, "id": p.attr("data-id"), "name": p.attr("name")}, function(d) {});
        p.fadeOut("slow").remove();
    };
    t.attachment_update.removeAttach = function(e) {
        e.preventDefault();
        var p = $(this).closest(".attachs");
		let fileSize = parseFloat(p.attr('data-size'));
        if (fileSize && totalUpdateFileSize - fileSize >= 0) {
            totalUpdateFileSize -= fileSize; 
        }   
		let fileName = p.find('.bord-btm p').text().split(' [')[0]; 
        const index = uploadedUpdateFiles.indexOf(fileName);
        if (index > -1) {
            uploadedUpdateFiles.splice(index, 1); 
        }   
        $.post(t.config.url.kd_attachment_remove, { "_token": t.config.token, "id": p.attr("data-id") }, function(d) {});
        p.fadeOut("slow").remove();
    };
	t.mdl.on("click", "#manual_file_trigger", function(e) {
        e.preventDefault();
    });
	t.frmUpdate.on("click", "#file_triggers", function(e) {
        e.preventDefault();
    });
    t.mdl.on("click", ".remove-attach", $.proxy(t.attachment.removeAttach));
	t.frmUpdate.on("click", ".remove-attach", $.proxy(t.attachment_update.removeAttach));

	t.addDocument = function(e) {
		t.resetFrm();
		if (e && typeof e.preventDefault === "function") {
			e.preventDefault();
		}
		t.mdl.frm.find('.error').text('');
		t.mdl.frm.find('#shows_error').html('');
		t.frmCommentTokenize();
		if (t.mdl.length && window.bootstrap && bootstrap.Modal) {
			bootstrap.Modal.getOrCreateInstance(t.mdl[0]).show();
		} else {
			t.mdl.modal('show');
		}
	};
	t.createDocument = function(e) {
		e.preventDefault();
		if (t.uploader && t.uploader.files.length > 10) {
			sweetAlert('center', 'error', {
				msg: 'Maximum 10 files allowed.'
			});
			return false;
		}
		if (!t.frmValidator.form()) {
			return false;
		}
		t.loader.show();
		t.mdl.btnSubmit.prop('disabled', true);
		t.httpPostPath = t.config.url.document;
		var formData = new FormData(t.mdl.frm[0]);
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
					t.mdl.btnSubmit.removeAttr('disabled', true);
					setTimeout(function() {
						window.location = t.config.url.create_document;
						t.resetFrm();
					}, 800);
				} else {
					t.mdl.btnSubmit.removeAttr('disabled', true);
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
			t.mdl.btnSubmit.removeAttr('disabled', true);
			var data = {
				'msg': t.config.translations.something_went_wrong,
			}
			sweetAlert('center', 'error', data);
		});
		http.always(function () {
			t.loader.hide();
		});
	};

	t.deleteDocument = function(e) {
		e.preventDefault();
		var Id = $(this).attr("data-href");
		t.httpPostPath = t.config.url.delete + "/" + Id;
		sweetAlertConfirmation({
			message: config.translations.are_you_want_Delete,
			onConfirm: function() {
				var http = $.get(t.httpPostPath);
				http.done(function(data) {
					if (typeof data == "object") {
						if (data.status == "success") {
							sweetAlert('center', 'success', data);
							setTimeout(function() {
								window.location = t.config.url.create_document;
							}, 800);
						} else {
							sweetAlert('center', 'error', data);
						}
					}
				});
				http.fail(function() {
				var data = {
					'msg': t.config.translations.something_went_wrong,
				}
				sweetAlert('center', 'error', data);
				});
				http.always(function() {
					t.httpCall = true;
				});
			}
		});
	};

	t.loadForm = function(data, forAction) {
		t.resetFrm();
		t.firstUpdateChange = true;
		t.frmUpdate.extraData = data;
		t.frmUpdate.el.title.val(data.title);
		t.frmUpdate.el.status.val(data.status);
		// t.frmUpdate.el.parent_category_id.val(data.parent_category_id);
		t.frmUpdate.el.id.val(data.id);
		t.frmUpdate.el.content.summernote('code', data.content);
		t.frmUpdate.el.id.val(data.id);
		$.each( data.tags, function( key, value ) {
			if(value.tags != '') {
				t.frmUpdate.el.tags.select2('trigger', 'select', {
					data: {text: value.tags, id: value.id, selected: true}
				});
			}
		});
		if (data.attachment_data != "" || data.attachment_data != null) {
            $.each(data.attachment_data, function(index, value) {
                t.attachment_update.append('<div id="attachs' + index + '" class="attachs pad-top count_img"><div class="bord-btm clearfix">' + '<p class="name pull-left">' + value.original_file_name + '</p><span style="cursor:pointer" class="remove-attach pull-right"><i class="fa fa-remove"></i> Remove</span></div>');
                t.attachment_update.find("#attachs" + index + " .name").text(decodeURIComponent(value.original_file_name));
                t.attachment_update.find("#attachs" + index).attr("data-id", value.id);
            });
        }

		if(data.department_id) {
			t.frmUpdate.el.departmentId.empty().append(new Option(data.dep_name, data.department_id, true, true)).trigger("change");
		}
		if(data.parent_category_id) {
			t.frmUpdate.el.parent_category_id.empty().append(new Option(data.kcategory, data.parent_category_id, true, true)).trigger("change");
		}
		if(data.sub_category_id) {
			t.frmUpdate.el.sub_category_id_cvr.show();
			t.frmUpdate.el.sub_category_id.empty().append(new Option(data.ksub_category, data.sub_category_id, true, true)).trigger("change");
		}
		t.data.id = data.id;
	};

	t.resetFrm = function() {
   		t.mdl.frm.trigger("reset");
		t.mdl.frmEl.id.val("");
		t.mdl.frmEl.title.val("").attr('disabled', false);
		t.mdl.frmEl.company.val("").trigger("change").attr('disabled', false);
		t.frmUpdate.el.tags.val("").trigger("change").attr('disabled', false);
		t.mdl.frmEl.tags.val("").trigger("change").attr('disabled', false);
		t.mdl.frmEl.status.val("").trigger("change").attr('disabled', false);
		t.mdl.frmEl.parent_category_id.val("").attr('disabled', false);
		t.mdl.frmEl.content.val('').summernote('code', '');
		t.mdl.find('#shows_error').html('');
		t.frmUpdate.el.departmentId.val("").trigger("change").attr('disabled', false);
		t.mdl.frmEl.departmentId.val("").trigger("change").attr('disabled',false);
		t.frmUpdate.el.sub_category_id_cvr.hide();
		t.attachment.empty();
		t.attachment_update.empty();
	};

	t.resetValidation = function () {
		if (t.frmValidator) {
			t.frmValidator.resetForm();
		}
		if (t.frmupdValidator) {
			t.frmupdValidator.resetForm();
		}
		t.mdl.find(".amg-form-invalid").removeClass("amg-form-invalid");
		t.mdl.find(".amg-form-icon-error").removeClass("amg-form-icon-error");
		t.mdl.find(".amg-form-select-error").removeClass("amg-form-select-error");
		t.mdlUpdate.find(".amg-form-invalid").removeClass("amg-form-invalid");
		t.mdlUpdate.find(".amg-form-icon-error").removeClass("amg-form-icon-error");
		t.mdlUpdate.find(".amg-form-select-error").removeClass("amg-form-select-error");
		t.mdl.find("label.error").remove();
		t.mdlUpdate.find("label.error").remove();
		t.mdl.find(".amg-form-error-wrap").html('');
		t.mdlUpdate.find(".amg-form-error-wrap").html('');
	};

	t.editDocument = function(e) {
		e.preventDefault();
		t.isUpdate = true;
		var shedId = $(this).attr("data-href");
		t.httpPostPath = t.config.url.edit;
		var http = $.get(t.httpPostPath + "/" + shedId);
		http.done(function(data) {
			if (typeof data == "object") {
				if (data.status == "success") {
					t.mdlUpdate.title.html(config.translations.Edit_Article);
					t.frmUpdate.el.btnUpdate.text("Update");
					t.frmUpdate.el.forAction.val("edit");
					t.frmCommentTokenize();
					t.mdlUpdate.modal('show');
					t.loadForm(data.data);
				} else {
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
			var data = {
				'msg': t.config.translations.something_went_wrong,
			}
			sweetAlert('center', 'error', data);
		});
		http.always(function() {
			t.httpCall = true;
		});
	};

	t.updateDocument = function(e) {
		e.preventDefault();
		let s = t.frmUpdate.el.content.val();
        if (s == '') {
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
					setTimeout(function() {
						window.location = t.config.url.create_document;
					}, 800);
				} else {
					t.frmUpdate.el.btnUpdate.removeAttr('disabled', true);
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
			t.frmUpdate.el.btnUpdate.removeAttr('disabled', true);
			var data = {
				'msg': t.config.translations.something_went_wrong,
			}
			sweetAlert('center', 'error', data);
		});
	};

	t.renderList = function(i, d) {
		var html = '';
		html += '<article class="post tag" id="posts_tag">' +
		'<button class="Show"><i class="fa fa-plus" aria-hidden="true"></i></button>' +
		'<button class="Hide"><i class="fa fa-minus" aria-hidden="true"></i></button>' +
		'<h4 class="title">'+d.title+'</h4>' +
		'<div class="target">' +
		'<p>'+d.content+'</p>' +
		'<div class="buttons_group">';
		if(jQuery.inArray("KnowledgeDocumentArticleView", t.config.permissions) !== -1) {
			html +='<a href="article/view/'+d.id+'" target="_blank" class="dtActView" data-id='+d.id+'> <i class="fa fa-eye" style="font-size:18px"></i></a>';
		}
		if(jQuery.inArray("KnowledgeDocumentArticleEdit", t.config.permissions) !== -1) {
			html +='<button type="button" class="btn btn-round dtActEdit" data-id='+d.id+'> <i class="fa fa-edit" style="font-size:18px"></i></button>';
		}
		if(jQuery.inArray("KnowledgeDocumentArticleDelete", t.config.permissions) !== -1) {
			html +='<button type="button" class="btn btn-round dtActDel" data-toggle="modal" data-target="" data-id='+d.id+'><i class="fa fa-trash" aria-hidden="true"></i></button>';
		}
		html +='</div>' +
		'</div>' +
		'</article>';

		t.article.append(html);

		$('.Show').click(function() {
			$(this).parent().find('.target').show(200);
			$(this).parent().find('.Show').hide(0);
			$(this).parent().find('.Hide').show(0);
		});

		$('.Hide').click(function() {
			$(this).parent().find('.target').hide(500);
			$(this).parent().find('.Show').show(0);
			$(this).parent().find('.Hide').hide(0);
		});
	};

	t.load = function(e) {
		t.api_loader.addClass('active');
		var category = [];
		t.category.find('.form-check-input:checked').each(function(i, element) {
			if($(element).is(":checked")) {
				category.push($(element).attr("data-id"));
			}
		});
		var tag = [];
		if($(this).hasClass('js-act-tags')){
			tag.push($(this).attr("data-id"));
		}

		var theName = $(".search-input").val()
		t.httpPostPath = t.config.url.document_list;
		var http = $.ajax({
			url: t.httpPostPath,
			type: "POST",
			data: {
				search: theName,
				page: t.pagebtns.pagination("getCurrentPage"),
				size: t.perPage,
				status: t.status.find('.js-actions.active').data('id'),
				category: category,
				tag: tag,
			}
		});
		http.done(function(data) {
			if (typeof data == "object" && typeof data.total != "undefined") {
				t.data = data;
				t.pagebtns.pagination("updateItems", data.filtered);
				t.pagebtns.pagination("drawPage", data.page);
				t.article.empty();
				if (data.filtered != data.total) {
					t.pageBtmSummary.html(config.translations.Available + data.filtered + " records (filtered from " + data.total + " total records)");
				} else {
					t.pageBtmSummary.html(config.translations.Available_Records + data.total);
				}
				if (data.filtered < 1) {
					t.article.html('<div class="no-record-found">'+config.translations.No_records_Found+'</div>');
					return;
				}
				$.each(data.data, t.renderList);

			}
		}).fail(function() {
			t.api_loader.removeClass('active');
			alert("Something went wrong. Please check given details are correct");
		}).always(function() {
			t.api_loader.removeClass('active');
		});
	};

	t.pageBtnClicked = function(n, e) {
		e.preventDefault();
		t.load();
	};

	t.pagebtns.pagination({
		itemsOnPage: t.perPage,
		onPageClick: t.pageBtnClicked
	});

	let tagSearchInput = null;
	t.mdl.frmEl.tags.select2({
		width: "100%",
		placeholder: config.translations.tag,
		dropdownParent: t.mdl.frm,
		tags: true,
		maximumSelectionLength: 10,
		tokenSeparators: [','],

		createTag: function (params) {
			let term = $.trim(params.term);

			if (!term) {
				return null;
			}

			if (term.length > 40) {
				toastr.error('Tag cannot be more than 40 characters.');

				if (tagSearchInput) {
					tagSearchInput.val('');
				}

				return null;
			}

			return {
				id: term,
				text: term,
				newTag: true
			};
		},

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
		},
		templateSelection: function(data, container) {
			$(container).attr('title', data.text);
			return data.text.length > 40 ? data.text.substring(0, 40) + '...' : data.text;
		}
	});

	t.mdl.frmEl.tags.on('select2:open', function () {
		tagSearchInput = $('.select2-search__field');
	});

	t.frmUpdate.el.tags.select2({
		width: "100%",
		placeholder: config.translations.tag,
		tags: true,
		maximumSelectionLength: 10,
		tokenSeparators: [','],

		createTag: function (params) {
			let term = $.trim(params.term);

			if (!term) {
				return null;
			}

			if (term.length > 40) {
				toastr.error('Tag cannot be more than 40 characters.');

				if (tagSearchInput) {
					tagSearchInput.val('');
				}

				return null;
			}

			return {
				id: term,
				text: term,
				newTag: true
			};
		},

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
		},

		templateSelection: function(data, container) {
			$(container).attr('title', data.text);

			return data.text.length > 40
				? data.text.substring(0, 40) + '...'
				: data.text;
		}
	});

	t.frmUpdate.el.tags.on('select2:open', function () {
		tagSearchInput = $('.select2-search__field');
	});

	var select2Opts = { width: "100%" };

	t.mdl.frmEl.company.select2($.extend({}, select2Opts, {
		dropdownParent: t.mdl.frmEl.company.parent(),
		ajax: {
			url: t.config.url.get_company_by_user_access,
			dataType: "json",
			method: "get",
			data: function (params) {
				return {
					search: params.term,
					page: params.page || 1
				};
			},
			delay: 300
		},
		allowClear: true,
		placeholder: t.config.translations.select_company || "Select Company"
	})).on("select2:select select2:clear", function () {
		t.mdl.frmEl.departmentId.val("").trigger("change");
		t.mdl.frmEl.parent_category_id.val("").trigger("change");
		t.mdl.frmEl.sub_category_id.val("").trigger("change");
	});

	t.updateValidationState = function (element, hasError) {

		var group = element.closest(".input-group");
		var isSelect2 = element.hasClass("select2-hidden-accessible");
		var isSummernote = element.hasClass("summernote");

		if (group.length) {
			group.toggleClass("amg-form-invalid", !!hasError);
			group.find(".input-group-text").toggleClass("amg-form-icon-error", !!hasError);
		}

		if (isSelect2) {
			var select2Container = element.next(".select2-container");
			select2Container.find(".select2-selection").toggleClass("amg-form-select-error", !!hasError);
			select2Container.closest(".input-group").find(".input-group-text").toggleClass("amg-form-icon-error", !!hasError);
		}
		 if (isSummernote) {
			var noteEditor = element.next('.note-editor');
			if (hasError) {
				noteEditor.addClass('amg-form-select-error');
			} else {
				noteEditor.removeClass('amg-form-select-error');
			}
			element.closest(".input-group").find(".input-group-text").toggleClass("amg-form-icon-error", !!hasError);
		}
	};
	t.getModalErrorWrap = function (element) {
        var row = element.closest(".amg-form-field-row");
        var wrap;

        if (!row.length) {
            return $();
        }

        wrap = row.children(".amg-form-error-wrap");
        if (!wrap.length) {
            wrap = $('<div class="amg-form-error-wrap"></div>');
            row.append(wrap);
        }

        return wrap;
    };

	$.validator.addMethod("summernote_content", function(value, element) {
		var content = $(element).summernote('code');
		if (!content) return false;
		var textContent = content.replace(/<[^>]*>/g, '').trim();
		return textContent !== '';
	}, "This field is required.");

	t.frmValidator = t.mdl.frm.validate({
		ignore: [],
		onsubmit: false,
			rules: {
				title: {
					required: true,
					clean_text_only: true,
				},
				company:{
					required:true,
				},
				department_id: {
					required: true,
				},
				parent_category_id: {
					required: true,
					clean_text_only: true,
				},
				"tags[]":{
					clean_text_only: true,
				},
				content:{
					required: true,
					summernote_content: true
				},
				status:{
					required: true,
				}
			},
			errorPlacement: function(error, element) {
				if (element.attr("id") === "content") {
					element.closest(".amg-form-field-row").find(".amg-form-error-wrap").html(error);
					t.updateValidationState(element, true);
					return;
				}
				var errorWrap = t.getModalErrorWrap(element);
				if (errorWrap.length) {
					error.appendTo(errorWrap);
				} else if (element.closest(".input-group").length) {
					error.insertAfter(element.closest(".input-group"));
				} else {
					error.insertAfter(element);
				}
				t.updateValidationState(element, true);
			},
			highlight: function (element) {
				t.updateValidationState($(element), true);
			},
			unhighlight: function (element) {
				t.updateValidationState($(element), false);
			}
	});


	t.mdl.frmEl.departmentId.select2($.extend({}, select2Opts, {
			dropdownParent: t.mdl.frmEl.departmentId.parent(),
            ajax: {
                url: t.config.url.departments_with_company,
                dataType: "json",
                method: 'get',
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
						company_id: t.mdl.frmEl.company.val(),
                    }
                    return query;
                }
            },
            allowClear: true,
			placeholder: t.config.translations.select_department,
        })
    ).on('select2:select', function (e) {
		t.mdl.frmEl.parent_category_id.val('').trigger('change');
        t.mdl.frmEl.sub_category_id.val('').trigger('change');
    });

	t.mdl.frmEl.parent_category_id.select2($.extend({}, select2Opts, {
		dropdownParent: t.mdl.frmEl.parent_category_id.parent(),
		ajax: {
			url: function() { return t.config.url.fetch_category_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    department: t.mdl.frmEl.departmentId.val(),
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
			if (/[<>]/.test(term)) {
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
				t.mdl.frmEl.sub_category_container.show();
			} else {
				t.mdl.frmEl.sub_category_container.hide();
			}
			t.mdl.frmEl.sub_category_id.val('').trigger('change');
		} else {
			t.mdl.frmEl.sub_category_container.hide();
			t.mdl.frmEl.sub_category_id.val('').trigger('change');
			console.log("New category entered:", selectedData.text);
		}
    });

	t.mdl.frmEl.sub_category_id.select2($.extend({}, select2Opts, {
		dropdownParent: t.mdl.frmEl.sub_category_id.parent(),
        ajax: {
            url: function () { return t.config.url.fetch_subcategory_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    sub_category: t.mdl.frmEl.parent_category_id.val(),
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
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

	t.frmUpdate.el.departmentId.select2($.extend({}, select2Opts, {
		ajax: {
			url: t.config.url.departments_with_company,
			dataType: "json",
			method: 'get',
			data: function (params) {
				var query = {
					search: params.term,
					page: params.page || 1,
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
	})).on('select2:select', function (e) {
		var selectedData = e.params.data;
		var subCatCount = selectedData.sub_cat.length;
		if (subCatCount > 0) {
			t.frmUpdate.el.sub_category_id_cvr.show();
		} else {
			t.frmUpdate.el.sub_category_id_cvr.hide();
		}
		t.frmUpdate.el.sub_category_id.val('').trigger('change');
	});

	t.frmUpdate.el.sub_category_id.select2($.extend({}, select2Opts, {
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

	if (t.article.length) {
		t.load();
		// t.reload_department();
		t.status.on("click", ".js-actions", t.load);
		t.category.on("click", "#defaultCheck1", t.load);
		t.tag.on("click", ".js-act-tags", t.load);
		t.searchbox.on("click", ".btn-searchbox", t.load);
		t.searchbox.find(".searchbox").on("input", t.load);
	}

	// on change if error message present then remove message.
	t.mdl.frm.find("select[name='company'],select[name='department_id'],select[name='parent_category_id'],select[name='status'],select[name='tags[]']").on("change", function () {
        var element = $(this);
        element.closest(".amg-form-field-row").find(".amg-form-error-wrap").empty();
		if ($(this).val() !== '' && $(this).val() !== null) {
            element.valid();
        }
    });

	var select2Opts = { width: "100%" };
	t.mdl.frmEl.status.select2($.extend({}, select2Opts,{ placeholder: config.translations.select_status,allowClear: true,dropdownParent:t.mdl.frmEl.status.parent()}));
	t.frmUpdate.el.status.select2($.extend({}, select2Opts,{ placeholder: config.translations.select_status,allowClear: true,dropdownParent:t.frmUpdate.el.status.parent()}));
};

var PublicArticle = function(config) {
	var t = this;
	t.config = config;
	t.config.other_filters = t.config.other_filters || {};
	t.page = $("#page_boxed");
	t.articlePage = t.page.find("#kd-article-page");
	t.department = t.page.find(".department");
	t.searchbox = t.page.find(".search-cover-box");
	t.loader = t.page.find("#api_loader");
	t.filterBadge = t.page.find(".filter-count-badge");
	t.state = {
		page: 1,
		selectedDepartment: null,
		selectedTag: config.tag_id || null,
		total: 0
	};

	t.filters = {
		wrapper: t.page.find("#advance-filters"),
		modal: t.page.find("#advanceFilterModal"),
		company: t.page.find("#filter_by_company"),
		category: t.page.find("#filter_by_category"),
		tag: t.page.find("#filter_by_tag"),
		department: t.page.find("#filter_by_department"),
		problemCategory: t.page.find("#filter_by_problem_category"),
		subCategory: t.page.find("#filter_by_sub_category"),
		basedOn: t.page.find("#filter_by_date"),
		dateRangePicker: t.page.find("#reportrange"),
		dateRange: t.page.find("#daterange"),
		status: t.page.find("#filter_by_access"),
		star: t.page.find("#filter_by_star"),
		btnApply: t.page.find("#btnFilterApply"),
		btnClear: t.page.find("#btnClrFilter")
	};

	t.esc = function(value) {
		return $("<div/>").text(value == null ? "" : value).html();
	};

	t.tr = function(key, fallback) {
		return (t.config.translations && t.config.translations[key]) ? t.config.translations[key] : fallback;
	};

	t.articleUrl = function(article) {
		return t.config.url.view_article + "/" + article.id;
	};

	t.imageClass = function(article, extra) {
		return (extra || "") + (article.card_img ? "" : " kd-red-card");
	};

	t.imageStyle = function(article) {
		return article.card_img ? " style=\"background-image:url('" + t.config.url.articleImagePath + "/" + article.card_img + "')\"" : "";
	};

	t.arrow = function() {
		return `<span class="kd-arrow"  data-bs-toggle="tooltip" data-bs-placement="top" title="${t.config.translations.view}" ><svg><use href="#kd-arrow-right"></use></svg></span>`;
	};

	t.pill = function(text, light) {
		return text ? '<span class="kd-pill text-truncate' + (light ? " light" : "") + '">' + t.esc(text) + "</span>" : "";
	};

	t.stackCard = function(article) {
		return '<article class="kd-stack">' +
			'<a href="' + t.articleUrl(article) + '" target="_blank">' +
			'<div class="' + t.imageClass(article, "kd-card-image") + '"' + t.imageStyle(article) + ">" +
			(!article.card_img ? '<span class="kd-watermark">KD</span>' : "") +
			'<div class="kd-overlay"><h4 class="h4-text">' + t.esc(article.title || "") + '</h4>' + t.pill(article.dep_name, true) + "</div>" +
			t.arrow() +
			"</div></a></article>";
	};

	// t.renderDepartments = function(items, selected) {
	// 	var html = "";
	// 	var extraDepartments = [];
	// 	$.each(items || [], function(index, dep) {
	// 		if (index < 4) {
	// 			html += '<button type="button" class="btn department-btn ' + (String(selected || "") === String(dep.id) ? "active_filter_btn" : "") + '" data-id="' + dep.id + '" id="' + dep.id + '">' + t.esc(dep.name) + "</button>";
	// 		} else {
	// 			extraDepartments.push(dep);
	// 		}
	// 	});

	// 	if (extraDepartments.length > 0) {
	// 		html += '<button type="button" class="btn mdl-dep">+ ' + extraDepartments.length + ' more</button>';
	// 	}
	// 	t.department.html(html);

	// 	if (extraDepartments.length > 0) {
	// 		var $moreBtn = t.department.find('.mdl-dep');
	// 		if ($moreBtn.length) {
	// 			var existingPopover = bootstrap.Popover.getInstance($moreBtn[0]);
	// 			if (existingPopover) {
	// 				existingPopover.dispose();
	// 			}

	// 			var popoverContent = '<div class="kd_department_modal">';
	// 			$.each(extraDepartments, function(i, dep) {
	// 				var activeClass = (String(selected || "") === String(dep.id)) ? "active_filter_btn" : "";
	// 				popoverContent += '<button type="button" class="btn department-btn active_filter_btn ' + activeClass + '" data-id="' + dep.id + '" id="' + dep.id + '">' + t.esc(dep.name) + '</button>';
	// 			});
	// 			popoverContent += '</div>';

	// 			new bootstrap.Popover($moreBtn[0], {
	// 				html: true,
	// 				trigger: 'click',
	// 				sanitize: false,
	// 				container: 'body',
	// 				placement: 'bottom',
	// 				title: '<b>More Departments</b>',
	// 				content: popoverContent
	// 			});

	// 			// $(document).off('click', '.kd_department_modal .department-btn').on('click', '.kd_department_modal .department-btn', function() {
	// 			// 	$moreBtn.popover('hide');
	// 			// });
	// 			$(document)
    // .off('click', '.kd_department_modal .tag-btn')
    // .on('click', '.kd_department_modal .tag-btn', function(e) {

    //     e.preventDefault();
    //     e.stopPropagation();

    //     console.log('clicked', $(this).data('id'));

    //     t.state.selectedDepartment = $(this).data('id');
    //     t.state.page = 1;

    //     var popover = bootstrap.Popover.getInstance($moreBtn[0]);
    //     if (popover) {
    //         popover.hide();
    //     }

    //     t.load(false);
    // });
	// 		}
	// 	}
	// };
	t.renderDepartments = function(items, selected) {
		var html = "";
		var extraDepartments = [];
		$.each(items || [], function(index, dep) {
			if (index < 4) {
				html += '<button type="button" class="btn department-btn ' + (String(selected || "") === String(dep.id) ? "active_filter_btn" : "") + '" data-id="' + dep.id + '" id="' + dep.id + '">' + t.esc(dep.name) + "</button>";
			} else {
				extraDepartments.push(dep);
			}
		});

		if (extraDepartments.length > 0) {
			var extraHtml = '';
			var hasSelectedExtra = false;
			$.each(extraDepartments, function(i, dep) {
				var active = String(selected || "") === String(dep.id);
				if (active) hasSelectedExtra = true;
				extraHtml += '<button type="button" class="btn department-btn ' + (active ? "active_filter_btn" : "") + '" data-id="' + dep.id + '" id="' + dep.id + '">' + t.esc(dep.name) + '</button>';
			});

			html += '<div class="kd-more-departments">' +
						'<button type="button" class="btn kd-more-departments-toggle ' + (hasSelectedExtra ? "active_filter_btn" : "") + '" aria-expanded="false">+ ' + extraDepartments.length + ' more</button>' +
						'<div class="kd-department-popover" role="menu">' +
							'<div class="kd-popover-title">More Departments</div>' +   
							extraHtml +
						'</div>' +
					'</div>';
		}
		t.department.html(html);
	};
	t.renderTags = function(items, selected) {
		var html = "";
		var extraHtml = "";
		var hasSelectedExtra = false;
		$.each(items || [], function(index, tag) {
			var active = String(selected || "") === String(tag.id);
			if (index < 21) {
				html += '<button type="button" class="kd-chip tag-btn ' + (active ? "active_filter_btn" : "") + '" data-id="' + tag.id + '" id="' + tag.id + '">' + t.esc(tag.tags) + "</button>";
			} else {
				hasSelectedExtra = hasSelectedExtra || active;
				extraHtml += '<button type="button" class="kd-chip tag-btn ' + (active ? "active_filter_btn" : "") + '" data-id="' + tag.id + '" id="more-tag-' + tag.id + '">' + t.esc(tag.tags) + "</button>";
			}
		});
		if ((items || []).length > 21) {
			html += '<div class="kd-more-tags">' +
				'<button type="button" class="kd-chip more kd-more-tags-toggle ' + (hasSelectedExtra ? "active_filter_btn" : "") + '" aria-expanded="false">+ ' + ((items || []).length - 21) + " more</button>" +
				'<div class="kd-tag-popover" role="menu">' + '<div class="kd-popover-title">More Tags</div>'+ extraHtml + "</div>" +
			"</div>";
		}
		return html;
	};

	t.totalPages = function() {
		return t.state.total <= 7 ? 1 : 1 + Math.ceil((t.state.total - 7) / 4);
	};

	t.toggleLoadMore = function() {
		t.articlePage.find("#kdLoadMoreBtn").toggleClass("d-none", t.state.page >= t.totalPages());
	};
	t.initTooltips = function () {
		$('[data-bs-toggle="tooltip"]').tooltip();
	}
	t.initTooltips = function () {
		$('[data-bs-toggle="tooltip"]').tooltip();
	}
	t.renderPage = function(data, append) {
		var response = data.data || {};
		t.state.total = data.total || 0;
		t.renderDepartments(response.department || [], response.current_department || t.state.selectedDepartment);

		if (append) {
			t.articlePage.find("#kd-list-grid").append($.map(response.list || [], t.stackCard).join(""));
			t.toggleLoadMore();
			return;
		}

		if (!t.state.total) {
			t.articlePage.html('<div class="d-flex justify-content-center align-items-center" style="min-height: 18rem;"><h3 class="text-center">' + t.tr("No_records_Found", "No Record Found") + "</h3></div>");
			t.toggleLoadMore();
			return;
		}

		var popular = response.popular_article || [];
		var most = response.most_visited || [];
		var latest = response.latest_articles || [];
		var list = response.list || [];
		var top = popular[0] || {};
		var latestTop = latest.slice(0, 4);
		var mosaic = latest.slice(4, 9);
		var html = '<section class="kd-top-grid"><article class="kd-feature"><h3 class="h3-text">' + t.tr("popular_articles", "Popular Articles") + "</h3>";

		if (top.id) {
			html += '<a href="' + t.articleUrl(top) + '" target="_blank">' +
				'<div class="' + t.imageClass(top, "kd-card-image kd-hero") + '"' + t.imageStyle(top) + ">" +
				(!top.card_img ? '<span class="kd-watermark large">KD</span>' : "") + t.arrow() + "</div>" +
				'<div class="kd-meta"><span class="kd-pill">' + t.esc(top.dep_name || top.category_name || "") + "</span><span>" + t.esc(top.updated_at_format || "") + "</span></div>" +
				'<h4 class="h4-text">' + t.esc(top.title || "") + "</h4><p>" + t.esc(top.content || "") + "</p></a>";
		}
		html += '</article><aside class="kd-most"><h3 class="h3-text">' + t.tr("most_viewed", "Most Viewed") + "</h3>";
		$.each(most, function(_, article) {
			html += '<article class="kd-side-card"><a href="' + t.articleUrl(article) + '" target="_blank">' +
				'<div class="' + t.imageClass(article, "kd-side-image kd-mini-red") + '"' + t.imageStyle(article) + ">" + t.arrow() + "</div>" +
				'<div class="kd-side-copy"><h4 class="h4-text text-truncate">' + t.esc(article.title || "") + "</h4>" + t.pill(article.dep_name, false) + "<small>" + t.esc(article.updated_at_format || "") + "</small></div>" +
				"</a></article>";
		});
		html += '</aside></section><div class="kd-red-rule"></div>';

		html += '<section class="kd-section"><h3 class="h3-text">' + t.tr("latest_articles", "Latest Articles") + '</h3><hr><div class="kd-latest-grid">';
		html += $.map(latestTop, t.stackCard).join("");
		html += '</div><div class="kd-latest-mosaic">';
		$.each(mosaic, function(index, article) {
			html += '<article class="' + (index === 0 ? "kd-wide" : "kd-small") + '"><a href="' + t.articleUrl(article) + '" target="_blank">' +
				'<div class="' + t.imageClass(article, "kd-card-image") + '"' + t.imageStyle(article) + ">" +
				(!article.card_img && index === 0 ? '<span class="kd-watermark large">KD</span>' : "") + t.arrow() + "</div>" +
				'<div class="kd-meta kd-under-meta">' + t.pill(article.dep_name, false) + "<span>" + t.esc(article.updated_at_format || "") + "</span></div>" +
				'<h4 class="h4-text">' + t.esc(article.title || "") + "</h4></a></article>";
		});
		html += "</div></section>";

		html += '<section class="kd-section kd-tags-section"><h3 class="h3-text">' + t.tr("tags", "Tags") + "</h3>" +
			'<div class="kd-tags" id="kd-tags-list">' + t.renderTags(response.allTags || [], response.current_tag || t.state.selectedTag) + "</div>" +
			'<div class="kd-tag-card-grid" id="kd-list-grid">' + $.map(list, t.stackCard).join("") + "</div></section>" +
			`<button type="button" class="kd-load-more" data-bs-toggle="tooltip" data-bs-placement="top" title="${t.config.translations.load_more}"  id="kdLoadMoreBtn"><svg><use href="#kd-arrow-down"></use></svg></button><div class="kd-red-rule"></div>`;

		t.articlePage.html(html);
		t.toggleLoadMore();
		t.initTooltips();
	};

	t.showArticlePage = function() {
		t.articlePage.removeClass("kd-api-pending");
	};

	t.filterValues = function() {
		var filters = {};
		var company = t.filters.company.val();
		var category = t.filters.category.val();
		var tag = t.filters.tag.val();
		var department = t.filters.department.val();
		var problemCategory = t.filters.problemCategory.val();
		var subCategory = t.filters.subCategory.val();
		var status = t.filters.status.val();
		var basedOn = t.filters.basedOn.val();
		var dateRange = t.filters.dateRange.val();
		var star = t.filters.star.val();
		var startDate = t.filters.dateRangePicker.attr("data-start");
		var endDate = t.filters.dateRangePicker.attr("data-end");

		if (company && company.length) filters.company = company;
		if (category && category.length) filters.category = category;
		if (tag && tag.length) filters.tag = tag;
		if (department && department !== "null" && department !== "0") filters.department = department;
		if (!filters.department && t.state.selectedDepartment) filters.department = t.state.selectedDepartment;
		if (!filters.tag && t.state.selectedTag) filters.tag = t.state.selectedTag;
		if (problemCategory && problemCategory.length) filters.problem_category = problemCategory;
		if (subCategory && subCategory.length) filters.sub_category = subCategory;
		if (status && status.length) filters.status_access = status;

		if (basedOn && basedOn !== "null" && dateRange && (!startDate || !endDate) && window.moment) {
			var rangeParts = dateRange.split(" - ");
			if (rangeParts.length === 2) {
				var parsedStart = moment(rangeParts[0], ["DD-MM-YYYY HH:mm:ss", "YYYY-MM-DD HH:mm:ss", "DD-MM-YYYY", "YYYY-MM-DD", "MM/DD/YYYY"], true);
				var parsedEnd = moment(rangeParts[1], ["DD-MM-YYYY HH:mm:ss", "YYYY-MM-DD HH:mm:ss", "DD-MM-YYYY", "YYYY-MM-DD", "MM/DD/YYYY"], true);
				if (parsedStart.isValid() && parsedEnd.isValid()) {
					startDate = parsedStart.format("YYYY-MM-DD HH:mm:ss");
					endDate = parsedEnd.format("YYYY-MM-DD HH:mm:ss");
				}
			}
		}

		if (basedOn && basedOn !== "null" && startDate && endDate) {
			filters.based_on = basedOn;
			filters.daterange = dateRange;
			filters.start_date = startDate;
			filters.end_date = endDate;
		}

		if (star && star !== "null") filters.star = star;
		return filters;
	};

	t.updateFilterBadge = function() {
		var count = 0;
		if (t.filters.company.val() && t.filters.company.val() !== '') count++;
		if (t.filters.department.val() && t.filters.department.val() !== 'null' && t.filters.department.val() !== '0') count++;
		if (t.filters.category.val() && t.filters.category.val().length) count++;
		if (t.filters.tag.val() && t.filters.tag.val().length) count++;
		if (t.filters.problemCategory.val() && t.filters.problemCategory.val().length) count++;
		if (t.filters.subCategory.val() && t.filters.subCategory.val().length) count++;
		if (t.filters.basedOn.val() && t.filters.basedOn.val() !== 'null');
		if (t.filters.dateRange.val() && t.filters.basedOn.val() && t.filters.basedOn.val() !== 'null') count++;
		if (t.filters.status.val() && t.filters.status.val().length) count++;
		if (t.filters.star.val() && t.filters.star.val() !== 'null') count++;
		t.filterBadge.text(count).toggleClass('d-none', count === 0);
	};

	t.load = function(append) {
		var filters = t.filterValues();
		var rendered = false;
		t.config.other_filters = filters;
		t.updateFilterBadge();
		if (append !== true) {
			t.articlePage.addClass("kd-api-pending");
		}
		t.loader.removeClass("hide");
		$.ajax({
			url: t.config.url.article_list,
			type: "POST",
			data: {
				_token: t.config.token,
				search: t.searchbox.find("input").val(),
				page: t.state.page,
				filters: filters,
				tag_id: ""
			}
		}).done(function(data) {
			if (typeof data === "object") {
				t.renderPage(data, append === true);
				t.showArticlePage();
				rendered = true;
			}
		}).always(function() {
			t.loader.addClass("hide");
			if (!rendered) {
				t.showArticlePage();
			}
		});
	};

	t.initFilters = function() {
		var select2Opts = { width: "100%", dropdownParent: t.filters.wrapper };
		$.each(t.config.category || [], function(_, category) {
			if (category.category_name) {
				t.filters.category.append(new Option(category.category_name, category.id, false, false));
			}
		});
		t.filters.category.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_by_category, allowClear: true }));

		$.each(t.config.tag || [], function(_, tag) {
			if (tag.tags && tag.tags !== "null") {
				t.filters.tag.append(new Option(tag.tags, tag.id, false, false));
			}
		});
		t.filters.tag.select2($.extend({}, select2Opts, { placeholder: t.config.translations.tag, allowClear: true }));
		
		t.filters.company.select2($.extend({}, select2Opts, {
			ajax: {
				url: t.config.url.get_company_by_user_access,
				dataType: "json",
				method: "get",
				data: function (params) {
					return {
						search: params.term,
						page: params.page || 1
					};
				},
				delay: 300
			},
			allowClear: true,
			placeholder: t.config.translations.filter_by_company 
		})).on("select2:select select2:clear", function () {
			t.filters.department.val("").trigger("change");
			t.filters.problemCategory.val("").trigger("change");
			t.filters.subCategory.val("").trigger("change");
		});
		t.filters.department.select2($.extend({}, select2Opts, {
			ajax: {
				url: t.config.url.departments_with_company,
				dataType: "json",
				method: "get",
				data: function(params) {
					return { search: params.term,company_id: t.filters.company.val(), page: params.page || 1 };
				}
			},
			allowClear: true,
			placeholder: t.config.translations.filter_by_department
		})).on("select2:select select2:clear", function() {
			t.filters.problemCategory.val(null).trigger("change");
			t.filters.subCategory.val(null).trigger("change");
		});

		t.filters.problemCategory.select2($.extend({}, select2Opts, {
			ajax: {
				url: t.config.url.fetch_category_by_ajax,
				dataType: "json",
				method: "get",
				data: function(params) {
					return {
						search: params.term,
						department: t.filters.department.val(),
						page: params.page || 1
					};
				},
				processResults: function(data, params) {
					params.page = params.page || 1;
					return {
						results: (data.results || []).map(function(item) {
							return { id: item.id, text: item.text };
						}),
						pagination: { more: (params.page * 10) < data.total_count }
					};
				},
				delay: 300
			},
			allowClear: true,
			placeholder: t.config.translations.Filter_By_Problem_Category
		})).on("select2:select select2:clear", function() {
			t.filters.subCategory.val(null).trigger("change");
		});

		t.filters.subCategory.select2($.extend({}, select2Opts, {
			ajax: {
				url: t.config.url.fetch_subcategory_by_ajax,
				dataType: "json",
				method: "get",
				data: function(params) {
					return {
						search: params.term,
						sub_category: t.filters.problemCategory.val(),
						page: params.page || 1
					};
				},
				processResults: function(data, params) {
					params.page = params.page || 1;
					return {
						results: (data.results || []).map(function(item) {
							return { id: item.id, text: item.text };
						}),
						pagination: { more: (params.page * 10) < data.total_count }
					};
				},
				delay: 300
			},
			allowClear: true,
			placeholder: t.config.translations.Filter_By_Sub_Category
		}));

		t.filters.basedOn.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_by_date }));
		t.filters.status.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_article_status }));
		t.filters.star.select2($.extend({}, select2Opts, { placeholder: t.config.translations.filter_article_star }));

		t.initDateRangePicker = function() {
			if (!$.fn.daterangepicker || !t.filters.dateRangePicker.length) {
				return;
			}

			var $dateRangePicker = t.filters.dateRangePicker;
			var $dateRange = t.filters.dateRange;
			var start = moment().startOf('day');
			var end = moment().endOf('day');

			if ($dateRangePicker.data('daterangepicker')) {
				return;
			}

			function updateDateRange(startDate, endDate) {
				$dateRangePicker.find("span").html(startDate.format('DD-MM-YYYY HH:mm:ss') + ' - ' + endDate.format('DD-MM-YYYY HH:mm:ss'));
				$dateRange.val(startDate.format('YYYY-MM-DD HH:mm:ss') + ' - ' + endDate.format('YYYY-MM-DD HH:mm:ss'));
				$dateRangePicker.attr('data-start', startDate.format('YYYY-MM-DD HH:mm:ss'));
				$dateRangePicker.attr('data-end', endDate.format('YYYY-MM-DD HH:mm:ss'));
			}

			function clearDateRange() {
				$dateRangePicker.find("span").empty();
				$dateRange.val("");
				$dateRangePicker.removeAttr("data-start data-end");
			}

			$dateRangePicker.daterangepicker({
				parentEl: '#advanceFilterModal',
				startDate: start,
				endDate: end,
				timePicker: true,
				timePicker24Hour: true,
				timePickerSeconds: true,
				timePickerIncrement: 1,
				autoApply: false,
				autoUpdateInput: false,
				opens: 'right',
				drops: 'down',
				locale: {
					format: 'DD-MM-YYYY HH:mm:ss',
					cancelLabel: 'Clear',
					separator: ' - '
				},
				ranges: {
					'Today': [
						moment().startOf('day'),
						moment().endOf('day')
					],
					'Yesterday': [
						moment().subtract(1, 'days').startOf('day'),
						moment().subtract(1, 'days').endOf('day')
					],
					'Last 7 Days': [
						moment().subtract(6, 'days').startOf('day'),
						moment().endOf('day')
					],
					'Last 30 Days': [
						moment().subtract(29, 'days').startOf('day'),
						moment().endOf('day')
					],
					'This Month': [
						moment().startOf('month'),
						moment().endOf('month')
					],
					'Last Month': [
						moment().subtract(1, 'month').startOf('month'),
						moment().subtract(1, 'month').endOf('month')
					]
				}
			}, updateDateRange);

			updateDateRange(start, end);

			$dateRangePicker.off('apply.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
				updateDateRange(picker.startDate, picker.endDate);
			});

			$dateRangePicker.off('cancel.daterangepicker').on('cancel.daterangepicker', clearDateRange);
		};

	};

	t.openFilter = function() {
		if (t.filters.modal.length && window.bootstrap && bootstrap.Modal) {
			bootstrap.Modal.getOrCreateInstance(t.filters.modal[0]).show();
		} else {
			t.filters.modal.modal("show");
		}
		setTimeout(function() {
			if (typeof t.initDateRangePicker === "function") {
				t.initDateRangePicker();
			}
		}, 300);
	};

	// t.clearFilters = function(e) {
	// 	if (e) e.preventDefault();
	// 	t.filters.wrapper.find("select").val(null).trigger("change");
	// 	t.filters.basedOn.val("null").trigger("change");
	// 	t.filters.star.val("null").trigger("change");
	// 	t.filters.dateRange.val("");
	// 	t.searchbox.find("input").val("");
	// 	t.state.selectedDepartment = null;
	// 	t.state.selectedTag = t.config.tag_id || null;
	// 	t.state.page = 1;
	// 	t.load(false);
	// };

	t.clearFilters = function(e) {
		if (e) e.preventDefault();

		// Reset all select2 fields
		t.filters.wrapper.find("select").val(null).trigger("change");
		// Reset the date type dropdown
		t.filters.basedOn.val("null").trigger("change");
		// Reset star filter
		t.filters.star.val("null").trigger("change");
		// Clear date range input and picker
		t.filters.dateRange.val("");
		t.filters.dateRangePicker.find("span").empty();
		t.filters.dateRangePicker.removeAttr("data-start data-end");
		if (t.filters.dateRangePicker.data('daterangepicker')) {
			t.filters.dateRangePicker.data('daterangepicker').setStartDate(moment().startOf('day'));
			t.filters.dateRangePicker.data('daterangepicker').setEndDate(moment().endOf('day'));
		}
		// Clear search box
		t.searchbox.find("input").val("");
		// Reset department & tag selections
		t.state.selectedDepartment = null;
		t.state.selectedTag = t.config.tag_id || null;
		t.state.page = 1;
		t.updateFilterBadge(); 
		// Reload the list
		t.load(false);

		// Close the modal
		if (t.filters.modal.length) {
			var modalInstance = bootstrap.Modal.getInstance(t.filters.modal[0]);
			if (modalInstance) {
				modalInstance.hide();
			} else {
				t.filters.modal.modal('hide');
			}
		}
	};


	t.initFilters();
	t.load(false);

	t.page.on("click.kdDynamic", ".btn-visible-content", $.proxy(t.openFilter));
	t.searchbox.on("keypress.kdDynamic", "input", function(e) {
		if (e.which === 13) {
			t.state.page = 1;
			t.load(false);
		}
	});
	t.searchbox.on("click.kdDynamic", ".btn-searchbox, .btn-reload", function() {
		t.state.page = 1;
		if ($(this).hasClass("btn-reload")) {
			t.searchbox.find("input").val("");
			t.state.selectedDepartment = null;
			t.state.selectedTag = t.config.tag_id || null;
		}
		t.load(false);
	});
	t.page.on("click.kdDynamic", ".btn-reload", function() {
		t.state.page = 1;
		t.searchbox.find("input").val("");
		t.state.selectedDepartment = null;
		t.state.selectedTag = t.config.tag_id || null;
		t.load(false);
	});
	// t.filters.btnApply.on("click.kdDynamic", function(e) {
	// 	e.preventDefault();
	// 	t.state.page = 1;
	// 	t.load(false);
	// });
	t.filters.btnApply.on("click.kdDynamic", function(e) {
		e.preventDefault();
		t.state.page = 1;
		t.load(false);

		// Close the modal after applying filters
		if (t.filters.modal.length) {
			var modalInstance = bootstrap.Modal.getInstance(t.filters.modal[0]);
			if (modalInstance) {
				modalInstance.hide();
			} else {
				// Fallback for older Bootstrap versions
				t.filters.modal.modal('hide');
			}
		}
	});

	
	t.filters.btnClear.on("click.kdDynamic", $.proxy(t.clearFilters));
	$(document).on("click.kdDynamic", ".department-btn", function(e) {
		e.preventDefault();
		t.state.selectedDepartment = $(this).hasClass("active_filter_btn") ? null : $(this).data("id");
		t.state.page = 1;
		t.load(false);
	});
	$(document).on("click.kdDynamic", "#kd-tags-list .tag-btn", function(e) {
		e.preventDefault();
		t.state.selectedTag = $(this).hasClass("active_filter_btn") ? null : $(this).data("id");
		t.state.page = 1;
		$(this).closest(".kd-more-tags").removeClass("is-open").find(".kd-more-tags-toggle").attr("aria-expanded", "false");
		t.load(false);
	});
	$(document).on("click.kdDynamic", "#kd-tags-list .kd-more-tags-toggle", function(e) {
		e.preventDefault();
		e.stopPropagation();
		var wrapper = $(this).closest(".kd-more-tags");
		$("#kd-tags-list .kd-more-tags").not(wrapper).removeClass("is-open").find(".kd-more-tags-toggle").attr("aria-expanded", "false");
		wrapper.toggleClass("is-open");
		$(this).attr("aria-expanded", wrapper.hasClass("is-open") ? "true" : "false");
	});
	$(document).on("click.kdDynamic", function(e) {
		if ($(e.target).closest(".kd-more-tags").length) {
			return;
		}
		$("#kd-tags-list .kd-more-tags").removeClass("is-open").find(".kd-more-tags-toggle").attr("aria-expanded", "false");
	});
	$(document).on("click.kdDynamic", ".kd-more-departments .kd-more-departments-toggle", function(e) {
		e.preventDefault();
		e.stopPropagation();
		var wrapper = $(this).closest(".kd-more-departments");
		$(".kd-more-departments").not(wrapper).removeClass("is-open").find(".kd-more-departments-toggle").attr("aria-expanded", "false");
		wrapper.toggleClass("is-open");
		$(this).attr("aria-expanded", wrapper.hasClass("is-open") ? "true" : "false");
	});

	$(document).on("click.kdDynamic", function(e) {
		if ($(e.target).closest(".kd-more-departments").length) return;
		$(".kd-more-departments").removeClass("is-open").find(".kd-more-departments-toggle").attr("aria-expanded", "false");
	});
	$(document).on("click.kdDynamic", "#kdLoadMoreBtn", function() {
		t.state.page++;
		t.load(true);
	});
};

var PublicArticleLegacy = function(config) {
	var t = this;
	t.config = config;
	t.page = $("#page_boxed");
	t.httpCall = true;
	t.httpPostPath = "";
	t.home = t.page.find("#knowledge-data-container");
	t.department=t.page.find(".department");
	t.departmentBtn=t.page.find(".department-btn");
	t.tagBtn=t.page.find(".tag-btn");
	t.searchbox = t.page.find(".search-cover-box");
	t.total = 0;
	t.perPage = 1;
	t.api_loader = t.page.find("#api_loader");
	t.data = {};

	t.filters = {
		wrapper: t.page.find("#advance-filters"),
		data: {
			problem_categories: {}
		}
	};
	t.filters.btnfilterclr = t.page.find(".advance-filters #btnClrFilter");
	t.filters.company = t.page.find(".advance-filters #filter_by_company");
	t.filters.category = t.page.find(".advance-filters #filter_by_category");
	t.filters.tag = t.page.find(".advance-filters #filter_by_tag");
	t.filters.btnFilterApply = t.page.find(".advance-filters #btnFilterApply");
	t.filters.based_on = t.page.find("#filter_by_date"),
	t.filters.department = t.filters.wrapper.find("#filter_by_department"),
	t.filters.problem_category = t.filters.wrapper.find("#filter_by_problem_category"),
	t.filters.sub_category = t.filters.wrapper.find("#filter_by_sub_category"),
	t.filters.status_access = t.filters.wrapper.find("#filter_by_access"),
	t.filters.daterange = t.page.find("#daterange");
	t.filters.star = t.filters.wrapper.find("#filter_by_star"),

	// if($.fn.daterangepicker && t.filters.daterange.length) {
	// 	t.filters.daterange.daterangepicker({
	// 		autoUpdateInput: false,
	// 		parentEl: "#advance-filters",
	// 		locale: {
	// 			format: "YYYY-MM-DD",
	// 			cancelLabel: "Clear",
	// 			separator: " - "
	// 		}
	// 	});
	// 	t.filters.daterange.on("apply.daterangepicker", function(ev, picker) {
	// 		$(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
	// 	}).on("cancel.daterangepicker", function() {
	// 		$(this).val('');
	// 	});
	// }

	t.filters.fun = {
		reload_category: function() {
			t.filters.category.append(new Option('Filter By Category', '', false, false))
			$.each(t.config.category, function(i, k) {
				t.filters.category.append(new Option(k.category_name, k.id, false, false));
			});
			t.filters.category.trigger("change");
		},
		reload_tag: function() {
			$.each(t.config.tag, function(i, k) {
				if (k.tags && k.tags != 'null') t.filters.tag.append(new Option(k.tags, k.id, false, false));
			});
			t.filters.tag.trigger("change");
		},
	};

	t.btnClrFilter = function() {
		t.filters.category.val("").trigger("change");
		t.filters.tag.val("").trigger("change");
		t.filters.based_on.val("null").trigger("change");
		t.filters.department.val(0).trigger("change");
		t.filters.problem_category.val(0).trigger("change");
		t.filters.sub_category.val(0).trigger("change");
		t.filters.status_access.val("").trigger("change");
		t.filters.star.val("null").trigger("change");
		t.filters.daterange.val("");
		if (t.filters.daterange.data('daterangepicker')) {
			t.filters.daterange.data('daterangepicker').setStartDate(moment().startOf('month'));
			t.filters.daterange.data('daterangepicker').setEndDate(moment().endOf('month'));
		}
		
		t.searchbox.find("input").val("");
		t.config.search = "";
		
		t.config.other_filters = {};
		resetFilterCount();  // if defined, otherwise remove or define
		t.load();

		// Close modal
		var modalEl = document.getElementById('advanceFilterModal');
		if (modalEl) {
			var modal = bootstrap.Modal.getInstance(modalEl);
			if (modal) modal.hide();
		}
	};


	var selectedPageFilters=[];//array of object keeping tract of applied filter and tag in main page.
	t.cache_pagefilter_values=function(ele){
		t.config.other_filters = {};
		if(ele){
			if($(ele).hasClass('active_filter_btn')) {
				var ele_id= null;
			} else {
				var ele_id= $(ele).attr('id');
			}
		}
		if($(ele).hasClass('tag-btn')){
			if(selectedPageFilters != []){
				t.config.other_filters.department = selectedPageFilters[0] && selectedPageFilters[0].department ? selectedPageFilters[0].department : [];
			}
			t.config.other_filters.tag = ele_id;

	   	}else if($(ele).hasClass('department-btn')){
			t.config.other_filters.department =ele_id;
		}else{
			t.config.other_filters.department = selectedPageFilters[0] && selectedPageFilters[0].department ? selectedPageFilters[0].department : [];
			t.config.other_filters.tag = selectedPageFilters[0] && selectedPageFilters[0].tag ? selectedPageFilters[0].tag : [];
		}

	}

	t.cache_filter_values = function() {
		t.config.other_filters = {};
		if(t.filters.category.val() && t.filters.category.val() != 'null') {
			t.config.other_filters.category = t.filters.category.val();
		}
		if(t.filters.tag.val() && t.filters.tag.val() != 'null') {
			t.config.other_filters.tag = t.filters.tag.val();
		}
		if(t.filters.based_on.val() && t.filters.based_on.val() != 'null') {
			t.config.other_filters.based_on = t.filters.based_on.val();
		}
		if(t.filters.daterange.val() && t.filters.daterange.val() != 'null') {
			t.config.other_filters.daterange = t.filters.daterange.val();
		}
		if(t.filters.department.val() && t.filters.department.val() != 'null') {
			t.config.other_filters.department = t.filters.department.val();
		}
		if(t.filters.problem_category.val() && t.filters.problem_category.val() != 'null') {
			t.config.other_filters.problem_category = t.filters.problem_category.val();
		}
		if(t.filters.sub_category.val() && t.filters.sub_category.val() != 'null') {
			t.config.other_filters.sub_category = t.filters.sub_category.val();
		}
		if(t.filters.status_access.val() && t.filters.status_access.val() != 'null') {
			t.config.other_filters.status_access = t.filters.status_access.val();
		}
		if(t.filters.star.val() && t.filters.star.val() != 'null') {
			t.config.other_filters.star = t.filters.star.val();
		}
		filterCount(t.config.other_filters, t.filters.based_on.val(), false);
	};

	var select2Opts = {
		width: "100%"
	};
	t.filters.category.select2($.extend({}, select2Opts, {
		placeholder: "Filter By Category"
	}));
	t.filters.tag.select2($.extend({}, select2Opts, {
		placeholder: t.config.translations.tag
	}));

	t.filters.department.select2($.extend({}, select2Opts, { placeholder: config.translations.filter_by_department }));
	t.filters.department.on("change", $.proxy(t.filters.fun.reload_ticket_type));
	t.filters.problem_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Problem_Category }));
	t.filters.sub_category.select2($.extend({}, select2Opts, { placeholder: config.translations.Filter_By_Sub_Category }));
	t.filters.based_on.select2($.extend({}, select2Opts, {placeholder: config.translations.filter_by_date}));
	t.filters.status_access.select2($.extend({}, select2Opts, {placeholder: config.translations.filter_article_status}));
	t.filters.star.select2($.extend({}, select2Opts, {placeholder: config.translations.filter_article_star}));
	t.filters.fun.reload_category();
	t.filters.fun.reload_tag();
	t.filters.department.select2(
        $.extend({}, select2Opts, {
            ajax: {
                url: t.config.url.departments_with_company,
                dataType: "json",
                method: 'get',
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
						company_id: t.filters.company.val(),
                    }
                    return query;
                }
            },
            allowClear: true,
            placeholder: config.translations.filter_by_department,
        })
    ).on('select2:select', function (e) {
        t.filters.problem_category.val('').trigger('change');
        t.filters.sub_category.val('').trigger('change');
    });

    t.filters.problem_category.select2($.extend({}, select2Opts, {
        ajax: {
            url: function() { return  t.config.url.fetch_category_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    department: t.filters.department.val(),
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
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
        placeholder: config.translations.Filter_By_Problem_Category,
    })).on('select2:select', function (e) {
        t.filters.sub_category.val('').trigger('change');
    });

    t.filters.sub_category.select2($.extend({}, select2Opts, {
        ajax: {
            url: function () { return t.config.url.fetch_subcategory_by_ajax },
            dataType: "json",
            method: 'get',
            data: function (p) {
                return {
                    search: p.term,
                    sub_category: t.filters.problem_category.val(),
                    page: p.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
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
        placeholder: config.translations.Filter_By_Sub_Category,
    }));

	t.search = function(e, ele='') {
		var target = e.target || e.currentTarget;
		if (e.keyCode == 13 || $(this).is("span")) {
			var v = t.searchbox.find("input").validate_str_param();
			if (v === false) {
				t.config.search = "";
				alert("Please enter a valid value for search");
				return false;
			}
			t.config.search = v;
			t.load();
		} else if (target.tagName == "BUTTON") {
			t.cache_filter_values();
			t.load();
		} else {
			t.cache_pagefilter_values(ele);
			t.load();
		}
	};

	t.staringUi = function(element, val) {
        var s = (typeof val != "undefined") ? val : t.data.self_star;
        if (s == true) {
            $(element).html('<i class="fa fa-star larger-icon list_icon"></i>');
        }
		else {
            $(element).html('<i class="fa fa-star-o larger-icon" style="color: white;"></i>');
        }
    };

    t.staring = function(e) {
        e.preventDefault();
        if (t.httpCall != true) {
            return false;
        }
        var id = this.id;
        var element = this;
        sweetAlertConfirm({
            message: config.translations.are_you_star,
			url: t.config.url.staring,
			type: "POST",
			data: {
				"_token": t.config.token,
				"id": id
            },
            onSuccess: function(data) {
				t.staringUi(element,data.star);
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

    t.load = function(append = false) {
		t.api_loader.removeClass('hide');
		$('#loadMoreBtn').addClass('hide');
		if(append !=true) {
			t.home.empty();
		}
        t.httpPostPath = t.config.url.article_list;
        var http = $.ajax({
            url: t.httpPostPath,
            type: "POST",
            data: {
                search: t.config.search,
                page: t.perPage,
                filters: t.config.other_filters,
				tag_id: t.config.tag_id,
            }
        });
        http.done(function(data) {
            if (typeof data == "object") {
				t.department.empty();
				t.api_loader.addClass('hide');
                t.data = data;
				t.totalPages = Math.ceil(data.total / data.page);
                let html = '';
				let dep_html='';
                let response = data.data;
                const baseUrl = config.url.articleImagePath;
                const baseViewUrl = config.url.view_article;
				if(append !=true) {
					if (response.current_department  != null && response.current_department != '') {
						selectedPageFilters = selectedPageFilters.filter(item => !item.department);//if present, filter it out
						selectedPageFilters.push({ department: response.current_department });
					}
					if(response.current_tag != null && response.current_tag != ''){
						selectedPageFilters = selectedPageFilters.filter(item => !item.tag); //if present, filter it out
						selectedPageFilters.push({ tag: response.current_tag });
					}
					// Popular Articles
					if (response.popular_article && response.popular_article.length > 0) {
						html += `<div class="col-md-8"><h2><strong>Popular Articles</strong></h2>`;
						response.popular_article.forEach((pa) => {
							html += `
							<div class="card">
								<a href="${baseViewUrl}/${pa.id}" target="_blank">
								<div class="image-wrapper" style="background-image: url('${baseUrl}/${pa.card_img ?? '18.png'}');">
									<div class="arro_back">
											<div>
												<svg class="hex-icon-color" width="55" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
												<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
												</svg>
											</div>
									</div>
								</div>
								<div class="card-body clearfix">
									<div class="pull-left gray-tags gap-2">
										<span>${pa.category_name ?? ''}</span>
										${(pa.sub_cat) ?`<span>|</span>
										<span>${pa.sub_cat}</span>`:''}
									</div>
									<div class="pull-right text-muted">
										<i class="fa fa-clock-o"> ${pa.updated_at_format ?? ''}</i>
									</div>
								</div>
								<div class="card-footer">
									<h4 style="margin-top: 0px;"><strong data-toggle="tooltip" style="font-size: 24px;" data-original-title="${pa.title ?? ''}">${pa.title && pa.title.length > 30 ? pa.title.substr(0, 30) + "..." : pa.title}</strong></h4>
									<p class="text-muted" data-toggle="tooltip" data-original-title="${pa.content ?? ''}">${pa.content && pa.content.length > 50 ? pa.content.substr(0, 50) + "..." : pa.content}</p>
									${(pa.dep_name) ? `<button class="btn btn-outline-gray rounded-pill">${pa.dep_name}</button>` : ''}
								</div>
								</a>
							</div>
							`;
						});
						html += `</div>`;
					}
					// Most Visited
					if (response.most_visited && response.most_visited.length > 0) {
						html += `<div class="col-md-4 most-visit-article" style="margin-top: 41px;"><h3><strong>Most Viewed</strong></h3>`;
						response.most_visited.forEach((mvq) => {
							html += `
								<div class="side-card">
									<div class="row">
										<!-- Wider Image Column -->
										<a href="${baseViewUrl}/${mvq.id}" target="_blank">
											<div class="col-xs-7 media-img-wrapper">
												<img src="${baseUrl}/${mvq.card_img ?? '18.png'}" alt="Side Article"
													class="media-img img-responsive">
											<div class="arrow-back">
												<svg class="hex-icon-color" width="30" height="30" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
												<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
												</svg>
												</div>
											</div>
										<div class="col-xs-5 article-content">
											<h4 data-toggle="tooltip" data-original-title="${mvq.title ?? ''}" style="margin-top: 0px;"><strong>${mvq.title && mvq.title.length > 30 ? mvq.title.substr(0, 30) + "..." : mvq.title}</strong></h4>
												${(mvq.dep_name) ? `<div class="gray-tags"><button class="btn btn-outline-gray rounded-pill">${mvq.dep_name}</button></div>` : ''}
												<small class="text-muted"><i class="fa fa-clock-o"></i> ${mvq.updated_at_format ?? ''}</small>
											</div>
										</a>
									</div>
								</div>
							`;
						});
						html += `</div>`;
					}
					const maxCards = 4;
					if (response.latest_articles && response.latest_articles.length > 0) {
						let totalCards = response.latest_articles.length;
						html += `<div class="col-md-12">`;
						response.latest_articles.forEach((la, index) => {
							if (index < maxCards) {
								if (index === 0) {
									html += `<div class="col-md-12"><h2>Latest Articles</h2><hr><div class="card-container row">`;
								}
								html += `
									<div class="col-md-3">
										<a href="${baseViewUrl}/${la.id}" target="_blank">
											<div class="card" style="background-image: url('${baseUrl}/${la.card_img ?? '18.png'}');">
												<div class="big-kd">KD</div>
												<div class="card-content">
													<div class="title" data-toggle="tooltip" data-original-title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></div>
													<div class="tags">
														${(la.dep_name) ? `<div class="tag">${la.dep_name}</div>` : ''}
													</div>
												</div>
												<div class="arro_back">
													<svg class="hex-icon-color" width="39" height="39" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
													<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
													</svg>
												</div>
											</div>
										</a>
									</div>
								`;
								if (index === maxCards - 1) {
									html += `</div></div>`;
								}
							}
							else if (index === maxCards) {

								if(index === 4) {
									html += `<div class="section latest_artical">`;
								}
								// 5th article
								html += `<div class="row">
											<div class="col-md-6">
												<a href="${baseViewUrl}/${la.id}" target="_blank">
													<div class="card">
														<div class="image-wrapper" style="background-image: url('${baseUrl}/${la.card_img ?? '18.png'}'); background-position-y: center;">
															<div class="arro_back">
																<div>
																	<svg class="hex-icon-color" width="55" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
																	<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
																	</svg>
																</div>
															</div>
														</div>
														<div class="card-body clearfix">
															<div class="gray-tags gap-2">
																${(la.dep_name) ? `<button class="btn btn-outline-gray rounded-pill">${la.dep_name}</button>` : ''}
																<span><i class="fa fa-clock-o"></i> ${la.updated_at_format ?? ''}</span>
															</div>
														</div>
														<div class="card-footer">
															<h4 data-toggle="tooltip" data-original-title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></h4>
														</div>
													</div>
												</a>
											</div>
										`;
					
								// if only 1 extra article, close the row here
								if (totalCards === maxCards + 1) {
									html += `</div>`; // closes row
								}
							}
							else if (index > maxCards) {
								// for 6th and beyond: put two cards in col-md-6
								if (index === maxCards + 1) {
									html += `<div class="col-md-6"><div class="row">`;
								}
					
								html += `<div class="col-md-6">
											<div class="card">
											<a href="${baseViewUrl}/${la.id}" target="_blank">
												<div class="image-wrapper-common" style="background-image: url('${baseUrl}/${la.card_img ?? '18.png'}'); background-position-y: center;">	
													<div class="arrow_back">
														<svg class="hex-icon-color" width="40" height="40" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
														<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
														<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
														</svg>
													</div>
												</div>
												<div class="card-body clearfix">
													<div class="pull-left gray-tags gap-2">
														${(la.dep_name) ? `<button class="btn btn-outline-gray rounded-pill">${la.dep_name}</button>` : ''}
														<span><i class="fa fa-clock-o"></i> ${la.updated_at_format ?? ''}</span>
													</div>
												</div>
												<div class="card-footer">
													<h4 data-toggle="tooltip" data-original-title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></h4>
												</div>
											</a>
											</div>
										</div>`;
								
								// close the wrapper after last article
								if (index === totalCards - 1) {
									html += `</div></div>`; // closes row and section
								}
							}
						});
						html += `</div>`;
					}
				}
				console.log(response.department);
				if(response.department && response.department.length > 0){
					let extraDepartments=[];
					response.department.forEach((dep,index)=>{
						if(index <= 3){
							dep_html += `<p class='btn department-btn ${response.current_department == dep.id ? "active_filter_btn" : ""}' data-id='${dep.id}' id='${dep.id}'>${dep.name}</p>`;
						}else{
							extraDepartments.push(dep);
						}
					})
					if(extraDepartments && extraDepartments.length > 0){
						dep_html += `
							<button class="btn mdl-dep" data-toggle="department-popover">
								+ ${response.department.length - 4} more
							</button>
						`;
					}
					t.department.append(dep_html);
					if(extraDepartments.length > 0){
						t.department.find(".mdl-dep").popover(
							{
								html: true, 
								trigger: 'focus',
								placement: 'right', 
								title: '<b>More Departments</b>',
								content: function () {
									return `
										<div class='kd_department_modal'>
											${extraDepartments.map(dep => `<p class='btn department-btn  ${response.current_department == dep.id ? "active_filter_btn" : ""}' data-id='${dep.id}' id='${dep.id}'>${dep.name}</p>`).join('')}
										</div>
									`;
								}
							}
						);
					}
				}

				if(response.allTags && response.allTags.length > 0 && append !=true){
					var extraTags=[];
					html += `<div class="section kd_details_cards"><h2 style="margin-top:20px">Tags</h2><div class='row'><div class="col-md-12">`;
					response.allTags.forEach((tag,index)=>{
						if(index <= 20){
							html += `<a class='btn tag-btn ${response.current_tag == tag.id ? "active_filter_btn" : ""}' data-id='${tag.id}' id='${tag.id}'>${tag.tags}</a>`;
						}else{
							extraTags.push(tag);
						}
					})
					if(extraTags && extraTags.length > 0){
						html += `
							<button class="btn mdl-tag" data-toggle="department-popover">
								+ ${response.allTags.length - 20} more
							</button>
						`;
					}

					html += `</div></div></div>`;
				}

				if (response.list && response.list.length > 0) {
					$('#loadMoreBtn').removeClass('hide');
					if (data.page == 1) {
						html += `<div class="section kd_document"><h2>Knowledge Document</h2><hr><div class="row">`;
					}
					let isCardContainerOpen = false;
					response.list.forEach((l, index) => {

						if (index === 0 && append !=true && data.page == 1) {
							html += `
							<div class="col-md-3">
								<div class="card h-100">
								<a href="${baseViewUrl}/${l.id}" target="_blank">
									<div class="image-wrapper" style="height: 180px; background-image: url('${baseUrl}/${l.card_img ?? '18.png'}'); background-position-y: center;">
										<div class="arrow_back">
											<svg class="hex-icon-color" width="40" height="40" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
											<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
											</svg>
										</div>
									</div>
									<div class="card-body clearfix">
										<div class="pull-left gray-tags gap-2">
											${l.dep_name ? `<button class="btn btn-outline-gray rounded-pill">${l.dep_name}</button>` : ''}
											<span><i class="fa fa-clock-o"></i> ${l.updated_at_format ?? ''}</span>
										</div>
									</div>
									<div class="card-footer">
										<h4 class="m-0" data-toggle="tooltip" data-original-title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
									</div>
								</a>
								</div>
							</div>`;
						} else if (index === 1 && append !=true && data.page == 1) {
							html += `
							<div class="col-md-6">
								<div class="card h-100">
									<a href="${baseViewUrl}/${l.id}" target="_blank">
										<div class="image-wrapper" style="height: 350px; background-image: url('${baseUrl}/${l.card_img ?? '18.png'}'); background-position-y: center;">
											<div class="arro_back">
												<svg class="hex-icon-color" width="55" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
												<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
												</svg>
											</div>
										</div>
										<div class="card-body clearfix">
											<div class="pull-left gray-tags gap-2">
												${l.dep_name ? `<button class="btn btn-outline-gray rounded-pill">${l.dep_name}</button>` : ''}
												<span><i class="fa fa-clock-o"></i> ${l.updated_at_format ?? ''}</span>
											</div>
										</div>
										<div class="card-footer">
											<h4 class="m-0" data-toggle="tooltip" data-original-title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
										</div>
									</a>
								</div>
							</div>`;
						} else if (index === 2 && append !=true && data.page == 1) {
							html += `
							<div class="col-md-3">
								<div class="card h-100">
									<a href="${baseViewUrl}/${l.id}" target="_blank">
										<div class="image-wrapper" style="height: 180px; background-image: url('${baseUrl}/${l.card_img ?? '18.png'}'); background-position-y: center;">
											<div class="arrow_back">
												<svg class="hex-icon-color" width="40" height="40" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
												<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
												</svg>
											</div>
										</div>
										<div class="card-body clearfix">
											<div class="pull-left gray-tags gap-2">
												${l.dep_name ? `<button class="btn btn-outline-gray rounded-pill">${l.dep_name}</button>` : ''}
												<span><i class="fa fa-clock-o"></i> ${l.updated_at_format ?? ''}</span>
											</div>
										</div>
										<div class="card-footer">
											<h4 class="m-0" data-toggle="tooltip" data-original-title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
										</div>
									</a>
								</div>
							</div>
							</div><div class="row">`; // Close first row and start new one
						} else {
							// Start card-container once after 3rd card
							if (!isCardContainerOpen) {
								html += `<div class="card-container row">`;
								isCardContainerOpen = true;
							}
				
							html += `
							<div class="col-md-3">
								<a href="${baseViewUrl}/${l.id}" target="_blank">
									<div class="card" style="background-image: url('${baseUrl}/${l.card_img ?? '18.png'}'); background-position-y: center; background-position: center;">
										<div class="big-kd">KD</div>
										<div class="card-content">
											<div class="title" data-toggle="tooltip" data-original-title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></div>
											<div class="tags">
												${l.dep_name ? `<button class="btn btn-outline-gray rounded-pill tag">${l.dep_name}</button>` : ''}
											</div>
										</div>
										<div class="curve_one"></div>
										<div class="arro_back">
												<svg class="hex-icon-color" width="39" height="39" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M61.6906 28C63.1197 30.4752 63.1197 33.5248 61.6906 36L50.3094 55.7128C48.8803 58.188 46.2393 59.7128 43.3812 59.7128L20.6188 59.7128C17.7607 59.7128 15.1197 58.188 13.6906 55.7128L2.3094 36C0.880335 33.5248 0.880339 30.4752 2.3094 28L13.6906 8.28718C15.1197 5.81198 17.7607 4.28719 20.6188 4.28719L43.3812 4.28719C46.2393 4.28719 48.8803 5.81198 50.3094 8.28719L61.6906 28Z" fill="#DA1A1A"/>
												<path d="M45.8962 33.1566L45.896 33.1569L35.4749 43.5779C35.1674 43.8855 34.7503 44.0582 34.3154 44.0582C33.8805 44.0582 33.4633 43.8855 33.1558 43.5779C32.8483 43.2704 32.6755 42.8533 32.6755 42.4184C32.6755 41.9835 32.8483 41.5663 33.1558 41.2588L40.6112 33.8063L40.782 33.6356H40.5405H19.2642C18.8301 33.6356 18.4137 33.4632 18.1068 33.1562C17.7998 32.8492 17.6273 32.4329 17.6273 31.9988C17.6273 31.5646 17.7998 31.1483 18.1068 30.8413C18.4137 30.5344 18.8301 30.3619 19.2642 30.3619H40.5405H40.7818L40.6112 30.1912L33.1587 22.7344L33.1587 22.7344C32.8512 22.4268 32.6784 22.0097 32.6784 21.5748C32.6784 21.1399 32.8512 20.7228 33.1587 20.4153C33.4662 20.1077 33.8833 19.935 34.3183 19.935C34.7532 19.935 35.1703 20.1077 35.4778 20.4153L45.8989 30.8363L45.8989 30.8364C46.0516 30.9887 46.1726 31.1696 46.2551 31.3688C46.3376 31.568 46.38 31.7815 46.3797 31.9971C46.3794 32.2127 46.3366 32.4261 46.2536 32.6251C46.1707 32.8241 46.0492 33.0047 45.8962 33.1566Z" fill="white" stroke="#DA1A1A" stroke-width="0.2"/>
												</svg>
										</div>
									</div>
								</a>
							</div>`;
						}
					});
				
					if (isCardContainerOpen) {
						html += `</div>`; // Close card-container
					}
					html += `</div></div>`; // Close outer .row and section
				}
				if(data.total != 0){
                	t.home.append(html);
				}else{
					html +=`<div class="d-flex justify-content-center align-items-center" style="height: 100vh;">
						<h1 class="text-center">No Record Found</h1>
					</div>`;
					t.home.append(html);
				}
				if(extraTags && extraTags.length > 0){
					t.home.find(".mdl-tag").popover(
						{
							html: true, 
							trigger: 'focus',
							placement: 'bottom', 
							title: '<b>More Tag</b>',
							content: function () {
								return `
									<div class='kd_department_modal'>
										${extraTags.map(tag => `<p class='btn tag-btn ${response.current_tag == tag.id ? "active_filter_btn" : ""}' data-id='${tag.id}' id='${tag.id}'>${tag.tags}</p>`).join('')}
									</div>
								`;
							}
						}
					);
				}
            }
        });
    };

	t.deleteDocument = function(e) {
        e.preventDefault();
        var Id = $(this).attr("data-href");
        t.httpPostPath = t.config.url.delete + "/" + Id;
        sweetAlertConfirmation({
            message: config.translations.are_you_want_Delete,
           	onConfirm: function() {
            	var http = $.get(t.httpPostPath);
            	http.done(function(data) {
            	   if (typeof data == "object") {
            	      if (data.status == "success") {
							sweetAlert('center', 'error', data);
							t.pagebtns.pagination("drawPage", 1);
						    t.load();
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

    t.updateDocument = function(e) {
		e.preventDefault();
		let s = t.frmUpdate.el.content.val();

        if (s == '') {
            $('#knowledge_document_update_form').find('#shows_error').html('This field is required.');
            $('#knowledge_document_update_form').find('#shows_error').css({'color':'#c53030','font-size':'13px','font-family':'Arial, sans-serif'});
            return false;
        }
		t.httpPostPath = t.config.url.update;
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
					sweetAlert('center', 'success ', data);
					setTimeout(function() {
						window.location = t.config.url.create_document;
					}, 800);
				} else {
					sweetAlert('center', 'error', data);
				}
			}
		});
		http.fail(function() {
			var data = {
				'msg': t.config.translations.something_went_wrong,
			}
			sweetAlert('center', 'error', data);
		});
	};

	document.getElementById("loadMoreBtn").addEventListener("click", function () {
		if (t.perPage < t.totalPages) {
			t.perPage++;
			t.cache_pagefilter_values(this);
			t.load(true);
		}
	
		if (t.perPage >= t.totalPages) {
			this.style.display = "none";
		}
	});
    t.load();
    t.searchbox.on("keypress", "input", t.search);
    t.searchbox.on("click", ".btn-searchbox", t.search);
    t.filters.btnfilterclr.on("click", t.btnClrFilter);
    t.searchbox.on("click", ".btn-reload", t.load);

    // t.filters.btnFilterApply.on("click", t.search);
	t.filters.btnFilterApply.on("click", function(e) {
		e.preventDefault();
		t.cache_filter_values();
		t.load();

		// Close the modal
		var modalEl = document.getElementById('advanceFilterModal');
		if (modalEl) {
			var modal = bootstrap.Modal.getInstance(modalEl);
			if (modal) modal.hide();
		}
	});
	t.page.on("click", ".dtArtDel", $.proxy(t.deleteDocument));
    t.page.on("click", ".dtArtEdit", $.proxy(t.updateDocument));
	t.page.on("click", ".js-act-staring", $.proxy(t.staring));
	$(document).on("click",".department-btn",function(e){
		t.search(e,this);
	});
	$(document).on("click",".tag-btn",function(e){
		t.search(e,this);
	});
};

var MyApp = function(config) {
	var t = this;
	t.myApp = this;
	t.page = $("#page_boxed");
	config.myApp = this;
	if (t.page.find("#article").length) {
		t.listing = new Document(config);
	}
	t.document = new KnowledgeDocument(config);
	// t.category = new Category(config);
	// t.parentcategory = new ParentCategory(config);
	t.publicarticle = new PublicArticle(config);

	t.page.on("click", ".js-act-add-document", $.proxy(t.document.addDocument));
	t.page.on("click", ".js-act-create", $.proxy(t.document.createDocument));
	t.page.on("click", ".dtActDel", $.proxy(t.document.deleteDocument));
	t.page.on("click", ".dtActEdit", $.proxy(t.document.editDocument));
	t.page.on("click", ".js-act-update", $.proxy(t.document.updateDocument));
	// t.page.on("click", ".js-act-add-category", $.proxy(t.category.addCategory));
	// t.page.on("click", ".js-act-create-category", $.proxy(t.category.createCategory));
	// t.page.on("click", ".js-act-delete-category", $.proxy(t.category.deleteCategory));
	// t.page.on("click", ".js-act-edit-category", $.proxy(t.category.editCategory));
	// t.page.on("click", ".js-act-update-category", $.proxy(t.category.updateCategory));
	// t.page.on("click", ".js-act-add-parent-category", $.proxy(t.parentcategory.addParentCategory));
	// t.page.on("click", ".js-act-create-parent-category", $.proxy(t.parentcategory.createParentCategory));
	// t.page.on("click", ".js-act-delete-parent-category", $.proxy(t.parentcategory.deleteParentCategory));
	// t.page.on("click", ".js-act-edit-parent-category", $.proxy(t.parentcategory.editParentCategory));
	// t.page.on("click", ".js-act-update-parent-category", $.proxy(t.parentcategory.updateParentCategory));

};

function openModal(tagData) {
    let html = '<div class="tags" style="display: flex; z-index:5;">';
    $.each(tagData, function(key, value) {
        html += '<span class="badge">' + value.tags + '</span>';
    });
    html += '</div>';
    $('#myModal .modal-body').html(html);
    $('#myModal').modal('show');
}
