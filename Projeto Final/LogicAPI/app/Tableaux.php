<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesTableaux;

//echo "<pre>";

class Tableaux extends Model
{
    private $exercicioEscolhido;
    private $hashInicial;
    private $listaFormulasNaoUsadas;
    private $listaFormulasDisponiveis;
    private $fork;
    private $nivelG;
    private $numRamoGlobal;
    private $listaConectivos;

	public function __construct($exercicioEscolhido){
    	$this->exercicioEscolhido = $exercicioEscolhido;
		$this->listaConectivos = Formula::getListaConectivos();
    }

    public function fullSteps(){
    	$this->hashInicial = array();
		$this->fork = false;
		$this->listaFormulasNaoUsadas = array();
		$this->listaFormulasDisponiveis = array();
		$this->nivelG=0;
		$this->numRamoGlobal=1;
		$arvoreSaida=[];
		$listaDeNos=[];
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
		$entradaTeste=FuncoesTableaux::negaPergunta($entradaTeste,$tamanho);

		//Passos 3 e 4
		//$raiz=array();
		foreach ($entradaTeste as $key => $value) {
			//$raiz[$key]=$value;
			$this->listaFormulasDisponiveis[$key]=$value;
			//Checa se é átomo para adicionar na hash

			if($value['info']['esquerdo']==NULL && ($value['info']['conectivo']==NULL || $value['info']['conectivo']=='not')){
				$this->hashInicial[$value['info']['direito']]=$value['info']['conectivo'] == "not" ? 0:1;
			}
		}

		//print_r($this->listaFormulasDisponiveis);
		//print_r($this->hashInicial);

		//Passo 5
		$flagFechou=false;
		$contador=0;
		$escolhaAleatoria=false;
		$escolhaEficiente=true;
		$escolhaUsuario=false;
		$raiz=FuncoesTableaux::criaFormulaTableaux();
		$historicoVariaveis=array();
		$nosFolha=array();

		//Inicialização do histórico de variáveis
		//Neste passo qualquer nó pode ser raiz
		$historicoVariaveis[0]['raiz']=FuncoesTableaux::criaFormulaTableaux();
		$historicoVariaveis[0]['nosFolha']=null;
		$historicoVariaveis[0]['listaFormulasDisponiveis']=null;
		$historicoVariaveis[0]['numPasso']=0;

		/*
		//print "<br>Modo de Usuário escolhe a fórmula<br>";
		escolhaUsuario($this->listaFormulasDisponiveis,$this->hashInicial,$this->listaFormulasDisponiveis[0],$nosFolha);
		$contador++;


		////print "<br>Imprime raiz<br>";
		////print_r($raiz);
		escolhaUsuario($this->listaFormulasDisponiveis,$this->hashInicial,$this->listaFormulasDisponiveis[1],$nosFolha,$nosFolha[0]);
		//print "<br>Imprime raiz<br>";
		//print_r($raiz);
		$contador++;

		//escolhaUsuario($this->listaFormulasDisponiveis,$this->hashInicial,$this->listaFormulasDisponiveis[2],$nosFolha,$raiz,$nosFolha[0]);

		//$contador++;
		*/




		while (!(FuncoesTableaux::todasFechadas($nosFolha,$contador)) && ($contador<100)) {
			//Recebe do front-end o critério para escolha de fórmula
			////////////////////////////////
			//.
			//.
			//Colocar aqui como vou receber do front-end
			//.
			//.
			//////////////////////////////////

			//Recebe do front-end o critério para escolha de fórmula
			/////////////////////////////////
			//.
			//.
			//Colocar aqui "voltar um passo"
			//.
			//.
			///////////////////////////
			
			if ($escolhaAleatoria) {
				# Chama a função de escolha aleatória
				//print "<br>Não está feito ainda a função de escolha aleatória<br>";
				break;
			}
			elseif ($escolhaEficiente) {
				/////////////////////
				//.
				//.
				////Receber aqui avançar um passo se for verdade, se não for simplesmente resolve tudo
				//.
				//.
				////////////////////
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
			foreach ($nosFolha as $key => $value) {
				$nosFolha[$key]['filhoCentral']="Não fechado";
			}
		}
		$resultado = null;
		$array = [];
		//print "<br>Árvore a partir da raiz<br>";
		$raiz['id']=0;
		FuncoesTableaux::imprimeArvore($raiz,$resultado,$listaDeNos,$arvoreSaida,$array);
		//FuncoesTableaux::imprimeArvore2($raiz);
		//print_r($arvoreSaida);
		//print_r($array);
		//print_r($listaDeNos);
		//dd(1);
		//FuncoesTableaux::imprimeArvore($raiz);
		//print '<br>Raiz<br>';
		//print_r($raiz);
		// $resultado = null;
		// $resultado[]['central'] = 'Zero';
		// $resultado[]['central'] = 'Um';
		// $resultado[]['central'] = 'Dois';
		// $resultado[]['central'] = 'Três';

		// $resultado[]['esquerda'] = 'Quatro';

		// $resultado[]['esquerda'] = 'Cinco';
		// $resultado[]['central'] = 'Seis';
		// $resultado[]['central'] = 'Sete';

		// $resultado[]['direita'] = 'Oito';

		// $resultado[]['direita'] = 'Nove';
		// $resultado[]['central'] = 'Dez';
		// $resultado[]['central'] = 'Onze';

		// $resultado[]['esquerda'] = 'Doze';
		// $resultado[]['central'] = 'Treze';
		// $resultado[]['central'] = 'Quatorze';
		
		// $resultado[]['direita'] = 'Quinze';
		// $resultado[]['central'] = 'Dezesseis';


		//$resposta = FuncoesTableaux::outputArvore($resultado,$this->exercicioEscolhido);
		//$resposta[] = $raiz;
		//return $resultado;
		$resposta[]=$array;
		$resposta[]=$listaDeNos;
		return $resposta;
    }
}
