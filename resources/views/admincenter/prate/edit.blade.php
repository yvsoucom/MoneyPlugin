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
    <h1 class="text-2xl font-semibold mb-6">Edit Rate</h1>

    <form method="POST" action="{{ route('plugins.MoneyPlugin.prate.update', $prate) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="rateName" class="block text-gray-700 font-bold mb-2">Rate Name</label>
            <input id="rateName" name="rateName" type="text" value="{{ old('rateName', $prate->rateName) }}"
                class="w-full border  rounded px-3 py-2 @error('rateName') border-red-500 @enderror">
            @error('rateName')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="rate" class="block text-gray-700 font-bold mb-2">Rate</label>
            <input id="rate" name="rate" type="number" step="0.00001" value="{{ old('rate', $prate->rate) }}"
                class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label for="cashtype" class="block text-gray-700 font-bold mb-2">Cash Type</label>
            <select id="cashtype" name="cashtype" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="1" {{ $prate->cashtype == 1 ? 'selected' : '' }}>Type 1</option>
                <option value="2" {{ $prate->cashtype == 2 ? 'selected' : '' }}>Type 2</option>
                <option value="3" {{ $prate->cashtype == 3 ? 'selected' : '' }}>Type 3</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="pernum" class="block text-gray-700 font-bold mb-2">Per Number</label>
            <input id="pernum" name="pernum" type="number" value="{{ old('pernum', $prate->pernum) }}"
                class="w-full border border-gray-300 rounded px-3 py-2">
        </div>

        <div class="mb-4">
            <label for="remarks" class="block text-gray-700 font-bold mb-2">Remarks</label>
            <textarea id="remarks" name="remarks" class="w-full border border-gray-300 rounded px-3 py-2">{{ old('remarks', $prate->remarks) }}</textarea>
        </div>

        <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
            Update Rate
        </button>
        <a href="{{ route('plugins.MoneyPlugin.prate.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection
