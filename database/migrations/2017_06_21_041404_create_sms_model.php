<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('sms_models', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->string('model_id')->comment('模板标识码');
            $table->string('model_name')->comment('模板名称');
            $table->text('content')->comment('内容');
            $table->integer('status')->default('1')->comment('模板状态');
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
        Schema::dropIfExists('sms_models');
    }
}
