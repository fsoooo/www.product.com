<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiFrom extends Model
{
    protected $table = 'api_from';

    protected $fillable = [
        'name', 'uuid', 'count_api','hebao_api', 'toubao_api', 'pay_api', 'issue_api', 'status',
    ];

    public function api_option()
    {
        return $this->hasMany(ApiOption::class, 'api_from_uuid', 'uuid');
    }

    public function insurances()
    {
        return $this->belongsToMany('App\Models\Insurance', 'insurance_api_from', 'api_from_id', 'insurance_id')->withPivot('p_code', 'private_p_code');
    }
}
