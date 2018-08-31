<?php
//Não serve pra nada, apenas testando código e funções internas para aplicar no código principal
echo "<pre>";

$form1['id']=5;
$form1['campos']='campo1';
$form2['id']=6;
$form2['campos']='campo2';
$form3['id']=7;
$form3['campos']='campo3';
$form4['id']=8;
$form4['campos']='campo4';
$form5['id']=9;
$form5['campos']='campo5';

$array=[];
$array[]['id']=$form1['id'];
//$array[]=[];
//array_push($array[0], $form2['id']);
//array_push($array[0], $form3['id']);

$array2=[];
$array2['id']=$form2['id'];
//$array2[]=[];
$array3=[];
$array3['id']=$form3['id'];
//$array3[]=[];

adicionaArray($array[0], $array2);
adicionaArray($array[0], $array3);

$array4=[];
$array4['id']=$form4['id'];
$array5=[];
$array5['id']=$form5['id'];

@adicionaArray($array2[0], $array4);
@adicionaArray($array2[0], $array5);

print_r($array);

function adicionaArray(&$array,&$valor){
		$tam=count($array);
		$soma=1;
		if ($tam!=0) {
			//Consertar o índice do array para evitar sobreposição
			while (@$array[$tam+$soma]!=null) {
				$soma++;
			}
			if (@$array[$tam+$soma-1]==null) {
				$soma--;
			}
			$array[$tam+$soma]=&$valor;
		}
		else{
			$array[0]=&$valor;
		}
	}

?>