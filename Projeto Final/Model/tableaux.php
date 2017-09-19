<?php
include("formula.php");
include("funcAuxiliares.php");

$hash = array();
$fork = false; 


//Inicialização das fórmulas, aqui recebo os dados para resolver o tableaux
//Vai sofrer mudanças, ao invés de inicializar diretamente eu receberei arrays e formulas prontas
//Mas no momento está assim para os testes iniciais

//A^B

$formula = new Formula("A","e","B");

//AvB
$formula2 = new Formula("A","ou","B");

//Av¬B
$formAux = new Formula("not","B");
$formula3= new Formula("A","ou",$formAux);

//A->B
$formula4 = new Formula("A","implica","B");

//¬¬A
$formula5 = new Formula('notnot',"A");

//¬(A^B)
//$formAux2 = new Formula("A","e","B");
$formula6 = new Formula("A","not_e","B");

//¬(AvB)
$formula7 = new Formula("A","not_ou","B");

//¬(A->B)
$formula8 = new Formula("A","not_implica","B");

//Inicializar o banco de dados do problema com todas as fórmulas, incluindo a pergunta negada
//$arvore[] = $formula;
$arvore[] = $formula2;

$retorno = aplicaFormula($arvore[0]); 
$indice=0;

//Fork generalizado para as fórmulas
//Toda vez que um novo ramo for gerado daremos fork. Funciona do mesmo jeito que o fork em C.

forkArv($arvore, $retorno, $indice);




$indice=1;
//forkArv($arvore, $retorno, $indice);

//$retorno = aplicaFormula($arvore['fork'][0]);
//$indice=1;
//forkArv($arvore, $retorno, $indice);



//Testes e impressões finais
echo "<pre>";
print_r($arvore);
echo "</br></br>";
echo "Hash";
echo "</br>";
print_r($hash);



?>