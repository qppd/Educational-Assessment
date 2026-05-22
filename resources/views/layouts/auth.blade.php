<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', 'Sign In') — {{ config('app.name', 'TOPCIT') }}</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css'])

    @stack('head')
</head>
<body
    class="font-['Inter'] antialiased bg-slate-900 text-slate-100 min-h-screen"
    x-data="{
        flash: { show: false, type: 'success', message: '' },
        init() {
            window.addEventListener('flash', (e) => {
                this.flash.show = true;
                this.flash.type = e.detail.type;
                this.flash.message = e.detail.message;
                setTimeout(() => this.flash.show = false, 5000);
            });
        }
    }"
>
    {{-- Preloader --}}
    <div
        class="preloader fixed inset-0 z-[9999] flex items-center justify-center bg-slate-900 transition-opacity duration-500"
        x-data="{ loading: true }"
        x-init="window.addEventListener('load', () => setTimeout(() => loading = false, 400))"
        x-show="loading"
        x-transition:leave="opacity-0 duration-500"
        aria-hidden="true"
    >
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-slate-400">Loading...</span>
        </div>
    </div>

    {{-- Particles Background --}}
    <div id="particles" class="fixed inset-0 pointer-events-none z-0" aria-hidden="true"></div>

    {{-- Flash Messages --}}
    <div
        class="fixed top-4 right-4 z-[9998] max-w-sm w-full transition-all duration-300"
        x-show="flash.show"
        x-transition:enter="transform translate-x-0 opacity-100"
        x-transition:leave="transform translate-x-full opacity-0"
        x-cloak
        role="alert"
        aria-live="polite"
    >
        <div
            class="rounded-xl px-5 py-4 shadow-2xl border backdrop-blur-xl flex items-start gap-3"
            :class="{
                'bg-emerald-500/20 border-emerald-500/30 text-emerald-200': flash.type === 'success',
                'bg-red-500/20 border-red-500/30 text-red-200': flash.type === 'error',
                'bg-amber-500/20 border-amber-500/30 text-amber-200': flash.type === 'warning',
                'bg-sky-500/20 border-sky-500/30 text-sky-200': flash.type === 'info'
            }"
        >
            <span class="text-lg flex-shrink-0 mt-0.5" x-text="flash.type === 'success' ? '✓' : flash.type === 'error' ? '✕' : flash.type === 'warning' ? '⚠' : 'ℹ'"></span>
            <p class="text-sm font-medium flex-1" x-text="flash.message"></p>
            <button type="button" class="ml-2 text-current opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" @click="flash.show = false" aria-label="Close notification">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Fallback for server-side flashed messages --}}
    @if(session('success'))
        <div class="fixed top-4 right-4 z-[9998] max-w-sm w-full" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transform translate-x-0 opacity-100" x-transition:leave="transform translate-x-full opacity-0" x-cloak role="alert" aria-live="polite">
            <div class="rounded-xl px-5 py-4 shadow-2xl border backdrop-blur-xl flex items-start gap-3 bg-emerald-500/20 border-emerald-500/30 text-emerald-200">
                <span class="text-lg flex-shrink-0 mt-0.5">✓</span>
                <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
                <button type="button" class="ml-2 text-current opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" @click="show = false" aria-label="Close notification">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 right-4 z-[9998] max-w-sm w-full" x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:enter="transform translate-x-0 opacity-100" x-transition:leave="transform translate-x-full opacity-0" x-cloak role="alert" aria-live="polite">
            <div class="rounded-xl px-5 py-4 shadow-2xl border backdrop-blur-xl flex items-start gap-3 bg-red-500/20 border-red-500/30 text-red-200">
                <span class="text-lg flex-shrink-0 mt-0.5">✕</span>
                <p class="text-sm font-medium flex-1">{{ session('error') }}</p>
                <button type="button" class="ml-2 text-current opacity-60 hover:opacity-100 transition-opacity flex-shrink-0" @click="show = false" aria-label="Close notification">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    @endif

    {{-- Auth Content Container --}}
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div
            class="w-full max-w-md"
            data-aos="fade-up"
            data-aos-duration="600"
        >
            {{-- Logo / Brand --}}
            <div class="text-center mb-8">
                <h1 class="gradient-text text-4xl sm:text-5xl font-bold tracking-tight">
                    TOPCIT
                </h1>
                <p class="mt-2 text-sm text-slate-400">
                    Tech-based Online Platform for Collaborative and Interactive Testing
                </p>
            </div>

            {{-- Auth Card --}}
            <div class="glass rounded-2xl p-8 sm:p-10 shadow-2xl">
                @yield('auth-content')
            </div>

            {{-- Footer --}}
            <p class="mt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'TOPCIT') }}. All rights reserved.
            </p>
        </div>
    </div>

    {{-- Scripts --}}
    @stack('scripts')
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof createParticles === 'function') {
                const container = document.getElementById('particles');
                if (container) createParticles(container);
            }
        });
    </script>
</body>
</html>