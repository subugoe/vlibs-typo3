$(document).ready(function(){
	$('#all').css('cursor', 'pointer');
	$('#no').css('cursor', 'pointer');




	$('#advancedSearch  .advTabs').children('li').each(function() {
		$(this).children('a').click(function(event){	
			event.preventDefault();
                        var liParent = $(this).parent().get(0);
			//alert($(liParent).children('a.currentTab').text());
			$(liParent).siblings().children('a.currentTab').each(function() {
				$(this).removeAttr('class');

			});
			$(this).attr('class', 'currentTab');
			$('label:first').text($(this).text());
			var addr = $(this).attr('href');
			var pos = addr.indexOf("sf");
			var field = addr.substring(pos+4, addr.length);
			//alert(ulParent);
			$(this).parents('div.advFirst').find('input[name*="searchField"]').attr('value',field);

		});
	});

	$('#all').click(function(event){	
	//	event.preventDefault();
		$(".free").find("input[type*='checkbox']").each(function(){
                	this.checked = true;
        	});

	});

	$('#no').click(function(event){	
	///	event.preventDefault();
		$(".free").find("input[type*='checkbox']").each(function(){
                	this.checked = false;
        	});	

	});

});


