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

$array=[];
$array['P']=array();

$variavel='x';
$constante='a';
$hash[$variavel]=$constante;
$array['P'][$variavel]=$constante;
//$array['P']=array();
//array_push($array['P'], $hash);

//$valor=array('P'=>array('x'=>'a','y'=>'b'));
if ($array['P']['x']=='a') {
	$hash=$array['P']['x'];
}
//array_push($array, $valor);
print_r($array['P']['x']);
print_r($hash);
//print $array[0]['P']['x'];

?>


