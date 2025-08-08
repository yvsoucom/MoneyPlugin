<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencytypeTable extends Migration
{
    public function up()
    {
        Schema::create('moneyplugin_currencytype', function (Blueprint $table) {
            $table->id();
            $table->string('currency_name', 100);
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('moneyplugin_currencytype');
    }
}
