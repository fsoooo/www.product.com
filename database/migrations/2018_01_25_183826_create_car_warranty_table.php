<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarWarrantyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_warranty', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id')->comment('订单id');
            $table->string('order_no')->comment('订单号');
            $table->string('total_premium')->comment('总保费')->nullable();
            $table->string('out_order_id')->comment('外部订单号');
            $table->string('ci_policy_no')->comment('交强险保单号');
            $table->string('bi_policy_no')->comment('商业险保单号');
            $table->text('options')->content('保障明细');
            $table->integer('ci_begin_date')->comment('交强险起保期');
            $table->integer('ci_end_date')->comment('交强险失效期');
            $table->integer('bi_begin_date')->comment('商业险起保期');
            $table->integer('bi_end_date')->comment('商业险失效期');
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
        Schema::dropIfExists('car_warranty');
    }
}
