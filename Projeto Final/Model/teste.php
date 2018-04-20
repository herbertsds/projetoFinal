<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!",'@','&');
$listaFormulasDisponiveis=array();

//Print parsing funcionando até o exemplo 12, corrigir o formata fórmulas e testar mais exemplos antes de passar
//Para para a classe principal no laravel/
/*$LPOquestao1 = array ("(xistx(F(x)))","(F(a))");
$LPOquestao2 = array ("not(xistx(F(x)))","not(F(a))");
$LPOquestao3 = array ("(paraTodox(F(x)))","(F(a))");
$LPOquestao4 = array ("(F(a))","(xistx(F(x)))");
$LPOquestao5 = array ("(paraTodox(F(x)))","(xistx(F(x)))");*/
//(paraTodox(paraTodoy(not(L(x,y)))))
//(paraTodoy(paraTodox(not(P(x,y)))))
//$entrada = array ("not(xist(xist(L(x,y))))","(paraTodox(paraTodox(not(P(x,y)))))");
//(paraTodox(paraTodoy(paraTodoz(((L(x,y))e(L(y,z)))implica(not(L(x,x)))))))

$entrada = array ("(paraTodox(paraTodoy((paraTodoz((R(x,z))e(R(x,y))))implica(xistw((R(z,w))e(Q(y,w)))))))");
$tamanho = count($entrada);

$saida=negaPerguntaLPO($entrada,$tamanho);

foreach ($saida as $key => $value) {
	formataFormulasLPO($saida[$key]);
}

print_r($saida);

?>


