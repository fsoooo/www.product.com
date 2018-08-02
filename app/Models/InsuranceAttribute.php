<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceAttribute extends Model
{
    protected $table = 'insurance_attributes';

    protected $fillable = [
        'name', 'api_name', 'type', 'regex', 'default_remind', 'error_remind', 'required', 'mid', 'ty_key', 'sort'
    ];

    public $timestamps = false;

    public function values()
    {
        return $this->hasMany('App\Models\InsuranceAttributeValue', 'aid');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\InsuranceAttributeModule', 'mid');
    }
}
