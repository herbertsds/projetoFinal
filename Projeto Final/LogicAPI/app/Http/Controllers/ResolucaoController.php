<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;

use App\Categorias;

use App\Listas;

//echo "<pre>";
class ResolucaoController extends Controller
{
	//Resolve um exercício específico
    public function index(Request $numeroExercicio){

      if(Resolucao::validaExercicio($numeroExercicio)){
        return 1;
      }

    	$exercicio = Exercicios::getExercicio($numeroExercicio);
      if(is_array($exercicio)){
          foreach ($exercicio as $key => $value) {
              $exercicio[$key] = Exercicios::converteSimbolosEntrada($exercicio[$key]);
          }
      }
      // $exercicio = Exercicios::getExercicio(1);
      // return $exercicio;
    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Resolucao($exercicio);


    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	//print_r($resposta->fullSteps());
      //dd($resposta->fullSteps());
      // dd($exercicio);
    
    }
    //Função de teste do relacionamento
   	public function teste(){
      // dd(Lista::find(1)->exercicios);
   		// dd(Lista::find(2)->exercicios); 
      // $categoria = Exercicios::find(1)->categorias[0]->tipo;
      // abort(400,"Este ramo já foi fechado.\n O nó folha é\n ".implode(array('pai','filho'),"\n"));
      // dd($categoria);
   		// return Exercicios::converteSaida("notA");
   		return "Ɐ       ∨      ∧       →       ¬       ∃";
    }   	

   	//Pega um exercício
    public function exercicio(Request $numeroExercicio){
   
    	$exercicio = Exercicios::getExercicio($numeroExercicio);
      //$exercicio = Exercicios::getExercicio(1);

    	// return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    	return json_encode($exercicio, JSON_UNESCAPED_UNICODE);
    }

    public function stepByStep(Request $request){

      // return ;
    	//return Exercicios::converteEntrada($request);
    	$resposta = new Resolucao();

    	// return json_encode($resposta->stepByStep(Exercicios::converteEntrada($request)), JSON_UNESCAPED_UNICODE);
      //dd($resposta->stepByStep(Exercicios::converteEntrada($request)));
      //print_r($resposta->stepByStep($request));

    	return Exercicios::converteSaida($resposta->stepByStep(Exercicios::converteEntrada($request)));
    }

    public function validaExercicio(Request $request){
        if(Resolucao::validaExercicio($request)){
          return 1;
        }
    }
}
