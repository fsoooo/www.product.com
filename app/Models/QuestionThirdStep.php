<?php
/**
 * Created by FangYuTing.
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionThirdStep extends Model{

    protected $table="question_third_step";

    protected $fillable = [
        'has_ten_percent_people',
        'change_staff',
        'know_compensation_info',
        'has_criminal',
        'has_litigation',
        'has_stock',
        'has_reduce_staff',
        'reduce_staff_plan',
        'has_human_resources',
        'has_staff_manual',
        'manual_update',
        'has_report',
        'has_assessment',
        'code_of_conduct',
        'fire_policy',
        'hire_policy',
        'vacation_policy',
        'discriminate_against',
        'has_country_supervision',
        'has_board_approval',
        'has_committees',
        'has_auditing_program',
        'pollution_restitution',
        'agree_obligations',
        'question_code',
    ];
}
