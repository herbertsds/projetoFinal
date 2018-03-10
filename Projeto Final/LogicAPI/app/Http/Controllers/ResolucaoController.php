<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;
echo "<pre>";
class ResolucaoController extends Controller
{
    public function index(Request $numeroExercicio){

    	//Exercício 10 equivale ao 1 da lista de DN com negação
    	$exercicioLista = new Exercicios('resolucao',20);
    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Resolucao($exercicioLista->getExercicio());

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	//print_r($resposta->fullSteps());
    	 //dd($resposta->fullSteps());
    
    }

    public function exercicio(Request $numeroExercicio){
    	$exercicioLista = new Exercicios('resolucao',$numeroExercicio->exercicio);
    	$resposta = $exercicioLista->getExercicio();

    	return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    }
}
