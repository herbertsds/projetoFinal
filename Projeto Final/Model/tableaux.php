<?php
require_once("formula.php");
require_once("funcAuxiliares.php");
include("arvore.php");
echo "<pre>";
//-------------------------------------VARIÁVEIS--GLOBAIS-------------------------------------------
//

$hash = array();
$fork = false;
$listaConectivos=array("^","v","-","!");
$listaFormulasNaoUsadas = array();
$listaFormulasDisponiveis = array();
$nivelG=0;
$numRamoGlobal=1;

//-------------------------------------VARIÁVEIS--GLOBAIS--------------------------------------------

//Inicialização das fórmulas, aqui recebo os dados para resolver o tableaux
//Vai sofrer mudanças, ao invés de inicializar diretamente eu receberei arrays e formulas prontas
//Mas no momento está assim para os testes iniciais

//-------------------------------------------------ENTRADAS--------------------------------------------------------------------
//
//A entrada inicial vai ser um array de formulas do tipo STRING, a sequência deve ser
//[banco_de_dados,pergunta], permitindo que eu saiba que a pergunta é a última facilita
//na hora de inicializar o processamento do tableaux

//Cada fórmula deve seguir o formato (Átomo conectivo Átomo), exemplos
//(Aimplica(BouC)), not((AeB)ou(CeD)),  (AouB), ((AeB)implica(not(CouD))

//-------------------------------------------------ENTRADAS---------------------------------------------------------------------

$entradaTeste=array("(AimplicaB)","(BimplicaC)","(A)","(C)");

$entradaTeste2=array("(CeD)","(AouB)","(AeB)","(A)");



/*
$arv = new Arvore(4);
$arv->cria($entradaTeste);
print_r($arv);

//$retorno=$arv->aplicaFormula(0,0);
print "<br><br>";
//print_r($retorno);
print_r($listaFormulasNaoUsadas);
*/



$arv = new Arvore(count($entradaTeste2));
//Criar hash inicial na função cria
$arv->cria($entradaTeste2);
//print_r($arv);
imprimeArvoreRaiz($arv->raiz);


//Inicialização da lista de fórmulas que não foram usadas
//Esta lista será única enquanto houver um único ramo
foreach ($arv->raiz as $key => $value) {
		$listaFormulasDisponiveis[$key]=$value;
}


$noFolha;
$noFolha=$arv->aplicaFormula(0,$nivelG);


$noFolha=$arv->aplicaFormula(0,$nivelG,$arv->raiz[2],$noFolha);
$noFolha=$arv->aplicaFormula(0,$nivelG,$arv->raiz[1],$noFolha);
print "<br>No Folha<br>";

//$retorno=$arv->aplicaFormula(0,$nivelG,$nosRetorno);
print "<br><br>";

//print_r($retorno);
//print_r($arv->raiz[0]->info);
//print_r($arv->raiz[0]->filhos[0]->info);
imprimeDescendo($arv->raiz[0]);

//imprimeDescendo($arv->raiz[2]);
print "<br><br>";





//VerificaFormulaCorreta($formulaTesteErro->getEsquerdo());


/*
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
 
 
 
 //Testes e impress�es finais
 echo "<pre>";
 print_r($arvore);
 echo "</br></br>";
 echo "Hash";
 echo "</br>";
 print_r($hash);
 
 */

?>