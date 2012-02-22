$(document).ready(function(){

	$('#all').html('<img src="fileadmin/images/plus.png" alt="plus" />');
	$('#no').html('<img src="fileadmin/images/minus.png" alt="minus" />');
	$('#all').css('cursor', 'pointer');
	$('#no').css('cursor', 'pointer');


	

	//print button
//	$('#print').click(function(event){
  //              event.preventDefault();
		
//	});	


	
	       var sf = $('input[name*="searchField"]').attr('value');
	//alert('HUHU');
       $('.tabs li').each(function() {
                //$('.currentTab').removeAttr('class');
		$(this).children('a').each(function() {
		//alert($(this).attr('href'));
		//var act = $('a[href$=sf]');
		var href = $(this).attr('href');
                if (href.indexOf(sf) > 0){
                     $('.currentTab').removeAttr('class');
                     $(this).attr('class', 'currentTab');

	        }
		
        	});
	});



	$('.tabs').children('li').each(function() {
		$(this).children('a').click(function(event){	
			event.preventDefault();
			$('.currentTab').removeAttr('class');
			$(this).attr('class', 'currentTab');
			$('label:first').text($(this).text());
			var addr = $(this).attr('href');
			var pos = addr.indexOf("searchField=");
			var field = addr.substring(pos+12, addr.length);
			//alert(field);
			$('input[name*="searchField"]').attr('value',field);

		});
	});


	$('#all').click(function(event){	
		//event.preventDefault();
		$(".free").find("input[type*='checkbox']").each(function(){
                	this.checked = true;
        	});

	});

	$('#no').click(function(event){	
		event.preventDefault();
		$(".free").find("input[type*='checkbox']").each(function(){
                	this.checked = false;
        	});	

	});
	
       var term1 = 'Suchbegriff eingeben';
       var term2 = 'Please insert your search terms';	
	if ($("#searchTerm").val() == term1){

		$("#searchTerm").attr({ value: term1 }).focus(function(){
        	    if($(this).val()== term1){
               	    $(this).val("");
            	}
       		
      		 });
	}

	if ($("#searchTerm").val() == term2){
		$("#searchTerm").attr({ value: term2 }).focus(function(){
	            if($(this).val()== term2){
        	    $(this).val("");
           	 }
       		});

	}


});


