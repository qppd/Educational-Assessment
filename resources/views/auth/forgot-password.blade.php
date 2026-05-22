@extends('layouts.app')

@section('title', 'Forgot Password - TOPCIT')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md" data-aos="fade-up" data-aos-duration="800">
        <div class="text-center mb-8">
            <div class="text-5xl mb-3">🔑</div>
            <h1 class="text-3xl font-extrabold gradient-text">Reset Password</h1>
            <p class="text-gray-400 text-sm mt-2">Enter your student number to reset your password</p>
        </div>

        <div class="card">
            <form method="POST" action="{{ url('/portal/forgot/password/update') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Student Number</label>
                        <input type="text" name="username" class="input-field" placeholder="Enter your student number" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">New Password</label>
                        <input type="password" name="ppassword" class="input-field" placeholder="At least 8 characters" minlength="8" required>
                    </div>
                    <button type="submit" class="btn-primary w-full ripple">Reset Password</button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ url('/portal') }}" class="text-sm text-primary hover:text-secondary transition-colors">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection