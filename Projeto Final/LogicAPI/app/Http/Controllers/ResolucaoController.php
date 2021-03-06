<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;

use App\Categorias;


class ResolucaoController extends Controller
{
	//Resolve um exercício específico
    public function index(Request $numeroExercicio){

    	//$exercicio = Exercicios::getExercicio($numeroExercicio);
        $exercicio = Exercicios::getExercicio(10);
    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Resolucao($exercicio);

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	//print_r($resposta->fullSteps());
    	 //dd($resposta->fullSteps());
    
    }
    //Função de teste do relacionamento
   	public function teste(){

   		dd(Exercicios::where_related('Categorias','tipo','resolucao'));
   	}

   	//Pega um exercício
    public function exercicio(Request $numeroExercicio){
   
    	$exercicio = Exercicios::getExercicio($numeroExercicio);

    	// return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    	return json_encode($exercicio, JSON_UNESCAPED_UNICODE);
    }
}
