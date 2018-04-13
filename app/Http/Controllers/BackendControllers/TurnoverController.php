<?php

namespace App\Http\Controllers\BackendControllers;

use Validator, DB;
use App\Models\InsOrder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TurnoverController extends Controller
{  
	//产品交易量显示
    public function index()
    {                 
       	$turnover =InsOrder::where('status', 'pay_end')
	       	->select('p_code', DB::raw('SUM(p_num) as p_num,SUM(total_premium) as total_premium,SUM(income) as income'))
		    ->groupBy('p_code')
		    ->paginate(15);
        return view('backend.turnover.index',compact('turnover'));
    }
}
