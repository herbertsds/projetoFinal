// problemas:  passo a passo + gabarito gera numLinha errado
// permitir selecionar mais de uma formula para passar para fnc

	var vet_regras = [];
	var pergunta;
	var tipoEx = "";
	var adicionadas="";
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
	var exercicioBuscado;
	var vet_exercicios = [];
	var indice = 0;
	
	
	// funcao apenas para testes de eventos
	function teste(){
		
		alert("teste");
		
	}
	
	
	
	function load(){
		$('#regra').val("");
		$('#pergunta').val("");
    	//alert("onload ok");

          	
	}
	

// ------------ Escolha do tipo de Ex / carregamento da ultima tela -------------------
	$(document).ready(function() {
		
    	$('#dcc').css({
			'float':'right'
	});

		$('#btn_ConfrontarRegra').hide();
		$('#btn_TransformarRegra').show();
		$('#btn_SepararE').hide();
		$('#btn_SepararOU').hide();
		
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
			f_listaEx();
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
					  $('#r_divFormulas').append("<input  type='checkbox' class='form-check-input'  name='ck_Formulas' id='" + cont +"' value='"+ vet_regras[cont]+"'>"+ numLinha + ": " + vet_regras[cont] + "</input></br>" );
	
				  }
				  numLinha++;
				$('#r_divFormulas').append("<input  type='checkbox' class='form-check-input'  name='ck_Formulas' id='finalVetor' value='"+ pergunta + "'>"+ numLinha +": " + pergunta + " # Pergunta </input></br>" );
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
	
	function f_buscaExercicio(btn_numExercicio){
//		if($('#numExercicio').val() == ""){
//			alert("Escreva o número de um exercícío!");
//		}
//		else{
				numExercicio = btn_numExercicio;
				regras = 0;
				numLinha = 0;
//		    	numExercicio = $('#numExercicio').val();
				$('#regrasAdicionadas').text("");
				$('#perguntaAdicionada').text("");
				$('#r_divFormulas').text("");
				$('#r_divNovasFormulas').text("");
				
				vet_regras = [];
//				var myData = { 'exercicio' : numExercicio};
//				//console.log("myData = " + myData);
//		            $.ajax({
//		
//		    	        url: 'http://127.0.0.1:8000/api/resolucao/exercicio',
//		    	        type: 'GET',
//		    	        callback: '?',
//		    	        data: myData,
//		    	        datatype: 'application/json',
//		    	        success: function(retorno) {
//		    	        	console.log("exercicio buscado "+ numExercicio + " = " + retorno);
//		    	        	exercicioBuscado = JSON.parse(retorno);
		    	        	 
//		    				$('#regra').prop('disabled', true);
//		    				$('#pergunta').prop('disabled', true);
//		    				$('#buttonRegra').hide();
//		    				$('#buttonPergunta').hide();
		    				var exercicio = vet_exercicios[numExercicio];
		    				var limiteFormulas = ((exercicio.length) -1);
		    				//console.log(limiteFormulas);
		    				for (var data = 0; data < limiteFormulas; data++) {
		    					  
		    					regras++;   					  
		    					  
		    					vet_regras.push(exercicio[data]);
		    					adicionadas = exercicio[data];
		    				
		    					$('#regrasAdicionadas').append("<br/>" + regras + ": " + adicionadas );
		    						
		    				}
		    				pergunta = exercicio[limiteFormulas];
		    				linhaPerg = regras+1;

		    				$('#perguntaAdicionada').append("<br/>" +linhaPerg + ": " + exercicio[limiteFormulas] );
		    				atualizaTela(tipoEx);
//		    				
//		    	        },
//		    	        error: function() { alert('Exercício inválido!'); },
//		    	    });
		
	}
	
	function f_Next(){
		alert("next");
	}
	
	
	function f_Gabarito(){
		
		switch(tipoEx){
		
		case "tableaux":
			f_GabTableaux();
			break;
		case "resolucao":
			f_GabResolucao();
			break;
		case "deducao":
			f_gabDeducao();
			break;
		}
			
	}
	
	
	function f_listaEx(){
		vet_exercicios[0] = "";
	for(var i=1;i<56;i++){
			$("#listaEx").append("<button id='"+ i + "' class='btn btn-info btn-sm dropdown-item' type='button' data-toggle='tooltip' data-placement='top' onclick='f_buscaExercicio(this.id)'>Ex."+ i +"</button>");
		}
	for(var i=1;i<56;i++){
		var myData = { 'exercicio' : i};
		indice = 1;
		$.ajax({
    		
	        url: 'http://127.0.0.1:8000/api/resolucao/exercicio',
	    	type: 'GET',
	        callback: '?',
	        data: myData, 
	        datatype: 'application/json',
	       
	        success: function(retorno) {
		        //console.log(retorno);

	        	vet_exercicios[indice] = JSON.parse(retorno);
	        	$("button[id='" + indice + "']").prop('title', retorno);
	        	indice++;
	        },
	        error: function() { alert('Exercicio não encontrado!'); },
	    });
		}

	}
	
	function sleep(milliseconds) {
		  var start = new Date().getTime();
		  for (var i = 0; i < 1e7; i++) {
		    if ((new Date().getTime() - start) > milliseconds){
		      break;
		    }
		  }
	}

	
