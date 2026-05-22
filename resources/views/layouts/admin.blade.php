<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — TOPCIT</title>
    <link rel="icon" href="{{ url('storage/images/topcitlogo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.tailwindcss.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        /* Override DataTables */
        div.dt-container .dt-length, div.dt-container .dt-search {
            margin-bottom: 1rem;
        }
        div.dt-container .dt-paging nav {
            margin-top: 1rem;
        }
        .dt-container select, .dt-container input {
            @apply input-field inline-block w-auto;
        }
    </style>
</head>
<body x-data="{ sidebar: true, mobileSidebar: false }" class="bg-dark text-gray-100 font-sans antialiased min-h-screen">

    <!-- Flash Messages (same as app layout) -->
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

    <!-- Mobile Overlay -->
    <div x-show="mobileSidebar" x-cloak @@click="mobileSidebar = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside x-show="sidebar || mobileSidebar" x-cloak
           :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-0 left-0 bottom-0 w-64 bg-surface border-r border-white/5 z-50 transition-transform duration-300 overflow-y-auto">
        <div class="p-4 border-b border-white/5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🎓</span>
                <div>
                    <h1 class="font-bold gradient-text text-lg">TOPCIT</h1>
                    <p class="text-xs text-gray-500">Admin Panel</p>
                </div>
            </div>
        </div>
        <nav class="p-3 space-y-1">
            <a href="{{ url('/admin/home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ request()->is('admin/home') ? 'bg-primary/20 text-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span>📊</span> Dashboard
            </a>
            <a href="{{ url('/admin/administrators') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ request()->is('admin/administrators*') ? 'bg-primary/20 text-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span>👥</span> Administrators
            </a>
            <a href="{{ url('/admin/students') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ request()->is('admin/students*') ? 'bg-primary/20 text-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span>👨‍🎓</span> Students
            </a>
            <a href="{{ url('/admin/professors') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ request()->is('admin/professors*') ? 'bg-primary/20 text-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span>👨‍🏫</span> Professors
            </a>
            <a href="{{ url('/admin/examinations') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors
                {{ request()->is('admin/examinations*') ? 'bg-primary/20 text-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
                <span>📝</span> Examinations
            </a>
            <hr class="border-white/5 my-2">
            <a href="{{ url('/admin/logout') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-500 hover:bg-white/5 hover:text-danger transition-colors">
                <span>🚪</span> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div :class="sidebar ? 'lg:ml-64' : ''" class="transition-all duration-300 min-h-screen">
        <!-- Top Navbar -->
        <header class="sticky top-0 z-30 bg-dark/80 backdrop-blur-lg border-b border-white/5">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <button @@click="sidebar = !sidebar; if(window.innerWidth < 1024) mobileSidebar = !mobileSidebar"
                            class="text-gray-400 hover:text-white p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <h2 class="text-sm font-medium text-gray-300 truncate">@yield('page-title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 hidden sm:inline">
                        {{ session('name') ?? 'Admin' }}
                    </span>
                    <div class="w-8 h-8 rounded-full bg-primary/30 flex items-center justify-center text-sm font-medium">
                        {{ substr(session('name') ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 md:p-6">
            @yield('admin-content')
        </main>
    </div>

    @stack('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
    @stack('datatables')
</body>
</html>