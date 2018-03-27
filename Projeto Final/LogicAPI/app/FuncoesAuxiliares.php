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
}
