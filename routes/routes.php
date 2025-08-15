<?php
/**
 * SPDX-FileCopyrightText: (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 * SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
 * SPDX-FileContributor: Lican Huang
 * @created 2025-08-07
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

use Illuminate\Support\Facades\Route;
use Plugins\MoneyPlugin\src\Http\Controllers\UserCenter\BalanceController;
use Plugins\MoneyPlugin\src\Http\Controllers\PaymentMethodController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\UserBalanceController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\CurrencyTypeController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\PPayTypeController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\PRateController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\PlatformBalanceController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\PSubBalanceController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\AdminPSavingsLogController;
use Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter\AdminSavingsLogController;
use Plugins\MoneyPlugin\src\Http\Controllers\UserCenter\PSavingsLogController;
use Plugins\MoneyPlugin\src\Http\Controllers\UserCenter\SavingsLogController;

use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\AlipayController;
use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\WeChatPayController;
use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\PayPalController;
use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\PayPayController;
use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\StripePaymentController;

use Plugins\MoneyPlugin\src\Http\Controllers\PayMethod\WebhookController;
use Plugins\MoneyPlugin\src\Http\Controllers\Shortcode\PaySemController;

use Plugins\MoneyPlugin\src\Http\Controllers\Pay\PayController;


Route::prefix('plugins')->name('plugins.')->group(function () {
    Route::prefix('MoneyPlugin')->name('MoneyPlugin.')->group(function () {

        Route::get('/', function () {
            return view('MoneyPlugin::index');
        })->name('index');

        Route::middleware('auth')->group(function () {
            Route::get('/paymentmethods', [PaymentMethodController::class, 'index'])
                ->name('paymentmethods');

            Route::get('/balance', [BalanceController::class, 'index'])
                ->name('balance');
        });

        Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
            Route::get('/userbalance', [UserBalanceController::class, 'index'])
                ->name('userbalance');
        });

        Route::middleware(['auth', 'role:admin'])->group(function () {
            Route::resource('currencytype', CurrencyTypeController::class);

            Route::resource('ppaytype', PPayTypeController::class);
            Route::resource('prate', PRateController::class);


            Route::get('/platformbalance', [PlatformBalanceController::class, 'index'])
                ->name('platformbalance.index');

            Route::get('/psubbalance', [PSubBalanceController::class, 'index'])
                ->name('plugins.MoneyPlugin.psubbalance.index');
            Route::resource('psavingslog', AdminPSavingsLogController::class)->only(['index']);
            Route::resource('savingslog', AdminSavingsLogController::class)
                ->only(['index']);

        });
        Route::middleware(['auth'])->group(function () {

            Route::resource('mypsavingslog', PSavingsLogController::class)->only(['index']);
            Route::resource('mysavingslog', SavingsLogController::class)
                ->only(['index']);

        });

        Route::middleware(['auth'])->group(function () {

            Route::prefix('pay')->name('pay.')->group(function () {

                Route::get('/recharge', [PayController::class, 'recharge'])->name('recharge');
                Route::post('/rechargehandle', [PayController::class, 'rechargehandle'])->name('rechargehandle');

                Route::get('/rechargeSem', [PayController::class, 'rechargeSem'])->name('rechargeSem');

                Route::post('/rechargeSemhandle', [PayController::class, 'rechargeSemhandle'])->name('rechargeSemhandle');

                Route::get('/directpay', [PayController::class, 'directpay'])->name('directpay');
                Route::post('/directpayhandle', [PayController::class, 'directpayhandle'])->name('directpayhandle');


            });
        });



        // Alipay
        Route::post('/alipay/pay', [AlipayController::class, 'pay'])->name('alipay.pay');
        Route::post('/alipay/notify', [AlipayController::class, 'notify'])->name('alipay.notify');
        Route::get('/alipay/return', [AlipayController::class, 'return'])->name('alipay.return');

        // WeChat
        Route::post('/wechat/pay', [WeChatPayController::class, 'pay'])->name('wechat.pay');
        Route::post('/wechat/notify', [WeChatPayController::class, 'notify'])->name('wechat.notify');
        Route::get('/wechat/return', [WeChatPayController::class, 'return'])->name('wechat.return');

        // PayPal

        Route::get('/paypal/create-payment', [PayPalController::class, 'createPayment'])->name('paypal.create');
        Route::get('/paypal/success', [PayPalController::class, 'success'])->name('paypal.success');
        Route::get('/paypal/cancel', [PayPalController::class, 'cancel'])->name('paypal.cancel');


        // PayPay

        Route::post('/paypay/create-payment', [PayPayController::class, 'createPayment']);

        //visa


        Route::post('/stripe/charge', [StripePaymentController::class, 'charge']);


        Route::post('/webhook/payment', [WebhookController::class, 'handle']);

        Route::get('/shortcode/paysem', [PaySemController::class, 'handle'])->name('shortcode.paysem');

    });
});
