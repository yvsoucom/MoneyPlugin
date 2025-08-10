<?php


namespace plugins\MoneyPlugin\src\Http\Controllers\PayMethod;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
 

 
use GuzzleHttp\Client;

class StripePaymentController extends Controller
{
    protected $client;
    protected $stripeSecret;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.stripe.com/v1/',
            'timeout'  => 10,
        ]);

        $this->stripeSecret = config('services.stripe.secret') ?? env('STRIPE_SECRET_KEY');
    }

    public function charge(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5', // in dollars
            'currency' => 'required|string|size:3',
            'source' => 'required|string', // token from frontend (e.g. Stripe Elements)
            'description' => 'nullable|string',
        ]);

        try {
            // Stripe expects amount in cents
            $amountInCents = intval($request->amount * 100);

            $response = $this->client->post('charges', [
                'auth' => [$this->stripeSecret, ''],
                'form_params' => [
                    'amount' => $amountInCents,
                    'currency' => strtolower($request->currency),
                    'source' => $request->source,
                    'description' => $request->description ?? 'Charge from Laravel',
                ],
            ]);

            $body = json_decode((string) $response->getBody(), true);

            if (isset($body['status']) && $body['status'] === 'succeeded') {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful',
                    'charge_id' => $body['id'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment failed',
                'response' => $body,
            ], 400);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Stripe API returned an error
            $errorBody = json_decode($e->getResponse()->getBody()->getContents(), true);
            return response()->json([
                'success' => false,
                'message' => $errorBody['error']['message'] ?? 'Payment error',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
