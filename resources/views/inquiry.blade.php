
@extends('layout.container')
@section('content')
    <h1 style="font-size: 1.5em; color: #333; margin-bottom: 1rem; font-weight: 600;">Hey {{ env('APP_NAME') }}...</h1>
    <h3 style="color: #777">I Have Message For You:</h3>
    <p style="color: #777">{{ $message }}</p>
    <p style="color: #777">Thank you for your help.</p>
    <p style="color: #777">Regards,</p>
    <p style="color: #777">{{ $name }}</p>
    <hr style="margin: 1.5rem 0">
    <p style="color: #777">
        If you want to contact {{ $name }}, here is his email 
        <a href={{ "mailto:" . $email }} class="link-el">{{ $email }}</a>
    </p>
@endsection