$(document)ready(function(){













$("div.abstrCont").each(function(){
                        $(this).css("display", "inline");
                });
tooltip();

if ($("#gen").length) {
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

        //fetch full record
        $('a.title').click(function(event){


                event.preventDefault();
                fullRecord($(this));
        });


});

