<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ config('app.name', 'Wiregrass Courier') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">

        <!-- ── Desktop sidebar (always visible, in flex flow) ── -->
        <aside class="hidden lg:flex flex-col w-64 bg-brand-800 text-white flex-shrink-0">
            <div class="p-6 border-b border-brand-700">
                <a href="{{ route('home') }}" class="font-semibold text-lg">Wiregrass Courier</a>
                <p class="text-brand-300 text-xs mt-1">Admin Panel</p>
            </div>
            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Deliveries
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Users
                </a>
                <a href="{{ route('admin.subscriptions.index') }}"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.subscriptions.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    Subscriptions
                </a>
            </nav>
            <div class="p-4 border-t border-brand-700">
                <p class="text-brand-300 text-xs">{{ auth()->user()->name }}</p>
                <a href="{{ route('dashboard') }}" class="text-brand-400 text-xs hover:text-brand-200">← Customer View</a>
            </div>
        </aside>

        <!-- ── Mobile sidebar overlay ── -->
        <div x-show="sidebarOpen" x-cloak class="lg:hidden">
            <!-- Backdrop -->
            <div @click="sidebarOpen = false" class="fixed inset-0 z-20 bg-black/50"></div>
            <!-- Drawer -->
            <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-brand-800 text-white flex flex-col">
                <div class="p-6 border-b border-brand-700 flex items-center justify-between">
                    <div>
                        <a href="{{ route('home') }}" class="font-semibold text-lg">Wiregrass Courier</a>
                        <p class="text-brand-300 text-xs mt-1">Admin Panel</p>
                    </div>
                    <button @click="sidebarOpen = false" class="text-brand-300 hover:text-white">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 p-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.orders.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Deliveries
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.users.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Users
                    </a>
                    <a href="{{ route('admin.subscriptions.index') }}"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('admin.subscriptions.*') ? 'bg-brand-700 text-white' : 'text-brand-200 hover:bg-brand-700' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Subscriptions
                    </a>
                </nav>
                <div class="p-4 border-t border-brand-700">
                    <p class="text-brand-300 text-xs">{{ auth()->user()->name }}</p>
                    <a href="{{ route('dashboard') }}" class="text-brand-400 text-xs hover:text-brand-200">← Customer View</a>
                </div>
            </aside>
        </div>

        <!-- ── Main content ── -->
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">
            <header class="bg-white shadow-sm px-4 sm:px-8 py-4 flex items-center gap-4">
                <!-- Hamburger (mobile only) -->
                <button @click="sidebarOpen = true" class="lg:hidden p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-xl font-semibold text-gray-800 truncate">@yield('title', 'Dashboard')</h1>
            </header>

            @if(session('success'))
                <div class="mx-4 sm:mx-8 mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <main class="flex-1 overflow-y-auto p-4 sm:p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
