<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('sms_infos', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->string('company_id')->comment('公司ID');
            $table->string('send_phone',1000)->comment('电话');
            $table->string('content',1000)->comment('内容');
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
        Schema::dropIfExists('sms_infos');
    }
}
