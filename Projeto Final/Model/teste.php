<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
//require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!",'@','&');
$listaFormulasDisponiveis=array();

$entrada['info']=array('esquerdo' => null, 'conectivo' => array('operacao' => null, 'variavel'=> null), 'direito'=>"F(x)");
print "<br>Antes da operação<br>";
print_r($entrada);
print "<br>Depois da operação<br>";
substituiPorConstante('a',$entrada['info']['direito'],'x');
print_r($entrada);
if (checaAtomicoLPO($entrada)) {
	print "<br>É atômico<br>";
}

function checaAtomicoLPO($form){
	if ($form['info']['esquerdo']!=null || ($form['info']['conectivo']['operacao']!='not' && $form['info']['conectivo']['operacao']!=null )) {
		return false;
	}
	if (!(is_array($form['info']['direito']))) {
		return true;
	}
}

function substituiPorConstante($constante, &$form, $variavel){
	for ($i=0; $i < strlen($form); $i++) { 
		if ($form[$i]==$variavel) {
			$form[$i]=$constante;
		}
	}
}

function atribuiConstateFormulaArray(&$form,$repetir,$listaConstantes,$hashAtomosLPO,$listaGlobalConstates,$variavel){
	if (is_array($form['info']['esquerdo'])) {
		atribuiConstateFormulaArray($form['info']['esquerdo'],$repetir,$listaConstantes,$variavel);
	}

	if (is_array($form['info']['direito'])) {
		atribuiConstateFormulaArray($form['info']['direito'],$repetir,$listaConstantes,$variavel);
	}
	elseif (!(is_array($form['info']['direito']))) {
		$aux=$form['info'];
		if ($repetir) {
			foreach ($listaGlobalConstates as $key => $value) {
				# code...
			}
		}
		elseif (!$repetir) {
			foreach ($listaGlobalConstates as $key => $value) {
				if (!in_array($value, $listaConstantes)) {
					substituiPorConstante($value,$aux['direito'],$variavel)
				}
			}
		}
		$form['info']['direito']=$aux;
	}
}

public static function casarFormulaLPO($hash,$form){
		$aux=$form['conectivo']['operacao'] == "not" ? '0':'1';
		foreach ($hash as $key => $value) {			
			//Verifico se alguma vez esse cara já foi setado na hash
			if(!is_null($hash[$key])){
				if(($hash[$key]==!$aux) && ($form['direito']==$key)){
					return true;
				}				
			}
		}
		return false;
	}

?>


