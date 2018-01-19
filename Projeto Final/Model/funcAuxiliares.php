<?php

//Função que pega uma fórmula da árvore e aplica a regra corresponte
//Para formulas cujo resultado da aplicação resulta em single note devem chamar aplicaFormula duas vezes para que o case not adicione os �tomos na hash
//Por exemplo, Av¬B ou A->B.
//$listaConectivos=array("^","v","->","¬");

function aplicaFormula(Formula $raiz){
	global $fork;
	global $hash;
	switch ($raiz->getConectivo()) {
		//Regra 1
		case 'e':
			return array($raiz->getEsquerdo(),$raiz->getDireito());
			//Regra 2
		case 'ou':
			$fork = true;
			return array($raiz->getEsquerdo(),$raiz->getDireito());
			//Tratamento de Single not
		case 'not':
			//Checa se � composto ou átomo
			if(!is_object($raiz->getDireito())){
				//print "Sei que é negativo<br>";
				$hash[$raiz->getDireito()][] = 'negativo';
			}
			//Se não for objeto chama de novo para aplicar a regra interior
			break;
			//Regra 3
		case 'implica':
			$fork = true;
			$aux1= new Formula();
			//O lado esquero da formula vira not
			//Atomos negativos são sempre adicionados no lado direito de uma Formula
			$aux1->setConectivo("not");
			$aux1->setDireito($raiz->getEsquerdo());
			return array($aux1,$raiz->getDireito());
			//Regra 4
		case 'notnot':
			if(!is_object($raiz->getDireito())){
				$hash[$raiz->getDireito()][] = 'positivo';
			}
			return array($raiz->getDireito());
			//Regra 5
		case 'not_e';
		$fork = true;
		$aux1 = new Formula();
		$aux2 = new Formula();
		$aux1->setConectivo('not');
		$aux1->setDireito($raiz->getEsquerdo());
		$aux2->setConectivo('not');
		$aux2->setDireito($raiz->getDireito());
		return array($aux1,$aux2);
		//Regra 6
		case 'not_ou';
		$aux1 = new Formula();
		$aux2 = new Formula();
		$aux1->setConectivo('not');
		$aux1->setDireito($raiz->getEsquerdo());
		$aux2->setConectivo('not');
		$aux2->setDireito($raiz->getDireito());
		return array($aux1,$aux2);
		//Regra 7
		case 'not_implica';
		$aux1 = new Formula();
		$aux1->setConectivo('not');
		$aux1->setDireito($raiz->getDireito());
		return array($raiz->getEsquerdo(),$aux1);
		default:
			# Tratamento de um poss�vel erro
			break;
	}
}
//Função fork para
function forkArv(&$arvore,&$retorno,$indice){
	global $fork;
	global $hash;
	
	if($fork == true){
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[$indice]->usaFormula();
			//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
			if(!is_object($valor)){
				$hash[$valor][] = 'positivo';
			}
		}
		$fork = false;
	}
	else{
		if (is_array($retorno) || is_object($retorno)){
			foreach ($retorno as $chave => $valor) {
				$arvore[$indice]->usaFormula();
				$arvore[] = $valor;
			}
		}
	}
}

//Função recebe um ponteiro para uma String fórmula e converte
//Seus conectivos para símbolos
function converteConectivoSimbolo(&$form){
	$form=str_replace('e','^',$form);
	$form=str_replace('ou','v',$form);
	$form=str_replace('implica','-',$form);
	$form=str_replace('not','!',$form);
}

//Função recebe um ponteiro para uma String fórmula e converte
//Seus conectivos de símbolos para o nome por extenso
function converteConectivoExtenso(&$form){
	$form=str_replace('^','e',$form);
	$form=str_replace('v','ou',$form);
	$form=str_replace('-','implica',$form);
	$form=str_replace('!','not',$form);
}

//Função auxiliar para facilitar a extração de conectivos de fórmulas com not
//Uso desta função é bem restrito e no momento do "parsing" das fórmulas
function converteConectivoNot(&$form){
	$form=str_replace('^','not_e',$form);
	$form=str_replace('v','not_ou',$form);
	$form=str_replace('-','not_implica',$form);
}


//Função para verificação da corretude das formulas com parenteses
//Use no lado direito ou esquerdo de um objeto formula
//Recebe uma STRING e retorna erro caso haja, ou Ok caso esteja correta
//OBSERVAÇÃO IMPORTANTE
//Verificar durante a etapa de fazer o WebService funcionar
//Um tratamento para fórmulas incorretas, a aplicação NÃO pode encerrar
function verificaFormulaCorreta(&$form){
    
	$contador=0;
	$contador2=0;
	$i;
	$abreFormula=false;
	$esquerdo=true;
	$subFormula=0;
	//$auxFormula[];
	for ($i=0; $i<strlen($form); $i++){
		
		//Abriu parenteses
		if($form[$i]=='('){
			$contador+=1;
			if($form[$i+1]!='('){
				$abreFormula=true;
				$subFormula++;
			}
		}
		//Fecha parenteses
		elseif($form[$i]==')'){
			$contador-=1;
			if($contador<0){
				#Criar um tratamento aqui
				//Se o usuário digitar a entrada vamos precisar usar uma rotina de correção e chamar verifica recursivamente
				print "Fórmula com digitação incorreta<br>";
				print $form;
				print "<br>";
				exit(1);
			}
			
			if($abreFormula==true){
				$abreFormula=false;
			}
			$contador2++;
		}
		
	}
	if($contador!=0){
		#Criar um tratamento aqui
		//Se o usuário digitar a entrada vamos precisar usar uma rotina de correção e chamar verifica recursivamente
		print $form;
		print "<br>";
	    print "Fórmula com digitação incorreta";
		exit(1);
	}
	print "Fórmula Ok<br>";
	
}


//Recebe uma String fórmula (Não é um objeto fórmula), remove os parenteses mais externos
//e devolve um objeto Formula com os dois lados separados e o conectivo mais externo classificado
//Basicamente faz um "PARSING"
function resolveParenteses($form){
	global $listaConectivos;
	$auxForm = new Formula();
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
		$auxForm->setDireito($form);
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
		$auxForm->setDireito($form);
		$auxForm->setConectivo("not");
		return $auxForm;
	}

	//Se não for átomo, caso mais geral
	for ($i=0; $i<strlen($form); $i++){
		//Caso notnotnot
		if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!'){
			$form=substr($form, 4);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm->setDireito($form);
			$auxForm->setConectivo("not");
			return $auxForm;
		}
		//Caso notnot
		if($form[$i]=='!' && $form[$i+1]=='!'){
			$form=substr($form, 3);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm->setDireito($form);
			$auxForm->setConectivo("notnot");
			return $auxForm;
		}
		

		//Se achar o conectivo not no exterior de um parentese
		//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
		//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
		//é a abertura de um parenteses
		if($form[$i]=='!' && $form[$i+1]=='('){
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
					$auxForm->setConectivo($aux);
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					converteConectivoExtenso($aux);
					$auxForm->setConectivo($aux);
					$esquerdo=false;
				}
				
			}
			if($esquerdo==true){
				$auxForm->setEsquerdo($auxForm->getEsquerdo().$form[$i]);
			}
			if($esquerdo==false){
				$auxForm->setDireito($auxForm->getDireito().$form[$i]);
			}
		}
	}
	$auxForm->setEsquerdo(substr($auxForm->getEsquerdo(), 1));
	$auxForm->setDireito(substr($auxForm->getDireito(), 1));
	return $auxForm;
}
//Versão que retorna um array fórmula
function resolveParenteses2($form){
	global $listaConectivos;
	$auxForm['esquerdo']=NULL;
	$auxForm['conectivo']=NULL;
	$auxForm['direito']=NULL;
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
		$auxForm['direito']=$form;
		$auxForm['conectivo']='not';
		return $auxForm;
	}

	//Se não for átomo, caso mais geral
	for ($i=0; $i<strlen($form); $i++){
		//Caso notnotnot
		if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!'){
			$form=substr($form, 4);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['direito']=$form;
			$auxForm['conectivo']='not';
			return $auxForm;
		}
		//Caso notnot
		if($form[$i]=='!' && $form[$i+1]=='!'){
			$form=substr($form, 3);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['direito']=$form;
			$auxForm['conectivo']='notnot';
			return $auxForm;
		}
		

		//Se achar o conectivo not no exterior de um parentese
		//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
		//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
		//é a abertura de um parenteses
		if($form[$i]=='!' && $form[$i+1]=='('){
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
					$auxForm['conectivo']=$aux;
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					converteConectivoExtenso($aux);
					$auxForm['conectivo']=$aux;
					$esquerdo=false;
				}
				
			}
			if($esquerdo==true){
				$auxForm['esquerdo']=$auxForm['esquerdo'].$form[$i];
			}
			if($esquerdo==false){
				$auxForm['direito']=$auxForm['direito'].$form[$i];
			}
		}
	}
	$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
	$auxForm['direito']=substr($auxForm['direito'], 1);
	return $auxForm;
}


//Recebe uma String formula como entrada, caso a formula não tenha
//nenhum problema de digitação, como por exemplo, os parenteses,
//a formula sera transformada em um objeto Formula pronto para
//ser inserido na árvore.
function processaEntrada($form,&$objForm) {
	verificaFormulaCorreta($form);
	$objForm=resolveParenteses($form);    
}
//Recebe um array de Strings formula e os adiciona na árvore para inicializar o processamento
//Um print está sendo colocado para controle interno, mas possivelmente será retirado na versão final
//Faz a negação da pergunta, que é sempre o último elemento do array
function inicializaArvore(&$arrayForm,&$arvore){

	for ($i=0; $i < count($arrayForm); $i++) { 
		converteConectivoSimbolo($arrayForm[$i]);
		print "Processando etapa ".$i."... ".$arrayForm[$i]."<br><br>";

		//Nega a pergunta
		if(($i+1)==count($arrayForm)){
			$arrayForm[$i]="not".$arrayForm[$i];
		}

		$auxForm = new Formula();
		processaEntrada($arrayForm[$i],$auxForm);
		$arvore[]=$auxForm;


	}
}

function imprime_r($array){
	for ($i=0; $i < count($array) ; $i++) {
		print "Formula ".$i." - "; 
		print_r($array[$i]);
		print "<br>";
	}
}


//Função recursiva para imprimir as fórmulas de cada Nó da Árvore
function imprimeDescendo($no){


	print_r($no->info);
	verificaStatusNo($no);

	if($no->filhoCentral){
		imprimeDescendo($no->filhoCentral);
	}
	if($no->filhoEsquerda){
		imprimeDescendo($no->filhoEsquerda);
	}
	if($no->filhoDireita){
		imprimeDescendo($no->filhoDireita);
	}

}
//Função utilizada somente por imprimDescendo para ajustaro formato da impressão
function verificaStatusNo($no){
	switch($no){
		case $no->central:
			print "  Central <br>";
			break;
		case $no->esquerda:
			print "  Esquerda ------ ";
			break;
		case $no->direita:
			print "  Direita <br>";
			break;
		default:
			if($no->info=="fechado"){
				break;
			}
			print "Nó não categorizado";
	}
}

function imprimeArvoreRaiz($arv){
	foreach ($arv as $key => $value) {
		print "BD".$key." - ";
		print_r($arv[$key]->info);

	}
}
//Função que adiciona Caracter no meio de uma string numa posição pré-definida
function addCaracter($var, $caracter, $lim){
	$saida;
	$parte1='';
	$parte2='';
	for($i=0;$i<strlen($var);$i++){
		if ($i>=$lim) {
			$parte2.=$var[$i];
		}
		else{
			$parte1.=$var[$i];
		}
				
	}
	$saida=$parte1.$caracter.$parte2;
	return $saida;
}

//Função que deleta um Carater específico de uma String
//Funciona por referência
function deletaCaracter(&$str, $indice){
	$str1=substr($str,0,$indice);
	$str2 = substr($str,$indice+1,strlen($str));
	$str=$str1.$str2;
}
//Em profundidade 
function formataFormulas(&$form){

	if(strlen(@$form['esquerdo'])>3){
		$aux=resolveParenteses2($form['esquerdo']);
		$form['esquerdo']=$aux;
		print_r($form['esquerdo']);
		formataFormulas($form['esquerdo']);
	}
	if(strlen(@$form['direito'])>3){
		$aux=resolveParenteses2($form['direito']);
		$form['direito']=$aux;
		print_r($form['direito']);
		formataFormulas($form['direito']);
	}

}

?>