<!DOCTYPE html>
<html>
<head>
    <title>Create Role</title>
<meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Layout -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dev/layouts/layout_dev.css') }}">

    <style type="text/css">
        #edit-role{
            margin: 30px;
        }
    </style>
</head>
<body>

<!-- edit Role Modal -->
    <div id="edit-role" >

                <form id="fr-edit" data-toggle="validator" method="POST">
                    <input type="hidden" name="_token" value="{{csrf_token('')}}">

                    <div class="form-group">
                        <input type="hidden" name="id" value="">
                        <label class="control-label" for="title">Name:</label>

                        <input type="text" name="name" value="{{$edit->name}}" class="form-control" />

                        <div class="help-block with-errors"></div>

                    </div>
                    <div class="form-group">

                        <label class="control-label" for="title">Role Value:</label>

                        <input type="text" name="role_value" value="{{$edit->role_value}}" class="form-control" />

                        <div class="help-block with-errors"></div>

                    </div>
                    <div class="form-group">

                        <label class="control-label" for="title">Description:</label>

                        <input type="text" name="description" value="{{$edit->description}} " class="form-control"/>

                        <div class="help-block with-errors"></div>

                    </div>

                    <div class="form-group">

                        <button type="submit" class="btn submit-edit btn-success">Submit</button>
                        <button type="button" id="close" class="btn btn-danger" data-izimodal-close="" data-izimodal-transitionout="bounceOutDown">Close</button>
                    </div>

                </form>
</div>


<script src="{{ asset('js/dev/layouts/layout_dev.js')}}"></script>
<script src="{{ asset('js/lang/text.js')}}"></script>
<script src="{{ asset('js/lib/common.js')}}"></script>
<script src="{{ asset('js/dev/includes/role.js')}}"></script>

<script type="text/javascript">
    $("#close").click(function(){
        parent.$('#modal-iFrame2').iziModal('close'); 
    });  


$("#fr-edit").submit(function(e){
                stopEvent(e);
            });


///submit edit role

$(".submit-edit").click(function(e){
    e.preventDefault();
    var id = "{{$edit->id}}";
    var name = $("#edit-role").find("input[name='name']").val();
    var role_value = $("#edit-role").find("input[name='role_value']").val();
    var description = $("#edit-role").find("input[name='description']").val();
    

    $.ajax({

        dataType: 'json',

        type:'POST',

        url: '{{route('updateRole')}}',

        data:{id:id, name:name, role_value:role_value, description:description}

    }).done(function(data){
        parent.toastr.success('Customer Created Successfully.', 'Success Alert', {timeOut: 3000}); 
        parent.location.reload();

    });

});

</script>
</body>
</html>