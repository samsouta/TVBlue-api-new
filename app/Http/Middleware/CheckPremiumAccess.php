<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->subscription_status !== 'premium') {
            return response()->json([
                'message' => 'Premium subscription required',
                'subscription_required' => true
            ], 403);
        }

        return $next($request);
    }
}
