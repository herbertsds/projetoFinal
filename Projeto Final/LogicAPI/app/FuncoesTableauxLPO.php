<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesAuxiliares;

class FuncoesTableauxLPO extends Model
{
    
	public static function escolhaEficiente(&$listaFormulasDisponiveis,&$hashInicial,&$nosFolha,&$historicoVariaveis,&$raiz,&$contador){
		//Verificação para saber se a função foi chamada mesmo
		//que todas os ramos já estejam fechados
		if (FuncoesTableauxLPO::todasFechadas($nosFolha,$contador)) {
			//print "<br>Todos os ramos já estão fechados<br>";
			//print $contador."<br>";
			return "fechado";
		}
		
		$conectivosEficientes=array("e","not_ou","not_implica","notnot");

		foreach ($listaFormulasDisponiveis as $key => $value) {
			ParsingFormulas::formataFormulasTableauxLPO($listaFormulasDisponiveis[$key]);
			////print_r($listaFormulasDisponiveis[$key]['info']);

		}


		//Aplicação na raiz
		//-------------------------------------------------------------------
		//Caso 1 - Raiz é eficiente
		//Busque o primeiro nó eficiente que existir e aplique a fórmula nele
		foreach ($listaFormulasDisponiveis as $key => $value) {
			if (in_array($value['info']['conectivo'],$conectivosEficientes)){
				if ($contador==0) {
					//Correções na fórmula
					$raiz=$listaFormulasDisponiveis[$key];
					$raiz['formDisponiveis']=$listaFormulasDisponiveis;
					$raiz['hashAtomos']=$hashInicial;
					//print "<br>Aplicando regra em<br>";
					//print_r($raiz['info']);
					FuncoesTableauxLPO::aplicaRegra($raiz,$raiz,$nosFolha,$contador);
					FuncoesTableauxLPO::removerFormula($listaFormulasDisponiveis,$raiz['info']);
					FuncoesTableauxLPO::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
					return;
				}
			}
		}

		//Caso 2 - Raiz não é eficiente
		//Se não há fórmula eficiente, aplique no primeiro elemento disponível
		if ($contador==0) {
			foreach ($listaFormulasDisponiveis as $key => $value) {
				if ($value['info']['conectivo']!=null && $value['info']['conectivo']!='not'){
					$raiz=$listaFormulasDisponiveis[$key];			
				}			
			}
			
			$raiz['formDisponiveis']=$listaFormulasDisponiveis;
			$raiz['hashAtomos']=$hashInicial;
			FuncoesTableauxLPO::aplicaRegraLPO($raiz,$raiz,$nosFolha,$contador);
			//print "<br>Aplicando regra em<br>";
			//print_r($raiz['info']);
			FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$raiz['info']);
			FuncoesTableaux::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
			return;
		}
		//--------------------------------------------------------------------	
		

		//Aplicação geral de regras
		//Caso 1 - Existem nós eficientes para serem escolhidos
		//Devo checar todos os nós folhas em busca do primeiro não não fechado
		//Se houver fórmula eficiente na lista de fórmulas disponíveis do ramo,
		//aplico a regra, senão continuo a busca até achar ou acabarem os nós folha
		foreach ($nosFolha as $key => $noFolhaAtual) {

			//Verifica se este nó já está fechado
			if (@$noFolhaAtual['filhoCentral']=='fechado') {
				//Deixe o loop passar
			}
			else{
				//Percorre a lista de fórmulas disponíveis do nó folha atual
				foreach ($noFolhaAtual['formDisponiveis'] as $key2 => $formDispAtual) {
					//Correções na fórmula
					FuncoesTableaux::formataFormulasTableaux($noFolhaAtual['formDisponiveis'][$key2]);
					FuncoesTableaux::corrigeArrays($noFolhaAtual['formDisponiveis'][$key2]);
					//Se achar conectivo eficiente aplique a regra
					if (in_array($formDispAtual['info']['conectivo'],$conectivosEficientes)){
						//print "<br>Aplicando regra em<br>";
						//print_r($formDispAtual['info']);
						//print "<br>Com nó pai sendo<br>";
						//print_r(@$nosFolha[$key]['info']);
						FuncoesTableaux::aplicaRegra($formDispAtual,$nosFolha[$key],$nosFolha,$contador);
						FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$formDispAtual['info']);
						FuncoesTableaux::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
						//OTIMIZAR - NÃO PODE ACONTECER
						//Verificação para saber se a função foi chamada mesmo
						//que todas os ramos já estejam fechados
						if (FuncoesTableaux::todasFechadas($nosFolha,$contador)) {
							//print "<br>Todos os ramos já estão fechados<br>";
							//print $contador."<br>";
							return;
						}
						
						return;
					}
				}	
			}
		}

		//Caso 2 - Não existem nós eficientes para serem escolhidos
		//Devo checar todos os nós folhas em busca do primeiro não não fechado
		//Se houver fórmula eficiente na lista de fórmulas disponíveis do ramo,
		//aplico a regra, senão continuo a busca até achar ou acabarem os nós folha
		foreach ($nosFolha as $key => $noFolhaAtual) {

			//Verifica se este nó já está fechado
			if (@$noFolhaAtual['filhoCentral']=='fechado') {
				//Deixe o loop passar
			}
			else{
				//Percorre a lista de fórmulas disponíveis do nó folha atual
				foreach ($noFolhaAtual['formDisponiveis'] as $key2 => $formDispAtual) {
					//Correções na fórmula
					FuncoesTableaux::formataFormulasTableaux($noFolhaAtual['formDisponiveis'][$key2]);
					FuncoesTableaux::corrigeArrays($noFolhaAtual['formDisponiveis'][$key2]);
					//print "<br>Aplicando regra em<br>";
					//print_r($formDispAtual['info']);
					//print "<br>Com nó pai sendo<br>";
					//print_r($nosFolha[$key]['info']);
					FuncoesTableaux::aplicaRegra($formDispAtual,$nosFolha[$key],$nosFolha,$contador);
					FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$formDispAtual['info']);
					FuncoesTableaux::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
					//OTIMIZAR - NÃO PODE ACONTECER
					//Verificação para saber se a função foi chamada mesmo
					//que todas os ramos já estejam fechados
					if (FuncoesTableaux::todasFechadas($nosFolha,$contador)) {
						//print "<br>Todos os ramos já estão fechados<br>";
						//print $contador."<br>";
						return;
					}
					
					return;
				}
			}	
		}	
		//print "<br>Erro, não houve nó para aplicar a fórmula<br>Verificar se todas as fórmulas estão fechadas<br>";	
		return;
	}
	public static function escolhaUsuario(&$listaFormulasDisponiveis,&$hashInicial,$formEscolhida,&$nosFolha,&$raiz,&$contador,&$noFolhaEscolhido=NULL){

		if ($contador==0) {
			$raiz=$formEscolhida;
			$raiz['formDisponiveis']=$listaFormulasDisponiveis;
			$raiz['hashAtomos']=$hashInicial;
			FuncoesTableaux::aplicaRegra($raiz,$raiz,$nosFolha,$contador);
			FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
			return;
		}
		else{
			FuncoesTableaux::aplicaRegra($formEscolhida,$noFolhaEscolhido,$nosFolha,$contador);
			FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
			return;
		}


	}
	//Serve apenas pra instânciar todos os campos do array fórmula para trabalhar evitando erros ou warnings
	public static function criaFormulaTableauxLPO(){
		$auxForm['info']=array('esquerdo' => null, 'conectivo'=> array('operacao' => null, 'variavel'=> null), 'direito'=>null);
		$auxForm['atualEsquerdo']=false;
		$auxForm['atualDireito']=false;
		$auxForm['atualCentral']=false;
		$auxForm['filhoEsquerdo']=null;
		$auxForm['filhoCentral']=null;
		$auxForm['filhoDireito']=null;
		$auxForm['pai']=null;
		$auxForm['formDisponiveis']=array();
		$auxForm['hashAtomos']=array();
		return $auxForm;

	}
	public static function pegaConstante($form){
		f
	}
	public static function aplicaRegraLPO(&$form,&$pai,&$nosFolha,&$contador){

		//Inicializando variáveis auxiliares com suas respectivas estruturas de dados
		$noAuxCen1=FuncoesTableauxLPO::criaFormulaTableauxLPO();
		$noAuxCen1['atualCentral']=true;
		$noAuxCen2=FuncoesTableauxLPO::criaFormulaTableauxLPO();
		$noAuxCen1['filhoCentral']=&$noAuxCen2;
		$noAuxCen2['pai']=&$noAuxCen1;
		$noAuxCen2['atualCentral']=true;
		$noAuxEsq=FuncoesTableauxLPO::criaFormulaTableauxLPO();
		$noAuxEsq['atualEsquerdo']=true;
		$noAuxDir=FuncoesTableauxLPO::criaFormulaTableauxLPO();
		$noAuxDir['atualDireito']=true;
		/*
		//print "<br>Nos Folha<br>";
		foreach ($nosFolha as $key => $value) {
			//print_r($value['info']);
		}*/
		
		
		//Verificação para o caso de haver tentativa de aplciar fórmula
		//num ramo que já foi fechado
		if ($pai['filhoCentral']=='fechado') {
			//print "<br>Este ramo já foi fechado<br>O nó folha é<br>";
			//print_r($pai['info']);
			abort(400,"<br>Este ramo já foi fechado<br>O nó folha é<br>".json_encode($pai['info']));
			return;
		}
		//Verifico o conectivo da fórmula que foi aplicada
		//NOTA:O pai não necessariamente será o mesmo cara o qual está sendo aplicada a fórmula
		switch ($form['info']['conectivo']['operacao']) {
			//Regra 1
			case 'e':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxCen1['info']['direito']=$form['info']['esquerdo'];
				$noAuxCen2['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen2['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen2['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				////print "<br>noAuxCen1<br>";
				////print_r($noAuxCen1);
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}

				FuncoesTableauxLPO::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen2);
				}
	
				return;

			//Regra 2
			case 'ou':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				$pai['filhoEsquerdo']=&$noAuxEsq;
				$pai['filhoDireito']=&$noAuxDir;
				$noAuxEsq['pai']=&$pai;
				$noAuxDir['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxEsq['info']['direito']=$form['info']['esquerdo'];
				$noAuxDir['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxEsq['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxDir['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxEsq['hashAtomos']=$pai['hashAtomos'];
				$noAuxDir['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				FuncoesTableauxLPO::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxDir['formDisponiveis'],$form['info']);	
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxDir);
				}	
				return;		
			case 'implica':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				$pai['filhoEsquerdo']=&$noAuxEsq;
				$pai['filhoDireito']=&$noAuxDir;
				$noAuxEsq['pai']=&$pai;
				$noAuxDir['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxEsq['info']['direito']=$form['info']['esquerdo'];
				$noAuxDir['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxEsq['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxDir['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxEsq['hashAtomos']=$pai['hashAtomos'];
				$noAuxDir['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);
				

				//Manipulação específica da implicação
				//O lado esquerdo deve passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxEsq['info']);
				}

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
						//print "<br>FECHADO<br>";
						//print_r($noAuxEsq['info']);
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormulaLPO($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
						//print "<br>FECHADO<br>";
						//print_r($noAuxDir['info']);
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}

				FuncoesTableauxLPO::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxDir['formDisponiveis'],$form['info']);
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxDir);
				}	
				
				
				return;		
			//Regra 4
			case 'notnot':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);
				

				//Manipulação específica de notnot
				

				//Se a fórmula for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='notnot') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
				}

				//ARRUMAR O CASO EM QUE NOTNOT ESTIVER EM ARRAY
				/*
				//Se o lado esquerdo for array
				elseif (!checaAtomico($noAuxEsq['info'])){
					negaArrayTableaux($noAuxEsq['info']);
				}			
				*/

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				if(FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormulaLPO($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
					
				FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen1);
				return;
			
			case 'not_e':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				$pai['filhoEsquerdo']=&$noAuxEsq;
				$pai['filhoDireito']=&$noAuxDir;
				$noAuxEsq['pai']=&$pai;
				$noAuxDir['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxEsq['info']['direito']=$form['info']['esquerdo'];
				$noAuxDir['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxEsq['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxDir['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxEsq['hashAtomos']=$pai['hashAtomos'];
				$noAuxDir['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);

				//Manipulação específica do not_e
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxEsq['info']);
				}

				//Se o lado direito for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])) {
					if ($noAuxDir['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxDir['info']['conectivo']=null;
					}
					else{
						$noAuxDir['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxDir['info']);
				}

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				FuncoesTableauxLPO::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxDir['formDisponiveis'],$form['info']);	
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxDir);
				}	
				return;	
			
			case 'not_ou':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxCen1['info']['direito']=$form['info']['esquerdo'];
				$noAuxCen2['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen2['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen2['hashAtomos']=$pai['hashAtomos'];

				

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);


				//Manipulação específica do not_ou
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])) {
					if ($noAuxCen1['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen1['info']['conectivo']=null;
					}
					else{
						$noAuxCen1['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen1['info']);
				}

				//Se o lado direito for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen2['info']);
				}
				


				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableauxLPO::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableauxLPO::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}


				FuncoesTableauxLPO::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen2);
				}
				return;
			
			case 'not_implica':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliáres
				$noAuxCen1['info']['direito']=$form['info']['esquerdo'];
				$noAuxCen2['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen2['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen2['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);

				//Manipulação específica do not_implica
				//O lado direito deve passar a ter um not externamente


				//Se o lado direito for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen2['info']);
				}
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableauxLPO::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}

				FuncoesTableauxLPO::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen2);
				}

				return;
			case 'paraTodo':
			//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
			//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);
				

				//Manipulação específica de notnot
				

				//Se a fórmula for átomo
				if (FuncoesTableauxLPO::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='notnot') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
				}

				//ARRUMAR O CASO EM QUE NOTNOT ESTIVER EM ARRAY
				/*
				//Se o lado esquerdo for array
				elseif (!checaAtomico($noAuxEsq['info'])){
					negaArrayTableaux($noAuxEsq['info']);
				}			
				*/

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				if(FuncoesTableauxLPO::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormulaLPO($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
					
				FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen1);
				return;
			

			case 'xist':

			case 'not_paraTodo':

			case 'not_xist':
			//Caso extra
			case 'not':
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableauxLPO::checaAtomico($pai['info'])){
					if(FuncoesTableauxLPO::casarFormula($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo'] == 'not' ? '0':'1';	
				}

				
				FuncoesTableauxLPO::removerFormula($pai['formDisponiveis'],$form['info']);

				return;
			case null:
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableauxLPO::checaAtomico($pai['info'])){
					if(FuncoesTableauxLPO::casarFormulaLPO($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo'] == 'not' ? '0':'1';	
				}
				
				FuncoesTableauxLPO::removerFormula($pai['formDisponiveis'],$form['info']);
				
				return;
			default:
				//print_r($form['hashAtomos']);
				break;
		}
	}
	public static function checaAtomico($form){
		//@ colocado para previnir que fórmulas não instanciadas deem warning
		if (@$form['esquerdo']==NULL && (@$form['conectivo']==NULL || @$form['conectivo']='not')) {
			return true;
		}
		else{
			return false;
		}

		
	}

	//Se houver digitação incorreta gera uma exceção (trabalhar na exceção depois)
	public static function negaPerguntaLPO($listaFormulas,$tamanho){
		//Nega a pergunta
		$listaFormulas[$tamanho-1]="not".$listaFormulas[$tamanho-1];
		//Tratar a entrada, verificação de digitação correta
		foreach ($listaFormulas as $key => $value) {
			FuncoesAuxiliares::verificaFormulaCorreta($listaFormulas[$key]);
			$entradaConvertida[$key]=ParsingFormulas::resolveParentesesTableauxLPO($listaFormulas[$key]);
		}
		
		return $entradaConvertida;
	}
	public static function removerFormula(&$listaFormulas,$form){
		foreach ($listaFormulas as $key => $value) {
			if ($value['info']==$form) {
				unset($listaFormulas[$key]);
				return;
			}
		}
	}
	public static function adicionaArray(&$array,&$valor){
		$tam=count($array);
		$soma=1;
		if ($tam!=0) {
			//Consertar o índice do array para evitar sobreposição
			while (@$array[$tam+$soma]!=null) {
				$soma++;
			}
			$array[$tam+$soma]=&$valor;
		}
		else{
			$array[0]=&$valor;
		}
	}
	public static function casarFormulaLPO($hash,$form){
		$aux=$form['conectivo']['operacao'] == "not" ? '0':'1';
		foreach ($hash as $key => $value) {			
			//Verifico se alguma vez esse cara já foi setado na hash
			if(!is_null($hash[$key])){
				if(($hash[$key]==!$aux) && ($form['direito']==$key)){
					return true;
				}				
			}
		}
		return false;
	}
	public static function juntarArrays(&$array1,$array2){
		$arrayAux=array();
		$igual=false;
		foreach ($array2 as $key2 => $value2) {
			$igual=false;
			foreach ($array1 as $key => $value) {
				if (@$array1[$key]['info']==@$array2[$key2]['info']) {
					$igual=true;
				}
			}
			if (!$igual) {
				array_push($arrayAux,$array2[$key2]);
			}
		}
		foreach ($arrayAux as $key => $value) {
			array_push($array1, $arrayAux[$key]);
		}
	}
	public static function todasFechadas($nosFolha,&$contador){
		$total=count($nosFolha);
		$total2=0;
		foreach ($nosFolha as $key => $value) {
			if ($nosFolha[$key]['filhoCentral']=='fechado') {
				$total2++;
			}
		}
		if ($total==$total2 && $contador!=0) {
			return true;
		}
		else{
			return false;
		}
	}
	public static function imprimeArvore(&$no){
		if (@$no['info']!=NULL) {
			print_r($no['info']);
			FuncoesTableaux::verificaStatusNo($no);
		}
		
		if(@$no['filhoCentral']!=NULL && @$no['filhoCentral']!='fechado'){
			@FuncoesTableaux::imprimeArvore($no['filhoCentral']);
		}
		if(@$no['filhoEsquerdo'] && @$no['filhoEsquerdo']!='fechado'){
			FuncoesTableaux::imprimeArvore(@$no['filhoEsquerdo']);
		}
		if(@$no['filhoDireito'] && @$no['filhoDireito']!='fechado'){
			FuncoesTableaux::imprimeArvore(@$no['filhoDireito']);
		}
	}

	//Função utilizada somente por imprimArvore para ajustaro formato da impressão
	public static function verificaStatusNo(&$no){
		switch($no){
			case @$no['atualCentral']:
				print "  Central <br>";
				break;
			case @$no['atualEsquerdo']:
				print "  Esquerda ------ ";
				break;
			case @$no['atualDireito']:
				print "  Direita <br>";
				break;
			default:
				if(@$no['info']=="fechado"){
					break;
				}
				//print "<br>Nó não categorizado<br>";
		}
	}
	public static function formataFormulasTableaux(&$form){
		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['info']['esquerdo'])>3){
			$aux=FuncoesTableaux::resolveParentesesTableaux($form['info']['esquerdo']);
			$form['info']['esquerdo']=$aux;
			FuncoesTableaux::formataFormulasTableaux($form['info']['esquerdo']);
		}
		if(@strlen(@$form['info']['direito'])>3){
			$aux=FuncoesTableaux::resolveParentesesTableaux($form['info']['direito']);
			$form['info']['direito']=$aux;
			FuncoesTableaux::formataFormulasTableaux($form['info']['direito']);
		}
	}
	//Função que corrige casos em que temos um campo array do tipo fórmula dentro de outro
	//array do tipo fórmula com um dos campos (esquerdo ou direito) vazio
	public static function corrigeArrays(&$form){
		if (@$form['info']['esquerdo']==NULL && @is_array($form['info']['direito'])) {
			$aux1=$form['info']['direito'];
			$form['info']['esquerdo']=$aux1['info']['esquerdo'];
			$form['info']['conectivo']=$aux1['info']['conectivo'];
			$form['info']['direito']=$aux1['info']['direito'];
			return;
		}
		if (@$form['info']['direito']==NULL && @is_array($form['info']['esquerdo'])) {
			$aux1=$form['info']['esquerdo'];
			$form['info']['esquerdo']=$aux1['info']['esquerdo'];
			$form['info']['conectivo']=$aux1['info']['conectivo'];
			$form['info']['direito']=$aux1['info']['direito'];
		}
		return;
	}
	//Função que processa o equivalente a um not(array)
	//Para processar not(atomo) funções não são necessárias
	public static function negaArrayTableauxLPO(&$form){
		if ($form['conectivo']['operacao']=='e') {
			$form['conectivo']['operacao']='not_e';
		}
		elseif ($form['conectivo']['operacao']=='ou') {
			$form['conectivo']['operacao']='not_ou';
		}
		elseif ($form['conectivo']['operacao']=='implica') {
			$form['conectivo']['operacao']='not_implica';
		}
		elseif ($form['conectivo']['operacao']=='not_e') {
			$form['conectivo']['operacao']='e';
		}
		elseif ($form['conectivo']['operacao']=='not_ou') {
			$form['conectivo']['operacao']='ou';
		}
		elseif ($form['conectivo']['operacao']=='not_implica') {
			$form['conectivo']['operacao']='implica';
		}
		elseif ($form['conectivo']['operacao']=='paraTodo') {
			$form['conectivo']['operacao']='not_paraTodo';
		}
		elseif ($form['conectivo']['operacao']=='not_paraTodo') {
			$form['conectivo']['operacao']='paraTodo';
		}
		elseif ($form['conectivo']['operacao']=='xist') {
			$form['conectivo']['operacao']='not_xist';
		}
		elseif ($form['conectivo']['operacao']=='not_xist') {
			$form['conectivo']['operacao']='xist';
		}
	}
	//Função para armazenar a estrutura de dados no histórico
	public static function armazenaHistorico(&$arrayHistorico,$nosFolha,$raiz,$numPasso,$listaFormulasDisponiveis){
		$aux['nosFolha']=$nosFolha;
		$aux['raiz']=$raiz;
		$aux['numPasso']=$numPasso;
		$aux['listaFormulasDisponiveis']=$listaFormulasDisponiveis;
		array_push($arrayHistorico,$aux);
	}
	//Função para voltar um passo na execução do Tableaux
	public static function voltaUmPasso(&$arrayHistorico,&$nosFolha,&$raiz,&$numPasso,&$listaFormulasDisponiveis){
		$tam=count($arrayHistorico);
		unset($arrayHistorico[$tam-1]);	
		$raiz=$arrayHistorico[$tam-2]['raiz'];
		$nosFolha=$arrayHistorico[$tam-2]['nosFolha'];
		$listaFormulasDisponiveis=$arrayHistorico[$tam-2]['listaFormulasDisponiveis'];
		$numPasso=$arrayHistorico[$tam-2]['numPasso'];
	}
}
