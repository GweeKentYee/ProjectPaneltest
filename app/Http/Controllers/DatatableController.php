<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameDataType;
use App\Models\Player;
use App\Models\PlayerFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatatableController extends Controller
{
    public function SubAccounts(){

        // Sub-Accounts Datatable

        $query = User::select('username', 'id')->where('is_admin', '0');

        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/allplayer/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href =  "/account/games/'.$query->id.'">Games</a>
                                    <a class= "btn btn-danger btn-sm delete" href= "/account/delete/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);

    }

    public function accountGames($id){

        // Sub-Account's Games Datatable

        $query = Game::select('game_name', 'id', 'users_id')->where('users_id', $id);

        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Game', function($query){

                    $Game = '<a href="/game/'.$query->id.'">'.$query->game_name.'</a>';
                    return $Game;
                })

                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/allplayer/edit/'.$query->id.'">Edit</a>
                                    '<a class= "btn btn-danger btn-sm delete" href= "/account/game/delete/'.$query->users_id.'/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action','Game'])
                ->make(true);

    }
    
    public function allPlayers(){

        // All Players Datatable

        $query = Player::select('player_name', 'games_id', 'id');

        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/allplayer/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href =  "/playerfile/'.$query->games_id.'/'.$query->id.'">Player File</a>
                                    <a class= "btn btn-danger btn-sm delete" href= "/allplayer/delete/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);

    }

    public function gamelist(){

        //Game List Datatable 

        $query = Game::select('id','game_name');
        return datatables($query)->make(true);

    }

    public function getPlayers($id){

        //Players Datatable (According to game)
        
        $query = Player::select('player_name', 'id', 'games_id')->where('games_id', $id);
        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/player/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href = "/playerfile/'.$query->games_id.'/'.$query->id.'">Player File</a>
                                    <a class = "btn btn-danger btn-sm delete" href = "/player/delete/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);
        
    }

    public function PlayerFile($gameID, $playerID){

        //Player Files Datatable 

        $game = Game::findorfail($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameModel = "App\\Models\\".$GameModelName;

        $query = $GameModel::select('file', 'type', 'id', 'players_id')->where('players_id', $playerID);
        return datatables($query)
        ->addIndexColumn()
        ->addColumn('File', function($query){

            $players = Player::findorfail($query->players_id);

            $File= '<a href = "/playerfile/view/'.$players->game->id.'/'.$query->id.'">'.$query->file.'</a>';

            return $File;
        })

        ->addColumn('Action', function($query){
            
            //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
            $actionBtn = '<a href = "/playerfile/download/' .$query->file. '" class = "download btn btn-primary btn-sm">Download</a>
                            <a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.request()->route('gameID').'/'.$query->id.'">Edit</a>
                            <a class = "btn btn-danger btn-sm delete" href = "/playerfile/delete/'.request()->route('gameID').'/'.$query->id.'">Delete</a>'
                            ;
            return $actionBtn;

        })
        // ->addColumn('Api', function($query){
        //     $actionBtn = '<button class = "path btn btn-secondary btn-sm" data-toggle="modal" data-target="#url" data-path="'.$query->id.'">URL</button>';
        //     return $actionBtn;
        // })
        ->rawColumns(['File', 'Action'])
        ->make(true);

    }

    public function GameData($gameID, $datatypeID)
    {
        //Dynamic Game Data Datatable

        $game = Game::findorfail($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameDataModel = "App\\Models\\".$GameModelName.str_replace(' ', '',$gameDataType->data_name);

        $gameDataTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$gameDataType->data_name));

        $columns = DB::getSchemaBuilder()->getColumnListing($gameDataTable);

        $exclude_columns = ['data_id', 'created_at', 'updated_at'];

        $get_columns = array_diff($columns, $exclude_columns);

        $query = $GameDataModel::select($get_columns)->where('data_id', $datatypeID);
        
        if(in_array("file", $columns)){

            if(in_array("players_id", $columns)){

                return datatables($query)
                ->addColumn('File', function($query){

                    $game = Game::findorfail(request()->route('gameID'));

                    $gameDataType = GameDataType::findorfail(request()->route('datatypeID'));
        
                    $File= '<a href = "/data/onelayer/file/view/'.$game->id.'/'.$gameDataType->id.'/'.$query->id.'">'.$query->file.'</a>';
        
                    return $File;
                })
                ->addColumn('Players_id', function($query){
        
                    return $query->players_id;

                })
                ->addColumn('Action', function($query){
                    
                    //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
                    $actionBtn = '<a href = "/data/onelayer/file/download/' .$query->file. '" class = "download btn btn-primary btn-sm">Download</a>
                                    <a class = "btn btn-success btn-sm edit" href = "/data/editpage/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Replace</a>
                                    <a class = "btn btn-danger btn-sm delete" href = "/data/onelayer/file/delete/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['File','Players_id', 'Action'])
                ->make(true);

            } else {

                return datatables($query)
                ->addColumn('File', function($query){

                    $game = Game::findorfail(request()->route('gameID'));

                    $gameDataType = GameDataType::findorfail(request()->route('datatypeID'));
        
                    $File= '<a href = "/data/onelayer/file/view/'.$game->id.'/'.$gameDataType->id.'/'.$query->id.'">'.$query->file.'</a>';
        
                    return $File;
                })
                ->addColumn('Action', function($query){
                    
                    //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
                    $actionBtn = '<a href = "/data/onelayer/file/download/' .$query->file. '" class = "download btn btn-primary btn-sm">Download</a>
                                    <a class = "btn btn-success btn-sm edit" href = "/data/editpage/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Replace</a>
                                    <a class = "btn btn-danger btn-sm delete" href = "/data/onelayer/file/delete/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['File','Action'])
                ->make(true);

            }

        } else {

            if(in_array("players_id", $columns)){

                return datatables($query)
                ->addColumn('Players_id', function($query){
        
                    return $query->players_id;

                })
                ->addColumn('Action', function($query){
                    
                    //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
                    $actionBtn = '<a class = "btn btn-secondary btn-sm playerfile" href = "/data/twolayer/file/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Data File</a> 
                                    <a class = "btn btn-success btn-sm edit" href = "/data/editpage/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Edit</a>                 
                                    <a class = "btn btn-danger btn-sm delete" href = "/data/twolayer/delete/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Players_id','Action'])
                ->make(true);

            } else {

                return datatables($query)
                ->addColumn('Action', function($query){
                    
                    //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
                    $actionBtn = '<a class = "btn btn-secondary btn-sm playerfile" href = "/data/twolayer/file/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Data File</a> 
                                    <a class = "btn btn-success btn-sm edit" href = "/data/editpage/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Edit</a>                 
                                    <a class = "btn btn-danger btn-sm delete" href = "/data/twolayer/delete/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.$query->id.'">Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);

            }

        }

    }

    public function GameDataFile($gameID, $datatypeID, $dataID)
    {
        //Dynamic Game Data File Datatable

        $game = Game::findorfail($gameID);

        $gameDataType = GameDataType::findorfail($datatypeID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GameDataModel = "App\\Models\\".$GameModelName.str_replace(' ', '',$gameDataType->data_name);

        $GameDataFileModel = $GameDataModel."File";

        $gameDataTypeTable = strtolower(str_replace(' ','_',$game->game_name).'_'.str_replace(' ','_',$gameDataType->data_name));

        $gameDataFileTable = $gameDataTypeTable."_files";

        $columns = DB::getSchemaBuilder()->getColumnListing($gameDataFileTable);

        $foreigncolumn = strtolower(str_replace(' ','_',$gameDataType->data_name)).'_id';

        // $exclude_columns = [$foreigncolumn, 'created_at', 'updated_at'];

        // $get_columns = array_diff($columns, $exclude_columns);

        // $query = $GameDataFileModel::select($get_columns)->where($foreigncolumn, $dataID);

        $query = $GameDataFileModel::select('file', 'type', 'id', 'column_folder')->where($foreigncolumn, $dataID);

        return datatables($query)
        ->addColumn('File', function($query){

            $File= '<a href = "/data/twolayer/file/view/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.request()->route('dataID').'/'.$query->id.'">'.$query->file.'</a>';

            return $File;
        })
        
        ->addColumn('Action', function($query){
            
            //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
            $actionBtn = '<a href = "/data/twolayer/file/download/' .$query->file. '" class = "download btn btn-primary btn-sm">Download</a>
                            <a class = "btn btn-success btn-sm edit" data-toggle="modal" data-target="#replacefile" data-type="'.$query->type.'" data-url="'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.request()->route('dataID').'/'.$query->id.'">Replace</a>
                            <a class = "btn btn-danger btn-sm delete" href = "/data/twolayer/file/delete/'.request()->route('gameID').'/'.request()->route('datatypeID').'/'.request()->route('dataID').'/'.$query->id.'">Delete</a>'
                            ;
            return $actionBtn;

        })->rawColumns(['File','Action'])
        ->make(true);

    }

}
