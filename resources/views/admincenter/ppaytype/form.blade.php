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
<div class="mb-4">
    <label class="block font-bold mb-1 text-gray-900 dark:text-gray-100">Pay Type ID</label>
    <input type="number" name="paytype" value="{{ old('paytype', $ppaytype->paytype ?? '') }}"
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 px-3 py-2 @error('paytype') border-red-500 @enderror">
    @error('paytype')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block font-bold mb-1 text-gray-900 dark:text-gray-100">Pay Name</label>
    <input type="text" name="payname" value="{{ old('payname', $ppaytype->payname ?? '') }}"
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 px-3 py-2 @error('payname') border-red-500 @enderror">
    @error('payname')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
</div>

<div class="mb-4">
    <label class="block font-bold mb-1 text-gray-900 dark:text-gray-100">Cash Type</label>
    <select id="cashtype" name="cashtype"
        class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        @foreach($currencyTypes as $currencyType)
            <option value="{{ $currencyType->id }}" {{ old('cashtype', isset($ppaytype) ? $ppaytype->cashtype : '') == $currencyType->id ? 'selected' : '' }}>
                {{ $currencyType->currency_name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-4">
    <label class="block font-bold mb-1 text-gray-900 dark:text-gray-100">To Cash Type</label>

    <select id="to_cashtype" name="to_cashtype"
        class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
        @foreach($currencyTypes as $currencyType)
              <option value="{{ $currencyType->id }}" {{ old('to_cashtype', isset($ppaytype) ? $ppaytype->to_cashtype : '') == $currencyType->id ? 'selected' : '' }}>
                {{ $currencyType->currency_name }}
            </option>
        @endforeach
    </select>

</div>

<div class="mb-4">
    <label class="block font-bold mb-1 text-gray-900 dark:text-gray-100">Rate</label>
    <input type="text" name="rate" value="{{ old('rate', $ppaytype->rate ?? '') }}"
        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 px-3 py-2 @error('rate') border-red-500 @enderror">
    @error('rate')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
</div>

<button type="submit"
    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
    {{ $buttonText }}
</button>
<a href="{{ route('plugins.MoneyPlugin.ppaytype.index') }}"
    class="ml-4 text-gray-600 hover:underline dark:text-gray-300 dark:hover:text-white">
    Cancel
</a>