<?php
namespace plugins\MoneyPlugin\src\Services\PayMethod;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
 
class PayPayService
{
    protected $client;
    protected $apiKey;
    protected $apiSecret;
    protected $merchantId;
    protected $apiBase;

    public function __construct()
    {
        $this->apiKey = 'YOUR_API_KEY';
        $this->apiSecret = 'YOUR_API_SECRET';
        $this->merchantId = 'YOUR_MERCHANT_ID';

        // PayPay Sandbox or Production base URL
        $this->apiBase = 'https://api.paypay.ne.jp'; // or sandbox URL if testing

        $this->client = new Client([
            'base_uri' => $this->apiBase,
            'timeout'  => 10.0,
        ]);
    }

    public function createPayment(array $data)
    {
        // Construct headers with authentication (example)
        // You need to implement your own signature/auth scheme as per PayPay API spec

        $headers = [
            'Content-Type' => 'application/json',
            'X-API-KEY' => $this->apiKey,
            'X-Merchant-Id' => $this->merchantId,
            // Add Authorization or Signature headers as required
        ];

        // The body must be JSON
        $body = json_encode($data);

        try {
            $response = $this->client->post('/v2/payments', [
                'headers' => $headers,
                'body' => $body,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return $result;

        } catch (\Exception $e) {
            // Handle errors
            return ['error' => $e->getMessage()];
        }
    }

      /* ---------- PayPay (Japan) ---------- */
    protected function process($tradeNo, $amount, $currency, $metadata)
    {
        $client = new Client([
            'base_uri' => 'https://api.paypay.ne.jp/v2/',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('services.paypay.token'),
            ]
        ]);

        $payload = [
            'merchantPaymentId' => $tradeNo,
            'amount' => [
                'amount' => $amount,
                'currency' => strtoupper($currency)
            ],
            'codeType' => 'ORDER_QR',
            'orderDescription' => 'Order #' . $tradeNo,
        ];

        try {
            $res = $client->post('payments', ['json' => $payload]);
            return [
                'gateway' => 'PayPay',
                'status' => 'pending',
                'data' => json_decode($res->getBody(), true)
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    protected function handlePayPayWebhook(Request $request)
    {
        if ($request->input('status') === 'COMPLETED') {
            $tradeNo = $request->input('merchantPaymentId');
            $amount = $request->input('amount.amount');
            $gateway = 'paypay';
            return(compact('tradeNo', 'gateway', 'amount'));
        }
        return response()->json(['status' => 'no']);
    }
}
