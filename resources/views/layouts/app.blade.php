<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Wiregrass Courier') }} - @yield('title', 'Your Whole Foods Courier')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased" x-data>
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold text-brand-700 text-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Wiregrass Courier
                </a>

                <!-- Desktop nav -->
                <div class="hidden sm:flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">Dashboard</a>
                        <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Deliveries</a>
                        <a href="{{ route('profile.edit') }}" class="text-sm text-gray-600 hover:text-gray-900">Profile</a>
                        <a href="{{ route('contact.show') }}" class="text-sm text-gray-600 hover:text-gray-900">Contact</a>
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-brand-700 hover:text-brand-900">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('contact.show') }}" class="text-sm text-gray-600 hover:text-gray-900">Contact</a>
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Log In</a>
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700">Get Started</a>
                    @endauth
                </div>

                <!-- Mobile hamburger -->
                <button @click="open = !open" class="sm:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" aria-label="Toggle menu">
                    <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-show="open" x-cloak @click.away="open = false"
            class="sm:hidden border-t border-gray-200 bg-white px-4 py-3 space-y-1">
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('orders.index') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Deliveries</a>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Profile</a>
                <a href="{{ route('contact.show') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Contact</a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-brand-700 hover:bg-brand-50">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-50">Log Out</button>
                </form>
            @else
                <a href="{{ route('contact.show') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Contact</a>
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50">Log In</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-brand-700 hover:bg-brand-50">Get Started</a>
            @endauth
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="border-t border-gray-200 mt-16 py-8 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Wiregrass Courier &mdash; Serving rural southern Alabama
    </footer>
</body>
</html>
