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
$form=array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>'not_e','variavel'=>null),'direito'=>array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>'paraTodo','variavel'=>'x'),'direito'=>'F(x)'))));
$form2=array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>'not_ou','variavel'=>null),'direito'=>array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>'xist','variavel'=>'x'),'direito'=>'F(x)'))));
$form3=array('info'=>array('esquerdo'=>array('info'=>array('esquerdo'=>null,'conectivo'=>array('operacao'=>'paraTodo','variavel'=>'x'),'direito'=>'F(x)')),'conectivo'=>array('operacao'=>'not_implica','variavel'=>null),'direito'=>null));

print "<br>Formula 1<br>";
print_r($form);
if (verificaPotencialPrioridade($form)) {
	print "<br>Ok<br>";
}
else{
	print "<br>Errado<br>";
}
print "<br>Formula 2<br>";
print_r($form2);
if (verificaPotencialPrioridade($form2)) {
	print "<br>Ok<br>";
}
else{
	print "<br>Errado<br>";
}
print "<br>Formula 3<br>";
print_r($form3);
if (verificaPotencialPrioridade($form3)) {
	print "<br>Ok<br>";
}
else{
	print "<br>Errado<br>";
}

//Recebe um array fórmula e verifica se o conectivo é not_e,not_ou ou not_implica.
//Sendo um desses, caso haja um paraTodo ou not_xist aninhado, retorno true
function verificaPotencialPrioridade($form){
	$conectivosImportantes=array('not_e','not_ou','not_implica');
	$conectivosImportantes2=array('paraTodo','xist');
	if (!in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
		return false;
	}
	elseif (in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
		if (@in_array($form['info']['esquerdo']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
			return true;
		}
		if (@in_array($form['info']['direito']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
			return true;
		}
	}
	if(is_array($form['info']['esquerdo'])){
		verificaPotencialPrioridade($form['info']['esquerdo']);
	}
	if(is_array($form['info']['direito'])){
		verificaPotencialPrioridade($form['info']['direito']);
	}
}

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


