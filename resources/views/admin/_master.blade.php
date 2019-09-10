<!DOCTYPE html>
<html>

<head>
    <meta name="url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('json/manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('css/pace-theme-cube.css') }}"> 
    <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-chalk/index.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ultraklin.css') }}?{{ time() }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU"
        crossorigin="anonymous">
</head>

<body>
    <div id="chat-app"></div>
    <div class="uk-container uk-padding-small">
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-medium">
    @include('admin._sidebar')
            </div>
            <div class="uk-width-expand">
                @yield('content')
            </div>
        </div>
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
                token: document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }

            Vue.config.devtools = true

            ELEMENT.locale(ELEMENT.lang.en)</script>
    <script src="{{asset('js/manifest.js')}}"></script>
    <script src="{{asset('js/vendor.js')}}"></script>
    <script src="{{ asset('js/firebase.js') }}"></script>
    {{--  <script src="{{ asset('js/chat.js') }}"></script>  --}}
    @yield('scripts')
</body>

</html>