function ow() {
var url = "fileadmin/js/ow.php";
var ajaxparam = "kalman";
$.ajax({

                        url: url,
                        data:{ person: ajaxparam },
                        dataType:"html",
                        success:function(data){
                            alert("Success");
                            alert(data);
                            $("#owWrapper").replaceWith(data);
                        //replace icon
                        },
                         error:function(xhr,err,e){ alert( "Error: " + err ); }
                        });


}


$(document).ready(function(){

	/*$.ajax({
      url:'http://134.76.160.80/fileadmin/js/ow.php',
      data:{ person:"kalman" },
      dataType:"xml",
      success: alert(data);},
     
      error:function (xhr, ajaxOptions, thrownError){
		 alert(xhr.responseText );
		}
          }); // $.ajax()

*/
//alert($("#owWrapper").text());

/*
$.ajax({
    url: 'fileadmin/js/ow.php',
    type: 'GET',
    dataType: 'xml',
    data: {person: 'kalman'},
    timeout: 1000,
    error: function(){
        alert('Error loading XML document');
    },
    success: function(data){
        alert(data);
    }
});
*/
/*
$.get(
	    "fileadmin/js/ow.php",
	    {person:"kalman"},
	    function(data) { $("#owWrapper").html(data); alert(data); },
	    "xml"
	);
*/



var url = "fileadmin/js/ow.php";
var ajaxparam = "kalman";
alert($("#owWrapper").html());
$.ajax({

                        url: url,
                        data:{ person: ajaxparam },
                        dataType:"html",
                        success:function(data){
			    alert("Success");
                            alert(data);
			    $("#owWrapper").replaceWith(data);
                        //replace icon
                        },
                         error:function(xhr,err,e){ alert( "Error: " + err ); }
                        });




});
