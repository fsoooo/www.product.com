<?php

namespace App\Http\Controllers\BackendControllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    protected $status = [
        'check_ing' => '投保中',
        'check_error' => '投保失败',
        'pay_ing' => '等待支付',
        'pay_error' => '支付失败',
        'pay_end' => '支付结束',
        'send_back_ing' => '退保中',
        'send_back_error' => '退保失败',
        'send_back_end' => '退保成功',
        'close' => '关闭',
    ];

    //订单列表首页，只显示账户的简要信息
    public function index()
    {
        $users = User::withCount('orders')->paginate(config('list_num.order_index'));

        return view('backend.order.index', compact('users'));
    }

    //订单列表页，显示某个账户下的订单信息
    public function list($account_id, $status = null)
    {
        $user = User::withCount('orders')->where('account_id', $account_id)->first();

        $current_status_count = null;
        if ($status) {
            $orders = $user->orders()->where('status', $status)->paginate(config('list_num.order_list'));
            $current_status_count = $user->orders()->where('status', $status)->count();
        } else {
            $orders = $user->orders()->paginate(config('list_num.order_list'));
        }

        return view('backend.order.list', [
            'user' => $user,
            'orders' => $orders,
            'current_status_count' => $current_status_count,
            'status' => $this->status,
        ]);
    }
}
