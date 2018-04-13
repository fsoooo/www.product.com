<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/9/6
 * Time: 11:27
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFinance extends Model
{
    protected $table = 'ins_order_finance';
    /**
     * 关联订单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function order()
    {
        return $this->hasOne('App\Models\InsOrder', 'id', 'order_id');
    }

    /**
     * 关联佣金比例表
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function broker()
    {
        return $this->hasOne('InsApiBrokerage', 'id', 'brokerage_id');
    }
}