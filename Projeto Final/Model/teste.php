<?php
//N�o serve pra nada, apenas testando c�digo e fun��es internas para aplicar no c�digo principal



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

$form='AvB';


$retorno = aplicaFormula($arvore[0]);
$indiceFormula=0;

function stringParaFormula($form){
    
}

function aplicaFork(&$retorno,&$arvore,$indice){
	global $fork;
	global $hash;
	if($fork == true){
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[$indice]['usado'] = true;
			//Se for um array, significa que � uma f�rmula. Se n�o for um array, significa que � um �tomo
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

//Chamando a fun��o para o �B
$retorno = aplicaFormula($arvore['fork'][1]);

if(is_array($retorno)){
	
	if($fork == true){
		
		foreach ($retorno as $chave => $valor) {
			$arvore['fork'][] = $valor;
			$arvore[0]['usado'] = true;
			//Se for um array, significa que � uma f�rmula. Se n�o for um array, significa que � um �tomo
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
			//Checa se � array ou �tomo
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