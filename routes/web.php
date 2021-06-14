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
Route::get('/game/{id}', 'App\Http\Controllers\GamesController@display');
Route::post('/data/add/{gameID}', 'App\Http\Controllers\GameDataTypeController@add');
Route::delete('/data/delete/{gameID}','App\Http\Controllers\GameDataTypeController@delete');
Route::get('/game/players/{id}', 'App\Http\Controllers\PlayersController@display');

Route::get('/data/{gameID}/{datatypeID}','App\Http\Controllers\GameDataController@display');
Route::post('/data/onelayer/column/add/{gameID}/{datatypeID}', 'App\Http\Controllers\GameDataController@OneLayerAddColumn');
Route::post('/data/onelayer/column/remove/{gameID}/{datatypeID}', 'App\Http\Controllers\GameDataController@OneLayerRemoveColumn');
Route::post('/data/onelayer/file/add/{gameID}/{datatypeID}', 'App\Http\Controllers\GameDataController@OneLayerAddDataFile');
Route::get('/data/onelayer/file/view/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@OneLayerViewFile');
Route::get('/data/editpage/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@EditPage');
Route::patch('/data/onelayer/file/edit/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@OneLayerEdit');
Route::get('/data/onelayer/file/delete/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@OneLayerDeleteDataFile');

Route::post('/data/twolayer/column/remove/{gameID}/{datatypeID}', 'App\Http\Controllers\GameDataController@TwoLayerRemoveColumn');
Route::post('/data/twolayer/add/{gameID}/{datatypeID}', 'App\Http\Controllers\GameDataController@TwoLayerAddData');
Route::patch('/data/twolayer/edit/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@TwoLayerEditData');
Route::get('/data/twolayer/delete/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataController@TwoLayerDeleteData');

Route::get('/data/twolayer/file/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataFileController@display');
Route::post('/data/twolayer/file/add/{gameID}/{datatypeID}/{dataID}', 'App\Http\Controllers\GameDataFileController@TwoLayerAddFile');
Route::get('/data/twolayer/file/view/{gameID}/{datatypeID}/{dataID}/{fileID}', 'App\Http\Controllers\GameDataFileController@TwoLayerViewFile');
Route::get('/data/twolayer/file/download/{file1}/{file2}/{file3}/{file4}/{file5}/{file6}/{file7}', 'App\Http\Controllers\GameDataFileController@TwoLayerDownload');
Route::patch('/data/twolayer/file/replace/{gameID}/{datatypeID}/{dataID}/{fileID}', 'App\Http\Controllers\GameDataFileController@TwoLayerReplaceFile');
Route::get('/data/twolayer/file/delete/{gameID}/{datatypeID}/{dataID}/{fileID}', 'App\Http\Controllers\GameDataFileController@TwoLayerDeleteFile');

Route::get('/allplayer' , 'App\Http\Controllers\PlayersController@AllPlayerPage');
Route::get('/allplayer/delete/{playerID}', 'App\Http\Controllers\PlayersController@deleteAllPlayer');

Route::post('/player/add/{id}', 'App\Http\Controllers\PlayersController@add');
Route::get('/player/delete/{playerID}', 'App\Http\Controllers\PlayersController@delete');

Route::get('/playerfile/{gameID}/{playerID}', 'App\Http\Controllers\PlayerFileController@PlayerFilePage');
Route::post('/playerfile/add/{gameID}/{playerID}', 'App\Http\Controllers\PlayerFileController@add');
Route::get('/playerfile/view/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@viewFile');
Route::get('/playerfile/download/{file1}/{file2}/{file3}/{file4}/{file5}/{file6}', 'App\Http\Controllers\PlayerFileController@download');
Route::get('/playerfile/edit/{gameID}/{fileID}','App\Http\Controllers\PlayerFileController@editPage');
Route::patch('/playerfile/editfile/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@edit');
Route::get('/playerfile/delete/{gameID}/{fileID}', 'App\Http\Controllers\PlayerFileController@delete');






