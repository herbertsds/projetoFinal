<?php
class Formula{
	private $esquerdo=NULL;
	private $direito;
	private $conectivo;
	private $usado=False;
	
	//Adaptaзгo para poder usar vбrios tipos de construtores diferentes
	function __construct()
	{
		$a = func_get_args();
		$i = func_num_args();
		if (method_exists($this,$f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		}
	} 
	
	//Construtor para fуrmula que й apenas um бtomo
	public function __construct1($direito){
		$this->direito=$direito;
	}
	//Construtor para fуrmula que й бtomo negado
	public function __construct2($conectivo,$direito){
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para fуrmula de dois termos comum
	public function __construct3($esquerdo,$conectivo,$direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para fуrmula de dois termos cujo lado direito й uma fуrmula objeto
	public function __construct4($esquero,$conectivo,Formula $direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	
	//Construtor para fуrmula de dois termos cujo lado esquerdo й uma fуrmula objeto
	public function __construct5(Formula $esquero,$conectivo,$direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para fуrmula de dois termos cujo os dois lados sгo uma fуrmula objeto
	public function __construct6(Formula $esquero,$conectivo,Formula $direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para fуrmula que й uma fуrmula negada
	public function __construct7($conectivo,Formula $direito){
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	
	
	public function setEsquerdo($e){
		$this->esquerdo=$e;
	}
	
	public function setDireito($d){
		$this->direito=$d;
	}
	
	public function setConectivo($c){
		$this->conectivo=$c;
	}
	
	//Se a fуrmula for usada ela deve ser marcada como true
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