<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $adhocPrice = Setting::get('adhoc_price_cents', 2500) / 100;
        $subscriptionPrice = Setting::get('subscription_price_cents', 7900) / 100;

        return view('welcome', compact('adhocPrice', 'subscriptionPrice'));
    }
}
