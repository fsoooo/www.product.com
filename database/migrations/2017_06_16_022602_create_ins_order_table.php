<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInsOrderTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        //保险订单表
        Schema::create('ins_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->comment('订单号');
            $table->string('pay_code')->comment('支付流水号 部分接口需要')->nullable();
            $table->string('union_order_code')->comment('合并订单号')->nullable();
            $table->string('create_account_id')->comment('经代理公司account_id');
            $table->string('api_from_uuid')->comment('接口来源码');
            $table->string('api_from_id')->comment('接口来源id');
            $table->integer('ins_id')->comment('产品id');
            $table->integer('bind_id')->comment('产品-api 关联ID');
            $table->string('p_code')->comment('外部产品码');
            $table->string('by_stages_way')->comment('缴费分期形式 0趸交');
            $table->string('total_premium')->comment('总保费')->nullable();
            $table->integer('p_num')->default(1)->comment('产品数量');
            $table->string('insured_num')->default(1)->comment('被保人数量');
            $table->text('buy_options')->comment('算费选择项、投保信息 json');
//            $table->string('income')->comment('根据佣金比所获收益')->nullable();
            $table->enum('status', ['check_ing', 'check_error', 'pay_ing', 'pay_end', 'pay_error', 'send_back_ing', 'send_back_error', 'send_back_end', 'close'])->default('check_ing')->comment('状态');
            $table->integer('policy_status', false, true)->default(3)->comment('保单状态 1-核保－提交资料 2-撤单 3-核保－核保中 4-核保－核保失败 5-核保－核保成功 6-承保－承保成功未生效 7-生效 8-失效');
            $table->timestamp('pay_time')->comment('支付时间')->nullable();
            $table->timestamp('start_time')->comment('生效时间')->nullable();
            $table->timestamp('end_time')->comment('失效时间')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->index(['order_no', 'create_account_id']);
        });

        //保险订单投保人信息表
        Schema::create('ins_order_policy_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_order_id')->default(0)->comment('订单表ID');
            $table->string('union_order_code')->comment('合并订单号');
            $table->string('name')->comment("投保人姓名");
            $table->enum('sex',['男', '女'])->comment('性别')->nullable();
            $table->string('phone')->comment("手机号");
            $table->string('card_type')->comment("证件类型")->default('1');
            $table->string('card_id')->comment("证件号");
            $table->string('birthday')->comment("出生日期")->nullable();
            $table->string('address')->comment("地址")->nullable();
            $table->string('email')->comment("邮箱")->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        //保险订单被保人信息表
        Schema::create('ins_order_insure_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ins_order_id')->default(0)->comment('订单表ID');
            $table->string('out_order_no')->comment('被保人单号')->nullable();
            $table->string('member_id')->comment('会员编号 泰康理赔时用')->nullable();
            $table->string('union_order_code')->comment('合并订单号');
            $table->string('ins_policy_code')->comment('保险保单号')->nullable();
            $table->string('e_policy_url')->comment('电子保单地址')->nullable();
            $table->string('p_code')->comment('产品码');
            $table->string('name')->comment("被投保人姓名");
            $table->enum('sex',[1, 0])->comment('性别 1男 0女')->nullable();
            $table->string('phone')->comment("手机号");
            $table->string('card_type')->comment("证件类型")->default('1');
            $table->string('card_id')->comment("证件号");
            $table->string('birthday')->comment("出生日期")->nullable();
            $table->string('address')->comment("地址")->nullable();
            $table->string('email')->comment("邮箱")->nullable();
            $table->string('relation')->comment('投保人与被保人关系');
            $table->string('coverage')->comment('保额')->nullable();
            $table->string('premium')->comment('保费')->nullable();
            $table->enum('status', ['check_ing', 'check_error', 'pay_ing', 'pay_end', 'pay_error', 'send_back_ing', 'send_back_error', 'send_back_end', 'close'])->default('check_ing')->comment('状态');
            $table->integer('policy_status', false, true)->default(3)->comment('保单状态 1-核保－提交资料 2-撤单 3-核保－核保中 4-核保－核保失败 5-核保－核保成功 6-承保－承保成功未生效 7-生效 18-失效');
            $table->string('ins_start_time')->comment('生效时间')->nullable();
            $table->string('ins_end_time')->comment('结束时间')->nullable();
            $table->string('check_error_message')->comment('投保失败原因')->nullable();
            $table->string('send_back_error_message')->comment('退保失败原因')->nullable();
            $table->string('pay_error_message')->comment('支付失败原因')->nullable();
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
        Schema::dropIfExists('ins_order');
        Schema::dropIfExists('ins_order_policy_info');
        Schema::dropIfExists('ins_order_insure_info');
    }
}
