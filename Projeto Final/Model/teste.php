<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";

$var='@x(F(x))';
$c='@';
utf8_encode($var);
utf8_encode($c);

if (strpos($var,$c)!==false) {
	print "<br>Verdade<br>";
}
else{
	print "<br>Falso<br>";
}

?>


