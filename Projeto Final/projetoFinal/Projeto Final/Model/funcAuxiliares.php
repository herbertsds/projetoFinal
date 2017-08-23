<?php
function aplicaFormula(Formula $raiz){
	global $fork;
	global $hash;
	switch ($raiz->getConectivo()) {
		//Regra 1
		case 'e':
			return array($raiz->getEsquerdo(),$raiz->getDireito());
			break;
		//Regra 2
		case 'ou':
			$fork = true;
			return array($raiz->getEsquerdo(),$raiz->getDireito());
		//Tratamento de Single not
		case 'not':
			//Checa se � array ou �tomo
			if(!is_array($raiz['left'])){
				$hash[$raiz['left']][] = 'negativo';
			}
			break;
		case 'implica':
			//C�digo da implica��o
			break;
		case 'notnot':
			//C�digo do not
			break;
		case 'not_e';
			//C�dido do not E
			break;
		case 'not_ou';
			//C�digo do not OU
			break;
		case 'not_implica';
			//C�digo do not IMPLICA
			break;
		default:
			# Tratamento de um poss�vel erro
			break;
	}
}
?>