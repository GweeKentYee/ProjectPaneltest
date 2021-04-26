<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\player;
use App\Models\PlayerFile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerFileController extends Controller
{

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }

    public function index()
    {
        //Show all player files (API)

        $playerfile = PlayerFile::all();

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
            'player_file' => ['mimetypes:application/json,text/plain', 'required'],
            'type' => ['required', 'unique:player_files,type,NULL,id,players_id,' .$request['player_id']],
            'player_id' => ['required', 'exists:players,id']
        ]);

        $player = player::find($request['player_id']);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
        
        return PlayerFile::create([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => request('type'),
            'players_id' => $data['player_id']
        ]);

    }

    public function show(Request $request)
    {
        //Show player files according to Player ID (API)

        $data = $request->validate([
            'player_id' => ['required','exists:players,id']   
        ]);
        
        $players = player::find($data["player_id"]);

        $playerfiles = $players->PlayerFile;

        if ($playerfiles->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {

            return $playerfiles;

        }

    }

    public function showSingle(Request $request)
    {
        //Show player file with file ID (API)

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id']
        ]);

        return PlayerFile::find($data['file_id']);
        
    }

    public function update(Request $request)
    {
        //Update player file (API) - *Replacing the player file*

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id'],
            'player_file' => ['mimetypes:application/json,text/plain', 'required']
        ]);

        $playerfile = PlayerFile::find($data['file_id']);

        $data2 = $request->validate([
            'type' => ['required', 'unique:player_files,type,'.$request['file_id'].',id,players_id,' .$playerfile->players_id]
        ]);
        
        $player = player::find($playerfile->players_id);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
    
        $playerfile->update([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => $data2['type']
        ]);
        
        return $playerfile;

    }

    public function destroy(request $Request)
    {
        //Delete a player file (API) - *Player file will be deleted according to File ID*

        $data = $Request->validate([
            'file_id' => ['required', 'exists:player_files,id']
        ]);

        $playerfile = PlayerFile::find($data['file_id']);

        $file = $playerfile->JSON_file;

        $filepath = str_replace('\\','/',public_path($file));

        if(file_exists($filepath)){

            unlink($filepath);
            PlayerFile::where('id', $data['file_id'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        } else{
            PlayerFile::where('id', $data['file_id'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        }   

    }

    public function downloadApi(request $request){

        //Download a player file (API) 

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id']
        ]);

        $playerfile = PlayerFile::find($data['file_id']);

        return response()->download($playerfile->JSON_file);

    }

    public function ReadFileApi($id){

        //Read a player file (API)

        $playerfile = PlayerFile::findorfail($id);

        $content = file_get_contents(public_path($playerfile->JSON_file));

        $data = json_decode($content, true);
        return $data;

    }

    public function PlayerFilePage(Game $gameID, $playerID){

        $modelname = str_replace(' ', '',$gameID->game_name);

        $model = "App\\Models\\".$modelname;

        $players = $model::findorfail($playerID);

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

        $GamePlayerModel = "App\\Models\\".$GameModelName.'PlayerFiles';

        $players = $GameModel::findorfail($playerID);

        $game = $players->game;

        $gamefile = $game->game_name;

        $GamePlayerTable = lcfirst(str_replace(' ', '_',$game->game_name.'_player_files'));

        $data = request()->validate([
            'json/txt' => ['mimetypes:application/json,text/plain', 'required'],
            'file_type' => ['required','unique:'.$GamePlayerTable.',type,NULL,id,players_id,'.$playerID],
        ]);

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('json/txt')->getClientOriginalName();

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);

        $GamePlayerModel::create([
            'JSON_file' => str_replace('\\','/',$filepath),
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

        $GameFileModel = "App\\Models\\".$GameModelName.'PlayerFiles';

        $playerfile = $GameFileModel::findorfail($fileID);

        $content = file_get_contents(public_path($playerfile->JSON_file));

        $data = json_decode($content, true);
        
        return $data;

    }

    public function download($file1,$file2,$file3,$file4,$file5){

        //Download a player file (Panel)

        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5);
        
        return response()->download($path);

    }

    public function editPage($gameID, $fileID){
        
        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameFileModel = "App\\Models\\".$GameModelName.'PlayerFiles';

        $playerfile = $GameFileModel::findorfail($fileID);

        $playerid = $playerfile->players_id;

        $players = $GameModel::find($playerid);

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

        $GameFileModel = "App\\Models\\".$GameModelName.'PlayerFiles';

        $GameFileTable = lcfirst(str_replace(' ','_',$game->game_name.'_player_files'));

        $playerfile = $GameFileModel::findorfail($fileID);

        $playerid = $playerfile->players_id;
        
        $players = $playerfile->$GameModelName;

        $gamefile = $game->game_name;

        $data = request()->validate([
            'json/txt' => ['required', 'mimetypes:application/json,text/plain'],
            'file_type' => ['required', 'unique:'.$GameFileTable.',type,'.$fileID.',id,players_id,' .$playerid], 
        ]);
            
        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $filename = request()->file('json/txt')->getClientOriginalName();

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);
        
        $playerfile->update([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => request('file_type'),
        ]);

        return redirect('/playerfile/' . $gameID.'/'.$playerid);

    }

    public function delete($gameID, $fileID){

        //Remove a player file (Panel) - *Player file will be deleted* 
        $game = Game::find($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameFileModel = "App\\Models\\".$GameModelName.'PlayerFiles';

        $playerfile = $GameFileModel::findorfail($fileID);

        $file = $playerfile->JSON_file;

        $filepath = str_replace('\\','/',public_path($file));

        if ($file == null){

            $GameFileModel::where('id', $fileID)->delete();

            $playerid = $playerfile->players_id;

            return redirect('playerfile/'.$gameID.'/'.$playerid);

        } else {
            
            if(file_exists($filepath)){

                unlink($filepath);
                $GameFileModel::where('id', $fileID)->delete();

                $playerid = $playerfile->players_id;
    
                return redirect('playerfile/'.$gameID.'/'.$playerid);

            } else{

                $GameFileModel::where('id', $fileID)->delete();

                $playerid = $playerfile->players_id;

                return redirect('playerfile/'.$gameID.'/'.$playerid);
            }   `

        }

    }

}
