<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerFile;
use App\Models\User;
use Illuminate\Http\Request;

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
        ->addColumn('Api', function($query){
            $actionBtn = '<button class = "path btn btn-secondary btn-sm" data-toggle="modal" data-target="#url" data-path="'.$query->id.'">URL</button>';
            return $actionBtn;
        })->rawColumns(['File', 'Api', 'Action'])
        ->make(true);

    }

}
