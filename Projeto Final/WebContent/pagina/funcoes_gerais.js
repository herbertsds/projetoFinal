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
	var inicio =0;
	var manual = false;
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

		$('#btn_Verificar').hide();
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
        $("#1aba").click( function()
    		    {
    		console.log("1aba");

//    		$("#tiposLogica").removeAttr("style");
    		$("#tipo").removeAttr("style");  
    		$("#metodoProposicional").attr("style",'display:none');    		      
    		$("#metodoLpo").attr("style",'display:none');  
    		    }
    		 );
        $("#botaoLpo").click( function()
        		{

    		$("#metodoLpo").removeAttr("style");
    		$("#tipo").attr("style",'display:none');    		      
    		    }
    		 );
        
        $("#botaoLpo2").click( function()
    		    {
    		console.log("aqui");

    		$("#metodoLpo").removeAttr("style");
    		$("#tipo").attr("style",'display:none');  
    		$("#metodoProposicional").attr("style",'display:none');    		      

    		    }
    		 );
        $("#botaoProposicional").click( function()
			{	
        		console.log("aqui");
        		$("#metodoProposicional").removeAttr("style");
        		$("#tipo").attr("style",'display:none');


 		    }
 		 );
        
        $("#botaoProposicional2").click( function()
    			{	
            		console.log("aqui");
            		$("#metodoProposicional").removeAttr("style");
            		$("#tipo").attr("style",'display:none');
    	    		$("#metodoLpo").attr("style",'display:none');    		      


     		    }
     		 );
	     $("#botaoTableaux").click( function()
		    {
				tipoEx = "tableaux";
				categoriaExercicio = 2;
				carregaTela("tableaux");
				$("#proximo").removeAttr("disabled");
	    		$("#metodoProposicional").attr("style",'display:none');    		      
	    		$("#metodoLpo").attr("style",'display:none');    		      

		      
		    }
		 );
		
		$("#botaoResolucao").click( function()
			    {
					
					tipoEx = "resolucao";
					categoriaExercicio = 1;
					carregaTela("resolucao");
					
					$("#proximo").removeAttr("disabled");
		    		$("#metodoProposicional").attr("style",'display:none');    		      
		    		$("#metodoLpo").attr("style",'display:none');   
			      
			    }
			 );
		$("#botaoDeducao").click( function()
			    {
					tipoEx = "deducao";
					categoriaExercicio = 3;

					carregaTela("deducao");
					$("#proximo").removeAttr("disabled");
		    		$("#metodoProposicional").attr("style",'display:none');    		      
		    		$("#metodoLpo").attr("style",'display:none');   
			    }
			 );
		
		$("#botaoSemantica").click( function()
			    {
					tipoEx = "semantica";
					categoriaExercicio = 6;

					carregaTela("semantica");
					$("#proximo").removeAttr("disabled");
		    		$("#metodoProposicional").attr("style",'display:none');    		      
		    		$("#metodoLpo").attr("style",'display:none');   
			    }
			 );
	     $("#botaot_lpo").click( function()
	 		    {
	 				tipoEx = "t_lpo";
	 				categoriaExercicio = 4;
	 				carregaTela("t_lpo");
	 				$("#proximo").removeAttr("disabled");
		    		$("#metodoProposicional").attr("style",'display:none');    		      
		    		$("#metodoLpo").attr("style",'display:none');   
	 		    }
	 		 );
	 		
		
	});
	
	function carregaTela(exercicio){
		switch (exercicio) {
		case "tableaux":
			$("#divt_lpo").attr("style", 'display:none');
			$("#divTableaux").removeAttr("style");
			$("#divResolucao").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			$('#div_ListasSup').attr("style", 'display : none');
			$('#div_Escolha').removeAttr("style");
			$('#adicaoExercicio').attr("style", 'display : none');
			$('#regra').prop('disabled', false);
			$('#pergunta').prop('disabled', false);
			$('#buttonRegra').show();
			$('#buttonPergunta').show();
			
			f_LimpaTipo();
//			f_BuscaListas();

			break;
		
		case "resolucao":
			$("#divResolucao").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#divt_lpo").attr("style", 'display:none');
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			$('#div_ListasSup').attr("style", 'display : none');
			$('#div_Escolha').removeAttr("style");
			$('#adicaoExercicio').attr("style", 'display : none');
			$('#regra').prop('disabled', false);
			$('#pergunta').prop('disabled', false);
			$('#buttonRegra').show();
			$('#buttonPergunta').show();
			
			f_LimpaTipo();
//			f_BuscaListas();
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
			$("#divt_lpo").attr("style", 'display:none');
			$('#div_ListasSup').attr("style", 'display : none');
			$('#div_Escolha').removeAttr("style");
			$('#adicaoExercicio').attr("style", 'display : none');
			$('#regra').prop('disabled', false);
			$('#pergunta').prop('disabled', false);
			$('#buttonRegra').show();
			$('#buttonPergunta').show();
			
			f_LimpaTipo();
//			f_BuscaListas();

			break;
		case "semantica":
			$("#divSemantica").removeAttr("style");
			$("#divTableaux").attr("style", 'display:none');
			$("#divResolucao").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#divt_lpo").attr("style", 'display:none');

			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			$('#div_ListasSup').attr("style", 'display : none');
			$('#div_Escolha').removeAttr("style");
			$('#adicaoExercicio').attr("style", 'display : none');
			$('#regra').prop('disabled', false);
			$('#pergunta').prop('disabled', false);
			$('#buttonRegra').show();
			$('#buttonPergunta').show();
			
			f_LimpaTipo();
//			f_BuscaListas();
			break;
			
		case "t_lpo":
			console.log("aqui");
			$("#divt_lpo").removeAttr("style");
			
			$("#divTableaux").attr("style", 'display:none');
			$("#divResolucao").attr("style", 'display:none');
			$("#divDeducao").attr("style", 'display:none');
			$("#divSemantica").attr("style", 'display:none');
			$("#exercicio").removeAttr("style");
			$("#liExercicio").removeAttr("style");
			$("#liExecucao").removeAttr("style");
			$('#tabExercicio').click();
			
			$('#div_ListasSup').attr("style", 'display : none');
			$('#div_Escolha').removeAttr("style");
			$('#adicaoExercicio').attr("style", 'display : none');
			$('#regra').prop('disabled', false);
			$('#pergunta').prop('disabled', false);
			$('#buttonRegra').show();
			$('#buttonPergunta').show();
			
			f_LimpaTipo();
//			f_BuscaListas();			

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
//				  for (cont = 0; cont in vet_regras; cont++){
//					  numLinha = cont+1;
//					  
//						$('#t_divFormulas').append("<ul id='" + cont+ "'><li>"+ numLinha +": " + vet_regras[cont]);
//	
//				  }
//				  numLinha++;
//				$('#t_divFormulas').append("<ul id='finalVetor'><li>"+ numLinha +": " + pergunta + " # Pergunta</li></ul>" );
//				for(i = 0; i<=vet_regras.length +1; i++){
//					$('#t_divFormulas').append("</li></ul>");
//				}
			case "resolucao":
				  for (cont = 0; cont in vet_regras; cont++){
					  numLinha = cont+1;
					  $('#r_divFormulas').append("<input  type='checkbox' class='form-check-input'  name='ck_Formulas' id='" + cont +"' value='"+ vet_regras[cont]+"'> " + numLinha + ": " + vet_regras[cont] + "</input></br>" );
	
				  }
				  numLinha++;
				$('#r_divFormulas').append("<input  type='checkbox' class='form-check-input'  name='ck_Formulas' id='finalVetor' value='"+ pergunta + "'> "+ numLinha +": " + pergunta + " # Pergunta </input></br>" );
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
				
			case "t_lpo":
				  for (cont = 0; cont in vet_regras; cont++){
					  numLinha = cont+1;
						$('#t_lpo_divFormulas').append("<p id='" + cont+ "'>"+ numLinha +": " + vet_regras[cont] + "</p>" );
	
				  }
				  numLinha++;
				$('#t_lpo_divFormulas').append("<p id='finalVetor'>"+ numLinha +": " + pergunta + " # Pergunta </p>" );
				break;
		}
	}	
//-------------------------------------------------------------------------------------------------------
	
	function f_Escolha(forma){
		switch(forma){
			case "novoExercicio":
				//console.log("aqui");
//				f_LimpaTipo();
				$('#adicaoExercicio').removeAttr("style");
				$('#div_Escolha').attr("style", 'display : none');
				$('#div_ListasSup').attr("style", 'display : none');

				break;
				
			case "listarEx":
				//console.log("listar");
//				f_LimpaTipo();
				
				f_BuscaListas();

				
				$('#div_ListasSup').removeAttr("style");
				$('#div_Escolha').attr("style", 'display : none');
				$('#adicaoExercicio').attr("style", 'display : none');
				break;
		}
		
	}
	function f_PreencherDivListas(){

//		console.log("inicio " + inicio);
//		console.log(vet_exercicios.length);
		for(j=inicio;j<vet_exercicios.length;j++){
			$('#div_ListaEx'+vet_exercicios[j]['pivot']['listas_id']).append("<button id='"+ vet_exercicios[j]['id'] + 
					"' class='btn btn-info btn-sm dropdown-item' type='button' data-toggle='tooltip' data-placement='top'" +
					" onclick='f_SelecionaExercicio(this.id)'>Ex."+ vet_exercicios[j]['id'] +"</button>");
        	
			$("button[id='" + vet_exercicios[j]['id'] + "']").prop('title', vet_exercicios[j]['sentenca']);
			console.log("listando...");
			
		}

	}
// ############# ADICAO DE REGRAS E PERGUNTA ############################################################
	
	function f_AddRegra(){
		if($('#regra').val() == ""){
			alert("Regra inválida!");
		}
		else{
			var myData = {
			        'formulas': $('#regra').val()
			    };
		
			$.ajax({

			url: 'http://127.0.0.1:8000/api/exercicios/verificaFormula',
	    	type: 'GET',
	        callback: '?',
	        data: myData, 
	        datatype: 'application/json',
	       
	        success: function(retorno) {
	        	
	        	console.log(retorno);
	        	if(retorno ==0){
					regras++;
		
					vet_regras.push($('#regra').val().replace(/\s/gi, ''));
					adicionadas = $('#regra').val();
					
					$('#regrasAdicionadas').append("<br/>" + regras + ": " + adicionadas );
					$('#regra').val("");
	        	}
	        	
	        	else{
	        		alert("Fórmula inválida! Verifique os parênteses e conectivos!" );

	        	}
	        },

			
			error: function() {
				
				console.log('ERRO: fórmula invalida');
				},
		    });
		}
	} 
	
	function f_AddPergunta(){
		if($('#pergunta').val() == ""){
			alert("Pergunta inválida!");
		}
		else{
			var myData = {
			        'formulas': $('#pergunta').val().replace(/\s/gi, '')
			    };
		
			$.ajax({

			url: 'http://127.0.0.1:8000/api/exercicios/verificaFormula',
	    	type: 'GET',
	        callback: '?',
	        data: myData, 
	        datatype: 'application/json',
	       
	        success: function(retorno) { 
	        	
	        if(retorno==0){
	        	
				encerrado = confirm("Tem certeza que todas as regras do BD foram adicionadas?");
				
				if(encerrado){
					pergunta = $('#pergunta').val().replace(/\s/gi, '');
					exercicioBuscado = [];
					for(i=0;i in vet_regras;i++){
						exercicioBuscado[i] = vet_regras[i];
					}
					
					exercicioBuscado[exercicioBuscado.length] = pergunta;
					linhaPerg = regras + 1;
					$('#perguntaAdicionada').append("<br/>" +linhaPerg + ": " + $('#pergunta').val() );
					$('#pergunta').val("Pergunta Adicionada!!");
					$('#regra').prop('disabled', true);
					$('#pergunta').prop('disabled', true);
					$('#buttonRegra').hide();
					$('#buttonPergunta').hide();
					atualizaTela(tipoEx);
					$('#tabExecucao').click();
					manual = true;
				}
	        }
	        else{
	        	alert("Pergunta Inválida!");
	        	
	        	}
	        },
	    	
			error: function() {
				
				console.log('ERRO: fórmula invalida');
				},
		    });		
		}
		
	}
	
	// FALTA PERMITIR EXCLUSAO/ALTERACAO DA PERGUNTA E DE REGRAS
	
	function f_SelecionaExercicio(btn_numExercicio){
		f_LimpaDesenvolvimento();
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

		    	exercicioBuscado = vet_exercicios[idnumExercicio]['sentenca'].split(',');
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
				$('#regra').prop('disabled', true);
				$('#pergunta').prop('disabled', true);
				$('#buttonRegra').hide();
				$('#buttonPergunta').hide();
		    	atualizaTela(tipoEx);
				$('#tabExecucao').click();

				$('#btn_TransformarRegra').show();
//				$('#btn_ProximoPasso').show();
		
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
		case "t_lpo":
			f_Gabt_lpo();
			break;
		}
			
	}
	
	
	function f_BuscaListas(){
		
		$("#listaEx").empty();
		$("#div_ListasInf").empty();
		vet_listas = [];
		vet_idListas= [];
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
	    		$("#div_ListasSup").append("<div class='col-sm-14 col-sm-offset-1' id='div_ListasInf'></div>");

	    		$("#div_ListasInf").empty();

	      	    $('#div_ListasInf').append("<br/><h8 color='gray'>Total de Listas Encontradas: "+ listas.length.toString() +"</h8>");
	      	    
	      	    //console.log(categoriaExercicio);
	        	for(var i =0; i<listas.length;i++){
	        		vet_idListas[i] = listas[i]['id'];
	        		vet_listas[i]= listas[i]['nome'];
	        		
	        		$("#div_ListasInf").append("<h6 >&#10022; "+listas[i]['nome']+": </h6><div id='div_ListaEx"+ vet_idListas[i]+"' </div>");


	        		
	        	}
	    		for(i=0; i < vet_idListas.length; i++){
	    			$('#div_ListaEx'+vet_idListas[i]).empty();
	    			
	    		}
	      	    
	      	    
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
	        	$('#div_ListasInf').append("<h8 color='gray'>> Total de Listas Encontradas: 0</br></br>Certifique-se de que existem listas ou verifique a conexão com o banco de dados</h8>");

	        },
	    });
		
	}
	
	function f_BuscaEx(){

		// BUSCAR EXERCICIOS DAS LISTAS
		indice_vet_Ex = 0;
		vet_exercicios= [];
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
		        	//console.log(exercicios);
					inicio =  exercicios[0]['id'];
		        	for( k=0,j=indice_vet_Ex; j<(n+exercicios.length);j++, k++){
			    		vet_exercicios[exercicios[k]['id']] = exercicios[k];
			    		indice_vet_Ex++;

					}
		        	n = indice_vet_Ex;
					f_PreencherDivListas();
					

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
	f_Limpat_lpo();
}

function f_LimpaDesenvolvimento(){
	f_LimpaTableaux();
	f_LimpaResolucao();
	f_LimpaDeducao();
	f_LimpaSemantica();
	f_Limpat_lpo();
}