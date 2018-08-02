<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mongo;
use DB;
class Mongodb extends Mongo
{
    protected $connection = 'mongodb';

    public static function connectionMongodb($tables)
    {
        return $users = DB::connection('mongodb')->collection($tables);
    }
}
