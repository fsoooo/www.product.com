<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceAttributeModule extends Model
{
    protected $table = 'insurance_attributes_modules';

    protected $fillable = [
        'bind_id', 'name', 'remark', 'module_key'
    ];

    public $timestamps = false;

    public function attributes()
    {
        return $this->hasMany('App\Models\InsuranceAttribute', 'mid');
    }
}
