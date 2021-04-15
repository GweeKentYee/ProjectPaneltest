<?php

namespace App\Http\Controllers;

use App\Models\games;
use App\Models\players;
use App\Models\player_files;
use Illuminate\Http\Request;

class DatatableController extends Controller
{
    //
    public function allPlayers(){

        $query = Players::select('player_name', 'games_id', 'id');
        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/allplayer/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href =  "/playerfile/'.$query->id.'">Player File</a>
                                    <a class= "btn btn-danger btn-sm delete" href= "/allplayer/delete/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);
    }

    public function gamelist(){

        $query = Games::select('id','game_name');
        return datatables($query)->make(true);
    }

    public function getPlayers($id){
        $query = Players::select('player_name', 'id')->where('games_id', $id);
        return datatables($query)
            ->addIndexColumn()
                ->addColumn('Action', function($query){
                    
                    $actionBtn = //'<a href = "/player/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a> 
                                    //'<a class = "btn btn-success btn-sm edit" href = "/player/edit/'.$query->id.'">Edit</a>
                                    '<a class = "btn btn-secondary btn-sm playerfile" href = "/playerfile/'.$query->id.'">Player File</a>
                                    <a class = "btn btn-danger btn-sm delete" href = "/player/delete/'.$query->id.'" >Delete</a>'
                                    ;
                    return $actionBtn;

                })->rawColumns(['Action'])
                ->make(true);
        
    }

    public function PlayerFile($id){
        $query = player_files::select('JSON_file', 'type', 'id', 'players_id')->where('players_id', $id);
        return datatables($query)
        ->addIndexColumn()
        ->addColumn('Action', function($query){
            //<a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->player_id.'">Edit</a>
            $actionBtn = '<a href = "/playerfile/download/' .$query->JSON_file. '" class = "download btn btn-primary btn-sm">Download</a>
                            <a class = "btn btn-success btn-sm edit" href = "/playerfile/edit/'.$query->id.'">Edit</a>
                            <a class = "btn btn-danger btn-sm delete" href = "/playerfile/delete/'.$query->id.'" >Delete</a>'
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
