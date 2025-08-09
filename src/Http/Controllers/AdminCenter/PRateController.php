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

use App\Http\Controllers\Controller;
use Plugins\MoneyPlugin\src\Models\PRate;
use Plugins\MoneyPlugin\src\Models\CurrencyType;
use Illuminate\Http\Request;

class PRateController extends Controller
{
    public function index()
    {
        $rates = PRate::all();
        return view('MoneyPlugin::admincenter.prate.index', compact('rates'));
    }

    public function create()
    {
        $currencyTypes = CurrencyType::all(); // Or add ordering if needed
        return view('MoneyPlugin::admincenter.prate.create', compact('currencyTypes'));

    }

    public function store(Request $request)
    {
        $request->validate([
            'rateType' => 'required',
            'rateName' => 'required|string|max:600',
            'rate' => 'required|numeric',
            'cashtype' => 'required',
            'pernum' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ]);

        PRate::create($request->all());
        return redirect()->route('plugins.MoneyPlugin.prate.index')->with('success', 'Rate added successfully.');
    }

    public function show(PRate $prate)
    {
        return view('MoneyPlugin::admincenter.prate.show', compact('prate'));
    }

    public function edit(PRate $prate)
    {
        $currencyTypes = CurrencyType::all(); // Or add ordering if needed

        return view('MoneyPlugin::admincenter.prate.edit', compact('prate', 'currencyTypes'));
    }

    public function update(Request $request, PRate $prate)
    {
        $request->validate([
            'rateType' => 'required',
            'rateName' => 'required|string|max:600',
            'rate' => 'required|numeric',
            'cashtype' => 'required',
            'pernum' => 'nullable|integer',
            'remarks' => 'nullable|string',
        ]);

        $prate->update($request->all());
        return redirect()->route('plugins.MoneyPlugin.prate.index')->with('success', 'Rate updated successfully.');
    }

    public function destroy(PRate $prate)
    {
        $prate->delete();
        return redirect()->route('plugins.MoneyPlugin.prate.index')->with('success', 'Rate deleted successfully.');
    }
}
