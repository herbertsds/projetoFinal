<?php
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
$teste="(A^B)vB";
contaParenteses($teste);

/*
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


print_r($arvore);
echo "</br></br>";
echo "Hash";
echo "</br>";
print_r($hash);
*/

function contaParenteses($form){
	$contador=0;
	$contador2=0;
	$i;
	$abreFormula=false;
	$subFormula=0;
	for ($i=0; $i<strlen($form); $i++){
		
		//Abriu parenteses
		if($form[$i]=='('){
			$contador+=1;
			print $contador;
			if($form[$i+1]!='('){
				$abreFormula=true;
				$subFormula++;
			}
		}
		//Fecha parenteses
		elseif($form[$i]==')'){
			$contador-=1;
			print $contador;
			
			if($contador<0){
				#Criar um tratamento aqui
				print "Fórmula com digitação incorreta";
			}
			
			if($abreFormula==true){
				$abreFormula=false;
			}
			$contador2++;
		}
		
		if($abreFormula==true){
			if($form[$i])
		}
		
	}
	print "<br><br> parenteses= ".$contador2;
}

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