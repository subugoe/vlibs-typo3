/* Adapted from Indexdata's js-client.js by ssp */

/* A very simple client that shows a basic usage of pz2.js */

/* Create a parameters array and pass it to the pz2's constructor.
 Then register the form submit event with the pz2.search function.
 autoInit is set to true on default.
*/
var usesessions = true;
var pazpar2path = '/pazpar2/search.pz2';
var showResponseType = '';
var termListNames = ["xtargets", "medium", "author", "subject", "date"];
if (document.location.hash == '#useproxy') {
    usesessions = false;
    pazpar2path = '/service-proxy/';
    showResponseType = 'json';
}

var germanTerms = {
	"xtargets": "Kataloge",
	"medium": "Dokumententyp",
	"author": "Autoren",
	"subject": "Themengebiete",
	"date": "Jahre",
};

var localisations = {
	"de": germanTerms,
};

function localise(term) {
	return localisations["de"][term];
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
var termListMax = {"xtargets": 16, "medium": 6, "author": 10, "subject": 10, "date": 6 };


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
		html.push('<li id="recdiv_' + hit.recid + '" >'
        	+ '<a href="#" id="rec_' + hit.recid
        	+ '" onclick="showDetails(this.id);return false;">'
			+ '<span class="pz-item-title">'+ hit["md-title"] + '</span>'); 
		if (hit["md-title-remainder"] !== undefined) {
			html.push(' <span class="pz-item-title-remainder">' 
				+ hit["md-title-remainder"] + '.</span>');
		}
		if (hit["md-title-responsibility"] !== undefined) {
		 	html.push(' <span class="pz-item-responsibility">'
				+ hit["md-title-responsibility"] + '</span>');
		}
		if (hit["md-date"] !== undefined) {
			html.push(', <span class="pz-item-date">'
				+ hit["md-date"] + '</span>');
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
			'<h5>', localise(type), '</h5><ol>'];
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
    var detRecordDiv = document.getElementById('det_'+data.recid);
    if (detRecordDiv) return;
    curDetRecData = data;
    var recordDiv = document.getElementById('recdiv_'+curDetRecData.recid);
    var html = renderDetails(curDetRecData);
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
    document.search.onsubmit = onFormSubmitEventHandler;
    document.search.query.value = '';
    document.select.sort.onchange = onSelectDdChange;
    document.select.perpage.onchange = onSelectDdChange;
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
function showDetails (prefixRecId) {
    var recId = prefixRecId.replace('rec_', '');
    var oldRecId = curDetRecId;
    curDetRecId = recId;
    
    // remove current detailed view if any
    var detRecordDiv = document.getElementById('det_'+oldRecId);
    // lovin DOM!
    if (detRecordDiv)
      detRecordDiv.parentNode.removeChild(detRecordDiv);

    // if the same clicked, just hide
    if (recId == oldRecId) {
        curDetRecId = '';
        curDetRecData = null;
        return;
    }
    // request the record
    my_paz.record(recId);
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

function renderDetails(data, marker)
{
    var details = '<div class="pz2-details" id="det_'+data.recid+'"><table>';
    if (marker) details += '<tr><td>'+ marker + '</td></tr>';
    if (data["md-title"] != undefined) {
        details += '<tr><td><b>Title</b></td><td><b>:</b> '+data["md-title"];
  	if (data["md-title-remainder"] !== undefined) {
	      details += ' : <span>' + data["md-title-remainder"] + ' </span>';
  	}
  	if (data["md-title-responsibility"] !== undefined) {
	      details += ' <span><i>'+ data["md-title-responsibility"] +'</i></span>';
  	}
 	  details += '</td></tr>';
    }
    if (data["md-date"] != undefined)
        details += '<tr><td><b>Date</b></td><td><b>:</b> ' + data["md-date"] + '</td></tr>';
    if (data["md-author"] != undefined)
        details += '<tr><td><b>Author</b></td><td><b>:</b> ' + data["md-author"] + '</td></tr>';
    if (data["md-electronic-url"] != undefined)
        details += '<tr><td><b>URL</b></td><td><b>:</b> <a href="' + data["md-electronic-url"] + '" target="_blank">' + data["md-electronic-url"] + '</a>' + '</td></tr>';
    if (data["location"][0]["md-subject"] != undefined)
        details += '<tr><td><b>Subject</b></td><td><b>:</b> ' + data["location"][0]["md-subject"] + '</td></tr>';
    if (data["location"][0]["@name"] != undefined)
        details += '<tr><td><b>Location</b></td><td><b>:</b> ' + data["location"][0]["@name"] + " (" +data["location"][0]["@id"] + ")" + '</td></tr>';
    details += '</table></div>';
    return details;
}
 


//EOF
 

