<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesSemantica;
use App\Formula;
use App\ParsingFormulas;
 echo "<pre>";


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
    	//Passo 1 - Recebe entrada e parâmetros
    	$entrada = $this->exercicioEscolhido;
		$dominio= array ('0','1');
		$tamanho=count($entrada);		
		$entradaConvertida=FuncoesSemantica::processaEntradaSemantica($entrada);
		print_r($entradaConvertida);

		FuncoesSemantica::adicionaArray($nosFolha, $entradaConvertida[0]);
		
		
		//Passo 2
		//Gera a raiz com seus primeiros filhos
		while ($nosFolha!=null) {
			FuncoesSemantica::geraArvore($entradaConvertida[0],$dominio,$nosFolha,$contador);
		}
		//Passo 3
		$relacoes = array ();
		//$relacoes = array ("P(0)","Q(0)","P(1)","Q(1)");
		FuncoesSemantica::preencheProximo($relacoes,$entradaConvertida[0]);
		FuncoesSemantica::validaFormulas($relacoes,$entradaConvertida[0]);

		print "<br>Imprime a arvore toda<br>";
		FuncoesSemantica::imprimeArvore($entradaConvertida[0]);
		$resposta[] = $entradaConvertida[0];
		//print_r($entradaConvertida[0]['filhos'][1]['filhos'][1]['proximo']);
		//$resposta[] = $entradaConvertida[0]['filhos'][1]['filhos'][1]['proximo'];
		return $resposta;

    	
    }
}
