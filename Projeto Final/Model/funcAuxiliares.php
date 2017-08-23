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
			//Checa se й array ou бtomo
			if(!is_array($raiz['left'])){
				$hash[$raiz['left']][] = 'negativo';
			}
			break;
		case 'implica':
			//Cуdigo da implicaзгo
			break;
		case 'notnot':
			//Cуdigo do not
			break;
		case 'not_e';
			//Cуdido do not E
			break;
		case 'not_ou';
			//Cуdigo do not OU
			break;
		case 'not_implica';
			//Cуdigo do not IMPLICA
			break;
		default:
			# Tratamento de um possнvel erro
			break;
	}
}
?>