@extends('layout.container')
@section('content')
    <h1 style="font-size: 1.5em; color: #333; margin-bottom: 1rem; font-weight: 600;">Hello {{ $name }}..</h1>
    <h3 style="color: #777">Welcome To IHoneyHerb Store</h3>
    <p style="color: #777">Your Verification Code Is :</p>
    <p class="link-btn-el">{{ $code }}</p>
@endsection