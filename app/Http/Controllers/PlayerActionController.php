<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerActionController extends Controller
{
    //API

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Store Player File to own account

        $player = Player::find(auth('api_player')->user()->id);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            'type' => ['required', 'unique:'.$GameTable.',type,NULL,id,players_id,' .$player->id],
        ]);

        $directory = $gamefile . '/' . $playername;

        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
        
        return $GameModel::create([
            'file' => str_replace('\\','/',$filepath),
            'type' => $data['type'],
            'players_id' => $player->id
        ]);

    }

    public function show()
    {
        //Show account details

        return auth('api_player')->user();

    }

    public function readFile(Request $request)
    {
        //Read own player file (Require type)

        $player = Player::find(auth('api_player')->user()->id);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where('type', $data['type'])->first();
     
        $file = public_path($playerfile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
 
    }

    public function readFileOthers(Request $request)
    {

        //Read others player file (Require playerID and type)

        $data = $request->validate([
            'player_id' =>  ['required','exists:players,id'],  
        ]);
        
        $player = Player::findorfail($data['player_id']);

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data2 = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where('type', $data2['type'])->first();
     
        $file = public_path($playerfile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }

    }

    public function randomPlayerFile(Request $request)
    {
        //Read random player file (Require type)

        $player = Player::inRandomOrder()->first();

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where([

            ['type', $data['type']],
            ['players_id', $player->id]

        ])->first();
     
        $file = public_path($playerfile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function update(Request $request)
    {
        //Update own player file (Require type)

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
            'new_player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
        ]);

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;

        $filename = request()->file('new_player_file')->getClientOriginalName();

        $filepath = request('new_player_file')->move('storage/uploads/' . $directory ,$filename);

        $playerfileInfo = $GameModel::select('id')->where('type', $data['type'])->first();

        $playerfile = $GameModel::findorfail($playerfileInfo->id);

        $playerfile->update([
            'file' => str_replace('\\','/',$filepath)
        ]);

        return $playerfile;
    }

    public function destroy($id)
    {
        //
    }
}
