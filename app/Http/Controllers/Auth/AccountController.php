<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    //
    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');
    }

    public function viewPage(){
        
        $this->authorize('viewAny', auth()->user());

        return view('Accounts');
        
    }
    
    public function accountGamesPage($userID){

        $this->authorize('viewAny', auth()->user());
     
        $user = User::findorfail($userID);
        
        return view('/AccountGames', [

        'account' => $user,

        ]);

    }

    public function delete($userID){    

        $this->authorize('viewAny', auth()->user());
     
        $user = User::findorfail($userID);

        $game = $user->games;

        foreach ($game as $game){

            $gamename = $game->game_name;

            $GameTable = lcfirst(str_replace(' ','_',$gamename));

            $modelname = str_replace(' ','',$gamename);

            $GameModel = app_path("/Models/".$modelname.".php");

            if(file_exists($GameModel)){

                unlink($GameModel);

            }

            Schema::dropIfExists(''.$GameTable.'');

            Artisan::call('krlove:generate:model Player --table-name="players"');

            $path = public_path('storage/uploads/'.$gamename);

            File::deleteDirectory($path);
            
        }

        User::where('id', $userID)->delete();
    
        return redirect('/account');

    }

    public function deleteAccountGames($userID, $gameID){

        $this->authorize('viewAny', auth()->user());
     
        $game = Game::findorfail($gameID);

        $gamename = $game->game_name;

        $GameTable = lcfirst(str_replace(' ','_',$gamename));

        $modelname = str_replace(' ','',$gamename);

        $GameModel = app_path("/Models/".$modelname.".php");

        if(file_exists($GameModel)){

            unlink($GameModel);

        }

        Schema::dropIfExists(''.$GameTable.'');

        Artisan::call('krlove:generate:model Player --table-name="players"');

        $path = public_path('storage/uploads/'.$gamename);

        File::deleteDirectory($path);

        Game::where('id',$gameID)->delete();

        return redirect('/account/games/'.$userID);
        
    }
}
