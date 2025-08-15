{{--
 SPDX-FileCopyrightText:  (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 SPDX-FileCopyrightText:  Institute of Future Science and Technology G.K., Tokyo
 SPDX-FileContributor: Lican Huang
 @created 2025-08-15
*
* SPDX-License-Identifier: GPL-3.0-or-later OR LicenseRef-Proprietary
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
--}} 
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 max-w-lg w-full">
        <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-gray-100">Payment Form</h2>

        <form method="POST" action="{{ route('plugins.MoneyPlugin.pay.directpayhandle') }}" class="space-y-5">
            @csrf

            {{-- Currency Type --}}
            <div>
                <label for="currency" class="block mb-1 text-gray-700 dark:text-gray-300 font-medium">
                    Currency Type
                </label>
                <select id="currency" name="currency" class="w-full border-gray-300 dark:border-gray-700 rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-400">
                    <option value="USD">USD</option>
                    <option value="RMB">RMB</option>
                    <option value="EUR">EUR</option>
                </select>
            </div>

            {{-- Amount --}}
            <div>
                <label for="amount" class="block mb-1 text-gray-700 dark:text-gray-300 font-medium">
                    Amount
                </label>
                <input type="number" step="0.01" id="amount" name="amount" required 
                    class="w-full border-gray-300 dark:border-gray-700 rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-400">
            </div>

            {{-- Payment Gateway --}}
            <div>
                <label for="gateway" class="block mb-1 text-gray-700 dark:text-gray-300 font-medium">
                    Payment Gateway
                </label>
                <select id="gateway" name="gateway" class="w-full border-gray-300 dark:border-gray-700 rounded-lg p-2 dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-400">
                    <option value="paypal">PayPal</option>
                    <option value="stripe">Stripe</option>
                    <option value="wechat">WeChat Pay</option>
                    <option value="alipay">Alipay</option>
                </select>
            </div>

            {{-- Submit --}}
            <button type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                Submit Payment
            </button>
        </form>
    </div>
</div>
@endsection
 
