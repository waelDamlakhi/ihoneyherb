@extends('layout.container')
@section('content')
    <h1 style="font-size: 1.5em; color: #333; margin-bottom: 1rem; font-weight: 600;">Hello {{ $name }}..</h1>
    <h3 style="color: #777">Welcome To IHoneyHerb Store</h3>
    <p style="color: #777">Your Verification Code Is :</p>
    <p class="link-btn-el">{{ $code }}</p>
    <p style="color: #777">Thank you for using our website.</p>
    <p style="color: #777">Regards,</p>
    <p style="color: #777">{{ env('APP_WEBSITE') }}</p>
    <hr style="margin: 1.5rem 0">
    <p style="color: #777">
        if you're having trouble, 
        <a href={{ env('APP_WEBSITE') . '/contact' }} class="link-el">Contact US</a>
    </p>
@endsection