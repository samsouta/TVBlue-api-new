<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Auth\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:paypal,card,code',
            'plan_type' => 'required|in:monthly,yearly ,lifetime',
        ]);

        if ($request->payment_method === 'code') {
            $request->validate([
                'subscription_code' => 'required|string'
            ]);

            // Verify the subscription code
            // You'll need to implement the code verification logic
        }

        // Handle payment processing based on payment_method
        // Implement PayPal and card payment processing

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => auth()->id(),
            'plan_type' => $request->plan_type,
            'payment_method' => $request->payment_method,
            'amount' => $request->plan_type === 'monthly' ? 9.99 : 99.99,
            'starts_at' => now(),
            'expires_at' => $request->plan_type === 'monthly' ?
                now()->addMonth() : now()->addYear(),
            'status' => 'active',
            'subscription_code' => $request->subscription_code ?? null
        ]);

        // Update user subscription status
        if (auth()->check()) {
            auth()->user()->update([
                'subscription_status' => 'premium',
                'subscription_expires_at' => $subscription->expires_at,
                'subscription_type' => $request->payment_method
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'message' => 'Subscription successful',
            'subscription' => $subscription
        ]);
    }
}
