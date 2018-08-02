<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preservation extends Model{

    protected $table = "preservation";
    //关联表
    public function claim_claim_rule()
    {
        return $this->hasOne('App\Models\ClaimRule','claim_id','id');
    }
    //关联单据表
    public function claim_url()
    {
        return $this->hasMany('App\Models\ClaimUrl','claim_id','id');
    }
    //关联状态
    public function claim_status()
    {
        return $this->hasOne('App\Models\Status','id','status');
    }
}
