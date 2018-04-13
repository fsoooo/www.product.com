<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyInsOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
                      alter table `com_ins_order` MODIFY api_from_uuid VARCHAR(200) NULL  COMMENT '接口来源码',
                      MODIFY api_from_id VARCHAR(200) NULL  COMMENT '接口来源id',
                      MODIFY ins_id VARCHAR(200) NULL  COMMENT '产品id',
                      MODIFY bind_id VARCHAR(200) NULL  COMMENT '产品-api 关联ID',
                      MODIFY p_code VARCHAR(200) NULL  COMMENT '外部产品码',
                      MODIFY by_stages_way VARCHAR(200) NULL  COMMENT '缴费分期形式0趸交',
                      ADD order_type INT DEFAULT 1 COMMENT '订单类型 1非车 11非车团 2车险'
        ;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
