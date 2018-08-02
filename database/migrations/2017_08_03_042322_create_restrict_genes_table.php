<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestrictGenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restrict_genes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 255)->nullable()->comment('试算因子对应属性key');
            $table->string('ty_key', 255)->nullable()->comment('内部key，统一对外显示用');
            $table->string('name', 255)->comment('试算因子名称');
            $table->string('default_value', 255)->nullable()->comment('默认值');
            $table->tinyInteger('type', false, true)->comment('html类型');
            $table->tinyInteger('display', false, true)->default(1)->comment('是否展示');
            $table->tinyInteger('sort', false, true)->nullable()->default(0)->comment('展示顺序');
            $table->integer('bind_id',false, true)->comment('产品与API来源中间表ID');
            $table->integer('clause_id',false, true)->comment('关联条款ID');
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
        Schema::dropIfExists('restrict_genes');
    }
}
