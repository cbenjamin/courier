<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load([
            'profile',
            'subscription',
            'orders' => fn ($q) => $q->latest()->limit(5),
        ]);

        return view('dashboard', compact('user'));
    }
}
