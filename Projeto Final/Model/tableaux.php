<?php
require_once("formula.php");
require_once("funcAuxiliares.php");
include("arvore.php");
echo "<pre>";
//-------------------------------------VARIÁVEIS--GLOBAIS-------------------------------------------
//

$hashInicial = array();
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

$entradaTeste=array("((AeC)implicaB)","(BimplicaC)","(A)","(C)");

$entradaTeste2=array("((CouA)e(DouB))","(AouB)","(AeB)","(A)");

$entradaTeste3=array("notnotnot(A)","(AeB)","(D)");

$entradaTeste4=array("((AouB)e((AouC)implicaD))","(A)");

$entradaTeste5=array("not(AeB)","(AeB)","(D)");
$entradaTeste6=array("not(AouB)","(AeB)","(D)");
$entradaTeste7=array("not(AimplicaB)","(AeB)","(D)");

$entradaTeste8=array("not((AouC)e(BouD))","(AeB)","(D)");
$entradaTeste9=array("not((AeC)ou(BeD))","(AeB)","(D)");
$entradaTeste10=array("not((AouC)implica(BouD))","(AeB)","(D)");


/*
$arv = new Arvore(4);
$arv->cria($entradaTeste);
print_r($arv);

//$retorno=$arv->aplicaFormula(0,0);
print "<br><br>";
//print_r($retorno);
print_r($listaFormulasNaoUsadas);
*/



$arv = new Arvore(count($entradaTeste9));
//Criar hash inicial na função cria
$arv->cria($entradaTeste9);
//print_r($arv);
imprimeArvoreRaiz($arv->raiz);


//Inicialização da lista de fórmulas que não foram usadas
//Esta lista será única enquanto houver um único ramo
foreach ($arv->raiz as $key => $value) {
	
	if(!$arv->checaAtomico($value->info)){
		$listaFormulasDisponiveis[$key]=$value->info;
	}
	else{
		array_push($hashInicial,$value->info);
	}
}


$noFolha;
$primeiroNo=$arv->aplicaFormula(0,$nivelG);
$noFolha=$primeiroNo;
$noFolha=$arv->aplicaFormula(0,$nivelG,$arv->raiz[1],$noFolha);


//$noFolha=$arv->aplicaFormula(0,$nivelG,$arv->raiz[2],$noFolha[0]);
//$noFolha=$arv->aplicaFormula(0,$nivelG,$arv->raiz[2],$noFolha[1]);
//print "<br>No Folha<br>";

print "<br><br>";


imprimeDescendo($primeiroNo->pai);


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