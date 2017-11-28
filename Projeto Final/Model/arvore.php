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
	//public $paiDireto;
	//public $numRamo;
	public $esquerda=NULL;
	public $direita=NULL;
	public $central=NULL;
	//public $filhos= array();
	public $filhoEsquerda=NULL;
	public $filhoDireita=NULL;
	public $filhoCentral=NULL;
	public $nivel;
	public $formulasDisponiveis = array();
	public $hashAtomos = array();


	public function __construct($info=NULL){
		$this->info = $info;
		$this->esquerda = false;
		$this->direita = false;
		$this->central = false;
		$this->nivel = NULL;

	}

	//public function __toString(){
	//	return "$this->info";
	//}
}


//Classe Arvore para administrar toda a manipulação da mesma
//Filhos são gerados a partir da aplicação de regras no array Raiz
//Uma Arvore pode conter somente No Central a depender dos dados na Raiz
class Arvore{
	public $raiz= array();
	public $tamanho;
	public $preenchidos;
	public $nivelGlobal;
	public $numRamos=1;

	public function __construct($tamanho){
		$this->raiz[] = NULL;
		$this->tamanho = $tamanho;
		$this->preenchidos = 0;
	}

	public function cria($info){
		//global $listaFormulasNaoUsadas;

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
			//array_push($listaFormulasNaoUsadas,$aux);			
		}

	}

	public function aplicaFormula($indice,&$nivelG,$no=NULL,$noPai=NULL){
		global $fork;
		global $hash;
		global $listaFormulasDisponiveis;
		//local
		$listaFormulasDisponiveis2;

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
			$paiAux->formulasDisponiveis=$listaFormulasDisponiveis;

		}
		else{
			$elementoNo=$no;
			$elementoForm=$elementoNo->info;

			$paiAux=$noPai;
		}
		//Preparações na estrutura de dados para a aplicação da fórmula
		//Em alguns casos uns serão usados e outros não, mas facilita manutenção depois separando este passo do switch


		$noAuxEsq->pai = $paiAux;
		$noAuxEsq->formulasDisponiveis=$paiAux->formulasDisponiveis;
		$noAuxDir->pai = $paiAux;
		$noAuxDir->formulasDisponiveis=$paiAux->formulasDisponiveis;
		$noAuxCen1->pai = $paiAux;
		$noAuxCen2->pai = $noAuxCen1;
		$noAuxCen2->formulasDisponiveis=$paiAux->formulasDisponiveis;



		//O nó da fórmula que eu usar terá de ser removido
		$remover = array($elementoNo->info)[0];

		// $remover[1]['esquerdo:Formula:private'] = "A";


		//Como eu separei a raiz e o tronco por razões de manipulação, foi conveniente criar
		//uma variável que pudesse assumir um valor tanto de tronco quanto de raiz

		switch ($elementoForm->getConectivo()) {
			//Regra 1
			case 'e':
				//array_push($elementoNo->filhos, $noAuxCen1);
				$elementoNo=$paiAux;
				$noAuxCen1->info = $elementoForm->getEsquerdo();
				$noAuxCen2->info = $elementoForm->getDireito();
				$noAuxCen1->central=true;
				$noAuxCen2->central=true;
				$nivelG++;
				$noAuxCen2->pai=$noAuxCen1;	

				$elementoNo->filhoCentral=$noAuxCen1;
				$noAuxCen1->filhoCentral=$noAuxCen2;
				$this->removerFormula($noAuxCen2->formulasDisponiveis,$remover);
				return $noAuxCen2;
				break; 
				//Regra 2
			case 'ou':
				$fork = true;
				$elementoNo=$paiAux;
				$noAuxEsq->info = $elementoForm->getEsquerdo();
				$noAuxDir->info = $elementoForm->getDireito();
				$noAuxEsq->esquerda=true;
				$noAuxDir->direita=true;
				$nivelG++;
				$elementoNo->filhoEsquerda=$noAuxEsq;
				$elementoNo->filhoDireita=$noAuxDir;
				$this->removerFormula($noAuxEsq->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxDir->formulasDisponiveis,$remover);
				return array($noAuxEsq,$noAuxDir);
				break; 
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

	public function removerFormula(&$formulasDisponiveis, $formulaRemover){
		foreach ($formulasDisponiveis as $key => $value) {
			if($value->info == $formulaRemover){
				unset($formulasDisponiveis[$key]);
			}
		}
	}


	//Checa se a fórmula é um átomo
	public function checaAtomico($formula){
		if(($formula->getEsquerdo()==NULL) && ($formula->getDireito()!=NULL)){
			return true;
		}
		else{
			return false;
		}
	}

	//public function


	/*//Checa se o nó folha Casa/Fecha com algum nó ascendente
	public function checaCasamento($noFolha,$noAscendente){
		//Para previnir erro
		if(is_object($noAscendente->info)){
			$conectivo=$noAscendente->info->getConectivo;
			if($conectivo=='not'){
				if(!is_object($noAscendente->info->getDireito))
					if($noFolha->info==$noAscendente->info->getDireito){
						print $noFolha." fechou";
					}
			}
		}
	}*/


}
?>