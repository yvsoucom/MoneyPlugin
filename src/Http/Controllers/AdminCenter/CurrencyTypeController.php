<?php
/**
* SPDX-FileCopyrightText: (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
* SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
* SPDX-FileContributor: Lican Huang
* @created 2025-08-09
*
* SPDX-License-Identifier: GPL-3.0-or-later
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


namespace Plugins\MoneyPlugin\src\Http\Controllers\AdminCenter;

use Plugins\MoneyPlugin\src\Models\CurrencyType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyTypeController extends Controller
{
    public function index()
    {
        $currencies = CurrencyType::all();
        return view('MoneyPlugin::currencytype.index', compact('currencies'));
    }

    public function edit(CurrencyType $currencyType)
    {
        return view('MoneyPlugin::currencytype.edit', ['currency' => $currencyType]);
    }

    public function update(Request $request, CurrencyType $currencyType)
    {
        $request->validate([
            'currency_name' => 'required|string|max:100',
            'remark' => 'nullable|string',
        ]);

        $currencyType->update($request->only('currency_name', 'remark'));

        return redirect()->route('plugins.MoneyPlugin.currencytype.index')->with('success', 'Currency updated!');
    }
    public function create()
    {
        return view('MoneyPlugin::currencytype.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_name' => 'required|string|max:100',
            'remark' => 'nullable|string',
        ]);

        CurrencyType::create([
            'currency_name' => $request->currency_name,
            'remark' => $request->remark,
        ]);

        return redirect()->route('plugins.MoneyPlugin.currencytype.index')
            ->with('success', 'Currency added successfully!');
    }

}
