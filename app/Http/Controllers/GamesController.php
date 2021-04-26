<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class GamesController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }

    public function index()
    {
        //Show all games (API)

        $games = Game::all();

        if ($games->isEmpty()){
            
            $response = ['message' =>  'No game avaliable.'];
            return response($response, 200);
            
        } else {

            return $games;

        }

    }

    public function store(Request $request)
    {
        //Create a game (API)

        $data = $request->validate([
            'game_name' => ['required','unique:games,game_name']
        ]);

        return Game::create($data);

    }

    public function show(Request $request)
    {
        //Show a single game according to ID (API)

        $data = $request->validate([
            'games_id' => ['required','exists:games,id']   
        ]);

        $game = Game::find($data['games_id']);
        return $game;

    }

    public function destroy(request $request)
    {
        //Delete a game according to ID (API) - *Game folder will be deleted*

        $data = $request->validate([
            'games_id' => ['required','exists:games,id']
        ]);

        $game = Game::find($data['games_id']);

        $path = public_path('storage/uploads/'.$game->game_name);

        File::deleteDirectory($path);

        Game::destroy($data);

        $response = ['message' =>  'Game deleted successfully'];
        return response($response, 200);  

    }

    public function add(){

        //Create a game (Panel)

        $data = request()->validate([
            'game_name' => ['required', 'unique:games']
        ]);

        $GameTable = lcfirst(str_replace(' ','_',$data['game_name']));
        
        Schema::create($GameTable, function (Blueprint $table) {
            $table->id();
            $table->string('player_name')->required();
            $table->unsignedBigInteger('games_id')->required();
            $table->timestamps();

            $table->foreign('games_id')->references('id')->on('games')->onDelete('cascade');
            $table->index('games_id');
        });
        
        $GamePlayerTable = $GameTable.'_player_files';

        Schema::create($GamePlayerTable, function (Blueprint $table) {

            $GameTable = lcfirst(str_replace(' ','_',request('game_name')));

            $table->id();
            $table->string('JSON_file')->required();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('players_id')->required();
            $table->timestamps();

            $table->foreign('players_id')->references('id')->on($GameTable)->onDelete('cascade');
            $table->index('players_id');
        });

        $GameModelName = str_replace(' ', '',$data['game_name']);
        $GamePlayerModelName = $GameModelName.'PlayerFiles';

        Artisan::call('krlove:generate:model '.$GameModelName.' --table-name='.$GameTable.'');
        Artisan::call('krlove:generate:model '.$GamePlayerModelName.' --table-name='.$GamePlayerTable.'');
        Artisan::call('krlove:generate:model Game --table-name="games"');

        Game::create($data);

        return redirect('/home');

    }

    public function remove(Request $Request){

        //Delete selected games (Panel) - *Game folder will be deleted*

        $checked = $Request->remove_game;

        $checkedvalue = $Request->remove_game;
        
        foreach ($checked as $checked){

            $game[] = Game::find($checked); 

        }  

        foreach ($game as $game){

            $gamename = $game->game_name;

            $GameTable = str_replace(' ','_',$gamename);

            $GamePlayerTable = $GameTable.'_player_files';

            $modelname = str_replace(' ','',$gamename);

            $GameModel = app_path("/Models/".$modelname.".php");
            $GamePlayerModel = app_path("/Models/".$modelname."PlayerFiles.php");

            if(file_exists($GameModel)){

                 unlink($GameModel);

            }

            if(file_exists($GamePlayerModel)){

                unlink($GamePlayerModel);

            }
            
            Schema::dropIfExists("'.$GamePlayerTable.'");

            Schema::dropIfExists("'.$GameTable.'");

            Artisan::call('krlove:generate:model Game --table-name="games"');

            $path = public_path('storage/uploads/'.$gamename);

            File::deleteDirectory($path);

        }

        foreach ($checkedvalue as $checkedvalue){

            Game::where('id',$checkedvalue)->delete();

        }

        return redirect('/home');

    } 
    
}
