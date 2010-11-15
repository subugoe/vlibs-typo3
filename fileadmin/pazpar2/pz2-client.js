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
var termListNames = ['xtargets', 'medium', 'author', 'subject', 'date'];
var termListMax = {'xtargets': 16, 'medium': 6, 'author': 10, 'subject': 10, 'date': 6 };

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
	'Ampelgraphik': 'Ampelgraphik der Elektronischen Zeitschriftenbibliothek zur Darstellung der Verfügbarkeit der Zeitschrift',
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
var totalRec = 0;
var curDetRecId = '';
var curDetRecData = null;
var curSort = 'date';
var curFilter = null;
var filter = [];
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



/*	my_onshow
	Creates a brief record for the data passed.
	Callback for pazpar2 when data become available.
	input: 	data - pazpar2 record data
*/
function my_onshow(data) {

	/*	markupForField
		Creates DOM element and content for a field name; Appends it to given container.
		input:	fieldName - string with key for a field stored in hit
				container (optional)- the DOM element we created is appended here
				prepend (optional) - string inserted before the DOM element with the field data
		output: the DOM SPAN element that was appended
	*/
	var markupForField = function (fieldName, container, prepend) {
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
		output:	* DOM SPAN element
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



	totalRec = data.merged;
	// move it out
	var pager = document.getElementById("pz2-pager");
	pager.innerHTML = "";
	pager.innerHTML +='<div class="pz2-recordCount">' 
					+ (data.start + 1) + ' to ' + (data.start + data.num) +
					 ' of ' + data.merged + ' (found: ' 
					 + data.total + ')</div>';
	drawPager(pager);
	// navi
	var results = document.getElementById("pz2-results");
  

	// Create results list.
	var OL = document.createElement('ol');
	OL.setAttribute('start', 1 + recPerPage * (curPage - 1))

	for (var i = 0; i < data.hits.length; i++) {
		var hit = data.hits[i];

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
		appendInfoToContainer(authorInfo(), linkElement);

		if (hit['md-medium'] == 'article') {
			appendJournalInfo();
		}
		else {
			markupForField('date', linkElement, ' ');
		}

		if (hit.recid == curDetRecId) {
			appendInfoToContainer(renderDetails(curDetRecData), linkElement);
		}
	}

	// Replace old results list
	while ( results.childNodes.length > 0 ) {
		results.removeChild( results.firstChild );
	}
	results.appendChild(OL);
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


function my_onterm(data) {
	// Creates markup for the termlist of type
	var termListHTML = function (type) {
		var theHTML = ['<div class="pz2-termList pz2-termList-', type, '">',
			'<h5>', localise('facet-title-'+type), '</h5><ol>'];
		var terms = data[type];
		for (var i = 0; i < terms.length && i < termListMax[type]; i++) {
			theHTML.push( '<li><a href="#"'
			+ ' target_name=' + terms[i].name
			+ ' onclick="limitTarget(' + terms[i].name + '), this.firstChild.nodeValue);return false;">'
			+ terms[i].name 
			+ '<span class="count">' + terms[i].freq + '</span>'
			+ '</a></li>');
		}
		theHTML.push('</ol></div>');
		return theHTML;		
	}

	var newTermListsHTML = ['<h4>Termlists:</h4>'];

	for ( index in termListNames ) {
		newTermListsHTML = newTermListsHTML.concat(termListHTML(termListNames[index]));
	}

	var currentTermLists = document.getElementById("pz2-termLists");
	replaceHtml(currentTermLists, newTermListsHTML.join(''));
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

function onSelectDdChange()
{
	if (!submitted) return false;
	resetPage();
	loadSelect();
	my_paz.show(0, recPerPage, curSort);
	return false;
}

function resetPage()
{
	curPage = 1;
	totalRec = 0;
}

function triggerSearch ()
{
	// TODO-ssp: Set filter to correct term (target-facet?)
	// var filter = NULL;
	my_paz.search(document.search.query.value, recPerPage, curSort, curFilter);
}

function loadSelect ()
{
	curSort = document.select.sort.value;
	recPerPage = document.select.perpage.value;
}

// limit the query after clicking the facet
function limitQuery (field, value)
{
	document.search.query.value += ' and ' + field + '="' + value + '"';
	onFormSubmitEventHandler();
}

// limit by target functions
function limitTarget (id, name)
{
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

function delimitTarget ()
{
	var navi = document.getElementById('pz2-navi');
	navi.innerHTML = '';
	curFilter = null; 
	resetPage();
	loadSelect();
	triggerSearch();
	return false;
}

function limitResults(name, kind) {
	var thisFilter = {'name': name, 'kind': kind};
	for (var index in filter) {
		if (filter[index] == thisFilter) {
			filter.push(thisFilter);
			redisplay();
			load();
			break;
		}
	}
}

function delimitResults(name, kind) {
	var thisFilter = {'name': name, 'kind': kind};
	for (var index in filter) {
		if (filter[index] == thisFilter) {
			filter.splice(index, 1);
			redisplay();
			load();
			break;
		}
	}

}

function redisplay() {
	
}



function drawPager (pagerDiv)
{
	//client indexes pages from 1 but pz2 from 0
	var onsides = 6;
	var pages = Math.ceil(totalRec / recPerPage);
	
	var firstClkbl = ( curPage - onsides > 0 ) 
		? curPage - onsides
		: 1;

	var lastClkbl = firstClkbl + 2*onsides < pages
		? firstClkbl + 2*onsides
		: pages;

	var prev = '<span class="pz2-prev">&#60;&#60; Prev</span><b> | </b>';
	if (curPage > 1)
		var prev = '<a href="#" class="pz2-prev" onclick="pagerPrev();">'
		+'&#60;&#60; Prev</a><b> | </b>';

	var middle = '';
	for(var i = firstClkbl; i <= lastClkbl; i++) {
		var numLabel = i;
		if(i == curPage)
			numLabel = '<b>' + i + '</b>';

		middle += '<a href="#" onclick="showPage(' + i + ')"> '
			+ numLabel + ' </a>';
	}
	
	var next = '<b> | </b><span class="pz2-next">Next &#62;&#62;</span>';
	if (pages - curPage > 0)
	var next = '<b> | </b><a href="#" class="pz2-next" onclick="pagerNext()">'
		+'Next &#62;&#62;</a>';

	predots = '';
	if (firstClkbl > 1)
		predots = '...';

	postdots = '';
	if (lastClkbl < pages)
		postdots = '...';

	pagerDiv.innerHTML += '<div style="float: clear">' 
		+ prev + predots + middle + postdots + next + '</div><hr/>';
}

function showPage (pageNum)
{
	curPage = pageNum;
	my_paz.showPage( curPage - 1 );
}

// simple paging functions

function pagerNext() {
	if ( totalRec - recPerPage*curPage > 0) {
		my_paz.showNext();
		curPage++;
	}
}

function pagerPrev() {
	if ( my_paz.showPrev() != false )
		curPage--;
}

// swithing view between targets and records

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
	// var oldRecId = curDetRecId;
	// curDetRecId = recId;
	
	// remove current detailed view if any
	var detRecordDiv = document.getElementById('det_'+ recId);
	// lovin DOM!
	if (detRecordDiv) {
		detRecordDiv.parentNode.removeChild(detRecordDiv);
	}
	else {
		my_paz.record(recordIDForHTMLID(recId));
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





function renderDetails(data, marker) {

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
		Loads XML journal info from ZDB via a proxy on our own server (to avoid cross-domain load problems).
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
						else {
							// Absence of an AccessURL implies this is inside PrintData.
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
			else if (targetURL.search(/z3950.gbv.de:20010\/subgoe_opc/) != -1) {
				catalogueURL = 'http://opac.sub.uni-goettingen.de/DB=1/CMD?ACT=SRCHA&IKT=1016&TRM=ppn+' + itemID;
			}

			if (catalogueURL) {
				var linkElement = document.createElement('a');
				linkElement.setAttribute('href', catalogueURL);
				linkElement.setAttribute('target', 'pz2-linktarget');
				linkElement.setAttribute('class', 'pz2-detail-catalogueLink')
				var linkText = localise('Ansehen und Ausleihen im Katalog:');
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


	return detailsDiv;
}




/* 	HTMLIDForRecordData
	input:	pz2 record data object
	output:	ID of that object in HTML-compatible form
			(replacing spaces by dashes)
*/
function HTMLIDForRecordData (recordData) {
	if (recordData.recid[0] !== undefined) {
		return recordData.recid[0].replace(/ /g,'+');
	}
}



/*	recordIDForHTMLID
	input:	record ID in HTML compatible form
	output:	input with dashes replaced by spaces
*/
function recordIDForHTMLID (HTMLID) {
	return HTMLID.replace(/\+/g,' ');
}

//EOF
 
