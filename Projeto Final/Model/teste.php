<?php
//N�o serve pra nada, apenas testando c�digo e fun��es internas para aplicar no c�digo principal
require_once("funcAuxiliares.php");
require_once("formula.php");
//require_once("funcAuxiliares.php");

$listaConectivos=array("^","v","-","!");
echo "<pre>";


$form="!(A^B)";
converteConectivoSimbolo($form);
print "<br>".$form."<br>";
$aux=new Formula();
$aux=resolveParenteses($form);
print "<br>".$form."<br>";
print_r($aux);



