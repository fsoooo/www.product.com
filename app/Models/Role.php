<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    //
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission','permission_role','role_id','permission_id');
    }
}
