<?php
/**
 * SPDX-FileCopyrightText: (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 * SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
 * SPDX-FileContributor: Lican Huang
 * @created 2025-08-12
 *
 * SPDX-License-Identifier: GPL-3.0-or-later
 * License: Dual Licensed – GPLv3 or Commercial
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * As an alternative to GPLv3, commercial licensing is available for organizations
 * or individuals requiring proprietary usage, private modifications, or support.
 *
 * Contact: yvsoucom@gmail.com
 * GPL License: https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace plugins\MoneyPlugin\src\Services\PayMethod;


use Illuminate\Http\Request;
use plugins\MoneyPlugin\src\Services\PayMethod\WeChatService;
use plugins\MoneyPlugin\src\Services\PayMethod\StripeService;
use plugins\MoneyPlugin\src\Services\PayMethod\AliPayService;
use plugins\MoneyPlugin\src\Services\PayMethod\PayPalService;
use plugins\MoneyPlugin\src\Services\PayMethod\PayPayService;
use plugins\MoneyPlugin\src\Services\Accounting\AccountingService;
class PaymentCoordinator
{
    protected $services;

    public function __construct(
        AliPayService $ali,
        WeChatService $wx,
        StripeService $stripe,
        PayPalService $paypal,
        PayPayService $paypay
    ) {
        $this->services = compact('ali', 'wx', 'stripe', 'paypal', 'paypay');
    }

    public function processPayment($gateway, array $data)
    {
        if (!isset($this->services[$gateway])) {
            throw new \Exception("Unknown payment gateway: $gateway");
        }
        return $this->services[$gateway]->process($data);
    }

    public function handleWebhook(Request $request)
    {
        $gateway = $this->detectProvider($request);
        if (!$gateway) {
            throw new \Exception("Unable to detect payment provider");
        }

        $result = $this->services[$gateway]->handleWebhook($request);

        if ($result) {
            $this->accountingComplete($result['trade_no'], $result['gateway'], $result['amount']);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function detectProvider(Request $request)
    {
        if ($request->hasHeader('Stripe-Signature'))
            return 'stripe';
        if ($request->hasHeader('Paypal-Transmission-Id') || $request->input('event_type'))
            return 'paypal';
        if ($request->input('merchantPaymentId'))
            return 'paypay';
        if ($request->input('trade_status'))
            return 'ali';
        if ($request->input('transaction_id') && $request->input('appid'))
            return 'wx';
        return null;
    }

    protected function accountingComplete($tradeNo, $gateway, $amount)
    {
        app(AccountingService::class)
            ->semRecharge($tradeNo, $gateway, $amount);

        app(AccountingService::class)
            ->directMoneyPay($tradeNo, $gateway, $amount);
        \Log::info("Payment complete", compact('tradeNo', 'gateway', 'amount'));
    }
}
