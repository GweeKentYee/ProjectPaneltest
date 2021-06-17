<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDataType;
use App\Models\Player;
use App\Models\PlayerFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PlayerActionController extends Controller
{
    //API

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //Store Player File to own account

        $player = Player::find(auth('api_player')->user()->id);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            'type' => ['required', 'unique:'.$GameTable.',type,NULL,id,players_id,' .$player->id],
        ]);

        $directory = $gamefile . '/' . $playername;

        $filename = request()->file('player_file')->getClientOriginalName();

        $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
        
        return $GameModel::create([
            'file' => str_replace('\\','/',$filepath),
            'type' => $data['type'],
            'players_id' => $player->id
        ]);

    }

    public function write(Request $request)
    {
        $player = Player::find(auth('api_player')->user()->id);

        $playername = $player->player_name;

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        if (!$GameModel::where('type', $request['type'])->where('players_id', $player->id)->exists()){

            $data = $request->validate([
                'player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
                'type' => ['required', 'unique:'.$GameTable.',type,NULL,id,players_id,' .$player->id],
            ]);

            $directory = $gamefile . '/' . $playername;

            $filename = request()->file('player_file')->getClientOriginalName();

            $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);
            
            return $GameModel::create([
                'file' => str_replace('\\','/',$filepath),
                'type' => $data['type'],
                'players_id' => $player->id
            ]);

        } else {

            $data = $request->validate([
                'player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
                'type' => ['required', 'exists:'.$GameTable.',type'],
            ]);

            $playername = $player->player_name;

            $directory = $gamefile . '/' . $playername;

            $filename = request()->file('player_file')->getClientOriginalName();

            $filepath = request('player_file')->move('storage/uploads/' . $directory ,$filename);

            $playerfileInfo = $GameModel::select('id')->where('type', $data['type'])->first();

            $playerfile = $GameModel::findorfail($playerfileInfo->id);

            $playerfile->update([
                'file' => str_replace('\\','/',$filepath)
            ]);

            return $playerfile;

        }

    }

    public function show()
    {
        //Show account details

        return auth('api_player')->user();

    }

    public function readFile(Request $request)
    {
        //Read own player file (Require type)

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where('type', $data['type'])->first();
     
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

    public function readFileOthers(Request $request)
    {

        //Read others player file (Require playerID and type)

        $data = $request->validate([
            'player_id' =>  ['required',
            Rule::exists('players','id')->where(function ($query) {
                return $query->where('games_id', auth('api_player')->user()->games_id);
            }),],  
        ]);

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data2 = $request->validate([
            'type' => ['required', 
            Rule::exists(''.$GameTable.'','type')->where(function ($query) {
                return $query->where('players_id', request('player_id'));
            }),],
        ]);

        $playerfile = $GameModel::select('file')->where('type', $data2['type'])->where('players_id',$data['player_id'])->first();
     
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

    public function randomPlayerFile(Request $request)
    {
        //Read random player file (Require type)

        $game = Game::findorfail(auth('api_player')->user()->games_id);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
        ]);

        $playerfile = $GameModel::select('file')->where([

            ['type', $data['type']],

        ])->inRandomOrder()->first();
     
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

    public function update(Request $request)
    {
        //Update own player file (Require type)

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $gamefile = $game->game_name;

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = lcfirst(str_replace(' ', '_',$game->game_name));

        $data = $request->validate([
            'type' => ['required', 'exists:'.$GameTable.',type'],
            'new_player_file' => ['required', 'mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
        ]);

        $playername = $player->player_name;

        $directory = $gamefile . '/' . $playername;

        $filename = request()->file('new_player_file')->getClientOriginalName();

        $filepath = request('new_player_file')->move('storage/uploads/' . $directory ,$filename);

        $playerfileInfo = $GameModel::select('id')->where('type', $data['type'])->first();

        $playerfile = $GameModel::findorfail($playerfileInfo->id);

        $playerfile->update([
            'file' => str_replace('\\','/',$filepath)
        ]);

        return $playerfile;
    }

    public function destroy($id)
    {
        //
    }

    public function OneLayerReadData(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '0')->where('layer','single')->where('games_id',auth('api_player')->user()->games_id);
                })
            ],
            'column' => ['required']
        ]);

        $game = Game::findorfail(auth('api_player')->user()->games_id);

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special));
        
        if($special_columns->contains($data['column'])){

            $data2 = $request->validate([
                $data['column'] => ['required', 'exists:'.$GameDataTable.','.$data['column'].'']
            ]);

        } else {

            $response = ['message' =>  'This column does not exist in the selected data table.'];

            return response($response, 200);
        }
        
        $datafile = $GameDataModel::select('file')->where($data['column'],$data2[$data['column']])->first();

        $file = public_path($datafile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function TwoLayerReadData(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '0')->where('layer','double')->where('games_id',auth('api_player')->user()->games_id);
                })
            ],
            'column' => ['required']
        ]);

        $game = Game::findorfail(auth('api_player')->user()->games_id);

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataFileTable = $GameDataTable."_files";

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special));
        
        if($special_columns->contains($data['column'])){

            $data2 = $request->validate([
                $data['column'] => ['required', 'exists:'.$GameDataTable.','.$data['column'].''],
            ]);

        } else {

            $response = ['message' =>  'This column does not exist in the selected data table.'];

            return response($response, 200);
        }

        $gamedata = $GameDataModel::select('id')->where($data['column'],$data2[$data['column']])->first();

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';

        $data3 = $request->validate([
            'file_type' => ['required',
            Rule::exists(''.$GameDataFileTable.'','type')->where(function ($query) use ($foreigncolumn, $gamedata) {
                return $query->where(''.$foreigncolumn.'',$gamedata->id);
            })]
        ]);
        
        $datafile = $GameDataFileModel::select('file')->where([

            ['type', $data3['file_type']],
            [$foreigncolumn, $gamedata->id]

        ])->first();

        $file = public_path($datafile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
    
    }

    public function OneLayerReadPlayerData(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '1')->where('layer','single');
                })
            ],
            'column' => ['required']
        ]);

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special));
        
        if($special_columns->contains($data['column'])){

            $data2 = $request->validate([
                $data['column'] => ['required', 'exists:'.$GameDataTable.','.$data['column'].'']
            ]);

        } else {

            $response = ['message' =>  'This column does not exist in the selected data table.'];

            return response($response, 200);
        }
        
        $datafile = $GameDataModel::select('file')->where($data['column'],$data2[$data['column']])->first();

        $file = public_path($datafile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function OneLayerReadPlayerDataOther(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '1')->where('layer','single');
                })
            ],
            'column' => ['required'],
            'player_id' =>  ['required','exists:players,id'],  
        ]);

        $player = Player::findorfail($data['player_id']);

        $game = $player->game;

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special));
        
        if($special_columns->contains($data['column'])){

            $data2 = $request->validate([
                $data['column'] => ['required', 'exists:'.$GameDataTable.','.$data['column'].'']
            ]);

        } else {

            $response = ['message' =>  'This column does not exist in the selected data table.'];

            return response($response, 200);
        }
        
        $datafile = $GameDataModel::select('file')->where($data['column'],$data2[$data['column']])->first();

        $file = public_path($datafile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function TwoLayerReadPlayerData(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '1')->where('layer','double');
                })
            ],
            'column' => ['required']
        ]);
        
        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataFileTable = $GameDataTable."_files";

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special));
        
        if($special_columns->contains($data['column'])){

            $data2 = $request->validate([
                $data['column'] => ['required', 'exists:'.$GameDataTable.','.$data['column'].''],
                'file_type' => ['required', 'exists:'.$GameDataFileTable.',type']
            ]);

        } else {

            $response = ['message' =>  'This column does not exist in the selected data table.'];

            return response($response, 200);
        }

        // $data2 = $request->validate([
        //     $special_columns => ['required', 'exists:'.$GameDataTable.','.$special_columns.''],
        //     'file_type' => ['required', 'exists:'.$GameDataFileTable.',type']
        // ]);

        $gamedata = $GameDataModel::select('id')->where($data['column'],$data2[$data['column']])->first();

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';
        
        $datafile = $GameDataFileModel::select('file')->where([

            ['type', $data2['file_type']],
            [$foreigncolumn, $gamedata->id]

        ])->first();

        $file = public_path($datafile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
    
    }

    public function OneLayerWritePlayerData(Request $request){

        $data = $request->validate([
            'data_id' => ['required',
                Rule::exists('game_data_types','id')->where(function ($query) {
                    return $query->where('player_related', '1')->where('layer','single');
                })
            ],
        ]);

        $player = Player::find(auth('api_player')->user()->id);

        $game = $player->game;

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        if ($GameDataModel::where('players_id', '=', $player->id)->exists()) {

            // Record of this player exist

            $dataID = $GameDataModel::select('id')->where('players_id', $player->id)->first();

            $gameData = $GameDataModel::findorfail($dataID->id);

            $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

            $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 
                'data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            ];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['unique:'.$GameDataTable.','.$special_columns.','.$dataID->id.',id'];
            }
                
            $data1 = $this->validate(request(), $validationArray);

            $input = collect($data1)->filter()->all();

            if(!empty($input)) {

                if(request('data_file')){

                    $inputWithOutFile = collect($data1)->except('data_file')->filter()->all();

                    $dataname = $gameDataType->data_name;
        
                    $gamefile = $game->game_name;
        
                    $directory = $gamefile . '/' . $dataname;
                    
                    $filename = request()->file('data_file')->getClientOriginalName();
        
                    $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);
        
                    $updatepath = [
                        'file' => str_replace('\\','/',$filepath),
                    ];
        
                    $updatedata = array_merge($inputWithOutFile, $updatepath);
                    
                    $gameData->update($updatedata);

                    return $gameData;
        
                } else {
                    
                    $gameData->update($input);

                    return $gameData;

                }

            } else {

                $response = ['message' =>  'No field was given'];

                return response($response, 200);

            }


        } else {

            // Record of this player doesn't exist

            $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);
            
            $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 
                'data_file' => ['required','mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            ];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.''];
            }
                
            $data1 = $this->validate(request(), $validationArray);

            $input = collect($data1)->except(['data_file'])->filter()->all();

            $dataname = $gameDataType->data_name;

            $gamefile = $game->game_name;

            $directory = $gamefile . '/' . $dataname;

            $filename = request()->file('data_file')->getClientOriginalName();

            $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);

            $storepath = [
                'file' => str_replace('\\','/',$filepath),
                'data_id' => $data['data_id'],
                'players_id' => $player->id
            ];

            $storedata = array_merge($input, $storepath);

            return $GameDataModel::create($storedata);

        }
        
    }

    // public function TwoLayerWritePlayerData(Request $request){

    //     $data = $request->validate([
    //         'data_id' => ['required',
    //             Rule::exists('game_data_types','id')->where(function ($query) {
    //                 return $query->where('player_related', '1')->where('layer','double');
    //             })
    //         ],
    //     ]);

    //     $player = Player::find(auth('api_player')->user()->id);

    //     $game = $player->game;

    //     $gameDataType = GameDataType::find($data['data_id']);

    //     $GameModelName = str_replace(' ', '',$game->game_name);

    //     $GameModel = "App\\Models\\".$GameModelName;

    //     $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

    //     $GameDataFileTable = $GameDataTable."_files";

    //     $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

    //     $GameDataFileModel = $GameDataModel."File";

    //     if ($GameDataModel::where('players_id', '=', $player->id)->exists()) {

    //         // Record of this player exist

    //         // $dataID = $GameDataModel::select('id')->where('players_id', $player->id)->first();

    //         // $gameData = $GameDataModel::findorfail($dataID->id);

    //         // $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

    //         // $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

    //         // $special_columns = array_diff($allcolumns, $exclude_columns);

    //         // $validationArray = [ 
    //         //     'data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
    //         // ];

    //         // foreach($special_columns as $special_columns)
    //         // {
    //         //     $validationArray[$special_columns] = ['unique:'.$GameDataTable.','.$special_columns.','.$dataID->id.',id'];
    //         // }
                
    //         // $data1 = $this->validate(request(), $validationArray);

    //         // $input = collect($data1)->filter()->all();

    //         // if(!empty($input)) {

    //         //     if(request('data_file')){

    //         //         $inputWithOutFile = collect($data1)->except('data_file')->filter()->all();

    //         //         $dataname = $gameDataType->data_name;
        
    //         //         $gamefile = $game->game_name;
        
    //         //         $directory = $gamefile . '/' . $dataname;
                    
    //         //         $filename = request()->file('data_file')->getClientOriginalName();
        
    //         //         $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);
        
    //         //         $updatepath = [
    //         //             'file' => str_replace('\\','/',$filepath),
    //         //         ];
        
    //         //         $updatedata = array_merge($inputWithOutFile, $updatepath);
                    
    //         //         $gameData->update($updatedata);

    //         //         return $gameData;
        
    //         //     } else {
                    
    //         //         $gameData->update($input);

    //         //         return $gameData;

    //         //     }

    //         // } else {

    //         //     $response = ['message' =>  'No field was given'];

    //         //     return response($response, 200);

    //         // }

    //     } else {

    //         // Record of this player doesn't exist

    //         // $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);
            
    //         // $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

    //         // $special_columns = array_diff($allcolumns, $exclude_columns);

    //         // $validationArray = [ 
    //         //     'data_file' => ['required','mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
    //         // ];

    //         // foreach($special_columns as $special_columns)
    //         // {
    //         //     $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.''];
    //         // }
                
    //         // $data1 = $this->validate(request(), $validationArray);

    //         // $input = collect($data1)->except(['data_file'])->filter()->all();

    //         // $dataname = $gameDataType->data_name;

    //         // $gamefile = $game->game_name;

    //         // $directory = $gamefile . '/' . $dataname;

    //         // $filename = request()->file('data_file')->getClientOriginalName();

    //         // $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);

    //         // $storepath = [
    //         //     'file' => str_replace('\\','/',$filepath),
    //         //     'data_id' => $data['data_id'],
    //         //     'players_id' => $player->id
    //         // ];

    //         // $storedata = array_merge($input, $storepath);

    //         // return $GameDataModel::create($storedata);

    //     }

    // }
    
}
