<?php 
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
$DNSquestao1 = array ("(AouB)","(Bimplica(notnot(C)))","((AimplicaC)implicaC)");
$DNSquestao2 = array ("(AimplicaB)","(BimplicaC)","(Aimplica(BeC))");
$DNSquestao3 = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
$DNSquestao4 = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
$DNSquestao5 = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))");
$DNSquestao6 = array ("((PeQ)implicaR)","(Pimplica(QimplicaR))");
$DNSquestao7 = array ("(Pimplica(QimplicaR))","((PeQ)implicaR)");
$DNSquestao8 = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
$DNSquestao9 = array ("((AouB)implicaC)","(Cimplica(DeE))","(A)","(EouF)");
$DNSquestao10 = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");
$DNSquestao11 = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(DeC))");
$DNSquestao12 = array ("(AouB)","((AouC)implicaD)","(Bimplica(DeC))","(D)");
$DNSquestao13 = array ("(Aimplica(BeC))","((BimplicaC)implicaD)","(Aimplica(BeD))");
$DNSquestao14 = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CeA)implicaD)","(D)");
$DNSquestao15 = array ("(Aimplica(BeC))","((DouF)implicaA)","(Dimplica(BouF))");
$DNSquestao16 = array ("((AeB)implicaC)","((CouD)implicaE)","((AeB)implica(EouF))");
$DNSquestao17 = array ("(AouB)","(D)","(BimplicaA)","((AeD)implicaC)","((AeD)implicaE)","(CimplicaE)","(E)");
$DNSquestao18 = array ("(BeC)","((AeB)implica(CimplicaD))","(Aimplica(DouE))");
$DNSquestao19 = array ("(BimplicaA)","(CimplicaB)","(BouC)","((CimplicaA)implicaE)","((DeB)implicaE)","(Eimplica(FeG))","(DimplicaF)");
$DNSquestao20 = array ("(B)","((RouS)implicaA)","(RouS)","((AeR)implicaC)","((BeS)implicaC)","(C)");
$DNSquestao21 = array ("(B)","((AeB)implica(CouD))","((AeC)implicaE)","(Dimplica(EeF))","(AimplicaE)");

//Lista de DN com Negação
$DNNquestao1 = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
$DNNquestao2 = array ("(Pimplica(QeR))","((PeQ)implicaR)");
$DNNquestao3 = array ("(PimplicaQ)","not(Q)","not(P)");
$DNNquestao4 = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
$DNNquestao5 = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))"); //testar depois
$DNNquestao6 = array ("(Bimplica(CeA))","(AimplicaD)","(BeC)","(D)");
$DNNquestao7 = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");
$DNNquestao8 = array ("(AimplicaB)","(BimplicaC)","(C)","(Aimplica(BeC))");
$DNNquestao9 = array ("(PouQ)","(PimplicaR)","(QimplicaR)","(R)");
$DNNquestao10 = array ("(Aimplica(BeC))","((DeF)implicaA)","(DeF)","(BeF)");
$DNNquestao11 = array ("(AouB)","(Cimplica(not(A)))","(CeD)","(BouA)");
$DNNquestao12 = array ("((AouB)implicaC)","(Cimplica(DeF))","(A)","(FouE)");
$DNNquestao13 = array ("((AeB)implicaC)","((CouD)implicaE)","((AeB)implica(EouF))");
$DNNquestao14 = array ("(AeB)","not(C)","((AeB)implica(CouD))","(DouE)");
$DNNquestao15 = array ("(D)","(I)","((DeA)implica(not(C)))","(IimplicaM)","(MimplicaA)","not(P)");
$DNNquestao16 = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(CeD))");
$DNNquestao17 = array ("((not(P))implicaQ)","(PouQ)");
$DNNquestao18 = array ("(AouB)","((AouC)implicaD)","(Bimplica(DeC))","(D)");
$DNNquestao19 = array ("((not(AeB))implica((not(A))ou(not(B))))");
$DNNquestao20 = array ("not(Pe(not(P)))");
$DNNquestao21 = array ("(not(PimplicaQ)implicaP)");
$DNNquestao22 = array ("(not(PimplicaQ)implica(not(Q)))");
$DNNquestao23 = array ("((Pimplica(not(P)))implica(not(P)))");
$DNNquestao24 = array ("((not(P)implicaP)implicaP)");
$DNNquestao25 = array ("((PeQ)implica(not((not(P))ou(not(Q)))))");
$DNNquestao26 = array ("(Aimplica(not(B)implica(not(A)implica(B))))");
$DNNquestao27 = array ("(Bou(BimplicaC))");
$DNNquestao28 = array ("((not(A)e(not(B)))implica(not(AouB)))");
$DNNquestao29 = array ("(Aimplica(BeC))","((BimplicaC)implicaD)","(Aimplica(BeD))");
$DNNquestao30 = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CimplicaD)implicaD)");
$DNNquestao31 = array ("((AouB)ouC)","(Aou(BouC))");
$DNNquestao32 = array ("(((AimplicaB)implicaA)implicaA)");
$DNNquestao33 = array ("(AouB)","(not(B)eD)","((AeD)ouC)","((AeD)implicaE)","(CimplicaE)","(E)");
$DNNquestao34 = array ("(not(A)ouB)","not(Be(not(C)))","(CimplicaD)","(not(A)ouD)");
$DNNquestao35 = array ("(B)","((RouS)implicaA)","(RouS)","((AeR)implicaC)","((BeS)implicaC)","(C)");
$DNNquestao36 = array ("(Aimplica(BouC))","(Bimplica(DeF))","((CeA)implicaD)","(AimplicaD)");
$DNNquestao37 = array ("((not(C))implica((not(A))e(not(B))))","((CouD)implicaE)","(B)","((not(E))implica(not(A)))");
$DNNquestao38 = array ("((AimplicaB)implicaB)","(AimplicaC)","(BimplicaC)","(C)");
$DNNquestao39 = array ("((CimplicaA)implicaB)", "(Cimplica(AeD))","((BeD)implica(EeF))","(FimplicaG)","(DimplicaG)");
$DNNquestao40 = array ("((PimplicaQ)implicaQ)","(QimplicaP)","(P)");
$DNNquestao41 = array ("(B)","((AeB)implica(CouD))","((CeA)implicaE)","(Dimplica(EeF))","(AimplicaE)");
$DNNquestao42 = array ("((PimplicaQ)implicaQ)","(QimplicaP)","(P)");
$DNNquestao43 = array ("(AeB)","((Cimplica(AouD))implicaE)","(EeB)");
$DNNquestao44 = "Questão insolúvel";
$DNNquestao45 = array ("((AimplicaB)implicaB)","((BimplicaA)implicaA)");
$DNNquestao46 = array ("(Aimplica(not(B)))","(BouC)","((AimplicaC)implica(not(C)))","((DeB)implicaE)","(Eimplica(FeG))","(DimplicaF)");
$DNNquestao47 = array ("(B)","(((AeB)e(not(A)))implicaD)","((CeA)implicaE)","(DimplicaE)","(DimplicaF)","(AimplicaE)");
$DNNquestao48 = array ("((not(P))implica(((PouQ)implicaQ)e(Qimplica(PouQ))))");
$DNNquestao49 = array ("(Pimplica(not(Q)))","(not(not(P)ou(not(Q)))ouR)","(Rimplica(not(AeB)ou(not(S))))","(Sou(not(CouD)))","(((not(A))ou(not(B)))implicaE)","(((not(C))e(not(D)))implicaE)","(E)");
$DNNquestao50 = array ("not(Aimplica(not(B)))","(CouD)","((CeA)implica(FouG))","(FimplicaH)","(((not(H))ou(not(I)))implica(not(G)))","(HimplicaK)","(DimplicaJ)","((JeB)implicaK)","(K)");

//Lista de DN de primeira ordem
$LPOquestao1;
$LPOquestao2;
$LPOquestao3;
$LPOquestao4;
$LPOquestao5;
$LPOquestao6;
$LPOquestao7;
$LPOquestao8;
$LPOquestao9;
$LPOquestao10;
$LPOquestao11;
$LPOquestao12;
$LPOquestao13;
$LPOquestao14;
$LPOquestao15;
$LPOquestao16;
$LPOquestao17;
$LPOquestao18;
$LPOquestao19;
$LPOquestao20;
$LPOquestao21;
$LPOquestao22;
$LPOquestao23;
$LPOquestao24;
$LPOquestao25;
$LPOquestao26;
$LPOquestao27;
$LPOquestao28;
$LPOquestao29;
$LPOquestao30;
$LPOquestao31;
$LPOquestao32;
$LPOquestao33;
$LPOquestao34;
$LPOquestao35;
$LPOquestao36;
$LPOquestao37;
$LPOquestao38;
$LPOquestao39;
$LPOquestao40;
$LPOquestao41;
$LPOquestao42;
$LPOquestao43;
$LPOquestao44;
$LPOquestao45;
$LPOquestao46;
$LPOquestao47;
$LPOquestao48;
$LPOquestao49;
$LPOquestao50;
$LPOquestao51;
$LPOquestao52;
$LPOquestao53;
$LPOquestao54;
$LPOquestao55;
$LPOquestao56;
$LPOquestao57;
$LPOquestao58;
$LPOquestao59;
$LPOquestao60;
$LPOquestao61;
$LPOquestao62;
$LPOquestao63;
$LPOquestao64;
$LPOquestao65;
$LPOquestao66;

//Lista de semantica Parte 1 
$SEMquestao1 = array ("((paraTodox((P(x))e(Q(x))))implica((paraTodox(P(x)))e(paraTodox(Q(x)))))"); 
$SEMquestao2 = array ("(((paraTodox(P(x)))e(paraTodox(Q(x))))implica(paraTodox((P(x))e(Q(x)))))");
$SEMquestao3 = array ("((xistx((P(x))e(Q(x))))implica((xistx(P(x)))e(xistx(Q(x)))))");
$SEMquestao4 = array ("(((xistx(P(x)))e(xistx(Q(x))))implica(xistx((P(x))e(Q(x)))))");
$SEMquestao5 = array ("((paraTodox((P(x))ou(Q(x))))implica((paraTodox(P(x)))ou(paraTodox(Q(x)))))"); 
$SEMquestao6 = array ("(((paraTodox(P(x)))ou(paraTodox(Q(x))))implica(paraTodox((P(x))ou(Q(x)))))");
$SEMquestao7 = array ("((xistx((P(x))ou(Q(x))))implica((xistx(P(x)))ou(xistx(Q(x)))))");
$SEMquestao8 = array ("(((xistx(P(x)))ou(xistx(Q(x))))implica(xistx((P(x))ou(Q(x)))))");
$SEMquestao9 = array ("((paraTodox((P(x))implica(Q(x))))implica((paraTodox(P(x)))implica(paraTodox(Q(x)))))");
$SEMquestao10 = array ("(((paraTodox(P(x)))implica(paraTodox(Q(x))))implica(paraTodox((P(x))implica(Q(x)))))");
$SEMquestao11 = array ("((xistx((P(x))implica(Q(x))))implica((xistx(P(x)))implica(xistx(Q(x)))))");
$SEMquestao12 = array ("(((xistx(P(x)))implica(xistx(Q(x))))implica(xistx((P(x))implica(Q(x)))))");
?>