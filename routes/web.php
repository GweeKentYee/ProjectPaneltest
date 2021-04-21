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
// Auth::routes();
// Auth::routes(['register' => false]);


Route::post('/game/add', 'App\Http\Controllers\GamesController@add');
Route::delete('/game/remove','App\Http\Controllers\GamesController@remove');
Route::get('/game/{id}', 'App\Http\Controllers\PlayersController@display');

Route::get('/allplayer' , 'App\Http\Controllers\PlayersController@all');
Route::get('/allplayer/edit/{id}', 'App\Http\Controllers\PlayersController@editpageall');
Route::patch('/allplayer/editplayer/{id}', 'App\Http\Controllers\PlayersController@editall');
Route::get('/allplayer/delete/{id}', 'App\Http\Controllers\PlayersController@deleteAll');

Route::post('/player/add/{id}', 'App\Http\Controllers\PlayersController@add');
Route::get('/player/delete/{id}', 'App\Http\Controllers\PlayersController@delete');

Route::get('/playerfile/{id}', 'App\Http\Controllers\PlayerFileController@viewpage');
Route::post('/playerfile/add/{id}', 'App\Http\Controllers\PlayerFileController@add');
Route::get('/playerfile/view/{id}', 'App\Http\Controllers\PlayerFileController@viewContent');
Route::get('/playerfile/download/{file1}/{file2}/{file3}/{file4}/{file5}', 'App\Http\Controllers\PlayerFileController@download');
Route::get('/playerfile/edit/{id}','App\Http\Controllers\PlayerFileController@editpage');
Route::patch('/playerfile/editfile/{id}', 'App\Http\Controllers\PlayerFileController@edit');
Route::get('/playerfile/delete/{id}', 'App\Http\Controllers\PlayerFileController@delete');






