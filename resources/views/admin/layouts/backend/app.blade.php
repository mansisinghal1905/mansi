<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
<meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="theme_ocean" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! BEGIN: Apps Title-->
    <title> Office Management</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('public/assets/images/favicon.ico')}}" />

    <!--! END: Favicon-->
    @include('admin.layouts.backend.headerLinks')
    @stack('style')

</head>
<body>
@include('admin.layouts.backend.flash_message')
    @include('admin.layouts.backend.header')
    @include('admin.layouts.backend.rightSideBar')
    @include('admin.layouts.backend.sidebar')
    @yield('content')
    @include('admin.layouts.backend.footerLinks')
   
    @stack('script')
   
</body>

</html>