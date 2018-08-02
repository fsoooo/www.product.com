<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'company';
    
    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function clauses()
    {
        return $this->hasMany('App\Models\Clause', 'company_id', 'id');
    }
}
