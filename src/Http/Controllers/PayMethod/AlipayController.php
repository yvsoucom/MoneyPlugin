<?php

namespace plugins\MoneyPlugin\src\Http\Controllers\PayMethod;

use Illuminate\Http\Request;
use plugins\MoneyPlugin\src\Services\PayMethod\AlipayService;
use App\Http\Controllers\Controller;
class AlipayController extends Controller
{
    protected $alipay;

    public function __construct(AlipayService $alipay)
    {
        $this->alipay = $alipay;
    }

    public function pay(Request $request)
    {
        // You can generate or get order details here
        $orderNo = 'order_' . time();
        $subject = 'Test Payment';
        $amount = '0.01'; // example amount

        $redirectUrl = $this->alipay->pay($orderNo, $subject, $amount);

        // Redirect to Alipay payment page
        return redirect($redirectUrl);
    }

    public function notify(Request $request)
    {
        $params = $request->all();

        if ($this->alipay->verify($params)) {
            // Payment success logic here
            // For example, update your order status, log payment, etc.

            return response('success');
        }

        return response('fail');
    }
}
