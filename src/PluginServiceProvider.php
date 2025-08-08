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


namespace Plugins\MoneyPlugin\src;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
class PluginServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Check for enabled.flag file
        if (!file_exists(__DIR__ . '/../enabled.flag')) {
            return; // ❌ Plugin is disabled
        }
        // Example: views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'MoneyPlugin');
        Route::middleware('web')->group(function () {
            require __DIR__ . '/../routes/routes.php';
        });

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

    }

    public function register()
    {
        //
    }
}