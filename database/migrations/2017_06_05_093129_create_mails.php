<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('mails', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->integer('send_id')->comment('发件人ID');
            $table->integer('receive_id')->comment('收件人ID');
            $table->string('title',100)->comment('标题');
            $table->text('content')->comment('内容');
            $table->integer('status')->default('0')->comment('状态');
            $table->integer('type')->default('0')->comment('类型');
//            $table->integer('create_time')->comment('创建时间');
            $table->integer('delete_id')->default('0')->comment('删除');
          
//            $table->rememberToken();
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
        Schema::dropIfExists('mails');
    }

}
