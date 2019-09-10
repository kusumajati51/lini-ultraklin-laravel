@extends('layouts.__app_blank')

@section('content')
<div class="full-page uk-flex uk-flex-center uk-flex-middle">
    <div class="uk-card uk-card-default uk-card-small" style="width: 360px;">
        <div class="uk-card-header uk-text-center">
            <h3 class="uk-card-title">Reset Password</h3>
        </div>
        <div class="uk-card-body">
            <form method="POST" action="{{ url('/password/reset') }}">
                {{ csrf_field() }}

                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="uk-margin">
                    <input class="uk-input rounded" type="email" name="email" value="{{ $email or old('email') }}"  placeholder="Email" />
                    {!! $errors->first('email', '<p class="uk-margin-small uk-text-danger uk-text-center">:message</p>') !!}
                </div>
                <div class="uk-margin">
                    <input class="uk-input rounded" type="password" name="password" placeholder="Password" />
                    {!! $errors->first('password', '<p class="uk-margin-small uk-text-danger uk-text-center">:message</p>') !!}
                </div>
                <div class="uk-margin">
                    <input class="uk-input rounded" type="password" name="password_confirmation" placeholder="Confirm Password" />
                    {!! $errors->first('password_confirmation', '<p class="uk-margin-small uk-text-danger uk-text-center">:message</p>') !!}
                </div>
                <div class="uk-margin">
                    <button class="uk-button uk-button-primary uk-width-1-1 rounded" type="submit">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop