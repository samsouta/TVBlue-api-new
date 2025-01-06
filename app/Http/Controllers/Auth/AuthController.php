<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Get credentials
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate user
        if (Auth::attempt($credentials)) {
            // Get the authenticated user
            $user = Auth::user();
            // Create a token for the user
            $token = $user->createToken('bluetv')->plainTextToken;

            // Return success response with status, message, and user details
            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'email' => $user->email,
                    'name' => $user->name,
                ]
            ], 200);
        }

        // Return error if authentication fails
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Register Method
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', // Change from username to name
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', // Ensure password_confirmation is included
        ]);

        // If validation fails, return a response with errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name, // Store name
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash the password
        ]);

        // Create a token for the new user
        $token = $user->createToken('bluetv')->plainTextToken;

        // Return success response with status, message, token, and user details
        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
            ]
        ], 201);
    }


    // Logout Method
    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete(); // Revoke all tokens
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }
}
