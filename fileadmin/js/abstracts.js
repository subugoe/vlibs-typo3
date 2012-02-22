$(document).ready(function(){

  $("td.data em").bind("mouseover", function() {
	var abstract = $(this).siblings(".hide").html();

	if (abstract != null) {
	var position = $(this).offset();
	$("#abstract").css( "top" , position.top-80 );
	$("#abstract").html(abstract);
	}

  });

  $("#abstract").bind("mouseout", function() { 
	$("#abstract").text('');
  });

})


function show_info(id) 
{
	if (document.getElementById(id).style.display == 'none')
	{
		document.getElementById(id).style.display='block';
	}
	else
	{
		document.getElementById(id).style.display='none';
	};
};