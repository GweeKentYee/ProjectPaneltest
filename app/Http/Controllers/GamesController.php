<?php

namespace App\Http\Controllers;

use App\Models\games;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GamesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        //
        $games = games::all();
        if ($games->isEmpty()){
            
            $response = ['message' =>  'No game avaliable.'];
            return response($response, 200);
            
        } else {
            return $games;
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
        $data = $request->validate([
            'game_name' => ['required','unique:games,game_name']
        ]);
        return games::create($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\games  $games
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $data = $request->validate([
            'games_id' => ['required','exists:games,id']   
        ]);
        $game = Games::find($data['games_id']);
        return $game;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\games  $games
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, games $games)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\games  $games
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request)
    {
        //
        $data = $request->validate([
            'games_id' => ['required','exists:games,id']
        ]);

        $game = games::find($data['games_id']);

        $path = public_path('storage/uploads/'.$game->game_name);
        File::deleteDirectory($path);

        Games::destroy($data);

        $response = ['message' =>  'Game deleted successfully'];

        return response($response, 200);                
    }

    public function add(){

        $data = request()->validate([
            'game_name' => ['required', 'unique:games']
        
        ]);
        Games::create($data);
        return redirect('/home');
    }

    public function remove(Request $Request){

        $checked = $Request->remove_game;
        $checkedvalue = $Request->remove_game;
        
        foreach ($checked as $checked){
            $game[] = games::find($checked); 
        }  

        foreach ($game as $game){

            $gamename = $game->game_name;
            $path = public_path('storage/uploads/'.$gamename);
            File::deleteDirectory($path);
        }

        foreach ($checkedvalue as $checkedvalue){
            games::where('id',$checkedvalue)->delete();
        }

        
        return redirect('/home');

        // $checked = $Request->remove_game;

        // foreach ($checked as $checked){
        //     games::where('id',$checked)->delete();
        // }

        // return redirect('/home');
    } 
}
