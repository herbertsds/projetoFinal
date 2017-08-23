<?php
include("formula.php");
include("funcAuxiliares.php");

$hash = array();
$fork = false; 



//Inicialização das fórmulas, aqui recebo os dados para resolver o tableaux
//Vai sofrer mudanças, ao invés de inicializar diretamente eu receberei arrays e formulas prontas
//Mas no momento está assim para os testes iniciais

//A^B
$formula= new Formula();
$formula->setEsquerdo("A");
$formula->setDireito("B");
$formula->setConectivo("e");

//AvB
$formula2= new Formula();
$formula2->setEsquerdo("A");
$formula2->setDireito("B");
$formula2->setConectivo("ou");


//Inicializar o banco de dados do problema com todas as fórmulas, incluindo a pergunta negada
//$arvore[] = $formula;
$arvore[] = $formula2;

//Fork generalizado para as fórmulas
//Toda vez que um novo ramo for gerado daremos fork. Funciona do mesmo jeito que o fork em C.

$retorno = aplicaFormula($arvore[0]); 

if($fork == true){
	foreach ($retorno as $chave => $valor) {
		$arvore['fork'][] = $valor;
		$arvore[0]->usaFormula();
		//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
		if(!is_array($valor)){
			$hash[$valor][] = 'positivo';
		}
	}
	$fork = false;
}
else{
	foreach ($retorno as $chave => $valor) {
		$arvore[0]->usaFormula();
		$arvore[] = $valor;
	}
}


//Testes e impressões finais
echo "<pre>";
print_r($arvore);
echo "</br></br>";
echo "Hash";
echo "</br>";
print_r($hash);


?>