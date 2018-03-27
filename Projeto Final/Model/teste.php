<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
require_once("funcResolucao.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!",'@','&');
$listaFormulasDisponiveis=array();

//Print parsing funcionando até o exemplo 12, corrigir o formata fórmulas e testar mais exemplos antes de passar
//Para para a classe principal no laravel
$hash=[];
$teste=array("(AouB)","(A)","(B)","(C)");
$tamanho=count($teste);
//Receber a entrada do Front-End
//Negação da pergunta+Validação
$entradaConvertida=negaPergunta($teste,$tamanho);
print "<br>Entrada recebida<br>";
print_r($entradaConvertida);
$hash=inicializaHash($entradaConvertida);
print_r($hash);
/*
$form= "not(paraTodox(not(F(a))))";
//$form = "((PeQ)implica(not((not(P))ou(not(Q)))))";
$form2['esquerdo']='B';
$form2['conectivo']='not_e';
$form2['direito']='F';

if (!is_array($form2['direito']) && !is_array($form2['direito']) ) {
				$form2['direito']="!(".$form2['direito'].")";
				$form2['esquerdo']="!(".$form2['esquerdo'].")";
			}

print_r($form2);
formataFormulas($form2);
print_r($form2);
*/

/*
verificaFormulaCorreta($form);
$form=resolveParenteses3($form);
//formataFormulas2($form);
print_r($form);
*/

function resolveParenteses3($form){
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
	print "<br>Fórmula convertida<br>";
	print "<br>".$form."<br>";
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

	if(strlen($form)==4 && $form[0]=='!'){
		$form=substr($form, 1);		
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$auxForm['direito']=$form;
		$auxForm['conectivo']='not';
		return $auxForm;
	}
	//Para átomos no contexto de quantificadores universais/existenciais
	//Exemplo F(a)
	if(strlen($form)==6){
		$form=substr($form, 1);		
		$form=substr($form, 0, strlen($form)-1);
		$flag=false;
		for($i=0;$i<strlen($form);$i++){
			if (in_array($form[$i], $listaConectivos)) {
				$flag=true;
			}
		}
		if(!$flag) {
			$auxForm['direito']=$form;
			return $auxForm;
		}
	}
	//Para átomos negativos no contexto de quantificadores universais/existenciais
	//Exemplo ¬F(a)
	if(strlen($form)==7){
		$aux=$form[0];
		$form=substr($form, 1);		
		$form=substr($form, 1);
		$form=substr($form, 0, strlen($form)-1);
		$flag=false;
		for($i=0;$i<strlen($form);$i++){
			if (in_array($form[$i], $listaConectivos)) {
				$flag=true;
			}
		}
		if(!$flag && $aux=='!') {
			$auxForm['conectivo']='not';
			$auxForm['direito']=$form;
			return $auxForm;
		}
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
				print "<br>entrei<br>";
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
					if ($aux=='&' || $aux=='@') {
						converteConectivoNot($aux);
						$auxForm['conectivo']['operacao']=$aux;
						$i++;
						$auxForm['conectivo']['variavel']=$form[$i];
					}
					else{
						converteConectivoNot($aux);
						$auxForm['conectivo']=$aux;
					}
					$esquerdo=false;
					$not=false;
				}
				else{
					$aux=$form[$i];
					if ($aux=='&' || $aux=='@') {
						converteConectivoExtenso($aux);
						$auxForm['conectivo']['operacao']=$aux;
						$i++;
						$auxForm['conectivo']['variavel']=$form[$i];
					}
					else{
						converteConectivoExtenso($aux);
						$auxForm['conectivo']=$aux;
					}
					
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

function formataFormulas2(&$form){
	//Se ocorrer erro, investigar a entrada no if barra por strlen
	if(@strlen(@$form['esquerdo'])>3){
		$aux=resolveParenteses3($form['esquerdo']);
		$form['esquerdo']=$aux;
		formataFormulas($form['esquerdo']);
	}
	if(@strlen(@$form['direito'])>3){
		$aux=resolveParenteses3($form['direito']);
		$form['direito']=$aux;
		formataFormulas($form['direito']);
	}

}

function inicializaHash(&$array){
	$hash=[];
	foreach ($array as $key => $value) {
		if (checaAtomico($value)) {
			$hash[$value['direito']]= $value['conectivo']=='not' ? 0:1;
		}
	}
	return $hash;
}

	
?>


