<?php
//N�o serve pra nada, apenas testando c�digo e fun��es internas para aplicar no c�digo principal



echo "<pre>";
$vetor = array("Tracer", "Sombra", "Bastion", "Zarya", "Roadhog");
if(!in_array("Orisa", $vetor)){
	print "Não tem<br>";
}
else{
	print "Tem<br>";
}


print "Bastion ".array_search("Orisa",$vetor)."<br>";
//$teste=substr($teste,0, strlen($teste)-1);
print_r($vetor);

/*
function imprimeDescendo($no){
	$impressos = array();
	$nivelSoma = 0;
	$nivelVetor = array();
	$i=0;
	$encerraloop=0;
	while($i<5){
		print_r($no->info);
		print "   ";
		verificaStatusNo($no);
		print "<br>";	
		array_push($impressos, $no);
		array_push($nivelVetor, $nivelSoma);
		print "<br><br>Impressos  ";
		print_r($impressos);
		print "<br><br>Nivel Vetor  ";
		print_r($nivelVetor);
		print "<br><br>";

		if($no->filhos==NULL){
			print "Indice ".array_search($no,$impressos)."<br>";
			print "Nivel soma ".$nivelSoma."<br>";
			while(in_array($no,$impressos) && ($nivelVetor[array_search($no,$impressos)]==$nivelSoma)){
				print "<br><br>Entrei no Loop1<br><br>";
				$nivelSoma--;
				$no=$no->pai;
				if($no->filhos[0]!=NULL){
					"<br><br>Entrei no if[0]<br><br>";
					if((in_array($no->filhos[0],$impressos)) && ($nivelVetor[array_search($no->filhos[0],$impressos)]==$nivelSoma)){
						//Array já impresso
						print "<br><br>Entrei no if1<br><br>";
					}
					else{
						$no=$no->filhos[0];
					}
				}
				else if($no->filhos[1]!=NULL){
					"<br><br>Entrei no if[1]<br><br>";
					if((in_array($no->filhos[1],$impressos)) && ($nivelVetor[array_search($no->filhos[1],$impressos)]==$nivelSoma)){
						//Array já impresso
						print "<br><br>Entrei no if2<br><br>";
					}
					else{
						$no=$no->filhos[1];
					}
				}
					if($nivelSoma==0){
						$encerraloop=1;
						break;
					}
				}
		}

		
		$nivelSoma++;

		if($encerraloop==1){
				break;
		}


		
		//Passos filhos da esquerda pra direita

		if($no->filhos[0]!=NULL){
			if((in_array($no->filhos[0],$impressos)) && ($nivelVetor[array_search($no->filhos[0],$impressos)]==$nivelSoma)){
				//Array já impresso
				print "<br><br>Entrei no if1<br><br>";
			}
			else{
				$no=$no->filhos[0];
			}
		}
		else if($no->filhos[1]!=NULL){
			if((in_array($no->filhos[1],$impressos)) && ($nivelVetor[array_search($no->filhos[1],$impressos)]==$nivelSoma)){
				//Array já impresso
				print "<br><br>Entrei no if2<br><br>";
			}
			else{
				$no=$no->filhos[1];
			}
		}
		//Se não acho filho, regresso para o pai em busca de outro ramo
		while(in_array($no,$impressos) &&($nivelVetor[array_search($no,$impressos)]==$nivelSoma)){
			print "<br><br>Entrei no Loop2<br><br>";
			if($no->filhos[0]!=NULL){
				"<br><br>Entrei no if[0]<br><br>";
				if((in_array($no->filhos[0],$impressos)) && ($nivelVetor[array_search($no->filhos[0],$impressos)]==$nivelSoma)){
					//Array já impresso
					print "<br><br>Entrei no if1<br><br>";
				}
				else{
					$no=$no->filhos[0];
				}
			}
			else if($no->filhos[1]!=NULL){
				"<br><br>Entrei no if[1]<br><br>";
				if((in_array($no->filhos[1],$impressos)) && ($nivelVetor[array_search($no->filhos[1],$impressos)]==$nivelSoma)){
					//Array já impresso
					print "<br><br>Entrei no if2<br><br>";
				}
				else{
					$no=$no->filhos[1];
				}
			}
			$nivelSoma--;
			$no=$no->pai;
			if($nivelSoma==0){
				break;
			}
		}
		if($nivelSoma==0){
				break;
		}
		$i++;

	}

}
*/
?>