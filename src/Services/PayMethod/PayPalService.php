<?php

namespace plugins\MoneyPlugin\src\Services\PayMethod;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class PayPalService
{
    protected $client;
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('paypal.mode') === 'live'
            ? 'https://api.paypal.com'
            : 'https://api.sandbox.paypal.com';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    // Get OAuth access token
    public function getAccessToken()
    {
        $response = $this->client->post('/v1/oauth2/token', [
            'auth' => [
                config('paypal.client_id'),
                config('paypal.client_secret'),
            ],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['access_token'] ?? null;
    }

    // Create an order
    public function createOrder($amount, $currency = 'USD', $returnUrl, $cancelUrl)
    {
        $accessToken = $this->getAccessToken();

        $response = $this->client->post('/v2/checkout/orders', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => number_format($amount, 2, '.', ''),
                        ],
                    ],
                ],
                'application_context' => [
                    'return_url' => $returnUrl,
                    'cancel_url' => $cancelUrl,
                ],
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    // Capture an order after user approval
    public function captureOrder($orderId)
    {
        $accessToken = $this->getAccessToken();

        $response = $this->client->post("/v2/checkout/orders/$orderId/capture", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /* ---------- PayPal ---------- */
    protected function process($tradeNo, $amount, $currency, $metadata)
    {
        return $this->createOrder(
            $tradeNo,
            $amount,
            strtoupper($currency),
            array_merge($metadata, ['trade_no' => $tradeNo])
        );
        
    }

     protected function handleWebhook(Request $request)
    {
        $eventType = $request->input('event_type');

        if ($eventType === 'CHECKOUT.ORDER.APPROVED') {
            $tradeNo = $request->input('resource.reference_id');
            $amount = $request->input('resource.purchase_units.0.amount.value');
            $gateway = 'paypal';
            return(compact('tradeNo', 'gateway', 'amount'));
        }
        return response()->json(['status' => 'no']);
    }


}
