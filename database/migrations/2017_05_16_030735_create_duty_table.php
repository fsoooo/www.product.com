<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDutyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('duty', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->comment('责任名称');
            $table->text('description')->comment('责任描述');
            $table->text('detail')->nullable()->comment('责任详情');
            $table->integer('category_id')->comment('分类ID');
            $table->string('type',100)->comment('类型');
            $table->integer('need_coverage')->comment('是否需要设置保额 1需要 0 不需要')->default(1);
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
        Schema::dropIfExists('duty');
    }
}
