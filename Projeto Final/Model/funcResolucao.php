<?php 

echo "<pre>";

//Variáveis Globais
//$listaConectivos=array("^","v","-","!");
//Recebe um array de fórmulas, verifica se a digitação está correta e 
//devolve um array com a pergunta negada.
//Se houver digitação incorreta gera uma exceção (trabalhar na exceção depois)
function negaPergunta($listaFormulas,$tamanho){
	//Nega a pergunta
	$listaFormulas[$tamanho-1]="not".$listaFormulas[$tamanho-1];
	//Tratar a entrada, verificação de digitação correta
	foreach ($listaFormulas as $key => $value) {
		verificaFormulaCorreta($listaFormulas[$key]);
		$entradaConvertida[$key]=resolveParenteses2($listaFormulas[$key]);
	}
	
	return $entradaConvertida;
}

function converteFNC(&$form){

	//Se for átomo, então sai
	if (!is_array($form)) {
		if (strlen($form)<=2) {
			return;
		}
	}
	if (is_array($form)) {
		if ($form['esquerdo']==NULL && ($form['conectivo']=='not' || $form['conectivo']==NULL)) {
			return;
		}
	}
	

	//Primeiro, remover a implicação, se houver
	//VAI MUDAR PARA O CASO GERAL (IDEIA: USAR WHILE)
	//Caso de implicação dentro de um not
	if($form['conectivo']=="not_implica"){
		if(@strlen($form['direito'])==1){
			$form['direito']="!(".$form['direito'].")";
		}
		else{
			$form['direito']="!".$form['direito'];
		}
		
		$form['conectivo']="e";
	}
	//Caso de implicação sem not
	elseif($form['conectivo']=="implica"){
		if(@strlen($form['esquerdo'])==1){
			$form['esquerdo']="!(".$form['esquerdo'].")";
		}
		else{
			$form['esquerdo']="!".$form['esquerdo'];
		}
		$form['conectivo']="ou";
	}
	print "<br>";
	//print "PRIMEIRO PASSO CONCLUÍDO";


		
	//Segundo Passar todos os not fora de parênteses para dentro

	formataFormulas($form);
	$aux1=&$form['esquerdo'];
	$aux2=&$form['direito'];
	$c=0;
	
	if($form['conectivo']=='not_e'){
		$form['direito']="!".$form['direito'];
		$form['conectivo']='ou';
		$form['esquerdo']="!".$form['esquerdo'];
	}

	if($form['conectivo']=='not_ou'){
		$form['direito']="!".$form['direito'];
		$form['conectivo']='e';
		$form['esquerdo']="!".$form['esquerdo'];
	}

	do{
		if(@$aux1['conectivo']=='not_e'){
			$aux1['direito']="!(".$aux1['direito'].")";
			$aux1['conectivo']='ou';
			$aux1['esquerdo']="!(".$aux1['esquerdo'].")";
		}

		if(@$aux1['conectivo']=='not_ou'){
			$aux1['direito']="!(".$aux1['direito'].")";
			$aux1['conectivo']='e';
			$aux1['esquerdo']="!(".$aux1['esquerdo'].")";
		}

		if(@$aux2['conectivo']=='not_e'){
			$aux2['direito']="!(".$aux2['direito'].")";
			$aux2['conectivo']='ou';
			$aux2['esquerdo']="!(".$aux2['esquerdo'].")";
		}

		if(@$aux2['conectivo']=='not_ou'){
			$aux2['direito']="!(".$aux2['direito'].")";
			$aux2['conectivo']='e';
			$aux2['esquerdo']="!(".$aux2['esquerdo'].")";
		}
		if(@is_array($aux1['esquerdo'])){
			$array1=$aux1;
			$aux1=$aux1['esquerdo'];
		}
		else{
			break;
		}
		if(@is_array($aux2['direito'])){
			$array2=$aux2;
			$aux2=$aux2['direito'];
		}
		else{
			break;
		}
		$c++;
	}while ($array1['esquerdo'] || $array1['direito'] || $array2['esquerdo'] || $array2['direito']);

	


	//Terceira, aplicar a distributiva, formalizar o "e" de "ou"


	if($form['conectivo']=='ou'){
		if(is_array($form['esquerdo']) && $form['esquerdo']['conectivo']=='e'){

			$aux1=&$form['esquerdo'];
			$auxilia['esquerdo']=NULL;
			$auxilia['conectivo']=NULL;
			$auxilia['direito']=NULL;

			if(!is_array($form['direito'])){
				$aux3=$aux1['direito'];
				$aux1['direito']=$form['direito'];
				$aux1['conectivo']="ou";
				$form['direito']=array('esquerdo' => $aux3 , 'conectivo' => "ou" , 'direito' => $form['direito'] );
				$form['conectivo']='e';

			}
			elseif(is_array($form['direito'])){
				$aux3=$aux1['direito'];
				$aux1['direito']=$form['direito'];
				$aux1['conectivo']="ou";
				$form['direito']=array('esquerdo' => $aux3 , 'conectivo' => "ou" , 'direito' => $form['direito'] );
				$form['conectivo']='e';
			}
		}
	
		elseif(is_array($form['direito']) && $form['direito']['conectivo']=='e'){


			$aux2=&$form['direito'];
			if(!is_array($form['esquerdo'])){
				$aux3=$aux2['esquerdo'];
				$aux2['esquerdo']=$form['esquerdo'];
				$aux2['conectivo']="ou";
				$form['esquerdo']=array('esquerdo' => $aux3 , 'conectivo' => "ou" , 'direito' => $form['esquerdo'] );
				$form['conectivo']='e';

			}
			elseif(is_array($form['esquerdo'])){
				$aux3=$aux2['esquerdo'];
				$aux2['esquerdo']=$form['esquerdo'];
				$aux2['conectivo']="ou";
				$form['esquerdo']=array('esquerdo' => $aux3 , 'conectivo' => "ou" , 'direito' => $form['esquerdo'] );
				$form['conectivo']='e';
			}
		}
	}
}

function casarAtomo($hash,$aux,$sinal){
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