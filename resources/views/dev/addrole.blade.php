<!DOCTYPE html>
<html>
<head>
    <title>Create Role</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Layout -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dev/layouts/layout_dev.css') }}">

    <style type="text/css">
    #create-role{
        margin: 30px;
    }
</style>
</head>
<body>


    <!-- Create Role Modal -->

    <div  id="create-role">

        <form data-toggle="validator" action="{{ route('createNewRoleItem') }}" method="POST">

            <div class="form-group">

                <label class="control-label" for="title">Name:</label>

                <input type="text" id="name" name="name" class="form-control" />

            </div>
            <div class="form-group">

                <label class="control-label" for="title">Description:</label>

                <input type="text" name="description" class="form-control" />

            </div>

            <!-- <div class="form-group">
                <button type="button" class="btn btn-success submit-create">Submit</button>
                <button type="button" id="close" class="btn btn-danger" data-izimodal-close="" data-izimodal-transitionout="bounceOutDown">Close</button>
            
            </div> -->

        </form>

    </div>
    <script src="{{ asset('js/dev/layouts/layout_dev.js')}}"></script>
    <script src="{{ asset('js/lang/text.js')}}"></script>
    <script src="{{ asset('js/lib/common.js')}}"></script>

    <script type="text/javascript">

        $("#close").click(function(){
            parent.$('#modal-iFrame').iziModal('close'); 
        });  


        // 

        /* Create new Role */
        $(".submit-create").click(function(e){

            e.preventDefault();
            var form_action = $("#create-role").find("form").attr("action");
            var name = $("#create-role").find("input[name='name']").val();
            var description = $("#create-role").find("input[name='description']").val();

            $.ajax({

                dataType: 'json',

                type:'POST',

                url: form_action,

                data:{name:name, role_value:role_value, description:description}

            }).done(function(data){
                if(data.status == '{{\App\Core\Common\SDBStatusCode::OK}}'){
                    parent.toastr.success('Created Successfully.', 'Success Alert', {timeOut: 3000});
                    parent.location.reload();
                }else{

                    _commonShowError(data.data);
                }

            });
        });

    </script>
</body>
</html>