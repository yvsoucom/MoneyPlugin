@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('plugins.MoneyPlugin.currencytype.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Add New Currency
    </a>
</div>

<div class="container mx-auto p-4">
    <h1 class="text-2xl mb-4">Currency Types</h1>

    @if(session('success'))
        <div class="mb-4 p-2 bg-green-200 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border border-gray-300 p-2">ID</th>
                <th class="border border-gray-300 p-2">Currency Name</th>
                <th class="border border-gray-300 p-2">Remark</th>
                <th class="border border-gray-300 p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($currencies as $currency)
                <tr>
                    <td class="border border-gray-300 p-2">{{ $currency->id }}</td>
                    <td class="border border-gray-300 p-2">{{ $currency->currency_name }}</td>
                    <td class="border border-gray-300 p-2">{{ $currency->remark }}</td>
                    <td class="border border-gray-300 p-2">
                        <a href="{{ route('moneyplugin_currencytype.edit', $currency) }}" class="text-blue-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
