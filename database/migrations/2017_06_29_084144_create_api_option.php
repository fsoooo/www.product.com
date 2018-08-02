<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_option', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_from_uuid')->comment('API来源的唯一标识');
            $table->string('name')->comment('名称');
            $table->string('number')->comment('编码');
            $table->string('code')->comment('代号');
            $table->enum('type', ['bank', 'profession', 'relationship', 'card_type'])->comment('类型 包括银行、职业、亲属关系等');

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
        Schema::dropIfExists('api_option');
    }
}
