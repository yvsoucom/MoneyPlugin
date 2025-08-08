@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl mb-4">Edit Currency: {{ $currency->currency_name }}</h1>

    <form action="{{ route('moneyplugin_currencytype.update', $currency) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="currency_name" class="block font-semibold mb-1">Currency Name</label>
            <input type="text" id="currency_name" name="currency_name" value="{{ old('currency_name', $currency->currency_name) }}" class="w-full border p-2 rounded">
            @error('currency_name')
                <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="remark" class="block font-semibold mb-1">Remark</label>
            <textarea id="remark" name="remark" class="w-full border p-2 rounded">{{ old('remark', $currency->remark) }}</textarea>
            @error('remark')
                <p class="text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
        <a href="{{ route('moneyplugin_currencytype.index') }}" class="ml-4 text-gray-600 hover:underline">Cancel</a>
    </form>
</div>
@endsection
