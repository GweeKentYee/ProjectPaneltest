@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <h3>All Players</h3>
            <div style = "text-align:right" class = "pb-1">
                <button class = "btn btn-primary"  data-toggle="modal" data-target="#gamelist">Game list</button>
            </div>
            <table class = "table" id = "datatable">
                <thead>
                    <tr>
                        <th>Player ID</th>
                        <th>Player name</th>
                        <th>Game ID</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class = "modal fade" id = "gamelist" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalCenterTitle">Game list</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
               </div>
                <div class="modal-body">
                    <div class="game">
                        <table class = "table" id = "gamesTable">
                            <thead>
                                <tr>
                                    <th>Game ID</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                    
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
            "ajax": "{{ route('api.allplayers')}}",
            "columns": [
                {"data": "id"},
                {"data": "player_name"},
                {"data": "games_id"},
                {"data": "Action", orderable: false, searchable: false}
            ]
        });
    });

    $(document).ready(function () { 
        $('#gamesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.gamelist')}}",
            "columns": [
                {"data": "id"},
                {"data": "game_name"}
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

@endsection
