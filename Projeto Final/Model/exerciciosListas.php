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
$DNSquestao1 = array ("(AouB)","(Bimplica(notnotC)","(AimplicaC)implicaC)");
$DNSquestao2 = array ("(AimplicaB)","(BimplicaC)","(Aimplica(BeC))");
$DNSquestao3 = array ("(AimplicaB)","(BimplicaC)","(A)","(C)");
$DNSquestao4 = array ("(AimplicaB)","(BimplicaC)","(AimplicaC)");
$DNSquestao5 = array ("((PouQ)implicaR)","(Pimplica(QimplicaR))");
$DNSquestao6 = array ("(PeQ)implicaR)","(Pimplica(QimplicaR))");
$DNSquestao7 = array ("(Pimplica(QimplicaR))","((PeQ)implicaR)");
$DNSquestao8 = array ("(Bimplica(CeA)","(AimplicaD)","(BeC)","(D)");
$DNSquestao9 = array ("((AouB)implicaC)","(Cimplica(DeE))","(A)","(DimplicaC)");
$DNSquestao10 = array ("((AouB)implicaC)","(DimplicaA)","(DimplicaC)");

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
$DNNquestao15 = array ("(D)","(I)","((DeA)implica(not(C))","(IimplicaM)","(MimplicaA)","not(P)");
$DNNquestao16 = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(CeD))");
$DNNquestao17 = array ("((not(P))implicaQ))","(PouQ)");
$DNNquestao18 = array ("(AouB)","((AouC)implicaD)","(Bimplica(DeC))","(D)");
$DNNquestao19 = array ("((not(AeB))implica((not(A))ou(not(B)))");
$DNNquestao20 = array ("not(Pe(not(P)))");
$DNNquestao21 = array ("(not(PimplicaQ)implicaP)");
$DNNquestao22 = array ("(not(PimplicaQ)implica(not(Q)))");
$DNNquestao23 = array ("((Pimplica(not(P)))implica(not(P)))");
$DNNquestao24 = array ("((not(P)implicaP)implicaP)");
$DNNquestao25 = array ("((PeQ)implica(not((not(P))ou(not(Q)))");
$DNNquestao30 = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CimplicaD)implicaD)");


?>