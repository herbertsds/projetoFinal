<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesAuxiliares;

class FuncoesTableauxLPO extends Model
{
    
	public static function escolhaEficiente(&$listaFormulasDisponiveis,&$hashInicial,&$hashFuncaoInicial,&$constantesInicial,&$nosFolha,&$historicoVariaveis,&$raiz,&$contador){
		//Verificação para saber se a função foi chamada mesmo
		//que todas os ramos já estejam fechados

		if (FuncoesTableauxLPO::todasFechadas($nosFolha,$contador)) {
			return "fechado";
		}
		$conectivosEficientes=array("e","not_ou","not_implica","notnot","paraTodo","not_paraTodo","xist","not_xist");
		
		foreach ($listaFormulasDisponiveis as $key => $value) {
			ParsingFormulas::formataFormulasTableauxLPO($listaFormulasDisponiveis[$key]);
			//ParsingFormulas::formataFormulasTableauxLPO($listaFormulasDisponiveis[$key]);
			ParsingFormulas::formataFormulasTableauxLPO($listaFormulasDisponiveis[$key]['esquerdo']);
			ParsingFormulas::formataFormulasTableauxLPO($listaFormulasDisponiveis[$key]['direito']);
		}
		//print "<br>Chamei Aqui<br>";
		//print_r($listaFormulasDisponiveis);
		//dd(1);

		$conectivosUmaVez=array('not_paraTodo','xist','notnot');
		$checarMudancaDePrioridade=true;
		

		//Aplicação na raiz
		//-------------------------------------------------------------------
		//Caso 1 - Raiz é eficiente
		//Busque o primeiro nó eficiente que existir e aplique a fórmula nele
		if ($contador==0) {
			foreach ($listaFormulasDisponiveis as $key => $value) {
				//Checar primeiro se há os conectivos nos quais a constante só pode aparecer pela primeira vez
				foreach ($listaFormulasDisponiveis as $key2 => $value2) {
					if (in_array($value2['info']['conectivo']['operacao'], $conectivosUmaVez)) {
						$checarMudancaDePrioridade=false;
						$value=$value2;
						$key=$key2;
					}
				}
				//Checar primeiro se vai aparecer na próxima iteração conectivos nos quais a constante só pode aparecer pela primeira vez
				if ($checarMudancaDePrioridade) {
					foreach ($listaFormulasDisponiveis as $key2 => $value2) {
						if (FuncoesTableauxLPO::verificaPotencialPrioridade($value2)) {
							$checarMudancaDePrioridade=false;
							$value=$value2;
							$key=$key2;
						}
					}
				}
				//Fazer a segunda checagem se vai aparecer na próxima iteração conectivos nos quais a constante só pode aparecer pela primeira vez
				if ($checarMudancaDePrioridade) {
					foreach ($listaFormulasDisponiveis as $key2 => $value2) {
						if (FuncoesTableauxLPO::verificaPotencialPrioridade2($value2)) {
							$value=$value2;
							$key=$key2;
						}
					}
				}
				if (in_array($value['info']['conectivo']['operacao'],$conectivosEficientes)){
					if ($contador==0) {
						//Correções na fórmula
						$raiz=$listaFormulasDisponiveis[$key];
						$raiz['formDisponiveis']=$listaFormulasDisponiveis;
						$raiz['hashAtomos']=$hashInicial;
						$raiz['hashAtomosFuncoes']=$hashFuncaoInicial;
						$raiz['constantesUsadas']=$constantesInicial;
						//print "<br>Aplicando regra em<br>";
						//print_r($raiz['info']);
						FuncoesTableauxLPO::aplicaRegraLPO($raiz,$raiz,$nosFolha,$contador);
						FuncoesTableauxLPO::removerFormula($listaFormulasDisponiveis,$raiz['info']);
						FuncoesTableauxLPO::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
						return;
					}
				}
			}
		}
		$checarMudancaDePrioridade=true;
		//Caso 2 - Raiz não é eficiente
		//Se não há fórmula eficiente, aplique no primeiro elemento disponível
		if ($contador==0) {
			foreach ($listaFormulasDisponiveis as $key => $value) {
				//Checar primeiro se há os conectivos nos quais a constante só pode aparecer pela primeira vez
				foreach ($listaFormulasDisponiveis as $key2 => $value2) {
					if (in_array($value2['info']['conectivo']['operacao'], $conectivosUmaVez)) {
						$value=$value2;
						$key=$key2;
						$checarMudancaDePrioridade=false;
					}
				}
				//Checar primeiro se vai aparecer na próxima iteração conectivos nos quais a constante só pode aparecer pela primeira vez
				if ($checarMudancaDePrioridade) {
					foreach ($listaFormulasDisponiveis as $key2 => $value2) {
						if (FuncoesTableauxLPO::verificaPotencialPrioridade($value2)) {
							$checarMudancaDePrioridade=false;
							$value=$value2;
							$key=$key2;
						}
					}
				}
				//Fazer a segunda checagem se vai aparecer na próxima iteração conectivos nos quais a constante só pode aparecer pela primeira vez
				if ($checarMudancaDePrioridade) {
					foreach ($listaFormulasDisponiveis as $key2 => $value2) {
						if (FuncoesTableauxLPO::verificaPotencialPrioridade2($value2)) {
							$value=$value2;
							$key=$key2;
						}
					}
				}
				if ($value['info']['conectivo']['operacao']!=null && $value['info']['conectivo']['operacao']!='not'){
					$raiz=$listaFormulasDisponiveis[$key];			
				}			
			}
			
			$raiz['formDisponiveis']=$listaFormulasDisponiveis;
			$raiz['hashAtomos']=$hashInicial;
			$raiz['hashAtomosFuncoes']=$hashFuncaoInicial;
			$raiz['constantesUsadas']=$constantesInicial;
			FuncoesTableauxLPO::aplicaRegraLPO($raiz,$raiz,$nosFolha,$contador);
			//print "<br>Aplicando regra em<br>";
			//print_r($raiz['info']);
			FuncoesTableauxLPO::removerFormula($listaFormulasDisponiveis,$raiz['info']);
			FuncoesTableauxLPO::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
			return;
		}
		//--------------------------------------------------------------------	
		
		$checarMudancaDePrioridade=true;
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
					//Caso 1.1 - Pega um conectivo que só cuja a substituição por constante só pode tê-la aparecendo pela primeira vez
					foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
						if (in_array($value3['info']['conectivo']['operacao'], $conectivosUmaVez)) {
							$formDispAtual=$value3;
							$key2=$key3;
							$checarMudancaDePrioridade=false;
						}
					}
					//Caso 1.2 - Pega um conectivo que gerará uma substituição por constante que só pode ser feita pela primeira vez
					//Na próxima iteração
					if ($checarMudancaDePrioridade) {
						foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
							if (FuncoesTableauxLPO::verificaPotencialPrioridade($value3)) {
								$checarMudancaDePrioridade=false;
								$formDispAtual=$value3;
								$key2=$key3;
							}
						}
					}
					//Caso 1.3 - Segunda verificação pega um conectivo que gerará uma substituição por constante que só pode ser feita pela primeira vez
					//Na próxima iteração
					if ($checarMudancaDePrioridade) {
						foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
							if (FuncoesTableauxLPO::verificaPotencialPrioridade2($value3)) {
								$formDispAtual=$value3;
								$key2=$key3;
							}
						}
					}
					
					//Correções na fórmula
					ParsingFormulas::formataFormulasTableauxLPO($noFolhaAtual['formDisponiveis'][$key2]);
					FuncoesTableauxLPO::corrigeArrays($noFolhaAtual['formDisponiveis'][$key2]);
					
					//Se achar conectivo eficiente aplique a regra
					if (in_array($formDispAtual['info']['conectivo']['operacao'],$conectivosEficientes)){
						//print "<br>Aplicando regra em<br>";
						//print_r($formDispAtual['info']);
						//print "<br>Com nó pai sendo<br>";
						//print_r(@$nosFolha[$key]['info']);
						FuncoesTableauxLPO::aplicaRegraLPO($formDispAtual,$nosFolha[$key],$nosFolha,$contador);
						FuncoesTableauxLPO::removerFormula($listaFormulasDisponiveis,$formDispAtual['info']);
						FuncoesTableauxLPO::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
						//OTIMIZAR - NÃO PODE ACONTECER
						//Verificação para saber se a função foi chamada mesmo
						//que todas os ramos já estejam fechados
						if (FuncoesTableauxLPO::todasFechadas($nosFolha,$contador)) {
							//print "<br>Todos os ramos já estão fechados<br>";
							//print $contador."<br>";
							return;
						}
						
						return;
					}
				}	
			}
		}
		$checarMudancaDePrioridade=true;
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
					//Caso 2.1 - Pega um conectivo que só cuja a substituição por constante só pode tê-la aparecendo pela primeira vez
					foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
						if (in_array($value3['info']['conectivo']['operacao'], $conectivosUmaVez)) {
							$formDispAtual=$value3;
							$key2=$key3;
							$checarMudancaDePrioridade=false;
						}
					}
					//Caso 2.2 - Pega um conectivo que gerará uma substituição por constante que só pode ser feita pela primeira vez
					//Na próxima iteração
					if ($checarMudancaDePrioridade) {
						foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
							if (FuncoesTableauxLPO::verificaPotencialPrioridade($value3)) {
								$checarMudancaDePrioridade=false;
								$formDispAtual=$value3;
								$key2=$key3;
							}
						}
					}
					//Caso 2.3 - Segunda verificação, pega um conectivo que gerará uma substituição por constante que só pode ser feita pela primeira vez
					//Na próxima iteração
					if ($checarMudancaDePrioridade) {
						foreach ($noFolhaAtual['formDisponiveis'] as $key3 => $value3) {
							if (FuncoesTableauxLPO::verificaPotencialPrioridade2($value3)) {
								$formDispAtual=$value3;
								$key2=$key3;
							}
						}
					}
					//Correções na fórmula
					ParsingFormulas::formataFormulasTableauxLPO($noFolhaAtual['formDisponiveis'][$key2]);
					FuncoesTableauxLPO::corrigeArrays($noFolhaAtual['formDisponiveis'][$key2]);
					//print "<br>Aplicando regra em<br>";
					//print_r($formDispAtual['info']);
					//print "<br>Com nó pai sendo<br>";
					//print_r($nosFolha[$key]['info']);
					FuncoesTableauxLPO::aplicaRegraLPO($formDispAtual,$nosFolha[$key],$nosFolha,$contador);
					FuncoesTableauxLPO::removerFormula($listaFormulasDisponiveis,$formDispAtual['info']);
					FuncoesTableauxLPO::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
					//OTIMIZAR - NÃO PODE ACONTECER
					//Verificação para saber se a função foi chamada mesmo
					//que todas os ramos já estejam fechados
					if (FuncoesTableauxLPO::todasFechadas($nosFolha,$contador)) {
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
			FuncoesTableaux::aplicaRegraLPO($raiz,$raiz,$nosFolha,$contador);
			FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$formEscolhida['info']);
			return;
		}
		else{
			FuncoesTableaux::aplicaRegraLPO($formEscolhida,$noFolhaEscolhido,$nosFolha,$contador);
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
		$auxForm['constantesUsadas']=array();
		$auxForm['hashAtomos']=array();
		$auxForm['hashAtomosFuncoes']=array();
		$auxForm['funcoesComSubstituicao']=null;
		return $auxForm;

	}
	public static function checaAtomicoLPO($form){
		if ((@$form['esquerdo']==null && @$form['conectivo']['operacao']==null) || (@$form['conectivo']['operacao']=='not' && @$form['esquerdo']==null )) {
			if (!(@is_array($form['direito']))) {
				return true;
			}
			
		}
		else{
			return false;
		}
	}
	//Método que recebe uma fórmula na última camada do array, a variável que deseja substituir, a constante
	//e aplica a substituição.
	public static function substituiPorConstante($constante, &$form, $variavel){
		for ($i=0; $i < strlen($form); $i++) { 
			if ($form[$i]==$variavel) {
				$form[$i]=$constante;
			}
		}
	}
	//Método que recebe uma fórmula, boolean repetir para o caso do conectivo permitir ou não a repetição de constante,
	//A lista de constantes daquele ramo, a hash de funções atômicas, a lista total de constantes que podem ser usadas,
	//A variável que se deseja substituir. Recursivamente busca na fórmula e efetua a substituição
	public static function atribuiConstanteFormulaArray(&$form,$repetir,$listaGlobalConstantes,$variavel,&$listaAcumuladora,$listaConstantes=null,$hashAtomosFuncoes=null){
		/*if ($form['info']['esquerdo']==null && $form['info']['direito']==null) {
			print "<br>Retornei direto<br>";
			return;
		}*/
		$constante=null;
		if (is_array($form['info']['esquerdo'])) {
			//print "<br>Fui pela esquerda<br>";
			$aux=$form;
			$form['info']['conectivo']['variavel']=null;
			FuncoesTableauxLPO::corrigeArraysLPO($form);
			//Pega todos os átomos contidos na fórmula para aplicar a substituição por constante em todos eles
			if ((@FuncoesTableauxLPO::checaAtomicoLPO($form['info']['esquerdo']) || !is_array($form['info']['esquerdo'])) &&($form['info']['esquerdo']!=null)) {
				if (!in_array($form['info']['esquerdo'], $listaAcumuladora)) {
					FuncoesTableauxLPO::adicionaArray($listaAcumuladora, $form['info']['esquerdo']);
					//print "<br>Lista Acumuladora<br>";
					//dd($listaAcumuladora);
					//print_r($listaAcumuladora);
				}				
			}
			if ($aux!=$form) {
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($form,$repetir,$listaGlobalConstantes,$variavel,$listaAcumuladora,$listaConstantes,$hashAtomosFuncoes);			}
			else{
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($form['info']['esquerdo'],$repetir,$listaGlobalConstantes,$variavel,$listaAcumuladora,$listaConstantes,$hashAtomosFuncoes);
			}
		}

		if (is_array($form['info']['direito'])) {
			//print "<br>Fui pela direita<br>";
			$aux=$form;
			$form['info']['conectivo']['variavel']=null;
			FuncoesTableauxLPO::corrigeArraysLPO($form);
			//Pega todos os átomos contidos na fórmula para aplicar a substituição por constante em todos eles
			if ((@FuncoesTableauxLPO::checaAtomicoLPO($form['info']['esquerdo']) || !is_array($form['info']['esquerdo'])) &&($form['info']['esquerdo']!=null)) {
				if (!in_array($form['info']['esquerdo'], $listaAcumuladora)) {
					FuncoesTableauxLPO::adicionaArray($listaAcumuladora, $form['info']['esquerdo']);
					//print "<br>Lista Acumuladora<br>";
					//dd($listaAcumuladora);
					//print_r($listaAcumuladora);
				}				
			}
			if ($aux!=$form) {
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($form,$repetir,$listaGlobalConstantes,$variavel,$listaAcumuladora,$listaConstantes,$hashAtomosFuncoes);			
			}
			else{
				//print '<br>Pega o direito<br>';
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($form['info']['direito'],$repetir,$listaGlobalConstantes,$variavel,$listaAcumuladora,$listaConstantes,$hashAtomosFuncoes);
			}
		}
		elseif (!(is_array($form['info']['direito']))) {
			$aux=$form['info'];
			$valor=null;
			$funcao=$form['info']['direito'][0];
			//Inicialização da hash de funções
			if (@$form['funcoesComSubstituicao'][$funcao]==null) {
				$form['funcoesComSubstituicao'][$funcao]=array();
			}
			
			if ($repetir) {
				foreach ($listaGlobalConstantes as $key => $value) {
					$aux=$form['info'];
					$valor=$value;
					
					//print "<br>Aux<br>";
					//print_r($aux);
					//dd($listaAcumuladora);
					foreach ($listaAcumuladora as $key2 => $value2) {
						FuncoesTableauxLPO::substituiPorConstante($value,$listaAcumuladora[$key2],$variavel);
					}
					//Caso já exista uma constante definida para esta variável e função
					//Devo manter a constante para substituições futuras
					if (@$form['funcoesComSubstituicao'][$funcao][$variavel]) {
						FuncoesTableauxLPO::substituiPorConstante($form['funcoesComSubstituicao'][$funcao][$variavel],$aux['direito'],$variavel);
						if ($aux['esquerdo']!=null) {
							FuncoesTableauxLPO::substituiPorConstante($form['funcoesComSubstituicao'][$funcao][$variavel],$aux['direito'],$variavel);
						}
						break;
					}
					FuncoesTableauxLPO::substituiPorConstante($value,$aux['direito'],$variavel);
					if ($aux['esquerdo']!=null) {
						FuncoesTableauxLPO::substituiPorConstante($value,$aux['esquerdo'],$variavel);
					}
					else{
						if ($aux['conectivo']['variavel']==$variavel) {
							$aux['conectivo']['variavel']=null;
						}
					}
					//caso haja 2 átomos
					if ($aux['esquerdo']!=null && is_string($aux['esquerdo'])) {
							if (FuncoesTableauxLPO::casarFormulaLPO($hashAtomosFuncoes,$aux['esquerdo'])) {
								if ($aux['conectivo']['variavel']==$variavel) {
									$aux['conectivo']['variavel']=null;
								}
								break;
							}
							if (FuncoesTableauxLPO::casarFormulaLPO($hashAtomosFuncoes,$aux['direito'])) {
								if ($aux['conectivo']['variavel']==$variavel) {
									$aux['conectivo']['variavel']=null;
								}
								break;
							}
					}
					if (FuncoesTableauxLPO::casarFormulaLPO($hashAtomosFuncoes,$aux)) {
						if ($aux['conectivo']['variavel']==$variavel) {
							$aux['conectivo']['variavel']=null;
						}
						break;
					}

				}
				//Verificação adicional para pegar um cara que possa casar no futuro
				if ($valor=='z') {
					$aux2=$form['info'];
					foreach ($form['constantesUsadas'] as $key => $value) {
						FuncoesTableauxLPO::substituiPorConstante($value,$aux2['direito'],$variavel);
						if ($aux['esquerdo']!=null) {
							FuncoesTableauxLPO::substituiPorConstante($value,$aux2['esquerdo'],$variavel);
						}
						else{
							if ($aux['conectivo']['variavel']==$variavel) {
								$aux['conectivo']['variavel']=null;
							}
						}
						foreach ($form['formDisponiveis'] as $key2 => $value2) {
							if ($value2['info']['direito']==$aux2['direito']) {
								if ($aux2['conectivo']['variavel']==$variavel) {
									$aux['conectivo']['variavel']=null;
								}
								$aux=$aux2;
								$form['info']=$aux;
								foreach ($listaAcumuladora as $key2 => $value2) {
									FuncoesTableauxLPO::substituiPorConstante($value,$listaAcumuladora[$key2],$variavel);
								}
								array_push($form['constantesUsadas'],$value);
								return;
							}
						}
					}
				}
				
			}
			elseif (!$repetir) {
				foreach ($listaGlobalConstantes as $key => $value) {
					if (!in_array($value, $listaConstantes)) {
						foreach ($listaAcumuladora as $key2 => $value2) {
							//Caso já exista uma constante definida para esta variável e função
							//Devo manter a constante para substituições futuras
							if (@$form['funcoesComSubstituicao'][$funcao][$variavel]) {
								FuncoesTableauxLPO::substituiPorConstante($form['funcoesComSubstituicao'][$funcao][$variavel],$aux['direito'],$variavel);
								if ($aux['esquerdo']!=null) {
									FuncoesTableauxLPO::substituiPorConstante($form['funcoesComSubstituicao'][$funcao][$variavel],$aux['direito'],$variavel);
								}
								break;
							}
							FuncoesTableauxLPO::substituiPorConstante($value,$listaAcumuladora[$key2],$variavel);
						}
						FuncoesTableauxLPO::substituiPorConstante($value,$aux['direito'],$variavel);
						if ($aux['esquerdo']!=null) {
							FuncoesTableauxLPO::substituiPorConstante($value,$aux['esquerdo'],$variavel);
						}
						else{
							if ($aux['conectivo']['variavel']==$variavel) {
								$aux['conectivo']['variavel']=null;
							}
						}
						array_push($form['constantesUsadas'],$value);
						$constante=$value;
						break;
						
					}
				}
			}
			if (@$form['funcoesComSubstituicao'][$funcao][$variavel]==null) {
				$form['funcoesComSubstituicao'][$funcao][$variavel]=$constante;
			}
			$form['info']=$aux;
			array_push($form['constantesUsadas'],$valor);
		}
	}
	public static function casarFormula($hash,$form){
		$aux=$form['conectivo'] == "not" ? '0':'1';
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
	public static function casarFormulaLPO($hash,$form){
		if ($hash==null) {
			return false;
		}
		if (is_string($form)) {
			$aux='0';
			$formula=$form;
		}
		else{
			$formula=$form['direito'];
			$aux=$form['conectivo']['operacao'] == "not" ? '0':'1';
		}
		
		foreach ($hash as $key => $value) {			
		//Verifico se alguma vez esse cara já foi setado na hash
			if(!is_null($hash[$key])){
				if(($hash[$key]==!$aux) && ($formula==$key)){
					return true;
				}				
			}
		}
		return false;
	}
	public static function aplicaRegraLPO(&$form,&$pai,&$nosFolha,&$contador){
		$listaGlobalConstantes=Formula::getListaConstantesGlobal();
		$listaGlobalConectivos=array('e','ou','implica','not_e','not_ou','not_implica','paraTodo','not_paraTodo','xist','not_xist');
		$listaAcumuladora=[];
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
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen2['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen2['constantesUsadas']=$pai['constantesUsadas'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}			
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen2['hashAtomosFuncoes'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
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
				$noAuxEsq['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxDir['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxEsq['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxDir['constantesUsadas']=$pai['constantesUsadas'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomosFuncoes'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomosFuncoes'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		

				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxDir['hashAtomosFuncoes'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomosFuncoes'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}	

				FuncoesTableauxLPO::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableauxLPO::removerFormula($noAuxDir['formDisponiveis'],$form['info']);	
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxDir);
				}
				//dd($noAuxEsq);	
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
				$noAuxEsq['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxDir['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxEsq['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxDir['constantesUsadas']=$pai['constantesUsadas'];


				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);
				

				//---------------------------------------MANIPULAÇÃO ESPECÍFICA DA IMPLICAÇÃO---------------------------------------
				//O lado esquerdo deve passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxEsq['info']);
				}

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomosFuncoes'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomosFuncoes'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		

				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxDir['hashAtomosFuncoes'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomosFuncoes'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';
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
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];

				//Correções na estrutura de dados
				
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);
				

				//Manipulação específica de notnot
				$noAuxCen1['filhoCentral']=null;

				//Se a fórmula for átomo simples
				if (FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					if ($noAuxEsq['info']['conectivo']['operacao']=='notnot') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']['operacao']=null;
					}
				}
				if (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if ($noAuxEsq['info']['conectivo']['operacao']=='notnot') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']['operacao']=null;
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
				//Checa se é átomo comum
				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}	
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				//Caso o nó não esteja fechado, adicionar como nó folha para dar prosseguimento
				if (@$noAuxCen1['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen1);
				}
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
				$noAuxEsq['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxDir['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxEsq['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxDir['constantesUsadas']=$pai['constantesUsadas'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxEsq);			
				FuncoesTableauxLPO::corrigeArrays($noAuxDir);

				//------------------------------------------MANIPULAÇÃO ESPECÍFICA DO NOT_E---------------------------------------
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableaux::checaAtomico($noAuxEsq['info']) || FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxEsq['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxEsq['info']);
				}

				//Se o lado direito for átomo
				if (FuncoesTableaux::checaAtomico($noAuxDir['info']) || FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					if ($noAuxDir['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxDir['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxDir['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxDir['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxDir['info']);
				}
				//---------------------------------------------------------------------------------------------------------------------------

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxEsq['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxEsq['hashAtomosFuncoes'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomosFuncoes'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		

				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxDir['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxDir['hashAtomosFuncoes'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomosFuncoes'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo']['operacao'] == 'not' ? '0':'1';
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
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen2['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen2['constantesUsadas']=$pai['constantesUsadas'];

				

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);


				//-------------------------MANIPULAÇÃO ESPECÍFICA DO NOT_OU----------------------------------
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo simples
				/*if (FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					if ($noAuxCen1['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxCen1['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxCen1['info']['conectivo']['operacao']='not';
					}
				}*/
				//Se o lado esquerdo for átomo LPO
				if (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if ($noAuxCen1['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxCen1['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxCen1['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen1['info']);
				}
				

				//Se o lado direito for átomo simples ou composto
				if (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen2['info']);
				}
				
				//----------------------------------------------------------------------------------------------------	

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				//PARA O PRIMEIRO TERMO
				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableauxLPO::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';		
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}

				//PARA O SEGUNDO TERMO
				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';				
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen2['hashAtomosFuncoes'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}

				//Remover da lista de fórmula disponíveis a fórmula que acabou de ser usada
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
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen2['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen2['constantesUsadas']=$pai['constantesUsadas'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);			
				FuncoesTableauxLPO::corrigeArrays($noAuxCen2);

				//------------------------MANIPULAÇÃO ESPECÍFICA DO NOT_IMPLICA-----------------------------

				//O lado direito deve passar a ter um not externamente
				//Se o lado direito for átomo simples ou composto
				if (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']['operacao']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']['operacao']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']['operacao']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])){
					FuncoesTableauxLPO::negaArrayTableauxLPO($noAuxCen2['info']);
				}
				//------------------------------------------------------------------------------------------------------------------------
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				//PARA O PRIMEIRO TERMO
				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableauxLPO::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';		
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}

				//PARA O SEGUNDO TERMO
				//Checa se é átomo comum
				/*if(FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';				
				}*/
				//Se não for nenhum tipo de átomo, entra como fórmula disponível para usar depois
				if(!FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);

				}
				//Checa se é átomo LPO
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen2['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen2['hashAtomosFuncoes'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomosFuncoes'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}

				//Remover da lista de fórmula disponíveis a fórmula que acabou de ser usada
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
				$noAuxCen1['info']=$form['info'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen1['funcoesComSubstituicao']=$pai['funcoesComSubstituicao'];

				//Correções na estrutura de dados
				
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);
				//Manipulação específica de paraTodo
				//print "<br>Antes a aplicação da regra<br>";
				//print_r($noAuxCen1['info']);
				$noAuxCen1['filhoCentral']=null;
				$noAuxCen1['info']['conectivo']['operacao']=null;
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,true,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);
				//dd($noAuxCen1);	
				//print "<br>Após a aplicação da regra<br>";
				//print_r($noAuxCen1['info']);
				//dd($form);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}

				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				if (@$noAuxCen1['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen1);
				}

				//dd($noAuxCen1);
				return;
			case 'xist':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']=$form['info'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen1['funcoesComSubstituicao']=$pai['funcoesComSubstituicao'];


				//Correções na estrutura de dados
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);				

				//Manipulação específica de xist
				$noAuxCen1['filhoCentral']=null;
				$noAuxCen1['info']['conectivo']['operacao']=null;
				FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,false,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);

				
				//dd($form);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				if (@$noAuxCen1['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen1);
				}
				return;

			case 'not_paraTodo':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']=$form['info'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen1['funcoesComSubstituicao']=$pai['funcoesComSubstituicao'];

				//Correções na estrutura de dados
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);

				

				//Manipulação específica de paraTodo
				$noAuxCen1['filhoCentral']=null;
				$noAuxCen1['info']['conectivo']['operacao']=null;
				//dd($noAuxCen1);
				//print "<br>Antes da aplicação da fórmula<br>";
				//print_r($noAuxCen1['constantesUsadas']);
				if (@$noAuxCen1['info']['direito']['info']['conectivo']['operacao']=='not') {
					$variavel=$noAuxCen1['info']['conectivo']['variavel'];
					$noAuxCen1['info']['conectivo']['operacao']=null;
					$noAuxCen1['info']['conectivo']['variavel']=null;
					FuncoesTableauxLPO::corrigeArraysLPO($noAuxCen1);
					//Negação do not
					$noAuxCen1['info']['conectivo']['operacao']=null;
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,false,$listaGlobalConstantes,$variavel,$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);

					//dd($noAuxCen1);
				}

				//Caso haja uma fórmula no lado direito com conectivo, devemos negar o conectivo
				elseif (@in_array($noAuxCen1['info']['direito']['info']['conectivo']['operacao'], $listaGlobalConectivos)) {

					FuncoesTableauxLPO::negaConectivo($noAuxCen1['info']['direito']['info']['conectivo']['operacao']);
					print "<br>Antes de negar parte 1<br>";
					print_r($noAuxCen1['info']);
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,false,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);
					print "<br>Depois de negar parte 1<br>";
					print_r($noAuxCen1['info']);
				}
				else{
					print "<br>Antes de negar<br>";
					print_r($noAuxCen1['info']);
					$noAuxCen1['info']['conectivo']['operacao']='not';
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,false,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);
					print "<br>Depois de negar<br>";
					print_r($noAuxCen1['info']);
					//dd($noAuxCen1);
				}
				//print "<br>Depois da aplicação da fórmula<br>";
				//print_r($noAuxCen1['constantesUsadas']);
				
				//dd($form);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				if (@$noAuxCen1['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen1);
				}
				//dd($noAuxCen1);
				return;

			case 'not_xist':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableauxLPO::removerFormula($nosFolha,$pai['info']);
				}
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']=$form['info'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];
				$noAuxCen1['hashAtomosFuncoes']=$pai['hashAtomosFuncoes'];
				$noAuxCen1['constantesUsadas']=$pai['constantesUsadas'];
				$noAuxCen1['funcoesComSubstituicao']=$pai['funcoesComSubstituicao'];

				//Correções na estrutura de dados
				FuncoesTableauxLPO::corrigeArrays($noAuxCen1);

				//Manipulação específica de paraTodo
				$noAuxCen1['filhoCentral']=null;
				//print_r($noAuxCen1['info']['direito']['info']['conectivo']);
				//dd($noAuxCen1);
				//Caso haja um átomo com not, fazer a correção do array
				$noAuxCen1['info']['conectivo']['operacao']=null;
				if (@$noAuxCen1['info']['direito']['info']['conectivo']['operacao']=='not') {
					$variavel=$noAuxCen1['info']['conectivo']['variavel'];
					$noAuxCen1['info']['conectivo']['operacao']=null;
					$noAuxCen1['info']['conectivo']['variavel']=null;
					FuncoesTableauxLPO::corrigeArraysLPO($noAuxCen1);
					//Negação do not
					$noAuxCen1['info']['conectivo']['operacao']=null;
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,true,$listaGlobalConstantes,$variavel,$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);

					//dd($noAuxCen1);
				}

				//Caso haja uma fórmula no lado direito com conectivo, devemos negar o conectivo
				elseif (@in_array($noAuxCen1['info']['direito']['info']['conectivo']['operacao'], $listaGlobalConectivos)) {
					FuncoesTableauxLPO::negaConectivo($noAuxCen1['info']['direito']['info']['conectivo']['operacao']);
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,true,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);
				}
				else{
					$noAuxCen1['info']['conectivo']['operacao']='not';
					FuncoesTableauxLPO::atribuiConstanteFormulaArray($noAuxCen1,true,$listaGlobalConstantes,$noAuxCen1['info']['conectivo']['variavel'],$listaAcumuladora,$noAuxCen1['constantesUsadas'],$noAuxCen1['hashAtomosFuncoes']);
					//dd($noAuxCen1);
				}
				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações

				/*if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}*/
				if(!FuncoesTableaux::checaAtomico($noAuxCen1['info']) && !FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}
				elseif (FuncoesTableauxLPO::checaAtomicoLPO($noAuxCen1['info'])) {
					//print "<br>Casarrrrrr<br>";
					//print_r($noAuxCen1['info']);
					if (FuncoesTableauxLPO::casarFormulaLPO($noAuxCen1['hashAtomosFuncoes'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomosFuncoes'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo']['operacao'] == 'not' ? '0':'1';
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				if (@$noAuxCen1['filhoCentral']!='fechado') {
						FuncoesTableauxLPO::adicionaArray($nosFolha, $noAuxCen1);
				}
				return;
			//Caso extra
			case 'not':
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableaux::checaAtomico($pai['info'])){
					if(FuncoesTableauxLPO::casarFormula($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}
				if(FuncoesTableauxLPO::checaAtomicoLPO($pai['info'])){
					if(FuncoesTableauxLPO::casarFormulaLPO($pai['hashAtomosFuncoes'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomosFuncoes'][$pai['info']['direito']]=$pai['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}

				
				FuncoesTableauxLPO::removerFormula($pai['formDisponiveis'],$form['info']);

				return;
			case null:
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableaux::checaAtomico($pai['info'])){
					if(FuncoesTableauxLPO::casarFormulaLPO($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo']['operacao'] == 'not' ? '0':'1';	
				}
				
				FuncoesTableauxLPO::removerFormula($pai['formDisponiveis'],$form['info']);
				
				return;
			default:
				//print_r($form['hashAtomos']);
				break;
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
	public static function negaConectivo(&$conectivo){
		if ($conectivo=='e') {
			$conectivo='not_e';
		}
		elseif ($conectivo=='ou') {
			$conectivo='not_ou';
		}
		elseif ($conectivo=='implica') {
			$conectivo='not_implica';
		}
		elseif ($conectivo=='not_e') {
			$conectivo='e';
		}
		elseif ($conectivo=='not_ou') {
			$conectivo='ou';
		}
		elseif ($conectivo=='not_implica') {
			$conectivo='implica';
		}
		elseif ($conectivo=='notnot') {
			$conectivo=null;
		}
		elseif ($conectivo=='paraTodo') {
			$conectivo='not_paraTodo';
		}
		elseif ($conectivo=='xist') {
			$conectivo='not_xist';
		}
		elseif ($conectivo=='not_paraTodo') {
			$conectivo='paraTodo';
		}
		elseif ($conectivo=='not_xist') {
			$conectivo='xist';
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
	/*public static function casarFormulaLPO($hash,$form){
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
	}*/
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
	public static function imprimeArvore(&$no,&$contador){
		
		if (@$no['info']!=NULL) {
			FuncoesTableauxLPO::converteFormulaStringTableaux($no['info']);
			print_r($no['info']);
			FuncoesTableauxLPO::verificaStatusNo($no);
			//print "<br>Lista de hashs<br>";
			//print_r($no['funcoesComSubstituicao']);
		}
		if ($contador==0) {
			$contador++;
			print "<br>";
		}
		if (@$no['filhoCentral']=='fechado'){
			print "<br>Fechado<br>";
		}

		
		if(@$no['filhoCentral']!=NULL && @$no['filhoCentral']!='fechado'){
			@FuncoesTableauxLPO::imprimeArvore($no['filhoCentral'],$contador);
		}
		if(@$no['filhoEsquerdo'] && @$no['filhoEsquerdo']!='fechado'){
			FuncoesTableauxLPO::imprimeArvore(@$no['filhoEsquerdo'],$contador);
		}
		if(@$no['filhoDireito'] && @$no['filhoDireito']!='fechado'){
			FuncoesTableauxLPO::imprimeArvore(@$no['filhoDireito'],$contador);
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
	//Função que corrige casos em que temos um campo array do tipo fórmula dentro de outro
	//array do tipo fórmula com um dos campos (esquerdo ou direito) vazio
	public static function corrigeArrays(&$form){
		$conectivos=array("paraTodo","xist","not_paraTodo","not_xist");
		if (@$form['info']['esquerdo']==NULL && @is_array($form['info']['direito']) && !in_array($form['info']['conectivo']['operacao'], $conectivos)) {
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
	public static function corrigeArraysLPO(&$form){
		$conectivos=array("paraTodo","xist","not_paraTodo","not_xist");
		if (@$form['info']['esquerdo']['operacao']==null && @$form['info']['conectivo']['operacao']==null){
			$aux=$form['info']['direito'];
			foreach ($form as $key => $value) {
				if ($key!='info') {
					$aux[$key]=$form[$key];
				}
			}
			$form=$aux;
		}
	}
	public static function temConectivo($form){
		$listaConectivos=array("^","v","-",'@','&');
		for ($i=0; $i < strlen($form) ; $i++) { 
			for ($j=0; $j < count($listaConectivos); $j++) { 
				if ($listaConectivos[$j]==$form[$i]) {
					return true;
				}
			}
		}
		return false;
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
	//Método que recebe um array fórmula e verifica se o conectivo é not_e,not_ou ou not_implica.
	//Sendo um desses, caso haja um paraTodo ou not_xist aninhado, retorno true
	public static function verificaPotencialPrioridade($form){
		$conectivosImportantes=array('not_e','not_ou','not_implica');
		$conectivosImportantes2=array('paraTodo','not_xist');
		if (!in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
			return false;
		}
		elseif (in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
			if (@in_array($form['info']['esquerdo']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
				return true;
			}
			if (@in_array($form['info']['direito']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
				return true;
			}
		}
		if(is_array($form['info']['esquerdo'])){
			FuncoesTableauxLPO::verificaPotencialPrioridade($form['info']['esquerdo']);
		}
		if(is_array($form['info']['direito'])){
			FuncoesTableauxLPO::verificaPotencialPrioridade($form['info']['direito']);
		}
	}
	//Método que recebe um array fórmula e verifica se o conectivo é "e", "ou" ou "implica"
	//Sendo um desses, caso haja um "notParaTodo" ou "xist" aninhado, retorna true
	public static function verificaPotencialPrioridade2($form){
		$conectivosImportantes=array('e','ou','implica');
		$conectivosImportantes2=array('not_paraTodo','xist');
		if (!in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
			return false;
		}
		elseif (in_array($form['info']['conectivo']['operacao'], $conectivosImportantes)) {
			if (@in_array($form['info']['esquerdo']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
				return true;
			}
			if (@in_array($form['info']['direito']['info']['conectivo']['operacao'],$conectivosImportantes2)) {
				return true;
			}
		}
		if(is_array($form['info']['esquerdo'])){
			FuncoesTableauxLPO::verificaPotencialPrioridade2($form['info']['esquerdo']);
		}
		if(is_array($form['info']['direito'])){
			FuncoesTableauxLPO::verificaPotencialPrioridade2($form['info']['direito']);
		}
	}

	public static function converteFormulaStringTableaux(&$form){
		$contador=0;
		if (@strlen($form['info'])==1) {
			$form="(".$form.")";
		}
		while (@is_array($form['info']['esquerdo']) || @is_array($form['info']['direito']) || is_array($form)) {
			FuncoesTableauxLPO::reverteFormatacaoTableaux($form,$contador);
		}
	}
	public static function consertaStringFormula(&$form){
		FuncoesAuxiliares::converteConectivoSimbolo($form);
		$contador=0;
		$abertoUmaVez=false;
		$listaConectivos=array("^","v","-","!",'@','&');
		for ($i=0; $i <strlen($form) ; $i++) { 
			if ($form[$i]=='(') {
				$abertoUmaVez=true;
				$contador++;
			}
			elseif ($form[$i]==')') {
				$contador--;
			}
			while($i==strlen($form)-1 && $contador>0){
				$form=substr($form, 1);
			}
			while($i==strlen($form)-1 && $contador<0){
				$form=substr($form, 0, strlen($form)-1);
			}
		}
		$aux=$form;
		flag:
		$aux=substr($aux, 1);
		$aux=substr($aux, 0, strlen($aux)-1);
		if ($aux[0]!='(' && $aux[0]!='!') {
			goto fim;
		}
		else{
			if ($aux[0]=='(') {
				$form=$aux;
				goto flag;
			}
		}
		fim:
		FuncoesAuxiliares::converteConectivoExtenso($form);
	}
	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e trabalha recursivamente
	//com colocaParenteses para transforma-lo em string
	//Deve ser usada para resolver os casos em que há arrays aninhados
	public static function reverteFormatacaoTableaux(&$form){
		if (@is_array($form['esquerdo'])) {
			FuncoesTableauxLPO::reverteFormatacaoTableaux($form['esquerdo']);
		}
		elseif (@!is_array($form['esquerdo']) ) {
			FuncoesTableauxLPO::colocaParentesesTableaux($form);
		}
		if (@is_array($form['direito'])) {
			FuncoesTableauxLPO::reverteFormatacaoTableaux($form['direito']);
		}
		elseif (@!is_array($form['direito']) ) {
			FuncoesTableauxLPO::colocaParentesesTableaux($form);
		}
	}

	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e transforma em string
	public static function colocaParentesesTableaux(&$form){
		if ((@is_array($form['info']['esquerdo']) || @is_array($form['esquerdo'])) && !(@is_array($form['direito']) || @is_array($form['info']['direito']))) {
			//print "<br>Entrei no Caso 1<br>";
			//print_r($form);
			if (@$form['conectivo']['operacao']=='not' || @$form['info']['conectivo']['operacao']=='not') {
				if (FuncoesTableauxLPO::checaAtomicoLPO($form) || FuncoesTableauxLPO::checaAtomicoLPO($form['info'])) {
					if (@$form['info']) {
						$aux=$form['info']['conectivo']['operacao'];
						$aux=$aux."(".$form['info']['direito'].")";
					}
					else{
						$aux=$form['conectivo']['operacao'];
						$aux=$aux."(".$form['direito'].")";
					}
					
				}
			}
			if (@$form['conectivo']['operacao']=='not_ou' || @$form['info']['conectivo']['operacao']=='not_ou') {
				if(@$form['info']){
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."ou";
					$aux=$aux.$form['info']['direito']."))";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."ou";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				
				return;
			}
			if (@$form['conectivo']['operacao']=='not_e' || @$form['info']['conectivo']['operacao']=='not_e') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."e";
					$aux=$aux.$form['info']['direito']."))";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."e";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				return;
			}
			if (@$form['conectivo']['operacao']=='not_implica' || @$form['info']['conectivo']['operacao']=='not_implica') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."implica";
					$aux=$aux.$form['info']['direito']."))";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."implica";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				return;
			}
			//Caso notnot
			if (@$form['conectivo']['operacao']=='notnot' || @$form['info']['conectivo']['operacao']=='notnot') {
				if (@$form['info']) {
					$form['info']['esquerdo']="notnot(".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito']."))";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="notnot(".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				return;
			}
			//Caso paraTodo
			if (@$form['conectivo']['operacao']=='paraTodo' || @$form['info']['conectivo']['operacao']=='paraTodo') {
				if (@$form['info']) {
					$form['info']['esquerdo']="paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="paraTodo".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso xist
			if (@$form['conectivo']['operacao']=='xist' || @$form['info']['conectivo']['operacao']=='xist') {
				if (@$form['info']) {
					$form['info']['esquerdo']="xist".$form['info']['esquerdo'];
					//$aux=$aux."xist";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="xist".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso not_paraTodo
			if (@$form['conectivo']['operacao']=='not_paraTodo' || @$form['info']['conectivo']['operacao']=='not_paraTodo') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not_paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not_paraTodo".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso not_xist
			if (@$form['conectivo']['operacao']=='not_xist' || @$form['info']['conectivo']['operacao']=='not_xist') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not_xist".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not_xist".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			if (@$form['info']) {
				if (is_array($form['info']['esquerdo'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
				}
				
				if (is_array($form['info']['direito'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
				}
				//$aux=$form['info']['conectivo']['operacao'];
				$aux=$aux.$form['info']['direito'].")";
				$form['info']['direito']=$aux;
			}
			else{
				if (is_array($form['info']['esquerdo'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
				}
				if (is_array($form['info']['direito'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
				}
				//$aux=$form['conectivo']['operacao'];
				$aux=$aux.$form['direito'].")";
				$form['direito']=$aux;
			}
			return;
		}
		elseif (!(@is_array($form['esquerdo']) || @is_array($form['info']['esquerdo'])) && ((@is_array($form['direito'])) || @is_array($form['info']['direito']))) {
			//$conectivos=array("not","notnot","paraTodo","not_paraTodo","xist","not_xist");
			//print "<br>Entrei no Caso 2<br>";
			//print_r($form);
			$aux=null;
			if (@$form['conectivo']['operacao']=='not' || @$form['info']['conectivo']['operacao']=='not') {
				if (FuncoesTableauxLPO::checaAtomicoLPO($form) || FuncoesTableauxLPO::checaAtomicoLPO($form['info'])) {
					if (@$form['info']) {
						$aux=$form['info']['conectivo']['operacao'];
						$aux=$aux."(".$form['info']['direito'].")";
					}
					else{
						$aux=$form['conectivo']['operacao'];
						$aux=$aux."(".$form['direito'].")";
					}
					
				}
			}
			if (@$form['conectivo']['operacao']=='not_ou' || @$form['info']['conectivo']['operacao']=='not_ou') {
				if(@$form['info']){
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
					}
					$aux="not(".$form['info']['esquerdo'];
					$aux=$aux."ou";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						$aux=$aux.$form['info']['direito'].")";
					}
					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."ou";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						$aux=$aux.$form['direito'].")";
					}
					$form=$aux;
				}
				
				return;
			}
			if (@$form['conectivo']['operacao']=='not_e' || @$form['info']['conectivo']['operacao']=='not_e') {
				if (@$form['info']) {
					while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
					}
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
					}
					$aux="not(".$form['info']['esquerdo'];
					$aux=$aux."e";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						$aux=$aux.$form['info']['direito'].")";
					}
					$form['info']=$aux;
				}
				else{
					while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
					}
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."e";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						$aux=$aux.$form['direito'].")";
					}
					$form=$aux;
				}
				return;
			}
			if (@$form['conectivo']['operacao']=='not_implica' || @$form['info']['conectivo']['operacao']=='not_implica') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['direito']);
					}
					$form['info']['direito']="not(".$form['info']['direito'];
					$aux=$aux."implica";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						$aux=$aux.$form['info']['direito'].")";
					}
					$form['info']=$aux;
				}
				else{
				while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."implica";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						$aux=$aux.$form['direito'].")";
					}
					$form=$aux;
				}
				return;
			}
			//Caso notnot
			if (@$form['conectivo']['operacao']=='notnot' || @$form['info']['conectivo']['operacao']=='notnot') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['direito']);
					}

					$form['info']['direito']="notnot(".$form['info']['direito'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito']."))";
					//$form['info']['esquerdo']="notnot(".$form['info']['esquerdo'];

					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['direito']);
					}
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true) {
						$form['direito']="notnot(".$form['direito'];
						$aux=$aux.$form['direito']."))";
						$form=$aux;
					}
					else{
						$form['direito']="notnot".$form['direito'];
						$aux=$aux.$form['direito'];
						$form=$aux;
					}
					//$form['esquerdo']="notnot(".$form['esquerdo'];
					//$form=$aux;
				}
				return;
			}
			//Caso paraTodo
			if (@$form['conectivo']['operacao']=='paraTodo' || @$form['info']['conectivo']['operacao']=='paraTodo') {
				if (@$form['info']) {
					$form['info']['esquerdo']="paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="paraTodo".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso xist
			if (@$form['conectivo']['operacao']=='xist' || @$form['info']['conectivo']['operacao']=='xist') {
				if (@$form['info']) {
					$form['info']['esquerdo']="xist".$form['info']['esquerdo'];
					//$aux=$aux."xist";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="xist".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso not_paraTodo
			if (@$form['conectivo']['operacao']=='not_paraTodo' || @$form['info']['conectivo']['operacao']=='not_paraTodo') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not_paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not_paraTodo".$form['esquerdo'];
					//$aux=$aux."implica";
					//print "<br>Teste<br>";
					//print_r($form);
					//dd(1);
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso not_xist
			if (@$form['conectivo']['operacao']=='not_xist' || @$form['info']['conectivo']['operacao']=='not_xist') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not_xist".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					$form['esquerdo']="not_xist".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			if (@$form['info']) {
				$aux=$form['info']['conectivo']['operacao'];
				$aux=$aux.$form['info']['direito'].")";
				$form['info']['direito']=$aux;
			}
			else{
				$aux=$form['conectivo']['operacao'];
				$aux=$aux.$form['direito'].")";
				$form['direito']=$aux;
			}
			return;

			if (@$form['info']) {
				if (strlen($form['info']['esquerdo'])==1) {
					//$aux="(";
				}
				elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
					$aux="(";
				}
				else{
					//$aux="(";
				}
				$aux=$aux.$form['info']['esquerdo'];
				$form['info']['esquerdo']=$aux;
			}
			else{
				//$aux="(";
				if (strlen($form['esquerdo'])==1) {
					//$aux="(";
				}
				elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
					$aux="(";
				}
				else{
					//$aux="not";
				}
				$aux=$aux.$form['esquerdo'];
				$form['esquerdo']=$aux;
			}
			if (@is_array($form['direito'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['direito']);
			}
			if (@is_array($form['info']['direito'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
			}	
			//print "<br><br>DEBUG -- ENTRADA NO direito é array<br><br>";
			//print_r($form);
			//dd('1');			
			//return;
		}
		elseif(is_array($form) || @is_array($form['info'])){
			//print "<br>Entrei no Caso 3<br>";
			//print_r($form);

			if (@$form['conectivo']['operacao']=='not_ou' || @$form['info']['conectivo']['operacao']=='not_ou') {
				
				if(@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not(";
						}
						else{
							$aux="not";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="not";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not(";
						}
						else{
							$aux="not";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}
					
				}
				else{
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not(".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['info']['direito']."))";
						$form['info']=$aux;

					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not(".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['direito']."))";
						$form=$aux;

					}
				}
				//$form=$aux;
				return;
			}
			if (@$form['conectivo']['operacao']=='not_e' || @$form['info']['conectivo']['operacao']=='not_e') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not(";
						}
						else{
							$aux="not";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="not";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not(";
						}
						else{
							$aux="not";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			if (@$form['conectivo']=='not_implica' || @$form['info']['conectivo']=='not_implica') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if ($form['info']['esquerdo'][0]=="(") {
							while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
								FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
							}
							while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
								FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
							}
							$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
							if (strlen($form['info']['esquerdo'])==1) {
								$aux="not";
							}
							elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
								$aux="not(";
							}
							else{
								$aux="not";
							}
							$aux=$aux.$form['info']['esquerdo'];
							$aux=$aux."implica";
							$aux=$aux.$form['info']['direito'].")";
							$form['info']=$aux;
						}
					}
					else{
						if ($form['esquerdo'][0]=="(") {
							while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
								FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
							}
							while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
								FuncoesTableaux::colocaParentesesTableaux($form['direito']);
							}
							$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
							if (strlen($form['esquerdo'])==1) {
								$aux="not";
							}
							elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
								$aux="not(";
							}
							else{
								$aux="not";
							}
							$aux=$aux.$form['esquerdo'];
							$aux=$aux."implica";
							$aux=$aux.$form['direito'].")";
							$form=$aux;
						}
					}
				}
				else{
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."implica";
						$aux=$aux.$form['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="not((";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."implica";
						$aux=$aux.$form['direito'].")";
						$form=$aux;		
					}					
				}			
				//$form=$aux;
				return;
			}
			//notnot
			if (@$form['conectivo']['operacao']=='notnot' || @$form['info']['conectivo']['operacao']=='notnot') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="notnot";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="notnot(";
						}
						else{
							$aux="notnot";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="notnot";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="notnot(";
						}
						else{
							$aux="notnot";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="notnot(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="notnot((";
						}
						else{
							$aux="notnot(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesTableaux::colocaParentesesTableaux($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if (strlen($form['esquerdo'])==1) {
							$aux="notnot(";
						}
						elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
							$aux="notnot((";
						}
						else{
							$aux="notnot(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			//Caso em subfórmulas com paraTodo
			if (@$form['conectivo']['operacao']=='paraTodo' || @$form['info']['conectivo']['operacao']=='paraTodo') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			//Caso em subfórmulas com xist
			if (@$form['conectivo']['operacao']=='xist' || @$form['info']['conectivo']['operacao']=='xist') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			//Caso em subfórmulas com not_paraTodo
			if (@$form['conectivo']['operacao']=='not_paraTodo' || @$form['info']['conectivo']['operacao']=='not_paraTodo') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			//Caso em subfórmulas com not_xist
			if (@$form['conectivo']['operacao']=='not_xist' || @$form['info']['conectivo']['operacao']=='not_xist') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			$aux=null;
			//Caso de subfórmula com not
			if (@$form['conectivo']['operacao']=='not' || @$form['info']['conectivo']['operacao']=='not') {
				if (FuncoesTableauxLPO::checaAtomicoLPO($form) || FuncoesTableauxLPO::checaAtomicoLPO($form['info'])) {
					if (@$form['info']) {
						$aux=$aux.$form['info']['conectivo']['operacao'];
						$aux=$aux."(".$form['info']['direito'].")";
						$form['info']=$aux;
					}
					else{
						$aux=$aux.$form['conectivo']['operacao'];
						$aux=$aux."(".$form['direito'].")";
						$form=$aux;
					}
					return;
				}
			}
			$aux=null;
			//Caso a subfórmula seja átomo sem not
			if (@$form['conectivo']['operacao']==null || @$form['info']['conectivo']['operacao']==null) {
				if (FuncoesTableauxLPO::checaAtomicoLPO($form) || FuncoesTableauxLPO::checaAtomicoLPO($form['info'])) {
					if (@$form['info']['direito'][0]=='(' || @$form['direito'][0]=='(') {
						return;
					}
					if (@$form['info']) {
						//$aux=$aux.$form['info']['conectivo']['operacao'];
						$aux="(".$form['info']['direito'].")";
						$form['info']=$aux;
					}
					elseif(@$form){
						//$aux=$aux.$form['conectivo']['operacao'];
						$aux="(".$form['direito'].")";
						$form=$aux;
					}
					return;
				}
			}
			if (@$form['info']) {
				while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
				}
				while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['info']['direito']);
				}
				$aux=$aux.$form['info']['conectivo']['operacao'];
				$aux=$aux.$form['info']['direito'].")";
			}
			else{
				while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
				}
				while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
					FuncoesTableaux::colocaParentesesTableaux($form['direito']);
				}
				$aux=$aux.$form['conectivo']['operacao'];
				$aux=$aux.$form['direito'].")";
			}
			//Ajustes
			$aux="(";
			if (@$form['info']) {
				while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['info']['esquerdo']);
				}
				$aux=$aux.$form['info']['esquerdo'];
				$form['info']=$aux;
			}
			else{
				while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
						FuncoesTableaux::colocaParentesesTableaux($form['esquerdo']);
				}
				$aux=$aux.$form['esquerdo'];
				$form=$aux;
			}
			//$form=$aux;
			return;
		}
	}
}


