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
            Route::get('currencytype', [CurrencyTypeController::class, 'index'])->name('currencytype.index');
            Route::get('currencytype/create', [CurrencyTypeController::class, 'create'])->name('currencytype.create');
            Route::post('currencytype', [CurrencyTypeController::class, 'store'])->name('currencytype.store');
            Route::get('currencytype/{currencyType}/edit', [CurrencyTypeController::class, 'edit'])->name('currencytype.edit');
            Route::put('currencytype/{currencyType}', [CurrencyTypeController::class, 'update'])->name('currencytype.update');

            Route::resource('ppaytype', PPayTypeController::class);
            Route::resource('prate', PRateController::class);


            Route::get('/platformbalance', [PlatformBalanceController::class, 'index'])
                ->name('plugins.MoneyPlugin.platformbalance.index');

            Route::get('/psubbalance', [PSubBalanceController::class, 'index'])
                ->name('plugins.MoneyPlugin.psubbalance.index');
            Route::get('psavingslog', AdminPSavingsLogController::class)->only(['index']);
            Route::get('savingslog', AdminSavingsLogController::class)
                ->only(['index']);

        });
        Route::middleware(['auth'])->group(function () {

            Route::get('mypsavingslog', PSavingsLogController::class)->only(['index']);
            Route::get('mysavingslog', SavingsLogController::class)
                ->only(['index']);

        });
    });
});
