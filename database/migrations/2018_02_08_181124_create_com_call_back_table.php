<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComCallBackTable extends Migration
{
    /**
     * Run the migrations.
     * 回调临时表
     * @return void
     */
    public function up()
    {
        Schema::create('ins_order_finance', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->integer('ty_product_id')->comment('产品ID');
            $table->string('union_order_code')->comment('联合订单号')->nullable();
            $table->string('type')->comment('回调类型');//支付回调、签约回调
            $table->text('return_data')->comment('回调返回数据');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_back');
    }
}
