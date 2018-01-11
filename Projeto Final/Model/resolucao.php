<?php 
require_once("formula.php");
require_once("funcAuxiliares.php");
echo "<pre>";

/*----Algoritmo Base------
1. Ler as fórmulas e armazenar numa lista
2. Negar a pergunta
3. Passar todas as fórmulas para FNC (conectivo e não pode estar dentro de parênteses)
4. Separar todos os "e", ou seja, cada "e" vira uma fórmula (ou linha) independente. 
5. Verificar se há átomos, havendo, confrontar átomos para ver se a resolução fecha.
Se não fechar, vá para a próxima etapa.
6. Fazer a simplificação do "ou", se possível. Se Av¬B e AvB então A
7. Verificar se há átomos, havendo, confrontar átomos para ver se a resolução fecha.
Se não fechar, o problema não é possível de resolver.
 
*/
$listaFormulasDisponiveis = array();
$tamanho=0;

//Passo 1

//Receber a entrada do Front-End
$entradaTeste=array("((AouB)implicaC)","(BimplicaC)","(A)");
$tamanho=count($entradaTeste);
//Tratar a entrada, verificação de digitação correta
foreach ($entradaTeste as $key => $value) {
	verificaFormulaCorreta($entradaTeste[$key]);
}
//Se houver digitação incorreta vai haver um aviso. Para o front-end adicionar uma flag (valor "1")
//A flag vai indicar que a fórmula está incorreta e ficar pedindo a digitação correta para o front-end
//Quando a flag voltar para o valor "0" pode passar para a próxima entrada

//Passo 2

$entradaTeste[$tamanho-1]="not".$entradaTeste[$tamanho-1];

//Passo 3

converteFNC($entradaTeste[0]);
converteFNC($entradaTeste[1]);

//Remove parênteses
//$form=substr($form, 1);
//$form=substr($form, 0, strlen($form)-1);

print "<br>";

print_r($entradaTeste);

function converteFNC(&$form){
	

	//Primeiro, remover a implicação, se houver
	converteConectivoSimbolo($form);
	if(strpos($form, "-")){
		//Caso de implicação dentro de um not
		if($form[0]=='!'){
			//Remove o not
			$form=substr($form, 1);

			//Remove parênteses mais externos
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);

			$parte2=strstr($form, "-");
			$parte1=strstr($form, "-",true);
			$parte2=substr($parte2, 1);

			$parte1="(".$parte1;
			$form=$parte1."enot".$parte2.")";
		}
		//Caso de implicação sem not
		else{
			//Remove parênteses mais externos
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);


			$parte2=strstr($form, "-");
			$parte1=strstr($form, "-",true);
			$parte2=substr($parte2, 1);

			$parte1="(not".$parte1;
			$form=$parte1."ou".$parte2.")";
		}
		
		//Segundo


	}
	converteConectivoExtenso($form);
}

?>