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
<div class="container mx-auto p-6 bg-white shadow rounded">
    <h1 class="text-2xl font-semibold mb-6">{{ __('MoneyPlugin::menu.Platform Balance') }}</h1>

    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 border">ID</th>
                <th class="px-4 py-2 border">Cash Type</th>
                <th class="px-4 py-2 border">Income</th>
                <th class="px-4 py-2 border">Pre Income</th>
                <th class="px-4 py-2 border">Pay</th>
                <th class="px-4 py-2 border">New</th>
            </tr>
        </thead>
        <tbody>
            @foreach($balances as $balance)
                <tr>
                    <td class="px-4 py-2 border">{{ $balance->id }}</td>
                    <td class="px-4 py-2 border">{{ $balance->cashtype }}</td>
                    <td class="px-4 py-2 border">{{ $balance->income }}</td>
                    <td class="px-4 py-2 border">{{ $balance->preincome }}</td>
                    <td class="px-4 py-2 border">{{ $balance->pay }}</td>
                    <td class="px-4 py-2 border">{{ $balance->new }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
