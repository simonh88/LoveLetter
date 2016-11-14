<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', 'AccueilController@accueil');

Route::get('/', 'AccueilController@getInfos');
Route::post('/', 'AccueilController@postInfos');


Route::get('salons/{n}', 'SalonsController@show')->where('n', '[1-3]+');