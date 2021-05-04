@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3><a href ="/game/{{$players->game->id}}">{{$players->game->game_name}}</a> / <a href = "/playerfile/{{$players->id}}">{{$players->player_name}}</a></h3>
            <form action="/playerfile/editfile/{{$players->game->id}}/{{$playerfile->id}}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <br>
            <br>
            <div class="modal-header">
                <h4>Old File: <a href = "/playerfile/view/{{$players->game->id}}/{{$playerfile->id}}">{{$playerfile->file}}</a></h4>
             </div>
                <div class="modal-body">
                    <div class="playerfile">
                        {{-- <label>Player name:</label>
                        <input id="playername" name = "player_name" type="text" class="form-control @error('player_name') is-invalid @enderror" value = "{{ old('player_name') ?? $players->player_name }}" autocomplete="off" autofocus>
                            @error('player_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        <br> --}}
                        <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                        <input type = "file" name = "json/txt" id = "playerjson" class = "form-control-file @error('json/txt') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                            @error('json/txt')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        <br>
                        <label>Type:</label>
                        <input id="filetype" name = "file_type" type="text" class="form-control @error('file_type') is-invalid @enderror" value = "{{ old('file_type') ?? $playerfile->type }}" autocomplete="off" autofocus>
                        @error('file_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick = "Back()">Cancel</button>
                    <input type="submit" class="btn btn-outline-success" id = "EditSubmit" value="Edit">
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    <script>
        function Back() {
            window.history.back();
        }
    </script>
    @include('scripts.JScript')
@endsection



