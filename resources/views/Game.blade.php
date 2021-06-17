@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                
            <h3>{{ $games->game_name }}</h3>

            <div style = "padding-bottom:5px; text-align:right;">
                <button type = "submit" class="Add_New_Data btn btn-primary" data-toggle="modal" data-target="#addnewdatatype">Add New Data</button>
            </div>
            <div class="card">
                <div class="card-header">
                    <span style = "font-size:25px;">Data</span>
                    <button class="remove_btn btn btn-outline-danger" data-toggle="modal" data-target="#removedata" style="float:right;"><i class="far fa-trash-alt"></i></button>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <a class = "btn btn-secondary btn-lg Players" href = "/game/players/{{$games->id}}">Players</a>
                    <hr>
                    <h4 class="card-title">One Layer</h4>
                    @foreach ($gamedataOneLayer as $gamedataOneLayer)
                        <a class = "btn btn-secondary btn-lg Data" href = "/data/{{$games->id}}/{{$gamedataOneLayer->id}}" data-toggle="tooltip" data-placement="top" title="Data ID: {{$gamedataOneLayer->id}}">{{ $gamedataOneLayer->data_name }}</a>
                    @endforeach
                    <hr>
                    <h4 class="card-title">Two Layer</h4>
                    @foreach ($gamedataTwoLayer as $gamedataTwoLayer)
                        <a class = "btn btn-secondary btn-lg Data" href = "/data/{{$games->id}}/{{$gamedataTwoLayer->id}}" data-toggle="tooltip" data-placement="top" title="Data ID: {{$gamedataTwoLayer->id}}">{{ $gamedataTwoLayer->data_name }}</a>
                    @endforeach
                    
                </div>
            </div>
            {{-- <div class="card">
                <div class="card-body">
                
                <hr>
                
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                <h4 class="card-title">Two Layer</h4>
                <hr>
                
                </div>
            </div> --}}
            
        </div>
    </div>


    <div class = "modal fade" id = "addnewdatatype" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add New Data</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="/data/add/{{$games->id}}" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-body">
                        <div class="gamedata">
                            <label>Data name:</label>
                            <input id="dataname" name = "data_name" type="text" class="form-control @error('data_name') is-invalid @enderror" value = "{{ old('data_name') }}" autocomplete="off" autofocus>
                                @error('data_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <br>
                            <label>Column name:</label>
                            <input id="columnname" name = "column_name" type="text" class="form-control @error('column_name') is-invalid @enderror" value = "{{ old('column_name') }}" autocomplete="off" autofocus>
                                @error('column_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <br>
                            <label>Layer:</label>
                            <select id="datalayer" name = "layer" class = "form-control @error('layer') is-invalid @enderror" onchange="DisableCheckBox(this)"> 
                                <option value="0" disabled selected>-- Please Select Layer --</option>  
                                <option value="single">Single</option>
                                <option value="double">Double</option> 
                            </select>  
                                @error('layer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            <br>
                            {{-- <input type="checkbox" name="player_related" id = "playerdata_checkbox">
                            <label>Player Related</label>
                            @error('player_related')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror --}}
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

    <div class = "modal fade" id = "removedata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Remove Data</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="/data/delete/{{$games->id}}" method="post" enctype="multipart/form-data">
                @csrf
                @method('DELETE')
                    <div class="modal-body">
                        <div class="RemoveDataContent">
                            <h5><u>One Layer</u></h5>
                            @foreach ( $gamedataOneLayerList as $gamedataOneLayerList )
                                <input type = "checkbox" class = "@error('remove_data') is-invalid @enderror"name = "remove_data[]" value = {{$gamedataOneLayerList->id}} style = "float:right;">
                                <label for = "remove_data">
                                    {{$gamedataOneLayerList->data_name}}
                                </label>
                                <br>
                            @endforeach
                            <hr>
                            <h5><u>Two Layer</u></h5>
                            @foreach ( $gamedataTwoLayerList as $gamedataTwoLayerList )
                                <input type = "checkbox" class = "@error('remove_data') is-invalid @enderror"name = "remove_data[]" value = {{$gamedataTwoLayerList->id}} style = "float:right;">
                                <label for = "remove_data">
                                    {{$gamedataTwoLayerList->data_name}}
                                </label>
                                <br>
                            @endforeach      
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "RemoveDataSubmit" value="Remove">
                    </div>
               </form>
            </div>
         </div>
    </div>

</div>
@endsection

@section('footer-scripts')
    @include('scripts.JScript')
    <script>

        function DisableCheckBox(element)
        {
            if (element.value == "double") {
                document.getElementById('playerdata_checkbox').disabled = true;
            } else {
                document.getElementById('playerdata_checkbox').disabled = false;
            }
        }

    </script>
@endsection



