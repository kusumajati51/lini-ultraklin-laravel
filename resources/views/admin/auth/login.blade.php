<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="{{ asset('css/uikit.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/montserrat.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ultraklin.css') }}?{{ time() }}">
    </head>
    <body class="uk--login-page">
        <div class="uk--login-container">
            <div class="uk--login-box uk-grid-collapse" uk-grid>
                <div class="uk--login-box-content">
                    
                </div>
                <div class="uk--login-box-form">
                    <form action="{{ url('/admin/login') }}" method="POST">
                        {{ csrf_field() }}
                        <div class="uk-margin">
                            <input class="uk-input" type="text" name="email" placeholder="Email">
                            {!! $errors->first('email', '<p class="uk--input-error">:message</p>') !!}
                        </div>
                        <div class="uk-margin">
                            <input class="uk-input" type="password" name="password" placeholder="Password">
                            {!! $errors->first('password', '<p class="uk--input-error">:message</p>') !!}
                        </div>
                        <div class="uk-margin">
                            <button class="uk-button uk--button-primary uk-width-1-1" type="submit">Sign In</button>
                        </div>
                    </form>
                </div>
            <div>
        </div>
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/uikit.min.js') }}"></script>
    </body>
</html>