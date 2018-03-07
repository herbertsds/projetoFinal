<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

use App\Resolucao;

class ResolucaoController extends Controller
{
    public function index(Request $numeroExercicio){

    	$exercicioLista = new Exercicios('resolucao',$numeroExercicio->exercicio);
    	$resposta = new Resolucao($exercicioLista->getExercicio());

    	$header = array (
            'Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'
        );

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    
    }
}
