<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::send([], [], function ($mail) use ($validated) {
            $mail->to(Setting::get('contact_email', config('mail.from.address')))
                ->replyTo($validated['email'], $validated['name'])
                ->subject('Contact Form: ' . $validated['name'])
                ->html(
                    '<p><strong>From:</strong> ' . e($validated['name']) . ' &lt;' . e($validated['email']) . '&gt;</p>' .
                    '<p><strong>Message:</strong></p>' .
                    '<p>' . nl2br(e($validated['message'])) . '</p>'
                );
        });

        return back()->with('success', "Thanks, {$validated['name']}! We'll be in touch shortly.");
    }
}
