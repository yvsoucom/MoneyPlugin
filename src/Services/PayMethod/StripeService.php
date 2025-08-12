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
use Stripe\Exception\ApiErrorException;


use Stripe\Stripe;
use Stripe\Webhook;

use Stripe\PaymentIntent;
 
use Illuminate\Http\JsonResponse;
 

class StripeService
{
    public function process(string $tradeNo, float $amount, string $currency, array $metadata): array
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
            // Log error, or return error response
            return ['error' => $e->getMessage()];
        }
    }

    protected function handleWebhook(Request $request): JsonResponse
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature'),
                $endpointSecret
            );

            if ($event->type === 'payment_intent.succeeded') {
                $tradeNo = $event->data->object->metadata->trade_no ?? null;
                $amount = $event->data->object->amount_received / 100;
                $gateway = 'stripe';

                // Your business logic here, e.g., update order status

                return response()->json(['status' => 'success', 'tradeNo' => $tradeNo, 'amount' => $amount]);
            }

            return response()->json(['status' => 'ignored']);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }
    }
}
