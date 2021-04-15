<?php

namespace App\Http\Controllers;

use App\Models\games;
use App\Models\players;
use App\Models\player_files;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlayerFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
        $playerfile = player_files::all();
        if ($playerfile->isEmpty()){
            
            $response = ['message' =>  'No player file avaliable.'];
            return response($response, 200);
            
        } else {
            return $playerfile;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\player_file  $player_file
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\player_file  $player_file
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\player_file  $player_file
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $Request)
    {
        //
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

        // $data = $request->validate([
        //     'player_id' => ['required', 'exists:player_files,player_id'],
        //     'type' => ['exists:player_files,type']    
        // ]);

        // $query2 = player_file::select("JSON_file")->where([
        //     'player_id' => $data['player_id'],
        //     'type' => $data['type']
        // ])->get();

        // foreach ($query2 as $query2){
        //     $file = $query2->JSON_file;
        // }

        // return response()->download($file);

        $data = $request->validate([
            'file_id' => ['required', 'exists:player_files,id']
        ]);

        $playerfile = player_files::find($data['file_id']);

        return response()->download($playerfile->JSON_file);
    }

    public function getContent($id){

        $playerfile = player_files::findorfail($id);
        $content = file_get_contents(public_path($playerfile->JSON_file));
        $data = json_decode($content, true);
        return $data;
    }

    
    public function viewpage($id){

        $players = players::findorfail($id);
        $games = $players->games;
        return view ('player_files', [
            'players'=> $players,
            'games' => $games
            ]);
    }
    
    public function add($id){

        $player = players::find($id);

        $game = $player->games;

        $gamefile = $game->game_name;

        // if (is_null(request('file_type'))){

        //     $data = request()->validate([
        //         'json/txt' => ['file', 'required'],
        //     ]);

        // } else {

        //     $data = request()->validate([
        //         'json/txt' => ['file', 'required'],
        //         'file_type' => ['unique:player_files,type,NULL,id,players_id,' .$id],
        //     ]);

        // }

        $data = request()->validate([
            'json/txt' => ['file', 'required'],
            'file_type' => ['required','unique:player_files,type,NULL,id,players_id,' .$id],
        ]);

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;
        $filename = request()->file('json/txt')->getClientOriginalName();

        // $fileconfirm = public_path('storage/uploads/'.$directory.'/'.$filename);
        // if (file_exists(str_replace('\\','/',$fileconfirm))){
        //     echo '<script>alert("test")</script>';
        // } else {
        //     echo '<script>alert("test2")</script>';
        // }

        $filepath = request('json/txt')->move('storage/uploads/' . $directory ,$filename);

        player_files::create([
            'JSON_file' => str_replace('\\','/',$filepath),
            'type' => request('file_type'),
            'players_id' => $id
        ]);

        return redirect('playerfile/' . $id);
    }

    public function viewContent($id){
        
        $playerfile = player_files::findorfail($id);
        $content = file_get_contents(public_path($playerfile->JSON_file));
        $data = json_decode($content, true);
        return $data;

    }

    public function download($file1,$file2,$file3,$file4,$file5){

        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5);
        
        return response()->download($path);
    }

    public function editpage($id){
        
        $playerfile = player_files::findorfail($id);
        $playerid = $playerfile->players_id;
        $players = players::find($playerid);
        return view ('edit_playerfile', [
            'players'=> $players,
            'playerfile'=> $playerfile
            ]);
    }

    public function edit($id){

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
