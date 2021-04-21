<?php

namespace App\Http\Controllers;

use App\Models\games;
use App\Models\players;
use App\Models\player_files;
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

        $playerfile = player_files::all();

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
            'player_file' => ['file', 'required'],
            'type' => ['required', 'unique:player_files,type,NULL,id,players_id,' .$request['player_id']],
            'player_id' => ['required', 'exists:players,id']
        ]);

        $player = players::find($request['player_id']);

        $playername = $player->player_name;

        $game = $player->games;

        $gamefile = $game->game_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
        
        return player_files::create([
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
        
        $players = players::find($data["player_id"]);

        $playerfiles = $players->player_files;

        if ($playerfiles->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {

            return $playerfiles;

        }

    }

    public function update(Request $request)
    {
        //Update player file (API) - *Replacing the player file*

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id'],
            'player_file' => ['file', 'required']
        ]);

        $playerfile = player_files::find($data['file_id']);

        $data2 = $request->validate([
            'type' => ['required', 'unique:player_files,type,'.$request['file_id'].',id,players_id,' .$playerfile->players_id]
        ]);
        
        $player = players::find($playerfile->players_id);

        $playername = $player->player_name;

        $game = $player->games;

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

        $playerfile = player_files::find($data['file_id']);

        $file = $playerfile->JSON_file;

        $filepath = str_replace('\\','/',public_path($file));

        if(file_exists($filepath)){

            unlink($filepath);
            player_files::where('id', $data['file_id'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        } else{
            player_files::where('id', $data['file_id'])->delete();

            $response = ['message' => 'Player file deleted successfully.'];
            return response($response, 200);

        }   

    }

    public function downloadApi(request $request){

        //Download a player file (API) 

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id']
        ]);

        $playerfile = player_files::find($data['file_id']);

        return response()->download($playerfile->JSON_file);

    }

    public function ReadFileApi($id){

        //Read a player file (API)

        $playerfile = player_files::findorfail($id);

        $content = file_get_contents(public_path($playerfile->JSON_file));

        $data = json_decode($content, true);
        return $data;

    }

    public function PlayerFilePage($id){

        $players = players::findorfail($id);

        $games = $players->games;

        return view ('PlayerFiles', [
            'players'=> $players,
            'games' => $games
            ]);

    }
    
    public function add($id){

        //Store a player file (Panel)

        $player = players::find($id);

        $game = $player->games;

        $gamefile = $game->game_name;

        $data = request()->validate([
            'json/txt' => ['file','mimetypes:application/json,text/plain', 'required'],
            'file_type' => ['required','unique:player_files,type,NULL,id,players_id,' .$id],
        ]);

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('json/txt')->getClientOriginalName();

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);

        player_files::create([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => request('file_type'),
            'players_id' => $id
        ]);

        return redirect('playerfile/' . $id);

    }

    public function viewFile($id){
        
        //View a player file (Panel)

        $playerfile = player_files::findorfail($id);
        $content = file_get_contents(public_path($playerfile->JSON_file));
        $data = json_decode($content, true);
        return $data;

    }

    public function download($file1,$file2,$file3,$file4,$file5){

        //Download a player file (Panel)

        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5);
        
        return response()->download($path);

    }

    public function editPage($id){
        
        $playerfile = player_files::findorfail($id);

        $playerid = $playerfile->players_id;

        $players = players::find($playerid);

        return view ('EditPlayerFile', [
            'players'=> $players,
            'playerfile'=> $playerfile
            ]);

    }

    public function edit($id){

        //Edit a player file (Panel) - *Replacing the player file*

        $playerfile = player_files::find($id);
        $playerid = $playerfile->players_id;
        $players = $playerfile->players;
        $gamefile = $players->games->game_name;

        $data = request()->validate([
            'json/txt' => ['required', 'file'],
            'file_type' => ['required', 'unique:player_files,type,'.$id.',id,players_id,' .$playerid], 
        ]);
            
        $playername = $players->player_name;
        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('json/txt')->getClientOriginalName();

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);
        
        $playerfile->update([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => request('file_type'),
        ]);

        return redirect('/playerfile/' . $playerid);

    }


    public function delete($id){

        //Remove a player file (Panel) - *Player file will be deleted* 

        $playerfile = player_files::find($id);

        $file = $playerfile->JSON_file;

        $filepath = str_replace('\\','/',public_path($file));

        if ($file == null){

            player_files::where('id', $id)->delete();

            $playerid = $playerfile->players_id;

            return redirect('playerfile/'.$playerid);

        } else {
            
            if(file_exists($filepath)){

                unlink($filepath);
                player_files::where('id', $id)->delete();

                $playerid = $playerfile->players_id;
    
                return redirect('playerfile/'.$playerid);

            } else{
                player_files::where('id', $id)->delete();

                $playerid = $playerfile->players_id;

                return redirect('playerfile/'.$playerid);
            }   

        }

    }

}
