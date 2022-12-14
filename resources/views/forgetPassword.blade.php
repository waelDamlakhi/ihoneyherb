@extends('layout.container')
@section('content')
    <h1 style="font-size: 1.5em; color: #333; margin-bottom: 1rem; font-weight: 600;">Hey {{ $name }}..</h1>
    <p style="color: #777">Forgot your password, don't worry, you can change it by pressing the button below</p>
    <a href={{ env('APP_WEBSITE') . "/change-password?token=" . $token }} class="link-btn-el">Change Password</a>
    <p style="color: #777">Thank you for using our website.</p>
    <p style="color: #777">Regards,</p>
    <p style="color: #777">{{ env('APP_WEBSITE') }}</p>
    <hr style="margin: 1.5rem 0">
    <p style="color: #777">
        if you're having trouble, 
        <a href={{ env('APP_WEBSITE') . '/contact' }} class="link-el">Contact US</a>
    </p>
@endsection