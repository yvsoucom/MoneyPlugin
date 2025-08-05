<?php

use Illuminate\Support\Facades\Route;

Route::get('/money-plugin', function () {
    return view('moneyPlugin::index');
});