<!DOCTYPE html>
<html>
    <head>
        <title>{{ $page['title'] }}</title>
        <meta name="url" content="{{ url('/') }}">
        <meta name="token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <style>
            html, body,
            h1, h2, h3, h4, h5, h6 {
                font-family: "Courier";
            }

            .uk-table th,
            .uk-table td {
                font-size: 1.1rem;
            }

            .uk--print-header {
                padding: 10px 0;
                border-bottom: dashed 2px #666666;
            }

            .uk--box-label {
                display: block;
                font-size: 0.9rem;
                text-transform: uppercase;
                color: #333;
            }

            .uk--box-text {
                display: block;
                font-size: 1.1rem;
            }
        </style>
    <body>
        <div class="uk-container">
            @yield('content')
        </div>
    </body>
</html>
