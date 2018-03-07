<?php
require_once("funcAuxiliares.php");
require_once("funcTableaux.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$listaFormulasDisponiveis=array();

$form= $DNNquestao19;
//$form= array ("((not(A))ou(not(B)))");
//$form=  array ("((not(AeB))implica((not(A))ou(not(B))))");
foreach ($form as $key => $value) {
	verificaFormulaCorreta($form[$key]);
	$form2[$key]=resolveParenteses2($form[$key]);
	formataFormulas($form2[$key]);
	//$form3[$key]=resolveParenteses2($form2[$key]['direito']);
	//$form4[$key]=resolveParenteses2($form2[$key]['esquerdo']);


}
print_r($form2);
foreach ($form as $key => $value) {
	verificaFormulaCorreta($form[$key]);
	$form3[$key]=resolveParentesesTableaux($form[$key]);
	formataFormulasTableaux($form3[$key]);
	//$form3[$key]=resolveParenteses2($form2[$key]['direito']);
	//$form4[$key]=resolveParenteses2($form2[$key]['esquerdo']);


}
print_r($form3);
//print_r($form3);
//print_r($form4);

function resolveParentesesTableaux2($form){
	global $listaConectivos;
	$auxForm['info']=array('esquerdo' => null, 'conectivo'=> null, 'direito'=>null);
	$auxForm['atualEsquerdo']=false;
	$auxForm['atualDireito']=false;
	$auxForm['atualCentral']=false;
	$auxForm['filhoEsquerdo']=null;
	$auxForm['filhoCentral']=null;
	$auxForm['filhoDireito']=null;
	$auxForm['pai']=null;
	$auxForm['formDisponiveis']=array();
	$auxForm['hashAtomos']=array();
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
		$auxForm['info']['direito']=$form;
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
		$auxForm['info']['direito']=$form;
		$auxForm['info']['conectivo']='not';
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

			if ($auxForm['info']['esquerdo']=='(') {
				$auxForm['info']['esquerdo']=NULL;
			}

			$form=substr($form, 4);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']='not';
			return $auxForm;
		}
		//Caso notnot
		if($form[$i]=='!' && $form[$i+1]=='!' && ($i==0 || $i==1)){
			//Correções específicas para o caso em que notnot está entre parênteses
			if ($form[0]=='(') {
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
			}

			if ($auxForm['info']['esquerdo']=='(') {
				$auxForm['info']['esquerdo']=NULL;
			}

			$form=substr($form, 3);		
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']='notnot';
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
					$auxForm['info']['conectivo']=$aux;
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					converteConectivoExtenso($aux);
					$auxForm['info']['conectivo']=$aux;
					$esquerdo=false;
				}
				
			}
			if($esquerdo==true){
				$auxForm['info']['esquerdo']=$auxForm['info']['esquerdo'].$form[$i];
			}
			if($esquerdo==false){
				$auxForm['info']['direito']=$auxForm['info']['direito'].$form[$i];
			}
		}
	}
	$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
	$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
	//Correções de parênteses excedentes antes de retornar a fórmula
	//Caso 1 - Átomo positivo
	
	if (strlen($auxForm['info']['esquerdo'])==3 && @$auxForm['info']['esquerdo'][0]=='(' ) {
		$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
		$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
	}
	if (strlen($auxForm['info']['direito'])==3 && @$auxForm['info']['direito'][0]=='(' ) {
		$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
		$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
	}
	//Caso 2 - Átomo negativo
	
	if (strlen($auxForm['info']['esquerdo'])==6 && @$auxForm['info']['esquerdo'][0]=='(' &&  @$auxForm['info']['esquerdo'][1]=='!' ) {
		$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
		$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
	}
	if (strlen($auxForm['info']['direito'])==6 && @$auxForm['info']['direito'][0]=='(' &&  @$auxForm['info']['direito'][1]=='!') {
		$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
		$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
	}
	
	$auxiliar=$auxForm['info']['esquerdo'];
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
		if (@$auxForm['info']['esquerdo'][0]=='(' && @$auxForm['info']['esquerdo'][strlen($auxForm['info']['esquerdo'])-1]==')') {
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
		}
	}
	$contador=0;
	$auxiliar=$auxForm['info']['direito'];
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
		if (@$auxForm['info']['direito'][0]=='(' && @$auxForm['info']['direito'][strlen($auxForm['info']['direito'])-1]==')') {
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
		}
	}
	
	return $auxForm;
}	
function formataFormulasTableaux2(&$form){
	//Se ocorrer erro, investigar a entrada no if barra por strlen
	if(@strlen(@$form['info']['esquerdo'])>3){
		$aux=resolveParentesesTableaux2($form['info']['esquerdo']);
		$form['info']['esquerdo']=$aux;
		formataFormulas($form['info']['esquerdo']);
	}
	if(@strlen(@$form['info']['direito'])>3){
		$aux=resolveParentesesTableaux2($form['info']['direito']);
		$form['info']['direito']=$aux;
		formataFormulas($form['info']['direito']);
	}
}
?>


