<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionThirdStepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_third_step', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('has_ten_percent_people')->comment('是否没有实体或个人持有投保公司10%以上的股票');
            $table->tinyInteger('change_staff')->comment('投保公司是否没有在过去的三年中更换过审计师， 外部律师或外部证券律师');
            $table->tinyInteger('know_compensation_info')->comment('投保公司或其任何董事或高级职员是否在经过询问，知晓任何有可能引起保险单项下的索赔的行动、疏忽、事件或情形');
            $table->tinyInteger('has_criminal')->comment('投保公司或其任何董事或高级职员是否因讳反任何可使用的证券法律或法规而导致的任何民生、刑事或行政程序');
            $table->tinyInteger('has_litigation')->comment('投保公司或其任何董事或高级职员是否与任何代表诉讼、集体诉讼或衍生诉讼事件有关');
            $table->tinyInteger('has_stock')->comment('投保公司是否在过去或未来的12个月中已经或将要发行股票(普通股或其他)');
            $table->tinyInteger('has_reduce_staff')->comment('投保公司在过去的12个月里是否有裁员');
            $table->tinyInteger('reduce_staff_plan')->comment('投保公司在未来的12个月里是否有裁员计划');
            $table->tinyInteger('has_human_resources')->comment('投保公司是否没有人力资源部门');
            $table->tinyInteger('has_staff_manual')->comment('投保公司没有向所有雇员发布的员工手册');
            $table->tinyInteger('manual_update')->comment('员工手册是否一至两年都没有更新一次');
            $table->tinyInteger('has_report')->comment('投保公司对其所投资的公司的董事及高管没有书面的性骚扰及报告程序');
            $table->tinyInteger('has_assessment')->comment('投保公司对其所投资的公司的董事及高管没有书面的书面测评');
            $table->tinyInteger('code_of_conduct')->comment('投保公司对其所投资的公司的董事及高管没有书面的行为守则');
            $table->tinyInteger('fire_policy')->comment('投保公司对其所投资的公司的董事及高管没有书面的解雇政策');
            $table->tinyInteger('hire_policy')->comment('投保公司对其所投资的公司的董事及高管没有书面的聘用政策');
            $table->tinyInteger('vacation_policy')->comment('投保公司没有家庭因素申请休假或病假政策');
            $table->tinyInteger('discriminate_against')->comment('投保公司没有雇佣歧视及报告程序');
            $table->tinyInteger('has_country_supervision')->comment('投保公司或其所投资的公司是否不涉及被国家环境监管的项目');
            $table->tinyInteger('has_board_approval')->comment('投保公司没有经过董事会批准的书面的环境政策');
            $table->tinyInteger('has_committees')->comment('投保公司没成立监察环境政策的委员会');
            $table->tinyInteger('has_auditing_program')->comment('投保公司没有履行正式的审计程序以确保环境政策的合规性');
            $table->tinyInteger('pollution_restitution')->comment('投保公司没有意识到目前存在环境污染的情形，并且公司正在或将要对此进行赔偿');
            $table->tinyInteger('agree_obligations')->comment('同意义务');
            $table->string('question_code',20)->nullable()->comment('唯一code码');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_third_step');
    }
}
