<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesTableaux;

echo "<pre>";

class TableauxLPO extends Model
{
    private $exercicioEscolhido;
    private $hashInicial;
    private $listaFormulasNaoUsadas;
    private $listaFormulasDisponiveis;
    private $numRamoGlobal;
    private $listaConectivos;

	public function __construct($exercicioEscolhido){
    	$this->exercicioEscolhido = $exercicioEscolhido;
		$this->listaConectivos = Formula::getListaConectivos();
    }

    public function fullSteps(){
    	$this->hashInicial = array();
		$this->listaFormulasNaoUsadas = array();
		$this->listaFormulasDisponiveis = array();
		$this->numRamoGlobal=1;
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

		//Recebe do front-end a entrada, que pode ser tanto um array novo quanto um exercício da lista de exercícios
		/*
		.
		.
		Colocar aqui como vou receber do front-end
		.
		.
		*/
    	$entradaTeste = $this->exercicioEscolhido;

    	//Caso tenha tido algum problema com a entrada, dá um die and dump
    	if(is_array($entradaTeste))
    		$tamanho=count($entradaTeste);
    	else
    		dd($entradaTeste);

    	//Passo 2
    	
		$entradaTeste=FuncoesTableauxLPO::negaPerguntaLPO($entradaTeste,$tamanho);
		print_r($entradaTeste);

		

		//Passos 3 e 4
		foreach ($entradaTeste as $key => $value) {
			$this->listaFormulasDisponiveis[$key]=$value;
			//Checa se é átomo para adicionar na hash

			if($value['info']['esquerdo']==NULL && ($value['info']['conectivo']==NULL || $value['info']['conectivo']=='not')){
				$this->hashInicial[$value['info']['direito']]=$value['info']['conectivo'] == "not" ? 0:1;
			}
		}
		
		
		//Passo 5
		$flagFechou=false;
		$contador=0;
		$escolhaAleatoria=false;
		$escolhaEficiente=true;
		$escolhaUsuario=false;
		$raiz=FuncoesTableaux::criaFormulaTableaux();
		$historicoVariaveis=array();
		$nosFolha=array();


		while (!(FuncoesTableaux::todasFechadas($nosFolha,$contador)) && ($contador<100)) {
			
			if ($escolhaAleatoria) {
				# Chama a função de escolha aleatória
				//print "<br>Não está feito ainda a função de escolha aleatória<br>";
				break;
			}
			elseif ($escolhaEficiente) {
				# Chama a função de escolha eficiente
				//print "<br>Chamando a função de escolha eficiente<br>";
				foreach ($this->listaFormulasDisponiveis as $key => $value) {
					////print "key ".$key."<br>";
					////print_r($value['info']);
					////print "<br>";
				}
				if(FuncoesTableaux::escolhaEficiente($this->listaFormulasDisponiveis,$this->hashInicial,$nosFolha,$historicoVariaveis,$raiz,$contador) === 'fechado')
					return ;
				
				if (FuncoesTableaux::todasFechadas($nosFolha,$contador)) {
					break;
				}
				//print "<br>Contador ".$contador."<br>";
				
			}
			elseif ($escolhaUsuario) {
				# Chama a função de escolha do usuário
				//print "<br>Não está feito ainda a função de escolha do usuário<br>";
				break;
			}

			$contador++;
		}


		if (FuncoesTableaux::todasFechadas($nosFolha,$contador)) {
			//print "<br>Todos os ramos foram fechados com sucesso<br>";
			//print $contador."<br>";
		}
		else{
			//print "<br>Nem todos os ramos foram fechados<br>Este Tableaux não fecha<br>";
		}
		//print "<br>Árvore a partir da raiz<br>";
		//FuncoesTableaux::imprimeArvore($raiz);
		//print '<br>Raiz<br>';
		//print_r($raiz);
		$resposta[] = $raiz;
		
		return $resposta;*/
    }
}