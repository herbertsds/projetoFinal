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
		global $hashInicial;
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
			//converteConectivoExtenso($info[$this->preenchidos]);

			$this->raiz[$this->preenchidos]= new No($aux);
			$this->raiz[$this->preenchidos]->central=true;
			//$aux2= $aux;
			//$this->raiz[$this->preenchidos]=$aux2;
			$this->preenchidos++;
			//array_push($listaFormulasNaoUsadas,$aux);			
		}

	}

	//Falta ainda otimizar o consumo de memória, apagando os dados das listas de fórmulas de cada nó
	//e também das hash de cada nó
	public function aplicaFormula($indice,&$nivelG,$no=NULL,$noPai=NULL){
		global $fork;
		global $hashInicial;
		global $listaFormulasDisponiveis;
		//local
		$listaFormulasDisponiveis2;

		$paiAux = new No();
		$noAuxEsq = new No();
		$noAuxDir = new No();
		$noAuxCen1 = new No();
		$noAuxCen2 = new No();
		$formAux1 = new Formula();
		$formAux2 = new Formula();
		$elementoForm= new Formula();
		$elementoNo= new No();

		//Pra previnir erros, eu encerro a função caso ela tenha sido chamada em um ramo fechado
		//Bug pra resolver, mesmo já tendo instanciado filho central ele avisa 
		//Notice:  Trying to get property of non-object in C:\xampp\htdocs\projetoFinal\Projeto Final\Model\arvore.php on line 112
		//Porém executa corretamente o if, achar um jeito de contornar esse aviso

		if($no!=NULL){
			if($noPai!=NULL){
				if ($noPai->filhoCentral->info=="fechado") {
					print "Este ramo já foi fechado. A função aplicaFormula deve ser chamada em outro ramo";
					return;
				}
			}
		}



		//Se o nível for 0, escolheremos um Nó do array Raiz
		//Caso contrário, escolheremos um Nó do array tronco
		if($nivelG==0){
			//Casting para extrair a fórmula
			$elementoNo=$this->raiz[$indice];
			$elementoForm=$elementoNo->info;

			$paiAux=$this->raiz[$indice];
			$paiAux->formulasDisponiveis=$listaFormulasDisponiveis;
			$paiAux->hashAtomos=$hashInicial;


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
		$noAuxCen1->formulasDisponiveis=$paiAux->formulasDisponiveis;



		//O nó da fórmula que eu usar terá de ser removido
		$remover = array($elementoNo->info)[0];


		// $remover[1]['esquerdo:Formula:private'] = "A";

		//Correção de conectivo
		//$elementoForm->setConectivo(converteConectivoExtenso($elementoForm->getConectivo()));


		//Como eu separei a raiz e o tronco por razões de manipulação, foi conveniente criar
		//uma variável que pudesse assumir um valor tanto de tronco quanto de raiz

		switch ($elementoForm->getConectivo()) {
			//Regra 1
			case 'e':
				//array_push($elementoNo->filhos, $noAuxCen1);
				$elementoNo=$paiAux;

				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				$noAuxCen1->info = $formAux1;
				$noAuxCen2->info = $formAux2;

				
				$noAuxCen1->central=true;
				$noAuxCen2->central=true;
				$nivelG++;
				$noAuxCen2->pai=$noAuxCen1;
				$noAuxCen1->hashAtomos=$paiAux->hashAtomos;
				$noAuxCen2->hashAtomos=$paiAux->hashAtomos;

				$elementoNo->filhoCentral=$noAuxCen1;
				$noAuxCen1->filhoCentral=$noAuxCen2;
				//Se após a aplicação da fórmula o objeto gerado não for uma fórmula atômica
				//Então será uma fórmula útil para posterior aplicação de regra
				//Desse modo, esta fórmula também deve constar na lista de fórmulas disponíveis
				//Senão se a fórmula for atômica eu a adiciono na hash
				
				if(!$this->checaAtomico($noAuxCen1->info)){
					array_push($noAuxCen1->formulasDisponiveis, $noAuxCen1->info);
					if(!$this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->formulasDisponiveis, $noAuxCen2->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen1->info)) {
					array_push($noAuxCen1->hashAtomos, $noAuxCen1->info);
					if($this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->hashAtomos, $noAuxCen2->info);
					}
				}
				if(!$this->checaAtomico($noAuxCen2->info)){
					array_push($noAuxCen2->formulasDisponiveis, $noAuxCen2->info);
					if(!$this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->formulasDisponiveis, $noAuxCen1->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen2->info)) {
					array_push($noAuxCen2->hashAtomos, $noAuxCen2->info);
					if($this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->hashAtomos, $noAuxCen1->info);
					}
				}
				
				if($this->casarFormula($noAuxCen2->hashAtomos)){
					$noAuxCen2->filhoCentral= new No();
					$noAuxCen2->filhoCentral->info="fechado";
				}

				$this->removerFormula($noAuxCen2->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxCen1->formulasDisponiveis,$remover);

				return $noAuxCen2;
				break; 
			//Regra 2
			case 'ou':
				$fork = true;
				$elementoNo=$paiAux;
				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				$noAuxEsq->info = $formAux1;
				$noAuxDir->info = $formAux2;
				$noAuxEsq->esquerda=true;
				$noAuxDir->direita=true;
				$noAuxEsq->hashAtomos=$paiAux->hashAtomos;
				$noAuxDir->hashAtomos=$paiAux->hashAtomos;
				$nivelG++;
				$elementoNo->filhoEsquerda=$noAuxEsq;
				$elementoNo->filhoDireita=$noAuxDir;
				//Se após a aplicação da fórmula o objeto gerado não for uma fórmula atômica
				//Então será uma fórmula útil para posterior aplicação de regra
				//Desse modo, esta fórmula também deve constar na lista de fórmulas disponíveis
				
				if(!$this->checaAtomico($noAuxEsq->info)){
					array_push($noAuxEsq->formulasDisponiveis, $noAuxEsq->info);
					if(!$this->checaAtomico($noAuxDir->info)){
						array_push($noAuxEsq->formulasDisponiveis, $noAuxDir->info);
					}
				}
				elseif ($this->checaAtomico($noAuxEsq->info)) {
					array_push($noAuxEsq->hashAtomos, $noAuxEsq->info);
				}
				if(!$this->checaAtomico($noAuxDir->info)){
					array_push($noAuxDir->formulasDisponiveis, $noAuxDir->info);
					if(!$this->checaAtomico($noAuxEsq->info)){
						array_push($noAuxDir->formulasDisponiveis, $noAuxEsq->info);
					}
				}
				elseif ($this->checaAtomico($noAuxDir->info)) {
					array_push($noAuxDir->hashAtomos, $noAuxDir->info);
				}

				if($this->casarFormula($noAuxEsq->hashAtomos)){
					$noAuxEsq->filhoCentral= new No();
					$noAuxEsq->filhoCentral->info="fechado";
				}
				if($this->casarFormula($noAuxDir->hashAtomos)){
					$noAuxDir->filhoCentral= new No();
					$noAuxDir->filhoCentral->info="fechado";
				}
				


				$this->removerFormula($noAuxEsq->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxDir->formulasDisponiveis,$remover);

	
				return array($noAuxEsq,$noAuxDir);
				break; 
				//Tratamento de Single not
			//possível dar bug se eu adicionar no ramo errado
			case 'not':
				$noAuxCen1->info=$elementoForm;
				$noAuxCen1->central=true;
				$noAuxCen1->hashAtomos=$paiAux->hashAtomos;
				$elementoNo->filhoCentral=$noAuxCen1;
				//Se for atômico, preciso adicionar na hash e dar prosseguimento
				if($this->checaAtomico($elementoForm)){
					array_push($noAuxCen1->hashAtomos,$elementoForm);
				}

				if($this->casarFormula($noAuxCen1->hashAtomos)){
					$noAuxCen1->filhoCentral= new No();
					$noAuxCen1->filhoCentral->info="fechado";
				}

				return $noAuxCen1;
		
				//Se não for objeto chama de novo para aplicar a regra interior
				break;
				//Regra 3
			//Falta corrigir o lado esquedo para transformar as formulas em not_alguma coisa
			case 'implica':
				$elementoNo=$paiAux;
				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);

				//O lado esquero da formula vira not
				//Atomos negativos são sempre adicionados no lado direito de uma Formula
				
				if($this->checaAtomico($formAux1)){
					$formAux1->setConectivo('not');
				}
				else{
					$elementoForm->setEsquerdo("!".$elementoForm->getEsquerdo());
					//print_r($elementoForm->getEsquerdo());
					//print "<br>| teste em cima |<br>";
					$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				}
				
				$noAuxEsq->info = $formAux1;
				$noAuxDir->info = $formAux2;
				$noAuxEsq->esquerda=true;
				$noAuxDir->direita=true;
				$noAuxEsq->hashAtomos=$paiAux->hashAtomos;
				$noAuxDir->hashAtomos=$paiAux->hashAtomos;
				$nivelG++;
				$elementoNo->filhoEsquerda=$noAuxEsq;
				$elementoNo->filhoDireita=$noAuxDir;

				if(!$this->checaAtomico($noAuxEsq->info)){
					array_push($noAuxEsq->formulasDisponiveis, $noAuxEsq->info);
					if(!$this->checaAtomico($noAuxDir->info)){
						array_push($noAuxEsq->formulasDisponiveis, $noAuxDir->info);
					}
				}
				elseif ($this->checaAtomico($noAuxEsq->info)) {
					array_push($noAuxEsq->hashAtomos, $noAuxEsq->info);
				}
				if(!$this->checaAtomico($noAuxDir->info)){
					array_push($noAuxDir->formulasDisponiveis, $noAuxDir->info);
					if(!$this->checaAtomico($noAuxEsq->info)){
						array_push($noAuxDir->formulasDisponiveis, $noAuxEsq->info);
					}
				}
				elseif ($this->checaAtomico($noAuxDir->info)) {
					array_push($noAuxDir->hashAtomos, $noAuxDir->info);
				}

				if($this->casarFormula($noAuxEsq->hashAtomos)){
					$noAuxEsq->filhoCentral= new No();
					$noAuxEsq->filhoCentral->info="fechado";
				}
				if($this->casarFormula($noAuxDir->hashAtomos)){
					$noAuxDir->filhoCentral= new No();
					$noAuxDir->filhoCentral->info="fechado";
				}
				
				$this->removerFormula($noAuxEsq->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxDir->formulasDisponiveis,$remover);

				//Correção do not
				$conectivoAux=$noAuxEsq->info->getConectivo();
				converteConectivoExtenso($conectivoAux);
				$noAuxEsq->info->setConectivo($conectivoAux);

				return array($noAuxEsq,$noAuxDir);
				break;


				//Regra 4
			case 'notnot':
				//Transformo notnot Formula em Formula removendo o conectivo
				//Caso seja atomo, eu o removo da hash e adiciono a versão sem notnot
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux1);
				$this->removerFormula($noAuxCen1->formulasDisponiveis,$remover);
				$formAux1->setConectivo(NULL);
				$noAuxCen1->info=$formAux1;
				$noAuxCen1->central=true;
				$elementoNo->filhoCentral=$noAuxCen1;
				if($this->checaAtomico($formAux1)){
					$this->removerFormula($noAuxCen1->hashAtomos,$remover);
					array_push($noAuxCen1->hashAtomos,$formAux1);
				}
				return $noAuxCen1;
				break;
				//Regra 5
			case 'not_e';
				$elementoNo=$paiAux;
				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				//Os dois lados viram not
				//Atomos negativos são sempre adicionados no lado direito de uma Formula
				
				if($this->checaAtomico($formAux1)){
					$formAux1->setConectivo('not');
				}
				elseif(!($this->checaAtomico($formAux1))){
					$elementoForm->setEsquerdo("!".$elementoForm->getEsquerdo());
					$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				}
				if($this->checaAtomico($formAux2)){
					$formAux2->setConectivo('not');
				}				
				elseif(!($this->checaAtomico($formAux2))){
					$elementoForm->setDireito("!".$elementoForm->getDireito());
					$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				}
				$noAuxEsq->info = $formAux1;
				$noAuxDir->info = $formAux2;
				$noAuxEsq->esquerda=true;
				$noAuxDir->direita=true;
				$noAuxEsq->hashAtomos=$paiAux->hashAtomos;
				$noAuxDir->hashAtomos=$paiAux->hashAtomos;
				$nivelG++;
				$elementoNo->filhoEsquerda=$noAuxEsq;
				$elementoNo->filhoDireita=$noAuxDir;
				//Se após a aplicação da fórmula o objeto gerado não for uma fórmula atômica
				//Então será uma fórmula útil para posterior aplicação de regra
				//Desse modo, esta fórmula também deve constar na lista de fórmulas disponíveis
				
				if(!$this->checaAtomico($noAuxEsq->info)){
					array_push($noAuxEsq->formulasDisponiveis, $noAuxEsq->info);
					if(!$this->checaAtomico($noAuxDir->info)){
						array_push($noAuxEsq->formulasDisponiveis, $noAuxDir->info);
					}
				}
				elseif ($this->checaAtomico($noAuxEsq->info)) {
					array_push($noAuxEsq->hashAtomos, $noAuxEsq->info);
				}
				if(!$this->checaAtomico($noAuxDir->info)){
					array_push($noAuxDir->formulasDisponiveis, $noAuxDir->info);
					if(!$this->checaAtomico($noAuxEsq->info)){
						array_push($noAuxDir->formulasDisponiveis, $noAuxEsq->info);
					}
				}
				elseif ($this->checaAtomico($noAuxDir->info)) {
					array_push($noAuxDir->hashAtomos, $noAuxDir->info);
				}

				if($this->casarFormula($noAuxEsq->hashAtomos)){
					$noAuxEsq->filhoCentral= new No();
					$noAuxEsq->filhoCentral->info="fechado";
				}
				if($this->casarFormula($noAuxDir->hashAtomos)){
					$noAuxDir->filhoCentral= new No();
					$noAuxDir->filhoCentral->info="fechado";
				}
				$this->removerFormula($noAuxEsq->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxDir->formulasDisponiveis,$remover);

	
				return array($noAuxEsq,$noAuxDir);
				//Regra 6
			case 'not_ou';
				$elementoNo=$paiAux;
				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				//Os dois lados viram not
				//Atomos negativos são sempre adicionados no lado direito de uma Formula
				
				if($this->checaAtomico($formAux1)){
					$formAux1->setConectivo('not');
				}
				elseif(!($this->checaAtomico($formAux1))){
					$elementoForm->setEsquerdo("!".$elementoForm->getEsquerdo());
					$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				}
				if($this->checaAtomico($formAux2)){
					$formAux2->setConectivo('not');
				}				
				elseif(!($this->checaAtomico($formAux2))){
					$elementoForm->setDireito("!".$elementoForm->getDireito());
					$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				}
				$noAuxCen1->info = $formAux1;
				$noAuxCen2->info = $formAux2;

				
				$noAuxCen1->central=true;
				$noAuxCen2->central=true;
				$nivelG++;
				$noAuxCen2->pai=$noAuxCen1;
				$noAuxCen1->hashAtomos=$paiAux->hashAtomos;
				$noAuxCen2->hashAtomos=$paiAux->hashAtomos;

				$elementoNo->filhoCentral=$noAuxCen1;
				$noAuxCen1->filhoCentral=$noAuxCen2;
				//Se após a aplicação da fórmula o objeto gerado não for uma fórmula atômica
				//Então será uma fórmula útil para posterior aplicação de regra
				//Desse modo, esta fórmula também deve constar na lista de fórmulas disponíveis
				//Senão se a fórmula for atômica eu a adiciono na hash
				
				if(!$this->checaAtomico($noAuxCen1->info)){
					array_push($noAuxCen1->formulasDisponiveis, $noAuxCen1->info);
					if(!$this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->formulasDisponiveis, $noAuxCen2->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen1->info)) {
					array_push($noAuxCen1->hashAtomos, $noAuxCen1->info);
					if($this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->hashAtomos, $noAuxCen2->info);
					}
				}
				if(!$this->checaAtomico($noAuxCen2->info)){
					array_push($noAuxCen2->formulasDisponiveis, $noAuxCen2->info);
					if(!$this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->formulasDisponiveis, $noAuxCen1->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen2->info)) {
					array_push($noAuxCen2->hashAtomos, $noAuxCen2->info);
					if($this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->hashAtomos, $noAuxCen1->info);
					}
				}
				
				if($this->casarFormula($noAuxCen2->hashAtomos)){
					$noAuxCen2->filhoCentral= new No();
					$noAuxCen2->filhoCentral->info="fechado";
				}
				

				$this->removerFormula($noAuxCen2->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxCen1->formulasDisponiveis,$remover);

				return $noAuxCen2;
				//Regra 7
				//Retorna apenas 1 elemento como central
			case 'not_implica';
				$elementoNo=$paiAux;
				//Tratamento necessário para o caso em que getEsquerdo e getDireito não sejam objetos
				$this->tratarNaoFormula($elementoForm->getEsquerdo(),$formAux1);
				$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				//Apenas o lado direito vira not
				//Atomos negativos são sempre adicionados no lado direito de uma Formula
				
				if($this->checaAtomico($formAux2)){
					$formAux2->setConectivo('not');
				}				
				elseif(!($this->checaAtomico($formAux2))){
					$elementoForm->setDireito("!".$elementoForm->getDireito());
					$this->tratarNaoFormula($elementoForm->getDireito(),$formAux2);
				}
				$noAuxCen1->info = $formAux1;
				$noAuxCen2->info = $formAux2;

				
				$noAuxCen1->central=true;
				$noAuxCen2->central=true;
				$nivelG++;
				$noAuxCen2->pai=$noAuxCen1;
				$noAuxCen1->hashAtomos=$paiAux->hashAtomos;
				$noAuxCen2->hashAtomos=$paiAux->hashAtomos;

				$elementoNo->filhoCentral=$noAuxCen1;
				$noAuxCen1->filhoCentral=$noAuxCen2;
				//Se após a aplicação da fórmula o objeto gerado não for uma fórmula atômica
				//Então será uma fórmula útil para posterior aplicação de regra
				//Desse modo, esta fórmula também deve constar na lista de fórmulas disponíveis
				//Senão se a fórmula for atômica eu a adiciono na hash
				
				if(!$this->checaAtomico($noAuxCen1->info)){
					array_push($noAuxCen1->formulasDisponiveis, $noAuxCen1->info);
					if(!$this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->formulasDisponiveis, $noAuxCen2->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen1->info)) {
					array_push($noAuxCen1->hashAtomos, $noAuxCen1->info);
					if($this->checaAtomico($noAuxCen2->info)){
						array_push($noAuxCen1->hashAtomos, $noAuxCen2->info);
					}
				}
				if(!$this->checaAtomico($noAuxCen2->info)){
					array_push($noAuxCen2->formulasDisponiveis, $noAuxCen2->info);
					if(!$this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->formulasDisponiveis, $noAuxCen1->info);
					}
				}
				elseif ($this->checaAtomico($noAuxCen2->info)) {
					array_push($noAuxCen2->hashAtomos, $noAuxCen2->info);
					if($this->checaAtomico($noAuxCen1->info)){
						array_push($noAuxCen2->hashAtomos, $noAuxCen1->info);
					}
				}
				
				if($this->casarFormula($noAuxCen2->hashAtomos)){
					$noAuxCen2->filhoCentral= new No();
					$noAuxCen2->filhoCentral->info="fechado";
				}
				

				$this->removerFormula($noAuxCen2->formulasDisponiveis,$remover);
				$this->removerFormula($noAuxCen1->formulasDisponiveis,$remover);

				return $noAuxCen2;
			default:
				print "<br><br>O elemento ".$elementoForm." chegou no método aplicaFormula e não possui
				conectivo, rever as chamadas dos métodos<br><br>";
				# Tratamento de um possível erro
				break;

		}
	}

	public function removerFormula(&$formulasDisponiveis, $formulaRemover){
		foreach ($formulasDisponiveis as $key => $value) {
			if($value == $formulaRemover){
				unset($formulasDisponiveis[$key]);
			}
		}
	}

	//public function adicionaFormula()


	//Checa se a fórmula é um átomo
	public function checaAtomico($formula){
		if(($formula->getEsquerdo()==NULL) && ($formula->getDireito()!=NULL) && (!is_object($formula->getDireito()))){
			return true;
		}
		else{
			return false;
		}
	}

	//Função para correção de nós cuja a informação não seja formula
	//O tratamento é a transformação dessas infos em fórmulas
	public function tratarNaoFormula($info,&$objForm){
		if(!is_object($info)){
			//If necessário porque fórmuas que começam com not, já tem parênteses
			if(($info[0]=="!") || ($info[0]=="n")){
				converteConectivoSimbolo($info);
				processaEntrada($info,$objForm);
				converteConectivoExtenso($info);
				return ;
			}
			if($info[0]!="("){
				$info="(".$info.")";
			}
						
			converteConectivoSimbolo($info);
			processaEntrada($info,$objForm);
			converteConectivoExtenso($info);
		}
	}
	public function imprimeListaNos($formulasDisponiveis){
		foreach ($formulasDisponiveis as $key => $value) {
			print_r($formulasDisponiveis[$key]->info);
			print "<br>";
		}
	}

	public function casarFormula($hash){
		$formAux = new Formula();
		//Primeiro procurar formula com not
		foreach ($hash as $key => $value) {
			//Casting
			if($value->getConectivo()=="not"){
				print "FECHADO<BR>";
				$formAux=$value;
				break;
			}

		}
		//Se não há nenhum átomo com not, então não é possível fechar ainda
		if($formAux==NULL){
			return false;
		}
		else{
			foreach ($hash as $key => $value) {

				if($value->getDireito()==$formAux->getDireito()){
					if (($value->getEsquerdo()==NULL) && ($value->getConectivo()!="not")) {
						return true;
					}
					
				}

			}
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