{{--
SPDX-FileCopyrightText: (c) 2025 Hangzhou Domain Zones Technology Co., Ltd.
SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
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
@extends('layouts.app') {{-- Assuming main app layout --}}

@section('content')
    <div class="container mx-auto p-6 bg-white dark:bg-gray-900 shadow dark:shadow-lg rounded min-h-screen">

        {{-- Page title --}}
        <h1 class="text-3xl font-semibold mb-6 text-gray-800 dark:text-gray-100">
            {{ __('MoneyPlugin::menu.Payment and Balance information') }}
        </h1>

        {{-- Admin submenu --}}
        @can('admin')
            <nav aria-label="Admin submenu" class="mb-8">
                <ul class="flex space-x-6 text-blue-600 dark:text-blue-400 font-medium">
                    <li>
                        <a href="{{ route('plugins.MoneyPlugin.userbalance') }}" class="hover:underline">
                            {{ __('MoneyPlugin::menu.Admin Balance Information') }}
                        </a>
                    </li>
                </ul>
            </nav>
        @endcan

        {{-- User submenu --}}
        <nav aria-label="User submenu" class="mb-8">
            <ul class="flex space-x-6 text-green-600 dark:text-green-400 font-medium">
                <li>
                    <a href="{{ route('plugins.MoneyPlugin.balance') }}" class="hover:underline">
                        {{ __('MoneyPlugin::menu.My Balance Information') }}
                    </a>
                </li>
            </ul>
            <ul class="flex space-x-6 text-green-600 dark:text-green-400 font-medium mt-2">
                <li>
                    <a href="{{ route('plugins.MoneyPlugin.paymentmethods') }}" class="hover:underline">
                        {{ __('MoneyPlugin::menu.pay methods') }}
                    </a>
                </li>
            </ul>
        </nav>

    </div>
@endsection
