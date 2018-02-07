<?php
require_once("funcAuxiliares.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");

$hash['A']='0';
$hash['B']='1';
$hash['C']='0';




if($hash['C']=='1'){
	print "Ok";
}
else{
	print "not";
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


