<?php
//Não serve pra nada, apenas testando código e funções internas para aplicar no código principal



echo "<pre>";
$hash = array();
$fork = false;

$outro['left'] = 'B';
$outro['conectivo'] = 'not';

$ramo['left'] = 'A';
$ramo['right'] = $outro;
$ramo['conectivo'] = 'ou';
$ramo['usado'] = false;
$arvore[] = $ramo;

$retorno = aplicaFormula($arvore[0]);
$indiceFormula=0;

function aplicaFork(&$retorno,&$arvore,$indice){
	global $fork;
	global $hash;
	if($fork == true){
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[$indice]['usado'] = true;
			//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
			if(!is_array($valor)){
				$hash[$valor][] = 'positivo';
			}
		}
		$fork = false;
	}
	else{
		foreach ($retorno as $chave => $valor) {
			$arvore[$indice]['usado'] = true;
			$arvore[] = $valor;
		}
	}
}

aplicaFork($retorno, $arvore, $indiceFormula);

//Chamando a função para o ¬B
$retorno = aplicaFormula($arvore['fork'][1]);

if(is_array($retorno)){
	
	if($fork == true){
		
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[0]['usado'] = true;
			//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
			if(!is_array($valor)){
				$hash[$valor][] = 'positivo';
			}
		}
		$fork = false;
		
	}
	else{
		foreach ($retorno as $chave => $valor) {
			$arvore[0]['usado'] = true;
			$arvore[] = $valor;
		}
	}
}


//print_r($arvore);
echo "</br></br>";
echo "Hash";
echo "</br>";
print_r($hash);

function aplicaFormula($raiz){
	global $fork;
	global $hash;
	
	switch ($raiz['conectivo']) {
		case 'e':
			return array($raiz['left'],$raiz['right']);
			break;
		case 'not':
			//Checa se é array ou átomo
			if(!is_array($raiz['left'])){
				$hash[$raiz['left']][] = 'negativo';
			}
			break;
		case 'ou':
			$fork = true;
			return array($raiz['left'],$raiz['right']);
			break;
		default:
			#Completar
			break;
			
	}
}
?>