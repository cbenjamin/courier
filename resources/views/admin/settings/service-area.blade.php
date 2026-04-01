@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="max-w-2xl">

    @include('admin.settings._subnav')

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">Service Area (ZIP Codes)</h2>
            <p class="text-sm text-gray-500 mt-1">
                Customers can only place delivery orders to these ZIP codes.
                @if($serviceZips->isEmpty())
                    <span class="text-amber-600 font-medium">No ZIPs configured — deliveries are currently open to any address.</span>
                @endif
            </p>
        </div>

        @forelse($serviceZips as $serviceZip)
            <div class="flex items-center justify-between px-6 py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800 font-mono">{{ $serviceZip->zip }}</p>
                    @if($serviceZip->label)
                        <p class="text-xs text-gray-400">{{ $serviceZip->label }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.settings.service-zips.destroy', $serviceZip) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 transition-colors">Remove</button>
                </form>
            </div>
        @empty
            <p class="px-6 py-4 text-sm text-gray-400">No ZIP codes added yet.</p>
        @endforelse

        <div class="px-6 py-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            <p class="text-sm font-medium text-gray-700 mb-3">Add a ZIP code</p>
            <form method="POST" action="{{ route('admin.settings.service-zips.store') }}" class="flex gap-3 flex-wrap">
                @csrf
                <div>
                    <input type="text" name="zip" maxlength="10" placeholder="36301" required
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 w-28 @error('zip') border-red-400 @enderror">
                    @error('zip')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex-1 min-w-48">
                    <input type="text" name="label" placeholder="Label, e.g. Dothan, AL (optional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition-colors whitespace-nowrap">
                    Add ZIP
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
