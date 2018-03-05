<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exercicios extends Model
{
    private $numeroExercicio;
    private $listaExercicios;
    private $metodo;

    public function __construct($metodo, $numeroExercicio=NULL){
    	$this->numeroExercicio = $numeroExercicio;
    	$this->metodo = $metodo;
    	$this->setListaExercicios();
    }


    /**
     * @return mixed
     */
    public function getExercicio()
    {
        if ($this->numeroExercicio == NULL){
        	return $this->getExercicioAleatorio();
        }else{

        	return $this->listaExercicios[$this->metodo][$this->numeroExercicio];
        }
    }

    public function getExercicioAleatorio(){
    	$aleatorio = rand(1,count($this->listaExercicios[$this->metodo]));
    	return $this->listaExercicios['resolucao'][$aleatorio];
    }

    private function setListaExercicios(){
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
		$this->listaExercicios['resolucao'][1] = array ("(AouB)","(Bimplica(notnot(C)))","((AimplicaC)implicaC)");
		$this->listaExercicios['resolucao'][2] = array ("(AimplicaB)","(BimplicaC)","(Aimplica(BeC))");
		$this->listaExercicios['resolucao'][3] = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
		$this->listaExercicios['resolucao'][4] = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
		$this->listaExercicios['resolucao'][5] = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))");
		$this->listaExercicios['resolucao'][6] = array ("((PeQ)implicaR)","(Pimplica(QimplicaR))");
		$this->listaExercicios['resolucao'][7] = array ("(Pimplica(QimplicaR))","((PeQ)implicaR)");
		$this->listaExercicios['resolucao'][8] = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
		$this->listaExercicios['resolucao'][9] = array ("((AouB)implicaC)","(Cimplica(DeE))","(A)","(EouF)");
		$this->listaExercicios['resolucao'][10] = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");

		//Lista de DN com Negação
		$this->listaExercicios['resolucao'][11] = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
		$this->listaExercicios['resolucao'][12] = array ("(Pimplica(QeR))","((PeQ)implicaR)");
		$this->listaExercicios['resolucao'][13] = array ("(PimplicaQ)","not(Q)","not(P)");
		$this->listaExercicios['resolucao'][14] = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
		$this->listaExercicios['resolucao'][15] = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))"); //testar depois
		$this->listaExercicios['resolucao'][16] = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
		$this->listaExercicios['resolucao'][17] = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");
		$this->listaExercicios['resolucao'][18] = array ("(AimplicaB)","(BimplicaC)","(C)","(Aimplica(BeC))");
		$this->listaExercicios['resolucao'][19] = array ("(PouQ)","(PimplicaR)","(QimplicaR)","(R)");
		$this->listaExercicios['resolucao'][20] = array ("(Aimplica(BeC))","((DeF)implicaA)","(DeF)","(BeF)");
		$this->listaExercicios['resolucao'][21] = array ("(AouB)","(Cimplica(not(A)))","(CeD)","(BouA)");
		$this->listaExercicios['resolucao'][22] = array ("((AouB)implicaC)","(Cimplica(DeF))","(A)","(FouE)");
		$this->listaExercicios['resolucao'][23] = array ("((AeB)implicaC)","((CouD)implicaE)","((AeB)implica(EouF))");
		$this->listaExercicios['resolucao'][24] = array ("(AeB)","not(C)","((AeB)implica(CouD))","(DouE)");
		$this->listaExercicios['resolucao'][25] = array ("(D)","(I)","((DeA)implica(not(C)))","(IimplicaM)","(MimplicaA)","not(P)");
		$this->listaExercicios['resolucao'][26] = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(CeD))");
		$this->listaExercicios['resolucao'][27] = array ("((not(P))implicaQ))","(PouQ)");
		$this->listaExercicios['resolucao'][28] = array ("(AouB)","((AouC)implicaD)","(Bimplica(DeC))","(D)");
		$this->listaExercicios['resolucao'][29] = array ("((not(AeB))implica((not(A))ou(not(B)))");
		$this->listaExercicios['resolucao'][30] = array ("not(Pe(not(P)))");
		$this->listaExercicios['resolucao'][31] = array ("(not(PimplicaQ)implicaP)");
		$this->listaExercicios['resolucao'][32] = array ("(not(PimplicaQ)implica(not(Q)))");
		$this->listaExercicios['resolucao'][33] = array ("((Pimplica(not(P)))implica(not(P)))");
		$this->listaExercicios['resolucao'][34] = array ("((not(P)implicaP)implicaP)");
		$this->listaExercicios['resolucao'][35] = array ("((PeQ)implica(not((not(P))ou(not(Q)))))");
		$this->listaExercicios['resolucao'][36] = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CimplicaD)implicaD)");
    }

}
