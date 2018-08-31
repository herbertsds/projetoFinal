<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\FuncoesAuxiliares;

use App\ParsingFormulas;

class FuncoesSemantica extends Model
{	

	public static function processaEntradaSemantica($listaFormulas){
		//Tratar a entrada, verificação de digitação correta
		foreach ($listaFormulas as $key => $value) {
			FuncoesAuxiliares::verificaFormulaCorreta($listaFormulas[$key]);

			$entradaConvertida[$key]=ParsingFormulas::resolveParentesesSemantica($listaFormulas[$key]);
			print "<br>Fórmula Correta<br>";
			print_r($entradaConvertida[$key]);
		}
		
		return $entradaConvertida;
	}

	public static function geraArvore(&$formula,$dominio,&$nosFolha,&$contador){
		if ($contador==0) {
			FuncoesSemantica::aplicaRegraSemantica($formula,$dominio,$nosFolha);
			$contador++;
		}
		else{
			foreach ($nosFolha as $key => $value) {
				FuncoesSemantica::aplicaRegraSemantica($nosFolha[$key],$dominio,$nosFolha);
			}
		}
		
	}
	public static function aplicaRegraSemantica(&$formula,$dominio,&$nosFolha){
		$noAuxEsq=FuncoesSemantica::criaFormulaSemantica();
		$noAuxDir=FuncoesSemantica::criaFormulaSemantica();
		$noAuxEsq2=FuncoesSemantica::criaFormulaSemantica();
		$noAuxDir2=FuncoesSemantica::criaFormulaSemantica();
		
		if (@$formula['info']['esquerdo']!=null) {
			FuncoesSemantica::corrigeAtomos($formula['info']['esquerdo']);
			$noAuxEsq=ParsingFormulas::resolveParentesesSemantica($formula['info']['esquerdo']);
		}
		if (@$formula['info']['direito']!=null) {
			FuncoesSemantica::corrigeAtomos($formula['info']['direito']);
				$noAuxDir=ParsingFormulas::resolveParentesesSemantica($formula['info']['direito']);
		}
		$noAuxDir['pai']=&$formula;
		$noAuxEsq['pai']=&$formula;

		FuncoesSemantica::removerFormula($nosFolha,$formula['info']);
		
		switch ($formula['info']['conectivo']['operacao']) {
			case 'e':
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;
			case 'ou':
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;		
			case 'implica':

				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;
			case 'not_e':
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;
			case 'not_ou':
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;
			case 'not_implica':
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxEsq);
				FuncoesSemantica::adicionaArray($formula['filhos'], $noAuxDir);

				$formula['filhos'][0]['proximo']=&$formula['filhos'][1];

				FuncoesSemantica::adicionaArray($nosFolha, $noAuxEsq);
				FuncoesSemantica::adicionaArray($nosFolha, $noAuxDir);
				break;
			case 'paraTodo':
				foreach ($dominio as $key => $value) {
					$auxString=$formula['info']['direito'];
					$noAux=FuncoesSemantica::criaFormulaSemantica();
					for ($i=0; $i < strlen($auxString); $i++) { 
						if ($auxString[$i]==$formula['info']['conectivo']['variavel']) {
							$auxString[$i]=$value;
						}
					}
					$noAux=ParsingFormulas::resolveParentesesSemantica($auxString);
					$noAux['pai']=&$formula;
					FuncoesSemantica::adicionaArray($formula['filhos'],$noAux);
					if ($key>0) {
						$formula['filhos'][$key-1]['proximo']=&$formula['filhos'][$key];
					}
					
					FuncoesSemantica::adicionaArray($nosFolha, $noAux);
					unset($noAux);
				}
				break;
			case 'xist':
				foreach ($dominio as $key => $value) {
					$auxString=$formula['info']['direito'];
					$noAux=FuncoesSemantica::criaFormulaSemantica();
					for ($i=0; $i < strlen($auxString); $i++) { 
						if ($auxString[$i]==$formula['info']['conectivo']['variavel']) {
							$auxString[$i]=$value;
						}
					}
					$noAux=ParsingFormulas::resolveParentesesSemantica($auxString);
					$noAux['pai']=&$formula;
					FuncoesSemantica::adicionaArray($formula['filhos'],$noAux);
					if ($key>0) {
						$formula['filhos'][$key-1]['proximo']=&$formula['filhos'][$key];
					}
					FuncoesSemantica::adicionaArray($nosFolha, $noAux);
					unset($noAux);
				}
				break;
			case 'not_paraTodo':
				foreach ($dominio as $key => $value) {
					$auxString=$formula['info']['direito'];
					$noAux=FuncoesSemantica::criaFormulaSemantica();
					for ($i=0; $i < strlen($auxString); $i++) { 
						if ($auxString[$i]==$formula['info']['conectivo']['variavel']) {
							$auxString[$i]=$value;
						}
					}
					$noAux=ParsingFormulas::resolveParentesesSemantica($auxString);
					$noAux['pai']=&$formula;
					FuncoesSemantica::adicionaArray($formula['filhos'],$noAux);
					if ($key>0) {
						$formula['filhos'][$key-1]['proximo']=&$formula['filhos'][$key];
					}
					FuncoesSemantica::adicionaArray($nosFolha, $noAux);
					unset($noAux);
				}
				break;
			case 'not_xist':
				foreach ($dominio as $key => $value) {
					$auxString=$formula['info']['direito'];
					$noAux=FuncoesSemantica::criaFormulaSemantica();
					for ($i=0; $i < strlen($auxString); $i++) { 
						if ($auxString[$i]==$formula['info']['conectivo']['variavel']) {
							$auxString[$i]=$value;
						}
					}
					$noAux=ParsingFormulas::resolveParentesesSemantica($auxString);
					$noAux['pai']=&$formula;
					FuncoesSemantica::adicionaArray($formula['filhos'],$noAux);
					if ($key>0) {
						$formula['filhos'][$key-1]['proximo']=&$formula['filhos'][$key];
					}
					FuncoesSemantica::adicionaArray($nosFolha, $noAux);
					unset($noAux);
				}
				break;
			
			default:
				# code...
				break;
		}
	}
	public static function criaFormulaSemantica(){
		global $id;
		$aux['info']=array('esquerdo' => null, 'conectivo' => array('operacao' => null, 'variavel'=> null), 'direito' =>null);
		$aux['filhos']=[];
		$aux['pai']=NULL;
		$aux['valor']=false;
		$aux['usado']=false;
		$aux['proximo']=NULL;
		$aux['id']=$id+1;
		$id++;
		return $aux;
	}

	public static function adicionaArray(&$array,&$valor){
		$tam=count($array);
		$soma=1;
		if ($tam!=0) {
			//Consertar o índice do array para evitar sobreposição
			while (@$array[$tam+$soma]!=null) {
				$soma++;
			}
			if (@$array[$tam+$soma-1]==null) {
				$soma--;
			}
			$array[$tam+$soma]=&$valor;
		}
		else{
			$array[0]=&$valor;
		}
	}
	public static function checaAtomico($form){
		//@ colocado para previnir que fórmulas não instanciadas deem warning
		if (@$form['esquerdo']==NULL && (@$form['conectivo']==NULL || @$form['conectivo']='not')) {
			return true;
		}
		if (!is_array($form)) {
			return true;
		}
		else{
			return false;
		}	
	}
	public static function checaAtomicoSemantica($form){
		//@ colocado para previnir que fórmulas não instanciadas deem warning
		if (@$form['info']['esquerdo']==NULL && (@$form['info']['conectivo']['operacao']==NULL || @$form['info']['conectivo']['operacao']='not')) {
			return true;
		}
		if (!is_array($form)) {
			return true;
		}
		else{
			return false;
		}	
	}
	public static function removerFormula(&$listaFormulas,$form){
		foreach ($listaFormulas as $key => $value) {
			if ($value['info']==$form) {
				unset($listaFormulas[$key]);
				return;
			}
		}
	}

	public static function imprimeArvore(&$raiz,&$arvoreSaida,&$listaDeNos,&$indice){
		//Os arrays auxiliares foram criados meramente para formatar a saída
		//de um jeito fácil para utilizar no plugin do front-end
		//Caso queira-se usar outra maneira pra imprimir a árvore
		//esses arrays não são necessários
		$arvoreSaida=[];
		$raiz['id']=0;
		$arvoreSaida[]['id']=$raiz['id'];
		print "<br>Fórmula inicial<br>";
		print "<br>Valor: ";
			if ($raiz['valor']==false) {
				print "Falso<br>";
			}
			else{
				print "Verdade<br>";
			}
			print "<br>Usado: ";
			if ($raiz['usado']==false) {
				print "Falso<br>";
			}
			elseif($raiz['usado']==true){
				print "Verdade<br>";
			}
			print "<br>Id: ".$raiz['id']." <br>";
		//$raiz['proximo']=null;
		FuncoesSemantica::converteFormulaStringSemantica($raiz['info']);
		array_push($listaDeNos,array($raiz['id'],$raiz['info']));

		print_r($raiz['info']);
		$arrayFilhos=[];
		$arrayFilhos[0]=[];
		$arrayFilhos[1]=[];
		$arrayFilhos[0]['id']=$raiz['filhos'][0]['id'];
		$arrayFilhos[1]['id']=$raiz['filhos'][1]['id'];
		@FuncoesSemantica::adicionaArray($arvoreSaida[0],$arrayFilhos[0]);
		@FuncoesSemantica::adicionaArray($arvoreSaida[0],$arrayFilhos[1]);
		print "<br>Filhos no próximo nível<br>";
		foreach ($raiz['filhos'] as $key => $value) {
			print "<br>Valor: ";
			if ($value['valor']==false) {
				print "Falso<br>";
			}
			else{
				print "Verdade<br>";
			}
			print "<br>Usado: ";
			if ($value['usado']==false) {
				print "Falso<br>";
			}
			elseif($value['usado']==true){
				print "Verdade<br>";
			}
			print "<br>Id: ".$value['id']." <br>";
			//$raiz['filhos'][$key]['proximo']=null;
			FuncoesSemantica::converteFormulaStringSemantica($value['info']);
			array_push($listaDeNos, array($arrayFilhos[0]['id'],$value['info']));
			array_push($listaDeNos, array($arrayFilhos[1]['id'],$value['info']));
			print_r($value['info']);
			//print "<br>Próximo: <br>";
			//print_r($value['proximo']['info']);
		}
		$posicao=0;
		//$posicao2=0;
		if ($raiz['filhos'][0]!=null) {
			$lista1=$raiz['filhos'];
		}
		$lista2=[];
		$arrayFilhos2=[];

		retorno:
		if ($lista1[0]['filhos']!=null) {
			print "<br>Filhos no próximo nível<br>";
		}
		$contador=0;
		foreach ($lista1 as $key => $value) {
			foreach ($lista1[$key]['filhos'] as $key2 => $value2) {
				$arrayFilhos2[$contador]=[];
				$arrayFilhos2[$contador]['id']=$value2['id'];
				@FuncoesSemantica::adicionaArray($arrayFilhos[$key],$arrayFilhos2[$contador]);
				//@array_push($arrayFilhos[$key],$arrayFilhos2[$contador]);
				array_push($lista2, $value2);
				print "<br>Valor: ";
				if ($value2['valor']==false) {
					print "Falso<br>";
				}
				elseif ($value2['valor']==true) {
					print "Verdade<br>";
				}
				print "<br>Usado: ";
				if ($value2['usado']==false) {
					print "Falso<br>";
				}
				elseif($value2['usado']==true){
					print "Verdade<br>";
				}
				print "<br>Id: ".$value2['id']." <br>";
				//$lista1[$key]['filhos'][$key2]['proximo']=null;
				FuncoesSemantica::converteFormulaStringSemantica($value2['info']);
				array_push($listaDeNos, array($value2['id'],$value2['info']));
				print_r($value2['info']);
				$contador++;
				//print "<br>Próximo: <br>";
				//print_r($value['proximo']['info']);
			}
			//Se os filhos deste nó acabarem
			if ($key==(count($lista1)-1)) {
				//Verificar se há irmão, se houver, ele deve ser o novo aux
				//para imprimir todos os seus filhos que são do mesmo nível
				if (@$lista2!=null) {
					$lista1=$lista2;
					$arrayFilhos=$arrayFilhos2;
					$arrayFilhos2=[];
					$lista2=[];

					goto retorno;
				}
				else{
					return;
				}
			}
		}
	}
	public static function listaFolhas($raiz){
		$listaF=[];
		if ($raiz['filhos'][0]!=null) {
			$lista1=$raiz['filhos'];
		}
		$lista2=[];
		retorno:
		foreach ($lista1 as $key => $value) {
			if ($value['filhos']==null) {
				array_push($listaF, $lista1[$key]);
			}
			foreach ($value['filhos'] as $key2 => $value2) {
				array_push($lista2, $value2);
				
			}
			//Se os filhos deste nó acabarem
			if ($key==(count($lista1)-1)) {
				//Verificar se há irmão, se houver, ele deve ser o novo lista1
				//para imprimir todos os seus filhos que são do mesmo nível
				if (@$lista2!=null) {
					$lista1=$lista2;
					$lista2=[];
					goto retorno;
				}
				else{
					return $listaF;
				}
			}
		}
	}

	public static function preencheProximo($relacoes,&$raiz){
		$flag=true;
		$lista1=[];
		$lista2=[];
		//Se for igual a null lançar uma exceção
		if ($raiz['filhos']!=null) {
			foreach ($raiz['filhos'] as $key => $value) {
				FuncoesSemantica::adicionaArray($lista1,$raiz['filhos'][$key]);
			}
		}
		//print "<br>Imprime Filhos<br>";
		//print_r($navega['filhos']);
		while ($flag) {
			$flag=false;
			foreach ($lista1 as $key => $value) {
				foreach ($lista1[$key]['filhos'] as $key2 => $value2) {
					FuncoesSemantica::adicionaArray($lista2,$lista1[$key]['filhos'][$key2]);
				}
			}
			foreach ($lista2 as $key => $value) {
				if ($lista2[$key]['proximo']==null && @$lista2[$key+1]!=null) {
					$lista2[$key]['proximo']=&$lista2[$key+1];
				}
			}
			$lista1=$lista2;
			$lista2=[];
			foreach ($lista1 as $key => $value) {
				if ($value['filhos']!=null) {
					$flag=true;
					break;
				}
			}
		}
	}

	public static function validaFormulas($relacoes,&$raiz){
		$flag=true;
		$lista1=[];
		$lista2=[];
		$navega;
		$navega=&$raiz;

		//print "<br>Imprime Filhos<br>";
		//print_r($navega['filhos']);
		$contador=0;
		retorno:
		while (1 || $contador<10) {
			$aux=null;
			$usados=0;
			$total=0;

			$lista1=[];
			
			if ($navega['info']==$raiz['info']) {
				if ($navega['filhos'][0]['usado']==true) {
					FuncoesSemantica::validaConectivo($raiz,$relacoes);
					return;
				}
				else{
					$navega=&$raiz['filhos'][0];
					if ($raiz['filhos'][0]['usado']==true) {
						FuncoesSemantica::validaConectivo($raiz['filhos'][0],$relacoes);
						FuncoesSemantica::validaConectivo($raiz['filhos'][1],$relacoes);
					}

				}
			}
			while ($navega!=null) {
				foreach ($navega['filhos'] as $key => $value) {
					if ($navega['filhos'][$key]['usado']==false) {
						FuncoesSemantica::adicionaArray($lista1,$navega['filhos'][$key]);
					}
					
				}
				$aux=$navega;
				$navega=&$navega['proximo'];
			}
			/*if ($navega['filhos'][0]['usado']==true) {
				print_r($navega['info']);
				dd(1);
			}*/
			//Se eu veriricar que todos os filhos são verdadeiros, quebro o loop
			foreach ($lista1 as $key => $elemento) {
				if ($elemento['filhos']!=null) {
					foreach ($elemento['filhos'] as $key2 => $filho) {
						$total++;
						if ($filho['usado']==true) {
							$usados++;
						}
					}
				}
			}
			//Fim do código de debug
			if ($total===$usados && $total!==0) {
				break;
			}
			//Se todos os filhos foram null então quebro o loop
			$total=0;
			foreach ($lista1 as $key => $value) {
				if ($value['filhos']==null) {
					$total++;
				}
			}
			if (count($lista1)==$total) {
				break;
			}
			$navega=&$lista1[0];
			$contador++;			
		}
		
		foreach ($lista1 as $key => $value) {		
			FuncoesSemantica::validaConectivo($lista1[$key],$relacoes);
		}
		
		if ($raiz['usado']==false) {
			$navega=$raiz;
			$lista1=[];
			goto retorno;
		}

		return;
	}
	public static function validaConectivo(&$pai,$relacoes){
		//Correção de átomos
		if ($pai['info']['esquerdo']!=null && $pai['info']['direito']==null) {
			$pai['info']['direito']=$pai['info']['esquerdo'];
			$pai['info']['esquerdo']=null;

		}
		switch ($pai['info']['conectivo']['operacao']) {
			case 'e':
				foreach ($pai['filhos'] as $key => $value) {
					if (!$value['valor']) {
						$pai['valor']=false;
						$pai['usado']=true;
						return;
					}
				}
				$pai['usado']=true;
				$pai['valor']=true;
				return;
			case 'ou':
				foreach ($pai['filhos'] as $key => $value) {
					if ($value['valor']) {
						$pai['valor']=true;
						$pai['usado']=true;
						return;
					}
				}
				$pai['usado']=false;
				$pai['valor']=true;
				return;
			case 'implica':
				if ($pai['filhos'][0]['valor']==true && $pai['filhos'][1]['valor']==false) {
					$pai['valor']=false;
					$pai['usado']=true;
					return;	
				}
				else{
					$pai['usado']=true;
					$pai['valor']=true;
					return;
				}
			case 'paraTodo':
				foreach ($pai['filhos'] as $key => $value) {
					if (!$value['valor']) {
						$pai['valor']=false;
						$pai['usado']=true;
						return;
					}
				}
				$pai['usado']=true;
				$pai['valor']=true;
				return;
			case 'xist':
				foreach ($pai['filhos'] as $key => $value) {
					if ($value['valor']) {
						$pai['valor']=true;
						$pai['usado']=true;
						return;
					}
				}
				$pai['usado']=true;
				$pai['valor']=false;
				return;
			case null:
				if (FuncoesSemantica::checaAtomicoSemantica($pai)) {
					if (in_array($pai['info']['direito'],$relacoes)) {
						$pai['valor']=true;
					}
					$pai['usado']=true;
				}
				else{
					print "<br>Deveria lançar uma exceção<br>";
					print_r($pai['info']);
					dd(1);
					//lançar uma exceção aqui
				}
			default:
				# code...
				break;
		}
	}
	//Método que recebe uma variável de valor qualquer e um array
	//Verifica se esta variável está contida no array
	//Retorna verdadeiro caso encontre ou falso caso não encontre
	public static function noArray($variavel,$array){
		foreach ($array as $key => $value) {
			if ($value['info']==$variavel['info']) {
				return true;
			}
		}
		return false;
	}

	public static function corrigeAtomos(&$form){
		if (strlen($form)==1) {
			$form="(".$form.")";
		}
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

	public static function converteFormulaStringSemantica(&$form){
		$contador=0;
		if (@strlen($form['info'])==1) {
			$form="(".$form.")";
		}
		while (@is_array($form['info']['esquerdo']) || @is_array($form['info']['direito']) || is_array($form)) {
			FuncoesSemantica::reverteFormatacaoSemantica($form,$contador);
		}
	}

	public static function reverteFormatacaoSemantica(&$form){
		if (@is_array($form['esquerdo'])) {
			FuncoesSemantica::reverteFormatacaoSemantica($form['esquerdo']);
		}
		elseif (@!is_array($form['esquerdo']) ) {
			FuncoesSemantica::colocaParentesesSemantica($form);
		}
		if (@is_array($form['direito'])) {
			FuncoesSemantica::reverteFormatacaoSemantica($form['direito']);
		}
		elseif (@!is_array($form['direito']) ) {
			FuncoesSemantica::colocaParentesesSemantica($form);
		}
	}

	//Função que recebe a referência para uma fórmula array com a estrutura
	//array ('esquerdo' => , 'conectivo' => , 'direito' =>) e transforma em string
	public static function colocaParentesesSemantica(&$form){
		//print_r($form);
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
			//Caso not_implica
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
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
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
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="xist".$form['info']['esquerdo'];
					//$aux=$aux."xist";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
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
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="not_paraTodo".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
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
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="not_xist".$form['info']['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['info']['direito'].")";
					$form['info']=$aux;
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['esquerdo']="not_xist".$form['esquerdo'];
					//$aux=$aux."implica";
					$aux=$aux.$form['direito'].")";
					$form=$aux;
				}
				return;
			}
			//Caso sem conectivo central
			if (@$form['info']) {
				if (is_array($form['info']['esquerdo'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
				}
				
				if (is_array($form['info']['direito'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
				}
				$aux=$form['info']['conectivo']['operacao'];
				$aux=$aux.$form['info']['direito'].")";
				$form['info']['direito']=$aux;
			}
			else{
				if (is_array($form['esquerdo'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
				}
				if (is_array($form['direito'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['direito']);
				}
				$aux=$form['conectivo']['operacao'];
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
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$aux="not(".$form['info']['esquerdo'];
					$aux=$aux."ou";
					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta($form['direito']['info'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						if (@$form['info']['direito']['info']) {
							$aux=$aux.$form['info']['direito']['info'];
						}
						else{
							$aux=$aux.$form['info']['direito'].")";
						}
					}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."ou";
					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])===true || @FuncoesAuxiliares::verificaFormulaCorreta(@$form['direito']['info'])===true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						if (@$form['info']['direito']['info']!=null && @$form['info']['direito']['info']!=true) {
							$aux=$aux.$form['direito']['info'];
						}
						else{
							$aux=$aux.$form['direito'].")";
						}
					}
					$form=$aux;
				}
				
				return;
			}
			if (@$form['conectivo']['operacao']=='not_e' || @$form['info']['conectivo']['operacao']=='not_e') {
				
				if (@$form['info']) {
					while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
					}
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$aux="not(".$form['info']['esquerdo'];
					$aux=$aux."e";
					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])===true || @FuncoesAuxiliares::verificaFormulaCorreta($form['direito']['info'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						if (@$form['info']['direito']['info']!=null && @$form['info']['direito']['info']!=true) {
							$aux=$aux.$form['info']['direito']['info'];
						}
						else{
							$aux=$aux.$form['info']['direito'].")";
						}
					}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
					}
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."e";
					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta($form['direito']['info'])==true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						if (@$form['direito']['info']) {
							$aux=$aux.$form['direito']['info'];
						}
						else{
							$aux=$aux.$form['direito'].")";
						}
					}
					$form=$aux;
				}
				return;
			}
			if (@$form['conectivo']['operacao']=='not_implica' || @$form['info']['conectivo']['operacao']=='not_implica') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['info']['direito']="not(".$form['info']['direito'];
					$aux=$aux."implica";
					if (FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta($form['direito']['info'])==true) {
						$aux=$aux.$form['info']['direito']."))";
					}
					else{
						if (@$form['info']['direito']['info']) {
							$aux=$aux.$form['info']['direito']['info'];
						}
						else{
							$aux=$aux.$form['info']['direito'].")";
						}
					}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$aux="not(".$form['esquerdo'];
					$aux=$aux."implica";
					//dd(1);
					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta(@$form['direito']['info'])==true) {
						$aux=$aux.$form['direito']."))";
					}
					else{
						if (@$form['direito']['info']!=null && @$form['direito']['info']!=true) {
							$aux=$aux.$form['direito']['info'];
						}
						else{
							$aux=$aux.$form['direito'].")";
						}
					}
					$form=$aux;
				}
				return;
			}
			//Caso notnot
			if (@$form['conectivo']['operacao']=='notnot' || @$form['info']['conectivo']['operacao']=='notnot') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}


					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['info']['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta($form['direito']['info'])==true) {
						$form['info']['direito']="notnot(".$form['info']['direito'];
						$aux=$aux.$form['info']['direito']."))";
						$form['info']=$aux;
					}
					else{
						if (@$form['direito']['info']) {
							$form['info']['direito']="notnot(".$form['info']['direito']['info'];
							$aux=$aux.$form['info']['direito'];
						}
						else{
							$form['info']['direito']="notnot(".$form['info']['direito'];
							$aux=$aux.$form['info']['direito'];
						}
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
				}
				else{
					//Antes estava assim
					//while((is_array($form['direito']) && @$form['direito']['atualCentral']) || is_array($form['direito']['info'])) {
					while((is_array($form['direito']) && @$form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}

					if (@FuncoesAuxiliares::verificaFormulaCorreta($form['direito'])==true || @FuncoesAuxiliares::verificaFormulaCorreta(@$form['direito']['info'])==true) {
						$form['direito']="notnot(".$form['direito'];
						$aux=$aux.$form['direito']."))";
						$form=$aux;
					}
					else{
						if (@!empty($form['direito']['info'])) {
							$form['direito']="notnot(".$form['direito']['info'];
							$aux=$aux.$form['direito'];
						}
						else{
							$form['direito']="notnot(".$form['direito'];
							$aux=$aux.$form['direito'];
						}
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
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="paraTodo(".$form['info']['esquerdo'];
					$aux=$form['info']['esquerdo'];
					//$aux=$aux."implica";
					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['info']['direito']['info']) {
						$aux=$aux.$form['info']['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['info']['direito'].")";
					//}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['esquerdo']="paraTodo(".$form['esquerdo'];
					$aux=$form['esquerdo'];
					//$aux=$aux."implica";
					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['direito']['info']) {
						$aux=$aux.$form['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['direito'].")";
					//}
					$form=$aux;
				}
				return;
			}
			//Caso xist
			if (@$form['conectivo']['operacao']=='xist' || @$form['info']['conectivo']['operacao']=='xist') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);	
					}
					$form['info']['esquerdo']="xist(".$form['info']['esquerdo'];
					$aux=$form['info']['esquerdo'];

					//$aux=$aux."xist";
					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['info']['direito']['info']) {
						$aux=$aux.$form['info']['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['info']['direito'].")";
					//}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['esquerdo']="xist(".$form['esquerdo'];
					$aux=$form['esquerdo'];
					//$aux=$aux."implica";
					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['direito']['info']) {
						$aux=$aux.$form['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['direito'].")";
					//}
					$form=$aux;
				}
				return;
			}
			//Caso not_paraTodo
			if (@$form['conectivo']['operacao']=='not_paraTodo' || @$form['info']['conectivo']['operacao']=='not_paraTodo') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="not_paraTodo(".$form['info']['esquerdo'];
					$aux=$form['info']['esquerdo'];
					//$aux=$aux."implica";
					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['info']['direito']['info']) {
						$aux=$aux.$form['info']['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['info']['direito'].")";
					//}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['esquerdo']="not_paraTodo(".$form['esquerdo'];
					$aux=$form['esquerdo'];
					//$aux=$aux."implica";

					//Como pode haver 'info' no lado direito, conforme o while anterior, é necessário
					//preparar 2 tipos de atribuições
					/*if (@$form['direito']['info']) {
						$aux=$aux.$form['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['direito'].")";
					//}
					$form=$aux;
				}
				return;
			}
			//Caso not_xist
			if (@$form['conectivo']['operacao']=='not_xist' || @$form['info']['conectivo']['operacao']=='not_xist') {
				if (@$form['info']) {
					while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
					}
					$form['info']['esquerdo']="not_xist(".$form['info']['esquerdo'];
					$aux=$form['info']['esquerdo'];
					//$aux=$aux."implica";
					/*if (@$form['info']['direito']['info']) {
						$aux=$aux.$form['info']['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['info']['direito'].")";
					//}
					$form['info']=$aux;
					//Limpar outros campos caso seja possível
					if (@!is_array($form['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form);
					}
				}
				else{
					while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['direito']);
					}
					$form['esquerdo']="not_xist(".$form['esquerdo'];
					$aux=$form['esquerdo'];
					//$aux=$aux."implica";
					/*if (@$form['direito']['info']) {
						$aux=$aux.$form['direito']['info'].")";
					}
					else{*/
						$aux=$aux.$form['direito'].")";
					//}
					$form=$aux;
				}
				return;
			}

			if (@$form['info']) {
				if ((FuncoesTableauxLPO::checaAtomicoLPO($form['info']['esquerdo']))) {
					//$aux='(';
				}
				elseif (FuncoesAuxiliares::verificaFormulaCorreta($aux2)==true) {
					$aux="(";
				}
				else{
					//$aux="(";
				}
				$aux=$aux.$form['info']['esquerdo'];
				$form['info']['esquerdo']=$aux;
				//Limpar outros campos caso seja possível
				if (@!is_array($form['info'])) {
					FuncoesTableauxLPO::limpaFormulaTableaux($form);
				}
			}
			else{
				$aux2=$form;
				//$aux="(";
				if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
					//$aux='(';
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
				FuncoesSemantica::colocaParentesesSemantica($form['direito']);
			}
			if ((@is_array($form['info']['direito'])) && @!is_string($form['info']['direito']['info'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);						
					if (@!is_array($form['info']['direito']['info'])) {
						FuncoesTableauxLPO::limpaFormulaTableaux($form['info']['direito']);
					}
					
			}
			//print "<br><br>DEBUG -- ENTRADA NO direito é array<br><br>";			
			return;
		}
		elseif(is_array($form) || @is_array($form['info'])){

			if (@$form['conectivo']['operacao']=='not_ou' || @$form['info']['conectivo']['operacao']=='not_ou') {
				
				if(@$form['esquerdo'][0]=="(" || @$form['info']['esquerdo'][0]=="(") {
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not(".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}

					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not(".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
								FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
							}
							while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
								FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
							}
							$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
							if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
							//Limpar outros campos caso seja possível
							if (@!is_array($form['info'])) {
								FuncoesTableauxLPO::limpaFormulaTableaux($form);
							}
						}
					}
					else{
						if ($form['esquerdo'][0]=="(") {
							while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
								FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
							}
							while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
								FuncoesSemantica::colocaParentesesSemantica($form['direito']);
							}
							$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
							if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['esquerdo'])==1 || strlen($form['info']['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['esquerdo'])==1 || strlen($form['esquerdo'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						$aux2="not".$form['info']['esquerdo']."implica".$form['info']['direito'].")";
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						$aux2="not".$form['esquerdo']."implica".$form['direito'].")";
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						$form=$aux;
					}					
				}
				//$form=$aux;
				return;
			}
			//Caso em subfórmulas com paraTodo
			if (@$form['conectivo']['operacao']=='paraTodo' || @$form['info']['conectivo']['operacao']=='paraTodo') {

				if (@$form['direito'][0]=="(" || @$form['info']['direito'][0]=="(") {
					if (@$form['info']) {
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="paraTodo";
						}
						else{
							$aux="paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="xist";
						}
						else{
							$aux="xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="not_paraTodo";
						}
						else{
							$aux="not_paraTodo(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						while((is_array($form['info']['esquerdo'])) && is_array($form['info']['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
						}
						while((is_array($form['info']['direito'])) && is_array($form['info']['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
						}
						if ((strlen($form['info']['direito'])==1 || strlen($form['info']['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form['info']))) {
							$aux="not_xist";
						}
						else{
							$aux="not_xist(";
						}
						$aux=$aux.$form['info']['esquerdo'];
						//$aux=$aux."e";
						$aux=$aux.$form['info']['direito'].")";
						$form['info']=$aux;
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
					}
					else{
						while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
						}
						while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
							FuncoesSemantica::colocaParentesesSemantica($form['direito']);
						}
						if ((strlen($form['direito'])==1 || strlen($form['direito'])==4) && (FuncoesTableauxLPO::checaAtomicoLPO($form))) {
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
						//Limpar outros campos caso seja possível
						if (@!is_array($form['info'])) {
							FuncoesTableauxLPO::limpaFormulaTableaux($form);
						}
						//dd(1);
					}
					else{
						$aux=$aux.$form['conectivo']['operacao'];
						$aux=$aux."(".$form['direito'].")";
						$form=$aux;
					}
					
					return;
				}
			}
			/*$aux=null;
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
			}*/
			//Ajustes
			$aux="(";
			if (@$form['info']) {
				while((is_array(@$form['info']['esquerdo'])) && is_array(@$form['info']['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
				}
				if (is_string($form['info'])) {
					$aux=$aux.$form['info'];
				}
				else{
					$aux=$aux.$form['info']['esquerdo'];
				}
				
				//$form['info']=$aux;
			}
			else{
				while((is_array(@$form['esquerdo'])) && is_array(@$form['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
				}
				if (is_string($form)) {
					$aux=$aux.$form;
				}
				else{
					$aux=$aux.$form['esquerdo'];
				}
			}
			if (@$form['info']) {
				while((is_array(@$form['info']['esquerdo'])) && is_array(@$form['info']['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['info']['esquerdo']);
				}
				while((is_array(@$form['info']['direito'])) && is_array(@$form['info']['direito']['info'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['info']['direito']);
				}
				if (is_string($form['info'])) {
					$aux=$aux.")";
				}
				else{
					$aux=$aux.$form['info']['conectivo']['operacao'];
					$aux=$aux.$form['info']['direito'].")";
				}
				$form['info']=$aux;
				if (@!is_array($form['info'])) {
					FuncoesTableauxLPO::limpaFormulaTableaux($form);
				}
				
			}
			else{
				while((is_array($form['esquerdo'])) && is_array($form['esquerdo']['info'])) {
						FuncoesSemantica::colocaParentesesSemantica($form['esquerdo']);
				}
				while((is_array($form['direito'])) && is_array($form['direito']['info'])) {
					FuncoesSemantica::colocaParentesesSemantica($form['direito']);
				}
				$aux=$aux.$form['conectivo']['operacao'];
				$aux=$aux.$form['direito'].")";
				$form=$aux;
			}
			return;
		}
	}


}