<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\Tableaux;

class TableauxController extends Controller
{
    public function index(Request $numeroExercicio){

        $exercicio = Exercicios::getExercicio(14);
        //$exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);

    	$resposta = new Tableaux($exercicio);

    	// print_r("Exercício:<br>");
    	// print_r($exercicio);
    	// print_r("<br>----------------------------------------------<br><br>");

    	//print_r($resposta->fullSteps());
        // print_r(Exercicios::converteSaida($resposta->fullSteps()));
        // dd(Exercicios::converteSaida($resposta->fullSteps()));
        return json_encode(Exercicios::converteSaida($resposta->fullSteps()), JSON_UNESCAPED_UNICODE);
    	 // dd($resposta->fullSteps());
    }
}
?>