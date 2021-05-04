@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
                
            <h3><a href ="/account">{{"Manage Sub-Accounts"}}</a>{{' / '.$account->username}}</h3>

            <br>
            <br>

            <table class = "table" id = "datatable">
                <thead>
                    <tr>
                        <th>Game ID</th>
                        <th>Game</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
                "ajax": "{{ route('api.subaccounts.games', $account->id)}}",
                "columns": [
                    {"data": "id"},
                    {"data": "Game"},
                    {"data": "Action", orderable: false, searchable: false}
                ]
            });

        });

        $(document).ready(function () { 
            $('#datatable').on('click', '.delete', function () { 

                var confirmation = confirm('Delete the game?');
    
                if (confirmation == false){
                return false;
                }
            });
        });
        
    </script>
    @include('scripts.JScript')
@endsection



