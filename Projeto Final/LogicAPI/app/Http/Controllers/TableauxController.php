<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\Tableaux;

class TableauxController extends Controller
{
    public function index(Request $numeroExercicio){

    	
        $exercicio = Exercicios::getExercicio($numeroExercicio);

    	$resposta = new Tableaux($exercicio);

    	print_r("Exercício:<br>");
    	print_r($exercicio);
    	print_r("<br>----------------------------------------------<br><br>");

    	return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	// dd($resposta->fullSteps());
    }
}
