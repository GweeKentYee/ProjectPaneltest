@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div style = "padding-bottom:5px; text-align:right;">
            {{-- <button class="btn btn-outline-info" onclick = "location.href = '/allplayer'" style = "float:left">All Players</button> --}}
            <button type = "submit" class="Add_Game btn btn-primary" data-toggle="modal" data-target="#addgame">Add Game</button>
            </div>
            <div class="card">
                <form method = "POST" action = "/game/remove">
                    @csrf
                    @method('DELETE')
                    <div class="card-header">
                        <span style = "font-size:25px;">Games</span>
                        <button class="remove_btn btn btn-outline-danger" name="remove" id = "remove" type="submit" style="float:right; "><i class="far fa-trash-alt"></i></button>
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (Auth::user()->is_admin == '0')
                            @foreach ($admingames as $admingames)
                                @foreach ($admingames as $admingames)
                                <div class = "admin_games_row" >
                                    <label>
                                        <a href = "/game/{{$admingames->id}}">{{ $admingames->game_name }}</a>
                                    </label>
                                </div>
                                @endforeach
                            @endforeach
                            @foreach ($AccountGames as $AccountGames)
                            <div class = "games_row" >
                                <input type = "checkbox" class = "@error('remove_game') is-invalid @enderror"name = "remove_game[]" value = {{$AccountGames->id}} style = "float:right;">
                                <label for = "remove_game">
                                    <a href = "/game/{{$AccountGames->id}}">{{ $AccountGames->game_name }}</a>
                                </label>
                            </div>
                            @endforeach
                        @else
                            @foreach ($games as $games)
                            <div class = "games_row" >
                                <input type = "checkbox" class = "@error('remove_game') is-invalid @enderror"name = "remove_game[]" value = {{$games->id}} style = "float:right;">
                                <label for = "remove_game">
                                    <a href = "/game/{{$games->id}}">{{ $games->game_name }}</a>
                                </label>
                            </div>
                            @endforeach
                        @endif 

                    </div>
                </form>  
            </div>
        </div>
    </div>

    <div class = "modal fade" id = "addgame" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add Game</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="/game/add" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-body">
                        <div class="game">
                            <label>Name of game:</label>
                            <input id="gamename" name = "game_name" type="text" class="form-control @error('game_name') is-invalid @enderror" value = "{{ old('game_name') }}" autofocus>
                            @error('game_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "AddSubmit" value="Add">
                    </div>
               </form>
            </div>
         </div>
    </div>
</div>

@endsection

@section('footer-scripts')
    @include('scripts.JScript')
@endsection
