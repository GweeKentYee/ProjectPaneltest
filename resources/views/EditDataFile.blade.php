@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3><a href ="/game/{{$games->id}}">{{ $games->game_name}}</a> / <a href ="/data/{{$games->id}}/{{$gamedatatype->id}}">{{ $gamedatatype->data_name }}</a></h3>
            @if(in_array("File", $checking))
            <form action="/data/onelayer/file/edit/{{$games->id}}/{{$gamedatatype->id}}/{{$gamedata->id}}" method="post" enctype="multipart/form-data" id="OneLayerEditForm">
             @else
             <form action="/data/twolayer/edit/{{$games->id}}/{{$gamedatatype->id}}/{{$gamedata->id}}" method="post" enctype="multipart/form-data" id="TwoLayerEditForm">
             @endif
            @csrf
            @method('PATCH')
            @if(\Session::has('field_empty'))
                <div class = "alert alert-danger">
                    <p>{{ \Session::get('field_empty')}}</p>
                </div>
            @endif
            <div class="modal-header">
                @if(in_array("File", $checking))
                <h4>Old File: <a href = "/data/onelayer/file/view/{{$games->id}}/{{$gamedatatype->id}}/{{$gamedata->id}}">{{$gamedata->file}}</a></h4>
                @endif 
            </div>
            
                <div class="modal-body">
                    <div class="playerfile">
                        @foreach ($specialcolumn as $specialcolumn)
                        <label>{{$specialcolumn.':'}}</label>
                        <input name = "{{$specialcolumn}}" type="text" class="form-control @error($specialcolumn) is-invalid @enderror" value = "{{ old($specialcolumn) }}" placeholder ="{{ $gamedata->$specialcolumn }}" autocomplete="off" autofocus>
                            @error($specialcolumn)
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <br>
                        @endforeach
                        @if(in_array("Players_id", $checking))
                            <label>players_id</label>
                            <input name = "players_id" type="text" class="form-control @error('players_id') is-invalid @enderror" value = "{{ old('players_id') ?? $gamedata->players_id}}" autocomplete="off" autofocus>
                                @error('players_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            <br>  
                        @endif
                        @if(in_array("File", $checking))
                        <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                        <input type = "file" name = "data_file" id = "playerjson" class = "form-control-file @error('data_file') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                            @error('data_file')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        @else
                        <p style="color:red">*Warning: The Records/Files Related To This Record Will be Removed Once Edited*</>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick = "Back()">Cancel</button>
                    @if(in_array("File", $checking))
                    <input type="submit" class="btn btn-outline-success" id = "ReplaceSubmit" value="Replace">
                    @else
                    <input type="submit" class="btn btn-outline-success" id = "EditDataSubmit" value="Edit">
                    @endif
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



