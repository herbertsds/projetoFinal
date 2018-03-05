<?php
require_once("funcAuxiliares.php");
require_once("funcTableaux.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$form="not(AouB)";
converteConectivoSimbolo($form);
print "<br>".$form."<br>";
$negaFormula=false;
if ($form[0]=='!' || $form[1=='!']) {
		$negaFormula=true;
	}
for($i=0; $i<strlen($form); $i++){
	if ($negaFormula) {
		converteConectivoNot($form);
	}
}
$form=resolveParenteses2($form);
print_r($form);


//Correções de parênteses excedentes antes de retornar a fórmula
	//Caso 1 - Átomo positivo
	if (strlen($auxForm['esquerdo'])==3 && @$auxForm['esquerdo'][0]=='(' ) {
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
	}
	if (strlen($auxForm['direito'])==3 && @$auxForm['direito'][0]=='(' ) {
		$auxForm['direito']=substr($auxForm['direito'], 1);
		$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
	}
	//Caso 2 - Átomo negativo
	if (strlen($auxForm['esquerdo'])==5 && @$auxForm['esquerdo'][0]=='('  @$auxForm['esquerdo'][1]=='!' ) {
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
	}
	if (strlen($auxForm['direito'])==5 && @$auxForm['direito'][0]=='('  @$auxForm['direito'][1]=='!') {
		$auxForm['direito']=substr($auxForm['direito'], 1);
		$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
	}
	$auxiliar=$auxForm['esquerdo'];
	$conectivo=false;
	for ($i=0; $i<strlen($auxForm['esquerdo']); $i++){
		if($form[$i]=='('){
			$abreFormula=true;
			$contador++;
			
		}
		if($form[$i]==')'){
			$contador-=1;
			if($contador==0){
				$abreFormula=False;
			}
			
		}
		if($abreFormula==true){
			if((in_array($form[$i],$listaConectivos)) && ($contador==1)){
				$conectivo=true;
				
			}
		}
	}
	if ($conectivo) {
		if (@$auxForm['esquerdo'][0]==  && @$auxForm['esquerdo'][0]== @$auxForm['esquerdo'][0]=='(' && @$auxForm['esquerdo'][strlen($auxForm['esquerdo'])-1]==')') {
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
		}
	}
	
	$auxiliar=$auxForm['direito'];
	$conectivo=false;
	for ($i=0; $i<strlen($auxForm['direito']); $i++){
		if($form[$i]=='('){
			$abreFormula=true;
			$contador++;
			
		}
		if($form[$i]==')'){
			$contador-=1;
			if($contador==0){
				$abreFormula=False;
			}
			
		}
		if($abreFormula==true){
			if((in_array($form[$i],$listaConectivos)) && ($contador==1)){
				$conectivo=true;
				
			}
		}
	}
	if ($conectivo) {
		if (@$auxForm['direito'][0]==  && @$auxForm['direito'][0]== @$auxForm['direito'][0]=='(' && @$auxForm['direito'][strlen($auxForm['direito'])-1]==')') {
			$auxForm['direito']=substr($auxForm['direito'], 1);
			$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
		}
	}
?>