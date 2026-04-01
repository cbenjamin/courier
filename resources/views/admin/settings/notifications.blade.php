@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">

    @include('admin.settings._subnav')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="font-semibold text-gray-900 mb-1">Courier Notifications</h2>
        <p class="text-sm text-gray-500 mb-5">New order SMS alerts will be sent to this number.</p>

        <form method="POST" action="{{ route('admin.settings.update-courier') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Courier phone number</label>
                <input type="tel" name="courier_phone"
                    value="{{ $settings['courier_phone'] }}"
                    maxlength="20"
                    placeholder="+12055550100"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('courier_phone') border-red-400 @enderror">
                <p class="mt-1 text-xs text-gray-400">US numbers can be entered as 10 digits (e.g. 2055550100) or E.164 format. Leave blank to disable SMS alerts.</p>
                @error('courier_phone')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-5 py-2 rounded-lg text-sm transition-colors">
                Save
            </button>
        </form>
    </div>

</div>
@endsection
