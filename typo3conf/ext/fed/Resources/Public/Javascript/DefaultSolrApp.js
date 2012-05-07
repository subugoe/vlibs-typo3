var timer;
var queryFieldSelector = '.solr-search input[name="q"]';
var limitFieldSelector = '.solr-limit-field';
var placeholder = 'Search for...';

jQuery(document).ready(function() {

	jQuery('.solr-limit').hide();

	FED.SOLR.onResult = function() {
		jQuery('.solr-limit').show();
		renderCount();
		renderResults();
		renderPaging();
		renderFacets();
	};

	jQuery(limitFieldSelector).change(function() {
		var perPage = jQuery(this).val();
		FED.SOLR.config.search.results.resultsPerPage = perPage;
		FED.SOLR.executeQuery();
	});

	jQuery(queryFieldSelector).focus(function() {
		if (jQuery(this).val() == placeholder) {
			jQuery(this).val('');
		};
	});
	jQuery(queryFieldSelector).blur(function() {
		if (jQuery(this).val() == '') {
			jQuery(this).val(placeholder);
		};
	});
	jQuery(queryFieldSelector).keyup(function() {
		clearTimeout(timer);
		timer = setTimeout(function() {
			page = 1;
			FED.SOLR.search(jQuery(queryFieldSelector).val());
		}, 150);
	});

	if (jQuery(queryFieldSelector).val().length > FED.SOLR.minQueryStringLength) {
		FED.SOLR.search(jQuery(queryFieldSelector).val());
	} else {
		jQuery(queryFieldSelector).val(placeholder);
	};

});

function renderResults() {
	var results = FED.SOLR.getResults();
	var i;
	var html = '';
	for (i=0; i<results.length; i++) {
		var result = results[i];
		html += getResultHtml(result, i+((FED.SOLR.getCurrentPage()-1)*FED.SOLR.config.search.results.resultsPerPage)+1);
	};
	jQuery(".solr-results table tbody").html(html);
};

function renderCount() {
	var html = "Found <strong>" + FED.SOLR.getNumResults() + "</strong> results";
	jQuery(".solr-stats").html(html);
};

function renderPaging() {
	var selector = jQuery(".solr-paginate");
	if (FED.SOLR.getNumPages() < 2) {
		selector.hide().animate();
		return;
	};
	selector.show().animate();
	var html = "Page: ";
	var cssClass;
	for (var i=1; i<=FED.SOLR.getNumPages(); i++) {
		if (i == FED.SOLR.getCurrentPage()) {
			cssClass = "active";
		}
		else {
			cssClass = "";
		}
		html += '<a href="javascript:;" onclick="javascript:FED.SOLR.getResultsPage(' + i.toString() + ');" class="' + cssClass + '">' + i.toString() + '</a>';
	};
	selector.html(html);
};

function renderFacets() {
	var html = "<ul>";
	for (var facetField in FED.SOLR.results.facet_counts.facet_fields) {
		var facet = FED.SOLR.results.facet_counts.facet_fields[facetField];
		html += '<li><h3>' + FED.SOLR.getFacetLabel(facetField) + "</h3></li>";
		for (var facetValue in facet) {
			var funcName, className;
			var facetCount = facet[facetValue];
			if (facetCount < 1) {
				continue;
			};
			if (FED.SOLR.hasFacet(facetField, facetValue)) {
				funcName = 'removeFacet';
				className = 'active';
			} else {
				funcName = 'addFacet';
				className = 'inactive';
			};
			html += '<li class="' + className + '"><a href="javascript:FED.SOLR.' + funcName + '(\'' + facetField + '\', \'' + facetValue + '\');">' + facetValue + '</a> (' + facetCount + ')</li>';
		};
	};
	html += "</ul>";
	jQuery(".solr-faceting").html(html);
};

function linkWrap(result, html) {
	var link = '<a href="' + result.url;
	link += '">';
	link += html;
	link += '</a>';
	return link;
};

function getResultHtml(result, index) {
	var html = '<tr>';
	html += '<td class="title">' + index + '. <strong>' + linkWrap(result, result.title) + '</strong> <span class="score">Score: ' + result.score + '</span></td>';
	html += '<td class="facets">' + result.documentationCategory + ' / ' + result.documentationType + '</td>';
	html += '</tr><tr>';
	html += '<td colspan="2" class="teaser">' + linkWrap(result, result.teaser) + '...</td>';
	html += '</tr>';
	return html;
};