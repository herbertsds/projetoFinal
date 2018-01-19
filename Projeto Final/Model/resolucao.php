<?php 
require_once("formula.php");
require_once("funcAuxiliares.php");
echo "<pre>";

//Variáveis Globais
$listaConectivos=array("^","v","-","!");

/*----Algoritmo Base------
1. Ler as fórmulas e armazenar numa lista
2. Negar a pergunta
3. Passar todas as fórmulas para FNC (conectivo e não pode estar dentro de parênteses)
4. Separar todos os "e", ou seja, cada "e" vira uma fórmula (ou linha) independente. 
5. Verificar se há átomos, havendo, confrontar átomos para ver se a resolução fecha.
Se não fechar, vá para a próxima etapa.
6. Fazer as simplificações do "ou", se possível. Se Av¬B e AvB então A. Se Av¬B e B então A
7. Verificar se há átomos, havendo, confrontar átomos para ver se a resolução fecha.
Se não fechar, o problema não é possível de resolver.
 
*/
$listaFormulasDisponiveis = array();
$tamanho=0;

//Passos 1 e 2

//Entrada
$entradaTeste= array ("(AimplicaB)","(BimplicaC)","(A)","(C)");


//Receber a entrada do Front-End

$tamanho=count($entradaTeste);

//Negação da pergunta
$entradaTeste[$tamanho-1]="not".$entradaTeste[$tamanho-1];

//Tratar a entrada, verificação de digitação correta
foreach ($entradaTeste as $key => $value) {
	verificaFormulaCorreta($entradaTeste[$key]);
	$entradaConvertida[$key]=resolveParenteses2($entradaTeste[$key]);
}


print_r($entradaConvertida);



//Se houver digitação incorreta vai haver um aviso. Para o front-end adicionar uma flag (valor "1")
//A flag vai indicar que a fórmula está incorreta e ficar pedindo a digitação correta para o front-end
//Quando a flag voltar para o valor "0" pode passar para a próxima entrada


//Passo 3
foreach ($entradaConvertida as $key => $value) {
	converteFNC($entradaConvertida[$key]);
}

//Remove parênteses
//$form=substr($form, 1);
//$form=substr($form, 0, strlen($form)-1);

print "<br>Após FNC<br>";

print_r($entradaConvertida);

//Passo 4
$arrayFormulas=array();
foreach ($entradaConvertida as $key => $value) {
	if($value['conectivo']=='e'){
		array_push($arrayFormulas, $value['esquerdo']);
		array_push($arrayFormulas, $value['direito']);
	}
	else{
		array_push($arrayFormulas, $value);
	}

}

print "<br> FÓRMULAS APÓS SEPARAÇÃO DO E<BR>";
print_r($arrayFormulas);

//Passo 5
$hashResolucao=array();
foreach ($arrayFormulas as $key => $value) {
	if (is_array($value['esquerdo']) && @$value['esquerdo']['esquerdo']==NULL) {
		//Se o atomo que está chegando casar com algum já existente, então fechamos a resolução
		if(casarAtomo($hashResolucao,$value['esquerdo']['direito'],$value['esquerdo']['conectivo'])){
			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['esquerdo']['direito']);
		}
		$hashResolucao[$value['esquerdo']['direito']]=$value['conectivo'] == "not" ? 0:1;
	}

	if (is_array($value['direito']) && @$value['direito']['esquerdo']==NULL) {
		if(casarAtomo($hashResolucao,$value['direito']['direito'],$value['direito']['conectivo'])){
			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['direito']['direito']);
		}
		$hashResolucao[$value['direito']['direito']]=$value['conectivo'] == "not" ? 0:1;
	}
	if ($value['esquerdo']==NULL) {
		if(casarAtomo($hashResolucao,$value['direito'],$value['conectivo'])){
			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['direito']);
		}
		$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
	}
}
print "HASH<BR>";
print_r($hashResolucao);

//Passo 6
//
foreach ($arrayFormulas as $key => $value) {
	//Simplificação do tipo: Se Av¬B e B então A
	foreach ($hashResolucao as $key2 => $value2) {
		if ($value['conectivo']=="ou") {
			if(is_array($value['esquerdo']) && $value['esquerdo']['esquerdo']==NULL){
				if($hashResolucao[$value['esquerdo']['direito']]==1){
					$arrayFormulas[$key]['esquerdo']=NULL;
					$hashResolucao[$arrayFormulas[$key]['direito']]=$value['conectivo'] == "not" ? 0:1;
					$arrayFormulas[$key]['conectivo']=NULL;
					print_r($value);
					print "<br><br>";
				}
			}
			if(is_array($value['direito']) && $value['direito']['esquerdo']==NULL){
				if($hashResolucao[$value['direito']['direito']]==1){
					$arrayFormulas[$key]['direito']=$value['esquerdo'];
					$arrayFormulas[$key]['esquerdo']=NULL;
					$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
					$arrayFormulas[$key]['conectivo']=NULL;
				}
			}
			if(!is_array($value['esquerdo'])){
				if($hashResolucao[$value['esquerdo']]==0){
					$value['esquerdo']=NULL;
					$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
					$value['conectivo']=NULL;
				}
			}
			if(!is_array($value['direito'])){
				if($hashResolucao[$value['direito']]==0){
					$value['direito']=$value['esquerdo'];
					$value['esquerdo']=NULL;
					$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
					$value['conectivo']=NULL;
				}
			}
		}
	}
	
}
//Simplificação do tipo: Se Av¬B e AvB então A.
	print "APÓS A SIMPLIFICAÇÃO<BR>";
	print_r($arrayFormulas);
	print_r($hashResolucao);


//Passo 5 - REPETIÇÃO
foreach ($arrayFormulas as $key => $value) {
	if (is_array($value['esquerdo']) && @$value['esquerdo']['esquerdo']==NULL) {
		print "ENTROU IF 1<BR>";
		//Se o atomo que está chegando casar com algum já existente, então fechamos a resolução
		if(casarAtomo($hashResolucao,$value['esquerdo']['direito'],$value['esquerdo']['conectivo'])){

			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['esquerdo']['direito']);
		}
		$hashResolucao[$value['esquerdo']['direito']]=$value['conectivo'] == "not" ? 0:1;
	}

	if (is_array($value['direito']) && @$value['direito']['esquerdo']==NULL) {
		print "ENTROU IF 2<BR>";
		if(casarAtomo($hashResolucao,$value['direito']['direito'],$value['direito']['conectivo'])){
			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['direito']['direito']);
		}
		$hashResolucao[$value['direito']['direito']]=$value['conectivo'] == "not" ? 0:1;
	}
	if ($value['esquerdo']==NULL) {
		
		if(casarAtomo($hashResolucao,$value['direito'],$value['conectivo'])){
			print "Fechou, contradição com o átomo abaixo<br>";
			print_r($value['direito']);
		}
		$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
	}
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
	else{
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

		if($aux1['conectivo']=='not_ou'){
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
		if(is_array($aux1['esquerdo'])){
			$array1=$aux1;
			$aux1=$aux1['esquerdo'];
		}
		else{
			break;
		}
		if(is_array($aux2['direito'])){
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
		if(is_array($form['esquerdo']) && $form['conectivo']=='ou' && $form['esquerdo']['conectivo']=='e'){

			$aux1=&$form['esquerdo'];
			$auxilia['esquerdo']=NULL;
			$auxilia['conectivo']=NULL;
			$auxilia['direito']=NULL;

			//Se o array na verdade for array de átomo, não faça nada
			if($aux1['esquerdo']==NULL){
				print "ESQUERDO É NULO<BR>";
				//NADA
			}
			else{
				if(!is_array($form['direito'])){
					$aux3=$aux1['direito'];
					$aux1['direito']=$form['direito'];
					$aux1['conectivo']="ou";
					$form['direito']=array('esquerdo' => $aux3 , 'conectivo' => "ou" , 'direito' => $form['direito'] );
					$form['conectivo']='e';

				}
			}

		}
	}

	/*

	converteConectivoExtenso($form);
	*/
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