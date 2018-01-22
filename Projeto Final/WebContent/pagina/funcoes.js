// ao clicar em + armazernar as regras e a pergunta em um array
// ir apresentando dinamicamente as regras ja adicionadas em uma area na lateral direita 
// ao clicar em next passar para a tela de selecao de metodo
// ao selecionar o metodo, dar algum destaque a ele 
/* ao clicar em next, passar para a tela de exercicio, apresentando as regras aplicaveis e uma area para a arvore
ser montada dinamicamente */ 
// nao permitir usar regras repetidas ou invalidas


	var vet_regras = [];
	var pergunta;
	
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
			alert("Regra inválida!");
		}
		else{
			pergunta = $('#pergunta').val().replace(/\s/gi, '');
			$('#perguntaAdicionada').append("<br/>" + $('#pergunta').val() );
			$('#pergunta').val("");
		}	
	}
	
	// ######################################################################################################
	
	// ######### ESCOLHA DO TIPO DE EXERCICIO ###############################################################
	
	
	
	
	
	
	
	
	