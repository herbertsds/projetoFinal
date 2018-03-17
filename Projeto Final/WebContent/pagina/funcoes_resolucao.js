var perguntaFNC=false;
var negadaIndice;



	function f_Transformar(){
		
	//VERIFICAR OBRIGATORIEDADE DA SEQUENCIA
		if(formulaId == ""){
				alert("Selecione uma fórmula.");
		}
		else{
		
			switch(passoId){
			
				case 'r_passo1':
					if(formulaId!='finalVetor'){
						alert("Atenção!\nSelecione a Pergunta para ser negada.");
					}
					else{
						
						perguntaNegada = true;
						
		//// NEGAR A PERGUNTA
						
						numLinha++;
						
						$('#r_divFormulas').append("<p id='" + cont +"'>"+ numLinha + ": "  + pergunta +" # pergunta negada </p>" );
						$('#r_divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
						$('#finalVetor').off();
						$('#finalVetor').append(" &#10004;");
						
						$('#r_passo1').off();
						$('#r_passo1').append(" &#10004;");

						passoId = "";
						formulaId = "";
						vet_regras[cont]= "perguntaNegada";
						negadaIndice == cont;
						regras++;
						perguntaFNC = true;
						
						}
					
					break;
				
				case 'r_passo2':
						
					if(perguntaNegada == false){
							alert("Atenção! Deve-se negar a pergunta como primeiro passo!");
	
						}
					else{
							regras --;
							cont++;
							numLinha++;

			// PASSAR VET_REGRAS[FORMULAID] PARA FNC ################################################################################
							
							$('#r_divNovasFormulas').append("<input type='checkbox' class='form-check-input' data-color = 'purple' name='ck_novasFormulas' id='" + cont +"' value='" + vet_regras[formulaId] +"'> "+ numLinha + ": "  + vet_regras[formulaId] +" em FNC </br>"  );

							vet_regras[cont]= "novaRegra";
	
							$('#' + formulaId).off();
							$('#' + formulaId).append(" &#10004;");

							
							$("#formulaId").prop("disabled", true);
							formulaId = "";
							if(regras ==0 && perguntaFNC == true){
								
								passoId = "";
								$('#btn_ConfrontarRegra').show();
								$('#btn_TransformarRegra').hide();
								
								$("#r_divFormulas").unbind();
					        	$('span').css({
				   					'color':'black'
					        	});	
					        	$('p').css({
				   					'color':'black'
					        	});	
								$('#alertResolucao').fadeOut();

								$('#r_passo2').off();
								$('#r_passo2').append(" &#10004;");
								
										
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
	
