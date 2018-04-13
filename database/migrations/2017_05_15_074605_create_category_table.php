<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('分类名称');
            $table->integer('pid')->default(0)->comment('父类ID');
            $table->integer('sort')->default(0)->comment('权重');
            $table->string('slug', 100)->comment('唯一标识缩率名');
            $table->string('path')->default(',0,')->comment('父类路径');
            $table->enum('status', ['on', 'off'])->default('on')->comment('状态');
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
        Schema::dropIfExists('category');
    }
}
