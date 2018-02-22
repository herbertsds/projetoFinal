<?php 
require_once("formula.php");
require_once("funcAuxiliares.php");
require_once("exerciciosListas.php");
require_once("funcTableaux.php");
echo "<pre>";
//-------------------------------------VARIÁVEIS--GLOBAIS-------------------------------------------
//

$hashInicial = array();
$fork = false;
$listaConectivos=array("^","v","-","!");
$listaFormulasNaoUsadas = array();
$listaFormulasDisponiveis = array();
$nivelG=0;
$numRamoGlobal=1;
//-------------------------------------VARIÁVEIS--GLOBAIS--------------------------------------------

//Inicialização das fórmulas, aqui recebo os dados para resolver o tableaux
//Vai sofrer mudanças, ao invés de inicializar diretamente eu receberei arrays e formulas prontas
//Mas no momento está assim para os testes iniciais


//Estrutura de dados
/*
As fórmulas agora possuem os seguintes campos
$form = array ('info' => array ('esquerdo' => , 'conectivo' => , 'direito' =>), '')
*/
//
/* -----Algoritmo Base-----
1. Ler as fórmulas e armazenar numa lista
2. Negar a pergunta
3. Inicializa uma raiz com as fórmulas  e a lista de fórmulas disponíveis
4. Adicione átomos na hash se houverem (verifica o fechamento)
5. Para cada fórmula não usada faça:
6. 	Aplica a regra na fórmula escolhida (pode ser feito do modo eficiente, do modo arbitrário, ou da escolha direta do usuário)
7.  Faça a fórmula atual gerar filhos de acordo com a regra escolhida
8. 	Adicione átomos na hash se houverem
9	Para os ramos gerados, checar se fecham.
10.	Se todos os ramos estiverem fechados PARE.
*/


//Passo 1
$entradaTeste=array("(AeB)","(BeC)","(A)");
$tamanho=count($entradaTeste);

//Passo 2
$entradaTeste=negaPergunta($entradaTeste,$tamanho);

//Passos 3 e 4
//$raiz=array();
foreach ($entradaTeste as $key => $value) {
	//$raiz[$key]=$value;
	$listaFormulasDisponiveis[$key]=$value;
	//Checa se é átomo para adicionar na hash
	if($value['info']['esquerdo']==NULL && ($value['info']['conectivo']==NULL || $value['info']['conectivo']='not')){
		$hashInicial[$value['info']['direito']]=$value['info']['conectivo'] == "not" ? 0:1;
	}


}


print_r($listaFormulasDisponiveis);
print_r($hashInicial);

//Passo 5
$flagFechou=false;
$contador=0;
$escolhaAleatoria=false;
$escolhaEficiente=true;
$escolhaUsuario=false;
$raiz=criaFormulaTableaux();
$noFolha=criaFormulaTableaux();
$noFolha2=criaFormulaTableaux();
$nosFolha=array();


/*
print "<br>Modo de Usuário escolhe a fórmula<br>";
escolhaUsuario($listaFormulasDisponiveis,$hashInicial,$listaFormulasDisponiveis[0],$nosFolha);
$contador++;


//print "<br>Imprime raiz<br>";
//print_r($raiz);
escolhaUsuario($listaFormulasDisponiveis,$hashInicial,$listaFormulasDisponiveis[1],$nosFolha,$nosFolha[0]);
print "<br>Imprime raiz<br>";
print_r($raiz);
$contador++;

//escolhaUsuario($listaFormulasDisponiveis,$hashInicial,$listaFormulasDisponiveis[2],$nosFolha,$raiz,$nosFolha[0]);

//$contador++;
*/


while ($contador <= 10) {
	//Recebe do front-end o critério para escolha de fórmula
	/*
	.
	.
	Colocar aqui como vou receber do front-end
	.
	.
	*/
	
	if ($escolhaAleatoria) {
		# Chama a função de escolha aleatória
		print "<br>Não está feito ainda a função de escolha aleatória<br>";
		break;
	}
	elseif ($escolhaEficiente) {
		# Chama a função de escolha eficiente
		print "<br>Chamando a função de escolha eficiente<br>";
		foreach ($listaFormulasDisponiveis as $key => $value) {
			print "key ".$key."<br>";
			print_r($value['info']);
			print "<br>";
		}
		escolhaEficiente($listaFormulasDisponiveis,$hashInicial,$nosFolha);
		
	}
	elseif ($escolhaUsuario) {
		# Chama a função de escolha do usuário
		print "<br>Não está feito ainda a função de escolha do usuário<br>";
		break;
	}

	$contador++;
}
print "<br>Imprime raiz<br>";
//print_r($raiz);
imprimeArvore($raiz);
?>