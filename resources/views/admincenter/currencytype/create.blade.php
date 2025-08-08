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
 