<?php 
//Aqui será um "banco de dados" com todos os exercícios de todas as listas
//Como muitos métodos utilizam os mesmos exercícios, fica fácil chamá-los a partir daqui

//O formato será um array padronizado, qualquer método pode chamar a questão diretamente como entrada

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
$DNNquestao16 = array ("((AouB)implicaC)","(CimplicaD)","((AeB)implica(CeD))");
$DNNquestao30 = array ("(Aimplica(BouC))","(BimplicaD)","(Fimplica(DeE))","(AouF)","((CimplicaD)implicaD)");


?>