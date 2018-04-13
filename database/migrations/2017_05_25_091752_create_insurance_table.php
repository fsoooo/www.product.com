<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsuranceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //产品详情表
        Schema::create('insurance', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150)->comment('保险产品名称');
            $table->string('display_name')->comment('保险产品简称');
//            $table->string('p_code', 200)->unique()->comment('产品外部唯一编码');
//            $table->string('private_p_code', 200)->unique()->comment('产品内部唯一编码');
            $table->text('content')->comment('保险产品说明');
            $table->integer('category_id')->comment('分类ID');
            $table->integer('company_id')->comment('公司ID');
            $table->integer('min_math')->comment('最小投保人数')->nullable();
            $table->integer('max_math')->comment('最大投保人数')->nullable();
//            $table->string('api_from_uuid')->comment('接口来源唯一标识（接口类名称）');
//            $table->enum('pay_type', ['online', 'bank_card', 'offline'])->comment('支付方式')->nullable();
//            $table->integer('brokerage')->comment('代理公司佣金比例比例');
            $table->string('type', 100)->comment('保险产品类型')->nullable();
//            $table->integer('insurance_type')->comment('保险个、团标识：1个险、2团险');
            $table->enum('status', ['on', 'off'])->default('on')->comment('状态');
            $table->tinyInteger('sell_status')->comment('可售状态 0配置 1测试 2可售')->defalut(0);
            $table->string('base_price')->comment('基础保费')->nullable();
            $table->string('base_stages_way')->default('0年')->comment('基础佣金比缴别')->nullable();
            $table->integer('base_ratio')->comment('基础佣金比')->nullable();
//            $table->string('insure_resourse')->comment('产品资源')->nullable();
//            $table->integer('health')->comment('有无健康告知')->default(0);
//            $table->text('health_notice')->comment('健康告知内容')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        //产品条款关联表
        Schema::create('insurance_clause', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('insurance_id')->unsigned();
            $table->integer('clause_id')->unsigned();
            $table->string('coverage_bs')->comment('保额倍数1,保额倍数2,保额倍数3...')->nullable();
            $table->engine = 'InnoDB';
        });
        //产品健康告知关联表
        Schema::create('insurance_health', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('insurance_id')->comment('产品ID');
            $table->text('content')->comment('健康告知内容');
            $table->integer('order')->comment('显示顺序');
            $table->string('condition')->comment('限制条件');
            $table->string('condition_value')->comment('限制条件值');
            $table->string('checked')->comment('默认选中值');
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
        Schema::dropIfExists('insurance');
        Schema::dropIfExists('insurance_clause');
        Schema::dropIfExists('insurance_health');
    }
}