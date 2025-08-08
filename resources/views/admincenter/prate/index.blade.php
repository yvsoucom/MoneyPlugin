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

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Rates</h1>
        <a href="{{ route('plugins.MoneyPlugin.prate.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add Rate
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr class="bg-gray-100 border-b">
                <th class="py-2 px-4 border-b">ID</th>
                <th class="py-2 px-4 border-b">Rate Name</th>
                <th class="py-2 px-4 border-b">Rate</th>
                <th class="py-2 px-4 border-b">Cash Type</th>
                <th class="py-2 px-4 border-b">Per Number</th>
                <th class="py-2 px-4 border-b">Remarks</th>
                <th class="py-2 px-4 border-b text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rates as $rate)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-4">{{ $rate->rateType }}</td>
                    <td class="py-2 px-4">{{ $rate->rateName }}</td>
                    <td class="py-2 px-4">{{ $rate->rate }}</td>
                    <td class="py-2 px-4">{{ $rate->cashtype }}</td>
                    <td class="py-2 px-4">{{ $rate->pernum }}</td>
                    <td class="py-2 px-4">{{ $rate->remarks }}</td>
                    <td class="py-2 px-4 text-right">
                        <a href="{{ route('plugins.MoneyPlugin.prate.show', $rate) }}" 
                           class="text-blue-600 hover:underline mr-2">View</a>
                        <a href="{{ route('plugins.MoneyPlugin.prate.edit', $rate) }}" 
                           class="text-yellow-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('plugins.MoneyPlugin.prate.destroy', $rate) }}" method="POST" 
                              class="inline-block" 
                              onsubmit="return confirm('Delete this rate?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="py-4 text-center text-gray-500">No rates found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
