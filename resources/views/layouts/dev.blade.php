<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/dev/layouts/layout_dev.css') }}" rel="stylesheet">
    @stack("css")
</head>
<body>
    <div id="app">
        <nav class="navbar-default navbar navbar-expand-sm fixed-top">
            <div class="container-fluid">
                <div class="col-md-3">
                    <a class="navbar-brand" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>
                </div>
                <div class="col-md-9">
                    <ul class="navbar-nav pull-right">
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('index') }}">Initialization Project</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('translationManagement') }}">Translation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('aclManangement') }}">Acl - Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('roleManagement') }}">Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('userRole') }}">User - Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('menu') }}">Categories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold" href="{{ route('doc') }}">Document</a>
                        </li>
                    </ul>
                </div>
            </div>

        </nav>
        <main class="py-4">
            <div class="container-fluid">
            @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('js/dev/layouts/layout_dev.js')}}" type="text/javascript"></script>
    <script src="{{ asset('js/lang/text.js')}}" type="text/javascript"></script>
    <script src="{{ asset('js/lib/common.js')}}" type="text/javascript"></script>
    @stack("js")
    <!-- Scripts -->
    @yield('scripts')
</body>
</html>
