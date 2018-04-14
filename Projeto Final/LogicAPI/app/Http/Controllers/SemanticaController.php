<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Semantica;

use App\Categorias;

use App\Listas;
//echo "<pre>";


//Exercícios são do 138 ao 154
class SemanticaController extends Controller
{
	//Resolve um exercício específico
    public function index(Request $numeroExercicio){

    	$exercicio = Exercicios::getExercicio(138);
     // $exercicio = Exercicios::getExercicio(40);

    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Semantica($exercicio);

      //print_r($resposta->fullSteps());
    	//return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
      dd($resposta->fullSteps());
    	//print_r($resposta->fullSteps());
      
      // dd($exercicio);
    
    }
     	

   	//Pega um exercício
    public function exercicio(Request $numeroExercicio){
   
    	$exercicio = Exercicios::getExercicio($numeroExercicio);

    	// return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    	return json_encode($exercicio, JSON_UNESCAPED_UNICODE);
    }    
}
