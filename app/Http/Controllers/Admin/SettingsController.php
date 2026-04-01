<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlackoutDate;
use App\Models\ServiceZip;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = [
            'adhoc_price_cents'        => Setting::get('adhoc_price_cents', 2500),
            'subscription_price_cents' => Setting::get('subscription_price_cents', 7900),
            'contact_email'            => Setting::get('contact_email', config('mail.from.address')),
            'courier_phone'            => Setting::get('courier_phone', ''),
        ];

        $blackouts = BlackoutDate::orderBy('date')->get();
        $serviceZips = ServiceZip::orderBy('zip')->get();

        return view('admin.settings.index', compact('settings', 'blackouts', 'serviceZips'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'adhoc_price'        => ['required', 'numeric', 'min:1'],
            'subscription_price' => ['required', 'numeric', 'min:1'],
            'contact_email'      => ['required', 'email', 'max:255'],
        ]);

        Setting::set('adhoc_price_cents', (int) round($validated['adhoc_price'] * 100));
        Setting::set('subscription_price_cents', (int) round($validated['subscription_price'] * 100));
        Setting::set('contact_email', $validated['contact_email']);

        return back()->with('success', 'Settings saved.');
    }

    public function updateCourierSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'courier_phone' => ['nullable', 'string', 'max:20'],
        ]);

        Setting::set('courier_phone', $validated['courier_phone'] ?? '');

        return back()->with('success', 'Courier settings saved.');
    }

    public function storeServiceZip(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zip'   => ['required', 'string', 'max:10', 'unique:service_zips,zip'],
            'label' => ['nullable', 'string', 'max:100'],
        ]);

        ServiceZip::create($validated);

        return back()->with('success', 'ZIP code added to service area.');
    }

    public function destroyServiceZip(ServiceZip $serviceZip): RedirectResponse
    {
        $serviceZip->delete();

        return back()->with('success', 'ZIP code removed from service area.');
    }

    public function storeBlackout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'   => ['required', 'date', 'after_or_equal:today', 'unique:blackout_dates,date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        BlackoutDate::create($validated);

        return back()->with('success', 'Blackout date added.');
    }

    public function destroyBlackout(BlackoutDate $blackout): RedirectResponse
    {
        $blackout->delete();

        return back()->with('success', 'Blackout date removed.');
    }
}
