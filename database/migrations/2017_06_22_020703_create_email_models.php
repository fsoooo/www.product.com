<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailModels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('email_models', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->integer('model_id')->comment('模板编码');
            $table->string('model_name')->comment('模板名称');
            $table->text('content')->comment('模板内容');
            $table->integer('status')->comment('状态');
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
        Schema::dropIfExists('email_models');
    }
}
