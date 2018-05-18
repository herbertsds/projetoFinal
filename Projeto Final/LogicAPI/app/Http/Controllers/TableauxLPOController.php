<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\TableauxLPO;

class TableauxLPOController extends Controller
{   
    public function index(Request $numeroExercicio){
        //Exercícios 72 até o 138
    	$exercicio = Exercicios::getExercicio(109);
        //$exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);

    	$resposta = new TableauxLPO($exercicio);

    	print_r("Exercício:<br>");
    	print_r($exercicio);
    	print_r("<br>----------------------------------------------<br><br>");

    	//return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	 dd($resposta->fullSteps());
    }
}