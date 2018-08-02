<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB;
use App\Models\InsOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class AgencyController extends Controller
{

    //代理商显示首页
    public function index()
    {
        $result = InsOrder::where('status', 'pay_end')
            ->join('users', 'users.account_id', '=', 'ins_order.create_account_id')
            ->select('name','users.account_id', DB::raw('SUM(p_num) as p_num,SUM(income) as income'))
            ->groupBy('name')->paginate(15);
        if($result){
            return view('backend.agency.index',compact('result'));
        }

    }

    //查看详情
    public  function  details(){
        if(!isset($_GET['page'])){
            $id  = $_GET['id'];
        }else{
            $id = $_GET['create_account_id'];
        }
        $res = InsOrder::where('create_account_id',$id)
             ->select('order_no','p_code','total_premium','p_num','insured_num')
            ->paginate(15);
        if($res){
            return view('backend.agency.details',compact(['res','id']));
        }
    }
}
