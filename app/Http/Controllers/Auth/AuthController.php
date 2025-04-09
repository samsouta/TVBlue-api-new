<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Login a user.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'session_token' => 'required',
            'device_info' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        Session::where('user_id', $user->id)->delete();
        $user->tokens()->delete();

        Session::create([
            'user_id' => $user->id,
            'session_token' => $request->session_token,
            'device_info' => $request->device_info,
            'ip_address' => $request->ip(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user_id' => $user->id,
            'token' => $token,
        ], 200);

        // Return error if authentication fails
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'session_token' => 'required',
            'device_info' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = 'Validation failed';

            if ($errors->has('email') && str_contains($errors->first('email'), 'taken')) {
                $message = 'This email is already registered';
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'errors' => $errors,
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'subscription_status' => 'free',
        ]);

        Session::create([
            'user_id' => $user->id,
            'session_token' => $request->session_token,
            'device_info' => $request->device_info,
            'ip_address' => $request->ip(),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'token' => $token,
            'user_id' => $user->id,
        ], 201);
    }


    /**
     * Logout a user.
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            Session::where('user_id', $user->id)->delete();

            // Revoke all tokens
            $user->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ])->cookie('device_token', '', -1); // Delete the cookie

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
