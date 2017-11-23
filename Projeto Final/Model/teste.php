<?php
//N�o serve pra nada, apenas testando c�digo e fun��es internas para aplicar no c�digo principal



echo "<pre>";

$vetor = array("Tracer","Zeny","Roadhog","Mercy");
$v="Zeny";
$remover= array($v);

//foreach ($vetor as $key => $value) {
//	$v[$key]=$value;
//}


print_r(array_diff($vetor, $remover));

