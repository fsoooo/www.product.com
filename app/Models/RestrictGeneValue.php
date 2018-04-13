<?php

namespace App\Models;

use App\Models\Insurance;
use Illuminate\Database\Eloquent\Model;

class RestrictGeneValue extends Model
{
    protected $table = 'restrict_genes_values';

    protected $fillable = [
        'type', 'value', 'name', 'min', 'max', 'step', 'unit', 'rid', 'ty_value'
    ];

    public $timestamps = false;

    public function restrictGene()
    {
        return $this->belongsTo('App\Models\RestrictGeneValue', 'rid');
    }
}
