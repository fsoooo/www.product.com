<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 100)->unique()->nullable();
            $table->string('password');
            $table->string('account_id', 100)->unique()->comment('接口ID');
            $table->string('sign_key')->comment('接口密钥');
            $table->string('call_back_url')->comment('异步回调地址');
            $table->integer('sell_status')->default(2)->comment('产品同步码');
            $table->string('code')->comment('企业代码')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
