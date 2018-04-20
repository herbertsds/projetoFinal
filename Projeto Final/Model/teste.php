<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!",'@','&');
$listaFormulasDisponiveis=array();

$email  = 'name@example.com';
$domain = strstr($email, '@');
echo $domain; // prints @example.com

$user = strstr($email, '@', true); // A partir do PHP 5.3.0
echo $user; // prints name


?>


