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


use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

use plugins\MoneyPlugin\src\Services\PayMethod\PayPalService;
use GuzzleHttp\Client; // For PayPay REST
use Exception;

class PaymentService
{
    public function processPayment($tradeNo, $payway, $amount, $currency = 'usd', $metadata = [])
    {
        switch (strtolower($payway)) {
            case 'ali':
                return $this->handleAliPay($tradeNo, $amount, $currency, $metadata);

            case 'wx':
                return $this->handleWeChatPay($tradeNo, $amount, $currency, $metadata);

            case 'stripe':
                return $this->handleStripe($tradeNo, $amount, $currency, $metadata);

            case 'paypal':
                return $this->handlePayPal($tradeNo, $amount, $currency, $metadata);

            case 'paypay':
                return $this->handlePayPay($tradeNo, $amount, $currency, $metadata);

            default:
                throw new Exception("Unsupported payment gateway: {$payway}");
        }
    }

    /* ---------- AliPay ---------- */
    protected function handleAliPay($tradeNo, $amount, $currency, $metadata)
    {
        // Call AliPay SDK or REST here
        return ['gateway' => 'AliPay', 'status' => 'pending', 'trade_no' => $tradeNo];
    }

    /* ---------- WeChat Pay ---------- */
    protected function handleWeChatPay($tradeNo, $amount, $currency, $metadata)
    {
        // Call WeChat Pay SDK or REST here
        return ['gateway' => 'WeChat Pay', 'status' => 'pending', 'trade_no' => $tradeNo];
    }

    /* ---------- Stripe ---------- */
    protected function handleStripe($tradeNo, $amount, $currency, $metadata)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => intval($amount * 100),
                'currency' => strtolower($currency),
                'payment_method_types' => ['card'],
                'metadata' => array_merge($metadata, ['trade_no' => $tradeNo]),
            ]);

            return [
                'gateway' => 'Stripe',
                'status' => 'pending',
                'client_secret' => $paymentIntent->client_secret,
                'id' => $paymentIntent->id,
            ];
        } catch (ApiErrorException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ---------- PayPal ---------- */
    protected function handlePayPal($tradeNo, $amount, $currency, $metadata)
    {
        $client = app(\App\Services\PayPalClient::class)->getClient();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $tradeNo,
                    'amount' => [
                        'currency_code' => strtoupper($currency),
                        'value' => number_format($amount, 2, '.', '')
                    ]
                ]
            ]
        ];

        try {
            $response = $client->execute($request);
            return [
                'gateway' => 'PayPal',
                'status' => 'pending',
                'approve_link' => collect($response->result->links)->firstWhere('rel', 'approve')->href,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ---------- PayPay (Japan) ---------- */
    protected function handlePayPay($tradeNo, $amount, $currency, $metadata)
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
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // Unified webhook handler
    public function handleWebhook(Request $request)
    {
        $provider = $this->detectProvider($request);

        switch ($provider) {
            case 'stripe':
                return $this->handleStripeWebhook($request);

            case 'paypal':
                return $this->handlePayPalWebhook($request);

            case 'paypay':
                return $this->handlePayPayWebhook($request);

            case 'ali':
                return $this->handleAliPayWebhook($request);

            case 'wx':
                return $this->handleWeChatWebhook($request);

            default:
                throw new Exception('Unknown webhook provider');
        }
    }

    protected function detectProvider(Request $request)
    {
        // Stripe has `Stripe-Signature` header
        if ($request->hasHeader('Stripe-Signature'))
            return 'stripe';

        // PayPal has `Paypal-Transmission-Id` header or `event_type` JSON
        if ($request->hasHeader('Paypal-Transmission-Id') || $request->input('event_type'))
            return 'paypal';

        // PayPay has `x-api-key` or JSON with merchantPaymentId
        if ($request->input('merchantPaymentId'))
            return 'paypay';

        // AliPay / WeChat detection can be based on known POST params
        if ($request->input('trade_status') || $request->input('sign_type'))
            return 'ali';
        if ($request->input('transaction_id') && $request->input('appid'))
            return 'wx';

        return null;
    }

    /* -------- Webhook handlers -------- */

    protected function handleStripeWebhook(Request $request)
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = StripeWebhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $endpointSecret
            );

            if ($event->type === 'payment_intent.succeeded') {
                $tradeNo = $event->data->object->metadata->trade_no ?? null;
                $amount = $event->data->object->amount_received / 100;
                $this->accountingComplete($tradeNo, 'stripe', $amount);
            }

            return response()->json(['status' => 'ok']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    protected function handlePayPalWebhook(Request $request)
    {
        $eventType = $request->input('event_type');

        if ($eventType === 'CHECKOUT.ORDER.APPROVED') {
            $tradeNo = $request->input('resource.reference_id');
            $amount = $request->input('resource.purchase_units.0.amount.value');
            $this->accountingComplete($tradeNo, 'paypal', $amount);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handlePayPayWebhook(Request $request)
    {
        if ($request->input('status') === 'COMPLETED') {
            $tradeNo = $request->input('merchantPaymentId');
            $amount = $request->input('amount.amount');
            $this->accountingComplete($tradeNo, 'paypay', $amount);
        }
        return response()->json(['status' => 'ok']);
    }

    protected function handleAliPayWebhook(Request $request)
    {
        if ($request->input('trade_status') === 'TRADE_SUCCESS') {
            $tradeNo = $request->input('out_trade_no');
            $amount = $request->input('total_amount');
            $this->accountingComplete($tradeNo, 'ali', $amount);
        }
        return response()->json(['status' => 'ok']);
    }

    protected function handleWeChatWebhook(Request $request)
    {
        if ($request->input('result_code') === 'SUCCESS') {
            $tradeNo = $request->input('out_trade_no');
            $amount = $request->input('total_fee') / 100;
            $this->accountingComplete($tradeNo, 'wx', $amount);
        }
        return response()->json(['status' => 'ok']);
    }

    /* -------- Unified Accounting -------- */
    protected function accountingComplete($tradeNo, $gateway, $amount)
    {
        // Replace with your DB logic:
        // Example: mark order paid, update balances, send email, etc.
        \Log::info("Payment completed", [
            'trade_no' => $tradeNo,
            'gateway' => $gateway,
            'amount' => $amount
        ]);
    }
}
