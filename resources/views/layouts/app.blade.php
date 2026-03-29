<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCRYPT — Marketing Agent</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        scrypt: {
                            orange: '#FF6B35',
                            dark:   'var(--bg-main)',
                            card:   'var(--bg-card)',
                            border: 'var(--border-color)',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-main: #ffffff;
            --bg-card: #ffffff;
            --bg-sidebar: #f8fafc;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-header: #0f172a;
            --text-muted: #64748b;
            --text-dim: #94a3b8;
            --nav-hover: rgba(15, 23, 42, 0.05);
        }

        .dark {
            --bg-main: #000000;
            --bg-card: #0a0a0a;
            --bg-sidebar: #050505;
            --border-color: #1a1a1a;
            --text-main: #e2e8f0;
            --text-header: #ffffff;
            --text-muted: #94a3b8;
            --text-dim: #475569;
            --nav-hover: rgba(255, 255, 255, 0.05);
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        h1, h2, h3, .text-header { color: var(--text-header); }
        .text-main  { color: var(--text-main)   !important; }
        .text-muted { color: var(--text-muted)  !important; }
        .text-dim   { color: var(--text-dim)    !important; }

        .nav-link {
            @apply flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all text-sm font-medium;
            color: var(--text-muted);
        }
        .nav-link:hover {
            color: var(--text-header);
            background-color: var(--nav-hover);
        }
        .nav-link.active {
            @apply text-orange-400;
            background-color: rgba(255, 107, 53, 0.1);
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .btn-primary {
            background: #FF6B35;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: opacity 0.2s;
            display: inline-block;
        }
        .btn-primary:hover { opacity: 0.85; }

        .btn-ghost {
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-block;
        }
        .btn-ghost:hover { border-color: #FF6B35; color: #FF6B35; }

        .badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: 0.03em; }
        .badge-draft     { background: rgba(148,163,184,0.15); color: #64748b; }
        .badge-approved  { background: rgba(34,197,94,0.15);   color: #16a34a; }
        .badge-published { background: rgba(59,130,246,0.15);  color: #2563eb; }
        .badge-rejected  { background: rgba(239,68,68,0.15);   color: #dc2626; }
        .badge-new       { background: rgba(255,107,53,0.15);  color: #ea580c; }
        .badge-contacted { background: rgba(234,179,8,0.15);   color: #ca8a04; }
        .badge-qualified { background: rgba(168,85,247,0.15);  color: #9333ea; }
        .badge-meeting   { background: rgba(34,197,94,0.15);   color: #16a34a; }
        .badge-closed    { background: rgba(59,130,246,0.15);  color: #2563eb; }
        .badge-lost      { background: rgba(239,68,68,0.15);   color: #dc2626; }

        .dark .badge-draft     { color: #94a3b8; }
        .dark .badge-approved  { color: #22c55e; }
        .dark .badge-published { color: #3b82f6; }
        .dark .badge-rejected  { color: #ef4444; }
        .dark .badge-new       { color: #FF6B35; }
        .dark .badge-contacted { color: #eab308; }
        .dark .badge-qualified { color: #a855f7; }
        .dark .badge-meeting   { color: #22c55e; }
        .dark .badge-closed    { color: #3b82f6; }
        .dark .badge-lost      { color: #ef4444; }

        .input {
            background: var(--bg-main);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
        }
        .input:focus { outline: none; border-color: #FF6B35; }
        select.input option { background: var(--bg-card); color: var(--text-main); }
        textarea.input { resize: vertical; min-height: 80px; }

        .theme-toggle {
            @apply p-2 rounded-lg border transition-all;
            color: var(--text-muted);
            border-color: var(--border-color);
        }
        .theme-toggle:hover {
            background: var(--nav-hover);
            color: var(--text-header);
        }

        #mobile-overlay { transition: opacity 0.3s ease; }
        #sidebar        { transition: transform 0.3s ease; }

        .border-scrypt-border { border-color: var(--border-color); }

        .nav-section {
            padding: 12px 16px 4px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--text-dim);
        }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const isOpen  = sidebar.style.transform === 'translateX(0%)';

            if (isOpen) {
                sidebar.style.transform = 'translateX(-100%)';
                overlay.classList.add('hidden');
                overlay.style.opacity  = '0';
            } else {
                sidebar.style.transform = 'translateX(0%)';
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.style.opacity = '1', 10);
            }
        }
    </script>
</head>
<body class="min-h-screen">

<div class="flex min-h-screen relative overflow-hidden">

    {{-- Mobile Overlay --}}
    <div id="mobile-overlay"
         onclick="toggleSidebar()"
         class="fixed inset-0 bg-black/50 z-40 hidden opacity-0 transition-opacity lg:hidden">
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           class="fixed inset-y-0 left-0 w-64 z-50 transform -translate-x-full border-r border-scrypt-border flex flex-col lg:relative lg:translate-x-0 transition-transform duration-300"
           style="background: var(--bg-sidebar)">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-scrypt-border">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background:#FF6B35">
                    <span class="text-white font-bold text-xs">SC</span>
                </div>
                <div>
                    <div class="font-bold text-sm leading-none" style="color:var(--text-header)">SCRYPT</div>
                    <div class="text-xs mt-0.5 text-muted">Marketing Agent</div>
                </div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            <div class="nav-section">Main</div>

            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section">Content</div>

            <a href="{{ route('content.index') }}"
               class="nav-link {{ request()->routeIs('content.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Content
            </a>

            <a href="{{ route('calendar') }}"
               class="nav-link {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendar
            </a>

            <div class="nav-section">Growth</div>

            <a href="{{ route('leads.index') }}"
               class="nav-link {{ request()->routeIs('leads.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Leads
            </a>

            <a href="{{ route('engagement.index') }}"
               class="nav-link {{ request()->routeIs('engagement.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Engagement
            </a>

            <a href="{{ route('kpi') }}"
               class="nav-link {{ request()->routeIs('kpi') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Analytics
            </a>

            <div class="nav-section">Account</div>
            <a href="{{ route('account.show') }}" class="nav-link {{ request()->routeIs('account.*') ? 'active' : '' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Settings
            </a>

        </nav>

        {{-- Sidebar Footer --}}
        <div class="px-4 py-4 border-t border-scrypt-border space-y-3">

            {{-- Logged in user --}}
            @auth
            <div class="flex items-center gap-2 px-1">
                <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 text-white text-xs font-bold"
                     style="background:#FF6B35">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-medium truncate" style="color:var(--text-main)">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="text-xs truncate text-muted">{{ auth()->user()->email }}</div>
                </div>
            </div>
            @endauth

            <div class="text-xs text-muted">scrypt.swiss · 2026</div>

            {{-- Sign out --}}
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left text-xs px-3 py-2 rounded-lg transition-all"
                        style="color:var(--text-muted);border:1px solid var(--border-color)"
                        onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'"
                        onmouseout="this.style.borderColor='var(--border-color)';this.style.color='var(--text-muted)'">
                    Sign out
                </button>
            </form>
            @endauth

        </div>

    </aside>

    {{-- Main --}}
    <main class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="h-14 border-b border-scrypt-border flex items-center justify-between px-4 md:px-6 shrink-0"
                style="background: var(--bg-sidebar)">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 -ml-2 rounded-lg transition-colors"
                        style="color:var(--text-muted)"
                        onmouseover="this.style.color='var(--text-header)'"
                        onmouseout="this.style.color='var(--text-muted)'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-sm font-semibold" style="color:var(--text-header)">@yield('title', 'Dashboard')</h1>
            </div>

            <div class="flex items-center gap-2 md:gap-3">
                <button onclick="toggleTheme()" class="theme-toggle" title="Toggle theme">
                    <svg class="w-4 h-4 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg class="w-4 h-4 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                <div class="hidden sm:block">
                    @yield('header-actions')
                </div>
            </div>
        </header>

        {{-- Header actions (mobile fallback) --}}
        @hasSection('header-actions')
        <div class="sm:hidden px-4 pt-4">
            @yield('header-actions')
        </div>
        @endif

        {{-- Flash: success --}}
        @if(session('success'))
        <div class="mx-4 md:mx-6 mt-4 px-4 py-3 rounded-lg text-sm font-medium"
             style="background:rgba(34,197,94,0.1);color:#22c55e;border:1px solid rgba(34,197,94,0.2)">
            {{ session('success') }}
        </div>
        @endif

        {{-- Flash: error --}}
        @if(session('error'))
        <div class="mx-4 md:mx-6 mt-4 px-4 py-3 rounded-lg text-sm font-medium"
             style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.2)">
            {{ session('error') }}
        </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
        <div class="mx-4 md:mx-6 mt-4 px-4 py-3 rounded-lg text-sm"
             style="background:rgba(239,68,68,0.08);color:#ef4444;border:1px solid rgba(239,68,68,0.2)">
            <div class="font-medium mb-1">Please fix the following errors:</div>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Page content --}}
        <div class="flex-1 p-4 md:p-6 overflow-auto">
            @yield('content')
        </div>

    </main>
</div>

</body>
</html>