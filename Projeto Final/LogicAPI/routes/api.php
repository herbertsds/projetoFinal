<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// api/exercicios/
Route::group(['prefix' => 'exercicios', 'middleware' => 'cors'], function(){
	
	//Lista todas as categorias (resolução, tableaux)
	//Não é necessário enviar nenhum dado
	Route::get('/listarCategorias', 'ExercicioController@listarCategorias');

	//Mostra todas as listas de uma categoria
	//Espera id: X (id da categoria)
	//Se não receber nada, retorna todas as listas cadastradas
	Route::get('/getListas', 'ExercicioController@getListas');

	//Pega todos os exercícios de uma lista ou de uma categoria
	//Para uma lista, enviar lista_id: X (id da lista)
	//Para uma categoria, enviar categoria_id: X (id da categoria)
	//Se não enviar nada, pega todos os exercícios
	Route::get('/listarExercicios', 'ExercicioController@listarExercicios');

	//Pega um exercício específico
	Route::get('/getExercicio', 'ExercicioController@getExercicio');


	Route::get('/teste]', 'ExercicioController@teste');
	
	Route::post('', function () {
	    return 'Exercicios';
	});

});

// api/resolucao/
Route::group(['prefix' => 'resolucao', 'middleware' => 'cors'], function(){
	
	//Resolve um exercício específico, rodando o algoritmo inteiro de resolução (fullSteps)
	Route::get('/', 'ResolucaoController@index');
	
	//Teste de relacionamento (não usar)
	Route::get('/teste', 'ResolucaoController@teste');

	Route::post('', function () {
	    return 'Resolucao ae';
	});

});
