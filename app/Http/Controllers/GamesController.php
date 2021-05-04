<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class GamesController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }

    //API

    public function index()
    {
        //Show all games (API)

        if (Auth::user()->is_admin == '0'){
        
            $admin = User::select('id')->where('is_admin', '1')->get();

            foreach ($admin as $admin){

                $adminID[] = $admin->id;
        
            }
            
            $admin = collect($adminID);

            $admingames = Game::all()->whereIn('users_id', $admin)->values();

            $AccountGames = Game::all()->where('users_id', Auth::id());

            if ($admingames->isEmpty() && $AccountGames->isEmpty()) {
                
                $response = ['message' =>  'No game avaliable.'];

                return response($response, 200);

            } else {

                return response($admingames->merge($AccountGames));
            
            }

        } else {

            $games = Game::all();

            if ($games->isEmpty()){
                
                $response = ['message' =>  'No game avaliable.'];

                return response($response, 200);
                
            } else {

                return $games;

            }
        }

    }

    public function store(Request $request)
    {
        //Create a game (API)

        $data = $request->validate([
            'game_name' => ['required','unique:games,game_name']
        ]);

        $GameTable = lcfirst(str_replace(' ','_',$data['game_name']));

        if (!Schema::hasTable($GameTable)) {
            Schema::create($GameTable, function (Blueprint $table) {
                $table->id();
                $table->string('file')->required();
                $table->string('type')->nullable();
                $table->unsignedBigInteger('players_id')->required();
                $table->timestamps();

                $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
                $table->index('players_id');
            });
        }   

        $GameModelName = str_replace(' ', '',$data['game_name']);

        Artisan::call('krlove:generate:model '.$GameModelName.' --table-name='.$GameTable.'');
        Artisan::call('krlove:generate:model Player --table-name="players"');

        return Game::create([
            'game_name' => $data['game_name'],
            'users_id' => Auth::guard('api')->id(),
        ]);

    }

    public function show(Request $request)
    {
        //Show a single game according to ID (API)

        $data = $request->validate([
            'game_id' => ['required','exists:games,id']   
        ]);

        $game = Game::find($data['game_id']);

        if (Auth::user()->is_admin == '0'){

            if (!$game->User->is_admin == '1'){

                $this->authorize('view', $game);

            }

            return $game;

       } else {

            return $game;
            
       }

    }

    public function destroy(request $request)

    {
        //Delete a game according to ID (API) - *Game folder will be deleted*

        $data = $request->validate([
            'game_id' => ['required','exists:games,id']
        ]);

        $game = Game::find($data['game_id']);

        if (Auth::user()->is_admin == '0'){

            $this->authorize('view', $game);

            $gamename = $game->game_name;

            $GameTable = lcfirst(str_replace(' ','_',$gamename));

            $modelname = str_replace(' ','',$gamename);

            $GameModel = app_path("/Models/".$modelname.".php");

            if(file_exists($GameModel)){

                    unlink($GameModel);

            }

            Schema::dropIfExists(''.$GameTable.'');

            Artisan::call('krlove:generate:model Player --table-name="players"');

            $path = public_path('storage/uploads/'.$gamename);

            File::deleteDirectory($path);

            Game::destroy($data);

            $response = ['message' =>  'Game deleted successfully'];

            return response($response, 200);  

       } else {

            $gamename = $game->game_name;

            $GameTable = lcfirst(str_replace(' ','_',$gamename));

            $modelname = str_replace(' ','',$gamename);

            $GameModel = app_path("/Models/".$modelname.".php");

            if(file_exists($GameModel)){

                    unlink($GameModel);

            }

            Schema::dropIfExists(''.$GameTable.'');

            Artisan::call('krlove:generate:model Player --table-name="players"');

            $path = public_path('storage/uploads/'.$gamename);

            File::deleteDirectory($path);

            Game::destroy($data);

            $response = ['message' =>  'Game deleted successfully'];

            return response($response, 200);  
            
       }

    }

    //Admin Panel

    public function add(){

        //Create a game (Panel)

        $data = request()->validate([
            'game_name' => ['required', 'unique:games']
        ]);

        $GameTable = lcfirst(str_replace(' ','_',$data['game_name']));

        if (!Schema::hasTable($GameTable)) {
            Schema::create($GameTable, function (Blueprint $table) {

                $table->id();
                $table->string('file')->required();
                $table->string('type')->nullable();
                $table->unsignedBigInteger('players_id')->required();
                $table->timestamps();

                $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
                $table->index('players_id');
            });
        }   

        $GameModelName = str_replace(' ', '',$data['game_name']);

        Artisan::call('krlove:generate:model '.$GameModelName.' --table-name='.$GameTable.'');
        Artisan::call('krlove:generate:model Player --table-name="players"');

        Game::create([
            'game_name' => $data['game_name'],
            'users_id' => Auth::id(),
        ]);

        return redirect('/home');

    }

    public function remove(Request $Request){

        //Delete selected games (Panel) - *Game folder will be deleted*

        $checked = $Request->remove_game;

        $gameID = collect($checked);

        $game = Game::find($gameID);

        foreach ($game as $game){

            $gamename = $game->game_name;

            $GameTable = lcfirst(str_replace(' ','_',$gamename));

            $modelname = str_replace(' ','',$gamename);

            $GameModel = app_path("/Models/".$modelname.".php");

            if(file_exists($GameModel)){

                 unlink($GameModel);

            }

            Schema::dropIfExists(''.$GameTable.'');

            Artisan::call('krlove:generate:model Player --table-name="players"');

            $path = public_path('storage/uploads/'.$gamename);

            File::deleteDirectory($path);

        }

        Game::whereIn('id',$gameID)->delete();

        return redirect('/home');

    } 
    
}
