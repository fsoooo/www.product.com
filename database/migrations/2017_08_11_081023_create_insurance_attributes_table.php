<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsuranceAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255)->nullable()->comment('属性名称');
            $table->string('api_name', 255)->nullable()->comment('api接口请求参数名');
            $table->string('ty_key', 255)->nullable()->comment('内部key，统一对外显示用');
            $table->tinyInteger('type', false, true)->comment('属性类型');
            $table->string('regex', 255)->nullable()->comment('属性校验正则表达式');
            $table->string('default_remind', 255)->nullable()->comment('默认提醒信息');
            $table->string('error_remind', 255)->nullable()->comment('出错提醒信息');
            $table->tinyInteger('required', false, true)->default(1)->comment('是否必填 ');
            $table->integer('mid', false, true)->comment('模块ID');
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
        Schema::dropIfExists('insurance_attributes');
    }
}
