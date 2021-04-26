<?php

namespace App\Http\Controllers;

use App\Models\game;
use App\Models\player;
use App\Models\PlayerFile;
use Illuminate\Http\Request;

class DatatableController extends Controller
{
    
    // public function allPlayers(){

    //     // All Players Datatable

    //     $query = Player::select('player_name', 'games_id', 'id');
    //     return datatables($query)
    //         ->addIndexColumn()
    //             ->addColumn('Action', function($query){
                    
    //                 $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
    //                                 //'<a class = "btn btn-success btn-sm edit" href = "/allplayer/edit/'.$query->id.'">Edit</a>
    //                                 '<a class = "btn btn-secondary btn-sm playerfile" href =  "/playerfile/'.$query->id.'">Player File</a>
    //                                 <a class= "btn btn-danger btn-sm delete" href= "/allplayer/delete/'.$query->id.'" >Delete</a>'
    //                                 ;
    //                 return $actionBtn;

    //             })->rawColumns(['Action'])
    //             ->make(true);

    // }

    // public function gamelist(){

    //     //Game List Datatable 

    //     $query = Game::select('id','game_name');
    //     return datatables($query)->make(true);

    // }

    public function getPlayers($id){

        //Players Datatable (According to game)
        $game = game::find($id);

        $modelname = str_replace(' ', '',$game->game_name);

        $model = "app\\Models\\".$modelname;

        $query = $model::select('player_name', 'id','games_id')->where('games_id', $id);
        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/player/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href = "/playerfile/'.$query->games_id.'/'.$query->id.'">Player File</a>
                                    <a class = "btn btn-danger btn-sm delete" href = "/player/delete/'.$query->games_id.'/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);
        
    }

    public function PlayerFile($gameID, $playerID){

        //Player Files Datatable 

        $game = game::findorfail($gameID);

        $GameModelName = str_replace(' ', '',$game->game_name);

        $GamePlayerModel = "App\\Models\\".$GameModelName."PlayerFiles";

        $query = $GamePlayerModel::select('JSON_file', 'type', 'id', 'players_id')->where('players_id', $playerID);
        return datatables($query)
        ->addIndexColumn()
        ->addColumn('Action', function($query){
            
            //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
            $actionBtn = '<a href = "/playerfile/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a>
                            <a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.request()->route('gameID').'/'.$query->id.'">Edit</a>
                            <a class = "btn btn-danger btn-sm delete" href = "/playerfile/delete/'.request()->route('gameID').'/'.$query->id.'">Delete</a>'
                            ;
            return $actionBtn;

        })
        ->addColumn('Api', function($query){
            $actionBtn = '<button class = "path btn btn-secondary btn-sm" data-toggle="modal" data-target="#url" data-path="'.$query->id.'">URL</button>';
            return $actionBtn;
        })->rawColumns(['Api', 'Action'])
        ->make(true);

    }

}
