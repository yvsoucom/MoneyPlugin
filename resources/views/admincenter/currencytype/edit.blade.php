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
    <div class="container mx-auto p-4 bg-white dark:bg-gray-900 rounded min-h-screen">
        <h1 class="text-2xl mb-4 text-gray-900 dark:text-gray-100">Edit Currency: {{ $currency->currency_name }}</h1>

        <form action="{{ route('plugins.MoneyPlugin.currencytype.update', ['currencytype' => $currency->id]) }}"
            method="POST">
            @csrf
            @method('PUT')
            
            <input type="hidden" id="id" name="id" value="{{ $currency->id ?? '' }}">

            <div class="mb-4">
                <label for="currency_name" class="block font-semibold mb-1 text-gray-800 dark:text-gray-200">Currency
                    Name</label>
                <input type="text" id="currency_name" name="currency_name"
                    value="{{ old('currency_name', $currency->currency_name) }}"
                    class="w-full border border-gray-300 dark:border-gray-600 p-2 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                @error('currency_name')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="remark" class="block font-semibold mb-1 text-gray-800 dark:text-gray-200">Remark</label>
                <textarea id="remark" name="remark"
                    class="w-full border border-gray-300 dark:border-gray-600 p-2 rounded bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">{{ old('remark', $currency->remark) }}</textarea>
                @error('remark')
                    <p class="text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('plugins.MoneyPlugin.currencytype.index') }}"
                class="ml-4 text-gray-600 dark:text-gray-400 hover:underline">Cancel</a>
        </form>
    </div>
@endsection