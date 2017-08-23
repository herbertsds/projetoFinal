<?php
class Formula{
	private $esquerdo;
	private $direito;
	private $conectivo;
	private $usado=False;
	
	public function setEsquerdo($e){
		$this->esquerdo=$e;
	}
	
	public function setDireito($d){
		$this->direito=$d;
	}
	
	public function setConectivo($c){
		$this->conectivo=$c;
	}
	
	//Se a fórmula for usada ela deve ser marcada como true
	public function usaFormula(){
		$this->usado=true;
	}
	
	public function getEsquerdo(){
		return $this->esquerdo;
	}
	
	public function getDireito(){
		return $this->direito;
	}
	
	public function getConectivo(){
		return $this->conectivo;
	}
	
	public function estadoFormula(){
		return $this->usado;
	}
	
	
}
?>