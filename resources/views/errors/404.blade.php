@extends('layouts.app')
@section('title', '404 Not Found')
@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center" data-aos="fade-up">
        <div class="text-7xl font-extrabold gradient-text">404</div>
        <h1 class="text-2xl font-bold mt-4">Page Not Found</h1>
        <p class="text-gray-400 mt-2">The page you're looking for doesn't exist.</p>
        <a href="{{ url('/') }}" class="btn-primary inline-block mt-6">Go Home</a>
    </div>
</div>
@endsection