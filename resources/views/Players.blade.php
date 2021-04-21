@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                
            <h3>{{ $games->game_name }}</h3>

            <div style = "padding-bottom:5px; text-align:right;">
                <button type = "submit" class="Add_Player btn btn-primary" data-toggle="modal" data-target="#addplayer">Add Player</button>
            </div>
            <table class = "table" id = "datatable">
                <thead>
                    <tr>
                        <th>Player name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


    <div class = "modal fade" id = "addplayer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Add Player</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <form action="/player/add/{{$games->id}}" method="post" enctype="multipart/form-data">
                @csrf
                    <div class="modal-body">
                        <div class="player">
                            <label>Player name:</label>
                            <input id="playername" name = "player_name" type="text" class="form-control @error('player_name') is-invalid @enderror" value = "{{ old('player_name') }}" autocomplete="off" autofocus>
                                @error('player_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            {{-- <br>
                            <label> JSON/Txt file:</label>
                            <input type = "file" name = "json/txt" id = "playerjson" class = "form-control-file @error('json/txt') is-invalid @enderror" accept = "application/JSON, text/plain"> 
                                @error('json/txt')
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

</div>
@endsection

@section('footer-scripts')
    <script>

        $(document).ready(function () { 
            $('#datatable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.players.index', $games->id)}}",
                "columns": [
                    {"data": "player_name"},
                    {"data": "Action", orderable: false, searchable: false}
                ]
            });

        });

        $(document).ready(function () { 
            $('#datatable').on('click', '.delete', function () { 

                var confirmation = confirm('Delete the record?');
    
                if (confirmation == false){
                return false;
                }
            });
        });
        
    </script>
    @include('scripts.JScript')
@endsection



