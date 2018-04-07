<?php
require_once("funcAuxiliares.php");
//require_once("funcTableaux.php");
//require_once("funcResolucao.php");
require_once("exerciciosListas.php");
require_once("funcSemantica.php");
echo "<pre>";
$listaConectivos=array("^","v","-","!",'@','&');
$listaFormulasDisponiveis=array();

//Print parsing funcionando até o exemplo 12, corrigir o formata fórmulas e testar mais exemplos antes de passar
//Para para a classe principal no laravel

$teste1 = array(0 => 'P(0)');
$teste2='P(0)';
print_r($teste1);
print_r($teste2);
if (in_array($teste2, $teste1)) {
	print "está sim";
}
else{
	print "não está";
}

/*
function preencheProximo($relacoes,&$raiz){
	$flag=false;
	//Se for igual a null lançar uma exceção
	if ($raiz['filhos']!=null) {
		$navega=&$raiz['filhos'][0];
	}
	//print "<br>Imprime Filhos<br>";
	//print_r($navega['filhos']);
	while (1) {
		flager:
		while ($navega['filhos']!=null) {
			print "<br>Estado anterior de navega['filhos']<br>";
			print_r($navega['filhos'][0]['info']);
			$navega=&$navega['filhos'][0];
			print "<br>Estado posterior de navega['filhos']<br>";
			print_r($navega['filhos'][0]['info']);
		}
		while ($navega['proximo']!=null) {
			print "<br>Estado anterior de navega['proximo']<br>";
			print_r($navega['proximo']['info']);
			$navega=&$navega['proximo'];
			print "<br>Estado posterior de navega['proximo']<br>";
			print_r($navega['proximo']['info']);
		}
		while ($navega['pai']!=null && $navega['pai']['proximo']==null) {
			$flag=true;
			$navega=&$navega['pai'];

		}
		
		if ($navega==$raiz) {
			break;
		}
		if ($flag) {
			if($navega['pai']['proximo']['filhos'] && $navega['proximo']==null) {
				$aux=&$navega['pai']['proximo']['filhos'][0];
				$navega['próximo']=&$aux;
			}
			unset($aux);
			$navega=&$navega['pai']['proximo'];
			$flag=false;
			goto flager;
		}

		//Processa a navegação

		print "<br>Estado anterior de navega['proximo'] - AUX<br>";
		print_r($navega['proximo']['info']);
		$aux=&$navega['pai']['proximo']['filhos'][0];
		if ($navega['proximo']==null) {
			$navega['proximo']=&$aux;
		}
		
		//$navega['proximo']=&$aux;
		print "<br>Estado posterior de navega['proximo'] - AUX<br>";
		print_r($navega['proximo']['info']);
		print "<br>Estado anterior de navega<br>";
		print_r($navega['info']);
		$navega=&$aux;
		print "<br>Estado posterior de navega<br>";
		print_r($navega['info']);
		unset($aux);

	}
}
*/

?>


