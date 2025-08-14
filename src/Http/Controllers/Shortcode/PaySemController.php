<?php
/**
 * SPDX-FileCopyrightText: (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 * SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
 * SPDX-FileContributor: Lican Huang
 * @created 2025-08-14
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
// app/Http/Controllers/PaySemController.php
namespace plugins\MoneyPlugin\src\Http\Controllers\Shortcode;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use plugins\MoneyPlugin\src\Services\Shortcode\PayShortcodeService;
class PaySemController extends Controller
{
    public function handle($attrs, $content = '')
    {
        $sem = $attrs['sem'] ?? 0;
        $p = $attrs['p'] ?? '';
        $type = $attrs['type'] ?? '';
        $seller = $attrs['seller'] ?? '';

        // Case: SEM is zero → directly return
        if ($sem == 0) {
            return $content;
        }

        if (is_numeric($sem) && strpos($sem, '.') === false && $sem > 0) {
            if (!empty($seller)) {
                $id = request('ID');
                $groupId = request('groupid');

                $userId = DB::table('domain_postIds')
                    ->where('ID', $id)
                    ->where('groupid', $groupId)
                    ->value('guserID');

                $userInfo = DB::table('users')->find($userId);
                $user = $userInfo->user_login ?? null;

                if (!empty($user) && trim($user) !== trim($seller)) {
                    return back()->withErrors(__('menu.Seller must be the author of the paper!'));
                }
            }

            if (Auth::check() && $seller === Auth::user()->id) {
                return $content;
            }
        } else {
            return back()->withErrors(__('menu.sem must be Integer and greater than 0'));
        }

        // Check SEM balance
        if (!(new PayShortcodeService())->checkEnoughSem($sem, $p, $type)) {
            //  $redirectTo = urlencode(url()->current() . '?' . http_build_query(request()->query()));
            // $clickUrl = SITEURL . "/dc/single.php?groupid=28.56698.327.9487&pid=1317759&redirect_to={$redirectTo}";

            return view('paysem.insufficient', compact('sem', 'clickUrl'));
        }

        // Pay SEM
        if ((new PayShortcodeService())->PaySemPost($sem, $p, $type, $seller)) {
            return $content;
        }


        return back()->withErrors(__('menu.Sem payment failed, please try again later.'));
    }
}
