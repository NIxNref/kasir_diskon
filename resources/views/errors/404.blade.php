@extends('errors::minimal')

@section('title', '404 - Page not found')

@section('content')
<div id="notfound">
    <div class="notfound">
        <div class="notfound-404">
            <h1>:(</h1>
        </div>
        <h2>404 - Page not found</h2>
        <p>The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="{{ url('/') }}">Home Page</a>
    </div>
</div>
@endsection
