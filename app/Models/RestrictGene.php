<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestrictGene extends Model
{
    protected $fillable = [
        'protect_item_id', 'key', 'name', 'default_value', 'bind_id', 'sort', 'type', 'display', 'ty_key', 'clause_id'
    ];

    public $timestamps = false;

    public function setSortAttribute($value)
    {
        $this->attributes['sort'] = (int)$value;
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Insurance', 'restrict_genes_insurances', 'restrict_gene_id', 'insurance_id');
    }

    public function values()
    {
        return $this->hasMany('App\Models\RestrictGeneValue', 'rid');
    }
}
