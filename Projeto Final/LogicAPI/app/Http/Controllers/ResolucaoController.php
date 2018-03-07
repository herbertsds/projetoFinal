<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios; 

class ResolucaoController extends Controller
{
    public function index(Request $numeroExercicio){

    	$exercicioLista = new Exercicios('resolucao',40);
    	$exercicioEscolhido = $exercicioLista->getExercicio();
    	return $exercicioEscolhido;

    }
}
