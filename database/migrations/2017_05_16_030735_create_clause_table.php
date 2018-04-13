<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClauseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clause', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('条款名称');
            $table->string('display_name')->comment('条款简称');
            $table->text('content')->comment('条款说明');
            $table->integer('category_id')->comment('分类ID');
            $table->integer('company_id')->comment('公司ID');
            $table->string('clause_code', 100)->comment('险别代码');
            $table->string('type', 100)->comment('条款类型');
//            $table->string('coverage_bs')->default(0)->comment('保额倍数1,保额倍数2,保额倍数3...')->nullable();
            $table->text('file_url')->comment('附件路径');
            $table->enum('status', ['on', 'off'])->default('on')->comment('状态');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        //first submit test
        Schema::create('clause_duty', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clause_id')->unsigned();
            $table->integer('duty_id')->unsigned();
            $table->integer('ins_id')->default(0)->comment('产品ID');
            $table->string('coverage_jc')->comment('基础保额');
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
        Schema::dropIfExists('clause');
        Schema::dropIfExists('clause_duty');
    }
}
