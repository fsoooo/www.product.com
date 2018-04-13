<?php
/**
 * Created by FangYuTing.
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionSecondStep extends Model{

    protected $table="question_second_step";

    protected $fillable = [
        'leave_count', 'recognize_company_name', 'high_position_count', 'recognize_country',
        'recognize_province', 'recognize_city', 'insured_count', 'insured_money',
        'insured_rate', 'stock_transact', 'transact_name', 'question_code',
    ];
}
