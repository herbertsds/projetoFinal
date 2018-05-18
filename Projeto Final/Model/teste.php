<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";
$listaFormulasDisponiveis=array();
$listaGlobalConstates=array("a","b","c","d");
//$hash['F(a)']='0';
$form=array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>null,'variavel'=>null),'direito'=>array('info'=>array('esquerdo'=>null,'conectivo'=>null,'direito'=>'F(x)'))));
$teste="((Ae(not(C)))))";
print "<br>Antes ".$teste."<br>";
consertaStringFormula($teste);
print "<br>Depois ".$teste."<br>";

//Recebe um array fórmula e verifica se o conectivo é not_e,not_ou ou not_implica.
//Sendo um desses, caso haja um paraTodo ou not_xist aninhado, haverá um aumento de priridade nesta fórmula
/*function verificaPotencialPrioridade(&$form){
	$conectivosImportantes=array('not_e','not_ou','not_implica');
	if (!in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
		return null;
	}

	if(is_array($form['info']['esquerdo'])){
		$aux=ParsingFormulas::resolveParentesesTableauxLPO($form['info']['esquerdo']);
		$form['info']['esquerdo']=$aux;
		ParsingFormulas::formataFormulasTableauxLPO($form['info']['esquerdo']);
	}
	if(@strlen(@$form['info']['direito'])>4 && @!FuncoesAuxiliares::temConectivo($form)){
		$aux=ParsingFormulas::resolveParentesesTableauxLPO($form['info']['direito']);
		$form['info']['direito']=$aux;
		ParsingFormulas::formataFormulasTableauxLPO($form['info']['direito']);
	}
}*/

function consertaStringFormula(&$form){
	converteConectivoSimbolo($form);
	$contador=0;
	$abertoUmaVez=false;
	$listaConectivos=array("^","v","-","!",'@','&');
	for ($i=0; $i <strlen($form) ; $i++) { 
		if ($form[$i]=='(') {
			$abertoUmaVez=true;
			$contador++;
		}
		elseif ($form[$i]==')') {
			$contador--;
		}
		while($i==strlen($form)-1 && $contador>0){
			$form=substr($form, 1);
		}
		while($i==strlen($form)-1 && $contador<0){
			$form=substr($form, 0, strlen($form)-1);
		}
	}
	$aux=$form;
	flag:
	$aux=substr($aux, 1);
	$aux=substr($aux, 0, strlen($aux)-1);
	if ($aux[0]!='(' && $aux[0]!='!') {
		goto fim;
	}
	else{
		if ($aux[0]=='(') {
			$form=$aux;
			goto flag;
		}
	}
	fim:
	converteConectivoExtenso($form);
}



?>


