<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TOPCIT') — Educational Assessment</title>
    <link rel="icon" href="{{ url('storage/images/topcitlogo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body x-data="{ sidebarOpen: true, darkMode: true }" class="bg-dark text-gray-100 font-sans antialiased min-h-screen" x-init="createParticles(document.getElementById('particles'))">

    <!-- Preloader -->
    <div class="preloader" x-data x-init="window.addEventListener('load', () => $el.classList.add('fade-out'))">
        <div class="preloader-spinner"></div>
    </div>

    <!-- Particles -->
    <div id="particles" class="particles"></div>

    <!-- Flash Messages -->
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
         class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
        @if (session('success'))
            <div class="glass-strong border-l-4 border-success px-4 py-3 flex items-center gap-3">
                <span class="text-success text-lg">✓</span>
                <span class="text-sm">{{ session('success') }}</span>
                <button @click="$el.parentElement.remove()" class="ml-auto text-gray-400 hover:text-white">&times;</button>
            </div>
        @endif
        @if ($errors->any())
            <div class="glass-strong border-l-4 border-danger px-4 py-3">
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-danger text-lg">✕</span>
                    <span class="text-sm font-medium">Error</span>
                    <button @click="$el.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-white">&times;</button>
                </div>
                <ul class="text-xs text-gray-400 ml-7 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="relative z-10">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>