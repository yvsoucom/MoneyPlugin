{{--
SPDX-FileCopyrightText: (c) 2025 Hangzhou Domain Zones Technology Co., Ltd.
SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
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
    <div class="container mx-auto p-6 bg-white dark:bg-gray-900 shadow rounded min-h-screen">
        <h1 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">Savings Log</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Transaction No
                        </th>
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Date</th>
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Pay Type</th>
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Pay Cash</th>
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Amount</th>

                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Savings</th>
                        <th class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">Abstract</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ $log->transactionno }}</td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">{{ $log->dtime }}
                            </td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ number_format($log->paytype) }}</td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ number_format($log->currency) }}</td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ number_format($log->amount, 2) }}</td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ number_format($log->savings, 2) }}</td>
                            <td class="border px-4 py-2 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                                {{ $log->abstract }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500 dark:text-gray-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
@endsection