
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <base href="{{asset('')}}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
    <link rel="icon" href="{{asset('/common_images/laravel-developer.png')}}" type="image/ico"/>
    @yield('css')
    @stack("css")
</head>
<body>
<div id="app" style="width: 100%; height: 100%">
    @yield('content')
</div>
</body>
@yield('scripts')
@stack("js")
<script type="text/javascript" src="{{asset('js/auth/login.js')}}"></script>
</html>
