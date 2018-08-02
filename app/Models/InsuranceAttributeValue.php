<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceAttributeValue extends Model
{
    protected $table = 'insurance_attributes_values';

    protected $fillable = [
        'value', 'control_value', 'conditions', 'regex', 'remind', 'attribute_type', 'unit', 'aid', 'ty_value'
    ];

    public $timestamps = false;
}
