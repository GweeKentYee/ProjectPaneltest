<?php

namespace App\Http\Controllers;

use App\Models\games;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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

        $games = games::all();

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

        return games::create($data);

    }

    public function show(Request $request)
    {
        //Show a single game according to ID (API)

        $data = $request->validate([
            'games_id' => ['required','exists:games,id']   
        ]);

        $game = Games::find($data['games_id']);
        return $game;

    }

    public function destroy(request $request)
    {
        //Delete a game according to ID (API) - *Game folder will be deleted*

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

        //Create a game (Panel)

        $data = request()->validate([
            'game_name' => ['required', 'unique:games']
        ]);

        if (Games::create($data)){

            $notification = array(
                'message' => 'Game created successfully.',
                'alert-type' => 'success'
            );

            return redirect('/home')->with($notification);

        } else {

            $notification = array(
                'message' => 'Create game failed.',
                'alert-type' => 'error'
            );
            
            return back()->with($notification);

        }

    }

    public function remove(Request $Request){

        //Delete selected games (Panel) - *Game folder will be deleted*

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

    } 
    
}
