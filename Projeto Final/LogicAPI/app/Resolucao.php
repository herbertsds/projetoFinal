<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesResolucao;
use App\Formula;

class Resolucao extends Model
{
	private $exercicioEscolhido;

	public function __construct($exercicioEscolhido){
    	$this->exercicioEscolhido = $exercicioEscolhido;
    }

    public function fullSteps(){
    	//Entrada
		$entradaTeste = $this->exercicioEscolhido;
		$tamanho=count($entradaTeste);

		//Receber a entrada do Front-End

		//Negação da pergunta+Validação
		$entradaConvertida=FuncoesResolucao::negaPergunta($entradaTeste,$tamanho);

		//Constrói o retorno
// 		$resposta[] = "<br>Entrada Recebida<br>";

		//Print, pré-processa os notnot
		foreach ($entradaTeste as $key => $value) {
			if ($entradaConvertida[$key]['conectivo']=='notnot') {
				$entradaConvertida[$key]['conectivo']=NULL;
			}	
		}

		//Constrói o retorno
// 		$resposta[] = "<br>Após o processamento dos notnot: <br>";
		$resposta[] = $entradaConvertida;

		//Se houver digitação incorreta vai haver um aviso. Para o front-end adicionar uma flag (valor "1")
		//A flag vai indicar que a fórmula está incorreta e ficar pedindo a digitação correta para o front-end
		//Quando a flag voltar para o valor "0" pode passar para a próxima entrada


		//Passo 3
		foreach ($entradaConvertida as $key => $value) {
			FuncoesResolucao::converteFNC($entradaConvertida[$key]);
		}


// 		$resposta[] = "<br>Após FNC<br>";
		$resposta[] = $entradaConvertida;

		//Loop para tranfosformar em arrays as fórmulas mais internas, por exemplo
		//Nesta etapa um Av(BeC) é representado como
		//$form['esquerdo']=A  $form['conectivo']='ou' $form['direito']='BvC'
		//Após este loop, este lado direito também estará no formato de array, dentro desse array mais externo
		foreach ($entradaConvertida as $key => $value) {
			if (is_array($value['esquerdo'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['esquerdo']);
			}
			if (is_array($value['direito'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['direito']);
			}
			elseif (!(is_array($value['esquerdo'])) && !(is_array($value['direito']))) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]);
			}	
		}

// 		$resposta[] = "<br>Após a formatação<br>";
		$resposta[] = $entradaConvertida;



// 		$resposta[] = "<br>Após o tratamento dos átomos<br>";
		$resposta[] = $entradaConvertida;

		//Os próximos passos precisam ser repetidos afim de extrair os arrays mais internos de fórmulas mais complexas
		$contador=0;
		$flag=false;
		while ($contador <= 10){
			

			//Passo 4
			$aux1['esquerdo']=NULL;
			$aux1['conectivo']=NULL;
			$aux1['direito']=NULL;
			$aux2['esquerdo']=NULL;
			$aux2['conectivo']=NULL;
			$aux2['direito']=NULL;

			FuncoesResolucao::separarE($arrayFormulas,$entradaConvertida,$aux1,$aux2,$contador);
// 			$resposta[] = "<br> FÓRMULAS APÓS SEPARAÇÃO DO E<BR>";
			$resposta[] = $arrayFormulas;

			//Passo 5
			$hashResolucao=array();
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag);
			if($flag){
				goto fim;
			}
// 			$resposta[] = "HASH<BR>";
			$resposta[] = $hashResolucao;


			//Passo 6
			//
			FuncoesResolucao::separarOU1($arrayFormulas,$hashResolucao);
// 			$resposta[] = "<br>APÓS A SIMPLIFICAÇÃO DE 'OU' SIMPLES<BR>";
			$resposta[] = $arrayFormulas;
			$resposta[] = $hashResolucao;

			//Simplificação do tipo: Se Av¬B e AvB então A.
			FuncoesResolucao::separarOU2($arrayFormulas);

// 			$resposta[] = "<br>APÓS A SIMPLIFICAÇÃO DE 'OU' COMPOSTO<BR>";
			$resposta[] = $arrayFormulas;
			$resposta[] = $hashResolucao;	


			//Passo 5 - REPETIÇÃO
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag);
			if($flag){
				goto fim;
			}

			if(!FuncoesResolucao::checaExisteArray($arrayFormulas)){
// 				$resposta[] = "<br>Não existem mais array, saindo do loop<br><br>";
				break;
			}
			else{
// 				$resposta[] = "<br>Ainda existe array, próxima iteração<br><br>";
			}
			$contador++;
		}

		fim:
// 		$resposta[] = "<br>Encerra processamento<br>";

		return $resposta;
    }
}
