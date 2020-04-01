<!DOCTYPE html>
<html lang="{{app()->getLocale()}}">
<head>
    @include("layouts.includes.backend.head")
</head>
<?php
$userInfor = \App\Core\Helpers\AuthHelper::getUserInfor();
$avatar = \App\Core\Helpers\CommonHelper::getAvatar($userInfor->avatar);
$name = 'Guess';
if (\Illuminate\Support\Facades\Auth::check()) {
    $name = $userInfor->name;
}

?>
<body class="app sidebar-mini rtl">
<!--==========Header==============================-->
    @include('layouts.includes.backend.header')
<!-- Sidebar menu-->
    @include('layouts.includes.backend.sidebar')
<main class="app-content">
    <div id="content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-coffee"></i> <span class="header-title"></span> </h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a class="header-link"><span class="header-title"></span></a></li>
            </ul>
        </div>
        @yield('content')
    </div>
</main>
</body>
@include("layouts.includes.backend.scripts")
</html>

