(function(jQuery){
	jQuery.fn.solr = function(options, url) {
		var defaults = {
			'resultsPerPage': 10,
			'crop': 200,
			'onionSkinNumbers': 5,
			'beforeResultNumber': '#',
			'auto': 1,
			'facetTitles': {},
			'facet': 'on',
			'scorebarHeight': 16,
			'scorebarWidth': 50
		};
		var options = jQuery.extend(defaults, options);
		options.resultsPerPage = parseInt(options.resultsPerPage);
		options.onionSkinNumbers = parseInt(options.onionSkinNumbers);
		return this.each(function() {
			var timer = null;
			var parent = this;
			var element = jQuery(this);
			var currentPage = 1;
			var pages = 1;
			var appliedFacets = [];
			var docs;
			var elements = {
				"facet": element.find('.fed-solr-facet-template').hide()
					.removeClass('fed-solr-facet-template').addClass('fed-solr-facet'),
				"facetGroup": element.find('.fed-solr-facet-group-template').hide()
					.removeClass('fed-solr-facet-group-template').addClass('fed-solr-facet-group'),
				"facetGroupTitle": element.find('.fed-solr-facet-group-title-template').hide()
					.removeClass('fed-solr-facet-group-title-template').addClass('fed-solr-facet-group-title'),
				"facetList": element.find('.fed-solr-facet-group-list-template').hide()
					.removeClass('fed-solr-facet-group-list-template').addClass('fed-solr-facet-group-list'),
				"facets": element.find('.fed-solr-facets').hide(),
				"results": element.find('.fed-solr-results').hide(),
				"resultsperpage": element.find('.fed-solr-resultsperpage').hide().each(function() { jQuery(this).change(function() { updateResultsPerPage(jQuery(this)); }); }),
				"result": element.find('.fed-solr-result-template').hide()
					.addClass('fed-solr-result').removeClass('fed-solr-result-template'),
				"noresults" : element.find('.fed-solr-noresults').hide(),
				"errors" : element.find('.fed-solr-errors').hide(),
				"statistics": {
					"first" : element.find('.fed-solr-statistics-first').hide(),
					"last" : element.find('.fed-solr-statistics-last').hide(),
					"total" : element.find('.fed-solr-statistics-total').hide(),
					"root" : element.find('.fed-solr-statistics').hide()
				},
				"paginate": {
					"numbers": element.find('.fed-solr-paginate-number-template').parent().hide(),
					"number": element.find('.fed-solr-paginate-number-template:first')
						.removeClass('fed-solr-paginate-number-template')
						.addClass('fed-solr-paginate-number').hide(),
					"total": element.find('.fed-solr-paginate-total').hide(),
					"current": element.find('.fed-solr-paginate-current').hide(),
					"first": element.find('.fed-solr-paginate-first')
						.hide().click(function() {gotoPage(1);}),
					"previous": element.find('.fed-solr-paginate-previous')
						.hide().click(function() {gotoPage(-1);}),
					"next": element.find('.fed-solr-paginate-next')
						.hide().click(function() {gotoPage(-2);}),
					"last": element.find('.fed-solr-paginate-last')
						.hide().click(function() {gotoPage(pages);}),
					"root": element.find('.fed-solr-paginate').hide()
				}
			};
			var searchField = element.find('.fed-solr-search-field');
			var updateResultsPerPage = function(selector) {
				var newResultsPerPage = selector.val();
				if (newResultsPerPage != options.resultsPerPage) {
					options.resultsPerPage = newResultsPerPage;
					gotoPage(1);
					elements.resultsperpage.val(newResultsPerPage);
				};
			};
			var gotoPage = function(page) {
				if (page < 0) {
					if (page == -1 && currentPage-1 > 0) {
						page = currentPage - 1
					} else if (page == -2) {
						page = currentPage + 1;
					};
				};
				if (page > pages) {
					page = pages;
				}if (page < 1) {
					page = 1;
				};
				currentPage = parseInt(page);
				performSearch();
			};
			var refreshInterface = function(reply) {
				modifyUrl();
				var results = reply.response;
				docs = results.docs;
				if (results.length == 0) {
					elements.noresults.show();
					elements.paginate.root.hide();
					elements.statistics.root.hide();
					elements.results.hide();
					return;
				};

				var page = (results.start / options.resultsPerPage) + 1;
				pages = Math.ceil(results.numFound / options.resultsPerPage);

				elements.statistics.root.show();
				elements.statistics.first.html(results.start + 1).show();
				elements.statistics.last.html((results.start + options.resultsPerPage < results.numFound) ? parseInt(results.start + parseInt(options.resultsPerPage)) : results.numFound).show();
				elements.statistics.total.html(results.numFound).show();

				elements.paginate.root.show();
				elements.paginate.first.show();
				elements.paginate.last.show();
				elements.paginate.previous.show();
				elements.paginate.next.show();
				elements.paginate.current.html(page).show();
				elements.paginate.total.html(pages).show();

				elements.results.html('');
				for (var i = 0; i < results.docs.length; i++) {
					var result = results.docs[i];
					var row = elements.result.clone().show();
					var scorebar = row.find('.fed-solr-scorebar');
					for (var name in result) {
						if (typeof name == 'string') {
							var value = result[name];
							if (name == 'url') {
								row.find('.' + name).attr('href', value);
							} else {
								row.find('.' + name).html(value);
							};
						};
					};
					var ratio = result.score / results.maxScore;
					var width = ratio * options.scorebarWidth;
					if (scorebar.length > 0) {
						scorebar.width(options.scorebarWidth);
						var fill = jQuery('<div></div>').width(parseInt(width) + 2).height(options.scorebarHeight).addClass('fed-solr-scorebar-fill');
						if (scorebar.hasClass('percent')) {
							var percent = jQuery('<div></div>').html(parseInt(ratio * 100) + '%').addClass('percent').width(options.scorebarWidth);
							fill.append(percent);
						};
						scorebar.append(fill);
					};
					row.find('.relevancy').html(parseInt(ratio * 100) + '%');
					row.find('.result-number').html(options.beforeResultNumber + (i + 1 + ((currentPage - 1) * options.resultsPerPage)));
					magicTruncate(row.find('.content'));
					elements.results.append(row).show();
				};

				elements.resultsperpage.show().val(options.resultsPerPage);
				if (pages == 1) {
					elements.paginate.root.hide();
				} else {
					elements.paginate.numbers.each(function() {
						jQuery(this).html('');
						for (var i = 1; i<=pages; i++) {
							var onion = options.onionSkinNumbers;
							if (onion < 1 || pages < (onion * 2) + 1 ||
									(i > currentPage - onion && i < currentPage + onion)
								) {
								var number = elements.paginate.number.clone();
								number.click(function() {gotoPage(jQuery(this).html());}).html(i.toString()).show();
								if (i == currentPage) {
									number.addClass('active');
								} else {
									number.removeClass('active');
								};
								jQuery(this).append(number).show();
							};
						};
					});
				};

				if (options.facet == 'on') {
					elements.facets.html('').show();
					for (var facet in reply.facet_counts.facet_fields) {
						var facets = reply.facet_counts.facet_fields[facet];
						var group = elements.facetGroup.clone().show().html('');
						var title = elements.facetGroupTitle.clone().show().html(options.facetTitles[facet]);
						var list = elements.facetList.clone().show().html('');
						for (var facetName in facets) {
							var count = parseInt(facets[facetName]);
							if (count > 0) {
								var facetElement = elements.facet.clone().show();
								facetElement.find('.fed-solr-facet-title').html(facetName).click(toggleFacet).attr('data-rel', facet + ':' + facetName);
								facetElement.find('.fed-solr-facet-count').html(count);
								if (hasFacet(facet, facetName)) {
									facetElement.addClass('active');
								};
								list.append(facetElement);
							};
						};
						group.append(title);
						group.append(list);
						elements.facets.append(group);
					};
				};

			};
			var hasFacet = function(facetName, facetValue) {
				for (var i=0; i<appliedFacets.length; i++) {
					var facet = appliedFacets[i];
					if (facet.facetName == facetName && facet.facetValue == facetValue) {
						return true;
					};
				};
				return false;
			};
			var toggleFacet = function() {
				var link = jQuery(this);
				var parts = link.attr('data-rel').split(':');
				if (!hasFacet(parts[0], parts[1])) {
					appliedFacets.push({'facetName':parts[0], 'facetValue': parts[1]});
				} else {
					var facets = [], i;
					for (var i=0; i<appliedFacets.length; i++) {
						var facet = appliedFacets[i];
						if (parts[0] != facet.facetName && parts[1] != facet.facetValue) {
							facets.push(facet);
						};
					};
					appliedFacets = facets;
				};
				performSearch();
			};
			var modifyUrl = function() {
				var params = [
					searchField.val(),
					currentPage.toString(),
					options.resultsPerPage
				];
				for (var key in appliedFacets) {
					var facet = appliedFacets[key];
					params.push(facet.facetName + '+' + decodeURI(facet.facetValue));
				};
				top.location.hash = params.join('/');
			};
			var parseUrl = function() {
				var hash = top.location.hash.substring(1).split('/');
				if (hash.length > 0) {
					searchField.val(hash.shift());
					currentPage = parseInt(hash.shift());
					var resultsPerPage = parseInt(hash.shift());
					options.resultsPerPage = resultsPerPage > 0 ? resultsPerPage : options.resultsPerPage
					for (var i in hash) {
						var parts = hash[i].split('+');
						var facet = {
							"facetName": parts[0],
							"facetValue": encodeURI(parts[1])
						};
						appliedFacets.push(facet);
					};
				};
			};
			var performSearch = function() {
				var arguments = buildSearchParameters();
				jQuery.ajax({
					"url": url,
					"type": 'get',
					"data": {
						"tx_fed_solr": {
							"query": arguments
						}
					},
					complete: function(request) {
						var response = jQuery.parseJSON(request.responseText);
						if (response.code) {
							elements.errors.show().html(response.message);
						} else {
							elements.errors.hide();
						};
						refreshInterface(response);
						searchField.removeClass('loading');
						if (response.response.docs.length == 0) {
							if (currentPage > 1) {
								gotoPage(1);
							} else if (appliedFacets.length > 0) {
								appliedFacets = [];
								performSearch();
							};
						};
					}
				})
			};
			var buildSearchParameters = function() {
				return {
					"q": searchField.val(),
					"wt": 'json',
					"json.nl": 'map',
					"facet": options.facet,
					"start": (currentPage - 1) * options.resultsPerPage,
					"fields": options.fields,
					"rows": options.resultsPerPage,
					"facets": appliedFacets
				};
			};
			var magicTruncate = function(container) {
				var full = container.html();
				container.html(container.html().trim().substring(0, options.crop)
					.split(" ").slice(0, -1).join(" "));
			};

			if (options.auto) {
				searchField.keypress(function() {
					clearInterval(timer);
					jQuery(this).addClass('loading');
					timer = setTimeout(function() {
						gotoPage(1);
					}, 250);
				});
			};

			parseUrl();
			if (searchField.val() != options.placeholder && searchField.val() != '') {
				performSearch();
			} else if (!options.auto) {
				element.find('.fed-solr-search').click(function() {
					gotoPage(1);
				});
				element.find('.fed-solr-reset').click(function() {
					searchField.val('');
					gotoPage(1);
				});
			};

		});
	};
})(jQuery);