<?php
require_once("funcAuxiliares.php");
require_once("funcTableaux.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$listaFormulasDisponiveis=array();

$form= $DNNquestao50;
//$form=  array ("((not(AeB))implica((not(A))ou(not(B))))");
foreach ($form as $key => $value) {
	verificaFormulaCorreta($form[$key]);
	$form2[$key]=resolveParenteses2($form[$key]);
	formataFormulas($form2[$key]);
	//$form3[$key]=resolveParenteses2($form2[$key]['direito']);
	//$form4[$key]=resolveParenteses2($form2[$key]['esquerdo']);


}
print_r($form2);
//print_r($form3);
//print_r($form4);


	

?>


