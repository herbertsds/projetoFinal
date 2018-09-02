function f_GabSemantica(){
	var myData = { 'exercicio' : exercicioBuscado}; // parseInt(numExercicio)
	console.log(' enviando : ' + myData);

	$.ajax({
		
        url: 'http://127.0.0.1:8000/api/semantica/',
    	type: 'GET',
        callback: '?',
        data: myData, 
        datatype: 'application/json',
        success: function(obj) {
	        //console.log(retorno);
	        //var obj = JSON.parse(retorno);
            //console.log(obj);
			//$('#t_divFormulas').append("<article> --------------------------------------------------------- </article>" );
        	//$('#iframe').css('height', $(window).height()+'px');
        	//$('#iframe').contents().find('body').append('<div  class="form-check"  id="s_divNovasFormulas"><div id="s_content"><div id="s_main"><ul id="s_organisation"></ul></div></div></div>');
        	//$('#iframe').contents().find("#s_organisation").append(obj);
        	//$('#iframe').contents().find("#s_organisation").orgChart({container: $('#iframe').contents().find("#s_main")});
            var win = window.open('gabSemantica.html');
            
        	$("win#s_organisation").append("oi");
             // $("win#s_organisation").orgChart({container: $("win#s_main")});
              
           // $('#btnSemantica').click();
	        
        },
	    error: function(retorno2) { 

	    	console.log(retorno2); },
        });
        	
}

function f_LimpaSemantica(){
	
	
}