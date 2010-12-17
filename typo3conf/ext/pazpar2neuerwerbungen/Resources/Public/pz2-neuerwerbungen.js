

function checkboxChanged (checkbox) {
	resetPage();
	setSortCriteriaFromString('author-a--title-a--date-d')
	var query = buildSearchQuery(checkbox.form);
	my_paz.search(query, 1000, null, null);
	$('.pz2-rsslink').attr('href', RSSURL(checkbox.form));
}


function buildSearchQuery (form) {
	return buildSearchQueryWithEqualsAndWildcard(form, '=', '');	
}

function buildSearchQueryWithEqualsAndWildcard (form, equals, wildcard) {
	var GOKs = [];
	
	$('fieldset', form).each( function (index) {
			if ( $('legend>label :checked', this)[0] 
					&& $('legend>label :checked', this)[0].value !== 'CHILDREN') {
				addSearchTermsToList($('legend>label :checked', this)[0].value, GOKs, wildcard);
			}
			else {
				$('ul :checked', this).each( function (index) {
						addSearchTermsToList(this.value, GOKs, wildcard);
					}
				);
			}
		}
	);
	
	var LKLQueryString = oredSearchQueries(GOKs, 'lkl', equals);
	
	var dates = [];
	addSearchTermsToList( $('.pz2-months :selected', form)[0].value, dates, wildcard);
	var DTMQueryString = oredSearchQueries(dates, 'dtm', equals);
	
	return LKLQueryString + ' and ' + DTMQueryString;
}


function oredSearchQueries (queryTerms, key, equals) {
	var query = '(' + key + equals + queryTerms.join(' or ' + key + equals) + ')';
	
	return query;
}


function addSearchTermsToList (termsString, list, wildcard) {
	var terms = termsString.split(',')
	for (var termIndex in terms) {
		var term = terms[termIndex];
		if (term && term !== '') {
			list.push(term + wildcard);
		}
	}
}

function RSSURL (form) {
	var searchQuery = buildSearchQueryWithEqualsAndWildcard(form, ' ', '*');
	var RSSURL = "http://k1www.gbv.de/rssopc/rss_feeds.php?HOST=opac.sub.uni-goettingen.de&INTPORT=80&EXTPORT=80&DB=1&SEARCH=00yS!t" + searchQuery + "!m!aY!cN.oY.vD.wD&EDOC=3073196597";
	
	return RSSURL.replace(/ /r, '+');
}
