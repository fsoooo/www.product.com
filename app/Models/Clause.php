<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clause extends Model
{
    protected $table = 'clause';

    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    //多对多责任
    public function duties(){
        return $this->belongsToMany('App\Models\Duty', 'clause_duty', 'clause_id', 'duty_id');
    }

    public function tariff(){
        return $this->hasMany('App\Models\Tariff', 'clause_id', 'id');
    }

    public function getTableColumns(){
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function insurances()
    {
        return $this->belongsToMany('App\Models\Insurance', 'insurance_clause', 'clause_id', 'insurance_id');
    }
}
