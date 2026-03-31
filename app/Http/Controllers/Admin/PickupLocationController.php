<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PickupLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PickupLocationController extends Controller
{
    public function index(): View
    {
        $locations = PickupLocation::orderBy('name')->orderBy('city')->get();
        return view('admin.pickup-locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'city'    => ['required', 'string', 'max:100'],
            'state'   => ['required', 'string', 'size:2'],
            'zip'     => ['required', 'string', 'max:10'],
        ]);

        PickupLocation::create($validated + ['active' => true]);

        return redirect()->route('admin.pickup-locations.index')
            ->with('success', 'Pickup location added.');
    }

    public function edit(PickupLocation $pickupLocation): View
    {
        return view('admin.pickup-locations.edit', compact('pickupLocation'));
    }

    public function update(Request $request, PickupLocation $pickupLocation): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'address' => ['required', 'string', 'max:255'],
            'city'    => ['required', 'string', 'max:100'],
            'state'   => ['required', 'string', 'size:2'],
            'zip'     => ['required', 'string', 'max:10'],
            'active'  => ['boolean'],
        ]);

        $pickupLocation->update($validated);

        return redirect()->route('admin.pickup-locations.index')
            ->with('success', 'Pickup location updated.');
    }

    public function destroy(PickupLocation $pickupLocation): RedirectResponse
    {
        $pickupLocation->delete();

        return redirect()->route('admin.pickup-locations.index')
            ->with('success', 'Pickup location removed.');
    }
}
