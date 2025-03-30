<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class PayPalService
{
    public function getAccessToken()
    {
        $response = Http::withBasicAuth(
            config('paypal.client_id'),
            config('paypal.secret')
        )->asForm()->post(config('paypal.api_url') . "/v1/oauth2/token", [
            'grant_type' => 'client_credentials',
        ]);

        return $response->json()['access_token'] ?? null;
    }

    public function createOrder()
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)->post(config('paypal.api_url') . "/v2/checkout/orders", [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => '10.00',
                    ],
                ],
            ],
        ]);

        return response()->json($response->json());
    }

    public function captureOrder($orderID)
    {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)->post(
            config('paypal.api_url') . "/v2/checkout/orders/{$orderID}/capture"
        );

        return $response->json();
    }
}
