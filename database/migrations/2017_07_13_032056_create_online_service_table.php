<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOnlineServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('online_services', function (Blueprint $table) {
            $table->increments('id')->comment('主键');
            $table->string('name')->comment('名称');
            $table->string('number')->comment('QQ号码');
            $table->integer('status')->default('0')->comment('状态');
            $table->string('real_name')->comment('真实姓名');
            $table->string('card_id')->comment('身份证号');
            $table->string('phone')->comment('联系方式');
            $table->string('datas')->comment('客服资料');
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
        Schema::dropIfExists('online_services');
    }
}
