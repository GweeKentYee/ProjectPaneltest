@if(Session::has('errors'))
    <script>
        $(document).ready(function(){
            $('#addgame').modal({show: true});
        })

        $(document).ready(function(){
            $('#addplayer').modal({show: true});
        })

        $(document).ready(function(){
            $('#addfile').modal({show:true});
        })

    </script>

@endif

<script type="text/javascript">
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
</script>
