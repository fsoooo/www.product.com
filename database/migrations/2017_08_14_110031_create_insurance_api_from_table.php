<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsuranceApiFromTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_api_from', function (Blueprint $table) {
            $table->increments('id');
            $table->string('private_p_code', 255)->comment('产品内部编码');
            $table->string('p_code', 255)->comment('产品编码');
            $table->string('template_url', 255)->comment('团险模板');
            $table->tinyInteger('status', false, true)->comment('绑定状态');
            $table->integer('insurance_id', false, true)->comment('保险ID');
            $table->integer('api_from_id', false,true)->comment('API来源ID');
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
        Schema::dropIfExists('insurance_api_from');
    }
}
