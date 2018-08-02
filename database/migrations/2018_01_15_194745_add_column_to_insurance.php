<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToInsurance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('insurance', function ($table) {
            $table->string('first_date')->comment('最小起保日期');
            $table->string('latest_date')->comment('最大起保日期');
            $table->string('observation_period')->comment('观察期');
            $table->string('period_hesitation')->comment('犹豫期');
            $table->string('insure_resourse')->comment('产品资源')->nullable();
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
    }
}
