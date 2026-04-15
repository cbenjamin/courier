<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone'            => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:255'],
            'city'             => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'size:2'],
            'zip'              => ['nullable', 'string', 'max:10'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'expo_push_token'  => ['nullable', 'string', 'max:200'],
        ]);

        $user = $request->user();

        if ($user->profile) {
            $user->profile->update($validated);
        } else {
            $user->profile()->create(array_merge($validated, ['user_id' => $user->id]));
        }

        return response()->json($user->fresh('profile')->profile);
    }
}
