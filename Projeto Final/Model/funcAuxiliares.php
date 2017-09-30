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

function converteConectivoSimbolo(&$form){
	$form=str_replace('e','^',$form);
	$form=str_replace('ou','v',$form);
	$form=str_replace('implica','->',$form);
	$form=str_replace('not','¬',$form);
}


function converteConectivoExtenso(&$form){
	$form=str_replace('^','e',$form);
	$form=str_replace('v','ou',$form);
	$form=str_replace('->','implica',$form);
	$form=str_replace('¬','not',$form);
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
				print "Fórmula com digitação incorreta";
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
	    print "Fórmula com digitação incorreta";
		exit(1);
	}
	print "Fórmula Ok<br>";
	
}


//Recebe uma String fórmula (Não é um objeto fórmula), remove os parenteses mais externos
//e devolve um objeto Formula com os dois lados separados e o conectivo mais externo classificado
function resolveParenteses($form){
	global $listaConectivos;
	$auxForm = new Formula();
	$aux;
	$esquerdo=true;
	$abreFormula=false;
	$contador=0;
	converteConectivoSimbolo($form);
	if(strlen($form)==3){
		substr($form, 1);
		substr($form, -1);
		$auxForm->setDireito($form);
		return $auxForm;
	}
	for ($i=0; $i<strlen($form); $i++){
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
				$auxForm->setConectivo($form[$i]);
				$esquerdo=false;
			}
			if($esquerdo==true){
				$auxForm->setEsquerdo($auxForm->getEsquerdo().$form[$i]);
				//$auxForm['esquerdo']=$auxForm['esquerdo'].$form[$i];
			}
			if($esquerdo==false){
				$auxForm->setDireito($auxForm->getDireito().$form[$i]);
				//$auxForm['direito']=$auxForm['direito'].$form[$i];
			}
		}
	}
	$auxForm->setEsquerdo(substr($auxForm->getEsquerdo(), 1));
	$auxForm->setDireito(substr($auxForm->getDireito(), 1));
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
function inicializaArvore(&$arrayForm,&$arvore){

	for ($i=0; $i < count($arrayForm); $i++) { 
		converteConectivoSimbolo($arrayForm[$i]);
		print "Processando... ".$arrayForm[$i]."<br><br>";

		$auxForm = new Formula();
		processaEntrada($arrayForm[$i],$auxForm);
		$arvore[]=$auxForm;

	}
}
?>