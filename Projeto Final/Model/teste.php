<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
require_once("funcResolucao.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$listaFormulasDisponiveis=array();

$form= "not(Aimplica(BeC))";
//$form = "((PeQ)implica(not((not(P))ou(not(Q)))))";
//(((PeQ)implica((not(P))not_ou(not(Q))))
//$form = "((BimplicaC)implicaD)";
verificaFormulaCorreta($form);
$form=resolveParenteses2($form);
formataFormulas($form);
print_r($form);

//colocaParenteses($form);

//$acumulada="";


while (@is_array($form['esquerdo']) || @is_array($form['direito']) || is_array($form)) {
	reverteFormatacao($form);
}
print_r($form);
function reverteFormatacao(&$form){
	if (@is_array($form['esquerdo'])) {
		reverteFormatacao($form['esquerdo']);
	}
	elseif (@!is_array($form['esquerdo']) ) {
		colocaParenteses($form);
	}
	if (@is_array($form['direito'])) {
		reverteFormatacao($form['direito']);
	}
	elseif (@!is_array($form['direito']) ) {
		colocaParenteses($form);
	}
		
	

}
function colocaParenteses(&$form){
	if (@is_array($form['esquerdo']) && @!is_array($form['direito'])) {
		print "entrei";
		if ($form['conectivo']=='not') {
			if (checaAtomico($form)) {
				$aux=$form['conectivo'];
				$aux=$aux."(".$form['direito']."))";
			}
		}
		if ($form['conectivo']=='not_ou') {
			$form['esquerdo']="not(".$form['esquerdo'];
			$aux=$aux."ou";
			$aux=$aux.$form['direito']."))";
			$form=$aux;
			return;
		}
		if ($form['conectivo']=='not_e') {
			$form['esquerdo']="not(".$form['esquerdo'];
			$aux=$aux."e";
			$aux=$aux.$form['direito']."))";
			$form=$aux;
			return;
		}
		if ($form['conectivo']=='not_implica') {
			$form['esquerdo']="not(".$form['esquerdo'];
			$aux=$aux."implica";
			$aux=$aux.$form['direito']."))";
			$form=$aux;
			return;
		}
		$aux=$form['conectivo'];
		$aux=$aux.$form['direito'].")";
		$form['direito']=$aux;
		return;

	}
	elseif (@!is_array($form['esquerdo']) && @is_array($form['direito'])) {
		$aux="(";
		$aux=$aux.$form['esquerdo'];
		$form['esquerdo']=$aux;
		return;
	}
	elseif(is_array($form)){
		if ($form['conectivo']=='not_ou') {
			if ($form['esquerdo'][0]=="(") {
				$aux="not";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."ou";
				$aux=$aux.$form['direito'].")";
			}
			else{
				$aux="not(";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."ou";
				$aux=$aux.$form['direito']."))";
			}
		}
		if ($form['conectivo']=='not_e') {
			if ($form['esquerdo'][0]=="(") {
				$aux="not";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."e";
				$aux=$aux.$form['direito'].")";
			}
			else{
				$aux="not(";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."e";
				$aux=$aux.$form['direito']."))";
			}
			return;
		}
		if ($form['conectivo']=='not_implica') {
			if ($form['esquerdo'][0]=="(") {
				$aux="not";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."implica";
				$aux=$aux.$form['direito'].")";
			}
			else{
				$aux="not(";
				$aux=$aux.$form['esquerdo'];
				$aux=$aux."implica";
				$aux=$aux.$form['direito']."))";
			}			
			$form=$aux;
			return;
		}
		$aux="(";
		$aux=$aux.$form['esquerdo'];
		if ($form['conectivo']=='not') {
			if (checaAtomico($form)) {
				$aux=$aux.$form['conectivo'];
				$aux=$aux."(".$form['direito']."))";
				$form=$aux;
				return;
			}
		}

		$aux=$aux.$form['conectivo'];
		$aux=$aux.$form['direito'].")";
		$form=$aux;
		return;
	}
}

	
?>


