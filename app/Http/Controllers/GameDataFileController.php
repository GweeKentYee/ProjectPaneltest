<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDataType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

class GameDataFileController extends Controller
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

        $GameDataFileTable = $GameDataTable."_files";

        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        //

        $datatypecolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        $exclude_for_special = ['id', 'file', 'data_id', 'created_at', 'updated_at'];

        $special_columns = collect(array_diff($datatypecolumns, $exclude_for_special))->implode(1);

        $data2 = $request->validate([
            $special_columns => ['required', 'exists:'.$GameDataTable.','.$special_columns.''],
            'file_type' => ['required', 'exists:'.$GameDataFileTable.',type']
        ]);

        $gamedata = $GameDataModel::select('id')->where($special_columns,$data2[$special_columns])->first();

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

    public function display($gameID, $datatypeID, $dataID){

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $gameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$gameDataType->data_name));

        $gameDataFileTable = $gameDataTable.'_files';

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $gameData = $GameDataModel::findorfail($dataID);

        //

        $datacolumns = DB::getSchemaBuilder()->getColumnListing($gameDataTable);

        if (in_array("players_id", $datacolumns)){

            $exclude_for_special = ['id', 'players_id', 'data_id', 'created_at', 'updated_at'];

        } else {

            $exclude_for_special = ['id', 'data_id', 'created_at', 'updated_at'];

        }

        $special_columns = collect(array_diff($datacolumns, $exclude_for_special))->implode(1);

        $column_option = array_diff($datacolumns, $exclude_for_special);

        //

        $allcolumns = DB::getSchemaBuilder()->getColumnListing($gameDataFileTable);

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';

        $exclude_columns = [$foreigncolumn, 'created_at', 'updated_at'];

        $columns = array_diff($allcolumns, $exclude_columns); 
        
        return view('GameDataFiles',[
            'games'=> $game,
            'gamedatatype' => $gameDataType,
            'gamedata' => $gameData,
            'columns' => array_map('ucfirst', $columns),
            'specialcolumn' => $special_columns,
            'column_option' => $column_option,
            'column_option_2' => $column_option,
            ]); 
    }

    public function TwoLayerAddFile($gameID, $datatypeID, $dataID){

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        $GameDataFileTable = $GameDataTable."_files";
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        $gameData = $GameDataModel::findorfail($dataID);

        //

        // $datacolumns = DB::getSchemaBuilder()->getColumnListing($GameDataTable);

        // if (Schema::hasColumn(''.$GameDataTable.'', 'players_id')){

        //     $exclude_for_special = ['id', 'players_id', 'data_id', 'created_at', 'updated_at'];

        // } else {

        //     $exclude_for_special = ['id', 'data_id', 'created_at', 'updated_at'];    

        // }

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';

        $data = request()->validate([
            'data_file' => ['required','mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            'file_type' => ['required','unique:'.$GameDataFileTable.',type,NULL,id,'.$foreigncolumn.','.$dataID],
        ]);

        $special_columns = request('folder_path');

        if ($special_columns == "none"){

            $dataname = $gameDataType->data_name;

            $gamefile = $game->game_name;

            $directory = $gamefile . '/' . $dataname;

            $filename = request()->file('data_file')->getClientOriginalName();

            $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);

            $GameDataFileModel::create([
                'file' => str_replace('\\','/',$filepath),
                'type' => $data['file_type'],
                'column_folder' => $special_columns,
                ''.$foreigncolumn.'' => $dataID
            ]);

        } else {

            if ($gameData->$special_columns == null){

                Session::flash('column_empty', 'The selected column value is empty.');

            }  else {

                $dataname = $gameDataType->data_name;

                $gamefile = $game->game_name;

                $directory = $gamefile . '/' . $dataname . '/' . $special_columns. '/' . $gameData->$special_columns;

                $filename = request()->file('data_file')->getClientOriginalName();

                $filepath = request('data_file')->move('storage/uploads/' . $directory ,$filename);

                $GameDataFileModel::create([
                    'file' => str_replace('\\','/',$filepath),
                    'type' => $data['file_type'],
                    'column_folder' => $special_columns,
                    ''.$foreigncolumn.'' => $dataID
                ]);

            }

        }

        return redirect('/data/twolayer/file/' . $gameID.'/'.$datatypeID.'/'.$dataID);

    }

    public function TwoLayerViewFile($gameID, $datatypeID, $dataID, $fileID){
        
        //View a data file (Panel)

        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        //

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        //

        $gameDataFile = $GameDataFileModel::findorfail($fileID);

        $file = public_path($gameDataFile->file);

        $fileInfo = pathinfo($file);

        if ($fileInfo['extension'] == 'json') {

            $content = file_get_contents($file);

            $data = json_decode($content, true);

            return $data;

        } else {

            return response()->download($file, '', [], 'inline');

        }
        
    }

    public function TwoLayerDownload($file1,$file2,$file3,$file4,$file5,$file6,$file7)
    {
        $path = public_path($file1 .'/'. $file2 .'/'. $file3 .'/'. $file4 .'/'. $file5 .'/'. $file6 .'/'. $file7);
        
        return response()->download($path);
    }

    public function TwoLayerReplaceFile($gameID, $datatypeID, $dataID, $fileID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        $GameDataFileTable = $GameDataTable."_files";
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        $gameData = $GameDataModel::findorfail($dataID);

        $gameDataFile = $GameDataFileModel::findorfail($fileID);

        //

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';

        $data = request()->validate([
            'replace_data_file' => ['mimetypes:application/json,application/xml,text/xml,text/plain,image/png,image/jpeg'],
            'type' => ['unique:'.$GameDataFileTable.',type,'.$fileID.',id,'.$foreigncolumn.','.$dataID],
        ]);

        $column_choice = request('replace_folder_path');

        $input = collect($data)->filter()->all();

        if(!empty($input)){
            
            if(request('replace_data_file')){

                $inputWithOutFile = collect($data)->except('replace_data_file')->filter()->all();

                $dataname = $gameDataType->data_name;

                $gamefile = $game->game_name;

                if ($column_choice == "none"){

                    $directory = $gamefile . '/' . $dataname;

                    $filename = request()->file('replace_data_file')->getClientOriginalName();

                    $filepath = request('replace_data_file')->move('storage/uploads/' . $directory ,$filename);

                    $updatepath = [
                        'file' => str_replace('\\','/',$filepath),
                        'column_folder' => $column_choice,
                    ];

                } elseif ($column_choice == "current"){

                    $special_columns = $gameDataFile->column_folder;

                    if ($special_columns == "none"){

                        $directory = $gamefile . '/' . $dataname;

                    } else {

                        $directory = $gamefile . '/' . $dataname . '/' . $special_columns. '/' . $gameData->$special_columns;
                    
                    }

                    $filename = request()->file('replace_data_file')->getClientOriginalName();

                    $filepath = request('replace_data_file')->move('storage/uploads/' . $directory ,$filename);

                    $updatepath = [
                        'file' => str_replace('\\','/',$filepath),
                        'column_folder' => $special_columns,
                    ];

                } else {

                    $directory = $gamefile . '/' . $dataname . '/' . $column_choice. '/' . $gameData->$column_choice;

                    $filename = request()->file('replace_data_file')->getClientOriginalName();

                    $filepath = request('replace_data_file')->move('storage/uploads/' . $directory ,$filename);

                    $updatepath = [
                        'file' => str_replace('\\','/',$filepath),
                        'column_folder' => $column_choice,
                    ];
                
                }
                
                $updatedata = array_merge($inputWithOutFile, $updatepath);

                $gameDataFile->update($updatedata);

            } else {

                $gameDataFile->update($input);

            }

            return redirect('data/twolayer/file/' .$gameID.'/'.$datatypeID.'/'.$dataID);

        } else {

            Session::flash('edit_empty_datafile', 'Please fill in at least one field.');

            return redirect('data/twolayer/file/' .$gameID.'/'.$datatypeID.'/'.$dataID);

        }

    }

    public function TwoLayerDeleteFile($gameID, $datatypeID, $dataID, $fileID)
    {
        $game = Game::find($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $GameTable = strtolower(str_replace(' ', '_',$game->game_name));

        $GameDataTable = $GameTable.'_'.strtolower($gameDataType->data_name);

        $GameDataFileTable = $GameDataTable."_files";
        
        $GameDataModel = $GameModel.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        $gameDataFile = $GameDataFileModel::findorfail($fileID);

        $file = $gameDataFile->file;

        $filepath = str_replace('\\','/',public_path($file));

        if ($file == null){

            $GameDataFileModel::where('id', $fileID)->delete();

            return redirect('data/twolayer/file/'.$gameID.'/'.$datatypeID.'/'.$dataID);

        } else {
            
            if(file_exists($filepath)){

                unlink($filepath);

                $GameDataFileModel::where('id', $fileID)->delete();

                return redirect('data/twolayer/file/'.$gameID.'/'.$datatypeID.'/'.$dataID);

            } else{

                $GameDataFileModel::where('id', $fileID)->delete();

                return redirect('data/twolayer/file/'.$gameID.'/'.$datatypeID.'/'.$dataID);
            }   

        }
    }

}
