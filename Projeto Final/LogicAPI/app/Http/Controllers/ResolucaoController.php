<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;

class ResolucaoController extends Controller
{
    public function index(Request $numeroExercicio){

    	$exercicioLista = new Exercicios('resolucao',$numeroExercicio->exercicio);
    	// $exercicioLista = new Exercicios('resolucao');
    	$resposta = new Resolucao($exercicioLista->getExercicio());

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	// dd($resposta->fullSteps());
    
    }
}
