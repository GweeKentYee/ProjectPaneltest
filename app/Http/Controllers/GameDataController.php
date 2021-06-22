<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDataType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isEmpty;

class GameDataController extends Controller
{
    public function __construct()
    {
        //Prevent access without authentication

        $this->middleware('auth');

    }
    
    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    public function readFile(Request $request){

        $data = $request->validate([
            'game_id' => ['required','exists:games,id'],  
            'data_id' => ['required','exists:game_data_types,id'] 
        ]);

        $game = Game::find($data['game_id']);

        $gameDataType = GameDataType::find($data['data_id']);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataTable = strtolower(str_replace(' ', '_',$game->game_name)).'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special))->implode(1);

        $data2 = $request->validate([
            $special_columns => ['required', 'exists:'.$GameDataTable.','.$special_columns.'']
        ]);
        
        $datafile = $GameDataModel::select('file')->where($special_columns,$data2[$special_columns])->first();

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

    public function display($gameID,$datatypeID)
    {
        //Show Game Data Details Page (According to game)

        $game = Game::findorfail($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $gameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$gameDataType->data_name));

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($gameDataTable);

        if (in_array("players_id", $allcolumns)){

            $exclude_columns = ['id', 'file','players_id', 'data_id', 'created_at', 'updated_at'];

            $exclude_columns2 = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

        } else {

            $exclude_columns = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

            $exclude_columns2 = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        }

        $columns = array_diff($allcolumns, $exclude_columns); 

        $special_columns = array_diff($allcolumns, $exclude_columns2);
    
        return view('GameData',[
            'games'=> $game,
            'gamedatatype' => $gameDataType,
            'columns' => $columns,
            'checking' => array_map('ucfirst', $allcolumns),
            'specialcolumn' => $special_columns,
            'specialcolumn2' => $special_columns,
            'specialcolumn3' => $special_columns,
            'columnlist' => $special_columns
            ]);  

    }

    public function OneLayerAddColumn($gameID, $datatypeID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataModelName = $GameModelName.str_replace(' ', '',$gameDataType->data_name);

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        $data = request()->validate([
            'new_column' => ['required'],
        ]);
        
        if (!Schema::hasColumn($GameDataTable, $data['new_column'])){

            if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){

                Schema::table($GameDataTable, function($table) {
                    $table->string(strtolower(str_replace(' ','_',request('new_column'))))->required();
                });

            } else {

                Schema::table($GameDataTable, function($table) {
                    $table->string(strtolower(str_replace(' ','_',request('new_column'))))->nullable()->unique();
                });

            }

        }

        Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');

        return redirect('data/' .$gameID.'/'. $datatypeID);
        
    }

    public function OneLayerRemoveColumn(Request $Request, $gameID, $datatypeID)
    {
        $checked = $Request->remove_column;

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataModelName = $GameModelName.str_replace(' ', '',$gameDataType->data_name);

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        Schema::table($GameDataTable, function($table) {

            $checked = request()->remove_column;
            $table->dropColumn($checked);
        });

        Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
        
        return redirect('data/' .$gameID.'/'. $datatypeID);
        
    }

    public function OneLayerAddDataFile($gameID, $datatypeID){

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower(str_replace(' ', '_',$gameDataType->data_name));
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){
            
            $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 
                'data_file' => ['required','mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
                // 'players_id' => ['required','unique:'.$GameDataTable.',players_id',
                //     Rule::exists('players','id')->where(function ($query) {
                //         return $query->where('games_id', request()->route('gameID'));
                //     }),
                // ],
                'players_id' => ['required'],
            ];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.',NULL,id,players_id,'.request('players_id').''];
            }
                
            $data = $this->validate(request(), $validationArray);

        } else {

            $exclude_columns = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 'data_file' => ['required','mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg']];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.''];
            }
                
            $data = $this->validate(request(), $validationArray);

        }

        $input = collect($data)->except(['data_file'])->filter()->all();

        $dataname = $gameDataType->data_name;

        $gamefile = $game->game_name;

        $directory = $gamefile . '/' . $dataname;

        $filename = request()->file('data_file')->getClientOriginalName();

        $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);

        $storepath = [
            'file' => str_replace('\\','/',$filepath),
            'data_id' => $datatypeID
        ];

        $storedata = array_merge($input, $storepath);

        $GameDataModel::insert($storedata);

        return redirect('data/' .$gameID.'/'. $datatypeID);

    }

    public function OneLayerViewFile($gameID, $datatypeID, $dataID){
        
        //View a data file (Panel)

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $Data = $GameDataModel::findorfail($dataID);

        $file = public_path($Data->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function EditPage($gameID, $datatypeID, $dataID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $Data = $GameDataModel::findorfail($dataID);

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        if (in_array("players_id", $datatypecolumns)){

            $exclude_for_special = ['id', 'players_id', 'file', 'data_id', 'created_at', 'updated_at'];

        } else {

            $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        }

        $special_columns = array_diff($datatypecolumns, $exclude_for_special);

        return view ('EditDataFile', [
            'games' => $game,
            'gamedatatype'=> $gameDataType,
            'gamedata'=> $Data,
            'specialcolumn' => $special_columns,
            'checking' => array_map('ucfirst', $datatypecolumns),
            ]);
    }

    public function OneLayerDownloadFile($file1,$file2,$file3,$file4,$file5){

        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5);
        
        return response()->download($path);

    }

    public function OneLayerEdit($gameID, $datatypeID, $dataID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $Data = $GameDataModel::findorfail($dataID);

        //

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){

            $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            if(request('players_id')){

                $validationArray = [ 
                    'data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
                    'players_id' => ['unique:'.$GameDataTable.',players_id,'.$dataID.',id',
                        Rule::exists('players','id')->where(function ($query) {
                            return $query->where('games_id', request()->route('gameID'));
                        }),
                    ],
                ];

            } else {

                $validationArray = [ 
                    'data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
                ];

            }

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = [
                    Rule::unique(''.$GameDataTable.'',''.$special_columns.'')->where(function ($query) use ($special_columns) {
                        return $query->where(''.$special_columns.'','!=', null)->where('id','!=',request()->route('dataID'))->where('players_id',request('players_id'));
                    }), ];
            }
                
            $data = $this->validate(request(), $validationArray);

        } else {

            $exclude_columns = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 'data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg']];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['unique:'.$GameDataTable.','.$special_columns.','.$dataID.',id'];
            }
                
            $data = $this->validate(request(), $validationArray);

        }  

        $input = collect($data)->filter()->all();

        if(!empty($input)) {

            if(request('data_file')){

                $inputWithOutFile = collect($data)->except('data_file')->filter()->all();

                $dataname = $gameDataType->data_name;
    
                $gamefile = $game->game_name;
    
                $directory = $gamefile . '/' . $dataname;
                
                $filename = request()->file('data_file')->getClientOriginalName();
    
                $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);
    
                $updatepath = [
                    'file' => str_replace('\\','/',$filepath),
                ];
    
                $updatedata = array_merge($inputWithOutFile, $updatepath);

                unlink($Data->file);
                
                $Data->update($updatedata);
    
                return redirect('/data/' . $gameID.'/'.$datatypeID);
    
            } else {
                
                $Data->update($input);
    
                return redirect('/data/' . $gameID.'/'.$datatypeID);
            }

        } else {

            Session::flash('field_empty', 'Please fill in at least one field.');

            return redirect('data/editpage/' . $gameID.'/'.$datatypeID.'/'.$dataID);

        }

    }

    public function OneLayerDeleteDataFile($gameID, $datatypeID, $dataID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $DataFile = $GameDataModel::findorfail($dataID);

        $file = $DataFile->file;

        $filepath = str_replace('\\','/',public_path($file));

        if ($file == null){

            $GameDataModel::where('id', $dataID)->delete();

            return redirect('data/'.$gameID.'/'.$datatypeID);

        } else {
            
            if(file_exists($filepath)){

                unlink($filepath);

                $GameDataModel::where('id', $dataID)->delete();
    
                return redirect('data/'.$gameID.'/'.$datatypeID);

            } else{

                $GameDataModel::where('id', $dataID)->delete();

                return redirect('data/'.$gameID.'/'.$datatypeID);
            }   

        }

    }

    public function TwoLayerRemoveColumn(Request $Request, $gameID, $datatypeID)
    {
        $checked = $Request->remove_column;

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameDataModelName = $GameModelName.str_replace(' ', '',$gameDataType->data_name);

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        Schema::table($GameDataTable, function($table) {

            $checked = request()->remove_column;
            $table->dropColumn($checked);

        });

        foreach ($checked as $checked){

            $GameDataFileModel::where('column_folder', $checked)->delete();

            $path = 'storage/uploads/'.$game->game_name.'/'.str_replace(' ', '',$gameDataType->data_name).'/'.$checked;

            File::deleteDirectory($path);

        }

        Artisan::call('krlove:generate:model '.$GameDataModelName.' --table-name='.$GameDataTable.'');
        
        return redirect('data/' .$gameID.'/'. $datatypeID);
        
    }

    public function TwoLayerAddData($gameID, $datatypeID){

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){
            
            $exclude_columns = ['id', 'file', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [ 
                'players_id' => ['required','unique:'.$GameDataTable.',players_id',
                    Rule::exists('players','id')->where(function ($query) {
                        return $query->where('games_id', request()->route('gameID'));
                    }),
                ],
            ];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.''];
            }
                
            $data = $this->validate(request(), $validationArray);

        } else {

            $exclude_columns = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = ['required','unique:'.$GameDataTable.','.$special_columns.''];
            }
                
            $data = $this->validate(request(), $validationArray);

        }

        $storedataID = [
            'data_id' => $datatypeID
        ];

        $storedata = array_merge($data,$storedataID);
        
        $GameDataModel::create($storedata);

        return redirect('data/' .$gameID.'/'. $datatypeID);
    }

    public function TwoLayerEditData($gameID, $datatypeID, $dataID){

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $gameData = $GameDataModel::findorfail($dataID);

        //

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){

            $exclude_columns = ['id', 'players_id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            if(request('players_id')){

                $validationArray = [ 
                    'players_id' => ['unique:'.$GameDataTable.',players_id,'.$dataID.',id',
                        Rule::exists('players','id')->where(function ($query) {
                            return $query->where('games_id', request()->route('gameID'));
                        }),
                    ],
                ];

                foreach($special_columns as $special_columns)
                {
                    $validationArray[$special_columns] = [
                        Rule::unique(''.$GameDataTable.'',''.$special_columns.'')->where(function ($query) use ($special_columns) {
                            return $query->where(''.$special_columns.'','!=', null)->where('id','!=',request()->route('dataID'));
                        }), ];
                }
                    
                $data = $this->validate(request(), $validationArray);

            } else {

                $validationArray = [];

                foreach($special_columns as $special_columns)
                {
                    // $validationArray[$special_columns] = ['unique:'.$GameDataTable.','.$special_columns.','.$dataID.',id'];
                    $validationArray[$special_columns] = [
                        Rule::unique(''.$GameDataTable.'',''.$special_columns.'')->where(function ($query) use ($special_columns) {
                            return $query->where(''.$special_columns.'','!=', null)->where('id','!=',request()->route('dataID'));
                        }), ];
                }
                    
                $data = $this->validate(request(), $validationArray);

            }

        } else {

            $exclude_columns = ['id', 'data_id', 'created_at', 'updated_at'];

            $special_columns = array_diff($allcolumns, $exclude_columns);

            $validationArray = [];

            foreach($special_columns as $special_columns)
            {
                $validationArray[$special_columns] = [
                Rule::unique(''.$GameDataTable.'',''.$special_columns.'')->where(function ($query) use ($special_columns) {
                    return $query->where(''.$special_columns.'','!=', null)->where('id','!=',request()->route('dataID'));
                }), ];
            }
                
            $data = $this->validate(request(), $validationArray);

        }
        
        $input = collect($data)->filter()->all();

        $column_folder = collect($data)->filter()->keys();

        if(!empty($input)){

            $GameDataFileModel = $GameDataModel."File";

            foreach($column_folder as $column_folder){

                $GameDataFileModel::where('column_folder', $column_folder)->delete();
                
                $path = 'storage/uploads/'.$game->game_name.'/'.str_replace(' ', '',$gameDataType->data_name).'/'.$column_folder.'-'.$gameData->$column_folder;

                File::deleteDirectory($path);

            }

            $gameData->update($input);

            return redirect('/data/' . $gameID.'/'.$datatypeID);
            
        } else {

            Session::flash('field_empty', 'Please fill in at least one field.');

            return redirect('data/editpage/' . $gameID.'/'.$datatypeID.'/'.$dataID);

        }

    }

    public function TwoLayerDeleteData($gameID, $datatypeID, $dataID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $gameData = $GameDataModel::findorfail($dataID);

        //

        $datacolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = array_diff($datacolumns, $exclude_for_special);

        //

        foreach ($special_columns as $special_columns){

            $path = 'storage/uploads/'.$game->game_name.'/'.str_replace(' ', '',$gameDataType->data_name).'/'.$special_columns.'/'.$gameData->$special_columns;

            File::deleteDirectory($path);
        
        }

        $GameDataModel::where('id', $dataID)->delete();
        
        return redirect('/data/' .$gameID.'/'.$datatypeID);

    }

}
