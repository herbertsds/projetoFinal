//import { org } from 'jquery.orgchart.js';
function f_Gabt_lpo(){

	var myData = { 'exercicio' : parseInt(numExercicio)};
	console.log(' enviando : ' + myData);

	$.ajax({
		
        url: 'http://127.0.0.1:8000/api/t_lpo/',
    	type: 'GET',
        callback: '?',
        data: myData, 
        datatype: 'application/json',
        success: function(retorno) {
	        //console.log(retorno);
	        var obj = JSON.parse(retorno);
            console.log(obj);
			$('#t_divFormulas').append("<article> --------------------------------------------------------- </article>" );

            $("#t_lpo_organisation").append(obj);
            $("#t_lpo_organisation").orgChart({container: $("#t_lpo_main")});
           // f_teste();
	        
	        
	        
        },
	    error: function() { 

	    	console.log('ERRO: Gabarito não encontrado!'); },
        });
        	
}




function f_Limpat_lpo(){
}

