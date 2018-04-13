<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceHealth extends Model
{
    protected $table = 'insurance_health';

    public function insurance()
    {
        return $this->hasOne('App\Models\Insurance', 'id', 'insurance_id');
    }
}
