@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
                
            <h3><a href ="/game/{{$games->id}}">{{ $games->game_name}}</a>{{' / '.$gamedatatype->data_name }}</h3>
            <br>
            <button class="btn btn-danger" style = "float:left" data-toggle="modal" data-target="#addnewcolumn">Add Column</button>
            <button class="btn btn-danger" style = "float:left;margin-left:5px;" data-toggle="modal" data-target="#removecolumn">Remove Column</button>
            <div style = "padding-bottom:5px; text-align:right;">
                <button type = "submit" class="Add_New_{{$gamedatatype->data_name}} btn btn-primary" data-toggle="modal" data-target="#addnewdata">{{'Add New '.$gamedatatype->data_name}}</button>
            </div>
            <table class = "table" id = "datatable">
                <thead>
                    <tr>
                        <th>id</th>
                        @foreach ($columns as $columns )
                            <th>{{ $columns }}</th>
                        @endforeach
                        @if(in_array("File", $checking))
                        <th>File</th>
                        @endif
                        @if(in_array("Players_id", $checking))
                        <th>Players_id</th>
                        @endif
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            
        </div>
    </div>

    <div class = "modal fade" id = "addnewdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add New {{$gamedatatype->data_name}}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @if(in_array("File", $checking))
               <form action="/data/onelayer/file/add/{{$games->id}}/{{$gamedatatype->id}}" method="post" enctype="multipart/form-data">
                @else
                <form action="/data/twolayer/add/{{$games->id}}/{{$gamedatatype->id}}" method="post" enctype="multipart/form-data">
                @endif
                @csrf
                    <div class="modal-body">
                        <div class="datacontent">
                            @foreach ($specialcolumn as $specialcolumn)
                            <label>{{$specialcolumn.':'}}</label>
                            <input name = "{{$specialcolumn}}" type="text" class="form-control @error($specialcolumn) is-invalid @enderror" value = "{{ old($specialcolumn) }}" autocomplete="off" autofocus>
                                @error($specialcolumn)
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            <br>
                            @endforeach

                            @if(in_array("Players_id", $checking))
                            <label>Players_id</label>
                            <input name = "players_id" type="text" class="form-control @error('players_id') is-invalid @enderror" value = "{{ old('players_id') }}" autocomplete="off" autofocus>
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
                            @endif            
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

    <div class = "modal fade" id = "addnewcolumn" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add Column</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
                <form action="/data/onelayer/column/add/{{$games->id}}/{{$gamedatatype->id}}" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-body">
                        <div class="columndetails">
                            <label>Column Name:</label>
                            <input id="NewColumn" name = "new_column" type="text" class="form-control @error('new_column') is-invalid @enderror" value = "{{ old('new_column') }}" autocomplete="off" autofocus>
                                @error('new_column')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror       
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "AddColumnSubmit" value="Add">
                    </div>
               </form>
            </div>
         </div>
    </div>

    <div class = "modal fade" id = "removecolumn" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Remove Column</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @if(in_array("File", $checking))
               <form action="/data/onelayer/column/remove/{{$games->id}}/{{$gamedatatype->id}}" method="post" enctype="multipart/form-data">
                @else
                <form action="/data/twolayer/column/remove/{{$games->id}}/{{$gamedatatype->id}}" method="post" enctype="multipart/form-data">
                @endif
                @csrf
                    <div class="modal-body">
                        <div class="RemoveColumnContent">
                            @foreach ( $columnlist as $columnlist )
                                <input type = "checkbox" class = "@error('remove_column') is-invalid @enderror"name = "remove_column[]" value = {{$columnlist}} style = "float:right;">
                                <label for = "remove_column">
                                    {{$columnlist}}
                                </label>
                                <br>
                            @endforeach      
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "RemoveColumnSubmit" value="Remove">
                    </div>
               </form>
            </div>
         </div>
    </div>

    <div class = "modal fade" id = "editdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                @if(in_array("File", $checking))
                    <h5 class="modal-title" id="exampleModalCenterTitle">Replace</h5>
                @else
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit</h5>
                @endif
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               @if(in_array("File", $checking))
               <form action="/data/onelayer/file/edit/" method="post" enctype="multipart/form-data" id="OneLayerEditForm">
                @else
                <form action="/data/twolayer/edit/" method="post" enctype="multipart/form-data" id="TwoLayerEditForm">
                @endif
                @csrf
                @method('PATCH')
                    <div class="modal-body">
                        <div class="data">
                            @foreach ($specialcolumn2 as $specialcolumn2)
                            <label>{{$specialcolumn2.':'}}</label>
                            <input name = "{{$specialcolumn2}}" type="text" class="form-control @error($specialcolumn2) is-invalid @enderror" value = "{{ old($specialcolumn2) ?? $gamedatatype->$specialcolumn2}}" autocomplete="off" autofocus>
                                @error($specialcolumn2)
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                                <br>
                            @endforeach
                            @if(in_array("File", $checking))
                            <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                            <input type = "file" name = "data_file" class = "form-control-file @error('data_file') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                                @error('data_file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            @endif
                            <br>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "EditSubmit" value="Edit">
                    </div>
               </form>
            </div>
         </div>
    </div>

</div>
@endsection

@section('footer-scripts')

    <script>

        var columns = [];

        function getDT() {
            
            $.ajax({
                url: "{{ route('api.gameData', [$games->id, $gamedatatype->id])}}",
                success: function (data) {
                    data = JSON.parse(JSON.stringify(data))
                    if (data.data[0] != undefined){
                        columnNames = Object.keys(data.data[0]);
                        var columnNames2 = $(columnNames).not(['id','file','players_id']).get();
                        console.log(columnNames2);
                        columns.push({
                            data: 'id',
                        })
                        for (var i in columnNames2) {
                        columns.push({data: columnNames2[i], 
                                    title: columnNames2[i]});
                        }
                        $('#datatable').DataTable( {
                            processing: true,
                            serverSide: true,
                            ajax: "{{ route('api.gameData', [$games->id, $gamedatatype->id])}}",
                            columns: columns
                        } );
                    } else {
                        $('#datatable').DataTable({
                            "processing": true,
                            "serverSide": true,
                            "ajax": "{{ route('api.gameData', [$games->id, $gamedatatype->id])}}",
                        });
                    }
                }
            });
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        $(document).ready(function() {
        
            getDT();

            $('#datatable').on('click', '.delete', function () { 

                var confirmation = confirm('Delete the record?');

                if (confirmation == false){
                    return false;
                }
            });

        });
        
    </script>
    @foreach ($specialcolumn3 as $specialcolumn3)
        @if ($errors->has(''.$specialcolumn3.''))
            <script type="text/javascript">
                $(document).ready(function(){
                    $('#addnewdata').modal({show:true});
                })
            </script>
        @endif
    @endforeach

    @include('scripts.JScript')

@endsection



