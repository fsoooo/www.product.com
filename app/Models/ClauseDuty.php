<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClauseDuty extends Model
{
    //
    protected $table = 'clause_duty';
    //关联责任
    public function tariff()
    {
        return $this->hasMany('App\Models\Tariff','clause_id','clause_id');
    }
}
