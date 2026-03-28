<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->load(['profile', 'subscription']);

        $recentOrders = $user->orders()->latest()->limit(5)->get();
        $totalOrders  = $user->orders()->count();

        return view('dashboard', compact('user', 'recentOrders', 'totalOrders'));
    }
}
