<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Formula;

use App\FuncoesResolucao;

class FuncoesAuxiliares extends Model
{
    //Função recebe um ponteiro para uma String fórmula e converte
	//Seus conectivos para símbolos
	public static function converteConectivoSimbolo(&$form){
		$form=str_replace('e','^',$form);
		$form=str_replace('ou','v',$form);
		$form=str_replace('implica','-',$form);
		$form=str_replace('not','!',$form);
	}

	//Função recebe um ponteiro para uma String fórmula e converte
	//Seus conectivos de símbolos para o nome por extenso
	public static function converteConectivoExtenso(&$form){
		$form=str_replace('^','e',$form);
		$form=str_replace('v','ou',$form);
		$form=str_replace('-','implica',$form);
		$form=str_replace('!','not',$form);
	}

	//Função auxiliar para facilitar a extração de conectivos de fórmulas com not
	//Uso desta função é bem restrito e no momento do "parsing" das fórmulas
	public static function converteConectivoNot(&$form){
		$form=str_replace('^','not_e',$form);
		$form=str_replace('v','not_ou',$form);
		$form=str_replace('-','not_implica',$form);
	}


	//Função para verificação da corretude das formulas com parenteses
	//Use no lado direito ou esquerdo de um objeto formula
	//Recebe uma STRING e retorna erro caso haja, ou Ok caso esteja correta
	//OBSERVAÇÃO IMPORTANTE
	//Verificar durante a etapa de fazer o WebService funcionar
	//Um tratamento para fórmulas incorretas, a aplicação NÃO pode encerrar
	public static function verificaFormulaCorreta(&$form){
	    
		$contador=0;
		$contador2=0;
		$i;
		$abreFormula=false;
		$esquerdo=true;
		$subFormula=0;
		//$auxFormula[];
		for ($i=0; $i<strlen($form); $i++){
			
			//Abriu parenteses
			if($form[$i]=='('){
				$contador+=1;
				if($form[$i+1]!='('){
					$abreFormula=true;
					$subFormula++;
				}
			}
			//Fecha parenteses
			elseif($form[$i]==')'){
				$contador-=1;
				if($contador<0){
					#Criar um tratamento aqui
					//Se o usuário digitar a entrada vamos precisar usar uma rotina de correção e chamar verifica recursivamente
					//print "Fórmula com digitação incorreta<br>";
					//print $form;
					//print "<br>";
					exit(1);
				}
				
				if($abreFormula==true){
					$abreFormula=false;
				}
				$contador2++;
			}
			
		}
		if($contador!=0){
			#Criar um tratamento aqui
			//Se o usuário digitar a entrada vamos precisar usar uma rotina de correção e chamar verifica recursivamente
			//print $form;
			//print "<br>";
		    //print "Fórmula com digitação incorreta";
			exit(1);
		}
		//print "Fórmula Ok<br>";
		
	}


	//Recebe uma String fórmula (Não é um objeto fórmula), remove os parenteses mais externos
	//e devolve um objeto Formula com os dois lados separados e o conectivo mais externo classificado
	//Basicamente faz um "PARSING"
	public static function resolveParenteses($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm = new Formula();
		$aux;
		$esquerdo=true;
		$abreFormula=false;
		$contador=0;
		$not=false;

		converteConectivoSimbolo($form);
		////print "<br> Teste".$form;
		//Se for um átomo positivo
		//OBS: Talvez haja uma maneira mais apropriada de tratar isto
		//Em caso de erro nos cálculos, checar esta etapa
		//Número 3 é porque há dois parênteses e o átomo SEMPRE, por exemplo: (A)

		if(strlen($form)==3){
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm->setDireito($form);
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
			$auxForm->setDireito($form);
			$auxForm->setConectivo("not");
			return $auxForm;
		}

		//Se não for átomo, caso mais geral
		for ($i=0; $i<strlen($form); $i++){
			//Caso notnotnot
			if($form[$i]=='!' && $form[$i+1]=='!' && $form[$i+2]=='!'){
				$form=substr($form, 4);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm->setDireito($form);
				$auxForm->setConectivo("not");
				return $auxForm;
			}
			//Caso notnot
			if($form[$i]=='!' && $form[$i+1]=='!'){
				$form=substr($form, 3);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm->setDireito($form);
				$auxForm->setConectivo("notnot");
				return $auxForm;
			}
			

			//Se achar o conectivo not no exterior de um parentese
			//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
			//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
			//é a abertura de um parenteses
			if($form[$i]=='!' && $form[$i+1]=='('){
				$not=true;
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
				if((in_array($form[$i],$listaConectivos)) && ($contador==1)){
					if($not==true){
						$aux=$form[$i];
						FuncoesAuxiliares::converteConectivoNot($aux);
						$auxForm->setConectivo($aux);
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						FuncoesAuxiliares::converteConectivoExtenso($aux);
						$auxForm->setConectivo($aux);
						$esquerdo=false;
					}
					
				}
				if($esquerdo==true){
					$auxForm->setEsquerdo($auxForm->getEsquerdo().$form[$i]);
				}
				if($esquerdo==false){
					$auxForm->setDireito($auxForm->getDireito().$form[$i]);
				}
			}
		}
		$auxForm->setEsquerdo(substr($auxForm->getEsquerdo(), 1));
		$auxForm->setDireito(substr($auxForm->getDireito(), 1));
		return $auxForm;
	}
	//Versão que retorna um array fórmula
	public static function resolveParenteses2($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm['esquerdo']=NULL;
		$auxForm['conectivo']=NULL;
		$auxForm['direito']=NULL;
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
			$auxForm['direito']=$form;
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
			$auxForm['direito']=$form;
			$auxForm['conectivo']='not';
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

				if ($auxForm['esquerdo']=='(') {
					$auxForm['esquerdo']=NULL;
				}
				$form=substr($form, 4);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['direito']=$form;
				$auxForm['conectivo']='not';
				return $auxForm;
			}
			//Caso notnot
			if($form[$i]=='!' && $form[$i+1]=='!' && ($i==0 || $i==1)){
				//Correções específicas para o caso em que notnot está entre parênteses
				if ($form[0]=='(') {
					$form=substr($form, 1);
					$form=substr($form, 0, strlen($form)-1);
				}

				if ($auxForm['esquerdo']=='(') {
					$auxForm['esquerdo']=NULL;
				}

				$form=substr($form, 3);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['direito']=$form;
				$auxForm['conectivo']='notnot';
				return $auxForm;
			}
			

			//Se achar o conectivo not no exterior de um parentese
			//Certamente há uma fórmula do tipo not para atribuir um conectivo not_algumacoisa
			//Mas preciso me certificar de que não é um átomo negativo, então verifico se o próximo elemento
			//é a abertura de um parenteses
			if($form[$i]=='!' && $form[$i+1]=='('){
				//Se for um átomo, não sinaliza a flag not
				if ($form[$i+3]==')') {
				//faça nada			
							
				}
				//Se o not for para uma fórmula mais interna e não para fórmula externa como um todo
				//Melhor tratar aqui
				

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
						$auxForm['conectivo']=$aux;
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						FuncoesAuxiliares::converteConectivoExtenso($aux);
						$auxForm['conectivo']=$aux;
						$esquerdo=false;
					}
					
				}
				if($esquerdo==true){
					$auxForm['esquerdo']=$auxForm['esquerdo'].$form[$i];
				}
				if($esquerdo==false){
					$auxForm['direito']=$auxForm['direito'].$form[$i];
				}
			}
		}
		$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
		$auxForm['direito']=substr($auxForm['direito'], 1);
		//Correções de parênteses excedentes antes de retornar a fórmula
		//Caso 1 - Átomo positivo
		
		if (strlen($auxForm['esquerdo'])==3 && @$auxForm['esquerdo'][0]=='(' ) {
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
		}
		if (strlen($auxForm['direito'])==3 && @$auxForm['direito'][0]=='(' ) {
			$auxForm['direito']=substr($auxForm['direito'], 1);
			$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
		}
		//Caso 2 - Átomo negativo
		
		if (strlen($auxForm['esquerdo'])==6 && @$auxForm['esquerdo'][0]=='(' &&  @$auxForm['esquerdo'][1]=='!' ) {
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
			$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
		}
		if (strlen($auxForm['direito'])==6 && @$auxForm['direito'][0]=='(' &&  @$auxForm['direito'][1]=='!') {
			$auxForm['direito']=substr($auxForm['direito'], 1);
			$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
		}
		
		$auxiliar=$auxForm['esquerdo'];
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
			if (@$auxForm['esquerdo'][0]=='(' && @$auxForm['esquerdo'][strlen($auxForm['esquerdo'])-1]==')') {
				$auxForm['esquerdo']=substr($auxForm['esquerdo'], 1);
				$auxForm['esquerdo']=substr($auxForm['esquerdo'], 0, strlen($auxForm['esquerdo'])-1);
			}
		}
		$contador=0;
		$auxiliar=$auxForm['direito'];
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
			if (@$auxForm['direito'][0]=='(' && @$auxForm['direito'][strlen($auxForm['direito'])-1]==')') {
				$auxForm['direito']=substr($auxForm['direito'], 1);
				$auxForm['direito']=substr($auxForm['direito'], 0, strlen($auxForm['direito'])-1);
			}
		}
		
		return $auxForm;
	}


	//Recebe uma String formula como entrada, caso a formula não tenha
	//nenhum problema de digitação, como por exemplo, os parenteses,
	//a formula sera transformada em um objeto Formula pronto para
	//ser inserido na árvore.
	public static function processaEntrada($form,&$objForm) {
		FuncoesResolucao::verificaFormulaCorreta($form);
		$objForm=FuncoesResolucao::resolveParenteses($form);    
	}
	//Recebe um array de Strings formula e os adiciona na árvore para inicializar o processamento
	//Um //print está sendo colocado para controle interno, mas possivelmente será retirado na versão final
	//Faz a negação da pergunta, que é sempre o último elemento do array
	public static function inicializaArvore(&$arrayForm,&$arvore){

		for ($i=0; $i < count($arrayForm); $i++) { 
			FuncoesAuxiliares::converteConectivoSimbolo($arrayForm[$i]);
			//print "Processando etapa ".$i."... ".$arrayForm[$i]."<br><br>";

			//Nega a pergunta
			if(($i+1)==count($arrayForm)){
				$arrayForm[$i]="not".$arrayForm[$i];
			}

			$auxForm = new Formula();
			FuncoesResolucao::processaEntrada($arrayForm[$i],$auxForm);
			$arvore[]=$auxForm;


		}
	}

	public static function imprime_r($array){
		for ($i=0; $i < count($array) ; $i++) {
			//print "Formula ".$i." - "; 
			//print_r($array[$i]);
			//print "<br>";
		}
	}


	//Função recursiva para imprimir as fórmulas de cada Nó da Árvore
	public static function imprimeDescendo($no){


		//print_r($no->info);
		verificaStatusNo($no);

		if($no->filhoCentral){
			imprimeDescendo($no->filhoCentral);
		}
		if($no->filhoEsquerda){
			imprimeDescendo($no->filhoEsquerda);
		}
		if($no->filhoDireita){
			imprimeDescendo($no->filhoDireita);
		}

	}
	/*
	//Função utilizada somente por imprimDescendo para ajustaro formato da impressão
	public static function verificaStatusNo($no){
		switch($no){
			case $no->central:
				//print "  Central <br>";
				break;
			case $no->esquerda:
				//print "  Esquerda ------ ";
				break;
			case $no->direita:
				//print "  Direita <br>";
				break;
			default:
				if($no->info=="fechado"){
					break;
				}
				//print "Nó não categorizado";
		}
	}
	*/
	public static function imprimeArvoreRaiz($arv){
		foreach ($arv as $key => $value) {
			//print "BD".$key." - ";
			//print_r($arv[$key]->info);

		}
	}
	//Função que adiciona Caracter no meio de uma string numa posição pré-definida
	public static function addCaracter($var, $caracter, $lim){
		$saida;
		$parte1='';
		$parte2='';
		for($i=0;$i<strlen($var);$i++){
			if ($i>=$lim) {
				$parte2.=$var[$i];
			}
			else{
				$parte1.=$var[$i];
			}
					
		}
		$saida=$parte1.$caracter.$parte2;
		return $saida;
	}

	//Função que deleta um Carater específico de uma String
	//Funciona por referência
	public static function deletaCaracter(&$str, $indice){
		$str1=substr($str,0,$indice);
		$str2 = substr($str,$indice+1,strlen($str));
		$str=$str1.$str2;
	}
	//Em profundidade 
	public static function formataFormulas(&$form){
		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['esquerdo'])>3){
			$aux=FuncoesAuxiliares::resolveParenteses2($form['esquerdo']);
			$form['esquerdo']=$aux;
			FuncoesAuxiliares::formataFormulas($form['esquerdo']);
		}
		if(@strlen(@$form['direito'])>3){
			$aux=FuncoesAuxiliares::resolveParenteses2($form['direito']);
			$form['direito']=$aux;
			FuncoesAuxiliares::formataFormulas($form['direito']);
		}

	}
}
