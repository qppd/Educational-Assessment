@extends('layouts.app')

@section('title', 'Login - TOPCIT')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" data-aos="fade-up" data-aos-duration="800">
        <!-- Logo & Brand -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🎓</div>
            <h1 class="text-4xl font-extrabold gradient-text">TOPCIT</h1>
            <p class="text-gray-400 text-sm mt-2">Tech-based Online Platform for Collaborative and Interactive Testing</p>
        </div>

        <!-- Login Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-1">Welcome Back</h2>
            <p class="text-gray-400 text-sm mb-6">Sign in to your account to continue</p>

            <form method="POST" action="{{ url('/portal/login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Student Number</label>
                        <input type="text" name="username" class="input-field" placeholder="Enter your student number"
                               value="{{ old('username') }}" required autofocus>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                        <input type="password" name="password" class="input-field" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-primary w-full ripple">Sign In</button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-white/10 space-y-3 text-center text-sm">
                <a href="{{ url('/portal/register') }}" class="text-primary hover:text-secondary transition-colors">
                    Don't have an account? Register
                </a>
                <a href="{{ url('/portal/forgot') }}" class="block text-gray-400 hover:text-white transition-colors">
                    Forgot password?
                </a>
                <div class="flex items-center justify-center gap-2 pt-2">
                    <span class="text-lg">📸</span>
                    <span class="text-xs text-gray-500">Face Recognition Ready</span>
                    <span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
                </div>
            </div>
        </div>

        <!-- Admin / Faculty Links -->
        <div class="mt-6 text-center space-x-4 text-xs text-gray-500">
            <a href="{{ url('/admin') }}" class="hover:text-primary transition-colors">Admin</a>
            <span>•</span>
            <a href="{{ url('/faculty') }}" class="hover:text-primary transition-colors">Faculty</a>
        </div>
    </div>
</div>
@endsection