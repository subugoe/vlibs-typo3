function getSize() {
   var myWidth = 0, myHeight = 0;
 
    if( typeof( window.innerWidth ) == 'number' ) {
       //Non-IE
      myWidth = window.innerWidth;
      myHeight = window.innerHeight;
   } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
      //IE 6+ in 'standards compliant mode'
       myWidth = document.documentElement.clientWidth;
       myHeight = document.documentElement.clientHeight;
   } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
      //IE 4 compatible
        myWidth = document.body.clientWidth;
      myHeight = document.body.clientHeight;
   }
   return [ myWidth, myHeight ];
}


function tooltip()
{

  $(".abstrCont").bind("mouseover", function(event){  
	$(this).css('cursor', 'help');

 
      var text = $(this).children(".abstr").text();
      // X- und Y-Koordinaten des Cursor ermitteln
      x = event.pageX-50;
	
      y = event.pageY-120;
     // alert(x +  "  " + y); 
      // ein DIV für den Tooltip erstellen
      $("body").append("<div id='tooltip'>"+text+"</div>");
      //alert($("#tooltip").text()); 
      // das erstellte DIV mit CSS positionieren
      $("#tooltip").css("left", "4.5em");
      $("#tooltip").css("top", y);  
  });
  

  $(".abstrCont").bind("mouseout", function(event){

      $("#tooltip").remove();
	
  });

};

function registerBS(node) {
                $(node).click(function(event){
                event.preventDefault();
                //alert($(this).html());
                var param= $(this).attr("href");
                var url = "fileadmin/js/iport-proxy.php"
                $start = param.indexOf("?");
                param = param.substring($start+1);

		   $.ajax({

                	url: url,
                	data:{ save: param },
                	dataType:"html",
                	success:function(data){
			$(node).find("img").attr("src", "typo3conf/ext/metasuchexml/res/bookshelf_ok.png");
                	},
               		 error:function(xhr,err,e){ alert( "Error: " + err ); }
         		});
        
                });


}
function registerSW(node, stored) {
                $(node).click(function(event){
                event.preventDefault();
                var old = $(stored).html();
		var par = $(node).parent();
		$(par).html(old);
		$(par).children().children("a.title").click(function(event) {
			        event.preventDefault();
        			fullRecord($(this));
	
		});

                });


}


function registerDL(node){
                $(node).click(function(event){
                event.preventDefault();
                var param= $(this).attr("href");
                var url = "fileadmin/js/iport-proxy.php"
                $start = param.indexOf("?");
                param = param.substring($start+1);

                   $.ajax({

                        url: url,
                        data:{ download: param },
                        dataType:"html",
                        success:function(data){
                             window.open(data);
                        //replace icon
                        },
                         error:function(xhr,err,e){ alert( "Error: " + err ); }
                        });

                });


}
function fullRecord(elem){
var cell = $(elem).parent().parent();
var url = "fileadmin/js/iport-proxy.php";
var query  = $(elem).attr("href");
var start = query.indexOf('i&');
params = query.substring(start+2);

var store = $(cell).html();
$(cell).html('<span class="status">loading data ...</span>' );
$.ajax({
      url:url,
      data:{ record:params },
      dataType:"html",
      success:function(data){$(cell).html( data ); $(cell).prepend('<div class="store">' + store + '</div>');registerBS($(cell).children('.record').children("a.save"));registerSW($(cell).children('.record').children('a:first'),$(cell).children(".store")); },
     
      error:function (xhr, ajaxOptions, thrownError){
		 alert(xhr.responseText );
		}
          }); // $.ajax()



}

function ow() {
var url = "fileadmin/js/ow.php";
var ajaxparam = $.trim($("#gen").text());
$.ajax({

                        url: url,
                        data:{ person: ajaxparam },
                        dataType:"html",
			timeout: 3000, 
                        success:function(data){
                            $("#owWrapper").replaceWith(data);
                        },
                         error:function(xhr,err,e){ 
				var message;
				if (err == 'timeout') {
				  message="Sorry, database not available."
				}
				else {
				  message = err;
				}
			    $("#owWrapper").replaceWith('<span>' + message + '</span>');		
			}
});
}



function genealogy(id){
	var param = $.trim($("#"+id).text());
	
$.ajax({
     
      url:"fileadmin/js/gen.php",
      data:{ name: param },
      dataType:"html",
      timeout: 5000, 
      success:function(data){ if(data.length > 290) {$("#"+id).html( data )} else {$("#"+id).html( 'No item found' )}},
      error:function(xhr,err,e){
          var message;
          if (err == 'timeout') {
            message="Sorry, database not available."
          }
          else {
            message = err;
         }
         $("#"+id).html('<span>' + message + '</span>');

      }
    }); 
}


function saveRecord(node){
     var param= $(node).attr("href");
     var url = "fileadmin/js/iport-proxy.php"
     $start = param.indexOf("?");
     param = param.substring($start+1);
     alert(param);

        $.ajax({

      		url: url,
      		data:{ save: param },
      		dataType:"html",
      		success:function(data){
	
			 //replace icon
		},
      		error:function(xhr,err,e){ alert( "Error: " + err ); }
     	 }); 
	
	

}



$(document).ready(function(){


$("div.abstrCont").each(function(){
                        $(this).css("display", "inline");
                });
tooltip();

if ($("#gen").length) {
	ow();
	genealogy("gen");
	

}


function register(node) {


	        $(node).click(function(event){
		event.preventDefault();
		alert($(this).html());
	     	var param= $(this).attr("href");
    	     	var url = "fileadmin/js/iport-proxy.php"
             	$start = param.indexOf("?");
             	param = param.substring($start+1);
              	alert(param);

 
	  
		});
}

	$('a.title').click(function(event){


		event.preventDefault();
		fullRecord($(this));
	});


});


