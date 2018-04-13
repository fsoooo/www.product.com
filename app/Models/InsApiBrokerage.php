<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsApiBrokerage extends Model
{
    protected $table = 'ins_api_brokerage';

    public function insurance()
    {
        return $this->hasOne('App\Models\Insurance', 'id', 'insurance_id');
    }
}
