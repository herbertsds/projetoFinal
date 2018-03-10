<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
require_once("funcResolucao.php");
require_once("exerciciosListas.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!");
$listaFormulasDisponiveis=array();

$form ="((not(P)implicaP)implicaP)";
verificaFormulaCorreta($form);
$form=resolveParenteses2($form);
print_r($form);
resolveImplicacoes2($form);
print_r($form);

function resolveImplicacoes2(&$form){
		$flag=true;
		$form3=&$form;


		
		//VAI MUDAR PARA O CASO GERAL (IDEIA: USAR WHILE)
		//Caso de implicação dentro de um not
		implica:
		while($flag){
			//print "<br>Fórmula<br>";
			//print_r($form);
			if($form['conectivo']=="not_implica"){
			if (!is_array($form['direito'])) {
				//Se já houver um "not", remove
				if ($form['direito'][0]=='!') {
					$form['direito']=substr($form['direito'], 1);
				}


				elseif(@strlen($form['direito'])==1){
					$form['direito']="!(".$form['direito'].")";
				}
				else{
					$form['direito']="!".$form['direito'];
				}
				
				$form['conectivo']="e";
			}
			elseif(is_array($form['direito'])){
				//Se já houver um "not", remove

				if (checaAtomico($form['direito'])) {
					//Pode ser um átomo, neste caso eu devo simplesmente tratar o not
					if ($form['direito']['conectivo']=='not') {
						$form['direito']['conectivo']=NULL;
					}
					elseif($form['direito']['conectivo']==NULL) {
						$form['direito']['conectivo']='not';
					}
				}
				elseif (!checaAtomico($form['direito'])) {
					//Caso não seja átomo eu preciso negar o conectivo externo da fórmula,
					//que seria equivalente a pôr um not no exterior do parênteses
					if ($form['direito']['conectivo']=='e') {
						$form['direito']['conectivo']='not_e';
					}
					elseif ($form['direito']['conectivo']=='not_e') {
						$form['direito']['conectivo']='e';
					}
					elseif ($form['direito']['conectivo']=='ou') {
						$form['direito']['conectivo']='not_ou';
					}
					elseif ($form['direito']['conectivo']=='not_ou') {
						$form['direito']['conectivo']='ou';
					}
					elseif ($form['direito']['conectivo']=='implica') {
						$form['direito']['conectivo']='not_implica';
					}
					elseif ($form['direito']['conectivo']=='not_implica') {
						$form['direito']['conectivo']='implica';
					}
				}				
				$form['conectivo']="e";
			}
			
		}
		
		//Caso de implicação sem not
		elseif($form['conectivo']=="implica"){
			if (!is_array($form['esquerdo'])) {
				//Se já houver um "not", remove
				if ($form['esquerdo'][0]=='!') {
					$form['esquerdo']=substr($form['esquerdo'], 1);
				}
				elseif(@strlen($form['esquerdo'])==1){
					$form['esquerdo']="!(".$form['esquerdo'].")";
				}
				else{
					$form['esquerdo']="!".$form['esquerdo'];
				}
				//print "<br>Esquerdo<br>";
				//print_r($form);
				
				$form['conectivo']="ou";
			}
			elseif(is_array($form['esquerdo'])){
				//Se já houver um "not", remove
				if (checaAtomico($form['esquerdo'])) {
					//Pode ser um átomo, neste caso eu devo simplesmente tratar o not
					if ($form['esquerdo']['conectivo']=='not') {
						$form['esquerdo']['conectivo']=NULL;
					}
					elseif($form['esquerdo']['conectivo']==NULL) {
						$form['esquerdo']['conectivo']='not';
					}
				}
				elseif (!checaAtomico($form['direito'])) {
					//Caso não seja átomo eu preciso negar o conectivo externo da fórmula,
					//que seria equivalente a pôr um not no exterior do parênteses
					if ($form['esquerdo']['conectivo']=='e') {
						$form['esquerdo']['conectivo']='not_e';
					}
					elseif ($form['esquerdo']['conectivo']=='not_e') {
						$form['esquerdo']['conectivo']='e';
					}
					elseif ($form['esquerdo']['conectivo']=='ou') {
						$form['esquerdo']['conectivo']='not_ou';
					}
					elseif ($form['esquerdo']['conectivo']=='not_ou') {
						$form['esquerdo']['conectivo']='ou';
					}
					elseif ($form['esquerdo']['conectivo']=='implica') {
						$form['esquerdo']['conectivo']='not_implica';
					}
					elseif ($form['esquerdo']['conectivo']=='not_implica') {
						$form['esquerdo']['conectivo']='implica';
					}
				}
				
				$form['conectivo']="ou";
			}
		}
			formataFormulas($form);
			print "<br>Fórmula<br>";
			print_r($form);
			if (checaImplica($form['esquerdo'])) {
				$form=&$form['esquerdo'];
				break;
			}
			if (checaImplica($form['direito'])) {
				$form=&$form['direito'];
				break;
			}
			if ($form!=$form3) {
				$form=&$form3;
			}
			if ($form==$form3) {
				$flag=false;
			}
			print "<br>";

		}
		//formataFormulas($form);
		if (checaImplica($form)) {
			$flag=true;
			goto implica;
		}
	}
function checaAtomico($form){
	//@ colocado para previnir que fórmulas não instanciadas deem warning
	if (@$form['esquerdo']==NULL && (@$form['conectivo']==NULL || @$form['conectivo']='not')) {
		return true;
	}
	else{
		return false;
	}	
}
?>


