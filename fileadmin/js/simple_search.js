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
      //alert(text); 
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

 
 /* $("span.abstr").bind("mouseout", function(){


      // das DIV beim verlassen des Events entfernen
      $("#tooltip").remove();
    });
  
*/
};

function registerBS(node) {
		//alert("REGISTERING...");
                $(node).click(function(event){
                event.preventDefault();
                //alert($(this).html());
                var param= $(this).attr("href");
                var url = "fileadmin/js/iport-proxy.php"
                $start = param.indexOf("?");
                param = param.substring($start+1);
                //alert(param);

		   $.ajax({

                	url: url,
                	data:{ save: param },
                	dataType:"html",
                	success:function(data){
                        $(node).text("OK");
                        //replace icon
                	},
               		 error:function(xhr,err,e){ alert( "Error: " + err ); }
         		});
        
                });


}
function registerSW(node, stored) {
                //alert("REGISTERING...");
                $(node).click(function(event){
                event.preventDefault();
                //alert($(this).html());
                var old = $(stored).html();
		//alert(old);
                //$(node).html(old);
		var par = $(node).parent();
		$(par).html(old);
		//alert($(par).children().children(".title").text());
		$(par).children().children("a.title").click(function(event) {
			        event.preventDefault();
        			fullRecord($(this));
	
		});

                });


}


function registerDL(node){
                $(node).click(function(event){
                event.preventDefault();
                //alert($(this).html());
                var param= $(this).attr("href");
                var url = "fileadmin/js/iport-proxy.php"
                $start = param.indexOf("?");
                param = param.substring($start+1);
                //alert(param);

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
//alert($(elem).attr("href"));	
var cell = $(elem).parent().parent();
//alert($(cell).html());
var url = "fileadmin/js/iport-proxy.php";
var query  = $(elem).attr("href");
var start = query.indexOf('i&');
params = query.substring(start+2);
//params = params + "&frame=RecordXML";

/*var temp = params.split("=");
params =  temp.join(':"');

temp = params.split('&');
params =  temp.join('",');


alert(params);*/
/*$.get( url, {params},  function(data){
        //$(cell).html( data );
	alert("Data Loaded: " + data);

      }
  );
*/
var store = $(cell).html();
//alert(store);
$(cell).html('<span class="status">loading data ...</span>' );
//alert($(cell).html());
$.ajax({
      url:url,
      data:{ record:params },
      dataType:"html",
      success:function(data){$(cell).html( data ); $(cell).prepend('<div class="store">' + store + '</div>'); registerBS($(cell).children(".save"));registerDL($(cell).children(".download"));registerSW($(cell).children(".close"),$(cell).children(".store")); },
     
      error:function (xhr, ajaxOptions, thrownError){
		 alert(xhr.responseText );
		}
          }); // $.ajax()



}


function genealogy(id){
	var param = $.trim($("#"+id).text());
	
	//alert("/" + $("#"+id).text()+"/");

$.ajax({
     
      url:"fileadmin/js/gen.php",
      data:{ name: param },
      dataType:"html",
      success:function(data){ $("#"+id).html( data );},
      error:function(xhr,err,e){ alert( "Error: " + err ); }
    }); // $.ajax()
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
     	 }); // $.ajax()
	
	

}


$(document).ready(function(){
///	$('.free').css('display', 'none');
	$('#all').css('cursor', 'pointer');
	$('#no').css('cursor', 'pointer');

$("div.abstrCont").each(function(){
                        $(this).css("display", "inline");
                });
tooltip();

if ($("#gen").length) {
	genealogy("gen");
	

}


function register(node) {
	//$(node).each(function(){

	        $(node).click(function(event){
		event.preventDefault();
		alert($(this).html());
	     	var param= $(this).attr("href");
    	     	var url = "fileadmin/js/iport-proxy.php"
             	$start = param.indexOf("?");
             	param = param.substring($start+1);
              	alert(param);

        /*$.ajax({

                url: url,
                data:{ save: param },
                dataType:"html",
                success:function(data){
			alert("success!");
                        //replace icon
                },
                error:function(xhr,err,e){ alert( "Error: " + err ); }
         });
	  */
		});
	//});


}

//fetch full record
$('a.title').click(function(event){


	event.preventDefault();
	fullRecord($(this));
});


//


//alert(getSize()[1]);
//alert(getSize()[1]/5);
/*$("td.data em").bind("mouseover", function() {S

	var abstract = $(this).siblings(".hide");


	//abstract.css("position", "absolute");
	
	abstract.css("top", (getSize()[1]/6));
        abstract.css("display", "block");



  });



    $("td.data em").bind("mouseout", function() {

        var abstract = $(this).siblings(".hide");



        abstract.css("display", "none");



  });
*/




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

});


