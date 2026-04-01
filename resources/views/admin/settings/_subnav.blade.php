<nav class="flex gap-1 border-b border-gray-200 mb-6 overflow-x-auto">
    @php
        $tabs = [
            ['route' => 'admin.settings.general',      'label' => 'General'],
            ['route' => 'admin.settings.notifications', 'label' => 'Notifications'],
            ['route' => 'admin.settings.service-area',  'label' => 'Service Area'],
            ['route' => 'admin.settings.blackouts',     'label' => 'Blackout Dates'],
        ];
    @endphp

    @foreach($tabs as $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition-colors
               {{ request()->routeIs($tab['route'])
                   ? 'border-brand-600 text-brand-700'
                   : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</nav>
