<?php 
require_once("formula.php");
require_once("funcAuxiliares.php");

//Neste arquivo criaremos classes No e Arvore para trabalhar como estrutura de dados no nosso tableaux


//Classe No que contem as propriedades que cada No no nossa arvore Tableaux devera ter.
//Cada No pode ser esquerda/direita/central
//Se um No é Central o pai dele também é Central
//O campo Nivel existe para cálculo de eficiência da Arvore.
class No{
	public $info;
	public $pai;
	public $esquerda=NULL;
	public $direita=NULL;
	public $central=NULL;
	public $filhos= array();
	public $nivel;


	public function __construct($info=NULL){
		$this->info = $info;
		$this->esquerda = false;
		$this->direita = false;
		$this->central = false;
		$this->nivel = NULL;

	}

	public function __toString(){
		return "$this->info";
	}
}


//Classe Arvore para administrar toda a manipulação da mesma
//Filhos são gerados a partir da aplicação de regras no array Raiz
//Uma Arvore pode conter somente No Central a depender dos dados na Raiz
class Arvore{
	public $raiz= array();
	public $tamanho;
	public $preenchidos;
	public $tronco= array();
	public $nivelGlobal;
	public $numRamos=1;

	public function __construct($tamanho){
		$this->raiz[] = NULL;
		$this->tamanho = $tamanho;
		$this->preenchidos = 0;
	}

	public function cria($info){
		global $listaFormulasNaoUsadas;

		//Criação do array raiz com os dados que compõe o banco de dados
		while ($this->preenchidos < $this->tamanho) {
			$aux= new Formula();
			//$aux2= new No();
			converteConectivoSimbolo($info[$this->preenchidos]);			
			if($this->preenchidos==$this->tamanho-1){
				$info[$this->preenchidos]="!".$info[$this->preenchidos];
			}
			processaEntrada($info[$this->preenchidos],$aux);
			converteConectivoExtenso($info[$this->preenchidos]);

			$this->raiz[$this->preenchidos]= new No($aux);
			$this->raiz[$this->preenchidos]->central=true;
			//$aux2= $aux;
			//$this->raiz[$this->preenchidos]=$aux2;
			$this->preenchidos++;
			array_push($listaFormulasNaoUsadas,$aux);			
		}

	}

	public function aplicaFormula($indice,$nivelG,$no=NULL){
		global $fork;
		global $hash;
		$paiAux = new No();
		$noAuxEsq = new No();
		$noAuxDir = new No();
		$noAuxCen1 = new No();
		$noAuxCen2 = new No();
		$elementoForm= new Formula();
		$elementoNo= new No();
		//Se o nível for 0, escolheremos um Nó do array Raiz
		//Caso contrário, escolheremos um Nó do array tronco
		if($nivelG==0){
			//Casting para extrair a fórmula
			$elementoNo=$this->raiz[$indice];
			$elementoForm=$elementoNo->info;

			$paiAux=$this->raiz[$indice];
		}
		else{
			$elementoNo=$no;
			$elementoForm=$elementoNo->info;

			$paiAux=$no;
		}

		$noAuxEsq->pai = $paiAux;
		$noAuxDir->pai = $paiAux;
		$noAuxCen1->pai = $paiAux;
		$noAuxCen2->pai = $noAuxCen1;
		//Como eu separei a raiz e o tronco por razões de manipulação, foi conveniente criar
		//uma variável que pudesse assumir um valor tanto de tronco quanto de raiz

		switch ($elementoForm->getConectivo()) {
			//Regra 1
			case 'e':
				array_push($elementoNo->filhos, $noAuxCen1);
				
				$noAuxCen1->info = $elementoForm->getEsquerdo();
				$noAuxCen2->info = $elementoForm->getDireito();
				$noAuxCen1->central=true;
				$noAuxCen2->central=true;
				$noAuxCen2->pai=$noAuxCen1;			
				array_push($noAuxCen1->filhos, $noAuxCen2);
				return array($noAuxCen1,$noAuxCen2);
				//return array($elementoForm->getEsquerdo(),$elementoForm->getDireito());
				//Regra 2
			case 'ou':
				$fork = true;

				$noAuxEsq->info = $elementoForm->getEsquerdo();
				$noAuxDir->info = $elementoForm->getDireito();
				$noAuxEsq->esquerda=true;
				$noAuxDir->direita=true;
				array_push($elementoNo->filhos, $noAuxEsq, $noAuxDir);
				//return array($elementoForm->getEsquerdo(),$elementoForm->getDireito());
				//Tratamento de Single not
			case 'not':
				//Checa se é composto ou átomo
				if(!is_object($elementoForm->getDireito())){
					//print "Sei que é negativo<br>";
					$hash[$elementoForm->getDireito()][] = 'negativo';
				}
				//Se não for objeto chama de novo para aplicar a regra interior
				break;
				//Regra 3
			case 'implica':
				$fork = true;
				$aux1= new Formula();
				//O lado esquero da formula vira not
				//Atomos negativos são sempre adicionados no lado direito de uma Formula
				$aux1->setConectivo("not");
				$aux1->setDireito($elementoForm->getEsquerdo());
				return array($aux1,$elementoForm->getDireito());
				//Regra 4
			case 'notnot':
				if(!is_object($elementoForm->getDireito())){
					$hash[$elementoForm->getDireito()][] = 'positivo';
				}
				return array($elementoForm->getDireito());
				//Regra 5
			case 'not_e';
				$fork = true;
				$aux1 = new Formula();
				$aux2 = new Formula();
				$aux1->setConectivo('not');
				$aux1->setDireito($elementoForm->getEsquerdo());
				$aux2->setConectivo('not');
				$aux2->setDireito($elementoForm->getDireito());
				return array($aux1,$aux2);
				//Regra 6
			case 'not_ou';
				$aux1 = new Formula();
				$aux2 = new Formula();
				$aux1->setConectivo('not');
				$aux1->setDireito($elementoForm->getEsquerdo());
				$aux2->setConectivo('not');
				$aux2->setDireito($elementoForm->getDireito());
				return array($aux1,$aux2);
				//Regra 7
			case 'not_implica';
				$aux1 = new Formula();
				$aux1->setConectivo('not');
				$aux1->setDireito($elementoForm->getDireito());
				return array($elementoForm->getEsquerdo(),$aux1);
			default:
				# Tratamento de um poss�vel erro
				break;

		}
	}


	function forkArv($indiceRamo,$filho1,$filho2=NULL){
		global $fork;
		global $hash;
		
		if($fork == true){
			foreach ($retorno as $chave => $valor) {
				$this->numRamos++;
				$tronco[$this->numRamos-1]= new Ramo();

				$arvore[$indice]->usaFormula();
				//Se for um array, significa que é uma fórmula. Se não for um array, significa que é um átomo
				if(!is_object($valor)){
					$hash[$valor][] = 'positivo';
				}
			}
			$fork = false;
		}
		else{
			if (is_array($retorno) || is_object($retorno)){
				foreach ($retorno as $chave => $valor) {
					$arvore[$indice]->usaFormula();
					$arvore[] = $valor;
				}
			}
		}
	}
}
?>