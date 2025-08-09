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
 
namespace Plugins\MoneyPlugin\src\Models;

use Illuminate\Database\Eloquent\Model;

class PSavingsLog extends Model
{
    /**
     * Table name
     */
    protected $table = 'MoneyPlugin_psavingslog';

    /**
     * Primary key
     */
    protected $primaryKey = 'transactionno';
    public $incrementing = false; // not auto-increment
    protected $keyType = 'int';

    /**
     * No default Laravel timestamps
     */
    public $timestamps = false;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'transactionno',
        'paytype',
        'userid',
        'dtime',
        'amount',
        'currency',
        'abstract',
        'income',
        'preincome',
        'pay',
        'new',
    ];

    /**
     * Casts for proper data types
     */
    protected $casts = [
        'dtime' => 'datetime',
        'amount' => 'float',
        'income' => 'float',
        'preincome' => 'float',
        'pay' => 'float',
        'new' => 'float',
    ];
}
