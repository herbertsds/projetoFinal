

	var vet_regras = [];
	var pergunta;
	var tipoEx = "";
	var adicionadas="";
	var passoId = "";
	var formulaSimplificar ="";
	var formulaId="";
	var regras = 0;
	var formulas_JSON;
	var transformados_FNC;
	var cont =0;
	var perguntaNegada = false;
	var numLinha = 0;
	var numExercicio = "";
	var gabaritoBuscado;
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
		
		
		$('#btn_ConfrontarRegra').hide();
		$('#btn_TransformarRegra').show();
		
		
		$('[data-toggle="popover"]').popover();

		 
		 // FORMULAS -------------------------------------------------------------------------------------------------------------------		
		// DESTAQUE AO SELECIONAR FORMULA
        $('#r_divFormulas').on('click', 'p',  function(e) {
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
        $('#r_divFormulas').on('mouseenter','p', function(e) {
        	$(e.target).css({
        		'cursor':'pointer',

   				'font-weight': 900	        			
        });
         $('#r_divFormulas').on('mouseout','p', function(e) {
        	 $('p').css({
					'font-weight': 100,
        	 });	
        	 $(e.target).css({
 	        		'cursor':'text'
        	 });;
         });
         
        });
        
        $('#r_divFormulas').on('click', 'p',  function(e) {
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
				$("#proximo").removeAttr("disabled");
		      
		    }
		 );
		
		$("#botaoResolucao").click( function()
			    {
					
					tipoEx = "resolucao";
					carregaTela("resolucao");
					
					$("#proximo").removeAttr("disabled");
					
			      
			    }
			 );
		$("#botaoDeducao").click( function()
			    {
					tipoEx = "deducao";
					
					carregaTela("deducao");
					$("#proximo").removeAttr("disabled");
			      
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
				  for (cont = 0; cont in vet_regras; cont++){
					  numLinha = cont+1;
						$('#t_divFormulas').append("<p id='" + cont+ "'>"+ numLinha +": " + vet_regras[cont] + "</p>" );
	
				  }
				  numLinha++;
				$('#t_divFormulas').append("<p id='finalVetor'>"+ numLinha +": " + pergunta + " # Pergunta </p>" );
				break;
			
			case "resolucao":
				  for (cont = 0; cont in vet_regras; cont++){
					  numLinha = cont+1;
						$('#r_divFormulas').append("<p id='" + cont+ "'>"+ numLinha +": " + vet_regras[cont] + "</p>" );
	
				  }
				  numLinha++;
				$('#r_divFormulas').append("<p id='finalVetor'>"+ numLinha +": " + pergunta + " # Pergunta </p>" );
				if(regras==0){
					$('#alertResolucao').fadeOut();

				}
				
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

			regras++;

			vet_regras.push($('#regra').val().replace(/\s/gi, ''));
			adicionadas = $('#regra').val();
			
			$('#regrasAdicionadas').append("<br/>" + regras + ": " + adicionadas );
			$('#regra').val("");
		}
	} 
	
	function f_AddPergunta(){
		if($('#pergunta').val() == ""){
			alert("Pergunta inválida!");
		}
		else{
			encerrado = confirm("Tem certeza que todas as regras do BD foram adicionadas?");
			if(encerrado){
				linhaPerg = regras + 1;
				pergunta = $('#pergunta').val().replace(/\s/gi, '');
				$('#perguntaAdicionada').append("<br/>" +linhaPerg + ": " + $('#pergunta').val() );
				$('#pergunta').val("Pergunta Adicionada!!");
				$('#regra').prop('disabled', true);
				$('#pergunta').prop('disabled', true);
				$('#buttonRegra').hide();
				$('#buttonPergunta').hide();
				atualizaTela(tipoEx);
				$('#tabExecucao').click();
				
			}
		}
		
	}
	
	// FALTA PERMITIR EXCLUSAO/ALTERACAO DA PERGUNTA E DE REGRAS
	
	function f_buscaExercicio(){
		if($('#numExercicio').val() == ""){
			alert("Escreva o número de um exercícío!");
		}
		else{
				regras = 0;
		    	numExercicio = $('#numExercicio').val();
				$('#regrasAdicionadas').text("");
				$('#perguntaAdicionada').text("");
				$('#r_divFormulas').text("");
				$('#r_divNovasFormulas').text("");
				
				vet_regras = [];
				var myData = { 'exercicio' : parseInt(numExercicio)};
				console.log("myData = " + myData);
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
	
	function f_Next(){
		alert("next");
	}
	
	
	function f_Gabarito(){
		
		switch(tipoEx){
		
		case "tableaux":
			break;
		case "resolucao":
				$('#r_divNovasFormulas').text("");
				var myData = { 'exercicio' : parseInt(numExercicio)};

				$.ajax({
		    		
			        url: 'http://127.0.0.1:8000/api/resolucao/',
			    	type: 'GET',
			        callback: '?',
			        data: myData, 
			        datatype: 'application/json',
			       
			        success: function(retorno) {
				        console.log(numExercicio);

			        	gabaritoBuscado = JSON.parse(retorno);
			        	console.log(gabaritoBuscado); 
						limiteGabarito = gabaritoBuscado.length;
						//console.log(limiteGabarito);
						//var data = 0;
						$('#r_divNovasFormulas').append("<article> --------------------------------------------------------- </article>" );
						
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
									data = data + 1;
									break;
								
								case "Separação do E":
									cont++;
									numLinha++;
									$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									cont++;
									numLinha++;
									$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do E' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+3]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+2] + "</p>" );
									data = data +2;
									
									break;
								
								case "Separação do Ou":
									cont++;
									numLinha++;
									$('#r_divNovasFormulas').append("<p id='" + cont+ "' data-html='true'  data-trigger='click' data-toggle='popover' data-placement='right' title='Separação do OU' data-content='Fórmulas usadas:\n<ul><li>"+ gabaritoBuscado[data+2]+"</li></ul>'>"+ numLinha +": " + gabaritoBuscado[data+1] + "</p>" );
									data = data+2;
									
									
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
									//$('#btn_gabarito').attr('disabled',true);
									$("#r_divFormulas").unbind();
									$("#r_passos").off();
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
			        error: function() { alert('Erro na chamada'); },
			    });

				
				break;
		case "deducao":
			break;
		}
			
	}
	


	
