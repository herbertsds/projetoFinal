<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";

$var1=null;
$var2=null;
$var3=null;
$arvore=null;
$lista=[];
$var1['campo1']['campo2'];
$var1['campo1']='info1';
$var1['campo2']='info2';
$indice=0;
array_push($lista, $var1);
$arvore[$indice]=[];
print "<br>Arvore<br>";
print_r($arvore);
print "<br>Lista<br>";
print_r($lista);
$var2['campo1']['campo2'];
$var2['campo1']='info1';
$var2['campo2']='info2';
$indice++;
array_push($arvore[0],$indice);
array_push($lista, $var2);
$var3['campo1']['campo2'];
$var3['campo1']='info1';
$var3['campo2']='info2';
$indice++;
array_push($arvore[0],$indice);
array_push($lista, $var3);
print "<br>Arvore<br>";
print_r($arvore);
print "<br>Lista<br>";
print_r($lista);

//$arvore=
?>


