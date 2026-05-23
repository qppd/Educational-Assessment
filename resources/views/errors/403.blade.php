@extends('layouts.app')
@section('title', '403 Forbidden')
@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="text-center" data-aos="fade-up">
        <div class="text-7xl font-extrabold gradient-text">403</div>
        <h1 class="text-2xl font-bold mt-4">Access Denied</h1>
        <p class="text-gray-400 mt-2">You don't have permission to access this page.</p>
        <a href="{{ url('/') }}" class="btn-primary inline-block mt-6">Go Home</a>
    </div>
</div>
@endsection