<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Meta, title, CSS, favicons, etc. -->
<meta charset="utf-8">
<base href="{{asset('')}}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="{{asset('/common_images/icon-app.png')}}" type="image/ico"/>

<title>{{env('APP_NAME')}} </title>

<!-- Layout backend -->
<link href="{{ asset('css/backend/layouts/layout_backend.css')}}" rel="stylesheet">
@yield('lib_style')
<title>{{ config('app.name', 'Laravel') }}</title>
@stack("css")
