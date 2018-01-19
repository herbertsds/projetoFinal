<?php
require_once("funcAuxiliares.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");

$hash = array('A' => 1, 'B' => 1 );

$form['direito']='B';
$form['conectivo']='not';
if(casarAtomo($hash,$form['direito'],$form['conectivo'])){
	print "fechou";
}

function casarAtomo($hash,$aux,$sinal=NULL){
		$aux2=$sinal == "not" ? 0:1;
		if (count($hash)<=1) {
			return false;
		}
		foreach ($hash as $key => $value) {		
			//Verifico se alguma vez esse cara já foi setado na hash
			if(!is_null($hash[$key])){

				if(($hash[$key]==!$aux2) && ($aux==$key)){
					return true;
				}				
			}
		}
		return false;
	}



?>


