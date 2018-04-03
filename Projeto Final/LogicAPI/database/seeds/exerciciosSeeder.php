<?php

use Illuminate\Database\Seeder;

class exerciciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dados = array(
		    
		    array('sentenca'=>'(AouB),(Bimplica(notnot(C))),((AimplicaC)implicaC))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(Aimplica(BeC)))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC))'),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR)))'),
			array('sentenca'=>'((PeQ)implicaR),(Pimplica(QimplicaR)))'),
			array('sentenca'=>'(Pimplica(QimplicaR)),((PeQ)implicaR))'),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D))'),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeE)),(A),(EouF))'),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC))'),
			array('sentenca'=>'((AouB)implicaC),(CimplicaD),((AeB)implica(DeC)))'),
			array('sentenca'=>'(AouB),((AouC)implicaD),(Bimplica(DeC)),(D))'),
			array('sentenca'=>'(Aimplica(BeC)),((BimplicaC)implicaD),(Aimplica(BeD)))'),
			array('sentenca'=>'(Aimplica(BouC)),(BimplicaD),(Fimplica(DeE)),(AouF),((CeA)implicaD),(D))'),
			array('sentenca'=>'(Aimplica(BeC)),((DouF)implicaA),(Dimplica(BouF)))'),
			array('sentenca'=>'((AeB)implicaC),((CouD)implicaE),((AeB)implica(EouF)))'),
			array('sentenca'=>'(AouB),(D),(BimplicaA),((AeD)implicaC),((AeD)implicaE),(CimplicaE),(E))'),
			array('sentenca'=>'(BeC),((AeB)implica(CimplicaD)),(Aimplica(DouE)))'),
			array('sentenca'=>'(BimplicaA),(CimplicaB),(BouC),((CimplicaA)implicaE),((DeB)implicaE),(Eimplica(FeG)),(DimplicaF))'),
			array('sentenca'=>'(B),((RouS)implicaA),(RouS),((AeR)implicaC),((BeS)implicaC),(C))'),
			array('sentenca'=>'(B),((AeB)implica(CouD)),((AeC)implicaE),(Dimplica(EeF)),(AimplicaE))'),

			//Lista de DN com Negação
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C))'),
			array('sentenca'=>'(Pimplica(QeR)),((PeQ)implicaR))'),
			array('sentenca'=>'(PimplicaQ),not(Q),not(P))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC))'),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR)))'),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D))'),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(C),(Aimplica(BeC)))'),
			array('sentenca'=>'(PouQ),(PimplicaR),(QimplicaR),(R))'),
			array('sentenca'=>'(Aimplica(BeC)),((DeF)implicaA),(DeF),(BeF))'),
			array('sentenca'=>'(AouB),(Cimplica(not(A))),(CeD),(BouA))'),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeF)),(A),(FouE))'),
			array('sentenca'=>'((AeB)implicaC),((CouD)implicaE),((AeB)implica(EouF)))'),
			array('sentenca'=>'(AeB),not(C),((AeB)implica(CouD)),(DouE))'),
			array('sentenca'=>'(D),(I),((DeA)implica(not(C))),(IimplicaM),(MimplicaA),not(P))'),
			array('sentenca'=>'((AouB)implicaC),(CimplicaD),((AeB)implica(CeD)))'),
			array('sentenca'=>'((not(P))implicaQ),(PouQ))'),
			array('sentenca'=>'(AouB),((AouC)implicaD),(Bimplica(DeC)),(D))'),
			array('sentenca'=>'((not(AeB))implica((not(A))ou(not(B)))))'),
			array('sentenca'=>'not(Pe(not(P))))'),
			array('sentenca'=>'(not(PimplicaQ)implicaP))'),
			array('sentenca'=>'(not(PimplicaQ)implica(not(Q))))'),
			array('sentenca'=>'((Pimplica(not(P)))implica(not(P))))'),
			array('sentenca'=>'((not(P)implicaP)implicaP))'),
			array('sentenca'=>'((PeQ)implica(not((not(P))ou(not(Q))))))'),
			array('sentenca'=>'(Aimplica(not(B)implica(not(A)implica(B)))))'),
			array('sentenca'=>'(Bou(BimplicaC)))'),
			array('sentenca'=>'((not(A)e(not(B)))implica(not(AouB))))'),
			array('sentenca'=>'(Aimplica(BeC)),((BimplicaC)implicaD),(Aimplica(BeD)))'),
			array('sentenca'=>'(Aimplica(BouC)),(BimplicaD),(Fimplica(DeE)),(AouF),((CimplicaD)implicaD))'),
			array('sentenca'=>'((AouB)ouC),(Aou(BouC)))'),
			array('sentenca'=>'(((AimplicaB)implicaA)implicaA))'),
			array('sentenca'=>'(AouB),(not(B)eD),((AeD)ouC),((AeD)implicaE),(CimplicaE),(E))'),
			array('sentenca'=>'(not(A)ouB),not(Be(not(C))),(CimplicaD),(not(A)ouD))'),
			array('sentenca'=>'(B),((RouS)implicaA),(RouS),((AeR)implicaC),((BeS)implicaC),(C))'),
			array('sentenca'=>'(Aimplica(BouC)),(Bimplica(DeF)),((CeA)implicaD),(AimplicaD))'),
			array('sentenca'=>'((not(C))implica((not(A))e(not(B)))),((CouD)implicaE),(B),((not(E))implica(not(A))))'),
			array('sentenca'=>'((AimplicaB)implicaB),(AimplicaC),(BimplicaC),(C))'),
			array('sentenca'=>'((CimplicaA)implicaB), (Cimplica(AeD)),((BeD)implica(EeF)),(FimplicaG),(DimplicaG))'),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P))'),
			array('sentenca'=>'(B),((AeB)implica(CouD)),((CeA)implicaE),(Dimplica(EeF)),(AimplicaE))'),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P))'),
			array('sentenca'=>'(AeB),((Cimplica(AouD))implicaE),(EeB))'),
			array('sentenca'=>'Questão insolúvel'),
			array('sentenca'=>'((AimplicaB)implicaB),((BimplicaA)implicaA))'),
			array('sentenca'=>'(Aimplica(not(B))),(BouC),((AimplicaC)implica(not(C))),((DeB)implicaE),(Eimplica(FeG)),(DimplicaF))'),
			array('sentenca'=>'(B),(((AeB)e(not(A)))implicaD),((CeA)implicaE),(DimplicaE),(DimplicaF),(AimplicaE))'),
			array('sentenca'=>'((not(P))implica(((PouQ)implicaQ)e(Qimplica(PouQ)))))'),
			array('sentenca'=>'(Pimplica(not(Q))),(not(not(P)ou(not(Q)))ouR),(Rimplica(not(AeB)ou(not(S)))),(Sou(not(CouD))),(((not(A))ou(not(B)))implicaE),(((not(C))e(not(D)))implicaE),(E))'),
			array('sentenca'=>'not(Aimplica(not(B))),(CouD),((CeA)implica(FouG)),(FimplicaH),(((not(H))ou(not(I)))implica(not(G))),(HimplicaK),(DimplicaJ),((JeB)implicaK),(K))')
		);
		
		App\Exercicios::insert($dados);
    }
}
