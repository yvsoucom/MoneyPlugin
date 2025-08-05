<?php

use Illuminate\Support\Facades\Route;

Route::get('/MoneyPlugin', function () {
    return view('MoneyPlugin::index');
});