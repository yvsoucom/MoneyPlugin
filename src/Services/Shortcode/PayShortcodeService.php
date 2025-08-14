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
// plugins/MoneyPlugin/src/Services/Shortcode/PayShortcodeService.php
namespace plugins\MoneyPlugin\src\Services\Shortcode;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayShortcodeService
{
    public function paySemPost(int $sem, int $p, string $type, string $seller = ''): bool
    {
        $id = request()->input('ID');
        $groupId = request()->input('groupid');
        $pid = request()->input('pid');

        $row = DB::table('domain_postIds AS A')
            ->join('domain_posts AS B', 'A.ID', '=', 'B.ID')
            ->where('A.ID', $id)
            ->where('A.groupid', $groupId)
            ->select('A.guserID', 'B.post_title')
            ->first();

        $user = null;
        $title = '';

        if ($row) {
            $title = $row->post_title;
            $userId = $row->guserID;

            $userInfo = DB::table('users')->find($userId);
            $user = $userInfo->user_login ?? null;
        }

        if (empty(trim($user))) {
            $user = $seller;
        }

        if (($row->guserID ?? null) == 1 || $p == 1) {
            $user = '';
        }

        $buyer = Auth::user()->id ;
        $abstract = "{$title} type = {$type} seller = {$user} buyer = {$buyer}";

        if (empty($user)) {
            $this->paySemTransactionAccounting($abstract, 5, $sem, $buyer);
        } else {
            $this->semPayBrokerageTransactionAccounting($abstract, 4, $sem, $buyer, $user);
        }

        return true;
    }

    public function checkEnoughSem(int $sem, int $p, string $type): bool
    {
  

        if (!$this->checkSemPost($sem)) {
            
                echo "Need {$sem} sem. Not enough balance. Please recharge SEM!";
            
            return false;
        }

        $this->paySemPost($sem, $p, $type);
        return true;
    }

    // ----- Placeholder helper methods ----- 

    public function checkSemPost(int $sem): bool
    {
        // Original Check_Sem_Post($sem) logic here
        return true; // placeholder
    }

    private function paySemTransactionAccounting($abstract, $code, $sem, $buyer)
    {
        // Original paysemansactionAccounting logic
    }

    private function semPayBrokerageTransactionAccounting($abstract, $code, $sem, $buyer, $seller)
    {
        // Original sempaybrokeragetransactionAccounting logic
    }
}

 