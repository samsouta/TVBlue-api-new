<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function UserProfileAll()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch users'
            ], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }
    }
}
