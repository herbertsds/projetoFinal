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
		$aux['info']=array('esquerdo' => null, 'conectivo' => array('operacao' => null, 'variavel'=> null), 'direito' =>null);
		$aux['filhos']=[];
		$aux['pai']=NULL;
		$aux['valor']=false;
		$aux['usado']=false;
		$aux['proximo']=NULL;
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

	public static function imprimeArvore(&$raiz){
		
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
		//$raiz['proximo']=null;
		print_r($raiz['info']);
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
			//$raiz['filhos'][$key]['proximo']=null;
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
		retorno:
		if ($lista1[0]['filhos']!=null) {
			print "<br>Filhos no próximo nível<br>";
		}
		foreach ($lista1 as $key => $value) {
			foreach ($lista1[$key]['filhos'] as $key2 => $value2) {
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
				//$lista1[$key]['filhos'][$key2]['proximo']=null;
				print_r($value2['info']);
				//print "<br>Próximo: <br>";
				//print_r($value['proximo']['info']);
			}
			//Se os filhos deste nó acabarem
			if ($key==(count($lista1)-1)) {
				//Verificar se há irmão, se houver, ele deve ser o novo aux
				//para imprimir todos os seus filhos que são do mesmo nível
				if (@$lista2!=null) {
					$lista1=$lista2;
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

		retorno:
		while (1) {
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
				$navega=&$navega['proximo'];
			}
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
			if ($total==$usados && $total!=0) {
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
				$pai['usado']=false;
				$pai['valor']=true;
				return;
			case null:
				if (FuncoesSemantica::checaAtomicoSemantica($pai)) {
					if (in_array($pai['info']['direito'],$relacoes)) {
						$pai['valor']=true;
					}
					$pai['usado']=true;
				}
				else{
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


}