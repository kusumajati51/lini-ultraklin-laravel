<!DOCTYPE html>
<html>
    <head>
        <title>{{ $page['title'] }}</title>
        <meta name="url" content="{{ url('/') }}">
        <meta name="token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <style>
            html, body,
            h1, h2, h3, h4, h5, h6,
            th, td,
            div {
                font-family: Arial;
                font-size: 0.8rem;
                font-weight: bold;
                color: #000;
            }

            th {
                text-align: left;
            }

            .page-container {
                position: relative;
                min-height: 12cm;
                max-height: 12cm;
                padding: 2mm;
                border: dotted 2px #EFEFEF;
            }

            .page-footer {
                position: absolute;
                left: 0;
                bottom: 0;
                width: 100%;
                padding-top: 10px;
                border-top: dashed 2px #000;
            }

            .page-footer .page-signature {
                height: 1cm;
            }

            .page-divider {
                margin: 10px 0;
                border-bottom: dashed 2px #000;
            }

            .page-table {
                width: 100%;
            }

            .page-table thead th {
                text-transform: uppercase;
            }

            .page-table-spacer {
                height: 5px;
            }

            @media print {
                .page-container {
                    border: none;
                }
            }
        </style>
    <body>
        <div class="uk-container">
            @yield('content')
        </div>
        <script src="{{ asset('js/uikit.min.js') }}"></script>
    </body>
</html>
