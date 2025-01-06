<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Google_Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GoogleController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'token' => 'required'
        ]);

        // Verify token with Google API
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]); // Make sure GOOGLE_CLIENT_ID is set in .env file
        $payload = $client->verifyIdToken($validated['token']);

        if (!$payload) {
            return response()->json(['error' => 'Invalid Token'], 401);
        }

        // Check if user already exists, or create a new one
        $user = User::updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'],
                'google_id' => $payload['sub'],
                'avatar' => $payload['picture'] ?? null,
                // 'password' => Hash::make(str_random(24)) 
            ]
        );

        // Generate API token for the user
        $token = $user->createToken('google-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
