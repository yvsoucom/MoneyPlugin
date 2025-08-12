<?php


namespace plugins\MoneyPlugin\src\Services\PayMethod;
use Illuminate\Http\Request;
class WeChatService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('moneyplugin.wechat');
    }

    public function createOrder($orderNo, $amount, $body)
    {
        // Call WeChat Pay SDK here with $this->config
    }

    public function handleNotify(array $data)
    {
        // Verify and process WeChat Pay notify callback
    }
    protected function process($tradeNo, $amount, $currency, $metadata)
    {
        // Call WeChat Pay SDK or REST here
        return ['gateway' => 'WeChat Pay', 'status' => 'pending', 'trade_no' => $tradeNo];
    }
    protected function handleWebhook(Request $request)
    {
        if ($request->input('result_code') === 'SUCCESS') {
            $tradeNo = $request->input('out_trade_no');
            $amount = $request->input('total_fee') / 100;

            $gateway = 'wx';
            return (compact('tradeNo', 'gateway', 'amount'));

        }
        return response()->json(['status' => 'no']);
    }
}
