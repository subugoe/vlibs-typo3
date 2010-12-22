/*
 * pz2-neuerwerbungen.js
 *
 * 2010 by Sven-S. Porst, SUB Göttingen
 * porst@sub.uni-goettingen.de
 *
 * JavaScript for interactive loading and display of new acquisitions by
 * the library.
 *
 * For use with the pazpar2neuerwerbungen Typo3 extension and the
 * script its dependencies require.
 *
 */



/*
 * pz2neuerwerbungenDOMReady
 *
 * To be called when the Document is ready (usually by jQuery).
 * Restores state from cookies.
 */
function pz2neuerwerbungenDOMReady () {
	restoreCookieState ();
}



/*
 * restoreCookieState
 *
 * Restore previously checked checkboxes from the stated stored in the
 *	'previousQuery' cookie.
 *
 * Cookie data is a string whose components are separated by colons (:).
 * Each item corresponds to the value of a checkbox. Select those checkboxes and
 * turn them on.
 *
 */
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



/*
 * saveFormStateAsCookie
 *
 * Get the checked checkboxes from the passed form and concatenate their values
 *	with colon (:) separators. Store the result in the 'previousQuery' cookie.
 *
 * input:	form - DOM form element in which to look for checked checkboxes
 */
function saveFormStateAsCookie (form) {
	var selectedValues = [];
	$(':checked', form).each( function (index) {
			selectedValues.push(this.value);
		}
	)

	document.cookie = 'previousQuery=' +  selectedValues.join(':');
}



/*
 * runSearchForForm
 *
 * Build search query from the selected checkboxes and use it to kick off
 *	pazpar2 and set the RSS subscription URL.
 *
 * input:	form - DOM form element in which to look for checked checkboxes
 */
function runSearchForForm (form) {
	resetPage();
	setSortCriteriaFromString('author-a--title-a--date-d');
	var query = buildSearchQueryWithEqualsAndWildcard(form, '=', '');
	
	my_paz.search(query, 2000, null, null);

	$('.pz2-rsslink').attr('href', RSSURL(form));

	saveFormStateAsCookie(form);
}



/*
 * checkboxChanged
 *
 * Callback for the lowest-level checkboxes' onclick handler.
 * Toggles parent checkbox if necessary, then starts the query.
 *
 * input:	checkbox - DOM element of the clicked checkbox
 */
function checkboxChanged (checkbox) {
	toggleParentCheckboxOf(checkbox);
	runSearchForForm(checkbox.form);
}



/*
 * groupCheckboxChanged
 *
 * Callbock for the top-level checkboxes' onlick handler.
 * Toggles the child checboxes, then starts the query.
 *
 * input:	checkbox - DOM element of the clicked checkbox
 */
function groupCheckboxChanged (checkbox) {
	toggleChildCheckboxesOf(checkbox);
	runSearchForForm(checkbox.form);
}



/*
 * toggleParentCheckboxOf
 *
 * Helper function to update the parent checkbox' state when one of its child
 * checkboxes was changed:
 *	* all child checkboxes on => parent checkbox on
 *	* any child checkbox off => parent checkbox off
 *
 * input:	checkbox - DOM element of the changed checkbox
 */
function toggleParentCheckboxOf (checkbox) {
	var fieldset = $(checkbox).parents('fieldset')[0];
	parentCheckbox = $('legend :checkbox', fieldset);
	
	parentCheckbox.attr({'checked': ($('li :checkbox', fieldset).length == $('li :checked', fieldset).length)});
}



/*
 * toggleChildCheckboxesOf
 *
 * Helper function to update the child checkboxes' state when their parent
 * checkbox was changed:
 *	* parent checkbox on => all child checkboxes on
 *	* parent checkbox off => all child checkboxes off
 *
 * input:	checkbox - DOM element of the changed checkbox
 */
function toggleChildCheckboxesOf (checkbox) {
	var fieldset = $(checkbox).parents('fieldset')[0];
	$(':checkbox', fieldset).attr({'checked': checkbox.checked});
}



/*
 * selectedGOKsInFormWithWildcard
 *
 * For a given form returns an array of all GOKs in the values of the checked
 *	checkboxes. Each checkbox' value can contain several GOKs, separated by a
 *	comma (,). A wildcard is appended to each GOKs if required.
 *
 * inputs:	form - DOM element of the form to get the data from
 *			wildcard - string to be appended to each extracted GOK
 * output:	array of strings, each of which is a GOK, potentially with a wildcard
 */
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



/*
 * buildSearchQueryWithEqualsAndWildcard
 *
 * Builds a query string using the selected GOKs and date in the passed form.
 * Equals assignment and wildcard can be configured to yield strings that can
 *	be used as Pica Opac queries or as CCL queries.
 * An additional not (SLK A OR SLK P) condition is added to the query to avoid
 *  getting results that are not avaialable.
 *
 * inputs:	form - DOM element of the form to get the data from
 *			equals - string used between the field name and the query term
 *				(typically ' ' in Pica or '=' in CCL)
 *			wildcard - string to be appended to each extracted GOK
 * output:	string containing the complete query
 */
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



/*
 * oredSearchQueries
 *
 * Helper function for preparing search queries.
 *
 * inputs:	queryTerms - array of strings, each of which will be a sub-query
 *			key - string with search key that each queryTerm should be found in
 *			equals - string used to separate the key and the query term
 * output:	string containing the query
 */
function oredSearchQueries (queryTerms, key, equals) {
	var query = '(' + key + equals + queryTerms.join(' or ' + key + equals) + ')';
	
	return query;
}



/*
 * addSearchTermsToList
 *
 * Helper function to add the components of a given string to a components
 *	array, potentially adding a wildcard to each in the process.
 *
 * inputs:	termsString - string containing a number of comma-separated
 *				components, each of which will be added to the list
 *			list - array that each component will be added to
 *			wildcard - string that is appended to each component before adding
 *				it to the list
 */
function addSearchTermsToList (termsString, list, wildcard) {
	var terms = termsString.split(',')
	for (var termIndex in terms) {
		var term = terms[termIndex];
		if (term && term !== '') {
			list.push(term + wildcard);
		}
	}
}



/*
 * RSSURL
 *
 * Creates the URL to the RSS feed for the query.
 * TODO: figure out the correct URL format.
 *
 * input:	form - DOM element of the form to get the data from
 * output:	string with the URL to the RSS feed
 */
function RSSURL (form) {
	var searchQuery = buildSearchQueryWithEqualsAndWildcard(form, ' ', '*');
	searchQuery = searchQuery.replace(/ /g, '+');
	searchQuery = encodeURI(searchQuery);

	var RSSURL = "http://k1www.gbv.de/rssopc/rss_feeds.php?HOST=opac.sub.uni-goettingen.de&INTPORT=80&EXTPORT=80&DB=1&SEARCH=00yS!t" + searchQuery + "!aY!cN.oY.vD.wD&EDOC=588692600"

	return RSSURL;
}
 