<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesResolucao;
use App\Formula;


//Remover a repetição das fórmulas em cada passo
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

		$mudancaArray;
		$mudancaHash=[];

		$formAntesDoE=[];
		$formAntesDoOu=[];
		$formsDepoisDoE=[];
		$formsDepoisDoOu=[];
		$statusFechado='Não fechado';

		//Receber a entrada do Front-End

		//Negação da pergunta+Validação
		$entradaConvertida=FuncoesResolucao::negaPergunta($entradaTeste,$tamanho);

		//Constrói o retorno

		//$resposta[] = "<br>Entrada Recebida<br>";
		//$resposta[] = $entradaConvertida;
		$mudancaArray=$entradaConvertida;


		//Print, pré-processa os notnot
		foreach ($entradaTeste as $key => $value) {
			if ($entradaConvertida[$key]['conectivo']=='notnot') {
				$entradaConvertida[$key]['conectivo']=NULL;
			}
			if (!is_array($entradaConvertida[$key]['direito']) && $entradaConvertida[$key]['direito']!=NULL && strlen($entradaConvertida[$key]['direito'])>4 ) {
				if ($entradaConvertida[$key]['direito'][0]!='(' && ($entradaConvertida[$key]['direito'][0]!='!' && $entradaConvertida[$key]['direito'][1]!='(' )) {
					$entradaConvertida[$key]['direito']="(".$entradaConvertida[$key]['direito'];
					$entradaConvertida[$key]['direito']=$entradaConvertida[$key]['direito'].")";
				}
			}

			if (!is_array($entradaConvertida[$key]['esquerdo']) && $entradaConvertida[$key]['esquerdo']!=NULL && strlen($entradaConvertida[$key]['esquerdo'])>4 ) {
				if ($entradaConvertida[$key]['esquerdo'][0]!='(' && ($entradaConvertida[$key]['esquerdo'][0]!='!' && $entradaConvertida[$key]['esquerdo'][1]!='(' )) {
					$entradaConvertida[$key]['esquerdo']="(".$entradaConvertida['esquerdo'];
					$entradaConvertida[$key]['esquerdo']=$entradaConvertida[$key]['esquerdo'].")";
				}
			}	
		}

		//Constrói o retorno
		
		if ($entradaConvertida!=$mudancaArray) {
			foreach ($entradaConvertida as $key => $value) {
				if ($entradaConvertida[$key]!=$mudancaArray[$key]) {
					//Fórmula nova
					$resposta[] = $entradaConvertida[$key];
					//Fórmula antiga
					$resposta[] = $mudancaArray[$key];
					//Regra
					$resposta[] = "Remove os notnot";

				}
			}
			//$resposta[] = "<br>Após o processamento dos notnot: <br>";
			//$resposta[] = $entradaConvertida;
			$mudancaArray=$entradaConvertida;
		}


		

		//Se houver digitação incorreta vai haver um aviso. Para o front-end adicionar uma flag (valor "1")
		//A flag vai indicar que a fórmula está incorreta e ficar pedindo a digitação correta para o front-end
		//Quando a flag voltar para o valor "0" pode passar para a próxima entrada


		//Passo 3
		foreach ($entradaConvertida as $key => $value) {
			FuncoesResolucao::converteFNC($entradaConvertida[$key]);
		}
		if ($entradaConvertida!=$mudancaArray) {
			foreach ($entradaConvertida as $key => $value) {
				if ($entradaConvertida[$key]!=$mudancaArray[$key]) {
					//Fórmula nova
					$resposta[] = $entradaConvertida[$key];
					//Fórmula antiga
					$resposta[] = $mudancaArray[$key];
					//Regra
					$resposta[] = "Fórmula em FNC";

				}
			}
			//$resposta[] = "<br>Após FNC<br>";
			//$resposta[] = $entradaConvertida;
			$mudancaArray=$entradaConvertida;
		}

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

		foreach ($entradaConvertida as $key => $value) {
			FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]);
			if (@is_array($entradaConvertida[$key]['esquerdo']['esquerdo'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['esquerdo']['esquerdo']);
			}
			if (@is_array($entradaConvertida[$key]['esquerdo']['direito'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['esquerdo']['direito']);
			}
			if (@is_array($entradaConvertida[$key]['direito']['esquerdo'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['direito']['esquerdo']);
			}
			if (@is_array($entradaConvertida[$key]['direito']['direito'])) {
				FuncoesAuxiliares::formataFormulas($entradaConvertida[$key]['direito']['direito']);
			}
		}

		//Formatação de array para controle interno
		//Não é necessário mostrar
		if ($entradaConvertida!=$mudancaArray) {
			//$resposta[] = "<br>Após a formatação<br>";
			//$resposta[] = $entradaConvertida;
			$mudancaArray=$entradaConvertida;
		}

		


		foreach ($entradaConvertida as $key => $value) {
			FuncoesResolucao::corrigeArrays($entradaConvertida[$key]);
		}

		//Correção de átomos para controle interno
		if ($entradaConvertida!=$mudancaArray) {
			//$resposta[] = "<br>Após o tratamento dos átomos<br>";
			//$resposta[] = $entradaConvertida;
			$mudancaArray=$entradaConvertida;
		}

		

		//Os próximos passos precisam ser repetidos afim de extrair os arrays mais internos de fórmulas mais complexas
		$contador=0;
		$flag=false;
		while ($contador <= 10){
			if ($contador==0) {
				foreach ($entradaConvertida as $key => $value) {
					FuncoesResolucao::corrigeArrays($entradaConvertida[$key]);
					FuncoesResolucao::corrigeAtomos($entradaConvertida[$key]);
				}
				//Correção de átomos para controle interno
				if ($entradaConvertida!=$mudancaArray) {
					$mudancaArray=$entradaConvertida;
				}
			}

			//Passo 4
			$aux1['esquerdo']=NULL;
			$aux1['conectivo']=NULL;
			$aux1['direito']=NULL;
			$aux2['esquerdo']=NULL;
			$aux2['conectivo']=NULL;
			$aux2['direito']=NULL;

			FuncoesResolucao::separarE($arrayFormulas,$entradaConvertida,$aux1,$aux2,$contador,$formAntesDoE,$formsDepoisDoE);

			if ($arrayFormulas!=$mudancaArray) {
				$key2=0;
				foreach ($formAntesDoE as $key => $value) {
						//Fórmula nova
						$resposta[] = $formsDepoisDoE[$key2];
						$resposta[] = $formsDepoisDoE[$key2+1];
						//Fórmula antiga
						$resposta[] = $formAntesDoE[$key];

						//Regra
						$resposta[] = "Separação do E";
						$key2+=2;
				}
			}
				//$resposta[] = "<br> FÓRMULAS APÓS SEPARAÇÃO DO E<BR>";
				//$resposta[] = $arrayFormulas;
				$mudancaArray=$arrayFormulas;
				$formAntesDoE=[];
				$formsDepoisDoE=[];
			

			//Passo 5
			$hashResolucao=array();
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			if($flag){
				goto fim;
			}

			/*
			if ($hashResolucao!=$mudancaHash) {
				$resposta[] = "HASH<BR>";
				$resposta[] = $hashResolucao;
			}*/
			


			//Passo 6
			//
			FuncoesResolucao::separarOU1($arrayFormulas,$hashResolucao,$formAntesDoOu,$formsDepoisDoOu);

			if ($arrayFormulas!=$mudancaArray) {
				foreach ($formAntesDoOu as $key => $value) {
						//Fórmula nova
						$resposta[] = $formsDepoisDoOu[$key];
						//Fórmula antiga
						$resposta[] = $formAntesDoOu[$key];


						//Regra
						$resposta[] = "Separação do Ou";
				}

				$mudancaArray=$arrayFormulas;
				$formAntesDoE=[];
				$formsDepoisDoE=[];
			}

			

			//Simplificação do tipo: Se Av¬B e AvB então A.
			FuncoesResolucao::separarOU2($arrayFormulas,$formAntesDoOu,$formsDepoisDoOu);
			if ($arrayFormulas!=$mudancaArray) {
				foreach ($formAntesDoOu as $key => $value) {
						//Fórmula nova
						$resposta[] = $formsDepoisDoOu[$key];
						//Fórmula antiga
						$resposta[] = $formAntesDoOu[$key];

						//Regra
						$resposta[] = "Separação do Ou";
				}

				$mudancaArray=$arrayFormulas;
				$formAntesDoE=[];
				$formsDepoisDoE=[];
			}

			foreach ($arrayFormulas as $key => $value) {
		 		FuncoesResolucao::corrigeArrays($arrayFormulas[$key]);
				FuncoesResolucao::corrigeAtomos($arrayFormulas[$key]);
		 	}
		 	//Atualização de mudancaArray para evitar erros
		 	if ($entradaConvertida!=$mudancaArray) {
					$mudancaArray=$entradaConvertida;
			}	


			//Passo 5 - REPETIÇÃO
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
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
 		$resposta[] = $statusFechado;

		return $resposta;
    }
}
