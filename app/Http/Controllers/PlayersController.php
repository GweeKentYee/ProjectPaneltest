<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\player;
use App\Models\PlayerFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class PlayersController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');
    }
    
    public function index()
    {
        //Show all players (API)

        $players = player::all();

        if ($players->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {

            return $players;

        }
        
    }

    public function store(Request $request)
    {
        //Create a player (API)

        $data = request()->validate([
            'player_name' => ['required', 'unique:players,player_name,NULL,id,games_id,' .$request['games_id']],
            'games_id' => ['required','exists:games,id']   
        ]);

        return player::create([
            "player_name" => $data["player_name"],
            "games_id" => $data["games_id"],
        ]);

    }

    public function show(Request $request)
    {
        //Show players according to game ID (API)

        $data = $request->validate([
            'games_id' => ['required','exists:games,id']   
        ]);
        
        $game = Game::find($data["games_id"]);

        $players = $game->players;

        if ($players->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {

            return $players;

        }
    
    }

    public function showSingle(request $request)
    {
        //Show player with player ID (API)

        $data = $request->validate([
            'player_id' => ['required', 'exists:players,id']
        ]);

        return player::find($data['player_id']);

    }

    public function destroy(request $request)
    {
        //Remove a player (API) - *Player folder will be deleted* 

        $data = $request->validate([
            'player_id' => ['required', 'exists:players,id']  
        ]);

        $player = player::find($data['player_id']);

        $game = $player->game;

        $gamefile = $game->game_name;

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        File::deleteDirectory($path);

        Player::where('id', $data['player_id'])->delete();
        
        $response = ['message' => 'Player deleted successfully.'];

        return response($response, 200);

    }

    public function AllPlayerPage(){

        return view('AllPlayers');

    }

    public function display($id){

        $games = Game::findorfail($id);
        
       return view('Players',[
           'games'=> $games,
           ]);

    }

    public function add($id){

        //Create a player (Panel)

        $game = Game::findorfail($id);

        $modelname = str_replace(' ', '',$game->game_name);

        $tablename = str_replace(' ', '_',$game->game_name);

        $model = "App\\Models\\".$modelname;

        $data = request()->validate([
            'player_name' => ['required', 'unique:'.$tablename.',player_name,NULL,id'],
        ]);

        $model::create([
            'player_name' => $data['player_name'],
            'games_id' => $id
        ]);
        return redirect('/game/' . $id);

    }

    public function delete(Game $gameID, $playerID){

        //Delete a player (Panel) - *Player folder will be deleted*
        
        $modelname = str_replace(' ', '',$gameID->game_name);

        $model = "App\\Models\\".$modelname;

        $players = $model::find($playerID);

        $game = $players->game;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        File::deleteDirectory($path);

        $model::where('id', $playerID)->delete();
        
        return redirect('/game/' .$players->games_id);

    }

    public function deleteAllPlayer($id){

        //Delete a player in All Players Page (Panel) - *Player folder will be deleted*

        $players = player::find($id);

        $game = $players->game;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        File::deleteDirectory($path);

        Player::where('id', $id)->delete();
        
        return redirect('/allplayer');

    }

}
