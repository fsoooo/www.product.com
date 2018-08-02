<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMsgOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('msg_orders', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->string('order_num',15)->comment('订单号');
            $table->string('name',100)->comment('接口名');
            $table->string('company',100)->comment('公司');
            $table->string('money',1000)->comment('金额');
            $table->string('pay_type',100)->comment('支付方式');
            $table->integer('is_pay')->comment('支付状态');
            $table->integer('delete_id')->default('0')->comment('删除');
//            $table->integer('create_time')->comment('创建时间');
            
          
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
        Schema::dropIfExists('msg_orders');
    }
}
