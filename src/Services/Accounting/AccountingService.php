<?php
/**
 * SPDX-FileCopyrightText: (c) 2025  Hangzhou Domain Zones Technology Co., Ltd.
 * SPDX-FileCopyrightText: Institute of Future Science and Technology G.K., Tokyo
 * SPDX-FileContributor: Lican Huang
 * @created 2025-08-12
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

namespace plugins\MoneyPlugin\src\Services\Accounting;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use plugins\MoneyPlugin\src\Models\Prate;
use plugins\MoneyPlugin\src\Models\Ppaytype;
use Carbon\Carbon;

class AccountingService
{

    function getPlatRate($rateType)
    {
        return Prate::where('rateType', $rateType)->value('rate');

    }
    /**
     * Generate a new unique transaction number.
     * You can customize this logic as needed.
     */
    protected function getTransactionNewNo()
    {
        // Example: Use current timestamp and random number for uniqueness
        return 'TXN' . date('YmdHis') . mt_rand(1000, 9999);
    }
    function getPaytypeRate($paytype)
    {
        return Ppaytype::where('paytype', $paytype)->value('rate');
    }

    function getPaytypeName($paytype)
    {
        return Ppaytype::where('paytype', $paytype)->value('payname');
    }

    public function semRecharge($tradeNo, $gateway, $amount)
    {
        $record = DB::table('alipay')
            ->where('alipay_num', $tradeNo)
            ->first();

        if (!$record) {
            Log::warning("No record found for semRecharge: $tradeNo");
            return;
        }

        $itemType = trim($record->alipay_type);
        $userName = $record->alipay_user;
        $sellerName = $record->alipay_seller;

        // Map gateway to paytype
        $paytypeMap = [
            'ali' => 1,
            'wx' => 11,
            'paypal' => 18
        ];

        if (in_array($itemType, ['semcharge', 'alibindingcharge', 'wxbindingcharge', 'logincharge'])) {
            $paytype = $paytypeMap[$gateway] ?? null;
            if ($paytype) {
                if ($gateway === 'paypal') {
                    $this->rechargeTransactionAccountingPayPal($tradeNo, $itemType, $paytype, $amount, $userName);
                } else {
                    $this->rechargeTransactionAccounting($tradeNo, $itemType, $paytype, $amount, $userName);
                }
            }
        }

        if ($itemType === 'donation') {
            $paytype = ($gateway === 'ali') ? 13 : 15;
            $this->donationTransactionAccounting($tradeNo, $itemType, $paytype, $amount, $userName, $sellerName);
        }
    }

    public function directMoneyPay($tradeNo, $gateway, $amount)
    {
        $record = DB::table('alipay')
            ->where('alipay_num', $tradeNo)
            ->first();

        if (!$record) {
            Log::warning("No record found for directMoneyPay: $tradeNo");
            return;
        }

        $itemType = trim($record->alipay_type);
        $userName = $record->alipay_user;
        $sellerName = $record->alipay_seller;

        if ($itemType === 'charge') {
            $paytype = ($gateway === 'ali') ? 9 : 12;
            $this->chargeTransactionAccounting($tradeNo, $itemType, $paytype, $amount, $userName);
        }

        if ($itemType === 'moneypay') {
            $paytype = ($gateway === 'ali') ? 10 : 14;
            $this->moneyPayTransactionAccounting($tradeNo, $itemType, $paytype, $amount, $userName);
        }

        if ($itemType === 'moneypaybrokerage') {
            $paytype = ($gateway === 'ali') ? 13 : 15;
            $this->moneyPayBrokerageTransactionAccounting($tradeNo, $itemType, $paytype, $amount, $userName, $sellerName);
        }
    }


    public function rechargetransactionAccountingPayPal($outTradeNo, $itemType, $paytype, $total, $adduser, $selluser = null)
    {
        $blogId = app()->getLocale(); // or your custom locale function
        $semrate = $this->getPlatRate(81);
        $addsavings = $total * $semrate;
        $alipayrate = $this->getPaytypeRate($paytype);

        $chargeStr = trim($itemType) . $this->getPaytypeName($paytype) . $total . " SEM " . $addsavings . " ) ";
        $chargeStrNewSem = "新发SEM" . trim($itemType) . $this->getPaytypeName($paytype) . $total . " SEM " . $addsavings . " ) ";
        $chargeStrIncome = "USD收入" . trim($itemType) . $this->getPaytypeName($paytype) . $total . " SEM " . $addsavings . " ) ";
        $chargeStrPay = "支付手续费" . trim($itemType) . $this->getPaytypeName($paytype) . $total . " SEM " . $addsavings . " ) ";

        $newTransactionNo = $this->getTransactionNewNo();

        $tenxiuFee = round($total * $alipayrate, 2) + 0.3;  // 4.4% + 0.3

        DB::beginTransaction();

        try {
            // Update savingsSEM
            DB::table('account')
                ->where('username', $adduser)
                ->increment('savingsSEM', $addsavings);

            // Get savings after update
            $account = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $adduser)
                ->first();

            // Insert savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargeStr,
                'savingsRMB' => $account->savingsRMB,
                'savingsUSD' => $account->savingsUSD,
                'savingsSEM' => $account->savingsSEM,
            ]);

            // Update platformbalance newsem
            DB::table('platformbalance')->increment('newsem', $addsavings);

            // Update psubbalance newsem for this paytype
            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->increment('newsem', $addsavings);

            $platformBalance = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            // Insert psavingslog for newsem
            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargeStrNewSem,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay,
            ]);

            // Update platformbalance usdincome
            DB::table('platformbalance')->increment('usdincome', $total);

            // Update psubbalance usdincome for paytype
            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->increment('usdincome', $total);

            $platformBalance = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            // Insert psavingslog for usdincome
            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => now(),
                'amount' => $total,
                'currency' => 'USD',
                'abstract' => $chargeStrIncome,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay,
            ]);

            // Handle tenxiu fee
            if ($tenxiuFee > 0) {
                DB::table('platformbalance')->increment('usdpay', $tenxiuFee);
                DB::table('psubbalance')
                    ->where('paytype', $paytype)
                    ->increment('usdpay', $tenxiuFee);

                $platformBalance = DB::table('platformbalance')
                    ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                    ->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $paytype,
                    'username' => $adduser,
                    'dtime' => now(),
                    'amount' => $tenxiuFee,
                    'currency' => 'USD',
                    'abstract' => $chargeStrPay,
                    'newsem' => $platformBalance->newsem,
                    'semincome' => $platformBalance->semincome,
                    'rmbincome' => $platformBalance->rmbincome,
                    'rmbpreincome' => $platformBalance->rmbpreincome,
                    'rmbpay' => $platformBalance->rmbpay,
                    'usdincome' => $platformBalance->usdincome,
                    'usdpreincome' => $platformBalance->usdpreincome,
                    'usdpay' => $platformBalance->usdpay,
                ]);
            }

            // Update paychecked on alipay table
            DB::table('yzwp_alipay')
                ->where('alipay_num', $outTradeNo)
                ->increment('paychecked', 1);

            DB::commit();

            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("rechargetransactionAccountingPayPal error: " . $e->getMessage());
            return false;
        }
    }


    public function rechargeTransactionAccounting($outTradeNo, $itemType, $paytype, $total, $adduser, $selluser = null)
    {
        $blogId = $this->getCurrentLang(); // Replace with your own lang logic

        $semRate = $this->getPlatRate(80);
        $addsavings = $total * $semRate;

        $alipayRate = $this->getPaytypeRate($paytype);
        $payName = $this->getPaytypeName($paytype);

        $chargestr = trim($itemType) . $payName . $total . " SEM " . $addsavings . " ) ";
        $chargestrnewsem = "新发SEM" . trim($itemType) . $payName . $total . " SEM " . $addsavings . " ) ";
        $chargestrincome = "RMB收入" . trim($itemType) . $payName . $total . " SEM " . $addsavings . " ) ";
        $chargestrpay = "支付手续费" . trim($itemType) . $payName . $total . " SEM " . $addsavings . " ) ";

        $newTransactionNo = $this->getTransactionNewNo();
        $tenxiufee = round($total * $alipayRate, 2);

        DB::transaction(function () use ($addsavings, $total, $adduser, $paytype, $chargestr, $chargestrnewsem, $chargestrincome, $chargestrpay, $newTransactionNo, $tenxiufee, $outTradeNo) {
            // 1. Update account
            DB::table('account')
                ->where('username', $adduser)
                ->increment('savingsSEM', $addsavings);

            $account = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $adduser)
                ->first();

            // 2. Insert into savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'savingsRMB' => $account->savingsRMB,
                'savingsUSD' => $account->savingsUSD,
                'savingsSEM' => $account->savingsSEM
            ]);

            // 3. Update platform balances
            DB::table('platformbalance')->increment('newsem', $addsavings);
            DB::table('psubbalance')->where('paytype', $paytype)->increment('newsem', $addsavings);

            $plat = DB::table('platformbalance')->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargestrnewsem,
                'newsem' => $plat->newsem,
                'semincome' => $plat->semincome,
                'rmbincome' => $plat->rmbincome,
                'rmbpreincome' => $plat->rmbpreincome,
                'rmbpay' => $plat->rmbpay,
                'usdincome' => $plat->usdincome,
                'usdpreincome' => $plat->usdpreincome,
                'usdpay' => $plat->usdpay
            ]);

            // RMB income
            DB::table('platformbalance')->increment('rmbincome', $total);
            DB::table('psubbalance')->where('paytype', $paytype)->increment('rmbincome', $total);

            $plat = DB::table('platformbalance')->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => Carbon::now(),
                'amount' => $total,
                'currency' => 'RMB',
                'abstract' => $chargestrincome,
                'newsem' => $plat->newsem,
                'semincome' => $plat->semincome,
                'rmbincome' => $plat->rmbincome,
                'rmbpreincome' => $plat->rmbpreincome,
                'rmbpay' => $plat->rmbpay,
                'usdincome' => $plat->usdincome,
                'usdpreincome' => $plat->usdpreincome,
                'usdpay' => $plat->usdpay
            ]);

            // RMB pay (fee)
            if ($tenxiufee > 0) {
                DB::table('platformbalance')->increment('rmbpay', $tenxiufee);
                DB::table('psubbalance')->where('paytype', $paytype)->increment('rmbpay', $tenxiufee);

                $plat = DB::table('platformbalance')->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $paytype,
                    'username' => $adduser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiufee,
                    'currency' => 'RMB',
                    'abstract' => $chargestrpay,
                    'newsem' => $plat->newsem,
                    'semincome' => $plat->semincome,
                    'rmbincome' => $plat->rmbincome,
                    'rmbpreincome' => $plat->rmbpreincome,
                    'rmbpay' => $plat->rmbpay,
                    'usdincome' => $plat->usdincome,
                    'usdpreincome' => $plat->usdpreincome,
                    'usdpay' => $plat->usdpay
                ]);
            }

            // Update alipay table (custom table name)
            DB::table('yzwp_alipay')
                ->where('alipay_num', $outTradeNo)
                ->increment('paychecked');
        });

        return true;
    }
    // Dummy stubs for actual accounting logic
    protected function getCurrentLang()
    {
        return app()->getLocale(); // or your custom lang function
    }

    function chargetransactionAccounting($out_trade_no, $item_type, $paytype, $total, $adduser, $selluser = null)
    {
        $blog_id = $this->getCurrentLang(); // Use the class method for current language
        $addsavings = $total;

        $alipayrate = $this->getpaytypeRate($paytype); // Needs to be converted to Laravel equivalent
        $tenxiufee = round($total * $alipayrate, 2);

        $chargestr = trim($item_type) . $this->getpaytypeName($paytype) . " $total 支付手续费(平台垫付) $tenxiufee ) ";

        $newtransactionno = $this->gettransactionnewno(); // Needs Laravel version too

        return DB::transaction(function () use ($out_trade_no, $paytype, $adduser, $addsavings, $total, $tenxiufee, $chargestr, $newtransactionno) {
            // Update account savings
            DB::table('account')
                ->where('username', $adduser)
                ->increment('savingsRMB', $addsavings);

            $rmbsvaingArray = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $adduser)
                ->first();

            // Insert into savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'RMB',
                'abstract' => $chargestr,
                'savingsRMB' => $rmbsvaingArray->savingsRMB,
                'savingsUSD' => $rmbsvaingArray->savingsUSD,
                'savingsSEM' => $rmbsvaingArray->savingsSEM
            ]);

            // Update rmbpreincome
            DB::table('platformbalance')->increment('rmbpreincome', $addsavings);
            DB::table('psubbalance')->where('paytype', $paytype)->increment('rmbpreincome', $addsavings);

            $platformBalance = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $adduser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'RMB',
                'abstract' => $chargestr,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay
            ]);

            // If there’s a fee, update rmbpay
            if ($tenxiufee > 0) {
                DB::table('platformbalance')->increment('rmbpay', $tenxiufee);
                DB::table('psubbalance')->where('paytype', $paytype)->increment('rmbpay', $tenxiufee);

                $platformBalance = DB::table('platformbalance')
                    ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                    ->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newtransactionno,
                    'paytype' => $paytype,
                    'username' => $adduser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiufee,
                    'currency' => 'RMB',
                    'abstract' => $chargestr,
                    'newsem' => $platformBalance->newsem,
                    'semincome' => $platformBalance->semincome,
                    'rmbincome' => $platformBalance->rmbincome,
                    'rmbpreincome' => $platformBalance->rmbpreincome,
                    'rmbpay' => $platformBalance->rmbpay,
                    'usdincome' => $platformBalance->usdincome,
                    'usdpreincome' => $platformBalance->usdpreincome,
                    'usdpay' => $platformBalance->usdpay
                ]);
            }

            // Update alipay paychecked
            DB::table('yzwp_alipay')
                ->where('alipay_num', $out_trade_no)
                ->increment('paychecked', 1);

            return true;
        });
    }

    public function moneypayTransactionAccounting($outTradeNo, $itemType, $payType, $total, $addUser, $sellUser = null)
    {
        // Prepare data
        $addsavings = $total;
        $alipayRate = $this->getPaytypeRate($payType);
        $tenxiufee = round($total * $alipayRate, 2);
        $paytypeName = $this->getPaytypeName($payType);
        $chargeStr = trim($itemType) . $paytypeName . " $total 支付手续费(平台垫付) $tenxiufee )";
        $newTransactionNo = $this->getTransactionNewNo();

        DB::beginTransaction();

        try {
            // Fetch current savings
            $account = DB::table('account')->where('username', $addUser)->first();

            // Insert into savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $addUser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'savingsRMB' => $account->savingsRMB,
                'savingsUSD' => $account->savingsUSD,
                'savingsSEM' => $account->savingsSEM
            ]);

            // Update platform balances (rmbincome)
            DB::table('platformbalance')->update([
                'rmbincome' => DB::raw("rmbincome + {$addsavings}")
            ]);
            DB::table('psubbalance')->where('paytype', $payType)->update([
                'rmbincome' => DB::raw("rmbincome + {$addsavings}")
            ]);

            // Insert into psavingslog for rmbincome
            $platform = DB::table('platformbalance')->first();
            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $addUser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'newsem' => $platform->newsem,
                'semincome' => $platform->semincome,
                'rmbincome' => $platform->rmbincome,
                'rmbpreincome' => $platform->rmbpreincome,
                'rmbpay' => $platform->rmbpay,
                'usdincome' => $platform->usdincome,
                'usdpreincome' => $platform->usdpreincome,
                'usdpay' => $platform->usdpay
            ]);

            // Handle rmbpay if fee > 0
            if ($tenxiufee > 0) {
                DB::table('platformbalance')->update([
                    'rmbpay' => DB::raw("rmbpay + {$tenxiufee}")
                ]);
                DB::table('psubbalance')->where('paytype', $payType)->update([
                    'rmbpay' => DB::raw("rmbpay + {$tenxiufee}")
                ]);

                $platform = DB::table('platformbalance')->first();
                DB::table('psavingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $payType,
                    'username' => $addUser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiufee,
                    'currency' => 'RMB',
                    'abstract' => $chargeStr,
                    'newsem' => $platform->newsem,
                    'semincome' => $platform->semincome,
                    'rmbincome' => $platform->rmbincome,
                    'rmbpreincome' => $platform->rmbpreincome,
                    'rmbpay' => $platform->rmbpay,
                    'usdincome' => $platform->usdincome,
                    'usdpreincome' => $platform->usdpreincome,
                    'usdpay' => $platform->usdpay
                ]);
            }

            // Update payment table
            DB::table('yzwp_alipay')
                ->where('alipay_num', $outTradeNo)
                ->increment('paychecked');

            DB::commit();
            return true;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function donationTransactionAccounting($outTradeNo, $itemType, $payType, $total, $payUser, $sellUser)
    {
        if (empty($sellUser)) {
            return $this->donationPlatformTransactionAccounting(
                $outTradeNo,
                $itemType,
                $payType,
                $total,
                $payUser,
                $sellUser
            );
        }

        return $this->donationBrokerageTransactionAccounting(
            $outTradeNo,
            $itemType,
            $payType,
            $total,
            $payUser,
            $sellUser
        );
    }



    public function donationBrokerageTransactionAccounting($outTradeNo, $itemType, $payType, $total, $payUser, $sellUser)
    {
        $blogId = app()->getLocale(); // Replace with your "get_current_lang()" equivalent

        $addsavings = $total;
        $rationalRate = $this->getPlatRate(80001);
        $paytypeRate = $this->getPaytypeRate($payType);

        $tenxiufee = round($total * $paytypeRate, 2);
        $rationalFee = $total * $rationalRate;
        $sellAddSavings = $total - $tenxiufee - $rationalFee;

        $apipayFeeStr = " 支付手续费($tenxiufee) ";
        $rationalFeeStr = " 分成费($rationalFee)";

        $chargeStr = trim($itemType) . $this->getPaytypeName($payType) . $apipayFeeStr . $rationalFeeStr . "seller $sellUser buyer $payUser";
        $chargeStrPay = trim($itemType) . $this->getPaytypeName($payType) . $total . "seller $sellUser";

        $newTransactionNo = $this->getTransactionNewNo();

        DB::transaction(function () use ($sellUser, $payType, $sellAddSavings, $chargeStr, $rationalFee, $tenxiufee, $outTradeNo, $newTransactionNo) {
            // 1. Update seller account balance
            DB::table('account')
                ->where('username', $sellUser)
                ->increment('savingsRMB', $sellAddSavings);

            $rmbSavingArray = DB::table('account')
                ->where('username', $sellUser)
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->first();

            // 2. Insert savings log
            DB::table('savingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $sellAddSavings,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'savingsRMB' => $rmbSavingArray->savingsRMB,
                'savingsUSD' => $rmbSavingArray->savingsUSD,
                'savingsSEM' => $rmbSavingArray->savingsSEM,
            ]);

            // 3. Platform pre-income
            DB::table('platformbalance')->increment('rmbpreincome', $sellAddSavings);
            DB::table('psubbalance')->where('paytype', $payType)->increment('rmbpreincome', $sellAddSavings);

            $platformBalance = DB::table('platformbalance')->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $sellAddSavings,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay,
            ]);

            // 4. Rational fee income
            DB::table('platformbalance')->increment('rmbincome', $rationalFee);
            DB::table('psubbalance')->where('paytype', $payType)->increment('rmbincome', $rationalFee);

            $platformBalance = DB::table('platformbalance')->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $rationalFee,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay,
            ]);

            // 5. Payment fee
            if ($tenxiufee > 0) {
                DB::table('platformbalance')->increment('rmbpay', $tenxiufee);
                DB::table('psubbalance')->where('paytype', $payType)->increment('rmbpay', $tenxiufee);

                $platformBalance = DB::table('platformbalance')->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $payType,
                    'username' => $sellUser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiufee,
                    'currency' => 'RMB',
                    'abstract' => $chargeStr,
                    'newsem' => $platformBalance->newsem,
                    'semincome' => $platformBalance->semincome,
                    'rmbincome' => $platformBalance->rmbincome,
                    'rmbpreincome' => $platformBalance->rmbpreincome,
                    'rmbpay' => $platformBalance->rmbpay,
                    'usdincome' => $platformBalance->usdincome,
                    'usdpreincome' => $platformBalance->usdpreincome,
                    'usdpay' => $platformBalance->usdpay,
                ]);
            }

            // 6. Mark as checked in Alipay table
            DB::table('yzwp_alipay')
                ->where('alipay_num', $outTradeNo)
                ->increment('paychecked', 1);
        });

        return true;
    }


    function donationPlatformTransactionAccounting($out_trade_no, $item_type, $paytype, $total, $payuser, $selluser)
    {
        // Get current language (assuming you have a helper)
        $blog_id = $this->getCurrentLang();

        $addsavings = $total;

        // External helper for paytype rate
        $alipayrate = $this->getpaytypeRate($paytype);

        $tenxiufee = round($total * $alipayrate, 2);
        $selladdsavings = $total - $tenxiufee;

        $apipayfeeestr = " 支付手续费($tenxiufee) ";
        $rationalfeeestr = " 分成费(0)"; // Note: $rationalfee not defined in original

        $chargestr = trim($item_type) . $this->getpaytypeName($paytype) . $apipayfeeestr . $rationalfeeestr . "seller $selluser buyer $payuser";
        $chargestrpay = trim($item_type) . $this->getpaytypeName($paytype) . $total . "seller $selluser";

        $newtransactionno = $this->gettransactionnewno();

        DB::transaction(function () use ($paytype, $selladdsavings, $tenxiufee, $chargestr, $selluser, $out_trade_no, $newtransactionno) {
            // Update platformbalance (rmbincome)
            DB::table('platformbalance')
                ->update([
                    'rmbincome' => DB::raw("rmbincome + $selladdsavings")
                ]);

            // Update psubbalance (rmbincome)
            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->update([
                    'rmbincome' => DB::raw("rmbincome + $selladdsavings")
                ]);

            // Fetch platformbalance for logging
            $platformBalance = DB::table('platformbalance')->first();

            // Insert psavingslog for rmbincome
            DB::table('psavingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $selluser,
                'dtime' => Carbon::now(),
                'amount' => $selladdsavings,
                'currency' => 'RMB',
                'abstract' => $chargestr,
                'newsem' => $platformBalance->newsem,
                'semincome' => $platformBalance->semincome,
                'rmbincome' => $platformBalance->rmbincome,
                'rmbpreincome' => $platformBalance->rmbpreincome,
                'rmbpay' => $platformBalance->rmbpay,
                'usdincome' => $platformBalance->usdincome,
                'usdpreincome' => $platformBalance->usdpreincome,
                'usdpay' => $platformBalance->usdpay,
            ]);

            // If payment fee exists
            if ($tenxiufee > 0) {
                DB::table('platformbalance')
                    ->update([
                        'rmbpay' => DB::raw("rmbpay + $tenxiufee")
                    ]);

                DB::table('psubbalance')
                    ->where('paytype', $paytype)
                    ->update([
                        'rmbpay' => DB::raw("rmbpay + $tenxiufee")
                    ]);

                // Refresh platform balance
                $platformBalance = DB::table('platformbalance')->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newtransactionno,
                    'paytype' => $paytype,
                    'username' => $selluser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiufee,
                    'currency' => 'RMB',
                    'abstract' => $chargestr,
                    'newsem' => $platformBalance->newsem,
                    'semincome' => $platformBalance->semincome,
                    'rmbincome' => $platformBalance->rmbincome,
                    'rmbpreincome' => $platformBalance->rmbpreincome,
                    'rmbpay' => $platformBalance->rmbpay,
                    'usdincome' => $platformBalance->usdincome,
                    'usdpreincome' => $platformBalance->usdpreincome,
                    'usdpay' => $platformBalance->usdpay,
                ]);
            }

            // Update alipay table (WordPress table => now Laravel table)
            DB::table('yzwp_alipay')
                ->where('alipay_num', $out_trade_no)
                ->update([
                    'paychecked' => DB::raw("paychecked + 1")
                ]);
        });

        return true;
    }

    public function moneypayBrokerageTransactionAccounting($outTradeNo, $itemType, $payType, $total, $payUser, $sellUser)
    {
        $blogId = app('current_lang'); // Replace with your own language detection
        $addsavings = $total;

        // Get rates
        $rationalRate = $this->getPlatRate(80001);
        $alipayRate = $this->getPaytypeRate($payType);

        $tenxiuFee = round($total * $alipayRate, 2);
        $rationalFee = $total * $rationalRate;
        $sellAddSavings = $total - $tenxiuFee - $rationalFee;

        // Build text strings
        $apipayFeeStr = " 支付手续费($tenxiuFee) ";
        $rationalFeeStr = " 分成费($rationalFee)";
        $paytypeName = $this->getPaytypeName($payType);

        $chargeStr = trim($itemType) . $paytypeName . $apipayFeeStr . $rationalFeeStr . "seller $sellUser buyer $payUser";
        $preChargeStr = "预收" . trim($itemType) . $paytypeName . $apipayFeeStr . $rationalFeeStr . "seller $sellUser buyer $payUser";
        $raChargeStr = "分成" . trim($itemType) . $paytypeName . $apipayFeeStr . $rationalFeeStr . "seller $sellUser buyer $payUser";
        $chargeStrPay = trim($itemType) . $paytypeName . $total . "seller $sellUser";

        $newTransactionNo = $this->getTransactionNewNo();

        DB::transaction(function () use ($payUser, $sellUser, $payType, $addsavings, $sellAddSavings, $total, $chargeStr, $preChargeStr, $raChargeStr, $chargeStrPay, $rationalFee, $tenxiuFee, $outTradeNo, $newTransactionNo) {
            // Buyer savings log
            $buyerSavings = DB::table('account')->where('username', $payUser)->first();
            if ($buyerSavings) {
                DB::table('savingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $payType,
                    'username' => $payUser,
                    'dtime' => Carbon::now(),
                    'amount' => $addsavings,
                    'currency' => 'RMB',
                    'abstract' => $chargeStrPay,
                    'savingsRMB' => $buyerSavings->savingsRMB,
                    'savingsUSD' => $buyerSavings->savingsUSD,
                    'savingsSEM' => $buyerSavings->savingsSEM
                ]);
            }

            // Update seller account
            DB::table('account')
                ->where('username', $sellUser)
                ->increment('savingsRMB', $sellAddSavings);

            $sellerSavings = DB::table('account')->where('username', $sellUser)->first();
            DB::table('savingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $sellAddSavings,
                'currency' => 'RMB',
                'abstract' => $chargeStr,
                'savingsRMB' => $sellerSavings->savingsRMB,
                'savingsUSD' => $sellerSavings->savingsUSD,
                'savingsSEM' => $sellerSavings->savingsSEM
            ]);

            // Platform balance updates
            DB::table('platformbalance')->increment('rmbpreincome', $sellAddSavings);
            DB::table('psubbalance')->where('paytype', $payType)->increment('rmbpreincome', $sellAddSavings);

            $platformBal = DB::table('platformbalance')->first();
            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $sellAddSavings,
                'currency' => 'RMB',
                'abstract' => $preChargeStr,
                'newsem' => $platformBal->newsem,
                'semincome' => $platformBal->semincome,
                'rmbincome' => $platformBal->rmbincome,
                'rmbpreincome' => $platformBal->rmbpreincome,
                'rmbpay' => $platformBal->rmbpay,
                'usdincome' => $platformBal->usdincome,
                'usdpreincome' => $platformBal->usdpreincome,
                'usdpay' => $platformBal->usdpay
            ]);

            // Rational fee income
            DB::table('platformbalance')->increment('rmbincome', $rationalFee);
            DB::table('psubbalance')->where('paytype', $payType)->increment('rmbincome', $rationalFee);

            $platformBal = DB::table('platformbalance')->first();
            DB::table('psavingslog')->insert([
                'transactionno' => $newTransactionNo,
                'paytype' => $payType,
                'username' => $sellUser,
                'dtime' => Carbon::now(),
                'amount' => $rationalFee,
                'currency' => 'RMB',
                'abstract' => $raChargeStr,
                'newsem' => $platformBal->newsem,
                'semincome' => $platformBal->semincome,
                'rmbincome' => $platformBal->rmbincome,
                'rmbpreincome' => $platformBal->rmbpreincome,
                'rmbpay' => $platformBal->rmbpay,
                'usdincome' => $platformBal->usdincome,
                'usdpreincome' => $platformBal->usdpreincome,
                'usdpay' => $platformBal->usdpay
            ]);

            // Tenxiu fee (platform pay)
            if ($tenxiuFee > 0) {
                DB::table('platformbalance')->increment('rmbpay', $tenxiuFee);
                DB::table('psubbalance')->where('paytype', $payType)->increment('rmbpay', $tenxiuFee);

                $platformBal = DB::table('platformbalance')->first();
                DB::table('psavingslog')->insert([
                    'transactionno' => $newTransactionNo,
                    'paytype' => $payType,
                    'username' => $sellUser,
                    'dtime' => Carbon::now(),
                    'amount' => $tenxiuFee,
                    'currency' => 'RMB',
                    'abstract' => $chargeStr,
                    'newsem' => $platformBal->newsem,
                    'semincome' => $platformBal->semincome,
                    'rmbincome' => $platformBal->rmbincome,
                    'rmbpreincome' => $platformBal->rmbpreincome,
                    'rmbpay' => $platformBal->rmbpay,
                    'usdincome' => $platformBal->usdincome,
                    'usdpreincome' => $platformBal->usdpreincome,
                    'usdpay' => $platformBal->usdpay
                ]);
            }

            // Update alipay table equivalent
            DB::table('yzwp_alipay')
                ->where('alipay_num', $outTradeNo)
                ->increment('paychecked', 1);
        });

        return true;
    }


    function paysemansactionAccounting($abstract, $paytype, $total, $payuser)
    {
        $blog_id = $this->getCurrentLang(); // Use the class method for current language

        $chargestr = $this->getpaytypeName($paytype) . $abstract . $total . " SEM";
        $newtransactionno = $this->gettransactionnewno(); // Keep your existing helper

        DB::transaction(function () use ($paytype, $total, $payuser, $chargestr, $newtransactionno) {

            // Deduct from user's SEM balance
            DB::table('account')
                ->where('username', $payuser)
                ->update([
                    'savingsSEM' => DB::raw("savingsSEM - {$total}")
                ]);

            // Get updated balances
            $balances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $payuser)
                ->first();

            // Insert into savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $total,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'savingsRMB' => $balances->savingsRMB,
                'savingsUSD' => $balances->savingsUSD,
                'savingsSEM' => $balances->savingsSEM
            ]);

            // Update platformbalance semincome
            DB::table('platformbalance')->update([
                'semincome' => DB::raw("semincome + {$total}")
            ]);

            // Update psubbalance semincome for specific paytype
            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->update([
                    'semincome' => DB::raw("semincome + {$total}")
                ]);

            // Get updated platformbalance values
            $platformBalances = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            // Insert into psavingslog
            DB::table('psavingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $total,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'newsem' => $platformBalances->newsem,
                'semincome' => $platformBalances->semincome,
                'rmbincome' => $platformBalances->rmbincome,
                'rmbpreincome' => $platformBalances->rmbpreincome,
                'rmbpay' => $platformBalances->rmbpay,
                'usdincome' => $platformBalances->usdincome,
                'usdpreincome' => $platformBalances->usdpreincome,
                'usdpay' => $platformBalances->usdpay
            ]);
        });

        return true;
    }




    function bonussemansactionAccounting($abstract, $paytype, $total, $payuser)
    {
        $blog_id = $this->getCurrentLang();
        $chargestr = $this->getpaytypeName($paytype) . $abstract . $total . " SEM";
        $newtransactionno = $this->gettransactionnewno();

        DB::transaction(function () use ($paytype, $total, $payuser, $chargestr, $newtransactionno) {
            // Update user savingsSEM
            DB::table('account')
                ->where('username', $payuser)
                ->update(['savingsSEM' => DB::raw("savingsSEM + {$total}")]);

            // Fetch updated balances
            $balances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $payuser)
                ->first();

            // Insert savingslog
            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $total,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'savingsRMB' => $balances->savingsRMB,
                'savingsUSD' => $balances->savingsUSD,
                'savingsSEM' => $balances->savingsSEM,
            ]);

            // Update platformbalance and psubbalance newsem
            DB::table('platformbalance')->update([
                'newsem' => DB::raw("newsem + {$total}")
            ]);

            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->update([
                    'newsem' => DB::raw("newsem + {$total}")
                ]);

            // Get updated platformbalance row
            $platformBalances = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            // Insert psavingslog
            DB::table('psavingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $total,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'newsem' => $platformBalances->newsem,
                'semincome' => $platformBalances->semincome,
                'rmbincome' => $platformBalances->rmbincome,
                'rmbpreincome' => $platformBalances->rmbpreincome,
                'rmbpay' => $platformBalances->rmbpay,
                'usdincome' => $platformBalances->usdincome,
                'usdpreincome' => $platformBalances->usdpreincome,
                'usdpay' => $platformBalances->usdpay,
            ]);
        });

        return true;
    }


    function sempaybrokeragetransactionAccounting($abstract, $paytype, $total, $payuser, $selluser)
    {
        $blog_id = $this->getCurrentLang();

        $rrationalfee = $blog_id == 1 ? "分成费" : "rationalfee";
        $rpayfee = $blog_id == 1 ? " 支付 " : " Pay ";
        $rsellfee = $blog_id == 1 ? "卖提成" : "Sell Income";

        $addsavings = $total;
        $rationalrate = $this->getplatRate(80000);
        $rationalfee = round($total * $rationalrate, 2);
        $selladdsavings = $total - $rationalfee;

        $rationalfeeestr = "$rrationalfee($rationalfee)";
        $chargestr = $this->getpaytypeName($paytype) . "$abstract total $total SEM $rationalfeeestr SEM seller $selluser buyer $payuser";
        $chargestrpay = "$rpayfee $abstract total $total SEM seller $selluser";
        $chargestrsell = "$rsellfee $abstract total $total SEM buyer $payuser";

        $newtransactionno = $this->gettransactionnewno();

        DB::transaction(function () use ($paytype, $total, $payuser, $selluser, $addsavings, $selladdsavings, $rationalfee, $chargestr, $chargestrpay, $chargestrsell, $newtransactionno) {
            // Deduct from payuser
            DB::table('account')
                ->where('username', $payuser)
                ->update(['savingsSEM' => DB::raw("savingsSEM - {$total}")]);

            // Insert payuser savingslog
            $payerBalances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $payuser)
                ->first();

            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargestrpay,
                'savingsRMB' => $payerBalances->savingsRMB,
                'savingsUSD' => $payerBalances->savingsUSD,
                'savingsSEM' => $payerBalances->savingsSEM,
            ]);

            // Add to selluser
            DB::table('account')
                ->where('username', $selluser)
                ->update(['savingsSEM' => DB::raw("savingsSEM + {$selladdsavings}")]);

            // Insert selluser savingslog
            $sellerBalances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $selluser)
                ->first();

            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $selluser,
                'dtime' => Carbon::now(),
                'amount' => $selladdsavings,
                'currency' => 'SEM',
                'abstract' => $chargestrsell,
                'savingsRMB' => $sellerBalances->savingsRMB,
                'savingsUSD' => $sellerBalances->savingsUSD,
                'savingsSEM' => $sellerBalances->savingsSEM,
            ]);

            // Update platformbalance and psubbalance with rational fee
            DB::table('platformbalance')->update([
                'semincome' => DB::raw("semincome + {$rationalfee}")
            ]);

            DB::table('psubbalance')
                ->where('paytype', $paytype)
                ->update([
                    'semincome' => DB::raw("semincome + {$rationalfee}")
                ]);

            // Insert into psavingslog
            $platformBalances = DB::table('platformbalance')
                ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                ->first();

            DB::table('psavingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $selluser,
                'dtime' => Carbon::now(),
                'amount' => $rationalfee,
                'currency' => 'SEM',
                'abstract' => $chargestr,
                'newsem' => $platformBalances->newsem,
                'semincome' => $platformBalances->semincome,
                'rmbincome' => $platformBalances->rmbincome,
                'rmbpreincome' => $platformBalances->rmbpreincome,
                'rmbpay' => $platformBalances->rmbpay,
                'usdincome' => $platformBalances->usdincome,
                'usdpreincome' => $platformBalances->usdpreincome,
                'usdpay' => $platformBalances->usdpay,
            ]);
        });

        return true;
    }


    function semtransfertransactionAccounting($abstract, $paytype, $total, $payuser, $selluser)
    {
        $blog_id = $this->getCurrentLang();

        $rrationalfee = $blog_id == 1 ? "分成费" : "rationalfee";
        $rpayfee = $blog_id == 1 ? " 转出 " : " Transfer Out ";
        $rsellfee = $blog_id == 1 ? "转入" : "Transfer In";

        $addsavings = $total;
        $rationalrate = $this->getplatRate(81000);
        $rationalfee = round($total * $rationalrate, 2);
        $selladdsavings = $total - $rationalfee;

        $rationalfeeestr = $rationalfee > 0 ? "$rrationalfee($rationalfee)" : "";

        $chargestr = $this->getpaytypeName($paytype) . "$abstract total $total SEM $rationalfeeestr SEM transfer_in $selluser transfer_out $payuser";
        $chargestrpay = "$rpayfee $abstract total $total SEM transfer_in $selluser";
        $chargestrsell = "$rsellfee $abstract total $total SEM transfer_out $payuser";

        $newtransactionno = $this->gettransactionnewno();

        DB::transaction(function () use ($paytype, $total, $payuser, $selluser, $addsavings, $selladdsavings, $rationalfee, $chargestr, $chargestrpay, $chargestrsell, $newtransactionno) {
            // Deduct from payuser
            DB::table('account')
                ->where('username', $payuser)
                ->update(['savingsSEM' => DB::raw("savingsSEM - {$total}")]);

            // Insert payuser savingslog
            $payerBalances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $payuser)
                ->first();

            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $payuser,
                'dtime' => Carbon::now(),
                'amount' => $addsavings,
                'currency' => 'SEM',
                'abstract' => $chargestrpay,
                'savingsRMB' => $payerBalances->savingsRMB,
                'savingsUSD' => $payerBalances->savingsUSD,
                'savingsSEM' => $payerBalances->savingsSEM,
            ]);

            // Add to selluser
            DB::table('account')
                ->where('username', $selluser)
                ->update(['savingsSEM' => DB::raw("savingsSEM + {$selladdsavings}")]);

            // Insert selluser savingslog
            $sellerBalances = DB::table('account')
                ->select('savingsRMB', 'savingsUSD', 'savingsSEM')
                ->where('username', $selluser)
                ->first();

            DB::table('savingslog')->insert([
                'transactionno' => $newtransactionno,
                'paytype' => $paytype,
                'username' => $selluser,
                'dtime' => Carbon::now(),
                'amount' => $selladdsavings,
                'currency' => 'SEM',
                'abstract' => $chargestrsell,
                'savingsRMB' => $sellerBalances->savingsRMB,
                'savingsUSD' => $sellerBalances->savingsUSD,
                'savingsSEM' => $sellerBalances->savingsSEM,
            ]);

            if ($rationalfee > 0) {
                DB::table('platformbalance')->update([
                    'semincome' => DB::raw("semincome + {$rationalfee}")
                ]);

                DB::table('psubbalance')
                    ->where('paytype', $paytype)
                    ->update([
                        'semincome' => DB::raw("semincome + {$rationalfee}")
                    ]);

                $platformBalances = DB::table('platformbalance')
                    ->select('newsem', 'semincome', 'rmbincome', 'rmbpreincome', 'rmbpay', 'usdincome', 'usdpreincome', 'usdpay')
                    ->first();

                DB::table('psavingslog')->insert([
                    'transactionno' => $newtransactionno,
                    'paytype' => $paytype,
                    'username' => $selluser,
                    'dtime' => Carbon::now(),
                    'amount' => $rationalfee,
                    'currency' => 'SEM',
                    'abstract' => $chargestr,
                    'newsem' => $platformBalances->newsem,
                    'semincome' => $platformBalances->semincome,
                    'rmbincome' => $platformBalances->rmbincome,
                    'rmbpreincome' => $platformBalances->rmbpreincome,
                    'rmbpay' => $platformBalances->rmbpay,
                    'usdincome' => $platformBalances->usdincome,
                    'usdpreincome' => $platformBalances->usdpreincome,
                    'usdpay' => $platformBalances->usdpay,
                ]);
            }
        });

        return true;
    }


}
