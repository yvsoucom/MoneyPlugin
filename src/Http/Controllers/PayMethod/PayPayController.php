<?php

namespace plugins\MoneyPlugin\src\Http\Controllers\PayMethod;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Services\PayPayService;

 
use GuzzleHttp\Client;

class PayPayController extends Controller
{
    protected $client;
    protected $apiKey;
    protected $apiSecret;
    protected $merchantId;
    protected $apiBase;

    public function __construct()
    {
        $this->apiKey = config('paypay.api_key');
        $this->apiSecret = config('paypay.api_secret');
        $this->merchantId = config('paypay.merchant_id');

        // Use sandbox or production API base URL
        $this->apiBase = 'https://sandbox-api.paypay.ne.jp'; 

        $this->client = new Client([
            'base_uri' => $this->apiBase,
            'timeout' => 10.0,
        ]);
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'orderDescription' => 'required|string|max:255',
        ]);

        $merchantPaymentId = uniqid('paypay_');

        $data = [
            "merchantPaymentId" => $merchantPaymentId,
            "amount" => [
                "amount" => $request->input('amount'),
                "currency" => strtoupper($request->input('currency')),
            ],
            "codeType" => "ORDER_QR",   // For example
            "orderDescription" => $request->input('orderDescription'),
            "requestedAt" => time(),
        ];

        // Build signature headers (you need to implement this method)
        $headers = $this->buildSignatureHeaders('POST', '/v2/payments', json_encode($data));

        try {
            $response = $this->client->post('/v2/payments', [
                'headers' => $headers,
                'json' => $data,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Example signature headers builder (simplified)
    protected function buildSignatureHeaders($method, $path, $body = '')
    {
        $timestamp = time() * 1000;
        $nonce = uniqid();

        // Here you build the signature string per PayPay's spec:
        // signature = HMAC_SHA256(apiSecret, method + path + body + timestamp + nonce)

        $message = $method . $path . $body . $timestamp . $nonce;
        $signature = base64_encode(hash_hmac('sha256', $message, $this->apiSecret, true));

        return [
            'Content-Type' => 'application/json',
            'X-API-KEY' => $this->apiKey,
            'X-REQUEST-TIME' => $timestamp,
            'X-REQUEST-NONCE' => $nonce,
            'X-REQUEST-SIGNATURE' => $signature,
        ];
    }
}
