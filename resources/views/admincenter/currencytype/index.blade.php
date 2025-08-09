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
--}}
@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('plugins.MoneyPlugin.currencytype.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-400">
        Add New Currency
    </a>
</div>

<div class="container mx-auto p-4 bg-white dark:bg-gray-900 rounded shadow">
    <h1 class="text-2xl mb-4 text-gray-900 dark:text-gray-100">Currency Types</h1>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-200 text-green-800 rounded dark:bg-green-700 dark:text-green-100">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300 dark:border-gray-700">
        <thead>
            <tr class="bg-gray-100 dark:bg-gray-800">
                <th class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">ID</th>
                <th class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">Currency Name</th>
                <th class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">Remark</th>
                <th class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">{{ $currency->id }}</td>
                    <td class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">{{ $currency->currency_name }}</td>
                    <td class="border border-gray-300 p-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">{{ $currency->remark }}</td>
                    <td class="border border-gray-300 p-2 flex space-x-2 dark:border-gray-700">
                        <a href="{{ route('plugins.MoneyPlugin.currencytype.edit', ['currencytype' => $currency->id]) }}"
                           class="text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">Edit</a>

                        <form action="{{ route('plugins.MoneyPlugin.currencytype.destroy', ['currencytype' => $currency->id]) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this currency?');">
                            @csrf
                            @method('DELETE')
                             <input type="hidden" id="id" name="id" value="{{ $currency->id ?? '' }}">
                            <button type="submit" class="text-red-600 hover:underline dark:text-red-400 dark:hover:text-red-300">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
 