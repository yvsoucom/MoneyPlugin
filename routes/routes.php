<?php

use Illuminate\Support\Facades\Route;

// Public
Route::prefix('plugins')->name('plugins.')->group(function () {
    Route::prefix('MoneyPlugin')->name('MoneyPlugin.')->group(function () {
        Route::get('/', function () {
            return view('MoneyPlugin::index');
        });
    });
});

