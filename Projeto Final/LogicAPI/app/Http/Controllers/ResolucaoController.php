<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

class ResolucaoController extends Controller
{
    public function index(Request $numeroExercicio){

    	$exercicioLista = new Exercicios('resolucao',$numeroExercicio->exercicio);
    	$exercicioEscolhido = $exercicioLista->getExercicio();
    	return $exercicioEscolhido;

    }
}
