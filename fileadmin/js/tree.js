function cleanMark () {
	$("#mark").remove();
}
function registerTree () {

		

		$("a.plus").click(function(event){



			//on click...

			event.preventDefault();

			

			var node = $(this).parent();

			var id = $(this).text();

			//alert("registerTree: " + id);

			
			var sid = id.replace("-XX","?");

 			$("#cat").html("<b>" + id + "</b>");

 			$("#msc").attr("value", sid);

 			



			//fetch document

			$.get("fileadmin/browsing/browsing.php", { id: id },function(data){

			

				var newnode = $(data);



				$(newnode).children("ul").children("li").children("ul").remove();



				$(newnode).find("a:first").attr("class", "minus");

				$(node).replaceWith(newnode);

				reg2contract(id);

				

				$('#' + id).children("ul").children("li").each(function() {

					//alert("ID: "+$(this).attr("id"));

					if ($(this).attr("id") == ""){
						
						
						//alert("No ID"+$(this).children("a.leaf").text());
						$(this).children("a.leaf").click(function(event){ 	
							event.preventDefault();
							//alert($(this).text());
							
							$("#cat").html('<b>'+$(this).text()+'</b>');

 							$("#msc").attr("value", $(this).text());
							cleanMark();
							$(this).prepend('<span id="mark"> -> </span>');

     						});
					} else {

					reg2expand($(this).attr("id"));
					//alert("ID");
					}
				

				

					

				});

				

			});

 		});	

	

}



function reg2expand(id) {



$('#' + id).children("a.plus").each(function() {



		$(this).click(function(event){



		//on click...

			event.preventDefault();

			//alert("reg2expand: " + id);

			var node = $(this).parent();



			var sid = id.replace("xx","?");


			$("#cat").html('<b>'+id+'</b>');

 			$("#msc").attr("value", sid);

			

			//fetch document

			$.get("fileadmin/browsing/browsing.php", { id: id },function(data){

				

				var newnode = $(data);



				$(newnode).children("ul").children("li").children("ul").remove();



				$(newnode).find("a:first").attr("class", "minus");

				$(node).replaceWith(newnode);

				



				reg2contract(id);

				$('#' + id).children("ul").children("li").each(function() {

					if ($(this).attr("id") == ""){
						
						
						//alert("No ID"+$(this).children("a.leaf").text());
						$(this).children("a.leaf").click(function(event){ 	
							event.preventDefault();
							//alert($(this).text());
							$("#cat").html('<b>'+$(this).text()+'</b>');

 							$("#msc").attr("value", $(this).text());
							cleanMark();
							$(this).prepend('<span id="mark"> -> </span>');
     						});
					} else {

					reg2expand($(this).attr("id"));
					//alert("ID");
					}
				

					

				});

				

	

				

				

			});

		

		

		});

	//});



});





}



function reg2contract(id) {





$('#' + id).children("a.minus").click(function(event){



		//on click ...

			event.preventDefault();

			var node = $(this).parent();

			$("#cat").html("");

			$("#msc").attr("value", id);

			$(node).find("a:first").attr('class', 'plus');

			

			$(node).find("ul").slideUp("medium",function(){



				$(this).remove();

			});

			

			reg2expand(id);

			

 		});



}







$(document).ready(function(){





registerTree();







});