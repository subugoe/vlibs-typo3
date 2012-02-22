$(document).ready(function(){




	$("#textnav").click(function(event){

		//on click...
			event.preventDefault();

		
			//alert(textnav.text());
			$("div.btn img").each(function() {

				$(this).fadeOut("slow", function() {
					var text = $(this).attr("alt");
					//lert(text);
					$(this).css("display", "none");
					$(this).replaceWith(text);
					$(this).fadeIn("slow");
				});
				

			});
			//$($textnav).children("div.btn a").attr("alt")

	});


});