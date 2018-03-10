var perguntaFNC=false;
var negadaIndice;
var numExercicio;

	function f_Transformar(){
		
	//VERIFICAR OBRIGATORIEDADE DA SEQUENCIA
		if(formulaId == ""){
				alert("Selecione uma fórmula.");
		}
		else{
		
			switch(passoId){
			
				case 'passo1':
					if(formulaId!='finalVetor'){
						alert("Atenção!\nSelecione a Pergunta para ser negada.");
					}
					else{
						
						perguntaNegada = true;
						
		//// NEGAR A PERGUNTA
						
						numLinha++;
						
						$('#divFormulas').append("<p id='" + cont +"'>"+ numLinha + ": "  + pergunta +" # pergunta negada </p>" );
						$('#divFormulas').append("<article> --------------------------------------------------------- </article>" );
						
						$('#finalVetor').off();
						$('#finalVetor').append(" &#10004;");
						
						$('#passo1').off();
						$('#passo1').append(" &#10004;");

						passoId = "";
						formulaId = "";
						vet_regras[cont]= "perguntaNegada";
						negadaIndice == cont;
						regras++;
						perguntaFNC = true;
						
						}
					
					break;
				
				case 'passo2':
						
					if(perguntaNegada == false){
							alert("Atenção! Deve-se negar a pergunta como primeiro passo!");
	
						}
					else{
							regras --;
							cont++;
							numLinha++;

			// PASSAR VET_REGRAS[FORMULAID] PARA FNC ################################################################################
							
							$('#divNovasFormulas').append("<input type='checkbox' class='form-check-input' data-color = 'purple' name='ck_novasFormulas' id='" + cont +"' value='" + vet_regras[formulaId] +"'> "+ numLinha + ": "  + vet_regras[formulaId] +" em FNC </br>"  );

							vet_regras[cont]= "novaRegra";
	
							$('#' + formulaId).off();
							$('#' + formulaId).append(" &#10004;");

							
							$("#formulaId").prop("disabled", true);
							formulaId = "";
							if(regras ==0 && perguntaFNC == true){
								
								passoId = "";
								$('#btn_ConfrontarRegra').show();
								$('#btn_TransformarRegra').hide();
								
								$("#divFormulas").unbind();
<<<<<<< HEAD
					        	$('span').css({
				   					'color':'black'
					        	});	
					        	$('p').css({
				   					'color':'black'
					        	});	
=======
>>>>>>> feature-Tableaux-Laravel
								$('#alertResolucao').fadeOut();

								$('#passo2').off();
								$('#passo2').append(" &#10004;");
<<<<<<< HEAD
								
=======

>>>>>>> feature-Tableaux-Laravel
										
							}							
						}
						break;
				
				case "":

					alert("Passo selecionado inválido!");

					break;
				
				default: 
				
					break;
			}
		}
		
		 
	}
	
	
	// abrir a formula para fnc
	function f_FNC(){
		
		// chamar a funcao php que transforma fnc

	
	}
	//bater duas fórmulas diferentes para gerar uma nova
	
	function f_Confrontar(){
		
		camposMarcados = new Array();
		$("input[type=checkbox][name='ck_novasFormulas']:checked").each(function(){
		    camposMarcados.push($(this).val());
		});
		// mostra a saída
		console.log("Selecionados:" + camposMarcados );
		
	}
	// ######################################################################################################
	
	function f_Gabarito(){
		
<<<<<<< HEAD
		switch(tipoEx){
		
		case "tableaux":
			break;
		case "resolucao":
		
				console.log("teste");
				
				
				console.log("Vetor: " + vet_regras);
				
				$.ajax({
		    		
			        url: 'http://127.0.0.1:8000/api/resolucao/',
			    	type: 'GET',
			        callback: '?',
			        data: numExercicio,
			        datatype: 'application/json',
			        success: function(retorno) {
			        	console.log(retorno);
			        	gabaritoBuscado = JSON.parse(retorno);
			        	console.log(gabaritoBuscado); 
		
						
			        },
			        error: function() { alert('Erro na chamada'); },
			    });
				
				break;
		case "deducao":
			break;
		}
			
=======
		alert('gabarito');
		console.log("aqui");
>>>>>>> feature-Tableaux-Laravel
	}
	
	function f_Next(){
		alert("next");
	}
	
	function f_buscaExercicio(){
		if($('#numExercicio').val() == ""){
			alert("Escreva o número de um exercícío!");
		}
		else{
				regras = 0;
		    	numExercicio = $('#numExercicio').val();
				$('#regrasAdicionadas').text("");
				$('#perguntaAdicionada').text("");
				$('#divFormulas').empty();
				$('#divNovasFormulas').empty();
				
				vet_regras = [];
				var myData = {
		    	        'exercicio' : numExercicio
		    	    };
		            $.ajax({
		
		    	        url: 'http://127.0.0.1:8000/api/resolucao/exercicio',
		    	        type: 'GET',
		    	        callback: '?',
		    	        data: myData,
		    	        datatype: 'application/json',
		    	        success: function(retorno) {
		    	        	exercicioBuscado = JSON.parse(retorno);
		    	        	 
		    				$('#regra').prop('disabled', true);
		    				$('#pergunta').prop('disabled', true);
		    				$('#buttonRegra').hide();
		    				$('#buttonPergunta').hide();
		    				
		    				var limiteFormulas = ((exercicioBuscado.length) -1);

		    				for (var data = 0; data < limiteFormulas; data++) {
		    					  
		    					regras++;   					  
		    					  
		    					vet_regras.push(exercicioBuscado[data]);
		    					adicionadas = exercicioBuscado[data];
		    				
		    					$('#regrasAdicionadas').append("<br/>" + regras + ": " + adicionadas );
		    						
		    				}
		    				pergunta = exercicioBuscado[limiteFormulas];
		    				linhaPerg = regras+1;

		    				$('#perguntaAdicionada').append("<br/>" +linhaPerg + ": " + exercicioBuscado[limiteFormulas] );
		    				atualizaTela(tipoEx);
		    				
		    	        },
		    	        error: function() { alert('Exercício inválido!'); },
		    	    });
		}
	}
	