<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $table = 'insurance';

    public function insurance_api_info()
    {
        return $this->hasOne('App\Models\InsuranceApiInfo', 'insurance_id', 'id');
    }
    public function insuranceHealth()
    {
        return $this->hasMany('App\Models\InsuranceHealth', 'insurance_id', 'id');
    }
    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function restrictGenes()
    {
        return $this->belongsToMany('App\Models\RestrictGene', 'restrict_genes_insurances', 'insurance_id', 'restrict_gene_id');
    }


    public function clauses(){
        return $this->belongsToMany('App\Models\Clause', 'insurance_clause', 'insurance_id', 'clause_id');
    }

    public function getTableColumns(){
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

    public function apiFroms()
    {
        return $this->belongsToMany('App\Models\ApiFrom', 'insurance_api_from', 'insurance_id', 'api_from_id');
    }

    public function binds()
    {
        return $this->hasMany('App\Models\InsApiBind', 'insurance_id' , 'id');
    }
}
