<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Session;
use Symfony\Component\HttpFoundation\Response;

class VerifySessionToken
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access. Token has expired or is invalid.',
                'code' => 'TOKEN_EXPIRED'
            ], 401);
        }

        $sessionToken = $request->header('X-Session-Token');
        
        if (!$sessionToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Session token is missing',
                'code' => 'SESSION_TOKEN_MISSING'
            ], 401);
        }

        $session = Session::where('user_id', $request->user()->id)
                         ->where('session_token', $sessionToken)
                         ->first();

        if (!$session) {
            // Revoke the current token as session is invalid
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Session has expired. Please login again.',
                'code' => 'SESSION_EXPIRED'
            ], 401);
        }

        return $next($request);
    }
}
