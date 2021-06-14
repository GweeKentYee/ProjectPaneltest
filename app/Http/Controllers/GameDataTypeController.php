<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDataType;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GameDataTypeController extends Controller
{
    //

    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }

    public function add($gameID)
    {
         
        $data = request()->validate([
            'data_name' => ['required', 'alpha_dash','unique:game_data_types,data_name,NULL,id,games_id,'.$gameID],
            'column_name' => ['required'],
            'layer' => ['required', Rule::notIn(['0'])],
        ]);
        
        $game = Game::findorfail($gameID);

        if (request('player_related')){

            if ($data['layer'] == "single"){

                $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$data['data_name']));

                if (!Schema::hasTable($GameDataTable)) {
                    Schema::create($GameDataTable, function (Blueprint $table) {

                        $table->id();
                        $table->string(strtolower(str_replace(' ','_',request('column_name'))))->required();
                        $table->string('file')->required();
                        $table->unsignedBigInteger('players_id')->required()->unique();
                        $table->unsignedBigInteger('data_id')->required();
                        $table->timestamps();

                        $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
                        $table->index('players_id');

                        $table->foreign('data_id')->references('id')->on('game_data_types')->onDelete('cascade');
                        $table->index('data_id');
                    });
                }   

                $GameDataModelName = str_replace(' ', '',$game->game_name).str_replace(' ', '',$data['data_name']);

                Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
                Artisan::call('krlove:generate:model Player --table-name="players"');
                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                GameDataType::create([
                    'data_name' => $data['data_name'],
                    'layer' => $data['layer'],
                    'player_related' => '1',
                    'games_id' => $gameID,
                ]);

                return redirect('/game/'.$gameID);

            } elseif ($data['layer'] == "double"){

                $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$data['data_name']));

                $GameDataFileTable = $GameDataTable."_files";

                if (!Schema::hasTable($GameDataTable)) {
                    Schema::create($GameDataTable, function (Blueprint $table) {

                        $table->id();
                        $table->string(strtolower(str_replace(' ','_',request('column_name'))))->required();
                        $table->unsignedBigInteger('players_id')->required()->unique();
                        $table->unsignedBigInteger('data_id')->required();
                        $table->timestamps();

                        $table->foreign('players_id')->references('id')->on('players')->onDelete('cascade');
                        $table->index('players_id');

                        $table->foreign('data_id')->references('id')->on('game_data_types')->onDelete('cascade');
                        $table->index('data_id');
                    });
                }

                if (!Schema::hasTable($GameDataFileTable)) {
                    Schema::create($GameDataFileTable, function (Blueprint $table) {

                        $game = Game::findorfail(request()->route('gameID'));

                        $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',request('data_name')));

                        $foreigncolumn = strtolower(str_replace(' ','_',request('data_name'))).'_id';

                        $table->id();
                        $table->string('file')->required();
                        $table->string('type')->required();
                        $table->unsignedBigInteger($foreigncolumn)->required();
                        $table->timestamps();

                        $table->foreign($foreigncolumn)->references('id')->on($GameDataTable)->onDelete('cascade');
                        $table->index($foreigncolumn);
                    });
                }

                $GameDataModelName = str_replace(' ', '',$game->game_name).str_replace(' ', '',$data['data_name']);
                $GameDataFileModelName = $GameDataModelName."File";

                Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
                Artisan::call('krlove:generate:model '.$GameDataFileModelName.' --table-name='.$GameDataFileTable.'');
                Artisan::call('krlove:generate:model Player --table-name="players"');
                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                GameDataType::create([
                    'data_name' => $data['data_name'],
                    'layer' => $data['layer'],
                    'player_related' => '1',
                    'games_id' => $gameID,
                ]);

                return redirect('/game/'.$gameID);
            }

        } else {
        
            if ($data['layer'] == "single"){

                $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$data['data_name']));

                if (!Schema::hasTable($GameDataTable)) {
                    Schema::create($GameDataTable, function (Blueprint $table) {

                        $table->id();
                        $table->string(strtolower(str_replace(' ','_',request('column_name'))))->nullable()->unique();
                        $table->string('file')->required();
                        $table->unsignedBigInteger('data_id')->required();
                        $table->timestamps();

                        $table->foreign('data_id')->references('id')->on('game_data_types')->onDelete('cascade');
                        $table->index('data_id');
                    });
                }   

                $GameDataModelName = str_replace(' ', '',$game->game_name).str_replace(' ', '',$data['data_name']);

                Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                GameDataType::create([
                    'data_name' => $data['data_name'],
                    'layer' => $data['layer'],
                    'player_related' => '0',
                    'games_id' => $gameID,
                ]);

                return redirect('/game/'.$gameID);

            } elseif ($data['layer'] == "double"){

                $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$data['data_name']));

                $GameDataFileTable = $GameDataTable."_files";

                if (!Schema::hasTable($GameDataTable)) {
                    Schema::create($GameDataTable, function (Blueprint $table) {

                        $table->id();
                        $table->string(strtolower(str_replace(' ','_',request('column_name'))))->nullable()->unique();
                        $table->unsignedBigInteger('data_id')->required();
                        $table->timestamps();

                        $table->foreign('data_id')->references('id')->on('game_data_types')->onDelete('cascade');
                        $table->index('data_id');
                    });
                }

                if (!Schema::hasTable($GameDataFileTable)) {
                    Schema::create($GameDataFileTable, function (Blueprint $table) {

                        $game = Game::findorfail(request()->route('gameID'));

                        $GameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',request('data_name')));

                        $foreigncolumn = strtolower(str_replace(' ','_',request('data_name'))).'_id';

                        $table->id();
                        $table->string('file')->required();
                        $table->string('type')->required();
                        $table->string('column_folder')->required();
                        $table->unsignedBigInteger($foreigncolumn)->required();
                        $table->timestamps();

                        $table->foreign($foreigncolumn)->references('id')->on($GameDataTable)->onDelete('cascade');
                        $table->index($foreigncolumn);
                    });
                }

                $GameDataModelName = str_replace(' ', '',$game->game_name).str_replace(' ', '',$data['data_name']);
                $GameDataFileModelName = $GameDataModelName."File";

                Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
                Artisan::call('krlove:generate:model '.$GameDataFileModelName.' --table-name='.$GameDataFileTable.'');
                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                GameDataType::create([
                    'data_name' => $data['data_name'],
                    'layer' => $data['layer'],
                    'player_related' => '0',
                    'games_id' => $gameID,
                ]);

                return redirect('/game/'.$gameID);

            }
            
        }
    }

    public function delete(Request $Request, $gameID)
    {
         
        $checked = $Request->remove_data;

        $game = Game::find($gameID);

        $datatypeID = collect($checked);

        $gameDataType = GameDataType::find($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        foreach ($gameDataType as $gameDataType){

            if($gameDataType->layer == "single"){

                $GameDataModelName = $GameModelName.str_replace(' ', '',$gameDataType->data_name);

                $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

                $GameDataModel = app_path("/Models/".$GameDataModelName.".php");

                if(file_exists($GameDataModel)){

                    unlink($GameDataModel);

                }

                Schema::dropIfExists(''.$GameDataTable.'');

                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                $path = public_path('storage/uploads/'.$game->game_name.'/'.$gameDataType->data_name);

                File::deleteDirectory($path);

            } else {

                $GameDataModelName = $GameModelName.str_replace(' ', '',$gameDataType->data_name);

                $GameDataFileModelName = $GameDataModelName.'File';

                $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

                $GameDataFileTable = $GameDataTable.'_files';

                $GameDataModel = app_path("/Models/".$GameDataModelName.".php");

                $GameDataFileModel = app_path("/Models/".$GameDataFileModelName.".php");

                if(file_exists($GameDataModel)){

                    unlink($GameDataModel);

                }

                if(file_exists($GameDataFileModel)){

                    unlink($GameDataFileModel);

                }

                Schema::dropIfExists(''.$GameDataFileTable.'');

                Schema::dropIfExists(''.$GameDataTable.'');

                Artisan::call('krlove:generate:model GameDataType --table-name="game_data_types"');

                $path = public_path('storage/uploads/'.$game->game_name.'/'.$gameDataType->data_name);

                File::deleteDirectory($path);
            }

        }

        GameDataType::whereIn('id',$datatypeID)->delete();

        return redirect('game/'.$gameID);

    }
}
