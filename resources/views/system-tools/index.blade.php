<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta name="base-url" content="{{ url('/') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/theme-chalk/index.css') }}">
        <link rel="stylesheet" href="{{ asset('css/system-tools.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}">
    </head>
    <body>
        <div id="app"></div>
        <script src="{{ asset('js/system-tools.js') }}"></script>
    </body>
</html>