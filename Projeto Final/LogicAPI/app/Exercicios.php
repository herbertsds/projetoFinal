<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Formula;
use App\FuncoesAuxiliares;

class Exercicios extends Model
{
    protected $fillable = ['sentença','listas'];

    public function categorias(){
    	return $this->belongsToMany('App\Categorias');
    }

    public function listas(){
    	return $this->belongsToMany('App\Listas', 'categorias_exercicios');
    }

    //Buscar dados com condição no relacionamento many-to-many
	public static function where_related($classe,$coluna,$valor){
		$novaClasse = "App\\".$classe;
		return $novaClasse::condicao($coluna,$valor)->exercicios;
	}
	
    public static function contar($tipo){
    	return Exercicios::where_related('Categorias','tipo',$tipo)->count();
    }

    public static function getExercicio($numeroExercicio=NULL){

    	if($numeroExercicio!=NULL){
    		if(is_numeric($numeroExercicio))
    			$exercicioLista = Exercicios::find($numeroExercicio);
    		else if($numeroExercicio->exercicio != NULL)
            	$exercicioLista = Exercicios::find($numeroExercicio->exercicio);
	        else
	            $exercicioLista = Exercicios::find(rand(1,Exercicios::contar('tableaux')));
    	}
        if(is_object($exercicioLista))
        	$exercicio = explode(',',$exercicioLista->sentenca);
        else{
        	
        	abort(404,"Exercício não encontrado");
        }

        return $exercicio;
    }

    public static function converteEntrada($request){
    	$request = $request->all();
    	$request['formulas'] = Exercicios::converteSimbolosEntrada($request['formulas']);
    	return $request;
    	
    }

    public static function converteSaida($string){
    	
    	//Verifica todas as strings dentro de todos os arrays recursivamente
    	if(is_array($string)){

	    	$stringPivot = $string;

	    	foreach ($stringPivot as $key => $value) {
	    		if(is_array($string[$key])){
		    		$string[$key] = Exercicios::converteSaida($string[$key]);
		    	}
	    	}

    	}
    	
		$string = str_replace("ou","∨",$string);
    	$string = str_replace("e","∧",$string);
    	$string = str_replace("implica","→",$string);
    	$string = str_replace("-","→",$string);
    	$string = str_replace("not","¬",$string);
    	$string = str_replace("!","¬",$string);
    	$string = str_replace("paraTodo", "∀",$string);
    	$string = str_replace("xist", "∃",$string);

    	$string = str_replace("f∧chado","fechado",$string);

    	$string = str_replace("F∧chado","Fechado",$string);
    	
    	

    	return $string;
    }
    public static function converteSimbolosEntrada($string){
        $string = str_replace("∨","ou",$string);
        $string = str_replace("∧","e",$string);
        $string = str_replace("→","implica",$string);
        $string = str_replace("→","-",$string);
        $string = str_replace("¬","not",$string);
        $string = str_replace("¬","!",$string);
        $string = str_replace("∀","paraTodo", $string);
        $string = str_replace("∃","xist", $string);
        
        return $string;
    }
    


    /*private function setListaExercicios(){
    	//Aqui será um "banco de dados" com todos os exercícios de todas as listas
		//Como muitos métodos utilizam os mesmos exercícios, fica fácil chamá-los a partir daqui

		//O formato será um array padronizado, qualquer método pode chamar a questão diretamente como entrada

		//-------------------------------------------------ENTRADAS--------------------------------------------------------------------
		//
		//A entrada inicial vai ser um array de formulas do tipo STRING, a sequência deve ser
		//[banco_de_dados,pergunta], permitindo que eu saiba que a pergunta é a última facilita
		//na hora de inicializar o processamento do tableaux

		//Cada fórmula deve seguir o formato (Átomo conectivo Átomo), exemplos
		//(Aimplica(BouC)), not((AeB)ou(CeD)),  (AouB), ((AeB)implica(not(CouD))

		//------------------------------------------------------------------------------------------------------------------------------

		//Lista de DN sem Negação
		$this->listaExercicios['resolucao'][] = array ("(AouB)","(Bimplica(notnot(C)))","((AimplicaC)implicaC)");
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(Aimplica(BeC))");
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
		$this->listaExercicios['resolucao'][] = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))");
		$this->listaExercicios['resolucao'][] = array ("((PeQ)implicaR)","(Pimplica(QimplicaR))");
		$this->listaExercicios['resolucao'][] = array ("(Pimplica(QimplicaR))","((PeQ)implicaR)");
		$this->listaExercicios['resolucao'][] = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)implicaC)","(Cimplica(DeE))","(A)","(EouF)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");

		//Lista de DN com Negação
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
		$this->listaExercicios['resolucao'][] = array ("(Pimplica(QeR))","((PeQ)implicaR)");
		$this->listaExercicios['resolucao'][] = array ("(PimplicaQ)","not(Q)","not(P)");
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
		$this->listaExercicios['resolucao'][] = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))"); //testar depois
		$this->listaExercicios['resolucao'][] = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");
		$this->listaExercicios['resolucao'][] = array ("(AimplicaB)","(BimplicaC)","(C)","(Aimplica(BeC))");
		$this->listaExercicios['resolucao'][] = array ("(PouQ)","(PimplicaR)","(QimplicaR)","(R)");
		$this->listaExercicios['resolucao'][] = array ("(Aimplica(BeC))","((DeF)implicaA)","(DeF)","(BeF)");
		$this->listaExercicios['resolucao'][] = array ("(AouB)","(Cimplica(not(A)))","(CeD)","(BouA)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)implicaC)","(Cimplica(DeF))","(A)","(FouE)");
		$this->listaExercicios['resolucao'][] = array ("((AeB)implicaC)","((CouD)implicaE)","((AeB)implica(EouF))");
		$this->listaExercicios['resolucao'][] = array ("(AeB)","not(C)","((AeB)implica(CouD))","(DouE)");
		$this->listaExercicios['resolucao'][] = array ("(D)","(I)","((DeA)implica(not(C)))","(IimplicaM)","(MimplicaA)","not(P)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(CeD))");
		$this->listaExercicios['resolucao'][] = array ("((not(P))implicaQ))","(PouQ)");
		$this->listaExercicios['resolucao'][] = array ("(AouB)","((AouC)implicaD)","(Bimplica(DeC))","(D)");
		$this->listaExercicios['resolucao'][] = array ("((not(AeB))implica((not(A))ou(not(B)))");
		$this->listaExercicios['resolucao'][] = array ("not(Pe(not(P)))");
		$this->listaExercicios['resolucao'][] = array ("(not(PimplicaQ)implicaP)");
		$this->listaExercicios['resolucao'][] = array ("(not(PimplicaQ)implica(not(Q)))");
		$this->listaExercicios['resolucao'][] = array ("((Pimplica(not(P)))implica(not(P)))");
		$this->listaExercicios['resolucao'][] = array ("((not(P)implicaP)implicaP)");
		$this->listaExercicios['resolucao'][] = array ("((PeQ)implica(not((not(P))ou(not(Q)))))");
		
		$this->listaExercicios['resolucao'][] = array ("(Aimplica(not(B)implica(not(Aimplica(B)))))");
		$this->listaExercicios['resolucao'][] = array ("(Bou(BimplicaC))");
		$this->listaExercicios['resolucao'][] = array ("((not(A)e(not(B)))implica(not(AouB)))");
		$this->listaExercicios['resolucao'][] = array ("(Aimplica(BeC))","((BimplicaC)implicaD)","(Aimplica(BeD))");

		$this->listaExercicios['resolucao'][] = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CimplicaD)implicaD)");
		$this->listaExercicios['resolucao'][] = array ("((AouB)ouC)","(Aou(BouC))");
		$this->listaExercicios['resolucao'][] = array ("(((AimplicaB)implicaA)implicaA)");
		$this->listaExercicios['resolucao'][] = array ("(AouB)","(not(B)eD)","((AeD)ouC)","((AeD)implicaE)","(CimplicaE)","(E)");
		$this->listaExercicios['resolucao'][] = array ("(not(A)ouB)","not(Be(not(C)))","(CimplicaD)","(not(A)ouD)");
		$this->listaExercicios['resolucao'][] = array ("(B)","((RouS)implicaA)","(RouS)","((AeR)implicaC)","((BeS)implicaC)","(C)");
		$this->listaExercicios['resolucao'][] = array ("(Aimplica(BouC))","(Bimplica(DeF))","((CeA)implicaD)","(AimplicaD)");
		$this->listaExercicios['resolucao'][] = array ("((not(C))implica((not(A))e(not(B))))","((CouD)implicaE)","(B)","((not(E))implica(not(A)))");
		$this->listaExercicios['resolucao'][] = array ("((AimplicaB)implicaB)","(AimplicaC)","(BimplicaC)","(C)");
		$this->listaExercicios['resolucao'][] = array ("((CimplicaA)implicaB)", "(Cimplica(AeD))","((BeD)implica(EeF))","(FimplicaG)","(DimplicaG)");
		$this->listaExercicios['resolucao'][] = array ("((PimplicaQ)implicaQ)","(QimplicaP)","(P)");
		$this->listaExercicios['resolucao'][] = array ("(B)","((AeB)implica(CouD))","((CeA)implicaE)","(Dimplica(EeF))","(AimplicaE)");
		$this->listaExercicios['resolucao'][] = array ("((PimplicaQ)implicaQ)","(QimplicaP)","(P)");
		$this->listaExercicios['resolucao'][] = array ("(AeB)","((Cimplica(AouD))implicaE)","(EeB)");
		$this->listaExercicios['resolucao'][] = "Questão insolúvel";
		$this->listaExercicios['resolucao'][] = array ("((AimplicaB)implicaB)","((BimplicaA)implicaA)");
		$this->listaExercicios['resolucao'][] = array ("(Aimplica(not(B)))","(BouC)","((AimplicaC)implica(not(C)))","((DeB)implicaE)","(Eimplica(FeG))","(DimplicaF)");
		$this->listaExercicios['resolucao'][] = array ("(B)","(((AeB)e(not(A)))implicaD)","((CeA)implicaE)","(DimplicaE)","(DimplicaF)","(AimplicaE)");
		$this->listaExercicios['resolucao'][] = array ("((not(P))implica(((PouQ)implicaQ)e(Qimplica(PouQ))))");
		$this->listaExercicios['resolucao'][] = array ("(Pimplica(not(Q)))","(not(not(P)ou(not(Q)))ouR)","(Rimplica(not(AeB)ou(not(S))))","(Sou(not(CouD)))","(((not(A))ou(not(B)))implicaE)","(((not(C))e(not(D)))implicaE)","(E)");
		$this->listaExercicios['resolucao'][] = array ("not(Aimplica(not(B)))","(CouD)","((CeA)implica(FouG))","(FimplicaH)","(((not(H))ou(not(I)))implica(not(G)))","(HimplicaK)","(DimplicaJ)","((JeB)implicaK)","(K)");
    }*/

}
