<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Grupo de endereços da api
Route::group(['prefix' => 'api', 'middleware' => 'cors'], function(){

	// api/tableaux/
	Route::group(['prefix' => 'tableaux', 'middleware' => 'cors'], function(){
		
		//Resolve um exercício específico, rodando o algoritmo inteiro do tableaux(fullSteps)
		Route::get('/', 'TableauxController@index');

		Route::post('', function () {
		    return request();
		});

	});
});

	



Route::get('/', function () {
    return view('welcome');
});