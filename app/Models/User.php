<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'account_id', 'sign_key','call_back_url','sell_status','code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getMyMessage()
    {
        return $this->hasMany('App\Models\MessageRule','rid','id');
    }

    public function orders()
    {
        return $this->hasMany(InsOrder::class, 'create_account_id', 'account_id');
    }
}
