<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\FuncoesResolucao;
use App\Formula;
use App\ParsingFormulas;
use App\Exercicios;
//echo "<pre>";


//Remover a repetição das fórmulas em cada passo
class Resolucao extends Model
{
	private $exercicioEscolhido;

	public function __construct($exercicioEscolhido=NULL){
    	$this->exercicioEscolhido = $exercicioEscolhido;
    }

    public function fullSteps(){
    	//Entrada
		$entradaTeste = $this->exercicioEscolhido;
		$tamanho=count($entradaTeste);

		$mudancaArray;
		$mudancaHash=[];

		$formAntesDoE=[];
		$formAntesDoOu1=[];
		$formAntesDoOu2=[];
		$formsDepoisDoE=[];
		$formsDepoisDoOu=[];
		$perguntaAntesNegar=null;
		$perguntaDepoisNegar=null;
		$statusFechado='Não fechado';

		//Receber a entrada do Front-End

		//Negação da pergunta+Validação

		$entradaConvertida=FuncoesResolucao::negaPergunta($entradaTeste,$tamanho,$perguntaAntesNegar,$perguntaDepoisNegar);

		$resposta[] = "Negação da pergunta";
		$resposta[] = $perguntaDepoisNegar;
		$resposta[] = $perguntaAntesNegar;
		

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
					//Regra
					$resposta[] = "Remove os notnot";
					//Fórmula nova
					$resposta[] = $entradaConvertida[$key];
					//Fórmula antiga
					$resposta[] = $mudancaArray[$key];
					

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
					//Regra
					$resposta[] = "Fórmula em FNC";
					//Fórmula nova
					$resposta[] = $entradaConvertida[$key];
					//Fórmula antiga
					$resposta[] = $mudancaArray[$key];
					

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
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']);
			}
			if (is_array($value['direito'])) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']);
			}
			elseif (!(is_array($value['esquerdo'])) && !(is_array($value['direito']))) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]);
			}	
		}

		foreach ($entradaConvertida as $key => $value) {
			ParsingFormulas::formataFormulas($entradaConvertida[$key]);
			if (@is_array($entradaConvertida[$key]['esquerdo']['esquerdo'])) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']['esquerdo']);
			}
			if (@is_array($entradaConvertida[$key]['esquerdo']['direito'])) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']['direito']);
			}
			if (@is_array($entradaConvertida[$key]['direito']['esquerdo'])) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']['esquerdo']);
			}
			if (@is_array($entradaConvertida[$key]['direito']['direito'])) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']['direito']);
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
			ParsingFormulas::corrigeArrays($entradaConvertida[$key]);
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
					ParsingFormulas::corrigeArrays($entradaConvertida[$key]);
					ParsingFormulas::corrigeAtomos($entradaConvertida[$key]);
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

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}

			if ($arrayFormulas!=$mudancaArray) {
				$key2=0;
				foreach ($formAntesDoE as $key => $value) {
					//Regra
					$resposta[] = "Separação do E";
					//Fórmula nova
					$resposta[] = $formsDepoisDoE[$key2];
					$resposta[] = $formsDepoisDoE[$key2+1];
					//Fórmula antiga
					$resposta[] = $formAntesDoE[$key];
					
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
			
			//print_r($arrayFormulas);

			//Passo 6
			//
			FuncoesResolucao::separarOU1($arrayFormulas,$hashResolucao,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);
			//dd($arrayFormulas);

			if ($arrayFormulas!=$mudancaArray) {
				foreach ($formsDepoisDoOu as $key => $value) {
					//Regra
					$resposta[] = "Separação do Ou";
					//Fórmula nova
					$resposta[] = $formsDepoisDoOu[$key];
					//Fórmula antiga 1
					$resposta[] = $formAntesDoOu1[$key];
					//Fórmula antiga 2
					$resposta[] = $formAntesDoOu2[$key];
				}

				$mudancaArray=$arrayFormulas;
				$formAntesDoOu1=[];
				$formAntesDoOu2=[];
				$formsDepoisDoOu=[];
			}
			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]);
		 	}
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			if($flag){
				goto fim;
			}


			

			//Simplificação do tipo: Se Av¬B e AvB então A.
			FuncoesResolucao::separarOU2($arrayFormulas,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);
			if ($arrayFormulas!=$mudancaArray) {
				foreach ($formAntesDoOu1 as $key => $value) {
					//Regra
					$resposta[] = "Separação do Ou";
					//Fórmula nova
					$resposta[] = $formsDepoisDoOu[$key];
					//Fórmula antiga 1
					$resposta[] = $formAntesDoOu1[$key];
					//Fórmula antiga 2
					$resposta[] = $formAntesDoOu2[$key];
				}

				$mudancaArray=$arrayFormulas;
				$formAntesDoOu1=[];
				$formAntesDoOu2=[];
				$formsDepoisDoOu=[];
			}

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}
		 	//Atualização de mudancaArray para evitar erros
		 	if ($entradaConvertida!=$mudancaArray) {
				$mudancaArray=$entradaConvertida;
			}	

			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			if($flag){
				goto fim;
			}

			FuncoesResolucao::separarOU3($arrayFormulas,$hashResolucao,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);
			if ($arrayFormulas!=$mudancaArray) {
				foreach ($formAntesDoOu1 as $key => $value) {
					//Regra
					$resposta[] = "Separação do Ou";
					//Fórmula nova
					$resposta[] = $formsDepoisDoOu[$key];
					//Fórmula antiga 1
					$resposta[] = $formAntesDoOu1[$key];
					//Fórmula antiga 2
					$resposta[] = $formAntesDoOu2[$key];					
				}

				$mudancaArray=$arrayFormulas;
				$formAntesDoOu1=[];
				$formAntesDoOu2=[];
				$formsDepoisDoOu=[];
			}

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}
		 	


			//Passo 5 - REPETIÇÃO
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			if($flag){
				goto fim;
			}

			//Aplica FNC novamente para passar not para dentro
			foreach ($arrayFormulas as $key => $value) {
			FuncoesResolucao::resolveNOT($arrayFormulas[$key]);
			}
			if ($arrayFormulas!=$mudancaArray) {
				foreach ($arrayFormulas as $key => $value) {
					if ($arrayFormulas[$key]!=$mudancaArray[$key]) {
						//Regra
						$resposta[] = "Passando o not para dentro";
						//Fórmula nova
						$resposta[] = $arrayFormulas[$key];
						//Fórmula antiga
						$resposta[] = $mudancaArray[$key];
						

					}
				}
				//$resposta[] = "<br>Após FNC<br>";
				//$resposta[] = $entradaConvertida;
				$mudancaArray=$arrayFormulas;
			}

			//Atualização de mudancaArray para evitar erros
		 	if ($entradaConvertida!=$mudancaArray) {
				$mudancaArray=$entradaConvertida;
			}	

			//Passo 5 - REPETIÇÃO
			if(!FuncoesResolucao::checaExisteArray($arrayFormulas)){
 				$resposta[] = "<br>Não existem mais array, saindo do loop<br><br>";
				break;
			}
			else{
				$resposta[] = "<br>Ainda existe array, próxima iteração<br><br>";
			}
			$contador++;
		}

		fim:
 		$resposta[] = $statusFechado;

 	
 		foreach ($resposta as $key => $value) {
 			//print "<br>";
 			//print_r($resposta[$key]);
 		}

 		foreach ($resposta as $key => $value) {
 			if (is_array($value)) {
 				ParsingFormulas::converteFormulaString($resposta[$key]);
 				$resposta[$key] = Exercicios::converteSaida($resposta[$key]);

 			}
 		}
		return $resposta;
    }
    public function stepByStep($request){
    	//[ "operação", "qtd_formulasSelecionadas",  "formula1", "formula2", .... , "formulaN" ]
    	//Entrada
    	/*$request=null;
    	$request["qtd_formulasSelecionadas"]=2;
    	$request["operacao"]="notnot";
		$request["formulas"]= [ "notnot(((A))"];*/
		
		$mudancaArray;
		$mudancaHash=[];


		//return $request;

		$formAntesDoE=[];
		$formAntesDoOu1=[];
		$formAntesDoOu2=[];
		$formsDepoisDoE=[];
		$formsDepoisDoOu=[];
		$perguntaAntesNegar=null;
		$perguntaDepoisNegar=null;
		$statusFechado='Não fechado';

		//dd($request);



		//Receber a entrada do Front-End
        
		//Negação da pergunta+Validação
		//Recebo a lista com todas as fórmulas e nego a pergunta, além de processar os notnot
    	 
		
		if ($request['operacao']=='negPergunta') {		
			$entradaConvertida=FuncoesResolucao::negaPergunta($request['formulas'],$request['qtd_formulasSelecionadas'],$perguntaAntesNegar,$perguntaDepoisNegar);
			$resposta = $entradaConvertida;

			//Constrói o retorno
			$mudancaArray=$entradaConvertida;


			//Print, pré-processa os notnot
			foreach ($entradaConvertida as $key => $value) {
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
			
			if ($entradaConvertida!=$mudancaArray) {
				foreach ($entradaConvertida as $key => $value) {
					if ($entradaConvertida[$key]!=$mudancaArray[$key]) {
						//Regra
						//$resposta[] = "Remove os notnot";
						//Fórmula nova
						$resposta[$key] = $entradaConvertida[$key];
						//Fórmula antiga
						//$resposta = $mudancaArray[$key];
					}
				}
				$mudancaArray=$entradaConvertida;
			}
			//print_r($resposta);
			foreach ($resposta as $key => $value) {
				if (is_array($value)) {
		 			ParsingFormulas::converteFormulaString($resposta[$key]);
		 		} 			
 			} 			
 			return $resposta;
		}		
		
		//Se houver digitação incorreta vai haver um aviso. Para o front-end adicionar uma flag (valor "1")
		//A flag vai indicar que a fórmula está incorreta e ficar pedindo a digitação correta para o front-end
		//Quando a flag voltar para o valor "0" pode passar para a próxima entrada

		//print_r($request);
		//Passo 3
		if ($request['operacao']=='FNC') {
			//Recebo as fórmulas em string do front-end e as converto
			//print_r($request['formulas']);
			$entradaConvertida=$request['formulas'];
			foreach ($entradaConvertida as $key => $value) {
				$entradaConvertida[$key]=Exercicios::converteSimbolosEntrada($value);
			}
			
			ParsingFormulas::ConverteFormulasEmArray($entradaConvertida);
			//print_r($entradaConvertida);
			//Aplico a operação do FNC
			foreach ($entradaConvertida as $key => $value) {
				FuncoesResolucao::converteFNC($entradaConvertida[$key]);
			}
			/*
			if ($entradaConvertida!=$mudancaArray) {
				foreach ($entradaConvertida as $key => $value) {
					if ($entradaConvertida[$key]!=$mudancaArray[$key]) {
						//Regra
						$resposta[] = "Fórmula em FNC";
						//Fórmula nova
						$resposta[] = $entradaConvertida[$key];
						//Fórmula antiga
						$resposta[] = $mudancaArray[$key];
						

					}
				}
				//$resposta[] = "<br>Após FNC<br>";
				//$resposta[] = $entradaConvertida;
				$mudancaArray=$entradaConvertida;
			}*/
			//Loop para tranfosformar em arrays as fórmulas mais internas, por exemplo
			//Nesta etapa um Av(BeC) é representado como
			//$form['esquerdo']=A  $form['conectivo']='ou' $form['direito']='BvC'
			//Após este loop, este lado direito também estará no formato de array, dentro desse array mais externo
			foreach ($entradaConvertida as $key => $value) {
				if (is_array($value['esquerdo'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']);
				}
				if (is_array($value['direito'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']);
				}
				elseif (!(is_array($value['esquerdo'])) && !(is_array($value['direito']))) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]);
				}	
			}

			foreach ($entradaConvertida as $key => $value) {
				ParsingFormulas::formataFormulas($entradaConvertida[$key]);
				if (@is_array($entradaConvertida[$key]['esquerdo']['esquerdo'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']['esquerdo']);
				}
				if (@is_array($entradaConvertida[$key]['esquerdo']['direito'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['esquerdo']['direito']);
				}
				if (@is_array($entradaConvertida[$key]['direito']['esquerdo'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']['esquerdo']);
				}
				if (@is_array($entradaConvertida[$key]['direito']['direito'])) {
					ParsingFormulas::formataFormulas($entradaConvertida[$key]['direito']['direito']);
				}
			}

			//Formatação de array para controle interno
			//Não é necessário mostrar
			/*
			if ($entradaConvertida!=$mudancaArray) {
				//$resposta[] = "<br>Após a formatação<br>";
				//$resposta[] = $entradaConvertida;
				$mudancaArray=$entradaConvertida;
			}*/

			


			foreach ($entradaConvertida as $key => $value) {
				ParsingFormulas::corrigeArrays($entradaConvertida[$key]);
			}
			/*
			//Correção de átomos para controle interno
			if ($entradaConvertida!=$mudancaArray) {
				//$resposta[] = "<br>Após o tratamento dos átomos<br>";
				//$resposta[] = $entradaConvertida;
				$mudancaArray=$entradaConvertida;
			}*/
			$resposta=$entradaConvertida;
			//Converte de volta para strings e retorna
			foreach ($resposta as $key => $value) {
	 			if (is_array($value)) {
	 				ParsingFormulas::converteFormulaString($resposta[$key]);
	 			}
	 		}
	 		//print "<br>Após a aplicação de FNC<br>";
	 		//print_r($resposta);
	 		//dd(1);
			return $resposta;			
		}
		

		//Passo 4
		if ($request['operacao']=='SeparaE'){
			$flag=false;
			$hashResolucao=[];
			$contador=0;
			$arrayFormulas=[];
			//Recebo as fórmulas em string do front-end e as converto
			$entradaConvertida=$request['formulas'];
			ParsingFormulas::ConverteFormulasEmArray($entradaConvertida);

			

			//Correções nas fórmulas
			foreach ($entradaConvertida as $key => $value) {
				ParsingFormulas::corrigeArrays($entradaConvertida[$key]);
				ParsingFormulas::corrigeAtomos($entradaConvertida[$key]);
			}

			//Inicializa a hash
			$hashResolucao=FuncoesResolucao::inicializaHash($entradaConvertida);
			/*
			//Correção de átomos para controle interno
			if ($entradaConvertida!=$mudancaArray) {
				$mudancaArray=$entradaConvertida;
			}*/
			$aux1['esquerdo']=NULL;
			$aux1['conectivo']=NULL;
			$aux1['direito']=NULL;
			$aux2['esquerdo']=NULL;
			$aux2['conectivo']=NULL;
			$aux2['direito']=NULL;

			FuncoesResolucao::separarE($arrayFormulas,$entradaConvertida,$aux1,$aux2,$contador,$formAntesDoE,$formsDepoisDoE);

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}

			
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			

			//Converte de volta para strings e retorna
			foreach ($arrayFormulas as $key => $value) {
	 			if (is_array($value)) {
	 				ParsingFormulas::converteFormulaString($arrayFormulas[$key]);
	 			}
	 		}
	 		$resposta=$arrayFormulas;
			return array($resposta,$statusFechado);
		}

		if ($request['operacao']=='SeparaOU'){
			$flag=false;
			$hashResolucao=[];
			$arrayFormulas=[];
			$retorno=[];
			//Recebo as fórmulas em string do front-end e as converto
			$arrayFormulas=$request['formulas'];

			ParsingFormulas::ConverteFormulasEmArray($arrayFormulas);



			//Correções nas fórmulas
			foreach ($arrayFormulas as $key => $value) {
				ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]);
			}
			//print_r($arrayFormulas);
			//dd(1);
			//Inicializa a hash
			$hashResolucao=FuncoesResolucao::inicializaHash($arrayFormulas);


			$mudancaArray=$arrayFormulas;
			FuncoesResolucao::separarOU1($arrayFormulas,$hashResolucao,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);
			

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]);
		 	}

			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			/*if($flag){
				goto fim;
			}*/
			if (($arrayFormulas!=$mudancaArray) || $flag) {
				//array_push($retorno, $formsDepoisDoOu);
				$resposta=$formsDepoisDoOu;
				goto fim;
			}			

			//Simplificação do tipo: Se Av¬B e AvB então A.
			FuncoesResolucao::separarOU2($arrayFormulas,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);


			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}

			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			/*if($flag){
				goto fim;
			}*/
			if (($arrayFormulas!=$mudancaArray) || $flag) {
				//array_push($retorno, $formsDepoisDoOu);
				$resposta=$formsDepoisDoOu;
				goto fim;
			}

			FuncoesResolucao::separarOU3($arrayFormulas,$hashResolucao,$formAntesDoOu1, $formAntesDoOu2, $formsDepoisDoOu);
			print_r($arrayFormulas);
			//print_r($formsDepoisDoOu);
			dd(1);

			foreach ($arrayFormulas as $key => $value) {
		 		ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['esquerdo']);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]['direito']);
		 	}



			//Passo 5 - REPETIÇÃO
			FuncoesResolucao::confrontaAtomos($arrayFormulas,$hashResolucao,$flag,$statusFechado);
			/*if($flag){
				goto fim;
			}*/
			if (($arrayFormulas!=$mudancaArray) || $flag) {
				//array_push($retorno, $formsDepoisDoOu);
				$resposta=$formsDepoisDoOu;
				goto fim;
			}
			else{
				$resposta=null;
			}
			fim:
			//return $arrayFormulas;
 			//return $resposta;
			//Converte de volta para strings e retorna
			//return $resposta;
			//if($resposta)
			foreach ($resposta as $key => $value) {
	 			if (is_array($value) || @strlen($value)==1) {
	 				ParsingFormulas::converteFormulaString($resposta[$key]);
	 			}
	 		}
	 	}
	 	//print_r($resposta);
	 	//dd(1);
	 	if ($request['operacao']=="PassarNotParaDentro") {
			$arrayFormulas=[];
			//Recebo as fórmulas em string do front-end e as converto
			$arrayFormulas=$request['formulas'];
			ParsingFormulas::ConverteFormulasEmArray($arrayFormulas);

			//Inicializa a hash
			$hashResolucao=FuncoesResolucao::inicializaHash($arrayFormulas);

			//Correções nas fórmulas
			foreach ($arrayFormulas as $key => $value) {
				ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]);
			}
			$mudancaArray=$arrayFormulas;

			//Aplica FNC novamente para passar not para dentro
			foreach ($arrayFormulas as $key => $value) {
			FuncoesResolucao::resolveNOT($arrayFormulas[$key]);
			}
			if (($arrayFormulas!=$mudancaArray)) {
				//array_push($retorno, $formsDepoisDoOu);
				$resposta=$mudancaArray;
				goto fim2;
			}
			else{
				$resposta=NULL;
				return array($resposta,$statusFechado);
			}
			fim2:
			foreach ($resposta as $key => $value) {
	 			if (is_array($value)) {
	 				ParsingFormulas::converteFormulaString($resposta[$key]);
	 			}
	 			//ParsingFormulas::consertaStringFormula($resposta[$key]);
	 		}


			return array($resposta,$statusFechado);
		}

		if ($request['operacao']=="notnot") {
			$arrayFormulas=[];
			//Recebo as fórmulas em string do front-end e as converto
			$arrayFormulas=$request['formulas'];
			ParsingFormulas::ConverteFormulasEmArray($arrayFormulas);			

			//Correções nas fórmulas
			foreach ($arrayFormulas as $key => $value) {
				ParsingFormulas::corrigeArrays($arrayFormulas[$key]);
				ParsingFormulas::corrigeAtomos($arrayFormulas[$key]);
			}
			//Inicializa a hash
			$hashResolucao=FuncoesResolucao::inicializaHash($arrayFormulas);


			//Aplica FNC novamente para passar not para dentro
			$mudancaArray=$arrayFormulas;
			foreach ($arrayFormulas as $key => $value) {
				FuncoesResolucao::resolveNOTNOT($arrayFormulas[$key]);			
			}
			if (($arrayFormulas!=$mudancaArray)) {
				//array_push($retorno, $formsDepoisDoOu);
				$resposta=$arrayFormulas;
				goto fim3;
			}
			else{
				$resposta=NULL;
				return array($resposta,$statusFechado);
			}
			fim3:
			foreach ($resposta as $key => $value) {
	 			if (is_array($value)) {
	 				ParsingFormulas::converteFormulaString($resposta[$key]);
	 			}
	 			//ParsingFormulas::consertaStringFormula($resposta[$key]);
	 		}			
		}
		return array($resposta,$statusFechado);
    }
}
