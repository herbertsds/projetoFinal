<?php
//Fun��o que pega uma f�rmula da �rvore e aplica a regra corresponte
//Para formulas cujo resultado da aplica��o resulta em single note devem chamar aplicaFormula duas vezes para que o case not adicione os �tomos na hash
//Por exemplo, Av�B ou A->B.
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
			//Checa se � composto ou �tomo
			if(!is_object($raiz->getDireito())){
				//print "Sei que � negativo<br>";
				$hash[$raiz->getDireito()][] = 'negativo';
			}
			//Se n�o for objeto chama de novo para aplicar a regra interior
			break;
		//Regra 3	
		case 'implica':
			$fork = true;
			$aux1= new Formula();
			//O lado esquero da formula vira not
			//Atomos negativos s�o sempre adicionados no lado direito de uma Formula
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
//Fun��o fork para 
function forkArv(&$arvore,&$retorno,$indice){
	global $fork;
	global $hash;
	
	if($fork == true){
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[$indice]->usaFormula();
			//Se for um array, significa que � uma f�rmula. Se n�o for um array, significa que � um �tomo
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
?>