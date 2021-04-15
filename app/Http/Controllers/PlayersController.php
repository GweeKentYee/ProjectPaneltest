<?php

namespace App\Http\Controllers;

use App\Models\games;
use App\Models\players;
use App\Models\player_files;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\support\Facades\File;

class PlayersController extends Controller
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
        $players = players::all();
        if ($players->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {
            return $players;
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
            'player_name' => ['required', 'unique:players,player_name,NULL,id,games_id,' .$request['games_id']],
            'games_id' => ['required','exists:games,id']   
        ]);
        return players::create([
            "player_name" => $data["player_name"],
            "games_id" => $data["games_id"],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\players  $players
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $data = $request->validate([
            'games_id' => ['required','exists:games,id']   
        ]);
        
        $game = games::find($data["games_id"]);

        $players = $game->players;

        if ($players->isEmpty()){
            
            $response = ['message' =>  'No player avaliable.'];
            return response($response, 200);
            
        } else {
            return $players;
        }
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\players  $players
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, players $players)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\players  $players
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request)
    {
        
        // $data = $request->validate([
        //     'player_id' => ['required', 'exists:players,id']  
        // ]);
        
        // $id = $data['player_id'];

        // $fileinfo = player_files::select("JSON_file")->where("players_id", $id)->get();

        // if($fileinfo->isEmpty()){
        //     Players::where('id', $id)->delete();
        //     $response = ['message' => 'Player deleted successfully.'];
        //     return response($response, 200); 
        // } else {
        //     foreach ($fileinfo as $fileinfo){
        //         $file = $fileinfo->JSON_file;
        //         $filepath[] = str_replace('\\','/',public_path($file));
        //     }

        //     foreach ($filepath as $filepath){
        //         if(file_exists($filepath)){
        //             unlink($filepath);    
        //         }
                 
        //         else{
        //             Players::where('id', $id)->delete();
        //             $response = ['message' => 'Player deleted successfully.'];
        //             return response($response, 200); 
        //         }
        //     } 
        //     Players::where('id', $id)->delete();
        //     $response = ['message' => 'Player deleted successfully.'];
        //     return response($response, 200);
            
        //  }

        $data = $request->validate([
            'player_id' => ['required', 'exists:players,id']  
        ]);

        $player = players::find($data['player_id']);

        $game = $player->games;

        $gamefile = $game->game_name;

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        file::deleteDirectory($path);

        Players::where('id', $data['player_id'])->delete();
        
        $response = ['message' => 'Player deleted successfully.'];
        return response($response, 200);


    }

    public function all(){

        return view('AllPlayers');
    }

    public function display($id){

        $games = games::findorfail($id);
        
       return view('game_players',[
           'games'=> $games,
           ]);
    }

    public function add($id){

        $data = request()->validate([
            'player_name' => ['required', 'unique:players,player_name,NULL,id,games_id,' .$id],
        ]);
        
        players::create([
            'player_name' => $data['player_name'],
            'games_id' => $id
        ]);
        return redirect('/game/' . $id);
    }

    public function delete($id){

        // $players = Players::find($id);
        // $fileinfo = player_files::select("JSON_file")->where("players_id", $players->id)->get();

        // if($fileinfo->isEmpty()){
        //     Players::where('id', $id)->delete();
        //     return redirect('/game/' .$players->games_id);   
        // } else {
        //     foreach ($fileinfo as $fileinfo){
        //         $file = $fileinfo->JSON_file;
        //         $filepath[] = str_replace('\\','/',public_path($file));
        //     }

        //     foreach ($filepath as $filepath){
        //         if(file_exists($filepath)){
        //             unlink($filepath);    
        //         }
                 
        //         else{
        //             Players::where('id', $id)->delete();
        //             return redirect('/game/' .$players->games_id);
        //         }
        //     } 
        //     Players::where('id', $id)->delete();
        //     return redirect('/game/' .$players->games_id); 
        
        //  }

        $players = players::find($id);

        $game = $players->games;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        file::deleteDirectory($path);

        Players::where('id', $id)->delete();
        
        return redirect('/game/' .$players->games_id);

    }

    public function deleteAll($id){
        
        // $players = Players::find($id);
        // $fileinfo = player_files::select("JSON_file")->where("players_id", $players->id)->get();

        // if($fileinfo->isEmpty()){
        //     Players::where('id', $id)->delete();
        //     return redirect('/allplayer');   
        // } else {
        //     foreach ($fileinfo as $fileinfo){
        //         $file = $fileinfo->JSON_file;
        //         $filepath[] = str_replace('\\','/',public_path($file));
        //     }

        //     foreach ($filepath as $filepath){
        //         if(file_exists($filepath)){
        //             unlink($filepath);    
        //         }
                 
        //         else{
        //             Players::where('id', $id)->delete();
        //             return redirect('/allplayer'); 
        //         }
        //     }
        //     Players::where('id', $id)->delete();
        //     return redirect('/allplayer');  
        
        // }

        $players = players::find($id);

        $game = $players->games;

        $gamefile = $game->game_name;

        $playername = $players->player_name;

        $directory = $gamefile . '/' . $playername;

        $path = public_path('storage/uploads/'.$directory);
        
        file::deleteDirectory($path);

        Players::where('id', $id)->delete();
        
        return redirect('/allplayer');
    }
}
