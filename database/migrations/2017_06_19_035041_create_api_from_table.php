<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFromTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('api_from', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique()->comment('接口来源名称');
            $table->string('uuid')->unique()->comment('唯一标识');
//            $table->text('count_api')->nullable()->comment('算费接口API');
//            $table->text('hebao_api')->nullable()->comment('核保查询API');
//            $table->text('toubao_api')->nullable()->comment('投保API');
//            $table->text('pay_api')->nullable()->comment('支付API');
//            $table->text('query_api')->nullable()->comment('查询API');
//            $table->text('issue_api')->nullable()->comment('出单API');
            $table->enum('status', ['on', 'off'])->default('on')->comment('状态');
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
        //
        Schema::dropIfExists('api_from');
    }
}
