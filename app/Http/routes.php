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


Route::get('/', 'HomeController@index');

Route::get('/jouer', 'AccueilController@getInfos');
Route::post('/', 'AccueilController@postInfos');


Route::get('salons/{n}', 'SalonsController@show');

Route::get('myturn', 'JeuxController@myturn');

Route::get('testsession', function (\Illuminate\Http\Request $req) {
    ob_start();
    var_dump($req->session());
    $res = ob_get_clean();
    return $res;
});

Route::get('play/{card}', 'JeuxController@play');

Route::get('chat/{msg}', 'JeuxController@chat');

Route::get('quit', 'JeuxController@quit');

Route::get('ready', 'JeuxController@ready');


/**ROUTES PARTIE AUTHENTIFICATION**/

//Voir Vendor/Bestmomo
Route::auth();

Route::get('/home', 'HomeController@index');
