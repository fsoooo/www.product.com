<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('email_infos', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->string('send',100)->comment('发送方');
            $table->string('receive',100)->comment('接收方');
            $table->string('title',100)->comment('邮件标题');
            $table->string('file',100)->default('null')->comment('邮件附件');
            $table->text('content')->comment('内容');
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
        Schema::dropIfExists('email_infos');
    }
}
