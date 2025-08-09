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
<div class="container mx-auto p-6 bg-white dark:bg-gray-900 shadow rounded min-h-screen">
    <h1 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-gray-100">Payment Types</h1>

    <a href="{{ route('plugins.MoneyPlugin.ppaytype.create') }}" 
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 mb-4 inline-block">
        Add New
    </a>

    @if(session('success'))
        <p class="text-green-600 dark:text-green-400 mb-4">{{ session('success') }}</p>
    @endif

    <table class="w-full border border-gray-300 dark:border-gray-700">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-800">
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">Pay Type ID</th>
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">Pay Name</th>
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">Cash Type</th>
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">To Cash Type</th>
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">Rate</th>
                <th class="px-4 py-2 border dark:border-gray-700 text-gray-900 dark:text-gray-100">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paytypes as $type)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-2 border dark:border-gray-700 text-gray-800 dark:text-gray-200">{{ $type->paytype }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700 text-gray-800 dark:text-gray-200">{{ $type->payname }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700 text-gray-800 dark:text-gray-200">{{ $type->cashtype }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700 text-gray-800 dark:text-gray-200">{{ $type->to_cashtype }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700 text-gray-800 dark:text-gray-200">{{ $type->rate }}</td>
                    <td class="px-4 py-2 border dark:border-gray-700 space-x-2">
                        <a href="{{ route('plugins.MoneyPlugin.ppaytype.show',  ['ppaytype'=>$type->id]) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                        <a href="{{ route('plugins.MoneyPlugin.ppaytype.edit', $type) }}" class="text-yellow-600 dark:text-yellow-400 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('plugins.MoneyPlugin.ppaytype.destroy', $type) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 border dark:border-gray-700 text-center text-gray-800 dark:text-gray-200">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
