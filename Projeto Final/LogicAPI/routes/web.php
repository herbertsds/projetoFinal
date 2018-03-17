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
Route::group(['prefix' => 'api'], function(){
	
	Route::group(['prefix' => 'tableaux', 'middleware' => 'cors'], function(){
		
		Route::get('/', 'TableauxController@index');

		Route::post('', function () {
		    return request();
		});

	});

	Route::group(['prefix' => 'resolucao', 'middleware' => 'cors'], function(){
		
		Route::get('/', 'ResolucaoController@index');

		Route::get('/exercicio', 'ResolucaoController@exercicio');
		
		Route::get('/teste', 'ResolucaoController@teste');

		Route::post('', function () {
		    return 'Resolucao';
		});

	});
});

Route::get('/', function () {
    return view('welcome');
});