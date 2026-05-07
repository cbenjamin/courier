<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Turnstile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $rules = [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
        if (config('services.turnstile.secret_key')) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile()];
        }
        $credentials = $request->validate($rules);
        unset($credentials['cf-turnstile-response']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        if (config('services.turnstile.secret_key')) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile()];
        }
        $validated = $request->validate($rules);
        unset($validated['cf-turnstile-response']);

        $user = User::create($validated);
        $user->profile()->create([]);
        $user->sendEmailVerificationNotification();

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
