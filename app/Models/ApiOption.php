<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiOption extends Model
{
    protected $table = 'api_option';

    public $timestamps = false;

    protected $fillable = [
        'api_from_uuid', 'name', 'number', 'code', 'type'
    ];
}
