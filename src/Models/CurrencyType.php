<?php

namespace Plugins\MoneyPlugin\src\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyType extends Model
{
    protected $table = 'moneyplugin_currencytype';

    protected $fillable = ['currency_name', 'remark'];
}
