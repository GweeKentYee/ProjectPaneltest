<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/', 'App\Http\Controllers\Auth\LoginController@showLoginForm');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/servermonitor', 'App\Http\Controllers\ServerMonitorController@disk_total');
Route::post('/registernew','App\Http\Controllers\Auth\RegisterController@register');

Route::get('/account', 'App\Http\Controllers\Auth\AccountController@viewPage');
Route::get('/account/delete/{userID}', 'App\Http\Controllers\Auth\AccountController@delete');
Route::get('/account/games/{userID}', 'App\Http\Controllers\Auth\AccountController@accountGamesPage');
Route::get('/account/game/delete/{userID}/{gameID}', 'App\Http\Controllers\Auth\AccountController@deleteAccountGames');

Auth::routes(['register' => false]);
// Auth::routes();

Route::post('/game/add', 'App\Http\Controllers\GamesController@add');
Route::delete('/game/remove','App\Http\Controllers\GamesController@remove');
Route::get('/game/{id}', 'App\Http\Controllers\PlayersController@display');

Route::get('/allplayer' , 'App\Http\Controllers\PlayersController@AllPlayerPage');
Route::get('/allplayer/delete/{playerID}', 'App\Http\Controllers\PlayersController@deleteAllPlayer');

Route::post('/player/add/{id}', 'App\Http\Controllers\PlayersController@add');
Route::get('/player/delete/{playerID}', 'App\Http\Controllers\PlayersController@delete');

Route::get('/playerfile/{gameID}/{playerID}', 'App\Http\Controllers\PlayerFileController@PlayerFilePage');
Route::post('/playerfile/add/{gameID}/{playerID}', 'App\Http\Controllers\PlayerFileController@add');
Route::get('/playerfile/view/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@viewFile');
Route::get('/playerfile/download/{file1}/{file2}/{file3}/{file4}/{file5}', 'App\Http\Controllers\PlayerFileController@download');
Route::get('/playerfile/edit/{gameID}/{fileID}','App\Http\Controllers\PlayerFileController@editPage');
Route::patch('/playerfile/editfile/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@edit');
Route::get('/playerfile/delete/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@delete');






