<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Exercicios;

use App\Categorias;

use App\Listas;

class ExercicioController extends Controller
{

    //Lista todas as categorias
    public function listarCategorias(){
    	return json_encode(Categorias::all());
    }

	//Pega todas as listas de uma determinada categoria
	//Se não receber nada, retorna todas as listas
    public function getListas(Request $categoria){

    	if (!is_null($categoria->id))
    		return json_encode(Categorias::find($categoria->id)->listas);
    	else
    		return json_encode(Listas::all());
    }


    public function listarExercicios(Request $request){
    	if(!is_null($request->lista_id))
    		return json_encode(Listas::find($request->lista_id)->exercicios);
    	else if (!is_null($request->categorias_id)) 
    		return json_encode(Categorias::find($request->categorias_id)->exercicios);
    	else
    		return json_encode(Exercicios::all());
    		// dd(Listas::find(1)->exercicios);
    	
    }

    //Pega um exercício específico
    public function getExercicio(Request $numeroExercicio){
   
    	$exercicio = Exercicios::getExercicio($numeroExercicio);

    	// return json_encode($resposta, JSON_UNESCAPED_UNICODE);
    	return json_encode($exercicio, JSON_UNESCAPED_UNICODE);
    }

    public function teste(Request $request){
        dd(Exercicios::converteEntrada($request));
    }

}
