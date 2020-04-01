<!DOCTYPE html>
<html>
<head>
    <title>Dialog</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--==============================layout =========================== -->
    <link href="{{ asset('css/backend/layout/layout_dialog.css') }}" rel="stylesheet">

    <style type="text/css">
        input::placeholder{
            font-weight: normal;
            font-size: 12px;
        }
    </style>
    @stack("css")
</head>
<body>
    @yield("content")
</body>
</html>
<!-- jQuery -->
<script src="{{ asset('layouts')}}"></script>
<script>
    var _success = "{{trans('common.success')}}";
    var _save    = "{{trans('common.save')}}";
    var _reset   = "{{trans('common.reset')}}";
    var _error   = "{{trans('common.error')}}";
</script>
@stack("js")