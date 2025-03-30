<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Services\CardService;
use App\Services\PremiumCodeService;

class PaymentController extends Controller
{
    protected $paypalService;
    protected $cardService;
    protected $premiumCodeService;

    public function __construct(
        PayPalService $paypalService,
        CardService $cardService,
        PremiumCodeService $premiumCodeService
    ) {
        $this->paypalService = $paypalService;
        $this->cardService = $cardService;
        $this->premiumCodeService = $premiumCodeService;
    }

    public function payWithPayPal(Request $request)
    {
        $result = $this->paypalService->processPayment($request->amount, $request->currency);
        return response()->json($result);
    }

    public function payWithCard(Request $request)
    {
        $result = $this->cardService->processPayment($request->amount, $request->currency);
        return response()->json($result);
    }

    public function redeemPremiumCode(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'code' => 'required|string|max:10',
        ]);

        $result = $this->premiumCodeService->redeemCode(
            $validatedData['code'],
            auth()->id()
        );

        return response()->json($result);
    }



    /**
     * 
     * code generater
     */

     public function generatePremiumCode(Request $request)
    {
        $code = $this->premiumCodeService->generateCode($request->amount, $request->currency);
        return response()->json(['message' => 'Premium code generated', 'code' => $code]);
    }
}
