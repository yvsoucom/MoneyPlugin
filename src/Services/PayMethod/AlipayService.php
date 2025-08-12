<?php

namespace plugins\MoneyPlugin\src\Services\PayMethod;

use Alipay\EasySDK\Kernel\Factory;


class AlipayService
{
    protected $alipay;

    public function __construct()
    {
        Factory::setOptions([
            'app_id' => config('alipay.app_id'),
            'merchant_private_key' => config('alipay.merchant_private_key'),
            'alipay_public_key' => config('alipay.alipay_public_key'),
            'gateway_url' => config('alipay.gateway_url'),
            'notify_url' => config('alipay.notify_url'),
            'return_url' => config('alipay.return_url'),
        ]);
        $this->alipay = Factory::payment()->gateway('web');
    }

    public function pay($orderNo, $subject, $amount)
    {
        // Returns an Alipay redirect URL (string)
        $response = $this->alipay->pay($orderNo, $subject, $amount, config('alipay.notify_url'));
        return $response->getBody();
    }

    public function verify(array $params)
    {
        return $this->alipay->verify($params);
    }

    protected function process($tradeNo, $amount, $currency, $metadata)
    {
        // Call AliPay SDK or REST here
        return ['gateway' => 'AliPay', 'status' => 'pending', 'trade_no' => $tradeNo];
    }

}
