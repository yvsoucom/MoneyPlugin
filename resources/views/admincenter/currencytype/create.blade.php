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

    <h1 class="text-2xl font-semibold mb-6">{{ __('MoneyPlugin::menu.Add Currency') }}</h1>

    <form method="POST" action="{{ route('plugins.MoneyPlugin.currencytype.store') }}">
        @csrf

        <div class="mb-4">
            <label for="currency_name" class="block text-gray-700 font-bold mb-2">Currency Name</label>
            <input id="currency_name" name="currency_name" type="text" value="{{ old('currency_name') }}"
                aria-describedby="currency_name_error"
                class="w-full border rounded px-3 py-2 @error('currency_name') border-red-500 @else @enderror">
            @error('currency_name')
            <p id="currency_name_error" class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="remark" class="block text-gray-700 font-bold mb-2">Remark</label>
            <textarea id="remark" name="remark"
                class="w-full border border-gray-300 rounded px-3 py-2">{{ old('remark') }}</textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Currency
        </button>

        <a href="{{ route('plugins.MoneyPlugin.currencytype.index') }}" class="ml-4 text-gray-600 hover:underline">
            Cancel
        </a>
    </form>

</div>
@endsection
 