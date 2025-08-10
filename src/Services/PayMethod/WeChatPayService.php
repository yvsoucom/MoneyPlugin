<?php
 

namespace plugins\MoneyPlugin\src\Services\PayMethod;

class WeChatPayService
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
}
