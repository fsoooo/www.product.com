<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceApiInfo extends Model
{
    public $timestamps = false;
    protected $table = 'insurance_api_from';

    public function insurance()
    {
        return $this->hasOne('App\Models\Insurance', 'id', 'insurance_id');
    }

    public function api()
    {
        return $this->hasOne('App\Models\ApiFrom', 'id', 'api_from_id');
    }
}
