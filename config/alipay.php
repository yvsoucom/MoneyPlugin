<?php
return [
    'app_id' => env('ALIPAY_APP_ID'),
    'merchant_private_key' => env('ALIPAY_MERCHANT_PRIVATE_KEY'),
    'alipay_public_key' => env('ALIPAY_PUBLIC_KEY'),
    'notify_url' => env('ALIPAY_NOTIFY_URL'),
    'return_url' => env('ALIPAY_RETURN_URL'),
    'gateway_url' => 'https://openapi.alipay.com/gateway.do',
];
