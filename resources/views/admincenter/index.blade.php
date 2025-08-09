{{--
 SPDX-FileCopyrightText:  (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 SPDX-FileCopyrightText:  Institute of Future Science and Technology G.K., Tokyo
 SPDX-FileContributor: Lican Huang
 @created 2025-08-07
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
<div class="container mx-auto p-6 bg-white dark:bg-gray-900 shadow rounded min-h-screen">
    <h1 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">Manage Money Plugin</h1>

    <nav class="space-y-2">
        <a href="{{ route('plugins.MoneyPlugin.currencytype.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Currency Types
        </a>
        
        <a href="{{ route('plugins.MoneyPlugin.ppaytype.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Payment Types
        </a>
        <a href="{{ route('plugins.MoneyPlugin.prate.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Rates
        </a>

        <a href="{{ route('plugins.MoneyPlugin.platformbalance.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Platform Balance
        </a>

        <a href="{{ route('plugins.MoneyPlugin.plugins.MoneyPlugin.psubbalance.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Sub Balance
        </a>

        <a href="{{ route('plugins.MoneyPlugin.psavingslog.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Savings Log (Admin PSavingsLog)
        </a>

        <a href="{{ route('plugins.MoneyPlugin.savingslog.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline block">
            Savings Log (Admin SavingsLog)
        </a>
    </nav>
</div>
@endsection
 