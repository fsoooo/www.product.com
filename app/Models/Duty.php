<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Duty extends Model
{
    protected $table = 'duty';
    
    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function clauses(){
        return $this->belongsToMany('App\Models\Clause', 'clause_duty', 'clause_id', 'duty_id');
    }
}
