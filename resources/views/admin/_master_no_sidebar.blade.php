<!DOCTYPE html>
<html>

<head>
    <meta name="url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/pace-theme-cube.css') }}">
    <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-chalk/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ultraklin.css') }}?{{ time() }}">
</head>

<body>
    <div class="uk-container uk-margin-top uk-margin-bottom">
        @yield('content')
    </div>
    <script src="{{ asset('js/pace.min.js') }}"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/uikit.min.js') }}"></script>
    <script src="{{ asset('js/vue.min.js') }}"></script>
    <script src="{{ asset('js/element-ui.min.js') }}"></script>
    <script src="{{ asset('js/element-ui-en.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script>
        window.Laravel = {
                url: document.querySelector('meta[name=url]').getAttribute('content'),
                token: document.querySelector('meta[name=token').getAttribute('content')
            }

            Vue.config.devtools = true

            ELEMENT.locale(ELEMENT.lang.en)
    </script>
    @yield('scripts')
</body>

</html>