<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\Tableaux;

class TableauxController extends Controller
{
    public function index(Request $numeroExercicio){

    	$exercicio = Exercicios::getExercicio(15);
        //$exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);

    	$resposta = new Tableaux($exercicio);

    	print_r("Exercício:<br>");
    	print_r($exercicio);
    	print_r("<br>----------------------------------------------<br><br>");

    	//print_r($resposta->fullSteps());
        //return json_encode($resposta->fullSteps(), JSON_UNESCAPED_UNICODE);
    	 dd($resposta->fullSteps());
    }
}
?>