<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Services\CardService;
use App\Services\PremiumCodeService;
use Carbon\Carbon;

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

    /**
     * 
     * paypal payment
     * @service PayPalService                   
     */
    public function PayPalCreateOrder(Request $request)
    {
        try {
            $amount = $request->input('amount');
            $order = $this->paypalService->createOrder($amount);

            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function PayPalCapturePayment(Request $request)
    {
        try {
            $orderId = $request->input('order_id');
            $result = $this->paypalService->capturePayment($orderId);

            

            if ($result['status'] === 'COMPLETED') {
                // Create subscription record
                Subscription::create([
                    'user_id' => auth()->id(),
                    'payment_method' => 'paypal',
                    'transaction_id' => $result['id'],
                    'status' => 'active',
                    'amount' => $result['purchase_units'][0]['payments']['captures'][0]['amount']['value'],
                    'currency' => $result['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
                    'is_lifetime' => true,
                    'starts_at' => Carbon::now(),
                    'expires_at' => null,
                ]);
                // Update user subscription status
                $user = User::find(auth()->id());
                $user->subscription_status = 'premium';
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment processed and subscription created successfully',
                    'payment_details' => $result
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Payment not completed'
            ], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    /**
     * 
     * card payment
     * @service CardService
     */

    public function payWithCard(Request $request)
    {
        $result = $this->cardService->processPayment($request->amount, $request->currency);
        return response()->json($result);
    }


    /**
     * 
     * premium code redeem
     */

    public function redeemPremiumCode(Request $request)
    {
        try {

            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please login first to redeem premium code'
                ], 401);
            }

            $user = auth()->user();
            if ($user->subscription_status === 'premium') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are already a premium user'
                ], 400);
            }
            // Validate the request data
            $validatedData = $request->validate([
                'code' => 'required|string|max:10',
            ]);

            $result = $this->premiumCodeService->redeemCode(
                $validatedData['code'],
                auth()->id()
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
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
