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
		    array('sentenca'=>'(AouB),(Bimplica(notnot(C))),((AimplicaC)implicaC)','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(Aimplica(BeC))','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C)','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC)','lista'=>1),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR))','lista'=>1),
			array('sentenca'=>'((PeQ)implicaR),(Pimplica(QimplicaR))','lista'=>1),
			array('sentenca'=>'(Pimplica(QimplicaR)),((PeQ)implicaR)','lista'=>1),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D)','lista'=>1),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeE)),(A),(EouF)','lista'=>1),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC)','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C)','lista'=>1),
			array('sentenca'=>'(Pimplica(QeR)),((PeQ)implicaR)','lista'=>1),
			array('sentenca'=>'(PimplicaQ),not(Q),not(P)','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC)','lista'=>1),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR))','lista'=>1),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D)','lista'=>1),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC)','lista'=>1),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(C),(Aimplica(BeC))','lista'=>1),
			array('sentenca'=>'(PouQ),(PimplicaR),(QimplicaR),(R)','lista'=>1),
			array('sentenca'=>'(Aimplica(BeC)),((DeF)implicaA),(DeF),(BeF)','lista'=>1),
			array('sentenca'=>'(AouB),(Cimplica(not(A))),(CeD),(BouA)','lista'=>1),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeF)),(A),(FouE)','lista'=>1),
			array('sentenca'=>'((AeB)implicaC),((CouD)implicaE),((AeB)implica(EouF))','lista'=>1),
			array('sentenca'=>'(AeB),not(C),((AeB)implica(CouD)),(DouE)','lista'=>1),
			array('sentenca'=>'(D),(I),((DeA)implica(not(C))),(IimplicaM),(MimplicaA),not(P)','lista'=>1),
			array('sentenca'=>'((AouB)implicaC),(CimplicaD),((AeB)implica(CeD))','lista'=>1),
			array('sentenca'=>'((not(P))implicaQ)),(PouQ)','lista'=>1),
			array('sentenca'=>'(AouB),((AouC)implicaD),(Bimplica(DeC)),(D)','lista'=>1),
			array('sentenca'=>'((not(AeB))implica((not(A))ou(not(B)))','lista'=>1),
			array('sentenca'=>'not(Pe(not(P)))','lista'=>1),
			array('sentenca'=>'(not(PimplicaQ)implicaP)','lista'=>1),
			array('sentenca'=>'(not(PimplicaQ)implica(not(Q)))','lista'=>1),
			array('sentenca'=>'((Pimplica(not(P)))implica(not(P)))','lista'=>1),
			array('sentenca'=>'((not(P)implicaP)implicaP)','lista'=>1),
			array('sentenca'=>'((PeQ)implica(not((not(P))ou(not(Q)))))','lista'=>1),
			array('sentenca'=>'(Aimplica(BouC)),(BimplicaD),(Fimplica(DeE)),(AouF),((CimplicaD)implicaD)','lista'=>1),
			array('sentenca'=>'((AouB)ouC),(Aou(BouC))','lista'=>1),
			array('sentenca'=>'(((AimplicaB)implicaA)implicaA)','lista'=>1),
			array('sentenca'=>'(AouB),(not(B)eD),((AeD)ouC),((AeD)implicaE),(CimplicaE),(E)','lista'=>1),
			array('sentenca'=>'(not(A)ouB),not(Be(not(C))),(CimplicaD),(not(A)ouD)','lista'=>1),
			array('sentenca'=>'(B),((RouS)implicaA),(RouS),((AeR)implicaC),((BeS)implicaC),(C)','lista'=>1),
			array('sentenca'=>'(Aimplica(BouC)),(Bimplica(DeF)),((CeA)implicaD),(AimplicaD)','lista'=>1),
			array('sentenca'=>'((not(C))implica((not(A))e(not(B)))),((CouD)implicaE),(B),((not(E))implica(not(A)))','lista'=>1),
			array('sentenca'=>'((AimplicaB)implicaB),(AimplicaC),(BimplicaC),(C)','lista'=>1),
			array('sentenca'=>'((CimplicaA)implicaB), (Cimplica(AeD)),((BeD)implica(EeF)),(FimplicaG),(DimplicaG)','lista'=>1),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P)','lista'=>1),
			array('sentenca'=>'(B),((AeB)implica(CouD)),((CeA)implicaE),(Dimplica(EeF)),(AimplicaE)','lista'=>1),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P)','lista'=>1),
			array('sentenca'=>'(AeB),((Cimplica(AouD))implicaE),(EeB)','lista'=>1),
			array('sentenca'=>'((AimplicaB)implicaB),((BimplicaA)implicaA)','lista'=>1),
			array('sentenca'=>'(Aimplica(not(B))),(BouC),((AimplicaC)implica(not(C))),((DeB)implicaE),(Eimplica(FeG)),(DimplicaF)','lista'=>1),
			array('sentenca'=>'(B),(((AeB)e(not(A)))implicaD),((CeA)implicaE),(DimplicaE),(DimplicaF),(AimplicaE)','lista'=>1),
			array('sentenca'=>'((not(P))implica(((PouQ)implicaQ)e(Qimplica(PouQ))))','lista'=>1),
			array('sentenca'=>'(Pimplica(not(Q))),(not(not(P)ou(not(Q)))ouR),(Rimplica(not(AeB)ou(not(S)))),(Sou(not(CouD))),(((not(A))ou(not(B)))implicaE),(((not(C))e(not(D)))implicaE),(E)','lista'=>1),
			array('sentenca'=>'not(Aimplica(not(B))),(CouD),((CeA)implica(FouG)),(FimplicaH),(((not(H))ou(not(I)))implica(not(G))),(HimplicaK),(DimplicaJ),((JeB)implicaK),(K)','lista'=>1)
		);
		
		App\Exercicios::insert($dados);
    }
}
