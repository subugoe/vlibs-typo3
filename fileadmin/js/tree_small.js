function cleanMark () {
	$("#mark").remove();
}

function renderNode( id ) {


			var node = $("#tree ul");
			//alert("making upLink");
			makeUpLink(id);
			//alert("upLink ready");

			
			//alert("registerTree: " + id);

			var sid = id.replace("-XX","?");

			//alert($sid);

 			$("#cat").html("<b>" + id + "</b>");

 			$("#msc").attr("value", sid);
			$.get("fileadmin/browsing/getnode_jq.php", { id: id },function(data){
			

				var newnode = $(data);

				//alert(data);

				$(newnode).children("ul").children("li").children("ul").remove();



				//$(newnode).find("a:first").attr("class", "minus");
				/*$(node).fadeOut("slow");
				$(node).replaceWith($('<ul></ul>').html(newnode));
				$(node).fadeIn("slow");
				*/
				$(node).replaceWith($('<ul></ul>').html(newnode));

				//reg2contract(id);
					$('#' + id).click( function (event) {
							event.preventDefault();
							
 							$("#msc").attr("value", id);
						
							$("#cat").html('<b>'+id+'</b>');



				});


				$('#' + id).children("ul").children("li").each(function() {


					//alert("ID: "+$(this).attr("id"));

					if ($(this).attr("id") == ""){
						

						//alert("No ID"+$(this).children("a.leaf").text());
						$(this).children("a.leaf").click(function(event){ 	
							event.preventDefault();
							 //$sid = id.replace("xx","?");
							cid = $(this).text();
							sid = cid.replace("xx","?");
							cleanMark();
							$(this).prepend('<span id="mark"> -> </span>');

							//alert($text);
							$("#cat").html('<b>'+cid+'</b>');

 							$("#msc").attr("value", sid);
     						});
					} else {

					registerNode($(this).attr("id"));
					//alert("ID");
					}
					

				

					

				});

				

			});

}

function registerUp (upid) {
	
	$("#parents").children("#up").children("a").click(function(event) {
		event.preventDefault();
		
		renderNode(upid);

	});

}

function makeUpLink( id ) {
			if (id.indexOf("xx") > -1) {upid = id.substr(0,2) + "-XX"; $("#parents").html('<span><a href="alternative-startseiten/browsing-alternative">MSC</a></span>&nbsp;&nbsp;->&nbsp;&nbsp;<span id="up"><a href="alternative-startseiten/browsing-alternative">' + upid + '</a></span>'); registerUp(upid);
			}
			else if (id.indexOf("00") > -1) {upid = id.substr(0,2) + "-XX"; $("#parents").html('<span><a href="alternative-startseiten/browsing-alternative">MSC</a></span>&nbsp;&nbsp;->&nbsp;&nbsp;<span id="up"><a href="alternative-startseiten/browsing-alternative">' + upid + '</a></span>'); registerUp( upid);
			}
			else if (id.indexOf("XX") > -1) { $("#parents").html('<span><a href="alternative-startseiten/browsing-alternative">MSC</a></span>');}
			
			

}

function registerNode ( id ) {

		

		$('#' + id).children("a.plus").click(function(event){



			//on click...

			event.preventDefault();
			//alert(id);
							$("#cat").html('<b>'+id+'</b>');

 							$("#msc").attr("value", id);			
			//var id = $(this).text();
			renderNode(id);


 			



			//fetch document

			//$.get("fileadmin/browsing/browsing.php", { id: id },function(data){

 		});	



}


$(document).ready(function(){

//registerTree();
$("#tree ul").children("li").each( function() {
//alert($(this).attr("id"));
registerNode($(this).attr("id"));

});






});