<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsBrokerage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ins_api_brokerage', function (Blueprint $table) {
            $table->increments('id')->comment('主键id,自增');
            $table->integer('insurance_id')->comment('保险ID');
            $table->integer('api_from_id')->comment('api来源ID');
            $table->integer('bind_id')->comment('产品-API来源关联表ID');
            $table->string('p_code', 255)->comment('外部产品码');
            $table->string('private_p_code', 255)->comment('产品内部编码');
            $table->integer('by_stages_way')->comment('缴费形式 0-趸缴')->default(0);
            $table->string('pay_type_unit', 5)->comment('缴费形式单位')->default('年');
            $table->string('ratio_for_us')->comment('内部所获佣金比');
            $table->string('ratio_for_agency')->comment('对外给予佣金比');
            $table->string('status', 20)->comment('状态0关闭 1激活')->default(1);
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
        Schema::dropIfExists('ins_api_brokerage');
    }
}
