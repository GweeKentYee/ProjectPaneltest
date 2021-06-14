@if(Session::has('errors'))
    <script>

        $(document).ready(function(){
            $('#addplayer').modal({show: true});
        })

        $(document).ready(function(){
            $('#addplayerfile').modal({show:true});
        })

        $(document).ready(function(){
            $('#registerSubAccount').modal({show:true});
        })

        $(document).ready(function(){
            $('#addnewdatatype').modal({show:true});
        })
        
    </script>

@endif

{{-- Home Page --}}
@error ('game_name')
    <script type="text/javascript">
    $(document).ready(function(){
        $('#addgame').modal({show: true});
    })
    </script>
@enderror

{{-- Data  --}}
@if ($errors->get('new_column'))
    <script type="text/javascript">
    $(document).ready(function(){
        $('#addnewcolumn').modal({show:true});
    })
    </script>
@endif

@if ($errors->has('data_file') or $errors->has('players_id'))
    <script type="text/javascript">
    $(document).ready(function(){
        $('#addnewdata').modal({show:true});
    })
    </script>
@endif

{{-- Data File (2 Layer) --}}
@if ($errors->get('data_file') or $errors->get('file_type'))
    <script type="text/javascript">
        $(document).ready(function(){
            $('#addnewdatafile').modal({show:true});
        })
    </script>
@endif

@if ($errors->get('replace_data_file') or $errors->get('replace_file_type'))
    <script type="text/javascript">
        $(document).ready(function(){
            $('#replacefileform').attr('action','/data/twolayer/file/replace/'+'{{old('form_url')}}');
            $('#replacefile').modal({show:true});
        })
    </script>
@endif

@if(Session::has('message'))
    <script>
        var type = "{{Session::get('alert-type', 'info')}}";

        toastr.options = {
            "closeButton": true,
            "preventDuplicates": true,
        };

        switch(type){
            case 'success':
                toastr.success("{{Session::get('message')}}");
                break;
            case 'error':
                toastr.error("{{Session::get('message')}}");
        }
    </script>
@endif

<script type="text/javascript">

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(document).ready(function () {
        $('#remove').click(function() {
        checked = $("input[type=checkbox]:checked").length;
    
        if(!checked) {
            alert("You must select at least one game.");
            return false;
        } else {
            var confirmation = confirm('Delete the selected games?');
    
            if (confirmation == false){
                return false;
                }
        }
        });
    });

    $(document).ready(function () {
        $('#RemoveColumnSubmit').click(function() {
        checked = $("input[type=checkbox]:checked").length;
    
        if(!checked) {
            alert("You must select at least one column.");
            return false;
        } else {
            var confirmation = confirm('Delete the selected columns?');
    
            if (confirmation == false){
                return false;
                }
        }
        });
    });

    $(document).ready(function () {
        $('#RemoveDataSubmit').click(function() {
        checked = $("input[type=checkbox]:checked").length;
    
        if(!checked) {
            alert("You must select at least one data.");
            return false;
        } else {
            var confirmation = confirm('Delete the selected data?');
    
            if (confirmation == false){
                return false;
                }
        }
        });
    });

    $(document).ready(function () {
        $('#EditDataSubmit').click(function() {

        var confirmation = confirm('Edit the record? \r\n*The records and files related to this record will be removed*');

        if (confirmation == false){
            return false;
            }
        
        });

    });
</script>
