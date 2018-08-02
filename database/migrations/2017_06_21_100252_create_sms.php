<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sms', function (Blueprint $table){
            $table->increments('id')->comment('主键id,自增');
            $table->integer('num')->comment('剩余条数');
            $table->integer('send_num')->comment('发送条数');
            $table->integer('notice_moiney')->comment('余额不足提醒金额');
            $table->integer('money')->comment('金额');
            $table->engine = 'InnoDB';
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('sms');
    }
}
