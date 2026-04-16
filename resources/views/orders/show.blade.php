@extends('layouts.app')

@section('title', 'Delivery #' . $order->id)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
.route-animated {
    stroke-dasharray: 8 14;
    animation: routeMarch 1.2s linear infinite;
}
@keyframes routeMarch {
    to { stroke-dashoffset: -22; }
}
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('orders.index') }}" class="text-sm text-gray-400 hover:text-gray-600">← Deliveries</a>
        <div class="flex items-center justify-between mt-2">
            <h1 class="text-2xl font-semibold text-gray-900">Delivery #{{ $order->id }}</h1>
            <span class="text-sm font-medium px-3 py-1 rounded-full
                @if($order->status === 'delivered') bg-green-100 text-green-700
                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-700
                @elseif($order->status === 'picked_up') bg-yellow-100 text-yellow-700
                @elseif($order->status === 'cancelled') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-600 @endif">
                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
            </span>
        </div>
    </div>

    <!-- Status Timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
        <h2 class="font-semibold text-gray-800 mb-4">Status Timeline</h2>
        @php
            $steps = ['pending', 'confirmed', 'picked_up', 'delivered'];
            $currentIndex = array_search($order->status, $steps);
        @endphp
        <div class="flex items-center gap-0">
            @foreach($steps as $i => $step)
                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold
                            {{ ($currentIndex !== false && $i <= $currentIndex) ? 'bg-brand-600 text-white' : 'bg-gray-200 text-gray-400' }}">
                            @if($currentIndex !== false && $i < $currentIndex)
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                {{ $i + 1 }}
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 whitespace-nowrap">{{ ucfirst(str_replace('_', ' ', $step)) }}</p>
                    </div>
                    @if($i < count($steps) - 1)
                        <div class="flex-1 h-0.5 mb-4 {{ ($currentIndex !== false && $i < $currentIndex) ? 'bg-brand-600' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Delivery Map -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-4">
        <div id="order-map" style="height: 260px; position: relative;">
            <div id="order-map-status" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#f9fafb;z-index:1000;">
                <span class="text-sm text-gray-400">Loading map…</span>
            </div>
        </div>
        <div class="px-4 py-2 flex items-center gap-5 text-xs text-gray-500 border-t border-gray-100">
            @if($order->pickupLocation)
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 shrink-0"></span>
                    Pickup: {{ $order->pickupLocation->name }}
                </span>
            @endif
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                Delivery
            </span>
        </div>
    </div>

    <!-- Pickup Details -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-4">
        <h2 class="font-semibold text-gray-800 mb-4">Pickup Details</h2>
        <dl class="space-y-3 text-sm">
            @if($order->pickupLocation)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Location</dt>
                    <dd class="text-gray-800 font-medium">{{ $order->pickupLocation->name }}</dd>
                    <dd class="text-gray-500 text-xs mt-0.5">{{ $order->pickupLocation->full_address }}</dd>
                </div>
            @endif
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Link</dt>
                <dd>
                    <a href="{{ $order->pickup_link }}" target="_blank" rel="noopener noreferrer"
                        class="text-brand-600 hover:underline break-all">
                        {{ $order->pickup_link }}
                    </a>
                </dd>
            </div>
            @if($order->pickup_time)
                <div>
                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Pickup Time</dt>
                    <dd class="text-gray-800">{{ $order->pickup_time->format('M j, Y g:i A') }}</dd>
                </div>
            @endif
        </dl>

        @if($order->notes)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Notes</p>
                <p class="text-sm text-gray-700">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Delivery & Payment -->
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Delivery Address</p>
            <p class="text-sm text-gray-800">{{ $order->delivery_address }}</p>
            <p class="text-sm text-gray-800">{{ $order->delivery_city }}, {{ $order->delivery_state }} {{ $order->delivery_zip }}</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Payment</p>
            @if($order->type === 'subscription')
                <p class="text-sm text-brand-700 font-medium">Subscription order</p>
                <p class="text-xs text-gray-400 mt-1">Included in your monthly plan</p>
            @elseif($order->payment)
                <p class="text-sm text-gray-800 font-semibold">{{ $order->amount_formatted }}</p>
                <p class="text-xs text-green-600 mt-1">Payment received</p>
                <p class="text-xs text-gray-400">{{ $order->payment->paid_at?->format('M j, Y') }}</p>
            @else
                <p class="text-sm text-gray-500">{{ $order->amount_formatted }}</p>
                <p class="text-xs text-yellow-600 mt-1">Payment pending</p>
            @endif
        </div>
    </div>

    @if($order->status === 'delivered')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            @if($order->tip_cents)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tip</p>
                        <p class="text-sm font-semibold text-green-700">{{ $order->tip_formatted }}</p>
                    </div>
                    <span class="text-sm text-green-600 font-medium">Thank you!</span>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Leave a tip for your courier</p>
                        <p class="text-xs text-gray-400 mt-0.5">Optional — 100% goes to your courier</p>
                    </div>
                    <a href="{{ route('orders.tip', $order) }}"
                        class="bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
                        Add a Tip
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(async function () {
    const statusEl = document.getElementById('order-map-status');
    function setStatus(msg) { if (statusEl) statusEl.innerHTML = '<span class="text-sm text-gray-400">' + msg + '</span>'; }
    function hideStatus() { if (statusEl) statusEl.style.display = 'none'; }

    if (typeof L === 'undefined') {
        setStatus('Map unavailable');
        return;
    }

    @php
        $pickupAddr = $order->pickupLocation
            ? "{$order->pickupLocation->address}, {$order->pickupLocation->city}, {$order->pickupLocation->state} {$order->pickupLocation->zip}"
            : null;
        $deliveryAddr = "{$order->delivery_address}, {$order->delivery_city}, {$order->delivery_state} {$order->delivery_zip}";
    @endphp

    const pickupAddress  = @json($pickupAddr);
    const deliveryAddress = @json($deliveryAddr);

    async function geocode(address) {
        try {
            const res = await fetch(
                'https://nominatim.openstreetmap.org/search?' +
                new URLSearchParams({ q: address, format: 'json', limit: 1, countrycodes: 'us' }),
                { headers: { 'Accept-Language': 'en' } }
            );
            const data = await res.json();
            if (data.length) return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
        } catch {}
        return null;
    }

    function bezierPoints(p1, p2, steps) {
        steps = steps || 60;
        const midLat = (p1[0] + p2[0]) / 2;
        const midLng = (p1[1] + p2[1]) / 2;
        const dLat = p2[0] - p1[0];
        const dLng = p2[1] - p1[1];
        const dist = Math.sqrt(dLat * dLat + dLng * dLng);
        if (dist === 0) return [p1, p2];
        const offset = dist * 0.3;
        const ctrlLat = midLat - dLng * (offset / dist);
        const ctrlLng = midLng + dLat * (offset / dist);
        const pts = [];
        for (let i = 0; i <= steps; i++) {
            const t = i / steps, mt = 1 - t;
            pts.push([
                mt * mt * p1[0] + 2 * mt * t * ctrlLat + t * t * p2[0],
                mt * mt * p1[1] + 2 * mt * t * ctrlLng + t * t * p2[1],
            ]);
        }
        return pts;
    }

    function dotIcon(color) {
        return L.divIcon({
            className: '',
            html: `<div style="width:14px;height:14px;border-radius:50%;background:${color};border:2.5px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.35)"></div>`,
            iconSize: [14, 14],
            iconAnchor: [7, 7],
            popupAnchor: [0, -10],
        });
    }

    const [pickup, delivery] = await Promise.all([
        pickupAddress ? geocode(pickupAddress) : Promise.resolve(null),
        geocode(deliveryAddress),
    ]);

    if (!delivery) {
        setStatus('Map unavailable');
        return;
    }

    hideStatus();

    const map = L.map('order-map', { zoomControl: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 18,
    }).addTo(map);

    const points = [];

    if (pickup) {
        L.marker(pickup, { icon: dotIcon('#22c55e') })
            .addTo(map)
            .bindPopup('<strong>Pickup</strong><br>{{ addslashes($order->pickupLocation?->name ?? '') }}');
        points.push(pickup);
    }

    L.marker(delivery, { icon: dotIcon('#3b82f6') })
        .addTo(map)
        .bindPopup('<strong>Delivery</strong><br>{{ addslashes($deliveryAddr) }}');
    points.push(delivery);

    if (pickup) {
        L.polyline(bezierPoints(pickup, delivery), {
            color: '#6366f1',
            weight: 2.5,
            opacity: 0.85,
            className: 'route-animated',
        }).addTo(map);
    }

    if (points.length === 1) {
        map.setView(points[0], 14);
    } else {
        map.fitBounds(points, { padding: [36, 36] });
    }
})();
</script>
@endpush
