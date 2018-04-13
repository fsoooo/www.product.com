<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictGenesValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restrict_genes_values', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value', 255)->nullable()->comment('选项值');
            $table->string('ty_value', 255)->nullable()->comment('内部value，统一对外显示用');
            $table->string('name', 255)->comment('选项名称');
            $table->tinyInteger('type', false, true)->comment('类型');
            $table->integer('min', false, true)->nullable()->comment('最小值');
            $table->integer('max', false, true)->nullable()->comment('最大值');
            $table->integer('step', false, true)->nullable()->comment('步长');
            $table->string('unit', 255)->nullable()->comment('单位');
            $table->integer('rid', false, true)->comment('试算因子ID');
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
        Schema::dropIfExists('restrict_genes_values');
    }
}
