@extends('layout.container')
@section('content')
    <h1 style="font-size: 1.5em; color: #333; margin-bottom: 1rem; font-weight: 600;">Hey {{ $name }}..</h1>
    <p style="color: #777">Forgot your password, don't worry, you can change it by pressing the button below</p>
    <a href={{ env('APP_WEBSITE') . "/change-password?token=" . $token }} class="link-btn-el">Change Password</a>
@endsection