<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsOrder extends Model
{
    protected $table = 'ins_order';

    public function policy()
    {
        return $this->hasOne('App\Models\Policy', 'ins_order_id', 'id');
    }

    public function insures()
    {
        return $this->hasMany('App\Models\Insure', 'ins_order_id', 'id');
    }

    public function insurance()
    {
        return $this->belongsTo('App\Models\Insurance', 'ins_id', 'id');
    }
}
