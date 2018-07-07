//import { org } from 'jquery.orgchart.js';
function f_GabLPO(){

	var myData = { 'exercicio' : parseInt(numExercicio)};
	console.log(' enviando : ' + myData);

	$.ajax({
		
        url: 'http://127.0.0.1:8000/api/LPO/',
    	type: 'GET',
        callback: '?',
        data: myData, 
        datatype: 'application/json',
        success: function(retorno) {
	        //console.log(retorno);
	        var obj = JSON.parse(retorno);
            console.log(obj);
			$('#t_divFormulas').append("<article> --------------------------------------------------------- </article>" );

            $("#lpo_organisation").append(obj);
            $("#lpo_organisation").orgChart({container: $("#lpo_main")});
           // f_teste();
	        
	        
	        
        },
	    error: function() { 

	    	console.log('ERRO: Gabarito não encontrado!'); },
        });
        	
}




function f_LimpaLPO(){
}

