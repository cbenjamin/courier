<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EverRoots — Whole Foods Delivered to Rural Southern Alabama</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white antialiased" x-data>

    <!-- Nav -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-sm border-b border-gray-100 z-50">
        <div class="max-w-6xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2 font-semibold text-brand-700 text-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                EverRoots
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('contact.show') }}" class="text-sm text-gray-600 hover:text-gray-900">Contact</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-sm font-medium bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700 transition-colors">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">Log In</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-brand-600 text-white px-4 py-2 rounded-lg hover:bg-brand-700 transition-colors">
                        Get Started
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-32 pb-20 px-6 bg-gradient-to-b from-brand-50 to-white">
        <div class="max-w-4xl mx-auto text-center">
            <p class="text-brand-600 font-semibold text-sm uppercase tracking-widest mb-4">Serving Rural Southern Alabama</p>
            <h1 class="text-5xl font-bold text-gray-900 leading-tight mb-6">
                Whole Foods, <span class="text-brand-600">delivered to your door.</span>
            </h1>
            <p class="text-xl text-gray-500 max-w-2xl mx-auto mb-10">
                We drive to the nearest Whole Foods and bring back exactly what you need — so you don't have to make the trip.
                One-time orders or a monthly subscription. You choose.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}"
                    class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-8 py-4 rounded-xl text-lg transition-colors">
                    Request Your First Delivery
                </a>
                <a href="#how-it-works"
                    class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold px-8 py-4 rounded-xl text-lg transition-colors">
                    How It Works
                </a>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20 px-6 bg-white">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900">Simple as three steps</h2>
                <p class="text-gray-500 mt-3">We handle the drive so you don't have to.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <div class="text-center">
                    <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="text-brand-500 font-bold text-sm mb-2">Step 1</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Create Your Account</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Sign up in minutes. Tell us your delivery address and any special instructions.</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <div class="text-brand-500 font-bold text-sm mb-2">Step 2</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Place Your Order</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Give us your order pickup link and the pickup time.</p>
                </div>
                <div class="text-center">
                    <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="text-brand-500 font-bold text-sm mb-2">Step 3</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">We Deliver</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Your courier picks up from Whole Foods and brings everything right to your door.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="py-20 px-6 bg-brand-50">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-gray-900">Simple, honest pricing</h2>
                <p class="text-gray-500 mt-3">Pay per order or save with a monthly subscription.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Ad-hoc -->
                <div class="bg-white rounded-2xl border border-gray-200 p-8">
                    <p class="text-sm font-semibold uppercase tracking-wide text-gray-500 mb-3">One-Time</p>
                    <p class="text-4xl font-bold text-gray-900 mb-1">${{ number_format($adhocPrice, 0) }}<span class="text-lg font-normal text-gray-400"> / order</span></p>
                    <p class="text-gray-500 text-sm mb-6">Pay only when you need us.</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            No commitment
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Secure card payment
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Same great service
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Order anytime
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="block text-center border border-brand-600 text-brand-700 hover:bg-brand-50 font-semibold px-6 py-3 rounded-xl transition-colors">
                        Order Now
                    </a>
                </div>

                <!-- Subscription -->
                <div class="bg-brand-700 rounded-2xl p-8 text-white relative overflow-hidden">
                    <div class="absolute top-4 right-4 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-full">BEST VALUE</div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-brand-300 mb-3">Monthly Plan</p>
                    <p class="text-4xl font-bold mb-1">${{ number_format($subscriptionPrice, 0) }}<span class="text-lg font-normal text-brand-300"> / mo</span></p>
                    <p class="text-brand-300 text-sm mb-6">Up to 4 deliveries per month — that's ${{ number_format($subscriptionPrice / 4, 2) }} each.</p>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center gap-3 text-sm text-brand-100">
                            <svg class="w-5 h-5 text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            4 deliveries per month
                        </li>
                        <li class="flex items-center gap-3 text-sm text-brand-100">
                            <svg class="w-5 h-5 text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save over $20 vs. ad-hoc
                        </li>
                        <li class="flex items-center gap-3 text-sm text-brand-100">
                            <svg class="w-5 h-5 text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Priority scheduling
                        </li>
                        <li class="flex items-center gap-3 text-sm text-brand-100">
                            <svg class="w-5 h-5 text-brand-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Cancel anytime
                        </li>
                    </ul>
                    <a href="{{ route('register') }}"
                        class="block text-center bg-white text-brand-700 hover:bg-brand-50 font-semibold px-6 py-3 rounded-xl transition-colors">
                        Subscribe Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-10 px-6">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-white font-semibold">
                <svg class="w-5 h-5 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                EverRoots
            </div>
            <p class="text-sm">&copy; {{ date('Y') }} EverRoots. Serving rural southern Alabama.</p>
            <div class="flex gap-4 text-sm">
                <a href="{{ route('contact.show') }}" class="hover:text-white transition-colors">Contact</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Log In</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a>
            </div>
        </div>
    </footer>

</body>
</html>
