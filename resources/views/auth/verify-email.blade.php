@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="max-w-md mx-auto text-center">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10">
        <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-semibold text-gray-900 mb-2">Check your inbox</h1>
        <p class="text-gray-500 text-sm mb-6">
            We sent a verification link to <strong>{{ auth()->user()->email }}</strong>.
            Please click the link to activate your account.
        </p>

        @if(session('status') === 'verification-link-sent')
            <div class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">
                A new verification link has been sent to your email.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit"
                class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                Resend verification email
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm text-gray-400 hover:text-gray-600">Log out</button>
    </form>
</div>
@endsection
