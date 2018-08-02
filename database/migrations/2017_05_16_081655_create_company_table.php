<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('保险公司全称');
            $table->string('display_name', 150)->comment('保险公司简称');
            $table->integer('category_id')->comment('分类ID');
            $table->string('code', 100)->comment('保险公司代码');
            $table->string('logo')->comment('保险公司LOGO');
            $table->string('bank_type', 100)->comment('账号银行卡类型')->nullable();
            $table->string('bank_num', 100)->comment('银行卡账号')->nullable();
            $table->string('email', 100)->comment('保险公司邮箱')->nullable();
            $table->string('url', 150)->comment('保险公司链接');
            $table->string('phone', 100)->comment('保险公司电话');
            $table->string('code_img', 150)->comment('保险公司公众号二维码')->nullable();
            $table->integer('status')->default('0')->comment('状态，0正常，1删除');
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
        Schema::dropIfExists('company');
    }
}
