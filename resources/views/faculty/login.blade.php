@extends('layouts.app')

@section('title', 'Faculty Login - TOPCIT')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" data-aos="fade-up" data-aos-duration="800">
        <!-- Logo & Brand -->
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🎓</div>
            <h1 class="text-4xl font-extrabold gradient-text">TOPCIT</h1>
            <p class="text-gray-400 text-sm mt-2">Faculty Portal</p>
        </div>

        <!-- Login Card -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-1">Faculty Login</h2>
            <p class="text-gray-400 text-sm mb-6">Sign in with your faculty credentials</p>

            <form method="POST" action="{{ url('/faculty/login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Username</label>
                        <input type="text" name="username" class="input-field" placeholder="Enter your username"
                               value="{{ old('username') }}" required autofocus>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                        <input type="password" name="password" class="input-field" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn-primary w-full ripple">Sign In</button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-white/10 text-center text-sm text-gray-500">
                <a href="{{ url('/portal') }}" class="hover:text-primary transition-colors">Student Portal</a>
                <span class="mx-2">•</span>
                <a href="{{ url('/admin') }}" class="hover:text-primary transition-colors">Admin Login</a>
            </div>
        </div>
    </div>
</div>
@endsection