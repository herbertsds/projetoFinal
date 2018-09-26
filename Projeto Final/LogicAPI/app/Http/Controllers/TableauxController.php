<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\Tableaux;
//echo "<pre>";
class TableauxController extends Controller
{
    public function index(Request $numeroExercicio){
        // return $numeroExercicio;
        $exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);


        //$exercicio = Exercicios::getExercicio(25);

    	$resposta = new Tableaux($exercicio);

        // return $resposta->fullSteps();

    	//print_r("Exercício:<br>");
    	//print_r($exercicio);
    	//print_r("<br>----------------------------------------------<br><br>");
        //dd(1);

    	//print_r($resposta->fullSteps());
        // print_r(Exercicios::converteSaida($resposta->fullSteps()));
        // dd(Exercicios::converteSaida($resposta->fullSteps()));
        
        return $resposta->fullSteps();
    	// dd($resposta->fullSteps());
    }
}
?>