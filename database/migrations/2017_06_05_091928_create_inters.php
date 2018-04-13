<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('inters', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->string('name',100)->comment('接口名');
            $table->string('token',30)->comment('接口token');
            $table->string('company_id',11)->comment('公司ID');
            $table->integer('price')->default('5')->comment('接口价格');
            $table->integer('money')->default('0')->comment('金额');
            $table->integer('status')->default('0')->comment('状态,0,未开通,1,正常,2,欠费');
            $table->integer('is_pay')->default('0')->comment('支付状态');
            $table->integer('inter_nums')->default('0')->comment('接口调用次数');
//            $table->string('create_time')->comment('token创建时间');
//            $table->rememberToken();
            $table->timestamps();
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
        Schema::dropIfExists('inters');
    }

}
