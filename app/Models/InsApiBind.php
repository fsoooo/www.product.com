<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsApiBind extends Model
{
    protected $table = 'insurance_api_from';

    public function insurance()
    {
        return $this->hasOne('App\Models\Insurance', 'id', 'insurance_id');
    }

    public function apiFrom()
    {
        return $this->hasOne('App\Models\ApiFrom', 'id', 'api_from_id');
    }

    public function insApiBrokerage()
    {
        return $this->hasMany('App\Models\InsApiBrokerage', 'bind_id', 'id');
    }
}
