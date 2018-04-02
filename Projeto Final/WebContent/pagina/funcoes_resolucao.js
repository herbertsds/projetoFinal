// tic adicionado só na primeira formula obrigatoria 
var perguntaFNC=false;
var negadaIndice;
var vet_Entrada = [];
var selecionadas = 0;
var linhasGab =0;
var idPergNegada;
var camposMarcados;

	function f_Transformar(){
		
	//VERIFICAR OBRIGATORIEDADE DA SEQUENCIA
//		if(formulaId == ""){
//				alert("Selecione uma fórmula.");
//		}
//		else{
		
			switch($('#btn_TransformarRegra').text()){
			
				case 'Negar Pergunta':
					f_CkSelecionados();
					if(selecionadas != 1 || camposMarcados[0] != pergunta){
						alert("Atenção!\nSelecione a Pergunta para ser negada.");
					}
					else{
						
						perguntaNegada = true;
						
		//// NEGAR A PERGUNTA
						resposta = f_Negar(pergunta);
						numLinha++;
						linhasGab++;
						idPergNegada = cont;
						$('#r_divFormulas').append("<input  type='checkbox' class='form-check-input'  name='ck_Formulas' id='"+ idPergNegada + "' value='"+ pergunta + "'>"+ numLinha +": " + pergunta + " # pergunta negada </input></br>" );
						$('#r_divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
						$("#finalVetor").prop("disabled", true);
						$("#finalVetor").prop("checked", false);
						
						$('#r_passo1').append(" &#10004;");

						formulaId = "";
						vet_regras[cont]= resposta;
						negadaIndice == cont;
						regras++;
						perguntaFNC = true;
						
						$('#btn_TransformarRegra').text("Passar para FNC");
						
						}
					
					break;
				
				case 'Passar para FNC':
	
					if(perguntaNegada == false){
							alert("Atenção! Deve-se negar a pergunta como primeiro passo!");
	
						}
					else{
						f_CkSelecionados();
						if(selecionadas == 0){
							alert("Selecione pelo menos uma fórmula!");
							
						}
						else{


	// PASSAR VET_REGRAS[FORMULAID] PARA FNC ################################################################################
							
							resposta = f_FNC(vet_regras[formulaId]);
							
							for(var i=0;i<selecionadas;i++){
								regras --;
								cont++;
								numLinha++;
								linhasGab++;
								$('#r_divNovasFormulas').append("<input disabled type='checkbox' class='form-check-input' data-color = 'purple' name='ck_novasFormulas' id='" + cont +"' value='" + vet_regras[formulaId] +"'> "+ numLinha + ": "  + resposta[i] +" em FNC </br>"  );

								vet_regras[cont]= resposta[i];
							}
							
							
							formulaId = "";
							if(regras ==0 && perguntaFNC == true){
								
								$('#btn_ConfrontarRegra').show();
								$('#btn_SepararE').show();
								$('#btn_SepararOU').show();
								$('#btn_TransformarRegra').hide();
								
								$("#r_divFormulas").unbind();
								$('#alertResolucao').fadeOut();

								$('#r_passo2').append(" &#10004;");
								$("#r_divNovasFormulas :checkbox").prop("disabled", false);
								
							}	
						}	
					}
						break;
				

				
				default: 
				
					break;
			}
//		}
		
		 
	}
	
	
	// abrir a formula para fnc
	function f_FNC(formula){
		selecionadas =0;
		// chamar a funcao php que transforma fnc
		vet_Entrada = [];
		camposMarcados = new Array();
		$("input[type=checkbox][name='ck_Formulas']:checked").each(function(){
		    camposMarcados.push($(this).val());
			$(this).prop("disabled", true);
			$(this).prop("checked", false);

		    selecionadas++;
		});
		
		
			// mostra a saída
			console.log("Selecionados:" + camposMarcados );
			vet_Entrada[0] = "FNC";
			vet_Entrada[1] = selecionadas;
			vet_Entrada[2] = camposMarcados;
			console.log("Entrada enviada:"+vet_Entrada);
		return vet_Entrada[2];
	
	}
	
	function f_Negar(formula){
		vet_Entrada = [];
		vet_Entrada[0] = "negPergunta";
		vet_Entrada[1] = vet_regras.length +1;
		vet_todasFormulas = vet_regras;
		vet_todasFormulas[vet_todasFormulas.length] = pergunta;
		//console.log("todas = " + vet_todasFormulas);
		vet_Entrada[2] = vet_todasFormulas;
		console.log(vet_Entrada);
		
		// CHAMAR PHP ENVIAR JSON
//		var myData = { 'operacao' : vet_entrada[0],
//						'qtd_formulasSelecionadas' : vet_entrada[1],
//						'formulas' : vet_entrada[2]
//	
//	};
//
//		$.ajax({
//    		
//	        url: 'http://127.0.0.1:8000/api/resolucao/',
//	    	type: 'GET',
//	        callback: '?',
//	        data: myData, 
//	        datatype: 'application/json',
//	       
//	        success: function(retorno) {
//		        //console.log(numExercicio);
//
//	        	gabaritoBuscado = JSON.parse(retorno);
//
		
		
		$(this).prop("disabled", true);
		$(this).prop("checked", false);
		
		
		
		return formula;
	}
	
	//bater duas fórmulas diferentes para gerar uma nova
	
	function f_Confrontar(){
		selecionadas = 0;

		camposMarcados = new Array();
		$("input[type=checkbox][name='ck_novasFormulas']:checked").each(function(){
		    camposMarcados.push($(this).val());
		    selecionadas++;

		});
		if(selecionadas == 0){
			alert("Número inválido de fórmulas para Confrontar!");
		}
		
		else{// mostra a saída
			console.log("Selecionados:" + camposMarcados );
		}
	}
	function f_SepararE(){
			selecionadas = 0;
			vet_Entrada = [];
			camposMarcados = new Array();
			$("input[type=checkbox][name='ck_novasFormulas']:checked").each(function(){
			    camposMarcados.push($(this).val());
			    selecionadas++;
			});
			
			if(selecionadas == 0){
				alert("Número inválido de fórmulas para separação do E!");
			}
			
			else{
				// mostra a saída
				console.log("Selecionados:" + camposMarcados );
				vet_Entrada[0] = "separaE";
				vet_Entrada[1] = selecionadas;
				vet_Entrada[2] = camposMarcados;
				console.log("Entrada enviada:"+vet_Entrada);
			}
		}
	
	function f_SepararOU(){
		
		
		selecionadas = 0;
		vet_Entrada = [];		
		camposMarcados = new Array();
		$("input[type=checkbox][name='ck_novasFormulas']:checked").each(function(){
		    camposMarcados.push($(this).val());
		    selecionadas++;
		});
		
		if(selecionadas % 2 === 0  && selecionadas > 0){
			// mostra a saída
			console.log("Selecionados:" + camposMarcados );
			vet_Entrada[0] = "separaOU";
			vet_Entrada[1] = selecionadas;
			vet_Entrada[2] = camposMarcados;
			console.log(vet_Entrada);		
		}
		else{
			selecionadas = 0;
			vet_Entrada = [];		
			alert("Número inválido de fórmulas para separação do OU!");
		}
	}

function f_GabResolucao(){
	$('#r_divNovasFormulas').text("");
	$("p[id='" + idPergNegada+"']").text("");
	
	var myData = { 'exercicio' : parseInt(numExercicio)};

	$.ajax({
		
        url: 'http://127.0.0.1:8000/api/resolucao/',
    	type: 'GET',
        callback: '?',
        data: myData, 
        datatype: 'application/json',
       
        success: function(retorno) {
	        //console.log(retorno);

        	gabaritoBuscado = JSON.parse(retorno);
        	//console.log(gabaritoBuscado); 
			limiteGabarito = gabaritoBuscado.length;
			//console.log(limiteGabarito);
			$('#r_divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
			numLinha = numLinha-linhasGab;
			
			// traduzir para o usuário o retorno  
			// apresentar o gabarito na tela
			for(var data=0; data < limiteGabarito; data++) {
				



				switch (gabaritoBuscado[data]){
					case "Negação da pergunta": 
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Negação da pergunta' data-content='Fórmula usada:<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>" + numLinha +": " + gabaritoBuscado[data+1] + "</p>");

						data = data + 2;
						
						break;
						
					case "Fórmula em FNC":
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Fórmula em FNC' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>" + numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
						data = data + 2;
						break;
					
					case "Separação do E":
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+2] + "</p>" );
						data = data +3;
						
						break;
					
					case "Separação do Ou":
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do OU' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
						data = data+3;
						
						
						break;
					
					case "Remove os notnot":
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Remove os notnot' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
						data = data+2;
						
						break;
						
					case "Fechado":
						cont++;
						numLinha++;
						$('#r_divNovasFormulas').append("<p id='" + cont +"'>"  + numLinha +"<b>: &#10066; Contradição Encontrada!</b> </p>" );
						$('#r_divNovasFormulas').append("<article><b> Fim do Exercício</b></article>");
						$('#btn_TransformarRegra').hide();
						$('#btn_ProximoPasso').hide();
						$('#btn_ConfrontarRegra').hide();
						$('#btn_SepararE').hide();
						$('#btn_SepararOU').hide();
						$("#r_divFormulas").unbind();
						$('span').css({
		   					'color':'black'
			        	});	
			        	$('p').css({
		   					'color':'black'
			        	});	
						$('#alertResolucao').fadeOut();
						console.log("resolvido!");

						$('#r_passo3').append(" &#10004;");
						
						break;
					default: 
						console.log("fim do gabarito");

						break;

				}
			}
			
	        //DESTAQUE MOUSE SOBRE
	        $('#r_divNovasFormulas').on('mouseenter','p', function(e) {
	        	

			$('[data-toggle="popover"]').popover();
	        	$(e.target).css({
	        		'cursor':'pointer',

	   				'font-weight': 900	        			
	        });
	         $('#r_divNovasFormulas').on('mouseout','p', function(e) {
				$('[data-toggle="popover"]').popover('hide');

	        	 $('p').css({
						'font-weight': 100,
	        	 });	
	        	 $(e.target).css({
	 	        		'cursor':'text'
	        	 });;
	         });
	         
	        });
	        


			
        },
        error: function() { alert('Gabarito não encontrado!'); },
    });

	

}

function f_CkSelecionados(){
	camposMarcados = new Array();
	selecionadas = 0;
	$("input[type=checkbox][name='ck_Formulas']:checked").each(function(){
	    camposMarcados.push($(this).val());
	    formulaId = $(this).attr("id");
	    
	    selecionadas++;
//	    console.log(formulaId);
	});
	
}
	
	// ######################################################################################################
	
