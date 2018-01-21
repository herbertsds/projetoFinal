<?php 
require_once("formula.php");
require_once("funcAuxiliares.php");
require_once("exerciciosListas.php");
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
$entradaTeste= $DNNquestao3;


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

//Print, pré-processa os notnot
foreach ($entradaTeste as $key => $value) {
	if ($entradaConvertida[$key]['conectivo']=='notnot') {
		$entradaConvertida[$key]['conectivo']=NULL;
	}	
}

print "<br>Após o processamento dos notnot<br>";
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

//Os próximos passos precisam ser repetidos afim de extrair os arrays mais internos de fórmulas mais complexas
$contador=0;
while ($contador <= 10){
	

	//Passo 4
	
	if ($contador==0) {
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
	}
	else{
		foreach ($arrayFormulas as $key => $value) {
			if($value['conectivo']=='e'){
				if (is_array($value['esquerdo'])) {
					array_push($arrayFormulas, $value['esquerdo']);
				}
				else{
					$arrayAux['esquerdo']=NULL;
					$arrayAux['conectivo']=NULL;
					$arrayAux['direito']=$value['esquerdo'];
					array_push($arrayFormulas, $arrayAux);
				}
				if (is_array($value['direito'])) {
					array_push($arrayFormulas, $value['direito']);
				}
				else{
					$arrayAux['esquerdo']=NULL;
					$arrayAux['conectivo']=NULL;
					$arrayAux['direito']=$value['direito'];
					array_push($arrayFormulas, $arrayAux);
				}
				unset($arrayFormulas[$key]);
			}

		}
	}
	

	print "<br> FÓRMULAS APÓS SEPARAÇÃO DO E<BR>";
	print_r($arrayFormulas);

	//Passo 5
	$hashResolucao=array();
	foreach ($arrayFormulas as $key => $value) {
		if (is_array($value['esquerdo']) && @$value['esquerdo']['esquerdo']==NULL && @$value['direito']==NULL) {
			//Se o atomo que está chegando casar com algum já existente, então fechamos a resolução
			if(casarAtomo($hashResolucao,$value['esquerdo']['direito'],$value['esquerdo']['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['esquerdo']['direito']);
				print "<br>primeira condição<br>";
				goto fim;
			}
			$hashResolucao[$value['esquerdo']['direito']]=$value['esquerdo']['conectivo'] == "not" ? 0:1;
		}

		if (is_array($value['direito']) && @$value['direito']['esquerdo']==NULL && @$value['esquerdo']==NULL) {
			if(casarAtomo($hashResolucao,$value['direito']['direito'],$value['direito']['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['direito']['direito']);
				print "<br>Segunda condição<br>";
				goto fim;
			}
			$hashResolucao[$value['direito']['direito']]=$value['direito']['conectivo'] == "not" ? 0:1;
		}
		if ($value['esquerdo']==NULL) {
			if(casarAtomo($hashResolucao,$value['direito'],$value['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['direito']);
				print "<br>Terceira condição<br>";
				goto fim;
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
				//Se for um array atômico, significa que é not átomo
				//Assim, se houver o mesmo átomo positivo na hash, ou seja átomo==1
				//Significa que esse membro é falso e eu posso isolar o lado direito do "ou"
				if(is_array($value['esquerdo']) && $value['esquerdo']['esquerdo']==NULL){
					//Pode acontecer de não existir o cara na hash ainda, então o @ é pra omitir este aviso desnecessário
					if(@$hashResolucao[$value['esquerdo']['direito']]==1){
						//O lado direito também pode ser array, porém não importa o que ele contém. Será verdade
						if(is_array($value['direito'])){
							$arrayFormulas[$key]['esquerdo']=$arrayFormulas[$key]['direito']['esquerdo'];
							$arrayFormulas[$key]['conectivo']=$arrayFormulas[$key]['direito']['conectivo'];
							$arrayFormulas[$key]['direito']=$arrayFormulas[$key]['direito']['direito'];
							break;					
							
						}
						else{
							$arrayFormulas[$key]['esquerdo']=NULL;					
							$hashResolucao[$arrayFormulas[$key]['direito']]=$value['conectivo'] == "not" ? 0:1;
							$arrayFormulas[$key]['conectivo']=NULL;
							break;
						}
						
						print_r($value);
						print "<br><br>";
					}
				}
				//Se for um array atômico, significa que é not átomo
				//Assim, se houver o mesmo átomo positivo na hash, ou seja átomo==1
				//Significa que esse membro é falso e eu posso isolar o lado direito do "ou"
				if(is_array($value['direito']) && $value['direito']['esquerdo']==NULL){								
					if($hashResolucao[$value['direito']['direito']]==1){
						//O lado direito também pode ser array, porém não importa o que ele contém. Será verdade
						if(is_array($value['esquerdo'])){
							$arrayFormulas[$key]['esquerdo']=$arrayFormulas[$key]['esquerdo']['esquerdo'];
							$arrayFormulas[$key]['conectivo']=$arrayFormulas[$key]['esquerdo']['conectivo'];
							$arrayFormulas[$key]['direito']=$arrayFormulas[$key]['esquerdo']['direito'];
							break;
						}
						else{
							//Todo átomo deve ser mantido do lado direito
							$arrayFormulas[$key]['direito']=$value['esquerdo'];

							$arrayFormulas[$key]['esquerdo']=NULL;
							$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
							$arrayFormulas[$key]['conectivo']=NULL;
						}	
						
					}
				}
				//Se não for array, então com certeza é um átomo positivo
				//Neste caso, temos que verificar, se há algum correspondente na hash com valor 0
				//Se houver significa que podemos cortar esse cara do "ou"
				if(!is_array($value['esquerdo'])){
					if($hashResolucao[$value['esquerdo']]==0){
						$value['esquerdo']=NULL;
						//garantidamente se o átomo não é array ele é positivo, então recebe 1
						$hashResolucao[$value['esquerdo']]=1;
						//$value['conectivo']=NULL;
					}
				}
				if(!is_array($value['direito'])){
					//Pode acontecer de não existir o cara na hash ainda, então o @ é pra omitir este aviso desnecessário
					if(@$hashResolucao[$value['direito']]==0){
						//print "Teste linha 235<br>";
						//print_r($value);
						//$value['direito']=$value['esquerdo'];
						$value['esquerdo']=NULL;
						//garantidamente se o átomo não é array ele é positivo, então recebe 1
						$hashResolucao[$value['direito']]=1;
						//$value['conectivo']=NULL;
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
		if (is_array($value['esquerdo']) && @$value['esquerdo']['esquerdo']==NULL && @$value['direito']==NULL) {
			print "ENTROU IF 1<BR>";
			//Se o atomo que está chegando casar com algum já existente, então fechamos a resolução
			if(casarAtomo($hashResolucao,$value['esquerdo']['direito'],$value['esquerdo']['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['esquerdo']['direito']);
				goto fim;
			}
			$hashResolucao[$value['esquerdo']['direito']]=$value['esquerdo']['conectivo'] == "not" ? 0:1;
		}

		if (is_array($value['direito']) && @$value['direito']['esquerdo']==NULL && @$value['esquerdo']==NULL) {
			print "ENTROU IF 2<BR>";
			if(casarAtomo($hashResolucao,$value['direito']['direito'],$value['direito']['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['direito']['direito']);
				goto fim;
			}
			$hashResolucao[$value['direito']['direito']]=$value['conectivo']['esquerdo'] == "not" ? 0:1;
		}
		if ($value['esquerdo']==NULL) {
			print "ENTROU IF 3<BR>";
			if(casarAtomo($hashResolucao,$value['direito'],$value['conectivo'])){
				print "<br>Fechou, contradição com o átomo abaixo<br>";
				print_r($value['direito']);
				goto fim;
			}
			$hashResolucao[$value['direito']]=$value['conectivo'] == "not" ? 0:1;
		}
	}

	if(!checaExisteArray($arrayFormulas)){
		print "<br>Não existem mais array, saindo do loop<br><br>";
		break;
	}
	else{
		print "<br>Ainda existe array, próxima iteração<br><br>";
	}
	$contador++;
}

fim:
print "<br>Encerra processamento<br>";

function checaExisteArray($listaFormulas){
	foreach ($listaFormulas as $key => $value) {
		if (is_array($listaFormulas[$key]['esquerdo'])) {
			if ($listaFormulas[$key]['esquerdo']['conectivo']!='not') {
				return true;
			}
		}
		if (is_array($listaFormulas[$key]['direito'])) {
			if ($listaFormulas[$key]['direito']['conectivo']!='not') {
				return true;
			}
		}
		if(!is_array($listaFormulas[$key]['esquerdo'])){
			if($listaFormulas[$key]['esquerdo']!=NULL) {
				return true;
			}
		}
	}
	return false;
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