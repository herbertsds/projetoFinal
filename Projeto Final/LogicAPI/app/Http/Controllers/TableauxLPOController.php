<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\TableauxLPO;
//echo "<pre>";
class TableauxLPOController extends Controller
{   
    public function index(Request $numeroExercicio){
        //Exercícios 72 até o 137


    	//$exercicio = Exercicios::getExercicio(92);
        // return $numeroExercicio->exercicio;
    	$exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);


        //$exercicio = Exercicios::getExercicio($numeroExercicio->exercicio);

    	$resposta = new TableauxLPO($exercicio);

    	//print_r("Exercício:<br>");
    	//print_r($exercicio);
    	//print_r("<br>----------------------------------------------<br><br>");
        //dd(1);

    	return $resposta->fullSteps();
    	 //dd($resposta->fullSteps());
    }
}
