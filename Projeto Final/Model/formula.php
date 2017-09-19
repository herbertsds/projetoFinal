<?php
class Formula{
	private $esquerdo=NULL;
	private $direito;
	private $conectivo;
	private $usado=False;
	
	//Adapta��o para poder usar v�rios tipos de construtores diferentes
	function __construct()
	{
		$a = func_get_args();
		$i = func_num_args();
		if (method_exists($this,$f='__construct'.$i)) {
			call_user_func_array(array($this,$f),$a);
		}
	} 
	
	//Construtor para f�rmula que � apenas um �tomo
	public function __construct1($direito){
		$this->direito=$direito;
	}
	//Construtor para f�rmula que � �tomo negado
	public function __construct2($conectivo,$direito){
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para f�rmula de dois termos comum
	public function __construct3($esquerdo,$conectivo,$direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para f�rmula de dois termos cujo lado direito � uma f�rmula objeto
	public function __construct4($esquero,$conectivo,Formula $direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	
	//Construtor para f�rmula de dois termos cujo lado esquerdo � uma f�rmula objeto
	public function __construct5(Formula $esquero,$conectivo,$direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para f�rmula de dois termos cujo os dois lados s�o uma f�rmula objeto
	public function __construct6(Formula $esquero,$conectivo,Formula $direito){
		$this->esquerdo=$esquerdo;
		$this->conectivo=$conectivo;
		$this->direito=$direito;
	}
	//Construtor para f�rmula que � uma f�rmula negada
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
	
	//Se a f�rmula for usada ela deve ser marcada como true
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