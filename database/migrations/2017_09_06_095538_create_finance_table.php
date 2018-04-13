<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ins_order_finance', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->integer('order_id')->comment('订单ID');
            $table->integer('insurance_id')->comment('保险ID');
            $table->integer('api_from_id')->comment('api来源ID');
            $table->integer('brokerage_id')->comment('佣金比例id');
            $table->string('union_order_code')->comment('合并订单号')->nullable();
            $table->string('p_code', 255)->comment('外部产品码');
            $table->string('private_p_code', 255)->comment('内部产品码');
            $table->string('brokerage_for_us')->comment('内部所获佣金值');
            $table->string('brokerage_for_agency')->comment('给予代理佣金值');
            $table->integer('us_settlement_status')->comment('内部结算状态 0未结算 1结算中 2已结算 3结算失败')->default(0);
            $table->integer('agency_settlement_status')->comment('渠道结算状态 0未结算 1结算中 2已结算 3结算失败')->default(0);
            $table->string('us_settlement_error_message')->comment('结算失败原因')->nullable();
            $table->string('agency_settlement_error_message')->comment('结算失败原因')->nullable();
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
        Schema::dropIfExists('ins_order_finance');
    }
}
