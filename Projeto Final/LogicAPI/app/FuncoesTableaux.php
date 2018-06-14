<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesAuxiliares;

class FuncoesTableaux extends Model
{
    //Versão que retorna um array fórmula deixando os campos específicos do Tableaux "declarados"
	public static function resolveParentesesTableaux($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm['info']=array('esquerdo' => null, 'conectivo'=> null, 'direito'=>null);
		$auxForm['atualEsquerdo']=false;
		$auxForm['atualDireito']=false;
		$auxForm['atualCentral']=false;
		$auxForm['filhoEsquerdo']=null;
		$auxForm['filhoCentral']=null;
		$auxForm['filhoDireito']=null;
		$auxForm['pai']=null;
		$auxForm['formDisponiveis']=array();
		$auxForm['hashAtomos']=array();
		$aux;
		$esquerdo=true;
		$abreFormula=false;
		$contador=0;
		$not=false;

		FuncoesAuxiliares::converteConectivoSimbolo($form);
		////print "<br> Teste".$form;
		//Se for um átomo positivo
		//OBS: Talvez haja uma maneira mais apropriada de tratar isto
		//Em caso de erro nos cálculos, checar esta etapa
		//Número 3 é porque há dois parênteses e o átomo SEMPRE, por exemplo: (A)

		if(strlen($form)==3){
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			return $auxForm;
		}
		//Se for um átomo negativo
		//OBS: Talvez haja uma maneira mais apropriada de tratar isto
		//Em caso de erro nos cálculos, checar esta etapa
		//Número 4 é porque há dois parênteses e o átomo com negativo SEMPRE, por exemplo: (!A)

		if(strlen($form)==4){
			$form=substr($form, 1);		
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']='not';
			return $auxForm;
		}

		//Se não for átomo, caso mais geral
		for ($i=0; $i<strlen($form); $i++){
			//Caso notnotnot
			if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!' && ($i==0 || $i==1)){
				//Correções específicas para o caso em que notnot está entre parênteses
				if ($form[0]=='(') {
					$form=substr($form, 1);
					$form=substr($form, 0, strlen($form)-1);
				}

				if ($auxForm['info']['esquerdo']=='(') {
					$auxForm['info']['esquerdo']=NULL;
				}

				$form=substr($form, 4);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['direito']=$form;
				$auxForm['info']['conectivo']='not';
				return $auxForm;
			}
			//Caso notnot
			if($form[$i]=='!' && $form[$i+1]=='!' && ($i==0 || $i==1)){
				//Correções específicas para o caso em que notnot está entre parênteses
				if ($form[0]=='(') {
					$form=substr($form, 1);
					$form=substr($form, 0, strlen($form)-1);
				}

				if ($auxForm['info']['esquerdo']=='(') {
					$auxForm['info']['esquerdo']=NULL;
				}

				$form=substr($form, 3);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['direito']=$form;
				if ($auxForm['info']['direito'][0]!='(') {
					$auxForm['info']['direito']="(".$auxForm['info']['direito'].")";
				}
				$auxForm['info']['conectivo']='notnot';
				return $auxForm;
			}
			

			//Se achar o conectivo not no exterior de um parentese
			//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
			//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
			//é a abertura de um parenteses
			if($form[$i]=='!' && $form[$i+1]=='(' && $abreFormula==false){
				//Se for um átomo, não sinaliza a flag not
				if ($form[$i+3]==')') {
				//faça nada			
							
				}
				elseif ($abreFormula==false && $contador==0) {
					$not=true;
				}
			}
			if($form[$i]=='('){
				$abreFormula=true;
				$contador++;
				
			}
			if($form[$i]==')'){
				$contador-=1;
				if($contador==0){
					$abreFormula=False;
				}
				
			}
			if($abreFormula==true){
				if((in_array($form[$i],$listaConectivos)) && ($contador==1) && $form[$i]!='!'){
					if($not==true){
						$aux=$form[$i];
						FuncoesAuxiliares::converteConectivoNot($aux);
						$auxForm['info']['conectivo']=$aux;
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						FuncoesAuxiliares::converteConectivoExtenso($aux);
						$auxForm['info']['conectivo']=$aux;
						$esquerdo=false;
					}
					
				}
				if($esquerdo==true){
					$auxForm['info']['esquerdo']=$auxForm['info']['esquerdo'].$form[$i];
				}
				if($esquerdo==false){
					$auxForm['info']['direito']=$auxForm['info']['direito'].$form[$i];
				}
			}
		}
		$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
		$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
		//Correções de parênteses excedentes antes de retornar a fórmula
		//Caso 1 - Átomo positivo
		
		if (strlen($auxForm['info']['esquerdo'])==3 && @$auxForm['info']['esquerdo'][0]=='(' ) {
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
		}
		if (strlen($auxForm['info']['direito'])==3 && @$auxForm['info']['direito'][0]=='(' ) {
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
		}
		//Caso 2 - Átomo negativo
		
		if (strlen($auxForm['info']['esquerdo'])==6 && @$auxForm['info']['esquerdo'][0]=='(' &&  @$auxForm['info']['esquerdo'][1]=='!' ) {
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
			$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
		}
		if (strlen($auxForm['info']['direito'])==6 && @$auxForm['info']['direito'][0]=='(' &&  @$auxForm['info']['direito'][1]=='!') {
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
			$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
		}
		
		$auxiliar=$auxForm['info']['esquerdo'];
		$conectivo=false;
		$contador=0;
		for ($i=0; $i<strlen($auxiliar); $i++){
			if($auxiliar[$i]=='('){
				$abreFormula=true;
				$contador++;
				
			}
			if($auxiliar[$i]==')'){
				$contador-=1;
				if($contador==0){
					$abreFormula=False;
				}
				
			}
			if($abreFormula==true){
				if((in_array($auxiliar[$i],$listaConectivos)) && ($contador==1) && $auxiliar[$i]!='!'){
					$conectivo=true;
					
				}
			}
		}
		if (!$conectivo) {
			if (@$auxForm['info']['esquerdo'][0]=='(' && @$auxForm['info']['esquerdo'][strlen($auxForm['info']['esquerdo'])-1]==')') {
				$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 1);
				$auxForm['info']['esquerdo']=substr($auxForm['info']['esquerdo'], 0, strlen($auxForm['info']['esquerdo'])-1);
			}
		}
		$contador=0;
		$auxiliar=$auxForm['info']['direito'];
		$conectivo=false;
		for ($i=0; $i<strlen($auxiliar); $i++){
			if($auxiliar[$i]=='('){
				$abreFormula=true;
				$contador++;
				
			}
			if($auxiliar[$i]==')'){
				$contador-=1;
				if($contador==0){
					$abreFormula=False;
				}
				
			}
			if($abreFormula==true){
				if((in_array($auxiliar[$i],$listaConectivos)) && ($contador==1) && $auxiliar[$i]!='!'){
					$conectivo=true;
					
				}
			}
		}
		if (!$conectivo) {
			if (@$auxForm['info']['direito'][0]=='(' && @$auxForm['info']['direito'][strlen($auxForm['info']['direito'])-1]==')') {
				$auxForm['info']['direito']=substr($auxForm['info']['direito'], 1);
				$auxForm['info']['direito']=substr($auxForm['info']['direito'], 0, strlen($auxForm['info']['direito'])-1);
			}
		}
		return $auxForm;
	}
	public static function escolhaEficiente(&$listaFormulasDisponiveis,&$hashInicial,&$nosFolha,&$historicoVariaveis,&$raiz,&$contador){
		//Verificação para saber se a função foi chamada mesmo
		//que todas os ramos já estejam fechados
		if (FuncoesTableaux::todasFechadas($nosFolha,$contador)) {
			//print "<br>Todos os ramos já estão fechados<br>";
			//print $contador."<br>";
			return "fechado";
		}
		
		$conectivosEficientes=array("e","not_ou","not_implica","notnot");

		foreach ($listaFormulasDisponiveis as $key => $value) {
			FuncoesTableaux::formataFormulasTableaux($listaFormulasDisponiveis[$key]);
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
					FuncoesTableaux::aplicaRegra($raiz,$raiz,$nosFolha,$contador);
					FuncoesTableaux::removerFormula($listaFormulasDisponiveis,$raiz['info']);
					FuncoesTableaux::armazenaHistorico($historicoVariaveis,$nosFolha,$raiz,$contador+1,$listaFormulasDisponiveis);
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
			FuncoesTableaux::aplicaRegra($raiz,$raiz,$nosFolha,$contador);
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
	public static function criaFormulaTableaux(){
		$auxForm['info']=array('esquerdo' => null, 'conectivo'=> null, 'direito'=>null);
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
	public static function aplicaRegra(&$form,&$pai,&$nosFolha,&$contador){
		//Inicializando variáveis auxiliares com suas respectivas estruturas de dados
		$noAuxCen1=FuncoesTableaux::criaFormulaTableaux();
		$noAuxCen1['atualCentral']=true;
		$noAuxCen2=FuncoesTableaux::criaFormulaTableaux();
		$noAuxCen1['filhoCentral']=&$noAuxCen2;
		$noAuxCen2['pai']=&$noAuxCen1;
		$noAuxCen2['atualCentral']=true;
		$noAuxEsq=FuncoesTableaux::criaFormulaTableaux();
		$noAuxEsq['atualEsquerdo']=true;
		$noAuxDir=FuncoesTableaux::criaFormulaTableaux();
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
		switch ($form['info']['conectivo']) {
			//Regra 1
			case 'e':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxCen1);			
				FuncoesTableaux::corrigeArrays($noAuxCen2);
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				////print "<br>noAuxCen1<br>";
				////print_r($noAuxCen1);
				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}

				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen2);
				}
	
				return;

			//Regra 2
			case 'ou':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxEsq);			
				FuncoesTableaux::corrigeArrays($noAuxDir);

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				FuncoesTableaux::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxDir['formDisponiveis'],$form['info']);	
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxDir);
				}	
				return;		
			case 'implica':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxEsq);			
				FuncoesTableaux::corrigeArrays($noAuxDir);
				

				//Manipulação específica da implicação
				//O lado esquerdo deve passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableaux::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxEsq['info']);
				}

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
						//print "<br>FECHADO<br>";
						//print_r($noAuxEsq['info']);
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
						//print "<br>FECHADO<br>";
						//print_r($noAuxDir['info']);
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}

				FuncoesTableaux::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxDir['formDisponiveis'],$form['info']);
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxDir);
				}	
				
				
				return;		
			//Regra 4
			case 'notnot':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
				}
				
				$pai['filhoCentral']=&$noAuxCen1;
				$noAuxCen1['pai']=&$pai;
				//Inicialização das variáveis auxiliares
				$noAuxCen1['info']['direito']=$form['info']['direito'];
				//Inicialização dos dados que são compartilhados com o pai
				$noAuxCen1['formDisponiveis']=$pai['formDisponiveis'];
				$noAuxCen1['hashAtomos']=$pai['hashAtomos'];

				//Correções na estrutura de dados
				
				
				FuncoesTableaux::corrigeArrays($noAuxCen1);
				

				//Manipulação específica de notnot
				

				//Se a fórmula for átomo
				if (FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					if ($noAuxCen1['info']['conectivo']=='notnot') {
						//Equivalente a notnot
						$noAuxCen1['info']['conectivo']=null;
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

				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen1['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
				}		
				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				
				if (!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen1);
				}	
				
				return;
			
			case 'not_e':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxEsq);			
				FuncoesTableaux::corrigeArrays($noAuxDir);

				//Manipulação específica do not_e
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableaux::checaAtomico($noAuxEsq['info'])) {
					if ($noAuxEsq['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxEsq['info']['conectivo']=null;
					}
					else{
						$noAuxEsq['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxEsq['info']);
				}

				//Se o lado direito for átomo
				if (FuncoesTableaux::checaAtomico($noAuxDir['info'])) {
					if ($noAuxDir['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxDir['info']['conectivo']=null;
					}
					else{
						$noAuxDir['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxDir['info']);
				}

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableaux::checaAtomico($noAuxEsq['info'])){
					if (FuncoesTableaux::casarFormula($noAuxEsq['hashAtomos'],$noAuxEsq['info'])) {
						$noAuxEsq['filhoCentral']='fechado';
					}
					$noAuxEsq['hashAtomos'][$noAuxEsq['info']['direito']]=$noAuxEsq['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxEsq['info'])) {
					array_push($noAuxEsq['formDisponiveis'], $noAuxEsq);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxDir['info'])){
					if (FuncoesTableaux::casarFormula($noAuxDir['hashAtomos'],$noAuxDir['info'])) {
						$noAuxDir['filhoCentral']='fechado';
					}
					$noAuxDir['hashAtomos'][$noAuxDir['info']['direito']]=$noAuxDir['info']['conectivo'] == 'not' ? '0':'1';	
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxDir['info'])) {
					array_push($noAuxDir['formDisponiveis'], $noAuxDir);
				}
				FuncoesTableaux::removerFormula($noAuxEsq['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxDir['formDisponiveis'],$form['info']);	
				if (@$noAuxEsq['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxEsq);
				}
				if (@$noAuxDir['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxDir);
				}	
				return;	
			
			case 'not_ou':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxCen1);			
				FuncoesTableaux::corrigeArrays($noAuxCen2);


				//Manipulação específica do not_ou
				//Os dois lados devem passar a ter um not externamente

				//Se o lado esquerdo for átomo
				if (FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					if ($noAuxCen1['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen1['info']['conectivo']=null;
					}
					else{
						$noAuxCen1['info']['conectivo']='not';
					}
				}
				//Se o lado esquerdo for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxCen1['info']);
				}

				//Se o lado direito for átomo
				if (FuncoesTableaux::checaAtomico($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxCen2['info']);
				}
				


				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}


				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen2);
				}
				return;
			
			case 'not_implica':
				//Com exceção da raiz, todo o no que é pai neste momento, deixará de ser nó folha.
				//Relembrando, consideramos que a raiz é pai dela mesma para a aplicação inicial.
				if ($contador!=0) {
					FuncoesTableaux::removerFormula($nosFolha,$pai['info']);
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
				
				FuncoesTableaux::corrigeArrays($noAuxCen1);			
				FuncoesTableaux::corrigeArrays($noAuxCen2);

				//Manipulação específica do not_implica
				//O lado direito deve passar a ter um not externamente


				//Se o lado direito for átomo
				if (FuncoesTableaux::checaAtomico($noAuxCen2['info'])) {
					if ($noAuxCen2['info']['conectivo']=='not') {
						//Equivalente a notnot
						$noAuxCen2['info']['conectivo']=null;
					}
					else{
						$noAuxCen2['info']['conectivo']='not';
					}
				}
				//Se o lado direito for array
				elseif (!FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					FuncoesTableaux::negaArrayTableaux($noAuxCen2['info']);
				}
				

				//Se a fórmula for atômica eu adiciono átomo gerado na hash da mesma
				//Se não for átomo, então é uma fórmula e adiciono a fórmula gerada na lista de fórmulas desse elemento
				//Como noAuxCen1 e noAuxCen2 estão no mesmo ramo, estes devem compartilhar as informações
				if(FuncoesTableaux::checaAtomico($noAuxCen1['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen1['hashAtomos'],$noAuxCen1['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen1['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen2['hashAtomos'][$noAuxCen1['info']['direito']]=$noAuxCen1['info']['conectivo'] == 'not' ? '0':'1';		
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen1['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen1);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen1);
				}			
				
				if(FuncoesTableaux::checaAtomico($noAuxCen2['info'])){
					if (FuncoesTableaux::casarFormula($noAuxCen2['hashAtomos'],$noAuxCen2['info'])) {
						$noAuxCen2['filhoCentral']='fechado';
					}
					$noAuxCen2['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';
					$noAuxCen1['hashAtomos'][$noAuxCen2['info']['direito']]=$noAuxCen2['info']['conectivo'] == 'not' ? '0':'1';				
				}
				elseif(!FuncoesTableaux::checaAtomico($noAuxCen2['info'])) {
					array_push($noAuxCen1['formDisponiveis'], $noAuxCen2);
					array_push($noAuxCen2['formDisponiveis'], $noAuxCen2);
				}

				FuncoesTableaux::removerFormula($noAuxCen1['formDisponiveis'],$form['info']);
				FuncoesTableaux::removerFormula($noAuxCen2['formDisponiveis'],$form['info']);
					
				if (@$noAuxCen2['filhoCentral']!='fechado') {
						FuncoesTableaux::adicionaArray($nosFolha, $noAuxCen2);
				}

				return;
			//Caso extra
			case 'not':
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableaux::checaAtomico($pai['info'])){
					if(FuncoesTableaux::casarFormula($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo'] == 'not' ? '0':'1';	
				}

				
				FuncoesTableaux::removerFormula($pai['formDisponiveis'],$form['info']);

				return;
			case null:
				//Se for atômico devemos adicionar na hash e verificar se casa com alguma fórmula
				if(FuncoesTableaux::checaAtomico($pai['info'])){
					if(FuncoesTableaux::casarFormula($pai['hashAtomos'],$pai['info'])){
						$pai['filhoCentral']='fechado';
					}
					$pai['hashAtomos'][$pai['info']['direito']]=$pai['info']['conectivo'] == 'not' ? '0':'1';	
				}
				
				FuncoesTableaux::removerFormula($pai['formDisponiveis'],$form['info']);
				
				return;
			default:
				//print_r($form['hashAtomos']);
				break;
		}
	}
	public static function checaAtomico($form){
		//@ colocado para previnir que fórmulas não instanciadas deem warning
		if (@$form['esquerdo']==NULL && (@$form['conectivo']==NULL || @$form['conectivo']=='not')) {
			return true;
		}
		else{
			return false;
		}

		
	}

	//Se houver digitação incorreta gera uma exceção (trabalhar na exceção depois)
	public static function negaPergunta($listaFormulas,$tamanho){
		//Nega a pergunta
		$listaFormulas[$tamanho-1]="not".$listaFormulas[$tamanho-1];
		//Tratar a entrada, verificação de digitação correta
		foreach ($listaFormulas as $key => $value) {
			FuncoesAuxiliares::verificaFormulaCorreta($listaFormulas[$key]);
			$entradaConvertida[$key]=FuncoesTableaux::resolveParentesesTableaux($listaFormulas[$key]);
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
	public static function imprimeArvore(&$no,&$resultado=NULL){
		if (@$no['info']!=NULL) {
			FuncoesTableaux::converteFormulaStringTableaux($no['info']);
			// FuncoesTableaux::consertaStringFormula($no['info']);
			 print "<br>";
			 print_r($no['info']);
			FuncoesTableaux::verificaStatusNo($no,$resultado);
		}
		
		if(@$no['filhoCentral']!=NULL && @$no['filhoCentral']!='fechado'){
			@FuncoesTableaux::imprimeArvore(@$no['filhoCentral'],$resultado);
		}
		if(@$no['filhoEsquerdo'] && @$no['filhoEsquerdo']!='fechado'){
			@FuncoesTableaux::imprimeArvore(@$no['filhoEsquerdo'],$resultado);
		}
		if(@$no['filhoDireito'] && @$no['filhoDireito']!='fechado'){
			@FuncoesTableaux::imprimeArvore(@$no['filhoDireito'],$resultado);
		}
	}

	//Função utilizada somente por imprimArvore para ajustaro formato da impressão
	public static function verificaStatusNo(&$no,&$resultado){
		switch($no){
			case @$no['atualCentral']:
				print "  Central <br>";
				$resultado[]['central'] = $no['info'];
				break;
			case @$no['atualEsquerdo']:
				print "  Esquerdo <br>";
				$resultado[]['esquerda'] = $no['info'];
				break;
			case @$no['atualDireito']:
				print "  Direito <br>";
				$resultado[]['direita'] = $no['info'];
				break;
			default:
				if(@$no['info']=="fechado"){
					break;
				}
				else{
					$resultado[]['raiz'] = $no['info'];
				}
				//print "<br>Nó não categorizado<br>";
		}
	}
	public static function imprimeArvore2(&$no){
		if (@$no['info']!=NULL) {
			//FuncoesTableaux::converteFormulaStringTableaux($no['info']);
			 print "<br>";
			 print_r($no['info']);
			FuncoesTableaux::verificaStatusNo2($no);
		}
		if (@$no['filhoCentral']=='fechado'){
			print "<br>Fechado<br>";
		}
		
		if(@$no['filhoCentral']!=NULL && @$no['filhoCentral']!='fechado'){
			@FuncoesTableaux::imprimeArvore2(@$no['filhoCentral']);
		}
		if(@$no['filhoEsquerdo'] && @$no['filhoEsquerdo']!='fechado'){
			//print "<br>Problema<br>";
			//print_r($no['filhoEsquerdo']['info']);
			@FuncoesTableaux::imprimeArvore2(@$no['filhoEsquerdo']);
		}
		if(@$no['filhoDireito'] && @$no['filhoDireito']!='fechado'){
			//print "<br>Problema2<br>";
			//print_r($no['filhoEsquerdo']['info']);
			@FuncoesTableaux::imprimeArvore2(@$no['filhoDireito']);
		}
	}

	//Função utilizada somente por imprimArvore para ajustaro formato da impressão
	public static function verificaStatusNo2(&$no){
		switch($no){
			case @$no['atualCentral']:
				// print "  Central <br>";
				$resultado[]['central'] = $no['info'];
				break;
			case @$no['atualEsquerdo']:
				// print "  Esquerdo <br>";
				$resultado[]['esquerda'] = $no['info'];
				break;
			case @$no['atualDireito']:
				// print "  Direito <br>";
				$resultado[]['direita'] = $no['info'];
				break;
			default:
				if(@$no['info']=="fechado"){
					break;
				}
				else{
					$resultado[]['raiz'] = $no['info'];
				}
				//print "<br>Nó não categorizado<br>";
		}
	}
	public static function outputArvore($resultado,$exercicio){
		
		$subgrupo = FuncoesTableaux::setSubGrupos($resultado);
		// dd($subgrupo);

		$grupo = FuncoesTableaux::setGrupo($subgrupo);
		// dd($grupo);

		$stringFinal = FuncoesTableaux::printEstrutura($grupo,$subgrupo,count($grupo));
		// dd($exercicio);
		for($i = count($exercicio) - 2; $i >=0 ; $i--){
			$stringFinal = "<ul><li>" . $exercicio[$i] .  $stringFinal . "</li></ul>";
		}
		return $stringFinal;
		
	}
	public static function printEstrutura($grupo,$subgrupo,$indice){
		// if($indice == 4){
		// 	dd($subgrupo[2]);
		// }
		if(isset($grupo[$indice]['filho'])){
			$filho = $grupo[$indice]['filho'];
			$countFilho = count($filho)==2 ? TRUE:FALSE;
		}
		else{
			$filho = null;
			$countFilho = FALSE;
		}
		unset($grupo[$indice]['filho']);
		$impressao = null;
		$impressao .= FuncoesTableaux::printAberturaSubArvore($grupo[$indice],$subgrupo,$countFilho);

		if(is_array($filho)){
			foreach ($filho as $key => $value) {
				$impressao .= FuncoesTableaux::printEstrutura($grupo,$subgrupo,$value);
			}
		}
		$impressao .= FuncoesTableaux::printFechamentoSubArvore($grupo[$indice],$subgrupo,$countFilho);
		// if($indice == 6){
		// 	dd($impressao);
		// }
		return $impressao;
		
	}

	public static function printAberturaSubArvore($grupo,$subgrupo,$doisFilhos=FALSE){
		$resposta = "";
		if($subgrupo[$grupo[0]]['node']['0'] == 'central'){
			
			foreach ($subgrupo[$grupo[0]]['string'] as $chave => $valor) {
				$resposta .= "<ul>";
				$resposta .= "<li>";
				$resposta .= $valor;
			}
			$resposta .= "<ul>";
		//Se o nó for esquerdo e estiver em um grupo com um nó direito
		}else if($subgrupo[$grupo[0]]['node']['0'] == 'esquerda' && isset($grupo[1]) ){
			$abertura = null;
			$fechamento = null;
			$resposta .= "<ul>";

			$abertura = "<li>";
			$abertura .= $subgrupo[$grupo[0]]['string'][0];
			$fechamento = "</li>";
			$print = false;

			foreach ($subgrupo[$grupo[0]]['string'] as $chave => $valor) {

				if(!$print){
					$print = true;
				}else{
					$abertura .= "<ul><li>";
					$abertura .= $valor;
					$fechamento = "</li></ul>" . $fechamento;
				}
			}
			$resposta .= $abertura;
			$resposta .= $fechamento;

			$abertura = "<li>";
			$abertura .= $subgrupo[$grupo[1]]['string'][0];
			$fechamento = "</li>";
			$print = false;
			foreach ($subgrupo[$grupo[1]]['string'] as $chave => $valor) {

				if(!$print){
					$print = true;
				}else{
					$abertura .= "<ul><li>";
					$abertura .= $valor;
					$fechamento = "</li></ul>" . $fechamento;
				}
			}
			$resposta .= $abertura;
			$resposta .= $fechamento;
			$resposta .= "</ul>";
		}else if($doisFilhos){
			$resposta .= "<li>";
			$resposta .= $subgrupo[$grupo[0]]['string']['0'];
			$print = false;
			foreach ($subgrupo[$grupo[0]]['string'] as $key => $value) {
				if(!$print){
					$print = true;
				}else{
					$resposta .= "<ul><li>";
					$resposta .= $value;
				}
			}
			$resposta .= "<ul>";

		}else if($subgrupo[$grupo[0]]['node']['0'] == 'direita' || $subgrupo[$grupo[0]]['node']['0'] == 'esquerda'){
			$resposta .= "<li>";
			$resposta .= $subgrupo[$grupo[0]]['string']['0'];
			$print = false;
			foreach ($subgrupo[$grupo[0]]['string'] as $key => $value) {
				if(!$print){
					$print = true;
				}else{
					$resposta .= "<ul><li>";
					$resposta .= $value;
				}
			}
		}
		

		return $resposta;
	}

	public static function printFechamentoSubArvore($grupo,$subgrupo,$doisFilhos=FALSE){
		$resposta = "";
		
		if($subgrupo[$grupo[0]]['node']['0'] == 'central'){
			$resposta .= "</ul>";
			foreach ($subgrupo[$grupo[0]]['string'] as $chave => $valor) {
				$resposta .= "</li>";
				$resposta .= "</ul>";
			}
		}else if($subgrupo[$grupo[0]]['node']['0'] == 'esquerda' && isset($grupo[1]) ){
			$resposta = "";
		}else if($subgrupo[$grupo[0]]['node']['0'] == 'esquerda' && $doisFilhos){
			$print = false;
			foreach ($subgrupo[$grupo[0]]['string'] as $key => $value) {
				if(!$print){
					$print = true;
				}else{
					$resposta .= "</li></ul>";
				}
			}
			$resposta .= "</ul></li>";
		}else if($subgrupo[$grupo[0]]['node']['0'] == 'direita' || $subgrupo[$grupo[0]]['node']['0'] == 'esquerda'){
			$print = false;
			foreach ($subgrupo[$grupo[0]]['string'] as $key => $value) {
				if(!$print){
					$print = true;
				}else{
					$resposta .= "</li></ul>";
				}
			}
			$resposta .= "</li>";
		}

		return $resposta;
	}
	private static function setSubGrupos($resultado){
		$subgrupo = NULL;
		$controle = 0;
		foreach ($resultado as $key => $value) {
			if(array_key_exists("central", $value)){
				$subgrupo[$controle]['string'][] = $value['central'];
				$subgrupo[$controle]['node'][] = 'central';
			}
			else if(array_key_exists("raiz", $value)){
				$subgrupo[$controle]['string'][] = $value['raiz'];
				$subgrupo[$controle]['node'][] = 'central';
			}
			else{
				$controle++;
				$subgrupo[$controle]['node'][] = key($value);
				$subgrupo[$controle]['string'][] = $value[key($value)];
			}
		}
		return $subgrupo;
	}

	public static function setGrupo(&$subgrupo){
		$controle = 1;
		for($i = count($subgrupo) - 1 ; $i >= 0 ; $i--) {
			if($subgrupo[$i]['node'][0] == "direita" && $subgrupo[$i-1]['node'][0] == "esquerda" && isset($subgrupo[$i+1]['node'][0]) && $subgrupo[$i+1]['node'][0] =="esquerda"){
				$grupo[$controle][] = $i;
				if(!array_key_exists("filho", $grupo[$controle])){
					$grupo[$controle]['filho'] = null;
				}
				$grupo[$controle+2]['filho'][] = $controle;
				$subgrupo[$i]['grupo'] = $controle;
				$controle++;
			}else if($subgrupo[$i]['node'][0] == "direita" && $subgrupo[$i-1]['node'][0] == "esquerda"){
				$grupo[$controle][] = $i-1;
				$grupo[$controle][] = $i;
				if(!array_key_exists("filho", $grupo[$controle])){
					$grupo[$controle]['filho'] = null;
				}
				$grupo[$controle+1]['filho'][] = $controle;
				$subgrupo[$i]['grupo'] = $controle;
				$subgrupo[$i-1]['grupo'] = $controle;
				$controle++;
				$i--;
			}
			else if($subgrupo[$i]['node'][0] == "direita" && $subgrupo[$i-1]['node'][0] == "direita"){
				$grupo[$controle][] = $i;
				$j = 2;
				$nextEsquerdo = 2;
				while (true) {
					
					if($subgrupo[$i-$j]['node'][0] == "direita")
						$nextEsquerdo++;
					else
						$nextEsquerdo--;

					if($nextEsquerdo == 0){
						$j++;
						if(array_key_exists('grupo', $subgrupo[$i-$j]))
							$grupo[$subgrupo[$i-$j]['grupo']]['filho'][1] = $controle;
						else
							$subgrupo[$i-$j]['filho'] = $controle;
						break;
					}
					$j++;
				}
				$controle++;
			}
			else if($subgrupo[$i]['node'][0] == "esquerda"){
				
				$grupo[$controle][] = $i;
				if(array_key_exists('filho', $subgrupo[$i])){
					$grupo[$controle]['filho'][1] = $subgrupo[$i]['filho'];
				}
				$controle++;
				if(isset($grupo[$controle]['filho'][0])){
					$grupo[$controle]['filho'][1] = $grupo[$controle]['filho'][0];
				}
				$grupo[$controle]['filho'][0] = $controle-1;
				
			}
			else if($subgrupo[$i]['node'][0] == "central"){
				$grupo[$controle][] = $i;
				if(array_key_exists('filho', $subgrupo[$i]))
					$grupo[$controle]['filho'][1] = $subgrupo[$i]['filho'];
			}
		}
		return $grupo;
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
	public static function negaArrayTableaux(&$form){
		if ($form['conectivo']=='e') {
			$form['conectivo']='not_e';
		}
		elseif ($form['conectivo']=='ou') {
			$form['conectivo']='not_ou';
		}
		elseif ($form['conectivo']=='implica') {
			$form['conectivo']='not_implica';
		}
		elseif ($form['conectivo']=='not_e') {
			$form['conectivo']='e';
		}
		elseif ($form['conectivo']=='not_ou') {
			$form['conectivo']='ou';
		}
		elseif ($form['conectivo']=='not_implica') {
			$form['conectivo']='implica';
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
	//Função que recebe uma referência para uma array fórmula e a converte num string fórmula
	//Esta é a função que deve ser chamada no código principal ao realizar a conversão
	public static function converteFormulaStringTableaux(&$form){
		if (@strlen($form['info'])==1) {
			$form="(".$form.")";
		}
		while (@is_array($form['info']['esquerdo']) || @is_array($form['info']['direito']) || is_array($form)) {
			FuncoesTableaux::reverteFormatacaoTableaux($form);
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
			FuncoesTableaux::reverteFormatacaoTableaux($form['esquerdo']);
		}
		elseif (@!is_array($form['esquerdo']) ) {
			FuncoesTableaux::colocaParentesesTableaux($form);
		}
		if (@is_array($form['direito'])) {
			FuncoesTableaux::reverteFormatacaoTableaux($form['direito']);
		}
		elseif (@!is_array($form['direito']) ) {
			FuncoesTableaux::colocaParentesesTableaux($form);
		}
	}

	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e transforma em string
	public static function colocaParentesesTableaux(&$form){
		if ((@is_array($form['info']['esquerdo']) || @is_array($form['esquerdo'])) && (@!is_array($form['direito']) || @!is_array($form['info']['direito']))) {
			if (@$form['conectivo']=='not' || @$form['info']['conectivo']=='not') {
				if (FuncoesResolucao::checaAtomico($form) || FuncoesTableaux::checaAtomico($form)) {
					if (@$form['info']) {
						$aux=$form['info']['conectivo'];
						$aux=$aux."(".$form['info']['direito']."))";
					}
					else{
						$aux=$form['conectivo'];
						$aux=$aux."(".$form['direito']."))";
					}
					
				}
			}
			if (@$form['conectivo']=='not_ou' || @$form['info']['conectivo']=='not_ou') {
				if(@$form['info']){
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."ou";
					$aux=$aux.$form['info']['direito']."))";
					$form=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."ou";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				
				return;
			}
			if (@$form['conectivo']=='not_e' || @$form['info']['conectivo']=='not_e') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."e";
					$aux=$aux.$form['info']['direito']."))";
					$form=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."e";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				return;
			}
			if (@$form['conectivo']=='not_implica' || @$form['info']['conectivo']=='not_implica') {
				if (@$form['info']) {
					$form['info']['esquerdo']="not(".$form['info']['esquerdo'];
					$aux=$aux."implica";
					$aux=$aux.$form['info']['direito']."))";
					$form=$aux;
				}
				else{
					$form['esquerdo']="not(".$form['esquerdo'];
					$aux=$aux."implica";
					$aux=$aux.$form['direito']."))";
					$form=$aux;
				}
				return;
			}
			if (@$form['info']) {
				$aux=$form['info']['conectivo'];
				$aux=$aux.$form['info']['direito'].")";
				$form['info']['direito']=$aux;
			}
			else{
				$aux=$form['conectivo'];
				$aux=$aux.$form['direito'].")";
				$form['direito']=$aux;
			}
			return;
		}
		elseif ((@!is_array($form['esquerdo']) || @!is_array($form['info']['esquerdo'])) && ((@is_array($form['direito'])) || @is_array($form['info']['direito']))) {
			if (@$form['info']) {
				$aux="(";
				$aux=$aux.$form['info']['esquerdo'];
				$form['info']['esquerdo']=$aux;
			}
			else{
				$aux="(";
				$aux=$aux.$form['esquerdo'];
				$form['esquerdo']=$aux;
			}			
			return;
		}
		elseif(is_array($form) || @is_array($form['info'])){

			if (@$form['conectivo']=='not_ou' || @$form['info']['conectivo']=='not_ou') {
				
				if(@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['info']['direito'].")";
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['direito'].")";
					}
					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."ou";
						$aux=$aux.$form['direito']."))";
					}
				}
				$form=$aux;
				return;
			}
			if (@$form['conectivo']=='not_e' || @$form['info']['conectivo']=='not_e') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not";
						}
						else{
							$aux="not(";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
					}					
				}
				else{
					if (@$form['info']) {
						if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['info']['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."e";
						$aux=$aux.$form['direito'].")";
					}					
				}
				$form=$aux;
				return;
			}
			if (@$form['conectivo']=='not_implica' || @$form['info']['conectivo']=='not_implica') {
				if (@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						if ($form['info']['esquerdo'][0]=="(") {
							if (strlen($form['info']['esquerdo'])==1) {
								$aux="not";
							}
							else{
								$aux="not(";
							}
							$aux=$aux.$form['info']['esquerdo'];
							$aux=$aux."implica";
							$aux=$aux.$form['info']['direito'].")";
						}
					}
					else{
						if ($form['esquerdo'][0]=="(") {
							if (strlen($form['esquerdo'])==1) {
								$aux="not";
							}
							else{
								$aux="not(";
							}
							$aux=$aux.$form['esquerdo'];
							$aux=$aux."implica";
							$aux=$aux.$form['direito'].")";
						}
					}
				}
				else{
					if (@$form['info']) {
							if (strlen($form['info']['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."implica";
						$aux=$aux.$form['direito'].")";
					}
					else{
						if (strlen($form['esquerdo'])==1) {
							$aux="not(";
						}
						else{
							$aux="not((";
						}
						$aux=$aux.$form['esquerdo'];
						$aux=$aux."implica";
						$aux=$aux.$form['direito'].")";		
					}					
				}			
				$form=$aux;
				return;
			}
			$aux="(";
			if (@$form['info']) {
				$aux=$aux.$form['info']['esquerdo'];
			}
			else{
				$aux=$aux.$form['esquerdo'];
			}
			if (@$form['conectivo']=='not' || @$form['info']['conectivo']=='not') {
				if (FuncoesResolucao::checaAtomico($form) || FuncoesTableaux::checaAtomico($form)) {
					if (@$form['info']) {
						$aux=$aux.$form['info']['conectivo'];
						$aux=$aux."(".$form['info']['direito']."))";
						$form=$aux;
					}
					else{
						$aux=$aux.$form['conectivo'];
						$aux=$aux."(".$form['direito']."))";
						$form=$aux;
					}
					return;
				}
			}
			if (@$form['info']) {
				$aux=$aux.$form['info']['conectivo'];
				$aux=$aux.$form['info']['direito'].")";
			}
			else{
				$aux=$aux.$form['conectivo'];
				$aux=$aux.$form['direito'].")";
			}
			$form=$aux;
			return;
		}
	}
}
