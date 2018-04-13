<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsuranceAttributesValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_attributes_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value', 255)->comment('属性值名称');
            $table->string('ty_value', 255)->nullable()->comment('内部value，统一对外显示用');
            $table->string('control_value', 255)->comment('api接口请求参数名');
            $table->tinyInteger('conditions', false, true)->comment('约束条件');
            $table->string('regex', 255)->nullable()->comment('属性值校验正则表达式');
            $table->string('remind', 255)->nullable()->comment('正则约束条件验证失败提示');
            $table->string('error_remind', 255)->nullable()->comment('出错提醒信息');
            $table->tinyInteger('attribute_type', false, true)->nullable()->comment('所限制属性的控件类型');
            $table->tinyInteger('unit', false, true)->default(0)->comment('单位 ');
            $table->integer('aid', false, true)->comment('属性ID');
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
        Schema::dropIfExists('insurance_attributes_values');
    }
}
