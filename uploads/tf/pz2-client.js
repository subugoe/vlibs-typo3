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
	'detail-label-author': 'Autor',
	'detail-label-date': 'Jahr',
	'detail-label-isbn': 'ISBN',
	'detail-label-medium': 'Art',
	'detail-label-description': 'Infos',
	'detail-label-series-title': 'Reihe',
	'detail-local-label-isbn': 'ISBN',
	'detail-local-label-id': 'PPN',
	'link': '[Link]',
	'Kataloge': 'Kataloge',
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
                    "showtime": 500,            //each timer (show, stat, term, bytarget) can be specified this way
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
var recPerPage = 90;
var totalRec = 0;
var curDetRecId = '';
var curDetRecData = null;
var curSort = 'date';
var curFilter = null;
var submitted = false;


//
// pz2.js event handlers:
//
function my_oninit() {
    my_paz.stat();
    my_paz.bytarget();
}


function my_onshow(data) {
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
  
    var html = ['<ol start="' + (1 + recPerPage * (curPage - 1)) + '">'];
    for (var i = 0; i < data.hits.length; i++) {
        var hit = data.hits[i];
		html.push('<li id="recdiv_' + HTMLIDForRecordData(hit) + '" >'
        	+ '<a href="#" class="pz2-recordLink" id="rec_' + HTMLIDForRecordData(hit)
        	+ '" onclick="toggleDetails(this.id);return false;">'
			+ '<span class="pz2-item-title">'+ hit["md-title"] + '</span>'); 
		if (hit['md-title-remainder'] !== undefined) {
			html.push(' <span class="pz2-item-title-remainder">' 
				+ hit['md-title-remainder'] + '.</span>');
		}
		if (hit['md-title-responsibility'] !== undefined) {
		 	html.push(' <span class="pz2-item-responsibility">'
				+ hit['md-title-responsibility'] + '</span>');
		}
		else if (hit['md-title-author'] !== undefined) {
			html.push(' <span class="pz2-item-responsibility">'
				+ hit['md-title-author'] + '</span>');
		}
		if (hit['md-date'] !== undefined) {
			html.push(', <span class="pz2-item-date">'
				+ hit['md-date'] + '</span>.');
		}
		if (hit.recid == curDetRecId) {
			html.push(renderDetails(curDetRecData));
		}
		html.push('</a></li>');
    }
   	html.push('</ol>');
    replaceHtml(results, html.join(''));
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
        	+ ' target_id=' + terms[i].id
        	+ ' onclick="limitTarget(this.getAttribute(\'target_id\'), this.firstChild.nodeValue);return false;">'
        	+ terms[i].name 
        	+ ' <span class="count">(' + terms[i].freq + ')</span>'
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
    var html = renderDetails(curDetRecData).join('');
    recordDiv.innerHTML += html;
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

    // if the same clicked, just hide
 //   if (recId == oldRecId) {
   //     curDetRecId = '';
     //   curDetRecData = null;
//        return;
  //  }
    // request the record
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
	var detailLine = function (title, information) {
		var rowMarkup = [];
		rowMarkup.push('<tr class="pz2-detail-', title, '"><th>', 
			localise('detail-label-'+title), ':</th><td>');
		if (information.length == 1) {
			rowMarkup.push(information[0]);
		}
		else {
			rowMarkup.push('<ul>');
			for (var itemNumber in information) {
				rowMarkup.push('<li>' + information[itemNumber] + '</li>');
			}
			rowMarkup.push('</ul>');
		}
		rowMarkup.push('</td></tr>');
		return rowMarkup.join('');
	}	

	var detailLineAuto = function (title) {
		// check whether metadata for type title exist
		if ( data['md-' + title] !== undefined ) {
			// build row with class / localised title / data
			return detailLine( title, data['md-' + title] );
		}
		return '';
	} 

	var locationDetails = function () {
		var addInfoItem = function (fieldName) {
			var value = location['md-'+fieldName];
			if ( value !== undefined ) {
				var title = '';
				var labelString = 'detail-local-label-'+fieldName
				var localisedLabelString = localise(labelString);
				if ( localisedLabelString != labelString ) {
					title = '<span class="title">' + localisedLabelString + ':</span> ';
				}  
				localInfoItems.push('<span class="pz2-local-"' + fieldName + '">'
					+ title + value.join(', ') + '</span>');
			}
		}
	
		var markup = ['<tr><th>', localise('Kataloge'), 
			':</th><td><dl class="pz2-locations">'];

		for ( var locationNumber in data.location ) {
			var localInfoItems = []

			var location = data.location[locationNumber];
			var localURL = location['@id'];
			var localName = location['@name'];

			markup.push('<dt class="pz2-location" title="' + localURL + '">' 
				+ localName + '</dt><dd>');

			addInfoItem('publication-name');
			addInfoItem('publication-place');
			addInfoItem('publication-year');
			addInfoItem('series-title');
			addInfoItem('isbn');

			
			// electronic resources
			var localElectronicURLs = location['md-electronic-url'];
			var localElectronicTexts = location['md-electronic-text'];

			if ( localElectronicURLs !== undefined ) {
				var URLMarkup = ['<span class="pz2-links">'];
				var links = []
				if ( localElectronicTexts !== undefined 
					&& localElectronicURLs.length == localElectronicTexts.length ) {
					for ( var URLNumber in localElectronicURLs ) {
						links.push('<a target="pz2-detail-tab" href="' 
							+ localElectronicURLs[URLNumber] + '">' 
							+ localElectronicTexts[URLNumber] + '</a>');
					}
				}
				else {
					for ( var URLNumber in localElectronicURLs ) {
						links.push('<a target="pz2-detail-tab" href="' 
							+ localElectronicURLs[URLNumber] + '">' 
							+ localise('link') + '</a>');
					}
				}
				URLMarkup.push(links.join(', '));
				URLMarkup.push('</span>');
				localInfoItems.push(URLMarkup.join(''));
			}

			addInfoItem('id');

			markup.push(localInfoItems.join('; '));
			markup.push('.</dd>');	
		}
		markup.push('</dl></td></tr>');
		return markup.join('');
	}



	var detailsHTML = ['<div class="pz2-details" id="det_', 
		HTMLIDForRecordData(data), '"><table>'];
	if (marker) {
		detailsHTML.push('<tr class="pz2-detail-' + marker + '"><td>'
			+ marker + '</td></tr>');
	}

/*
	if (data['md-title'] !== undefined) {
		var titleDetail = data['md-title'];
		if (data['md-title-remainder'] !== undefined) {
			titleDetail += ' : <span class="pz2-title-remainder">' 
				+ data['md-title-remainder'] + '</span>';
		}
	  	if (data['md-title-responsibility'] !== undefined) {
	    	titleDetail += ' / <span class="pz2-item-responsibility">'+ data['md-title-responsibility'] + '</span>';
	  	}
		detailsHTML.push( detailLine('title', titleDetail) );
	}
*/
	detailsHTML.push( detailLineAuto('author') );
//	detailsHTML.push( detailLineAuto('date') );
	detailsHTML.push( detailLineAuto('description') );
	detailsHTML.push( detailLineAuto('isbn') );
	detailsHTML.push( detailLineAuto('medium') );
	detailsHTML.push( locationDetails() );

    if (data['md-electronic-url'] !== undefined) {
		var link = '<a href="' + data['md-electronic-url'] + '>' + data['md-electronic-url'] + '</a>';
		detailsHTML.push( detailLine('URL', link) );
	}

/*
    if (data["location"][0]["md-subject"] != undefined)
        details += '<tr><td><b>Subject</b></td><td><b>:</b> ' + data["location"][0]["md-subject"] + '</td></tr>';
*/
/*
    if (data["location"][0]["@name"] != undefined)
        details += '<tr><td><b>Location</b></td><td><b>:</b> ' + data["location"][0]["@name"] + " (" +data["location"][0]["@id"] + ")" + '</td></tr>';
    details += '</table></div>';
*/

    return detailsHTML;
}


/* 	Input: pz2 record data object
	Output: ID of that object in HTML-compatible form
			(replacing spaces by dashes)
*/
function HTMLIDForRecordData (recordData) {
	if (recordData.recid[0] !== undefined) {
		return recordData.recid[0].replace(/ /g,'-');
	}
}

/*	Input: Record ID in HTML compatible form
	Output: input with dashes replaced by spaces
*/
function recordIDForHTMLID (HTMLID) {
	return HTMLID.replace(/-/g,' ');
}

//EOF
 

