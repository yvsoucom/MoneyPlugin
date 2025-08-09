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
    <h1 class="text-2xl font-semibold mb-6">Savings Log</h1>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Transaction No</th>
                <th class="border px-4 py-2">Pay Type</th>
                <th class="border px-4 py-2">User ID</th>
                <th class="border px-4 py-2">Date/Time</th>
                <th class="border px-4 py-2">Amount</th>
                <th class="border px-4 py-2">Currency</th>
                <th class="border px-4 py-2">Abstract</th>
                <th class="border px-4 py-2">Savings</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="border px-4 py-2">{{ $log->transactionno }}</td>
                    <td class="border px-4 py-2">{{ $log->paytype }}</td>
                    <td class="border px-4 py-2">{{ $log->userid }}</td>
                    <td class="border px-4 py-2">{{ $log->dtime }}</td>
                    <td class="border px-4 py-2">{{ $log->amount }}</td>
                    <td class="border px-4 py-2">{{ $log->currency }}</td>
                    <td class="border px-4 py-2">{{ $log->abstract }}</td>
                    <td class="border px-4 py-2">{{ $log->savings }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="border px-4 py-2 text-center">No data found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
