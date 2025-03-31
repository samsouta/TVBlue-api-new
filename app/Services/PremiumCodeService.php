<?php

namespace App\Services;

use App\Models\PremiumCode;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class PremiumCodeService
{
    public function generateCode($amount, $currency)
    {
        $code = Str::upper(Str::random(10));

        PremiumCode::create([
            'code' => $code,
            'amount' => $amount,
            'currency' => $currency ?? 'USD',
        ]);

        return $code;
    }

    public function redeemCode($code, $userId)
    {

        $premiumCode = PremiumCode::where('code', $code)->first();

        if (!$premiumCode) {
            return ['status' => 'error', 'message' => 'Invalid code'];
        }

        if ($premiumCode->is_used) {
            return ['status' => 'error', 'message' => 'Code already used'];
        }

        // Mark code as used
        $premiumCode->is_used = true;
        $premiumCode->save();

        // Create subscription
        Subscription::create([
            'user_id' => $userId,
            'payment_method' => 'premium_code',
            'transaction_id' => $code,
            'status' => 'active',
            'amount' => $premiumCode->amount,
            'currency' => $premiumCode->currency,
            'is_lifetime' => true,
            'starts_at' => Carbon::now(),
            'expires_at' => null,
        ]);

        // Update user subscription status
        $user = User::find($userId);
        $user->subscription_status = 'premium';
        $user->save();

        return [
            'status' => 'success',
            'message' => 'Code redeemed successfully and subscription created'
        ];
    }
}
