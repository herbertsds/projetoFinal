// ao clicar em + armazernar as regras e a pergunta em um array
// ir apresentando dinamicamente as regras ja adicionadas em uma area na lateral direita 
// ao clicar em next passar para a tela de selecao de metodo
// ao selecionar o metodo, dar algum destaque a ele 
/* ao clicar em next, passar para a tela de exercicio, apresentando as regras aplicaveis e uma area para a arvore
ser montada dinamicamente */ 
// nao permitir usar regras repetidas ou invalidas


	var vet_regras = [];
	var pergunta;
	
	function load(){
		$('#regra').val("");
		$('#pergunta').val("");
    	//alert("onload ok");
    
          	
	}
	

	$('input[type=radio]').change( function() {
		   alert("test");   
		});
	
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
			}
		}	
	}
	
	// FALTA PERMITIR EXCLUSAO/ALTERACAO DA PERGUNTA E DE REGRAS
	
	// ######################################################################################################
	
	// ######### ESCOLHA DO TIPO DE EXERCICIO ###############################################################
		// ao selecionar o tipo, mudar a tela de resolução


	// ######################################################################################################
	
	// ######### EXECUCAO DO EXERCICIO ######################################################################
	
	// ######################################################################################################
	
		
	
	
	
	
	
	