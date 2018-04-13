<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionSecondStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_second_step', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_count')->comment('所投公司离职董事及高管总数');
            $table->string('recognize_company_name',50)->nullable()->comment('所投公司名称');
            $table->integer('high_position_count')->comment('所投公司董事及高管人数');
            $table->string('recognize_country',20)->nullable()->comment('所投公司所在国家');
            $table->string('recognize_province',20)->nullable()->comment('所投公司所在地区');
            $table->string('recognize_city',20)->nullable()->comment('所投公司所在城市');
            $table->integer('insured_count')->comment('最近所投该公司轮次');
            $table->integer('insured_money')->comment('最近该轮次所投金额');
            $table->integer('insured_rate')->comment('最近所投该公司轮次持股比例');
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
        Schema::dropIfExists('question_second_step');
    }
}
