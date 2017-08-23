<?php
echo "<pre>";
$hash = array();
$fork = false;

$outro['left'] = 'B';
$outro['conectivo'] = 'not';

$ramo['left'] = 'A';
$ramo['right'] = 'B';
$ramo['conectivo'] = 'ou';
$ramo['usado'] = false;
$arvore[] = $ramo;


$retorno = aplicaFormula($arvore[0]);

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

//Chamando a função para o ¬B
//$retorno = aplicaFormula($arvore['fork'][1]);
/*
if(is_array($retorno)){
	echo "Retorno não é átomo";
	if($fork == true){
		
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[0]['usado'] = true;
			//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
			if(!is_array($valor)){
				echo "Não é array";
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
}*/


print_r($arvore);
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
		default:
			$fork = true;
			return array($raiz['left'],$raiz['right']);
			break;
	}
}
?>