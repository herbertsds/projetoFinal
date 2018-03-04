// Separar as funcoes dos diferentes tipos em js diferentes
// ao clicar em + armazernar as regras e a pergunta em um vetor - ok
// ir apresentando dinamicamente as regras ja adicionadas em uma area na lateral direita - ok 
// ao clicar em next passar para a tela de selecao de metodo - ok
// ao selecionar o metodo, dar algum destaque a ele - ok
/* ao clicar em next, passar para a tela de exercicio, apresentando as regras aplicaveis e uma area para a arvore
ser montada dinamicamente */ 
// nao permitir usar regras repetidas ou invalidas 


	var vet_regras = [];
	var pergunta;
	var tipoEx = "";
	var adicionadas="";
	var passoId = "";
	var formulaSimplificar ="";
	var formulaId="";
	var regras = 0;
	var perguntaFNC=false;
	var formulas_JSON;
	var transformados_FNC;
	var cont =0;
	
	// funcao apenas para testes de eventos
	function teste(){
		
		alert("teste");
		
	}
	
	
	
	function load(){
		$('#regra').val("");
		$('#pergunta').val("");
    	//alert("onload ok");
    
          	
	}
	
//	function RefreshEventListener() {
//        // Remove handler from existing elements
//        $(".testeFormula").off(); 
//
//        // Re-add event handler for all matching elements
//        $(".testeFormula").on("click", function(e) {
//        	 var txt = $(e.target).text();
//			  console.log(txt);
//        })
//    }
	
// ------------ Escolha do tipo de Ex / carregamento da ultima tela -------------------
	$(document).ready(function() {
	

// FORMULAS -------------------------------------------------------------------------------------------------------------------		
		// DESTAQUE AO SELECIONAR FORMULA
        $('#divFormulas').on('click', 'p',  function(e) {
        	$('p').css({
					'color':'black',
//					'background':'none',
					'font-weight': 100

        	});
			  
        	$(e.target).css({
					'color':'red',
					'font-weight': 900
					
        	});;
        	//formulaSimplificar = $(e.target).text();
			formulaId = this.id;
			
			  
        });
 
        //DESTAQUE MOUSE SOBRE
        $('#divFormulas').on('mouseenter','p', function(e) {
        	$(e.target).css({
        		'cursor':'pointer',

   				'font-weight': 900	        			
        });
         $('#divFormulas').on('mouseout','p', function(e) {
        	 $('p').css({
					'font-weight': 100,
        	 });	
        	 $(e.target).css({
 	        		'cursor':'text'
        	 });;
         });
         
        });
 
// --------------------------------------------------------------------------------------------------------------------------        
// PASSOS -------------------------------------------------------------------------------------------------------------------
		// DESTAQUE AO SELECIONAR PASSO
	        $('span').on('click', function(e) {
	          	 $('span').css({
	   					'color':'black',
	   					//'background':'none',
						'font-weight': 100,
						'border' : 'none'

	          	 });
	   			  
	          	 $(e.target).css({
	          		 	//'border': '1px dashed',
	   					'color':'darkgreen',
	   					//'background':'LightGreen ',
	   					'font-weight': 900
	          	 });;
	          	passoId = this.id;
          });
	        
	        //destaque mouse sobre
	        $('span').on('mouseenter', function(e) {
	        	$(e.target).css({
	        		'cursor':'pointer',
	   				//'color':'green',
	   				//'background':'LightGreen ',
	   				'font-weight': 900	        			
	        	});
	        $('span').on('mouseout', function(e) {
	        	$('span').css({
	   					//'color':'black',
	   					//'background':'none',
						'font-weight': 100,
	        	});	
	        	$(e.target).css({
	   					//'color':'black',
	   					//'background':'none',
						//'font-weight': 100,
	 	        		'cursor':'text'
	        	});;
	         });
    
	        });

//--------------------------------------------------------------------------------------------------------------------------                

// EXIBIR A TAB DE EXECUCAO DE ACORDO COM O TIPO DE EX ---------------------------------------------------------------------
		
	     $("#botaoTableaux").click( function()
		    {
				tipoEx = "tableaux";
			
				carregaTela("tableaux");
				$("#next").removeAttr("disabled");
		      
		    }
		 );
		
		$("#botaoResolucao").click( function()
			    {
					
					tipoEx = "resolucao";
					carregaTela("resolucao");
					
					$("#next").removeAttr("disabled");
					
			      
			    }
			 );
		$("#botaoDeducao").click( function()
			    {
					tipoEx = "deducao";
					
					carregaTela("deducao");
					$("#next").removeAttr("disabled");
			      
			    }
			 );

	});
	
	function carregaTela(exercicio){
		switch (exercicio) {
		case "tableaux":
			$("#divTableaux").removeAttr("style");
			$("#divResolucao").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();

			break;
		
		case "resolucao":
			$("#divResolucao").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");

			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();

			break;
		
		case "deducao":
			$("#divDeducao").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divResolucao").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");

			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();

			break;
			
		default:
			break;
		}
	}
	
	function atualizaTela(){
		
		switch(tipoEx){
		
			case "tableaux":
				$('#formulas').append("<br/>" + pergunta );
			
				break;
			
			case "resolucao":
				  for (cont = 0; cont in vet_regras; cont++){
						$('#divFormulas').append("<p id='" + cont+ "'>" + vet_regras[cont] + "</p>" );
	
				  }
				$('#divFormulas').append("<p id='finalVetor'>" + pergunta + "</p>" );
				
			break;
			case "deducao":
				$('#formulas').append("<br/>" + pergunta );
				break;
		}
	}	
//-------------------------------------------------------------------------------------------------------
// ############# ADICAO DE REGRAS E PERGUNTA ############################################################
	
	function f_AddRegra(){
		if($('#regra').val() == ""){
			alert("Regra inválida!");
		}
		else{
			vet_regras.push($('#regra').val().replace(/\s/gi, ''));
			adicionadas = $('#regra').val();
			
			$('#regrasAdicionadas').append("<br/>" + adicionadas );
			$('#regra').val("");
			regras++;
		}
	} 
	
	function f_AddPergunta(){
		if($('#pergunta').val() == ""){
			alert("Pergunta inválida!");
		}
		else{
			encerrado = confirm("Tem certeza que todas as regras do BD foram adicionadas?");
			if(encerrado){
		
				pergunta = $('#pergunta').val().replace(/\s/gi, '');
				$('#perguntaAdicionada').append("<br/>" + $('#pergunta').val() );
				$('#pergunta').val("Pergunta Adicionada!!");
				$('#regra').prop('disabled', true);
				$('#pergunta').prop('disabled', true);
				$('#buttonRegra').hide();
				$('#buttonPergunta').hide();
				atualizaTela(tipoEx);
				$('#tabExecucao').click();
				if(regras == 0){
					$('#1').html('');
					$('#alertResolucao').attr("style", 'display:none');
					// mudando a cor do campo
				}
				//CHAMAR PASSAR PRA FNC(vet_regras)############################################################################
			}
		}
		
	}
	
	// FALTA PERMITIR EXCLUSAO/ALTERACAO DA PERGUNTA E DE REGRAS
	

	// ######################################################################################################
	
	// ######### EXECUCAO DO EXERCICIO ######################################################################
	function simplificar(){
		
		//VERIFICAR OBRIGATORIEDADE DA SEQUENCIA
		switch(passoId){
			case 'passo1':
				if(formulaId=='finalVetor'){
					alert("Atenção!\nPasse todas as fórmulas do banco para FNC.");
				}
				else{
					cont++;
					regras--;
					$('#divFormulas').append("<p id='" + cont+ "'>Formula " + $("[id^= '0']").text() + " em FNC</p>");
					//console.log($("p[id^='formulaId']"));
//					.css({
//						    'text-decoration': 'line-through'
//
//					});
//					$('#divFormulas').append("<p id='" + cont+ "'>" + transformados_FNC[formulaId] + "</p>" );
					if(regras == 0){
						$('#passo1').off();

						$('#passo1').css({
						    'text-decoration': 'line-through',

						});
						$('#alertResolucao').fadeOut();
						passoId = "passo2";
						formulaId = "";
					}
				}
				break;
			
			case 'passo2':
					if(regras>0){
						alert("Atenção!\nPasse todas as fórmulas do banco para FNC.");

					}
					else if(formulaId == "finalVetor"){
						perguntaFNC = true;
						$('#divFormulas').append("<p id='" + cont+ "'>Pergunta " + $('#finalVetor').text() + " em FNC</p>");

						
						$('#passo2').off();
						$('#finalVetor').unbind();

						$('#passo2').css({
						    'text-decoration': 'line-through'
							
						});

							$('#finalVetor').css({

							    'text-decoration': 'line-through'
								
							});
							
						}										

						
						
					else{
						alert("Selecione a pergunta!");
						}
				
				break;
			case 'passo3':
				if(regras>0){
					alert("Atenção!\nPasse todas as fórmulas do banco para FNC.");

				}
				else{
					if(perguntaFNC == false){
						alert("Atenção!\nPasse a pergunta para FNC.")
						break;
					}
					else{
						console.log("ok");
					}//exibir pergunta negada
				}
				break;
			default: 

					break;
		}
		
		
		 
	}
	// abrir a formula para fnc
	function f_FNC(){
		
		// chamar a funcao php que transforma fnc

	
	}
	//bater duas fórmulas diferentes para gerar uma nova
	function f_ComparaRegra(){
		// chamar a funcao php que executa o passo da resolucao
	}
	// ######################################################################################################
	
		
	
	
	
	
	
	