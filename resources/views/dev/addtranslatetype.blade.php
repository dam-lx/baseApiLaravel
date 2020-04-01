<!DOCTYPE html>
<html>
<head>
    <title>Create Translate Type</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Layout -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/dev/layouts/layout_dev.css') }}">

    <style type="text/css">
    #create-translate-type{
        margin: 30px;
    }
</style>
</head>
<body>


    <div id="create-translate-type">

        <form data-toggle="validator" action="{{ route('createNewTranslateType') }}" method="POST">

            <div class="form-group">

                <label class="control-label" for="title">Code:</label>

                <input type="text" id="code" name="code" class="form-control" />

            </div>
            <div class="form-group">

                <label class="control-label" for="title">Comment:</label>

                <input type="text" name="comment" class="form-control" />

            </div>

        </form>

    </div>
    <script src="{{ asset('js/dev/layouts/layout_dev.js')}}"></script>
    <script src="{{ asset('js/lang/text.js')}}"></script>
    <script src="{{ asset('js/lib/common.js')}}"></script>

    <script type="text/javascript">

        $("#close").click(function(){
            parent.$('#modal-iFrame').iziModal('close'); 
        });  


        /* Create new translate type */
        $(".submit-create").click(function(e){

            e.preventDefault();
            var form_action = $("#create-translate-type").find("form").attr("action");
            var code = $("#create-translate-type").find("input[name='code']").val();
            var comment = $("#create-translate-type").find("input[name='comment']").val();

            $.ajax({

                dataType: 'json',

                type:'POST',

                url: form_action,

                data:{code:code, order_value:order_value, comment:comment}

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