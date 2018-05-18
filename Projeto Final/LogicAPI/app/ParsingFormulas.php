<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesResolucao;
use App\Formula;

class ParsingFormulas extends Model{
	//Função que recebe uma fórmula em string e retorna a fórmula no formato de array
	//Retorno é array ('esquerdo' => , 'conectivo' => , 'direito' =>)
	//Métodos que usarão: Resolução, Dedução natural proposicionais
    public static function resolveParenteses($form){
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
	//Função que recebe uma fórmula em string e retorna a fórmula no formato de array
	//Retorno é array ('info' => array ('esquerdo' => , 'conectivo' => , 'direito' =>), ... 'hashAtomos => ' )
	//Para saber todos os campos do array, checar a inicialização de auxForm abaixo
	//Métodos que usarão: Tableaux (Proposicional e primeira ordem)
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
		//print "<br> Teste".$form;
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
	//Função que recebe uma fórmula em string e retorna a fórmula no formato de array
	//Retorno é array ('info' => array ('esquerdo' => , 'conectivo' => , 'direito' =>), ... 'hashAtomos => ' )
	//Para saber todos os campos do array, checar a inicialização de auxForm abaixo
	//Métodos que usarão: Tableaux (Proposicional e primeira ordem)
	public static function resolveParentesesTableauxLPO($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm['info']=array('esquerdo' => null, 'conectivo' => array('operacao' => null, 'variavel'=> null), 'direito'=>null);
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
		$aux;
		$esquerdo=true;
		$abreFormula=false;
		$contador=0;
		$not=false;

		FuncoesAuxiliares::converteConectivoSimbolo($form);
		//Correção básica de parenteses
		if ($form[0]!='(' && $form[0]!='!') {
			$form="(".$form.")";
		}
		//print "<br> Teste".$form;
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

		if(strlen($form)==4 && $form[0]=='!'){
			$form=substr($form, 1);		
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']['operacao']='not';
			return $auxForm;
		}
		//Para átomos no contexto de quantificadores universais/existenciais
		//Exemplo F(a)
		if(strlen($form)==6){		
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$form=substr($form, 1);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==4){
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		//Para átomos negativos no contexto de quantificadores universais/existenciais
		//Exemplo ¬F(a)
		if(strlen($form)==7){
			$aux=$form[0];			
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);	
				$auxForm['info']['conectivo']['operacao']='not';
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==5){
			$aux=$form[0];
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);		
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['conectivo']['operacao']='not';
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
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
				$auxForm['info']['conectivo']['operacao']='not';
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
					//$auxForm['info']['direito']="(".$auxForm['info']['direito'].")";
				}
				$auxForm['info']['conectivo']['operacao']='notnot';
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
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['info']['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
						}
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['info']['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
						}
						
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
	//Função que recebe uma fórmula em string e retorna a fórmula no formato de array
	//Retorno é array ('info' => array ('esquerdo' => , 'conectivo' => , 'direito' =>), ... 'proximo => ' )
	//Para saber todos os campos do array, checar a inicialização de auxForm abaixo
	//Métodos que usarão: Semantica
	public static function resolveParentesesSemantica($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm['info']=array('esquerdo' => null, 'conectivo' => array('operacao' => null, 'variavel'=> null), 'direito' =>null);
		$auxForm['filhos']=[];
		$auxForm['pai']=NULL;
		$auxForm['valor']=false;
		$auxForm['usado']=false;
		$auxForm['proximo']=NULL;
		$aux;
		$esquerdo=true;
		$abreFormula=false;
		$contador=0;
		$not=false;

		FuncoesAuxiliares::converteConectivoSimbolo($form);

		//Correção básica de parenteses
		if ($form[0]!='(' && $form[0]!='!') {
			$form="(".$form.")";
		}
		//print "<br> Teste".$form;
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

		if(strlen($form)==4 && $form[0]=='!'){
			$form=substr($form, 1);		
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['info']['direito']=$form;
			$auxForm['info']['conectivo']['operacao']='not';
			return $auxForm;
		}
		//Para átomos no contexto de quantificadores universais/existenciais
		//Exemplo F(a)
		if(strlen($form)==6){		
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$form=substr($form, 1);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==4){
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		//Para átomos negativos no contexto de quantificadores universais/existenciais
		//Exemplo ¬F(a)
		if(strlen($form)==7){
			$aux=$form[0];			
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);	
				$auxForm['info']['conectivo']['operacao']='not';
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==5){
			$aux=$form[0];
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);		
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['conectivo']['operacao']='not';
				$auxForm['info']['direito']=$form;
				return $auxForm;
			}
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
				$auxForm['info']['conectivo']['operacao']='not';
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
					//$auxForm['esquerdo']=NULL;
				}
				

				$form=substr($form, 3);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['info']['direito']=$form;
				if ($auxForm['info']['direito'][0]!='(') {
					$auxForm['info']['direito']="(".$auxForm['info']['direito'].")";
				}
				$auxForm['info']['conectivo']['operacao']='notnot';
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
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['info']['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
						}
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['info']['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['info']['conectivo']['operacao']=$aux;
						}
						
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

	//Função que recebe uma fórmula em string e retorna a fórmula no formato de array
	//Retorno é array ('esquerdo' => , 'conectivo' => , 'direito' =>)
	//Métodos que usarão: Resolução, Dedução natural de primeira ordem
	public static function resolveParentesesLPO($form){
		$listaConectivos = Formula::getListaConectivos();
		$auxForm['esquerdo']=NULL;
		$auxForm['conectivo']=array('operacao' => null, 'variavel'=> null);
		$auxForm['direito']=NULL;

		$aux;
		$esquerdo=true;
		$abreFormula=false;
		$contador=0;
		$not=false;

		FuncoesAuxiliares::converteConectivoSimbolo($form);

		//Correção básica de parenteses
		if ($form[0]!='(' && $form[0]!='!') {
			$form="(".$form.")";
		}
		//print "<br> Teste".$form;
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

		if(strlen($form)==4 && $form[0]=='!'){
			$form=substr($form, 1);		
			$form=substr($form, 1);
			$form=substr($form, 0, strlen($form)-1);
			$auxForm['direito']=$form;
			$auxForm['conectivo']['operacao']='not';
			return $auxForm;
		}
		//Para átomos no contexto de quantificadores universais/existenciais
		//Exemplo F(a)
		if(strlen($form)==6){		
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$form=substr($form, 1);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==4){
			$flag=false;
			for($i=0;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag) {
				$auxForm['direito']=$form;
				return $auxForm;
			}
		}
		//Para átomos negativos no contexto de quantificadores universais/existenciais
		//Exemplo ¬F(a)
		if(strlen($form)==7){
			$aux=$form[0];			
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);	
				$auxForm['conectivo']['operacao']='not';
				$auxForm['direito']=$form;
				return $auxForm;
			}
		}
		if(strlen($form)==5){
			$aux=$form[0];
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);		
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['conectivo']['operacao']='not';
				$auxForm['direito']=$form;
				return $auxForm;
			}
		}
		//Para átomos de qualquer tamanho negativos
		if($form[0]=='!'){
			$aux=$form[0];			
			$flag=false;
			for($i=1;$i<strlen($form);$i++){
				if (in_array($form[$i], $listaConectivos)) {
					$flag=true;
				}
			}
			if(!$flag && $aux=='!') {
				$form=substr($form, 1);
				$form=substr($form, 1);
				$form=substr($form, 0, strlen($form)-1);	
				$auxForm['conectivo']['operacao']='not';
				$auxForm['direito']=$form;
				return $auxForm;
			}
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
				$auxForm['conectivo']['operacao']='not';
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
					//$auxForm['esquerdo']=NULL;
				}
				

				$form=substr($form, 3);		
				$form=substr($form, 0, strlen($form)-1);
				$auxForm['direito']=$form;
				if ($auxForm['direito'][0]!='(') {
					$auxForm['direito']="(".$auxForm['direito'].")";
				}
				$auxForm['conectivo']['operacao']='notnot';
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
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoNot($aux);
							$auxForm['conectivo']['operacao']=$aux;
						}
						$esquerdo=false;
						$not=false;
					}
					else{
						$aux=$form[$i];
						if ($aux=='&' || $aux=='@') {
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['conectivo']['operacao']=$aux;
							$i++;
							$auxForm['conectivo']['variavel']=$form[$i];
						}
						else{
							FuncoesAuxiliares::converteConectivoExtenso($aux);
							$auxForm['conectivo']['operacao']=$aux;
						}
						
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

	public static function formataFormulasLPO(&$form){
		$listaConectivos = Formula::getListaConectivos();
		$flag=false;
		$flag2=false;
		for ($i=0; $i < strlen(@$form['esquerdo']); $i++) { 
			if (@in_array($form['esquerdo'][$i], $listaConectivos)) {
				$flag=true;
			}
		}
		for ($i=0; $i < strlen(@$form['direito']); $i++) {
			if (@in_array($form['direito'][$i], $listaConectivos)) {
				$flag2=true;
			}
		}

		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['esquerdo'])>3 && $flag ){
			$aux=ParsingFormulas::resolveParentesesLPO($form['esquerdo']);
			$form['esquerdo']=$aux;
			ParsingFormulas::formataFormulasLPO($form['esquerdo']);
		}
		if(@strlen(@$form['direito'])>3 && $flag2){
			
			$aux=ParsingFormulas::resolveParentesesLPO($form['direito']);
			$form['direito']=$aux;
			ParsingFormulas::formataFormulasLPO($form['direito']);
		}
	}	

	//Função que recebe a referência uma fórmula em string e a transforma em array
	//gerando arrays aninhados para fórmulas mais complexas
	public static function formataFormulas(&$form){
		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['esquerdo'])>3){
			$aux=ParsingFormulas::resolveParenteses($form['esquerdo']);
			$form['esquerdo']=$aux;
			ParsingFormulas::formataFormulas($form['esquerdo']);
		}
		if(@strlen(@$form['direito'])>3){
			$aux=ParsingFormulas::resolveParenteses($form['direito']);
			$form['direito']=$aux;
			ParsingFormulas::formataFormulas($form['direito']);
		}

	}

	//Função que recebe a referência uma fórmula em string e a transforma em array
	//gerando arrays aninhados para fórmulas mais complexas específico para Tableaux
	public static function formataFormulasTableaux(&$form){
		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['info']['esquerdo'])>3){
			$aux=ParsingFormulas::resolveParentesesTableaux($form['info']['esquerdo']);
			$form['info']['esquerdo']=$aux;
			FuncoesTableaux::formataFormulasTableaux($form['info']['esquerdo']);
		}
		if(@strlen(@$form['info']['direito'])>3){
			$aux=ParsingFormulas::resolveParentesesTableaux($form['info']['direito']);
			$form['info']['direito']=$aux;
			ParsingFormulas::formataFormulasTableaux($form['info']['direito']);
		}
	}

	//Função que recebe a referência uma fórmula em string e a transforma em array
	//gerando arrays aninhados para fórmulas mais complexas específico para Tableaux
	public static function formataFormulasTableauxLPO(&$form){
		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['info']['esquerdo'])>4 && @!FuncoesAuxiliares::temConectivo($form)){
			$aux=ParsingFormulas::resolveParentesesTableauxLPO($form['info']['esquerdo']);
			$form['info']['esquerdo']=$aux;
			ParsingFormulas::formataFormulasTableauxLPO($form['info']['esquerdo']);
		}
		if(@strlen(@$form['info']['direito'])>4 && @!FuncoesAuxiliares::temConectivo($form)){
			$aux=ParsingFormulas::resolveParentesesTableauxLPO($form['info']['direito']);
			$form['info']['direito']=$aux;
			ParsingFormulas::formataFormulasTableauxLPO($form['info']['direito']);
		}
	}


	//Função que recebe a referência uma fórmula em string e a transforma em array
	//gerando arrays aninhados para fórmulas mais complexas específico para Semantica
	public static function formataFormulasSemantica(&$form){
		$listaConectivos = Formula::getListaConectivos();
		$flag=false;
		$flag2=false;
		for ($i=0; $i < strlen(@$form['esquerdo']); $i++) { 
			if (@in_array($form['esquerdo'][$i], $listaConectivos)) {
				$flag=true;
			}
		}
		for ($i=0; $i < strlen(@$form['direito']); $i++) {
			if (@in_array($form['direito'][$i], $listaConectivos)) {
				$flag2=true;
			}
		}

		//Se ocorrer erro, investigar a entrada no if barra por strlen
		if(@strlen(@$form['esquerdo'])>3 && $flag ){
			$aux=ParsingFormulas::resolveParentesesSemantica($form['esquerdo']);
			$form['esquerdo']=$aux;
			ParsingFormulas::formataFormulasSemantica($form['esquerdo']);
		}
		if(@strlen(@$form['direito'])>3 && $flag2){
			
			$aux=ParsingFormulas::resolveParentesesSemantica($form['direito']);
			$form['direito']=$aux;
			ParsingFormulas::formataFormulasSemantica($form['direito']);
		}
	}

	//Função que recebe um array fórmula e corrige casos em que temos um campo array do tipo fórmula dentro de outro
	//array do tipo fórmula com um dos campos (esquerdo ou direito) vazio.
	//Específico para Tableaux
	public static function corrigeArraysTableaux(&$form){
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

	//Função que recebe um array fórmula e corrige casos em que temos um campo array do tipo fórmula dentro de outro
	//array do tipo fórmula com um dos campos (esquerdo ou direito) vazio
	public static function corrigeAtomos(&$form){
		if(@$form['esquerdo']==NULL && @is_array($form['direito'])){
			if (@$form['direito']['esquerdo']==NULL) {
				$form['conectivo']=$form['direito']['conectivo'];
				$form['direito']=$form['direito']['direito'];
			}
		}
		
		if (@strlen(@$form['esquerdo'])==3 && @$form['esquerdo'][0]=='(' ) {
			$form['esquerdo']=substr($form['esquerdo'], 1);
			$form['esquerdo']=substr($form['esquerdo'], 0, strlen($form['esquerdo'])-1);
			//$form['esquerdo']=$form;
		}
		if (@strlen(@$form['direito'])==3 && @$form['direito'][0]=='(' ) {
			$form['direito']=substr($form['direito'], 1);
			$form['direito']=substr($form['direito'], 0, strlen($form['direito'])-1);
			//$form['direito']=$form['direito'];
		}
	}
	//Função que recebe um array fórmula e corrige casos em que temos um campo array do tipo fórmula dentro de outro
	//array do tipo fórmula com um dos campos (esquerdo ou direito) vazio.
	public static function corrigeArrays(&$form){
		if (@$form['esquerdo']==NULL && @is_array($form['direito'])) {
			$aux1=$form['direito'];
			$form['esquerdo']=$aux1['esquerdo'];
			$form['conectivo']=$aux1['conectivo'];
			$form['direito']=$aux1['direito'];
			return;
		}
		if (@$form['direito']==NULL && @is_array($form['esquerdo'])) {
			$aux1=$form['esquerdo'];
			$form['esquerdo']=$aux1['esquerdo'];
			$form['conectivo']=$aux1['conectivo'];
			$form['direito']=$aux1['direito'];
		}

		//Correção para caso haja um átomo sendo tratado como array
		//dentro de um array, por exemplo form[esquerdo][esquerdo]==null e form[esquerdo][direito]==algumacoisa
		if (is_array(@$form['esquerdo']['esquerdo']) && @$form['esquerdo']['esquerdo']['esquerdo']==NULL && @$form['esquerdo']['esquerdo']['conectivo']==NULL) {
			$form['esquerdo']['esquerdo']=$form['esquerdo']['esquerdo']['direito'];
			return;
		}
		if (is_array(@$form['esquerdo']['direito']) && @$form['esquerdo']['direito']['esquerdo']==NULL && @$form['esquerdo']['direito']['conectivo']==NULL) {
			$form['esquerdo']['direito']=$form['esquerdo']['direito']['direito'];
			return;
		}
		if (is_array(@$form['direito']['direito']) && @$form['direito']['direito']['esquerdo']==NULL && @$form['direito']['direito']['conectivo']==NULL) {
			$form['direito']['direito']=$form['direito']['direito']['direito'];
			return;
		}
	}
	//Função que recebe uma referência para uma array fórmula e a converte num string fórmula
	//Esta é a função que deve ser chamada no código principal ao realizar a conversão
	public static function converteFormulaString(&$form){
		while (@is_array($form['esquerdo']) || @is_array($form['direito']) || is_array($form)) {
			ParsingFormulas::reverteFormatacao($form);
		}

		if (strlen($form)==1) {
			$form="(".$form.")";
		}
	}
	public static function consertaStringFormula(&$form){
		converteConectivoSimbolo($form);
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
		converteConectivoExtenso($form);
	}

	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e trabalha recursivamente
	//com colocaParenteses para transforma-lo em string
	//Deve ser usada para resolver os casos em que há arrays aninhados
	public static function reverteFormatacao(&$form){
		if (@is_array($form['esquerdo'])) {
			ParsingFormulas::reverteFormatacao($form['esquerdo']);
		}
		elseif (@!is_array($form['esquerdo']) ) {
			ParsingFormulas::colocaParenteses($form);
		}
		if (@is_array($form['direito'])) {
			ParsingFormulas::reverteFormatacao($form['direito']);
		}
		elseif (@!is_array($form['direito']) ) {
			ParsingFormulas::colocaParenteses($form);
		}
	}

	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e transforma em string
	public static function colocaParenteses(&$form){
		//print_r($form);
		if (@is_array($form['esquerdo']) && @!is_array($form['direito'])) {
			if ($form['conectivo']=='not') {
				if (FuncoesResolucao::checaAtomico($form)) {
					$aux=$form['conectivo'];
					$aux=$aux."(".$form['direito']."))";
				}
			}
			if ($form['conectivo']=='not_ou') {
				$form['esquerdo']="not(".$form['esquerdo'];
				$aux=$aux."ou";
				$aux=$aux.$form['direito']."))";
				$form=$aux;
				return;
			}
			if ($form['conectivo']=='not_e') {
				$form['esquerdo']="not(".$form['esquerdo'];
				$aux=$aux."e";
				$aux=$aux.$form['direito']."))";
				$form=$aux;
				return;
			}
			if ($form['conectivo']=='not_implica') {
				$form['esquerdo']="not(".$form['esquerdo'];
				$aux=$aux."implica";
				$aux=$aux.$form['direito']."))";
				$form=$aux;
				return;
			}
			$aux=$form['conectivo'];
			$aux=$aux.$form['direito'].")";
			$form['direito']=$aux;

			return;

		}
		elseif (@!is_array($form['esquerdo']) && @is_array($form['direito'])) {
			$aux="(";
			$aux=$aux.$form['esquerdo'];
			$form['esquerdo']=$aux;
			return;
		}
		elseif(is_array($form)){

			if ($form['conectivo']=='not_ou') {
				
				if ($form['esquerdo'][0]=="(") {
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
				$form=$aux;
				return;
			}
			if ($form['conectivo']=='not_e') {
				if ($form['esquerdo'][0]=="(") {
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
				$form=$aux;
				return;
			}
			if ($form['conectivo']=='not_implica') {
				
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
				$form=$aux;
				return;
			}
			$aux="(";
			$aux=$aux.$form['esquerdo'];
			if ($form['conectivo']=='not') {
				if (FuncoesResolucao::checaAtomico($form)) {
					$aux=$aux.$form['conectivo'];
					$aux=$aux."(".$form['direito']."))";
					$form=$aux;
					return;
				}
			}

			$aux=$aux.$form['conectivo'];
			$aux=$aux.$form['direito'].")";
			$form=$aux;
			return;
		}
	}
	//Método que recebe a referência para um array contendo todas as fórmulas em String
	//e faz a transformação dessas strings em Arrays trabalháveis pelos métodos
	//Essa versão não pode ser usada pelo tableaux
	public static function converteFormulasEmArray(&$array){
		$aux=[];
		//Faz o parsing inicial, separando as fórmulas em esquerdo, direito e conectivo
		foreach ($array as $key => $value) {
			$aux[$key]=ParsingFormulas::resolveParenteses($value);
		}
		$array=$aux;


		//Repete a opetaração em profundidade
		foreach ($array as $key => $value) {
			if (is_array($value['esquerdo'])) {
				ParsingFormulas::formataFormulas($array[$key]['esquerdo']);
			}
			if (is_array($value['direito'])) {
				ParsingFormulas::formataFormulas($array[$key]['direito']);
			}
			elseif (!(is_array($value['esquerdo'])) && !(is_array($value['direito']))) {
				ParsingFormulas::formataFormulas($array[$key]);
			}	
		}

		foreach ($array as $key => $value) {
			ParsingFormulas::formataFormulas($array[$key]);
			if (@is_array($array[$key]['esquerdo']['esquerdo'])) {
				ParsingFormulas::formataFormulas($array[$key]['esquerdo']['esquerdo']);
			}
			if (@is_array($array[$key]['esquerdo']['direito'])) {
				ParsingFormulas::formataFormulas($array[$key]['esquerdo']['direito']);
			}
			if (@is_array($array[$key]['direito']['esquerdo'])) {
				ParsingFormulas::formataFormulas($array[$key]['direito']['esquerdo']);
			}
			if (@is_array($array[$key]['direito']['direito'])) {
				ParsingFormulas::formataFormulas($array[$key]['direito']['direito']);
			}
		}
	}

	//Método que recebe um array de fórmulas string e devolve um array convertido em fórmulas array
	//Os campos do array fórmula são ('esquerdo' => , 'conectivo' => , 'direito' =>)
	public static function processaEntradaLPO($listaFormulas){
		//Tratar a entrada, verificação de digitação correta
		foreach ($listaFormulas as $key => $value) {
			FuncoesAuxiliares::verificaFormulaCorreta($listaFormulas[$key]);
			$entradaConvertida[$key]=ParsingFormulas::resolveParentesesLPO($listaFormulas[$key]);
		}
		
		return $entradaConvertida;
	}

	//Método que recebe um array de fórmulas string e o tamnanho deste array
	//devolve um array convertido em fórmulas array com a pergunta negada
	//Os campos do array fórmula são ('esquerdo' => , 'conectivo' => , 'direito' =>)

	public static function negaPerguntaLPO($listaFormulas,$tamanho){
		//Nega a pergunta
		$listaFormulas[$tamanho-1]="not".$listaFormulas[$tamanho-1];
		//Tratar a entrada, verificação de digitação correta
		foreach ($listaFormulas as $key => $value) {
			FuncoesAuxiliares::verificaFormulaCorreta($listaFormulas[$key]);
			$entradaConvertida[$key]=ParsingFormulasresolveParentesesLPO($listaFormulas[$key]);
		}
		
		return $entradaConvertida;
	}
}
