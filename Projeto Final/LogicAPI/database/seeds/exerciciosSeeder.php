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
		    
		    array('sentenca'=>'(AouB),(Bimplica(notnot(C))),((AimplicaC)implicaC)'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(Aimplica(BeC))'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C)'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC)'),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR))'),
			array('sentenca'=>'((PeQ)implicaR),(Pimplica(QimplicaR))'),
			array('sentenca'=>'(Pimplica(QimplicaR)),((PeQ)implicaR)'),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D)'),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeE)),(A),(EouF)'),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC)'),
			array('sentenca'=>'((AouB)implicaC),(CimplicaD),((AeB)implica(DeC))'),
			array('sentenca'=>'(AouB),((AouC)implicaD),(Bimplica(DeC)),(D)'),
			array('sentenca'=>'(Aimplica(BeC)),((BimplicaC)implicaD),(Aimplica(BeD))'),
			array('sentenca'=>'(Aimplica(BouC)),(BimplicaD),(Fimplica(DeE)),(AouF),((CeA)implicaD),(D)'),
			array('sentenca'=>'(Aimplica(BeC)),((DouF)implicaA),(Dimplica(BouF))'),
			array('sentenca'=>'((AeB)implicaC),((CouD)implicaE),((AeB)implica(EouF))'),
			array('sentenca'=>'(AouB),(D),(BimplicaA),((AeD)implicaC),((AeD)implicaE),(CimplicaE),(E)'),
			array('sentenca'=>'(BeC),((AeB)implica(CimplicaD)),(Aimplica(DouE))'),
			array('sentenca'=>'(BimplicaA),(CimplicaB),(BouC),((CimplicaA)implicaE),((DeB)implicaE),(Eimplica(FeG)),(DimplicaF)'),
			array('sentenca'=>'(B),((RouS)implicaA),(RouS),((AeR)implicaC),((BeS)implicaC),(C)'),
			array('sentenca'=>'(B),((AeB)implica(CouD)),((AeC)implicaE),(Dimplica(EeF)),(AimplicaE)'),

			//Lista de DN com Negação
			array('sentenca'=>'(AimplicaB),(BimplicaC),(A),(C)'),
			array('sentenca'=>'(Pimplica(QeR)),((PeQ)implicaR)'),
			array('sentenca'=>'(PimplicaQ),not(Q),not(P)'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(AimplicaC)'),
			array('sentenca'=>'((PouQ)implicaR),(Pimplica(QimplicaR))'),
			array('sentenca'=>'(Bimplica(CeA)),(AimplicaD),(BeC),(D)'),
			array('sentenca'=>'((AouB)implicaC),(DimplicaA),(DimplicaC)'),
			array('sentenca'=>'(AimplicaB),(BimplicaC),(C),(Aimplica(BeC))'),
			array('sentenca'=>'(PouQ),(PimplicaR),(QimplicaR),(R)'),
			array('sentenca'=>'(Aimplica(BeC)),((DeF)implicaA),(DeF),(BeF)'),
			array('sentenca'=>'(AouB),(Cimplica(not(A))),(CeD),(BouA)'),
			array('sentenca'=>'((AouB)implicaC),(Cimplica(DeF)),(A),(FouE)'),
			array('sentenca'=>'((AeB)implicaC),((CouD)implicaE),((AeB)implica(EouF))'),
			array('sentenca'=>'(AeB),not(C),((AeB)implica(CouD)),(DouE)'),
			array('sentenca'=>'(D),(I),((DeA)implica(not(P))),(IimplicaM),(MimplicaA),not(P)'),
			array('sentenca'=>'((AouB)implicaC),(CimplicaD),((AeB)implica(CeD))'),
			array('sentenca'=>'((not(P))implicaQ),(PouQ)'),
			array('sentenca'=>'(AouB),((AouC)implicaD),(Bimplica(DeC)),(D)'),
			array('sentenca'=>'((not(AeB))implica((not(A))ou(not(B))))'),
			array('sentenca'=>'not(Pe(not(P)))'),
			array('sentenca'=>'(not(PimplicaQ)implicaP)'),
			array('sentenca'=>'(not(PimplicaQ)implica(not(Q)))'),
			array('sentenca'=>'((Pimplica(not(P)))implica(not(P)))'),
			array('sentenca'=>'((not(P)implicaP)implicaP)'),
			array('sentenca'=>'((PeQ)implica(not((not(P))ou(not(Q)))))'),
			array('sentenca'=>'(Aimplica(not(B)implica(not(A)implica(B))))'),
			array('sentenca'=>'(Bou(BimplicaC))'),
			array('sentenca'=>'((not(A)e(not(B)))implica(not(AouB)))'),
			array('sentenca'=>'(Aimplica(BeC)),((BimplicaC)implicaD),(Aimplica(BeD))'),
			array('sentenca'=>'(Aimplica(BouC)),(BimplicaD),(Fimplica(DeE)),(AouF),((CimplicaD)implicaD)'),
			array('sentenca'=>'((AouB)ouC),(Aou(BouC))'),
			array('sentenca'=>'(((AimplicaB)implicaA)implicaA)'),
			array('sentenca'=>'(AouB),(not(B)eD),((AeD)ouC),((AeD)implicaE),(CimplicaE),(E)'),
			array('sentenca'=>'(not(A)ouB),not(Be(not(C))),(CimplicaD),(not(A)ouD)'),
			array('sentenca'=>'(B),((RouS)implicaA),(RouS),((AeR)implicaC),((BeS)implicaC),(C)'),
			array('sentenca'=>'(Aimplica(BouC)),(Bimplica(DeF)),((CeA)implicaD),(AimplicaD)'),
			array('sentenca'=>'((not(C))implica((not(A))e(not(B)))),((CouD)implicaE),(B),((not(E))implica(not(A)))'),
			array('sentenca'=>'((AimplicaB)implicaB),(AimplicaC),(BimplicaC),(C)'),
			array('sentenca'=>'((CimplicaA)implicaB), (Cimplica(AeD)),((BeD)implica(EeF)),(FimplicaG),(DimplicaG)'),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P)'),
			array('sentenca'=>'(B),((AeB)implica(CouD)),((CeA)implicaE),(Dimplica(EeF)),(AimplicaE)'),
			array('sentenca'=>'((PimplicaQ)implicaQ),(QimplicaP),(P)'),
			array('sentenca'=>'(AeB),((Cimplica(AouD))implicaE),(EeB)'),
			array('sentenca'=>'Questão insolúve'),
			array('sentenca'=>'((AimplicaB)implicaB),((BimplicaA)implicaA)'),
			array('sentenca'=>'(Aimplica(not(B))),(BouC),((AimplicaC)implica(not(C))),((DeB)implicaE),(Eimplica(FeG)),(DimplicaF)'),
			array('sentenca'=>'(B),(((AeB)e(not(C)))implicaD),((CeA)implicaE),(DimplicaE),(DimplicaF),(AimplicaE)'),
			array('sentenca'=>'((not(P))implica(((PouQ)implicaQ)e(Qimplica(PouQ))))'),
			array('sentenca'=>'(Pimplica(not(Q))),(not(not(P)ou(not(Q)))ouR),(Rimplica(not(AeB)ou(not(S)))),(Sou(not(CouD))),(((not(A))ou(not(B)))implicaE),(((not(C))e(not(D)))implicaE),(E)'),
			array('sentenca'=>'not(Aimplica(not(B))),(CouD),((CeA)implica(FouG)),(FimplicaH),(((not(H))ou(not(I)))implica(not(G))),(HimplicaK),(DimplicaJ),((JeB)implicaK),(K)'),
			
			//Lista de DN de primeira ordem
			array ('sentenca'=>"(xistx(F(x))),(F(a))"),
			array ('sentenca'=>"not(xistx(F(x))),not(F(a))"),
			array ('sentenca'=>"(paraTodox(F(x))),(F(a))"),
			array ('sentenca'=>"(F(a)),(xistx(F(x)))"),
			array ('sentenca'=>"(paraTodox(F(x))),(xistx(F(x)))"),
			array ('sentenca'=>"(paraTodox(not(F(x)))),not(paraTodox(F(x)))"),
			array ('sentenca'=>"(xistx(not(F(x)))),not(paraTodox(F(x)))"),
			array ('sentenca'=>"not(paraTodox(F(x))),(xistx(not(F(x))))"),
			array ('sentenca'=>"not(xistx(F(x))),(paraTodox(not(F(x))))"),
			array ('sentenca'=>"(paraTodox(not(F(x)))),not(xistx(F(x)))"),
			array ('sentenca'=>"(paraTodox(A(x))),not(paraTodox(not(A(x))))"),
			array ('sentenca'=>"not(paraTodox(not(A(x)))),(xistx(A(x)))"),
			array ('sentenca'=>"(paraTodox(P(x))),not(xistx(not(P(x))))"),
			array ('sentenca'=>"not(xistx(not(P(x)))),(paraTodox(P(x)))"),
			array ('sentenca'=>"(xistx(F(x))),not(paraTodox(not(F(x))))"),
			array ('sentenca'=>"not(xistx(F(x)e(not(F(x)))))"),
			array ('sentenca'=>"((xistx(F(x)))ou(xistx(not(F(x)))))"),
			array ('sentenca'=>"((xistx(F(x)))ou(paraTodox(not(F(x)))))"),
			array ('sentenca'=>"(paraTodox(not(F(x)e(not(F(x))))))"),
			array ('sentenca'=>"(paraTodox((F(x)ou(not(F(x))))))"),
			array ('sentenca'=>"(paraTodox(A(x)eB(x))),((paraTodox(A(x)))e(paraTodox(B(x))))"),
			array ('sentenca'=>"((paraTodox(A(x)))e(paraTodox(B(x)))),(paraTodox(A(x)eB(x)))"),
			array ('sentenca'=>"(paraTodox(A(x)ouB(x))),((paraTodox(A(x)))ou(paraTodox(B(x))))"),
			array ('sentenca'=>"((paraTodox(A(x)))ou(paraTodox(B(x)))),(paraTodox(A(x)ouB(x)))"),
			array ('sentenca'=>"(xistx(A(x)eB(x))),((xistx(A(x)))e(xistx(B(x))))"),
			array ('sentenca'=>"((xistx(A(x)))e(xistx(B(x)))),(xistx(A(x)eB(x)))"),
			array ('sentenca'=>"(xistx(A(x)ouB(x))),((xistx(A(x)))ou(xistx(B(x))))"),
			array ('sentenca'=>"((xistx(A(x)))ou(xistx(B(x)))),(xistx(A(x)ouB(x)))"),
			array ('sentenca'=>"(paraTodox(A(x)implicaB(x))),((paraTodox(A(x)))implica(paraTodox(B(x))))"),
			array ('sentenca'=>"((paraTodox(A(x)))implica(paraTodox(B(x)))),(paraTodox(A(x)implicaB(x)))"),
			array ('sentenca'=>"(paraTodox(paraTodoy(P(x|y)))),(paraTodoy(paraTodox(P(x|y))))"),
			array ('sentenca'=>"(xistx(xisty(L(x,y)))),(paraTodox(paraTodoy(not(L(x,y)))))"),
			array ('sentenca'=>"(xistx(F(x))),(xistx(xisty(F(x)eF(y))))"),
			array ('sentenca'=>"(paraTodox(not(F(x)))),(paraTodox(F(x)implicaG(x)))"),
			array ('sentenca'=>"(paraTodox(not(F(x)))),(paraTodox(F(x)implica(not(G(x)))))"),
			array ('sentenca'=>"not(xistx(paraTodoy((L(x,y)implica(not(L(x,x))))e((not(L(x,x)))implica(L(x,y))))))"), //Se e somente se convertido
			array ('sentenca'=>"(paraTodox((F(x)implicaR)e(RimplicaF(x)))),(R),(F(a))"),
			array ('sentenca'=>"not(F(a)),not(paraTodox(F(x)eG(x)))"),
			array ('sentenca'=>"((xistx(F(x)))e(xistx(not(F(x))))),(P)"),
			array ('sentenca'=>"(xistx((F(x))e(not(F(x))))),(P)"),
			array ('sentenca'=>"(xistx(xisty(L(x,y)))),(xisty(xistx(L(x,y))))"),
			array ('sentenca'=>"not(xistx(F(x))),(paraTodox(F(x)implicaP))"),
			array ('sentenca'=>"(paraTodox(paraTodoy(F(x,y)))),(paraTodox(F(x,x)))"),
			array ('sentenca'=>"(paraTodox((Q(y))implica(P(x)))),((Q(y))implica(paraTodox(P(x))))"),
			array ('sentenca'=>"((paraTodox(A(x)))e(paraTodox(B(x)))),(paraTodox(A(x)eB(x)))"),
			array ('sentenca'=>"(paraTodox(A(x)eB(x))),((paraTodox(A(x)))e(paraTodox(B(x))))"),
			array ('sentenca'=>"(paraTodox(A(x)ouB(x))),((paraTodox(A(x)))ou(paraTodox(B(x))))"),
			array ('sentenca'=>"((paraTodox(A(x)))ou(paraTodox(B(x)))),(paraTodox(A(x)ouB(x)))"),
			array ('sentenca'=>"(paraTodox(F(x)implicaG(x))),((paraTodox(not(F(x))))implica(paraTodox(not(G(x)))))"),
			array ('sentenca'=>"((xistx(A(x)))e(xistx(B(x)))),(xistx(A(x)eB(x)))"),
			array ('sentenca'=>"(xistx(A(x)eB(x))),((xistx(A(x)))e(xistx(B(x))))"),
			array ('sentenca'=>"(paraTodox(F(x)implicaG(x))),not(F(a)),(G(a))"),
			array ('sentenca'=>"(paraTodox((not(F(x)))e(not(G(x))))),not(F(a)eG(a))"),
			array ('sentenca'=>"(paraTodox(F(x))),(F(a)e(F(b)e((F(c))eF(d))))"),
			array ('sentenca'=>"(paraTodox(F(x)implicaG(x))),((paraTodox(not(G(x))))implica(paraTodox(not(F(x)))))"),
			array ('sentenca'=>"(paraTodox(F(x)implicaG(x))),((xistx(F(x)))implica(xistx(G(x))))"),
			array ('sentenca'=>"not(xistx(F(x)eG(x))),(paraTodox((not(F(x)))ou(not(G(x)))))"),
			array ('sentenca'=>"not(paraTodox(F(x)eG(x))),(xistx((not(F(x)))ou(not(G(x)))))"),
			array ('sentenca'=>"(paraTodox(F(x))),((paraTodox(G(x)))implica(paraTodox(F(x)eG(x))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy(F(x,y)implica(not(F(y,x)))))),(paraTodox(not(F(x,x))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((L(x,y))implica(L(y,x))))),(xistx(L(a,x))),(xistx(L(x,a)))"),
			array ('sentenca'=>"(paraTodox(paraTodoy(paraTodoz(((L(x,y))e(L(y,z)))implica(not(L(x,z))))))),(paraTodox(not(L(x,x))))"),
			array ('sentenca'=>"(paraTodox((P(x))implica(Q(x)))),(xistx(P(x)ouR(x))),((xistx(R(x)))implica(paraTodox(Q(x)))),(xistx(Q(x)))"),
			array ('sentenca'=>"(paraTodox((A(x))implica(B(x)))),(xisty(A(y)eC(y))),((xistx(C(x)eB(x)))implica(D(a))),(xistx(D(x)))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((P(x,y))implica(Q(x)eR(y))))),(paraTodox(P(a,x))),(((xistx(Q(x)))e(paraTodox(R(x))))implica(T(b))),(xistx(T(x)))"),
			array ('sentenca'=>"(paraTodox(paraTodoy(P(x,y)))),(paraTodox(paraTodoy((P(x,y))implica(Q(x)eR(y))))),(((xistx(R(x)))e(paraTodox(Q(x))))implica(xistx(S(x)eT(x)))),(xistx(T(x)))"),
					
			
			//Lista de semantica Parte 1 - Verificar se as fórmulas são válidas
			array ('sentenca'=>"((paraTodox((P(x))e(Q(x))))implica((paraTodox(P(x)))e(paraTodox(Q(x)))))"), 
			array ('sentenca'=>"(((paraTodox(P(x)))e(paraTodox(Q(x))))implica(paraTodox((P(x))e(Q(x)))))"),
			array ('sentenca'=>"((xistx((P(x))e(Q(x))))implica((xistx(P(x)))e(xistx(Q(x)))))"),
			array ('sentenca'=>"(((xistx(P(x)))e(xistx(Q(x))))implica(xistx((P(x))e(Q(x)))))"),
			array ('sentenca'=>"((paraTodox((P(x))ou(Q(x))))implica((paraTodox(P(x)))ou(paraTodox(Q(x)))))"), 
			array ('sentenca'=>"(((paraTodox(P(x)))ou(paraTodox(Q(x))))implica(paraTodox((P(x))ou(Q(x)))))"),
			array ('sentenca'=>"((xistx((P(x))ou(Q(x))))implica((xistx(P(x)))ou(xistx(Q(x)))))"),
			array ('sentenca'=>"(((xistx(P(x)))ou(xistx(Q(x))))implica(xistx((P(x))ou(Q(x)))))"),
			array ('sentenca'=>"((paraTodox((P(x))implica(Q(x))))implica((paraTodox(P(x)))implica(paraTodox(Q(x)))))"),
			array ('sentenca'=>"(((paraTodox(P(x)))implica(paraTodox(Q(x))))implica(paraTodox((P(x))implica(Q(x)))))"),
			array ('sentenca'=>"((xistx((P(x))implica(Q(x))))implica((xistx(P(x)))implica(xistx(Q(x)))))"),
			array ('sentenca'=>"(((xistx(P(x)))implica(xistx(Q(x))))implica(xistx((P(x))implica(Q(x)))))"),

			//Lista de semantica Parte 2 - Verificar e justificar se as fórmulas podem ser verdadeiras e falsas dando relações para elas

			array ('sentenca'=>"(paraTodox(paraTodoy((xistz(R(x,z)e(R(z,y))))implica(R(x,y)))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((R(x,y))implica(R(y,x)))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((xistz(R(z,x)e(R(z,y))))implica(R(x,y)))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((xistz(R(z,x)e(R(z,y))))implica((xistz(R(z,x)))e(xistz(Q(z,y)))))))"),
			array ('sentenca'=>"(paraTodox(paraTodoy((paraTodoz((R(x,z))e(R(x,y))))implica(xistw((R(z,w))e(Q(y,w)))))))")
		);
		
		App\Exercicios::insert($dados);
    }
}
