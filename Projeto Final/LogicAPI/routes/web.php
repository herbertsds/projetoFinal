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
		
		Route::get('/', function () {
		    return 'Recebe uma nova proposição';
		});

		Route::post('', function () {
		    return request();
		});

	});
});

Route::get('/', function () {
    return view('welcome');
});