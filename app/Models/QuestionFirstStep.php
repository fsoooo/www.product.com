<?php
/**
 * Created by FangYuTing.
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class QuestionFirstStep extends Model{

    protected $table="question_first_step";

    protected $fillable = [
        'company_name', 'website', 'sign_up_address', 'address', 'start_date', 'nature',
        'has_stock', 'stock_num', 'stock_rate', 'stock_transact', 'transact_name',
        'transact_image', 'stock_code', 'detail_nature', 'contact_name', 'contact_phone',
        'question_code',
    ];
}
