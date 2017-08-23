<?php
include("formula.php");
include("funcAuxiliares.php");

$hash = array();
$fork = false; 



//Inicializa��o das f�rmulas, aqui recebo os dados para resolver o tableaux
//Vai sofrer mudan�as, ao inv�s de inicializar diretamente eu receberei arrays e formulas prontas
//Mas no momento est� assim para os testes iniciais

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


//Inicializar o banco de dados do problema com todas as f�rmulas, incluindo a pergunta negada
//$arvore[] = $formula;
$arvore[] = $formula2;

//Fork generalizado para as f�rmulas
//Toda vez que um novo ramo for gerado daremos fork. Funciona do mesmo jeito que o fork em C.

$retorno = aplicaFormula($arvore[0]); 

if($fork == true){
	foreach ($retorno as $chave => $valor) {
		$arvore['fork'][] = $valor;
		$arvore[0]->usaFormula();
		//Se for um array, significa que � uma f�rmula. Se n�o for um array, significa que � um �tomo
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


//Testes e impress�es finais
echo "<pre>";
print_r($arvore);
echo "</br></br>";
echo "Hash";
echo "</br>";
print_r($hash);


?>