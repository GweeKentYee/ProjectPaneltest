<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/game/allplayers', 'App\Http\Controllers\DatatableController@AllPlayers')->name('api.allplayers');
Route::get('/game/list' ,'App\Http\Controllers\DatatableController@gamelist')->name('api.gamelist');
Route::get('/game/player/{id}', 'App\Http\Controllers\DatatableController@getPlayers')->name('api.players.index');
Route::get('/player/file/{gameID}/{playerID}', 'App\Http\Controllers\DatatableController@PlayerFile')->name('api.playerFile');

Route::group(['middleware'], function () {

    // public routes
    Route::post('/login', 'App\Http\Controllers\Auth\ApiAuthController@login')->name('login.api');
    Route::post('/register','App\Http\Controllers\Auth\ApiAuthController@register')->name('register.api');

});

Route::middleware('auth:api')->group(function () {

    // our routes to be protected will go in here
    Route::post('/logout', 'App\Http\Controllers\Auth\ApiAuthController@logout')->name('logout.api');
    
    Route::get('/games', 'App\Http\Controllers\GamesController@index');
    Route::post('games/create', 'App\Http\Controllers\GamesController@store');
    Route::post('/games/show', 'App\Http\Controllers\GamesController@show');
    Route::post('/games/delete', 'App\Http\Controllers\GamesController@destroy');
    
    Route::get('/players', 'App\Http\Controllers\PlayersController@index');
    Route::post('/players/gameID', 'App\Http\Controllers\PlayersController@show');
    Route::post('/players/playerID', 'App\Http\Controllers\PlayersController@showSingle');
    Route::post('/players/create', 'App\Http\Controllers\PlayersController@store');
    Route::post('/players/delete', 'App\Http\Controllers\PlayersController@destroy');

    Route::get('/playerfile', 'App\Http\Controllers\PlayerFileController@index');
    Route::get('/playerfile/read/{id}', 'App\Http\Controllers\PlayerFileController@ReadFileApi');
    Route::post('/playerfile/store', 'App\Http\Controllers\PlayerFileController@store');
    Route::post('/playerfile/update', 'App\Http\Controllers\PlayerFileController@update');
    Route::post('/playerfile/playerID', 'App\Http\Controllers\PlayerFileController@show');
    Route::post('/playerfile/fileID', 'App\Http\Controllers\PlayerFileController@showSingle');
    Route::post('/playerfile/download','App\Http\Controllers\PlayerFileController@downloadApi');
    Route::post('/playerfile/delete', 'App\Http\Controllers\PlayerFileController@destroy');

});

Route::middleware('auth:api')->get('/user', function (Request $request) {

    return $request->user();

});
