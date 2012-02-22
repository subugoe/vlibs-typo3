$(document).ready(function(){

	$("h1.standard").after('<h2 class="searchingIn">beta version</h2>');

	//modify searchforms	
	//default seachfield is fulltext
	if ($(".checked").length < 1) {
		$("#ft").css("checked","checked");
	}

	$("input[name='sf']").each(function(){
	  var id = $(this).attr("id");

	  var par = $(this).parent();
	   var active = $(this).is(':checked');	
	   var newNode = $("<span>" + $(par).text() + "</span>");
	   $(newNode).attr("id",id);
	   if ($(this).is(':checked')) {$(newNode).attr("class", "currentTab");}
	   
	    $(par).replaceWith($(newNode));
	    register(id);
	});	


	$("#sort").attr("value", "");
	$("#sort").css("border","none");
	$("#sort").css("background", "transparent");

	//handle searchfield
	$('.submStart').click( function() {
		modifyQuery();
		//$('.submStart').trigger('submit');
                 //   return true;

	  
	});

	//trigger sort on radio buttons and checkboxes
	$(":radio").click( function() { 
		$("#sort").trigger('click');
		return true;
	});

	$(":checkbox").click( function() {
                $("#sort").trigger('click');
                return true;
        });


});

function register(id) {
	

	$('#' + id).css('cursor', 'pointer');
	$('#' + id).click(function(event){
		  activate(id);
      
	});



}

function activate(id) {
	$(".tabs li span").each( function(){
		$(this).removeAttr("class");	
			
	});
	$('#' + id).attr("class", "currentTab");
}

function modifyQuery() {
		  if ($(".currentTab").attr("id") == "ft") {
		     if($("input[name='q']").attr("value").indexOf('fulltext:') < 0) {
                        var query ="fulltext:" + $("input[name='q']").attr("value");
                        $("input[name='q']").attr("value", query);
		     }
			
                }
}
