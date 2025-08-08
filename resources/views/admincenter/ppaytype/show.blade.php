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
    <h1 class="text-2xl font-semibold mb-6">Payment Type Details</h1>

    <p><strong>Pay Type ID:</strong> {{ $ppaytype->paytype }}</p>
    <p><strong>Pay Name:</strong> {{ $ppaytype->payname }}</p>
    <p><strong>Cash Type:</strong> {{ $ppaytype->cashtype }}</p>
    <p><strong>To Cash Type:</strong> {{ $ppaytype->to_cashtype }}</p>
    <p><strong>Rate:</strong> {{ $ppaytype->rate }}</p>

    <a href="{{ route('plugins.MoneyPlugin.ppaytype.index') }}" class="text-blue-600 hover:underline mt-4 inline-block">
        Back to list
    </a>
</div>
@endsection
