<?php

namespace plugins\MoneyPlugin\src\Http\Controllers\PayMethod;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
 
use plugins\MoneyPlugin\src\Services\PayMethod\PayPalService;


class PayPalController extends Controller
{
    protected $paypal;

    public function __construct(PayPalService $paypal)
    {
        $this->paypal = $paypal;
    }

    // Step 1: Create order and redirect user to PayPal approval
    public function createPayment()
    {
        $order = $this->paypal->createOrder(
            10.00,
            'USD',
            route('paypal.success'),
            route('paypal.cancel')
        );

        foreach ($order['links'] as $link) {
            if ($link['rel'] === 'approve') {
                return redirect($link['href']);
            }
        }

        return redirect()->back()->with('error', 'Unable to create PayPal order.');
    }

    // Step 2: Handle successful payment (after user approves on PayPal)
    public function success(Request $request)
    {
        $orderId = $request->query('token');

        $result = $this->paypal->captureOrder($orderId);

        if (isset($result['status']) && $result['status'] === 'COMPLETED') {
            // Payment succeeded
            return view('payment.success', ['result' => $result]);
        }

        return redirect()->route('payment.failed');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }
}
