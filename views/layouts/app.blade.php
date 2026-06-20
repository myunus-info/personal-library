<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Library Management') - Antigravity Library</title>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/css/app.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full text-slate-100 antialiased flex">

    @if(\App\Core\Auth::check())
        <!-- Sidebar Navigation -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 flex flex-col justify-between shrink-0">
            <div>
                <!-- Logo / Header -->
                <div class="h-16 flex items-center px-6 border-b border-slate-800 gap-2">
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-violet-400 bg-clip-text text-transparent">Antigravity Lib</span>
                    <span class="px-2 py-0.5 text-xs bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/20">Admin</span>
                </div>

                <!-- Nav Links -->
                <nav class="p-4 space-y-1">
                    <a href="/" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition duration-200 {{ \App\Core\Request::path() === '/' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="/books/create" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition duration-200 {{ \App\Core\Request::path() === '/books/create' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add New Book
                    </a>
                    <a href="/tags" class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition duration-200 {{ \App\Core\Request::path() === '/tags' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Categories / Tags
                    </a>
                </nav>
            </div>

            <!-- Footer / Session User info -->
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center font-bold text-white shadow-md">
                            {{ strtoupper(substr($admin['username'] ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Logged in as</p>
                            <p class="text-sm font-semibold text-slate-200">{{ $admin['username'] ?? 'Admin' }}</p>
                        </div>
                    </div>
                </div>
                <a href="/logout" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 text-xs font-semibold bg-slate-800 hover:bg-red-500/10 hover:text-red-400 text-slate-300 rounded-xl transition duration-200 border border-slate-700/50 hover:border-red-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout Session
                </a>
            </div>
        </aside>
    @endif

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
        <!-- Top bar (only if authenticated) -->
        @if(\App\Core\Auth::check())
            <header class="h-16 border-b border-slate-800 flex items-center justify-between px-8 bg-slate-950/80 backdrop-blur sticky top-0 z-10">
                <h1 class="text-lg font-bold text-slate-200">@yield('page_title', 'Dashboard')</h1>
                <div class="text-sm text-slate-400" id="current-time"></div>
            </header>
        @endif

        <div class="flex-1 p-8">
            <!-- Flash Notification Alerts -->
            @if($success)
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-between shadow-lg shadow-emerald-950/10 alert-dismissible">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">{{ $success }}</p>
                    </div>
                    <button class="text-slate-400 hover:text-slate-200 transition focus:outline-none close-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if($error)
                <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 text-rose-400 rounded-xl flex items-center justify-between shadow-lg shadow-rose-950/10 alert-dismissible">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">{{ $error }}</p>
                    </div>
                    <button class="text-slate-400 hover:text-slate-200 transition focus:outline-none close-btn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- Main Content Container -->
            @yield('content')
        </div>
    </main>

    <!-- Custom Javascript -->
    <script src="/js/app.js"></script>
    <script>
        // Real-time Clock
        function updateClock() {
            const timeEl = document.getElementById('current-time');
            if (timeEl) {
                const now = new Date();
                timeEl.textContent = now.toLocaleDateString('en-US', { 
                    weekday: 'short', 
                    month: 'short', 
                    day: 'numeric',
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                });
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
