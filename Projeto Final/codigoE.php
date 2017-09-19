<?php 
	$ramo['left'] = 'A';
	$ramo['right'] = 'B';
	$ramo['conectivo'] = 'e';
	$ramo['usado'] = false;
	$arvore[] = $ramo;

	$retorno = aplicaFormula($arvore[0]);

	foreach ($retorno as $chave => $valor) {
		$arvore[0]['usado'] = true;
		$arvore[] = $valor;
	}

	echo "<pre>";
	print_r($arvore);

	function aplicaFormula($raiz){
		switch ($raiz['conectivo']) {
			case 'e':
				return array($raiz['left'],$raiz['right']);
				break;
			
			default:
				# code...
				break;
		}
		if($raiz['conectivo'] == 'e'){
			
		}
	}
?>