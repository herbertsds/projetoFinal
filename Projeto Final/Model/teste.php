<?php
require_once("funcAuxiliares.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");




$outro['esquerdo']=NULL;
$outro['conectivo']='not';
$outro['direito']='A';



$form['esquerdo']=NULL;
$form['conectivo']=NULL;
$form['direito']=$outro;

print_r($form);
corrigeAtomos($form);
print_r($form);

// && @!$form['esquerdo']


//Talvez precise de melhorias
function corrigeAtomos(&$form){
	if(@$form['esquerdo']==NULL && @is_array($form['direito'])){
		if (@$form['direito']['esquerdo']==NULL) {
			$form['conectivo']=$form['direito']['conectivo'];
			$form['direito']=$form['direito']['direito'];

		}
	}
}
/*
//Loop para garantir que os átomos restantes se tornem arrays
foreach ($entradaConvertida as $key => $value) {
	if (!is_array($value)) {
		if (strlen($value)==1) {
			$entradaConvertida[$key]['esquerdo']=NULL;
			$entradaConvertida[$key]['conectivo']=NULL;
			$entradaConvertida[$key]['direito']=$value;
		}
	}
	if (!is_array($value['esquerdo'])) {
		if (strlen($value['esquerdo'])==1) {
			$entradaConvertida[$key]['esquerdo']['esquerdo']=NULL;
			$entradaConvertida[$key]['esquerdo']['conectivo']=NULL;
			$entradaConvertida[$key]['esquerdo']['direito']=$value['esquerdo'];
		}
	}
	if (!is_array($value['direito'])) {
		if (strlen($value['direito'])==1) {
			$entradaConvertida[$key]['direito']['esquerdo']=NULL;
			$entradaConvertida[$key]['direito']['conectivo']=NULL;
			$entradaConvertida[$key]['direito']['direito']=$value['direito'];
		}
	}
}
*/

?>


