// tic adicionado só na primeira formula obrigatoria 
var perguntaFNC=false;
var negadaIndice;
var vet_Entrada = [];
var selecionadas =0;
var linhasGab =0;
var idPergNegada;
	function f_Transformar(){
		
	//VERIFICAR OBRIGATORIEDADE DA SEQUENCIA
		if(formulaId == ""){
				alert("Selecione uma fórmula.");
		}
		else{
		
			switch($('#btn_TransformarRegra').text()){
			
				case 'Negar Pergunta':
					if(formulaId!='finalVetor'){
						alert("Atenção!\nSelecione a Pergunta para ser negada.");
					}
					else{
						
						perguntaNegada = true;
						
		//// NEGAR A PERGUNTA
						resposta = f_Negar(pergunta);
						numLinha++;
						linhasGab++;
						idPergNegada = cont;
						$('#r_divFormulas').append("<p id='" + cont +"'>"+ numLinha + ": "  + pergunta +" # pergunta negada </p>" );
						$('#r_divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
						$('#finalVetor').off();
						$('#finalVetor').append(" &#10004;");
						
//						$('#r_passo1').off();
						$('#r_passo1').append(" &#10004;");

//						passoId = "";
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
							regras --;
							cont++;
							numLinha++;
							linhasGab++;
			// PASSAR VET_REGRAS[FORMULAID] PARA FNC ################################################################################
							resposta = f_FNC(vet_regras[formulaId]);
							$('#r_divNovasFormulas').append("<input disabled type='checkbox' class='form-check-input' data-color = 'purple' name='ck_novasFormulas' id='" + cont +"' value='" + vet_regras[formulaId] +"'> "+ numLinha + ": "  + resposta +" em FNC </br>"  );

							vet_regras[cont]= "novaRegra";

							$("p[id='" + formulaId+"']").append(" &#10004;");

							
							
							formulaId = "";
							if(regras ==0 && perguntaFNC == true){
								
//								passoId = "";
								$('#btn_ConfrontarRegra').show();
								$('#btn_SepararE').show();
								$('#btn_SepararOU').show();

								
								
								$('#btn_TransformarRegra').hide();
								
								$("#r_divFormulas").unbind();
					        	$('span').css({
				   					'color':'black'
					        	});	
					        	$('p').css({
				   					'color':'black'
					        	});	
								$('#alertResolucao').fadeOut();

								$('#r_passo2').append(" &#10004;");
								$(":checkbox").prop("disabled", false);

										
							}							
						}
						break;
				

				
				default: 
				
					break;
			}
		}
		
		 
	}
	
	
	// abrir a formula para fnc
	function f_FNC(formula){
		
		// chamar a funcao php que transforma fnc
		vet_Entrada = [];
		vet_Entrada[0] = "FNC";
		vet_Entrada[1] = 1;
		vet_Entrada[2] = formula;
		console.log("Entrada enviada:"+vet_Entrada);
		return formula;
	
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
		
		
		
		
		
		return formula;
	}
	
	//bater duas fórmulas diferentes para gerar uma nova
	
	function f_Confrontar(){
		
		camposMarcados = new Array();
		$("input[type=checkbox][name='ck_novasFormulas']:checked").each(function(){
		    camposMarcados.push($(this).val());
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


	
	// ######################################################################################################
	
