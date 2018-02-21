<?php
//Versão que retorna um array fórmula deixando os campos específicos do Tableaux "declarados"
function resolveParentesesTableaux($form){
	global $listaConectivos;
	$auxForm['info']=array('esquerdo' => null, 'conectivo'=> null, 'direito'=>null);
	$auxForm['atualEsquerdo']=false;
	$auxForm['atualDireito']=false;
	$auxForm['atualCentral']=false;
	$auxForm['filhoEsquerdo']=null;
	$auxForm['filhoCentral']=null;
	$auxForm['filhoDireito']=null;
	$auxForm['pai']=null;
	$auxForm['formDisponiveis']=array();
	$auxForm['hashAtomos']=array();
	$aux;
	$esquerdo=true;
	$abreFormula=false;
	$contador=0;
	$not=false;

	converteConectivoSimbolo($form);
	//print "<br> Teste".$form;
	//Se for um átomo positivo
	//OBS: Talvez haja uma maneira mais apropriada de tratar isto
	//Em caso de erro nos cálculos, checar esta etapa
	//Número 3 é porque há dois parênteses e o átomo SEMPRE, por exemplo: (A)

	if(strlen($form)==3){
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$auxForm['direito']=$form;
		return $auxForm;
	}
	//Se for um átomo negativo
	//OBS: Talvez haja uma maneira mais apropriada de tratar isto
	//Em caso de erro nos cálculos, checar esta etapa
	//Número 4 é porque há dois parênteses e o átomo com negativo SEMPRE, por exemplo: (!A)

	if(strlen($form)==4){
		$form=substr($form, 1);		
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$auxForm['info']['direito']=$form;
		$auxForm['info']['conectivo']='not';
		return $auxForm;
	}

	//Se não for átomo, caso mais geral
	for ($i=0; $i<strlen($form); $i++){
		//Caso notnotnot
		if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!'){
			$form=substr($form, 4);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']='not';
			return $auxForm;
		}
		//Caso notnot
		if($form[$i]=='!' && $form[$i+1]=='!'){
			$form=substr($form, 3);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']='notnot';
			return $auxForm;
		}
		

		//Se achar o conectivo not no exterior de um parentese
		//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
		//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
		//é a abertura de um parenteses
		if($form[$i]=='!' && $form[$i+1]=='(' && $abreFormula==false){
			$not=true;
		}
		if($form[$i]=='('){
			$abreFormula=true;
			$contador++;
			
		}
		if($form[$i]==')'){
			$contador-=1;
			if($contador==0){
				$abreFormula=False;
			}
			
		}
		if($abreFormula==true){
			if((in_array($form[$i],$listaConectivos)) && ($contador==1)){
				if($not==true){
					$aux=$form[$i];
					converteConectivoNot($aux);
					$auxForm['info']['conectivo']=$aux;
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					converteConectivoExtenso($aux);
					$auxForm['info']['conectivo']=$aux;
					$esquerdo=false;
				}
				
			}
			if($esquerdo==true){
				$auxForm['info']['esquerdo']=$auxForm['info']['esquerdo'].$form[$i];
			}
			if($esquerdo==false){
				$auxForm['info']['direito']=$auxForm['info']['direito'].$form[$i];
			}
		}
	}
	$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
	$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
	return $auxForm;
}
function escolhaEficiente(&$listaFormulasDisponiveis,&$hashInicial,&$nosFolha){
	global $contador;
	global $raiz;
	//Verificação para saber se a função foi chamada mesmo
	//que todas os ramos já estejam fechados
	if (todasFechadas($nosFolha)) {
		print "<br>Todos os ramos já estão fechados<br>";
		print $contador."<br>";
		return;
	}
	$totalFormulasDisponives=array();
	juntarArrays($totalFormulasDisponives,$listaFormulasDisponiveis);
	juntarArrays($totalFormulasDisponives,$nosFolha);
	$conectivosEficientes=array("e","not_ou","not_implica");
	foreach ($totalFormulasDisponives as $key => $value) {
		if (in_array($value['info']['conectivo'],$conectivosEficientes)){
			if ($contador==0) {
				$raiz=$totalFormulasDisponives[$key];
				$raiz['formDisponiveis']=$totalFormulasDisponives;
				$raiz['hashAtomos']=$hashInicial;
				aplicaRegra($raiz,$raiz,$nosFolha);
				removerFormula($listaFormulasDisponiveis,$raiz['info']);
				return;
			}
			foreach ($nosFolha as $key => $value) {
				if ($nosFolha[$key]['info']!='fechado') {
					aplicaRegra($totalFormulasDisponives[$key],$nosFolha[$key],$nosFolha);
					//Verificação para saber se a função foi chamada mesmo
					//que todas os ramos já estejam fechados
					if (todasFechadas($nosFolha)) {
						print "<br>Todos os ramos já estão fechados<br>";
						print $contador."<br>";
						return;
					}
					removerFormula($listaFormulasDisponiveis,$nosFolha[$key]['info']);
					return;
				}
				print "<br>Erro, não houve nó para aplicar a fórmula<br>Verificar se todas as fórmulas estão fechadas<br>";
				return;
			}
		}
	}
	//Se nao existe mais conectivo que gere um ramo só, a escolha é arbitrária
	//Checar se a lista de fórmula não está vazia para garantir
	if(!empty($totalFormulasDisponives)){
		foreach ($totalFormulasDisponives as $key => $value) {
			if ($contador==0) {
				$raiz=$totalFormulasDisponives[$key];
				$raiz['formDisponiveis']=$totalFormulasDisponives;
				$raiz['hashAtomos']=$hashInicial;
				$noFolha=aplicaRegra($raiz);
				removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
				return;
			}
			foreach ($nosFolha as $key => $value) {
				if ($nosFolha[$key]['info']!='fechado') {
					aplicaRegra($totalFormulasDisponives[$key],$nosFolha[$key],$nosFolha);
					//Verificação para saber se a função foi chamada mesmo
					//que todas os ramos já estejam fechados
					if (todasFechadas($nosFolha)) {
						print "<br>Todos os ramos já estão fechados<br>";
						print $contador."<br>";
						return;
					}
					removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
					return;
				}
				print "<br>Erro, não houve nó para aplicar a fórmula<br>Verificar se todas as fórmulas estão fechadas<br>";
				return;
			}
		}
			
	}
	return;

}
function escolhaUsuario(&$listaFormulasDisponiveis,&$hashInicial,$formEscolhida,&$nosFolha,&$noFolhaEscolhido=NULL){
	global $contador;
	global $raiz;

	if ($contador==0) {
		$raiz=$formEscolhida;
		$raiz['formDisponiveis']=$listaFormulasDisponiveis;
		$raiz['hashAtomos']=$hashInicial;
		aplicaRegra($raiz,$raiz,$nosFolha);
		removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
		return;
	}
	else{
		aplicaRegra($formEscolhida,$noFolhaEscolhido,$nosFolha);
		removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
		return;
	}


}
//Serve apenas pra instânciar todos os campos do array fórmula para trabalhar evitando erros ou warnings
function criaFormulaTableaux(){
	$auxForm['info']=array('esquerdo' => null, 'conectivo'=> null, 'direito'=>null);
	$auxForm['atualEsquerdo']=false;
	$auxForm['atualDireito']=false;
	$auxForm['atualCentral']=false;
	$auxForm['filhoEsquerdo']=null;
	$auxForm['filhoCentral']=null;
	$auxForm['filhoDireito']=null;
	$auxForm['pai']=null;
	$auxForm['formDisponiveis']=array();
	$auxForm['hashAtomos']=array();
	return $auxForm;

}
function aplicaRegra(&$form,&$pai,&$nosFolha){
	global $contador;
	//Inicializando variáveis auxiliares com suas respectivas estruturas de dados
	$noAuxCen1=criaFormulaTableaux();
	$noAuxCen1['atualCentral']=true;
	$noAuxCen2=criaFormulaTableaux();
	$noAuxCen1['filhoCentral']=&$noAuxCen2;
	$noAuxCen2['pai']=&$noAuxCen1;
	$noAuxCen2['atualCentral']=true;
	$noAuxEsq=criaFormulaTableaux();
	$noAuxEsq['atualEsquerdo']=true;
	$noAuxDir=criaFormulaTableaux();
	$noAuxDir['atualDireito']=true;

	//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
	//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
	if ($contador!=0) {
		removerFormula($nosFolha,$pai['info']);
	}
	//Verificação para o caso de haver tentativa de aplciar fórmula
	//num ramo que já foi fechado
	if ($pai['filhoCentral']=='fechado') {
		print "<br>Este ramo já foi fechado<br>O nó folha é<br>";
		print_r($pai['info']);
		return;
	}
	//Verifico o conectivo da fórmula que foi aplicada
	//NOTA:O pai não necessariamente será o mesmo cara o qual está sendo aplicada a fórmula
	switch ($form['info']['conectivo']) {
		//Regra 1
		case 'e':
			
			$pai['filhoCentral']=&$noAuxCen1;
			$noAuxCen1['pai']=&$pai;
			//Inicialização das variáveis auxiliáres
			$noAuxCen1['info']['direito']=$form['info']['esquerdo'];
			$noAuxCen2['info']['direito']=$form['info']['direito'];
			//Inicialização dos dados que são compartilhados com o pai
			$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
			$noAuxCen2['formDisponiveis']=$pai['formDisponiveis'];
			$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
			$noAuxCen2['hashAtomos']=$pai['hashAtomos'];

			//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
			//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
			//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
			if(checaAtomico($noAuxCen1['info'])){
				if (casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info']['direito'])) {
					$noAuxCen2['filhoCentral']='fechado';
				}
				$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
				$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
			}
			elseif(!checaAtomico($noAuxCen1['info'])) {
				array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
			}			
			
			if(checaAtomico($noAuxCen2['info'])){
				if (casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info']['direito'])) {
					$noAuxCen2['filhoCentral']='fechado';
				}
				$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
				$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
			}
			elseif(!checaAtomico($noAuxCen2['info'])) {
				array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
				array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
			}
			removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
			removerFormula($noAuxCen2['formDisponiveis'],$form['info']);	
			adicionaArray($nosFolha, $noAuxCen2);
			return;

		//Regra 2
		case 'ou':
			$pai['filhoCentral']=&$noAuxCen1;
			$noAuxCen1['pai']=&$pai;
			//Inicialização das variáveis auxiliáres
			$noAuxCen1['info']['direito']=$form['info']['esquerdo'];
			$noAuxCen2['info']['direito']=$form['info']['direito'];
			//Inicialização dos dados que são compartilhados com o pai
			$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
			$noAuxCen2['formDisponiveis']=$pai['formDisponiveis'];
			$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
			$noAuxCen2['hashAtomos']=$pai['hashAtomos'];

			//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
			//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
			//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
			if(checaAtomico($noAuxCen1['info'])){
				if (casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info']['direito'])) {
					$noAuxCen2['filhoCentral']='fechado';
				}
				$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
				$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
			}
			elseif(!checaAtomico($noAuxCen1['info'])) {
				array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
			}			
			
			if(checaAtomico($noAuxCen2['info'])){
				if (casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info']['direito'])) {
					$noAuxCen2['filhoCentral']='fechado';
				}
				$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
				$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
			}
			elseif(!checaAtomico($noAuxCen2['info'])) {
				array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
				array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
			}
			removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
			removerFormula($noAuxCen2['formDisponiveis'],$form['info']);	
			adicionaArray($nosFolha, $noAuxCen2);
			return;		
		case 'implica':
			# code...
			break;
		
		case 'notnot':
			# code...
			break;
		
		case 'not_e':
			# code...
			break;
		
		case 'not_ou':
			# code...
			break;
		
		case 'not_implica':
			# code...
			break;
		//Caso extra
		case 'not':
			//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
			if(checaAtomico($pai['info'])){
				if(casarFormula($pai['hashAtomos'],$pai['info'])){
					$pai['filhoCentral']='fechado';
				}
				$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo'] == 'not' ? '0':'1';	
			}
			return;
		default:
			# code...
			break;
	}
}
function checaAtomico($form){
	//@ colocado para previnir que fórmulas não instanciadas deem warning
	if (@$form['esquerdo']==NULL && (@$form['conectivo']==NULL || @$form['conectivo']='not')) {
		return true;
	}
	else{
		return false;
	}

	
}

//Se houver digitação incorreta gera uma exceção (trabalhar na exceção depois)
function negaPergunta($listaFormulas,$tamanho){
	//Nega a pergunta
	$listaFormulas[$tamanho-1]="not".$listaFormulas[$tamanho-1];
	//Tratar a entrada, verificação de digitação correta
	foreach ($listaFormulas as $key => $value) {
		verificaFormulaCorreta($listaFormulas[$key]);
		$entradaConvertida[$key]=resolveParentesesTableaux($listaFormulas[$key]);
	}
	
	return $entradaConvertida;
}
function removerFormula(&$listaFormulas,$form){
	foreach ($listaFormulas as $key => $value) {
		if ($value['info']==$form) {
			unset($listaFormulas[$key]);
			return;
		}
	}
}
function adicionaArray(&$array,&$valor){
	$tam=count($array);
	if ($tam!=0) {
		$array[$tam+1]=&$valor;
	}
	else{
		$array[0]=&$valor;
	}
}
function casarFormula($hash,$form){
	$aux=$form == "not" ? '0':'1';
	foreach ($hash as $key => $value) {			
		//Verifico se alguma vez esse cara já foi setado na hash
		if(!is_null($hash[$key])){
			if(($hash[$key]==!$aux) && ($form==$key)){
				return true;
			}				
		}
	}
	return false;
}
function juntarArrays(&$array1,$array2){
	foreach ($array2 as $key => $value) {
		array_push($array1, $array2[$key]);
	}
}
function todasFechadas($nosFolha){
	global $contador;
	$tam=count($nosFolha);
	$c=0;
	foreach ($nosFolha as $key => $value) {
		if ($value['info']=='fechada') {
			$c++;
		}
	}
	if ($c==$tam && $contador!=0) {
		return true;
	}
	else{
		return false;
	}
}
function imprimeArvore(&$no){
	if (@$no['info']!=NULL) {
		print_r($no['info']);
		verificaStatusNo($no);
	}
	
	if(@$no['filhoCentral']!=NULL && @$no['filhoCentral']!='fechado'){
		@imprimeArvore($no['filhoCentral']);
	}
	if(@$no['filhoEsquerdo'] && @$no['filhoEsquerdo']!='fechado'){
		imprimeArvore(@$no['filhoEsquerdo']);
	}
	if(@$no['filhoDireito'] && @$no['filhoDireito']!='fechado'){
		imprimeArvore(@$no['filhoDireito']);
	}
}

//Função utilizada somente por imprimArvore para ajustaro formato da impressão
function verificaStatusNo(&$no){
	switch($no){
		case @$no['atualCentral']:
			print "  Central <br>";
			break;
		case @$no['atualEsquerdo']:
			print "  Esquerda ------ ";
			break;
		case @$no['atualDireito']:
			print "  Direita <br>";
			break;
		default:
			if(@$no['info']=="fechado"){
				break;
			}
			print "<br>Nó não categorizado<br>";
	}
}
?>
