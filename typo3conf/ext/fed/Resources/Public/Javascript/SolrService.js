/**
 * SOLR service for Javascript
 *
 * DEPENDS ON jQuery for AJAX!
 *
 * Fairly light-weight SOLR API implementation designed for use with FED/TYPO3.
 *
 * Works by passing the request through a Proxy which rebuilds the query (i.e.
 * adds access protection parameters from TYPO3 and a few cleanups) and returns
 * SOLR's response to that new Query.
 *
 * This allows SOLR to be used easily from a Javascript/JSON context instead
 * of the provided PHP API implementation which is overkill and makes me sad.
 * SOLR is BLAZING fast and adding a complete TYPO3 request/response just to
 * facet/re-search etc. is complete overkill.
 *
 * This should make things go a little faster. Go speed racer, go!
 */

if (typeof FED == 'undefined') {
	var FED = {};
}

FED.SOLR = {

	config: {},
	minQueryStringLength: 3,
	page: 1,
	fields: [
		'title^40',
		'content^10',
		'keywords^2.0',
		'tagsH1^5.0',
		'tagsH2H3^3.0',
		'tagsH4H5H6^2.0',
		'tagsInline'
	],
	proxy: '?type=1324054607',
	query: {},
	queryString: '',
	results: [],
	facets: [],

	setConfig: function(config) {
		this.config = config;
	},

	onResult: function() {

	},

	search: function (queryString, facets, onResult) {
		if (typeof queryString == 'undefined') {
			return this;
		} else if (queryString.length < this.minQueryStringLength) {
			return this;
		};
		this.queryString = queryString;
		if (typeof facets == 'array') {
			this.facets = facets;
		};
		if (typeof onResult == 'function') {
			this.async = true;
			this.onResult = onResult;
			this.query = this.executeQuery(true);
		} else {
			this.query = this.executeQuery(false);
		};
		return this;
	},

	executeQuery: function() {
		var request;
		var options = {
			async: false,
			url: this.proxy,
			data: {
				"wt": "json",
				"json.nl": "map",
				"facet": "on",
				"q": this.queryString,
				"fl": "*,score",
				"start": parseInt(this.config.search.results.resultsPerPage*(this.page-1)),
				"fields": this.fields,
				"rows": this.config.search.results.resultsPerPage,
				"facets": this.getFacetQueryFields(),
				"fq": this.getFacetQueryString()
			}
		};
		this.results = jQuery.parseJSON(jQuery.ajax(options).responseText);
		this.onResult();
	},

	getFacetQueryString: function() {
		var queryString = '';
		for (var i=0; i<this.facets.length; i++) {
			var facet = this.facets[i];
			queryString += facet.facetName + ':"' + facet.facetValue + '" ';
		};
		return queryString;
	},

	getFacetQueryFields: function() {
		var facets = [];
		for (var fieldName in this.config.search.faceting.facets) {
			facets.push(fieldName);
		};
		return facets;
	},

	getCurrentPage: function() {
		return this.page;
	},

	getFacetLabel: function(facet) {
		for (var i in this.config.search.faceting.facets) {
			if (this.config.search.faceting.facets[i].field == facet) {
				return this.config.search.faceting.facets[i].label;
			};
		};
	},

	setFacets: function (facets) {
		this.facets = facets;
	},

	getFacets: function() {
		return this.facets;
	},


	addFacet: function(facetName, facetValue) {
		if (this.hasFacet(facetName, facetValue)) {
			return;
		};
		this.facets.push({'facetName':facetName, 'facetValue': facetValue});
		this.executeQuery();
	},

	removeFacet: function(facetName, facetValue) {
		if (!this.hasFacet(facetName, facetValue)) {
			return;
		};
		var facets = [], i;
		for (var i=0; i<this.facets.length; i++) {
			var facet = this.facets[i];
			if (facetName != facet.facetName && facetValue != facet.facetValue) {
				facets.push(facet);
			};
		};
		this.facets = facets;
		this.executeQuery();
	},

	hasFacet: function(facetName, facetValue) {
		for (var i=0; i<this.facets.length; i++) {
			var facet = this.facets[i];
			if (facetName == facet.facetName && facetValue == facet.facetValue) {
				return true;
			};
		};
		return false;
	},

	getNumResults: function() {
		if (this.results.response) {
			return this.results.response.numFound;
		} else {
			return -1;
		}
	},

	getNumPages: function() {
		if (this.results.response) {
			return Math.ceil(this.results.response.numFound / this.config.search.results.resultsPerPage);
		} else {
			return -1
		};
	},

	getResults: function() {
		return this.results.response.docs;
	},

	getResultsPage: function(pageNum) {
		this.page = pageNum;
		this.executeQuery();
	}


};