<?php

namespace plugins\MoneyPlugin\src\Http\Controllers\PayMethod;

use Illuminate\Http\Request;
use plugins\MoneyPlugin\src\Services\PayMethod\WeChatPayService;
use App\Http\Controllers\Controller;
 
 

class WeChatPayController extends Controller
{
    protected $wechat;

    public function __construct(WeChatPayService $wechat)
    {
        $this->wechat = $wechat;
    }

    public function pay(Request $request)
    {
        $orderNo = uniqid('order_');
        $amount = $request->input('amount', 1.00);
        $subject = "Order Payment #{$orderNo}";

        return $this->wechat->createOrder($orderNo, $amount, $subject);
    }

    public function notify(Request $request)
    {
        if ($this->wechat->handleNotify($request->all())) {
            return 'SUCCESS';
        }
        return 'FAIL';
    }

    public function return(Request $request)
    {
        return view('moneyplugin.payment-success', [
            'order_id' => $request->input('out_trade_no'),
            'status'   => 'Paid'
        ]);
    }
}
