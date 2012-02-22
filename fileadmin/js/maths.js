var term="";
var refresh = false;

function ow(name) {
	var url = "fileadmin/js/ow-proxy.php";
	var param = name;
	$("#owRequest").html('Loading data ...' );
	$.ajax({
		url: url,
		data:{ person: param },
		dataType:"html",
		timeout: 3000,
		success:function(data){
			$("#owRequest").replaceWith( data );
		},
		 error:function(xhr,err,e){
			var message;
			if (err == 'timeout') {
				message="Sorry, database not available."
			}
			else {
			//	alert(err);
			//	alert(e);
				message = err;
			}
			$("#owRequest").replaceWith('<span>' + message + '</span>');
		}
	});
}



function genealogy(name){
	var param = name;
	$("#genResult").html('Loading data ...' );
	$.ajax({
		url:"fileadmin/js/gen-proxy.php",
		data:{ name: param },
		dataType:"html",
		timeout: 8000,
		success:function(data){$("#genResult").html( data ); return;},
		error:function(xhr,err,e){
			var message;
			if (err == 'timeout') {
				message="Sorr, database not available."
			}
			else {
				message = err;
			}
			$("#genResult").html('<span>' + message + '</span>');
			return;
		}
	});
}


function mactut(name){
	var param = name;
	$.ajax({
		url:"fileadmin/js/mactut-search.php",
		data:{ person: param },
		dataType:"html",
		timeout: 2000,
		success:function(data){$('#mactutResult').html(data) },
		error:function(xhr,err,e){
			alert(err);
			var message;
			if (err == 'timeout') {
				message="Die Datenbank ist zur Zeit leider nicht verfügbar."
			}
			else {
				message = err;
			}
			$("#macutResult").html('<span>' + message + '</span>');
		}
	});
}


function mycarousel_itemLoadCallback(carousel, state) {
	if (!refresh) {
		if (carousel.has(carousel.first, carousel.last)) {
			return;
		}
	}
	jQuery.get(
		'fileadmin/js/ow-proxy.php',
		{
			person: term,
			first: carousel.first,
			last: carousel.last
		},
		function(xml) {
			mycarousel_itemAddCallback(carousel, carousel.first, carousel.last, xml);
			//alert(carousel.first);
			//alert(carousel.last);
		},
		'xml'
	);
	refresh = false;
};

function mycarousel_callback(carousel, state) {
	carousel.reload();
}


function mycarousel_itemAddCallback(carousel, first, last, xml) {
	// Set the size of the carousel
	carousel.size(parseInt(jQuery('total', xml).text()));
	jQuery('link', xml).each(function(i) {
		carousel.add(first + i, jQuery(this).text());
	});
};

/**
 * Item html creation helper.
 */
function mycarousel_getItemHTML(url) {
	return '<img src="' + url + '" height="110" alt="" />';
};


jQuery(document).ready(function() {

    if (!$.browser.safari) {
	$("#simpleSearch").submit(function(event) {
		event.preventDefault();
		$('#mycarousel').html('<ul></ul>');
		refresh = true;
		term = $("input[name='person']").val();
		//mactut(term);
		//alert("TERM: " +term);
		jQuery('#mycarousel').jcarousel({
			scroll: 1,
			visible: 3,
			size: 8,
			start:1,
			//initCallback: init,
			//initCallback: mycarousel_callback
			itemLoadCallback: mycarousel_itemLoadCallback
		});
		genealogy(term);
		// mactut(term);
	});
    }
});
