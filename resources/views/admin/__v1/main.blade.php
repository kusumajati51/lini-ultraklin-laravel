<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-chalk/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/solid.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    <div id="chat-app"></div>
    <div id="app"></div>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script src="{{asset('js/manifest.js')}}"></script>
    <script src="{{asset('js/vendor.js')}}"></script>
    <script src="{{asset('js/firebase.js')}}"></script>
    {{--  <script src="{{asset('js/chat.js')}}"></script>  --}}
</body>



</html>