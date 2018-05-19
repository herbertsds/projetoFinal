function f_GabTableaux(){

	var myData = { 'exercicio' : parseInt(numExercicio)};

	$.ajax({
		
        url: 'http://127.0.0.1:8000/api/tableaux/',
    	type: 'GET',
        callback: '?',
        data: myData, 
        datatype: 'application/json',
       
        success: function(retorno) {
	        console.log(retorno);
        },
	    error: function() { console.log('ERRO: Gabarito não encontrado!'); },
        });
        	
}




function f_LimpaTableaux(){
}

