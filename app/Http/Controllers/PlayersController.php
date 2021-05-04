<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class PlayersController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');
    }

    //API
    
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
            'password' => ['required', 'string', 'min:8'],
            'games_id' => ['required','exists:games,id']   
        ]);

        return player::create([
            "player_name" => $data["player_name"],
            'password' => Hash::make($data['password']),
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

    //Admin Panel

    public function AllPlayerPage(){

        //Show All Players Page

        $this->authorize('viewAny', auth()->user());

        return view('AllPlayers');

    }

    public function display($id){

        //Show Players Page (According to game)

        $games = Game::findorfail($id);

        if (Auth::user()->is_admin == '0'){

            if (!$games->User->is_admin == '1'){

                $this->authorize('view', $games);
            }

        return view('Players',[
            'games'=> $games,
        ]);

       } else {

            return view('Players',[
                'games'=> $games,
             ]);

       }

    }

    public function add($id){

        //Create a player (Panel)

        $data = request()->validate([
            'player_name' => ['required', 'unique:players,player_name,NULL,id,games_id,' .$id],
            'player_password' => ['required', 'string', 'min:8'],
        ]);
        
        Player::create([
            'player_name' => $data['player_name'],
            'password' => Hash::make($data['player_password']),
            'games_id' => $id
        ]);
        return redirect('/game/' . $id);

    }

    public function delete($playerID){

        //Delete a player (Panel) - *Player folder will be deleted*

        $players = Player::find($playerID);

        $game = $players->game;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        File::deleteDirectory($path);

        Player::where('id', $playerID)->delete();
        
        return redirect('/game/' .$players->games_id);

    }

    public function deleteAllPlayer($playerID){

        //Delete a player in All Players Page (Panel) - *Player folder will be deleted*

        $players = Player::find($playerID);

        $game = $players->game;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        File::deleteDirectory($path);

        Player::where('id', $playerID)->delete();
        
        return redirect('/allplayer');

    }

}
