@extends('layouts.app')
@section('title', '500 Server Error')
@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center" data-aos="fade-up">
        <div class="text-7xl font-extrabold gradient-text">500</div>
        <h1 class="text-2xl font-bold mt-4">Server Error</h1>
        <p class="text-gray-400 mt-2">Something went wrong. Please try again later.</p>
        <a href="{{ url('/') }}" class="btn-primary inline-block mt-6">Go Home</a>
    </div>
</div>
@endsection