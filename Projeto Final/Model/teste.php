<?php
require_once("funcAuxiliares.php");
require_once("funcTableaux.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$listaFormulasDisponiveis=array();

$form['esquerdo']=null;
$form['conectivo']=null;
$form['direito']='A';


$form2['esquerdo']=null;
$form2['conectivo']='not';
$form2['direito']='A';

if (checaAtomico($form)) {
	print "O primeiro é atomico<br>";
}
if (checaAtomico($form2)) {
	print "<br>O segundo é atomico<br>";
}
?>


