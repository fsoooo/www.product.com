<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionFirstStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_first_step', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name',50)->nullable()->comment('公司名称');
            $table->string('website',100)->nullable()->comment('公司网址');
            $table->string('sign_up_address',200)->nullable()->comment('公司注册地');
            $table->string('address',200)->nullable()->comment('公司地址');
            $table->date('start_date')->nullable()->comment('公司经营起始时间');
            $table->string('nature',20)->nullable()->comment('公司营业性质');
            $table->tinyInteger('has_stock')->comment('是否有股票在公开市场上发行，0：是，1：否');
            $table->integer('stock_num')->comment('股票发行总数');
            $table->integer('stock_rate')->comment('流通股比率(百分制)');
            $table->string('stock_transact',20)->nullable()->comment('在哪个证券交易所上市');
            $table->string('transact_name',50)->nullable()->comment('交易所名称');
            $table->string('transact_image',200)->nullable()->comment('美国证券交易补充投保书');
            $table->string('stock_code',50)->nullable()->comment('股票代码');
            $table->string('detail_nature',50)->nullable()->comment('公司营业性质（其他时写入）');
            $table->string('contact_name',50)->nullable()->comment('联系人姓名');
            $table->string('contact_phone',20)->nullable()->comment('联系人电话');
            $table->string('question_code',20)->nullable()->comment('唯一code码');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_first_step');
    }
}
