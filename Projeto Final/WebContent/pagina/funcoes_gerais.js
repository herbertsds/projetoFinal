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
	var categoriaExercicio;
	var vet_listas = [];
	var vet_idListas = [];
	var exercicios;
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
		$('#btn_SepararE').hide();
		$('#btn_SepararOU').hide();
		
		$('#btn_TransformarRegra').hide();
		$('#btn_ProximoPasso').hide();

		
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
				categoriaExercicio = 2;
				carregaTela("tableaux");
				$("#proximo").removeAttr("disabled");
		      
		    }
		 );
		
		$("#botaoResolucao").click( function()
			    {
					
					tipoEx = "resolucao";
					categoriaExercicio = 1;
					carregaTela("resolucao");
					
					$("#proximo").removeAttr("disabled");
					
			      
			    }
			 );
		$("#botaoDeducao").click( function()
			    {
					tipoEx = "deducao";
					categoriaExercicio = 3;

					carregaTela("deducao");
					$("#proximo").removeAttr("disabled");
			      
			    }
			 );
		
		$("#botaoSemantica").click( function()
			    {
					tipoEx = "semantica";
					categoriaExercicio = 0;

					carregaTela("semantica");
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
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			f_LimpaTipo();
			f_BuscaListas();

			break;
		
		case "resolucao":
			$("#divResolucao").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");

			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();

			f_LimpaTipo();
			f_BuscaListas();
			break;
		
		case "deducao":
			$("#divDeducao").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divResolucao").attr("style", 'display:none');
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");

			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			f_LimpaTipo();
			f_BuscaListas();

			break;
		case "semantica":
			$("#divSemantica").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divResolucao").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			f_LimpaTipo();
			f_BuscaListas();

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
				
			case "semantica":
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
	
	function f_SelecionaExercicio(btn_numExercicio){

				idnumExercicio = btn_numExercicio;
				numExercicio = idnumExercicio;
				console.log("id exercicio = " +idnumExercicio);
				regras = 0;
				numLinha = 0;

				$('#regrasAdicionadas').text("");
				$('#perguntaAdicionada').text("");
				$('#r_divFormulas').text("");
				$('#r_divNovasFormulas').text("");
				
				vet_regras = [];

		    	exercicioBuscado = vet_exercicios[idnumExercicio-1]['sentenca'].split(',');
		    	//console.log("f_buscaExercicio() = " + exercicio);
		    	var limiteFormulas = ((exercicioBuscado.length) -1);
		    				//console.log(limiteFormulas);
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
		    	
				$('#btn_TransformarRegra').show();
				$('#btn_ProximoPasso').show();
		
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
		case "semantica":	
			//f_gabSemantica();
		}
			
	}
	
	
	function f_BuscaListas(){
		
		$("#listaEx").empty();
		var myData = { 'id' : categoriaExercicio};

		// BUSCAR AS LISTAS NA CATEGORIA ESCOLHIDA
		$.ajax({
	        url: 'http://127.0.0.1:8000/api/exercicios/getListas',
	    	type: 'GET',
	        callback: '?',
	        data: myData, 
	        datatype: 'application/json',
	       
	        success: function(retorno) {
	        	listas = JSON.parse(retorno);
	      	    $('#listaEx').append("<h8 color='gray'>Total de Listas Encontradas: "+ listas.length.toString() +"</h8>");
	      	    
	      	    //console.log(categoriaExercicio);
	        	for(var i =0; i<listas.length;i++){
	        		vet_idListas[i] = listas[i]['id'];
	        		vet_listas[i]= listas[i]['nome'];
	        		
	        		$("#listaEx").append("<h6 class='dropdown-header'>&#10022; "+listas[i]['nome']+": </h6><div id='lista"+ vet_idListas[i]+"' </div>");

	
	        		
	        	}
	    		f_BuscaEx();

	        },
	        error: function() { console.log('ERRO: listas não encontradas!');
	        	$('#listaEx').append("<h8 color='gray'>> Total de Listas Encontradas: 0</br></br>Certifique-se de que existem listas ou verifique a conexão com o banco de dados</h8>");

	        },
	    });
		
	}
	
	function f_BuscaEx(){

		// BUSCAR EXERCICIOS DAS LISTAS
		indice_vet_Ex = 0;
		vet_exercicios= [];
		vet_exercicios = [];
		n=0;
		for( i=0; i<vet_idListas.length;i++){
			var myData = {'lista_id' : vet_idListas[i]};
			$.ajax({
		        url: 'http://127.0.0.1:8000/api/exercicios/listarExercicios',
		    	type: 'GET',
		        callback: '?',
		        data: myData, 
		        datatype: 'application/json',
		       
		        success: function(retorno) {
		        	exercicios = JSON.parse(retorno);
//		        	console.log(exercicios);
					
		        	for( k=0,j=indice_vet_Ex; j<(n+exercicios.length);j++, k++){
			    		vet_exercicios[j] = exercicios[k];
			    		indice_vet_Ex++;

					}
		        	n = indice_vet_Ex;

		        },
		        error: function() { console.log('ERRO: exercicios não encontrados!'); },
		    });
			

		}
		
	}	
	
	function f_PreencheDrop(){
		for(i=0; i < vet_idListas.length; i++){
			$('#lista'+vet_idListas[i]).empty();
			
		}
		for(j=0;j<vet_exercicios.length;j++){
			$('#lista'+vet_exercicios[j]['pivot']['listas_id']).append("<button id='"+ vet_exercicios[j]['id'] + 
					"' class='btn btn-info btn-sm dropdown-item' type='button' data-toggle='tooltip' data-placement='top'" +
					" onclick='f_SelecionaExercicio(this.id)'>Ex."+ vet_exercicios[j]['id'] +"</button>");
        	
			$("button[id='" + vet_exercicios[j]['id'] + "']").prop('title', vet_exercicios[j]['sentenca']);

			
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

	
function f_LimpaTipo(){
	console.log("limpando");
	$('#regrasAdicionadas').empty();
	$('#perguntaAdicionada').empty();
	vet_regras= [];
	pergunta = "";
	adicionadas="";
	formulaSimplificar ="";
	formulaId="";
	regras = 0;
	formulas_JSON = "";
	transformados_FNC = "";
	cont =0;
	perguntaNegada = false;
	numLinha = 0;
	numExercicio = "";
	vet_exercicios = [];
	indice = 0;
	vet_listas = [];
	vet_idListas = [];
	
	f_LimpaTableaux();
	f_LimpaResolucao();
	f_LimpaDeducao();
	f_LimpaSemantica();
}