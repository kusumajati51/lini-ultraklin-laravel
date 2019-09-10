<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta name="base-url" content="{{ url('/') }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ultraklin.css') }}?{{ time() }}">
    </head>
    <body>
        <div class="full-page">
            @yield('content')
        </div>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/uikit.min.js') }}"></script>
    </body>
</html>