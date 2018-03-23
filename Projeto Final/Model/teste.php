<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
require_once("funcResolucao.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!","@","&");
$listaFormulasDisponiveis=array();

$form= "(((AeB)e(not(A)))implicaD)";
//$form = "((PeQ)implica(not((not(P))ou(not(Q)))))";
//(((PeQ)implica((not(P))not_ou(not(Q))))
//$form = "((BimplicaC)implicaD)";
verificaFormulaCorreta($form);
$form=resolveParenteses2($form);
formataFormulas($form);
print_r($form);

//colocaParenteses($form);

//$acumulada="";

/*
while (@is_array($form['esquerdo']) || @is_array($form['direito']) || is_array($form)) {
	reverteFormatacao($form);
}*/
print_r($form);
function resolveParentesesLPO($form){
	global $listaConectivos;
	$auxForm['esquerdo']=NULL;
	$auxForm['conectivo']=NULL;
	$auxForm['direito']=NULL;
	$aux;
	$esquerdo=true;
	$abreFormula=false;
	$contador=0;
	$not=false;

	converteConectivoSimbolo($form);
	//print "<br> Teste".$form;
	//Se for um átomo positivo
	//OBS: Talvez haja uma maneira mais apropriada de tratar isto
	//Em caso de erro nos cálculos, checar esta etapa
	//Número 3 é porque há dois parênteses e o átomo SEMPRE, por exemplo: (A)

	if(strlen($form)==3){
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$auxForm['direito']=$form;
		return $auxForm;
	}
	//Se for um átomo negativo
	//OBS: Talvez haja uma maneira mais apropriada de tratar isto
	//Em caso de erro nos cálculos, checar esta etapa
	//Número 4 é porque há dois parênteses e o átomo com negativo SEMPRE, por exemplo: (!A)

	if(strlen($form)==4){
		$form=substr($form, 1);		
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$auxForm['direito']=$form;
		$auxForm['conectivo']='not';
		return $auxForm;
	}

	//Se não for átomo, caso mais geral
	for ($i=0; $i<strlen($form); $i++){
		//Caso notnotnot
		if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!' && ($i==0 || $i==1)){
			//Correções específicas para o caso em que notnot está entre parênteses
			if ($form[0]=='(') {
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
			}

			if ($auxForm['esquerdo']=='(') {
				$auxForm['esquerdo']=NULL;
			}

			$form=substr($form, 4);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['direito']=$form;
			$auxForm['conectivo']='not';
			return $auxForm;
		}
		//Caso notnot
		if($form[$i]=='!' && $form[$i+1]=='!' && ($i==0 || $i==1)){
			//Correções específicas para o caso em que notnot está entre parênteses
			if ($form[0]=='(') {
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
			}

			if ($auxForm['esquerdo']=='(') {
				//$auxForm['esquerdo']=NULL;
			}
			

			$form=substr($form, 3);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['direito']=$form;
			if ($auxForm['direito'][0]!='(') {
				$auxForm['direito']="(".$auxForm['direito'].")";
			}
			$auxForm['conectivo']='notnot';
			return $auxForm;
		}
		

		//Se achar o conectivo not no exterior de um parentese
		//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
		//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
		//é a abertura de um parenteses
		if($form[$i]=='!' && $form[$i+1]=='('){
			//Se for um átomo, não sinaliza a flag not
			if ($form[$i+3]==')') {
			//faça nada			
						
			}
			elseif ($abreFormula==false && $contador==0) {
				$not=true;
			}
			
		}
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
			if((in_array($form[$i],$listaConectivos)) && ($contador==1) && $form[$i]!='!'){
				if($not==true){
					$aux=$form[$i];
					converteConectivoNot($aux);
					$auxForm['conectivo']=$aux;
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					converteConectivoExtenso($aux);
					$auxForm['conectivo']=$aux;
					$esquerdo=false;
				}
				
			}
			if($esquerdo==true){
				$auxForm['esquerdo']=$auxForm['esquerdo'].$form[$i];
			}
			if($esquerdo==false){
				$auxForm['direito']=$auxForm['direito'].$form[$i];
			}
		}
	}
	$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
	$auxForm['direito']=substr($auxForm['direito'], 1);
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
	
	if (strlen($auxForm['esquerdo'])==6 && @$auxForm['esquerdo'][0]=='(' &&  @$auxForm['esquerdo'][1]=='!' ) {
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
	}
	if (strlen($auxForm['direito'])==6 && @$auxForm['direito'][0]=='(' &&  @$auxForm['direito'][1]=='!') {
		$auxForm['direito']=substr($auxForm['direito'], 1);
		$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
	}
	
	$auxiliar=$auxForm['esquerdo'];
	$conectivo=false;
	$contador=0;
	for ($i=0; $i<strlen($auxiliar); $i++){
		if($auxiliar[$i]=='('){
			$abreFormula=true;
			$contador++;
			
		}
		if($auxiliar[$i]==')'){
			$contador-=1;
			if($contador==0){
				$abreFormula=False;
			}
			
		}
		if($abreFormula==true){
			if((in_array($auxiliar[$i],$listaConectivos)) && ($contador==1) && $auxiliar[$i]!='!'){
				$conectivo=true;
				
			}
		}
	}
	if (!$conectivo) {
		if (@$auxForm['esquerdo'][0]=='(' && @$auxForm['esquerdo'][strlen($auxForm['esquerdo'])-1]==')') {
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
		}
	}
	$contador=0;
	$auxiliar=$auxForm['direito'];
	$conectivo=false;
	for ($i=0; $i<strlen($auxiliar); $i++){
		if($auxiliar[$i]=='('){
			$abreFormula=true;
			$contador++;
			
		}
		if($auxiliar[$i]==')'){
			$contador-=1;
			if($contador==0){
				$abreFormula=False;
			}
			
		}
		if($abreFormula==true){
			if((in_array($auxiliar[$i],$listaConectivos)) && ($contador==1) && $auxiliar[$i]!='!'){
				$conectivo=true;
				
			}
		}
	}
	if (!$conectivo) {
		if (@$auxForm['direito'][0]=='(' && @$auxForm['direito'][strlen($auxForm['direito'])-1]==')') {
			$auxForm['direito']=substr($auxForm['direito'], 1);
			$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
		}
	}
	
	return $auxForm;
}
	
?>


