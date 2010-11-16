/* Adapted from Indexdata's js-client.js by ssp */

/* A very simple client that shows a basic usage of pz2.js */

/* Create a parameters array and pass it to the pz2's constructor.
 Then register the form submit event with the pz2.search function.
 autoInit is set to true on default.
*/
var usesessions = true;
var pazpar2path = '/pazpar2/search.pz2';
var showResponseType = '';

/* Maintain a list of all facet types so we can loop over it. 
   Don't forget to also set termlist attributes in the corresponding
   metadata tags for the service. */ 
var termListNames = ['xtargets', 'medium', 'author', 'date'];
var termListMax = {'xtargets': 25, 'medium': 10, 'author': 10, 'date': 10 };

if (document.location.hash == '#useproxy') {
	usesessions = false;
	pazpar2path = '/service-proxy/';
	showResponseType = 'json';
}


/* Simple-minded localisation:
   Create a hash for each language, then use the appropriate one on the page. */
var germanTerms = {
	'facet-title-xtargets': 'Kataloge',
	'facet-title-medium': 'Art',
	'facet-title-author': 'Autoren',
	'facet-title-subject': 'Themengebiete',
	'facet-title-date': 'Jahre',
	'detail-label-title': 'Titel',
	'detail-label-series-title': 'Serie',
	'detail-label-author': 'Autor',
	'detail-label-author-plural': 'Autoren',
	'detail-label-other-person': 'Person',
	'detail-label-other-person-plural': 'Personen',
	'detail-label-medium': 'Art',
	'detail-label-description': 'Information',
	'detail-label-description-plural': 'Informationen',
	'detail-label-series-title': 'Reihe',
	'detail-label-issn': 'ISSN',
	'detail-label-isbn': 'ISBN',
	'detail-label-doi': 'DOI',
	'detail-label-doi-plural': 'DOIs',
	'detail-label-verfügbarkeit': 'Verfügbarkeit',
	'elektronisch': 'digital',
	'gedruckt': 'gedruckt',
	'detail-label-id': 'PPN',
	'link': '[Link]',
	'Kataloge': 'Kataloge',
	'Google Books Vorschau': 'Google Books Vorschau',
	'Umschlagbild': 'Umschlagbild',
};


var localisations = {
	'de': germanTerms,
};


function localise (term) {
	var localised = localisations['de'][term];
	if ( localised == undefined ) {
		localised = term;
	}
	return localised;
}




my_paz = new pz2( { "onshow": my_onshow,
					"showtime": 500,			//each timer (show, stat, term, bytarget) can be specified this way
					"pazpar2path": pazpar2path,
					"oninit": my_oninit,
					"onstat": my_onstat,
					"onterm": my_onterm,
					"termlist": termListNames.join(","),
					"onbytarget": my_onbytarget,
	 				"usesessions" : usesessions,
					"showResponseType": showResponseType,
					"onrecord": my_onrecord,
					"serviceId": my_serviceID } );
// some state vars
var curPage = 1;
var recPerPage = 100;
var curDetRecId = '';
var curDetRecData = null;
var curSort = [];
var curFilter = null;
var facetData = {}; // stores faceting information as sent by pazpar2
var filterArray = {};
var displaySort =  [{'fieldName': 'date', 'direction': 'descending'}, 
						{'fieldName': 'author', 'direction': 'ascending'}, 
						{'fieldName': 'title', 'direction': 'ascending'}];
var displayFilter = undefined;
var hitList = []; // local storage for the records sent from pazpar2
var displayHitList = []; // filtered and sorted list used for display
var submitted = false;




//
// pz2.js event handlers:
//
function my_oninit() {
	my_paz.stat();
	my_paz.bytarget();
}



/*	appendInfoToContainer
	Convenince method to append an item to another one, even if undefineds and arrays are involved.
	input:	* info - the DOM element to insert
			* container - the DOM element to insert info to
*/
var appendInfoToContainer = function (info, container) {
	if (info != undefined && container != undefined ) {
		if (typeof(info.length) === 'undefined') {
			// info is a single item
			container.appendChild(info);
		}
		else {
			for (var infoNumber in info) {
				container.appendChild(info[infoNumber]);
			}
		}
	}
}



/*	fieldContentInRecord
	Returns a record's md-fieldName field. 
		* Concatenated when several instances of the field are present.
	input:	fieldName - name of the field to use
			record - pazpar2 record
			lowerCase (optional) - boolean indicating whether to transform the string to lower case
	output: string with content of the field in the record
*/		
function fieldContentInRecord (fieldName, record, lowerCase) {
	var result = String(fieldContentsInRecord(fieldName, record));

	if (lowerCase) {
		result.toLowerCase();
	}

	return result;
}



/*	fieldContentsInRecord
	Returns array of data from a record's md-fieldName field.
		* special case for xtargets which is mapped to location/@name
		* special case for date which uses the date from each location rather than the merged range
	input:	fieldName - name of the field to use
			record - pazpar2 record
	output:	array with content of the field in the record
*/
function fieldContentsInRecord (fieldName, record) {
	var result = [];
	
	if ( fieldName === 'xtargets' ) {
		// special case xtargets: gather server names from location info for this
		for ( var locationNumber in record.location) {
			result.push(record.location[locationNumber]['@name']);
		}
	}
	else if ( fieldName === 'date' ) {
		// special case for dates: go through locations and collect date for each edition
		for ( var locationNumber in record.location) {
			var date = record.location[locationNumber]['md-date'];
			if (date) {
				result.push(date);
			}
		}		
	}
	else {
		result = record['md-' + fieldName];
	}

	return result;
}





/*	displayList
	Converts a given list of data to a the list used for display by:
		1. applying filters
		2. sorting
	according to the setup in the displaySort and displayFilter variables.
*/
function displayList (list) {
	/*	filter
		Returns a filtered list of pazpar2 records according to the current filterArray.
		input:	list - list of pazpar2 records
		output:	list of pazpar2 records
	*/
	var filter = function (listToFilter) {
		/*	matchesFilters
			Returns whether the passed record passes all filters.
				I.e.: for each facet type it meets one of the listed conditions.
			input:	record - pazpar2 record
			output: boolean
		*/
		var matchesFilters = function (record) {
			var matches = true;
			for (var facetType in filterArray) {
				for (var filterIndex in filterArray[facetType]) {
					var filterValue = filterArray[facetType][filterIndex];
					if (facetType === 'xtargets') {
						for (var locationIndex in record.location) {
							matches = (record.location[locationIndex]['@name'] == filterValue);
							if (matches) { break; }
						}
					}
					else {
						matches = (fieldContentInRecord(facetType, record, true) == filterValue);
					}

					if (matches) { break; }
				}

				if (!matches) {	break; }
			}

			return matches;
		}


		var filteredList = [];
		for (var index in listToFilter) {
			var item = listToFilter[index];
			if ( matchesFilters(item) ) {
				filteredList.push(item);
			}
		}
		return filteredList;
	}


	/*	sortFunction
		Sort function for pazpar2 records.
		Sorts by date or author according to the current setup in the displaySort variable.
		input:	record1, record2 - pazpar2 records
		output: negative/0/positive number
	*/
	var sortFunction = function(record1, record2) {
		/*	dateForRecord
			Returns the year / last year of a date range of the given pazpar2 record.
			If no year is present
			input:	record - pazpar2 record
			output: Date object with year found in the pazpar2 record
		*/
		function dateForRecord (record) {
			var dateArray = record['md-date'];
			if (dateArray) {
				var dateString = record['md-date'][0];
				if (dateString) {
					var dateArray = dateString.split('-');
					var date = new Date(dateArray[dateArray.length - 1]);
				}
			}

			// Records without a date are treated as very old.
			// Except when they are Guide Links which are treated as coming from the future.
			if (date == undefined) {
				if (record['location'][0]['@id'].search('ssgfi') != -1) {
					date = new Date(2500,1,1);
				}
				else {
					date = new Date(0,1,1);
				}	
			}

			return date;
		}

		
		var result = 0;

		for (var sortCriterionIndex in displaySort) {
			var fieldName = displaySort[sortCriterionIndex].fieldName;
			var direction = (displaySort[sortCriterionIndex].direction === 'ascending') ? 1 : -1;

			if (fieldName == 'date') {
				var date1 = dateForRecord(record1);
				var date2 = dateForRecord(record2);
				
				result = (date1.getTime() - date2.getTime()) * direction;
			}
			else {
				var string1 = fieldContentInRecord(fieldName, record1, true);
				var string2 = fieldContentInRecord(fieldName, record2, true);
				
				if (string1 == string2) {
					result = 0;
				}
				else if (string1 == undefined) {
					result = 1;
				}
				else if (string2 == undefined) {
					result = -1;
				}
				else {
					result = ((string1 < string2) ? -1 : 1) * direction;
				}
			}

			if (result != 0) {
				break;
			}
		}

		return result;
	}
	

	return filter(list).sort(sortFunction);
}



/*	updateAndDisplay
	Updates the displayHitList and redraws.
*/
function updateAndDisplay () {
	displayHitList = displayList(hitList);
	display();
}



/*	my_onshow
	Callback for pazpar2 when data become available.
	Goes through the records and adds them to hitList.
	Regenerates displayHitList and triggers redisplay.
	input:	data - result data passed from pazpar2
*/
function my_onshow (data) {
	for (var hitNumber in data.hits) {
		var hit = data.hits[hitNumber];
		var hitID = hit.recid[0];
		if (hitID) {
			hitList[hitID] = hit;
		}
	}

	updateAndDisplay();
}



/*	display
	Displays the records stored in displayHitList as short records.
*/
function display () {

	/*	markupForField
		Creates DOM element and content for a field name; Appends it to given container.
		input:	fieldName - string with key for a field stored in hit
				container (optional)- the DOM element we created is appended here
				prepend (optional) - string inserted before the DOM element with the field data
				append (optional) - string appended after the DOM element with the field data
		output: the DOM SPAN element that was appended
	*/
	var markupForField = function (fieldName, container, prepend, append) {
		var theHit = hit['md-' + fieldName];

		if (theHit !== undefined && container) {
			var span = document.createElement('span');
			span.setAttribute('class', 'pz2-' + fieldName);
			span.appendChild(document.createTextNode(theHit));
		
			if (container) {
				if (prepend) {
					container.appendChild(document.createTextNode(prepend));
				}
				container.appendChild(span);
				if (append) {
					container.appendChild(document.createTextNode(append));
				}
			}
		}

		return span;
	}



	/*	titleInfo
		Returns DOM SPAN element with markup for the current hit's title.
		output:	DOM SPAN element
	*/
	var titleInfo = function() {
		var titleCompleteElement = document.createElement('span');
		titleCompleteElement.setAttribute('class', 'pz2-title-complete');

		var titleMainElement = document.createElement('span');
		titleCompleteElement.appendChild(titleMainElement);
		titleMainElement.setAttribute('class', 'pz2-title-main');
		markupForField('title', titleMainElement);
		markupForField('multivolume-title', titleMainElement, ' ');

		markupForField('title-remainder', titleCompleteElement, ' ');
		markupForField('title-number-section', titleCompleteElement, ' ');

		titleCompleteElement.appendChild(document.createTextNode('. '));

		return titleCompleteElement;
	}



	/*	authorInfo
		Returns DOM SPAN element with markup for the current hit's author information.
		The pre-formatted title-responsibility field is preferred and a list of author
			names is used as a fallback.
		output:	DOM SPAN element
	*/
	var authorInfo = function() {
		var outputText;

		if (hit['md-title-responsibility'] !== undefined) {
			// use responsibility field if available
		 	outputText = hit['md-title-responsibility'];
		}
		else if (hit['md-author'] !== undefined) {
			// otherwise try to fall back to author fields
			var authors = [];
			for (var index = 0; index < hit['md-author'].length; index++) {
				var authorname = hit['md-author'][index];
				authors.push(authorname);
			}

			outputText = authors.join('; ');
		}

		if (outputText) {
			// ensure the author designation ends with a single full stop
			var extraFullStop = '';
			if (outputText.length > 1 && outputText[outputText.length - 1] != '.') {
				extraFullStop = '.';
			}
		
			var output = document.createElement('span');
			output.setAttribute('class', 'pz2-item-responsibility');
			output.appendChild(document.createTextNode(outputText + extraFullStop))
		}
		
		return output;
	}



	/*	appendJournalInfo
		Appends DOM SPAN element with the current hit's journal information to linkElement.
	*/
	var appendJournalInfo = function () {
		var output = document.createElement('span');
		output.setAttribute('class', 'pz2-journal');

		var journalTitle = markupForField('journal-title', linkElement, localise(' In') + ': ');
		if (journalTitle) {
			markupForField('journal-subpart', journalTitle, ', ')
			journalTitle.appendChild(document.createTextNode('.'));
		}
	}


	/* 	updatePagers
		Updates pagers and record counts shown in .pz2-pager elements.
	*/
	var updatePagers = function () {
		$('div.pz2-pager').each( function(index) {
				// remove existing pagers
				$(this).empty()

				var onsides = 6;
				var pages = Math.ceil(displayHitList.length / recPerPage);
	
				var firstClickable = ( curPage - onsides > 0 ) ? curPage - onsides : 1;
				var lastClickable = firstClickable + 2*onsides < pages ? firstClickable + 2*onsides	: pages;

				// create pager
				if (curPage > 1) {
					var prevLink = document.createElement('a');
					prevLink.setAttribute('class', 'pz2-prev');
					prevLink.setAttribute('href', '#');
					prevLink.setAttribute('onclick', 'pagerPrev();return false;');
					prevLink.setAttribute('title', localise('Vorige Trefferseite anzeigen'));
					prevLink.appendChild(document.createTextNode('«'));
					this.appendChild(prevLink);
				}
		
				var pageList = document.createElement('ol');
				pageList.setAttribute('class', 'pz2-pages');
				this.appendChild(pageList);	
			
				var dotsItem = document.createElement('li');
				dotsItem.appendChild(document.createTextNode('…'));
			
				if (firstClickable > 1) {
					pageList.appendChild(dotsItem.cloneNode());
				}
		
				for(var pageNumber = firstClickable; pageNumber <= lastClickable; pageNumber++) {
					var pageItem = document.createElement('li');
					pageList.appendChild(pageItem);
					if(pageNumber != curPage) {
						var linkElement = document.createElement('a');
						linkElement.setAttribute('href', '#');
						linkElement.setAttribute('onclick', 'showPage(' + pageNumber + ');return false;');
						linkElement.appendChild(document.createTextNode(pageNumber));
						pageItem.appendChild(linkElement);
					}
					else {
						pageItem.setAttribute('class', 'currentPage');
						pageItem.appendChild(document.createTextNode(pageNumber));
					}
				}

				if (pages - curPage > 0) {
					var nextLink = document.createElement('a');
					nextLink.setAttribute('class', 'pz2-next');
					nextLink.setAttribute('href', '#');
					nextLink.setAttribute('onclick', 'pagerNext();return false;');
					nextLink.setAttribute('title', localise('Nächste Trefferseite anzeigen'));
					nextLink.appendChild(document.createTextNode('»'));
					this.appendChild(nextLink);			
				}
		
				if (lastClickable < pages) {
					pageList.appendChild(dotsItem);
				}
			
			
				// add record count information
				var recordCountDiv = document.createElement('div');
				recordCountDiv.setAttribute('class', 'pz2-recordCount');
				var infoString = String(firstIndex + 1) + '-' 
									+ String(firstIndex + numberOfRecordsOnPage) 
									+ ' ' + localise('von') + ' ' 
									+ String(displayHitList.length);
				if (displayFilter) {
					infoString += ' ' + localise('gefiltert');
				}
				recordCountDiv.appendChild(document.createTextNode(infoString));
				this.appendChild(recordCountDiv);
			}
		);	
	}



	// Create results list.
	var OL = document.createElement('ol');
	var firstIndex = recPerPage * (curPage - 1);
	var numberOfRecordsOnPage = Math.min(displayHitList.length - firstIndex, recPerPage);
	OL.setAttribute('start', firstIndex + 1);

	for (var i = 0; i < numberOfRecordsOnPage; i++) {
		var hit = displayHitList[firstIndex + i];

		var LI = document.createElement('li');
		OL.appendChild(LI);
		LI.setAttribute('id', 'recdiv_' + HTMLIDForRecordData(hit));

		var linkElement = document.createElement('a');
		LI.appendChild(linkElement);
		linkElement.setAttribute('href', '#');
		linkElement.setAttribute('class', 'pz2-recordLink');
		linkElement.setAttribute('onclick', 'toggleDetails(this.id);return false;');
		linkElement.setAttribute('id', 'rec_' + HTMLIDForRecordData(hit));

		appendInfoToContainer(titleInfo(), linkElement);
		var authors = authorInfo();
		appendInfoToContainer(authors, linkElement);

		if (hit['md-medium'] == 'article') {
			appendJournalInfo();
		}
		else {
			var spaceBefore = ' ';
			if (authors) {
				spaceBefore = ', ';
			}
			markupForField('date', linkElement, spaceBefore, '.');
		}

		if (hit.recid == curDetRecId) {
			appendInfoToContainer(renderDetails(curDetRecData), linkElement);
		}
	}

	// Replace old results list
	var results = document.getElementById("pz2-results");
	$(results).empty();
	results.appendChild(OL);

	updatePagers();
}



function my_onstat(data) {
	var stat = document.getElementById("pz2-stat");
	if (stat == null)
	return;
	
	stat.innerHTML = '<h4>Status Information</h4term> -- Active clients: '
						+ data.activeclients
						+ '/' + data.clients + ' -- </span>'
						+ '<span>Retrieved records: ' + data.records
						+ '/' + data.hits + ' :.</span>';
}



/*	facetListForType
	Creates DOM elements for the facet list of the requested type.
		Uses facet information stored in facetData.
	input:	type - string giving the facet type
			preferOriginalFacets (optional) - boolean that triggers using 
				the facet information sent by pazpar2 
	output:	DOM elements for displaying the list of faces
*/
function facetListForType (type, preferOriginalFacets) {
	/*	isFilteredForType
		Returns whether there is a filter for the given type.
		input:	type - string with the type's name
		output:	boolean indicating whether there is a filter or not
	*/
	var isFilteredForType = function (type) {
		var result = false;
		if (filterArray[type]) {
			result = (filterArray[type].length > 0);
		}
		return result;
	}


	/*	facetInformationForType
		Creates list with facet information.
			* information is collected from the filtered hit list.
			* list is sorted by term frequency.
		output:	list of Objects with properties 'name' and 'freq'(ency)
					(these are analogous to the Objects passed to the callback by pz2.js)
	*/
	var facetInformationForType = function (type) {
		/*	isFiltered
			Returns whether there is any filter active.
				(One may want to use pazpar2's original term lists if not.)
			output:	boolean indicating whether a filter is active
		*/
		var isFiltered = function () {
			var isFiltered = false;
			for (var filterType in filterArray) {
				isFiltered = isFilteredForType(filterType);
				if (isFiltered) { break; }
			}
			return isFiltered;
		}


		var termList = [];
		if (!isFiltered() && preferOriginalFacets) {
			termList = facetData[type]
		}
		else {
			// loop through data ourselves to gather information
			var termArray = {};
			for (var recordIndex in displayHitList) {
				var record = displayHitList[recordIndex];
				var dataArray = fieldContentsInRecord(type, record);
				for (var index in dataArray) {
					var data = dataArray[index];
					if (termArray[data]) {
						termArray[data]++;
					}
					else {
						termArray[data] = 1;
					}
				}
			}
			
			// sort by term frequency
			for (var term in termArray) {
				termList.push({'name': term, 'freq': termArray[term]});
			} 
			termList.sort( function(term1, term2) {
					if (term1.freq < term2.freq) { return 1; }
					else if (term1.freq == term2.freq) {
						if (term1.name < term2.name) { return -1; }
						else { return 1; }
					}
					else { return -1; }
				}
			);

			// special case for dates: take the topmost items and sort by date
			if (type === 'date') {
				if (termList.length > termListMax['date']) {
					termList.splice(termListMax['date'], termList.length - termListMax['date']);
				}
				termList.sort( function(term1, term2) {
						return (term1.name < term2.name) ? 1 : -1;
					}
				);
			}
		}

		return termList;
	}


	// Create container and heading.
	var container = document.createElement('div');
	container.setAttribute('class', 'pz2-termList pz2-termList-' + type);
	var heading = document.createElement('h5')
	container.appendChild(heading);
	var headingText = localise('facet-title-'+type);
	if (isFilteredForType(type)) {
		headingText += ' [' + localise('gefiltert') + ']';
	}
	heading.appendChild(document.createTextNode(headingText));
	var list = document.createElement('ol');
	container.appendChild(list);

	// Loop through list of terms for the type and create an item with link for each one.
	var terms = facetInformationForType(type);
	for (var i = 0; i < terms.length && i < termListMax[type]; i++) {
		var item = document.createElement('li');
		list.appendChild(item);

		var link = document.createElement('a');
		item.appendChild(link);
		link.setAttribute('href', '#');
		var facetName = terms[i].name; 
		link.setAttribute('onclick', 
			'limitResults("' + type + '","' + facetName + '");return false;');
		link.appendChild(document.createTextNode(facetName));

		var count = document.createElement('span');
		link.appendChild(count);
		count.setAttribute('class', 'pz2-facetCount');
			count.appendChild(document.createTextNode(terms[i].freq));

		// Mark facets which are currently active and add button to remove faceting.
		if (isFilteredForType(type)) {
			for (var filterIndex in filterArray[type]) {
				if (facetName === filterArray[type][filterIndex]) {
					item.setAttribute('class', 'pz2-activeFacet');
					var cancelLink = document.createElement('a');
					item.appendChild(cancelLink);
					cancelLink.setAttribute('href', '#');
					cancelLink.setAttribute('class', 'pz2-facetCancel');
					cancelLink.setAttribute('onclick', 
						'delimitResults("' + type + '","' + facetName + '"); return false;');
					cancelLink.appendChild(document.createTextNode(localise('Filter aufheben')));
					break;
				}
			}
		}
	}
	return container;		
}



/*	updateFacetLists
	Updates all facet lists for the facet names stored in termListNames.
*/
function updateFacetLists () {
	var container = document.getElementById('pz2-termLists');
	$(container).empty();

	var mainHeading = document.createElement('h4');
	container.appendChild(mainHeading);
	mainHeading.appendChild(document.createTextNode(localise('Facetten')));

	for ( index in termListNames ) {
		container.appendChild(facetListForType(termListNames[index]));
	}
}



/*	my_onterm
	pazpar2 callback for receiving facet data.
		Stores faces data and recreates facets on page.
	input:	data - Array with facet information.
*/
function my_onterm (data) {
	facetData = data;
	updateFacetLists();
}


function my_onrecord(data) {
	// FIXME: record is async!!
	clearTimeout(my_paz.recordTimer);
	// in case on_show was faster to redraw element
	var detRecordDiv = document.getElementById('det_'+ HTMLIDForRecordData(data));
	if (detRecordDiv) return;
	curDetRecData = data;
	var recordDiv = document.getElementById('recdiv_'+ HTMLIDForRecordData(curDetRecData));
	var details = renderDetails(curDetRecData);
	recordDiv.appendChild( details );
}


function my_onbytarget(data) {
	var targetDiv = document.getElementById("pz2-byTarget");
	var table ='<table><thead><tr><td>Target ID</td><td>Hits</td><td>Diags</td>'
		+'<td>Records</td><td>State</td></tr></thead><tbody>';
	
	for (var i = 0; i < data.length; i++ ) {
		table += "<tr><td>" + data[i].id +
			"</td><td>" + data[i].hits +
			"</td><td>" + data[i].diagnostic +
			"</td><td>" + data[i].records +
			"</td><td>" + data[i].state + "</td></tr>";
	}

	table += '</tbody></table>';
	targetDiv.innerHTML = table;
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// wait until the DOM is ready
function domReady () 
{
	if ( document.search ) {
		document.search.onsubmit = onFormSubmitEventHandler;
		document.search.query.value = '';
		document.select.sort.onchange = onSelectDdChange;
		document.select.perpage.onchange = onSelectDdChange;
	}
}

// when search button pressed
function onFormSubmitEventHandler() 
{
	resetPage();
	loadSelect();
	triggerSearch();
	submitted = true;
	return false;
}



/*	onSelectDdChange
	Called when sort-order popup menu is changed.
		Gathers new sort-order information and redisplays.
*/
function onSelectDdChange() {
	loadSelect();
	updateAndDisplay();
	return false;
}



/*	resetPage
	Empties result lists and filters, switches to first page and redisplays.
*/
function resetPage() {
	curPage = 1;
	hitList = [];
	displayHitList = [];
	filterArray = {};
	display();
}



function triggerSearch () {
	my_paz.search(document.search.query.value, recPerPage, curSort, curFilter);
}



/*	curSortToDisplaySort
	Takes the passed sort value, parses it according to pazpar2's documentation and
		sets up the according displaySort Object.
	input:	curSortString - string with pazpar2-style sort criteria
*/
function curSortToDisplaySort (curSortString) {
	var sortCriteria = curSortString.split(',');

	displaySort = [];
	for ( var criterionIndex in sortCriteria ) {
		var criterionParts = sortCriteria[criterionIndex].split(':');
		if (criterionParts.length == 2) {
			var fieldName = criterionParts[0];
			var direction = criterionParts[1];
			displaySort.push({'fieldName': fieldName,
								'direction': ((direction == 0) ? 'descending' : 'ascending')});
		}
	}
}


function loadSelect () {
	curSort = document.select.sort.value;
	curSortToDisplaySort(curSort);
	recPerPage = document.select.perpage.value;
}


// limit the query after clicking the facet
function limitQuery (field, value)
{
	document.search.query.value += ' and ' + field + '="' + value + '"';
	onFormSubmitEventHandler();
}

// limit by target functions
function limitTarget (id, name) {
	var navi = document.getElementById('pz2-navi');
	navi.innerHTML = 
		'Source: <a class="crossout" href="#" onclick="delimitTarget();return false;">'
		+ name + '</a>';
	navi.innerHTML += '<hr/>';
	curFilter = 'pz:id=' + id;
	resetPage();
	loadSelect();
	triggerSearch();
	return false;
}

function delimitTarget (id) {
	var navi = document.getElementById('pz2-navi');
	navi.innerHTML = '';
	curFilter = null; 
	resetPage();
	loadSelect();
	triggerSearch();
	return false;
}



/*	limitResults
	Adds a filter for term for the data of type kind. Then redisplays.
	input:	* kind - string with name of facet type
			* term - string that needs to match the facet
*/
function limitResults(kind, term) {
	if (filterArray[kind]) {
		// add alternate condition to an existing filter
		filterArray[kind].push(term);
	}
	else {
		// the first filter of its kind: create array
		filterArray[kind] = [term];
	}

	updateAndDisplay();
	updateFacetLists();
}



/*	delimitResults
	Removes a filter for term from the data of type kind. Then redisplays.
	input:	* kind - string with name of facet type
			* term (optional) - string that shouldn't be filtered for
					all terms are removed if term is omitted.
*/
function delimitResults(kind, term) {
	if (filterArray[kind]) {
		if (term) {
			// if a term is given only delete occurrences of 'term' from the filter
			for (var index in filterArray[kind]) {
				if (filterArray[kind][index] == term) {
					filterArray[kind].splice(index,1);
				}
			}
			if (filterArray[kind].length == 0) {
				filterArray[kind] = undefined;
			}
		}
		else {
			// if no term is given, delete the complete filter
			filterArray[kind] = undefined;
		}

		updateAndDisplay();
		updateFacetLists();
	}
}



/*	showPage
	Display the results page with the given number.
		If the number is out of range, go to the first or last page instead.
	input:	pageNum - number of the page to be shown
*/
function showPage (pageNum) {
	curPage = Math.min( Math.max(0, pageNum), Math.ceil(displayHitList.length / recPerPage) );
	display();
}


/*	pagerNext
	Display the next page (if available).
*/
function pagerNext() {
	showPage (curPage + 1);
}


/*	pagerPrev
	Display the previous page (if available).
*/
function pagerPrev() {
	showPage(curPage - 1);
}





// switching view between targets and records

function switchView(view) {
	
	var targets = document.getElementById('pz2-targetView');
	var records = document.getElementById('pz2-recordView');
	
	switch(view) {
		case 'pz2-targetView':
			targets.style.display = "block";			
			records.style.display = "none";
			break;
		case 'pz2-recordView':
			targets.style.display = "none";			
			records.style.display = "block";
			break;
		default:
			alert('Unknown view.');
	}
}

// detailed record drawing
function toggleDetails (prefixRecId) {
	var recId = prefixRecId.replace('rec_', '');
	
	var detRecordDiv = document.getElementById('det_'+ recId);
	if (detRecordDiv) {
		// Detailed record information is present.
		if ( detRecordDiv.offsetHeight == 0 ) {
			// … not visible, so show it
			detRecordDiv.setAttribute('style', 'display:block');
		}
		else {
			// … it's visible so hide it
			detRecordDiv.setAttribute('style', 'display:none');
		}
	}
	else {
		// Create detailed record information if
		var parent = document.getElementById('recdiv_'+ recId);
		parent.appendChild(renderDetails(recordIDForHTMLID(recId)));
	}
}



function replaceHtml(el, html) {
  var oldEl = typeof el === "string" ? document.getElementById(el) : el;
  /*@cc_on // Pure innerHTML is slightly faster in IE
	oldEl.innerHTML = html;
	return oldEl;
	@*/
  var newEl = oldEl.cloneNode(false);
  newEl.innerHTML = html;
  oldEl.parentNode.replaceChild(newEl, oldEl);
  /* Since we just removed the old element from the DOM, return a reference
	 to the new element, which can be used to restore variable references. */
  return newEl;
};





function renderDetails(recordID, marker) {
	/*	deduplicate
		Removes duplicate entries from an array. 
		The first occurrence of any item is kept, later ones are removed.
		This function works in place and alters the original array.
		input:	information - array to remove duplicate entries from.
	*/
	var deduplicate = function (information) {
		for (var i = 0; i < information.length; i++) {
			var item = information[i];
			var isDuplicate = false;
			for (var j = 0; j < i; j++) {
				var jtem = information[j];					
				if ( item == jtem) {
					isDuplicate = true;
					information.splice(i, 1);
					i--;
					break;
				}
			}
		}	
	}



	/*	markupInfoItems
		Returns marked up version of the DOM items passed, putting them into a list if necessary:
		input:	infoItems - array of DOM elements to insert
		output: * 1-element array => just the element
				* multi-element array => UL with an LI containing each of the elements
				* empty array => undefined
	*/
	var markupInfoItems = function (infoItems) {
		var result;

		if (infoItems.length == 1) {
			 result = infoItems[0];
		}
		else if (infoItems.length > 1) {
			result = document.createElement('ul');
			$(infoItems).each( function(index) {
					var LI = document.createElement('li');
					result.appendChild(LI);
					LI.appendChild(this);
				}
			);
		}

		return result;
	}



	/*	detailLine
		input:	title - string with element's name
				informationElements - array of DOM elements with the information to be displayed
		output: DOM element of table row with 
				* heading containing the localised and plural conscious name of the item
				* data containing the informationElements passed. 
					If there is more than one of them, they are wrapped in a list
	*/
	var detailLine = function (title, informationElements) {
		if (informationElements && title) {
			var headingText;	

			if (informationElements.length == 1) {
				headingText = localise('detail-label-'+title);
			}
			else {
				var labelKey = 'detail-label-' + title + '-plural';
				var labelLocalisation = localise(labelKey);
				if (labelKey === labelLocalisation) { // no plural form, fall back to singular
					labelKey = 'detail-label-' + title;
					labelLocalisation = localise(labelKey);
				}
				headingText = labelLocalisation;
			}

			var infoItems = markupInfoItems(informationElements);

			if (infoItems) { // we have information, so insert it
				var tableRow = document.createElement('tr');
				tableRow.setAttribute('class', 'pz2-detail-' + title);

				var rowHeading = document.createElement('th');
				tableRow.appendChild(rowHeading);
				rowHeading.appendChild(document.createTextNode(headingText + ':'));

				var rowData = document.createElement('td');
				tableRow.appendChild(rowData);
				rowData.appendChild(infoItems);
			}
		}

		return tableRow;
	}



	/*	detailLineAuto
		input:	title - string with the element's name
		output:	DOM element for title and the data coming from data[md-title]
				as created by detailLine.
	*/
	var detailLineAuto = function (title) {
		var result = undefined;
		var element = DOMElementForTitle(title);

		if (element.length !== 0) {
			result = detailLine( title, element );
		}

		return result;
	} 


	
	/*	linkForDOI
		input:	DOI - string with DOI
		output: DOM anchor element with link to the DOI at dx.doi.org
	*/
	var linkForDOI = function (DOI) {
		var linkElement = document.createElement('a');
		linkElement.setAttribute('href', 'http://dx.doi.org/' + DOI);
		linkElement.setAttribute('target', 'pz2-linktarget');
		linkElement.appendChild(document.createTextNode(DOI));
		return linkElement;
	}



	/*	DOMElementForTitle
		input:	title - title string
		output:	nil, if the field md-title does not exist in data. Otherwise:
				array of DOM elements created from the fields of data[md-title]
	*/
	var DOMElementForTitle = function (title) {
		var result = [];
		if ( data['md-' + title] !== undefined ) {
			var theData = data['md-' + title];
			deduplicate(theData);

			// run loop backwards as pazpar2 seems to reverse the order of metadata items
			for (var dataNumber = theData.length -1; dataNumber >= 0; dataNumber--) {
				var rawDatum = theData[dataNumber];
				var wrappedDatum;
				switch	(title) {
					case 'doi':
						wrappedDatum = linkForDOI(rawDatum);
						break;
					default:
						wrappedDatum = document.createTextNode(rawDatum);
				}
				result.push(wrappedDatum);
			}
		}

		return result;
	}



	/*	ZDBQuery
		Loads XML journal info from ZDB via a proxy on our own server
			(to avoid cross-domain load problems).
		Inserts the information into the DOM.

		input:	element - DOM element that the resulting information is inserted into.
	*/
	var addZDBInfoIntoElement = function (element) {
		var ISSNs = data['md-issn'];

		// Do nothing if there are no ISSNs.
		if (ISSNs === undefined) { return; }

		var serviceID = 'sub:vlib';
		var parameters = 'sid=' + serviceID + '&genre=article';

		for (ISSNNumber in ISSNs) {
			parameters += '&issn=' + ISSNs[ISSNNumber];
		}

		var year = data['md-date'];
		if (year) {
			var yearNumber = parseInt(year[0], 10);
			parameters += '&date=' + yearNumber;
		}

		var ZDBURL = '/zdb/full.xml?' + parameters;

		$.get(ZDBURL,
			/*	AJAX callback
				Creates DOM elements with information coming from ZDB.
				input:	resultData - XML from ZDB server
				uses:	element - DOM element for inserting the information into
			*/
			function (resultData) {

				/*	ZDBInfoItemForResult
					Turns XML of a single ZDB Result a DOM element displaying the relevant information.
					input:	ZDBResult - XML Element with a Full/(Print|Electronic)Data/ResultList/Result element
					output:	DOM Element for displaying the information in ZDBResult that's relevant for us
				*/
				var ZDBInfoItemForResult = function (ZDBResult) {
					var status = ZDBResult.getAttribute('state');
					var statusText;

					// Determine the access status of the result.
					if (status == 0) {
						statusText = localise('frei verfügbar');
					}
					else if (status == 1) {
						statusText = localise('teilweise frei verfügbar');
					}
					else if (status == 2) {
						statusText = localise('verfügbar');
					}
					else if (status == 3) {
						statusText = localise('teilweise verfügbar');
					}
					else if (status == 4) {
						statusText = localise('nicht verfügbar');
					}
					else if (status == 5) {
						statusText = localise('diese Ausgabe nicht verfügbar');
					}
					else {
						/*	Remaining cases are:
								status == -1: non-unique ISSN
								status == 10: unknown
						*/
					}
					
					// Only display detail information if we do have access.
					if (statusText) {
						var statusDiv = document.createElement('div');
						statusDiv.setAttribute('class', 'pz2-ZDBStatusInfo');

						var accessLinkURL = $('AccessURL', ZDBResult);
						if (accessLinkURL.length > 0) {
							// Having an AccessURL implies this is inside ElectronicData.
							statusDiv.appendChild(document.createTextNode(statusText));
							var accessLink = document.createElement('a');
							statusDiv.appendChild(document.createTextNode(' – '));
							statusDiv.appendChild(accessLink);
							accessLink.setAttribute('href', accessLinkURL[0].textContent);
							accessLink.appendChild(document.createTextNode(localise('Zugriff')));
							accessLink.setAttribute('target', 'pz2-linktarget');

							var additionals = [];
							var ZDBAdditionals = $('Additional', ZDBResult);
							ZDBAdditionals.each( function (index) {
									additionals.push(this.textContent);
								}
							);
							if (additionals.length > 0) {
								accessLink.setAttribute('title', additionals.join('; ') + '.');
							}
						}
						else if (status < 4) {
							// Absence of an AccessURL implies this is inside PrintData.
							// status > 3 means the volume is not available. Don't print info then.
							var locationInfo = document.createElement('span');
							var infoText = '';

							var period = $('Period', ZDBResult)[0];
							if (period) {
								infoText += period.textContent + ': ';

							}
							var location = $('Location', ZDBResult)[0];
							if (location) {
								infoText += location.textContent + ' ';
							}

							var signature = $('Signature', ZDBResult)[0];
							if (signature) {
								infoText += signature.textContent;
							}

							locationInfo.appendChild(document.createTextNode(infoText));
							statusDiv.appendChild(locationInfo);
						}
						else {
							statusDiv = undefined;
						}
					}	
					return statusDiv;
				}



				/*	appendLibraryNameFromResultDataTo
					If we there is a Library name, insert it into the target container.
					input:	* data: ElectronicData or PrintData element from ZDB XML
							* target: DOM container to which the marked up library name is appended
				*/
				var appendLibraryNameFromResultDataTo = function (data, target) {
					var libraryName = $('Library', data)[0];
					if (libraryName) {
						var libraryNameSpan = document.createElement('span');
						libraryNameSpan.setAttribute('class', 'pz2-ZDBLibraryName');
						libraryNameSpan.appendChild(document.createTextNode(libraryName.textContent));
						target.appendChild(libraryNameSpan);
					}
				}



				/*	ZDBInfoElement
					Coverts ZDB XML data for electronic or print journals
						to DOM elements displaying their information.
					input:	data - ElectronicData or PrintData element from ZDB XML
					output:	DOM element containing the information from data
				*/				
				var ZDBInfoElement = function (data) {
					var results = $('Result', data);

					if (results.length > 0) {
						var infoItems = [];
						results.each( function(index) {
								var ZDBInfoItem = ZDBInfoItemForResult(this);
								if (ZDBInfoItem) {
									infoItems.push(ZDBInfoItem);
								}
							}
						);

						if (infoItems.length > 0) {
							var infos = document.createElement('span');
							infos.appendChild(markupInfoItems(infoItems));
						}
					}

					return infos;
				}



				/*	ZDBInformation
					Converts complete ZDB XML data to DOM element containing information about them.
					input:	data - result from ZDB XML request
					output: DOM element displaying information about journal availability.
								If ZDB figures out the local library and the journal
									is accessible there, we display:
									* its name
									* electronic journal information with access link
									* print journal information
				*/
				var ZDBInformation = function (data) {
					var container = document.createElement('div');
					var ZDBLink = document.createElement('a');
					container.appendChild(ZDBLink);
					var ZDBLinkURL = 'http://services.d-nb.de/fize-service/gvr/html-service.htm?' + parameters;
					ZDBLink.setAttribute('href', ZDBLinkURL);
					ZDBLink.setAttribute('class', 'pz2-ZDBLink');
					ZDBLink.setAttribute('target', 'pz2-linktarget');
					ZDBLink.appendChild(document.createTextNode(localise('Informationen bei der Zeitschriftendatenbank')));
					
					var electronicInfos = ZDBInfoElement( $('ElectronicData', data) );
					var printInfos = ZDBInfoElement( $('PrintData', data) );
					
					if (electronicInfos || printInfos) {
						appendLibraryNameFromResultDataTo(data, container);
					}

					if (electronicInfos) {
						var heading = document.createElement('h4');
						container.appendChild(heading);
						heading.appendChild(document.createTextNode(localise('elektronisch')));
						container.appendChild(electronicInfos);
					}

					if (printInfos) {
						var heading = document.createElement('h4');
						container.appendChild(heading);
						heading.appendChild(document.createTextNode(localise('gedruckt')));
						container.appendChild(printInfos);
					}

					return container
				}


				var infoBlock = [ZDBInformation(resultData)];
				appendInfoToContainer( detailLine(localise('verfügbarkeit'), infoBlock), element);

			}
		);
	}
	



	/*	addGoogleBooksLinkIntoElement
		Figure out whether there is a Google Books Preview for the current data.
		input:	element: DOM element that is the container of the Google Books Preview button.
	*/
	var addGoogleBooksLinkIntoElement = function (element) {

		// Create list of search terms from ISBN and OCLC numbers.
		var searchTerms = [];
		for (locationNumber in data.location) {
			var numberField = String(data.location[locationNumber]['md-isbn']);
			var matches = numberField.replace(/-/g,'').match(/[0-9]{9,12}[0-9xX]/g);
			for (var matchNumber in matches) {
				searchTerms.push('ISBN:' + matches[matchNumber]);
			}
			numberField = String(data.location[locationNumber]['md-oclc']);
			matches = numberField.match(/[0-9]{4,}/g);
			for (var matchNumber in matches) {
				searchTerms.push('OCLC:' + matches[matchNumber]);
			}
		}
		

		// Query Google Books for the ISBN/OCLC numbers in question.
		var googleBooksURL = 'http://books.google.com/books?bibkeys=' + searchTerms 
					+ '&jscmd=viewapi&callback=?';
		$.getJSON(googleBooksURL,
			function(data) {
				/*
					If there are multiple results choose the one we want:
						1. If available the first one with 'full' preview capabilities,
						2. otherwise the first one with 'partial' preview capabilities,
						3. undefined if none of the results has preview capabilities.
					Usually the first item in the list is also the newest one.
				*/
				var selectedBook;
				$.each(data, 
					function(bookNumber, book) {
						if (book.preview === 'full') {
							selectedBook = book;
							return false;
						}
						else if (book.preview === 'partial' && selectedBook === undefined) {
							selectedBook = book;
						}
					}
				);
			
				// Add link to Google Books if there is a selected book.
				if (selectedBook !== undefined) {
					var bookLink = document.createElement('a');
					bookLink.setAttribute('href', selectedBook.preview_url);
					bookLink.onclick = openPreview;

					var language = $('html').attr('lang');
					if (language === undefined) {
						language = 'en';
					}
					var buttonImageURL = 'http://www.google.com/intl/' + language + '/googlebooks/images/gbs_preview_button1.gif';
					var buttonImage = document.createElement('img');
					buttonImage.setAttribute('src', buttonImageURL);
					buttonImage.setAttribute('alt', localise('Google Books Vorschau'));
					bookLink.appendChild(buttonImage);
					element.appendChild(bookLink);

					if (selectedBook.thumbnail_url !== undefined) {
						var coverArtImage = document.createElement('img');
						bookLink.appendChild(coverArtImage);
						coverArtImage.setAttribute('src', selectedBook.thumbnail_url);
						coverArtImage.setAttribute('alt', localise('Umschlagbild'));
						coverArtImage.setAttribute('class', 'bookCover');
					}
				}
			}
		);



		/*	openPreview
			Called when the Google Books button is clicked.
			Opens Google Preview.
			output: false (so the click isn't handled any further)
		*/
		var openPreview = function() {
			// Get hold of containing <div>, creating it if necessary.
			var previewContainerDivName = 'googlePreviewContainer';
			var previewContainerDiv = document.getElementById(previewContainerDivName);
			var previewDivName = 'googlePreview';
			var previewDiv = document.getElementById(previewDivName);

			if (!previewContainerDiv) {
				previewContainerDiv = document.createElement('div');
				previewContainerDiv.setAttribute('id', previewContainerDivName);
				$('#page').get(0).appendChild(previewContainerDiv);

				var titleBarDiv = document.createElement('div');
				titleBarDiv.setAttribute('class', 'titleBar');
				previewContainerDiv.appendChild(titleBarDiv);
				$(titleBarDiv).css({height:'20px', width:'100%', 
									position:'absolute', top:'-20px', background:'#eee'});

				var closeBoxLink = document.createElement('a');
				titleBarDiv.appendChild(closeBoxLink);
				$(closeBoxLink).css({display:'block', height:'16px', width:'16px', 
									position:'absolute', right:'2px', top:'2px', background:'#666'})
				closeBoxLink.setAttribute('href', 'javascript:$("#' + previewContainerDivName + '").hide(200);');

				var previewDiv = document.createElement('div');
				previewDiv.setAttribute('id', previewDivName);
				previewContainerDiv.appendChild(previewDiv);
			}
			else {
				$(previewContainerDiv).show(200);
			}

			var viewer = new google.books.DefaultViewer(previewDiv);
			viewer.load(this.href);

			return false;
		}

	} // end of addGoogleBooksLinkIntoElement


	
	/*	extraLinks
		Returns table row element with additional links:
			* Google Books, if possible
		output:	TR DOM element
	*/
	var extraLinks = function () {
		var tr = document.createElement('tr');
		tr.appendChild(document.createElement('th'));
		var td = document.createElement('td');
		tr.appendChild(td);

		var booksSpan = document.createElement('span');
		td.appendChild(booksSpan);
		booksSpan.setAttribute('class', 'googleBooks');
		addGoogleBooksLinkIntoElement(booksSpan);

		return tr;
	}



	/*	locationDetails
		Returns markup for each location of the item found from the current data.
		output:	DOM object with information about this particular copy/location of the item found
	*/
	var locationDetails = function () {

		/*	detailInfoItemWithLabel
			input:	fieldContent - DOM object with content to display in the field
					labelName - string displayed as the label
					dontTerminate - boolean:	false puts a ; after the text
												true puts nothing after the text
		*/
		var detailInfoItemWithLabel = function (fieldContent, labelName, dontTerminate) {
			var infoSpan;
			if ( fieldContent !== undefined ) {
				infoSpan = document.createElement('span');
				infoSpan.setAttribute('class', 'pz2-info'); 
				if ( labelName !== undefined ) {
					var infoLabel = document.createElement('span');
					infoSpan.appendChild(infoLabel);
					infoLabel.setAttribute('class', 'pz2-label');
					infoLabel.appendChild(document.createTextNode(labelName));
					infoSpan.appendChild(document.createTextNode(' '));
				}
				infoSpan.appendChild(document.createTextNode(fieldContent));

				if (!dontTerminate) {
					infoSpan.appendChild(document.createTextNode('; '));
				}
			}			
			return infoSpan;
		}



		/*	detailInfoItem
			input:	fieldName - string
			output:	DOM elements containing the label and information for fieldName data
						* the label is looked up from the localisation table
						* data[detail-label-fieldName] provides the data
		*/
		var detailInfoItem = function (fieldName) {
			var infoItem;
			var value = location['md-'+fieldName];

			if ( value !== undefined ) {
				var label;
				var labelID = 'detail-label-' + fieldName;
				var localisedLabelString = localise(labelID);

				if ( localisedLabelString != labelID ) {
					label = localisedLabelString;
				}

				var content = value.join(', ').replace(/^[ ]*/,'').replace(/[ ;.,]*$/,'');

				infoItem = detailInfoItemWithLabel(content, label);
			}

			return infoItem;
		}



		/*  cleanISBNs
			Takes the array of ISBNs in location['md-isbn'] and
				1. Normalises them
				2. Removes duplicates (particularly the ISBN-10 corresponding to an ISBN-13)
		*/
		var cleanISBNs = function () {
			/*	normaliseISBNsINString
				Vague matching of ISBNs and removing the hyphens in them.
				input: string
				output: string
			*/
			var normaliseISBNsInString = function (ISBN) {
				return ISBN.replace(/([0-9]*)-([0-9Xx])/g, '$1$2');
			}


			/*	pickISBN 
				input: 2 ISBN number strings without dashes
				output: if both are 'the same': the longer one (ISBN-13)
				        if they aren't 'the same': undefined
			*/
			var pickISBN = function (ISBN1, ISBN2) {
				var result = undefined;
				var numberRegexp = /([0-9]{9,12})[0-9xX].*/;
				var numberPart1 = ISBN1.replace(numberRegexp, '$1');
				var numberPart2 = ISBN2.replace(numberRegexp, '$1');
				if (!(numberPart1 == numberPart2)) {
					if (numberPart1.indexOf(numberPart2) != -1) {
						result = ISBN1;
					}
					else if (numberPart2.indexOf(numberPart1) != -1) {
						result = ISBN2;
					}
				}
				return result;
			}



			if (location['md-isbn'] !== undefined) {
				var newISBNs = []
				for (var index in location['md-isbn']) {
					var normalisedISBN = normaliseISBNsInString(location['md-isbn'][index]);
					for (var newISBNNumber in newISBNs) {
						var newISBN = newISBNs[newISBNNumber];
						var preferredISBN = pickISBN(normalisedISBN, newISBN);
						if (preferredISBN !== undefined) {
							newISBNs.splice(newISBNNumber, 1, preferredISBN);
							normalisedISBN = undefined;
							break;
						}
					}
					if (normalisedISBN !== undefined) {
						newISBNs.push(normalisedISBN);
					}
				}
				location['md-isbn'] = newISBNs;
			}
		}



		/*	electronicURLs
			Create markup for URLs in current data.
			output:	DOM element containing URLs as links.
		*/
		var electronicURLs = function() {
			var electronicURLs = location['md-electronic-url'];
			var URLsContainer;

			if (electronicURLs && electronicURLs.length != 0) {
				URLsContainer = document.createElement('span');

				for (var URLNumber in electronicURLs) {
					var URLInfo = electronicURLs[URLNumber];
					var linkText = '[' + localise('Link') + ']';
					var linkURL = URLInfo;
	
					if (typeof(URLInfo) === 'object' && URLInfo['#text'] !== undefined) {
						// URLInfo is not just an URL but an array also containing the link name 
						if (URLInfo['@name'] !== undefined) {
							linkText = '[' + URLInfo['@name'] + ']';
						}
						linkURL = URLInfo['#text'];
					}

					var link = document.createElement('a');
					URLsContainer.appendChild(link);
					link.setAttribute('href', linkURL);
					link.setAttribute('target', 'pz2-linktarget');
					link.innerHTML = linkText;
					if (URLNumber < electronicURLs.length - 1) {
						URLsContainer.appendChild(document.createTextNode(', '));
					}
				}
				URLsContainer.appendChild(document.createTextNode('; '));
			}
			return URLsContainer;		
		}



		/*	catalogueLink
			Returns a link for the current record that points to the catalogue page for that item.
			output:	DOM anchor element pointing to the catalogue page.
		*/
		var catalogueLink = function () {
			var targetURL = location['@id'];
			var targetName = location['@name'];
			var itemID = location['md-id'];

			var catalogueURL;			
			if (targetURL.search(/gso.gbv.de\/sru/) != -1) {
				catalogueURL = targetURL.replace(/(gso.gbv.de\/sru\/)(DB=[\.0-9]*)/,
										'http://gso.gbv.de/$2/CMD?ACT=SRCHA&IKT=1016');
				catalogueURL += '&TRM=ppn+' + itemID;
			}
			else if (targetURL.search(/z3950.gbv.de:20012\/subgoe_opc/) != -1) {
				catalogueURL = 'http://opac.sub.uni-goettingen.de/DB=1/CMD?ACT=SRCHA&IKT=1016&TRM=ppn+' + itemID;
			}

			if (catalogueURL) {
				var linkElement = document.createElement('a');
				linkElement.setAttribute('href', catalogueURL);
				linkElement.setAttribute('target', 'pz2-linktarget');
				linkElement.setAttribute('class', 'pz2-detail-catalogueLink')
				var linkText = localise('Ansehen und Ausleihen bei:');
				if (targetName) {
					linkText += ' ' + targetName;
				}
				linkElement.appendChild(document.createTextNode(linkText));
			}

			return linkElement;
		}



		var locationDetails = [];

		for ( var locationNumber in data.location ) {
			var localInfoItems = []

			var location = data.location[locationNumber];
			var localURL = location['@id'];
			var localName = location['@name'];

			var detailsRow = document.createElement('tr');
			var detailsHeading = document.createElement('th');
			detailsRow.appendChild(detailsHeading);
			detailsHeading.appendChild(document.createTextNode(localise('Ausgabe')+':'));
			var detailsData = document.createElement('td');
			detailsRow.appendChild(detailsData);

			appendInfoToContainer( detailInfoItem('edition'), detailsData );
			appendInfoToContainer( detailInfoItem('physical-extent'), detailsData );
			appendInfoToContainer( detailInfoItem('publication-name'), detailsData );
			appendInfoToContainer( detailInfoItem('publication-place'), detailsData );
			appendInfoToContainer( detailInfoItem('date'), detailsData );

			cleanISBNs();
			appendInfoToContainer( detailInfoItem('isbn'), detailsData );
			appendInfoToContainer( electronicURLs(), detailsData);
			appendInfoToContainer( catalogueLink(), detailsData);
			
			locationDetails.push(detailsRow);
		}

		return locationDetails;
	}

	
	var data = hitList[recordID];

	if (data) {
		var detailsDiv = document.createElement('div');
		detailsDiv.setAttribute('class', 'pz2-details');
		detailsDiv.setAttribute('id', 'det_' + HTMLIDForRecordData(data));

		var detailsTable = document.createElement('table');
		detailsDiv.appendChild(detailsTable);

		appendInfoToContainer( detailLineAuto('author'), detailsTable );
		appendInfoToContainer( detailLineAuto('other-person'), detailsTable )
		appendInfoToContainer( detailLineAuto('description'), detailsTable );
	 	appendInfoToContainer( detailLineAuto('medium'), detailsTable );
		appendInfoToContainer( detailLineAuto('series-title'), detailsTable );
		appendInfoToContainer( detailLineAuto('issn'), detailsTable );
		appendInfoToContainer( detailLineAuto('doi'), detailsTable );
		appendInfoToContainer( locationDetails(), detailsTable );
		appendInfoToContainer( extraLinks(), detailsTable );
		addZDBInfoIntoElement( detailsTable );
	}

	return detailsDiv;
}




/* 	HTMLIDForRecordData
	input:	pz2 record data object
	output:	ID of that object in HTML-compatible form
			(replacing spaces by dashes)
*/
function HTMLIDForRecordData (recordData) {
	if (recordData.recid[0] !== undefined) {
		return recordData.recid[0].replace(/ /g,'§+§');
	}
}



/*	recordIDForHTMLID
	input:	record ID in HTML compatible form
	output:	input with dashes replaced by spaces
*/
function recordIDForHTMLID (HTMLID) {
	return HTMLID.replace(/§\+§/g,' ');
}

//EOF
 
