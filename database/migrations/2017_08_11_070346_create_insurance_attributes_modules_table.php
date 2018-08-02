<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsuranceAttributesModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_attributes_modules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bind_id', false, true)->comment('产品和API来源中间表ID');
            $table->string('name', 255)->comment('模块名称');
            $table->string('module_key', 255)->nullable()->comment('模块');
            $table->string('remark', 255)->nullable()->comment('模块说明');
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
        Schema::dropIfExists('insurance_attributes_modules');
    }
}
