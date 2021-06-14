@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
                
            <h3><a href ="/game/{{$games->id}}">{{ $games->game_name}}</a> / <a href ="/data/{{$games->id}}/{{$gamedatatype->id}}">{{ $gamedatatype->data_name }}</a></h3>

            <div style = "padding-bottom:5px; text-align:right;">
                <button type = "submit" class="Add_New_{{$gamedata->data_name}} btn btn-primary" data-toggle="modal" data-target="#addnewdatafile">{{'Add New File'}}</button>
            </div>
            <table class = "table" id = "datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Column_folder</th>
                        <th>File</th>
                        <th>Type</th>
                        {{-- <th>Api</th> --}}
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            
        </div>
    </div>


    <div class = "modal fade" id = "addnewdatafile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add New File</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
                <form action="/data/twolayer/file/add/{{$games->id}}/{{$gamedatatype->id}}/{{$gamedata->id}}" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-body">
                        <div class="gamedata">
                            @if(\Session::has('column_empty'))
                                <div class = "alert alert-danger">
                                    <p>{{ \Session::get('column_empty')}}</p>
                                </div>
                            @endif
                            <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                            <input type = "file" name = "data_file" id = "playerjson" class = "form-control-file @error('data_file') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                                @error('data_file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            <br>
                            <label>Type:</label>
                            <input id="filetype" name = "file_type" type="text" class="form-control @error('file_type') is-invalid @enderror" value = "{{ old('file_type') }}" autocomplete="off" autofocus>
                            @error('file_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                            <br>
                            <label>Column Folder:</label>
                            <select id="folderpath" name = "folder_path" class = "form-control @error('folder_path') is-invalid @enderror" onchange="ColumnTip(this)"> 
                                <option value="none">None</option>
                                @foreach ($column_option as $column_option)
                                    <option value="{{$column_option}}">{{$column_option}}</option>
                                @endforeach
                            </select>
                            <small id = "column_tip"></small>  
                                @error('folder_path')
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

    <div class = "modal fade" id = "replacefile" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Replace File</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
                <form action="/data/twolayer/file/replace/" method="post" enctype="multipart/form-data" id="replacefileform" name = "replace_file_form">
                @csrf
                @method('PATCH')
                    <div class="modal-body">
                        <div class="datafile">
                            @if(\Session::has('edit_empty_datafile'))
                                <div class = "alert alert-danger">
                                    <p>{{ \Session::get('edit_empty_datafile')}}</p>
                                </div>
                            @endif
                            <label>File ( JSON, XML, Txt, PNG, JPEG ):</label>
                            <input type = "file" name = "replace_data_file" id = "replacedatafile" class = "form-control-file @error('replace_data_file') is-invalid @enderror" accept = "application/JSON,application/xml,text/plain,text/xml,image/png,image/jpeg"> 
                                @error('replace_data_file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            <br>
                            <label>Type:</label>
                                <input id="replacefiletype" name = "type" type="text" class="form-control @error('type') is-invalid @enderror" value = "{{ old('type') }}" autocomplete="off" autofocus>
                                @error('type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            <br>
                            <label>Column Folder:</label>
                            <select id="replacefolderpath" name = "replace_folder_path" class = "form-control @error('replace_folder_path') is-invalid @enderror" onchange="ReplaceColumnTip(this)"> 
                                <option value="current">Current</option>
                                <option value="none">None</option>
                                @foreach ($column_option_2 as $column_option_2)
                                    <option value="{{$column_option_2}}">{{$column_option_2}}</option>
                                @endforeach
                            </select>
                            <small id = "replace_column_tip"></small>  
                                @error('replace_folder_path')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-outline-success" id = "ReplaceSubmit" value="Replace">
                    </div>
                    <input type="hidden" id="formurl" name="form_url" value="{{ old('form_url') }}">
               </form>
            </div>
         </div>
    </div>

</div>
@endsection

@section('footer-scripts')
    <script>

        // var columns = [];

        // function getDT() {
            
        //     $.ajax({
        //         url: "{{ route('api.gameDataFile', [$games->id, $gamedatatype->id, $gamedata->id])}}",
        //         success: function (data) {
        //             data = JSON.parse(JSON.stringify(data))
        //             if (data.data[0] != undefined){
        //                 columnNames = Object.keys(data.data[0]);
        //                 var columnNames2 = $(columnNames).not(['file']).get();
        //                 for (var i in columnNames2) {
        //                     columns.push({data: columnNames2[i], 
        //                             title: capitalizeFirstLetter(columnNames2[i])});
        //                 }
        //                 $('#datatable').DataTable( {
        //                     processing: true,
        //                     serverSide: true,
        //                     ajax: "{{ route('api.gameDataFile', [$games->id, $gamedatatype->id, $gamedata->id])}}",
        //                     columns: columns
        //                 } );
        //             } else {
        //                 $('#datatable').DataTable({
        //                     "processing": true,
        //                     "serverSide": true,
        //                     "ajax": "{{ route('api.gameDataFile', [$games->id, $gamedatatype->id, $gamedata->id])}}",
        //                 });
        //             }
        //         }
        //     });
        // }

        // function capitalizeFirstLetter(string) {
        //     return string.charAt(0).toUpperCase() + string.slice(1);
        // }

        function ColumnTip(element){
            if (element.value == "none") {
                document.getElementById('column_tip').style.display = "none";
            } else {
                document.getElementById('column_tip').innerText = "Tip: Your file will be saved in " +element.value+ " folder";
                document.getElementById('column_tip').style.display = "block";
            }
        }

        function ReplaceColumnTip(element){
            if (element.value == "none") {
                document.getElementById('replace_column_tip').style.display = "none";
            } else {
                document.getElementById('replace_column_tip').innerText = "Tip: Your file will be saved in " +element.value+ " folder";
                document.getElementById('replace_column_tip').style.display = "block";
            }
        }

        $(document).ready(function () { 
            $('#datatable').DataTable({
                "columnDefs": [{
                    "defaultContent": "-",
                    "targets": "_all"
                }], 
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.gameDataFile', [$games->id, $gamedatatype->id, $gamedata->id])}}",
                "columns": [
                    {"data": "id"},
                    {"data": "column_folder"},
                    {"data": "File"},
                    {"data": "type"},
                    // {"data": "Api", orderable: false, searchable: false},
                    {"data": "Action", orderable: false, searchable: false}
                ]
            });

        });

        $(document).ready(function() {

            // getDT();

            $('#datatable').on('click', '.delete', function () { 

                var confirmation = confirm('Delete the record?');

                if (confirmation == false){
                    return false;
                }
            });

            $('#datatable').on('click','.edit', function(){

                var modal = $('#replacefile');

                modal.find('#replacefiletype').attr("placeholder", ($(this).data('type')));

                $('#replacefileform').attr('action','/data/twolayer/file/replace/'+$(this).data('url'));

                document.getElementById("formurl").value = $(this).data('url');
                
            })

        });

    </script>

    @if(\Session::has('column_empty'))
        <script type="text/javascript">
            $(document).ready(function(){
                $('#addnewdatafile').modal({show:true});
            })
        </script>
    @endif

    @include('scripts.JScript')

@endsection



