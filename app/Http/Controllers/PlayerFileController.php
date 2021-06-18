<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerFile;
use App\Models\Batman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PlayerFileController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }
    
    //API
    
    public function index()
    {

    }

    public function withGameID(Request $request){
        
        //Show player files with game ID (API)

        $data = $request->validate([
            'game_id' => ['required','exists:games,id']   
        ]);

        $game = Game::find($data['game_id']);

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $playerfile = $GameModel::all();

        if ($playerfile->isEmpty()){
            
            $response = ['message' =>  'No player file avaliable.'];
            return response($response, 200);
            
        } else {

            return $playerfile;

        }
    }

    public function store(Request $request)
    {
        //Store new player file (API)

        $data = request()->validate([
            'player_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg', 'required'],
            'player_id' => ['required', 'exists:players,id']
        ]);

        $player = Player::find($request['player_id']);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $data2 = request()->validate([
            'type' => ['required', 'unique:'.$GameTable.',type,NULL,id,players_id,' .$request['player_id']],
        ]);

        $directory = $gamefile . '/Player/' . $playername;

        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
        
        return $GameModel::create([
            'file' => str_replace('\\','/',$filepath),
            'type' => $data2['type'],
            'players_id' => $data['player_id']
        ]);

    }

    public function show(Request $request)
    {
        //Show player files according to Player ID (API)

        $data = $request->validate([
            'player_id' => ['required','exists:players,id']   
        ]);
        
        $players = Player::find($data["player_id"]);

        $game = $players->game;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;
        
        $playerfiles = $GameModel::all()->where('players_id', $data['player_id']);

        if ($playerfiles->isEmpty()){
            
            $response = ['message' =>  'No player file avaliable.'];
            return response($response, 200);
            
        } else {

            return $playerfiles;

        }

    }

    public function update(Request $request)
    {
        //Update player file (API) - *Replacing the player file*

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
            'new_player_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg', 'required'],
        ]);

        $playerfileInfo = $GameModel::select('id','file','players_id')->where('type', $data2['type'])->first();

        if (request('new_type')) {
            
            $data3 = $request->validate([
                'new_type' => ['unique:'.$GameTable.',type,'.$request['file_id'].',id,players_id,' .$playerfileInfo->players_id]
            ]);

            $playername = $player->player_name;

            $directory = $gamefile . '/Player/' . $playername;

            $filename = request()->file('new_player_file')->getClientOriginalName();

            $filepath = request('new_player_file')->move('storage/uploads/' . $directory ,$filename);

            $playerfile = $GameModel::findorfail($playerfileInfo->id);

            $playerfile->update([
                'file' => str_replace('\\','/',$filepath),
                'type' => $data3['new_type']
            ]);

            return $playerfile;

        } else {

            $playername = $player->player_name;

            $directory = $gamefile . '/Player/' . $playername;

            $filename = request()->file('new_player_file')->getClientOriginalName();

            $filepath = request('new_player_file')->move('storage/uploads/' . $directory ,$filename);

            $playerfile = $GameModel::findorfail($playerfileInfo->id);

            $playerfile->update([
                'file' => str_replace('\\','/',$filepath),
            ]);

            return $playerfile;
        }

    }

    public function destroy(request $Request)
    {
        //Delete a player file (API) - *Player file will be deleted according to File ID*

        $data = $Request->validate([
            'player_id' =>  ['required','exists:players,id'],  
        ]);
        
        $player = Player::findorfail($data['player_id']);

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data2 = $Request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where('type', $data2['type'])->first();

        $file = $playerfile->file;

        $filepath = str_replace('\\','/',public_path($file));

        if(file_exists($filepath)){

            unlink($filepath);
            $GameModel::where('type', $data2['type'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        } else{
            $GameModel::where('type', $data2['type'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        }   

    }

    public function downloadApi(request $request){

        //Download a player file (API) 

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

        return response()->download($playerfile->file);

    }

    public function ReadFileApi($gameID, $fileID){

        //Read a player file (API)

        $game = Game::findorfail($gameID);

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;
        
        $playerfile = $GameModel::findorfail($fileID);
     
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

    //Admin Panel

    public function PlayerFilePage(Game $gameID, $playerID){

        //Show player file page

        $players = Player::findorfail($playerID);

        return view ('PlayerFiles', [
            'players'=> $players,
            'games' => $gameID
            ]);

    }
    
    public function add($gameID, $playerID){

        //Store a player file (Panel)

        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $players = Player::findorfail($playerID);

        $game = $players->game;

        $gamefile = $game->game_name;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $data = request()->validate([
            'json/txt' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg', 'required'],
            'file_type' => ['required','unique:'.$GameTable.',type,NULL,id,players_id,'.$playerID],
        ]);

        $playername = $players->player_name;

        $directory = $gamefile . '/Player/' . $playername;
        $filename = request()->file('json/txt')->getClientOriginalName();

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);

        $GameModel::create([
            'file' => str_replace('\\','/',$filepath),
            'type' => request('file_type'),
            'players_id' => $playerID
        ]);

        return redirect('playerfile/' .$gameID.'/'. $playerID);

    }

    public function viewFile($gameID, $fileID){
        
        //View a player file (Panel)

        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $playerfile = $GameModel::findorfail($fileID);

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

    public function download($file1,$file2,$file3,$file4,$file5,$file6){

        //Download a player file (Panel)

        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5 . '/' . $file6);
        
        return response()->download($path);

    }

    public function editPage($gameID, $fileID){

        //Show edit player file page
        
        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $playerfile = $GameModel::findorfail($fileID);

        $playerid = $playerfile->players_id;

        $players = Player::find($playerid);

        return view ('EditPlayerFile', [
            'players'=> $players,
            'playerfile'=> $playerfile
            ]);

    }

    public function edit($gameID, $fileID){

        //Edit a player file (Panel) - *Replacing the player file*

        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ','_',$game->game_name));

        $playerfile = $GameModel::findorfail($fileID);

        $playerid = $playerfile->players_id;
        
        $players = $playerfile->player;

        $gamefile = $game->game_name;

        $data = request()->validate([
            'json/txt' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            'type' => ['unique:'.$GameTable.',type,'.$fileID.',id,players_id,' .$playerid], 
        ]);

        $input = collect($data)->filter()->all();

        if(!empty($input)){
            
            if(request('json/txt')){

                $inputWithOutFile = collect($data)->except('json/txt')->filter()->all();

                $playername = $players->player_name;

                $directory = $gamefile . '/Player/' . $playername;

                $filename = request()->file('json/txt')->getClientOriginalName();

                $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);

                $updatepath = [
                    'file' => str_replace('\\','/',$filepath),
                ];
                
                $updatedata = array_merge($inputWithOutFile, $updatepath);

                unlink($playerfile->file);

                $playerfile->update($updatedata);

            } else {

                $playerfile->update($input);

            }

            return redirect('/playerfile/' . $gameID.'/'.$playerid);

        } else {

            Session::flash('edit_empty_playerfile', 'Please fill in at least one field.');

            return redirect('/playerfile/edit/' . $gameID.'/'.$fileID);
        }

    }

    public function delete($gameID, $fileID){

        //Remove a player file (Panel) - *Player file will be deleted* 

        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $playerfile = $GameModel::findorfail($fileID);

        $file = $playerfile->file;

        $filepath = str_replace('\\','/',public_path($file));

        if ($file == null){

            $GameModel::where('id', $fileID)->delete();

            $playerid = $playerfile->players_id;

            return redirect('playerfile/'.$gameID.'/'.$playerid);

        } else {
            
            if(file_exists($filepath)){

                unlink($filepath);
                $GameModel::where('id', $fileID)->delete();

                $playerid = $playerfile->players_id;
    
                return redirect('playerfile/'.$gameID.'/'.$playerid);

            } else{

                $GameModel::where('id', $fileID)->delete();

                $playerid = $playerfile->players_id;

                return redirect('playerfile/'.$gameID.'/'.$playerid);
            }   

        }

    }

}
