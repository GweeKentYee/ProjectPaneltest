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
            @if(\Session::has('edit_empty_playerfile'))
                <div class = "alert alert-danger">
                    <p>{{ \Session::get('edit_empty_playerfile')}}</p>
                </div>
            @endif
            <div class="modal-header">
                <h4>Old File: <a href = "/playerfile/view/{{$players->game->id}}/{{$playerfile->id}}">{{$playerfile->file}}</a></h4>
             </div>
                <div class="modal-body">
                    <div class="playerfile">
                        <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                        <input type = "file" name = "json/txt" id = "playerjson" class = "form-control-file @error('json/txt') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                            @error('json/txt')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        <br>
                        <label>Type:</label>
                        <input id="filetype" name = "type" type="text" class="form-control @error('type') is-invalid @enderror" value = "{{ old('type') }}" placeholder="{{ $playerfile->type }}" autocomplete="off" autofocus>
                        @error('type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick = "Back()">Cancel</button>
                    <input type="submit" class="btn btn-outline-success" id = "EditPlayerSubmit" value="Edit">
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



