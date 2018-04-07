<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;

use App\Categorias;

use App\Listas;


class ResolucaoController extends Controller
{
	//Resolve um exercício específico
    public function index(Request $numeroExercicio){

    	$exercicio = Exercicios::getExercicio(1);
     // $exercicio = Exercicios::getExercicio(40);

    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Resolucao($exercicio);

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	//print_r($resposta->fullSteps());
      // dd($exercicio);
    
    }
    //Função de teste do relacionamento
   	public function teste(){
      // dd(Lista::find(1)->exercicios);
   		// dd(Lista::find(2)->exercicios); 
      $categoria = Exercicios::find(1)->categorias[0]->tipo;
      dd($categoria);
    }   	

   	//Pega um exercício
    public function exercicio(Request $numeroExercicio){
   
    	$exercicio = Exercicios::getExercicio($numeroExercicio);

    	// return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    	return json_encode($exercicio, JSON_UNESCAPED_UNICODE);
    }

    public function stepByStep(Request $request){
    	$resposta = new Resolucao();
    	// return json_encode($resposta->stepByStep(Exercicios::converteEntrada($request)), JSON_UNESCAPED_UNICODE);
    	return $resposta->stepByStep(Exercicios::converteEntrada($request));
    }
}
