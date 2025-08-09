{{--
 SPDX-FileCopyrightText:  (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 SPDX-FileCopyrightText:  Institute of Future Science and Technology G.K., Tokyo
 SPDX-FileContributor: Lican Huang
 @created 2025-08-09
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
<div class="container mx-auto p-6 bg-white shadow rounded min-h-screen">
    <h1 class="text-2xl font-semibold mb-6">{{ __('MoneyPlugin::menu.Savings Log') }}</h1>

    <table class="min-w-full bg-white border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Transaction No</th>
                <th class="px-4 py-2 border">Pay Type</th>
                <th class="px-4 py-2 border">User</th>
                <th class="px-4 py-2 border">Date</th>
                <th class="px-4 py-2 border">Amount</th>
                <th class="px-4 py-2 border">Currency</th>
                <th class="px-4 py-2 border">Abstract</th>
                <th class="px-4 py-2 border">Income</th>
                <th class="px-4 py-2 border">Pre Income</th>
                <th class="px-4 py-2 border">Pay</th>
                <th class="px-4 py-2 border">New Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td class="px-4 py-2 border">{{ $log->transactionno }}</td>
                <td class="px-4 py-2 border">{{ $log->paytype }}</td>
                <td class="px-4 py-2 border">{{ $log->userid }}</td>
                <td class="px-4 py-2 border">{{ $log->dtime }}</td>
                <td class="px-4 py-2 border">{{ number_format($log->amount, 2) }}</td>
                <td class="px-4 py-2 border">{{ $log->currency }}</td>
                <td class="px-4 py-2 border">{{ $log->abstract }}</td>
                <td class="px-4 py-2 border">{{ number_format($log->income, 2) }}</td>
                <td class="px-4 py-2 border">{{ number_format($log->preincome, 2) }}</td>
                <td class="px-4 py-2 border">{{ number_format($log->pay, 2) }}</td>
                <td class="px-4 py-2 border">{{ number_format($log->new, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center py-4 text-gray-500">No transactions found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
