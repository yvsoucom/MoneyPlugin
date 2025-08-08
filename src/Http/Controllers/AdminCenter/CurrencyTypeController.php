<?php

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
