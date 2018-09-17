<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesSemantica;
use App\Formula;
use App\ParsingFormulas;
use App\Exercicios;


//Remover a repetição das fórmulas em cada passo
class Semantica extends Model
{
	private $exercicioEscolhido;

	public function __construct($exercicioEscolhido=NULL){
    	$this->exercicioEscolhido = $exercicioEscolhido;
    }

    public function fullSteps(){
    	$contador=0;
    	//$resposta=[];
    	$nosFolha=[];
    	$entrada=null;
    	$arvoreSaida=null;
    	$listaDeNos=[];
    	$indice=0;
    	//Passo 1 - Recebe entrada e parâmetros
    	$entrada = $this->exercicioEscolhido;
		$dominio= array ('0','1');
		$tamanho=count($entrada);		
		$entradaConvertida=FuncoesSemantica::processaEntradaSemantica($entrada);
		// print_r($entradaConvertida);

		FuncoesSemantica::adicionaArray($nosFolha, $entradaConvertida[0]);
		
		//dd(1);
		//Passo 2
		//Gera a raiz com seus primeiros filhos
		while ($nosFolha!=null) {
			FuncoesSemantica::geraArvore($entradaConvertida[0],$dominio,$nosFolha,$contador);
		}
		//print_r($entradaConvertida);
		//dd(1);
		//Passo 3
		//$relacoes = array ();
		$relacoes = array ("R(0;0)","R(1;1)");
		//$relacoes = array ("P(0)");
		FuncoesSemantica::preencheProximo($relacoes,$entradaConvertida[0]);
		//dd(1);
		FuncoesSemantica::validaFormulas($relacoes,$entradaConvertida[0]);
		//dd(1);

		// print "<br>Imprime a arvore toda<br>";
		FuncoesSemantica::imprimeArvore($entradaConvertida[0],$arvoreSaida,$listaDeNos,$indice);
		// $resposta[] = $entradaConvertida[0];
		// print "<br>Arvore Saida<br>";
		// print_r($arvoreSaida);
		// print "<br>Lista de nos<br>";
		// print_r($listaDeNos);
		$resposta[] = $arvoreSaida;
		$resposta[] = $listaDeNos;

		$respostaFinal = array($this->retornaArvore($resposta),$resposta[1]);

		//print_r($entradaConvertida[0]['filhos'][1]['filhos'][1]['proximo']);
		//$resposta[] = $entradaConvertida[0]['filhos'][1]['filhos'][1]['proximo'];
		return $respostaFinal;

    	
    }

    private function retornaArvore($resposta){
 		// echo "<pre>";

    	$arrayArvore = $resposta[0];
    	$hash = $resposta[1];
    	// dd($resposta);

		$respostaFinal = "<ul><li id=".$arrayArvore[0]['id'].">";
			//Colocar ['formula']
			$respostaFinal .= Exercicios::converteSaida($hash[$arrayArvore[0]['id']]['info']);
				$respostaFinal .= "<ul>";
					$respostaFinal .= $this->retornaNo($arrayArvore[0],$hash);
				$respostaFinal .= "</ul>";
		$respostaFinal .= "</li></ul>";
    	

    	return $respostaFinal;


    }

    private function retornaNo($arrayArvore,$hash){
    	$respostaFinal = "";
    	for($i = 1; $i < count($arrayArvore); $i++){
    		$respostaFinal .= "<li id=".$arrayArvore[$i]['id'].">";
    			//Colocar ['formula']
    			$respostaFinal .= Exercicios::converteSaida($hash[$arrayArvore[$i]['id']]['info']);
    				$respostaFinal .= "<ul>";
    					if(is_array($arrayArvore[$i]))
    						$respostaFinal .= $this->retornaNo($arrayArvore[$i],$hash);
    				$respostaFinal .= "</ul>";
    		$respostaFinal .= "</li>";
    	}
    	// dd($respostaFinal);
    	return $respostaFinal;
    }

}
