@extends('layouts.__app_blank')

@section('content')
<div class="full-page uk-flex uk-flex-center uk-flex-middle">
    <div class="uk-card uk-card-default uk-card-small" style="width: 360px;">
        <div class="uk-card-header uk-text-center">
            <h3>Password Reset</h3>
        </div>
        <div class="uk-card-body uk-flex uk-flex-middle uk-flex-center" style="height: 200px;">
            <div class="uk-text-center">
                <i class="fas fa-check fa-2x text-green"></i>
                <p>{{ "Your password was reset." }}</p>
                <form class="uk-margin-large-top" method="POST" action="{{ url('/user/logout') }}">
                    {{ csrf_field() }}
                    <button class="uk-button uk-button-danger rounded" type="submit">SIGN OUT</button>
                </form>
            </div>
        </div>
        <div class="uk-card-footer uk-text-center">
            <a href="https://play.google.com/store/apps/details?id=lintasinsan.app.ultraklinapps">
                <img src="{{ asset('/images/play_store.png') }}" width="150" />
            </a>
            <a href="https://itunes.apple.com/us/app/ultraklin/id1303429279">
                <img src="{{ asset('/images/app_store.png') }}" width="150" />
            </a>
        </div>
    </div>
</div>
@stop