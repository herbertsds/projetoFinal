var perguntaFNC=false;
var negadaIndice;
var numExercicio;
var gabaritoBuscado;


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
						$('#divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
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
					        	$('span').css({
				   					'color':'black'
					        	});	
					        	$('p').css({
				   					'color':'black'
					        	});	
								$('#alertResolucao').fadeOut();

								$('#passo2').off();
								$('#passo2').append(" &#10004;");
								
										
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
		
		switch(tipoEx){
		
		case "tableaux":
			break;
		case "resolucao":
				$('#divNovasFormulas').text("");
				
				$.ajax({
		    		
			        url: 'http://127.0.0.1:8000/api/resolucao/',
			    	type: 'GET',
			        callback: '?',
			        data: numExercicio,
			        datatype: 'application/json',
			       
			        success: function(retorno) {
				        console.log(numExercicio);

			        	gabaritoBuscado = JSON.parse(retorno);
			        	console.log(gabaritoBuscado); 
						limiteGabarito = gabaritoBuscado.length;
						//console.log(limiteGabarito);
						//var data = 0;
						$('#divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
						for(var data=0; data < limiteGabarito; data++) {
							



							switch (gabaritoBuscado[data]){
								case "Negação da pergunta": 
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Negação da pergunta' data-content='Fórmula usada:<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>" + numLinha +": " + gabaritoBuscado[data+1] + "</p>");

									data = data + 2;
									
									break;
									
								case "Fórmula em FNC":
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Fórmula em FNC' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>" + numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									data = data + 1;
									break;
								
								case "Separação do E":
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+2] + "</p>" );
									data = data +2;
									
									break;
								
								case "Separação do Ou":
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do OU' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									data = data+2;
									
									
									break;
								
								case "Remove os notnot":
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Remove os notnot' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									data = data+2;
									
									break;
									
								case "Fechado":
									cont++;
									numLinha++;
									$('#divNovasFormulas').append("<p id='" + cont +"'>"  + numLinha +"<b>: &#10066; Contradição Encontrada!</b> </p>" );
									$('#divNovasFormulas').append("<article><b> Fim do Exercício</b></article>");
									$('#btn_TransformarRegra').hide();
									$('#btn_ProximoPasso').hide();
									$('#btn_gabarito').attr('disabled',true);
									$("#divFormulas").unbind();
									$("#passos").off();
						        	$('span').css({
					   					'color':'black'
						        	});	
						        	$('p').css({
					   					'color':'black'
						        	});	
									$('#alertResolucao').fadeOut();
									console.log("resolvido!");
									break;
								default: 
									console.log("nenhum case");
									break;

							}
						}
						
				        //DESTAQUE MOUSE SOBRE
				        $('#divNovasFormulas').on('mouseenter','p', function(e) {
				        	

						$('[data-toggle="popover"]').popover();
				        	$(e.target).css({
				        		'cursor':'pointer',

				   				'font-weight': 900	        			
				        });
				         $('#divNovasFormulas').on('mouseout','p', function(e) {
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
			        error: function() { alert('Erro na chamada'); },
			    });

				
				break;
		case "deducao":
			break;
		}
			
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
		    	        	console.log(retorno);
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
	