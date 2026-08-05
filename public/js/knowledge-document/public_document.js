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
		return `<span class="kd-arrow" data-bs-toggle="tooltip" data-bs-placement="top" title="${t.config.translations.view}"><svg><use href="#kd-arrow-right"></use></svg></span>`;
	};

	t.pill = function(text, light) {
		return text ? '<span class="kd-pill' + (light ? " light" : "") + '">' + t.esc(text) + "</span>" : "";
	};

	t.stackCard = function(article) {
		return '<article class="kd-stack">' +
			'<a href="' + t.articleUrl(article) + '" target="_blank">' +
			'<div class="' + t.imageClass(article, "kd-card-image") + '"' + t.imageStyle(article) + ">" +
			(!article.card_img ? '<span class="kd-watermark">KD</span>' : "") +
			'<div class="kd-overlay"><h4>' + t.esc(article.title || "") + "</h4>" + t.pill(article.dep_name, true) + "</div>" +
			t.arrow() +
			"</div></a></article>";
	};

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
				'<div class="kd-tag-popover" role="menu">' + extraHtml + "</div>" +
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
			'<button type="button" class="kd-load-more" id="kdLoadMoreBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="${t.config.translations.load_more}"><svg><use href="#kd-arrow-down"></use></svg></button><div class="kd-red-rule"></div>';

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
		var popover = wrapper.find(".kd-department-popover");

		// Close others
		$(".kd-more-departments").not(wrapper).removeClass("is-open").find(".kd-department-popover").hide();

		wrapper.toggleClass("is-open");
		if (wrapper.hasClass("is-open")) {
			popover.show();
			$(this).attr("aria-expanded", "true");
		} else {
			popover.hide();
			$(this).attr("aria-expanded", "false");
		}
	});

	$(document).on("click.kdDynamic", function(e) {
		if ($(e.target).closest(".kd-more-departments").length) return;
		$(".kd-more-departments").removeClass("is-open").find(".kd-department-popover").hide();
		$(".kd-more-departments-toggle").attr("aria-expanded", "false");
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

	t.initTooltips = function () {
		$('[data-bs-toggle="tooltip"]').tooltip();
	}

    t.load = function(append = false) {
		t.api_loader.removeClass('hide');
		$('#loadMoreBtn').addClass('hide');
		if(append !=true) {
			t.home.empty();
		}
		t.initTooltips();
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
									<h4 style="margin-top: 0px;"><strong data-bs-toggle="tooltip" style="font-size: 24px;" data-bs-placement="top" title="${pa.title ?? ''}">${pa.title && pa.title.length > 30 ? pa.title.substr(0, 30) + "..." : pa.title}</strong></h4>
									<p class="text-muted" data-bs-toggle="tooltip" data-bs-placement="top" title="${pa.content ?? ''}">${pa.content && pa.content.length > 50 ? pa.content.substr(0, 50) + "..." : pa.content}</p>
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
											<h4 data-bs-toggle="tooltip" data-bs-placement="top" title="${mvq.title ?? ''}" style="margin-top: 0px;"><strong>${mvq.title && mvq.title.length > 30 ? mvq.title.substr(0, 30) + "..." : mvq.title}</strong></h4>
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
													<div class="title" data-bs-toggle="tooltip" data-bs-placement="top" title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></div>
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
															<h4 data-bs-toggle="tooltip" data-bs-placement="top" title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></h4>
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
													<h4 data-bs-toggle="tooltip" data-bs-placement="top" title="${la.title ?? ''}"><strong>${la.title && la.title.length > 30 ? la.title.substr(0, 30) + "..." : la.title}</strong></h4>
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
										<h4 class="m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
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
											<h4 class="m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
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
											<h4 class="m-0" data-bs-toggle="tooltip" data-bs-placement="top" title="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></h4>
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
											<div class="title" data-bs-toggle="tooltip" data-bs-placement="top" title=="${l.title ?? ''}"><strong>${l.title && l.title.length > 30 ? l.title.substr(0, 30) + "..." : l.title}</strong></div>
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
	t.publicarticle = new PublicArticle(config);
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
