<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class PayPalService
{
    protected $client;
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $mode = Config::get('paypal.mode');
        $this->clientId = Config::get("paypal.{$mode}.client_id");
        $this->clientSecret = Config::get("paypal.{$mode}.client_secret");
        $this->baseUrl = $mode === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function getAccessToken()
    {
        $response = $this->client->post("{$this->baseUrl}/v1/oauth2/token", [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'auth' => [$this->clientId, $this->clientSecret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    public function createOrder($amount)
    {
        $accessToken = $this->getAccessToken();

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'USD',
                            'value' => $amount
                        ]
                    ],
                ],
                'application_context' => [
                    'return_url' => 'https://bluetv.xyz/payment', // Redirect after payment
                    'cancel_url' => 'https://bluetv.xyz/home',  // Redirect if canceled
                ]
            ]
        ]);

        return json_decode($response->getBody(), true);
    }

    public function capturePayment($orderId)
    {
        $accessToken = $this->getAccessToken();

        $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture", [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ]
        ]);

        return json_decode($response->getBody(), true);
    }
}
