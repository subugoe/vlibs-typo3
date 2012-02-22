$(document).ready(function(){

	renderSearchFields();	

	function renderSearchFields() {
       	   //workaround for browser back
	   var name;
	   var sf;
	   var sfvalue;
	   var href;

           for (i=1; i < 4; i++){
                name = "searchField" + i;
                sf = $('input[name*=' + name + ']');
                sfvalue = $('input[name*=' + name + ']').attr('value');
		
                $(sf).siblings('ul').children('li').each(function() {
                        $(this).children('a').each(function() {
                                href = $(this).attr('href');
                                if (href.indexOf(sfvalue) > 0){
                                   $(this).attr('class', 'currentTab');

                                } else {
				   $(this).removeAttr('class');
				}	
				
			

                        });
                });
		
           }
	}




	$('.dbGroup').after('<span class="all"><img src="fileadmin/images/plus.png" alt="plus" /></span> / <span class="no"><img src="fileadmin/images/minus.png" alt="minus" /></span>');

	$('.all').css('cursor', 'pointer');
	$('.no').css('cursor', 'pointer');

       $('.all').click(function(event){
		
		$(this).siblings("input[type*='checkbox']").each(function(){
                        this.checked = true;
                });

	
       });

       $('.no').click(function(event){
                $(this).siblings("input[type*='checkbox']").each(function(){
                        this.checked = false;
                });


       });

        $('#all').html('<img src="fileadmin/images/plus.png" alt="plus" />');
        $('#no').html('<img src="fileadmin/images/minus.png" alt="minus" />');


	$('#all').css('cursor', 'pointer');	
	$('#no').css('cursor', 'pointer');

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

        $('#advancedSearch  .advTabs').children('li').each(function() {
                $(this).children('a').click(function(event){
                        event.preventDefault();
                        var liParent = $(this).parent().get(0);
                        //alert($(liParent).children('a.currentTab').text());
                        $(liParent).siblings().children('a.currentTab').each(function() {
                                $(this).removeAttr('class');

                        });
                        $(this).attr('class', 'currentTab');
                        //$('label:first').text($(this).text());
                        var addr = $(this).attr('href');
                        var pos = addr.indexOf("sf");
                        var field = addr.substring(pos+4, addr.length);
                        //alert(ulParent);
                        $(this).parents('div.advFirst').find('input[name*="searchField"]').attr('value',field);

                });
        });


});


