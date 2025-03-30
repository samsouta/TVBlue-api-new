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

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'subscription_status' => $user->subscription_status,
                'is_premium' => $user->subscription_status === 'premium',
                'token' => $token,
            ]);
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
            'name' => 'required|string|max:255', 
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed', 
        ]);

        // If validation fails, return a response with errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = 'Validation failed';
            
            // Check specifically for email uniqueness error
            if ($errors->has('email') && str_contains($errors->first('email'), 'taken')) {
                $message = 'This email is already registered';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ], 422);
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'subscription_status' => 'free', // Default status
        ]);

        // Create a token for the new user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return success response with status, message, token, and user details
        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'token' => $token,
            'user' => $user,
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
