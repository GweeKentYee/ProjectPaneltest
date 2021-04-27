<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->is_admin == '0'){
        
            $admin = User::select('id')->where('is_admin', '1')->get();

            foreach ($admin as $admin){

                $adminID[] = $admin->id;
        
            }

            foreach ($adminID as $adminID){

                $admingames[] = Game::all()->where('users_id', $adminID);

            }

            $AccountGames = Game::all()->where('users_id', Auth::id());

            return view('home',[
                'AccountGames' => $AccountGames,
                'admingames' => $admingames
            ]);

        } else {

            $games = Game::all();
            return view('home',[
                'games' => $games
            ]);

        }

    }
}
