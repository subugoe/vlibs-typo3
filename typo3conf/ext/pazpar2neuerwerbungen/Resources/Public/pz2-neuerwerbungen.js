
function pz2neuerwerbungenDOMReady () {
	restoreCookieState ();
}


function restoreCookieState () {
	var cookies = document.cookie.split('; ');
	for (var cookieIndex in cookies) {
		var cookie = cookies[cookieIndex];
		var equalsLocation = cookie.search('=');
		if (equalsLocation != -1) {
			var cookieName = cookie.substring(0, equalsLocation);
			if (cookieName == 'previousQuery') {
				var cookieValue = cookie.substring(equalsLocation +1);
				var fieldNames = cookieValue.split(':');
				for (var fieldNameIndex in fieldNames) {
					var fieldName = fieldNames[fieldNameIndex];
					$(':checkbox[value="' + fieldName + '"]').attr({'checked': true});
				}
				break;
			}
		}
		var cookieParts = cookies[cookieIndex].split
	}
}


function saveStateAsCookie (form) {
	var selectedValues = [];
	$(':checked', form).each( function (index) {
			selectedValues.push(this.value);
		}
	)

	document.cookie = 'previousQuery=' +  selectedValues.join(':');
}


function runSearchForForm (form) {
	resetPage();
	setSortCriteriaFromString('author-a--title-a--date-d');
	var query = buildSearchQuery(form);
	
	my_paz.search(query, 2000, null, null);

	$('.pz2-rsslink').attr('href', RSSURL(form));

	saveStateAsCookie(form);
}



function checkboxChanged (checkbox) {
	toggleParentCheckboxOf(checkbox);
	runSearchForForm(checkbox.form);
}

function groupCheckboxChanged (checkbox) {
	toggleChildCheckboxesOf(checkbox);
	runSearchForForm(checkbox.form);
}

function toggleParentCheckboxOf (checkbox) {
	var fieldset = $(checkbox).parents('fieldset')[0];
	parentCheckbox = $('legend :checkbox', fieldset);
	
	parentCheckbox.attr({'checked': ($('li :checkbox', fieldset).length == $('li :checked', fieldset).length)});
}

function toggleChildCheckboxesOf (checkbox) {
	var fieldset = $(checkbox).parents('fieldset')[0];
	$(':checkbox', fieldset).attr({'checked': checkbox.checked});
}


function buildSearchQuery (form) {
	return buildSearchQueryWithEqualsAndWildcard(form, '=', '');	
}


function selectedGOKsInFormWithWildcard (form, wildcard) {
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

	return GOKs;
}


function buildSearchQueryWithEqualsAndWildcard (form, equals, wildcard) {
	var GOKs = selectedGOKsInFormWithWildcard(form, wildcard);
	var LKLQueryString = oredSearchQueries(GOKs, 'lkl', equals);
	
	var dates = [];
	addSearchTermsToList( $('.pz2-months :selected', form)[0].value, dates, wildcard);
	var DTMQueryString = oredSearchQueries(dates, 'dtm', equals);
	var statuses = [];
	addSearchTermsToList('a,r', statuses, wildcard);
	var SLKQueryString = oredSearchQueries(statuses, 'slk', equals);

	return LKLQueryString + ' and ' + DTMQueryString + ' not ' + SLKQueryString;
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
	searchQuery = searchQuery.replace(/ /g, '+');
	searchQuery = encodeURI(searchQuery);

	var RSSURL = "http://k1www.gbv.de/rssopc/rss_feeds.php?HOST=opac.sub.uni-goettingen.de&INTPORT=80&EXTPORT=80&DB=1&SEARCH=00yS!t" + searchQuery + "!aY!cN.oY.vD.wD&EDOC=588692600"

	return RSSURL;
}


