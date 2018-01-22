<?php
require_once("funcAuxiliares.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");

$form1['esquerdo']="A";
$form1['conectivo']='ou';
$form1['direito']='B';

$form2['esquerdo']="A";
$form2['conectivo']='e';
$form2['direito']='B';

$form3['esquerdo']="C";
$form3['conectivo']='ou';
$form3['direito']='B';

$form4['esquerdo']="A";
$form4['conectivo']='ou';
$form4['direito']='B';

$form5['esquerdo']="A";
$form5['conectivo']="ou";
$form5['direito']='B';

if ($form1==$form2) {
	print "Form1 e form2 são iguais<br><br>";
}
if ($form1==$form3) {
	print "Form1 e form3 são iguais<br><br>";
}
if ($form1==$form4) {
	print "Form1 e form4 são iguais<br><br>";
}
if ($form1==$form5) {
	print "Form1 e form5 são iguais<br><br>";
}


?>


